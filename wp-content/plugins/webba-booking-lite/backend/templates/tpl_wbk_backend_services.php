<!-- Webba Booking backend options page template --> 
<?php
    // check if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;
    date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) ); 
    $bh = WBK_Date_Time_Utils::renderBHForm();
?>
<div id="dialog-confirm-delete" title="<?php echo __( 'Confirm action', 'wbk') ?>" >
    <p> 
        <?php echo __( 'These services will be permanently deleted and cannot be recovered. Continue?', 'wbk' ) ?>
    </p>
</div>
<div id="dialog-interval-error" title="<?php echo __( 'Error', 'wbk') ?>" >
    <p> 
        <?php echo __( 'Unable to add the gap.', 'wbk' ) ?>
    </p>
</div>
<div id="dialog-interval-error-2" title="<?php echo __( 'Error', 'wbk') ?>" >
    <p> 
        <?php echo __( 'Unable to set the gap.', 'wbk' ) ?>
    </p>
</div>
<div id="dialog-add-service">
    <div id="service_dialog_left">
            <?php
                $format_js = get_option( 'wbk_date_format_backend', 'm-d-y');
                $format_js = str_replace('d', 'dd', $format_js );
                $format_js = str_replace('m', 'mm', $format_js );
                $format_js = str_replace('y', 'yyyy', $format_js );      
                echo '<input type="hidden" id="wbk_backend_date_format" value="' . $format_js . '">';
            ?>
            <label for="wbk-service-name"><?php echo __( 'Name', 'wbk') ?> <span class="input-error" id="error-name"></span></label><br/>
            <input id="wbk-service-name" class="wbk-long-input" type="text" value="" /><br/>
            <input id="wbk-service-prev-name" type="hidden" value="" /> 
            <label for="wbk-service-desc"><?php echo __( 'Description', 'wbk') ?></label><br/> 
            <textarea id="wbk-service-desc" class="wbk-long-input"></textarea>
           
            <label for="wbk-service-email"><?php echo __( 'Email', 'wbk') ?></label><br/> 
            <input id="wbk-service-email" class="wbk-long-input" type="text" value="" /><br/>

            <label for="wbk-service-quantity"><?php echo __( 'Maximum booking count per time slot', 'wbk') ?></label><br/> 
            <input id="wbk-service-quantity" class="wbk-long-input" type="text" value="1" /><br/>

            <label for="wbk-service-priority"><?php echo __( 'Priority', 'wbk') ?></label><br/> 
            <input id="wbk-service-priority" class="wbk-long-input" type="text" value="0" /><br/>
            
            <label for="wbk-service-duration"><?php echo __( 'Duration (in minutes)', 'wbk') ?></label><br/>          
            <input id="wbk-service-duration" type="text" value="" class="wbk-long-input"><br/>
            <label for="wbk-service-interval"><?php echo __( 'Gap (in minutes)', 'wbk') ?></label><br/>
            <input id="wbk-service-interval" class="wbk-long-input" type="text"><br/>
            <label for="wbk-service-step"><?php echo __( 'Step', 'wbk') ?></label><br/>
            <input id="wbk-service-step" class="wbk-long-input" type="text" value="" ><br/>
            <label for="wbk-service-users"><?php echo __( 'Available to users', 'wbk') ?></label><br/>

            <?php
                $arr_users_admin = WBK_Db_Utils::getAdminUsers();  
                $arr_users_not_admin = WBK_Db_Utils::getNotAdminUsers();
                $html = '<select name="wbk-user-list" class="wbk-user-list" id="wbk-user-list" multiple>';
                    foreach ( $arr_users_admin[0] as $user ) {
                        $user_info = get_userdata($user);
                        $html .=  '<option value="' . $user . '" disabled>' . $user_info->user_login . __( ' (has access)', 'wbk' ) . '</option>';
                    }
                if( isset( $arr_users_not_admin[0] ) ){
                    foreach ( $arr_users_not_admin[0] as $user ) {
                        $user_info = get_userdata($user);
                        $html .=  '<option value="' . $user . '">' . $user_info->user_login . '</option>';
                    }            
                }
                $html .= '</select>';
                echo $html;
            ?>
            <label for="wbk-form-list"><?php  echo __( 'Select form', 'wbk') ?></label><br/> 
            <?php
                $html =  '<select name="wbk-form-list" class="wbk-long-input" id="wbk-form-list" >';
                $html .= '<option value="0">' . __( 'default form', 'wbk' ) . '</option>';
                $arr_forms =  WBK_Db_Utils::getCF7Forms();
                if ( count( $arr_forms ) > 0 ) {
                    foreach ($arr_forms as $form ) {
                        $html .=  '<option value="' . $form->id . '">' . $form->name . '</option>';
                    }
                }
                $html .= '</select>';
                echo $html;
            ?>
    </div>
    <div id="service_dialog_left2">
            <label for="wbk-service-users"><?php echo __( 'Price', 'wbk') ?></label><br/>
            <input  id="wbk-service-price" type="text" value="0.00" class="wbk-long-input"><br/>
            <label for="wbk-service-payment_methods"><?php echo __( 'Payment methods', 'wbk') ?></label><br/>
            <select name="wbk-payment-methods" class="wbk-payment-methods" id="wbk-payment-methods" multiple> 
                <option value="paypal">PayPal</option>
                <option value="stripe">Stripe</option>           
                <option value="arrival">On arrival</option>
                <option value="bank">Bank transfer</option>                       
                <option value="woocommerce">WooCommerce</option>                                      
            </select>
            <label for="wbk-service-notification_template"><?php echo __( 'Notification email template', 'wbk') ?></label><br/>
            <select class="wbk-long-input"      name="wbk-service-notification_template"  id="wbk-service-notification_template" > 
                <option value="0">Default</option>           
                <?php
                    $tamplates = array();
                    $templates =  WBK_Db_Utils::getIndexedNames( 'wbk_email_templates' );
                    foreach ( $templates  as $template ) {
                         echo '  <option value="' . $template->id . '">' . $template->name .'</option>';
                    }
                ?>     
            </select>
            <label for="wbk-service-reminder_template"><?php echo __( 'Reminder email template', 'wbk') ?></label><br/>
            <select class="wbk-long-input"  name="wbk-service-reminder_template"  id="wbk-service-reminder_template" > 
                <option value="0">Default</option>                
                <?php
                    $tamplates = array();
                    $templates =  WBK_Db_Utils::getIndexedNames( 'wbk_email_templates' );
                    foreach ( $templates  as $template ) {
                         echo '  <option value="' . $template->id . '">' . $template->name .'</option>';
                    }
                ?>     
            </select>
            <label for="wbk-service-invoice_template"><?php echo __( 'Invoice email template', 'wbk') ?></label><br/>
            <select class="wbk-long-input"  name="wbk-service-invoice_template"  id="wbk-service-invoice_template" > 
                <option value="0"><?php echo __( 'Not set', 'wbl' )?></option>                
                <?php
                    $tamplates = array();
                    $templates =  WBK_Db_Utils::getIndexedNames( 'wbk_email_templates' );
                    foreach ( $templates  as $template ) {
                         echo '  <option value="' . $template->id . '">' . $template->name .'</option>';
                    }
                ?>     
            </select>
            <label for="wbk-service-prepare-time"><?php echo __( 'Preparation time (days)', 'wbk') ?></label><br/>
            <input  id="wbk-service-prepare-time" type="text" value="0" class="wbk-long-input"><br/>

            <label for="wbk-service-date-range"><?php echo __( 'Availability date range. Leave empty to set unlimited.', 'wbk') ?></label><br/>
            <input  id="wbk-service-date-range" type="text" value="" class="wbk-long-input"><br/>

            <label for="wbk-service-gg_calendar"><?php echo __( 'Google Calendar', 'wbk') ?></label><br/>
            <select class="wbk-long-input" multiple name="wbk-service-gg_calendar"  id="wbk-service-gg_calendar" > 
                <?php
                    $calendars = array();
                    $calendars =  WBK_Db_Utils::getIndexedNames( 'wbk_gg_calendars' );
                    foreach ( $calendars  as $calendar ) {
                         echo '  <option value="' . $calendar->id . '">' . $calendar->name .'</option>';
                    }
                ?>     
            </select>
            <label for="wbk-service-multiple-low-limit"><?php echo __( 'Low limit for multiple mode. Leave empty to not use low limit', 'wbk') ?></label><br/>
            <input  id="wbk-service-multiple-low-limit" type="text" value="" class="wbk-long-input"><br/>

            <label for="wbk-service-multiple-limit"><?php echo __( 'Limit for multiple mode. Leave empty to global option', 'wbk') ?></label><br/>
            <input  id="wbk-service-multiple-limit" type="text" value="" class="wbk-long-input"><br/>
    </div>
    <div id="service_dialog_right">
        <?php           
            echo $bh;
        ?>
    </div>
    <div style="clear:both"></div>
</div>
<div class="wrap">
	<h2 class="wbk_panel_title"><?php  echo __( 'Services', 'wbk' ); ?>
    <a style="text-decoration:none;" href="http://webba-booking.com/documentation/services-management/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>
    </h2>
    <div class="notice notice-warning is-dismissible"><p>Please, note that Email notifications (except administrator's message options), PayPal, Stripe, Google Calendar, iCalendar, Coupons, WooCommerce integration and CSV export elements are for demo purpose only. To unlock notifications, payment and csv-export features, please, upgrade to Premium version. <a  rel="noopener"  href="https://codecanyon.net/item/appointment-booking-for-wordpress-webba-booking/13843131?ref=WebbaPlugins" target="_blank">Upgrade now</a>. </p>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>

    <div class="row">
        <table  class="service_table"  >
            <thead>
                <tr class="table_title">
                        <th>
                        </th>       
                        <th class="table_title">
                            <?php echo __( 'Name', 'wbk' ) ?>
                        </th>       
                        <th class="table_title">
                            <?php echo __( 'Description', 'wbk' ) ?>
                        </th>       
                        <th class="table_title">
                            <?php echo __( 'Email', 'wbk' ) ?>
                        </th>       
                        <th class="table_title">
                            <?php echo __( 'Duration ', 'wbk' ) ?>
                        </th>  
                        <th class="table_title">
                            <?php echo __( 'Gap', 'wbk' ) ?>
                        </th> 
                        <th class="table_title">
                            <?php echo __( 'Step', 'wbk' ) ?>
                        </th>       
                        <th class="table_title">
                            <?php echo __( 'Items', 'wbk' ) ?>
                        </th>                   
                        <th class="table_title">
                            <?php echo __( 'Business hours', 'wbk' ) ?>
                        </th>
                        <th class="table_title">
                            <?php echo __( 'Users', 'wbk' ) ?>
                        </th> 
                        <th class="table_title">
                            <?php echo __( 'Price', 'wbk' ) ?>
                        </th>   
                </tr>
            </thead>        
            <tbody>
        <?php 
            // get ids of services
            $ids = WBK_Db_Utils::getServices();
            foreach ( $ids as $id ) {
                $service = new WBK_Service();
                if ( !$service->setId( $id ) ){
                    continue;
                }
                if ( !$service->load() ){
                    continue;
                }
        ?>
            <tr id="row_<?php echo $service->getId(); ?>">
                    <td>
                        <input type="checkbox" class="chk_row" id="chk_row_<?php echo $service->getId(); ?>" />
                    </td>       
                    <td>
                        <div id="value_name_<?php echo $service->getId(); ?>" class="value_container"><?php echo $service->getName() . ' (' . $service->getId() . ')' ?></div>
                    </td>       
                    <td>
                        <div id="value_description_<?php echo $service->getId(); ?>" class="value_container"><?php echo $service->getDescription( true ); ?></div>                                            
                    </td>       
                    <td>
                        <div id="value_email_<?php echo $service->getId(); ?>" class="value_container"><?php echo $service->getEmail(); ?></div>                            
                    </td>       
                    <td>                
                        <div id="value_duration_<?php echo $service->getId(); ?>" class="value_container"><?php echo $service->getDuration() . ' ' . __( 'minutes', 'wbk' ) ?></div>
                     </td>
                    <td>                
                        <div id="value_interval_<?php echo $service->getId(); ?>" class="value_container"><?php echo $service->getInterval() . ' ' . __( 'minutes', 'wbk' ) ?></div>
                    </td>
                    <td>                
                        <div id="value_step_<?php echo $service->getId(); ?>" class="value_container"><?php echo $service->getStep() . ' ' . __( 'minutes', 'wbk' ) ?></div>
                    </td>    
                    <td>                
                        <div id="value_quantity_<?php echo $service->getId(); ?>" class="value_container"><?php echo $service->getQuantity() ?></div>
                    </td>                                    
                    <td>
                        <div class="wbk-font-10" id="value_business_hours_<?php echo $service->getId(); ?>">
                            <?php
                                 echo  WBK_Date_Time_Utils::renderBHCell( $service->getBusinessHours() );                           
                            ?>
                        </div>
                    </td>
                    <td>                
                        <div id="value_users_<?php echo $service->getId(); ?>" class="value_container">
                            <?php   
                                $arr_users = explode( ';', $service->getUsers() );
                                $usernames = '';
                                foreach ( $arr_users as $user ) {
                                    if ( $user == '' ) {
                                        continue;
                                    }
                                    $user_info = get_userdata( $user[0] );
                                    if( is_object( $user_info) ){
                                        $usernames .=  $user_info->user_login.', ';
                                    }
                                }
                                if ( $usernames != '' ) {
                                    $usernames = rtrim( $usernames, ', ' );
                                }
                                echo $usernames;                                 
                            ?>
                        </div>
                    </td>
                    <td>                
                        <div id="value_price_<?php echo $service->getId(); ?>" class="value_container"><?php echo number_format( $service->getPrice(),  get_option( 'wbk_price_fractional', '2' ) ) ?></div>
                    </td>                       
            </tr>
        <?php
            }
         ?>
            </tbody>
        </table>
    </div>
    <a class="button" href="javascript:add_service()"><?php echo __( 'Add service', 'wbk' );  ?></a>
    <a class="button" id="btn_service_delete" disabled="disabled" href="javascript:delete_service()" ><?php echo __( 'Delete service', 'wbk' );  ?></a>
    <a class="button" id="btn_service_edit"  disabled="disabled" href="javascript:edit_service()" ><?php echo __( 'Edit service', 'wbk' );  ?></a>
</div>
<?php
    date_default_timezone_set( 'UTC' ); 
?>