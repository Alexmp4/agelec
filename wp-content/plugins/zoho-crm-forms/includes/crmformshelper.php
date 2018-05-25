<?php

if (!defined('ABSPATH'))
    exit;

class zcfhelper {

    private $data_information = array();

    public function __construct() {

        $this->data_information['active_plugins'] = get_option('active_plugins');
    }
    public function zcf_setActivatedPlugin($ActivatedPlugin) {
        $this->data_information['ActivatedPlugin'] = $ActivatedPlugin;
    }
    public function zcf_setShortcodeDetails($shortcode_details) {
        $this->data_information['shortcode_details'] = array();
        if (!empty($shortcode_details))
            $this->data_information['shortcode_details'] = $shortcode_details;
    }
    public function zcf_setPluginsUrl($plugins_url) {
        $this->data_information['plugins_url'] = $plugins_url;
    }
}
