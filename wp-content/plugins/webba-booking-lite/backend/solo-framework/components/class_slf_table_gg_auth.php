<?php
// Solo Framework table html editor
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableGGAuth extends SLFTableComponent {

	public function __construct( $title, $name, $value, $data_source ) {
		parent::__construct( $title, $name, $value, null );
		$this->data_source = $data_source;
	}
	
    public function renderCell(){
		$html = __( 'Authorization is available only in the premium version', 'wbk' );
		$html .= '<br><a  rel="noopener"  href="https://codecanyon.net/item/appointment-booking-for-wordpress-webba-booking/13843131?ref=WebbaPlugins" target="_blank">' . __( 'Upgrade now', 'wbk' ) . '</a>';

	    return $html;
    }
    public function renderControl(){      

		return '';
    }


}
