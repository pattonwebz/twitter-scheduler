<?php
/**
 * Plugin Name:     Twitter Scheduler
 * Plugin URI:      https://www.pattonwebz.com/twitter-scheduler/
 * Description:     A plugin to help schedule posts for Twitter.
 * Author:          William Patton
 * Author URI:      https://www.pattonwebz.com/
 * Licence:         GPLv2 or later
 * Text Domain:     twitter-scheduler
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Twitter Scheduler
 * @since           0.1.0
 * @author          William Patton <will@pattonwebz.com>
 * @copyright       Copyright (c) 2018, William Patton
 * @link            https://github.com/pattonwebz/customizer-framework/
 * @license         http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! defined( 'TWSC_PLUGIN_DIR' ) ) {
	define( 'TWSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'TWSC_PLUGIN_URL' ) ) {
	define( 'TWSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'TWSC_PLUGIN_VERSION' ) ) {
	define( 'TWSC_PLUGIN_VERSION', '0.1.0' );
}
if ( ! defined( 'TWSC_ADMIN_PAGE_ID' ) ) {
	define( 'TWSC_ADMIN_PAGE_ID', 'twitter_scheduler' );
}
if ( ! defined( 'TWSC_POST_TYPE' ) ) {
	define( 'TWSC_POST_TYPE', 'tweets' );
}

require_once TWSC_PLUGIN_DIR . 'inc/functions/base.php'; // plugins base setup functions.
require_once TWSC_PLUGIN_DIR . 'inc/functions/helpers.php'; // some helper functions and setter/getters.
require_once TWSC_PLUGIN_DIR . 'inc/cron-functions.php'; // handles adding cron schedules and actions.

$autoload = TWSC_PLUGIN_DIR . 'vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

/**
 * The main plugin activation/deactivation actions. Used to register the runner
 * action into the scheduled cron slot. Sheduled every 5 mins.
 */
register_activation_hook( __FILE__, '\Pattonwebz\TwitterScheduler\Cron\activation' );
register_deactivation_hook( __FILE__, '\Pattonwebz\TwitterScheduler\Cron\deactivation' );
