<?php 

namespace SimpleLocator\API;

use SimpleLocator\Repositories\PostRepository;
use SimpleLocator\Repositories\SettingsRepository;

/**
* Shortcode for displaying a single location map
*/
class SingleLocationShortcode 
{

	/**
	* Shortcode Options
	* @var arrat
	*/
	public $options;

	/**
	* Location Data
	* @var array
	*/
	private $location_data;

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
		add_shortcode('wp_simple_locator_map', array($this, 'renderView'));
	}

	/**
	* Shortcode Options
	*/
	private function setOptions($options)
	{
		$this->options = shortcode_atts(array(
			'post' => get_the_id(),
			'additionalfields' => 'hide'
		), $options);
	}

	/**
	* Set the location data for use in map
	*/
	private function setLocationData()
	{
		$this->location_data = $this->post_repo->getLocationData($this->options['post']);
	}

	/**
	* Enqueue the single view script & add localized data
	*/
	private function enqueueScripts()
	{
		if ( (isset($this->location_data['latitude'])) && (isset($this->location_data['longitude'])) ){
			wp_enqueue_script(
				'simple-locator-single', 
				\SimpleLocator\Helpers::plugin_url(). '/assets/js/simple-locator-single.js', 
				array('jquery', 'simple-locator'), 
				'1.0'
			);
			wp_localize_script( 
				'simple-locator-single', 
				'wpsl_locator_single', 
				$this->location_data
			);
		}
	}

	/**
	* The View
	*/
	public function renderView($options)
	{	
		$this->setOptions($options);
		$this->setLocationData();
		$this->enqueueScripts();

		include ( \SimpleLocator\Helpers::view('singular-post') );
		return $out;
	}

}