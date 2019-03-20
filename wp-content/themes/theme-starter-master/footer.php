<!--BEGIN: Footer Section-->
<footer>

	<!--BEGIN: Footer Nav-->
	<nav>
		<ul>
			<?php wp_nav_menu('menu=footerNav'); // create the footerNav menu inside Appearance menus and go to town -- for more on menus see: http://templatic.com/news/wordpress-3-0-menu-management ?>
		</ul>
	</nav>
	<!--END: Footer Nav-->
	
	<p><small>&copy; <?php echo date('Y'); ?> <?php bloginfo('name')?></small></p>
	
</footer>
<!--END: Footer Section-->

<!-- wp_footer hook for Plugins -->
<?php wp_footer(); ?>
<!-- CSS -->

</body>
</html>