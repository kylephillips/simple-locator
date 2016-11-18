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
		if ( !isset($_POST['search_term']) ) return;
		$q = sanitize_text_field($_POST['search_term']);
		$this->url .= '&q=' . $q;
	}


	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}