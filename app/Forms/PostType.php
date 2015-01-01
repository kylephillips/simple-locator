<?php namespace SimpleLocator\Forms;

use \SimpleLocator\Repositories\FieldRepository;

/**
* Ajax Handler for choosing post type fields in settings
* Returns a response with html option list
*/
class PostType {

	/**
	* Field Repository
	* @var object
	*/
	private $field_repo;

	/**
	* Response
	* @var array
	*/
	private $response;

	/**
	* Form Data
	* @var array
	*/
	private $data;


	public function __construct()
	{
		$this->field_repo = new FieldRepository;
		$this->setData();
		$this->validateNonce();
		$this->getFields();
	}


	/**
	* Sanitize and set the user-submitted data
	*/
	private function setData()
	{
		$this->data = array(
			'nonce' => sanitize_text_field($_GET['nonce']),
			'post_type' => sanitize_text_field($_GET['post_type']),
		);
	}


	/**
	* Validate Nonce
	*/
	private function validateNonce()
	{
		if ( ! wp_verify_nonce( $this->data['nonce'], 'wpsl_locator-locator-nonce' ) ){
			$this->sendResponse(array('status'=>'error', 'message'=>'Incorrect Form Field'));
		}
	}


	/**
	* Get the fields for the post type
	*/
	private function getFields()
	{
		$fields = $this->field_repo->displayFieldOptions($this->data['post_type']);
		$response = array('status'=>'success', 'fields'=>$fields);
		$this->sendResponse($response);
	}


	/**
	* Send the Response
	* @param response array
	* @return JSON response
	*/
	private function sendResponse($response)
	{
		return wp_send_json($response);
	}

}