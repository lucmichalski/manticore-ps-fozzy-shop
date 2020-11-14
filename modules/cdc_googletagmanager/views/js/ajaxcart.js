/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @package   cdc_googletagmanager
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 *
 * Project Name : Google Tag Manager Enhanced Ecommerce (UA) Tracking
 * Created By  : Comptoir du Code
 * Created On  : 2016-06-02
 * Support : https://addons.prestashop.com/contact-community.php?id_product=23806
 */

// GTM Datalayer for google analytics EE when ajaxCart is enabled
var cdcGtm = {
	addToCart : function(product_id, attribute_id, qtity, addedFromProductPage, callerElement) {
		if(product_id) {
			// convert from NaN to default value
			attribute_id = attribute_id || 0;
			if(always_display_variant_id || attribute_id) {
				attribute_id = "-" + attribute_id;
			} else {
				attribute_id = "";
			}
			qtity = qtity || 1;
			var product = {
				'id': product_id + attribute_id,
				'quantity': qtity
			}


			if(addedFromProductPage) {
				// try to add extra informations on product detail
				var productContainer = $('#product');
				if(productContainer.length) {
					// product name
					var pname = productContainer.find('h1').first().text();

					// product price
					var pprice = productContainer.find('#our_price_display').first().text();
					pprice = parseFloat(pprice.replace(/[^0-9\.,]+/g,"").replace(',','.').replace(' ',''));
				}
			} else {
				// try to add extra informations on product list
				var productContainer = $(callerElement).closest('.product-container');
				if(productContainer.length) {
					// product name
					var pname = productContainer.find('.product-name').first().prop('title');

					// product price
					var pprice = productContainer.find('.product-price').first().text();
					pprice = parseFloat(pprice.replace(/[^0-9\.,]+/g,"").replace(',','.').replace(' ',''));
				}
			}

			if(pname) {
				product['name'] = pname;
			}
			if(pprice > 0) {
				product['price'] = "" + pprice;
			}


			// construct datalayer wrapper
			var dlAddToCart = {
				'event': 'addToCart',
				'ecommerce': {
					'add': {
						'products': [product]
					}
				}
			};

			// try to get currency
			if(typeof dataLayer[0] !== 'undefined'
				&& typeof dataLayer[0].ecommerce !== 'undefined'
				&& typeof dataLayer[0].ecommerce.currencyCode !== 'undefined') {
				dlAddToCart['ecommerce']['currencyCode'] = dataLayer[0].ecommerce.currencyCode;
			} else if(typeof currency !== 'undefined') {
				dlAddToCart['ecommerce']['currencyCode'] = currency['iso_code'];
			}

			//console.debug(dlAddToCart);
			dataLayer.push(dlAddToCart);
			//console.log("add to cart: " + product_id + attribute_id + " x " + qtity);
		}
	},

	removeFromCart : function(product_id, attribute_id, qtity) {
		if(product_id) {
			// convert from NaN to default value
			attribute_id = attribute_id || 0;
			if(attribute_id) {
				attribute_id = "-" + attribute_id;
			} else {
				attribute_id = "";
			}
			qtity = qtity || 1;
			dataLayer.push({
				'event': 'removeFromCart',
				'ecommerce': {
					'remove': {
						'products': [{
							'id': product_id + attribute_id,
							'quantity': qtity
						}]
					}
				}
			});
			//console.log("remove from cart: " + product_id + attribute_id + " x " + qtity);
		}
	}
}

// Prestashop 1.5 || 1.6
if(typeof(ajaxCart) != 'undefined') {
	// override ajaxCart.add function
	var ajaxCartAddFunc = ajaxCart.add;
	ajaxCart.add = function(idProduct, idCombination, addedFromProductPage, callerElement, quantity, wishlist) {
		ajaxCartAddFunc(idProduct, idCombination, addedFromProductPage, callerElement, quantity, wishlist);
		cdcGtm.addToCart(idProduct, idCombination, quantity, addedFromProductPage, callerElement);
	}

	// override ajax.remove function
	var ajaxCartRemoveFunc = ajaxCart.remove;
	ajaxCart.remove = function(idProduct, idCombination, customizationId, idAddressDelivery) {
		ajaxCartRemoveFunc(idProduct, idCombination, customizationId, idAddressDelivery);
		cdcGtm.removeFromCart(idProduct, idCombination);
	}
}

// Prestashop >= 1.7
else if(typeof(prestashop) != 'undefined') {
   $(document).ready(function () {
    prestashop.on(
      'updateCart',
      function (event) {
        var refreshURL = $('.blockcart').data('refresh-url');
        var requestData = {};
        console.log(event);
        if (event && event.reason) {
          requestData = {
            id_product_attribute: event.reason.idProductAttribute,
            id_product: event.reason.idProduct,
            action: event.reason.linkAction
          };

          if(requestData.action == 'add-to-cart') {
          	cdcGtm.addToCart(requestData.id_product, requestData.id_product_attribute, 1, null, null);
          } else if(requestData.action == 'delete-from-cart') {
            cdcGtm.removeFromCart(requestData.id_product, requestData.id_product_attribute, 1);
          }
        }
      }
    );
  });
}

// override deleteProductFromSummary (checkout page)
var deleteProductFromSummary = (function(id) {
    var original_deleteProductFromSummary = deleteProductFromSummary;
    return function(id) {
		var productId = 0;
		var productAttributeId = 0;
		var ids = 0;
		ids = id.split('_');
		productId = parseInt(ids[0]);
		if (typeof(ids[1]) !== 'undefined') {
			productAttributeId = parseInt(ids[1]);
		}

		var cart_qtity = parseInt($('input[name=quantity_' + id + ']').val());

        cdcGtm.removeFromCart(productId, productAttributeId, cart_qtity);
        original_deleteProductFromSummary(id);
    }
})();

// override downQuantity (checkout page)
var downQuantity = (function(id, qty) {
    var original_downQuantity = downQuantity;
    return function(id, qty) {
		var productId = 0;
		var productAttributeId = 0;
		var ids = 0;
		ids = id.split('_');
		productId = parseInt(ids[0]);
		if (typeof(ids[1]) !== 'undefined') {
			productAttributeId = parseInt(ids[1]);
		}

		// qty
		var val = $('input[name=quantity_' + id + ']').val();
		var newVal = val;
		if(typeof(qty) == 'undefined' || !qty)
		{
			new_qty = 1;
			newVal = val - 1;
		}
		else if (qty < 0)
			new_qty = -qty;

		// if qtity is > 0, decrease qtity, if qtity = 0, it will be handled by "deleteProductFromSummary"
		if(newVal > 0) {
        	cdcGtm.removeFromCart(productId, productAttributeId, new_qty);
		}

        original_downQuantity(id, qty);
    }
})();

