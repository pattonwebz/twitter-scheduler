<?php
/**
 * An abstract class used to create metaboxes. Just provide a save and a render
 * method as well as updating the $id and $title properties.
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
 * Abstract class to make creating metaboxes easier.
 *
 * @since  0.1.0
 */
abstract class AbstractMetaBox {

	/**
	 * A string ID to use for the metabox.
	 *
	 * @since  0.1.0
	 *
	 * @var string|null
	 */
	public $id = null;

	/**
	 * A text string to hold the title.
	 *
	 * @since  0.1.0
	 *
	 * @var string|null
	 */
	public $title = null;

	/**
	 * Array of the supported screen string where the metabox should appear.
	 *
	 * @since  0.1.0
	 *
	 * @var array
	 */
	public $supported_screens = [
		TWSC_POST_TYPE,
	];

	/**
	 * Adds the actions to place metabox in it's screen and hooks in the save action.
	 *
	 * @since  0.1.0
	 * @method register
	 */
	public function register() {
		add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );
		add_action( 'save_post', [ $this, 'save_metabox' ], 10, 3 );
	}

	/**
	 * Adds a meta box in the editor for social schedule settings.
	 *
	 * @since  0.1.0
	 * @method add_metabox
	 */
	public function add_metabox() {
		add_meta_box( $this->id, $this->title, [ $this, 'render' ], $this->supported_screens, 'side', 'high', null );
	}

	/**
	 * Outputs a block of markup containing a nonce and form inputs.
	 *
	 * @since  0.1.0
	 *
	 * @param  object $object contains an object with some post info.
	 */
	abstract public function render( $object );

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
	abstract public function save_metabox( $post_id, $post, $update );

}
