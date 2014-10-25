<?php
/**
* Styles & Scripts required by Simple Locator
*/
class WPSL_Dependencies {

	/**
	* Plugin Directory
	*/
	private $plugin_dir;

	public function __construct()
	{
		$this->plugin_dir = plugins_url() . '/wp-simple-locator';
		add_action( 'admin_enqueue_scripts', array( $this, 'adminStyles' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'adminScripts' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ));
	}


	/**
	* Admin Styles
	*/
	public function adminStyles()
	{
		wp_enqueue_style(
			'simplelocator', 
			$this->plugin_dir . '/assets/css/simple-locator-admin.css', 
			array(), 
			'1.0'
		);
	}


	/**
	* Admin Scripts
	*/
	public function adminScripts()
	{
		$this->addGoogleMaps();
		wp_enqueue_script('google-maps');

		wp_enqueue_script(
			'simple-locator-admin', 
			$this->plugin_dir . '/assets/js/simple-locator-admin.js', 
			array('jquery'), 
			'1.0'
		);
	}


	/**
	* Front End Styles
	*/
	public function styles()
	{
		wp_enqueue_style(
			'simple-locator', 
			$this->plugin_dir . '/assets/css/simple-locator.css', 
			'', 
			'1.0'
		);
	}


	/**
	* Front End Scripts
	*/
	public function scripts()
	{
		$this->addGoogleMaps();

		wp_register_script(
			'simple-locator', 
			$this->plugin_dir . '/assets/js/simple-locator.js', 
			'jquery', '1.0', 
			true
		);
		wp_localize_script( 
			'simple-locator', 
			'wpsl_locator', 
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'locatorNonce' => wp_create_nonce( 'wpsl_locator-locator-nonce' ),
				'distance' => __( 'Distance', 'wpsimplelocator' ), 
				'website' => __('Website', 'wpsimplelocator'),
				'location' => __('location', 'wpsimplelocator'),
				'locations' => __('locations', 'wpsimplelocator'),
				'found_within' => __('found within', 'wpsimplelocator'),
				'phone' => __('Phone', 'wpsimplelocator')
			)
		);
	}


	/**
	* Register the Google Maps Script
	* Only Enqueue when needed
	*/
	private function addGoogleMaps()
	{
		$maps_url = 'http://maps.google.com/maps/api/js?';
		$maps_url .= ( get_option('wpsl_google_api_key') ) ? 'key=' . get_option('wpsl_google_api_key') . '&' : '';
		$maps_url .= 'sensor=false';

		wp_register_script(
			'google-maps', 
			$maps_url
		);
	}


}