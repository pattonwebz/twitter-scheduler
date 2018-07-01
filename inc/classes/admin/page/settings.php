<?php
/**
 * Class to create the main settings page for the plugin.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Admin\Page;

use PattonWebz\TwitterScheduler\Admin\AbstractAdminSubpage;

/**
 * The main settings page for the Twitter Scheduler Plugin. Gets the auth keys.
 *
 * @since  0.1.0
 */
class Settings extends AbstractAdminSubpage {

	/**
	 * The ID used to identify this page and it's assosiated settings.
	 *
	 * @since  0.1.0
	 *
	 * @var string|null
	 */
	public $id = TWSC_ADMIN_PAGE_ID;

	/**
	 * When constructing this object set the title.
	 *
	 * @since  0.1.0
	 * @method __construct
	 */
	public function __construct() {
		$this->title = esc_html__( 'Social Scheduler', 'twitter-scheduler' );
	}

	/**
	 * This is our primary settings page. It's the anchor all other pages are
	 * hooked into.
	 *
	 * @since  0.1.0
	 * @method add_admin_menu
	 */
	public function add_admin_menu() {
		add_menu_page( $this->title, $this->title, 'manage_options', $this->id, [ $this, 'render' ] );
		parent::add_admin_menu();
	}

	/**
	 * Register our settings and add our sections and setting fields.
	 *
	 * @since  0.1.0
	 * @method settings_init
	 */
	public function settings_init() {
		register_setting( $this->id, 'twitter_scheduler_settings' );

		add_settings_section(
			'twitter_scheduler_section',
			__( 'Options and Settings.', 'twitter-scheduler' ),
			[ $this, 'section_callback' ],
			'twitter_scheduler'
		);

		add_settings_field(
			'oauth_access_token',
			__( 'OAuth Access Token', 'twitter-scheduler' ),
			[ $this, 'oauth_access_token_render' ],
			'twitter_scheduler',
			'twitter_scheduler_section',
			array(
				'label_for' => 'twitter_scheduler_settings[oauth_access_token]',
			)
		);

		add_settings_field(
			'oauth_access_token_secret',
			__( 'OAuth Access Token Secret', 'twitter-scheduler' ),
			[ $this, 'oauth_access_token_secret_render' ],
			'twitter_scheduler',
			'twitter_scheduler_section',
			array(
				'label_for' => 'twitter_scheduler_settings[oauth_access_token_secret]',
			)
		);

		add_settings_field(
			'consumer_key',
			__( 'Consumer Key', 'twitter-scheduler' ),
			[ $this, 'consumer_key_render' ],
			'twitter_scheduler',
			'twitter_scheduler_section',
			array(
				'label_for' => 'twitter_scheduler_settings[consumer_key]',
			)
		);

		add_settings_field(
			'consumer_secret',
			__( 'Consumer Secret', 'twitter-scheduler' ),
			[ $this, 'consumer_secret_render' ],
			'twitter_scheduler',
			'twitter_scheduler_section',
			array(
				'label_for' => 'twitter_scheduler_settings[consumer_secret]',
			)
		);

		add_settings_section(
			'twitter_scheduler_section2',
			__( 'Extra Options (not required).', 'twitter-scheduler' ),
			[ $this, 'section2_callback' ],
			'twitter_scheduler'
		);
		add_settings_field(
			'username',
			__( 'Twitter Username (reserved for future possible use)', 'twitter-scheduler' ),
			[ $this, 'username_render' ],
			'twitter_scheduler',
			'twitter_scheduler_section2',
			array(
				'label_for' => 'twitter_scheduler_settings[username]',
			)
		);
	}

	/**
	 * Echos a title above certain sections.
	 *
	 * @since  0.1.0
	 */
	public function section_callback() {
		?>
		<p><?php esc_html_e( 'Enter your Twitter App credentials. You will need OAuth Access Tokens and Consumer Keys. Find out more about that here: ', 'twitter-scheduler' ) . esc_url( 'https://apps.twitter.com/' ); ?></p>
		<?php
	}

	/**
	 * Echos a title above certain sections.
	 *
	 * @since  0.1.0
	 */
	public function section2_callback() {
		?>
		<p><?php esc_html_e( 'These options may or may not be of value.', 'twitter-scheduler' ); ?></p>
		<?php
	}

	/**
	 * Holds the twitter oaccess token.
	 *
	 * Renders an input box to hold a settings value.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function oauth_access_token_render() {

		$options = get_option( 'twitter_scheduler_settings' );
		?>
		<input type='text' id='twitter_scheduler_settings[oauth_access_token]' name='twitter_scheduler_settings[oauth_access_token]' value='<?php echo esc_attr( $options['oauth_access_token'] ); ?>'>
		<?php

	}

	/**
	 * Holds the twitter oauth access token secret.
	 *
	 * Renders an input box to hold a settings value.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function oauth_access_token_secret_render() {

		$options = get_option( 'twitter_scheduler_settings' );
		?>
		<input type='text' id='twitter_scheduler_settings[oauth_access_token_secret]' name='twitter_scheduler_settings[oauth_access_token_secret]' value='<?php echo esc_attr( $options['oauth_access_token_secret'] ); ?>'>
		<?php

	}

	/**
	 * Holds the twitter consumer key.
	 *
	 * Renders an input box to hold a settings value.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function consumer_key_render() {

		$options = get_option( 'twitter_scheduler_settings' );
		?>
		<input type='text' id='twitter_scheduler_settings[consumer_key]' name='twitter_scheduler_settings[consumer_key]' value='<?php echo esc_attr( $options['consumer_key'] ); ?>'>
		<?php

	}

	/**
	 * Holds the twitter consumer secret.
	 *
	 * Renders an input box to hold a settings value.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function consumer_secret_render() {

		$options = get_option( 'twitter_scheduler_settings' );
		?>
		<input type='text' id='twitter_scheduler_settings[consumer_secret]' name='twitter_scheduler_settings[consumer_secret]' value='<?php echo esc_attr( $options['consumer_secret'] ); ?>'>
		<?php

	}

	/**
	 * Holds the twitter  username - not currently in use.
	 *
	 * Renders an input box to hold a settings value.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function username_render() {

		$options = get_option( 'twitter_scheduler_settings' );
		?>
		<input type='text' id='twitter_scheduler_settings[username]' name='twitter_scheduler_settings[username]' value='<?php echo esc_attr( $options['username'] ); ?>'>
		<?php

	}

}
