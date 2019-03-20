<?php

/*

    This file can get a bit unruly depending on how much you are customizing it.
    I like to do the standard wordpress functions here - wordpress nav menus, sidebars, stylesheets, scripts and utlity classes
    If you find this file getting convoluted or hard to read please consider breaking 
    it up using an inc folder. See below for some examples. 

    include 'inc-functions/functions-remove.php'; --> This enables you to customize and hide things on the wordpress admin side.
    include 'inc-functions/functions-general.php'; --> General handles any content modifications like Flexible Content, Format stripping, etc.
    include 'inc-functions/functions-header.php'; --> Used to be used for minification purposes and header cleanup. I believe we aren't on that path anymore.
    include 'inc-functions/functions-footer.php'; --> Any addition scripts that need to be included in the footer. Believe we can do this using wp_enqueue_script instead. 
    include 'inc-functions/functions-project.php'; --> Any specific non-universal project functions could go here. 
    include 'inc-functions/functions-shortcodes.php'; --> Add shortcodes in here. 
    include 'inc-functions/functions-post-types-taxonomies.php'; --> Register your custom post types and taxonomies here. 

*/

//Add the inc functions like below

// Enables us to customize the login side of wordpress. See the admin folder for the style sheet and images. 
include 'inc-functions/functions-admin.php';
include 'inc-functions/functions-acf.php';
include 'inc-functions/functions-shortcodes.php';

// ]f you are logged into the admin area. Uncomment the items inside of show_template to help debug and see what template pages are getting pulled in.
if (is_user_logged_in()) { add_action('wp_footer', 'show_template'); }

function show_template() {
  //global $template;
  //print_r($template);
}

// Add a 'first' and 'last' class to the first and last menu item pulled from custom menus
function add_first_and_last($output) {
  $search_str = 'class="menu-item';
  $search_len = strlen($search_str);

  $first_position = stripos($output, $search_str);
  if ($first_position !== false) {
    $output = substr_replace($output, 'class="menu-item first-menu-item', $first_position, $search_len);
  }

  $last_position = strripos($output, $search_str);
  if ($last_position !== false) {
    $output = substr_replace($output, 'class="menu-item last-menu-item', $last_position, $search_len);
  }

  return $output;
}
add_filter('wp_nav_menu', 'add_first_and_last');


// Removes tags generated in the WordPress Head that we don't use, you could read up and re-enable them if you think they're needed
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');

/*
if you are developing this site locally you can use wordpress' local copy of jquery by commenting out the deregister line and the line with google's version of jquery below and registering the local copy
like this:
   // wp_deregister_script( 'jquery' );
   // wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
   wp_register_script ( 'jquery' );
   wp_enqueue_script( 'jquery' );
*/

function startertemplate_all_scriptsandstyles() {
  
  // Loads jQuery from the Google CDN, loading jquery this way ensures it won't be included twice with plugins that include it

  if (!is_admin()) {
    wp_deregister_script( 'jquery' );
    wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js", null,null,true);
    wp_enqueue_script( 'jquery' );
  }

  //register and enqueue main site css - already minified using gulp
  wp_register_style( 'main', get_stylesheet_directory_uri() . '/dist/css/main.min.css', null, null, 'screen');
  wp_enqueue_style( 'main' );

  //register and enqueue main site javascript - already minified using gulp
  wp_register_script ('app', get_stylesheet_directory_uri() . '/dist/js/app.min.js', null,null,true);
  wp_enqueue_script( 'app' );
  
}
add_action( 'wp_enqueue_scripts', 'startertemplate_all_scriptsandstyles' );



// Activates menu features
  if (function_exists('add_theme_support')) {
      add_theme_support('menus');
  }

// Activates Featured Image function
  add_theme_support( 'post-thumbnails' );

// Removes the automatic paragraph tags from the excerpt, we leave it on for the content and have a custom field you can use to turn it off on a page by page basis --> wpautop = false
  remove_filter('the_excerpt', 'wpautop');

// Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

// Makes it so you can upload svgs through the Wordpress Uploader - http://css-tricks.com/snippets/wordpress/allow-svg-through-wordpress-media-uploader/
function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

// Used to create custom length excerpts
function get_the_custom_excerpt($length){
  return substr( get_the_excerpt(), 0, strrpos( substr( get_the_excerpt(), 0, $length), ' ' ) ).'...';
}

add_filter( 'max_srcset_image_width', 'remove_max_srcset_image_width' );
function remove_max_srcset_image_width( $max_width ) {
    return false;
}

/* 

//Register wigetized sidebars, changing the default output from lists to divs

function seagulls_starter_widgets_init() {
  register_sidebar( array(
    'name'          => esc_html__( 'Sidebar', 'seagulls-starter' ),
    'id'            => 'sidebar-1',
    'description'   => esc_html__( 'Add widgets here.', 'seagulls-starter' ),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ) );
}
add_action( 'widgets_init', 'seagulls_starter_widgets_init' );

*/


// This function is used to get the slug of the page
function get_the_slug() {
  global $post;
  if ( is_single() || is_page() ) {
    return $post->post_name;
  } else {
    return "";
  }
}

// Funstion used to see if you are in a post type
function is_post_type($type){
  global $wp_query;
  if($type == get_post_type($wp_query->post->ID)) return true;
  return false;
}

/*
COMMENT FUNCTIONS:
we usually use LiveFyre, Disqus, or Intense Debate for comments
also jetpack has some kind of commenting plugin that we haven't tried yet.
*/


?>
