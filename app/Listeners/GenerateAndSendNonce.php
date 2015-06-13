<?php 

namespace SimpleLocator\Listeners;

/**
* Create a nonce and return via AJAX
*/
class GenerateAndSendNonce 
{

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