<?php

if (!defined('_PS_VERSION_'))
	exit;

class FozzyPriceUpd extends Module
{
 private $id_shop;
 private $shop_list;
 private $_postErrors = array();

public function __construct()
	{
		$this->name = 'fozzypriceupd';
		$this->tab = 'quick_bulk_update';
		$this->version = '1.0.0';
		$this->author = 'Novevision.com, Britoff A.';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Fozzy Rubicon Price Update');
		$this->description = $this->l('Add price from Excel 2007 (Rubicon format) files');
  }
  
public function install()
	{
		if (!parent::install()
    || !$this->registerHook('DisplayBackOfficeHeader')
    )
    return false;
    
    $this->shop_list = Shop::getShops();
    
    foreach ($this->shop_list as $shop)
    {
          $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzypricetable_'. $shop['id_shop'] .'`';
          Db::getInstance()->execute($sql_drop);
          $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzyactionstable_'. $shop['id_shop'] .'`';
          Db::getInstance()->execute($sql_drop);
          $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."fozzypricetable_". $shop['id_shop'] ."` (
              `id_excel` int(10) unsigned NOT NULL AUTO_INCREMENT,                  
              `id_product` int(10) NOT NULL DEFAULT '0',                            
              `reference` varchar(32),                                              
              `id_shop` int(10) NOT NULL DEFAULT '1',                               
              `active` tinyint(1) NOT NULL DEFAULT '1',                             
              `price` decimal(20,2) NOT NULL DEFAULT '0.00',                        
              `wholesale_price` decimal(20,2) NOT NULL DEFAULT '0.00',              
              `input_price` decimal(20,2) NOT NULL DEFAULT '0.00',                  
              `on_sale` tinyint(1)  NOT NULL DEFAULT '0',                           
              `quantity` decimal(20,2) NOT NULL DEFAULT '0',                        
              `available_for_order` tinyint(1) NOT NULL DEFAULT '1',                
              `show_price` tinyint(1)  NOT NULL DEFAULT '1',                        
              `visibility` varchar(32) NOT NULL DEFAULT 'both',                     
              `sapstatus` varchar(32),                                              
              `amount` decimal(20,2) NOT NULL DEFAULT '0.00',                       
              `reduction_from` datetime DEFAULT NULL,                               
              `reduction_to` datetime DEFAULT NULL,                                 
              `date_upd` datetime NOT NULL DEFAULT '2001-01-01 00:00:00',           
              PRIMARY KEY (`id_excel`),
              KEY `id_product` (`id_product`),
              KEY `reference` (`reference`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
          Db::getInstance()->execute($sql_create);
          $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."fozzyactionstable_". $shop['id_shop'] ."` (
              `id_excel` int(10) unsigned NOT NULL AUTO_INCREMENT,                       
              `id_product` int(10) NOT NULL DEFAULT '0',                                 
              `reference` varchar(32),                                                   
              `id_shop` int(10) NOT NULL DEFAULT '1',                                    
              `fake_price_old` decimal(20,2) NOT NULL DEFAULT '0.00',                    
              `price_old` decimal(20,2) NOT NULL DEFAULT '0.00',                         
              `price` decimal(20,2) NOT NULL DEFAULT '0.00',                             
              `reduction_from` datetime DEFAULT NULL,                                    
              `reduction_to` datetime DEFAULT NULL,                                      
              `date_upd` DATETIME NOT NULL DEFAULT '2001-01-01 00:00:00',                
              PRIMARY KEY (`id_excel`),
              KEY `id_product` (`id_product`),
              KEY `reference` (`reference`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
          Db::getInstance()->execute($sql_create);
		}
    return true;
  
  }

public function uninstall()
	{
    foreach ($this->shop_list as $shop)
    {
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzyactionstable_'. $shop['id_shop'] .'`';
    Db::getInstance()->execute($sql_drop);
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzypricetable_'. $shop['id_shop'] .'`';
    Db::getInstance()->execute($sql_drop);
    }
		return (parent::uninstall()
  		);
	}

  public function getContent()
	{
  
    //$this->context->controller->addJS($this->_path.'fozzypriceupd.js');
		//$this->context->controller->addCSS($this->_path.'fozzypriceupd.css');
		$this->_html = '';
    $this->id_shop = (int)Shop::getContextShopID();
    
    // Обнуление файла акций ----------------------------------------------------------
    if (Tools::isSubmit('btnReloadActions'))
		{
     $sql_drop = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzyactionstable_'. $this->id_shop .'`';
     Db::getInstance()->execute($sql_drop);
     Configuration::updateValue('PS_FOZZYPRICEUPD_ACTIONS', 0);
		}
    // Загрузка файла акций ----------------------------------------------------------
    if (Tools::isSubmit('btnUploadActions'))
		{
      
      $this->_postValidationAction();
			if (!count($this->_postErrors))
				{
        $this->_postProcessAction();
         if (!count($this->_postErrors))
				  {
          $OK_MESSAGE = $this->l('Upload Actions OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          $OK_MESSAGE = '';
          Configuration::updateValue('PS_FOZZYPRICEUPD_ACTIONS', 1);
          }
         else
          {
           foreach ($this->_postErrors as $err)
					   $this->_html .= $this->displayError($err);
          }
				}
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);
		}
    // Обработка файла акций 
    if (Tools::isSubmit('btnSubmitActions'))
		{
       $sql_drop = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzyactionstable_'. $this->id_shop .'`';
       Db::getInstance()->execute($sql_drop);
       
       $file = dirname(__FILE__).'/upload/actions.xlsx';
       require_once "classes/simplexlsx.class.php";
       $xlsx = new SimpleXLSX( $file );
       $actionfilecontent = $xlsx->rows();
 
       $sqlinsert = 'INSERT INTO `'._DB_PREFIX_.'fozzyactionstable_'. $this->id_shop .'`(`reference`, `id_shop`, `price_old`, `reduction_from`,`reduction_to`,`date_upd`) VALUES ';
       foreach ( $actionfilecontent as $key=>$row )
        {
            if ($key == 0) continue;
            $row[1] = $this->ExcelToSQLDate($row[1]);
            $row[2] = $this->ExcelToSQLDatePlus($row[2]);
            $row[3] = round((float)$row[3],2);
            $sqlinsert.= "('$row[0]' , $this->id_shop , $row[3], '$row[1]', '$row[2]','".date('Y-m-d H:i:s')."'),";
        }
       $sqlinsert = substr($sqlinsert, 0, -1);
       unset ($xlsx);
       unset ($actionfilecontent);
       unset ($row);
       $upload = Db::getInstance()->execute($sqlinsert);
       unset ($sqlinsert);
        
       $sql_updid = "UPDATE `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` f, `"._DB_PREFIX_."product` p SET f.`id_product` = p.`id_product` WHERE f.`reference` = p.`reference`";
       Db::getInstance()->execute($sql_updid);
    
      if (!$upload) $this->_postErrors[] = $this->l('Error in Excel file.');
      
      if (!count($this->_postErrors))
				  {
          $OK_MESSAGE = $this->l('Update Actions OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          $OK_MESSAGE = '';
          }
         else
          {
           foreach ($this->_postErrors as $err)
					   $this->_html .= $this->displayError($err);
          }
    
		}
    // Загрузка файла цен ----------------------------------------------------------
    Configuration::updateValue('PS_FOZZYPRICEUPD_PRI', 0);
    if (Tools::isSubmit('btnUploadPrice'))
		{
      
      $this->_postValidationPrice();
			if (!count($this->_postErrors))
				{
        $this->_postProcessPrice();
         if (!count($this->_postErrors))
				  {
          $OK_MESSAGE = $this->l('Upload Price OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          $OK_MESSAGE = '';
          Configuration::updateValue('PS_FOZZYPRICEUPD_PRI', 1);
          }
         else
          {
           foreach ($this->_postErrors as $err)
					   $this->_html .= $this->displayError($err);
          }
				}
			else {
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);}
    
	}
  // Обработка файла цен 
    if (Tools::isSubmit('btnPreparePrice'))
		{
       set_time_limit(10000);
       $sql_drop = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzypricetable_'. $this->id_shop .'`';
       Db::getInstance()->execute($sql_drop);
       
       if ($this->id_shop == 1) {
       $sql_drop_5 = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzypricetable_5`';
       Db::getInstance()->execute($sql_drop_5);
       }
       if ($this->id_shop == 2) {
       $sql_drop_6 = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzypricetable_6`';
       Db::getInstance()->execute($sql_drop_6);
       }
       if ($this->id_shop == 4) {
       $sql_drop_7 = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzypricetable_7`';
       Db::getInstance()->execute($sql_drop_7);
       }
       
       $file = dirname(__FILE__).'/upload/price.xlsx';
       require_once "classes/simplexlsx.class.php";
       $xlsx = new SimpleXLSX( $file );
       $pricefilecontent = $xlsx->rows();

       $sqlinsert = 'INSERT INTO `'._DB_PREFIX_.'fozzypricetable_'. $this->id_shop .'`(`reference`, `id_shop`, `price`,`wholesale_price`,`input_price`, `quantity`, `sapstatus`) VALUES ';
       foreach ( $pricefilecontent as $key=>$row )
        {
            if ($key == 0) continue;
            $row[8] = (float)$row[8];
            $row[7] = (float)$row[7];
            $row[10] = (float)$row[10];
            $row[12] = round(abs((float)$row[12]),2);
            $sqlinsert.= "('$row[0]' , $this->id_shop , $row[8], $row[10], $row[7], $row[12], '$row[18]'),";
        }
       $sqlinsert = substr($sqlinsert, 0, -1);
       unset ($xlsx);
       unset ($pricefilecontent);
       unset ($row);
       $uploadp = Db::getInstance()->execute($sqlinsert);
       unset ($sqlinsert);
       
       // Сопоставляем товары 
       $sql_updidp = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` f, `"._DB_PREFIX_."product` p SET f.`id_product` = p.`id_product` WHERE f.`reference` = p.`reference`";
       Db::getInstance()->execute($sql_updidp);
       //Удаляем лишнее
       $sql_delnoid = "DELETE FROM `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` WHERE `id_product` = 0";
       Db::getInstance()->execute($sql_delnoid);
      
      if (!$uploadp) $this->_postErrors[] = $this->l('Error in Excel file.');
      
      if (!count($this->_postErrors))
				  {
          $OK_MESSAGE = $this->l('Prepare Price OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          $OK_MESSAGE = '';
          Configuration::updateValue('PS_FOZZYPRICEUPD_PRI', 2);
          }
         else
          {
           foreach ($this->_postErrors as $err)
					   $this->_html .= $this->displayError($err);
          }
    
		}
    
// Обновление цен 
    if (Tools::isSubmit('btnSubmitPrice'))
		{
      $shopsik = '1';
      if ($this->id_shop == 1) {
       $shopsik = '1,5';
      }
      if ($this->id_shop == 2) {
       $shopsik = '2,6';
      }
      if ($this->id_shop == 3) {
       $shopsik = '3';
      }
      if ($this->id_shop == 4) {
       $shopsik = '4,7';
      }
      $sql_clear_all = "UPDATE `"._DB_PREFIX_."product_shop` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'none' WHERE `id_shop` IN (".$shopsik.") ";
      $sql_nostock = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'catalog' WHERE `quantity` = 0 ";
      $sql_sapstatus = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` SET `available_for_order` = 0, `show_price` = 0, `visibility` = 'none' WHERE sapstatus !='АА' AND (sapstatus !='BP') ";
      $sql_actp = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` f, `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` fa SET fa.`price` = f.`price` WHERE fa.`id_product` = f.`id_product` "; 
      $sql_actsale = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` f, `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` fa SET f.`on_sale` = 1 WHERE fa.`id_product` = f.`id_product` AND ( (fa.`price_old` - fa.`price`) > 0  ) "; 
      $sql_actsalegon = "UPDATE `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` SET `fake_price_old` = `price` * (ROUND((RAND() * 40)+10)/100 + 1) WHERE (`price_old` - `price`) <= 0 "; 
      $sql_actsale_am = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` f, `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` fa SET f.`reduction_from` = fa.`reduction_from`,f.`reduction_to` =  fa.`reduction_to`, f.`amount` = (fa.`price_old` - fa.`price`) WHERE fa.`id_product` = f.`id_product` AND ( fa.`fake_price_old` = 0  ) "; 
      $sql_actsale_amf= "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` f, `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` fa SET f.`reduction_from` = fa.`reduction_from`,f.`reduction_to` =  fa.`reduction_to`, f.`amount` = (fa.`fake_price_old` - fa.`price`) WHERE fa.`id_product` = f.`id_product` AND ( fa.`fake_price_old` > 0  ) "; 
      $sql_price_shop = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`price`, p.`wholesale_price` = e.`wholesale_price`, p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = e.`visibility` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) ";
      $sql_quantity = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (s.`id_shop` = e.`id_shop`)";

      if ($this->id_shop == 1) {
        $sql_price_shop5 = "UPDATE `"._DB_PREFIX_."fozzypricetable_1` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`wholesale_price`, p.`wholesale_price` = e.`input_price`, p.`unit_price_ratio` = e.`price`, p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = 'both' WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = 5) ";
        $sql_quantity5 = "UPDATE `"._DB_PREFIX_."fozzypricetable_1` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (s.`id_shop` = 5)";
      }
      if ($this->id_shop == 2) {
        $sql_price_shop6 = "UPDATE `"._DB_PREFIX_."fozzypricetable_2` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`wholesale_price`, p.`wholesale_price` = e.`input_price`, p.`unit_price_ratio` = e.`price`, p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = 'both' WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = 6) ";
        $sql_quantity6 = "UPDATE `"._DB_PREFIX_."fozzypricetable_2` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (s.`id_shop` = 6)";
      }
      if ($this->id_shop == 4) {
        $sql_price_shop7 = "UPDATE `"._DB_PREFIX_."fozzypricetable_4` e, `"._DB_PREFIX_."product_shop` p SET p.`price` = e.`wholesale_price`, p.`wholesale_price` = e.`input_price`, p.`unit_price_ratio` = e.`price`, p.`available_for_order` = e.`available_for_order`, p.`show_price` = e.`show_price`, p.`visibility` = 'both' WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = 7) ";
        $sql_quantity7 = "UPDATE `"._DB_PREFIX_."fozzypricetable_4` e, `"._DB_PREFIX_."stock_available` s SET s.`quantity` = e.`quantity` WHERE s.`id_product` = e.`id_product` AND (s.`id_shop` = 7)";
      }

// Акции -------------------------------------------------------------------------------------------------------------------------------------   
      $sql_action_clear_shop = "UPDATE `"._DB_PREFIX_."product_shop` SET `on_sale` = 0 WHERE `id_shop` = ". $this->id_shop;
      $sql_action_shop = "UPDATE `"._DB_PREFIX_."product_shop` p, `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` e SET p.`on_sale` = e.`on_sale` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`)";
      $sql_action_shop_price = "UPDATE `"._DB_PREFIX_."product_shop` p, `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` e SET p.`price` = e.`price_old` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND ( e.`fake_price_old` = 0 )";
      $sql_action_shop_fake_price = "UPDATE `"._DB_PREFIX_."product_shop` p, `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` e SET p.`price` = e.`fake_price_old` WHERE p.`id_product` = e.`id_product` AND (p.`id_shop` = e.`id_shop`) AND ( e.`fake_price_old` > 0 )";
    
      $sql_sp_clear = "DELETE FROM `"._DB_PREFIX_."specific_price` WHERE `id_shop` = ".$this->id_shop;
      $sql_sp_clear2 = "TRUNCATE TABLE `"._DB_PREFIX_."specific_price_priority`";

    
      $sql_sp_add = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $this->id_shop .", 0, 0, 0, 0, 0, 0, -1, 1, `amount`, 1, 'amount', `reduction_from`, `reduction_to`
      FROM `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."`
      WHERE `amount` > 0
      ";
      $sql_sp_add2 = "INSERT INTO `"._DB_PREFIX_."specific_price_priority`(`id_product`, `priority`)
      SELECT `id_product`, 'id_shop;id_currency;id_country;id_group'
      FROM `"._DB_PREFIX_."product` WHERE 1 ";    
// ОПТ
      $sql_opt_prices = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`opt_price_". $this->id_shop ."` = e.`wholesale_price` WHERE p.`id_product` = e.`id_product` ";
      $sql_opt_actions_clear = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $this->id_shop ."` = 0 WHERE 1";
      $sql_opt_actions_no_buhlo = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $this->id_shop ."` = 1 WHERE p.`category` IN (300333, 300334, 300335, 300336, 300337, 300338, 300526, 300339, 300340, 300341, 300342, 300345, 300346, 300347, 300348, 300349, 300350, 300351, 300352, 300353, 300354, 300355, 300357, 300358, 300359, 300360, 300361, 300362, 300366, 300389, 300390, 300391, 300613, 300603, 300370)";
      $sql_opt_actions_no_buhlo_exlude = "UPDATE `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $this->id_shop ."` = 0 WHERE p.`category` IN (300333, 300334, 300335, 300336, 300337, 300338, 300526, 300345, 300346, 300347, 300348, 300349, 300350, 300351, 300352, 300353, 300354, 300355, 300357, 300358, 300359, 300360, 300361, 300362, 300366, 300389, 300390, 300391, 300613, 300603) AND (p.`opt_price_". $this->id_shop ."` > p.`min_price`) ";

      $sql_opt_actions = "UPDATE `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $this->id_shop ."` = 1 WHERE p.`id_product` = e.`id_product`";
      $sql_opt_hernia = "UPDATE `"._DB_PREFIX_."fozzypricetable_". $this->id_shop ."` e, `"._DB_PREFIX_."fozzyprices_opt` p SET p.`action_". $this->id_shop ."` = 1 WHERE p.`id_product` = e.`id_product` AND (e.`price` = e.`wholesale_price`) ";
      $date_time_version = "SET SESSION sql_mode = ''";
      $sql_opt__sp_add4 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $this->id_shop .", 0, 0, 0, 4, 0, 0, `opt_price_". $this->id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $this->id_shop ."` = 0 AND (`opt_price_". $this->id_shop ."` > 0)
      ";
      $sql_opt__sp_add3 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $this->id_shop .", 0, 0, 0, 3, 0, 0, `opt_price_". $this->id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $this->id_shop ."` = 0 AND (`opt_price_". $this->id_shop ."` > 0)
      ";
      $sql_opt__sp_add2 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $this->id_shop .", 0, 0, 0, 2, 0, 0, `opt_price_". $this->id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $this->id_shop ."` = 0 AND (`opt_price_". $this->id_shop ."` > 0)
      ";
      $sql_opt__sp_add1 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
      SELECT 0, 0, `id_product`, ". $this->id_shop .", 0, 0, 0, 1, 0, 0, `opt_price_". $this->id_shop ."`, `qty`, '0.000000', 1, 'amount', '0000-00-00 00:00:00', '0000-00-00 00:00:00'
      FROM `"._DB_PREFIX_."fozzyprices_opt`
      WHERE `action_". $this->id_shop ."` = 0 AND (`opt_price_". $this->id_shop ."` > 0)
      ";

     
     Db::getInstance()->execute($sql_clear_all);
     Db::getInstance()->execute($sql_nostock);
     Db::getInstance()->execute($sql_sapstatus);
     Db::getInstance()->execute($sql_actp);
     Db::getInstance()->execute($sql_actsale);
     Db::getInstance()->execute($sql_actsalegon);  
     Db::getInstance()->execute($sql_actsale_am);
     Db::getInstance()->execute($sql_actsale_amf);
     
     Db::getInstance()->execute($sql_price_shop);
     Db::getInstance()->execute($sql_quantity);
     Db::getInstance()->execute($sql_action_clear_shop);
     Db::getInstance()->execute($sql_action_shop);
     Db::getInstance()->execute($sql_action_shop_price);
     Db::getInstance()->execute($sql_action_shop_fake_price);
     
     Db::getInstance()->execute($sql_sp_clear);
     Db::getInstance()->execute($sql_sp_clear2);
     Db::getInstance()->execute($sql_sp_add);
     Db::getInstance()->execute($sql_sp_add2);
     
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
     
     if ($this->id_shop == 1) {
        Db::getInstance()->execute($sql_price_shop5);
        Db::getInstance()->execute($sql_quantity5);
      }
      if ($this->id_shop == 2) {
        Db::getInstance()->execute($sql_price_shop6);
        Db::getInstance()->execute($sql_quantity6);
      }
      if ($this->id_shop == 4) {
        Db::getInstance()->execute($sql_price_shop7);
        Db::getInstance()->execute($sql_quantity7);
      }
     
     //Очищаем кеш
     Tools::clearSmartyCache();
     Tools::clearXMLCache();
     Media::clearCache();
     Tools::generateIndex();
     //------------
     
      if (!count($this->_postErrors))
				  {
          $OK_MESSAGE = $this->l('Update Price OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          $OK_MESSAGE = '';
          Configuration::updateValue('PS_FOZZYPRICEUPD_PRI', 0);
          }
         else
          {
           foreach ($this->_postErrors as $err)
					   $this->_html .= $this->displayError($err);
          }
    
    
    }    
    
    
    		$this->_html .= $this->renderForm();

		return $this->_html;
}
private function ExcelToSQLDate($exceldate)
	{
    $sqldate = gmdate("Y-m-d H:i:s", ($exceldate - 25569) * 86400);
    if (!$sqldate|| $sqldate == '1899-12-30 00:00:00') $sqldate = '0000-00-00 00:00:00';
    return $sqldate;
  }
                               
private function ExcelToSQLDatePlus($exceldate)
	{
    $sqldate = gmdate("Y-m-d H:i:s", (($exceldate - 25569) * 86400) + 86399 );
    if (!$sqldate|| $sqldate == '1899-12-30 00:00:00') $sqldate = '0000-00-00 00:00:00';
    return $sqldate;
  }  

private function _postValidationAction()
	{
   if (isset($_FILES['PS_FOZZYPRICEUPD_ACTIONSFILE']['tmp_name']) && !empty($_FILES['PS_FOZZYPRICEUPD_ACTIONSFILE']['tmp_name'])) // Проверка на Excel
		{
			if (!(substr($_FILES['PS_FOZZYPRICEUPD_ACTIONSFILE']['name'], -5) == '.xlsx'))
				$this->_postErrors[] = $this->l('File must be in XLSX format.');
		}
		return !count($this->_postErrors) ? true : false;
	}

private function _postProcessAction()
	{

   $target_file = dirname(__FILE__)."/upload/actions.xlsx";

   if (move_uploaded_file($_FILES['PS_FOZZYPRICEUPD_ACTIONSFILE']['tmp_name'], $target_file)) {
    } else {
        $this->_postErrors[] = $this->l('Sorry, there was an error uploading your file.');
    }
    
    return !count($this->_postErrors) ? true : false;
	}
private function _postValidationPrice()
	{
   if (isset($_FILES['PS_FOZZYPRICEUPD_PRICEFILE']['tmp_name']) && !empty($_FILES['PS_FOZZYPRICEUPD_PRICEFILE']['tmp_name'])) // Проверка на Excel
		{
			if (!(substr($_FILES['PS_FOZZYPRICEUPD_PRICEFILE']['name'], -5) == '.xlsx'))
				$this->_postErrors[] = $this->l('File must be in XLSX format.');
		}
		return !count($this->_postErrors) ? true : false;
	}

private function _postProcessPrice()
	{

   $target_file = dirname(__FILE__)."/upload/price.xlsx";

   if (move_uploaded_file($_FILES['PS_FOZZYPRICEUPD_PRICEFILE']['tmp_name'], $target_file)) {
    } else {
        $this->_postErrors[] = $this->l('Sorry, there was an error uploading your file.');
    }
    
    return !count($this->_postErrors) ? true : false;
	}
  
public function renderForm()
	{       

    $sql_action_detect = "SELECT MAX(DATE_FORMAT(`date_upd`, '%d-%m-%Y')) AS Date FROM `"._DB_PREFIX_."fozzyactionstable_". $this->id_shop ."` ";
    $detect_action = Db::getInstance()->executeS($sql_action_detect);
    if ($detect_action) $last_update = $detect_action[0]['Date'];
    else $last_update = 0;
    
    if ($last_update)
    {
      $manual = '<p align="center"><strong>Внимание.</strong></p>';
      $manual.= '<p align="left">Акции были загружены '.$last_update."</p>";
      $manual.= '<p align="left">Для обновления акций нажмите кнопку <strong>Перезагрузить</strong>.</p>';
      $fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Actions Import'),
					'icon' => 'icon-cogs'
				),
				'description' =>$manual,
				'buttons' => array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Reload Actions File'),
                    'icon' => 'process-icon-update',
                    'name' => 'btnReloadActions',
                    'id'   => 'btnReloadActions',
                    'class'=> 'pull-right'
                )
            )
		  );
    }
    else
    {
      $manual = '<p align="center"><strong>Загрузите файл с акциями.</strong></p>';
      $manual.= '<p align="left">Шаблон фала: <a href="/modules/'.$this->name.'/files/actions.xlsx">Скачать</a></p>';
      $manual.= '<p align="left">Акции будут загружены в <strong>2 этапа</strong> - сначала загрузка файла, потом обновление акционных позиций.</p>';
      $fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Actions Import'),
					'icon' => 'icon-cogs'
				),
				'description' =>$manual,
        'input' => array(
          array(
						'type' => 'file',
						'label' => $this->l('File to import:'),
						'name' => 'PS_FOZZYPRICEUPD_ACTIONSFILE',
						'desc' => $this->l('Must be in XLSX format'),
					)
        )
				
		  );
      
      if ( Configuration::get('PS_FOZZYPRICEUPD_ACTIONS') == 1 )
        {
         unset($fields_form[0]['form']['input']);
         $fields_form[0]['form']['buttons'] = array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Submit Actions File'),
                    'icon' => 'process-icon-update',
                    'name' => 'btnSubmitActions',
                    'id'   => 'btnSubmitActions',
                    'class'=> 'pull-right'
                )
            );
        }
        else
        {
        $fields_form[0]['form']['buttons'] = array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Upload Actions File'),
                    'icon' => 'process-icon-upload',
                    'name' => 'btnUploadActions',
                    'id'   => 'btnUploadActions',
                    'class'=> 'pull-right'
                )
         );
        
        }
    }
    
    
    $manual = '<p align="center"><strong>Загрузите файл с ценами из <strong>ПО РУБИКОН</strong>.</strong></p>';
    $manual.= '<p align="left">Акции будут загружены в <strong>3 этапа</strong> - сначала загрузка файла, проверка и обновление цен.</p>'; 
      
    $fields_form[1]['form'] = array(
				'legend' => array(
					'title' => $this->l('Price Import'),
					'icon' => 'icon-cogs'
				),
				'description' =>$manual,
        'input' => array(
          array(
						'type' => 'file',
						'label' => $this->l('File to import:'),
						'name' => 'PS_FOZZYPRICEUPD_PRICEFILE',
						'desc' => $this->l('Must be in XLSX format'),
					)
        ),
				'buttons' => array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Upload Price File'),
                    'icon' => 'process-icon-upload',
                    'name' => 'btnUploadPrice',
                    'id'   => 'btnUploadPrice',
                    'class'=> 'pull-right'     
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Submit Prepare File'),
                    'icon' => 'process-icon-update',
                    'name' => 'btnPreparePrice',
                    'id'   => 'btnPreparePrice',
                    'class'=> 'pull-right'
                )
                ,
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Submit Price File'),
                    'icon' => 'process-icon-update',
                    'name' => 'btnSubmitPrice',
                    'id'   => 'btnSubmitPrice',
                    'class'=> 'pull-right'
                )
            )
		  );
    
     if ( Configuration::get('PS_FOZZYPRICEUPD_PRI') == 0 )
      {
       unset($fields_form[1]['form']['buttons'][1]);
       unset($fields_form[1]['form']['buttons'][2]);
      }
      if ( Configuration::get('PS_FOZZYPRICEUPD_PRI') == 1 )
      {
       unset($fields_form[1]['form']['buttons'][0]);
       unset($fields_form[1]['form']['buttons'][2]);
       unset($fields_form[1]['form']['input']);
      }
      if ( Configuration::get('PS_FOZZYPRICEUPD_PRI') == 2 )
      {
       unset($fields_form[1]['form']['buttons'][0]);
       unset($fields_form[1]['form']['buttons'][1]);
       unset($fields_form[1]['form']['input']);
      }
    
     if ($this->id_shop == 0)
     {
      unset($fields_form[1]['form']);
      unset($fields_form[0]['form']);
      $manual_shop = "Модуль нельзя использовать для нескольких магазинов. Выберите конкретный магазин.";
      $fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Price Import'),
					'icon' => 'icon-cogs'
				),
				'description' =>$manual_shop
		  );
      
      
     }
     
     
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
	//		'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm($fields_form);
  
  
  
  
    
  }




}