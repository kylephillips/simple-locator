/**
* Import Functionality - Column Mapping
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.ImportColumnMap = function()
{
	var self = this;
	var $ = jQuery;

	self.totalrows = 0;
	self.rowcount = 0; // for selecting a specific row from csv

	self.selectors = {
		loadingIndicator : 'data-simple-locator-import-loading',
		columnSelection : 'data-simple-locator-import-column-selection',
		rowSelectionButton : 'data-simple-locator-import-row-selection-button',
		rowSelectionButtonNext : '[data-simple-locator-import-row-selection-button="next"]',
		rowSelectionButtonBack : '[data-simple-locator-import-row-selection-button="back"]',
		currentRowText : 'data-simple-locator-import-current-row',
		postType : 'data-simple-locator-import-post-type',
		selectColumn : 'data-simple-locator-import-column-select',
		selectField : 'data-simple-locator-import-field-select',
		selectType : 'data-simple-locator-import-type-select',
		addFieldButton : 'data-simple-locator-import-add-field',
		removeFieldButton : 'data-simple-locator-import-remove-field',
		saveButton : 'data-simple-locator-import-save',
		formError : 'data-simple-locator-form-error',
		columnError : 'data-simple-locator-column-error',
		fieldRow : 'data-simple-locator-import-field',
		form : 'data-simple-locator-import-column-form',
		status : 'data-import-post-status',
		taxonomySeparator : 'data-import-taxonomy-separator',
		uniqueIdCheckbox : 'data-simple-locator-import-unique-identifier',
		uniqueIdAction : 'data-import-unique-action'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( wpsl_locator.isimport === "true" && wpsl_locator.importstep === "2" ) {
				self.getColumns();
				self.getPostTypeFields();
			}
		});
		$('[' + self.selectors.rowSelectionButton + ']').on('click', function(e){
			e.preventDefault();
			self.moveRowSelection($(this));
		});
		$(document).on('change', '[' + self.selectors.selectField + ']', function(){
			self.autoSelectFieldType($(this));
			self.toggleStatusSelection();
			self.toggleTaxonomySelector();
		});
		$(document).on('click', '[' + self.selectors.addFieldButton + ']', function(e){
			e.preventDefault();
			self.addField();
		});
		$(document).on('change', '[' + self.selectors.uniqueIdCheckbox + ']', function(e){
			self.toggleUniqueIdCheckbox($(this));
		});
		$(document).on('click', '[' + self.selectors.removeFieldButton + ']', function(e){
			e.preventDefault();
			self.removeField($(this));
		});
		$(document).on('click', '[' + self.selectors.saveButton + ']', function(e){
			e.preventDefault();
			$('[' + self.selectors.formError + '],[' + self.selectors.columnError + ']').hide();
			if ( self.validates() ) {
				$('[' + self.selectors.form + ']').submit();
			}
		});
	}

	/**
	* Get the columns for the row count
	*/
	self.getColumns = function()
	{
		self.toggleLoading(true);
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			datatype: 'json',
			data: {
				action: 'wpslimportcolumns',
				rowcount: self.rowcount,
				nonce: wpsl_locator.locatorNonce
			},
			success: function(data){
				self.totalrows = data.row_count;
				self.updateCurrentRowText();
				self.addColumnRows(data)
				self.populateColumnSelects(data);
				self.selectColumns();
			}
		});
	}

	/**
	* Select the columns
	*/
	self.addColumnRows = function(data)
	{
		for ( var i = 0; i < (data.columns.length - 1); i++ ){
			self.addField();
		}
	}

	/**
	* Populate the select boxes from the row data
	*/
	self.populateColumnSelects = function(data)
	{
		var nextBtnDisabled = ( (self.rowcount+1) === data.row_count ) ? 'disabled' : false;
		$(self.selectors.rowSelectionButtonNext).attr('disabled', nextBtnDisabled);

		var html = '<option value="">' + wpsl_locator.choose_column + '</option>';
		for ( var i = 0; i < data.columns.length; i++ ){
			html += '<option value="' + i + '">' + data.columns[i] + '</option>';
		}
		$('[' + self.selectors.selectColumn + ']').html(html);
		self.toggleLoading(false);
	}

	/**
	* Loop through the rows and select the appropriate column
	*/
	self.selectColumns = function()
	{
		var rows = $('[' + self.selectors.fieldRow + ']');
		$.each(rows, function(i){
			var select = $(this).find('[' + self.selectors.selectColumn + ']');
			var value = String(i);
			$(select).val(value);
		});
	}

	/**
	* Move the row selection back/next
	*/
	self.moveRowSelection = function(button)
	{
		self.rowcount = ( $(button).attr(self.selectors.rowSelectionButton) === 'next' ) ? self.rowcount + 1 : self.rowcount - 1;
		var buttonAttr = ( self.rowcount == 0 ) ? 'disabled' : false;
		$(self.selectors.rowSelectionButtonBack).attr('disabled', buttonAttr);
		self.getColumns();
	}

	/**
	* Update the current row text 
	*/
	self.updateCurrentRowText = function()
	{
		var text = wpsl_locator.Row + ' ' + (self.rowcount + 1) + ' of ' + self.totalrows;
		$('[' + self.selectors.currentRowText + ']').text(text);
	}

	/**
	* Get the custom fields for the post type
	*/
	self.getPostTypeFields = function()
	{
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslposttype',
				nonce: wpsl_locator.locatorNonce,
				post_type: $('[' + self.selectors.postType + ']').val(),
				show_hidden: 'false',
				include_wpsl: 'true'
			},
			success: function(data){
				self.populateWordPressFieldSelects(data);
			}
		});
	}

	/**
	* Populate WordPress Field Selects
	*/
	self.populateWordPressFieldSelects = function(data)
	{
		var selects = $('[' + self.selectors.selectField + ']');
		
		var wordpress_fields = '<optgroup label="' + wpsl_locator.wordpress_fields + '">';
		wordpress_fields += '<option value="title">' + wpsl_locator.title + '</option>';
		wordpress_fields += '<option value="content">' + wpsl_locator.content + '</option>';
		wordpress_fields += '<option value="excerpt">' + wpsl_locator.excerpt + '</option>';
		wordpress_fields += '<option value="publish_date">' + wpsl_locator.publish_date + '</option>';
		wordpress_fields += '<option value="publish_date_gmt">' + wpsl_locator.publish_date_gmt + '</option>';
		wordpress_fields += '<option value="modified_date">' + wpsl_locator.modified_date + '</option>';
		wordpress_fields += '<option value="modified_date_gmt">' + wpsl_locator.modified_date_gmt + '</option>';
		wordpress_fields += '<option value="slug">' + wpsl_locator.slug + '</option>';
		wordpress_fields += '<option value="status">' + wpsl_locator.status + '</option>';
		wordpress_fields += '</optgroup>';

		var custom_fields = '<optgroup label="' + wpsl_locator.custom_fields + '">';
		custom_fields += data.fields;
		custom_fields += '</optgroup';

		var taxonomies = data.taxonomies;
		taxonomy_fields = null;
		if ( typeof taxonomies !== 'undefined' && taxonomies !== '' ){
			taxonomy_fields = '<optgroup label="' + wpsl_locator.taxonomies + '">';
			$.each(taxonomies, function(){
				taxonomy_fields += '<option value="taxonomy_' + this.name + '">' + this.label + '</option>';
			});
			taxonomy_fields += '<optgroup>';
		}

		$.each(selects, function(i, v){
			$(this).append(wordpress_fields);
			$(this).append(custom_fields);
			if ( taxonomy_fields ) $(this).append(taxonomy_fields);
		});
	}

	/**
	* Choose Field type based on field selection
	*/
	self.autoSelectFieldType = function(select)
	{
		var field = $(select).val();
		var type_field = $(select).next('[' + self.selectors.selectType + ']');
		if ( field === 'wpsl_address' ) $(type_field).val('address');
		if ( field === 'wpsl_city' ) $(type_field).val('city');
		if ( field === 'wpsl_state' ) $(type_field).val('state');
		if ( field === 'wpsl_zip' ) $(type_field).val('zip');
		if ( field === 'wpsl_website' ) $(type_field).val('website');
	}

	/**
	* Add a new field to be mapped
	*/
	self.addField = function()
	{
		var fieldcount = $('.wpsl-field').length;
		
		var newrow = $('.row-template').clone().removeClass('row-template');
		$(newrow).appendTo('.wpsl-column-fields').find('[' + self.selectors.removeFieldButton + ']').show();
		$(newrow).find('.wpsl-column-error').text('').hide();
		
		// Set the name indexes
		$(newrow).find('[' + self.selectors.selectField + ']').attr('name', 'wpsl_import_field[' + fieldcount + '][field]');
		var csv_column = $(newrow).find('[' + self.selectors.selectColumn + ']').attr('name', 'wpsl_import_field[' + fieldcount + '][csv_column]');
		$(newrow).find('[' + self.selectors.selectType + ']').attr('name', 'wpsl_import_field[' + fieldcount + '][type]');
		$(newrow).find('[' + self.selectors.uniqueIdCheckbox + ']').attr('name', 'wpsl_import_field[' + fieldcount + '][unique]').removeAttr('checked');
	}

	/**
	* Remove a field to be mapped
	*/
	self.removeField = function(button)
	{
		var row = $(button).parent('li');
		$(row).remove();
	}

	/**
	* Validate required fields
	*/
	self.validates = function()
	{
		var added_title = false;
		var added_address = false;
		var passes = true;

		$.each($('[' + self.selectors.fieldRow + ']'), function(i, v){
			
			// Validate Column & Field Selection
			var column = $(this).find('[' + self.selectors.selectColumn + ']');
			var field = $(this).find('[' + self.selectors.selectField + ']');
			if ( $(column).val() === "" || $(field).val() === "" ){
				$(this).find('[' + self.selectors.columnError + ']').text(wpsl_locator.required).show();
				passes = false;
			}

			// Make sure at least title and address are selected/added
			var type = $(this).find('[' + self.selectors.selectType + ']');
			if ( $(type).val() === "address" || $(type).val() === "full_address" ) added_address = true;
			if ( $(field).val() === "title" ) added_title = true;
		});

		// A title & address are required
		if ( !added_address ){
			$('[' + self.selectors.formError + ']').html('<p>' + wpsl_locator.required_address + '</p>').show();
			passes = false;
		}

		if ( !added_title ){
			$('[' + self.selectors.formError + ']').html('<p>' + wpsl_locator.required_title + '</p>').show();
			passes = false;
		}
		return passes;
	}

	/**
	* Toggle Unique identifier checkboxes 
	* (only 1 may be checked)
	*/
	self.toggleUniqueIdCheckbox = function(checked_box)
	{
		var checked = $(checked_box).is(':checked');
		var action = $('[' + self.selectors.uniqueIdAction + ']');
		if ( !checked ) {
			$(action).hide();
			return;
		}
		$('[' + self.selectors.uniqueIdCheckbox + ']').removeAttr('checked');
		$(checked_box).attr('checked', true);
		$(action).show();
	}

	/**
	* Toggle the status selection
	*/
	self.toggleStatusSelection = function()
	{
		var selected = false;
		var all_selects = $('[' + self.selectors.selectField + ']');
		$.each(all_selects, function(){
			if ( $(this).val() === 'status' ) selected = true;
		});
		if ( selected ){
			$('[' + self.selectors.status + ']').hide();
			return;
		}
		$('[' + self.selectors.status + ']').show();
	}

	/**
	* Toggle the taxonomy separator
	*/
	self.toggleTaxonomySelector = function()
	{
		var selected = false;
		var all_selects = $('[' + self.selectors.selectField + ']');
		$.each(all_selects, function(){
			var value = $(this).val();
			if ( value.includes('taxonomy_') ) selected = true;
		});
		if ( selected ){
			$('[' + self.selectors.taxonomySeparator + ']').show();
			return;
		}
		$('[' + self.selectors.taxonomySeparator + ']').hide();
	}

	/**
	* Toggle Loading
	*/
	self.toggleLoading = function(loading)
	{
		var loader = $('[' + self.selectors.loadingIndicator + ']');
		var columnSelection = $('[' + self.selectors.columnSelection + ']');
		if ( loading ){
			$(loader).show();
			$(columnSelection).hide();
			return;
		}
		$(loader).hide();
		$(columnSelection).show();
	}

	return self.bindEvents();
}