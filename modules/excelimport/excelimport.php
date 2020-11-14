<?php

if (!defined('_PS_VERSION_'))
	exit;

class Excelimport extends Module
{

private $id_lang;
//private $id_shop;
private $_postErrors = array();
	
public function __construct()
	{
		$this->name = 'excelimport';
		$this->tab = 'quick_bulk_update';
		$this->version = '2.7.8';
		$this->author = 'Novevision.com, Britoff A.';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Excel import-export');
		$this->description = $this->l('Add export-import products from Excel 2007 (.xslx) files');
  }
    
public function install()
	{
		if (!parent::install()
    || !$this->registerHook('DisplayBackOfficeHeader')
    )
			return false;
		Configuration::updateValue('PS_EXCELFILE_OPT', 'newproducts');
		Configuration::updateValue('PS_EXCEL_MAN', 0);
    Configuration::updateValue('PS_EXCEL_CUR', 0);
    Configuration::updateValue('PS_EXCEL_ART', 0);
    Configuration::updateValue('PS_EXCEL_CLSALE', 0);
    Configuration::updateValue('PS_EXCEL_CUR_E', 0);
    Configuration::updateValue('PS_EXCEL_OFFSET', 1);                      
		Configuration::updateValue('PS_EXCEL_SUP', 0);
    Configuration::updateValue('PS_EXCELFILE_UPL', 0);
    Configuration::updateValue('PS_EXCEL_CLHTML', 0);
		Configuration::updateValue('PS_EXCEL_CAT', 2);
		Configuration::updateValue('PS_EXCEL_LANG_I', Configuration::get('PS_LANG_DEFAULT'));
		Configuration::updateValue('PS_EXCEL_LANG_E', Configuration::get('PS_LANG_DEFAULT'));  
    Configuration::updateValue('PS_EXCEL_DESCR', 1);
    Configuration::updateValue('PS_EXCEL_OFFPROD', 0);
    Configuration::updateValue('PS_EXCEL_CF', 0);
    Configuration::updateValue('PS_EXCEL_EXCOMB', 0);
    
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'exceltable`';
    Db::getInstance()->execute($sql_drop);
    $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."exceltable` (
        `id_excel` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `id_product` int(10),
        `id_lang` int(10) NOT NULL DEFAULT '1',
        `id_shop` int(10) NOT NULL DEFAULT '1',
        `active` tinyint(1) NOT NULL DEFAULT '1',
        `name` varchar(128),
        `categories` text,
        `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `on_sale` tinyint(1)  NOT NULL DEFAULT '0',
        `reduction_s` decimal(20,6),
        `reduction_p` decimal(20,6),
        `reduction_from` datetime,
        `reduction_to` datetime,
        `reference` varchar(32),
        `supplier_reference` varchar(32),
        `supplier` varchar(128),
        `manufacturer` varchar(128),
        `ean13` varchar(13),
        `width` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `height` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `depth` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
        `quantity` decimal(20,6) NOT NULL DEFAULT '0',
        `description_short` text,
        `description` text,
        `images` text,
        `images_del` tinyint(1) NOT NULL DEFAULT '0',
        `features` text,
        `online_only` tinyint(1) NOT NULL DEFAULT '0',
        `available_for_order` tinyint(1) NOT NULL DEFAULT '1',
        `available_now` varchar(255) DEFAULT NULL,
        `available_later` varchar(255) DEFAULT NULL,
        `meta_title` varchar(128) DEFAULT NULL,
        `meta_keywords` varchar(255) DEFAULT NULL,
        `meta_description` varchar(255) DEFAULT NULL,
        `link_rewrite` varchar(128) DEFAULT NULL,
        `tovar` int(20) NOT NULL DEFAULT '1',
        `cur` int(20) NOT NULL DEFAULT '1',
        `date_upd` datetime NOT NULL,
        `offset` int(10) NOT NULL DEFAULT '1',
        `checkin` int(10) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id_excel`),
        KEY `date_upd` (`date_upd`)
      ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
      Db::getInstance()->execute($sql_create);
		return true;
	}

public function uninstall()
	{
    $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'exceltable`';
    Db::getInstance()->execute($sql_drop);
		return (parent::uninstall()
			&& Configuration::deleteByName('PS_EXCELFILE_OPT')
			&& Configuration::deleteByName('PS_EXCEL_MAN')
      && Configuration::deleteByName('PS_EXCEL_CUR')
      && Configuration::deleteByName('PS_EXCEL_ART')
      && Configuration::deleteByName('PS_EXCEL_CLSALE')
      && Configuration::deleteByName('PS_EXCEL_CUR_E')
      && Configuration::deleteByName('PS_EXCEL_OFFSET')
			&& Configuration::deleteByName('PS_EXCEL_SUP')
			&& Configuration::deleteByName('PS_EXCEL_CAT')
      && Configuration::deleteByName('PS_EXCEL_CLHTML')
      && Configuration::deleteByName('PS_EXCELFILE_UPL')
			&& Configuration::deleteByName('PS_EXCEL_LANG_E')
			&& Configuration::deleteByName('PS_EXCEL_LANG_I')
      && Configuration::deleteByName('PS_EXCEL_DESCR')
      && Configuration::deleteByName('PS_EXCEL_OFFPROD')
      && Configuration::deleteByName('PS_EXCEL_CF')
      && Configuration::deleteByName('PS_EXCEL_EXCOMB')
  		);
	}

public function getContent()
	{
  
    $this->context->controller->addJS($this->_path.'excelimport.js');
		//$this->context->controller->addCSS($this->_path.'back.css');
		$this->_html = '';
    Configuration::updateValue('PS_EXCELFILE_UPL', 0);
    Configuration::updateValue('PS_EXCEL_OFFSET', 1);
    
    $sql_row_offset = 'SELECT MAX(`offset`) AS offset FROM `'._DB_PREFIX_.'exceltable`'; 
    $row_offset = Db::getInstance()->getRow($sql_row_offset);
    
    if ( isset($row_offset) && $row_offset['offset'] > 1) 
      {
        Configuration::updateValue('PS_EXCEL_OFFSET', $row_offset['offset']);
      }

    // Загрузка файла ----------------------------------------------------------
    if (Tools::isSubmit('btnUpload'))
		{
    
      $clean_html = Tools::getValue('clean_html');
      Configuration::updateValue('PS_EXCEL_CLHTML', $clean_html);
      $lang_i = Tools::getValue('lang_i');
      Configuration::updateValue('PS_EXCEL_LANG_I', $lang_i);
      $import_option = Tools::getValue('import_options');
      Configuration::updateValue('PS_EXCELFILE_OPT', $import_option);
      $offprod = Tools::getValue('offprod');
      Configuration::updateValue('PS_EXCEL_OFFPROD', $offprod);
      $clcf = Tools::getValue('clearfeatures');
      Configuration::updateValue('PS_EXCEL_CF', $clcf);
      $cur = Tools::getValue('cur');
      Configuration::updateValue('PS_EXCEL_CUR', $cur);
      $art = Tools::getValue('art');
      Configuration::updateValue('PS_EXCEL_ART', $art);
      $clsale = Tools::getValue('clsale');
      Configuration::updateValue('PS_EXCEL_CLSALE', $clsale);
      
      $this->_postValidation();
			if (!count($this->_postErrors))
				{
        $this->_postProcess();
         if (!count($this->_postErrors))
				  {
          
          $OK_MESSAGE = $this->l('Upload OK');
          $this->_html .= $this->displayConfirmation($OK_MESSAGE);
          
          Configuration::updateValue('PS_EXCELFILE_UPL', 1);
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
    // -------------------------------------------------------------------------
		if (Tools::isSubmit('btnExport'))
		{
      $manufacturer = Tools::getValue('manufacturer');
  		$supplier = Tools::getValue('supplier');
  		$category = Tools::getValue('category');
  		$lang_e = Tools::getValue('lang_e');
      $descr = Tools::getValue('descr');            
      $export_comb = Tools::getValue('export_comb');
      $cur_e = Tools::getValue('cur_e');
      Configuration::updateValue('PS_EXCEL_CUR_E', $cur_e);
      Configuration::updateValue('PS_EXCEL_LANG_E', $lang_e);
      Configuration::updateValue('PS_EXCEL_MAN', $manufacturer);
      Configuration::updateValue('PS_EXCEL_SUP', $supplier);
      Configuration::updateValue('PS_EXCEL_CAT', $category);
      Configuration::updateValue('PS_EXCEL_DESCR', $descr);
      Configuration::updateValue('PS_EXCEL_EXCOMB', $export_comb);
      $this->pre_export($manufacturer,$supplier,$category,$descr,$export_comb);
  		$this->_html .= $this->displayConfirmation($this->l('Export OK'));
    }
    
		$this->_html .= $this->renderForm();

		return $this->_html;
	}
	
private function _getCategories($id_category = 1, $id_lang, $id_shop = -1, $recursive = true)
    {
        if ($id_shop == -1) $id_shop = (int)$this->context->shop->id;
        
        $category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);

        if (is_null($category->id))
            return;

        if ($recursive){
            $children = Category::getChildren((int) $id_category, (int) $id_lang, true, (int) $id_shop);
            if ($category->level_depth == 0) {
                $depth = $category->level_depth;
            } else {
                $depth = $category->level_depth - 1;
            }

            $spacer = str_repeat('&mdash;', 1 * $depth);
        }

        $this->_categorySelect[] = array(
            'value' =>  (int) $category->id,
            'name' => (isset($spacer) ? $spacer : '') . $category->name
        );

        if (isset($children) && count($children)){
            foreach ($children as $child){
                $this->_getCategories((int) $child['id_category'], $id_lang,(int) $child['id_shop'], true);
            }
        }
    } 
    
public function renderForm()
	{

    $languages = $this->context->language->getLanguages();
    
    $manual='<span><p align="center"><strong>Инструкция.</strong>';
    $manual.='</p>';
    $manual.='<ol>';
    $manual.='<li><strong>Принцип работы импорта.</strong><br />Загрузка проходит в 3 этапа. Загрузка файла на сервер, подготова файла и собственно импорт. Разделение на этапы необходимо для совместимости со всеми типами хостингов.</li>';
    $manual.='<li><strong>Новые товары.</strong><br />При импорте новых товаров используются все колонки файла кроме ID.</li>';
    $manual.='<li><strong>Полное обновление.</strong><br />При полном обновлении используются все колонки, кроме: | Категории | Удалить старые фото.<br /><strong>Важно:</strong> Если скидка в файле не указана, а в товаре в магазине присутствует, она не удаляется, поля просто игнорируются.<br /><strong>Важно:</strong> Остаток не суммируется, а заменяется. Если остаток товара в магазине – 1 шт., а Вы указали в файле 2 шт., итоговый остаток будет 2 шт.<br /><strong>Важно:</strong> Не работает с раширенным управлением складами.<br /><strong>Важно:</strong> Перезаписывается постоянная сслыка (если она не указана) на основании названия товара!</li>';
    $manual.='<li><strong>Обновление цен.</strong><br />При обновлении цен используются следующие колонки: ID или Артикул или EAN13 | Статус (0 - неактивен/1 - активен) | Цена | Закупочная цена | Распродажа (0/1) | Скидка (сумма) | Скидка (процент) | Скидка с (yyyy-mm-dd) | Скидка по (yyyy-mm-dd) | Код валюты (для модуля мультивалют).<br /><strong>Важно:</strong> Если скидка в файле не указана, а в товаре в магазине присутствует, она не удаляется, поля просто игнорируются.</li>';
    $manual.='<li><strong>Обновление цен и количеств.</strong><br />При обновлении цен и количеств используются следующие колонки: ID или Артикул или EAN13 | Статус (0 - неактивен/1 - активен) | Цена | Закупочная цена | Распродажа (0/1) | Скидка (сумма) | Скидка (процент) | Скидка с (yyyy-mm-dd) | Скидка по (yyyy-mm-dd) | Количество. | Доступен для заказа | Текст когда товар на складе | Текст когда товара нет на складе | Код валюты (для модуля мультивалют)<br /><strong>Важно:</strong> Если скидка в файле не указана, а в товаре в магазине присутствует, она не удаляется, поля просто игнорируются.<br /><strong>Важно:</strong> Остаток не суммируется, а заменяется. Если остаток товара в магазине – 1 шт., а Вы указали в файле 2 шт., итоговый остаток будет 2 шт.<br /><strong>Важно:</strong> Не работает с раширенным управлением складами.</li>';
    $manual.='<li><strong>Обновление количеств.</strong><br />При обновлении количеств используются следующие колонки: ID или Артикул или EAN13 | Количество | Доступен для заказа | Текст когда товар на складе | Текст когда товара нет на складе .<br /><strong>Важно:</strong> Остаток не суммируется, а заменяется. Если остаток товара в магазине – 1 шт., а Вы указали в файле 2 шт., итоговый остаток будет 2 шт.</li>';
    $manual.='<li><strong>Обновление описаний.</strong><br />При обновлении описаний используются следующие колонки: ID или Артикул | Короткое описание | Описание.</li>';
    $manual.='<li><strong>Обновление коротких описаний.</strong><br />При обновлении коротких описаний используются следующие колонки: ID или Артикул | Короткое описание.</li>';
    $manual.='<li><strong>Обновление названий.</strong><br />При обновлении названий товаров используются следующие колонки: ID или Артикул | Название.<br /><strong>Важно:</strong> Перезаписывается постоянная сслыка на основании названия товара!</li>';
    $manual.='<li><strong>Обновление артикулов.</strong><br />При обновлении артикулов товаров используются следующие колонки: ID | Артикул.</li>';
    $manual.='<li><strong>Обновление штрикодов по артикулу.</strong><br />При обновлении штрикодов товаров используются следующие колонки: Артикул | EAN13.</li>';
    $manual.='<li><strong>Обновление изображений.</strong><br />При обновлении изображений товаров используются следующие колонки: ID или Артикул|Ссылки на изображения (x,y,z...)|Удалить старые фото (0=Нет, 1=Да). Не для комбинаций</li>';
    $manual.='<li><strong>Обновление поставщика.</strong><br />При обновлении поставщика используются следующие колонки: ID | Артикул поставщика | Поставщик</li>';
    $manual.='<li><strong>Обновление производителя.</strong><br />При обновлении поставщика используются следующие колонки: ID | Производитель</li>';
    $manual.='<li><strong>Обновление характеристик.</strong><br />При обновлении характеристик используются следующие колонки: ID или Артикул | Характеристики</li>';
    $manual.='<li><strong>Обновление SEO.</strong><br />При обновлении SEO используются следующие колонки: ID или Артикул | Мета-заголовок | Мета-ключевые слова | Мета-описание | ЧПУ.</li>';
    $manual.='<li><strong>Комбинации товаров.</strong><br />Для обновлений Артикула, Изображений, Цен, Количеств, Цен и количеств и при полном обновлении можно производить обновление вомбинаций товаров. Используемые поля: ID | Цена | Закупочная цена |Артикул | Штрихкод | Признак Товар\Комбинация.<br /><strong>Важно:</strong> Поле Признак Товар\Комбинация заполняется так: 1 если товар, ID комбинации, если комбинация</li>';
    $manual.='</ol>';
    $manual.="<p><strong>Важно:</strong> Во всех случаях необходимо заполнять поле - Признак Товар\Комбинация. Оно заполняется так: 1 если товар, ID комбинации, если комбинация</p>";
    $manual.="<p><strong>Важно:</strong> Обновление комбинаций только по ID товара и комбинации!</p>";
    $manual.="<p><strong>Важно:</strong> При очистке от HTML очищается весь код, за исключением тегов a, img, p, br, strong, b. Атрибуты тегов также удаляются.</p>";
    $manual.="<p><strong>Важно:</strong> Нельзя использовать в значениях полей символы - <strong> одинарная кавычка (') </strong>(нигде, совсем нельзя)</p>";
    $manual.='<p><strong>Очень важно:</strong> Не удаляйте и не меняйте местами столбцы в файле для импорта. По расположению и количеству столбцов он должен строго соответствовать шаблону.</p>';
    $manual.='<br/>'.$this->l('Template file to import:').' <a href="/modules/'.$this->name.'/files/products.xlsx">'.$this->l('Download').'</a></span>'; 

    // <-- Данные для экспорта -->
    $root_category = Category::getRootCategory(Configuration::get('PS_EXCEL_LANG_E'));
    $this->_getCategories($root_category->id_category, Configuration::get('PS_EXCEL_LANG_E'));
    
    $export_file_link = $this->_listFiles();
    if ($export_file_link)
    $exp_mes = $this->l('File is formed and ready to download.')." ".$export_file_link;
    else $exp_mes = '';
    
    $man = Manufacturer::getManufacturers(false,Configuration::get('PS_EXCEL_LANG_E'));
    
    $manu = array();
    $manu[0]['id_manufacturer'] = 0;
    $manu[0]['name'] = $this->l('All');
    for ($i=0;$i<count($man);$i++)
      {
        $manu[$i+1]['id_manufacturer'] = $man[$i]['id_manufacturer'];
        $manu[$i+1]['name'] = $man[$i]['name'];
      }
      
    $sup = Supplier::getSuppliers(false,Configuration::get('PS_EXCEL_LANG_E'));
    $supp = array();
    $supp[0]['id_supplier'] = 0;
    $supp[0]['name'] = $this->l('All');
    for ($i=0;$i<count($sup);$i++)
      {
        $supp[$i+1]['id_supplier'] = $sup[$i]['id_supplier'];
        $supp[$i+1]['name'] = $sup[$i]['name'];
      }
    // <-- /Данные для экспорта -->  
    
    $shopdomain = $this->context->shop->domain;
    
		$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Excel Import'),
					'icon' => 'icon-cogs'
				),
				'description' =>$manual,
        'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Choose language:'),
						'name' => 'lang_i',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $languages,
							'id' => 'id_lang',
							'name' => 'name'
						),
					),
          array(
						'type' => 'file',
						'label' => $this->l('File to import:'),
						'name' => 'PS_EXCELFILE_NEW',
						'desc' => $this->l('Must be in XLSX format'),
					),
          array(
						'type' => 'hidden',
						'name' => 'PS_EXCELFILE_UPL',
					),
          array(
						'type' => 'hidden',
						'name' => 'url',
            'id'  => 'url',
					),
          array(
						'type' => 'hidden',
						'name' => 'offset',
            'id'   => 'offset',
					),
          array(
						'type' => 'hidden',
						'name' => 'offset_c',
            'id'   => 'offset_c',
					),
          array(
						'type' => 'hidden',
						'name' => 'barpr',
            'id'   => 'barpr',
					),
          array(
						'type' => 'hidden',
						'name' => 'loadtext',
            'id'   => 'loadtext',
					),
          array(
						'type' => 'hidden',
						'name' => 'id_shop',
            'id'   => 'id_shop',
					),
					array(
						'type' => 'select',
						'label' => $this->l('Choose You import type:'),
						'name' => 'import_options',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => array(
								array(
									'id' => 'newproduct',
									'name' => $this->l('New products')
								),
                array(
									'id' => 'fullupdate',
									'name' => $this->l('Full update')
								),
								array(
									'id' => 'updateprice',
									'name' => $this->l('Price update')
								),
                array(
									'id' => 'updatepra',
									'name' => $this->l('Update prices by reference')
								),
                array(
									'id' => 'updatepriceq',
									'name' => $this->l('Price and quantity update')
								),
                array(
									'id' => 'updatepriceqfozzy',
									'name' => $this->l('Fozzy')
								),
                array(
									'id' => 'updatepriceqfozzysales',
									'name' => $this->l('Fozzy with Sales')
								),  
                array(
									'id' => 'updatepraq',
									'name' => $this->l('Update price and quantity by reference')
								),
                array(
									'id' => 'updatepraean',
									'name' => $this->l('Update prices by reference by EAN13')
								),
                array(
									'id' => 'updatepraqean',
									'name' => $this->l('Update price and quantity by EAN13')
								),
                array(
									'id' => 'updatemassean',
									'name' => $this->l('Mass update by EAN13')
								),
								array(
									'id' => 'updatemass',
									'name' => $this->l('Mass update')
								),
                array(
									'id' => 'updatemassa',
									'name' => $this->l('Mass update by reference')
								),
								array(
									'id' => 'updatedescription',
									'name' => $this->l('Description update')
								),
                array(
									'id' => 'updatedescriptiona',
									'name' => $this->l('Description update by reference')
								),
                array(
									'id' => 'updateean13a',
									'name' => $this->l('EAN13 update by reference')
								),
                array(
									'id' => 'updateshortdescription',
									'name' => $this->l('Short description update')
								),
                array(
									'id' => 'updateshortdescriptiona',
									'name' => $this->l('Short description by reference')
								),
								array(
									'id' => 'updatenames',
									'name' => $this->l('Names update')
								),
                array(
									'id' => 'updatenamesa',
									'name' => $this->l('Names update by reference')
								),
                array(
									'id' => 'updatereference',
									'name' => $this->l('References update')
								),
                array(
									'id' => 'updateimages',
									'name' => $this->l('Images update')
								),
                array(
									'id' => 'updateimagesa',
									'name' => $this->l('Images update by reference')
								),
								array(
									'id' => 'updatesupplier',
									'name' => $this->l('Supplier update')
								),
                array(
									'id' => 'updatemanufacturer',
									'name' => $this->l('Manufacturer update')
								),
                array(
									'id' => 'updateseo',
									'name' => $this->l('SEO update')
								),
                array(
									'id' => 'updateseoa',
									'name' => $this->l('SEO update by reference')
								),
                array(
									'id' => 'updatefeatures',
									'name' => $this->l('Features update')
								),
                array(
									'id' => 'updatefeaturesa',
									'name' => $this->l('Features by reference update')
								)
							),
							'id' => 'id',
							'name' => 'name',
						)
					),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Off not updated products:'),
  					'name' => 'offprod',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'offprod_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'offprod_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Multicurrency module is installed?'),
  					'name' => 'cur',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'cur_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'cur_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Check uniq references?'),
  					'name' => 'art',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'art_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'art_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Clear prices drops?'),
  					'name' => 'clsale',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'clsale_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'clsale_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Clean HTML in descriptions:'),
  					'name' => 'clean_html',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'clean_html_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'clean_html_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Clear features before update:'),
  					'name' => 'clearfeatures',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'clearfeatures_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'clearfeatures_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          
          ),
				'buttons' => array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Upload File'),
                    'icon' => 'process-icon-upload',
                    'name' => 'btnUpload',
                    'id'   => 'btnUpload',
                    'class'=> 'pull-right'
                ),
                array(
                    'type' => 'button',
                    'title'=> $this->l('Prepare file'),
                    'icon' => 'process-icon-update',
                    'name' => 'PrepareFile',
                    'id'   => 'PrepareFile',
                    'class'=> 'pull-right'
                ),
                array(
                    'type' => 'button',
                    'title'=> $this->l('Continue import'),
                    'icon' => 'process-icon-download',
                    'name' => 'ImportContinue',
                    'id'   => 'ImportContinue',
                    'class'=> 'pull-right'
                ),
                array(
                    'type' => 'button',
                    'title'=> $this->l('Import products'),
                    'icon' => 'process-icon-download',
                    'name' => 'ImportProducts',
                    'id'   => 'ImportProducts',
                    'class'=> 'pull-right'
                ),
                array(
                    'type' => 'button',
                    'title'=> $this->l('Stop Import'),
                    'icon' => 'process-icon-minus',
                    'name' => 'ImportStop',
                    'id'   => 'ImportStop',
                    'class'=> 'pull-right'
                ),
            )
		  );
      
    if ($shopdomain != 'fozzyshop.com.ua')
    {
     unset($fields_form[0]['form']['input'][9]['options']['query'][5]);
     unset($fields_form[0]['form']['input'][9]['options']['query'][6]);
    }
    
    $fields_form[1]['form'] = array(
				'legend' => array(
					'title' => $this->l('Excel Export'),
					'icon' => 'icon-cogs'
				),
				'description' => $exp_mes,
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Choose language:'),
						'name' => 'lang_e',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $languages,
							'id' => 'id_lang',
							'name' => 'name'
						),
					),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Multicurrency module is installed?'),
  					'name' => 'cur_e',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'cur_e_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'cur_e_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          array(
						'type' => 'select',
						'label' => $this->l('Choose manufacturer:'),
						'name' => 'manufacturer',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $manu,
							'id' => 'id_manufacturer',
							'name' => 'name'
						),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Choose supplier:'),
						'name' => 'supplier',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $supp,
							'id' => 'id_supplier',
							'name' => 'name'
						),	
					),
					array(
						'type' => 'select',
						'label' => $this->l('Choose category:'),
						'name' => 'category',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $this->_categorySelect,
							'id' => 'value',
							'name' => 'name'
						),
					),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Export descriptoins:'),
  					'name' => 'descr',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'descr_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'descr_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ),
          array(
  					'type' => 'switch',
  					'label' => $this->l('Export combinations:'),
  					'name' => 'export_comb',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'export_comb_on',
  							'value' => 1,
  							'label' => $this->l('Yes')),
  						array(
  							'id' => 'export_comb_off',
  							'value' => 0,
  							'label' => $this->l('No')),
  					),
            'validation' => 'isBool',
				  ), 
					
          ),
					
				'submit' => array(
					'title' => $this->l('Export'),
					'id' => 'btnExport',
					'name' => 'btnExport'
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

		return $helper->generateForm($fields_form);
	}

public function getConfigFieldsValues()
	{
		$config_fields = array(
			'import_options' => Tools::getValue('import_options', Configuration::get('PS_EXCELFILE_OPT')),
			'manufacturer' => Tools::getValue('manufacturer', Configuration::get('PS_EXCEL_MAN')),
			'supplier' => Tools::getValue('supplier', Configuration::get('PS_EXCEL_SUP')),
			'category' => Tools::getValue('category', Configuration::get('PS_EXCEL_CAT')),
			'lang_i' => Tools::getValue('lang_i', Configuration::get('PS_EXCEL_LANG_I')),
			'lang_e' => Tools::getValue('lang_e', Configuration::get('PS_EXCEL_LANG_E')),
      'clean_html' => Tools::getValue('clean_html', Configuration::get('PS_EXCEL_CLHTML')),
      'url' => 'upload/toprepare.xlsx',
      'loadtext' => $this->l('Loading...'),
      'offset' => 0,
      'offset_c' => Configuration::get('PS_EXCEL_OFFSET'),
      'id_shop' => (int)$this->context->shop->id,
      'barpr' => 0,
			'PS_EXCEL_KOL' => Tools::getValue('PS_EXCEL_KOL', Configuration::get('PS_EXCEL_KOL')),
      'PS_EXCELFILE_UPL' => Configuration::get('PS_EXCELFILE_UPL'),
      'offprod' => Tools::getValue('offprod', Configuration::get('PS_EXCEL_OFFPROD')),
      'clearfeatures' => Tools::getValue('clearfeatures', Configuration::get('PS_EXCEL_CF')), 
      'cur' => Tools::getValue('cur', Configuration::get('PS_EXCEL_CUR')),
      'art' => Tools::getValue('art', Configuration::get('PS_EXCEL_ART')),
      'clsale' => Tools::getValue('clsale', Configuration::get('PS_EXCEL_CLSALE')),
      'cur_e' => Tools::getValue('cur_e', Configuration::get('PS_EXCEL_CUR_E')), 
      'descr' => Tools::getValue('descr', Configuration::get('PS_EXCEL_DESCR')),
      'export_comb' => Tools::getValue('export_comb', Configuration::get('PS_EXCEL_EXCOMB'))
		);
		return $config_fields;
	}

private function _postValidation()
	{
   if (isset($_FILES['PS_EXCELFILE_NEW']['tmp_name']) && !empty($_FILES['PS_EXCELFILE_NEW']['tmp_name'])) // Проверка на Excel
		{
			if (!(substr($_FILES['PS_EXCELFILE_NEW']['name'], -5) == '.xlsx'))
				$this->_postErrors[] = $this->l('File must be in XLSX format.');
		}
		return !count($this->_postErrors) ? true : false;
	}

private function _postProcess()
	{

   $target_file = dirname(__FILE__)."/upload/toprepare.xlsx";

   if (move_uploaded_file($_FILES['PS_EXCELFILE_NEW']['tmp_name'], $target_file)) {
      //  echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        $this->_postErrors[] = $this->l('Sorry, there was an error uploading your file.');
    }
    
    return !count($this->_postErrors) ? true : false;
	}

public function pre_export($manufacturer,$supplier,$category,$descr,$export_comb)
	{
  set_time_limit(500);	
	//global $cookie;
	$lang = Configuration::get('PS_EXCEL_LANG_E');
  $p_m = array();
  $p_s = array();
  $p_c = array();
  $p_SM = array();
  $p_SMP = array();
  $attributes_array = array();
  $acounter = 0;
  
  if ($supplier)
    {
    $sup = new Supplier($supplier);
    $sup_products = $sup->getProductsLite($lang);
    foreach ($sup_products as $product)
      {
      $p_s[]=(int)$product['id_product'];
      }
    $p_SMP = $p_s;
    }
  
   if ($manufacturer)
    {
    $man = new Manufacturer($manufacturer);
    $man_products = $man->getProductsLite($lang);
    foreach ($man_products as $product)
      {
      $p_m[]=(int)$product['id_product'];
      }
    $p_SMP = $p_m;
      if ($p_s) 
        {
        $p_SM = array_intersect ($p_s,$p_m);
        $p_SMP = $p_SM;
        }
    }
   $cat_products_s = array ();
   if ($category > 2)
    {
    $cat = new Category($category);
    $cat_products = $cat->getProducts($lang,1,1000000,null,null,false,false);
    if ($cat_products) 
      {
      foreach ($cat_products as $cat_product)
        {
        $cat_products_s[$cat_product['id_product']] = $cat_product;
        }
      }
    $cat_products = $cat_products_s;
    unset ($cat_products_s);
    unset ($cat);
    $cat_products_s = array ();
 //   p($cat_products); 
    if (Category::hasChildren ($category,$lang,true,true))
      {
        $childrens = Category::getChildren ($category,$lang,true,true);
        foreach ($childrens as $children)
          {
            $cat = new Category($children['id_category']);
            $children_products = $cat->getProducts($lang,1,1000000,null,null,false,false);
            foreach ($children_products as $children_product)
                {
                $cat_products_s[$children_product['id_product']] = $children_product;
                }
            $cat_products += $cat_products_s;
           // d($childrens);
          }
      }
    
 //   d($cat_products);
    foreach ($cat_products as $product)
      {
      $p_c[]=(int)$product['id_product'];
      }
       if ($p_SM) $p_SMP = array_intersect ($p_SM,$p_c);
       else if ($p_m && !$p_SM) $p_SMP = array_intersect ($p_c,$p_m);
       else if ($p_s && !$p_SM) $p_SMP = array_intersect ($p_c,$p_s);
       else if (!$p_s && !$p_m) $p_SMP = $p_c;
    } 
    
 // if (!$p_SMP) return;
  
  $products_to_export = array_values($p_SMP);
  $products_num = count($products_to_export);
  if (!$products_num) 
    {
    $products_to_export =array();
    $sql_p = 'SELECT id_product FROM '._DB_PREFIX_.'product';
    $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_p);
    foreach ($rq as $product_i)
      {
      $products_to_export[]=(int)$product_i['id_product'];
      }
    $products_num = count($products_to_export);
    }
 sort($products_to_export);
// $kol_to_page = 1000;
 
    $products = implode(",",$products_to_export);

    $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name  ';
    $sql.= 'FROM `'._DB_PREFIX_.'product` p ';
    $sql.= 'INNER JOIN '._DB_PREFIX_.'product_shop product_shop ';
    $sql.= 'ON (product_shop.id_product = p.id_product AND product_shop.id_shop = 1) ';
    $sql.= 'LEFT JOIN `ps_product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.id_shop = '.(int)$this->context->shop->id.' ) ';
    $sql.= 'LEFT JOIN `ps_manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`) ';
    $sql.= 'LEFT JOIN `ps_supplier` s ON (s.`id_supplier` = p.`id_supplier`) ';
    $sql.= 'WHERE pl.`id_lang` = '.$lang.' ';
    $sql.= 'AND (p.`id_product` IN ('.$products.')) ';
    $sql.= "ORDER BY p.`id_product` ASC ";
    $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

    for($i=0;$i<count($rq); $i++) { 
                      $rq[$i]['tovar']=1;
                      $rq[$i]['images']='';
                      $rq[$i]['features'] = implode(",",$this->getFeaturesProduct((int)$rq[$i]['id_product'],$lang));
                      if ((int)$rq[$i]['id_category_default'] != 0)
                            {
                              $categ = '';
                              $categ_def = array();
                              $categ_def[]=(int)$rq[$i]['id_category_default'];
                              $categ = array_merge($categ_def,array_values(array_diff(Product::getProductCategories((int)$rq[$i]['id_product']),$categ_def)));
                              $rq[$i]['categories'] = implode(",",$categ);
                            }
                            else
                            {
                              $rq[$i]['categories'] = '';
                            }
                      
                        $prices=SpecificPrice::getSpecificPrice($rq[$i]['id_product'], $this->context->shop->id,0,0,$this->context->shop->id_shop_group,1);
                        if(isset($prices) && $prices['reduction_type']=='percentage') {
                               $prices['reduction_percent'] = str_replace('.',',',$prices['reduction']*100);
                               $prices['reduction_amount'] = ''; 
                            }
                        elseif(isset($prices) && $prices['reduction_type']=='amount') {
                                $prices['reduction_amount'] = str_replace('.',',',$prices['reduction']);
                                $prices['reduction_percent'] = ''; 
                            }
                            
                        if(is_array($prices)) {
                          $rq[$i]['reduction_amount']=$prices['reduction_amount'];
                          $rq[$i]['reduction_percent']=$prices['reduction_percent'];
                          $rq[$i]['reduction_from']=$prices['from'];
                          $rq[$i]['reduction_to']=$prices['to'];
                          unset($prices);
                        }
                               
                       $rq[$i]['quantity'] = StockAvailable::getQuantityAvailableByProduct($rq[$i]['id_product'],0);
                       $rq[$i]['supplier_reference'] = ProductSupplier::getProductSupplierReference($rq[$i]['id_product'],0, $rq[$i]['id_supplier']);
   /*                    if ($rq[$i]['id_product'] == 6242) {
                           dump($rq[$i]);
    die();
    }      */
                       if ($descr == 0)
                       {
                       $rq[$i]['description'] = ' ';
                       $rq[$i]['description_short'] = ' ';
                       $rq[$i]['meta_description'] = ' ';
                       $rq[$i]['meta_keywords'] = ' ';
                       $rq[$i]['meta_title'] = ' ';
                       $rq[$i]['meta_title'] = ' ';
                       }
                       if ($export_comb != 0)
                       {
                       $product_a = new Product ($rq[$i]['id_product']);
                       $attributes = $product_a->getAttributesResume ($lang);
                       $attributes_images = $product_a->getCombinationImages ($lang);
                     //  p($attributes_images);
                   //  d($product_a);
                     //  d($attributes);
                       if ($attributes)
                        {
                          foreach ($attributes as $attribute)
                          {
                    //     d(  );
                          $attributes_array[$acounter]['id_product'] = $attribute['id_product'];
                          $attributes_array[$acounter]['name'] = $attribute['attribute_designation'];
                          $attributes_array[$acounter]['reference'] = $attribute['reference'];
                          $attributes_array[$acounter]['supplier_reference'] = ProductSupplier::getProductSupplierReference($attribute['id_product'], $attribute['id_product_attribute'], $product_a->id_supplier);
                          $attributes_array[$acounter]['ean13'] = $attribute['ean13'];
                          $attributes_array[$acounter]['wholesale_price'] = $attribute['wholesale_price'];
                          $attributes_array[$acounter]['id_category_default'] = 0;
                          $attributes_array[$acounter]['price'] = $attribute['price'];
                          $attributes_array[$acounter]['quantity'] = $attribute['quantity'];
                          $attributes_array[$acounter]['weight'] = $attribute['weight'];
                          $attributes_array[$acounter]['tovar'] = $attribute['id_product_attribute'];
                          $attributes_array[$acounter]['images'] = '';
                          
                          if ( isset( $attributes_images[$attribute['id_product_attribute']] ) )
                            {
                               $attr_images = array();
                               foreach ($attributes_images[$attribute['id_product_attribute']] as $attr_image)
                                {
                                 $attr_images[]= $attr_image['id_image'];
                                }
                               $attr_images = implode(',',$attr_images);
                               
                            $attributes_array[$acounter]['images'] = $attr_images;
                            unset($attr_images);
                            }
                          
                          
                          $prices_a=SpecificPrice::getSpecificPrice($rq[$i]['id_product'], $this->context->shop->id,0,0,$this->context->shop->id_shop_group,1,$attribute['id_product_attribute']);
                            if(isset($prices_a) && $prices_a['reduction_type']=='percentage') {
                                   $prices_a['reduction_amount'] = str_replace('.',',',$prices_a['reduction']*100);
                                   $prices_a['reduction_price'] =''; 
                                }
                            elseif(isset($prices_a) && $prices_a['reduction_type']=='amount') {
                                    $prices_a['reduction_amount'] = str_replace('.',',',$prices_a['reduction']);
                                    $prices_a['reduction_percent'] =''; 
                                }
                                
                          if(is_array($prices_a)) {
                          $attributes_array[$acounter]['reduction_amount'] = $prices_a['reduction_amount'];
                          $attributes_array[$acounter]['reduction_percent'] =  $prices_a['reduction_percent'];
                          $attributes_array[$acounter]['reduction_from'] =  $prices_a['from'];
                          $attributes_array[$acounter]['reduction_to'] = $ $prices_a['to'];
                          unset($prices_a);
                          } 
                          
                          $acounter++;
                          }
                        
                        }
                       }
                       
                }
  //  d($attributes_array);
    $description=1;
    if ($attributes_array)
      {
        $rq = array_merge($rq,$attributes_array);
        // Obtain a list of columns
        foreach ($rq as $key => $row) 
          {
              $idp[$key]  = $row['id_product'];
              $idpa[$key] = $row['tovar'];
          // Sort the data with volume descending, edition ascending
          // Add $data as the last parameter, to sort by the common key
          array_multisort($idp, SORT_ASC, $idpa, SORT_ASC, $rq);
          }
      } 
    
   $this->createExcel($rq);
   
    $products = 0;
    $rq = 0;
    $attributes_array = 0;

}	

private function getFeaturesProduct($id_product,$lang)  
{
  //global $cookie;
  $features=Product::getFeaturesStatic($id_product);
  
  foreach ($features as $feature)
          {
          $a = Feature::getFeature($lang, $feature['id_feature']);
          $b = FeatureValue::getFeatureValueLang($feature['id_feature_value']);
          foreach ($b as $l)
            {
            if ($l['id_lang']==$lang) $c=$l['value'];
            }
          $prod_features[]=$a['name'].":".$c;
          }
  unset ($features);        
  return $prod_features;
  unset ($prod_features);
}	

private function createExcel($products) {

 require_once dirname(__FILE__) . '/classes/PHPExcel.php';
 $objPHPExcel = new PHPExcel();
 
 // Set document properties
 $objPHPExcel->getProperties()->setCreator($this->l('Prestashop'))
      							 ->setLastModifiedBy($this->l('Prestashop'))
      							 ->setTitle($this->l('Products'))
      							 ->setSubject($this->l('Products'))
      							 ->setDescription($this->l('Products'))
      							 ->setKeywords($this->l('Products'))
      							 ->setCategory($this->l('Products'));
  
  
  $cur = Configuration::get('PS_EXCEL_CUR_E');
  if ($cur)
  {
  $objPHPExcel->setActiveSheetIndex(0)
              ->setCellValue('A1', $this->l('id_product'))
              ->setCellValue('B1', $this->l('active'))
              ->setCellValue('C1', $this->l('name'))
              ->setCellValue('D1', $this->l('categories'))
              ->setCellValue('E1', $this->l('price'))
              ->setCellValue('F1', $this->l('wholesale_price'))
              ->setCellValue('G1', $this->l('on_sale'))
              ->setCellValue('H1', $this->l('reduction_amount'))
              ->setCellValue('I1', $this->l('reduction_percent'))
              ->setCellValue('J1', $this->l('reduction_from'))
              ->setCellValue('K1', $this->l('reduction_to'))
              ->setCellValue('L1', $this->l('reference'))
              ->setCellValue('M1', $this->l('supplier_reference'))
              ->setCellValue('N1', $this->l('supplier_name'))
              ->setCellValue('O1', $this->l('manufacturer_name'))
              ->setCellValue('P1', $this->l('ean13'))
              ->setCellValue('Q1', $this->l('width'))
              ->setCellValue('R1', $this->l('height'))
              ->setCellValue('S1', $this->l('depth'))
              ->setCellValue('T1', $this->l('weight'))
              ->setCellValue('U1', $this->l('quantity'))
              ->setCellValue('V1', $this->l('description_short'))
              ->setCellValue('W1', $this->l('description'))
              ->setCellValue('X1', $this->l('images'))
              ->setCellValue('Y1', $this->l('del_images'))
              ->setCellValue('Z1', $this->l('features'))
              ->setCellValue('AA1', $this->l('online_only'))
              ->setCellValue('AB1', $this->l('available_for_order'))
              ->setCellValue('AC1', $this->l('available_now'))
              ->setCellValue('AD1', $this->l('available_later'))
              ->setCellValue('AE1', $this->l('meta_title'))
              ->setCellValue('AF1', $this->l('meta_keywords'))
              ->setCellValue('AG1', $this->l('meta_description'))
              ->setCellValue('AH1', $this->l('link_rewrite'))
              ->setCellValue('AI1', $this->l('tovar'))
              ->setCellValue('AJ1', $this->l('pc_currency'));
  }
  else
  {
  $objPHPExcel->setActiveSheetIndex(0)
              ->setCellValue('A1', $this->l('id_product'))
              ->setCellValue('B1', $this->l('active'))
              ->setCellValue('C1', $this->l('name'))
              ->setCellValue('D1', $this->l('categories'))
              ->setCellValue('E1', $this->l('price'))
              ->setCellValue('F1', $this->l('wholesale_price'))
              ->setCellValue('G1', $this->l('on_sale'))
              ->setCellValue('H1', $this->l('reduction_amount'))
              ->setCellValue('I1', $this->l('reduction_percent'))
              ->setCellValue('J1', $this->l('reduction_from'))
              ->setCellValue('K1', $this->l('reduction_to'))
              ->setCellValue('L1', $this->l('reference'))
              ->setCellValue('M1', $this->l('supplier_reference'))
              ->setCellValue('N1', $this->l('supplier_name'))
              ->setCellValue('O1', $this->l('manufacturer_name'))
              ->setCellValue('P1', $this->l('ean13'))
              ->setCellValue('Q1', $this->l('width'))
              ->setCellValue('R1', $this->l('height'))
              ->setCellValue('S1', $this->l('depth'))
              ->setCellValue('T1', $this->l('weight'))
              ->setCellValue('U1', $this->l('quantity'))
              ->setCellValue('V1', $this->l('description_short'))
              ->setCellValue('W1', $this->l('description'))
              ->setCellValue('X1', $this->l('images'))
              ->setCellValue('Y1', $this->l('del_images'))
              ->setCellValue('Z1', $this->l('features'))
              ->setCellValue('AA1', $this->l('online_only'))
              ->setCellValue('AB1', $this->l('available_for_order'))
              ->setCellValue('AC1', $this->l('available_now'))
              ->setCellValue('AD1', $this->l('available_later'))
              ->setCellValue('AE1', $this->l('meta_title'))
              ->setCellValue('AF1', $this->l('meta_keywords'))
              ->setCellValue('AG1', $this->l('meta_description'))
              ->setCellValue('AH1', $this->l('link_rewrite'))
              ->setCellValue('AI1', $this->l('tovar'));
  }
  $i1 = 0;

  foreach ($products as $key=>$row) {
      $i1 = $key + 2;
      
      // Add some data
      if ($cur)
      {
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue("A$i1", $row['id_product'])
                  ->setCellValue("B$i1", $row['active'])
                  ->setCellValue("C$i1", $row['name'])
                  ->setCellValue("D$i1", $row['categories'])
                  ->setCellValue("E$i1", $row['price'])
                  ->setCellValue("F$i1", $row['wholesale_price'])
                  ->setCellValue("G$i1", $row['on_sale'])
                  ->setCellValue("H$i1", $row['reduction_amount'])
                  ->setCellValue("I$i1", $row['reduction_percent'])
                  ->setCellValue("J$i1", $row['reduction_from'])
                  ->setCellValue("K$i1", $row['reduction_to'])
                  ->setCellValue("L$i1", $row['reference'])
                  ->setCellValue("M$i1", $row['supplier_reference'])
                  ->setCellValue("N$i1", $row['supplier_name'])
                  ->setCellValue("O$i1", $row['manufacturer_name'])
                  ->setCellValue("P$i1", $row['ean13'])
                  ->setCellValue("Q$i1", $row['width'])
                  ->setCellValue("R$i1", $row['height'])
                  ->setCellValue("S$i1", $row['depth'])
                  ->setCellValue("T$i1", $row['weight'])
                  ->setCellValue("U$i1", $row['quantity'])
                  ->setCellValue("V$i1", $row['description_short'])
                  ->setCellValue("W$i1", $row['description'])
                  ->setCellValue("X$i1", $row['images'])
                  ->setCellValue("Y$i1", 0)
                  ->setCellValue("Z$i1", $row['features'])
                  ->setCellValue("AA$i1", $row['online_only'])
                  ->setCellValue("AB$i1", $row['available_for_order'])
                  ->setCellValue("AC$i1", $row['available_now'])
                  ->setCellValue("AD$i1", $row['available_later'])
                  ->setCellValue("AE$i1", $row['meta_title'])
                  ->setCellValue("AF$i1", $row['meta_keywords'])
                  ->setCellValue("AG$i1", $row['meta_description'])
                  ->setCellValue("AH$i1", $row['link_rewrite'])
                  ->setCellValue("AI$i1", $row['tovar'])
                  ->setCellValue("AJ$i1", $row['pc_currency']);
      }           
      else
      {

      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue("A$i1", $row['id_product'])
                  ->setCellValue("B$i1", $row['active'])
                  ->setCellValue("C$i1", $row['name'])
                  ->setCellValueExplicit("D$i1", $row['categories'])
                  ->setCellValue("E$i1", $row['price'])
                  ->setCellValue("F$i1", $row['wholesale_price'])
                  ->setCellValue("G$i1", $row['on_sale'])
                  ->setCellValue("H$i1", $row['reduction_amount'])
                  ->setCellValue("I$i1", $row['reduction_percent'])
                  ->setCellValue("J$i1", $row['reduction_from'])
                  ->setCellValue("K$i1", $row['reduction_to'])
                  ->setCellValueExplicit("L$i1", $row['reference'])
                  ->setCellValueExplicit("M$i1", $row['supplier_reference'])
                  ->setCellValue("N$i1", $row['supplier_name'])
                  ->setCellValue("O$i1", $row['manufacturer_name'])
                  ->setCellValueExplicit("P$i1", $row['ean13'])
                  ->setCellValue("Q$i1", $row['width'])
                  ->setCellValue("R$i1", $row['height'])
                  ->setCellValue("S$i1", $row['depth'])
                  ->setCellValue("T$i1", $row['weight'])
                  ->setCellValue("U$i1", $row['quantity'])
                  ->setCellValue("V$i1", $row['description_short'])
                  ->setCellValue("W$i1", $row['description'])
                  ->setCellValue("X$i1", $row['images'])
                  ->setCellValue("Y$i1", 0)
                  ->setCellValue("Z$i1", $row['features'])
                  ->setCellValue("AA$i1", $row['online_only'])
                  ->setCellValue("AB$i1", $row['available_for_order'])
                  ->setCellValue("AC$i1", $row['available_now'])
                  ->setCellValue("AD$i1", $row['available_later'])
                  ->setCellValue("AE$i1", $row['meta_title'])
                  ->setCellValue("AF$i1", $row['meta_keywords'])
                  ->setCellValue("AG$i1", $row['meta_description'])
                  ->setCellValue("AH$i1", $row['link_rewrite'])
                  ->setCellValue("AI$i1", $row['tovar']);
      }

      }
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle($this->l('Products'));
      
      
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      
      
      // Save Excel 2007 file
      
     $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
     $objWriter->save(dirname(__FILE__).'/download/exporttemp.xlsx');
      
}

private function csvInit() {
        global $cookie;
        $this->id_lang=Configuration::get('PS_EXCEL_LANG_E'); 
        require_once dirname(__FILE__) .'/classes/CsvWrite.php'; 
    }

private function FileSizeConvert($bytes)
{
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => $this->l('TB'),
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => $this->l('GB'),
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => $this->l('MB'),
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => $this->l('KB'),
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => $this->l('Bytes'),
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}
    
private function _listFiles() {
    if(is_dir(dirname(__FILE__).'/download')) {
        if ($dh = scandir(dirname(__FILE__).'/download')) {
            foreach ($dh as $file)
            { 
              if($file !='.' && $file !='..' && $file =='exporttemp.xlsx')
              {
                $time_t = time();
                $file_date = date('d_m_y_H_i_s', $time_t);
                $file_date_l = date('d/m/y H:i:s', $time_t);
                rename(dirname(__FILE__).'/download/exporttemp.xlsx',dirname(__FILE__).'/download/export_'.$file_date.'.xlsx');
                $f_size = filesize ( dirname(__FILE__).'/download/export_'.$file_date.'.xlsx' );
                $f_size = $this-> FileSizeConvert ($f_size);
                $link = '<a href="/modules/'.$this->name.'/download/export_'.$file_date.'.xlsx">'.$this->l('Download').', '.$this->l('File from').' '.$file_date_l.', '.$this->l('File size:')." ".$f_size.'</a>';
              }
              else 
              {
                if($file !='.' && $file !='..')
                {
                  unlink (dirname(__FILE__).'/download'.'/'.$file);
                }
                $link='';
              }
            }
        }
    }
    return $link;
   }

}
