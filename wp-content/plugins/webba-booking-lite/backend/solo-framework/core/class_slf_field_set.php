<?php
// Solo Framework table class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFFieldSet extends stdClass {
	public function __construct( $editable, $allow_add ) {
        $this->field_set = array();      
        $this->editable = $editable;
		$this->allow_add = $allow_add;
	}
	public function append( $field ){
		$this->fields[ $field->name ] = $field;
	}
	public function getByName( $name ){
		if( isset( $this->fields[ $name ] ) ){
			return $this->fields[ $name ];
		} else {
			return false;
		}
	}
}



?>