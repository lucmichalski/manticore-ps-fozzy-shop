<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
/*
 *
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockguestbook
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

function upgrade_module_1_2_2($module)
{


    $name_module = "blockguestbook";
    Configuration::updateValue($name_module.'BGCOLOR_G', '#fafafa');

    Configuration::updateValue($name_module.'is_webg', 1);
    Configuration::updateValue($name_module.'is_companyg', 1);
    Configuration::updateValue($name_module.'is_addrg', 1);

    Configuration::updateValue($name_module.'is_countryg', 1);
    Configuration::updateValue($name_module.'is_cityg', 1);

    Configuration::updateValue($name_module.'perpageg', 5);
    Configuration::updateValue($name_module.'notig', 1);
    Configuration::updateValue($name_module.'mailg', @Configuration::get($name_module.'mail'));
    Configuration::updateValue($name_module.'gbook_blc', 5);

    Configuration::updateValue($name_module.'is_captchag', 1);

    Configuration::updateValue($name_module.'n_rssitemsg', 10);
    Configuration::updateValue($name_module.'rssong', 1);

	// recreate tabs

    $tab_id = Tab::getIdFromClassName("AdminGuestbook");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminGuestbooks");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }


    $module->createAdminTabs();

    // recreate tabs



    // add routes only if prestashop > 1.6
    if(version_compare(_PS_VERSION_, '1.6', '>')){
        $module->registerHook('ModuleRoutes');
        $module->registerHook('DisplayBackOfficeHeader');
    }


    $module->createFolderAndSetPermissions();



    ### add field avatar in ps_blockguestbook table ####

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('avatar', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `avatar` text')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('response', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `response` text')) {
                return false;
            }

        }
    }


    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('is_show', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `is_show` int(11) NOT NULL default \'0\'')) {
                return false;
            }

        }
    }


    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('web', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `web` varchar(500) default NULL')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('company', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `company` varchar(500) default NULL')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('address', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `address` varchar(500) default NULL')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('country', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `country` varchar(500) default NULL')) {
                return false;
            }

        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'blockguestbook`');
    if (is_array($list_fields))
    {
        foreach ($list_fields as $k => $field)
            $list_fields[$k] = $field['Field'];
        if (!in_array('city', $list_fields)) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'blockguestbook` ADD `city` varchar(500) default NULL')) {
                return false;
            }

        }
    }

    return true;
}
?>