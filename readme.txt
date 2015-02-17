=== Trashed By ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: trash, deleted, post, audit, auditing, tracking, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 1.0.2

Tracks the user who trashed a post and when they trashed it. Displays that info as columns in admin trashed posts listings.

== Description ==

This plugin records which user actually trashed/deleted a post, which in a multi-author environment may not always be the original post author. This helps to maintain accountability for who was ultimately responsible for deleting a post. It also records when the post got deleted.

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

No. Once a trashed post is restored and thus removed from the trash, the information about when and who deleted the post is deleted.

= Does this plugin include unit tests? =

Yes.


== Changelog ==

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

= 1.0.2 =
Trivial update: minor additions to unit tests; noted compatibility through WP 4.1+; updated copyright date (2015)

= 1.0.1 =
Trivial update: noted compatibility through WP 4.0+; added plugin icon.

= 1.0 =
Initial public release.
