/**
* Map Style Selection under plugin settings
*/
jQuery(function($){

	$(document).ready(function(){
		loopMaps();
	});

	$(document).on('click', '.choose-style', function(e){
		e.preventDefault();
		selectMapStyle($(this));
	});

	$(document).on('change', '#wpsl_map_styles_type', function(){
		if ( $(this).val() === 'choice' ) loopMaps();
	});

	$(document).on('change', '#wpsl_custom_map_options', function(){
		if ( $(this).is(':checked') ){
			$('#wpsl_map_options').show();
			return;
		}
		$('#wpsl_map_options').hide();
	});

	/**
	* Select a Map Style
	*/
	function selectMapStyle(item)
	{
		var style_id = $(item).attr('href');
		$('#wpsl_map_styles_choice').val(style_id);
		$('#map-styles li').removeClass('active');
		$(item).parent('li').addClass('active');
	}

	/**
	* Load the Map Choices
	*/
	function loopMaps()
	{
		$('#map-styles').empty();
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
		var out = '<li class="';
		if ( i % 3 === 0 ) out += 'first';
		if ( map.selected ) out += ' active';
		out += '">';
		out += '<h4>' + map.title + '</h4>';
		out += '<div class="map" id="map_' + map.id + '"></div>';
		out += '<a href="' + map.id + '" class="choose-style">';
		out += 'Use Style';
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