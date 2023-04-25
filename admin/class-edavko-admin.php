<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    edavko
 * @subpackage edavko/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    edavko
 * @subpackage edavko/admin
 * @author     Your Name <email@example.com>
 */
class Edavko_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $edavko    The ID of this plugin.
	 */
	private $edavko;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $edavko       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($edavko, $version)
	{

		$this->edavko = $edavko;
		$this->version = $version;
		add_action('admin_menu', array($this, 'add_plugin_admin_menu'), 9);
		add_action('admin_init', array($this, 'register_and_build_fields'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in edavko_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The edavko_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->edavko, plugin_dir_url(__FILE__) . 'css/edavko-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in edavko_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The edavko_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->edavko, plugin_dir_url(__FILE__) . 'js/edavko-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu()
	{
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page($this->edavko, 'eDavko', 'administrator', $this->edavko, array($this, 'display_plugin_admin_dashboard'), 'dashicons-money', 26);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'eDavko nastavitve', 'Nastavitve', 'administrator', $this->edavko . '-settings', array($this, 'display_plugin_admin_settings'));

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'Preverjanje poslovnega prostora', 'Preverjanje poslovnega prostora', 'administrator', $this->edavko . '-verify-business-space', array($this, 'display_plugin_admin_verify_business_space'));

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'Preverjanje izdanega računa', 'Preverjanje izdanega računa', 'administrator', $this->edavko . '-verify-invoice', array($this, 'display_plugin_admin_verify_invoice'));

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->edavko, 'Registracija poslovnega prostora', 'Registracija poslovnega prostora', 'administrator', $this->edavko . '-register-business-space', array($this, 'display_plugin_admin_register_business_space'));
	}

	/**
	 * Render the admin dashboard page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_dashboard()
	{
		require_once 'partials/' . $this->edavko . '-admin-display.php';
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_settings()
	{
		// set this var to be used in the settings-display view
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
		if (isset($_GET['error_message'])) {
			add_action('admin_notices', array($this, 'edavko_settings_messages'));
			do_action('admin_notices', $_GET['error_message']);
		}
		require_once 'partials/' . $this->edavko . '-admin-settings-display.php';
	}

	/**
	 * Render the check business space page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_verify_business_space()
	{
		require_once 'partials/' . $this->edavko . '-admin-verify-business-space.php';
	}

	/**
	 * Render the check invoice page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_verify_invoice()
	{
		require_once 'partials/' . $this->edavko . '-admin-verify-invoice.php';
	}

	/**
	 * Render the register business space page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_register_business_space()
	{
		require_once 'partials/' . $this->edavko . '-admin-register-business-space.php';
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
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

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function register_and_build_fields()
	{
		/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */
		add_settings_section(
			// ID used to identify this section and with which to register options
			'edavko_general_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			array($this, 'edavko_display_general_account'),
			// Page on which to add this section of options
			'edavko_general_settings'
		);

		unset($args);

		$args = array(
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'edavko_furs_api_token',
			'name'      => 'edavko_furs_api_token',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		);

		add_settings_field(
			'edavko_furs_api_token',
			'FURS API Token',
			array($this, 'edavko_render_settings_field'),
			'edavko_general_settings',
			'edavko_general_section',
			$args
		);

		add_settings_field(
			'edavko_furs_business_space_id',
			'ID Poslovnega prostora',
			array($this, 'edavko_render_settings_field'),
			'edavko_general_settings',
			'edavko_general_section',
			$args
		);

		add_settings_field(
			'edavko_furs_electronic_device_id',
			'ID Elektronske naprave',
			array($this, 'edavko_render_settings_field'),
			'edavko_general_settings',
			'edavko_general_section',
			$args
		);

		register_setting(
			'edavko_general_settings',
			'edavko_furs_api_token',
			'edavko_furs_business_space_id',
			'edavko_furs_electronic_device_id'
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function edavko_display_general_account()
	{
		echo '<p></p>';
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function edavko_render_settings_field($args)
	{
		/* EXAMPLE INPUT
			'type'      => 'input',
			'subtype'   => '',
			'id'    => $this->plugin_name.'_example_setting',
			'name'      => $this->plugin_name.'_example_setting',
			'required' => 'required="required"',
			'get_option_list' => "",
				'value_type' = serialized OR normal,
			'wp_data'=>(option or post_meta),
			'post_id' =>
			*/
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
}
