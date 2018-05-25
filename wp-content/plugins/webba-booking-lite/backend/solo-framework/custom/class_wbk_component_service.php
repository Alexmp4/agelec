<?php
// Solo Framework table text component
if ( ! defined( 'ABSPATH' ) ) exit;
class SLFTableWbkService extends SLFTableComponent {
	public function __construct( $title, $name, $value, $data_source ) {
		parent::__construct( $title, $name, $value, null );
	}
    public function renderCell(){
		$error_message = 'Internal error: unable to init service.';
		if ( !is_numeric( $this->value ) ){
			return $error_message;
		}
		$service = new WBK_Service();
		if ( !$service->setId( $this->value  ) ) {
			return $error_message;
		}
		if ( !$service->load() ) {
 			return $error_message;
		}
		return $service->getName();    	   	
    }
    public function renderControl(){
    	global $current_user;
		$html = '<label class="slf_table_component_label" >' . $this->title . '</label>';
 		$html .= '<select  class="slf_table_component_select wbk-service-select slf_table_component_input" name="' . $this->name . '">';
		$html .= '<option value="-1" >' . __( 'Select service', 'wbk' ) . '</option>';
 		$arrIds = WBK_Db_Utils::getServices();
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
			$selected = '';
			if( $id == $this->value ){
				$selected = 'selected="selected"'; 
			}
			$html .= '<option data-ext="' . $service->getDuration() . '" ' . $selected . ' value="' . $id . '" >' . $service->getName() . '</option>';
		}	 	
		$html .= '</select>';
		return $html;
    }
}
