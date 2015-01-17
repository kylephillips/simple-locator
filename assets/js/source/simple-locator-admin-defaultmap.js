/**
* Default Map under plugin settings
*/
jQuery(function($){

	$(document).ready(function(){
		if ( wpsl_locator_defaultmap.enabled ){
			hide_show_default('show');
		}
	});

	$('#wpsl_default_map_enable').on('change', function(){
		if ( $(this).is(':checked') ) {
			hide_show_default('show');
		} else {
			hide_show_default('hide');
		}
	});

	$('#wpsl_default_submit').on('click', function(e){
		e.preventDefault();
		location_search();
	});

	/**
	* Hide/Show the default map options
	*/
	function hide_show_default(show)
	{
		if ( show === 'show' ){
			$('.wpsl-default-map').show();
			load_map();
		} else {
			$('.wpsl-default-map').hide();
		}
	}


	/**
	* Load Default Map
	*/
	function load_map(lat, lng)
	{
		if ( !lat ) lat = wpsl_locator_defaultmap.latitude;
		if ( !lng ) lng = wpsl_locator_defaultmap.longitude;

		var center = new google.maps.LatLng(lat, lng);
		var mapOptions = {
			center: center,
			zoom: parseInt(wpsl_locator_defaultmap.zoom),
			mapTypeControl: false,
			streetViewControl: false,
			styles: wpsl_locator_defaultmap.styles
		}
		var map = new google.maps.Map(document.getElementById('wpsl-default'),mapOptions);
		var marker = new google.maps.Marker({
			position: center,
			map: map,
			icon: wpsl_locator_defaultmap.mappin,
			draggable: true
		});

		// Set the Zoom Level on Change
		google.maps.event.addListener(map, 'zoom_changed', function(){
			var zoomLevel = map.getZoom();
			$('#wpsl_default_zoom').val(zoomLevel);
		});

		// Make Marker Draggable and update on change
		google.maps.event.addListener(marker, 'drag', function(){
			$('#wpsl_default_latitude').val(marker.position.lat());
			$('#wpsl_default_longitude').val(marker.position.lng());
		});
	}


	/**
	* Submit Google Search
	*/
	function location_search()
	{
		var searchterm = $('#wpsl_default_search').val();
		geocoder = new google.maps.Geocoder();
			
		geocoder.geocode({
			'address' : searchterm
		}, 
		function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				load_results(lat, lng);
			} else {
				displayErrorModal();
			}
		});
	}


	/**
	* Set the coordinates for result and load map
	*/
	function load_results(lat, lng)
	{
		load_map(lat, lng);
		$('#wpsl_default_latitude').val(lat);
		$('#wpsl_default_longitude').val(lng);
	}


	/**
	* Display the error modal
	*/
	function displayErrorModal()
	{
		$('#wpsl-error-modal').modal('show');
	}


}); // jQuery