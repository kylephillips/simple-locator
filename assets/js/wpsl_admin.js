
// Geocode the address on submit
function geocodeAddress(address){
   geocoder = new google.maps.Geocoder();
		
	geocoder.geocode({
		'address' : address
	}, function(results, status){
		if ( status == google.maps.GeocoderStatus.OK ){
			
			var latitude = results[0].geometry.location.lat();
			var longitude = results[0].geometry.location.lng();
			
			jQuery('#wpsl_latitude').val(latitude);
			jQuery('#wpsl_longitude').val(longitude);
			
			jQuery('#publish').unbind('click').click();

		} else {
			alert('Google Maps could not geocode this address.');
		}
	});
}

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