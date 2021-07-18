=== Trashed By ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: trash, deleted, post, audit, tracking, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.7
Stable tag: 1.4

Records which user trashed a post and when they trashed it. Displays that info as columns in admin trashed posts listings.

== Description ==

This plugin records which user actually trashed a post, which in a multi-author environment may not always be the original post author. This helps to maintain accountability for who was ultimately responsible for deleting a post. It also records when the post got trashed.

The admin listing of trashed posts is amended with new "Trashed By" and "Trashed On" columns that shows the name of the person who trashed the post or page and the date the post was trashed, respectively.

The plugin makes no assumption about who trashed a post, or when, for posts that were trashed prior to the use of this plugin (since the plugin could not have directly recorded information about the post's trashing). The "Trashed By" and "Trashed On" values for those posts will remain empty. Put another way, only posts or pages trashed while this plugin is active will have the user who trashed the post/page and that date recorded.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/trashed-by/) | [Plugin Directory Page](https://wordpress.org/plugins/trashed-by/) | [GitHub](https://github.com/coffee2code/trashed-by/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or download and unzip `trashed-by.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
2. Activate the plugin through the 'Plugins' admin menu in WordPress


== Screenshots ==

1. The admin post trash listing showing the added "Trashed By" and "Trashed On" columns.


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

Just so everyone is clear, "Trashed" and "Permanently deleted" are two different things.

Trashed posts get assigned a post status of "trash" and then only appear in the "Trash" list of the page/post area of the admin. These posts still exist, they're just hidden from public view. This operates like the trash feature in your operating system; you can still go into the trash to retrieve something before it is gone for good. As such, it is possible for the plugin to keep track of and report who trashed the post.

Permanently deleted posts (whether done so directly by a user or automatically by WordPress for posts that have been in the trash for a period of time) are completely deleted from the database. This plugin does not track who deleted those posts.

= How do I see (or hide) the "Trash By" and/or "Trashed On" columns in an admin listing of posts? =

In the upper-right of the page is a "Screen Options" link that reveals a panel of options. In the "Columns" section, check (to show) or uncheck (to hide) the "Trashed By" and/or "Trashed On" options.

= Does this plugin include unit tests? =

Yes.


== Changelog ==

= 1.4 (2021-07-18) =
Highlights:

This minor release reimplements how the plugin hooks into WordPress to handle when a post is trashed or untrashed, restructures unit test files, notes compatibility through WP 5.7, and more.

Details:

* Change: Separately hook filters directly related to a post being trashed or untrashed rather than checking during any post status transition
    * New: Add `trash_post()` to react when a post is trashed
    * New: Add `untrash_post()` to react when a post is untrashed
    * Delete: Remove `transition_post_status()`
* Fix: Change `__wakeup()` method visibility from `private` to `public` to avoid warnings under PHP8
* Change: Throw an error if class is instantiated or unserialized
* Change: Improve some function and parameter inline documentation
* Change: Note compatibility through WP 5.7+
* Change: Update copyright date (2021)
* Unit tests:
    * Change: Restructure unit test directories and files within `tests/` top-level directory
        * Change: Move `bin/` into `tests/`
        * Change: Move `tests/bootstrap.php` into `tests/phpunit/`
        * Change: In bootstrap, store path to plugin file constant so its value can be used within that file and in test file
        * Change: In bootstrap, check for test installation in more places and exit with error message if not found
        * Change: Move `tests/*.php` into `tests/phpunit/tests/`
        * Change: Remove 'test-' prefix from unit test files
        * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Remove "A screenshot of" prefix from caption
* New: Add a few more possible TODO items

= 1.3.1 (2020-07-27) =
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* Change: Fix typo in docs
* Change: Remove a tag from readme.txt's 'Tags' field
* Unit tests: Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)

= 1.3 (2020-03-17) =
* New: Add CHANGELOG.md and move all but most recent changelog entries into it
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add more items to the list)
* New: Add .gitignore file
* New: Add link to plugin's page in Plugin Directory to README.md
* Improve meta key handling:
    * New: Add `is_protected_meta()` to protect the meta key from being exposed as a custom field
    * Change: Update `register_meta()` with a proper auth_callback
    * Change: Prefer registering meta via `register_post_meta()` when available
    * Change: Register meta on `init` action instead of `plugins_loaded`
* Change: Record date post was trashed even if a current user can't be determined
* Change: Expose custom field via REST API
* Change: Omit 'type' attribute for 'style' tag
* Change: Add `get_trashed_by()` as the renamed replacement for the now-deprecated `get_trasher_id()`
* Change: Support a post object or null (for current post) being sent as the argument to `get_trashed_by()` and `get_trashed_on()`
* Change: Prevent object instantiation of the class
* Change: Remove duplicate hook registrations
* Change: Allow string "you" to be translated
* Unit tests:
    * New: Add more unit tests
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Add test and data provider for default hooks that should get hooked
    * Change: Allow `create_user()` to accept an array argument of user attributes
    * Change: Use `update_post_meta()` within `create_user()` instead of `add_post_meta()`
* Change: Note compatibility through WP 5.3+
* Change: Drop compatibility with version of WP older than 4.9
* Change: Tweak plugin description
* Change: Amend inline docs for `get_trashed_by()` and `get_trashed_on()` to indicate that meta values won't be returned, even if present, if post isn't in the trash
* Change: Add inline documentation reference for a filter that originates in WP
* Change: Split paragraph in README.md's "Support" section into two
* Change: Update copyright date (2020)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/trashed-by/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.4 =
Minor update: Reimplemented how plugin hooks into WordPress to handle a post being trashed or untrashed, restructured unit test files, noted compatibility through WP 5.7, updated copyright date (2021), and more minor changes.

= 1.3.1 =
Trivial update: Updated a few URLs to be HTTPS and noted compatibility through WP 5.4+.

= 1.3 =
Recommended minor update: improved meta key handling, added a lot more unit tests, added CHANGELOG.md, added TODO.md, noted compatibility through WP 5.3+, dropped compatibility with versions of WP older than 4.9, updated copyright date (2020), and more minor changes.

= 1.2 =
Minor update: modified initialization handling, escaped attribute prior to display (hardening), noted compatibility through WP 5.1+, updated copyright date (2019)

= 1.1.1 =
Trivial update: noted compatibility through WP 4.9+; added README.md; added GitHub link to readme; updated copyright date (2018)

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
