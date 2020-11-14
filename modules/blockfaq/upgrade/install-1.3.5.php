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
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

function upgrade_module_1_3_5($module)
{
	// recreate tabs

    $tab_id = Tab::getIdFromClassName("AdminFaq");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminFaqs");
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



    return true;
}
?>