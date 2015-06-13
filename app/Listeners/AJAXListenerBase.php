<?php 

namespace SimpleLocator\Listeners;

use SimpleLocator\Services\Validation\NonceValidator;

/**
* Abstract Listener Base
*/
abstract class AJAXListenerBase 
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
			$this->nonce_validator->validate($_POST['locatorNonce'], 'locatornonce');
		} catch ( \Exception $e ){
			return $this->error($e->getMessage());
		}			
	}

	/**
	* Send a JSON Response
	* @param array $json
	*/
	protected function respond($json)
	{
		return wp_send_json($json);
		die();
	}

	/**
	* Send a Success Response
	*/
	protected function success($message)
	{
		$this->respond(array('status' => 'success', 'message' => $message));
	}

	/**
	* Send an Error Response
	*/
	protected function error($message)
	{
		$this->respond(array('status' => 'error', 'message' => $message));
	}

}