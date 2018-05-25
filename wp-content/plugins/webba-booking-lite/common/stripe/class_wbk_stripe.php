<?php
 
// webba booking Stripe integration class
class WBK_Stripe{
	protected 
	$api_key;
	protected 
	$api_sectet;
	protected
	$tax;
	protected 
	$currency;

	public function init(){	 
		 
		return FALSE;
	}
	public static function getCurrencies(){
		return array();
	}
	public static function isCurrencyZeroDecimal( $currency ){
	 	return FALSE;		
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
			if( !in_array( 'stripe', $arr_items) ){
				return '';
			}
		}		
		$html = '';	  
		$stripe_btn_text = get_option( 'wbk_stripe_button_text', 'Pay with credit card' );
		 
		$html .= '<input class="wbk-button wbk-width-100 wbk-mt-10-mb-10 wbk-payment-init" data-method="stripe" data-app-id="'. implode(',',  $appointment_ids ) . '"  value="' . $stripe_btn_text . '  " type="button">';	
		return $html;
	}
    public function createPayment( $method, $app_ids  ){     	
        return '';
    }
    public function getOrderData( $app_ids ){ 		 
		return array( '', '' );
    } 
    public function charge( $app_ids, $amount, $token ){
 		return;
    }
}
?>