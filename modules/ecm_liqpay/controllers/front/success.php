<?php

class ecm_liqpaysuccessModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $ordernumber = Tools::getValue('order_id');
        $this->context->smarty->assign('ordernumber', $ordernumber);
        $postvalidate = Configuration::get('liqpay_postvalidate');
        if ($postvalidate == 1) {
            if (!$ordernumber) {
                ecm_liqpay::validateAnsver($this->module->l('Cart number is not set') . $ordernumber);
            }
            $cart = new Cart((int) $ordernumber);
            if (!Validate::isLoadedObject($cart)) {
                ecm_liqpay::validateAnsver($this->module->l('Cart does not exist'));
            }
            if (!($ordernumber = Order::getOrderByCartId($cart->id))) {
                $this->setTemplate('module:ecm_liqpay/views/templates/front/waitingPayment.tpl');
                return;
            }
        }
        if (!$ordernumber) {
            ecm_liqpay::validateAnsver($this->module->l('Order number is not set') . $ordernumber);
        }
        $order = new Order((int) $ordernumber);
        if (!Validate::isLoadedObject($order)) {
            ecm_liqpay::validateAnsver($this->module->l('Order does not exist') . $ordernumber);
        }
        $customer = new Customer((int) $order->id_customer);

        if ($customer->id != $this->context->cookie->id_customer) {
            ecm_liqpay::validateAnsver($this->module->l('You are not logged in'));
        }
        if ($order->hasBeenPaid() || $order->current_state == Configuration::get('EC_OS_HOLDPAYMENT')) {
            Tools::redirectLink(__PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . $order->id_cart .
                '&id_module=' . $this->module->id . '&id_order=' . $order->id);
        } else {
            $this->setTemplate('module:ecm_liqpay/views/templates/front/waitingPayment.tpl');
        }

    }
}
