<?php
// Solo Framework table text component
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableTime extends SLFTableComponent {


	public function __construct( $title, $name, $value, $validation ) {
		parent::__construct( $title, $name, $validation );
	}
	
    public function renderCell(){
		
    	$format = get_option( 'time_format' );
		return date_i18n( $format,   $this->value );  	

    }
    public function renderControl(){
    	$format = get_option( 'time_format' );
    	$html = '<label class="slf_table_component_label" >' . $this->title . '</label>';
		$html .= '<input class="slf_table_component_input" name="' . $this->name . '" data-type="time"  type="text" value="' . date_i18n( $format,   $this->value ) . '"  />';
		return $html;
    }


}
