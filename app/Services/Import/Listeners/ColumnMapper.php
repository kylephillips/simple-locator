<?php 
namespace SimpleLocator\Services\Import\Listeners;

/**
* Saves the column mapping for imports
*/
class ColumnMapper extends ImportListenerBase 
{

	/**
	* Fields
	* @var array
	*/
	private $fields;

	public function __construct()
	{
		parent::__construct();
		$this->setFields();
		$this->saveTransient();
	}

	/**
	* Sanitize and set the fields
	*/
	private function setFields()
	{
		$fields = $_POST['wpsl_import_field'];
		$post_fields = ['title', 'content', 'excerpt', 'status', 'publish_date', 'publish_date_gmt', 'modified_date', 'modified_date_gmt', 'slug'];
		foreach ( $fields as $key => $field ){
			$this->fields[$key] = new \StdClass();
			$this->fields[$key]->csv_column = intval($field['csv_column']);
			$this->fields[$key]->field = sanitize_text_field($field['field']);
			$this->fields[$key]->type = sanitize_text_field($field['type']);
			$this->fields[$key]->unique = ( isset($field['unique']) && $field['unique'] == 1 ) ? true : false;
			$field_type = 'post_meta';
			if ( in_array($field['field'], $post_fields) ) $field_type = 'post_field';
			if ( strpos($field['field'], 'taxonomy') !== false ) $field_type = 'taxonomy';
			$this->fields[$key]->field_type = $field_type;
		}
	}

	/**
	* Save the Map to the Transient
	*/
	private function saveTransient()
	{
		$transient = get_transient('wpsl_import_file');
		$transient['columns'] = $this->fields;
		$transient['import_status'] = ( isset($_POST['wpsl_import_status']) && $_POST['wpsl_import_status'] == 'draft' ) ? 'draft' : 'publish';
		$transient['skip_first'] = false;
		$transient['skip_geocode'] = ( isset($_POST['wpsl_skip_geocoding']) && $_POST['wpsl_skip_geocoding'] == '1' ) ? true : false;
		if ( isset($_POST['wpsl_first_row_header']) ){
			$transient['skip_first'] = true;
			$transient['row_count'] = $transient['row_count'] - 1;
		}
		$transient['taxonomy_separator'] = sanitize_text_field($_POST['wpsl_import_taxonomy_separator']);
		$transient['duplicate_handling'] = sanitize_text_field($_POST['wpsl_import_duplicate_handling']);
		$transient['missing_handling'] = sanitize_text_field($_POST['wpsl_import_missing_handling']);
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
		$this->success('3');
	}
}