<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Date_Time_Utils {
	// get date format option
	public static function getDateFormat () {
		$date_format =  trim ( get_option ( 'wbk_date_format' ) );
		if ( empty ( $date_format ) ) {
			$date_format = trim ( get_option ( 'date_format' ) );
				if ( empty ( $date_format ) ) {
					$date_format = 'l, F j';
				}
		}
		return $date_format;
	}
	// get start of week option
	public static function getStartOfWeek () {
		$start_of_week = get_option ( 'wbk_start_of_week' );
		if ( $start_of_week == 'wordpress' ) {
			$start_of_week = get_option ( 'start_of_week', 0 );
			if ( $start_of_week == 0 ) {
				$start_of_week = 'sunday';
			
			} else {
				$start_of_week = 'monday';
			}
		}
		if ( $start_of_week !== 'sunday' &&  $start_of_week !== 'monday' ){
			$start_of_week = 'sunday';
		}
		return $start_of_week;
	}
	// get time format option
	public static function getTimeFormat () {
		$time_format =  trim ( get_option ( 'wbk_time_format' ) );
		if ( empty ( $time_format ) ) {
			$time_format = trim ( get_option ( 'time_format' ) );
				if ( empty ( $time_format ) ) {
					$time_format = 'H:i';
				}
		}
		return $time_format;
	}
	// get start of current week
	public static function getStartOfCurrentWeek() {
		$start_of_week = WBK_Date_Time_Utils::getStartOfWeek();
		if ( $start_of_week == 'sunday' ){
			return strtotime( 'last sunday', strtotime('tomorrow') );
		} else {
			return strtotime( 'last monday', strtotime('tomorrow') );
		}
	}
	// get start of current week
	public static function getStartOfWeekDay( $day ) { 
		$start_of_week = WBK_Date_Time_Utils::getStartOfWeek();
		if ( $start_of_week == 'sunday' ){
			if( date( 'N', $day ) == '7' ) {
		   		return  $day; 
		    } else {
				return strtotime( 'last sunday', $day );
			}
		} else {
		   if( date( 'N', $day ) == '1' ) {
		   		return  $day; 
		   } else {
				return strtotime( 'last monday', $day );
		   } 
		}
	}
	// render business hours form 
    public static function renderBHForm() {  
        date_default_timezone_set( 'UTC' );     
        $business_hours = new WBK_Business_Hours();
        $business_hours->setDefault();
        $html =  WBK_Date_Time_Utils::render_business_hours_at_day( $business_hours, 'monday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_at_day( $business_hours, 'tuesday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_at_day( $business_hours, 'wednesday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_at_day( $business_hours, 'thursday' );        
        $html .= WBK_Date_Time_Utils::render_business_hours_at_day( $business_hours, 'friday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_at_day( $business_hours, 'saturday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_at_day( $business_hours, 'sunday' );  
        date_default_timezone_set( get_option( 'wbk_timezone' ,'UTC' ) );          
        return $html;
    }
    // render business hours for cell (string)
    public static function renderBHCell( $value ) {  
        date_default_timezone_set( 'UTC' );            
        $business_hours = new WBK_Business_Hours();
        $arr_bh = explode( ';', $value );
        $business_hours->setFromArray( $arr_bh );
        $html =  WBK_Date_Time_Utils::render_business_hours_cell_at_day( $business_hours, 'monday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_cell_at_day( $business_hours, 'tuesday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_cell_at_day( $business_hours, 'wednesday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_cell_at_day( $business_hours, 'thursday' );        
        $html .= WBK_Date_Time_Utils::render_business_hours_cell_at_day( $business_hours, 'friday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_cell_at_day( $business_hours, 'saturday' );
        $html .= WBK_Date_Time_Utils::render_business_hours_cell_at_day( $business_hours, 'sunday' );    
        date_default_timezone_set( get_option( 'wbk_timezone' ,'UTC' ) );                
        return $html;
    }
    // render hours for day
    public static function render_business_hours_at_day( $business_hours, $day ) {
        // prepare title
        if ( $day == 'monday' ){
            $day_name =  __( 'Monday', 'wbk' );
        }
        if ( $day == 'tuesday' ){
            $day_name =  __( 'Tuesday', 'wbk' );
        }
        
        if ( $day == 'wednesday' ){
            $day_name =  __( 'Wednesday', 'wbk' );
        }
        if ( $day == 'thursday' ){
            $day_name =  __( 'Thursday', 'wbk' );
        }
        if ( $day == 'friday' ){
            $day_name =  __( 'Friday', 'wbk' );
        }
        if ( $day == 'saturday' ){
            $day_name =  __( 'Saturday', 'wbk' );
        }
        if ( $day == 'sunday' ){
            $day_name =  __( 'Sunday', 'wbk' );
        }
        // create html for time lists
        $interval_count = $business_hours->getIntervalCount( $day );
        $time_format = WBK_Date_Time_Utils::getTimeFormat();
        if ( $business_hours->isWorkday( $day )  == true ) {
            $disabled = '';
        } else {
            $disabled = 'disabled';
        }
        
        // render interval 1
        $interval = $business_hours->getInterval( $day, 1 );
        
        if ( isset ( $interval ) && count ( $interval ) == 2 ){
            $start_time = $interval[0] - 2;
            $end_time   = $interval[1] - 2; 
        }  else {
            return;
        }
        
        // render "from" list for interval 1
        $html_interval_1_1 = '<select  class="wbk_select_no_border wbk-business-hours" id="int_1_1_' . $day . '" name="wbk_business_hours[]" >';
        for( $time = 0; $time <= 86400;  $time += 300 ) {
            $temp_time = $time + 2;
            $html_interval_1_1 .= '<option ' . selected( $start_time, $time, false ) . ' value="' . $temp_time . '">' . date_i18n ( $time_format, $time ) . '</option>';
          
        }
        $html_interval_1_1 .= '</select>';
        // render "to" list for interval 1
        $html_interval_1_2 = '<select  class="wbk_select_no_border wbk-business-hours" id="int_1_2_' . $day . '" name="wbk_business_hours[]" >';
        for( $time = 0; $time <= 86400;  $time += 300 ) {
            $temp_time = $time + 2;
            $html_interval_1_2 .= '<option ' . selected( $end_time, $time, false ) . ' value="' . $temp_time . '">' . date_i18n ( $time_format, $time ) . '</option>';
          
        }
        $html_interval_1_2 .= '</select>';
        // render interval 2
        if ( $interval_count == 2 ) {
            $interval = $business_hours->getInterval( $day, 2 );
            
            if ( isset ( $interval ) && count ( $interval ) == 2 ){
        
                $start_time = $interval[0] - 2;
                $end_time   = $interval[1] - 2; 
            }  else {
                return;
            }
            
            // render "from" list for interval 1
            $html_interval_2_1 = '<select class="wbk_select_no_border wbk-business-hours" id="int_2_1_' . $day . '" name="wbk_business_hours[]" >';
            for( $time = 0; $time <= 86400;  $time += 300 ) {
                $temp_time = $time + 2;
                $html_interval_2_1 .= '<option ' . selected( $start_time, $time, false ) . ' value="' . $temp_time . '">' . date_i18n ( $time_format, $time ) . '</option>';
              
            }
            $html_interval_2_1 .= '</select>';
            // render "to" list for interval 1
            $html_interval_2_2 = '<select class="wbk_select_no_border wbk-business-hours" id="int_2_2_' . $day . '" name="wbk_business_hours[]" >';
            for( $time = 0; $time <= 86400;  $time += 300 ) {
                $temp_time = $time + 2;
                $html_interval_2_2 .= '<option ' . selected( $end_time, $time, false ) . ' value="' . $temp_time . '">' . date_i18n ( $time_format, $time ) . '</option>';
              
            }
            $html_interval_2_2 .= '</select>';
        }
         
        $checkbox_val = (int) $business_hours->isWorkday( $day );
        $html = '<input type="checkbox"  value = "' . $checkbox_val . '"' . checked( $business_hours->isWorkday( $day ), true, false ) . ' id="chk_day_' . $day . '" />';
        $html .= '<input type="hidden" class="wbk-business-hours" name="wbk_business_hours[]" value = "' . $checkbox_val . '"' . ' id="chk_day_val_' . $day . '" />';
        $html .= '<label for="chk_day_' . $day . '">' . $day_name . '</label><br/>';
        $html .= '<div id="business_hours_' . $day . '_1" class="business_hours_container" >' . $html_interval_1_1 . ' - ' . $html_interval_1_2 . '</div>';
        if ( $interval_count == 2 ){
            $html .= '<div id="business_hours_' . $day . '_2" class="business_hours_container" >' . $html_interval_2_1 . ' - ' . $html_interval_2_2 . '</div>';
            $html .= '<div id="business_hours_' . $day . '_control" class="business_hours_control_container" >' .       
                        
                        ' <a href="javascript:removeInterval( &#39;' . $day . '&#39; )">' . __( 'Remove the second gap', 'wbk') . '</a> 
                      </div>';
        } else {
            $html .= '<div id="business_hours_' . $day . '_2" class="business_hours_container" ></div>';
            $html .= '<div id="business_hours_' . $day . '_control" class="business_hours_control_container" >' .       
                        
                        ' <a href="javascript:addInterval( &#39;' . $day . '&#39; )">' . __( 'Add the second gap', 'wbk') . '</a> 
                      </div>';
        }
        return $html;
    }
    // render hours for day (cell)
    public static function render_business_hours_cell_at_day( $business_hours, $day ) {        
        date_default_timezone_set( 'UTC' );                 
        // prepare title
        if ( $day == 'monday' ){
            $day_name =  __( 'Monday', 'wbk' );
        }
        if ( $day == 'tuesday' ){
            $day_name =  __( 'Tuesday', 'wbk' );
        }       
        if ( $day == 'wednesday' ){
            $day_name =  __( 'Wednesday', 'wbk' );
        }
        if ( $day == 'thursday' ){
            $day_name =  __( 'Thursday', 'wbk' );
        }
        if ( $day == 'friday' ){
            $day_name =  __( 'Friday', 'wbk' );
        }
        if ( $day == 'saturday' ){
            $day_name =  __( 'Saturday', 'wbk' );
        }
        if ( $day == 'sunday' ){
            $day_name =  __( 'Sunday', 'wbk' );
        }
        $html = '<b>' . $day_name . '</b>';
 
        $interval_count = $business_hours->getIntervalCount( $day );
        $time_format = WBK_Date_Time_Utils::getTimeFormat();
        if ( !$business_hours->isWorkday( $day )  == true ) {
           return;
        }              
        $interval = $business_hours->getInterval( $day, 1 );        
        if ( isset ( $interval ) && count ( $interval ) == 2 ){
    
            $start_time = $interval[0];
            $end_time   = $interval[1]; 
        }  else {
            return;
        }
        $html .= ' ('.  date_i18n( $time_format, $start_time ) . ' - ' . date_i18n( $time_format, $end_time );
        
 
        if ( $interval_count == 2 ) {
            $interval = $business_hours->getInterval( $day, 2 );
            
            if ( isset ( $interval ) && count ( $interval ) == 2 ){
        
                $start_time = $interval[0];
                $end_time   = $interval[1]; 
            }  else {
                return;
            }
            $html .= ', '.  date_i18n( $time_format, $start_time ) . ' - ' . date_i18n( $time_format, $end_time );
        }
         
        $html .= ') ';
        date_default_timezone_set( get_option( 'wbk_timezone' ,'UTC' ) );                
        return $html;
    }    
    // render service disabilities 
    public static function renderBHDisabilities(){
        $arrIds = WBK_Db_Utils::getServices();
        $html  = '<script type=\'text/javascript\'>';
        $html .= 'var wbk_disabled_days = {';
        foreach ( $arrIds as $id ) {
            $service = new WBK_Service();
            if ( !$service->setId( $id ) ) {  
                    continue;
            }
            if ( !$service->load() ) {  
                continue;
            }
            $arr_bh = explode( ';',  $service->getBusinessHours() );
            $business_hours = new WBK_Business_Hours();
            if ( !$business_hours->setFromArray( $arr_bh ) ) {
                continue;
            }
                 

            $arr_disabled = array();
            if (  !$business_hours->isWorkday( 'monday' ) ){
                if ( WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){
                    array_push($arr_disabled, 1 );  
                } else {
                    array_push($arr_disabled, 2 );
                }             
            }
            if (  !$business_hours->isWorkday( 'tuesday' ) ){
                if ( WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){
                    array_push($arr_disabled, 2 );
                } else {
                    array_push($arr_disabled, 3 );
                }
            }
            if (  !$business_hours->isWorkday( 'wednesday' ) ){
               if ( WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){
                    array_push($arr_disabled, 3 );
                } else {
                    array_push($arr_disabled, 4 );
                }
            }
            if (  !$business_hours->isWorkday( 'thursday' ) ){
               if ( WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){
                    array_push($arr_disabled, 4 );
                } else {
                    array_push($arr_disabled, 5 );
                }
            }
            if (  !$business_hours->isWorkday( 'friday' ) ){
               if ( WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){
                    array_push($arr_disabled, 5 );
                } else {
                    array_push($arr_disabled, 6 );
                }
            }
            if (  !$business_hours->isWorkday( 'saturday' ) ){
                if ( WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){
                    array_push($arr_disabled, 6 );
                } else {
                    array_push($arr_disabled, 7 );
                }
            }
            if (  !$business_hours->isWorkday( 'sunday' ) ){
                if ( WBK_Date_Time_Utils::getStartOfWeek() == 'monday' ){
                    array_push($arr_disabled, 7 );
                } else {
                    array_push($arr_disabled, 1 );
                }
            }
            
                $html .=  '"'. $id .'":"'. implode(',', $arr_disabled ).'",';

                 
       }

       $html .=  '"blank":"blank"';      
       $html .= '};</script>';
       return $html;
    }
    // render service abilities 
    public static function renderBHAbilities(){
        $arrIds = WBK_Db_Utils::getServices();        
        $date_format = self::getDateFormat();
        $html  = '<script type=\'text/javascript\'>';
        $html .= 'var wbk_available_days = {';
        foreach ( $arrIds as $id ) {
            $service = new WBK_Service();
            if ( !$service->setId( $id ) ) {  
                    continue;
            }
            if ( !$service->load() ) {  
                continue;
            }
            // init service schedulle
            $service_schedule = new WBK_Service_Schedule();
            $service_schedule->setServiceId( $id );
            $service_schedule->load();
            $prepare_time = $service->getPrepareTime();                            
            $limited = false;
            if( $service->getDateRange() == '' ){
                $day_to_render = strtotime( 'today midnight' );

            } else{
                $day_to_render = $service->getDateRangeStart();
                $limited = true;
            }                             
            $endofrange = false;        
            $i = 1;
            $i_prepare = 1;
            $i_count_of_dates = get_option( 'wbk_date_input_dropdown_count', '30' );
            if( !is_numeric( $i_count_of_dates ) ){
                $i_count_of_dates = 30;
            } else {
                if( $i_count_of_dates < 2 || $i_count_of_dates > 360 ){
                    $i_count_of_dates = 30;
                }
            }
            $arr_days = array();
            while ( !$endofrange ){
                if( !$limited ){
                    if( $i_prepare < $prepare_time ){
                        $day_to_render = strtotime( 'tomorrow', $day_to_render );
                        $i_prepare++;
                        continue;
                    }
                }
                if( $service_schedule->getDayStatus( $day_to_render ) == 0 ){
                    $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                    continue;
                }
                if( get_option( 'wbk_disable_day_on_all_booked', 'disabled' ) == 'enabled' ){
                    $service_schedule->buildSchedule( $day_to_render, false, true );
                    if( $service_schedule->hasFreeTimeSlots() === false ){
                        $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                        continue;
                    }
                }               
                $arr_days[] = $day_to_render . '-HM-' . date_i18n ( $date_format, $day_to_render );
                $i++;
                $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                if( $limited ){
                    if( $day_to_render >= $service->getDateRangeEnd() ){
                        $endofrange = true;
                    }
                } else {
                    if( $i > $i_count_of_dates  ){
                        $endofrange = true;
                    }
                }
            }
            $day_to_render = strtotime( 'tomorrow', $day_to_render  );
            $html .=  '"'. $id .'":"'. implode(';', $arr_days ).'",';                
       }
       $html .=  '"blank":"blank"';      
       $html .= '};</script>';
       return $html;
    }
    // get  service abilities 
    public static function getBHAbilities( $service_id ){              
        $date_format = self::getDateFormat();        
        $id = $service_id;
        $result = '';
        $service = new WBK_Service();
        if ( !$service->setId( $id ) ) {  
            return '';
        }
        if ( !$service->load() ) {  
            return '';
        }
        // init service schedulle
        $service_schedule = new WBK_Service_Schedule();
        $service_schedule->setServiceId( $id );
        $service_schedule->load();
        $prepare_time = $service->getPrepareTime();                            
        $limited = false;
        if( $service->getDateRange() == '' ){
            $day_to_render = strtotime( 'today midnight' );

        } else{
            $day_to_render = $service->getDateRangeStart();
            $limited = true;
        }                             
        $endofrange = false;        
        $i = 1;
        $i_prepare = 1;
        $i_count_of_dates = get_option( 'wbk_date_input_dropdown_count', '30' );
        if( !is_numeric( $i_count_of_dates ) ){
            $i_count_of_dates = 30;
        } else {
            if( $i_count_of_dates < 2 || $i_count_of_dates > 360 ){
                $i_count_of_dates = 30;
            }
        }
        $arr_days = array();
        while ( !$endofrange ){
            if( !$limited ){
                if( $i_prepare < $prepare_time ){
                    $day_to_render = strtotime( 'tomorrow', $day_to_render );
                    $i_prepare++;
                    continue;
                }
            }
            if( $day_to_render < strtotime( 'today midnight') ){
                $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                continue;
            }
            if( $service_schedule->getDayStatus( $day_to_render ) == 0 ){
                $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                continue;
            }
            if( get_option( 'wbk_disable_day_on_all_booked', 'disabled' ) == 'enabled' ){
                $service_schedule->buildSchedule( $day_to_render, false, true );
                if( $service_schedule->hasFreeTimeSlots() === false ){
                    $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                    continue;
                }
            }               
            $arr_days[] = $day_to_render . '-HM-' . date_i18n ( $date_format, $day_to_render );
            $i++;
            $day_to_render = strtotime( 'tomorrow', $day_to_render  );
            if( $limited ){
                if( $day_to_render >= $service->getDateRangeEnd() ){
                    $endofrange = true;
                }
            } else {
                if( $i > $i_count_of_dates  ){
                    $endofrange = true;
                }
            }
        }
        $day_to_render = strtotime( 'tomorrow', $day_to_render  );
        $result .=  implode(';', $arr_days );
        return $result;
    }
    // render service disabilities 
    public static function renderBHDisabilitiesFull(){
        $arrIds = WBK_Db_Utils::getServices();
        $html  = '<script type=\'text/javascript\'>';
        $html .= 'var wbk_disabled_days = {';
        foreach ( $arrIds as $id ) {
            $service = new WBK_Service();
            if ( !$service->setId( $id ) ) {  
                    continue;
            }
            if ( !$service->load() ) {  
                continue;
            }
            // init service schedulle
            $service_schedule = new WBK_Service_Schedule();
            $service_schedule->setServiceId( $id );
            $service_schedule->load();
            
            $prepare_time = $service->getPrepareTime();                 

            $arr_disabled = array();
            
            $day_to_render = strtotime('today midnight');
            for ( $i = 1;  $i <= 360 ;  $i++ ){
                if( $i <=  $prepare_time ){
                    array_push($arr_disabled, date('Y', $day_to_render).','. intval( date('n', $day_to_render) - 1 )  . ','.date('j', $day_to_render) ) ;
                    $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                    continue;
                }
                if( $service_schedule->getDayStatus( $day_to_render ) == 0 ){
                    array_push($arr_disabled, date('Y', $day_to_render).','. intval( date('n', $day_to_render) - 1 )  . ','.date('j', $day_to_render) ) ;
                } else {
                    if( get_option( 'wbk_disable_day_on_all_booked', 'disabled' ) == 'enabled' ){
                        $service_schedule->buildSchedule( $day_to_render, false, true );
                        if( $service_schedule->hasFreeTimeSlots() === false  ){
                            array_push($arr_disabled, date('Y', $day_to_render).','. intval( date('n', $day_to_render) - 1 )  . ','.date('j', $day_to_render) ) ;
                        }
                    }
                }
                $day_to_render = strtotime( 'tomorrow', $day_to_render  );
            }
            $html .=  '"'. $id .'":"'. implode(';', $arr_disabled ).'",';                
       }
       $html .=  '"blank":"blank"';      
       $html .= '};</script>';
       return $html;
    }
    // get single service disabilities 
    public static function getServiceDisabiliy( $service_id ){
        $id = $service_id;
        $service = new WBK_Service();
        if ( !$service->setId( $id ) ) {  
            return;
        }
        if ( !$service->load() ) {  
            return;
        }
        // init service schedulle
        $service_schedule = new WBK_Service_Schedule();
        $service_schedule->setServiceId( $id );
        $service_schedule->load();        
        $prepare_time = $service->getPrepareTime();                 
        $arr_disabled = array();        
        $day_to_render = strtotime('today midnight');
        $result = '';
        for ( $i = 1;  $i <= 360 ;  $i++ ){
            if( $i <=  $prepare_time ){
                array_push($arr_disabled, date('Y', $day_to_render).','. intval( date('n', $day_to_render) - 1 )  . ','.date('j', $day_to_render) ) ;
                $day_to_render = strtotime( 'tomorrow', $day_to_render  );
                continue;
            }
            if( $service_schedule->getDayStatus( $day_to_render ) == 0 ){
                array_push($arr_disabled, date('Y', $day_to_render).','. intval( date('n', $day_to_render) - 1 )  . ','.date('j', $day_to_render) ) ;
            } else {
                if( get_option( 'wbk_disable_day_on_all_booked', 'disabled' ) == 'enabled' ){

                    $service_schedule->buildSchedule( $day_to_render, false, true );
                    if( $service_schedule->hasFreeTimeSlots() === false  ){
                         
                        array_push($arr_disabled, date('Y', $day_to_render).','. intval( date('n', $day_to_render) - 1 )  . ','.date('j', $day_to_render) ) ;
                    }
                }
            }
            $day_to_render = strtotime( 'tomorrow', $day_to_render  );
        }

        $result .= implode(';', $arr_disabled );                 
        return $result;
    }
    public static function getServicWeekDisabiliy( $service_id ){
        $service_schedule = new WBK_Service_Schedule();
        $service_schedule->setServiceId( $service_id );
        $service_schedule->load();
        $disabilities = $service_schedule->getWeekDisabilities();
        return $disabilities;

    }   
    // render service limits 
    public static function getServiceLimits( $service_id ){
        $id = $service_id;
        $service = new WBK_Service();
        if ( !$service->setId( $id ) ) {  
            return '';
        }
        if ( !$service->load() ) {  
            return '';
        }
        $result = '';
        // init service schedulle
        if( $service->getDateRange() == '' ){
            $limit_value = '';
        } else{
            if(  $service->getDateRangeStart() ==  $service->getDateRangeEnd() ){
                $limit_value = $service->getDateRangeStart();
            } else {
                $limit_value = date( 'Y,n,j', $service->getDateRangeStart() ) . '-' . date( 'Y,n,j', $service->getDateRangeEnd() );
            }
        }                        
        $result .=  $limit_value;                
        return $result;
     }
    // render service limits 
    public static function renderServiceLimits(){
        $arrIds = WBK_Db_Utils::getServices();
        $html  = '<script type=\'text/javascript\'>';
        $html .= 'var wbk_service_limits = {';
        foreach ( $arrIds as $id ) {
            $service = new WBK_Service();
            if ( !$service->setId( $id ) ) {  
                    continue;
            }
            if ( !$service->load() ) {  
                continue;
            }
            // init service schedulle
            if( $service->getDateRange() == '' ){
                $limit_value = '';
            } else{
                if(  $service->getDateRangeStart() ==  $service->getDateRangeEnd() ){
                    $limit_value = $service->getDateRangeStart();
                } else {
                    $limit_value = date( 'Y,n,j', $service->getDateRangeStart() ) . '-' . date( 'Y,n,j', $service->getDateRangeEnd() );
                }
            }                        
            $html .=  '"'. $id .'":"'. $limit_value .'",';                
       }
       $html .=  '"blank":"blank"';      
       $html .= '};</script>';
       return $html;
    }

    public static function chekRangeIntersect( $start, $end, $start_compare, $end_compare ){
        $intersect = FALSE;         
        if ( $start_compare == $start ){
            $intersect = TRUE;                  
        }
        if ( $start_compare > $start && $start_compare < $end ){
            $intersect = TRUE;                  
        }
        if ( $end_compare > $start && $end_compare <= $end  ){
            $intersect = TRUE;                  
        }
        if ( $start >= $start_compare && $end <= $end_compare  ){
            $intersect = TRUE;                  
        }
        return $intersect;
    }
           
}
?>