/**
* Open an infowindow when clicking a link
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.InfoWindowOpen = function()
{
	var self = this;
	var $ = jQuery;

	self.mapIndex;
	self.markerIndex;

	self.bindEvents = function()
	{
		$(document).on('click', '[' + SimpleLocator.selectors.infoWindowLink + ']', function(e){
			e.preventDefault();
			self.setIndexes($(this));
			self.openInfoWindow();
		});
	}

	/**
	* Set the map index
	*/
	self.setIndexes = function(link)
	{
		var activeList = $(link).parents('[' + SimpleLocator.selectors.resultsWrapper + ']');
		var lists = $('[' + SimpleLocator.selectors.resultsWrapper + ']');
		self.mapIndex = $(activeList).index(lists);
		self.markerIndex = parseInt($(link).attr(SimpleLocator.selectors.infoWindowLink));
		console.log(self.mapIndex);
		console.log(self.markerIndex);
		console.log(SimpleLocator.markers);
	}

	/**
	* Open the infowindow
	*/
	self.openInfoWindow = function()
	{
		google.maps.event.trigger(SimpleLocator.markers[self.mapIndex][self.markerIndex], 'click');
		$(document).trigger('simple-locator-infowindow-opened', [self.mapIndex, self.markerIndex]);
	}

	return self.bindEvents();
}