<?php

/*
Plugin Name: My Tag Cloud
Plugin URI: http://www.stratos.me/wp-plugins/my-tag-cloud
Description: It provides you with a widget to show your tag cloud with an alternative way. First 5 tags will be on a list and the rest will be on a dropdown. Clicking takes you to the tag page. There are a few preferences you can set in the code.
Author: stratosg
Version: 1.0
Author URI: http://www.stratos.me
*/


function widget_mytagcloud($args) {
	global $wpdb;
	global $wpdb_query;
	extract($args);
	
	/*-------- PREFERENCES START --------*/
	$list_tags = 5;//how many tags to show on the list before adding to the dropdown
	$widget_title = 'My Tag Cloud';//title of the widget
	$order_tags = 'count';//you can set this to "count" or "name".
						  //Count means the most popular will come first, name means they will be ordered alphabeticaly
	/*-------- PREFERENCES END ----------*/
	
	echo $before_widget;
	echo $before_title; echo $widget_title; echo $after_title;
	
	$site_base = get_bloginfo('url');
	
	$tags_query = "SELECT
						terms.name, terms.slug, term_taxonomy.count
				   FROM $wpdb->terms as terms, $wpdb->term_taxonomy as term_taxonomy
				   WHERE
						terms.term_id = term_taxonomy.term_id
						AND term_taxonomy.taxonomy = 'post_tag'
				   ORDER BY ".($order_tags == 'count' ? "term_taxonomy.count DESC" : "terms.name ASC");
	$tags = $wpdb->get_results($tags_query, ARRAY_A);
	if(count($tags) > $list_tags){//i have more tags so a list and a dropdown should be rendered
		echo '<ul>';
		for($i = 0; $i<$list_tags; $i++){
			echo '<li><a title="Used '.$tags[$i]['count'].' times" href="'.$site_base.'/tag/'.$tags[$i]['slug'].'">'.$tags[$i]['name'].'</a></li>';
		}
		echo '</ul><br>';
		echo '<script lang="javascript">
				function goOnTagSelect(){
					var tag = document.getElementById("mytags_select").value;
					var site_url = "'.$site_base.'";
					document.location = site_url + "/tag/" +  tag;
				}
			  </script>';
		echo '<select id="mytags_select" onchange="goOnTagSelect()">
				<option>Choose tag...</option>';
		for($i = $list_tags; $i<count($tags); $i++){
			echo '<option value="'.$tags[$i]['name'].'">'.$tags[$i]['name'].' ('.$tags[$i]['count'].')</option>';
		}
		echo '</select>';
	}
	else{//just a list is fine
		echo '<ul>';
		foreach($tags as $tag){
			echo '<li><a title="Used '.$tags[$i]['count'].' times" href="'.$site_base.'/tag/'.$tag['slug'].'">'.$tag['name'].'</a></li>';
		}
		echo '</ul>';
	}
	echo $after_widget;
}

function mytagcloud_init()
{
  register_sidebar_widget(__('MY Tag Cloud'), 'widget_mytagcloud');
}
add_action("plugins_loaded", "mytagcloud_init");


?>