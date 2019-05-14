<!--BEGIN: Footer Section-->
<footer>
	<!--BEGIN: Footer Nav-->
	<nav class="footer-nav">
		<ul>
			<?php wp_nav_menu('menu=footerNav'); ?>
		</ul>
	</nav>
  <!--END: Footer Nav-->
  <div class="footer-contact">
    <a href="<?php echo home_url(); ?>"><img src="<?php echo get_template_directory_uri();?>/assets/img/footer-logo.png" /></a>
    <div>
      <a href="#"><img src="<?php echo get_template_directory_uri();?>/assets/img/facebook.png" /></a>
      <a href="#"><img src="<?php echo get_template_directory_uri();?>/assets/img/instagram.png" /></a>
    </div>
    <div class="personal-contact">
      <a href="mailto:dmiron@lakeviewassistedliving.com">Email: dmiron@lakeviewassistedliving.com</a>
      <div>
        <a href="tel:906-428-7000">Phone: (906)428-7000</a>
        <br>
        <a href="fax:906-428-7003">Fax: (906)428-7003</a>
      </div>
    </div>
  </div>
  <hr>
  <div class="sub-footer">
    <p><small>&copy; <?php echo date('Y'); ?> <?php bloginfo('name')?> All Rights Reserved</small></p>
    <p>Website Design: <a href="https://potpie.digital/">Pot Pie Digital, LLC</a></p>
  </div>
</footer>
<!--END: Footer Section-->

<!-- wp_footer hook for Plugins -->
<?php wp_footer(); ?>
<!-- CSS -->

</body>
</html>
