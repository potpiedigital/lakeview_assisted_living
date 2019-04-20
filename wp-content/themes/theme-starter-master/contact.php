<?php
/*
Template Name: contact
*/
?>

<?php get_header(); ?>

<main>
  <div class="hero-image" style="background-image: url(<?php the_field('hero_image'); ?>"></div>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
