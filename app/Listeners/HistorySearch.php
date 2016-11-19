<?php
namespace SimpleLocator\Listeners;

class HistorySearch
{
	/**
	* URL to redirect to
	* @var string
	*/
	private $url;

	public function __construct()
	{
		$this->setURL();
		$this->redirect();
	}

	/**
	* Build the URL to Redirect to
	*/
	private function setURL()
	{
		$this->url = sanitize_text_field($_POST['page']);
		$this->setSearchTerm();
	}

	/**
	* Set Order parameters
	*/
	private function setSearchTerm()
	{
		if ( !isset($_POST['search_term']) || !isset($_POST['date_start']) || !isset($_POST['date_end']) ) return;
		$q = sanitize_text_field($_POST['search_term']);
		$date_start = strtotime(sanitize_text_field($_POST['date_start']));
		$date_end = strtotime(sanitize_text_field($_POST['date_end']));
		$this->url .= '&q=' . $q;
		$this->url .= '&date_start=' . $date_start;
		$this->url .= '&date_end=' . $date_end;
	}


	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}