<?php
namespace SimpleLocator\Listeners;

class HistoryClear
{
	/**
	* URL to redirect to
	* @var string
	*/
	private $url;

	public function __construct()
	{
		$this->url = sanitize_text_field($_POST['page']);
		$this->clearLog();
		$this->redirect();
	}

	/**
	* Clear the log
	*/
	private function clearLog()
	{
		global $wpdb;
		$table = $wpdb->prefix . 'simple_locator_history';
		$wpdb->query("TRUNCATE TABLE $table");
	}

	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}