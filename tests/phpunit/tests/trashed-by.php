<?php

defined( 'ABSPATH' ) or die();

class Trashed_By_Test extends WP_UnitTestCase {

	protected static $meta_key_user = 'c2c-trashed-by';
	protected static $meta_key_date = 'c2c-trashed-on';

	/**
	 * Test REST Server
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

	public function setUp() {
		parent::setUp();

		c2c_TrashedBy::register_meta();

		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;
		do_action( 'rest_api_init' );
	}

	public function tearDown() {
		parent::tearDown();
		$this->unset_current_user();
	}


	//
	// DATA PROVIDERS
	//


	public static function get_default_hooks() {
		return array(
			array( 'filter', 'manage_posts_columns',       'add_post_column',        10 ),
			array( 'action', 'manage_posts_custom_column', 'handle_column_data',     10 ),
			array( 'action', 'load-edit.php',              'add_admin_css',          10 ),
			array( 'action', 'trashed_post',               'trash_post',             10 ),
			array( 'action', 'untrashed_post',             'untrash_post',           10 ),
			array( 'filter', 'is_protected_meta',          'is_protected_meta',      10 ),
			array( 'action', 'init',                       'register_meta',          10 ),
		);
	}

	public static function get_metas() {
		return array(
			array( self::$meta_key_user ),
			array( self::$meta_key_date ),
		);
	}


	//
	// HELPER FUNCTIONS
	//


	private function create_user( $set_as_current = true, $user_args = array() ) {
		$user_id = $this->factory->user->create( $user_args );
		if ( $set_as_current ) {
			wp_set_current_user( $user_id );
		}
		return $user_id;
	}

	// helper function, unsets current user globally. Taken from post.php test.
	private function unset_current_user() {
		global $current_user, $user_ID;

		$current_user = $user_ID = null;
    }

	private function set_trashed_by( $post_id, $user_id = '', $date = null ) {
		update_post_meta( $post_id, self::$meta_key_user, $user_id );
		if ( empty( $date ) ) {
			$date = current_time( 'mysql' );
		}
		update_post_meta( $post_id, self::$meta_key_date, $date );
	}


	//
	// FUNCTIONS FOR HOOKING ACTIONS/FILTERS
	//


	public function query_for_posts( $text ) {
		$q = new WP_Query( array( 'post_type' => 'post' ) );
		$GLOBALS['custom_query'] = $q;
		return $text;
	}

	public function filter_on_special_meta( $wpquery ) {
		$wpquery->query_vars['meta_query'][] = array(
			'key'     => 'special',
			'value'   => '1',
			'compare' => '='
		);
	}


	//
	// TESTS
	//


	public function test_plugin_version() {
		$this->assertEquals( '1.4', c2c_TrashedBy::version() );
	}

	public function test_class_is_available() {
		$this->assertTrue( class_exists( 'c2c_TrashedBy' ) );
	}

	public function test_instantiating_object_fails() {
		$this->setExpectedException( 'error' );

		new c2c_TrashedBy;
	}

	public function test_plugins_loaded_action_triggers_init() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_TrashedBy', 'init' ) ) );
	}

	/**
	 * @expectedException Error
	 */
	public function test_unable_to_instantiation_object_from_class() {
		new c2c_TrashedBy;
	}

	/**
	 * @expectedException Error
	 */
	public function test_unable_to_unserialize_an_instance_of_the_class() {
		$data = 'O:13:"c2c_TrashedBy":0:{}';

		unserialize( $data );
	}

	/**
	 * @dataProvider get_default_hooks
	 */
	public function test_default_hooks( $hook_type, $hook, $function, $priority ) {
		$callback = array( 'c2c_TrashedBy', $function );
		$prio = $hook_type === 'action' ?
			has_action( $hook, $callback ) :
			has_filter( $hook, $callback );
		$this->assertNotFalse( $prio );
		if ( $priority ) {
			$this->assertEquals( $priority, $prio );
		}
	}

	public function test_meta_keys_not_created_for_post_not_trashed() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'draft', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		$post = get_post( $post_id );
		wp_update_post( $post );

		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	public function test_meta_keys_not_created_for_post_saved_as_pending() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'draft', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		$post = get_post( $post_id );
		$post->post_status = 'pending';
		wp_update_post( $post );

		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	public function test_meta_keys_created_for_trashed_post() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		wp_trash_post( $post_id );

		$this->assertEquals( $user_id, c2c_TrashedBy::get_trashed_by( $post_id ) );
		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertNotEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	public function test_meta_keys_updated_for_retrashed_post() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_author' => $author_id ) );
		$user1_id  = $this->create_user( false );
		$date      = '2014-03-01 13:02:04';

		$this->set_trashed_by( $post_id, $user1_id, $date );

		$this->assertEmpty(  c2c_TrashedBy::get_trashed_by( $post_id ) );
		$this->assertEmpty(  c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user1_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEquals( $date,     get_post_meta( $post_id, self::$meta_key_date, true ) );

		$user2_id = $this->create_user();

		wp_trash_post( $post_id );

		$this->assertEquals( $user2_id, c2c_TrashedBy::get_trashed_by( $post_id ) );
		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user2_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertNotEmpty(  $date,  get_post_meta( $post_id, self::$meta_key_date, true ) );
		$this->assertNotEquals( $date,  get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	public function test_blank_is_returned_if_metas_not_present() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'publish', 'post_author' => $author_id ) );
		wp_trash_post( $post_id );
		$user_id   = $this->create_user();

		$this->assertEmpty( c2c_TrashedBy::get_trashed_by( $post_id ) );
		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertNotEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	public function test_blank_is_returned_if_not_trash() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'publish', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		$this->assertEmpty( c2c_TrashedBy::get_trashed_by( $post_id ) );
		$this->assertEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	public function test_blank_is_returned_if_not_trash_even_if_metas_is_present() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'publish', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();
		$date      = '2014-03-01 13:02:04';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->assertEmpty( c2c_TrashedBy::get_trashed_by( $post_id ) );
		$this->assertEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEquals( $date,    get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	public function test_editing_trashed_post_does_not_change_trasher() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_author' => $author_id ) );
		$user_id1  = $this->create_user();

		wp_trash_post( $post_id );

		$this->assertEquals( $user_id1, c2c_TrashedBy::get_trashed_by( $post_id ) );

		$date      = c2c_TrashedBy::get_trashed_on( $post_id );
		$user_id2  = $this->create_user();
		$post      = get_post( $post_id );
		$post->post_title = $post->post_title . ' changed';
		wp_update_post( $post );

		$this->assertEquals( $user_id1, c2c_TrashedBy::get_trashed_by( $post_id ) );
		$this->assertEquals( $date,     c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
	}

	/*
	 * c2c_TrashedBy::add_admin_css()
	 */

	public function test_admin_head_is_not_hooked_when_not_applicable() {
		c2c_TrashedBy::add_admin_css();
		$this->assertFalse( has_action( 'admin_head', array( 'c2c_TrashedBy', 'admin_css' ) ) );

		$_GET = array();
		c2c_TrashedBy::add_admin_css();
		$this->assertFalse( has_action( 'admin_head', array( 'c2c_TrashedBy', 'admin_css' ) ) );

		$_GET['post_status'] = 'pending';
		c2c_TrashedBy::add_admin_css();
		$this->assertFalse( has_action( 'admin_head', array( 'c2c_TrashedBy', 'admin_css' ) ) );

		$_GET['post_status'] = 'publish';
		c2c_TrashedBy::add_admin_css();
		$this->assertFalse( has_action( 'admin_head', array( 'c2c_TrashedBy', 'admin_css' ) ) );
	}

	public function test_include_column_is_true_for_trash() {
		$_GET['post_status'] = 'trash';
		c2c_TrashedBy::add_admin_css();

		$this->assertEquals( 10, has_action( 'admin_head', array( 'c2c_TrashedBy', 'admin_css' ) ) );
	}

	/*
	 * c2c_TrashedBy::admin_css()
	 */

	public function test_admin_css() {
		$this->expectOutputRegex(
			// Testing the actual styles is not important.
			'~^<style>.fixed .column-trashed_by, .fixed .column-trashed_on { .+ }</style>$~',
			c2c_TrashedBy::admin_css()
		);
	}

	/*
	 * c2c_TrashedBy::add_post_column()
	 */

	public function test_add_post_column_does_not_add_when_not_appropriate() {
		$this->assertEmpty( c2c_TrashedBy::add_post_column( array() ) );
		$_GET['post_status'] = 'draft';
		$this->assertEmpty( c2c_TrashedBy::add_post_column( array() ) );
	}

	public function test_add_post_column_adds_when_appropriate() {
		$_GET['post_status'] = 'trash';
		$expected = array(
			'trashed_by' => 'Trashed By',
			'trashed_on' => 'Trashed On',
		);

		$this->assertEquals( $expected, c2c_TrashedBy::add_post_column( array() ) );
	}

	/*
	 * c2c_TrashedBy::handle_column_data()
	 */

	public function test_handle_column_data_output_nothing_for_unrelated_columns() {
		$_GET['post_status'] = 'trash';
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( array( 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->expectOutputRegex( '/^$/', c2c_TrashedBy::handle_column_data( 'date', $post_id ) );
	}

	public function test_handle_column_data_output_nothing_for_column_when_post_not_trashed() {
		$_GET['post_status'] = 'publish';
		$post_id  = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		$user_id  = $this->create_user( array( 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->expectOutputRegex( '/^$/', c2c_TrashedBy::handle_column_data( 'trashed_by', $post_id ) );
	}

	public function test_handle_column_data_outputs_trashed_by_value_for_its_column_for_non_current_user() {
		$_GET['post_status'] = 'trash';
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$user_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( c2c_TrashedBy::get_user_url( $user_id ) ),
			'Matt Smith'
		);

		$this->expectOutputRegex( '~^' . preg_quote( $user_link ) . '$~', c2c_TrashedBy::handle_column_data( 'trashed_by', $post_id ) );
	}

	public function test_handle_column_data_outputs_trashed_by_value_for_its_column_for_current_user() {
		$_GET['post_status'] = 'trash';
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( true, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$user_link = '<span>you</span>';

		$this->expectOutputRegex( '~^' . preg_quote( $user_link ) . '$~', c2c_TrashedBy::handle_column_data( 'trashed_by', $post_id ) );
	}

	public function test_handle_column_data_outputs_trashed_on_value_for_its_column() {
		$_GET['post_status'] = 'trash';
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$date_link = sprintf(
			'<abbr title="%s">%s</abbr>',
			'2020/03/01 12:13:14 PM',
			'2020/03/01'
		);

		$this->expectOutputRegex( '~^' . preg_quote( $date_link ) . '$~', c2c_TrashedBy::handle_column_data( 'trashed_on', $post_id ) );
	}

	/*
	 * c2c_TrashedBy::get_user_url()
	 */

	public function test_get_user_url() {
		$user_id = $this->create_user( false );

		$this->assertEquals( self_admin_url( 'user-edit.php?user_id=' . $user_id ), c2c_TrashedBy::get_user_url( $user_id ) );
		$this->assertEquals( self_admin_url( 'user-edit.php?user_id=' . $user_id ), c2c_TrashedBy::get_user_url( "{$user_id}" ) );
	}

	// @todo: This test should actually fail as-is seeing as how the user IDs aren't valid.
	public function test_get_user_url_with_invalid_user_id() {
		$this->assertEquals( self_admin_url( 'user-edit.php?user_id=20' ), c2c_TrashedBy::get_user_url( 20 ) );
		$this->assertEquals( self_admin_url( 'user-edit.php?user_id=30' ), c2c_TrashedBy::get_user_url( '30' ) );
	}

	public function test_get_user_url_with_invalid_arguments() {
		$this->assertEmpty( c2c_TrashedBy::get_user_url( 0 ) );
		$this->assertEmpty( c2c_TrashedBy::get_user_url( '' ) );
		$this->assertEmpty( c2c_TrashedBy::get_user_url( 'hello' ) );
	}

	/*
	 * c2c_TrashedBy::get_trashed_by()
	 */

	public function test_get_trashed_by_for_trashed_post_without_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );

		$this->assertEmpty( c2c_TrashedBy::get_trashed_by( $post_id ) );
	}

	public function test_get_trashed_by_for_trashed_post_with_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->assertEquals( $user_id, c2c_TrashedBy::get_trashed_by( $post_id ) );
	}

	public function test_get_trashed_by_for_trashed_post_via_object_with_meta() {
		$post     = $this->factory->post->create_and_get( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post->ID, $user_id, $date );

		$this->assertEquals( $user_id, c2c_TrashedBy::get_trashed_by( $post ) );
	}

	public function test_get_trashed_by_for_trashed_post_via_implied_global_post_object_with_meta() {
		global $post;
		$post     = $this->factory->post->create_and_get( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post->ID, $user_id, $date );

		$this->assertEquals( $user_id, c2c_TrashedBy::get_trashed_by() );
		unset( $post );
	}

	public function test_get_trashed_by_for_draft_post_without_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'publish' ) );

		$this->assertEmpty( c2c_TrashedBy::get_trashed_by( $post_id ) );
	}

	public function test_get_trashed_by_for_draft_post_with_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->assertEmpty( c2c_TrashedBy::get_trashed_by( $post_id ) );
	}

	/*
	 * c2c_TrashedBy::get_trasher_id()
	 */

	 /**
	 * Test deprecated function, at least while it's still present.
	 *
	 * @expectedDeprecated get_trasher_id
	 */
	public function test_deprecated_get_trasher_id() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->assertEquals( $user_id, c2c_TrashedBy::get_trasher_id( $post_id ) );
	}

	/*
	 * c2c_TrashedBy::get_trashed_on()
	 */

	public function test_get_trashed_on_for_trashed_post_without_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		wp_trash_post( $post_id );
		$date = current_time( 'mysql' );

		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		// Note: The expected date may actually differ from the actual date by a
		// second, so this assertion may actual fail on occasion.
		$this->assertEquals( $date, c2c_TrashedBy::get_trashed_on( $post_id ) );
	}

	public function test_get_trashed_on_for_trashed_post_with_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->assertEquals( $date, c2c_TrashedBy::get_trashed_on( $post_id ) );
	}

	public function test_get_trashed_on_for_trashed_post_via_object_with_meta() {
		$post     = $this->factory->post->create_and_get( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post->ID, $user_id, $date );

		$this->assertEquals( $date, c2c_TrashedBy::get_trashed_on( $post ) );
	}

	public function test_get_trashed_on_for_trashed_post_via_implied_global_object_with_meta() {
		global $post;
		$post     = $this->factory->post->create_and_get( array( 'post_status' => 'trash' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post->ID, $user_id, $date );

		$this->assertEquals( $date, c2c_TrashedBy::get_trashed_on() );
		unset( $post );
	}

	public function test_get_trashed_on_for_draft_post_without_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'publish' ) );

		$this->assertEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
	}

	public function test_get_trashed_on_for_draft_post_with_meta() {
		$post_id  = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		$user_id  = $this->create_user( false, array( 'display_name' => 'Matt Smith', 'role' => 'author' ) );
		$date     = '2020-03-01 12:13:14';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->assertEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
	}

	/*
	 * c2c_TrashedBy::trash_post()
	 */

	public function test_trash_post_not_invoked_during_publish() {
		$post = $this->factory->post->create_and_get( array( 'post_status' => 'draft' ) );

		wp_publish_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post->ID, self::$meta_key_user ) );
		$this->assertFalse( metadata_exists( 'post', $post->ID, self::$meta_key_date ) );
	}

	public function test_trash_post_adds_meta() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		$user_id = $this->create_user();

		c2c_TrashedBy::trash_post( $post_id );
		$date = current_time( 'mysql' );

		$this->assertTrue( metadata_exists( 'post', $post_id, self::$meta_key_user ) );
		$this->assertEquals( $user_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertTrue( metadata_exists( 'post', $post_id, self::$meta_key_date ) );
		// Note: The expected date may actually differ from the actual date by a
		// second, so this assertion may actual fail on occasion.
		$this->assertEquals( $date, get_post_meta( $post_id, self::$meta_key_date, true ) );

		return $post_id;
	}

	public function test_trash_post_adds_meta_during_wp_trash_post() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		$user_id = $this->create_user();

		wp_trash_post( $post_id );
		$date = current_time( 'mysql' );

		$this->assertTrue( metadata_exists( 'post', $post_id, self::$meta_key_user ) );
		$this->assertEquals( $user_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertTrue( metadata_exists( 'post', $post_id, self::$meta_key_date ) );
		// Note: The expected date may actually differ from the actual date by a
		// second, so this assertion may actual fail on occasion.
		$this->assertEquals( $date, get_post_meta( $post_id, self::$meta_key_date, true ) );

		return $post_id;
	}

	/*
	 * c2c_TrashedBy::untrash_post()
	 */

	public function test_untrash_post_deletes_meta() {
		$post_id = $this->test_trash_post_adds_meta();

		c2c_TrashedBy::untrash_post( $post_id );

		$this->assertFalse( metadata_exists( 'post', $post_id, self::$meta_key_user ) );
		$this->assertFalse( metadata_exists( 'post', $post_id, self::$meta_key_date ) );
	}

	public function test_untrash_post_deletes_meta_during_wp_untrash_post() {
		$post_id = $this->test_trash_post_adds_meta_during_wp_trash_post();

		wp_untrash_post( $post_id );

		$this->assertFalse( metadata_exists( 'post', $post_id, self::$meta_key_user ) );
		$this->assertFalse( metadata_exists( 'post', $post_id, self::$meta_key_date ) );
	}

	/*
	 * c2c_TrashedBy::is_protected_meta()
	 */

	public function test_is_protected_meta_does_not_affect_unrelated_meta() {
		$this->assertFalse( c2c_TrashedBy::is_protected_meta( false, 'meta1' ) );
		$this->assertTrue( c2c_TrashedBy::is_protected_meta( true, 'meta2' ) );
	}

	/**
	 * @dataProvider get_metas
	 */
	public function test_is_protected_meta_protects_plugin_meta() {
		$this->assertTrue( c2c_TrashedBy::is_protected_meta( true, self::$meta_key_user ) );
		$this->assertTrue( c2c_TrashedBy::is_protected_meta( true, self::$meta_key_date ) );
		$this->assertTrue( c2c_TrashedBy::is_protected_meta( false, self::$meta_key_user ) );
		$this->assertTrue( c2c_TrashedBy::is_protected_meta( false, self::$meta_key_date ) );
	}

	/*
	 * REST API
	 */


	public function test_meta_are_registered() {
		$this->assertTrue( registered_meta_key_exists( 'post', self::$meta_key_user, 'post' ) );
		$this->assertTrue( registered_meta_key_exists( 'post', self::$meta_key_date, 'post' ) );

		$this->assertFalse( registered_meta_key_exists( 'secret', self::$meta_key_user, 'post' ) );
		$this->assertFalse( registered_meta_key_exists( 'secret', self::$meta_key_date, 'post' ) );
	}

	/**
	 * @dataProvider get_metas
	 */
	public function test_rest_post_request_does_not_include_meta( $meta_key ) {
		$author_id = $this->create_user( false );
		$trasher_id = $this->create_user( false );
		$post_id = $this->factory->post->create( array( 'post_status' => 'publish', 'post_author' => $author_id, 'post_date' => '2020-02-05 19:45:06' ) );

		$trashed_on = '2020-03-02 14:12:11';

		add_post_meta( $post_id, self::$meta_key_user, $trasher_id );
		add_post_meta( $post_id, self::$meta_key_date, $trashed_on );

		$request = new WP_REST_Request( 'GET', sprintf( '/wp/v2/posts/%d', $post_id ) );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'meta', $data );

		$meta = (array) $data['meta'];

		$this->assertArrayHasKey( $meta_key, $meta );

		$this->assertEquals( ( 'c2c-trashed-by' === $meta_key ) ? $trasher_id : $trashed_on, $meta[ $meta_key ] );
	}
}
