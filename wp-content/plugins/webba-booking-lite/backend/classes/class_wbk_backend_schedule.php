<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
require_once  dirname(__FILE__).'/../../common/class_wbk_date_time_utils.php';
require_once  dirname(__FILE__).'/../../common/class_wbk_service_schedule.php';
class WBK_Backend_Schedule extends WBK_Backend_Component   {
	public function __construct() {
		//set component-specific variables
		$this->name          = 'wbk-schedule';
		$this->title         = __( 'Schedules', 'wbk' );
		$this->main_template = 'tpl_wbk_backend_schedule.php';
		$this->capability    = 'read';
		// init scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts') );
		// add ajax actions
		add_action( 'wp_ajax_wbk_schedule_load', array( $this, 'ajaxScheduleLoad' ) );
		add_action( 'wp_ajax_wbk_lock_day', array( $this, 'ajaxLockDay' ) );
		add_action( 'wp_ajax_wbk_unlock_day', array( $this, 'ajaxUnlockDay' ) );
		add_action( 'wp_ajax_wbk_lock_time', array( $this, 'ajaxLockTime' ) );
		add_action( 'wp_ajax_wbk_unlock_time', array( $this, 'ajaxUnlockTime' ) );  
		add_action( 'wp_ajax_wbk_view_appointment', array( $this, 'ajaxViewAppointment' ) );
		add_action( 'wp_ajax_wbk_prepare_appointment', array( $this, 'ajaxPrepareAppointment' ) );
		add_action( 'wp_ajax_wbk_delete_appointment', array( $this, 'ajaxDeleteAppointment' ) ); 
		add_action( 'wp_ajax_wbk_add_appointment_backend', array( $this, 'ajaxAddAppointment' ) );
		add_action( 'wp_ajax_wbk_render_tool', array( $this, 'ajaxRenderTool' ) );
		add_action( 'wp_ajax_wbk_auto_lock', array( $this, 'ajaxAutoLock' ) );
		add_action( 'wp_ajax_wbk_auto_unlock', array( $this, 'ajaxAutoUnLock' ) );
		add_action( 'wp_ajax_wbk_auto_lock_time_slot', array( $this, 'ajaxAutoLockTimeSlot' ) );
		add_action( 'wp_ajax_wbk_auto_unlock_time_slot', array( $this, 'ajaxAutoUnLockTimeSlot' ) );
 
 	}
	// init styles and scripts
	public function enqueueScripts() {
 		if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wbk-schedule' ) { 
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-plugin', plugins_url( 'js/jquery.plugin.js', dirname( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-dialog' );
	        wp_enqueue_script( 'wbk-schedule', plugins_url( 'js/wbk-schedule.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog' ) );
 			wp_enqueue_script( 'wbk-validator', plugins_url( '../common/wbk-validator.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core' ) );   
	 	
			if( get_option('wbk_phone_mask', 'enabled') == 'enabled' ){
					wp_enqueue_script( 'jquery-maskedinput', plugins_url( '../common/jquery.maskedinput.min.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core' ) );
			} elseif( get_option('wbk_phone_mask', 'enabled') == 'enabled_mask_plugin' ){
				wp_enqueue_script( 'jquery-maskedinput', plugins_url( '../common/jquery.mask.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ) );  
			}

	 		wp_enqueue_script( 'multidate-picker', plugins_url( 'js/jquery.datepick.min.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ) );
	 		wp_enqueue_style( 'wbk-datepicker-css', plugins_url( 'css/jquery.datepick.css', dirname( __FILE__ ) )  );
			$translation_array = array(
				'addappointment' => __( 'Add appointment', 'wbk' ),			
				'add' => __( 'Add', 'wbk' ),	
				'close' => __( 'Close', 'wbk' ),
				'appointment' => __( 'Appointment', 'wbk' ),
				'delete' => __( 'Delete', 'wbk' ),
				'shownextweek' => __( 'Show next week', 'wbk' ),
				'phonemask' => get_option( 'wbk_phone_mask', 'enabled' ),
				'phoneformat' => get_option( 'wbk_phone_format', '(999) 999-9999' ),	
				
			);
			wp_localize_script( 'wbk-schedule', 'wbkl10n', $translation_array );
 		}
 	}
 	// ajax edit description
 	public function ajaxScheduleLoad() {
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		$service_id = $_POST['service_id'];
		global $current_user;
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	            date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
 		$start = $_POST['start'];
 		if ( !WBK_Validator::checkInteger( $service_id, 1, 99999 ) ){
			echo '-1';
            date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( !WBK_Validator::checkInteger( $start, 0, 99999 ) ){
			echo '-2';
            date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		// check if service exists
 		$service_test = new WBK_Service();
 		if ( !$service_test->setId( $service_id ) ){
 			echo -1;
            date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( !$service_test->load() ){
 			echo -1;
            date_default_timezone_set( 'UTC' );		
 			die();
 			return;
 		}
 		// init service schedulle
 		$service_schedule = new WBK_Service_Schedule();
 		$service_schedule->setServiceId( $service_id );
 		$service_schedule->load();
 		// output days
 		if ( $start == 0 ){
			$day_to_render = WBK_Date_Time_Utils::getStartOfCurrentWeek();
 		} else {
			$nextWeekDay = strtotime('today') +  86400 * 7 * $start;
			$tz = new DateTimeZone( get_option( 'wbk_timezone', 'UTC' ) ); 
			$transition = $tz->getTransitions( time(), time() ); 
			$offset1 = $transition[0]['offset']; 				 
			$transition = $tz->getTransitions( $nextWeekDay, $nextWeekDay ); 
			$offset2 = $transition[0]['offset']; 
			$difference = $offset1 - $offset2;
			$nextWeekDay = $nextWeekDay + $difference; 
			$day_to_render = WBK_Date_Time_Utils::getStartOfWeekDay( $nextWeekDay );
 		}
		$date_format = WBK_Date_Time_Utils::getDateFormat();	 
		$html = '<div class="row-simple">';
		for ( $i = 1;  $i <= 7 ;  $i++ ) { 
			$statusClass = 'green_bg';
			$day_status = $service_schedule->getDayStatus( $day_to_render );
			if ( $day_status == 0 ) {
				$statusClass = 'red_bg';
			}
			$today = strtotime('today');
			if ( $day_to_render < $today ) {
				$statusClass = 'gray_bg';		
				$html_day_controls = ''; 
			} else {
				if ( $day_status == 0 ){
					$html_day_controls = '<div class="day_controls" href="/" id="day_controls_' . $day_to_render . '">
												<a class="green_font" id="day_unlock_' . $service_id . '_' . $day_to_render . '">' . __( 'open', 'wbk' ) . '</a>
										  </div>';
				} else {
					$html_day_controls = '<div class="day_controls" id="day_controls_' . $day_to_render . '">
												<a class="red_font" id="day_lock_' . $service_id . '_' . $day_to_render . '">' . __( 'close', 'wbk' ) . '</a>
										  </div>';
				}
			}
			$service_schedule->buildSchedule( $day_to_render );
			if ( $day_to_render < $today ) {
				$html_schedule = $service_schedule->renderPastDayBackend();
			} else {
				$html_schedule = $service_schedule->renderDayBackend();
			}
			$html .=  '<div class="day_container">' . 
					    	'<div id="day_title_' . $day_to_render . '" class="day_title ' . $statusClass . '">'.
								date_i18n( $date_format, $day_to_render ).
								'</div>' . $html_day_controls . '
								<div>'.
								$html_schedule
								.'</div>
						</div>'; 
			$day_to_render = strtotime( 'tomorrow', $day_to_render  );
		}
  		$html .= '</div>';
 	    date_default_timezone_set( 'UTC' );
		echo $html;
		die();
 	}
 	// ajax lock day
 	public function ajaxLockDay() {
 		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		global $wpdb;
 		$service_id = $_POST['service_id'];	
		global $current_user;
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	             date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
 		if ( !WBK_Validator::checkInteger( $service_id, 1, 99999 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		$day = $_POST['day'];
 		if ( !WBK_Validator::checkInteger( $day, 1438426800, 1754046000 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_days_on_off WHERE day = %d and service_id = %d",  $day, $service_id ) ) === false ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( $wpdb->insert( 'wbk_days_on_off', array( 'service_id' => $service_id, 'day' => $day, 'status' => 0 ), array( '%d', '%d', '%d' ) ) === false ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
		date_default_timezone_set( 'UTC' );

 		echo '<a class="green_font" id="day_unlock_' . $service_id . '_' . $day . '">' . __( 'open', 'wbk' ) . '</a>';
		die();
		return;
 	}	 
	// ajax unlock day
 	public function ajaxUnlockDay() {
 		global $wpdb;
 		global $current_user;
 		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		$service_id = $_POST['service_id'];
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	            date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
 		if ( !WBK_Validator::checkInteger( $service_id, 1, 99999 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		$day = $_POST['day'];
 		if ( !WBK_Validator::checkInteger( $day, 1438426800, 1754046000 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_days_on_off WHERE day = %d and service_id = %d",  $day, $service_id ) ) === false ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( $wpdb->insert( 'wbk_days_on_off', array( 'service_id' => $service_id, 'day' => $day, 'status' => 1 ), array( '%d', '%d', '%d' ) ) === false ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		date_default_timezone_set( 'UTC' );
 		echo '<a class="red_font" id="day_lock_' . $service_id . '_' . $day . '">' . __( 'close', 'wbk' ) . '</a>';
		die();
 	}	
 	// ajax lock time
 	public function ajaxLockTime() {
 		global $wpdb;
 		global $current_user;
 		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		$service_id = $_POST['service_id'];
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	            date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
 		if ( !WBK_Validator::checkInteger( $service_id, 1, 99999 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		$time = $_POST['time'];
 		if ( !WBK_Validator::checkInteger( $time, 1438426800, 1754046000 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_locked_time_slots WHERE time = %d and service_id = %d",  $time, $service_id ) ) === false ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( $wpdb->insert( 'wbk_locked_time_slots', array( 'service_id' => $service_id, 'time' => $time ), array( '%d', '%d' ) ) === false ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		echo  '<a class="red_font" id="time_unlock_' . $service_id . '_' . $time . '"><span class="dashicons dashicons-lock"></span></a>';
 		date_default_timezone_set( 'UTC' );
		die();
 	}	 
 	// ajax unlock time
 	public function ajaxUnlockTime() {
 		global $wpdb;
 		global $current_user;
 		$service_id = $_POST['service_id'];
 		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	            date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
 		if ( !WBK_Validator::checkInteger( $service_id, 1, 99999 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		$time = $_POST['time'];
 		if ( !WBK_Validator::checkInteger( $time, 1438426800, 1754046000 ) ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_locked_time_slots WHERE time = %d and service_id = %d",  $time, $service_id ) ) === false ){
 			echo -1;
 			date_default_timezone_set( 'UTC' );
 			die();
 			return;
 		}
 		date_default_timezone_set( 'UTC' );
 		echo  '<a id="app_add_' . $service_id . '_' . $time . '"><span class="dashicons dashicons-welcome-add-page"></span></a><a id="time_lock_' . $service_id . '_' . $time . '"><span class="dashicons dashicons-unlock"></a>';
		die();	
 	}
	// ajax view appointmet
 	public function ajaxViewAppointment() {
 		global $wpdb;
 		global $current_user;
 		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		$service_id = $_POST['service_id'];
 		$appointment_id = $_POST['appointment_id'];
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	            date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
        $appointment = new WBK_Appointment();
        if ( !$appointment->setId( $appointment_id ) ) {
            echo '-2';
            date_default_timezone_set( 'UTC' );
            die();
            return;
        }     
        if ( !$appointment->load() ) {
            echo '-4';
            date_default_timezone_set( 'UTC' );
            die();
            return;
        }
        $name = $appointment->getName();
        $desc = $appointment->getDescription();
        $email = $appointment->getEmail();
        $phone = $appointment->getPhone();
        $time = $appointment->getTime();
        $quantity = $appointment->getQuantity();
        $extra = $appointment->getExtra();
         
        $extra = json_decode( $extra );
    	$extra_data = '';
		foreach( $extra as $item ){
    		if( count( $item ) <> 3 ){
    			continue;    			
    		}
    		$extra_data .= $item[1] . ': ' . $item[2] . PHP_EOL;
		}
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$time_string = date_i18n( $date_format, $time ) . ' ' . date_i18n( $time_format, $time );
		$resarray = array( 'name' => $name, 'desc' =>  $desc, 'email' => $email, 'phone' => $phone, 'time' => $time_string, 'extra' => $extra_data, 'quantity' => $quantity );
        echo json_encode($resarray);
        date_default_timezone_set( 'UTC' );
        die();
        return;
 	}
	// ajax prepare appointmet
 	public function ajaxPrepareAppointment() {
 		global $wpdb;
 		global $current_user;
 		$time = $_POST['time'];
 		$service_id = $_POST['service_id'];
 		$service = new WBK_Service();
 		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		if ( !$service->setId( $service_id ) ){
			echo '-1';
			date_default_timezone_set( 'UTC' );
			die();
			return; 
 		}
  		if ( !$service->load() ){
			echo '-1';
			date_default_timezone_set( 'UTC' );
			die();
			return; 
 		}
 		$quantity = $service->getQuantity();
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	            date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$time_string = date_i18n( $date_format, $time ) . ' ' . date_i18n( $time_format, $time );
		$service_schedule = new WBK_Service_Schedule();
		$service_schedule->setServiceId( $service_id );
		$appointment_available =   $service_schedule->getAvailableCount( $time );
		$phone_mask = get_option( 'wbk_phone_mask', 'disabled' );
		$phone_format = '';
		if( $phone_mask == 'enabled' ){
			$phone_format = get_option( 'wbk_phone_format', '999-9999' );
		}
		$resarray = array( 'time' => $time_string, 'timestamp' => $time, 'quantity' => $quantity, 'available' => $appointment_available, 'phone_format' => $phone_format );
        echo json_encode($resarray);
        date_default_timezone_set( 'UTC' );
        die();
        return;
 	}
 	// ajax delete appointment
 	public function ajaxDeleteAppointment() {
 		global $wpdb;
 		global $current_user;
 		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		$service_id = $_POST['service_id'];
 		$appointment_id = $_POST['appointment_id'];
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
	            echo '-1';
	            date_default_timezone_set( 'UTC' );
	            die();
	            return; 
	        }    	
        }
        $appointment = new WBK_Appointment();
        if ( !$appointment->setId( $appointment_id ) ) {
	 			echo '-1';
	 			date_default_timezone_set( 'UTC' );
	 			die();
	 			return;
		}
		if ( !$appointment->load() ){
			echo -3;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		$day = $appointment->getDay();
		if ( $appointment->delete() === false ) {
	 			echo '-2';
	 			date_default_timezone_set( 'UTC' );
	 			die();
	 			return;
		} 
 
		WBK_Db_Utils::freeLockedTimeSlot( $appointment_id );

 		$service_schedule = new WBK_Service_Schedule();
 		$service_schedule->setServiceId( $service_id );
 		$service_schedule->load();
 		$service_schedule->buildSchedule( $day );
 		$day = $service_schedule->renderDayBackend();
		$resarray = array( 'day' =>  $day );

        echo json_encode($resarray);
        date_default_timezone_set( 'UTC' );
	 	die();
	 	return;
 	}
	public function ajaxAddAppointment() {
		global $wpdb;
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$name = $_POST['name'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		$time = $_POST['time'];
		$desc = $_POST['desc'];
		$extra =  stripcslashes( $_POST['extra'] );
		$quantity = $_POST['quantity'];
		$service_id = $_POST['service_id'];
		$day = strtotime( date( 'Y-m-d', $time ).' 00:00:00' );
		$service = new WBK_Service();
		if ( !$service->setId( $service_id ) ) {
			echo -6;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		if ( !$service->load() ) {
			echo -6;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		$count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM wbk_appointments where service_id = %d and time = %d', $service_id, $time ) );
		if ( $count > 0 && $service->getQuantity() == 1 ) {
			echo -9;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		$duration = $service->getDuration();
		$appointment = new WBK_Appointment();
		if ( !$appointment->setName( $name ) ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		if ( !$appointment->setEmail( $email ) ){
			echo -2;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		} 
		if ( !$appointment->setPhone( $phone ) ){
			echo -3;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		if ( !$appointment->setTime( $time ) ){
			echo -4;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}  
		if ( !$appointment->setDay( $day ) ){
			echo -5;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}  
		if ( !$appointment->setService( $service_id ) ){
			echo -6;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}  
		if ( !$appointment->setDuration( $duration ) ){
			echo -7;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		if ( !$appointment->setDescription( $desc ) ){
			echo -9;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		if ( !$appointment->setExtra( $extra ) ){
			echo -9;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		if ( !$appointment->setQuantity( $quantity ) ){
			echo -9;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		$id = $appointment->add();
		if ( $id === false ) {
			echo -8;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}	
		$auto_lock = get_option( 'wbk_appointments_auto_lock', 'disabled' );
		if ( $auto_lock == 'enabled' ){
			WBK_Db_Utils::lockTimeSlotsOfOthersServices( $service_id, $id );
		}
		// *** GG ADD
        WBK_Db_Utils::addAppointmentDataToGGCelendar( $service_id, $id );

 		$service_schedule = new WBK_Service_Schedule();
 		$service_schedule->setServiceId( $service_id );
 		$service_schedule->load();
 		$service_schedule->buildSchedule( $day );
 		$day = $service_schedule->renderDayBackend();
		$resarray = array( 'day' =>  $day );
		date_default_timezone_set( 'UTC' );
        echo json_encode($resarray);
		die();
		return;
	}
	// ajax auto lock day
	public function ajaxAutoLock() {
		global $wpdb;		 		 
		global $current_user;
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$date_range =  sanitize_text_field( $_POST['date_range'] );
		$date_exclude = sanitize_text_field( $_POST['date_exclude'] );
		$service_id = sanitize_text_field ( $_POST['service_id'] );

		$date_range = explode( ' - ', $date_range );
		if( !is_array($date_range) ||  count( $date_range ) <> 2 ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}		 
 		$start = strtotime( $date_range[0] );
 		$end = strtotime( $date_range[1] );
 	 	if ( $end < $start ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
 	 	}
 	 	$exclude_arr = array();
 	 	if( isset($date_exclude) && $date_exclude != '' ){
 	 		$date_exclude = explode( ',', $date_exclude );
 	 		foreach( $date_exclude as $item ) {
 	 			$exclude_arr[] = strtotime( $item );
 	 		}
 	 	}
 	 	 
 	 	$total_locked = 0;
 	 	for ( $i = $start; $i <= $end; $i += 86400 ){
 	 		if ( !in_array( $i, $exclude_arr ) ){
					
			        // check access
			        if ( !in_array( 'administrator', $current_user->roles ) ) {
			        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
				            echo '-2';
				            date_default_timezone_set( 'UTC' );
				            die();
				            return; 
				        }    	
			        }
			 		if ( !WBK_Validator::checkInteger( $service_id, 1, 99999 ) ){
			 			echo -3;
			 			date_default_timezone_set( 'UTC' );
			 			die();
			 			return;
			 		}
			 		 
			 		if ( !WBK_Validator::checkInteger( $i, 1438426800, 1754046000 ) ){
			 			echo -4;
			 			date_default_timezone_set( 'UTC' );
			 			die();
			 			return;
			 		}
			 		if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_days_on_off WHERE day = %d and service_id = %d",  $i, $service_id ) ) === false ){
			 			echo -5;
			 			date_default_timezone_set( 'UTC' );
			 			die();
			 			return;
			 		}
			 		if ( $wpdb->insert( 'wbk_days_on_off', array( 'service_id' => $service_id, 'day' => $i, 'status' => 0 ), array( '%d', '%d', '%d' ) ) === false ){
			 			echo -6;
			 			date_default_timezone_set( 'UTC' );
			 			die();
			 			return;
			 		}
			 		$total_locked++;
			 	 		  
 	 		}

 	 	}
 	 	echo __( 'Total locked: ', 'wbk' ).$total_locked;
 	 	date_default_timezone_set( 'UTC' );
		die();
		return;
 	}
 	// ajax auto lock time slot
	public function ajaxAutoLockTimeSlot() {
		global $wpdb;		 		 
		global $current_user;
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$date_range = sanitize_text_field( $_POST['date_range'] );	 
		$service_id = sanitize_text_field( $_POST['service_id'] );
		$category_id = sanitize_text_field( $_POST['category_id'] );

		$time_start =  sanitize_text_field( $_POST['time_start'] );
		$time_end   =  sanitize_text_field( $_POST['time_end'] );

		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		

		$date_range = explode( ' - ', $date_range );
		if( !is_array($date_range) ||  count( $date_range ) <> 2 ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}

 		$start = strtotime( $date_range[0] );
 		$end = strtotime( $date_range[1] );
 
 	 	if ( $end < $start ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
 	 	}
 	 	if( !is_numeric( $time_start) || !is_numeric( $time_end ) ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
 	 	}
 	 	if ( $time_start > $time_end ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
 	 	} 	 	 
 	 	// check access
		if ( !in_array( 'administrator', $current_user->roles ) ) {
			if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
				echo '-2';
				date_default_timezone_set( 'UTC' );
				die();
				return; 
			}    	
		}
		$total_locked = 0;
		$arr_service_ids = array( $service_id );
		if( $category_id != -1 ){
			if ( WBK_Validator::checkInteger( $category_id, 1, 999999 ) ){
				$arr_service_ids = WBK_Db_Utils::getServicesInCategory( $category_id );
			}
		}  
		foreach( $arr_service_ids as $service_id ){				 
			if( WBK_Db_Utils::initServiceById( $service_id ) == FALSE ){
				continue;
			}
			$service_schedule = new WBK_Service_Schedule();
			$service_schedule->setServiceId( $service_id );
			$service_schedule->load();
	  	 	for ( $i = $start; $i <= $end; $i += 86400 ){ 		  				 				 
				$day_time_start = $i + $time_start;
				$day_time_end = $i + $time_end;
				 
				$service_schedule->buildSchedule( $i );
 				
 				$timeslots_to_lock = $service_schedule->getNotBookedTimeSlotsInRange( $day_time_start, $day_time_end );

 				foreach ( $timeslots_to_lock as $time_slot_start ) {
					if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_locked_time_slots WHERE time = %d and service_id = %d",  $time_slot_start, $service_id ) ) === false ){
						echo -1;
						date_default_timezone_set( 'UTC' );
						die();
						return;
					}
					if ( $wpdb->insert( 'wbk_locked_time_slots', array( 'service_id' => $service_id, 'time' => $time_slot_start ), array( '%d', '%d' ) ) === false ){
						echo -1;
						date_default_timezone_set( 'UTC' );
						die();
						return;
 					}			 
 					$total_locked++;
 				}
	 	 	}
 	 	}
 	  	echo __( 'Total locked: ', 'wbk' ).$total_locked;
 	  	date_default_timezone_set( 'UTC' );
		die();
		return;
 	}	
	// ajax auto unlock time slot
	public function ajaxAutoUnLockTimeSlot() {
		global $wpdb;		 		 
		global $current_user;
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$date_range = sanitize_text_field( $_POST['date_range'] );	 
		$service_id = sanitize_text_field( $_POST['service_id'] );
		$time_start =  sanitize_text_field( $_POST['time_start'] );
		$time_end   =  sanitize_text_field( $_POST['time_end'] );
		$category_id = sanitize_text_field( $_POST['category_id'] );
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$date_range = explode( ' - ', $date_range );
		if( !is_array($date_range) ||  count( $date_range ) <> 2 ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
 		$start = strtotime( $date_range[0] );
 		$end = strtotime( $date_range[1] );
 	 	if ( $end < $start ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
 	 	}
 	 	if( !is_numeric( $time_start) || !is_numeric( $time_end ) ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
 	 	}
 	 	if ( $time_start > $time_end ){
			echo -1;
			date_default_timezone_set( 'UTC' );
			die();
			return;
 	 	}
 	 	$total_locked = 0;
 	 	// check access
		if ( !in_array( 'administrator', $current_user->roles ) ) {
			if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
				echo '-2';
				date_default_timezone_set( 'UTC' );
				die();
				return; 
				}    	
		}
		if ( !WBK_Validator::checkInteger( $service_id, -1, 99999 ) ){
			echo -3;
			date_default_timezone_set( 'UTC' );
			die();
			return;
		}
		$arr_service_ids = array( $service_id );
		if( $category_id != -1 ){
			if ( WBK_Validator::checkInteger( $category_id, 1, 999999 ) ){
				$arr_service_ids = WBK_Db_Utils::getServicesInCategory( $category_id );
			}
		}  
		foreach( $arr_service_ids as $service_id ){
			if( WBK_Db_Utils::initServiceById( $service_id ) == FALSE ){
				continue;
			}
			$service_schedule = new WBK_Service_Schedule();
			$service_schedule->setServiceId( $service_id );
			$service_schedule->load();
	 
	 	 	for ( $i = $start; $i <= $end; $i += 86400 ){ 		  
					$day_time_start = $i + $time_start;
					$day_time_end = $i + $time_end;
					 
					$service_schedule->buildSchedule( $i );
	 				
	 				$timeslots_to_lock = $service_schedule->getLockedTimeSlotsInRange( $day_time_start, $day_time_end );

	 				foreach ( $timeslots_to_lock as $time_slot_start ) {
	 			 
						if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_locked_time_slots WHERE time = %d and service_id = %d",  $time_slot_start, $service_id ) ) === false ){
							echo -1;
							die();
							return;
						}				 	 
	 					$total_locked++;
	 				}

	 	 	}
 	 	}
 	 	date_default_timezone_set( 'UTC' ); 
 	  	echo __( 'Total unlocked: ', 'wbk' ).$total_locked;
		die();
		return;
 	} 	
	// ajax auto unlock day
	public function ajaxAutoUnLock () {
		global $wpdb;		 		 
		global $current_user;
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$date_range =  sanitize_text_field( $_POST['date_range'] );
		$date_exclude = sanitize_text_field( $_POST['date_exclude'] );
		$service_id = sanitize_text_field ( $_POST['service_id'] );

		$date_range = explode( ' - ', $date_range );
		if( !is_array($date_range) ||  count( $date_range ) <> 2 ){
			echo -1;
			date_default_timezone_set( 'UTC' ); 
			die();
			return;
		}		 
 		$start = strtotime( $date_range[0] );
 		$end = strtotime( $date_range[1] );
 	 	if ( $end < $start ){
			echo -1;
			date_default_timezone_set( 'UTC' ); 
			die();
			return;
 	 	}
 	 	$exclude_arr = array();
 	 	if( isset($date_exclude) && $date_exclude != '' ){
 	 		$date_exclude = explode( ',', $date_exclude );
 	 		foreach( $date_exclude as $item ) {
 	 			$exclude_arr[] = strtotime( $item );
 	 		}
 	 	}
 	 	 
 	 	$total_locked = 0;
 	 	for ( $i = $start; $i <= $end; $i += 86400 ){
 	 		if ( !in_array( $i, $exclude_arr ) ){
					
			        // check access
			        if ( !in_array( 'administrator', $current_user->roles ) ) {
			        	if ( !WBK_Validator::checkAccessToService( $service_id ) ) {
				            echo '-2';
				            date_default_timezone_set( 'UTC' ); 
				            die();
				            return; 
				        }    	
			        }
			 		if ( !WBK_Validator::checkInteger( $service_id, 1, 99999 ) ){
			 			echo -3;
			 			date_default_timezone_set( 'UTC' ); 
			 			die();
			 			return;
			 		}
			 		 
			 		if ( !WBK_Validator::checkInteger( $i, 1438426800, 1754046000 ) ){
			 			echo -4;
			 			date_default_timezone_set( 'UTC' ); 
			 			die();
			 			return;
			 		}
			 		if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_days_on_off WHERE day = %d and service_id = %d",  $i, $service_id ) ) === false ){
			 			echo -5;
			 			date_default_timezone_set( 'UTC' ); 
			 			die();
			 			return;
			 		}
			 		if ( $wpdb->insert( 'wbk_days_on_off', array( 'service_id' => $service_id, 'day' => $i, 'status' => 1 ), array( '%d', '%d', '%d' ) ) === false ){
			 			echo -6;
			 			date_default_timezone_set( 'UTC' ); 
			 			die();
			 			return;
			 		}
			 		$total_locked++;
			 	 		  
 	 		}

 	 	}
 	 	date_default_timezone_set( 'UTC' ); 
 	 	echo __( 'Total unlocked: ', 'wbk' ).$total_locked;
		die();
		return;
 	}

 	public function ajaxRenderTool() {
 		global $current_user;
 		$tool = $_POST['tool'];
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
 		$format_js = get_option( 'wbk_date_format_backend', 'm-d-y');
		$format_js = str_replace('d', 'dd', $format_js );
	    $format_js = str_replace('m', 'mm', $format_js );
        $format_js = str_replace('y', 'yyyy', $format_js );
        $dateformat_source = '<input type="hidden" id="wbk_backend_date_format" value="' . $format_js . '">';

 		if ( $tool == 'auto_lock' ){
	 		$html = '<label for="lock_service_list">' . __( 'Select service', 'wbk' ) . ':</label><br>';
	 		$html .= '<select class="wbk_input_500" id="lock_service_list">';
			$html .= '<option value="-1" >' . __( 'Select service', 'wbk' ) . '</option>';
			$arrIds = WBK_Db_Utils::getServices();
	 		if ( count( $arrIds ) > 0 ) {
		 		foreach ( $arrIds as $id ) {
					// check access
					if ( !in_array( 'administrator', $current_user->roles ) ) {
						if ( !WBK_Validator::checkAccessToService( $id ) ) {							
 							continue;
						}    	
					}
		 			$service = new WBK_Service();
		 			if ( !$service->setId( $id ) ) {  
		 				continue;
		 			}
		 			if ( !$service->load() ) {  	 				 
		 				continue;
		 			}
		 			$html .= '<option value="'. $id .'" >' . $service->getName() . '</option>';
		 		}
		 	}
			$html .= '</select><br>';
	 
			$html .= '<label for="lock_date_range">' . __( 'Lock all dates on range', 'wbk' ) . ':</label><br>';
			$html .= '<input class="wbk_input_500" type="text" id="lock_date_range"><br>';
			$html .= '<label for="lock_date_exclude">' . __( 'Exclude dates', 'wbk' ) . ':</label><br>';
			$html .= '<input class="wbk_input_500" type="text" id="lock_exclude_date"><br>';
			$html .= '<p><a class="button-primary" id="auto_lock_launch" >' . __( 'Start', 'wbk' ) . '</a></p>';
			$html .= $dateformat_source;

			date_default_timezone_set( 'UTC' );
	 		echo $html;
			die();
	 		return;

 		}
 		if ( $tool == 'auto_unlock' ){
	 		$html = '<label for="lock_service_list">' . __( 'Select service', 'wbk' ) . ':</label><br>';
	 		$html .= '<select class="wbk_input_500" id="lock_service_list">';
			$html .= '<option value="-1" >' . __( 'Select service', 'wbk' ) . '</option>';
			$arrIds = WBK_Db_Utils::getServices();
	 		if ( count( $arrIds ) > 0 ) {
		 		foreach ( $arrIds as $id ) {
					// check access
					if ( !in_array( 'administrator', $current_user->roles ) ) {
						if ( !WBK_Validator::checkAccessToService( $id ) ) {							
 							continue;
						}    	
					}
		 			$service = new WBK_Service();
		 			if ( !$service->setId( $id ) ) {  
		 				continue;
		 			}
		 			if ( !$service->load() ) {  	 				 
		 				continue;
		 			}
		 			$html .= '<option value="'. $id .'" >' . $service->getName() . '</option>';
		 		}
		 	}
			$html .= '</select><br>';
	 
			$html .= '<label for="lock_date_range">' . __( 'Unlock all dates on range', 'wbk' ) . ':</label><br>';
			$html .= '<input class="wbk_input_500" type="text" id="lock_date_range"><br>';
			$html .= '<label for="lock_date_exclude">' . __( 'Exclude dates', 'wbk' ) . ':</label><br>';
			$html .= '<input class="wbk_input_500" type="text" id="lock_exclude_date"><br>';
			$html .= '<p><a class="button-primary" id="auto_unlock_launch" >' . __( 'Start', 'wbk' ) . '</a></p>';
			$html .= $dateformat_source;

	 		echo $html;
	 		date_default_timezone_set( 'UTC' );
			die();
	 		return;

 		} 
		if ( $tool == 'auto_lock_timeslot' ){
		 		$html = '<label for="lock_service_list">' . __( 'Select service', 'wbk' ) . ':</label><br>';
		 		$html .= '<select class="wbk_input_500" id="lock_service_list">';
				$html .= '<option value="-1" >' . __( 'select...', 'wbk' ) . '</option>';
				$arrIds = WBK_Db_Utils::getServices();
		 		if ( count( $arrIds ) > 0 ) {
			 		foreach ( $arrIds as $id ) {
						// check access
						if ( !in_array( 'administrator', $current_user->roles ) ) {
							if ( !WBK_Validator::checkAccessToService( $id ) ) {							
	 							continue;
							}    	
						}
			 			$service = new WBK_Service();
			 			if ( !$service->setId( $id ) ) {  
			 				continue;
			 			}
			 			if ( !$service->load() ) {  	 				 
			 				continue;
			 			}
			 			$html .= '<option value="'. $id .'" >' . $service->getName() . '</option>';
			 		}
			 	}
				$html .= '</select><br>';
				$html .= '<label for="lock_service_list">' . __( 'or Service category', 'wbk' ) . ':</label><br>';
		        $html .= '<select class="wbk_input_500" id="lock_category_list">';
				$html .= '<option value="-1" selected="selected">' . __( 'select...', 'wbk' ) . '</option>';
				$arrIds = WBK_Db_Utils::getServiceCategoryList();
				foreach ( $arrIds as $key => $value ) {
			 		$html .=  '<option value="' . $key . '"" >' . $value . '</option>';
				}
		 		$html .= '</select><br>';
			    $time_format = WBK_Date_Time_Utils::getTimeFormat();
				$html_time_options = '';
	    		date_default_timezone_set( 'UTC' );
				for( $time = 0; $time <= 86400;  $time += 900 ) {
		            $temp_time = $time;
		            if ( $time == 0 ){
		            	$selected = ' selected ';
		            } else {
		            	$selected = '';
		            }

		            $html_time_options .= '<option ' . $selected . ' value="' . $temp_time . '">' . date_i18n ( $time_format, $time ) . '</option>';
		        }	         
	        	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );	 
				$html .= '<label for="lock_date_range">' . __( 'Lock time slots on date range', 'wbk' ) . ':</label><br>';
				$html .= '<input class="wbk_input_500" type="text" id="lock_date_range"><br>';	 		
				$html .= 'from:<br> <select class="wbk_input" type="text" id="lock_time_start">' . $html_time_options . '</select><br>';
				$html .= 'to: <br> <select class="wbk_input" type="text" id="lock_time_end">' . $html_time_options . '</select><br>';
				$html .= '<p><a class="button-primary" id="auto_lock_time_slot_launch" >' . __( 'Start', 'wbk' ) . '</a></p>';
				$html .= $dateformat_source;	 			      	
		 		echo $html;
		 		date_default_timezone_set( 'UTC' );
				die();
		 		return;

		}
		if ( $tool == 'auto_unlock_timeslot' ){
	 	$html = '<label for="lock_service_list">' . __( 'Select service', 'wbk' ) . ':</label><br>';
	 		$html .= '<select class="wbk_input_500" id="lock_service_list">';
			$html .= '<option value="-1" >' . __( 'select...', 'wbk' ) . '</option>';
			$arrIds = WBK_Db_Utils::getServices();
	 		if ( count( $arrIds ) > 0 ) {
		 		foreach ( $arrIds as $id ) {
					// check access
					if ( !in_array( 'administrator', $current_user->roles ) ) {
						if ( !WBK_Validator::checkAccessToService( $id ) ) {							
 							continue;
						}    	
					}
		 			$service = new WBK_Service();
		 			if ( !$service->setId( $id ) ) {  
		 				continue;
		 			}
		 			if ( !$service->load() ) {  	 				 
		 				continue;
		 			}
		 			$html .= '<option value="'. $id .'" >' . $service->getName() . '</option>';
		 		}
		 	}
			$html .= '</select><br>';
			$html .= '<label for="lock_service_list">' . __( 'or Service category', 'wbk' ) . ':</label><br>';
	        $html .= '<select class="wbk_input_500" id="lock_category_list">';
			$html .= '<option value="-1" selected="selected">' . __( 'select...', 'wbk' ) . '</option>';
			$arrIds = WBK_Db_Utils::getServiceCategoryList();
			foreach ( $arrIds as $key => $value ) {
		 		$html .=  '<option value="' . $key . '"" >' . $value . '</option>';
			}
	 		$html .= '</select><br>';
		    $time_format = WBK_Date_Time_Utils::getTimeFormat();
			$html_time_options = '';
    		date_default_timezone_set( 'UTC' );
			for( $time = 0; $time <= 86400;  $time += 900 ) {
	            $temp_time = $time;
	            if ( $time == 0 ){
	            	$selected = ' selected ';
	            } else {
	            	$selected = '';
	            }

	            $html_time_options .= '<option ' . $selected . ' value="' . $temp_time . '">' . date_i18n ( $time_format, $time ) . '</option>';
	        }	         
        	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );	 
			$html .= '<label for="lock_date_range">' . __( 'Unlock time slots on date range', 'wbk' ) . ':</label><br>';
			$html .= '<input class="wbk_input_500" type="text" id="lock_date_range"><br>';	 		
			$html .= 'from:<br> <select class="wbk_input" type="text" id="lock_time_start">' . $html_time_options . '</select><br>';
			$html .= 'to: <br> <select class="wbk_input" type="text" id="lock_time_end">' . $html_time_options . '</select><br>';
			$html .= '<p><a class="button-primary" id="auto_unlock_time_slot_launch" >' . __( 'Start', 'wbk' ) . '</a></p>';
			$html .= $dateformat_source;	 			      	
	 		echo $html;
	 		date_default_timezone_set( 'UTC' );
			die();
	 		return;
		}
 		echo 'Attempting to load undefined tool.';
 		date_default_timezone_set( 'UTC' );
		die();
 		return;
 	}
}
?>