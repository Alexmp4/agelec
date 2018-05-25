<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
try {
    $dbKey       = get_option('zc4wp_a_apikey');
    $apikeyval   = $dbKey['api_key'];
    $domain_name = 'https://campaigns.zoho.com';
    $xmldata     = $domain_name . '/api/getmailinglists?authtoken=' . $apikeyval . '&scope=CampaignsAPI&sort=asc&fromindex=1&range=20&resfmt=XML';
    $open = wp_remote_post($xmldata,array('method' => 'POST','timeout' => 45));
    if(strpos(wp_remote_retrieve_body($open),'IamError.zc'))
    {
        $xml = simplexml_load_string('<response uri="/api/getmailinglists" version="1"><code>404</code><status>Not Found</status><message>We couldn\'t find the resource you\'re looking for.Please recheck the documentation and try again.</message></response>');
    }
    else
    {
        $xml = simplexml_load_string((wp_remote_retrieve_body($open)));
    }
}
catch (Exception $e) {
    echo $e->getMessage();
    die();
}

delete_option('zc4wp_a_apikey');

if ($xml->status == 'success' && $xml->code == 0) {
    foreach ($xml->list_of_details->list as $list) {
        $sno = -1;
        foreach ($list->fl as $fl) {
            switch ((string) $fl['val']) {
                case 'sno':
                    $sno = $fl;
                    break;
            }
        }
		delete_option('zc4wp_a_' . $sno);
    }
}

?>