<?php
/**
* Front-end form handler for simple locator lookup
* @return JSON Response
*/
function wpsl_form_handler()
{
	$nonce = sanitize_text_field($_POST['locatorNonce']);
	$zip = sanitize_text_field($_POST['zip']);
	$distance = sanitize_text_field($_POST['distance']);
	$latitude = sanitize_text_field($_POST['latitude']);
	$longitude = sanitize_text_field($_POST['longitude']);
	$unit = sanitize_text_field($_POST['unit']);

	$data = array(
		'nonce' => $nonce,
		'zip' => $zip,
		'distance' => $distance,
		'latitude' => $latitude,
		'longitude' => $longitude,
		'unit' => $unit
	);

	// Validate
	wpsl_validate_data($data);
	
	// Get Data
	$output = wpls_locations_query($data);

	// Return Data
	wp_send_json($output);
}


/**
* Validate form data
*/
function wpsl_validate_data($data)
{
	// Validate Nonce
	if ( ! wp_verify_nonce( $data['nonce'], 'wpsl_locator-locator-nonce' ) ){
		$output = json_encode(array(
			'status' => 'error',
			'message' => 'Busted Yo!'
		));
		echo $output;
		die();
	}

	// Validate Zip
	if ( !preg_match("#[0-9]{5}#", $data['zip']) ){
		$output = json_encode(array(
			'status'=>'error',
			'message' => 'Please enter a valid 5-digit zip code'
		));
		echo $output;
		die();
	}

	// Validate Latitude & Longitude
	if ( !is_numeric($data['latitude']) || !is_numeric($data['longitude']) ) {
		$output = json_encode(array(
			'status'=>'error',
			'message' => 'The address could not be located at this time.'
		));
		echo $output;
		die();
	}

	// Validate Distance
	if ( !ctype_digit($data['distance']) ) {
		$output = json_encode(array(
			'status'=>'error',
			'message' => 'Please enter a valid distance'
		));
		echo $output;
		die();
	}

	// Validate Unit
	if ( ($data['unit'] !== 'miles') && ($data['unit'] !== 'kilometers') ){
		$output = json_encode(array(
			'status'=>'error',
			'message' => 'Invalid unit'
		));
		echo $output;
		die();
	}
}


/**
* Lookup location data
*/
function wpls_locations_query($data)
{

	global $wpdb;
	$p = $wpdb->prefix;
	$post_table = $p . 'posts';
	$meta_table = $p . 'postmeta';
	$distance = $data['distance'];
	$ulat = $data['latitude'];
	$ulong = $data['longitude'];
	$unit = $data['unit'];
	$post_type = get_option('wpsl_post_type');
	$lat_field = get_option('wpsl_lat_field');
	$lng_field = get_option('wpsl_lng_field');
	
	if ( $unit == "miles" ){
		$l = 3959;
	} else {
		$l = 6371;
	}

	$sql = "
	SELECT 
		p.post_title AS title,
		p.ID AS id,
		p.post_content AS content,
		t.meta_value AS phone,
		a.meta_value AS address,
		c.meta_value AS city,
		s.meta_value AS state,
		z.meta_value AS zip,
		w.meta_value AS website,
		lat.meta_value AS latitude,
		lng.meta_value AS longitude,
		( $l * acos( cos( radians($ulat) ) * cos( radians( lat.meta_value ) ) 
		* cos( radians( lng.meta_value ) - radians($ulong) ) + sin( radians($ulat) ) * sin(radians(lat.meta_value)) ) ) 
		AS distance
		FROM $post_table AS p
		LEFT JOIN $meta_table AS lat
		ON p.ID = lat.post_id AND lat.meta_key = '$lat_field'
		LEFT JOIN $meta_table AS lng
		ON p.ID = lng.post_id AND lng.meta_key = '$lng_field'
		LEFT JOIN $meta_table AS c
		ON p.ID = c.post_id AND c.meta_key = 'wpsl_city'
		LEFT JOIN $meta_table AS a
		ON p.ID = a.post_id AND a.meta_key = 'wpsl_address'
		LEFT JOIN $meta_table AS s
		ON p.ID = s.post_id AND s.meta_key = 'wpsl_state'
		LEFT JOIN $meta_table AS z
		ON p.ID = z.post_id AND z.meta_key = 'wpsl_zip'
		LEFT JOIN $meta_table AS t
		ON p.ID = t.post_id AND t.meta_key = 'wpsl_phone'
		LEFT JOIN $meta_table AS w
		ON p.ID = w.post_ID AND w.meta_key = 'wpsl_website'
		WHERE `post_type` = '$post_type'
		AND `post_status` = 'publish'
		HAVING distance < $distance
		ORDER BY distance
	";
	
	$query_results = $wpdb->get_results($sql);

	// Add result data to response object
	$results = array();
	$result_count = count($query_results);

	foreach ( $query_results as $qr ) :
		$location = array(
			'title' => $qr->title,
			'permalink' => get_permalink($qr->id),
			'distance' => round($qr->distance, 2),
			'address' => $qr->address,
			'city' => $qr->city,
			'state' => $qr->state,
			'zip' => $qr->zip,
			'phone' => $qr->phone,
			'website' => $qr->website,
			'latitude' => $qr->latitude,
			'longitude' => $qr->longitude
		);
		array_push($results, $location);
	endforeach;


	$output = array(
		'status' => 'success',
		'zip'=> $data['zip'], 
		'distance'=> $data['distance'],
		'latitude' => $data['latitude'],
		'longitude' => $data['longitude'],
		'unit' => $unit,
		'results' => $results,
		'result_count' => $result_count,
		'sql'=> $sql
	);
	return $output;
}


