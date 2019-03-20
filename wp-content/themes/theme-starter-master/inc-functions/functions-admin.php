<?php

// Add user id to body cloass for admin
// Allows me to select in jquery to append/hide/show things based on the suer
function jn_admin_body_class( $classes ) {
	
	$current_user = wp_get_current_user();
	return $classes.' user-'.$current_user->ID;
}
add_filter( 'admin_body_class', 'jn_admin_body_class' );

// Remove menue options in admin
function remove_menus () {
global $menu;
	$restricted = array( __('Links'), __('Comments') );
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
	}
}
add_action('admin_menu', 'remove_menus');

// Allows the Editor role to edit the menu and appearance 
$role_object = get_role( 'editor' ); // get the the role object
$role_object->add_cap( 'edit_theme_options' ); // add $cap capability to this role object

// Add CSS file and favicon to admin
add_action('admin_head', 'my_admin_head');
function my_admin_head() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('template_directory').'/assets/img/favicon.png" />';
}

// Custom login css
add_action('login_head', 'custom_login');
function custom_login() { 
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/admin/style.css">';
}

// Remove 'Wordpress' from admint page title
add_filter('admin_title', 'my_admin_title', 10, 2);
function my_admin_title($admin_title, $title) {
    return get_bloginfo('name').' | '.$title;
}

// Remove Comments from admin bar
function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );


// Change login title and URL for logo
add_filter("login_headerurl","collage_custom_login_link");
add_filter("login_headertitle","collage_custom_login_title");
function collage_custom_login_link($url) {
	return get_bloginfo('url');
}
function collage_custom_login_title($message) {
	return get_bloginfo('name');
}

// Remove Wordpress logo from admin bar
function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);

// Show post thumbnail in admin columns
// For posts
add_filter('manage_posts_columns', 'posts_columns', 5);
add_action('manage_posts_custom_column', 'posts_custom_columns', 5, 2);

// For pages
add_filter('manage_pages_columns', 'posts_columns', 5);
add_action('manage_pages_custom_column', 'posts_custom_columns', 5, 2);

function posts_columns($defaults){
    $defaults['column-post_thumbs'] = __('Thumb');
    return $defaults;
}
function posts_custom_columns($column_name, $id){

  if( $column_name === 'column-post_thumbs' && get_the_post_thumbnail($id) ) {
      echo '<div class="column_post_thumb_holder">'.get_the_post_thumbnail( $id, 'thumbnail' ).'</div>';
  } else {
  	echo '<div class="no-thumb"><span>x</span></div>';
  }

}