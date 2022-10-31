/**
* Utility Functions
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.Utilities = function()
{
	var self = this;
	var $ = jQuery;

	/**
	* Get the currently displayed radius/distance of a map
	* @return int
	*/
	self.getMapRadius = function(map)
	{
		var bounds = map.getBounds();
		var center = bounds.getCenter();
		var ne = bounds.getNorthEast();
		var r = 3963.0; // radius of earth in miles

		var lat1 = center.lat() / 57.2958; 
		var lon1 = center.lng() / 57.2958;
		var lat2 = ne.lat() / 57.2958;
		var lon2 = ne.lng() / 57.2958;

		var dis = r * Math.acos(Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1));
		return dis;
	}

	/**
	* Get a map center point
	* @return array [latitude, longitude]
	*/
	self.getMapCenterPoint = function(map)
	{
		var bounds = map.getBounds();
		var center = bounds.getCenter();
		var latlng = [center.lat(), center.lng()];
		return latlng;
	}

	/**
	* Cluster markers if set to do so
	*/
	self.clusterMarkers = function(map, markers)
	{
		var options = {
			map: map, 
			markers: markers
		}
		if ( typeof wpsl_locator.cluster_renderer !== 'undefined' && wpsl_locator.cluster_renderer !== '' ) options.renderer = wpsl_locator.cluster_renderer;
		if ( wpsl_locator.marker_clusters === '1' && typeof markerClusterer.MarkerClusterer !== 'undefined' ){
			const markerCluster = new markerClusterer.MarkerClusterer(options);
		}
	}
}