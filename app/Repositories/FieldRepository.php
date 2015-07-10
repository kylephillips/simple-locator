<?php 

namespace SimpleLocator\Repositories;

/**
* Field Repository Class
*/
class FieldRepository 
{

	/**
	* Get all the post types
	* @return array
	*/
	public function getPostTypes()
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
	* @return array
	*/
	public function getFieldsForPostType($post_type, $show_hidden = false)
	{
		global $wpdb;
		$post_table = $wpdb->prefix . 'posts';
		$meta_table = $wpdb->prefix . 'postmeta';
		if ( $show_hidden ){
			$sql = "SELECT DISTINCT meta_key FROM $post_table AS p LEFT JOIN $meta_table AS m ON m.post_id = p.id WHERE p.post_type = '$post_type' AND meta_key NOT LIKE ''";
		} else {
			$sql = "SELECT DISTINCT meta_key FROM $post_table AS p LEFT JOIN $meta_table AS m ON m.post_id = p.id WHERE p.post_type = '$post_type' AND meta_key NOT LIKE '\_%'";
		}
		$results = $wpdb->get_results($sql);
		$fields = ( $results ) ? $this->fieldsArray($results) : array();
		$fields = $this->addSimpleLocatorMeta($post_type, $fields);
		return $fields;
	}

	/**
	* Add the default location meta if location type is selected
	* @param string post type
	* @param array $fields
	*/
	private function addSimpleLocatorMeta($post_type, $fields)
	{
		$location_type = get_option('wpsl_post_type');
		if ( $post_type !== $location_type ) return $fields;
		$sl_meta = array(
			'wpsl_address', 'wpsl_address_two', 'wpsl_city', 'wpsl_state', 'wpsl_zip', 'wpsl_country', 'wpsl_phone', 'wpsl_website'
		);
		return array_unique(array_merge($fields, $sl_meta));
	}

	/**
	* Format DB results into an array
	*/
	private function fieldsArray($results)
	{
		$fields = array();
		$exclude = array('_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time', 'layout', 'position', 'rule', 'hide_on_screen');
		foreach ( $results as $field ){
			if ( !in_array($field->meta_key, $exclude) ) 
				array_push($fields, $field->meta_key);
		}
		return $fields;
	}

	/**
	* Display field options
	* @param string post_type
	* @return html
	*/
	public function displayFieldOptions($post_type, $show_hidden = false)
	{
		$fields = $this->getFieldsForPostType($post_type, $show_hidden);
		$out = '';
		foreach($fields as $field){
			$out .= '<option value="' . $field . '">' . $field . '</option>';
		}
		return $out;
	}

	/**
	* Get all the ACF Map fields for the selected post type
	* @return array
	* @since 1.2.2
	*/
	public function getAcfMapFields()
	{
		$map_fields = array();
		if ( !function_exists('acf_get_field_groups') ) return $map_fields;
		$post_type = get_option('wpsl_post_type');

		// Get the field groups for the post type
		$field_groups = acf_get_field_groups(array('post_type' => $post_type));
		foreach ( $field_groups as $group ){
			$fields = acf_get_fields($group);
			foreach($fields as $field){
				if ( $field['type'] !== 'google_map' ) continue;
				$map_fields[$field['key']] = $field['label'];
			}
		}
		return $map_fields;
	}

}