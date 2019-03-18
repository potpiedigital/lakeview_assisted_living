<?php
/*
Template Name: template-name
*/
?>

<?php get_header(); ?>

<!--BEGIN: sidebar~main-->
<aside>
	<h1>Main Sidebar</h1>
	<?php 
	//consider adding a ACF field enablign users to turn a sidebar on or off if a site requires one. See functions.php on how to enable or add multiple.
	dynamic_sidebar('sidebar-main'); 
	?>
</aside>

<!--END: sidebar~main-->

<!--BEGIN: content div-->
<main>

	<!-- ICONS: This is the way to include inline SVGs without using USE. See the icons folder for naming conventions. Must also use SVGOMG to optimize otherwise they fail. -->
	<?php get_template_part('icons/icon', 'search.svg'); ?>

	<?php
	
	// this is an example of a custom WP_Query
	// if you're just making edits to the main loop you should probably try using pre_get_posts instead: http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts   ---   also: http://developer.wordpress.com/2012/05/14/querying-posts-without-query_posts/

	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	$my_args = array(
		'category_name' => 'home', //change this to the category you want or remove it
		'posts_per_page' => '2',
		'paged' => $paged
	);

	$my_query = new WP_Query($my_args);
	?>
		
	<?php if ($my_query->have_posts()) : //BEGIN: The Loop ?>

		<h1>Posts in <?php the_category(', ') ?></h1>

		<?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
			
			<?php 
			// this is a really good way to prevent having to repeated blocks of content that share a similar markup. Articles for example or page content blocks.
			get_template_part( 'template-parts/content', 'article' ); 
			?>

			<?php wp_reset_postdata() // Reset the post data, necessary when you create a new WP_Query object ?>
				
		<?php endwhile; ?>

			<!--BEGIN: Page Nav-->
			<?php if ( $wp_query->max_num_pages > 1 ) : // if there's more than one page turn on pagination ?>
				<nav>
	        <ul>
		        <li class="next-link"><?php next_posts_link('Next Page', $my_query->max_num_pages) //important to put in the argument for the number of pages in the custom query here or else it grabs page numbers from the main wp_query ?></li>
		        <li class="prev-link"><?php previous_posts_link('Previous Page') ?></li>
	        </ul>
        </nav>
			<?php endif; ?>
			<!--END: Page Nav-->
			
		<?php else : ?>

		<h2>No posts were found :(</h2>

	<?php endif; //END: The Loop ?>

</main>
<!--END: Content-->

<?php get_footer(); ?>