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

$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "staff_schedule_picker` (
  `id_person` int(11) NOT NULL AUTO_INCREMENT,
  `week_number_picker` int(11) NOT NULL,
  `year_number_picker` int(11) NOT NULL,
  `shop_name_picker` int(11) NOT NULL,
  `fillial_name_picker` int(11) NOT NULL DEFAULT '0',
  `persone_role` int(11) NOT NULL,
  `tab_number_picker` int(11) NOT NULL,
  `picker_name_person` int(11) NOT NULL,
  `norm_day` int(11) NOT NULL,
  `start_work` varchar(256) NOT NULL,
  `end_work` varchar(256) NOT NULL,
  `time_work` int(11) NOT NULL,
  `day_pn` varchar(10) NOT NULL COMMENT 'Понедельник',
  `day_vt` varchar(10) NOT NULL COMMENT 'Вторник',
  `day_sr` varchar(10) NOT NULL COMMENT 'Среда',
  `day_cht` varchar(10) NOT NULL COMMENT 'Четверг',
  `day_pt` varchar(10) NOT NULL COMMENT 'Пятница',
  `day_sb` varchar(10) NOT NULL COMMENT 'Суббота',
  `day_vs` varchar(10) NOT NULL COMMENT 'Воскресенье',
  `week_time_all` int(11) NOT NULL,
  `week_work_day_all` int(11) NOT NULL,
  `week_output_day_all` int(11) NOT NULL,
  `comment_person` text NOT NULL,                                                     
  PRIMARY KEY (`id_person`)                                                         
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "staff_schedule_packer` (
  `id_person` int(11) NOT NULL AUTO_INCREMENT,
  `week_number_packer` int(11) NOT NULL,
  `year_number_picker` int(11) NOT NULL,
  `shop_name_packer` int(11) NOT NULL,
  `fillial_name_packer` int(11) NOT NULL DEFAULT '0',
  `persone_role` int(11) NOT NULL,
  `tab_number_packer` int(11) NOT NULL,
  `packer_name_person` varchar(256) NOT NULL,
  `norm_day` int(11) NOT NULL,
  `start_work` varchar(256) NOT NULL,
  `end_work` varchar(256) NOT NULL,
  `time_work` int(11) NOT NULL,
  `day_pn` varchar(10) NOT NULL COMMENT 'Понедельник',
  `day_vt` varchar(10) NOT NULL COMMENT 'Вторник',
  `day_sr` varchar(10) NOT NULL COMMENT 'Среда',
  `day_cht` varchar(10) NOT NULL COMMENT 'Четверг',
  `day_pt` varchar(10) NOT NULL COMMENT 'Пятница',
  `day_sb` varchar(10) NOT NULL COMMENT 'Суббота',
  `day_vs` varchar(10) NOT NULL COMMENT 'Воскресенье',
  `week_time_all` int(11) NOT NULL,
  `week_work_day_all` int(11) NOT NULL,
  `week_output_day_all` int(11) NOT NULL,
  `comment_person` text NOT NULL,                                      
  PRIMARY KEY (`id_person`)                                                         
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "staff_schedule_driver` (
  `id_person` int(11) NOT NULL AUTO_INCREMENT,
  `week_number_driver` int(11) NOT NULL,
  `year_number_picker` int(11) NOT NULL,
  `shop_name_driver` int(11) NOT NULL,
  `fillial_name_driver` int(11) NOT NULL DEFAULT '0',
  `persone_role` int(11) NOT NULL,
  `tab_number_driver` int(11) NOT NULL,
  `driver_name_person` int(11) NOT NULL,
  `norm_day` int(11) NOT NULL,
  `start_work` varchar(256) NOT NULL,
  `end_work` varchar(256) NOT NULL,
  `time_work` int(11) NOT NULL,
  `day_pn_1` varchar(11) NOT NULL,
  `day_pn_2` varchar(11) NOT NULL,
  `day_pn_3` varchar(11) NOT NULL,
  `day_vt_1` varchar(11) NOT NULL,
  `day_vt_2` varchar(11) NOT NULL,
  `day_vt_3` varchar(11) NOT NULL,
  `day_sr_1` varchar(11) NOT NULL,
  `day_sr_2` varchar(11) NOT NULL,
  `day_sr_3` varchar(11) NOT NULL,
  `day_cht_1` varchar(11) NOT NULL,
  `day_cht_2` varchar(11) NOT NULL,
  `day_cht_3` varchar(11) NOT NULL,
  `day_pt_1` varchar(11) NOT NULL,
  `day_pt_2` varchar(11) NOT NULL,
  `day_pt_3` varchar(11) NOT NULL,
  `day_sb_1` varchar(11) NOT NULL,
  `day_sb_2` varchar(11) NOT NULL,
  `day_sb_3` varchar(11) NOT NULL,
  `day_vs_1` varchar(11) NOT NULL,
  `day_vs_2` varchar(11) NOT NULL,
  `day_vs_3` varchar(11) NOT NULL,
  `week_time_all` int(11) NOT NULL,
  `week_work_day_all` int(11) NOT NULL,
  `week_output_day_all` int(11) NOT NULL,
  `comment_person` text NOT NULL,                              
  PRIMARY KEY (`id_person`)                                                         
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "staff_schedule_manager` (
  `id_person` int(11) NOT NULL AUTO_INCREMENT,
  `week_number_manager` int(11) NOT NULL,
  `year_number_picker` int(11) NOT NULL,
  `shop_name_manager` int(11) NOT NULL,
  `fillial_name_manager` int(11) NOT NULL DEFAULT '0',
  `persone_role` int(11) NOT NULL,
  `tab_number_manager` int(11) NOT NULL,
  `manager_name_person` varchar(256) NOT NULL,
  `norm_day` int(11) NOT NULL,
  `start_work` varchar(256) NOT NULL,
  `end_work` varchar(256) NOT NULL,
  `time_work` int(11) NOT NULL,
  `day_pn` varchar(10) NOT NULL COMMENT 'Понедельник',
  `day_vt` varchar(10) NOT NULL COMMENT 'Вторник',
  `day_sr` varchar(10) NOT NULL COMMENT 'Среда',
  `day_cht` varchar(10) NOT NULL COMMENT 'Четверг',
  `day_pt` varchar(10) NOT NULL COMMENT 'Пятница',
  `day_sb` varchar(10) NOT NULL COMMENT 'Суббота',
  `day_vs` varchar(10) NOT NULL COMMENT 'Воскресенье',
  `week_time_all` int(11) NOT NULL,
  `week_work_day_all` int(11) NOT NULL,
  `week_output_day_all` int(11) NOT NULL,
  `comment_person` text NOT NULL,                             
  PRIMARY KEY (`id_person`)                                                         
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}