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
function wpsl_after_render(active_form){}

// Runs on click event on a map marker
function wpsl_click_marker(marker, i, active_form, post_id){}

// Runs if no results were returned from the query
function wpsl_no_results(location, active_form){}

// Runs on form error
function wpsl_error(message, active_form){}

// Runs immediately on form success, pre-render of map/results
function wpsl_success(resultcount, results, active_form){}

// Returns the Google Maps Response
function wpsl_googlemaps_response(){
	return googlemaps_response;
}

var active_form = '';
var formatted_address = '';
var googlemaps_response = '';
var geolocation = false;


jQuery(function($){

$(document).ready(function(){
	enable_autocomplete();
	queue_default_map();
});


/**
* Enable Places Autocomplete on the search form
*/
function enable_autocomplete()
{
	if ( wpsl_locator.autocomplete !== '1' ) return;
	var inputs = $('.wpsl-search-form');
	$.each(inputs, function(i, v){
		var autocomplete = new google.maps.places.Autocomplete(this);
		var submitBtn = $(this).parents('form').find('.wpslsubmit');
		google.maps.event.addListener(autocomplete, 'place_changed', function(){
			$(submitBtn).click();
		});
	});
}

/**
* Queue the default map
* @param boolean errors (geolocation errors) - if true, show default map (not user centered)
*/
function queue_default_map(errors)
{
	if ( wpsl_locator.default_enabled ) {
		if ( wpsl_locator.default_user_center === 'true' && navigator.geolocation && !errors ){
			var forms = $('.simple-locator-form');
			$.each(forms, function(i, v){
				var formelements = setFormElements($(this));
				$(formelements.results).empty().addClass('loading').show();
				navigator.geolocation.getCurrentPosition(function(position){
					process_geo_button(position, formelements);
				}, function(error){
					queue_default_map(true);
					$(formelements.results).empty().removeClass('loading').hide();
				});
			});
		} else {
			loadDefault();
		}
	}	
}

function default_load_results()
{
	var forms = $('.simple-locator-form');
}

// Process the Search Form
$('.wpslsubmit').on('click', function(e){
	e.preventDefault();
	geolocation = false;
	var form = $(this).parents('.simple-locator-form');
	active_form = form;
	var formelements = setFormElements(form);

	$(formelements.errordiv).hide();

	if ( wpsl_locator.default_enabled ){
		$(formelements.map).find('.gm-style').remove();
	} else {
		$(formelements.map).hide();
	}

	$(formelements.results).empty().addClass('loading').show();

	generate_nonce(form, formelements, true);
});


/**
* Generate and Inject the Nonce
*/
function generate_nonce(form, formelements, processform)
{
	$.ajax({
		url: wpsl_locator.ajaxurl,
		type: 'post',
		datatype: 'json',
		data: {
			action : 'locatornonce'
		},
		success: function(data){
			$('.locator-nonce').remove();
			$(form).find('form').append('<input type="hidden" class="locator-nonce" name="nonce" value="' + data.nonce + '" />');
			if ( processform ) geocodeAddress(formelements);
		}
	});
}



/**
* Load default map if enabled
* @param boolean
*/
function loadDefault(userlocation, position)
{
	var latitude = wpsl_locator.default_latitude;
	var longitude = wpsl_locator.default_longitude;

	var forms = $('.simple-locator-form');
	$.each(forms, function(i, v){
		formelements = setFormElements(this);
		formelements.map.show();
		var center = new google.maps.LatLng(latitude, longitude);
		var mapOptions = {
			center: center,
			zoom: parseInt(wpsl_locator.default_zoom),
			mapTypeControl: false,
			streetViewControl: false,
			styles: wpsl_locator.mapstyles
		}
		var map = new google.maps.Map(formelements.map[0],mapOptions);
	});
}


/**
* Set the form elements
*/
function setFormElements(form)
{
	var mapcont = '.wpsl-map';
	var resultscontainer = '.wpsl-results';

	// Get the DOM elements for results. Either a class within the form or a unique ID
	if ( ( $(active_form).siblings('#widget').length < 1 ) ){ // Not the Widget
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
	} else { // Its the widget
		var mapcont = $(active_form).find(mapcont);
		var resultscontainer = $(active_form).find(resultscontainer);
	}

	formelements = {
		'parentdiv' : $(form),
		'errordiv' : $(form).find('.wpsl-error'),
		'map' : mapcont,
		'results' : resultscontainer,
		'distance' : $(form).find('.distanceselect'),
		'address' : $(form).find('.address'),
		'latitude' : $(form).find('.latitude'),
		'longitude' : $(form).find('.longitude'),
		'unit' : $(form).find('.unit'),
		'form' : $(form).find('form')
	}
	return formelements;
}


/**
* Geocode the address prior to submitting the search form
*/
function geocodeAddress(formelements)
{
	var address = $(formelements.address).val();
	
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'address' : address
	}, function(results, status){

		if ( status == google.maps.GeocoderStatus.OK ){
			googlemaps_response = results;
			var latitude = results[0].geometry.location.lat();
			var longitude = results[0].geometry.location.lng();
			formatted_address = results[0].formatted_address;
			
			$(formelements.latitude).val(latitude);
			$(formelements.longitude).val(longitude);
			
			if ( $(formelements.form).find('#wpsl_action').length === 0 ){
				return sendFormData(formelements);
			}
			return appendNonAjaxFields(formelements);

		} else {
			wpsl_error(wpsl_locator.notfounderror, active_form);
			$(formelements.errordiv).text(wpsl_locator.notfounderror).show();
			$(formelements.results).hide();
		}
	});
}


/**
* Append fields to non-ajax forms
*/
function appendNonAjaxFields(formelements)
{
	$(formelements.form).append('<input type="hidden" name="formatted_address" value="' + formatted_address + '">');
	$(formelements.form).append('<input type="hidden" name="geolocation" value="' + geolocation + '">');
	$(formelements.form).submit();
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
			action : 'locate',
			address : $(formelements.address).val(),
			formatted_address : formatted_address,
			locatorNonce : $('.locator-nonce').val(),
			distance : $(formelements.distance).val(),
			latitude : $(formelements.latitude).val(),
			longitude : $(formelements.longitude).val(),
			unit : $(formelements.unit).val(),
			geolocation : geolocation
		},
		success: function(data){
			if (data.status === 'error'){
				wpsl_error(data.message, active_form);
				$(formelements.errordiv).text(data.message).show();
				$(formelements.results).hide();
				$(formelements.map).hide();
			} else {
				wpsl_success(data.result_count, data.results, active_form);
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

		var output = '<h3>' + data.result_count + ' ' + location + ' ' + wpsl_locator.found_within + ' ' + data.distance + ' ' + data.unit + ' of ';
		output += ( data.using_geolocation === "true" ) ? wpsl_locator.yourlocation : data.formatted_address;
		output += '</h3><ul>';
		
		for( i = 0; i < data.results.length; i++ ) {
			output = output + '<li data-result=' + i + '>' + data.results[i].output + '</li>';
		}

		output = output + '</ul>';

		$(formelements.results).removeClass('loading').html(output);

		$(formelements.map).show();
		$(formelements.zip).val('').blur();
		showLocationMap(data, formelements);

		// Simple Locator Callback function after results have rendered
		wpsl_after_render(active_form);

	} else {
		// No results were returned
		wpsl_no_results(data.formatted_address, active_form);
		$(formelements.errordiv).text(wpsl_locator_options.noresultstext).show();
		$(formelements.results).hide();
		$(formelements.map).hide();
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
		var controlposition = "TOP_LEFT";
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
		var id = data.results[i].id;
		var location = [title,lat,lng,link,id];
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
				infoWindow.setContent('<h4>' + locations[i][0] + '</h4><p><a href="' + locations[i][3] + '" data-location-id="' + locations[i][4] + '">' + wpsl_locator.viewlocation + '</a></p>');
				infoWindow.open(map, marker);

				// Simple Locator Callback function for click event
				wpsl_click_marker(marker, i, active_form, locations[i][4]);
			}
		})(marker, i));

		 // Push the marker to the global 'markers' array
        markers.push(marker);
		
		// Center the Map
		map.fitBounds(bounds);
		var listener = google.maps.event.addListener(map, "idle", function() { 
				if ( data.results.length < 2 ) {
				map.setZoom(13);
			}
			google.maps.event.removeListener(listener); 
		});
	}

	// Fit the map bounds to all the pins
	var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
		google.maps.event.removeListener(boundsListener);
	});

	// Set the global map var
	googlemap = map;

}

/**
* ======================================================
* Get User Location (for initial map load)
* ======================================================
*/

/**
* Returns user coordinates if available, false if not
*/
function get_user_coordinates()
{
	var coords = {};
	if (navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(position){
			loadDefault(true, position);
		});
	}
	return false;
}
function user_coordinates_found(position)
{

}


/**
* ======================================================
* Geolocation button
* ======================================================
*/
function append_geo_button()
{
	if ( wpsl_locator.showgeobutton !== 'true' ) return false;
	if (navigator.geolocation){
		var html = '<button class="wpsl-geo-button">' + wpsl_locator.geobuttontext + '</button>';
		$('.geo_button_cont').html(html);
	}

}

function process_geo_button(position, formelements)
{
	var longitude = position.coords.longitude;
	var latitude = position.coords.latitude;

	$(formelements.latitude).val(latitude);
	$(formelements.longitude).val(longitude);

	geolocation = true;
	
	sendFormData(formelements);
}

$(document).ready(function(){
	append_geo_button();
	$('.simple-locator-form').each(function(){
		generate_nonce($(this));
	});
});

$(document).on('click', '.wpsl-geo-button', function(e){
	e.preventDefault();
	
	var form = $(this).parents('.simple-locator-form');
	active_form = form;
	var formelements = setFormElements(form);

	$(formelements.errordiv).hide();
	
	if ( wpsl_locator.default_enabled ){
		$(formelements.map).find('.gm-style').remove();
	} else {
		$(formelements.map).hide();
	}
	
	$(formelements.results).empty().addClass('loading').show();

	navigator.geolocation.getCurrentPosition(function(position){
		process_geo_button(position, formelements);
	});
});	

}); // jQuery
