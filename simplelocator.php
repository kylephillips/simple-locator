<?php
/*
Plugin Name: Simple Locator
Plugin URI: http://locatewp.com/
Description: Location search in WordPress, made simple. Can be used for store or any other type of location. Simply add the shortcode [wp_simple_locator] to add the locator.
Version: 1.5.6
Author: Kyle Phillips
Author URI: https://github.com/kylephillips
Text Domain: wpsimplelocator
Domain Path: /languages/
License: GPLv2 or later.
*/

/*  Copyright 2016 Kyle Phillips  (email : support@locatewp.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Check versions before Instantiating Plugin Class
register_activation_hook( __FILE__, 'wpsimplelocator_check_versions' );
function wpsimplelocator_check_versions( $wp = '3.8', $php = '5.4.0' ) {
	global $wp_version;
	if ( version_compare( PHP_VERSION, $php, '<' ) ) $flag = 'PHP';
	elseif ( version_compare( $wp_version, $wp, '<' ) ) $flag = 'WordPress';
	else return;
	$version = 'PHP' == $flag ? $php : $wp;
	deactivate_plugins( basename( __FILE__ ) );
	wp_die('<p><strong>Simple Locator</strong> plugin requires'.$flag.'  version '.$version.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
}

if ( !class_exists('Bootstrap') ) :
	wpsimplelocator_check_versions();
	require('vendor/autoload.php');
	require_once('app/SimpleLocator.php');
	SimpleLocator::init();
endif;