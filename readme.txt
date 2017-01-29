=== Trashed By ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: trash, deleted, post, audit, auditing, tracking, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 1.1

Tracks the user who trashed a post and when they trashed it. Displays that info as columns in admin trashed posts listings.

== Description ==

This plugin records which user actually trashed a post, which in a multi-author environment may not always be the original post author. This helps to maintain accountability for who was ultimately responsible for deleting a post. It also records when the post got trashed.

The admin listing of trashed posts is amended with new "Trashed By" and "Trashed On" columns that shows the name of the person who trashed the post or page and the date the post was trashed, respectively.

The plugin makes no assumption about who trashed a posts, or when, for posts that were trashed prior to the use of this plugin (since the plugin could not have directly recorded information about the post's trashing). The "Trashed By" and "Trashed On" values for those posts will remain empty. Put another way, only posts or pages trashed while this plugin is active will have the user who trashed the post/page and that date recorded.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/trashed-by/) | [Plugin Directory Page](https://wordpress.org/plugins/trashed-by/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `trashed-by.zip` inside the plugins directory for your site (typically `/wp-content/plugins/`). Or install via the built-in WordPress plugin installer)
2. Activate the plugin through the 'Plugins' admin menu in WordPress


== Screenshots ==

1. A screenshot of the admin post trash listing showing the added "Trashed By" and "Trashed On" columns.


== Frequently Asked Questions ==

= What if I am only interested in seeing when a post was trashed but not who trashed it? =

The visibility of the "Trashed By" and "Trashed On" columns can be controlled by the "Screen Options" slide-down options panel available at the top right of the page when viewing the trash listing in the admin.

= If a post is trashed, then restored, and then trashed a second time by a different person, who is noted as the trashing user? =

The user most recently responsible for trashing a post will be recorded as the trashing user.

= Why are the "Trashed By" and "Trashed On" columns blank for some posts in the trash? =

This should only be the case for posts that were trashed prior to activating this plugin (or any time when the plugin wasn't active).

= Does the plugin retain any information about a previously trashed post once it has been restored? =

No. Once a trashed post is restored and thus removed from the trash, the information about when and who trashed the post is deleted.

= Does this plugin track who permanently deleted a posted? =

Just so everyone is clear, "Trashed" and "Permanently deleted" are two differently things.

Trashed posts get assigned a post status of "trash" and then only appear in the "Trash" list of the page/post area of the admin. These posts still exist, they're just hidden from public view. This operates like the trash feature in your operating system; you can still go into the trash to retrieve something before it is gone for good. As such, it is possible for the plugin to keep track of and report who trashed the post.

Permanently deleted posts (whether done so directly by a user or automatically by WordPress for posts that have been in the trash for a period of time) are completely deleted from the database. This plugin does not track who deleted those posts.

= How do I see (or hide) the "Trash By" and/or "Trashed On" columns in an admin listing of posts? =

In the upper-right of the page is a "Screen Options" link that reveals a panel of options. In the "Columns" section, check (to show) or uncheck (to hide) the "Trashed By" and/or "Trashed On" options.

= Does this plugin include unit tests? =

Yes.


== Changelog ==

= 1.1 (2017-01-29) =
* New: When showing the 'Trashed By' user, link their display name to their profile page.
    * Add `get_user_url()` to get the link to the user's profile
* Change: Register meta field via `register_meta()`
    * Add own `register_meta()`
    * Remove `hide_meta()` in favor of use of `register_meta()`
    * Do not include meta fields and values in REST API responses for posts
* Change: If the current user is the person who trashed the post, then simply state "you" as the name.
* Change: Ensure `get_trasher_id()` returns an integer value.
* Change: Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable.
* Change: Enable more error output for unit tests.
* Change: Note compatibility through WP 4.7+.
* Change: Remove support for WordPress older than 4.6 (should still work for earlier versions)
* New: Add FAQ about showing or hiding the "Trashed By" and "Trashed On" columns.
* New: Add LICENSE file.
* Change: Add inline docs for class variables.
* Change: Update screenshot.
* Change: Update copyright date (2017).

= 1.0.4 (2016-01-22) =
* New: Add support for language packs:
    * Add omitted textdomain from some string translation calls.
    * Remove 'Domain Path' header attribute.
    * Don't load textdomain from file.
    * Remove .pot file and /lang subdirectory.
* New: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* New: Add additional FAQ about trash versus permanently deleted.
* Change: Note compatibility through WP 4.4+.
* Change: Explicitly declare methods in unit tests as public.
* Change: Update copyright date (2016).

= 1.0.3 (2015-09-01) =
* Change: Use `dirname(__FILE__)` instead of `__DIR__` since the latter is only available on PHP 5.3+.
* Change: Note compatibility through WP 4.3+.

= 1.0.2 (2015-02-17) =
* Minor additions to unit tests
* Use __DIR__ instead of `dirname(__FILE__)`
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Regenerate .pot

= 1.0.1 (2014-08-25) =
* Change documentation links to wp.org to be https
* Change donate link
* Note compatibility through WP 4.0+
* Add plugin icon

= 1.0 =
* Initial public release


== Upgrade Notice ==

= 1.1 =
Minor feature update: linked usernames to profiles, referred to currenet user as "you", compatibility is now WP 4.6-4.7+, updated copyright date (2017), and more

= 1.0.4 =
Trivial update: improved support for localization, minor unit test tweaks, verified compatibility through WP 4.4+, and updated copyright date (2016)

= 1.0.3 =
Minor bugfix release for users running PHP 5.2.x: revert use of a constant only defined in PHP 5.3+. You really should upgrade your PHP or your host if this affects you. Also noted compatibility with WP 4.3+.

= 1.0.2 =
Trivial update: minor additions to unit tests; noted compatibility through WP 4.1+; updated copyright date (2015)

= 1.0.1 =
Trivial update: noted compatibility through WP 4.0+; added plugin icon.

= 1.0 =
Initial public release.
