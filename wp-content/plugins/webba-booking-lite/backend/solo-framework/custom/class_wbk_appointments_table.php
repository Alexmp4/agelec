<?php
//WBK appointment table class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'wp_ajax_wbk_get_free_time_for_day',  'wbkGetFreeTimeForDay' ); 
function wbkGetFreeTimeForDay(){
    date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
    if( isset( $_POST['date'] ) && isset( $_POST['appointment_id'] ) ){
        $date = sanitize_text_field( $_POST['date'] );
        $appointment_id = sanitize_text_field( $_POST['appointment_id'] );
        $service_id = sanitize_text_field( $_POST['service_id'] );
    } else {
        echo '-1';
        date_default_timezone_set( 'UTC' );
        wp_die();
        return;
    }
   
    if( !is_numeric( $appointment_id ) || !is_numeric( $service_id ) ){
        echo  $appointment_id.'-'. $service_id;
        date_default_timezone_set( 'UTC' );
        wp_die();
        return;
    }
    $date = strtotime( $date );
    if( $date == FALSE  ){
        echo '-1';
        date_default_timezone_set( 'UTC' );
        wp_die();
        return;
    }
    $service_schedule = new WBK_Service_Schedule();
    if ( !$service_schedule->setServiceId( $service_id ) ){
        echo '-2';
        date_default_timezone_set( 'UTC' );
        wp_die();
        return;
    }
    if ( !$service_schedule->load() ){
        echo '-3';
        date_default_timezone_set( 'UTC' );
        wp_die();
        return;    
    }    
    if( $service_schedule->getDayStatus( $date ) == 0 ){
        $html = '<option data-ext="0"   value="0" >' . __( 'Free time slots not found', 'wbk' ) . '</option>';
        echo $html;
        date_default_timezone_set( 'UTC' );
        wp_die();
        return;
    }
    $service_schedule->buildSchedule( $date );
    $options = $service_schedule->getFreeTimeslotsPlusGivenAppointment( $appointment_id );
    $html = '';
    foreach( $options as $key => $value ){
        $html .= '<option data-ext="' . $value[1] . '"  value="' . $key . '" >' . $value[0] . '</option>';
    }
    echo $html;
    date_default_timezone_set( 'UTC' );
    wp_die();
    return;
}

class WBK_Appointments_Table extends SLFTable {
	public function __construct() {
			$this->field_set = new SLFFieldSet( true, true );
            $field = new SLFField( array( 'title' => __( 'Service','wbk' ),     
                                         'name' => 'service_id',
                                         'format' => '%d',
                                         'component' => 'SLFTableWbkService',
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkInteger' ), array( 1, 10000 ) )

                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array( 'title' => __( 'Date','wbk' ),     
                                         'name' => 'day',
                                         'format' => '%d',
                                         'component' => 'SLFTableDate',
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkDate' ), array( 0, 0 ) )
                                        )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array( 'title' => __( 'Time','wbk' ),     
                                         'name' => 'time',
                                         'format' => '%d',
                                         'component' => 'SLFTableSelect',
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'data_source' => array( 'WBK_Db_Utils', 'getFreeTimeslotsArray' ),
                                         'validation' => array( array( 'SLFValidator', 'checkInteger' ), array( 1481270915, 4132022915 ) )
                                          )
                                 );

            $this->field_set->append( $field );
            $field = new SLFField( array('title' => __( 'Places booked', 'wbk' ),     
                                         'name' => 'quantity',
                                         'format' => '%d',
                                         'component' => 'SLFTableSelect',
                                         'assoc' => 'time',
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'data_source' => array( 'WBK_Db_Utils', 'blankArray' ),
                                         'validation' => array( array( 'SLFValidator', 'checkInteger' ), array( 1, 100000 ) )
                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array( 'title' => __( 'Customer name','wbk' ),     
                                         'name' => 'name',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkText' ), array( 3, 128 ) )
                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array('title' => __( 'Customer email', 'wbk' ),     
                                         'name' => 'email',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkEmail' ), array( 0, 0 ) )
                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array('title' => __( 'Customer phone', 'wbk' ),     
                                         'name' => 'phone',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkText' ), array( 3, 30 ) )
                                          )
                                 );
            $this->field_set->append( $field );            
            $field = new SLFField( array('title' => __( 'Customer comment', 'wbk' ),     
                                         'name' => 'description',
                                         'format' => '%s',
                                         'component' => 'SLFTableTextarea',
                                          'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkText' ), array( 0, 1023 ) )
                                          )
                                 );
            $this->field_set->append( $field );            
            $field = new SLFField( array('title' => __( 'Custom fields', 'wbk' ),     
                                         'name' => 'extra',
                                         'format' => '%s',
                                         'component' => 'SLFTableWbkCustomField',      
                                         'render_cell' =>  true,
                                         'render_control' => true,
                                          )
                                 );
            $this->field_set->append( $field );           
            $field = new SLFField( array('title' => __( 'Status', 'wbk' ),     
                                         'name' => 'status',
                                         'format' => '%s',
                                         'component' => 'SLFTableSelect', 
                                          'data_source' =>  array( 'WBK_Db_Utils', 'getAppointmentStatusList' ),    
                                         'render_cell' => true,
                                         'render_control' => true,
                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array('title' => __( 'Duration', 'wbk' ),     
                                         'name' => 'duration',
                                         'format' => '%d',
                                         'component' => 'SLFTableHiddenText',      
                                         'render_cell' => false,
                                         'render_control' => true,
                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array('title' => __( 'Payment method', 'wbk' ),     
                                         'name' => 'payment_method',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',      
                                         'render_cell' => true,
                                         'render_control' => false,
                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array('title' => __( 'Price', 'wbk' ),     
                                         'name' => 'moment_price',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',      
                                         'render_cell' => true,
                                         'render_control' => false,
                                          )
                                 );
            $this->field_set->append( $field );

            

            $this->table_name = 'wbk_appointments';
            $this->filter_set = array();
            $filter = new SLFTableFilterDateRange( __( 'Select date range:', 'wbk' ), 'day' );
            $filter->setDefault();
            $this->filter_set['day'] = $filter;
            $filter = new WBKTableFilterServices( __( 'Select services:', 'wbk' ), 'service_id' );
            $filter->setDefault();
            $this->filter_set['service_id'] = $filter;
	}
    public function onAfterAdd( $id ){
        $service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $id );

        $auto_lock = get_option( 'wbk_appointments_auto_lock', 'disabled' );
        if ( $auto_lock == 'enabled' ){           
            WBK_Db_Utils::lockTimeSlotsOfOthersServices( $service_id, $id );
        }
        WBK_Db_Utils::setAmountForApppointment( $id );
        // *** GG ADD
        
    }
    protected function getOrder(){
        return ' order by time ';
    } 
    public function checkAccess(){  
        global $current_user;
        if ( in_array( 'administrator', $current_user->roles ) ) {
            return TRUE;
        }
        $services = WBK_Db_Utils::getServices();
        foreach ($services as $service_id) {
           if( WBK_Validator::checkAccessToService( $service_id ) ){
                return TRUE;
           }
        }
        return FALSE;
    }
    public function footerContent(){
        $html = '<div class="slf_col_12_12_12 slf_pd10">';        
            $html .= '<input class="button-primary slf_table_csv_export" value="' . __( 'CSV Export', 'wbk' ) . '" type="button">';
            $html .= '<div id="slf_table_csv_export_container"></div>';
        $html .= '</div>';
        $html .= '<div style="clear:both"></div>';
        return $html;
    }   
    public function onBeforeUpdate( $row_id ){
       global $wpdb;
       $sql =  $wpdb->prepare( "SELECT status FROM wbk_appointments WHERE id = %d", $row_id);
       $value = $wpdb->get_var( $sql );
       return $value;
    }
    public function onAfterUpdate( $data, $row_id ){
       global $wpdb;
       $sql =  $wpdb->prepare( "SELECT status FROM wbk_appointments WHERE id = %d", $row_id);
       $current_status = $wpdb->get_var( $sql );
       if( $data == 'pending' || $data == 'paid' ){
            if( $current_status == 'approved' || $current_status == 'paid_approved' ){
                $service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $row_id );
                $noifications = new WBK_Email_Notifications( $service_id, $row_id );
                $noifications->sendOnApprove();
                $expiration_mode = get_option( 'wbk_appointments_delete_not_paid_mode', 'disabled' );
                if( $expiration_mode == 'on_approve' ){
                    WBK_Db_Utils::setAppointmentsExpiration( $row_id );
                }

            }
       }
       return;      
    }
    public function onBeforeDelete( $row_id ){
        
    }
    public function onAfterDelete( $data, $row_id ){
        WBK_Db_Utils::freeLockedTimeSlot( $row_id ); 
      
    }   

}