<?php namespace SimpleLocator\Repositories;
/**
* Field Repository Class
*/
class Fields {


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
	* @todo exclude wpsl fields
	*/
	public function getFieldsForPostType($post_type)
	{
		global $wpdb;
		$post_table = $wpdb->prefix . 'posts';
		$sql = "SELECT DISTINCT meta_key FROM wp_posts AS p LEFT JOIN wp_postmeta AS m ON m.post_id = p.id WHERE p.post_type = '$post_type' AND meta_key NOT LIKE '\_%'";
		$results = $wpdb->get_results($sql);
		$fields = ( $results ) ? $this->fieldsArray($results) : array();
		return $fields;
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
	public function displayFieldOptions($post_type)
	{
		$fields = $this->getFieldsForPostType($post_type);
		$out = '';
		foreach($fields as $field){
			$out .= '<option value="' . $field . '">' . $field . '</option>';
		}
		return $out;
	}

}