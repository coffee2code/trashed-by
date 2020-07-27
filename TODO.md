# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Provisions to disable/enable per post_type?
* Log permanently deleted posts. Perhaps as a new post type not listed in the main menu. Maybe accessible via pseudo-status link atop post listing table (e.g. "| Permanently Deleted (21)" ). Only duplicate necessary fields from original post before its deletion: post_title, post_author, post_date, post_modified, post_type (stored as meta), and the trashed-by and trashed-on metas. Add hook to allow other data about post to be stored as post meta.
* Should user see "me" instead of "you" if they are the user who trashed a post?

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/trashed-by/) or on [GitHub](https://github.com/coffee2code/trashed-by/) as an issue or PR).
 