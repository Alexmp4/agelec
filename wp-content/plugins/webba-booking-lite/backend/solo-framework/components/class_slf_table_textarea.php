<?php
// Solo Framework table text component
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableTextarea extends SLFTableComponent {


	public function __construct( $title, $name, $value, $data_source ) {
		parent::__construct( $title, $name, $value, $data_source );
	}
	
    public function renderCell(){
    	 
		return $this->value;    	
    }
    public function renderControl(){
    	$html = '<label class="slf_table_component_label" >' . $this->title . '</label>';
		$html .= '<textarea type="text" class="slf_table_component_input slf_table_component_textarea" name="' . $this->name . '"    >' .  $this->value . '</textarea>'; 
		return $html;
    }


}
