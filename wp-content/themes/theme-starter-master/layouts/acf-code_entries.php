<section>
<?php
		// check if the repeater field has rows of data
		if( have_rows('entry_codes') ):
        // display a sub field value
	?>
	<div>
		<?php // loop through the rows of data
			while ( have_rows('entry_codes') ) : the_row();
		?>
		<?php echo do_shortcode( "<?php the_sub_field('single_code'); ?>" ) ?>
		<?php endwhile; ?>
	</div>
	<?php
		else :
    	// no rows found
		endif;
	?>
</section>
