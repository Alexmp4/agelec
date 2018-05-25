<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class zcfmaincorehelpers {

    public $capturedId = 0;
    public $ActivatedPlugin;
    public $Action;
    public $Module;
    public $ModuleSlug;
    public $instanceurl;
    public $accesstoken;

    public function __construct() {
        global $IncludedPluginsWP;
        $ContactFormPluginsObj = new zcfactivehelper();
        $this->ActivatedPlugin = 'crmformswpbuilder';
        if (isset($_REQUEST['action'])) {
            $this->Action = sanitize_text_field($_REQUEST['action']);
        } else {
            $this->Action = "";
        }
        if (isset($_REQUEST['module'])) {
            $this->Module = sanitize_text_field($_REQUEST['module']);
        } else {
            $this->Module = "";
        }
        $this->ModuleSlug = rtrim(strtolower($this->Module), "s");
    }

    public static function activate() {
        $maincrmforms_helper = new zcfmaincorehelpers();
        self::zcf_migration();
        $sync_array = array('user_sync_module' => 'Leads', 'crmforms_capture_duplicates' => 'skip');
        $zcf_user_assignto = array('usersync_assign_leads' => '--Select--');
        update_option('zcf_usersync_assignedto_settings', $zcf_user_assignto);
        update_option("zcf_user_capture_settings", $sync_array);
        update_option("ZcfLeadContactformPLugin", "none");
        update_option("zohocrmbasemodule", "Leads");
        update_option("custom_plugin", "none");
        update_option("Sync_value_on_off", "On");
        global $IncludedPluginsWP, $zohocrmbasename;
        $index = 0;
        $i = 0;
        update_option("ZcfFromBuilderFirstTimeWarning", "true");
        if ($index == 0) {
            update_option('ZCFFormBuilderPluginActivated', $firstplugin);
        }
        self::zcf_createPluginTables();
        self::zcf_checkVersion();
        self::zcf_migration();
    }

    public static function deactivate() {

        global $IncludedPluginsWP;
        delete_option("zcf_lead_post_field_settings");
        delete_option("zcf_lead_widget_field_settings");

        delete_option("zcf_lead_fields-tmp");
        delete_option("zcf_contact_fields-tmp");

        delete_option("zcf_crmformswpbuilder_settings");
        delete_option("zcf_crmfields_shortcodes");

        delete_option("zcf_crm_oldversion_shortcodes");
        delete_option("ZcfFromBuilderFirstTimeWarning");
    }

   
    public static function zcf_checkVersion() {
        $zcf_lead_builder_version = get_option("zcf-zohocrm-form-builder");
        update_option('zcf-zohocrm-form-builder', ZCF_PLUGIN_VERSION);
        if ($zcf_lead_builder_version == NULL || $zcf_lead_builder_version == "" || !$zcf_lead_builder_version) {
            self::zcf_createPluginTables();
        }
        
        if ($zcf_lead_builder_version == NULL || $zcf_lead_builder_version == "" || !$zcf_lead_builder_version || $zcf_lead_builder_version < 2.0) {
            self::zcf_altershortcodetable();  
        }
        self::zcf_altershortcodetable();  
    }

    public static function zcf_createPluginTables() {
        global $wpdb;
        $wpdb->query("
			CREATE TABLE IF NOT EXISTS `zcf_zohoshortcode_manager` (
				`shortcode_id` int(11) NOT NULL AUTO_INCREMENT,
				  `shortcode_name` varchar(10) NOT NULL,
				  `old_shortcode_name` varchar(255) DEFAULT NULL,
				  `form_type` varchar(10) NOT NULL,
				  `assigned_to` varchar(200) NOT NULL,
				  `error_message` text NOT NULL,
				  `success_message` text NOT NULL,
				  `submit_count` int(10) NOT NULL DEFAULT '0',
				  `success_count` int(10) NOT NULL DEFAULT '0',
				  `failure_count` int(10) NOT NULL DEFAULT '0',
				  `is_redirection` tinyint(1) NOT NULL,
				  `url_redirection` varchar(255) NOT NULL,
				  `duplicate_handling` varchar(10) NOT NULL DEFAULT 'none',
				  `google_captcha` tinyint(1) NOT NULL,
				  `module` varchar(25) NOT NULL,
				  `Round_Robin` varchar(50) NOT NULL,
				  `crm_type` varchar(25) NOT NULL,
				  `Layout_Name` varchar(255) NOT NULL,
				  `form_name` varchar(255) NOT NULL,
				  `layoutId` varchar(255) NOT NULL,
 				  `assignmentrule_ID` varchar(255) NOT NULL,
 				 `assignmentrule_enable` varchar(55) NOT NULL DEFAULT '0',
 				 `thirtparty_enable` varchar(55) DEFAULT '0',
				   PRIMARY KEY ( shortcode_id )
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8
		");

        $wpdb->query("
			CREATE TABLE IF NOT EXISTS `zcf_zohocrm_formfield_manager` (
				`rel_id` int(11) NOT NULL AUTO_INCREMENT,
				  `shortcode_id` int(11) NOT NULL,
				  `field_id` int(11) NOT NULL,
				  `zcf_field_mandatory` varchar(10) NOT NULL,
				  `state` varchar(10) NOT NULL,
				  `custom_field_type` varchar(20) NOT NULL,
				  `custom_field_values` longtext NOT NULL,
				  `custom_field_default` text NOT NULL,
				  `form_field_sequence` int(3) NOT NULL,
				  `display_label` varchar(50) NOT NULL,
				  `editupdate` int(55) NOT NULL,
				  `hiddenfield` varchar(55) DEFAULT '0',
  				  `defaultvalues` varchar(255) NOT NULL,
				   PRIMARY KEY ( rel_id )
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8
		");
        $wpdb->query("
			CREATE TABLE IF NOT EXISTS `zcf_zohocrm_assignmentrule` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				  `modulename` varchar(255) NOT NULL,
				  `assignmentrule_ID` varchar(255) NOT NULL,
				  `assignmentrrule_name` varchar(255) NOT NULL,
				   PRIMARY KEY ( id )
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8
		");

        $wpdb->query("
			CREATE TABLE IF NOT EXISTS `zcf_zohocrmform_field_manager` (
			`field_id` int(11) NOT NULL AUTO_INCREMENT,
			  `field_name` varchar(50) NOT NULL,
			  `field_label` varchar(50) NOT NULL,
			  `field_type` varchar(20) NOT NULL,
			  `field_values` longtext NOT NULL,
			  `field_default` text NOT NULL,
			  `module_type` varchar(20) NOT NULL,
			  `field_mandatory` varchar(10) NOT NULL,
			  `crm_type` varchar(25) NOT NULL,
			  `field_sequence` int(10) NOT NULL,
			  `base_model` varchar(20) NOT NULL,
			  `last_modified_date` date NOT NULL,
			  `Layout_Name` varchar(255) NOT NULL,
			  `layoutId` varchar(55) NOT NULL,
			  `readonly` varchar(55) NOT NULL,
			  `editupdate` int(11) NOT NULL,
			  `viewcreate_type` int(1) NOT NULL,
			  PRIMARY KEY ( field_id )
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
        $wpdb->query("
			CREATE TABLE IF NOT EXISTS `zcf_zohocrm_list_module` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			  `api_supported` varchar(255) NOT NULL,
			  `plural_label` varchar(255) NOT NULL,
			  `api_name` varchar(255) NOT NULL,
			  `module_name` varchar(255) NOT NULL,
			  `module_id` varchar(255) NOT NULL,
			  `business_card_field_limit` varchar(255) NOT NULL,
			  `modifydate` date NOT NULL,
			   PRIMARY KEY ( id )
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8
		");
        

        //create table for form field relations
        $wpdb->query("
			CREATE TABLE IF NOT EXISTS `zcf_contactformrelation` (
			`id` int(6) unsigned NOT NULL AUTO_INCREMENT,
			  `crmformsshortcodename` varchar(30) NOT NULL,
			  `crmformsfieldid` int(20) DEFAULT NULL,
			  `crmformsfieldslable` varchar(30) NOT NULL,
			  `thirdpartypluginname` varchar(30) NOT NULL,
			  `thirdpartyformid` int(50) DEFAULT NULL,
			  `thirdpartyfieldids` varchar(50) DEFAULT NULL,
			   PRIMARY KEY ( id )
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8
		");
    }
    public static function zcf_migration(){
        global $wpdb;
        $wpdb->query("ALTER table zcf_contactformrelation (ADD crmformsshortcodename varchar(50),crmformsfieldid int(20) DEFAULT NULL,crmformsfieldslable varchar(30) NOT NULL,thirdpartypluginname varchar(30) NOT NULL,thirdpartyformid int(50) DEFAULT NULL,thirdpartyfieldids varchar(50) DEFAULT NULL");

    }
    public static function zcf_altershortcodetable() {
         global $wpdb;
        $wpdb->query("ALTER table zcf_zohoshortcode_manager ADD Round_Robin varchar(50)");
    }
    public function zcf_CreateFieldShortcode($crmtype, $module) {
        global $crmdetails;
        $module = $module;
        $moduleslug = rtrim(strtolower($module), "s");
        $tmp_option = "crmforms_{$crmtype}_{$moduleslug}_fields-tmp";
        if (!function_exists("zcfgenerateRandomStringActivate")) {

            function zcfgenerateRandomStringActivate($length = 10) {
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
            $random_string = zcfgenerateRandomStringActivate(5);
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

    

    public function renderMenu() {
        include(ZCF_BASE_DIR_URI . 'includes/menu.php');
    }

    public function renderContent() {
        if ($this->Action == "Settings" || $this->Action == "") {
            if ($this->Action == "") {
                $this->Action = "Settings";
            }
            $action = $this->ActivatedPlugin . $this->Action;
            $module = $this->Module;
        } else {
            $action = $this->Action;
            $module = $this->Module;
        }
        include(plugin_dir_path(__FILE__) . '../modules/' . $action . '/actions/actions.php');
        include(plugin_dir_path(__FILE__) . '../modules/' . $action . '/includes/view.php');
    }

}

class ZcfCallCaptureObjCrm extends zcfmaincorehelpers {

    private static $_instance = null;

    public static function ZcfgetInstance() {
        if (!is_object(self::$_instance))
            self::$_instance = new zcfmaincorehelpers();
        return self::$_instance;
    }

}

