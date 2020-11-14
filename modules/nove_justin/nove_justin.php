<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_ . 'nove_justin/classes/api_get.php');
require_once(_PS_MODULE_DIR_ . 'nove_justin/classes/justin.php');

class Nove_justin extends CarrierModule
{
protected $config_form = false;

public function __construct()
    {
        $this->name = 'nove_justin';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 1;
        $this->controllers = array('ware');
        
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('JustIn');
        $this->description = $this->l('Shipping module for JustIn');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

public function install()
    {
        if (extension_loaded('curl') == false)
        {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }
        include(dirname(__FILE__).'/sql/install.php');
        $carrier = $this->addCarrier();
        $this->addZones($carrier);
        $this->addGroups($carrier);
        $this->addRanges($carrier);
        Configuration::updateValue('NV_JUSTIN_API_KEY', '');
        Configuration::updateValue('NV_JUSTIN_API_PASS', '');
        Configuration::updateValue('NV_JUSTIN_API_LOGIN', '');


        return parent::install() &&
              $this->registerHook('displayCarrierExtraContent') &&
              $this->registerHook('displayaftercarrier') &&
              $this->registerHook('actionCarrierUpdate') &&
              $this->registerHook('actionCartSave') &&
              $this->registerHook('actionValidateOrder') &&
              $this->registerHook('displayOrderDetail') &&
              $this->registerHook('displayAdminOrder') &&
              $this->registerHook('DisplayHeader') &&
              $this->registerHook('DisplayBackOfficeHeader') &&
              $this->registerHook('actionObjectOrderUpdateBefore') &&
              $this->registerHook('actionObjectOrderUpdateAfter') &&
              $this->registerHook('actionObjectOrderCartRuleAddAfter') &&
              $this->registerHook('actionObjectOrderCartRuleDeleteAfter') &&
              $this->registerHook('actionObjectOrderDetailAddAfter') &&
              $this->registerHook('actionObjectOrderDetailDeleteAfter') &&
              $this->registerHook('actionObjectOrderDetailUpdateAfter') &&
              $this->registerHook('ExtraCarrier');
    }

public function uninstall()
    {
        Configuration::deleteByName('NV_JUSTIN_API_KEY');
        Configuration::deleteByName('NV_JUSTIN_CARRIER_ID');
        Configuration::deleteByName('NV_JUSTIN_API_LOGIN');
        Configuration::deleteByName('NV_JUSTIN_API_PASS');
        include(dirname(__FILE__).'/sql/uninstall.php');
        return parent::uninstall();
    }

public function j_fill_regions()
    {
     $justin = new JustIn(Configuration::Get('NV_JUSTIN_API_LOGIN'),Configuration::Get('NV_JUSTIN_API_PASS'),Configuration::Get('NV_JUSTIN_API_KEY'),'RU');
     $regions = $justin->getRegions();
      $sql_clear = 'TRUNCATE `' . _DB_PREFIX_ . 'nv_justin_region`';
      Db::getInstance()->execute($sql_clear); 
      $sql_regions = 'INSERT INTO `' . _DB_PREFIX_ . 'nv_justin_region`(`uuid`, `code`, `descr`, `SCOATOU`) VALUES ';
      foreach ($regions['data'] as $region)
        {
        if ($region['fields']['uuid']) $sql_regions .= "('".$region['fields']['uuid']."','".$region['fields']['code']."','".addslashes($region['fields']['descr'])."', '".$region['fields']['SCOATOU']."'),";
        }
      $sql_regions = substr($sql_regions, 0, -1);
      $return = Db::getInstance()->execute($sql_regions);       
      return $return;        
    }
public function j_fill_towns()
    {
     $justin = new JustIn(Configuration::Get('NV_JUSTIN_API_LOGIN'),Configuration::Get('NV_JUSTIN_API_PASS'),Configuration::Get('NV_JUSTIN_API_KEY'),'RU');
     $towns = $justin->getCities();
      $sql_clear = 'TRUNCATE `' . _DB_PREFIX_ . 'nv_justin_towns`';
      Db::getInstance()->execute($sql_clear);
      $sql_towns = 'INSERT INTO `' . _DB_PREFIX_ . 'nv_justin_towns`(`uuid`, `code`, `descr`, `owner_uuid`) VALUES ';
      foreach ($towns['data'] as $town)
        {
        if ($town['fields']['uuid']) $sql_towns .= "('".$town['fields']['uuid']."','".$town['fields']['code']."','".addslashes($town['fields']['descr'])."', '".$town['fields']['objectOwner']['uuid']."'),";
        }
      $sql_towns = substr($sql_towns, 0, -1);
      $return = Db::getInstance()->execute($sql_towns);       
      return $return;        
    }
public function j_fill_wares()
    {
     $justin = new JustIn(Configuration::Get('NV_JUSTIN_API_LOGIN'),Configuration::Get('NV_JUSTIN_API_PASS'),Configuration::Get('NV_JUSTIN_API_KEY'),'RU');
     $wares = $justin->getDepartments();
      $sql_clear = 'TRUNCATE `' . _DB_PREFIX_ . 'nv_justin_ware`';
      Db::getInstance()->execute($sql_clear);
      $sql_wares = 'INSERT INTO `' . _DB_PREFIX_ . 'nv_justin_ware`(`branch`, `code`, `descr`, `owner_uuid`) VALUES ';
      foreach ($wares['data'] as $ware)
        {
        if ($ware['fields']['branch']) $sql_wares .= "('".$ware['fields']['branch']."','".$ware['fields']['code']."','".addslashes($ware['fields']['descr']." (".$ware['fields']['street']['descr'].", ".$ware['fields']['houseNumber'].")")."','".$ware['fields']['city']['uuid']."'),";
        }
      $sql_wares = substr($sql_wares, 0, -1);
      $return = Db::getInstance()->execute($sql_wares);       
      return $return;        
    }
       
public function getContent()
    {

        if (((bool)Tools::isSubmit('submit_nv_justin')) == true) {
            $this->postProcess();
            $m = Meta::getMetaByPage('module-nove_justin-ware',Configuration::get('PS_LANG_DEFAULT'));
            $meta = new Meta();
            $meta->page = 'module-nove_justin-ware';
            $meta->configurable = 0;
            $meta->url_rewrite = 'justin-ware';
            $meta->id = $m->id_meta;
            $meta->update();        
             
        }
        
        if (Tools::isSubmit('btnUpdate'))
        		{
            $this->postProcess();
            
            $ok_reg = $this->j_fill_regions(); 
            $ok_town = $this->j_fill_towns(); 
            $ok_ware = $this->j_fill_wares(); 
            }
          
        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $output = '';
        
        return $output.$this->renderForm();
    }

protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_nv_justin';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid api key'),
                        'name' => 'NV_JUSTIN_API_KEY',
                        'label' => $this->l('Api key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'name' => 'NV_JUSTIN_API_LOGIN',
                        'label' => $this->l('Api login'),
                        'desc' => $this->l('Enter a valid api login'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'name' => 'NV_JUSTIN_API_PASS',
                        'desc' => $this->l('Enter a valid api password'),
                        'label' => $this->l('Api password'),
                    ),
                ),
                'buttons'	=> array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Update warehouses'),
                    'icon' => 'process-icon-download',
                    'name' => 'btnUpdate',
                    'id'   => 'btnUpdate',
                    'class'=> 'pull-left'
                )
               ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

protected function getConfigFormValues()
    {
        return array(
            'NV_JUSTIN_API_KEY' => Configuration::get('NV_JUSTIN_API_KEY', 'contact@prestashop.com'),
            'NV_JUSTIN_API_LOGIN' => Configuration::get('NV_JUSTIN_API_LOGIN', null),
            'NV_JUSTIN_API_PASS' => Configuration::get('NV_JUSTIN_API_PASS', null),
        );
    }

protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

public function getOrderShippingCost($params, $shipping_cost)
    {
      $cost = 0;
      $summ = 0;
      if (Context::getContext()->cart)
      foreach (Context::getContext()->cart->getProducts() as $product)
      {
        $summ = $summ + $product['total'];
      }
      if ($summ < 200)
        {
         $cost = $shipping_cost;
        }
      else
        {
         $cost = $shipping_cost + $summ*0.005;
        }
      /*   if (Context::getContext()->customer->logged == true)
        {
            $id_address_delivery = Context::getContext()->cart->id_address_delivery;
            $address = new Address($id_address_delivery);


            return 10;
        }
      */
        return $cost; 
    }

public function getOrderShippingCostExternal($params)
    {
        return true;
    }

protected function addCarrier()
    {
        $carrier = new Carrier();

        $carrier->name = $this->l('JustIN');
        $carrier->is_module = true;
        $carrier->active = 0;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = true;
        $carrier->range_behavior = 1;
        $carrier->external_module_name = $this->name;
        $carrier->shipping_method = 1;
        $carrier->shipping_handling = 0;

        foreach (Language::getLanguages() as $lang)
            $carrier->delay[$lang['id_lang']] = $this->l('Super fast delivery');

        if ($carrier->add() == true)
        {
            @copy(dirname(__FILE__).'/views/img/carrier_image.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg');
            Configuration::updateValue('NV_JUSTIN_CARRIER_ID', (int)$carrier->id);
            return $carrier;
        }

        return false;
    }

protected function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group)
            $groups_ids[] = $group['id_group'];

        $carrier->setGroups($groups_ids);
    }

protected function addRanges($carrier)
    {
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '0';
        $range_weight->delimiter2 = '10';
        $range_weight->add();
        
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '10';
        $range_weight->delimiter2 = '20';
        $range_weight->add();
        
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '20';
        $range_weight->delimiter2 = '30';
        $range_weight->add();
        
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '30';
        $range_weight->delimiter2 = '40';
        $range_weight->add();
        
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '40';
        $range_weight->delimiter2 = '50';
        $range_weight->add();
    }

protected function addZones($carrier)
    {
        $zones = Zone::getZones();

        foreach ($zones as $zone)
            $carrier->addZone($zone['id_zone']);
    }

public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

public function hookUpdateCarrier($params)
    {
        /**
         * Not needed since 1.5
         * You can identify the carrier by the id_reference
        */
    }

public function hookDisplayAdminOrderContentOrder()
    {
        /* Place your code here. */
    }

public function hookDisplayAdminOrderContentShip()
    {
        /* Place your code here. */
    }
    
    
public function hookdisplayCarrierExtraContent($params) {
		$just_id = Configuration::get('NV_JUSTIN_CARRIER_ID');
    $carrier = new Carrier($params['cart']->id_carrier);
    $carrier_id = $carrier->id_reference; 
    $id_cart = $params['cart']->id;
    $del_sum = $carrier->getDeliveryPriceByWeight(30,1);
    //$md_carriers = json_decode(Configuration::get('ecm_md_carriers'),true);
		//setcookie('md_carriers', json_encode($md_carriers), time() + 600, '/', Tools::getShopDomain());
    $sql_select = "SELECT * FROM `"._DB_PREFIX_."nv_justin_carts` WHERE `id_cart`=".$id_cart." LIMIT 1";
    $sel_addr = Db::getInstance()->executeS($sql_select);
    $region = 0;
    $town = 0;
    $ware = 0;
    if (count($sel_addr) > 0) {
      $region = $sel_addr[0]['region'];
      $town = $sel_addr[0]['town'];
      $ware = $sel_addr[0]['ware'];
    }
    
    $areas = Justin_get::areaList();
    $areas_select = array();
    $areas_select[0]='Выберите область';
    foreach ($areas as $area) {
       $areas_select[$area['uuid']]=$area['descr'];
    }
    
    $towns = Justin_get::areaList($town);
    $towns_select = array();
    $towns_select[0]='Выберите город';
    foreach ($towns as $town) {
       $towns_select[$town['uuid']]=$town['descr'];
    }
    
    $wares = Justin_get::areaList($town);
    $wares_select = array();
    $wares_select[0]='Выберите город';
    foreach ($wares as $ware) {
       $wares_select[$ware['uuid']]=$ware['descr'];
    }
    
		if ($just_id == $carrier_id) {
			setcookie('just_id', $just_id, time() + 600, '/', Tools::getShopDomain());
			//$cart_al = alexec::GetCartAL($params['cart']->id);
			//$select = alexec_::select($params['cart']->id_lang);
			//$cartdetails = alexec::cartdetails($params['cart']->id);
			//$address = alexec::CheckAddress($params['cart'], $al_id);
			$currency_cart = new Currency($params['cart']->id_currency);
			//$currency_uah = new Currency(Currency::getIdByIsoCode('UAH'));
			$currency_def = Currency::getDefaultCurrency();
			//$cart_al['cost'] = Tools::convertPriceFull($cart_al['cost'],$currency_def,$currency_cart);
			//$cart_al['costredelivery'] = Tools::convertPriceFull($cart_al['costredelivery'],$currency_def,$currency_cart);
			$this->context->smarty->assign(array(
				'just_id' => $just_id,
        'cart_id' => $id_cart,
        'cost' => $del_sum,
			//	'cart_al' => $cart_al,
			//	'fixcost' => Configuration::get(self::PREFIX . 'al_chk_fixcost'),
			//	'fill' => Configuration::get(self::PREFIX . 'np_fill'),
			//	'ac' => Configuration::get(self::PREFIX . 'np_ac'),
        'region' => $region,
        'town' => $town,
        'ware' =>  $ware,
				'Areas' => $areas_select,
				'Citys' => $towns_select,
				'Wares' => $wares_select
			));
			return $this->display(__FILE__, 'changeshipping.tpl');
		}
		else{
	//		if(!array_key_exists($params['cart']->id_carrier, $md_carriers) and Configuration::get(self::PREFIX . 'np_fill')){
				return $this->display(__FILE__, 'hide.tpl');
//			}
		}
	}     
    
  
  
  
      
    
}
