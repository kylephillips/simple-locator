=== Simple Locator ===
Contributors: kylephillips
Donate link: http://locatewp.com/
Tags: location, store locator, google maps, store map
Requires at least: 3.8
Tested up to: 4.6
Stable tag: 1.5.5

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Google Maps location search functionality for any post type.

== Description ==

**Why use Simple Locator?**

Simple locator is ideal for developers who are looking for a way to implement location search for any post type. Simple locator is different in that it allows the selection of any existing custom fields for use as the geocoded data sources.

Don't have an existing post type with geocoded latitude and longitude fields? Simple Locator includes a default "Locations" post type with fields for latitutde, longitude, address, phone number, website, & more. This post type's name and labels are configurable through the plugin settings.

Simple Locator also offers developer-friendly options for adding custom Google Maps styling. Select from an existing list, leave the default styles, or add your own JSON styles.

**Using Simple Locator**

1. Add locations. This can either be the included "locations" post type or an existing custom post type with existing geocoded fields. Locations can also be imported in bulk using the included CSV importer.
2. Use the Shortcode [wp_simple_locator] or Widget to display a search form. The shortcode offers more options for map and form customization.

For more information visit [locatewp.com](http://locatewp.com).

**Important: Simple Locator requires WordPress version 3.8 or higher, and PHP version 5.4 or higher.**


== Installation ==

1. Upload simple-locator to the wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. Click on the Locations Menu item to begin adding locations.
4. To change the post type, geocoded fields, or map styles, visit Settings > Simple Locator

== Frequently Asked Questions ==

= I have a LOT of locations. How can I get them imported in bulk? =
As of version 1.2.0, Simple Locator includes a bulk CSV data importer. If you have your location data available in CSV format with separate address, city and state/province columns, you may use this tool to import your locations as posts and geocode the locations automatically. The Google Maps Geocoding API limits requests to 2500 per day, so if your file contains over 2500 rows, it may take multiple days to complete the import. See the [Simple Locator Website](http://locatewp.com) for details on importing locations.

= Why does importing take so long? =
The Google Maps Geocoding API limits requests to 5 per second, and 2500 per day. The import process is throttled to comply with the API limits. See the [Google Geocode API documentation](https://developers.google.com/maps/documentation/geocoding/#Limits) for more information.

= I got an error during a large import. What do I do now? =
Import progress is saved, and you may always come back and continue at a later time. If you received an API limit error, you'll need to wait 24 hours before continuing the import. If you attempt to continue after receiving this notice, your API key may be disabled by Google for violating their API terms. You may check your request usage in the Google Developer Console. If you receive a 500 error from Google during the import, try refreshing the page and continuing the import. If the issue persists, check the last import row for formatting errors (the  last import row will be displayed on page refresh).

= Why does "ZERO RESULTS" mean in the import error log? =
The Google Maps Geocoder could not locate the address. Check the corresponding CSV row number for formatting errors.

= Why isn't my Google Maps API Key Working for the Importer? =
Your API key must be public, and have the Geocoding API enabled. To enable the API, visit your Google Developer console, and enable both the "Javascript API v3" and the "Geocoding API". You may also check your API usage through the developer console.

= What 3rd party services does this plugin use? =
All geocoding and mapping functionality takes advantage of the Google Maps Javascript API V3. Unexpected changes in the API service may effect plugin functionality, but any changes will be monitored and addressed as needed.

= Can I hide the map, and only show text results? =
Use of the Google Maps API requires that the data be displayed in a Google map. Hiding the map violates the API licensing.

= How do I add custom functionality to the generated maps? =
Several Javascript callback functions are provided for key events in search, and map rendering. To learn more about these callbacks, visit [locatewp.com](http://locatewp.com).

= Is my existing data automatically geocoded? =
**No.** If an existing post type is chosen as the search term, there must be preformatted latitude and longitude fields associated with each post. Geocoded fields must be formatted using the degree format. All new posts under the “location” post type are geocoded on save.

= Does it work outside the United States? =
The search form uses the Google Maps API geocoding service, which offers reliable data throughout the world. Use of the API may change depending on your specific location. Localization is possible using the provided POT files in the /languages folder. To toggle between miles and kilometers, visit Settings > Simple Locator.

= How do I paginate results? =
Pagination is available in non-AJAX forms. To disable AJAX form submission, use the ajax="false" shortcode option. To limit results per page, use the perpage="15" option. NOTE: custom map/results containers are not available in non-AJAX forms.

= I'm a developer. How do I customize the map options? =
As of version 1.4.0, the Google Maps options object is customizable. To customize the options, visit settings > Simple Locator > Map Display. Check the box to enable custom map options and enter them. This MUST be a properly formatted Javascript object. Think of this as if you were writing your own Google Maps implementation and needed to pass options. Also note, enabling custom options will override any options set in shortcodes.

= Can I use this on a commercial site? =
This plugin relies on the Google Maps Javascript API. Please refer to the API documentation regarding commercial use and licensing.


== Screenshots ==

1. Display a simple, AJAX-enabled locator form using a shortcode or widget.

2. Form results load without page refresh. Customize the results & map containers if needed.

3. The included "location" post type includes custom fields specific to locations.

4. Includes options for Google Maps API Key, measurement unit (miles/kilometers), custom map pin image, whether to output the included CSS, whether to display the map in the specified post type's singular view, and option to add a geolocation button on enabled devices.

5. Use the included post type and latitude/longitude fields, or specify your own existing combination of type and fields. Works with Advanced Custom Fields. Ideal for developers looking for a customizable location search.

6. Choose from a list of pre-defined map styles, and optionally customize the Google Maps options object.

7. Or, paste your own JSON formatted styles.

8. Optionally show a default map on page load, with a custom location. Additionally, results can be set to show automatically on page load if the user's device or browser has geolocation capabilities. If the user doesn't, or they deny location access, the default map will show if set to.

9. Configure results display by adding custom fields from the chosen post type plus post data including the title, permalink, excerpt and thumbnails.

10. Import locations in bulk from a CSV file. Locations will be automatically geocoded using the Google Maps Geocode API (Import functionality requires PHP version 5.4+ and a valid Google Maps API key with the Geocoding API enabled).

== Changelog ==

= 1.5.6 =
* Pagination added to search log.

= 1.5.5 =
* Search log (list & map view) added along with filters and CSV export. History save must be enabled to view the search log.

= 1.5.4 =
* Google now requires a valid API key for all Javascript applications. 
* Fix in importer CSV upload that was preventing some csv formatted files from being uploaded
* Error handling added for missing API key on post edit screen.
* Option added to store user searches in the database

= 1.5.3 =
* Bug fix where post data not being reset in all locations shortcode
* Javascript Bug fix in custom map marker insertion
* Bug fix in all locations map, where custom JS options weren't being applied
* Infowindow filter applied to all locations shortcode
* Callback functions added for all locations map load and click events.
* Importer improvements: Check added for existing location based on title
* Shortcode option added to customize results wrapping element.
* Bug fix for custom results limits not saving.

= 1.5.2 =
* Bug fix where SQL JOIN limit being exceeded on some servers (Thanks to Jeff Dreher)
* Bug fixes in Widget display
* PHP 7 bug fixes (Thanks to Loic Froidmont)

= 1.5.1 =
* Option added to enable Javascript console logging for debugging/development purposes

= 1.5.0 =
* Compatibility tested with WordPress 4.4
* Bug fix in single location shortcode
* Bug fix where multiple instances of the same location being returned on certain hosts (thanks to Christine McDermott for bug tracking help)
* Minor admin bug fixes
* Filter added for customizing infowindow content in map results (Contributed by Scott Polhemus)
* Filter added for customizing form HTML (AJAX only)
* Filters added for customizing SQL queries
* Shortcode option added to include taxonomy filters in search form (AJAX only)
* Shortcode option added to allow empty address field (returns all results)
* French Translation (Thanks to Khelil Benosman)
* Partial Dutch Translation (Thanks to Kristof De Loof)
* Option added to enable/disable the Google Maps API from being enqueued in the admin area (separate from front end)

= 1.4.0 =
* Customizable Map Options - Ability to customize the Google options array added. Visit settings > map display. To enable custom map options, check the appropriate box and enter a properly formatted Google maps options object. Important: If custom map options are enabled, options specified in shortcodes will be overwritten.

= 1.3.1 =
* Bug fix - fatal error thrown on some sites when attempting to activate without Advanced Custom Fields installed.
* Bug fix – error when saving a location using an Advanced Custom Field Google Map field with the included fields visible.

= 1.3.0 =
* Important: PHP version 5.4+ is required to run version 1.2.0 or higher. To use Simple Locator with PHP version 5.3, version 1.1.5 should be installed. Updating to a newer version on servers running older versions of PHP will result in an error.
* Option to enable Google Places autocomplete on search form added. Visit the plugin general settings to enable autocomplete. Customize the placeholder text using the new shortcode option: addresslabel="Enter Your Location".
* Ability to drag map marker to save custom location added on post entry screens.
* Advanced Custom Fields Google Map Field integration. If you are using a post type with an associated Google Map field, you can now set the latitude and longitude fields to be populated from that field when saving posts. Visit the plugin post type settings to enable the feature. A map field must be associated with the selected post type for the setting to be visible.
* Option added to hide the included custom meta fields from the post entry screen.
* Option added to customize no results text in shortcode. Use the option noresultstext="Your Custom Message" to display a custom message.
* Non-AJAX option added to the form shortcode. To disable AJAX form submission, add the shortcode parameter ajax="false". When using the non-ajax form option, and additional pagination parameter is enabled. Use the parameter perpage="15" to designate how many results should show per page.

= 1.2.1 =
* Importer bug fix – API key for Geocoding saved separately as server key. 
* Google Maps Import over HTTPS – Fixes issue on sites running HTTPS

= 1.2.0 =
* CSV bulk importing is now included for importing and geocoding locations. PHP version 5.4+ is required for import functionality, as well as a valid Google Maps API key with the Geocode API enabled. See the Simple Locator website for more details.

= 1.1.5 =
* Bug fix in singular location view that was preventing display of additional custom meta data when using the included location post type
* "Reset to Default" button added to post type settings for resetting to plugin defaults in cases of inadvertently changing critical post type settings.

= 1.1.4 =
* Conflict with nonce and page caching resolved
* German translation added (provided by Slava Klejman)
* Option added to hide default post type
* Added post id to search result data, added to marker click callback function and data attribute on view location link

= 1.1.3 =
* Minor Javascript error fix in all locations script.

= 1.1.2 =
* Javascript Bug fix in all locations shortcode

= 1.1.1 =
* Bug fix in singular view

= 1.1.0 =
* A new shortcode [simple_locator_all_locations] is now available. The shortcode displays a map with all locations, zoomed to fit.

= 1.0.10 =
* Missing localized strings added for translations

= 1.0.9 =
* Option to add default empty map below search form on page load (visit Simple Locator settings to configure a default map)
* Option to enable default results – if user has geolocation enabled and permits access to your site, results will automatically load using their location
* Option added to limit results
* Admin UI Enhancements
* Portuguese translation (Luis Martens)

= 1.0.8 =
* Option added to remove Google Maps API script from output (to prevent conflicts with other plugins already outputting the script)
* Option added to include geolocation button in search form, with customizable button text

= 1.0.7 =
* Bug fixes in location fields when using a custom post type other than location. Option added to set menu icon for location/custom post type

= 1.0.6 =
* Options added to customize the included locations post type and its labels

= 1.0.5 =
* Minor Bug fixes in compatibility with other plugins

= 1.0.4 =
* Added option to select hidden meta fields for custom field selection

= 1.0.3 =
* Bug fix in singular post view map display

= 1.0.2 =
* Bug fix in custom post type meta field selection

= 1.0.1 =
* Localization bug fixes

= 1.0.0 =
* Initial release 


== Upgrade Notice ==

= 1.4.0 =
Ability to customize Google Maps options object added. Important: If the Google Maps API has been disabled via the plugin settings, this option will not be available.

= 1.3.1 =
Minor bug fixes.

= 1.3.0 =
Various features added including Google Places autocomplete option, Advanced Custom Field Map field integration, "no results" text customization, non-ajax option with simple pagination, and option to hide the included meta fields from post entry screens. 

= 1.2.0 =
Bulk CSV import is now included. Simple Locator now requires PHP version 5.4+.

= 1.1.0 =
Important: the form widget namespace has changed. Any manual calls to the widget should be updated to the new namespace: SimpleLocator\API\FormWidget. The single map shortcode name and namespace have also changed. Any manual calls to this shortocde should be updated. The new name is 'SingleLocationShortcode'.

= 1.0.9 =
Multiple results configuration options added including default map/results, results field display, and more. Portuguese Translation (Provided by Luis Martens)

= 1.0.8 =
Optional geolocation button added with customizable text, option to disable output of Google Maps API call (for compatibility with other plugins)

= 1.0.7 =
Minor bug fixes in custom post types

= 1.0.6 =
Options added to customize the included locations post type and its labels

= 1.0.5 =
Minor Bug fixes in compatibility with other plugins

= 1.0.4 =
Added option to select hidden meta fields for custom field selection

= 1.0.3 =
Bug fix in singular post view map display

= 1.0.2 =
Bug fixes

= 1.0.1 =
Minor Localization bug fixes

= 1.0.0 =
Initial release


== More Information ==

= Shortcode Options =
The shortcode to display the form and results is [wp_simple_locator]. There are several options available for customizing the form and results:

* **distances** - A comma separated list of numbers used to ustomize the list of available distances to choose from in the form
* **mapheight** - The height of the map in pixels
* **mapcontainer** – The unique ID of a custom container in which to load the map.
* **resultscontainer** - The unique ID of a custom container in which to load the search results.
* **buttontext** - Text to display in submit button
* **addresslabel** – Customize the address form label
* **mapcontrols** – Hide or show the map controls
* **mapcontrolsposition** – Google Maps formatted position for map controls (ex: TOP_LEFT)
* **placeholder** - Customize the search input placeholder text (added in v1.3.0)
* **noresultstext** - Customize the text displayed if no results are returned (added in v1.3.0)
* **ajax** – To disable AJAX form submission, use ajax="false". NOTE: custom map/results containers are not available for non-AJAX forms (added in v1.3.0)
* **perpage** - Used in conjunction with AJAX option. If a perpage option is included in a non-AJAX form, the results will be displayed with simple pagination, limited to the number per page specified. (Ex: ajax="false" perpage="15"). Pagination is not available with AJAX forms. (added in v1.3.0)
* **taxonomies** - To include taxonomy filters in the search form, use a comma-separated list of taxonomy names/slugs. The form will include select menus for each of the taxonomies specified. (AJAX only)
* **allowemptyaddress** - Set as "true" to allow empty address fields to be submitted (useful for returning all locations regardless of the user-specified location)

Visit [locate.wp](http://locatewp.com#documentation) for more detailed information.


= Widget Use =

Options include the title, distance options, and the map height. To use the widget outside of a predefined sidebar area, use the following function in your template, as outlined in the [WordPress documentation](http://codex.wordpress.org/Function_Reference/the_widget). The widget name is SimpleLocator\Widgets\Form (must include namespaces).

= Form Options =

To enable Google Places autocomplete in the search form, visit Settings > Simple Locator > General, and select the "Enable Autocomplete in Search" option. Your form will now include an autocomplete dropdown populated by the Google Places API.


= Singular View =

By default, a map of the location is added to the content on singular views. To remove this feature, update the setting under Simple Locator > General Settings.


= Extending & Customizing Map Display =

A number of JavaScript functions are provided to extend and customize the map results. Visit [locatewp.com](http://locatewp.com#documentation) for a list and usage details.


= Filters =

For a complete description of available filters and example usage, see the [plugin website](http://locatewp.com#documentation). 

* `simple_locator_form($output, $distances, $taxonomies, $widget)` – Customize the search form HTML
* `simple_locator_result($output, $result, $count)` - Customize the result output within the result list
* `simple_locator_infowindow($infowindow, $result, $count)` - Customize the display of results within the Google Maps infowindow
* `simple_locator_post_fields()` - Add additional fields to the search parameters (field names for $_POST data)
* `simple_locator_sql_select($sql)` - Add additional fields to the SELECT sql query during search
* `simple_locator_sql_join($sql)` - Join additional fields in the sql query during search
* `simple_locator_sql_where($sql)` - Add additional WHERE parameters to the sql query during search
