<?php 
namespace SimpleLocator\Events;

use SimpleLocator\Listeners\GetMetaFieldsForPostType;
use SimpleLocator\Listeners\ResetPostTypeSettings;
use SimpleLocator\Listeners\HistorySearch;
use SimpleLocator\Services\CSVDownload\HistoryCsvDownload;

/**
* Register Admin Events
*/
class RegisterAdminEvents 
{

	public function __construct()
	{
		// Admin Settings Post Type Select
		add_action( 'wp_ajax_wpslposttype', [$this, 'PostTypeMetaRequested']);
		add_action( 'wp_ajax_wpslresetposttype', [$this, 'PostTypeResetRequested']);
		add_action( 'admin_post_wpslhistorysearch', [$this, 'SearchHistoryQueried']);
		add_action( 'admin_post_wpslhistorycsv', [$this, 'SearchHistoryCSVTriggered']);
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
	* Generate a Search History CSV
	*/
	public function SearchHistoryCSVTriggered()
	{
		new HistoryCsvDownload;
	}
}