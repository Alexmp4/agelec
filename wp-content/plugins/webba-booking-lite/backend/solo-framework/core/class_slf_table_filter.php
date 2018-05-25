<?php
// Solo Framework table filter class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableFilter extends stdClass {
 	
	public function __construct( $title, $field ) {
       $this->title = sanitize_text_field( $title );
       $this->field = sanitize_text_field( $field );
        
 	}
  
}
