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

	$data = array(
		'nonce' => $nonce,
		'zip' => $zip,
		'distance' => $distance,
		'latitude' => $latitude,
		'longitude' => $longitude
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
		( 3959 * acos( cos( radians($ulat) ) * cos( radians( lat.meta_value ) ) 
		* cos( radians( lng.meta_value ) - radians($ulong) ) + sin( radians($ulat) ) * sin(radians(lat.meta_value)) ) ) 
		AS distance
		FROM $post_table AS p
		JOIN $meta_table AS lat
		ON p.ID = lat.post_id
		JOIN $meta_table AS lng
		ON p.ID = lng.post_id
		JOIN $meta_table AS c
		ON p.ID = c.post_id
		JOIN $meta_table AS a
		ON p.ID = a.post_id
		JOIN $meta_table AS s
		ON p.ID = s.post_id
		JOIN $meta_table AS z
		ON p.ID = z.post_id
		JOIN $meta_table AS t
		ON p.ID = t.post_id
		JOIN $meta_table AS w
		ON p.ID = w.post_ID
		WHERE `post_type` = 'location'
		AND `post_status` = 'publish'
		AND lat.meta_key = 'wpsl_latitude'
		AND lng.meta_key = 'wpsl_longitude'
		AND c.meta_key = 'wpsl_city'
		AND a.meta_key = 'wpsl_address'
		AND s.meta_key = 'wpsl_state'
		AND z.meta_key = 'wpsl_zip'
		AND t.meta_key = 'wpsl_phone'
		AND w.meta_key = 'wpsl_website'
		HAVING distance < $distance
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
		'results' => $results,
		'result_count' => $result_count
	);
	return $output;
}


