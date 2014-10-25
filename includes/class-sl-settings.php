<?php
/**
* Settings page
*/
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
	

	public function __construct()
	{
		$this->setUnit();
		$this->setFieldType();
		$this->setPostType();
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
		register_setting( 'wpsimplelocator-posttype', 'wpsl_post_type' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_field_type' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_lat_field' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_lng_field' );
	}


	/**
	* Display the Settings Page
	*/
	public function settingsPage()
	{
		$tab = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		include( dirname( dirname(__FILE__) ) . '/views/settings.php');
	}


	/**
	* Get all the post types
	*/
	private function getPostTypes()
	{
		$types = get_post_types(array('public' => true, 'publicly_queryable' => true ), 'objects');
		$post_types = array();
		foreach( $types as $key => $type ){ 
			if ( $type->name == 'attachment' ) continue;
			$post_types[$key]['name'] = $type->name;
			$post_types[$key]['label'] = $type->labels->name;
			
		}
		return $post_types;
	}


	/**
	* Get ACF Fields
	*/
	private function get_acf_fields()
	{
		if ( function_exists('get_field_objects') ){

			// Get the meta fields where convention matches ACFs
			global $wpdb;
			$m = $wpdb->prefix . 'postmeta';
			$sql = "SELECT meta_value FROM $m WHERE `meta_key` LIKE '%field_%'";
			$results = $wpdb->get_results($sql);

			foreach ($results as $result){
				$acf = unserialize($result->meta_value);
				$field = array(
					'name' => $acf['name'],
					'label' => $acf['label']
				);
				$fields[] = $field;
			}
			return $fields;
		}
	}


	/**
	* Get ACF options
	* @return array
	*/
	private function show_acf_fields()
	{
		$acfs = $this->get_acf_fields();

		foreach ( $acfs as $field )
		{
			$f['value'] = $field['name'];
			$f['label'] = $field['label'];
			$output[] = $f;
		}

		return $output;
	}


	/**
	* Get Custom Fields
	* @return array of custom fields that are not ACFs
	*/
	private function get_custom_fields() 
	{
		
		// Get all the meta keys that don't match ACF conventions
		global $wpdb;
		$t = $wpdb->prefix . 'postmeta';
		$sql = "SELECT DISTINCT meta_key FROM $t WHERE meta_key NOT LIKE '%field_%' AND meta_key NOT LIKE '\_%'";
		$results = $wpdb->get_results($sql);
		foreach ($results as $result){
			$meta_keys[] = $result->meta_key;
		}

		// Fields to exclude from returned array
		$exclude_fields = array('_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time', 'layout', 'position', 'rule', 'hide_on_screen');

		// Add the ACFs to the excluded fields
		$acfs = $this->get_acf_fields();
		if ( $acfs ){
			foreach ( $acfs as $acf ){
				array_push($exclude_fields, $acf['name']);
			}
		}
		
		// Add the fields to the output array
		if (!empty($meta_keys) ):
			foreach ($meta_keys as $meta_key) {
				if ( !in_array($meta_key, $exclude_fields) ) {
					$f['value'] = $meta_key;
					$f['label'] = $meta_key;
					$output[] = $f;
				}
			}
		endif;

		return $output;
	}


	/**
	* Show the options for custom lat/lng fields
	* @param string $field
	*/
	private function show_field_options($field = null)
	{
		$cfs = $this->get_custom_fields();

		if ( function_exists('get_field_objects') ){
			// Advanced Custom Fields
			$acfs = $this->show_acf_fields();
			echo '<optgroup label="Advanced Custom Fields">';
			foreach($acfs as $acf){
				echo '<option value="' . $acf['value'] . '"';
				if ( get_option($field) == $acf['value'] ) echo ' selected';
				echo '>' . $acf['label'] . '</option>';
			}
			echo '</optgroup>';
				
			// Other fields
			echo '<optgroup label="Other Custom Fields">';
			foreach($cfs as $cf){
				echo '<option value="' . $cf['value'] . '"';
				if ( get_option($field) == $cf['value'] ) echo ' selected';
				echo '>' . $cf['label'] . '</option>';
			}

			echo '</optgroup>';
		} else {
			// ACF not installed
			foreach($cfs as $cf){
				echo '<option value="' . $cf['value'] . '"';
				if ( get_option($field) == $cf['value'] ) echo ' selected';
				echo '>' . $cf['label'] . '</option>';
			}
		}
	}


}