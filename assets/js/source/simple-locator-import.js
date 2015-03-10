/**
* Import Functionality
*/
jQuery(function($){

	var offset = 0;
	var completecount = 0;
	var errorcount = 0;
	var pause = false;

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
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpslimportcolumns',
				rowcount: rowcount,
				nonce: wpsl_locator.locatorNonce
			},
			success: function(data){
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

		var html = '<option value="">--</option>';
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
		$('.wpsl-current-row').text(wpsl_locator.Row + ' ' + currentrow);
		get_csv_columns();
	}


	/**
	* Validate required columns
	*/
	$('.wpsl_save_columns').on('click', function(e){
		e.preventDefault();
		$('.wpsl-column-error').hide();
		if ( required_columns_validate() ){
			$(this).unbind('click').click();	
		} 
	});

	function required_columns_validate()
	{
		var required = [
			'wpsl_import_column_title',
			'wpsl_import_column_address',
			'wpsl_import_column_city',
			'wpsl_import_column_state'
		];
		
		for ( var i = 0; i < required.length; i++ ){
			var field = $('select[name=' + required[i] + ']');
			var value = $(field).val();
			if ( value === "" || !$.isNumeric(value) ){
				$(field).siblings('.wpsl-column-error').show();
				return false;
			}
		}

		return true;
		
	}


	/**
	* ------------------------------------------------------
	* Do the Import
	* ------------------------------------------------------
	*/

	$('.wpsl-start-import').on('click', function(e){
		e.preventDefault();
		$('.wpsl-import-indicator-intro').hide();
		$('.wpsl-import-indicator').show();
		do_import();
	});

	/**
	* Start the Import
	*/
	function do_import()
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpsldoimport',
				offset: offset
			},
			success: function(data){
				console.log(data);
				if ( data.status === 'complete' ){
					complete();
				} else {
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
		offset = offset + 1;
		var imported = 1 - data.failed;
		completecount = completecount + imported;
		errorcount = errorcount + data.failed;
		$('.progress-count').text(completecount);
		$('.error-count').text(errorcount);
		update_progress_bar();
		if ( !pause ) do_import();
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
		console.log('all done');
	}

	/**
	* Pause the Import
	*/
	$('.wpsl-pause-import').on('click', function(e){
		
		if ( $(this).hasClass('paused') ){
			$(this).removeClass('paused');
			$(this).text(wpsl_locator.pause);
			pause = true;
		} else {
			$(this).addClass('paused');
			$(this).text(wpsl_locator.pause_continue);
			pause = false;
		}
	});


}); // jQuery