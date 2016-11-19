/**
* Global function catches Google API Errors
* @link https://developers.google.com/maps/documentation/javascript/events#auth-errors
*/
var editScreenGoogleApiError = false;
function gm_authFailure(e) {
	editScreenGoogleApiError = true;
}

jQuery(function($){

	var lookupaddress = true;
	var mappinrelocated = false;

	/**
	* ------------------------------------------------------
	* Location Post Type Entry Map Functions
	* ------------------------------------------------------
	*/

	/**
	* Geocode Address when saving Location Posts
	*/
	var form = $("form[name='post']");
	$(form).find("#publish").on('click', function(e){
		if ( wpsl_locator.lat_field !== 'wpsl_latitude' && wpsl_locator.map_field !== "" ) return;
		e.preventDefault();

		if ( editScreenGoogleApiError ){
			displayErrorModal(wpsl_locator.api_load_error);
			return;
		}
		var address = formatAddress();
		googleGeocodeAddress(address);
	});


	/**
	* Format the provided address to submit for geocoding
	*/
	function formatAddress()
	{
		var streetaddress = $('#wpsl_address').val();
		var city = $('#wpsl_city').val();
		var state = $('#wpsl_state').val();
		var zip = $('#wpsl_zip').val();
		var address = streetaddress + ' ' + city + ' ' + state + ' ' + zip;
		return address;
	}


	/**
	* Submit the address to Google for Geocoding
	*/
	function googleGeocodeAddress(address)
	{
		geocoder = new google.maps.Geocoder();
			
		geocoder.geocode({
			'address' : address
		}, 
		function(results, status){
			if ( lookupaddress == true && !mappinrelocated ){
				if ( status == google.maps.GeocoderStatus.OK ){
					var lat = results[0].geometry.location.lat();
					var lng = results[0].geometry.location.lng();
					setFormCoordinates(lat, lng);
					$('#publish').unbind('click').click();
				} else {
					displayErrorModal(wpsl_locator.address_not_found);
				}
			} else {
				$('#publish').unbind('click').click();
			}
		});
	}

	/**
	* Display the error modal
	*/
	function displayErrorModal(text)
	{
		$('#wpsl-error-modal').find('h3').text(text);
		$('#wpsl-error-modal').modal('show');
	}

	/**
	* Save the post without location data
	*/
	$('.wpsl-address-confirm').on('click', function(e){
		e.preventDefault()
		$('#wpsl-error-modal').modal('hide');
		lookupaddress = false;
		$('#publish').unbind('click').click();
	});


	/**
	* Set the Lat & Lng Form Fields
	*/
	function setFormCoordinates(lat, lng)
	{
		$('#wpsl_latitude').val(lat);
		$('#wpsl_longitude').val(lng);
	}


	/**
	* Check if the Location has Geocode Saved
	*/
	$(document).ready(function(){
		checkMapStatus();
		if ( $('#wpsl_custom_geo').val() === 'true' ) mappinrelocated = true;
	});
	// For custom ACF Tab Placement
	$(document).on('click', '.acf-tab-button', function(){
		checkMapStatus();
	});
	function checkMapStatus()
	{
		if ( $("#wpslmap").length > 0 ){
			var lat = $('#wpsl_latitude').val();
			var lng = $('#wpsl_longitude').val();
			if ( (lat !== "") && (lng !== "") ){
				$('#wpslmap').show();
				loadGoogleMap(lat, lng);
			}
		}
	}


	/*
	* Load the Google Map in Admin View
	*/
	function loadGoogleMap(lat, lng){
		var map = new google.maps.Map(document.getElementById('wpslmap'), {
			zoom: 14,
			center: new google.maps.LatLng(lat,lng),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false,
			scaleControl : false,
		});

		var marker, i;

		marker = new google.maps.Marker({
			position: new google.maps.LatLng(lat, lng),
			map: map,
			draggable: true
		});

		// Make Marker Draggable and update on change
		google.maps.event.addListener(marker, 'drag', function(){
			$('#wpsl_latitude').val(marker.position.lat());
			$('#wpsl_longitude').val(marker.position.lng());
			$('#wpsl_latitude, #wpsl_longitude').attr('readonly', false);
			$('#wpsl_custom_geo').val('true');
			mappinrelocated = true;
		});
	}

	/**
	* ------------------------------------------------------
	* Settings Page – Hide/Show location button text
	* ------------------------------------------------------
	*/
	function hideGeoText()
	{
		if ( $('#wpsl_geo_button_enable').is(':checked') ){
			$('.wpsl-location-text').show();
		} else {
			$('.wpsl-location-text').hide();
		}
	}

	$('#wpsl_geo_button_enable').on('change', function(){
		hideGeoText();
	});

	$(document).ready(function(){
		hideGeoText();
	});


	/**
	* ------------------------------------------------------
	* Settings Page – Post Type & Geocode Fields
	* ------------------------------------------------------
	*/
	$(document).ready(function(){
		post_type_labels();
		var pt = $('#wpsl_post_type').val();
		if ( pt !== wpsl_locator.posttype ){
			$('#field_wpsl').attr('disabled', 'disabled').removeAttr('checked');
			$('#field_custom').attr('checked','checked');
		}

		var ft = $('input[name="wpsl_field_type"]:checked').val();
	
		if ( ft === 'wpsl' ) $('.latlng').hide();
	});

	// Prevent the selection of wspl geo fields if another post type is selected
	$(document).on('change', '#wpsl_post_type, #wpsl_show_hidden', function(){
		load_posttype_fields();
		post_type_labels();
	});

	/**
	* Load the post type meta fields
	*/
	function load_posttype_fields()
	{
		var value = $('#wpsl_post_type').val();
		if ( value !== wpsl_locator.posttype ){
			$('#field_wpsl').attr('disabled', 'disabled').removeAttr('checked');
			$('#field_custom').attr('checked','checked');
			$('.latlng').show();
		} else {
			$('#field_wpsl').removeAttr('disabled');
			select_wpsl_fields();
		}

		// Load field selections with select post type's custom fields
		$('#lat_select, #lng_select').empty();
		get_fields_for_posttype(value);
	}


	$(document).on('change', 'input[name="wpsl_field_type"]:radio', function(){
		var type = $(this).val();
		if ( type == 'wpsl' ){
			$('.latlng').hide();
			select_wpsl_fields();
		} else {
			$('.latlng').show();
			update_lat_lng_values();
		}
	});

	// Update lat field on select change
	$(document).on('change', '#lat_select, #lng_select', function(){
		update_lat_lng_values();
	});


	/**
	* Update the hidden latitude and longitude fields
	*/
	function update_lat_lng_values()
	{
		var lat = $('#lat_select').val();
		var lng = $('#lng_select').val();
		$('#wpsl_lat_field').val(lat);
		$('#wpsl_lng_field').val(lng);
	}

	/**
	* Select the WPSL latlng fields
	*/
	function select_wpsl_fields()
	{	
		$('#field_wpsl').attr('checked','checked');
		$('#field_custom').removeAttr('checked','checked');
		$('#wpsl_lat_field').val('wpsl_latitude');
		$('#wpsl_lng_field').val('wpsl_longitude');
		$('#lat_select').val('wpsl_latitude');
		$('#lng_select').val('wpsl_longitude');
	}

	/**
	* Show/Hide the Post Type Label Fields
	*/
	function post_type_labels()
	{
		if ( $('#wpsl_post_type').val() === wpsl_locator.posttype ){
			$('.wpsl-label-row').show();
		} else {
			$('.wpsl-label-row').hide();
		}
	}

	/**
	* ------------------------------------------------------
	* Settings Page - Reset to Default Post Type
	* ------------------------------------------------------
	*/
	$('.wpsl-reset-posttype').on('click', function(e){
		e.preventDefault();
		if ( !confirm('Are you sure you want to reset to the default post type?') ) return false;
		reset_posttype();
	});

	function reset_posttype()
	{
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'wpslresetposttype',
				nonce: wpsl_locator.locatorNonce
			},
			success: function(data){
				location.reload();
			}
		});
	}



	/**
	* ------------------------------------------------------
	* Settings Page - Map Pin
	* ------------------------------------------------------
	*/
	$(document).ready(function() {

		$(document).on('click', '#upload_image_button', function() {
			formfield = $('#upload_image').attr('name');
			tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			return false;
		});

		window.send_to_editor = function(html) {
			imgurl = $('a' + html).attr('href');
			console.log(html);
			var imagehtml = '<img src="' + imgurl + '" id="map-pin-image" />';
			imagehtml += '<input id="remove_map_pin" type="button" value="' + wpsl_locator.remove + '" class="button action" style="margin-right:5px;margin-left:10px;" />';
			$('#map-pin-image-cont').append(imagehtml);
			$('#upload_image_button').remove();
			$('#wpsl_map_pin').val(imgurl);
			tb_remove();
		}

	});

	$(document).on('click', '#remove_map_pin', function(e){
		e.preventDefault();
		$('#map-pin-image').remove();
		$('#wpsl_map_pin').prop('value', '');
		$('#map-pin-image-cont').append('<input id="upload_image_button" type="button" value="' + wpsl_locator.upload + '" class="button action" />');
		$(this).remove();
	});



	/**
	* ------------------------------------------------------
	* Settings Page - AJAX Field List for Post Types
	* ------------------------------------------------------
	*/
	function get_fields_for_posttype(post_type)
	{
		var showHidden = ( $('#wpsl_show_hidden').is(':checked') ) ? 'true' : 'false';
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslposttype',
				nonce: wpsl_locator.locatorNonce,
				post_type: post_type,
				show_hidden: showHidden
			},
			success: function(data){
				console.log(data);
				$('#lat_select, #lng_select').html(data.fields);
				update_lat_lng_values();
				if ( post_type === 'location' ){
					select_wpsl_fields();
				}
			}
		});
	}


	/**
	* ------------------------------------------------------
	* Settings Page - Map Styles Type
	* ------------------------------------------------------
	*/
	$(document).on('change', '#wpsl_map_styles_type', function(){
		var choice = $(this).val();
		toggle_map_style_choice(choice);
	});

	function toggle_map_style_choice(choice)
	{
		if ( choice === 'none' ){
			$('#map-styles-choice, #map-styles-custom').hide();
		} else if ( choice === 'custom' ){
			$('#map-styles-custom').show();
			$('#map-styles-choice').hide();
		} else {
			$('#map-styles-choice').show();
			$('#map-styles-custom').hide();
		}
	}


	/**
	* ------------------------------------------------------
	* Settings Page - Results Fields (WYSIWYG)
	* ------------------------------------------------------
	*/
	$(document).on('click', '.wpsl-field-add', function(e){
		e.preventDefault();
		add_result_field();
	});
	function add_result_field()
	{
		var field = '[' + $('#wpsl-fields').val() + ']';
		tinymce.activeEditor.execCommand('mceInsertContent', false, field);
	}


	$(document).on('click', '.wpsl-post-field-add', function(e){
		e.preventDefault();
		add_post_field();
	});
	function add_post_field()
	{
		var field = '[' + $('#wpsl-post-fields').val() + ']';
		tinymce.activeEditor.execCommand('mceInsertContent', false, field);
	}

	// Enable Datepicker Fields
	$(document).ready(function(){
		$('[data-date-picker]').datepicker({
			beforeShow: function(input, inst){
				$('#ui-datepicker-div').addClass('wpsl-datepicker');
			}
		});
	});	

	

}); // jQuery