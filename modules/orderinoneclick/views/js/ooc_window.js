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
*  @copyright 2014-2017 Yuri Denisov
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var ooc_window_content = '#modal-content-ooc';
var ooc_window_modal = '#ooc-window-modal';
var ooc_form = '#ooc_form';
var hide_ooc_window_button = '#hide-ooc-window';
var product_form = '#add-to-cart-or-refresh';

$(document).ready(function() {
    $('.show_ooc_window').click(function() {
	$.post(action_url, {
	    'ajax': true,
	    'preview': preview,
            'id_lang': id_lang,
            'preview_shop_id': preview_shop_id,
	    'product_button': product_button,
	}, function(data) {
	    data = JSON.parse(data);
	    if (data.status === 'ok') {
		$(ooc_window_content).html(data.message);
                $(ooc_window_modal).modal('show');
                $(hide_ooc_window_button).click(function() {
                    $(ooc_window_modal).modal('hide');
                });
                $(ooc_form).submit(function(e) {
                    $('.sk-circle').css("z-index", 10000);
                    $('.sk-circle').animate({opacity:'0.5'},1000);
                    update_ooc_window(this, e);
		});
	    } else {
		alert('ERROR');
	    }
	});
    });
});

function update_ooc_window(form, e) {
    e.preventDefault();
    $.post(action_url, {
	'type': 'POST',
	'preview': preview,
        'id_lang': id_lang,
        'preview_shop_id': preview_shop_id,
	'product_button': product_button,
	'act': 'ooc_submit',
	'data': $(form).serialize(),
        'product_data': $(product_form).serialize(),
    }, function(data) {
        $('.sk-circle').css("z-index", 0);
        $('.sk-circle').animate({opacity:'0'});
	data = JSON.parse(data);
	if (data.status === 'ok') {
	    $(ooc_window_content).html(data.message);
            $(ooc_form).submit(function(e) {
                $('.sk-circle').css("z-index", 10000);
                $('.sk-circle').animate({opacity:'0.5'},1000);
                update_ooc_window(this, e);
            });
            $(hide_ooc_window_button).click(function() {
                $(ooc_window_modal).modal('hide');
                $('.sk-circle').css("z-index", 0);
                $('.sk-circle').animate({opacity:'0'});
            });
	} else {
            $('.sk-circle').css("z-index", 0);
	    alert('ERROR');
	}
    });
}
