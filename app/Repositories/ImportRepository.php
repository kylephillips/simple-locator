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

}