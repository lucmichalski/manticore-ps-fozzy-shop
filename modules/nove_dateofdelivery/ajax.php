<?php
error_reporting(E_ALL ^ E_NOTICE);
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$cart_id = (int)$_POST['cart_id']; if (empty($cart_id)) return;
$admin = (int)$_POST['admin'];
$timeofdelivery = $_POST['timeofdelivery']; if (empty($timeofdelivery)) return;

$date_period = explode ('_', $timeofdelivery);
$period = (int)$date_period[1];

$date = explode ('.', $date_period[0]);
$date = $date[2]."-".$date[1]."-".$date[0];
/*
$sql_select = "SELECT * FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$cart_id;
$cart = Db::getInstance()->executeS($sql_select);

if (count($cart) > 0)
  {
   $sql_insert = "UPDATE `"._DB_PREFIX_."nove_dateofdelivery_cart` SET `dateofdelivery` = '$date', `period` = $period  WHERE `cart_id` = $cart_id";
  }
else
  {
   $sql_insert = "INSERT INTO `"._DB_PREFIX_."nove_dateofdelivery_cart` (`cart_id`, `dateofdelivery`, `period`) VALUES ($cart_id, '$date', $period)";
  }
*/
$sql_insert = "REPLACE `"._DB_PREFIX_."nove_dateofdelivery_cart` (`cart_id`, `dateofdelivery`, `period`) VALUES ($cart_id, '$date', $period)";
$add_row = Db::getInstance()->execute($sql_insert);

if ($admin) {
$sql_upd_order = "UPDATE `"._DB_PREFIX_."orders` SET `delivery_date` = '$date' WHERE `id_cart` = $cart_id";
Db::getInstance()->execute($sql_upd_order);
$sql_order = "SELECT `id_order` FROM `"._DB_PREFIX_."orders` WHERE `id_cart` = $cart_id LIMIT 1";
$order = Db::getInstance()->executeS($sql_order);
$id_order = (int)$order[0]['id_order'];
$sql_upd_del = "UPDATE `"._DB_PREFIX_."order_invoice` SET `delivery_date` = '$date' WHERE `id_order` = $id_order";
Db::getInstance()->execute($sql_upd_del);
$sql_upd_del2 = "UPDATE `"._DB_PREFIX_."order_carrier` SET `date_add` = '$date' WHERE `id_order` = $id_order";
Db::getInstance()->execute($sql_upd_del2);
//admin logic fozzy
$sql_upd_log = "UPDATE `"._DB_PREFIX_."fozzy_logistic` SET `dtd_upd` = '$date' WHERE `id_order` = $id_order";
Db::getInstance()->execute($sql_upd_log);
return true;
}
if ($add_row)
  {
   echo 1;
  }
else
  {
   echo 0;
  }

?>