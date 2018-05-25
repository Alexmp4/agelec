<?php

if (!defined('ABSPATH'))
    exit;
require_once('crmcontactformgenerator.php');
add_action('wpcf7_before_send_mail', 'zcfcontact_forms_example');

function zcfreplace_key_function($all_fields, $key1, $key2) {
    $keys = array_keys($all_fields);
    $index = array_search($key1, $keys);
    if ($index !== false) {
        $keys[$index] = $key2;
        $all_fields = array_combine($keys, $all_fields);
    }
    return $all_fields;
}

function zcfgetTBetBrackets($post_content) {
    $data_type_array = array('text', 'email', 'date', 'checkbox', 'select', 'url', 'number', 'textarea', 'radio', 'quiz', 'file', 'acceptance', 'hidden', 'tel', 'dynamichidden');
    $contact_labels = array();
    foreach ($data_type_array as $dt_key => $dt_val) {
        $patternn = "(\[$dt_val(\s|\*\s)(.*)\])";
        preg_match_all($patternn, $post_content, $matches);
        if (!empty($matches[1])) {
            $contact_labels[] = $matches[0];
        }
        $i = 0;
        $merge_array = array();
        foreach ($contact_labels as $cf7key => $cf7value) {
            foreach ($cf7value as $cf_get_key => $cf_get_fields) {
                $merge_array[] = $cf_get_fields;
            }
        }
    }
    return $merge_array;
}
function zcfcontact_forms_example() {

    global $wpdb, $HelperObj;
    $post_id = intval($_POST['_wpcf7']);
    $thirdparty = 'contactform';
    $crmname = 'crmformswpbuilder';
    $get_contact_option = $crmname . '_zcf_contact' . $post_id;
    $noe = '';
    $check_map_exist = get_option($get_contact_option);
    if (!empty($check_map_exist)) {
        $all_fields = $_POST;
        $submission = WPCF7_Submission::get_instance();
        $attachments = $submission->uploaded_files();
        foreach ($all_fields as $key => $value) {
            if (preg_match('/^_wp/', $key))
                unset($all_fields[$key]);
        }
        foreach ($all_fields as $cfkey => $cfvalue) {
            if ($cfkey == 'g-recaptcha-response') {
                if (empty($cfvalue)) {
                    die;
                }
            }
        }
        $mapped_array = $check_map_exist['fields'];
        $mapped_array_key_labels = array_keys($mapped_array);
        $get_json_array = $wpdb->get_results($wpdb->prepare("select ID,post_content from $wpdb->posts where ID=%d", $post_id));
        $contact_post_content = $get_json_array[0]->post_content;
        $fields = zcfgetTBetBrackets($contact_post_content);
        $i = 0;
        foreach ($fields as $cfkey => $cfval) {
            if (preg_match('/\s/', $cfval)) {
                $final_arr = explode(' ', $cfval);
                $contact_form_labels[$i] = rtrim($final_arr[1], ']');
                $i++;
            }
        }

        //get mapped label keys from gravity array
        foreach ($contact_form_labels as $cf_key => $cf_val) {
            foreach ($mapped_array_key_labels as $labels) {
                if ($labels == $cf_val && $labels != 'gclid_value') {
                    $field_name = $mapped_array[$labels];
                    $user_value = $all_fields[$labels];
                    $data_array[$field_name] = $user_value;
                }
                if ($labels == 'gclid_value') {
                    $field_name = $mapped_array[$labels];
                    $user_value = $_COOKIE['gclid'];
                    $data_array[$field_name] = $user_value;
                }
            }
        }

        $activatedPlugin = $check_map_exist['thirdparty_crm'];
        if (!empty($data_array)) {
            foreach ($data_array as $key => $value) {
                if ($key == '') {
                    $noe = $key;
                }
                if (is_array($data_array[$key])) {
                    $data_array[$key] = 'true';
                    break;
                }
            }
            unset($data_array[$noe]);
        }

        if ($attachments) {
            $data_array['attachments'] = $attachments;
        }
        $ArraytoApi['posted'] = $data_array;
        $ArraytoApi['third_module'] = $check_map_exist['third_module'];
        $ArraytoApi['thirdparty_crm'] = $check_map_exist['thirdparty_crm'];
        $ArraytoApi['third_plugin'] = $check_map_exist['third_plugin'];
        $ArraytoApi['form_title'] = $check_map_exist['form_title'];
        $ArraytoApi['shortcode'] = $get_contact_option;
        $ArraytoApi['duplicate_option'] = $check_map_exist['thirdparty_duplicate'];
        $ArraytoApi['layoutId'] = $check_map_exist['layoutId'];
        $capture_obj = new zcf_CapturingClassAjax();
        $capture_obj->zcf_contactform_mapped_submission($ArraytoApi);
    } else {
        $crmforms_shortcode = $wpdb->get_var($wpdb->prepare("select shortcode from zcf_contactformrelation where thirdpartyid =%d and thirdparty=%s ", $post_id, $thirdparty));
        $code['name'] = $crmforms_shortcode;
        $submission = WPCF7_Submission::get_instance();
        $attachments = $submission->uploaded_files();
        $newform = new zcffieldlistDatamanage();
        $newshortcode = $newform->zcfformfieldsPropsettings($code['name']);
        $FormSettings = $newform->zcfFormPropSettings($code['name']);
        $module = $FormSettings->module;
        $activatedPlugin = "crmformswpbuilder";
        $all_fields = $_POST;
        foreach ($all_fields as $key => $value) {
            if (preg_match('/^_wp/', $key))
                unset($all_fields[$key]);
        }
        foreach ($all_fields as $cfkey => $cfvalue) {
            if ($cfkey == 'g-recaptcha-response') {
                if (empty($cfvalue)) {
                    die;
                }
            }
        }
        $mapping = $wpdb->get_results($wpdb->prepare("select crmformsfieldslable,thirdpartyfieldids from zcf_contactformrelation where thirdpartyformid=%d", $post_id), ARRAY_A);
        foreach ($mapping as $key => $value) {
            $crmformsfieldslable[$key] = $value['crmformsfieldslable'];
            $thirdpartyfieldids[$key] = $value['thirdpartyfieldids'];
        }
        $crmformsfieldName = $wpdb->get_results(" select a.field_name , a.field_values , a.field_type from zcf_zohocrmform_field_manager as a join zcf_zohocrm_formfield_manager as b join zcf_contactformrelation as c where b.field_id=a.field_id and c.crmformsfieldid=b.rel_id and thirdpartyformid='{$post_id}'", ARRAY_A);
      
        $thirdpartyfieldids = array_flip($thirdpartyfieldids);

        foreach ($thirdpartyfieldids as $key => $value) {
            $OriginalMap[$key] = $crmformsfieldname[$value];
        }
        
        if (is_array($all_fields)) { 
            foreach ($all_fields as $field_id => $user_value) {
                if (isset($OriginalMap[$field_id]))
                    $ArraytoApi[$OriginalMap[$field_id]] = $user_value;
            }
            $code['name'] = $crmforms_shortcode;
            $newform = new zcffieldlistDatamanage();
            $newshortcode = $newform->zcfformfieldsPropsettings($code['name']);
            $FormSettings = $newform->zcfFormPropSettings($code['name']);
            $module = $FormSettings->module; //$shortcodes[$attr['name']]['module'];
            $ArraytoApi['moduleName'] = $module;
            $ArraytoApi['formnumber'] = $post_id;
            $ArraytoApi['submit'] = 'Submit';
            $activatedPlugin = "wpzohoWP";
            if (!empty($ArraytoApi)) {
                foreach ($ArraytoApi as $key => $value) {
                    if ($key == '') {
                        $noe = $key;
                    }
                    if (is_array($ArraytoApi[$key])) {
                        $ArraytoApi[$key] = 'true';
                    }
                }
                unset($ArraytoApi[$noe]);
            }
            if ($attachments) {
                $ArraytoApi['attachments'] = $attachments;
            }
            global $_POST;
            $_POST = array();
            $_POST = $ArraytoApi;
            zcf_ContactFormFieldsGenerator($code, 'thirdparty');
            zcf_callcontactform7mapping('post');
            return true;
        }
    }
}
?>
