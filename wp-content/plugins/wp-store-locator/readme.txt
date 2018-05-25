=== WP Store Locator ===
Plugin URI: https://wpstorelocator.co
Contributors: tijmensmit
Donate link: https://www.paypal.me/tijmensmit
Tags: google maps, store locator, business locations, geocoding, stores, geo, zipcode locator, dealer locater, geocode, gmaps, google map, google map plugin, location finder, map tools, shop locator, wp google map
Requires at least: 3.7
Tested up to: 4.9.4
Stable tag: 2.2.14
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

An easy to use location management system that enables users to search for nearby physical stores.

== Description ==

WP Store Locator is a powerful and easy to use location management system. 
You can customize the appearance of the map, and provide custom labels for entry fields. 
Users can filter the results by radius, and see driving directions to the nearby stores in 
the language that is set in the admin panel. 

= Features include: =

* Manage an unlimited numbers of stores.
* Provide extra details for stores like the phone, fax, email, url, description and opening hours. There are filters available that allow you add [custom](http://wpstorelocator.co/document/add-custom-meta-data-to-store-locations/) meta data.
* Support for custom [map styles](http://www.mapstylr.com/).
* Choose from nine retina ready marker icons.
* Show the driving distances in either km or miles.
* Shortcodes that enable you to add individual opening hours, addresses or just a map with a single marker to any page.
* Compatible with multilingual plugins like [WPML](https://wpml.org/plugin/wp-store-locator/) and qTranslate X.
* You can drag the marker in the editor to the exact location on the map.
* Show the search results either underneath the map, or next to it.
* Show Google Maps in different languages, this also influences the language for the driving directions.
* Show the driving directions to the stores.
* Customize the max results and search radius values that users can select.
* Users can filter the returned results by radius, max results or category.
* Supports [marker clusters](https://developers.google.com/maps/articles/toomanymarkers?hl=en#markerclusterer).
* Customize map settings like the terrain type, location of the map controls and the default zoom level.
* Use the Geolocation API to find the current location of the user and show nearby stores.
* Developer friendly code. It uses custom post types and includes almost 30 different [filters](https://wpstorelocator.co/documentation/filters/) that help you change the look and feel of the store locator.

> <strong>Documentation</strong><br>
> Please take a look at the store locator [documentation](https://wpstorelocator.co/documentation/) before making a support request.

* [Getting Started](https://wpstorelocator.co/documentation/getting-started/)
* [Troubleshooting](https://wpstorelocator.co/documentation/troubleshooting/)
* [Customisations](https://wpstorelocator.co/documentation/customisations/)
* [Filters](https://wpstorelocator.co/documentation/filters/)

= Premium Add-ons =
 
**CSV Manager**

The [CSV Manager](https://wpstorelocator.co/add-ons/csv-manager/) allows you to bulk import, export and update your locations using a CSV file.

**Search Widget**

The [Search Widget](https://wpstorelocator.co/add-ons/search-widget/) enables users to search from any of the widgetized areas in your theme for nearby store locations, and show the results on the store locator page.

**Statistics**

The [Statistics](https://wpstorelocator.co/add-ons/statistics/) add-on enables you to keep track of the locations users are searching for and see where there is demand for a new store.

**Store Directory  - Coming Soon**

Generate a directory based on the store locations.

== Installation ==

1. Upload the `wp-store-locator` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a [Google API Key](https://wpstorelocator.co/document/create-google-api-keys/) and set them on the [settings](https://wpstorelocator.co/document/configure-wp-store-locator/#google-maps-api) page.
1. Set the start point on the [settings](https://wpstorelocator.co/document/configure-wp-store-locator/#map).
1. Add your stores under 'Store Locator' -> Add Store
1. Add the map to a page with this shortcode: [wpsl]

== Frequently Asked Questions ==

= How do I add the store locator to a page? =

Add this [shortcode](https://wpstorelocator.co/document/shortcodes/) [wpsl] to the page where you want to display the store locator.

= Oops! Something went wrong =

You can fix this by setting the [browser](https://wpstorelocator.co/document/configure-wp-store-locator/#google-maps-api) key on the settings page.

= There are weird characters in the search results, how do I remove them? =

This is most likely caused by a plugin like W3 Total Cache that tried to minify the HTML output on the store locator page. You can fix this by excluding the store locator from being minified on the settings page of the caching plugin you're using. In W3 Total Cache this is done by going to Minify -> Advanced -> Never minify the following pages, and fill in the page you don't want to have minified. So if your store locator is used on mydomain.com/store-locator, then fill in 'store-locator'.

= Can I use different markers for category or individual store locations? =

How to use custom markers is described [here](https://wpstorelocator.co/document/use-custom-markers/), you can also only use [different markers](https://wpstorelocator.co/document/use-custom-markers/) for a few locations, or just for the [categories](https://wpstorelocator.co/document/set-unique-category-markers/).

= The map doesn't display properly. It's either broken in half or doesn't load at all. =

Make sure you have defined a start point for the map under settings -> Map Settings.

= The map doesn't work anymore after installing the latest update =

If you use a caching plugin, or a service like Cloudflare, then make sure to flush the cache.

= I can't dismiss the pop up asking me to join the mailing list, how do I fix this? =

There is probably a JS error in the WP Admin area that prevents the pop up from being dismissed. Try for a second to switch back to a default WP theme, disable all other plugins, and then try to dismiss the newsletter pop up again.

= Why does it show the location I searched for in the wrong country? =

Some location names exist in more then one country, and Google will guess which one you mean. This can be fixed by setting the correct 'Map Region' on the settings page -> API Settings.

= The store locator doesn't load, it only shows the number 1? =

This is most likely caused by your theme using ajax navigation ( the loading of content without reloading the page ), or a conflict with another plugin. Try to disable the ajax navigation in the theme settings, or deactivate the plugin that enables it to see if that solves the problem.

If you don't use ajax navigation, but do see the number 1 it's probably a conflict with another plugin. Try to disable the plugins one by one to see if one of them is causing a conflict.

If you find a plugin or theme that causes a conflict, please report it on the [support page](http://wordpress.org/support/plugin/wp-store-locator).

> You can find the full documentation [here](https://wpstorelocator.co/documentation/).

== Screenshots ==

1. Front-end of the plugin
2. The driving directions from the user location to the selected store
3. The 'Store Details' section
4. The plugin settings

== Changelog ==

= 2.2.14, March 27, 2018 =
* Added: Included a wpsl_setting_dropdowns filter that enables the creation of additional dropdowns on the settings page.
* Added: A wpsl_get_location_fields() function that returns an array of the used meta fields.
* Changed: Made it easier for add-ons to access the WPSL_Frontend class.
* Fixed: Made sure the search results are visible when a RTL language is used.
* Fixed: Removed a CSS rule that prevented the markers and the links in the info window from responding to mouse clicks in IE 11.

= 2.2.13, February 28, 2018 =
* Added: Included support for 'directions' and 'clickable_contact_details' to the [wpsl_address](https://wpstorelocator.co/document/shortcodes/#store-address) shortcode.
* Added: An option to the User Experience section on the settings page to make the contact details ( phone / email ) always clickable.
* Added: A 'wpsl_skip_cpt_template' filter that you can use to prevent the code in the 'cpt_template' function from running.
* Changed: When the statistics add-on is active, then the complete response from the Geocode API is included in the AJAX data.
* Changed: Added a check in the makeAjaxRequest function to prevent it from processing data from AJAX request made by add-ons.
* Changed: Included a CSS rule to hide duplicate dropdowns generated by the [Select2](https://select2.org/) library used by some themes.
* Changed: Replaced the usage of findFormattedAddress with the reverseGeocode function in the wpsl-gmap.js.
* Changed: Fixed a typo in the geoLocationTim(e)out var.
* Changed: Updated the wpsl.pot file.
* Fixed: A ICL_LANGUAGE_CODE notice when Polylang is active.
* Fixed: On some installations a call to undefined function pll__() showed up when Polylang was used.

= 2.2.12, February 16, 2018 =
* Fixed: The zoom level automatically going down to streetlevel with the wpsl_map shortcode, and ignoring the set zoom level in the shortcode options.
* Fixed: The zipcode not always being correctly filtered out of the geocode API response when the user location is automatically detected.
* Changed: Removed unused customCheckboxValue variable.

= 2.2.11, January 14, 2018 =
* Added: A WPSL_Templates class that handles the different templates in the store locator and in the upcoming directory and nearby locations add-ons.
* Added: A 'wpsl_settings_tab' filter that makes it possible to add custom tabs on the settings page.
* Added: A 'wpsl_settings_section' action so you can add custom fields to the settings page.
* Changed: The find_nearby_locations(), check_store_filter() and the check_allowed_filter_value() now accepts an $args param.
* Changed: Increased the timeout for the geolocation request.

= 2.2.10, December 12, 2017 =
* Added: The [wpsl_map_tab_anchor](https://wpstorelocator.co/document/wpsl_map_tab_anchor) filter now also accepts an array, so you can show multiple maps ( with the wpsl_map shortcode ) next to eachother in different tabs.
* Added: A store locator media button in the editor that enables you to generate the shortcode attributes for the wpsl shortcode.
* Added: A check on the settings page that validates the provided Google Maps API keys in the background when they are saved.
* Added: Support to the [wpsl](https://wpstorelocator.co/document/shortcodes/) shortcode for 'auto_locate', 'category_selection', 'category_filter_type', 'checkbox_columns', 'map_type', 'start_marker' and 'store_marker'.
* Added: A requiredFields value to the js_settings function that allows you to customize/remove the required fields check when the 'Preview Location' button is used in the admin area.
* Added: A wpsl_save_post action.
* Added: Placed a 'wpsl-no-results' class on the outer div when no results are returned.
* Changed: Removed unused $i counter from the settings class.
* Changed: Improved the handling of errors returned by the Geocode API and clarified the meaning of them.
* Changed: Firefox now also [requires](https://www.mozilla.org/en-US/firefox/55.0/releasenotes/) SSL to access the Geolocation API, so updated the notice text on the settings page.
* Changed: Included the latest version of the EDD_SL_Plugin_Updater class ( 1.6.14 ).
* Changed: Replaced $wpsl_settings['category_filter'] with $this->use_category_filter() in the templates to make it compatible with the new category shortcode attributes. So if you're using a custom template, then make the same change.
* Changed: Renamed the 'no-results' class to 'wpsl-no-results-msg' to prevent conflicts with CSS rules from other themes / plugins.
* Changed: Included a missing ) in the country name list on the settings page.
* Changed: Updated the .pot file, and the Dutch and Spanish ( via [Jaime Smeke](http://untaljai.me/) ) translations.
* Changed: Set the display mode for the wpsl-directions class to table instead of block to prevent some themes from showing a wide underline.
* Changed: Moved get_ajax_url to the wpsl-functions.php and named it wpsl_get_ajax_url().
* Fixed: Include the used [travel mode](https://wpstorelocator.co/document/wpsl_direction_travel_mode/) in the generated URL that shows the user the directions on Google Maps itself.
* Fixed: A notice triggered by Polylang for ICL_SITEPRESS_VERSION.

= 2.2.9, July 9, 2017 =
* Added: The possibility to load [custom images](https://wpstorelocator.co/document/change-marker-cluster-images/) for the marker clusters.
* Added: An option to the map section to disable the zoom level from being automatically adjusted after a search is complete. If it's disabled then it will focus on the start point, and use the zoom level from the 'Initial zoom level' field.
* Added: A check that prevents the search radius / max results value used in the SQL query from being bigger then the max value set on the settings page.
* Fixed: The get_default_filter_value func not returning the default value for the search radius field ( see next item ).
* Changed: Had to rename the param for the search radius in the AJAX call from radius to search_radius to make it match with the settings page value.
* Note: If you're using custom code that relies on the returned paramater being radius, then rename it to search_radius.
* Changed: Updated the .pot file.

= 2.2.8, April 30, 2017 =
* Added: Support for [Polylang](https://wordpress.org/plugins/polylang/).
* Added: A [wpsl_direction_travel_mode](https://wpstorelocator.co/document/wpsl_direction_travel_mode/) filter that enabled you to change the used [travel mode](https://developers.google.com/maps/documentation/javascript/directions#TravelModes) for the directions.
* Added: A [wpsl_distance_unit](https://wpstorelocator.co/document/wpsl_distance_unit/) filter.
* Added: A [wpsl_disable_welcome_pointer](https://wpstorelocator.co/document/wpsl_disable_welcome_pointer/) filter that enables you disable the newsletter signup on a multisite network.
* Added: Support for custom alternateMarkerUrl and categoryMarkerUrl data in the JS file, this allows you to set a custom marker for [individual locations](https://wpstorelocator.co/document/use-different-marker-for-each-location/) and for [categories](https://wpstorelocator.co/document/set-unique-category-markers/).
* Changed: Deprecated the [wpsl_draggable_map](https://wpstorelocator.co/document/wpsl_draggable_map/) filter and [replaced](https://wpstorelocator.co/document/wpsl_gesture_handling/) it with support for Google Maps [gestureHandling](https://developers.google.com/maps/documentation/javascript/interaction).
* Changed: Made sure the supported map regions on the settings page match with the list of supported regions from [Google](https://developers.google.com/maps/coverage).
* Changed: Included the latest version of the EDD_SL_Plugin_Updater class ( 1.6.12 ).
* Fixed: A fatal call to undefined function error when the plugin is activated through WP-CLI.
* Fixed: The controls in street view mode not having a background color, so the back button wasn't visible.
* Fixed: Prevented two consecutive underscores from showing up in the generated transient names if no autoload limit is set.

= 2.2.7, December 31, 2016 =
* Changed: Included the latest version of the EDD_SL_Plugin_Updater class ( 1.6.8 ).
* Changed: Reverted a change in the CSS file that ended up breaking the map for some users.

= 2.2.6, December 24, 2016 =
* Fixed: The opening hours not working correctly for Saturday / Sunday in the admin area. The 12:00 AM field was missing.
* Fixed: A PHP notice showing up when an invalid value was set for the radius / max results dropdown.
* Fixed: The zoom attribute now works correctly for the wpsl_map shortcode.
* Changed: Included the latest version of the EDD_SL_Plugin_Updater class ( 1.6.7 ).
* Changed: Removed unused locationCount var from wpsl-gmap.js.
* Changed: Added a CSS rule that makes it harder for themes to scaled images on the map.

= 2.2.5, December 11, 2016 =
* Fixed: Made it work with the latest WPML version.
* Fixed: Remove the WPSL caps and Store Locator Manager role on uninstall. The code was always there to do so, but was never called.
* Fixed: A PHP notice that showed up when the settings page was saved with an empty start location field.
* Changed: Adjusted the structure of the post type labels so you can correctly translate them in singular / plural forms based on the used language. Via [deshack](https://wordpress.org/support/users/deshack/).
* Changed: Added a tooltip to the 'Attempt to auto-locate the user' field explaining that HTTPS is now [required](https://wpstorelocator.co/document/html-5-geolocation-not-working-chrome-safari/) in Chrome and Safari.
* Changed: The coordinates from the start location are now used to center the map in the map section on the settings page instead of it always defaulting to Holland.
* Changed: Renamed the existing option that prevents two Google Maps libraries from loading at the same time to "Enable compatibility mode" on the settings page ( Tools section ).
* Changed: Updated the wpsl.pot file.
* Changed: No longer use the deprecated icl_object_id() function when the WPML version is newer then 3.2.

= 2.2.4, Augustus 6, 2016 =
* New: Added an option to the tools section to prevent other scripts from including the Google Maps API a second time on the store locator page. This sometimes breaks the map.
* Fixed: Assigned the correct country code to Martinique on the settings page.
* Fixed: The code that calls [wp_editor](https://codex.wordpress.org/Function_Reference/wp_editor) now includes a random ID after 'wpsleditor' to make sure you can use it multiple times on the same page.
* Fixed: The inline [qTranslate X](https://wordpress.org/plugins/qtranslate-x/) syntax ( [:en]English Text[:de]Deutsch[:] ) now works for the label fields.
* Fixed: Added a workaround for [this](https://bugzilla.mozilla.org/show_bug.cgi?id=1283563) bug with the Geolocation API in Firefox.
* Changed: Automatically adjust the language Google Maps uses when WMPL or qTranslate X is active.
* Changed: Improved the handling of error codes returned by the Google Geocode API.
* Changed: Removed the '_' prefix from the returned language code in check_multilingual_code().
* Changed: Updated the wpsl.pot file.

= 2.2.3, June 27, 2016 =
* Fixed: Included the browser key in requests made to the Google Maps JavaScript API in the admin area. This is now [required](http://googlegeodevelopers.blogspot.nl/2016/06/building-for-scale-updates-to-google.html).
* Changed: Include the language code in the AJAX request if WPML is active.
* New: Spanish translations (es_ES). Via [Jaime Smeke](http://untaljai.me/).
* New: Added support for the upcoming statistics add-on.

= 2.2.2, May 18, 2016 =
* Fixed: Corrected the [path](https://github.com/googlemaps/js-marker-clusterer/pull/61) for the cluster marker images.

= 2.2.1, March 24, 2016 =
* Fixed: A JS bug that sometimes resulted in duplicate results showing up in the search results.

= 2.2, March 20, 2016 =
* New: The option to show the categories with checkboxes instead of a dropdown.
* Note: If you're showing the categories with checkboxes, then you can change the amount of used columns by setting the "checkbox_columns" ( between 1 and 4 ) on the [wpsl] shortcode.
* New: A [wpsl_no_results](https://wpstorelocator.co/document/wpsl_no_results) filter that allows you to create a custom HTML block that replaces the 'No results found' text.
* New: The option to enable [autocomplete](https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete) for location searches.
* Note: Read [this](https://wpstorelocator.co/version-2-2-released/#autocomplete) if you're using a custom template and want to use the autocomplete option.
* New: A [wpsl_enable_styled_dropdowns](https://wpstorelocator.co/document/wpsl_enable_styled_dropdowns/) filter that allows you to disable the current JS styling for all the dropdowns.
* New: You can now change the "Any" text used in the category dropdown in the labels section on the settings page.
* New: The option to hide the country name in the search results / marker pop-up.
* New: The option to show the contact details below the address.
* New: A [wpsl_hide_closed_hours](https://wpstorelocator.co/document/wpsl_hide_closed_hours/) filter that enables you to hide the days that the location is closed from the opening hours list.
* New: Values from [custom dropdowns](https://wpstorelocator.co/version-2-2-released/#custom-dropdown) that have a "wpsl-custom-dropdown" class set are automatically included in the AJAX data.
* New: Added a Add-Ons page to the "Store Locator" menu.
* New: A [wpsl_map_tab_anchor_return](https://wpstorelocator.co/document/wpsl_map_tab_anchor_return/) filter that allows you to choose between return true or false if the tab anchor is clicked.
* New: Added the "aria-required" attribute to the search field.
* Changed: The category dropdown is now created with [wp_dropdown_categories](https://codex.wordpress.org/Function_Reference/wp_dropdown_categories) and correctly indents sub categories.
* Changed: If a search returns no results, then the map will now focus on the searched location instead of only showing the "No results found" msg. 
* Changed: Instead of a single "API key" [field](https://wpstorelocator.co/document/configure-wp-store-locator/#google-maps-api) there are now separate [server](https://developers.google.com/maps/documentation/geocoding/get-api-key#get-an-api-key) and [browser](https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key) key fields. If an API key existed, then it's assumed to be a server key.
* Changed: A scrollbar is shown inside the styled dropdown filters if they are heigher then 300px. You can change the maximum height with the [wpsl_max_dropdown_height](https://wpstorelocator.co/document/wpsl_max_dropdown_height/) filter.
* Changed: Removed the text wrap on the category filter items. If a category name is too long, then it will now go over two lines instead of being cut off.
* Changed: Updated the wpml-config.xml and wpsl.pot.
* Changed: If the autocomplete for the start location on the settings page fails ( JS error ), then the set start location is geocoded in the background when the settings are saved.
* Changed: A single JS function now handles all the conditional options on the settings page instead of several smaller ones.
* Changed: The "zoom_name" and "zoom_latlng" setting is renamed to "start_name" and "start_latlng".
* Changed: Renamed the "category_dropdown" setting to "category_filter" to better reflect the value that it holds ( either checkbox or dropdown ). So if you're using a custom template, then make sure to change "category_dropdown" to "category_filter" in your code.
* Fixed: If you click on one of the styled dropdowns when it's already open, then it will now close.

= 2.1.2, March 4, 2016 =
* Fixed: Invalid HTML in the category dropdown.
* Fixed: Clicking on the marker on a single store / [wpsl_map] page triggered a JS error if the location permalinks are enabled.
* Fixed: The wpsl_map_tab_anchor filter not working with the [wpsl_map] shortcode.
* Fixed: Compatibility [issue](https://make.wordpress.org/core/2016/02/17/backbone-and-underscore-updated-to-latest-versions/) with Underscore 1.8.3.

= 2.1.1, January 13, 2016 =
* New: Restrict the search results to one or more categories by using the "category" attribute on the [wpsl] [shortcode](https://wpstorelocator.co/document/shortcodes/#store-locator).
* New: A "start_location" attribute for the [wpsl] [shortcode](https://wpstorelocator.co/document/shortcodes/#store-locator).
* New: Included a link to the add-ons page in the plugin meta row.
* New: Support for the "wp_editor" type with the [wpsl_meta_box_fields](https://wpstorelocator.co/document/wpsl_meta_box_fields/) filter, this will render the default WP Editor. Via [Richard](http://ampersandstudio.uk/).
* Changed: Moved the documentation link from the plugin actions row to the plugin meta row.
* Changed: If you use the "category" attribute on the [wpsl_map] shortcode, then the store names in the marker info window will automatically link to the store page or custom url.

= 2.1.0, December 23, 2015 =
* New: You can now use the "category" attribute ( use the category slugs as values ) on the [wpsl_map] shortcode to show locations that belong to one or more categories.
* New: Support to load the marker images from a [different folder](https://wpstorelocator.co/document/use-custom-markers/).
* New: A [wpsl_marker_props](https://wpstorelocator.co/document/wpsl_marker_props) filter that enables you to change the default "anchor", "scaledSize" and "origin" for the [marker image](https://developers.google.com/maps/documentation/javascript/3.exp/reference#Icon).
* New: A [wpsl_geocode_components](https://wpstorelocator.co/document/wpsl_geocode_components) filter that enables you to restrict the returned geocode results by administrativeArea, country, locality, postalCode and route.
* New: A [wpsl_draggable](https://wpstorelocator.co/document/wpsl_draggable_map) filter that enables you to enable/disable the dragging of the map.
* New: Support for the upcoming add-ons.
* Note: Read [this](https://wpstorelocator.co/version-2-1-released/#widget-support) if you're using a custom template!
* Changed: If you need to geocode the full address ( new store ), and a value for 'state' is provided it's now included in the geocode request.
* Changed: If the Geocode API returns a REQUEST_DENIED status, then the returned error message is shown explaining why it failed. 
* Fixed: In rare cases the SQL query returned duplicate locations with the same post id. To prevent this from happening the results are now by default grouped by post id.

= 2.0.4, November 23, 2015 =
* Fixed: HTML entity encoding issue in the marker tooltip, via [momo-fr](https://wordpress.org/support/profile/momo-fr) and [js-enigma](https://wordpress.org/support/profile/js-enigma).
* Fixed: Missing tooltip text for the start marker, and the info window for the start marker breaking when the Geolocation API successfully determined the users location.
* Fixed: Multiple shortcode attributes ignoring the "false" value, via [dynamitepets](https://wordpress.org/support/profile/dynamitepets) and [drfoxg](https://profiles.wordpress.org/drfoxg/).
* Changed: If a WPML compatible plugin is detected, a notice is shown above the label section explaining that the "String Translations" section in the used multilingual plugin should be used to change the labels.
* Changed: Removed the "sensor" parameter from the Google Maps JavaScript API. It triggered a 'SensorNotRequired' [warning](https://developers.google.com/maps/documentation/javascript/error-messages).
* Changed: Updated translation files.

= 2.0.3, October 27, 2015 =
* Fixed: The default search radius is no longer ignored if the Geolocation API is used. Via [xeyefex](https://wordpress.org/support/profile/xeyefex).
* Changed: Replaced get_page ( deprecated ) with get_post.
* Changed: Adjusted the position, and size of the reset map / current location icon to make them match with the new [control styles](http://googlegeodevelopers.blogspot.com/2015/09/new-controls-style-for-google-maps.html) introduced in v3.22 of the Google Maps API.
* Changed: Made it harder for themes to overwrite the icon font that is used to show the reset map / current location icon.
* Changed: Removed support for the map's pan control and zoom control style from the settings page and [wpsl_map] shortcode attributes. They are both [deprecated](https://developers.google.com/maps/articles/v322-controls-diff) in v3.22 of the Google Maps API.

= 2.0.2, September 19, 2015 =
* Fixed: Not all users always seeing the notice to convert the 1.x locations to custom post types.
* Fixed: Prevented empty search results from ending up in the autoload transient.
* Fixed: The autoload transient not being cleared after changing the start location on the settings page.
* Changed: Added extra CSS to make it harder for themes to turn the map completely grey, and set the default opening hours alignment to left.
* Changed: If you use the store locator in a tab, then it no longer requires the tab anchor to be 'wpsl-map-tab'. You can use whatever you want with the 'wpsl_map_tab_anchor' filter.

= 2.0.1, September 10, 2015 =
* Fixed: Prevented other plugins that use [underscore](http://underscorejs.org/) or [backbone](http://backbonejs.org/) from breaking the JavaScript templates, via [fatman49](https://profiles.wordpress.org/fatman49/) and [zurf](https://profiles.wordpress.org/zurf/).
* Fixed: Street view not showing the correct location after using it more then once, via [marijke_25](https://profiles.wordpress.org/marijke_25/).

= 2.0, September 7, 2015 =
* New: Moved away from a custom db table, the store locations are now registered as custom post types. 
* Note: The upgrade procedure will ask you to convert the current store locations to custom post types. This takes around 1 minute for every 1000 store locations.
* New: The option to enable/disable permalinks for the stores, and set a custom slug from the settings page.
* New: Three new [shortcodes](http://wpstorelocator.co/document/shortcodes/): [wpsl_map], [wpsl_hours] and [wpsl_address].
* New: A template attribute for the [wpsl](http://wpstorelocator.co/document/shortcodes/#store-locator) shortcode, via [Damien Carbery](http://www.damiencarbery.com).
* New: Supports [WPML](https://wpml.org/) and [qTranslate X](https://wordpress.org/plugins/qtranslate-x/).
* New: A textarea on the settings page where you can paste JSON code to create a [custom map style](https://developers.google.com/maps/documentation/javascript/styling).
* New: The option to hide the search radius dropdown on the frontend.
* New: A [wpsl_geolocation_timeout](http://wpstorelocator.co/document/wpsl_geolocation_timeout/) filter.
* New: The option to choose between different address formats, and a [filter](http://wpstorelocator.co/document/wpsl_address_formats/) to add custom ones.
* New: The option to use the [InfoBox](http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/docs/reference.html) library to style the info window.
* New: The option to choose between two different effects when a user hovers over the result list.
* New: Set the opening hours through dropdowns instead of a textarea.
* New: Filters that make it possible to add custom store data, and change the HTML structure of the info window and store listing template.
* New: The option to define a max location load if the auto loading of locations is enabled.
* New: The option to enable/disable scroll wheel zooming and the map type control on the map.
* New: Added 'Email' and 'Url' to the labels on the settings page.
* New: Added a general settings and documentation link to the plugin action links.
* New: The option to set a max auto zoom level to prevent the auto zoom from zooming to far.
* New: The option to set a different map type for the location preview.
* New: A check to see if the [SCRIPT_DEBUG](https://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG) constant is set, if this is the case the full scripts are loaded, otherwise the minified scripts are used.
* New: A [wpsl_thumb_size](http://wpstorelocator.co/document/wpsl_thumb_size/) filter that enables you to set the thumb size on the frontend without editing CSS files.
* New: The option to hide the distance in the store listing.
* New: Added JS code that prevents a grey map when the store locator is placed in a tab. This does require the use of a #wpsl-map-tab anchor.
* New: Portuguese translation via [Rúben Martins](http://www.rubenmartins.pt/).
* Changed: Better error handling for the Geolocation API.
* Changed: Regardless of the selected template, the store map is always placed before the store list on smaller screens.
* Changed: The wp-content/languages folder is checked for translations before using the translations in the plugin folder.
* Changed: The 'reset map' button now uses an icon font, and is placed in right bottom corner together with a new 'current location' icon.
* Changed: The cluster marker image will use HTTPS when available.
* Changed: Increased the default Geolocation timeout from 3000 to 5000 ms.
* Changed: The geocode requests to the Google Maps API will always use HTTPS.
* Changed: Instead of curl or file_get_contents the Google Maps API request will now use [wp_remote_get](https://codex.wordpress.org/Function_Reference/wp_remote_get).
* Changed: Replaced the 'wpsl_capability' filter with a 'Store Locator Manager' [role](http://wpstorelocator.co/document/roles/).
* Changed: Added an extra check in JS to prevent the search radius or max results value being set to NaN.
* Changed: The [wpsl_templates](http://wpstorelocator.co/document/wpsl_templates/) filter now expects an id field to be present in the array.
* Changed: Renamed the 'wpsl_gmap_api_attributes' filter to [wpsl_gmap_api_params](http://wpstorelocator.co/document/wpsl_gmap_api_params/).
* Changed: Added the 'enableHighAccuracy' parameter to the Geolocation request to make it more accurate on mobile devices.
* Fixed: An issue that prevented the settings page from saving the changes on servers that used the mod_security module.
* Fixed: The pan control option not working on the frontend if it was enabled on the settings page.
* Fixed: Prevented an empty comma from appearing in the direction URL if the zip code didn't exist.
* Fixed: Modified the CSS to prevent themes hiding the map images.
* Fixed: Dragging the store location marker in the store editor would sometimes return the incorrect coordinates.
* Fixed: The 'Back' button appeared multiple times after the user clicked on the 'Directions' link from different info windows.
* Fixed: The dropdown fields not being restored to the default values after the 'reset map' button was clicked.
* Note: Requires at least WP 3.7 instead of WP 3.5.

= 1.2.25 =
* Fixed: The store search breaking after the reset button was clicked, via [Drew75](https://wordpress.org/support/profile/drew75)
* Fixed: Two PHP notices.

= 1.2.24 =
* Fixed: Clicking the marker would no longer open the info window after a Google Maps API update. This only happened if street view was enabled.
* Fixed: A fatal error on some installations caused by the usage of mysql_real_escape_string, it is replaced with esc_sql.
* Fixed: A problem where some themes would just show "1" instead of the shortcode output.
* Fixed: The "dismiss" link not working in the notice that reminds users to define a start point.
* Fixed: A missing html tag that broken the store listing in IE7/8.
* Changed: Replaced the non-GPL compatible dropdown script.

= 1.2.23 =
* Fixed the geocoding request for the map preview on the add/edit page not including the zipcode when it's present, which can misplace the marker

= 1.2.22 =
* Fixed compatibility issues with the Google Maps field in the Advanced Custom Fields plugin
* Fixed the store urls in the store listings sometimes breaking
* Removed the requirement for a zipcode on the add/edit store page
* Improved the documentation in the js files

= 1.2.21 =
* Fixed an js error breaking the store locator

= 1.2.20 =
* Fixed the directions url sometimes showing an incomplete address due to an encoding issue
* Fixed the 'items' count on the store overview page showing the incorrect number after deleting a store
* Fixed the autocomplete for the 'start point' field sometimes not working on the settings page
* Fixed php notices breaking the store search when wp_debug is set to true
* Fixed the bulk actions when set to 'Bulk Actions' showing the full store list without paging
* Fixed small css alignment issues in the admin area
* Fixed the js script still trying to load store data when autoload was disabled
* Fixed the clickable area around the marker being to big
* Improved: After a user clicks on 'directions' and then clicks 'back', the map view is returned to the original location
* Removed: the 'Preview location on the map' button no longer updates the zip code value it receives from the Google Maps API
* Changed the way the dropdown filters are handled on mobile devices. They are now styled and behave according to the default UI of the device
* Added support for WP Multisite
* Added 'Screen Options' for the 'Current Stores' page, so you can define the amount of stores that are visible on a single page
* Added the option to make phone numbers clickable on mobile devices by adding a link around them with 'tel:'
* Added the option to make store names automatically clickable if the store url exists
* Added the option to show a 'zoom here' and 'street view' (when available) into the infowindow
* Added a second address field to the store fields
* Added the option to enable marker clusters
* Added the option to set a default country for the "Add Store" page
* Added Dutch (nl_NL) translations
* Added a .pot file to the languages folder for translators
* Added error handling for the driving directions
* Added several filters for developers: 
'wpsl_templates' for loading a custom template from another directory
'wpsl_menu_position' for adjusting the position of the store locator menu in the admin panel
'wpsl_capability' to manually set the required user capability for adding/editing stores
'wpsl_gmap_api_attributes' to modify the Google maps parameters ( change the map language dynamically )

= 1.2.13 =
* Fixed the store search not returning any results when the limit results dropdown is hidden

= 1.2.12 =
* Added an option to choose where the 'More info' details is shown, either in the store listings or on the map
* Added the 'back' and 'reset' text to the label fields on the settings page
* Added the option to remove the scrollbar when the store listings are shown below the map
* Improved the position of the reset button when the map controls are right aligned
* Fixed the 'More info' translation not working
* Fixed the start position marker disappearing when dragged

= 1.2.11 =
* Fixed the distance format always using km when you click the 'directions' text in the marker
* Fixed an issue where a CSS rule in some themes would place a background image on the active item in the dropdown list
* Added an option to disable the mouse cursor on pageload focusing on the location input field 
* Added an option to add a 'More info' link to the store listings, which when clicked will open the info window in the marker on the map

= 1.2.1 =
* Added an option to show the store listings below the map instead of next to it
* Added an option to open the directions in a new window on maps.google.com itself
* Fixed a 'too much recursion' js error that showed up when no start location was defined
* Fixed the auto loading of stores not being ordered by distance
* Fixed a problem with the input fields not always aligning in Chrome
* Improved the handling of thumbnails. If the thumbnail format is disabled in the theme, it will look for the medium or full format instead
* Several other small code improvements

= 1.1 =
* Added the option to open a link in a new window
* Added the option to show a reset button that will reset the map back to how it was on page load
* Added the option to load all stores on page load
* Fixed a problem with the shortcode output

= 1.0.1 =
* Fixed the styling for the store locator dropdowns being applied site wide
* Fixed a problem with slashes in store titles

= 1.0 =
* Initial release