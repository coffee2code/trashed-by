<?php
/**
 * Plugin Name: Trashed By
 * Version:     1.0.2
 * Plugin URI:  http://coffee2code.com/wp-plugins/trashed-by/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: trashed-by
 * Domain Path: /lang/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Tracks the user who trashed a post and when they trashed it. Displays that info as columns in admin trashed posts listings.
 *
 * Compatible with WordPress 3.6 through 4.1+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/trashed-by/
 *
 * @package Trashed_By
 * @author  Scott Reilly
 * @version 1.0.2
 */

/*
TODO:
	* Provisions to disable/enable per post_type?
*/

/*
	Copyright (c) 2014-2015 by Scott Reilly (aka coffee2code)

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

	private static $meta_key_user = 'c2c-trashed-by';
	private static $meta_key_date = 'c2c-trashed-on';
	private static $field_user    = 'trashed_by';
	private static $field_date    = 'trashed_on';

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.0
	 */
	public static function version() {
		return '1.0.2';
	}

	/**
	 * Hooks actions and filters.
	 *
	 * @since 1.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Performs initializations on the 'init' action.
	 *
	 * @since 1.0
	 */
	public static function do_init() {
		// Load textdomain
		load_plugin_textdomain( 'trashed-by', false, basename( __DIR__ ) );

		// Register hooks
		add_filter( 'manage_posts_columns',        array( __CLASS__, 'add_post_column' )               );
		add_action( 'manage_posts_custom_column',  array( __CLASS__, 'handle_column_data' ),     10, 2 );
		add_filter( 'manage_pages_columns',        array( __CLASS__, 'add_post_column' )               );
		add_action( 'manage_pages_custom_column',  array( __CLASS__, 'handle_column_data' ),     10, 2 );

		add_action( 'load-edit.php',               array( __CLASS__, 'add_admin_css' )                 );
		add_action( 'transition_post_status',      array( __CLASS__, 'transition_post_status' ), 10, 3 );
		add_filter( 'is_protected_meta',           array( __CLASS__, 'hide_meta' ),              10, 2 );
	}

	/**
	 * Determines if the Trashed By column should be shown.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	private static function include_column() {
		return ( isset( $_GET['post_status'] ) && 'trash' == $_GET['post_status'] );
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
		echo "<style type='text/css'>.fixed .column-" . self::$field_user . ", .fixed .column-" . self::$field_date . " { width: 10%; }</style>\n";
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
			$trasher_id = self::get_trasher_id( $post_id );
			if ( $trasher_id ) {
				$trasher = get_userdata( $trasher_id );
				echo sanitize_text_field( $trasher->display_name );
			}

		// Display the date for when the post was trashed.
		} elseif ( self::$field_date === $column_name ) {
			$trashed_date = self::get_trashed_on( $post_id );
			if ( $trashed_date ) {
				$post      = get_post( $post_id );
				$t_time    = mysql2date( __( 'Y/m/d g:i:s A' ), $trashed_date, false );
				$time_from = mysql2date( 'U', $trashed_date, false );
				$time_to   = current_time( 'timestamp', false );
				$time_diff = $time_to - $time_from;

				if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
					$h_time = sprintf( __( '%s ago' ), human_time_diff( $time_from, $time_to ) );
				} else {
					$h_time = mysql2date( __( 'Y/m/d' ), $trashed_date );
				}

				echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'trashed_on', 'list' ) . '</abbr>';
			}
		}
	}

	/**
	 * Hides the meta key from the custom field dropdown.
	 *
	 * @since 1.0
	 *
	 * @param  bool   $protected Is the meta key protected?
	 * @param  string $meta_key  The meta key
	 * @return bool
	 */
	public static function hide_meta( $protected, $meta_key ) {
		if ( in_array( $meta_key, array( self::$meta_key_user, self::$meta_key_date ) ) ) {
			return true;
		}

		return $protected;
	}

	/**
	 * Records the date a post was trashed and the user who trashed the post.
	 *
	 * @since 1.0
	 *
	 * @param string  $new_status New post status
	 * @param string  $old_status Old post status
	 */
	public static function transition_post_status( $new_status, $old_status, $post ) {
		// Only concerned with posts changing post status
		if ( $new_status === $old_status ) {
			return;
		}

		// Save user and date for post being trashed
		if ( 'trash' === $new_status ) {
			// Can only save trashing user ID if one can be obtained
			if ( $current_user_id = get_current_user_id() ) {
				update_post_meta( $post->ID, self::$meta_key_user, $current_user_id );
				update_post_meta( $post->ID, self::$meta_key_date, current_time( 'mysql' ) );
			}

		// Clear trashing user and date when being untrashed
		} elseif ( 'trash' === $old_status ) {
			delete_post_meta( $post->ID, self::$meta_key_user );
			delete_post_meta( $post->ID, self::$meta_key_date );
		}
	}

	/**
	 * Returns the ID of the user who trashed the post.
	 *
	 * Note: Makes no attempt to guess who deleted a post that was deleted
	 * while this plugin was not active (e.g. all pre-existing posts).
	 *
	 * @since 1.0
	 *
	 * @param  int $post_id The id of the post being displayed.
	 * @return int The ID of the user who trashed the post.
	 */
	public static function get_trasher_id( $post_id ) {
		$trasher_id = '';
		$post       = get_post( $post_id );

		if ( $post && 'trash' === get_post_status( $post_id ) ) {
			// Use trasher id saved in custom field by the plugin.
			$trasher_id = get_post_meta( $post_id, self::$meta_key_user, true );
		}

		return $trasher_id;
	}

	/**
	 * Returns the date the post was trashed.
	 *
	 * @since 1.0
	 *
	 * @param  int    $post_id The id of the post being displayed.
	 * @return string The datetime string for when the post was trashed.
	 */
	public static function get_trashed_on( $post_id ) {
		$trashed_on = '';
		$post       = get_post( $post_id );

		if ( $post && 'trash' === get_post_status( $post_id ) ) {
			// Use trashed date saved in custom field by the plugin.
			$trashed_on = get_post_meta( $post_id, self::$meta_key_date, true );
		}

		return $trashed_on;
	}

} // end c2c_TrashedBy

c2c_TrashedBy::init();

endif; // end if !class_exists()
