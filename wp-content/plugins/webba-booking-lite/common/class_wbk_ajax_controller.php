<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
// Webba Booking common ajax controller class
require_once 'class_wbk_business_hours.php';

// define main frontend class
class WBK_Ajax_Controller {
	public function __construct() {
		// add action render service hours on frontend
		add_action( 'wp_ajax_wbk-render-days', array( $this, 'ajaxRenderDays') );
		add_action( 'wp_ajax_nopriv_wbk-render-days', array( $this,'ajaxRenderDays') );
		// add action search time slots on fronted
		add_action( 'wp_ajax_wbk_search_time', array( $this, 'ajaxSearchTime') );
		add_action( 'wp_ajax_nopriv_wbk_search_time', array( $this,'ajaxSearchTime') );
		// add action render time form
		add_action( 'wp_ajax_wbk_render_booking_form', array( $this, 'ajaxRenderBookingForm') );
		add_action( 'wp_ajax_nopriv_wbk_render_booking_form', array( $this, 'ajaxRenderBookingForm') );
		// add action for booking
		add_action( 'wp_ajax_wbk_book', array( $this, 'ajaxBook') );
		add_action( 'wp_ajax_nopriv_wbk_book', array( $this, 'ajaxBook') );
		// add action for payment prepare
		add_action( 'wp_ajax_wbk_prepare_payment', array( $this, 'ajaxPreparePayment') );
		add_action( 'wp_ajax_nopriv_wbk_prepare_payment', array( $this, 'ajaxPreparePayment') );
		// add action for appointment delete
		add_action( 'wp_ajax_wbk_cancel_appointment', array( $this, 'ajaxCancelAppointment') );
		add_action( 'wp_ajax_nopriv_wbk_cancel_appointment', array( $this, 'ajaxCancelAppointment') );
		
		add_action( 'wp_ajax_wbk_prepare_service_data', array( $this, 'ajaxPrepareServiceData') );
		add_action( 'wp_ajax_nopriv_wbk_prepare_service_data', array( $this, 'ajaxPrepareServiceData') );
	}
	// callback render service hours on frontend
	public function ajaxRenderDays() {
	//**TZ 	date_default_timezone_set( 'UTC' );
		$total_steps = $_POST['step'];
		$service_id = $_POST['service'];
		if ( !WBK_Validator::checkInteger( $service_id, 1 , 999999 ) ) {
			echo -1;
			die();
			return;
		}
		if ( !WBK_Validator::checkInteger( $total_steps, 3 , 4 ) ) {
			echo -1;
			die();
			return;
		}
		if ( $total_steps == 3 ) {
			$step = 2;
		} else {
			$step = 3;
		}
		// initalize service object
	 	$this->service = new WBK_Service();
	 	if ( $this->service->setId( $service_id ) ){
	 		if ( !$this->service->load() ){
	 			echo -1;
	 			die();
	 			return;
	 		}
	 	} else {
	 		echo -1;
	 		die();
	 		return;
	 	}
	 	global $wbk_wording;
		$business_hours = new WBK_Business_Hours();
 		$business_hours->load( $this->service->getBusinessHours() );
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$select_hours_label = get_option( 'wbk_hours_label', '' );
		if ( $select_hours_label ==  '' ) {
			$select_hours_label = sanitize_text_field( $wbk_wording['hours_label'] ); 
		}
 		$show_hours =  get_option( 'wbk_show_suitable_hours', 'yes');
 		if( $show_hours == 'yes' ){
 			$row_class = 'wbk-frontend-row';
 		} else {
 			$row_class = 'wbk_hidden';
 		}
		$html = '<div class="wbk-col-12-12">';
		if( $show_hours == 'yes' ){
			$html .= '<label class="wbk-input-label">' . $select_hours_label .' </label>';
			$html .= '<hr class="wbk-hours-separator">';
		}		
 		$unlocked_days_of_week = $business_hours->getLockedDaysOfWeek( $service_id );
 		$hours_step = $this->service->getStep() * 60;

		for ( $i = 1;  $i <= 7;  $i++ ) {
			$day_name = $business_hours->getDayName( $i );
			if ( $business_hours->isWorkday( $day_name ) ) {
				$html .= '<div class="' . $row_class . '" >';
				$hours = $business_hours->getFullInterval( $day_name );
				$day_name_translated = $business_hours->getDayNameTranslated( $i );
				$select = '<select id="wbk-time_' . $day_name . '" class="wbk-input wbk-time_after">';
				for ( $time = $hours[0]; $time < $hours[1]; $time += $hours_step ) {
					$time_temp = $time - 2;
					$select .= '<option value="' . $time_temp . '" >' .  __( 'from', 'wbk' ) . ' ' . date_i18n ( $time_format, $time ) . '</option>';
				}
				$select .= '</select>';
				$html .= '<div class="wbk-col-3-12 wbk-table-cell">
							<input type="checkbox" value="' . $day_name . '" class="wbk-checkbox" id="wbk-day_' . $day_name .  '" checked="checked"/>
							<label for="wbk-day_' . $day_name . '" class="wbk-checkbox-label wbk-dayofweek-label">' . $day_name_translated . '</label>
						  </div>';
				$html .= '<div class="wbk-col-9-12">' . $select . '</div>';
				$html .= '</div>';
			 	$html .= '<div class="wbk-clear"></div>';
			} else {
				if ( in_array( $i, $unlocked_days_of_week ) ) {					
					$html .= '<div class="' . $row_class . '" >';
					$hours = $business_hours->getFullInterval( $day_name );
					$day_name_translated = $business_hours->getDayNameTranslated( $i );
					$select = '<select id="wbk-time_' . $day_name . '" class="wbk-input wbk-time_after">';
					for ( $time = $hours[0]; $time < $hours[1]; $time += $hours_step ) {
						$time_temp = $time - 2;
						$select .= '<option value="' . $time_temp . '" >' .  __( 'from', 'wbk' ) . ' ' . date_i18n ( $time_format, $time ) . '</option>';
					}
					$select .= '</select>';
					$html .= '<div class="wbk-col-3-12 wbk-table-cell">
								<input type="checkbox" value="' . $day_name . '" class="wbk-checkbox" id="wbk-day_' . $day_name .  '" checked="checked"/>
								<label for="wbk-day_' . $day_name . '" class="wbk-checkbox-label">' . $day_name_translated . '</label>
							  </div>';
					$html .= '<div class="wbk-col-9-12">' . $select . '</div>';
					$html .= '</div>';
				 	$html .= '<div class="wbk-clear"></div>';
				}
			}
		}
 		$html .= '<input type="button" class="wbk-button wbk-searchtime-btn"  id="wbk-search_time_btn" value="' . __( 'Search time slots', 'wbk' ) . '"  />'; 		 
	 	echo '<hr class="wbk-separator"/>' . $html;
 	    die();
 	    return;
	}
	// callback search time slots
	// timizone conversion enbaled
	public function ajaxSearchTime() {
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$service_id  = $_POST['service'];
		$date        = $_POST['date'];
		$days 	     = $_POST['days'];
		$times       = $_POST['times'];
		$offset 	 = $_POST['offset'];
		if( !is_numeric( $offset ) ){
			$offset = 0;
		}
		// check date variable: string date or int timestamp
		if ( !is_numeric( $date) ) {
			$day_to_render = strtotime( $date );
		} else {
			$day_to_render = $date;
		}
		if( !is_numeric( $offset ) ){
			$offset = 0;
		}
		// validation
		if ( get_option( 'wbk_mode', 'extended' ) == 'extended' && !is_array( $service_id ) ) {
			if ( !is_array( $days ) || !is_array( $times ) ) {
				echo -1;
				die();
				return;
			}		 
			foreach ( $days as $day ) {
				if ( !WBK_Validator::checkDayofweek( $day ) ) {
					date_default_timezone_set('UTC');
					echo -3;
					die();
					return;
				}
			}
			foreach ( $times as $time ) {
				if ( !WBK_Validator::checkInteger( $time, 0, 1758537351 ) ) {
					date_default_timezone_set('UTC');
					echo -4;
					die();
					return;
				}
			}
		}
		if ( !WBK_Validator::checkInteger( $day_to_render, 0, 1758537351 ) ) {
				date_default_timezone_set('UTC');
				echo -5;
				die();
				return;
		}
		// end validation
		if( !is_array( $service_id ) ){

	 		$service_schedule = new WBK_Service_Schedule();
	 		$service_schedule->setServiceId( $service_id );
	 		if ( !$service_schedule->load() ) {
	 			date_default_timezone_set('UTC');
	 			echo -6;
	 			die();
	 			return;
	 		}
	 		$limit_end = 0;
	 		if( $service_schedule->getService()->getDateRange() != '' ){
	 			$limit_end = $service_schedule->getService()->getDateRangeEnd();
	 		}	 		
			$date_format = WBK_Date_Time_Utils::getDateFormat();
	 		$i = 0;
	 		if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
		 		$output_count = 2;
		 	} else {
		 		$output_count = 0;
		 	}
		 	$html = '';
		 	$limit_year = 0;
	 		while ( $i <= $output_count ) {
	 			$limit_year++;
	 			if(  $limit_year > 360  ){
	 				$i = $output_count + 1;
	 				continue;
	 			}
	 			if( $limit_end != 0 && get_option( 'wbk_mode', 'extended' ) == 'extended'  ){
	 				if( $day_to_render > $limit_end ){
	 					$i = $output_count + 1;
	 					continue;
	 				}
	 			}
	 			$day_status =  $service_schedule->getDayStatus( $day_to_render );
	 			if ( $day_status == 1 ) {
					if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
		 				$day_name = strtolower( date( 'l', $day_to_render ) );
		 				$key = array_search( $day_name, $days );
		 				if ( $key === FALSE ) {
							$day_to_render = strtotime( 'tomorrow', $day_to_render );
		 					continue;
		 				} else {
		 					$time_after = $times[$key] + $day_to_render;
		 					
							$day = $day_to_render;
							$tz = new DateTimeZone( get_option( 'wbk_timezone', 'UTC' ) ); 
							$transition = $tz->getTransitions( $day, $day ); 
							$offset1 = $transition[0]['offset']; 	 
							$next_day = strtotime('+1 day', $day );
						 	$transition = $tz->getTransitions($next_day, $next_day ); 
							$offset2 = $transition[0]['offset']; 
							$difference = $offset1 - $offset2;

							if( $difference != 0 ){
								$time_after += $difference;
							}
		 				}
		 			} else {
		 				$time_after = $day_to_render;
		 			}
	 				$service_schedule->buildSchedule( $day_to_render );
		 			$day_title = date_i18n ( $date_format, $day_to_render );
		 			$day_slots = $service_schedule->renderDayFrontend( $time_after, $offset );
		 		 
		 			// CHECK FOR 1 ONLY TIME SLOTS
		 			if ( substr_count( $day_slots, 'wbk-timeslot-btn' ) == 1  &&  get_option( 'wbk_skip_timeslot_select', 'disabled' ) == 'enabled'  && get_option( 'wbk_mode', 'extended' ) == 'simple' ){
		 				$first_time = $service_schedule->getFirstAvailableTime();
		 				$form_html = $this->render_booking_form( $service_id, $first_time );
		 				$result =  array( 'dest' => 'form', 'data'  => $form_html,  'time' => $first_time );
		 				date_default_timezone_set('UTC');
						echo json_encode( $result );
				 		die();
						return; 
		 			}
		 			// END CHECK FOR 1 ONLY TIME SLOTS

		 			if ( $day_slots != '' ) {
		 				$html .= '<div class="wbk-col-12-12">
									<div class="wbk-day-title">
										'. $day_title .' 
									</div>
									<hr class="wbk-day-separator">
		  						  </div>';
						$html .= '<div class="wbk-col-12-12 wbk-text-center" >' . $day_slots . '</div>';
		 			}
		 			$i++;
	 			}
				if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
		  			$day_to_render = strtotime( 'tomorrow', $day_to_render );
				} else {
		  			$i++;
				}
			}
		
		}  else {
			$service_ids = $service_id;
			$date_format = WBK_Date_Time_Utils::getDateFormat();
	 		if ( !is_numeric( $date) ) {
				$day_to_render = strtotime( $date );
			} else {
				$day_to_render = $date;
			}
			if( !is_numeric( $offset ) ){
				$offset = 0;
			}
	 		if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
		 		$output_count = 2;
		 	} else {
		 		$output_count = 0;
		 	}
		 	$html = '';
			$i = 0;
			$multi_serv_date_limit = get_option( 'wbk_multi_serv_date_limit', '360' );
			$up_limit = strtotime('today midnight') + 86400 * $multi_serv_date_limit;
	 		while ( $i <= $output_count ) {
	 			if( $day_to_render > $up_limit ){
	 				$limit_end = $day_to_render + 1;
	 				$i = $output_count + 1;
	 			}
	 			$day_slots_all_services = '';
			 	foreach( $service_ids as $service_id ){
		 			$service_schedule = new WBK_Service_Schedule();
				 	$service_schedule->setServiceId( $service_id );
			 		if ( !$service_schedule->load() ) {
			 			continue;
			 		} 			 
		 			$day_status =  $service_schedule->getDayStatus( $day_to_render );		 			 
		 			if ( $day_status == 1 ) {					 
			 			$time_after = $day_to_render;			 			 
		 				$service_schedule->buildSchedule( $day_to_render );			 		 
			 			$day_slots  = $service_schedule->renderDayFrontend( $time_after, $offset );		 		 	 	
		 			} else {
		 				$day_slots = '';
		 			}
		 			if( $day_slots != '' ){
						$day_slots_all_services .=  '<label class="wbk-multiple-service-title">' .  $service_schedule->getService()->getName() . '</label>'.  $day_slots; 				
		 			}
				} 
                $day_title = date_i18n ( $date_format, $day_to_render );
                if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
		  			$day_to_render = strtotime( 'tomorrow', $day_to_render );
				} else {
		  			$i++;
				}
				
				 
	 			if ( $day_slots_all_services != '' ) {
	 				$html .= '<div class="wbk-col-12-12">
								<div class="wbk-day-title">
									'. $day_title .' 
								</div>
								<hr class="wbk-day-separator">
	  						  </div>';
					$html .= '<div class="wbk-col-12-12 wbk-text-center" >' . $day_slots_all_services . '</div>';
					$i++;
	 			}
	 			

			}

		}


 		
 		if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
			if ( $html != '' ){
				if( $limit_end != 0 ){
	 				if(  $day_to_render <= $limit_end ){
	 					$html .= '<div class="wbk-frontend-row" id="wbk-show_more_container">
								<input type="button" class="wbk-button"  id="wbk-show_more_btn" value="' . __( 'Show more', 'wbk' ) . '"  />
								<input type="hidden" id="wbk-show-more-start" value="' . $day_to_render . '">
							  </div>';
					}
					$html .=  '<div class="wbk-more-container"></div>'; 				
	 			} else {
					$html .= '<div class="wbk-frontend-row" id="wbk-show_more_container">
								<input type="button" class="wbk-button"  id="wbk-show_more_btn" value="' . __( 'Show more', 'wbk' ) . '"  />
								<input type="hidden" id="wbk-show-more-start" value="' . $day_to_render . '">
							  </div>';
					$html .= '<div class="wbk-more-container"></div>';
	 			}
			} else {
				$html = get_option( 'wbk_book_not_found_message',  'Unfortunately we were unable to meet your search criteria. Please change the criteria and try again.' );
			}
		} else {
			if ( $html == '' ) {
				$html = get_option( 'wbk_book_not_found_message',  'Unfortunately we were unable to meet your search criteria. Please change the criteria and try again.' );
			}
		}

		if ( get_option( 'wbk_show_cancel_button', 'disabled' ) == 'enabled' ){
			global $wbk_wording;
        	$cancel_label =  get_option( 'wbk_cancel_button_text',  '' );
        	if( $cancel_label == '' ){
        		$cancel_label = sanitize_text_field( $wbk_wording['cancel_label_form'] );
        	}
			$html .= '<input class="wbk-button wbk-width-100 wbk-cancel-button"  value="' . $cancel_label . '" type="button">';					
		}
		$result =  array( 'dest' => 'slot', 'data'  => $html );
		echo json_encode( $result );
		date_default_timezone_set('UTC');
 		die();
		return;
	}
	// time zone converted
	public function ajaxRenderBookingForm() {
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$time = $_POST['time'];
		if( isset( $_POST['service'] ) ){
			$service_id = $_POST['service'];
			$multi_service = false;
		} else {
			$multi_service = true;
			$services = $_POST['services'];
			foreach ( $services as $service_this ) {
				if ( !WBK_Validator::checkInteger( $service_this, 1, 2758537351 ) ) {
					echo -1;
					date_default_timezone_set('UTC');
					die();
					return;
				}
			}
		}
        $offset = $_POST['time_offset'];
		if( !is_numeric( $offset ) ){
			$offset = 0;
		}
		if ( is_array( $time ) ){
			foreach ( $time as $time_this ) {
				if ( !WBK_Validator::checkInteger( $time_this, 0, 2758537351 ) ) {
					echo -1;
					date_default_timezone_set('UTC');
					die();
					return;
				}
			}
		} else {
			if ( !WBK_Validator::checkInteger( $time, 0, 2758537351 ) ) {
				echo -1;
				date_default_timezone_set('UTC');
				die();
				return;
			}
		}

		if( $multi_service ){
		    $result = $this->render_booking_form_multiple( $services, $time );	
		} else {
		    $result = $this->render_booking_form( $service_id, $time, $offset );	
		}
	    
	    if( $result === FALSE ){
	    	echo -1;
	    	date_default_timezone_set('UTC');
	    	die();
	    	return;
	    } 	 
 	 	echo $result;
 	 	date_default_timezone_set( 'UTC' );
		die();
		return;
	}
	public function ajaxBook() {
		global $wpdb;
		global $wbk_wording;
		$arr_uploaded_urls = array();
		if( get_option( 'wbk_allow_attachemnt', 'no' ) == 'yes' ){
			foreach ( $_FILES as $file  ) {				 				 
				$uploaded_file = wp_handle_upload( $file, array( 'test_form' => false ) );
				if( $uploaded_file && !isset( $uploaded_file['error'] ) ) {
					$arr_uploaded_urls[] = $uploaded_file['file'];
				}
			}
		}
		if( count( $arr_uploaded_urls ) > 0 ){
			$attachments = json_encode( $arr_uploaded_urls );
		} else {
			$attachments = '';
		}
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$name = sanitize_text_field( $_POST['name'] );
		$email = sanitize_text_field( $_POST['email'] );
		$phone = sanitize_text_field( $_POST['phone'] );
		$times = explode( ',', $_POST['time'] );
		if( isset( $_POST['services'] ) ){
			$services = explode( ',', $_POST['services'] );			
		}	
		$desc = sanitize_text_field(  $_POST['desc'] );
		$extra =  stripcslashes( $_POST['extra'] );
 		$quantity = sanitize_text_field( $_POST['quantity']);
 		$current_category = sanitize_text_field( $_POST['current_category']);
		$time_offset =  sanitize_text_field( $_POST['time_offset']);								
									 
		$per_serv_quantity_result = array();
		if( !is_numeric( $time_offset ) ){
			$time_offset = 0;
		}
		if( isset( $_POST['secondary_data'] ) ){
			$secondary_data = $_POST['secondary_data'];
		}	
		if( isset( $_POST['service'] ) && $_POST['service']  != 'undefined' ){
			$service_id = $_POST['service'];
			$multi_service = false;
		} else {
			$multi_service = true;	
			if( $_POST['per_serv_quantity']  != '' ){				 
				$per_serv_quantity = explode(',', $_POST['per_serv_quantity'] );
				foreach( $per_serv_quantity as $cur_quantity ){					 
					$cur_quantity = explode( ';', $cur_quantity );		
					if( count( $cur_quantity ) != 2 ){
						echo -1;
						date_default_timezone_set('UTC');
						die();
						return;
					}	
					if ( !WBK_Validator::checkInteger( $cur_quantity[0] , 1, 2758537351 ) ) {
						echo -9;
						date_default_timezone_set('UTC');
						die();
						return;
					}		 
					if( !WBK_Validator::checkInteger( $cur_quantity[1], 1, 1000000 ) ){
						echo -9;
						date_default_timezone_set('UTC');
						die();
						return;
					}
					$per_serv_quantity_result[  'service-' . $cur_quantity[0] ] = $cur_quantity[1];					 
				}
			}		 
			foreach ( $services as $service_this ) {
				if ( !WBK_Validator::checkInteger( $service_this, 1, 2758537351 ) ) {
					echo -1;
					date_default_timezone_set('UTC');
					die();
					return;
				}
			}
		}	 		
		$appointment_ids = array();
		$i = -1;
		$skipped_count = 0;		 
		$serices_used = array();
		foreach ( $times as $time ) {
			$i++;
			if( !is_numeric( $time ) ){
				echo -9;

				date_default_timezone_set('UTC');
				die();
				return;
			}
			if( $time < time() ){
				echo -9;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if( !WBK_Validator::checkInteger( $quantity, 1, 1000000 ) ){
				echo -9;
				date_default_timezone_set('UTC');
				die();
				return;
			}		 	
			$day = strtotime( date( 'Y-m-d', $time ).' 00:00:00' );
			$service = new WBK_Service();
			if( $multi_service ){
				$service_id = $services[ $i ];
			}
			if ( !$service->setId( $service_id ) ) {
				echo -6;				 
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$service->load() ) {
				echo -6;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if( isset( $per_serv_quantity_result[ 'service-' . $service_id ] ) ){
				$quantity_this = $per_serv_quantity_result[ 'service-' .$service_id ];
			} else {
				$quantity_this = $quantity;
			}

			if( $service->getQuantity() == 1 ) {
				$count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM wbk_appointments where service_id = %d and time = %d', $service_id, $time ) );
				if ( $count > 0 ) {

					$skipped_count++;			 
					continue;
				}
			} else {
				$service_schedule = new WBK_Service_Schedule();
				$service_schedule->setServiceId( $service->getId() );
				$avail_count  = $service_schedule->getAvailableCount( $time );
				if ( $quantity_this > $avail_count ){
					$skipped_count++;					 
					continue;
				}
			}			
			// strong validation for multiple service mode
			if( $multi_service ){
				$service_schedule = new WBK_Service_Schedule();
				$service_schedule->setServiceId( $service->getId() );
				$avail_count  = $service_schedule->getAvailableCount( $time );
				if ( $quantity_this > $avail_count ){
					$skipped_count++;					 
					continue;
				}		 
			}
			$duration = $service->getDuration();
			$appointment = new WBK_Appointment();			
			if ( !$appointment->setName( $name ) ){
				echo -1;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setEmail( $email ) ){
				echo -2;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setPhone( $phone ) ){
				echo -3;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setTime( $time ) ){
				echo -4;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setDay( $day ) ){
				echo -5;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setService( $service_id ) ){
				echo -6;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setDuration( $duration ) ){
				echo -7;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setDescription( $desc ) ){
				echo -9;
				date_default_timezone_set('UTC');
				die();
				return;
			}			 
			$extra = apply_filters( 'wbk_external_custom_field', $extra, ''  );			 
			if ( !$appointment->setExtra( $extra ) ){
				echo -9;
				date_default_timezone_set('UTC');
				die();
				return;
			}			 		
			if ( !$appointment->setQuantity( $quantity_this ) ){
				echo -9;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			if ( !$appointment->setTimeOffset( $time_offset ) ){
				echo -9;
				date_default_timezone_set('UTC');				 
				die();
				return;
			}
			if ( !$appointment->setAttachment( $attachments ) ){
				echo -9;
				date_default_timezone_set('UTC');				 
				die();
				return;
			}
			if( !is_numeric( $time ) ){
				echo -9;
				date_default_timezone_set('UTC');				 
				die();
				return;
			}
			// LIMIT validation
			// check time slot limits
			if( get_option( 'wbk_appointments_only_one_per_slot', 'disabled' ) == 'enabled' ){
				if( $service->getQuantity() > 1 ) {
					$count = WBK_Db_Utils::getCountOfAppointmentsByEmailTimeService( $email, $time, $service_id );
					if( $count > 0 ){
						continue;
					}
				}
			}
			if( get_option( 'wbk_appointments_only_one_per_service', 'disabled' ) == 'enabled' ){
				if( $service->getQuantity() > 1 ) {
					$count = WBK_Db_Utils::getCountOfAppointmentsByEmailService( $email, $service_id );
					if( $count > 0 ){
						continue;
					}
				}
			}
			// END LIMIT VALIDATION
			$appointment_id = $appointment->add();
			if ( !$appointment_id ) {
				echo -8;
				date_default_timezone_set('UTC');
				die();
				return;
			}
			WBK_Db_Utils::setCreatedOnToAppointment( $appointment_id );
			WBK_Db_Utils::setActualDurationToAppointment( $appointment_id, 0 );
			$serices_used[] = $service_id;
			if( get_option( 'wbk_appointments_default_status', 'approved' ) == 'approved' ){
				WBK_Db_Utils::updateAppointmentStatus( $appointment_id, 'approved' );
			}
			$auto_lock = get_option( 'wbk_appointments_auto_lock', 'disabled' );
			if ( $auto_lock == 'enabled' ){
				WBK_Db_Utils::lockTimeSlotsOfOthersServices( $service_id, $appointment_id );
			}
			$appointment_ids[] = $appointment_id;

			$noifications = new WBK_Email_Notifications( $service_id, $appointment_id, $current_category );
			$noifications->send( 'book' );
			
			// secondary names notifications
			if ( isset( $secondary_data ) ){
				if ( is_array( $secondary_data ) ){
					$noifications->sendToSecondary( $secondary_data );
				}
			}
			$expiration_mode = get_option( 'wbk_appointments_delete_not_paid_mode', 'disabled' );
			if( $expiration_mode == 'on_booking' ){
				WBK_Db_Utils::setAppointmentsExpiration( $appointment_id );
			}		 	
		 
		 
			WBK_Db_Utils::setLangToAppointmentId( $appointment_id );
			WBK_Db_Utils::setAmountForApppointment( $appointment_id );
			$wbK_action_data = array(   'appointment_id' => $appointment_id,
										'customer' => $name, 
										'email'  => $email,
										'phone' =>  $phone,
										'time'  =>  $time,
										'serice id'  =>  $service_id,
										'duration' =>  $duration,
										'comment'  =>  $desc,
										'quantity' =>  $quantity 
										 );

			do_action( 'wbk_add_appointment',  $wbK_action_data ); 

		}
       	if( count( $appointment_ids ) == 0 ){
			echo '-13';
			wp_die();
			return;
		} 	 
		 
		if( get_option( 'wbk_multi_booking', 'disabled' ) != 'disabled' &&  get_option( 'wbk_email_admin_book_multiple_mode', 'one' ) == 'one' ) {
			$noifications = new WBK_Email_Notifications( $service_id, $appointment_id, $current_category );
			$noifications->sendMultipleAdminNotification( $appointment_ids );			 
		}
		$thanks_message = get_option( 'wbk_book_thanks_message', '' ); 	

	 	$thanks_message = WBK_Db_Utils::prepareThankYouMessage( $appointment_ids, $service_id, $thanks_message, $skipped_count );
	 	if ( $thanks_message == '' ) {
	 		$thanks_message =  sanitize_text_field( $wbk_wording['thanks_for_booking'] );	
	 	}
	 	$payment_methods = explode( ';', $service->getPayementMethods() );
		$payment_methods_html = '';	 		

		if( $multi_service ){
			$payment_methods_html .= WBK_PayPal::renderPaymentMethods( $serices_used, $appointment_ids );
			$payment_methods_html .= WBK_Stripe::renderPaymentMethods( $serices_used, $appointment_ids );			
			$payment_methods_html .= WBK_WooCommerce::renderPaymentMethods( $serices_used, $appointment_ids );		
		} else {
			$payment_methods_html .= WBK_PayPal::renderPaymentMethods( $service_id, $appointment_ids );
			$payment_methods_html .= WBK_Stripe::renderPaymentMethods( $service_id, $appointment_ids );		
			$payment_methods_html .= WBK_WooCommerce::renderPaymentMethods( $service_id, $appointment_ids );			
		}

		if( $payment_methods_html != '' ){
			if( get_option( 'wbk_allow_coupons', 'disabled' ) == 'enabled' ){
				$payment_methods_html = '<input class="wbk-input" id="wbk-coupon" placeholder="coupon code" >' . $payment_methods_html;
			}
		}	
		if( in_array( 'arrival', $payment_methods ) ){		
			$button_text = get_option( 'wbk_pay_on_arrival_button_text', __( 'Pay on arrival', 'wbk' ) );			 
			$payment_methods_html .= '<input class="wbk-button wbk-width-100 wbk-mt-10-mb-10 wbk-payment-init wbk-payment-on-booking-init" data-method="arrival" data-app-id="'. implode(',',  $appointment_ids ) . '"  value="' . $button_text . '  " type="button">';
		}
		if( in_array( 'bank', $payment_methods ) ){		
			$button_text =  get_option( 'wbk_bank_transfer_button_text', __( 'Pay by bank transfer', 'wbk' ) );
			$payment_methods_html .= '<input class="wbk-button wbk-width-100 wbk-mt-10-mb-10 wbk-payment-init wbk-payment-on-booking-init" data-method="bank" data-app-id="'. implode(',',  $appointment_ids ) . '"  value="' . $button_text . '  " type="button">';
		}


		$thanks_message .= $payment_methods_html;
		if( count( $appointment_ids ) > 0 ){
			$booked_slot_text = WBK_Db_Utils::booked_slot_placeholder_processing( $appointment_ids[0] );
		} else {
			$booked_slot_text = '';
		}
		$result = array( 'thanks_message' => $thanks_message,
						 'booked_slot_text' => $booked_slot_text );
		echo json_encode( $result );
		date_default_timezone_set( 'UTC' );
		die();
		return;
	}
	public function ajaxPreparePayment() {	
    	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$method = sanitize_text_field( $_POST['method'] );
		$app_ids = explode( ',', sanitize_text_field( $_POST['app_id'] ) );
		$referer = explode( '?' , wp_get_referer() ); 
		$coupon = sanitize_text_field( trim( $_POST['coupon'] ) );
		if( $method == 'arrival' ){
			foreach ( $app_ids as $app_id ){
				WBK_Db_Utils::setPaymentMethodToAppointment( $app_id, 'Pay on arrival' );
			}
			$html =  get_option( 'wbk_pay_on_arrival_message', ''  );
			echo $html;
			wp_die();
			return;
		}
		if( $method == 'bank' ){
			foreach ( $app_ids as $app_id ){
				WBK_Db_Utils::setPaymentMethodToAppointment( $app_id, 'Bank transfer' );
			}
			$html =  get_option( 'wbk_bank_transfer_message', ''  );
			echo $html;
			wp_die();
			return;
		}
		$pay_not_approved = get_option( 'wbk_appointments_allow_payments', 'disabled' );
		$appointment_ids = array();
		foreach ( $app_ids as  $appointment_id ) {
			$status =  WBK_Db_Utils::getStatusByAppointmentId( $appointment_id );		
			if( $status == 'woocommerce' || $status == 'paid' || $status == 'paid_approved' || ( $status == 'pending' && $pay_not_approved == 'enabled' ) || is_null( $status ) ){
				continue;
			}	
			$appointment_ids[] = $appointment_id;
		}
		if( count( $appointment_ids) == 0 ){
			global $wbk_wording;
			$html = get_option( 'wbk_nothing_to_pay_message', '' );
			if( $html == '' ){
				$html = $wbk_wording['nothing_to_pay'];
			}
			if( $method == 'woocommerce' ){
				echo json_encode( array( 'status' => 0, 'details' => $html ) );		 
			}
			date_default_timezone_set('UTC');
			wp_die();
			return;
		}
		if( get_option('wbk_allow_coupons', 'disabled' ) == 'enabled' ){
			if ( $coupon != '' ){
				$service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $appointment_ids[0] );
				$coupon_result = WBK_Validator::checkCoupon( $coupon, $service_id );							 
			} else {
				$coupon_result = FALSE;				 
			}			
		} else {
			$coupon_result = FALSE;			 
		}
		$coupon_status_html = '';
		if( get_option('wbk_allow_coupons', 'disabled' ) == 'enabled' && $coupon != '' ){
			global $wbk_wording;
			if( is_array( $coupon_result ) ) {
				$coupon_status_html =get_option( 'wbk_coupon_applied', __( 'Coupon applied', 'wbk' ) );
				if( $coupon_status_html == '' ){
					$coupon_status_html = $wbk_wording['wbk_coupon_applied'];
				}
				foreach( $appointment_ids as $appointment_id ){
					WBK_Db_Utils::setCouponToAppointment( $appointment_id, $coupon_result[0] );
				}
			} else {
				$coupon_status_html =get_option( 'wbk_coupon_not_applied', __( 'Coupon not applied', 'wbk' ) );
				if( $coupon_status_html == '' ){
					$wbk_coupon_not_applied = $wbk_wording['wbk_coupon_not_applied'];
				}
				foreach( $appointment_ids as $appointment_id ){
					WBK_Db_Utils::setCouponToAppointment( $appointment_id, 0 );
				}
			}		 
		}
		if( $method == 'paypal' ){
			echo __( 'This Payment method is not supported.', 'wbk' ); 	 			
		}
		if( $method == 'stripe' ){
			$html = __( 'Payment method not supported', 'wbk' );
		}
		if( $method == 'woocommerce' ){			
			$result = WBK_WooCommerce::addToCart( $appointment_ids );			
			echo $result;
			wp_die();
			return;
		}
		$html = '<div class="wbk-details-sub-title">' . $coupon_status_html . '</div>' . $html;
		echo $html;
		date_default_timezone_set( 'UTC' );
		wp_die();
		return;
	}
	public function	ajaxCancelAppointment(){
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$email = $_POST['email'];
		$email = strtolower( $email );
		$app_token = $_POST['app_token'];
		$app_token = str_replace('"', '', $app_token );
		$app_token = str_replace('<', '', $app_token );
		$app_token = str_replace('\'', '', $app_token );
		$app_token = str_replace('>', '', $app_token );
		$app_token = str_replace('/', '', $app_token );
		$app_token = str_replace('\\',  '', $app_token );
		if ( !WBK_Validator::checkEmail( $email ) ){
			global $wbk_wording;
			$message = get_option( 'wbk_booking_cancel_error_message', '' );
			if( $message == '' ){
				global $wbk_wording;
				$message = sanitize_text_field( $wbk_wording['booking_cancel_error'] );
			}
			echo  '<span class="wbk-input-label">' . $message . '</span>';
			date_default_timezone_set('UTC');
			wp_die();
			return;
		}
		$appointment_ids = WBK_Db_Utils::getAppointmentIdsByGroupToken( $app_token );
		$valid = true;
		$arr_tokens = explode( '-', $app_token );
		$i = 0;
		foreach( $appointment_ids as $appointment_id ){	
			$service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $appointment_id );
			if( $service_id == FALSE ){
				continue;
			}
			 	 
			if( WBK_Db_Utils::deleteAppointmentByEmailTokenPair( $email, $arr_tokens[$i] ) == true ){		        		 
		        WBK_Db_Utils::freeLockedTimeSlot( $appointment_id );
			} else {				 
				$valid = false;							 
			}	
			$i++;
		}
 		if( $valid == true ){
			$message = get_option( 'wbk_booking_canceled_message', '' );
			if( $message == ''  ){
				global $wbk_wording;
				$message = sanitize_text_field( $wbk_wording['booking_canceled'] );
			}	
			$message = '<span class="wbk-input-label">' . $message . '</span>';
			$result = array( 'status' => 1, 'message' => $message );
							
		} else {
			global $wbk_wording;
			$message = get_option( 'wbk_booking_cancel_error_message', '' );
			if( $message == '' ){
				global $wbk_wording;
				$message = $wbk_wording['booking_cancel_error'];
			}
			$message = '<span class="wbk-input-label">' . $message . '</span>';
			$result = array( 'status' => 0, 'message' => $message );
		}
		echo json_encode( $result );
		date_default_timezone_set( 'UTC' );
		wp_die();
		return;
	}	 
	public function ajaxPrepareServiceData(){
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		$service_id = $_POST['service'];
		
		$result = array();

		if( !is_array( $service_id ) ){			
	 		if( !is_numeric( $service_id ) ) {
	 			$result['disabilities'] = '';
	 			$result['limits'] = '';
	 			$result['abilities'] = '';
	 			echo json_encode( $result );
	 			date_default_timezone_set( 'UTC' );
	 			wp_die();
	 			return;
	 		}
	 		if ( get_option( 'wbk_date_input', 'popup' ) == 'popup' ){
				$result['disabilities'] = WBK_Date_Time_Utils::getServiceDisabiliy( $service_id );
				$result['limits'] = WBK_Date_Time_Utils::getServiceLimits( $service_id );
				$result['abilities'] = '';
				$result['week_disabilities'] = WBK_Date_Time_Utils::getServicWeekDisabiliy( $service_id );		  

			} else {
				$result['disabilities'] = '';
				$result['limits'] = '';
				$result['abilities'] = WBK_Date_Time_Utils::getBHAbilities( $service_id );	
				$result['week_disabilities'] = '';
			}
		} else {
			$service_ids = $service_id;
			$total_array = array();
			foreach( $service_ids as $service_id ){			
		 		if( !is_numeric( $service_id ) ) {
		 			continue;
		 		}
		 		if ( get_option( 'wbk_date_input', 'popup' ) == 'popup' ){
 					$current_data = explode( ';', WBK_Date_Time_Utils::getServiceDisabiliy( $service_id ) ); 					 
				} else {				 
					$current_data = explode( ';', WBK_Date_Time_Utils::getBHAbilities( $service_id ) );			  
				}
				if( count( $total_array ) == 0 ){
					$total_array = $current_data;
				} else {
					$total_array = array_intersect( $total_array, $current_data );				
				}
			}
			 
			$multi_serv_date_limit = get_option( 'wbk_multi_serv_date_limit', '360' );
			
			$result['limits'] = date( 'Y,n,j', strtotime('today midnight') ) . '-' . date( 'Y,n,j', strtotime('today midnight') + 86400 * $multi_serv_date_limit );

			$result['week_disabilities'] = '';
			if ( get_option( 'wbk_date_input', 'popup' ) == 'popup' ){
				$result['disabilities'] = implode( ';', $total_array );
				$result['abilities'] = ''; 
			} else {
				$result['disabilities'] = '';
				$result['abilities'] = implode( ';', $total_array );; 
			}
		}	
		echo json_encode( $result );
		date_default_timezone_set( 'UTC' );
		wp_die();
		return;		
	}
	protected function render_booking_form_multiple( $service_ids, $times, $offset = 0 ){
		global $wbk_wording;
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$service_ids_unique = array_unique( $service_ids );
 
		$html_all_services = '';
		$forms = array();
		foreach( $service_ids_unique as $service_id ){
			$service = new WBK_Service();
			if ( !$service->setId( $service_id ) ) {
				return FALSE;
			}
			if ( !$service->load() ) {
				return FALSE;
			}
			$forms[] = $service->getForm();
		 	$form_label = get_option( 'wbk_form_label', '' );
		 	if ( $form_label ==  '' ) {
		 		$form_label = sanitize_text_field( $wbk_wording['form_label'] );
		 	}
			$form_label = str_replace( '#service', $service->getName(), $form_label );
			$price_format = get_option( 'wbk_payment_price_format', '$#price' );
			$price = str_replace( '#price', number_format( $service->getPrice(), 2 ), $price_format );
			$form_label = str_replace( '#price', $price, $form_label );	
			$total = $service->getPrice();
			$price_format = get_option( 'wbk_payment_price_format', '$#price' );
			$tax_rule = get_option( 'wbk_tax_for_messages', 'paypal' );
			if( $tax_rule == 'paypal' ){
				$tax = get_option( 'wbk_paypal_tax', 0 );			
			}
			if( $tax_rule == 'stripe' ){
				$tax = get_option( 'wbk_stripe_tax', 0 );			
			}
			if( $tax_rule == 'none' ){
				$tax = 0;			
			}
			if( is_numeric( $tax ) && $tax > 0 ){
				$tax_amount = ( ( $total ) / 100 ) * $tax;
		    	$total = $total + $tax_amount;
			} 
			$time = array();
			$i = 0;
			foreach( $times as $curent_time ){
				if( $service_ids[$i] == $service_id  ){
					$time[] = $curent_time;
				}	
				$i++;
			} 
			if( is_array( $time ) ){

				$timezone = new DateTimeZone( get_option( 'wbk_timezone' ) );
				$current_offset =  $offset * - 60 - $timezone->getOffset( new DateTime );

				$total = $total * count( $time );			
				$date_collect = array();
				$time_collect = array();
				$datetime_collect = array();
				$datetime_n_collect = array();
				$datetimerange_n_collect = array();
				$time_local_collect = array();

				foreach ( $time as $time_this ) {
					$end_this = $time_this + $service->getDuration() * 60;
					$date_collect[] = date_i18n( $date_format, $time_this );
					$time_collect[] = date_i18n( $time_format, $time_this );
					$time_local_collect[] = date_i18n( $time_format, $time_this + $current_offset );
					$datetime_collect[] = date_i18n( $date_format, $time_this ) . ' ' .  date_i18n( $time_format, $time_this );
					$datetime_n_collect[] = '<br>' . date_i18n( $date_format, $time_this ) . ' ' .  date_i18n( $time_format, $time_this );
					$datetimerange_n_collect[] = '<br>' . date_i18n( $date_format, $time_this ) . '   ' .  date_i18n( $time_format, $time_this ) . ' - ' . date_i18n( $time_format, $end_this );
				}
				$form_label = str_replace( '#date', implode(', ', $date_collect ), $form_label );
				$form_label = str_replace( '#time', implode(', ', $time_collect ), $form_label );
				$form_label = str_replace( '#local', implode(', ', $time_local_collect ), $form_label );
				$form_label = str_replace( '#dt', implode(', ', $datetime_collect ), $form_label );
				$form_label = str_replace( '#drt', implode('', $datetime_n_collect ), $form_label );
				$form_label = str_replace( '#dre', implode('', $datetimerange_n_collect ), $form_label );
	 		}  
			$total_price =  str_replace( '#price', number_format( $total, 2 ), $price_format );

			$form_label = str_replace( '#total_amount', $total_price, $form_label );

			$form_label = str_replace( '#selected_count', count( $time ), $form_label );

			$html = '<div class="wbk-details-sub-title">' . $form_label . ' </div>';
			
			$html_all_services .= $html;
		} 

		foreach( $service_ids_unique as $service_id ){
			$html = '';
			$service = new WBK_Service();
			if ( !$service->setId( $service_id ) ) {
				return FALSE;
			}
			if ( !$service->load() ) {
				return FALSE;
			}		
			if ( $service->getQuantity() > 1 ) {
				$service_schedule = new WBK_Service_Schedule();
				$service_schedule->setServiceId( $service->getId() );
				if( is_array( $time ) ){
					$avail_count  = 1000000;
					foreach ( $time as $time_this ) {
						$current_avail  = $service_schedule->getAvailableCount( $time_this );
						if( $current_avail < $avail_count ){
							$avail_count = $current_avail;
						}
					}
				} else {
					$avail_count  = $service_schedule->getAvailableCount( $time );
				}
				$quantity_label = get_option( 'wbk_book_items_quantity_label', '' );
				if( $quantity_label == '' ){
				 	$quantity_label =  sanitize_text_field( $wbk_wording['quantity_label'] ); 
				}
				$quantity_label = str_replace( '#service', $service->getName(), $quantity_label );

				$selection_mode = get_option( 'wbk_places_selection_mode', 'normal' );
				if( $selection_mode == 'normal' ){
					$html .= '<label class="wbk-input-label" for="wbk-quantity">' . $quantity_label  . '</label>';
					$html .= '<select  type="text" data-service="' . $service_id . '" class="wbk-input wbk-width-100 wbk-mb-10 wbk-book-quantity">';
					for ( $i = 1; $i <= $avail_count; $i ++ ) {
						$html .= '<option value="' . $i . '" >' . $i . '</option>';
					}			
				} elseif ( $selection_mode == '1'){
					$html .= '<select  type="text" data-service="' . $service_id . '" class="wbk-input wbk_hidden wbk-width-100 wbk-mb-10 wbk-book-quantity">';
					$html .= '<option value="1">1</option>';
					$html .= '</select>';

				} elseif ( $selection_mode == 'max' ){
					$html .= '<select   type="text" data-service="' . $service_id . '" class="wbk-input wbk_hidden wbk-width-100 wbk-mb-10 wbk-book-quantity">';
					$html .= '<option value="' . $service->getQuantity() . '">' .  $service->getQuantity() .'</option>';
					$html .= '</select>';
				}

				$html .= '</select>';
			}		
			$html_all_services .= $html;
		}
		$html = $html_all_services;
		$forms = array_unique( $forms );
		if( count( $forms ) == 1 ){
			$form = $forms[0];
		} else {
			$form = 0;
		}

		if ( $form == 0 ){
			$name_label = get_option( 'wbk_name_label', '' );
			$email_label = get_option( 'wbk_email_label', '' );
			$phone_label = get_option( 'wbk_phone_label', '' );
			$comment_label = get_option( 'wbk_comment_label', '' ); 

			if ( $name_label == '' ){
				$name_label = sanitize_text_field( $wbk_wording['form_name'] );
			}
			if ( $email_label == '' ){
				$email_label = sanitize_text_field( $wbk_wording['form_email'] );
			}
			if ( $phone_label == '' ){
				$phone_label = sanitize_text_field( $wbk_wording['form_phone'] );
			}
			if ( $comment_label == '' ){
				$comment_label = sanitize_text_field( $wbk_wording['form_comment'] );
			}	

			$html .= '<label class="wbk-input-label" for="wbk-customer_name">' .$name_label . '</label>';
			$html .= '<input name="wbk-name" type="text" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-customer_name" />';
			$html .= '<label class="wbk-input-label" for="wbk-customer_email">' . $email_label . '</label>';
			$html .= '<input name="wbk-email"  type="text" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-customer_email" />';
			$html .= '<label class="wbk-input-label" for="wbk-customer_phone">' . $phone_label . '</label>';
			$html .= '<input name="wbk-phone" type="text" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-customer_phone" />';
			$html .= '<label class="wbk-input-label" for="wbk-customer_desc">' . $comment_label . '</label>';
	 		$html .= '<textarea name="wbk-comment" rows="3" class="wbk-input wbk-textarea wbk-width-100 wbk-mb-10" id="wbk-customer_desc"></textarea> ';
		} else {
			$cf7_form = do_shortcode( '[contact-form-7 id="' . $form . '"]' );
			
			$cf7_form = str_replace('<p>', '', $cf7_form );
			$cf7_form = str_replace('</p>', '', $cf7_form );
			$cf7_form = str_replace('<label', '<label class="wbk-input-label" ', $cf7_form );
			$cf7_form = str_replace('type="checkbox"', 'type="checkbox" class="wbk-checkbox" ', $cf7_form );
			$cf7_form = str_replace('wbk-checkbox', ' wbk-checkbox wbk-checkbox-custom ', $cf7_form );
			$cf7_form = str_replace('wpcf7-list-item-label', 'wbk-checkbox-label', $cf7_form );
			$cf7_form = str_replace('wpcf7-list-item', 'wbk-checkbox-span-holder', $cf7_form );
			$cf7_form = str_replace('wpcf7-list-item-label', 'wbk-checkbox-label', $cf7_form );
			$cf7_form = str_replace( 'name="wbk-acceptance"',
									 'name="wbk-acceptance" value="1" id="wbk-acceptance" aria-invalid="false"><span class="wbk-checkbox-label"></span> <input type="hidden"',
									  $cf7_form );

			$cf7_form = str_replace('type="file"', 'type="file" accept="' . get_option( 'wbk_attachment_file_types', 'image/*' ) . '"', $cf7_form );
			$html .= $cf7_form;
		}
		$book_text = get_option( 'wbk_book_text_form', '');
		if ( $book_text == '' ){
			$book_text = $wbk_wording['book_text'];
		}
        $html .= '<input type="button" class="wbk-button wbk-width-100 wbk-mt-10-mb-10" id="wbk-book_appointment" value="' . $book_text . '">';

        if ( get_option( 'wbk_show_cancel_button', 'disabled' ) == 'enabled' ){
        	global $wbk_wording;
        	$cancel_label = get_option( 'wbk_cancel_button_text',  '' );
        	if( $cancel_label == '' ){
        		$cancel_label = sanitize_text_field( $wbk_wording['cancel_label_form'] );
        	}
			$html .= '<input class="wbk-button wbk-width-100 wbk-cancel-button"  value="' . $cancel_label . '" type="button">';
		}
 	 	return '<hr class="wbk-separator"/>' . $html;
	}	
	protected function render_booking_form( $service_id, $time, $offset = 0 ){
		global $wbk_wording;
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$service = new WBK_Service();
		if ( !$service->setId( $service_id ) ) {
			return FALSE;
		}
		if ( !$service->load() ) {
			return FALSE;
		}
		$form = $service->getForm();
	 	$form_label = get_option( 'wbk_form_label', '' );
	 	if ( $form_label ==  '' ) {
	 		$form_label = sanitize_text_field( $wbk_wording['form_label'] );
	 	}

		$timezone = new DateTimeZone( get_option( 'wbk_timezone' ) );
		$current_offset =  $offset * - 60 - $timezone->getOffset( new DateTime );
		
		$form_label = str_replace( '#service', $service->getName(), $form_label );

		$price_format = get_option( 'wbk_payment_price_format', '$#price' );
		$price = str_replace( '#price', number_format( $service->getPrice(),  get_option( 'wbk_price_fractional', '2' ) ), $price_format );
		$form_label = str_replace( '#price', $price, $form_label );
		
		$total = $service->getPrice();

		$price_format = get_option( 'wbk_payment_price_format', '$#price' );
		$tax_rule = get_option( 'wbk_tax_for_messages', 'paypal' );
		if( $tax_rule == 'paypal' ){
			$tax = get_option( 'wbk_paypal_tax', 0 );			
		}
		if( $tax_rule == 'stripe' ){
			$tax = get_option( 'wbk_stripe_tax', 0 );			
		}
		if( $tax_rule == 'none' ){
			$tax = 0;			
		}
		if( is_numeric( $tax ) && $tax > 0 ){
			$tax_amount = ( ( $total ) / 100 ) * $tax;
	    	$total = $total + $tax_amount;
		} 
		

		if( is_array( $time ) ){
			$total = $total * count( $time );
			
			$date_collect = array();
			$time_collect = array();
			$datetime_collect = array();
			foreach ( $time as $time_this ) {
				$end_this = $time_this + $service->getDuration() * 60;
				$date_collect[] = date_i18n( $date_format, $time_this );
				$time_collect[] = date_i18n( $time_format, $time_this );
				$time_local_collect[] = date_i18n( $time_format, $time_this + $current_offset );
				$datetime_collect[] = date_i18n( $date_format, $time_this ) . ' ' .  date_i18n( $time_format, $time_this );
				$datetime_n_collect[] = '<br>' . date_i18n( $date_format, $time_this ) . ' ' .  date_i18n( $time_format, $time_this );
				$datetimerange_n_collect[] = '<br>' . date_i18n( $date_format, $time_this ) . ' / ' .  date_i18n( $time_format, $time_this ) . ' - ' . date_i18n( $time_format, $end_this );
			}
			$form_label = str_replace( '#date', implode(', ', $date_collect ), $form_label );
			$form_label = str_replace( '#time', implode(', ', $time_collect ), $form_label );
			$form_label = str_replace( '#local', implode(', ', $time_local_collect ), $form_label );
			$form_label = str_replace( '#dt', implode(', ', $datetime_collect ), $form_label );
			$form_label = str_replace( '#drt', implode('', $datetime_n_collect ), $form_label );
			$form_label = str_replace( '#dre', implode('', $datetimerange_n_collect ), $form_label );
			$form_label = str_replace( '#selected_count', count( $time ), $form_label );
 		} else {
			$form_label = str_replace( '#date', date_i18n( $date_format, $time ), $form_label );
			$form_label = str_replace( '#time', date_i18n( $time_format, $time ), $form_label );
			$form_label = str_replace( '#dt', date_i18n( $date_format, $time ) . ' ' .  date_i18n( $time_format, $time ), $form_label );			
			$local_time = $time + $current_offset;
			$form_label = str_replace( '#local', date_i18n( $time_format, $local_time ), $form_label );
		}
		$total_price =  str_replace( '#price', number_format( $total,  get_option( 'wbk_price_fractional', '2' ) ), $price_format );

		$form_label = str_replace( '#total_amount', $total_price, $form_label );
		$html = '<div class="wbk-details-sub-title">' . $form_label . ' </div>';
		$html .= '<hr class="wbk-form-separator">';
		if ( $service->getQuantity() > 1 ) {
			$service_schedule = new WBK_Service_Schedule();
			$service_schedule->setServiceId( $service->getId() );

			if( is_array( $time ) ){
				$avail_count  = 1000000;
				foreach ( $time as $time_this ) {
					$current_avail  = $service_schedule->getAvailableCount( $time_this );
					if( $current_avail < $avail_count ){
						$avail_count = $current_avail;
					}
				}
			} else {
				$avail_count  = $service_schedule->getAvailableCount( $time );
			}
			$quantity_label = get_option( 'wbk_book_items_quantity_label', '' );
			if( $quantity_label == '' ){
			 	$quantity_label =  sanitize_text_field( $wbk_wording['quantity_label'] ); 
			}
			$quantity_label = str_replace( '#service', $service->getName(), $quantity_label );
			$selection_mode = get_option( 'wbk_places_selection_mode', 'normal' );

			if( $selection_mode == 'normal' ){
				$html .= '<label class="wbk-input-label" for="wbk-quantity">' . $quantity_label  . '</label>';
				$html .= '<select name="wbk-book-quantity" type="text" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-book-quantity">';
				for ( $i = 1; $i <= $avail_count; $i ++ ) {
					$html .= '<option value="' . $i . '" >' . $i . '</option>';
				}			
			} elseif ( $selection_mode == '1'){
				$html .= '<select name="wbk-book-quantity" type="text" class="wbk-input wbk_hidden wbk-width-100 wbk-mb-10" id="wbk-book-quantity">';
				$html .= '<option value="1">1</option>';
				$html .= '</select>';

			} elseif ( $selection_mode == 'max' ){
				$html .= '<select name="wbk-book-quantity" type="text" class="wbk-input wbk_hidden wbk-width-100 wbk-mb-10" id="wbk-book-quantity">';
				$html .= '<option value="' . $service->getQuantity() . '">' .  $service->getQuantity() .'</option>';
				$html .= '</select>';
			}

			$html .= '</select>';
		}
		if ( $form == 0 ){
			$name_label = get_option( 'wbk_name_label', '' );
			$email_label = get_option( 'wbk_email_label', '' );
			$phone_label = get_option( 'wbk_phone_label', '' );
			$comment_label = get_option( 'wbk_comment_label', '' ); 

			if ( $name_label == '' ){
				$name_label = sanitize_text_field( $wbk_wording['form_name'] );
			}
			if ( $email_label == '' ){
				$email_label = sanitize_text_field( $wbk_wording['form_email'] );
			}
			if ( $phone_label == '' ){
				$phone_label = sanitize_text_field( $wbk_wording['form_phone'] );
			}
			if ( $comment_label == '' ){
				$comment_label = sanitize_text_field( $wbk_wording['form_comment'] );
			}	

			$html .= '<label class="wbk-input-label" for="wbk-customer_name">' .$name_label . '</label>';
			$html .= '<input name="wbk-name" type="text" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-customer_name" />';
			$html .= '<label class="wbk-input-label" for="wbk-customer_email">' . $email_label . '</label>';
			$html .= '<input name="wbk-email"  type="text" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-customer_email" />';
			$html .= '<label class="wbk-input-label" for="wbk-customer_phone">' . $phone_label . '</label>';
			$html .= '<input name="wbk-phone" type="text" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-customer_phone" />';
			$html .= '<label class="wbk-input-label" for="wbk-customer_desc">' . $comment_label . '</label>';
	 		$html .= '<textarea name="wbk-comment" rows="3" class="wbk-input wbk-textarea wbk-width-100 wbk-mb-10" id="wbk-customer_desc"></textarea> ';
		} else {
			$cf7_form = do_shortcode( '[contact-form-7 id="' . $form . '"]' );
			
			$cf7_form = str_replace('<p>', '', $cf7_form );
			$cf7_form = str_replace('</p>', '', $cf7_form );
			$cf7_form = str_replace('<label', '<label class="wbk-input-label" ', $cf7_form );
			$cf7_form = str_replace('type="checkbox"', 'type="checkbox" class="wbk-checkbox" ', $cf7_form );
			$cf7_form = str_replace('wbk-checkbox', ' wbk-checkbox wbk-checkbox-custom ', $cf7_form );
			$cf7_form = str_replace('wpcf7-list-item-label', 'wbk-checkbox-label', $cf7_form );
			$cf7_form = str_replace('wpcf7-list-item', 'wbk-checkbox-span-holder', $cf7_form );
			$cf7_form = str_replace('wpcf7-list-item-label', 'wbk-checkbox-label', $cf7_form );
			$cf7_form = str_replace( 'name="wbk-acceptance"',
									 'name="wbk-acceptance" value="1" id="wbk-acceptance" aria-invalid="false"><span class="wbk-checkbox-label"></span> <input type="hidden"',
									  $cf7_form );

			$cf7_form = str_replace('type="file"', 'type="file" accept="' . get_option( 'wbk_attachment_file_types', 'image/*' ) . '"', $cf7_form );
			$html .= $cf7_form;
		}
		$book_text = get_option( 'wbk_book_text_form', '');
		if ( $book_text == '' ){
			$book_text = $wbk_wording['book_text'];
		}
        $html .= '<input type="button" class="wbk-button wbk-width-100 wbk-mt-10-mb-10" id="wbk-book_appointment" value="' . $book_text . '">';

        if ( get_option( 'wbk_show_cancel_button', 'disabled' ) == 'enabled' ){
        	global $wbk_wording;
        	$cancel_label = get_option( 'wbk_cancel_button_text',  '' );
        	if( $cancel_label == '' ){
        		$cancel_label = sanitize_text_field( $wbk_wording['cancel_label_form'] );
        	}
			$html .= '<input class="wbk-button wbk-width-100 wbk-cancel-button"  value="' . $cancel_label . '" type="button">';
		}
 	 	return '<hr class="wbk-separator"/>' . $html;
	}
	public function searchMultiServiceTimeSlots( $service_ids, $date, $offset ){ 		 		
		$date_format = WBK_Date_Time_Utils::getDateFormat();
 		if ( !is_numeric( $date) ) {
			$day_to_render = strtotime( $date );
		} else {
			$day_to_render = $date;
		}
		if( !is_numeric( $offset ) ){
			$offset = 0;
		}
 		if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
	 		$output_count = 2;
	 	} else {
	 		$output_count = 0;
	 	}
	 	$html = '';
		$i = 0;

 		while ( $i <= $output_count ) {
 			$day_slots_all_services = '';
		 	foreach( $service_ids as $service_id ){

	 			$service_schedule = new WBK_Service_Schedule();
			 	$service_schedule->setServiceId( $service_id );
		 		if ( !$service_schedule->load() ) {
		 			continue;
		 		}
	 			 
	 			$day_status =  $service_schedule->getDayStatus( $day_to_render );
	 			 
	 			if ( $day_status == 1 ) {
					 
		 			$time_after = $day_to_render;
		 			 
	 				$service_schedule->buildSchedule( $day_to_render );
		 		 
		 			$day_slots  = $service_schedule->renderDayFrontend( $time_after, $offset );
	 		 	 	
	 			}
				if ( get_option( 'wbk_mode', 'extended' ) == 'extended' ) {
		  			$day_to_render = strtotime( 'tomorrow', $day_to_render );
				} else {
		  			$i++;
				}
				$day_slots_all_services .= $day_slots;
			} 
			$day_title = date_i18n ( $date_format, $day_to_render );
 			if ( $day_slots_all_services != '' ) {
 				$html .= '<div class="wbk-col-12-12">
							<div class="wbk-day-title">
								'. $day_title .' 
							</div>
							<hr class="wbk-day-separator">
  						  </div>';
				$html .= '<div class="wbk-col-12-12 wbk-text-center" >' . $day_slots . '</div>';
 			}
 			$i++;

		}
		return $html;
	}
}
?>