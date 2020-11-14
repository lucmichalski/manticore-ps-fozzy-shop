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

$(document).ready(function() {
    
    var heightOfWindow = $('.modal-content').innerHeight();
    var widthOfWindow = $('.modal-content').innerWidth();
    var marginTop = -1*heightOfWindow/2;
    var marginLeft = -1*widthOfWindow/2;
    $('.modal-content').css("margin-top", marginTop);
    $('.modal-content').css("margin-left", marginLeft);
    
    $('.assign_c').click(function() {
	$('#assign_to_customer').modal('show');
    });
    
    $('#create_c').click(function() {
	$('#create_customer').modal('show');
    });
    $('#show_d').click(function() {
	$('#show_discounts').modal('show');
    });
    
    $('#act_assign').click(function() {
	$('.assign_wait').animate({'opacity':'0.3'}, 'fast');
	$('.assign_wait').css("z-index", 10000);
	$.post(find_url, {
	    'ajax': true,
	    'action': 'assignOrderToCustomer',
	    'id_sm_ooc_order': id_sm_ooc_order,
	    'id_customer': $('input[name="customer_autocomplete_hinput"]').val(),
	}, function(data) {
	    data = JSON.parse(data);
	    if (data.status === 'ok') {
		location.reload();
	    } else if (data.status === 'error') {
		$('.assign_wait').animate({'opacity':'0'}, 'fast');
		$('.assign_wait').css("z-index", -1);
		alert(data.message);
	    } else {
		$('.assign_wait').animate({'opacity':'0'}, 'fast');
		$('.assign_wait').css("z-index", -1);
		alert('Unknown error');
	    }
	});
    });
    
    $('#act_create').click(function() {
	$('.assign_wait').animate({'opacity':'0.3'}, 'fast');
	$('.assign_wait').css("z-index", 10000);
	$.post(find_url, {
	    'ajax': true,
	    'action': 'createANewCustomer',
	    'id_sm_ooc_order': id_sm_ooc_order,
	    'firstname': $('input[name="new_customer_firstname"]').val(),
	    'lastname': $('input[name="new_customer_lastname"]').val(),
	    'email': $('input[name="new_customer_email"]').val(),
	    'password': $('input[name="new_customer_password"]').val(),
	}, function(data) {
	    data = JSON.parse(data);
	    if (data.status === 'ok') {
		location.reload();
	    } else if (data.status === 'error') {
		$('.assign_wait').animate({'opacity':'0'}, 'fast');
		$('.assign_wait').css("z-index", -1);
		alert(data.message);
	    } else {
		$('.assign_wait').animate({'opacity':'0'}, 'fast');
		$('.assign_wait').css("z-index", -1);
		alert('Unknown error');
	    }
	});
    });
    
    $('#customer_autocomplete_input')
	.autocomplete(find_url, {
	    minChars: 1,
	    autoFill: true,
	    max:20,
	    matchContains: true,
	    mustMatch:false,
	    scroll:false,
	    cacheLength:0,
	    formatItem: function(item) {
		return item[1]+' - '+item[0];
	    },
	    extraParams: {
		//ast: customer_token,
		'action': 'findCustomer',
		'ajax': 'true',
	    }
	}).result(function(event, data, formatted){
	    $('#customer_autocomplete_input').val(data[0]);
	    $('#customer_autocomplete_hinput').val(data[1]);
	});
});
