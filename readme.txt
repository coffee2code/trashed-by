=== Trashed By ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: trash, deleted, post, audit, auditing, tracking, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.6
Tested up to: 3.8
Stable tag: 1.0

Track which user actually trashed a post, separate from who created the post. Display that info as a column in admin post trash listings.

== Description ==

This plugin records which user actually trashed/deleted a post, which in a multi-author environment may not always be the original post author. This helps to maintain accountability for who was ultimately responsible for deleting a post.

The admin listing of trashed posts is amended with a new "Trashed By" column that shows the name of the person who trashed the post or page.

For posts that were trashed prior to the use of this plugin (thus the plugin could not have directly recorded who trashed those posts), the plugin makes no assumption about who trashed those posts. The "Trashed By" value for those posts will remain empty. Put another way, only posts or pages trashed while this plugin is active will have the user who trashed the post/page recorded.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/trashed-by/) | [Plugin Directory Page](http://wordpress.org/plugins/trashed-by/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `trashed-by.zip` inside the plugins directory for your site (typically `/wp-content/plugins/`). Or install via the built-in WordPress plugin installer)
2. Activate the plugin through the 'Plugins' admin menu in WordPress


== Screenshots ==

1. A screenshot of the admin post trash listing showing the added "Trashed By" column.


== Frequently Asked Questions ==

= If a post is trashed, then restored, and then trashed a second time by a different person, who is noted as the trashing user? =

The user most recently responsible for trashing a post will be recorded as the trashing user.

= Why is the "Trashed By" column blank for posts in trash? =

This should only be the case for posts that were trashed prior to activating this plugin (or any time when the plugin wasn't active).

= Does this plugin include unit tests? =

Yes.


== Changelog ==

= 1.0 =
* Initial public release


== Upgrade Notice ==

= 1.0 =
Initial public release.
