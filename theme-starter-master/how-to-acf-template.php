<?php
/*
Template Name: template-acf
*/
?>

<?php get_header(); ?>

<!--BEGIN: content div-->
<main>

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<!-- 
			
			Core content on page.php or singlephp technically has the standard wordpress wysiwig available unless you turn it off using Advanced Custom Fields.
			If the theme is full pull flexible content fields I would turn it off. Alternatives (depending if a plugin has to rely on it or other exceptions) would
			be to include above or below the rest of the flexible content fields. You could also create a dummy acf flexible content field that has no content, but chunks
			in the content template part in the correct folow. Only draw back is that in the order of the fields - the wysiwig is either above or below them and you 
			can't reference the actual content in the flow.

		-->

		<!-- This gives you the standard -->
		<?php flexible_content('sections'); ?>

		<!-- 

		This enables you to create a seperate folder and naming convention 
		directoryName references a folder in the theme 
		flexibleContentName and sections references the top level flexible content group name
		php files used in the layouts or new folders created should all get prefixed with acf- for example: acf-hero-block.php
		if you look in functions.php you will see this: flexible_content( $flex_field = 'home', $directory = 'layouts', $prefix = 'acf' ) {

		flexible_content('flexibleContentName', 'directoryName');
		
		-->

	<?php endwhile; endif; ?>

</main>
<!--END: Content-->

<?php get_footer(); ?>