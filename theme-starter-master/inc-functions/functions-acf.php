<?php

//ACF Flexible Content Function (helps prevent nightmare conditionals)  
function flexible_content( $flex_field = 'home', $directory = 'layouts', $prefix = 'acf' ) {

  // Manage all the page sections
  //  $sections = get_field('general_sections');
  //  print_r($sections);

  global $evenOdd, $flexItem; // adds odd even classes to flex items - useful incase we need to style certain blocks based on their location. 

  if( have_rows($flex_field) ):

    $flexItem = 0;

    while ( have_rows($flex_field) ) : the_row();

      $flexItem++;

      $evenOdd = "odd";

      if($flexItem%2 == 0) {
          $evenOdd = "even";
      }

      $layout = get_row_layout();
      get_template_part($directory."/".$prefix, $layout);

    endwhile;

  endif;

}

//Adds options for ACF
if( function_exists('acf_add_options_page') ) {
	
 acf_add_options_page('Footer Settings');
 acf_add_options_page('Navigation');
	
}

?>