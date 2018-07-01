<?php
/**
 * Class SOSC_Helpers_Test
 *
 * @package twitter_scheduler
 */

class SOSC_Helpers_Test extends WP_UnitTestCase {

	function test_twitter_settings_returns_array() {
		$settings = \PattonWebz\TwitterScheduler\Helpers\get_twitter_settings();
		$this->assertTrue( is_array( $settings ) );
	}

	function test_farthest_schedule_post_args_returns_array() {
		$args = \PattonWebz\TwitterScheduler\Helpers\farthest_scheuled_posting_query_args();
		$this->assertTrue( is_array( $args ) );
	}
}
