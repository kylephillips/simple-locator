<?php 

namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Services\Validation\NonceValidator;

abstract class ImportListenerBase 
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
			$this->nonce_validator->validate($_POST['nonce'], 'wpsl-import-nonce');
		} catch ( \Exception $e ){
			return $this->error($e->getMessage());
		}			
	}

	/**
	* Redirect to next step on success
	*/
	protected function success($step = null, $message = null)
	{
		$url = 'options-general.php?page=wp_simple_locator&tab=import';
		if ( $step ) $url .= '&step=' . $step;
		if ( $message ) $url .= '&message=' . $message;
		$url = admin_url($url);
		return header('Location:' . $url);
	}

	/**
	* Redirect to current step with error
	*/
	protected function error($error)
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&error=' . $error);
		header('Location:' . $url);
		die();
	}

}