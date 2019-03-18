<?php 
  $image = get_sub_field('full_image');
  $image_format = get_sub_field('image_format');
  $remove_padding = get_sub_field('remove_padding');
?>

<section class="product-big-feat product-big-feat-<?php echo $image_format; ?> <?php echo $remove_padding; ?>">
  
    <!-- You can use this method for responsive images where you need to remove the width and height attributes -->
    <?php
      $image  =  $image['id'];
      $src    = wp_get_attachment_image_src( $image, 'small' );
      $srcset = wp_get_attachment_image_srcset( $image, 'full' );
      $sizes  = wp_get_attachment_image_sizes( $image, 'full' );
      $alt    = get_post_meta( $image, '_wp_attachment_image_alt', true); 
    ?>

    <img 
      src="<?php echo esc_attr( $src[0] );?>"
      srcset="<?php echo esc_attr( $srcset ); ?>"
      sizes="<?php echo esc_attr( $sizes );?>"
      alt="<?php echo esc_attr( $alt );?>"
    />

    <!-- This way works as well and works great for most line images -->

    <?php echo wp_get_attachment_image( $image['id'], 'full', "", ["class" => "swap"] ); ?>
    
</section><!-- end .product-big-feat -->
