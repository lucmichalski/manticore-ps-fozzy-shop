<?php
if (!defined('_PS_VERSION_'))
	exit;

class FozzyPrice extends Module
{
	public function __construct()
	{
		$this->name = 'fozzyprice';
		$this->tab = 'quick_bulk_update';
		$this->version = '1.0';
		$this->author = 'Novevision.com, Britoff A.';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Обновление цен для Fozzy');
		$this->description = $this->l('Автоматическое обновление цен для гипермаркета Fozzy');
	}

	public function install()
	{
		if (!parent::install()
      )
				return false;
        
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzy_auto_prices`';
    Db::getInstance()->execute($sql_drop);
    $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."fozzy_auto_prices` (
        `id_excel` int(10) unsigned NOT NULL AUTO_INCREMENT,                                 
        `reference` varchar(32),                                                             
        `id_product` int(10) NOT NULL DEFAULT '0',                                           
        `sap_status` varchar(32),                                                            
        `id_shop` int(10) NOT NULL DEFAULT '1',                                              
        `price_rozn` decimal(20,6) NOT NULL DEFAULT '0.000000',                              
        `price_opt` decimal(20,6) NOT NULL DEFAULT '0.000000',                               
        `price_in` decimal(20,6) NOT NULL DEFAULT '0.000000',                                
        `price_old` decimal(20,6) NOT NULL DEFAULT '0.000000',                               
        `quantity` decimal(20,6) NOT NULL DEFAULT '0.00',                                    
        `on_sale` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',                                  
        `online_only` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',                              
        `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',                                   
        `available_for_order` tinyint(1) NOT NULL DEFAULT '1',                               
        `show_price` tinyint(1) NOT NULL DEFAULT '1',                                        
        `visibility` enum('both','catalog','search','none') NOT NULL DEFAULT 'both',         
        `id_category_default` int(10) UNSIGNED DEFAULT NULL,                                 
        `amount` decimal(20,2) NOT NULL DEFAULT '0.00',                                      
        `reduction_from` datetime DEFAULT NULL,                                              
        `reduction_to` datetime DEFAULT NULL,                                                
        `date_upd` datetime NOT NULL,                                                        
        PRIMARY KEY (`id_excel`),
        KEY `id_category_default` (`id_category_default`),
        KEY `date_upd` (`date_upd`),
        UNIQUE KEY `reference` (`reference`,`id_product`) 
      ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
    Db::getInstance()->execute($sql_create);
    
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzy_auto_lager`';
    Db::getInstance()->execute($sql_drop);
    $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."fozzy_auto_lager` (
        `id_excel` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `reference` varchar(32),
        `id_product` int(10) NOT NULL DEFAULT '0',
        `optqty` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `brutto` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_excel`),
        KEY `date_upd` (`date_upd`),
        UNIQUE KEY `reference` (`reference`,`id_product`) 
      ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
    Db::getInstance()->execute($sql_create);
      
		return true;
	}

	public function uninstall()
	{
		$sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzy_auto_prices`';
    Db::getInstance()->execute($sql_drop);
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzy_auto_lager`';
    Db::getInstance()->execute($sql_drop);
    return parent::uninstall();
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

  function export_data_to_csv($data,$filename='export',$delimiter = ';',$enclosure = '"')
    {
    // Tells to the browser that a file is returned, with its name : $filename.csv
    header("Content-disposition: attachment; filename=$filename.csv");
    // Tells to the browser that the content is a csv file
    header("Content-Type: text/csv");

    // I open PHP memory as a file
    $fp = fopen("php://output", 'w');

    // Insert the UTF-8 BOM in the file
    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    // I add the array keys as CSV headers
    $headerss = array('Артикул','Вес','Опт');
    fputcsv($fp,$headerss,$delimiter,$enclosure);

    // Add all the data in the file
    foreach ($data as $fields) {
        if (!$fields['barcodeQuantity']) 
        {
          $fields['barcodeQuantity'] = 0;
        }
        krsort($fields);
        fputcsv($fp, $fields,$delimiter,$enclosure);
    }

    // Close the file
    fclose($fp);

    // Stop the script
    die();
    }

  public function LoadPrices($filial=1614, $dateFrom='2018-12-01T09:00:00.000', $topRow=800000, $topReload=1)
  {
   ini_set('display_errors', 1);
   error_reporting(E_ALL ^ E_NOTICE);
   //$dateFrom='2019-10-30T09:00:00.000';
   //$dateFrom='2019-11-01T01:00:00.000';
   if ($filial == 1614) $id_shop = 1;
   if ($filial == 322) $id_shop = 2;
   if ($filial == 1674) $id_shop = 3;
   if ($filial == 510) $id_shop = 4;
    
   if ($filial == 100) //Проверка сервиса  - это блин не отсюда, перенести в Oreders!
    {
     $xml_url_test = "https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetServiceCheck";
     $response_xml_data = $this->file_get_contents_curl($xml_url_test);
     dump($response_xml_data);
     die();
    }
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
     
   $sql_drop = "DELETE FROM `"._DB_PREFIX_."fozzy_auto_prices` WHERE `id_shop` = ".$id_shop;
   Db::getInstance()->execute($sql_drop);
   $xml_url = "https://193.19.84.156:1445/OnLineShopService.svc/GetChangesOnLineShopLagerPrice?filialId=".$filial."&dateFrom=".$dateFrom."&topRow=".$topRow."&topReload=".$topReload;
   $response_xml_data = $this->file_get_contents_curl($xml_url);
   //   dump($xml_url);
   //  die();
   $Change = array();
   $Change = json_decode($response_xml_data, true);
   $Change = $Change['OnLineShopLagerPrice'];
  //    dump($Change);
  //   die();
   if ( !$Change) {
    return false;
    } else {
       
    $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'fozzy_auto_prices` (`reference`, `sap_status`, `id_shop`, `price_rozn`, `price_opt`, `price_in`, `quantity`, `date_upd`) VALUES ';
    
    foreach ($Change as $row)
      {
        
        $sap_status = $row['SAPStatus'];
        if (!$sap_status) $sap_status = 0;
        
        $price_rozn = $row['priceRozn'];
        if (!$price_rozn) $price_rozn = -100000;
        else $price_rozn = number_format((float)str_replace(",",".",$row['priceRozn']), 6, '.', '');
        
        $price_opt = $row['priceOpt'];
        if (!$price_opt) $price_opt = -100000;
        else $price_opt = number_format((float)str_replace(",",".",$row['priceOpt']), 6, '.', '');
        
        $price_in = $row['priceIn'];
        if (!$price_in) $price_in = -100000;
        else $price_in = number_format((float)str_replace(",",".",$row['priceIn']), 6, '.', '');
        
        $qty = $row['kolvoNow'];
        if (!$qty) $qty = -100000; 
        else $qty = number_format((float)str_replace(",",".",$row['kolvoNow']), 6, '.', '');
         
        $sql_insert .= "('".$row['lagerId']."','".$sap_status."',".$id_shop.",".$price_rozn.",".$price_opt.",".$price_in.",".$qty.",'".date('Y-m-d H:i:s')."'), "; 
      }
    $sql_insert = substr($sql_insert, 0, -2);
 
    Db::getInstance()->execute($sql_insert);
    unset($Change);
     //  dump(count($Change));
     //   die();
    $sql_product_ref = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` fp, `"._DB_PREFIX_."product` p SET fp.`id_product` = p.`id_product` WHERE p.`reference` = fp.`reference`";
    $sql_product_del = "DELETE FROM `"._DB_PREFIX_."fozzy_auto_prices` WHERE `id_product` = 0";
    $sql_product_def = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` fp, `"._DB_PREFIX_."product_shop` ps SET fp.`id_product` = ps.`id_product`, fp.`on_sale` = ps.`on_sale`, fp.`online_only` = ps.`online_only`, fp.`active` = ps.`active`, fp.`available_for_order` = ps.`available_for_order`, fp.`show_price` = ps.`show_price`, fp.`visibility` = ps.`visibility`, fp.`id_category_default` = ps.`id_category_default` WHERE ps.`id_product` = fp.`id_product` AND (ps.`id_shop` = fp.`id_shop`)";
    $sql_product_abs = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` SET `quantity` = 1000 WHERE `quantity` < 0 AND `quantity` != -100000";
    Db::getInstance()->execute($sql_product_ref);   //Получаем ID товара, так проще дальше
    Db::getInstance()->execute($sql_product_del);   //Удаляем товары которых нет в базе
    Db::getInstance()->execute($sql_product_abs);   //Убираем отрицалово из количесв
    Db::getInstance()->execute($sql_product_def);   //Получаем текущие параметры товара
 
   return true;
   }
  }

  public function UpdatePrices($filial=1614)
  {
      if ($filial == 1614) $id_shop = 1;
      if ($filial == 322) $id_shop = 2;
      if ($filial == 1674) $id_shop = 3;
      if ($filial == 510) $id_shop = 4;
      //Получаем ID товаров для обновления
      $sql_id_to_update = "SELECT `id_product` FROM `"._DB_PREFIX_."fozzy_auto_prices` WHERE `id_shop` = ".$id_shop;
      $ids_from_base = Db::getInstance()->executeS($sql_id_to_update);
      $ids_from_base_norm = array();
      foreach ($ids_from_base as $ids_from)
        {
         $ids_from_base_norm[]=$ids_from['id_product'];
        }
      $id_to_update = implode(",",$ids_from_base_norm);

      //Остатки нулевые, товар делаем недоступным к заказу
      $sql_nostock = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'catalog' WHERE `quantity` = 0  AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_nostock);
      //Остатки не нулевые, товар делаем доступным к заказу
      $sql_nostock2 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` SET `available_for_order` = 1, `show_price` = 1, `visibility` = 'both' WHERE `quantity` <> 0 AND `quantity` != -100000  AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_nostock2);
      //Статусы, товар скрываем
      $sql_sapstatus = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'none' WHERE sap_status !='АА' AND (sap_status !='BP') AND (sap_status !=0) AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_sapstatus);
      //Акции, нормализуем этот бред
      //Проставим старые цены и даты акции
      $sql_actions = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` f, `"._DB_PREFIX_."fozzyactionstable_".$id_shop."` fa SET f.`price_old` = fa.`price_old`, f.`on_sale` = 1, f.`reduction_from` = fa.`reduction_from`, f.`reduction_to` =  fa.`reduction_to` WHERE f.`id_product` = fa.`id_product`  AND (f.`id_shop` = ".$id_shop.") ";
      //Там где старая цена не адекватна, нормализируем
      $sql_actions_norm_old = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` SET `price_old` = `price_rozn`*1.15 WHERE `on_sale` = 1 AND (`price_old` <= `price_rozn`) AND (`id_shop` = ".$id_shop.") ";
      //Вычисляем разницу
      $sql_actions_amount = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` SET `amount`= `price_old` - `price_rozn` WHERE `on_sale` = 1 AND (`id_shop` = ".$id_shop.")";      
      Db::getInstance()->execute($sql_actions);
      Db::getInstance()->execute($sql_actions_norm_old);
      Db::getInstance()->execute($sql_actions_amount);

      //Обновляем видимость в текущем магазине
      $sql_visible_shop = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) ";
      Db::getInstance()->execute($sql_visible_shop);
      //Обновляем цены в текущем магазине
      $sql_opt_shop = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`price_opt` <> -100000)  ";
      $sql_roznica_shop = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`price_rozn` <> -100000)";
      Db::getInstance()->execute($sql_opt_shop);
      Db::getInstance()->execute($sql_roznica_shop);
      //Обновляем наличие в текущем магазине
      $sql_quantity = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (s.`id_shop` = e.`id_shop`) AND (e.`quantity` <> -100000)";
      Db::getInstance()->execute($sql_quantity);
      
      //Обновляем Хореку - Киев
      if ($id_shop == 1) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, unit_price_ratio - используем для розницы )
      $sql_opt_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (e.`price_opt` <> -100000) AND (p.`id_shop` = 5)";
      $sql_roznica_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (e.`price_rozn` <> -100000) AND (p.`id_shop` = 5)";
      $sql_vhod_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`unit_price_ratio` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (e.`price_rozn` <> -100000) AND (p.`id_shop` = 5)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_5 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (e.`quantity` <> -100000) AND (s.`id_shop` = 5)";  
      Db::getInstance()->execute($sql_visible_shop_5);
      Db::getInstance()->execute($sql_opt_shop_5);
      Db::getInstance()->execute($sql_roznica_shop_5);
      Db::getInstance()->execute($sql_vhod_shop_5);
      Db::getInstance()->execute($sql_quantity_5);
      }
      //Обновляем Хореку - Одесса
      if ($id_shop == 2) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, unit_price_ratio - используем для розницы )
      $sql_opt_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`price_opt` <> -100000) AND (p.`id_shop` = 6)";
      $sql_roznica_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`price_rozn` <> -100000) AND (p.`id_shop` = 6)";
      $sql_vhod_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`unit_price_ratio` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`price_rozn` <> -100000) AND (p.`id_shop` = 6)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_6 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`quantity` <> -100000) AND (s.`id_shop` = 6)";  
      Db::getInstance()->execute($sql_visible_shop_6);
      Db::getInstance()->execute($sql_opt_shop_6);
      Db::getInstance()->execute($sql_roznica_shop_6);
      Db::getInstance()->execute($sql_vhod_shop_6);
      Db::getInstance()->execute($sql_quantity_6);
      } 
      //Обновляем Хореку - Харьков
      if ($id_shop == 4) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, unit_price_ratio - используем для розницы )
      $sql_opt_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`price_opt` <> -100000) AND (p.`id_shop` = 7)";
      $sql_roznica_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`price_rozn` <> -100000) AND (p.`id_shop` = 7)";
      $sql_vhod_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."product_shop` p SET p.`unit_price_ratio` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`price_rozn` <> -100000) AND (p.`id_shop` = 7)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_7 = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (e.`quantity` <> -100000) AND (s.`id_shop` = 7)";  
      Db::getInstance()->execute($sql_visible_shop_7);
      Db::getInstance()->execute($sql_opt_shop_7);
      Db::getInstance()->execute($sql_roznica_shop_7);
      Db::getInstance()->execute($sql_vhod_shop_7);
      Db::getInstance()->execute($sql_quantity_7);
      }
      //Акции
      $sql_action_clear_shop = "UPDATE `"._DB_PREFIX_."product_shop` SET `on_sale` = 0 WHERE `id_shop` = ". $id_shop ." AND `id_product` IN (".$id_to_update.")";
      $sql_action_shop_price = "UPDATE `"._DB_PREFIX_."product_shop` p, `"._DB_PREFIX_."fozzy_auto_prices` e SET p.`price` = e.`price_old`, p.`on_sale` = e.`on_sale` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`id_shop` = ".$id_shop.") AND (e.`on_sale` = 1) ";
    
      
      $sql_sp_clear = "DELETE FROM `"._DB_PREFIX_."specific_price` WHERE `id_shop` = ".$id_shop." AND `id_product` IN (".$id_to_update.")";   //Удаляем акции из обновленніх  товаров
      $sql_sp_opt_clear = "DELETE FROM `"._DB_PREFIX_."specific_price` WHERE `id_shop` = ".$id_shop." AND `price` > 0";  //Очистка опта в текущем магазине
      $sql_sp_clear2 = "TRUNCATE TABLE `"._DB_PREFIX_."specific_price_priority`";
    
      $sql_sp_add = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $id_shop .", 0, 0, 0, 0, 0, 0, -1, 1, `amount`, 1, 'amount', `reduction_from`, `reduction_to`
      FROM `"._DB_PREFIX_."fozzy_auto_prices`  
      WHERE `amount` > 0 AND `id_shop` = ". $id_shop ." 
      ";
  //    d($sql_sp_add);
      $sql_sp_add2 = "INSERT INTO `"._DB_PREFIX_."specific_price_priority`(`id_product`, `priority`)
      SELECT `id_product`, 'id_shop;id_currency;id_country;id_group'
      FROM `"._DB_PREFIX_."product` WHERE 1 ";
      
      Db::getInstance()->execute($sql_action_clear_shop);
      Db::getInstance()->execute($sql_action_shop_price);
      Db::getInstance()->execute($sql_sp_clear);
      Db::getInstance()->execute($sql_sp_opt_clear);
      Db::getInstance()->execute($sql_sp_clear2);
      Db::getInstance()->execute($sql_sp_add);
      Db::getInstance()->execute($sql_sp_add2);
      
      //Оптовые цены
      //Проставили оптовые цены
      $sql_opt_prices = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`opt_price_". $id_shop ."` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = ".$id_shop.")";
      //Проставили очистили метку - АКЦИЯ
      $sql_opt_actions_clear = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 0 WHERE 1";
      //Отключили опт для алкоголя и херни всякой
      $sql_opt_actions_no_buhlo = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 1 WHERE p.`category` IN (300333, 300334, 300335, 300336, 300337, 300338, 300526, 300339, 300340, 300341, 300342, 300345, 300346, 300347, 300348, 300349, 300350, 300351, 300352, 300353, 300354, 300355, 300357, 300358, 300359, 300360, 300361, 300362, 300366, 300389, 300390, 300391, 300613, 300603, 300370)";
      //И включили его по индикативам
      $sql_opt_actions_no_buhlo_exlude = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 0 WHERE p.`category` IN (300333, 300334, 300335, 300336, 300337, 300338, 300526, 300345, 300346, 300347, 300348, 300349, 300350, 300351, 300352, 300353, 300354, 300355, 300357, 300358, 300359, 300360, 300361, 300362, 300366, 300389, 300390, 300391, 300613, 300603) AND (p.`opt_price_". $id_shop ."` > p.`min_price`) ";
      //Влили акции с таблицы акций
      $sql_opt_actions = "UPDATE `"._DB_PREFIX_."fozzyactionstable_". $id_shop ."` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 1 WHERE p.`id_product` = e.`id_product`";
      //Отключили опт где цена опта равна рознице
      $sql_opt_hernia = "UPDATE `"._DB_PREFIX_."fozzy_auto_prices` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $id_shop ."` = 1 WHERE p.`id_product` = e.`id_product` AND (e.`price_rozn` = e.`price_opt`) ";
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
      
      $sql_truncs = "TRUNCATE TABLE `"._DB_PREFIX_."fozzy_auto_prices`";
      Db::getInstance()->execute($sql_truncs);

  return true;
  
  }

  /*  [OnLineShopLager] => Array
        (
            [0] => Array
                (
                    [barcodeQuantity] => 15,000
                    [lagerBrutto] => 0,095
                    [lagerId] => 774164
                )
         */

}
