<?php
/*
Template Name: apply
*/
?>

<?php get_header(); ?>

<main>
  <div class="hero-image" style="background-image: url(<?php the_field('apply_hero'); ?>"></div>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<div class="applications">
    <?php if(get_field('available_jobs')): ?>
    <ul class="job-details">
      <?php while(has_sub_field('available_jobs')): ?>
      <li><h4><?php the_sub_field('job_title'); ?></h4><?php the_sub_field('short_job_description'); ?></li>
      <?php endwhile; ?>
    </ul>
    <?php endif; ?>
    <div class="apply-submit">
      <?php the_field('jobs_form'); ?>
    </div>
	</div>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
