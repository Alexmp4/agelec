var zc_domain_url = "https://campaigns.zoho.com";
jQuery(document).ready(function () {
    window.history.pushState(null,null,window.location.href.replace("settings-updated=true",""));
       var data = {
            'action': 'zc_get_domain'
        };
        jQuery.post(ajaxurl, data, function(response) {
           jQuery("#zc_domain_url").attr("value",response);
            zc_domain_url = response;
       
	if(typeof(zc_on_load_api_val) == "undefined")
	{
		return;
	}
    if (jQuery("#zc_api_key").val() != '') {
        jQuery("#zc_api_key").attr("disabled", "disabled");
        jQuery("#zc_api_key_error").html("");
        jQuery("#ageIndicator").val("old");
        jQuery("#succes_integration_message").css("color", "#23ae44");
        jQuery("#help_link").text("Learn how to get the API key.");
        jQuery("#help_link").css("display", "none");
        jQuery("#cancel_account_changes").css("display", "none");
        jQuery("#next_page_span").css("display","block");
        jQuery("#welcome_div").css("display","none");
        jQuery("#details_div").css("display","block");
        jQuery("#api_key_details").text(zc_on_load_api_val);
        jQuery("#email_span").text(jQuery("#emailId").val());
        jQuery("#orgName_span").text(jQuery("#orgName").val());
        jQuery("#intergrated_date_span").text(jQuery("#integratedDate").val());
        if(jQuery("#active").val() == '1')  {
            jQuery("#active_span").text("Active");
        }
        else {
            jQuery("#active_span").text("Inactive");
        }
    }
    else {
        jQuery("#welcome_div").css("display","block");
        jQuery("#cancel_account_changes").attr("onclick","zc_cancelIntegration()");
        jQuery("#cancel_account_changes").css("display","inline-block");
    }
    if(zc_on_load_api_val != '')  {
        var api_url = zc_domain_url + '/api/checkuserispresentincampaign?authtoken=' +  zc_on_load_api_val + '&scope=CampaignsAPI&resfmt=XML';
        jQuery.ajax({
            type: 'POST',
            url: api_url,
            success: function (responseData, textStatus, jqXHR) {
                var accountStatus = jQuery("is_user_present", responseData).text();
                accountStatus = (accountStatus == "true" )?1:0;
                if(accountStatus != jQuery("#active").val() )   {
                    jQuery("#active").val(accountStatus);
                    jQuery("#zc_api_key").removeAttr("disabled");
                    jQuery("#saving_success_message").css("display","block");
                    jQuery("#saving_success_message").css({"color":"red"});
                    jQuery("#saving_success_message").html("Your account status has been changed in Zoho Campaigns.We are updating your profile..");
                    jQuery("#saving_success_message").css("display","block");
                    jQuery("#api_key_form").submit();
                }
            },
            error: function (responseData, textStatus, errorThrown) {

            }
        });
    }
     });
});
function zc_changeAPIKey() {
    jQuery("#saving_success_message").css("display","none");
    jQuery("#details_div").css("display","none");
    jQuery("#api_key_div").css("display","block");
    jQuery("#zc_api_key_error").text("");
    jQuery("#succes_integration_message").css("color", "#23ae44");
    jQuery("#help_link").text("Learn how to get the API key.");
    zc_accountVerfication('0');
}
function zc_cancelIntegration() {
    jQuery("#api_key_div").css("display","none");
    jQuery("#welcome_div").css("display","block");
}
function zc_successMessage()    {
    jQuery("#saving_success_message").css("display","block");
    jQuery("#proceed_button_div").css("display","block");
}
function zc_startIntegration() {
    jQuery("#welcome_div").css("display","none");
    jQuery("#api_key_div").css("display","block");
}
function zc_cancelChanges() {
    jQuery("#zc_api_key_error").text("");
    jQuery("#zc_api_key_error").css("color", "#23ae44");
    jQuery("#zc_api_key").val(zc_on_load_api_val);
    jQuery("#save_account").removeAttr("disabled");
    jQuery("#cancel_account_changes").removeAttr("disabled");
    jQuery("#zc_api_key").css("border", "1px solid #DDDDDD");
    jQuery("#api_key_div").css("display","none");
    jQuery("#details_div").css("display","block");
}

function zc_apiKeyValidator() {
    var apikeyvalue = jQuery("#zc_api_key").val().trim();
    if (apikeyvalue == '') {
        jQuery("#zc_api_key").css({
            "border": "1px solid #DDDDDD"
        });
        jQuery("#zc_api_key_error").text("");
        jQuery("#zc_api_key_error").css({
            "color": "black"
        });
        jQuery("#save_account").attr("disabled", "disabled");
        jQuery("#cancel_account_changes").attr("disabled","disabled");
        return;
    }
    if (apikeyvalue) {
        var str = new RegExp("^[A-Za-z0-9-]+$");
        if (str.test(apikeyvalue)) {
            jQuery("#zc_api_key").css({
                "border": "1px solid #DDDDDD"
            });
            jQuery("#zc_api_key_error").text("");
            jQuery("#zc_api_key_error").css({
                "color": "black"
            });
            jQuery("#save_account").removeAttr("disabled");
            jQuery("#cancel_account_changes").removeAttr("disabled");
            return;
        } else {
            jQuery("#zc_api_key").css({
                "border": "2px solid #e03131"
            });
            jQuery("#zc_api_key_error").text("Invalid e-mail");
            jQuery("#zc_api_key_error").css({
                "color": "#e03131",
                "font-size": "100%"
            });
            jQuery("#save_account").attr("disabled", "disabled");
            jQuery("#cancel_account_changes").attr("disabled","disabled");
            return;
        }
    }
}
function zc_emailIdValidator()  {
    var apikeyvalue = jQuery("#zc_emailId").val().trim();
    if (apikeyvalue == '') {
        jQuery("#zc_emailId").css({
            "border": "1px solid #DDDDDD"
        });
        jQuery("#zc_email_error").text("");
        jQuery("#zc_email_error").css({
            "color": "black"
        });
        jQuery("#save_account").attr("disabled", "disabled");
        jQuery("#cancel_account_changes").attr("disabled","disabled");
        return;
    }
    if (apikeyvalue) {
	var str=new RegExp(/^[a-zA-Z0-9\_\-\'\.\+]+\@[a-zA-Z0-9\-\_]+(?:\.[a-zA-Z0-9\-\_]+){0,3}\.(?:[a-zA-Z0-9\-\_]{2,15})$/);
        if (str.test(apikeyvalue)) {
            jQuery("#zc_emailId").css({
                "border": "1px solid #DDDDDD"
            });
            jQuery("#zc_email_error").text("");
            jQuery("#zc_email_error").css({
                "color": "black"
            });
            jQuery("#save_account").removeAttr("disabled");
            jQuery("#cancel_account_changes").removeAttr("disabled");
            return;
        } else {
            jQuery("#zc_emailId").css({
                "border": "2px solid #e03131"
            });
            jQuery("#zc_email_error").text("Invalid e-mail");
            jQuery("#zc_email_error").css({
                "color": "#e03131",
                "font-size": "100%"
            });
            jQuery("#save_account").attr("disabled", "disabled");
            jQuery("#cancel_account_changes").attr("disabled", "disabled");
            return;
        }
    }    
}
function zc_fieldFocused(option)   {
    if(option == '0')   {
        jQuery("#zc_api_key_error").html("");
        jQuery("#zc_api_key").css({"border": "1px solid #DDDDDD"});
    }
    else if(option == '1')  {
        jQuery("#zc_email_error").html("");
        jQuery("#zc_emailId").css({"border": "1px solid #DDDDDD"});
    }
}
function zc_accountVerfication(identifier) {
    jQuery("#zc_api_key").val(jQuery("#zc_api_key").val().trim());
    var api_key_val = jQuery("#zc_api_key").val();
    jQuery("#zc_api_key_error").css("color","");
    jQuery("#zc_email_error").css("color","");
    jQuery("#cancel_account_changes").focus();
    if (identifier == '0') {
        jQuery("#zc_api_key").removeAttr("disabled");
        jQuery("#zc_api_key_error").text("");
        jQuery("#zc_emailId").select();
        jQuery("#help_link").css("display", "block");
        jQuery("#next_page_span").css("display","none");
        jQuery("#cancel_account_changes").css("display", "inline-block");
        return;
    }
    jQuery("#zc_api_key").attr("readonly", "readonly");
    jQuery("#zc_emailId").attr("readonly", "readonly");
    jQuery("#save_account").attr("disabled", "disabled");
    jQuery("#cancel_account_changes").attr("disabled","disabled");
    var api_url = 'https://campaigns.zoho.com/api/checkuserispresentincampaign?authtoken=' + api_key_val + '&scope=CampaignsAPI&resfmt=XML&apikeyzuid=true';
    jQuery("#zc_api_key_error").html('<img width="20" height="20" align="absmiddle" src="' + zc_pluginDir + '/assets/images/uploadImg.gif" />&nbsp;Validating API Key');
    jQuery("#zc_email_error").html('<img width="20" height="20" align="absmiddle" src="' + zc_pluginDir + '/assets/images/uploadImg.gif" />&nbsp;Validating email id');
    jQuery.ajax({
        type: 'POST',
        url: api_url,
        success: function (responseData, textStatus, jqXHR) {
            var parsedXML = jQuery.parseXML(responseData);
            var statusText = jQuery("status", responseData).text();
            var codeText = jQuery("code", responseData).text();
            var accountId = jQuery("owner_zuid", responseData).text();
            var orgName = jQuery("org_name", responseData).text();
            var emailId = jQuery("user_email_id", responseData).text();
            var accountStatus = jQuery("is_user_present", responseData).text();
            accountStatus = (accountStatus=="true")?1:0;
            if(emailId != jQuery("#zc_emailId").val().trim())   {
                jQuery("#zc_emailId").css({"border": "2px solid #e03131"});
                jQuery("#zc_email_error").html("<img width='20' height='20' src='" + zc_pluginDir + "/assets/images/zc_fail.png' align='absmiddle'/>&nbsp;This is not a valid email id.");
                jQuery("#zc_email_error").css({
                    "color": "#e03131",
                    "font-size": "100%"
                });
                jQuery("#zc_api_key_error").html('');
                jQuery("#zc_emailId").removeAttr("readonly");
                jQuery("#zc_api_key").removeAttr("readonly");
                jQuery("#save_account").removeAttr("disabled");
                jQuery("#cancel_account_changes").removeAttr("disabled");
                return;
            }
            if (statusText == 'success' && ( codeText == 0 || codeText == 2401 )) {
                if(zc_on_load_api_val != '' && zc_on_load_account_id != accountId) {
                    jQuery("#ageIndicator").val("new");
                    var newKeyPromptVal = confirm("Once you update your API key, all sign-up forms related to the existing API key will be automatically deleted.");
                    if(!newKeyPromptVal) {
                        jQuery("#zc_api_key").removeAttr("disabled");
                        jQuery("#save_account").removeAttr("disabled");
                        jQuery("#cancel_account_changes").removeAttr("disabled");
                        jQuery("#zc_api_key_error").text("");
                        jQuery("#zc_api_key").select();
                        jQuery("#help_link").css("display", "block");
                        jQuery("#next_page_span").css("display","none");
                        jQuery("#cancel_account_changes").css("display", "inline-block");
                        return false;
                    }
                    else {
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth()+1; //January is 0!
                        var yyyy = today.getFullYear();
                        jQuery("#integratedDate").val(dd+"/"+mm+"/"+yyyy);
                    }
                }
                else if(zc_on_load_api_val != '')  {
                    jQuery("#ageIndicator").val("old");
                }
                else {
                    var today = new Date();
                    var dd = today.getDate();
                    var mm = today.getMonth()+1; //January is 0!
                    var yyyy = today.getFullYear();
                    jQuery("#integratedDate").val(dd+"/"+mm+"/"+yyyy);
                }
                jQuery("#zc_api_key_error").css({"color":"#55b667"});
                jQuery("#zc_api_key_error").html('<img width="20" height="20" src="' + zc_pluginDir + '/assets/images/zc_success.png" align="absmiddle" />&nbsp;Verified');
                jQuery("#zc_email_error").css({"color":"#55b667"});
                jQuery("#zc_email_error").html('<img width="20" height="20" src="' + zc_pluginDir + '/assets/images/zc_success.png" align="absmiddle" />&nbsp;Verified');
                jQuery("#save_account").html('Saving..&nbsp;<img width="20" height="20" src="' + zc_pluginDir + '/assets/images/uploadImg.gif" align="absmiddle" />');
                jQuery("#cancel_account_changes").attr("disabled","disabled");
                jQuery("#next_page_span").css("display","none");
                jQuery("#hidden_text").val("VALID");
                jQuery("#zc_api_key").css({"border": "1px solid #DDDDDD"});
                jQuery("#zc_emailId").css({"border": "1px solid #DDDDDD"});
                jQuery("#accountId").val(accountId);
                jQuery("#orgName").val(orgName);
                jQuery("#active").val(accountStatus)
                jQuery("#emailId").val(emailId);
                jQuery("#firsttimesave").val("1");
                jQuery("#zc_domain_url").attr("value",'https://campaigns.zoho.com');
                jQuery("#api_key_form").submit();
                return true;
            }
            else if(codeText == 1007)
            {
                return false;
            }
            else {
                if(codeText == 997 || codeText == 998) {
                    var message = jQuery("message",responseData).text();
                    alert(message);
                }  
                jQuery("#zc_api_key").css({"border": "2px solid #e03131"});
                jQuery("#zc_api_key_error").html("<img width='20' height='20' src='" + zc_pluginDir + "/assets/images/zc_fail.png' align='absmiddle'/>&nbsp;Invalid API key.");
                jQuery("#zc_email_error").html("");
                jQuery("#zc_api_key_error").css({
                    "color": "#e03131",
                    "font-size": "100%"
                });
                jQuery("#zc_emailId").removeAttr("readonly");
                jQuery("#zc_api_key").removeAttr("readonly");
                jQuery("#save_account").removeAttr("disabled");
                jQuery("#cancel_account_changes").removeAttr("disabled");
                return false;
            }
        },
        error: function (responseData, textStatus, errorThrown) {
            var api_url = 'https://campaigns.zoho.eu' + '/api/checkuserispresentincampaign?authtoken=' + api_key_val + '&scope=CampaignsAPI&resfmt=XML&apikeyzuid=true';
             jQuery.ajax({
                type: 'POST',
                url: api_url,
                success: function (responseData, textStatus, jqXHR) {
                    var parsedXML = jQuery.parseXML(responseData);
                    var statusText = jQuery("status", responseData).text();
                    var codeText = jQuery("code", responseData).text();
                    var accountId = jQuery("owner_zuid", responseData).text();
                    var orgName = jQuery("org_name", responseData).text();
                    var emailId = jQuery("user_email_id", responseData).text();
                    var accountStatus = jQuery("is_user_present", responseData).text();
                    accountStatus = (accountStatus=="true")?1:0;
                    if(emailId != jQuery("#zc_emailId").val().trim())   {
                        jQuery("#zc_emailId").css({"border": "2px solid #e03131"});
                        jQuery("#zc_email_error").html("<img width='20' height='20' src='" + zc_pluginDir + "/assets/images/zc_fail.png' align='absmiddle'/>&nbsp;This is not a valid email id.");
                        jQuery("#zc_email_error").css({
                            "color": "#e03131",
                            "font-size": "100%"
                        });
                        jQuery("#zc_api_key_error").html('');
                        jQuery("#zc_emailId").removeAttr("readonly");
                        jQuery("#zc_api_key").removeAttr("readonly");
                        jQuery("#save_account").removeAttr("disabled");
                        jQuery("#cancel_account_changes").removeAttr("disabled");
                        return;
                    }
                    if (statusText == 'success' && ( codeText == 0 || codeText == 2401 )) {
                        if(zc_on_load_api_val != '' && zc_on_load_account_id != accountId) {
                            jQuery("#ageIndicator").val("new");
                            var newKeyPromptVal = confirm("Once you update your API key, all sign-up forms related to the existing API key will be automatically deleted.");
                            if(!newKeyPromptVal) {
                                jQuery("#zc_api_key").removeAttr("disabled");
                                jQuery("#save_account").removeAttr("disabled");
                                jQuery("#cancel_account_changes").removeAttr("disabled");
                                jQuery("#zc_api_key_error").text("");
                                jQuery("#zc_api_key").select();
                                jQuery("#help_link").css("display", "block");
                                jQuery("#next_page_span").css("display","none");
                                jQuery("#cancel_account_changes").css("display", "inline-block");
                                return false;
                            }
                            else {
                                var today = new Date();
                                var dd = today.getDate();
                                var mm = today.getMonth()+1; //January is 0!
                                var yyyy = today.getFullYear();
                                jQuery("#integratedDate").val(dd+"/"+mm+"/"+yyyy);
                            }
                        }
                        else if(zc_on_load_api_val != '')  {
                            jQuery("#ageIndicator").val("old");
                        }
                        else {
                            var today = new Date();
                            var dd = today.getDate();
                            var mm = today.getMonth()+1; //January is 0!
                            var yyyy = today.getFullYear();
                            jQuery("#integratedDate").val(dd+"/"+mm+"/"+yyyy);
                        }
                        jQuery("#zc_api_key_error").css({"color":"#55b667"});
                        jQuery("#zc_api_key_error").html('<img width="20" height="20" src="' + zc_pluginDir + '/assets/images/zc_success.png" align="absmiddle" />&nbsp;Verified');
                        jQuery("#zc_email_error").css({"color":"#55b667"});
                        jQuery("#zc_email_error").html('<img width="20" height="20" src="' + zc_pluginDir + '/assets/images/zc_success.png" align="absmiddle" />&nbsp;Verified');
                        jQuery("#save_account").html('Saving..&nbsp;<img width="20" height="20" src="' + zc_pluginDir + '/assets/images/uploadImg.gif" align="absmiddle" />');
                        jQuery("#cancel_account_changes").attr("disabled","disabled");
                        jQuery("#next_page_span").css("display","none");
                        jQuery("#hidden_text").val("VALID");
                        jQuery("#zc_api_key").css({"border": "1px solid #DDDDDD"});
                        jQuery("#zc_emailId").css({"border": "1px solid #DDDDDD"});
                        jQuery("#accountId").val(accountId);
                        jQuery("#orgName").val(orgName);
                        jQuery("#active").val(accountStatus)
                        jQuery("#emailId").val(emailId);
                        jQuery("#firsttimesave").val("1");
                        jQuery("#zc_domain_url").attr("value",'https://campaigns.zoho.eu');
                        jQuery("#api_key_form").submit();
                        return true;
                    }
                    else {
                        if(codeText == 997 || codeText == 998) {
                            var message = jQuery("message",responseData).text();
                            alert(message);
                        }  
                        jQuery("#zc_api_key").css({"border": "2px solid #e03131"});
                        jQuery("#zc_api_key_error").html("<img width='20' height='20' src='" + zc_pluginDir + "/assets/images/zc_fail.png' align='absmiddle'/>&nbsp;Invalid API key.");
                        jQuery("#zc_email_error").html("");
                        jQuery("#zc_api_key_error").css({
                            "color": "#e03131",
                            "font-size": "100%"
                        });
                        jQuery("#zc_emailId").removeAttr("readonly");
                        jQuery("#zc_api_key").removeAttr("readonly");
                        jQuery("#save_account").removeAttr("disabled");
                        jQuery("#cancel_account_changes").removeAttr("disabled");
                        return false;
                    }
                },
                 error: function (responseData, textStatus, errorThrown) {
                    var codeText = jQuery("code", responseData).text();
                    var statusText = jQuery("status", responseData).text();
                    jQuery("#zc_api_key_error").html('');
                    jQuery("#zc_api_key").css({
                        "border": "2px solid #e03131"
                    });
                    jQuery("#zc_api_key_error").html("<img width='20' height='20' src='" + zc_pluginDir + "/assets/images/zc_fail.png' align='absmiddle'/>&nbsp;Invalid API key.");
                    jQuery("#zc_email_error").html("");
                    jQuery("#zc_api_key_error").css({
                        "color": "#e03131",
                        "font-size": "100%"
                    });
                    jQuery("#zc_emailId").removeAttr("readonly");
                    jQuery("#zc_api_key").removeAttr("readonly");
                    jQuery("#save_account").removeAttr("disabled");
                    jQuery("#cancel_account_changes").removeAttr("disabled");
                    return false;
             }  });
        }
    });
    
}
