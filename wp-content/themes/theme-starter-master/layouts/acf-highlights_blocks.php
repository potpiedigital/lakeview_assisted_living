<section class="highlights">
<?php
		// check if the repeater field has rows of data
		if( have_rows('highlights') ):
        // display a sub field value
	?>

		<?php // loop through the rows of data
			while ( have_rows('highlights') ) : the_row();
    ?>
    <div class="highlights-info">
      <div class="thumbnail-container">
        <img src="<?php the_sub_field('highlight_image'); ?>" />
      </div>
      <div class="post-info">
        <h3><?php the_sub_field('highlight_title') ?></h3>
        <?php the_sub_field('highlight_copy') ?>
      </div>
    </div>
  <?php endwhile; ?>


	<?php
		else :
    	// no rows found
		endif;
	?>
</section>
