<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2019 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_0($module)
{
    if ($module->is_17) {
        $module->myDeleteModuleTabs();
        $tabs = $module->tabs;
        foreach ($tabs as $tab) {
            $tabName = isset($tab['name'][Context::getContext()->language->iso_code]) ? $tab['name'][Context::getContext()->language->iso_code] : $tab['name']['en'];
            $module->myInstallModuleTab($tabName, $tab['class_name'], Tab::getIdFromClassName($tab['parent_class_name']));
        }
    }
    
    return true;
}
