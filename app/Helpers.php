<?php namespace SimpleLocator;

class Helpers {

	/**
	* Plugin Root Directory
	*/
	public static function plugin_url()
	{
		return plugins_url() . '/wp-simple-locator';
	}

	/**
	* View
	*/
	public static function view($file)
	{
		return dirname(dirname(__FILE__)) . '/views/' . $file . '.php';
	}

}