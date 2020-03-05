# Changelog

## _(in-progress)_
* New: Add CHANGELOG.md and move all but most recent changelog entries into it

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