<?php namespace SimpleLocator\Forms;

use SimpleLocator\Import\CSVRow;

class ImportGetRowHandler {

	/**
	* CSV Columns Object
	*/
	private $columns;


	public function __construct()
	{
		$this->validateNonce();
		$this->columns = new CSVRow;
		$this->getRow();
	}

	/**
	* Validate Nonce
	*/
	private function validateNonce()
	{
		if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'wpsl_locator-locator-nonce' ) ){
			$this->sendResponse(array('status'=>'error', 'message'=>'Incorrect Form Field'));
		}
	}

	/**
	* Get the column data for a given row
	*/
	private function getRow()
	{
		$row = ( !isset($_POST['rowcount']) || !is_numeric($_POST['rowcount']) ) ? 0 : intval($_POST['rowcount']);
		$columns = $this->columns->getRow($row);
		
		$count = $this->columns->rowCount();
		$this->sendResponse(array('status'=>'success', 'columns'=>$columns, 'row_count'=>$count));
	}

	/**
	* Send the response
	*/
	private function sendResponse($response)
	{
		return wp_send_json($response);
	}

}