<div class="wrap">
	<h1><?php _e('Simple Locator Settings', 'wpsimplelocator'); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab <?php if ( $tab == 'general' ) echo 'nav-tab-active'; ?>" href="options-general.php?page=wp_simple_locator"><?php _e('General', 'wpsimplelocator'); ?></a>
		<a class="nav-tab <?php if ( $tab == 'posttype' ) echo 'nav-tab-active'; ?>" href="options-general.php?page=wp_simple_locator&tab=posttype"><?php _e('Post Type & Geocode Fields', 'wpsimplelocator'); ?></a>
		<a class="nav-tab <?php if ( $tab == 'map' ) echo 'nav-tab-active'; ?>" href="options-general.php?page=wp_simple_locator&tab=map"><?php _e('Map Styles', 'wpsimplelocator'); ?></a>
		<a class="nav-tab <?php if ( $tab == 'defaultmap' ) echo 'nav-tab-active'; ?>" href="options-general.php?page=wp_simple_locator&tab=defaultmap"><?php _e('Default Map', 'wpsimplelocator'); ?></a>
	</h2>

	<form method="post" enctype="multipart/form-data" action="options.php">
		<table class="form-table">
			<?php
			$view = 'settings-' . $tab . '.php';
			include($view);
			?>
		</table>
		<?php submit_button(); ?>
	</form>	

	<p class="wpsl-plugin-version"><?php _e('Simple Locator Version', 'wpsimplelocator'); echo ' ' . get_option('wpsl_version'); ?></p>
</div>