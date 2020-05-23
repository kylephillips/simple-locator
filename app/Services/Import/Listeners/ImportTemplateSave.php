<?php
namespace SimpleLocator\Services\Import\Listeners;

class ImportTemplateSave extends ImportListenerBase
{
	/**
	* The Import Post ID
	* @var int
	*/
	private $import_post_id;

	public function __construct()
	{
		parent::__construct();
		$this->validateUser();
		$this->validate();
		$this->getImport();
		$this->save();
		$this->returnSuccess();
	}

	/**
	* Validate the data
	*/
	private function validate()
	{
		if ( !isset($_POST['template_name']) || $_POST['template_name'] == '' ) return $this->error(__('A template name is required.', 'simple-locator'));
		if ( !isset($_POST['template_id']) || $_POST['template_id'] == '' ) return $this->error(__('An import ID is required.', 'simple-locator'));
	}

	/**
	* Get the import
	*/
	private function getImport()
	{
		$post = null;
		$q = new \WP_Query([
			'post_type' => 'wpslimport',
			'posts_per_page' => 1,
			'fields' => 'ids',
			'p' => intval(sanitize_text_field($_POST['template_id']))
		]);
		if ( $q->have_posts() ) :
			$post = $q->posts;
		endif; wp_reset_postdata();
		if ( !$post ) return $this->error(__('The specified import could not be found.', 'simple-locator'));
		$this->import_post_id = $post[0];
	}

	/**
	* Save the template
	*/
	private function save()
	{
		$import_data = get_post_meta($this->import_post_id, 'wpsl_import_data', true);
		$title = sanitize_text_field($_POST['template_name']);
		$template = [
			'post_title' => $title, 
			'post_status' => 'publish',
			'post_type' => 'wpslimporttemplate'
		];
		$post_id = wp_insert_post($template);
		add_post_meta($post_id, 'wpsl_import_data', $import_data);
	}

	/**
	* Redirect back on success
	*/
	private function returnSuccess()
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&success=' . __('Import template successfully saved.', 'simple-locator'));
		return header('Location:' . $url);
	}

}