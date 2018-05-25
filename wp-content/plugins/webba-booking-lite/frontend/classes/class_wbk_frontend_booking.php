<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Frontend_Booking {
	public function __construct() {
		// add shortcode
		add_shortcode( 'webba_booking' , array( $this, 'shotrcodeBooking' ) ); 
		add_shortcode( 'webba_feature_appointmens' , array( $this, 'shotrcodeFeatureAppointments' ) ); 
		add_shortcode( 'webba_email_landing' , array( $this, 'shotrcodeEmailLanding' ) ); 
		add_shortcode( 'webba_multi_service_booking' , array( $this, 'shotrcodeMultiServiceBooking' ) ); 

 		// init scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts') );
		// param process
		add_action ('wp_loaded', array( $this, 'paramProcessing' ) );
	    // render services in multiple mode
		add_action('wbk_render_multi_service_services', array( $this, 'render_multi_service_services' ) );
		add_filter('wbk_pre_render_multi_service_services', array( $this, 'pre_render_multi_service_services' ), 10, 2 );

		// Webba Booking native frontent actions
		// frontend category list 
		add_action( 'wbk_render_frontend_category_list', array( $this, 'render_frontend_category_list') );
		add_filter( 'wbk_pre_render_frontend_category_list', array( $this, 'pre_render_frontend_category_list') );


	} 
	public function paramProcessing() {
		// check if called as payment result
	    if( isset( $_GET['pp_aprove'] ) ){
	    	if ( $_GET['pp_aprove'] == 'true' ){
	    		if ( isset( $_GET['paymentId'] ) && isset( $_GET['PayerID'] ) ){
	    			$paymentId = $_GET['paymentId'];
	    			$PayerID = $_GET['PayerID'];
	    			$paypal = new WBK_PayPal();
	    			$init_result = $paypal->init( false );
	    			if ( $init_result === FALSE ){   				    				 
	    			 	wp_redirect( get_permalink() . '?paypal_status=2'  );
					 	exit;
	    			} else {
	    				$execResult = $paypal->executePayment( $paymentId, $PayerID );
	    				if( $execResult === false ){
	    					wp_redirect( get_permalink() . '?paypal_status=3' );
							exit;
	    				} else {
	    					$pp_redirect_url = trim( get_option( 'wbk_paypal_redirect_url', '' ) );
	    					if( $pp_redirect_url != '' ){
								if( filter_var( $pp_redirect_url, FILTER_VALIDATE_URL ) !== FALSE) {
									wp_redirect( $pp_redirect_url );
									exit;
								}
	    					}
	    					wp_redirect( get_permalink() . '?paypal_status=1' );
							exit;
	    				}
	    			}
	    		} else {  			 
		   			wp_redirect( get_permalink() . '?paypal_status=4' );
					exit;
	    		}
	    	} elseif( $_GET['pp_aprove'] == 'false' ) {
				if( isset( $_GET['cancel_token'] ) ){
					$cancel_token =  $_GET['cancel_token'];
					$cancel_token = str_replace('"', '', $cancel_token );
					$cancel_token = str_replace('<', '', $cancel_token );
					$cancel_token = str_replace('\'', '', $cancel_token );
					$cancel_token = str_replace('>', '', $cancel_token );
					$cancel_token = str_replace('/', '', $cancel_token );
					$cancel_token = str_replace('\\',  '', $cancel_token );			 
					WBK_Db_Utils::clearPaymentIdByToken( $cancel_token );
					
				}
				wp_redirect( get_permalink() . '?paypal_status=5' );
				exit;
	    	}
		} 
 	}
	public function render( $template, $data ){
		// load and output view template
		ob_start();
        ob_implicit_flush(0);
		try {
             include  dirname(__FILE__) . '/../templates/tpl_wbk_frontend_' . $template . '.php';
        } catch (Exception $e) {
        	ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
	}
	public function shotrcodeBooking( $attr ) {
		extract( shortcode_atts( array( 'service' => '0' ), $attr ) );
		extract( shortcode_atts( array( 'category' => '0' ), $attr ) );
		extract( shortcode_atts( array( 'category_list' => '0' ), $attr ) );

		$data = array();
		$data[0] = $service;
		$data[1] = $category;
		$data[3] = $category_list;
		return $this->render( 'booking_ui', $data );
	}
	public function shotrcodeMultiServiceBooking( $attr ) {		 
		extract( shortcode_atts( array( 'category' => '0', 'skip_services' => '0' ), $attr ) );

		$data = array();
		$data[0] = $category;
		$data[1] = $skip_services;
		 
		return $this->render( 'multserv_booking_ui', $data );
	}
	public function shotrcodeFeatureAppointments( $attr ) {
		extract( shortcode_atts( array( 'service' => '0' ), $attr ) );
		extract( shortcode_atts( array( 'category' => '0' ), $attr ) );
		if( is_numeric( $service) && $service != 0 ){
			$data = array();
			$data[0] = $service;
			return $this->render( 'feature_appointments', $data );
		}
		if( is_numeric( $category) && $category != 0 ){
			$data = array();
			$data[0] = $category;
			return $this->render( 'feature_appointments_category', $data );
		}	
		return '';
	}
	public function shotrcodeEmailLanding( $attr ) {
		extract( shortcode_atts( array( 'service' => '0' ), $attr ) );
		if( !is_numeric( $service) ){
			return;
		}
		$data = array();
		$data[0] = $service;
		return $this->render( 'landing', $data );
	}
	public function enqueueScripts() {
 		global $wbk_wording;
		if( $this->has_shortcode( 'webba_booking' ) ) {

			wp_enqueue_script( 'wbk-validator', plugins_url( '../common/wbk-validator.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );   

			if( get_option('wbk_phone_mask', 'enabled') == 'enabled' ){
				wp_enqueue_script( 'jquery-maskedinput', plugins_url( '../common/jquery.maskedinput.min.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );  
			} elseif( get_option('wbk_phone_mask', 'enabled') == 'enabled_mask_plugin' ){
				wp_enqueue_script( 'jquery-maskedinput', plugins_url( '../common/jquery.mask.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );  
			}		
			wp_enqueue_script( 'jquery-effects-fade' ); 
			if( get_option( 'wbk_jquery_nc', 'disabled' ) == 'disabled' ){
			    wp_enqueue_script( 'wbk-frontend', plugins_url( 'js/wbk-frontend.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ), '3.3.51' );	 
			} elseif( get_option( 'wbk_jquery_nc', 'disabled' ) == 'enabled' ){
				wp_enqueue_script( 'wbk-frontend', plugins_url( 'js/wbk-frontend-nc.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ), '3.3.51' );	 
			}
			if( get_option( 'wbk_pickadate_load', 'yes' ) == 'yes' ){
			    wp_enqueue_script( 'picker', plugins_url( 'js/picker.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );
			    wp_enqueue_script( 'picker-date', plugins_url( 'js/picker.date.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );
			    wp_enqueue_script( 'picker-legacy', plugins_url( 'js/legacy.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );
			}
	 		wp_enqueue_style( 'picker-default', plugins_url( 'css/default.css', dirname( __FILE__ ) ) );
	 		wp_enqueue_style( 'picker-default-date', plugins_url( 'css/default.date.css', dirname( __FILE__ ) ) );

	 		wp_enqueue_style( 'wbk-frontend-style-custom', plugins_url( 'css/wbk-frontend-custom-style.css', dirname( __FILE__ ) ), array(), null );
	 		wp_enqueue_style( 'wbk-frontend-style', plugins_url( 'css/wbk-frontend-default-style.css', dirname( __FILE__ ) ), array(), null );



	 		$startOfWeek = get_option( 'wbk_start_of_week', 'monday' );
	 		if ( $startOfWeek == 'monday' ){
	 			$startOfWeek = true;
	 		} else{
	 			$startOfWeek = false;
	 		}
	 		$select_date_extended_label = get_option( 'wbk_date_extended_label', '' );
	 		if ( $select_date_extended_label ==  '' ) {
	 			$select_date_extended_label =  sanitize_text_field( $wbk_wording['date_extended_label'] );
	 		}
	 		$select_date_basic_label = get_option( 'wbk_date_basic_label', '' );
	 		if ( $select_date_basic_label ==  '' ) {
	 			$select_date_basic_label = sanitize_text_field( $wbk_wording['date_basic_label'] );
	 		}	 	 
	 		$select_slots_label = get_option( 'wbk_slots_label', '' );
	 		if ( $select_slots_label ==  '' ) {
	 			$select_slots_label =  sanitize_text_field( $wbk_wording['slots_label'] );
	 		}
			$thanks_message = get_option( 'wbk_book_thanks_message', '' );
	 		if ( $thanks_message ==  '' ) {
	 			$thanks_message = sanitize_text_field( $wbk_wording['book_thanks_message'] );	
	 		}
	 		$select_date_placeholder = get_option( 'wbk_date_input_placeholder', '' );
	 		if ( $select_date_placeholder == '' ){
	 			$select_date_placeholder = sanitize_text_field( $wbk_wording['date_input_placeholder'] );
	 		}
	 		$booked_text = get_option( 'wbk_booked_text',  '' );
	 		if ( $booked_text == '' ){
	 			$booked_text = sanitize_text_field( $wbk_wording['booked_text'] );
	 		}

			// Localize the script with new data
			$checkout_label = get_option( 'wbk_checkout_button_text', '' );
			if( $checkout_label == '' ){
				$checkout_label = sanitize_text_field( $wbk_wording['checkout'] );
			}
			$checkout_label = str_replace( '#selected_count', '<span class="wbk_multi_selected_count"></span>', $checkout_label );
			$checkout_label = str_replace( '#total_count', '<span class="wbk_multi_total_count"></span>', $checkout_label );

			$translation_array = array(
				'mode' => get_option( 'wbk_mode', 'extended' ),		
				'phonemask' => get_option( 'wbk_phone_mask', 'enabled' ),
				'phoneformat' => get_option( 'wbk_phone_format', '(999) 999-9999' ),	
				'ajaxurl' => admin_url( 'admin-ajax.php'),
				'selectdatestart' => $select_date_extended_label,
				'selectdatestartbasic' => $select_date_basic_label,
				'selecttime' => $select_slots_label, 
				'selectdate' => $select_date_placeholder,			
				'thanksforbooking' =>  $thanks_message,
				'january' => __( 'January', 'wbk' ),
				'february' => __( 'February', 'wbk' ),
				'march' => __( 'March', 'wbk' ),
				'april' => __( 'April', 'wbk' ),
				'may' => __( 'May', 'wbk' ),
				'june' => __( 'June', 'wbk' ),
				'july' => __( 'July', 'wbk' ),
				'august' => __( 'August', 'wbk' ),
				'september' => __( 'September', 'wbk' ),
				'october' => __( 'October', 'wbk' ),
				'november' => __( 'November', 'wbk' ),
				'december' => __( 'December', 'wbk' ),
				'jan' =>  __( 'Jan', 'wbk' ),  
				'feb' =>  __( 'Feb', 'wbk' ),  
				'mar' =>  __( 'Mar', 'wbk' ),  
				'apr' =>  __( 'Apr', 'wbk' ),  
				'mays' =>  __( 'May', 'wbk' ), 
				'jun' =>  __( 'Jun', 'wbk' ), 
				'jul' =>  __( 'Jul', 'wbk' ), 
				'aug' =>  __( 'Aug', 'wbk' ), 
				'sep' =>  __( 'Sep', 'wbk' ), 
				'oct' =>  __( 'Oct', 'wbk' ), 
				'nov' =>  __( 'Nov', 'wbk' ), 
				'dec' =>  __( 'Dec', 'wbk' ), 
				'sunday' =>  __( 'Sunday', 'wbk' ), 
				'monday' =>  __( 'Monday', 'wbk' ), 
				'tuesday' =>  __( 'Tuesday', 'wbk' ), 
				'wednesday' =>  __( 'Wednesday', 'wbk' ), 
				'thursday' =>  __( 'Thursday', 'wbk' ), 
				'friday' =>  __( 'Friday', 'wbk' ), 
				'saturday' =>  __( 'Saturday', 'wbk' ), 
				'sun' =>  __( 'Sun', 'wbk' ), 
				'mon' =>  __( 'Mon', 'wbk' ), 
				'tue' =>  __( 'Tue', 'wbk' ), 
				'wed' =>  __( 'Wed', 'wbk' ), 
				'thu' =>  __( 'Thu', 'wbk' ), 
				'fri' =>  __( 'Fri', 'wbk' ), 
				'sat' =>  __( 'Sat', 'wbk' ), 
				'today' =>  __( 'Today', 'wbk' ), 
				'clear' =>  __( 'Clear', 'wbk' ), 
				'close' =>  __( 'Close', 'wbk' ),
				'startofweek' => $startOfWeek,
				'nextmonth' => __( 'Next month', 'wbk' ),
				'prevmonth'=> __( 'Previous  month', 'wbk' ),
				'hide_form' => get_option( 'wbk_hide_from_on_booking', 'disabled' ),
				'booked_text' => $booked_text,
				'show_booked'  => get_option( 'wbk_show_booked_slots', 'disabled' ),
				'multi_booking'  => get_option( 'wbk_multi_booking', 'disabled' ),
				'checkout'  => $checkout_label,
				'multi_limit'  => get_option( 'wbk_multi_booking_max', '' ),
				'multi_limit_default'  => get_option( 'wbk_multi_booking_max', '' ),
				'phone_required'  => get_option( 'wbk_phone_required', '3' ),
				'show_desc' => get_option( 'wbk_show_service_description', 'disabled' ),
				'date_input' => get_option( 'wbk_date_input', 'popup' ),
				'allow_attachment' => get_option( 'wbk_allow_attachemnt',  'no' ),
				'stripe_public_key' => get_option( 'wbk_stripe_publishable_key', '' ),
				'override_stripe_error' => get_option( 'wbk_stripe_card_input_mode', 'no' ),
				'stripe_card_error_message' => get_option( 'wbk_stripe_card_element_error_message', 'incorrect input' ),
				'something_wrong' => __( 'Something went wrong, please try again.', 'wbk' ),
				'pp_redirect' => get_option( 'wbk_paypal_auto_redirect', 'disabled' )


			);                                                 
			wp_localize_script( 'wbk-frontend', 'wbkl10n', $translation_array ); 	
			
	 	}
	 	if( $this->has_shortcode_strong( 'webba_feature_appointmens' ) ) {
	 		wp_enqueue_style( 'slf-tablesaw', plugins_url( '../../backend/solo-framework/css/tablesaw.css', __FILE__ ) );
	 		wp_enqueue_script( 'slf-tablesaw', plugins_url( '../../backend/solo-framework/js/tablesaw.js',  __FILE__ ), array( 'jquery' ) );
	 		wp_enqueue_script( 'wbk-feature-appointments', plugins_url( 'js/wbk-feature-appointments.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );	 	 		                      
		}
	}
	private function has_shortcode_strong( $shortcode ){
	    $post_to_check = get_post(get_the_ID()); 
	    if ( !$post_to_check ) {
	    	return false;
	    }    
	    $found = false;
	    if ( !$shortcode ) {
	        return $found;
	    }
	    if ( stripos($post_to_check->post_content, '[' . $shortcode) !== false ) {
 	        $found = true;
	    }
 	    return $found;

	}

	// check if post has shortcode
	private function has_shortcode( $shortcode = '' ) {     
	    if( get_option('wbk_check_short_code', 'disabled') == 'disabled' ){
	    	return true;
	    }
	    $post_to_check = get_post(get_the_ID()); 
	    if ( !$post_to_check ) {
	    	return false;
	    }    
	    $found = false;
	    if ( !$shortcode ) {
	        return $found;
	    }
	    if ( stripos($post_to_check->post_content, '[' . $shortcode) !== false ) {
 	        $found = true;
	    }
 	    return $found;
	}
	// hook for action wbk_render_frontend_category_list
	public function render_frontend_category_list( $data ){
		if( $data != 1 ){
			return;
		}
		$render_data = array();	
		$render_data = apply_filters( 'wbk_pre_render_frontend_category_list', $render_data, $data );
		$this->render_from_array( $render_data );		
	}
	// hook for wbk_render_multi_service_services
	public function render_multi_service_services( $arg ){
		$render_data = array();		
		if( get_option( 'wbk_multi_booking', 'disabled' ) == 'disabled' ){
			$render_data[ 'error_message' ] = 'Multi-service mode required multiple booking to be enabled. Check the Mode tab of Webba Settings page.';
			$this->render_from_array( $render_data );
			return;
		}
		$render_data = apply_filters( 'wbk_pre_render_multi_service_services', $render_data, $arg );
		$this->render_from_array( $render_data );
	}
	public function pre_render_multi_service_services( $input, $arg ){
		if( $arg[1] == '1' ){
			$input['hide_open'] = '<div class="wbk_multiserv_hidden_services" style="display:none">';
		}
		$input['title'] =  '<label class="wbk-input-label">' . wbk_get_translation_string( 'wbk_service_label', 'service_label' , 'Select service' ) . '</label>';
		$services = WBK_Db_Utils::getServices();
		$temp = '';
		$filter_used = FALSE; 
		if( get_option( 'wbk_allow_service_in_url', 'no' ) == 'yes'  && isset( $_GET['service'] ) ){
			$arr_from_url = explode( '-', $_GET['service']  );
			$filter_used = TRUE;
		}
		foreach ( $services as $service_id ){
			if( $filter_used ){
				if( !in_array( $service_id, $arr_from_url ) ){
					continue;
				}
			}
			$service = WBK_Db_Utils::initServiceById( $service_id );
			if( $service === FALSE ){
				continue;
			}
			if( $arg[1] == '1' ){
				$temp .= '<input type="checkbox" value="' . $service_id . '" class="wbk-checkbox wbk-service-checkbox" id="wbk-service_chk_' . $service_id .  '" checked />';
			} else {
				$temp .= '<input type="checkbox" value="' . $service_id . '" class="wbk-checkbox wbk-service-checkbox" id="wbk-service_chk_' . $service_id .  '" />';			
			}
			$temp .= '<label for="wbk-service_chk_' . $service_id . '" class="wbk-checkbox-label wbk_service_chk_label">' . $service->getName() . '</label>';
			$temp .= '<div class="wbk-clear"></div>';
		}
		$input['services'] = $temp;	
		$input['confirm_button'] =  '<input type="button" disabled="disabled" class="wbk-button wbk-width-100 wbk-mt-10-mb-10" id="wbk-confirm-services" value="' . __( 'Start booking', 'wbk' ) . ' ">';
		if( $arg[1] == '1' ){
			$input['hide_close'] = '</div>';
		}

		return $input;
	} 
	// hook for filter wbk_pre_render_frontend_category_list
	public function pre_render_frontend_category_list( $input ){
		$input['label'] = '<label class="wbk-input-label wbk-category-input-label">' . wbk_get_translation_string( 'wbk_category_label', 'category_label', 'Select category' ) . '</label>';	
		$catetories = WBK_Db_Utils::getServiceCategoryList();	
		$category_html = '<select class="wbk-select wbk-input" id="wbk-category-id">';
		$category_html .= '<option value="0" selected="selected">' . __( 'select...', 'wbk' ) . '</option>';
		foreach ( $catetories as $key => $value ){
			$category_html .= '<option data-services="' . implode( '-', WBK_Db_Utils::getServicesInCategory( $key ) ) . '" value="' . $key . '">' . $value . ' </option>';	
		}
		$category_html .= '</select>';
		$input['categories'] = $category_html;
		$full_service_list = '<select class="wbk_hidden" id="wbk_service_id_full_list">'; 		
		$arrIds = WBK_Db_Utils::getServices();
		foreach ( $arrIds as $service_id ) {
			$service = WBK_Db_Utils::initServiceById( $service_id );			 
 			if( get_option( 'wbk_show_service_description', 'disabled' ) == 'disabled' ){
	 			$full_service_list .=  '<option value="' . $service->getId() . '"  data-multi-low-limit="' . $service->getMultipleLowLimit() . '" data-multi-limit="' . $service->getMultipleLimit() . '" >' . $service->getName( true ) . '</option>';
 			} else {
	 			$full_service_list .=  '<option data-desc="' . htmlspecialchars( $service->getDescription( true ) ) . '" value="' . $service->getId() . '"  data-multi-low-limit="' . $service->getMultipleLowLimit() . '"  data-multi-limit="' . $service->getMultipleLimit() . '" >' . $service->getName( true ) . '</option>';
 			}
		}
		$full_service_list .= '</select>';
		$input['full_service_list'] = $full_service_list; 
		return $input;
	}
	// render from array 
	private function render_from_array( $input ){
		foreach ($input as $key => $value) {
			echo $value;
		}
	}

}
?>