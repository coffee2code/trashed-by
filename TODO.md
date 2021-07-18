# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Apply to all post types by default
* Provisions to disable/enable per post_type? (via filter at least)
* Log permanently deleted posts. Perhaps as a new post type not listed in the main menu. Maybe accessible via pseudo-status link atop post listing table (e.g. "| Permanently Deleted (21)" ). Only duplicate necessary fields from original post before its deletion: post_title, post_author, post_date, post_modified, post_type (stored as meta), and the trashed-by and trashed-on metas. Add hook to allow other data about post to be stored as post meta.
* Re-evaluate showing "you" when current user is the one who trashed a post
  * Should user see "me" instead of "you"?
  * Should it be configurable (via filter) to allow showing actual username?
  * Or, should it just behave like the Author column and show actual username by default, with the other alternative(s) as filterable options.
* Consider not deleting meta data when a post is untrashed.
  * Might be useful as a simple way of knowing that a post was previously trashed. Expose this in UI anywhere?
  * Likely something that would need to be enabled enabled via filter
  * Does it warrant untrashed_by/untrashed_on meta fields?
  * Only saves most recent trashed by/on data, so isn't a true trash activity log for the post. But how often are posts being trashed and untrashed? Undoubtedly there are activity logging plugins for users really concerned about full logging history.
* Add GDPR compliance for data export and erasure

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/trashed-by/) or on [GitHub](https://github.com/coffee2code/trashed-by/) as an issue or PR).
 