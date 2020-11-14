<?php

if (!defined('_PS_VERSION_'))
	exit;

class FozzyFiskal extends Module
{
 private $id_shop;
 private $shop_list;
 private $_postErrors = array();

public function __construct()
	{
		$this->name = 'fozzyfiskal';
		$this->tab = 'quick_bulk_update';
		$this->version = '1.0.0';
		$this->author = 'Novevision.com, Britoff A.';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Fozzy Fiskal Update');
		$this->description = $this->l('Add fiskal from Excel 2007 (Rubicon format) files');
  }
  
public function install()
	{
		if (!parent::install()
    || !$this->registerHook('DisplayBackOfficeHeader')
    || !Configuration::updateValue('fz_fiskal_detail', 0)
    )
    return false;
    
    $this->shop_list = Shop::getShops();
    
    foreach ($this->shop_list as $shop)
    {
          $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzyfiskaltable_'. $shop['id_shop'] .'`';
          Db::getInstance()->execute($sql_drop);
          $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."fozzyfiskaltable_". $shop['id_shop'] ."` (
              `id_fiskal` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `id_order` int(10) NOT NULL DEFAULT '0',
              `id_shop` int(10) NOT NULL DEFAULT '1',
              `price` decimal(20,2) NOT NULL DEFAULT '0.00',
              `date_upd` DATETIME NOT NULL DEFAULT '2001-01-01 00:00:00', 
              PRIMARY KEY (`id_fiskal`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
          Db::getInstance()->execute($sql_create);
          $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzyfiskaltable_det_'. $shop['id_shop'] .'`';
          Db::getInstance()->execute($sql_drop);
          $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."fozzyfiskaltable_det_". $shop['id_shop'] ."` (
              `id_fiskal` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `fiskal_num` varchar(50) NOT NULL DEFAULT '0',
              `id_order` int(10) NOT NULL DEFAULT '0',
              `id_shop` int(10) NOT NULL DEFAULT '1',
              `reference` int(15) NOT NULL DEFAULT '0',
              `qty` decimal(20,4) NOT NULL DEFAULT '0.0000',
              `price` decimal(20,2) NOT NULL DEFAULT '0.00',
              `summa` decimal(20,2) NOT NULL DEFAULT '0.00',
              `date_upd` DATETIME NOT NULL DEFAULT '2001-01-01 00:00:00', 
              PRIMARY KEY (`id_fiskal`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
          Db::getInstance()->execute($sql_create);
		}
    return true;
  
  }

public function uninstall()
	{
    foreach ($this->shop_list as $shop)
    {
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzyfiskaltable_'. $shop['id_shop'] .'`';
    Db::getInstance()->execute($sql_drop);
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fozzyfiskaltable_det_'. $shop['id_shop'] .'`';
    Db::getInstance()->execute($sql_drop);
    }
    Configuration::deleteByName('fz_fiskal_detail');
		return (parent::uninstall()
  		);
	}

  public function getContent()
	{
  
    //$this->context->controller->addJS($this->_path.'fozzyfiskal.js');
		//$this->context->controller->addCSS($this->_path.'fozzyfiskal.css');
		$this->_html = '';
    $this->id_shop = (int)Shop::getContextShopID();
    
    // Обнуление таблицы чеков  ----------------------------------------------------------
    if (Tools::isSubmit('btnReloadFiskal'))
		{
     $sql_drop = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzyfiskaltable_'. $this->id_shop .'`';
     Db::getInstance()->execute($sql_drop);
     Configuration::updateValue('PS_FOZZYFISKAL_UPLOAD', 0);
     Configuration::updateValue('fz_fiskal_detail', 0);
		}
    // Загрузка таблицы чеков ----------------------------------------------------------
    if (Tools::isSubmit('btnSubmitFiskalU'))
		{
      $fz_det = Tools::GetValue('fz_fiskal_detail');
      Configuration::updateValue('fz_fiskal_detail', $fz_det);

      $this->_postValidationFiskal();
			if (!count($this->_postErrors))
				{
        $this->_postProcessFiskal();
         if (!count($this->_postErrors))
				  {
          $OK_MESSAGE = $this->l('Upload Fiskal OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          $OK_MESSAGE = '';
          Configuration::updateValue('PS_FOZZYFISKAL_UPLOAD', 1);
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
    // Обработка файла чеков 
    if (Tools::isSubmit('btnSubmitFiskal'))
		{
       $fz_det = Tools::GetValue('fz_fiskal_detail');
       Configuration::updateValue('fz_fiskal_detail', $fz_det);
       
       if ($fz_det) {
        $sql_drop = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzyfiskaltable_det_'. $this->id_shop .'`';
       }
       else
       {
        $sql_drop = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzyfiskaltable_'. $this->id_shop .'`';
       }
       Db::getInstance()->execute($sql_drop);

       
       $file = dirname(__FILE__).'/upload/fiskal.xlsx';
       require_once "classes/simplexlsx.class.php";
       $xlsx = new SimpleXLSX( $file );
       $fiskalfilecontent = $xlsx->rows();
    //   d($fiskalfilecontent);
    
       if (!$fz_det) {
       $sqlinsert = 'INSERT INTO `'._DB_PREFIX_.'fozzyfiskaltable_'. $this->id_shop .'` (`id_order`,`id_shop`,`price`) VALUES ';
       foreach ( $fiskalfilecontent as $key=>$row )
        {
            if (!is_int($row[20])) continue;
            $sqlinsert.= "($row[20] , $this->id_shop , $row[8]),";
        }
       $sqlinsert = substr($sqlinsert, 0, -1);
   //    d($sqlinsert);
       unset ($xlsx);
       unset ($fiskalfilecontent);
       unset ($row);
       $upload = Db::getInstance()->execute($sqlinsert);
       unset ($sqlinsert);
        
      if (!$upload) $this->_postErrors[] = $this->l('Error in Excel file.');
      
      $sql_sum = 'SELECT id_order , id_shop , SUM(price) as price FROM '._DB_PREFIX_.'fozzyfiskaltable_'. $this->id_shop .' WHERE id_shop = '.$this->id_shop.' GROUP BY id_order ORDER BY id_order';
      $sql_temp = 'CREATE TEMPORARY TABLE foo AS '.$sql_sum;
      $sql_ins = 'INSERT INTO '._DB_PREFIX_.'fozzyfiskaltable_'. $this->id_shop .' SELECT NULL,`id_order`, `id_shop`, `price`, NOW() FROM foo';
      $sql_dr = 'DROP TABLE foo';
      
      $sql_drop = 'TRUNCATE TABLE `'._DB_PREFIX_.'fozzyfiskaltable_'. $this->id_shop .'`';
 //     d($sql_ins);
      Db::getInstance()->execute($sql_temp);
      Db::getInstance()->execute($sql_drop);
      Db::getInstance()->execute($sql_ins);
      Db::getInstance()->execute($sql_dr);
      
      $sql_upd = 'UPDATE '._DB_PREFIX_.'fozzyfiskaltable_'. $this->id_shop .' f, '._DB_PREFIX_.'orders o SET o.fiskal = f.price WHERE o.id_order = f.id_order';
      $upd = Db::getInstance()->execute($sql_upd);
  //    d($sql_ins);
      if (!$upd) $this->_postErrors[] = $this->l('Error in Base.');
      }
      else
      {

   //    d($fiskalfilecontent);
       $sqlinsert = 'INSERT INTO `'._DB_PREFIX_.'fozzyfiskaltable_det_'. $this->id_shop .'` (`fiskal_num`,`id_order`,`id_shop`,`reference`,`qty`,`price`,`summa`) VALUES ';
       foreach ( $fiskalfilecontent as $key=>$row )
        {
            if (!is_int($row[21])) continue;
            $sqlinsert.= "($row[3], $row[21] , $this->id_shop , $row[7], $row[10], $row[11], $row[12]),";
        }
       $sqlinsert = substr($sqlinsert, 0, -1);
   //    d($sqlinsert);
       unset ($xlsx);
       unset ($fiskalfilecontent);
       unset ($row);
       $upload = Db::getInstance()->execute($sqlinsert);
       unset ($sqlinsert);
        
      if (!$upload) $this->_postErrors[] = $this->l('Error in Excel file.');
      
      }
      if (!count($this->_postErrors))
				  {
          $OK_MESSAGE = $this->l('Update Fiskal OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          $OK_MESSAGE = '';
          Configuration::updateValue('PS_FOZZYFISKAL_UPLOAD', 0);
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

private function _postValidationFiskal()
	{
   if (isset($_FILES['PS_FOZZYFISKAL_FILE']['tmp_name']) && !empty($_FILES['PS_FOZZYFISKAL_FILE']['tmp_name'])) // Проверка на Excel
		{
			if (!(substr($_FILES['PS_FOZZYFISKAL_FILE']['name'], -5) == '.xlsx'))
				$this->_postErrors[] = $this->l('File must be in XLSX format.');
		}
		return !count($this->_postErrors) ? true : false;
	}

private function _postProcessFiskal()
	{
   $target_file = dirname(__FILE__)."/upload/fiskal.xlsx";

   if (move_uploaded_file($_FILES['PS_FOZZYFISKAL_FILE']['tmp_name'], $target_file)) {
    } else {
        $this->_postErrors[] = $this->l('Sorry, there was an error uploading your file.');
    }
    
    return !count($this->_postErrors) ? true : false;
	}

  
public function renderForm()
	{       

      $manual = '<p align="center"><strong>Загрузите файл с чеками.</strong></p>';
      $manual.= '<p align="left">Шаблон файла: <a href="/modules/'.$this->name.'/files/fiskal.xlsx">Скачать</a></p>';
      $manual.= '<p align="left">Чеки будут загружены в <strong>2 этапа</strong> - сначала загрузка файла, потом обновление заказов.</p>';
      $fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Fiskal Import'),
					'icon' => 'icon-cogs'
				),
				'description' =>$manual,
        'input' => array(
          array(
						'type' => 'file',
						'label' => $this->l('File to import:'),
						'name' => 'PS_FOZZYFISKAL_FILE',
						'desc' => $this->l('Must be in XLSX format'),
					),
          array(
						'type' => 'switch',
						'label' => $this->l('Детализация чека?'),
						'name' => 'fz_fiskal_detail',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'fz_fiskal_detail_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'fz_fiskal_detail_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
        )
				
		  );
      
      if ( Configuration::get('PS_FOZZYFISKAL_UPLOAD') == 1 )
        {
         unset($fields_form[0]['form']['input'][0]);
         $fields_form[0]['form']['buttons'] = array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Submit Fiskal File'),
                    'icon' => 'process-icon-update',
                    'name' => 'btnSubmitFiskal',
                    'id'   => 'btnSubmitFiskal',
                    'class'=> 'pull-right'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Reload Fiskal File'),
                    'icon' => 'process-icon-update',
                    'name' => 'btnReloadFiskal',
                    'id'   => 'btnReloadFiskal',
                    'class'=> 'pull-right'
                )
            );
        }
        else
        {
        $fields_form[0]['form']['buttons'] = array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Upload Fiskal File'),
                    'icon' => 'process-icon-upload',
                    'name' => 'btnSubmitFiskalU',
                    'id'   => 'btnSubmitFiskalU',
                    'class'=> 'pull-right'
                )
         );
        
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
			'fields_value' => array(
			'fz_fiskal_detail' => Configuration::get('fz_fiskal_detail'),
		),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm($fields_form);
    
  }




}