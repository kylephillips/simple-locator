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
	public function getPostTypes($public = true)
	{
		$args = ['public' => true, 'publicly_queryable' => true ];
		if ( !$public ) $args = [];
		$types = get_post_types($args, 'objects');
		$post_types = [];
		foreach( $types as $key => $type ){ 
			if ( $type->name == 'attachment' ) continue;
			$post_types[$key]['name'] = $type->name;
			$post_types[$key]['label'] = $type->labels->name;
			$post_types[$key]['public'] = $type->public;
		}
		return $post_types;
	}

	/**
	* Get all custom fields associated with a post type
	* @param string post_type
	* @return array
	*/
	public function getFieldsForPostType($post_type, $show_hidden = false, $include_wpsl = true)
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
		if ( $include_wpsl ) $fields = $this->addSimpleLocatorMeta($fields);
		return $fields;
	}

	/**
	* Add the default location meta if location type is selected
	* @param string post type
	* @param array $fields
	*/
	private function addSimpleLocatorMeta($fields)
	{
		$sl_meta = ['wpsl_address', 'wpsl_address_two', 'wpsl_city', 'wpsl_state', 'wpsl_zip', 'wpsl_country', 'wpsl_phone', 'wpsl_website'];
		return array_unique(array_merge($fields, $sl_meta));
	}

	/**
	* Format DB results into an array
	*/
	private function fieldsArray($results)
	{
		$fields = [];
		$exclude = ['_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time', 'layout', 'position', 'rule', 'hide_on_screen'];
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
	public function displayFieldOptions($post_type, $show_hidden = false, $include_wpsl = true)
	{
		$fields = $this->getFieldsForPostType($post_type, $show_hidden, $include_wpsl);
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
		$map_fields = [];
		if ( !function_exists('acf_get_field_groups') ) return $map_fields;
		$post_type = get_option('wpsl_post_type');

		// Get the field groups for the post type
		$field_groups = acf_get_field_groups(['post_type' => $post_type]);
		foreach ( $field_groups as $group ){
			$fields = acf_get_fields($group);
			foreach($fields as $field){
				if ( $field['type'] !== 'google_map' ) continue;
				$map_fields[$field['key']] = $field['label'];
			}
		}
		return $map_fields;
	}

	/**
	* Get all the ACF tab fields for the selected post type
	*/
	public function getAcfTabFields()
	{
		if ( !function_exists('get_fields') ) return false;
		$post_type = get_option('wpsl_post_type');
		return $this->getAcfFieldsForPostType($post_type, 'tab');
	}

	/**
	* Get all the ACF fields for a post type (optionally specify a field type (ex: tab))
	*/
	public function getAcfFieldsForPostType( $post_type, $field_type = null )
  	{
  		$return_fields = [];
  		$groups = acf_get_field_groups(['post_type' => $post_type]);
  		foreach ( $groups as $group ){
  			$fields = acf_get_fields($group['ID']);
  			if ( !$fields ) continue;
  			foreach ($fields as $field){
  				if ( isset($field_type) && $field_type == $field['type'] )	 $return_fields[$field['key']] = $field['label'];
  			}
  		}
  		return $return_fields;
  	}

  	/**
  	* Get an array of standard post fields/columns
  	*/
  	public function getStandardPostColumns()
  	{
  		$standard_fields = [
			'ID' => ['name' => __('ID', 'simple-locator'), 'default' => true],
			'post_title' => ['name' => __('Title', 'simple-locator'), 'default' => true],
			'post_name' => ['name' => __('Slug', 'simple-locator'), 'default' => true],
			'post_content' => ['name' => __('Content', 'simple-locator'), 'default' => true],
			'post_date' => ['name' => __('Date', 'simple-locator'), 'default' => true],
			'post_date_gmt' => ['name' => __('Date (GMT)', 'simple-locator'), 'default' => true],
			'post_modified' => ['name' => __('Modified Date', 'simple-locator'), 'default' => true],
			'post_modified_gmt' => ['name' => __('Modified Date (GMT)', 'simple-locator'), 'default' => true],
			'post_excerpt' => ['name' => __('Excerpt', 'simple-locator'), 'default' => true],
			'post_status' => ['name' => __('Status', 'simple-locator'), 'default' => true],
			'comment_status' => ['name' => __('Comment Status', 'simple-locator'), 'default' => false],
			'post_password' => ['name' => __('Password', 'simple-locator'), 'default' => false],
			'post_password' => ['name' => __('Password', 'simple-locator'), 'default' => false],
			'post_type'	=> ['name' => __('Post Type', 'simple-locator'), 'default' => false],
			'comment_count'	=> ['name' => __('Comment Count', 'simple-locator'), 'default' => false]
		];
		return $standard_fields;
  	}
}