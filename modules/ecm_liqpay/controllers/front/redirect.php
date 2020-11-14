<?php
class ecm_liqpayRedirectModuleFrontController extends ModuleFrontController
{
    public $display_header       = true;
    public $display_column_left  = true;
    public $display_column_right = true;
    public $display_footer       = true;
    public $ssl                  = true;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if ($id_cart = Tools::getValue('id_cart')) {
            $myCart = new Cart($id_cart);
            if (!Validate::isLoadedObject($myCart)) {
                $myCart = $this->context->cart;
            }

        } else {
            $myCart = $this->context->cart;
        }
        $id_customer = (int)$myCart->id_customer;
        $id_carrier = (int)$myCart->id_carrier;
        $id_shop = (int)$myCart->id_shop;
        if ($id_customer == 5)
        {
        //dump($myCart);
        //die();
        }
        $currency = new Currency($myCart->id_currency);
        if (Configuration::get('liqpay_delivery'))
        {
            if ($id_carrier == 50 || $id_carrier == 37)
            {
             $amount = ($myCart->getOrderTotal(true, Cart::ONLY_PRODUCTS))*1.1;
            }
            else
            {
              $amount = ($myCart->getOrderTotal(true, Cart::BOTH))*1.1;
            }
        } else
        {
            $amount = $myCart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        }

        if (Configuration::get('liqpay_wrapping')) {
            $amount += $myCart->getOrderTotal(true, Cart::ONLY_WRAPPING);
        }
        $amount   = number_format($amount, 2, '.', '');
        $currency = $currency->iso_code == 'RUR' ? 'RUB' : $currency->iso_code;
        $id_cart  = $myCart->id;
        $details  = $this->trans('Payment for cart № ', array(), 'Modules.Liqpay.Admin') . $id_cart;
        if ($postvalidate = Configuration::get('liqpay_postvalidate')) {
            $order_number = $myCart->id;
        } else {
            if (!($order_number = Order::getOrderByCartId($myCart->id))) {
                $this->module->validateOrder((int) $myCart->id, Configuration::get('EC_OS_WAITPAYMENT'), $amount, $this->module->displayName, null, array(), null, false, $myCart->secure_key);
                $order_number = $this->module->currentOrder;
                $details      = $this->module->l('Payment for order № ') . $order_number;
            }
        }
        $ssl_enable  = Configuration::get('PS_SSL_ENABLED');
        $base        = (($ssl_enable) ? 'https://' : 'http://');
     //   $server_url  = $base . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/ecm_liqpay/validation.php';
         if ($id_shop == 1) {
            $server_url  = $base . $_SERVER['HTTP_HOST'] . '/modules/ecm_liqpay/validation.php';
         }
         else {
            $server_url  = $base . $_SERVER['HTTP_HOST'] . '/modules/ecm_liqpay/validation.php'.'?id_shop='.$id_shop;
         }
     
        $success_url = $this->context->link->getModuleLink('ecm_liqpay', 'success', array('order_id' => $order_number), true);
        $action = (Configuration::get('liqpay_hold'))?'hold':'pay';
        $version     = '3';
        $language    = Configuration::get('PS_LANG_DEFAULT') == 'ru' ? 'en' : 'ru';
        
        //$amount = 1;
        
        $data        = base64_encode(
            json_encode(
                array('version' => $version,
                    'public_key'    => Tools::getValue('liqpay_id', $this->module->liqpay_merchant_id),
                    'amount'        => $amount,
                    'currency'      => $currency,
                    'description'   => $details,
                    'order_id'      => $order_number,
                    'action'        => $action,
                    'language'      => $language,
                    'server_url'    => $server_url,
                    'result_url'    => $success_url,
                )
            )
        );
        $signature = base64_encode(sha1($this->module->liqpay_merchant_pass . $data . $this->module->liqpay_merchant_pass, 1));
        $this->context->smarty->assign(compact('data', 'signature'));
        $this->setTemplate('module:ecm_liqpay/views/templates/front/redirect.tpl');
    }
}
