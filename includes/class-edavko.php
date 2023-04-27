<?php
class edavko
{
	protected $loader;
	protected $edavko;
	protected $version;

	public function __construct()
	{
		if (defined('EDAVKO_VERSION')) {
			$this->version = EDAVKO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->edavko = 'edavko';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-edavko-loader.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-edavko-i18n.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-edavko-admin.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-edavko-public.php';

		$this->loader = new Edavko_Loader();
	}

	private function set_locale()
	{

		$plugin_i18n = new Edavko_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function define_admin_hooks()
	{

		$plugin_admin = new Edavko_Admin($this->get_edavko(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	private function define_public_hooks()
	{

		$plugin_public = new Edavko_Public($this->get_edavko(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('woocommerce_order_status_completed', $plugin_public, 'process_completed_order');
		$this->loader->add_action('woocommerce_email_order_meta', $plugin_public, 'add_zoi_eor_to_email', 10, 4);
	}

	public function run()
	{
		$this->loader->run();
	}

	public function get_edavko()
	{
		return $this->edavko;
	}

	public function get_loader()
	{
		return $this->loader;
	}

	public function get_version()
	{
		return $this->version;
	}

}