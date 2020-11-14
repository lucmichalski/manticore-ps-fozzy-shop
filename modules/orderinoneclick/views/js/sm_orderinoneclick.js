/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Yuri Denisov <contact@splashmart.ru>
*  @copyright 2014-2016 Yuri Denisov
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready( function () {
    $('#page-header-desc-sm_ooc_order-switch_pcc').tooltip().click(function(e) {
	$('#sm_loader').animate({'opacity':'0.4'}, 'slow');
	$.ajax({
	    url : sm_ajax_url,
	    data : {
		ajax:true,
		action:'setPriceInCustomerCurrency',
		SHOW_PRICE_IN_CUSTOMER_CURRENCY: $(this).find('i').hasClass('process-icon-toggle-on') ? 0 : 1
	    },
	    success : function(result) {
		if ($('#page-header-desc-sm_ooc_order-switch_pcc i').hasClass('process-icon-toggle-on')) {
		    $('#page-header-sm_ooc_order-switch_pcc i').removeClass('process-icon-toggle-on').addClass('process-icon-toggle-off');
		} else {
		    $('#page-header-sm_ooc_order-switch_pcc i').removeClass('process-icon-toggle-off').addClass('process-icon-toggle-on');
		}
		location.reload(true);
	    }
	});
    });
});
