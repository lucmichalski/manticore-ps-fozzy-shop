<?php
if (!defined('_PS_VERSION_'))
	exit;

class Fozzy_autoupdate extends Module
{
	public function __construct()
	{
		$this->name = 'fozzy_autoupdate';
		$this->tab = 'quick_bulk_update';
		$this->version = '1.0';
		$this->author = 'Novevision.com, Britoff A.';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Обновление цен c акциями и оптом для Fozzy');
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
  
  public function getContent()
	{
   if (Tools::isSubmit('btnUpload'))
		{
      $this->_postProcess();
         if (!count($this->_postErrors))
				  {
          
          $OK_MESSAGE = $this->l('Файл загружен');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          
        //  $sql_drop = 'TRUNCATE `'._DB_PREFIX_.'fozzy_autoupdate_online`';
        //  Db::getInstance()->execute($sql_drop);
          
          require_once dirname(__FILE__)."/classes/simplexlsx.class.php";
          $xlsx = new SimpleXLSX(dirname(__FILE__).'/upload/toprepare.xlsx');
          $excelfile = $xlsx->rows();
          
          $cols = count ($excelfile);
          
          if (!$cols) {
          $this->_html .= $this->displayError('Ошибка в файле загрузки');
          }
          foreach ($excelfile as $key=>$row)
          {
          if (!$row[0])
          unset($excelfile[$key]);
          }
      //    dump($excelfile);
      //    die();
          
          
          unset ($xlsx);
   
          $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'fozzy_autoupdate_online`(`reference`, `price`, `price_retail`, `price_old`, `date_start`, `date_stop`, `factor`) VALUES ';
          foreach ($excelfile as $key=>$row)
          {
            if ($key == 0 && $offset == 0) continue;
            $row[5] = gmdate("Y-m-d H:i:s", (($row[5] - 25569) * 86400) + 86400);
            $row[4] = gmdate("Y-m-d H:i:s", ($row[4] - 25569) * 86400);
            $sql_insert .= "($row[0],$row[1],$row[2],$row[3],'$row[4]','$row[5]',$row[6]),";
   
          }
          $sql_insert = substr($sql_insert, 0, -1);
          
          unset ($excelfile);
          unset ($row);

          Db::getInstance()->execute($sql_insert);
          unset ($sql_insert);
          
          $sql_compare = 'UPDATE `'._DB_PREFIX_.'fozzy_autoupdate_online` e, `'._DB_PREFIX_.'product` p SET e.`id_product` = p.`id_product` WHERE e.`reference` = p.`reference`';
          $sql_delete = 'DELETE FROM `'._DB_PREFIX_.'fozzy_autoupdate_online` WHERE `id_product` IS NULL';
          $sql_delete2 = "DELETE FROM `"._DB_PREFIX_."fozzy_autoupdate_online` WHERE `id_product` = 0";
          Db::getInstance()->execute($sql_compare);
          Db::getInstance()->execute($sql_delete);
          Db::getInstance()->execute($sql_delete2);
         
          $this->_html .= $this->displayConfirmation('Файл обработан');
          
          
          }
    }
   
   $fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Спец. скидки'),
					'icon' => 'icon-cogs'
				),
        'input' => array(
          array(
						'type' => 'file',
						'label' => $this->l('Файл со скидками:'),
						'name' => 'PS_AUTOUPDATE_FILE',
						'desc' => $this->l('Должен быть в XLSX'),
					),
          
          ),
				'buttons' => array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Загрузить'),
                    'icon' => 'process-icon-upload',
                    'name' => 'btnUpload',
                    'id'   => 'btnUpload',
                    'class'=> 'pull-right'
                ),
            )
		  );
      $helper = new HelperForm();
      
      // Module, token and currentIndex
      $helper->module = $this;
      $helper->name_controller = $this->name;
      $helper->token = Tools::getAdminTokenLite('AdminModules');
      $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
      
      // Language
      $languages = Language::getLanguages(false);
      $helper->languages = $languages;
      $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		  $helper->allow_employee_form_lang = true;
       
      // Title and toolbar
      $helper->title = $this->displayName;
      $helper->show_toolbar = true;        // false -> remove toolbar
      $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
      $helper->submit_action = 'submit'.$this->name;
      
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

    $this->_html .= $helper->generateForm($fields_form);
		return  $this->_html;
   
  }
  
  public function getConfigFieldsValues()
	{
		$config_fields = array();
		return $config_fields;
	}
  
  private function _postProcess()
	{

   $target_file = dirname(__FILE__)."/upload/toprepare.xlsx";

   if (move_uploaded_file($_FILES['PS_AUTOUPDATE_FILE']['tmp_name'], $target_file)) {
      //  echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        $this->_postErrors[] = $this->l('Ошибка при загрузке файла.');
    }
    
    return !count($this->_postErrors) ? true : false;
	}
  
  public function UpdateOnlineOnly()
  {
    $sql_category_clear = 'DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_category` = 300809';
    $sql_category_insert = 'INSERT INTO `'._DB_PREFIX_.'category_product`(`id_category`, `id_product`, `position`) SELECT 300809, `id_product`, `id_online` FROM `'._DB_PREFIX_.'fozzy_autoupdate_online` WHERE now() BETWEEN `date_start` AND `date_stop`';
    $sql_supplier_clear = 'DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE `id_supplier` = 15';
    $sql_supplier_insert = 'INSERT INTO `'._DB_PREFIX_.'product_supplier`(`id_product`, `id_supplier`, `id_currency`) SELECT `id_product`, 15, 1 FROM `'._DB_PREFIX_.'fozzy_autoupdate_online` WHERE now() BETWEEN `date_start` AND `date_stop`';
    $sql_old_clear = 'DELETE FROM `'._DB_PREFIX_.'fozzy_autoupdate_online` WHERE `date_stop` < now()';
    Db::getInstance()->execute($sql_old_clear);
    Db::getInstance()->execute($sql_category_clear);
    Db::getInstance()->execute($sql_category_insert);
    Db::getInstance()->execute($sql_supplier_clear);
    Db::getInstance()->execute($sql_supplier_insert);
  } 
  
  public function LoadPrices($filial=1614, $dateFrom='2018-12-01T09:00:00.000', $topRow=800000, $topReload=1)
  {
   ini_set('display_errors', 1);
   error_reporting(E_ALL ^ E_NOTICE);
  // $dateFrom='2020-03-31T15:00:00.000';
  // $dateFrom='2020-11-03T21:00:00.000';
   
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
   
   
   if ($filial == 1614) $id_shop = 1; // Киев
   if ($filial == 322) $id_shop = 2;  // Одесса
   if ($filial == 1674) $id_shop = 3; // Днепр
   if ($filial == 510) $id_shop = 4;  // Харьков
   if ($filial == 382) $id_shop = 8;  // Ровно
   if ($filial == 1292) $id_shop = 9; // Кременчуг
   // if ($filial == xxx) $id_shop = 10; // Львов
   
   //Текущая дата в формате SQL
   $tek_date = date('Y-m-d H:i:s', time());
   // Очищаем данные по последнему обновлению   
   $sql_drop = "DELETE FROM `"._DB_PREFIX_."fozzy_autoupdate` WHERE `id_shop` = ".$id_shop;
   Db::getInstance()->execute($sql_drop);
   
   $xml_url = "https://193.19.84.156:1445/OnLineShopService.svc/GetChangesOnLineShopLagerPrice?filialId=".$filial."&dateFrom=".$dateFrom."&topRow=".$topRow."&topReload=".$topReload;
   //dump($xml_url);
   //die();
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
    $filename = _PS_ROOT_DIR_.'/modules/fozzy_autoupdate/log/'.$filial."_".date('d_m_Y_H_i_s')."_prices_error.txt";
    $file = fopen($filename, 'w');
    fwrite($file, $response_xml_data);
    fclose($file);
    return false;
    } else {
       
    $sql_insert = 'REPLACE INTO `'._DB_PREFIX_.'fozzy_autoupdate` (`reference`, `sap_status`, `id_shop`, `price_rozn`, `price_opt`, `price_in`, `quantity`, `date_upd`, `on_sale`, `reduction_from`, `reduction_to`, `price_old`, `site_display`) VALUES ';
    
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
         
        $sql_insert .= "('".$row['lagerId']."','".$sap_status."',".$id_shop.",".$price_rozn.",".$price_opt.",".$price_in.",".$qty.",'".date('Y-m-d H:i:s')."',".$on_sale.",'".$from."','".$to."',".$price_old.",".$row['DisplayOnFozzyShopSite']."), "; 
      }
    $sql_insert = substr($sql_insert, 0, -2);
   //  dump($sql_insert);
    Db::getInstance()->execute($sql_insert);
    unset($Change);
    //   dump(count($Change));
    //  $this->export_data_to_csv($Change);
    //   die();
    $sql_product_ref = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` fp, `"._DB_PREFIX_."product` p SET fp.`id_product` = p.`id_product` WHERE p.`reference` = fp.`reference`";
    $sql_product_del = "DELETE FROM `"._DB_PREFIX_."fozzy_autoupdate` WHERE `id_product` = 0";
    $sql_product_def = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` fp, `"._DB_PREFIX_."product_shop` ps SET fp.`id_product` = ps.`id_product`, fp.`online_only` = ps.`online_only`, fp.`active` = ps.`active`, fp.`available_for_order` = ps.`available_for_order`, fp.`show_price` = ps.`show_price`, fp.`visibility` = ps.`visibility`, fp.`id_category_default` = ps.`id_category_default`, fp.`indexed` = ps.`indexed` WHERE ps.`id_product` = fp.`id_product` AND (ps.`id_shop` = fp.`id_shop`)";
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
      if ($filial == 1614) $id_shop = 1; // Киев
      if ($filial == 322) $id_shop = 2;  // Одесса
      if ($filial == 1674) $id_shop = 3; // Днепр
      if ($filial == 510) $id_shop = 4;  // Харьков
      if ($filial == 382) $id_shop = 8;  // Ровно
      if ($filial == 1292) $id_shop = 9; // Кременчуг
     // if ($filial == xxx) $id_shop = 10; // Львов
     
      //Текущая дата в формате SQL
      $tek_date = date('Y-m-d H:i:s', time());

      //Получаем ID товаров для обновления - 30/03/2020
      $sql_id_to_update = "SELECT `id_product` FROM `"._DB_PREFIX_."fozzy_autoupdate` WHERE `id_shop` = ".$id_shop;
      $ids_from_base = Db::getInstance()->executeS($sql_id_to_update);
      $ids_from_base_norm = array();
      foreach ($ids_from_base as $ids_from)
        {
         $ids_from_base_norm[]=$ids_from['id_product'];
        }
      $id_to_update = implode(",",$ids_from_base_norm);
      
    //  die('tok-1');
    
      
      //Синхронизируем отображение в ИМ
      $sql_im_sync = "UPDATE `"._DB_PREFIX_."product_shop` p, `"._DB_PREFIX_."fozzy_autoupdate` a SET p.`show_site` = a.`site_display` WHERE p.`id_product` = a.`id_product` AND (p.`id_shop` = a.`id_shop`)";
      Db::getInstance()->execute($sql_im_sync);
      
      //Остатки нулевые или цена, товар делаем недоступным к заказу, отображаем везде - 30/03/2020
      $sql_nostock = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'both', `indexed` = 0 WHERE (`quantity` = 0 OR `price_rozn` = 0) AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_nostock);
      //Остатки не нулевые и цена, товар делаем доступным к заказу, отображаем везде - 30/03/2020
      $sql_nostock2 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` SET `available_for_order` = 1, `show_price` = 1, `visibility` = 'both', `indexed` = 1 WHERE (`quantity` > 0 AND `price_rozn` > 0) AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_nostock2);
      //Статусы, товар скрываем - 30/03/2020
      $sql_sapstatus = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'none', `indexed` = 0 WHERE sap_status !='АА' AND (sap_status !='BP')  AND (sap_status !='RT') AND (sap_status !=0) AND (`id_shop` = ".$id_shop.") ";
      Db::getInstance()->execute($sql_sapstatus);
      //Акции, нормализуем этот бред
      //Там где старая цена не адекватна, нормализируем - 30/03/2020
      $sql_actions_norm_old = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` SET `price_old` = `price_rozn`*1.15 WHERE `on_sale` = 1 AND (`price_old` <= `price_rozn`) AND (`id_shop` = ".$id_shop.") ";
      //Вычисляем разницу - 30/03/2020
      $sql_actions_amount = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` SET `amount`= `price_old` - `price_rozn` WHERE `on_sale` = 1 AND (`id_shop` = ".$id_shop.")";
      //Очищием непонятки - 30/03/2020
      $sql_actions_del_old = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` SET `price_old`= 0 WHERE `on_sale` = 0 AND (`id_shop` = ".$id_shop.")";       
      Db::getInstance()->execute($sql_actions_del_old);
      Db::getInstance()->execute($sql_actions_norm_old);
      Db::getInstance()->execute($sql_actions_amount);
      
      //Получаем ID неакционных товаров для обновления - 30/03/2020
      $sql_id_noa_to_update = "SELECT `id_product` FROM `"._DB_PREFIX_."fozzy_autoupdate` WHERE `id_shop` = ".$id_shop." AND (`on_sale` = 0)";
      $id_noas_from_base = Db::getInstance()->executeS($sql_id_noa_to_update);
      $id_noas_from_base_norm = array();
      foreach ($id_noas_from_base as $id_noas_from)
        {
         $id_noas_from_base_norm[]=$id_noas_from['id_product'];
        }
      $id_noa_to_update = implode(",",$id_noas_from_base_norm);
      //Получаем ID акционных товаров для обновления - 30/03/2020
      $id_a_to_update = implode(",",array_diff($ids_from_base_norm, $id_noas_from_base_norm));
      //Дата закрытия опта или акции в формате SQL - 30/03/2020
      $close_date = date('Y-m-d', time());
       
    //  die('tok-2');
      
      //Удаляем пометку акции по товарам в обновлении, где нет акцийй - 30/03/2020
      $sql_action_clear_shop = "UPDATE `"._DB_PREFIX_."product_shop` SET `on_sale` = 0 WHERE `id_shop` = ". $id_shop ." AND `id_product` IN (".$id_noa_to_update.")";
      Db::getInstance()->execute($sql_action_clear_shop);
      
      //Обновляем видимость в текущем магазине - 30/03/2020
      $sql_visible_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility`, p.`indexed` = e.`indexed` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) ";
      Db::getInstance()->execute($sql_visible_shop);
      //Обновляем цены в текущем магазине - 30/03/2020
      $sql_prices_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET  p.`price` = e.`price_rozn`, p.`wholesale_price` = e.`price_opt`, p.`z_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`)";
      Db::getInstance()->execute($sql_prices_shop);
      //Прописываем старую цену как основную для акционных позиций - 30/03/2020
      $sql_actions_shop = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_old`, p.`on_sale` = e.`on_sale` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND (e.`price_rozn` > 0) AND (e.`on_sale` = 1) AND ('".$tek_date."' BETWEEN e.`reduction_from` AND e.`reduction_to`)";
      Db::getInstance()->execute($sql_actions_shop);
      //Обновляем наличие в текущем магазине - 30/03/2020
      $sql_quantity = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (s.`id_shop` = e.`id_shop`)";
      Db::getInstance()->execute($sql_quantity);
      
      //IQOS
      $sql_iqos1 = "UPDATE "._DB_PREFIX_."product_shop` SET `price` = 149, `wholesale_price` = 149 WHERE `id_product` = 76411";
      $sql_iqos2 = "UPDATE `"._DB_PREFIX_."product_shop` SET `price` = 49, `wholesale_price` = 49 WHERE `id_product` = 76410";
      $sql_iqos3 = "UPDATE `"._DB_PREFIX_."product_shop` SET `price` = 1999, `wholesale_price` = 1999 WHERE `id_product` = 76409";
      $sql_iqos4 = "UPDATE `"._DB_PREFIX_."product_shop` SET `price` = 1999, `wholesale_price` = 1999 WHERE `id_product` = 76408";
      $sql_iqos5 = "UPDATE `"._DB_PREFIX_."product_shop` SET `price` = 1999, `wholesale_price` = 1999 WHERE `id_product` = 76407";
      $sql_iqos6 = "UPDATE `"._DB_PREFIX_."product_shop` SET `price` = 1999, `wholesale_price` = 1999  WHERE `id_product` = 76406";
      $sql_iqos7 = "UPDATE `"._DB_PREFIX_."product_shop` SET `price` = 999, `wholesale_price` = 999 WHERE `id_product` = 76405";
      $sql_iqos8 = "UPDATE `"._DB_PREFIX_."product_shop` SET `price` = 999, `wholesale_price` = 999 WHERE `id_product` = 76404";
      Db::getInstance()->execute($sql_iqos1);
      Db::getInstance()->execute($sql_iqos2);
      Db::getInstance()->execute($sql_iqos3);
      Db::getInstance()->execute($sql_iqos4);
      Db::getInstance()->execute($sql_iqos5);
      Db::getInstance()->execute($sql_iqos6);
      Db::getInstance()->execute($sql_iqos7);
      Db::getInstance()->execute($sql_iqos8);
      
      
      //Акции и опт
      
      //Закрываем акции по товарам в обновлении, где нет акций - 30/03/2020
      $sql_sp_close = "UPDATE `"._DB_PREFIX_."specific_price` SET `to` = '".$close_date."' WHERE `id_shop` = ".$id_shop." AND `price` = -1 AND `id_product` IN (".$id_noa_to_update.")";
      //Обновляем оптовые цены и количества в выдаче оптовых цен - 30/03/2020
      $sql_sp_opt_price = "UPDATE `"._DB_PREFIX_."specific_price` sp, `"._DB_PREFIX_."product_shop` p SET sp.`price` = p.`wholesale_price`, sp.`from_quantity` = p.`opt_kol` WHERE sp.`id_product` = p.`id_product` AND sp.`id_shop` = ".$id_shop." AND p.`id_shop` = ".$id_shop." AND sp.`price` > -1 AND p.`wholesale_price` > p.`mrс` AND sp.`id_product` IN (".$id_noa_to_update.")";
      //Закрываем опт по товарам с МРЦ больше оптовой цены - 30/03/2020
      $sql_sp_opt_price_mrc = "UPDATE `"._DB_PREFIX_."specific_price` sp, `"._DB_PREFIX_."product_shop` p SET sp.`price` = 0, sp.`from_quantity` = 0, `to` = '2001-01-01 00:00:00' WHERE sp.`id_product` = p.`id_product` AND sp.`id_shop` = ".$id_shop." AND p.`id_shop` = ".$id_shop." AND sp.`price` > -1 AND (p.`wholesale_price` < p.`mrс`)";
      //Открываем опт по товарам в обновлении, где нет акций - 30/03/2020
      $sql_sp_on = "UPDATE `"._DB_PREFIX_."specific_price` SET `to` = '0000-00-00 00:00:00' WHERE `id_shop` = ".$id_shop." AND `price` > 0 AND `from_quantity` > 0 AND `id_product` IN (".$id_noa_to_update.")";
      //Закрываем опт по товарам в обновлении, где есть акции - 30/03/2020
      $sql_sp_off = "UPDATE `"._DB_PREFIX_."specific_price` SET `to` = '".$close_date."' WHERE `id_shop` = ".$id_shop." AND `price` > -1 AND `id_product` IN (".$id_a_to_update.")";
      //Обновляем акционные цены в выдаче акций и включаем акции - 30/03/2020
      $sql_sp_opt_price_action = "UPDATE `"._DB_PREFIX_."specific_price` sp, `"._DB_PREFIX_."fozzy_autoupdate` p SET sp.`reduction` = p.`amount`, `from` = `reduction_from`, `to` = `reduction_to` WHERE sp.`id_product` = p.`id_product` AND sp.`price` = -1 AND sp.`id_shop` = ".$id_shop." AND p.`id_shop` = ".$id_shop." AND sp.`id_product` IN (".$id_a_to_update.")";

      Db::getInstance()->execute($sql_sp_close);
      Db::getInstance()->execute($sql_sp_opt_price);
      Db::getInstance()->execute($sql_sp_opt_price_mrc);
      Db::getInstance()->execute($sql_sp_on);
      Db::getInstance()->execute($sql_sp_off);
      Db::getInstance()->execute($sql_sp_opt_price_action);        
    //  dump($sql_sp_opt_price);
    //  die();

      //Спец. цены для ИМ
      //Обновляем цены в текущем магазине
      $sql_prices_shop_im = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate_online` e, `"._DB_PREFIX_."product_shop` p SET  p.`price` = e.`price_old` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` IN (1,2,3,4,8,9)) AND (now() BETWEEN e.`date_start` AND e.`date_stop`)";
      Db::getInstance()->execute($sql_prices_shop_im);
      //Проставляем ограничения
      $sql_sp_im = "UPDATE `"._DB_PREFIX_."specific_price` sp, `"._DB_PREFIX_."fozzy_autoupdate_online` p SET sp.`from` = p.`date_start`, sp.`to` = p.`date_stop`, sp.`price` = p.`price_retail`, sp.`from_quantity` = p.`factor` WHERE sp.`id_product` = p.`id_product` AND sp.`price` > -1 AND p.`factor` > 0 AND (now() BETWEEN p.`date_start` AND p.`date_stop`)";
      //Проставляем суперцены
      $sql_sp_im_action = "UPDATE `"._DB_PREFIX_."specific_price` sp, `"._DB_PREFIX_."fozzy_autoupdate_online` p SET sp.`reduction` = (p.`price_old` - p.`price`), `from` = `date_start`, `to` = `date_stop` WHERE sp.`id_product` = p.`id_product` AND sp.`price` = -1 AND (now() BETWEEN p.`date_start` AND p.`date_stop`)";
      Db::getInstance()->execute($sql_sp_im);
      Db::getInstance()->execute($sql_sp_im_action);
      


      
      //Обновляем Хореку - Киев
      if ($id_shop == 1) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility`, p.`indexed` = e.`indexed` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, z_price - используем для розницы )
      $sql_opt_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      $sql_roznica_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      $sql_vhod_shop_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`z_price` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (p.`id_shop` = 5)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_5 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 1) AND (s.`id_shop` = 5)";  
      Db::getInstance()->execute($sql_visible_shop_5);
      Db::getInstance()->execute($sql_opt_shop_5);
      Db::getInstance()->execute($sql_roznica_shop_5);
      Db::getInstance()->execute($sql_vhod_shop_5);
      Db::getInstance()->execute($sql_quantity_5);
      }                      
      //Обновляем Хореку - Одесса
      if ($id_shop == 2) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility`, p.`indexed` = e.`indexed` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, z_price - используем для розницы )
      $sql_opt_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      $sql_roznica_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      $sql_vhod_shop_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`z_price` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 6)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_6 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (s.`id_shop` = 6)";  
      Db::getInstance()->execute($sql_visible_shop_6);
      Db::getInstance()->execute($sql_opt_shop_6);
      Db::getInstance()->execute($sql_roznica_shop_6);
      Db::getInstance()->execute($sql_vhod_shop_6);
      Db::getInstance()->execute($sql_quantity_6);
      } 
      //Обновляем Хореку - Харьков
      if ($id_shop == 4) {
      //Обновляем видимость в текущем магазине
      $sql_visible_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility`, p.`indexed` = e.`indexed` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      //Обновляем цены в текущем магазине (розница - это опт, опт - это вход, z_price - используем для розницы )
      $sql_opt_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`wholesale_price` = e.`price_in` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      $sql_roznica_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price_opt` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      $sql_vhod_shop_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."product_shop` p SET p.`z_price` = e.`price_rozn` WHERE p.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (p.`id_shop` = 7)";
      //Обновляем наличие в текущем магазине
      $sql_quantity_7 = "UPDATE `"._DB_PREFIX_."fozzy_autoupdate` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (e.`id_shop` = 2) AND (s.`id_shop` = 7)";  
      Db::getInstance()->execute($sql_visible_shop_7);
      Db::getInstance()->execute($sql_opt_shop_7);
      Db::getInstance()->execute($sql_roznica_shop_7);
      Db::getInstance()->execute($sql_vhod_shop_7);
      Db::getInstance()->execute($sql_quantity_7);
      }
     
      //прячем товары не отображаемые в ИМ
      $sql_im = "UPDATE `"._DB_PREFIX_."product_shop` SET `available_for_order` = 0, `show_price` = 0, `indexed` = 0, `visibility` = 'none' WHERE `show_site` = 0 AND `id_shop` = ".$id_shop;
      Db::getInstance()->execute($sql_im);
      
      
      //test - ODesssa
      if ($id_shop == 2) {
        $sql_test_od = "UPDATE `"._DB_PREFIX_."product_shop` p, `"._DB_PREFIX_."stock_available` s SET p.`available_for_order` = 0, p.`show_price` = 0, p.`visibility` = 'both', p.`indexed` = 0 WHERE p.`id_shop` = 2 AND s.`id_shop` = 2 AND p.`id_shop` = s.`id_shop` AND p.`id_product` = s.`id_product` AND s.`quantity` <= p.`min_ost` AND p.`min_ost` > 0";
        Db::getInstance()->execute($sql_test_od);
      }  
      // Костыли - начало
      
        
        
        //Подарки
        $podarks = "UPDATE `"._DB_PREFIX_."stock_available` SET `quantity` = 100 WHERE `id_product` IN 57195";
        Db::getInstance()->execute($podarks);
        $podarks2 = "UPDATE `"._DB_PREFIX_."product_shop` SET `available_for_order` = 1, `show_price` = 1, `visibility` = 'both'  WHERE `id_product` = 57195";
        Db::getInstance()->execute($podarks2);
        
        //Подарки по 10 коп
        $sql_gifts = "UPDATE `"._DB_PREFIX_."product_shop` SET `available_for_order` = 1, `show_price` = 1, `indexed` = 0, `visibility` = 'none', `show_site` = 1 WHERE `id_product` IN (57195,57194,57193,57192,57191,57190,57189,57188,57187,57186,46059,41473,35594,34235,34234)";
        Db::getInstance()->execute($sql_gifts);
        
      // Костыли - конец
      $sql_truncs = "TRUNCATE TABLE `"._DB_PREFIX_."fozzy_autoupdate`";
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
  curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
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

    $filename = _PS_ROOT_DIR_.'/modules/fozzy_autoupdate/log/'.$filial."_".date('d_m_Y_H_i_s')."_prices.csv";
    $fp = fopen($filename, 'w');

    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    $headerss = array('ActivityDateFrom','ActivityDateTo','ActivityPriceBefore','DisplayOnFozzyShopSite','IsActivityBlocked','IsActivityEnable','SAPStatus','kolvoNow','lagerId','priceIn','priceOpt','priceRozn');
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
