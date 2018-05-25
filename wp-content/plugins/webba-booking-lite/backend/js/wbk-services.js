// WEBBA Booking services page javascript
// onload functions
jQuery(function ($) {
	setRowCheckBoxEvent();
    setIntervalOnChange();
    setDurationIntervalOnChange();
	setQuantityOnChange();
    jQuery( 'input' ).not('#wbk-service-quantity').focus(function() {
		jQuery( this ).removeClass('wbk-input-error');
	});
	jQuery('#wbk-payment-methods').chosen({width: '95%'});
	jQuery('#wbk-service-gg_calendar').chosen({width: '95%'});

	jQuery('#wbk-user-list').chosen({width: '95%'});
	var wbk_date_format = jQuery('#wbk_backend_date_format').val();
	jQuery('#wbk-service-date-range').datepick({ rangeSelect: true, dateFormat: wbk_date_format });
});
// set row checkbox event
function setRowCheckBoxEvent() {
	jQuery('[id^=chk_row_]').change( function() {    	
    	// get id of checkbox
		var chk_row_id = jQuery(this).attr('id');  
		chk_row_id = chk_row_id.substring(8, chk_row_id.length);
		var row_id = '#row_' + chk_row_id;
		if( jQuery(this).is(':checked') ) {
			jQuery(row_id).children('td:not(.small)').css('background-color','#E2E2E2');			
		} else {
			jQuery(row_id).children('td:not(.small)').css('background-color','#fff');			
		}	
		if ( jQuery( '.chk_row:checked' ).length ){
			jQuery( '#btn_service_delete').removeAttr('disabled');	
		} else {
			jQuery( '#btn_service_delete').attr('disabled','disabled');		
		}	 
		if ( jQuery( '.chk_row:checked' ).length == 1){
			jQuery( '#btn_service_edit').removeAttr('disabled');	
		} else {
			jQuery( '#btn_service_edit').attr('disabled','disabled');		
		}	 
	});
 }
// delete services
function delete_service() {
	if ( jQuery( '.chk_row:checked' ).length ) {  
		jQuery( '#dialog-confirm-delete' ).dialog({
		    resizable: false,
	        height:240,
	        modal: true,
	        buttons:[
				        {
				            text: wbkl10n.delete  ,
				            click: function() {
				            			jQuery( '.ui-dialog-buttonset' ).html( '<div class="loading"></div>' );
							 			var idSelector = function() { return this.id; };
										var arrIds = jQuery( '.chk_row:checked' ).map(idSelector).get();
						 			 	var data = {
											'action': 'wbk_service_delete',
						 					'ids': arrIds 
						 				};
						 				jQuery.post(ajaxurl, data, function(response) {
						 					if ( response == 1 ){
							 					jQuery.each( arrIds, function( key, value ) {
													jQuery( '#' + value ).closest('tr').remove();;
												});	
												jQuery( '#dialog-confirm-delete'  ).dialog( 'close' );
						 					}
						 				});
				            		}
				        },
				        {
				            text: wbkl10n.cancel,
				            click: function() {
				            	jQuery( this ).dialog( 'close' );
				            }
				        }
				    ]    
	    });
	}
}
// add service
function add_service() {
	// prepare fields
	jQuery( '#wbk-service-name' ).val('');
	jQuery( '#wbk-service-desc' ).val('');
	jQuery( '#wbk-service-email' ).val('');
	jQuery( '#wbk-service-duration' ).val('30');
	jQuery( '#wbk-service-interval' ).val('0');
	jQuery( '#wbk-service-quantity' ).val('1');
	jQuery( '#wbk-service-priority' ).val('0');
	jQuery( '#wbk-service-quantity' ).prop('disabled', false);
 	jQuery( '#wbk-service-step' ).val( '30' );
 	jQuery( '#wbk-service-price' ).val( '0.00' );
	jQuery( '#wbk-user-list > option:selected' ).removeAttr('selected');
	jQuery( '#wbk-payment-methods > option:selected' ).removeAttr('selected');
	jQuery( '#error-name' ).html('');	
	jQuery('#wbk-payment-methods').trigger('chosen:updated');
	jQuery('#wbk-user-list'). trigger('chosen:updated');
	jQuery( '#wbk-service-notification_template' ).val( '0' );
	jQuery( '#wbk-service-reminder_template' ).val( '0' );
	jQuery( '#wbk-service-invoice_template' ).val( '0' );
	jQuery( '#wbk-service-prepare-time' ).val( '0' );
	jQuery( '#wbk-service-date-range' ).val( '' );
	jQuery( 'input' ).removeClass('wbk-input-error');
	jQuery( '.ui-dialog-buttonset' ).css( 'display', 'block' ); 
	jQuery( '#dialog-add-service' ).dialog({
	    resizable: false,       
        width: 1000,
        modal: true,
        title: wbkl10n.addservice,
        open: function( event, ui ) {},
        buttons: [
        	{
        		text: wbkl10n.addservice,
        		click: function() {
 						var error_status = 0;	
			 			var name = jQuery( '#wbk-service-name' ).val();
			 			if ( !wbkCheckString( name, 1, 128 ) ){
			 				error_status = 1;
			 				jQuery( '#wbk-service-name' ).addClass('wbk-input-error');	 				
			 			}	
			 			var desc = jQuery( '#wbk-service-desc' ).val();
			 			if ( !wbkCheckString( desc, 0, 1024 ) ){
			 				error_status = 1;
			 				jQuery( '#wbk-service-desc' ).addClass('wbk-input-error');	 				
			 			}	
			 			var email = jQuery( '#wbk-service-email' ).val();
						if ( !wbkCheckEmail( email) ){
			 				error_status = 1;
			 				jQuery( '#wbk-service-email' ).addClass('wbk-input-error');	 				
			 			}	
						var duration = jQuery.trim( jQuery( '#wbk-service-duration' ).val() );
						if ( !wbkCheckInteger( duration ) ){
							error_status = 1;
			 				jQuery( '#wbk-service-duration' ).addClass('wbk-input-error');	 				
						}
						if ( !wbkCheckIntegerMinMax( duration, 1, 1440 ) ){
							error_status = 1;
			 				jQuery( '#wbk-service-duration' ).addClass('wbk-input-error');	 				
						}
						var step = jQuery.trim( jQuery( '#wbk-service-step' ).val() );
						if ( !wbkCheckInteger( step ) ){
							error_status = 1;
			 				jQuery( '#wbk-service-step' ).addClass('wbk-input-error');	 				
						}
		  				var total_duration = parseInt( jQuery( '#wbk-service-duration' ).val() ) +  parseInt( jQuery( '#wbk-service-interval' ).val() );
						if ( !wbkCheckIntegerMinMax( step, 1, 720 ) ){ 
							error_status = 1;
			 				jQuery( '#wbk-service-step' ).addClass('wbk-input-error');	 				
						}
						var interval = jQuery.trim( jQuery( '#wbk-service-interval' ).val() );
						if ( !wbkCheckInteger( interval ) ){
						 	error_status = 1;
			 			 	jQuery( '#wbk-service-interval' ).addClass('wbk-input-error');	 				
						}		  
						if ( !wbkCheckIntegerMinMax( interval, 0, 1440) ){
						 	error_status = 1;
			 			 	jQuery( '#wbk-service-interval' ).addClass('wbk-input-error');	 				
						}
						var quantity = jQuery.trim( jQuery( '#wbk-service-quantity' ).val() );
						if ( !wbkCheckIntegerMinMax( quantity, 1, 1000000) ){
						 	error_status = 1;
			 			 	jQuery( '#wbk-service-quantity' ).addClass('wbk-input-error');	 				
						}
						var priority = jQuery.trim( jQuery( '#wbk-service-priority' ).val() );
						if( wbkCheckInteger( priority ) ){
							if ( !wbkCheckIntegerMinMax( priority, 0, 1000000) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-priority' ).addClass('wbk-input-error');	 				
							}  
						} else {
								error_status = 1;
				 			 	jQuery( '#wbk-service-priority' ).addClass('wbk-input-error');	 	
						}
						var price = jQuery.trim( jQuery( '#wbk-service-price' ).val() );
						if ( !wbkCheckPrice( price ) ){
						 	error_status = 1;
			 			 	jQuery( '#wbk-service-price' ).addClass('wbk-input-error');	 				
						}
						var prepare_time = jQuery.trim( jQuery( '#wbk-service-prepare-time' ).val() );
						if ( !wbkCheckIntegerMinMax( prepare_time, 0, 360 ) ){
						 	error_status = 1;
			 			 	jQuery( '#wbk-service-prepare-time' ).addClass('wbk-input-error');	 				
						}
						var date_range =  jQuery.trim( jQuery( '#wbk-service-date-range' ).val() );
						if ( !wbkCheckString( date_range, 0, 128 ) ){
			 				error_status = 1;
			 				jQuery( '#wbk-service-date-range' ).addClass('wbk-input-error');	 				
			 			}	
		 				var multi_limit =  jQuery.trim( jQuery( '#wbk-service-multiple-limit' ).val() );		 				 
						if( multi_limit != '' ){							 
							if ( !wbkCheckInteger( multi_limit ) ){							 
				 				error_status = 1;
				 				jQuery( '#wbk-service-multiple-limit' ).addClass('wbk-input-error');	 				
				 			}	
				 		}
				 		var multi_low_limit =  jQuery.trim( jQuery( '#wbk-service-multiple-low-limit' ).val() );		 				 
						if( multi_low_limit != '' ){				 
							if ( !wbkCheckInteger( multi_low_limit ) ){							 
				 				error_status = 1;
				 				jQuery( '#wbk-service-multiple-low-limit' ).addClass('wbk-input-error');	 				
				 			}	
				 		}				 		 
			 			if ( error_status == 1 ){
			 				return;
			 			}
			 			var users = jQuery( '#wbk-user-list' ).val(); 
			 			var gg_calendars = jQuery( '#wbk-service-gg_calendar' ).val(); 
		 			
			 			var form = jQuery( '#wbk-form-list' ).val();
			 			var reminder_template = jQuery( '#wbk-service-reminder_template' ).val();
			 			var invoice_template = jQuery( '#wbk-service-invoice_template' ).val();	 			
			 			var notification_template = jQuery( '#wbk-service-notification_template' ).val();
			 			var form = jQuery( '#wbk-form-list' ).val();
			 			var payment_methods = jQuery( '#wbk-payment-methods' ).val();
						jQuery( '.ui-dialog-buttonpane' ).append( '<div class="loading"></div>' );
		 			 	jQuery( '.ui-dialog-buttonset' ).css( 'display', 'none' );
					    var business_hours = jQuery( '.wbk-business-hours' ).map(function() {
					    	return jQuery( this ).val();
					    }).get();
		 			 	var data = {   
							'action': 'wbk_service_add',
							'name': name,
							'desc': desc,
							'email': email,
							'duration': duration,
							'interval': interval,
							'step': step,
							'users': users,
							'business_hours': business_hours,
							'form': form,
							'quantity': quantity,
							'priority': priority,
							'price': price,
							'payment_methods': payment_methods,
							'reminder_template': reminder_template,
							'invoice_template': invoice_template,
							'notification_template': notification_template,
							'prepare_time': prepare_time,
							'date_range': date_range,
							'gg_calendars': gg_calendars,
							'multi_limit': multi_limit,
							'multi_low_limit': multi_low_limit

		 				};
		 				jQuery.post(ajaxurl, data, function(response) {
		 					if ( response != -1 && response != -2  && response != -3 && response != -4 && response != -5 && response != -6 && response != -7 ){
			 					jQuery( '.service_table tr:last').after( response );
								jQuery( '.loading' ).remove();
								setRowCheckBoxEvent();
								jQuery( '.ui-dialog-buttonset' ).css( 'display', 'block' );
								jQuery( '#dialog-add-service'  ).dialog( 'close' );
							} else {
								if ( response == -2 ){
									jQuery( '#error-name' ).html(' this name is used');
									jQuery( '#wbk-service-name' ).addClass('wbk-input-error');
								}	
								jQuery( '.loading' ).remove();
								jQuery( '.ui-dialog-buttonset' ).css( 'display', 'block' );
							}		
		 				});
        		}
        	},
        	{
        		text: wbkl10n.cancel,
        		click: function() {
					jQuery( this ).dialog( 'close' );
        		}
        	}
	    ]
    });
}
// edit service
function edit_service() {
	if ( jQuery( '.chk_row:checked' ).length != 1 ) {
		return;
	}
	var service_id = jQuery( '.chk_row:checked' ).attr( 'id' );
	jQuery( 'input' ).removeClass('wbk-input-error');	
	jQuery( '#service_dialog_right' ).css( 'display', 'none' );
	jQuery( '#service_dialog_left' ).css( 'display', 'none' );
	jQuery( '#service_dialog_left2' ).css( 'display', 'none' );
	jQuery( '#error-name' ).html('');
	jQuery( '#dialog-add-service' ).dialog({
	    resizable: false,        
        width: 1000,
        title: wbkl10n.editservice,
        modal: true,
        open: function( event, ui ) {
        	jQuery( '.ui-dialog-buttonset' ).css( 'display', 'none' );
			jQuery( '#dialog-add-service' ).append( '<div class="loading"></div>');
			var data = {
						'action': 'wbk_service_load',
	 					'id': service_id 
	 				};
	 		jQuery.post(ajaxurl, data, function(response) {	
		 			var matches = response.match(/\{(.*?)\}/);
					if ( matches ) {
					    var response = '{' + matches[1] + '}';
					    jQuery( '.loading' ).remove();
			        	jQuery( '.ui-dialog-buttonset' ).css( 'display', 'block' ); 	
						jQuery( '#service_dialog_right' ).css( 'display', 'block' );
						jQuery( '#service_dialog_left' ).css( 'display', 'block' );
						jQuery( '#service_dialog_left2' ).css( 'display', 'block' );
						var objdata = jQuery.parseJSON(response);
						jQuery( '#wbk-service-name' ).val( objdata.name );
						jQuery( '#wbk-service-prev-name' ).val( objdata.name );						
						jQuery( '#wbk-service-desc' ).val( objdata.desc );
						jQuery( '#wbk-service-email' ).val( objdata.email );
						jQuery( '#wbk-service-duration' ).val( objdata.duration );
						jQuery( '#wbk-service-interval' ).val( objdata.interval );
						jQuery( '#wbk-service-step' ).val( objdata.step );
						jQuery( '#wbk-service-quantity' ).val( objdata.quantity );
						jQuery( '#wbk-service-priority' ).val( objdata.priority );
						jQuery( '#wbk-service-prepare-time' ).val( objdata.prepare_time );
						jQuery( '#wbk-service-date-range' ).val( objdata.date_range );
						jQuery( '#wbk-service-multiple-limit' ).val( objdata.multi_limit );
						jQuery( '#wbk-service-multiple-low-limit' ).val( objdata.multi_low_limit );				 
						var quantity = parseInt( objdata.quantity );
						if ( quantity == 1 ){
							jQuery( '#wbk-service-step' ).prop('disabled', false );
						} else {
							jQuery( '#wbk-service-step' ).prop('disabled', false );							
						}
						jQuery( '#wbk-service-price' ).val( objdata.price );
						var exists = false;
						jQuery('#wbk-form-list option').each(function(){
						    if (this.value == objdata.form ) {
						        exists = true;
						    }
						});
						if ( exists ){
							jQuery( '#wbk-form-list' ).val( objdata.form );
						} else {
							jQuery( '#wbk-form-list' ).val(0);							
						}						
						var exists = false;
						jQuery('#wbk-service-notification_template option').each(function(){
						    if (this.value == objdata.notification_template ) {
						        exists = true;
						    }
						});
						if ( exists ){
							jQuery( '#wbk-service-notification_template' ).val( objdata.notification_template );
						} else {
							jQuery( '#wbk-service-notification_template' ).val(0);							
						}	

						var exists = false;
						jQuery('#wbk-service-reminder_template option').each(function(){
						    if (this.value == objdata.reminder_template ) {
						        exists = true;
						    }
						});
						if ( exists ){
							jQuery( '#wbk-service-reminder_template' ).val( objdata.reminder_template );
						} else {
							jQuery( '#wbk-service-reminder_template' ).val(0);							
						}							
						var exists = false;
						jQuery('#wbk-service-invoice_template option').each(function(){
						    if (this.value == objdata.invoice_template ) {
						        exists = true;
						    }
						});
						if ( exists ){
							jQuery( '#wbk-service-invoice_template' ).val( objdata.invoice_template );
						} else {
							jQuery( '#wbk-service-invoice_template' ).val(0);							
						}			
						jQuery( '#wbk-user-list > option:selected' ).removeAttr('selected');					
						if ( objdata.users != null ) {
							var arr_users = objdata.users.split(';');
							arr_users.forEach( function( item, i, arr ) {
								jQuery( '#wbk-user-list > option[value="' + item + '"]' ).attr( 'selected', 'selected' );
							});
 					    }


						jQuery( '#wbk-service-gg_calendar > option:selected' ).removeAttr('selected');							 			
						if ( objdata.gg_calendars != null ) {							 
							var arr_gg_calendars = objdata.gg_calendars.split(';');							 
							arr_gg_calendars.forEach( function( item, i, arr ) {								 
								jQuery( '#wbk-service-gg_calendar > option[value="' + item + '"]' ).attr( 'selected', 'selected' );
							});
 					    }
 					   

						jQuery( '#wbk-payment-methods > option:selected' ).removeAttr('selected');
						if ( objdata.payment_methods != null ) {
							var payment_methods = objdata.payment_methods.split(';');
							payment_methods.forEach( function( item, i, arr ) {
								jQuery( '#wbk-payment-methods > option[value="' + item + '"]' ).attr( 'selected', 'selected' );
							});
 					    }
						jQuery('#wbk-payment-methods').trigger('chosen:updated');
						jQuery('#wbk-user-list'). trigger('chosen:updated');
						jQuery('#wbk-service-gg_calendar').trigger('chosen:updated');

 					    jQuery( '#service_dialog_right' ).html( objdata.bh );
 					    setIntervalOnChange();
 					    jQuery( '.ui-dialog' ).position( { my: 'center', at: 'center', of: window } )
	 				} else {
						jQuery( '.loading' ).remove();
						jQuery( '#service_dialog_right' ).css( 'display', 'block' );
						jQuery( '#service_dialog_left' ).css( 'display', 'block' );
						jQuery( '#service_dialog_left2' ).css( 'display', 'block' );
						jQuery( '#service_dialog_left' ).html('error');
						jQuery( '#service_dialog_right' ).html('');
						;
	 				}
	 			});	
        },
        buttons: [
        			{
        				text: wbkl10n.save,
        				click: function() {
			 				var error_status = 0;		
				 			var name = jQuery( '#wbk-service-name' ).val();
				 			var prevname = jQuery( '#wbk-service-prev-name' ).val();
				 			if ( !wbkCheckString( name, 1, 128 ) ){
				 				error_status = 1;
				 				jQuery( '#wbk-service-name' ).addClass('wbk-input-error');	 				
				 			}	
				 			var desc = jQuery( '#wbk-service-desc' ).val();
				 			if ( !wbkCheckString( desc, 0, 1024 ) ){
				 				error_status = 1;
				 				jQuery( '#wbk-service-desc' ).addClass('wbk-input-error');	 				
				 			}	
				 			var email = jQuery( '#wbk-service-email' ).val();
							if ( !wbkCheckEmail( email) ){
				 				error_status = 1;
				 				jQuery( '#wbk-service-email' ).addClass('wbk-input-error');	 				
				 			}	
							var duration = jQuery.trim( jQuery( '#wbk-service-duration' ).val() );
							if ( !wbkCheckInteger( duration ) ){
								error_status = 1;
				 				jQuery( '#wbk-service-duration' ).addClass('wbk-input-error');	 				
							}
							if ( !wbkCheckIntegerMinMax( duration, 1, 1440 ) ){
								error_status = 1;
				 				jQuery( '#wbk-service-duration' ).addClass('wbk-input-error');	 				
							}
							var interval = jQuery.trim( jQuery( '#wbk-service-interval' ).val() );
							if ( !wbkCheckInteger( interval ) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-interval' ).addClass('wbk-input-error');	 				
							}			 
						  	if ( !wbkCheckIntegerMinMax( interval, 0, 1440 ) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-interval' ).addClass('wbk-input-error');	 				
							}
							var step = jQuery.trim( jQuery( '#wbk-service-step' ).val() );
							if ( !wbkCheckInteger( step ) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-step' ).addClass('wbk-input-error');	 				
							}		
							var total_duration = parseInt( jQuery( '#wbk-service-duration' ).val() ) +  parseInt( jQuery( '#wbk-service-interval' ).val() );
							if ( !wbkCheckIntegerMinMax( step, 1, 720 ) ){
							 	error_status = 1;
				 				jQuery( '#wbk-service-step' ).addClass('wbk-input-error');	 				
							}
							var quantity = jQuery.trim( jQuery( '#wbk-service-quantity' ).val() );
							if ( !wbkCheckIntegerMinMax( quantity, 1, 1000000 ) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-quantity' ).addClass('wbk-input-error');	 				
							}
							var priority = jQuery.trim( jQuery( '#wbk-service-priority' ).val() );
							if( wbkCheckInteger( priority ) ){
								if ( !wbkCheckIntegerMinMax( priority, 0, 1000000) ){
								 	error_status = 1;
					 			 	jQuery( '#wbk-service-priority' ).addClass('wbk-input-error');	 				
								}  
							} else {
								error_status = 1;
				 			 	jQuery( '#wbk-service-priority' ).addClass('wbk-input-error');	 	
							}
							var price = jQuery.trim( jQuery( '#wbk-service-price' ).val() );
							if ( !wbkCheckPrice( price ) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-price' ).addClass('wbk-input-error');	 				
							}
 							var prepare_time = jQuery.trim( jQuery( '#wbk-service-prepare-time' ).val() );
 							if ( !wbkCheckInteger( prepare_time ) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-prepare-time' ).addClass('wbk-input-error');	 				
							} 
							if ( !wbkCheckIntegerMinMax( prepare_time, 0, 360 ) ){
							 	error_status = 1;
				 			 	jQuery( '#wbk-service-prepare-time' ).addClass('wbk-input-error');	 				
							}
							var date_range =  jQuery.trim( jQuery( '#wbk-service-date-range' ).val() );
							if ( !wbkCheckString( date_range, 0, 128 ) ){
				 				error_status = 1;
				 				jQuery( '#wbk-service-date-range' ).addClass('wbk-input-error');	 				
				 			}	
				 			var multi_limit =  jQuery.trim( jQuery( '#wbk-service-multiple-limit' ).val() );

							if( multi_limit != '' ){
								if ( !wbkCheckInteger( multi_limit ) ){
					 				error_status = 1;
					 				jQuery( '#wbk-service-multiple-limit' ).addClass('wbk-input-error');	 				
					 			}	
					 		}
					 		var multi_low_limit =  jQuery.trim( jQuery( '#wbk-service-multiple-low-limit' ).val() );
							if( multi_low_limit != '' ){
								if ( !wbkCheckInteger( multi_low_limit ) ){
					 				error_status = 1;
					 				jQuery( '#wbk-service-multiple-low-limit' ).addClass('wbk-input-error');	 				
					 			}	
					 		}
 				 			if ( error_status == 1 ){
				 				return;
				 			}
				 			var users = jQuery( '#wbk-user-list' ).val(); 
							var form = jQuery( '#wbk-form-list' ).val(); 
							var gg_calendars = jQuery( '#wbk-service-gg_calendar' ).val(); 
				 			var payment_methods = jQuery( '#wbk-payment-methods' ).val(); 
							var reminder_template = jQuery( '#wbk-service-reminder_template' ).val();
							var invoice_template = jQuery( '#wbk-service-invoice_template' ).val();

			 				var notification_template = jQuery( '#wbk-service-notification_template' ).val();
							jQuery( '.ui-dialog-buttonpane' ).append( '<div class="loading"></div>' );
			 			 	jQuery( '.ui-dialog-buttonset' ).css( 'display', 'none' );
						    var business_hours = jQuery( '.wbk-business-hours' ).map(function() {
						    	return jQuery( this ).val();
						    }).get();
			 			 	var data = {   
								'action': 'wbk_service_edit',
								'name': name,
								'prevname': prevname,
								'desc': desc,
								'email': email,
								'duration': duration,
								'interval': interval,
								'step': step,
								'users': users,
								'form': form,
								'business_hours': business_hours,
								'id': service_id,
								'quantity': quantity,
								'priority': priority,							
								'price': price,
								'payment_methods': payment_methods,
								'reminder_template': reminder_template,
								'invoice_template': invoice_template,					
								'notification_template': notification_template,
								'prepare_time': prepare_time,
								'date_range': date_range,
								'gg_calendars': gg_calendars,
								'multi_limit': multi_limit,
								'multi_low_limit': multi_low_limit

			 				};
			 				jQuery.post(ajaxurl, data, function(response) { 			 					 
			 		 			if ( response != -1 && 
			 						 response != -2 &&
			 						 response != -3 &&
			 						 response != -4 && 
			 						 response != -5 && 
			 						 response != -6 && 
			 						 response != -7 && 
			 						 response != -8 && 
			 						 response != -9 && 
			 						 response != -10 && 
			 						 response != -11 && 
			 						 response != -12 ) {
			 						var objdata = jQuery.parseJSON( response );
									var id = objdata.id;
									jQuery( '#value_name_' + id ).html( objdata.name );
									jQuery( '#value_description_' + id ).html( objdata.desc );
									jQuery( '#value_email_' + id ).html( objdata.email );
			 						jQuery( '#value_duration_' + id ).html( objdata.duration );
			 						jQuery( '#value_interval_' + id ).html( objdata.interval );
			 						jQuery( '#value_users_' + id ).html( objdata.users );
									jQuery( '#value_business_hours_' + id ).html( objdata.bh );									
									jQuery( '#value_step_' + id ).html( objdata.step );	 
									jQuery( '#value_quantity_' + id ).html( objdata.quantity );	 
									jQuery( '#value_price_' + id ).html( objdata.price );	
				 					jQuery( '.loading' ).remove();					 
									jQuery( '.ui-dialog-buttonset' ).css( 'display', 'block' );
									jQuery( '#dialog-add-service'  ).dialog( 'close' );
								} else {
									if ( response == -2 ){
										jQuery( '#error-name' ).html(' this name is used');
										jQuery( '#wbk-service-name' ).addClass('wbk-input-error');
									}	
									jQuery( '.loading' ).remove();
									jQuery( '.ui-dialog-buttonset' ).css( 'display', 'block' );
								}		
			 				});        						
        				}
        			},
        			{
        				text: wbkl10n.cancel,
        				click: function(){
							jQuery( '.loading' ).remove();
	        				jQuery( this ).dialog( 'close' );
        				}
        			}
        		 ]
        });
}
function setDurationIntervalOnChange() {
	jQuery( '#wbk-service-duration, #wbk-service-interval' ).change(function() {
		var duration = parseInt( jQuery( '#wbk-service-duration' ).val() );
		var interval = parseInt( jQuery( '#wbk-service-interval' ).val() );	
		jQuery( '#wbk-service-step' ).val( duration + interval );
	});
}
function setQuantityOnChange() {
	jQuery( '#wbk-service-quantity' ).focusout(function() {
		var quantity = jQuery( '#wbk-service-quantity' ).val();
		var error_status = 0;
		if ( !wbkCheckIntegerMinMax(quantity,1,1000000 ) || !wbkCheckInteger( quantity ) ){
			jQuery( '#wbk-service-quantity' ).addClass('wbk-input-error');
			jQuery( '#wbk-service-quantity' ).focus();
			error_status =1;
		} else {
			jQuery( '#wbk-service-quantity' ).removeClass('wbk-input-error');			
		}
		if ( error_status == 0 ){
		}
 	});
}
// set interval onchange function
function setIntervalOnChange() {
 	jQuery('[id^=int_]').on('focus', function () {
        previous = jQuery(this).val();
    }).change(function() {
		var sel_id = jQuery(this).attr('id'); 
   		var arr = sel_id.split('_');
   		current = jQuery(this).val();
   		// interval 1 start
	   	if ( arr[1] == 1 && arr[2] == 1 ){
	   		var nextval = jQuery('#int_1_2_'+arr[3]).val();	
 	   		if (current >= nextval){
	   		//	intervalShowError();
	   		//	jQuery(this).val(previous);
	   		} 
	   	}
	   	// interval 1 end
		if ( arr[1] == 1 && arr[2] == 2 ){
	   		var nextval = jQuery('#int_2_1_'+arr[3]).val();
	   		var prevval = jQuery('#int_1_1_'+arr[3]).val();	
 	   		if (current >= nextval || current <= prevval){
	   		//	intervalShowError();
	   		//	jQuery(this).val(previous);
	   		} 
	   	}
	   	// interval 2 start
		if ( arr[1] == 2 && arr[2] == 1 ){
	   		var nextval = jQuery('#int_2_2_'+arr[3]).val();
	   		var prevval = jQuery('#int_1_2_'+arr[3]).val();	
 	   		if (current >= nextval || current <= prevval){
	   		//	intervalShowError();
	   		//	jQuery(this).val(previous);
	   		} 
	   	}
   		// interval 2 end
	   	if ( arr[1] == 2 && arr[2] == 2 ){
	   		var prevval = jQuery('#int_2_1_'+arr[3]).val();	
 	   		if (current <= prevval){
	   		//	intervalShowError();
	   		//	jQuery(this).val(previous);
	   		} 
	   	}
        previous = this.value;
    });
	jQuery('[id^=chk_day_]').change( function() {
    	var day = jQuery(this).attr('id');  
		day = day.substring(8, day.length);
		if( jQuery(this).is(':checked') ) {
			jQuery('#chk_day_val_'+day).val('1');
        } else {
 			jQuery('#chk_day_val_'+day).val('0');
        }         
    });
}
// show interval error 2 dialog
function intervalShowError() {
	jQuery( '#dialog-interval-error-2' ).dialog({
	    resizable: false,
	    height:140,
	    modal: true,
	    buttons: {            
	    Ok: function() {
		   	jQuery( this ).dialog( 'close' );
		}
		}
	});
}
// remove 2nd time interval from day
function removeInterval( day ) { 
	jQuery('#business_hours_' + day +'_2 ').empty();
	jQuery('#business_hours_' + day +'_control').html('<a href="javascript:addInterval( \'' + day + '\' )">' + wbkl10n.addgap + '</a>');
}
// add 2nd time interval to day
function addInterval( day ) {
	var val =  parseInt(jQuery('#int_1_2_' + day + ' option:selected').val());
	if (val > 81000){ 
		jQuery( '#dialog-interval-error' ).dialog({
		    resizable: false,
	        height:140,
	        modal: true,
	        buttons: {            
		        Ok: function() {
		        	jQuery( this ).dialog( 'close' );
		        }
		    }
	    });
		return;
	}
	val = val + 1800;
	var val2 = val + 3600;
	var html = jQuery('#business_hours_' + day +'_1').html();
	jQuery('#business_hours_' + day +'_2').html(html);
	jQuery('#business_hours_' + day + '_2  >  select' ).first().attr('id','int_2_1_' + day); 
	jQuery('#business_hours_' + day + '_2  >  select' ).last().attr('id','int_2_2_' + day); 
	jQuery('#business_hours_' + day +'_control').html('<a href="javascript:removeInterval( \'' + day + '\' )">' + wbkl10n.removegap + '</a>');
	var val =  parseInt(jQuery('#int_1_2_' + day + ' option:selected').val()) + 1800;
	var val2 =  parseInt(jQuery('#int_1_2_' + day + ' option:selected').val()) + 3600;
	jQuery('#int_2_1_' + day).val(val);
	jQuery('#int_2_2_' + day).val(val2);
	jQuery('#int_2_1_' + day + ' [value=0]').remove();
	jQuery('#int_2_1_' + day + ' [value=1800]').remove();
	jQuery('#int_2_2_' + day + ' [value=0]').remove();
	jQuery('#int_2_2_' + day + ' [value=1800]').remove();
	jQuery('#int_2_2_' + day + ' [value=3600]').remove();
	setIntervalOnChange();  
}
     