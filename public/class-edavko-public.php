<?php
class edavko_Public
{
	private $edavko;
	private $version;

	public function __construct($edavko, $version)
	{
		$this->edavko = $edavko;
		$this->version = $version;
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->edavko, plugin_dir_url(__FILE__) . 'css/edavko-public.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->edavko, plugin_dir_url(__FILE__) . 'js/edavko-public.js', array('jquery'), $this->version, false);
	}

	public function process_completed_order($order_id)
	{
		// Define the file path and name
		$log_file_path = __DIR__ . '/request.log';

		$order = wc_get_order($order_id);

		$request_body = [
			"InvoiceRequest" => [
				"Header" => [
					"MessageID" => wp_generate_uuid4(),
					"DateTime" => date('Y-m-d\TH:i:s\Z')
				],
				"Invoice" => [
					"TaxNumber" => 10596631,
					"IssueDateTime" => date('Y-m-d\TH:i:s\Z'),
					"NumberingStructure" => "B",
					"InvoiceIdentifier" => [
						"BusinessPremiseID" => get_option('edavko_furs_business_space_id'),
						"ElectronicDeviceID" => get_option('edavko_furs_electronic_device_id'),
						"InvoiceNumber" => $order->get_order_number()
					],
					"InvoiceAmount" => round(floatval($order->get_total()), 2),
					"PaymentAmount" => round(floatval($order->get_total()), 2),
					"TaxesPerSeller" => [
						[
							"VAT" => [
								[
									"TaxRate" => round(floatval(22.00), 2),
									"TaxableAmount" => round(floatval($order->get_total() / 1.22), 2),
									"TaxAmount" => round(floatval($order->get_total() * 0.22 / 1.22), 2)
								]
							]
						]
					],
					"OperatorTaxNumber" => 10596631,
					"ProtectedID" => 'protectedid'
				]
			]
		];

		$api_url = 'http://studentdocker.informatika.uni-mb.si:49163/invoice';
		$bearer_token = '1002376637';

		// Log the response body to the file
		file_put_contents($log_file_path, json_encode($request_body) . PHP_EOL, FILE_APPEND);

		$response = wp_remote_post(
			$api_url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $bearer_token,
					'Content-Type' => 'application/json'
				],
				'body' => json_encode($request_body)
			]
		);

		if (is_wp_error($response)) {
			return;
		} else {
			$response_body = json_decode(wp_remote_retrieve_body($response), true);

			$zoi = $response_body['ZOI'];
			$eor = $response_body['response']['InvoiceResponse']['UniqueInvoiceID'];

			$order->update_meta_data('furs_zoi', $zoi);
			$order->update_meta_data('furs_eor', $eor);
			$order->save_meta_data();
		}
	}

	public function add_zoi_eor_to_email($order, $sent_to_admin, $plain_text, $email)
	{
		if ($email->id == 'customer_completed_order') {
			$zoi = $order->get_meta('furs_zoi');
			$eor = $order->get_meta('furs_eor');

			echo '<p><strong>ZOI:</strong> ' . $zoi . '</p>';
			echo '<p><strong>EOR:</strong> ' . $eor . '</p>';
		}
	}
}