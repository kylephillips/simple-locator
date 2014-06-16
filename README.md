# WP Simple Locator


## Overview

**WP Simple locator** is a simple location plugin for Wordpress.


#### Demo 
[View the Demo](http://locatewp.com)

![Screenshot](https://raw.githubusercontent.com/kylephillips/wp-simple-locator/master/assets/images/screenshot-2.png)


#### Installation 
1. Upload the contents of /wpsimplelocator/ to the /wp-content/plugins/ directory
1. Activate Simple Locator through the 'Plugins' menu in WordPress



#### Settings

* Settings are located under Settings > Simple Locator.

| Option       | Description   
| ------------- |:-------------:
| Google Maps API Key      | If you have a Google Maps public API key, enter it here
| Measurement Unit      | Select between miles & kilometers
| Post Type | Select either a pre-existing post type, or the ```Locations``` post type enabled by the plugin
| Lat & Long fields  |  Select whether to use the plugin's encluded fields, or an existing custom field (ACF fields are supported). Note: If a post type other than ```Locations``` is selected, existing custom fields must be used.

![Screenshot](https://raw.githubusercontent.com/kylephillips/wp-simple-locator/master/assets/images/screenshot.png)

#### Usage
To display the locator, include the shortcode ```[wp_simple_locator]```
