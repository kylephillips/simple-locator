<?php 

namespace SimpleLocator\Services\Import\Listeners;

/**
* Do the Import
*/
class Import extends ImportAJAXListenerBase 
{

	/**
	* Transient
	* @var array
	*/
	private $transient;

	/**
	* Import Class
	* @var object
	*/
	private $import_class;


	public function __construct()
	{
		parent::__construct();
		$this->setTransient();
		$this->setImportClass();
		$this->doImport();
	}

	/**
	* Get the transient and set property
	*/
	private function setTransient()
	{
		$this->transient = get_transient('wpsl_import_file');
	}

	/**
	* Instantiate import class based on type of import
	*/
	private function setImportClass()
	{
		$import_type = $this->transient['import_type'];
		if ( $import_type == 'text/csv' ) return $this->import_class = new \SimpleLocator\Services\Import\CSV\CSVImport;
	}

	/**
	* Do the Import
	*/
	private function doImport()
	{
		$offset = intval(sanitize_text_field($_POST['offset']));
		$request_number = intval(sanitize_text_field($_POST['imports_per_request']));

		try {
			$ids = $this->import_class->doImport($offset, $request_number, $this->transient);
		} catch (\SimpleLocator\Services\Import\Exceptions\ImportCompleteException $e ) {
			$this->respond(array('status' => 'complete'));
		} catch ( \Exception $e ){
			$this->error($e->getMessage());
		}
		$this->updatePostIDs($ids);
		$this->sendResponse();
	}

	/**
	* Update New Post IDs in Transient
	*/ 
	private function updatePostIDs($ids)
	{
		$transient = get_transient('wpsl_import_file');
		$transient['post_ids'] = array_unique(array_merge($transient['post_ids'], $ids));
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
	}

	/**
	* Send Response
	*/
	private function sendResponse()
	{
		$this->respond(array(
			'status' => 'success', 
			'failed_count' => $this->import_class->getFailedCount(), 
			'import_count' => $this->import_class->getImportCount(),
			'complete_rows' => $this->import_class->getCompleteCount()
		));
	}

}