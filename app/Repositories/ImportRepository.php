<?php 

namespace SimpleLocator\Repositories;

class ImportRepository
{
	/**
	* Import Transient
	* @var array
	*/
	private $transient;

	public function __construct()
	{
		$this->transient = get_transient('wpsl_import_file');
	}

	/**
	* Is there an incomplete import?
	* @return int
	*/
	public function incomplete()
	{
		if ( isset($this->transient['complete']) && !$this->transient['complete'] ){
			return $this->transient['row_count'] - $this->transient['complete_rows'] - count($this->transient['error_rows']);
		}
		return false;
	}

	/**
	* Get the import transient
	*/
	public function transient()
	{
		return $this->transient;
	}

	/**
	* Get imported post IDs from an import
	* @param int $id - The Import ID
	*/
	public function getImportedPostIDs($id)
	{
		$meta = get_post_meta($id, 'wpsl_import_data', true);
		return $meta['post_ids'];
	}

	/**
	* Get Import Data for a specific import
	* @param int $id - The Import ID
	*/
	public function getImportData($id)
	{
		return get_post_meta($id, 'wpsl_import_data', true);
	}

}