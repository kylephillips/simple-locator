<?php 
namespace SimpleLocator\Dependencies;

/**
* Register & Enqueue Styles & Scripts
*/
class PublicDependencies extends DependencyBase 
{
	public function __construct()
	{
		parent::__construct();
		add_action( 'wp_enqueue_scripts', [$this, 'styles']);
		add_action( 'wp_enqueue_scripts', [$this, 'scripts']);
	}

	/**
	* Front End Styles
	*/
	public function styles()
	{
		if ( !$this->settings_repo->includeCss() ) return;
		wp_enqueue_style(
			'simple-locator', 
			$this->plugin_dir . '/assets/css/simple-locator.css', 
			'',
			$this->version
		);
	}

	/**
	* Front End Scripts
	*/
	public function scripts()
	{
		$this->addGoogleMaps();
		$dependencies = ['jquery'];
		if ( $this->settings_repo->mapService() == 'google' && $this->settings_repo->includeMapLibrary() ) $dependencies[] = 'google-maps';
		wp_register_script(
			'simple-locator', 
			$this->plugin_dir . '/assets/js/simple-locator.min.js', 
			$dependencies, 
			$this->version, 
			true
		);
		wp_register_script(
			'simple-locator-non-ajax-results', 
			$this->plugin_dir . '/assets/js/simple-locator-non-ajax-results.js', 
			array('jquery', 'simple-locator'), 
			$this->version, 
			true
		);
		$localized_data = [
			'rest_url'				=> get_rest_url() . 'simplelocator/v2',
			'distance' 				=> __('Distance', 'simple-locator'), 
			'website' 				=> __('Website', 'simple-locator'),
			'location' 				=> __('location', 'simple-locator'),
			'locations' 			=> __('locations', 'simple-locator'),
			'found_within' 			=> __('found within', 'simple-locator'),
			'phone' 				=> __('Phone', 'simple-locator'),
			'of'					=> __('of', 'simple-locator'),
			'showonmap' 			=> __('Show on Map', 'simple-locator'),
			'viewlocation' 			=> __('View Location', 'simple-locator'),
			'notfounderror' 		=> __('Address not found', 'simple-locator'),
			'nolocationserror' 		=> __('No locations were found near', 'simple-locator'),
			'nolocationsfound' 		=> __('No locations found', 'simple-locator'),
			'alllocations'			=> apply_filters('simple_locator_all_locations_text', __('All Locations', 'simple-locator')),
			'mapservice'			=> $this->settings_repo->mapService(),
			'mappin' 				=> $this->settings_repo->mapPin(),
			'mappinuser' 			=> $this->settings_repo->mapPin('user'),
			'includeuserpin'		=> $this->settings_repo->includeUserPin(),
			'showgeobutton'			=> $this->settings_repo->geoButton('enabled'),
			'geobuttontext'			=> $this->settings_repo->geoButton('text'),
			'yourlocation' 			=> __('your location', 'simple-locator'),
			'default_enabled' 		=> $this->settings_repo->showDefaultMap(),
			'default_latitude' 		=> $this->settings_repo->defaultMap('latitude'),
			'default_longitude'		=> $this->settings_repo->defaultMap('longitude'),
			'default_zoom' 			=> intval($this->settings_repo->defaultMap('zoom')),
			'default_user_center'	=> $this->settings_repo->defaultMap('user_location'),
			'custom_map_options'	=> $this->settings_repo->customMapOptions(),
			'custom_autocomplete'	=> $this->settings_repo->customAutocompleteOptions(),
			'postfields'			=> apply_filters('simple_locator_post_fields', false),
			'jsdebug'				=> $this->settings_repo->jsDebug()
		];
		$localized_data['mapstyles'] = $this->styles_repo->getLocalizedStyles();
		
		// Localized JS objects
		$localized_objects = '';
		if ( $this->settings_repo->customMapOptions() ) 
			$localized_objects .= 'wpsl_locator.map_options = ' . apply_filters('simple_locator_js_map_options', $this->settings_repo->mapOptions()) . ';';
		if ( $this->settings_repo->customAutocompleteOptions() ) 
			$localized_objects .= 'wpsl_locator.autocomplete_options = ' . apply_filters('simple_locator_autocomplete_js_options', $this->settings_repo->autocompleteOptions());
		$localized_data['l10n_print_after'] = $localized_objects;

		wp_localize_script( 
			'simple-locator', 
			'wpsl_locator', 
			$localized_data
		);
	}
}