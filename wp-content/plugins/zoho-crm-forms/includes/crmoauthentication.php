<?php

function zcfgetAuthTokennew($code) {
    $configSetting = get_option("zcf_crmformswpbuilder_settings");
    if ($configSetting['authtoken'] == '') {
        $clientid = '1000.A6YMBP1U9X5F58424HJDUEZ92JMVB6';
        $client_secret = '685aec4faaa65c268eb31cfff1295d8c042799ad5e';
        $config['client_id'] = $clientid;
        $config['client_secret'] = $client_secret;
        $config['redirect_uri'] = 'https://extensions.zoho.com/plugin/wordpress/callback';
        $url ="https://accounts.zoho.com/oauth/v2/token?code=$code&grant_type=authorization_code&client_id=" . $config['client_id'] . "&client_secret=" . $config['client_secret'] . "&redirect_uri=" . $config['redirect_uri'] . "";
        $zfformsresponse =  wp_remote_post( $url);
        $responsedata = json_decode(wp_remote_retrieve_body($zfformsresponse),true);
        $authtokensEncript = base64_encode(base64_encode(base64_encode($responsedata['access_token'])));
        $refreshtokenEncript = base64_encode(base64_encode(base64_encode($responsedata['refresh_token'])));
        $config['authtoken'] = $authtokensEncript;
        $config['refresh_token'] = $refreshtokenEncript;
        $config['created_time'] = date("Y-m-d H:i:s");
        $config['valid_time'] = $responsedata['expires_in_sec'];
        $config['api_domain'] = $responsedata['api_domain'];
        update_option("zcf_crmformswpbuilder_settings", $config);
        zcfsynModules();
    }
}

function zcfcheckAccessToken() {

    $configSetting = get_option("zcf_crmformswpbuilder_settings");

    if ($configSetting['refresh_token'] != '') {
        $currentDate = strtotime(date("Y-m-d H:i:s"));
        $access_token = base64_decode(base64_decode(base64_decode($configSetting['authtoken'])));
        $refresh_token = base64_decode(base64_decode(base64_decode($configSetting['refresh_token'])));
        $created_time = strtotime($configSetting['created_time']);
        $created_time = $created_time + 3600;
        $plusOneHour = strtotime(date('Y-m-d H:i:s', $created_time));
        if ($access_token == '' || ($currentDate > $plusOneHour)) {
            $generateValue = zcfgenerateAccessToken();
            $config['result_Type'] = true;
            $config['access_token'] = $generateValue['authtoken'];
            $config['refresh_token'] = $generateValue['refresh_token'];
            $config['client_id'] = $generateValue['client_id'];
            $config['client_secret'] = $generateValue['client_secret'];
            $config['oath_id'] = $generateValue['authtoken'];
            $config['result_Type'] = $generateValue['result_Type'];
        } else {
            $config['result_Type'] = true;
            $config['access_token'] = $access_token;
            $config['refresh_token'] = $refresh_token;
            $config['client_id'] = $configSetting['client_id'];
            $config['client_secret'] = $configSetting['client_secret'];
            $config['oath_id'] = $configSetting['authtoken'];
        }

        return $config;
    }
}

function zcfgenerateAccessToken() {
    $curl = curl_init();
    $configSetting = get_option("zcf_crmformswpbuilder_settings");
    $currentDate = strtotime(date("Y-m-d H:i:s"));
    $refresh_token = base64_decode(base64_decode(base64_decode($configSetting['refresh_token'])));
    $client_id = $configSetting['client_id'];
    $client_secret = $configSetting['client_secret'];
    $redirect_uri = 'https://extensions.zoho.com/plugin/wordpress/callback';
    $url ="https://accounts.zoho.com/oauth/v2/token?client_id=$client_id&client_secret=$client_secret&grant_type=refresh_token&refresh_token=$refresh_token&redirect_uri=$redirect_uri";
    $zfformsresponse =  wp_remote_post( $url);
    $response = json_decode(wp_remote_retrieve_body($zfformsresponse),true);
    $authtokensEncript = base64_encode(base64_encode(base64_encode($response['access_token'])));
    $refreshtokenEncript = base64_encode(base64_encode(base64_encode($refresh_token)));
    $config['authtoken'] = $authtokensEncript;
    $config['refresh_token'] = $refreshtokenEncript;
    $config['created_time'] = date("Y-m-d H:i:s");
    $config['valid_time'] = $response['expires_in_sec'];
    $config['client_id'] = $client_id;
    $config['client_secret'] = $client_secret;
    $config['redirect_uri'] = $redirect_uri;
    $config['result_Type'] = 'TRUE';
    update_option("zcf_crmformswpbuilder_settings", $config);
    return $config;
}

function zcfsynModules() {
    include_once(ZCF_BASE_DIR_URI . 'includes/crmapiintergration.php');
    include_once(ZCF_BASE_DIR_URI . 'includes/crmwebformfieldsfuntions.php');
    $client = new zcfaccountApi();
    $core = new zcfcoreGetFields();
    zcfcheckAccessToken();
    $SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
    $authtoken = base64_decode(base64_decode(base64_decode($SettingsConfig['authtoken'])));
    $client->zcfGetModules($authtoken);
    $core->zcfgetUsersList();
    global $wpdb;
    $resultaiss = $wpdb->get_results("select distinct(api_name),plural_label from zcf_zohocrm_list_module where  api_name !='' and api_name NOT IN('Visits','Vendors','Tasks','Social','Sales_Orders','Reports','Quotes','Purchase_Orders','WPjects','WPducts','Price_Books','Deals','Notes','Invoices','Home','Feeds','Events','Accounts','Emails','Documents','Dashboards','Campaigns','Calls','Attachments','ApWPvals','Activities')");
    foreach ($resultaiss as $key => $value) {
        $client->zcfgetAssignmentRule($authtoken, $value->api_name);
    }
}

?>