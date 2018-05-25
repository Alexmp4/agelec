<?php
// Solo Framework table text component
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableEmail extends SLFTableComponent {


	public function __construct( $title, $name, $value, $data_source  ) {
		parent::__construct( $title, $name, $value, null );
	}
	
    public function renderCell(){
		return $this->value;    	

    }
    public function renderControl(){
    	$html = '<label class="slf_table_component_label" >' . $this->title . '</label>';
		$html .= '<input class="slf_table_component_input" name="' . $this->name . '" data-type="text" ' . $this->value . '"  />';
		return $html;
    }


}
