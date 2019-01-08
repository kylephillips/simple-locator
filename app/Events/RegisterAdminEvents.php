<?php 
namespace SimpleLocator\Events;

use SimpleLocator\Listeners\GetMetaFieldsForPostType;
use SimpleLocator\Listeners\ResetPostTypeSettings;
use SimpleLocator\Listeners\HistorySearch;
use SimpleLocator\Listeners\HistoryClear;
use SimpleLocator\Services\CSVDownload\HistoryCsvDownload;
use SimpleLocator\Services\CSVDownload\ExportCsvDownload;
use SimpleLocator\Services\ExportTemplates\TemplateRequest;
use SimpleLocator\Services\ExportTemplates\TemplateDestroyer;
use SimpleLocator\Listeners\QuickEdit;

/**
* Register Admin Events
*/
class RegisterAdminEvents 
{
	public function __construct()
	{
		add_action( 'wp_ajax_wpslposttype', [$this, 'PostTypeMetaRequested']);
		add_action( 'wp_ajax_wpslresetposttype', [$this, 'PostTypeResetRequested']);
		add_action( 'admin_post_wpslhistorysearch', [$this, 'SearchHistoryQueried']);
		add_action( 'admin_post_wpslhistorycsv', [$this, 'SearchHistoryCSVTriggered']);
		add_action( 'admin_post_wpslhistoryclear', [$this, 'SearchHistoryCleared']);
		add_action( 'admin_post_wpslexport', [$this, 'DataExportTriggered']);
		add_action( 'wp_ajax_wpslexporttemplates', [$this, 'ExportTemplatesRequested']);
		add_action( 'wp_ajax_wpslexporttemplatedelete', [$this, 'ExportTemplateDeleted']);
		add_action( 'wp_ajax_wpslquickedit', [$this, 'quickEditSubmitted']);
	}

	/**
	* Meta Fields for a Specific Post Type were Requested
	*/
	public function PostTypeMetaRequested()
	{
		new GetMetaFieldsForPostType;
	}

	/**
	* Reset Post Type to Default
	*/
	public function PostTypeResetRequested()
	{
		new ResetPostTypeSettings;
	}

	/**
	* Search the Search History
	*/
	public function SearchHistoryQueried()
	{
		new HistorySearch;
	}

	/**
	* Clear the Search History
	*/
	public function SearchHistoryCleared()
	{
		new HistoryClear;
	}

	/**
	* Generate a Search History CSV
	*/
	public function SearchHistoryCSVTriggered()
	{
		new HistoryCsvDownload;
	}

	/**
	* Generate an Export CSV
	*/
	public function DataExportTriggered()
	{
		new ExportCsvDownload;
	}

	/**
	* Export Templates Requested
	*/
	public function ExportTemplatesRequested()
	{
		new TemplateRequest;
	}

	/**
	* Export Template Deleted
	*/
	public function ExportTemplateDeleted()
	{
		new TemplateDestroyer;
	}

	/**
	* Quick Edit Submitted
	*/
	public function quickEditSubmitted()
	{
		new QuickEdit;
	}
}