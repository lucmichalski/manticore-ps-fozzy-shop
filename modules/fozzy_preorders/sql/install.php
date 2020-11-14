<?php
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fozzy_preorders_vendorcode` (
  `id_vendorcode` int(11) NOT NULL AUTO_INCREMENT,
  `vendorcode` int(11) NOT NULL,
  `name_product` text NOT NULL,
  `unit_product` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `comment_vendorcode` text NOT NULL,
  PRIMARY KEY  (`id_vendorcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$sql[] .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fozzy_preorders_settings` (
  `id_email` int(11) NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$sql[] .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fozzy_preorders_zone` (
  `id_shop` int(11) NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '4', 'shop_name' => 'Петрівка'));
Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '5', 'shop_name' => 'Заболотного'));
Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '6', 'shop_name' => 'Проліски'));
Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '200', 'shop_name' => 'Одеса'));
Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '300', 'shop_name' => 'Дніпро'));
Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '400', 'shop_name' => 'Харків'));
Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '500', 'shop_name' => 'Рівне'));
Db::getInstance()->insert('fozzy_preorders_zone', array('id_shop' => '600', 'shop_name' => 'Кременчуг'));