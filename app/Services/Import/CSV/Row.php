<?php 

namespace SimpleLocator\Services\Import\CSV;

use League\Csv\Reader;

/**
* Return an array of CSV columns for a given row
*/
class Row 
{

	/**
	* The File Path
	*/
	private $file;

	/**
	* Set the file path based on the transient
	*/
	private function setFile()
	{
		if ( !get_transient('wpsl_import_file') ){
			throw new \Exception(__('An uploaded file could not be found', 'wpsimplelocator'));
		}
		$this->file = get_transient('wpsl_import_file');
	}

	/**
	* Get the columns for a given row
	*/
	public function getRow($row)
	{
		$this->setFile();
		$this->setMacFormatting();
		$csv = Reader::createFromPath($this->file['file']);
		$res = $csv->fetchOne($row);

		if ( !$res ){
			throw new \Exception(__('Row not found', 'wpsimplelocator'));
		}
		return $res;
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
}