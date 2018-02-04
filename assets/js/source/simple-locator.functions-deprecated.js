/**
* Callback functions available to users
* Place in Theme scripts to perform actions after map has rendered
* Theme scripts should be enqueued with a script dependency for 'simple-locator'
* Deprecated as of version 2 in place of events.
*/

// Replaced with data-simple-locator-open-infowindow attribute on link
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
// Replaced with event: simple-locator-before-submit[form]
function wpsl_before_submit(active_form, formelements){}

// Runs after map & results render
// Replaced with two events: simple-locator-map-rendered[mapIndex, form], simple-locator-results-rendered[listIndex, form]
function wpsl_after_render(active_form){}

// Runs on click event on a map marker
// Replaced with event: simple-locator-marker-clicked[marker, index, form, post_id]
function wpsl_click_marker(marker, i, active_form, post_id){}

// Runs if no results were returned from the query
// Replaced with event: simple-locator-error[error, form, message]
function wpsl_no_results(location, active_form){}

// Runs on form error
// Replaced with event: simple-locator-error[error, form, message]
function wpsl_error(message, active_form){}

// Runs immediately on form success, pre-render of map/results
// Replaced with event: simple-locator-form-success[data, form]
function wpsl_success(resultcount, results, active_form){}

// Returns the Google Maps Response
function wpsl_googlemaps_response(){
	return googlemaps_response;
}

// Runs after locations map has rendered
// Replaced with event: simple-locator-all-locations-rendered[map]
function wpsl_all_locations_rendered(map){}

// Runs after clicking on a marker in all locations map
// Replaced with event: simple-locator-all-locations-marker-clicked[marker, infoWindow]
function wpsl_all_locations_marker_clicked(marker, infoWindow){}