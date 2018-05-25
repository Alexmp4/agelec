<?php
ob_start();
if (!defined("ZC4WP")) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

try {
    $dbKey = get_option('zc4wp_a_apikey');
    $ageIndicator = '';
    $apiKeyVal = '';
    if($dbKey != '') {
        $apiKeyVal = trim($dbKey['api_key']);
        $ageIndicator = $dbKey['ageIndicator'];
    }
    global $wpdb; 
    if($ageIndicator == 'new')  {
        $wpdb->query(
            'DELETE FROM ' . $wpdb->prefix  . 'options WHERE option_name like "zc4wp_a_%";'
        );
?>
        <div style="display:none">
            <form action="options.php" method="post" id="zc4wp_a_apikey" autocomplete="off">
                <?php settings_fields( 'zoho_settings' ); ?>
                <h3>Zoho Campaigns Integration</h3>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr width="100%">
                        <td width="180" scope="row"><label for="zoho_api_key">Enter API key:</label></td>
                        <td width="500">
                            <input type="text" class="zcctmzfldpan" placeholder="Your Zoho Campaign API key" onkeyup="if (event.keyCode == 13) { api_key_verfication();}else{apiKeyValidator();}" id="zc_api_key" name="zc4wp_a_apikey[api_key]" value="<?php echo $apiKeyVal; ?>" size="20" autocomplete="on" />
                            <input type="hidden" name="zc4wp_a_apikey[ageIndicator]" id="ageIndicator" value="old">
                            <input type="hidden" name="zc4wp_a_apikey[accountId]" id="accountId" value="<?php if($dbKey != ''){echo  $dbKey['accountId'];}else{echo "-1";}?>">
                            <input type="hidden" name="zc4wp_a_apikey[apiKeyCount]" id="apiKeyCount" value="<?php if($dbKey != ''){echo  $dbKey['apiKeyCount'];}else{echo "0";}?>">
                            <input type="hidden" name="zc4wp_a_apikey[orgName]" id="orgName" value="<?php if($dbKey != ''){echo  $dbKey['orgName'];}else{echo "-1";}?>">
                            <input type="hidden" name="zc4wp_a_apikey[active]" id="active" value="<?php if($dbKey != ''){echo  $dbKey['active'];}else{echo "-1";}?>">
                            <input type="hidden" name="zc4wp_a_apikey[emailId]" id="emailId" value="<?php if($dbKey != ''){echo  $dbKey['emailId'];}else{echo "-1";}?>">
                            <input type="hidden" name="zc4wp_a_apikey[integratedDate]" id="integratedDate" value="<?php if($dbKey != ''){echo  $dbKey['integratedDate'];}else{echo "-1";}?>">                            
                            <input type="hidden" name="zc4wp_a_apikey[firsttimesave]" id="firsttimesave" value="<?php if($dbKey != ''){echo  $dbKey['firsttimesave'];}else{echo "-1";}?>">
                        </td>
                        <td style="padding-left:15px;">
                            <span id="zc_api_key_error"></span>
                        </td>
                    </tr>
                    <tr><td></td><td><a target="_blank" id="help_link" href="https://www.zoho.com/campaigns/help/api/authentication-token.html">Learn how to generate the API key.</a></td><td></td></tr>
                    <tr><td></td><td height="60"><input type="button" value="Activate Now" id="save_api_key" onclick="return api_key_verfication()" class="button button-primary" style="box-shadow:none;margin-right:15px;"><input type="button" value="Cancel" id="cancel_api_key_changes" onclick="cancel_changes()" style="display:none;" class="button"></td><td></td></tr>
                    <tr id="form_page_link_tr" style="display:none;width:100%"><td width="180">To Procced:</td><td><a href="admin.php?page=zc-forms">Create Forms</a></td><td></td></tr>
                </table>
                <input type="text" value="" id="hidden_text" style="display:none;" name="zc4wp_a_apikey[error_message]"/>
            </form>
        </div>
        <script type="text/javascript">
            document.getElementById("zc4wp_a_apikey").submit();
        </script>
<?php
    }
    $str = "1:";
    foreach( $wpdb->get_results('SELECT option_name,option_value FROM ' . $wpdb->prefix  . 'options WHERE option_name like "zc4wp_a_%";') as $key => $row) {
        // each column in your row will be accessible like this
        $my_column = $row->option_name;
        $option_value = get_option($my_column);
        $temp = substr($my_column,8);
        if(is_numeric($temp) && $option_value != '' && ( (array_key_exists('simple_form', $option_value) && trim($option_value['simple_form'])  != '') || (array_key_exists('zoho_form', $option_value) && trim($option_value['zoho_form']) != '') ) )   {
            $str = $str . $temp . ":";
        }
    }
    $var_domain = get_option('zc_domain_url');

    if(isset($var_domain) && $var_domain !== FALSE)
        
        {
            $domain_name =  trim($var_domain,' ');
        }
        else 
        {
            $domain_name = 'https://campaigns.zoho.com';
        }

    if($str != "1:") {
        $xmldata = $domain_name . '/api/getmailinglistsprivacy?authtoken=' . $apiKeyVal . '&scope=CampaignsAPI&resfmt=XML&sort=asc&fromindex=1&usertype=1&range=0&sno=' . $str;
    }
    else {
        $xmldata = $domain_name . '/api/getmailinglistsprivacy?authtoken=' . $apiKeyVal . '&scope=CampaignsAPI&resfmt=XML&sort=asc&fromindex=1&usertype=1&range=0';
    }
    $open = wp_remote_post($xmldata, array(
            'method' => 'POST',
            'timeout' => 45
    ));
    $xmlDataRequestResponseBody = wp_remote_retrieve_body($open);
    if (strpos( $xmlDataRequestResponseBody , 'IamError.zc')) {
        $xml = simplexml_load_string('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
    }
    else {
        try {
            $xml = simplexml_load_string($xmlDataRequestResponseBody);
        } catch (Exception $e) {
            $xml = simplexml_load_string('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
        }
    }
    if(isset($xml) && $xml->status == "success" && $xml->code == 2401)    {
        $str = "1:";
        $xmldata = $domain_name . '/api/getmailinglistsprivacy?authtoken=' . $apiKeyVal . '&scope=CampaignsAPI&resfmt=XML&sort=asc&fromindex=1&usertype=1&range=1';
        $open = wp_remote_post($xmldata, array(
                'method' => 'POST',
                'timeout' => 45
        ));
        if (strpos(wp_remote_retrieve_body($open) , 'IamError.zc')) {
            $xml = simplexml_load_string('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
        }
        else {
            try {
                $xml = simplexml_load_string(wp_remote_retrieve_body($open));
            } catch (Exception $e) {
                $xml = simplexml_load_string('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
            }
        }
    }
    if(empty($xml))  {
        $xml =simplexml_load_string('<response uri="/api/getmailinglistsprivacy" version="1"><status>success</status><code>0</code><signupformversion>2</signupformversion><total_list_count>1</total_list_count><list_of_details><list><fl val="validlist">true</fl><fl val="listkey">34594177d382061ba77ae02d1fd93cb6</fl><fl val="listname">My Mailing List 1</fl><fl val="s_no">2</fl></list></list_of_details></response>');
    }
}

catch(Exception $e) {
    echo $e->getMessage();
    die();
}
$function_adder_simple = false;
$GLOBALS['xml'] = $xml;
$GLOBALS['domain_name'] = $domain_name;
$GLOBALS['apikeyval'] = $apiKeyVal;
$GLOBALS['function_adder_simple'] = $function_adder_simple;
$GLOBALS['str'] = $str;
function zc_register_settings_form() {
    $xml = $GLOBALS['xml'];
    $str = $GLOBALS['str'];
    if ($xml->status == 'success' && $xml->code == 0) {
        $list_count = $xml->total_list_count;
        for($i = 2 ; $i <= $list_count + 1; $i++)
        {
            register_setting("zoho_settings_form_" . $i, "zc4wp_a_" . $i, 'zc_validate_settings_id');
        }
    }
}
function zc_zcplugin_form_script() {
    wp_enqueue_style( 'zc_signupform_style1', plugins_url( 'assets/css/signup-formpage-css.css', dirname(__FILE__) ), false, '1.0.0' );
    wp_enqueue_style( 'zc_signupform_style2', plugins_url( 'assets/css/slider.css', dirname(__FILE__) ), false, '1.0.0' );
    wp_enqueue_style( 'zc_signupform_style3', plugins_url( 'assets/css/jquery.ui.datepicker.css', dirname(__FILE__) ), false, '1.0.0' );
    
    $scripts = array('jquery-ui-core', 'jquery-ui-widget','jquery-ui-mouse', 'jquery-ui-sortable', 'jquery-ui-slider', 'jquery-ui-datepicker');
    foreach($scripts as $script) {
        wp_enqueue_script($script);
    }
    global $wp_scripts;
    $sources = array();
    foreach($wp_scripts->registered as $name => $data) {
        if(in_array($name, $scripts)) {
            $sources[$name]['src'] = $data->src;
            $sources[$name]['ver'] = $data->ver;
            $sources[$name]['deps'] = $data->deps;
        }
    }
    foreach($scripts as $script) {
        //wp_deregister_script($script);
        wp_enqueue_script($script,site_url($sources[$script]['src']),$sources[$script]['deps'],$sources[$script]['ver'],false);
    }
    wp_enqueue_script( 'zc_signupform_script4', plugins_url( 'assets/js/signup-formpage-js.js', dirname(__FILE__) ), array(),'1.0.0',false);
    wp_enqueue_script( 'zc_signupform_script6','https://wordpress.maillist-manage.com/js/resource.js', array(),'1.0.0',false);
    wp_enqueue_script( 'zc_signupform_script7','https://wordpress.maillist-manage.com/js/subscribe.js', array(),'1.0.0',false);
    wp_enqueue_script( 'zc_signupform_script8','https://wordpress.maillist-manage.com/js/zccommon.js', array(),'1.0.0',false);
    wp_enqueue_script( 'zc_signupform_script9','https://wordpress.maillist-manage.com/js/optin_min.js', array(),'1.0.0',false);
    wp_enqueue_script( 'zc_signupform_script10', plugins_url( 'assets/js/xregexp.min.js', dirname(__FILE__) ), array(),'1.0.0',false);
    //wp_enqueue_script( 'zc_signupform_script10','https://campaigns.zoho.com/js/util.js', array(),'1.0.0',false);
}
function zc_validate_settings_id($arg) {
    return $arg;
}

function zc_colorboxcreater() {
?>
    <div style="background-color: rgb(0, 0, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(17, 17, 17);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(34, 34, 34);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 51, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(68, 68, 68);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(85, 85, 85);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 102, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(119, 119, 119);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(136, 136, 136);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 153, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(170, 170, 170);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(187, 187, 187);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 204, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(221, 221, 221);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(238, 238, 238);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 255, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div class="zccolors" ></div>
    <br/>
    <div style="background-color: rgb(0, 0, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 0, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 0, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 0, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 0, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 0, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 0, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 0, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 0, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 0, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 0, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 0, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 0, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 0, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 0, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 0, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 0, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 0, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(0, 51, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 51, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 51, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 51, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 51, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 51, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 51, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 51, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 51, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 51, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 51, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 51, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 51, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 51, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 51, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 51, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 51, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 51, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(0, 102, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 102, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 102, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 102, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 102, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 102, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 102, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 102, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 102, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 102, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 102, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 102, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 102, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 102, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 102, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 102, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 102, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 102, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(0, 153, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 153, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 153, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 153, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 153, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 153, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 153, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 153, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 153, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 153, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 153, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 153, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 153, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 153, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 153, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 153, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 153, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 153, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(0, 204, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 204, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 204, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 204, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 204, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 204, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 204, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 204, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 204, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 204, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 204, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 204, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 204, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 204, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 204, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 204, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 204, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 204, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(0, 255, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 255, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 255, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 255, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 255, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(0, 255, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 255, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 255, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 255, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 255, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 255, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(51, 255, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 255, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 255, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 255, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 255, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 255, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(102, 255, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(153, 0, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 0, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 0, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 0, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 0, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 0, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 0, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 0, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 0, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 0, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 0, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 0, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 0, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 0, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 0, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 0, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 0, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 0, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(153, 51, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 51, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 51, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 51, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 51, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 51, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 51, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 51, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 51, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 51, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 51, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 51, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 51, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 51, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 51, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 51, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 51, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 51, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(153, 102, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 102, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 102, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 102, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 102, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 102, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 102, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 102, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 102, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 102, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 102, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 102, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 102, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 102, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 102, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 102, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 102, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 102, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(153, 153, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 153, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 153, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 153, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 153, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 153, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 153, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 153, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 153, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 153, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 153, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 153, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 153, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 153, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 153, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 153, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 153, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 153, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(153, 204, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 204, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 204, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 204, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 204, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 204, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 204, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 204, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 204, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 204, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 204, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 204, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 204, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 204, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 204, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 204, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 204, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 204, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <div style="background-color: rgb(153, 255, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 255, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 255, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 255, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 255, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(153, 255, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 255, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 255, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 255, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 255, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 255, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(204, 255, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 255, 0);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 255, 51);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 255, 102);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 255, 153);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 255, 204);" class="zccolors" onclick="zc_setColor(this);"></div>
    <div style="background-color: rgb(255, 255, 255);" class="zccolors" onclick="zc_setColor(this);"></div>
    <br/>
    <?php
}
function zc_formprinter($apiKeyVal,$domain_name,$id,$sno) {
?>
    <form id="form_<?php echo $sno; ?>" action="options.php" method="post" autocomplete="off" >
        <?php
                settings_fields("zoho_settings_form_" . $sno);
                $dbshortcodeval = get_option("zc4wp_a_" . $sno);
        ?>
        <input type="text" name="zc4wp_a_<?php echo $sno; ?>[saved_form_id]" id="saved_form_id_<?php echo $sno; ?>" style="display:none;" value="
                <?php
                        if ($dbshortcodeval != '' && trim($dbshortcodeval['saved_form_id']) != '') {
                                echo $dbshortcodeval['saved_form_id'];
                        }
                        else {
                                echo "0_s_{$sno}";
                        } 
                ?>"
        >
        <input type="text" name="zc4wp_a_<?php echo $sno; ?>[signupformid]" id="signupformid_<?php echo $sno; ?>" style="display:none;" value="
                <?php
                        if ($dbshortcodeval != '' && trim($dbshortcodeval['signupformid']) != '') {
                                echo $dbshortcodeval['signupformid'];
                        }
                        else {
                                echo "0_s_{$sno}";
                        } 
                ?>"
        >
        <input type="text" id="list_key_<?php echo $sno; ?>" style="display:none;" value="<?php echo $id; ?>">
        <script type="text/javascript">
                zc_savedIdFinder('<?php echo $sno; ?>');
        </script>
        <textarea name="zc4wp_a_<?php echo $sno; ?>[simple_form]" id="hidden_simple_textarea_<?php echo $sno; ?>" style="display:none;">
                <?php
                        if ($dbshortcodeval != '' && trim($dbshortcodeval['simple_form']) != '') {
                                echo $dbshortcodeval['simple_form'];
                        }
                        else {
                                echo '';
                        }
                ?>
        </textarea>
        <textarea name="zc4wp_a_<?php echo $sno; ?>[zoho_form]" id="hidden_zcform_textarea_<?php echo $sno; ?>" style="display:none;">
                <?php
                        if ($dbshortcodeval != '' && trim($dbshortcodeval['zoho_form']) != '') {
                                echo rawurlencode($dbshortcodeval['zoho_form']);
                        }
                        else {
                                echo '';
                        } 
                ?>
        </textarea>
        <div name="form_all" id="form_all_<?php echo $sno; ?>" style="display:none;">
                <div name="container" id="simple_form_div_<?php echo $sno; ?>" >                                            
                        <div class="zcoutercntr">
                                <div class="zcotrcntr">
                                        <div>
                                            <a target="_blank" href="https://www.zoho.com/campaigns/help/integrations/zoho-campaigns-plugin-for-wordpress.html#customize_tools">
                                                <img src="<?php echo plugins_url('assets/images/zchelpicon.png', dirname(__FILE__)); ?>" width="23" height="23" alt="Help" style="float:right; cursor:help;" title="Help"/>
                                            </a>
                                        </div>
                                        <div style="font-size:20px;" id="lf_form_div_<?php echo $sno; ?>" onmouseover="zc_onFormDiv(this);" onmouseout="zc_outFormDiv(this);" class="zcml10">
                                            <span id="lf_form_name_<?php echo $sno; ?>">
                                            <?php 
                                                $total_form = get_option("zc4wp_a_" . $sno);
                                                if ( $total_form != '' && trim($total_form['simple_form']) != '') { 
                                                    echo $total_form['lf_form_name'];
                                                } else { 
                                                    echo "Untitled_{$sno}";
                                            } ?>
                                            </span>
                                            <input type="text" id="lf_form_edit_<?php echo $sno; ?>" name="zc4wp_a_<?php echo $sno; ?>[lf_form_name]" onkeypress="if (event.keyCode == 13) {zc_saved_editFormName(this);return false;}" onkeydown="if (event.keyCode == 27){zc_canceled_editFormName(this);}" style="display:none;" value="<?php $total_form = get_option("zc4wp_a_" . $sno);if ( $total_form != '' && trim($total_form['simple_form']) != '') { echo $total_form['lf_form_name'];} else { echo "Untitled_{$sno}";} ?>">
                                            <a id="lf_form_save_<?php echo $sno; ?>" onclick="zc_saved_editFormName(this)" class="zcml5 zcsfe">Save</a>
                                            <a id="lf_form_cancel_<?php echo $sno; ?>" onclick="zc_canceled_editFormName(this)" class="zcml5 zcsfe">Cancel</a>
                                            <span id="lf_edit_delete_panel_<?php echo $sno; ?>" style="display: none;" onclick="zc_editFormName(this)">
                                                <img title="Edit" src="<?php echo plugins_url('assets/images/edit_icon.png', dirname(__FILE__)); ?>" class="zcfldredit" style="opacity:.5;vertical-align:middle;marign-left:3px;">
                                            </span>
                                        </div>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="zcmt15">
                                                <tr>
                                                        <td align="left" valign="top" id="customize_td_<?php echo $sno; ?>" style="display:none;border-right: dotted 1px #f1f1f1;">
                                                                <div class="zcsfrmcstmzpan" id="designer_div_<?php echo $sno; ?>" style="width:95%">
                                                                        <div class="zcsfrmtitle">
                                                                                Customization Tools
                                                                        </div>
                                                                        <div class="zcpantitle" onclick="zc_customLabelToggle('<?php echo $sno; ?>');" ><img id="label_field_div_image_<?php echo $sno; ?>" src="<?php echo plugins_url('assets/images/zcdwnarw.png', dirname(__FILE__)); ?>" width="11" alt=""/>
                                                                                <label>
                                                                                        Edit fields 
                                                                                </label>
                                                                        </div>
                                                                        <div id="label_field_div_<?php echo $sno; ?>" style="border:solid 1px #e9e9e9; padding:20px; background-color:#f5f5f5;">
                                                                                <div class="zcctmzfldpan" style="display:table;">
                                                                                        <div class="zcjustheading">
                                                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                        <tr>
                                                                                                                <td width="165" style="padding-left:5px;">
                                                                                                                        Field Name
                                                                                                                </td>
                                                                                                                <td style="padding-left:59px;">
                                                                                                                        Field Title
                                                                                                                </td>
                                                                                                        </tr>
                                                                                                </table>
                                                                                        </div>
                                                                                        <div class="zcp10">
                                                                                                <ul id="adder_ul_container_<?php echo $sno; ?>">
                                                                                                        <li id="adder_li_email_<?php echo $sno; ?>" class="zcdrgnrml" >
                                                                                                                <div id="adder_email_div_<?php echo $sno; ?>">
                                                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                                                <tr>
                                                                                                                                        <td width="20" align="left" valign="middle">
                                                                                                                                                <input type="checkbox" id="adder_email_checkbox_<?php echo $sno; ?>" checked disabled />
                                                                                                                                        </td>
                                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                                Email Address:
                                                                                                                                        </td>
                                                                                                                                        <td align="left" valign="middle">
                                                                                                                                                <input type="text" onkeyup="zc_formLabelChanger(this);" onblur="zc_emptyCheck(this);" id="adder_email_textfield_<?php echo $sno; ?>" value="Email Address:" />
                                                                                                                                        </td>
                                                                                                                                        <td align="right" width="25">
                                                                                                                                                <img src="<?php echo plugins_url('assets/images/zcdragicon.png',dirname(__FILE__)); ?>" >
                                                                                                                                        </td>
                                                                                                                                </tr>
                                                                                                                        </table>
                                                                                                                </div>
                                                                                                        </li>

                                                                                                        <li id="adder_li_fname_<?php echo $sno; ?>" class="zcdrgnrml" >
                                                                                                                <div id="adder_fname_div_<?php echo $sno; ?>">
                                                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                                                <tr>
                                                                                                                                        <td width="20" align="left" valign="middle">
                                                                                                                                                <input type="checkbox" id="adder_fname_checkbox_<?php echo $sno; ?>" onclick="zc_elementManager(this);" />
                                                                                                                                        </td>
                                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                                First Name:
                                                                                                                                        </td>
                                                                                                                                        <td align="left" valign="middle">
                                                                                                                                                <input type="text" onkeyup="zc_formLabelChanger(this);" onblur="zc_emptyCheck(this);" value="First Name:" id="adder_fname_textfield_<?php echo $sno; ?>" />
                                                                                                                                        </td>
                                                                                                                                        <td align="right" width="25">
                                                                                                                                                <img src="<?php echo plugins_url('assets/images/zcdragicon.png',dirname(__FILE__)); ?>" >
                                                                                                                                        </td>
                                                                                                                                </tr>
                                                                                                                        </table>
                                                                                                                </div>
                                                                                                        </li>

                                                                                                        <li id="adder_li_lname_<?php echo $sno; ?>" class="zcdrgnrml" >
                                                                                                                <div id="adder_lname_div_<?php echo $sno; ?>">
                                                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                                                <tr>
                                                                                                                                        <td width="20" align="left" valign="middle">
                                                                                                                                                <input type="checkbox" id="adder_lname_checkbox_<?php echo $sno; ?>" onclick="zc_elementManager(this);" />
                                                                                                                                        </td>
                                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                                Last Name:
                                                                                                                                        </td>
                                                                                                                                        <td align="left" valign="middle">
                                                                                                                                                <input type="text" value="Last Name:" onblur="zc_emptyCheck(this);" onkeyup="zc_formLabelChanger(this);" id="adder_lname_textfield_<?php echo $sno; ?>" />
                                                                                                                                        </td>
                                                                                                                                        <td align="right" width="25">
                                                                                                                                                <img src="<?php echo plugins_url('assets/images/zcdragicon.png',dirname(__FILE__)); ?>" >
                                                                                                                                        </td>
                                                                                                                                </tr>
                                                                                                                        </table>
                                                                                                                </div>
                                                                                                        </li>

                                                                                                        <li id="adder_li_phone_<?php echo $sno; ?>" class="zcdrgnrml" >
                                                                                                                <div id="adder_phone_div_<?php echo $sno; ?>">
                                                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                                                <tr>
                                                                                                                                        <td width="20" align="left" valign="middle">
                                                                                                                                                <input type="checkbox" id="adder_phone_checkbox_<?php echo $sno; ?>" onclick="zc_elementManager(this);" />
                                                                                                                                        </td>
                                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                                Phone:
                                                                                                                                        </td>
                                                                                                                                        <td align="left" valign="middle">
                                                                                                                                                <input type="text" value="Phone:" onblur="zc_emptyCheck(this);" onkeyup="zc_formLabelChanger(this);" id="adder_phone_textfield_<?php echo $sno; ?>" />
                                                                                                                                        </td>
                                                                                                                                        <td align="right" width="25">
                                                                                                                                                <img src="<?php echo plugins_url('assets/images/zcdragicon.png',dirname(__FILE__)); ?>" >
                                                                                                                                        </td>
                                                                                                                                </tr>
                                                                                                                        </table>
                                                                                                                </div>
                                                                                                        </li>
                                                                                                </ul>
                                                                                                <span class="zcbdrbtm zcmt15" style="display:block;"></span>
                                                                                                <div id="adder_title_div_<?php echo $sno; ?>" class="zcmt15">
                                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                                <tr>
                                                                                                                        <td width="20" align="left" valign="middle"></td>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Title Text:
                                                                                                                        </td>
                                                                                                                        <td align="left" valign="middle">
                                                                                                                                <input type="text" value="Subscribe to our Newsletter" onkeyup="zc_formLabelChanger(this);" onblur="zc_emptyCheck(this);" id="adder_title_textfield_<?php echo $sno; ?>" />
                                                                                                                        </td>
                                                                                                                        <td width="25"></td>
                                                                                                                </tr>
                                                                                                        </table>
                                                                                                </div>
                                                                                                <div>
                                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                                                <tr>
                                                                                                                        <td width="20" align="left" valign="middle"></td>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Button Text:
                                                                                                                        </td>
                                                                                                                        <td align="left" valign="middle">
                                                                                                                                <input type="text" style="height:auto;" onkeyup="zc_formLabelChanger(this);" onblur="zc_emptyCheck(this);" id="adder_buton_textfield_<?php echo $sno; ?>" value="Subscribe Now" />
                                                                                                                        </td>
                                                                                                                        <td width="25"></td>
                                                                                                                </tr>
                                                                                                        </table>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                        <hr id="hr_custom_design_<?php echo $sno; ?>" >
                                                                        <div class="zcpantitle" onclick="zc_designDivToggle('<?php echo $sno; ?>');" ><img id="customizer__div_image_<?php echo $sno; ?>" src="<?php echo plugins_url('assets/images/zcrgtarw.png', dirname(__FILE__)); ?>" width="11" height="11" alt=""/>
                                                                                <label>
                                                                                        Edit Layout
                                                                                </label>
                                                                        </div>
                                                                        <div id="customizer__div_<?php echo $sno; ?>" style="display:none;">
                                                                                <div style="height:350px;border: 1px solid rgb(233, 233, 233); padding: 20px; background-color: rgb(245, 245, 245); display: block;">
                                                                                        <div style="height:310px;" class ="zcdznpan zcp10" >
                                                                                                <div style="width:400px;" align="center" >
                                                                                                        <select id="style_select_<?php echo $sno; ?>" onchange="zc_styleSelect(this);" class="zclstfld zcml10 zclstoptpl10">
                                                                                                                <option value="form" selected>Form</option>
                                                                                                                <option value="title">Title</option>
                                                                                                                <option value="label">Label</option>
                                                                                                                <option value="textfield">TextField</option>
                                                                                                                <option value="button">Button</option>
                                                                                                        </select>
                                                                                                </div>
                                                                                                <div id="title_div_<?php echo $sno; ?>" style="display:none;">
                                                                                                        <table cellspacing="10px;">
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <select id="title_font_<?php echo $sno; ?>" class="zclstoptpl10" onchange="zc_fontSelect(this);">
                                                                                                                                        <option value="Arial">Arial</option>
                                                                                                                                        <option value="Georgia">Georgia</option>
                                                                                                                                        <option value="Times New Roman">Times New Roman</option>
                                                                                                                                        <option value="Verdana">Verdana</option>
                                                                                                                                        <option value="Optima">Optima</option>
                                                                                                                                        <option value="Others">Others</option>
                                                                                                                                </select>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr style="display:none;" id="title_input_tr_<?php echo $sno; ?>">
                                                                                                                        <td width="200"></td>
                                                                                                                        <td>
                                                                                                                                <input align="right" type="text" id="title_font_value_<?php echo $sno; ?>" placeholder="Font Face for Title" onkeyup="zc_changeFontFamily(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Size:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <input type="text" id="title_size_value_<?php echo $sno; ?>" placeholder="Size of Title" onkeyup="zc_sizeChange(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="title_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div id="title_color_picker_div_<?php echo $sno; ?>" style="position:relative;display:none;">
                                                                                                                                        <div  class="zcfloat" >
                                                                                                                                                <input style="display:none;" type="text" id="title_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != '' && isset($dbshortcodeval['title_bg_val']) && trim($dbshortcodeval['title_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['title_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                }
                                                                                                                                                        ?>"
                                                                                                                                                />
                                                                                                                                                <div id="title_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                        </table>
                                                                                                </div>
                                                                                                <div id="label_div_<?php echo $sno; ?>" style="display:none;">
                                                                                                        <table cellspacing="10px;">    
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <select id="label_font_<?php echo $sno; ?>" class="zclstoptpl10" onchange="zc_fontSelect(this);">
                                                                                                                                        <option value="Arial">Arial</option>
                                                                                                                                        <option value="Georgia">Georgia</option>
                                                                                                                                        <option value="Times New Roman">Times New Roman</option>
                                                                                                                                        <option value="Verdana">Verdana</option>
                                                                                                                                        <option value="Optima">Optima</option>
                                                                                                                                        <option value="Others">Others</option>
                                                                                                                                </select>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr style="display:none;" id="label_input_tr_<?php echo $sno; ?>">
                                                                                                                        <td width="200"></td>
                                                                                                                        <td>
                                                                                                                                <input align="right" type="text" id="label_font_value_<?php echo $sno; ?>" placeholder="Font Face for label" onkeyup="zc_changeFontFamily(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Size:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <input type="text" id="label_size_value_<?php echo $sno; ?>" placeholder="Size of label" onkeyup="zc_sizeChange(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="label_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div id="label_color_picker_div_<?php echo $sno; ?>" style="position:relative;display:none;" >
                                                                                                                                        <div class="zcfloat" >
                                                                                                                                                <input style="display:none;" type="text" id="label_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != '' && isset($dbshortcodeval['label_bg_val']) && trim($dbshortcodeval['label_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['label_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                }
                                                                                                                                                        ?>"
                                                                                                                                                />
                                                                                                                                                <div id="label_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                        </table>
                                                                                                </div>
                                                                                                <div id="textfield_div_<?php echo $sno; ?>" style="display:none;">
                                                                                                        <table cellspacing="10px;">
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <select id="field_font_<?php echo $sno; ?>" class="zclstoptpl10" onchange="zc_fontSelect(this);">
                                                                                                                                        <option value="Arial">Arial</option>
                                                                                                                                        <option value="Georgia">Georgia</option>
                                                                                                                                        <option value="Times New Roman">Times New Roman</option>
                                                                                                                                        <option value="Verdana">Verdana</option>
                                                                                                                                        <option value="Optima">Optima</option>
                                                                                                                                        <option value="Others">Others</option>
                                                                                                                                </select>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr style="display:none;" id="field_input_tr_<?php echo $sno; ?>">
                                                                                                                        <td width="200"></td>
                                                                                                                        <td>
                                                                                                                                <input align="right" type="text" id="field_font_value_<?php echo $sno; ?>" placeholder="Font Face for field" onkeyup="zc_changeFontFamily(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Size:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <input type="text" id="field_size_value_<?php echo $sno; ?>" placeholder="Size of field" onkeyup="zc_sizeChange(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="field_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div style="position:relative;display:none;" id="field_color_picker_div_<?php echo $sno; ?>">
                                                                                                                                        <div class="zcfloat">
                                                                                                                                        <input style="display:none;" type="text" id="field_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                <?php
                                                                                                                                                        if ($dbshortcodeval != '' && isset($dbshortcodeval['field_bg_val']) && trim($dbshortcodeval['field_bg_val']) != '') {
                                                                                                                                                                echo $dbshortcodeval['field_bg_val'];
                                                                                                                                                        }
                                                                                                                                                        else {
                                                                                                                                                                echo "rgb(0,0,0)";
                                                                                                                                                        } 
                                                                                                                                                ?>" 
                                                                                                                                        />
                                                                                                                                        <div id="field_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                <?php
                                                                                                                                                        zc_colorboxcreater();
                                                                                                                                                ?>
                                                                                                                                        </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Background Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="field_bgcolbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div style="position:relative;display:none;" id="field_bgcolor_picker_div_<?php echo $sno; ?>">
                                                                                                                                        <div  class="zcfloat">
                                                                                                                                                <input style="display:none;" type="text" id="field_bgbgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != '' && isset($dbshortcodeval['field_bg_val'])  && trim($dbshortcodeval['field_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['field_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                } 
                                                                                                                                                        ?>" 
                                                                                                                                                />
                                                                                                                                                <div id="field_bgcolorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                        </table>
                                                                                                </div>
                                                                                                <div id="button_div_<?php echo $sno; ?>" style="display:none;">
                                                                                                        <table cellspacing="10px;">
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <select id="buton_font_<?php echo $sno; ?>" class="zclstoptpl10" onchange="zc_fontSelect(this);">
                                                                                                                                        <option value="Arial">Arial</option>
                                                                                                                                        <option value="Georgia">Georgia</option>
                                                                                                                                        <option value="Times New Roman">Times New Roman</option>
                                                                                                                                        <option value="Verdana">Verdana</option>
                                                                                                                                        <option value="Optima">Optima</option>
                                                                                                                                        <option value="Others">Others</option>
                                                                                                                                </select>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr style="display:none;" id="buton_input_tr_<?php echo $sno; ?>">
                                                                                                                        <td width="200"></td>
                                                                                                                        <td>
                                                                                                                                <input align="right" type="text" id="buton_font_value_<?php echo $sno; ?>" placeholder="Font Face for buton" onkeyup="zc_changeFontFamily(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Size:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <input type="text" id="buton_size_value_<?php echo $sno; ?>" placeholder="Size of buton" onkeyup="zc_sizeChange(this);" />
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Font Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="buton_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div id="buton_color_picker_div_<?php echo $sno; ?>" style="position:relative;display:none;">
                                                                                                                                        <div class="zcfloat"  >
                                                                                                                                                <input style="display:none;" type="text" id="buton_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != ''&& isset($dbshortcodeval['buton_bg_val']) && trim($dbshortcodeval['buton_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['buton_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                } 
                                                                                                                                                        ?>" 
                                                                                                                                                />
                                                                                                                                                <div id="buton_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="middle">
                                                                                                                                Background Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="bgcol_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div id="bgcol_color_picker_div_<?php echo $sno; ?>" style="position:relative;display:none;">
                                                                                                                                        <div class="zcfloat" >
                                                                                                                                                <input style="display:none;" type="text" id="bgcol_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != '' && isset($dbshortcodeval['bgcol_bg_val']) && trim($dbshortcodeval['bgcol_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['bgcol_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                } 
                                                                                                                                                        ?>" 
                                                                                                                                                />
                                                                                                                                                <div id="bgcol_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                        </table>
                                                                                                </div>
                                                                                                <div id="form_div_<?php echo $sno; ?>">
                                                                                                        <table cellspacing="10px;">
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="top">
                                                                                                                                Header Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                        <div id="hedbg_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div style="position:relative">
                                                                                                                                        <div id="hedbg_color_picker_div_<?php echo $sno; ?>" class="zcfloat" style="display:none;" >
                                                                                                                                                <input style="display:none;" type="text" id="hedbg_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != '' && isset($dbshortcodeval['forbg_bg_val']) && trim($dbshortcodeval['forbg_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['forbg_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                } 
                                                                                                                                                        ?>" 
                                                                                                                                                />
                                                                                                                                                <div id="hedbg_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="top">
                                                                                                                                Form Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="forbg_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div style="position:relative">
                                                                                                                                        <div id="forbg_color_picker_div_<?php echo $sno; ?>" class="zcfloat" style="display:none;" >
                                                                                                                                                <input style="display:none;" type="text" id="forbg_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != '' &&isset($dbshortcodeval['forbg_bg_val'])&& trim($dbshortcodeval['forbg_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['forbg_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                } 
                                                                                                                                                        ?>" 
                                                                                                                                                />
                                                                                                                                                <div id="forbg_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="240" align="left" valign="top">
                                                                                                                                Form Border Color:
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                                <div id="borbg_colorbox_<?php echo $sno; ?>" onclick="zc_setColor(this);" style="background-color:rgb(0,0,0);display:inline-block;width:35px;height:20px;border:solid 1px #555;" ></div>
                                                                                                                                <div style="position:relative">
                                                                                                                                        <div id="borbg_color_picker_div_<?php echo $sno; ?>" class="zcfloat" style="display:none;" >
                                                                                                                                                <input style="display:none;" type="text" id="borbg_bgval_<?php echo $sno; ?>" value="
                                                                                                                                                        <?php
                                                                                                                                                                if ($dbshortcodeval != ''&&isset($dbshortcodeval['borbg_bg_val']) && trim($dbshortcodeval['borbg_bg_val']) != '') {
                                                                                                                                                                        echo $dbshortcodeval['borbg_bg_val'];
                                                                                                                                                                }
                                                                                                                                                                else {
                                                                                                                                                                        echo "rgb(0,0,0)";
                                                                                                                                                                } 
                                                                                                                                                        ?>" 
                                                                                                                                                />
                                                                                                                                                <div id="borbg_colorpicker_<?php echo $sno; ?>" >
                                                                                                                                                        <?php
                                                                                                                                                                zc_colorboxcreater();
                                                                                                                                                        ?>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                        <td width="200" align="left" valign="top">
                                                                                                                                Form Width:
                                                                                                                        </td>
                                                                                                                        <td width="500px;">
                                                                                                                                <div class="zcsilderMain zcsliderpd0" style="width:250px;">
                                                                                                                                        <div style="position:absolute;top:0px;" class="zcsliderpd0">
                                                                                                                                                <div id="sliderToolTip_<?php echo $sno; ?>" class="zcsliderToolTip zcsliderpd0" style="display:none;width:30px;height:23px;background-color:#555; font-size:11px; color:#fff; padding:2px 5px; border-radius:4px; font-family:Arial;"></div>
                                                                                                                                        </div>
                                                                                                                                        <div id="slider_<?php echo $sno; ?>" class="zcsliderpd0" style="width:200px;"></div>
                                                                                                                                        <script type="text/javascript">jQuery(document).ready(function() { zc_uiSliderChangeSetter('<?php echo $sno; ?>') });</script>
                                                                                                                                </div>
                                                                                                                        </td>
                                                                                                                </tr>
                                                                                                        </table>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </td>
                                                        <td width="40%" align="left" valign="top">
                                                                <div class="zcsfrmcntrcenter" id="form_holder_<?php echo $sno; ?>">
                                                                        <div class="zcsfrmtitle" id="preview_text_div_<?php echo $sno; ?>" style="display:none;">
                                                                                Preview
                                                                        </div>
                                                                        <div style=" background-color:#fff;width:350px;" id="total_form_<?php echo $sno; ?>">
                                                                                <?php
                                                                                        if ($dbshortcodeval != '' && trim($dbshortcodeval['simple_form']) != '') {
                                                                                                echo $dbshortcodeval['simple_form'];
                                                                                        }
                                                                                        else { 
                                                                                ?>
                                                                                        <div style="background-color:#fff;border:solid 1px rgb(207, 207, 207);width:350px;" id="total_form_child_<?php echo $sno; ?>">
                                                                                                <div style="background-color:rgb(228, 228, 228);font-family:Arial;font-size:14px;color:#000000;padding:15px;border-bottom:solid 1px rgb(207, 207, 207);" id="form_title_<?php echo $sno; ?>" align="center">
                                                                                                        Subscribe to our Newsletter
                                                                                                </div>
                                                                                                <div id="form_body_container_<?php echo $sno; ?>" style="background-color:#fff;">
                                                                                                        <ul style="list-style-type:none;padding:10px 15px; margin-top:10px;" id="form_body_<?php echo $sno; ?>">
                                                                                                                <li style="padding-top:13px;" id="form_email_li_<?php echo $sno; ?>">
                                                                                                                        <div style=" width:110px; float:left; font-size:14px; font-family:Arial;margin-top:5px;color:#000000;padding-right:10px;" align="right" id="form_email_label_<?php echo $sno; ?>">
                                                                                                                                Email Address:
                                                                                                                        </div>
                                                                                                                        <div style="  width:60%; float:left;min-width:150px;">
                                                                                                                                <input name="CONTACT_EMAIL" style="font-size:12px; border:solid 1px #dcdcdc; width:100%; height:30px; padding:5px;color:#000000;background-color:#ffffff;font-family:Arial;" valType="placeHolder" autocomplete="off" id="form_email_input_<?php echo $sno; ?>" type="text" />
                                                                                                                        </div>
                                                                                                                        <div style="clear:both;"></div>
                                                                                                                </li>
                                                                                                        </ul>
                                                                                                        <div style=" padding:10px 15px; text-align:center; margin-bottom:10px;">
                                                                                                                <input type="button" class="button button-primary" style="box-shadow: none;background-color:#e07070;font-family:Arial; font-size:14px; color:#fff; border:solid 1px #e07070; border-radius:5px;text-shadow: none;" value="Subscribe Now" id="form_button_<?php echo $sno; ?>"/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                <?php
                                                                                        } 
                                                                                ?>
                                                                        </div>
                                                                        <br/>
                                                                        <div class="lnkpan" id="design_shower_<?php echo $sno; ?>" align="right"> 
                                                                                <a class="zclink" href="javascript:zc_designshower('<?php echo $sno; ?>','0');">
                                                                                        Design and Customize 
                                                                                </a> 
                                                                        </div>
                                                                </div>
                                                        </td>
                                                </tr>
                                        </table>
                                        <div class="zcclr"></div>
                                        <br/>
                                        <div class="zcclr"></div>
                                        <div class="zcclr"></div>
                                </div>
                        </div>
                        <div class="zcoutercntrrespan" style="display:none;" id="total_response_<?php echo $sno; ?>">
                                <div class="zcotrcntr zcrespontitle">
                                        <span class="zcml10">
                                                <img src="<?php echo plugins_url('assets/images/zcrgtarw.png', dirname(__FILE__)); ?>" width="11" height="11" alt="" onclick="zc_responseContainerDisplay('<?php echo $sno; ?>')" id="response_div_image_<?php echo $sno; ?>"/>
                                                <label onclick="zc_responseContainerDisplay('<?php echo $sno; ?>')" >
                                                        Response Settings
                                                </label>
                                        </span>
                                        <span style="float:right">
                                                <a class="zclink" href="javascript:zc_responsePreviwerShowerHider('<?php echo $sno; ?>');" />
                                                        Preview
                                                </a>
                                        </span>
                                        <div align="left" style="display:none;" id="response_showe_container_<?php echo $sno; ?>">
                                                <div class="zcdtdbdr"></div>
                                                <div class="zcctmzfldpan1" style="width:1000px;" align="left">
                                                                <!-- 
                                                                <div class="zcp10 zcmt20" style="width:80%">
                                                                    <select class="zclstfld zclstoptpl10" name="zc4wp_a_<?php echo $sno; ?>[response_area_shower]" id="response_area_shower_<?php echo $sno; ?>"  onchange="zc_responseShowerArea(this)">
                                                                        <option value="simple_form_response">Simple Form Response</option>
                                                                        <option value="zoho_campaigns_response">Zoho Campaigns Response</option>
                                                                    </select>
                                                                </div>
                                                                -->
                                                        <div class="zcclr"></div>
                                                        <div id="response_editor_<?php echo $sno; ?>" class="zcp10 zcctmzrespan zcmt20" align="left"> 
                                                                <table>
                                                                        <tr>
                                                                            <td colspan="2">
                                                                                When Subscription is successful: 
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                                <td class="zcrs" valign="middle">
                                                                                        Message Header:
                                                                                </td>
                                                                                <td style="padding:7px;">
                                                                                        <input onkeyup="zc_checkContenet(this,0,'<?php echo $sno; ?>');" type="text" id="success_header_<?php echo $sno; ?>" name="zc4wp_a_<?php echo $sno; ?>[success_header]" style="width:400px;" value="
                                                                                                <?php
                                                                                                        if ($dbshortcodeval != '' && trim($dbshortcodeval['success_header']) != '') {
                                                                                                                echo $dbshortcodeval['success_header'];
                                                                                                        }
                                                                                                        else {
                                                                                                                echo 'Please confirm your Subscription.';
                                                                                                        } 
                                                                                                ?>"
                                                                                        >
                                                                                </td>
                                                                        </tr>
                                                                        <tr>
                                                                                <td class="zcrs" valign="middle">
                                                                                        Message Content:
                                                                                </td>
                                                                                <td style="padding:7px;">
                                                                                        <textarea onkeyup="zc_checkContenet(this,0,'<?php echo $sno; ?>');" id="success_body_<?php echo $sno; ?>" name="zc4wp_a_<?php echo $sno; ?>[success_body]" style="width:400px;">
                                                                                                <?php
                                                                                                        if ($dbshortcodeval != '' && trim($dbshortcodeval['success_body']) != '') {
                                                                                                           echo $dbshortcodeval['success_body'];
                                                                                                        }
                                                                                                        else {
                                                                                                                echo 'Thank you for joining. &#10;&#10;We have sent a confirmation link to your registered email. Please click the link to activate your subscription. &#10;&#10;Note: If you don\'t receive our email, check your Spam or Junk folder. ';
                                                                                                        } 
                                                                                                ?>
                                                                                        </textarea>
                                                                                </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2" height="50">
                                                                                When Subscription is incorrect/unsuccessful: 
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                                <td class="zcrs" valign="middle">
                                                                                        Message Header:
                                                                                </td>
                                                                                <td style="padding:7px;">
                                                                                        <input onkeyup="zc_checkContenet(this,0,'<?php echo $sno; ?>');" type="text" id="error_header_<?php echo $sno; ?>" name="zc4wp_a_<?php echo $sno; ?>[error_header]" style="width:400px;" value="
                                                                                                <?php
                                                                                                        if ($dbshortcodeval != '' && trim($dbshortcodeval['error_header']) != '') {
                                                                                                                echo $dbshortcodeval['error_header'];
                                                                                                        }
                                                                                                        else {
                                                                                                                echo 'Registration Failed!';
                                                                                                        } 
                                                                                                ?>" 
                                                                                        >
                                                                                </td>
                                                                        </tr>
                                                                        <tr>
                                                                                <td class="zcrs" valign="middle">
                                                                                        Message Content:
                                                                                </td>
                                                                                <td style="padding:7px;">
                                                                                        <textarea onkeyup="zc_checkContenet(this,0,'<?php echo $sno; ?>');" id="genaral_error_body_<?php echo $sno; ?>" name="zc4wp_a_<?php echo $sno; ?>[error_body]" style="width:400px;">
                                                                                                <?php
                                                                                                        if ($dbshortcodeval != '' && trim($dbshortcodeval['error_body']) != '') {
                                                                                                                echo $dbshortcodeval['error_body'];
                                                                                                        }
                                                                                                        else {
                                                                                                                echo 'An error occurred while trying to subscribe.&#10;Please try again!';
                                                                                                        } 
                                                                                                ?>
                                                                                        </textarea>
                                                                                </td>
                                                                        </tr>
                                                                        <tr>
                                                                                <td style="width:300px;" class="zcrs" valign="middle">
                                                                                        When Email ID is already subscribed:
                                                                                </td>
                                                                                <td style="padding:7px;">
                                                                                        <textarea onkeyup="zc_checkContenet(this,0,'<?php echo $sno; ?>');" id="exists_email_body_<?php echo $sno; ?>" name="zc4wp_a_<?php echo $sno; ?>[exists_email_body]" style="width:400px;">
                                                                                                <?php
                                                                                                        if ($dbshortcodeval != '' && trim($dbshortcodeval['exists_email_body']) != '') {
                                                                                                                echo $dbshortcodeval['exists_email_body'];
                                                                                                        }
                                                                                                        else {
                                                                                                                echo 'This email address already exists in our records! Please use another email address for registration.';
                                                                                                        }
                                                                                                ?>
                                                                                        </textarea>
                                                                                </td>
                                                                        </tr>
                                                                </table>
                                                        </div>
                                                        <!-- 
                                                        <div id="zoho_form_response_indicator_<?php echo $sno; ?>" style="display:none;text-transform:initial;padding:10px;padding-bottom:25px;">
                                                            Zoho Campaigns response form will be available upon subscription.
                                                        </div>
                                                        -->
                                                </div>
                                        </div>
                                        <div id="preview_container_<?php echo $sno; ?>" class="zcpopup" style="text-transformation:initial;display:none;width:700px; left:23%; top:100px;z-index: 9999;text-transform:initial;">
                                                <div class="zcpopuptitle" style="margin:10px;">
                                                        <span style="float:right;">
                                                                <img style="cursor:pointer;" src="<?php echo plugins_url('assets/images/spacer.gif', dirname(__FILE__)); ?>" width="10" height="10" onclick="zc_responsePreviwerShowerHider('<?php echo $sno; ?>');" />
                                                        </span>
                                                        Subscription Response
                                                </div>
                                                <div class="zcp20">
                                                        <div>
                                                                <select  class="zclstfld zclstoptpl10" id="radio_header_<?php echo $sno; ?>" onchange="zc_responseHeaderShower(this);">
                                                                        <option value="registration_success">Registration Success</option>
                                                                        <option value="registration_failed">Registration Failed</option>
                                                                        <option value="registration_duplicate">Email-Id already exists</option>
                                                                </select>
                                                        </div>
                                                        <div class="zcmt20 zctitle">
                                                                Preview 
                                                        </div>
                                                        <div class="zcouterpreviewpan">
                                                                <h1 id="headval_<?php echo $sno; ?>" >
                                                                        Loading...
                                                                </h1>
                                                                <div class="zcp20">
                                                                        <div id="bodyval_<?php echo $sno; ?>" class=" zctxt" >
                                                                                <img src="<?php echo plugins_url('assets/images/uploadImg.gif', dirname(__FILE__)); ?>" />
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div class="zcbtncntr" style="display:none;" id="save_cancel_button_div_<?php echo $sno; ?>"> 
                                <button onclick="return zc_editorContentChecker(this,'1','<?php echo $apiKeyVal; ?>','<?php echo $id; ?>');" type="button" class="zcnewbuttonWP" id="simple_button_<?php echo $sno; ?>" style="box-shadow: none;margin-right:15px;">
                                    <span>Save Changes</span>
                                    <img width="20" height="20" src="<?php echo plugins_url('assets/images/uploadImg.gif', dirname(__FILE__)); ?>" style="display:none;margin-left:5px;vertical-align:middle;">
                                </button>
                                <input type="submit" style="display:none" id="submit_<?php echo $sno; ?>" />
                                <button id="cancel_simple_form_changes_<?php echo $sno; ?>" onclick="zc_discardChanges('<?php echo $sno; ?>','create');return false;" style="box-shadow: none;" class="zcnewcnclbutton">Cancel</button>
                        </div>
                                            
                        <div class="zcclr"></div>
                </div>
                <script type="text/javascript">
					jQuery(document).ready(function() {
						zc_makeSortable('<?php echo $sno; ?>');
					});
                </script>
                <div id="zcsignupformmsg_<?php echo $sno; ?>" style="display:none;" class="zcwelcomepansf"><div class="zctcenter zcmt20"><img src="<?php echo plugins_url('assets/images/zcsignupicon.png', dirname(__FILE__)); ?>" width="100" alt=""></div><br><h1 id="signUpFormErrorHeader">No Signup Forms Available</h1><div class=" zcmt15 zctxt zctcenter zcf14">Head over to Zoho Campaigns and create a new sign-up form. To learn more <a href="https://campaigns.zoho.com/listcreationhelpcontent.html">click here</a>.</div></div>
                <div name="zohoform" style="display:none;padding:30px;" id="zoho_form_div_<?php echo $sno; ?>">
                        <div class="zcotrcntr">
                            <div id="zcformcontainer_<?php echo $sno; ?>" class="zcmt15">
                                <div style="font-size: 20px;" id="zc_form_div_<?php echo $sno; ?>" onmouseover="zc_onFormDiv(this);" onmouseout="zc_outFormDiv(this);">
                                    <span id="zc_form_name_<?php echo $sno; ?>">
                                        <?php 
                                            $total_form = get_option("zc4wp_a_" . $sno);
                                            if ( $total_form != '' && trim( $total_form['zoho_form']) != '') {
                                                echo $total_form['zc_form_name'];
                                            } else { ?>
                                                Sign-up Form Name
                                        <?php } ?>
                                    </span>
                                    <input type="text" name="zc4wp_a_<?php echo $sno; ?>[zc_form_name]" onkeypress="if (event.keyCode == 13) {zc_saved_editFormName(this);return false;}" onkeydown="if (event.keyCode == 27){zc_canceled_editFormName(this);}" id="zc_form_edit_<?php echo $sno; ?>" style="display:none;" value="<?php $total_form = get_option("zc4wp_a_" . $sno);if ( $total_form != '' && trim( $total_form['zoho_form']) != '') {echo $total_form['zc_form_name'];} else { echo"Sign-up Form Name";} ?>">
                                    <a id="zc_form_save_<?php echo $sno; ?>" onclick="zc_saved_editFormName(this)" class="zcml5 zcsfe">save</a>
                                    <a id="zc_form_cancel_<?php echo $sno; ?>" onclick="zc_canceled_editFormName(this)" class="zcml5 zcsfe">cancel</a>
                                    <span id="zc_edit_delete_panel_<?php echo $sno; ?>" style="display: none;" onclick="zc_editFormName(this)">
                                        <img title="Edit" src="<?php echo plugins_url('assets/images/edit_icon.png', dirname(__FILE__)); ?>" class="zcfldredit" style="opacity:.5;margin-left:3px;vertical-align:middle;">
                                    </span>
                                </div>
                                <div id="form_radiobuttons_<?php echo $sno; ?>">
                                    <input type="radio" id="embed__form_<?php echo $sno; ?>" onclick="zc_formRetreiver(this.id,'<?php echo $id; ?>');" name="zoho_form_selector" />
                                    <b>
                                            Smart Form
                                    </b>
                                    <input type="radio" id="custom_form_<?php echo $sno; ?>" onclick="zc_formRetreiver(this.id,'<?php echo $id; ?>');" name="zoho_form_selector" style="margin-left:25px;" />
                                    <b>
                                            Advanced Form
                                    </b>
                                    <input type="radio" id="button_form_<?php echo $sno; ?>" onclick="zc_formRetreiver(this.id,'<?php echo $id; ?>');" name="zoho_form_selector" style="margin-left:25px;" />
                                    <b>
                                            Button Form
                                    </b>
                                </div>
                                <div style="margin-top: 25px;font-size: 16px;color: #555;">You can embed the sign-up form on your blog/website or add a subscription button for visitors.</div>
                                <div id="zoho_form_viewer_<?php echo $sno; ?>" align="center">
                                        <?php
                                                if ($dbshortcodeval['zoho_form'] != '' && trim($dbshortcodeval['zoho_form']) != '') {
                                                        $zoho_form_html = str_replace("trackSignupEvent","//trackSignupEvent",$dbshortcodeval['zoho_form']);
                                                        echo $zoho_form_html;
                                                }
                                                else {
                                                        echo "none";
                                        ?> 
                                                        <!--script type="text/javascript"> 
                                                                jQuery("#embed__form_<?php echo $sno; ?>").attr("checked","checked"); 
                                                                zc_formRetreiver(document.getElementById('embed__form_<?php echo $sno; ?>'),'<?php echo $apiKeyVal; ?>','<?php echo $id; ?>'); 
                                                        </script--> 
                                        <?php
                                                } 
                                        ?>
                                </div>
                            </div>
                        </div>
                        <div id="zcform_save_button_<?php echo $sno; ?>" class="zcbtncntr">
                                <br/>
                                <button onclick="return zc_editorContentChecker(this,'2','<?php echo $apiKeyVal; ?>','<?php echo $id; ?>');" type="button" class="zcnewbuttonWP" id="zcform_button_<?php echo $sno; ?>" style="box-shadow: none;margin-right:15px;">
                                    <span>Save Changes</span>
                                    <img width="20" height="20" src="<?php echo plugins_url('assets/images/uploadImg.gif', dirname(__FILE__)); ?>" style="display:none;vertical-align:middle;">
                                </button>
                                <button onclick="zc_discardZohoFormChanges('<?php echo $sno; ?>','create');return false;" id="cancel_zoho_form_changes_<?php echo $sno; ?>" style="box-shadow: none;" class="zcnewcnclbutton">Cancel</button>
                        </div>

                </div>
        </div>
    </form>
<?php
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <script type="text/javascript">
            zc_pluginDir = "<?php echo plugins_url("",dirname(__FILE__));?>";
            var zc_all_idval = new Array();
            var zc_unsaved_idval = new Array();
            var zc_saved_idval = new Array();
            var zc_saved_page = false;
        </script>        
    </head>
    <body>
    <?php

    function zc_init() {
        $xml = $GLOBALS['xml'];
        $domain_name = $GLOBALS['domain_name'];
        $apiKeyVal = $GLOBALS['apikeyval'];
        $str = $GLOBALS['str'];
        $no_header = true;
        $saved_list_count = 0;
?>   
        <script type="text/javascript">
            zc_on_load_apikey_val = '<?php echo $apiKeyVal; ?>';
        </script>
<?php
        $ageIndicator = '';
        if(isset($GLOBALS['ageIndicator']))
        {
                $ageIndicator = $GLOBALS['ageIndicator'];
        }
        if ($xml->status == 'success' && $xml->code == 0) {

                $count = 0;
?>
                <script type="text/javascript">
                    const zc_ver = "<?php echo $xml->signupformversion ?>";
                </script>
                <div id="no_list" style="display:none;"><img src="<?php echo plugins_url('assets/images/zc_mailinglist.png', dirname(__FILE__)); ?>" alt=""></div>
                <!-- <div class="zcheading" id="click_list_content" style="display:none;">
                    <div>
                        Create Sign-up Form
                    </div>
                    <div class="zcclr"></div>
                </div> -->
                <div class="zclistheading" id="click_list_div" style="display:none;font-size: 17px;padding: 15px;">
                    Choose a mailing list
                    <span class="zcsearchcntr">
                        <div class="zcdrbdwn" id="choosen_user_type" value="1" onclick="zc_dropDownListToggle('1')" tabindex="0" onblur="zc_dropDownListToggle('2')"><span>All Mailing Lists</span><img style="float:right; margin-top:6px;" src="<?php echo plugins_url('assets/images/zcdrpdwnarw.png', dirname(__FILE__)); ?>" width="7" height="4" alt="">
                            <div class="zcdrbdwnlist" id="drop_down_list" style="display:none;">
                                <ul>
                                    <li value="1" onclick="zc_selectUserType('1')" style="display:none;"><a>All Mailing Lists</a></li>
                                    <li value="2" onclick="zc_selectUserType('2')"><a>My Mailing Lists</a></li>
                                    <li value="3" onclick="zc_selectUserType('3')"><a>Other User Mailing Lists</a></li>
                                </ul>
                            </div>
                        </div>
                        <input class="zcsearchbox" type="text" placeholder="Search your mailinglist" id="search_bar" onkeyup="if (event.keyCode == 13) { zc_searchList();}">
                        <img src="<?php echo plugins_url('assets/images/zc_searchicon.png', dirname(__FILE__)); ?>" alt="" onclick="zc_searchList()" id="search_icon" style="vertical-align:middle; position:absolute; right:10px; top:3px; cursor:pointer;"> 
                    </span>
                </div>
                <div class="zclistheading" id="create_content_div" style="display:none;padding:0px;">
                        <div class="zcheading">
                                <?php if(isset($sno)){ ?>
                                        <img src="<?php echo plugins_url('assets/images/zcbckarw.png', dirname(__FILE__)); ?>" alt="Back"  style="vertical-align:top; margin-right:10px; cursor:pointer;float:left;margin-top:2px;" title="Back" onclick="zc_back('2','<?php echo $sno; ?>');">
                                <?php }else{ ?>
                                        <img src="<?php echo plugins_url('assets/images/zcbckarw.png', dirname(__FILE__)); ?>" alt="Back"  style="vertical-align:top; margin-right:10px; cursor:pointer;float:left;margin-top:2px;" title="Back" onclick="zc_back('2','');">
                                <?php } ?>
                                <span id="create_content_div_span">Create sign-up form for </span>
                        </div>
                        <div class="zcclr"></div>
                </div>
                <div id="saved_list_content" style="padding:0px;">
                        <div class="zctright zcmt15">
                            <button class="zcnewbuttonWP" type="button"  id="new_mailing_button" onclick="zc_createNewForm();">
                                New sign-up form
                            </button>
                        </div>
                        <div class="zclistheading" style="padding-bottom:25px;">
                            My Sign-up Forms
                        </div>
                        <div class="zcclr"></div>
                </div>
                <div id="background_div" style="text-transformation:initial;display:none;background-color: rgb(0, 0, 0); opacity: 0.5; z-index: 100; position: fixed; width: 100%; height: 950px; top: 0px; left: 0px;"></div>
                <div id="form_preview_container" class="zcpopup" style="text-transformation:initial;display:none;width:600px;  left : calc(50% - 300px) ; top:100px;z-index: 9999;text-transform:initial;">
                        <div style="margin:10px;" class="zcpopuptitle">
                                <span style="float:right;">
                                <?php if(isset($sno)){ ?>
                                        <img style="cursor:pointer;" src="<?php echo plugins_url('assets/images/spacer.gif', dirname(__FILE__)); ?>" width="10" height="10" onclick="zc_hidePreviewSavedForm('<?php echo $sno; ?>');" />
                                <?php }else{ ?>
                                        <img style="cursor:pointer;" src="<?php echo plugins_url('assets/images/spacer.gif', dirname(__FILE__)); ?>" width="10" height="10" onclick="zc_hidePreviewSavedForm('');" />
                                <?php } ?>
                                </span>
                                <span id="preview_heading"></span>
                        </div>
                        <div class="zcclr"></div>
                        <div id="preview_container" align="center" class="zcoutercntr" valign="center"></div>
                </div>
                <div class="zcoutercntr" id="form_type_selector_div" style="display:none;">
                        <div class="zcmt20 zcotrcntr" >
                                <div id="sf_create_help_link">
                                    <a target="_blank" href="https://www.zoho.com/campaigns/help/integrations/zoho-campaigns-plugin-for-wordpress.html#create_sign-up_form">
                                        <img src="<?php echo plugins_url('assets/images/zchelpicon.png', dirname(__FILE__)); ?>" width="23" height="23" alt="Help" style="float:right; cursor:help;" title="Help"/>
                                    </a>
                                </div>
                                <table  border="0" cellspacing="25" cellpadding="0" width="100%">
                                        <tr>
                                                <td id="zc_form"width="48%" align="left" valign="top">
                                                        <div class="zctitle" id="zc_form_text">
                                                                Forms available in your Zoho Campaigns account
                                                        </div>
                                                        <div class="zctxt zcmt20">
                                                                <!-- Use sign-up forms which you?ve already created in your Zoho Campaigns account. -->
                                                                <div id="all_zc_signup_forms" class="zcsfrmlstcntr">
                                                                </div>
                                                        </div>
                                                        <div class="zcmt20" align="center">
                                                                <button id="zoho_form_shower" type="button" class="zcnewbuttonWP">
                                                                        Proceed
                                                                </button>
                                                        </div>
                                                </td>
                                                <td id="form_seperator" align="left" valign="top" width="50" style="border-left:1px solid rgb(241, 241, 241);">
                                                </td>
                                                <td id="local_form" width="48%" align="left" valign="top">
                                                        <div class="zctitle">
                                                                Create a form
                                                        </div>
                                                        <div class="zctxt zcmt15">
                                                            <ol>
                                                                <li>Build your sign-up form from start.</li>
                                                                <li>You can add new fields, edit labels, change background colors, font style and many more.</li>
                                                                <li>This form is exclusive to this WordPress plugin and will not be available for use in Zoho Campaigns.</li>
                                                            </ol>
                                                        </div>
                                                        <div class="zcmt20">
                                                                <button id="simple_form_shower" type="button" class="zcnewbuttonWP">
                                                                        Create
                                                                </button>
                                                        </div>
                                                </td>
                                        </tr>
                                </table>
                        </div>
                        <div id="hidden_div" style="display:none;"></div>
                </div>
                <?php
                $list_value_storage = array();
                $temp_var = 0;
                if($str != "1:") {
                    foreach($xml->list_of_details->list as $list) {
                                $count++;
                                $name = 'name';
                                $id = 'id';
                                $sno = - 1;
                                $isPublic = false;
                                foreach($list->fl as $fl) {
                                        switch ((string)$fl['val']) {
                                                case 'validlist':
                                                        $validlist = ucfirst($fl);
                                                        break;
                                                case 's_no':
                                                        $sno = $fl;
                                                        break;
                                                case 'listname':
                                                        $name = $fl;
                                                        break;
                                                case 'listkey':
                                                        $id = $fl;
                                                        break;
                                        }
                                }
                                if ($validlist == 'false') {
                                    continue;
                                }
                                if($ageIndicator == 'new')  {
                                    delete_option('zc4wp_a_' . $sno);
                                    continue;
                                }
                                $list_value_storage[$temp_var] = $sno;
                                $temp_var++;
                ?>
                        <script type="text/javascript">
                        </script>
                        <div id="saved_div_<?php echo $sno; ?>"><!--  class="zclist" onmouseover="zc_hintandTrashToggle('<?php echo $sno; ?>');" onmouseout="zc_hintandTrashToggle('<?php echo $sno; ?>');"> -->
                        <?php 
                        $total_form = get_option("zc4wp_a_" . $sno);
                        // print_r($total_form);
                        // echo "lksdjflksdjfljasdlfjaslkdfjlasdkjflsdajflksjd<br/>";
                        // echo "here:" . (trim($total_form['simple_form']) != '') . "<br/>";
                        // echo "here:" . (trim( $total_form['zoho_form']) != '');
                        if ( ($total_form != '' && array_key_exists('simple_form', $total_form) && trim($total_form['simple_form']) != '') || ($total_form != '' && array_key_exists('zoho_form', $total_form) && trim( $total_form['zoho_form']) != '') ) {  
                            $saved_list_count++;
                        ?>
                            <script>
                                zc_all_idval[zc_all_idval.length] = "<?php echo $sno; ?>";
                                zc_saved_idval[zc_saved_idval.length] = "<?php echo $sno; ?>";
                            </script>
                            <div id="saved_list_total_div">
                            <table width="99%" style="border: solid 1px #ddd;border-top:0px;" cellspacing="0" cellpadding="0" class="zcsfrmlsttbl zclstclr<?php echo $saved_list_count%2; ?>">
                                <?php if($no_header) { 
                                    $no_header = false; ?>
                                    <tr>
                                        <th height="30" width="20%" align="left" valign="top">
                                            List Name
                                        </th>
                                        <th height="30" width="20%" align="left" valign="top">
                                            Form Name
                                        </th>
                                        <th height="30" width="20%" align="left" valign="top">
                                            Form Type
                                        </th>
                                        <th height="30" width="20%" align="left" valign="top">
                                            Short Code
                                        </th>
                                        <th height="30" width="20%" align="left" valign="top">
                                            &nbsp;
                                        </th>
                                    </tr>
                                <?php } 
                                if ( $total_form != '' && array_key_exists('simple_form', $total_form) && trim($total_form['simple_form']) != '') { ?>
                                <tr>
                                    <td height="30" width="20%" align="left" valign="center" rowspan="2">
                                        <?php echo $name; ?>
                                    </td>
                                    <td height="30" width="20%" align="left" valign="top">
                                        <?php 
                                            echo $total_form['lf_form_name'];
                                        ?>
                                    </td>
                                    <td height="30" width="20%" align="left" valign="top">
                                        Local Form
                                    </td>
                                    <td height="30" width="20%" align="left" valign="top">
                                    <?php 
                                        echo "<input id=\"simple_short_code_{$sno}\" type=\"text\" readonly=\"readonly\" onclick=\"this.select();\" style=\"outline:0px;box-shadow: 0 0px 0px #f5f5f5;background:#FFF;margin:0px;padding:5px;\" class=\"zccodetxtfield\" value=\"[zc4wp_sa{$sno}]\" size=\"11\" />";
                                        $saved_list = true; 
                                    ?> 
                                    </td>
                                    <td height="30" width="20%" align="left" valign="top">
                                        <a class="zcsmallink " href="javascript:zc_showPreviewSavedForm('<?php echo $sno; ?>','simple','<?php echo $name; ?>');" id="preview_simple_<?php echo $sno; ?>">
                                            Preview
                                        </a>
                                        <a class="zcml20 zcsmallink" href="javascript:zc_editShow('<?php echo $sno; ?>','<?php echo $name; ?>','simple','edit','<?php echo $sno; ?>');" id="edit_simple_<?php echo $sno; ?>">
                                            Edit
                                        </a>
                                        <a class="zcml20 zcsmallink" id="delete_imageb_<?php echo $sno; ?>" formtype="simple" onclick="zc_editorContentChecker(this,'0','<?php echo $apiKeyVal; ?>','<?php echo $id; ?>');" style="cursor:pointer;">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php } 
                                if ( $total_form != '' && array_key_exists('zoho_form', $total_form) && trim( $total_form['zoho_form']) != '') { ?>
                                <tr>
                                    <?php if ( array_key_exists('simple_form', $total_form) && trim($total_form['simple_form']) == '') { ?>
                                    <td height="30" width="20%" align="left" valign="center" rowspan="2">
                                        <?php echo $name; ?>
                                    </td>
                                    <?php } ?>
                                    <td height="30" width="20%" align="left" valign="top">
                                        <?php 
                                            echo $total_form['zc_form_name'];
                                        ?>
                                    </td>
                                    <td height="30" width="20%" align="left" valign="top">
                                        Form from Zoho Campaigns
                                    </td>
                                    <td height="30" width="20%" align="left" valign="top">
                                        <?php
                                            echo "<input type=\"text\" id=\"zcform_short_code_{$sno}\" class=\"zccodetxtfield\" readonly=\"readonly\" onclick=\"this.select();\" style=\"outline:0px;box-shadow: 0 0px 0px #f5f5f5;background:#FFF;margin:0px;padding:5px;\" value=\"[zc4wp_za{$sno}]\" size=\"11\" />";
                                        ?>
                                    </td>
                                    <td height="30" width="20%" align="left" valign="top">
                                        <a class="zcsmallink " href="javascript:zc_showPreviewSavedForm('<?php echo $sno; ?>','zoho','<?php echo $name; ?>');" id="preview_zoho_<?php echo $sno; ?>">
                                            Preview
                                        </a>
                                        <a class="zcml20 zcsmallink" href="javascript:zc_editShow('<?php echo $sno; ?>','<?php echo $name; ?>','zoho','edit','<?php echo $sno; ?>');" id="edit_zoho_<?php echo $sno; ?>">
                                            Edit
                                        </a>
                                        <a class="zcml20 zcsmallink" id="delete_imageb_<?php echo $sno; ?>" formtype="zoho" onclick="zc_editorContentChecker(this,'0','<?php echo $apiKeyVal; ?>','<?php echo $id; ?>');" style="cursor:pointer;">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php }
                            if( (!array_key_exists('simple_form', $total_form) || trim($total_form['simple_form']) == '') && (!array_key_exists('zoho_form', $total_form) || trim($total_form['zoho_form']) != '')) { ?>
                                <tr>
                                    <td colspan="4">
                                        <em>
                                            Create a sign-up form for this mailing list within WordPress 
                                            <?php 
                                                echo "<a href=\"javascript:zc_editShow('{$sno}','{$name}','simple','create','{$sno}');\" id=\"create_simple_form_{$sno}\" class=\"zcsmallink \">Click to create</a>";
                                                $saved_list = false; 
                                            ?>
                                        </em>
                                    </td>
                                </tr>
                            <?php } else if(array_key_exists('simple_form', $total_form) && trim($total_form['simple_form']) != '' && array_key_exists('zoho_form', $total_form) && trim($total_form['zoho_form']) == '') { ?>
                                <tr>
                                    <td colspan="4">
                                        <em>
                                            Use your sign-up form from Zoho Campaigns! 
                                            <?php 
                                            echo "<a href=\"javascript:zc_editShow('{$sno}','{$name}','zoho','create','{$sno}');\" id=\"create_zoho_form_{$sno}\" class=\"zcsmallink \">Associate</a>";
                                            if (!($saved_list)) { 
                                            ?> 
                                        </em>
                                    <script type="text/javascript">
                                        zc_unsaved_idval[zc_unsaved_idval.length] = '<?php echo $sno; ?>';
                                    </script> 
                                <?php
                                } ?>
                                    </td>
                                </tr>
                             <?php } ?>
                            </table>
                            </div>
                        <?php }?>
                        </div>
                        <div style="clear:both"></div>
<?php
                            zc_formprinter($apiKeyVal,$domain_name,$id,$sno);
                        }
                }
                        $list_count = $xml->total_list_count;
                        for($i = 2; $i <= $list_count+1 ; $i++ )
                        {
                            if(!in_array($i,$list_value_storage))
                            {
                         ?>
                            <form id="form_<?php echo $i; ?>" action="options.php" method="post" autocomplete="off" >
<?php
                                settings_fields("zoho_settings_form_" . $i);
?>
                            </form>
<?php
                            }
                        }
?>
                        <script type="text/javascript">
                                zc_divHiderEvent();
                                zc_saved_page = false;
                                zc_from_index = 3;
                                var zc_saved_sno_val = '<?php echo $str; ?>';
                                <?php if(isset($sno)){ ?>
                                        var zc_sno_val = '<?php echo $sno; ?>';
                                <?php }else{ ?>
                                        var zc_sno_val = '';
                                <?php } ?>
                                var zc_first_load = true;
                                var zc_str = "<?php echo $str; ?>";
                        </script>
                        <div id="unsaved_list">
                            <div class=" zcmt15 zctxt zctcenter zcf14" id="no_list_message" style="display:none;">
                                No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.
                            </div>
                        </div>
<?php
                zc_formprinter($apiKeyVal,$domain_name,"-1","-1");
                if( isset($_GET['settings-updated']) ) {
                    unset($_GET['settings-updated']);
?>
                    <script type="text/javascript">
                        zc_saved_page = true;
                    </script>
<?php 
                } 
?>
                <div class="zcmorelist" id="morelist" onclick="javascript:zc_moreList();" style="display:none;">
                    <div>View More List</div>
                </div>
                <div class="zctcenter zcmt15" id="proceed_div" style="display:none;">
                    <input  class="zcnewbuttonWP" type="button" value="Create Sign-up Form" onclick="zc_callI('create','-1');" />
                    <input  class="zcnewcnclbutton" type="button" id="cancel_list_selection" value="Cancel" style="display:none;" />
                </div>
                <div class="zcwelcomepanouter" id="no_mailing_list" style="display:none;">
                    <div class="zcwelcomepansf" style="width:100%">
                        <div class="zctcenter zcmt20">
                            <img src="<?php echo plugins_url('assets/images/zc_mailinglist.png', dirname(__FILE__)); ?>" alt="">
                        </div>
                        <br>
                        <h1>
                            No Mailing list
                        </h1>
                        <div class=" zcmt15 zctxt zctcenter zcf14">
                            No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.
                        </div>
                    </div>
                </div>

<?php
        }
        else if($xml->status == 'success' && $xml->code == 2401)    {
?>
            <div class="zcwelcomepanouter" id="no_mailing_list" >
                <div class="zcwelcomepansf" style="width:100%">
                    <div class="zctcenter zcmt20">
                        <img src="<?php echo plugins_url('assets/images/zc_mailinglist.png', dirname(__FILE__)); ?>" alt="">
                    </div>
                    <br>
                    <h1>
                        No Mailing list
                    </h1>
                    <div class=" zcmt15 zctxt zctcenter zcf14">
                        No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.
                    </div>
                </div>
            </div>
<?php                
        }
        else if($xml->status == 'error' && $xml->code == 902)    {
?>
            <div class="zcwelcomepanouter" id="no_contact" >
                <div class="zcwelcomepansf">
                    <div class="zctcenter zcmt20">
                        <img src="<?php echo plugins_url('assets/images/zc_unknowuser.png', dirname(__FILE__)); ?>" alt="">
                    </div>
                    <br>
                    <h1>
                        Account Inactive
                    </h1>
                    <div class=" zcmt15 zctxt zctcenter zcf14">
                        The account with email <b><?php echo $dbKey['emailId']; ?></b> has been removed. Please contact the organisation admin or support@zohocampaigns.com.
                    </div>
                </div>
            </div>
<?php                
        }
        elseif ($xml->status == 'error' && ( $xml->code == 997 || $xml->code == 998 ) ) {
?>
            <div class="zcwelcomepanouter" id="user_or_org_inactive" >
                <div class="zcwelcomepansf">
                    <div class="zctcenter zcmt20">
                        <img src="<?php echo plugins_url('assets/images/zc_userinactive.gif', dirname(__FILE__)); ?>" alt="">
                    </div>
                    <br>
                    <h1>
                        Unable to access your account
                    </h1>
                    <div class=" zcmt15 zctxt zctcenter zcf14">
                        <?php echo $xml->message; ?>
                    </div>
                </div>
            </div>
<?php
        }
        else {
?>
            <div class="zcwelcomepansf" id="welcome_container">
                <h1>
                    Welcome to Zoho Campaigns Plugin
                </h1>
                    Looks like you've not integrated your Zoho Campaigns account yet. Before you start building sign-up forms, go to API settings to integrate your account with WordPress.
                    <div id="choosen_user_type" value="1" ></div>
                <div class="zcbtncntr">
                    <input type="button" value="Go to Welcome Page" class="zcnewbuttonWP" onclick="window.location = 'admin.php?page=zc-custompage'">
                </div>
            </div>
<?php
        }

?>
        <br/>
<?php
    }
?>
    </body>                    
</html>
<?php
        add_action('admin_init', 'zc_register_settings_form', '');
        add_action( 'admin_enqueue_scripts', 'zc_zcplugin_form_script');
?>
