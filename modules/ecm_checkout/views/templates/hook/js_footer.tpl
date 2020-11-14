<script>
var page = "{$page.page_name}";
var ajaxUrl = "{$ajaxUrl|escape:'html'}";
var id_customer = {$id_customer|intval};
var authMethod = {$authMethod|intval};
var formatError= "{l s='Format phone number error!' mod='ecm_checkout' js=1}";
var hasPhone = "{l s='A user with such a phone already exists in the site database. Change is impossible!' mod='ecm_checkout' js=1}";
var deletePhone = "{l s='The phone is used as a register phone. You can not just delete it!!!' mod='ecm_checkout' js=1}";
var deleteAll = "{l s='You can not delete the phone and email at the same time. Leave something)))' mod='ecm_checkout' js=1}";
var phone_mask = "{Configuration::get('ecm_checkout_phone_mask')}";

$(document).ready(function(){
	if(authMethod > 0){
		$("[name^=email]").removeAttr('type');
	}
	if($('#phone').length > 0){
		if(phone_mask) $('#phone').mask(phone_mask);
	}

	if (page == 'password'){
		$(".form-control-submit:visible").css("margin-right","20px").css("margin-bottom","10px");
		$("#btn-sms").insertAfter($(".form-control-submit:visible")).css("margin-bottom","10px");
		
		if (authMethod == 1){
			$(".center-email-fields label").html(phone);
      $("#email").prev().html(phone);
			$(".form-control-submit").hide();
		}
		if (authMethod == 2){
			$(".center-email-fields label").html(phone_email);
			if(phone_mask) $('#btn-sms').mask(phone_mask);
		}
	}
	
	$("#middlename-group").insertAfter($("#customer_first_name").parent().parent()).show()

	
});

$("#middlename-group").insertAfter($("input[name=firstname]").parent().parent()).show()
$("#phone-group").insertBefore($("input[name=email]").parent().parent()).show()

if(validate_isPhoneNumber($('#email').val()) === true){
    $('#phone').val($('#email').val())
    $('#email').val('')
}


if(authMethod==1){
	//	$("#email").parent().hide();

	}
if(authMethod==2 && typeof  page_name !== 'undefined'){
	$("label[for=email]").removeClass('required');
	$(function() {
		$("label[for=email]").find("sup").remove();
	});
	$("#email").removeClass('is_required');
}

function validate_isEmail(s)
{
{literal}
    var reg = unicode_hack(/^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i, false);
{/literal}
 
	var result = reg.test(s)
    if (result == false){
        result = validate_isPhoneNumber(s)
    }
    return result;
}

function validate_isPhoneNumber(s)
{
        var reg = /^[+0-9. ()-]+$/;
        return reg.test(s);
}


</script>
