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

function simcheck_sms(){
    var data = {
        action: 'Sms',
        ajax : true,
        phone: $('#email').val(),
    };
    $.ajax({
		type: 'POST',
		dataType: 'json',
		data: data,
		url: ajaxSms,
		success: function(result) {
			if(result.success){
				document.location.href =  ajaxSms;
				return;
			}
			$('.alert-danger').remove();
			$('h1.page-subheading').after(result.error_msg);
		}
	});
}

