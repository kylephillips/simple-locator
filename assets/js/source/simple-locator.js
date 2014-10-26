jQuery(function($){


$('.wpslsubmit').on('click', function(e){
	e.preventDefault();
	var form = $(this).parents('.simple-locator-form');
	var formelements = setFormElements(form);

	$(formelements.errordiv).hide();
	$(formelements.map).hide();
	$(formelements.results).empty().addClass('loading').show();

	geocodeZip(formelements);
});


/**
* Set the form elements
*/
function setFormElements(form)
{
	formelements = {
		'parentdiv' : $(form),
		'errordiv' : $(form).find('.wpsl-error'),
		'map' : $(form).find('.wpsl-map'),
		'results' : $(form).find('.wpsl-results'),
		'distance' : $(form).find('.distanceselect'),
		'zip' : $(form).find('.zipcode'),
		'latitude' : $(form).find('.latitude'),
		'longitude' : $(form).find('.longitude'),
		'unit' : $(form).find('.unit')
	}
	return formelements;
}


/**
* Geocode the zip prior to submitting the search form
*/
function geocodeZip(formelements)
{
	var zip = $(formelements.zip).val();
	console.log(zip)
	
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'address' : zip
	}, function(results, status){

		if ( status == google.maps.GeocoderStatus.OK ){

			var latitude = results[0].geometry.location.lat();
			var longitude = results[0].geometry.location.lng();
			
			$(formelements.latitude).val(latitude);
			$(formelements.longitude).val(longitude);
			
			sendFormData(formelements);

		} else {
			$(formelements.errordiv).text('Address could not be found at this time.').show();
			$(formelements.results).hide();
		}
	});
}



/**
* Send the form data to the form handler
*/
function sendFormData(formelements)
{
	$.ajax({
		url: wpsl_locator.ajaxurl,
		type: 'post',
		datatype: 'json',
		data: {
			action: 'locate',
			zip: $(formelements.zip).val(),
			locatorNonce : wpsl_locator.locatorNonce,
			distance: $(formelements.distance).val(),
			latitude: $(formelements.latitude).val(),
			longitude: $(formelements.longitude).val(),
			unit: $(formelements.unit).val()
		},
		success: function(data){
			if (data.status === 'error'){
				$(formelements.errordiv).text(data.message).show();
				$(formelements.results).hide();
			} else {
				loadLocationResults(data, formelements);
			}
		}
	});
}




/**
* Load the results into the view
*/
function loadLocationResults(data, formelements)
{
	if ( data.result_count > 0 ){

		if ( data.result_count === 1 ){
			var location = wpsl_locator.location;
		} else {
			var location = wpsl_locator.locations;
		}

		var output = '<h3>' + data.result_count + ' ' + location + ' ' + wpsl_locator.found_within + ' ' + data.distance + ' ' + data.unit + ' of ' + data.zip + '</h3><ul>';
		
		for( i = 0; i < data.results.length; i++ ) {
			
			output = output + '<li><strong>';
			output = output + '<a href="' + data.results[i].permalink + '">';
			output = output + data.results[i].title;
			output = output + '</a></strong><br />';
			output = output + '<em>' + wpsl_locator.distance + ': ' + data.results[i].distance + ' ' + data.unit + '</em><br />';

			if ( data.results[i].address ){
			output = output + data.results[i].address + '<br />' + data.results[i].city + ', ' + data.results[i].state + ' ' + data.results[i].zip;
			}

			var phone = data.results[i].phone;
			var website = data.results[i].website;
			
			if ( phone ){
				output = output + '<br />' + wpsl_locator.phone + ': ' + phone;
			}
			if ( website ){
				output = output + '<br /><a href="' + website + '" target="_blank">' + website + '</a>';
			}
			output = output + '</li>';
		}

		output = output + '</ul>';

		$(formelements.results).removeClass('loading').html(output);

		$(formelements.map).show();
		showLocationMap(data, formelements);

	} else {
		// No results were returned
		$(formelements.errordiv).text('No results found.').show();
		$(formelements.results).hide();
	}
}


/**
* Load the Google map and show locations found
*/
function showLocationMap(data, formelements)
{
	var mapstyles = wpsl_locator.mapstyles;
	var mapstyles = $.parseJSON(mapstyles);

	var mapcont = $(formelements.map)[0];
	var map;
	var bounds = new google.maps.LatLngBounds();
	var mapOptions = {
			mapTypeId: 'roadmap',
			mapTypeControl: false,
			zoom: 8,
			styles: mapstyles
		}
		console.log(mapOptions);
	var locations = [];
	var infoWindow = new google.maps.InfoWindow(), marker, i;
	map = new google.maps.Map(mapcont, mapOptions);
	
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

}); // jQuery
