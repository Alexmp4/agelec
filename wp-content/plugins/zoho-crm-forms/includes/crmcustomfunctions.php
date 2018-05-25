<?php

if (!defined('ABSPATH'))
    exit;

class zcfcustomfunctions {
    public function zcf_FetchedCrmModuleDetails() {
        global $wpdb;
        $HelperObj = new zcfmaincorehelpers();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = 'crmformswpbuilder';
        $SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
        $shortcodeObj = new zcffieldlistDatamanage();
        $leadsynced = $shortcodeObj->zcfFieldManager($activatedplugin, 'Leads');
        $users = get_option('crm_users');
        $usersynced = false;
        if(!empty($users['crmformswpbuilder'])){
            if (is_array($users['crmformswpbuilder']) && count($users['crmformswpbuilder']) > 0) {
                $usersynced = true;
             }
        }
        $content = "";
        $flag = true;
        if (!$leadsynced) {
            $content = __("Please configure your CRM in the CRM Configuration", "zoho-crm-form-builder");
        }
        $return_array = array('content' => "$content", 'status' => $flag);
        return $return_array;
    }

    function zcf_getRoundRobinUser($assignedto_old) {
        $crm_users_list = get_option('crm_users');
        $crmname = "crmformswpbuilder";
        $RR_users_list = $crm_users_list[$crmname];
        $RR_users_id = $RR_users_list['id'];

        foreach ($RR_users_id as $RR_key => $RR_val) {
            $i = $RR_key;
            if ($assignedto_old == $RR_val) {
                if (isset($RR_users_id[$i + 1])) {
                    $assignedto_new = $RR_users_id[$i + 1];
                } else {
                    $assignedto_new = $RR_users_id[0];
                }
            }

            $i++;
        }
        return $assignedto_new;
    }

    public function zcf_CreateFieldShortcode($zohocrmformname, $module) {
        global $zohocrmdetails;
        $module = $module;
        $moduleslug = rtrim(strtolower($module), "s");
        $tmp_option = "crmforms_crmformswpbuilder_{$moduleslug}_fields-tmp";
        if (!function_exists("zcfgenerateRandomStringActivate")) {

            function generateRandomString($length = 10) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, strlen($characters) - 1)];
                }
                return $randomString;
            }

        }
        $list_of_shorcodes = Array();
        $shortcode_present_flag = "No";
        $config_fields = get_option($tmp_option);
        $options = "zcf_crmfields_shortcodes";
        $config_contact_shortcodes = get_option($options);
        if (is_array($config_contact_shortcodes)) {
            foreach ($config_contact_shortcodes as $shortcode => $values) {
                $list_of_shorcodes[] = $shortcode;
            }
        }

        for ($notpresent = "no"; $notpresent == "no";) {
            $random_string = generateRandomString(5);
            if (in_array($random_string, $list_of_shorcodes)) {
                $shortcode_present_flag = 'Yes';
            }
            if ($shortcode_present_flag != 'yes') {
                $notpresent = 'yes';
            }
        }
        $options = $tmp_option;
        return $random_string;
    }

    public static function zcf_selectLayoutName() {
        global $wpdb;
        $modulemname = $_REQUEST['module'];
        echo "select distinct(Layout_Name),layoutId from zcf_zohocrmform_field_manager where  module_type='" . $modulemname . "'";
        $layoutarray = $wpdb->get_results("select distinct(Layout_Name),layoutId from zcf_zohocrmform_field_manager where  module_type='" . $modulemname . "'");
        print_r($layoutarray);
        $content = "<option value=''>Select Layout</option>";
        foreach ($layoutarray as $key => $value) {
            $content .= "<option  value='" . $value->layoutId . "'>" . $value->Layout_Name . "</option>";
        }
        echo $content;
    }

    public static function zcf_updateFormTitle() {
        global $wpdb;
        $shortcode = $_REQUEST['shortcode'];
        $formTitle = $_REQUEST['formvalue'];
        $wpdb->query("update zcf_zohoshortcode_manager set form_name = '{$formTitle}' where shortcode_name='{$shortcode}'");
        echo "success";
    }

    public static function zcf_FieldsAjaxAction() {
        require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
        zcfcheckAccessToken();
        $zohocrmformname = isset($_REQUEST['crmtype']) ? sanitize_text_field($_REQUEST['crmtype']) : "";
        $module = isset($_REQUEST['module']) ? sanitize_text_field($_REQUEST['module']) : "";
        $module_options = $module;
        $options = sanitize_text_field(isset($_REQUEST['option']));
        $onAction = sanitize_text_field(isset($_REQUEST['onAction']));
        $siteurl = site_url();
        $HelperObj = new zcfmaincorehelpers();
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = 'crmformswpbuilder';
        $content = '';
        require_once(ZCF_BASE_DIR_URI . "includes/crmwebformfieldsfuntions.php");
        $FunctionsObj = new zcfcoreGetFields();
        $tmp_option = "crmforms_{$activatedplugin}_{$moduleslug}_fields-tmp";
        if ($onAction == 'onEditShortCode')
            ;
        {
            $original_options = "crmforms_{$activatedplugin}_fields_shortcodes";
            $original_config_fields = get_option($original_options);
        }
        $SettingsConfig = get_option("zcf_{$activatedplugin}_settings");
        if ($onAction == 'onCreate') {
            $config_fields = get_option($options);
        } else {
            $config_fields = get_option($options);
        }
        $FieldCount = 0;
        if (isset($config_fields['fields'])) {
            $FieldCount = count($config_fields['fields']);
        }

        if (isset($config_fields)) {
            $error[0] = 'no fields';
        }
        switch ($_REQUEST['doaction']) {
            case "GetAssignedToUser":
                $Functions = new zcfcoreGetFields();
                $Functions->zcfgetUsersListHtml();
                break;
            case "CheckformExits":
                include(ZCF_BASE_DIR_URI . 'includes/crmshortcodefunctions.php');
                $fields = new zcfManageShortcodesActions();
                $all_fields = $fields->zcfCrmManageFieldsLists($_REQUEST['shortcode'], $_REQUEST['crmtype'], $_REQUEST['module'], $_REQUEST['bulkaction'], $_REQUEST['chkarray'], $_REQUEST['labelarray'], $_REQUEST['orderarray'], $_REQUEST['defaultvalue'], $_REQUEST['inputtype']);
                $moduleslug = rtrim(strtolower($module), "s");
                $config_fields = get_option("crmforms_crmformswpbuilder_{$moduleslug}_fields-tmp");
                if (!isset($config_fields['fields'][0]))
                    die("Not synced");
                else
                    die("Synced");
                break;
            case "GetTemporaryFields":
                $moduleslug = rtrim(strtolower($module), "s");
                $config_fields = get_option("crmforms_{$zohocrmformname}_{$moduleslug}_fields-tmp");
                if ($options != 'getSelectedModuleFields') {
                    include(ZCF_BASE_DIR_URI . 'includes/crm-fields-form.php');
                }
                break;
            case "FetcheditCrmFields":
                $moduleslug = rtrim(strtolower($module), "s");
                $config_fields = $FunctionsObj->zcfgetCrmFieldsList($module);
                $seq = 1;
                $field_details = $current_fields = $existing_fields = array();
                foreach ($config_fields['fields'] as $fkey => $fval) {
                    $field_details['name'] = $fval['name'];
                    $field_details['id'] = $fval['id'];
                    $field_details['label'] = $fval['label'];
                    $field_details['type'] = isset($fval['type']['name']) ? $fval['type']['name'] : "";
                    $field_details['field_values'] = null;
                    if (!empty($fval['type']['picklistValues'])) {
                        $field_details['field_values'] = serialize($fval['type']['picklistValues']);
                    }
                    $field_details['module'] = $module;
                    if (isset($fval['mandatory']) && $fval['mandatory'] == 2)
                        $field_details['mandatory'] = 1;
                    else
                        $field_details['mandatory'] = 0;
                    $field_details['crmtype'] = $zohocrmformname;
                    $field_details['sequence'] = $seq;
                    $field_details['layout_name'] = $fval['layout_name'];
                    $field_details['layoutId'] = $fval['layoutId'];
                    $field_details['data_type'] = $fval['data_type'];
                    $field_details['readonly'] = $fval['readonly'];
                    $field_details['viewcreate_type'] = $fval['viewcreate_type'];

                    $field_details['base_model'] = null;
                    if (isset($fval['base_model']))
                        $field_details['base_model'] = $fval['base_model'];
                    $seq++;

                    if ($field_details['label'] == 'Date of Birth') {
                        $field_details['type'] = 'date';
                    }
                    $DataObj = new zcffieldlistDatamanage();
                    $DataObj->zcffielddataedit($field_details, $module);
                    $DataObj->zcfupdateDataeditScodeFields($field_details, $module);
                    $current_fields[] = $field_details['name'];
                }

                global $wpdb;
                $get_existing_fields = $wpdb->get_results("select field_name from zcf_zohocrmform_field_manager where module_type ='" . $module . "'  and Layout_Name ='" . $field_details['layout_name'] . "'");
                foreach ($get_existing_fields as $ex_key => $ex_val) {
                    $existing_fields[] = $ex_val->field_name;
                }

                if (!empty($existing_fields)) {
                    $check_deleted_fields = array();
                    $check_deleted_fields = array_diff($existing_fields, $current_fields);
                    if (!empty($check_deleted_fields)) {
                        //Delete fields from table
                        $DataObj = new zcffieldlistDatamanage();
                        $DataObj->zcfDeleteFields($zohocrmformname, $module, $check_deleted_fields, $field_details['layout_name']);
                    }
                }

                //Update Current Fields
                $options = "crmforms_wpzohocrm_{$moduleslug}_fields-tmp";
                update_option($options, $config_fields);
                $options = "zcf_crmfields_shortcodes";
                $edit_config_fields = get_option($options);
                $edit_config_fields[sanitize_text_field($_REQUEST['shortcode'])] = $config_fields;
                update_option($options, $edit_config_fields);
                break;
            case "FetchcrmModules":
                require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
                zcfsynModules();
                break;
            case "FetchCrmFields":

                $moduleslug = rtrim(strtolower($module), "s");
                $config_fields = $FunctionsObj->zcfgetCrmFieldsList($module);
                
                $seq = 1;
                $field_details = $current_fields = $existing_fields = array();
                foreach ($config_fields['fields'] as $fkey => $fval) {
                    $field_details['name'] = $fval['name'];
                    $field_details['id'] = $fval['id'];
                    $field_details['label'] = $fval['label'];
                    $field_details['type'] = isset($fval['type']['name']) ? $fval['type']['name'] : "";
                    $field_details['field_values'] = null;
                    if (!empty($fval['type']['picklistValues'])) {
                        $field_details['field_values'] = serialize($fval['type']['picklistValues']);
                    }
                    $field_details['module'] = $module;
                    if (isset($fval['mandatory']) && $fval['mandatory'] == 2)
                        $field_details['mandatory'] = 1;
                    else
                        $field_details['mandatory'] = 0;
                    $field_details['crmtype'] = $zohocrmformname;
                    $field_details['sequence'] = $seq;
                    $field_details['layout_name'] = $fval['layout_name'];
                    $field_details['layoutId'] = $fval['layoutId'];
                    $field_details['data_type'] = $fval['data_type'];
                    $field_details['readonly'] = $fval['readonly'];
                    $field_details['viewcreate_type'] = $fval['viewcreate_type'];
                    $field_details['base_model'] = null;
                    if (isset($fval['base_model']))
                        $field_details['base_model'] = $fval['base_model'];
                    $seq++;

                    if ($field_details['label'] == 'Date of Birth') {
                        $field_details['type'] = 'date';
                    }
                    $DataObj = new zcffieldlistDatamanage();
                    $DataObj->zcffieldData($field_details, $module);
                    $DataObj->zcfupdateScodeFields($field_details, $module);
                    $current_fields[] = $field_details['name'];
                }


                global $wpdb;
                $get_existing_fields = $wpdb->get_results($wpdb->prepare("select field_name from zcf_zohocrmform_field_manager where module_type =%s and crm_type =%s and Layout_Name =%s", $module, $zohocrmformname, $field_details['layout_name']));
                foreach ($get_existing_fields as $ex_key => $ex_val) {
                    $existing_fields[] = $ex_val->field_name;
                }

                if (!empty($existing_fields)) {
                    $check_deleted_fields = array();
                    $check_deleted_fields = array_diff($existing_fields, $current_fields);
                    if (!empty($check_deleted_fields)) {
                        //Delete fields from table
                        $DataObj = new zcffieldlistDatamanage();
                        $DataObj->zcfDeleteFields($zohocrmformname, $module, $check_deleted_fields, $field_details['layout_name']);
                    }
                }

                $options = "crmforms_{$zohocrmformname}_{$moduleslug}_fields-tmp";
                update_option($options, $config_fields);
                $options = "zcf_crmfields_shortcodes";
                $edit_config_fields = get_option($options);
                $edit_config_fields[sanitize_text_field($_REQUEST['shortcode'])] = $config_fields;
                update_option($options, $edit_config_fields);
                break;
            case "FetchAssignedUsers":
                $HelperObj = new zcfmaincorehelpers();
                $module = $HelperObj->Module;
                $moduleslug = $HelperObj->ModuleSlug;
                $activatedplugin = "crmformswpbuilder";
                $FunctionsObj = new zcfcoreGetFields();
                $crmusers = get_option('crm_users');
                $users = $FunctionsObj->zcfgetUsersList();
                $crmusers[$activatedplugin] = $users;
                update_option('crm_users', $crmusers);
                $content .= '<h5>Assigned Users:</h5>';
                $firstname = '';
                foreach ($users['first_name'] as $assignusers) {
                    $firstname .= $assignusers . "<br>";
                }
                echo $content;
                echo $firstname;
                die;
                break;
            default:
                break;
        }
    }

    public function zcf_updateform_title($shortcode, $tp_title, $tp_formtype) {
        global $wpdb;
        switch ($tp_formtype) {

            case 'contactform':
                $get_checkid = $wpdb->get_results("select thirdpartyid from zcf_contactformrelation where  shortcode='{$shortcode}' and thirdparty='contactform'");
                if (isset($get_checkid[0])) {
                    $checkid = $get_checkid[0]->thirdpartyid;
                } else {
                    $checkid = "";
                }
                if (!empty($checkid)) {
                    $wpdb->update($wpdb->posts, array('post_title' => $tp_title), array('ID' => $checkid));
                }

                break;
        }
        return;
    }

    public function zcf_NormalFieldAjaxAction() {
        global $wpdb, $adminmenulable;
        $HelperObj = new zcfmaincorehelpers();

        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = "crmformswpbuilder";
        $SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
        $shortcodeObj = new zcffieldlistDatamanage();
        switch ($_REQUEST['doaction']) {
            case "SaveFormSettings":
                $shortcode_name = sanitize_text_field($_REQUEST['shortcode']);
                $thirdparty_title = sanitize_text_field($_REQUEST['thirdparty_title']);
                $thirdparty_form_type = sanitize_text_field($_REQUEST['thirdparty_form_type']);
                if ($thirdparty_form_type != 'none') {
                    update_option($shortcode_name, $thirdparty_title);
                    update_option('Thirdparty_' . $shortcode_name, $thirdparty_form_type);
                }
                if ($thirdparty_title != "") {
                    $this->zcf_updateform_title($shortcode_name, $thirdparty_title, $thirdparty_form_type);
                }
                $shortcodedata['module'] = $module;
                $shortcodedata['crm_type'] = $activatedplugin;
                $shortcodedata['name'] = $shortcode_name;
                $shortcodedata['type'] = "post";
                $shortcodedata['assignto'] = sanitize_text_field($_REQUEST['assignedto']);
                $shortcodedata['errormesg'] = sanitize_text_field($_REQUEST['errormessage']);
                $shortcodedata['successmesg'] = sanitize_text_field($_REQUEST['successmessage']);
                $shortcodedata['duplicate_handling'] = sanitize_text_field($_REQUEST['duplicate_handling']);
                $shortcodedata['assignmentrule_ID'] = sanitize_text_field($_REQUEST['assignmentrule_ID']);
                $shortcodedata['assignmentrule_enable'] = sanitize_text_field($_REQUEST['assignmentrule_enable']);
                if (sanitize_text_field($_REQUEST['enableurlredirection']) == "true") {
                    $shortcodedata['isredirection'] = 1;
                } else {
                    $shortcodedata['isredirection'] = 0;
                }
                $shortcodedata['urlredirection'] = sanitize_text_field($_REQUEST['redirecturl']);
                if (sanitize_text_field($_REQUEST['enablecaptcha']) == "true") {
                    $shortcodedata['captcha'] = 1;
                } else {
                    $shortcodedata['captcha'] = 0;
                }
                if (sanitize_text_field($_REQUEST['customthirdpartyplugin']) == "true") {
                    $shortcodedata['customthirdpartyplugin'] = 1;
                } else {
                    $shortcodedata['customthirdpartyplugin'] = 0;
                }
                $shortcodeObj->zcfformScodelists($shortcodedata, "edit");
                break;

            case "CaptureAllWpUsers" :
                $config_user_capture = get_option("crmforms_{$activatedplugin}_user_capture_settings");
                $module = $config_user_capture['user_sync_module'];
                if ($module == "Leads" || $module == "Contacts") {
                    $rr_module = 'leads';
                }
                $zcf_start = $_POST['wp_start'];
                $zcf_offset = $_POST['wp_offset'];

                $users_synced_count = $_POST['synced_count'];
                $fetch_last_id = $wpdb->get_results("select ID from {$wpdb->prefix}users order by id desc limit 1");
                $last_user_id = $fetch_last_id[0]->ID;
                $zcf_users_count = count(get_users());
                $duplicate_cancelled = 0;
                $duplicate_inserted = 0;
                $duplicate_updated = 0;
                $successful = 0;
                $failed = 0;
                $url = isset($SettingsConfig['url']) ? $SettingsConfig['url'] : "";
                $username = $SettingsConfig['username'];
                $accesskey = isset($SettingsConfig['accesskey']) ? $SettingsConfig['accesskey'] : $SettingsConfig['password'];
                $zcf_active_crm = 'crmformswpbuilder';
                $FunctionsObj = new zcfcoreGetFields();
                global $wpdb;
                $blogusers = $wpdb->get_results("select ID from " . $wpdb->prefix . "users limit $zcf_start, $zcf_offset");
                $user = array();
                foreach ($blogusers as $users) {
                    $user[] = $users->ID;
                }
                $users_within_limit = count($user);
                if (!empty($user)) {
                    foreach ($user as $user_id) {
                        $zcf_active_crm = 'crmformswpbuilder';
                        $zcf_assigneduser_config = get_option("crmforms_{$zcf_active_crm}_usersync_assignedto_settings");
                        $zcf_usersync_assignedto = $zcf_assigneduser_config['usersync_assign_leads'];
                        //Code For RR
                        $assignedto_old = $zcf_assigneduser_config['usersync_rr_value'];
                        if (empty($assignedto_old)) {
                            $get_first_usersync_owner = new zcfcoreGetFields();
                            $get_first_user = $get_first_usersync_owner->zcfgetUsersList();
                            $assignedto_old = $get_first_user['id'][0];
                            $zcf_assigneduser_config['usersync_rr_value'] = $assignedto_old;
                            update_option("crmforms_{$zcf_active_crm}_usersync_assignedto_settings", $zcf_assigneduser_config);
                        }
                        $email_present = "no";

                        $posts = new zcf_CapturingClassAjax();
                        $post = $posts->zcf_mapUserCaptureFields($module, $user_id, $assignedto_old);
                        $user_email = "";
                        $CheckEmailResult = array();
                        $duplicate_option_check = $config_user_capture['crmforms_capture_duplicates'];
                       
                            $result_id = $FunctionsObj->result_ids;
                            $result_emails = $FunctionsObj->result_emails;
                            $record = $FunctionsObj->zcfcreatenewRecord($module, $post);
                                if ($record['result'] == "success") {
                                    $data = "/$module entry is added./";
                                    if ($zcf_usersync_assignedto == 'Round Robin') {
                                        $new_assigned_val = self::zcf_getRoundRobinUser($assignedto_old);
                                        $zcf_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
                                        update_option("crmforms_{$zcf_active_crm}_usersync_assignedto_settings", $zcf_assigneduser_config);
                                    }
                                }
                        
                        if (isset($data) && $data) {
                            if (preg_match("/$module entry is added./", $data)) {
                                if (!empty($user_email)) {
                                    if ((in_array($user_email, $FunctionsObj->result_emails)) && ($config_user_capture['crmforms_capture_duplicates'] != 'on' )) {
                                        $duplicate_inserted++;
                                    }
                                    $successful++;
                                }
                            } else {
                                $failed++;
                            }
                        }
                    }
                }
                $users_synced_count = $users_synced_count + $zcf_offset;
                $zcf_start = $zcf_offset + $zcf_start;
                $user_sync_array['start'] = $zcf_start;
                $user_sync_array['offset'] = $zcf_offset;
                $user_sync_array['total_count'] = $zcf_users_count;
                $user_sync_array['last_user_id'] = $last_user_id;
                $user_sync_array['users_within_limit'] = $users_within_limit;
                $user_sync_array['synced_count'] = $users_synced_count;
                $user_sync_array['duplicate_option'] = $config_user_capture['crmforms_capture_duplicates'];
                $sync_array = json_encode($user_sync_array);
                die;
                break;
        }
    }

}

class zcf_AjaxActionsClass {

    public static function zcfmainFormsActions() {
        $OverallFunctionObj = new zcfcustomfunctions();
        if (isset($_REQUEST['operation']) && (sanitize_text_field($_REQUEST['operation']) == "NoFieldOperation")) {
            $OverallFunctionObj->zcf_NormalFieldAjaxAction();
        } else {
            $OverallFunctionObj->zcf_FieldsAjaxAction();
        }
        die;
    }

}

add_action('wp_ajax_mainActionscrmForms', array("zcf_AjaxActionsClass", 'zcfmainFormsActions'));

class zcf_ajaxActionModuleList {

    public static function zcf_getModuleLayoutlist() {
        $OverallFunctionObj = new zcfcustomfunctions();
        $OverallFunctionObj->zcf_selectLayoutName();
        die;
    }

}

class zcf_ajaxzcf_updateTitles {

    public static function zcf_updateTitles() {
        $OverallFunctionObj = new zcfcustomfunctions();
        $OverallFunctionObj->zcf_updateFormTitle();
        die;
    }

}

add_action('wp_ajax_zcf_updateTitles', array("zcf_ajaxzcf_updateTitles", 'zcf_updateTitles'));


add_action('wp_ajax_zcf_getModuleLayoutlist', array("zcf_ajaxActionModuleList", 'zcf_getModuleLayoutlist'));

class zcf_CapturingClassAjax {

    public function zcf_mapUserCaptureFields($module, $user_id, $assignedto_old) {
        
    }

    public static function zcf_get_usersync_assignedto($assignedto_old) {
        $zcf_active_crm = 'crmformswpbuilder';
        $usersync_config = get_option("zcf_user_capture_settings");
        $module = $usersync_config['user_sync_module'];
        $zcf_assigneduser_config = get_option("zcf_usersync_assignedto_settings");
        $assignedto_user = array();
        if ($zcf_assigneduser_config['usersync_assign_leads'] != "Round Robin") {
            $assignedto_user['SMOWNERID'] = $zcf_assigneduser_config['usersync_assign_leads'];
        } else {
            $assignedto_user['SMOWNERID'] = $assignedto_old;
        }
        return $assignedto_user;
    }

    function zcf_getRoundRobinUser($assignedto_old) {
        $crm_users_list = get_option('crm_users');
        $crmname = get_option('ZCFFormBuilderPluginActivated');
        $RR_users_list = $crm_users_list[$crmname];
        $RR_users_id = $RR_users_list['id'];

        foreach ($RR_users_id as $RR_key => $RR_val) {
            $i = $RR_key;
            if ($assignedto_old == $RR_val) {
                if (isset($RR_users_id[$i + 1])) {
                    $assignedto_new = $RR_users_id[$i + 1];
                } else {
                    $assignedto_new = $RR_users_id[0];
                }
            }

            $i++;
        }
        return $assignedto_new;
    }

    function zcf_CaptureFormFieldsList($globalvariables) {
        global $wpdb;
        $HelperObj = new zcfmaincorehelpers();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = "crmformswpbuilder";
        $duplicate_inserted = $duplicate_cancelled = $duplicate_updated = 0;
        $module = $globalvariables['formattr']['module'];
        $post = $globalvariables['post'];
        $FunctionsObj = new zcfcoreGetFields();
        $emailfield = $FunctionsObj->zcfduplicateCheckEmailField();
        $shortcode_name = $globalvariables['attrname'];
        $layoutarray = $wpdb->get_results($wpdb->prepare("select * from zcf_zohoshortcode_manager where shortcode_name =%s", $shortcode_name));
        $enable_round_robin = $wpdb->get_var($wpdb->prepare("select assigned_to from zcf_zohoshortcode_manager where shortcode_name =%s", $shortcode_name));
        if ($enable_round_robin == 'Round Robin') {
            $assignedto_old = $wpdb->get_var($wpdb->prepare("select Round_Robin from zcf_zohoshortcode_manager where shortcode_name =%s", $shortcode_name));
        }

        if (is_array($post)) {

            foreach ($post as $key => $value) {
                if (($key != 'moduleName') && ($key != 'submitcontactform') && ($key != 'submitcontactformwidget') && ($key != '') && ($key != 'submit')) {
                    $module_fields[$key] = $value;
                    if ($key == $emailfield) {
                        $email_field_present = "yes";
                        $user_email = $value;
                    }
                }
            }
        }
        foreach ($_REQUEST as $key => $value) {
            $module_fields[$key] = $value;
        }
        $module_fields['layoutId'] = $layoutarray[0]->layoutId;
        $module_fields['larId'] = $layoutarray[0]->assignmentrule_ID;

        if ($enable_round_robin != 'Round Robin') {
            $module_fields[$FunctionsObj->zcfassignedToFieldId()] = $globalvariables['assignedto'];
        } else {
            $module_fields[$FunctionsObj->zcfassignedToFieldId()] = $assignedto_old;
        }
        unset($module_fields['formnumber']);
        unset($module_fields['IsUnreadByOwner']);

        //Check both module and Skip
        $duplicate_option_check = $globalvariables['formattr']['duplicate_handling'];
        $CheckEmailResult = $FunctionsObj->zcfcheckEmailPresent($module, $post[$emailfield]);
            $result_id = $FunctionsObj->result_ids;
            $result_emails = $FunctionsObj->result_emails;
            $record = $FunctionsObj->zcfcreatenewRecord($module, $module_fields);
                $data = "failure";
                if ($record['result'] == "success") {
                    $duplicate_inserted++;
                    $data = "/$module entry is added./";

                    if ($enable_round_robin == 'Round Robin') {
                        $new_assigned_val = self::zcf_getRoundRobinUser($assignedto_old);
                        $wpdb->update('zcf_zohoshortcode_manager', array('Round_Robin' => $new_assigned_val), array('shortcode_name' => $shortcode_name));
                    }
              }
        
        return $data;
    }

    public static function zcf_contactform_mapped_submission($posted_array) {
        $tp_module = $posted_array['third_module'];
        $tp_active_crm = $posted_array['thirdparty_crm'];
        $tp_plugin_name = $posted_array['third_plugin'];
        $tp_form_title = $posted_array['form_title'];
        $tp_shortcode = $posted_array['shortcode'];
        $duplicate_option = $posted_array['duplicate_option'];
        $layoutId = $posted_array['layoutId'];

        //Code For RR
        $get_existing_option = get_option($tp_shortcode);
        $tp_assignedto = $get_existing_option['thirdparty_assignedto'];
        $assignedto_old = $get_existing_option['tp_roundrobin'];
        $zcf_active_crm = 'crmformswpbuilder';

        if (empty($assignedto_old)) {
            $get_first_RR_owner = new zcfcoreGetFields();
            $get_first_user = $get_first_RR_owner->zcfgetUsersList();
            $assignedto_old = $get_first_user['id'][0];
            $get_existing_option['tp_roundrobin'] = $assignedto_old;
            update_option($tp_shortcode, $get_existing_option);
        }

        //END RR


        if (isset($tp_module)) {
            $module = $tp_module;
            $duplicate_cancelled = 0;
            $duplicate_inserted = 0;
            $duplicate_updated = 0;
            $successful = 0;
            $failed = 0;
            $FunctionsObj = new zcfcoreGetFields();
            $post = $posted_array['posted'];

            $Assigned_user = zcf_CapturingClassAjax::zcf_get_mapping_assignedto($tp_shortcode, $assignedto_old);
            $Assigned_user_value = array_values($Assigned_user);
            if ($Assigned_user_value[0] != "--Select--") {
                $post = array_merge($post, $Assigned_user);
            } else {
                $assign_user_key = array_keys($Assigned_user);
                $get_crm_users = get_option('crm_users');
                $crmuserid = $get_crm_users['crmformswpbuilder']['id'][0];
                $assign_user = array();
                $assign_user[$assign_user_key[0]] = $crmuserid;
                $post = array_merge($post, $assign_user);
            }
            $user_email = "";
            $CheckEmailResult = array();
            $post['layoutId'] = $layoutId;
            if (isset($post[$FunctionsObj->zcfduplicateCheckEmailField()])) {
                if ($duplicate_option == 'skip_both') {
                    $CheckEmailResult_Leads = $FunctionsObj->zcfcheckEmailPresent('Leads', $post[$FunctionsObj->zcfduplicateCheckEmailField()]);
                    $CheckEmailResult_Contacts = $FunctionsObj->zcfcheckEmailPresent('Contacts', $post[$FunctionsObj->zcfduplicateCheckEmailField()]);

                    if ($CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1) {
                        $CheckEmailResult = 1;
                    }
                } else {
                    $CheckEmailResult = $FunctionsObj->zcfcheckEmailPresent($module, $post[$FunctionsObj->zcfduplicateCheckEmailField()]);
                }
                $user_email = $post[$FunctionsObj->zcfduplicateCheckEmailField()];
            }

            $record = $FunctionsObj->zcfcreatenewRecord($module, $post);
                    if ($record['result'] == "success") {
                        if ($tp_assignedto == 'Round Robin') {
                            $new_assigned_val = self::zcf_getRoundRobinUser($assignedto_old);
                            $get_existing_option['tp_roundrobin'] = $new_assigned_val;
                            update_option($tp_shortcode, $get_existing_option);
                        }
                    }
        }
    }

    public function zcf_get_mapping_assignedto($shortcode, $assignedto_old) {
        $zcf_active_crm = 'crmformswpbuilder';
        $zcf_assigneduser_config = get_option($shortcode);
        $module = $zcf_assigneduser_config['third_module'];
        $tp_assignedto = $zcf_assigneduser_config['thirdparty_assignedto'];
        $assignedto_user = array();
        if ($tp_assignedto != 'Round Robin') {
            $assignedto_user['SMOWNERID'] = $zcf_assigneduser_config['thirdparty_assignedto'];
        } else {
            $assignedto_user['SMOWNERID'] = $assignedto_old;
        }
        return $assignedto_user;
    }

    public static function zcf_capture_registering_users($user_id) {
        $posted_custom_fields = $_POST;
        $HelperObj = new zcfmaincorehelpers();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = "crmformswpbuilder";
        $config_user_capture = get_option("zcf_user_capture_settings");

        $zcf_active_crm = 'crmformswpbuilder';
        $zcf_assigneduser_config = get_option("zcf_usersync_assignedto_settings");
        $zcf_usersync_assignedto = $zcf_assigneduser_config['usersync_assign_leads'];

        $assignedto_old = $zcf_assigneduser_config['usersync_rr_value'];
        if (empty($assignedto_old)) {
            $get_first_usersync_owner = new zcfcoreGetFields();
            $get_first_user = $get_first_usersync_owner->zcfgetUsersList();
            $assignedto_old = $get_first_user['id'][0];
            $zcf_assigneduser_config['usersync_rr_value'] = $assignedto_old;
            update_option("crmforms_{$zcf_active_crm}_usersync_assignedto_settings", $zcf_assigneduser_config);
        }



        if (isset($config_user_capture['user_sync_module'])) {
            $module = $config_user_capture['user_sync_module'];
            $duplicate_cancelled = 0;
            $duplicate_inserted = 0;
            $duplicate_updated = 0;
            $successful = 0;
            $failed = 0;
            $FunctionsObj = new zcfcoreGetFields();
            $post = zcf_CapturingClassAjax::mapRegisterUser($module, $user_id, $posted_custom_fields, $assignedto_old);
            $user_email = "";
            $CheckEmailResult = array();
            $duplicate_option_check = $config_user_capture['crmforms_capture_duplicates'];
            if (isset($post[$FunctionsObj->zcfduplicateCheckEmailField()])) {
                $CheckEmailResult = $FunctionsObj->zcfcheckEmailPresent($module, $post[$FunctionsObj->zcfduplicateCheckEmailField()]);
                $user_email = $post[$FunctionsObj->zcfduplicateCheckEmailField()];
            }

            $record = $FunctionsObj->zcfcreatenewRecord($module, $post);
                    if ($record['result'] == "success") {
                        $data = "/$module entry is added./";
                        if ($zcf_usersync_assignedto == 'Round Robin') {
                            $new_assigned_val = self::zcf_getRoundRobinUser($assignedto_old);
                            $zcf_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
                            update_option("crmforms_{$zcf_active_crm}_usersync_assignedto_settings", $zcf_assigneduser_config);
                        }
                    }
            }
        
    }

    public static function zcf_capture_updating_users($user_id) {
        $HelperObj = new zcfmaincorehelpers();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = "crmformswpbuilder";
        $config_user_capture = get_option("zcf_user_capture_settings");
        $custom_plugin = get_option('custom_plugin');

        $zcf_active_crm = 'crmformswpbuilder';
        $zcf_assigneduser_config = get_option("zcf_usersync_assignedto_settings");
        $zcf_usersync_assignedto = $zcf_assigneduser_config['usersync_assign_leads'];

        $assignedto_old = $zcf_assigneduser_config['usersync_rr_value'];
        if (empty($assignedto_old)) {
            $get_first_usersync_owner = new zcfcoreGetFields();
            $get_first_user = $get_first_usersync_owner->zcfgetUsersList();
            $assignedto_old = $get_first_user['id'][0];
            $zcf_assigneduser_config['usersync_rr_value'] = $assignedto_old;
            update_option("zcf_usersync_assignedto_settings", $zcf_assigneduser_config);
        }

        if (isset($config_user_capture['user_sync_module'])) {
            $module = $config_user_capture['user_sync_module'];
            $duplicate_cancelled = 0;
            $duplicate_inserted = 0;
            $duplicate_updated = 0;
            $successful = 0;
            $failed = 0;
            $FunctionsObj = new zcfcoreGetFields();
            $post = zcf_CapturingClassAjax::zcf_mapUserCaptureFields($module, $user_id, $assignedto_old);
            $user_email = "";
            $CheckEmailResult = array();
            $duplicate_option_check = $config_user_capture['crmforms_capture_duplicates'];
            if (isset($post[$FunctionsObj->zcfduplicateCheckEmailField()])) {
                if ($duplicate_option_check == 'skip_both') {
                    $CheckEmailResult_Leads = $FunctionsObj->zcfcheckEmailPresent('Leads', $post[$FunctionsObj->zcfduplicateCheckEmailField()]);
                    $CheckEmailResult_Contacts = $FunctionsObj->zcfcheckEmailPresent('Contacts', $post[$FunctionsObj->zcfduplicateCheckEmailField()]);

                    if ($CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1) {
                        $CheckEmailResult = 1;
                    }
                } else {
                    $CheckEmailResult = $FunctionsObj->zcfcheckEmailPresent($module, $post[$FunctionsObj->zcfduplicateCheckEmailField()]);
                }
                $user_email = $post[$FunctionsObj->zcfduplicateCheckEmailField()];
            }

            $record = $FunctionsObj->zcfcreatenewRecord($module, $post);
                    if ($record['result'] == "success") {
                        $data = "/$module entry is added./";
                        if ($zcf_usersync_assignedto == 'Round Robin') {
                            $new_assigned_val = self::zcf_getRoundRobinUser($assignedto_old);
                            $zcf_assigneduser_config['usersync_rr_value'] = $new_assigned_val;
                            update_option("zcf_usersync_assignedto_settings", $zcf_assigneduser_config);
                        }
                    }
        }
    }

}
