<?php namespace SimpleLocator\Forms;

/**
* Resets the post type back to defaults 
*/
class PostTypeResetHandler {

	public function __construct()
	{
		$this->validateNonce();
		$this->removeOptions();
		$this->sendResponse();
	}

	/**
	* Validate Nonce
	*/
	private function validateNonce()
	{
		if ( ! wp_verify_nonce( sanitize_text_field($_GET['nonce']), 'wpsl_locator-locator-nonce' ) ){
			return wp_send_json(array('status'=>'error', 'message'=>__('Incorrect Form Field', 'wpsimplelocator')));
		}
	}

	/**
	* Remove the Post type option(s)
	*/
	private function removeOptions()
	{
		delete_option('wpsl_post_type');
		delete_option('wpsl_posttype_labels');
		delete_option('wpsl_field_type');
		delete_option('wpsl_lng_field');
		delete_option('wpsl_lat_field');
	}

	/**
	* Send a JSON response
	*/
	private function sendResponse()
	{
		return wp_send_json(array('status'=>'success', 'message'=>__('Post Type successfully reset', 'wpsimplelocator')));
	}

}