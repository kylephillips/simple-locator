<?php namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Services\Import\FileUploader;

/**
* Handle the uploading of file for the purpose of importing addresses
*/
class ImportFileHandler {

	public function __construct()
	{
		new FileUploader();
	}	

}