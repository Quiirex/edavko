<?php
class Edavko_Admin
{
	private $edavko;
	private $version;
	private $invoice_verification_result_message = '';
	private $business_space_verification_result_message = '';
	private $business_space_registration_result_message = '';
	private $settings_set_result_message = '';

	public function __construct($edavko, $version)
	{
		$this->edavko = $edavko;
		$this->version = $version;

		add_action('admin_menu', [$this, 'add_plugin_admin_menu'], 9);
		add_action('admin_init', [$this, 'register_and_build_settings_fields']);
		add_action('admin_init', [$this, 'register_and_build_verify_invoice_settings_fields']);
		add_action('admin_init', [$this, 'register_and_build_verify_business_space_settings_fields']);
		add_action('admin_init', [$this, 'register_and_build_register_business_space_settings_fields']);
		add_action('admin_init', [$this, 'handle_invoice_verification_submission']);
		add_action('admin_init', [$this, 'handle_business_space_verification_submission']);
		add_action('admin_init', [$this, 'handle_business_space_registration_submission']);
		add_action('admin_init', [$this, 'handle_settings_set_submission']);
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->edavko, plugin_dir_url(__FILE__) . 'css/edavko-admin.css', [], $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->edavko, plugin_dir_url(__FILE__) . 'js/edavko-admin.js', ['jquery'], $this->version, false);
	}

	public function add_plugin_admin_menu()
	{
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page($this->edavko, 'eDavko', 'administrator', $this->edavko, [$this, 'display_plugin_admin_dashboard'], 'dashicons-money', 26);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'eDavko nastavitve', 'Nastavitve', 'administrator', $this->edavko . '-settings', [$this, 'display_plugin_admin_settings']);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'Preverjanje poslovnega prostora', 'Preverjanje poslovnega prostora', 'administrator', $this->edavko . '-verify-business-space', [$this, 'display_plugin_admin_verify_business_space']);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'Preverjanje izdanega računa', 'Preverjanje izdanega računa', 'administrator', $this->edavko . '-verify-invoice', [$this, 'display_plugin_admin_verify_invoice']);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'Registracija poslovnega prostora', 'Registracija poslovnega prostora', 'administrator', $this->edavko . '-register-business-space', [$this, 'display_plugin_admin_register_business_space']);
	}

	public function display_plugin_admin_dashboard()
	{
		require_once 'partials/' . $this->edavko . '-admin-display.php';
	}

	public function display_plugin_admin_settings()
	{
		// set this var to be used in the settings-display view
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
		if (isset($_GET['error_message'])) {
			add_action('admin_notices', [$this, 'edavko_settings_messages']);
			do_action('admin_notices', $_GET['error_message']);
		}
		require_once 'partials/' . $this->edavko . '-admin-settings-display.php';
	}

	public function display_plugin_admin_verify_business_space()
	{
		require_once 'partials/' . $this->edavko . '-admin-verify-business-space.php';
	}

	public function display_plugin_admin_verify_invoice()
	{
		require_once 'partials/' . $this->edavko . '-admin-verify-invoice.php';
	}

	public function display_plugin_admin_register_business_space()
	{
		require_once 'partials/' . $this->edavko . '-admin-register-business-space.php';
	}

	public function edavko_settings_messages($error_message)
	{
		switch ($error_message) {
			case '1':
				$message = __('There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain');
				$err_code = esc_attr('edavko_example_setting');
				$setting_field = 'edavko_example_setting';
				break;
		}
		$type = 'error';
		add_settings_error(
			$setting_field,
			$err_code,
			$message,
			$type
		);
	}

	public function register_and_build_settings_fields()
	{
		add_settings_section(
			// ID used to identify this section and with which to register options
			'edavko_general_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			[$this, 'edavko_display_general_account'],
			// Page on which to add this section of options
			'edavko_general_settings'
		);

		unset($args1);
		unset($args2);
		unset($args3);

		$args1 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_furs_api_token',
			'name' => 'edavko_furs_api_token',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_furs_api_token',
			'FURS API Žeton',
			[$this, 'edavko_render_settings_field'],
			'edavko_general_settings',
			'edavko_general_section',
			$args1
		);

		$args2 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_furs_business_space_id',
			'name' => 'edavko_furs_business_space_id',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_furs_business_space_id',
			'ID Poslovnega prostora',
			[$this, 'edavko_render_settings_field'],
			'edavko_general_settings',
			'edavko_general_section',
			$args2
		);

		$args3 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_furs_electronic_device_id',
			'name' => 'edavko_furs_electronic_device_id',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_furs_electronic_device_id',
			'ID Elektronske naprave',
			[$this, 'edavko_render_settings_field'],
			'edavko_general_settings',
			'edavko_general_section',
			$args3
		);

		register_setting(
			'edavko_general_settings',
			'edavko_furs_api_token'
		);

		register_setting(
			'edavko_general_settings',
			'edavko_furs_business_space_id'
		);

		register_setting(
			'edavko_general_settings',
			'edavko_furs_electronic_device_id'
		);
	}

	public function register_and_build_verify_invoice_settings_fields()
	{
		add_settings_section(
			// ID used to identify this section and with which to register options
			'edavko_verify_invoice_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			[$this, 'edavko_display_verify_invoice'],
			// Page on which to add this section of options
			'edavko_verify_invoice_settings'
		);

		unset($args1);
		unset($args2);

		$args1 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_verify_invoice_zoi',
			'name' => 'edavko_verify_invoice_zoi',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_verify_invoice_zoi',
			'ZOI',
			[$this, 'edavko_render_settings_field'],
			'edavko_verify_invoice_settings',
			'edavko_verify_invoice_section',
			$args1
		);

		$args2 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_verify_invoice_eor',
			'name' => 'edavko_verify_invoice_eor',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_verify_invoice_eor',
			'EOR',
			[$this, 'edavko_render_settings_field'],
			'edavko_verify_invoice_settings',
			'edavko_verify_invoice_section',
			$args2
		);
	}

	public function register_and_build_verify_business_space_settings_fields()
	{
		add_settings_section(
			// ID used to identify this section and with which to register options
			'edavko_verify_business_space_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			[$this, 'edavko_display_verify_business_space'],
			// Page on which to add this section of options
			'edavko_verify_business_space_settings'
		);

		unset($args);

		$args = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_verify_business_space',
			'name' => 'edavko_verify_business_space',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_verify_business_space',
			'ID poslovnega prostora',
			[$this, 'edavko_render_settings_field'],
			'edavko_verify_business_space_settings',
			'edavko_verify_business_space_section',
			$args
		);
	}

	public function register_and_build_register_business_space_settings_fields()
	{
		add_settings_section(
			// ID used to identify this section and with which to register options
			'edavko_register_business_space_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			[$this, 'edavko_display_register_business_space'],
			// Page on which to add this section of options
			'edavko_register_business_space_settings'
		);

		unset($args1);
		unset($args2);
		unset($args3);
		unset($args4);
		unset($args5);
		unset($args6);
		unset($args7);
		unset($args8);
		unset($args9);

		$args1 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_register_business_space_id',
			'name' => 'edavko_register_business_space_id',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_id',
			'ID poslovnega prostora',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args1
		);

		$args2 = [
			'type' => 'input',
			'subtype' => 'number',
			'id' => 'edavko_register_business_space_katastrska_stevilka',
			'name' => 'edavko_register_business_space_katastrska_stevilka',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'numeric',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_katastrska_stevilka',
			'Katastrska številka',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args2
		);

		$args3 = [
			'type' => 'input',
			'subtype' => 'number',
			'id' => 'edavko_register_business_space_stevilka_stavbe',
			'name' => 'edavko_register_business_space_stevilka_stavbe',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'numeric',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_stevilka_stavbe',
			'Številka stavbe',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args3
		);

		$args4 = [
			'type' => 'input',
			'subtype' => 'number',
			'id' => 'edavko_register_business_space_stevilka_dela_stavbe',
			'name' => 'edavko_register_business_space_stevilka_dela_stavbe',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'numeric',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_stevilka_dela_stavbe',
			'Številka dela stavbe',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args4
		);

		$args5 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_register_business_space_ulica',
			'name' => 'edavko_register_business_space_ulica',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_ulica',
			'Ulica',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args5
		);

		$args6 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_register_business_space_hisna_stevilka',
			'name' => 'edavko_register_business_space_hisna_stevilka',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_hisna_stevilka',
			'Hišna številka',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args6
		);

		$args7 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_register_business_space_hisna_stevilka_add',
			'name' => 'edavko_register_business_space_hisna_stevilka_add',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_hisna_stevilka_add',
			'Hišna številka dodatek',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args7
		);

		$args8 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_register_business_space_obcina',
			'name' => 'edavko_register_business_space_obcina',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option',
		];

		add_settings_field(
			'edavko_register_business_space_obcina',
			'Občina',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args8
		);

		$args9 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_register_business_space_postna_stevilka',
			'name' => 'edavko_register_business_space_postna_stevilka',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_postna_stevilka',
			'Poštna številka',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args9
		);

		$args10 = [
			'type' => 'input',
			'subtype' => 'text',
			'id' => 'edavko_register_business_space_mesto',
			'name' => 'edavko_register_business_space_mesto',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		add_settings_field(
			'edavko_register_business_space_mesto',
			'Mesto',
			[$this, 'edavko_render_settings_field'],
			'edavko_register_business_space_settings',
			'edavko_register_business_space_section',
			$args10
		);
	}

	public function edavko_display_general_account()
	{
		echo '<p></p>';
	}

	public function edavko_display_verify_invoice()
	{
		echo '<p></p>';
	}

	public function edavko_display_verify_business_space()
	{
		echo '<p></p>';
	}

	public function edavko_display_register_business_space()
	{
		echo '<p></p>';
	}

	public function edavko_render_settings_field($args)
	{
		if ($args['wp_data'] == 'option') {
			$wp_data_value = get_option($args['name']);
		} elseif ($args['wp_data'] == 'post_meta') {
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
		}

		switch ($args['type']) {
			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if ($args['subtype'] != 'checkbox') {
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
					$min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
					$max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
					if (isset($args['disabled'])) {
						// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
					} else {
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
				}
				break;
			default:
				# code...
				break;
		}
	}

	public function display_settings_set_result_message()
	{
		echo $this->settings_set_result_message;
	}

	public function handle_settings_set_submission()
	{
		$url = 'http://studentdocker.informatika.uni-mb.si:49163';

		$response = wp_remote_get($url, [
			'headers' => [
				'Authorization' => 'Bearer ' . get_option('edavko_furs_api_token')
			]
		]);

		if (is_wp_error($response)) {
			$this->settings_set_result_message = '<p>Povezava na FURS davčno blagajno neuspešna: ' . $response->get_error_message() . '</p>';
			return;
		}

		$response_body = json_decode(wp_remote_retrieve_body($response), true);

		if (isset($response_body['EchoRequest'])) {
			$result_message = '<p>Povezava na FURS davčno blagajno uspešna.</p>';
			$this->settings_set_result_message = $result_message;
		} else {
			$this->settings_set_result_message = '<p>Povezava na FURS davčno blagajno neuspešna.</p>';
		}
	}

	public function display_invoice_verification_result_message()
	{
		echo $this->invoice_verification_result_message;
	}

	public function handle_invoice_verification_submission()
	{
		if (!isset($_POST['edavko_verify_invoice_nonce']) || !wp_verify_nonce($_POST['edavko_verify_invoice_nonce'], 'edavko_verify_invoice_nonce_action')) {
			return;
		}

		$zoi = sanitize_text_field($_POST['edavko_verify_invoice_zoi'] ?? '');
		$eor = sanitize_text_field($_POST['edavko_verify_invoice_eor'] ?? '');

		if (!$zoi && !$eor) {
			$this->invoice_verification_result_message = '<p>Vnesite ZOI ali EOR!</p>';
			return;
		}

		$params = $zoi ? ['zoi' => $zoi] : ['eor' => $eor];
		$url = add_query_arg($params, 'http://studentdocker.informatika.uni-mb.si:49163/check-invoice');

		$response = wp_remote_get($url, [
			'headers' => [
				'Authorization' => 'Bearer ' . get_option('edavko_furs_api_token')
			],
		]);

		if (is_wp_error($response)) {
			$this->invoice_verification_result_message = sprintf('<p>Napaka pri obdelavi zahteve: %s</p>', $response->get_error_message());
			return;
		}

		$response_body = json_decode(wp_remote_retrieve_body($response), true);

		if (isset($response_body['TAX'], $response_body['AMOUNT'], $response_body['DATETIME'], $response_body['CONTENT'])) {
			$this->invoice_verification_result_message = sprintf('<p>Račun je veljaven.</p><ul><li><b>Davčna številka</b>: "%s"</li><li><b>Znesek</b>: "%s"</li><li><b>Datum in čas</b>: "%s"</li></ul>', $response_body['TAX'], $response_body['AMOUNT'], $response_body['DATETIME']);
		} else {
			$this->invoice_verification_result_message = '<p>Račun ni veljaven.</p>';
		}
	}

	public function display_business_space_verification_result_message()
	{
		echo $this->business_space_verification_result_message;
	}

	public function handle_business_space_verification_submission()
	{
		if (!isset($_POST['edavko_verify_business_space_nonce']) || !wp_verify_nonce($_POST['edavko_verify_business_space_nonce'], 'edavko_verify_business_space_nonce_action')) {
			return;
		}

		$id_business_space = isset($_POST['edavko_verify_business_space']) ? sanitize_text_field($_POST['edavko_verify_business_space']) : '';

		if (!$id_business_space) {
			$this->business_space_verification_result_message = '<p>Vnesite ID poslovnega prostora!</p>';
			return;
		}

		$params = ['id' => $id_business_space];
		$url = add_query_arg($params, 'http://studentdocker.informatika.uni-mb.si:49163/check-premise');

		$response = wp_remote_get($url, [
			'headers' => [
				'Authorization' => 'Bearer ' . get_option('edavko_furs_api_token')
			]
		]);

		if (is_wp_error($response)) {
			$this->business_space_verification_result_message = '<p>Napaka pri obdelavi zahteve: ' . $response->get_error_message() . '</p>';
			return;
		}

		$response_body = json_decode(wp_remote_retrieve_body($response), true);

		if (isset($response_body['Data']) && is_array($response_body['Data']) && count($response_body['Data']) > 0) {
			$result_message = '<p>Poslovni prostor je veljaven.</p><ul>';

			foreach ($response_body['Data'] as $data) {
				$result_message .= '<li><b>' . $data['Field_name'] . '</b>: "' . $data['Field_value'] . '"</li>';
			}

			$result_message .= '</ul>';

			$this->business_space_verification_result_message = $result_message;
		} else {
			$this->business_space_verification_result_message = '<p>Poslovni prostor ni veljaven/ne obstaja.</p>';
		}
	}

	public function display_business_space_registration_result_message()
	{
		echo $this->business_space_registration_result_message;
	}

	public function handle_business_space_registration_submission()
	{
		if (!isset($_POST['edavko_register_business_space_nonce']) || !wp_verify_nonce($_POST['edavko_register_business_space_nonce'], 'edavko_register_business_space_nonce_action')) {
			return;
		}

		$id_business_space = isset($_POST['edavko_register_business_space_id']) ? sanitize_text_field($_POST['edavko_register_business_space_id']) : '';
		$katastrska_stevilka = isset($_POST['edavko_register_business_space_katastrska_stevilka']) ? sanitize_text_field($_POST['edavko_register_business_space_katastrska_stevilka']) : 0;
		$stevilka_stavbe = isset($_POST['edavko_register_business_space_stevilka_stavbe']) ? sanitize_text_field($_POST['edavko_register_business_space_stevilka_stavbe']) : 0;
		$stevilka_dela_stavbe = isset($_POST['edavko_register_business_space_stevilka_dela_stavbe']) ? sanitize_text_field($_POST['edavko_register_business_space_stevilka_dela_stavbe']) : 0;
		$ulica = isset($_POST['edavko_register_business_space_ulica']) ? sanitize_text_field($_POST['edavko_register_business_space_ulica']) : '';
		$hisna_stevilka = isset($_POST['edavko_register_business_space_hisna_stevilka']) ? sanitize_text_field($_POST['edavko_register_business_space_hisna_stevilka']) : '';
		$hisna_stevilka_add = isset($_POST['edavko_register_business_space_hisna_stevilka_add']) ? sanitize_text_field($_POST['edavko_register_business_space_hisna_stevilka_add']) : '';
		$obcina = isset($_POST['edavko_register_business_space_obcina']) ? sanitize_text_field($_POST['edavko_register_business_space_obcina']) : '';
		$postna_stevilka = isset($_POST['edavko_register_business_space_postna_stevilka']) ? sanitize_text_field($_POST['edavko_register_business_space_postna_stevilka']) : '';
		$mesto = isset($_POST['edavko_register_business_space_mesto']) ? sanitize_text_field($_POST['edavko_register_business_space_mesto']) : '';

		if (!$id_business_space || !$katastrska_stevilka || !$stevilka_stavbe || !$stevilka_dela_stavbe || !$ulica || !$hisna_stevilka || !$obcina || !$postna_stevilka || !$mesto) {
			$this->business_space_registration_result_message = '<p>Za registracijo poslovnega prostora morate izpolniti vsa polja!</p>';
			return;
		}

		$request_body = [
			'BusinessPremiseRequest' => [
				'Header' => [
					'MessageID' => wp_generate_uuid4(),
					'DateTime' => date('Y-m-d\TH:i:s\Z')
				],
				'BusinessPremise' => [
					// fiksna vrednost
					'TaxNumber' => 10596631,
					'BusinessPremiseID' => $id_business_space,
					'BPIdentifier' => [
						'RealEstateBP' => [
							'PropertyID' => [
								'CadastralNumber' => (int) $katastrska_stevilka,
								'BuildingNumber' => (int) $stevilka_stavbe,
								'BuildingSectionNumber' => (int) $stevilka_dela_stavbe
							],
							'Address' => [
								'Street' => $ulica,
								'HouseNumber' => $hisna_stevilka,
								'HouseNumberAdditional' => $hisna_stevilka_add,
								'Community' => $obcina,
								'City' => $mesto,
								'PostalCode' => $postna_stevilka
							]
						]
					],
					'ValidityDate' => date('Y-m-d\TH:i:s\Z'),
					'SoftwareSupplier' => [
						[
							'TaxNumber' => 10596631 // fiksna vrednost
						]
					]
				]
			]
		];

		$request_body_json = json_encode($request_body);

		// log request body in console
		// echo '<script>console.log(' . json_encode($request_body_json) . ')</script>';

		$url = 'http://studentdocker.informatika.uni-mb.si:49163/register';

		$response = wp_remote_post(
			$url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . get_option('edavko_furs_api_token'),
					'Content-Type' => 'application/json'
				],
				'body' => $request_body_json
			]
		);

		if (is_wp_error($response)) {
			$this->business_space_registration_result_message = '<p>Registracija poslovnega prostora ni uspela. Poskusite znova.</p>';
			return;
		}

		$response_data = json_decode(wp_remote_retrieve_body($response), true);

		if (isset($response_data['error'])) {
			$this->business_space_registration_result_message = '<p>' . esc_html($response_data['error']) . '</p>';
			return;
		}

		$this->business_space_registration_result_message = '<p>Poslovni prostor uspešno registriran.</p>';
	}
}