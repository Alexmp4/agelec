<?php
// Solo Framework table multiselet component
if ( ! defined( 'ABSPATH' ) ) exit;
class SLFTableServiceMultiSelect extends SLFTableComponent {
	public function __construct( $title, $name, $value, $data_source ) {
		parent::__construct( $title, $name, $value, null );
		$this->data_source = $data_source;
	}
    public function renderControl(){
    	$html = '<label class="slf_table_component_label" >' . $this->title . '</label>';
		$html .= '<select multiple class="slf_table_component_select slf_table_component_service_multi_select slf_table_component_input" name="' . $this->name . '" data-type="select" data-init="' . $this->value . '"  >';
		$arr_value = explode(',', $this->value );
	 	$options =  WBK_Db_Utils::getServices();
	 	foreach( $options as $id ){ 		
	 		$service = new WBK_Service();
            if ( !$service->setId( $id ) ){
                continue;
            }
            if ( !$service->load() ){
                continue;
            }

	 		$selected = '';
	 		if( in_array( $id, $arr_value ) ){
	 			$selected = ' selected ';
	 		}

			$html .= '<option ' . $selected . ' value="' . $id . '" >' . $service->getName() . '</option>';
	 	}
 		$html .= '</select>';
		return $html;
    }
    public function renderCell(){
    	$arr_value = explode(',', $this->value );
	  	$arr_result = array();
	 	foreach( $arr_value as $id ){ 		
	 		$service = new WBK_Service();
            if ( !$service->setId( $id ) ){
                continue;
            }
            if ( !$service->load() ){
                continue;
            }		
		 	$arr_result[] = $service->getName();
	 	}
	 	return implode(', ', $arr_result);

    }


}
