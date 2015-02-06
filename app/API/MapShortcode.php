<?php namespace SimpleLocator\API;

use SimpleLocator\Repositories\PostRepository;

/**
* Shortcode for displaying a single location map
*/
class MapShortcode {

	/**
	* Shortcode Options
	*/
	public $options;

	/**
	* Shortcode Options
	*/
	private $location_data;

	/**
	* Location Post Type
	*/
	private $post_type;

	/**
	* Post Repository
	*/
	private $post_repo;


	public function __construct()
	{
		$this->post_repo = new PostRepository;
		add_shortcode('wp_simple_locator_map', array($this, 'renderView'));
	}

	/**
	* Post Type
	*/
	private function setPostType()
	{
		$this->post_type = get_option('wpsl_post_type');
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
		$this->location_data['additionalfields'] = $this->options['additionalfields'];
	}

	/**
	* Enqueue the single view script & add localized data
	*/
	private function enqueueScript()
	{
		if ( (isset($this->location_data['latitude'])) && (isset($this->location_data['longitude'])) ){
			wp_enqueue_script(
				'simple-locator-single', 
				\SimpleLocator\Helpers::plugin_url(). '/assets/js/simple-locator-single.js', 
				array('jquery'), 
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
		$this->setPostType();
		$this->setOptions($options);
		$this->setLocationData();
		$this->enqueueScript();

		include ( \SimpleLocator\Helpers::view('singular-post') );
		return $out;
	}

}