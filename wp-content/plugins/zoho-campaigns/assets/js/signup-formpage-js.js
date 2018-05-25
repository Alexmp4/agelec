var zc_temporary_form = new Array();
var zc_form_response = new Array();
var zc_div_identifier = new Array();
var zc_div_flag = new Array();
var zc_largeformid = new Array();
var zc_quickformid = new Array();
var zc_allvisibleformid = new Array();
var $zc_j = jQuery.noConflict();
var $zc_j1 = jQuery.noConflict();
var zc_check_invalid = {};
var zc_slider_on = false,zc_temp_global_idval;
var zc_zcform_temp;
var zc_saved_id = -1;
var zc_saved_val = 0;
var zc_saved_form = '';
var zc_processing_list = -1;
var zc_savedsignupformid = -1;
var zc_selection = '';
var zc_from_index =0;
var zc_domain_url = "https://campaigns.zoho.com";
var zc_new_domain_url="https://wordpress.maillist-manage.com";
var zc_max_signup_form = 0;
var zc_processing_signup_form = 0;
var zc_goto_savedlist=false;
function zc_savedIdFinder(idval) {
    var countSize = jQuery("#saved_form_id_" + idval).val().search(/_/i);
    var my_val = jQuery("#saved_form_id_" + idval).val().substring(0, countSize).trim();
    if (zc_saved_val < my_val) {
        zc_saved_val = my_val;
        zc_saved_form = jQuery("#saved_form_id_" + idval).val().substring(countSize+1).trim();
        zc_saved_id = idval;
    }
}
jQuery(document).ready(function () {
    window.history.pushState(null,null,window.location.href.replace("settings-updated=true",""));
    jQuery("#wpcontent").css("margin-left","160px");
    data = {
            'action': 'zc_get_domain'
        };
     jQuery.post(ajaxurl, data, function(response) {
            jQuery("#zc_domain_url").attr("value",response);
            zc_domain_url = response;
			if(zc_domain_url.indexOf("eu")>-1){
				zc_new_domain_url = zc_new_domain_url.replace("com","eu");
			}
    if( ( ( (typeof zc_all_idval) != undefined ) && zc_all_idval.length == 0) && typeof zc_str != 'undefined' && zc_str != "1:") {
        //jQuery("#click_list_content").css("display","none");
        jQuery("#click_list_div").css("display","none");
        jQuery("#saved_list_content").css("display","none");
        jQuery("#proceed_div").css("display","none");
        jQuery("#morelist").css("display","none");
        jQuery("#no_list").css("display","block");
        jQuery("#no_list").html('<div class="zcwelcomepanouter" id="no_mailing_list" >' +
                        '<div class="zcwelcomepansf" style="width:100%">' +
                            '<div class="zctcenter zcmt20">' +
                                '<img src="' + zc_pluginDir + '/assets/images/zc_mailinglist.png" alt="">' +
                            '</div>' +
                            '<br>' +
                            '<h1>' +
                                'No Mailing list' +
                            '</h1>' +
                            '<div class=" zcmt15 zctxt zctcenter zcf14">' +
                                'No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.' +
                            '</div>' +
                        '</div>' +
                    '</div>');
        return;
    }

    if(zc_saved_page == true)   {
        if(zc_saved_form == 's')    {
            jQuery("#simple_short_code_"+zc_saved_id).css("color","#55B667");
        }
        else if(zc_saved_form == 'z')    {
            jQuery("#zcform_short_code_"+zc_saved_id).css("color","#55B667");
        }
    }
    if (zc_saved_id == -1 || zc_all_idval.length == zc_unsaved_idval.length) {
        jQuery("#saved_list_content").css("display","none");
        jQuery("#cancel_list_selection").css("display","none");
        zc_first_load = true;
        zc_unsaved_idval = [];
        zc_all_idval = zc_saved_idval.slice(0);
        zc_createNewForm();
        //jQuery("#click_list_content").css("display", "block");
        jQuery("#proceed_div").css("display","block");
        jQuery("#morelist").css("display","block");
        jQuery("#click_list_div").css("display","block");

    } else {
        jQuery("#saved_list_content").css("display","block");
        jQuery("#cancel_list_selection").attr("onClick","zc_back('1','')");
        for (var i = 0; i < zc_all_idval.length; i++) {
            if(jQuery.inArray( zc_all_idval[i],zc_unsaved_idval) < 0) {
                jQuery("#saved_div_"+zc_all_idval[i]).css("display","block");
            }
            if (jQuery("#create_simple_form_" + zc_all_idval[i]).length > 0) {
                jQuery("#preview_simple_" + zc_all_idval[i]).css("display", "none");
                jQuery("#edit_simple_" + zc_all_idval[i]).css("display", "none");
            }
            if (jQuery("#create_zoho_form_" + zc_all_idval[i]).length > 0) {
                jQuery("#preview_zoho_" + zc_all_idval[i]).css("display", "none");
                jQuery("#edit_zoho_" + zc_all_idval[i]).css("display", "none");
            }
        }
    }
     });
});
jQuery(document).mouseup(function () {
    if (zc_slider_on) {
        zc_slider_on = false;
        $zc_j1("#sliderToolTip_" + zc_temp_global_idval).fadeOut(1000);
    }
});
function zc_dropDownListToggle(val) {
    if(val == 1)    {
        jQuery("#drop_down_list").toggle();
    }
    else if(val == 2)   {
        jQuery("#drop_down_list").hide();
    }
}
function zc_selectUserType(val)    {
    jQuery("#choosen_user_type").attr("value",val);
    var userType = 0;
    if(val == 1) {
        userType = "All Mailing Lists";
    }
    else if(val == 2) {
        userType = "My Mailing Lists";
    }
    else if(val == 3) {
        userType = "Other User Mailing Lists";
    }
    jQuery("#drop_down_list").find("li").show();
    jQuery("#drop_down_list").find("li[value='" + val + "']").hide();
    jQuery("#choosen_user_type").find("span").text(userType);
    zc_first_load = true;
    zc_unsaved_idval = [];
    zc_all_idval = zc_saved_idval.slice(0);
    zc_createNewForm();
    /*if(jQuery("#unsaved_list").html() == "") {
        jQuery("#unsaved_list").html('<div class=" zcmt15 zctxt zctcenter zcf14" id="no_list_message"><h4>No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.</h4></div>');
        // alert("No Mailing list available from Zoho-Campaigns to show. Either create new Mailing List or check for the privilege to access it.");
    } */
}
function zc_searchList()   {
    var query = jQuery("#search_bar").val();
    var userType = jQuery("#choosen_user_type").attr("value");
    var unwantedsno = zc_all_idval.join(":");
    if(unwantedsno == "") {
        unwantedsno = "1";
    }
    var api_url = zc_domain_url + '/api/getmailinglistsprivacy?authtoken=' +  zc_on_load_apikey_val + '&scope=CampaignsAPI&sort=asc&fromindex=1&range=20&resfmt=JSON&listname='+query+'&usertype='+userType+'&unwantedsno='+unwantedsno;
    jQuery("#unsaved_list").append('<div id="temp_loading_div" align="center"><h1>Loading...</h1><img id="laoding_-1" src="' + zc_pluginDir + '/assets/images/uploadImg.gif"  /></div>');
    if(query == "") {
        jQuery("#temp_loading_div").remove();
        alert("Enter Mailing list name.");
        return;
    }
    jQuery.ajax({
        type: 'POST',
        url: api_url,
        success: function (responseData, textStatus, jqXHR) {
            jQuery("#temp_loading_div").remove();
            var sno,name,unSavedListTemplate,listkey,validlist;
            // responseData = JSON.stringify(responseData);
            if((typeof responseData) != "object")
            {
                responseData = JSON.parse(responseData);
            }
            if(responseData.list_of_details != null) {    
                var newVal = false;
                jQuery.each(responseData.list_of_details,function(index,val){
                    sno = val.s_no;
                    validlist = val.validlist;
                    if( (zc_all_idval.indexOf(sno) < 0) && validlist == "true") {
                        newVal = true;
                        name = val.listname;
                        listkey = val.listkey;
                        zc_unsaved_idval[zc_unsaved_idval.length] = sno;
                        zc_all_idval[zc_all_idval.length] = sno;
                        unSavedListTemplate = "<div class=\"zcmlist\" id=\"list_name_" + sno + "\" onclick=\"javascript:zc_setSelection('" + sno + "','" + name + "');\"><img src=\"" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png\" style=\"vertical-align:middle;\" alt=\"O\"> &nbsp; <span class=\"zclstnmeblk\" style=\"vertical-align:middle;cursor:default;\">" + name + "</span><div style=\"display:none;\" id=\"list_key_" + sno + "\">" + listkey + "</div></div>";
                        jQuery("#unsaved_list").append(unSavedListTemplate);
                    }
                });
                if(!newVal)  {
                    alert("All the Mailing List are available already");
                }
            }
            else {
                alert("No Mailing List available with the name " + query);
            }
        },
        error: function (responseData, textStatus, errorThrown) {
            jQuery("#temp_loading_div").remove();
            alert("Some Problem in Loading!!");
        }
    });
}
function zc_moreList() {
    var query = jQuery("#search_bar").val();
    var userType = jQuery("#choosen_user_type").attr("value");
    var unwantedsno = zc_all_idval.join(":");
    if(unwantedsno == "") {
        unwantedsno = "1";
    }
    var api_url = zc_domain_url + '/api/getmailinglistsprivacy?authtoken=' +  zc_on_load_apikey_val + '&scope=CampaignsAPI&sort=asc&fromindex=1&range=2&resfmt=JSON&unwantedsno='+unwantedsno+'&usertype='+userType;
    if(query != "") {
        api_url += '&listname='+ query;
    }
    jQuery("#unsaved_list").append('<div id="temp_loading_div" align="center"><h1>Loading...</h1><img id="laoding_-1" src="' + zc_pluginDir + '/assets/images/uploadImg.gif"  /></div>');
    jQuery.ajax({
        type: 'POST',
        url: api_url,
        success: function (responseData, textStatus, jqXHR) {
            jQuery("#temp_loading_div").remove();
            zc_from_index += 2;
            // responseData = JSON.stringify(responseData);
            if((typeof responseData) != "object")
            {
                responseData = JSON.parse(responseData);
            }
            var sno,name,unSavedListTemplate,listkey,validlist;
            if(responseData.list_of_details != null) {
                jQuery.each(responseData.list_of_details,function(index,val){
                    sno = val.s_no;
                    validlist = val.validlist;
                    if( (zc_all_idval.indexOf(sno) < 0) && validlist == "true") {
                        name = val.listname;
                        listkey = val.listkey;
                        zc_unsaved_idval[zc_unsaved_idval.length] = sno;
                        zc_all_idval[zc_all_idval.length] = sno;
                        unSavedListTemplate = "<div class=\"zcmlist\" id=\"list_name_" + sno + "\" onclick=\"javascript:zc_setSelection('" + sno + "','" + name + "');\"><img src=\"" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png\" style=\"vertical-align:middle;\" alt=\"O\"> &nbsp; <span class=\"zclstnmeblk\" style=\"vertical-align:middle;cursor:default;\">" + name + "</span><div style=\"display:none;\" id=\"list_key_" + sno + "\">" + listkey + "</div></div>";
                        jQuery("#unsaved_list").append(unSavedListTemplate);
                    }
                });
            }
            else {
                alert("No more Mailing list available from Zoho-Campaigns to append");
            }
        },
        error: function (responseData, textStatus, errorThrown) {
            jQuery("#temp_loading_div").remove();
            alert("Some Problem in Loading!!");
        }
    });
}
function zc_editShow(idval, name, type,link_type,list_no) {
    jQuery("#saved_list_content").css("display", "none");
    for (var i = 0; i < zc_saved_idval.length; i++) {
        jQuery("#saved_div_" + zc_saved_idval[i]).css("display", "none");
    }
    jQuery("#create_content_div").find("img").css("display","none");
    jQuery("#create_content_div").css('display', 'block');
    if (type == 'simple') {
        if(link_type == 'create') {
            jQuery("#create_content_div_span").text("Create sign-up form for " + name);
        }
        else if(link_type == 'edit') {
            jQuery("#create_content_div_span").text("Edit Sign-up Form for " + name);
        }
        jQuery("#cancel_simple_form_changes_" + idval).attr("onClick", "zc_discardChanges('" + idval + "','edit');return false;");
        zc_formToggle(idval, 'simple_form_' + idval);
        zc_designshower(idval, '0');
    } else if (type == 'zoho') {
        /*if(link_type == "edit") {
            zc_savedsignupformid = jQuery("#signupformid_"+idval).val().trim().split("_")[0];
            var savedformtype = jQuery("#signupformid_"+idval).val().trim().split("_")[1];
            if(savedformtype == 0)  {
                jQuery("#mini_form_tab_"+idval).addClass("sel");
                jQuery("#big_form_tab_"+idval).removeClass("sel");
            }
            else if(savedformtype == 1) {
                jQuery("#mini_form_tab_"+idval).removeClass("sel");
                jQuery("#big_form_tab_"+idval).addClass("sel");
            }
        }
        jQuery("#cancel_zoho_form_changes_" + idval).attr("onClick", "zc_discardZohoFormChanges('" + idval + "','edit');return false;");
        zc_formToggle(idval, 'zoho_campaign_form_' + idval);*/
        if(link_type == "edit")
        {
            jQuery("#zc_form_text").text("Select a sign-up form. If you wish to edit the Sign-up form, head over to Zoho Campaigns.");
        }
        else if(link_type == "create")
        {
            jQuery("#zc_form_text").text("Forms available in your Zoho Campaigns account");
        }
        jQuery("#create_content_div_span").text("Select Sign-up Form for " + name);
        zc_callI('edit',list_no);
    }
}

function zc_hidePreviewSavedForm(idval) {
    jQuery("#background_div").css("display", "none");
    jQuery("#form_preview_container").css("display", "none");
}

function zc_showPreviewSavedForm(idval, type, name) {
    jQuery('html, body').animate({
        scrollTop: '0px'
    }, 0);
    jQuery("#background_div").css("display", "block");
	jQuery("#form_preview_container").css("display", "block");
	jQuery("#zoho_form_viewer_" + idval).find('.ui-datepicker-trigger').remove();
	/*if(jQuery("#zoho_form_viewer_" + idval).find('.ui-datepicker-trigger').length > 0){
		alert(jQuery("#zoho_form_viewer_" + idval).find('.ui-datepicker-trigger').length);
	}    */
    if (type == 'simple') {
        jQuery("#preview_container").html(jQuery("#total_form_" + idval).html());
    } else if (type == 'zoho') {
        jQuery("#preview_container").html(jQuery("#zoho_form_viewer_" + idval).html());
    }
    jQuery("#preview_heading").html("Preview");
}

function zc_hintandTrashToggle(idval) {
    jQuery("#trash_container_" + idval).toggle();
    if(jQuery("#shortcode_help_sform_" + idval).css("visibility") == 'visible' || jQuery("#shortcode_help_zform_" + idval).css("visibility") == 'visible' ) {
        jQuery("#shortcode_help_sform_" + idval).css("visibility","hidden");
        jQuery("#shortcode_help_zform_" + idval).css("visibility","hidden");
    }
    else {
        if(jQuery("#preview_simple_"+idval).css("display") != 'none')   {
            jQuery("#shortcode_help_sform_" + idval).css("visibility","visible");
        }
        if(jQuery("#preview_zoho_"+idval).css("display") != 'none')   {
            jQuery("#shortcode_help_zform_" + idval).css("visibility","visible");   
        }
    }
}

function zc_uiSliderChangeSetter(idval) {
	var startSliderWidth = jQuery("#total_form_child_" + idval).width();
    $zc_j1('#slider_' + idval).slider({
        animate: "slow",
        range: "min",
        min: 150,
        max: 400,
        value: startSliderWidth,
        step: 1
    }).bind('slide', function (event, ui) {
        jQuery("#total_form_child_" + idval).css("width", ui.value);
        jQuery("#total_form_" + idval).css("width", ui.value);
        if (ui.value < 307) {
            zc_fieldsWidth("80", idval);
        } else {
            zc_fieldsWidth("100", idval);
        }
        $zc_j1("#sliderToolTip_" + idval).css("margin-left", $zc_j1("#slider_" + idval).find("span").position().left - 17);
        $zc_j1("#sliderToolTip_" + idval).html(ui.value);
        $zc_j1("#sliderToolTip_" + idval).css("display", "inline-block");
        zc_slider_on = true;
        zc_temp_global_idval = idval;
    });
    $zc_j1('#slider_' + idval).bind('mouseup', function (event) {
        var left = $zc_j1("#slider_" + idval).find("span").position().left;
        zc_slider_on = false;
        $zc_j1("#sliderToolTip_" + idval).css("margin-left", (event.clientX - $zc_j1("#slider_" + idval).offset().left));
        $zc_j1("#sliderToolTip_" + idval).html(Math.trunc(jQuery("#total_form_child_" + idval).width()));
        $zc_j1("#sliderToolTip_" + idval).css("display", "block");
        $zc_j1("#sliderToolTip_" + idval).fadeOut(1000);
    });

}

function zc_makeSortable(idval) {
    $zc_j("#adder_ul_container_" + idval).sortable({
        axis: "y",
        update: function (event, ui) {
            zc_liElementArranger(idval);
        }
    });
    // $zc_j( "#adder_ul_container_"+idval).disableSelection();
}

function zc_liElementArranger(idval) {
    var ulChildrenObj = jQuery("#adder_ul_container_" + idval).children();
    var count;
    var childId, correspondingFormElementId;
    var identifier;
    var elementObj, elementCloneObj;
    var ulElementObj = jQuery("#form_body_" + idval);
    for (count = 0; count < ulChildrenObj.length; count++) {
        childId = ulChildrenObj[count].id;
        identifier = childId.substring(9, 14);
        correspondingFormElementId = "form_" + identifier + "_li_" + idval;
        if (jQuery("#" + correspondingFormElementId).length) {
            elementObj = jQuery("#" + correspondingFormElementId);
            elementCloneObj = elementObj.clone();
            elementObj.remove();
            ulElementObj.append(elementCloneObj);
        }
    }
}

function zc_customLabelToggle(idval) {
    jQuery("#label_field_div_" + idval).toggle();
    if (jQuery("#label_field_div_" + idval).css("display") == 'none') {
        jQuery("#label_field_div_image_" + idval).attr("src", zc_pluginDir + "/assets/images/zcrgtarw.png");
        jQuery("#label_field_div_image_" + idval).attr("height", "11");
    } else {
        jQuery("#label_field_div_image_" + idval).attr("src", zc_pluginDir + "/assets/images/zcdwnarw.png");
        jQuery("#label_field_div_image_" + idval).removeAttr("height");
        jQuery("#customizer__div_" + idval).css("display", "none");
        jQuery("#customizer__div_image_" + idval).attr("src", zc_pluginDir + "/assets/images/zcrgtarw.png");
        jQuery("#customizer__div_image_" + idval).attr("height", "11");
    }
}

function zc_emptyCheck(me) {
    if (me.value == '') {
        var identifier = me.id.substring(6, 11);
        var idval = me.id.substring(22);
        if (identifier == 'title') {
            me.value = 'Form';
        } else if (identifier == 'email') {
            me.value = 'Email Address:';
        } else if (identifier == 'fname') {
            me.value = 'First Name:';
        } else if (identifier == 'lname') {
            me.value = 'Last Name:';
        } else if (identifier == 'phone') {
            me.value = 'Phone:';
        }
    }
}

function zc_checkContenet(me, identifier, idval) {
    var str;
    if (identifier == 0) {
        str = new XRegExp("^[^<^>^;^\"]+$");
    } else if (identifier == 1) {
        str = new XRegExp("^[0-9]+$");
    } else if (identifier == 2) {
        str = new XRegExp("^[\\p{L}0-9-_ :\\.]+$");
    }
    var elementValue = me.value;
    if (str.test(elementValue) || elementValue == '') {
        jQuery("#" + me.id).css("border", "1px solid #DDDDDD");
        var string = zc_check_invalid[idval];
        if (string == null) {
            string = '';
        }
        if (string.indexOf(me.id) >= 0) {
            string = string.replace(me.id + ";", '');
        }
        zc_check_invalid[idval] = string;
    } else {
        jQuery("#" + me.id).css("border", "2px solid red");
        var string = zc_check_invalid[idval];
        if (string == null) {
            string = me.id + ";";
        }
        if (string.indexOf(me.id) < 0) {
            string += me.id + ";";
        }
        zc_check_invalid[idval] = string;
    }
}

function zc_responsePreviwerShowerHider(idval) {
    jQuery('html, body').animate({
        scrollTop: '0px'
    }, 0);
    /*if(jQuery("#response_area_shower_"+idval).val() == "simple_zc_form_response")
    {*/
    jQuery("#preview_container_" + idval).toggle();
    jQuery("#background_div").toggle();
    zc_responseHeaderShower(document.getElementById("radio_header_" + idval));
    /*}
    else {
        alert("Preview not allowed for Zoho Campaigns’ based sign-up form responses. However, you can view and edit the responses in your Zoho Campaigns account.");
    }*/
}

function zc_responseBodyShower(me) {
    var idval;
    if (me.value == 'general') {
        idval = me.id.substring(25);
        jQuery("#bodyval_" + idval).html(jQuery("#genaral_error_body_" + idval).val().replace(/\n/g, "<br>"));
    } else if (me.value == 'email') {
        idval = me.id.substring(30);
        jQuery("#bodyval_" + idval).html(jQuery("#exists_email_body_" + idval).val().replace(/\n/g, "<br>"));
    }
}

function zc_responseHeaderShower(me) {
    var idval = me.id.substring(13);
    if(me.selectedIndex == 2)   {
        jQuery("#headval_" + idval).html(jQuery("#error_header_" + idval).val());
        jQuery("#bodyval_" + idval).html(jQuery("#exists_email_body_" + idval).val().replace(/\n/g, "<br>"));
    }
    else if (me.selectedIndex == 1) {
        jQuery("#headval_" + idval).html(jQuery("#error_header_" + idval).val());
        jQuery("#bodyval_" + idval).html(jQuery("#genaral_error_body_" + idval).val().replace(/\n/g, "<br>"));
    } else if (me.selectedIndex == 0) {
        jQuery("#headval_" + idval).html(jQuery("#success_header_" + idval).val());
        jQuery("#bodyval_" + idval).html(jQuery("#success_body_" + idval).val().replace(/\n/g, "<br>"));
    }
}

function zc_designDivToggle(idval) {
    jQuery("#customizer__div_" + idval).toggle();
    if (jQuery("#customizer__div_" + idval).css("display") == 'none') {
        jQuery("#customizer__div_image_" + idval).attr("src", zc_pluginDir + "/assets/images/zcrgtarw.png");
        jQuery("#customizer__div_image_" + idval).attr("height", "11");
    } else {
        jQuery("#customizer__div_image_" + idval).attr("src", zc_pluginDir + "/assets/images/zcdwnarw.png");
        jQuery("#customizer__div_image_" + idval).removeAttr("height");
        jQuery("#label_field_div_" + idval).css("display", "none");
        jQuery("#label_field_div_image_" + idval).attr("src", zc_pluginDir + "/assets/images/zcrgtarw.png");
        jQuery("#label_field_div_image_" + idval).attr("height", "11");
    }
}

function zc_responseContainerDisplay(idval) {
    var me = document.getElementById("response_div_image_" + idval);
    jQuery("#response_showe_container_" + idval).toggle();
    if (jQuery("#response_showe_container_" + idval).css("display") == 'none') {
        me.src = zc_pluginDir + '/assets/images/zcrgtarw.png';
        me.height = 11;
    } else {
        me.src = zc_pluginDir + '/assets/images/zcdwnarw.png';
        me.removeAttribute("height");
    }
}

function zc_designSetter(idval) {
    document.getElementById("adder_title_textfield_" + idval).value = document.getElementById("form_title_" + idval).innerHTML;
    document.getElementById("adder_email_textfield_" + idval).value = document.getElementById("form_email_label_" + idval).innerHTML;
    document.getElementById("adder_buton_textfield_" + idval).value = document.getElementById("form_button_" + idval).value;
    if (document.getElementById("form_phone_label_" + idval) != null) {
        document.getElementById("adder_phone_textfield_" + idval).value = document.getElementById("form_phone_label_" + idval).innerHTML.trim();
        document.getElementById("adder_phone_checkbox_" + idval).checked = true;
    } else {
        document.getElementById("adder_phone_textfield_" + idval).value = "Phone:";
        document.getElementById("adder_phone_checkbox_" + idval).checked = false;
        zc_elementManager(document.getElementById("adder_phone_checkbox_" + idval));
    }
    if (document.getElementById("form_lname_label_" + idval) != null) {
        document.getElementById("adder_lname_textfield_" + idval).value = document.getElementById("form_lname_label_" + idval).innerHTML.trim();
        document.getElementById("adder_lname_checkbox_" + idval).checked = true;
    } else {
        document.getElementById("adder_lname_textfield_" + idval).value = "Last Name:";
        document.getElementById("adder_lname_checkbox_" + idval).checked = false;
        zc_elementManager(document.getElementById("adder_lname_checkbox_" + idval));
    }
    if (document.getElementById("form_fname_label_" + idval) != null) {
        document.getElementById("adder_fname_textfield_" + idval).value = document.getElementById("form_fname_label_" + idval).innerHTML.trim();
        document.getElementById("adder_fname_checkbox_" + idval).checked = true;
    } else {
        document.getElementById("adder_fname_textfield_" + idval).value = "First Name:";
        document.getElementById("adder_fname_checkbox_" + idval).checked = false;
        zc_elementManager(document.getElementById("adder_fname_checkbox_" + idval));
    }

    var titleObj = document.getElementById("form_title_" + idval);
    zc_abstractDesignSetter(titleObj, idval, 'title');

    var labelObj = document.getElementById("form_email_label_" + idval);
    zc_abstractDesignSetter(labelObj, idval, 'label');

    var fieldObj = document.getElementById("form_email_input_" + idval);
    document.getElementById("field_bgcolbox_" + idval).style.backgroundColor = fieldObj.style.backgroundColor;

    zc_abstractDesignSetter(fieldObj, idval, 'field');

    var buttonObj = document.getElementById("form_button_" + idval);
    document.getElementById("bgcol_colorbox_" + idval).style.backgroundColor = buttonObj.style.backgroundColor;
    zc_abstractDesignSetter(buttonObj, idval, 'buton');

    document.getElementById("hedbg_colorbox_" + idval).style.backgroundColor = document.getElementById("form_title_" + idval).style.backgroundColor;
    document.getElementById("forbg_colorbox_" + idval).style.backgroundColor = document.getElementById("total_form_child_" + idval).style.backgroundColor;
    document.getElementById("borbg_colorbox_" + idval).style.backgroundColor = document.getElementById("total_form_child_" + idval).style.borderColor;

    var widthValue = document.getElementById("total_form_" + idval).style.width;
    // document.getElementById("form_width_size_"+idval).value = widthValue.substring(0,widthValue.length-2);
    /*if(response_area_shower == 'zoho_campaigns_response') {
        jQuery("#response_area_shower_"+idval).val('zoho_campaigns_response');
        jQuery("#response_editor_"+idval).css("display","none");
        jQuery("#zoho_zc_form_response_indicator_"+idval).css("display","block");
    }
    else {*/
    // jQuery("#response_area_shower_"+idval).val('simple_zc_form_response');
    jQuery("#response_editor_" + idval).css("display", "block");
    // jQuery("#zoho_zc_form_response_indicator_"+idval).css("display","none");
    // }

    var ulChildrenObj = jQuery("#form_body_" + idval).children();
    var count;
    var childId, correspondingFormElementId;
    var identifier;
    var elementObj, elementCloneObj;
    var ulElementObj = jQuery("#adder_ul_container_" + idval);
    for (count = ulChildrenObj.length - 1; count >= 0; count--) {
        childId = ulChildrenObj[count].id;
        identifier = childId.substring(5, 10);
        correspondingFormElementId = "adder_li_" + identifier + "_" + idval;

        elementObj = jQuery("#" + correspondingFormElementId);
        elementCloneObj = elementObj.clone();
        elementObj.remove();
        ulElementObj.prepend(elementCloneObj);
    }

}
/*function zc_responseShowerArea(me) {
    var idval = me.id.substring(21);
    jQuery("#response_editor_"+idval).toggle();
    jQuery("#zoho_zc_form_response_indicator_"+idval).toggle();
}*/
function zc_divHiderEvent() {
    jQuery('html').click(function () {
        var idval = zc_processing_list;
        if(zc_saved_idval.indexOf(zc_processing_list) < 0 ) {
            idval = -1;
        }
        if (zc_div_identifier[idval] != '' && zc_div_identifier[idval] != null) {
            zc_toggler(zc_div_identifier[idval], idval, zc_div_flag[idval]);
            if (zc_div_flag[idval] == 0) {
                if (document.getElementById(zc_div_identifier[idval] + "_color_picker_div_" + idval).style.display == 'none') {
                    zc_div_identifier[idval] = '';
                }
            } else if (zc_div_flag[idval] == 1) {
                if (document.getElementById(zc_div_identifier[idval] + "_bgcolor_picker_div_" + idval).style.display == 'none') {
                    zc_div_identifier[idval] = '';
                }
            }
        }
    });
}

function zc_formRetreiver(id, listkey) {
    var idval = id.substring(12);
    var radioButtonId = id.substring(0, 12);
    var formname;
    if(listkey == "-1") {
        listkey = jQuery("#list_key_"+zc_processing_list).text();
        if("" == listkey.trim())    {
            listkey = jQuery("#list_key_"+zc_processing_list).val();
        }
    }
    if (radioButtonId == 'button_form_') {
        formname = "ButtonForm";
    } else if (radioButtonId == 'embed__form_') {
        formname = "EmbededForm";
    } else if (radioButtonId == 'custom_form_') {
        formname = "CustomForm";
    }
    jQuery("#zoho_form_viewer_" + idval).html(' Loading...<br/><img id="laoding_' + idval + '" src="' + zc_pluginDir + '/assets/images/uploadImg.gif"  />');
    jQuery("#zcform_button_" + idval).attr("disabled", "disabled");
    var signupformid = jQuery("#signup_form_key_"+zc_processing_signup_form).text();
    var signupformname = jQuery("#signup_form_name_"+zc_processing_signup_form).find("[name='signupformname']").text();
    jQuery('#zc_form_save_'+idval).hide();
    jQuery('#zc_form_cancel_'+idval).hide();
    jQuery('#zc_form_edit_'+idval).hide();
    jQuery('#zc_form_name_'+idval).show();
    jQuery('#zc_edit_delete_panel_'+idval).show();

    jQuery("#zc_form_name_"+idval).text(signupformname);
    jQuery("#zc_form_edit_"+idval).val(signupformname);
    var form_url = zc_new_domain_url + "/api/xml/formdetails?authtoken=" + zc_on_load_apikey_val + "&scope=CampaignsAPI&version=1&resfmt=json&listkey=" + listkey /*+ "&resform=" + formname*/+"&userDomain=wordpress&signupformid="+signupformid;
/*    if(zc_savedsignupformid != '-1')    {
        var savedformtype = jQuery("#signupformid_"+idval).val().trim().split("_")[1];
        if(savedformtype == 0 && jQuery("#mini_form_tab_"+idval).hasClass('sel'))  {
            form_url +=  "&signupformid="+zc_savedsignupformid;
        }
        else if(savedformtype == 1 && jQuery("#big_form_tab_"+idval).hasClass('sel')) {
            form_url +=  "&signupformid="+zc_savedsignupformid;
        }
    }*/

    jQuery.ajax({
        type: 'POST',
        url: form_url,
        success: function (responseData, textStatus, jqXHR) {
            var statusText = responseData.status;
            var codeText = responseData.code;
            jQuery("#zc_api_key_error").html('');
            if (statusText == 'success' && codeText == 0) {
                if(zc_ver == "1")
                {
                    var formValue = responseData.message;
                    jQuery("#zoho_form_viewer_" + idval).html("<style>table,td,tr{border-width:0px 0 0 0px;}</style>" + formValue);
                }
                else if(zc_ver == "2")
                {
                    zc_allvisibleformid = [];
                    zc_allvisibleformid[zc_allvisibleformid.length] = responseData.results.currentformid;
                    zc_largeformid = responseData.results.largeformids.split(";");
                    zc_quickformid = responseData.results.quickformids.split(";");
                    // alert("2");
                    /*if(responseData.results.formtype == "bigform")  {
                        jQuery("#mini_form_tab").removeClass("sel");
                        jQuery("#big_form_tab").addClass("sel");

                    }
                    else   {
                        jQuery("#mini_form_tab").addClass("sel");
                        jQuery("#big_form_tab").removeClass("sel");

                    }*/
                    jQuery("#zcform_button_" + idval).removeAttr("disabled");
                    var formValue = responseData.results.formhtml;
                    var buttonFormValue = responseData.results.buttonhtml;
                    var buttonCreated = true;
                    var callFunction = 'zc_formselection(this)';
                    var opacity = '';
                    if(buttonFormValue == undefined || buttonFormValue.indexOf("You haven't created a button for this sign-up form") > 0)
                    {
                        buttonCreated = false;
                        opacity = 'opacity:0.3;filter:alpha(opacity=60);';
                        callFunction = '';
                    }
                    formValue = formValue.replace(/width:700px/g,"");
                    formValue = formValue.replace(/trackSignupEvent/g,"//trackSignupEvent");
                    buttonFormValue = buttonFormValue.replace(/trackSignupEvent/g,"//trackSignupEvent");

                    jQuery("#zoho_form_viewer_" + idval).html(
                        "<div id='signupform_"+(zc_allvisibleformid.length - 1)+"' data-signupid='" + responseData.results.currentformid + "'>" +
                            "<div id='buttonform_"+(zc_allvisibleformid.length - 1)+"'>" +
                                "<div class='zcmt40' id='buttonformdiv_"+(zc_allvisibleformid.length - 1)+"'>" +
                                    "<img style='float:left;margin-right:20px;" + opacity + "' align='absmiddle' src='" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png' onClick='" + callFunction + "' id='BFradiobutton_" + (zc_allvisibleformid.length - 1) + "' />" +
                                    "<div style='float:left;display:inline-block;' id='buttonformviewer_"+(zc_allvisibleformid.length - 1)+"' >" +
                                        buttonFormValue +
                                    "</div>" +
                                "</div>" +
                            "</div><div style='clear:both;'></div>" +
                            "<div style='margin-top:60px;' id='originalform_"+(zc_allvisibleformid.length - 1)+"'>" +
                                "<div class='zcmt15' id='originalformdiv_"+(zc_allvisibleformid.length - 1)+"'>" +
                                    "<img style='float:left; margin-right:20px;' align='absmiddle' src='" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png' onClick='zc_formselection(this)' id='OFradiobutton_" + (zc_allvisibleformid.length - 1) + "' />" +
                                    "<div style='float:left;display:inline-block;' id='originalformviewer_"+(zc_allvisibleformid.length - 1)+"'>" +
                                        formValue +
                                    "</div>" +
                                "</div>" +
                            "</div><div style='clear:both;'></div>" +
                        "</div>"
                    );
                    jQuery("#signupMainDiv").css("width","");
                    jQuery("#captchaDiv").css("height","100");
                }
                jQuery("#originalformviewer_"+(zc_allvisibleformid.length - 1)).find("#SIGNUP_PAGE").css("position","");
                jQuery("#originalformviewer_"+(zc_allvisibleformid.length - 1)).find("#SIGNUP_PAGE").css("overflow","");
                jQuery("body").css("backgroundColor","transparent");
                jQuery("body").css("font-family","");
                jQuery("#signUpFormErrorHeader").html(responseData.message);
            }
            else if(responseData.code == 8000 && responseData.status == "error")    {
                jQuery("#zcformcontainer_"+idval).css("display","none");
                jQuery("#zcform_save_button_"+idval).css("display","none");
                jQuery("#zoho_form_div_"+idval).css("display","none");
                jQuery("#zcsignupformmsg_"+idval).css("display","block");
                jQuery("#signUpFormErrorHeader").html(responseData.message);
                //
                return;
            }
            else if(responseData.code == 8001 && responseData.status == "error")    {
                jQuery("#zoho_form_viewer_"+idval).html(jQuery("#zcsignupformmsg_"+idval).clone());
                var innerEmptySignUpFormDiv = jQuery("#zoho_form_viewer_"+idval).find("#zcsignupformmsg_"+idval);
                jQuery(innerEmptySignUpFormDiv).css("display","block");
                jQuery(innerEmptySignUpFormDiv).removeAttr("id");
                jQuery("#signUpFormErrorHeader").html(responseData.message);
                return;
            }
            else if(responseData.code == 2006 && responseData.status == "error")    {
                jQuery("#zoho_form_viewer_"+idval).html(responseData.message);
                jQuery("#signUpFormErrorHeader").html(responseData.message);
            }
            else {
                jQuery("#zcform_button_" + idval).removeAttr("disabled");
                jQuery("#zoho_form_viewer_" + idval).html("statusText:" + statusText + " codeText:" + codeText);
            }
        },
        error: function (responseData, textStatus, errorThrown) {
            jQuery("#zcform_button_" + idval).removeAttr("disabled");
            jQuery("#zoho_form_viewer_" + idval).html(errorThrown);
        }
    });

}
function zc_addForm(idval,signupformid)  {
    var i = 0;
    // var resform = "";
    var listkey="";
    var saved_form = false;
    if(idval != -1) {
        zc_processing_list = idval;
    }
    jQuery('#zc_form_name_'+idval).show();
    jQuery('#zc_edit_delete_panel_'+idval).show();
    var signupformname = jQuery("#signup_form_name_"+zc_processing_signup_form).find("[name='signupformname']").text();
    jQuery("#zc_form_name_"+idval).text(signupformname);
    jQuery("#zc_form_edit_"+idval).val(signupformname);
    listkey = jQuery("#list_key_"+zc_processing_list).text();
    if("" == listkey.trim())    {
        listkey = jQuery("#list_key_"+zc_processing_list).val();
    }

    /*if(jQuery("#big_form_tab_"+idval).hasClass('sel')) {
        if(signupformid == -1)  {
            for(i=0;i<zc_largeformid.length;i++) {
                if(zc_largeformid[i] != '' && !(jQuery.inArray(zc_largeformid[i],zc_allvisibleformid) >= 0) )   {
                    signupformid = zc_largeformid[i];
                    break;
                }
            }
        }
        else {
            saved_form = true;
        }
        resform = "CustomForm";
    }
    else if(jQuery("#mini_form_tab_"+idval).hasClass('sel'))   {
        if(signupformid == -1)  {
            for(i=0;i<zc_quickformid.length;i++) {
                if(zc_quickformid[i] != '' && zc_quickformid[i] != null && !(jQuery.inArray(zc_quickformid[i],zc_allvisibleformid) >= 0) )   {
                    signupformid = zc_quickformid[i];
                    break;
                }
            }
        }
        else {
            saved_form = true;
        }
        resform = "EmbededForm";
    }*/
    if(signupformid == -1)  {
        alert("No SignUp Form to show")
        return;
    }
    signupformid = jQuery("#signup_form_key_"+zc_processing_signup_form).text();
    jQuery("#zoho_form_viewer_" + idval).html('<img style="vertical-align: middle;margin-top: 40px;"" id="laoding_' + idval + '" src="' + zc_pluginDir + '/assets/images/uploadImg.gif"  /><span style="font-size:20px;display:block;margin-top:10px;">Loading...</span>');
    var form_url = zc_new_domain_url + "/api/xml/formdetails?authtoken=" + zc_on_load_apikey_val + "&scope=CampaignsAPI&version=1&resfmt=json&listkey=" + listkey + "&userDomain=wordpress&signupformid=" + signupformid;
    jQuery.ajax({
        type: 'POST',
        url: form_url,
        success: function (responseData, textStatus, jqXHR) {
            var statusText = responseData.status;
            var codeText = responseData.code;
            jQuery("#zc_api_key_error").html('');
            if (statusText == 'success' && codeText == 0) {
                if(zc_ver == "1")
                {
                    // jQuery("#zoho_form_viewer_" + idval).html("<style>table,td,tr{border-width:0px 0 0 0px;}</style>" + formValue);
                }
                else if(zc_ver == "2")
                {
                    if(saved_form)  {
                        zc_allvisibleformid = [];
                        zc_allvisibleformid[zc_allvisibleformid.length] = responseData.results.currentformid;
                        zc_largeformid = responseData.results.largeformids.split(";");
                        zc_quickformid = responseData.results.quickformids.split(";");
                    }
                    zc_allvisibleformid[zc_allvisibleformid.length] = responseData.results.currentformid;
                    jQuery("#zcform_button_" + idval).removeAttr("disabled");
                    var formValue = responseData.results.formhtml;
                    var buttonFormValue = responseData.results.buttonhtml;
                    var buttonCreated = true;
                    var callFunction = 'zc_formselection(this)';
                    var opacity = '';
                    if(buttonFormValue == undefined || buttonFormValue.indexOf("You haven't created a button for this sign-up form") > 0) {
                        buttonCreated = false;
                        opacity = 'opacity:0.3;filter:alpha(opacity=60);';
                        callFunction = '';
                    }
                    formValue = formValue.replace(/width:700px/g,"");
                    formValue = formValue.replace(/trackSignupEvent/g,"//trackSignupEvent");
                    buttonFormValue = buttonFormValue.replace(/trackSignupEvent/g,"//trackSignupEvent");
                    jQuery("#zoho_form_viewer_" + idval).html(
                        "<div id='signupform_"+(zc_allvisibleformid.length - 1)+"' data-signupid='" + responseData.results.currentformid + "'>" +
                            "<div id='buttonform_"+(zc_allvisibleformid.length - 1)+"'>" +
                                "<div class='zcmt40' id='buttonformdiv_"+(zc_allvisibleformid.length - 1)+"'>" +
                                    "<img style='float:left;margin-right:20px;" + opacity + "' align='absmiddle' src='" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png' onClick='" + callFunction + "' id='BFradiobutton_" + (zc_allvisibleformid.length - 1) + "' />" +
                                    "<div style='float:left;display:inline-block;' id='buttonformviewer_"+(zc_allvisibleformid.length - 1)+"' >" +
                                        buttonFormValue +
                                    "</div>" +
                                "</div>" +
                            "</div><div style='clear:both;'></div>" +
                            "<div style='margin-top:60px;' id='originalform_"+(zc_allvisibleformid.length - 1)+"'>" +
                                "<div class='zcmt15' id='originalformdiv_"+(zc_allvisibleformid.length - 1)+"'>" +
                                    "<img style='float:left; margin-right:20px;' align='absmiddle' src='" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png' onClick='zc_formselection(this)' id='OFradiobutton_" + (zc_allvisibleformid.length - 1) + "' />" +
                                    "<div style='float:left;display:inline-block;' id='originalformviewer_"+(zc_allvisibleformid.length - 1)+"'>" +
                                        formValue +
                                    "</div>" +
                                "</div>" +
                            "</div><div style='clear:both;'></div>" +
                        "</div>"
                    );
                    jQuery("#originalformviewer_"+(zc_allvisibleformid.length - 1)).find("#SIGNUP_PAGE").css("position","");
                    jQuery("#originalformviewer_"+(zc_allvisibleformid.length - 1)).find("#SIGNUP_PAGE").css("overflow","");
                    var savedBF = jQuery("#signupformid_"+idval).val().trim().split("_")[2];
                    if(idval != -1 && jQuery("#zoho_form_viewer_2").find("[id*='signupform_']").length == 1) {
                        if(savedBF == 0)    {
                            jQuery("#BFradiobutton_"+(zc_allvisibleformid.length - 1)).attr("src",zc_pluginDir + "/assets/images/zc_radiobtnchkd.png");
                        }
                        else if(savedBF == 1)   {
                            jQuery("#OFradiobutton_"+(zc_allvisibleformid.length - 1)).attr("src",zc_pluginDir + "/assets/images/zc_radiobtnchkd.png");
                        }
                    }
                }
            }
            else if(responseData.code == 8000 && responseData.status == "error")    {
                jQuery("#zcformcontainer_"+idval).css("display","none");
                jQuery("#zcform_save_button_"+idval).css("display","none");
                jQuery("#zoho_form_div_"+idval).css("display","none");
                jQuery("#zcsignupformmsg_"+idval).css("display","block");
                return;
            }
            else if(responseData.code == 8001 && responseData.status == "error")    {
                jQuery("#zoho_form_viewer_"+idval).html(jQuery("#zcsignupformmsg_"+idval).clone());
                return;
            }
            else if(responseData.code == 2006 && responseData.status == "error")    {
                jQuery("#zoho_form_viewer_"+idval).html(responseData.message);
            }
            else {
                jQuery("#zcform_button_" + idval).removeAttr("disabled");
                jQuery("#zoho_form_viewer_" + idval).html("statusText:" + statusText + " codeText:" + codeText);
            }
        },
        error: function (responseData, textStatus, errorThrown) {
            jQuery("#zcform_button_" + idval).removeAttr("disabled");
            jQuery("#zoho_form_viewer_" + idval).html(errorThrown);
        }
    });   
}
function zc_formselection(me) {
    var i=0;
    for(i=0;i<=zc_largeformid.length;i++)   {
        jQuery("#OFradiobutton_"+i).attr("src",zc_pluginDir + "/assets/images/zc_radiobtnnrml.png");
        jQuery("#BFradiobutton_"+i).attr("src",zc_pluginDir + "/assets/images/zc_radiobtnnrml.png");
    }
    me.src = zc_pluginDir + "/assets/images/zc_radiobtnchkd.png";
}
function zc_tabchange(option,idval) {
    if(option == '2' && jQuery("#mini_form_tab_"+idval).hasClass('sel'))
    {
        jQuery("#mini_form_tab_"+idval).removeClass('sel');
        jQuery("#big_form_tab_"+idval).addClass('sel');
        zc_formRetreiver("custom_form_"+idval,"-1");
    }
    else if(option == '1' && jQuery("#big_form_tab_"+idval).hasClass('sel'))
    {
        jQuery("#big_form_tab_"+idval).removeClass('sel');
        jQuery("#mini_form_tab_"+idval).addClass('sel');
        zc_formRetreiver("embed__form_"+idval,"-1");
    }
}
function zc_radioButtonChecker(idval) {
    var currentElement = jQuery("#zoho_form_viewer_" + idval);
    if (currentElement.find("div[id='main-container']").is(":visible")) {
        zc_selection = 'custom_form';
        jQuery("#custom_form_" + idval).attr("checked", "checked");
    } else if (currentElement.find("input[id='zcemail']").is(":visible")) {
        zc_selection = 'mini_form';
        jQuery("#embed__form_" + idval).attr("checked", "checked");
    } else if (currentElement.find("div[id='zc_embed_signup']").is(":visible")) {
        zc_selection = 'button_form';
        jQuery("#button_form_" + idval).attr("checked", "checked");
    }
}

function zc_designshower(idval, toggleBoolean) {
    zc_designSetter(idval);
    document.getElementById("form_holder_" + idval).className = "zcsfrmcntr";
    document.getElementById("designer_div_" + idval).style.display = "block";
    document.getElementById("design_shower_" + idval).style.display = "none";

    jQuery("#success_header_" + idval).val(jQuery("#success_header_" + idval).val().trim());
    jQuery("#success_body_" + idval).val(jQuery("#success_body_" + idval).val().trim());
    jQuery("#error_header_" + idval).val(jQuery("#error_header_" + idval).val().trim());
    jQuery("#genaral_error_body_" + idval).val(jQuery("#genaral_error_body_" + idval).val().trim());
    jQuery("#exists_email_body_" + idval).val(jQuery("#exists_email_body_" + idval).val().trim());
    jQuery("#adder_title_textfield_" + idval).val(jQuery("#adder_title_textfield_" + idval).val().trim());
    jQuery("#adder_email_textfield_" + idval).val(jQuery("#adder_email_textfield_" + idval).val().trim());
    jQuery("#adder_fname_textfield_" + idval).val(jQuery("#adder_fname_textfield_" + idval).val().trim());
    jQuery("#adder_lname_textfield_" + idval).val(jQuery("#adder_lname_textfield_" + idval).val().trim());
    jQuery("#adder_phone_textfield_" + idval).val(jQuery("#adder_phone_textfield_" + idval).val().trim());
    jQuery("#adder_buton_textfield_" + idval).val(jQuery("#adder_buton_textfield_" + idval).val().trim());

    if (toggleBoolean != 1) {
        zc_temporary_form[idval] = document.getElementById("total_form_" + idval).cloneNode(true);
        zc_form_response["success_header_" + idval] = jQuery("#success_header_" + idval).val().trim();
        zc_form_response["success_body_" + idval] = jQuery("#success_body_" + idval).val().trim();
        zc_form_response["error_header_" + idval] = jQuery("#error_header_" + idval).val().trim();
        zc_form_response["genaral_error_body_" + idval] = jQuery("#genaral_error_body_" + idval).val().trim();
        zc_form_response["exists_email_body_" + idval] = jQuery("#exists_email_body_" + idval).val().trim();
        jQuery("#total_response_" + idval).css("display", "block");
        jQuery("#save_cancel_button_div_" + idval).css("display", "block");
        jQuery("#customize_td_" + idval).css("display", "block");
        jQuery("#simple_form_content_" + idval).css("display", "block");
        jQuery("#preview_text_div_" + idval).css("display", "block");
    }
    var success_body = "Thank you for joining.\n\nYour interest preferences have been recorded successfully.\nPlease check your email to confirm your subscription. In order to activate your subscription, you need to click on the link in that email.  \n\nNote: If you don’t receive the email in your inbox shortly, please check your junk or spam folder.";
    var genaral_error_body = "An error occured while trying to subscribe.\n\nPlease try again later.";
    var orginialFormVal = "<div style=\"background-color:#fff;border:solid 1px rgb(207, 207, 207);\" id=\"total_form_child_" + idval + "\">" +
        "<div style=\"background-color:rgb(228, 228, 228);font-family:arial;font-size:14px;color:#000000;padding:15px;border-bottom:solid 1px rgb(207, 207, 207);\" id=\"form_title_" + idval + "\" align=\"center\">Subscribe to our Newsletter</div>" +
        "<div id=\"form_body_container_" + idval + "\" style=\"background-color:#fff;\">" +
        "<ul style=\"list-style-type:none;padding:10px 15px; margin-top:10px;\" id=\"form_body_" + idval + "\">" +
        "<li style=\"padding-top:13px;\" id=\"form_email_li_" + idval + "\">" +
        "<div style=\" width:110px; float:left; font-size:14px; font-family:arial;margin-top:5px;color:#000000;\" id=\"form_email_label_" + idval + "\">Email Address:</div>" +
        "<div style=\"  width:60%; float:left;min-width:150px;\"><input name=\"CONTACT_EMAIL\" style=\"font-size:12px; border:solid 1px #dcdcdc; width:100%; height:30px; padding:5px;color:#000000;background-color:#ffffff;font-family:arial;\" valType=\"placeHolder\" id=\"form_email_input_" + idval + "\" type=\"text\" /></div>" +
        "<div style=\"clear:both;\"></div>" +
        "</li>" +
        "</ul>" +
        "<div style=\" padding:10px 15px; text-align:center; margin-bottom:10px;\">" +
        "<input type=\"button\" class=\"button button-primary\" style=\"box-shadow: none;background-color:#e07070;font-family:arial; font-size:14px; color:#fff; border:solid 1px #e07070; border-radius:5px;\" value=\"Join Now\" id=\"form_button_" + idval + "\"/>" +
        "</div>" +
        "</div>" +
        "</div>";
    var currentHolder = jQuery("#total_form_" + idval).html();
    jQuery("#total_form_" + idval).html(orginialFormVal);
    if (jQuery("#adder_title_textfield_" + idval).val().trim() != "Subscribe to our Newsletter" || jQuery("#adder_email_textfield_" + idval).val().trim() != "Email Address:" || jQuery("#adder_fname_textfield_" + idval).val().trim() != "First Name:" || jQuery("#adder_lname_textfield_" + idval).val().trim() != "Last Name:" || jQuery("#adder_phone_textfield_" + idval).val().trim() != "Phone:" || jQuery("#adder_buton_textfield_" + idval).val().trim() != "Join Now" || jQuery("#success_header_" + idval).val().trim() != "Activate your Subscription" || jQuery("#error_header_" + idval).val().trim() != "Registration Failed!" || jQuery("#exists_email_body_" + idval).val().trim() != "This email address already exists in the mailing list." || jQuery("#success_body_" + idval).val().trim() != success_body || jQuery("#genaral_error_body_" + idval).val().trim() != genaral_error_body || /*jQuery("#response_area_shower_"+idval).val().trim() != "simple_zc_form_response" ||*/ jQuery("#adder_phone_checkbox_" + idval).prop("checked") || jQuery("#adder_lname_checkbox_" + idval).prop("checked") || jQuery("#adder_fname_checkbox_" + idval).prop("checked") || jQuery("#total_form_" + idval).html().replace(/\n/g, "").replace(/>\s+</g, "><").trim() != currentHolder.replace(/\n/g, "").replace(/>\s+</g, "><").trim()) {
        jQuery("#delete_form_" + idval).css("display", "inline-block");
    }
    jQuery("#total_form_" + idval).html(currentHolder);
}
/*
function zc_formBackgroundSetter (idval) {
    document.getElementById("total_form_"+idval).style.backgroundColor = document.getElementById("total_form_child_"+idval).style.backgroundColor;
    document.getElementById("total_form_"+idval).style.width = document.getElementById("total_form_child_"+idval).style.width;
    document.getElementById("total_form_"+idval).style.height = document.getElementById("total_form_child_"+idval).style.height;
}*/
function zc_discardChanges(idval, type) {
    jQuery("#simple_button_"+idval).find("img").css("display","none");
    if (type == 'create') {
        jQuery("#form_type_selector_div").css("display", "block");
        jQuery("#form_all_" + idval).css("display", "none");
        jQuery("#create_content_div").find("img").attr("onClick", "zc_back('2','" + idval + "');");
        jQuery("#simple_form_shower").attr("onClick", "zc_formToggle('" + idval + "','simple_form_" + idval + "');zc_designshower('" + idval + "','0');");
        jQuery("#zoho_form_shower").attr("onClick", "zc_formToggle('" + idval + "','zoho_campaign_form_" + idval + "');");
        zc_loadSignupFormList();
    } else if (type == 'edit') {
        jQuery("#create_content_div").css('display', 'none');
        jQuery("#form_all_" + idval).css("display", "none");
        jQuery("#saved_list_content").css("display", "block");
        for (var i = 0; i < zc_saved_idval.length; i++) {
            jQuery("#saved_div_" + zc_saved_idval[i]).css("display", "block");
        }
    }
    var totalForm = document.getElementById("total_form_" + idval);
    totalForm.innerHTML = zc_temporary_form[idval].innerHTML;
    jQuery("#success_header_" + idval).val(zc_form_response["success_header_" + idval].trim());
    jQuery("#success_body_" + idval).val(zc_form_response["success_body_" + idval].trim());
    jQuery("#error_header_" + idval).val(zc_form_response["error_header_" + idval].trim());
    jQuery("#genaral_error_body_" + idval).val(zc_form_response["genaral_error_body_" + idval].trim());
    jQuery("#exists_email_body_" + idval).val(zc_form_response["exists_email_body_" + idval].trim());

    totalForm.style.width = zc_temporary_form[idval].style.width;
    totalForm.style.height = zc_temporary_form[idval].style.height;

    jQuery("#total_response_" + idval).toggle();
    jQuery("#save_cancel_button_div_" + idval).toggle();
    jQuery("#customize_td_" + idval).toggle();
    if (zc_check_invalid[idval] != null) {
        var zc_check_invalidarray = zc_check_invalid[idval].split(";");
        for (var i = 0; i < zc_check_invalidarray.length; i++) {
            jQuery("#" + zc_check_invalidarray[i]).css("border", "1px solid #DDDDDD");
        }
        zc_check_invalid[idval] = '';
    }
    jQuery("#simple_form_content_" + idval).toggle();
    jQuery("#preview_text_div_" + idval).toggle();
    zc_setSortableInOrder(idval);
    return false;
}

function zc_setSortableInOrder(idval) {
    var emailLiClone = jQuery("#adder_li_email_" + idval).clone();
    jQuery("#adder_li_email_" + idval).remove();
    var fnameLiClone = jQuery("#adder_li_fname_" + idval).clone();
    jQuery("#adder_li_fname_" + idval).remove();
    var lnameLiClone = jQuery("#adder_li_lname_" + idval).clone();
    jQuery("#adder_li_lname_" + idval).remove();
    var phoneLiClone = jQuery("#adder_li_phone_" + idval).clone();
    jQuery("#adder_li_phone_" + idval).remove();

    jQuery("#adder_ul_container_" + idval).append(emailLiClone);
    jQuery("#adder_ul_container_" + idval).append(fnameLiClone);
    jQuery("#adder_ul_container_" + idval).append(lnameLiClone);
    jQuery("#adder_ul_container_" + idval).append(phoneLiClone);
}

function zc_formLabelChanger(me) {
    var idval = me.id.substring(22);
    zc_checkContenet(me, 2, idval);
    var input_field_name = me.id.substring(0, 22);
    if (input_field_name == 'adder_title_textfield_') {
        if (document.getElementById("form_title_" + idval) != null) {
            document.getElementById("form_title_" + idval).innerHTML = ((me.value == '') ? 'Form' : me.value);
        }
    } else if (input_field_name == 'adder_email_textfield_') {
        if (document.getElementById("form_email_label_" + idval) != null) {
            document.getElementById("form_email_label_" + idval).innerHTML = ((me.value == '') ? 'Email Address:' : me.value);
        }
    } else if (input_field_name == 'adder_fname_textfield_') {
        if (document.getElementById("form_fname_label_" + idval) != null) {
            document.getElementById("form_fname_label_" + idval).innerHTML = ((me.value == '') ? 'First Name:' : me.value);
        }
    } else if (input_field_name == 'adder_lname_textfield_') {
        if (document.getElementById("form_lname_label_" + idval) != null) {
            document.getElementById("form_lname_label_" + idval).innerHTML = ((me.value == '') ? 'Last Name:' : me.value);
        }
    } else if (input_field_name == 'adder_phone_textfield_') {
        if (document.getElementById("form_phone_label_" + idval) != null) {
            document.getElementById("form_phone_label_" + idval).innerHTML = ((me.value == '') ? '' : me.value);
        }
    } else if (input_field_name == 'adder_buton_textfield_') {
        if (document.getElementById("form_button_" + idval) != null) {
            document.getElementById("form_button_" + idval).value = ((me.value == '') ? 'Join Now' : me.value);
        }
    }
}

function zc_elementManager(me) {
    var idval = me.id.substring(21);
    var checkbox_name = me.id.substring(6, 11);
    if (me.checked) {
        if (checkbox_name == 'fname') {
            if (document.getElementById("form_fname_li_" + idval) == null) {
                var fname_textfield_value = document.getElementById("adder_fname_textfield_" + idval).value;
                document.getElementById("form_body_" + idval).innerHTML += '<li style="padding-top:13px;" id="form_fname_li_' + idval + '">' +
                    '<div style="width:110px; float:left; font-size:14px; margin-top:5px;color:#555;padding-right:10px;" align="right" id="form_fname_label_' + idval + '">' + ((fname_textfield_value == '') ? 'First Name:' : fname_textfield_value) + '</div>' +
                    '<div style="width:60%; float:left;min-width:150px;"> <input name="FIRSTNAME" style="font-size:12px; border:solid 1px #dcdcdc; width:100%; height:30px; padding:5px;" autocomplete="off" id="form_fname_input_' + idval + '" type="text" /></div>' +
                    '<div style="clear:both;"></div>' +
                    '</li>';
                document.getElementById("form_fname_label_" + idval).style.fontSize = document.getElementById("form_email_label_" + idval).style.fontSize;
                document.getElementById("form_fname_label_" + idval).style.fontFamily = document.getElementById("form_email_label_" + idval).style.fontFamily;
                document.getElementById("form_fname_label_" + idval).style.color = document.getElementById("form_email_label_" + idval).style.color;
                document.getElementById("form_fname_input_" + idval).style.fontSize = document.getElementById("form_email_input_" + idval).style.fontSize;
                document.getElementById("form_fname_input_" + idval).style.fontFamily = document.getElementById("form_email_input_" + idval).style.fontFamily;
                document.getElementById("form_fname_input_" + idval).style.color = document.getElementById("form_email_input_" + idval).style.color;
                document.getElementById("form_fname_input_" + idval).style.backgroundColor = document.getElementById("form_email_input_" + idval).style.backgroundColor;
                $zc_j("#form_fname_input_" + idval).width($zc_j("#form_email_input_" + idval).width());
            }
        } else if (checkbox_name == 'lname') {
            if (document.getElementById("form_lname_li_" + idval) == null) {
                var lname_textfield_value = document.getElementById("adder_lname_textfield_" + idval).value;
                document.getElementById("form_body_" + idval).innerHTML += '<li style="padding-top:13px;" id="form_lname_li_' + idval + '">' +
                    '<div style=" width:110px; float:left; font-size:14px; margin-top:5px;color:#555;padding-right:10px;" align="right" id="form_lname_label_' + idval + '">' + ((lname_textfield_value == '') ? 'Last Name:' : lname_textfield_value) + '</div>' +
                    '<div style="  width:60%; float:left;min-width:150px;"> <input name="LASTNAME" style="font-size:12px; border:solid 1px #dcdcdc; width:100%; height:30px; padding:5px;" autocomplete="off" id="form_lname_input_' + idval + '" type="text" /></div>' +
                    '<div style="clear:both;"></div>' +
                    '</li>';
                document.getElementById("form_lname_label_" + idval).style.fontSize = document.getElementById("form_email_label_" + idval).style.fontSize;
                document.getElementById("form_lname_label_" + idval).style.fontFamily = document.getElementById("form_email_label_" + idval).style.fontFamily;
                document.getElementById("form_lname_label_" + idval).style.color = document.getElementById("form_email_label_" + idval).style.color;
                document.getElementById("form_lname_input_" + idval).style.fontSize = document.getElementById("form_email_input_" + idval).style.fontSize;
                document.getElementById("form_lname_input_" + idval).style.fontFamily = document.getElementById("form_email_input_" + idval).style.fontFamily;
                document.getElementById("form_lname_input_" + idval).style.color = document.getElementById("form_email_input_" + idval).style.color;
                document.getElementById("form_lname_input_" + idval).style.backgroundColor = document.getElementById("form_email_input_" + idval).style.backgroundColor;
                $zc_j("#form_lname_input_" + idval).width($zc_j("#form_email_input_" + idval).width());
            }
        } else if (checkbox_name == 'phone') {
            if (document.getElementById("form_phone_li_" + idval) == null) {
                var phone_textfield_value = document.getElementById("adder_phone_textfield_" + idval).value;
                document.getElementById("form_body_" + idval).innerHTML += '<li style="padding-top:13px;" id="form_phone_li_' + idval + '">' +
                    '<div style=" width:110px; float:left; font-size:14px; margin-top:5px;color:#555;padding-right:10px;" align="right" id="form_phone_label_' + idval + '">' + ((phone_textfield_value == '') ? 'Phone:' : phone_textfield_value) + '</div>' +
                    '<div style="  width:60%; float:left;min-width:150px;"> <input name="PHONE" style="font-size:12px; border:solid 1px #dcdcdc; width:100%; height:30px; padding:5px;" autocomplete="off" id="form_phone_input_' + idval + '" type="text" /></div>' +
                    '<div style="clear:both;"></div>' +
                    '</li>';
                document.getElementById("form_phone_label_" + idval).style.fontSize = document.getElementById("form_email_label_" + idval).style.fontSize;
                document.getElementById("form_phone_label_" + idval).style.fontFamily = document.getElementById("form_email_label_" + idval).style.fontFamily;
                document.getElementById("form_phone_label_" + idval).style.color = document.getElementById("form_email_label_" + idval).style.color;
                document.getElementById("form_phone_input_" + idval).style.fontSize = document.getElementById("form_email_input_" + idval).style.fontSize;
                document.getElementById("form_phone_input_" + idval).style.fontFamily = document.getElementById("form_email_input_" + idval).style.fontFamily;
                document.getElementById("form_phone_input_" + idval).style.color = document.getElementById("form_email_input_" + idval).style.color;
                document.getElementById("form_phone_input_" + idval).style.backgroundColor = document.getElementById("form_email_input_" + idval).style.backgroundColor;
                $zc_j("#form_phone_input_" + idval).width($zc_j("#form_email_input_" + idval).width());
            }
        }
        zc_liElementArranger(idval);
    } else {
        if (checkbox_name == 'fname') {
            if (document.getElementById("form_fname_li_" + idval) != null) {
                document.getElementById("form_body_" + idval).removeChild(document.getElementById("form_fname_li_" + idval));
            }
        } else if (checkbox_name == 'lname') {
            if (document.getElementById("form_lname_li_" + idval) != null) {
                document.getElementById("form_body_" + idval).removeChild(document.getElementById("form_lname_li_" + idval));
            }
        } else if (checkbox_name == 'phone') {
            if (document.getElementById("form_phone_li_" + idval) != null) {
                document.getElementById("form_body_" + idval).removeChild(document.getElementById("form_phone_li_" + idval));
            }
        }
    }
}

function zc_formToggle(idval, form_type) {
	if((zc_processing_signup_form == 0 || zc_processing_signup_form == "0") && form_type.indexOf('simple_form') == -1){
		alert('No Signupform selected.');
		return;
	}
    zc_zcform_temp = jQuery("#zoho_form_viewer_"+idval).html();
    jQuery("#form_type_selector_div").css("display", "none");
    jQuery("#form_all_" + idval).css("display", "block");
    if(idval != -1) {
        zc_processing_list = idval;
    }
    if (form_type.substring(0, 12) == 'simple_form_') {
        jQuery("#simple_form_div_" + idval).css("display", "block");
        jQuery("#zoho_form_div_" + idval).css("display", "none");
        jQuery("#create_content_div").find("img").attr("onClick", jQuery("#cancel_simple_form_changes_" + idval).attr("onClick"));
        //jQuery("#lf_form_name_"+idval).text("Untitled_"+zc_processing_list);
        //jQuery("#lf_form_edit_"+idval).val("Untitled_"+zc_processing_list);
        jQuery('#lf_form_save_'+idval).hide();
        jQuery('#lf_form_cancel_'+idval).hide();
        jQuery('#lf_form_edit_'+idval).hide();
        jQuery('#lf_form_name_'+idval).show();
        jQuery('#lf_edit_delete_panel_'+idval).show();
    } else if (form_type.substring(0, 19) == 'zoho_campaign_form_') {
        jQuery("#simple_form_div_" + idval).css("display", "none");
        jQuery("#zoho_form_div_" + idval).css("display", "block");
        jQuery("#create_content_div_span").text(jQuery("#create_content_div_span").text().replace("Create","Select"));
        
        if (!jQuery("#button_form_" + idval).attr("checked") && !jQuery("#embed__form_" + idval).attr("checked") && !jQuery("#custom_form_" + idval).attr("checked")) {
            zc_radioButtonChecker(idval);
        }
        jQuery("#create_content_div").find("img").attr("onClick", jQuery("#cancel_zoho_form_changes_" + idval).attr("onClick"));
        if(zc_ver == "2")   {
            jQuery("#form_radiobuttons_"+idval).css("display","none");
            if(zc_savedsignupformid == -1)  {
                jQuery("#mini_form_tab_"+idval).addClass("sel");
                jQuery("#big_form_tab_"+idval).removeClass("sel");
            }
        }
        else if(zc_ver == "1")  {
            jQuery("#form_radiobuttons_"+idval).css("display","block");
        }
        if(idval == "-1") {// zc_formRetreiver
            jQuery("#embed__form_-1").click();
        }
        else {
            jQuery("#zoho_form_viewer_"+idval).html("");
            zc_addForm(idval,jQuery("#signupformid_"+idval).val().trim().split("_")[0]);
        }
    }
}
function zc_createNewForm() {
    /*if( (zc_unsaved_idval.length <= 0) )    {
        alert("You've already created sign up forms for all mailing lists present in your account");
        return;
    }*/
    if(typeof(zc_on_load_apikey_val) == "undefined")
    {
        return;
    }
    zc_savedsignupformid = -1;
    if(zc_first_load || zc_saved_idval.length == zc_all_idval.length) {
        jQuery("#new_mailing_button").attr("disabled","disabled");
        jQuery("#new_mailing_button").css("opacity","0.3");
        var unwantedsno = zc_all_idval.join(":");
        if(unwantedsno == "") {
            unwantedsno = "1";
        }
        var userType = jQuery("#choosen_user_type").attr("value");
        var api_url = zc_domain_url + '/api/getmailinglistsprivacy?authtoken=' +  zc_on_load_apikey_val + '&scope=CampaignsAPI&resfmt=JSON&&sort=asc&fromindex=1&range=2&unwantedsno='+unwantedsno+'&usertype='+userType;
        if(jQuery("#unsaved_list").length > 0 && (jQuery("#unsaved_list").html() == "" || jQuery("#unsaved_list").html().indexOf("No mailing list") >=0 )) {
            jQuery("#unsaved_list").append('<div id="temp_loading_div" align="center"><h1>Loading...</h1><img id="laoding_-1" src="' + zc_pluginDir + '/assets/images/uploadImg.gif"  /></div>');
        }
		jQuery("#unsaved_list").html('');
		jQuery("#temp_loading_div").hide();
        jQuery("#morelist").hide();
        jQuery("#proceed_div").hide();
        jQuery.ajax({
            type: 'POST',
            url: api_url,
            success: function (responseData, textStatus, jqXHR) {
                jQuery("#new_mailing_button").removeAttr("disabled");
                jQuery("#new_mailing_button").css("opacity","1");
                if(responseData.code == 2401 && responseData.status == "error")   {
					//jQuery("#unsaved_list").html('<div class=" zcmt15 zctxt zctcenter zcf14" id="no_list_message"><h4>No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.</h4></div>');
					alert('No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.');
					zc_selectUserType('1');
                    return;
                }
                zc_first_load = false;
                zc_from_index += 2;
                var sno,name,unSavedListTemplate,listkey,validlist;
                if((typeof responseData) != "object")
                {
                    responseData = JSON.parse(responseData);
                }
                jQuery("#create_content_div").css("display", "none");
                jQuery("#form_type_selector_div").css("display", "none");
                jQuery("#saved_list_content").css("display", "none");
                jQuery("#unsaved_list").css("display","block");
                //jQuery("#click_list_content").css("display", "block");
                jQuery("#click_list_div").css("display","block");
                jQuery("#proceed_div").css("display","block");
                if (zc_saved_id == -1 || zc_all_idval.length == zc_unsaved_idval.length) {
                    jQuery("#cancel_list_selection").hide();
                }
                else {
                    jQuery("#cancel_list_selection").show();
                }
                jQuery("#morelist").css("display","block");
                for (var i = 0; i < zc_saved_idval.length; i++) {
                    jQuery("#saved_div_" + zc_saved_idval[i]).css("display", "none");
                    jQuery("#list_name_" + zc_saved_idval[i]).css("display", "none");
                    jQuery("#list_name_" + zc_saved_idval[i]).find("img").attr("src",zc_pluginDir + "/assets/images/zc_radiobtnnrml.png");
                }
                for (var i = 0; i < zc_unsaved_idval.length; i++) {
                    jQuery("#list_name_" + zc_unsaved_idval[i]).css("display", "block");
                }
                jQuery("#unsaved_list").html('');
                if(responseData.list_of_details != null) {
                    jQuery("#form_radiobuttons_"+zc_processing_list).css("display","none");
                    jQuery.each(responseData.list_of_details,function(index,val){
                        sno = val.s_no;
                        validlist = val.validlist;
                        if( (zc_all_idval.indexOf(sno) < 0) && validlist == "true") {
                            name = val.listname;
                            listkey = val.listkey;
                            zc_unsaved_idval[zc_unsaved_idval.length] = sno;
                            zc_all_idval[zc_all_idval.length] = sno;
                            unSavedListTemplate = "<div class=\"zcmlist\" id=\"list_name_" + sno + "\" onclick=\"javascript:zc_setSelection('" + sno + "','" + name + "');\"><img src=\"" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png\" style=\"vertical-align:middle;\" alt=\"O\"> &nbsp; <span class=\"zclstnmeblk\" style=\"vertical-align:middle;cursor:default;\">" + name + "</span><div style=\"display:none;\" id=\"list_key_" + sno + "\">" + listkey + "</div></div>";
                            jQuery("#unsaved_list").append(unSavedListTemplate);
                        }
                    });
                }
                if(jQuery("#unsaved_list").html() == "") {
                    jQuery("#new_mailing_button").removeAttr("disabled");
                    jQuery("#new_mailing_button").css("opacity","1");
                    jQuery("#unsaved_list").html('<div class=" zcmt15 zctxt zctcenter zcf14" id="no_list_message"><h4>No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.</h4></div>');
                    // alert("No Mailing list available from Zoho-Campaigns to show. Either create new Mailing List or check for the privilege to access it.");
                }
            },
            error: function (responseData, textStatus, errorThrown) {
                if(jQuery("#unsaved_list").html() == "" || jQuery("#unsaved_list").html().indexOf("No mailing list") >=0 ) {
                    jQuery("#unsaved_list").html('<div class=" zcmt15 zctxt zctcenter zcf14" id="no_list_message"><h4>No mailing list found. Please add a mailing list in your Zoho Campaigns account before proceeding with sign-up form.</h4></div>');
                    // alert("No Mailing list available from Zoho-Campaigns to show. Either create new Mailing List or check for the privilege to access it.");
                }
            }
        });
    }
    else {
        jQuery("#create_content_div").css("display", "none");
        jQuery("#form_type_selector_div").css("display", "none");
        jQuery("#saved_list_content").css("display", "none");
        jQuery("#unsaved_list").css("display","block");
        //jQuery("#click_list_content").css("display", "block");
        jQuery("#click_list_div").css("display","block");
        jQuery("#proceed_div").css("display","block");
        jQuery("#morelist").css("display","block");
        for (var i = 0; i < zc_saved_idval.length; i++) {
            jQuery("#saved_div_" + zc_saved_idval[i]).css("display", "none");
            jQuery("#list_name_" + zc_saved_idval[i]).css("display", "none");
            jQuery("#list_name_" + zc_saved_idval[i]).find("img").attr("src",zc_pluginDir + "/assets/images/zc_radiobtnnrml.png");
        }
        for (var i = 0; i < zc_unsaved_idval.length; i++) {
            jQuery("#list_name_" + zc_unsaved_idval[i]).css("display", "block");
        }
    }   
}

function zc_discardZohoFormChanges(idval, type) {
    jQuery("#create_content_div_span").text(jQuery("#create_content_div_span").text().replace("Select","Create"));
    jQuery("#zcform_button_"+idval).find("img").css("display","none");
    if (zc_selection == 'custom_form') {
        jQuery("#custom_form_" + idval).attr("checked","checked");
    } else if (zc_selection == 'button_form') {
        jQuery("#button_form_" + idval).attr("checked","checked");
    } else {
        jQuery("#embed__form_" + idval).attr("checked","checked");
    }
    jQuery("#zoho_form_viewer_"+idval).html(zc_zcform_temp);
    jQuery("#form_all_" + idval).css("display", "none");
    jQuery("#zcsignupformmsg_"+idval).css("display","none");
    jQuery("#zcformcontainer_"+idval).css("display","block");
    jQuery("#zcform_save_button_"+idval).css("display","block");
    if (type == 'create') {
		zc_processing_signup_form = 0;
        jQuery("#form_type_selector_div").css("display", "block");
        jQuery("#create_content_div").find("img").attr("onClick", "zc_back('2','" + idval + "');");
        jQuery("#simple_form_shower").attr("onClick", "zc_formToggle('-1','simple_form_-1');zc_designshower('-1','0');");
        jQuery("#zoho_form_shower").attr("onClick", "zc_formToggle('" + idval + "','zoho_campaign_form_" + idval + "');");
        zc_loadSignupFormList();
    } else if (type == 'edit') {
        zc_savedsignupformid = -1;
        jQuery("#create_content_div").css('display', 'none');
        jQuery("#form_all_" + idval).css("display", "none");
        jQuery("#saved_list_content").css("display", "block");
        for (var i = 0; i < zc_all_idval.length; i++) {
            jQuery("#saved_div_" + zc_all_idval[i]).css("display", "block");
        }
        for (var i = 0; i <= zc_unsaved_idval.length; i++) {
            jQuery("#saved_div_" + zc_unsaved_idval[i]).css("display", "none");
        }
    }
    return false;
}

function zc_back(option, idval) {
    if(zc_goto_savedlist) {
        jQuery("#form_type_selector_div").hide();
        jQuery("#create_content_div").hide();
        zc_savedsignupformid = -1;
        jQuery("#create_content_div").css('display', 'none');
        jQuery("#form_all_" + idval).css("display", "none");
        jQuery("#saved_list_content").css("display", "block");
        for (var i = 0; i < zc_all_idval.length; i++) {
            jQuery("#saved_div_" + zc_all_idval[i]).css("display", "block");
        }
        for (var i = 0; i <= zc_unsaved_idval.length; i++) {
            jQuery("#saved_div_" + zc_unsaved_idval[i]).css("display", "none");
        }        
    }
    else if (option == '1') {
        zc_savedsignupformid = -1;
        jQuery("#unsaved_list").css("display","none");
        //jQuery("#click_list_content").css("display", "none");
        jQuery("#saved_list_content").css("display", "block");
        jQuery("#proceed_div").css("display","none");
        jQuery("#morelist").css("display","none");
        jQuery("#click_list_div").css("display","none");
        for (var i = 0; i <= zc_unsaved_idval.length; i++) {
            jQuery("#list_name_" + zc_unsaved_idval[i]).css("display", "none");
        }
        for (var i = 0; i < zc_saved_idval.length; i++) {
            jQuery("#saved_div_" + zc_saved_idval[i]).css("display", "block");
        }
    } else if (option == '2') {
        zc_max_signup_form = 0;
        jQuery("#create_content_div").css("display", "none");
        jQuery("#form_type_selector_div").css("display", "none");
        //jQuery("#click_list_content").css("display", "block");
        jQuery("#proceed_div").css("display","block");
        jQuery("#morelist").css("display","block");
        jQuery("#click_list_div").css("display","block");
        for (var i = 0; i < zc_all_idval.length; i++) {
            jQuery("#list_name_" + zc_all_idval[i]).css("display", "none");
        }
        for (var i = 0; i <= zc_unsaved_idval.length; i++) {
            jQuery("#list_name_" + zc_unsaved_idval[i]).css("display", "block");
        }
    } else if (option == '3') {
        jQuery("#form_type_selector_div").css("display", "block");
        jQuery("#form_all_" + idval).css("display", "none");
        jQuery("#create_content_div").find("img").attr("onClick", "zc_back('2','" + idval + "');");
        jQuery("#simple_form_shower").attr("onClick", "zc_formToggle('-1','simple_form_-1');zc_designshower('-1','0');");
        jQuery("#zoho_form_shower").attr("onClick", "zc_formToggle('-1','zoho_campaign_form_-1');");
    }
}
function zc_setSelection(idval,name)   {
    for(var i = 0; i < zc_unsaved_idval.length ; i++) {
        jQuery("#list_name_"+zc_unsaved_idval[i]).find("img").attr("src",zc_pluginDir + "/assets/images/zc_radiobtnnrml.png");
    }
    jQuery("#list_name_"+idval).find("img").attr("src",zc_pluginDir + "/assets/images/zc_radiobtnchkd.png");
}
function zc_callI(type,processing_list_id) {
    var idval = -1;
    if(processing_list_id != -1) {
        zc_processing_list = processing_list_id;
        idval = processing_list_id;
    }
    if(type == 'create') {
        jQuery("#zc_form_text").text("Forms available in your Zoho Campaigns account");
        if(idval == -1) {
            var name = '';
            for (var i = 0; i < zc_unsaved_idval.length; i++) {
                var url = jQuery("#list_name_"+zc_unsaved_idval[i]).find("img").attr("src");
                if(url.indexOf("zc_radiobtnchkd") >= 0) {
                    idval = zc_unsaved_idval[i];
                    name = jQuery("#list_name_"+zc_unsaved_idval[i]).find("span").text().trim();
                }
            }
            zc_processing_list = idval;
        }
        if(idval == -1) {
            alert("No List selected.");
            return;
        }
        for (var i = 0; i < zc_unsaved_idval.length; i++) {
            jQuery("#list_name_" + zc_unsaved_idval[i]).css("display", "none");
        }
        //jQuery("#click_list_content").css("display", "none");
        jQuery("#proceed_div").css("display","none");
        jQuery("#morelist").css("display","none");
        jQuery("#click_list_div").css("display","none");
        jQuery("#zc_form").css("width","48%");
        jQuery("#local_form").show();
        jQuery("#form_seperator").show();
        jQuery("#sf_create_help_link").show();
        zc_goto_savedlist = false;
    }
    else {
        jQuery("#local_form").hide();
        jQuery("#form_seperator").hide();
        jQuery("#sf_create_help_link").hide();
        jQuery("#zc_form").css("width","100%");
        zc_goto_savedlist = true;
    }
    jQuery("#create_content_div").find("img").css("display","inline-block");
    jQuery("#create_content_div").find("img").attr("onClick","zc_back('2','" + idval + "');")
    jQuery("#create_content_div").css("display", "block");
    jQuery("#form_type_selector_div").css("display", "block");
    jQuery("#simple_form_shower").attr("onClick", "zc_formToggle('-1','simple_form_-1');zc_designshower('-1','0');");
        jQuery("#zoho_form_shower").attr("onClick", "zc_formToggle('" + processing_list_id + "','zoho_campaign_form_" + processing_list_id + "');");
    if(processing_list_id == -1) {
        jQuery("#create_content_div_span").text("Create sign-up form for " + name);
    }
    zc_loadSignupFormList();
}
function zc_loadSignupFormList() {
    var listkey = jQuery("#list_key_"+zc_processing_list).text();
    if("" == listkey.trim())    {
        listkey = jQuery("#list_key_"+zc_processing_list).val();
    }
    var form_url = zc_domain_url + "/api/xml/getallforms?authtoken=" + zc_on_load_apikey_val + "&scope=CampaignsAPI&version=1&resfmt=JSON&listkey=" + listkey;
    jQuery("#zoho_form_shower").show();
    jQuery("#all_zc_signup_forms").attr("align","center");
    jQuery("#all_zc_signup_forms").html('<img style="vertical-align: middle;margin-top: 40px;"" id="laoding_' + zc_processing_list + '" src="' + zc_pluginDir + '/assets/images/uploadImg.gif"  /><span style="font-size:20px;display:block;margin-top:10px;">Loading...</span>');
    jQuery.ajax({
        type: 'POST',
        url: form_url,
        success: function (responseData, textStatus, jqXHR) {
            if(responseData.list_of_details != null) {
                var count = 0;
                jQuery("#all_zc_signup_forms").css("padding-top","");
                jQuery("#all_zc_signup_forms").html('');
                jQuery("#all_zc_signup_forms").removeAttr("align");
                jQuery.each(responseData.list_of_details,function(index,val){
                    count++;
                    var isdefault = val.isdefault;
                    var signupformid = val.signupformid;
                    var signupformname = val.signupformname;
                    var unSavedListTemplate;
                    if(isdefault == "true")
                    {
                        unSavedListTemplate = "<div id=\"signup_form_name_" + count + "\" onclick=\"javascript:zc_selectSignUpForm('" + count + "','" + signupformname + "');\"><img src=\"" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png\" style=\"vertical-align:middle;\" alt=\"O\"> &nbsp; <span class=\"zclstnmeblk\" style=\"vertical-align:middle;cursor:default;\"><span name=\"signupformname\">" + signupformname + "</span><span class=\"zcdefault zcml10\">(Default)</span>" + "</span><div style=\"display:none;\" id=\"signup_form_key_" + count + "\">" + signupformid + "</div></div>";
                    }
                    else
                    {
                        unSavedListTemplate = "<div id=\"signup_form_name_" + count + "\" onclick=\"javascript:zc_selectSignUpForm('" + count + "','" + signupformname + "');\"><img src=\"" + zc_pluginDir + "/assets/images/zc_radiobtnnrml.png\" style=\"vertical-align:middle;\" alt=\"O\"> &nbsp; <span class=\"zclstnmeblk\" style=\"vertical-align:middle;cursor:default;\"><span name=\"signupformname\">" + signupformname + "</span></span><div style=\"display:none;\" id=\"signup_form_key_" + count + "\">" + signupformid + "</div></div>";
                    }
                    jQuery("#all_zc_signup_forms").append(unSavedListTemplate);
                });
                zc_max_signup_form = count;
            }
            else {
                jQuery("#all_zc_signup_forms").css("padding-top","10px");
                jQuery("#all_zc_signup_forms").html("You don’t have a Sign-up form for this mailing list. Create one!");
                jQuery("#zoho_form_shower").hide();
            }
            return false;
        },
        error: function (responseData, textStatus, errorThrown) {
            alert("Exception while getting sign-up form related details");
            return false;
        }
    });    
}
function zc_selectSignUpForm(signupformIdval,signupformname) {
    for(var temp_form_count = 1; temp_form_count <= zc_max_signup_form ; temp_form_count++) {
        jQuery("#signup_form_name_"+temp_form_count).find("img").attr("src",zc_pluginDir + "/assets/images/zc_radiobtnnrml.png");
    }
    jQuery("#signup_form_name_"+signupformIdval).find("img").attr("src",zc_pluginDir + "/assets/images/zc_radiobtnchkd.png");
    zc_processing_signup_form = signupformIdval;
}

function zc_styleSelect(me) {
    var idval = me.id.substring(13);
    document.getElementById("title_div_" + idval).style.display = 'none';
    document.getElementById("label_div_" + idval).style.display = 'none';
    document.getElementById("textfield_div_" + idval).style.display = 'none';
    document.getElementById("button_div_" + idval).style.display = 'none';
    document.getElementById("form_div_" + idval).style.display = 'none';
    var myindex = me.selectedIndex;
    if (myindex == 1) {
        var selectedId = 'title_div_';
    } else if (myindex == 2) {
        var selectedId = 'label_div_';
    } else if (myindex == 3) {
        var selectedId = 'textfield_div_';
    } else if (myindex == 4) {
        var selectedId = 'button_div_';
    } else if (myindex == 0) {
        var selectedId = 'form_div_';
    }
    document.getElementById(selectedId + "" + idval).style.display = 'block';
}

function zc_fontSelect(me) {
    var idval = me.id.substring(11);
    var identifier = me.id.substring(0, 5);
    if (me.selectedIndex == 5) {
        document.getElementById(identifier + "_input_tr_" + idval).removeAttribute('style');
    } else {
        document.getElementById(identifier + "_input_tr_" + idval).style.display = 'none';
        document.getElementById(identifier + "_input_tr_" + idval).value = '';

        if (identifier == 'title') {
            document.getElementById("form_title_" + idval).style.fontFamily = document.getElementById(identifier + "_font_" + idval).value;
        } else if (identifier == 'label') {
            zc_setFontFamilyLabel(document.getElementById(identifier + "_font_" + idval).value, idval);
        } else if (identifier == 'field') {
            zc_setFontFamilyField(document.getElementById(identifier + "_font_" + idval).value, idval);
        } else if (identifier == 'buton') {
            document.getElementById("form_button_" + idval).style.fontFamily = document.getElementById(identifier + "_font_" + idval).value;
        }
    }

}

function zc_setColor(me) {
    var myid = me.id;
    var identifier;
    var idval;
    if (myid != '') {
        idval = myid.substring(15);
        identifier = me.id.substring(0, 5);
        zc_div_identifier[idval] = identifier;
        if (me.id.substring(6, 8) == 'bg') {
            zc_div_flag[idval] = 1;
            // jQuery("#"+identifier+"_bgcolor_picker_div_"+idval).css("display","block");
        } else {
            zc_div_flag[idval] = 0;
            // jQuery("#"+identifier+"_color_picker_div_"+idval).css("display","block");
        }
        return;
    } else {
        var parentid = me.parentNode.id;
        // zc_div_identifier[idval] = identifier;
        if (parentid.substring(6, 8) == 'bg') {
            idval = parentid.substring(20);
            identifier = parentid.substring(0, 5);
            // jQuery("#"+identifier+"_bgcolor_picker_div_"+idval).css("display","none");
        } else {
            idval = parentid.substring(18);
            identifier = parentid.substring(0, 5);
            // jQuery("#"+identifier+"_color_picker_div_"+idval).css("display","none");
        }
        if (identifier == 'title') {
            document.getElementById("form_title_" + idval).style.color = me.style.backgroundColor;
            document.getElementById(identifier + "_bgval_" + idval).value = me.style.backgroundColor;
            document.getElementById(identifier + "_colorbox_" + idval).style.backgroundColor = me.style.backgroundColor;
        } else if (identifier == 'label') {
            document.getElementById(identifier + "_bgval_" + idval).value = me.style.backgroundColor;
            document.getElementById(identifier + "_colorbox_" + idval).style.backgroundColor = me.style.backgroundColor;
            if (document.getElementById("form_email_label_" + idval)) {
                document.getElementById("form_email_label_" + idval).style.color = me.style.backgroundColor;
            }
            if (document.getElementById("form_phone_label_" + idval)) {
                document.getElementById("form_phone_label_" + idval).style.color = me.style.backgroundColor;
            }
            if (document.getElementById("form_fname_label_" + idval)) {
                document.getElementById("form_fname_label_" + idval).style.color = me.style.backgroundColor;
            }
            if (document.getElementById("form_lname_label_" + idval)) {
                document.getElementById("form_lname_label_" + idval).style.color = me.style.backgroundColor;
            }
        } else if (identifier == 'field') {
            if (parentid.substring(0, 17) == 'field_colorpicker') {
                document.getElementById(identifier + "_bgval_" + idval).value = me.style.backgroundColor;
                document.getElementById(identifier + "_colorbox_" + idval).style.backgroundColor = me.style.backgroundColor;
                if (document.getElementById("form_email_input_" + idval)) {
                    document.getElementById("form_email_input_" + idval).style.color = me.style.backgroundColor;
                }
                if (document.getElementById("form_phone_input_" + idval)) {
                    document.getElementById("form_phone_input_" + idval).style.color = me.style.backgroundColor;
                }
                if (document.getElementById("form_fname_input_" + idval)) {
                    document.getElementById("form_fname_input_" + idval).style.color = me.style.backgroundColor;
                }
                if (document.getElementById("form_lname_input_" + idval)) {
                    document.getElementById("form_lname_input_" + idval).style.color = me.style.backgroundColor;
                }
            } else if (parentid.substring(0, 19) == 'field_bgcolorpicker') {
                document.getElementById(identifier + "_bgbgval_" + idval).value = me.style.backgroundColor;
                document.getElementById(identifier + "_bgcolbox_" + idval).style.backgroundColor = me.style.backgroundColor;
                if (document.getElementById("form_email_input_" + idval)) {
                    document.getElementById("form_email_input_" + idval).style.backgroundColor = me.style.backgroundColor;
                }
                if (document.getElementById("form_phone_input_" + idval)) {
                    document.getElementById("form_phone_input_" + idval).style.backgroundColor = me.style.backgroundColor;
                }
                if (document.getElementById("form_fname_input_" + idval)) {
                    document.getElementById("form_fname_input_" + idval).style.backgroundColor = me.style.backgroundColor;
                }
                if (document.getElementById("form_lname_input_" + idval)) {
                    document.getElementById("form_lname_input_" + idval).style.backgroundColor = me.style.backgroundColor;
                }
            }
        } else if (identifier == 'buton' || identifier == 'bgcol') {
            document.getElementById(identifier + "_bgval_" + idval).value = me.style.backgroundColor;
            document.getElementById(identifier + "_colorbox_" + idval).style.backgroundColor = me.style.backgroundColor;
            if (identifier == 'buton') {
                document.getElementById("form_button_" + idval).style.color = me.style.backgroundColor;
            } else if (identifier == 'bgcol') {
                document.getElementById("form_button_" + idval).style.borderColor = me.style.backgroundColor;
                document.getElementById("form_button_" + idval).style.backgroundColor = me.style.backgroundColor;
            }
        } else if (identifier == 'forbg' || identifier == 'hedbg' || identifier == 'borbg') {
            document.getElementById(identifier + "_bgval_" + idval).value = me.style.backgroundColor;
            document.getElementById(identifier + "_colorbox_" + idval).style.backgroundColor = me.style.backgroundColor;
            if (identifier == 'forbg') {
                document.getElementById("total_form_child_" + idval).style.backgroundColor = me.style.backgroundColor;
                document.getElementById("total_form_" + idval).style.backgroundColor = me.style.backgroundColor;
                document.getElementById("form_body_container_" + idval).style.backgroundColor = me.style.backgroundColor;
            } else if (identifier == 'hedbg') {
                document.getElementById("form_title_" + idval).style.backgroundColor = me.style.backgroundColor;
            } else if (identifier == 'borbg') {
                document.getElementById("total_form_child_" + idval).style.borderColor = me.style.backgroundColor;
                document.getElementById("total_form_" + idval).style.borderColor = me.style.backgroundColor;
                document.getElementById("form_title_" + idval).style.borderBottomColor = me.style.backgroundColor;
            }
        }
    }
}

function zc_toggler(identifier, idval, flag) {
    if (flag == 0) {
        if (jQuery("#" + identifier + "_color_picker_div_" + idval).css('display') == 'none') {
            jQuery("#" + identifier + "_color_picker_div_" + idval).css('display', 'block');
        } else {
            jQuery("#" + identifier + "_color_picker_div_" + idval).css('display', 'none');
        }
    } else {
        if (jQuery("#" + identifier + "_bgcolor_picker_div_" + idval).css('display') == 'none') {
            jQuery("#" + identifier + "_bgcolor_picker_div_" + idval).css('display', 'block');
        } else {
            jQuery("#" + identifier + "_bgcolor_picker_div_" + idval).css('display', 'none');
        }
    }
}

function zc_changeFontFamily(me) {
    var idval = me.id.substring(17);
    zc_checkContenet(me, 2, idval);
    var identifier = me.id.substring(0, 5);
    if (identifier == 'title') {
        document.getElementById("form_title_" + idval).style.fontFamily = me.value;
    } else if (identifier == 'label') {
        zc_setFontFamilyLabel(me.value, idval);
    } else if (identifier == 'field') {
        zc_setFontFamilyField(me.value, idval);
    } else if (identifier == 'buton') {
        document.getElementById("form_button_" + idval).style.fontFamily = me.value;
    }
}

function zc_sizeChange(me) {
    var idval = me.id.substring(17);
    zc_checkContenet(me, 1, idval);
    var identifier = me.id.substring(0, 5);
    if (identifier == 'title') {
        document.getElementById("form_title_" + idval).style.fontSize = me.value + "px";
    } else if (identifier == 'label') {
        if (document.getElementById("form_email_label_" + idval)) {
            document.getElementById("form_email_label_" + idval).style.fontSize = me.value + "px";
        }
        if (document.getElementById("form_phone_label_" + idval)) {
            document.getElementById("form_phone_label_" + idval).style.fontSize = me.value + "px";
        }
        if (document.getElementById("form_fname_label_" + idval)) {
            document.getElementById("form_fname_label_" + idval).style.fontSize = me.value + "px";
        }
        if (document.getElementById("form_lname_label_" + idval)) {
            document.getElementById("form_lname_label_" + idval).style.fontSize = me.value + "px";
        }
    } else if (identifier == 'field') {
        if (document.getElementById("form_email_input_" + idval)) {
            document.getElementById("form_email_input_" + idval).style.fontSize = me.value + "px";
        }
        if (document.getElementById("form_phone_input_" + idval)) {
            document.getElementById("form_phone_input_" + idval).style.fontSize = me.value + "px";
        }
        if (document.getElementById("form_fname_input_" + idval)) {
            document.getElementById("form_fname_input_" + idval).style.fontSize = me.value + "px";
        }
        if (document.getElementById("form_lname_input_" + idval)) {
            document.getElementById("form_lname_input_" + idval).style.fontSize = me.value + "px";
        }
    } else if (identifier == 'buton') {
        document.getElementById("form_button_" + idval).style.fontSize = me.value + "px";
    }
}

function zc_setFontFamilyLabel(passedvalue, idval) {
    if (document.getElementById("form_email_label_" + idval)) {
        document.getElementById("form_email_label_" + idval).style.fontFamily = passedvalue;
    }
    if (document.getElementById("form_phone_label_" + idval)) {
        document.getElementById("form_phone_label_" + idval).style.fontFamily = passedvalue;
    }
    if (document.getElementById("form_fname_label_" + idval)) {
        document.getElementById("form_fname_label_" + idval).style.fontFamily = passedvalue;
    }
    if (document.getElementById("form_lname_label_" + idval)) {
        document.getElementById("form_lname_label_" + idval).style.fontFamily = passedvalue;
    }
}

function zc_setFontFamilyField(passedvalue, idval) {
    if (document.getElementById("form_email_input_" + idval)) {
        document.getElementById("form_email_input_" + idval).style.fontFamily = passedvalue;
    }
    if (document.getElementById("form_phone_input_" + idval)) {
        document.getElementById("form_phone_input_" + idval).style.fontFamily = passedvalue;
    }
    if (document.getElementById("form_fname_input_" + idval)) {
        document.getElementById("form_fname_input_" + idval).style.fontFamily = passedvalue;
    }
    if (document.getElementById("form_lname_input_" + idval)) {
        document.getElementById("form_lname_input_" + idval).style.fontFamily = passedvalue;
    }
}

function zc_fieldsWidth(val, idval) {
    jQuery("#form_email_input_" + idval).css("width", val + "%");
    if (jQuery("#form_fname_input_" + idval).length >= 0) {
        jQuery("#form_fname_input_" + idval).css("width", val + "%");
    }
    if (jQuery("#form_lname_input_" + idval).length >= 0) {
        jQuery("#form_lname_input_" + idval).css("width", val + "%");
    }
    if (jQuery("#form_phone_input_" + idval).length >= 0) {
        jQuery("#form_phone_input_" + idval).css("width", val + "%");
    }
}

function zc_abstractDesignSetter(formObj, idval, identifier) {
    var str = formObj.style.fontSize;
    document.getElementById(identifier + "_size_value_" + idval).value = str.substring(0, str.length - 2);
    document.getElementById(identifier + "_colorbox_" + idval).style.backgroundColor = formObj.style.color;
    document.getElementById(identifier + "_input_tr_" + idval).style.display = 'none';
    if (formObj.style.fontFamily == 'Arial' || formObj.style.fontFamily == '') {
        document.getElementById(identifier + "_font_" + idval).selectedIndex = 0;
    } else if (formObj.style.fontFamily == 'Georgia') {
        document.getElementById(identifier + "_font_" + idval).selectedIndex = 1;
    } else if (formObj.style.fontFamily == 'Times New Roman') {
        document.getElementById(identifier + "_font_" + idval).selectedIndex = 2;
    } else if (formObj.style.fontFamily == 'Verdana') {
        document.getElementById(identifier + "_font_" + idval).selectedIndex = 3;
    } else if (formObj.style.fontFamily == 'Optima') {
        document.getElementById(identifier + "_font_" + idval).selectedIndex = 4;
    } else {
        document.getElementById(identifier + "_input_tr_" + idval).removeAttribute('style');
        document.getElementById(identifier + "_input_tr_" + idval).value = formObj.style.fontFamily;
        document.getElementById(identifier + "_font_" + idval).selectedIndex = 5;
    }
}
function zc_nameSetter(idval) {
    jQuery("#hidden_simple_textarea_-1").attr('name', 'zc4wp_a_'+idval+"[simple_form]");
    jQuery("#hidden_simple_textarea_-1").attr('id', 'hidden_simple_textarea_'+idval);
    jQuery("#hidden_zcform_textarea_-1").attr('name','zc4wp_a_'+idval+"[zoho_form]");
    jQuery("#hidden_zcform_textarea_-1").attr('id','hidden_zcform_textarea_'+idval);
    jQuery("#saved_form_id_-1").attr('name','zc4wp_a_'+idval+"[saved_form_id]");
    jQuery("#saved_form_id_-1").attr('id','saved_form_id_'+idval);
    jQuery("#signupformid_-1").attr('name','zc4wp_a_'+idval+"[signupformid]");
    jQuery("#signupformid_-1").attr('id','signupformid_'+idval);
    jQuery("#lf_form_edit_-1").attr('name','zc4wp_a_'+idval+"[lf_form_name]");
    jQuery("#lf_form_edit_-1").attr('id','lf_form_edit_'+idval);
    jQuery("#zc_form_edit_-1").attr('name','zc4wp_a_'+idval+"[zc_form_name]");
    jQuery("#zc_form_edit_-1").attr('id','zc_form_edit_'+idval);
    jQuery("#success_header_-1").attr('name','zc4wp_a_'+idval+"[success_header]");
    jQuery("#success_header_-1").attr('id','success_header_'+idval);
    jQuery("#success_body_-1").attr('name','zc4wp_a_'+idval+"[success_body]");
    jQuery("#success_body_-1").attr('id','success_body_'+idval);
    jQuery("#error_header_-1").attr('name','zc4wp_a_'+idval+"[error_header]");
    jQuery("#error_header_-1").attr('id','error_header_'+idval);
    jQuery("#genaral_error_body_-1").attr('name','zc4wp_a_'+idval+"[error_body]");
    jQuery("#genaral_error_body_-1").attr('id','genaral_error_body_'+idval);
    jQuery("#exists_email_body_-1").attr('name','zc4wp_a_'+idval+"[exists_email_body]");
    jQuery("#exists_email_body_-1").attr('id','exists_email_body_'+idval);
    jQuery("#simple_button_-1").attr("id","simple_button_"+idval);
    jQuery("#cancel_simple_form_changes_-1").attr("id","cancel_simple_form_changes_"+idval);
    // jQuery("#zcform_button_-1").attr("id","zcform_button_"+idval);
    // jQuery("#cancel_zoho_form_changes_-1").attr("id","cancel_zoho_form_changes_"+idval);
    // jQuery("#total_form_-1").html(jQuery("#total_form_-1").val().replaceAll("-1",idval));

/*    jQuery("#total_form_-1").html(decodeURI(encodeURI(jQuery("#total_form_-1").html()).replace(/-1/g,idval)));
    jQuery("#total_form_-1").attr("id","total_form_"+idval);
    jQuery("#customize_td_-1").html(decodeURI(encodeURI(jQuery("#customize_td_-1").html()).replace(/-1/g,idval)));
    jQuery("#customize_td_-1").attr("id","customize_td_"+idval);
    jQuery("#total_response_-1").html(decodeURI(encodeURI(jQuery("#total_response_-1").html()).replace(/-1/g,idval)));
    jQuery("#total_response_-1").attr("id","total_response_"+idval);*/
    //jQuery("#form_-1").html(decodeURI(encodeURI(jQuery("#form_-1").html()).replace(/-1+/g,idval)));
    var zohoFormInnerHtml = jQuery("#zoho_form_viewer_-1").html();
    var inhtm = document.getElementById("form_-1").innerHTML;
    var htmlTemp = inhtm.replace(/-1/g,idval);
    jQuery("#hidden_div").html('');
    jQuery("#hidden_div").html(htmlTemp);
    jQuery("#hidden_div").find("input[name='option_page']").remove();
    jQuery("#hidden_div").find("input[name='action']").remove();
    jQuery("#hidden_div").find("input[name='_wpnonce']").remove();
    jQuery("#hidden_div").find("input[name='_wp_http_referer']").remove();
    htmlTemp = jQuery("#hidden_div").html();
    jQuery("#hidden_div").html('');
    jQuery("#zoho_form_viewer_-1").attr("id","zoho_form_viewer_"+idval);
    jQuery("#zoho_form_div_-1").attr("id","zoho_form_div_"+idval);
    jQuery("#form_-1").attr("id","form_"+idval);
    //htmlTemp = jQuery("[name='option_page']",htmlTemp).remove();
    // jQuery(htmlTemp).find("[name='option_page']").remove();
    //jQuery("#form_-1").html(jQuery("#form_-1").html().replace(/-1/g,idval));
    // jQuery("#form_-1").attr("id","form_"+idval);
    jQuery("#form_"+idval).append(htmlTemp);
    jQuery("#form_-1").html("");
    jQuery("#zoho_form_viewer_"+idval).html(zohoFormInnerHtml);
    jQuery("#zoho_form_div_"+zc_processing_list).css("display","none");
    jQuery("#zoho_form_viewer_"+zc_processing_list).find("[name='SIGNUP_BODY'],[name='SIGNUP_PAGE']").find("[name='zc_trackCode']").remove();
    jQuery("[name='zc4wp_a_" + zc_processing_list + "[lf_form_name]']").val(jQuery("#lf_form_name_"+zc_processing_list).text());
    jQuery("[name='zc4wp_a_" + zc_processing_list + "[zc_form_name]']").val(jQuery("#zc_form_name_"+zc_processing_list).text());
}
function zc_editorContentChecker(me,option,authToken, listkey) {
    var saving_form_id = -1;
    var formType = -1;
    var formname = "ButtonForm";
    var zcform_button;
    var idval = me.id.substring(14);
    /*if(jQuery("#zoho_form_viewer_"+idval).find("[id*='buttonformdiv']").length <= 0)
    {
        alert("No sign-up form available");
        return;
    }*/
    if(jQuery("#big_form_tab_"+zc_processing_list).hasClass('sel')) {
        formname = "CustomForm";
    }
    else if(jQuery("#mini_form_tab_"+zc_processing_list).hasClass('sel'))    {
        formname = "EmbededForm";
    }
    if(zc_ver == '2' && option == '2')   {        
        var srcb;
        var srcf;
        for(var i = 0 ; i < zc_allvisibleformid.length ; i++)   {
            srcb = jQuery("#BFradiobutton_"+i).attr("src");
            srcf = jQuery("#OFradiobutton_"+i).attr("src");
            if(srcb == null || srcf == null)
            {
                continue;
            }
            if(srcb.indexOf("zc_radiobtnchkd") >= 0) {
                saving_form_id = i;
                formType = 0;
                break;
            }
            else if(srcf.indexOf("zc_radiobtnchkd") >= 0)   {
                saving_form_id = i;
                formType = 1;
                break;
            }
        }
        if(saving_form_id == -1)    {
            alert("No Form selected.");
            return;
        }
    }
    if(idval == "-1") {
        var radioButtonType = 0;
        if(jQuery("#embed__form_"+idval).attr("checked") == true)   {
            radioButtonType = 0;
        }
        else if(jQuery("#custom_form_"+idval).attr("checked") == true)   {
            radioButtonType = 1;
        }
        else if(jQuery("#button_form_"+idval).attr("checked") == true)   {
            radioButtonType = 2;
        }
        zc_nameSetter(zc_processing_list,option);
        idval = zc_processing_list;
        if(radioButtonType == 0) {
            jQuery("#embed__form_"+idval).attr("checked","checked");
        }
        else if(radioButtonType == 1) {
            jQuery("#custom_form_"+idval).attr("checked","checked");
        }
        else if(radioButtonType == 2) {
            jQuery("#button_form_"+idval).attr("checked","checked");
        }
    }
    if (option == '0') {
        var confirmation = confirm("Do you want to delete this sign-up form?");
        if(!confirmation)   {
            return;
        }
        var delete_form_type = jQuery(me).attr("formtype");;
        var hidden_simple_textarea = '';
        var hidden_zcform_textarea = '';
        var saved_form_id = '';
        var signupformid = '';
        var zcformname = '';
        var lfformname = '';
        var success_header = '';
        var success_body = '';
        var error_header = '';
        var genaral_error_body = '';
        var exists_email_body = '';
        var trash_container = new Array();
        
        if(delete_form_type == "zoho") {
            hidden_zcform_textarea = jQuery("#hidden_zcform_textarea_" + idval).attr('name');
            signupformid = jQuery("#signupformid_" + idval).attr('name');
            zcformname = jQuery("#zc_form_edit_" + idval).attr('name');
            jQuery("#hidden_zcform_textarea_" + idval).attr('name', '');
            jQuery("#signupformid_" + idval).attr('name', '');
            jQuery("#zc_form_edit_" + idval).attr('name','');
        } else if(delete_form_type == "simple") {
            hidden_simple_textarea = jQuery("#hidden_simple_textarea_" + idval).attr('name');
            lfformname = jQuery("#lf_form_edit_" + idval).attr('name');
            success_header = jQuery("#success_header_" + idval).attr('name');
            success_body = jQuery("#success_body_" + idval).attr('name');
            error_header = jQuery("#error_header_" + idval).attr('name');
            genaral_error_body = jQuery("#genaral_error_body_" + idval).attr('name');
            exists_email_body = jQuery("#exists_email_body_" + idval).attr('name');
            jQuery("#hidden_simple_textarea_" + idval).attr('name', '');
            jQuery("#lf_form_edit_" + idval).attr('name','');
            jQuery("#success_header_" + idval).attr('name', '');
            jQuery("#success_body_" + idval).attr('name', '');
            jQuery("#error_header_" + idval).attr('name', '');
            jQuery("#genaral_error_body_" + idval).attr('name', '');
            jQuery("#exists_email_body_" + idval).attr('name', '');
        }
                
        // saved_form_id = jQuery("#saved_form_id_" + idval).attr('name');
        // jQuery("#saved_form_id_" + idval).attr('name', '');

        /*for(var i = 0; i < zc_saved_idval.length; i++)   {
            trash_container[i] = jQuery("#trash_container_" + zc_saved_idval[i]).html();
            jQuery("#trash_container_" + zc_saved_idval[i]).html('');
        }*/
        var form_url = zc_new_domain_url + "/api/xml/formdetails?authtoken=" + authToken + "&scope=CampaignsAPI&version=1&resfmt=xml&listkey=" + listkey + "&resform=" + formname;
        jQuery.ajax({
            type: 'POST',
            url: form_url,
            success: function (responseData, textStatus, jqXHR) {
                document.getElementById('form_' + idval).submit();
                return true;
            },
            error: function (responseData, textStatus, errorThrown) {
                alert("This list has been removed from your Zoho-Campaigns account or there has been a change in the privilege.");
                if(delete_form_type == "zoho") {
                    jQuery("#hidden_zcform_textarea_" + idval).attr('name', hidden_zcform_textarea);
                    jQuery("#signupformid_" + idval).attr('name', signupformid);
                    jQuery("#zc_form_edit_" + idval).attr('name',zcformname);
                } else if(delete_form_type == "simple") {
                    jQuery("#hidden_simple_textarea_" + idval).attr('name', hidden_simple_textarea);                
                    jQuery("#lf_form_edit_" + idval).attr('name',lfformname);
                    jQuery("#success_header_" + idval).attr('name', success_header);
                    jQuery("#success_body_" + idval).attr('name', success_body);
                    jQuery("#error_header_" + idval).attr('name', error_header);
                    jQuery("#genaral_error_body_" + idval).attr('name', genaral_error_body);
                    jQuery("#exists_email_body_" + idval).attr('name', exists_email_body);
                }
                // jQuery("#saved_form_id_" + idval).attr('name', saved_form_id);
                /*for(var i = 0; i < zc_all_idval.length; i++)   {
                    jQuery("#trash_container_" + zc_all_idval[i]).html(trash_container[i]);
                }*/
                return;
            }
        });
        return;
    }
    if (zc_check_invalid[idval] != null && zc_check_invalid[idval] != '') {
        alert("Invalid input!");
        return false;
    }
    if (option == '1') {
        if (document.getElementById("form_email_input_" + idval) == null) {
            alert("Email field is missing.");
            return false;
        }
        jQuery("#simple_button_" + idval).attr("disabled","disabled");
        jQuery("#cancel_simple_form_changes_"+idval).attr("disabled","disabled");
        jQuery("#simple_button_" + idval).find("img").css("display","inline-block");
        jQuery("#simple_button_" + idval).find("span").html("Saving Changes...&nbsp;");
    } 
    else if(option == '2'){
        zcform_button = jQuery("#zcform_button_-1").attr("onClick");
        jQuery("#zcform_button_-1").removeAttr("onClick");
        jQuery("#zcform_button_-1").attr("disabled","disabled");
        jQuery("#cancel_zoho_form_changes_-1").attr("disabled","disabled");
        jQuery("#zcform_button_-1").find("img").css("display","inline-block");
        jQuery("#zcform_button_-1").find("span").html("Saving Changes...&nbsp;");
    }
    var create_content_div = jQuery("#create_content_div").find("img").attr("onClick");
    jQuery("#create_content_div").find("img").attr("onClick","");
    var form_url = zc_new_domain_url + "/api/xml/formdetails?authtoken=" + authToken + "&scope=CampaignsAPI&version=1&resfmt=xml&listkey=" + listkey + "&resform=" + formname;
    jQuery.ajax({
        type: 'POST',
        url: form_url,
        success: function (responseData, textStatus, jqXHR) {
            if (option == '1') {
                document.getElementById("hidden_simple_textarea_" + idval).value = document.getElementById("total_form_" + idval).innerHTML.trim();
                var zcformTextareaObj = document.getElementById("hidden_zcform_textarea_" + idval);
                zcformTextareaObj.value = decodeURIComponent(zcformTextareaObj.value.trim());
                jQuery("#saved_form_id_" + idval).val((parseInt(zc_saved_val) + 1) + "_s");
            }
            else if(option == '2'){
                if(zc_ver == '1')   {
                    document.getElementById("hidden_zcform_textarea_" + idval).value = document.getElementById("zoho_form_viewer_" + idval).innerHTML.trim();
                }
                else if(zc_ver == '2')  {
                    if(jQuery("#mini_form_tab_"+idval).hasClass('sel')) {
                        jQuery("#signupformid_" + idval).val(jQuery("#signupform_"+saving_form_id).attr("data-signupid")+"_0_"+formType);
                    }
                    else if(jQuery("#big_form_tab_"+idval).hasClass('sel')) {
                        jQuery("#signupformid_" + idval).val(jQuery("#signupform_"+saving_form_id).attr("data-signupid")+"_1_"+formType);
                    }
                    if(formType == '0') {
                        var formValue = document.getElementById("buttonformviewer_" + saving_form_id).innerHTML.trim();
                        formValue = formValue.replace(/\/\/trackSignupEvent/g,"trackSignupEvent");
                        document.getElementById("hidden_zcform_textarea_" + idval).value = formValue;
                    }
                    else if(formType == '1')    {
                        jQuery("#originalformviewer_"+saving_form_id).find("script").remove();
                        jQuery("#originalformviewer_"+saving_form_id).find("style").remove();
                        jQuery("#originalformviewer_"+saving_form_id).find(".hasDatepicker").removeClass("hasDatepicker");
						iconForDate = jQuery("#originalformviewer_"+saving_form_id).find(".calicon");
                        jQuery("#originalformviewer_"+saving_form_id).find(".calicon").remove();
                        jQuery("#originalformviewer_"+saving_form_id).find("#refImage").attr("onload","referenceSetter(this)");
                        var link = zc_new_domain_url;
                        var trackingText = "wordpress";
                        var jsCode ="<link href=\""+link+"/css/ui.theme.css\" rel=\"stylesheet\" type=\"text/css\" />\n";//No I18N
                        jsCode +="<link href=\""+link+"/css/ui.datepicker.css\" rel=\"stylesheet\" type=\"text/css\" />\n";//No I18N
                        jsCode +="<link href=\""+link+"/css/ui.core.css\" rel=\"stylesheet\" type=\"text/css\" />\n";//No I18N
                        //jsCode +="<script type=\"text/javascript\" src=\""+link+"/js/jquery-1.11.0.min.js\"><\/script>\n";//No I18N
                        //jsCode +="<script type='text/javascript' src='"+link+"/js/jquery-migrate-1.2.1.min.js'><\/script>\n";//No I18N
                        //jsCode +="<script type=\"text/javascript\" src='"+link+"/js/ui.datepicker.js'  charset=\"utf-8\"><\/script>\n";//No I18N
                        jsCode +="<script type=\"text/javascript\" src=\""+link+"/js/jquery.form.js\"><\/script>\n";//No I18N
                        jsCode +="<script type=\"text/javascript\" src=\""+link+"/js/optin_min.js\"><\/script>\n";//No I18N
			jsCode +="<script src='https://www.google.com/recaptcha/api.js?onload=loadreCaptcha&render=explicit' async defer ><\/script>\n";
                        jsCode +="<script type=\"text/javascript\">\n";//No I18N
                        jsCode +="var $ZC = jQuery.noConflict();\n";//No I18N
                        jsCode +="var trackingText=\'"+trackingText+"\';\n";//No I18N
			jsCode +="var recapTheme =  jQuery(\"#recapThemeOptin\").val();var zcReTheme ='light';if(recapTheme=='1' || recapTheme == 1){zcReTheme='dark';}var loadreCaptcha = function()\n{if($(\"[id='recapDiv']\").length>1){\n var a = 1; $(\"[id='recapDiv']\").each(function(){\n$(this).attr(\"id\",\"recapDiv_\"+a);\n widgetId1 = grecaptcha.render('recapDiv_'+a,{\n 'sitekey' : '6LdNeDUUAAAAABpwRBYbCMJvQoxLi4d31Oho0EBw',\n'theme' : zcReTheme,\n});a++;});}else\n {widgetId1 = grecaptcha.render('recapDiv',{\n 'sitekey' : '6LdNeDUUAAAAABpwRBYbCMJvQoxLi4d31Oho0EBw',\n'theme' : zcReTheme,\n});\n}\n};";
                        jsCode +="$ZC(document).ready( function($) {\n";//No I18N
                        jsCode +="$ZC(\"#zc_trackCode\").val(trackingText);\n";//No I18N
                        jsCode +="\t$ZC(\"#fieldBorder\").val($ZC(\"[changeItem='SIGNUP_FORM_FIELD']\").css(\"border-color\"));\n";//No I18N
                        jsCode +="\t_setOptin(false,function(th){\n";//No I18N
                        jsCode +="\t/*Before submit, if you want to trigger your event, \"include your code here\"*/\n";//No I18N
                        jsCode +="});\n\n";//No I18N
                        jsCode +="/*Tracking Enabled*/ \n trackSignupEvent(trackingText);\n ";//No I18N
						jsCode += "if(jQuery('#zcampaignOptinForm').find('.dateClass').length > 0){jQuery('#zcampaignOptinForm').find('#captchaDiv').css('height','100px');}";
						jsCode += "jQuery('#zcampaignOptinForm').find('.dateClass').closest('div').html($('.dateClass').parent().children());";
						jsCode += "jQuery('#zcampaignOptinForm').find('.dateClass').after('<img class=\"ui-datepicker-trigger\" src=\""+ link +"/images/spacer.gif\" alt=\"...\" title=\"...\" style=\"z-index: 1; background-position: 0px -153px; width: 16px; height: 18px; vertical-align: middle; background-image: url(&quot;"+ link +"/images/icons.png&quot;); border: 0px; position: relative; float: right; top: -22px; right: 15px;\">');";
						jsCode += "if(jQuery.datepicker !== undefined && jQuery.datepicker !=='undefined'){jQuery('.dateClass').datepicker();}";
                        jsCode +="});\n";//No I18N
                        jsCode +="<\/script>\n";//No I18N
			var capDivTemp = jQuery("#recapDiv").html();
			jQuery("#originalformviewer_"+saving_form_id).find("#recapDiv").html("");
                        var formValue = document.getElementById("originalformviewer_" + saving_form_id).innerHTML.trim();
			jQuery("#originalformviewer_"+saving_form_id).find("#recapDiv").html(capDivTemp);
                        formValue = formValue.replace(/\/\/trackSignupEvent/g,"trackSignupEvent");
                        document.getElementById("hidden_zcform_textarea_" + idval).value = formValue + jsCode;
                    }
                }
                jQuery("#saved_form_id_" + idval).val((parseInt(zc_saved_val) + 1) + "_z");                
            }
            jQuery("[name='option_page'][value='zoho_settings_form_-1']").closest("#form_"+idval).remove();
            jQuery("#form_" + idval).submit();
            return true;
        },
        error: function (responseData, textStatus, errorThrown) {
            alert("This list has been removed from your Zoho-Campaigns account or there has been a change in the privilege.");
            if (option == '1') {
                document.getElementById("hidden_simple_textarea_" + idval).value = document.getElementById("total_form_" + idval).innerHTML.trim();
                var zcformTextareaObj = document.getElementById("hidden_zcform_textarea_" + idval);
                zcformTextareaObj.value = decodeURIComponent(zcformTextareaObj.value.trim());

                jQuery("#saved_form_id_" + idval).val((parseInt(zc_saved_val) + 1) + "_s");
                jQuery("#simple_button_" + idval).removeAttr("disabled");
                jQuery("#cancel_simple_form_changes_"+idval).removeAttr("disabled");
                jQuery("#simple_button_" + idval).find("img").css("display","none");
                jQuery("#simple_button_" + idval).find("span").html("Saving Changes");
            } 
            else if(option == '2'){
                document.getElementById("hidden_zcform_textarea_" + idval).value = document.getElementById("zoho_form_viewer_" + idval).innerHTML.trim();
                jQuery("#saved_form_id_" + idval).val((parseInt(zc_saved_val) + 1) + "_z");
                jQuery("#zcform_button_-1").removeAttr("disabled");
                jQuery("#zcform_button_-1").attr("onClick",zcform_button);
                jQuery("#cancel_zoho_form_changes_-1").removeAttr("disabled");
                jQuery("#zcform_button_-1").find("img").css("display","none");
                jQuery("#zcform_button_-1").find("span").html("Saving Changes");
            }
            jQuery("#create_content_div").find("img").attr("onClick",create_content_div);
            return;
        }
    });
    return true;
}
function zc_onFormDiv(me) {
    if(jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").is(":visible")) {
        jQuery(me).find("[id*='_edit_delete_panel_']").css("visibility","visible");
    }
}
function zc_outFormDiv(me) {
    if(jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").is(":visible")) {
        jQuery(me).find("[id*='_edit_delete_panel_']").css("visibility","hidden");
    }
}
function zc_editFormName(me) {
    jQuery(me).hide();
	var form_name = jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").text();
	jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_edit_']").val(trim(form_name));
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").hide();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_edit_']").show();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_save_']").show();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_cancel_']").show();
}
function zc_saved_editFormName(me) {
    var form_name = jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_edit_']").val();
    if(form_name == '') {
        alert("Form name is empty!");
        return;
    }
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").text(form_name);
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_save_']").hide();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_cancel_']").hide();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_edit_']").hide();

    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").show();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_edit_delete_panel_']").show();
}
function zc_canceled_editFormName(me) {
    var form_name = jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").text();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_edit_']").val(form_name);
    
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_save_']").hide();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_cancel_']").hide();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_edit_']").hide();

    jQuery(me).closest("[id*='_form_div_']").find("[id*='_form_name_']").show();
    jQuery(me).closest("[id*='_form_div_']").find("[id*='_edit_delete_panel_']").show();
}
