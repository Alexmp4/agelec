<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFComponent extends stdClass {
	
	public function __construct( $param ) {
		$this->name = $param['name'];
        $this->desc = $param['desc'];
        $this->slug = $param['slug'];
        $this->value = $param['value'];
        $this->class_name = get_class($this);
       
        if ( isset( $param['css_class'] ) ){
        	$this->css_class = $param['css_class'];
        } else {
        	$this->css_class = 'none';
        }
        if ( isset( $param['css_prop'] ) ){
        	$this->css_prop = $param['css_prop'];
        } else {
    	    $this->css_prop = 'none';
        }

	}

    }