<?php 
namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Repositories\PostRepository;

class FinishImport 
{
	/**
	* Transient
	*/
	private $transient;

	/**
	* Post Repository
	*/
	private $post_repo;

	public function __construct()
	{
		$this->post_repo = new PostRepository;
		$this->getTransient();
		$this->saveImport();
		$this->handleMissing();
		$this->response();
	}

	/**
	* Get the Transient
	*/
	private function getTransient()
	{
		$this->transient = get_transient('wpsl_import_file');
		$this->transient['complete'] = true;
		set_transient('wpsl_import_file', $this->transient, 1 * YEAR_IN_SECONDS);
	}

	/**
	* Save the Import
	*/
	private function saveImport()
	{
		$title = __('Import on ', 'simple-locator') . date_i18n( 'Y-m-d H:m:s', time() );
		$importpost = [
			'post_title' => $title, 
			'post_status' => 'publish',
			'post_type' => 'wpslimport'
		];
		$post_id = wp_insert_post($importpost);
		add_post_meta($post_id, 'wpsl_import_data', $this->transient);
	}

	/**
	* Handle Missing Rows
	*/
	private function handleMissing()
	{
		$action = $this->transient['missing_handling'];
		if ( $action == 'skip' ) return;
		$has_unique = false;
		foreach ( $this->transient['columns'] as $column ){
			if ( $column->unique ) $has_unique = true;
		}
		if ( !$has_unique ) return;
		$import_ids = $this->transient['post_ids'];
		$import_post_type = $this->transient['post_type'];
		$missing_ids = $this->post_repo->getMissingPostsFromImport($import_ids, $import_post_type);
		$this->post_repo->updateMissingPostsFromImport($missing_ids, $action);
	}

	/**
	* Send the Response
	*/
	private function response()
	{
		return wp_send_json([
			'status' => 'success', 
			'import_count' => $this->transient['complete_rows'], 
			'error_count'=> count($this->transient['error_rows']), 
			'errors' => $this->transient['error_rows']
		]);
	}
}