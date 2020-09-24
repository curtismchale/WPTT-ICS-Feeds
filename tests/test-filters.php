<?php
/**
 * Class TestFilters
 *
 * @package Wptt_Ics_Feeds
 */

/**
 * Sample test case.
 */
class TestFilters extends WP_UnitTestCase {

	public function setUp(){
		parent::setUp();

		// getting the plugin global
		$this->plugin = $GLOBALS['wptt_ics_feeds'];

	}

	/**
	 * Tests that the post where time can be changed with a filter
	 */
	public function test_posts_where_filter(){

		add_filter(	'wptt_ics_feeds_how_old', array( $this, 'new_where' ), 10, 2 );

		 $output = $this->plugin->two_months( 'nothing' );

		 $date = date('Y-m-d', strtotime( $this->new_where() ) );

		 $this->assertStringContainsString( $date, $output, 'The date filter did not work' );

		//echo $output;

	}

	public function new_where(){
		return '-1 week';
	}

	public function tearDown(){
		parent::tearDown();
	}
}
