jQuery(function($){

	/**
	* ------------------------------------------------------
	* Location Post Type Entry Map Functions
	* ------------------------------------------------------
	*/

	/**
	* Geocode Address when saving Location Posts
	*/
	var form = $("form[name='post']");
	$(form).find("#publish").on('click', function(e){
		e.preventDefault();
		var address = formatAddress();
		googleGeocodeAddress(address);
	});


	/**
	* Format the provided address to submit for geocoding
	*/
	function formatAddress()
	{
		var streetaddress = $('#wpsl_address').val();
		var city = $('#wpsl_city').val();
		var state = $('#wpsl_state').val();
		var zip = $('#wpsl_zip').val();
		var address = streetaddress + ' ' + city + ' ' + state + ' ' + zip;
		return address;
	}


	/**
	* Submit the address to Google for Geocoding
	*/
	function googleGeocodeAddress(address)
	{
		geocoder = new google.maps.Geocoder();
			
		geocoder.geocode({
			'address' : address
		}, 
		function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				setFormCoordinates(lat, lng);
				$('#publish').unbind('click').click();
			} else {
				alert('The address could not be located.');
			}
		});
	}


	/**
	* Set the Lat & Lng Form Fields
	*/
	function setFormCoordinates(lat, lng)
	{
		$('#wpsl_latitude').val(lat);
		$('#wpsl_longitude').val(lng);
	}


	/**
	* Check if the Location has Geocode Saved
	*/
	$(document).ready(function(){
		checkMapStatus();
	});
	function checkMapStatus()
	{
		var lat = $('#wpsl_latitude').val();
		var lng = $('#wpsl_longitude').val();
		if ( (lat !== "") && (lng !== "") ){
			$('#wpslmap').show();
			loadGoogleMap(lat, lng);
		}
	}


	/*
	* Load the Google Map in Admin View
	*/
	function loadGoogleMap(lat, lng){
		var map = new google.maps.Map(document.getElementById('wpslmap'), {
			zoom: 14,
			center: new google.maps.LatLng(lat,lng),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false,
			scaleControl : false,
		});

		var marker, i;

		marker = new google.maps.Marker({
			position: new google.maps.LatLng(lat, lng),
			map: map
		});
	}


	/**
	* ------------------------------------------------------
	* Settings Page
	* ------------------------------------------------------
	*/


}); // jQuery


/**
* Settings Page
*/
jQuery(document).ready(function(){
	var pt = jQuery('#wpsl_post_type').val();
	if ( pt !== 'location' ){
		jQuery('#field_wpsl').attr('disabled', 'disabled').removeAttr('checked');
		jQuery('#field_custom').attr('checked','checked');
	}

	var ft = jQuery('input[name="wpsl_field_type"]:checked').val();
	
	if ( ft === 'wpsl' ){
		jQuery('.latlng').hide();
	}
});

// Prevent the selection of wspl geo fields if another post type is selected
jQuery(document).on('change', '#wpsl_post_type', function(){
	var value = jQuery(this).val();
	if ( value !== 'location' ){
		jQuery('#field_wpsl').attr('disabled', 'disabled').removeAttr('checked');
		jQuery('#field_custom').attr('checked','checked');
		jQuery('.latlng').show();
	} else {
		jQuery('#field_wpsl').removeAttr('disabled');
	}
});


jQuery(document).on('change', 'input[name="wpsl_field_type"]:radio', function(){
	var type = jQuery(this).val();
	if ( type == 'wpsl' ){
		jQuery('.latlng').hide();
		jQuery('#wpsl_lat_field').val('wpsl_latitude');
		jQuery('#wpsl_lng_field').val('wpsl_longitude');
	} else {
		jQuery('.latlng').show();
		var lat = jQuery('#lat_select').val();
		var lng = jQuery('#lng_select').val();
		jQuery('#wpsl_lat_field').val(lat);
		jQuery('#wpsl_lng_field').val(lng);
	}
});

// Update lat field on select change
jQuery(document).on('change', '#lat_select', function(){
	var value = jQuery(this).val();
	jQuery('#wpsl_lat_field').val(value);
});
jQuery(document).on('change', '#lng_select', function(){
	var value = jQuery(this).val();
	jQuery('#wpsl_lng_field').val(value);
});

