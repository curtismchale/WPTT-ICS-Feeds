<?php
/**
 * Class TestPostQueries
 *
 * @package Wptt_Ics_Feeds
 */

/**
 * Sample test case.
 */
class TestFeedLinks extends WP_UnitTestCase {

	public function setUp(){
		parent::setUp();

		// getting the plugin global
		$this->plugin = $GLOBALS['wptt_ics_feeds'];

		// make a fake user
		$this->editor = new WP_User( $this->factory->user->create( array( 'role' => 'editor' ) ) );

	}

	/**
	 * Tests base feed link without author
	 */
	public function test_base_feed_link(){

		$feed_link = $this->plugin->get_subscribe_link();
		$complete_link = site_url() . '/?feed=wptticsfeeds';

		$this->assertEquals( $feed_link, $complete_link, 'The feed links are not equal' );

	}

	/**
	 * Tests feed link with author
	 */
	 public function test_author_feed_link(){

		$feed_link = $this->plugin->get_subscribe_link( array( 'author' => $this->editor->ID ) );
		$complete_link = esc_url( site_url() . '/?feed=wptticsfeeds&wpttauthor='. $this->editor->user_login );

		$this->assertEquals( $feed_link, $complete_link, 'The feed links with author are not equal' );

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
