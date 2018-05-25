<?php 

if ( ! defined( 'ABSPATH' ) )
        exit; 

class zcffieldlistDatamanage {
	function zcfFieldManager( $crmtype = "" , $module = "" )
	{
		global $wpdb;
		$sql = "select *from zcf_zohocrmform_field_manager";
		$fields = $wpdb->get_results($wpdb->prepare( " $sql where crm_type =%s and module_type =%s" , $crmtype,$module ) );
		if( count( $fields ) > 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function zcfDeleteFields($crmtype , $module , $check_deleted_fields,$Layout_Name)
	{
		global $wpdb;
		$get_shortcodes = array();
                $get_shortcodes = $wpdb->get_results($wpdb->prepare("select * from zcf_zohoshortcode_manager where module =%s and crm_type =%s and Layout_Name =%s" , $module , $crmtype,$Layout_Name) );

		foreach($check_deleted_fields as $del_key => $del_value)
		{
			$get_field_id = $wpdb->get_results($wpdb->prepare("select field_id from zcf_zohocrmform_field_manager where field_name=%s and crm_type=%s and module_type=%s and Layout_Name =%s" , $del_value , $crmtype , $module,$Layout_Name));
			$crm_field_id = $get_field_id[0]->field_id;
			$wpdb->delete('zcf_zohocrmform_field_manager' , array('field_name' => $del_value , 'crm_type'=> $crmtype , 'module_type' => $module ,'Layout_Name' =>$Layout_Name) , array('%s' , '%s' , '%s', '%s'));	

			foreach( $get_shortcodes as $key => $shortcodedata )
			{
				$fields = array();
				$shortcode_id = $shortcodedata->shortcode_id;
				$wpdb->delete('zcf_zohocrm_formfield_manager' , array('field_id' => $crm_field_id , 'shortcode_id' => $shortcode_id ) , array('%s' , '%d'));
			}
		}
	}

	function zcffieldData($data , $module )
	{
		global $wpdb;
                print_r($data);
		$field_name = $data['name'];
		$field_label = $data['label'];
		$field_type = $data['type'];
		$module_type = $data['module'];
		$field_mandatory = $data['mandatory'];
		$crm_type = $data['crmtype'];
		$base_model = $data['base_model'];
		$field_sequence = $data['sequence'];
		$field_values = $data['field_values'];
		$layoutname = $data['layout_name'];
		$layoutId = $data['layoutId'];
		$readonly = $data['readonly'];
		$viewcreate_type = $data['viewcreate_type'];
		$fields = $wpdb->get_results( $wpdb->prepare( "select *from zcf_zohocrmform_field_manager where field_name='".$field_name."' and module_type='".$module."' and crm_type='".$crm_type."'  and Layout_Name='".$layoutname."'") );
		if(count($fields) == 0  )
		{
			$fields = $wpdb->insert( 'zcf_zohocrmform_field_manager' , array( 'field_name' => "$field_name", 'field_label' => "$field_label", 'field_type' => "$field_type", 'field_values' => "$field_values", 'module_type' => "$module_type", 'field_mandatory' => $field_mandatory, 'crm_type' => "$crm_type", 'field_sequence' => $field_sequence, 'base_model' => "$base_model",'last_modified_date' => date("Y-m-d H:i:s"),'Layout_Name' =>$layoutname,'layoutId'=>$layoutId,'readonly'=>$readonly,'editupdate'=>0,'viewcreate_type'=>$viewcreate_type) );
		}
		else {
			$fields = $wpdb->update( 'zcf_zohocrmform_field_manager' , array( 'field_label' => "$field_label", 'field_type' => "$field_type", 'field_values' => "$field_values", 'field_mandatory' => "$field_mandatory", 'field_sequence' => "$field_sequence", 'base_model' => "$base_model",'last_modified_date' => date("Y-m-d H:i:s"),'Layout_Name' =>$layoutname,'layoutId'=>$layoutId,'readonly'=>$readonly,'editupdate'=>0,'viewcreate_type'=>$viewcreate_type) , array( 'field_name' => "$field_name" , 'module_type' => "$module_type" , 'crm_type' => "$crm_type",'Layout_Name' =>"$layoutname",'layoutId'=>"$layoutId" ) );
		}
	}
	function zcffielddataedit($data , $module )
	{
		global $wpdb;
		$field_name = $data['name'];
		$field_label = $data['label'];
		$field_type = $data['type'];
		$module_type = $data['module'];
		$field_mandatory = $data['mandatory'];
		$crm_type = $data['crmtype'];
		$base_model = $data['base_model'];
		$field_sequence = $data['sequence'];
		$field_values = $data['field_values'];
		$layoutname = $data['layout_name'];
		$layoutId = $data['layoutId'];
		$readonly = $data['readonly'];
		$viewcreate_type = $data['viewcreate_type'];
		$fields = $wpdb->get_results( $wpdb->prepare( "select *from zcf_zohocrmform_field_manager where field_name='".$field_name."' and module_type='".$module."'  and Layout_Name='".$layoutname."'") );

		if( count($fields) == 0  )
		{
			$fields = $wpdb->insert( 'zcf_zohocrmform_field_manager' , array( 'field_name' => "$field_name", 'field_label' => "$field_label", 'field_type' => "$field_type", 'field_values' => "$field_values", 'module_type' => "$module_type", 'field_mandatory' => $field_mandatory, 'crm_type' => "$crm_type", 'field_sequence' => $field_sequence, 'base_model' => "$base_model",'last_modified_date' => date("Y-m-d H:i:s"),'Layout_Name' =>$layoutname,'layoutId'=>$layoutId,'readonly'=>$readonly,'editupdate'=>1,'viewcreate_type'=>$viewcreate_type) );
		}
		else {
			$fields = $wpdb->update( 'zcf_zohocrmform_field_manager' , array( 'field_label' => "$field_label", 'field_type' => "$field_type", 'field_values' => "$field_values", 'field_mandatory' => "$field_mandatory", 'field_sequence' => "$field_sequence", 'base_model' => "$base_model",'last_modified_date' => date("Y-m-d H:i:s"),'Layout_Name' =>$layoutname,'layoutId'=>$layoutId,'readonly'=>$readonly,'editupdate'=>0,'viewcreate_type'=>$viewcreate_type) , array( 'field_name' => "$field_name" , 'module_type' => "$module_type" , 'crm_type' => "$crm_type",'Layout_Name' =>$layoutname ) );
		}


	}

	function zcfupdateFormSaveStatuses( $submit_parameters , $shortcodename )
	{
		global $wpdb;
		$submit_parameters['failure_count'] = $submit_parameters['total'] - $submit_parameters['success'];

		$update_form_submits = $wpdb->get_results("update zcf_zohoshortcode_manager set submit_count = '{$submit_parameters['total']}' , success_count = '{$submit_parameters['success']}' , failure_count = '{$submit_parameters['failure_count']}' where shortcode_name = '$shortcodename'");
	}

	function zcfupdateScodeFields( $data , $module  )
	{
		global $wpdb;
		$field_name = $data['name'];
		$field_label = $data['label'];
		$field_type = $data['type'];
		$module_type = $data['module'];
		$field_mandatory = $data['mandatory'];

		$publish = 0;
		if( $field_mandatory == 1 )
		{
			$publish = 1;
		}

		$crm_type = $data['crmtype'];
		
		$field_sequence = $data['sequence'];
		$field_values = $data['field_values'];
		$get_shortcodes = array();
		$get_shortcodes = $wpdb->get_results($wpdb->prepare("select * from zcf_zohoshortcode_manager where module =%s and crm_type =%s" , $module , $crm_type));
		$get_field_manager = $wpdb->get_results( $wpdb->prepare("select * from zcf_zohocrmform_field_manager where module_type =%s and field_name =%s and crm_type =%s" , $module , $field_name , $crm_type) );
		foreach( $get_shortcodes as $key => $shortcodedata )
		{
			$fields = array();
			$shortcodename = $shortcodedata->shortcode_name;	
			$shortcode_id = $shortcodedata->shortcode_id;
			
			$fields = $wpdb->get_results("select ffm.* , sm.*  from zcf_zohocrm_formfield_manager as ffm inner join zcf_zohocrmform_field_manager as fm on fm.field_id = ffm.field_id inner join zcf_zohoshortcode_manager as sm on sm.shortcode_id = ffm.shortcode_id where fm.field_name = '$field_name' and fm.module_type = '$module' and shortcode_name = '$shortcodename' and sm.crm_type = '$crm_type' ");
			$rel_id = isset($fields[0]) ? $fields[0]->rel_id : "";
			$field_id = isset($get_field_manager[0]) ? $get_field_manager[0]->field_id : "";

			if( $crm_type == $shortcodedata->crm_type && $module_type == $module )
			{
				if(count($fields) == 0)
				{

					$query = $wpdb->get_results( "insert into zcf_zohocrm_formfield_manager( field_id , shortcode_id , display_label , custom_field_type , custom_field_values , zcf_field_mandatory , form_field_sequence , state ) VALUES ('$field_id', '$shortcode_id' , '$field_label', '$field_type', '$field_values' , $field_mandatory , $field_sequence , $publish )" );
				}
				else 
				{
					$state = "";
					if( $field_mandatory == 1 )
					{
						$field_mandatory = 1;
						$state = ", state = '1'";
					}

					$query = $wpdb->get_results("update zcf_zohocrm_formfield_manager set zcf_field_mandatory = '$field_mandatory' {$state} , custom_field_values = '$field_values' where rel_id = '{$rel_id}'");
				
					if( $field_type == 'picklist' || $field_type == 'multipicklist')
					{
						$wpdb->update( 'zcf_zohocrm_formfield_manager' , array( 'custom_field_values' => $field_values ) , array( 'rel_id' => $rel_id ) );	
					}
				}
			}
		}
	}
	function zcfupdateDataeditScodeFields( $data , $module  )
	{
		global $wpdb;
		$field_name = $data['name'];
		$field_label = $data['label'];
		$field_type = $data['type'];
		$module_type = $data['module'];
		$field_mandatory = $data['mandatory'];

		$publish = 0;
		if( $field_mandatory == 1 )
		{
			$publish = 1;
		}

		$crm_type = $data['crmtype'];
		
		$field_sequence = $data['sequence'];
		$field_values = $data['field_values'];
		$layout_name = $data['layout_name'];
		$get_shortcodes = array();
		$get_shortcodes = $wpdb->get_results("select * from zcf_zohoshortcode_manager where module ='".$module."' ");
		$get_field_manager = $wpdb->get_results("select * from zcf_zohocrmform_field_manager where module_type ='".$module."' and field_name ='".$field_name."' and  Layout_Name ='".$layout_name."'");
		foreach( $get_shortcodes as $key => $shortcodedata )
		{
			$fields = array();
			$shortcodename = $shortcodedata->shortcode_name;	
			$shortcode_id = $shortcodedata->shortcode_id;
			$fields = $wpdb->get_results("select ffm.* , sm.*  from zcf_zohocrm_formfield_manager as ffm inner join zcf_zohocrmform_field_manager as fm on fm.field_id = ffm.field_id inner join zcf_zohoshortcode_manager as sm on sm.shortcode_id = ffm.shortcode_id where fm.field_name = '$field_name' and fm.module_type = '$module' and sm.shortcode_name = '$shortcodename' ");
			$rel_id = isset($fields[0]) ? $fields[0]->rel_id : "";
			$field_id = isset($get_field_manager[0]) ? $get_field_manager[0]->field_id : "";

			if( $crm_type == $shortcodedata->crm_type && $module_type == $module  && $shortcode_id !='' & $shortcode_id !=0)
			{
				if(count($fields) == 0)
				{
					
					$query = $wpdb->get_results( "insert into zcf_zohocrm_formfield_manager( field_id , shortcode_id , display_label , custom_field_type , custom_field_values , zcf_field_mandatory , form_field_sequence , state ,editupdate) VALUES ('$field_id', '$shortcode_id' , '$field_label', '$field_type', '$field_values' , $field_mandatory , $field_sequence , $publish,1 )" );
				}
				else 
				{
					
					$state = "";
					if( $field_mandatory == 1 )
					{
						$field_mandatory = 1;
						$state = ", state = '1'";
					}

					$query = $wpdb->get_results("update zcf_zohocrm_formfield_manager set zcf_field_mandatory = '$field_mandatory' {$state} , custom_field_values = '$field_values' ,editupdate=0 where rel_id = '{$rel_id}'");
				
					if( $field_type == 'picklist' || $field_type == 'multipicklist')
					{
						$wpdb->update( 'zcf_zohocrm_formfield_manager' , array( 'custom_field_values' => $field_values ) , array( 'rel_id' => $rel_id ) );	
					}
				}
			}
		}
	}
	function zcfformScodelists($shortcodedata , $mode = "create",$layoutname,$formname,$layoutId)
	{
		global $wpdb;
		$shortcode_name = $shortcodedata['name'];
		$form_type = $shortcodedata['type'];
		$assigned_to = $shortcodedata['assignto'];
		$error_message = $shortcodedata['errormesg'];
		$success_message = $shortcodedata['successmesg'];
		$is_redirection = $shortcodedata['isredirection'];
		$url_redirection = $shortcodedata['urlredirection'];
		$google_captcha = $shortcodedata['captcha'];
		$module = $shortcodedata['module'];
		$crm_type = $shortcodedata['crm_type'];
		$duplicate_handling = $shortcodedata['duplicate_handling'];
		$customthirdpartyplugin = $shortcodedata['customthirdpartyplugin'];
		
		$assignmentrule_ID ='';
		$assignmentrule_enable='';
		if(isset($shortcodedata['assignmentrule_ID'])){
			$assignmentrule_ID = $shortcodedata['assignmentrule_ID'];
			$assignmentrule_enable = $shortcodedata['assignmentrule_enable'];

		}
		if(isset($shortcodedata['assignmentrule_enable'])){
			$assignmentrule_enable = $shortcodedata['assignmentrule_enable'];

		}
		$formname = $shortcodedata['formname'];
		if($crm_type == 'crmformswpbuilder'){
			$crm_type = 'crmformswpbuilder';
			$temp = 'crmformswpbuilder';
		}
		require_once(ZCF_BASE_DIR_URI . "includes/ZFCore-functions.php");
		$FunctionsObj = new zcfcoreGetFields();
		$get_userslist = $FunctionsObj->zcfgetUsersList();
        $first_userid = $get_userslist['users'][0]['id'];
		if($crm_type == 'crmformswpbuilder' && !empty($temp)){
			$crm_type = $temp;
		}
		if( $mode == "create" )
		{
			$shortcodemanager = $wpdb->insert( 'zcf_zohoshortcode_manager' , array( 'shortcode_name' => "$shortcode_name" , 'form_type' => "$form_type" , 'assigned_to' => "$assigned_to" , 'error_message' => "$error_message" , 'success_message' => "$success_message" , 'is_redirection' => "$is_redirection" , 'url_redirection' => "$url_redirection" , 'google_captcha' => "$google_captcha" , 'module' => "$module" , 'crm_type' => "$crm_type" , 'Round_Robin' => "$first_userid",'Layout_Name' =>$layoutname,'form_name'=>$formname,'layoutId'=>$layoutId,'assignmentrule_ID'=>$assignmentrule_ID,'assignmentrule_enable'=>$assignmentrule_enable,'thirtparty_enable'=>$customthirdpartyplugin) );
		}
		else
		{
			$shortcodemanager = $wpdb->update( 'zcf_zohoshortcode_manager' , array( 'form_type' => "$form_type" , 'assigned_to' => "$assigned_to", 'error_message' => "$error_message" , 'success_message' => "$success_message" , 'is_redirection' => $is_redirection , 'url_redirection' => $url_redirection, 'google_captcha' => $google_captcha , 'duplicate_handling' => "$duplicate_handling",'assignmentrule_ID'=>$assignmentrule_ID,'assignmentrule_enable'=>$assignmentrule_enable,'thirtparty_enable'=>$customthirdpartyplugin) , array( 'shortcode_name' => "$shortcode_name" ) );

		}
		$lastid = $wpdb->insert_id;
		return $lastid;	
	}

	function zcfinsertFormFields( $shortcode_id, $field_id, $zcf_field_mandatory, $state, $custom_field_type, $custom_field_values , $form_field_sequence, $display_label)
	{
		global $wpdb;
		
			$forms = $wpdb->insert( 'zcf_zohocrm_formfield_manager' , array( 'shortcode_id' => "$shortcode_id" , 'field_id' => "$field_id" , 'zcf_field_mandatory' => "$zcf_field_mandatory" , 'state' => "$state" , 'custom_field_type' => "$custom_field_type" , 'custom_field_values' => "$custom_field_values" , 'form_field_sequence' => "$form_field_sequence" , 'display_label' => "$display_label") );
		
	}

	function zcffieldsPropsettings($crmtype, $module,$layoutname)
	{
		global $wpdb;
		$fields = $wpdb->get_results($wpdb->prepare("select *from zcf_zohocrmform_field_manager where  module_type = '".$module."' and Layout_Name = '".$layoutname."' and readonly != '1' and field_name !='Layout' and  viewcreate_type != '0' and field_type NOT IN ('lookup','ownerlookup','multiselectlookup')") );
		
		return $fields;
	}

	function zcfformfieldsPropsettings($shortcode_name)
	{
		global $wpdb;
		$crm_type = 'crmformswpbuilder';
		$get_shortcode_id = $wpdb->get_results("select shortcode_id from zcf_zohoshortcode_manager where shortcode_name = '".$shortcode_name."' and crm_type ='".$crm_type."'");
		$shortcode_id = $get_shortcode_id[0]->shortcode_id;
		$field = $wpdb->get_results("select fm.field_mandatory,ffm.defaultvalues,ffm.rel_id,ffm.hiddenfield,ffm.field_id,fm.field_name,ffm.zcf_field_mandatory,ffm.form_field_sequence,ffm.state,fm.editupdate,ffm.display_label,fm.field_label,fm.layoutId,ffm.custom_field_values,ffm.custom_field_type from zcf_zohocrmform_field_manager fm join zcf_zohocrm_formfield_manager ffm ON ffm.field_id = fm.field_id join zcf_zohoshortcode_manager sm ON sm.shortcode_id = ffm.shortcode_id where sm.shortcode_id='{$shortcode_id}' and  ffm.state=1 and fm.viewcreate_type=1 and fm.field_type NOT IN ('lookup','ownerlookup','multiselectlookup') group by fm.field_name order by ffm.form_field_sequence");
		$editupdatecount = $wpdb->get_results( "select * from zcf_zohocrmform_field_manager fm join zcf_zohocrm_formfield_manager ffm ON ffm.field_id = fm.field_id join zcf_zohoshortcode_manager sm ON sm.shortcode_id = ffm.shortcode_id where sm.shortcode_name='{$shortcode_name}' and fm.editupdate=1 and fm.viewcreate_type=1 group by fm.field_name" );
		$i = 0;
		$crmFields = array();
		foreach($field as $newfields) 
		{
			$crmFields['fields'][$i]['field_id'] = $newfields->field_id;
			$crmFields['fields'][$i]['name'] = $newfields->field_name;
			$zcf_field_mandatory=$newfields->zcf_field_mandatory;
			if( $newfields->field_mandatory == 1 ){
				$crmFields['fields'][$i]['mandatory'] = 2;//$newfields->zcf_field_mandatory;
				 $zcf_field_mandatory=1;
			}else{
				$crmFields['fields'][$i]['mandatory'] = 0;
				 $zcf_field_mandatory=$newfields->zcf_field_mandatory;
			}

			$crmFields['fields'][$i]['zcf_mandatory'] = $zcf_field_mandatory;
			$crmFields['fields'][$i]['order'] = $newfields->form_field_sequence;
			$crmFields['fields'][$i]['rel_id'] = $newfields->rel_id;

			
			$crmFields['fields'][$i]['publish'] = $newfields->state;
			$crmFields['fields'][$i]['editupdate'] = $newfields->editupdate;
			$crmFields['fields'][$i]['display_label'] = $newfields->display_label;
			$crmFields['fields'][$i]['label'] = $newfields->field_label;
			$crmFields['fields'][$i]['layoutId'] = $newfields->layoutId;
			$crmFields['fields'][$i]['hiddenfield'] = $newfields->hiddenfield;
			if( $newfields->custom_field_type == 'select-multiple' || $newfields->custom_field_type =='select'){
				$crmFields['fields'][$i]['defaultvalue'] =  array( 'defaultvalues' => @unserialize($newfields->defaultvalues));

			}else{
				$crmFields['fields'][$i]['defaultvalue'] =  $newfields->defaultvalues;
			}
			$crmFields['fields'][$i]['type'] = array( 'picklistValues' => @unserialize($newfields->custom_field_values) , 'name' => $newfields->custom_field_type , 'defaultValue' => $newfields->custom_field_values );
			$i++;
		}
		$crmFields['fields']['editupdatecount'] = sizeof($editupdatecount);

		return $crmFields;
	} 

	function zcfFormPropSettings( $shortcodename = "" )
	{
		global $wpdb;
		$query = "";
		$where = "";
		if( $shortcodename != "" )
		{
			$where = " where shortcode_name = '$shortcodename'";
		}
		$query = "select * from zcf_zohoshortcode_manager";
		$sql = $query.$where;
		$results = $wpdb->get_results($sql);
		if( ( $shortcodename != "" ) && ( count( $results ) > 0 ) )
		{
			$return_results = $results[0];
			return $return_results;
		}
		else
		{
			return $results;
		}
	}
}
