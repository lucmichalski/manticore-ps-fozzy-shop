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

/**
 * Таблица созданных акций, учет созданных акций.
 * The table of created shares, accounting for created shares.
 */
$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "promotions_rules` (
  `id_promotions_rules` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_rule` int(11) UNSIGNED NOT NULL,
  `priority_rule` int(11) NOT NULL,
  `code_rule` varchar(256) NOT NULL,
  `count_rule` int(11) NOT NULL,
  `date_from` varchar(256) NOT NULL,
  `date_to` varchar(256) NOT NULL,
  `delivery_block` int(11) NOT NULL,
  `free_shipping` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,                                            
  PRIMARY KEY (`id_promotions_rules`)                                                         
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

/**
 * Таблица переводов модуля.
 * Module translation table.
 */
$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "promotions_rule_lang` (
  `id_promotions_rules` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `title_rule` varchar(256) NOT NULL,                                           
  PRIMARY KEY (`id_promotions_rules`,`id_lang`)                                                   
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}