<?php get_header(); ?>

<main>

	<!--BEGIN: content div-->
	<section>
		
		<?php if (have_posts()) : // BEGIN THE LOOP ?>

			<?php while (have_posts()) : the_post(); //LOOPING through all the posts, we split onto two lines for clean indentation ?>

				<article <?php post_class() ?> id="post-<?php the_ID(); ?>">

					<h1><?php the_title(); ?></h1>
					<time datetime="<?php the_time('c'); ?>" pubdate="pubdate"><?php the_time('F jS, Y'); ?></time>
					<p>by <?php the_author() ?></p>
					
					<div>
						<?php the_content(); ?>
					</div>
								
					<ul>
						<li>Posted in <?php the_category(', ') ?></li>
						<li><?php edit_post_link('[Edit]', '<small>', '</small>'); ?></li>
						<li><?php the_tags('Tags: ', ', ', '<br />'); ?></li>
					</ul>
				
				</article>

			<?php wp_link_pages(); //this allows for multi-page posts ?>
					
			<?php endwhile; //END: looping through all the posts ?>

				<!--BEGIN: Page Nav-->
				<?php if ( $wp_query->max_num_pages > 1 ) : // if there's more than one page turn on pagination ?>
			    <nav class="pagination">
			      <ul>
			        <li><?php next_posts_link('Next Page') ?></li>
			        <li><?php previous_posts_link('Previous Page') ?></li>
			      </ul>
			    </nav>
				<?php endif; ?>
				<!--END: Page Nav-->
				
		<?php else : ?>

			<h2>No posts were found :(</h2>

		<?php endif; //END: The Loop ?>
		
	</section>
	<!--END: content div-->

</main>

<?php get_footer(); ?>