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
	protected function success($step)
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&step=' . $step);
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