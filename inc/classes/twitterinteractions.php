<?php
/**
 * This is the class which holds any direct interaction that occurs over the
 * Twitter API.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler;

use PattonWebz\TwitterScheduler\Helpers;
use PattonWebz\TwitterSchedler\Tweet;

/**
 * Holder for any functions that interact with twitter API.
 */
class TwitterInteractions {

	const TWEET_URL  = 'https://api.twitter.com/1.1/statuses/update.json';
	const CONFIG_URL = 'https://api.twitter.com/1.1/help/configuration.json';
	const MEDIA_URL  = 'https://upload.twitter.com/1.1/media/upload.json';
	// This enpoint needs the original Tweet ID added appended along with .json:
	// example: `$suffix = '97324934923874274398.json'`.
	const RETWEET_URL_PARTIAL = 'https://api.twitter.com/1.1/statuses/retweet/';

	/**
	 * Updates the stored value for the latest max tco shortened url length along
	 * with a timestamp so we can recheck every 24 hours.
	 */
	public static function update_tco_short_url_limit() {

		// this is the main config endpoint and we use 'GET' for it.
		$request_method = 'GET';

		// Create a new object, passing auth credentials, and make the request.
		$twitter  = new \TwitterAPIExchange( Helpers\get_twitter_settings() );
		$response = $twitter->buildOauth( self::CONFIG_URL, $request_method )->performRequest();

		// Did we get a response?
		if ( $response ) {
			// TODO: check the response isn't an error!
			// decode the json object to an array.
			$response = json_decode( $response, true );

			$length = (int) $response['short_url_length_https'];
			// if we have a length and it's over 15 (experimentation lets me know
			// it's always going to be more than this).
			if ( is_int( $length ) && $length > 15 ) {
				// since we have an integer let's store it with timestamp as array.
				$value = array(
					'length'    => $length,
					'timestamp' => current_time( 'timestamp', true ),
				);
				// update the limit with current length and timestamp.
				update_option( 'twsc_config_short_url_length', $value, true );
			}
		}
	}

	/**
	 * Function to attach media to tweets - by uploading it to Twitter first
	 * and storing the media id that they return.
	 *
	 * @param  integer $post_id the post ID which we'll be saving data to.
	 * @return string|void      the attached media id for storage.
	 */
	public function attach_media( $post_id = 0 ) {

		if ( 0 !== absint( $post_id ) ) {
			$media_id = get_post_thumbnail_id( $post_id );
			if ( $media_id ) {
				/**
				 * We have a post thumbnail to work with, get the url.
				 */
				$media_url = wp_get_attachment_url( $media_id );
				if ( filter_var( $media_url, FILTER_VALIDATE_URL ) ) {
					$image = file_get_contents( $media_url );
					// base 64 encoded the image so it can be sent easily as a string.
					$image          = base64_encode( $image );
					$request_method = 'POST';
						/** POST fields required by the URL above. See relevant docs as above */
					$postfields = array(
						'media' => $image,
					);

					$twitter = new \TwitterAPIExchange( Helpers\get_twitter_settings() );

					$response = $twitter->buildOauth( self::MEDIA_URL, $request_method )
						->setPostfields( $postfields )
						->performRequest();

					$response = json_decode( $response );

					if ( is_object( $response ) ) {
						/**
						 * Get this posts `tweet` object and store the media info.
						 */
						$tweet_obj = get_post_meta( $post_id, '_twsc_tweet_obj', true );
						if ( $tweet_obj ) {
							if ( ! is_object( $tweet_obj ) && is_array( $tweet_obj ) ) {
								$tweet_obj = $tweet_obj[0];
							}
						} else {
							$tweet_obj = new Tweet();
						}
						$tweet_obj->media_id_string = $response->media_id_string;
						$tweet_obj->media_id_exires = get_the_time( 'U' ) + ( $response->expires_after_secs - 30 );
						update_post_meta( $post_id, '_twsc_tweet_obj', $tweet_obj );
						// for back compat also store the info inside root postmeta.
						update_post_meta( $post_id, '_media_id_string', $response->media_id_string );
						update_post_meta( $post_id, '_media_id_expires', get_the_time( 'U' ) + ( $response->expires_after_secs - 30 ) );
						// return the media_id_string.
						return $response->media_id_string;
					}
				}
			}
		}
	}

	/**
	 * Sends a retweet based on a tweet id that is passed.
	 *
	 * @param integer $post_id    the id of post we'll update meta for.
	 * @param integer $retweet_id the ido of the tweet we're retweeting.
	 *
	 * @return void;
	 */
	public static function send_retweet( $post_id, $retweet_id ) {
		$request_method = 'POST';

		$postfields['id'] = $retweet_id;

		$twitter  = new \TwitterAPIExchange( Helpers\get_twitter_settings() );
		$response = $twitter->buildOauth( self::RETWEET_URL_PARTIAL . $retweet_id . '.json', $request_method )
			->setPostfields( $postfields )
			->performRequest();
		$response = json_decode( $response );
		if ( is_object( $response ) ) {
			if ( ! property_exists( $response, 'errors' ) ) {

				/**
				 * Get this posts `tweet` object and store the statuses.
				 */
				$tweet_obj = get_post_meta( $post_id, '_twsc_tweet_obj', true );
				if ( $tweet_obj ) {
					if ( ! is_object( $tweet_obj ) && is_array( $tweet_obj ) ) {
						$tweet_obj = $tweet_obj[0];
					}
				} else {
					$tweet_obj = new Tweet();
				}
				$tweet_obj->id       = $response->id_str;
				$tweet_obj->response = $response;
				$tweet_obj->status   = 'sent';
				update_post_meta( $post_id, '_twsc_tweet_obj', $tweet_obj );

				update_post_meta( $post_id, '_twsc_already_sent', true );

			}
		}
	}

	/**
	 * Function to send a tweet.
	 *
	 * This function optionally attaches images as needed.
	 *
	 * @param  integer $post_id       the post ID that we're working from.
	 * @param  string  $tweet_content the contents of the tweet, already validated.
	 */
	public static function send_tweet( $post_id, $tweet_content ) {
		$request_method = 'POST';
		/** POST fields required by the URL above. See relevant docs as above */
		$postfields = array(
			'status' => $tweet_content,
		);
		/**
		 * Get any attached media ids.
		 */
		$tweet_media_id = Helpers\get_valid_media_ids( $post_id );
		if ( $tweet_media_id ) {
			$postfields['media_ids'] = $tweet_media_id;
		}
		/**
		 * Authenticate with API and send our request.
		 */
		$twitter  = new \TwitterAPIExchange( Helpers\get_twitter_settings() );
		$response = $twitter->buildOauth( self::TWEET_URL, $request_method )
			->setPostfields( $postfields )
			->performRequest();
		$response = json_decode( $response );
		if ( is_object( $response ) ) {
			if ( ! property_exists( $response, 'errors' ) ) {

				/**
				 * Get this posts `tweet` object and store the statuses.
				 */
				$tweet_obj = get_post_meta( $post_id, '_twsc_tweet_obj', true );
				if ( $tweet_obj ) {
					if ( ! is_object( $tweet_obj ) && is_array( $tweet_obj ) ) {
						$tweet_obj = $tweet_obj[0];
					}
				} else {
					$tweet_obj = new Tweet();
				}
				$tweet_obj->id       = $response->id_str;
				$tweet_obj->response = $response;
				$tweet_obj->status   = 'sent';
				update_post_meta( $post_id, '_twsc_tweet_obj', $tweet_obj );

				update_post_meta( $post_id, '_twsc_already_sent', true );
			}
		}
	}

}
