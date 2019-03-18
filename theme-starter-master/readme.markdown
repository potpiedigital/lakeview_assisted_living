HTML5 Wordpress Shell Seagulls
------------------------------

NOTE: Parts of this boilerplate makes use of these two frameworks.

[Mimoymima version]: https://github.com/condensed/html5-responsive-wordpress-shell-SASS
[Underscore version]: https://underscores.me/

The theme is used by Elegant Seagulls to rollout more uniform wordpress sites.

**IMPORTANT**
  - We need a solid method for handling srcset with background images.  
  - Superior SCSS is getting added shortly.
  - Vertical Rhythm & Defauly WYSIWYG discussion next dev meeting
  - The new wordpress plugin / rollout, as well as the updated ACF plugin may change some of this significantly lets not neglect it
  - Someone should do some best practices on custom plugins - Brian and Dan
  - Let's get our SVG icon solution in here too! 
  - Setup deploy bot?

**Live theme for reference**
  - This theme is installed on the dev:
  - Login Details: Seagull
  - Seagulls!2018!
  - URL: http://elegantseagullsdev.com/client/wordpress-starter/

**Plugins: Must Install**
  - ACF Pro (ask Chris for key if you don't have it) https://www.advancedcustomfields.com/resources/
  - Yoast https://yoast.com/wordpress/plugins/seo/
  - ACF Content Analysis for Yoast https://wordpress.org/plugins/acf-content-analysis-for-yoast-seo/
  - Site speed and other utlities: https://wordpress.org/plugins/w3-total-cache/ *recommend installing this at the end*

**Plugins: AMP**
  - AMP developed by Automatic Team: https://wordpress.org/plugins/amp/
  - Makes Yoast Play nice with AMP https://wordpress.org/plugins/glue-for-yoast-seo-amp/

**Plugins: Keep on your radar**
  - Jetpack extends wordpress functionality in a couple of different ways. It's pretty heavy, but it is the best way to get infinite scroll working on themes. https://jetpack.com/
  - Multi Language support: https://wpml.org/

**Best Practices**

  - index.php should not be turned into a custom page template for the homepage unless the client plans on pulling in their full blog roll on the homepage. If you need a custom homepage it's recommended you use home.php which is used by default if it exists in the directory. You can also use front-page.php. Keep in mind index.php is used to display a list of posts in excerpt or full-length form. Look / interact here for more info: https://developer.wordpress.org/themes/basics/template-hierarchy/
	- kitchen-sink-template.php shows additional methods for pulling in things like sidebars, how to create custom page templates, executing a wp-query to pull in posts vs. the standard loop and more.
	- how-to-acf.php shows some methods for setting up flexibile content modules using the functions file.
  - You can debug wordpress with this method: define( 'WP_DEBUG', true ); inside of wp-config.

**ACF Field Creation**
  - Think like you are the client updating content
  - Condense flexible sections using smart conditional fields
  - Try to make sure every field has a unqiue field
  - try not to use the duplicate feature ACF provides.

**Theme Folder Structure**

  - admin contains the stylesheet and potential images used when styling the wordpress login dashboard for the client
  - assets contains images, javascript, styles, gulp, etc.
  - functions-inc contains additional function files and can additional contain any additional theme functionality that relies mainly on php
  - layouts contains flexible content modules used by ACF
  - modules can contain any content modules outside of ACF --> for example sections like newsletters that get pulled in on specific pages only.
  - template-parts should contain pieces of content that is repeated through wordpress core 

**Additional Notes**

  - Comments.php is removed. See functions.php for other options if comments are a most have. IE. Disqus and others.
  - If we are hosting the site we should put the wordpress site a directory back in a folder called admin. Then add an .htaccess file to point to it, as well as point the index.php file to it. 
  - If a theme needs localization refer to this boilerplate theme on implementing: https://underscores.me/
  - Avatars are tricky - Gravitar is the best solution. 
  - Gutenburg new functionality is added via plugin on the dev starter template for you reference