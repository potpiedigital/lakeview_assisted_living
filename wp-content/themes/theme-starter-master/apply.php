<?php
/*
Template Name: apply
*/
?>

<?php get_header(); ?>

<main>
  <div class="hero-image" style="background-image: url(<?php the_field(''); ?>"></div>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <div>
		<?php flexible_content('application_codes');
		?>
	</div>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
