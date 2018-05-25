// WEBBA Booking service dialog javascript
// onload functions
jQuery(function ($) {
	jQuery( '#wbk-service-id' ).change( function(){
		if(  jQuery(this).val() != 0 ){
			jQuery( '#wbk-category-id' ).val(0);
		//	jQuery( '#wbk-category-id' ).prop('disabled', true );
		} else {
			jQuery( '#wbk-category-id' ).val(0);	 
		//	jQuery( '#wbk-category-id' ).prop('disabled', false );
		}
	});
	jQuery( '#wbk-category-id' ).change( function(){
		if(  jQuery(this).val() != 0 ){
			jQuery( '#wbk-service-id' ).val(0);
		//	jQuery( '#wbk-service-id' ).prop('disabled', true );
		} else {
			jQuery( '#wbk-service-id' ).val(0);	 
		//	jQuery( '#wbk-service-id' ).prop('disabled', false );
		}
	});
 	jQuery('#wbk-add-shortcode').click(function() {
 	 	jQuery( '#wbk-service-dialog' ).dialog({
		resizable: false,
		width: 400,
		title: wbkl10n.formtitle,
	    height:240,
	    modal: true,
	    buttons: [
	    	{
	    		text: wbkl10n.add,
	    		click: function() {
					var service_id = jQuery( '#wbk-service-id' ).val();
					var category_id = jQuery( '#wbk-category-id' ).val();

					if ( service_id == 0 && category_id == 0) {
				        window.send_to_editor('[webba_booking]');
					} else {
						if( service_id != 0 ){
							window.send_to_editor('[webba_booking service="' + service_id + '"]');						
						}
						if( category_id != 0 ){
							window.send_to_editor('[webba_booking category="' + category_id + '"]');						
						}
					}
					jQuery( '#wbk-service-dialog' ).dialog( 'close' ); 
	    		}
	    	},
	    	{
	    		text: wbkl10n.cancel,
	    		click: function() {
	    			jQuery( '#wbk-service-dialog' ).dialog( 'close' ); 		
	    		} 
	    	}
		    ]
	    });
 	});
 	jQuery('#wbk-add-shortcode-landing').click(function() {
 		window.send_to_editor('[webba_email_landing]');	
 	});

});