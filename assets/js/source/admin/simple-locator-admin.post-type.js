/**
* Post Type Settings Page
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.PostType = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		postTypeField : 'data-simple-locator-post-type-field',
		postTypeLabels : 'data-simple-locator-post-type-labels',
		latitudeSelectField : 'data-simple-locator-latitude-select',
		longitudeSelectField : 'data-simple-locator-longitude-select',
		latitudeField : 'data-simple-locator-latitude-field',
		longitudeField : 'data-simple-locator-longitude-field',
		useLocatorFieldsRadio : 'data-simple-locator-use-included-fields',
		useCustomFieldsRadio : 'data-simple-locator-use-custom-fields',
		latLngOptions : 'data-simple-locator-lat-lng-options',
		showHiddenCheckbox : 'data-simple-locator-show-hidden',
		hidePostTypeCheckbox : 'data-simple-locator-hide-post-type',
		hideIncludedFieldsCheckbox : 'data-simple-locator-hide-included-fields'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.togglePostTypeLabels();
			self.toggleIncludedOptions();
			self.toggleCustomFieldOptions();
			self.toggleHideDefaultCheckbox();
			self.toggleHideIncludedFieldsCheckbox();
		});
		$(document).on('change', '[' + self.selectors.postTypeField + ']', function(){
			self.togglePostTypeLabels();
			self.loadPostTypeFields();
			self.toggleIncludedOptions();
			self.toggleHideDefaultCheckbox();
			self.toggleHideIncludedFieldsCheckbox();
		});
		$(document).on('change', '[' + self.selectors.latitudeSelectField + '],[' + self.selectors.longitudeSelectField + ']', function(){
			self.updateLatLngValues();
		});
		$(document).on('change', '[' + self.selectors.useLocatorFieldsRadio + '],[' + self.selectors.useCustomFieldsRadio + ']', function(){
			self.toggleCustomFieldOptions();
			self.toggleHideIncludedFieldsCheckbox();
		});
		$(document).on('change', '[' + self.selectors.showHiddenCheckbox + ']', function(){
			self.loadPostTypeFields();
		});
	}

	self.toggleIncludedOptions = function()
	{
		var pt = $('[' + self.selectors.postTypeField + ']').val();
		if ( pt !== wpsl_locator.posttype ){
			$('[' + self.selectors.useLocatorFieldsRadio + ']').attr('disabled', true).removeAttr('checked');
			$('[' + self.selectors.useCustomFieldsRadio + ']').attr('checked', true);
			return;
		}
		$('[' + self.selectors.useLocatorFieldsRadio + ']').removeAttr('disabled');
	}

	self.togglePostTypeLabels = function()
	{
		var labels = $('[' + self.selectors.postTypeLabels + ']');
		if ( $('[' + self.selectors.postTypeField + ']').val() === wpsl_locator.posttype ){
			$(labels).show();
			return;
		}
		$(labels).hide();
	}

	self.loadPostTypeFields = function()
	{
		var postType = $('[' + self.selectors.postTypeField + ']').val();
		if ( postType !== wpsl_locator.posttype ){
			$('[' + self.selectors.useLocatorFieldsRadio + ']').attr('disabled', true).removeAttr('checked');
			$('[' + self.selectors.useCustomFieldsRadio + ']').attr('checked', true);
			$('[' + self.selectors.latLngOptions + ']').show();
		} else {
			$('[' + self.selectors.useCustomFieldsRadio + ']').removeAttr('disabled');
			self.selectLocationFields();
		}
		$('[' + self.selectors.latitudeSelectField + '],[' + self.selectors.longitudeSelectField + ']').empty();
		self.getCustomFields(postType);
	}

	/**
	* Select the built-in fields
	*/
	self.selectLocationFields = function()
	{
		$('[' + self.selectors.useLocatorFieldsRadio + ']').attr('checked','checked');
		$('[' + self.selectors.useCustomFieldsRadio + ']').removeAttr('checked');
		$('[' + self.selectors.latitudeField + ']').val('wpsl_latitude');
		$('[' + self.selectors.longitudeField + ']').val('wpsl_longitude');
		$('[' + self.selectors.latitudeSelectField + ']').val('wpsl_latitude');
		$('[' + self.selectors.longitudeSelectField + ']').val('wpsl_longitude');
	}

	/**
	* Get the Custom Fields for the post type
	*/
	self.getCustomFields = function(postType)
	{
		var showHidden = ( $('[' + self.selectors.showHidden + ']').is(':checked') ) ? 'true' : 'false';
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslposttype',
				nonce: wpsl_locator.locatorNonce,
				post_type: postType,
				show_hidden: showHidden
			},
			success: function(data){
				$('[' + self.selectors.latitudeSelectField + '],[' + self.selectors.longitudeSelectField + ']').html(data.fields);
				self.updateLatLngValues();
				if ( postType === 'location' ) self.selectLocationFields();
			}
		});
	}

	/**
	* Toggle the custom field options
	*/
	self.toggleCustomFieldOptions = function()
	{
		var options = $('[' + self.selectors.latLngOptions + ']');
		if ( $('[' + self.selectors.useCustomFieldsRadio + ']').is(':checked') ){
			$(options).show();
			return;
		}
		$(options).hide();
	}

	/**
	* Update the latitude and longitude hidden fields based on the selected
	*/
	self.updateLatLngValues = function()
	{
		var lat = $('[' + self.selectors.latitudeSelectField + ']').val();
		var lng = $('[' + self.selectors.longitudeSelectField + ']').val();
		$('[' + self.selectors.latitudeField + ']').val(lat);
		$('[' + self.selectors.longitudeField + ']').val(lng);
	}

	/**
	* Toggle the hide default post type checkbox
	*/
	self.toggleHideDefaultCheckbox = function()
	{
		var posttype = $('[' + self.selectors.postTypeField + ']').val();
		var checkbox = $('[' + self.selectors.hidePostTypeCheckbox + ']');
		var checkboxParent = $(checkbox).parent('label').parent('p');
		if ( posttype === wpsl_locator.posttype ){
			$(checkbox).removeAttr('checked');
			$(checkboxParent).hide();
			return;
		}
		$(checkboxParent).show();
	}

	/**
	* Toggle the hide included field types checkbox
	*/
	self.toggleHideIncludedFieldsCheckbox = function()
	{
		var posttype = $('[' + self.selectors.postTypeField + ']').val();
		var checkbox = $('[' + self.selectors.hideIncludedFieldsCheckbox + ']');
		var checkboxParent = $(checkbox).parent('label').parent('p');
		var useIncluded = ( $('[' + self.selectors.useLocatorFieldsRadio + ']').is(':checked') ) ? true : false;
		if ( posttype !== wpsl_locator.posttype || useIncluded ){
			$(checkbox).removeAttr('checked');
			$(checkboxParent).hide();
			return;
		}
		$(checkboxParent).show();
	}

	return self.bindEvents();
}