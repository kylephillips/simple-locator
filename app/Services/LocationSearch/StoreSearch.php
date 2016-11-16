<?php
namespace SimpleLocator\Services\LocationSearch;

/**
* Store the search in the DB
*/
class StoreSearch
{
	/**
	* Store the Search
	* @param array $search_data
	*/
	public function save()
	{
		$address = sanitize_text_field($_POST['address']);
		$formatted_address = sanitize_text_field($_POST['formatted_address']);
		$distance = sanitize_text_field($_POST['distance']);
		$latitude = sanitize_text_field($_POST['latitude']);
		$longitude = sanitize_text_field($_POST['longitude']);
		$ip = $this->getIP();
		
		global $wpdb;
		$table = $wpdb->prefix . 'simple_locator_history';
		$wpdb->insert(
			$table,
			array(
				'user_ip' => $ip,
				'search_lat' => $latitude,
				'search_lng' => $longitude,
				'search_term' => $address,
				'search_term_formatted' => $formatted_address,
				'distance' => $distance
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			)
		);
	}

	/**
	* Get the user's IP
	*/
	private function getIP()
	{
	    $ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
}