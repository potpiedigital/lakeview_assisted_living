<section>
  <img class="top-image" src="<?php the_sub_field('top_image')['value']; ?>" />
  <img class="center-image" src="<?php the_sub_field('center_image'); ?>" />
  <img class="faded-left-image" src="<?php the_sub_field('faded_left_image'); ?>" />
  <img class="faded-right-image" src="<?php the_sub_field('faded_right_image'); ?>" />
  <p><?php the_sub_field('image_copy'); ?>
  <a href="<?php the_sub_field('image_cta_link') ?>"><?php the_sub_field('image_cta_text'); ?></a>
</section>
