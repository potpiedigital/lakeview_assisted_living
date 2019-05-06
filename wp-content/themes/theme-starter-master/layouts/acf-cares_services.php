<section>
<?php
		// check if the repeater field has rows of data
		if( have_rows('cares') ):
        // display a sub field value
	?>
	<div class="service-types">
		<?php // loop through the rows of data
			while ( have_rows('cares') ) : the_row();
    ?>
    <div clas="single-care">
    <div class="overview">
		  <h4><?php the_sub_field('title'); ?></h4>
      <p><?php the_sub_field('description'); ?></p>
    </div>
    <?php the_sub_field('list_items'); ?>
    </div>
		<?php endwhile; ?>
	</div>
	<?php
		else :
    	// no rows found
		endif;
	?>
</section>
