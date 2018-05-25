<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

include_once(ZCF_BASE_DIR_URI . 'includes/crmapiintergration.php');

class zcfcoreGetFields {

    public $username;
    public $accesskey;
    public $authtoken;
    public $url;
    public $result_emails;
    public $result_ids;
    public $resultducts;

    public function __construct() {
        require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
        zcfcheckAccessToken();
        $maincrmforms_helper_Obj = new zcfmaincorehelpers();
        $activateplugin = "crmformswpbuilder";
        $SettingsConfig = get_option("zcf_{$activateplugin}_settings");
        if (isset($_REQUEST['crmtype'])) {
            $SettingsConfig = get_option("zcf_{$_REQUEST['crmtype']}_settings");
        } else {
            $SettingsConfig = get_option("zcf_{$activateplugin}_settings");
        }

        $this->url = "";
        $this->authtoken = base64_decode(base64_decode(base64_decode($SettingsConfig['authtoken'])));
    }

    public function ZcfUserlogin() {
        $client = new zcfaccountApi();
        return $client;
    }

    public function zcfgetCrmFieldsList($module) {
        require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
        zcfcheckAccessToken();
        $client = $this->ZcfUserlogin();
        $SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
        $this->authtoken = base64_decode(base64_decode(base64_decode($SettingsConfig['authtoken'])));
        $result_array = $client->zcfGetModuleFields($module, "getFields", $this->authtoken);
        $config_fields = array();
        $AcceptedFields = Array('ownerlookup' => 'ownerlookup', 'multiselectlookup'=>'multiselectlookup','text' => 'text', 'textarea' => 'textarea', 'email' => 'email', 'double' => 'double', 'website' => 'website', 'lookup' => 'lookup', 'formula' => 'formula', 'boolean' => 'boolean', 'bigint' => 'bigint', 'integer' => 'integer', 'currency' => 'currency', 'autonumber' => 'autonumber', 'datetime' => 'datetime', 'date' => 'date', 'multiselectpicklist' => 'multiselectpicklist', 'phone' => 'phone', 'picklist' => 'picklist');

        $i = 0;

        foreach ($result_array['layouts'] as $key => $sectionMeta) {
            foreach ($sectionMeta['sections'] as $key => $sectionMeta1) {

                foreach ($sectionMeta1['fields'] as $key => $fieldMeta) {           
                    echo "<pre>";
                    print_r($fieldMeta);  
                    echo "</pre>";
                    
                    if ($fieldMeta['read_only'] == '') {
                        $config_fields['fields'][$i]['name'] = $fieldMeta['api_name'];
                        $config_fields['fields'][$i]['readonly'] = $fieldMeta['read_only'];
                        $config_fields['fields'][$i]['id'] = $fieldMeta['id'];
                        $config_fields['fields'][$i]['fieldname'] = $fieldMeta['api_name'];
                        $config_fields['fields'][$i]['label'] = $fieldMeta['field_label'];
                        $config_fields['fields'][$i]['display_label'] = $fieldMeta['field_label'];
                        $config_fields['fields'][$i]['publish'] = 1;
                        $config_fields['fields'][$i]['order'] = $fieldMeta['sequence_number'];
                        $config_fields['fields'][$i]['data_type'] = $fieldMeta['data_type'];
                        if(!empty($fieldMeta['json_type'])){
                             $config_fields['fields'][$i]['json_type'] = $fieldMeta['json_type'];
                        }else{
                             $config_fields['fields'][$i]['json_type'] = '';
                        }
                       
                        $config_fields['fields'][$i]['layout_name'] = $sectionMeta['name'];
                        $config_fields['fields'][$i]['layoutId'] = $sectionMeta['id'];
                        $config_fields['fields'][$i]['viewcreate_type'] = $fieldMeta['view_type']['create'];
                        if ($fieldMeta['required'] == 1) {
                            $config_fields['fields'][$i]['zcf_mandatory'] = 1;
                            $config_fields['fields'][$i]['mandatory'] = 2;
                        } else {
                            $config_fields['fields'][$i]['zcf_mandatory'] = 0;
                            $config_fields['fields'][$i]['mandatory'] = '';
                        }
                        $dataType = $fieldMeta['data_type'];
                        if (($dataType == 'multiselectpicklist') || ($dataType == 'picklist') || ($dataType == 'Radio')) {
                            $optionindex = 0;
                            $picklistValues = array();
                            foreach ($fieldMeta['pick_list_values'] as $option) {
                                $picklistValues[$optionindex]['label'] = $option;
                                $picklistValues[$optionindex]['value'] = $option;
                                $optionindex++;
                            }
                            $config_fields['fields'][$i]['type'] = Array('name' => $AcceptedFields[$dataType], 'picklistValues' => $picklistValues);
                        } else {
                            $config_fields['fields'][$i]['type'] = array('name' => $AcceptedFields[$dataType]);
                        }
                        $i++;
                    }
                }
            }
        }

        $config_fields['check_duplicate'] = 0;
        $config_fields['isWidget'] = 0;
        // $users_list = $this->zcfgetUsersList();
        $config_fields['assignedto'] = 'ilakk';
        $config_fields['module'] = $module;

        return $config_fields;
    }

    public function zcfgetUsersList() {
        require_once( ZCF_BASE_DIR_URI . "includes/crmoauthentication.php");
        zcfcheckAccessToken();
        $client = $this->ZcfUserlogin();
        $extraparams = "&type=ActiveUsers";
        $records = $client->zcfGetUserRecord("Users", "getUsers", $this->authtoken, "", "", $extraparams);
        update_option("crm_users", $records);

        return $records;
    }

    public function zcfgetUsersListHtml($shortcode = "") {
        $HelperObj = new zcfmaincorehelpers();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = "crmformswpbuilder";
        $formObj = new zcffieldlistDatamanage();
        if (isset($shortcode) && ( $shortcode != "" )) {
            $config_fields = $formObj->zcfFormPropSettings($shortcode);  // Get form settings 
        }
        $users_list = get_option('crm_users');

        // $users_list = $users_list[$activatedplugin];

        $html = "";
        $html = '<select class=" form-control" name="assignedto" id="assignedto">';
        $htmlcontent_option = "";

        if (isset($users_list['users'][0]['email']))
            for ($i = 0; $i < count($users_list['users']); $i++) {
                $htmlcontent_option .= "<option id='{$users_list['users'][$i]['email']}' value='{$users_list['users'][$i]['email']}'";
                if ($users_list['users'][$i]['id'] == $config_fields->assigned_to) {
                    $htmlcontent_option .= " selected";
                }
                $htmlcontent_option .= ">{$users_list['users'][$i]['email']}</option>";
            }

        $html .= $htmlcontent_option;
        $html .= "</select> ";
        return $html;
    }

    public function zcfgetAssignedToList() {
        $users_list = $this->zcfgetUsersList();
        for ($i = 0; $i < count($users_list['user_name']); $i++) {
            $user_list_array[$users_list['user_name'][$i]] = $users_list['user_name'][$i];
        }
        return $user_list_array;
    }

    public function zcf_mapUserCaptureFields($user_firstname, $user_lastname, $user_email) {
        $post = array();
        $post['First_Name'] = $user_firstname;
        $post['Last_Name'] = $user_lastname;
        $post[$this->zcfduplicateCheckEmailField()] = $user_email;
        return $post;
    }

    public function zcfassignedToFieldId() {
        return "Lead_Owner";
    }

    public function zcfcreateRecordOnUserCapture($module, $module_fields) {

        $client = $this->ZcfUserlogin();
        $post_fields['First Name'] = $module_fields['First_Name'];
        $post_fields['Last Name'] = $module_fields['Last_Name'];
        $post_fields[$this->zcfduplicateCheckEmailField()] = $module_fields[$this->zcfduplicateCheckEmailField()];
        $postfields = "<{$module}>\n<row no=\"1\">\n";
        if (isset($post_fields)) {
            foreach ($post_fields as $key => $value) {
                $postfields .= "<FL val=\"" . $key . "\">" . $value . "</FL>\n";
            }
        } else {
            foreach ($module_fields as $key => $value) {
                $postfields .= "<FL val=\"" . $key . "\">" . $value . "</FL>\n";
            }
        }
        $postfields .= "</row>\n</$module>";
        $record = $client->zcfFormDatainsert($module, "insertRecords", $this->authtoken, $postfields);
        if (isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) added successfully" )) {
            $data['result'] = "success";
            $data['failure'] = 0;
        } else {
            $data['result'] = "failure";
            $data['failure'] = 1;
            $data['reason'] = "failed adding entry";
        }
        return $data;
    }

    public function zcfreplace_key_function($module_fields, $key1, $key2) {
        $keys = array_keys($module_fields);
        $index = array_search($key1, $keys);
        if ($index !== false) {
            $keys[$index] = $key2;
            $module_fields = array_combine($keys, $module_fields);
        }
        return $module_fields;
    }

    public function zcfcreatenewRecord($module, $module_fields) {
        $client = $this->ZcfUserlogin();
        global $HelperObj;
        $maincrmforms_helper_Obj = new zcfmaincorehelpers();
        $activateplugin = $maincrmforms_helper_Obj->ActivatedPlugin;
        $moduleslug = $this->ModuleSlug = rtrim(strtolower($module), "s");
        $config_fields = get_option("crmforms_{$activateplugin}_{$moduleslug}_fields-tmp");
        $underscored_field = "";
        foreach ($config_fields['fields'] as $key => $fields) {  //      To add _ for field with spaces to capture the REQUEST
            if (count($exploded_fields = explode(' ', $fields['fieldname'])) > 1) {
                foreach ($exploded_fields as $exploded_field) {
                    $underscored_field .= $exploded_field . "_";
                }
                $underscored_field = rtrim($underscored_field, "_");
            } else {
                $underscored_field = $fields['fieldname'];
            }
            $config_underscored_fields[$underscored_field] = $fields['fieldname'];
            $underscored_field = "";
        }

        // Change checkbox value on => true


        foreach ($config_fields['fields'] as $checkbox_key => $checkbox_val) {
            foreach ($module_fields as $mod_cb_key => $mod_cb_val) {
                if ($checkbox_val['type']['name'] == 'boolean' && $mod_cb_key == $checkbox_val['name']) {

                    $module_fields[$mod_cb_key] = true;
                }
                if ($checkbox_val['type']['name'] == 'datetime' && $mod_cb_key == $checkbox_val['name']) {

                    $curDate = date('Y-m-d', strtotime($mod_cb_val));
                    $minHour = date('h:i:s', strtotime($mod_cb_val));
                    $customDate = $curDate . "T" . $minHour . "+05:30";
                    $module_fields[$mod_cb_key] = $customDate;
                }
            }
        }

        foreach ($module_fields as $field => $value) {
            if (array_key_exists($field, $config_underscored_fields)) {
                $post_fields[$config_underscored_fields[$field]] = $value; //urlencode($value);
            }
        }

        foreach ($module_fields as $key => $value) {
            $key = preg_replace('/_/', ' ', $key);
            $module_field[$key] = $value;
        }
        $module_fields = $module_field;
        $postfields = "<{$module}>\n<row no=\"1\">\n";

        if ($activateplugin == 'crmformswpbuilder') {
            if (!empty($module_fields)) {
                $module_fields = $this->zcfreplace_key_function($module_fields, 'Lead Owner', 'SMOWNERID');
            }
        }
        if (isset($post_fields)) {
            foreach ($post_fields as $key => $value) {
                $postfields .= "<FL val=\"" . $key . "\">" . $value . "</FL>\n";
            }
        } else {
            foreach ($module_fields as $key => $value) {
                $postfields .= "<FL val=\"" . $key . "\">" . $value . "</FL>\n";
            }
        }
        if (isset($_REQUEST['gclid'])) {
            $postfields .= '<FL val="GCLID">' . $_REQUEST['gclid'] . '</FL>';
        }
        $postfields .= "</row>\n</$module>";

        if (isset($module_fields['SMOWNERID'])) {
            $post_fields['SMOWNERID'] = $module_fields['SMOWNERID']; // Assign user in post_fields Array
        } else {
            $post_fields['SMOWNERID'] = '';
        }

        //Attachment
        if (isset($module_fields['attachments'])) {
            $attachments = $module_fields['attachments'];
        }
        if (isset($post_fields)) {
            $post_fields['layoutId'] = $module_fields['layoutId'];
            $postFieldsArray['data'][] = $post_fields;
            if ($module_fields['larId'] != '' || $module_fields['larId'] != null) {
                $postFieldsArray['lar_id'] = $module_fields['larId'];
            }

            $response = json_encode($postFieldsArray, true);
        }
        // New code for changing field_name into Lable for other languages

        $record = $client->zcfFormDatainsert($module, "insertRecords", $this->authtoken, $response, $attachments); //attachments
      
        if (isset($record['data'][0]['message']) && ( $record['data'][0]['message'] == "record added" )) {

            $data['result'] = "success";
            $data['failure'] = 0;
        } else {
            $data['result'] = "failure";
            $data['failure'] = 1;
            $data['reason'] = "failed adding entry";
        }

        return $data;
    }

    public function zcfupdateRecord($module, $module_fields, $ids_present) {
        $client = $this->ZcfUserlogin();
        $underscored_field = '';
        $config_underscored_fields = array();
        global $HelperObj;
        $maincrmforms_helper_Obj = new zcfmaincorehelpers();
        $activateplugin = $maincrmforms_helper_Obj->ActivatedPlugin;
        $moduleslug = $this->ModuleSlug = rtrim(strtolower($module), "s");
        $config_fields = get_option("crmforms_{$activateplugin}_{$moduleslug}_fields-tmp");
        foreach ($config_fields['fields'] as $key => $fields) {  //      To add _ for field with spaces to capture the REQUEST
            if (count($exploded_fields = explode(' ', $fields['fieldname'])) > 1) {
                foreach ($exploded_fields as $exploded_field) {
                    $underscored_field .= $exploded_field . "_";
                }
                $underscored_field = rtrim($underscored_field, "_");
            } else {
                $underscored_field = $fields['fieldname'];
            }
            $config_underscored_fields[$underscored_field] = $fields['fieldname'];
            $underscored_field = "";
        }
        foreach ($module_fields as $field => $value) {
            if (array_key_exists($field, $config_underscored_fields)) {
                $post_fields[$config_underscored_fields[$field]] = $value; //urlencode($value);
            }
        }

        // New code for changing field_name into Lable for other languages

        if (isset($post_fields)) {
            foreach ($config_fields['fields'] as $conf_key => $conf_val) {
                foreach ($post_fields as $post_key => $post_val) {
                    if ($post_key == $conf_val['fieldname']) {
                        unset($post_fields[$post_key]);
                        $post_fields[$conf_val['label']] = $post_val;
                    }
                }
            }
        } else {
            foreach ($config_fields['fields'] as $conf_key => $conf_val) {
                foreach ($module_fields as $module_key => $module_val) {
                    if ($module_key == $conf_val['fieldname']) {
                        unset($module_fields[$module_key]);
                        $module_fields[$conf_val['label']] = $module_val;
                    }
                }
            }
        }
        //End new code for other language

        $postfields = "<{$module}>\n<row no=\"1\">\n";
        if (isset($post_fields)) {
            foreach ($post_fields as $key => $value) {
                $postfields .= "<FL val=\"" . $key . "\">" . $value . "</FL>\n";
            }
        } else {
            foreach ($module_fields as $key => $value) {
                $postfields .= "<FL val=\"" . $key . "\">" . $value . "</FL>\n";
            }
        }
        $postfields .= "</row>\n</$module>";
        $config_fields = get_option("crmforms_crmformswpbuilder_fields_shortcodes");
        $extraparams = "&id={$ids_present}";
        $record = $client->zcfFormDatainsert($module, "zcfupdateRecords", $this->authtoken, $postfields, $extraparams);
        if (isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) updated successfully" )) {
            $data['result'] = "success";
            $data['failure'] = 0;
        } else {
            $data['result'] = "failure";
            $data['failure'] = 1;
            $data['reason'] = "failed adding entry";
        }
        return $data;
    }

    public function zcfcheckEmailPresent($module, $email) {
        $maincrmforms_helper_Obj = new zcfmaincorehelpers();
        $activateplugin = $maincrmforms_helper_Obj->ActivatedPlugin;
        $result_emails = array();
        $result_ids = array();
        $client = $this->ZcfUserlogin();
        $email_present = "no";
        $extraparams = "&searchCondition=(Email|=|{$email})"; // Old API Method for search record
        //$extraparams = "&criteria=(Email:$email)"; // New API method for search
        $records = $client->zcfGetRecords($module, "getSearchRecords", $this->authtoken, "Id , Email", "", $extraparams); // Replaced getSearchRecords by searchRecords
        if (isset($records['result'][$module]['row']['@attributes'])) {
            $result_lastnames[] = "Last Name";
            $result_emails[] = $email;
            $result_ids[] = $records['result'][$module]['row']['FL'];
            $email_present = "yes";
        } else {
            if (!empty($records) && isset($records['result']) && is_array($records['result'][$module]['row'])) {
                foreach ($records['result'][$module]['row'] as $key => $record) {
                    $result_lastnames[] = "Last Name";
                    $result_emails[] = $email;
                    $result_ids[] = $record['FL'];
                    $email_present = "yes";
                }
            }
        }
        $this->result_emails = $result_emails;
        $this->result_ids = $result_ids;
        if ($email_present == 'yes')
            return true;
        else
            return false;
    }

    public function zcfduplicateCheckEmailField() {
        return "Email";
    }

    public function zcfcheckcrmcontactPresent($module, $WPduct) {
        $maincrmforms_helper_Obj = new zcfmaincorehelpers();
        $activateplugin = $maincrmforms_helper_Obj->ActivatedPlugin;
        $result_emails = array();
        $result_ids = array();
        $client = $this->ZcfUserlogin();
        $WPduct_present = "no";
        $extraparams = "&criteria=(WPduct Name:$WPduct)"; // New Method for search record
        $records = $client->zcfGetRecords($module, "searchRecords", $this->authtoken, "WPduct Name", "", $extraparams); // // Replaced getSearchRecords by searchRecords
        if (isset($records['result'][$module]['row']['@attributes'])) {
            $resultducts[] = $WPduct;
            $result_ids[] = $records['result'][$module]['row']['FL'];
            $WPduct_present = "yes";
        } else {
            if (is_array($records['result'][$module]['row'])) {
                foreach ($records['result'][$module]['row'] as $key => $record) {
                    $resultducts[] = $WPduct;
                    $result_ids[] = $record['FL'];
                    $WPduct_present = "yes";
                }
            }
        }
        $this->resultducts = $resultducts;
        $this->result_ids = $result_ids;
        if ($WPduct_present == 'yes')
            return true;
        else
            return false;
    }

}
