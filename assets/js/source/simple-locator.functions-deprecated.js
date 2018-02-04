/**
* Callback functions available to users
* Place in Theme scripts to perform actions after map has rendered
* Theme scripts should be enqueued with a script dependency for 'simple-locator'
* Deprecated as of version 2 in place of events.
*/

// Replaced with data-attribute
function openInfoWindow(id){
	google.maps.event.trigger(SimpleLocator.markers[0][id], 'click');
	var lat = SimpleLocator.markers[0][id].getPosition().lat();
	var lng = SimpleLocator.markers[0][id].getPosition().lng();
	var position = new google.maps.LatLng(lat,lng);
	SimpleLocator.maps[0].panTo(position);
	SimpleLocator.maps[0].fitBounds(position);
	SimpleLocator.maps[0].setZoom(12);
	return false;
}

// Runs before form has been submitted/after click
function wpsl_before_submit(active_form, formelements){}

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

// Runs after locations map has rendered
function wpsl_all_locations_rendered(map){}

// Runs after clicking on a marker in all locations map
function wpsl_all_locations_marker_clicked(marker, infoWindow){}