/**
* Import Functionality
*/
jQuery(function($){

	var offset = parseInt(wpsl_locator.importoffset);
	var completecount = parseInt(wpsl_locator.complete_count);
	var errorcount = parseInt(wpsl_locator.error_count); 
	var pause = false; // pause import
	var rowcount = 0; // for selecting a specific row from csv
	var totalrows = 0;
	var passed_validation = true;
	var imports_per_request = 5; // How many imports per request

	/**
	* Start New Import (cancel previous)
	*/
	$(document).on('click', '.wpsl-new-import', function(e){
		e.preventDefault();
		$('.wpsl-upload-form').show();
		$(this).parents('.wpsl-import-instructions').hide();
	});

	/**
	* Toggle Import Instructions
	*/
	$(document).on('click', '[data-toggle-import-instructions]', function(e){
		e.preventDefault();
		$('.wpsl-import-instructions').toggle();
	});

	/**
	* ------------------------------------------------------
	* Map CSV columns
	* ------------------------------------------------------
	*/
	$(document).ready(function(){
		if ( wpsl_locator.isimport === "true" && wpsl_locator.importstep === "2" ){
			get_csv_columns();
		}
	});

	/**
	* Get the columns for the rowcount
	*/
	function get_csv_columns()
	{
		$('.wpsl-loading-settings').show();
		$('.wpsl-column-selection').hide();
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			datatype: 'json',
			data: {
				action: 'wpslimportcolumns',
				rowcount: rowcount,
				nonce: wpsl_locator.locatorNonce
			},
			success: function(data){
				totalrows = data.row_count;
				populate_select_boxes(data);
			}
		});
	}

	/**
	* Populate the select boxes from the row data
	*/
	function populate_select_boxes(row)
	{
		if ( (rowcount+1) === row.row_count ){
			$('button[data-direction="next"').attr('disabled', 'disabled');
		} else {
			$('button[data-direction="next"').attr('disabled', false);
		}

		var html = '<option value="">' + wpsl_locator.choose_column + '</option>';
		for ( var i = 0; i < row.columns.length; i++ ){
			html += '<option value="' + i + '">' + row.columns[i] + '</option>';
		}
		$('.wpsl-import-column-selection').html(html);
		$('.wpsl-loading-settings').hide();
		$('.wpsl-column-selection').show();
	}

	/**
	* Move forward/back a row in select display
	*/
	$('.wpsl-row-selection button').on('click', function(e){
		e.preventDefault();
		var direction = $(this).data('direction');
		move_row_selection(direction);
	});

	/**
	* Reload selected row to given index
	*/
	function move_row_selection(direction)
	{
		if ( direction == 'back' ){
			rowcount--;
		} else {
			rowcount++;
		}

		if ( rowcount == 0 ){
			$('button[data-direction="back"]').attr('disabled', 'disabled');
		} else {
			$('button[data-direction="back"]').attr('disabled', false);
		}

		var currentrow = rowcount + 1;
		var text = wpsl_locator.Row + ' ' + currentrow + ' of ' + totalrows;
		$('.wpsl-current-row').text(text);
		get_csv_columns();
	}

	/**
	* Get the fields for the chosen post type
	*/
	$(document).ready(function(){
		if ( wpsl_locator.importstep == "2" ) get_fields_for_posttype(true);
	});
	function get_fields_for_posttype(populate_all)
	{
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslposttype',
				nonce: wpsl_locator.locatorNonce,
				post_type: $('#wpsl-import-post-type').val(),
				show_hidden: 'false'
			},
			success: function(data){
				if ( populate_all ){
					var fields = $('.wpsl-import-field-selection');
					$.each(fields, function(i, v){
						populate_field_select($(this), data.fields);
					});
				}
			}
		});
	}

	/**
	* Populate field select menu
	*/
	function populate_field_select(item, fields)
	{
		$(item).append('<option value="title">' + wpsl_locator.title + '</option>');
		$(item).append('<option value="content">' + wpsl_locator.content + '</option>');
		$(item).append(fields);
	}


	/**
	* Validate required columns
	*/
	$('.wpsl_save_columns').on('click', function(e){
		e.preventDefault();
		$('.wpsl-column-error, .wpsl-form-error').hide();
		
		passed_validation = true;
		validate_required_columns();
		
		if ( passed_validation ){
			$(this).unbind('click').click();	
		} 
	});

	function validate_required_columns()
	{
		var rows = $('.wpsl-field');
		var added_title = false;
		var added_address = false;

		$.each(rows, function(i, v){
			
			// Validate Column & Field Selection
			var column = $(this).find('.wpsl-import-column-selection');
			var field = $(this).find('.wpsl-import-field-selection');
			if ( $(column).val() === "" || $(field).val() === "" ){
				$(this).find('.wpsl-column-error').text(wpsl_locator.required).show();
				passed_validation = false;
			}

			// Make sure at least title and address are selected/added
			var type = $(this).find('.wpsl-import-type-selection');
			if ( $(type).val() === "address" || $(type).val() === "full_address" ) added_address = true;
			if ( $(field).val() === "title" ) added_title = true;
		});

		// A title & address are required
		if ( !added_address ){
			$('.wpsl-form-error').html('<p>' + wpsl_locator.required_address + '</p>').show();
			passed_validation = false;
		}

		if ( !added_title ){
			$('.wpsl-form-error').html('<p>' + wpsl_locator.required_title + '</p>').show();
			passed_validation = false;
		}
	}

	/**
	* Remove a field
	*/
	$(document).on('click', '.wpsl-import-remove-field', function(e){
		e.preventDefault();
		remove_field($(this))
	});
	function remove_field(button)
	{
		var row = $(button).parent('li');
		$(row).remove();
	}

	/**
	* Add a New Row/Field
	*/
	$(document).on('click', '.wpsl-import-add-field a', function(e){
		e.preventDefault();
		add_field();
	});
	function add_field()
	{
		var fieldcount = $('.wpsl-field').length;
		
		var newrow = $('.row-template').clone().removeClass('row-template');
		$(newrow).appendTo('.wpsl-column-fields').find('.wpsl-import-remove-field').show();
		$(newrow).find('.wpsl-column-error').text('').hide();
		
		// Set the name indexes
		$(newrow).find('.wpsl-import-field-selection').attr('name', 'wpsl_import_field[' + fieldcount + '][field]');
		$(newrow).find('.wpsl-import-column-selection').attr('name', 'wpsl_import_field[' + fieldcount + '][csv_column]');
		$(newrow).find('.wpsl-import-type-selection').attr('name', 'wpsl_import_field[' + fieldcount + '][type]');
	}

	/**
	* Choose Field type based on field selection
	*/
	$(document).on('change', '.wpsl-import-field-selection', function(){
		var field = $(this).val();
		var type_field = $(this).next('.wpsl-import-type-selection');
		if ( field === 'wpsl_address' ) $(type_field).val('address');
		if ( field === 'wpsl_city' ) $(type_field).val('city');
		if ( field === 'wpsl_state' ) $(type_field).val('state');
		if ( field === 'wpsl_zip' ) $(type_field).val('zip');
		if ( field === 'wpsl_website' ) $(type_field).val('website');
	});


	/**
	* ------------------------------------------------------
	* Do the Import
	* ------------------------------------------------------
	*/

	$('.wpsl-start-import').on('click', function(e){
		e.preventDefault();
		$('.wpsl-import-indicator-intro, .wpsl-last-row-imported').hide();
		$('.wpsl-import-indicator').show();
		do_import();
	});

	/**
	* Start the Import
	*/
	function do_import()
	{
		if ( pause ) return;
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpsldoimport',
				nonce: wpsl_locator.locatorNonce,
				offset: offset,
				imports_per_request: imports_per_request
			},
			success: function(data){
				console.log(data);
				if ( data.status === 'complete' ){
					complete();
				} else if (data.status === 'error') {
					$('.wpsl-import-error p').text(data.message);
					$('.wpsl-import-error').show();
				} else if ( data.status === 'success' ){
					update_counts(data);
				}
			}
		});
	}

	/**
	* Update Counts
	*/
	function update_counts(data)
	{
		completecount = completecount + data.import_count;
		offset = offset + data.import_count + data.failed_count; // increment the offset
		errorcount = errorcount + data.failed_count;
		$('.progress-count').text(completecount);
		$('.error-count').text(errorcount);
		update_progress_bar();
		do_import();
	}

	/**
	* Update the width of the progress bar indicator
	*/
	function update_progress_bar()
	{
		var progress_total_width = $('.progress-bar-bg').width();
		var progress_ratio = completecount / parseInt($('.progress-bar-bg').data('total'));
		var progress_width = progress_total_width * progress_ratio;
		$('.progress-bar').width(progress_width + 'px');
	}

	/**
	* Import is complete
	*/
	function complete()
	{
		$('.wpsl-import-indicator').hide();
		$('.wpsl-import-complete').show();
		get_import_complete_status();
	}


	/**
	* ------------------------------------------------------
	* Pause the Import
	* ------------------------------------------------------
	*/
	$(document).on('click', '.wpsl-pause-import', function(e){
		e.preventDefault();
		pause = ( pause ) ? false : true;
		var buttontext = ( pause ) ? wpsl_locator.pause_continue : wpsl_locator.pause;
		$(this).text(buttontext);
		do_import();
	});



	/**
	* ------------------------------------------------------
	* Import is Complete
	* ------------------------------------------------------
	*/
	function get_import_complete_status()
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpslfinishimport'
			},
			success: function(data){
				console.log(data);
				$('.wpsl-total-import-count').text(data.import_count);
				$('.wpsl-total-error-count, .error-count').text(data.error_count);
				if ( data.errors.length > 0 ){
					append_import_errors(data.errors);
				}
			}
		});
	}

	/**
	* Append rows to the error log table
	*/
	function append_import_errors(errors)
	{
		for ( var i = 0; i < errors.length; i++ ){
			var html = '<tr><td>' + errors[i].row + '</td><td>' + errors[i].error + '</td></tr>';
			$('.wpsl-import-details table').append(html);
		}
		$('.wpsl-import-details').show();
	}



	/**
	* ------------------------------------------------------
	* Undo / Redo / Remove an Import
	* ------------------------------------------------------
	*/

	$(document).on('click', '[data-import-toggle-details]', function(e){
		e.preventDefault();
		$(this).parents('.import').find('.import-body').toggle();
	});

	// Undo
	$(document).on('click', '[data-undo-import]', function(e){
		e.preventDefault();
		var id = $(this).attr('data-undo-import');
		if ( confirm(wpsl_locator.confirm_undo) ) undo_import(id);
	});

	function undo_import(id)
	{
		$('#undo_import_id').val(id);
		$('[data-undo-import-form]').submit();
	}


	// Redo
	$(document).on('click', '[data-redo-import]', function(e){
		e.preventDefault();
		var id = $(this).attr('data-redo-import');
		if ( confirm(wpsl_locator.confirm_redo) ) redo_import(id);
	});

	function redo_import(id)
	{
		$('#redo_import_id').val(id);
		$('[data-redo-import-form]').submit();
	}


	// Remove
	$(document).on('click', '[data-remove-import]', function(e){
		e.preventDefault();
		var id = $(this).attr('data-remove-import');
		if ( confirm(wpsl_locator.confirm_remove) ) remove_import(id);
	});

	function remove_import(id)
	{
		$('#remove_import_id').val(id);
		$('[data-remove-import-form]').submit();
	}


}); // jQuery