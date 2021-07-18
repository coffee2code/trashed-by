<?php
/**
 * Plugin Name: Trashed By
 * Version:     1.4
 * Plugin URI:  https://coffee2code.com/wp-plugins/trashed-by/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: trashed-by
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Records which user trashed a post and when they trashed it. Displays that info as columns in admin trashed posts listings.
 *
 * Compatible with WordPress 4.9 through 5.7+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/trashed-by/
 *
 * @package Trashed_By
 * @author  Scott Reilly
 * @version 1.4
 */

/*
	Copyright (c) 2014-2021 by Scott Reilly (aka coffee2code)

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

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_TrashedBy' ) ) :

class c2c_TrashedBy {

	/**
	 * Name for meta key used to store id of trashing user.
	 *
	 * @access private
	 * @var string
	 */
	private static $meta_key_user = 'c2c-trashed-by';

	/**
	 * Name for meta key used to store date post was trashed.
	 *
	 * @access private
	 * @var string
	 */
	private static $meta_key_date = 'c2c-trashed-on';

	/**
	 * Field name for the post listing column for the trashed by user.
	 *
	 * @access private
	 * @var string
	 */
	private static $field_user    = 'trashed_by';

	/**
	 * Field name for the post listing column for the trashbed on date.
	 *
	 * @access private
	 * @var string
	 */
	private static $field_date    = 'trashed_on';

	/**
	 * Prevents instantiation.
	 *
	 * @since 1.3
	 */
	private function __construct() {}

	/**
	 * Prevents unserializing an instance.
	 *
	 * @since 1.3
	 * @since 1.4 Changed method visibility from private to public and throw exception if invoked.
	 */
	public function __wakeup() {
		/* translators: %s: Name of plugin class. */
		throw new Error( sprintf( __( '%s cannot be unserialized.', 'trashed-by' ), __CLASS__ ) );
	}

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.0
	 */
	public static function version() {
		return '1.4';
	}

	/**
	 * Hooks actions and filters.
	 *
	 * @since 1.0
	 */
	public static function init() {
		// Load textdomain
		load_plugin_textdomain( 'trashed-by' );

		// Register hooks
		add_filter( 'manage_posts_columns',        array( __CLASS__, 'add_post_column' )               );
		add_action( 'manage_posts_custom_column',  array( __CLASS__, 'handle_column_data' ),     10, 2 );

		add_action( 'load-edit.php',               array( __CLASS__, 'add_admin_css' )                 );

		add_action( 'trashed_post',                array( __CLASS__, 'trash_post' ) );
		add_action( 'untrashed_post',              array( __CLASS__, 'untrash_post' ) );

		add_action( 'init',                        array( __CLASS__, 'register_meta' )                 );
		add_filter( 'is_protected_meta',           array( __CLASS__, 'is_protected_meta' ),      10, 2 );
	}

	/**
	 * Registers the post meta fields.
	 *
	 * @since 1.1
	 */
	public static function register_meta() {
		$default = array(
			'single'            => true,
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
			'show_in_rest'      => true,
		);

		$user_config = wp_parse_args(
			array(
				'type'              => 'integer',
				'description'       => __( 'The user who trashed the post', 'trashed-by' ),
				'sanitize_callback' => 'absint',
			),
			$default
		);

		$date_config = wp_parse_args(
			array(
				'type'              => 'string',
				'description'       => __( 'The date the post was trashed', 'trashed-by' ),
				'sanitize_callback' => function ( $value ) {
					return $value;
				},
			),
			$default
		);

		if ( function_exists( 'register_post_meta' ) ) {
			register_post_meta( 'post', self::$meta_key_user, $user_config );
			register_post_meta( 'post', self::$meta_key_date, $date_config );
		}
		// Pre WP 4.9.8 support
		else {
			register_meta( 'post', self::$meta_key_user, $user_config );
			register_meta( 'post', self::$meta_key_date, $date_config );
		}
	}

	/**
	 * Hides the meta key from the custom field dropdown.
	 *
	 * @since 1.3
	 *
	 * @param  bool   $protected Is the meta key protected?
	 * @param  string $meta_key  The meta key.
	 * @return bool True if meta key is protected, else false.
	 */
	public static function is_protected_meta( $protected, $meta_key ) {
		return in_array( $meta_key, array( self::$meta_key_date, self::$meta_key_user ) ) ? true : $protected;
	}

	/**
	 * Determines if the Trashed By column should be shown.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	private static function include_column() {
		return ( isset( $_GET['post_status'] ) && 'trash' === $_GET['post_status'] );
	}

	/**
	 * Adds hook to outputs CSS for the display of the Trashed By column if
	 * on the appropriate admin page.
	 *
	 * @since 1.0
	 */
	public static function add_admin_css() {
		if ( ! self::include_column() ) {
			return;
		}

		add_action( 'admin_head', array( __CLASS__, 'admin_css' ) );
	}

	/**
	 * Outputs CSS for the display of the Trashed By column.
	 *
	 * @since 1.0
	 */
	public static function admin_css() {
		echo '<style>.fixed .column-' . self::$field_user . ', .fixed .column-' . self::$field_date . " { width: 10%; }</style>\n";
	}

	/**
	 * Returns the URL for the user.
	 *
	 * @since 1.1
	 *
	 * @param  int $user_id The user ID.
	 * @return string
	 */
	public static function get_user_url( $user_id ) {
		$url = '';

		if ( (int) $user_id ) {
			$url = self_admin_url( 'user-edit.php?user_id=' . (int) $user_id );
		}

		return $url;
	}

	/**
	 * Adds a column to show who trashed the post/page.
	 *
	 * @since 1.0
	 *
	 * @param  array $posts_columns Array of post column titles.
	 * @return array The $posts_columns array with the 'trashed by' column's title added.
	 */
	public static function add_post_column( $posts_columns ) {
		if ( self::include_column() ) {
			$posts_columns[ self::$field_user ] = __( 'Trashed By', 'trashed-by' );
			$posts_columns[ self::$field_date ] = __( 'Trashed On', 'trashed-by' );
		}

		return $posts_columns;
	}

	/**
	 * Outputs the user who trashed the post for each post listed in the post
	 * listing table in the admin.
	 *
	 * @since 1.0
	 *
	 * @param string $column_name The name of the column.
	 * @param int    $post_id     The id of the post being displayed.
	 */
	public static function handle_column_data( $column_name, $post_id ) {
		if ( ! self::include_column() ) {
			return;
		}

		// Display the username of the user who trashed the post.
		if ( self::$field_user === $column_name ) {
			$trasher_id = self::get_trashed_by( $post_id );
			if ( $trasher_id ) {
				if ( get_current_user_id() === $trasher_id ) {
					$user_link = '<span>' . __( 'you', 'trashed-by' ) . '</span>';
				} else {
					$trasher = get_userdata( $trasher_id );
					$user_link = sprintf(
						'<a href="%s">%s</a>',
						esc_url( self::get_user_url( $trasher_id ) ),
						sanitize_text_field( $trasher->display_name )
					);
				}
				echo $user_link;
			}

		// Display the date for when the post was trashed.
		} elseif ( self::$field_date === $column_name ) {
			$trashed_date = self::get_trashed_on( $post_id );
			if ( $trashed_date ) {
				$post      = get_post( $post_id );
				$t_time    = mysql2date( __( 'Y/m/d g:i:s A', 'trashed-by' ), $trashed_date, false );
				$time_from = mysql2date( 'U', $trashed_date, false );
				$time_to   = current_time( 'timestamp', false );
				$time_diff = $time_to - $time_from;

				if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
					$h_time = sprintf( __( '%s ago', 'trashed-by' ), human_time_diff( $time_from, $time_to ) );
				} else {
					$h_time = mysql2date( __( 'Y/m/d', 'trashed-by' ), $trashed_date );
				}

				/** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php. */
				echo '<abbr title="' . esc_attr( $t_time ) . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'trashed_on', 'list' ) . '</abbr>';
			}
		}
	}

	/**
	 * Records the date a post was trashed and the user who trashed the post.
	 *
	 * @since 1.4
	 *
	 * @param int $post_id Post ID.
	 */
	public static function trash_post( $post_id ) {
		// Can only save trashing user ID if one can be obtained
		if ( $current_user_id = get_current_user_id() ) {
			update_post_meta( $post_id, self::$meta_key_user, $current_user_id );
		}
		update_post_meta( $post_id, self::$meta_key_date, current_time( 'mysql' ) );
	}

	/**
	 * Clears the date a post was trashed and the user who trashed the post when
	 * a post is untrashed.
	 *
	 * @since 1.4
	 *
	 * @param int $post_id Post ID.
	 */
	public static function untrash_post( $post_id ) {
		delete_post_meta( $post_id, self::$meta_key_user );
		delete_post_meta( $post_id, self::$meta_key_date );
	}

	/**
	 * Returns the ID of the user who trashed the post.
	 *
	 * Note: Makes no attempt to guess who deleted a post that was deleted
	 * while this plugin was not active (e.g. all pre-existing posts). Also
	 * does not return the meta field value, if present, if the post isn't
	 * currently in the trash.
	 *
	 * @since 1.0
	 * @since 1.3 Renamed from `get_trasher_id()`
	 *
	 * @param  int|WP_Post|null $post Optional. Post ID, object, or null for
	 *                                current post. Default null.
	 * @return int The ID of the user who trashed the post.
	 */
	public static function get_trashed_by( $post = null ) {
		$trashed_by = 0;
		$post       = get_post( $post );

		if ( $post && 'trash' === get_post_status( $post ) ) {
			// Use trasher id saved in custom field by the plugin.
			$trashed_by = get_post_meta( $post->ID, self::$meta_key_user, true );
		}

		return (int) $trashed_by;
	}

	/**
	 * DEPRECATED. Returns the ID of the user who trashed the post.
	 *
	 * @since 1.0
	 * @deprecated 1.3 Use get_trashed_by() instead
	 *
	 * @param  int|WP_Post|null $post Optional. Post ID, object, or null for
	 *                                current post. Default null.
	 * @return int The ID of the user who trashed the post.
	 */
	public static function get_trasher_id( $post = null ) {
		_deprecated_function( 'get_trasher_id', '1.3', 'get_trashed_by' );

		return self::get_trashed_by( $post );
	}

	/**
	 * Returns the date the post was trashed.
	 *
	 * Note: Makes no attempt to guess when the post was deleted if that post
	 * was deleted while this plugin was not active (e.g. all pre-existing
	 * posts). Also does not return the meta field value, if present, if the
	 * post isn't currently in the trash.
	 *
	 * @since 1.0
	 *
	 * @param  int|WP_Post|null $post Optional. Post ID, object, or null for
	 *                                current post. Default null.
	 * @return string The datetime string for when the post was trashed.
	 */
	public static function get_trashed_on( $post = null ) {
		$trashed_on = '';
		$post       = get_post( $post );

		if ( $post && 'trash' === get_post_status( $post ) ) {
			// Use trashed date saved in custom field by the plugin.
			$trashed_on = get_post_meta( $post->ID, self::$meta_key_date, true );
		}

		return $trashed_on;
	}

} // end c2c_TrashedBy

add_action( 'plugins_loaded', array( 'c2c_TrashedBy', 'init' ) );

endif; // end if !class_exists()
