<?php namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Services\Import\CSVImport;

/**
* Do the Import
*/
class ImportHandler {

	/**
	* Importer Class
	*/
	private $importer;

	public function __construct()
	{
		$this->importer = new CSVImport;
		$this->doImport();
	}

	/**
	* Do the Import
	*/
	private function doImport()
	{
		$offset = intval(sanitize_text_field($_POST['offset']));
		$this->importer->doImport($offset);
	}

}