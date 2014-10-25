<?php

class WPSL_Shortcode {

	/**
	* Unit of Measurement
	*/
	protected $unit;

	/**
	* Maps API Key
	*/
	protected $maps_api;


	public function __construct()
	{
		$this->setUnit();
		add_shortcode('wp_simple_locator', array($this, 'renderView'));
	}

	/**
	* Set the unit of measurement
	*/
	private function setUnit()
	{
		$this->unit = ( get_option('wpsl_measurement_unit') ) ? get_option('wpsl_measurement_unit') : 'miles';
	}

	/**
	* Enqueue the Required Scripts
	*/
	private function enqueueScripts()
	{
		wp_enqueue_script('google-maps');
		wp_enqueue_script('simple-locator');
	}

	/**
	* The View
	*/
	public function renderView()
	{	
		$this->enqueueScripts();
		include( dirname( dirname(__FILE__) ) . '/views/ajax-form.php');
		return $output;
	}

}

