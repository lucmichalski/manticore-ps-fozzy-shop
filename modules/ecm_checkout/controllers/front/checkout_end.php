<?php
/**
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    Elcommerce <support@elcommece.com.ua>
* @copyright 2010-2018 Elcommerce
* @license   Comercial
* @category  PrestaShop
* @category  Module
*/

class Ecm_checkoutCheckout_endModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $order = new Order(Tools::GetValue('id_order'));
        $cart = new Cart($order->id_cart);
        $carrier   = new Carrier($order->id_carrier);
        $address = new Address($order->id_address_delivery);
        $customer = new Customer($order->id_customer);
        $current_payment = Db::getInstance()->getValue("SELECT `payment` FROM `"._DB_PREFIX_.$this->module->name."` WHERE `id_order`='{$order->id}' AND (`id_address`='{$address->id}' OR  `id_address_temp`='{$address->id}')");
        $employee = Db::getInstance()->getRow("SELECT * FROM `"._DB_PREFIX_."employee` WHERE `id_employee`=(SELECT `id_employee` FROM `"._DB_PREFIX_."customer` WHERE `id_customer`='{$order->id_customer}')");
        $middlename = Db::getInstance()->getValue("SELECT `middlename` FROM `"._DB_PREFIX_."address` WHERE `id_address`=".(int)$order->id_address_delivery);
        $this->context->smarty->assign(array(
			'order'  => $order,
			'cart'   => $cart,
			'carrier'   => $carrier,
		//	'customer'   => $customer,
			'address'   => $address,
			'current_payment' => $current_payment,
			'employee' => $employee,
			'middlename' => $middlename,
			'status' => true,
		));
        Media::addJsDef(array(
            'order'  => $order,
			'cart'   => $cart,
			'carrier'   => $carrier,
			'customer'   => $customer,
			'address'   => $address,
        ));
		$this->setTemplate("module:{$this->module->name}/views/templates/front/".Configuration::get($this->module->name.'_simple_layout')."/order-confirm.tpl");
        if($this->context->cookie->need_logout){
            $this->context->customer->logout();
			$this->context->cookie->__set('exist_customer', false);
			$this->context->cookie->__set('sc_auth', false);
        }
        
    }

   public function init()
    {
        parent::init();
        if (Configuration::get($this->module->name.'_hide_column_left')) $this->display_column_left = false;
        if (Configuration::get($this->module->name.'_hide_column_right')) $this->display_column_right = false;
    }
    
    public function setMedia()
    {
        parent::setMedia();
    }

    
}
