<?php
/*
Template Name: apply
*/
?>

<?php get_header(); ?>

<main>


  <div class="hero-image" style="background-image: url(<?php the_field(''); ?>"></div>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
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
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
