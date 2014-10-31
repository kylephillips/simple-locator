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
			$this->error('Incorrect Form Field');
		}
	}


	/**
	* Validate the form data
	*/
	private function form()
	{
		// Zip
		if ( !preg_match("#[0-9]{5}#", $this->data['zip']) ){
			$this->error('Please enter a valid 5-digit zip code');
		}

		// Latitude & Longitude
		if ( !is_numeric($this->data['latitude']) || !is_numeric($this->data['longitude']) ) {
			$this->error('The address could not be located at this time.');
		}

		// Distance
		if ( !ctype_digit($this->data['distance']) ) {
			$this->error('Please enter a valid distance.');
		}

		// Unit
		if ( ($this->data['unit'] !== 'miles') && ($this->data['unit'] !== 'kilometers') ){
			$this->error('Invalid unit.');
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