<?php
$sql = array();
$sql[] ='DROP TABLE `'._DB_PREFIX_.'fozzy_preorders_vendorcode`';
$sql[] .='DROP TABLE `'._DB_PREFIX_.'fozzy_preorders_settings`';
$sql[] .='DROP TABLE `'._DB_PREFIX_.'fozzy_preorders_zone`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}