<section>
	<?php
		// check if the repeater field has rows of data
		if( have_rows('services') ):
        // display a sub field value
	?>
	<div>
		<?php // loop through the rows of data
			while ( have_rows('services') ) : the_row();
		?>
    <h3><?php the_sub_field('service_title'); ?></h3>
    <p><?php the_sub_field('service_description'); ?></p>
    <a href="<?php the_sub_field('service_link') ?>"><?php the_sub_field('service_link_text'); ?></a>
		<?php endwhile; ?>

	</div>
	<?php
		else :
    	// no rows found
		endif;
	?>
</section>
