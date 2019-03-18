<?php get_header(); ?>

<!--BEGIN: Content-->
<main>

		<h1>404</h1>
	
		<p style="margin-top: 1em;">The URL you've come to doesn't exist...<br />  If it's an error with our site <a href="/contact/">please tell us about it</a>, if not use the searchbox below to find what you're looking for.</p>
		<?php get_search_form(); ?>
	
		<h2>Or Choose A Popular Topic</h2>
		<p><?php wp_tag_cloud(''); ?> </p>

</main>
<!--END: Content-->
	
<?php get_footer(); ?>