<?php

if (!defined('_PS_VERSION_'))
	exit;

class Nove_DateOfDelivery extends Module
{
	/* @var boolean error */
	protected $error = false;
	
	public function __construct()
	{
		$this->name = 'nove_dateofdelivery';
		$this->tab = 'shipping_logistics';
		$this->version = '3.0.1';
		$this->author = 'Novevision.com, Britoff A.';
		$this->need_instance = 1;
		$this->bootstrap = true;

	 	parent::__construct();

		$this->displayName = $this->l('Date and time interval of delivery');
		$this->description = $this->l('Customer can enter date and time interval of delivery');
		$this->confirmUninstall = $this->l('Are you sure you want to delete this module?');
	}
	
	public function install()
	{
		$this->_clearCache('nove_dateofdelivery.tpl');
		if (!parent::install() ||
			!$this->registerHook('beforeCarrier') ||  
      !$this->registerHook('displayAfterCarrier') ||
      !$this->registerHook('displayDateOfDelivery') || 
      !$this->registerHook('displayHeader') ||
      !$this->registerHook('displayHome') ||
			!$this->registerHook('orderDetailDisplayed') ||
			!$this->registerHook('actionCarrierUpdate') || 
      !$this->registerHook('actionAdminControllerSetMedia') || 
      !$this->registerHook('actionObjectOrderAddAfter') || 
      
			!Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'nove_dateofdelivery (
  			`id_period` int(3) NOT NULL AUTO_INCREMENT,                             
        `timefrom` time NOT NULL,                                            
        `timeto` time NOT NULL,                                              
        `timeoff` time NOT NULL,                                             
        `express` tinyint(1) NOT NULL DEFAULT "0",                           
        `carriers` int(10) NOT NULL DEFAULT "0",                             
        `carriers_name` VARCHAR(256) NULL DEFAULT NULL,                      
  			`active` TINYINT(1) NOT NULL,                                           
			PRIMARY KEY(`id_period`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') ||
      
      !Db::getInstance()->execute("INSERT INTO `ps_nove_dateofdelivery` (`id_period`, `timefrom`, `timeto`, `timeoff`, `express`, `carriers`, `carriers_name`, `active`) VALUES (0, '00:00:00', '00:00:00', '00:00:00', 0, 0, '".$this->l('All periods')."', '0') ") ||
      
      !Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'nove_dateofdelivery_holidays (
  			`id_rest` int(3) NOT NULL AUTO_INCREMENT,                                  
        `date` date NOT NULL,                                                   
        `period` int(3) NOT NULL,                                               
        `id_shop` int(2) NOT NULL,                                              
			PRIMARY KEY(`id_rest`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') ||
      
			!Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'nove_dateofdelivery_shop (
			  `id_period` int(3) NOT NULL AUTO_INCREMENT, 
			  `id_shop` int(2) NOT NULL,
			PRIMARY KEY(`id_period`, `id_shop`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') ||
      
      !Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'nove_dateofdelivery_cart` (
        `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT,                              
        `cart_id` int(20) NOT NULL,                                                 
        `dateofdelivery` datetime NOT NULL,                                         
        `period` int(2),                                                            
      PRIMARY KEY (`id`,`cart_id`)) 
      ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8') ||
       
			!Configuration::updateValue('NDOD2_DAYSOFDELIVERY', 3) ||
      !Configuration::updateValue('NDOD2_DAYTODAY', 1) ||
      !Configuration::updateValue('NDOD2_DELHOL', 1) ||
      !Configuration::updateValue('NDOD2_SAT', 0) ||
      !Configuration::updateValue('NDOD2_SUN', 0)
			)
			return false;
		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('nove_dateofdelivery.tpl');
		if (!parent::uninstall() ||
			!Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'nove_dateofdelivery') ||
      !Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'nove_dateofdelivery_holidays') ||
			!Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'nove_dateofdelivery_shop') ||
      !Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'nove_dateofdelivery_cart') ||
			!Configuration::deleteByName('NDOD2_DAYSOFDELIVERY') ||
      !Configuration::deleteByName('NDOD2_DAYTODAY') ||
      !Configuration::deleteByName('NDOD2_DELHOL') ||
      !Configuration::deleteByName('NDOD2_SAT') ||
      !Configuration::deleteByName('NDOD2_SUN')
      )
			return false;
		return true;
	}
	
	public function getHolidays($id=0)
	{
		$links = array();

		$sql = 'SELECT b.`id_rest`, b.`date`, b.`period`, CONCAT ("'.$this->l('From').' ", a.`timefrom`, " '.$this->l('To').' ", a.`timeto`, " - ", a.`carriers_name`) as pername
				FROM `'._DB_PREFIX_.'nove_dateofdelivery_holidays` b, `'._DB_PREFIX_.'nove_dateofdelivery` a ' ;
    $sql .= ' WHERE a.`id_period` = b.`period`';
    if ($id) $sql .= ' AND (b.`id_rest` = '.$id.') ';
      $sql .= ' ORDER BY  b.`date` ASC';
    
    $links = Db::getInstance()->executeS($sql);

		return $links;
	}
  
  public function getHolidaysFront($date)
	{
		$links = array();

		$sql = 'SELECT b.`id_rest`, b.`date`, b.`period`, CONCAT ("'.$this->l('From').' ", a.`timefrom`, " '.$this->l('To').' ", a.`timeto`, " - ", a.`carriers_name`) as pername
				FROM `'._DB_PREFIX_.'nove_dateofdelivery_holidays` b, `'._DB_PREFIX_.'nove_dateofdelivery` a ' ;
    $sql .= ' WHERE a.`id_period` = b.`period`';
    if ($date) $sql .= " AND (b.`date` >= '".$date."') ";
      $sql .= ' ORDER BY  b.`date` ASC';
    $links = Db::getInstance()->executeS($sql);

		return $links;
	}
  
  public function addHoliday($date, $period, $id = 0)
	{
    $id_shop = (int)Context::getContext()->shop->id;
    $sql = "INSERT INTO `"._DB_PREFIX_."nove_dateofdelivery_holidays` (`id_rest`, `date`, `period`, `id_shop`) VALUES (NULL, '$date', $period, $id_shop)";
    if ($id) $sql = "UPDATE `"._DB_PREFIX_."nove_dateofdelivery_holidays` SET `date` = '$date' ,`period` =  $period WHERE `id_rest` = ".$id;
    Db::getInstance()->execute($sql);
		return true;
	}
  
  public function delHoliday($id)
	{
    $sql = "DELETE FROM `"._DB_PREFIX_."nove_dateofdelivery_holidays` WHERE `id_rest` = ".$id;
    Db::getInstance()->execute($sql);
		return true;
	}
  
  public function delOldHolidays()
	{
    $sql = "DELETE FROM `"._DB_PREFIX_."nove_dateofdelivery_holidays` WHERE `date` < '".date ('Y-m-d',strtotime("-10 day"))."'";
    Db::getInstance()->execute($sql);
		return true;
	}
  
  public function getLinks2($active = true, $id = 0, $carriers = array(), $id_shop = 1)
	{
		$links = array();
   
		$sql = 'SELECT b.`id_period`, b.`timefrom`, b.`timeto`, b.`timeoff`, b.`active`, b.`express`, b.`carriers`, b.`carriers_name` FROM `'._DB_PREFIX_.'nove_dateofdelivery` b';
			$sql .= ' JOIN `'._DB_PREFIX_.'nove_dateofdelivery_shop` bs ON b.`id_period` = bs.`id_period` AND bs.`id_shop` = '.$id_shop;
		  $sql .= ' WHERE  1';
      if ($active) $sql .= ' AND  (b.`active` = 1)';
      if ($id) $sql .= ' AND  (b.`id_period` = '. (int)$id . ') ';
      if ($carriers) $sql .= ' AND  (b.`carriers` IN ('. implode(",",$carriers) . ')) ';
      $sql .= ' ORDER BY  b.`timefrom` ASC';
    $links = Db::getInstance()->executeS($sql);

		return $links;
	}
  
  public function getLinks($active = true, $id = 0, $carriers = 0)
	{
		$links = array();
   
		$sql = 'SELECT b.`id_period`, b.`timefrom`, b.`timeto`, b.`timeoff`, b.`active`, b.`express`, b.`carriers`, b.`carriers_name` FROM `'._DB_PREFIX_.'nove_dateofdelivery` b';
		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
			$sql .= ' JOIN `'._DB_PREFIX_.'nove_dateofdelivery_shop` bs ON b.`id_period` = bs.`id_period` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
		  $sql .= ' WHERE  1';
      if ($active) $sql .= ' AND  (b.`active` = 1)';
      if ($id) $sql .= ' AND  (b.`id_period` = '. (int)$id . ") ";
      if ($carriers) $sql .= ' AND  (b.`carriers` = '. (int)$carriers . ") ";
      $sql .= ' ORDER BY  b.`timefrom` ASC';
    $links = Db::getInstance()->executeS($sql);

		return $links;
	}
	
	public function addLink()
	{
		if ($id_period = Tools::getValue('id_period'))
		{
			$timefrom = '00:00:00';
      $timeto = '00:00:00';
      $timeoff = '00:00:00';
      $express = 0;
      $carrier = 0;
      $active = 1;
      
      if ( isset($_POST['timefrom']) )  $timefrom = $_POST['timefrom'];
      if ( isset($_POST['timeto']) )  $timeto = $_POST['timeto'];
      if ( isset($_POST['timeoff']) )  $timeoff = $_POST['timeoff'];
      if ( isset($_POST['express']) )  $express = $_POST['express'];
      if ( isset($_POST['carrier']) )  
        {
        $carrier = new Carrier ($_POST['carrier'][0], $this->context->language->id);
        }
      if ( isset($_POST['active']) )  $active = $_POST['active'];
      $sql_upd = 'UPDATE '._DB_PREFIX_.'nove_dateofdelivery SET `timefrom` = "'. $timefrom .'", `timeto` = "'. $timeto .'", `timeoff` = "'. $timeoff .'", `express` = "'. $express .'", `carriers` = "'. $carrier->id.'", `carriers_name` = "'. $carrier->name .'", `active` = '.$active.' WHERE `id_period` = '.(int)$id_period ;
      Db::getInstance()->execute($sql_upd);

    }
		else
		{
      $carrier = new Carrier ($_POST['carrier'][0], $this->context->language->id);
      if (!Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."nove_dateofdelivery VALUES (NULL, '".(isset($_POST['timefrom']) ? $_POST['timefrom'] : '00:00:00')."','".(isset($_POST['timeto']) ? $_POST['timeto'] : '00:00:00')."','".(isset($_POST['timeoff']) ? $_POST['timeoff'] : '00:00:00')."','".(isset($_POST['express']) ? $_POST['express'] : 0)."','".(isset($carrier->id) ? $carrier->id : '0')."','".(isset($carrier->name) ? $carrier->name : '')."' , ".((isset($_POST['active']) && $_POST['active']) == 'on' ? 1 : 0).")") || !$id_period = Db::getInstance()->Insert_ID())
				return false;

		}

		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'nove_dateofdelivery_shop WHERE id_period='.(int)$id_period);

		if (!Shop::isFeatureActive())
		{
			Db::getInstance()->insert('nove_dateofdelivery_shop', array(
				'id_period' => (int)$id_period,
				'id_shop' => (int)Context::getContext()->shop->id,
			));
		}
		else
		{
			$assos_shop = Tools::getValue('checkBoxShopAsso_configuration');
			if (empty($assos_shop))
				return false;
			foreach ($assos_shop as $id_shop => $row)
					Db::getInstance()->insert('nove_dateofdelivery_shop', array(
						'id_period' => (int)$id_period,
						'id_shop' => (int)$id_shop,
					));
		}
		return true;
	}

	public function deleteLink()
	{
		return (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'nove_dateofdelivery WHERE `id_period` = '.(int)$_GET['id_period']) &&
					Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'nove_dateofdelivery_shop WHERE `id_period` = '.(int)$_GET['id_period']));
	}

	public function updateTitle()
	{
		$daysofdelivery = $_POST['daysofdelivery'];
    Configuration::updateValue('NDOD2_DAYSOFDELIVERY', $daysofdelivery);
    $daytoday = $_POST['daytoday'];
    Configuration::updateValue('NDOD2_DAYTODAY', $daytoday);
    $delhol = $_POST['delhol'];
    Configuration::updateValue('NDOD2_DELHOL', $delhol);
    $sun = $_POST['sunday'];
    Configuration::updateValue('NDOD2_SUN', $sun);
    $sat = $_POST['saturday'];
    Configuration::updateValue('NDOD2_SAT', $sat);
    $delclose = $_POST['delclose'];
    if ($delclose) $this->delOldHolidays();
    
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
	
		// Add a period

		if (Tools::isSubmit('submitLinkAdd'))
     	{
        if ($this->addLink()) {
	     	  		$this->_html .= $this->displayConfirmation($this->l('The period has been added.'));
              $_POST['id_period'] = 0;
              }
				else
					$this->_html .= $this->displayError($this->l('An error occurred during period creation.'));
          
				$this->_clearCache('nove_dateofdelivery.tpl');
     	}
		// Update the block title
		elseif (Tools::isSubmit('submitTitle'))
		{
		$this->_clearCache('nove_dateofdelivery.tpl');
	
			if (!Validate::isInt($_POST['daysofdelivery']))
				$this->_html .= $this->displayError($this->l('The "daysofdelivery" field is invalid'));
      elseif (!$this->updateTitle())
				$this->_html .= $this->displayError($this->l('An error occurred during daysofdelivery updating.'));
			else
			{
				$this->_html .= $this->displayConfirmation($this->l('The block period has been updated.'));
			}
		}
		// Delete a link
		elseif (Tools::isSubmit('deletenove_dateofdelivery') && Tools::getValue('id_period'))
		{
			$this->_clearCache('nove_dateofdelivery.tpl');
			if (!is_numeric($_GET['id_period']) || !$this->deleteLink())
			 	$this->_html .= $this->displayError($this->l('An error occurred during period deletion.'));
			else
			 	$this->_html .= $this->displayConfirmation($this->l('The period has been deleted.'));
		}
    elseif ( Tools::isSubmit('submitLinkAddHoliday') )
    {
        if ($this->addHoliday($_POST['restday'],$_POST['period'],$_POST['id_rest'])) {
	     	  		$this->_html .= $this->displayConfirmation($this->l('The holiday has been added.'));
              $_POST['id_rest'] = 0;
              }
				else
					$this->_html .= $this->displayError($this->l('An error occurred during holiday creation.'));
          
				$this->_clearCache('nove_dateofdelivery.tpl');
    }
    elseif ( Tools::isSubmit('deletenove_dateofdeliverylist2') )
    {
      $this->_clearCache('nove_dateofdelivery.tpl');
			if (!is_numeric($_GET['id_rest']) || !$this->delHoliday( (int)$_GET['id_rest'] ))
			 	$this->_html .= $this->displayError($this->l('An error occurred during holiday deletion.'));
			else
         {
         $this->_html .= $this->displayConfirmation($this->l('The holiday has been deleted.'));
         $_POST['id_rest'] = 0;
         }
    }

		$this->_html .= $this->renderForm();
		$this->_html .= $this->renderList();
    $this->_html .= $this->renderList2();

		return $this->_html;
	}
	
	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'css/nove_dateofdelivery.css', 'all');
    $this->context->controller->addCSS(($this->_path).'css/style_buttons.css', 'all'); 
	}
  
  public function renderList()
	{
		$fields_list1 = array(
			'id_period' => array(
				'title' => $this->l('Id'),
				'type' => 'text',
			),
			'timefrom' => array(
				'title' => $this->l('From'),
				'type' => 'text',
			),
			'timeto' => array(
				'title' => $this->l('To'),
				'type' => 'text',
			),
      'timeoff' => array(
				'title' => $this->l('Off'),
				'type' => 'text',
			),
      'carriers_name' => array(
				'title' => $this->l('Carrier'),
				'type' => 'text',
			),
      'active' => array(
				'title' => $this->l('Active'),
				'type' => 'text',
			),
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_period';
		$helper->actions = array('edit','delete');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Period list');
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$links = $this->getLinks(0);
		if (is_array($links) && count($links))
			return $helper->generateList($links, $fields_list1);
		else
			return false;
	}
  
  public function renderList2()
	{
		$fields_list2 = array(
			'id_rest' => array(
				'title' => $this->l('Id'),
				'type' => 'text',
			),
			'date' => array(
				'title' => $this->l('Date'),
				'type' => 'date',
			),
			'pername' => array(
				'title' => $this->l('Period'),
				'type' => 'text',
			)
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
	  $helper->identifier = 'id_rest';
		$helper->actions = array('delete','edit');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Holidays list');
		$helper->table = $this->name.'list2';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$links = $this->getHolidays();
		if (is_array($links) && count($links))
			return $helper->generateList($links, $fields_list2);
		else
			return false;
	}
  
	public function renderForm()
	{
		
    $carriers = Carrier::getCarriers ($this->context->language->id, true);
    array_unshift($carriers, array('id_carrier'=>0,'name'=>$this->l('Choose carrier:')));        //Добавляем элемент все
    
    $tit = $this->l('Add a new period');
    if ( Tools::getValue('id_period') ) $tit = $this->l('Edit a period');
    $fields_form_1 = array(
			'form' => array(
				'legend' => array(
					'title' => $tit,
					'icon' => 'icon-plus-sign-alt'
				),
				'input' => array(
					array(
						'type' => 'hidden',
						'name' => 'id_period',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Time from'),
						'name' => 'timefrom',
            'desc' => $this->l('Opening of the delivery time')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Time to'),
						'name' => 'timeto',
            'desc' => $this->l('Closing of the delivery time')
					),
          	array(
						'type' => 'text',
						'label' => $this->l('Time off'),
						'name' => 'timeoff',
            'desc' => $this->l('Time to off this period')
					),
          array(
						'type' => 'select',
						'label' => $this->l('Choose carrier:'),
						'name' => 'carrier[]',
            'multiple' => false,
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $carriers,
							'id' => 'id_carrier',
							'name' => 'name'
						),
					),
          array(
						'type' => 'switch',
						'label' => $this->l('Express'),
						'name' => 'express',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'express_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'express_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
          array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),

				),
				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'submitLinkAdd',
				)
			),
		);
    
    $tit2 = $this->l('Add a new rest day');
    if ( Tools::getValue('id_rest') ) $tit2 = $this->l('Edit a rest day');
    $periods = $this->getLinks(1);
 
    $per[0]['id_period'] = 0;
    $per[0]['text'] = $this->l('All');
    
    foreach ($periods as $key=>$period)
    {
     $per[$key+1]['id_period'] = $period['id_period'];
     $per[$key+1]['text'] = $this->l('From')." ".$period['timefrom']." ".$this->l('To')." ".$period['timeto']." - ".$period['carriers_name'];
    }
    
    $fields_form_3 = array(
			'form' => array(
				'legend' => array(
					'title' => $tit2,
					'icon' => 'icon-plus-sign-alt'
				),
				'input' => array(
					array(
						'type' => 'hidden',
						'name' => 'id_rest',
					),
          array(
						'type' => 'date',
						'label' => $this->l('Rest day date'),
						'name' => 'restday'
					),
          array(
						'type' => 'select',
						'label' => $this->l('Period:'),
						'name' => 'period',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $per,
							'id' => 'id_period',
							'name' => 'text'
						),
					)

				),
				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'submitLinkAddHoliday',
				)
			),
		);

		$shops = Shop::getShops(true, null, true);
		if (Shop::isFeatureActive())
		{
			$fields_form_1['form']['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association'),
				'name' => 'checkBoxShopAsso',
			);
		}

		$fields_form_2 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Options'),
					'icon' => 'icon-cog'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Days of delivery'),
						'name' => 'daysofdelivery',
            'desc' => $this->l('The number of days from the current, which is permitted to postage')
					),
          array(
						'type' => 'switch',
						'label' => $this->l('Delivery in day of the order?'),
						'name' => 'daytoday',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'daytoday_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'daytoday_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
          array(
						'type' => 'switch',
						'label' => $this->l('Is Saturday a day off?'),
						'name' => 'saturday',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'saturday_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'saturday_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
          array(
						'type' => 'switch',
						'label' => $this->l('Is Sunday a day off?'),
						'name' => 'sunday',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'sunday_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'sunday_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
          array(
						'type' => 'switch',
						'label' => $this->l('Remove a closed day from the list?'),
						'name' => 'delhol',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'delhol_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'delhol_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
          ),
          array(
						'type' => 'switch',
						'label' => $this->l('Do you want to delete closed periods older than 7 days?'),
						'name' => 'delclose',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'delclose_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'delclose_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
          ),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'submitTitle',
				)
			),
		);


		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = 'id_period';
		$helper->submit_action = 'submit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

    return $helper->generateForm(array($fields_form_1, $fields_form_3, $fields_form_2));
	}

	public function getConfigFieldsValues()
	{
		if ( Tools::getValue('id_period') ) {
     $links = $this->getLinks(0, (int)Tools::getValue('id_period') );
     $active = $links[0]['active'];
     $express = $links[0]['express'];
     $carrier = $links[0]['carriers'];
     $timefrom = $links[0]['timefrom'];
     $timeoff = $links[0]['timeoff'];
     $timeto = $links[0]['timeto'];  
    }
    else
    {
     $active = Tools::getValue('active');
     $carrier = array(0);
     $express = Tools::getValue('express');
     $timeoff = Tools::getValue('timeoff');                                        
     $timefrom = Tools::getValue('timefrom');
     $timeto = Tools::getValue('timeto');
    }
    if ( Tools::getValue('id_rest') ) {
     $datas = $this->getHolidays( (int)Tools::getValue('id_rest') );
     if ($datas)
     {
     $restday = $datas[0]['date'];
     $period = $datas[0]['period'];
     }
     else
     {
     $restday = '';
     $period = 0;
     }
    }
    else
    {
     $restday = '';
     $period = 0;
    }
    $fields_values = array(
			'id_period' => Tools::getValue('id_period'),
      'id_rest' => Tools::getValue('id_rest'),
      'carrier[]' => $carrier,
      'express' => $express,
			'active' => $active,
      'timefrom' => $timefrom,
      'timeoff' => $timeoff,
      'timeto' => $timeto,
      'restday' => $restday,
      'period' => $period,
      'sunday' => Tools::getValue('NDOD2_SUN', Configuration::get('NDOD2_SUN')),
      'saturday' => Tools::getValue('NDOD2_SAT', Configuration::get('NDOD2_SAT')),
      'daysofdelivery' => Tools::getValue('NDOD2_DAYSOFDELIVERY', Configuration::get('NDOD2_DAYSOFDELIVERY')),
      'daytoday' => Tools::getValue('NDOD2_DAYTODAY', Configuration::get('NDOD2_DAYTODAY')),
      'delhol' => Tools::getValue('NDOD2_DELHOL', Configuration::get('NDOD2_DELHOL')),
      'delclose' => 0,
		);


		if (Tools::getIsset('updateblocklink') && (int)Tools::getValue('id_period') > 0)
			$fields_values = array_merge($fields_values, $this->getLinkById((int)Tools::getValue('id_period')));

		return $fields_values;
	}

  public function hookactionObjectOrderAddAfter($params)
  {
   $order = $params['object'];
   $id_order = (int)$order->id;
   $id_cart = (int)$order->id_cart;
   $id_carrier = (int)$order->id_carrier;
   
   $date_a2 = array();
   $sql_date = "SELECT `dateofdelivery`, `period` FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id` = $id_cart LIMIT 1";
   $date_a = Db::getInstance()->executeS($sql_date);
   $date = $date_a[0]['dateofdelivery'];
   $date_a2 = explode ('-', $date);
   $date = $date_a2[0]."-".$date_a2[1]."-".$date_a2[2];
  /*
    $sql_upd_order = "UPDATE `"._DB_PREFIX_."orders` SET `delivery_date` = '".$date_a[0]['dateofdelivery']."' WHERE `id_order` =".$id_order;
    Db::getInstance()->execute($sql_upd_order);
    
    $sql_upd_del = "UPDATE `"._DB_PREFIX_."order_invoice` SET `delivery_date` = '".$date_a[0]['dateofdelivery']."' WHERE `id_order` = $id_order";
    Db::getInstance()->execute($sql_upd_del);
    $sql_upd_del2 = "UPDATE `"._DB_PREFIX_."order_carrier` SET `date_add` = '".$date_a[0]['dateofdelivery']."' WHERE `id_order` = $id_order";
    Db::getInstance()->execute($sql_upd_del2);        */
    //admin logic fozzy
 /*   $sql_upd_log = "UPDATE `"._DB_PREFIX_."fozzy_logistic` SET `dtd_upd` = '".$date."' WHERE `id_order` = $id_order";
    Db::getInstance()->execute($sql_upd_log);  */
    if ($id_carrier == 37 || $id_carrier == 50) // Новая почта - 37, Жастин - 50
      {
        if (date('H') <= 11 && $id_carrier == 37)
        {
          $date_a[0]['period'] = 137;
          $date_a[0]['dateofdelivery'] = date('Y-m-d 00:00:00');
        }
        if (date('H') > 11 && $id_carrier == 37)
        {
          $date_a[0]['period'] = 137;
          $date_a[0]['dateofdelivery'] = date('Y-m-d 00:00:00', strtotime("+1 day"));
        }
        if (date('H') <= 11 && $id_carrier == 50)
        {
          $date_a[0]['period'] = 138;
          $date_a[0]['dateofdelivery'] = date('Y-m-d 00:00:00');
        }
        if (date('H') > 11 && $id_carrier == 50)
        {
          $date_a[0]['period'] = 138;
          $date_a[0]['dateofdelivery'] = date('Y-m-d 00:00:00', strtotime("+1 day"));
        }
      }
    
    
    
        
    $sql_upd_orr = "UPDATE `"._DB_PREFIX_."orders` SET `dateofdelivery` = '".$date_a[0]['dateofdelivery']."', `period` = ".$date_a[0]['period']."  WHERE `id_order` = $id_order";
    Db::getInstance()->execute($sql_upd_orr);
  }
  
  public function hookActionAdminControllerSetMedia($params)
  {
    $module_name = "";
    if ( isset ( $_GET['configure'] ) ) $module_name = $_GET['configure'] ;
    
    if ( $module_name == 'nove_dateofdelivery'  ) 
     {
      $this->context->controller->addJS($this->_path.'js/jquery.maskedinput.js');
      $this->context->controller->addJS($this->_path.'js/nove_dateofdelivery.js');
     }

  }
  
  public function hookDisplayHome($params)
  {

    $id_customer = (int)$this->context->customer->id;
    
   // $id_shop = (int)$this->context->shop->id;
    $id_shop = (int)$params['id_shop'];
    $id_lang = (int)$this->context->cookie->id_lang;
/*    if ($id_customer == 5)
    {
     dump($id_shop);             
    }  */
    switch ($id_shop) {
        case 1:
            $carrier_active = array(27,22,51,56);
            $carrier_active_display = array(
              array('id'=>27,'name'=>$this->l('Доставка курьером')),
              array('id'=>51,'name'=>$this->l('Fozzy-Drive (Самовывоз - пр. C. Бандеры 23)')),
              array('id'=>22,'name'=>$this->l('Fozzy-Drive (Самовывоз - ул. Заболотного 37)')),
              array('id'=>56,'name'=>$this->l('Fozzy-Drive (Самовывоз - с. Пролиски, ул. Броварская, 2а)')),
              );
            break;
        case 2:
            $carrier_active = array(30,24);
            $carrier_active_display = array(
              array('id'=>30,'name'=>$this->l('Доставка курьером')),
              array('id'=>24,'name'=>$this->l('Fozzy-Drive (Самовывоз - ул. Балковская, 88)')),
              );
            break;
        case 3:
            $carrier_active = array(33,32);     
            $carrier_active_display = array(
              array('id'=>33,'name'=>$this->l('Доставка курьером')),
              array('id'=>32,'name'=>$this->l('Fozzy-Drive (Самовывоз - ул Маршала Малиновского 2)')),
              );
            break;
        case 4:
            $carrier_active = array(42,43);
            $carrier_active_display = array(
              array('id'=>42,'name'=>$this->l('Доставка курьером')),
              array('id'=>43,'name'=>$this->l('Fozzy-Drive (Самовывоз - ул. Героев Труда, 9)')),
              );
            break;
        case 8:
            $carrier_active = array(55,54);
            $carrier_active_display = array(
              array('id'=>55,'name'=>$this->l('Доставка курьером')),
              array('id'=>54,'name'=>$this->l('Fozzy-Drive (Самовывоз - ул.Курчатова, 9)')),
              );
            break;
        case 9:
            $carrier_active = array(57,58);
            $carrier_active_display = array(
              array('id'=>57,'name'=>$this->l('Доставка курьером')),
              array('id'=>58,'name'=>$this->l('Fozzy-Drive (Самовывоз - ул Киевская 66г)')),
              );
            break;
        }
    
    //$periods = $this->getLinks(true, 0, $carrier_active);
     $periods = $this->getLinks2(true, 0, $carrier_active,$id_shop);
     if ($periods) {
         $today = explode("/", date ('d/m/Y'));
         $time_hour = date ('H:i'); 
         $today = mktime(0,0,0,$today[1],$today[0],$today[2]);
         $now = time();
         $holidays_all = $this->getHolidaysFront(date('Y-m-d'));
         $sun = Configuration::get('NDOD2_SUN');
         $sat = Configuration::get('NDOD2_SAT');
         $daytoday = Configuration::get('NDOD2_DAYTODAY');
         $delhol = Configuration::get('NDOD2_DELHOL');
          
         $days_delivery = array();
         
         $days_open = (int)Configuration::get('NDOD2_DAYSOFDELIVERY');
         $arr_day = [
            'Воскресенье',
            'Понедельник',
            'Вторник',
            'Среда',
            'Четверг',
            'Пятница',
            'Суббота'
          ];
         $id_lang = (int)$this->context->cookie->id_lang;
         if ($id_lang == 2) {
           $arr_day = [
              'Неділя',
              'Понеділок',
              'Вівторок',
              'Середа',
              'Четвер',
              'П`ятница',
              'Субота'
            ];
         }
         $windowsclose = 0;
         if (date('H') > 19) {            
            $windowsclose = 1;          
          }                               
         
         //Britoff - 10.04.2019 - по просьбе Стеценко отключил закрытие завтрашних утрених окон после 18-00 сегодня нахуй!
         //Britoff - 18.04.2019 - по просьбе Оксаны включил нахуй закрытие завтрашних утрених окон после 20-00 сегодня!                                                         
         //Britoff - 05.09.2019 - по просьбе Стеценко отключил закрытие завтрашних утрених окон после 20-00 сегодня нахуй!
         //Britoff - 22.04.2020 - по просьбе Стеценко включил закрытие завтрашних утрених окон после 20-00 сегодня нахуй!
               
         for ($i = 0; $i < $days_open; $i++) {
          if (!$daytoday && $i == 0)  
            {
            $days_open++;
            continue; 
            }
          if ($sat && date('w', strtotime("+$i day")) == 6) 
            {
            $days_open++;
            continue;
            }
          if ($sun && date('w', strtotime("+$i day")) == 0)  
            {
            $days_open++;
            continue; 
            }
          $delholt=0;
          if ($delhol)  
            {
            foreach ($holidays_all as $holiday)    
              {
               if ($holiday['period'] == 0 && $holiday['date'] == date ('Y-m-d',strtotime("+$i day"))) 
                {
                 $delholt=1;
                 break;       
                } 
              }
          if ($delholt)  
            {
            $days_open++;
            continue; 
            }
          }
          $days_delivery[$i] = date ('d.m.Y', strtotime("+$i day")); 
          $days_delivery_cl[$i]['data'] = date ('d.m.Y', strtotime("+$i day"));
          if (date ('N', strtotime("+$i day")) == 7) $dataUr = 0;
          else $dataUr = date ('N', strtotime("+$i day"));
          $days_delivery_cl[$i]['dataU'] = $arr_day[$dataUr];
          $days_delivery_cl[$i]['week'] = (int)date ('W', strtotime("+$i day")); //Номера недель для дней разрешенных к доставке
          $days_delivery_cl[$i]['day'] = date ('D', strtotime("+$i day"));  //Названия дней для дней разрешенных к доставке
         }
  
         //Britoff - Мультсклады по городу
         
         switch ($id_shop) {
              case 1:
                  $shops_to_close = '1,25,30';
                  break;
              case 2:
                  $shops_to_close = '2';
                  break;
              case 3:
                  $shops_to_close = '3';
                  break;
              case 4:
                  $shops_to_close = '4';
                  break;
              case 8:
                  $shops_to_close = '8';
                  break;
              case 9:
                  $shops_to_close = '9';
                  break;
              default:
                  $shops_to_close = '1,25,30';
                  break;
          }
         
          
         for ($i = 0; $i < (int)Configuration::get('NDOD2_DAYSOFDELIVERY'); $i++) 
              {
               $sql_windows_close = "SELECT SUM(".$days_delivery_cl[$i]['day'].") as plan, `window` FROM `"._DB_PREFIX_."nove_dateofdelivery_block` WHERE `week` = ".$days_delivery_cl[$i]['week']." AND `id_shop` IN (".$shops_to_close.") GROUP BY `window` ORDER BY `window`";
               
               $windows_close = Db::getInstance()->executeS($sql_windows_close);
                foreach ($windows_close as $key=>$window) {
                   $windows_close[$key]['close'] = 0;
                   $sql_cart_w = "SELECT cart_id FROM "._DB_PREFIX_."nove_dateofdelivery_cart WHERE period = ".$window['window']." AND dateofdelivery = '".date ('Y-m-d', strtotime($days_delivery_cl[$i]['data']))."'";                            
                   $cart_w = Db::getInstance()->executeS($sql_cart_w);
                   
                   $carts = array();
                   foreach ($cart_w AS $cart)
                    {
                       $carts[]= $cart['cart_id']; 
                    }
                    if (!$carts) continue;   
                    $sql_order_in_window = "SELECT COUNT(id_order) as odr FROM "._DB_PREFIX_."orders WHERE id_cart IN (".implode(",",$carts).") AND current_state IN (SELECT `id_order_state` FROM `"._DB_PREFIX_."order_state` WHERE `window` = 1) AND id_carrier IN (22,51,56,24,32,43,54,27,30,33,42,55,58,57) AND id_shop = ".$id_shop;
                    $order_count = Db::getInstance()->executeS($sql_order_in_window);                                                                                                                                                                                  
  
                    $windows_close[$key]['close'] = $order_count[0]['odr'];
  
                }
               $days_delivery_cl[$i]['close_on'] = $windows_close;
              }
         
          foreach ($periods as $key=>$perv)
              {
              $periods[$key]['timeoff'] = strtotime($perv['timeoff']);
              $periods[$key]['timenow'] = time();
              }
  
          $this->smarty->assign(array(
                  'periods'=>$periods,
                  'carrier_active' => $carrier_active_display,
                  'days_delivery'=>$days_delivery_cl,
                  'holidays' => $holidays_all,
                  'today'=>date ('d.m.Y', time()),
                  'tomorrow'=>date ('d.m.Y', strtotime("+1 day")),
                  'windowsclose' => $windowsclose,
                  ));
        
      		return $this->display(__FILE__, 'home.tpl');       
      }    
    
    
   // }
  }
  
  public function hookBeforeCarrier($params)
  {
   $id_shop = (int)$this->context->shop->id;
   if (isset($params['delivery_option'])) $carrier_active = (int)implode(",",$params['delivery_option']);
   else  $carrier_active = null;
   if (!$carrier_active)  $carrier_active = (int)$params['cart']->id_carrier;
   if (!$carrier_active)  
      {
      switch ($id_shop) {
        case 1:
            $carrier_active = 27;
            break;
        case 2:
            $carrier_active = 30;
            break;
        case 3:
            $carrier_active = 33;
            break;
        case 4:
            $carrier_active = 42;
            break;
        case 8:
            $carrier_active = 55;
            break;
        case 9:
            $carrier_active = 57;
            break;
        }
      }
  
   $periods = $this->getLinks(true, 0, $carrier_active);
   if ($periods) {
       $today = explode("/", date ('d/m/Y'));
       $time_hour = date ('H:i'); 
       $today = mktime(0,0,0,$today[1],$today[0],$today[2]);
       $now = time();
       $holidays_all = $this->getHolidaysFront(date('Y-m-d'));
       $sun = Configuration::get('NDOD2_SUN');
       $sat = Configuration::get('NDOD2_SAT');
       $daytoday = Configuration::get('NDOD2_DAYTODAY');
       $delhol = Configuration::get('NDOD2_DELHOL');
        
       $days_delivery = array();
       
       $days_open = (int)Configuration::get('NDOD2_DAYSOFDELIVERY');
       $arr_day = [
          'Воскресенье',
          'Понедельник',
          'Вторник',
          'Среда',
          'Четверг',
          'Пятница',
          'Суббота'
        ];
       $id_lang = (int)$this->context->cookie->id_lang;
       if ($id_lang == 2) {
         $arr_day = [
            'Неділя',
            'Понеділок',
            'Вівторок',
            'Середа',
            'Четвер',
            'П`ятница',
            'Субота'
          ];
       }
       $windowsclose = 0;
       if (date('H') > 19) {            
          $windowsclose = 1;          
        }                               
       
       //Britoff - 10.04.2019 - по просьбе Стеценко отключил закрытие завтрашних утрених окон после 18-00 сегодня нахуй!
       //Britoff - 18.04.2019 - по просьбе Оксаны включил нахуй закрытие завтрашних утрених окон после 20-00 сегодня!                                                         
       //Britoff - 05.09.2019 - по просьбе Стеценко отключил закрытие завтрашних утрених окон после 20-00 сегодня нахуй!
       //Britoff - 22.04.2020 - по просьбе Стеценко включил закрытие завтрашних утрених окон после 20-00 сегодня нахуй!
             
       for ($i = 0; $i < $days_open; $i++) {
        if (!$daytoday && $i == 0)  
          {
          $days_open++;
          continue; 
          }
        if ($sat && date('w', strtotime("+$i day")) == 6) 
          {
          $days_open++;
          continue;
          }
        if ($sun && date('w', strtotime("+$i day")) == 0)  
          {
          $days_open++;
          continue; 
          }
        $delholt=0;
        if ($delhol)  
          {
          foreach ($holidays_all as $holiday)    
            {
             if ($holiday['period'] == 0 && $holiday['date'] == date ('Y-m-d',strtotime("+$i day"))) 
              {
               $delholt=1;
               break;       
              } 
            }
        if ($delholt)  
          {
          $days_open++;
          continue; 
          }
        }
        $days_delivery[$i] = date ('d.m.Y', strtotime("+$i day")); 
        $days_delivery_cl[$i]['data'] = date ('d.m.Y', strtotime("+$i day"));
        if (date ('N', strtotime("+$i day")) == 7) $dataUr = 0;
        else $dataUr = date ('N', strtotime("+$i day"));
        $days_delivery_cl[$i]['dataU'] = $arr_day[$dataUr];
        $days_delivery_cl[$i]['week'] = (int)date ('W', strtotime("+$i day")); //Номера недель для дней разрешенных к доставке
        $days_delivery_cl[$i]['day'] = date ('D', strtotime("+$i day"));  //Названия дней для дней разрешенных к доставке
       }

       //Britoff - Мультсклады по городу
       
       switch ($id_shop) {
            case 1:
                $shops_to_close = '1,25,30';
                break;
            case 2:
                $shops_to_close = '2';
                break;
            case 3:
                $shops_to_close = '3';
                break;
            case 4:
                $shops_to_close = '4';
                break;
            case 8:
                  $shops_to_close = '8';
                  break;
            case 9:
                  $shops_to_close = '9';
                  break;
            default:
                $shops_to_close = '1,25,30';
                break;
        }
       
        
       for ($i = 0; $i < (int)Configuration::get('NDOD2_DAYSOFDELIVERY'); $i++) 
            {
           //  $sql_windows_close = "SELECT ".$days_delivery_cl[$i]['day']." as plan, window FROM "._DB_PREFIX_."nove_dateofdelivery_block WHERE week = ".$days_delivery_cl[$i]['week']." AND id_shop IN (".$shops_to_close.")";
             $sql_windows_close = "SELECT SUM(".$days_delivery_cl[$i]['day'].") as plan, `window` FROM `"._DB_PREFIX_."nove_dateofdelivery_block` WHERE `week` = ".$days_delivery_cl[$i]['week']." AND `id_shop` IN (".$shops_to_close.") GROUP BY `window` ORDER BY `window`";
             
             $windows_close = Db::getInstance()->executeS($sql_windows_close);
              foreach ($windows_close as $key=>$window) {
                 $windows_close[$key]['close'] = 0;
                 $sql_cart_w = "SELECT cart_id FROM "._DB_PREFIX_."nove_dateofdelivery_cart WHERE period = ".$window['window']." AND dateofdelivery = '".date ('Y-m-d', strtotime($days_delivery_cl[$i]['data']))."'";                            
                 $cart_w = Db::getInstance()->executeS($sql_cart_w);
                 
                 $carts = array();
                 foreach ($cart_w AS $cart)
                  {
                     $carts[]= $cart['cart_id']; 
                  }
                  if (!$carts) continue;   
                  $sql_order_in_window = "SELECT COUNT(id_order) as odr FROM "._DB_PREFIX_."orders WHERE id_cart IN (".implode(",",$carts).") AND current_state IN (SELECT `id_order_state` FROM `"._DB_PREFIX_."order_state` WHERE `window` = 1) AND id_carrier IN (22,51,56,24,32,43,54,27,30,33,42,55,57,58)  AND id_shop = ".$id_shop;
                  $order_count = Db::getInstance()->executeS($sql_order_in_window);

                  $windows_close[$key]['close'] = $order_count[0]['odr'];

              }
             $days_delivery_cl[$i]['close_on'] = $windows_close;
            }
       
       $id_customer = (int)$this->context->customer->id;
       $id_cart = $params['cart']->id;
   /* Получение даты и периода из текущей корзины и очистка его в случае прошедшего времени */
/*       
       if ($id_cart) {
          $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
          $cart_time = Db::getInstance()->executeS($sql_select);
       }
  
    
       if (isset($cart_time) && count($cart_time) > 0) {
            $cart_date = $cart_time[0]['cart_date'];
            $period = (int)$cart_time[0]['period'];
            $cart_date_a = explode(".", $cart_date);
    
            $e_period = $this->getLinks(false, $period, $carrier_active);
            $e_time = explode(":", $e_period[0]['timefrom']);
            
            $cart_date_u = mktime($e_time[0],$e_time[1],$e_time[2],$cart_date_a[1],$cart_date_a[0],$cart_date_a[2]);
             if ($cart_date_u < $now)
              {
                $sql_del_old_period = "DELETE FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
                Db::getInstance()->execute($sql_del_old_period);
              } 
            
            foreach ($holidays_all as $holiday)
            {
             if ($holiday['period'] != 0 ) 
              {
               $h_period = $this->getLinks(false, $holiday['period'], $carrier_active);
               $h_date = $holiday['date'];
               $h_date_a = explode("-", $holiday['date']);
               $holiday_date = $h_date_a[2].".".$h_date_a[1].".".$h_date_a[0];
               if ( ($cart_date == $holiday_date) && ( $e_period[0]['timefrom'] == $h_period[0]['timefrom'] ) )
                {
                  $sql_del_old_period = "DELETE FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
                  Db::getInstance()->execute($sql_del_old_period);
                }       
                
              } 
            }
            
            }
          else
            {
              $cart_date = null;
              $period = null;
            }           */
    /* /Получение даты и периода из текущей корзины и очистка его в случае прошедшего времени */
        
    /* Очистка даты доставки мертово!! */
    $sql_del_old_period = "DELETE FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
    Db::getInstance()->execute($sql_del_old_period);
    $cart_date = null;
    $period = null;
    /* /Очистка даты доставки мертово!! */
        
        
        foreach ($periods as $key=>$perv)
            {
            $periods[$key]['timeoff'] = strtotime($perv['timeoff']);
            $periods[$key]['timenow'] = time();
            }

            
        $carr = new Carrier ($carrier_active);
        $this->smarty->assign(array(
                'periods'=>$periods,
                'days_delivery'=>$days_delivery_cl,
                'period'=>(int)$period,
                'id_cart'=>$id_cart,
                'car_active' => $carrier_active,
                'holidays' => $holidays_all,
                'cart_date'=>date ('d.m.Y', strtotime($cart_date)),
                'today'=>date ('d.m.Y', time()),
                'tomorrow'=>date ('d.m.Y', strtotime("+1 day")),
                'windowsclose' => $windowsclose,
                'carrir_name' => $carr->name
                ));
      
    		return $this->display(__FILE__, 'nove_dateofdelivery.tpl');
    }
    else
    {
        $carr = new Carrier ($carrier_active);
        $this->smarty->assign(array(
                'tomorrow'=>date ('d.m.Y', strtotime("+1 day")),
                'tomorrow1'=>date ('d.m.Y', strtotime("+2 day")),
                'carrir_name' => $carr->name
                ));
        return $this->display(__FILE__, 'nove_dateofdelivery_clear.tpl');
    }
  }
  public function hookdisplayOrderConfirmation($params)
	{
   $id_cart = (int)$params['order']->id_cart;
    
    $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period  FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
    $cart = Db::getInstance()->executeS($sql_select);
      
      if (count($cart) > 0)
        {
        $cart_date = $cart[0]['cart_date'];
        $period = $cart[0]['period'];
        $links = $this->getLinks(0, (int)$period );
        $active = $links[0]['active'];
        $timefrom = $links[0]['timefrom'];
        $timeoff = $links[0]['timeoff'];
        $timeto = $links[0]['timeto'];
        $car_del = (int)$links[0]['carriers'];
        $carriers = Carrier::getCarriers ($this->context->language->id, true);

        foreach ($carriers as $carrier)
        {
          if ( (int)$carrier['id_carrier'] == $car_del )
           {
             $car_del_name = $carrier['name'];
           }
        }
          
        }
      else
        {
        $cart_date = null;
        $period = null;
        $active = null;
        $timefrom = null;
        $timeoff = null;
        $timeto = null;
        }
     
        
      
		$this->smarty->assign(array(
          'cart_date' => $cart_date,
          'period'=>(int)$period,
          'active' => $active,
          'timefrom' => $timefrom,
          'timeoff' => $timeoff,
          'car_del' => $car_del_name,
          'timeto' => $timeto
          ));

		return $this->display(__FILE__, 'orderDetailClient2.tpl');
  }
  
  public function hookOrderDetailDisplayed($params)
	{

    $id_cart = (int)$params['order']->id_cart;
    
    $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period  FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
    $cart = Db::getInstance()->executeS($sql_select);
      
      if (count($cart) > 0)
        {
        $cart_date = $cart[0]['cart_date'];
        $period = $cart[0]['period'];
        $links = $this->getLinks(0, (int)$period );
        $active = $links[0]['active'];
        $timefrom = $links[0]['timefrom'];
        $timeoff = $links[0]['timeoff'];
        $timeto = $links[0]['timeto'];
        $car_del = (int)$links[0]['carriers'];
        $carriers = Carrier::getCarriers ($this->context->language->id, true);

        foreach ($carriers as $carrier)
        {
          if ( (int)$carrier['id_carrier'] == $car_del )
           {
             $car_del_name = $carrier['name'];
           }
        }
          
        }
      else
        {
        $cart_date = null;
        $period = null;
        $active = null;
        $timefrom = null;
        $timeoff = null;
        $timeto = null;
        }
     
        
      
		$this->smarty->assign(array(
          'cart_date' => $cart_date,
          'period'=>(int)$period,
          'active' => $active,
          'timefrom' => $timefrom,
          'timeoff' => $timeoff,
          'car_del' => $car_del_name,
          'timeto' => $timeto
          ));

		return $this->display(__FILE__, 'orderDetailClient.tpl');
	}
  
  public function hookActionCarrierUpdate($params) {

   $old_carrier_id = (int)$params['id_carrier'];
   $new_carrier_id = (int)$params['carrier']->id;
   
		$sql_carrier = 'UPDATE '._DB_PREFIX_.'nove_dateofdelivery SET `carriers` = '. $new_carrier_id .' WHERE `carriers` = '.$old_carrier_id;
    Db::getInstance()->execute($sql_carrier);
    
	}
  
}