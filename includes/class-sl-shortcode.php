<?php

class WPSL_Shortcode {

	/**
	* Unit of Measurement
	*/
	protected $unit;

	/**
	* Localized strings
	*/
	protected $distance;
	protected $zip;

	/**
	* Maps API Key
	*/
	protected $maps_api;


	public function __construct()
	{
		$this->set_unit();
		$this->set_localized_strings();
		$this->set_api_key();
		add_shortcode('wp_simple_locator', array($this, 'wp_simple_locator'));
	}

	private function set_unit()
	{
		if ( get_option('wpsl_measurement_unit') ){
			$this->unit = get_option('wpsl_measurement_unit');
		} else {
			$this->unit = 'miles';
		}
	}

	private function set_localized_strings()
	{
		$this->distance = __('Distance', 'wpsimplelocator');
		$this->zip = __('Zip/Postal Code', 'wpsimplelocator');
	}

	private function set_api_key()
	{
		$this->maps_api = get_option('wpsl_google_api_key');
	}

	public function wp_simple_locator()
	{	
		include( dirname( dirname(__FILE__) ) . '/views/ajax-form.php');
		return $output;
	}

}

