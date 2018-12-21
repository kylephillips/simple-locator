<?php
namespace SimpleLocator\Services\ExportTemplates;

/**
* Return all templates as json
*/
class TemplateRequest
{
	public function __construct()
	{
		$this->getTemplates();
	}

	public function getTemplates()
	{
		$templates = get_option('wpsl_export_templates');
		if ( isset($_GET['templateKey']) && isset($templates[$_GET['templateKey']]) ) $templates = $templates[$_GET['templateKey']];
		return wp_send_json([
			'status' => 'success', 
			'templates' => $templates
		]);
	}
}