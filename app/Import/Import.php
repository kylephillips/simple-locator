<?php namespace SimpleLocator\Import;

use SimpleLocator\Import\ImportRow;
use League\Csv\Reader;

/**
* Primary Import Class (Called in Import step 3 via AJAX)
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
	private $failed_imports;

	/**
	* Import Count
	*/
	private $import_count;

	public function __construct($offset)
	{
		$this->failed_imports = 0;
		$this->import_count = 0;
		$this->offset = $offset;
		$this->getTransient();
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
	* Get the Geocode Meta Field Preferences
	* @return array
	*/
	private function geocodeMeta()
	{
		$geo_fields = array();
		$geo_fields['lat'] = get_option('wpsl_lat_field');
		$geo_fields['lng'] = get_option('wpsl_lng_field');
		return $geo_fields;
	}

	/**
	* Import Rows
	*/
	private function importRows()
	{
		$this->setMacFormatting();
		$csv = Reader::createFromPath($this->transient['file']);
		$offset = $this->transient['last_imported'] + $this->offset;
		$res = $csv->setOffset($offset)->setLimit(1)->fetchAll();
		$geo_fields = $this->geocodeMeta();

		if ( !$res ) $this->complete();

		foreach($res as $key => $row){
			$row[count($row) + 1] = $key + $this->offset;
			$import = new ImportRow($row, $this->transient, $geo_fields);
			if ( !$import->importSuccess() ) $this->failed_imports = $this->failed_imports + 1;
			if ( $import->importSuccess() ) $this->import_count = $this->import_count + 1;
		}
		sleep(1); // for Google Map API rate limit - 5 requests per second
	}

	/**
	* Update the completed rows transient
	*/
	private function updateCompleteCount()
	{
		$this->getTransient();
		$transient = $this->transient;
		$transient['complete_rows'] = $transient['complete_rows'] + $this->import_count;
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