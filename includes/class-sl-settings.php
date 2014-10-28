<?php
/**
* Settings page
*/
require_once('class-sl-repository-field.php');

class WPSL_Settings {

	/**
	* Selected Unit of Measurement
	*/
	private $unit;

	/**
	* Selected Field Type (Custom vs Provided)
	*/
	private $field_type;

	/**
	* Currently Selected Post Type
	*/
	private $post_type;

	/**
	* Map Options
	* @var array
	*/
	private $map_options;

	/**
	* Field Respository
	*/
	private $field_repo;
	

	public function __construct()
	{
		$this->field_repo = new WPSL_Field_Repository;
		$this->setUnit();
		$this->setFieldType();
		$this->setPostType();
		$this->setMapOptions();
		add_action( 'admin_menu', array( $this, 'registerPage' ) );
		add_action( 'admin_init', array($this, 'registerSettings' ) );
	}


	/**
	* Set the Unit
	*/
	private function setUnit()
	{
		$this->unit = get_option('wpsl_measurement_unit');
	}


	/**
	* Set the Field Type
	*/
	private function setFieldType()
	{
		$this->field_type = $field_type = get_option('wpsl_field_type');
	}


	/**
	* Set the Selected Post Type
	*/
	private function setPostType()
	{
		$this->post_type = get_option('wpsl_post_type');
	}


	/**
	* Set the Map Options
	*/
	private function setMapOptions()
	{
		$this->map_options['type'] = get_option('wpsl_map_styles_type');
	}


	/**
	* Add the admin menu item
	*/
	public function registerPage()
	{
		add_options_page( 
			'WP Simple Locator',
			'Simple Locator',
			'manage_options',
			'wp_simple_locator', 
			array( $this, 'settingsPage' ) 
		);
	}


	/**
	* Register the settings
	*/
	public function registerSettings()
	{
		register_setting( 'wpsimplelocator-general', 'wpsl_google_api_key' );
		register_setting( 'wpsimplelocator-general', 'wpsl_measurement_unit' );
		register_setting( 'wpsimplelocator-general', 'wpsl_output_css' );
		register_setting( 'wpsimplelocator-general', 'wpsl_map_pin' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_post_type' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_field_type' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_lat_field' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_lng_field' );
		register_setting( 'wpsimplelocator-map', 'wpsl_map_styles_type' );
		register_setting( 'wpsimplelocator-map', 'wpsl_map_styles' );
	}


	/**
	* Display the Settings Page
	*/
	public function settingsPage()
	{
		$tab = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		include( dirname( dirname(__FILE__) ) . '/views/settings.php');
	}	


}