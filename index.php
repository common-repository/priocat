<?php
/*
Plugin Name: PrioCat
Plugin URI: http://www.flopper.dk
Description: PrioCat can sort WordPress categories.
Version: 0.2
Author: Filip Wallberg
Author URI: http://www.flopper.dk

Copyright 2010 Filip Wallberg  (email : filip@flopper.dk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */

$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'PrioCat', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );

add_action('admin_menu', 'priocat_config_page');

function priocat_config_page() {
	add_submenu_page('post-new.php', __('PrioCat'), __('PrioCat'), 'manage_options', 'priocat-edit', 'priocat_conf');
}

add_action('admin_print_scripts', 'scripts_til_head');

function scripts_til_head() {
	$js_scriptaculous = get_bloginfo('wpurl') . "/wp-content/plugins/PrioCat/js/scriptaculous.js";
	wp_enqueue_script('scriptaculous', ($js_scriptaculous), array('scriptaculous'), '2.0');
}

function priocat_conf() {
	global $wpdb;

	$posts = $wpdb->prefix . "posts";
	$term_relationships = $wpdb->prefix . "term_relationships";
	$postmeta = $wpdb->prefix . "postmeta";
	$term_taxonomy = $wpdb->prefix . "term_taxonomy";
	$terms = $wpdb->prefix . "terms";

	if($_REQUEST[cat]):
		$category = $_REQUEST[cat];
		$cat_priot = "cat_priot_" . $category;
	else:
		$sql1 = $wpdb->get_results("SELECT term_id FROM $term_taxonomy WHERE taxonomy = 'category' ORDER BY term_id ASC limit 1");
		foreach($sql1 as $sql2) {
			$category = $sql2->term_id;
			$cat_priot = "cat_priot_" . $category;
		}
	endif;
	
	
		$sql001 = $wpdb->get_results("SELECT term_taxonomy_id FROM $term_taxonomy WHERE term_id = $category");
	foreach($sql001 as $sql002) {
		$category_term_id = $sql002->term_taxonomy_id;
	}

		$sql15 = $wpdb->get_results("SELECT name FROM $terms WHERE term_id = $category limit 1");
		foreach($sql15 as $sql16) {
			$category_name = $sql16->name;
		}

	if($_REQUEST[show_on_cat] == "y"):
		$wpdb->insert( 'wp_postmeta', array( 'post_id' => $_REQUEST[post_ID], 'meta_key' => $cat_priot, 'meta_value' => '-1'  ));
		unset($_REQUEST[post_ID]);
	endif;

	if($_REQUEST[show_on_cat] == "n"):
		$wpdb->query("DELETE FROM $postmeta WHERE post_id = '$_REQUEST[post_ID]' AND meta_key = '$cat_priot'");
		unset($_REQUEST[post_ID]);
	endif;
?>

<div class="wrap">

<h2><?php _e('PrioCat'); ?>: <?php echo $category_name; ?></h2>

<div>

<form name="jump">

<select name="menu" onChange="location=document.jump.menu.options[document.jump.menu.selectedIndex].value;" value="GO">

<option value="edit.php?page=priocat-edit"> - - Select a different category - - </option>

<?php
	$sql11 = $wpdb->get_results("SELECT term_id FROM $term_taxonomy WHERE taxonomy = 'category' ORDER BY term_id ASC");
	foreach($sql11 as $sql12) {
		$term_id = $sql12->term_id;
		$sql13 = $wpdb->get_results("SELECT name FROM $terms WHERE term_id = $term_id");
		foreach($sql13 as $sql14) {
			$name = $sql14->name;
		}
?>

<option value="edit.php?page=priocat-edit&cat=<?php echo $term_id; ?>"><?php echo $name; ?></option>

<?php
unset($name);
?>

<?php
	}
?>

</select>

</form>

</div>

<div style="width:350px;float:left;">

<h3>Shown on page:</h3>

<ul id="list_to_sort" class="sort_afspilningslister1">

<?php
	$x = 0;
	
	$sql3 = $wpdb->get_results("SELECT post_id FROM $postmeta WHERE meta_key = '$cat_priot' ORDER BY meta_value ASC");
	foreach($sql3 as $sql4) {	
		$post_id = $sql4->post_id;
		$gemte_poster[$x] = $post_id;
		$x = $x + 1;
		$sql5 = $wpdb->get_results("SELECT post_title FROM $posts WHERE ID = $post_id AND post_type = 'post'");
		foreach($sql5 as $sql6) {
			$post_title = $sql6->post_title;
		}	
		if($post_title):
?>

<li id="item_<?php echo $post_id; ?>" style="margin:6px 6px 0 0;width:300px;text-align:left;padding-top:5px;padding-right:5px;padding-left:5px;padding-bottom:5px;border:1px solid #000;"><a href="edit.php?page=priocat-edit&post_ID=<?php echo $post_id; ?>&show_on_cat=n&cat=<?php echo $category; ?>"><img ALIGN="absmiddle" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/PrioCat/images/48px-Crystal_Clear_action_button_cancel.png" height="20px" WIDTH="20px" /></a>  <a href="post.php?action=edit&post=<?php echo $post_id ?>"><?php echo $post_title; ?></a></li>

<?php
			unset($post_id);
			unset($post_title);
		endif;
	}
?>

</ul>

</div>

<script type="text/javascript">
Sortable.create("list_to_sort", {
onUpdate: function() {
new Ajax.Request("../wp-content/plugins/PrioCat/sort.php?page=priocat-edit&cat=<?php echo $category; ?>&sort=sort&tabel=<?php echo $postmeta; ?>", {
method: "post",
parameters: { data: Sortable.serialize("list_to_sort") }
});
}
});
</script>

<div style="width:350px;float:left;">

<h3>Not shown on page:</h3>

<ul id="not-sortlist" class="sort_afspilningslister1">

<?php
	$sql7 = $wpdb->get_results("SELECT object_id FROM $term_relationships WHERE term_taxonomy_id = $category_term_id ORDER BY object_id DESC");
	foreach($sql7 as $sql8) {
			$object_id = $sql8->object_id;	
		if(!empty($gemte_poster)):
			if(in_array($object_id,$gemte_poster)):
				unset($object_id);
			endif;
		endif;
	
		if($object_id):
			$sql9 = $wpdb->get_results("SELECT post_title FROM $posts WHERE ID = $object_id AND post_type = 'post' ORDER BY ID DESC limit 1");
			foreach($sql9 as $sql10) {
				$post_title = $sql10->post_title;
			}
		
			if($post_title):
	?>
	
<li id="item_<?php echo $object_id; ?>" style="margin:6px 6px 0 0;width:300px;text-align:left;padding-top:5px;padding-right:5px;padding-left:5px;padding-bottom:5px;border:1px solid #000;"><a href="edit.php?page=priocat-edit&post_ID=<?php echo $object_id; ?>&show_on_cat=y&cat=<?php echo $category; ?>"><img ALIGN="absmiddle" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/PrioCat/images/48px-Crystal_Clear_action_edit_add.png" height="20px" WIDTH="20px" /></a>  <a href="post.php?action=edit&post=<?php echo $object_id; ?>"><?php echo $post_title; ?></a></li>
	
	<?php
				unset($object_id);
				unset($post_title);
			endif;
		endif;
	}
?>
</ul>

</div>

<div style="width:350px;float:left;clear: both;">

</div>

<?php
}
?>