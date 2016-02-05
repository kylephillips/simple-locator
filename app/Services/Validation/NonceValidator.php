<?php 

namespace SimpleLocator\Services\Validation;

class NonceValidator 
{

	/**
	* Validate a Nonce
	* @param string $submitted_nonce - the user submitted nonce
	* @param string $match_nonce - the nonce to test against
	*/
	public function validate($submitted_nonce, $match_nonce)
	{
		if ( ! wp_verify_nonce( $submitted_nonce, $match_nonce ) ){
			throw new \Exception(__('Incorrect Form Field', 'wpsimplelocator'));
		}
	}

}