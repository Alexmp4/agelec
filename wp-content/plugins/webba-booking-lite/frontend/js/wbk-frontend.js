// WEBBA Booking frontend scripts
// step count
var wbk_total_steps; 
// onload function
jQuery( function ($) {
	jQuery(document).off('ajaxSend');

	if( jQuery('.wbk-payment-init').length > 0 ){
 		wbk_set_payment_events();
	}
 	if( jQuery( '#wbk-cancel_booked_appointment' ).length > 0 ){
 		wbk_cancel_booked_appointment_events();
	}
 	if( jQuery( '.wbk-addgg-link' ).length > 0 ){
 		wbk_add_gg_appointment_events();
	}
	if( jQuery('#wbk-confirm-services').length > 0 ){		 
		jQuery( '.wbk-service-checkbox' ).change( function(){			 
			var service_count = jQuery( '.wbk-service-checkbox:checked').length;
		 	if( service_count == 0 ){
		 		jQuery( '#wbk-confirm-services' ).attr( "disabled", true );
		 	} else {
		 		jQuery( '#wbk-confirm-services' ).attr( "disabled", false );
		 	}
		});
		jQuery( '#wbk-confirm-services' ).click( function(){			
			wbk_renderSetDate( false );
		});
	} 
	if( jQuery('.wbk_multiserv_hidden_services').length > 0 ){
		wbk_renderSetDate( false );
	}
	if( jQuery('#wbk-service-id').length == 0 ){
	    return;
    }
	var service_id = jQuery('#wbk-service-id').val();
	if ( wbkl10n.mode == 'extended' ){
	// extended mode
		if ( service_id == 0 ) {
			wbk_total_steps = 4;
	 		wbk_setServiceEvent();
	 	} else {
	 		wbk_total_steps = 3;
	 		wbk_renderSetDate( false );
		}
	} else {
	// basic mode
		if ( service_id == 0 ) {
			wbk_total_steps = 3;
	 		wbk_setServiceEvent();
	 	} else {
	 		wbk_total_steps = 2;
	 		wbk_renderSetDate( false );
		}
		jQuery('#timeselect_row').remove();
	} 
	if ( service_id != 0 ) {
		var multi_limit = jQuery('#wbk-service-id').attr('data-multi-limit');
		wbkl10n.multi_low_limit = jQuery('#wbk-service-id').attr('data-multi-low-limit');
		if( multi_limit != '' ){
			wbkl10n.multi_limit = multi_limit;
		} 
	}
	jQuery('#wbk-category-id').change(function() {
		jQuery( '#wbk_current_category' ).val =jQuery(this).val();
		wbk_clearSetDate();
		wbk_clearSetTime();
		wbk_clearForm();
		wbk_clearDone();
		wbk_clearTimeslots();
		wbk_clearForm();

		if( jQuery(this).val() == 0 ){			 
			jQuery('#wbk_service_list_holder').fadeOut('fast');
			return;
		}
		var services_opt = jQuery(this).find('option:selected').attr('data-services').split('-');

		jQuery('#wbk-service-id > option').each( function() {
			if( jQuery(this).attr('value') != 0 ){
				jQuery(this).remove();
			}			 			 
		});
		jQuery('#wbk_service_id_full_list > option').each( function() {
			if( jQuery(this).attr('value') != 0 ){
				if( jQuery.inArray( jQuery(this).attr('value'), services_opt ) != -1 ){					 					
					var elem_outerHtml = jQuery(this)[0].outerHTML;
					jQuery('#wbk-service-id').append( elem_outerHtml );
				}
			} 
		});
		jQuery('#wbk-service-id').val(0);
		jQuery('#wbk_service_list_holder').fadeIn('fast');	 
	});

});
function wbk_is_int(n){
	return n % 1 === 0;
}
// since 3.0.8
function wbk_cancel_booking(){
	jQuery('#wbk-slots-container, #wbk-time-container, #wbk-booking-form-container').fadeOut( 'fast', function(){
		jQuery('#wbk-time-container').html('');
		jQuery('#wbk-booking-form-container').html('');
		jQuery('#wbk-slots-container').html('');
		if( jQuery('#wbk-date').attr('type') == 'text' ){
			jQuery('#wbk-date').val(wbkl10n.selectdate);		
		} else {
			jQuery('#wbk-date').val(0);		
		}		
		jQuery('html, body').animate({
        	scrollTop: jQuery('#wbk-date-container').offset().top - 120
   		}, 1000);
	});
	jQuery('#wbk-to-checkout').fadeOut('fast'); 
}
// clear set date
function wbk_clearSetDate() {
	jQuery('#wbk-date-container').html('');
}
// clear timeslots
function wbk_clearTimeslots() {
	jQuery('#wbk-slots-container').html('');
}
// clear form
function wbk_clearForm() {
	jQuery('#wbk-booking-form-container').html('');
}
// clear results
function wbk_clearDone() {
	jQuery('#wbk-booking-done').html('');
	jQuery('#wbk-payment').html('');
}
// set service event
function wbk_setServiceEvent() {
	jQuery('#wbk-service-id').change(function() {
		wbk_clearSetDate();
		wbk_clearSetTime();
		wbk_clearForm();
		wbk_clearDone();
		wbk_clearTimeslots();
		wbk_clearForm();
		var service_id = jQuery('#wbk-service-id').val();
		if ( service_id != 0 ){
			wbk_renderSetDate( true );
			var service_desc = jQuery('#wbk-service-id').find('[value="' + service_id + '"]').attr('data-desc');			 
			if( wbkl10n.show_desc == 'enabled' ){
				jQuery( '#wbk_description_holder' ).html( '<label class="wbk-input-label">' + service_desc + '</label>' );
			}
			var multi_limit = jQuery('#wbk-service-id').find(':selected').attr('data-multi-limit');
			if( multi_limit == '' ){
 				wbkl10n.multi_limit = wbkl10n.multi_limit_default;
			} else {
 				wbkl10n.multi_limit = multi_limit;
			}  
			wbkl10n.multi_low_limit = jQuery('#wbk-service-id').find(':selected').attr('data-multi-low-limit'); 		 
		} else {
			wbk_clearSetDate();
			wbk_clearSetTime();
		}
	});
}
// clear set time
function wbk_clearSetTime() {
	jQuery('#wbk-time-container').html('');
	jQuery('#wbk-to-checkout').fadeOut( function(){
		jQuery('#wbk-to-checkout').remove();
	});
}
// render time set
function wbk_renderTimeSet() {
	var service = jQuery('#wbk-service-id').val();
	var data = {
		'action' : 'wbk-render-days',
		'step' : wbk_total_steps,
 		'service' : service
 	};
	jQuery('#wbk-time-container').html('<div class="wbk-loading"></div>');
 	jQuery.post( wbkl10n.ajaxurl, data, function(response) {
 		jQuery('#wbk-time-container').attr('style', 'display:none !important');
 		if ( response == -1 ){
			jQuery('#wbk-time-container').html('error');
 		} else {
			jQuery('#wbk-time-container').html(response);
 		}
		jQuery('#wbk-time-container').fadeIn('slow');
		jQuery('#wbk-search_time_btn').focus();
		if( jQuery('#wbk-time-container').length > 0 ){
			jQuery('html, body').animate({
	        	scrollTop: jQuery('#wbk-time-container').offset().top - 120
	   		}, 1000);
		}
   		jQuery( '[id^=wbk-day]' ).change(function() {
			var day = jQuery(this).attr('id');
			day = day.substring(8, day.length);
			if( jQuery(this).is(':checked') ) {
				jQuery('#wbk-time_'+day).attr("disabled", false);
 	        } else {
				jQuery('#wbk-time_'+day).attr("disabled", true);
 	        }
		});
   		jQuery('#wbk-search_time_btn').click(function() {
			 wbk_searchTime();
		});
	});
}
// render date input
function wbk_renderSetDate( scroll ) {
	if( jQuery('#wbk-confirm-services').length > 0 ){
		var selected_service_id = [];
		jQuery( '.wbk-service-checkbox:checked').each( function(){
			selected_service_id.push( jQuery( this ).val() );
		});
		if( selected_service_id.length == 0 ){
			return;
		}
	} else {
		var selected_service_id = jQuery('#wbk-service-id').val();
	}


	var data = {
		'action' : 'wbk_prepare_service_data',
		'service' : selected_service_id
 	};
  
	jQuery('#wbk-date-container').html('<div class="wbk-loading"></div>');
    jQuery.post( wbkl10n.ajaxurl, data, function(response) {   
    	response_obj = jQuery.parseJSON( response );    	 
		jQuery('#wbk-date-container').css( 'display', 'none');
		var sep_html = '<hr class="wbk-separator"/>';
		if ( jQuery('#wbk-service-id').attr('type') == 'hidden' ){
			sep_html = '';
		}		
		if( wbkl10n.date_input == 'popup' ){	
			if ( response_obj.limits != '' ){
				 
				if( wbk_is_int( response_obj.limits  ) ){
					jQuery('#wbk-date-container').html('<input value="' +  response_obj.limits  + '" type="hidden" name="wbk-date_submit" class="wbk-input" id="wbk-date" />');
					wbk_clearForm();
	         		wbk_clearDone();
	         		wbk_clearTimeslots();
	         		wbk_clearSetTime();
	    			if( jQuery('#wbk-date').val() != 0 ){
		         		if ( wbkl10n.mode == 'extended' ){
		         			wbk_renderTimeSet();
		    			} else {
		  					wbk_searchTime();
		   				}   			 
	    			}     		
					return;
				}
			}

			if ( wbkl10n.mode == 'extended' ){
				jQuery('#wbk-date-container').html( sep_html + '<div class="wbk-col-12-12"><label class="wbk-input-label">' + wbkl10n.selectdatestart + '</label><input value="' + wbkl10n.selectdate + '" type="text" class="wbk-input" id="wbk-date" /></div>');
			} else {
				jQuery('#wbk-date-container').html(  sep_html + '<div class="wbk-col-12-12"><label class="wbk-input-label">' + wbkl10n.selectdatestartbasic + '</label><input value="' + wbkl10n.selectdate + '" type="text" class="wbk-input" id="wbk-date" /></div>');
		 	}
		} else {
			if ( wbkl10n.mode == 'extended' ){
				jQuery('#wbk-date-container').html( sep_html + '<div class="wbk-col-12-12"><label class="wbk-input-label">' + wbkl10n.selectdatestart + '</label><select name="wbk-date_submit" class="wbk-input" id="wbk-date" /></select></div>');
			} else {
				jQuery('#wbk-date-container').html(  sep_html + '<div class="wbk-col-12-12"><label class="wbk-input-label">' + wbkl10n.selectdatestartbasic + '</label><select name="wbk-date_submit" class="wbk-input" id="wbk-date" /></select></div>');
		 	}

		}
		jQuery('#wbk-date-container').fadeIn('slow');

		if( wbkl10n.date_input == 'popup' ){	
		 	var disability_result = [];
		 	var range_min = undefined;
		 	var range_max = undefined;
		    if ( response_obj.disabilities  != '' ){
				var day_disabilities_all = response_obj.disabilities.split(';');
			 	var index;
				for (index = 0; index < day_disabilities_all.length; index++ ) {   	 
			    	var disablity_current_day = day_disabilities_all[index].split(',');
					disability_result.push(disablity_current_day);
				} 
		    }
		    if ( response_obj.week_disabilities != '' ){
		    	var day_disabilities_all = response_obj.week_disabilities.split(';');
			 	var index;
				for (index = 0; index < day_disabilities_all.length; index++ ) {   	 
			    	 
					disability_result.push( parseInt( day_disabilities_all[index] ) );
				}  
		    }		    		    
		    if ( response_obj.limits != '' ){
	 			
		    	var date_range =  response_obj.limits.split('-');
		    	range_min = date_range[0].split(',');
		    	range_min[0] = parseInt(range_min[0]);
		    	range_min[1] = parseInt(range_min[1]) - 1;
		    	range_min[2] = parseInt(range_min[2]);   	

		    	range_max = date_range[1].split(',');
		   		range_max[0] = parseInt(range_max[0]);
		    	range_max[1] = parseInt(range_max[1]) - 1;
		    	range_max[2] = parseInt(range_max[2]);

		    	var range_start = new Date( range_min[0], range_min[1], range_min[2] );
			   	var range_end = new Date( range_max[0], range_max[1], range_max[2] );
				var now = new Date();

				if( range_start < now ){		 
					range_min[0] = now.getFullYear();
		    		range_min[1] = now.getMonth();
		    		range_min[2] = now.getDate();
				}
			}    
			var date_input = jQuery('#wbk-date').pickadate({
					min: true,
				    monthsFull: [ wbkl10n.january,
								  wbkl10n.february,
		  						  wbkl10n.march,
								  wbkl10n.april,
								  wbkl10n.may,
								  wbkl10n.june,
								  wbkl10n.july,
								  wbkl10n.august,
								  wbkl10n.september,
								  wbkl10n.october,
								  wbkl10n.november,
								  wbkl10n.december
		 	    				 ],
				    monthsShort: [ wbkl10n.jan,
								   wbkl10n.feb,
		  						   wbkl10n.mar,
								   wbkl10n.apr,
								   wbkl10n.mays,
								   wbkl10n.jun,
								   wbkl10n.jul,
								   wbkl10n.aug,
								   wbkl10n.sep,
								   wbkl10n.oct,
								   wbkl10n.nov,
								   wbkl10n.dec
				    			 ],
				    weekdaysFull: [ wbkl10n.sunday,
								    wbkl10n.monday,
		  						    wbkl10n.tuesday,
								    wbkl10n.wednesday,
								    wbkl10n.thursday,
								    wbkl10n.friday,
								    wbkl10n.saturday
		  		     			  ],
				    weekdaysShort: [ wbkl10n.sun,
								     wbkl10n.mon,
		  						     wbkl10n.tue,
								     wbkl10n.wed,
								     wbkl10n.thu,
								     wbkl10n.fri,
								     wbkl10n.sat
		  		     			  ],
				    today:  wbkl10n.today,
				    clear:  wbkl10n.clear,
				    close:  wbkl10n.close,
				    firstDay:  wbkl10n.startofweek,
				    format: 'dd mmmm yyyy',
				    disable: disability_result,
				    labelMonthNext: wbkl10n.nextmonth,
				    labelMonthPrev: wbkl10n.prevmonth,
					formatSubmit: 'yyyy/mm/dd',
					hiddenPrefix: 'wbk-date',			 
					onOpen: function() {
						if( range_min != undefined ){
						    this.set('highlight', range_min );
						}
					},
					onClose: function(){
					    jQuery(document.activeElement).blur();
					},		 
		         	onSet: function( thingSet ) {
		         		wbk_clearForm();
		         		wbk_clearDone();
		         		wbk_clearTimeslots();
		         		if(typeof thingSet.select != 'undefined'){
		         			if( jQuery('#wbk-confirm-services').length > 0 ){	
		         				wbk_searchTime();
		         			} else {
			         			if ( wbkl10n.mode == 'extended' ){		         				
			         				wbk_clearSetTime();
				   					wbk_renderTimeSet();
			    				} else {		    					
			    					wbk_clearSetTime();
			    					wbk_searchTime();
			    				}
			    			}
		    			}
		  			}
			});
			if( range_min != undefined ){
				var picker = date_input.pickadate('picker');
				picker.set( 'min', range_min );
				picker.set( 'max', range_max );
			}
		} else {
			 if ( response_obj.abilities != '' ){
			 	var options_html = '<option value="0">' + wbkl10n.selectdate + '</option>';
				var day_abilities = response_obj.abilities.split(';');			 
				jQuery.each( day_abilities, function( key, value ) {
					var formated_pair =  value.split( '-HM-');
					options_html += '<option value="' + formated_pair[0] + '" >' + formated_pair[1] + '</option>';
					
				});
				jQuery('#wbk-date').html( options_html );
				jQuery('#wbk-date').unbind('change');
				jQuery('#wbk-date').change(function() { 
					wbk_clearForm();
	         		wbk_clearDone();
	         		wbk_clearTimeslots();
	         		wbk_clearSetTime();
	    			if( jQuery('#wbk-date').val() != 0 ){
		         		if ( wbkl10n.mode == 'extended' ){
		         			wbk_renderTimeSet();
		    			} else {
		  					wbk_searchTime();
		   				}   			 
	    			}     		
				});
			}
		}
		if ( scroll == true ) {
			jQuery('html, body').animate({
		       	scrollTop: jQuery('#wbk-date-container').offset().top - 120
		   	}, 1000);
		}
	});
}

// search time
function wbk_searchTime() {
	wbk_clearForm();
	wbk_clearDone();

	if( jQuery('#wbk-confirm-services').length > 0 ){
		days = '';
		times = '';

		var service = [];
		jQuery( '.wbk-service-checkbox:checked').each( function(){
			service.push( jQuery( this ).val() );
		});

	} else {		
		if ( wbkl10n.mode == 'extended' ) {
		    var days = jQuery( '.wbk-checkbox:checked' ).map(function() {
		    	return jQuery( this ).val();
		    }).get();
		    var times = jQuery( '.wbk-time_after:enabled' ).map(function() {
		    	return jQuery( this ).val();
		    }).get();
		    if ( days == '' ) {
		    	return;
		    }
		} else {
			days = '';
			times = '';
		}
		var service = jQuery('#wbk-service-id').val();
	}
    
   
    var date = jQuery('[name=wbk-date_submit]').val();
    if ( date == '' ){
    	jQuery('#wbk-date').addClass('wbk-input-error');
    	return;
    }
    var offset = new Date().getTimezoneOffset();
    var data = {
		'action' : 'wbk_search_time',
		'days': days,
		'times': times,
		'service': service,
		'date': date,
 		'offset': offset
 	};
  
	jQuery('#wbk-slots-container').html('<div class="wbk-loading"></div>');
    jQuery.post( wbkl10n.ajaxurl, data, function(response) {   
     
    	if ( response == 0 || response == -1 || response == -2 || response == -3 || response == -4 || response == -4 || response == -6 || response == -7){
     		jQuery('#wbk-slots-container').html('error');
     	} else {
     		response_obj = jQuery.parseJSON( response );
     		if( response_obj.dest == 'form' ){
     			jQuery('#wbk-slots-container').html('');
     			jQuery('#wbk-booking-form-container').html(response_obj.data); 
     			jQuery('#wbk-book_appointment').click(function() {
			   		wbk_book_processing( response_obj.time, service );
				});
     			return;
     		}
     		if( response_obj.dest == 'slot' ){
     			response = response_obj.data;
     		}  
    		jQuery('#wbk-slots-container').attr('style', 'display:none !important');
			jQuery('#wbk-slots-container').html( response );
			jQuery('#wbk-slots-container').fadeIn('slow');
    		jQuery('html, body').animate({ scrollTop: jQuery('#wbk-slots-container').offset().top - 120 }, 1000);
			wbk_setTimeslotEvent();
			if ( wbkl10n.mode == 'extended' ){
				jQuery('#wbk-show_more_btn').click(function() {
					jQuery('.wbk-cancel-button').fadeOut( function(){
						jQuery(this).remove();
					});					
					wbk_showMore();
				});
			} else {
				jQuery('#wbk-service-id').focus();
			}
			jQuery('.wbk-cancel-button').click(function() {
				wbk_cancel_booking();
			});
    	}
    });
}
// search time show more callback
function wbk_showMore() {
	jQuery('.wbk-cancel-button').fadeOut( function(){
		jQuery( '.wbk-cancel-button' ).remove();
	});   
    if( jQuery('#wbk-confirm-services').length > 0 ){
		days = '';
		times = '';
		var service = [];
		jQuery( '.wbk-service-checkbox:checked').each( function(){
			service.push( jQuery( this ).val() );
		});

	} else {		 
	    var days = jQuery( '.wbk-checkbox:checked' ).map(function() {
	    	return jQuery( this ).val();
	    }).get();
	    var times = jQuery( '.wbk-time_after:enabled' ).map(function() {
	    	return jQuery( this ).val();
	    }).get();
	    if ( days == '' ) {
	    	return;
	    }
		var service = jQuery('#wbk-service-id').val();
	}
    var date = jQuery('#wbk-show-more-start').val();
    var offset = new Date().getTimezoneOffset();
    var data = {
		'action' : 'wbk_search_time',
		'days': days,
		'times': times,
		'service': service,
		'date': date,
		'offset': offset
 	};
	jQuery('#wbk-show_more_container').html('<div class="wbk-loading"></div>');
    jQuery.post( wbkl10n.ajaxurl, data, function(response) {   		 
    	if (response == 0 || response == -1){
			jQuery('#wbk-more-container').html('error');
    	} else {
    		response_obj = jQuery.parseJSON( response );
     		if( response_obj.dest == 'slot' ){
     			response = response_obj.data;
     		}
      		jQuery('#wbk-show_more_container').remove();
      		jQuery('html, body').animate({ scrollTop: jQuery('.wbk-more-container').last().offset().top - 120 }, 1000);
			jQuery('.wbk-more-container').last().attr('style', 'display:none !important');
			jQuery('.wbk-more-container').last().html(response);
			jQuery('.wbk-more-container').eq(-2).fadeIn('slow');
			wbk_setTimeslotEvent();
			jQuery('.wbk-cancel-button').click(function() {
				wbk_cancel_booking();
			});
			jQuery('#wbk-show_more_btn').click(function() {
				wbk_showMore();
			});
   	}
   });
}
// set timeslot button event
function wbk_setTimeslotEvent(){	 
	wbk_clearDone();
	jQuery('[id^=wbk-timeslot-btn_]').unbind('click');
	jQuery('[id^=wbk-timeslot-btn_]').click(function() { 
		// multi booking mode start
		if( wbkl10n.multi_booking == 'enabled' || wbkl10n.multi_booking == 'enabled_slot' ){
 			jQuery('#wbk-booking-form-container').html('');
 			jQuery(this).toggleClass('wbk-slot-active-button');		
			var selected_cnt = jQuery('.wbk-slot-active-button').not('#wbk-to-checkout').length;	

			if ( wbkl10n.multi_limit != '' && parseInt( wbkl10n.multi_limit ) < parseInt( selected_cnt ) ){
				jQuery(this).toggleClass('wbk-slot-active-button');
				return;
			}
			if( selected_cnt > 0){
				if(  wbkl10n.multi_booking == 'enabled_slot' ){
					jQuery('#wbk-to-checkout').remove();
				}
		 
					if(  wbkl10n.multi_booking  == 'enabled' ){
						var zindex = parseInt( wbk_find_highest_zindex('div') ) + 1;
						if( jQuery( '#wbk-to-checkout' ).length == 0 ){
							jQuery( 'body' ).prepend( '<div  id="wbk-to-checkout" style="display:none;" class="wbk-slot-active-button" >' + wbkl10n.checkout + '</div>' );
						}		 	 
						jQuery('.wbk_multi_selected_count').html(selected_cnt);
 						jQuery('.wbk_multi_total_count').html(wbkl10n.multi_limit);

						jQuery('#wbk-to-checkout').css('z-index', zindex);					
					} 				
					if(  wbkl10n.multi_booking  == 'enabled_slot' ){
						jQuery( this ).parent().append( '<div  id="wbk-to-checkout" style="display:none;" class="wbk-slot-active-button" >' + wbkl10n.checkout + '</div>' );
						
						jQuery('.wbk_multi_selected_count').html(selected_cnt);
						jQuery('.wbk_multi_total_count').html(wbkl10n.multi_limit);



						jQuery( '#wbk-to-checkout' ).css( 'position', 'relative' );
						jQuery( '#wbk-to-checkout' ).css( 'margin-top', '5px' );
						var fontsize = jQuery( '.wbk-slot-time' ).css('font-size');
						jQuery( '#wbk-to-checkout' ).css( 'font-size', fontsize );

					}
				 
				jQuery('#wbk-to-checkout').fadeIn('slow');
				jQuery('#wbk-to-checkout').unbind('click');			
				if ( wbkl10n.multi_low_limit != '' ){
			    	if( parseInt( wbkl10n.multi_low_limit ) > selected_cnt ){
			    		jQuery( '#wbk-to-checkout' ).css( 'cursor', 'default' );
			    		jQuery( '#wbk-to-checkout' ).addClass( 'wbk_not_active_checkout' );
			    	} else {
			    		jQuery( '#wbk-to-checkout' ).css( 'cursor', 'pointer' );
			    		jQuery( '#wbk-to-checkout' ).removeClass( 'wbk_not_active_checkout' );
			    	}
			    }  
				jQuery('#wbk-to-checkout').click(function() {
					
					if ( wbkl10n.multi_low_limit != '' ){
				    	if( parseInt( wbkl10n.multi_low_limit ) > selected_cnt ){
				    		return
				    	} 
				    }  
					var times = [];
					var services = [];
					jQuery('.wbk-slot-active-button').not('#wbk-to-checkout').each(function() {
						var btn_id = jQuery(this).attr('id');
						var time = btn_id.substring(17, btn_id.length);
						times.push(time);
						services.push( jQuery(this).attr( 'data-service' ) );
					});
					var service = jQuery('#wbk-service-id').val();
					var time_offset = new Date().getTimezoneOffset();
					var data = {
						'action' : 'wbk_render_booking_form',
						'time': times,
						'service': service,
						'step' : wbk_total_steps,
						'services' : services,
                        'time_offset' : time_offset,                        

				 	};				 
					jQuery.each( wbk_get_converted, function( key, value ) {
						if ( key != 'action' && key != 'time' && key != 'service' && key != 'step' ){
						 	data[key] = value;
						}
			 		}); 
			 		 
					// begin render booking form for multiple slots **********************************************************************************************
					jQuery('.wbk-cancel-button').fadeOut( function(){
						jQuery(this).remove();
										});
					wbk_clearDone();
					jQuery('#wbk-booking-form-container').html('<div class="wbk-loading"></div>');
					jQuery('html, body').animate({ scrollTop: jQuery('#wbk-booking-form-container').last().offset().top - 120 }, 1000);

					// request form rendering and binding events   **********************************************************************************************				
					jQuery.post( wbkl10n.ajaxurl, data, function(response) { 
						jQuery('#wbk-booking-form-container').attr('style', 'display: none !important;');
				    	if (response == 0 || response == -1){
							jQuery('#wbk-booking-form-container').html('error');
				    	} else {
							jQuery('#wbk-to-checkout').fadeOut('fast'); 
							jQuery('#wbk-booking-form-container').html(response);
							if( typeof wbk_init_conditional_fields === 'function' ) {											 
								wbk_init_conditional_fields();  
							}
							jQuery('.wbk-cancel-button').click(function() {
								wbk_cancel_booking();
							}); 
				    		if ( wbkl10n.phonemask == 'enabled' ||  wbkl10n.phonemask == 'enabled_mask_plugin' ){
				    			jQuery('[name="wbk-phone"]').mask(wbkl10n.phoneformat);
				    		}
							jQuery('.wbk-checkbox-label').not('.wbk-dayofweek-label').each(function() {
								jQuery(this).replaceWith( '<label class="wbk-checkbox-label">' + jQuery(this).html() + '</label>' );
							});
							jQuery('.wbk-checkbox-label').not('.wbk-dayofweek-label').click(function() {
			 					if ( !jQuery(this).siblings('.wbk-checkbox').prop('checked') ){
			 						jQuery(this).siblings('.wbk-checkbox').prop('checked',true);
			 				 		var current_box = jQuery(this).siblings('.wbk-checkbox');

			 				 		var elem_cf_holder =  jQuery(this).closest( '.wbk-checkbox-custom');
			 				 		if( elem_cf_holder.hasClass('wpcf7-exclusive-checkbox') == true){
			 				 			 elem_cf_holder.find('.wbk-checkbox').not( current_box ).prop('checked',false);
			 				 		}

			 					} else {
			 					 	jQuery(this).siblings('.wbk-checkbox').not('.wbk-service-checkbox').prop('checked',false);
				 				}
							});
							jQuery('#wbk-booking-form-container').fadeIn('slow');
				    		jQuery( 'input, textarea' ).focus(function() {
								jQuery( this ).removeClass('wbk-input-error');
							});
				    		jQuery( '.wbk-select' ).change( function(){
				    			jQuery( this ).removeClass('wbk-input-error');
				    		});

				    		// assign book click
				    		jQuery('#wbk-book_appointment').click(function() {
								var al = jQuery('[name="wbk-acceptance"]').length;
				 				if ( al == 1 ){
				 					if( !jQuery ('[name="wbk-acceptance"]').attr('checked')) {
				 						jQuery('.wbk-acceptance-error').css('display','inline');
				 						jQuery('.wbk-acceptance-error').css('color','red');
				 						return;
				 					} else {
				 						jQuery('.wbk-acceptance-error').css('display','none');
				 					}
				  				}
								var name = jQuery.trim( jQuery( '[name="wbk-name"]' ).val() );
								var email = jQuery.trim( jQuery( '[name="wbk-email"]' ).val() );
								var phone = jQuery.trim( jQuery( '[name="wbk-phone"]' ).val() );
								var desc =  jQuery.trim( jQuery( '[name="wbk-comment"]' ).val() );
								var quantity_length = jQuery( '[name="wbk-book-quantity"]' ).length;
								var quantity = -1;
								if ( quantity_length == 0 ){
									quantity = 1;
								} else {
									quantity =  jQuery.trim( jQuery( '[name="wbk-book-quantity"]' ).val() );
								}
								var error_status = 0;
								if ( !wbkCheckString( name, 1, 128 ) ){
					 				error_status = 1;
				 	 				jQuery( '[name="wbk-name"]' ).addClass('wbk-input-error');
				 	 			 
							 
					 			}
					 			if ( !wbkCheckEmail( email ) ){
					 				error_status = 1;
					 				jQuery( '[name="wbk-email"]' ).addClass('wbk-input-error');

								 
					 			}
					 			if ( !wbkCheckString( phone, wbkl10n.phone_required, 30 ) ){
					 				error_status = 1;
					 				jQuery( '[name="wbk-phone"]' ).addClass('wbk-input-error');

								 
					 			}
					 			if ( !wbkCheckString( desc, 0, 1024 ) ){
					 				error_status = 1;
					 				jQuery( '[name="wbk-comment"]' ).addClass('wbk-input-error');

								 
					 			}
					 			if ( !wbkCheckIntegerMinMax( quantity, 1, 1000000 ) ){

							 
					 				error_status = 1;
					 			}
					 			var current_category = jQuery( '#wbk_current_category' ).val();
								if ( !wbkCheckIntegerMinMax( current_category, 1, 1000000 ) ){
									current_category = 0;
								} 

					 			// validate custom fields (text)
								jQuery('.wbk-text[aria-required="true"]').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
									    var value =  jQuery( this ).val();
										if ( !wbkCheckString( value, 1, 128 ) ){
						 					error_status = 1;
						 					jQuery( this ).addClass('wbk-input-error');
						 				}										 
									}
								});
					 			// validate custom fields (select)
								jQuery('.wbk-select[aria-required="true"]').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
									    var value =  jQuery( this ).val();
									    var first_value  = jQuery(this).find('option:eq(0)').html();
									    if ( value == first_value ){
									    	error_status = 1;
						 					jQuery( this ).addClass('wbk-input-error');
									    }
									}
								});
					 			// validate custom fields (emails)
								jQuery('.wbk-email-custom[aria-required="true"]').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
									    var value =  jQuery( this ).val();
										if ( !wbkCheckEmail( value, 1, 128 ) ){
						 					error_status = 1;
						 					jQuery( this ).addClass('wbk-input-error');
						 				}
						 			}
								});
								// validate custom fields (textareas)
								jQuery('.wbk-textarea[aria-required="true"]').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
									    var value =  jQuery( this ).val();
										if ( !wbkCheckString( value, 1, 1024 ) ){
						 					error_status = 1;
						 					jQuery( this ).addClass('wbk-input-error');
						 				}
						 			}
								});
								// validate custom fields file inputs
								jQuery('.wbk-file[aria-required="true"]').each(function() {   
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){  
										if ( jQuery(this).prop('files').length == 0 ){
											error_status = 1;
											jQuery( this ).addClass('wbk-input-error');
										}
									}
								});
								// validate checkbox
								jQuery('.wbk-checkbox-custom.wpcf7-validates-as-required').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){  
										var validbox = false;
										jQuery(this).find('.wbk-checkbox-custom').each( function(){
											if ( jQuery(this).is(':checked') ){
												validbox = true;
											}
										});
										if( !validbox ){
											jQuery(this).find('.wbk-checkbox-label').addClass( 'wbk-input-error' );
											error_status = 1;
										}
									}
								});
								// end validate custom fields
								var extra_value = [];							 
								// custom fields values (text)			 
								jQuery('.wbk-text, .wbk-email-custom').not('#wbk-name,#wbk-email,#wbk-phone').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
										var extra_item = [];										 
										extra_item.push( jQuery( this ).attr('id') );
										extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
										extra_item.push( jQuery( this ).val() );
										extra_value.push( extra_item );									
									}
								});
								// custom fields values (checkbox)
								jQuery('.wbk-checkbox-custom.wpcf7-checkbox').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
										var extra_item = [];	
										extra_item.push( jQuery( this ).attr('id') );
										extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
										var current_checkbox_value = '';
										jQuery(this).children('span').each(function() {
											jQuery(this).children('input:checked').each(function() {
												current_checkbox_value += jQuery(this).val() + ' ';												 
											});
										});

										extra_item.push( current_checkbox_value );
										extra_value.push( extra_item );	
									}
								});	 
								jQuery('.wbk-select').not('#wbk-book-quantity, #wbk-service-id').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
										var extra_item = [];	
										extra_item.push( jQuery( this ).attr('id') );
										extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
										extra_item.push( jQuery( this ).val() );
										extra_value.push( extra_item );	
									}
								});
								// custom fields text areas
								jQuery('.wbk-textarea').not('#wbk-comment,#wbk-customer_desc').each(function() {
									if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
										var extra_item = [];	
										extra_item.push( jQuery( this ).attr('id') );
										extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
										extra_item.push( jQuery( this ).val() );
										extra_value.push( extra_item );	
									}
								});
								// secondary names, emails
								var secondary_data = [];
								jQuery('[id^="wbk-secondary-name"]').each(function() {
									var name_p = jQuery(this).val();
									var name_id = jQuery(this).attr('id');
									if ( wbkCheckString( name, 1, 128 ) ){
										var arr = name_id.split( '_' );
										var id2 = 'wbk-secondary-email_' + arr[1];
										email_p = jQuery('#' + id2).val();
										var person = new Object();
										person.name = name_p;
										person.email = email_p;
										secondary_data.push(person);
									}
								});
					 			if ( error_status == 1 ) {
					 				jQuery('html, body').animate({ scrollTop: jQuery('#wbk-booking-form-container').last().offset().top - 120 }, 1000);
					 				return;
					 			}
					 			jQuery('#wbk-booking-done').html( '<div class="wbk-loading"></div>');
								jQuery('#wbk-booking-form-container').fadeOut('slow', function() {
				    				jQuery('#wbk-booking-form-container').html('');
				    				jQuery('#wbk-booking-form-container').fadeIn();
									jQuery('html, body').animate({
								        							scrollTop: jQuery('#wbk-booking-done').offset().top - 120
								   								}, 1000);
				  				});
								var time_offset = new Date().getTimezoneOffset();
								var form_data = new FormData();				 			 						 		 
								form_data.append( 'action' , 'wbk_book');
								form_data.append( 'time', times);
								form_data.append( 'service', service);
				 				form_data.append( 'name', name);
				 				form_data.append( 'email', email);
				 				form_data.append( 'phone', phone);
				 				form_data.append( 'desc', desc);
				 				form_data.append( 'extra', JSON.stringify( extra_value ) );
				 				form_data.append( 'quantity', quantity);
				 				form_data.append( 'secondary_data', secondary_data);	
			 					form_data.append( 'current_category', current_category );
			 					form_data.append( 'time_offset', time_offset );				 					 
			 					form_data.append( 'services', services );	
			 					var per_serv_quantity = [];
			 					if( jQuery('.wbk-book-quantity').length > 0 ){			 						 
			 						jQuery('.wbk-book-quantity').each(function() {
			 							per_serv_quantity.push( jQuery(this).attr( 'data-service' ) + ';' + jQuery(this).val() );
			 						});
			 					} else{
			 						var per_serv_quantity = '';
			 					}
			 					form_data.append( 'per_serv_quantity', per_serv_quantity );	

								var iteration = 0;	
								if( wbkl10n.allow_attachment == 'yes' ){
									jQuery('.wbk-file').each( function () { 
										iteration++;
										var fileindex = 'file' + iteration;
										form_data.append( fileindex, jQuery(this).prop('files')[0] );
									});
								}
				                jQuery.ajax({
					    		  	url: wbkl10n.ajaxurl,
						          	type: 'POST',
						          	data: form_data,
						          	cache: false,
						            processData: false,
								    contentType: false,
						        	success: function( response ) { 
						        		 
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
					 						response != -12 &&
											response != -13
					 						) {



												jQuery('#wbk-to-checkout').fadeOut('fast');
												response_obj = jQuery.parseJSON( response );
												jQuery('#wbk-booking-done').html( '<div class="wbk-details-sub-title wbk-mb-20">' + response_obj.thanks_message + '</div>' );
						                        if ( wbkl10n.hide_form == 'enabled' ){                   	
							                        jQuery('#wbk-slots-container, #wbk-time-container, #wbk-date-container, #wbk-service-container' ).fadeOut( 'fast', function(){
							                        	jQuery('#wbk-slots-container, #wbk-time-container, #wbk-date-container, #wbk-service-container').html('');
							                        	jQuery('html, body').animate({
						        							scrollTop: jQuery('#wbk-booking-done').offset().top - 120
						   								}, 1000);
							                        });
						                    	} else {					                    		
						                    		jQuery('.wbk-slot-active-button').not('#wbk-to-checkout').each(function () { 
						                    			timeslots_after_book( jQuery( this ), quantity, response_obj.booked_slot_text );
							                    	});
						                    	}
												if( typeof wbk_on_booking === 'function' ) {											 
												   wbk_on_booking( service, time, name, email, phone, desc, quantity );  
												}
												wbk_set_payment_events();								
										} else {
											jQuery('html, body').animate({
					        							scrollTop: jQuery('#wbk-booking-done').offset().top
					   								}, 1000);
											jQuery('#wbk-booking-done').html(wbkl10n.something_wrong);
										}
										jQuery('#wbk-slots-container').show('slide');						        	
						        	}
				        	 	});		 	    			
			  	 	    			

						        	
								 

							});
						};
					}); 



				});	 
			} else { 		 
				jQuery('#wbk-to-checkout').fadeOut('slow');
			}
 			return;
		}
		// multi booking mode end
		// get time from id
		jQuery('.wbk-slot-button').removeClass('wbk-slot-active-button');
		jQuery(this).addClass('wbk-slot-active-button');
		jQuery('.wbk-cancel-button').fadeOut( function(){
			jQuery(this).remove();
		});
		wbk_clearDone();
		var btn_id = jQuery(this).attr('id');
		var time = btn_id.substring(17, btn_id.length);
		var service = jQuery('#wbk-service-id').val();
		var availale_count = jQuery(this).attr('data-available');
		var max_available = 0;                   
		var time_offset = new Date().getTimezoneOffset();

	    var data = {
			'action' : 'wbk_render_booking_form',
			'time': time,
			'service': service,
			'step' : wbk_total_steps,
			'time_offset' : time_offset			 
	 	};
		jQuery.each( wbk_get_converted, function( key, value ) {
			 if ( key != 'action' && key != 'time' && key != 'service' && key != 'step' ){
			 	data[key] = value;
			 }
 		});
		jQuery('#wbk-booking-form-container').html('<div class="wbk-loading"></div>');
		jQuery('html, body').animate({ scrollTop: jQuery('#wbk-booking-form-container').last().offset().top - 120 }, 1000);
	 		 	jQuery.post( wbkl10n.ajaxurl, data, function(response) {

			jQuery('#wbk-booking-form-container').attr('style', 'display:none !important');
	    	if (response == 0 || response == -1){
				jQuery('#wbk-booking-form-container').html('error');
	    	} else {
			jQuery('#wbk-booking-form-container').html(response);
			if( typeof wbk_init_conditional_fields === 'function' ) {											 
				wbk_init_conditional_fields();  
			}
			jQuery('.wbk-cancel-button').click(function() {
				wbk_cancel_booking();
			}); 
    		if ( wbkl10n.phonemask == 'enabled' ||  wbkl10n.phonemask == 'enabled_mask_plugin' ){
    			jQuery('[name="wbk-phone"]').mask(wbkl10n.phoneformat);
    		}
			jQuery('.wbk-checkbox-label').not('.wbk-dayofweek-label').each(function() {
				jQuery(this).replaceWith( '<label class="wbk-checkbox-label">' + jQuery(this).html() + '</label>' );
			});

			jQuery('.wbk-checkbox-label').not('.wbk-dayofweek-label').click(function() {
 				if ( !jQuery(this).siblings('.wbk-checkbox').prop('checked') ){
 				 	jQuery(this).siblings('.wbk-checkbox').prop('checked',true);
 				} else {
 				 	jQuery(this).siblings('.wbk-checkbox').prop('checked',false);
 				}
			});
			jQuery('#wbk-booking-form-container').fadeIn('slow');
    		jQuery( 'input, textarea' ).focus(function() {
				jQuery( this ).removeClass('wbk-input-error');
			});
			jQuery( '.wbk-select' ).change( function(){
    			jQuery( this ).removeClass('wbk-input-error');
    		});
			jQuery('#wbk-book_appointment').click(function() {
			     wbk_book_processing( time, service );
			});

 
    	}
    });

	});
}
function wbk_cancel_booked_appointment_events(){
	jQuery('#wbk-cancel_booked_appointment').click(function() {
		var app_token = jQuery(this).attr('data-appointment');
		var email = jQuery.trim( jQuery( '#wbk-customer_email' ).val() );
		jQuery( '#wbk-customer_email' ).val(email); 
		if ( !wbkCheckEmail( email ) ){
			jQuery( '#wbk-customer_email' ).addClass('wbk-input-error');
	 	} else {
		    var data = {
				'action' : 'wbk_cancel_appointment',
				'app_token':  app_token,
				'email': email
		 	};
		 	jQuery('#wbk-cancel-result').html('<div class="wbk-loading"></div>');
			jQuery('#wbk-cancel_booked_appointment')
			jQuery('#wbk-cancel_booked_appointment').prop('disabled', true);
		 	jQuery.post( wbkl10n.ajaxurl, data, function(response) {
		 		response = jQuery.parseJSON( response );
		 		jQuery('#wbk-cancel-result').html( response.message );
			  	if( response.status == 0 ){
			  		jQuery('#wbk-cancel_booked_appointment').prop('disabled', false );
			  	}
			  	 
		 	});

	 	}
	});
}
function wbk_add_gg_appointment_events(){
	jQuery('.wbk-addgg-link').click(function() {
	    window.location.href = jQuery(this).attr('data-link');             
	});
}
function wbk_set_payment_events(){
	jQuery('.wbk-payment-init').click(function() {
	    jQuery('#wbk-payment').html('<div class="wbk-loading"></div>');
		jQuery('html, body').animate({ scrollTop: jQuery('#wbk-payment').last().offset().top - 120 }, 1000);
		var method = jQuery(this).attr('data-method');
		var coupon = '';
		if( jQuery('#wbk-coupon').length > 0 ){
			coupon = jQuery.trim( jQuery('#wbk-coupon').val() );
		}		
	    var data = {
			'action' : 'wbk_prepare_payment',
			'app_id': jQuery(this).attr('data-app-id'),
			'method': method,
			'coupon': coupon
	 	};
	 	jQuery.post( wbkl10n.ajaxurl, data, function(response) {
            jQuery('#wbk-payment').fadeOut('fast', function(){
				 
					jQuery('#wbk-payment').html(response);
					jQuery('#wbk-payment').fadeIn('slow');		
							 				 
            });		
	 	});
	});
}
function wbk_find_highest_zindex(elem){
	var elems = document.getElementsByTagName(elem);
	var highest = 0;
	for (var i = 0; i < elems.length; i++)
	{
	var zindex=document.defaultView.getComputedStyle(elems[i],null).getPropertyValue("z-index");
	if ((zindex > highest) && (zindex != 'auto')){
	  highest = zindex;
	}
	}
	return highest;
}
function timeslots_after_book( element, quantity, slot_text ){ 
	var avail_container_cnt = element.siblings('.wbk-slot-available').length;
	if ( avail_container_cnt == 1 ){
		// decrease available count	 
		var current_avail = parseInt( element.siblings('.wbk-slot-available').find('.wbk-abailable-container').html() );
		current_avail = current_avail - quantity;
		if( current_avail == 0 ){                   				
			if( wbkl10n.show_booked == 'disabled' ){
				element.parent().parent().fadeOut( 'fast', function(){
					element.parent().parent().remove();
				});
			} else {
				element.siblings('.wbk-slot-available').find('.wbk-abailable-container').html(current_avail);
				element.replaceWith('<input value="' + slot_text +'" class="wbk-slot-button wbk-slot-booked" type="button">');
			}
		} else {
			element.siblings('.wbk-slot-available').find('.wbk-abailable-container').html(current_avail);
		}
	} else {
		if( wbkl10n.show_booked == 'disabled' ){
			element.parent().fadeOut( 'fast', function(){
				element.parent().remove();
			});
		} else {
			element.replaceWith('<input value="' + slot_text +'" class="wbk-slot-button wbk-slot-booked" type="button">');
		}
	}
}
function wbk_book_processing( time, service ){
	var al = jQuery('[name="wbk-acceptance"]').length;
	if ( al == 1 ){
		if( !jQuery ('[name="wbk-acceptance"]').attr('checked')) {
			jQuery('.wbk-acceptance-error').css('display','inline');
			jQuery('.wbk-acceptance-error').css('color','red');
			return;
		} else {
			jQuery('.wbk-acceptance-error').css('display','none');
		}
	}
	var name = jQuery.trim( jQuery( '[name="wbk-name"]' ).val() );
	var email = jQuery.trim( jQuery( '[name="wbk-email"]' ).val() );
	var phone = jQuery.trim( jQuery( '[name="wbk-phone"]' ).val() );
	var desc =  jQuery.trim( jQuery( '[name="wbk-comment"]' ).val() );
	var quantity_length = jQuery( '[name="wbk-book-quantity"]' ).length;
	var quantity = -1;
	if ( quantity_length == 0 ){
		quantity = 1;
	} else {
		quantity =  jQuery.trim( jQuery( '[name="wbk-book-quantity"]' ).val() );
	}
	var error_status = 0;
	if ( !wbkCheckString( name, 1, 128 ) ){
			error_status = 1;
				jQuery( '[name="wbk-name"]' ).addClass('wbk-input-error');
	}
	if ( !wbkCheckEmail( email ) ){
		error_status = 1;
		jQuery( '[name="wbk-email"]' ).addClass('wbk-input-error');
	}
	if ( !wbkCheckString( phone, wbkl10n.phone_required, 30 ) ){
		error_status = 1;
		jQuery( '[name="wbk-phone"]' ).addClass('wbk-input-error');
	}
	if ( !wbkCheckString( desc, 0, 1024 ) ){
		error_status = 1;
		jQuery( '[name="wbk-comment"]' ).addClass('wbk-input-error');
	}
	if ( !wbkCheckIntegerMinMax( quantity, 1, 1000000 ) ){
		error_status = 1;
	}
	var current_category = jQuery( '#wbk_current_category' ).val();
	if ( !wbkCheckIntegerMinMax( current_category, 1, 1000000 ) ){
		current_category = 0;
	}	 
	// validate custom fields (text)
	jQuery('.wbk-text[aria-required="true"]').each(function() {
	    var value =  jQuery( this ).val();
		if ( !wbkCheckString( value, 1, 128 ) ){
				error_status = 1;
				jQuery( this ).addClass('wbk-input-error');
			}
	});
	// validate custom fields (select)
	jQuery('.wbk-select[aria-required="true"]').each(function() {
	    var value =  jQuery( this ).val();
	    var first_value  = jQuery(this).find('option:eq(0)').html();
	    if ( value == first_value ){
	    	error_status = 1;
				jQuery( this ).addClass('wbk-input-error');
	    }
	});
	// validate custom fields (emails)
	jQuery('.wbk-email-custom[aria-required="true"]').each(function() {
	    var value =  jQuery( this ).val();
		if ( !wbkCheckEmail( value, 1, 128 ) ){
				error_status = 1;
				jQuery( this ).addClass('wbk-input-error');
			}
	});
	// validate custom fields (textareas)
	jQuery('.wbk-textarea[aria-required="true"]').each(function() {
	    var value =  jQuery( this ).val();
		if ( !wbkCheckString( value, 1, 1024 ) ){
			error_status = 1;
			jQuery( this ).addClass('wbk-input-error');
		}
	});
	// validate custom fields file inputs
	jQuery('.wbk-file[aria-required="true"]').each(function() {     
		if ( jQuery(this).prop('files').length == 0 ){
			error_status = 1;
			jQuery( this ).addClass('wbk-input-error');
		}
	});
	// validate checkbox
	jQuery('.wbk-checkbox-custom.wpcf7-validates-as-required').each(function() {
		var validbox = false;
		jQuery(this).find('.wbk-checkbox-custom').each( function(){
			if ( jQuery(this).is(':checked') ){
				validbox = true;
			}
		});
		if( !validbox ){
			jQuery(this).find('.wbk-checkbox-label').addClass( 'wbk-input-error' );
			error_status = 1;
		}
	});

	var extra_value = [];	 
	// custom fields values (text)			 
	jQuery('.wbk-text, .wbk-email-custom').not('#wbk-name,#wbk-email,#wbk-phone').each(function() {
		if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
			var extra_item = [];										 
			extra_item.push( jQuery( this ).attr('id') );
			extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
			extra_item.push( jQuery( this ).val() );
			extra_value.push( extra_item );									
		}
	});
	// custom fields values (checkbox)
	jQuery('.wbk-checkbox-custom.wpcf7-checkbox').each(function() {
		if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
			var extra_item = [];	
			extra_item.push( jQuery( this ).attr('id') );
			extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
			var current_checkbox_value = '';
			jQuery(this).children('span').each(function() {
				jQuery(this).children('input:checked').each(function() {
					current_checkbox_value += jQuery(this).val() + ' ';					 
				});
			});
			extra_item.push( current_checkbox_value );
			extra_value.push( extra_item );	
		}
	});	 
	jQuery('.wbk-select').not('#wbk-book-quantity, #wbk-service-id').each(function() {
		if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
			var extra_item = [];	
			extra_item.push( jQuery( this ).attr('id') );
			extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
			extra_item.push( jQuery( this ).val() );
			extra_value.push( extra_item );	
		}
	});
	// custom fields text areas
	jQuery('.wbk-textarea').not('#wbk-comment,#wbk-customer_desc').each(function() {
		if( jQuery(this).parent().parent().hasClass( 'wpcf7cf-hidden' ) == false ){
			var extra_item = [];	
			extra_item.push( jQuery( this ).attr('id') );
			extra_item.push( jQuery('label[for="' + jQuery( this ).attr('id') + '"]').html() );
			extra_item.push( jQuery( this ).val() );
			extra_value.push( extra_item );	
		}
	});
	// secondary names, emails
	var secondary_data = [];
	jQuery('[id^="wbk-secondary-name"]').each(function() {
		var name_p = jQuery(this).val();
		var name_id = jQuery(this).attr('id');
		if ( wbkCheckString( name, 1, 128 ) ){
			var arr = name_id.split( '_' );
			var id2 = 'wbk-secondary-email_' + arr[1];
			email_p = jQuery('#' + id2).val();

			var person = new Object();
			person.name = name_p;
			person.email = email_p;
			secondary_data.push(person);
		}
	});
	if ( error_status == 1 ) {
		jQuery('html, body').animate({ scrollTop: jQuery('#wbk-booking-form-container').last().offset().top - 120 }, 1000);
		return;
	}
	jQuery('#wbk-booking-done').html( '<div class="wbk-loading"></div>');
	jQuery('#wbk-booking-form-container').fadeOut('slow', function() {
		jQuery('#wbk-booking-form-container').html('');
		jQuery('#wbk-booking-form-container').fadeIn();
		jQuery('html, body').animate({
	        							scrollTop: jQuery('#wbk-booking-done').offset().top - 120
	   								}, 1000);
	});
	var form_data = new FormData();
	var time_offset = new Date().getTimezoneOffset();	

	extra_value = JSON.stringify( extra_value );
	form_data.append( 'action' , 'wbk_book');
	form_data.append( 'time', time );
	form_data.append( 'service', service);
	form_data.append( 'name', name);
	form_data.append( 'email', email);
	form_data.append( 'phone', phone);
	form_data.append( 'desc', desc);
	form_data.append( 'extra', extra_value );
	form_data.append( 'quantity', quantity);
	form_data.append( 'secondary_data', secondary_data);	
	form_data.append( 'current_category', current_category );
	form_data.append( 'time_offset', time_offset );

	wbk_cstuomer_email_on_from = email;

	var iteration = 0;
	if( wbkl10n.allow_attachment == 'yes' ){
		jQuery('.wbk-file').each( function () { 
			iteration++;
			var fileindex = 'file' + iteration;
			form_data.append( fileindex, jQuery(this).prop('files')[0] );			
		});
	}

    jQuery.ajax({
	  	url: wbkl10n.ajaxurl,
      	type: 'POST',
      	data: form_data,
      	cache: false,
        processData: false,
	    contentType: false,
    	success: function( response ) { 
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
					response != -12 &&
				response != -13
					) {
					jQuery('#wbk-to-checkout').fadeOut('fast');
					response_obj = jQuery.parseJSON( response );
					jQuery('#wbk-booking-done').html( '<div class="wbk-details-sub-title wbk-mb-20">' + response_obj.thanks_message + '</div>' );
                    if ( wbkl10n.hide_form == 'enabled' ){                   	
                        jQuery('#wbk-slots-container, #wbk-time-container, #wbk-date-container, #wbk-service-container' ).fadeOut( 'fast', function(){
                        	jQuery('#wbk-slots-container, #wbk-time-container, #wbk-date-container, #wbk-service-container').html('');
                        	jQuery('html, body').animate({
    							scrollTop: jQuery('#wbk-booking-done').offset().top - 120
								}, 1000);
                        });
                	} else {					                    		
                		jQuery('.wbk-slot-active-button').not('#wbk-to-checkout').each(function () { 
                			timeslots_after_book( jQuery( this ), quantity, response_obj.booked_slot_text );
                    	});
                	}
					if( typeof wbk_on_booking === 'function' ) {											 
					   wbk_on_booking( service, time, name, email, phone, desc, quantity );  
					}
					wbk_set_payment_events();								
			} else {
				jQuery('html, body').animate({
							scrollTop: jQuery('#wbk-booking-done').offset().top
							}, 1000);
				jQuery('#wbk-booking-done').html(wbkl10n.something_wrong);
			}
			jQuery('#wbk-slots-container').show('slide');						        	
    	}
 	});		 	    	
 
}
