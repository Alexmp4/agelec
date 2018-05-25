<?php
// Solo Framework table text component
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableHiddenText extends SLFTableComponent {


	public function __construct( $title, $name, $value, $data_source  ) {
		parent::__construct( $title, $name, $value, null );
	}
	
    public function renderCell(){
    	if ( $this->name == 'status' &&   $this->value == 'pending' ){
    		return __( 'Booked (not paid)', 'wbk' );
    	}
		return $this->value;    	
    }
    public function renderControl(){
		$html = '<input  type="hidden" class="slf_table_component_input slf_table_component_text" name="' . $this->name . '"   value="' . $this->value . '"  />';
		return $html;
    }


}
