<?php namespace SimpleLocator\Import;
/**
* Save the column mapping
*/
class SaveColumnMap {

	public function save()
	{
		$transient = get_transient('wpsl_import_file');
		$transient['columns'] = array(
			'title' => sanitize_text_field($_POST['wpsl_import_column_title']),
			'address' => sanitize_text_field($_POST['wpsl_import_column_address']),
			'city' => sanitize_text_field($_POST['wpsl_import_column_city']),
			'state' => sanitize_text_field($_POST['wpsl_import_column_state']),
			'zip' => sanitize_text_field($_POST['wpsl_import_column_zip']),
			'phone' => sanitize_text_field($_POST['wpsl_import_column_phone']),
			'website' => sanitize_text_field($_POST['wpsl_import_column_website']),
			'additional' => sanitize_text_field($_POST['wpsl_import_column_additional']),
			'content' => sanitize_text_field($_POST['wpsl_import_column_content'])
		);
		$transient['import_status'] = ( isset($_POST['wpsl_import_status']) && $_POST['wpsl_import_status'] == 'draft' ) ? 'draft' : 'publish';
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
	}

}