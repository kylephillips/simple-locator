<?php
namespace SimpleLocator\Services\ExportTemplates;

class TemplateDestroyer
{
	public function __construct()
	{
		$this->deleteTemplate();
		return wp_send_json(['status' => 'success']);
	}

	private function deleteTemplate()
	{
		$template_key = $_GET['templateKey'];
		$templates = get_option('wpsl_export_templates', []);
		if ( !isset($templates[$template_key]) ) return;
		unset($templates[$template_key]);
		update_option('wpsl_export_templates', $templates);
	}
}