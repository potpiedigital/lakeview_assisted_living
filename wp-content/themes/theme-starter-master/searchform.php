<form method="get" action="<?php bloginfo('home'); ?>/">
	<fieldset>
		<label for="s">Search</label>
		<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" accesskey="s" tabindex="1" />
		<input type="submit" value="Go &gt;" tabindex="2" />
	</fieldset>
</form>