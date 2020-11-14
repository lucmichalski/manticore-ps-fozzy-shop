<?php
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

require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');

include_once(dirname(__FILE__).'/models/OOCOrderFieldsModel.php');
include_once(dirname(__FILE__).'/models/OOCTypeOrderFieldsModel.php');
include_once(dirname(__FILE__).'/models/OOCTitlesModel.php');
include_once(dirname(__FILE__).'/models/OOCOrderModel.php');
include_once(dirname(__FILE__).'/models/OOCCartModel.php');
include_once(dirname(__FILE__).'/models/OOCCartProductModel.php');
include_once(dirname(__FILE__).'/models/OOCFieldsModel.php');
include_once(dirname(__FILE__).'/models/OOCCustomizationModel.php');
include_once(dirname(__FILE__).'/models/OOCCartVouchersModel.php');
include_once(dirname(__FILE__).'/classes/OOCToolsClass.php');

$context = Context::getContext();
$smarty = $context->smarty;

$preview = ((int)Tools::getValue('preview') == true) ? true : false;
$id_lang = $preview ? (int)Tools::getValue('id_lang') : (int)$context->language->id;
$id_shop = $preview ? (int)Tools::getValue('preview_shop_id') : (int)$context->shop->id;
$id_shop = Configuration::get('SM_OOC_OWN_FIELDS_SETTINGS') ? $id_shop : (int)Configuration::get('PS_SHOP_DEFAULT');
$id_currency = (int)$context->currency->id;
$id_customer = (int)$context->customer->id;
$id_guest = (int)$context->customer->id_guest;
$id_cart = (int)$context->cookie->id_cart;

$require_cms = (int)Configuration::get('SM_OOC_CMS_ID');

$product_button = (int)Tools::getValue('product_button');

$fields = OOCOrderFieldsModel::getFieldsByShop($id_shop);

foreach ($fields as $key => $field) {
    $field_object = new OOCOrderFieldsModel($field['id']);
    $type_field_object = new OOCTypeOrderFieldsModel($field_object->id_sm_ooc_type_order_field);
    $fields[$key]['type'] = $type_field_object->validate_func;
}

$cms_error_required = false;

$agree = false;

if (Tools::getValue('act') == 'ooc_submit') {
    parse_str(Tools::getValue('data'), $array_data);

    $agree = isset($array_data['cms_agree']) ? true : false;

    $is_valid = true;

    foreach ($fields as $key => $field) {
        $value = $array_data[$field['id']];
        $fields[$key]['value'] = $value;

        $fields[$key]['error_required'] = false;

        if ($field['required'] && empty($value)) {
            $fields[$key]['is_valid'] = false;
            $is_valid = false;
            $fields[$key]['error_required'] = true;
        } elseif (!$field['required'] && empty($value)) {
            $fields[$key]['is_valid'] = true;
        } else {
            $fields[$key]['is_valid'] = Validate::{$field['type']}($value);
            $is_valid = $is_valid && $fields[$key]['is_valid'];
        }
    }

    if (($require_cms != 0) && !$agree) {
        $is_valid = false;
        $cms_error_required = true;
    }

    if ($is_valid && $preview == true) {
        $json = array(
            'status' => 'ok',
            'message' => $smarty->fetch(dirname(__FILE__).'/views/templates/front/ooc_window_preview_ok.tpl'),
        );
        die(Tools::jsonEncode($json));
    } elseif ($is_valid) {
        $result = true;

        if ($product_button) {
            parse_str(Tools::getValue('product_data'), $product_data);
            $qty = $product_data['qty'] ? (int)$product_data['qty'] : 1;
            $id_product = (int)$product_data['id_product'];

            if (isset($product_data['group'])) {
                $id_product_attribute = (int)Product::getIdProductAttributesByIdAttributes(
                    $id_product,
                    $product_data['group'],
                    true
                );
            } else {
                $id_product_attribute = 0;
            }

            if (Configuration::get('SM_OOC_PQCHK')) {
                $available = StockAvailable::getQuantityAvailableByProduct(
                    $id_product,
                    $id_product_attribute
                );

                if ($available = 0 || $available < $qty) {
                    $message = $smarty->fetch(dirname(__FILE__).'/views/templates/front/ooc_window_not_available.tpl');
                    $json = array(
                        'status' => 'ok',
                        'message' => $message,
                    );
                    die(Tools::jsonEncode($json));
                }
            }

            $ooc_order = new OOCOrderModel();
            $ooc_order->id_customer = $id_customer;
            $ooc_order->id_guest = $id_guest;
            $ooc_order->id_sm_ooc_group = (int)Configuration::get('SM_OOC_DEFAULT_GROUP_ORDERS');
            $ooc_order->id_shop = (int)$context->shop->id;
            $ooc_order->date = date('Y-m-d H:i:s');

            $ooc_order_cart = new OOCCartModel();
            $ooc_order_cart->id_currency = $id_currency;




            $product_price = Product::getPriceStatic(
                $id_product,
                Configuration::get('PS_TAX'),
                $id_product_attribute,
                6,
                null,
                false,
                true,
                $qty,
                false,
                $id_customer,
                $id_cart
            );

            $total = $product_price*$qty;

            $discount = Product::getPriceStatic(
                $id_product,
                Configuration::get('PS_TAX'),
                $id_product_attribute,
                6,
                null,
                true,
                true,
                $qty,
                false,
                $id_customer,
                $id_cart
            );

            $discount = $discount * $qty;

            $ooc_order_cart->order_price = $total;
            $ooc_order_cart->total_discount = $discount;

            $result = $result && $ooc_order_cart->add();

            $ooc_order->id_sm_ooc_cart = $ooc_order_cart->id;
            $result = $result && $ooc_order->add();

            foreach ($fields as $field) {
                $field_object = new OOCFieldsModel();
                $field_object->id_sm_ooc_order = $ooc_order->id;
                $field_object->id_sm_ooc_order_fields = $field['id'];
                $field_object->value = $array_data[$field['id']];
                $result = $result && $field_object->add();
            }

            $ooc_cart_product = new OOCCartProductModel();
            $ooc_cart_product->id_sm_ooc_cart = $ooc_order_cart->id;
            $ooc_cart_product->id_product = $id_product;
            $ooc_cart_product->id_product_attribute = $id_product_attribute;
            $ooc_cart_product->quantity = $qty;
            $ooc_cart_product->price = $product_price;
            $result = $result && $ooc_cart_product->add();
        } else {
            $id_standart_cart = $id_cart;
            $cart = new Cart($id_standart_cart);

            $ooc_order = new OOCOrderModel();
            $ooc_order->id_customer = $id_customer;
            $ooc_order->id_guest = $id_guest;
            $ooc_order->id_sm_ooc_group = (int)Configuration::get('SM_OOC_DEFAULT_GROUP_ORDERS');
            $ooc_order->id_shop = (int)$context->shop->id;
            $ooc_order->date = date('Y-m-d H:i:s');

            $cart_products = $cart->getProducts();

            $ooc_order_cart = new OOCCartModel();
            $ooc_order_cart->id_currency = $cart->id_currency;

            $total = 0;
            foreach ($cart_products as $product) {
                $total += $product['quantity']*$product['price_wt'];
            }

            $discount = $cart->getOrderTotal((int)Configuration::get('PS_TAX'), Cart::ONLY_DISCOUNTS);

            $ooc_order_cart->order_price = $total - $discount;
            $ooc_order_cart->total_discount = $discount;

            $result = $result && $ooc_order_cart->add();

            $ooc_order->id_sm_ooc_cart = $ooc_order_cart->id;
            $result = $result && $ooc_order->add();

            foreach ($fields as $field) {
                $field_object = new OOCFieldsModel();
                $field_object->id_sm_ooc_order = $ooc_order->id;
                $field_object->id_sm_ooc_order_fields = $field['id'];
                $field_object->value = $array_data[$field['id']];
                $result = $result && $field_object->add();
            }

            $customized_datas = Product::getAllCustomizedDatas($id_cart);

            foreach ($cart_products as $product) {
                $id_product = (int)$product['id_product'];
                $id_product_attribute = (int)$product['id_product_attribute'];
                $id_address_delivery = (int)$product['id_address_delivery'];
                $customized_data = $customized_datas[$id_product][$id_product_attribute][$id_address_delivery];
                if ($customized_data) {
                    $summary_qty = $product['cart_quantity'];
                    foreach ($customized_data as $cust) {
                        $ooc_cart_product = new OOCCartProductModel();
                        $ooc_cart_product->id_sm_ooc_cart = $ooc_order_cart->id;
                        $ooc_cart_product->id_product = $product['id_product'];
                        $ooc_cart_product->id_product_attribute = $product['id_product_attribute'];
                        $ooc_cart_product->quantity = $cust['quantity'];
                        $ooc_cart_product->price = $product['price_wt'];
                        $result = $result && $ooc_cart_product->add();
                        $summary_qty = $summary_qty - $ooc_cart_product->quantity;
                        foreach ($cust['datas'][Product::CUSTOMIZE_FILE] as $cust_data) {
                            $customization = new OOCCustomizationModel();
                            $customization->id_sm_ooc_cart_product = $ooc_cart_product->id;
                            $customization->customization_type = Product::CUSTOMIZE_FILE;
                            $customization->customization_value = $cust_data['value'];
                            $result = $result && $customization->add();
                        }
                        foreach ($cust['datas'][Product::CUSTOMIZE_TEXTFIELD] as $cust_data) {
                            $customization = new OOCCustomizationModel();
                            $customization->id_sm_ooc_cart_product = $ooc_cart_product->id;
                            $customization->customization_type = Product::CUSTOMIZE_TEXTFIELD;
                            $customization->customization_value = $cust_data['value'];
                            $result = $result && $customization->add();
                        }
                    }
                    if ($summary_qty > 0) {
                        $ooc_cart_product = new OOCCartProductModel();
                        $ooc_cart_product->id_sm_ooc_cart = $ooc_order_cart->id;
                        $ooc_cart_product->id_product = $product['id_product'];
                        $ooc_cart_product->id_product_attribute = $product['id_product_attribute'];
                        $ooc_cart_product->quantity = $summary_qty;
                        $ooc_cart_product->price = $product['price_wt'];
                        $result = $result && $ooc_cart_product->add();
                    }
                } else {
                    $ooc_cart_product = new OOCCartProductModel();
                    $ooc_cart_product->id_sm_ooc_cart = $ooc_order_cart->id;
                    $ooc_cart_product->id_product = $product['id_product'];
                    $ooc_cart_product->id_product_attribute = $product['id_product_attribute'];
                    $ooc_cart_product->quantity = $product['cart_quantity'];
                    $ooc_cart_product->price = $product['price_wt'];
                    $result = $result && $ooc_cart_product->add();
                }
            }

            $vouchers = $cart->getOrderedCartRulesIds();
            if ($vouchers) {
                foreach ($vouchers as $voucher) {
                    $ooc_voucher = new OOCCartVouchersModel();
                    $ooc_voucher->id_voucher = $voucher['id_cart_rule'];
                    $ooc_voucher->id_sm_ooc_cart = $ooc_order_cart->id;
                    $ooc_voucher->add();
                }
            }
        }

        if ($result) {
            OOCToolsClass::sendEmailToCustomer($ooc_order->id);
            OOCToolsClass::sendEmailToAdmin($ooc_order->id);
            $cart->delete();
            $smarty->assign(
                array(
                    'confirm_url' => __PS_BASE_URI__,
                    'product_button' => $product_button,
                )
            );
            $message = $smarty->fetch(dirname(__FILE__).'/views/templates/front/ooc_window_confirm.tpl');
        } else {
            $message = $smarty->fetch(dirname(__FILE__).'/views/templates/front/ooc_window_error.tpl');
        }

        $json = array(
            'status' => 'ok',
            'message' => $message,
        );
        die(Tools::jsonEncode($json));
    }
}


$href_agree = '#';
if ($require_cms != 0) {
    $cmslinks = CMS::getLinks($id_lang);
    foreach ($cmslinks as $link) {
        if ($link['id_cms'] == $require_cms) {
            $href_agree = $link['link'];
        }
    }
}

$smarty->assign(
    array(
        'titles' => OOCTitlesModel::getObjectByShopAndLang($id_shop, $id_lang),
        'id_lang' => $id_lang,
        'fields' => $fields,
        'require_cms' => $require_cms,
        'href_agree' => $href_agree,
        'product_button' => $product_button,
        'cms_error_required' => $cms_error_required,
        'agree' => $agree,
    )
);

$json = array(
    'status' => 'ok',
    'message' => $smarty->fetch(dirname(__FILE__).'/views/templates/front/ooc_window.tpl'),
);
die(Tools::jsonEncode($json));
