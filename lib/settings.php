<?php
/**
* Settings page
*/
class WPSimpleLocatorSettings {
	

	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array($this, 'register_settings' ) );
	}

	/**
	* Add the admin menu item
	*/
	public function admin_menu()
	{
		add_options_page( 
			'WP Simple Locator',
			'Simple Locator',
			'manage_options',
			'wp_simple_locator', 
			array( $this, 'settings_page' ) 
		);
	}


	/**
	* Register the settings
	*/
	public function register_settings()
	{
		register_setting( 'wp-simple-locator', 'wpsl_google_api_key' );
		register_setting( 'wp-simple-locator', 'wpsl_measurement_unit' );
		register_setting( 'wp-simple-locator', 'wpsl_post_type' );
		register_setting( 'wp-simple-locator', 'wpsl_field_type' );
		register_setting( 'wp-simple-locator', 'wpsl_lat_field' );
		register_setting( 'wp-simple-locator', 'wpsl_lng_field' );
	}

	/**
	* Add the Settings Page
	*/
	public function settings_page () {
		
		$unit = get_option('wpsl_measurement_unit');
		$field_type = get_option('wpsl_field_type');

		echo '<div class="wrap">';
		echo '<h1>WP Simple Locator Settings</h1>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'wp-simple-locator' );
		echo '<table class="form-table">
			<tr valign="top">
			<th scope="row">Google Maps API Key</th>
			<td><input type="text" name="wpsl_google_api_key" value="' . get_option('wpsl_google_api_key') . '" /></td>
			</tr>
			<tr valign="top">
			<th scope="row">Measurement Unit</th>
			<td>
				<select name="wpsl_measurement_unit">
					<option value="miles"';
					if ( $unit == "miles") echo ' selected';
					echo '>Miles</option>
					<option value="kilometers"';
					if ( $unit == "kilometers") echo ' selected';
					echo '>Kilometers</option>
				</select>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row">Post Type for locations</th>
			<td>
				<select name="wpsl_post_type" id="wpsl_post_type">';
				echo $this->get_post_types();
				echo '</select>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row">Latitude &amp; Longitude Fields</th>
			<td>
			<p>
				<label for="field_wpsl" class="wpsl-field-type">
					<input type="radio" name="wpsl_field_type" id="field_wpsl" value="wpsl"';
					if ( $field_type == 'wpsl' ) echo ' checked';
					echo '>Use WP Simple Locator Fields
				</label>
			</p>
			<p>
				<label for="field_custom" class="wpsl-field-type">
					<input type="radio" name="wpsl_field_type" id="field_custom" value="custom"';
					if ( $field_type == 'custom' ) echo ' checked';
					echo '>Use Other Custom Fields
				</label>
			</p>
			</td>
			</tr>
			<tr valign="top" class="latlng">
			<th scope="row">Latitude Field</th>
			<td>
			<select id="lat_select">';
				$this->show_field_options('wpsl_lat_field');
			echo '</select>
			</td>
			</tr>

			<tr valign="top" class="latlng">
			<th scope="row">Longitude Field</th>
			<td>
			<select id="lng_select">';
				$this->show_field_options('wpsl_lng_field');
			echo '</select>
			</td>
			</tr>
			</table>

			<input type="hidden" id="wpsl_lat_field" name="wpsl_lat_field"';
			if ( get_option('wpsl_lat_field') ){
				echo ' value="' . get_option('wpsl_lat_field') .  '"';
			} else {
				echo ' value="wpsl_latitude"';
			}
			echo ' />
			<input type="hidden" id="wpsl_lng_field" name="wpsl_lng_field"';
			if ( get_option('wpsl_lng_field') ){
				echo ' value="' . get_option('wpsl_lng_field') . '"';
			} else {
				echo ' value="wpsl_longitude"';
			}
			echo ' />';

		submit_button();
		echo '</form>';
		echo '</div>';
	}


	/**
	* Get all the post types
	*/
	private function get_post_types()
	{
		$types = get_post_types(array('public'=>true, 'publicly_queryable'=>true ), 'objects');
		$current = get_option('wpsl_post_type');
		$output = "";

		foreach( $types as $type ){
			$output .= '<option value="' . $type->name . '"';
			if ( $type->name == $current ){
				$output .= ' selected';
			}
			$output .= '>';
			if ( $type->name == 'location' ){
				$output .= 'Locations (Simple Locator Default)';
			} else {
				$output .= $type->labels->name;
			}
			$output .= '</option>';
		}
		return $output;
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
		$output = [];

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
					$f = [];
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