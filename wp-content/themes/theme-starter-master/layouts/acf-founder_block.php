<section class="founder">
  <h2><?php the_sub_field('founder_title'); ?></h2>
  <div class="founder-content">
    <div class="founder-img">
        <img src="<?php the_sub_field('founder_image'); ?>" />
    </div>
    <div class="founder-info">
      <h4><?php the_sub_field('founder_name'); ?></h4>
      <p><?php the_sub_field('founder_message'); ?><p>
      <a class="button" href="<?php the_sub_field('founder_link') ?>"><?php the_sub_field('founder_link_text'); ?></a>
    </div>
  </div>
</section>
