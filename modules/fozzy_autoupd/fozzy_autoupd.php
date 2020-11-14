<?php
if (!defined('_PS_VERSION_'))
	exit;

class Fozzy_autoupd extends Module
{
	public function __construct()
	{
		$this->name = 'fozzy_autoupd';
		$this->tab = 'quick_bulk_update';
		$this->version = '1.0';
		$this->author = 'Novevision.com, Britoff A.';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Обновление цен c акциями для Fozzy');
		$this->description = $this->l('Автоматическое обновление цен и акций для гипермаркета Fozzy');
	}

	public function install()
	{
		include(dirname(__FILE__).'/sql/install.php');
    return parent::install();
	}

	public function uninstall()
	{
    return parent::uninstall();
	}
  
  public function LoadPrices($filial=1614, $dateFrom='2018-12-01T09:00:00.000', $topRow=800000, $topReload=1)
  {
   ini_set('display_errors', 1);
   error_reporting(E_ALL ^ E_NOTICE);
   //$dateFrom='2019-10-30T09:00:00.000';
 //  $dateFrom='2020-03-27T07:00:00.000';
   
   if ($filial == 200)  //Получение оптовых количеств и веса
    {
     $dateFrom='2001-02-14T09:00:00.000';
     $topRow=50000000;
     $xml_url_test = "https://193.19.84.156:1445/OnLineShopService.svc/GetChangesOnLineShopLager?filialId=1614&dateFrom=".$dateFrom."&topRow=".$topRow."&topReload=".$topReload;
     $response_xml_data = $this->file_get_contents_curl($xml_url_test);
     $Change_lager = array();
     $Change_lager = json_decode($response_xml_data, true);
     $Change_lager = $Change_lager['OnLineShopLager'];
     $this->export_data_to_csv($Change_lager);
     die();
    }
   
   
   if ($filial == 1614) $id_shop = 1;
   if ($filial == 322) $id_shop = 2;
   if ($filial == 1674) $id_shop = 3;
   if ($filial == 510) $id_shop = 4;
   //Текущая дата в формате SQL
   $tek_date = date('Y-m-d H:i:s', time());
   // Очищаем данные по последнему обновлению   
   $sql_drop = "DELETE FROM `"._DB_PREFIX_."fozzy_autoupd` WHERE `id_shop` = ".$id_shop;
   Db::getInstance()->execute($sql_drop);
   
   $xml_url = "https://193.19.84.156:1445/OnLineShopService.svc/GetChangesOnLineShopLagerPrice?filialId=".$filial."&dateFrom=".$dateFrom."&topRow=".$topRow."&topReload=".$topReload;

   $response_xml_data = $this->file_get_contents_curl($xml_url);
   //   dump($response_xml_data);
   //  die('ok');
   $Change = array();
   $Change = json_decode($response_xml_data, true);
   $Change = $Change['OnLineShopLagerPrice'];
   
   $this->log_data_to_csv($Change, $filial);
  //    dump($Change);
  //   die();
   if ( !$Change) {
    return false;
    } else {
       
    $sql_insert = 'REPLACE INTO `'._DB_PREFIX_.'fozzy_autoupd` (`reference`, `sap_status`, `id_shop`, `price_rozn`, `price_opt`, `price_in`, `quantity`, `date_upd`, `on_sale`, `reduction_from`, `reduction_to`, `price_old`) VALUES ';
    
    foreach ($Change as $row)
      {
        /*if ($row['lagerId'] == 364860)
          {
            dump($row);
            die();
          } 
        */
        $sap_status = $row['SAPStatus'];
        if (!$sap_status) $sap_status = 0;
        $price_rozn = $row['priceRozn'];
        if (!$price_rozn) $price_rozn = 0;
        else $price_rozn = number_format((float)str_replace(",",".",$row['priceRozn']), 6, '.', '');
        $price_opt = $row['priceOpt'];
        if (!$price_opt) $price_opt = 0;
        else $price_opt = number_format((float)str_replace(",",".",$row['priceOpt']), 6, '.', '');
        $price_in = $row['priceIn'];
        if (!$price_in) $price_in = 0;
        else $price_in = number_format((float)str_replace(",",".",$row['priceIn']), 6, '.', '');
        $qty = $row['kolvoNow'];
        if (!$qty) $qty = 0; 
        else $qty = abs(number_format((float)str_replace(",",".",$row['kolvoNow']), 6, '.', ''));
        $on_sale = $row['IsActivityEnable'];
        if (!$on_sale) $on_sale = 0; 
        $from = date('Y-m-d H:i:s', strtotime($row['ActivityDateFrom']));
        if ($from == '1970-01-01 03:00:00') $from = '';
        //$to = date('Y-m-d H:i:s', strtotime($row['ActivityDateTo']) + 86340);
        $to = date('Y-m-d H:i:s', strtotime($row['ActivityDateTo'])); 
        if ($to == '1970-01-01 03:00:00') $to = '';
        $price_old = $row['ActivityPriceBefore'];
        if (!$price_old) $price_old = 0;
        else $price_old = number_format((float)str_replace(",",".",$row['ActivityPriceBefore']), 6, '.', '');
         
        $sql_insert .= "('".$row['lagerId']."','".$sap_status."',".$id_shop.",".$price_rozn.",".$price_opt.",".$price_in.",".$qty.",'".date('Y-m-d H:i:s')."',".$on_sale.",'".$from."','".$to."',".$price_old."), "; 
      }
    $sql_insert = substr($sql_insert, 0, -2);
   //  dump($sql_insert);
    Db::getInstance()->execute($sql_insert);
    unset($Change);
    //   dump(count($Change));
    //  $this->export_data_to_csv($Change);
    //   die();
    $sql_product_ref = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` fp, `"._DB_PREFIX_."product` p SET fp.`id_product` = p.`id_product` WHERE p.`reference` = fp.`reference`";
    $sql_product_del = "DELETE FROM `"._DB_PREFIX_."fozzy_autoupd` WHERE `id_product` = 0";
    $sql_product_def = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` fp, `"._DB_PREFIX_."product_shop` ps SET fp.`id_product` = ps.`id_product`, fp.`online_only` = ps.`online_only`, fp.`active` = ps.`active`, fp.`available_for_order` = ps.`available_for_order`, fp.`show_price` = ps.`show_price`, fp.`visibility` = ps.`visibility`, fp.`id_category_default` = ps.`id_category_default` WHERE ps.`id_product` = fp.`id_product` AND (ps.`id_shop` = fp.`id_shop`)";
    Db::getInstance()->execute($sql_product_ref);   //Получаем ID товара, так проще дальше
    Db::getInstance()->execute($sql_product_del);   //Удаляем товары которых нет в базе
   // die('tok-0');
    Db::getInstance()->execute($sql_product_def);   //Получаем текущие параметры товара
   // die('tok-0-0');
   return true;
   }
  }

  public function UpdatePrices($filial=1614)
  {
      if ($filial == 1614) $id_shop = 1;
      if ($filial == 322) $id_shop = 2;
      if ($filial == 1674) $id_shop = 3;
      if ($filial == 510) $id_shop = 4;
      
      //Текущая дата в формате SQL
      $tek_date = date('Y-m-d H:i:s', time());
      $sql_actions_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_old`, p.`on_sale` = e.`on_sale` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`price_rozn` > 0) AND (e.`on_sale` = 1) AND ('".$tek_date."' BETWEEN e.`reduction_from` AND e.`reduction_to`)";
      
  //    dump($sql_actions_shop);
   //   die();
      //Получаем ID товаров для обновления
      $sql_id_to_update = "SELECT `id_product` FROM `"._DB_PREFIX_."fozzy_autoupd` WHERE `id_shop` = ".$id_shop;
      $ids_from_base = Db::getInstance()->executeS($sql_id_to_update);
      $ids_from_base_norm = array();
      foreach ($ids_from_base as $ids_from)
        {
         $ids_from_base_norm[]=$ids_from['id_product'];
        }
      $id_to_update = implode(",",$ids_from_base_norm);
    //  die('tok-1');
      //Остатки нулевые, товар делаем недоступным к заказу
      $sql_nostock = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'catalog' WHERE `quantity` = 0  AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_nostock);
      //Остатки не нулевые, товар делаем доступным к заказу
      $sql_nostock2 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` SET `available_for_order` = 1, `show_price` = 1, `visibility` = 'both' WHERE `quantity` > 0 AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_nostock2);
      //Статусы, товар скрываем
      $sql_sapstatus = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'none' WHERE sap_status !='АА' AND (sap_status !='BP') AND (sap_status !=0) AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_sapstatus);
      //Акции, нормализуем этот бред
      //Там где старая цена не адекватна, нормализируем
      $sql_actions_norm_old = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` SET `price_old` = `price_rozn`*1.15 WHERE `on_sale` = 1 AND (`price_old` <= `price_rozn`) AND (`id_shop` = ".$id_shop.") ";
      //Вычисляем разницу
      $sql_actions_amount = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` SET `amount`= `price_old` - `price_rozn` WHERE `on_sale` = 1 AND (`id_shop` = ".$id_shop.")";
      //Очищием непонятки
      $sql_actions_del_old = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` SET `price_old`= 0 WHERE `on_sale` = 0 AND (`id_shop` = ".$id_shop.")";       
      Db::getInstance()->execute($sql_actions_del_old);
      Db::getInstance()->execute($sql_actions_norm_old);
      Db::getInstance()->execute($sql_actions_amount);
      
    //  die('tok-2');
      
      //Удаляем акции по товарам в обновлении
      $sql_action_clear_shop = "UPDATE `"._DB_PREFIX_."product_shop` SET `on_sale` = 0 WHERE `id_shop` = ". $id_shop ." AND `id_product` IN (".$id_to_update.")";
      //Удаляем акции из обновляемых  товаров
      $sql_sp_clear = "DELETE FROM `"._DB_PREFIX_."specific_price` WHERE `id_shop` = ".$id_shop." AND `id_product` IN (".$id_to_update.")";   
      //Очистка опта в текущем магазине
  //    $sql_sp_opt_clear = "DELETE FROM `"._DB_PREFIX_."specific_price` WHERE `id_shop` = ".$id_shop." AND `price` > 0";
      //Очистка приоритета во всех магазинах  
  //    $sql_sp_clear2 = "TRUNCATE TABLE `"._DB_PREFIX_."specific_price_priority`";
      Db::getInstance()->execute($sql_action_clear_shop);
      Db::getInstance()->execute($sql_sp_clear);
  //    Db::getInstance()->execute($sql_sp_opt_clear);
    //  Db::getInstance()->execute($sql_sp_clear2); убрать в новые товары
      
      //Обновляем видимость в текущем магазине
      $sql_visible_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) ";
      Db::getInstance()->execute($sql_visible_shop);
      //Обновляем цены в текущем магазине
      $sql_opt_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`price_opt` > 0)";
      $sql_roznica_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`price_rozn` > 0)";
      Db::getInstance()->execute($sql_opt_shop);
      Db::getInstance()->execute($sql_roznica_shop);
      //Прописываем старую цену как основную для акционных позиций
      $sql_actions_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_old`, p.`on_sale` = e.`on_sale` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`price_rozn` > 0) AND (e.`on_sale` = 1) AND ('".$tek_date."' BETWEEN e.`reduction_from` AND e.`reduction_to`)";
      Db::getInstance()->execute($sql_actions_shop);
      //Обновляем наличие в текущем магазине
      $sql_quantity = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (s.`id_shop` = e.`id_shop`)";
      Db::getInstance()->execute($sql_quantity);
      //Акции
      $sql_sp_add = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $id_shop .", 0, 0, 0, 0, 0, 0, -1, 1, `amount`, 1, 'amount', `reduction_from`, `reduction_to`
      FROM `"._DB_PREFIX_."fozzy_autoupd`  
      WHERE `amount` > 0 AND on_sale = 1 AND `id_shop` = ". $id_shop;
   /*   $sql_sp_add2 = "INSERT INTO `"._DB_PREFIX_."specific_price_priority`(`id_product`, `priority`)
      SELECT `id_product`, 'id_shop;id_currency;id_country;id_group'
      FROM `"._DB_PREFIX_."product` WHERE 1 "; */
      Db::getInstance()->execute($sql_sp_add);
   //   Db::getInstance()->execute($sql_sp_add2); убрать в новые товары
     // dump($sql_sp_add);
    //  die();




      
      //Обновляем Хореку - Киев
      if ($id_shop == 1) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, unit_price_ratio - используем для розницы )
      $sql_opt_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      $sql_roznica_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      $sql_vhod_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`unit_price_ratio` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (s.`id_shop` = 5)";  
      Db::getInstance()->execute($sql_visible_shop_5);
      Db::getInstance()->execute($sql_opt_shop_5);
      Db::getInstance()->execute($sql_roznica_shop_5);
      Db::getInstance()->execute($sql_vhod_shop_5);
      Db::getInstance()->execute($sql_quantity_5);
      }
      //Обновляем Хореку - Одесса
      if ($id_shop == 2) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, unit_price_ratio - используем для розницы )
      $sql_opt_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      $sql_roznica_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      $sql_vhod_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`unit_price_ratio` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (s.`id_shop` = 6)";  
      Db::getInstance()->execute($sql_visible_shop_6);
      Db::getInstance()->execute($sql_opt_shop_6);
      Db::getInstance()->execute($sql_roznica_shop_6);
      Db::getInstance()->execute($sql_vhod_shop_6);
      Db::getInstance()->execute($sql_quantity_6);
      } 
      //Обновляем Хореку - Харьков
      if ($id_shop == 4) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, unit_price_ratio - используем для розницы )
      $sql_opt_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      $sql_roznica_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      $sql_vhod_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."product_shop` p SET p.`unit_price_ratio` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (s.`id_shop` = 7)";  
      Db::getInstance()->execute($sql_visible_shop_7);
      Db::getInstance()->execute($sql_opt_shop_7);
      Db::getInstance()->execute($sql_roznica_shop_7);
      Db::getInstance()->execute($sql_vhod_shop_7);
      Db::getInstance()->execute($sql_quantity_7);
      }
      /*
      
      //Оптовые цены
      //Проставили оптовые цены из обновления
      $sql_opt_prices = "UPDATE `"._DB_PREFIX_."fozzy_autoupd` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`opt_price_". $id_shop ."` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = ".$id_shop.")";
      //Проставили очистили метку - АКЦИЯ во всей таблице
      $sql_opt_actions_clear = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 0 WHERE 1";
      //Отключили опт для алкоголя и херни всякой во всей таблице
      $sql_opt_actions_no_buhlo = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 1 WHERE p.`category` IN (300069,300070,300073,300074,300333,300334,300335,300336,300337,300526,300338,300345,300346,300347,300348,300349,300350,300351,300352,300353,300354,300355,300356,300357,300358,300359,300360,300361,300362,300613,300388,300603,300389,300390,300391,300086,300087,300343,300363,300364,300365,300366,300344,300339,300340,300341,300342)";
      //И включили опт по индикативам во всей таблице
      $sql_opt_actions_no_buhlo_exlude = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 0 WHERE p.`category` IN (300069,300070,300073,300074,300333,300334,300335,300336,300337,300526,300338,300345,300346,300347,300348,300349,300350,300351,300352,300353,300354,300355,300356,300357,300358,300359,300360,300361,300362,300613,300388,300603,300389,300390,300391,300086,300087,300343,300363,300364,300365,300366,300344,300339,300340,300341,300342) AND (p.`opt_price_". $id_shop ."` > p.`min_price`) ";
      //Влили акции с таблицы товаров по всей таблице
      $sql_opt_actions = "UPDATE `"._DB_PREFIX_."product_shop` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 1 WHERE p.`id_product` = e.`id_product` AND e.`on_sale` = 1 AND e.`id_shop` = ". $id_shop;
      //Отключили опт где цена опта равна рознице
      $sql_opt_hernia = "UPDATE `"._DB_PREFIX_."product_shop` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 1 WHERE p.`id_product` = e.`id_product` AND (e.`price` = e.`wholesale_price`) AND e.`id_shop` = ". $id_shop;
      //Баг сервера баз данных с датами
      $date_time_version = "SET SESSION sql_mode = ''";
      //Влили новые скидки 
      $sql_opt__sp_add4 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $id_shop .", 0, 0, 0, 4, 0, 0, `opt_price_". $id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $id_shop ."` = 0 AND (`opt_price_". $id_shop ."` > 0)
      ";    
      $sql_opt__sp_add3 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $id_shop .", 0, 0, 0, 3, 0, 0, `opt_price_". $id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $id_shop ."` = 0 AND (`opt_price_". $id_shop ."` > 0)
      ";
      $sql_opt__sp_add2 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $id_shop .", 0, 0, 0, 2, 0, 0, `opt_price_". $id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $id_shop ."` = 0 AND (`opt_price_". $id_shop ."` > 0)
      ";
      $sql_opt__sp_add1 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $id_shop .", 0, 0, 0, 1, 0, 0, `opt_price_". $id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $id_shop ."` = 0 AND (`opt_price_". $id_shop ."` > 0)
      ";
      
      
      Db::getInstance()->execute($sql_opt_prices);
      Db::getInstance()->execute($sql_opt_actions_clear);
      Db::getInstance()->execute($sql_opt_actions_no_buhlo);
      Db::getInstance()->execute($sql_opt_actions_no_buhlo_exlude);
      Db::getInstance()->execute($sql_opt_actions);
      Db::getInstance()->execute($sql_opt_hernia);
      Db::getInstance()->execute($sql_opt__sp_add1);
      Db::getInstance()->execute($sql_opt__sp_add2);
      Db::getInstance()->execute($sql_opt__sp_add3);
      Db::getInstance()->execute($sql_opt__sp_add4);
         */
      $sql_truncs = "TRUNCATE TABLE `"._DB_PREFIX_."fozzy_autoupd`";
      Db::getInstance()->execute($sql_truncs);

  return true;
  
  }

private function ExcelToSQLDate($exceldate)
	{
    $sqldate = gmdate("Y-m-d H:i:s", ($exceldate - 25569) * 86400);
    if (!$sqldate || $sqldate == '1899-12-30 00:00:00') $sqldate = '0000-00-00 00:00:00';
    return $sqldate;
  }
  
public function xml2array ( $xmlObject, $out = array () )
  {
    foreach ( (array) $xmlObject as $index => $node )
        $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

    return $out;
  }

private function file_get_contents_curl( $url ) {

  $ch = curl_init();
  
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
  curl_setopt( $ch, CURLOPT_HEADER, false );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

  $data = curl_exec( $ch );
  curl_close( $ch );

  return $data;

  }

public function export_data_to_csv($data,$filename='export',$delimiter = ';',$enclosure = '"')
    {
    header("Content-disposition: attachment; filename=$filename.csv");
    header("Content-Type: text/csv");

    $fp = fopen("php://output", 'w');

    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    $headerss = array('Артикул','Вес','Опт');
    fputcsv($fp,$headerss,$delimiter,$enclosure);

    foreach ($data as $fields) {
        if (!$fields['barcodeQuantity']) 
        {
          $fields['barcodeQuantity'] = 0;
        }
        krsort($fields);
        fputcsv($fp, $fields,$delimiter,$enclosure);
    }
    fclose($fp);
    die();
  }

public function log_data_to_csv($data,$filial = 1614,$delimiter = ';',$enclosure = '"')
    {

    $filename = '/home/admin/web/fozzyshop.com.ua/public_html/modules/fozzy_autoupd/log/'.$filial."_".date('d_m_Y_H_i_s')."_prices.csv";
    $fp = fopen($filename, 'w');

    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    $headerss = array('ActivityDateFrom','ActivityDateTo','ActivityPriceBefore','IsActivityEnable','SAPStatus','kolvoNow','lagerId','priceIn','priceOpt','priceRozn');
    fputcsv($fp,$headerss,$delimiter,$enclosure);

    foreach ($data as $key => $fields) {
        foreach ($headerss as $h)
          {
            if (!array_key_exists($h, $fields)) {
               $fields[$h] = 'NULL';
            }          
          }
        ksort($fields);
        fputcsv($fp, $fields,$delimiter,$enclosure);
    }
    fclose($fp);
    return true;
  }    
    
}
