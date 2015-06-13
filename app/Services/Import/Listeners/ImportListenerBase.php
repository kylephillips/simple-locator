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
	* Check Capabilities
	*/
	protected function validateUser()
	{
		if ( !current_user_can('delete_others_posts') ) return $this->error(__('You do not have the necessary capabilities to undo an import. Contact your site administrator to perform this action.', 'wpsimplelocator'));
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