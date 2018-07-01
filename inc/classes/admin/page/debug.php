<?php
/**
 * Class to create the debug info page in the settings section of the plugin.
 *
 * @package   Twitter Scheduler
 * @since     0.1.0
 * @author    William Patton <will@pattonwebz.com>
 * @copyright Copyright (c) 2018, William Patton
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace PattonWebz\TwitterScheduler\Admin\Page;

use PattonWebz\TwitterScheduler\Helpers;
use PattonWebz\TwitterScheduler\Admin\AbstractAdminSubpage;

/**
 * Creates a debug output subpage for the plugin.
 *
 * Shows the settings that are retrieved from DB along with the list of upcoming
 * posts that should be scheduled.
 *
 * @since  0.1.0
 */
class Debug extends AbstractAdminSubpage {

	/**
	 * The ID used to identify this page and it's assosiated settings.
	 *
	 * @since  0.1.0
	 *
	 * @var string|null
	 */
	public $id = 'twitter_scheduler_debug';

	/**
	 * When constructing this object set the title.
	 *
	 * @since  0.1.0
	 */
	public function __construct() {
		$this->title = esc_html__( 'Debug', 'twitter-scheduler' );
	}

	/**
	 * Used to register the settings for this page id.
	 *
	 * Because the debug page is only output it doesn't have any settings,
	 * return null to bypass the usual init step.
	 *
	 * @since  0.1.0
	 *
	 * @method settings_init
	 * @return null
	 */
	public function settings_init() {
		// do nothing here.
		return null;
	}

	/**
	 * Renders the debug page. Shows the currently saved options as well as a
	 * list of upcoming/recent scheduled posts.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		?>
		<h1><?php echo esc_html( $this->title ); ?></h1>
		<?php
		// output the tabs.
		$this->options_tabs( $this->id );
		$args = Helpers\farthest_scheuled_posting_query_args(
			array(
				'posts_per_page' => 10,
			)
		);
		// get any posts scheduled to go out today.
		$sosc_query = new \WP_Query( $args );

		// The loop.
		$posts = array();
		if ( $sosc_query->have_posts() ) {
			// set array to hold the posts.
			$posts = array();
			$i     = 0;
			while ( $sosc_query->have_posts() ) {
				$sosc_query->the_post();
				$posts[ $i ] = array(
					'id'          => get_the_id(),
					'content'     => get_the_content(),
					'readytosend' => get_post_meta( get_the_id(), 'sosc_readytosend', true ),
					'alreadysent' => get_post_meta( get_the_id(), 'sosc_already_sent', true ),
					'datetosend'  => get_post_meta( get_the_id(), 'sosc-datepicker', true ),
					'timetosend'  => get_post_meta( get_the_id(), 'sosc-timepicker', true ),
				);
				$i++;
			}
		}
		?>
<div class="sosc-admin-col sosc-admin-left">
	<pre>
<?php
// I want to use print_r() here. This is output for a debug page.
// phpcs:disable
print_r( Helpers\get_twitter_settings() );
print_r( get_option( 'sosc_config_short_url_length' ) );
print_r( $posts );
// phpcs:enable
?>
	</pre>
</div>
		<?php
	}

}
