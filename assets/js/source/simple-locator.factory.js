/**
* Primary Simple Locator Initialization
* @package Simple Locator
* @author Kyle Phillips - https://github.com/kylephillips
*
* Document Events:
* simple-locator-error[form, message]
* simple-locator-address-geocoded[results, form]
* simple-locator-autocomplete-changed[place, form]
* simple-locator-form-success[results, form]
* simple-locator-infowindow-opened[mapIndex, markerIndex]
* simple-locator-geolocation-available-set[available]
* simple-locator-geolocation-success[form]
* simple-locator-marker-clicked[marker, markerIndex, form, locationId]
* simple-locator-results-rendered[listIndex, form]
*/

jQuery(document).ready(function(){
	new SimpleLocator.Factory;
});

var SimpleLocator = SimpleLocator || {};

// DOM Selectors
SimpleLocator.selectors = {
	resultsWrapper : 'data-simple-locator-results-wrapper',
	form : 'data-simple-locator-form',
	formContainer : 'data-simple-locator-form-container',
	formError : 'data-simple-locator-form-error',
	results : 'data-simple-locator-results',
	map : 'data-simple-locator-map',
	mapNonAjax : 'data-simple-locator-map-non-ajax',
	inputAddress : 'data-simple-locator-input-address',
	inputLatitude : 'data-simple-locator-input-latitude',
	inputLongitude : 'data-simple-locator-input-longitude',
	inputUnit : 'data-simple-locator-input-unit',
	inputDistance : 'data-simple-locator-input-distance',
	inputFormattedLocation : 'data-simple-locator-input-formatted-location',
	inputGeocode : 'data-simple-locator-input-geocode',
	inputLimit : 'data-simple-locator-input-limit',
	submitButton : 'data-simple-locator-submit',
	infoWindowLink : 'data-simple-locator-open-infowindow',
	geoButton : 'data-simple-locator-geolocation-button',
	ajaxForm : 'data-simple-locator-ajax-form',
	paginationButton : 'data-simple-locator-pagination'
}

// API Endpoints
SimpleLocator.endpoints = {
	search : wpsl_locator.rest_url + '/search',
	locations : wpsl_locator.rest_url + '/locations'
}

// Map Objects
SimpleLocator.maps = [];

// Map Markers
SimpleLocator.markers = [];
SimpleLocator.userPin = [];

/**
* Primary Simple Locator Class
*/
SimpleLocator.Factory = function()
{
	var self = this;
	var $ = jQuery;

	self.init = function()
	{
		new SimpleLocator.Geocoder;
		new SimpleLocator.Geolocation;
		new SimpleLocator.PlacesAutocomplete;
		new SimpleLocator.DefaultMap;
		new SimpleLocator.ResultsMapNonAjax;
		new SimpleLocator.SingleLocation;
		new SimpleLocator.AllLocations;
		new SimpleLocator.Form;
		new SimpleLocator.ResultsMap;
		new SimpleLocator.ResultsList;
		new SimpleLocator.InfoWindowOpen;
		new SimpleLocator.Errors;
	}

	return self.init();
}