<?php
// Webba Booking service schedule management class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
require_once 'class_wbk_business_hours.php';
require_once 'class_wbk_appointment.php';
require_once 'class_wbk_service.php';
require_once 'class_wbk_time_slot.php';
require_once 'class_wbk_date_time_utils.php';
class WBK_Service_Schedule {
	// service id
	protected $service_id;
	// service
	protected $service;
	// result time slots for day
	protected $timeslots;
	// time slots locked manualy
	protected $locked_ts;
	// days locked manualy
	protected $locked_days;
	// days unlocked manualy
	protected $unlocked_days;
	// locked timeslots
	protected $locked_timeslots;
	// list of appointments for day
	protected $appointments;
	// business hours global option
	protected $busines_hours;
	// breakers (appointments, day break)
	protected $breakers;
	// Google calendar breakers (event imported from google)
	protected $gg_breakers;

 	// load custom locks / unlocks
 	public function load() {
 		// load locked and unlocked days
 		$this->loadLockedDays();
	 	$this->loadUnlockedDays();
	 	// load locked timeslots
		$this->loadLockedTimeSlots();
	 	// initalize service object
	 	$this->service = new WBK_Service();
	 	if ( $this->service->setId( $this->service_id ) ){
	 		if ( !$this->service->load() ){
	 			return false;
	 		}
	 	} else {
	 		return false;
	 	}
		$this->busines_hours = new WBK_Business_Hours();
 		$this->busines_hours->load( $this->service->getBusinessHours() );
	 	return true;
 	}
	// set service id
	public function setServiceId( $value ) {
		if ( WBK_Validator::checkInteger( $value, 1, 99999 ) ){
			$this->service_id = $value;
			return true;
		} else {
		 
			return false;
		}
	}	
	// full schedule for day
	public function buildSchedule( $day, $ignore_optimization = false, $skip_gg_calendar = false ){
		$this->day = $day;
		$this->breakers = array();
		$this->gg_breakers = array();
		// load appointments
		$this->loadAppointmentsDay( $day );
		// output
		$html = '';
		$arr_hours =  $this->busines_hours->getBusinessHours( $day );
 
		
		$tz = new DateTimeZone( get_option( 'wbk_timezone', 'UTC' ) ); 
		$transition = $tz->getTransitions( $day, $day ); 
		$offset1 = $transition[0]['offset']; 	 
		$next_day = strtotime('+1 day', $day );
	 	$transition = $tz->getTransitions($next_day, $next_day ); 
		$offset2 = $transition[0]['offset']; 
		$difference = $offset1 - $offset2;
		


		$count = count( $arr_hours );
		if ( $count != 2 && $count != 4 ) {
			return;
		}
		$betw_interval = $this->service->getInterval() * 60;
		$duration = $this->service->getDuration() * 60;
		if( get_option( 'wbk_multi_booking', 'disabled' ) == 'disabled' ){
			$step = $this->service->getStep() * 60;			 
		} else {			 
			$step = $duration + $betw_interval;
		}	
		$this->timeslots = array();
		$total_duration_optim = $betw_interval + $duration; 
		// interval 1 output
		$start = $day + $arr_hours[0] - 2; 
		$end = $day + $arr_hours[1] - 2;	 		  
		if( $difference != 0 ){
			if ( date( 'I', $start ) == date( 'I', $end ) ){
				$start += $difference;	 	
			}
			$end += $difference;
		}
		for ( $time = $start; $time < $end; $time += $step ) {			 			 
			$temp = $time + $duration + $betw_interval;
			$total_duration = $duration + $betw_interval;
			if ( $temp > $end ) {
				continue;
			}
			$status = $this->timeSlotStatus( $time, $total_duration );
			if ( $status == -1 ) {
				continue;
			}
			$slot = new WBK_Time_Slot( $time, $temp );
 			$slot->setStatus( $status );
 			array_push( $this->timeslots, $slot );
 			if( $difference > 0 ){
				if( date( 'I', $time ) != date( 'I', $temp ) ){
					$time += $difference;				 
				}  
			}
		}
		// interval 2
		if ( $count == 4 ) {
			$start = $day + $arr_hours[2] - 2;
			$end = $day + $arr_hours[3] - 2;
			if( $difference > 0 ){
				if ( date( 'I', $start ) == date( 'I', $end ) ){
					$start += $difference;	 	
				}
				$end += $difference;
			}
			$total_duration = $duration + $betw_interval;
			for ( $time = $start; $time < $end; $time += $step ) {				 				 
				$temp = $time + $duration + $betw_interval;
				if( $difference > 0 ){
					if( date( 'I', $time ) != date( 'I', $temp ) ){
						$time += $difference;
						$temp = $time + $duration + $betw_interval;
					}
				}
				if ( $temp > $end ) {
					continue;
				}
				$status = $this->timeSlotStatus( $time, $total_duration );
				if ( $status == -1 ){
					continue;
				}
				$slot = new WBK_Time_Slot( $time, $temp );
	 			$slot->setStatus( $status );
	 			array_push( $this->timeslots, $slot );
	 			if( $difference > 0 ){
					if( date( 'I', $time ) != date( 'I', $temp ) ){
						$time += $difference;				 						 
					}
				}
			}
		}
		// check for not attached appointments
		$need_sort = false;
		foreach ( $this->appointments as $appointment ) {
			$appointment_found = false;
			foreach ( $this->timeslots as $timeslot ) {
				if ( $appointment->getTime() == $timeslot->getStart() ) {
					$appointment_found = true;
				}
			}
			if ( !$appointment_found ) {				
				$temp = $appointment->getTime() + $duration + $betw_interval;
				$slot = new WBK_Time_Slot( $appointment->getTime(), $temp );
	 			$slot->setStatus( $appointment->getId() );
	 			array_push( $this->timeslots, $slot );
	 			$need_sort = true;
			}
		}
		if ( $need_sort ) {
			$arr_temp = array();
			foreach ( $this->timeslots as $timeslot ) {
				array_push( $arr_temp, $timeslot->getStart() );
			}
			array_multisort( $this->timeslots, $arr_temp  );
		}
		

		if( $ignore_optimization == true ){
			return;
		}
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$optimization = get_option( 'wbk_time_hole_optimization', 'disabled' );
		if( $optimization == 'disabled' ){
			return;
		}

		$up_limit = -1;
		$temp_timeslots = array();
		foreach( $this->timeslots as $timeslot ) {
			// not free or multiple slots
			if( $timeslot->getStatus() != 0 ){
				array_push( $temp_timeslots, $timeslot );
				continue;
			}
			// free and single slots
			if( $up_limit == -1 ){
				array_push( $temp_timeslots, $timeslot );
				$up_limit = $timeslot->getStart() + $total_duration_optim;
				continue;
			}
			if( $timeslot->getStart() < $up_limit ){
				continue;
			} else {
				array_push( $temp_timeslots, $timeslot );
				$up_limit = $timeslot->getStart() + $total_duration_optim;
				continue;
			}
		}

		$this->timeslots = $temp_timeslots;

	}
	// render select options for free appointments including given appointment_id
	// -1 means appointment not provided
	// REMOVE IN FUTURE RELEASE
	public function renderSelectOptionsFreeTimslot( $appointment_id ){	
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		foreach ( $this->timeslots as $timeslot ) {
			$time = date_i18n( $time_format, $timeslot->getStart() ); 
			// group booking
			if( is_array( $timeslot->getStatus() ) || ( $timeslot->getStatus() == 0 && $this->service->getQuantity() > 1  ) ){
				$available = $this->getAvailableCount( $timeslot->getStart() );
				if ( $available > 0 || in_array( $appointment_id,  $timeslot->getStatus() ) ){
					$selected = '';
					if ( in_array( $appointment_id, $timeslot->getStatus() ) ){
						$selected = 'selected';
						$appointment = new WBK_Appointment();
						if ( !$appointment->setId( $appointment_id ) ) {
							continue;
						};
						if ( !$appointment->load() ) {
							continue;
						};
						$available = $available . '+' . $appointment->getQuantity();
					}
					$available_lablel = get_option( 'wbk_time_slot_available_text', __( 'available', 'wbk' ) ); 
					if( $available_lablel == '' ){
						global $wbk_wording;
						$available_lablel = $wbk_wording['wbk_time_slot_available_text'];
					}
					$html .= '<option ' . $selected . ' value="' . $timeslot->getStart() . '">'. $time . ' ('. $available_lablel . ' ' . ')</option>';

				}
			} 

			if( $timeslot->getStatus() == $appointment_id || ( $timeslot->getStatus() == 0 &&  $this->service->getQuantity() == 1 )  ){
					$selected = '';
					if (  $timeslot->getStatus()== $appointment_id ){
						$selected = 'selected';
					}
					$html .= '<option ' . $selected . '  value="' . $timeslot->getStart() . '">'. $time .'</option>';
				 
			}
		}
		return $html;
	}
	// get array of free time slots
	public function getFreeTimeslotsPlusGivenAppointment( $appointment_id ){	
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$result = array();
  		$result[]  = array( __( 'Select time slot', 'wbk' ), 0  );
		foreach ( $this->timeslots as $timeslot ) {
			if( get_option( 'wbk_date_format_time_slot_schedule', 'start' ) == 'start' ){
				$time = date( $time_format, $timeslot->getStart() );	
			} else {
				$time = date( $time_format, $timeslot->getStart() ) . ' - ' . date( $time_format, $timeslot->getStart() + $this->service->getDuration() * 60 );
			}			 
			// group booking
			if( is_array( $timeslot->getStatus() ) || ( $timeslot->getStatus() == 0 && $this->service->getQuantity() > 1  ) ){
				$available = $this->getAvailableCount( $timeslot->getStart() );
 				if ( $available > 0 || in_array( $appointment_id,  $timeslot->getStatus() ) ){
 					if( is_array( $timeslot->getStatus() ) ){
						if ( in_array( $appointment_id, $timeslot->getStatus() ) ){					 
							$appointment = new WBK_Appointment();
							if ( !$appointment->setId( $appointment_id ) ) {
								continue;
							};
							if ( !$appointment->load() ) {
								continue;
							};
							$available = $available + $appointment->getQuantity();
						}
					}
					$available_lablel = get_option( 'wbk_time_slot_available_text', __( 'available', 'wbk' ) ); 
					if( $available_lablel == '' ){
						global $wbk_wording;
						$available_lablel = $wbk_wording['wbk_time_slot_available_text'];
					}
					$opt_name = $time . ' (' . $available . ' ' . $available_lablel . ')';
					$result[ $timeslot->getStart() ] = array( $opt_name, $available );
				}
			} 
			if( $timeslot->getStatus() == $appointment_id || ( $timeslot->getStatus() == 0 &&  $this->service->getQuantity() == 1 )  ){
				$result[ $timeslot->getStart() ] = array( $time, 1 );			
			}
		}
		return $result;
	}

	// render shcedule for day for backend
	public function renderDayBackend() {
		$html = '';
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		foreach ( $this->timeslots as $timeslot ) {
			$time = date( $time_format, $timeslot->getStart() );

			if( $this->service->getQuantity() == 1 ){
				if( get_option( 'wbk_appointments_auto_lock', 'disabled' ) == 'enabled' ){
					if( get_option( 'wbk_appointments_auto_lock_allow_unlock', 'allow' ) == 'disallow' ){
						$connected_quantity = WBK_Db_Utils::getQuantityFromConnectedServices2( $this->service->getId(), $timeslot->getStart() );                    
						if( $connected_quantity > 0 ){
							continue;
						}
					}					 		
				}
			}	
				if( get_option( 'wbk_date_format_time_slot_schedule', 'start' ) == 'start' ){
					$time = date( $time_format, $timeslot->getStart() );	
				} else {
					$time = date( $time_format, $timeslot->getStart() ) . ' - ' . date( $time_format, $timeslot->getStart() + $this->service->getDuration() * 60 );
				}

			$status_class = '';
			$time_controls = '<a id="time_lock_' . $this->service_id . '_' . $timeslot->getStart() . '"><span class="dashicons dashicons-unlock"></span></a>';
			$time_controls = '<a id="app_add_' . $this->service_id . '_' . $timeslot->getStart() . '"><span class="dashicons dashicons-welcome-add-page"></span></a>' . $time_controls;
			if( is_array( $timeslot->getStatus() ) ){
				$time_controls = '';
				$items_booked = 0;
				foreach ( $timeslot->getStatus() as $app_id ) {
					$appointment = new WBK_Appointment();
					if ( !$appointment->setId( $app_id ) ) {
						continue;
					};
					if ( !$appointment->load() ) {
						continue;
					};
					$items_booked += $appointment->getQuantity();
				 	$time_controls .= '<a class="wbk-appointment-backend" id="wbk_appointment_' .  $app_id . '_'. $this->service_id .'_1" >' . $appointment->getName() . ' ('. $appointment->getQuantity() . ')' . '</a> ';
				}
				if ( $items_booked < $this->service->getQuantity() ) {
					$time_controls .= '<a id="app_add_' . $this->service_id . '_' . $timeslot->getStart() . '"><span class="dashicons dashicons-welcome-add-page"></span></a>';
				}
			}
			if ( $timeslot->getStatus() == -2 ) {
				$status_class = 'red_font';
			 	$time_controls = '<a class="red_font" id="time_unlock_' . $this->service_id . '_' . $timeslot->getStart() . '"><span class="dashicons dashicons-lock"></span></a></a>';
			}
			if ( $timeslot->getStatus() > 0  && !is_array( $timeslot->getStatus() ) ) {
				$appointment = new WBK_Appointment();
				if ( !$appointment->setId( $timeslot->getStatus() ) ) {
					continue;
				};
				if ( !$appointment->load() ) {
					continue;
				};
				if ( strlen( $appointment->getName() ) > 9 ){
					$name = substr( $appointment->getName(), 0, 8 ) . '...';
				} else {
					$name = $appointment->getName();
				}
				$name = WBK_Db_Utils::backend_customer_name_processing( $appointment->getId(), $name );
				

			 	$time_controls = '<a class="wbk-appointment-backend" id="wbk_appointment_' .  $appointment->getId() . '_'. $this->service_id .'_1" >' . $name . '</a>';
			}
			$html .= '<div class="timeslot_container">
						<div class="timeslot_time ' . $status_class . '">'.
							$time.
						'</div>
						<div class="timeslot_controls">'.
							$time_controls.'
						</div>
						<div class="cb"></div>
					  </div>';
		}
		return $html;
	}
	// render for past day for backend
	public function renderPastDayBackend() {
		$html = '';
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		foreach ( $this->timeslots as $timeslot ) {
			$time = date( $time_format, $timeslot->getStart() );
		 	if ( $timeslot->getStatus() > 0 ) {
				$appointment = new WBK_Appointment();
				if ( !$appointment->setId( $timeslot->getStatus() ) ) {
					continue;
				};
				if ( !$appointment->load() ) {
					continue;
				};
				if ( strlen( $appointment->getName() ) > 9 ){
					$name = substr( $appointment->getName(), 0, 8 ) . '...';
				} else {
					$name = $appointment->getName();
				}
				$time_controls = '<a class="wbk-appointment-backend" id="wbk_appointment_' .  $appointment->getId() . '_'. $this->service_id .'_0" >' . $name . '</a>';
			} else {
				continue;
			}
			$html .= '<div class="timeslot_container">
						<div class="timeslot_time">'.
							$time.
						'</div>
						<div class="timeslot_controls">'.
							$time_controls.'
						</div>
						<div class="cb"></div>
					  </div>';
		}
		return $html;
	}
	// get timeslot status. 0 - free timeslot
	public function timeSlotStatus( $time, $duration ) {
		$start = $time;
		$end = $time + $duration;
		// check breakers
		foreach ( $this->breakers as $breaker ) {
			if ( $start > $breaker->getStart() && $start < $breaker->getEnd() ) {
				return -1;
			}
			if ( $end > $breaker->getStart() && $end < $breaker->getEnd() ) {
				return -1;
			}
		}
		// check locked timeslots 
 		if ( in_array( $start, $this->locked_timeslots ) ) {
 			return -2;
 		}
		// check appointments
		if ( $this->service->getQuantity() == 1 ) {
			foreach ( $this->appointments as $appointment ) {
				if ( $time == $appointment->getTime() ){
					return $appointment->getId();
				}
			}
		} else {
			$booking_ids = array();
			foreach ( $this->appointments as $appointment ) {
				if ( $time == $appointment->getTime() ){
					array_push( $booking_ids, $appointment->getId() );
				}
			}
			if ( count( $booking_ids ) > 0 ) {
				return $booking_ids;
			}
		}
		return 0;

	}
	// load locked days for service
	public function loadLockedDays() {
		global $wpdb;
		$days = $wpdb->get_col( $wpdb->prepare(
						"
						SELECT day
						FROM wbk_days_on_off
						where service_id = %d AND status = 0
						",
						$this->service_id
					));
		$this->locked_days = array();
		$this->locked_days = array_merge( $this->locked_days, $days );
	}
	// load unlocked days for service
	public function loadUnlockedDays() {
		global $wpdb;
		$days = $wpdb->get_col( $wpdb->prepare(
						"
						SELECT day
						FROM wbk_days_on_off
						where service_id = %d AND status = 1
						",
						$this->service_id
					));
		$this->unlocked_days = array();
		$this->unlocked_days = array_merge( $this->unlocked_days, $days );
	}
	// load unlocked days for service
	public function loadLockedTimeSlots() {
		global $wpdb;
		$timeslots = $wpdb->get_col( $wpdb->prepare(
						"
						SELECT time
						FROM wbk_locked_time_slots
						where service_id = %d",
						$this->service_id
					));
		$this->locked_timeslots = array();
		$this->locked_timeslots = array_merge( $this->locked_timeslots, $timeslots );
	}
	// get day status working / weekend
	// 1 - working, 0 - weekend
	public function getDayStatus( $day ){
		// check manual arrays
		if ( in_array( $day, $this->locked_days ) === true ){
		 	return 0;
		}
		// check manual arrays
		if ( in_array( $day, $this->unlocked_days ) === true ){
		 	return 1;
		}
		// check global holyday option
		if ( $this->busines_hours->checkIfHolyday( $day )  === true ) {
		 	return 0;
		}
		// check global weekly options
		if ( $this->busines_hours->isWorkdayTime( $day )  === true ) {
			return 1;
		} else {
			return 0;
		}
	}
	// load all appoitments from db for given day
	public function loadAppointmentsDay( $day ) {
		global $wpdb;
		$db_arr = $wpdb->get_results( $wpdb->prepare( "
													SELECT *
													FROM wbk_appointments
													where service_id = %d AND day = %d
													",
													$this->service_id,
													$day
													) );
		$this->appointments  = array();
		if ( count($db_arr ) == 0 ) {
			return 0;
		}
		foreach ( $db_arr as $item ) {
			$appointment = new WBK_Appointment();
			if ( $appointment->set( $item->id,
								    $item->name,
									$item->description,
									$item->email,
									$item->duration,
									$item->time,
									$item->day,
									$item->phone,
									$item->extra,
									$item->attachment,
									$item->quantity ) ){
				array_push( $this->appointments, $appointment );
				// create breaker
				$service = new WBK_Service();
				if ( !$service->setId( $this->service_id ) ) {
					continue;
				}
				if ( !$service->load() ) {
		 			continue;
				}
				if( $service->getQuantity() == 1 ) {
					$betw_interval = $this->service->getInterval();
					$app_end = $item->time + $item->duration * 60 + $betw_interval * 60;
					$breaker = new WBK_Time_Slot( $item->time, $app_end );
					array_push( $this->breakers, $breaker );
				}
			};
		}
		return;
	}
	// add break for day
	public function addBusinessHoursBreak( $day ) {
		$arr = $this->busines_hours->getBusinessHours( $day );
		if ( count( $arr) == 4 ) {
			$start = $day + $arr[1];
			$end = $day + $arr[2];
			$breaker = new WBK_Time_Interval( $start, $end );
			array_push( $this->breakers, $breaker );
		}
	}
	public function getFirstAvailableTime(){
		foreach ( $this->timeslots as $timeslot ) {
			if ( $timeslot->getStatus() == 0 ){
				return $timeslot->getStart();
			}
			if ( is_array( $timeslot->getStatus() ) ){
				if( $this->service->getQuantity() > 1 ){
					if( $this->getAvailableCount( $timeslot->getStart() ) > 0 ){
						return $timeslot->getStart();
					}
				}
			}
		}
		return 0;
	}
	// frontend render
	public function renderDayFrontend( $time_after, $offset ){
		global $wbk_wording;
		$html = '';
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_slots = '';
		$timeslot_format = get_option( 'wbk_timeslot_format', 'detailed' );		
		$night_houts_addon = get_option( 'wbk_night_hours', '0' ) * 60* 60;
		 
		if( !WBK_Validator::checkInteger( $night_houts_addon, 0, 64800 ) ){
			$night_houts_addon = 0;
		} else {
			$service_schedule_tomorrow = new WBK_Service_Schedule();
			$service_schedule_tomorrow->setServiceId( $this->service_id );
			$service_schedule_tomorrow->load();
			$tomorrow =  strtotime( '+1 day', $this->day );
			$service_schedule_tomorrow->buildSchedule( $tomorrow );
			$tomorrows_time_slots = $service_schedule_tomorrow->getTimeSlots();		 
			foreach ( $tomorrows_time_slots as $timeslot ) {
				if( $timeslot->getStart() < ( $tomorrow + $night_houts_addon ) ){
					$this->timeslots[] = $timeslot;
				}
			}
		}
		foreach ( $this->timeslots as $timeslot ) {
			// night hours 			
			if( $night_houts_addon != 0 ){					 					
				if( $timeslot->getStart() < ( $this->day + $night_houts_addon ) ){							 
					continue;
				}
			}			
			
			if( get_option( 'wbk_appointments_auto_lock', 'disabled' ) == 'enabled' ){
				if( get_option( 'wbk_appointments_auto_lock_allow_unlock', 'allow' ) == 'disallow' ){
					$connected_quantity = WBK_Db_Utils::getQuantityFromConnectedServices2( $this->service->getId(), $timeslot->getStart() );					
					if( $connected_quantity > 0 ){
						if( $this->service->getQuantity() == 1 ){
							$timeslot->setStatus( -2 );							
						} else {
							if(  get_option( 'wbk_appointments_auto_lock_group', 'lock' ) == 'lock' ){
								$timeslot->setStatus( -2 );		
							}
						}
					}					 
				}					 		
			}
							
			$timeslot_time_string = get_option( 'wbk_timeslot_time_string', 'start' );
			if( $timeslot_time_string == 'start' ){
				$time = date_i18n( $time_format, $timeslot->getStart() );
				if( get_option( 'wbk_show_local_time', 'disabled' ) == 'enabled' || get_option( 'wbk_show_local_time', 'disabled' ) == 'enabled_only' ){
					$timezone = new DateTimeZone( get_option( 'wbk_timezone' ) );
					$current_offset =  $offset * - 60 - $timezone->getOffset( new DateTime );
					$local_start = $timeslot->getStart() + $current_offset;
					$local_start = date_i18n( $time_format, $local_start );

					$local_start_date = $timeslot->getStart() + $current_offset;
					$local_start_date = date_i18n( $date_format, $local_start_date );

					$local_time_str = get_option( 'wbk_local_time_format', 'Your local time:<br>#ds<br>#ts' );

					$local_time_str = str_replace( '#ts', $local_start, $local_time_str );
					$local_time_str = str_replace( '#ds', $local_start_date, $local_time_str );

					
				} else {
					$local_time_str = '';
				}
			}
			if( $timeslot_time_string == 'start_end' ){
				$end_minus_gap = $timeslot->getEnd() - $this->service->getInterval() * 60;
				$time = date_i18n( $time_format, $timeslot->getStart() ) . ' - ' . date_i18n( $time_format,  $end_minus_gap ) ;
				if(  get_option( 'wbk_show_local_time', 'disabled' ) == 'enabled' || get_option( 'wbk_show_local_time', 'disabled' ) == 'enabled_only'  ){
					$timezone = new DateTimeZone( get_option( 'wbk_timezone' ) );

					$current_offset =  $offset * -60 - $timezone->getOffset( new DateTime );		
					$local_start = $timeslot->getStart() + $current_offset;
					$local_start = date_i18n( $time_format, $local_start );
					$local_end = $timeslot->getEnd() + $current_offset;
					$local_end = date_i18n( $time_format, $local_end );
					$local_start_date = $timeslot->getStart() + $current_offset;
					$local_start_date = date_i18n( $date_format, $local_start_date );

					$local_time_str = get_option( 'wbk_local_time_format', 'Your local time:<br>#ds<br>#ts - #te' );
					$local_time_str= str_replace( '#ts', $local_start, $local_time_str );
					$local_time_str= str_replace( '#te', $local_end, $local_time_str );
					$local_time_str= str_replace( '#ds', $local_start_date, $local_time_str );
					
				} else {
					$local_time_str = '';
				}
			} 
			if ( $timeslot->getStatus() == 0 || is_array( $timeslot->getStatus()  ) ) {
				if ( $timeslot->getStart() >= $time_after ) {
					if ( $timeslot->getStart() > time() ) {
						$slot_html = '';
						if ( $timeslot_format == 'detailed' ){
							$available_html = '';
							$available_count = '';
							if ( $this->service->getQuantity() > 1 ){
								$available_count = $this->getAvailableCount( $timeslot->getStart() );
								if ( $available_count == 0 ) {
									if( get_option( 'wbk_show_booked_slots', 'disabled' ) == 'disabled' ){
										continue;
									}									 
								}
								$available_lablel = get_option( 'wbk_time_slot_available_text', __( 'available', 'wbk' ) ); 
								if( $available_lablel == '' ){
									global $wbk_wording;
									$available_lablel = $wbk_wording['wbk_time_slot_available_text'];
								}
								$available_html = '<div class="wbk-slot-available"><span class="wbk-abailable-container">'. $available_count . '</span> ' . $available_lablel .'</div>';
							}
							$book_text = get_option( 'wbk_book_text_timeslot', '' );
							if ( $book_text == '' ){
								$book_text =  sanitize_text_field( $wbk_wording['book_text'] );
							}
							if( $available_count > 0 || $this->service->getQuantity() == 1   ){
							$book_button = '<input type="button" data-end="' . $timeslot->getEnd() . '"  data-start="' . $timeslot->getStart() . '"  value="' . $book_text .'" id="wbk-timeslot-btn_' . $timeslot->getStart() . '" data-available="' . $available_count . '"   data-service="' . $this->service->getId() . '"  class="wbk-slot-button" />';
							} else {
								$slot_button =  get_option ( 'wbk_booked_text', '' );
								if( $slot_button == '' ){
									$slot_button = sanitize_text_field( $wbk_wording['booked_text'] );
								}		 
								$book_button = '<input type="button" data-service="' . $this->service->getId() . '" value="' . $slot_button .'" class="wbk-slot-button wbk-slot-booked" />';
							}
							if( get_option( 'wbk_show_local_time', 'disabled' ) == 'enabled_only' ){
								$time = '';
							} else {
								$time .= '<br>';
							}
							$slot_html ='<div class="wbk-slot-time">' . $time  . $local_time_str . '</div>'. $available_html . $book_button;
						} else {
							if ( $this->service->getQuantity() > 1 ){
								$available_count = $this->getAvailableCount( $timeslot->getStart() );
								if ( $available_count == 0 ) {
									if( get_option( 'wbk_show_booked_slots', 'disabled' ) == 'disabled' ){
										continue;
									}	
								}
							}
							$slot_html = '<input type="button"  data-service="' . $this->service->getId() . '"  data-end="' . $timeslot->getEnd() . '"  data-start="' . $timeslot->getStart() . '" value="' . $time .'" id="wbk-timeslot-btn_' . $timeslot->getStart() . '" data-available="' . $available_count . '"   class="wbk-slot-button" />';
						}

						
						$availability = '';
						$time_slots .=
							'<li class="wbk-col-4-6-12">
								<div class="wbk-slot-inner">'.
									$slot_html
								.'</div>
							</li>';


					}
				}
			};
			if( get_option( 'wbk_show_locked_as_booked', 'no' ) == 'yes' ){
				if( $timeslot->getStatus() == -2 ){
					if( get_option( 'wbk_show_booked_slots', 'disabled' ) == 'enabled'){
						$slot_button =  get_option ( 'wbk_booked_text', '' );
						if( $slot_button == '' ){
							$slot_button = sanitize_text_field( $wbk_wording['booked_text'] );
						}		 
						if ( $timeslot_format == 'detailed' ){
							$slot_html = '<div class="wbk-slot-time">' .
												$time .
											'</div>
											<input  data-service="' . $this->service->getId() . '" type="button" value="' . $slot_button .'" class="wbk-slot-button wbk-slot-booked" />';
						} else {
							$slot_html = '<input  data-service="' . $this->service->getId() . '"  type="button" value="' . $slot_button .'" class="wbk-slot-button wbk-slot-booked" />';

						}
						$time_slots .=
								'<li class="wbk-col-4-6-12">
									<div class="wbk-slot-inner">'.
										$slot_html
									.'</div>
								</li>';
					}
				}
			}
		  	if( $timeslot->getStatus() > 0 && !is_array( $timeslot->getStatus() ) ) {
				$show_booked_slots = get_option( 'wbk_show_booked_slots', 'disabled' );
				if( $show_booked_slots == 'enabled'){
					$slot_button =  get_option ( 'wbk_booked_text', '' );
					if( $slot_button == '' ){
						$slot_button = sanitize_text_field( $wbk_wording['booked_text'] );
					}
					// replace placeholders
					// name
					$appointment = new WBK_Appointment();
					if ( !$appointment->setId( $timeslot->getStatus() ) ) {
						continue;
					};
					if ( !$appointment->load() ) {
						continue;
					};
					$customer_name = $appointment->getName();
					$slot_button = str_replace( '#username', $customer_name, $slot_button );
					// $time
					$slot_button = str_replace( '#time', $time, $slot_button );
					// end replace placeholders
					if ( $timeslot_format == 'detailed' ){
						if( get_option( 'wbk_show_local_time', 'disabled' ) == 'enabled_only' ){
								$time = '';
							} else {
								$time .= '<br>';
							}
						$slot_html ='
										<div class="wbk-slot-time">' .
											$time .  $local_time_str .
										'</div>
										<input type="button" value="' . $slot_button .'" class="wbk-slot-button wbk-slot-booked" />';
					} else {
						$slot_html ='
										<input type="button" value="' . $slot_button .'" class="wbk-slot-button wbk-slot-booked" />';

					}
					$time_slots .=
							'<li class="wbk-col-4-6-12">
								<div class="wbk-slot-inner">'.
									$slot_html
								.'</div>
							</li>';
				}
			}
 		}


 		if ( $time_slots != '' ) {
			$html = '<ul class="wbk-timeslot-list">';
			$html .= $time_slots;
			$html .= '</ul>';
 		}
		return $html;
	}
	public function fableCount( $time ){
		global $wpdb;
		$service = new WBK_Service();
 		if( !$service->setId( $this->service_id ) ){
 			return 0;
 		}
 		if( !$service->load() ){
 			return 0;
 		}
 		$total_duration= $service->getDuration() * 60 + $service->getInterval() * 60;
  		$booked = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  time = %d",
									$this->service_id,
									$time
								)
 								);
 		if ( $booked === NULL ){
 			$booked = 0;
 		}
		$booked2 = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  ( time < %d AND ( time + %d ) > %d)",
									$this->service_id, $time, $total_duration, $time
								  )
 								);
 		if ( $booked2 === NULL ){
 			$booked2 = 0;
 		}
		$booked3 = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  ( time > %d AND time < ( %d + %d ) )",
									$this->service_id, $time, $time, $total_duration
								  )
 								);

 		if ( $booked3 === NULL ){
 			$booked3 = 0;
 		}

 		$booked = $booked + $booked2 + $booked3; 		
 		$available = $service->getQuantity() - $booked;
 		$end = $time + $service->getDuration() * 60 + $service->getInterval() * 60;	 
		$connected_quantity = WBK_Db_Utils::getQuantityFromConnectedServices2( $this->service_id, $time );		 
		$available = $available - $connected_quantity;
		if ( $available < 0 ){
			$available = 0;
		}
		return $available;
	}
	public function getAvailableCount( $time ){
		global $wpdb;
		$service = new WBK_Service();
 		if( !$service->setId( $this->service_id ) ){
 			return 0;
 		}
 		if( !$service->load() ){
 			return 0;
 		}
 		$total_duration= $service->getDuration() * 60 + $service->getInterval() * 60;
  		$booked = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  time = %d",
									$this->service_id,
									$time
								)
 								);
 		if ( $booked === NULL ){
 			$booked = 0;
 		}
		$booked2 = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  ( time < %d AND ( time + %d ) > %d)",
									$this->service_id, $time, $total_duration, $time
								  )
 								);
 		if ( $booked2 === NULL ){
 			$booked2 = 0;
 		}
		$booked3 = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  ( time > %d AND time < ( %d + %d ) )",
									$this->service_id, $time, $time, $total_duration
								  )
 								);

 		if ( $booked3 === NULL ){
 			$booked3 = 0;
 		}

 		$booked = $booked + $booked2 + $booked3; 		
 		$available = $service->getQuantity() - $booked;
 		$end = $time + $service->getDuration() * 60 + $service->getInterval() * 60;	 
		$connected_quantity = WBK_Db_Utils::getQuantityFromConnectedServices2( $this->service_id, $time );		 
		$available = $available - $connected_quantity;
		if ( $available < 0 ){
			$available = 0;
		}
		return $available;
	}
	public function getAvailableCountSingle( $time ){
		global $wpdb;
		$service = new WBK_Service();
 		if( !$service->setId( $this->service_id ) ){
 			return 0;
 		}
 		if( !$service->load() ){
 			return 0;
 		}
 		$total_duration= $service->getDuration() * 60 + $service->getInterval() * 60;
  		$booked = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  time = %d",
									$this->service_id,
									$time
								)
 								);
 		if ( $booked === NULL ){
 			$booked = 0;
 		}
		$booked2 = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  ( time < %d AND ( time + %d ) > %d)",
									$this->service_id, $time, $total_duration, $time
								  )
 								);
 		if ( $booked2 === NULL ){
 			$booked2 = 0;
 		}
		$booked3 = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  ( time > %d AND time < ( %d + %d ) )",
									$this->service_id, $time, $time, $total_duration
								  )
 								);

 		if ( $booked3 === NULL ){
 			$booked3 = 0;
 		} 		 
 		$booked = $booked + $booked2 + $booked3; 		
 		return $booked;
	}
		
	public function getAvailableCountSingleRange( $start, $end ){
		global $wpdb;
		$service = new WBK_Service();
 		if( !$service->setId( $this->service_id ) ){
 			return 0;
 		}
 		if( !$service->load() ){
 			return 0;
 		}
 		$total_duration= $service->getDuration() * 60 + $service->getInterval() * 60;
  		$booked = $wpdb->get_var(
									$wpdb->prepare(	"SELECT sum(quantity) FROM wbk_appointments WHERE service_id = %d AND  ( time >= %d AND ( time + %d ) <= %d    )",
									$this->service_id,
									$start, 
									$total_duration,
									$end
								)
 								);
 		if ( $booked === NULL ){
 			$booked = 0;
 		}
		 	
 		return $booked;
	}
	public  function getTimeSlotStartForParticularTime( $time ){
		foreach ( $this->timeslots  as $timeslot ) {
 			if( $timeslot->isTimeIn( $time ) || $time == $timeslot->getStart()  ){
 				return $timeslot->getStart();
 			}
 		}
 		return FALSE;
	}
	// get free time slots in range
	public function getNotBookedTimeSlotsInRange( $start, $end ){ 
		$result = array();
		foreach ( $this->timeslots as $timeslot ) { 
			if( $timeslot->getStatus() == 0 ){
				if ( $timeslot->getStart() >= $start && $timeslot->getStart() < $end ){
					$result[] = $timeslot->getStart();
				}
			}
		}
		return $result;
	}
	public function getLockedTimeSlotsInRange( $start, $end ){ 
		$result = array();
		foreach ( $this->timeslots as $timeslot ) { 
			if( $timeslot->getStatus() == -2 ){
				if ( $timeslot->getStart() >= $start && $timeslot->getStart()  <= $end ){
					$result[] = $timeslot->getStart();
				}
			}
		}
		return $result;
	}
	// get free time slots 
	public function getNotBookedTimeSlots(){
		$result = array();
		foreach ( $this->timeslots as $timeslot ) { 
			if( $timeslot->getStatus() == 0 ){			 
					$result[] = $timeslot->getStart();				 
			}
		}
		return $result;
	}
	public function hasFreeTimeSlots(){
		foreach ( $this->timeslots as $timeslot ) { 
			if( $timeslot->getStatus() == 0 ){		
				return true;
			} 
			if ( is_array( $timeslot->getStatus() ) ){
				$available = $this->getAvailableCount( $timeslot->getStart() );
				if ( $available > 0 ){
					return true;
				}
			}
		}
		return false;
	}
	public function getTimeSlots(){
		return $this->timeslots;
	}
	public function getService(){
		return $this->service;
	}
	// loadAppointmentsDay( $day );
	public function	loadFromGGCalendar( $day ){	
		 
		return array();
 	}
	public function getAppointment(){
		return $this->appointments;
	} 
	public function parital_load1(){
		$this->breakers = array();
		$this->service = new WBK_Service();
	 	if ( $this->service->setId( $this->service_id ) ){
	 		if ( !$this->service->load() ){
	 			return false;
	 		}
	 	} else {
	 		return false;
	 	}
	}	
	// get diabilities depended on week_starts_on
	public function getWeekDisabilities(){
		$result = array();
		if(  WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){

			if( !$this->busines_hours->monday_workday ){
				$result[] = 1;
			}
			if( !$this->busines_hours->tuesday_workday ){
				$result[] = 2;
			}				
			if( !$this->busines_hours->wednesday_workday ){
				$result[] = 3;
			}				
			if( !$this->busines_hours->thursday_workday ){
				$result[] = 4;
			}				
			if( !$this->busines_hours->friday_workday ){
				$result[] = 5;
			}				
			if( !$this->busines_hours->saturday_workday ) {
				$result[] = 6;
			}				
			if( !$this->busines_hours->sunday_workday ){
				$result[] = 7;
			}
		
		} else {

			if( !$this->busines_hours->monday_workday ){
				$result[] = 2;
			}
			if( !$this->busines_hours->tuesday_workday ){
				$result[] = 3;
			}				
			if( !$this->busines_hours->wednesday_workday ){
				$result[] = 4;
			}				
			if( !$this->busines_hours->thursday_workday ){
				$result[] = 5;
			}				
			if( !$this->busines_hours->friday_workday ){
				$result[] = 6;
			}				
			if( !$this->busines_hours->saturday_workday ) {
				$result[] = 7;
			}				
			if( !$this->busines_hours->sunday_workday ){
				$result[] = 1;
			}
		}



 

		$result = implode( ';', $result );

		return $result;

	}
}
?>