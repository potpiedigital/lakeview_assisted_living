<?php get_header(); ?>

<main>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); //BEGIN: The Loop ?>

			<!--BEGIN: Post-->
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

				<h1><?php the_title(); ?></h1>
				<time datetime="<?php the_time('c'); ?>" pubdate="pubdate"><?php the_time('F jS, Y'); ?></time>
				<p>by <?php the_author() ?></p>

				<div class="entry">
          <?php the_content(); ?>
				</div>

			</div>
			<!--END: Post-->

			<?php wp_link_pages(); //this allows for multi-page posts delete if not using ?>

		<?php endwhile; ?>

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

</main>

<?php get_footer(); ?>
