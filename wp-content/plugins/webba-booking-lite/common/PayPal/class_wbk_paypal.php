<?php

class WBK_PayPal{
	protected 
	$apiContext;
	protected
	$currency;
	protected 
	$tax;
	protected
	$fee;
	protected
	$referer;
	protected
	$experience_profile_id;
	public function init( $referer ){
	    return FALSE;
	}     
    public function createPaymentPaypal( $item_name, $price, $quantity, $sku  ){
		return FASLE;   
    }
    public function createPayment( $method, $app_id  ){       
    	return -1;     
    }
	static function	renderPaymentMethods( $service_id, $appointment_ids ){
		global $wbk_wording;
		if( !is_array( $service_id ) ){
			$services = array( $service_id );
		} else {
			$services = $service_id;
		} 
		foreach( $services as $service_id ){
		 
			$service = new WBK_Service();
		    if ( !$service->setId( $service_id ) ){
		        return 'Unable to access service: wrong service id.';      
		    }
		    if ( !$service->load() ){
		         return 'Unable to access service: load failed.';      
		    }
		    if ( $service->getPayementMethods() == '' ){
		      	return '';
		    }
		    $arr_items = explode( ';', $service->getPayementMethods() );
			if( !in_array( 'paypal', $arr_items) ){
				return '';
			}
		}		
		$html = '';	  
		$paypal_btn_text = get_option( 'wbk_payment_pay_with_paypal_btn_text', '' );
		if( $paypal_btn_text == '' ){
			$paypal_btn_text = sanitize_text_field( $wbk_wording['paypal_btn_text'] );
		}
		$html .= '<input class="wbk-button wbk-width-100 wbk-mt-10-mb-10 wbk-payment-init" data-method="paypal" data-app-id="'. implode(',',  $appointment_ids ) . '"  value="' . $paypal_btn_text . '  " type="button">';	
		return $html;
	}
}
?>