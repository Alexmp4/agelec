<?php

if (!defined('ABSPATH'))
    exit;

global $zohocrmbasename, $zohocrmdetails, $ThirdPartyPlugins;
$ThirdPartyPlugins = array('none' => "None", 'contactform' => "Contact Form",);
$zohocrmbasemodule = array('Leads' => 'Leads');
$zohocrmdetails = array('crmformswpbuilder' => array("Label" => "WP Zoho WP", "crmname" => "ZohoCRM", "modulename" => array("Leads" => "Leads")),);
$zohocrmbasename = "crmformswpbuilder";
