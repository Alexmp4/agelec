<?php
/* 
     * Solo Framework is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * any later version.
     * Solo Framework is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     * You should have received a copy of the GNU General Public License
     * along with Solo Framework. If not, see <http://www.gnu.org/licenses/>.
*/
if ( !class_exists('SoloFramework', false) ):
include 'core/class_slf_section.php';
include 'core/class_slf_section_set.php';
include 'core/class_slf_component.php';
include 'core/class_slf_field.php';
include 'core/class_slf_field_set.php';
include 'core/class_slf_table.php';
include 'core/class_slf_table_filter.php';
include 'core/class_slf_valdator.php';
include 'components/class_slf_table_filter_date_range.php';
include 'custom/class_wbk_table_filter_services.php';
include 'custom/class_wbk_table_filter_access.php';
include 'core/class_slf_table_component.php';
include 'components/class_slf_table_text.php';
include 'components/class_slf_table_textarea.php';
include 'components/class_slf_table_hidden_text.php';
include 'components/class_slf_table_email.php';
include 'components/class_slf_table_date.php';
include 'components/class_slf_table_time.php';
include 'components/class_slf_table_select.php';
include 'custom/class_slf_table_service_multi_select.php';
include 'components/class_slf_table_html_editor.php';
include 'components/class_slf_table_wbk_custom_field.php';
include 'custom/class_wbk_component_service.php';
include 'custom/class_wbk_appointments_table.php';
include 'custom/class_wbk_coupons_table.php';
include 'custom/class_wbk_service_categories_table.php';
include 'custom/class_wbk_email_templates_table.php';
include 'components/class_slf_text.php';
include 'components/class_slf_padding_margin.php';
include 'components/class_slf_border.php';
include 'components/class_slf_color.php';
include 'components/class_slf_size_px.php';
include 'components/class_slf_font_style.php';
include 'components/class_slf_font_weight.php';
include 'components/class_slf_text_align.php';
include 'custom/class_wbk_gg_calendar_table.php';
include 'components/class_slf_table_gg_auth.php';
include 'components/class_slf_table_wp_user.php';

function slf_register_actions(){ 
	add_action( 'init', 'slfLoadAssets' );
}
add_action( 'wp_ajax_slf_save_section_set', 'slf_save_section_set' );	
add_action( 'wp_ajax_slf_serialize_section_set', 'slf_serialize_section_set' );	
add_action( 'wp_ajax_slf_deserialize_section_set', 'slf_deserialize_section_set' );	
add_filter( 'mce_buttons',  'slf_mce_add_button'  );
add_filter( 'mce_external_plugins',   'slf_mce_add_javascript'   );
add_filter( 'wp_default_editor', create_function( '', 'return \'tinymce\';' ) );
add_filter( 'tiny_mce_before_init', 'slf_customizeEditor'   );
   
function slf_customizeEditor( $in ) {
	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wbk-email-templates' ) {
		$in['remove_linebreaks'] = false;
	 	$in['remove_redundant_brs'] = false;
 		$in['wpautop'] = false;
 	}
 	return $in;
}
function slf_mce_add_button( $buttons ) {
	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wbk-email-templates' ) {
		$buttons[] = 'wbk_service_name_button';
		$buttons[] = 'wbk_customer_name_button';
		$buttons[] = 'wbk_appointment_day_button';
		$buttons[] = 'wbk_appointment_time_button';
		$buttons[] = 'wbk_appointment_id_button';
		$buttons[] = 'wbk_customer_phone_button';
		$buttons[] = 'wbk_customer_email_button';
		$buttons[] = 'wbk_customer_comment_button';
		$buttons[] = 'wbk_customer_custom_button';
		$buttons[] = 'wbk_items_count';
		$buttons[] = 'wbk_total_amount';
		$buttons[] = 'wbk_payment_link';
		$buttons[] = 'wbk_cancel_link';
		$buttons[] = 'wbk_tomorrow_agenda';
		$buttons[] = 'wbk_group_customer';
		$buttons[] = 'wbk_multiple_loop';
		$buttons[] = 'wbk_category_names_button';
	}
	return $buttons;
}
function slf_mce_add_javascript( $plugin_array ) {
	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wbk-email-templates' ) {
		$plugin_array['wbk_tinynce'] =  plugins_url( 'js/wbk-tinymce.js' , dirname( __FILE__ ) );
	}
	return $plugin_array;
}
function slf_save_section_set(){
	if ( !current_user_can('manage_options') ){
		wp_die();
		return;
	}
	$data = (object) $_POST['data'];
	$framework_slug = $_POST['framework_slug'];
	$slf = new SoloFramework( $framework_slug );
  	foreach ( $data->components as $component ) {
 		$section = $component['section'];
 		$name = $component['name'];
 		$value = $component['value'];
 		$slf->getSetionsSet( $data->slug )->sections[ $section ]->setComponentValue( $name, $value );
 	}
 	$slf->save();
	$result = $slf->getSetionsSet( $data->slug )->compileFrontendCss( $data );
	$save_status = get_option( 'wbk_appearance_saved','' );
	if ( $save_status == '' ){
		add_option( 'wbk_appearance_saved', 'true' );	
	} else {
		update_option( 'wbk_appearance_saved', 'true' );		
	}
  	wp_die();
	return;
}
// DEVELOPERS ONLY
function slf_serialize_section_set(){
	if ( !current_user_can('manage_options') ){
		wp_die();
		return;
	}
 	$data = (object) $_POST['data'];
	$framework_slug = $_POST['framework_slug'];
	$slf = new SoloFramework( $framework_slug );
 	$section_set = $slf->getSetionsSet( $data->slug );
 	$section_set->name = $data->name;
  	$result = json_encode( $section_set ); 
 	$section_slug = $section_set->slug;  	
	$path_to_file =   dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'export.json';
    $json_file = fopen( $path_to_file, 'w' ) or die( 'Unable to create json file '); 	        
	fwrite( $json_file, $result );
	fclose( $json_file );
	wp_die();
	return;
}
function slf_deserialize_section_set(){
	if ( !current_user_can('manage_options') ){
		wp_die();
		return;
	}	
 	$data = (object) $_POST['data'];
	$framework_slug = $_POST['framework_slug'];
	$slf = new SoloFramework( $framework_slug );
 	$section_slug = $slf->getSetionsSet( $data->slug )->slug;  	
	$path_to_file = $data->path;
    $objData = file_get_contents($path_to_file);
	$data = json_decode($objData, true);
	$section_set = new SLFSectionSet( array( 'slug' => $data['slug'],
											 'css_default' => $data['css_default'],
											 'css_custom'  => $data['css_custom'],
											 'name' => $data['name']
												) );
 	foreach ($data['sections'] as $section_data) {
		$section = new SLFSection( array( 'name' => $section_data['name'],
										  'description' => $section_data['description'],
										  'slug' => $section_data['slug'] 
										  ) );
		foreach( $section_data['components'] as $component_data ){
				$component = new $component_data['class_name'] ( array( 'name' => $component_data['name'] ,
						 												'desc' => $component_data['desc'],  
						 												'slug' => $component_data['slug'], 
						 												'value' => $component_data['value'],
						 												'css_class' => $component_data['css_class'],
						 												'css_prop' => $component_data['css_prop']
																) ); 
				$section->addCompontnet( $component );					 												
		}
		$section_set->addSection( $section );
 	}
 	echo $section_set->render();
  	wp_die();
  	return;
}
function slfLoadAssets(){
	if ( isset( $_GET[ 'page' ] ) && ( $_GET[ 'page' ] == 'wbk-appearance'   ||   
		$_GET[ 'page' ] == 'wbk-appointments' ||
		$_GET[ 'page' ] == 'wbk-email-templates' || 
		$_GET[ 'page' ] == 'wbk-service-categories'  ||
		$_GET[ 'page' ] == 'wbk-gg-calendars'||
		$_GET[ 'page' ] == 'wbk-coupons'   ) ) { 

	  	wp_enqueue_style( 'slf-default-style', plugins_url( '/css/slf-default.css', __FILE__ ) );
		wp_enqueue_style( 'slf-minicolors-style', plugins_url( 'css/jquery.minicolors.css',  __FILE__ ) );
 		wp_enqueue_style( 'slf-tablesaw', plugins_url( '/css/tablesaw.css', __FILE__ ) );
		wp_enqueue_style( 'slf-chosen-css', plugins_url( 'css/chosen.min.css',  __FILE__ ) );
		wp_enqueue_style( 'slf-jquery-ui-css', plugins_url( 'css/jquery-ui.wbk.min.css',  __FILE__ ) );
		wp_enqueue_script( 'jquery-plugin', plugins_url( 'js/jquery.plugin.js', dirname( __FILE__ ) ), array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-resizable', array( 'jquery','jquery-ui-core','jquery-ui-widget', 'jquery-ui-mouse' ) );
	  	wp_enqueue_script( 'slf-default-javascript', plugins_url( 'js/slf-default.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-fade' ) ); 
	    wp_enqueue_script( 'slf-minicolors', plugins_url( 'js/jquery.minicolors.min.js',  __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog' ) );                      
	    wp_enqueue_script( 'slf-tablesaw', plugins_url( 'js/tablesaw.js',  __FILE__ ), array( 'jquery' ) );                      
		wp_enqueue_script( 'slf-chosen', plugins_url( 'js/chosen.jquery.min.js',   __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ) );
		$translation_array = array(
				'slf_date_format' => get_option('date_format','F j, Y')		
		);
		wp_localize_script( 'slf-default-javascript', 'slfl10n', $translation_array );
	}
	if ( isset( $_GET[ 'page' ] ) &&   $_GET[ 'page' ] == 'wbk-email-templates' ) { 
		wp_enqueue_script( 'wbk-email-templates', plugins_url( 'custom/wbk_email_templates_table.js',   __FILE__ ), array( 'jquery', 'jquery-ui-core' ) );
	}
	if ( isset( $_GET[ 'page' ] ) &&   $_GET[ 'page' ] == 'wbk-appointments' ) { 
		wp_enqueue_script( 'wbk-email-templates', plugins_url( 'custom/wbk_appointments_table.js',   __FILE__ ), array( 'jquery', 'jquery-ui-core' ) );
	}
	if ( isset( $_GET[ 'page' ] ) &&   $_GET[ 'page' ] == 'wbk-service-categories' ) { 
		wp_enqueue_script( 'wbk-email-templates', plugins_url( 'custom/wbk_service_categories_table.js',   __FILE__ ), array( 'jquery', 'jquery-ui-core' ) );
	}
	if ( isset( $_GET[ 'page' ] ) &&   $_GET[ 'page' ] == 'wbk-gg-calendars' ) { 
		wp_enqueue_script( 'wbk-email-templates', plugins_url( 'custom/wbk_gg_calendars_table.js',   __FILE__ ), array( 'jquery', 'jquery-ui-core' ) );
	}
	if ( isset( $_GET[ 'page' ] ) &&   $_GET[ 'page' ] == 'wbk-coupons' ) { 
		wp_enqueue_script( 'slf-coupons', plugins_url( 'custom/wbk_coupons_table.js',   __FILE__ ), array( 'jquery', 'jquery-ui-core' ) );
		wp_enqueue_script( 'slf-datepick', plugins_url( 'js/jquery.datepick.min.js',   __FILE__ ), array( 'jquery', 'jquery-ui-core' ) );		
		wp_enqueue_style( 'wbk-datepicker-css', plugins_url( 'css/jquery.datepick.css', dirname( __FILE__ ) )  );
	}

}
class SoloFramework extends stdClass  {
	protected $sections_sets = array();
	protected $slug;
	public function __construct( $slug ) { 			 
		$this->slug = $slug;
		if ( get_option( $slug, '' ) != '' ){
			$loaded_obj = get_option( $slug ) ;
			$this->slug = $loaded_obj->slug;
			$this->sections_sets = $loaded_obj->sections_sets; 
		} else {
	 		$this->init();
		}
		$this->update_3_4_0();
	}
	public function getSetionsSet( $slug ){
		return $this->sections_sets[ $slug ];
	}
	public function loadSectionAssets( $slug ){
		$this->sections_sets[ $slug ]->loadSectionAssets();
	}
	public function init() { 
		$section_set = new SLFSectionSet( array( 'slug' => 'wbk_extended_appearance_options',
												 'css_default' => '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'wbk-frontend-default-style.css',
												 'css_custom'  => dirname( __FILE__ ) . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'wbk-frontend-custom-style.css',
												 'name' => 'Webba Booking appearance options'
												) );
	 	// outer container section
 		$section = new SLFSection( array( 'name' => __( 'Outer container', 'wbk' ),
										  'description' => __( 'Booking form outer container appearance options', 'wbk' ),
										  'slug' => 'section_outer_container' ) );
				// padding 
		 		$component = new SLFPaddingMargin( array( 'name' => __( 'Padding', 'wbk' ),
		 												  'desc' => __( 'Outer container padding', 'wbk' ),  
		 												  'slug' => 'outer_container_padding', 
		 												  'value' => '5px 5px 5px 5px',
		 												  'css_class' => 'wbk-outer-container',
		 												  'css_prop' => 'padding'
		 												   )); 		
		 		$section->addCompontnet( $component );
		 		// margin
				$component = new SLFPaddingMargin( array( 'name' => __( 'Margin', 'wbk' ),
														  'desc' => __( 'Outer container margin', 'wbk' ),
														  'slug' => 'outer_container_margin', 
														  'value' => '10px 10px 10px 10px',
		 												  'css_class' => 'wbk-outer-container',
		 												  'css_prop' => 'margin'
		 												   )); 
		 		$section->addCompontnet( $component );
		 		// border
				$component = new SLFBorder( array( 'name' => __( 'Border', 'wbk' ),
												   'desc' => __( 'Outer container border', 'wbk' ),
												   'slug' => 'outer_container_border',
												   'value' => '1px solid #c7c7c7',
												   'css_class' => 'wbk-outer-container',
		 										   'css_prop' => 'border'
												    )); 		
		 		$section->addCompontnet( $component );
		 		// border radius
				$component = new SLFSizePx( array( 'name' => __( 'Border radius', 'wbk' ),
												   'desc' => __( 'Outer container border radius', 'wbk' ),
												   'slug' => 'outer_container_border_radius',
												   'value' => '0',
												   'css_class' => 'wbk-outer-container',
		 										   'css_prop' => 'border-radius'
												    )); 		
		 		$section->addCompontnet( $component );
		 		// background color
				$component = new SLFColor( array( 'name' => __( 'Background color', 'wbk' ),
												   'desc' => __( 'Outer container background color', 'wbk' ),
												   'slug' => 'outer_container_bg_color',
												   'value' => '#ffffff',
												   'css_class' => 'wbk-outer-container',
		 										   'css_prop' => 'background-color'
												    )); 		
		 		$section->addCompontnet( $component );
		$section_set->addSection( $section );
	 	// inner container section
 		$section = new SLFSection( array( 'name' => __( 'Inner container', 'wbk' ),
										  'description' => __( 'Booking form inner container appearance options', 'wbk' ),
										  'slug' => 'section_inner_container' ) );
				// padding 
		 		$component = new SLFPaddingMargin( array( 'name' => __( 'Padding', 'wbk' ), 
		 												  'desc' => __( 'Inner container padding', 'wbk' ),
		 												  'slug' => 'inner_container_padding',
		 												  'value' => '15px 15px 15px 15px',
														  'css_class' => 'wbk-inner-container',
		 												  'css_prop' => 'padding'
    	 												    )); 		
		 		$section->addCompontnet( $component );
	 			// border
				$component = new SLFBorder( array( 'name' => __( 'Border', 'wbk' ),
												   'desc' => __( 'Inner container border', 'wbk' ),
												   'slug' => 'inner_container_border',
												   'value' => '1px solid #ebebeb',
												   'css_class' => 'wbk-inner-container',
		 										   'css_prop' => 'border'
												    )); 	
		 		$section->addCompontnet( $component );
		 		// border radius
				$component = new SLFSizePx( array( 'name' => __( 'Border radius', 'wbk' ),
												   'desc' => __( 'Inner container border radius', 'wbk' ),
												   'slug' => 'inner_container_border_radius',
												   'value' => '0',
												   'css_class' => 'wbk-inner-container',
		 										   'css_prop' => 'border-radius'
												    )); 		
		 		$section->addCompontnet( $component );
		 		// background color
				$component = new SLFColor( array( 'name' => __( 'Background color', 'wbk' ),
												   'desc' => __( 'Inner container background color', 'wbk' ),
												   'slug' => 'inner_container_bg_color',
												   'value' => '#f7f7f7',
												   'css_class' => 'wbk-inner-container',
		 										   'css_prop' => 'background-color'
												    )); 		
		 		$section->addCompontnet( $component );
		$section_set->addSection( $section );
		// step separator
 		$section = new SLFSection( array( 'name' => __( 'Separators', 'wbk' ),
										  'description' => __( 'Booking form separators', 'wbk' ),
										  'slug' => 'section_separators' ) );
		 		// main margin
				$component = new SLFPaddingMargin( array( 'name' => __( 'Step separator margin', 'wbk' ),
														  'desc' => __( 'Booking form step separator margin', 'wbk' ),
														  'slug' => 'step_separator_margin', 
														  'value' => '10px 0 10px 0',
		 												  'css_class' => 'wbk-separator',
		 												  'css_prop' => 'margin'
		 												   ));  
		 		$section->addCompontnet( $component );
	 			// main border
				$component = new SLFBorder( array( 'name' => __( 'Step separator border', 'wbk' ),
												   'desc' => __( 'Booking form step separator border', 'wbk' ),
												   'slug' => 'step_separator_border',
												   'value' => '1px solid #d1d1d1',
												   'css_class' => 'wbk-separator',
		 										   'css_prop' => 'border-top'
												    )); 	
		 		$section->addCompontnet( $component );
		 		// select hours label underline margin                                            
				$component = new SLFPaddingMargin( array( 'name' => __( 'Select hours label	underline margin', 'wbk' ),
														  'desc' => __( 'Booking form select hours label underline margin', 'wbk' ),
														  'slug' => 'hours_separator_margin', 
														  'value' => '10px 0 10px 0',
		 												  'css_class' => 'wbk-hours-separator',
		 												  'css_prop' => 'margin'
		 												   ));  
		 		$section->addCompontnet( $component );
	 			// main border
				$component = new SLFBorder( array( 'name' => __( 'Select hours label underline style', 'wbk' ),
												   'desc' => __( 'Booking form select hours label underline style', 'wbk' ),
												   'slug' => 'hours_separator_border',
												   'value' => '1px solid #d1d1d1',
												   'css_class' => 'wbk-hours-separator',
		 										   'css_prop' => 'border-top'
												    )); 	
		 		$section->addCompontnet( $component );
		 		// select hours label underline margin                                            
				$component = new SLFPaddingMargin( array( 'name' => __( 'Day title underline margin', 'wbk' ),
														  'desc' => __( 'Booking form day title underline margin', 'wbk' ),
														  'slug' => 'day_separator_margin', 
														  'value' => '10px 0 10px 0',
		 												  'css_class' => 'wbk-day-separator',
		 												  'css_prop' => 'margin'
		 												   ));  
		 		$section->addCompontnet( $component );
	 			// main border
				$component = new SLFBorder( array( 'name' => __( 'Day title underline style', 'wbk' ),
												   'desc' => __( 'Booking form day title underline style', 'wbk' ),
												   'slug' => 'day_separator_border',
												   'value' => '1px solid #d1d1d1',
												   'css_class' => 'wbk-day-separator',
		 										   'css_prop' => 'border-top'
												    )); 	
		 		$section->addCompontnet( $component );
		 		// form title underline margin                                            
				$component = new SLFPaddingMargin( array( 'name' => __( 'Customer details title underline margin', 'wbk' ),
														  'desc' => __( 'Booking form customer details title underline margin', 'wbk' ),
														  'slug' => 'form_separator_margin', 
														  'value' => '10px 0 10px 0',
		 												  'css_class' => 'wbk-form-separator',
		 												  'css_prop' => 'margin'
		 												   ));  
		 		$section->addCompontnet( $component );
	 			// main border
				$component = new SLFBorder( array( 'name' => __( 'Customer details title underline style', 'wbk' ),
												   'desc' => __( 'Booking form customer details title underline style', 'wbk' ),
												   'slug' => 'form_separator_border',
												   'value' => '1px solid #d1d1d1',
												   'css_class' => 'wbk-form-separator',
		 										   'css_prop' => 'border-top'
												    )); 	
		 		$section->addCompontnet( $component );
		$section_set->addSection( $section );
		// label section
 		$section = new SLFSection( array( 'name' => __( 'Input labels', 'wbk' ),
										  'description' => __( 'Booking form input labels', 'wbk' ),
										  'slug' => 'section_input_labels' ) );
				// padding 
		 		$component = new SLFPaddingMargin( array( 'name' => __( 'Padding', 'wbk' ),
		 												  'desc' => __( 'Input label padding', 'wbk' ),  
		 												  'slug' => 'labels_padding', 
		 												  'value' => '0 0 0 0',
		 												  'css_class' => 'wbk-input-label',
		 												  'css_prop' => 'padding'
		 												   )); 		
		 		$section->addCompontnet( $component );
		 		// margin
				$component = new SLFPaddingMargin( array( 'name' => __( 'Margin', 'wbk' ),
														  'desc' => __( 'Outer label margin', 'wbk' ),
														  'slug' => 'label_margin', 
														  'value' => '10px 0 2px 0',
		 												  'css_class' => 'wbk-input-label',
		 												  'css_prop' => 'margin'
		 												   ));  
		 		$section->addCompontnet( $component );
		 		// font color
				$component = new SLFColor( array( 'name' => __( 'Label color', 'wbk' ),
												   'desc' => __( 'Booking form input labels color', 'wbk' ),
												   'slug' => 'labels_color',
												   'value' => '#383838',
												   'css_class' => 'wbk-input-label',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// font size
				$component = new SLFSizePx(  array( 'name' => __( 'Label font size', 'wbk' ),
												   'desc' => __( 'Booking form input labels font size', 'wbk' ),
												   'slug' => 'labels_size',
												   'value' => '14px',
												   'css_class' => 'wbk-input-label',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );
		 		// font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Label font style', 'wbk' ),
												   'desc' => __( 'Booking form input labels font style', 'wbk' ),
												   'slug' => 'labels_font_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-input-label',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// font weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Label font weight', 'wbk' ),
												   'desc' => __( 'Booking form input labels font weight', 'wbk' ),
												   'slug' => 'labels_font_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-input-label',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
		$section_set->addSection( $section );
		// inputs section
 		$section = new SLFSection( array( 'name' => __( 'Inputs', 'wbk' ),
										  'description' => __( 'Booking form inputs', 'wbk' ),
										  'slug' => 'section_inputs' ) );
				// padding 
		 		$component = new SLFPaddingMargin( array( 'name' => __( 'Padding', 'wbk' ),
		 												  'desc' => __( 'Input padding', 'wbk' ),  
		 												  'slug' => 'input_padding', 
		 												  'value' => '0 5px 0 5px',
		 												  'css_class' => 'wbk-input',
		 												  'css_prop' => 'padding'
		 												   )); 		
		 		$section->addCompontnet( $component );
				// background color
				$component = new SLFColor( array( 'name' => __( 'Background color', 'wbk' ),
												   'desc' => __( 'Booking form input background color', 'wbk' ),
												   'slug' => 'input_background',
												   'value' => '#ffffff',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'background-color'
												    )); 
				$section->addCompontnet( $component );
		 		// font color
				$component = new SLFColor( array( 'name' => __( 'Input text color', 'wbk' ),
												   'desc' => __( 'Booking form input text color', 'wbk' ),
												   'slug' => 'input_color',
												   'value' => '#000000',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// font size
				$component = new SLFSizePx(  array( 'name' => __( 'Input font size', 'wbk' ),
												   'desc' => __( 'Booking form input font size', 'wbk' ),
												   'slug' => 'input_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );
		 		// font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Input font style', 'wbk' ),
												   'desc' => __( 'Booking form input font style', 'wbk' ),
												   'slug' => 'input_font_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// font weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Input font weight', 'wbk' ),
												   'desc' => __( 'Booking form input font weight', 'wbk' ),
												   'slug' => 'input_font_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
				// font size
				$component = new SLFSizePx(  array( 'name' => __( 'Input height', 'wbk' ),
												   'desc' => __( 'Booking form input height', 'wbk' ),
												   'slug' => 'inputs_height',
												   'value' => '45px',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'height'
												    )); 
				$section->addCompontnet( $component );
	 			// border
				$component = new SLFBorder( array( 'name' => __( 'Border', 'wbk' ),
												   'desc' => __( 'Input border', 'wbk' ),
												   'slug' => 'input_border',
												   'value' => '1px solid #c7c7c7',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'border'
												    )); 	
				$section->addCompontnet( $component );
				// font size
				$component = new SLFSizePx(  array( 'name' => __( 'Border radius', 'wbk' ),
												   'desc' => __( 'Booking form input border radius', 'wbk' ),
												   'slug' => 'inputs_border_radius',
												   'value' => '0',
												   'css_class' => 'wbk-input',
		 										   'css_prop' => 'border-radius'
												    )); 
				$section->addCompontnet( $component );
		$section_set->addSection( $section );
		// checkbox section
 		$section = new SLFSection( array( 'name' => __( 'Checkboxes', 'wbk' ),
										  'description' => __( 'Booking form checkboxes', 'wbk' ),
										  'slug' => 'section_checkboxes' ) );
				// checbox height
				$component = new SLFSizePx(  array('name' => __( 'Checkbox height', 'wbk' ),
												   'desc' => __( 'Booking form checkbox height', 'wbk' ),
												   'slug' => 'checkbox_height',
												   'value' => '45px',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'height'
												    )); 
				$section->addCompontnet( $component );
				// checbox width
				$component = new SLFSizePx(  array('name' => __( 'Checkbox width', 'wbk' ),
												   'desc' => __( 'Booking form checkbox width', 'wbk' ),
												   'slug' => 'checkbox_width',
												   'value' => '45px',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'width'
												    )); 
				$section->addCompontnet( $component );
				// marker line-height
				$component = new SLFSizePx(  array('name' => __( 'Checkbox marker line-height', 'wbk' ),
												   'desc' => __( 'Booking form marker line-height', 'wbk' ),
												   'slug' => 'checkbox_line_height',
												   'value' => '45px',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'line-height'
												    )); 
				$section->addCompontnet( $component );		
				// marker size
				$component = new SLFSizePx(  array('name' => __( 'Checkbox marker size', 'wbk' ),
												   'desc' => __( 'Booking form marker size', 'wbk' ),
												   'slug' => 'checkbox_marker_size',
												   'value' => '20px',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );		
				// checbox marker color
				$component = new SLFColor(  array('name' => __( 'Checkbox marker color', 'wbk' ),
												   'desc' => __( 'Booking form checkbox marker color', 'wbk' ),
												   'slug' => 'checkbox_marker_color',
												   'value' => '#757575',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );					
				// checbox background color
				$component = new SLFColor(  array('name' => __( 'Background color', 'wbk' ),
												   'desc' => __( 'Booking form checkbox background', 'wbk' ),
												   'slug' => 'checkbox_background',
												   'value' => '#ffffff',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'background-color'
												    )); 
				$section->addCompontnet( $component );
				// checbox border
				$component = new SLFBorder(  array('name' => __( 'Checkbox border', 'wbk' ),
												   'desc' => __( 'Booking form checkbox border', 'wbk' ),
												   'slug' => 'checkbox_border',
												   'value' => '1px solid #c7c7c7',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'border'
												    )); 
				$section->addCompontnet( $component );
				// checkbox border radius
				$component = new SLFSizePx(  array('name' => __( 'Checkbox border radius', 'wbk' ),
												   'desc' => __( 'Booking form checkbox border radius', 'wbk' ),
												   'slug' => 'checkbox_border_radius',
												   'value' => '0',
												   'css_class' => 'wbk-checkbox',
		 										   'css_prop' => 'border-radius'
												    )); 
				$section->addCompontnet( $component );	
		 		// checkbox label font color
				$component = new SLFColor( array( 'name' => __( 'Label color', 'wbk' ),
												   'desc' => __( 'Checkbox label color', 'wbk' ),
												   'slug' => 'checkbox_label_color',
												   'value' => '#383838',
												   'css_class' => 'wbk-checkbox-label',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// checkbox label font size
				$component = new SLFSizePx(  array( 'name' => __( 'Label font size', 'wbk' ),
												   'desc' => __( 'Checkbox label font size', 'wbk' ),
												   'slug' => 'checkbox_label_size',
												   'value' => '14px',
												   'css_class' => 'wbk-checkbox-label',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );
		 		// font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Label font style', 'wbk' ),
												   'desc' => __( 'Checkbox label font style', 'wbk' ),
												   'slug' => 'checkbox_label_font_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-checkbox-label',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// font weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Label font weight', 'wbk' ),
												   'desc' => __( 'Checkbox label font weight', 'wbk' ),
												   'slug' => 'checkbox_label_font_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-checkbox-label',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
		$section_set->addSection( $section );
		// buttons section
 		$section = new SLFSection( array( 'name' => __( 'Buttons', 'wbk' ),
										  'description' => __( 'Booking form buttons', 'wbk' ),
										  'slug' => 'section_buttons' ) );
		 		// margin
				$component = new SLFPaddingMargin( array( 'name' => __( 'Margin', 'wbk' ),
														  'desc' => __( 'Button margin', 'wbk' ),
														  'slug' => 'button_margin', 
														  'value' => '5px 0 0 0',
		 												  'css_class' => 'wbk-button',
		 												  'css_prop' => 'margin'
		 												   ));   		
				$section->addCompontnet( $component );		 		
		 		// height
				$component = new SLFSizePx(  array( 'name' => __( 'Height', 'wbk' ),
												   'desc' => __( 'Button height', 'wbk' ),
												   'slug' => 'button_height',
												   'value' => '45px',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'height'
												    )); 	
				$section->addCompontnet( $component );			
				// background color
				$component = new SLFColor(  array('name' => __( 'Background color', 'wbk' ),
												   'desc' => __( 'Button background color', 'wbk' ),
												   'slug' => 'button_background',
												   'value' => '#dbdbdb',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'background-color'
												    )); 
				$section->addCompontnet( $component );
				// text color
				$component = new SLFColor(  array('name' => __( 'Text color', 'wbk' ),
												   'desc' => __( 'Button text color', 'wbk' ),
												   'slug' => 'button_text_color',
												   'value' => '#000000',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
 		 		// border
				$component = new SLFBorder( array( 'name' => __( 'Border', 'wbk' ),
												   'desc' => __( 'Button border', 'wbk' ),
												   'slug' => 'button_border',
												   'value' => '1px solid #a6a6a6',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'border'
												    )); 		
		 		$section->addCompontnet( $component );
		 		// border radius
				$component = new SLFSizePx(  array( 'name' => __( 'Border radius', 'wbk' ),
												   'desc' => __( 'Button border radius', 'wbk' ),
												   'slug' => 'button_border_radius',
												   'value' => '0',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'border-radius'
												    )); 	
				$section->addCompontnet( $component );	
		 		// font size
				$component = new SLFSizePx(  array( 'name' => __( 'Button font size', 'wbk' ),
												   'desc' => __( 'Booking form button font size', 'wbk' ),
												   'slug' => 'button_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );
		 		// font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Button font style', 'wbk' ),
												   'desc' => __( 'Booking form button font style', 'wbk' ),
												   'slug' => 'button_font_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// font weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Button font weight', 'wbk' ),
												   'desc' => __( 'Booking form button font weight', 'wbk' ),
												   'slug' => 'labels_font_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-button',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
		$section_set->addSection( $section );
		// timeslots section
 		$section = new SLFSection( array( 'name' => __( 'Time slots', 'wbk' ),
										  'description' => __( 'Booking form time slots', 'wbk' ),
										  'slug' => 'section_timeslots' ) );
		 		// day title allign
				$component = new SLFTextAlign( array( 'name' => __( 'Day title align', 'wbk' ),
														  'desc' => '',
														  'slug' => 'day_title_align', 
														  'value' => 'left',
		 												  'css_class' => 'wbk-day-title',
		 												  'css_prop' => 'text-align'
		 												   ));   		
				$section->addCompontnet( $component );	
 				// Day title font size
				$component = new SLFSizePx(  array( 'name' => __( 'Day title font size', 'wbk' ),
												   'desc' => '',
												   'slug' => 'timeslots_day_title_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-day-title',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );	
				// Day title color
				$component = new SLFColor(  array('name' => __( 'Day title color', 'wbk' ),
												   'desc' => '',
												   'slug' => 'timeslot_day_title_color',
												   'value' => '#383838',
												   'css_class' => 'wbk-day-title',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// Day title font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Day title font style', 'wbk' ),
												   'desc' => '',
												   'slug' => 'timeslot_day_title_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-day-title',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// Day title weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Day title font weight', 'wbk' ),
												   'desc' => '',
												   'slug' => 'timeslot_day_title_weight',
												   'value' => 'bold',
												   'css_class' => 'wbk-day-title',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
				// Day title line-height
				$component = new SLFSizePx(  array( 'name' => __( 'Day title line height', 'wbk' ),
												   'desc' => '',
												   'slug' => 'timeslots_day_title_line_height',
												   'value' => '36px',
												   'css_class' => 'wbk-day-title',
		 										   'css_prop' => 'line-height'
												    )); 
				$section->addCompontnet( $component );	
  		 		// margin
				$component = new SLFPaddingMargin( array( 'name' => __( 'Margin', 'wbk' ),
														  'desc' => __( 'Time slot block margin', 'wbk' ),
														  'slug' => 'timeslot_margin', 
														  'value' => '5px 5px 5px 5px',
		 												  'css_class' => 'wbk-slot-inner',
		 												  'css_prop' => 'margin'
		 												   ));   		
				$section->addCompontnet( $component );	
  		 		// padding
				$component = new SLFPaddingMargin( array( 'name' => __( 'Padding', 'wbk' ),
														  'desc' => __( 'Time slot block padding', 'wbk' ),
														  'slug' => 'timeslot_padding', 
														  'value' => '0 0 0 0',
		 												  'css_class' => 'wbk-slot-inner',
		 												  'css_prop' => 'padding'
		 												   ));   		
				$section->addCompontnet( $component );	
				// border
				$component = new SLFBorder( array( 'name' => __( 'Border', 'wbk' ),
												   'desc' => __( 'Time slot block border', 'wbk' ),
												   'slug' => 'timeslot_border',
												   'value' => '1px solid #a6a6a6',
												   'css_class' => 'wbk-slot-inner',
		 										   'css_prop' => 'border'
												    )); 
				$section->addCompontnet( $component );	
				// border radius
				$component = new SLFSizePx(  array( 'name' => __( 'Border radius', 'wbk' ),
												   'desc' => __( 'Time slot border radius', 'wbk' ),
												   'slug' => 'timeslots_border_radius',
												   'value' => '0',
												   'css_class' => 'wbk-slot-inner',
		 										   'css_prop' => 'border-radius'
												    )); 
				$section->addCompontnet( $component );	
				// background color
				$component = new SLFColor(  array('name' => __( 'Background color', 'wbk' ),
												   'desc' => __( 'Time slot background color', 'wbk' ),
												   'slug' => 'timeslot_background',
												   'value' => '#dbdbdb',
												   'css_class' => 'wbk-slot-inner',
		 										   'css_prop' => 'background-color'
												    )); 
				$section->addCompontnet( $component );
				// time font size
				$component = new SLFSizePx(  array( 'name' => __( 'Time font size', 'wbk' ),
												   'desc' => __( 'Time slot time font size', 'wbk' ),
												   'slug' => 'timeslots_time_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-slot-time',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );	
				// time color
				$component = new SLFColor(  array('name' => __( 'Time color', 'wbk' ),
												   'desc' => __( 'Time slot time color', 'wbk' ),
												   'slug' => 'timeslot_time_color',
												   'value' => '#000000',
												   'css_class' => 'wbk-slot-time',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// time font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Time font style', 'wbk' ),
												   'desc' => __( 'Time slot time font style', 'wbk' ),
												   'slug' => 'timeslot_time_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-slot-time',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// font weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Time font weight', 'wbk' ),
												   'desc' => __( 'Time slot time font weight', 'wbk' ),
												   'slug' => 'timeslot_time_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-slot-time',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
				// time line-height
				$component = new SLFSizePx(  array( 'name' => __( 'Time line height', 'wbk' ),
												   'desc' => __( 'Time slot time line height', 'wbk' ),
												   'slug' => 'timeslots_time_line_height',
												   'value' => '24px',
												   'css_class' => 'wbk-slot-time',
		 										   'css_prop' => 'line-height'
												    )); 
				$section->addCompontnet( $component );	
 				// available font size
				$component = new SLFSizePx(  array( 'name' => __( 'Available font size', 'wbk' ),
												   'desc' => __( 'Time slot time available size', 'wbk' ),
												   'slug' => 'timeslots_available_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-slot-available',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );	
				// available color
				$component = new SLFColor(  array('name' => __( 'Available color', 'wbk' ),
												   'desc' => __( 'Time slot available color', 'wbk' ),
												   'slug' => 'timeslot_available_color',
												   'value' => '#000000',
												   'css_class' => 'wbk-slot-available',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// available font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Available font style', 'wbk' ),
												   'desc' => __( 'Time slot available font style', 'wbk' ),
												   'slug' => 'timeslot_available_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-slot-available',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// available weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Available font weight', 'wbk' ),
												   'desc' => __( 'Time slot available font weight', 'wbk' ),
												   'slug' => 'timeslot_available_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-slot-available',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
				// available line-height
				$component = new SLFSizePx(  array( 'name' => __( 'Available line height', 'wbk' ),
												   'desc' => __( 'Time slot available line height', 'wbk' ),
												   'slug' => 'timeslots_available_line_height',
												   'value' => '24px',
												   'css_class' => 'wbk-slot-available',
		 										   'css_prop' => 'line-height'
												    )); 
				$section->addCompontnet( $component );	
		 		// button
				$component = new SLFPaddingMargin( array( 'name' => __( 'Button margin', 'wbk' ),
														  'desc' => __( 'Time slot button margin', 'wbk' ),
														  'slug' => 'timeslot_button_margin', 
														  'value' => '0 0 0 0',
		 												  'css_class' => 'wbk-slot-button',
		 												  'css_prop' => 'margin'
		 												   ));   		
				$section->addCompontnet( $component );	
		 		// button background
				$component = new SLFColor( array( 'name' => __( 'Button background', 'wbk' ),
														  'desc' => __( 'Time slot button background', 'wbk' ),
														  'slug' => 'timeslot_button_background', 
														  'value' => '#474747',
		 												  'css_class' => 'wbk-slot-button',
		 												  'css_prop' => 'background-color'
		 												   ));  
				$section->addCompontnet( $component );	
		 		// border
				$component = new SLFBorder( array( 'name' => __( 'Button border', 'wbk' ),
												   'desc' => __( 'Time slot button border', 'wbk' ),
												   'slug' => 'timeslot_button_border',
												   'value' => '1px solid #474747',
												   'css_class' => 'wbk-slot-button',
		 										   'css_prop' => 'border'
												    )); 		
		 		$section->addCompontnet( $component );
				// button color
				$component = new SLFColor( array( 'name' => __( 'Button color', 'wbk' ),
														  'desc' => __( 'Time slot button color', 'wbk' ),
														  'slug' => 'timeslot_button_color', 
														  'value' => '#ffffff',
		 												  'css_class' => 'wbk-slot-button',
		 												  'css_prop' => 'color'
		 												   ));  
				$section->addCompontnet( $component );	
		 		// active button background
				$component = new SLFColor( array( 'name' => __( 'Active button background', 'wbk' ),
														  'desc' => __( 'Time slot active button background', 'wbk' ),
														  'slug' => 'timeslot_active_button_background', 
														  'value' => '#474747',
		 												  'css_class' => 'wbk-slot-active-button',
		 												  'css_prop' => 'background-color'
		 												   ));  
				$section->addCompontnet( $component );	
				// active button color
				$component = new SLFColor( array( 'name' => __( 'Active button color', 'wbk' ),
														  'desc' => __( 'Time slot active button color', 'wbk' ),
														  'slug' => 'timeslot_active_button_color', 
														  'value' => '#ffffff',
		 												  'css_class' => 'wbk-slot-active-button',
		 												  'css_prop' => 'color'
		 												   ));  
				$section->addCompontnet( $component );	
				// button font size
				$component = new SLFSizePx(  array( 'name' => __( 'Button font size', 'wbk' ),
												   'desc' => __( 'Time slot button font size', 'wbk' ),
												   'slug' => 'timeslots_button_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-slot-button',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );	
				// button line height
				$component = new SLFSizePx( array( 'name' => __( 'Button line height', 'wbk' ),
														  'desc' => __( 'Time slot button line height', 'wbk' ),
														  'slug' => 'timeslot_button_lineheight', 
														  'value' => '24px',
		 												  'css_class' => 'wbk-slot-button',
		 												  'css_prop' => 'line-height'
		 												   ));  
				$section->addCompontnet( $component );	
		 		// button font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Button font style', 'wbk' ),
												   'desc' => __( 'Time slot button font style', 'wbk' ),
												   'slug' => 'timeslot_button_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-slot-button',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// button weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Button font weight', 'wbk' ),
												   'desc' => __( 'Time slot button font weight', 'wbk' ),
												   'slug' => 'timeslot_button_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-slot-button',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
		$section_set->addSection( $section );
		// customer details section
 		$section = new SLFSection( array( 'name' => __( 'Customer details', 'wbk' ),
										  'description' => __( 'Booking form customer details', 'wbk' ),
										  'slug' => 'section_details' ) );
		 		// title align
				$component = new SLFTextAlign( array( 'name' => __( 'Title align', 'wbk' ),
														  'desc' => __( 'Customer details title align', 'wbk' ),
														  'slug' => 'details_title_align', 
														  'value' => 'left',
		 												  'css_class' => 'wbk-details-title',
		 												  'css_prop' => 'text-align'
		 												   ));   		
				$section->addCompontnet( $component );	
 				// title font size
				$component = new SLFSizePx(  array( 'name' => __( 'Title font size', 'wbk' ),
												   'desc' => __( 'Customer details title font size', 'wbk' ),
												   'slug' => 'details_title_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-details-title',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );	
				// title color
				$component = new SLFColor(  array('name' => __( 'Title color', 'wbk' ),
												   'desc' => __( 'Customer details title color', 'wbk' ),
												   'slug' => 'details_title_color',
												   'value' => '#383838',
												   'css_class' => 'wbk-details-title',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// title font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Title font style', 'wbk' ),
												   'desc' => __( 'Customer details title font style', 'wbk' ),
												   'slug' => 'details_title_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-details-title',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		//   title weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Title font weight', 'wbk' ),
												   'desc' => __( 'Customer details title font weight', 'wbk' ),
												   'slug' => 'details_title_weight',
												   'value' => 'bold',
												   'css_class' => 'wbk-details-title',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
				// title line-height
				$component = new SLFSizePx(  array( 'name' => __( 'Details title line height', 'wbk' ),
												   'desc' => __( 'Customer details title line height', 'wbk' ),
												   'slug' => 'details_title_line_height',
												   'value' => '36px',
												   'css_class' => 'wbk-details-title',
		 										   'css_prop' => 'line-height'
												    )); 
				$section->addCompontnet( $component );	
		 		// title align
				$component = new SLFTextAlign( array( 'name' => __( 'Sub-title align', 'wbk' ),
														  'desc' => __( 'Customer details sub-title align', 'wbk' ),
														  'slug' => 'details_sub_title_align', 
														  'value' => 'left',
		 												  'css_class' => 'wbk-details-sub-title',
		 												  'css_prop' => 'text-align'
		 												   ));   		
				$section->addCompontnet( $component );	
 				// title font size
				$component = new SLFSizePx(  array( 'name' => __( 'Sub-title font size', 'wbk' ),
												   'desc' => __( 'Customer details sub-title font size', 'wbk' ),
												   'slug' => 'details_sub_title_font_size',
												   'value' => '14px',
												   'css_class' => 'wbk-details-sub-title',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );	
				// title color
				$component = new SLFColor(  array('name' => __( 'Sub-title color', 'wbk' ),
												   'desc' => __( 'Customer details sub-title color', 'wbk' ),
												   'slug' => 'details_sub_title_color',
												   'value' => '#383838',
												   'css_class' => 'wbk-details-sub-title',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// title font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Sub-title font style', 'wbk' ),
												   'desc' => __( 'Customer details sub-title font style', 'wbk' ),
												   'slug' => 'details_sub_title_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-details-sub-title',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		//title weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Sub-title font weight', 'wbk' ),
												   'desc' => __( 'Customer details sub-title font weight', 'wbk' ),
												   'slug' => 'details_sub_title_weight',
												   'value' => 'bold',
												   'css_class' => 'wbk-details-sub-title',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
				// title line-height
				$component = new SLFSizePx(  array( 'name' => __( 'Details sub-title line height', 'wbk' ),
												   'desc' => __( 'Customer details sub-title line height', 'wbk' ),
												   'slug' => 'details_sub_title_line_height',
												   'value' => '36px',
												   'css_class' => 'wbk-details-sub-title',
		 										   'css_prop' => 'line-height'
												    )); 
				$section->addCompontnet( $component );	
		$section_set->addSection( $section );
		$this->sections_sets[$section_set->slug] = $section_set;
 		add_option( $this->slug, $this );
	}
	public function save(){
		update_option( $this->slug, $this );
	}
	public function renderSectionSet( $slug ){
		$html  = '<div class="slf-wrap">';
			$html .= '<div class="slf_overlay"></div>';
			$html .= '<div class="slf-menu">';
				$html .=  $this->renderSectionSetMenu( $slug );
			$html .= '</div>';		 
		$html .= '<div class="slf-right">';
			$html .= '<div class="slf-bar-top">';
				$html .=  $this->renderSectionSetControls( $slug );
			$html .= '</div>';
			$html .= '<div  id="slf-console">';
 			$html .= '</div>';		 
			$html .= '<div class="slf-content" id="slf-sections">';
		        $html .=  $this->sections_sets[ $slug ]->render();		 
			$html .= '</div>';		 
		$html .= '</div>';
		$html .= '<div class="slf-clear"></div>';
		$html .= '</div>';
		$html .= '<div id="slf-preview">';
		$html .= $this->renderPreview(  $slug );
		$html .= '</div>';
		return $html;
	}
	public function renderPreview( $slug ){
 		$html =  '<div class="wbk-outer-container">
					<div class="wbk-inner-container">
						<div class="wbk-frontend-row">
							<div class="wbk-col-12-12" id="wbk-service-id">
								<label class="wbk-input-label">Select emloyee</label>
								<select class="wbk-select wbk-input">
									<option value="John Smith">John Smith</option>
								</select>
							</div>
							<hr class="wbk-separator"/>
						</div>
						<div class="wbk-frontend-row" id="wbk-date-container">	
							<div class="wbk-col-12-12" id="wbk-service-id">	
						 		<label class="wbk-input-label">Select date</label>
								<input type="text" class="wbk-input"/>
							</div>		
							<hr class="wbk-separator"/>
						</div>	
						<div class="wbk-frontend-row" id="timeselect_row">	
							<div class="wbk-col-12-12">	
								<label class="wbk-input-label">Tell us your availibilities</label>
								<hr class="wbk-hours-separator"/>		
									<div class="wbk-frontend-row" >
				 				 		<div class="wbk-col-3-12 wbk-table-cell">
											<input type="checkbox" value="tuesday" class="wbk-checkbox" id="wbk-day_tuesday" checked="checked">
											<label  for="wbk-day_tuesday" class="wbk-checkbox-label">
												Tuesday
											</label>
										</div>						 
				 				 		<div class="wbk-col-9-12">					 
											<select id="wbk-time_tuesday" class="wbk-input wbk-width-100 wbk-time_after">
												<option value="32400">from 9:00 am</option>
												<option value="36000">from 10:00 am</option>
											</select>
										</div>	
										<div style="clear:both"></div>		
									</div>	
									<div class="wbk-frontendrow" >
				 				 		<div class="wbk-col-3-12">
											<input type="checkbox" value="tuesday" class="wbk-checkbox" id="wbk-day_tuesday" checked="checked">
											<label for="wbk-day_tuesday" class="wbk-checkbox-label">
												 Friday
											</label>
										</div>						 
				 				 		<div class="wbk-col-9-12">					 
											<select id="wbk-time_tuesday" class="wbk-input wbk-width-100 wbk-time_after">
												<option value="32400">from 9:00 am</option>
												<option value="36000">from 10:00 am</option>
											</select>
										</div>	
										<div style="clear:both"></div>	
										<input type="button" class="wbk-button" id="wbk-search_time_btn" value="Search time slots">	
									</div>	
							 				  				 
				 			</div>		
						</div>			
						<div class="wbk-frontend-row" id="wbk-slots-container">	
							<div class="wbk-col-12-12">	
								<div class="wbk-day-title">
									15 April 16
								</div>
								<hr class="wbk-day-separator"/>		

							</div>	
							<div class="wbk-col-12-12 wbk-text-center" >
								<ul class="wbk-timeslot-list">				
									<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button" />		 	
				 						</div>
				 					</li>
				 					<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button wbk-slot-active-button" />		 	
				 						</div>
				 					</li>
				 		 					<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button" />		 	
				 						</div>
				 					</li>
				 		 					<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button" />		 	
				 						</div>
				 					</li>
				 		 					<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button" />		 	
				 						</div>
				 					</li>
				 		 					<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button" />		 	
				 						</div>
				 					</li>
				 		 					<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button" />		 	
				 						</div>
				 					</li>
				 		 					<li class="wbk-col-4-6-12">
										<div class="wbk-slot-inner">					
											<div class="wbk-slot-time">
												10pm	
											</div>
											<div class="wbk-slot-available">
												1 seat available
											</div>
											<input type="button" value="Book" class="wbk-slot-button" />		 	
				 						</div>
				 					</li>
				 		 		 
				 
								</ul>
							</div>	
							<hr class="wbk-separator"/>		
				 		</div>
				 		<div class="wbk-frontend-row" id="wbk-booking-form-container">
							<div class="wbk-col-12-12">									 
								<div class="wbk-details-sub-title">
				 					Fill in a form
				 				</div>
								<hr class="wbk-form-separator"/>		

				 		 		<label class="wbk-input-label">Full name</label>
								<input type="text" class="wbk-input"/>
				 		 		<label class="wbk-input-label">E-mail</label>
								<input type="text" class="wbk-input"/>
				  		 		<label class="wbk-input-label">Phone number</label>
								<input type="text" class="wbk-input"/>
				 		 		<label class="wbk-input-label">Comment</label>
								<input type="text" class="wbk-input"/>
								<input type="button" class="wbk-button" id="wbk-book_appointment" value="Book">	
				 		 	</div>
				 		</div>
					</div>
				</div>';
		return $html;
	}
	public function renderSectionSetMenu( $slug ){
		return $this->sections_sets[ $slug ]->renderMenu();		 
	}
	public function renderSectionSetControls( $slug ){
		$html = '<a class="button" id="wbk-preview-btn" href="javascript:show_prview()">Show preview</a>'; 
		$html .= '<input  value="'. __( 'Save options', 'w' ) . '" type="button" class="button slf-save-button" onclick = "slf_save_sections_set( \'' . $this->slug . '\',  \'' . $slug . '\')">';
		 
		$presets_folder =  WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'webba-booking-lite' . DIRECTORY_SEPARATOR . 'presets' . DIRECTORY_SEPARATOR.'*';

  		$select = '<select id="presets_list" class="slf_right" >';
 		foreach( glob( $presets_folder ) as $file) {    
			    $objData = file_get_contents( $file );
				$data = json_decode( $objData, true );
  				$select .= '<option value="' . $file . '" >' . $data['name'] . '</option>';
 		}
 		$select .= '</select>';
  		$select .= '<label class="slf_right slf_control_label" for="presets_list">Presets: </label>';
		$html .= '<input  value="'. __( 'Load presets', 'wbk' ) . '" type="button" class="button slf-deserialize-button slf_right" onclick = "slf_load_presets( \'' . $this->slug . '\',  \'' . $slug . '\')">';
 		$html .= $select;
		// $html .= $this->adminFunctions( $slug );
     	return $html;
	}
	public function adminFunctions( $slug ){
		$html  = '<p>';
		$html .= 'Developers functions:   </br>';
		$html .= '<input style="width:230px;height: 27px;" name="serial_section_name" id="serial_section_name" placeholder="section name: for ex. Preset 1"/>';
		$html .= '<input  value="'. __( 'Serialize options', 'wbk' ) . '" type="button" class="button slf-serialize-button" onclick = "slf_serialize_sections_set( \'' . $this->slug . '\',  \'' . $slug . '\')">';
		$html .= '</p>';
		return $html;
	}
	public function update_3_4_0(){		 	
		if( isset( $this->sections_sets[ 'wbk_extended_appearance_options' ] ) ) {	
			if( !$this->sections_sets[ 'wbk_extended_appearance_options' ]->hasSection( 'multi_service' ) ){
				// multi service section
		 		$section = new SLFSection( array( 'name' => __( 'Multi-Service', 'wbk' ),
										          'description' => __( 'Options for multi service mode', 'wbk' ),
								          		  'slug' => 'multi_service' ) );
		 		// title align 
				$component = new SLFTextAlign( array( 'name' => __( 'Title align', 'wbk' ),
													  'desc' => __( 'Service title text align', 'wbk' ),
													  'slug' => 'multi_service_title_align', 
													  'value' => 'left',
		 											  'css_class' => 'wbk-multiple-service-title',
		 											  'css_prop' => 'text-align'
		 											   ));   		
				$section->addCompontnet( $component );	
				// padding 
		 		$component = new SLFPaddingMargin( array( 'name' => __( 'Padding', 'wbk' ),
		 												  'desc' => __( 'Service title text padding', 'wbk' ),  
		 												  'slug' => 'multi_service_padding', 
		 												  'value' => '0 0 0 0',
		 												  'css_class' => 'wbk-multiple-service-title',
		 												  'css_prop' => 'padding'
		 												   )); 		
		 		$section->addCompontnet( $component );
		 		// margin
				$component = new SLFPaddingMargin( array( 'name' => __( 'Margin', 'wbk' ),
														  'desc' => __( 'Service title text margin', 'wbk' ),
														  'slug' => 'multi_service_margin', 
														  'value' => '10px 0 2px 0',
		 												  'css_class' => 'wbk-multiple-service-title',
		 												  'css_prop' => 'margin'
		 												   ));  
		 		$section->addCompontnet( $component );
		 		// font color
				$component = new SLFColor( array( 'name' => __( 'Color', 'wbk' ),
												   'desc' => __( 'Service title text color', 'wbk' ),
												   'slug' => 'multi_service_color',
												   'value' => '#383838',
												   'css_class' => 'wbk-multiple-service-title',
		 										   'css_prop' => 'color'
												    )); 
				$section->addCompontnet( $component );
		 		// font size
				$component = new SLFSizePx(  array( 'name' => __( 'Font size', 'wbk' ),
												   'desc' => __( 'Service title font size', 'wbk' ),
												   'slug' => 'multi_service_fort_size',
												   'value' => '14px',
												   'css_class' => 'wbk-multiple-service-title',
		 										   'css_prop' => 'font-size'
												    )); 
				$section->addCompontnet( $component );
		 		// font style
				$component = new SLFFontStyle(  array( 'name' => __( 'Font style', 'wbk' ),
												   'desc' => __( 'Service title Font style', 'wbk' ),
												   'slug' => 'multi_service_font_style',
												   'value' => 'normal',
												   'css_class' => 'wbk-multiple-service-title',
		 										   'css_prop' => 'font-style'
												    )); 
				$section->addCompontnet( $component );
		 		// font weight
				$component = new SLFFontWeight(  array( 'name' => __( 'Font weight', 'wbk' ),
												   'desc' => __( 'Service title font weight', 'wbk' ),
												   'slug' => 'multi_service_font_weight',
												   'value' => 'normal',
												   'css_class' => 'wbk-multiple-service-title',
		 										   'css_prop' => 'font-weight'
												    )); 
				$section->addCompontnet( $component );
				$this->sections_sets[ 'wbk_extended_appearance_options' ]->addSection( $section );
			}
		}
	}
}
endif;