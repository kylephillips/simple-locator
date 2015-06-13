<?php

namespace SimpleLocator\Services\Import\Listeners;

/**
* Remove an Import Record
*/
class RemoveImport extends ImportListenerBase
{
	/**
	* Import ID
	* @var int
	*/
	private $import_id;

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
		$this->import_id = ( isset($_POST['remove_import_id']) ) ? intval($_POST['remove_import_id']) : 0;
	}

	/**
	* Delete the Posts
	*/
	private function removeImport()
	{
		wp_delete_post($this->import_id, true);
	}

	/**
	* Redirect back on success
	*/
	protected function success($step)
	{
		$url = admin_url('options-general.php?page=wp_simple_locator&tab=import&success=' . __('Import successfully removed.', 'wpsimplelocator'));
		return header('Location:' . $url);
	}

}