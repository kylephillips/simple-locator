<?php namespace SimpleLocator\API;
/**
* Shortcode for displaying a single location map
*/
class ShortcodeMap {

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


	public function __construct()
	{
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
		$this->location_data['additionalfields'] = $this->options['additionalfields'];
		$this->location_data['title'] = get_the_title($this->options['post']);
		$this->location_data['latitude'] = get_post_meta( $this->options['post'], get_option('wpsl_lat_field'), true );
		$this->location_data['longitude'] = get_post_meta( $this->options['post'], get_option('wpsl_lng_field'), true );
		if ( $this->post_type == 'location' ){
			$this->location_data['address'] = get_post_meta( $this->options['post'], 'wpsl_address', true);
			$this->location_data['city'] = get_post_meta( $this->options['post'], 'wpsl_city', true);
			$this->location_data['state'] = get_post_meta( $this->options['post'], 'wpsl_state', true);
			$this->location_data['zip'] = get_post_meta( $this->options['post'], 'wpsl_zip', true);
			$this->location_data['phone'] = get_post_meta( $this->options['post'], 'wpsl_phone', true);
			$this->location_data['website'] = get_post_meta( $this->options['post'], 'wpsl_website', true);
			$this->location_data['additionalinfo'] = get_post_meta( $this->options['post'], 'wpsl_additionalinfo', true);
		}
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