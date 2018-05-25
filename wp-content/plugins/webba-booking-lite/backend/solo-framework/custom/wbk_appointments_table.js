// onload function
jQuery(function ($) {	
	jQuery('#wbk_filter_services_control_helper').on('change', function() {
  		if( this.value == '1' ){
  			jQuery('#wbk_filter_services_control option').prop( 'selected', true);
  			jQuery('#wbk_filter_services_control').trigger( 'chosen:updated' );
  			jQuery( '.slf_filter').trigger('change');
  		}	
  		if(  this.value == '2' ){
  			var serv_slug =  jQuery('#wbk_filter_services_control_helper option:selected').attr('data-services-classes');
  			if( serv_slug != '' ){
  				jQuery('#wbk_filter_services_control option').prop( 'selected', false );
  				jQuery( serv_slug ).prop( 'selected', true );
  				jQuery('#wbk_filter_services_control').trigger( 'chosen:updated' );
  				jQuery( '.slf_filter').trigger('change');
  			}
  		}

	});
});
function slf_on_colform_loaded( col_form_id ){
	// select service event
	jQuery('#' + col_form_id ).find('.wbk-service-select').on('change', function() {
		if( this.value == '-1' ){
			slf_disable_date_picker( col_form_id );
			slf_disable_time_select( col_form_id );
			slf_disable_quantity_select( col_form_id );
		} else {
			slf_enable_date_picker( col_form_id );
			slf_date_changed( col_form_id );
			jQuery('#' + col_form_id ).find('[name="duration"]').val( jQuery(this).find('option:selected').attr('data-ext') );
		}
	});
	// assign date picker
	var wbk_filter_date_format = jQuery('#wbk_filter_date_format').val();
	jQuery('#' + col_form_id ).find('.slf_table_component_date').datepicker({dateFormat: wbk_filter_date_format }).on( 'change', function() {
		slf_date_changed( col_form_id );	 		 
	});

	jQuery('#' + col_form_id ).find('[name="time"]').on('change', function() {
		slf_reset_available_count_field( col_form_id );
	});	
 	slf_reset_available_count_field( col_form_id );

 	jQuery('#' + col_form_id ).find('.slf_table_custom_field_part').on('change', function() {

 		extra_value = [];

 		jQuery( '#' + col_form_id ).find('.slf_table_custom_field_part').each( function() {
		 
			var extra_item = [];	

			extra_item.push( jQuery( this ).attr('data-id') );
			extra_item.push( jQuery( this ).attr('data-label') );
			extra_item.push( jQuery( this ).val() );

			extra_value.push( extra_item );

		});

 		jQuery( '#' + col_form_id ).find('.slf_table_component_custom_field').val(  JSON.stringify( extra_value ) );


 	});
}
function slf_enable_date_picker( col_form_id ){
	jQuery('#' + col_form_id ).find('.slf_table_component_date').prop('disabled', false);
}
function slf_disable_date_picker( col_form_id ){
	jQuery('#' + col_form_id ).find('.slf_table_component_date').prop('disabled', true); 
	jQuery('#' + col_form_id ).find('.slf_table_component_date').val('');
}
function slf_enable_time_select( col_form_id ){
	jQuery('#' + col_form_id ).find('[name="time"]').prop('disabled', false);
}
function slf_disable_time_select( col_form_id ){
	jQuery('#' + col_form_id ).find('[name="time"]').prop('disabled', true); 
	jQuery('#' + col_form_id ).find('[name="time"]').find('option').remove();
}
function slf_enable_quantity_select( col_form_id ){
	jQuery('#' + col_form_id ).find('[name="quantity"]').prop('disabled', false);
}
function slf_disable_quantity_select( col_form_id ){
	jQuery('#' + col_form_id ).find('[name="quantity"]').prop('disabled', true); 
	jQuery('#' + col_form_id ).find('[name="quantity"]').find('option').remove();
}
function slf_date_changed( col_form_id ){
	var appointment_id;	
	if ( col_form_id == 'slf_table_add_panel' ){
		appointment_id = -1;
	} else {
		appointment_id = col_form_id.split( '-' );
		appointment_id = appointment_id[2];
	}
	var service = jQuery('#' + col_form_id ).find('.wbk-service-select').val();
	var date = jQuery('#' + col_form_id ).find('.slf_table_component_date').val();
	jQuery('#' + col_form_id ).find('[name="time"]').prop('disabled', true); 
	jQuery('#' + col_form_id ).find('[name="time"]').find('option').remove();
	jQuery('#' + col_form_id ).find('[name="time"]').prop('quantity', true); 
	jQuery('#' + col_form_id ).find('[name="time"]').find('option').remove();
	jQuery('#' + col_form_id ).find('[name="time"]').append( jQuery('<option>', { value: '-1', text:  'loading...' }));
	var data = {   
							'action': 'wbk_get_free_time_for_day',
							'appointment_id': appointment_id,
							'service_id': service,
							'date': date
							 
		 				};
	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#' + col_form_id ).find('[name="time"]').prop('disabled', false); 
		jQuery('#' + col_form_id ).find('[name="time"]').find('option').remove();
 		jQuery('#' + col_form_id ).find('[name="time"]').append(response);
 		jQuery('#' + col_form_id ).find('[name="time"]').trigger('change');
	});
}
function slf_reset_available_count_field( col_form_id ){
	jQuery('#' + col_form_id ).find('[name="quantity"]').find('option').remove();
	if( jQuery('#' + col_form_id ).find('[name="time"]').val() == null || jQuery('#' + col_form_id ).find('[name="time"]').val() == '0' ){
		return;
	}
	var avail = jQuery('#' + col_form_id ).find('[name="time"]').find('option:selected').attr('data-ext');
	for ( i = 0; i < avail; i++ ) {
		var n = i + 1;
	    jQuery('#' + col_form_id ).find('[name="quantity"]').append( jQuery('<option>', {
		    value: n,
		    text:  n
		}));
	}
	var data_init = jQuery('#' + col_form_id ).find('[name="quantity"]').attr('data-init');
	if( data_init != '' ){
		jQuery('#' + col_form_id ).find('[name="quantity"]').val( data_init );
		jQuery('#' + col_form_id ).find('[name="quantity"]').attr('data-init', '');
	}
}
function slf_row_after_edit(){
	jQuery('[id^=col-form-]').each(function() {	
		var col_form_id = jQuery(this).attr('id');
		slf_date_changed( col_form_id );
	});
	slf_date_changed( 'slf_table_add_panel' );
}
function slf_row_after_add(){
	jQuery('[id^=col-form-]').each(function() {	
		var col_form_id = jQuery(this).attr('id');
		slf_date_changed( col_form_id );
	});
	slf_date_changed( 'slf_table_add_panel' );
}