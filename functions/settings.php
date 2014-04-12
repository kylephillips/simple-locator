<?php
/**
* Settings page
*/
class WPSimpleLocatorSettings {

	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array($this, 'register_settings' ) );
	}

	/**
	* Add the admin menu item
	*/
	public function admin_menu()
	{
		add_options_page( 
			'WP Simple Locator',
			'Simple Locator',
			'manage_options',
			'wp_simple_locator', 
			array( $this, 'settings_page' ) 
		);
	}

	/**
	* Register the settings
	*/
	public function register_settings()
	{
		register_setting( 'wp-simple-locator', 'wpsl_google_api_key' );
	}

	/**
	* Add the Settings Page
	*/
	function  settings_page () {
		echo '<div class="wrap">';
		echo '<h1>WP Simple Locator Settings</h1>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'wp-simple-locator' );
		echo '<table class="form-table">
			<tr valign="top">
			<th scope="row">Google Maps API Key</th>
			<td><input type="text" name="wpsl_google_api_key" value="' . get_option('wpsl_google_api_key') . '" /></td>
			</tr>
			</table>';
		submit_button();
		echo '</form>';
		echo '</div>';
	}

}