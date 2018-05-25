function slf_on_colform_loaded( col_form_id ){
	jQuery('.slf_table_component_service_multi_select').chosen({width: '300px'});

	var wbk_date_format = jQuery('#wbk_backend_date_format').val(); 
	jQuery('input[name="date_range"]').datepick({ rangeSelect: true, dateFormat: wbk_date_format });

	jQuery('input[name="amount_percentage"]').change(function() {
		jQuery('input[name="amount_fixed"]').val(0);

	});
	jQuery('input[name="amount_fixed"]').change(function() {
		jQuery('input[name="amount_percentage"]').val(0);

	});

}