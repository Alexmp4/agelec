jQuery( document ).ready( function( $ ) {
var map, geocoder, markersArray = [];

if ( $( "#wpsl-gmap-wrap" ).length ) {
	initializeGmap();
}

/*
 * If we're on the settings page, then check for returned
 * browser key errors from the autocomplete field
 * and validate the server key when necessary.
 */
if ( $( "#wpsl-map-settings").length ) {
	observeBrowserKeyErrors();

    /**
	 * Check if we need to validate the server key, and if no
	 * error message is already visible after saving the setting page.
     */
    if ( $( "#wpsl-api-server-key" ).hasClass( "wpsl-validate-me" ) && !$( "#setting-error-server-key" ).length ) {
    	validateServerKey();
	}
}

/**
 * Initialize the map with the correct settings.
 *
 * @since	1.0.0
 * @returns {void}
 */
function initializeGmap() {
	var defaultLatLng = wpslSettings.defaultLatLng.split( "," ),
		latLng = new google.maps.LatLng( defaultLatLng[0], defaultLatLng[1] ),
		mapOptions = {};

	mapOptions = {
		zoom: parseInt( wpslSettings.defaultZoom ),
		center: latLng,
		mapTypeId: google.maps.MapTypeId[ wpslSettings.mapType.toUpperCase() ],
		mapTypeControl: false,
		streetViewControl: false,
		zoomControlOptions: {
			position: google.maps.ControlPosition.RIGHT_TOP
		}
	};		

	geocoder = new google.maps.Geocoder();
	map		 = new google.maps.Map( document.getElementById( "wpsl-gmap-wrap" ), mapOptions );

	checkEditStoreMarker();
}

/**
 * Check if we have an existing latlng value.
 * 
 * If there is an latlng value, then we add a marker to the map.
 * This can only happen on the edit store page.
 *
 * @since	1.0.0
 * @returns {void}
 */
function checkEditStoreMarker() {
	var location,
		lat = $( "#wpsl-lat" ).val(),
		lng = $( "#wpsl-lng" ).val();
	
	if ( ( lat ) && ( lng ) ) {
		location = new google.maps.LatLng( lat, lng );

		map.setCenter( location );
		map.setZoom( 16 );
		addMarker( location );
	}
}

// If we have a city/country input field enable the autocomplete.
if ( $( "#wpsl-start-name" ).length ) {
	activateAutoComplete();	
}

/**
 * Activate the autocomplete function for the city/country field.
 *
 * @since	1.0.0
 * @returns {void}
 */
function activateAutoComplete() {
	var latlng,
		input = document.getElementById( "wpsl-start-name" ),
		options = {
		  types: ['geocode']
		},
		autocomplete = new google.maps.places.Autocomplete( input, options );

	google.maps.event.addListener( autocomplete, "place_changed", function() {
		latlng = autocomplete.getPlace().geometry.location;
		setLatlng( latlng, "zoom" );
	});	
}

/**
 * Add a new marker to the map based on the provided location (latlng).
 *
 * @since	1.0.0
 * @param   {object} location The latlng value
 * @returns {void}
 */
function addMarker( location ) {
	var marker = new google.maps.Marker({
		position: location,
		map: map,
		draggable: true
	});
	
	markersArray.push( marker );
	
	// If the marker is dragged on the map, make sure the latlng values are updated.
	google.maps.event.addListener( marker, "dragend", function() {
		setLatlng( marker.getPosition(), "store" );
	});
}

// Lookup the provided location with the Google Maps API.
$( "#wpsl-lookup-location" ).on( "click", function( e ) {
	e.preventDefault();
	codeAddress();
});

/**
 * Update the hidden input field with the current latlng values.
 *
 * @since	1.0.0
 * @param   {object} latLng The latLng values
 * @param   {string} target The location where we need to set the latLng
 * @returns {void}
 */
function setLatlng( latLng, target ) {
	var coordinates = stripCoordinates( latLng ),
		lat			= roundCoordinate( coordinates[0] ),
		lng			= roundCoordinate( coordinates[1] );

	if ( target == "store" ) {
		$( "#wpsl-lat" ).val( lat );
		$( "#wpsl-lng" ).val( lng );
	} else if ( target == "zoom" ) {
		$( "#wpsl-latlng" ).val( lat + ',' + lng );
	}
}

/**
 * Geocode the user input. 
 *
 * @since	1.0.0
 * @returns {void}
 */
function codeAddress() {
    var filteredResponse, geocodeAddress;
		
	// Check if we have all the required data before attempting to geocode the address.
	if ( !validatePreviewFields() ) {
		geocodeAddress = createGeocodeAddress();

		geocoder.geocode( { 'address': geocodeAddress }, function( response, status ) {
			if ( status === google.maps.GeocoderStatus.OK ) {

				// If we have a previous marker on the map we remove it.
				if ( typeof( markersArray[0] ) !== "undefined" ) {
					if ( markersArray[0].draggable ) {
						markersArray[0].setMap( null );
						markersArray.splice(0, 1);
					}
				}

				// Center and zoom to the searched location.
				map.setCenter( response[0].geometry.location );
				map.setZoom( 16 );

				addMarker( response[0].geometry.location );
				setLatlng( response[0].geometry.location, "store" );

				filteredResponse = filterApiResponse( response );

				$( "#wpsl-country" ).val( filteredResponse.country.long_name );
				$( "#wpsl-country_iso" ).val( filteredResponse.country.short_name );
			} else {
				alert( wpslL10n.geocodeFail + ": " + status );
			}
		});

		return false;
	} else {
		activateStoreTab( "first" );

		alert( wpslL10n.missingGeoData );

		return true;
	}
}

/**
 * Check that all required fields for the map preview are there. 
 *
 * @since	1.0.0
 * @returns {boolean} error  Whether all the required fields contained data.
 */
function validatePreviewFields() {
	var i, fieldData, requiredFields,
		error = false;

	$( ".wpsl-store-meta input" ).removeClass( "wpsl-error" );

	// Check which fields are required.
    if ( typeof wpslSettings.requiredFields !== "undefined" && _.isArray( wpslSettings.requiredFields ) ) {
        requiredFields = wpslSettings.requiredFields;

        // Check if all the required fields contain data.
        for ( i = 0; i < requiredFields.length; i++ ) {
            fieldData = $.trim( $( "#wpsl-" + requiredFields[i] ).val() );

            if ( !fieldData ) {
                $( "#wpsl-" + requiredFields[i] ).addClass( "wpsl-error" );
                error = true;
            }

            fieldData = '';
        }
    }

	return error;
}

/**
 * Build the address that's send to the Geocode API.
 *
 * @since	2.1.0
 * @returns {string} geocodeAddress The address separated by , that's send to the Geocoder.
 */
function createGeocodeAddress() {
	var i, part,
		address      = [], 
		addressParts = [ "address", "city", "state", "zip", "country" ];

	for ( i = 0; i < addressParts.length; i++ ) {
		part = $.trim( $( "#wpsl-" + addressParts[i] ).val() );

		/*
		 * At this point we already know the address, city and country fields contain data.
		 * But no need to include the zip and state if they are empty.
		 */ 
		if ( part ) {
			address.push( part );
		}

		part = "";
	}

	return address.join();
}

/**
 * Filter out the country name from the API response.
 *
 * @since	1.0.0
 * @param   {object} response	   The response of the geocode API
 * @returns {object} collectedData The short and long country name
 */
function filterApiResponse( response ) {
	var i, responseType,
		country		  = {},
		collectedData = {},
		addressLength = response[0].address_components.length;
	
	// Loop over the API response.
	for ( i = 0; i < addressLength; i++ ) {
		responseType = response[0].address_components[i].types;

		// Filter out the country name.
		if ( /^country,political$/.test( responseType ) ) {
			country = {
				long_name: response[0].address_components[i].long_name,
				short_name: response[0].address_components[i].short_name
			};
		}
	}
	
	collectedData = {
		country: country
	};
	
	return collectedData;
}

/**
 * Round the coordinate to 6 digits after the comma. 
 *
 * @since	1.0.0
 * @param   {string} coordinate   The coordinate
 * @returns {number} roundedCoord The rounded coordinate
 */
function roundCoordinate( coordinate ) {
	var roundedCoord, decimals = 6;
	
	roundedCoord = Math.round( coordinate * Math.pow( 10, decimals ) ) / Math.pow( 10, decimals );
	
	return roundedCoord;
}

/**
 * Strip the '(' and ')' from the captured coordinates and split them. 
 *
 * @since	1.0.0
 * @param   {string} coordinates The coordinates
 * @returns {object} latLng      The latlng coordinates
 */
function stripCoordinates( coordinates ) {
	var latLng    = [],
		selected  = coordinates.toString(),
		latLngStr = selected.split( ",", 2 );

	latLng[0] = latLngStr[0].replace( "(", "" );
	latLng[1] = latLngStr[1].replace( ")", "" );	

	return latLng;
}

$( ".wpsl-marker-list input[type=radio]" ).click( function() {
	$( this ).parents( ".wpsl-marker-list" ).find( "li" ).removeClass();
	$( this ).parent( "li" ).addClass( "wpsl-active-marker" );
});

$( ".wpsl-marker-list li" ).click( function() {
	$( this ).parents( ".wpsl-marker-list" ).find( "input" ).prop( "checked", false );
	$( this ).find( "input" ).prop( "checked", true );
	$( this ).siblings().removeClass();
	$( this ).addClass( "wpsl-active-marker" );
});

// Handle a click on the dismiss button. So that the warning msg that no starting point is set is disabled.
$( ".wpsl-dismiss" ).click( function() {
	var $link = $( this ),
		data = {
			action: "disable_location_warning",
			_ajax_nonce: $link.attr( "data-nonce" )
		};
		
	$.post( ajaxurl, data );
	
	$( ".wpsl-dismiss" ).parents( ".error" ).remove();

	return false;
});

// Detect changes in checkboxes that have a conditional option.
$( ".wpsl-has-conditional-option" ).on( "change", function() {
	$( this ).parent().next( ".wpsl-conditional-option" ).toggle();
});

/* 
 * Detect changes to the store template dropdown. If the template is selected to 
 * show the store list under the map then we show the option to hide the scrollbar.
 */
$( "#wpsl-store-template" ).on( "change", function() {
	var $scrollOption = $( "#wpsl-listing-below-no-scroll" );

	if ( $( this ).val() == "below_map" ) {
		$scrollOption.show();
	} else {
		$scrollOption.hide();
	}
});

$( "#wpsl-api-region" ).on( "change", function() {
	var $geocodeComponent = $( "#wpsl-geocode-component" );
	
	if ( $( this ).val() ) {
		$geocodeComponent.show();
	} else {
		$geocodeComponent.hide();
	}
});

// Make sure the correct hour input format is visible.
$( "#wpsl-editor-hour-input" ).on( "change", function() {
	$( ".wpsl-" + $( this ).val() + "-hours" ).show().siblings( "div" ).hide();
	$( ".wpsl-hour-notice" ).toggle();
});

// Set the correct tab to active and show the correct content block.
$( "#wpsl-meta-nav li" ).on( "click", function( e ) {
	var activeClass = $( this ).attr( "class" );
		activeClass = activeClass.split( "-tab" );

	e.stopPropagation();
	
	// Set the correct tab and metabox to active.
	$( this ).addClass( "wpsl-active" ).siblings().removeClass( "wpsl-active" );
	$( ".wpsl-store-meta ." + activeClass[0] + "" ).show().addClass( "wpsl-active" ).siblings( "div" ).hide().removeClass( "wpsl-active" );
});

// Make sure the required store fields contain data.
if ( $( "#wpsl-store-details" ).length ) {
	$( "#publish" ).click( function() {
		var firstErrorElem, currentTabClass, elemClass,
			errorMsg	= '<div id="message" class="error"><p>' + wpslL10n.requiredFields + '</p></div>',
			missingData = false;

		// Remove error messages and css classes from previous submissions.
		$( "#wpbody-content .wrap #message" ).remove();
		$( ".wpsl-required" ).removeClass( "wpsl-error" );

		// Loop over the required fields and check for a value.
		$( ".wpsl-required" ).each( function() {
			if ( $( this ).val() == "" ) {
				$( this ).addClass( "wpsl-error" );

				if ( typeof firstErrorElem === "undefined" ) {
					firstErrorElem = getFirstErrorElemAttr( $( this ) );
				}

				missingData = true;
			}
		});
		
		// If one of the required fields are empty, then show the error msg and make sure the correct tab is visible.
		if ( missingData ) {	
			$( "#wpbody-content .wrap > h2" ).after( errorMsg );

			if ( typeof firstErrorElem.val !== "undefined" ) {
				if ( firstErrorElem.type == "id" ) {
					currentTabClass = $( "#" + firstErrorElem.val + "" ).parents( ".wpsl-tab" ).attr( "class" );
					$( "html, body" ).scrollTop( Math.round( $( "#" + firstErrorElem.val + "" ).offset().top - 100 ) );
				} else if ( firstErrorElem.type == "class" ) {
					elemClass		= firstErrorElem.val.replace( /wpsl-required|wpsl-error/g, "" );
					currentTabClass = $( "." + elemClass + "" ).parents( ".wpsl-tab" ).attr( "class" );
					$( "html, body" ).scrollTop( Math.round( $( "." + elemClass + "" ).offset().top - 100 ) );
				}

				currentTabClass = $.trim( currentTabClass.replace( /wpsl-tab|wpsl-active/g, "" ) );
			}			
			
			// If we don't have a class of the tab that should be set to visible, we just show the first one.
			if ( !currentTabClass ) {
				activateStoreTab( 'first' );
			} else {
				activateStoreTab( currentTabClass );
			}
			
			/* 
			 * If not all required fields contains data, and the user has 
			 * clicked the submit button. Then an extra css class is added to the 
			 * button that will disabled it. This only happens in WP 3.8 or earlier.
			 * 
			 * We need to make sure this css class doesn't exist otherwise 
			 * the user can never resubmit the page.
			 */
			$( "#publish" ).removeClass( "button-primary-disabled" );
			$( ".spinner" ).hide();

			return false;
		} else {
			return true;
		}	
	});
}

/**
 * Set the correct tab to visible, and hide all other metaboxes
 *
 * @since	2.0.0
 * @param   {string} $target The name of the tab to show
 * @returns {void}
 */
function activateStoreTab( $target ) {
	if ( $target == 'first' ) {
		$target = ':first-child';
	} else {
		$target = '.' + $target;
	}
	
	if ( !$( "#wpsl-meta-nav li" + $target + "-tab" ).hasClass( "wpsl-active" ) ) {
		$( "#wpsl-meta-nav li" + $target + "-tab" ).addClass( "wpsl-active" ).siblings().removeClass( "wpsl-active" );
		$( ".wpsl-store-meta > div" + $target + "" ).show().addClass( "wpsl-active" ).siblings( "div" ).hide().removeClass( "wpsl-active" );
	}
}

/**
 * Get the id or class of the first element that's an required field, but is empty.
 * 
 * We need this to determine which tab we need to set active,
 * which will be the tab were the first error occured. 
 * 
 * @since	2.0.0
 * @param   {object} elem			The element the error occured on
 * @returns {object} firstErrorElem The id/class set on the first elem that an error occured on and the attr value
 */
function getFirstErrorElemAttr( elem ) {
	var firstErrorElem = { "type": "id", "val" : elem.attr( "id" ) };

	// If no ID value exists, then check if we can get the class name.
	if ( typeof firstErrorElem.val === "undefined" ) {
		firstErrorElem = { "type": "class", "val" : elem.attr( "class" ) };
	}
	
	return firstErrorElem;
}

// If we have a store hours dropdown, init the event handler.
if ( $( "#wpsl-store-hours" ).length ) {
	initHourEvents();
}

/**
 * Assign an event handler to the button that enables 
 * users to remove an opening hour period.
 * 
 * @since	2.0.0
 * @returns {void}
 */
function initHourEvents() {
	$( "#wpsl-store-hours .wpsl-icon-cancel-circled" ).off();
	$( "#wpsl-store-hours .wpsl-icon-cancel-circled" ).on( "click", function() {
		removePeriod( $( this ) );
	});
}

// Add new openings period to the openings hours table.
$( ".wpsl-add-period" ).on( "click", function( e ) {
	var newPeriod,
		hours		= {},
		returnList  = true,
		$tr			= $( this ).parents( "tr" ),
		periodCount = currentPeriodCount( $( this ) ),
		periodCss   = ( periodCount >= 1 ) ? "wpsl-current-period wpsl-multiple-periods" : "wpsl-current-period",
		day 	    = $tr.find( ".wpsl-opening-hours" ).attr( "data-day" ),	
		selectName  = ( $( "#wpsl-settings-form" ).length ) ? "wpsl_editor[dropdown]" : "wpsl[hours]";

	newPeriod = '<div class="' + periodCss +'">';
	newPeriod += '<select autocomplete="off" name="' + selectName + '[' + day + '_open][]" class="wpsl-open-hour">' + createHourOptionList( returnList ) + '</select>';
	newPeriod += '<span> - </span>';
	newPeriod += '<select autocomplete="off" name="' + selectName + '[' + day + '_close][]" class="wpsl-close-hour">' + createHourOptionList( returnList ) + '</select>';
	newPeriod += '<div class="wpsl-icon-cancel-circled"></div>';
	newPeriod += '</div>';

	$tr.find( ".wpsl-store-closed" ).remove();		
	$( "#wpsl-hours-" + day + "" ).append( newPeriod ).end();

	initHourEvents();
	
	if ( $( "#wpsl-editor-hour-format" ).val() == 24 ) {
		hours = {
			"open": "09:00",
			"close": "17:00"
		};
	} else {
		hours = {
			"open": "9:00 AM",
			"close": "5:00 PM"
		};
	}
	
	$tr.find( ".wpsl-open-hour:last option[value='" + hours.open + "']" ).attr( "selected", "selected" );
	$tr.find( ".wpsl-close-hour:last option[value='" + hours.close + "']" ).attr( "selected", "selected" );

	e.preventDefault();
});

/**
 * Remove an openings period
 * 
 * @since  2.0.0
 * @param  {object} elem The clicked element
 * @return {void}
 */	
function removePeriod( elem ) {
	var periodsLeft	= currentPeriodCount( elem ),
		$tr			= elem.parents( "tr" ),
		day 	    = $tr.find( ".wpsl-opening-hours" ).attr( "data-day" );
	
	// If there was 1 opening hour left then we add the 'Closed' text.
	if ( periodsLeft == 1 ) {
		$tr.find( ".wpsl-opening-hours" ).html( "<p class='wpsl-store-closed'>" + wpslL10n.closedDate + "<input type='hidden' name='wpsl[hours][" + day + "_open]' value='' /></p>" );
	}	
	
	// Remove the selected openings period.
	elem.parent().closest( ".wpsl-current-period" ).remove();
	
	// If the first element has the multiple class, then we need to remove it.
	if ( $tr.find( ".wpsl-opening-hours div:first-child" ).hasClass( "wpsl-multiple-periods" ) ) {
		$tr.find( ".wpsl-opening-hours div:first-child" ).removeClass( "wpsl-multiple-periods" );
	}
}

/**
 * Count the current opening periods in a day block
 * 
 * @since  2.0.0
 * @param  {object} elem		   The clicked element
 * @return {string} currentPeriods The ammount of period divs found
 */	
function currentPeriodCount( elem ) {
	var currentPeriods = elem.parents( "tr" ).find( ".wpsl-current-period" ).length;

	return currentPeriods;
}

/**
 * Create an option list with the correct opening hour format and interval
 * 
 * @since  2.0.0
 * @param  {string} returnList Whether to return the option list or call the setSelectedOpeningHours function
 * @return {mixed}  optionList The html for the option list of or void
 */	
function createHourOptionList( returnList ) {
	var openingHours, openingHourInterval, hour, hrFormat,
		pm   			   = false,
        twelveHrsAfternoon = false,
		pmOrAm			   = "",
		optionList		   = "",
		openingTimes 	   = [],
		openingHourOptions = {
			"hours": { 
				"hr12": [ 12, 1, 2, 3 ,4 ,5 ,6, 7, 8, 9, 10, 11, 12, 1, 2, 3 , 4, 5, 6, 7, 8, 9, 10, 11 ],
				"hr24": [ 0, 1, 2, 3 ,4 ,5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 ,16, 17, 18, 19, 20, 21, 22, 23 ]
			},
			"interval": [ '00', '15', '30', '45' ]
		};

	if ( $( "#wpsl-editor-hour-format" ).length ) {
		hrFormat = $( "#wpsl-editor-hour-format" ).val();
	} else {
		hrFormat = wpslSettings.hourFormat;	
	}
	
	$( "#wpsl-store-hours td" ).removeAttr( "style" );

	if ( hrFormat == 12 ) {
		$( "#wpsl-store-hours" ).removeClass().addClass( "wpsl-twelve-format" );
		openingHours = openingHourOptions.hours.hr12;
	} else {
		$( "#wpsl-store-hours" ).removeClass().addClass( "wpsl-twentyfour-format" );
		openingHours = openingHourOptions.hours.hr24;
	}

	openingHourInterval = openingHourOptions.interval;

	for ( var i = 0; i < openingHours.length; i++ ) {
		hour = openingHours[i];

		/* 
		 * If the 12hr format is selected, then check if we need to show AM or PM.
		 * 
		 * If the 24hr format is selected and the hour is a single digit 
		 * then we add a 0 to the start so 5:00 becomes 05:00. 
		 */
		if ( hrFormat == 12 ) {
			if ( hour >= 12 ) {
				pm = ( twelveHrsAfternoon ) ? true : false;

                twelveHrsAfternoon = true;
			}

			pmOrAm = ( pm ) ? "PM" : "AM";
		} else if ( ( hrFormat == 24 ) && ( hour.toString().length == 1 ) ) {
			hour = "0" + hour;
		}

		// Collect the new opening hour format and interval.
		for ( var j = 0; j < openingHourInterval.length; j++ ) {
			openingTimes.push( hour + ":" + openingHourInterval[j] + " " + pmOrAm );
		}				
	}

	// Create the <option> list.
	for ( var i = 0; i < openingTimes.length; i++ ) {
		optionList = optionList + '<option value="' + $.trim( openingTimes[i] ) + '">' + $.trim( openingTimes[i] ) + '</option>';
	}
	
	if ( returnList ) {
		return optionList;
	} else {
		setSelectedOpeningHours( optionList, hrFormat );
	}
}

/**
 * Set the correct selected opening hour in the dropdown
 * 
 * @since  2.0.0
 * @param  {string} optionList The html for the option list 
 * @param  {string} hrFormat   The html for the option list
 * @return {void}
 */	
function setSelectedOpeningHours( optionList, hrFormat ) {
	var splitHour, hourType, periodBlock,		
		hours = {};
		
	/* 
	 * Loop over each open/close block and make sure the selected 
	 * value is still set as selected after changing the hr format.
	 */			
	$( ".wpsl-current-period" ).each( function() {
		periodBlock = $( this ),
		hours 		= {
			"open": $( this ).find( ".wpsl-open-hour" ).val(),
			"close": $( this ).find( ".wpsl-close-hour" ).val()
		};
						
		// Set the new hour format for both dropdowns.
		$( this ).find( "select" ).html( optionList ).promise().done( function() {

			// Select the correct start/end hours as selected.
			for ( var key in hours ) {
				if ( hours.hasOwnProperty( key ) ) {
					
					// Breakup the hour, so we can check the part before and after the : separately.
					splitHour = hours[key].split( ":" );

					if ( hrFormat == 12 ) {
						hourType = "";

						// Change the hours to a 12hr format and add the correct AM or PM.
						if ( hours[key].charAt( 0 ) == 0 ) {
							hours[key] = hours[key].substr( 1 );
							hourType   = " AM";
						} else if ( ( splitHour[0].length == 2 ) && ( splitHour[0] > 12 ) ) {									
							hours[key] = ( splitHour[0] - 12 ) + ":" + splitHour[1];
							hourType   = " PM";
						} else if ( splitHour[0] < 12 ) {
							hours[key] = splitHour[0] + ":" + splitHour[1];
							hourType   = " AM";
						} else if ( splitHour[0] == 12 ) {
							hours[key] = splitHour[0] + ":" + splitHour[1];
							hourType   = " PM";
						}

						// Add either AM or PM behind the time.
						if ( ( splitHour[1].indexOf( "PM" ) == -1 ) && ( splitHour[1].indexOf( "AM" ) == -1 ) ) {
							hours[key] = hours[key] + hourType;
						}

					} else if ( hrFormat == 24 ) {								
						
						// Change the hours to a 24hr format and remove the AM or PM.
						if ( splitHour[1].indexOf( "PM" ) != -1 ) {		
							if ( splitHour[0] == 12 ) {
								hours[key] = "12:" + splitHour[1].replace( " PM", "" );
							} else {
								hours[key] = ( + splitHour[0] + 12 ) + ":" + splitHour[1].replace( " PM", "" );	
							}
						} else if ( splitHour[1].indexOf( "AM" ) != -1 ) {
							if ( splitHour[0].toString().length == 1 ) {
								hours[key] = "0" + splitHour[0] + ":" + splitHour[1].replace( " AM", "" );			
							} else {
								hours[key] = splitHour[0] + ":" + splitHour[1].replace( " AM", "" );
							}
						} else {
							hours[key] = splitHour[0] + ":" + splitHour[1]; // When the interval is changed
						}
					}
					
					// Set the correct value as the selected one.
					periodBlock.find( ".wpsl-" + key + "-hour option[value='" + $.trim( hours[key] ) + "']" ).attr( "selected", "selected" );
				}
			}

		});		
	});
}

// Update the opening hours format if one of the dropdown values change.
$( "#wpsl-editor-hour-format, #wpsl-editor-hour-interval" ).on( "change", function() {
	createHourOptionList();
});

// Show the tooltips.
$( ".wpsl-info" ).on( "mouseover", function() {	
	$( this ).find( ".wpsl-info-text" ).show();
});

$( ".wpsl-info" ).on( "mouseout", function() {	
	$( this ).find( ".wpsl-info-text" ).hide();
});

// If the start location is empty, then we color the info icon red instead of black.
if ( $( "#wpsl-latlng" ).length && !$( "#wpsl-latlng" ).val() ) {
	$( "#wpsl-latlng" ).siblings( "label" ).find( ".wpsl-info" ).addClass( "wpsl-required-setting" );
}

/** 
 * Try to apply the custom style data to the map.
 * 
 * If the style data is invalid json we show an error.
 * 
 * @since  2.0.0
 * @return {void}
 */
function tryCustomMapStyle() {
	var validStyle = "", 
		mapStyle   = $.trim( $( "#wpsl-map-style" ).val() );

	$( ".wpsl-style-preview-error" ).remove();
	
	if ( mapStyle ) {
		
		// Make sure the data is valid json.
		validStyle = tryParseJSON( mapStyle );

		if ( !validStyle ) {
			$( "#wpsl-style-preview" ).after( "<div class='wpsl-style-preview-error'>" + wpslL10n.styleError + "</div>" );
		}
	}	

	map.setOptions({ styles: validStyle });
}

// Handle the map style changes on the settings page.
if ( $( "#wpsl-map-style" ).val() ) {
	tryCustomMapStyle();
}

// Handle clicks on the map style preview button.
$( "#wpsl-style-preview" ).on( "click", function() {
	tryCustomMapStyle();
	
	return false;
});

/**
 * Make sure the JSON is valid. 
 * 
 * @link   http://stackoverflow.com/a/20392392/1065294 
 * @since  2.0.0
 * @param  {string} jsonString The JSON data
 * @return {object|boolean}	   The JSON string or false if it's invalid json.
 */
function tryParseJSON( jsonString ) {
	
    try {
        var o = JSON.parse( jsonString );

        /* 
		 * Handle non-exception-throwing cases:
		 * Neither JSON.parse(false) or JSON.parse(1234) throw errors, hence the type-checking,
		 * but... JSON.parse(null) returns 'null', and typeof null === "object", 
		 * so we must check for that, too.
		 */ 
        if ( o && typeof o === "object" && o !== null ) {
            return o;
        }
    }
    catch ( e ) { }

    return false;
}

/**
 * Look for changes on the start location input field.
 *
 * If there's a problem with the browser API key,
 * then a 'gm-err-autocomplete' class is added to the input field.
 *
 * When this happens we create a notice explaining how to fix the issue.
 *
 * @since 2.2.10
 * @return void
 */
function observeBrowserKeyErrors() {
    var observer,
		attributeValue 	 = '',
		MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,
		$startName 		 = $( "#wpsl-start-name" );

	if ( typeof MutationObserver !== "undefined" ) {
		observer = new MutationObserver( function( mutations ) {

			// Loop over the mutations.
			mutations.forEach( function( mutation ) {
				if ( mutation.attributeName === "class" ) {
					attributeValue = $( mutation.target ).prop( mutation.attributeName );

					// Look for a specific class that's added when there's a problem with the browser API key
					if ( ( attributeValue.indexOf( "gm-err-autocomplete" ) !== -1 ) ) {
                        createErrorNotice( wpslL10n.browserKeyError, "browser-key" );
					}
				}
			});
		});

		observer.observe( $startName[0], {
			attributes: true
		});
	}
}

/**
 * Make a request to the geocode API with the
 * provided server key to check for any errors.
 *
 * @since 2.2.10
 * @return void
 */
function validateServerKey() {
    var ajaxData = {
        action: "validate_server_key",
        server_key: $( "#wpsl-api-server-key" ).val()
    };

    $.get( wpslSettings.ajaxurl, ajaxData, function( response ) {
        if ( !response.valid && typeof response.msg !== "undefined" ) {
            createErrorNotice( response.msg, "server-key" );
		} else {
            $( "#wpsl-api-server-key" ).removeClass( "wpsl-error" );
        }
    });
}

/**
 * Create the error notice.
 *
 * @since 2.2.10
 * @param {string} errorMsg The error message to show
 * @param {string} type 	The type of API key we need to show the notice for
 * @return void
 */
function createErrorNotice( errorMsg, type ) {
	var errorNotice, noticeLocation;

	errorNotice = '<div id="setting-error-' + type + '" class="error settings-error notice is-dismissible">';
	errorNotice += '<p><strong>' + errorMsg + '</strong></p>';
	errorNotice += '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' + wpslL10n.dismissNotice + '</span></button>';
	errorNotice += '</div>';

	noticeLocation = ( $( "#wpsl-tabs" ).length ) ? 'wpsl-tabs' : 'wpsl-settings-form';

	$( "#" + noticeLocation + "" ).before( errorNotice );
	$( "#wpsl-api-" + type + "").addClass( "wpsl-error" );
}

// Make sure the custom error notices can be removed
$( "#wpsl-wrap" ).on( "click", "button.notice-dismiss", function() {
    $( this ).closest( 'div.notice' ).remove();
})

});


