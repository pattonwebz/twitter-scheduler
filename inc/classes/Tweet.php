<?php
/**
 * A class intended to hold an arbitrary 'tweet' object for ease of storage.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler;

/**
 * Class used as a holder object to store all the Tweet data.
 *
 * @since  0.1.0
 */
class Tweet {

	/**
	 * If this tweet object holds a tweet that has been sent already then this
	 * is where the ID of that tweet will be found as a string. NULL otherwise.
	 *
	 * @since  0.1.0
	 *
	 * @var null|string
	 */
	public $id = null;

	/**
	 * The type of tweet to send. Default is 'tweet' but may also be 'retweet'.
	 *
	 * @since  0.1.0
	 *
	 * @var string
	 */
	public $type = 'tweet';

	/**
	 * Hold the current status of this tweet. It could be used to hold a string
	 * like 'scheduled', 'sent', 'failed' etc.
	 *
	 * @since  0.1.0
	 *
	 * @var null|string
	 */
	public $status = null;

	/**
	 * Text content that is to be sent as the actual tweet content.
	 *
	 * @since  0.1.0
	 *
	 * @since  0.1.0
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * A string to hold an ID of a retweet this object will represent.
	 *
	 * @since  0.1.0
	 *
	 * @var string
	 */
	public $retweet_id = '';

	/**
	 * Should hold a unix timestamp for when the tweet is scheduled to send.
	 *
	 * @since  0.1.0
	 *
	 * @var string
	 */
	public $scheduled_time = '';

	/**
	 * Property to hold the timestamp of when this object was created.
	 *
	 * @since  0.1.0
	 *
	 * @var string
	 */
	public $creation_time = '';

	/**
	 * At creation time store the current servertime in the object.
	 *
	 * @method __construct
	 * @since  0.1.0
	 */
	public function __construct() {
		$this->creation_time = get_the_time( 'U' );
	}

}
