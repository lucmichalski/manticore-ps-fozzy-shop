<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2018 Elcommerce
 * @license   Closed
 * @category  PrestaShop
 * @category  Module
 */


$sql = array();

//$sql[] = "DROP TABLE IF EXISTS `"._DB_PREFIX_.$this->name."`";
//$sql[] = "DROP TABLE IF EXISTS `"._DB_PREFIX_.$this->name."_custom`";
//$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer`  DROP `phone`';
//$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer`  DROP `middlename`';
//$sql[] ='ALTER TABLE `'._DB_PREFIX_.'address`  DROP `middlename`';
foreach ($sql as $s) {
    try {
        Db::getInstance()->Execute($s);
    } catch (Exception $e) {
        //d($s);
    }
}
