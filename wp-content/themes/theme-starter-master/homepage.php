<?php
/*
Template Name: homepage
*/
?>

<?php get_header(); ?>

<main>
<div class="hero-image" style="background-image: url(<?php the_field('hero_image'); ?>"></div>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<div class="home-page-content">
		<?php flexible_content('homepage_blocks');
		?>
	</div>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
