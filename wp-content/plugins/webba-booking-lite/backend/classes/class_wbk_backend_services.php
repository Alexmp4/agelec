<?php
// Webba Booking backend service class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Backend_Services extends WBK_Backend_Component   {
	
	public function __construct() {
		//set component-specific properties
		$this->name          = 'wbk-services';
		$this->title         = 'Services';
		$this->main_template = 'tpl_wbk_backend_services.php';
        $this->capability    = 'manage_options';
		// init scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts') );
 
		// add ajax action
		add_action( 'wp_ajax_wbk_service_delete', array( $this, 'ajaxServiceDelete' ) ); 
        add_action( 'wp_ajax_wbk_service_add', array( $this, 'ajaxServiceAdd' ) );
        add_action( 'wp_ajax_wbk_service_load', array( $this, 'ajaxServiceLoad' ) );
        add_action( 'wp_ajax_wbk_service_edit', array( $this, 'ajaxServiceEdit' ) );
        
	}
	// init styles and scripts
	public function enqueueScripts() {
                     
 		if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wbk-services' ) { 
 
            wp_enqueue_script( 'wbk-services', plugins_url( 'js/wbk-services.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog' ), '3.3.38' );            	         
            wp_enqueue_script( 'wbk-validator', plugins_url( 'common/wbk-validator.js', dirname( dirname( __FILE__ ) ) ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog' ) );   
            wp_enqueue_script( 'slf-chosen', plugins_url( 'js/chosen.jquery.min.js', dirname( __FILE__ )  ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ) );
            wp_enqueue_style( 'slf-chosen-css', plugins_url( 'css/chosen.min.css', dirname( __FILE__ ) ) );

            wp_enqueue_script( 'jquery-plugin', plugins_url( 'js/jquery.plugin.js', dirname( __FILE__ ) ), array( 'jquery' ) );
            wp_enqueue_script( 'multidate-picker', plugins_url( 'js/jquery.datepick.min.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ) );
            wp_enqueue_style( 'wbk-datepicker-css', plugins_url( 'css/jquery.datepick.css', dirname( __FILE__ ) )  );


            $translation_array = array( 
                'cancel' => __( 'Cancel', 'wbk' ),         
                'delete' => __( 'Delete', 'wbk' ), 
                'addservice' => __( 'Add service', 'wbk' ),    
                'editservice' => __( 'Edit service', 'wbk' ), 
                'save' => __( 'Save', 'wbk' ),
                'removegap' => __( 'Remove the second gap', 'wbk' ),
                'addgap' => __( 'Add the second gap', 'wbk' )
            ); 
            wp_localize_script( 'wbk-services', 'wbkl10n', $translation_array );

 		}
 	}
 	// delete service
 	public function ajaxServiceDelete(){
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
        global $current_user;
        // check access
        if ( !in_array( 'administrator', $current_user->roles ) ) {
            echo '-1';
            date_default_timezone_set('UTC');
            die();
            return; 
        }
 		$ids = $_POST['ids'];
 		foreach ( $ids as $id ) {
 			
	 		$arrId = explode( '_', $id );
	 		if ( count ( $arrId ) <> 3 ) {
	 			echo '-1';
                date_default_timezone_set('UTC');
	 			die();
	 			return;
	 		}
	 		if ( !is_numeric( $arrId[2] ) ) {
	 			echo '-2';
                date_default_timezone_set('UTC');
	 			die();
	 			return;
	 		}      
	 		$service = new WBK_Service();
 			if ( !$service->setId( $arrId[2] ) ) {
	 			echo '-3';
                date_default_timezone_set('UTC');
	 			die();
	 			return;
	 		}
	 		if ( $service->delete() === false ) {
	 			echo '-4';
                date_default_timezone_set('UTC');
	 			die();
	 			return;
	 		}
 		}
 		echo '1';	
        date_default_timezone_set('UTC');
 		die();
        return;
 	}
    // render business hours (string)
    // time zone correction removed
    public function renderBusinesHoursString( $value ) {
        date_default_timezone_set( 'UTC' );
        $business_hours = new WBK_Business_Hours();
        $arr_bh = explode( ';', $value );
        if ( !$business_hours->setFromArray( $arr_bh ) ) {
           return false;
        }
        $html =  $this->render_business_hours_at_day( $business_hours, 'monday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'tuesday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'wednesday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'thursday' );        
        $html .= $this->render_business_hours_at_day( $business_hours, 'friday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'saturday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'sunday' );
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
        return $html;
    }
    // render business hours for cell (string)
    // time zone correction removed
    public function renderBusinesHoursStringCell( $value ) {
        date_default_timezone_set( 'UTC' );      
        $business_hours = new WBK_Business_Hours();
        $arr_bh = explode( ';', $value );
        $business_hours->setFromArray( $arr_bh );
        $html =  $this->render_business_hours_cell_at_day( $business_hours, 'monday' );
        $html .= $this->render_business_hours_cell_at_day( $business_hours, 'tuesday' );
        $html .= $this->render_business_hours_cell_at_day( $business_hours, 'wednesday' );
        $html .= $this->render_business_hours_cell_at_day( $business_hours, 'thursday' );
        
        $html .= $this->render_business_hours_cell_at_day( $business_hours, 'friday' );
        $html .= $this->render_business_hours_cell_at_day( $business_hours, 'saturday' );
        $html .= $this->render_business_hours_cell_at_day( $business_hours, 'sunday' );
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
        return $html;
    }
    // render business hours (default)
    // time zone correction removed
    public function renderBusinessHours() {
        date_default_timezone_set( 'UTC' ); 
        $business_hours = new WBK_Business_Hours();
        $business_hours->setDefault();
        $html =  $this->render_business_hours_at_day( $business_hours, 'monday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'tuesday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'wednesday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'thursday' );
        
        $html .= $this->render_business_hours_at_day( $business_hours, 'friday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'saturday' );
        $html .= $this->render_business_hours_at_day( $business_hours, 'sunday' );
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );       
        return $html;
    }
    // render hours for day
    public function render_business_hours_at_day( $business_hours, $day ) {
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
    public function render_business_hours_cell_at_day( $business_hours, $day ) {        
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
            date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );  
            return;
        }  
        
        
        $interval = $business_hours->getInterval( $day, 1 );
        
        if ( isset ( $interval ) && count ( $interval ) == 2 ){
    
            $start_time = $interval[0];
            $end_time   = $interval[1]; 
        }  else {
            date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );  
            return;
        }
        $html .= ' ('.  date_i18n( $time_format, $start_time ) . ' - ' . date_i18n( $time_format, $end_time );
        
 
        if ( $interval_count == 2 ) {
            $interval = $business_hours->getInterval( $day, 2 );
            
            if ( isset ( $interval ) && count ( $interval ) == 2 ){
        
                $start_time = $interval[0];
                $end_time   = $interval[1]; 
            }  else {
                date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );  
                return;
            }
            $html .= ', '.  date_i18n( $time_format, $start_time ) . ' - ' . date_i18n( $time_format, $end_time );
        }
         
        $html .= ') ';    
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );  
        return $html;
    }
    // add service
    public function ajaxServiceAdd() {
        global $current_user;
        // check access
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
        if ( !in_array( 'administrator', $current_user->roles ) ) {
            echo '-1';
            date_default_timezone_set('UTC');
            die();
            return; 
        }
        $name = trim( $_POST['name'] );
        $desc = trim( $_POST['desc'] );
        $email = strtolower( trim ( $_POST['email'] ) );
        $duration = trim( $_POST['duration'] );
        $interval = trim( $_POST['interval'] );
        $step = trim( $_POST['step'] );
        $form = trim( $_POST['form'] );
        $quantity = trim( $_POST['quantity'] );
        $priority = trim( $_POST['priority'] );

        $price = trim( $_POST['price'] );
        $notification_template = trim( $_POST['notification_template']);
        $reminder_template = trim( $_POST['reminder_template']);
        $invoice_template = trim( $_POST['invoice_template']);
        $prepare_time = trim( $_POST['prepare_time']);
        $date_range = trim( $_POST['date_range'] );       
        $multi_limit = trim( $_POST['multi_limit'] );       
        $multi_low_limit = trim( $_POST['multi_low_limit'] );       

        if ( isset( $_POST['payment_methods'] ) && $_POST['payment_methods'] != 'null' && $_POST['payment_methods'] != '' ) {
            $payment_methods = implode( ';', $_POST['payment_methods'] );
        } else {
            $payment_methods = '';
        }

        if ( isset( $_POST['users'] ) && $_POST['users'] != 'null' && $_POST['users'] != '' ) {
            $users = implode( ';', $_POST['users'] );
        } else {
            $users = '';
        }
        

        if ( isset( $_POST['gg_calendars'] ) && $_POST['gg_calendars'] != 'null' && $_POST['gg_calendars'] != '' ) {
            $gg_calendars = implode( ';', $_POST['gg_calendars'] );
        } else {
            $gg_calendars = '';
        }
        



        if ( isset( $_POST['business_hours'] ) && $_POST['business_hours'] != '' ) {
            $business_hours = implode( ';' , $_POST['business_hours'] );
        } else {
            $business_hours = '';
        }
        $service = new WBK_Service();
        if ( !$service->setName( $name ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setDescription( $desc ) ){
            echo -3;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setEmail( $email ) ){
            echo -4;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setDuration( $duration ) ){
            echo -5;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setInterval( $interval ) ){
            echo -6;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setStep( $step ) ){
            echo -6;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( $business_hours != '' ) {
            if ( !$service->setBusinessHours( $business_hours ) ){
                echo -7;
                date_default_timezone_set('UTC');
                die();
                return;
            }
        }
        if ( !$service->setUsers( $users ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setForm( $form ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setQuantity( $quantity ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setPriority( $priority ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setPrice( $price ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setPayementMethods( $payment_methods ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setNotificationTemplate( $notification_template ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setReminderTemplate( $reminder_template ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setInvoiceTemplate( $invoice_template ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setPrepareTime( $prepare_time ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setDateRange( $date_range ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setGgCalendars( $gg_calendars ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setMultipleLimit( $multi_limit ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setMultipleLowLimit( $multi_low_limit ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        $new_id = $service->add();
        if ( $new_id === false ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        $arr_users = explode( ';', $service->getUsers() );
        $usernames = '';
        foreach ( $arr_users as $user ) {
            if ( $user == '' ) {
                continue;
            }
                                     
            $user_info = get_userdata( $user[0] );
                                
            $usernames .=  $user_info->user_login.', ';
            
        }
        $usernames = rtrim( $usernames, ', ' );
                            
        echo '<tr id="row_' . $new_id . '">              
                <td>
                   <input type="checkbox" class="chk_row" id="chk_row_' . $new_id . '">
                </td>       
                <td>
                    <div id="value_name_' . $new_id . '" class="value_container">' . $service->getName() . '</div>
                </td>       
                <td>
                    <div id="value_description_' . $new_id . '" class="value_container">' . $service->getDescription() . '</div>                                            
                </td>       
                <td>
                    <div id="value_email_' . $new_id . '" class="value_container">' . $service->getEmail() . '</div>                            
                </td>       
                <td>                
                    <div id="value_duration_' . $new_id . '" class="value_container">' . $service->getDuration() . ' ' . __( 'minutes', 'wbk' ) . '</div>
                </td>
                <td>                
                    <div id="value_interval_' . $new_id . '" class="value_container">' . $service->getInterval() . ' ' . __( 'minutes', 'wbk' ) . '</div>
                </td>
                <td>                
                    <div id="value_step_' . $new_id . '" class="value_container">' . $service->getStep() . ' ' . __( 'minutes', 'wbk' ) . '</div>
                </td>
                <td>                
                    <div id="value_quantity_' . $new_id . '" class="value_container">' . $service->getQuantity() . '</div>
                </td>
                <td>
                    <div id="value_business_hours_' . $service->getId() . '" class="wbk-font-10">' .                       
                        $this->renderBusinesHoursStringCell( $service->getBusinessHours() ) .'                           
                    </div>
                </td>
                <td>                
                    <div id="value_users_' . $service->getId() . '" class="value_container">' . $usernames . '</div>
                </td>
                <td>                
                   <div id="value_price_' . $service->getId() .'" class="value_container">'. number_format( $service->getPrice(),  get_option( 'wbk_price_fractional', '2' ) ) .'</div>
                </td>  
            </tr>';
        date_default_timezone_set('UTC');
        die();
        return;
 
    }
     // edit service
    public function ajaxServiceEdit() {
        global $current_user;
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
        // check access
        if ( !current_user_can('manage_options') ) {
            echo '-1';
            date_default_timezone_set('UTC');
            die();
            return; 
        }
        $name = trim( $_POST['name'] );
        $prevname = trim( $_POST['prevname'] );        
        $desc = trim( $_POST['desc'] );
        $email = strtolower( trim ( $_POST['email'] ) );
        $duration = trim( $_POST['duration'] );
        $interval = trim( $_POST['interval'] );     
        $step = trim( $_POST['step'] );
        $service_id = trim ( $_POST['id'] );
        $form = trim ( $_POST['form'] );
        $arrId = explode( '_', $service_id );
        $quantity = trim( $_POST['quantity'] );
        $priority = trim( $_POST['priority'] );
        $price = trim( $_POST['price'] );
        $notification_template = trim( $_POST['notification_template']);
        $reminder_template = trim( $_POST['reminder_template']);
        $invoice_template = trim( $_POST['invoice_template']);
        $prepare_time = trim( $_POST['prepare_time']);
        $date_range = trim( $_POST['date_range']);
        $multi_limit = trim( $_POST['multi_limit']);
        $multi_low_limit = trim( $_POST['multi_low_limit']);

        if ( count ( $arrId ) <> 3 ) {
            echo '-12';
            date_default_timezone_set('UTC');
            die();
            return;
        }
        $service_id = $arrId[2];
        if ( isset( $_POST['users'] ) && $_POST['users'] != null ) {
            $users = implode( ';', $_POST['users']) ;
        } else {
            $users = '';
        }
        if ( isset( $_POST['payment_methods'] ) && $_POST['payment_methods'] != null ) {
            $payment_methods = implode( ';', $_POST['payment_methods']) ;
        } else {
            $payment_methods = '';
        }
        if (  isset( $_POST['business_hours'] ) && $_POST['business_hours'] != '' ) {
        
            $business_hours = implode( ';' , $_POST['business_hours'] );
        
        } else {
            $business_hours = '';
        }

        if ( isset( $_POST['gg_calendars'] ) && $_POST['gg_calendars'] != 'null' && $_POST['gg_calendars'] != '' ) {
            $gg_calendars = implode( ';', $_POST['gg_calendars'] );
        } else {
            $gg_calendars = '';
        }
        

        $service = new WBK_Service();
        if ( !$service->setId( $service_id ) ){
            echo -10;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->load() ){
            echo -11;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setName( $name ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setDescription( $desc ) ){
            echo -3;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setEmail( $email ) ){
            echo -4;
            die();
            return;
        }
        if ( !$service->setDuration( $duration ) ){
            echo -5;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setInterval( $interval ) ){
            echo -6;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setStep( $step ) ){
            echo -6;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setBusinessHours( $business_hours ) ){
            echo -7;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setQuantity( $quantity ) ){
            echo -7;
            date_default_timezone_set('UTC');
            die();
            return;
        }
         if ( !$service->setPriority( $priority ) ){
            echo -7;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setUsers( $users ) ){
            echo -8;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setForm( $form ) ){
            echo -8;
            date_default_timezone_set('UTC');
            die();
            return;
        }  
        if ( !$service->setPrice( $price ) ){
            echo -7;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setPayementMethods( $payment_methods ) ){
            echo -7;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setNotificationTemplate( $notification_template ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setReminderTemplate( $reminder_template ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !$service->setInvoiceTemplate( $invoice_template ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }   
        if ( !$service->setPrepareTime( $prepare_time ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }        
        if ( !$service->setDateRange( $date_range ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }        
        if ( !$service->setGgCalendars( $gg_calendars ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }        
        if ( !$service->setMultipleLimit( $multi_limit ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }      
         if ( !$service->setMultipleLowLimit( $multi_low_limit ) ){
            echo -1;
            date_default_timezone_set('UTC');
            die();
            return;
        }           
        if ( !$service->update() ){
            echo -9;
            date_default_timezone_set('UTC');
            die();
            return;
        }     
        $arr_users = explode( ';', $service->getUsers() );
        $usernames = '';
        foreach ( $arr_users as $user ) {
            if ( $user == '' ) {
                continue;
            }
                                     
            $user_info = get_userdata( $user[0] );
                                
            $usernames .=  $user_info->user_login.', ';
            
        }
        if ( $usernames != '' ) {
            $usernames = rtrim( $usernames, ', ' );
        }    
        $name = $service->getName();
        $desc = $service->getDescription();
        $email = $service->getEmail();
        $form = $service->getForm();
        $duration = $service->getDuration() . ' ' . __( 'minutes', 'wbk' );
        $interval = $service->getInterval() . ' ' . __( 'minutes', 'wbk' );
        $step = $service->getStep(). ' ' . __( 'minutes', 'wbk' );
        $users = $usernames;
        $quantity = $service->getQuantity();
        $priority = $service->getPriority();
        $price = number_format( $service->getPrice(),  get_option( 'wbk_price_fractional', '2' ) );
        $payment_methods = $service->getPayementMethods();

        if ( $business_hours != '' ){
            $bh = $this->renderBusinesHoursStringCell( $service->getBusinessHours() );
        }
        if ( $bh === false ) {
            echo -12;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        $resarray = array( 'id' => $service_id, 'name' => $name, 'desc' =>  $desc, 'email' => $email, 'duration' => $duration,
                           'step' => $step, 'interval' => $interval, 'users' => $users, 'form' => $form , 'bh' => $bh,  
                           'quantity' => $quantity, 'price' => $price, 'priority' => $priority );
    
        echo json_encode( $resarray );
        date_default_timezone_set('UTC');
        die();
        return;
 
    }
    // add service
    public function ajaxServiceLoad() {
        global $current_user;
        date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
        // check access
        if ( !current_user_can( 'manage_options' ) ){
           echo '-1';
           date_default_timezone_set('UTC');
           die();
           return; 
        }
        $id = $_POST['id'];
            
        $arrId = explode( '_', $id );
        if ( count ( $arrId ) <> 3 ) {
            echo '-12';
            date_default_timezone_set('UTC');
            die();
            return;
        }
        if ( !is_numeric( $arrId[2] ) ) {
            echo '-2';
            date_default_timezone_set('UTC');           
            die();
            return;
        }
        $service = new WBK_Service();
        if ( !$service->setId( $arrId[2] ) ) {
            echo '-3';
            date_default_timezone_set('UTC');
            die();
            return;
        }     
        if ( !$service->load() ) {
            echo '-4';
            date_default_timezone_set('UTC');
            die();
            return;
        }
        $name = $service->getName();
        $desc = $service->getDescription();
        $email = $service->getEmail();
        $duration = $service->getDuration();
        $interval = $service->getInterval();
        $users = $service->getUsers();
        $step = $service->getStep();
        $form = $service->getForm();
        $quantity = $service->getQuantity();
        $priority = $service->getPriority();
        $price =  number_format( $service->getPrice(),  get_option( 'wbk_price_fractional', '2' ));
        $payment_methods = $service->getPayementMethods();
        $notification_template = $service->getNotificationTemplate();
        $reminder_template = $service->getReminderTemplate();
        $invoice_template = $service->getInvoiceTemplate();    
        $prepare_time = $service->getPrepareTime();
        $date_range = $service->getDateRange();
        $gg_calendars = $service->getGgCalendars();
        $multi_limit = $service->getMultipleLimit();
        $multi_low_limit = $service->getMultipleLowLimit();

        $bh = $this->renderBusinesHoursString( $service->getBusinessHours() );
        if ( $bh === false ) {
            echo -5;
            date_default_timezone_set('UTC');
            die();
            return;
        }
        $resarray = array( 'name' => $name, 'desc' =>  $desc, 'email' => $email, 'duration' => $duration, 'step' => $step,
                           'interval' => $interval, 'users' => $users, 'form' => $form,'bh' => $bh, 'quantity' => $quantity, 'priority' => $priority,
                           'price' => $price, 'payment_methods'  => $payment_methods, 'notification_template' => $notification_template, 'reminder_template' => $reminder_template, 'invoice_template' => $invoice_template,
                            'prepare_time' => $prepare_time, 'date_range' => $date_range, 'gg_calendars' => $gg_calendars, 'multi_limit' => $multi_limit, 'multi_low_limit' => $multi_low_limit   ); 
        echo json_encode($resarray);
        date_default_timezone_set('UTC');
        die();
        return;
    }
}
?>
