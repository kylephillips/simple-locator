<?php 

namespace SimpleLocator\Listeners;

/**
* Resets the post type back to defaults 
*/
class ResetPostTypeSettings extends AJAXAdminListenerBase 
{

	public function __construct()
	{
		parent::__construct();
		$this->reset();
	}

	/**
	* Remove the Post type option(s)
	*/
	private function reset()
	{
		delete_option('wpsl_post_type');
		delete_option('wpsl_posttype_labels');
		delete_option('wpsl_field_type');
		delete_option('wpsl_lng_field');
		delete_option('wpsl_lat_field');
		flush_rewrite_rules(false);
		$this->success(__('Post Type successfully reset', 'wpsimplelocator'));
	}

}