<?php

namespace PattonWebz\TwitterScheduler\PostType;

class TweetsPostTypeTest extends \WP_UnitTestCase {

	public $post_type;

	function setUp() {
		parent::setUp();

		$this->post_type = new Tweets();
	}

	function test_it_has_a_name() {
		$actual = $this->post_type->get_name();
		$this->assertEquals( TWSC_POST_TYPE, $actual );
	}

	function test_it_doesnt_supports_comments() {
		$actual = $this->post_type->get_editor_support();
		$this->assertNotContains( 'comments', $actual );
	}

	function test_it_is_not_public() {
		$actual = $this->post_type->get_options()['public'];
		$this->assertNotTrue( $actual );
	}

}
