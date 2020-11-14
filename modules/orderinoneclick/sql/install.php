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

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_order` (
    `id_sm_ooc_order` INT(10) unsigned NOT NULL auto_increment,
    `id_sm_ooc_group` INT(10) unsigned NOT NULL,
    `id_sm_ooc_cart` INT(10) unsigned NOT NULL,
    `id_customer` INT(10) unsigned NULL,
    `id_guest` INT(10) unsigned NULL,
    `id_shop` INT(10) unsigned NULL,
    `date` DATETIME NOT NULL,
    `comment` MEDIUMTEXT DEFAULT NULL,
    PRIMARY KEY (`id_sm_ooc_order`),
    KEY (`id_shop`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_fields` (
    `id_sm_ooc_fields` INT(10) unsigned NOT NULL auto_increment,
    `id_sm_ooc_order` INT(10) unsigned NOT NULL,
    `id_sm_ooc_order_fields` INT(10) unsigned NOT NULL,
    `value` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_fields`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_cart` (
    `id_sm_ooc_cart` INT(10) unsigned NOT NULL auto_increment,
    `id_currency` INT(10) unsigned NULL,
    `order_price` DECIMAL(20,6) NOT NULL,
    `total_discount` DECIMAL(20,6) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_cart`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_cart_product` (
    `id_sm_ooc_cart_product` INT(10) unsigned NOT NULL auto_increment,
    `id_sm_ooc_cart` INT(10) unsigned NOT NULL,
    `id_product` INT(10) unsigned NOT NULL,
    `id_product_attribute` INT(10) unsigned NOT NULL,
    `quantity` INT(10) unsigned NOT NULL,
    `price` DECIMAL(20,6) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_cart_product`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_customization` (
    `id_sm_ooc_customization` INT(10) unsigned NOT NULL auto_increment,
    `id_sm_ooc_cart_product` INT(10) unsigned NOT NULL,
    `customization_type` INT(10) unsigned NOT NULL,
    `customization_value` VARCHAR(255) NULL,
    PRIMARY KEY (`id_sm_ooc_customization`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_group` (
    `id_sm_ooc_group` INT(10) unsigned NOT NULL auto_increment,
    `name` VARCHAR(25) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_group`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_cart_voucher` (
    `id_sm_ooc_cart_voucher` INT(10) unsigned NOT NULL auto_increment,
    `id_sm_ooc_cart` INT(10) unsigned NOT NULL,
    `id_voucher` INT(10) unsigned NOT NULL,
    PRIMARY KEY (`id_sm_ooc_cart_voucher`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_order_fields` (
    `id_sm_ooc_order_fields` INT(10) unsigned NOT NULL auto_increment,
    `required` bool NOT NULL DEFAULT false,
    `active` bool NOT NULL DEFAULT false,
    `id_shop` INT(10) unsigned NULL,
    `position` INT(10) unsigned NULL,
    `id_sm_ooc_type_order_field` INT(10) unsigned NULL,
    PRIMARY KEY (`id_sm_ooc_order_fields`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_order_fields_lang` (
    `id_sm_ooc_order_fields` INT(10) unsigned NOT NULL auto_increment,
    `id_lang` INT(10) unsigned NOT NULL,
    `name` VARCHAR(60) NOT NULL,
    `description` VARCHAR(160) NOT NULL,
    `tip` VARCHAR(160) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_order_fields`,`id_lang`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_type_order_field` (
    `id_sm_ooc_type_order_field` INT(10) unsigned NOT NULL auto_increment,
    `name` VARCHAR(60) NOT NULL,
    `validate_func` VARCHAR(60) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_type_order_field`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_user_field_corr` (
    `id_sm_ooc_user_field_corr` INT(10) unsigned NOT NULL auto_increment,
    `id_sm_ooc_type_order_field` INT(10) unsigned NOT NULL,
    `corr` VARCHAR(60) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_user_field_corr`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sm_ooc_titles` (
    `id_sm_ooc_titles` INT(10) unsigned NOT NULL auto_increment,
    `id_shop` INT(10) unsigned NOT NULL,
    `id_lang` INT(10) unsigned NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_sm_ooc_titles`),
    KEY (`id_shop`, `id_lang`))
ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

foreach ($queries as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
