<?php
/*
Plugin Name: WPTT ICS Feeds
Plugin URI: http://wpthemetutorial.com/plugins/wptt-ics-feeds/
Description: Adds an ICS compatible feed for future posts
Version: 1.3
Author: WP Theme Tutorial, Curtis McHale
Author URI: http://wpthemetutorial.com
License: GPLv2 or later
*/

/**
 * @todo make sure that we can let authors define a post status
 * @todo do some obfuscation so that it's harder to just get the feed if you know the author name
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class WPTT_ICS_Feeds{

	function __construct(){

		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

		$this->constants();
		$this->includes();

		add_action( 'init', array( $this, 'add_new_feed' ) );
		add_filter( 'query_vars', array( $this, 'add_user_query_var' ) );

		add_action( 'show_user_profile', array( $this, 'show_links' ) );
		add_action( 'edit_user_profile', array( $this, 'show_links' ) );

	} // construct

	/**
	 * Shows any links we may have to subscribe to our posts feeds
	 *
	 * @since 1.1
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access private
	 */
	public function show_links( $user ){
	?>

		<h3>ICS Feed Links</h3>

		<table class="form-table">

			<tr>
				<th><label for="company">Links</label></th>

				<td>
					<p><a href="<?php echo $this->get_subscribe_link(); ?>">All Posts</a></p>
					<p><a href="<?php echo $this->get_subscribe_link( array( 'author' => $user->ID ) ); ?>"><?php echo $user->display_name; ?> Posts Only</a></p>
					<span class="description">Copy the link (right click and copy link address) and subscribe to the link in your calendar program.</span>
				</td>
			</tr>

		</table>

	<?php
	} // show_links

	/**
	 * Gets us any links we have and allows us to change things around based on arguements
	 *
	 * @since 1.1
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access private
	 *
	 * @param array     $args    optional       Any arguements we have
	 *
	 * @return string   The built out link depending on $args
	 *
	 * @uses wp_get_current_user()              Gets the current user object
	 * @uses site_url()                         Gets the site url for us
	 * @uses esc_url()                          We like to keep things safe with safe URL's
	 */
	private function get_subscribe_link( $args = array() ){

		$link = site_url() . '/?feed=wptticsfeeds';

		if ( isset( $args ) && is_array( $args ) ){

			// adding author feed links
			if ( isset( $args['author'] ) ){
				$user = get_userdata( (int) $args['author'] );
				$link = $link . '&wpttauthor=' . $user->user_login;
			}

		} // if

		return esc_url( $link );

	} // get_subscribe_link

	/**
	 * Adds our new feed endpoint so users can subscribe to a calendar
	 *
	 * @since 1.0
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access public
	 *
	 * @uses add_feed()     Adds a feed endpoint at location in first param
	 */
	public function add_new_feed(){
		add_feed( 'wptticsfeeds', array( $this, 'generate_feed' ) );
	} // add_new_feed

	/**
	 * Lets WP know we have a new query arguement
	 *
	 * @since 1.0
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access public
	 *
	 * @param   array   $qv     required    The existing array of query args
	 *
	 * @return  array   $qv     Our modified query args
	 */
	public function add_user_query_var( $qv ){
		$qv[] = 'wpttauthor';
		return $qv;
	} // add_user_query_var

	/**
	 * Actually queries and builds our calendar items
	 *
	 * @since 1.0
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access public
	 *
	 * @uses EasyPeasyICS()                 Does our heavy lifting for ICS feeds
	 * @uses EasyPeasyICS->add_event()      Adds events to our ICS feeds
	 * @uses wp_reset_post_data()           Resets the post data so we don't stomp all over WP_Query globals and stuff
	 * @uses EasyPeasyICS->render()         Actually renders the calendar feed we want
	 * @uses $wp_query                      Global WP_Query object so we can capture userid in the feed
	 * @uses get_the_ID()                   Gets us the post_id in the loop
	 * @uses the_time()                     Returns the time given time format
	 * @uses get_the_title()                Returns the post title given post_id
	 * @uses get_the_excerpt()              Gets post excerpt
	 * @uses get_permalink()                Gets the post permalink
	 * @uses $this->filter_by_author()      Filters the query and adds the author name so we only get posts by that author
	 */
	public function generate_feed(){

		$ics = new EasyPeasyICS();

		$args = array(
			'post_status'     => array( 'publish', 'future' ),
			'posts_per_page'  => -1
		);

		// use the author param if it's set
		if ( isset( $_GET['wpttauthor'] ) ){
			$args = $this->filter_by_author( $args, $_GET['wpttauthor'] );
		}

		date_default_timezone_set( get_option('timezone_string') );

		add_filter( 'posts_where', array( $this, 'two_months' ) );

		$feed = new WP_Query( $args );

		remove_filter( 'posts_where', array( $this, 'two_months' ) );

		if ( $feed->have_posts() ) : while ( $feed->have_posts() ) : $feed->the_post();

			$starttime      = get_the_time( 'U' );
			$endtime        = strtotime( '+10 minutes', $starttime );
			$summary        = get_the_title( get_the_ID() );
			$description    = get_the_excerpt();
			$url            = get_permalink( get_the_ID() );

			$ics->addEvent( $starttime, $endtime, $summary, $description, $url );

		endwhile; else:

		endif;

		wp_reset_postdata();

		$ics->render();

	} // generate_feed

	/**
	 * Adds a where clause to our posts so that we get 6 weeks back in time.
	 *
	 * @since 1.2
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access public
	 */
	public function two_months( $where ){

		$how_old = apply_filters( 'wptt_ics_feeds_how_old', '-6 weeks' );

		$where .= " AND post_date > '" . date('Y-m-d', strtotime( $how_old )) . "'";
		return $where;

	} // two_months

	/**
	 * Takes our author query arg and adds it to the exising Query.
	 *
	 * @since 1.1
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access private
	 *
	 * @param array     $args     required     The existing query args
	 * @param string    $nicename requride     The author nicename so we can use it to modify the query
	 *
	 * @return array    $updated_args          The updated WP_Query args now that we have the author parameter
	 */
	private function filter_by_author( $args, $nicename ){

		$author_args = array( 'author_name' => esc_attr( $nicename ) );

		$updated_args = array_merge( $author_args, $args );

		return $updated_args;

	} // filter_by_author

	/**
	 * Includes any files we need for our plugin
	 *
	 * @since 1.0
	 * @author  WP Theme Tutorial, Curtis McHale
	 * @access private
	 */
	private function includes(){

		include( WPTT_ICS_FEED_FOLDER . '/EasyPeasyICS.php' );

	} // includes

	/**
	 * Defines any constants we need for the site
	 *
	 * @since 1.0
	 * @author WP Theme Tutorial, Curtis McHale
	 * @access public
	 */
	public function constants(){
		define( 'WPTT_ICS_FEED_FOLDER', dirname( __FILE__ ) );
	} // constants

	/**
	 * Fired when plugin is activated
	 *
	 * @param   bool    $network_wide   TRUE if WPMU 'super admin' uses Network Activate option
	 */
	public function activate( $network_wide ){

		// need to have something that requires a rewrite update
		$this->add_new_feed();

		// makes sure our rewrite rules are set
		flush_rewrite_rules();

	} // activate

	/**
	 * Fired when plugin is deactivated
	 *
	 * @param   bool    $network_wide   TRUE if WPMU 'super admin' uses Network Activate option
	 */
	public function deactivate( $network_wide ){

		// makes sure our rewrite rules are set
		flush_rewrite_rules();

	} // deactivate

	/**
	 * Fired when plugin is uninstalled
	 *
	 * @param   bool    $network_wide   TRUE if WPMU 'super admin' uses Network Activate option
	 */
	public function uninstall( $network_wide ){

	} // uninstall

} // WPTT_ICS_Feed

$GLOBALS['wptt_ics_feeds'] = new WPTT_ICS_Feeds();