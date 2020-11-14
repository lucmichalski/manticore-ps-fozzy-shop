<?php
error_reporting(E_ALL ^ E_NOTICE);
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$cart_id = (int)$_POST['cart_id']; if (empty($cart_id)) return false;
$admin = (int)$_POST['admin'];
$empl = (int)Tools::GetValue('employee');
$timeofdelivery = $_POST['timeofdelivery']; if (empty($timeofdelivery)) return false;

$date_period = explode ('_', $timeofdelivery);
$period = (int)$date_period[1];

$date = explode ('.', $date_period[0]);
$date = $date[2]."-".$date[1]."-".$date[0];

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
Db::getInstance()->execute($sql_insert);

if ($admin) {

$sql_upd_order = "UPDATE `"._DB_PREFIX_."orders` SET `delivery_date` = '$date' WHERE `id_cart` = $cart_id";
Db::getInstance()->execute($sql_upd_order);
$sql_order = "SELECT `id_order`, `current_state`, `fiskal`, `id_prihod` FROM `"._DB_PREFIX_."orders` WHERE `id_cart` = $cart_id LIMIT 1";
$order = Db::getInstance()->executeS($sql_order);
$id_order = (int)$order[0]['id_order'];
$current_state = (int)$order[0]['current_state'];
$fiskal = (float)$order[0]['fiskal'];
$id_prihod = (int)$order[0]['id_prihod'];
$sql_upd_del = "UPDATE `"._DB_PREFIX_."order_invoice` SET `delivery_date` = '$date' WHERE `id_order` = $id_order";
Db::getInstance()->execute($sql_upd_del);
$sql_upd_del2 = "UPDATE `"._DB_PREFIX_."order_carrier` SET `date_add` = '$date' WHERE `id_order` = $id_order";
Db::getInstance()->execute($sql_upd_del2);
//admin logic fozzy
//$sql_upd_log = "UPDATE `"._DB_PREFIX_."fozzy_logistic` SET `dtd_upd` = '$date' WHERE `id_order` = $id_order";
//Db::getInstance()->execute($sql_upd_log);

$sql_upd_orr = "UPDATE `"._DB_PREFIX_."orders` SET `dateofdelivery` = '".$date."', `period` = ".$period."  WHERE `id_cart` = $cart_id";
Db::getInstance()->execute($sql_upd_orr);

$id_order_states = Db::getInstance()->executeS('
        SELECT `id_order_state`
        FROM `' . _DB_PREFIX_ . 'order_history`
        WHERE `id_order` = ' . (int)$id_order . '
        ORDER BY `id_order_history` DESC');
$zakaz_states = array();
foreach ($id_order_states as $id_order_state) 
  {
    $zakaz_states[]=(int)$id_order_state['id_order_state'];
  }
if ($current_state != 939)
{
if (in_array(16, $zakaz_states) && $current_state == 932 && $fiskal > 0) //Если в заказе был статус Готов к отгрузке и текущий статус Запрос переноса и был чек
  {
   $sql_upd_pr = "UPDATE `"._DB_PREFIX_."fozzy_kassa_prihod` SET `perenos` = 1 WHERE `id_prihod` = ".$id_prihod;
   Db::getInstance()->execute($sql_upd_pr);
   $history = new OrderHistory();
   $history->id_order = $id_order;
   $history->id_employee = $empl;
   $history->changeIdOrderState(16, $id_order);  //Статус - Готов к отгрузке
   $history->add();
  } 
else if (!in_array(16, $zakaz_states) && $current_state == 932 && $fiskal == 0) //Если в заказе не было статуса Готов к отгрузке и текущий статус Запрос переноса и нет чека
  {
   $history = new OrderHistory();
   $history->id_order = $id_order;
   $history->id_employee = $empl;
   $history->changeIdOrderState((int)$id_order_states[1]['id_order_state'], $id_order);  //Статус - Статус перед запросом переноса
   $history->add();
  }
else if (!in_array(16, $zakaz_states) && $current_state != 932 && $fiskal == 0) //Если в заказе не было статуса Готов к отгрузке и текущий статус не Запрос переноса и нет чека
  {
   $history = new OrderHistory();
   $history->id_order = $id_order;
   $history->id_employee = $empl;
   $history->changeIdOrderState(908, $id_order);  //Статус - Сброс заказа с ТСД
   $history->add();
   $history = new OrderHistory();
   $history->id_order = $id_order;
   $history->id_employee = $empl;
   $history->changeIdOrderState((int)$id_order_states[0]['id_order_state'], $id_order);  //Статус - Возвращаем текущий
   $history->add();
  }
}

$mess = 'Изменение даты и времени доставки';
$sql_log = "INSERT INTO `ps_log`(`severity`, `error_code`, `message`, `object_type`, `object_id`, `id_employee`, `date_add`, `date_upd`) VALUES (1,0,'$mess','order',".$id_order.",".$empl.",NOW(),NOW())";
Db::getInstance()->execute($sql_log);
return true;
}

return false;
?>