# Changelog
======
1.7.8
======
- NEW:	Full Height Map
		See Settings > Map > Full Height Map

======
1.7.7
======
- NEW:	Get my Position works on search for store shortcode
- FIX:	Added loading hint when get my position clicked

======
1.7.6
======
- FIX:	Updated Default settings
- FIX:	Added button class for Divi Themes

======
1.7.5
======
- NEW:	Lat / Lng data now available in store overview (easiy to check if data missing)
- FIX:	Predefined Category has overwritten category by URL

======
1.7.4
======
- NEW:	! Important Update !
		As of 1st of July the old geoip provider we used "https://freegeoip.net/json/" 
		is no longer available, we switched to a new one: "https://geoip.nekudo.com/"
		You need to update our plugin otherwise it won't work anymore after 1st of July
- NEW:	Radius now also in URL PushState HTML5
- FIX:	jQuery attr changed for prop
- FIX:	Image Filter now also works with URL PushState
- FIX:	Radius Circle JS error for all stores link

======
1.7.3
======
- NEW:	URL PushState HTML5
		The users search (address, category + filters) will
		be stored in the URL. This can be shared then
- FIX:	Radius Circle JS Error
- FIX:	Multiple Locations

======
1.7.2
======
- NEW:  Display Distance in Result List
- FIX:  Issue when ordering was set to premium and all stores fetched

======
1.7.1
======
- FIX:	Missing translation "Enter your Address" added

======
1.7.0
======
- FIX:  Tax Meta Class Updated

======
1.6.9
======
- NEW:  Option for Infowindow Open check to prevent twiches
		See Settings > Infowindow > Check if Infowindow is closed
- FIX:  Icon Hover Fix
- FIX:  JS Code improvements
- FIX:  Renamed Pan to Marker on Mouse Hover => Enable Result List Hover
		Pan to Marker should be set in settings > maps > pan to map

======
1.6.8
======
- NEW:  Map style now also applies on Single Store Page

======
1.6.7
======
- FIX:  Show all stores did not respected the data to show

======
1.6.6
======
- NEW: 	Added an option to show a button "show on map"
		See data to show > Show on Map

======
1.6.5
======
- NEW: Map > Enable / Disable Pan to Marker on Hover
- NEW: Result List > Enable / Disable Pan to Marker on Hover

======
1.6.4
======
- FIX: IE11 Problems

======
1.6.3
======
- NEW:  Option to enable Output Buffering as some themes had problem with displaying

======
1.6.2
======
- FIX:  Moved google maps script to the bottom to avoid conflicts with other maps plugins

======
1.6.1
======
- NEW:  Shortcode to show a search box only, that links to the store locator
		Demo: https://plugins.db-dzine.com/wordpress-store-locator/search-for-store/
		Shortcode: [wordpress_store_locator_search url="YOUR_STORE_LOCATOR_URL" style="1" show_filter="yes"]
- FIX:  Filters not working when premium sorting was checked

======
1.6.0
======
- FIX:  Added output buffering for shortcode used

======
1.5.9
======
- FIX:  renamed import upload file because of theme conflicts

======
1.5.8
======
- NEW:  Check if you want to Adjust zoom level to Radius automatically
- NEW:  Set a link action to "none"

======
1.5.7
======
- NEW:  It is now possible to set the default radius to more than 1000

======
1.5.6
======
- FIX:  Fixed an issue where the zip was not laoded in contact form
- FIX:  Use store_locator_store_info_XX for info on contact page

======
1.5.5
======
- FIX:  Fixed an issue where the loading spinner
		did not got away

======
1.5.4
======
- NEW:  Added a pan to map on store hover

======
1.5.3
======
- FIX:  Fixed an issue where the Show all Stores
		Link did not work without Radius enabled

======
1.5.2
======
- NEW:  Sort stores by Premium Stores first
		See Settings > Result List > Sort Results by
- NEW:  Show image also in Result list 
- NEW:  Set a position (left / right) for store image 
		in result list

======
1.5.1
======
- NEW:  Prefixed all bootstrap files to not get in conflict with themes

======
1.5.0
======
- NEW:  Link stores to custom contact form pages
		You can now add an action to your stores, that 
		will redirect to a contact form with dealer information
		See Settings Data to Show > Show Contact Dealer
		See Tutorial & More Info here: https://plugins.db-dzine.com/wordpress-store-locator/docs/faq/create-store-contact-page/
- NEW:  Show All Stores link
		See Settings > Search Box > Show All stores Button
- NEW:  All Stores Zoom Level
- NEW:  All Stores Default Lat / Lng

======
1.4.6
======
- FIX:  Only 1 store showed in backend

======
1.4.5
======
- FIX:  Removed PHP Notices from Import Class
- FIX:  Removed PHP notice in Public Class

======
1.4.4
======
- NEW:  Option to display category filter as an image
		See Settings > Data To Show > Display Category Filters as Image
		Make sure you have set a category icon in the backend

======
1.4.3
======
- NEW:  Removed Meta Boxes to be required
		From now on we use custom meta data code
- NEW:  Radius Limitation of 999 removed and set to 3999
- FIX:  Removed Google Sensor required console issue

======
1.4.2
======
- FIX:  PHP Notice in php file

======
1.4.1
======
- NEW:  Shortcode attributes to create multiple maps with different categories:
		Example 1: 
		- Only show category ID 34 (T-Shirts) 
		- no children 
		- no "show all categories"
		[wordpress_store_locator categories="34" show_children="no" show_all="no"]
		Example 2:
		- Show Categories 32 (Clothes) and 35 (Music Stores)
		- Show no children
		- Show "all categories"
		[wordpress_store_locator categories="32,35" show_children="no" show_all="yes"]

======
1.4.0
======
- NEW:  Automatic zoom level adjustment when radius changes

======
1.3.9
======
- FIX:  Resultlist Link action issue

======
1.3.8
======
- NEW:  Display opening hours in single store pages
		Example: https://plugins.db-dzine.com/wordpress-store-locator/store/clothes-1/
- NEW:  Red border when address field is empty
- FIX:  Opening hours title showed up when no opening hours were entered

======
1.3.7
======
- NEW:  CSS Classes for active filter
- NEW:  CSS Classes for store filters

======
1.3.6
======
- NEW: 	Show the filter box open by default 
		Settings > Search Box > Filter open by default
- NEW:  Adress format options (standard or American / Australian)
		Settings > Data to Show > Address Format
- NEW: 	Switched from Time field to Text field for opening hours
		Now you can write "closed" for example
- NEW: 	Set a custom infowindow link action
- NEW: 	Show ZIP & Region
		Settings > Data to Show
- FIX: 	Sunday does not display
- FIX: 	Server side API key

======
1.3.5
======
- FIX: undefined variable

======
1.3.4
======
NEW: Logic for icons changed: when a store is in multiple categories 
	 -> Take the first categories icon
FIX: custom category icon could not be uploaded wpColorpicker error
FIX: map icon on result list hover

======
1.3.3
======
- FIX:  Export Stores misses filter categories
- FIX:  Import stores file upload in Multisite environments
- FIX:  Categories & Filter now update the count after import

======
1.3.2
======
- FIX:  PHP 7.1 fix

======
1.3.1
======
- NEW: WPML Support
- NEW: Complete new documentation (see here: https://plugins.db-dzine.com/docs/wordpress-store-locator/)
- NEW: Updated offline documentation
- FIX: Added map Meta key to import 
- FIX: Added Opening hours to Sample Import file
- FIX: Latitute wrongly assigned when importing XLSX without lat / lng
- FIX: Get Sample import file now respects the Excel 2007 option
- FIX: Switched custom store icon from Media Library to raw URL
	   !!! Make sure you change this in your stores settings !!!

======
1.3.0
======
- NEW: Set a custom map icon for a single store
- NEW: Logic for Icons:
		1. If a custom icon on single store is set -> take this
		2. If a custom category icon is set:
		2.1. If a store is only in one category -> take this icon
		2.2. If a store has multiple icons, the icon only changes when a category has been choosen in frontend
		3. If no store / category icon is set -> take the default Icon
- NEW: Improved editing layout in backend
- FIX: Export files are corrupt

======
1.2.9
======
- NEW: Add a custom Icon for store categories
- NEW: Added store categories & filter information on single store page
- FIX: Removed icon from json to increase speed

======
1.2.8
======
- FIX: undefined index in public.php file for meta boxes request

======
1.2.7
======
- FIX: Default Country
- FIX: Import of new stores

======
1.2.6
======
- NEW: Sort by distance or alphabetically (settings > result list > sort by)
- NEW: Try updating stores during import (updating process is checked by store name)
- FIX: Store categories / filters are now sorted alphabetically ASC

======
1.2.5
======
- NEW: Import Store Opening Hours
- NEW: Export Opening Hours
- NEW: Slightly settings panel improvements

======
1.2.4
======
- FIX: DB Prepare statement

======
1.2.3
======
- NEW: Set a default store category to be active (Settings > Search Box > Default filter category)
- NEW: Description now also shows up in result list
- FIX: Description will now be exported correctly
- FIX: Description does not show up when no email was set up

======
1.2.2
======
- NEW:  When importing stores with no Latitute / Longitute (lat / lng) the system will try to fetch the data from Google Reverse Maps
		You will need to set a extra server side API Key this on the settings panel
- FIX: Result list height in the Modal

======
1.2.1
======
- NEW:  Use custom Map Styling (e.g. from https://snazzymaps.com OR https://mapstyle.withgoogle.com/)
		See settings -> Maps -> Styling
- NEW:  Select what happens when a user clicks on the stores name in the result list
		See settings -> Result list -> Link Action

======
1.2.0
======
- NEW: Automatically extend the map if no stores are found
- NEW: Excel files are now .xlsx (for Excel 2007 and higher), but you can still switch to Excel 5 if preferred
- NEW: Hide search Active filter
- NEW: Hide search Filters completley
- NEW: Hide search Title
- FIX: Hardcoded some CSS settings

======
1.1.7
======
- FIX: CSS bug border-box-size

======
1.1.6
======
- NEW: Autocomplete country restriction: restricts the users search only within a specific country
- NEW: Autocomplete type restriction. Only return city or zip code, but never street (specially used in US)
- NEW: Conditional Data loading (e.g. when a store has no email, it will not be ouputted as "undefined")

======
1.1.5
======
- NEW: Support for subcategories in Category Dropdown
- FIX: WP-Admin URL for settings buttons

======
1.1.4
======
- FIX: No Sidebar autoHeight
- FIX: Map Center position

======
1.1.3
======
- FIX: Issue with Sidebar Height
- FIX: Issue with Sidebar position

======
1.1.2
======
- FIX: Button does not show up on single product pages
- FIX: Search in this area button styling

======
1.1.1
======
- Name change to WordPress Store locator
- !!Important!! Before you upgrade make an export of your stores. Then update and import the exported stores. 

======
1.1.0
======
- NEW: Drag the map and a button appear to do a search in this area
- NEW: Stores & Categories can now be shown as items:
- NEW: Single Store: http://wordpress.db-dzine.com/store/clothes-1/
- NEW: Store Category: http://wordpress.db-dzine.com/store-categories/clothes-stores/
- NEW: Option to hide the get directions link
- NEW: Option to hide the search button
- NEW: Option to set a custom text for the search button
- NEW: Option to hide the result list title
- NEW: Updated Design
- NEW: When set a default position & auto location is disabled it automatically searches there
- NEW: Better Versioning
- FIX: Tel, Email, Web not showing on map
- FIX: Description showed "undefined"

======
1.0.10
======
- FIX: Import issue with categories (not appended, but replaced)

======
1.0.9
======
- NEW: Enter button now works in search field
- FIX: Description will now be shown
- FIX: Premium icon on all result items
- FIX: Versioning for JS / CSS files

======
1.0.8
======
- NEW:  When user declines HTML5 Geolocation or when it is not support, the user will now be 
		located by his IP address using the free service https://freegeoip.net (max. 10.000 requests per hour!)
- FIX: switched the Google Maps "Get Direction" position

======
1.0.7
======
- NEW: Code cleanup
- FIX: capability added for the import page

======
1.0.6
======
- NEW: Better plugin activation
- FIX: Better advanced settings page (ACE Editor for CSS and JS )
- FIX: array key exists

======
1.0.5
======
- FIX: Redux Framework error

======
1.0.4
======
- NEW: Import function for XLS-Files
- NEW: Get Sample Import file (dynamically creates categories / filters too)
- NEW: Export function to XLS
- NEW: Delete all stores

======
1.0.3
======
- FIX: Google new requires API Key for everything 
- FIX: API Key now also provided in backend

======
1.0.2
======
- FIX: error when Meta-Box Plugin was not installed & activated

======
1.0.1
======
- NEW: Removed the embedded Redux Framework AND Meta Boxes for update consistency
//* PLEASE MAKE SURE YOU INSTALL THE REDUX FRAMEWORK & Meta Box PLUGIN *//

======
1.0.0.3
======
- FIX: PHP 5.4 compatible errors

======
1.0.0.2
======
- FIX: Remove close button on shortcode pages
- FIX: when hiding active filters also hide the active filter text

======
1.0.0.1
======
- FIX: Bring back the close button 

======
1.0.0
======
- Inital release