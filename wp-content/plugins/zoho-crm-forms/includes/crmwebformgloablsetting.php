<?php
if (!defined('ABSPATH'))
    exit;

$siteurl = site_url();
$siteurl = esc_url($siteurl);
$config = get_option("zcf_crmformswpbuilder_settings");
if ($config == "") {
    $config_data = 'no';
} else {
    $config_data = 'yes';
}
require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
zcfcheckAccessToken();

if (isset($_REQUEST['code'])) {
    $code = $_REQUEST['code'];
    $reposnseAuth = zcfgetAuthTokennew($code);
}

?>
<input type="hidden" name="currentpageUrl" id="currentpageUrl" value=""/>

<div class="clearfix"></div>      
<div class="">
    <div class="panel" style="width:99%;">
        <div class="panel-body">
            <input type="hidden" id="get_config" value="<?php echo $config_data ?>" >
            <input type="hidden" id="revert_old_crm" value="crmformswpbuilder">

            <input type="hidden" id="site_url" name="site_url" value="<?php echo esc_attr($siteurl); ?>">

            <form id="crmforms-zoho-settings-form"  method="post">
                <input type="hidden" name="crmforms-zoho-settings-form" value="crmforms-zoho-settings-form" />
                <input type="hidden" id="plug_URL" value="<?php echo esc_url(ZCF_PLUGIN_BASE_URL); ?>" />

                <div class="clearfix"></div>

                <div class="clearfix"></div>
                <div class="mt20">   
                    <div class="form-group col-md-12">
                        <?php
                        global $wp;
                        $current_url = home_url(add_query_arg(array(), $wp->request));
                        $current_url = $current_url . '/wp-admin/admin.php?page=crmforms-builder';
                        require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
                        zcfcheckAccessToken();
                        $SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
                        $authtokens = $SettingsConfig['authtoken'];
                        if ($authtokens == '') {
                            ?>
                            <span class="f14 mb20 dB"><b>Zoho CRM Form Builder</b></span>
                            <span class="f14 mb20 dB">The form builder allows you to create forms in your wordpress and push the data into your Zoho CRM. Also, you can map the third party forms with Zoho CRM</span>
                            <span class="f14 mb20 dB">You must authenticate Zoho CRM before you start building.</span>

                            <a onclick="window.open('https://accounts.zoho.com/oauth/v2/auth?scope=ZohoCRM.modules.ALL,ZohoCRM.settings.ALL,ZohoCRM.users.ALL&response_type=code&access_type=offline&client_id=1000.A6YMBP1U9X5F58424HJDUEZ92JMVB6&client_secret=685aec4faaa65c268eb31cfff1295d8c042799ad5e&state=<?php echo $current_url; ?>&redirect_uri=https://extensions.zoho.com/plugin/wordpress/callback', 'Accounts Zoho', 'width=600,height=400');
                                    jQuery('#loading-image').show();
                                    jQuery('.freezelayer').show();" class="primarybtn" target="popup">Authenticate Zoho CRM</a>            
                           <?php } ?>

                    </div>
                </div>

            </form>
            <div id="loading-sync" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL); ?>/zoho-crm-forms/assets/images/ajax-loaders.gif) no-repeat cente"><?php echo esc_html__('', 'zoho-crm-form-builder'); ?></div>
            <div id="loading-image" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL); ?>/zoho-crm-forms/assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__("", "zoho-crm-form-builder"); ?></div>
        </div>
    </div>
</div>    
<?php
require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
zcfcheckAccessToken();
$SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
$authtokens = $SettingsConfig['authtoken'];
if ($authtokens != '' && isset($_REQUEST['code'])) {
    ?>
    <script>
        jQuery(window).load(function () {
            saveConfig('save', "<php echo $current_url?>");
            window.opener.location.reload();
            self.close();
        });


    </script>
<?php } ?>
<div class="freezelayer"></div>