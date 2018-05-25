<?php
if( ! defined("ZC4WP") ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}
try {
    $dbKey = get_option('zc4wp_a_apikey');
    $apikeyval = '';
    if($dbKey != '') {
        $apikeyval = $dbKey['api_key'];
    }
    $str = "1:";
    if($wpdb != null)   {
        foreach( $wpdb->get_results('SELECT option_name,option_value FROM ' . $wpdb->prefix . 'options WHERE option_name like "zc4wp_a_%";') as $key => $row) {
            // each column in your row will be accessible like this
            $my_column = $row->option_name;
            $option_value = $row->option_value;
            $temp = substr($my_column,8);
            if(is_numeric($temp) && $option_value != '')   {
                $str = $str . $temp . ":";
            }
        }
    }
    $domain_name = 'https://campaigns.zoho.com';
/*    $xmldata = $domain_name . '/api/getmailinglistsprivacy?authtoken=' . $apikeyval . '&scope=CampaignsAPI&resfmt=XML&sort=asc&fromindex=1&usertype=1&range=0&sno=' . $str;
    $open = wp_remote_post($xmldata, array(
            'method' => 'POST',
            'timeout' => 45
    ));
    $xml = "";
    if (strpos(wp_remote_retrieve_body($open) , 'IamError.zc')) {
        $xml = new SimpleXMLElement('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
    }
    else {
        try {
            $xml = new SimpleXMLElement((wp_remote_retrieve_body($open)));
        } catch (Exception $e) {
            $xml = new SimpleXMLElement('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
        }
    }
    if( $xml->status == "success" && $xml->code == 2401)    {
        $str = "1:";
        $xmldata = $domain_name . '/api/getmailinglistsprivacy?authtoken=' . $apikeyval . '&scope=CampaignsAPI&resfmt=XML&sort=asc&fromindex=1&usertype=1&range=1';
        $open = wp_remote_post($xmldata, array(
                'method' => 'POST',
                'timeout' => 45
        ));
        if (strpos(wp_remote_retrieve_body($open) , 'IamError.zc')) {
            $xml = new SimpleXMLElement('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
        }
        else {
            try {
                $xml = new SimpleXMLElement((wp_remote_retrieve_body($open)));
            } catch (Exception $e) {
                $xml = new SimpleXMLElement('<response uri="/api/getmailinglistsprivacy" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
            }
        }
    }
    if(empty($xml))
    {
        $xml = new SimpleXMLElement('<response uri="/api/getmailinglistsprivacy" version="1"><status>success</status><code>0</code><signupformversion>2</signupformversion><total_list_count>1</total_list_count><list_of_details><list><fl val="validlist">true</fl><fl val="listkey">34594177d382061ba77ae02d1fd93cb6</fl><fl val="listname">My Mailing List 1</fl><fl val="s_no">2</fl></list></list_of_details></response>');
    }*/
}
catch (Exception $e) {
    echo $e->getMessage();
    die();
}
$function_adder_simple = false;
// $GLOBALS['xml'] = $xml;
$GLOBALS['domain_name'] = $domain_name;
$GLOBALS['apikeyval'] = $apikeyval;
$GLOBALS['function_adder_simple'] = $function_adder_simple;
$GLOBALS['str'] = $str;
function zc_shortCodeAdder($attr, $content, $tag) {
    $function_adder_simple = $GLOBALS['function_adder_simple'];
    $domain_name = $GLOBALS['domain_name'];
    $apikeyval = $GLOBALS['apikeyval'];
    // $xml = $GLOBALS['xml'];
    $str = $GLOBALS['str'];
    // $sno = -1;
    // $listkey = '';
    // $listdgs = '';
    // $zuid = -1;
    // $zx = '';
    /*if($str == "1:") {
        return;
    }*/
    /*if ($xml->status == 'success' && $xml->code == 0) {
        foreach ($xml->list_of_details->list as $list) {
            $sno = -1;
            $listkey = '';
            $zuid = -1;
            $listdgs = '';
            $zx = '';
            foreach ($list->fl as $fl) {
                switch ((string) $fl['val']) {
                    case 's_no':
                        $sno = $fl;
                        break;
                    case 'listkey':
                        $listkey = $fl;
                        break;
                    case 'zuid':
                        $zuid = $fl;
                        break;
                    case 'listdgs':
                        $listdgs = $fl;
                        break;
                    case 'zx':
                        $zx = $fl;
                        break;
                }
            }
            if($sno == substr($tag,8)) {
                break;
            }
        }
    }*/
    $formType = substr($tag,6,1);
    $const = 8;
    $sNoFn = substr($tag, $const);
    $dbshortcodeval = get_option("zc4wp_a_" . $sNoFn);
    if($formType == 's') {
        if(isset($dbshortcodeval['success_header']))
        {    
            $try1 = $dbshortcodeval['success_header'];
            $try1 = str_replace("\n", "<br/>", $try1);
        }
        if(isset($dbshortcodeval['success_body']))
        {    
            $try2 = $dbshortcodeval['success_body'];
            $try2 = str_replace("\n", "<br/>", $try2);
        }
        if(isset($dbshortcodeval['error_header']))
        {    
            $try3 = $dbshortcodeval['error_header'];
            $try3 = str_replace("\n", "<br/>", $try3);
        }
        if(isset($dbshortcodeval['error_body']))
        {    
            $try4 = $dbshortcodeval['error_body'];
            $try4 = str_replace("\n", "<br/>", $try4);
        }
        if(isset($dbshortcodeval['exists_email_body']))
        {    
            $try5 = $dbshortcodeval['exists_email_body'];
            $try5 = str_replace("\n", "<br/>", $try5);
        }
        if (isset($dbshortcodeval['simple_form'])) {
            if ($function_adder_simple == false) {
                $appenderValPhp = "_" . get_the_ID() . "_";
                $action= "{$domain_name}/api/xml/listsubscribe?authtoken={$apikeyval}&scope=CampaignsAPI&version=1&resfmt=JSON&sno={$sNoFn}&";
                echo "<style>
                        * {
                            padding: 0px;
                            margin: 0px;
                        }

                        *,
                        *:after,
                        *:before {
                            -webkit-box-sizing: border-box;
                            -moz-box-sizing: border-box;
                            box-sizing: border-box;
                        }

                        body {
                            font-family: Arial, Helvetica, sans-serif;
                            background-color: #f1f1f1;
                        }

                        h1 {
                            font-size: 20px;
                            color: #333;
                            font-weight: normal;
                        }

                        .zcouterpan {
                            background-color: #fff;
                            width: 600px;
                            margin: 100px auto;
                            border: solid 1px #ddd;
                            box-shadow: 0px 1px 5px #ddd;
                        }

                        .zcouterpan h1 {
                            font-size: 16px;
                            border-bottom: solid 1px #eee;
                            background-color: #f5f5f5;
                            color: #222;
                            padding: 10px 20px;
			    margin:0px;
                        }

                        .zcp20 {
                            padding: 20px;
                        }
                    </style>
                        <script type='text/javascript'>
                            function zc_inputValidator(me,field)   {
                                var idval = me.attr('id').substring(17);
                                var fieldValue = me.val();
                                if(field == 0) {
                                    if(fieldValue=='') {
                                        jQuery(me).css({\"border\":\"2px solid red\"});
                                        jQuery(me).focus();
                                        return false;
                                    }
                                    else {
                                        jQuery(me).css({\"border\":\"1px inset grey\"});
                                    }
                                    var str=new RegExp(/^[a-zA-Z0-9\_\-\'\.]+\@[a-zA-Z0-9\-\_]+(?:\.[a-zA-Z0-9\-\_]+){0,3}\.(?:[a-zA-Z0-9\-\_]{2,6})$/);
                                    if(str.test(fieldValue) || fieldValue== '') {
                                        jQuery(me).css({\"border\":\"1px inset grey\"});
                                    }
                                    else {
                                        jQuery(me).css({\"border\":\"2px solid red\"});
                                        jQuery(me).focus();
                                        return false;
                                    }
                                }
                                else if(field == 1 || field == 2) {
                                    var str = new RegExp(\"^[A-Za-z0-9- ]+$\");
                                    if(str.test(fieldValue) || fieldValue== '') {
                                        jQuery(me).css({\"border\":\"1px inset grey\"});
                                        return;
                                    }
                                    else {
                                        jQuery(me).css({\"border\":\"2px solid red\"});
                                        jQuery(me).focus();
                                        return false;
                                    }
                                }
                                else if(field == 3) {
                                    var str = new RegExp(\"^[0-9-]+$\");
                                    if(str.test(fieldValue) || fieldValue== '') {
                                        jQuery(me).css({\"border\":\"1px inset grey\"});
                                        return;
                                    }
                                    else {
                                        jQuery(me).css({\"border\":\"2px solid red\"});
                                        jQuery(me).focus();
                                        return false;
                                    }
                                }
                            }
                            function zc_action_builder_simple(buttonRef,response_type) {
                                var me = jQuery(buttonRef).closest('[id*=\"zc_simple_\"]')[0];
                                var id = me.id.substring(10);
                                var fname = '',lname = '',phone='';
                                if(zc_inputValidator(jQuery('#form_email_input_'+id,me),0) == false) {
                                    alert(\"Enter your email address\");
                                    return false;
                                }
                                if(jQuery('#form_fname_input_'+id).is(':visible') && zc_inputValidator(jQuery('#form_fname_input_'+id,me),1) == false) {
                                    return false;
                                }
                                else if(jQuery('#form_fname_input_'+id).length > 0){
                                    fname = jQuery('#form_fname_input_'+id).val().trim();
                                }
                                if(jQuery('#form_lname_input_'+id).is(':visible') && zc_inputValidator(jQuery('#form_lname_input_'+id,me),2) == false) {
                                    return false;
                                }
                                else if(jQuery('#form_lname_input_'+id).length > 0){
                                    lname = jQuery('#form_lname_input_'+id).val().trim();
                                }
                                if(jQuery('#form_phone_input_'+id).is(':visible') && zc_inputValidator(jQuery('#form_phone_input_'+id,me),3) == false) {
                                    return false;
                                }
                                else if(jQuery('#form_phone_input_'+id).length > 0){
                                    phone = jQuery('#form_phone_input_'+id).val().trim();
                                }
                                if(response_type == 'simple_form_response') {
                                    var url = \"{$action}\";
                                    var contactxml = \"contactinfo=<xml><fl val='Contact Email'>\" + jQuery('#form_email_input_'+id).val().trim() + \"</fl>\";
                                    if(fname != '') {
                                        contactxml += \"<fl val='First Name'>\" + fname + \"</fl>\";
                                    }
                                    if(lname != '') {
                                        contactxml += \"<fl val='Last Name'>\" + lname + \"</fl>\";
                                    }
                                    if(phone != '') {
                                        contactxml += \"<fl val='Phone'>\" + phone + \"</fl>\";
                                    }
                                    contactxml += \"</xml>\";
                                    url += contactxml;
                                    jQuery('#background_div').css('display','block');
                                    jQuery('#response_div').css('display','block');
                                    jQuery('#response_div').css('z-index','999');
                                    jQuery('#headval').attr('align','center');
                                    jQuery('#bodyval').attr('align','center');
                                    jQuery('#headval').html('Loading...');
                                    jQuery('#bodyval').html(\"<img src='" . plugins_url('assets/images/uploadImg.gif',dirname(__FILE__)) . "' />\");
                                    jQuery.ajax({
                                        type: 'POST',
                                        url: url,
                                        success: function (responseData, textStatus, jqXHR) {
                                            jQuery('#form_email_input_'+id).val('');
                                            if(fname != '') {
                                                jQuery('#form_fname_input_'+id).val('');
                                            }
                                            if(lname != '') {
                                                jQuery('#form_lname_input_'+id).val('');
                                            }
                                            if(phone != '') {
                                                jQuery('#form_phone_input_'+id).val('');
                                            }
                                            var try1 = jQuery(\"#success_header_\"+id).val();
                                            var try2 = jQuery(\"#success_body_\"+id).val();
                                            var try3 = jQuery(\"#error_header_\"+id).val();
                                            var try4 = jQuery(\"#error_body_\"+id).val();
                                            var try5 = jQuery(\"#exists_email_body_\"+id).val();
                                            jQuery(\"#headval\").removeAttr(\"align\");
                                            jQuery(\"#bodyval\").removeAttr(\"align\");
                                            if(responseData.status == 'success' && responseData.code == 0) {
                                                if((responseData.message).indexOf(\"email address already exists in the list\") < 0)  {
                                                    jQuery(\"#headval\").html(try1);
                                                    jQuery(\"#bodyval\").html(try2);
                                                }
                                                else    {
                                                    jQuery(\"#headval\").html(try3);
                                                    jQuery(\"#bodyval\").html(try5);
                                                }
                                            }
                                            else {
                                                jQuery(\"#headval\").html(try3);
                                                if(responseData.code == 2003)
                                                {
                                              
                                                    jQuery(\"#bodyval\").html(try5);
                                                }
                                                else
                                                {
                                                    jQuery(\"#bodyval\").html(try4);
                                                }
                                            }
                                            return false;
                                        },
                                        error: function (responseData, textStatus, errorThrown) {
                                            var try3 = jQuery(\"#error_header_\"+id).val();
                                            var try4 = jQuery(\"#error_body_\"+id).val();
                                            jQuery(\"#headval\").removeAttr(\"align\");
                                            jQuery(\"#bodyval\").removeAttr(\"align\");
                                            jQuery(\"#headval\").html(try3);
                                            jQuery(\"#bodyval\").html(try4);
                                            return false;
                                        }
                                    });
                                }
                                return false;
                            }
                            function zc_closePopUp(me) {
                                jQuery('#background_div').css('display','none');
                                jQuery('#response_div').css('display','none');
                            }
                        </script>";
                        $response_part = "<div id=\"background_div\" style=\"text-transformation:initial;display:none;background-color: rgb(0, 0, 0); opacity: 0.5; z-index: 100; position: fixed; width: 100%; height: 950px; top: 0px; left: 0px;\"></div>";
                        $response_part .= "<div id=\"response_div\" style=\"left:calc(50% - 300px);display:none;z-index:999px;position:fixed;top:15px;\" class=\"zcouterpan\"><img src='" . plugins_url('assets/images/close.png',dirname(__FILE__)) . "' id='closeImg' width='12' style='position:absolute;cursor:pointer;right:15px;top:15px;z-index:1008;' onClick='zc_closePopUp();'><h1 id=\"headval\" align=\"center\">Loading...</h1><div id=\"bodyval\" class=\"zcp20\" align=\"center\" style='font-size:13px;'><img src='" . plugins_url('assets/images/uploadImg.gif',dirname(__FILE__)) . "' /></div></div>";
                        echo $response_part;
                $function_adder_simple = true;
            }
            $texteditorvalue = $dbshortcodeval['simple_form'];
            // $texteditorvalue = str_replace("\"button\"", "\"submit\"", $texteditorvalue);
            // if($dbshortcodeval != '' && $dbshortcodeval['response_area_shower'] == 'simple_form_response' ) {
                $starting_part  = "<!--Start Zoho Campaigns Signup Form--><div id='zc_simple_$sNoFn' name='zcampaignminiform'>";
                $starting_part .= "<input style='display:none;' id='success_header_{$sNoFn}'  type='text' name='success_header' value=\"{$try1}\"/>";
                $starting_part .= "<textarea style='display:none;' id='success_body_{$sNoFn}'  name='success_body' >{$try2}</textarea>";
                $starting_part .= "<input style='display:none;' id='error_header_{$sNoFn}' value=\"{$try3}\" type='text' name='error_header' />";
                $starting_part .= "<textarea style='display:none;' id='error_body_{$sNoFn}'  name='error_body' >{$try4}</textarea>";
                $starting_part .= "<textarea  style='display:none;' id='exists_email_body_{$sNoFn}' name='exists_email_body' >{$try5}</textarea>";
                $starting_part .= "<input style='display:none;' id='refresh_stopper_{$sNoFn}'  type='text' name='refresh_stopper' />";
		$ending_part    = "</div><script>jQuery('#form_button_{$sNoFn}').attr('onClick','zc_action_builder_simple(this,\"simple_form_response\")');jQuery(document).ready(function(){if(jQuery.datepicker !== undefined && jQuery.datepicker !==\"undefined\"){jQuery('.dateClass').datepicker({changeYear: true,changeMonth: true,yearRange: '-100:+50',showOn: 'both',buttonImage: '" . plugins_url('assets/images/icon_calendar.gif',dirname(__FILE__)) . "',buttonImageOnly: true,});jQuery('#ui-datepicker-div').removeClass('ui-helper-clearfix ui-corner-all ui-helper-hidden-accessible');}});</script><!-- End Zoho Campaigns Signup Form -->";
                
            // }
            // else {
            //     $starting_part   = "<!--Start Zoho Campaigns Signup Form--><form method ='POST' target='_blank' id='zc_simple_$sNoFn' name='zcampaignminiform' onsubmit='return zc_action_builder_simple(this,\"zcform_form_response\")'>"
            //      . "<!-Do not edit the below ##DEPLOY_PRODUCT_NAME## Campaigns hidden tags -->"
            //      . "<input type=\"hidden\" id=\"fieldBorder_{$sNoFn}\" value=\"\">"
            //      . "<input type=\"hidden\"  id=\"submitType_{$sNoFn}\" name=\"submitType\" value=\"optinCustomView\">"
            //      . "<input type=\"hidden\"  name=\"zx\" id=\"cmpZuid_{$sNoFn}\" value=\"{$zx}\">"
            //      . "<input type=\"hidden\" id=\"zcvers_{$sNoFn}\" name=\"zcvers\" value=\"1.0\">"
            //      . "<input type='hidden' name='oldListIds' id='allCheckedListIds_{$sNoFn}' value=''>"
            //      . "<input type=\"hidden\" id=\"mode_{$sNoFn}\" name=\"mode\" value=\"OptinCreateView\">"
            //      . "<input type=\"hidden\" id=\"zcld_{$sNoFn}\" name=\"zcld\" value=\"{$listdgs}\">"
            //      . "<!-- End of the campaigns hidden tags -->";
            //     $ending_part     = "</form><!-- End Zoho Campaigns Signup Form -->";
            // }
            return $starting_part . $texteditorvalue . $ending_part;
        } else {
            $tag = '[' . $tag . ']';
            return $tag;
        }
    }
    elseif ($formType == 'z') {
        $const = 8;
        $sNoFn = substr($tag, $const);
        $dbshortcodeval = get_option("zc4wp_a_" . $sNoFn);
        if (isset($dbshortcodeval['zoho_form'])) {
            $formValue = $dbshortcodeval['zoho_form'];
			$datepickerPath = includes_url() . '/js/jquery/ui/datepicker.min.js';
            $formValue = "<style>#THANKSPAGE table,td,th{border:0px !important;}</style><script type=\"text/javascript\">var opt_jQuery = jQuery.noConflict();opt_jQuery(document).ready( function($) {var brClr = opt_jQuery(\"#CONTACT_EMAIL\").css(\"border-left-color\");opt_jQuery(\"#fieldBorder\").val(brClr);/*Before submit, if you want to trigger your event, \"include your code below\" */ _setOptin(false,function(){/*include your code is here.*/});});</script><script type='text/JavaScript' src='https://wordpress.maillist-manage.com/js/resource.js'></script><script type='text/JavaScript' src='https://wordpress.maillist-manage.com/js/subscribe.js'></script><script type='text/JavaScript' src='https://wordpress.maillist-manage.com/js/zccommon.js'></script><script type='text/JavaScript' src='https://wordpress.maillist-manage.com/js/optin_min.js'></script><script type='text/javascript' src='https://wordpress.maillist-manage.com/js/util.js'></script><script type=\"text/javascript\" src='" . $datepickerPath ."'  charset=\"utf-8\"></script>" . $formValue;
            return $formValue;
        } else {
            $tag = '[' . $tag . ']';
            return $tag;
        }
    }
}
function ZC_KMEncryptionAlgorithm($apikeyval)   {
    for($i = 0,$encryptedValue=''; $i < strlen($apikeyval); $i++)
    {
        if( ( ord($apikeyval[$i]) >= 65 && ord($apikeyval[$i]) <= 77 ) || ( ord($apikeyval[$i]) >= 97 && ord($apikeyval[$i]) <= 109 ) ) {
            $encryptedValue[$i] = chr( ord($apikeyval[$i]) + 13 );
        }
        else if( ( ord($apikeyval[$i]) > 77 && ord($apikeyval[$i]) < 91 ) || ( ord($apikeyval[$i]) > 109 && ord($apikeyval[$i]) < 123 ) )    {
            $encryptedValue[$i] = chr( ord($apikeyval[$i]) - 13 );
        }
        else if( ord($apikeyval[$i]) >= 48 && ord($apikeyval[$i]) <= 52 )   {
            $encryptedValue[$i] = chr( ord($apikeyval[$i]) + 5 );
        }
        else if( ord($apikeyval[$i]) > 52 && ord($apikeyval[$i]) <= 57 )    {
            $encryptedValue[$i] = chr( ord($apikeyval[$i]) - 5 );
        }
    }
    return implode("",$encryptedValue);
}
/*if ($xml->status == 'success' && ($xml->code == 0 || ($xml->code == 2401 && $str == "1:"))) {
    foreach ($xml->list_of_details->list as $list) {
        $sno = -1;
        foreach ($list->fl as $fl) {
            switch ((string) $fl['val']) {
                case 'sno':
                    $sno = $fl;
                    break;
            }
        }
        add_shortcode("zc4wp_sa" . $sno, 'zc_shortCodeAdder');
        add_shortcode("zc4wp_za" . $sno, 'zc_shortCodeAdder');
    }
}*/

// if($str != "1:"  && ($xml->status == 'success' && ($xml->code == 0 || $xml->code == 2401 )) )    {
if($str != "1:")    {
    $saved_sno = explode(":",$str);
    foreach ($saved_sno as $sno) {
        add_shortcode("zc4wp_sa" . $sno, 'zc_shortCodeAdder');
        add_shortcode("zc4wp_za" . $sno, 'zc_shortCodeAdder');
    }
}
?>
