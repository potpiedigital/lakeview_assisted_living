<?php
/*
Template Name: about
*/
?>

<?php get_header(); ?>

<main>
  <div class="hero-image" style="background-image: url(<?php the_field('about_hero'); ?>"></div>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<div>
		<?php flexible_content('about_blocks');
		?>
	</div>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
