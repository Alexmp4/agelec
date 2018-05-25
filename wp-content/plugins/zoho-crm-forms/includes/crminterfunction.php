<?php

if (!defined('ABSPATH'))
    exit;

class zcfajaxcore {

    public static function zcfforms_ajax_events() {
        $ajax_actions = array(
            'zcfchooseplugin' => false,
            'zcfSaveCRMconfig' => false,
            'zcfsaveSyncValue' => 'false',
            'zcfsend_mapping_configuration' => 'false',
            'zcfnewlead_form' => 'false',
            'zcf_get_contactform_fields' => 'false',
            'zcf_map_contactform_fields' => 'false',
            'zcf_save_contact_form_title' => 'false',
            'zcf_send_mapped_config' => 'false',
            'zcf_delete_mapped_config' => 'false',
            'zcfcaptcha_info' => 'false',
            'zcf_save_usersync_option' => 'false',
            'zcf_change_menu_order' => 'false',
            'send_order_info' => 'false',
            'zcfmainFormsActions' => 'false',
        );
        foreach ($ajax_actions as $action => $value) {
            add_action('wp_ajax_' . $action, array(__CLASS__, $action));
        }
    }

    public static function zcfchooseplugin() {

        $selectedPlugin = 'crmformswpbuilder';
        update_option('ZCFFormBuilderPluginActivated', $selectedPlugin);
        require_once(ZCF_BASE_DIR_URI . "includes/form-zohocrmconfig.php");
        die;
    }

    public static function zcfnewlead_form() {
        if ($_REQUEST['Action'] == 'zcfCreateShortcode') {

            require_once(ZCF_BASE_DIR_URI . "includes/crmshortcodefunctions.php");
            $zcfCreateShortcode = new zcfManageShortcodesActions();
            $value = $zcfCreateShortcode->zcfCreateShortcode($_REQUEST['Module'], $_REQUEST['LayoutName'], $_REQUEST['formTitle'], $_REQUEST['layoutId']);
            $value['onAction'] = 'onCreate';
        } elseif ($_REQUEST['Action'] == 'Editshortcode') {
            $value = array();
            $value['shortcode'] = $_REQUEST['shortcode'];
            $value['module'] = $_REQUEST['Module'];
            $value['crmtype'] = $_REQUEST['plugin'];
            $value['onAction'] = 'onEditShortCode';
            $value['formTitle'] = $_REQUEST['formTitle'];
            require_once(ZCF_BASE_DIR_URI . "includes/crmshortcodefunctions.php");
            $zcfCreateShortcode = new zcfManageShortcodesActions();
            $zcfCreateShortcode->zcfsynceditUploadField($_REQUEST['Module'], $_REQUEST['LayoutName'], $_REQUEST['formTitle'], $_REQUEST['layoutId'], $value['shortcode']);
        } elseif ($_REQUEST['Action'] == 'zcfupdateState') {
            $value = array();
            $value['formfieldIds'] = json_decode(stripslashes($_REQUEST['formfieldIds']));
            require_once(ZCF_BASE_DIR_URI . "includes/crmshortcodefunctions.php");
            $zcfCreateShortcode = new zcfManageShortcodesActions();
            $zcfCreateShortcode->zcfupdateState($value, $_REQUEST['formfieldsLength'], $_REQUEST['shortcodename']);
        } elseif ($_REQUEST['Action'] == 'zcfdeleteFieldsState') {
            echo $formfieldId = $_REQUEST['formfieldIds'];
            require_once(ZCF_BASE_DIR_URI . "includes/crmshortcodefunctions.php");
            $zcfCreateShortcode = new zcfManageShortcodesActions();
            $zcfCreateShortcode->zcfdeleteFieldsState($formfieldId);
        } else {
            require_once(ZCF_BASE_DIR_URI . "includes/crmshortcodefunctions.php");
            $zcfDeleteShortcode = new zcfManageShortcodesActions();
            $zcfDeleteShortcode->zcfDeleteShortcode($_REQUEST['shortcode']);
            $value = array();
        }
        $shortcodevalues = json_encode($value);
        print_r($shortcodevalues);
        die;
    }

    public static function zcfSaveCRMconfig() {
        require_once( ZCF_BASE_DIR_URI . "includes/zcfSaveCRMconfig.php" );
        die;
    }

    public static function zcfmainFormsActions() {
        require_once( ZCF_BASE_DIR_URI . "includes/crmcustomfunctions.php" );
        $adminObj = new zcf_AjaxActionsClass();
        $admin = $adminObj->zcfmainFormsActions();
        die;
    }

    public static function zcfcaptcha_info() {
        $final_captcha_array['recaptcha_public_key'] = sanitize_text_field($_REQUEST['recaptcha_public_key']);
        $final_captcha_array['recaptcha_private_key'] = sanitize_text_field($_REQUEST['recaptcha_private_key']);
        $final_captcha_array['crmforms_recaptcha'] = sanitize_text_field($_REQUEST['crmforms_recaptcha']);
        $final_captcha_array['email'] = sanitize_text_field($_REQUEST['email']);
        $final_captcha_array['emailcondition'] = sanitize_text_field($_REQUEST['emailcondition']);
        update_option("zcf_captcha_settings", $final_captcha_array);
        die;
    }

    public static function zcfmappingmoduleconf() {
        $map_module = $_REQUEST['postdata'];
        update_option('zohocrmbasemodule', $map_module);
        die;
    }

    public static function zcfsaveSyncValue() {
        $Sync_value = sanitize_text_field($_REQUEST['syncedvalue']);
        update_option('Sync_value_on_off', $Sync_value);
        die;
    }

    public static function zcfsend_mapping_configuration() {
        require_once( ZCF_BASE_DIR_URI . 'includes/crmcontactformfieldsmapping.php' );
        $module = sanitize_text_field($_REQUEST['thirdparty_module']);
        $thirdparty_form = sanitize_text_field($_REQUEST['thirdparty_plugin']);
        $mapping_ui_fields = new zcfcontactformfieldmapping();
        $mapping_ui_fields->zcfget_mapping_field_config($module, $thirdparty_form);
    }

    public static function zcf_get_contactform_fields() {
        require_once( ZCF_BASE_DIR_URI . 'includes/crmcontactformfieldsmapping.php' );
        $mapping_ui_fields = new zcfcontactformfieldmapping();
        $mapping_ui_fields->zcfget_contactform_fields();
    }

    public static function zcf_map_contactform_fields() {
        require_once( ZCF_BASE_DIR_URI . 'includes/crmcontactformfieldsmapping.php' );
        $mapping_ui_fields = new zcfcontactformfieldmapping();
        $mapping_ui_fields->zcfmaping_contactform_fields();
    }

    public static function zcf_save_contact_form_title() {
        $thirdparty_title_key = sanitize_text_field($_REQUEST['tp_title_key']);
        $thirdparty_title_value = sanitize_text_field($_REQUEST['tp_title_val']);
        update_option($thirdparty_title_key, $thirdparty_title_value);
        die;
    }

    public static function zcf_send_mapped_config() {
        require_once( ZCF_BASE_DIR_URI . 'includes/crmcontactformfieldsmapping.php' );
        $mapping_ui_fields = new zcfcontactformfieldmapping();
        $mapping_ui_fields->zcf_mapped_fields_config();
    }

    public static function zcf_delete_mapped_config() {
        require_once( ZCF_BASE_DIR_URI . 'includes/crmcontactformfieldsmapping.php' );
        $mapping_ui_fields = new zcfcontactformfieldmapping();
        $mapping_ui_fields->zcf_delete_mappedfields_config();
    }

    public static function zcf_save_usersync_option() {
        $usersync_RR_value = sanitize_text_field($_REQUEST['user_rr_val']);
        update_option('usersync_rr_value', $usersync_RR_value);
        die;
    }

    public static function zcf_change_menu_order($menu_order) {
        return array(
            'index.php',
            'edit.php',
            'edit.php?post_type=page',
            'upload.php',
            'zoho-crm-form-builder/index.php',
        );
    }

}
