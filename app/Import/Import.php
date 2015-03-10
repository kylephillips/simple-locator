<?php namespace SimpleLocator\Import;

use SimpleLocator\Import\ImportRow;
use League\Csv\Reader;

/**
* Primary Import Class
*/
class Import {

	/**
	* Transient
	*/
	private $transient;

	/**
	* Row Offset
	* @var int
	*/
	private $offset;

	/**
	* Failed Imports
	*/
	private $failed_imports = 0;

	public function __construct($offset)
	{
		$this->offset = $offset;
		$this->getTransient();
		$this->importRows();
		$this->sendResponse();
	}

	/**
	* Get the transient and set property
	*/
	private function getTransient()
	{
		$this->transient = get_transient('wpsl_import_file');
	}

	/**
	* Import Rows
	*/
	private function importRows()
	{
		$this->setMacFormatting();
		$csv = Reader::createFromPath($this->transient['file']);
		$res = $csv->setOffset($this->offset)->setLimit(1)->fetchAll();
		if ( !$res ) $this->complete();
		foreach($res as $key => $row){
			$import = new ImportRow($row, $this->transient);
			if ( !$import ) $this->failed_imports++;
			sleep(1); // for Google Map API rate limit
		}
	}

	/**
	* Set Mac Formatting
	*/
	private function setMacFormatting()
	{
		if ( isset($this->transient['mac']) && $this->transient['mac'] ){
			if (!ini_get("auto_detect_line_endings")) {
				ini_set("auto_detect_line_endings", '1');
			}
		}
	}

	/**
	* Send Response
	*/
	private function sendResponse()
	{
		return wp_send_json(array('status'=>'success', 'failed'=>$this->failed_imports));
	}

	/**
	* All Done
	*/
	private function complete()
	{
		return wp_send_json(array('status'=>'complete'));
	}

}