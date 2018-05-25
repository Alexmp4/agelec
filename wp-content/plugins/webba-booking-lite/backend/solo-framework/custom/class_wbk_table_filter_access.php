<?php
// Solo Framework table filter class
if ( ! defined( 'ABSPATH' ) ) exit;
class SLFTableFilterAccess extends SLFTableFilter {
	public function __construct( $title, $field ) {
  		parent::__construct( $title, $field );
   	}
    public function valid(){
        return TRUE;
    }
    public function  set( $value ){
        return TRUE;
         
    }
    public function setDefault(){
    	 $this->id = 0;
    }
    public function render(){
        global $current_user;
        $html = '<input  type="hidden" data-field="' . $this->field . '" class="slf_filter" id="wbk_filter_access" >';  
        return $html;
    }
    public function getSql(){
        if ( current_user_can('manage_options') ){
            $result = ' 1 ';
        } else {
            $result = ' id = 0 ';
        }
	  	return $result;    	
    }
}
