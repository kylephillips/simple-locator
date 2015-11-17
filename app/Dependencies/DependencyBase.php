<?php 

namespace SimpleLocator\Dependencies;

use SimpleLocator\Repositories\MapStyles;
use SimpleLocator\Repositories\SettingsRepository;

/**
* Abstract Class for Dependencies
*/
abstract class DependencyBase 
{

	/**
	* Plugin Directory
	*/
	protected $plugin_dir;

	/**
	* Map Styles Repository
	* @var SimpleLocator\Repositories\MapStyles
	*/
	protected $styles_repo;

	/**
	* Settings Repository
	* @var SimpleLocator\Repositories\SettingsRepository
	*/
	protected $settings_repo;

	/**
	* Post Type for Locations
	* @var string
	*/
	protected $post_type;

	/**
	* Plugin Version
	* @var string
	*/
	protected $version;

	public function __construct()
	{
		$this->plugin_dir = \SimpleLocator\Helpers::plugin_url();
		$this->styles_repo = new MapStyles;
		$this->settings_repo = new SettingsRepository();
		$this->setPostType();
	}

	/**
	* Set the Post Type from Options
	* @since 1.0.6
	*/
	protected function setPostType()
	{
		$labels = get_option('wpsl_posttype_labels');
		$this->post_type = ( isset($labels['name']) ) ? $labels['name'] : 'location';
	}

	/**
	* Set the Plugin Version for dependency versioning
	*/
	protected function setVersion()
	{
		global $simple_locator_version;
		$this->version = $simple_locator_version;
	}

	/**
	* Register the Google Maps Script
	* Only Enqueue when needed
	*/
	protected function addGoogleMaps()
	{
		if ( !is_admin() && !$this->settings_repo->outputGMaps() ) return;
		if ( is_admin() && !$this->settings_repo->outputGMapsAdmin() ) return;
		$maps_url = 'https://maps.google.com/maps/api/js?';
		$maps_url .= ( get_option('wpsl_google_api_key') ) ? 'key=' . get_option('wpsl_google_api_key') . '&' : '';
		$maps_url .= '&libraries=places';
		wp_register_script(
			'google-maps', 
			$maps_url
		);
	}

	/**
	* Get Map Style Data
	* for use in settings page display of google maps
	*/
	protected function mapStyleData()
	{
		return $this->styles_repo->getAllStyles();
	}

}