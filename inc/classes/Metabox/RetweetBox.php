<?php
/**
 * A metabox setup to collect a retweet id from the user and save it to the
 * database as part of an updated `Tweet` object that is stored in post_meta.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Metabox;

use PattonWebz\TwitterSchedler\Tweet;

/**
 * Adds a metabox to capture retweet id and handles the saving of it's data.
 *
 * @since  0.1.0
 */
class RetweetBox extends AbstractMetaBox {

	/**
	 * The id of this meta box.
	 *
	 * @since  0.1.0
	 *
	 * @var string
	 */
	public $id = 'twsc_retweet_metabox';

	/**
	 * Setup the meta boxes title.
	 *
	 * @since  0.1.0
	 * @method __construct
	 */
	public function __construct() {
		$this->title = esc_html__( 'Retweet', 'twitter-scheduler' );
	}

	/**
	 * Outputs a block of markup containing a nonce and form inputs.
	 *
	 * @since  0.1.0
	 *
	 * @param  object $object contains and object with some post info.
	 */
	public function render( $object ) {
		wp_nonce_field( basename( __FILE__ ), 'twsc-post-retweet-nonce' );
		?>
			<div id="twsc-network-options">
				<p class="howto"><?php esc_html_e( 'NOTE: Having any value in this field will force this to be a retweet, other values may be ignored.', 'twitter-scheduler' ); ?></p>
				<label for="_twsc_retweet_id"><?php esc_html_e( 'Retweet ID:', 'twitter-scheduler' ); ?></label>
				<input id="_twsc_retweet_id" name="_twsc_retweet_id" type="text" class="widefat" value="<?php echo esc_textarea( get_post_meta( $object->ID, '_twsc_retweet_id', true ) ); ?>">
			</div>
		<?php
	}

	/**
	 * Saves the data passed to the custom metabox.
	 *
	 * Returns just the $post_id on failure.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $post_id id of the post.
	 * @param  object $post    original post object.
	 * @param  object $update  post updated object.
	 * @return integer|void
	 */
	public function save_metabox( $post_id, $post, $update ) {

		// verify we have a nonce passed and it's the nonce we expect.
		if ( ! isset( $_POST['twsc-post-retweet-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['twsc-post-retweet-nonce'] ) ), basename( __FILE__ ) ) ) {
			return $post_id;
		}
		// Users must have the 'edit_post' permission to update the meta for it.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// if this is just an autosave then skip.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// if this isn't the post type we are after then skip.
		$cpt_slug = TWSC_POST_TYPE;
		if ( $cpt_slug !== $post->post_type ) {
			return $post_id;
		}

		$retweet_id = 0;
		$tweet_obj  = get_post_meta( $post_id, '_twsc_tweet_obj', true );
		// $tweet_obj = null;
		if ( $tweet_obj ) {
			if ( ! is_object( $tweet_obj ) && is_array( $tweet_obj ) ) {
				$tweet_obj = $tweet_obj[0];
			}
		} else {
			$tweet_obj = new Tweet();
		}

		// the retweet id if it is one.
		if ( isset( $_POST['_twsc_retweet_id'] ) ) {
			$retweet_id = (integer) $_POST['_twsc_retweet_id'];
			if ( $retweet_id > 0 ) {
				$tweet_obj->type       = 'retweet';
				$tweet_obj->content    = '';
				$tweet_obj->retweet_id = (string) $retweet_id;
			}
		}
		update_post_meta( $post_id, '_twsc_retweet_id', $retweet_id );
		update_post_meta( $post_id, '_twsc_tweet_obj', $tweet_obj );

	}

}
