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


function validate_isEmail(s)
{
    var reg = unicode_hack(/^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i, false);
    var result = reg.test(s)
	console.log(result)
    if (result == false){
        result = validate_isPhoneNumber(s)
    }
	console.log(result)
    return result;
}
$(document).ready(function(){
		if(authMethod > 0){
			$("[name^=email]").removeAttr('type');
		}
    });
