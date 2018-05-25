<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class zcfcontactformfieldmapping {

    public function zcfget_user_assignedto($shortcode_option) {
        $crm_users_list = get_option('crm_users');
        $zohocrmformname = 'crmformswpbuilder';
        $assignedtouser_config = get_option($shortcode_option);
        $assignedtouser_config_leads = $assignedtouser_config['thirdparty_assignedto'];
        $webform_module = "";
        $webform_module = '<select class="form-control"  name="assignedto" id="assignedto">';
        $webform_module_lists = "";
        if (isset($crm_users_list['users']))
            for ($i = 0; $i < count($crm_users_list['users']); $i++) {
                $webform_module_lists .= "<option id='{$crm_users_list['users'][$i]['email']}' value='{$crm_users_list['users'][$i]['email']}'";
                if ($crm_users_list['users'][$i]['id'] == $assignedtouser_config_leads) {
                    $webform_module_lists .= " selected";
                }

                $webform_module_lists .= ">{$crm_users_list['users'][$i]['email']}</option>";
            }
        $webform_module .= $webform_module_lists;
        $webform_module .= "</select>";
        return $webform_module;
    }

    public function zcfget_mapping_field_config1() {
        global $wpdb;
        $layoutarray = $wpdb->get_results("select distinct(api_name),plural_label from zcf_zohocrm_list_module where  api_name !='' and api_name NOT IN('Visits','Vendors','Tasks','Social','Sales_Orders','Projects','Approvals','Products','Solution','Invoice','Estimate','Reports','Quotes','Purchase_Orders','WPjects','WPducts','Price_Books','Deals','Notes','Invoices','Home','Feeds','Events','Emails','Documents','Dashboards','Campaigns','Calls','Attachments','ApWPvals','Activities');");
        $rulearray = $resultaiss = $wpdb->get_results("select * from zcf_zohocrm_assignmentrule");
       $webform_layout_lists = "";
       $webform_layout_lists .= "<div>
		<div class='form-group col-md-12 mt20'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Your Module </label></div>
                <div class='exist_mapping col-md-4'> <select id='map_thirdparty_module' class='selectpicker form-control' data-live-search='false' name='map_thirdparty_module'  onchange='selectThirdModule(this,$siteurl)'><option value=''>Select Module</option>";
        foreach ($layoutarray as $key => $value) {
           $webform_layout_lists .= "<option value='" . $value->api_name . "'>" . $value->plural_label . "</option>";
        }
       $webform_layout_lists .= "</select><span class='smaill-loading-image'></span></div></div><br><br>";



       $webform_layout_lists .= "<div id='layout-third-module'>
		<div class='form-group col-md-12 mt20'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Select Layout </label></div>
                <div class='exist_mapping col-md-4'> <select id='choose-thirdleads-layout' name='choose-thirdleads-layout' class='form-control' onchange='selectThirdlayout(this)'><option value=''>Select Layout</option>";

       $webform_layout_lists .= "</select></div></div><br><br>";
       $webform_layout_lists .= "<div class='form-group col-md-12 mb50' id='thirdparty-plugin-list'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose your Form Type </label></div>
                <div class='exist_mapping col-md-4'> <select id='map_thirdparty_form' class='selectpicker form-control' data-live-search='false' name='map_thirdparty_form' onchange='getMappingConfiguration(this.value)'>";
       $webform_layout_lists .= "<option value='none'>None</option>
				<option value='contactform'>Contact Form</option>
				</select></div></div></div>";
    }

    public function zcfget_mapping_field_config($tp_module, $cForm_namePlugin) {
        global $wpdb;
        $zohocrmformname = 'crmformswpbuilder';
        $save_form_id = array();
        $contact_option_name = $zohocrmformname . "_zcf_contact";
        $list_of_shortcodes = $wpdb->get_results($wpdb->prepare("select option_name from $wpdb->options where option_name like %s", "$contact_option_name%"));
        if (!empty($list_of_shortcodes)) {
            foreach ($list_of_shortcodes as $list_key => $list_val) {
                $shortcode_name = $list_val->option_name;
                $form_id = explode($contact_option_name, $shortcode_name);
                $save_form_id[] = $form_id[1];
            }
        }
        $get_existing_forms = $wpdb->get_results($wpdb->prepare("select ID,post_title from $wpdb->posts where post_type=%s", 'wpcf7_contact_form'));
        $cont_form_titles = array();
        $i = 0;
        foreach ($get_existing_forms as $zcf_cont_key => $zcf_cont_title) {
            $i++;
            $cont_form_titles[$i]['title'] = $zcf_cont_title->post_title;
            $cont_form_titles[$i]['id'] = $zcf_cont_title->ID;
        }
        $html = "";
        $html = "<div class='form-group col-md-12'> <div class='exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label'> Choose Any One Of the Form</label></div>
                <div class='exist_mapping col-md-4'> <select id='thirdparty_form_title' class='selectpicker form-control' data-live-search='false' name='thirdparty_form_title' onchange='thirdparty_form_title_change()'>";
        $option_content = '';
        $option_content = "<option value='--None--'>--None123--</option>";
        foreach ($cont_form_titles as $option_key => $option_value) {
            $form_id = $option_value['id'];
            $title = $option_value['title'];
            if (!in_array($form_id, $save_form_id)) {
                $option_content .= "<option value='{$form_id}'>$title</option>";
            }
        }
        $html .= $option_content;
        $html .= "</select></div></div></div>";
        print_r($html);
        die;
    }

    public function zcfget_contactform_fields() {
        global $wpdb;
        $zohocrmformname = 'crmformswpbuilder';
        $cForm7_module = sanitize_text_field($_REQUEST['third_module']);
        $cForm_form_name = sanitize_text_field($_REQUEST['form_title']);
       $cForm_layout_name = sanitize_text_field($_REQUEST['layoutname']);
        $cForm_namePlugin = sanitize_text_field($_REQUEST['third_plugin']);
        $shortcode = $zohocrmformname . "_zcf_contact" . $cForm_form_name;
        $config = get_option($shortcode);
        $contact_config = $config['fields'];
        $assigned_to_user = $this->zcfget_user_assignedto($shortcode);
        $layoutname = isset($config['layoutname']);
        $layoutId = isset($config['layoutId']);
        $contact_config = $config['fields'];
        $cForm_fieldsoptions = '';
        $cForm_fieldsoptions .= "<div></div>";
        $cForm_fieldsoptions .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
        if ($cForm7_module == "Leads") {
            $cForm_fieldsoptions .= "Lead Owner";
            $cForm_fieldsoptions .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
        } else if ($cForm7_module == "Contacts") {
            $cForm_fieldsoptions .= "Contact Owner";
            $cForm_fieldsoptions .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
        }
        $cForm_fieldsoptions .= "</label></div></div>";
        if ($cForm_form_name != '--None--') {
            $get_json_array = $wpdb->get_results($wpdb->prepare("select ID,post_content from $wpdb->posts where ID=%d", $cForm_form_name));
            $contact_post_content = $get_json_array[0]->post_content;
            $fields = $this->zcfgetTBetBrackets($contact_post_content);
            $i = 0;
            foreach ($fields as $cfkey => $cfval) {
                if (preg_match('/\s/', $cfval)) {
                    $final_arr = explode(' ', $cfval);
                    $contact_form_labels[$i] = rtrim($final_arr[1], ']');
                    $i++;
                }
            }
            $crmformsfieldDataObj = new zcffieldlistDatamanage();
            $crm_fields = $crmformsfieldDataObj->zcffieldsPropsettings($zohocrmformname, $cForm7_module,$cForm_layout_name);
            $j = 1;

            $js_mandatory_fields = array();

            foreach ($crm_fields as $crm_field_key => $crm_fields_vals) {
                $crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
                if ($crm_fields_vals->field_mandatory == 1) {
                    if (!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
                        $js_mandatory_fields[] = $crm_fields_vals->field_name;
                }
                $j++;
            }
            $js_mandatory_array = json_encode($js_mandatory_fields);
            $crm_field_options = '';
            $crm_field_options .= "<option>--None--</option>";
            foreach ($crm_field_labels as $field_key => $crm_field_label) {

                $crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
            }


            $fields_html = '';
            $fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'> <div class='leads-builder-heading'>ContactForm Fields</div></div><div class='exist_mapping col-md-4'> <div class='leads-builder-heading'>CRM Fields</div></div></div> ";
            $i = 1;

            foreach ($contact_form_labels as $cont_id => $cont_label) {
                $fields_html .= "<div class='form-group col-md-12'>
					<div class='col-md-6'><label class='leads-builder-label'> $cont_label </label></div>
			<input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$cont_label' />";

                $fields_html .= "<div class='col-md-4'><select class=' selectpicker form-control'  name='crm_fields_$i' id='crm_fields_$i' >";
                $crm_field_options = '';
                $crm_field_options .= "<option value=''>--None--</option>";
                foreach ($crm_field_labels as $field_key => $crm_field_label) { // Prepare crm fileds drop down
                    $crm_field_options .= "<option value='{$crm_field_label}'";
                    if (isset($contact_config)) {
                        foreach ($contact_config as $config_key => $config_val) { // configuration
                            if ($cont_label == $config_key && $crm_field_label == $config_val) { //match label and fieldname
                                $crm_field_options .= "selected=selected"; //select when the configuration exist
                            }
                        }
                    }
                    $crm_field_options .= "> $field_key</option>";
                }
                $fields_html .= $crm_field_options;
                $fields_html .= "</select>
					</div>
					</div>";
                $i++;
            }
        } else {
            $fields_html = "";
            $cForm_fieldsoptions = "";
            $fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
        }
        $fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
        $fields_html .= "<input type='hidden' value='$cForm7_module' id='module'>";
        $fields_html .= "<input type='hidden' value='$zohocrmformname' id='active_crm'>";
        $fields_html .= "<input type='hidden' value='$cForm_form_name' id='form_name'>";
        $fields_html .= "<input type='hidden' value='$cForm_namePlugin' id='thirdparty_plugin'>";
        $fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
        $fields_html .= '<input type="hidden" name="modulename" id="modulename"  value=""><input type="hidden" name="layoutname" id="layoutname"  value="' . $layoutname . '"><input type="hidden" name="layoutId" id="layoutId" value="' . $layoutId . '">';
        $fields_html .= "</div>";
        $html_data_array = array();
        $html_data_array['map_options'] = $cForm_fieldsoptions;
        $html_data_array['fields_html'] = $fields_html;
        print_r(json_encode($html_data_array));
        die;
    }

    public function zcfgetTBetBrackets($post_content) {

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

    public function zcfmaping_contactform_fields() {
        $config_data = $_REQUEST['post_data'];
        $form_title = sanitize_text_field($_REQUEST['form_title']);
        $cForm7_name = sanitize_text_field($_REQUEST['third_plugin']);
        $cForm7_module = sanitize_text_field($_REQUEST['third_module']);
        $cForm7_crm = sanitize_text_field($_REQUEST['third_crm']);
        $cForm7_duplicate = sanitize_text_field($_REQUEST['third_duplicate']);
        $cForm7_assignedto = sanitize_text_field($_REQUEST['third_assigedto']);
        $cForm7_assignedto_name = sanitize_text_field($_REQUEST['assignedto_name']);
        $layoutname = sanitize_text_field($_REQUEST['layoutname']);
        $layoutId = sanitize_text_field($_REQUEST['layoutId']);

        foreach ($config_data as $data_key => $data_val) {
            if (preg_match('/^thirdpartyfield/', $data_key)) {
                $thirdparty_key = ltrim($data_key, 'thirdpartyfield_');
                $thirdparty_labels[$thirdparty_key] = $data_val; // Make thirdparty label array
            }

            if (preg_match('/^crm_fields/', $data_key)) {
                $crm_field_key = ltrim($data_key, 'crm_fields_');
                if ($data_val != '--None--') {
                    $crm_labels[$crm_field_key] = $data_val; // Make crm labels array -take only mapped values
                }
            }
        }
        $get_keys_crm_labels = array_keys($crm_labels); // get keys from crm labels- to prepare mapped thirdparty labels

        foreach ($thirdparty_labels as $tp_key => $tp_val) {
            foreach ($get_keys_crm_labels as $index_val) {
                if ($tp_key == $index_val) {//check crm key index with thirdparty label  array
                    $thirdparty_mapped_labels[$tp_key] = $tp_val;  // prepare mapped values for thirdparty label array
                }
            }
        }
        $mapped_array = array_combine($thirdparty_mapped_labels, $crm_labels); // Combine final mapped array(thirdparty, crm fields)
        $final_mapped_array = array();
        $final_mapped_array['form_title'] = $form_title;
        $final_mapped_array['third_plugin'] = $cForm7_name;
        $final_mapped_array['third_module'] = $cForm7_module;
        $final_mapped_array['thirdparty_crm'] = $cForm7_crm;
        $final_mapped_array['thirdparty_duplicate'] = $cForm7_duplicate;
        $final_mapped_array['thirdparty_assignedto'] = $cForm7_assignedto;
        $final_mapped_array['thirdparty_assignedto_name'] = $cForm7_assignedto_name;
        $final_mapped_array['layoutname'] = $layoutname;
        $final_mapped_array['fields'] = $mapped_array;
        $final_mapped_array['layoutId'] = $layoutId;
        $zohocrmformname = 'crmformswpbuilder';
        $option = $zohocrmformname . '_zcf_contact' . $form_title;
        $check_exist_array = get_option($option);
        if (!empty($check_exist_array['tp_roundrobin'])) {
            $final_mapped_array['tp_roundrobin'] = $check_exist_array['tp_roundrobin'];
        }
        update_option($option, $final_mapped_array);
        die;
    }


    public function zcf_mapped_fields_config() {
        global $wpdb;
        $zohocrmformname = 'crmformswpbuilder';
        $cForm7_module = sanitize_text_field($_REQUEST['third_module']);
        $cForm_form_name = sanitize_text_field($_REQUEST['form_id']);
        $cform7_title = sanitize_text_field($_REQUEST['form_title']);
        $cForm_namePlugin = sanitize_text_field($_REQUEST['third_plugin']);
        $layoutname = sanitize_text_field($_REQUEST['layoutname']);
        $shortcode = $zohocrmformname . "_zcf_contact" . $cForm_form_name;
        $config = get_option($shortcode);
        $layoutname = $config['layoutname'];
        $layoutId = $config['layoutId'];
        $contact_config = $config['fields'];
        $assigned_to_user = $this->zcfget_user_assignedto($shortcode);
        $cForm_fieldsoptions = '';
        $cForm_fieldsoptions .= "<div>	
			<div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Module Type</label> </div><div class='col-md-4'> $cForm7_module</div></div>
                        <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Type </label></div><div class='col-md-4'> $cForm_namePlugin</div></div>
                        
                        <div class='form-group col-md-12'><div class='exist_mapping col-md-6'><label id='innertext' class='leads-builder-label'> Form Title </label></div><div class='exist_mapping col-md-4'> $cform7_title</div></div>
			</div>";

        $cForm_fieldsoptions .= "<div><div class='form-group col-md-12'> <div class='assign_leads exist_mapping col-md-6'> <label id='innertext' class='leads-builder-label' >";
        if ($cForm7_module == "Leads") {
            $cForm_fieldsoptions .= "Lead Owner";
            $cForm_fieldsoptions .= "</label></div><div class='exist_mapping col-md-4'> $assigned_to_user</div></div><div class='form-group col-md-12'><div col-md-6>";
        } else if ($cForm7_module == "Contacts") {
            $cForm_fieldsoptions .= "Contact Owner";
            $cForm_fieldsoptions .= "</div><div class='exist_mapping col-md-4'> $assigned_to_user</div>";
        }
        $cForm_fieldsoptions .= "</label></div></div>";

        if ($cForm_form_name != '--None--') {
            $get_json_array = $wpdb->get_results($wpdb->prepare("select ID,post_content from $wpdb->posts where ID=%d", $cForm_form_name));
            $contact_post_content = $get_json_array[0]->post_content;
            $fields = $this->zcfgetTBetBrackets($contact_post_content);
            $i = 0;
            foreach ($fields as $cfkey => $cfval) {
                if (preg_match('/\s/', $cfval)) {
                    $final_arr = explode(' ', $cfval);
                    $contact_form_labels[$i] = rtrim($final_arr[1], ']');
                    $i++;
                }
            }
            $crmformsfieldDataObj = new zcffieldlistDatamanage();

            $crm_fields = $crmformsfieldDataObj->zcffieldsPropsettings($zohocrmformname, $cForm7_module, $layoutname);
            $j = 1;

            $js_mandatory_fields = array();
            foreach ($crm_fields as $crm_field_key => $crm_fields_vals) {
                $crm_field_labels[$crm_fields_vals->field_label] = $crm_fields_vals->field_name;
                if ($crm_fields_vals->field_mandatory == 1) {
                    if (!in_array($crm_fields_vals->field_name, $js_mandatory_fields))
                        $js_mandatory_fields[] = $crm_fields_vals->field_name;
                }
                $j++;
            }
            $js_mandatory_array = json_encode($js_mandatory_fields);
            $crm_field_options = '';
            $crm_field_options .= "<option>--None--</option>";
            foreach ($crm_field_labels as $field_key => $crm_field_label) {

                $crm_field_options .= "<option value='{$crm_field_label}'> $crm_field_label</option>";
            }
            $fields_html = '';
            $fields_html .= "<div><div class='form-group col-md-12'><div class='exist_mapping col-md-6'><div class='leads-builder-heading'> ContactForm Fields</div></div><div class='exist_mapping col-md-4'><div class='leads-builder-heading'> CRM Fields</div></div> </div>";
            $i = 1;
            foreach ($contact_form_labels as $cont_id => $cont_label) {
                $fields_html .= "<div class='form-group col-md-12'>
					<div class='col-md-6'><label class='leads-builder-label'> $cont_label </label></div>
			<input type='hidden' name='thirdpartyfield_$i' id='thirdpartyfield_$i' value='$cont_label' />";

                $fields_html .= "<div class='col-md-4'><select class='selectpicker form-control' style='width:150px;' name='crm_fields_$i' id='crm_fields_$i' >";
                $crm_field_options = '';
                $crm_field_options .= "<option value=''>--None--</option>";
                foreach ($crm_field_labels as $field_key => $crm_field_label) { 
                    $crm_field_options .= "<option value='{$crm_field_label}'";
                    foreach ($contact_config as $config_key => $config_val) { 
                        if ($cont_label == $config_key && $crm_field_label == $config_val) { 
                            $crm_field_options .= "selected=selected"; 
                        }
                    }
                    $crm_field_options .= "> $field_key</option>";
                }
                $fields_html .= $crm_field_options;
                $fields_html .= "</select>
					</div>
					</div>";
                $i++;
            }
        } else {
            $fields_html = "";
            $cForm_fieldsoptions = "";
            $fields_html .= "<span style='color:red;font-size:18px;margin-left:23%;'>Please choose any form</span>";
        }
        $fields_html .= "<input type='hidden' value='$i' id='total_field_count'>";
        $fields_html .= "<input type='hidden' value='$cForm7_module' id='module'>";
        $fields_html .= "<input type='hidden' value='$zohocrmformname' id='active_crm'>";
        $fields_html .= "<input type='hidden' value='$cForm_form_name' id='form_name'>";
        $fields_html .= "<input type='hidden' value='$cForm_namePlugin' id='thirdparty_plugin'>";
        $fields_html .= "<input type='hidden' value='$js_mandatory_array' id='crm_mandatory_fields'>";
        $fields_html .= '<input type="hidden" name="modulename" id="modulename"  value="' . $cForm7_module . '"><input type="hidden" name="layoutname"  id="layoutname" value="' . $layoutname . '"><input type="hidden" name="layoutId" id="layoutId"  value="' . $layoutId . '">';
        $fields_html .= "</div>";
        $html_data_array = array();
        $html_data_array['map_options'] = $cForm_fieldsoptions;
        $html_data_array['fields_html'] = $fields_html;
        print_r(json_encode($html_data_array));
        die;
    }

    function zcf_delete_mappedfields_config() {
        $zohocrmformname = 'crmformswpbuilder';
        $cForm_namePlugin = sanitize_text_field($_REQUEST['third_plugin']);
        $thirdparty_form_id = sanitize_text_field($_REQUEST['form_id']);
        $option_name = $zohocrmformname . '_zcf_contact' . $thirdparty_form_id;
        delete_option($option_name);
        die;
    }

}
