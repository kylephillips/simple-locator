# Simple Locator for WordPress


## Overview

Simple locator is ideal for developers who are looking for a way to implement location search for any post type within WordPress. Simple locator is different in that it allows the selection of any existing custom fields for use as the geocoded data sources. If there are no existing data sources, the plugin ships with a "location" post type with fields ready to go.

Best suited for WordPress developers looking for a customizable way of adding location search.

Learn more at [locatewp.com](http://locatewp.com) and download from the [WordPress Plugin Repository](https://wordpress.org/plugins/simple-locator/).

#### Requirements

Simple Locator requires WordPress version 3.8+ and PHP version 5.4+. Simple Locator version 1.1.5 (no longer maintained) is compatible with PHP version 5.3.2+.


#### Demo 
[View the Demo](http://locatewp.com)

![Screenshot](https://raw.githubusercontent.com/kylephillips/wp-simple-locator/master/assets/images/screenshot-2.png)


#### Installation - from the WordPress Repository
1. Upload simple-locator to the wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. Click on the Locations Menu item to begin adding locations.
4. To change the post type, geocoded fields, or map styles, visit Settings > Simple Locator

#### Installation - from the GitHub Project
1. cd into the wp-content/plugins directory
2. Clone the project: ```git clone https://github.com/kylephillips/wp-simple-locator.git simple-locator```
3. cd into the ```simple-locator``` directory and run a ```composer install``` to download the plugin dependencies. [More on composer](https://getcomposer.org)
4. Activate the plugin through the Plugins menu in WordPress


#### Usage
To display the locator, include the shortcode ```[wp_simple_locator]```
