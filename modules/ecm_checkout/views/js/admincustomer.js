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
document.addEventListener('DOMContentLoaded', function(){ 
	$("#middlename-group").insertAfter($("#firstname").parent().parent()).show()
	$("#phone-group").insertAfter($("#lastname").parent().parent()).show()
	$("#middlename-group").insertAfter($("#customer_first_name").parent().parent()).show()
	$("#phone-group").insertAfter($("#customer_last_name").parent().parent()).show()
});
