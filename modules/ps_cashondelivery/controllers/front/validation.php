<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class Ps_CashondeliveryValidationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function postProcess()
    {
        if ($this->context->cart->id_customer == 0 || $this->context->cart->id_address_delivery == 0 || $this->context->cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'ps_cashondelivery') {
                $authorized = true;
                break;
            }
        }
        if (!$authorized) {
            die(Tools::displayError('This payment method is not available.'));
        }
        $customer = new Customer($this->context->cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
        }
        $customer = new Customer((int)$this->context->cart->id_customer);
        
              
           //$last_email = substr($customer->email, -9);
           $email_cr = explode('@',$customer->email);
           $last_email = array_pop($email_cr);
           $shipping_summ = $this->context->cart->getTotalShippingCost();
           $cart_summ = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
           $courier_array = array();
           $courier_array = array(27,33,30,42,55);
           
          
           
            if ( ($last_email == 'fozzy.ua' || $last_email == 'temabit.com') && $shipping_summ > 0 && $cart_summ > 499 && in_array((int)$this->context->cart->id_carrier, $courier_array) ) 
            {
              $rule_name = array();
              $rule_name[1] = 'Бесплатная доставка для сотрудников';
              $rule_name[2] = 'Безкоштовна доставка для працівників';
              $cart_rule = new CartRule();
              if ($last_email == 'fozzy.ua') $cart_rule->code = 'Fozzy_'.$this->context->cart->id;
              if ($last_email == 'temabit.com') $cart_rule->code = 'Temabit_'.$this->context->cart->id;
          		$cart_rule->name = $rule_name;
          		$cart_rule->id_customer = (int)$this->context->cart->id_customer;
          		$cart_rule->free_shipping = true;
          		$cart_rule->quantity = 1;
          		$cart_rule->quantity_per_user = 1;
          		$cart_rule->minimum_amount_currency = (int)$this->context->cart->id_currency;
          		$cart_rule->reduction_currency = (int)$this->context->cart->id_currency;
          		$cart_rule->date_from = date('Y-m-d H:i:s', time());
          		$cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
          		$cart_rule->active = 1;
          		$cart_rule->add();

              $this->context->cart->addCartRule($cart_rule->id);
            }
             
        
        $total = $this->context->cart->getOrderTotal(true, Cart::BOTH);
        $this->module->validateOrder((int)$this->context->cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $this->module->displayName, null, array(), null, false, $customer->secure_key);
        Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->module->id.'&id_order='.(int)$this->module->currentOrder);
    }
}
