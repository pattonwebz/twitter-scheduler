<?php
/**
 * Class SOSC_Helpers_Test
 *
 * @package twitter_scheduler
 */

class SOSC_Helpers_Test extends WP_UnitTestCase {

	function test_twitter_settings() {
		$settings = \PattonWebz\TwitterScheduler\Helpers\get_twitter_settings();
		$this->assertTrue( is_array( $settings ) );
	}
}
