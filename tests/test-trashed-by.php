<?php

class Trashed_By_Test extends WP_UnitTestCase {

	protected static $meta_key_user = 'c2c-trashed-by';
	protected static $meta_key_date = 'c2c-trashed-on';

	function tearDown() {
		parent::tearDown();
		$this->unset_current_user();
	}



	/**
	 * HELPER FUNCTIONS
	 */



	private function create_user( $set_as_current = true ) {
		$user_id = $this->factory->user->create();
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
		add_post_meta( $post_id, self::$meta_key_user, $user_id );
		if ( empty( $date ) ) {
			$date = current_time( 'mysql' );
		}
		add_post_meta( $post_id, self::$meta_key_date, $date );
	}



	/**
	 * FUNCTIONS FOR HOOKING ACTIONS/FILTERS
	 */

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



	/**
	 * TESTS
	 */

	function test_meta_keys_not_created_for_post_not_trashed() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'draft', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		$post = get_post( $post_id );
		wp_update_post( $post );

		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	function test_meta_keys_not_created_for_post_saved_as_pending() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'draft', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		$post = get_post( $post_id );
		$post->post_status = 'pending';
		wp_update_post( $post );

		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	function test_meta_keys_created_for_trashed_post() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		wp_trash_post( $post_id );

		$this->assertEquals( $user_id, c2c_TrashedBy::get_trasher_id( $post_id ) );
		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertNotEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	function test_meta_keys_updated_for_retrashed_post() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_author' => $author_id ) );
		$user1_id  = $this->create_user( false );
		$date      = '2014-03-01 13:02:04';

		$this->set_trashed_by( $post_id, $user1_id, $date );

		$this->assertEmpty(  c2c_TrashedBy::get_trasher_id( $post_id ) );
		$this->assertEmpty(  c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user1_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEquals( $date,     get_post_meta( $post_id, self::$meta_key_date, true ) );

		$user2_id = $this->create_user();

		wp_trash_post( $post_id );

		$this->assertEquals( $user2_id, c2c_TrashedBy::get_trasher_id( $post_id ) );
		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user2_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertNotEmpty(  $date,  get_post_meta( $post_id, self::$meta_key_date, true ) );
		$this->assertNotEquals( $date,  get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	function test_blank_is_returned_if_metas_not_present() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'trash', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		$this->assertEmpty( c2c_TrashedBy::get_trasher_id( $post_id ) );
		$this->assertEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	function test_blank_is_returned_if_not_trash() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'publish', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();

		$this->assertEmpty( c2c_TrashedBy::get_trasher_id( $post_id ) );
		$this->assertEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEmpty( get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	function test_blank_is_returned_if_not_trash_even_if_metas_is_present() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_status' => 'publish', 'post_author' => $author_id ) );
		$user_id   = $this->create_user();
		$date      = '2014-03-01 13:02:04';

		// Set the custom field, as if it had been set on a previous publish
		$this->set_trashed_by( $post_id, $user_id, $date );

		$this->assertEmpty( c2c_TrashedBy::get_trasher_id( $post_id ) );
		$this->assertEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertEquals( $user_id, get_post_meta( $post_id, self::$meta_key_user, true ) );
		$this->assertEquals( $date,    get_post_meta( $post_id, self::$meta_key_date, true ) );
	}

	function test_editing_trashed_post_does_not_change_trasher() {
		$author_id = $this->create_user( false );
		$post_id   = $this->factory->post->create( array( 'post_author' => $author_id ) );
		$user_id1  = $this->create_user();

		wp_trash_post( $post_id );

		$this->assertEquals( $user_id1, c2c_TrashedBy::get_trasher_id( $post_id ) );

		$date      = c2c_TrashedBy::get_trashed_on( $post_id );
		$user_id2  = $this->create_user();
		$post      = get_post( $post_id );
		$post->post_title = $post->post_title . ' changed';
		wp_update_post( $post );

		$this->assertEquals( $user_id1, c2c_TrashedBy::get_trasher_id( $post_id ) );
		$this->assertEquals( $date,     c2c_TrashedBy::get_trashed_on( $post_id ) );
		$this->assertNotEmpty( c2c_TrashedBy::get_trashed_on( $post_id ) );
	}

}
