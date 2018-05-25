<!-- Webba Booking backend options page template --> 
<?php
    // check if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;
    date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
?> 
<div class="wrap">
	<h2 class="wbk_panel_title"><?php  echo 'Webba Booking ' . __( 'Settings', 'wbk' ); ?>
    <a style="text-decoration:none;" href="http://webba-booking.com/documentation/getting-started/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>
    </h2>
    <div class="notice notice-warning is-dismissible">
    <p>Please, note that Email notifications (except administrator's message options), PayPal, Stripe, Google Calendar, iCalendar, CSV export, WooCommerce integration and Coupons elements are for demo purpose only. To unlock notifications, payment and csv-export features, please, upgrade to Premium version. <a  rel="noopener"  href="https://codecanyon.net/item/appointment-booking-for-wordpress-webba-booking/13843131?ref=WebbaPlugins" target="_blank">Upgrade now</a>. </p>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    
 	<?php
        if( function_exists( 'settings_errors' ) ) {
            settings_errors(); 
        }
    ?>
    <form method="post" action="options.php">
    
    <?php
    // output settings tabs 
    settings_fields( 'wbk_options' );
   // settings_fields( 'wbk_schedule_settings_section' );
    global $wp_settings_sections, $wp_settings_fields;
    $page = 'wbk-options';
    if ( !isset( $wp_settings_sections[$page] ) ){
        return;      
    } 
  
    echo '<div id="tabs">';
    
    echo '<ul>';
    foreach( (array)$wp_settings_sections[$page] as $section ) {
        if( !isset( $section['title'] ) )
            continue;
        printf( '<li><a href="#%1$s">%2$s</a></li>', $section['id'], $section['title']  );
    }
    echo '</ul>';
    foreach( (array)$wp_settings_sections[$page] as $section ) {
        printf( '<div id="%1$s" >', $section['id'] );
        if( !isset($section['title']) ){
            continue;
        }
        
        if( $section['callback'] ) {
            call_user_func($section['callback'], $section);
        }
        if( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']] ) ) {
            continue;
        }
        echo '<table class="form-table">';
        do_settings_fields( $page, $section['id'] );
        
        echo '</table>';
        echo '</div>';
    }
    echo '</div>';
    submit_button(); 
    
    ?>
  
    </form> 
</div>
<?php
date_default_timezone_set( 'UTC' );
?>
