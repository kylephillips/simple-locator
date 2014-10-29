/**
* Map Style Selection under plugin settings
*/
jQuery(function($){

	$(document).ready(function(){
		loopMaps();
	});

	/**
	* Load the Map Choices
	*/
	function loopMaps()
	{
		$.each(wpsl_locator_mapstyles, function(i, map){
			buildDOM(i, map);
			loadMap(map);
		});
	}

	/**
	* Build the DOM tree for the maps
	* @todo add logic to designate active choice
	*/
	function buildDOM(i, map)
	{
		var out = '<li';
		if ( i % 3 === 0 ) out += ' class="first"';
		out += '>';
		out += '<h4>' + map.title + '</h4>';
		out += '<div class="map" id="map_' + map.id + '"></div>';
		out += '<a href="' + map.id + '" class="choose-style">';
		out += 'Choose';
		out += '</a>';
		out += '</li>';
		$('#map-styles').append(out);
	}

	/**
	* Load the Google Maps
	*/
	function loadMap(map)
	{
		var styles = JSON.parse(map.styles);
		var container = 'map_' + map.id;
		var mapOptions = {
			center: new google.maps.LatLng(40.7699354,-73.9810812),
			zoom: 13,
			mapTypeControl: false,
			streetViewControl: false,
			styles: styles
		}
		var map = new google.maps.Map(document.getElementById(container),mapOptions);
	}

});