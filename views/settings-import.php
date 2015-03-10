<div class="simple-locator-import-tab">
	<?php 
		
		
	if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
		echo '<div class="error"><p>' . __('Importing functionality requires PHP version 5.4 or higher.', 'wpsimplelocator') . '<a href="http://csv.thephpleague.com/installation/" target="_blank">' . __('Read more about the PHP package used to parse CSV files', 'wpsimplelocator') . '</a></p></div>';
	}
	else if ( !get_option('wpsl_google_api_key') ) :
		echo '<div class="error"><p>' . __('Importing requires a Google Maps API key for geocoding addresses.', 'wpsimplelocator') . ' <a href="https://developers.google.com/maps/documentation/geocoding/#BYB" target="_blank">' . __('More Info', 'wpsimplelocator') . '</p></div>';
	else :
		// Steps
		if ( isset($_GET['step']) && in_array($_GET['step'], array('1', '2', '3')) ) {
			include 'settings-import-' . $_GET['step'] . '.php';
		} else {
			include 'settings-import-1.php';
		}
	endif;
	?>
</div><!-- .simple-locator-import-tab" -->