<!-- Webba Booking service categories page template --> 
<?php
    // check if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
    $format_js = get_option( 'wbk_date_format_backend', 'm-d-y');
    $format_js = str_replace('d', 'dd', $format_js );
    $format_js = str_replace('m', 'mm', $format_js );
    $format_js = str_replace('y', 'yyyy', $format_js );      
    echo '<input type="hidden" id="wbk_backend_date_format" value="' . $format_js . '">';
?>
<div class="wrap">
 	<h2 class="wbk_panel_title"><?php  echo __( 'Coupons', 'wbk' ); ?>
    </h2>
    <div class="notice notice-warning is-dismissible">
    <p>Please, note that Email notifications (except administrator's message options), PayPal, Stripe, Google Calendar, iCalendar, CSV export, WooCommerce integration and Coupons elements are for demo purpose only. To unlock notifications, payment and csv-export features, please, upgrade to Premium version. <a  rel="noopener"  href="https://codecanyon.net/item/appointment-booking-for-wordpress-webba-booking/13843131?ref=WebbaPlugins" target="_blank">Upgrade now</a>. </p>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
        <?php        
            $table = new WBK_Coupons_Table();
            $html = $table->render();
            echo $html;
        ?>                                            
</div>
