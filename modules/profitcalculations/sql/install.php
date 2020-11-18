<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'profitcalculations` (
    `id_profitcalculations` int(11) NOT NULL AUTO_INCREMENT,
    `id_order` int(11),
    `type_action` varchar(255) DEFAULT NULL,
    `debit` int(11),
    `credit` int(11),
    `profit` int(11),
    `comment` varchar(255) DEFAULT NULL,
	`active` tinyint(1) NOT NULL DEFAULT \'1\',	
    `date_transaction` date,
    PRIMARY KEY  (`id_profitcalculations`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'ALTER TABLE  '. _DB_PREFIX_ .'order_carrier ADD real_shipping int(11) NOT NULL DEFAULT \'0\' AFTER tracking_number';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
