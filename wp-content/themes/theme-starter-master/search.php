<?php get_header(); ?>

<!--BEGIN: Content-->
<main>
	
	<h1>Search Results</h1>
	
	<?php
	
	// Query Posts
	
	//BEGIN: The Loop
	if (have_posts()) : while (have_posts()) : the_post();?>
	
		<div>
		<!--BEGIN: List Item-->
			<a <?php post_class('clearfix') ?> id="post-<?php the_ID(); ?>" href="<?php the_permalink() ?>" title="Click to read more...">
			
				<strong><?php the_title(); ?></strong>

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
								
				<!--BEGIN: Excerpt-->
				<p>
					<?php the_excerpt("Continue reading &rarr;"); ?>
				</p>
				<!--END: Excerpt-->
						
			</a>
		<!--END: List Item-->
		</div>	
		
		<?php endwhile; ?>

			<div>
				<?php posts_nav_link('&nbsp;','<div class="alignleft">&laquo; Previous Page</div>','<div class="alignright">Next Page &raquo;</div>') ?>
			</div>

		<?php else : // if no posts were found give the warning below ?>

		<div>
			<p>Nothing Found, there seems to be something wrong... Try searching instead:</p>
			<?php get_search_form(); ?>
		
			<h2>Topics of Interest</h2>
			<p><?php wp_tag_cloud(''); ?></p>
		</div>
		
	<?php endif; //END: The Loop ?>

</main>
<!--END: Content-->

<?php get_footer(); ?>