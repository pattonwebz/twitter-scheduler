<?php
/**
 * Decides on the time to set the tweet schedluer for if no time is set.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Scheduler;

/**
 * Tweets all need a time stored with them so when none is set this class
 * decides what should be stored.
 */
class TimeSetter {

	public function __construct( $tweet ) {
		if ( ! is_a( $tweet, '\PattonWebz\TwitterSchedler\Tweet' ) ) {
			return false;
		}
		$old_time = $tweet->$scheduled_time;

		// get the list of pre-defined timeslots.


	}

}
