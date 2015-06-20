<?php 

namespace SimpleLocator;

class Helpers 
{

	/**
	* Plugin Root Directory
	*/
	public static function plugin_url()
	{
		return plugins_url() . '/simple-locator';
	}

	/**
	* View
	*/
	public static function view($file)
	{
		return dirname(dirname(__FILE__)) . '/views/' . $file . '.php';
	}

	/**
	* Check URL Format
	*/
	public static function checkURL($url)
	{
		$parsed = parse_url($url);
		if (empty($parsed['scheme'])) $url = 'http://' . ltrim($url, '/');
		return $url;
	}

	/**
	* Find and replace a url given a string
	* Searches string for hrefs, checks urls, adds http if not present
	*/
	public static function replaceURLs($string)
	{
		$urls = preg_match_all('/href=(&#39;|&quot;|\'|")(.+?)(&#39;|&quot;|\'|")/', $string, $matches);

		foreach($matches[2] as $key=>$m){
			$matches[2][$key] = 'href="' . self::checkURL($m) . '"';
		}
		foreach($matches[0] as $key=>$m){
			$string = str_replace($m, $matches[2][$key], $string);
		}
		
		return $string;
	}

	/**
	* Get Post Excerpt By ID
	*/
	public static function excerptByID($id)
	{
		$post = get_post($id);
		if( has_excerpt($post->ID) ){
			$the_excerpt = $post->post_excerpt;
			return apply_filters('the_content', $the_excerpt);
		}

		$the_excerpt = $post->post_content;
		$the_excerpt = preg_split('/\b/', $the_excerpt, 20 * 2+1);
		$excerpt_waste = array_pop($the_excerpt);
		$the_excerpt = implode($the_excerpt) . '...';
		$the_excerpt = substr($the_excerpt, 0, strripos($the_excerpt, " "));
 
		return apply_filters('the_content', $the_excerpt);
	}

	/**
	* Get the current URL
	*/
	public static function currentUrl()
	{
		global $wp;
		return home_url(add_query_arg(array(), $wp->request));
	}

}