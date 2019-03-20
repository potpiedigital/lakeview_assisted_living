<?php get_header(); ?>

<main>
	
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
	<!--BEGIN: Single Post-->
	<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
				
		<h1><?php the_title(); ?></h1>	
		<time datetime="<?php the_time('c'); ?>" pubdate="pubdate"><?php the_time('F jS, Y'); ?></time>
		<p>by <?php the_author(); ?></p>
		
		<div>
			<?php the_content(); ?>
			<?php wp_link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
		</div>
		
		<!--BEGIN: Post Meta Data-->
		<div><?php the_tags('Tags: ', ', ', '<br />'); ?></div>
		<!--END: Post Meta Data-->
			
	</article>
	<!--END: Single Post-->

	<?php wp_link_pages(); //this allows for multi-page posts ?>

<?php endwhile; ?>

<?php else: //ERROR: Nothing Found ?>

	<h2>No posts were found :(</h2>
	
<?php endif; //END: The Loop ?>
		
</main>
<!--END: Content-->

<?php get_footer(); ?>