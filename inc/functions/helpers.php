<?php
/**
 * The sosc-helpers.php file.
 *
 * This file holds some helper functions used that might be used throughout
 * the plugin for various purposes.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Helpers;

use PattonWebz\TwitterScheduler\TwitterInteractions;

/**
 * Function to retrieve all of the the auth settings needed from the database for twitter.
 *
 * @return array the auth settings needed for twitter.
 */
function get_twitter_settings() {
	$options  = get_option( 'twitter_scheduler_settings' );
	$settings = array(
		'oauth_access_token'        => $options['oauth_access_token'],
		'oauth_access_token_secret' => $options['oauth_access_token_secret'],
		'consumer_key'              => $options['consumer_key'],
		'consumer_secret'           => $options['consumer_secret'],
	);
	return apply_filters( 'sosc_filter_twitter_settings', $settings );
}

/**
 * Check that the tweet seems valid.
 *
 * This includes checking total character count and amking sure links fit.
 *
 * @param  string $content string that will become the tweet content.
 * @return boolean          returns true if tweet seems valid, false otherwise.
 */
function is_tweet_valid( $content ) {
	// The Regular Expression filter.
	$expression = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
	// assume not valid till we've checked.
	$tweet_valid = false;

	$url_chars_total = 0;
	$url_chars_short = 0;
	// Check if there is urls in the text.
	if ( preg_match_all( $expression, $content, $urls ) ) {

		// get the current length, defaults to 25 if not yet stored.
		$short_url_length = get_option( 'twsc_config_short_url_length', false );
		if ( $short_url_length ) {
			$short_url_length = $short_url_length['length'];
		} else {
			$short_url_length = 25;
		}
		foreach ( $urls[0] as $url ) {
			$url_chars_total = $url_chars_total + strlen( $url );
			$url_chars_short = $url_chars_short + $short_url_length;
		}
	}
	$char_counter = strlen( $content );
	if ( $url_chars_total > 0 && $url_chars_short > 0 ) {
		$char_counter = $char_counter - ( $url_chars_total - $url_chars_short );
	}
	if ( $char_counter < 279 ) {
		return true;
	}
}

/**
 * Checks the current limit on t.co links length.
 *
 * Should be run daily and cached.
 */
function check_tco_short_url_limit() {

	// get any currently saved options tco short url length.
	$current = get_option( 'twsc_config_short_url_length', false );

	// does the option already exist?
	if ( $current ) {
		// get a datetime object for when last update for length happened.
		$last_checked = new \datetime( '@' . $current['timestamp'] );

		// calculate the time difference betwen last update and now. Return it as hours.
		$time_difference = $last_checked->diff( new \datetime() );
		$time_difference = $time_difference->format( '%R%h' );
		// make sure we're a positive number.
		$count = 0;
		str_replace( '+', '', $time_difference, $count );
		if ( $count ) {
			// has it been 24 hours?
			if ( (int) $time_difference < 24 ) {
				// less than 24 hours, no need to update, return early.
				return;
			}
		}
	}

	// since we didn't return early let's update the value.
	TwitterInteractions::update_tco_short_url_limit();
}

/**
 * Gets a set of previously saved media ids and return those which are still
 * within validity timeframe.
 *
 * NOTE: This only works with single id, but API can accept potentially 4 for images.
 *
 * @param  integer $post_id the post ID which we are workign with.
 * @return integer          the media id which we are attaching.
 */
function get_valid_media_ids( $post_id ) {
	$media_id         = '';
	$media_id_expires = get_post_meta( $post_id, 'media_id_expires', true );
	if ( $media_id_expires && get_the_time( 'U' ) < $media_id_expires ) {
		$media_id = get_post_meta( $post_id, 'media_id_string', true );
	} else {
		$twitter  = new TwitterInteractions();
		$media_id = $twitter->attach_media( $post_id );
	}
	return $media_id;

}

/**
 * Retuns an array of args used to get the next scheduled post for social share.
 * Filters the array before return.
 *
 * @param  array $args array of args to override defaults to be returned.
 * @return array       array of maybe updated args.
 */
function next_scheduled_posting_query_args( $args = array() ) {
	$defaults = array(
		'post_type'      => TWSC_POST_TYPE,
		'meta_key'       => '_twsc_timetosend', // main sort key is based on send time.
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'relation' => 'AND',
				array(
					'key'     => '_twsc_timetosend',
					'value'   => (int) current_time( 'timestamp', true ) + apply_filters( 'twsc_allow_early_seconds', 30 ), // current time + 30 sec to account for any deviance.
					'compare' => '<=', // send time must be less than current time + 30.
				),
				array(
					'key'     => '_twsc_timetosend', // Only allow up to 15 minutes of missed scheduled posts.
					'value'   => (int) current_time( 'timestamp', true ) - apply_filters( 'twsc_allow_late_seconds', 900 ), // current time - 15 minutes seconds to account for any deviance.
					'compare' => '>=', // send time must be more than current time - 15 minutes.
				),
			),
			array(
				'relation' => 'OR', // check if already sent OR that key doesn't exist yet.
				array(
					'key'     => '_twsc_already_sent',
					'value'   => '1', // 1 indicates already sent.
					'compare' => '!=',
				),
				array(
					'key'     => '_twsc_already_sent',
					'compare' => 'NOT EXISTS', // not exists indicates that it's not yet sent.
				),
			),
		),
		'orderby'        => 'meta_value_num', // order by number.
		'order'          => 'ASC', // assending (smallest number is closes time till now).
		'posts_per_page' => 1,
	);
	if ( is_array( $args ) ) {
		$args = wp_parse_args( $args, $defaults );
	}
	return apply_filters( 'sosc_next_posting_query_args', $args );
}

/**
 * Retuns an array of args used to get the next scheduled post for social share.
 * Filters the array before return.
 *
 * @param  array $args array of args to override defaults to be returned.
 * @return array       array of maybe updated args.
 */
function farthest_scheuled_posting_query_args( $args = array() ) {
	$defaults = array(
		'post_type'      => TWSC_POST_TYPE,
		'meta_key'       => '_twsc_timetosend', // main sort key is based on send time.
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'relation' => 'OR', // check if already sent OR that key doesn't exist yet.
				array(
					'key'     => '_twsc_already_sent',
					'value'   => '1', // 1 indicates already sent.
					'compare' => '!=',
				),
				array(
					'key'     => '_twsc_already_sent',
					'compare' => 'NOT EXISTS', // not exists indicates that it's not yet sent.
				),
			),
		),
		'orderby'        => 'meta_value_num', // order by number.
		'order'          => 'DESC',
		'posts_per_page' => 1,
	);
	if ( is_array( $args ) ) {
		$args = wp_parse_args( $args, $defaults );
	}
	return apply_filters( 'twsc_next_posting_query_args', $args );
}

/**
 * Determine an optimal set of schedule times based on the options set.
 *
 * @method determine_schedule
 */
function determine_schedule() {
	// get nearest current time and in 3 days based on nearest min and max times.
	$options_advanced = get_option( 'twitter_scheduler_advanced_settings' );

	$min_time = $options_advanced['prefered_time_min'];
	$max_time = $options_advanced['prefered_time_max'];

	$args = array(
		'post_type'      => TWSC_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		'meta_key'       => '_twsc_timetosend',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'relation' => 'OR', // check if already sent OR that key doesn't exist yet.
				array(
					'key'     => '_twsc_already_sent',
					'value'   => '1', // 1 indicates already sent.
					'compare' => '!=',
				),
				array(
					'key'     => '_twsc_already_sent',
					'compare' => 'NOT EXISTS', // not exists indicates that it's not yet sent.
				),
			),
			array(
				'key'     => '_twsc_readytosend', // this is toggle to say it's ready to send.
				'value'   => '1',
				'compair' => '=',
			),
			array(
				'relation' => 'OR',
				array(
					'key'     => '_twsc_timetosend',
					'compair' => '<',
				),
				array(
					'key'     => '_twsc_timetosend',
					'compair' => '>',
				),
			),
		),
	);
}
