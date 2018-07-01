<?php
/**
 * Handles all of the cron setups and halts for this plugin.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Cron;

/**
 * Addd a 5 minute cron slot to schedule things on.
 *
 * @since  0.1.0
 * @param  array $schedules array of current cron schedules.
 * @return array            updated array of cron schedules.
 */
function cron_schedules( $schedules ) {
	if ( ! isset( $schedules['5min'] ) ) {
		$schedules['5min'] = array(
			'interval' => 5 * 60,
			'display'  => esc_html__( 'Once every 5 minutes', 'twitter-scheduler' ),
		);
	}
	return $schedules;
}
add_filter( 'cron_schedules', __NAMESPACE__ . '\\cron_schedules' ); // PHPCS:CronSchedulesInterval: ok!

/**
 * On activation of our plugin we will set a cron task to run every 5 minutes.
 *
 * First runtime will be a random time between 30s from now and 5 min from now.
 *
 * @since  0.1.0
 */
function activation() {
	if ( ! wp_next_scheduled( 'sosc_schedule_hooks' ) ) {
		// schedule the first cron to run at random time between 30 and 300
		// seconds in the future, every 5 minutes after that.
		wp_schedule_event( time() + rand( 30, 300 ), '5min', 'sosc_schedule_hooks' );
	}
}

/**
 * On deactivation of our plugin we will remove our plugins cron task.
 *
 * This could also be used to cleanup our post_types and settings data - but it
 * currently does not cleanup work yet.
 *
 * @since  0.1.0
 */
function deactivation() {
	wp_clear_scheduled_hook( 'sosc_schedule_hooks' );
}
