<?php
namespace SimpleLocator\Services\Import\Listeners;

/**
* Remove an Import Record
*/
class ImportTemplateRemove extends ImportListenerBase
{
	/**
	* Template ID to Remove
	* @var int
	*/
	private $template_id;

	public function __construct()
	{
		parent::__construct();
		$this->validateUser();
		$this->setID();
		$this->removeImport();
		$this->success(null);
	}

	/**
	* Set the Import ID & Post IDs
	*/
	private function setID()
	{
		$this->template_id = ( isset($_POST['template_remove_id']) ) ? intval(sanitize_text_field($_POST['template_remove_id'])) : 0;
	}

	/**
	* Delete the Posts
	*/
	private function removeImport()
	{
		wp_delete_post($this->template_id, true);
	}

	/**
	* Redirect back on success
	*/
	protected function success($step)
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&success=' . __('Import template successfully removed.', 'simple-locator'));
		return header('Location:' . $url);
	}

}