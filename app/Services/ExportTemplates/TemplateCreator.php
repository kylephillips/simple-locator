<?php
namespace SimpleLocator\Services\ExportTemplates;

class TemplateCreator
{
	/**
	* The Template to Create
	* @var array
	*/
	private $template;

	/**
	* Create the Template Array
	*/
	public function create($options)
	{
		$standard_columns = ( isset($options['standard_columns']) ) ? $options['standard_columns'] : [];
		$custom_columns = ( isset($options['custom_columns']) ) ? $options['custom_columns'] : [];
		$column_names = ( isset($options['column_name']) ) ? $options['column_name'] : [];
		$include_header = ( isset($options['include_header_row']) && $options['include_header_row'] == 'true' ) ? true : false;
		$filename = __('location-export', 'simple-locator') . '.csv';
		if ( isset($_POST['file_name']) && $_POST['file_name'] !== '' ) $filename = sanitize_text_field($_POST['file_name']) . '.csv';
		$template_name = __('template', 'simple-locator') . '-' . time() . '.csv';
		if ( isset($_POST['save_template_name']) && $_POST['save_template_name'] !== '' ) $template_name = sanitize_text_field($_POST['save_template_name']);
		$this->template = [
			'name'	=> $template_name,
			'standard_columns' => $standard_columns,
			'custom_columns' => $custom_columns,
			'include_header' => $include_header,
			'column_names' => $column_names,
			'filename' => $filename
		];
		$this->store();
	}

	private function store()
	{
		$templates = get_option('wpsl_export_templates', []);
		$templates[] = $this->template;
		update_option('wpsl_export_templates', $templates);
	}
}