// Geocode the address on submit
jQuery(function($){
	var form = $("form[name='post']");
	$(form).find("#publish").on('click', function(e){
		e.preventDefault();
		var streetaddress = $('#wpsl_address').val();
		var city = $('#wpsl_city').val();
		var state = $('#wpsl_state').val();
		var zip = $('#wpsl_zip').val();
		var address = streetaddress + ' ' + city + ' ' + state + ' ' + zip;
		geocodeAddress(address);
	});

	function geocodeAddress(address){
		geocoder = new google.maps.Geocoder();
			
		geocoder.geocode({
			'address' : address
		}, 
		function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				
				var latitude = results[0].geometry.location.lat();
				var longitude = results[0].geometry.location.lng();
				
				$('#wpsl_latitude').val(latitude);
				$('#wpsl_longitude').val(longitude);
				
				$('#publish').unbind('click').click();

			} else {
				alert('Google Maps could not geocode this address.');
			}
		});
	}

	$(document).ready(function(){
		checkMapStatus();
	});
}); // jQuery


// Check if post has geocode info saved
function checkMapStatus(){
	var lat = jQuery('#wpsl_latitude').val();
	var lng = jQuery('#wpsl_longitude').val();
	if ( (lat !== "") && (lng !== "") ){
		// Show the hidden map div
		jQuery('#wpslmap').show();
		// Load the GMap
		loadMap(lat, lng);
	}
}


// Load the Google Map in Admin View
function loadMap(lat, lng){
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

