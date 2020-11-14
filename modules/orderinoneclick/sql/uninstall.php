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

$queries = array();

$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_order`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_fields`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_cart`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_cart_product`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_customization`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_group`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_cart_voucher`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_order_fields`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_order_fields_lang`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_type_order_field`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_user_field_corr`';
$queries[] = 'DROP TABLE IF EXISTS`'._DB_PREFIX_.'sm_ooc_titles`';

foreach ($queries as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
