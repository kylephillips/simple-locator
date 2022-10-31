/**
* Map Display Page
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.MapDisplay = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		styleChoiceSelect : 'data-simple-locator-map-style-choice',
		styleChoice : 'data-simple-locator-map-style',
		styleList : 'data-simple-locator-map-style-list',
		styleSelect : 'data-simple-locator-map-style-select',
		styleChoiceInput : 'data-simple-locator-map-style-choice-input',
		customOptionsCheckbox : 'data-simple-locator-custom-map-options-checkbox',
		customOptions : 'data-simple-locator-custom-map-options',
		customAutocompleteCheckbox : 'data-simple-locator-custom-autocomplete-option',
		customAutocomplete : 'data-simple-locator-custom-autocomplete',
		clusterRendererCheckbox : 'data-simple-locator-custom-marker-clustering-option',
		clusterRenderer : 'data-simple-locator-clustering-renderer'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( $('[' + self.selectors.styleList + ']').length < 1 ) return;
			self.buildMapChoices();
		});
		$(document).on('change', '[' + self.selectors.styleChoiceSelect + ']', function(){
			var choice = $(this).val();
			self.toggleChoice(choice);
		});
		$(document).on('click', '[' + self.selectors.styleSelect + ']', function(e){
			e.preventDefault();
			self.selectStyle($(this));
		});
		$(document).on('change', '[' + self.selectors.customOptionsCheckbox + ']', function(){
			var checked = $(this).is(':checked');
			if ( checked ){
				$('[' + self.selectors.customOptions + ']').show();
				return;
			}
			$('[' + self.selectors.customOptions + ']').hide();
		});
		$(document).on('change', '[' + self.selectors.customAutocompleteCheckbox + ']', function(){
			self.toggleAutocompleteOptions();
		});
		$(document).on('change', '[' + self.selectors.clusterRendererCheckbox + ']', function(){
			self.toggleClusterRenderer();
		});
	}

	/**
	* Toggle the map style choice
	*/
	self.toggleChoice = function(choice)
	{
		var choice = $('[' + self.selectors.styleChoice + '=' + choice + ']');
		$('[' + self.selectors.styleChoice + ']').hide();
		$(choice).show();
	}

	/**
	* Build the map choices
	*/
	self.buildMapChoices = function()
	{
		$('[' + self.selectors.styleList + ']').empty();
		$.each(wpsl_locator_mapstyles, function(i, map){
			self.buildMapHtml(i, map);
			self.loadChoiceMap(map);
		});
	}

	/**
	* Build a single map element 
	*/
	self.buildMapHtml = function(i, map)
	{
		var out = '<li class="';
		if ( i % 3 === 0 ) out += 'first';
		if ( map.selected ) out += ' active';
		out += '">';
		out += '<h4>' + map.title + '</h4>';
		out += '<div class="map" id="map_' + map.id + '"></div>';
		out += '<a href="' + map.id + '" class="choose-style" ' + self.selectors.styleSelect + '>';
		out += 'Use Style';
		out += '</a>';
		out += '</li>';
		$('[' + self.selectors.styleList + ']').append(out);
	}

	/**
	* Load the choice map
	*/
	self.loadChoiceMap = function(map)
	{
		var styles = JSON.parse(map.styles);
		var container = 'map_' + map.id;
		var mapOptions = {
			center: new google.maps.LatLng(40.7699354, -73.9810812),
			zoom: 13,
			mapTypeControl: false,
			streetViewControl: false,
			styles: styles
		}
		var map = new google.maps.Map(document.getElementById(container),mapOptions);
	}

	/**
	* Select a style from the list
	*/
	self.selectStyle = function(button)
	{
		var style_id = $(button).attr('href');
		$('[' + self.selectors.styleChoiceInput + ']').val(style_id);
		$('[' + self.selectors.styleList + '] li').removeClass('active');
		$(button).parent('li').addClass('active');
	}

	/**
	* Toggle Autocomplete Options
	*/
	self.toggleAutocompleteOptions = function()
	{
		if ( $('[' + self.selectors.customAutocompleteCheckbox + ']').is(':checked') ){
			$('[' + self.selectors.customAutocomplete + ']').show();
			return;
		}
		$('[' + self.selectors.customAutocomplete + ']').hide();
	}

	/**
	* Toggle Clustering Renderer Field
	*/
	self.toggleClusterRenderer = function()
	{
		if ( $('[' + self.selectors.clusterRendererCheckbox + ']').is(':checked') ){
			$('[' + self.selectors.clusterRenderer + ']').show();
			return;
		}
		$('[' + self.selectors.clusterRenderer + ']').hide();
	}

	return self.bindEvents();
}