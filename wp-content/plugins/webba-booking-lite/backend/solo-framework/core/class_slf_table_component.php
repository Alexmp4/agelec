<?php
// Solo Framework table component control class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableComponent extends stdClass {
 
	
	public function __construct( $title, $name, $value, $data_source ) {

        $this->title = $title;      
        $this->name = $name;
        $this->value = $value;
        $this->data_source = $data_source;
        
 	}
    public function renderCell(){


    }
    public function renderControl(){


    }


}
