<?php
/**
* 2007-2018 PrestaShop
*
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'nv_justin_carts` ( 
          `id` INT(10) NOT NULL AUTO_INCREMENT,
          `id_cart` INT(20) NULL DEFAULT NULL,
          `region` VARCHAR(100) NULL DEFAULT NULL,
          `town` VARCHAR(100) NULL DEFAULT NULL,
          `ware` VARCHAR(100) NULL DEFAULT NULL,
          PRIMARY KEY (`id`), INDEX `id_cart` (`id_cart`, `region`, `town`, `ware`)
          ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
          
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'nv_justin_region` ( 
          `id_region` INT(10) NOT NULL AUTO_INCREMENT,
          `uuid` VARCHAR(100) NULL DEFAULT NULL,
          `code` VARCHAR(100) NULL DEFAULT NULL,
          `descr` VARCHAR(100) NULL DEFAULT NULL,
          `SCOATOU` VARCHAR(100) NULL DEFAULT NULL,
          PRIMARY KEY (`id_region`), INDEX `uuid` (`uuid`, `code`, `SCOATOU`)
          ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
          
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'nv_justin_towns` ( 
          `id_town` INT(10) NOT NULL AUTO_INCREMENT,
          `uuid` VARCHAR(100) NULL DEFAULT NULL,
          `code` VARCHAR(100) NULL DEFAULT NULL,
          `descr` VARCHAR(100) NULL DEFAULT NULL,
          `owner_uuid` VARCHAR(100) NULL DEFAULT NULL,
          PRIMARY KEY (`id_town`), INDEX `uuid` (`uuid`, `code`, `owner_uuid`)
          ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
          
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'nv_justin_ware` ( 
          `id_ware` INT(10) NOT NULL AUTO_INCREMENT,
          `branch` VARCHAR(100) NULL DEFAULT NULL,
          `code` VARCHAR(100) NULL DEFAULT NULL,
          `descr` VARCHAR(100) NULL DEFAULT NULL,
          `owner_uuid` VARCHAR(100) NULL DEFAULT NULL,
          PRIMARY KEY (`id_ware`), INDEX `branch` (`branch`, `code`, `owner_uuid`)
          ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
          
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
