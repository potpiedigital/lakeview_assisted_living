<!--BEGIN: Post-->
<article <?php post_class(); ?> id="<?php the_ID(); ?>">
	
	<h2><?php the_title(); ?></h2>
	
	<time datetime="<?php the_time('c'); ?>" pubdate="pubdate"><?php the_time('F jS, Y'); ?></time>
	
	<div>
		<?php the_excerpt("Continue reading &rarr;"); ?>
	</div>

	<?php if ( has_post_thumbnail() ) : ?>
		<!-- https://martinwolf.org/blog/2016/02/how-to-edit-srcset-attribute-in-wordpress-responsive-images -->
		<!-- http://aaronrutley.com/responsive-images-in-wordpress-with-acf/ this is an easy way to handle img srcset with ACF-->

		<!-- you can use this method for full flexibility with srcset and sizes -->
		 <?php
		 		$id     = get_post_thumbnail_id();
		    $src    = wp_get_attachment_image_src( $id, 'full' );
		    $srcset = wp_get_attachment_image_srcset( $id, 'full' );
		    $sizes  = wp_get_attachment_image_sizes( $id, 'full' );
		    $alt    = get_post_meta( $id, '_wp_attachment_image_alt', true); 
		 	?>

	    <img src="<?php echo esc_attr( $src[0] );?>"
	         srcset="<?php echo esc_attr( $srcset ); ?>"
	         sizes="<?php echo esc_attr( $sizes );?>"
	         alt="<?php echo esc_attr( $alt );?>" />

	<?php endif; ?>

</article>
<!--END: Post-->