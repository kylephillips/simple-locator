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
		foreach ( $fields as $key => $field ){
			$this->fields[$key] = new \StdClass();
			$this->fields[$key]->csv_column = intval($field['csv_column']);
			$this->fields[$key]->field = sanitize_text_field($field['field']);
			$this->fields[$key]->type = sanitize_text_field($field['type']);
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
		if ( isset($_POST['wpsl_first_row_header']) ){
			$transient['skip_first'] = true;
			$transient['row_count'] = $transient['row_count'] - 1;
		}
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
		$this->success('3');
	}
}