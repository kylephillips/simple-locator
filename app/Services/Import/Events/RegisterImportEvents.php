<?php 
namespace SimpleLocator\Services\Import\Events;

use SimpleLocator\Services\Import\Listeners\FileUploader;
use SimpleLocator\Services\Import\Listeners\GetCSVRow;
use SimpleLocator\Services\Import\Listeners\ColumnMapper;
use SimpleLocator\Services\Import\Listeners\Import;
use SimpleLocator\Services\Import\Listeners\FinishImport;
use SimpleLocator\Services\Import\Listeners\UndoImport;
use SimpleLocator\Services\Import\Listeners\RedoImport;
use SimpleLocator\Services\Import\Listeners\RemoveImport;
use SimpleLocator\Services\Import\Listeners\ImportTemplateSave;
use SimpleLocator\Services\Import\Listeners\ImportTemplateRemove;

/**
* Register Events Related to Imports
*/
class RegisterImportEvents 
{

	public function __construct()
	{
		// Import Handlers
		add_action( 'admin_post_wpslimportupload', [$this, 'FileWasUploaded']);
		add_action( 'admin_post_wpslmapcolumns', [$this, 'ColumnMapWasSaved']);
		add_action( 'wp_ajax_wpsldoimport', [$this, 'ImportRequestMade']);

		add_action( 'wp_ajax_wpslimportcolumns', [$this, 'CSVRowRequested']);
		add_action( 'wp_ajax_wpslfinishimport', [$this, 'ImportComplete']);

		// Undo an Import
		add_action( 'admin_post_wpslundoimport', [$this, 'undoImportRequested']);

		// Redo an Import
		add_action( 'admin_post_wpslredoimport', [$this, 'redoImportRequested']);

		// Remove an Import
		add_action( 'admin_post_wpslremoveimport', [$this, 'removeImportRequested']);

		// Save an Import Template
		add_action( 'admin_post_wpslsaveimporttemplate', [$this, 'importTemplateSaved']);

		// Remove a Template
		add_action( 'admin_post_wpslremoveimporttemplate', [$this, 'importTemplateRemoved']);
	}

	/**
	* A File Was Uploaded
	*/
	public function FileWasUploaded()
	{
		new FileUploader;
	}

	/**
	* A CSV row was requested via AJAX
	*/
	public function CSVRowRequested()
	{
		new GetCSVRow;
	}

	/**
	* Map the columns for import
	*/
	public function ColumnMapWasSaved()
	{
		new ColumnMapper;
	}

	/**
	* Import Request Was Made
	*/
	public function ImportRequestMade()
	{
		new Import;
	}

	/**
	* Finish the Import
	*/
	public function ImportComplete()
	{
		new FinishImport;
	}

	/**
	* Undo an Import
	*/
	public function undoImportRequested()
	{
		new UndoImport;
	}

	/**
	* Redo an Import
	*/
	public function redoImportRequested()
	{
		new RedoImport;
	}

	/**
	* Remove an Import Record
	*/
	public function removeImportRequested()
	{
		new RemoveImport;
	}

	/**
	* Import Template was Saved
	*/
	public function importTemplateSaved()
	{
		new ImportTemplateSave;
	}

	/**
	* Import Template was Removed
	*/
	public function importTemplateRemoved()
	{
		new ImportTemplateRemove;
	}

}