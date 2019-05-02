<section>
<?php
		// check if the repeater field has rows of data
		if( have_rows('images') ):
        // display a sub field value
	?>
	<div class="swiper-container">
    <div class="swiper-wrapper">
		  <?php // loop through the rows of data
			while ( have_rows('images') ) : the_row();
      ?>
      <img class="swiper-slide" src="<?php the_sub_field('image') ?>" />
      <?php endwhile; ?>
    </div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>

  <div class="swiper-pagination"></div>
	<?php
		else :
    	// no rows found
		endif;
  ?>
</section>
