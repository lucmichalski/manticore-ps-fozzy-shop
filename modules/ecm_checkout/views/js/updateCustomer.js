/**
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    Elcommerce <support@elcommece.com.ua>
* @copyright 2010-2018 Elcommerce
* @license   Comercial
* @category  PrestaShop
* @category  Module
*/
function ajax(data,phone){
    return  $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: JSON.stringify(data, null, 2),
            success: function(msg) {
                if(msg == 'OK') {
                    if (typeof  page_name === 'undefined')
                    showSuccessMessage(success);
                }else if(msg == 'PHONE_ERROR'){
					console.log(msg)
					$('#phone').val(phone);
						$('#phone').val(phone);
						$.growl.error({
                            title: "Error",
                            size: "small",
                            message: hasPhone
                        });

                }else if(msg == 'PHONE_ERROR_FORMAT'){
                        console.log(msg)
						$('#phone').val(phone);
                        $.growl.error({
                                title: "Error",
                                size: "small",
                                message: formatError
                            });
                }
            },
        });
}
document.addEventListener('DOMContentLoaded', function(){ 
        var phone = $('#phone').val();
        var email = $('#email').val();
		
		//$('#phone').mask('380999999999');
        
		$("input:text").change(function() {
			if(id_customer){
                if(authMethod==1 && $(this).attr("id")== 'phone' && $('#phone').val() == ''){
                    alert(deletePhone);
                    $('#phone').val(phone);
                    return false;
                }
                if($(this).attr("id")== 'phone' || $(this).attr("id")== 'middlename'){
                    if ($(this).change) {
                        var data = {
                            val_name:$(this).attr("name"),
                            val_flag         :$(this).val(),
                            val_id         :id_customer
                        };
                        ajax(data,phone);
                    }
                }
			}
        });
        if(authMethod==1 && typeof  page_name !== 'undefined'){
            $("#email").parent().hide();
        }
        if(authMethod==2 && typeof  page_name !== 'undefined'){
            $("#email").addClass('mixed');
            $(".mixed").change(function() {
				if(id_customer){
                    if($('#phone').val() == '' && $('#email').val() == ''){
                        alert(deleteAll);
                        $('#phone').val(phone);
                        $('#email').val(email);
                    }
				}
            });
        }
        $("button[name=submitIdentity]").click(function(){
                var url = document.createElement('a');
                url.href = baseUri
                if ($('#phone').val() && $('#email').val() == '')
                $('#email').val($('#phone').val()+'@'+url.host)

            });
    });
