<?php namespace SimpleLocator\Services\Import;

use SimpleLocator\Services\Import\ImportRow;
use League\Csv\Reader;

/**
* Primary Import Class (Called in Import step 3 via AJAX)
*/
class CSVImport {

	/**
	* Transient
	* @var array
	*/
	private $transient;

	/**
	* Row Offset
	* @var int
	*/
	private $offset;

	/**
	* Failed Imports
	* @var int
	*/
	private $failed_imports;

	/**
	* Import Count
	* @var int
	*/
	private $import_count;

	public function __construct()
	{
		$this->failed_imports = 0;
		$this->import_count = 0;
	}

	/**
	* Run the Import 
	*/
	public function doImport($offset)
	{
		$this->offset = $offset;
		$this->getTransient();
		$this->setImportCount();
		$this->importRows();
		$this->updateCompleteCount();
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
	* Set the import count
	*/
	private function setImportCount()
	{
		$this->import_count = $this->transient['complete_rows'];
	}

	/**
	* Import Rows
	*/
	private function importRows()
	{
		$this->setMacFormatting();
		$csv = Reader::createFromPath($this->transient['file']);

		// Remove Title Row
		if ( $this->transient['last_imported'] == 0 && $this->transient['skip_first'] ) $this->offset = 1;

		$offset = $this->transient['last_imported'] + $this->offset;
		$res = $csv->setOffset($offset)->setLimit(1)->fetchAll(); // Update to 5 for production, will submit 5 every 1 second, per Google's API limits

		if ( !$res ) $this->complete();

		foreach($res as $key => $row){
			$row[count($row) + 1] = $key + $this->offset;
			$import = new ImportRow($row, $this->transient);
			if ( !$import->importSuccess() ) $this->failed_imports = $this->failed_imports + 1;
			if ( $import->importSuccess() ) $this->import_count = $this->import_count + 1;
		}
		sleep(1); // for Google Map API rate limit - 5 requests per second
	}

	/**
	* Update the transient with import data
	*/
	private function updateCompleteCount()
	{
		$this->getTransient();
		$transient = $this->transient;
		$transient['complete_rows'] = $transient['complete_rows'] + $this->import_count;
		$transient['last_imported'] = $this->offset;
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
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
		return wp_send_json(array('status'=>'success', 'failed'=>$this->failed_imports, 'import_count'=>$this->import_count));
	}

	/**
	* All Done
	*/
	private function complete()
	{
		return wp_send_json(array('status'=>'complete'));
	}

}