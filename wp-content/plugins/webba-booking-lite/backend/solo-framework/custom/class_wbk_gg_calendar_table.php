<?php
//WBK email templates table class

// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_GG_Calendar_Table extends SLFTable {
	public function __construct() {			           
			$this->field_set = new SLFFieldSet( true, true );
            $field = new SLFField( array( 'title' => __( 'Name','wbk' ),     
                                         'name' => 'name',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',                                     
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkNameField' ), array(1,128)  )
                                          )

                                 );
            $this->field_set->append( $field );

          
             $field = new SLFField( array( 'title' => __( 'WordPress user','wbk' ),     
                                         'name' => 'user_id',
                                         'format' => '%d',
                                         'component' => 'SLFTableWpUser',                                         
                                         'render_cell' => true,
                                         'render_control' => true
                                           
                                          )
                                 );
             $this->field_set->append( $field );
 
             $field = new SLFField( array( 'title' => __( 'Calendar id','wbk' ),     
                                         'name' => 'calendar_id',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',                                         
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkText' ), array( 3, 512 ) )
                                        )
                                 );
  
 
            $this->field_set->append( $field );
            $field = new SLFField( array('title' => __( 'Mode', 'wbk' ),     
                                         'name' => 'mode',
                                         'format' => '%s',
                                         'component' => 'SLFTableSelect', 
                                          'data_source' =>  array( 'WBK_Db_Utils', 'getGGCalendarModeList' ),    
                                         'render_cell' => true,
                                         'render_control' => true,
                                          )
                                 );
            $this->field_set->append( $field );
            $field = new SLFField( array( 'title' => __( 'Authorization','wbk' ),     
                                         'name' => 'access_token',
                                         'format' => '%s',
                                         'component' => 'SLFTableGGAuth',                                         
                                         'render_cell' => true,
                                         'render_control' => false
                                           
                                          )
                                 );
             $this->field_set->append( $field );

             
            $this->table_name = 'wbk_gg_calendars';
 
            $filter = new SLFTableFilterAccess( '' , 'id' );
            $filter->setDefault();
            $this->filter_set['id'] = $filter;            
	}
    public function checkAccess(){  
        global $current_user;
        if ( in_array( 'administrator', $current_user->roles ) ) {
            return TRUE;
        }
        return FALSE;
    }

}