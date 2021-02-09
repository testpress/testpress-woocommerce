<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://testpress.in
 * @since             1.0.0
 * @package           Testpress_Lms
 *
 * @wordpress-plugin
 * Plugin Name:       Testpress LMS
 * Plugin URI:        https://testpress.in/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Testpress
 * Author URI:        https://testpress.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       testpress-lms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TESTPRESS_LMS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-testpress-lms-activator.php
 */
function activate_testpress_lms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-testpress-lms-activator.php';
	Testpress_Lms_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-testpress-lms-deactivator.php
 */
function deactivate_testpress_lms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-testpress-lms-deactivator.php';
	Testpress_Lms_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_testpress_lms' );
register_deactivation_hook( __FILE__, 'deactivate_testpress_lms' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-testpress-lms.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_testpress_lms() {

	$plugin = new Testpress_Lms();
	$plugin->run();

}
run_testpress_lms();
