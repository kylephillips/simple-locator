<div class="simple-locator-import-tab">
	<?php 
	$apikey = get_option('wpsl_google_geocode_api_key');

	// Require PHP v5.4+
	if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) :
		echo '<div class="error"><p>' . __('Importing functionality requires PHP version 5.4 or higher.', 'wpsimplelocator') . '<a href="http://csv.thephpleague.com/installation/" target="_blank">' . __('Read more about the PHP package used to parse CSV files.', 'wpsimplelocator') . '</a> <a href="http://docs.guzzlephp.org/en/latest/overview.html" target="_blank">' . __('Read more about the PHP package used for API connections.','wpsimplelocator') . '</a></p></div>';
	elseif ( !$apikey ) :
		echo '<div class="error"></p>' . __('Import functionality requires a Google Maps Server API key. The key must have the Geocoding API enabled, and your server\'s IP address must be whitelisted in the Google Developer Console. To enter an API key, visit the Simple Locator general settings tab.', 'wpsimplelocator') . '</p></div>';
	else :
		// Steps
		if ( isset($_GET['step']) && in_array($_GET['step'], array('1', '2', '3')) ) {
			include 'import-' . $_GET['step'] . '.php';
		} else {
			include 'import-1.php';
		}
	endif;
	?>
</div><!-- .simple-locator-import-tab" -->