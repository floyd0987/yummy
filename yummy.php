<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www
 * @since             1.0.0
 * @package           Yummy
 *
 * @wordpress-plugin
 * Plugin Name:       Yummy
 * Plugin URI:        www
 * Description:       Restaurant Booking.
 * Version:           1.0.2
 * Author:            Eronne Bernucci
 * Author URI:        www
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yummy
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently pligin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.2' );

define( 'YUMMY_BOOKING_POST_META', array('yummy_order_start_date','yummy_order_end_date','yummy_guests_number','yummy_user_lastname','yummy_user_name','yummy_user_email','yummy_user_telephone' ) );



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-yummy-activator.php
 */
function activate_yummy() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yummy-activator.php';
	Yummy_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-yummy-deactivator.php
 */
function deactivate_yummy() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yummy-deactivator.php';
	Yummy_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_yummy' );
register_deactivation_hook( __FILE__, 'deactivate_yummy' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yummy.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yummy() {

	$plugin = new Yummy();
	$plugin->run();

}
run_yummy();
