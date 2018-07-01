<?php
/**
 * The base.php file holds all the functions required for the Twitter Scheduler
 * plugin to get setup and running.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler;

use PattonWebz\TwitterScheduler\Helpers;
use PattonWebz\TwitterScheduler\PostType;
use PattonWebz\TwitterScheduler\Metabox;
use PattonWebz\TwitterScheduler\Admin\Page;

/**
 * Display a notice on sections of admin is there is no API key set.
 *
 * @since  0.1.0
 */
function display_notice() {
	global $hook_suffix;

	if ( ! in_array( $hook_suffix, [ 'edit.php', 'post-new.php', 'post.php' ], true ) ) {
		return;
	}

	$options      = get_option( 'twitter_scheduler_settings' );
	$oauth_token  = isset( $options['oauth_access_token'] ) ? $options['oauth_access_token'] : false;
	$oauth_secret = isset( $options['oauth_access_token_secret'] ) ? $options['oauth_access_token_secret'] : false;
	$key          = isset( $options['consumer_key'] ) ? $options['consumer_key'] : false;
	$secret       = isset( $options['consumer_secret'] ) ? $options['consumer_secret'] : false;

	// if we are missing any one of the 4 values then show the notice.
	if ( $oauth_token && $oauth_secret && $key && $secret ) {
		return;
	}

	$url = add_query_arg( [ 'page' => 'twitter_scheduler' ], admin_url( 'options-general.php' ) );
	?>

<div class="notice notice-warning is-dismissible">
<p>
	<?php
	/* translators: 1: a url to page in the admin area */
	printf( wp_kses( __( 'Almost done. <a href="%s">Setup your API keys</a> to schedule Tweets.', 'twitter-scheduler' ), [ 'a' => [ 'href' => [] ] ] ), esc_url( $url ) );
	?>
</p>
</div>

	<?php
}
add_action( 'admin_notices', __NAMESPACE__ . '\\display_notice' );

/**
 * Handle all the base setup work needed for the plugin to work. This is the
 * earlierst action we're hooking into so all setup work is best placed here.
 *
 * @since  0.1.0
 *
 * @method setup
 */
function setup() {
	// register the post_type.
	add_post_types();
	// register the metaboxes.
	add_meta_boxes();
	// register the admin pages.
	add_admin_pages();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\setup' );

/**
 * Adds the plugins post types.
 *
 * @since  0.1.0
 *
 * @method add_post_types
 */
function add_post_types() {
	$cpt = new PostType\Tweets();
	$cpt->register();
	$listmods = new Admin\TweetsListMods();
	$listmods->setup();
}

/**
 * Adds the plugins meta boxes.
 *
 * @since  0.1.0
 *
 * @method add_meta_boxes
 */
function add_meta_boxes() {
	$tweetbox = new Metabox\TweetBox();
	$tweetbox->register();
	$retweetbox = new Metabox\RetweetBox();
	$retweetbox->register();
}

/**
 * Adds the plugins admin pages.
 *
 * @since  0.1.0
 *
 * @method add_admin_pages
 */
function add_admin_pages() {
	$settings = new Page\Settings();
	$settings->register();
	$settingsadvanced = new Page\SettingsAdvanced();
	$settingsadvanced->register();
	// load the debug page only when debug mode is active in WP.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$debug = new Page\Debug();
		$debug->register();
	}
}

/**
 * The main runner function for the plugin, it's the function that is triggered
 * on cron.
 *
 * The runner decides if we have a post to send, if it's valid and if so pushes
 * it out to twitter then stores the response for later reference.
 *
 * @since  0.1.0
 * @method runner
 */
function runner() {

	// args to get the CPT based on a slightly complicated meta query.
	$args = Helpers\next_scheduled_posting_query_args();
	// get any posts scheduled to go out today.
	$sosc_query = new \WP_Query( $args );
	// The Loop.
	if ( $sosc_query->have_posts() ) {
		while ( $sosc_query->have_posts() ) {
			$sosc_query->the_post();
			if ( ! get_post_meta( get_the_id(), '_twitter_sent' ) ) {
				$scheduled_time  = new \datetime( get_post_meta( get_the_id(), 'sosc-datepicker', true ) . ' ' . get_post_meta( get_the_id(), 'sosc-timepicker', true ) );
				$time_difference = $scheduled_time->diff( new \datetime() );
				$time_difference = $time_difference->format( '%R%i' );
				$count           = 0;
				str_replace( '+', '', $time_difference, $count );
				if ( $count ) {
					// we are positive on time - meaning schedule has passed.
					if ( (int) $time_difference < 60 ) {
						/**
						 * Within an hour of scheduled time, maybe send.
						 *
						 * Either a retweet or a tweet. Handle both cases.
						 */
						$tweet      = get_post_meta( get_the_id(), '_twsc_tweet_obj', true );
						$retweet_id = $tweet->retweet_id;
						$twitter    = new TwitterInteractions();

						if ( (integer) $retweet_id > 0 ) {
							$twitter->send_retweet( get_the_ID(), $retweet_id );
						} else {
							$content = get_the_content();
							if ( Helpers\is_tweet_valid( $content ) ) {
								$twitter->send_tweet( get_the_id(), $content );
								$yoo = 'hoo';
							};
						}
					}
				}
			}
		}
		wp_reset_postdata();
	} // End if().
	// do a url limit check to update the cached t.co max length if needed.
	Helpers\check_tco_short_url_limit();
	Helpers\determine_schedule();

}
add_action( 'sosc_schedule_hooks', __NAMESPACE__ . '\\runner' );
