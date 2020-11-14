<?php
/**
 * 2016 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2016 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$sql = array();
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'gshoppingfeed`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'gshoppingfeed_taxonomy`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'gshoppingfeed_custom_features`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'gshoppingfeed_custom_attributes`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
