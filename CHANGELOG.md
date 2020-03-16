# Changelog

## _(in-progress)_
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
* Change: Support a post object or null (for current post) being sent as the argument to `get_trasher_id()` and `get_trashed_on()`
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
* Change: Amend inline docs for `get_trasher_id()` and `get_trashed_on()` to indicate that meta values won't be returned, even if present, if post isn't in the trash
* Change: Add inline documentation reference for a filter that originates in WP
* Change: Split paragraph in README.md's "Support" section into two
* Change: Update copyright date (2020)

## 1.2 _(2018-03-05)_
* Change: (Hardening) Escape attribute before being output within markup
* Change: Initialize plugin on 'plugins_loaded' action instead of on load
* Change: Merge `do_init()` into `init()`
* Change: Note compatibility through WP 5.1+
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS

## 1.1.1 _(2017-12-27)_
* New: Add README.md
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Update copyright date (2018)
* Change: Minor code spacing in unit test bootstrap

## 1.1 _(2017-01-29)_
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

## 1.0.4 _(2016-01-22)_
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

## 1.0.3 _(2015-09-01)_
* Change: Use `dirname(__FILE__)` instead of `__DIR__` since the latter is only available on PHP 5.3+.
* Change: Note compatibility through WP 4.3+.

## 1.0.2 _(2015-02-17)_
* Minor additions to unit tests
* Use `__DIR__` instead of `dirname(__FILE__)`
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Regenerate .pot

## 1.0.1 _(2014-08-25)_
* Change documentation links to wp.org to be https
* Change donate link
* Note compatibility through WP 4.0+
* Add plugin icon

## 1.0
* Initial public release
