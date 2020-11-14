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

function upgrade_module_1_3_6($module)
{
    $name_module = 'blockfaq';



    Configuration::updateValue($name_module.'faq_spm', 1);

    if(version_compare(_PS_VERSION_, '1.6', '>')) {
        $module->registerHook('faqSPM');

    }



    return true;
}
?>