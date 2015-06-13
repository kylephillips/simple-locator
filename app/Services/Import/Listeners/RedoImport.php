<?php

namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Repositories\ImportRepository;

/**
* Undo a previous import and erase all data
*/
class RedoImport extends ImportListenerBase
{
	/**
	* Import ID
	* @var int
	*/
	private $import_id;

	/**
	* Import Repository
	* @var object
	*/
	private $import_repo;


	public function __construct()
	{
		parent::__construct();
		$this->import_repo = new ImportRepository;
		$this->setID();
		$this->resetTransient();
		$this->success(3);
	}

	/**
	* Set the Import ID & Post IDs
	*/
	private function setID()
	{
		$this->import_id = ( isset($_POST['redo_import_id']) ) ? intval($_POST['redo_import_id']) : 0;
	}

	/**
	* Delete the Posts
	*/
	private function resetTransient()
	{
		$import_data = $this->import_repo->getImportData($this->import_id);
		$import_data['error_rows'] = array();
		$import_data['complete_rows'] = 0;
		$import_data['last_imported'] = 0;
		$import_data['post_ids'] = array();
		$import_data['complete'] = false;
		$import_data['last_imported_time'] = time();
		set_transient('wpsl_import_file', $import_data, 1 * YEAR_IN_SECONDS);
	}

}