<?php 

namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Services\Validation\NonceValidator;

abstract class ImportAJAXListenerBase 
{

	/**
	* Nonce Validator
	*/
	protected $nonce_validator;

	public function __construct()
	{
		$this->nonce_validator = new NonceValidator;
		$this->validateNonce();
	}

	/**
	* Validate the Nonce
	*/
	protected function validateNonce()
	{
		try {
			$this->nonce_validator->validate($_POST['nonce'], 'wpsl_locator-locator-nonce');
		} catch ( \Exception $e ){
			return $this->error($e->getMessage());
		}			
	}

	/**
	* Send a Response
	*/
	protected function respond($json)
	{
		wp_send_json($json);
		die();
	}

	/**
	* Send an Error response
	*/
	protected function error($error)
	{
		wp_send_json(array('status' => 'error', 'message' => $error));
		die();
	}

}