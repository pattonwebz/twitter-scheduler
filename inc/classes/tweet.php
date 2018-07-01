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
 */
class Tweet {

	/**
	 * If this tweet object holds a tweet that has been sent already then this
	 * is where the ID of that tweet will be found as a string. NULL otherwise.
	 *
	 * @var null|string
	 */
	public $id = null;

	/**
	 * The type of tweet to send. Default is 'tweet' but may also be 'retweet'.
	 *
	 * @var string
	 */
	public $type = 'tweet';

	/**
	 * Hold the current status of this tweet. It could be used to hold a string
	 * like 'scheduled', 'sent', 'failed' etc.
	 *
	 * @var null|string
	 */
	public $status = null;

	/**
	 * Text content that is to be sent as the actual tweet content.
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * A string to hold an ID of a retweet this object will represent.
	 *
	 * @var string
	 */
	public $retweet_id = '';

	/**
	 * Should hold a unix timestamp for when the tweet is scheduled to send.
	 *
	 * @var string
	 */
	public $scheduled_time = '';

	/**
	 * Property to hold the timestamp of when this object was created.
	 *
	 * @var string
	 */
	public $creation_time = '';

	/**
	 * At creation time store the current servertime in the object.
	 *
	 * @method __construct
	 */
	public function __construct() {
		$this->creation_time = get_the_time( 'U' );
	}

}
