<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Nove_dateofdelivery_change extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'nove_dateofdelivery_change';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Date of delyvery for Fozzy - Change data');
        $this->description = $this->l('Module date of delyvery for Fozzy - Change data');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('displayPDFInvoice') &&
            $this->registerHook('displayPDFInvoice2') &&                        
            $this->registerHook('displayAdminOrder');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
      if ( get_class($this->context->controller) == 'AdminOrdersController' && Tools::GetValue('id_order') ) $this->context->controller->addJS($this->_path.'views/js/admin-control_06.js');
    }
    private function getLinks($active = true, $id = 0, $carriers = 0)
  	{
  		$links = array();
     
  		$sql = 'SELECT b.`id_period`, b.`timefrom`, b.`timeto`, b.`timeoff`, b.`active`, b.`express`, b.`carriers`, b.`carriers_name` FROM `'._DB_PREFIX_.'nove_dateofdelivery` b';
  		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
  			$sql .= ' JOIN `'._DB_PREFIX_.'nove_dateofdelivery_shop` bs ON b.`id_period` = bs.`id_period` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
  		  $sql .= ' WHERE  1';
        if ($active) $sql .= ' AND  (b.`active` = 1)';
        if ($id) $sql .= ' AND  (b.`id_period` = '. (int)$id . ") ";
        if ($carriers) $sql .= ' AND  (b.`carriers` = '. (int)$carriers . ") ";
        $sql .= ' ORDER BY  b.`carriers`, b.`timefrom` ASC';
      $links = Db::getInstance()->executeS($sql);
  
  		return $links;
  	}
    public function hookDisplayPDFInvoice($params)
    	{
      //  $cart = Cart::getCartByOrderId((int)$params['object']->id_order);
      //  $id_cart = (int)$cart->id;
        $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period FROM `"._DB_PREFIX_."orders` WHERE `id_order`=".$params['object']->id_order;
        $cart = Db::getInstance()->executeS($sql_select);
            if (count($cart) > 0)
              {
              $cart_date = $cart[0]['cart_date'];
              $period = (int)$cart[0]['period'];
              $links = $this->getLinks(0, $period);
              $active = $links[0]['active'];
              $timefrom = $links[0]['timefrom'];
              $timeoff = $links[0]['timeoff'];
              $timeto = $links[0]['timeto'];
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
              $errortext = $this->l('From')." ".$links[0]['timefrom']." ".$this->l('to')." ".$links[0]['timeto']; 
              if ($period) $period = Tools::displayError($errortext); 
            
        if ($cart_date) return '<p><b>'.$this->l('Date of delivery:').'</b> <span style="font-size:18pt;">'.$cart_date.'</span><br><b>'.$this->l('Period of delivery:').'</b> <span style="font-size:18pt;">'.$errortext.'</span></p>';
        else  return '';
      
    	}
  
  public function hookDisplayPDFInvoice2($params)
  	{
    //  $cart = Cart::getCartByOrderId((int)$params['order']);
    //  $id_cart = (int)$cart->id;
      $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period FROM `"._DB_PREFIX_."orders` WHERE `id_order`=".$params['order'];
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
            $errortext = $this->l('From')." ".$links[0]['timefrom']." ".$this->l('to')." ".$links[0]['timeto']; 
            if ($period) $period = Tools::displayError($errortext); 
          
      if ($cart_date) return '<p><b>'.$this->l('Date of delivery:').'</b> <span style="font-size:12pt;">'.$cart_date.'</span><br><b>'.$this->l('Period of delivery:').'</b> <span style="font-size:12pt;">'.$errortext.'</span></p>';
      else  return '';
    
  	}  
    
    public function hookDisplayAdminOrder($params)
    	{
        
        $order = new Order((int)$params['id_order']);
        $id_cart = (int)$params['cart']->id;
        $id_shop = (int)$params['cart']->id_shop;
        if (!$id_cart) $id_cart = (int)$order->id_cart;
        if (!$id_shop) $id_shop = (int)$order->id_shop; 
     //   dump($id_cart);
        
        $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period, id_vodila, id_carrier FROM `"._DB_PREFIX_."orders` WHERE `id_order`=".$params['id_order'];
         
        $cart = Db::getInstance()->executeS($sql_select);
            
            if (count($cart) > 0)
              {
              $cart_date = $cart[0]['cart_date'];
              $period = $cart[0]['period'];
              $id_vodila = $cart[0]['id_vodila'];
              $id_carrier = $cart[0]['id_carrier'];
              }
            else
              {
              $cart_date = null;
              $period = null;
              $id_vodila = 0;
              $id_carrier = 0;
              }
        
        $employee = $this->context->employee->id;
        $employee_group = $this->context->employee->id_profile;
        $modules_restrictions = Module::getModulesAccessesByIdProfile($employee_group);
        $this_module_restrictions = $modules_restrictions['NOVE_DATEOFDELIVERY_CHANGE'];    

        if ($this_module_restrictions['configure'] == 1) $can_change = 1;  
        else $can_change = 0;
        
        if ($id_vodila) $can_change = 0;
        
        if ($employee_group == 1 || $employee_group == 8 || $employee_group == 10 || $employee_group == 11) $super_change = 1;
        else $super_change = 0;
        
        if ($employee_group == 1) {
 //       dump($cart);
        }
              
        Media::addJsDef(array(
            'cart_date' => $cart_date,
            'period' => (int)$period,
            'id_cart' => $id_cart,
            'employee' => $employee
            ));
            
        $period = (int)$period;
        $periods = $this->getLinks(true,0,$id_carrier);
        if ($id_carrier == 37 || $id_carrier == 50) $periods = $this->getLinks(false,0,$id_carrier);
       /*  
        $carriers = Carrier::getCarriers ($this->context->language->id, true);
       
        foreach ($periods as $key=>$periodik)
          {
           foreach ($carriers as $carrier)
           {
             if ( (int)$periodik['carriers'] == (int)$carrier['id_carrier'] )
              {
                $periods[$key]['carriers'] = $carrier['name'];
              }
           }
          }   
        */
        $this->smarty->assign(array(
            'cart_date' => $cart_date,
            'period' => $period,
            'periods' => $periods,
            'id_order' => (int)$params['id_order'],
            'id_cart' => $id_cart,
            'employee' => $employee,
            'can_change' => $can_change,
            'super_change' => $super_change
        ));
       return $this->display(__FILE__, 'orderDetailAdmin.tpl');
      }

}
