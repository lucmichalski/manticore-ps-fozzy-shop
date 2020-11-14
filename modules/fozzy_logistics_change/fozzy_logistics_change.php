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

class Fozzy_logistics_change extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fozzy_logistics_change';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Logistics for Fozzy - Change personal');
        $this->description = $this->l('Module for advandced logistics for Fozzy - Change personal');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
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
      
      if ( get_class($this->context->controller) == 'AdminOrdersController' && Tools::GetValue('id_order') ) $this->context->controller->addJS($this->_path.'views/js/admin-control.js');
    }

    public function hookDisplayAdminOrder($params)
    {
        $cart = Cart::getCartByOrderId((int)$params['id_order']);
        $id_cart = (int)$cart->id;
        $id_shop = (int)$params['cart']->id_shop;
        $order = new Order((int)$params['id_order']);
        if (!$id_cart) $id_cart = (int)$order->id_cart;
        if (!$id_shop) $id_shop = (int)$order->id_shop; 
        $sql_sv = "SELECT logs.`fio` as sborshik, logv.`fio` as vodila, logv.`phone` as vodilaphone FROM `"._DB_PREFIX_."orders` log ";
        $sql_sv .= 'LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic_sborshik` logs ON (logs.`id_sborshik` = log.`id_sborshik`) ';
        $sql_sv .= 'LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic_vodila` logv ON (logv.`id_vodila` = log.`id_vodila`) ';
        $sql_sv .= 'WHERE log.`id_order` = '.(int)$params['id_order'];
        
        $sql_vodila = 'SELECT `id_vodila`, CONCAT(`fio`, " - ", `phone`) as fio FROM `'._DB_PREFIX_.'fozzy_logistic_vodila` WHERE `id_shop` = '.$id_shop.' AND `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC';
        $sql_sborshik = 'SELECT `id_sborshik`, `fio` FROM `'._DB_PREFIX_.'fozzy_logistic_sborshik` WHERE `id_shop` = '.$id_shop.' AND `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC';
        $vodily = Db::getInstance()->ExecuteS($sql_vodila); 
        $sborshiky = Db::getInstance()->ExecuteS($sql_sborshik);

        $vodily_array = array();
        $sborshiky_array = array();
        foreach ($vodily as $vodila) {
          $vodily_array[$vodila['id_vodila']] = $vodila['fio'].' - '.$vodila['phone'];  
        }
        foreach ($sborshiky as $sborshik) {
          $sborshiky_array[$sborshik['id_sborshik']] = $sborshik['fio'];  
        }
        
        $sv = Db::getInstance()->executeS($sql_sv);
          
            if (count($sv) > 0)
              {
              $sborshik = $sv[0]['sborshik'];
              $vodila = $sv[0]['vodila'].' - '.$sv[0]['vodilaphone'];
              }
            else
              {
              $sborshik = null;
              $vodila = null;
              }

        $employee = $this->context->employee->id;
        $employee_group = $this->context->employee->id_profile;
        $modules_restrictions = Module::getModulesAccessesByIdProfile($employee_group);
        $this_module_restrictions = $modules_restrictions['FOZZY_LOGISTICS_CHANGE'];

        if ($this_module_restrictions['configure'] == 1) $can_change = 1;  
        else $can_change = 0;
        
        if ($employee_group == 1 || $employee == 10) 
          {
            $super_change = 1;
          } 
        else $super_change = 0; 
        
        Media::addJsDef(array(
            'id_cart' => $id_cart,
            'employee' => $employee
        ));

        $this->smarty->assign(array(
            'sborshik' => $sborshik,
            'vodila' => $vodila,
            'sborshiki' => $sborshiky,
            'vodily' => $vodily,
            'id_order' => (int)$params['id_order'],
            'id_cart' => $id_cart,
            'employee' => $employee,
            'can_change' => $can_change,
            'super_change' => $super_change
        ));
       return $this->display(__FILE__, 'orderDetailAdmin.tpl');
    }
}
