jQuery( document ).ready( function( $ ) { 
var updateInterval;

$( "#wpsl-cpt-dialog" ).on( "click", function() {	
	$( "#wpsl-cpt-lightbox, #wpsl-cpt-overlay" ).show();
});

$( "#wpsl-cpt-overlay, #wpsl-cpt-lightbox .tb-close-icon" ).on( "click", function() {
	$( "#wpsl-cpt-lightbox, #wpsl-cpt-overlay" ).hide();
});

/* Start converting the locations to custom post types once the button is clicked */
$( "#wpsl-start-cpt-conversion" ).on( "click", function() {
	var ajaxData = {
		action: "convert_cpt",
		_ajax_nonce: $( this ).parents( "#wpsl-cpt-lightbox" ).find( "input[name='wpsl-cpt-fix-nonce']" ).val()
	};

	$( "#wpsl-cpt-lightbox .wpsl-preloader" ).show();
	$( ".wpsl-cpt-timeout" ).remove();	
	
	/* Make the ajax request to start the cpt conversion */ 
	$.get( ajaxurl, ajaxData, function( response ) {
		 if ( response == -1 ) {
			alert( wpslCptConversion.securityFail );
			stopConvertingCpt();
		}
	});
	
	/* Get the latest amount of locations that still need to be converted */
	updateInterval = setInterval( function() { convertCptCount(); }, 10000 );
	
	return false;
});

/**
 * Cancel the conversion count updates and hide the preloader.
 * 
 * @since 2.0
 * @return {void}
 */
function stopConvertingCpt() {
	clearInterval( updateInterval );
	$( ".wpsl-preloader" ).hide();	
}

/**
 * When the script that converts the locations to custom post types timed out, we show this msg.
 * 
 * @since 2.0
 * @return {void}
 */
function convertCptTimeoutMsg() {
	$( ".wslp-cpt-fix-wrap" ).after( "<p class='wpsl-cpt-timeout'>" + wpslCptConversion.timeout + "</p>" );	
}

/**
 * Make the ajax request to update the count of the
 * remaining locations that need to be converted.
 * 
 * @since 2.0
 * @return {void}
 */
function convertCptCount() { 
	var convertCount, ajaxData = {
		action: "convert_cpt_count",
		_ajax_nonce: $( "#wpsl-cpt-lightbox" ).find( "input[name='wpsl-cpt-conversion-count']" ).val()
	};
	
	$.ajaxQueue({
		url: ajaxurl,
		data: ajaxData,
		type: "GET"
	}).done( function( response ) {
		
		if ( response == -1 ) {
			stopConvertingCpt();
			alert( wpslCptConversion.securityFail );
		} else if ( typeof response.count !== "undefined" ) {
			convertCount = $( "#wpsl-cpt-lightbox p span" ).text();
			
			/* Check if the convert count still changes, if so the script is still running and we update the correct value. 
			 * If not then the convert script timed out and we show a different message. 
			 */
			if ( response.count != convertCount ) {
				$( ".wpsl-cpt-remaining span").html( response.count );	
			} else if ( response.count > 0 ) {
				stopConvertingCpt();
				convertCptTimeoutMsg();				
			}
		} else if ( typeof response.url !== "undefined" ) {
			$( ".wpsl-cpt-remaining" ).html( response.url ).parents( ".error" ).remove();
			$( ".wslp-cpt-fix-wrap" ).remove();
			stopConvertingCpt();	
		}
	});
}

/* Copy the remaining number of locations that need to be 
 * converted to custom post types to the thickbox field.
 */
if ( $( ".error .wpsl-cpt-remaining" ).length ) {
	var cptCount = parseInt( $( ".error .wpsl-cpt-remaining" ).text() );
	
	if ( isNaN( cptCount ) ) {
		cptCount = '-';	
	}
		
	$( ".wpsl-cpt-remaining span" ).html( cptCount );
}

});