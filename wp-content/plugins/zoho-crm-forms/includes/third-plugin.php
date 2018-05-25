<?php

if (!defined('ABSPATH'))
    exit; 
$selectedPlugin = sanitize_text_field($_REQUEST['postdata']);
$crmnames = get_option("active_plugins");
if (in_array("contact-form-7/wp-contact-form-7.php", $crmnames)) {
    $activated = "yes";
    update_option('ZcfLeadContactformPLugin', $selectedPlugin);
} else {
    $activated = "no";
}
print_r($activated);
die;
?>
