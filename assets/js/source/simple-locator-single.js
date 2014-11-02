/**
* Single View Map Functionality
*/
jQuery(function($){
	
	$(document).ready(function(){
		loadmap();
	});

	function loadmap()
	{
		var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
		var position = new google.maps.LatLng( parseFloat(wpsl_locator_single.latitude), parseFloat(wpsl_locator_single.longitude) );
		var options = {
			center: position,
			zoom: 12,
			styles: wpsl_locator.mapstyles
		};
		var map = new google.maps.Map( document.getElementById('locationmap') , options );
		var marker = new google.maps.Marker({
			position: position,
			map: map,
			icon: mappin,
			title: wpsl_locator_single.title
		});
	}

}); // jQuery