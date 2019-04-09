<?php
/*
Template Name: homepage
*/
?>

<?php get_header(); ?>

<main>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<div class="home-page-content">
		<?php flexible_content('homepage_blocks');
		?>
	</div>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
