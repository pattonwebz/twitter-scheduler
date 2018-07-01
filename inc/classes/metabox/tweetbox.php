<?php
/**
 * A metabox setup to collect a scheduling info from the user and save it to the
 * database as part of an updated `Tweet` object that is stored in post_meta.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Metabox;

use \PattonWebz\TwitterScheduler\Helpers;
use \PattonWebz\TwitterScheduler\Tweet;

/**
 * An class to create the main tweet box metabox.
 */
class TweetBox extends AbstractMetaBox {

	/**
	 * A string ID to use for the metabox.
	 *
	 * @var string|null
	 */
	public $id = 'twsc_meta_box';

	/**
	 * Setup the meta boxes title.
	 *
	 * @method __construct
	 */
	public function __construct() {
		$this->title = esc_html__( 'Twitter Scheduler', 'twitter-scheduler' );
	}

	/**
	 * Adds the actions to place metabox in it's screen and hooks in the save action.
	 *
	 * @method register
	 */
	public function register() {
		parent::register();
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
	}

	/**
	 * Outputs a block of markup containing a nonce and form inputs.
	 *
	 * @param  object $object contains and object with some post info.
	 */
	public function render( $object ) {
		wp_nonce_field( basename( __FILE__ ), 'twsc-post-nonce' );

		?>
			<div id="twsc-time-options">
				<?php /* translators: 1 = a date and time */ ?>
				<p><?php echo sprintf( esc_html__( 'Current Time: %1$s', 'twitter-scheduler' ), esc_html( get_the_time( 'd/m/Y G:i' ) ) ); ?></p>
				<h3><?php esc_html_e( 'Send Time', 'twitter-scheduler' ); ?></h3>
				<label for="twsc_datepicker" class="screen-reader-text"><?php echo sprintf( esc_html( '%1$sSchedule %2$sDate:', 'twitter-scheduler' ), '<span class="screen-reader-text">', '</span>' ); ?></label>
				<input id="twsc_datepicker" name="twsc_datepicker" type="date" value="<?php echo esc_textarea( get_post_meta( $object->ID, '_twsc_datepicker', true ) ); ?>">
				<label for="twsc_timepicker" class="screen-reader-text"><?php echo sprintf( esc_html( '%1$sSchedule %2$sTime:', 'twitter-scheduler' ), '<span class="screen-reader-text">', '</span>' ); ?></label>
				<input id="twsc_timepicker" name="twsc_timepicker" type="time" value="<?php echo esc_textarea( get_post_meta( $object->ID, '_twsc_timepicker', true ) ); ?>">
				<br>
				<p class="howto"><?php esc_html_e( 'You can these buttons autofill the time.', 'twitter-scheduler' ); ?></p>
				<label for="twsc_datateime_now" class="screen-reader-text"><?php esc_html_e( 'Set Date and Time to now.', 'twitter-scheduler' ); ?></label>
				<button id="twsc_datateime_now" name="twsc_datateime_now" type="button" class="button"><?php esc_html_e( 'Now', 'twitter-scheduler' ); ?></button>
				<label for="twsc_datetime_next" class="screen-reader-text"><?php esc_html_e( 'Set Date and Time to farthest in future.', 'twitter-scheduler' ); ?></label>
				<button id="twsc_datetime_next" name="twsc_datetime_next" type="button" class="button"><?php esc_html_e( 'Farthest', 'twitter-scheduler' ); ?></button>
			</div>
		<?php
	}

	/**
	 * Saves the data passed to the custom metabox.
	 *
	 * Returns just the $post_id on failure.
	 *
	 * @param  string $post_id id of the post.
	 * @param  object $post    original post object.
	 * @param  object $update  post updated object.
	 * @return integer|void
	 */
	public function save_metabox( $post_id, $post, $update ) {

		// verify we have a nonce passed and it's the nonce we expect.
		if ( ! isset( $_POST['twsc-post-nonce'] ) || ! wp_verify_nonce( $_POST['twsc-post-nonce'], basename( __FILE__ ) ) ) {
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

		/**
		 * A whole collection of some default values to save in place of empty.
		 */
		$datepicker  = '';
		$timepicker  = '';
		$network     = '';
		$readytosend = '0';

		$tweet_obj = get_post_meta( $post_id, '_twsc_tweet_obj', true );
		// $tweet_obj = null;
		if ( $tweet_obj ) {
			if ( ! is_object( $tweet_obj ) && is_array( $tweet_obj ) ) {
				$tweet_obj = $tweet_obj[0];
			} else {
				$tweet_obj = new Tweet();
			}
		} else {
			$tweet_obj = new Tweet();
		}

		$tweet_obj->content = $post->post_content;
		// datepicker info.
		if ( isset( $_POST['twsc_datepicker'] ) ) {
			$datepicker = $_POST['twsc_datepicker'];
		}
		update_post_meta( $post_id, '_twsc_datepicker', $datepicker );

		// timepicker value.
		if ( isset( $_POST['twsc_timepicker'] ) ) {
			$timepicker = $_POST['twsc_timepicker'];
		}
		update_post_meta( $post_id, '_twsc_timepicker', $timepicker );

		// if we have both a time and date then use it to make a timestamp - this
		// is used later for easier time checking.
		if ( ( isset( $_POST['twsc_datepicker'] ) && '' !== $_POST['twsc_datepicker'] ) && ( isset( $_POST['twsc_timepicker'] ) && '' !== $_POST['twsc_timepicker'] ) ) {
			$timestamptosend = strtotime( $_POST['twsc_datepicker'] . ' ' . $_POST['twsc_timepicker'] );
			update_post_meta( $post_id, '_twsc_timetosend', $timestamptosend );
			$tweet_obj->scheduled_time = $timestamptosend;
		}

		if ( null === $tweet_obj->status ) {
			$tweet_obj->status = 'scheduled';
		}
		update_post_meta( $post_id, '_twsc_tweet_obj', $tweet_obj );
	}

	/**
	 * Adds some functions for use within our editor space.
	 *
	 * @param  string $hook a string that contains the hook that is firing.
	 * @return void
	 */
	public function enqueue_editor_scripts( $hook ) {
		global $post;
		// if we're not on the edit screen then return.
		if ( 'post-new.php' !== $hook && 'post.php' !== $hook ) {
			return;
		}
		if ( TWSC_POST_TYPE === $post->post_type ) {
			wp_register_script( 'twitter-text', TWSC_PLUGIN_URL . 'assets/js/twitter-text/twitter-text.min.js', array( 'jquery' ), '2.0.0' );

			// enqueue the editor script we have functions inside.
			wp_enqueue_script( 'twsc-admin', TWSC_PLUGIN_URL . 'assets/js/editor.js', array( 'twitter-text' ) );
			// make a new query for getting the scheduled item farthest in the future.
			$newquery = new \WP_Query( Helpers\farthest_scheuled_posting_query_args() );

			$options_advanced = get_option( 'twitter_scheduler_advanced_settings' );
			$data = array(
				'prefered_time_min' => $options_advanced['prefered_time_min'],
				'prefered_time_max' => $options_advanced['prefered_time_max'],
				'times_per_weekday' => $options_advanced['times_per_weekday'],
				'max_link_length'   => get_option( 'twsc_config_short_url_length', false ),
			);
			if ( $newquery->have_posts() ) {
				// if we have posts (should have max 1) then we need to foreach
				// loop through them. See: https://core.trac.wordpress.org/ticket/18408.
				foreach ( $newquery->get_posts() as $p ) {
					$timetosend                 = get_post_meta( $p->ID, '_twsc_timetosend', true );
					$data['farthest_scheduled'] = $timetosend ? $timetosend : get_the_time( 'U' );
				}
			}
			wp_localize_script( 'twsc-admin', 'twsc_data', $data );
		}

	}

}
