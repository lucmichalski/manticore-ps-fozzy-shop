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

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

if (!class_exists('OOCToolsClass')) {

    class OOCToolsClass
    {
        public static function getShopName($id_shop = null)
        {
            if ($id_shop) {
                $shop = new Shop($id_shop);
                return $shop->name;
            }
            return null;
        }

        public static function notAllowOwnFields()
        {
            return
                Shop::isFeatureActive() &&
                !Configuration::get('SM_OOC_OWN_FIELDS_SETTINGS') &&
                !Context::getContext()->shop->isDefaultShop();
        }

        public static function displayCartPrice($id_sm_ooc_cart)
        {
            $backend_currency = Context::getContext()->currency->id;
            $ooc_cart = new OOCCartModel($id_sm_ooc_cart);
            $customer_currency = new Currency($ooc_cart->id_currency);
            $price = $ooc_cart->order_price;
            if (!Configuration::get('SM_OOC_SHOW_PRICE_IN_CUSTOMER_CURRENCY')
                && ($backend_currency != $ooc_cart->id_currency)
            ) {
                $price = Tools::convertPrice($ooc_cart->order_price, $customer_currency, false);
                $price = Tools::displayPrice($price);
            } else {
                $price = Tools::displayPrice($price, $customer_currency);
            }
            return $price;
        }

        public static function sendEmailToCustomer($id_oc_order = null)
        {
            if (!$id_oc_order) {
                return;
            }

            if (!$oc_order = new OOCOrderModel((int)$id_oc_order)) {
                return;
            }

            $smarty = Context::getContext()->smarty;
            $id_lang = Context::getContext()->language->id;
            $id_shop = Context::getContext()->shop->id;

            $smarty->assign(
                array(
                    'ooc_order' => $oc_order,
                    'id_lang' => $id_lang,
                )
            );

            $customer_currency = new Currency($oc_order->ooc_cart->id_currency);
            $price = $oc_order->ooc_cart->order_price;
            $order_total = Tools::displayPrice($price, $customer_currency);

            $date = $oc_order->date;

            $result = true;
            $customer_emails = $oc_order->getCustomerEmails();
            if (!empty($customer_emails)) {
                foreach ($customer_emails as $email) {
                    if (Configuration::get('SM_OOC_SEND_EMAIL_TO_CUSTOMER') && $email) {
                        $result = $result && Mail::Send(
                            $id_lang,
                            'mail_to_customer',
                            Mail::l('Quick order confirmation', $id_lang),
                            array(
                                '{name}' => $oc_order->getCustomerName(),
                                '{products}' => $smarty->fetch(
                                    dirname(__FILE__).'/../views/templates/admin/mails/products_table.tpl'
                                ),
                                '{order_total}' => $order_total,
                                '{date}' => $date,
                            ),
                            $email,
                            null,
                            null,
                            null,
                            null,
                            null,
                            dirname(__FILE__).'/../mails/',
                            false,
                            $id_shop
                        );
                    }
                }
            }
            return $result;
        }

        public static function sendEmailToAdmin($id_oc_order = null)
        {
            $admin_email = Configuration::get('SM_OOC_EMAIL_TO_ADMIN_NOTIFY');
            if (!$id_oc_order || empty($admin_email)) {
                return;
            }

            if (!$oc_order = new OOCOrderModel((int)$id_oc_order)) {
                return;
            }

            $smarty = Context::getContext()->smarty;
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
            $id_shop = Context::getContext()->shop->id;

            $smarty->assign(
                array(
                    'ooc_order' => $oc_order,
                    'id_lang' => $id_lang,
                )
            );

            $customer_currency = new Currency($oc_order->ooc_cart->id_currency);
            $price = $oc_order->ooc_cart->order_price;
            $order_total = Tools::displayPrice($price, $customer_currency);

            $customer_emails = $oc_order->getCustomerEmails();
            $email = null;
            foreach ($customer_emails as $ce) {
                $email = $email.$ce.', ';
            }

            $customer_phones = $oc_order->getCustomerPhones();
            $phone = null;
            foreach ($customer_phones as $cp) {
                $phone = $phone.$cp.', ';
            }

            $date = $oc_order->date;

            $result = Mail::Send(
                $id_lang,
                'mail_to_admin',
                Mail::l('New order!', $id_lang),
                array(
                    '{name}' => $oc_order->getCustomerName(),
                    '{products}' => $smarty->fetch(
                        dirname(__FILE__).'/../views/templates/admin/mails/products_table.tpl'
                    ),
                    '{order_total}' => $order_total,
                    '{date}' => $date,
                    '{email}' => $email,
                    '{phone}' => $phone,
                ),
                $admin_email,
                null,
                null,
                null,
                null,
                null,
                dirname(__FILE__).'/../mails/',
                false,
                $id_shop
            );

            return $result;
        }
    }
}
