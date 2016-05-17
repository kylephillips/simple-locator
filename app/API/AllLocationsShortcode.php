<?php 

namespace SimpleLocator\API;

use \SimpleLocator\Repositories\MapStyles;
use \SimpleLocator\Repositories\SettingsRepository;
use \SimpleLocator\Repositories\PostRepository;

class AllLocationsShortcode 
{
	
	/**
	* Shortcode Options
	* @var arrat
	*/
	public $options;

	/**
	* All Locations
	*/
	private $locations;

	/**
	* Post Repository
	*/
	private $post_repo;

	/**
	* Settings Repository
	*/
	private $settings_repo;


	public function __construct()
	{
		$this->post_repo = new PostRepository;
		$this->settings_repo = new SettingsRepository;
		add_shortcode('simple_locator_all_locations', array($this, 'renderView'));
	}

	/**
	* Shortcode Options
	*/
	private function setOptions($options)
	{
		$this->options = shortcode_atts(array(
			'limit' => '-1',
		), $options);
	}

	/**
	* Get all locations
	*/
	private function getAllLocations()
	{
		$this->locations = $this->post_repo->allLocations($this->options['limit']);
		$this->formatInfoWindows();
	}

	/**
	* Apply Infowindow Formatting to locations
	*/
	private function formatInfoWindows()
	{
		foreach ($this->locations as $key => $location) :
			$infowindow = '<div data-result="' . $key . '"><h4>'. $location->title . '</h4><p><a href="' . $location->permalink . '" data-location-id="'. $location->id .'">' . __('View Location', 'wpsimplelocator') . '</a></p></div>';
			$infowindow = apply_filters('simple_locator_infowindow', $infowindow, $location, $key);
			$this->locations[$key]->infowindow = $infowindow;
		endforeach;
	}

	/**
	* Enqueue the single view script & add localized data
	*/
	private function enqueueScripts()
	{
		wp_enqueue_script('google-maps');
		wp_enqueue_script('simple-locator');
		wp_enqueue_script(
			'simple-locator-all', 
			\SimpleLocator\Helpers::plugin_url(). '/assets/js/simple-locator-all-locations.js', 
			array('jquery'), 
			'1.0'
		);
		wp_localize_script( 
			'simple-locator-all', 
			'wpsl_locator_all', 
			array(
				'locations' => $this->locations
			)
		);
	}

	/**
	* The View
	*/
	public function renderView($options)
	{	
		$this->setOptions($options);
		$this->getAllLocations();
		$this->enqueueScripts();
		echo '<div id="alllocationsmap" class="wpsl-map" style="display:block;"></div>';
	}

}