=== Plugin Name ===
Contributors: Filip Wallberg
Tags: Categories, sorting, prioritizing
Requires at least: 2.9.2
Tested up to: 3.0

You can use this plugin to prioritize the posts in each category.

== Description ==

WordPress is a great CMS. But until now one critical function has been missing: You haven't been able to sort each category. 

But now you can - with this amazing plugin: PrioCat. 

The plugin uses Custom Fields to sort the posts.

This plugin use Scriptaculous.

== Installation ==

1. Upload the folder PrioCat to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Place `if (is_category()): if($_REQUEST[cat]): $cat_id = $_REQUEST[cat]; else: $wp_terms = $wpdb->prefix . "terms"; $cat_id = single_cat_title("", false); $sql15 = $wpdb->get_results("SELECT term_id FROM $wp_terms WHERE name = '$cat_id' limit 1"); foreach($sql15 as $sql16) { $cat_id = $sql16->term_id; } endif; $cat_priot = "cat_priot_" . $cat_id; $posts = query_posts($query_string. '&orderby=meta_value_num&meta_key=' . $cat_priot . '&order=ASC' ); endif;` in the template archive.php. Before get_header();. If you don't have a archive.php, then place the code in the template index.php.

== Changelog ==

= 0.1 =
* The first stable version of PrioCat.

= 0.2 =
* Updated PHP-code to the templates.

= 0.3 =
* Updated PHP-code in the admin and templates.