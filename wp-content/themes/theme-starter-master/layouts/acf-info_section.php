<section>
	<?php
		// check if the repeater field has rows of data
		if( have_rows('info_blocks') ):
        // display a sub field value
	?>
	<div>
		<?php // loop through the rows of data
			while ( have_rows('info_blocks') ) : the_row();
		?>
		<a class="information" href="<?php the_sub_field('info_link') ?>">
			<div class="info-background" style="background-image: url(<?php the_sub_field('info_image'); ?>"></div>
			<h2><?php the_sub_field('info_title'); ?></h2>
		</a>
		<?php endwhile; ?>

	</div>
	<?php
		else :
    	// no rows found
		endif;
	?>
</section>
