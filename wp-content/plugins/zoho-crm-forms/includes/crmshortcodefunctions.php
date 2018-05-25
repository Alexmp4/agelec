<?php
/* * ****************************************************************************************
 * Copyright (C) Zoho Corp 2017 - All Rights Reserved
 * ***************************************************************************************** */
if (!defined('ABSPATH'))
    exit;

class zcffieldoptions {

    public $nonceKey = null;

    public function __construct() {
        require_once(ZCF_BASE_DIR_URI . 'includes/crmcustomfunctions.php');
        $helperObj = new zcfcustomfunctions();
    }

    function zcfsaveFormFields($options, $onAction, $editShortCodes, $formtype = "post") {
        $HelperObj = new zcfmaincorehelpers();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = 'crmformswpbuilder';
        $save_field_config = array();
        $crmtype = sanitize_text_field($_REQUEST['crmtype']);
        $module = sanitize_text_field($_REQUEST['module']);
        $moduleslug = rtrim(strtolower($module), "s");
        $options = "zcf_crmfields_shortcodes";
        if (isset($_POST ['savefields']) && (sanitize_text_field($_POST ['savefields']) == "GenerateShortcode")) {
            $config_fields = get_option("crmforms_{$crmtype}_{$moduleslug}_fields-tmp");
            $config_contact_shortcodes = get_option($options);
        } else {
            $options = "zcf_crmfields_shortcodes";
            $config_contact_shortcodes = get_option($options);
            $config_fields = $config_contact_shortcodes[$editShortCodes];
        }
        foreach ($config_fields as $shortcode_attributes => $fields) {
            if ($shortcode_attributes == "fields") {
                foreach ($fields as $key => $field) {
                    $save_field_config["fields"][$key] = $field;

                    if (!isset($field['mandatory']) || $field['mandatory'] != 2) {
                        if (isset($_POST['select' . $key])) {
                            $save_field_config['fields'][$key]['publish'] = 1;
                        } else {
                            $save_field_config['fields'][$key]['publish'] = 0;
                        }
                    } else {
                        $save_field_config['fields'][$key]['publish'] = 1;
                    }

                    if (!isset($field['mandatory']) || $field['mandatory'] != 2) {
                        if (isset($_POST['mandatory' . $key])) {
                            $save_field_config['fields'][$key]['zcf_mandatory'] = 1;
                            $save_field_config['fields'][$key]['publish'] = 1;
                        } else {
                            $save_field_config['fields'][$key]['zcf_mandatory'] = 0;
                        }
                    } else {
                        $save_field_config['fields'][$key]['zcf_mandatory'] = 1;
                    }

                    $save_field_config['fields'][$key]['display_label'] = sanitize_text_field($_POST['fieldlabel' . $key]);
                }
            } else {
                $save_field_config[$shortcode_attributes] = $fields;
            }
        }
        if (!isset($save_fields_config["check_duplicate"])) {
            $save_fields_config["check_duplicate"] = 'none';
        } else if (isset($save_fields_config["check_duplicate"]) && ($save_fields_config["check_duplicate"] === 1)) {
            $save_fields_config["check_duplicate"] === 'skip';
        } else if (isset($save_fields_config["check_duplicate"]) && ($save_fields_config["check_duplicate"] === 0)) {
            $save_fields_config["check_duplicate"] = 'none';
        }

        $extra_fields = array("formtype", "enableurlredirection", "redirecturl", "errormessage", "successmessage", "assignedto", "check_duplicate", "enablecaptcha");

        foreach ($extra_fields as $extra_field) {
            if (isset($_POST[$extra_field])) {
                $save_field_config[$extra_field] = $_POST[$extra_field];
            } else {
                unset($save_field_config[$extra_field]);
            }
        }
        for ($i = 0; $i < $_REQUEST['no_of_rows']; $i++) {
            $REQUEST_DATA[$i] = $_REQUEST['position' . $i];
        }

        asort($REQUEST_DATA);

        $i = 0;
        foreach ($REQUEST_DATA as $key => $value) {
            $Ordered_field_config['fields'][$i] = $save_field_config['fields'][$key];
            $i++;
        }
        $save_field_config['fields'] = $Ordered_field_config['fields'];
        $save_field_config['crm'] = $_REQUEST['crmtype'];
        if (isset($_POST ['savefields']) && (sanitize_text_field($_POST ['savefields']) == "GenerateShortcode")) {
            $OverallFunctionObj = new zcfcustomfunctions();
            $random_string = $OverallFunctionObj->zcf_CreateFieldShortcode($_REQUEST['crmtype'], $_REQUEST['module']);
            $config_contact_shortcodes[$random_string] = $config_fields;
            update_option("zcf_crmfields_shortcodes", $config_contact_shortcodes);
            update_option("crmforms_crmformswpbuilder_{$moduleslug}_fields-tmp", $save_field_config);
            exit;
        } else {
            $config_contact_shortcodes[$_REQUEST['EditShortcode']] = $save_field_config;
            update_option("zcf_crmfields_shortcodes", $config_contact_shortcodes);
            update_option("crmforms_crmformswpbuilder_{$moduleslug}_fields-tmp", $save_field_config);
        }
        $data['display'] = "";
        return $data;
    }

    function zcfformFields($options, $onAction, $editShortCodes, $formtype = "post", $module, $layoutname) {
        global $wpdb;
        $fields = $wpdb->get_results("select * from zcf_zohocrmform_field_manager where  module_type='" . $module . "' and Layout_Name ='" . $layoutname . "'");

        $siteurl = site_url();
        $crmformsfieldData = new zcffieldlistDatamanage();
        $module = $module_options = 'Leads';
        $htmlcontent1 = '';
        $config_leads_fields = $crmformsfieldData->zcfformfieldsPropsettings($editShortCodes);

        $editupdatecount = $wpdb->get_results("select * from zcf_zohocrmform_field_manager fm join zcf_zohocrm_formfield_manager ffm ON ffm.field_id = fm.field_id join zcf_zohoshortcode_manager sm ON sm.shortcode_id = ffm.shortcode_id where sm.shortcode_name='" . $editShortCodes . "' and fm.editupdate=1 and fm.viewcreate_type=1 group by fm.field_name");
        $imagepath = ZCF_BASE_DIR . 'assets/images/';
        $imagepath = esc_url($imagepath);
        $htmlcontent = '
		<input type="hidden" name="field-form-hidden" value="field-form" />
		<div>';
        $i = 0;

        if (!isset($config_leads_fields['fields'][0])) {
            $htmlcontent .= '<p style="color:red;font-size:20px;text-align:center;margin-top:-22px;margin-bottom:20px;">' . __("Crm fields are not yet synchronised sss", "zoho-crm-form-builder") . ' </p>';
        } else {
            $htmlcontent .= '<form method="post" name = "userform" id="userform" action="' . ZCF_BASE_DIR . '/includes/class-lb-crmshortcodefunctions.php">
				<table class="table  mb0" cellpadding="0" cellspacing="0"  id="sort_table" >
				<thead>
				<tr class="crmforms_highlight crmforms_alt table-heading" >';

            $htmlcontent .= '<th class="boder_lefte7e7e7 boder_bottome7e7e7 border-rightTrans border-topTrans sortable-icon" width="6%" scope=col rowspan=2></th>';
            if ($config_leads_fields['fields']['editupdatecount'] >= 1) {
                $htmlcontent .= '<th  align="left" width="22%"  scope=col rowspan=2 class="boder_righte7e7e7 boder_lefte7e7e7 border-topTrans color_222">' . __('Crm Fields', 'zoho-crm-form-builder') . '<span class="editupdatecount">' . $config_leads_fields['fields']['editupdatecount'] . '</span></th>';
            } else {
                $htmlcontent .= '<th  align="left" width="22%"   scope=col rowspan=2 class="border-topTrans boder_bottome7e7e7 boder_righte7e7e7 color_222">' . __('CRM Fields', 'zoho-crm-form-builder') . '</th>';
            }
            $htmlcontent .= '<th scope=colgroup colspan=5 class="aligncenter border-topTrans boder_righte7e7e7 pR pb0 color_222"  scope=col> Wordpress form<span class="tableseperator"></span></th></tr><tr>
			    <th  class="border-topTrans  border-rightTrans pl30 boder_bottome7e7e7" align="left" width="22%"  scope=col>' . __('Field Label', 'zoho-crm-form-builder') . '</th>
				<th  class="border-topTrans aligncenter border-rightTrans boder_bottome7e7e7" align="left" width="10%"  scope=col>' . __('Mandatory', 'zoho-crm-form-builder') . '</th>
			    <th  class="border-topTrans  aligncenter border-rightTrans boder_bottome7e7e7" align="left" width="10%"  scope=col>' . __('Hidden Field', 'zoho-crm-form-builder') . '</th>
				<th  class="border-topTrans boder_bottome7e7e7 boder_righte7e7e7 " align="left" width="17%"  scope=col>' . __('Default Values', 'zoho-crm-form-builder') . '</th>
			    </tr></thead><tbody>';
            $fieldcount = count($config_leads_fields['fields']) - 1;
            for ($i = 0; $i < $fieldcount; $i++) {

                if ($config_leads_fields['fields'][$i]['publish'] == 1 || $config_leads_fields['fields'][$i]['publish'] == '') {
                    $disableTypefield = '';
                } else {
                    $disableTypefield = 'disabled';
                }
                if ($config_leads_fields['fields'][$i]["zcf_mandatory"] == 1 || $config_leads_fields['fields'][$i]["zcf_mandatory"] == '') {
                    $disableTypeHiddenfield = 'disabledHidden';
                    $disableTypeDisabledfield = 'disabled';
                } else {
                    $disableTypeHiddenfield = '';
                    $disableTypeDisabledfield = '';
                }



                $defaultvaluepicklist = $config_leads_fields['fields'][$i]['defaultvalue'];
                if (($config_leads_fields['fields'][$i]['type']['name'] == 'multiselectpicklist') && $config_leads_fields['fields'][$i]['defaultvalue'] != '') {
                    $defaultvaluepicklist = unserialize($config_leads_fields['fields'][$i]['defaultvalue']);
                }


                $defaultvalueLabel = '';
                if ($config_leads_fields['fields'][$i]['hiddenfield'] == 1) {
                    $defaultvalueLabel = 'dB';
                }



                if ($config_leads_fields['fields'][$i]['zcf_mandatory'] == 1) {
                    $madantory_checked = 'checked="checked"';
                } else {
                    $madantory_checked = "";
                }
                $neweditupate = '';
                if ($config_leads_fields['fields'][$i]['editupdate'] == 1) {
                    $neweditupate = 'active';
                }
                $field_id = $config_leads_fields['fields'][$i]['field_id'];
                $rel_id = $config_leads_fields['fields'][$i]['rel_id'];
                $orderpos = $i;
                if (isset($config_leads_fields['fields'][$i]['mandatory']) && $config_leads_fields['fields'][$i]['mandatory'] == 2) {
                    if ($i % 2 == 1)
                        $htmlcontent1 .= '<tr></tr><tr class="crmforms_highlight crmforms_alt ' . $neweditupate . '" >';
                    else
                        $htmlcontent1 .= '<tr class="crmforms_highlight ' . $neweditupate . '">';

                    $htmlcontent1 .= '<td class="back_f8f8f8 sortable-icon border-leftTrans border-rightTrans" width="4%"></td>';
                    $htmlcontent1 .= '<td class="back_f8f8f8 arrow-div" ><span class="newmandatory">' . $config_leads_fields['fields'][$i]['label'] . '</span></td>';

                    $htmlcontent1 .= "<td class=' aligncenter border-rightTrans pl30'><input type='text' data-id='" . $field_id . "' order-pos='" . $orderpos . "' data-label='Save Display Label' class='textField form-control pl0 field_label_display orderPos ' name='fieldlabel" . $field_id . "'  id='field_label_display_" . $i . "' data-value='" . $config_leads_fields['fields'][$i]['display_label'] . "' value='" . $config_leads_fields['fields'][$i]['display_label'] . "' onblur =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\"></td>";

                    $htmlcontent1 .= '<td class=" aligncenter border-rightTrans">';
                    $htmlcontent1 .= "</td>";
                    $htmlcontent1 .= '<td class="  border-rightTrans aligncenter  hiddenFields vH" width="10%">';

                    if ($config_leads_fields['fields'][$i]['hiddenfield'] == 1) {

                        $htmlcontent1 .= "<label class='newCustomchkbox-md  {$disableTypeHiddenfield}'><input checked='checked' type='checkbox' data-id='" . $field_id . "'' name='hiddenfieldChk" . $field_id . "' class='onoffswitch-checkbox hiddenfieldChk enablefield' data-status='hiddenenablefield' data-label='Hidden Disable Field' id='hiddenfieldChk" . $i . "' onclick =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" > <span class='vam chkbxIcon'></span></label>";
                    } else {

                        $htmlcontent1 .= "<label class='newCustomchkbox-md  {$disableTypeHiddenfield}'><input type='checkbox' data-id='" . $field_id . "'' name='hiddenfieldChk" . $field_id . "' class='hiddenfieldChk disablefield' data-status='hiddendisablefield'  data-label='Hidden Enable Field' id='hiddenfieldChk" . $i . "' onclick =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" ><span class='vam chkbxIcon'></span></label>";
                    }

                    $htmlcontent1 .= '</td>';
                    if ($config_leads_fields['fields'][$i]['type']['name'] == 'picklist') {
                        $picklist_count = count($config_leads_fields['fields'][$i]['type']['picklistValues']);
                        $htmlcontent2 = '';
                        $htmlcontent1 .= "<td width='17%' class=' vH " . $disableTypeHiddenfield . " defaultvaluesField border-rightTrans'><select data-id='" . $field_id . "'' data-label='Default value' onchange =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" class='multipicklist form-control crmforms_post_fields defaultvalue " . $defaultvalueLabel . " '   name='{$config_leads_fields['fields'][$i]['name']}[]'id='{$config_leads_fields['fields'][$i]['name']}' >";
                        for ($j = 0; $j < $picklist_count; $j++) {

                            if ($config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value'] == $defaultvaluepicklist) {
                                $htmlcontent2 .= "<option  selected id='{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            } else {
                                $htmlcontent2 .= "<option  id='{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            }
                        }
                        $htmlcontent1 .= $htmlcontent2;
                    } else if ($config_leads_fields['fields'][$i]['type']['name'] == 'multiselectpicklist') {
                        $picklist_count = count($config_leads_fields['fields'][$i]['type']['picklistValues']);
                        $htmlcontent2 = '';
                        $htmlcontent1 .= "<td width='17%' class=' vH defaultvaluesField border-rightTrans " . $disableTypeHiddenfield . "'><select data-id='" . $field_id . "'' data-label='Default value' onchange =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" class='multipicklist form-control crmforms_post_fields defaultvalue " . $defaultvalueLabel . "' multiple='multiple' name='{$config_leads_fields['fields'][$i]['name']}[]'id='{$config_leads_fields['fields'][$i]['name']}' >";
                        for ($j = 0; $j < $picklist_count; $j++) {
                            $arrayexistchk = in_array($config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value'], $defaultvaluepicklist);

                            if ($arrayexistchk) {
                                $htmlcontent2 .= "<option  selected id='sdsd{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            } else {
                                $htmlcontent2 .= "<option  id='sdsd{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            }
                        }
                        $htmlcontent1 .= $htmlcontent2;
                    } else {
                        $htmlcontent1 .= "<td width='17%' class='border-rightTrans  " . $disableTypeHiddenfield . " defaultvaluesField vH' ><input type='text' data-id='" . $field_id . "'' data-label='Default value' onblur =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" class='textField pl0 form-control defaultvalue {$defaultvalueLabel} ' name='dafalutvalue{$field_id}'  id='dafalutvalue{$i}' value='" . $config_leads_fields['fields'][$i]['defaultvalue'] . "' data-value='" . $config_leads_fields['fields'][$i]['defaultvalue'] . "'>";
                    }


                    $htmlcontent1 .= "</td></tr>";
                } else {
                    if ($i % 2 == 1)
                        $htmlcontent1 .= '<tr class="crmforms_highlight crmforms_alt ' . $neweditupate . '">';
                    else
                        $htmlcontent1 .= '<tr class="crmforms_highlight ' . $neweditupate . '">';

                    $htmlcontent1 .= '<td class="back_f8f8f8 sortable-icon border-leftTrans border-rightTrans" width="4%"><a href="#" class="delete_link" style="margin-left:2px;" onclick="zcfdeleteFieldsState(' . $rel_id . ')">  <sapn class="delete-icon dB"> </sapn></a></td>';

                    $htmlcontent1 .= '
					<td width="22%" class="back_f8f8f8 arrow-div">' . $config_leads_fields['fields'][$i]['label'] . '</td>';

                    $htmlcontent1 .= "<td class='aligncenter border-rightTrans pl30'><input type='text' data-id='" . $field_id . "' order-pos='" . $orderpos . "' data-label='Save Display Label' class='form-control pl0 field_label_display orderPos  textField' name='fieldlabel" . $field_id . "'  id='field_label_display_" . $i . "' data-value='" . $config_leads_fields['fields'][$i]['display_label'] . "' value='" . $config_leads_fields['fields'][$i]['display_label'] . "' onblur =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\"></td>";

                    $htmlcontent1 .= ' <td class="aligncenter border-rightTrans  mandatoryField" width="10%">';
                    if ($config_leads_fields['fields'][$i]["zcf_mandatory"] == 1 || $config_leads_fields['fields'][$i]["zcf_mandatory"] == '') {

                        $htmlcontent1 .= "<label class='newCustomchkbox-md'><input checked='checked' type='checkbox' data-id='" . $field_id . "'' name='setmandatory" . $field_id . "' class='setmandatory enablefield' data-status='enablefield' data-label='Disable Mandatory' id='setmandatory" . $i . "' onclick =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" ><span class='vam chkbxIcon'></span></label>";
                    } else {

                        $htmlcontent1 .= "<label class='newCustomchkbox-md '><input type='checkbox' data-id='" . $field_id . "' name='setmandatory" . $field_id . "' class='setmandatory disablefield' data-status='disablefield'  data-label='Enable Mandatory' id='setmandatory" . $i . "' onclick =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" ><span class='vam chkbxIcon'></span></label>";
                    }
                    $htmlcontent1 .= '</td>';






                    $htmlcontent1 .= '<td width="17%" class="border-rightTrans aligncenter ' . $disableTypeDisabledfield . ' hiddenFields " width="10%">';

                    if ($config_leads_fields['fields'][$i]['hiddenfield'] == 1) {

                        $htmlcontent1 .= "<label class='newCustomchkbox-md'><input checked='checked' type='checkbox' data-id='" . $field_id . "'' name='hiddenfieldChk" . $field_id . "' class='hiddenfieldChk enablefield' data-status='hiddenenablefield' data-label='Hidden Disable Field' id='hiddenfieldChk" . $i . "' onclick =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" > <span class='vam chkbxIcon'></span></label>";
                    } else {

                        $htmlcontent1 .= "<label class='newCustomchkbox-md'><input type='checkbox' data-id='" . $field_id . "'' name='hiddenfieldChk" . $field_id . "' class='hiddenfieldChk disablefield' data-status='hiddendisablefield'  data-label='Hidden Enable Field' id='hiddenfieldChk" . $i . "' onclick =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" ><span class='vam chkbxIcon'></span></label>";
                    }

                    $htmlcontent1 .= '</td>';

                    if ($config_leads_fields['fields'][$i]['type']['name'] == 'picklist') {
                        $picklist_count = count($config_leads_fields['fields'][$i]['type']['picklistValues']);
                        $htmlcontent2 = '';
                        $htmlcontent1 .= "<td width='17%' class='border-rightTrans  " . $disableTypeHiddenfield . " defaultvaluesField'><select data-id='" . $field_id . "'' data-label='Default value' onchange =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" class='multipicklist form-control crmforms_post_fields defaultvalue " . $defaultvalueLabel . " '   name='{$config_leads_fields['fields'][$i]['name']}[]'id='{$config_leads_fields['fields'][$i]['name']}' >";
                        for ($j = 0; $j < $picklist_count; $j++) {
                            if ($config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value'] == $defaultvaluepicklist) {
                                $htmlcontent2 .= "<option  selected id='{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            } else {
                                $htmlcontent2 .= "<option  id='{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            }
                        }
                        $htmlcontent1 .= $htmlcontent2;
                    } else if ($config_leads_fields['fields'][$i]['type']['name'] == 'multiselectpicklist') {
                        $picklist_count = count($config_leads_fields['fields'][$i]['type']['picklistValues']);
                        $htmlcontent2 = '';
                        $htmlcontent1 .= "<td width='17%' class='border-rightTrans  " . $disableTypeHiddenfield . " defaultvaluesField'><select data-id='" . $field_id . "'' data-label='Default value' onchange =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" class='multipicklist form-control crmforms_post_fields defaultvalue " . $defaultvalueLabel . " ' multiple='multiple' name='{$config_leads_fields['fields'][$i]['name']}[]'id='{$config_leads_fields['fields'][$i]['name']}' >";
                        for ($j = 0; $j < $picklist_count; $j++) {
                            $arrayexistchk = in_array($config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value'], $defaultvaluepicklist);

                            if ($arrayexistchk) {
                                $htmlcontent2 .= "<option  selected id='sdsd{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            } else {
                                $htmlcontent2 .= "<option  id='sdsd{$config_leads_fields['fields'][$i]['name']}' value='{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}'>{$config_leads_fields['fields'][$i]['type']['picklistValues'][$j]['label']['actual_value']}</option>";
                            }
                        }
                        $htmlcontent1 .= $htmlcontent2;
                    } else {
                        $htmlcontent1 .= "<td width='17%' class='border-rightTrans  " . $disableTypeHiddenfield . " defaultvaluesField'><input type='text' data-id='" . $field_id . "'' data-label='Default value' onblur =  \" return updateStatus(this,'" . site_url() . "','{$_REQUEST['module']}','zcf_crmfields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" class='form-control pl0 textField defaultvalue {$defaultvalueLabel} ' name='dafalutvalue{$field_id}'  id='dafalutvalue{$i}' value='" . $config_leads_fields['fields'][$i]['defaultvalue'] . "' data-value='" . $config_leads_fields['fields'][$i]['defaultvalue'] . "'>";
                    }




                    $htmlcontent1 .= "</td></tr>";
                }
            }
            $htmlcontent1 .= "<input type='hidden' name='no_of_rows' id='no_of_rows' value={$i} />";
            $htmlcontent .= $htmlcontent1;
            $htmlcontent .= '</tbody></table>
		</form>';
        }
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery("tbody").sortable({
                    update: function (event, ui) {
                        var orderArray = new Array;
                        var siteurl = "<?php echo site_url(); ?>";
                        var module = '<?php echo $_REQUEST['module']; ?>';
                        var option = 'crmforms_fields_shortcoders';
                        var shortcode = '<?php echo $_REQUEST['EditShortcode']; ?>';
                        var onAction = '<?php echo $_REQUEST['onAction']; ?>';
                        var crmtype = document.getElementById("lead_crmtype").value;
                        var bulkaction = 'Update Order';
                        //var chkArray = new Array;
                        //var labelArray = new Array;
                        var chkarray = [];
                        var labelarray = [];
                        jQuery("#sort_table tbody").find('tr').each(function (i, el) {
                            var tds = jQuery(this).find('.orderPos');
                            var idx = tds.attr('data-id');
                            var changed_pos = parseInt(idx);
                            orderArray.push(changed_pos);

                        });

                        var orderarray = JSON.stringify(orderArray);
                        var flag = true;

                        jQuery.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: {
                                'action': 'zcfmainFormsActions',
                                'doaction': 'CheckformExits',
                                'siteurl': siteurl,
                                'module': module,
                                'crmtype': crmtype,
                                'option': option,
                                'onAction': onAction,
                                'shortcode': shortcode,
                                'bulkaction': bulkaction,
                                'chkarray': chkarray,
                                'labelarray': labelarray,
                                'orderarray': orderarray,
                            },
                            success: function (data) {
                                jQuery('#loading-image').hide();
                                if (data == "Not synced") {
                                    alert("Must Fetch fields before Saving Settings");
                                    flag = false;
                                    return false;
                                } else {
                                    showMsgBand('success', 'Field order updated successfully!', 10000);
                                }
                            },
                            error: function (errorThrown) {
                            }
                        });
                        return flag;
                    }
                });
            });
            jQuery('tbody').sortable({
                handle: '.handle'
            });
        </script>	
        <?php
        return $htmlcontent;
    }

    function zcfenableMandatoryFields($selectedfields, $shortcode_name) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set zcf_field_mandatory = '1' ,editupdate = '0' where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id'");
    }

    function zcfdisableMandatoryFields($selectedfields, $shortcode_name) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set zcf_field_mandatory = '0',editupdate = '0' where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id' ");
    }

    function zcfsaveFieldLabelDisplay($fieldDisplayLabels, $selectedfields, $shortcode_name) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set display_label = '{$fieldDisplayLabels}' , editupdate = '0' where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id'");
    }

    function zcfdefaultvalueFields($selectedfields, $shortcode_name, $defaultvalue) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set defaultvalues = '{$defaultvalue}' , editupdate = '0' where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id'");
    }

    function zcfenableFields($selectedfields, $shortcode_name) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set state = '1' , editupdate = '0'  where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id'");
    }

    function zcfdisableFields($selectedfields, $shortcode_name) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set state = '0' , editupdate = '0' where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id'");
    }

    function zcfenableHiddenFields($selectedfields, $shortcode_name) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set hiddenfield = '1' , editupdate = '0'  where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id'");
    }

    function zcfdisableHiddenFields($selectedfields, $shortcode_name) {
        global $wpdb;
        $enable_showfields = $wpdb->get_results($wpdb->prepare("SELECT fieldmanager.field_id,fieldmanager.shortcode_id,fieldmanager.rel_id, shorcodemanager.shortcode_id FROM zcf_zohocrm_formfield_manager as fieldmanager INNER JOIN zcf_zohoshortcode_manager as shorcodemanager ON fieldmanager.shortcode_id=shorcodemanager.shortcode_id AND fieldmanager.field_id='$selectedfields' AND shorcodemanager.shortcode_name = '$shortcode_name'", $shortcode_name));
        $rel_id = $enable_showfields[0]->rel_id;
        $shortcode_id = $enable_showfields[0]->shortcode_id;
        $field_id = $enable_showfields[0]->field_id;
        $enable_crmfields = $wpdb->query("update zcf_zohocrm_formfield_manager set hiddenfield = '0' , editupdate = '0' where rel_id ='$rel_id' and shortcode_id = '$shortcode_id'");
        $wpdb->query("update zcf_zohocrmform_field_manager set editupdate = '0' where field_id ='$field_id'");
    }

    function zcfupdateFieldsOrder($field_order, $shortcode_name) {
        $field_order = array_flip($field_order);
        global $wpdb;
        $get_shortcode_id = $wpdb->get_results($wpdb->prepare("select shortcode_id from zcf_zohoshortcode_manager where shortcode_name = %s and crm_type = %s", $shortcode_name, 'crmformswpbuilder'));
        $shortcode_id = $get_shortcode_id[0]->shortcode_id;
        $get_existing_field_order = $wpdb->get_results($wpdb->prepare("select field_id,rel_id, form_field_sequence from zcf_zohocrm_formfield_manager where shortcode_id = %d order by form_field_sequence", $shortcode_id));
        $i = 0;
        foreach ($get_existing_field_order as $key => $ffOrder) {
            $updates_orders = $wpdb->query("update zcf_zohocrm_formfield_manager set form_field_sequence ='" . $field_order[$ffOrder->field_id] . "' where rel_id ={$ffOrder->rel_id} ");
            $i++;
        }
    }

}

class zcfManageShortcodesActions {

    public $nonceKey = null;

    public function __construct() {
        require_once(ZCF_BASE_DIR_URI . 'includes/crmcustomfunctions.php');
        $helperObj = new zcfcustomfunctions();
    }

    public function zcfFieldorderIndex($request) {
        $data = array();
        return $data;
    }

    public function zcfFieldOrderexecuteView($request) {
        $data = array();
        $data['plugin_url'] = ZCF_BASE_DIR_URI;
        $data['onAction'] = 'onCreate';
        $data['siteurl'] = site_url();
        $data['nonce_key'] = $this->nonceKey;
        return $data;
    }

    public function zcfOrderManageFieldsList($request) {
        $data = $request;
        return $data;
    }

    public function zcfCrmManageFieldsLists($shortcode, $crmtype, $module, $bulkaction, $chkarray, $labelarray, $orderarray, $defaultvalue, $inputtype) {
        $labelArray = stripslashes($labelarray);
        $FieldOperation = new zcffieldoptions();
        $crmformsfieldData = new zcffieldlistDatamanage();
        $config_leads_fields = $crmformsfieldData->zcfformfieldsPropsettings($shortcode);
        $chkArray = json_decode(stripslashes($chkarray));
        $orderArray = json_decode(stripslashes($orderarray));
        $labelArray = stripslashes($labelarray);
        $newlabelarray = json_decode($labelArray);

        $inputtype = $inputtype;
        if ($inputtype == 'select-multiple' || $inputtype == 'select') {
            $defaultvalue = serialize($defaultvalue);
        } else {
            $defaultvalue = $defaultvalue;
        }

        if (isset($bulkaction)) {
            $fieldpostions = array();
            $fieldLabelDisplay = array();
            if (!empty($config_leads_fields['fields'])) {

                foreach ($config_leads_fields['fields'] as $index => $fInfo) {
                    $current_field_positions[$fInfo['field_id']] = $fInfo['order'];
                }
            }
            if (!empty($orderArray)) {
                foreach ($orderArray as $key1 => $value1) {
                    $new_field_positions[$key1] = $value1;
                }
            }
            $bulkaction = isset($bulkaction) ? $bulkaction : 'enable_field';
            $shortcode_name = $shortcode;
            switch ($bulkaction) {
                case 'Enable Field':
                    $FieldOperation->zcfenableFields($chkarray, $shortcode_name);
                    break;
                case 'Disable Field':
                    $FieldOperation->zcfdisableFields($chkarray, $shortcode_name);
                    break;
                case 'Update Order':
                    $FieldOperation->zcfupdateFieldsOrder($new_field_positions, $shortcode_name);
                    break;
                case 'Enable Mandatory':
                    $FieldOperation->zcfenableMandatoryFields($chkarray, $shortcode_name);
                    break;
                case 'Disable Mandatory':
                    $FieldOperation->zcfdisableMandatoryFields($chkarray, $shortcode_name);
                    break;
                case 'Hidden Enable Field':
                    $FieldOperation->zcfenableHiddenFields($chkarray, $shortcode_name);
                    break;
                case 'Hidden Disable Field':
                    $FieldOperation->zcfdisableHiddenFields($chkarray, $shortcode_name);
                    break;
                case 'Default value':
                    $FieldOperation->zcfdefaultvalueFields($chkarray, $shortcode_name, $defaultvalue);
                    break;
                case 'Save Display Label':
                    $FieldOperation->zcfsaveFieldLabelDisplay($labelarray, $chkarray, $shortcode_name);
                    break;
            }
        }
        
        $get_edit_shortcode = $shortcode;
        $thirdPartyPlugin = get_option('Thirdparty_' . $shortcode);
        $getThirdpartyTitle = get_option($get_edit_shortcode);
        if ($thirdPartyPlugin == 'contactform') {
            $title = $crmtype . '-' . $module . '-' . $shortcode;
            $obj = new ZcfCallMShortcodeObj();
            $get_edit_shortcode = $shortcode;
            $getThirdpartyTitle = get_option($get_edit_shortcode);

            if (!empty($getThirdpartyTitle)) {
                $title = $getThirdpartyTitle;
            } else {
                $title = $get_edit_shortcode;
            }
            $obj->ZcfformatContactFields($thirdPartyPlugin, $title, $shortcode);
        }




        $data = array();

        return $data;
    }

    public static function zcfsynceditUploadField($module, $layoutname, $formname, $layoutId, $shortcode) {
        global $adminmenulable, $wpdb;
        $crmtype = 'crmformswpbuilder';
        $moduleslug = rtrim(strtolower($module), "s");
        $tmp_option = "crmforms_{$crmtype}_{$moduleslug}_fields-tmp";
        // Function call
        $shortcodeObj = new zcffieldlistDatamanage();
        $OverallFunctions = new zcfcustomfunctions();
        $is_redirection = '';
        $url_redirection = '';
        $google_captcha = '';
        $config_fields['crm'] = $crmtype;
        $users_list = get_option('crm_users');
        $assignee = $users_list['users'][0]['email'];
        $fields = $wpdb->get_results($wpdb->prepare("select *from zcf_zohoshortcode_manager where shortcode_name = '" . $shortcode . "'"));

        $config_fields = $shortcodeObj->zcffieldsPropsettings($crmtype, $module, $layoutname);
        foreach ($config_fields as $field) {
            $shortcodeObj->zcfinsertFormFields($shortcode_id, $field->field_id, $field->field_mandatory, '1', $field->field_type, $field->field_values, $field->field_sequence, $field->field_label);
        }

        $config_shortcodes = get_option("zcf_crmfields_shortcodes");
        $config_shortcodes[$randomstring] = $config_fields;
        $details = array();
        $details['shortcode'] = $randomstring;
        $details['module'] = $module;
        $details['crmtype'] = $crmtype;
        return $details;
    }

    public static function zcfupdateState($value, $formfieldsLength, $shortcode_id) {
        $fieldsListarray = stripslashes($value);
        global $wpdb;
        $formfieldsLength = $formfieldsLength + 1;
        $shortcode_array = $wpdb->get_results("select * from zcf_zohocrm_formfield_manager where shortcode_id='" . $shortcode_id . "'");
        $shortcode_count = sizeof($shortcode_array) + 10;

        if (!empty($value)) {
            $i = $shortcode_count;
            foreach ($value['formfieldIds'] as $key1 => $value1) {

                $wpdb->update('zcf_zohocrm_formfield_manager', array('state' => "1", 'form_field_sequence' => $shortcode_count), array('rel_id' => $value1));
                $shortcode_count++;
            }
        }
    }

    public static function zcfdeleteFieldsState($value) {
        global $wpdb;
        $wpdb->update('zcf_zohocrm_formfield_manager', array('state' => "0"), array('rel_id' => $value));
    }

    public static function zcfCreateShortcode($module, $layoutname, $formname, $layoutId) {
        global $adminmenulable;
        $crmtype = "crmformswpbuilder";
        $moduleslug = rtrim(strtolower($module), "s");
        $tmp_option = "crmforms_crmformswpbuilder_{$moduleslug}_fields-tmp";
        $shortcodeObj = new zcffieldlistDatamanage();
        $OverallFunctions = new zcfcustomfunctions();
        $randomstring = $OverallFunctions->zcf_CreateFieldShortcode($crmtype, $module);
        $is_redirection = '';
        $url_redirection = '';
        $google_captcha = '';
        $config_fields['crm'] = $crmtype;
        $users_list = get_option('crm_users');
        $assignee = $users_list['users'][0]['email'];
        $shortcode_details['name'] = $randomstring;
        $shortcode_details['type'] = 'post';
        $shortcode_details['assignto'] = $assignee;
        $shortcode_details['isredirection'] = $is_redirection;
        $shortcode_details['urlredirection'] = $url_redirection;
        $shortcode_details['captcha'] = $google_captcha;
        $shortcode_details['crm_type'] = $crmtype;
        $shortcode_details['module'] = $module;
        $shortcode_details['errormesg'] = '';
        $shortcode_details['successmesg'] = '';
        $shortcode_details['duplicate_handling'] = '';
        $shortcode_details['formname'] = $formname;
        $adminmenulable->zcf_setShortcodeDetails($shortcode_details);
        $shortcode_id = $shortcodeObj->zcfformScodelists($shortcode_details, $mode = "create", $layoutname, $formname, $layoutId);
        $config_fields = $shortcodeObj->zcffieldsPropsettings($crmtype, $module, $layoutname);
        foreach ($config_fields as $field) {
            $field_mandatory = $field->field_mandatory;
            if ($field_mandatory == '1') {
                $state = 1;
            } else {
                $state = 0;
            }
            $shortcodeObj->zcfinsertFormFields($shortcode_id, $field->field_id, $field->field_mandatory, $state, $field->field_type, $field->field_values, $field->field_sequence, $field->field_label);
        }

        $config_shortcodes = get_option("zcf_crmfields_shortcodes");
        $config_shortcodes[$randomstring] = $config_fields;
        $details = array();
        $details['shortcode'] = $randomstring;
        $details['module'] = $module;
        $details['crmtype'] = $crmtype;
        return $details;
    }

    public function zcfDeleteShortcode($shortcode) {
        global $wpdb;
        $data = array();
        $delete_short = $shortcode;
        $deletedata = $wpdb->get_results("select shortcode_id from zcf_zohoshortcode_manager where shortcode_name = '$delete_short'");
        $deleteid = $deletedata[0]->shortcode_id;
        $delete_shortcode = $wpdb->query("delete from zcf_zohoshortcode_manager where shortcode_id = '$deleteid'");
        $delete_shortcode_fields = $wpdb->query("delete from zcf_zohocrm_formfield_manager where shortcode_id = '$deleteid'");
        return $deletedata;
        exit;
    }

}

class ZcfCallMShortcodeObj extends zcfManageShortcodesActions {

    private static $_instance = null;

    public static function ZcfgetInstance() {
        if (!is_object(self::$_instance))
            self::$_instance = new ZcfCallMShortcodeObj();
        return self::$_instance;
    }

    public function ZcfformatContactFields($thirdparty_form, $title, $shortcode) {
        global $wpdb;
        $word_form_enable_fields = $wpdb->get_results("select a.rel_id,a.zcf_field_mandatory,a.custom_field_type,a.custom_field_values,a.display_label,a.field_id,c.field_name ,a.defaultvalues,a.hiddenfield from zcf_zohocrm_formfield_manager as a join zcf_zohoshortcode_manager as b join zcf_zohocrmform_field_manager as c where b.shortcode_id=a.shortcode_id and b.shortcode_name='{$shortcode}' and a.state=1 and c.field_id=a.field_id order by form_field_sequence");
       echo "<pre>";
        print_r($word_form_enable_fields);
         echo "</pre>";
        $checkid = $wpdb->get_var($wpdb->prepare("select thirdpartyid from zcf_contactformrelation where shortcode =%s and thirdparty=%s", $shortcode, 'contactform'));

        if (!empty($checkid)) {
            $wpdb->query($wpdb->prepare("delete from zcf_contactformrelation where thirdpartyformid=%d", $checkid));
        }
        $contact_array = '';

        foreach ($word_form_enable_fields as $key => $value) {
            $mandatory ='';
            $type = $value->custom_field_type;
            $labl = $value->display_label;
            $label = $labl;
            $name = $value->field_name;
            $mandatory = $value->zcf_field_mandatory;
            $cont_array = array();
            $cont_array = unserialize($value->custom_field_values);
            $classhidden = 'class:tt';
            $classtrHidden = 'dB';
            $defaultvalue = '';
             if ($mandatory == 0) {
                $man = "";
            } else {
                $man = "*";
            }
            if ($value->hiddenfield == 1) {
                $classhidden = 'class:hidden';
                $classtrHidden = 'dN';
                if ($type == 'multiselectpicklist') {
                    $cont_array = unserialize($value->defaultvalues);
                } else {
                    $defaultvalue = $value->defaultvalues;
                }
            }

            $string = "";
            if (!empty($cont_array)) {

                foreach ($cont_array as $val) {
                    $string .= "\"{$val['value'][actual_value]}\" ";
                }
            }
            $str = rtrim($string, ',');
            if ($value->hiddenfield == 1) {
                if ($type == 'picklist') {
                    unset($str);
                    $str = "'" . $value->defaultvalues . "'";
                }
            }
           

            switch ($type) {
                case 'phone':
                case 'currency':
                case 'text':
                case 'integer':
                case 'string':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[text" . $man . " " . $name . " " . $classhidden . " '" . $defaultvalue . "'] </p>";
                    break;
                case 'email':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[email" . $man . " " . $name . " " . $classhidden . "] </p>";
                    break;
                case 'website':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[url" . $man . " " . $name . " " . $classhidden . "] </p>";
                    break;
                case 'picklist':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[select" . $man . " " . $name . " " . $str . " ] </p>";
                    $str = "";
                    break;
                case 'multiselectpicklist':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[select" . $man . " " . $name . " multiple " . $str . "] </p>";
                    $str = "";
                    break;
                case 'boolean':
                    $contact_array .= "<p class='" . $classtrHidden . "'>[checkbox" . $man . " " . $name . "  label_first '" . $name . "' ] </p>";
                    break;
                case 'date':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[date" . $man . " " . $name . " min:1950-01-01 max:2050-12-31 placeholder \"YYYY-MM-DD\" ] </p>";
                    break;
                case 'datetime':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[date" . $man . " " . $name . " min:1950-01-01 max:2050-12-31 placeholder \"YYYY-MM-DD\" ] </p>";
                    break;
                case '':
                    $contact_array .= "<p class='" . $classtrHidden . "'>" . $label . "" . $man . "<br />[text" . $man . " " . $name . " ] </p>";
                    break;
                default:
                    break;
            }
        }
        $contact_array .= "<p><br /> [submit " . " \"Submit\"" . "]</p>";
        $meta = $contact_array;
        $checkid = $wpdb->get_var($wpdb->prepare("select thirdpartyid from zcf_contactformrelation inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID = zcf_contactformrelation.thirdpartyid and {$wpdb->prefix}posts.post_status='publish' where shortcode =%s and thirdparty=%s", $shortcode, 'contactform'));

        if (empty($checkid)) {
            $contform = array(
                'post_title' => $title,
                'post_content' => $contact_array,
                'post_type' => 'wpcf7_contact_form',
                'post_status' => 'publish',
                'post_name' => $shortcode
            );
            $id = wp_insert_post($contform);
            $htmlcontent2 = "[contact-form-7 id=\"$id\" title=\"$shortcode\"]";
            $contform2 = array(
                'post_title' => $id,
                'post_content' => $htmlcontent2,
                'post_type' => 'post',
                'post_status' => 'publish',
                'post_name' => $id
            );
            wp_insert_post($contform2);

            $post_id = $id;
            $meta_key = '_form';
            $meta_value = $meta;
            update_post_meta($post_id, $meta_key, $meta_value);
            $wpdb->query("update zcf_contactformrelation set thirdpartyid = {$id} where thirdparty='contactform' and shortcode ='{$shortcode}'");
        } else {

            $wpdb->update($wpdb->posts, array('post_content' => $contact_array, 'post_title' => $title), array('ID' => $checkid));
            $wpdb->update($wpdb->postmeta, array('meta_value' => $meta), array('post_id' => $checkid, 'meta_key' => '_form'));
            $id = $checkid;
        }
        $thirdPartyPlugin = $thirdparty_form;
        $obj = new ZcfCallMShortcodeObj();
        $obj->ZcfcontactFormRelation($shortcode, $id, $thirdPartyPlugin, $word_form_enable_fields);
    }

    public function ZcfcontactFormRelation($shortcode, $id, $thirdparty, $zcfenableFields) {
        global $wpdb;
        $checkid = $wpdb->get_var($wpdb->prepare("select thirdpartyid from zcf_contactformrelation where shortcode =%s", $shortcode));
        if (empty($checkid)) {
            $wpdb->insert('zcf_contactformrelation', array('shortcode' => $shortcode, 'thirdparty' => $thirdparty, 'thirdpartyid' => $id));
        }
        foreach ($zcfenableFields as $value) {

            $wpdb->insert('zcf_contactformrelation', array('crmformsshortcodename' => $shortcode, 'crmformsfieldid' => $value->rel_id, 'crmformsfieldslable' => $value->display_label, 'thirdpartypluginname' => $thirdparty, 'thirdpartyformid' => $id, 'thirdpartyfieldids' => $value->field_name));
        }
    }

}
