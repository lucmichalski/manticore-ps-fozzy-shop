<?php
/*
* We offer the best and most useful modules PrestаShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    Elcommerce <support@elcommece.com.ua>
* @copyright 2010-2019 Elcommerce TM
* @license   Comercial
* @category  PrestaShop
* @category  Module
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class ecm_liqpay extends PaymentModule
{
    private $_html       = '';
    private $_postErrors = array();

    public function __construct()
    {
        $this->name          = 'ecm_liqpay';
        $this->tab           = 'payments_gateways';
        $this->version       = '1.0.5';
        $this->author        = 'Elcommerce';
        $this->need_instance = 1;
        $this->bootstrap     = true;
        $this->controllers   = array('payment', 'validation');
        $this->currencies      = true;
        $this->currencies_mode = 'checkbox';
        $config                = Configuration::getMultiple(array('liqpay_id', 'liqpay_pass'));
        if (isset($config['liqpay_pass'])) {
            $this->liqpay_merchant_pass = $config['liqpay_pass'];
        }
        if (isset($config['liqpay_id'])) {
            $this->liqpay_merchant_id = $config['liqpay_id'];
        }
        parent::__construct();
        $this->displayName = $this->l('Liqpay');
        $this->description = $this->l('Payments with liqpay');
        if (!isset($this->liqpay_merchant_pass) or !isset($this->liqpay_merchant_id)) {
            $this->warning = $this->l('Your liqpay account must be set correctly (specify a password and a unique id merchant');
        }
        $this->ps_versions_compliancy = array('min'=> '1.7','max'=> '1.7.99.99');

    }

    public function install()
    {
        return (parent::install()
            && $this->registerHook('paymentOptions')
            && $this->registerHook('displayAdminOrder')
			&& $this->_addOS()
			&& $this->addTab('AdminLiqpayConfirmation','LiqpayConfirmation', -1)
			);
    }

    public function uninstall()
    {
        return $this->deleteTab('AdminLiqpayConfirmation') 
		     && parent::uninstall();
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitliqpay')) {
            $this->postValidation();
            if (!@count(@$this->post_errors)) {
                $this->postProcess();
            } else {
                foreach ($this->post_errors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }

        }
        $this->_html .= $this->renderForm();
        $this->_displayabout();
        return $this->_html;
    }

    public function renderForm()
    {
        $hold = Configuration::get('liqpay_hold');
        
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon'  => 'icon-cog',

            ),
            'input'  => array(
                array(
                    'type'  => 'text',
                    'label' => $this->l('Public key'),
                    'desc'  => $this->l('Public key in Liqpay'),
                    'name'  => 'liqpay_id',
                ),
                array(
                    'type'  => 'text',
                    'label' => $this->l('Private key'),
                    'desc'  => $this->l('Private key in Liqpay'),
                    'name'  => 'liqpay_pass',
                ),
               array(
                'type' => 'switch',
           //     'disabled' => ($hold)?true:false,
                'label' => $this->l('Order after payment'),
                'name' => 'liqpay_postvalidate',
                'desc' => $this->l('Create order after receive payment notification'),
                'values' => array(
                    array(
                        'id' => 'liqpay_postvalidate_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id' => 'liqpay_postvalidate_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                    )
                )
            ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->l('Order total with delivery cost'),
                    'name'   => 'liqpay_delivery',
                    'desc'   => $this->l('Send order total with delivery cost'),
                    'values' => array(
                        array(
                            'id'    => 'liqpay_delivery_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id'    => 'liqpay_delivery_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->l('Order total with gift wrapping cost'),
                    'name'   => 'liqpay_wrapping',
                    'desc'   => $this->l('Send order total with gift wrapping cost'),
                    'values' => array(
                        array(
                            'id'    => 'liqpay_wrapping_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id'    => 'liqpay_wrapping_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
            array(
                'type' => 'switch',
                'label' => $this->l('Hold money'),
                'name' => 'liqpay_hold',
                'desc' => $this->l('Money will be frozen on the payer\'s card until the store administrator confirms the order.'),
                'values' => array(
                    array(
                        'id' => 'liqpay_hold_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id' => 'liqpay_hold_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                    )
                )
            ),
        ),

            'submit' => array(
                'name'  => 'submitliqpay',
                'title' => $this->l('Save'),
            ),
        );
        $helper                           = new HelperForm();
        $helper->module                   = $this;
        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $lang                             = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language    = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier               = $this->identifier;
        $helper->submit_action            = 'submitliqpay';
        $helper->currentIndex             = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name .
        '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token    = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );
        return $helper->generateForm($this->fields_form);
    }

    public function getConfigFieldsValues()
    {
        $fields_values                        = array();
        $languages                            = Language::getLanguages(false);
        $fields_values['liqpay_id']           = Configuration::get('liqpay_id');
        $fields_values['liqpay_pass']         = Configuration::get('liqpay_pass');
        $fields_values['liqpay_postvalidate'] = Configuration::get('liqpay_postvalidate');
        $fields_values['liqpay_delivery']     = Configuration::get('liqpay_delivery');
        $fields_values['liqpay_wrapping']     = Configuration::get('liqpay_wrapping');
        $fields_values['liqpay_hold'] = Configuration::get('liqpay_hold');
        return $fields_values;
    }

    private function postValidation()
    {
        if (Tools::getValue('liqpay_id') && (!Validate::isString(Tools::getValue('liqpay_id')))) {
            $this->post_errors[] = $this->l('Invalid') . ' ' . $this->l('Public key');
        }

        if (Tools::getValue('liqpay_pass') && (!Validate::isString(Tools::getValue('liqpay_pass')))) {
            $this->post_errors[] = $this->l('Invalid') . ' ' . $this->l('Private key');
        }

    }

    private function postProcess()
    {
        Configuration::updateValue('liqpay_id', Tools::getValue('liqpay_id'));
        Configuration::updateValue('liqpay_pass', Tools::getValue('liqpay_pass'));
        $hold = Tools::getValue('liqpay_hold');
		Configuration::updateValue('liqpay_hold', $hold);
	//	$postvalidate  = ($hold)?0:Tools::getValue('liqpay_postvalidate');
    $postvalidate  = Tools::getValue('liqpay_postvalidate');
		Configuration::updateValue('liqpay_postvalidate', $postvalidate);
		Configuration::updateValue('liqpay_delivery', Tools::getValue('liqpay_delivery'));
        Configuration::updateValue('liqpay_wrapping', Tools::getValue('liqpay_wrapping'));
        $this->_html .= $this->displayConfirmation($this->l('Settings updated.'));
    }
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        
        //for test
        /*
        $cust = (int)$params['cart']->id_customer;
        if ($cust != 5) {
            return;
        }
        */
        
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $this->smarty->assign(array(
            'id_cart'       => $params['cart']->id,
            'this_path'     => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/',
        ));
        if ((int)$this->context->language->id == 2)
        {
          $text='module:ecm_liqpay/views/templates/hook/payment_ukr.tpl';
        }
        else
        {
          $text='module:ecm_liqpay/views/templates/hook/payment.tpl';
        }
        $newOption = new PaymentOption();
        $newOption->setCallToActionText($this->l('Pay with Liqpay'))
            ->setAction($this->context->link->getModuleLink($this->name, 'redirect', array(), true))
            ->setAdditionalInformation($this->fetch($text))
            ->setModuleName($this->name);

        $payment_options = [
            $newOption,
        ];

        return $payment_options;
    }

    public function checkCurrency($cart)
    {

        $currency_order    = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function validateAnsver($message)
    {
        Logger::addLog('liqpay: ' . $message);
        die($message);
    }

    public
	function deleteTab($name)
	{
		$idTab = Tab::getIdFromClassName($name);
        if($idTab){
			$tab = new Tab($idTab);
        $tab->delete();
        
		}
	return true;	
	}
	public
	function addTab($name, $public_name,$id_parent_tab)
	{
		$tab = new Tab();
		$tab->class_name = $name;
        $tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l($public_name);
        $tab->id_parent = $id_parent_tab;
        $tab->module = $this->name;
        $tab->add();
        return true;
	}

	private function _addOS()
	{
		$this->_addStatus('EC_OS_WAITPAYMENT', $this->l('Waiting payment'));
		$this->_addStatus('EC_OS_HOLDPAYMENT', $this->l('Payment success. Wait confirmation'));
		$this->_addStatus('EC_OS_HOLDCONFIRM', $this->l('Payment success'),'payment');
		return true;
	}
	private function _addStatus($setting_name, $name, $template=false)
	{
		$id_status = Configuration::get($setting_name);
		$status= new OrderState($id_status);
		$status->send_email = ($template?1:0);
		$status->invoice = ($template?1:0);
		$status->logable = ($template?1:0);
		$status->delivery = 0;
		$status->hidden = 0;
		if(!$id_status){
		$color          = sprintf( '#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255) );
		$status->color = $color;
	    }
		$lngs = Language::getLanguages();
		foreach ($lngs as $lng) {
			$status->name[$lng['id_lang']] =$name ;
			if($template)
				$status->template[$lng['id_lang']] =$template ;
		}
		if(!$id_status){
		if($status->add()){
			Configuration::updateValue($setting_name, $status->id);
			return true;
		}
		}else{
		 $status->update();
		   return true;	
			}
		return false;
	}
	public
	function hookdisplayAdminOrder($params) {

		$order = new Order($params['id_order']);
		if($order->module == $this->name || $order->current_state == Configuration::get('EC_OS_HOLDPAYMENT')){
		$context = Context::getContext();
        $err = ($context->cookie->redirect_errors)?$context->cookie->redirect_errors:'';
		$type = ($err)?'warning':'success';
		if(!$err) 
		$err = ($context->cookie->redirect_success)?$context->cookie->redirect_success:'';
  		
		$amount = (Module::isInstalled('ecm_novaposhta'))?$order->total_paid:$order->total_paid_real;
        $amount = number_format($amount, 2, '.', '');
		
		$this->context->smarty->assign(array(
			'ecmLiqpayhold_id_order' => $params['id_order'],
			'ecmLiqpayhold_paid' => $amount,
			'err' => $err,
			'type' => $type,
		));
		$context->cookie->__unset('redirect_errors');
		$context->cookie->__unset('redirect_success');
    if ($context->employee->id == 1 || $context->employee->id == 10 || $context->employee->id == 29)
    {
		return $this->display(__FILE__, 'displayAdminOrder.tpl');
		}
    //$this->setTemplate('module:ecm_liqpay/views/templates/front/redirect.tpl');
		}
	}
    private function _displayabout()
    {

        $this->_html .= '
		<div class="panel">
		<div class="panel-heading">
			<i class="icon-envelope"></i> ' . $this->l('Информация') . '
		</div>
		<div id="dev_div">
		<span><b>' . $this->l('Версия') . ':</b> ' . $this->version . '</span><br>
		<span><b>' . $this->l('Разработчик') . ':</b> <a class="link" href="mailto:support@elcommerce.com.ua" target="_blank">Savvato</a>
		<span><b>' . $this->l('Описание') . ':</b> <a class="link" href="http://elcommerce.com.ua" target="_blank">http://elcommerce.com.ua</a><br><br>
		<p style="text-align:center"><a href="http://elcommerce.com.ua/"><img src="http://elcommerce.com.ua/img/m/logo.png" alt="Электронный учет коммерческой деятельности" /></a>
		</div>
		</div>
		';
    }

}
