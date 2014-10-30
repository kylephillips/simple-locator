<?php
/*
Plugin Name: Simple Locator
Plugin URI: http://locatewp.com/
Description: Simple locator for Wordpress. Can be used for store or any other type of location. Simply add the shortcode [wp_simple_locator] to add the locator.
Version: 1.0
Author: Kyle Phillips
Author URI: https://github.com/kylephillips
License: GPLv2 or later.
*/

/*  Copyright 2014 Kyle Phillips  (email : support@locatewp.com)

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
require('vendor/autoload.php');
if ( !class_exists('WPSL_SimpleLocator') ) :
	require_once('SimpleLocator/SimpleLocator.php');
	$wpsimplelocator = new WPSL_SimpleLocator;
endif;