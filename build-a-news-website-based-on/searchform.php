<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'bharat-bulletin' ); ?></label>
	<input type="search" id="s" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'खबर खोजें...', 'bharat-bulletin' ); ?>">
	<button type="submit"><?php esc_html_e( 'खोजें', 'bharat-bulletin' ); ?></button>
</form>
