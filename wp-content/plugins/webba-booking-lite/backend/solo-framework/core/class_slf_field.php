<?php
// Solo Framework table class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFField extends stdClass {
	public function __construct( $param = array() ) {
        $this->title = $param['title'];
        $this->name = $param['name'];
        $this->format = $param['format'];
        $this->component = $param['component'];
        $this->render_cell = $param['render_cell'];
        $this->render_control = $param['render_control'];
        if ( isset($param['assoc'] ) ) {
            $this->assoc = $param['assoc'];
        } else {
            $this->assoc = null;
        }
        if ( isset($param['data_source'] ) ) {
            $this->data_source  = $param['data_source'];
        } else {
            $this->data_source = null;
        } 
         if ( isset($param['validation'] ) ) {
            $this->validation = $param['validation'];
        } else {
            $this->validation = null;
        }  
	}
}



?>