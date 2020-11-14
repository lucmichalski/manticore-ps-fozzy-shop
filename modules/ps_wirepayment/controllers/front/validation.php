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
class Ps_WirepaymentValidationModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$cart = $this->context->cart;
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'ps_wirepayment')
			{
				$authorized = true;
				break;
			}
		if (!$authorized)
			die($this->module->getTranslator()->trans('This payment method is not available.', array(), 'Modules.Wirepayment.Shop'));

    if ($cart->getTotalShippingCost() > 0) 
    
    {
    $rule_name = array();
    $rule_name[1] = 'Бесплатная доставка за безналичный платеж';
    $rule_name[2] = 'Безкоштовна доставка за безготівковий платіж';
    $cart_rule = new CartRule();
		$cart_rule->code = 'NVBNKTRNS_'.$cart->id;
		$cart_rule->name = $rule_name;
		$cart_rule->id_customer = $cart->id_customer;
		$cart_rule->free_shipping = true;
		$cart_rule->quantity = 1;
		$cart_rule->quantity_per_user = 1;
		$cart_rule->minimum_amount_currency = $cart->id_currency;
		$cart_rule->reduction_currency = $cart->id_currency;
		$cart_rule->date_from = date('Y-m-d H:i:s', time());
		$cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
		$cart_rule->active = 1;
		$cart_rule->add();
    
    $cart->addCartRule($cart_rule -> id);
    }

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		$mailVars = array(
			'{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),
			'{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
			'{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
		);

		$this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
		Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
	}
}
