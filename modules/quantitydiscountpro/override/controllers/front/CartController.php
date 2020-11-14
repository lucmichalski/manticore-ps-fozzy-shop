<?php
/**
* Quantity Discount Pro
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2019 idnovate.com
*  @license   See above
*/

class CartController extends CartControllerCore
{
    protected function updateCart()
    {
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            // Update the cart ONLY if $this->cookies are available, in order to avoid ghost carts created by bots
            if ($this->context->cookie->exists() && !$this->errors && !($this->context->customer->isLogged() && !$this->isTokenValid())) {
                if (Tools::getIsset('add') || Tools::getIsset('update')) {
                    $this->processChangeProductInCart();
                } elseif (Tools::getIsset('delete')) {
                    $this->processDeleteProductInCart();
                } elseif (CartRule::isFeatureActive()) {
                    if (Tools::getIsset('addDiscount') || Tools::getIsset('searchcoupon')) {
                        if (!($code = trim((Tools::getValue('discount_name') ? Tools::getValue('discount_name') : Tools::getValue('coupon'))))) {
                            $this->errors[] = $this->trans('You must enter a voucher code.', array(), 'Shop.Notifications.Error');
                        } elseif (!Validate::isCleanHtml($code)) {
                            $this->errors[] = $this->trans('The voucher code is invalid.', array(), 'Shop.Notifications.Error');
                        } else {
                            $quantityDiscount = new QuantityDiscountRule();
                            if (($quantityDiscount = new quantityDiscountRule(QuantityDiscountRule::getQuantityDiscountRuleByCode($code))) && Validate::isLoadedObject($quantityDiscount)) {
                                // Add valid rule with discount code
                                if ($quantityDiscount->createAndRemoveRules($code) !== true) {
                                    $this->errors[] = $this->trans('The voucher code is invalid.', array(), 'Shop.Notifications.Error');
                                }
                            } elseif (($cartRule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cartRule)) {
                                //Check if user is trying to manually add a coupon generated automatically
                                if ($quantityDiscount->cartRuleGeneratedByAQuantityDiscountRuleCode($code)) {
                                    $this->errors[] = $this->trans('The voucher code is invalid.', array(), 'Shop.Notifications.Error');
                                } elseif ($error = $cartRule->checkValidity($this->context, false, true)) {
                                    $this->errors[] = $error;
                                } else {
                                    $this->context->cart->addCartRule($cartRule->id);
                                }
                            } else {
                                $this->errors[] = $this->trans('This voucher does not exist.', array(), 'Shop.Notifications.Error');
                            }
                        }
                    } elseif (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule)) {
                        //Remove cart rule. It'll be aded later with CartRule::autoAddToCart() if applicable
                        if (!QuantityDiscountRule::removeQuantityDiscountCartRule($id_cart_rule, (int)$this->context->cart->id)) {
                            $this->context->cart->removeCartRule($id_cart_rule);
                        }
                        CartRule::autoAddToCart($this->context);
                    }
                }
            } elseif (!$this->isTokenValid() && Tools::getValue('action') !== 'show' && !Tools::getValue('ajax')) {
                Tools::redirect('index.php');
            }
        } else {
            parent::updateCart();
        }
    }

    /*
    protected function processChangeProductInCart()
    {
        $mode = (Tools::getIsset('update') && $this->id_product) ? 'update' : 'add';
        $ErrorKey = ('update' === $mode) ? 'updateOperationError' : 'errors';

        if (Tools::getIsset('group')) {
            $this->id_product_attribute = (int) Product::getIdProductAttributeByIdAttributes(
                $this->id_product,
                Tools::getValue('group')
            );
        }

        if ($this->qty == 0) {
            $this->{$ErrorKey}[] = $this->trans(
                'Null quantity.',
                array(),
                'Shop.Notifications.Error'
            );
        } elseif (!$this->id_product) {
            $this->{$ErrorKey}[] = $this->trans(
                'Product not found',
                array(),
                'Shop.Notifications.Error'
            );
        }

        $product = new Product($this->id_product, true, $this->context->language->id);
        if (!$product->id || !$product->active || !$product->checkAccess($this->context->cart->id_customer)) {
            $this->{$ErrorKey}[] = $this->trans(
                'This product (%product%) is no longer available.',
                array('%product%' => $product->name),
                'Shop.Notifications.Error'
            );

            return;
        }

        if (!$this->id_product_attribute && $product->hasAttributes()) {
            $minimum_quantity = ($product->out_of_stock == 2)
                ? !Configuration::get('PS_ORDER_OUT_OF_STOCK')
                : !$product->out_of_stock;
            $this->id_product_attribute = Product::getDefaultAttribute($product->id, $minimum_quantity);
            // @todo do something better than a redirect admin !!
            if (!$this->id_product_attribute) {
                Tools::redirectAdmin($this->context->link->getProductLink($product));
            }
        }

        $qty_to_check = $this->qty;
        $cart_products = $this->context->cart->getProducts();

        if (is_array($cart_products)) {
            foreach ($cart_products as $cart_product) {
                if ($this->productInCartMatchesCriteria($cart_product)) {
                    $qty_to_check = $cart_product['cart_quantity'];

                    if (Tools::getValue('op', 'up') == 'down') {
                        $qty_to_check -= $this->qty;
                    } else {
                        $qty_to_check += $this->qty;
                    }

                    break;
                }
            }
        }

        // Check product quantity availability
        if ('update' !== $mode && $this->shouldAvailabilityErrorBeRaised($product, $qty_to_check)) {
            $this->{$ErrorKey}[] = $this->trans(
                'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                array('%product%' => $product->name),
                'Shop.Notifications.Error'
            );
        }

        // Check minimal_quantity
        if (!$this->id_product_attribute) {
            if ($qty_to_check < $product->minimal_quantity) {
                $this->errors[] = $this->trans(
                     'The minimum purchase order quantity for the product %product% is %quantity%.',
                     array('%product%' => $product->name, '%quantity%' => $product->minimal_quantity),
                     'Shop.Notifications.Error'
                 );

                return;
            }
        } else {
            $combination = new Combination($this->id_product_attribute);
            if ($qty_to_check < $combination->minimal_quantity) {
                $this->errors[] = $this->trans(
                     'The minimum purchase order quantity for the product %product% is %quantity%.',
                     array('%product%' => $product->name, '%quantity%' => $combination->minimal_quantity),
                     'Shop.Notifications.Error'
                 );

                return;
            }
        }

        // If no errors, process product addition
        if (!$this->errors) {
            // Add cart if no cart found
            if (!$this->context->cart->id) {
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                }
            }

            // Check customizable fields

            if (!$product->hasAllRequiredCustomizableFields() && !$this->customization_id) {
                $this->{$ErrorKey}[] = $this->trans(
                    'Please fill in all of the required fields, and then save your customizations.',
                    array(),
                    'Shop.Notifications.Error'
                );
            }

            if (!$this->errors) {
                $cart_rules = $this->context->cart->getCartRules();
                $update_quantity = $this->context->cart->updateQty(
                    $this->qty,
                    $this->id_product,
                    $this->id_product_attribute,
                    $this->customization_id,
                    Tools::getValue('op', 'up'),
                    $this->id_address_delivery,
                    null,
                    true,
                    true
                );
                $available_cart_rules = CartRule::getCustomerCartRules(
                    $this->context->language->id,
                    (isset($this->context->customer->id) ? $this->context->customer->id : 0),
                    true,
                    true,
                    true,
                    $this->context->cart,
                    false,
                    true
                );
                if ($update_quantity < 0) {
                    // If product has attribute, minimal quantity is set with minimal quantity of attribute
                    $minimal_quantity = ($this->id_product_attribute)
                        ? Attribute::getAttributeMinimalQty($this->id_product_attribute)
                        : $product->minimal_quantity;
                    $this->{$ErrorKey}[] = $this->trans(
                        'You must add %quantity% minimum quantity',
                        array('%quantity%' => $minimal_quantity),
                        'Shop.Notifications.Error'
                    );
                } elseif (!$update_quantity) {
                    $this->errors[] = $this->trans(
                        'You already have the maximum quantity available for this product.',
                        array(),
                        'Shop.Notifications.Error'
                    );
                } elseif ($this->shouldAvailabilityErrorBeRaised($product, $qty_to_check)) {
                    // check quantity after cart quantity update
                    $this->{$ErrorKey}[] = $this->trans(
                        'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                        array('%product%' => $product->name),
                        'Shop.Notifications.Error'
                    );
                }
            }
        }

        $removed = CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
    }
    */
}
