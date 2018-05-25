<?php

if (!defined('ABSPATH'))
    exit;

include_once ( plugin_dir_path(__FILE__) . 'crmformshelper.php' );

class zcfadminhelperconfig extends zcfhelper {

    public function __construct() {
        
    }
    public static function zcfadminenus() {
        global $submenu;
        add_menu_page(ZCF_PLUGIN_NAME_SETTINGS, ZCF_PLUGIN_NAME, 'manage_options', ZCF_BASE_SLUG, array(__CLASS__, 'zcfmenuactivecheck'), plugins_url("assets/images/zohocrmicon.png", dirname(__FILE__)));
        add_submenu_page(null, ZCF_PLUGIN_NAME, esc_html__('CRM Forms', 'zoho-crm-form-builder'), 'manage_options', 'crmforms-builder', array(__CLASS__, 'zcfmenuactivecheck'));
        add_submenu_page(null, ZCF_PLUGIN_NAME, esc_html__('Forms Settings', 'zoho-crm-form-builder'), 'manage_options', 'formsettings-builder', array(__CLASS__, 'zcfmenuactivecheck'));
        add_submenu_page(null, ZCF_PLUGIN_NAME, esc_html__('', 'zoho-crm-form-builder'), 'manage_options', 'create-leadform-builder', array(__CLASS__, 'zcfmenuactivecheck'));
        add_submenu_page(null, ZCF_PLUGIN_NAME, esc_html__('', 'zoho-crm-form-builder'), 'manage_options', 'create-thirdpartyform-builder', array(__CLASS__, 'zcfmenuactivecheck'));
        unset($submenu[ZCF_BASE_SLUG][0]);
    }

    public static function zcfmenuactivecheck() {
        global $adminmenulable;
        $crmname = 'crmformswpbuilder';
        $adminmenulable->zcf_setActivatedPlugin($crmname);
        $page = sanitize_text_field($_REQUEST['page']);
        $url = site_url();
        $adminmenulable->zcfshowTopNaviMenus();
        switch (sanitize_title($_REQUEST['page'])) {
            case 'crmforms-builder':
                $manuactive = "active";
                $adminmenulable->zcfshowForms($manuactive);

                break;
            case 'formsettings-builder':
                $manuactive = "active";
                $adminmenulable->zcfshowFormPropsettings($manuactive);
                break;
            case 'create-leadform-builder':
                $adminmenulable->zcfleadView();
                break;
            case 'create-thirdpartyform-builder':
                $adminmenulable->zcfcontactFormView();
                break;
            default:
                break;
        }
        return false;
    }

    public function zcfleadView() {
        global $adminmenulable;
        include ('crmwebformfields.php');
    }

    public function zcfcontactFormView() {
        include ('crmcontactformfields.php');
    }

    public function zcfshowForms() {
        include ('crmwebforms.php');
    }

    public function zcfshowFormPropsettings() {
        include ('crmsettingstab.php');
    }

   

    public function zcfshowTopNaviMenus() {
        global $wpdb;
        $crmname = 'crmformswpbuilder';
        $crmSettings = get_option("zcf_{$crmname}_settings");
        $disabledMenu = '';
        if (!$crmSettings) {
            $disabledMenu = "pointer-events:none;opacity:0.8;";
        }

        switch (sanitize_title($_REQUEST['page'])) {

            case 'crmforms-builder':
                $manuactive = "active";
                $manuformsettingsactive = "";
                $manucrmconfigactive = "";
                break;
            case 'create-leadform-builder':
                $manuactive = "active";
                $manuformsettingsactive = "";
                $manucrmconfigactive = "";
                break;
            case 'create-thirdpartyform-builder':
                $manuactive = "active";
                $manuformsettingsactive = "";
                $manucrmconfigactive = "";
                break;
            case 'formsettings-builder':
                $manuactive = "";
                $manuformsettingsactive = "active";
                $manucrmconfigactive = "";
                break;
            default:
                break;
        }
        $admin_url = 'admin.php';
        $modulearray = $wpdb->get_results("select modifydate from zcf_zohocrm_list_module");
        if ($_REQUEST['page'] == 'crmforms-builder' || $_REQUEST['page'] == 'formsettings-builder') {
            echo '<div class="zoho-crm-form-builder">
                <table class="commentabmenu"><tr>
                       <td class="' . $manuactive . '"> <a href="' . esc_url(admin_url() . 'admin.php?page=crmforms-builder') . '"  id = "menu1" style="' . $disabledMenu . '">' . esc_html__('Forms', 'zoho-crm-form-builder') . '</a> <t/d> 
                         <td class="' . $manuformsettingsactive . '"> <a href="' . esc_url(admin_url() . 'admin.php?page=formsettings-builder') . '"  id = "menu2" style="' . $disabledMenu . '">' . esc_html__('Settings', 'zoho-crm-form-builder') . '</a> <t/d> 
            
                </tr></table></div>';
        } else if ($_REQUEST['page'] == 'create-leadform-builder') {
            echo '<div class="mainheader-webform"><a class="backbtn" href="admin.php?page=crmforms-builder"></a>Create a New Form </div>';
        } else if ($_REQUEST['page'] == 'create-thirdpartyform-builder') {
            echo '<div class="mainheader-webform">Create a New Form </div>';
        } else {
            echo '<div class="zoho-crm-form-builder">
                <table class="commentabmenu"><tr>
                       <td class="' . $manuactive . '"> <a href="' . esc_url(admin_url() . 'admin.php?page=crmforms-builder') . '"  id = "menu1" style="' . $disabledMenu . '">' . esc_html__('Forms', 'zoho-crm-form-builder') . '</a> <t/d> 
                         <td class="' . $manuformsettingsactive . '"> <a href="' . esc_url(admin_url() . 'admin.php?page=formsettings-builder') . '"  id = "menu2" style="' . $disabledMenu . '">' . esc_html__('Settings', 'zoho-crm-form-builder') . '</a> <t/d> 
            
                </tr></table></div>';
        }
    }

}
add_action('admin_menu', array('zcfadminhelperconfig', 'zcfadminenus'));
global $adminmenulable;
$adminmenulable = new zcfadminhelperconfig();

