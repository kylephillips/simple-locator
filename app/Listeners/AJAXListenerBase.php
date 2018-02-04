<?php 
namespace SimpleLocator\Listeners;

/**
* Abstract Listener Base
*/
abstract class AJAXListenerBase 
{
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