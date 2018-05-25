<?php
/*
   Plugin Name:Zoho Campaigns
   Plugin URI:https://www.zoho.com/campaigns/help/integrations/zoho-campaigns-plugin-for-wordpress.html
   Description:The go-to solution for email marketing, Zoho Campaigns lets you use Sign-up forms for your blog or website. 1) INTEGRATE your Zoho Campaigns account 2) Use Sign-up forms from your account or CREATE one right here in WordPress 3) EMBED these forms in your blog or website.
   Version:1.4.8
   Author:Zoho Campaigns
   Author URI:https://zoho.com/campaigns
*/
/*
    Copyright (c) 2015, ZOHO CORPORATION
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

    2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
if( !defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}
else {
    define("ZC4WP","ZC4WP_PLUGIN_ACTIVATED");
    define("ZC4WP_VERSION","1.0");
    define("ZC4WP_ZC_PLUGIN_HOME_DIR",plugin_dir_path(__FILE__));
}
function zc_zcplugin_script() {
    wp_enqueue_style( 'zc_zohocampaigns_style', plugin_dir_url( __FILE__ ) . '/assets/css/Zoho-Campaigns-css.css', false, '1.0.0' );
    wp_enqueue_script( 'zc_zohocampaigns_script', plugin_dir_url( __FILE__ ) . '/assets/js/Zoho-Campaigns-js.js', array(),'1.0.0',false); 
}
function zc_register_settings_apikey() {
    register_setting('zoho_settings', 'zc4wp_a_apikey', 'zc_validate_settings');
}
function zc_validate_settings($settings) {
    if( isset( $settings['api_key'] ) ) {
        $settings['api_key'] = trim( strip_tags( $settings['api_key'] ) );
    }
     if( isset($_POST['zc_domain_url'])) {
    update_option('zc_domain_url', esc_attr($_POST['zc_domain_url']));
    }
    return $settings;
} 
function zc_register_my_custom_menu_page() {
    add_menu_page( 'Zoho Campaigns Plugin', 'Zoho Campaigns Plugin', 'manage_options', 'zc-custompage', 'zc_init_home', plugins_url('assets/images/zc_campaigns_logo.1.svg',__FILE__), 90 );
    add_submenu_page('zc-custompage','API Settings','API Settings','manage_options','zc-custompage','zc_init_home');
    require_once ZC4WP_ZC_PLUGIN_HOME_DIR . "php/signup-formpage.php";
    add_submenu_page( 'zc-custompage', 'Form Creation', 'Forms', 'manage_options', 'zc-forms', 'zc_init' );
}
function zc_init_home() {
    $dbKey=get_option('zc4wp_a_apikey');    
?>
    <!doctype>
    <html>
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=Edge">
            <script type="text/javascript">
                zc_pluginDir = "<?php echo plugins_url("",__FILE__); ?>";
            </script>
            <script type="text/javascript">
                const zc_on_load_api_val = "<?php if($dbKey != '') { echo $dbKey['api_key']; } else { echo ''; } ?>";
            </script>
            <script type="text/javascript">
                const zc_on_load_account_id = "<?php if($dbKey != '') {  echo $dbKey['accountId']; } else { echo ''; } ?>";
            </script>
        </head>
        <body >
            <img width="0" height="0" src="<?php echo plugins_url('assets/images/zc_success.png',__FILE__); ?>" style="display:none;" />
            <form action="options.php" method="post" id="api_key_form" autocomplete="off">
                <?php settings_fields( 'zoho_settings' ); ?>
                <div class="zcwelcomepanouter" id="api_key_div" style="display:none;">
                    <div class="zcwelcomepan">
                        <div>
                            <a href="https://www.zoho.com/campaigns/help/integrations/zoho-campaigns-plugin-for-wordpress.html#API_settings" target="_blank">
                                <img src="<?php echo plugins_url('assets/images/zchelpicon.png',__FILE__); ?>" width="23" height="23" alt="Help" style="float:right; cursor:help;" title="Help"/>
                            </a>
                        </div>
                        <div class="zcwelheading zctcntr" style="margin-bottom: 30px;">
                            Zoho Campaigns Account Details
                        </div>
                        <div class="zcmt20">
                            <table width="100%" border="0" cellspacing="10" cellpadding="0">
                                <tr>
                                    <td class="zctxt" align="right">
                                        Email Address:
                                    </td>
                                    <td height="45">
                                        <input type="text" class="zcctmzfldpan" onkeyUp="if (event.keyCode == 13) { zc_accountVerfication();}else{zc_emailIdValidator();}" id="zc_emailId" name="zc4wp_a_apikey[emailId]" size="20" autocomplete="on" onfocus="zc_fieldFocused('1');" value="<?php if($dbKey != ''){echo  $dbKey['emailId'];}else{echo "";}?>" onblur="zc_emailIdValidator();"/>
                                    </td>
                                    <td>
                                        &nbsp;
                                        <span id="zc_email_error">
                                            <img width="20" height="20" src="<?php echo plugins_url('assets/images/zc_success.png',__FILE__); ?>" align="absmiddle" style="display:none;" />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="100" class="zctxt" align="right">
                                        API Key:
                                    </td>
                                    <td height="45" width="40%"> 
                                        <input type="text" class="zcctmzfldpan" onkeyUp="if (event.keyCode == 13) { zc_accountVerfication();}else{zc_apiKeyValidator();}" id="zc_api_key" name="zc4wp_a_apikey[api_key]" value="<?php if($dbKey != ''){echo  $dbKey['api_key'];}else{echo "";}?>" size="20" autocomplete="on" onfocus="zc_fieldFocused('0');"  onblur="zc_apiKeyValidator();"/>
                                        <input type="hidden" name="zc4wp_a_apikey[ageIndicator]" id="ageIndicator" value="new">
                                        <input type="hidden" name="zc4wp_a_apikey[accountId]" id="accountId" value="<?php if($dbKey != ''){echo  $dbKey['accountId'];}else{echo "-1";}?>">
                                        <input type="hidden" name="zc4wp_a_apikey[orgName]" id="orgName" value="<?php if($dbKey != ''){echo  $dbKey['orgName'];}else{echo "-1";}?>">
                                        <input type="hidden" name="zc4wp_a_apikey[active]" id="active" value="<?php if($dbKey != ''){echo  $dbKey['active'];}else{echo "-1";}?>">
                                        <input type="hidden" name="zc4wp_a_apikey[emailId]" id="emailId" value="<?php if($dbKey != ''){echo  $dbKey['emailId'];}else{echo "-1";}?>">
                                        <input type="hidden" name="zc4wp_a_apikey[integratedDate]" id="integratedDate" value="<?php if($dbKey != ''){echo  $dbKey['integratedDate'];}else{echo "-1";}?>">
                                        <input type="hidden" name="zc_domain_url" id="zc_domain_url" val ="<?php $var_domain = get_option('zc_domain_url'); if(isset($var_domain) && $var_domain !== FALSE){echo $var_domain;} else { echo 'https://campaigns.zoho.com';}?>">

                                    </td>
                                    <td>
                                        &nbsp;
                                        <span id="zc_api_key_error">
                                            <img width="20" height="20" src="<?php echo plugins_url('assets/images/zc_success.png',__FILE__); ?>" align="absmiddle" style="display:none;" />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <div class="zclink">
                                            <a class="zcsmallink" href="https://www.zoho.com/campaigns/help/api/authentication-token.html#API_Auth_Token" id="help_link" target="_blank">
                                                Learn how to generate the API key.
                                            </a>
                                        </div>
                                        <div class="zcmt20">
                                            <div id="save_account" onclick="return zc_accountVerfication('1')" class="button button-primary" style="box-shadow:none;margin-right:15px;">
                                                Integrate
                                            </div>
                                            <input type="button" value="Cancel" id="cancel_account_changes" onclick="zc_cancelChanges()" style="display:none;" class="button">
                                        </div>
                                    </td>
                                    <td>
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                            <input type="text" value="" id="hidden_text" style="display:none;" name="zc4wp_a_apikey[error_message]"/>
                        </div>
                        <br />
                    </div>
                </div>
                <div class="zcwelcomepanouter" id="welcome_div" style="display:none;">
                    <div class="zcwelcomepan">
                        <div>
                            <a href="https://www.zoho.com/campaigns/help/integrations/zoho-campaigns-plugin-for-wordpress.html" target="_blank">
                                <img src="<?php echo plugins_url('assets/images/zchelpicon.png',__FILE__); ?>" width="23" height="23" alt="Help" style="float:right; cursor:help;" title="Help"/>
                            </a>
                        </div>
                        <div class="zclogo">
                            <img src="<?php echo plugins_url('assets/images/zc_campaigns_logo.svg',__FILE__); ?>" width="141" height="130" alt=""/>
                        </div>
                        <h1>
                            Welcome to Zoho Campaigns
                        </h1>
                        <div class="zcsubcontent">
                            Three steps is all it takes to add a Sign-up form to your website or blog.
                        </div>
                        <div style="margin-top:50px; margin-left:30px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" valign="top">
                                        <div class="zcstepsno">
                                            1
                                        </div>
                                        <div style="position:relative">
                                            <div class="zcstepline"></div>
                                        </div>
                                    </td>
                                    <td align="center" valign="top">
                                        <div class="zcstepsno">
                                            2
                                        </div>
                                    </td>
                                    <td align="center" valign="top">
                                        <div class="zcstepsno">
                                            3
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        &nbsp;
                                    </td>
                                    <td align="center" valign="top">
                                        &nbsp;
                                    </td>
                                    <td align="center" valign="top">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td width="33%" align="center" valign="top">
                                        <div class="zcsteptitle">
                                            Integrate
                                        </div>
                                        <div class="zcstepcontent">
                                            your Zoho Campaigns account using the API key.
                                        </div>
                                    </td>
                                    <td width="33%" align="center" valign="top">
                                        <div class="zcsteptitle">
                                            Create
                                        </div>
                                        <div class="zcstepcontent">
                                            sign-up forms for your mailing lists using our design tools.  
                                        </div>
                                    </td>
                                    <td width="33%" align="center" valign="top">
                                        <div class="zcsteptitle">
                                            Embed
                                        </div>
                                        <div class="zcstepcontent">
                                            sign-up forms in your website or blogs using a short code.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="zcwelcomebtncntr">
                        <input type="button" class="zcnewbuttonWP" value="Start Integration" onclick="zc_startIntegration()" />
                    </div>
                </div>
                <div class="zcwelcomepanouter" id="details_div" style="display:none;">
                    <div class="zcwelcomepan">
                        <div class="zcgreentxt zctcntr" id="saving_success_message" style="margin-bottom:20px;display:none;">
                        <?php 
                            $is_submission_successful = false;
                            if( isset($_GET['settings-updated']) ) {
                                unset($_GET['settings-updated']);
                                $is_submission_successful = true;
                        ?>
                                You’ve successfully integrated your Zoho Campaigns account with WordPress.
                        <?php
                            }
                        ?>
                        </div>
                        <div class="zcwelheading zctcntr" >
                            Zoho Campaigns Account Details
                        </div>
                        <br />
                        <div class="zcmt10 zctxtnrml">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="zctable zctblbdr ">
                                <tr>
                                    <td width="170" class=" zclabel">
                                        Email Address:
                                    </td>
                                    <td height="45" width="50%">
                                        <span id="email_span"></span>
                                    </td>
                                    <td rowspan="2" align="center">
                                        <a href="javascript:zc_changeAPIKey();" class="zcsmallink">
                                            Change Account
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="170" class="zctxt zclabel">
                                        API Key:
                                    </td>
                                    <td height="45">
                                        <span id="api_key_details"></span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="zctable zctblbdr " style="margin-top:35px;">
                                <tr>
                                    <td class=" zclabel" width="170">
                                        Organisation Name:
                                    </td>
                                    <td height="45">
                                        <span id="orgName_span"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td  class=" zclabel">
                                        User Status:
                                    </td>
                                    <td height="45">
                                        <span id="active_span"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td  class=" zclabel">
                                        Integrated on:
                                    </td>
                                    <td height="45">
                                        <span id="intergrated_date_span"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <br />
                    </div>
                    <?php if($is_submission_successful)  { ?>
                        <div class="zcwelcomebtncntr" id="proceed_button_div" style="display:none;">
                            <input type="button" value="Proceed" class="zcnewbuttonWP" onclick="window.location='admin.php?page=zc-forms'">
                        </div>
                        <script type="text/javascript">
                                        zc_successMessage();
                        </script>
                    <?php } ?>
                </div>    
            </form>
            <br/>
        </body>
    </html>
<?php
}
function zc_get_domain() {
                 //ob_start();
                $var_domain = get_option('zc_domain_url');
                if(isset($var_domain) && $var_domain !== FALSE)
                {
                    echo trim($var_domain,' ');
                    
                }
                else 
                {
                 
                    echo 'https://campaigns.zoho.com';
                }
                //echo ob_get_clean();
                die();
            }
    add_action( 'admin_menu', 'zc_register_my_custom_menu_page' );
    require_once ZC4WP_ZC_PLUGIN_HOME_DIR . "php/shortcode-adder-php.php";
    
    add_action('admin_init', 'zc_register_settings_apikey');
    add_action( 'admin_enqueue_scripts', 'zc_zcplugin_script');
    add_action('wp_ajax_zc_get_domain', 'zc_get_domain');
?>
