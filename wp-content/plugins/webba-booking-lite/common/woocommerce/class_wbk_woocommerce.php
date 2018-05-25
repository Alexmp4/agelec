<?php
/*
Webba Booking integration with WooCommerce
*/ 
 
class WBK_WooCommerce{
    static function renderPaymentMethods( $service_id, $appointment_ids ){
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
            if( !in_array( 'woocommerce', $arr_items) ){
                return '';
            }
        }       
        $html = '';   
        $woo_btn_text =  wbk_get_translation_string( 'wbk_woo_button_text', 'wbk_woo_button_text' , 'Add to cart' );  
         
        $html .= '<input class="wbk-button wbk-width-100 wbk-mt-10-mb-10 wbk-payment-init" data-method="woocommerce" data-app-id="'. implode(',',  $appointment_ids ) . '"  value="' . $woo_btn_text . '  " type="button">';  
        return $html;
    }
    static function addToCart( $appointment_ids ){         
        return __( 'Payment method not supported', 'wbk' );                 
    }
}
?>