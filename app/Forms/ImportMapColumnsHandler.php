<?php namespace SimpleLocator\Forms;

use SimpleLocator\Import\SaveColumnMap;
use SimpleLocator\Import\Import;

/**
* Saves the column mapping for imports
*/
class ImportMapColumnsHandler {

	/**
	* Column Saving Class
	*/
	private $column_map;

	public function __construct()
	{
		$this->column_map = new SaveColumnMap;
		$this->save();
	}

	private function save()
	{
		$this->column_map->save();
		$this->success();
	}

	/**
	* Redirect to step 2 on success
	*/
	private function success()
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&step=3');
		return header('Location:' . $url);
	}

}