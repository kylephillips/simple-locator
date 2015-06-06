<?php namespace SimpleLocator\Listeners;

use SimpleLocator\Services\Validation\NonceValidator;

/**
* Abstract Listener Base
*/
class AJAXListenerBase {

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
	* Send a Success Response
	*/
	protected function success($message)
	{
		return wp_send_json(array('status'=>'success', 'message'=>$message));
		die();
	}

	/**
	* Send an Error Response
	*/
	protected function error($error)
	{
		return wp_send_json(array('status'=>'error', 'message'=>$error));
		die();
	}

}