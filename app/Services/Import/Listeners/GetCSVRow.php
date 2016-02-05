<?php 

namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Services\Import\CSV\Row;

class GetCSVRow extends ImportAJAXListenerBase 
{

	/**
	* CSV Row Object
	*/
	private $row;

	public function __construct()
	{
		parent::__construct();
		$this->row = new Row;
		$this->getRow();
	}

	/**
	* Get the column data for a specific row
	*/
	private function getRow()
	{
		$row = ( !isset($_POST['rowcount']) || !is_numeric($_POST['rowcount']) ) ? 0 : intval($_POST['rowcount']);
		
		try {
			$columns = $this->row->getRow($row);
		} catch ( \Exception $e ){
			$this->error($e->getMessage);
		}

		$this->respond(array('status'=>'success', 'columns' => $columns, 'row_count' => $this->totalRowCount()));
	}

	/**
	* Get total number of rows
	*/
	private function totalRowCount()
	{
		$transient = get_transient('wpsl_import_file');
		return $transient['row_count'];
	}

}