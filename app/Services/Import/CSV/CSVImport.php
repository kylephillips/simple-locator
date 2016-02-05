<?php 

namespace SimpleLocator\Services\Import\CSV;

use SimpleLocator\Services\Import\PostImporter;
use League\Csv\Reader;

/**
* Import from a CSV File
*/
class CSVImport 
{

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
	* Number of Records to Request
	* @var int
	*/
	private $request_number;

	/**
	* Failed Imports
	* @var int
	*/
	private $failed_count = 0;

	/**
	* Import Count
	* @var int
	*/
	private $success_count = 0;

	/**
	* Rows to Import
	* @var array
	*/
	private $rows;

	/**
	* Post Importer
	*/
	private $post_importer;

	/**
	* Newly Imported Post IDs
	* @var array
	*/
	private $post_ids = array();


	public function __construct()
	{
		$this->post_importer = new PostImporter;
	}

	/**
	* Run the Import 
	* @param int $offset
	* @param int $request_number
	* @param array $transient
	*/
	public function doImport($offset, $request_number, $transient)
	{
		$this->transient = $transient;
		$this->request_number = $request_number;
		$this->setOffset($offset);
		$this->setRows();
		$this->importRows();
		$this->updateCompleteCount();
		return $this->post_ids;
	}

	/**
	* Set the Offset
	*/
	private function setOffset($offset)
	{
		$this->offset = $offset;
		if ( $this->transient['last_imported'] == 0 && $this->transient['skip_first'] ) $this->offset = 1;
	}

	/**
	* Set the Rows to Import
	*/
	private function setRows()
	{
		$this->setMacFormatting();
		$csv = Reader::createFromPath($this->transient['file']);
		$this->rows = $csv->setOffset($this->offset)->setLimit($this->request_number)->fetchAll();
		
		if ( !$this->rows ) {
			throw new \SimpleLocator\Services\Import\Exceptions\ImportCompleteException;
		}

		$this->setRecordNumbers();
	}

	/**
	* Set the Record Numbers for each record, relative to all records
	* Used for identifying record numbers for failed imports
	*/
	private function setRecordNumbers()
	{
		foreach ($this->rows as $key => $row){
			$this->rows[$key]['record_number'] = $this->offset + $key + 1;
		}
	}

	/**
	* Import Rows
	*/
	private function importRows()
	{
		foreach($this->rows as $key => $row){
			if ( $new_id = $this->post_importer->import($row, $this->transient) ){
				$this->success_count = $this->success_count + 1;
				$this->post_ids[] = $new_id;
			} else {
				$this->failed_count = $this->failed_count + 1;
			}
		}
		sleep(1); // for Google Map API rate limit - 5 requests per second
	}

	/**
	* Update the transient with import data
	*/
	private function updateCompleteCount()
	{
		$transient = get_transient('wpsl_import_file');
		$transient['complete_rows'] = $transient['complete_rows'] + $this->success_count;
		$transient['last_imported'] = $this->rows[count($this->rows) - 1]['record_number']; // Last row in this batch
		$transient['last_imported_time'] = time();
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
		$this->transient = $transient;
	}


	/**
	* Set Mac Formatting
	*/
	private function setMacFormatting()
	{
		if ( isset($this->transient['mac']) && $this->transient['mac'] ){
			if (!ini_get("auto_detect_line_endings")) ini_set("auto_detect_line_endings", '1');
		}
	}

	/**
	* Get Number of Failed Imports
	* @return int
	*/
	public function getFailedCount()
	{
		return $this->failed_count;
	}

	/**
	* Get the Import Count
	* @return int
	*/
	public function getImportCount()
	{
		return $this->success_count;
	}

	/**
	* Get Complete Rows Count
	*/
	public function getCompleteCount()
	{
		return $this->transient['complete_rows'];
	}

}