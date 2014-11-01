/**
* Open a map info window, panto and zoom to bounds when a result is clicked
*/
function openInfoWindow(id){
	google.maps.event.trigger(markers[id], 'click');
	googlemap.panTo(markers[id].getPosition());
	googlemap.fitBounds(markers[id].getPosition());
	googlemap.setZoom(12);
	return false;
}
var markers = [];
var googlemap = '';


/**
* Callback functions available to users
* Place in Theme scripts to perform actions after map has rendered
*/

// Runs after map & results render
function wpsl_after_render(){}

// Runs on click event on a map marker
function wpsl_click_marker(marker, i){}

// Runs if no results were returned from the query
function wpsl_no_results(searchterm){}

// Runs on form error
function wpsl_error(message){}

// Runs immediately on form success, pre-render of map/results
function wpsl_success(resultcount, results){}



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
	var mapcont = '.wpsl-map';
	var resultscontainer = '.wpsl-results';

	// Get the DOM elements for results. Either a class within the form or a unique ID
	if ( (typeof wpsl_locator_options != "undefined") ){ // Not the Widget
		if ( wpsl_locator_options.mapcont.charAt(0) === '.' ){
			var mapcont = $(form).find(wpsl_locator_options.mapcont);
		} else {
			var mapcont = $(wpsl_locator_options.mapcont);
		}

		if ( wpsl_locator_options.resultscontainer.charAt(0) === '.' ){
			var resultscontainer = $(form).find(wpsl_locator_options.resultscontainer);
		} else {
			var resultscontainer = $(wpsl_locator_options.resultscontainer);
		}
	}

	formelements = {
		'parentdiv' : $(form),
		'errordiv' : $(form).find('.wpsl-error'),
		'map' : mapcont,
		'results' : resultscontainer,
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
			wpsl_error('Address not found.');
			$(formelements.errordiv).text('Address not found.').show();
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
			console.log(data);
			if (data.status === 'error'){
				wpsl_error(data.message);
				$(formelements.errordiv).text(data.message).show();
				$(formelements.results).hide();
			} else {
				wpsl_success(data.result_count, data.results);
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

		var location = ( data.result_count === 1 ) ? wpsl_locator.location : wpsl_locator.locations;

		var output = '<h3>' + data.result_count + ' ' + location + ' ' + wpsl_locator.found_within + ' ' + data.distance + ' ' + data.unit + ' of ' + data.zip + '</h3><ul>';
		
		for( i = 0; i < data.results.length; i++ ) {
			
			output = output + '<li data-result=' + i + '><strong>';
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

			output += '<br /><a href="#" class="infowindow-open map-link" onClick="event.preventDefault(); openInfoWindow(' + i + ');">' + wpsl_locator.showonmap + '</a>';
			output = output + '</li>';
		}

		output = output + '</ul>';

		$(formelements.results).removeClass('loading').html(output);

		$(formelements.map).show();
		$(formelements.zip).val('').blur();
		showLocationMap(data, formelements);

		// Simple Locator Callback function after results have rendered
		wpsl_after_render();

	} else {
		// No results were returned
		wpsl_no_results(data.zip);
		$(formelements.errordiv).text('No results found.').show();
		$(formelements.results).hide();
	}
}


/**
* Load the Google map and show locations found
*/
function showLocationMap(data, formelements)
{
	// Reset the global markers array
	markers = [];

	// Set the optional user parameters
	var mapstyles = wpsl_locator.mapstyles;	
	var mapcont = $(formelements.map)[0];
	
	if ( typeof wpsl_locator_options != 'undefined' ){
		var disablecontrols = ( wpsl_locator_options.mapcontrols === 'show') ? false : true;
	} else {
		var disablecontrols = false;
	}

	// Control Position
	if ( typeof wpsl_locator_options != 'undefined' ){
		var controlposition = google.maps.ControlPosition[wpsl_locator_options.mapcontrolsposition];
	} else {
		var controlposition = TOP_LEFT;
	}
	
	var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
	var map;
	var bounds = new google.maps.LatLngBounds();
	var mapOptions = {
			mapTypeId: 'roadmap',
			mapTypeControl: false,
			zoom: 8,
			styles: mapstyles,
			panControl : false,
			disableDefaultUI: disablecontrols,
			zoomControlOptions : {
				style: google.maps.ZoomControlStyle.SMALL,
				position : controlposition
			}
		}
	var locations = [];
	var infoWindow = new google.maps.InfoWindow(), marker, i;
	
	map = new google.maps.Map( mapcont, mapOptions );
	
	// Array of locations
	for (var i = 0, length = data.results.length; i < length; i++) {
		var title = data.results[i].title;
		var lat = data.results[i].latitude;
		var lng = data.results[i].longitude;
		var link = data.results[i].permalink;
		var location = [title,lat,lng,link];
		locations.push(location);
	}
	
	// Loop through array of markers & place each one on the map  
	for( i = 0; i < locations.length; i++ ) {
		var position = new google.maps.LatLng(locations[i][1], locations[i][2]);
		bounds.extend(position);
		
		marker = new google.maps.Marker({
			position: position,
			map: map,
			title: locations[i][0],
			icon: mappin
		});	

		// Info window for each marker 
		google.maps.event.addListener(marker, 'click', (function(marker, i) {
			return function() {
				infoWindow.setContent('<h4>' + locations[i][0] + '</h4><p><a href="' + locations[i][3] + '">' + wpsl_locator.viewlocation + '</a></p>');
				infoWindow.open(map, marker);

				// Simple Locator Callback function for click event
				wpsl_click_marker(marker, i);
			}
		})(marker, i));

		 // Push the marker to the global 'markers' array
        markers.push(marker);
		
		// Center the Map
		map.fitBounds(bounds);
	}

	// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
	var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
		google.maps.event.removeListener(boundsListener);
	});

	// Set the global map var
	googlemap = map;

}

}); // jQuery
