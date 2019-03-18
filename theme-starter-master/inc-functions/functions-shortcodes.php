<?php

//This adds a line inside of the wysiwig editor [line]
function line() {
  return '<div class="hlineFull bgLightGray"></div>';
}
add_shortcode('line', 'line');

//This adds a line inside of the wysiwig editor [button link="google.com" ]Button Text[/button]
function button($atts, $content = null) {
   extract(shortcode_atts(array('link' => '#'), $atts));
   return '<a href="'.$link.'">' . do_shortcode($content) . '</a>';
}
add_shortcode('button', 'button');

//This wraps content in a div with a background color and text color set via the short code [colorbox backgroundcolor="#000000" textcolor="#ffffff" ]Wrap me.[/colorbox]
function colorbox($atts, $content = null) {
 extract( shortcode_atts( array(
          'backgroundcolor' => '',
          'textcolor' => ''
), $atts ) );
return '<div style="background-color:'.$backgroundcolor.'; color:'.$textcolor.';">' . do_shortcode($content) . '</div>';
}
add_shortcode('colorbox', 'colorbox');

?>