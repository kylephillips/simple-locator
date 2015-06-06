<?php namespace SimpleLocator\Services\Import;

use League\Csv\Reader;

/**
* Uploads a File for Importing Purposes (Step 1 in import process)
*/
class FileUploader {

	public function __construct()
	{
		$this->verifyNonce();
		$this->copyFile();
	}

	/**
	* Verify the nonce
	*/
	private function verifyNonce()
	{
		if ( ! wp_verify_nonce($_POST['nonce'], 'wpsl-import-nonce') ) {
			$this->error(__('Incorrect Form Field', 'wpsimplelocator'));
		}
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
		if ( isset($movefile['error']) ) return $this->error($movefile['error']);
		
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
			'file' => $file, // full path to file
			'mac' => $mac, // is mac formatted?
			'row_count' => $rowcount, // total rows in CSV file
			'post_type' => $this->setPostType(), // post type to import to
			'filename' => $_FILES['file']['name'], // filename for display purposes
			'complete_rows' => '0',
			'error_rows' => array(), // Rows with import or geocoding errors,
			'last_imported' => 0,
			'lat' => get_option('wpsl_lat_field'), // Field to save latitude to
			'lng' => get_option('wpsl_lng_field'), // Field to save longitude to
			'import_type' => 'csv'
		);
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
	}

	/**
	* Set the Post Type
	*/
	private function setPostType()
	{
		return ( isset($_POST['import_post_type']) ) ? sanitize_text_field($_POST['import_post_type']) : 'location';
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
		$count = $csv->each(function(){
    		return true;
		}); 
		return $count;
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
		die();
	}

}