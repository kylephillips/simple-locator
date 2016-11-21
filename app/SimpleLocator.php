<?php 
/**
* Static Wrapper for Bootstrap Class
* Prevents T_STRING error when checking for 5.3.2
*/
class SimpleLocator 
{
	public static function init()
	{
		// dev/live
		global $simple_locator_env;
		$ls_env = 'live';

		global $simple_locator_version;
		$simple_locator_version = '1.5.6';

		$app = new SimpleLocator\Bootstrap;
	}
}