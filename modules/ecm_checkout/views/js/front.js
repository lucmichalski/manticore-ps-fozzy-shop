/**
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop (ц)
*
* @author    Elcommerce <support@elcommece.com.ua>
* @copyright 2010-2018 Elcommerce
* @license   Comercial
* @category  PrestaShop
* @category  Module
*/


document.addEventListener('DOMContentLoaded', function(){
        
$('#pleaseWaitDialog').hide();

	$('#page-preloader').hide();
	$('#page-preloader').hide();
	if (hide_column_right) {
		$('#center_column').prop('style', 'width:100%');
		$('#right_column').hide();
	}

	if (hide_header) {
		$('.header-container').hide();
	} else {
		$('.shopping_cart').hide();
	}
	//$('.breadcrumb').hide();
	if (cart_qties){
		//action('init_cart', id_cart, false);
	} else {
		$('#pleaseWaitDialog').hide();
	}
	//setInterval("action('wake_', 0)", 10000);
	//$('.has-spinner').attr('unselectable', 'on').on('selectstart', false);
	var force_render_customer = false;
	var discount_errors = false;
	var to_focus = false;
	var old_qty = 1;
	$("#order_checkout").scrollView();
	//$(".ecm-menu").scrollView();



	$('.type_auth').off ('change')
	$('.type_auth').on ('change', function(){
		check_auth()
		action('save_auth','auth',type_auth);
	})

/*
	$('.login_ontype.email').off ('input')
	$('.login_ontype.email').on ('input', function(){
		if (authMethod == 2 && type_auth != 'login' && !prestashop.customer.is_logged){
			if (no_phonenumber($('.login_ontype.email').val())){
				$('.login_ontype.phone').removeAttr('hidden').prop('id','phone').prop('name','phone');
				$('.login_ontype.email').prop('id','email');
			} else {
				$('.login_ontype.phone').hide().prop('id','').prop('name','');
				$('.login_ontype.email').prop('id','phone');
			}
			//$('#email').trigger('input')
		} else if (authMethod == 2 && type_auth != 'login'){
			//console.log(1)
		}
		if (type_auth != 'login'){
			delay(this,1000,true);
		}
	})

	$('.login_ontype.email').trigger('input');
    
*/
	check_auth()

});
	


function action(command, id, name, to_focus) {
    if (to_focus === undefined) {
        to_focus = true; 
    }
    if (to_focus === 'true') {
        to_focus = true; 
    }
    if (to_focus === 'false') {
        to_focus = false; 
    }
    if (to_focus === 'null') {
        to_focus = false; 
    }

    if (!(command === 'save_auth' || command === 'save_message' || command === 'save_customer' || command === 'save_login' || command === 'save_address' || command === 'wake_')) {
        $('.has-spinner').attr('disabled','disabled');
        $('#pleaseWaitDialog').show();
        $('#page-preloader').show();
    }

    var data = {
        action: 'Refresh',
        ajax : true,
        command: command,
        id: id,
        name: name,
    };
	$.ajax({
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(result) {
                if (!result) {
                    console.error('Error: '+ data.command + ', not found');
                }
                if (typeof(result.errors) != 'undefined'){
                    $('#ecm_checkout #login_errors').html(result.errors);
                    $('#ecm_checkout #errors_msg').show();
                } else {
                    $('#ecm_checkout #login_errors').html('');
                    $('#ecm_checkout #errors_msg').hide();
                }

                if(typeof(result) == 'object'){
					if (result.length == 0) return;
					if (result.empty_cart){
						document.location.href =  result['href'];
						return false;
                    }
                    
                    if (command == 'make_order'){
						if(result.error  != undefined){
							$.growl.error({
	                            title: result.error,
	                            size: "medium",
	                            message: "",
	                        });
							$('.has-spinner').removeAttr('disabled');
	                        $('#pleaseWaitDialog').hide();
							return;
						}
                        if(result.id_customer  == '0'){
							$.growl.error({
	                            title: 'Customer not created!!!',
	                            size: "medium",
	                            message: "Contact site administration",
	                        });
							$('.has-spinner').removeAttr('disabled');
	                        $('#pleaseWaitDialog').hide();
							return;
						}
						$('#order-validation').remove();
                        $('.has-spinner').removeAttr('disabled');
                        //$('#pleaseWaitDialog').hide();
                        
                        var first_url = document.createElement('a');
                        first_url.href = $('#action').val();
                        result['href'] = $('#action').val();
                        result['href'] = result['href'] ? result['href'] : $('#ya-form').prop('action');
                        var searh = first_url.search.replace('?','').split('&');
                        var params = [];
                        for (i = 0; i < searh.length; i++) {
                            var pair = searh[i].split('=');
                            params[pair[0]] = pair[1];
                        }
                        var inputs = $('#payment_place form').serializeArray();
                        for (var key in inputs){
                            //console.log (inputs[key].name);
                            params[inputs[key].name] = inputs[key].value;
                        }
                        delete (params['controller']);
                        delete (params['s']);
                        delete (params['step']);
                        delete (params['order']);
                        params['confirm'] = true;
                        params['id_address_delivery'] = result['id_address_delivery'];
                        params['delivery_option['+result['id_address_delivery']+']'] = result['id_carrier'];
			
                        post(result['href'], params);
                        return false;
                    }
                    
					
					for (var place in result) {
						//console.log(place);
                        $('#'+place+'_place').html(result[place]);
                    }
                    if (typeof(result['res']) == 'string'){
                        console.log(result['res']);
                    }
	                
	                if (command == 'save_customer') {
	                    var force_render_customer = true
	                }
	                
                    if (typeof(result.errors) != 'undefined'){
                        console.log(result.errors)
                        $.growl.error({
                            title: result.errors,
                            size: "medium",
                            message: "",
                            duration: 6000,
                        });
	                    //$('#ecm_checkout #login_errors').removeAttr('hidden').html(result.errors);
	                    //$('#ecm_checkout #errors_msg').show();
	                    //$('#ecm_checkout #'+name).removeClass('sc-ok').addClass('sc-error');
	                    //$('#ecm_checkout #'+name).next().show().text(result.errors);
	                    return;
	                } else {
	                    //$('#ecm_checkout .login_errors').html('')
	                    //$('#ecm_checkout #errors_msg').hide();
	                    //$('#ecm_checkout #'+name).removeClass('sc-error');
	                    //$('#ecm_checkout #'+name).next().hide().text('');
	                }
	                
	                if (typeof(result.warning) != 'undefined'){
	                    $('.'+name).next().next().show().text(result.warning);
	                    
	                    if (result.exist_customer){
	                        $.each($('.customer_ontype:required'), function(index, value) {
	                                $(value).removeAttr('required');
	                                $(value).removeClass('sc-error');
	                            });
	                        //$('.login_ontype').removeAttr('required').removeClass('sc-error');
	                        $('.'+name).removeClass('sc-error').prop("defaultValue", $('.'+name).val());
	                        if(typeof(result.exist_customer) != 'boolean') {
	                            $('#errors_msg').hide();
	                        }
	                        $('.auth_ch').prop('hidden',true);
							$('.password').prop('hidden',true);
							//sc_customer = 'temp';

	                    } else {
	                        $('.'+name).prop("defaultValue", $('.'+name).val());
	                        $.each($('.customer_ontype, .login_ontype'), function(index, value) {
	                                if(typeof($(value).attr('unvisible')) != 'string') {
	                                    if($(value).prev().hasClass('required-after')) {
	                                        $(value).prop('required',true)
	                                    }
	                                   /* if(!$(value)[0].checkValidity()){
	                                        $(value).removeClass('sc-ok').addClass('sc-error');
	                                    } else{
	                                        $(value).removeClass('sc-error').addClass('sc-ok');
	                                    } */
	                                }
	                        });
	                        $('.auth_ch').removeAttr('hidden');
	                        if ($('#auth').prop('checked')) {
								$('.password').removeAttr('hidden');
							}
	                        
	                        if (force_render_customer) {
	                            action(renderCustomerSeq);
	                            var force_render_customer = false;
	                        }
							//sc_customer = 'temp';
	                    }
	                } else {
	                    if(typeof(result.warning) == 'undefined'){
	                        if (force_render_customer) {
	                            action(renderCustomerSeq);
	                            var force_render_customer = false;
	                        }
	                    }
	                }
	
	                if (command != 'wake_'  || command != 'make_order') {
	                    $('.has-spinner').removeAttr('disabled');
	                    $('#pleaseWaitDialog').hide();
	                }
	                if (typeof(result.errors) == 'undefined'){
	                    $("#"+name).prop("defaultValue", id);
	                }
	            
	                 
	                if (to_focus) $("#"+name).focus().setCursorPosition(to_focus);
	                //if (cart.id_carrier == "0") $('.sc_carrier_row_checked').trigger('click');
	                
	                if (typeof(discount_errors) != 'undefined' && discount_errors){
	                    $('#discount_error').html(result.discount_errors);
	                    $('#discount_error').show();
						discount_errors = result.discount_errors;
	                } else {
	                    $('#discount_error').hide();
						discount_errors = false;
	                }
	                
	                if (typeof(result.quantity_errors) != 'undefined'){
	                   $.growl.error({
							title: result.quantity_errors,
			                size: "medium",
			                message: "",
			            });                
		            }

	                if (typeof(old_qty) != 'undefined' && typeof(result.qty_success) != 'undefined' && !result.qty_success){
	                    $("#"+name).val(old_qty);
	                    $("#"+name).prop("defaultValue", old_qty);
	                }

	                
	

				}
                //if (to_focus) $("#"+name).focus().setCursorPosition(to_focus);
                //if (cart.id_carrier == "0") $('.sc_carrier_row_checked').trigger('click');
                
				//console.log(data.command)

				if(render_action.indexOf(data.command) != -1){
					action(renderSeq);
				}

				if (typeof(updateAjaxCart)=='function') updateAjaxCart();

                $('#page-preloader').hide();
            },
        });
}


function ischange(obj){
    obj.val($.trim(obj.val()))
	return (obj.val() != obj.prop("defaultValue"));
}


function add_Discount(obj){
    action('add_Discount', $("#discount_name").val());
}

function delay(obj,timeout,to_focus){
    if (to_focus === undefined) {
        to_focus = true; 
    }

	if(ischange($(obj)) && ( !$(obj).val() || $(obj)[0].checkValidity() ) ) {
        
		if (to_focus) to_focus = obj.selectionStart;
        var command ="action('"+$(obj).attr('act')+"', '"+escapeHtml($(obj).val()).replace(/\n/g, '\\n')+"', '"+$(obj).attr('id')+"', '"+to_focus+"')";
        clearTimeout(timeoutId);
        timeoutId = setTimeout(command, timeout);
    } else {
        clearTimeout(timeoutId);
    }
    
}

function delay_quantity(obj,timeout,to_focus){
    if(ischange($(obj))) {
        old_qty = $(obj).prop("defaultValue");
        if (to_focus) to_focus = obj.selectionStart;
        var command ="action('"+$(obj).attr('act')+"', '"+escapeHtml($(obj).val())+"', '"+$(obj).attr('id')+"', '"+to_focus+"')";
        clearTimeout(timeoutId);
        timeoutId = setTimeout(command, timeout);
    } else {
        clearTimeout(timeoutId);
    }
}

function ischange_quantity(obj){
    return (obj.val() != obj.prop("defaultValue"))
}

$.fn.setCursorPosition = function(pos) {
    this.each(function(index, elem) {
            if (elem.setSelectionRange) {
                elem.setSelectionRange(pos, pos);
            } else if (elem.createTextRange) {
                var range = elem.createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        });
    return this;
};


function login(){
    var data = {
        action: 'Refresh',
        ajax : true,
        command: 'to_login',
        email: $('#email').val()?$('#email').val():$('#phone_email').val(),
        phone: $('#phone').val()?$('#phone').val():$('#phone_email').val(),
        passwd: $('#password').val(),
    };
    $.ajax({
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(result) {
/*                if (!result) {
                    alert('Error: '+ data.command + ', not found');
                }*/
                //console.log(result);
                if (typeof(result.errors) != 'undefined'){
                    $('#ecm_checkout .login_errors').html(result.errors);
                    $('#ecm_checkout .login_errors').removeAttr('hidden');
                } else {
                    $('#ecm_checkout .login_errors').html('');
                    $('#ecm_checkout .login_errors').prop('hidden',true);
                }
                if (typeof(result.success) != 'undefined' &&  result.success){
                    location.reload();
                    //action(''+renderSeq, id_cart);
                }
            }
        });
}

function escapeHtml(text) {
    return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function post(path, params, method) {
    method = method || "post"; 
    $('#order-validation').remove();
    var form = document.createElement("form");
    form.setAttribute("id", "order-validation");
    form.setAttribute("name", "order-validation");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }
    document.body.appendChild(form);
    $('#order-validation').submit();
}

function no_phonenumber(inputtxt){
	if (inputtxt=='') return false;
	var reg_phone = /^[+0-9. ()-]+$/;
	return !reg_phone.test(inputtxt);
}

$.fn.scrollView = function () {
  return this.each(function () {
    $('html, body').animate({
      scrollTop: $(this).offset().top
    }, 500);
  });
}


$(document).ajaxComplete(function( event, xhr, settings) {
	
	$('.type_auth').off ('change')
	$('.type_auth').on ('change', function(){
		check_auth()
		action('save_auth','auth',type_auth);
	})


 	$('#phone_email').off ('input')
	$('#phone_email').on ('input', function(){
		if (authMethod == 2 && type_auth != 'login' && !prestashop.customer.is_logged){
			if ($(this).val() && no_phonenumber($(this).val())){
				if ($('#email').val() != $(this).val())
					$('#email').val($(this).val()).trigger('change').prop('hidden',true).attr('required',true);
				$('#phone').removeAttr('hidden');
				if (verify_password){
					$(this).parent().removeClass('frl-50').addClass('frr-100');
					$('.phone-block').removeClass('frr-50').addClass('frr-100');
					$('#phone2').addClass('check-50');
				}
			} else {
				$('#email').val('').prop('hidden',true).removeAttr('required').change();
				if ($(this).val())
					if ($('#phone').val() != $(this).val())
						$('#phone').val($(this).val()).prop('hidden',true).trigger('change');
				$('#phone').prop('hidden',true);
				
				if (verify_password){
					$(this).parent().removeClass('frr-100').addClass('frl-50');
					$('.phone-block').removeClass('frr-100').addClass('frr-50');
					$('#phone2').removeClass('check-50');
				}
			}
			//$('#email').trigger('input')
		} else if (authMethod == 2 && type_auth != 'login'){
			//console.log(1)
		}
	})

	
	
	$("#ecm_checkout .login_ontype").off('input')
	$("#ecm_checkout .login_ontype").on("input", function (){
		if (type_auth != 'login') {
			if ($(this)[0].checkValidity() || !$(this).val()) {
				$(this).removeClass('sc-error');
			} else {
				//$(this).removeClass('sc-ok').addClass('sc-error');
			}
			if (verify_password){
				if ($('#phone2').val() == $('#phone').val()) {
					$('#phone2').removeClass('sc-error');
				} else {
					$('#phone2').addClass('sc-error');
				}
			}
			delay(this,3000,true);
		}
	})

	$("#ecm_checkout .pass_ontype").off('input')
	$("#ecm_checkout .pass_ontype").on("input", function (){
		if (type_auth != 'login') {
			if ($(this)[0].checkValidity() || !$(this).val()) {
				$(this).removeClass('sc-error');
			} else {
				//$(this).removeClass('sc-ok').addClass('sc-error');
			}
			if (verify_password){
				if ($('#password2').val() == $('#password').val()) {
					$('#password2').removeClass('sc-error');
				} else {
					$('#password2').addClass('sc-error');
				}
			}
			delay(this,3000,true);
		}
	})



	
	if ($('#phone_email').val() != $('#phone_email').prop('defaultValue'))
		$('#phone_email').trigger('input');
	
	
	check_auth();
	
})

function check_auth(){
	type_auth = $('[name=type_auth]:checked').val();

	if (type_auth == 'guest') {
		$('.phone-block').removeAttr('hidden')
		$('.password-block').prop('hidden',true);
		$('.button-block').prop('hidden',true);
		$('#password').removeAttr('required');
		if (verify_password){
			$('#password2').removeAttr('required');
			$('#phone2').attr('required',true).removeAttr('hidden');
		}
		$('.customer_fields').removeAttr('hidden');
		if ($('#phone_email').val() != $('#phone_email').prop('defaultValue'))
			$('#phone_email').trigger('input');
		
		$('#ecm_checkout .login_errors').html('');
        $('#ecm_checkout .login_errors').prop('hidden',true);

	} else if (type_auth == 'login') {
		$('.password-block').removeAttr('hidden');
		$('.button-block').removeAttr('hidden');
	//	$('.phone-block').prop('hidden',true)
		$('#password').prop('required',true);
		if (verify_password){
			$('#password2').removeAttr('required').prop('hidden',true);
			$('#phone2').removeAttr('required').prop('hidden',true);
		}
		$('.customer_fields').prop('hidden',true);
 
	} else {
		$('.phone-block').removeAttr('hidden')
		$('.password-block').removeAttr('hidden');
		$('.button-block').prop('hidden',true);
		$('#password').prop('required',true);
		if (verify_password){
			$('#password2').prop('required',true).removeAttr('hidden');
			$('#phone2').attr('required',true).removeAttr('hidden');
		}
		if (password_generate){
			$('.password-block').prop('hidden',true);
			$('#password').removeAttr('required');
									   
			$('#password2').removeAttr('required');
		}
		$('.customer_fields').removeAttr('hidden');
		if ($('#phone_email').val() != $('#phone_email').prop('defaultValue'))
			$('#phone_email').trigger('input');
		$('#ecm_checkout .login_errors').html('');
        $('#ecm_checkout .login_errors').prop('hidden',true);
	}
	if (type_auth == 'login') {
		if (verify_password) {
			$('.phone_email-block').removeClass('frl-50').addClass('frr-100');
	   
			$('.password_login.check-50, .password2_login.check-50').removeClass('check-50')
		}
	} else {
		if (verify_password) {
			$('.phone_email-block').removeClass('frl-100').addClass('frl-50');
			$('.password_login, .password2_login').addClass('check-50')
		}
	}
	$('#phone_email').trigger('input');
}