<?php

if (!defined('ABSPATH'))
    exit;
$selectedPlugin = 'crmformswpbuilder';
update_option('ZCFFormBuilderPluginActivated', $selectedPlugin);
require_once(ZCF_BASE_DIR_URI . "includes/form-zohocrmconfig.php");
?>
