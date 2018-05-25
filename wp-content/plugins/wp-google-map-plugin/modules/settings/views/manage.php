<?php
/**
 * This class used to manage settings page in backend.
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package Maps
 */

$form  = new WPGMP_Template();
$form->set_header( __( 'General Setting(s)', WPGMP_TEXT_DOMAIN ), $response );
$form->add_element('text','wpgmp_api_key',array(
	'lable' => __( 'Google Maps API Key',WPGMP_TEXT_DOMAIN ),
	'value' => get_option( 'wpgmp_api_key' ),
	'desc' => __( 'Get here <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key"> Api Key </a> and insert here.', WPGMP_TEXT_DOMAIN ),
	));

$language = array(
'en' => __( 'ENGLISH', WPGMP_TEXT_DOMAIN ),
'ar' => __( 'ARABIC', WPGMP_TEXT_DOMAIN ),
'eu' => __( 'BASQUE', WPGMP_TEXT_DOMAIN ),
'bg' => __( 'BULGARIAN', WPGMP_TEXT_DOMAIN ),
'bn' => __( 'BENGALI', WPGMP_TEXT_DOMAIN ),
'ca' => __( 'CATALAN', WPGMP_TEXT_DOMAIN ),
'cs' => __( 'CZECH', WPGMP_TEXT_DOMAIN ),
'da' => __( 'DANISH', WPGMP_TEXT_DOMAIN ),
'de' => __( 'GERMAN', WPGMP_TEXT_DOMAIN ),
'el' => __( 'GREEK', WPGMP_TEXT_DOMAIN ),
'en-AU' => __( 'ENGLISH (AUSTRALIAN)', WPGMP_TEXT_DOMAIN ),
'en-GB' => __( 'ENGLISH (GREAT BRITAIN)', WPGMP_TEXT_DOMAIN ),
'es' => __( 'SPANISH', WPGMP_TEXT_DOMAIN ),
'fa' => __( 'FARSI', WPGMP_TEXT_DOMAIN ),
'fi' => __( 'FINNISH', WPGMP_TEXT_DOMAIN ),
'fil' => __( 'FILIPINO', WPGMP_TEXT_DOMAIN ),
'fr' => __( 'FRENCH', WPGMP_TEXT_DOMAIN ),
'gl' => __( 'GALICIAN', WPGMP_TEXT_DOMAIN ),
'gu' => __( 'GUJARATI', WPGMP_TEXT_DOMAIN ),
'hi' => __( 'HINDI', WPGMP_TEXT_DOMAIN ),
'hr' => __( 'CROATIAN', WPGMP_TEXT_DOMAIN ),
'hu' => __( 'HUNGARIAN', WPGMP_TEXT_DOMAIN ),
'id' => __( 'INDONESIAN', WPGMP_TEXT_DOMAIN ),
'it' => __( 'ITALIAN', WPGMP_TEXT_DOMAIN ),
'iw' => __( 'HEBREW', WPGMP_TEXT_DOMAIN ),
'ja' => __( 'JAPANESE', WPGMP_TEXT_DOMAIN ),
'kn' => __( 'KANNADA', WPGMP_TEXT_DOMAIN ),
'ko' => __( 'KOREAN', WPGMP_TEXT_DOMAIN ),
'lt' => __( 'LITHUANIAN', WPGMP_TEXT_DOMAIN ),
'lv' => __( 'LATVIAN', WPGMP_TEXT_DOMAIN ),
'ml' => __( 'MALAYALAM', WPGMP_TEXT_DOMAIN ),
'it' => __( 'ITALIAN', WPGMP_TEXT_DOMAIN ),
'mr' => __( 'MARATHI', WPGMP_TEXT_DOMAIN ),
'nl' => __( 'DUTCH', WPGMP_TEXT_DOMAIN ),
'no' => __( 'NORWEGIAN', WPGMP_TEXT_DOMAIN ),
'pl' => __( 'POLISH', WPGMP_TEXT_DOMAIN ),
'pt' => __( 'PORTUGUESE', WPGMP_TEXT_DOMAIN ),
'pt-BR' => __( 'PORTUGUESE (BRAZIL)', WPGMP_TEXT_DOMAIN ),
'pt-PT' => __( 'PORTUGUESE (PORTUGAL)', WPGMP_TEXT_DOMAIN ),
'ro' => __( 'ROMANIAN', WPGMP_TEXT_DOMAIN ),
'ru' => __( 'RUSSIAN', WPGMP_TEXT_DOMAIN ),
'sk' => __( 'SLOVAK', WPGMP_TEXT_DOMAIN ),
'sl' => __( 'SLOVENIAN', WPGMP_TEXT_DOMAIN ),
'sr' => __( 'SERBIAN', WPGMP_TEXT_DOMAIN ),
'sv' => __( 'SWEDISH', WPGMP_TEXT_DOMAIN ),
'tl' => __( 'TAGALOG', WPGMP_TEXT_DOMAIN ),
'ta' => __( 'TAMIL', WPGMP_TEXT_DOMAIN ),
'te' => __( 'TELUGU', WPGMP_TEXT_DOMAIN ),
'th' => __( 'THAI', WPGMP_TEXT_DOMAIN ),
'tr' => __( 'TURKISH', WPGMP_TEXT_DOMAIN ),
'uk' => __( 'UKRAINIAN', WPGMP_TEXT_DOMAIN ),
'vi' => __( 'VIETNAMESE', WPGMP_TEXT_DOMAIN ),
'zh-CN' => __( 'CHINESE (SIMPLIFIED)', WPGMP_TEXT_DOMAIN ),
'zh-TW' => __( 'CHINESE (TRADITIONAL)', WPGMP_TEXT_DOMAIN ),
);

$form->add_element( 'select', 'wpgmp_language', array(
	'lable' => __( 'Map Language', WPGMP_TEXT_DOMAIN ),
	'current' => get_option( 'wpgmp_language' ),
	'desc' => __( 'Choose your language for map. Default is English.', WPGMP_TEXT_DOMAIN ),
	'options' => $language,
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));

$form->add_element( 'radio', 'wpgmp_scripts_place', array(
	'lable' => __( 'Include Scripts in ', WPGMP_TEXT_DOMAIN ),
	'radio-val-label' => array( 'header' => __( 'Header',WPGMP_TEXT_DOMAIN ),'footer' => __( 'Footer (Recommanded)',WPGMP_TEXT_DOMAIN ) ),
	'current' => get_option( 'wpgmp_scripts_place' ),
	'class' => 'chkbox_class',
	'default_value' => 'footer',
));

$form->add_element('submit','wpgmp_save_settings',array(
	'value' => __( 'Save Setting',WPGMP_TEXT_DOMAIN ),
	));
$form->add_element('hidden','operation',array(
	'value' => 'save',
	));
$form->add_element('hidden','page_options',array(
	'value' => 'wpgmp_api_key,wpgmp_scripts_place',
	));
$form->render();
