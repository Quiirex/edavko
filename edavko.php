<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           edavko
 *
 * @wordpress-plugin
 * Plugin Name:       eDavko
 * Description:       Plugin used for electronic validation of all invoices with FURS.
 * Version:           1.0.0
 * Author:            Luka Mlinarič
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       edavko
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
	die;
}

define('EDAVKO_VERSION', '1.0.0');

function activate_edavko()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-edavko-activator.php';
	Edavko_Activator::activate();
}

function deactivate_edavko()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-edavko-deactivator.php';
	Edavko_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_edavko');
register_deactivation_hook(__FILE__, 'deactivate_edavko');

require plugin_dir_path(__FILE__) . 'includes/class-edavko.php';

function run_edavko()
{
	$plugin = new Edavko();
	$plugin->run();
}
run_edavko();
