<?php namespace SimpleLocator\Import;

use League\Csv\Reader;

/**
* Uploads a File for Importing Purposes
*/
class FileUploader {

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
		if ( $_FILES['file']['name'] == "" ) return $this->error('Please include a file.');
		if ( $_FILES['file']['type'] !== "text/csv" ) return $this->error('File must be CSV format.');
		$file = $_FILES['file'];
		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload($file, $upload_overrides);

		$this->setTransient($movefile['file']);
		$this->success();
	}

	/**
	* Set the transient data to use in the remaining import steps
	* @var string path and name of file, as returned by wp_handle_upload
	*/
	private function setTransient($file)
	{
		$mac = ( isset($_POST['mac_formatted']) ) ? true : false;
		$rowcount = $this->rowCount($file, $mac);
		$transient = array(
			'file' => $file,
			'mac' => $mac,
			'row_count' => $rowcount,
			'filename' => $_FILES['file']['name'],
			'complete_rows' => 'false',
			'error_rows' => array()
		);
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
	}


	/**
	* Get total row count
	*/
	private function rowCount($file, $mac)
	{
		if ($mac && !ini_get("auto_detect_line_endings")) {
			ini_set("auto_detect_line_endings", '1');
		}
		$csv = Reader::createFromPath($file);
		$res = $csv->fetchAll();
		return count($res);
	}


	/**
	* Redirect to step 2 on success
	*/
	private function success()
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&step=2');
		return header('Location:' . $url);
	}


	/**
	* Error Notice
	*/
	private function error($error)
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&error=' . $error);
		header('Location:' . $url);
		return;
	}

}