<?php
/**
 * Class TestIcsFeed
 *
 * @package Wptt_Ics_Feeds
 */

/**
 * Sample test case.
 */
class TestIcsFeed extends WP_UnitTestCase {

	public function setUp(){
		parent::setUp();

		// getting the plugin global
		$this->plugin = $GLOBALS['wptt_ics_feeds'];

		// make a fake user
		$this->editor = new WP_User( $this->factory->user->create( array( 'role' => 'editor' ) ) );

	}

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}

	public function tearDown(){
		parent::tearDown();
		wp_delete_user( $this->editor->ID, true );
	}
}
