jQuery( document ).ready( function( $ ) {
    $( "#wpsl-csv-tools-form" ).submit( function ( e ) {
        if ( $( "#wpsl-bulk-delete" ).is( ":checked" ) ) {
            e.preventDefault();

            $( "#dialog" ).dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Yes": function() {
                        $( this ).dialog( "close" );
                        $( "#wpsl-csv-tools-form" ).unbind( "submit" ).submit();
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        }
    });

    $( ".wpsl-slide-option" ).change( function() {
        $( this ).parents( "tr" ).next( "tr" ).toggle();
    });
});