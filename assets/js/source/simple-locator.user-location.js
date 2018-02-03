/**
* Gets the user's current location if available
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.UserLocation = function()
{
	var self = this;
	var $ = jQuery;

	/**
	* Returns user location if available, null if not
	*/
	self.getLocation = function()
	{
		// if ( !SimpleLocator.jsData.secure ) return null;
		var location = [];
		navigator.geolocation.getCurrentPosition(function(position){
			location['latitude'] = position.coords.latitude;
			location['longitude'] = position.coords.longitude;
		});
		console.log(location);
		return location;
	}

}