<?php namespace SimpleLocator;

use SimpleLocator\Repositories\MapStyles;
/**
* Styles & Scripts required by Simple Locator
*/
class Dependencies {

	/**
	* Plugin Directory
	*/
	private $plugin_dir;

	/**
	* Map Styles Repository
	* @var object
	*/
	private $styles_repo;


	public function __construct()
	{
		$this->styles_repo = new MapStyles;
		$this->plugin_dir = \SimpleLocator\Helpers::plugin_url();
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
		$screen = get_current_screen();
		if ( ($screen->post_type == get_option('wpsl_post_type')) || ($screen->id == 'settings_page_wp_simple_locator') ) {
			$this->addGoogleMaps();
			wp_enqueue_script('google-maps');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script(
				'simple-locator-admin', 
				$this->plugin_dir . '/assets/js/simple-locator-admin.js', 
				array('jquery'), 
				'1.0'
			);
			wp_localize_script( 
				'simple-locator-admin', 
				'wpsl_locator', 
				array( 
					'locatorNonce' => wp_create_nonce( 'wpsl_locator-locator-nonce' )
				)
			);
		}

		// Map Style Choices
		if ( ($screen->id == 'settings_page_wp_simple_locator') && (isset($_GET['tab'])) && ($_GET['tab'] == 'map') ){
			wp_enqueue_script(
				'simple-locator-admin-maps', 
				$this->plugin_dir . '/assets/js/simple-locator-admin-maps.js', 
				array('jquery'), 
				'1.0'
			);
			wp_localize_script( 
				'simple-locator-admin-maps', 
				'wpsl_locator_mapstyles', 
				$this->mapStyleData()
			);
		}
	}


	/**
	* Front End Styles
	*/
	public function styles()
	{
		if ( get_option('wpsl_output_css') == "true" ){
			wp_enqueue_style(
				'simple-locator', 
				$this->plugin_dir . '/assets/css/simple-locator.css', 
				'', 
				'1.0'
			);
		}
	}


	/**
	* Front End Scripts
	*/
	public function scripts()
	{
		wp_enqueue_script('jquery');
		$this->addGoogleMaps();

		wp_register_script(
			'simple-locator', 
			$this->plugin_dir . '/assets/js/simple-locator.js', 
			'jquery', '1.0', 
			true
		);

		$localized_data = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'locatorNonce' => wp_create_nonce( 'wpsl_locator-locator-nonce' ),
			'distance' => __( 'Distance', 'wpsimplelocator' ), 
			'website' => __('Website', 'wpsimplelocator'),
			'location' => __('location', 'wpsimplelocator'),
			'locations' => __('locations', 'wpsimplelocator'),
			'found_within' => __('found within', 'wpsimplelocator'),
			'phone' => __('Phone', 'wpsimplelocator'),
			'showonmap' => __('Show on Map', 'wpsimplelocator'),
			'viewlocation' => __('View Location', 'wpsimplelocator'),
			'mappin' => get_option('wpsl_map_pin')
		);
		$localized_data['mapstyles'] = $this->styles_repo->getLocalizedStyles();
		wp_localize_script( 
			'simple-locator', 
			'wpsl_locator', 
			$localized_data
		);
	}


	/**
	* Get Map Style Data
	* for use in settings page display of google maps
	*/
	private function mapStyleData()
	{
		return $this->styles_repo->getAllStyles();
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