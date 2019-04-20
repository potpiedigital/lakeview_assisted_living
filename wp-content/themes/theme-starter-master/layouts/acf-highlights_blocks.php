<div class="full-bleed">
<div class="updates-wrapper">
	<div>
		<?php $posts = get_sub_field('highlights'); if( $posts ): ?>
		<ul class="updates-cols">
		<?php foreach( $posts as $post): // IMPORTANT - variable must be called $post ?>
			<?php setup_postdata($post); ?>
			<div>
			<div class="thumbnail-container"><?php the_post_thumbnail(); ?></div>
				<h4><?php the_title(); ?></h4>
				<p><?php the_excerpt(); ?></p>
			</div>
		<?php endforeach; ?>
		</ul>
		<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
		<?php endif; ?>
	</div>
</div>
</div>
