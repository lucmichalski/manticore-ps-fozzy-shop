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

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzy_autoupd`';
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."fozzy_autoupd` (
        `id_excel` int(10) unsigned NOT NULL AUTO_INCREMENT,                                 
        `reference` varchar(32),                                                             
        `id_product` int(10) NOT NULL DEFAULT '0',                                           
        `sap_status` varchar(32),                                                            
        `id_shop` int(10) NOT NULL DEFAULT '1',                                              
        `price_rozn` decimal(20,6) NOT NULL DEFAULT '0.000000',                              
        `price_opt` decimal(20,6) NOT NULL DEFAULT '0.000000',                               
        `price_in` decimal(20,6) NOT NULL DEFAULT '0.000000',                                
        `price_old` decimal(20,6) NOT NULL DEFAULT '0.000000',                               
        `quantity` decimal(20,6) NOT NULL DEFAULT '0.00',                                    
        `on_sale` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',                                  
        `online_only` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',                              
        `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',                                   
        `available_for_order` tinyint(1) NOT NULL DEFAULT '1',                               
        `show_price` tinyint(1) NOT NULL DEFAULT '1',                                        
        `visibility` enum('both','catalog','search','none') NOT NULL DEFAULT 'both',         
        `id_category_default` int(10) UNSIGNED DEFAULT NULL,                                 
        `amount` decimal(20,2) NOT NULL DEFAULT '0.00',                                      
        `reduction_from` datetime DEFAULT NULL,                                              
        `reduction_to` datetime DEFAULT NULL,                                                
        `date_upd` datetime NOT NULL,                                                        
        PRIMARY KEY (`id_excel`),
        KEY `id_category_default` (`id_category_default`),
        KEY `date_upd` (`date_upd`),
        KEY `id_product` (`id_product`),
        UNIQUE KEY `reference` (`reference`,`id_product`,`id_shop`) 
      ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
