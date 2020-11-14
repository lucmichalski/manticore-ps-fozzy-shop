<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
$cart_id = (int)Tools::GetValue('cart_id'); if (empty($cart_id) || $cart_id == 0) return;
$admin = (int)Tools::GetValue('admin');
$empl = (int)Tools::GetValue('employee');
$sborshik = (int)Tools::GetValue('sborshik');
$vodila = (int)Tools::GetValue('vodila');
$kto = (int)Tools::GetValue('kto');
if ($admin) {
$sql_order = "SELECT `id_order` FROM `"._DB_PREFIX_."orders` WHERE `id_cart` = $cart_id LIMIT 1";
$order = Db::getInstance()->executeS($sql_order);
$id_order = (int)$order[0]['id_order'];
if ($kto == 1) {
  $mess = 'Изменение сборщика';
  if ($sborshik == 0) $mess = 'Удаление сборщика';
  $sql_upd_sb = "UPDATE `"._DB_PREFIX_."orders` SET `id_sborshik` = $sborshik WHERE `id_order` = $id_order";
  $sql_log = "INSERT INTO `ps_log`(`severity`, `error_code`, `message`, `object_type`, `object_id`, `id_employee`, `date_add`, `date_upd`) VALUES (1,0,'$mess','order',".$id_order.",".$empl.",NOW(),NOW())";
  Db::getInstance()->execute($sql_upd_sb);
  Db::getInstance()->execute($sql_log);
  }
if ($kto == 2) {
  $mess = 'Изменение водителя';
  if ($vodila == 0) $mess = 'Удаление водителя';
  $sql_upd_vod = "UPDATE `"._DB_PREFIX_."orders` SET `id_vodila` = $vodila WHERE `id_order` = $id_order";
  $sql_log = "INSERT INTO `ps_log`(`severity`, `error_code`, `message`, `object_type`, `object_id`, `id_employee`, `date_add`, `date_upd`) VALUES (1,0,'$mess','order',".$id_order.",".$empl.",NOW(),NOW())";
  Db::getInstance()->execute($sql_upd_vod);
  Db::getInstance()->execute($sql_log);
  }
return true;
}
return false;
?>