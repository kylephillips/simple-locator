<?php namespace SimpleLocator\Forms;

use SimpleLocator\Import\Import;

/**
* Do the Import
*/
class ImportHandler {


	public function __construct()
	{
		$this->doImport();
	}

	/**
	* Do the Import
	*/
	private function doImport()
	{
		$offset = intval(sanitize_text_field($_POST['offset']));
		$import = new Import($offset);
	}

}