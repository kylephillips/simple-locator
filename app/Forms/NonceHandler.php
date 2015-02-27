<?php namespace SimpleLocator\Forms;

/**
* Create a nonce and return via AJAX
* (workaround for cacheing conflict)
*/
class NonceHandler {

	public function __construct()
	{
		$this->generateNonce();
	}

	private function generateNonce()
	{
		$data = array(
			'status' => 'success',
			'nonce' => wp_create_nonce('locatornonce')
		);
		return wp_send_json($data);
	}

}