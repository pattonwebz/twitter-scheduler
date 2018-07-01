<?php
/**
 * Any updates or modifications that are done for the post list admin screen
 * where our social scheduled posts are held.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Admin;

use PattonWebz\TwitterScheduler\Tweet;

/**
 * Adds and reorders some colums on our tweets post list page as well as setting
 * which can be sorted by, adds a new 'Duplicate' link to posts actions row.
 */
class TweetsListMods {

	/**
	 * The public callable setup method for hooking in this classes functions.
	 *
	 * @method setup
	 */
	public function setup() {
		add_filter( 'manage_' . TWSC_POST_TYPE . '_posts_columns', [ $this, 'configure_new_cols' ] );
		add_filter( 'manage_edit-' . TWSC_POST_TYPE . '_sortable_columns', [ $this, 'sortable_cols' ] ); // allows sorting on the cols.
		add_action( 'manage_' . TWSC_POST_TYPE . '_posts_custom_column', [ $this, 'add_col_contents' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ] );
		add_filter( 'post_row_actions', [ $this, 'add_copy_row_action' ], 10, 2 );
		add_action( 'wp_ajax_copy_tweet', [ $this, 'ajax_copy_tweet' ] );
	}

	/**
	 * Add some cols to the admin table for our CPT and remove uneeded ones.
	 *
	 * @param array $cols all the currently set cols for this admin table.
	 */
	public function configure_new_cols( $cols ) {
		// add some new cols for the admin table.
		$cols_new = [];
		if ( array_key_exists( 'cb', $cols ) ) {
			$cols_new['cb'] = $cols['cb'];
		}
		$cols_new['twsc_type']    = __( 'Type', 'twitter-scheduler' );
		$cols_new['title']        = $cols['title'];
		$cols_new['twsc_excerpt'] = __( 'Tweet', 'twitter-scheduler' );
		$cols_new['twsc_time']    = __( 'Time', 'twitter-scheduler' );
		$cols_new['twsc_status']  = __( 'Status', 'twitter-scheduler' );
		// return the updated cols.
		return $cols_new;
	}

	/**
	 * Array of adming page list cols we want to be able to sort by.
	 *
	 * @method sortable_cols
	 * @param  array $cols list of current cols.
	 * @return array       list of cols we want to sort with.
	 */
	public function sortable_cols( $cols ) {
		$sortable['twsc_type']   = __( 'Type', 'twitter-scheduler' );
		$sortable['twsc_time']   = __( 'Time', 'twitter-scheduler' );
		$sortable['twsc_status'] = __( 'Status', 'twitter-scheduler' );
		return $sortable;
	}

	/**
	 * Add the content of each col for each entry in the list.
	 *
	 * @param string  $column_name the column name we're on.
	 * @param integer $post_id     the id of the current post.
	 */
	public function add_col_contents( $column_name, $post_id ) {

		$tweet = get_post_meta( $post_id, '_twsc_tweet_obj', true );
		// if we can't get a tweet object for this post then bail early.
		if ( ! is_object( $tweet ) ) {
			return;
		}

		if ( 'twsc_time' === $column_name ) {
			/**
			 * In scheduled time fields we put a formatted datetime.
			 */
			echo '<p>' . esc_html( date( 'D, d/m/y H:i', $tweet->scheduled_time ) ) . '</p>';
		} elseif ( 'twsc_type' === $column_name ) {
			switch ( $tweet->type ) {
				case 'tweet':
					echo '<div class="dashicons-before dashicons-twitter twsc-blue"><span class="screen-reader-text">Tweet</span></div>';
					break;
				case 'retweet':
					echo '<div class="dashicons-before dashicons-share-alt2"><span class="screen-reader-text">Retweet</span></div>';
					break;

			}
		} elseif ( 'twsc_excerpt' === $column_name ) {
			if ( 'retweet' === $tweet->type ) {
				echo '<code>' . absint( $tweet->retweet_id ) . '</code>';
			} else {
				echo wp_kses_post( wpautop( $tweet->content ) );
			}
		} elseif ( 'twsc_status' === $column_name ) {
			switch ( $tweet->status ) {
				case 'scheduled':
					$time = get_the_time( 'U' );
					if ( $tweet->scheduled_time > ( $time + 30 ) ) {
						$color = 'green';
					} else {
						$color = 'red';
					}
					echo '<div class="dashicons-before dashicons-clock twsc-' . esc_attr( $color ) . '"><span class="screen-reader-text">Scheduled</span></div>';
					break;
				case 'sent':
					echo '<div class="dashicons-before dashicons-yes twsc-green"><span class="screen-reader-text">Sent</span></div>';
					break;
				case 'failed':
					echo '<div class="dashicons-before dashicons-no-alt twsc-red"><span class="screen-reader-text">Failed</span></div>';
					break;
				default:
					echo '<div class="dashicons-before dashicons-clock twsc-red"><br></div>';

			}
		} // End if().
	}

	/**
	 * Adds any stylesheets or scripts to the social posts admin table.
	 *
	 * @param string $hook the hook we're on to test for.
	 */
	public function add_admin_scripts( $hook ) {
		global $post;
		if ( 'edit.php' === $hook ) {
			if ( is_object( $post ) && TWSC_POST_TYPE === $post->post_type ) {
				wp_enqueue_style( 'twsc-admin', TWSC_PLUGIN_URL . 'inc/admin/css/tables.css' );
				wp_enqueue_script( 'twsc-copy', TWSC_PLUGIN_URL . 'inc/admin/js/copypost.js' );
				wp_localize_script( 'twsc-copy', 'twsc_copy',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
					)
				);
			}
		}
	}

	/**
	 * Add a link to the post action row that can be used to trigger a 'copy'
	 * action that will allow recheduling of posts that have already sent or
	 * are still scheduled.
	 *
	 * @method add_copy_row_action
	 * @param  array  $actions list of current post action row links.
	 * @param  object $post    a post object.
	 * @return array           updated actions list.
	 */
	public function add_copy_row_action( $actions, $post ) {
		// Check for your post type.
		if ( TWSC_POST_TYPE === $post->post_type ) {

			// You can check if the current user has some custom rights.
			if ( current_user_can( 'edit_posts', $post->ID ) ) {

				// Add the new Copy quick link.
				$actions = array_merge(
					$actions, array(
						'copy' => sprintf(
							'<a class="twsc-copy-trigger" href="#" data-postid="%2$d">%1$s</a>',
							esc_html__( 'Duplicate', 'twitter-scheduler' ),
							$post->ID
						),
					)
				);

			}
		}
		return $actions;
	}

	/**
	 * Acepts the ajax requires for dupicating entries.
	 *
	 * @method ajax_copy_tweet
	 * @return void|int
	 */
	function ajax_copy_tweet() {

		if ( ! isset( $_POST['postID'] ) ) {
			return;
		}

		$post_id = $_POST['postID'];
		// Make your response and echo it.
		$title       = get_the_title( $post_id );
		$oldpost     = get_post( $post_id );
		$post        = array(
			'post_title'   => $title,
			'post_status'  => 'draft',
			'post_type'    => $oldpost->post_type,
			'post_author'  => 1,
			'post_content' => $oldpost->post_content,
		);
		$new_post_id = wp_insert_post( $post );
		// Copy post metadata.
		$data = get_post_custom( $post_id );
		foreach ( $data as $key => $values ) {
			foreach ( $values as $value ) {
				add_post_meta( $new_post_id, $key, $value );
			}
		}
		$tweet_original = get_post_meta( $post_id, '_twsc_tweet_obj', true );

		$tweet                 = new Tweet();
		$tweet->status         = 'copy';
		$tweet->content        = $tweet_original->content;
		$tweet->scheduled_time = $tweet_original->scheduled_time;

		update_post_meta( $new_post_id, '_twsc_tweet_obj', $tweet );

		// Don't forget to stop execution afterward.
		wp_die( absint( $new_post_id ) );
	}

}
