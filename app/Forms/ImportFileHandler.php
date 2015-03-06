<?php namespace SimpleLocator\Forms;

use League\Csv\Reader;

/**
* Handle the uploading of file for the purpose of importing addresses
*/
class ImportFileHandler {

	public function __construct()
	{
		$this->copyFile();
	}

	/**
	* Copy the file to the uploads folder
	* @see SimpleLocator\WPData\UploadFilter for uploads filter
	*/
	private function copyFile()
	{
		$file = $_FILES['file'];
		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload($file, $upload_overrides);
		$this->loopRows($movefile['file']);
	}

	/**
	* Loop Rows
	*/
	private function loopRows($file)
	{
		if (! ini_get("auto_detect_line_endings")) {
			ini_set("auto_detect_line_endings", '1');
		}
		$csv = Reader::createFromPath($file);
		$res = $csv->setLimit(25)->fetchAll();
    	var_dump($res); die();
	}

}