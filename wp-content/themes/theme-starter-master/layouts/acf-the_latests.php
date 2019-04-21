<section>
  <div class="updates-wrapper">
    <h2><?php the_sub_field('updates_title'); ?></h2>
    <div>
      <?php $posts = get_sub_field('updates_posts'); if( $posts ): ?>
      <ul class="updates-cols">
      <?php foreach( $posts as $post): // IMPORTANT - variable must be called $post ?>
        <?php setup_postdata($post); ?>
        <div class="updates-col updates">
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
</section>
