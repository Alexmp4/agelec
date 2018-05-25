/* SLE default javacript */	
// validation functions
function slf_check_css_length( val ){
	var pattern = new RegExp(/^(auto|0)$|^[+-]?[0-9]+.?([0-9]+)?(px|em|ex|%|in|cm|mm|pt|pc)$/);
	return pattern.test(val);
}
// check integer
function slf_check_integer( val ) { 
 	return /^\+?(0|[1-9]\d*)$/.test(val);
}
// onload function
jQuery(function ($) {		 
	slf_set_events();
	jQuery('#slf-preview').dialog({ width:650,
									height:700,
									title: 'Booking form preview',
									resizable: false,									 
									autoOpen: false,
									close: function( event, ui ) { jQuery('#wbk-preview-btn').html('Show preview') },
									position: { my: 'right top', at: 'right bottom', of: '.slf-bar-top', collision: 'none' },
								    show: {
								        effect: 'fade',
								        duration: 300
								    },
								    hide: {
								        effect: 'fade',
								        duration: 300
								    }
									});
 

	jQuery( '#wpbody' ).prepend( '<div class="slf_notice"></div>' );
  
});
function slf_table_render_add_form( class_name ){
		var create_button_html = jQuery('#slf_table_add_panel').html();

		jQuery('.slf_create_new_btn').attr( 'Value', 'Loading...' );
		jQuery('.slf_create_new_btn').prop('disabled', true);
		
		var data = {
						'action': 'slf_table_render_add_row',
						'class_name': class_name,
	 					
	 				};
		var col_form_id = 'slf_table_add_panel';  	
		 		   	 
		jQuery.post( ajaxurl, data, function(response) { 
				jQuery('#slf_table_add_panel').fadeOut( 'fast', function(){
					
					jQuery('#' + col_form_id ).html(response);


					slf_on_colform_loaded( col_form_id );


			 		jQuery('#slf_table_add_panel').fadeIn('fast');

			 		// add row start
			        jQuery('#' + col_form_id ).find('.slf_table_row_save').click(function() {
			        	var fields = [];
			        	jQuery(this).parent().parent().find('.slf_table_component_input').each(function() {
			        		    
				        		var component_name = jQuery(this).attr('name');
				        		var component_val;
				        		if( jQuery(this).hasClass('slf_table_component_editor') ){
				        			component_val =tinyMCE.get( jQuery(this).attr('id') ).getContent();
				        		} else {
					        		component_val = jQuery(this).val();
				        		}
				        		 
								field = new Object();				
								field.value = component_val;
								field.name = component_name;
								 
								fields.push( field ); 

				       	});	

				        var data = {
									'action': 'slf_table_add_row',
									'class_name': class_name, 					 
				 					'fields': fields
				 		}; 
						

				 		jQuery('#' + col_form_id ).find('.slf_table_row_save').prop('disabled', true);
				 		jQuery('#' + col_form_id ).find('.slf_table_row_cancel').prop('disabled', true);
				 		jQuery('#' + col_form_id ).find('.slf_control_error_message' ).html('<div class="loading"></div>');	

						jQuery.post( ajaxurl, data, function(response) { 

					 		jQuery('#' + col_form_id ).find('.slf_table_row_save').prop('disabled', false);
					 		jQuery('#' + col_form_id ).find('.slf_table_row_cancel').prop('disabled', false);
					 	 	response = JSON.parse(response);
					 	 	if( response.status == 1 ){
					 	 		jQuery( '#slf_table_add_panel' ).fadeOut('fast', function(){
						 	 		jQuery('.slf-table > tbody').append(response.data);
                                    jQuery( '#slf_table_add_panel' ).html( create_button_html );
                                    jQuery( '#slf_table_add_panel' ).fadeIn('fast', function() {
                                    	if( typeof slf_row_after_add === 'function' ) {
											slf_row_after_edit();
										}	
                                    });
					 	 		});	
					 	 		 
					 	 	}
					 	 	if( response.status == 0 || response.status == 2 ){
						 		jQuery('#' + col_form_id ).find('.slf_table_row_save').prop('disabled', false);
						 		jQuery('#' + col_form_id ).find('.slf_table_row_cancel').prop('disabled', false);
						 		jQuery('#' + col_form_id ).find('.slf_control_error_message' ).html(response.data);	

					 	 	}
						});	 				
						return false;
				    });
			        // add row end
			        // start cancel
			        jQuery('#' + col_form_id ).find('.slf_table_row_cancel').click(function(){
			         	jQuery( '#slf_table_add_panel' ).fadeOut('fast', function(){
                            jQuery( '#slf_table_add_panel' ).html( create_button_html );
                            jQuery( '#slf_table_add_panel' ).fadeIn('fast');
						});	
			        });
			        // end cancel
		 		}); 		 
		
	       
        });

	 
}
function slf_table_prepare_delete_row( id, class_name ){
	jQuery('.tablesaw-sortable-btn').prop('disabled', true);
	var row_id = 'slf-table-row-' + id;
	var row_form_id = 'row-form-' + id;
	var col_form_id = 'col-form-' + id;
	if ( jQuery('#' + row_form_id ).length > 0 ){
		return;
	}
	var td_count = jQuery( '#' + row_id ).children('td').length;	 
 	var delete_panel ='<div class="slf_control_container"><input type="button" class="button-primary slf_table_row_delete" value="Delete" /><input type="button" class="button-primary slf_table_row_cancel" value="cancel" /></div><div class="slf_control_error_message"></div>';
 	jQuery('<tr style="display:none;background:rgb(243, 243, 243);" id="' + row_form_id +'"><td id="' + col_form_id +'" colspan="' + td_count + '">' + delete_panel + '</td></tr>').insertAfter(jQuery('#' + row_id ));	
	jQuery('#' + row_form_id ).toggle('slow');
 	// start cancel
	jQuery('#' + col_form_id ).find('.slf_table_row_cancel').click(function(){
		jQuery( '#' + row_form_id  ).fadeOut('fast', function(){
			jQuery( '#' + row_form_id  ).remove();
			if ( jQuery('[id^=row-form-]').length == 0){
				jQuery('.tablesaw-sortable-btn').prop('disabled', false );
			}
		 
		});	
	
	});
	// end cancel
	// delete start
	jQuery('#' + col_form_id ).find('.slf_table_row_delete').click(function() {
		var data = {
			'action': 'slf_table_delete_row',
			'class_name': class_name, 					 
			'row_id': id
		}; 
		jQuery('#' + col_form_id ).find('.slf_table_row_delete').prop('disabled', true);
		jQuery('#' + col_form_id ).find('.slf_table_row_cancel').prop('disabled', true);
		jQuery('#' + col_form_id ).find('.slf_control_error_message' ).html('<div class="loading"></div>');	
		jQuery.post( ajaxurl, data, function(response) { 
			response = parseInt( response );
 			if( response == '1' ){
				jQuery( '#' + row_form_id  ).fadeOut('fast', function(){
				jQuery( '#' + row_form_id  ).remove();
				jQuery( '#' + row_id ).remove();
				if ( jQuery('[id^=row-form-]').length == 0){
						jQuery('.tablesaw-sortable-btn').prop('disabled', false );
				}
			    });
			} else {
				jQuery('#' + col_form_id ).find('.slf_table_row_delete').prop('disabled', false);
	 			jQuery('#' + col_form_id ).find('.slf_table_row_cancel').prop('disabled', false);
	 			jQuery('#' + col_form_id ).find('.slf_control_error_message' ).html('Internal error x');

			}  
		});
	});
	// delete row end

}

function slf_table_prepare_row( id, class_name ){
 		jQuery('.tablesaw-sortable-btn').prop('disabled', true);

       
		var row_id = 'slf-table-row-' + id;
		var row_form_id = 'row-form-' + id;
		var col_form_id = 'col-form-' + id;
		 
		if ( jQuery('#' + row_form_id ).length > 0 ){
			return;
		}
		 
		var td_count = jQuery( '#' + row_id ).children('td').length;
		
		var data = {
						'action': 'slf_table_prepare_row',
						'class_name': class_name,
	 					'row_id': id
	 				};
    	jQuery('<tr style="display:none;background:rgb(243, 243, 243);" id="' + row_form_id +'"><td id="' + col_form_id +'" colspan="' + td_count + '"><div class="loading"></div></td></tr>').insertAfter(jQuery('#' + row_id ));	
		jQuery('#' + row_form_id ).toggle('slow');
		
		jQuery.post( ajaxurl, data, function(response) { 
				jQuery('#' + col_form_id ).html(response);

				slf_on_colform_loaded( col_form_id );

		        // save row start
		        jQuery('#' + col_form_id ).find('.slf_table_row_save').click(function() {
		        	var fields = [];
		         
		        	jQuery(this).parent().parent().find('.slf_table_component_input').each(function() {
		        		    
			        		var component_name = jQuery(this).attr('name');
			        		var component_val;
			        		if( jQuery(this).hasClass('slf_table_component_editor') ){
			        			component_val =tinyMCE.get( jQuery(this).attr('id') ).getContent();
			        		} else {
				        		component_val = jQuery(this).val();
			        		}
			        		 
							field = new Object();				
							field.value = component_val;
							field.name = component_name;
							 
							fields.push( field ); 

			       	});	
			      

			        var data = {
								'action': 'slf_table_update_row',
								'class_name': class_name,
			 					'row_id': id,
			 					'fields': fields
			 		}; 

			 		jQuery('#' + col_form_id ).find('.slf_table_row_save').prop('disabled', true);
			 		jQuery('#' + col_form_id ).find('.slf_table_row_cancel').prop('disabled', true);
			 		jQuery('#' + col_form_id ).find('.slf_control_error_message').html('<div class="loading"></div>');		
					jQuery.post( ajaxurl, data, function(response) { 
				 		jQuery('#' + col_form_id ).find('.slf_table_row_save').prop('disabled', false);
				 		jQuery('#' + col_form_id ).find('.slf_table_row_cancel').prop('disabled', false);
				 		response = JSON.parse(response);
				 		var status = response.status;
				 		if ( status == 1 ){
				 			jQuery('#' + row_id ).html(response.data);
							jQuery('#' + col_form_id ).fadeOut('fast', function() {
										jQuery('#' + row_form_id ).remove();
										if ( jQuery('[id^=row-form-]').length == 0){
												jQuery('.tablesaw-sortable-btn').prop('disabled', false );
										}
										if( typeof slf_row_after_edit === 'function' ) {
											slf_row_after_edit();
										}	
									});
							
				 		}
				 		if ( status == 2 ){		 			 
							jQuery('#' + col_form_id ).fadeOut('fast', function() {
										jQuery('#' + row_form_id ).remove();
										if ( jQuery('[id^=row-form-]').length == 0){
												jQuery('.tablesaw-sortable-btn').prop('disabled', false );
										}	
									});

				 		}
				 		if ( status == 0 ){	
				 			jQuery('#' + col_form_id ).find('.slf_control_error_message').html(response.data);
				 		}
					});	 				
					return false;
			    });
		        // save row end
				// start cancel
				jQuery('#' + col_form_id ).find('.slf_table_row_cancel').click(function(){
						jQuery( '#' + row_form_id  ).fadeOut('fast', function(){
							jQuery( '#' + row_form_id  ).remove();
							if ( jQuery('[id^=row-form-]').length == 0){
								jQuery('.tablesaw-sortable-btn').prop('disabled', false );
							}	
						});	
					});	
				// end cancel


		});
         

	
}

function aplyTableFiltering(){
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

// set events on load or update
function slf_set_events(){
	// tablesaw
	aplyTableFiltering();
	var wbk_filter_date_format = jQuery('#wbk_filter_date_format').val();
	jQuery( '.slf_date_range_start, .slf_date_range_end' ).datepicker({dateFormat: wbk_filter_date_format }).on( 'change', function() {
		 		jQuery('.slf_date_range').val( jQuery('.slf_date_range_start').val() + ';' + jQuery('.slf_date_range_end').val());
				jQuery('.slf_date_range').trigger('change');
	        });
	jQuery('#wbk_filter_services_control').chosen({width: '95%'}).change(function() {
   		 
	});
	// filter change event
	jQuery( '.slf_filter').change(function() {
		var filters = [];
		jQuery( '.slf_filter' ).each(function() {		 
		    filter = new Object();
			filter.field = jQuery(this).attr('data-field');	
			filter.value = jQuery(this).val();
	 
			filters.push(filter); 
		});					
		jQuery('.slf_overlay').css('display','block');
		var class_name =  jQuery('#slf_table_class_name').val();
		 
		var data = {
						'action': 'slf_table_update',
						'filters': filters,
						'class_name': class_name 
	 				};
		jQuery.post( ajaxurl, data, function(response) { 
		 	jQuery('#slf-table-container').html(response);
			aplyTableFiltering();
		});
	});
 	// CSV Export
	jQuery( '.slf_table_csv_export').click(function(){
 
		jQuery('#slf_table_csv_export_container').html('CSV export is here for demo purpose only. To unlock this feauture, please, upgrade to Premium version. <a  rel="noopener"  href="https://codecanyon.net/item/appointment-booking-for-wordpress-webba-booking/13843131?ref=WebbaPlugins" target="_blank">Upgrade now</a>.  '); 
		 
	});


	// slf menu porcessing
	jQuery( '.slf-section' ).css( 'display', 'none' );
	jQuery( '.slf-section' ).first().css( 'display', 'block' );
	jQuery( '.slf-menu-link' ).click(function() {
		jQuery('.slf-menu-item').removeClass( 'active' );
		jQuery(this).parent().addClass( 'active' );

		var section_selector = jQuery(this).attr( 'href' );
		jQuery( '.slf-section' ).css( 'display', 'none' );
		jQuery( section_selector ).fadeIn( 'fast' );

 		return false;
	});
	 

	// component value change event
	jQuery( '.slf-component' ).change(function() {
		var class_name = jQuery(this).attr( 'data-class' );
		var prop_name = jQuery(this).attr( 'data-prop' );
		var value = jQuery(this).val();
		var special = false;	
		if ( class_name == 'wbk-checkbox:after'  || class_name == 'wbk-checkbox' || class_name == 'wbk-checkbox + label::before, .wbk-checkbox + span::before' ) {
 			jQuery('<style> .wbk-checkbox + label::before, .wbk-checkbox + span::before{'+prop_name+':'+value+' !important;}</style>').appendTo('head'); 		 		
			special = true;
		}  
		if ( !special ){
			jQuery( '.'+class_name).css(prop_name, value, 'important');
		}		
	});


	// remove error status on focus
	jQuery( 'input' ).focus(function() {
		 
	});
	// set minicolors hex
	jQuery('.slf-color-hex').minicolors();

	// PM - component
	jQuery( '.slf-type-pm-sub' ).on( 'input',function() {
		var parennt = jQuery( this ).attr('data-parent');
		var val = jQuery( this ).val();
		val = val.replace(/\s+/g, '');
		jQuery(this).val(val);
		if ( !slf_check_css_length(val) ){
			jQuery(this).addClass('slf-input-error', 500,'easeOutQuad');
		} else {
			jQuery(this).removeClass('slf-input-error');
			var top = jQuery('#' + parennt + '_top').val();
			if ( !slf_check_css_length(top) ){
				return;
			}
			var right = jQuery('#' + parennt + '_right').val();
			if ( !slf_check_css_length(right) ){
				return;
			}		 
			var bottom = jQuery('#' + parennt + '_bottom').val();
			if ( !slf_check_css_length(bottom) ){
				return;
			}	
			var left = jQuery('#' + parennt + '_left').val();
			if ( !slf_check_css_length(left) ){
				return;
			}		 
			jQuery( '#' + parennt ).val(top + ' ' + right + ' ' + bottom + ' ' + left);
			jQuery( '#' + parennt ).trigger( 'change' );
		}
	});
 
	// border - component
	jQuery( '.slf-type-border-sub' ).on( 'input', function() {
		var parennt = jQuery( this ).attr('data-parent');
		var val = jQuery( this ).val();
		val = val.replace(/\s+/g, '');
 		jQuery(this).val(val);	 

 		var width = jQuery('#' + parennt + '_width').val();
  		var type = jQuery('#' + parennt + '_type').val();
 		var color = jQuery('#' + parennt + '_color').val();

 		// visualizate error
 		if ( jQuery(this).hasClass( 'slf-type-border-width' ) ){
 			if ( !slf_check_integer(width) ){
				jQuery(this).addClass('slf-input-error', 500,'easeOutQuad');
 			} else {
 				jQuery(this).removeClass('slf-input-error');
 			}
 		}
  		if ( jQuery(this).hasClass( 'slf-type-border-color' ) ){
 			if ( color == '' ){
 				jQuery(this).addClass('slf-input-error');
 			} else {
 				jQuery(this).removeClass('slf-input-error');
 			}
 		}
  
 		var error_status = 0;
		if ( !slf_check_integer(width) ){
			error_status = 1;
		}
		if ( color == '' ){
			error_status = 1;
		}
		if ( error_status == 0 ){
			jQuery( '#' + parennt ).val(width + 'px ' + type + ' ' + color );
		}
 		jQuery( '#' + parennt ).trigger( 'change' );	 
	});

	// color component 
	jQuery( '.slf-type-color' ).on( 'input', function() {
		var color = jQuery(this).val();
		if ( color == '' ){
 			jQuery(this).addClass('slf-input-error');
 		} else {
 			jQuery(this).removeClass('slf-input-error');
 		}
		jQuery(this).trigger( 'change' );	
	});

	// size px component
	jQuery( '.slf-type-size-px' ).on( 'input', function() {
		var value = jQuery(this).val();
		if ( !slf_check_css_length(value ) ){
 			jQuery(this).addClass('slf-input-error');
 		} else {
 			jQuery(this).removeClass('slf-input-error');
 		}
		jQuery(this).trigger( 'change' );	
	});



}
  
// save data
function slf_save_sections_set( framework_slug, section_set_slug ){

	var error_count = jQuery('.slf-input-error').size();
 	if ( error_count > 0){
		jQuery('.slf_notice').html( error_count + ' validation errors, please fix it.' );	
		jQuery('.slf_notice').css('background', 'red');				
		jQuery(function () {
		  jQuery('.slf_notice').fadeIn('slow', function () {
		     jQuery(this).delay(3000).fadeOut('slow');
		  });
		});
		return;

	}
	jQuery('.slf_overlay').css('display','block');
	jQuery( '.slf-save-button' ).prop( 'disabled', 'true');	
	jQuery( '.slf-save-button' ).val( 'Saving...');	

	var data = new Object();
	data.slug = section_set_slug;
	data.components = [];
	jQuery( '.slf-component' ).each(function() {
		item = new Object();
		item.section = jQuery( this ).attr('data-section');
		item.name =  jQuery( this ).attr('name');
		item.value =  jQuery( this ).val();
		item.css_class = jQuery( this ).attr('data-class');
		item.css_prop = jQuery( this ).attr('data-prop');
		data.components.push(item); 
	});

	var data = {   
		'action': 'slf_save_section_set',
 		'data':  data,
 		'framework_slug': framework_slug
	};

	jQuery.post(ajaxurl, data, function(response) {
		jQuery( '.slf-save-button' ).val( 'Save options');	
		jQuery( '.slf-save-button' ).prop('disabled', false);	
		jQuery('.slf_overlay').css('display','none');
		jQuery('.slf_notice').html('Saved');	
		jQuery('.slf_notice').css('background', 'green');				
		jQuery(function () {
		  jQuery('.slf_notice').fadeIn('slow', function () {
		     jQuery(this).delay(3000).fadeOut('slow');
		  });
		});
	});
}

function show_prview(){
	if ( jQuery('#slf-preview').dialog('isOpen') ){
		jQuery('#slf-preview').dialog('close');
		jQuery('#wbk-preview-btn').html('Show preview')
	} else {
		jQuery('#slf-preview').dialog('open');
		jQuery('#wbk-preview-btn').html('Hide preview')
	}

	jQuery('.slf-component').trigger('change');
 }

// serialize data
function slf_serialize_sections_set( framework_slug, section_set_slug ){

 
	jQuery( '.slf-serialize-button' ).prop( 'disabled', 'true');	
	jQuery( '.slf-serialize-button' ).val( 'loading...');	
	
	var section_name = jQuery('#serial_section_name').val();

	var data = new Object();
	data.slug = section_set_slug;
	data.name = section_name;

	var data = {   
		'action': 'slf_serialize_section_set',
 		'data':  data,
 		'framework_slug': framework_slug
	};

	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#slf-console').html(response);
		 
		jQuery( '.slf-serialize-button' ).val( 'Serialize options');	
		jQuery( '.slf-serialize-button' ).prop('disabled', false);			
	});
}

// deserialize data
function slf_load_presets( framework_slug, section_set_slug ){
	jQuery('.slf_overlay').css('display','block');
	jQuery( '.slf-deserialize-button' ).prop( 'disabled', 'true');	
	jQuery( '.slf-deserialize-button' ).val( 'loading...');	
	var path = jQuery('#presets_list').val();
 	
	var data = new Object();
	data.slug = section_set_slug;
	data.path = path;
	var data = {   
		'action': 'slf_deserialize_section_set',
 		'data':  data,
 		'framework_slug': framework_slug
	};

	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#slf-sections').html(response);	
	 	jQuery('.slf_overlay').css('display','none');
		jQuery( '.slf-deserialize-button' ).val( 'Load presets');	
		jQuery( '.slf-deserialize-button' ).prop('disabled', false);	
		slf_set_events();
		jQuery( '.slf-type-border-sub' ).trigger( 'input' );	
		jQuery( '.slf-component' ).trigger( 'change' );	

	});

}