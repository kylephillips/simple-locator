<?php namespace SimpleLocator\Forms;
/**
* Form Validation
*/
class Validation {

	/**
	* Form Data
	*/
	public $data;


	/**
	* Run the Validation
	*/
	public function validates($data)
	{
		$this->data = $data;
		$this->nonce();
		$this->form();
	}


	/**
	* Validate Nonce
	*/
	private function nonce()
	{
		if ( ! wp_verify_nonce( $this->data['nonce'], 'wpsl_locator-locator-nonce' ) ){
			$this->error('Incorrect Form Field', 'wpsimplelocator');
		}
	}


	/**
	* Validate the form data
	*/
	private function form()
	{
		// Latitude & Longitude
		if ( !is_numeric($this->data['latitude']) || !is_numeric($this->data['longitude']) ) {
			$this->error(__('The address could not be located at this time.', 'wpsimplelocator'));
		}

		// Distance
		if ( !ctype_digit($this->data['distance']) ) {
			$this->error(__('Please enter a valid distance.'));
		}

		// Unit
		if ( ($this->data['unit'] !== 'miles') && ($this->data['unit'] !== 'kilometers') ){
			$this->error(__('Invalid unit.', 'wpsimplelocator'));
		}

		return true;
	}


	/**
	* Send an Error Response
	*/
	private function error($error)
	{
		return wp_send_json(array('status'=>'error', 'message'=>$error));
		die();
	}

}