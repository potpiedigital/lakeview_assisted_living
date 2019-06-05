<!DOCTYPE html>

<html <?php language_attributes(); ?> xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://ogp.me/ns#">

<head>

	<meta charset="<?php bloginfo( 'charset' ); // lets you change the charset from within wp, defaults to UTF8 ?>" />
  <link href="https://fonts.googleapis.com/css?family=Crimson+Text|IBM+Plex+Sans" rel="stylesheet">
	<!--Forces latest IE rendering engine & chrome frame-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<!-- title & meta handled by the yoast plugin, don't add your own here just activate the plugin -->

	<title><?php wp_title(''); ?></title>

	<!-- favicon & other link Tags -->
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="apple-touch-icon" href="/images/custom_icon.png"/><!-- Use iconifier.net to get the full pull -->
	<link rel="copyright" href="#copyright" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/css/swiper.min.css">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
  <script src="https://unpkg.com/headroom.js"><script>

	<?php wp_head();  ?>


</head>

<body <?php body_class(); ?>>

		<header class="header" id="site-header" role="banner">
      <div class="main-navigation">
        <div class="logo">
          <a href="<?php echo home_url(); ?>"><img src="<?php echo get_template_directory_uri();?>/assets/img/logo.svg" /></a>
        </div>
        <nav class="main-nav" id="main-nav" role="navigation">
          <?php wp_nav_menu('menu=mainNav'); // create the mainNav menu inside Appearance menus and go to town -- for more on menus see: https://developer.wordpress.org/reference/functions/wp_nav_menu/ ?>
        </nav>
        <nav role='navigation' class='hambuger-menu' >
						<div id="menuToggle">
							<input type="checkbox" />
							<span></span>
							<span></span>
							<span></span>
							<ul id="menu">
							<?php wp_nav_menu('menu=mainNav'); // create the mainNav menu inside Appearance menus and go to town -- for more on menus see: https://developer.wordpress.org/reference/functions/wp_nav_menu/ ?>
							</ul>
						</div>
					</nav>
      </div>
			<!-- depending on what the mock calls for text, image, svg you can handle this differently images should get alt text that matches the page title svgs should get visually hidden elements we can look into adding title to the anchor tag and see if that gets pulled in for SEO -->
			<?php if ( is_front_page() ) { ?>
				<h1 id="site-title"><a href="/"><?php bloginfo('name'); ?></a></h1>
			<?php } else { ?>
				<div id="site-title"><a href="/"><?php bloginfo('name'); ?></a></div>
			<?php } ?>

			<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>

		</header>


