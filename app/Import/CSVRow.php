<?php namespace SimpleLocator\Import;

use League\Csv\Reader;

/**
* Return an array of CSV columns for a given row
*/
class CSVRow {

	/**
	* The File Page
	*/
	private $file;


	public function __construct()
	{
		$this->setFile();
	}

	/**
	* Set the file path based on the transient
	*/
	private function setFile()
	{
		if ( !get_transient('wpsl_import_file') ){
			return $this->jsonError(__('An uploaded file could not be found', 'wpsimplelocator'));
		}
		$this->file = get_transient('wpsl_import_file');
	}

	/**
	* Get the columns for a given row
	*/
	public function getRow($row)
	{
		$this->setMacFormatting();
		$csv = Reader::createFromPath($this->file['file']);
		$res = $csv->fetchOne($row);

		if ( !$res ) $this->jsonError(__('Row not found', 'wpsimplelocator'));
		return $res;
	}

	/**
	* Get total row count
	*/
	public function rowCount()
	{
		return $this->file['row_count'];
	}

	/**
	* Set Mac Formatting
	*/
	private function setMacFormatting()
	{
		if ( isset($this->file['mac']) && $this->file['mac'] ){
			if (!ini_get("auto_detect_line_endings")) {
				ini_set("auto_detect_line_endings", '1');
			}
		}
	}

	/**
	* Send a JSON error
	*/
	private function jsonError($error)
	{
		return wp_send_json(array('status'=>'error', 'message'=>$error));
	}

}