<section class="family-posts">
  <h2><?php the_sub_field('employee_title'); ?></h2>
  <?php
		// check if the repeater field has rows of data
		if( have_rows('employee_list') ):
        // display a sub field value
	?>
	<div class="our-family">
		<?php // loop through the rows of data
			while ( have_rows('employee_list') ) : the_row();
    ?>
    <div class="employee-content">
      <div class="employee-img-holder">
        <img src="<?php the_sub_field('employee_image'); ?>" />
      </div>
      <div class="employee-info">
        <h4><?php the_sub_field('employee_name'); ?></h4>
        <h6><?php the_sub_field('employee_title'); ?></h6>
        <p><?php the_sub_field('employee_bio'); ?></p>
      </div>
    </div>
		<?php endwhile; ?>
	</div>
	<?php
		else :
    	// no rows found
		endif;
	?>
</section>
