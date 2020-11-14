/* global $, prestashop */

/**
 * This module exposes an extension point in the form of the `showModal` function.
 *
 * If you want to override the way the modal window is displayed, simply define:
 *
 * prestashop.blockcart = prestashop.blockcart || {};
 * prestashop.blockcart.showModal = function myOwnShowModal (modalHTML) {
 *   // your own code
 *   // please not that it is your responsibility to handle closing the modal too
 * };
 *
 * Attention: your "override" JS needs to be included **before** this file.
 * The safest way to do so is to place your "override" inside the theme's main JS file.
 *
 */

$(document).ready(function () {
    prestashop.blockcart = prestashop.blockcart || {};

    var showModal = prestashop.blockcart.showModal || function (modal) {};

    $(document).ready(function () {

        $(document).on('click', '#js-cart-close', function (e) {
            $('#blockcart, #mobile-cart-wrapper, #_mobile_blockcart-content, #_desktop_blockcart-content').removeClass('show');
            e.stopPropagation();
        });

        $(document).on('click', '#blockcart-content', function (e) {
            e.stopPropagation();
        });

        prestashop.on(
            'updateCart',
            function (event) {
                var refreshURL = $('#blockcart').data('refresh-url');
                var requestData = {};

                if (event && event.reason) {

                    requestData = {
                        id_product_attribute: event.reason.idProductAttribute,
                        id_product: event.reason.idProduct,
                        action: event.reason.linkAction
                    };
                }
                $.post(refreshURL, requestData).then(function (resp) {
                  //   console.log(event);
                    if ( (event.reason && event.reason.linkPlace == 'cart-preview') || (event.reason && event.reason.linkAction == 'add-to-cart' && $('input#popup_change_' + event.reason.idProduct).val() == 1 && $('input#popup_fromcart_' + event.reason.idProduct).val() == 1 ) ) {
                        $('#blockcart').replaceWith($(resp.preview).addClass('show'));
                        if ( (event.reason && event.reason.linkAction == 'add-to-cart' && $('input#popup_change_' + event.reason.idProduct).val() == 1 && $('input#popup_fromcart_' + event.reason.idProduct).val() == 1) && $("#blockcart #blockcart-content").length )
                          {
                          $('#blockcart #_desktop_blockcart-content').addClass('show');
                          $('#cart-toogle').attr( "aria-expanded", "true" );
                          }
                    } else {
                        $('#blockcart').replaceWith($(resp.preview));
                    }

                    $('#mobile-cart-products-count').text($(resp.preview).find('.cart-products-count-btn').first().text());

                    prestashop.emit('responsive updateAjax', {
                        mobile: prestashop.responsive.mobile
                    });
                    if (event.reason.linkAction == 'delete-from-cart') {
                          $('#btn_add_' + event.reason.idProduct).show();
                          $('#btn_change_' + event.reason.idProduct).hide();
                          $('#blockcart-modal-' + event.reason.idProduct).remove();
                          $('div.modal-backdrop.fade').remove();
                          $('body').removeClass('modal-open');
                          $('body').find('.loader').remove();
                          active_krutilka();
                        }
                    if (resp.modal) {
                        if ($('input#popup_change_' + event.reason.idProduct).val() == 1) {
                        $('body').find('.loader').remove();
                        }
                        else {
                        showModal(resp.modal);
                        }
                        if (event.reason.linkAction == 'add-to-cart') {
                          $('#btn_add_' + event.reason.idProduct).hide();
                          $('#btn_change_' + event.reason.idProduct).show();
                          $('body>#blockcart-modal-' + event.reason.idProduct).detach();
                        //  $('#blockcart-content #blockcart-modal-' + event.reason.idProduct).detach().prependTo("body"); 
                          $('div.modal-backdrop.fade').remove();
                          $('body').removeClass('modal-open');
                          $('body').find('.loader').remove();
                          active_krutilka();
                        }
                    }
                }).fail(function (resp) {
                    prestashop.emit('handleError', {eventType: 'updateShoppingCart', resp: resp});
                });
            }
        );
    });
});
