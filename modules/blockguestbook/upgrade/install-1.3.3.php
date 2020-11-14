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

function upgrade_module_1_3_3($module)
{


    // GDPR
    $module->registerHook('registerGDPRConsent');
    $module->registerHook('actionDeleteGDPRCustomer');
    $module->registerHook('actionExportGDPRData');
    // GDPR

    return true;
}
?>