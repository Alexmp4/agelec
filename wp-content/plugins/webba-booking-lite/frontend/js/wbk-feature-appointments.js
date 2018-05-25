// WEBBA Booking frontent feature appointments scripts
// onload function
jQuery(function ($) {
	wbk_aplyTableFiltering();	 
});

function wbk_aplyTableFiltering(){
	jQuery( document ).trigger( 'enhance.tablesaw' );
	jQuery( 'th#date' ).data( 'tablesaw-sort', function( ascending ) {
	    // return a function
	    return function( a, b ) {
	        // use a.cell and b.cell for cell values
	        var dateA =   Date.parse(  a.cell );
	        var dateB =   Date.parse(  b.cell );	

	        if( ascending ) {
	            return dateA > dateB;
	        } else { // descending
	            return dateA < dateB;
	        }
	    };
	});
	jQuery( 'th#time' ).data( 'tablesaw-sort', function( ascending ) {
	    // return a function
	    return function( a, b ) {
	        // use a.cell and b.cell for cell values       
	        var dateA =   Date.parse( 'January 1, 2015 ' + a.cell );
	        var dateB =   Date.parse( 'January 1, 2015 ' + b.cell );
	        if( ascending ) {
	            return dateA > dateB;
	        } else { // descending
	            return dateA < dateB;
	        }
	    };
	});
}

