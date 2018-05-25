<?php
// Solo Framework table filter class
if ( ! defined( 'ABSPATH' ) ) exit;
class SLFTableFilterDateRange extends SLFTableFilter {
	public function __construct( $title, $field ) {
  		parent::__construct( $title, $field );
   	}
    public function valid(){
        return TRUE;
    }
    public function set( $value ){
 	   	$arr = explode( ';', $value );
 	   	if ( count( $arr ) != 2 ){
 	   		return FALSE;
 	   	} 
        $arr[0] = strtotime( $arr[0] );
        $arr[1] = strtotime( $arr[1] );
 	   	if ( !is_numeric( $arr[0] )  ||  !is_numeric( $arr[1] ) ){
			return FALSE;
 	   	}
 	   	if ( $arr[0] < 1449446400 || $arr[0] > 2160054510 ){
			return FALSE;
		}
 	   	if ( $arr[1] < 1449446400 || $arr[1] > 2160054510 ){
			return FALSE;
		}
		$this->start = $arr[0];
		$this->end = $arr[1];
		return TRUE;
    }
    public function setDefault(){
    	$this->start = strtotime('today midnight');
    	$this->end = $this->start + 24 * 60 * 60 * 14;
    }
    public function render(){
        $format = get_option( 'wbk_date_format_backend', 'm-d-y');
        $format = str_replace('y', 'Y', $format );       

        $format_js = get_option( 'wbk_date_format_backend', 'm-d-y');
        $format_js = str_replace('d', 'dd', $format_js );
        $format_js = str_replace('m', 'mm', $format_js );
        $format_js = str_replace('y', 'yy', $format_js );

    	$start = date( $format, $this->start );
    	$end   = date( $format, $this->end );
	    $html = '<div class="slf-filter-container">';
        $html .= '<input type="hidden" id="wbk_filter_date_format" value="' . $format_js . '">';
        $html .= '<span class="slf-filter-title">' . $this->title .'</span>';
	    $html .= '<input type="text" value="'. $start .'" class="slf_date_range_start slf-date slf-input-50" >-';
		$html .= '<input type="text" value="'. $end .'" class="slf_date_range_end slf-date slf-input-50" >';
		$html .= '<input data-field="' . $this->field . '" type="hidden" value="'. $start . ';' . $end .'" class="slf_filter slf_date_range" >';      
        $html .='</div>';
        return $html;
    }
    public function getSql(){
    	global $wpdb;
    	$result = $wpdb->prepare( ' day >= %d AND day <= %d ',   $this->start,  $this->end );
		return $result;    	
    }
}
