<?php
//WBK validator class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Validator {
	// check string size
	public static function checkStringSize( $str, $min, $max ) {
		if ( strlen($str) > $max || strlen($str) < $min ) {
			return false;
		
		} else {
			return true;
		}
	}
	// check integer 
	public static function checkInteger( $int, $min, $max ) {
	  	if ( !is_numeric( $int ) ) {	  	 
			return false;
		}
		if ( intval( $int ) <> $int ) {
			return false;
		}
		if ( $int > $max || $int < $min ) {
			return false;	
		}  
		return true;
	}
	// check if email
	public static function checkEmail( $eml ) {
		if ( !preg_match( '/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,10})$/', $eml ) ) {
			return false;
			
		} else {
			return true;
		}
	}
	// check if color
	public static function checkColor( $clr ) {
		if ( !preg_match( '/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $clr ) ) {
			return false;
			
		} else {
			return true;
		}
	}
	// check if day of week
	public static function checkDayofweek( $str ) {
		if ( $str != 'monday' && $str != 'tuesday' && $str != 'wednesday' && $str != 'thursday' && $str != 'friday' && $str != 'saturday' && $str != 'sunday' ) {
			return false;
		
		} else {
			return true;
		}		 
	 
	}
	// check business hours array 
	public static function checkBusinessHours( $value ) {
		$bh = new WBK_Business_Hours();
		$arr = explode( ';', $value );
		if ( $bh->setFromArray( $arr ) ) {
			return true;
		
		} else {
			return false;
		}
	}
	// check if current user has access to any of existing service
	public static function checkAccessToSchedule(){
		$user_id = get_current_user_id();
		if ( $user_id == 0 ) {
			return false;
		
		}
		global $wpdb;
		$users = $wpdb->get_col(  'SELECT users FROM wbk_services'   );
		foreach ( $users as $user ) {
			$user_arr = explode( ';' , $user );
			if ( in_array( $user_id, $user_arr ) ){
				return true;
			} 
		}
		return false;
	}
	public static function checkAccessToGgCalendarPage(){
		global $current_user; 
		global $wpdb;
		$user_id = get_current_user_id();
		if ( $user_id == 0 ) {
			return false;		
		}	
		$user_count = $wpdb->get_var(   $wpdb->prepare( 'SELECT count(*) as cnt FROM wbk_gg_calendars where user_id = %d', $user_id ) );		 	
		if( $user_count > 0 ){
			return true;
		} 
		return false;
	}
	public static function checkAccessToGgCalendar( $calendar_id ){
		global $current_user; 
		global $wpdb;
		$user_id = get_current_user_id();
		if ( $user_id == 0 ) {
			return false;		
		}
		
		$user_count = $wpdb->get_var(   $wpdb->prepare( 'SELECT count(*) as cnt FROM wbk_gg_calendars where user_id = %d AND id = %d', $user_id, $calendar_id ) );	
  	
		if( $user_count > 0 ){
			return true;
		} 
		return false;
	}
	// check if current user has access to specified service
	public static function checkAccessToService( $service_id ) {
		global $current_user;
		$user_id = get_current_user_id();
		if ( $user_id == 0 ) {
			return false;
		}
		global $wpdb;
		$user = $wpdb->get_var(  $wpdb->prepare( 'SELECT users FROM wbk_services WHERE id = %d', $service_id )  );   
 		$user_arr = explode( ';' , $user );
		if ( in_array( $user_id, $user_arr ) ){
			return true;
		} 
		return false;
	}
	// check price (PayPal format)
	public static function checkPrice( $value ){
		if ( !is_numeric( $value ) ){
			return false;
		}
		if ( $value < 0 || $value > '9999999' ){
			return false;
		}
		return true;
	}
	// check email loop for multiple emails
	public static function checkEmailLoop( $value ){
 		if( substr_count( $value, '[appointment_loop_start]' ) == 1 && substr_count( $value, '[appointment_loop_end]' )  == 1 ){
	 		if( strpos( $value, '[appointment_loop_start]' ) < strpos( $value, '[appointment_loop_end]' ) ){
 				return true;
 			}
 		}
 		return false;
	}
	// check if coupon is applicable
	public static function checkCoupon( $coupon, $service_id ){
		global $wpdb;
		$result = $wpdb->get_row( $wpdb->prepare( " SELECT * FROM wbk_coupons WHERE name = %s", $coupon ) , ARRAY_A );	
		if( $result == NULL ){
			return FALSE;
		}
		// check service 
		if( $result['services'] != ''){
			$services = explode(',', $result['services'] );
			if( !in_array( $service_id, $services ) ){				 
				return FALSE;
			}
		}
		// check used
		if( $result['maximum'] != 0 && $result['maximum'] != '' ){
			if( intval( $result['used'] ) >= $result['maximum'] ){				 
				return FALSE;
			}
		}
		// check date range
		if( $result['date_range'] != '' ){
			$range = explode( ' - ', $result['date_range'] );
			$start = strtotime( trim( $range[0] ) );
			$end = strtotime( trim( $range[1] ) );	

			if( time() >= $start && time() <= $end ){
			} else { 				 
				return FALSE;
			}
		}
		return array( $result['id'], $result['amount_fixed'], $result['amount_percentage'] );
	}
	
	
	

}
?>