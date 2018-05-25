/**
 * Insert the WPSL shortcode
 *
 * Grab the values from the thickbox form
 * and use them to set the wpsl shortcode attributes.
 *
 * @since 2.2.10
 */
function WPSL_InsertShortcode() {
    var markers, shortcodeAtts, checkboxColumns, catSelectionID, catSelection,
        win            = window.dialogArguments || opener || parent || top,
        startLocation  = jQuery( "#wpsl-start-location" ).val(),
        catFilterType  = jQuery( "#wpsl-cat-filter-types" ).val(),
        catRestriction = jQuery( "#wpsl-cat-restriction" ).val(),
        locateUser     = ( jQuery( "#wpsl-auto-locate" ).is( ":checked" ) ) ? true : false;

    shortcodeAtts = 'template="' + jQuery( "#wpsl-store-template" ).val() + '" map_type="' + jQuery( "#wpsl-map-type" ).val() + '" auto_locate="' + locateUser + '"';

    // Grab the values for the selected markers
    markers = WPSL_Selected_Markers();

    if ( typeof markers.start !== "undefined" ) {
        shortcodeAtts += ' start_marker="' + markers.start + '"';
    }

    if ( typeof markers.store !== "undefined" ) {
        shortcodeAtts += ' store_marker="' + markers.store + '"';
    }

    if ( startLocation ) {
        shortcodeAtts += ' start_location="' + startLocation + '"';
    }

    if ( typeof catRestriction !== "undefined" && catRestriction !== null && !catFilterType ) {
        shortcodeAtts += ' category="' + catRestriction + '"';
    }

    // Make sure we target the correct ID based on the filter type selection.
    if ( catFilterType == "dropdown" ) {
        catSelectionID = "wpsl-cat-selection";
    } else {
        catSelectionID = "wpsl-checkbox-selection";
    }

    catSelection = jQuery( '#' + catSelectionID + '' ).val();

    if ( catSelection ) {
        shortcodeAtts += ' category_selection="' + catSelection + '"';
    }

    if ( catFilterType ) {
        shortcodeAtts += ' category_filter_type="' + catFilterType + '"';
    }

    if ( catFilterType == "checkboxes" ) {
        checkboxColumns = parseInt( jQuery( "#wpsl-checkbox-columns" ).val() );

        if ( typeof checkboxColumns === 'number' ) {
            shortcodeAtts += ' checkbox_columns="' + checkboxColumns + '"';
        }
    }

    // Send the collected shortcode attributes to the editor
    win.send_to_editor("[wpsl " + shortcodeAtts + "]");
}

function WPSL_Selected_Markers() {
    var startMarker, storeMarker,
        markers = [],
        selectedMarkers = {};

    jQuery( ".wpsl-marker-list ").each( function( i ) {
        markers.push( jQuery( ".wpsl-marker-list:eq(" + i + " ) .wpsl-active-marker input" ).val());
    });

    if ( markers.length == 2 ) {
        startMarker = markers[0].split( "." );
        storeMarker = markers[1].split( "." );

        if ( typeof startMarker[0] !== "undefined" ) {
            selectedMarkers.start = startMarker[0];
        }

        if ( typeof storeMarker[0] !== "undefined" ) {
            selectedMarkers.store = storeMarker[0];
        }
    }

    return selectedMarkers;
}

jQuery( document ).ready( function( $ ) {
    $( "#wpsl-media-tabs" ).tabs();

    // Show the tooltips.
    $( ".wpsl-info" ).on( "mouseover", function() {
        $(this).find(".wpsl-info-text").show();
    });

    $( ".wpsl-info" ).on( "mouseout", function() {
        $(this).find( ".wpsl-info-text" ).hide();
    });

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

    $( "#wpsl-cat-filter-types" ).change( function() {
        var filterType = $( this ).val();

        if ( filterType == 'dropdown' ) {
            $( ".wpsl-cat-selection" ).show();
            $( ".wpsl-checkbox-options, .wpsl-cat-restriction, .wpsl-checkbox-selection" ).hide();
        } else if ( filterType == 'checkboxes' ) {
            $( ".wpsl-cat-selection, .wpsl-cat-restriction" ).hide();
            $( ".wpsl-checkbox-options, .wpsl-checkbox-selection" ).show();
        } else {
            $( ".wpsl-cat-restriction" ).show();
            $( ".wpsl-checkbox-options, .wpsl-cat-selection, .wpsl-checkbox-selection" ).hide();
        }
    });
});