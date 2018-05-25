<?php
if (!defined('ABSPATH'))
    exit;
?>
<input type="hidden" name="currentpageUrl" id="currentpageUrl" value="" />
<?php
$SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
$authtokens = $SettingsConfig['authtoken'];
if ($authtokens == '') {
    require_once( ZCF_BASE_DIR_URI . "includes/crmwebformgloablsetting.php");
} else {$current_url ='';
    ?>

    <div>
        <div style="width:98%;">
            <div >
                <?php
                require_once( ZCF_BASE_DIR_URI . "includes/crmcustomfunctions.php" );
                $crmformsFunctionsObj = new zcfcustomfunctions();
                $page = $_REQUEST['page'];
                $result = $crmformsFunctionsObj->zcf_FetchedCrmModuleDetails();
                require_once( ZCF_BASE_DIR_URI . "includes/crmcontactformfieldsmapping.php" );
                if (!$result['status']) {
                    $display_content = "<br>" . $result['content'] . " to create Forms <br><br>";
                    echo "<div style='font-weight:bold;  color:red; font-size:16px;text-align:center'> $display_content </div>";
                } else {
                    global $zohocrmdetails;
                    global $attrname;
                    global $migrationmap;
                    global $wpdb;
                    global $adminmenulable;
                    require_once( ZCF_BASE_DIR_URI . "includes/crmshortcodefunctions.php" );
                    $HelperObj = new zcfmaincorehelpers();
                    $module = $HelperObj->Module;
                    $moduleslug = $HelperObj->ModuleSlug;
                    $activatedplugin = 'crmformswpbuilder';
                    $plugin_url = ZCF_BASE_DIR_URI;
                    $adminmenulable->zcf_setPluginsUrl($plugin_url);
                    $onAction = 'onCreate';
                    $siteurl = site_url();
                    $crm_users = get_option("crm_users");

                    $users_detail = array();


                    foreach ($crm_users['users'] as $value) {

                        //echo $value['email'];
                        $users_detail[$value['email']] = array('user_name' => $value['email'], 'first_name' => $value['first_name'], 'last_name' => $value['last_name']);
                    }
                    //print_r($users_detail[$value['email']]);
                    $htmlcontent1 = "";
                    $htmlcontent1 .= "
			<div class='wp-common-crm-content'>
			<table class='listview w100per'><thead>
				<tr class='border_ddd'>
				</tr>
				<tr class='crmforms-crm-highlight crmforms-crm-alt border_ddd' >

					<th class='crmforms-crm-free-list-view-th' style='width: 80px;'></th>
					<th class='crmforms-crm-free-list-view-th' >" . __('Form name', 'zoho-crm-form-builder') . "</th>
					<th class='crmforms-crm-free-list-view-th' >" . __('Shortcode', 'zoho-crm-form-builder') . "</th>
					<th class='crmforms-crm-free-list-view-th'>" . __('Module', 'zoho-crm-form-builder') . "</th>
					<th class='crmforms-crm-free-list-view-th' >" . __('Layout Name', 'zoho-crm-form-builder') . "</th>
					<th class='crmforms-crm-free-list-view-th'>" . __('Assignee', 'zoho-crm-form-builder') . "</th>
					<th class='crmforms-crm-free-list-view-th aligncenter'>" . __('Form Type', 'zoho-crm-form-builder') . "</th>			

				</tr></thead><tbody>";

                    $shortcodemanager = $wpdb->get_results("select *from zcf_zohoshortcode_manager");
                    foreach ($shortcodemanager as $shortcode_fields) {
                        //print_r($shortcode_fields);
                        $htmlcontent1 .= "<tr>";
                        $shortcode_name = "[zohocrm-web-form name='" . $shortcode_fields->shortcode_name . "']";
                        if ($shortcode_fields->assigned_to == "Round Robin") {
                            $assigned_to = "Round Robin";
                        } else {
                            $assigned_to = $users_detail[$shortcode_fields->assigned_to]['first_name'] . " " . $users_detail[$shortcode_fields->assigned_to]['last_name'];
                        }
                        $oldshortcodename = "";
                        $oldshortcode_reveal_html = "";
                        $oldshortcode_html = "";
                        if ($shortcode_fields->old_shortcode_name != NULL) {
                            $oldshortcodename = $shortcode_fields->old_shortcode_name;
                            $oldshortcode_reveal_html = "<p><a style='cursor:pointer;' id='oldshortcodename_reveal{$shortcode_fields->shortcode_id}' onclick='jQuery(\"#oldshortcodename\"+{$shortcode_fields->shortcode_id}).show(); jQuery(\"#oldshortcodename_reveal\"+{$shortcode_fields->shortcode_id}).hide(); '> Click here to reveal old shortcode </a></p>";
                            $oldshortcode_html = "<p style='display:none;' id='oldshortcodename{$shortcode_fields->shortcode_id}'> $oldshortcodename </p>";
                        }
                        $htmlcontent1 .= "<td class='crmforms-crm-highlight' >";
                        $htmlcontent1 .= "<a href='#' class='edit_link' onclick='edit_forms(\"Editshortcode\" , \"$shortcode_fields->module\" , \"$shortcode_fields->shortcode_name\",\"$activatedplugin\",\"$shortcode_fields->Layout_Name\")'> <sapn class='edit-icon dB'></span> </a>";
                        $htmlcontent1 .= "<a href='#' class='delete_link' style='margin-left:2px;' onclick='deleteforms(\"zcfDeleteShortcode\" , \"$shortcode_fields->module\" , \"$shortcode_fields->shortcode_name\",\"$activatedplugin\",\"\",\"$shortcode_fields->form_name\")'>  <sapn class='delete-icon dB'></span> </a>";
                        $htmlcontent1 .= "</td>";
                        $htmlcontent1 .= "<td class='crmforms-crm-highlight form-name-link' >" . $shortcode_fields->form_name . "</td>";
                        $htmlcontent1 .= "<td class='crmforms-crm-highlight '><span class='copyshortcodeTxt' id='copyshortcodeTxt'>" . $shortcode_name . "$oldshortcode_reveal_html $oldshortcode_html</span><span class='copyshortcode' onclick='clicktocopyshortcodeList(this)' title='Click to copy shortcode' data-toggle='tooltip' data-placement='top'></span></td>";
                        $htmlcontent1 .= "<td class='crmforms-crm-highlight' >" . $shortcode_fields->module . "</td>";
                        $htmlcontent1 .= "<td class='crmforms-crm-highlight' >" . $shortcode_fields->Layout_Name . "</td>";
                        $htmlcontent1 .= "<td class='crmforms-crm-highlight' >" . $assigned_to . "</td>";
                        $htmlcontent1 .= "<td class='crmforms-crm-highlight' > - </td>";

                        $htmlcontent1 .= "</tr>";
                    }

                    //Codes for getting Thirdparty existing forms
                    $existing_content = '';




                    $save_contact_form_id = array();
                    $contact_option_name = $activatedplugin . "_zcf_contact";
                    $list_of_shortcodes = $wpdb->get_results($wpdb->prepare("select option_name from {$wpdb->prefix}options where option_name like %s", "$contact_option_name%"));
                    if (!empty($list_of_shortcodes)) {
                        foreach ($list_of_shortcodes as $list_key => $list_val) {
                            $shortcode_name = $list_val->option_name;
                            $form_id = explode($contact_option_name, $shortcode_name);
                            $save_contact_form_id[] = $form_id[1];
                        }
                    }

                    foreach ($save_contact_form_id as $contact_val) {
                        $get_config = get_option($contact_option_name . "" . $contact_val);

                        $exist_module = $get_config['third_module'];
                        $exist_assignee = $get_config['thirdparty_assignedto_name'];
                        $get_form_title = $wpdb->get_results($wpdb->prepare("select post_title from $wpdb->posts where post_type=%s and ID=%d", 'wpcf7_contact_form', $contact_val));
                        $contact_form_title = $get_form_title[0]->post_title;
                        $third_plugin = $get_config['third_plugin'];
                        if (isset($get_config['tp_roundrobin'])) {
                            $third_roundrobin = $get_config['tp_roundrobin'];
                        } else {
                            $third_roundrobin = "";
                        }
                        $form_ID = $get_config['form_title'];
                        $third_plugin = $get_config['third_plugin'];
                        $third_module = $get_config['third_module'];
                        $layoutname = $get_config['layoutname'];
                        $layoutId = $get_config['layoutId'];
                        $contactformShortcode = "[contact-form-7 " . "id='{$form_ID}' title='{$contact_form_title}']";
                        $existing_content .= "<tr>";
                        $existing_content .= "<td class='crmforms-crm-highlight' >";
                        $existing_content .= "<a href='#' class='edit_link' onclick='return editThirdpartyFrom(\"$form_ID\" , \"$third_plugin\" , \"$third_module\" , \"$layoutname\" , \"$layoutId\",\"$contact_form_title\")'> <sapn class='edit-icon dB'></span> </a>";
                        $existing_content .= "<a href='#' class='delete_link' onclick='return delete_mappping_config(\"$third_plugin\" , \"$contact_val\" ,\"$contact_form_title\");' style='margin-left:2px;'> <sapn class='delete-icon dB'></span> </a>";
                        $existing_content .= "</td>";
                        $existing_content .= "<td class='crmforms-crm-highlight form-name-link' > $contact_form_title</td><td class='crmforms-crm-highlight'><span class='copyshortcodeTxt' id='copyshortcodeTxt' title='Click to copy shortcode' data-toggle='tooltip' data-placement='top'>$contactformShortcode</span><span class='copyshortcode' onclick='clicktocopyshortcodeList(this)'></span></td>			
				<td class='crmforms-crm-highlight' > $exist_module</td><td class='crmforms-crm-highlight' > $layoutname</td><td class='crmforms-crm-highlight' > $exist_assignee</td>	
				<td class='crmforms-crm-highlight' > Contact Form7</td>";

                        $existing_content .= "</tr>";
                    }

                    $htmlcontent1 .= $existing_content;
                    $htmlcontent1 .= "</tbody></table></div>";
                    $modulearray = $wpdb->get_results("select modifydate from zcf_zohocrm_list_module");
                    $SettingsConfig = get_option("zcf_crmformswpbuilder_settings");
                    $authtokens = $SettingsConfig['authtoken'];
                    ?>
                    <div class="mb20 mt30">
                        <h4 class="mb10 dB">Zoho CRM Forms</h4><span class="dB">The form builder allows you to create forms in your wordpress and push the data into your Zoho CRM. Also, you can map the third party forms with Zoho CRM.</span> </div>
                    <div class="mt30 mb30 pR"> 
                        <input class="primaryflatbtn   btn_small" type="submit" value="<?php echo esc_attr__('Create New Form', " zoho-crm-form-builder "); ?>" onclick="createNewFormPopup()" id="createNewFormPopup" />

                        <input class="outlineprimary  btn_small" type="button" onclick="createNewTPFormPopup()" id="thirdparty_map" value="<?php echo esc_attr__('Use Contact Form 7', " zoho-crm-form-builder "); ?>" />
                        <?php if ($authtokens != '') { ?>
                            <span class="latest_module_syn" ><span class="mr20">Last sync on: <?php echo date("M d, Y", strtotime($modulearray[0]->modifydate)); ?> </span><a onclick="syncrmModules();"  class="synmodules pR pl20" title="Modules of Zoho CRM will be synchronized with WordPress" data-toggle='tooltip' data-placement='top'>Sync now</a></span>
                        <?php } ?>
                    </div>
                    <?php
                    $fields = $wpdb->get_results("select last_modified_date from zcf_zohocrmform_field_manager ");
                    ?>

                </div>
                <?php echo $htmlcontent1; ?>

                <head>
                    <meta charset="utf-8">
                </head>

                <body>
                    <input type="hidden" name="currentpageUrl" id="currentpageUrl" value="<?php echo $current_url; ?>" />

                    <div class="container" style="width:100%;">

                        <div class="newPopup ppTop dB" id="create-new-form-popup" role="dialog">

                            <div class="h1 mB0 pp-header m0"> Create New Form</div>
                            <!-- Modal content-->

                            <div class="pp-content pb0">
                                <?php
                                include_once(ZCF_BASE_DIR_URI . 'includes/crmapiintergration.php');
                                $authTokenConfig = get_option("zcf_crmformswpbuilder_settings");
                                $authToken = base64_decode(base64_decode(base64_decode($authTokenConfig['authtoken'])));
                                $crmformsZohoapi = new zcfaccountApi();
                                $crmformsZohoapi->zcfGetModules($authToken);
                                $layoutarray = $wpdb->get_results("select distinct(api_name),plural_label from zcf_zohocrm_list_module where  api_name !='' and api_name NOT IN('Visits','Vendors','Tasks','Social','Sales_Orders','Reports','Quotes','Purchase_Orders','Projects','Products','Price_Books','Deals','Notes','Invoices','Home','Feeds','Events','Emails','Documents','Dashboards','Campaigns','Calls','Attachments','Approvals','Activities');");
                                ?>

                                <div class="col-md-12  m10">
                                    <div class="form-name-title mb20">
                                        <label style="width:200px;"> Form Title</label>
                                        <input type="text" id="form-name" name="form-name" value="Unititled" />

                                    </div>
                                    <div class="module-name dIB" id="choose-module">
                                        <label style="width:200px;"> Select Module</label>
                                        <select name="select-module" id="select-module" style="width:285px" onchange="selectModule(this, '<?php echo $siteurl; ?>')">
                                            <option value=''>Select Module</option>
                                            <?php
                                            foreach ($layoutarray as $key => $value) {
                                                echo "<option value='" . $value->api_name . "'>" . $value->plural_label . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <span class="smaill-loading-image"></span>
                                    </div>

                                    <div class="layout-name dIB dN mt20" id="choose-leads-layout">
                                        <label style="width:200px;"> Select Layout</label>
                                        <select name="select-layout" id="select-layout" style="width:285px" onchange="selectLayout(this);">
                                        </select>
                                    </div>
                                    <input type="hidden" name="modulename" id="modulename" class="modulename" value="">
                                    <input type="hidden" name="layoutname" id="layoutname" class="layoutname" value="">
                                    <input type="hidden" name="layoutId" id="layoutId" class="layoutId" value="">
                                </div>
                            </div>

                            <div class="pp-footer">
                                <button type="button" id="close" class="newgraybtn " data-dismiss="modal" onclick="cancelNewFormPopup();">Cancel</button>
                                <input type="button" id="form-submit-module" class="primarybtn " value="Next" onclick="create_crmform('zcfCreateShortcode', 'Leads', '', '', '')" disabled>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div id="loading-image" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL); ?>/zoho-crm-forms/assets/images/ajax-loaders.gif) no-repeat center">
                        <?php echo esc_html__('', "zoho-crm-form-builder"); ?> </div>

                </body>

                <?php
            }
            ?>
        </div>
    </div>
    </div>
    <div class="freezelayer"></div>
<?php } ?>
<div class="newPopup ppTop" id="thirdparity-field-mapping-popup">
    <div class="h1 mB0 pp-header m0">Configure Contact Form 7</div>
    <div class="pp-content pb0">
        <div>

            <?php
            global $wpdb;
            $layoutarray = $wpdb->get_results("select distinct(api_name),plural_label from zcf_zohocrm_list_module where  api_name !='' and api_name NOT IN('Visits','Vendors','Tasks','Social','Sales_Orders','Projects','Approvals','Products','Solution','Invoice','Estimate','Reports','Quotes','Purchase_Orders','WPjects','WPducts','Price_Books','Deals','Notes','Invoices','Home','Feeds','Events','Emails','Documents','Dashboards','Campaigns','Calls','Attachments','ApWPvals','Activities');");
            $rulearray = $wpdb->get_results("select * from zcf_zohocrm_assignmentrule");
            ?>
            <div class='form-group col-md-12'>
                <div id="display_form_lists" class='col-md-offset-1'>
                    <?php
                    global $wpdb;
                    $crmname = 'crmformswpbuilder';

                    //Check Shortcode exist
                    $save_form_id = array();
                    $contact_option_name = $crmname . "_zcf_contact";
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
                    $html = "  <label id='innertext' class='leads-builder-label width200 mr10'>Select a form </label>
			                <select id='thirdparty_form_title' class='selectpicker form-control' data-live-search='false' name='thirdparty_form_title' onchange='thirdparty_form_title_change(this)'>";
                    $option_content = '';
                    $option_content = "<option value='None'> None </option>";
                    foreach ($cont_form_titles as $option_key => $option_value) {
                        $form_id = $option_value['id'];
                        $title = $option_value['title'];
                        if (!in_array($form_id, $save_form_id)) {
                            $option_content .= "<option value='{$form_id}'>$title</option>";
                        }
                    }

                    $html .= $option_content;
                    $html .= "</select></div>";
                    print_r($html);
                    ?>

                    <div class=' crmmodule-container dN pR cB mt20'>
                        <label id='innertext' class='leads-builder-label width200 mr10'> To which CRM module would you like to map this form</label>

                        <select id='map_thirdparty_module' class='form-control' name='map_thirdparty_module' onchange='selectThirdModule(this, "")'>
                            <option value=''>Select Module</option>
                            <?php
                            foreach ($layoutarray as $key => $value) {
                                echo "<option value='" . $value->api_name . "'>" . $value->plural_label . "</option>";
                            }
                            ?>

                        </select>
                        <span class='smaill-loading-image'></span>

                    </div>
                    <div id='layout-third-module' class="mt20">

                        <label id='innertext' class='leads-builder-label width200 mr10'> Select Layout </label>

                        <select id='choose-thirdleads-layout' name='choose-thirdleads-layout' class='form-control' onchange='selectThirdlayout(this)'>
                            <option value=''>Select a Layout</option>";
                        </select>


                    </div>
                </div>
            </div>
        </div>

        <div class="pp-footer">
            <button type="button" id="close" class="newgraybtn " data-dismiss="modal" onclick="cancelNewTPFormPopup();">Cancel</button>
            <input type="button" id="form-tbsubmit-module" class="primarybtn " value="Next" onclick='getThirdpartyTitle("", "contactform", "Leads")' disabled>
        </div>
    </div>