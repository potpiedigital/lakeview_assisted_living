<?php get_header(); ?>

<!--BEGIN: Content-->
<main>
	
	<?php if (have_posts()) : ?>
		
		<h1>Posts in <?php single_cat_title(); ?></h1>

		<?php while (have_posts()) : the_post(); ?>

			<!--BEGIN: Post-->
			<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
				
				<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title='Click to read: "<?php strip_tags(the_title()); ?>"'><?php the_title(); ?></a></h1>
				<p><?php the_time('F jS, Y') ?> &#8212; <?php the_category(', ') ?></p>
			
				<div>
					<?php the_excerpt("Continue reading &rarr;"); ?>
				</div>
								
				<!--BEGIN: Post Meta Data-->
					<ul>
						<li><?php the_time('F jS, Y') ?> by <?php the_author(); ?></li>
						<li class="add-comment"><?php comments_popup_link('Share Your Comments', '1 Comment', '% Comments'); ?></li>
						<li><?php edit_post_link('[Edit]', '<small>', '</small>'); ?></li>
						<li><?php the_tags('Tags: ', ', ', '<br />'); ?></li>
					</ul>
				<!--END: Post Meta Data-->
			
			</article>
			<!--END: Post-->
				
		<?php endwhile; ?>

			<!--BEGIN: Page Nav-->
			<?php if ( $wp_query->max_num_pages > 1 ) : // if there's more than one page turn on pagination ?>
        <nav>
	        <ul>
		        <li class="next-link"><?php next_posts_link('Next Page') ?></li>
		        <li class="prev-link"><?php previous_posts_link('Previous Page') ?></li>
	        </ul>
        </nav>
			<?php endif; ?>
			<!--END: Page Nav-->
			
		<?php else : ?>

			<h2>No posts were found :(</h2>

	<?php endif; //END: The Loop ?>

</main>
<!--END: Content-->

<?php get_footer(); ?>