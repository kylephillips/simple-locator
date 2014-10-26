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
	* Get all custom fields associated with a post type
	* @param string post_type
	*/
	private function getFieldsForPostType($post_type)
	{
		global $wpdb;
		$post_table = $wpdb->prefix . 'posts';
		$sql = "SELECT DISTINCT meta_key FROM wp_posts AS p LEFT JOIN wp_postmeta AS m ON m.post_id = p.id WHERE p.post_type = '$post_type' AND meta_key NOT LIKE '\_%'";
		$results = $wpdb->get_results($sql);
		
		$exclude = array('_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time', 'layout', 'position', 'rule', 'hide_on_screen');
		
		foreach ( $results as $field )
		{
			if ( !in_array($field->meta_key, $exclude) ) echo $field->meta_key . '<br>';
		}
	}


	/**
	* Get ACF Fields
	* @return array
	*/
	private function get_acf_fields()
	{
		if ( function_exists('get_field_objects') ){

			global $wpdb;
			$meta_table = $wpdb->prefix . 'postmeta';
			$sql = "SELECT meta_value FROM $meta_table WHERE `meta_key` LIKE '%field_%'";
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

		// Fields to exclude from returned array (Built in WP fields, Simple Locator Fields)
		$exclude_fields = array('_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time', 'layout', 'position', 'rule', 'hide_on_screen', 'wpsl_latitude', 'wpsl_longitude', 'wpsl_additionalinfo', 'wpsl_address', 'wpsl_city', 'wpsl_state', 'wpsl_phone', 'wpsl_zip', 'wpsl_zip', 'wpsl_website');

		// Add the ACFs to the excluded fields
		$acfs = $this->get_acf_fields();
		if ( $acfs ){
			foreach ( $acfs as $acf ){ array_push($exclude_fields, $acf['name']); }
		}
		
		// Add the fields to the output array
		$output = "";
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
	* @return html
	*/
	private function show_field_options($field = null)
	{
		$cfs = $this->get_custom_fields();
		$current = get_option($field);
		$out = "";

		/**
		* Optgroup for Advanced Custom Fields
		*/
		if ( function_exists('get_field_objects') ){
			$acfs = $this->show_acf_fields();
			$out .= '<optgroup label="Advanced Custom Fields">';
			foreach($acfs as $acf){
				$out .= '<option value="' . $acf['value'] . '"';
				if ( $current == $acf['value'] ) $out .= ' selected';
				$out .= '>' . $acf['label'] . '</option>';
			}
			$out .= '</optgroup>';
		}

		/**
		* Optgroup for other custom fields
		*/
		if ( $cfs ){
			$out .= '<optgroup label="Custom Fields">';
			foreach($cfs as $cf){
				$out .= '<option value="' . $cf['value'] . '"';
				if ( $current == $cf['value'] ) $out .= ' selected';
				$out .= '>' . $cf['label'] . '</option>';
			}
			$out .= '</optgroup>';
		}

		return $out;

	}


}