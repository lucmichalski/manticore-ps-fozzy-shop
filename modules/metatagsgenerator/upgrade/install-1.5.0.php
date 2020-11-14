<?php
/**
* 2007-2017 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2017 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

function upgrade_module_1_5_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $module_obj->registerHook('actionObjectAddAfter');
    $module_obj->registerHook('actionObjectUpdateAfter');

    $local_path = _PS_MODULE_DIR_.$module_obj->name;
    $files_to_delete = glob($local_path.'/saved_patterns/*');
    $pattern_saved = false;
    foreach ($files_to_delete as $file) {
        if (Tools::substr($file, -4) == '.txt' && !$pattern_saved && $pattern_data = Tools::file_get_contents($file)) {
            $pattern_data = Tools::jsonDecode($pattern_data, true);
            foreach ($pattern_data as $id_lang => $pattern) {
                foreach ($pattern as $resource_type => $data) {
                    foreach ($data as $meta_name => $p) {
                        if (!isset($p['active'])) {
                            $pattern_data[$id_lang][$resource_type][$meta_name]['active'] = 1;
                        }
                    }
                }
            }
            $pattern_data = Tools::jsonEncode($pattern_data);
            $pattern_saved = Configuration::updateGlobalValue('MTG_SAVED_PATTERNS', $pattern_data);
        }
        unlink($file);
    }
    rmdir($local_path.'/saved_patterns/');
    return true;
}
