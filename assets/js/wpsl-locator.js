// Location Scripts
jQuery('#wpslsubmit').on('click', function(e){
	e.preventDefault();
	jQuery('#searcherror').hide();
	jQuery('#locatormap').hide();
	jQuery('#locatorresults').empty();
	jQuery('#locatorresults').show();
	geocodeZip();	
});


/**
* Send the form data to the form handler
*/
function sendFormData()
{
	jQuery.ajax({
		url: wpsl_locator.ajaxurl,
		type: 'post',
		datatype: 'json',
		data: {
			action: 'locate',
			zip: jQuery('#zip').val(),
			locatorNonce : wpsl_locator.locatorNonce,
			distance: jQuery('#distance').val(),
			latitude: jQuery('#latitude').val(),
			longitude: jQuery('#longitude').val(),
			unit: jQuery('#unit').val()
		},
		success: function(data){
			if (data.status === 'error'){
				jQuery('#searcherror').text(data.message);
				jQuery('#searcherror').show();
				jQuery('#locatorresults').hide();
			} else {
				loadLocationResults(data);
			}
		}
	});
}


/**
* Geocode the zip prior to submitting the search form
*/
function geocodeZip()
{
	var zip = jQuery('#zip').val();
	
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'address' : zip
	}, function(results, status){

		if ( status == google.maps.GeocoderStatus.OK ){
			
			var latitude = results[0].geometry.location.lat();
			var longitude = results[0].geometry.location.lng();
			
			jQuery('#latitude').val(latitude);
			jQuery('#longitude').val(longitude);
			
			sendFormData();

		} else {
			jQuery('#searcherror').text('Address could not be found at this time.');
			jQuery('#searcherror').show();
			jQuery('#locatorresults').hide();
		}
	});
}


/**
* Load the results into the view
*/
function loadLocationResults(data)
{
	if ( data.result_count > 0 ){

		if ( data.result_count === 1 ){
			var location = 'location';
		} else {
			var location = 'locations';
		}

		var output = '<h3>' + data.result_count + ' ' + location + ' found within ' + data.distance + ' ' + data.unit + ' of ' + data.zip + '</h3><ul>';
		
		for( i = 0; i < data.results.length; i++ ) {
			
			output = output + '<li><strong>';
			output = output + '<a href="' + data.results[i].permalink + '">';
			output = output + data.results[i].title;
			output = output + '</a></strong><br />';
			output = output + '<em>Distance: ' + data.results[i].distance + ' ' + data.unit + '</em><br />';
			output = output + data.results[i].address + '<br />' + data.results[i].city + ', ' + data.results[i].state + ' ' + data.results[i].zip;

			var phone = data.results[i].phone;
			var website = data.results[i].website;
			
			if ( phone !== "" ){
				output = output + '<br />Phone: ' + phone;
			}
			if ( website !== "" ){
				output = output + '<br /><a href="' + website + '" target="_blank">' + website + '</a>';
			}
			output = output + '</li>';
		}

		output = output + '</ul>';

		jQuery('#locatorresults').removeClass('loading');
		jQuery('#locatorresults').html(output);

		jQuery('#locatormap').show();
		showLocationMap(data);

	} else {
		// No results were returned
		jQuery('#searcherror').text('No results found.');
		jQuery('#searcherror').show();
		jQuery('#locatorresults').hide();
	}
}


/**
* Load the Google map and show locations found
*/
function showLocationMap(data)
{
	var map;
	var bounds = new google.maps.LatLngBounds();
	var mapOptions = {
			mapTypeId: 'roadmap',
			mapTypeControl: false,
			zoom: 8
		}
	var locations = [];
	var infoWindow = new google.maps.InfoWindow(), marker, i;
	map = new google.maps.Map(document.getElementById("locatormap"), mapOptions);
	
	// Array of locations
	for (var i = 0, length = data.results.length; i < length; i++) {
		var title = data.results[i].title;
		var lat = data.results[i].latitude;
		var lng = data.results[i].longitude;
		var counselor = [title,lat,lng];
		locations.push(counselor);
	}
	
	// Loop through array of markers & place each one on the map  
	for( i = 0; i < locations.length; i++ ) {
		var position = new google.maps.LatLng(locations[i][1], locations[i][2]);
		bounds.extend(position);
		
		marker = new google.maps.Marker({
			position: position,
			map: map,
			
			title: locations[i][0]
		});	

		// Info window for each marker 
		google.maps.event.addListener(marker, 'click', (function(marker, i) {
			return function() {
				infoWindow.setContent(locations[i][0]);
				infoWindow.open(map, marker);
			}
		})(marker, i));
		
		// Automatically center the map fitting all markers on the screen
		map.fitBounds(bounds);
	}

	// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
	var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
		google.maps.event.removeListener(boundsListener);
	});
}
