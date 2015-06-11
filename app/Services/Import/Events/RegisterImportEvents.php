<?php namespace SimpleLocator\Services\Import\Events;

use SimpleLocator\Services\Import\Listeners\FileUploader;
use SimpleLocator\Services\Import\Listeners\GetCSVRow;
use SimpleLocator\Services\Import\Listeners\ColumnMapper;
use SimpleLocator\Services\Import\Listeners\Import;
use SimpleLocator\Services\Import\Listeners\FinishImport;


/**
* Register Events Related to Imports
*/
class RegisterImportEvents {

	public function __construct()
	{
		// Import Handlers
		add_action( 'admin_post_wpslimportupload', array($this, 'FileWasUploaded'));
		add_action( 'admin_post_wpslmapcolumns', array($this, 'ColumnMapWasSaved'));
		add_action( 'wp_ajax_wpsldoimport', array($this, 'ImportRequestMade' ));

		add_action( 'wp_ajax_wpslimportcolumns', array($this, 'CSVRowRequested' ));
		add_action( 'wp_ajax_wpslfinishimport', array($this, 'ImportComplete'));

		// Reset Test Data
		add_action( 'wp_ajax_reset_test_import', array($this, 'resetTestData' ));
	}

	/**
	* A File Was Uploaded
	*/
	public function FileWasUploaded()
	{
		new FileUploader;
	}

	/**
	* A CSV row was requested via AJAX
	*/
	public function CSVRowRequested()
	{
		new GetCSVRow;
	}

	/**
	* Map the columns for import
	*/
	public function ColumnMapWasSaved()
	{
		new ColumnMapper;
	}

	/**
	* Import Request Was Makde
	*/
	public function ImportRequestMade()
	{
		new Import;
	}

	/**
	* Finish the import
	*/
	public function ImportComplete()
	{
		new FinishImport;
	}

	/**
	* Test
	*/
	public function resetTestData()
	{
		$transient = get_transient('wpsl_import_file');
		$transient['last_imported'] = 0;
		$transient['error_rows'] = array();
		$transient['complete_rows'] = 0;
		$transient['post_ids'] = array();
		$transient['complete'] = false;
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
		return wp_send_json(array('status' => 'success'));
	}


}