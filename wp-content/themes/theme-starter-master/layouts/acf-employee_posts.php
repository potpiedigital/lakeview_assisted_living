<section class="our-family">
  <h2><?php the_sub_field('employee_title'); ?></h2>
  <div>
		<?php $posts = get_sub_field('employee_listing'); if( $posts ): ?>
		<ul class="family-posts">
		<?php foreach( $posts as $post): // IMPORTANT - variable must be called $post ?>
			<?php setup_postdata($post); ?>
			<div class="employee-info">
			  <div class="family-image"><?php the_post_thumbnail(); ?></div>
				<h4><?php the_title(); ?></h4>
				<p><?php the_excerpt(); ?></p>
			</div>
		<?php endforeach; ?>
		</ul>
		<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
		<?php endif; ?>
	</div>
</section>
