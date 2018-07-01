<?php
/**
 * Class to create the advanced settings page for the plugin.
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
 * Creates a settings input page for the plugin.
 *
 * Used to hold the advanced settings or options the plugin uses.
 *
 * @since  0.1.0
 */
class SettingsAdvanced extends AbstractAdminSubpage {

	/**
	 * The ID used to identify this page and it's assosiated settings.
	 *
	 * @since  0.1.0
	 *
	 * @var string|null
	 */
	public $id = 'twitter_scheduler_advanced';

	/**
	 * When constructing this object set the title.
	 *
	 * @since  0.1.0
	 */
	public function __construct() {
		$this->title = esc_html__( 'Advanced Settings', 'twitter-scheduler' );
	}

	/**
	 * Register our settings and add our sections and setting fields.
	 *
	 * @since  0.1.0
	 * @method settings_init
	 */
	public function settings_init() {
		register_setting( $this->id, 'twitter_scheduler_advanced_settings' );

		add_settings_section(
			'twitter_scheduler_advanced_section',
			__( 'Advanced Scheduling Options and Settings.', 'twitter-scheduler' ),
			[ $this, 'section_callback' ],
			'twitter_scheduler_advanced'
		);

		add_settings_field(
			'prefered_time_min',
			__( 'Prefered minimum time', 'twitter-scheduler' ),
			[ $this, 'prefered_time_min_render' ],
			'twitter_scheduler_advanced',
			'twitter_scheduler_advanced_section',
			array(
				'label_for' => 'twitter_scheduler_advanced_settings[prefered_time_min]',
			)
		);

		add_settings_field(
			'prefered_time_max',
			__( 'Prefered maxmum time', 'twitter-scheduler' ),
			[ $this, 'prefered_time_max_render' ],
			'twitter_scheduler_advanced',
			'twitter_scheduler_advanced_section',
			array(
				'label_for' => 'twitter_scheduler_advanced_settings[prefered_time_max]',
			)
		);

		add_settings_field(
			'prefered_weekdays',
			__( 'Days that have higher priority', 'twitter-scheduler' ),
			[ $this, 'prefered_weekdays_render' ],
			'twitter_scheduler_advanced',
			'twitter_scheduler_advanced_section',
			array(
				'label_for' => 'twitter_scheduler_advanced_settings[prefered_weekdays]',
			)
		);

		add_settings_field(
			'times_per_weekday',
			__( 'How frequent to try setup tweets on weekdays', 'twitter-scheduler' ),
			[ $this, 'times_per_weekday_render' ],
			'twitter_scheduler_advanced',
			'twitter_scheduler_advanced_section',
			array(
				'label_for' => 'twitter_scheduler_advanced_settings[times_per_weekday]',
			)
		);

		add_settings_field(
			'times_per_weekend',
			__( 'How frequent to try setup tweets on weekends', 'twitter-scheduler' ),
			[ $this, 'times_per_weekend_render' ],
			'twitter_scheduler_advanced',
			'twitter_scheduler_advanced_section',
			array(
				'label_for' => 'twitter_scheduler_advanced_settings[times_per_weekend]',
			)
		);
	}

	/**
	 * Echos a title above certain sections.
	 *
	 * @since  0.1.0
	 * @method section_callback
	 */
	public function section_callback() {
		?>
		<p><?php esc_html_e( 'These options offer some of the more tweakable scheduling arrangements.', 'twitter-scheduler' ); ?></p>
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
	public function prefered_time_min_render() {

		$options = get_option( 'twitter_scheduler_advanced_settings' );
		?>
		<input type='time' id='twitter_scheduler_advanced_settings[prefered_time_min]' name='twitter_scheduler_advanced_settings[prefered_time_min]' value='<?php echo esc_attr( $options['prefered_time_min'] ); ?>'>
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
	public function prefered_time_max_render() {

		$options = get_option( 'twitter_scheduler_advanced_settings' );
		?>
		<input type='time' id='twitter_scheduler_advanced_settings[prefered_time_max]' name='twitter_scheduler_advanced_settings[prefered_time_max]' value='<?php echo esc_attr( $options['prefered_time_max'] ); ?>'>
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
	public function prefered_weekdays_render() {

		$options = get_option( 'twitter_scheduler_advanced_settings' );
		?>
		<select id="twitter_scheduler_advanced_settings[prefered_weekdays]" name='twitter_scheduler_advanced_settings[prefered_weekdays]' value='<?php echo esc_attr( $options['prefered_weekdays'] ); ?>' multiple>
			<option value="monday">Monday</option>
			<option value="tuesday">Tuesday</option>
			<option value="wednesday">Wednesday</option>
			<option value="thursday">Thursday</option>
			<option value="friday">Friday</option>
			<option value="saturday">Saturday</option>
			<option value="sunday">Sunday</option>
		</select>
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
	public function times_per_weekday_render() {

		$options = get_option( 'twitter_scheduler_advanced_settings' );
		?>
		<input type='number' id='twitter_scheduler_advanced_settings[times_per_weekday]' name='twitter_scheduler_advanced_settings[times_per_weekday]' value='<?php echo esc_attr( $options['times_per_weekday'] ); ?>' min="0" max="5">
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
	public function times_per_weekend_render() {

		$options = get_option( 'twitter_scheduler_advanced_settings' );
		?>
		<input type='number' id='twitter_scheduler_advanced_settings[times_per_weekend]' name='twitter_scheduler_advanced_settings[times_per_weekend]' value='<?php echo esc_attr( $options['times_per_weekend'] ); ?>' min="0" max="3">
		<?php

	}

}
