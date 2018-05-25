<?php
if (!defined('ABSPATH'))
    exit; 

if (!function_exists("zcf_recaptcha_get_html"))
    ;
{
    require_once(ZCF_BASE_DIR_URI . "captcha/recaptchalib.php");
}
if (isset($_SESSION['generated_forms'])) {
    unset($_SESSION['generated_forms']);
}
global $HelperObj;
require_once(ZCF_BASE_DIR_URI . "includes/crmwebformshelper.php");
$HelperObj = new zcfmaincorehelpers;
$activatedplugin = "crmformswpbuilder";
add_filter('widget_text', 'do_shortcode');
add_shortcode("zohocrm-web-form", 'zcf_ContactFormFieldsGenerator');
global $migrationmap;
$migrationmap = get_option("zcf_crm_oldversion_shortcodes");
if (is_array($migrationmap)) {
    foreach ($migrationmap as $key => $value) {
        add_shortcode($key, "zcf_forms_mig_listoffields");
    }
}

function zcf_forms_mig_listoffields($attr, $htmlcontent, $tag) {
    global $migrationmap;
    $migrate = $migrationmap[$tag];
    foreach ($migrate as $key => $value) {
        if (!isset($attr['name'])) {
            $name = $value['newrandomname'];
        } else {

            if ($value['oldrandomname'] == $attr['name']) {
                $name = $value['newrandomname'];
            }
        }
    }
    return zcf_ContactFormFieldsGenerator(array('name' => $name));
}

global $plugin_dir, $plugin_url;
$plugin_dir = ZCF_BASE_DIR_URI;
$plugin_url = ZCF_BASE_DIR;
$onAction = 'onCreate';
$siteurl = site_url();
global $config;
global $post;
$config = get_option("zcf_{$activatedplugin}_settings");
$post = array();
global $module_options, $module, $isWidget, $assignedto, $check_duplicate, $update_record;

function zcf_ContactFormFieldsGenerator($attr, $thirdparty) {


    global $HelperObj;
    global $module_options, $module, $isWidget, $assignedto, $check_duplicate, $update_record, $formattr, $attrname;
    $module_options = 'Leads';
    $newform = new zcffieldlistDatamanage();
    $newshortcode = $newform->zcfformfieldsPropsettings($attr['name']);

    $FormSettings = $newform->zcfFormPropSettings($attr['name']);
    $formattr = array_merge(json_decode(json_encode($FormSettings), true), $newshortcode);
    $attrname = $attr['name'];
    $config_fields = $newshortcode['fields'];
    $module = $FormSettings->module;
    $assignedto = $FormSettings->assigned_to;
    $module_options = $module;
    $check_duplicate = $FormSettings->duplicate_handling;
    if (isset($shortcodes['update_record'])) {
        $update_record = $shortcodes['update_record'];
    }
    return zcf_ContactFormfieldlistsMapping($module, $config_fields, $module_options, "post", $thirdparty);
}

function zcf_callcontactform7mapping($formtype) {

    global $HelperObj;
    global $plugin_dir;
    global $plugin_url;
    global $config;
    global $post;
    global $formattr;
    global $attrname;
    global $module_options, $module, $isWidget, $assignedto, $check_duplicate, $update_record;
    $plugin_dir = ZCF_BASE_DIR_URI;
    $globalvariables = Array('plugin_dir' => $plugin_dir, 'plugin_url' => $plugin_url, 'post' => $post, 'module_options' => $module_options, 'module' => $module, 'isWidget' => $isWidget, 'assignedto' => $assignedto, 'check_duplicate' => $check_duplicate, 'update_record' => $update_record, 'HelperObj' => $HelperObj, 'formattr' => $formattr, 'attrname' => $attrname);
    require_once( ZCF_BASE_DIR_URI . "includes/crmcustomfunctions.php" );
    $CapturingWPcessClass = new zcf_CapturingClassAjax();
    $data = $CapturingWPcessClass->zcf_CaptureFormFieldsList($globalvariables);
    $crmformslog = '';
    $HelperObj = new zcfmaincorehelpers();
    $module = $HelperObj->Module;
    $moduleslug = $HelperObj->ModuleSlug;
    $activatedplugin = "crmformswpbuilder";

    $newform = new zcffieldlistDatamanage();
    $newshortcode = $newform->zcfformfieldsPropsettings($attrname);
    $FormSettings = $newform->zcfFormPropSettings($attrname);
    $config_fields = array_merge(json_decode(json_encode($FormSettings), true), $newshortcode);
    $submitcontactform = '';
    if (isset($data) && $data) {
        if (isset($_REQUEST['submitcontactform'])) {
            $form_no = sanitize_text_field($_REQUEST['formnumber']);
            $submitcontactform = "crmformsLogMsg{$form_no}";
        }
        if (isset($_REQUEST['submitcontactformwidget'])) {
            $submitcontactform = "widgetcrmformsLogMsg{$_REQUEST['formnumber']}";
        }
        $successfulAttemptsOption['total'] = $config_fields['submit_count'];
        $successfulAttemptsOption['success'] = $config_fields['success_count'];
        $total = 0;
        $success = 0;
        if (!isset($successfulAttemptsOption['total']) && ($successfulAttemptsOption['success'] )) {
            $successfulAttemptsOption['total'] = 0;
            $successfulAttemptsOption['success'] = 0;
        } else {
            $total = $successfulAttemptsOption['total'];
            $success = $successfulAttemptsOption['success'];
        }
        $total++;
        $htmlcontenttype = "\n";
        foreach ($config_fields['fields'] as $key => $value) {
            $config_field_label[$value['name']] = $value['display_label'];
        }
        foreach ($post as $key => $value) {
            if (($key != 'formnumber') && ($key != 'submitcontactformwidget') && ($key != 'moduleName') && ($key != "submit" ) && ( $key != "") && ($key != 'submitcontactform') && ($key != "g-recaptcha-response"))
                if (isset($config_field_label[$key])) {
                    $htmlcontenttype .= "{$config_field_label[$key]} : $value" . "\n";
                } else {
                    $htmlcontenttype .= "$key : $value" . "\n";
                }
        }
        $config = get_option("zcf_captcha_settings");
        if (preg_match("/{$config_fields['module']} entry is added./", $data)) {
            $success++;
            $successfulAttemptsOption['total'] = $total;
            $successfulAttemptsOption['success'] = $success;

            $successfulAttemptsOption['success'] = $success;
            $successfulAttemptsOption['total'] = $total;
            $total_config_fields[$attrname] = $config_fields;
            $newform->zcfupdateFormSaveStatuses($successfulAttemptsOption, $attrname);
            if (isset($config_fields['is_redirection']) && ($config_fields['is_redirection'] == "1") && isset($config_fields['url_redirection']) && ( $config_fields['url_redirection'] !== "" )) {
                $crmformslog .= "<script>";
                $crmformslog .= "window.location='" . $config_fields['url_redirection'] . "'";
                $crmformslog .= "</script>";
            }
            $crmformslog .= "<script>";
            if (isset($config_fields['success_message']) && ($config_fields['success_message'] != "")) {
                $crmformslog .= "document.getElementById('{$submitcontactform}').innerHTML=\"<div  style='color:green;'>{$config_fields['success_message']}</div>\"";
            } else {
                unset($_REQUEST);
                $crmformslog .= "document.getElementById('{$submitcontactform}').innerHTML=\"<div  style='color:green;'>Thank you. The data has been submitted successfully.</div>\"";
            }
            $crmformslog .= "</script>";
            return $crmformslog;
        } else {
            $successfulAttemptsOption['total'] = $total;
            $successfulAttemptsOption['success'] = $success;

            $config_fields['success'] = $success;
            $config_fields['total'] = $total;
            $total_config_fields[$attrname] = $config_fields;
            update_option("zcf_crmfields_shortcodes", $total_config_fields);
            $crmformslog .= "<script>";
            if (isset($config_fields['error_message']) && ($config_fields['error_message'] != "")) {
                $crmformslog .= "document.getElementById('{$submitcontactform}').innerHTML=\"<p  style='color:red;'>{$config_fields['error_message']}</p>\"";
            } else {
                $crmformslog .= "document.getElementById('{$submitcontactform}').innerHTML=\"<div  style='color:red;'>Sorry, there were some issues in submitting your data. Please try again later</div>\"";
            }
            $crmformslog .= "</script>";
            $successfulAttemptsOption['total'] = $total;
            $successfulAttemptsOption['success'] = $success;
            $failure = $total - $success;
            $newform->zcfupdateFormSaveStatuses($successfulAttemptsOption, $attrname);

            return $crmformslog;
        }
    }
}

function zcf_ContactFormfieldlistsMapping($module, $config_fields, $module_options, $formtype, $thirdparty) {
    global $plugin_dir;
    global $plugin_url;
    $siteurl = site_url();
    global $config;
    global $post;
    global $formattr;
    $HelperObj = new zcfmaincorehelpers();
    $captcha_error = false;
    $activatedplugin = "crmformswpbuilder";
    $script = '';
    $post = $_POST;
    if (!isset($_SESSION["generated_forms"])) {
        $_SESSION["generated_forms"] = 1;
    } else {
        $_SESSION["generated_forms"] ++;
    }

    if (isset($_POST['submitcontactform']) && (sanitize_text_field($_POST['formnumber']) == sanitize_text_field($_SESSION['generated_forms']))) {
        $count_error = 0;
        for ($i = 0; $i < count($config_fields); $i++) {
            if (array_key_exists($config_fields[$i]['name'], $_POST)) {

                if ($config_fields[$i]['zcf_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "") {

                    $count_error++;
                } elseif ($config_fields[$i]['type']['name'] == 'integer' && !preg_match('/^[\d]*$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != "")) {
                    $count_error++;
                } elseif ($config_fields[$i]['type']['name'] == 'double' && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && ($_POST[$config_fields[$i]['name']] != "")) {
                    $count_error++;
                } elseif ($config_fields[$i]['type']['name'] == 'currency' && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']] != ""))) {
                    $count_error++;
                } elseif ($config_fields[$i]['type']['name'] == 'email' && (!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))) {
                    $count_error++;
                } elseif ($config_fields[$i]['type']['name'] == 'url' && (!preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-=#]+\.([a-zA-Z0-9\.\/\?\:@\-=#])*/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))) {
                    if ($_POST[$config_fields[$i]['name']] == "") {
                        
                    } else {
                        $count_error++;
                    }
                } elseif ($config_fields[$i]['type']['name'] == 'multipicklist') {
                    $concat = "";
                    for ($index = 0; $index < count($_POST[$config_fields[$i]['name']]); $index++) {
                        $concat .= $_POST[$config_fields[$i]['name']][$index] . " |##| ";
                    }
                    $concat = substr($concat, 0, -6);
                    $post[$config_fields[$i]['name']] = $concat;
                } elseif ($config_fields[$i]['type']['name'] == 'phone' && !preg_match('/^[2-9]{1}[0-9]{2}-[0-9]{3}-[0-9]{4}$/', $_POST[$config_fields[$i]['name']])) {
                    
                }
            }
        }
        $captcha_config = get_option("zcf_captcha_settings");
        $save_field_config = $formattr;
        if (($captcha_config['crmforms_recaptcha'] == 'yes') && (isset($save_field_config['google_captcha']) && (sanitize_text_field($save_field_config['google_captcha']) == 1 ))) {
            $privatekey = $captcha_config['recaptcha_private_key'];
            if (!isset($_REQUEST['g-recaptcha-response']) || ( sanitize_text_field($_REQUEST['g-recaptcha-response']) == NULL ) || ( sanitize_text_field($_REQUEST['g-recaptcha-response']) == "" )) {
                $captcha_error = true;
            } else {
                $botcheck_url = "https://www.google.com/recaptcha/api/siteverify?secret=$privatekey&response={$_REQUEST['g-recaptcha-response']}";
                $ch = curl_init($botcheck_url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $google_bot_check_result = curl_exec($ch);
                $decoded_result = json_decode($google_bot_check_result);
                if ($decoded_result->success) {
                    $captcha_error = false;
                } else {
                    $count_error++;
                    $captcha_error = true;
                }
            }
        }
    }
    $htmlcontent = "<form id='contactform{$_SESSION["generated_forms"]}' name='contactform{$_SESSION["generated_forms"]}' method='post'>";
    $htmlcontent .= "<table class='field-list-table'>";
    $htmlcontent .= "<div id='crmformsLogMsg{$_SESSION["generated_forms"]}'></div>";
    $htmlcontent1 = "";
    $count_selected = 0;
    for ($i = 0; $i < count($config_fields); $i++) {

        $htmlcontent2 = "";
        $fieldtype = $config_fields[$i]['type']['name'];
        $hiddenFieldChk = $config_fields[$i]['hiddenfield'];

        if ($config_fields[$i]['publish'] == 1 && $hiddenFieldChk != 1) {

            if ($config_fields[$i]['zcf_mandatory'] == 1) {
                if ($fieldtype != "ownerlookup" && $fieldtype != "lookup" && $fieldtype != 'multiselectlookup') {
                    $htmlcontent1 .= "<tr class='" . $fieldtype . "'><td><p>" . $config_fields[$i]['display_label'] . " <span class='red'>*</span></p></td></tr>";
                    $M = ' mandatory';
                }
            } else {
                if ($fieldtype != "ownerlookup" && $fieldtype != "lookup" && $fieldtype != 'multiselectlookup') {
                    $htmlcontent1 .= "<tr class='" . $fieldtype . "'><td><p>" . $config_fields[$i]['display_label'] . "</p></td></tr>";
                    $M = '';
                }
            }
            $htmlcontent1 .= "<tr>";
            if ($fieldtype == "string" || $fieldtype == "text" || $fieldtype == "formula" || $fieldtype == "bigint") {
                $htmlcontent1 .= "<td><input type='text' class='string{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
                if (isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                else
                    $htmlcontent1 .= '';
                $htmlcontent1 .= "'/><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'>";
                //print_r($_POST);

                if (isset($_POST['submitcontactform']) && (intval($_POST['formnumber']) == $_SESSION['generated_forms'])) {


                    if ($config_fields[$i]['zcf_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "") {
                        $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                    }
                }
                $htmlcontent1 .= "</span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == "textarea") {
                $htmlcontent1 .= "<td><textarea class='textarea{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}'></textarea><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'></span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'radioenum') {
                $htmlcontent1 .= "<td>";
                $picklist_count = count($config_fields[$i]['type']['picklistValues']);
                for ($j = 0; $j < $picklist_count; $j++) {
                    $htmlcontent2 .= "<input type='radio' name='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['label']}'>{$config_fields[$i]['type']['picklistValues'][$j]['value']}";
                }
                $htmlcontent1 .= $htmlcontent2;
                $htmlcontent1 .= "<script>document.getElementById('{$config_fields[$i]['name']}').value='{$_POST[$config_fields[$i]['name']]}'</script>";
                $htmlcontent1 .= "<span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'></span>";
                $htmlcontent1 .= "</td>";
                $count_selected++;
            } elseif ($fieldtype == 'multiselectpicklist') {
                $picklist_count = count($config_fields[$i]['type']['picklistValues']);
                $htmlcontent1 .= "<td><select class='multipicklist{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}[]' multiple='multiple' id='{$module_options}_{$config_fields[$i]['name']}' >";
                for ($j = 0; $j < $picklist_count; $j++) {
                    $htmlcontent2 .= "<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                }
                $htmlcontent1 .= $htmlcontent2;
                $htmlcontent1 .= "</select><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'></span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'picklist') {
                $picklist_count = count($config_fields[$i]['type']['picklistValues']);

                $htmlcontent1 .= "<td><select class='picklist{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}'  value='";
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                else
                    $htmlcontent1 .= '';

                $htmlcontent1 .= "'>";
                for ($j = 0; $j < $picklist_count; $j++) {
                    if ($activatedplugin == 'freshsales') {
                        $htmlcontent2 .= "<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                    } else {
                        $htmlcontent2 .= "<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                    }
                }
                $htmlcontent1 .= $htmlcontent2;
                $htmlcontent1 .= "</select><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'></span></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'integer') {
                $htmlcontent1 .= "<td><input type='text' class='integer{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                else
                    $htmlcontent1 .= '';
                $htmlcontent1 .= "'/><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'>";
                if ($config_fields[$i]['zcf_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "") {
                    $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                } elseif (isset($_POST[$config_fields[$i]['name']]) && sanitize_text_field($config_fields[$i]['type']['name']) == 'integer' && !preg_match('/^[\d]*$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != "")) {
                    $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                }
                $htmlcontent1 .= "</span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'double') {
                $htmlcontent1 .= "<td><input type='text' class='double{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value=''/><br/><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'></span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'currency') {
                $htmlcontent1 .= "<td><input type='text' class='currency{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                else
                    $htmlcontent1 .= '';
                $htmlcontent1 .= "'/><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'>";
                if ($config_fields[$i]['zcf_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "") {
                    $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                } elseif (isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'currency' && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != "")) {
                    $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                }
                $htmlcontent1 .= "</span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'email') {
                $htmlcontent1 .= "<td><input type='text' class='email{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= $_POST[$config_fields[$i]['name']];
                else
                    $htmlcontent1 .= '';

                $htmlcontent1 .= "'/><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'>";

                if ($config_fields[$i]['zcf_mandatory'] == 1 && isset($_POST[$config_fields[$i]['name']]) && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "") {
                    $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                } elseif (isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'email' && (!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))) {
                    $htmlcontent1 .= "Please enter a valid Email.";
                }
                $htmlcontent1 .= "</span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'date') {
                if ($thirdparty != "thirdparty") {
                    ?>
                    <script>

                        jQuery(document).ready(function () {

                            jQuery("#<?php echo esc_js($module_options . '_' . $config_fields[$i]['name'] . '_' . $_SESSION['generated_forms']); ?>").datepicker({ format: 'YYYY-MM-DD'});
                        });
                    </script>

                    <?php
                }
                $htmlcontent1 .= '<td class="datetimetD"><input type="text" class="date' . $M . ' crmforms_post_fields" name=' . $config_fields[$i]['name'] . ' id="' . $module_options . '_' . $config_fields[$i]['name'] . '_' . $_SESSION['generated_forms'] . '" value="';
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= $_POST[$config_fields[$i]['name']];
                else
                    $htmlcontent1 .= '';

                $htmlcontent1 .= '" /> <span class="crmforms_field_error" id="' . $config_fields[$i]['name'] . 'error' . $_SESSION["generated_forms"] . '"></span></td></tr>';

                $count_selected++;
            }
            elseif ($fieldtype == 'datetime') {
                if ($thirdparty != "thirdparty") {
                    ?>
                    <script>

                        jQuery(document).ready(function () {

                            jQuery("#<?php echo esc_js($module_options . '_' . $config_fields[$i]['name'] . '_' . $_SESSION['generated_forms']); ?>").datepicker({ format: 'YYYY-MM-DD'});
                        });
                    </script>

                    <?php
                }
                $htmlcontent1 .= '<td class="datetimetD"><input type="text" class="' . $fieldtype . ' crmforms_post_fields" name=' . $config_fields[$i]['name'] . ' id="' . $module_options . '_' . $config_fields[$i]['name'] . '_' . $_SESSION['generated_forms'] . '" value="';
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= $_POST[$config_fields[$i]['name']];
                else
                    $htmlcontent1 .= '';

                $htmlcontent1 .= '" /> <span class="crmforms_field_error" id="' . $config_fields[$i]['name'] . 'error' . $_SESSION["generated_forms"] . '"></span></td></tr>';

                $count_selected++;
            }
            elseif ($fieldtype == 'boolean') {
                $htmlcontent1 .= '<td><input type="checkbox' . $M . '" class="boolean" name=' . $config_fields[$i]['name'] . ' id="' . $module_options . '_' . $config_fields[$i]['name'] . '" value="on"/><br/><span class="crmforms_field_error" id="' . $config_fields[$i]['name'] . 'error' . $_SESSION["generated_forms"] . '"></span></td></tr>';
                $count_selected++;
            } elseif ($fieldtype == 'url' || $fieldtype == 'website') {
                $htmlcontent1 .= "<td><input type='text' class='url{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                else
                    $htmlcontent1 .= '';
                $htmlcontent1 .= "'/><br/><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'>";
                if (isset($_POST['submitcontactform']) && (intval($_POST['formnumber']) == $_SESSION['generated_forms'])) {
                    if ($config_fields[$i]['zcf_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "") {
                        $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                    } elseif (isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'url' && (!preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-=#]+\.([a-zA-Z0-9\.\/\?\:@\-=#])*/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))) {
                        $htmlcontent1 .= "Please enter a valid Website.";
                    }
                }
                $htmlcontent1 .= "</span></td></tr>";
                $count_selected++;
            } elseif ($fieldtype == 'phone') {
                $htmlcontent1 .= "<td><input type='text' class='phone{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
                if (isset($_POST[$config_fields[$i]['name']]) && (intval($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error != 0)
                    $htmlcontent1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                else
                    $htmlcontent1 .= '';
                $htmlcontent1 .= "'/><br/><span class='crmforms_field_error' id='" . $config_fields[$i]['name'] . "error{$_SESSION["generated_forms"]}'>";
                if (isset($_POST['submitcontactform']) && (intval($_POST['formnumber']) == $_SESSION['generated_forms'])) {
                    if ($config_fields[$i]['zcf_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "") {
                        $htmlcontent1 .= $config_fields[$i]['display_label'] . " cannot be empty";
                    }
                }
                $htmlcontent1 .= "</span></td></tr>";
                $count_selected++;
            } else {
                //$htmlcontent1.="<td><input type='text' class='others{$M} crmforms_post_fields' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='{".sanitize_text_field($_POST[$config_fields[$i]['name']])."}'/><br/><span class='crmforms_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></td></tr>";
                //$count_selected++;
            }
        } //test'

        if ($hiddenFieldChk == 1) {
            if ($config_fields[$i]['type']['name'] == 'multiselectpicklist') {
                $defaultvaluepicklist = unserialize($config_fields[$i]['defaultvalue']);
            }

            if ($config_fields[$i]['type']['name'] == 'picklist') {
                $picklist_count = count($config_fields[$i]['type']['picklistValues']);
                $htmlcontent2 = '';
                $htmlcontent1 .= "<td><select  class='multipicklist form-control crmforms_post_fields hidden '   name='{$config_fields[$i]['name']}'id='{$config_fields[$i]['name']}' >";


                $htmlcontent2 .= "<option  selected id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['defaultvalue']}'>{$config_fields[$i]['defaultvalue']}</option>";


                $htmlcontent1 .= $htmlcontent2;
            } else if ($config_fields[$i]['type']['name'] == 'multiselectpicklist') {
                $picklist_count = count($config_fields[$i]['type']['picklistValues']);
                $htmlcontent2 = '';
                $htmlcontent1 .= "<td><select class='multipicklist form-control hidden' multiple='multiple' name='{$config_fields[$i]['name']}[]'id='{$$config_fields[$i]['name']}' >";
                foreach ($defaultvaluepicklist as $key => $value) {
                    $htmlcontent2 .= "<option  selected id='{$config_fields[$i]['name']}' value='{$value}'>{$value}</option>";
                }
                $htmlcontent1 .= $htmlcontent2;
            } else {
                $htmlcontent1 .= "<td><input type='hidden'  class='form-control dafalutvalue ss' name='{$config_fields[$i]['name']}'   value='" . $config_fields[$i]['defaultvalue'] . "'>";
            }
        }
    }

    if ($count_selected == 0) {
        $htmlcontent .= "<h3>You have selected no fields</h3>";
    } else {
        $htmlcontent .= $htmlcontent1;
    }
    $htmlcontent .= "<tr><td>";
    if ($count_selected == 0) {
        
    } else {
        $config = get_option("zcf_captcha_settings");
        $save_field_config = $formattr;
        if (($config['crmforms_recaptcha'] == 'yes') && (isset($save_field_config['google_captcha']) && ($save_field_config['google_captcha'] == "1"))) {
            $publickey = $config['recaptcha_public_key'];
            
            $htmlcontent .= '<br><div class="g-recaptcha" data-sitekey="' . $publickey . '"></div>';
            if (isset($captcha_error) && ($captcha_error == true)) {
                $htmlcontent .= "<div style='color:red' id='recaptcha_response_field_error{$_SESSION["generated_forms"]}'>Captcha Error</div>";
                $count_error++;
            }
        }
        $htmlcontent .= "<div class='mT20' >
		<div class='form-submit'>";
        $htmlcontent .= "<input type='hidden' name='formnumber' value='{$_SESSION['generated_forms']}'>";
        $htmlcontent .= "<input type='hidden' name='submitcontactform' value='submitcontactform{$_SESSION['generated_forms']}'/>";
        $htmlcontent .= '<input type="submit" value="Submit" id="submit" name="submit"></div>';
    }
    $htmlcontent .= "</td></tr></table>";
    $htmlcontent .= "<input type='hidden' value='" . $module . "' name='moduleName' /><input type='hidden'  name='layoutId' value='" . $config_fields[0] ['layoutId'] . "'/></div></form>";

    if (isset($_POST['submitcontactform']) && (intval($_POST['formnumber']) == $_SESSION['generated_forms'])) {
        if ($count_error == 0) {
            $htmlcontent .= zcf_callcontactform7mapping($formtype);
        }
    }
    return $htmlcontent;
}
?>
