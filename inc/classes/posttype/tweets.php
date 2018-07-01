<?php
/**
 * Handles adding our CPT for Tweets.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\PostType;

/**
 * Creates a `TWSC_POST_TYPE` post type to use for input and storing Tweets.
 */
class Tweets {

	/**
	 * Hook in and register the post type.
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_cpt' ], 0 );
	}
	/**
	 * Get the name of this post type.
	 *
	 * @method get_name
	 * @return string
	 */
	public function get_name() {
		return TWSC_POST_TYPE;
	}

	/**
	 * Get the labels used with this post type.
	 *
	 * @method get_labels
	 * @return array
	 */
	public function get_labels() {
		return array(
			'name'                  => _x( 'Tweet', 'Post Type General Name', 'twitter-scheduler' ),
			'singular_name'         => _x( 'Tweet', 'Post Type Singular Name', 'twitter-scheduler' ),
			'menu_name'             => __( 'Tweets', 'twitter-scheduler' ),
			'name_admin_bar'        => __( 'Tweet', 'twitter-scheduler' ),
			'archives'              => __( 'Item Archives', 'twitter-scheduler' ),
			'attributes'            => __( 'Item Attributes', 'twitter-scheduler' ),
			'parent_item_colon'     => __( 'Parent Item:', 'twitter-scheduler' ),
			'all_items'             => __( 'All Tweets', 'twitter-scheduler' ),
			'add_new_item'          => __( 'Schedule New Tweet', 'twitter-scheduler' ),
			'add_new'               => __( 'Schedule New', 'twitter-scheduler' ),
			'new_item'              => __( 'New Tweet', 'twitter-scheduler' ),
			'edit_item'             => __( 'Edit Tweet', 'twitter-scheduler' ),
			'update_item'           => __( 'Update Tweet', 'twitter-scheduler' ),
			'view_item'             => __( 'View Tweet', 'twitter-scheduler' ),
			'view_items'            => __( 'View Tweets', 'twitter-scheduler' ),
			'search_items'          => __( 'Search Tweet', 'twitter-scheduler' ),
			'not_found'             => __( 'Not found', 'twitter-scheduler' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'twitter-scheduler' ),
			'featured_image'        => __( 'Attached Image', 'twitter-scheduler' ),
			'set_featured_image'    => __( 'Set attached image', 'twitter-scheduler' ),
			'remove_featured_image' => __( 'Remove attached image', 'twitter-scheduler' ),
			'use_featured_image'    => __( 'Use as attached image', 'twitter-scheduler' ),
			'insert_into_item'      => __( 'Insert into Tweet', 'twitter-scheduler' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Tweet', 'twitter-scheduler' ),
			'items_list'            => __( 'Tweets list', 'twitter-scheduler' ),
			'items_list_navigation' => __( 'Tweets list navigation', 'twitter-scheduler' ),
			'filter_items_list'     => __( 'Filter Tweets list', 'twitter-scheduler' ),
		);
	}

	/**
	 * Get the post ype registration args.
	 *
	 * @method get_args
	 * @return array
	 */
	public function get_args() {
		return array(
			'label'               => __( 'Tweets', 'twitter-scheduler' ),
			'description'         => __( 'A CPT to hold Tweets', 'twitter-scheduler' ),
			'labels'              => $this->get_labels(),
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-twitter',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
	}
	/**
	 * Register the CPT used to hold posts.
	 */
	public function register_cpt() {
		register_post_type( $this->get_name(), $this->get_args() );
	}
}
