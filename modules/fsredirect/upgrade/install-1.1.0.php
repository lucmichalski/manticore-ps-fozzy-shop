<?php
/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($module)
{
    $return = true;
    $return = $return && $module->registerHook('actionObjectProductDeleteBefore');
    $return = $return && $module->registerHook('actionObjectCategoryDeleteBefore');
    $return = $return && $module->registerHook('actionObjectManufacturerDeleteBefore');
    $return = $return && $module->registerHook('actionObjectSupplierDeleteBefore');
    $return = $return && $module->registerHook('actionObjectCMSDeleteBefore');
    $return = $return && $module->registerHook('actionObjectCMSCategoryDeleteBefore');
    $return = $return && $module->registerHook('displayBackOfficeHeader');
    $return = $return && Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fsredirect_deleted` (
          `id_fsredirect_deleted` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `id_shop` int(10) unsigned NOT NULL,
          `name` varchar(255) NOT NULL,
          `url` varchar(255) NOT NULL,
          `type` varchar(255) NOT NULL,
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
          PRIMARY KEY (`id_fsredirect_deleted`),
          KEY `id_shop` (`id_shop`),
          KEY `url` (`url`)
        ) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
    $return = $return && $module->installOverrides();
    return $return;
}
