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

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fozzy_logistic` (
  `id_logic` int(20) NOT NULL AUTO_INCREMENT,                                         
  `id_order` int(20) NOT NULL,                                                        
  `mest` int(20) NOT NULL DEFAULT '0',                                                
  `fiskal` decimal(20,2) NOT NULL DEFAULT '0.00',                                     
  `norm` tinyint(1) NOT NULL DEFAULT '0',                                             
  `ice` tinyint(1) NOT NULL DEFAULT '0',                                              
  `fresh` tinyint(1) NOT NULL DEFAULT '0',                                            
  `hot` tinyint(1) NOT NULL DEFAULT '0',                                              
  `id_sborshik` int(10) NOT NULL DEFAULT '0',                                         
  `id_vodila` int(10) NOT NULL DEFAULT '0',                                           
  `start_sborki` datetime DEFAULT NULL,                                               
  `stop_sborki` datetime DEFAULT NULL,                                                
  `Pos_Id` int(3) DEFAULT NULL,                                                       
  `QtyW` varchar(128) DEFAULT NULL,                                                   
  `Time_Arrival` varchar(128) DEFAULT NULL,                                           
  `distance` int(15) DEFAULT NULL,                                                    
  `Unload_Time` int(3) DEFAULT NULL,                                                  
  `Route_Num` int(10) DEFAULT NULL,                                                   
  `end_route` varchar(20) DEFAULT NULL,                                               
  `longs` decimal(20,3) DEFAULT NULL,                                                 
  `Travel_Duration` int(10) DEFAULT NULL,                                             
  `dtd_upd` datetime NOT NULL DEFAULT '2001-01-01 00:00:00',                          
  `cartnum` int(20) DEFAULT NULL,                                                     
  PRIMARY KEY (`id_logic`),                                                         
  KEY `id_order` (`id_order`),
  KEY `id_logic` (`id_logic`,`id_order`,`mest`,`fiskal`,`norm`,`ice`,`fresh`,`hot`,`id_sborshik`,`id_vodila`,`start_sborki`,`stop_sborki`,`cartnum`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
 
$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fozzy_logistic_sborshik` (
  `id_sborshik` int(10) NOT NULL AUTO_INCREMENT,                                   
  `tabnum` varchar(20) NOT NULL DEFAULT '0',                                       
  `INN` varchar(30) DEFAULT NULL,                                                  
  `fio` text NOT NULL,                                                             
  `phone` varchar(255) DEFAULT NULL,                                               
  `active` tinyint(1) NOT NULL DEFAULT '1',                                        
  `id_shop` int(10) NOT NULL DEFAULT '1',
  `employment` double NOT NULL,
  `add_date` date,
  `delete_date` date,                                          
  `deleted` tinyint(1) NOT NULL DEFAULT '0',                                       
  PRIMARY KEY (`id_sborshik`,`tabnum`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fozzy_logistic_packer` (
  `id_packer` int(10) NOT NULL AUTO_INCREMENT,                                   
  `tabnum` varchar(20) NOT NULL DEFAULT '0',                                       
  `INN` varchar(30) DEFAULT NULL,                                                  
  `fio` text NOT NULL,                                                             
  `phone` varchar(255) DEFAULT NULL,                                               
  `active` tinyint(1) NOT NULL DEFAULT '1',                                        
  `id_shop` int(10) NOT NULL DEFAULT '1',
  `employment` double NOT NULL,
  `add_date` date,
  `delete_date` date,                                          
  `deleted` tinyint(1) NOT NULL DEFAULT '0',                                       
  PRIMARY KEY (`id_packer`,`tabnum`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fozzy_logistic_vodila` (
  `id_vodila` int(10) NOT NULL AUTO_INCREMENT,
  `tabnum` int(20) NOT NULL DEFAULT '0',                                      
  `INN` varchar(30) DEFAULT NULL,                                                   
  `fio` text NOT NULL,                                                                  
  `phone` varchar(255) NOT NULL,                                                        
  `active` tinyint(1) NOT NULL DEFAULT '1',                                             
  `id_shop` int(10) NOT NULL DEFAULT '1',                                               
  `driver_id` int(10) DEFAULT NULL,
  `employment` double NOT NULL,
  `add_date` date,
  `delete_date` date,                                                     
  `deleted` tinyint(1) NOT NULL DEFAULT '0',                                            
  PRIMARY KEY (`id_vodila`,`tabnum`)                                                         
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fozzy_logistic_manager` (
  `id_manager` int(10) NOT NULL AUTO_INCREMENT,                                   
  `tabnum` varchar(20) NOT NULL DEFAULT '0',                                       
  `INN` varchar(30) DEFAULT NULL,                                                  
  `fio` text NOT NULL,                                                             
  `phone` varchar(255) DEFAULT NULL,                                               
  `active` tinyint(1) NOT NULL DEFAULT '1',                                        
  `id_shop` int(10) NOT NULL DEFAULT '1',
  `employment` double NOT NULL,
  `add_date` date,
  `delete_date` date,                                          
  `deleted` tinyint(1) NOT NULL DEFAULT '0',                                       
  PRIMARY KEY (`id_manager`,`tabnum`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fozzy_logistic_role` (
  `id_role` int(10) NOT NULL AUTO_INCREMENT,                                   
  `role_name` varchar(255) DEFAULT NULL,                                       
  PRIMARY KEY (`id_role`,`role_name`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fozzy_logistic_shop` (
  `id_shop` int(11) NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

Db::getInstance()->insert('fozzy_logistic_role', array('id_role' => '1', 'role_name' => 'Сборщик'));
Db::getInstance()->insert('fozzy_logistic_role', array('id_role' => '2', 'role_name' => 'Упаковщик'));
Db::getInstance()->insert('fozzy_logistic_role', array('id_role' => '3', 'role_name' => 'Водитель'));
Db::getInstance()->insert('fozzy_logistic_role', array('id_role' => '4', 'role_name' => 'Старший менеджер'));

Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '25', 'shop_name' => 'Петрівка'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '1', 'shop_name' => 'Заболотного'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '30', 'shop_name' => 'Проліски'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '2', 'shop_name' => 'Одеса'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '3', 'shop_name' => 'Дніпро'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '4', 'shop_name' => 'Харків'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '8', 'shop_name' => 'Рівне'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '9', 'shop_name' => 'Кременчуг'));
Db::getInstance()->insert('fozzy_logistic_shop', array('id_shop' => '40', 'shop_name' => ' '));