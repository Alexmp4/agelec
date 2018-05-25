function slf_on_colform_loaded( col_form_id ){
	var loaded_id = jQuery('#' + col_form_id ).find('textarea').attr('id');

	var mceInit_loc = {forced_root_block:"",theme:"modern",skin:"lightgray",language:"en",formats:{alignleft: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
		 styles: {textAlign:"left"}},{selector: "img,table,dl.wp-caption", classes: "alignleft"}],aligncenter: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"center"}},
		 {selector: "img,table,dl.wp-caption", classes: "aligncenter"}],alignright: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"right"}},
		 {selector: "img,table,dl.wp-caption", classes: "alignright"}],strikethrough: {inline: "del"}},
		 relative_urls:false,remove_script_host:false,convert_urls:false,browser_spellcheck:true,
		 fix_list_elements:true,entities:"38,amp,60,lt,62,gt",entity_encoding:"raw",
		 keep_styles:false,cache_suffix:"wp-mce-4401-20160726",preview_styles:"font-family font-size font-weight font-style text-decoration text-transform",
		 end_container_on_empty_block:true,wpeditimage_disable_captions:false,wpeditimage_html5_captions:true,
	 plugins:"charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
	 wp_lang_attr:"en-US",
	 external_plugins:{"wbk_tinynce":"\/wp-content\/plugins\/webba-booking\/backend\/js\/wbk-tinymce.js"},
	 selector:"#asdd",resize:"vertical",menubar:false,wpautop:true,indent:false,toolbar1:"bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv,wbk_service_name_button,wbk_category_names_button,wbk_customer_name_button,wbk_appointment_day_button,wbk_appointment_time_button,wbk_appointment_local_day_button,wbk_appointment_local_time_button,wbk_appointment_id_button,wbk_customer_phone_button,wbk_customer_email_button,wbk_customer_comment_button,wbk_customer_custom_button,wbk_items_count,wbk_total_amount,wbk_payment_link,wbk_cancel_link,wbk_tomorrow_agenda,wbk_group_customer,wbk_multiple_loop,wbk_admin_cancel_link,wbk_admin_approve_link,wbk_customer_ggcl_link,wbk_time_range",toolbar2:"formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",toolbar3:"",toolbar4:"",tabfocus_elements:":prev,:next",body_class:"asdd locale-en-us"};

	tinymce.init( mceInit_loc ); 	 	

	tinyMCE.execCommand('mceAddEditor', false, loaded_id);
}