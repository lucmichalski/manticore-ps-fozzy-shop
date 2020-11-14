<?php
/**
* We offer the best and most useful modules PrestаShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    Elcommerce <support@elcommece.com.ua>
* @copyright 2010-2018 Elcommerce
* @license   Comercial
* @category  PrestaShop
* @category  Module
*/

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;

if (!defined('_PS_VERSION_')) {
    exit;
}
if (!Configuration::get('ecm')) {
    Configuration::updateValue('ecm', '//0050f');
}

class Ecm_checkout extends Module
{
    protected $config_form = false;
    public $id_carrier;
    
    public function __construct()
    {
        $this->name = 'ecm_checkout';
        $this->tab = 'checkout';
        $this->version = '0.4.0';
        $this->author = 'Elcommerce';
        $this->need_instance = 1;
        $this->controller_name = 'checkout';
        $this->module_key = 'e1c38ursnmtgo6h0xdl27a4p95ifb_ ';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_active = (bool)Configuration::get($this->name.'_active');
        //if ($_SERVER['REMOTE_ADDR'] == '46.149.83.225') $this->module_active=false;
			
        $this->CartPresenter = new CartPresenter();

        $this->layout = array(
            array('id'  => 'avant', 'name'=> $this->l('Avant')),
            array('id'  => 's', 'name'=> $this->l('Standart')),
            array('id'  => 'w','name'=> $this->l('Warehouse')),
            array('id'  => 'bulb','name'=> $this->l('bulb')),
        );
        
        $this->flag = array(
            array('id' => 'NUMERIC',      'name' => $this->l('NUMERIC')),
            array('id' => 'NO_NUMERIC',   'name' => $this->l('NO_NUMERIC')),
            array('id' => 'RANDOM',       'name' => $this->l('RANDOM')),
            array('id' => 'ALPHANUMERIC', 'name' => $this->l('ALPHANUMERIC')),
        );

        $this->items = ["'optin'", "'newsletter'", "'phone'", "'phone_mobile'", "'company'", "'firstname'",      "'lastname'", "'middlename'", "'address1'", "'address2'", "'postcode'", "'city'", "'id_state'", "'id_country'", "'other'", "'vat_number'", "'dni'",
        ];
		
		$this->key_skip = ['id_cart','id_order','id_carrier','id_customer','id_address','id_address_temp','email','callme','alias','optin','newsletter','payment','middlename','password','password2'];
        
        $this->key_strong =['zone', 'tochka'];
		
		$this->qty_step = array(
            //'999999' => 5, //sample for product step
        );
        
        $this->yn = [
			['id'   => '1', 'value'=> true, 'label'=> $this->l('Yes'), ],
            ['id'   => '0', 'value'=> false, 'label'=> $this->l('No'), ],
        ];
		
		$suffix = '';
		$this->cookie_name = [
			'sc_address_delivery' => 'sc_address_delivery'.$suffix,
			'exist_customer' => 'exist_customer'.$suffix,
			'current_payment' => 'current_payment'.$suffix,
			'need_logout' => 'need_logout'.$suffix,
			'sc_carrier' => 'sc_carrier'.$suffix,
			'sc_customer' => 'sc_customer'.$suffix,
			'sc_country' => 'sc_country'.$suffix,
			'sc_end' => 'sc_end'.$suffix,
		];

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Simple checkout');
        $this->description = $this->l('Light weight checkout module in one step');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->sms_not_found = $this->l('Phone number not found! Please recovery your password via e-mail');
        $this->sms_invalid_code = $this->l('Invalid code.');
        $this->sms_attempts_left = $this->l('%d more attempts left');
        $this->sms_every_minute = $this->l('You can regenerate your password only every %d minute(s)');
        $this->sms_inactive_customer = $this->l('You cannot regenerate the password for this account.');
        $this->terms_text = $this->l('I agree to the [terms of service] and will adhere to them unconditionally.');
        $this->bad_password = $this->l('Password does not meet requirements.');
        
        $this->no_login_address = $this->l('SC no login address');
        $this->my_address = $this->l('My address');
        $this->sc_address = $this->l('SC address');

        require_once(_PS_MODULE_DIR_.$this->name.'/classes/sc.php');
		$this->payments = $this->GetPayments();
    }

    
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        @unlink(dirname(__FILE__).'/key.lic');

        return parent::install() &&
        $this->registerHook('displayOrderConfirmation') &&
        $this->registerHook('Header') &&
        $this->registerHook('displayHeader') &&
        $this->registerHook('displayFooter') &&
        $this->registerHook('displayAdminOrderContentShip') &&
        $this->registerHook('displayAdminOrderTabShip') &&
        $this->registerHook('actionValidateOrder') &&
        $this->registerHook('actionOrderStatusUpdate') &&
        $this->registerHook('displayOrderDetail') &&
        
        $this->registerHook('actionObjectCustomerAddAfter') &&
        $this->registerHook('actionCustomerAccountUpdate') &&
        
        $this->registerHook('displayCustomerAccountForm') &&
        $this->registerHook('DisplayBeforeBodyClosingTag') &&
        $this->registerHook('displayBackOfficeHeader') &&
        $this->registerHook('ActionAdminControllerSetMedia') &&
        
        //$this->registerHook('actionBeforeAuthentication') &&
        //$this->registerHook('actionBeforeSubmitAccount') &&
        
        $this->registerHook('actionAuthentication') &&
        $this->registerHook('ActionCustomerLogoutBefore')
        ;
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');
        return parent::uninstall();
    }

    /**
    * Load the configuration form
    */
    public function getContent()
    {
        /**
        * If values have been submitted in the form, process.
        */
        if (((bool)Tools::isSubmit('submitEcm_simplecheckoutModule')) == true) {
            $this->postProcess();
        }

        if (((bool)Tools::isSubmit('submit_customs')) == true) {
            @file_put_contents(_PS_MODULE_DIR_ . $this->name.'/views/css/custom.css', Tools::getValue('custom_css', true));
            @file_put_contents(_PS_MODULE_DIR_ . $this->name.'/views/js/custom.js', Tools::getValue('custom_js', true));
        }

        if (((bool)Tools::isSubmit('submitCustom_payment')) == true) {
            $this->postProcessCustom_payment();
        }

        if (((bool)Tools::isSubmit('submitCustom_carrier')) == true) {
            $this->postProcessCustom_carrier();
        }

        if (((bool)Tools::isSubmit('submit_cs')) == true) {
            //p(Tools::getValue('cs'));
            $cs = Tools::getValue('cs');
            foreach ($cs as $id_ref=>$c) {
                Configuration::updateValue($this->name.'_cs_'.$id_ref, json_encode($c));
            }
            //$this->postProcess();
        }

        if (!Configuration::get($this->name.'_lic_key')) {
            return $this->renderForm();
        }

        $secureKey       = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
        $key_changer_url = Tools::getHttpHost(true).__PS_BASE_URI__."modules/{$this->name}/sql/key_changer.php?secure_key=".$secureKey;
        $sc = new Sc();
        $this->context->smarty->assign(array(
                'key_changer_url'=> $key_changer_url,
                'lic_key'        => Configuration::get($this->name.'_lic_key')?Configuration::get($this->name.'_lic_key'):$this->l('Click here'),
                'version'        => $this->version,
                'carriers'       => Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS),
                'payments'       => $this->payments,
                'currentIndex'   => $this->context->link->getAdminLink('AdminModules', false)
                .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name,
                'token'          => Tools::getAdminTokenLite('AdminModules'),
                'items'          => $this->items,
                'cs'             => $this->GetCs(),
                'CustomPaymentForms' => $this->renderCustomPaymentForm(),
                'CustomCarrierForms' => $this->renderCustomCarrierForm(),
                'general' => $this->renderForm(),
                'custom_css' => @file_get_contents(_PS_MODULE_DIR_ . $this->name.'/views/css/custom.css'),
                'custom_js' => @file_get_contents(_PS_MODULE_DIR_ . $this->name.'/views/js/custom.js'),
            ));


        $output = '';
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
    * Create the form that will be displayed in the configuration of your module.
    */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEcm_simplecheckoutModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value'=> $this->getConfigFormValues(),/* Add values for your inputs */
            'languages'=> $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        if (!Configuration::get($this->name.'_lic_key')) {
            $helper->tpl_vars = array(
                'fields_value'=> $this->getConfigFirstValues(),
                'languages'   => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            return $helper->generateForm(array($this->getFirstForm()));
        }

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
    * Create the structure of your form.
    */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title'=> $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input'   => array(
                    array(
                        'type'    => 'hidden',
                        'name'    => $this->name.'_lic_key',
                        'label'   => $this->l('Licension key'),
                        'required'=> true,
                        'col'     => 3,
                        'desc'    => $this->l('Your licension key'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Module active'),
                        'name'   => $this->name.'_active',
                        'is_bool'=> true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Use middlename'),
                        'name'   => $this->name.'_middlename',
                        'is_bool'=> true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Skip standart cart'),
                        'name'   => $this->name.'_skip_cart',
                        'is_bool'=> true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type'   => 'select',
                        'label'  => $this->l('Authentication'),
                        'name'   => $this->name.'_auth',
                        'options' => array(
                            'query' => array(
                                array('id'  => 0,'name'=> $this->l('By email')),
                                array('id'  => 1,'name'=> $this->l('By phone')),
                                array('id'  => 2,'name'=> $this->l('Mixed(by phone or by email)')),
                            ),
                            'id'   => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Verify password'),
                        'name'   => $this->name.'_password2',
                        'is_bool'=> true,
                        'values' => $this->yn,
                   ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Quick order without login'),
                        'name'   => $this->name.'_quick_order',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('This will give the opportunity to place an order by entering only the email'),
                   ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Statuses'),
                        'name' => $this->name.'_statuses[]',
                        'selected'=> $this->name.'_statuses',
                        'options' => array(
                            'query' => $query = $this->getStatuses(),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'desc'    => $this->l('Select order statuses for delete fake addresses. (use Ctrl-click)'),
                        'size' => count($query),
                        'col' => 3,
                        'class' => 'fixed-width-xxl',
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Show radio'),
                        'name'   => $this->name.'_show_radio',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Show radio-buttons for carrier and payment selection'),
                   ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Show logo'),
                        'name'   => $this->name.'_show_logo',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Show logo icons for carrier and payment selection'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Payment simple name'),
                        'name'   => $this->name.'_simple_name_pay',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Show only name for payment selection'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Simple name'),
                        'name'   => $this->name.'_simple_name',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Show only name for carrier selection'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Show price'),
                        'name'   => $this->name.'_show_price',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Show price for carrier selection'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Hide discount block'),
                        'name'   => $this->name.'_hide_discount',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Hide discount block for checkout page'),
                    ),
                     array(
                        'type'   => 'switch',
                        'label'  => $this->l('Hide header'),
                        'name'   => $this->name.'_hide_header',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Hide header site for checkout page'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Hide column left'),
                        'name'   => $this->name.'_hide_column_left',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Hide left column site for checkout page'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Hide column right'),
                        'name'   => $this->name.'_hide_column_right',
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Hide right column site for checkout page'),
                    ),
                    array(
                        'type'   => 'select',
                        'label'  => $this->l('Layout design'),
                        'name'   => $this->name.'_simple_layout',
                        'options' => array(
                            'query' => $this->layout,
                            'id'   => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type'   => 'select',
                        'label'  => $this->l('Default payment'),
                        'name'   => $this->name.'_payment',
                        'options' => array(
                            'query'=> $this->payments,
                            'id'   => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type'   => 'text',
                        'col' => 3,
                        'label'  => $this->l('Phone mask'),
                        'name'   => $this->name.'_phone_mask',
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Generate password'),
						'hint' => $this->l('Automatic generated password for full registration'),
                        'name'   => $this->name.'_password_generate',
                        'is_bool'=> true,
                        'values' => $this->yn,
                    ),
                     array(
                        'type'   => 'select',
                        'label'  => $this->l('Password type'),
                        'name'   => $this->name.'_password_type',
                        'hint' => $this->l('Setting for automatic generated password'),
                        'options' => array(
                            'query' => $this->flag,
                            'id'   => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type'   => 'html',
						'col' => 3,
						'label'  => $this->l('Password length'),
                        'name'   => $this->name.'_password_length',
                        'desc'    => sprintf($this->l('Minimal length %d chars'), Validate::PASSWORD_LENGTH),
                        'html_content' => '<input min="'.Validate::PASSWORD_LENGTH.'" max="10" type="number" 
							name="'.$this->name.'_password_length" class="bootstrap"
							value="'.Configuration::get($this->name.'_password_length').'">'
                    ),
                ),
                'submit' => array(
                    'title'=> $this->l('Save'),
                ),
            ),
        );
    }

    public function getCs()
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $cs = array();
        foreach ($carriers as $carrier) {
            $cs[$carrier['id_reference']] = json_decode(Configuration::get($this->name.'_cs_'.$carrier['id_reference']), true);
        }
        return $cs;
    }

    protected function renderCustomCarrierForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCustom_carrier';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value'=> $this->getConfigCarrierValues(),/* Add values for your inputs */
            'languages'=> $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        return $helper->generateForm($this->getCustomCarrierForm());
    }
    
    protected function renderCustomPaymentForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCustom_payment';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $fields_value = $this->getConfigPaymentValues();
        $helper->tpl_vars = array(
            'fields_value'=> $fields_value,
            'languages'=> $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        return $helper->generateForm($this->getCustomPaymentForm($fields_value));
    }
    
    protected function getCustomPaymentForm($fields_value)
    {
        $fields_form = array();
        
        foreach ($this->payments as $key=>$payment) {
            $fields_form[]['form'] =
            array(
                'tinymce' => true,
                'legend' => array(
                    'title'=> $this->l($payment['name']),
                    'icon' => 'icon-cogs',
                ),
                'input'   => array(
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Custom order information page'),
                        'name'   => 'sc_end_'.$payment['id'],
                        'is_bool'=> true,
                        'values' => $this->yn,
                        'hint' => $this->l('Use custom order information page  for this payment metod.'),
                    ),
                    array(
                        'type'   => 'html',
                        'col' => 3,
                        'label'  => $this->l('From'),
                        'name'   => 'from_'.$payment['id'],
                        'html_content' => '<input type="number" 
                            name="'.'from_'.$payment['id'].'" class="bootstrap"
                            value="'.$fields_value['from_'.$payment['id']].'">'
                    ),
                    array(
                        'type'   => 'html',
                        'col' => 3,
                        'label'  => $this->l('To'),
                        'name'   => 'to_'.$payment['id'],
                        'html_content' => '<input type="number" 
                            name="'.'to_'.$payment['id'].'" class="bootstrap"
                            value="'.$fields_value['to_'.$payment['id']].'">'
                    ),
                    array(
                        'type'    => 'text',
                        'name'    => 'payment_name_'.$payment['id'],
                        'label'   => $this->l('Custom name'),
                        'lang' => true,
                    ),
                    array(
                        'type'    => 'textarea',
                        'name'    => 'payment_desc_'.$payment['id'],
                        'label'   => $this->l('Custom description'),
                        'lang' => true,
                        'autoload_rte' => true,
                        'cols' => 30,
                        'rows' => 4,
                    ),
                ),
                'submit' => array(
                    'title'=> $this->l('Save'),
                ),
           );
        }
        return $fields_form;
    }

    protected function getCustomCarrierForm()
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $fields_form = array();
        
        foreach ($carriers as $key=>$carrier) {
            $fields_form[]['form'] =
            array(
                'tinymce' => true,
                'legend' => array(
                    'title'=> $this->l($carrier['name']),
                    'icon' => 'icon-cogs',
                ),
                'input'   => array(
                     array(
                        'type'    => 'text',
                        'name'    => 'carrier_info_'.$carrier['id_reference'],
                        'label'   => $this->l('Custom name'),
                        'lang' => true,
                        'hint' => $this->l('This value use for replace cost delivery for this carrier'),
                    ),
                    array(
                        'type'    => 'textarea',
                        'name'    => 'carrier_desc_'.$carrier['id_reference'],
                        'label'   => $this->l('Custom description'),
                        'lang' => true,
                        'autoload_rte' => true,
                        'cols' => 30,
                        'rows' => 4,
                    ),
                     array(
                        'type'    => 'text',
                        'name'    => 'carrier_name_'.$carrier['id_reference'],
                        'label'   => $this->l('Replace at'),
                        'lang' => true,
                        'hint' => $this->l('This value use for replace cost delivery for this carrier'),
                    ),
                ),
                'submit' => array(
                    'title'=> $this->l('Save'),
                ),
           );
        }
        return $fields_form;
    }

    protected function getConfigPaymentValues()
    {
        $fields_value = $customization = array();
        $langs = $this->context->controller->getLanguages();
        foreach ($this->payments as $payment) {
            $customization = array();
            $sql    = "SELECT * FROM `"._DB_PREFIX_.$this->name."_custom` WHERE `type` = 'payment' AND `ref` ='{$payment['id']}'";
            $result = Db::getInstance()->ExecuteS($sql);
            foreach ($result as $res) {
                $customization[$res['id_lang']] = $res;
            }
            $names = $desqs = array();
            foreach ($langs as $lang) {
                $names[$lang['id_lang']] = isset($customization[$lang['id_lang']]['name'])?$customization[$lang['id_lang']]['name']:'';
                $descs[$lang['id_lang']] = isset($customization[$lang['id_lang']]['description'])?$customization[$lang['id_lang']]['description']:'';
            }
            $fields_value ['payment_name_'.$payment['id']] = $names;
            $fields_value ['payment_desc_'.$payment['id']] = $descs;
            $fields_value ['sc_end_'.$payment['id']] = @$customization[@$lang['id_lang']]['sc_end'];
            $fields_value ['from_'.$payment['id']] = @$customization[@$lang['id_lang']]['from'];
            $fields_value ['to_'.$payment['id']] = @$customization[@$lang['id_lang']]['to'];
        }
        return $fields_value;
    }

    protected function getConfigCarrierValues()
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $fields_value = $customization = array();
        $langs = $this->context->controller->getLanguages();
        foreach ($carriers as $carrier) {
            $customization = array();
            $sql    = "SELECT * FROM `"._DB_PREFIX_.$this->name."_custom` WHERE `type` = 'carrier' AND `ref` ='{$carrier['id_reference']}'";
            $result = Db::getInstance()->ExecuteS($sql);
            foreach ($result as $res) {
                $customization[$res['id_lang']] = $res;
            }
            $names = $desqs = array();
            foreach ($langs as $lang) {
                $names[$lang['id_lang']] = isset($customization[$lang['id_lang']]['name'])?$customization[$lang['id_lang']]['name']:'';
                $descs[$lang['id_lang']] = isset($customization[$lang['id_lang']]['description'])?$customization[$lang['id_lang']]['description']:'';
                $info[$lang['id_lang']] = isset($customization[$lang['id_lang']]['info'])?$customization[$lang['id_lang']]['info']:'';
            }
            $fields_value ['carrier_name_'.$carrier['id_reference']] = $names;
            $fields_value ['carrier_desc_'.$carrier['id_reference']] = $descs;
            $fields_value ['carrier_info_'.$carrier['id_reference']] = $info;
        }
        return $fields_value;
    }

    protected function getFirstForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title'=> $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input'   => array(
                    array(
                        'type'    => 'text',
                        'name'    => $this->name.'_lic_key',
                        'label'   => $this->l('Licension key'),
                        'required'=> true,
                        'col'     => 3,
                        'desc'    => $this->l('Your licension key'),
                    ),
                    array(
                        'type'   => 'switch',
                        'label'  => $this->l('Module active'),
                        'name'   => $this->name.'_active',
                        'is_bool'=> true,
                        'values'   => $this->yn,
                    ),
                ),
                'submit' => array(
                    'title'=> $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFirstValues()
    {
        return array(
            $this->name.'_lic_key'=> Configuration::get($this->name.'_lic_key'),
            $this->name.'_active' => Configuration::get($this->name.'_active'),
        );
    }

    protected function getConfigFormValues()
    {
        return array_merge($this->getConfigFirstValues(), array(
                //$this->name.'_payments[]'       => explode(',', Configuration::get($this->name.'_payments')),
                $this->name.'_statuses[]'       => explode(',', Configuration::get($this->name.'_statuses')),
                $this->name.'_show_radio'       => Configuration::get($this->name.'_show_radio'),
                $this->name.'_show_logo'        => Configuration::get($this->name.'_show_logo'),
                $this->name.'_simple_name_pay'  => Configuration::get($this->name.'_simple_name_pay'),
                $this->name.'_simple_name'      => Configuration::get($this->name.'_simple_name'),
                $this->name.'_show_price'       => Configuration::get($this->name.'_show_price'),
                $this->name.'_hide_header'      => Configuration::get($this->name.'_hide_header'),
                $this->name.'_hide_discount'    => Configuration::get($this->name.'_hide_discount'),
                $this->name.'_hide_column_left' => Configuration::get($this->name.'_hide_column_left'),
                $this->name.'_hide_column_right'=> Configuration::get($this->name.'_hide_column_right'),
                $this->name.'_simple_layout'    => Configuration::get($this->name.'_simple_layout'),
                $this->name.'_payment'          => Configuration::get($this->name.'_payment'),
                $this->name.'_phone_mask'       => Configuration::get($this->name.'_phone_mask'),
                $this->name.'_middlename'       => Configuration::get($this->name.'_middlename'),
                $this->name.'_skip_cart'        => Configuration::get($this->name.'_skip_cart'),
                $this->name.'_auth'             => Configuration::get($this->name.'_auth'),
                $this->name.'_quick_order'      => Configuration::get($this->name.'_quick_order'),
                $this->name.'_callme_status'    => Configuration::get($this->name.'_callme_status'),
                $this->name.'_password_generate'=> Configuration::get($this->name.'_password_generate'),
                $this->name.'_password_type'    => Configuration::get($this->name.'_password_type'),
                $this->name.'_password_length'  => Configuration::get($this->name.'_password_length'),
                $this->name.'_password2'        => Configuration::get($this->name.'_password2'),
            ));
    }

    protected function getStatuses()
    {
        $statuses = array();
        //$statuses[] = array('id' =>0, 'name' => $this->l('Do not add'));
        $list = OrderState::getOrderStates($this->context->language->id);
        foreach ($list as $status) {
            if (!$status['deleted'] or !$status['hidden']) {
                $statuses[] = array('id' =>$status['id_order_state'], 'name' => $status['name']);
            }
        }
        return $statuses;
    }

    protected function postProcess()
    {
        if (!Configuration::get($this->name.'_lic_key')) {
            $form_values = $this->getConfigFirstValues();
        } else {
            $form_values = $this->getConfigFormValues();
        }
        foreach (array_keys($form_values) as $key) {
            if (stripos($key, '[]')) {
                $key   = Tools::substr($key, 0, - 2);
                $value = implode(',', Tools::getValue($key));
            } else {
                $value = Tools::getValue($key);
            }
            Configuration::updateValue($key, $value);
        }
    }

    protected function postProcessCustom_payment()
    {
        $langs = $this->context->controller->getLanguages();
        foreach ($this->payments as $payment) {
            foreach ($langs as $lang) {
                $sql = "INSERT INTO `"._DB_PREFIX_.$this->name."_custom` (`id_lang`,`ref`,`type`) VALUES ('{$lang['id_lang']}','{$payment['id']}', 'payment') ON DUPLICATE KEY UPDATE id_lang = VALUES(id_lang), ref = VALUES(ref), type = VALUES(type) ";
                Db::getInstance()->Execute($sql);
                $sql = "UPDATE `"._DB_PREFIX_.$this->name."_custom` SET 
					`name` ='".pSql(Tools::getValue('payment_name_'.$payment['id'].'_'.$lang['id_lang']))."', 
					`description` = '".pSql(Tools::getValue('payment_desc_'.$payment['id'].'_'.$lang['id_lang']), true)."',
					`sc_end` = '".Tools::getValue('sc_end_'.$payment['id'])."',
					`from` = '".Tools::getValue('from_'.$payment['id'])."',
					`to` = '".Tools::getValue('to_'.$payment['id'])."'
					WHERE `id_lang` = '{$lang['id_lang']}' AND `ref` = '{$payment['id']}' AND  `type` = 'payment' ";
                Db::getInstance()->Execute($sql);
            }
        }
    }

    protected function postProcessCustom_carrier()
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $langs = $this->context->controller->getLanguages();
        foreach ($carriers as $carrier) {
            foreach ($langs as $lang) {
                $sql = "INSERT INTO `"._DB_PREFIX_.$this->name."_custom` (`id_lang`,`ref`,`type`) VALUES ('{$lang['id_lang']}','{$carrier['id_reference']}', 'carrier') ON DUPLICATE KEY UPDATE id_lang = VALUES(id_lang), ref = VALUES(ref), type = VALUES(type) ";
                Db::getInstance()->Execute($sql);
                $sql = "UPDATE `"._DB_PREFIX_.$this->name."_custom` SET 
					`name` = '".pSql(Tools::getValue('carrier_name_'.$carrier['id_reference'].'_'.$lang['id_lang']), true)."', 
					`info` = '".pSql(Tools::getValue('carrier_info_'.$carrier['id_reference'].'_'.$lang['id_lang']), true)."', 
					`description` = '".pSql(Tools::getValue('carrier_desc_'.$carrier['id_reference'].'_'.$lang['id_lang']), true)."' 
					WHERE `id_lang` = '{$lang['id_lang']}' AND `ref` = '{$carrier['id_reference']}' AND  `type` = 'carrier' ";
                Db::getInstance()->Execute($sql);
            }
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be added on the FO.
    */
    public function hookHeader($params)
    {
    }

    public function hookDisplayFooter()
    {
        if ($this->module_active && Module::isInstalled('ecm_smssender')  && Module::isEnabled('ecm_smssender')
            && $this->context->smarty->tpl_vars['page']->value['page_name'] == 'password') {
            $url_string = parse_url(__PS_BASE_URI__ . $_SERVER['REQUEST_URI']);
            $url_params = array();
            if (isset($url_string['query'])) {
                parse_str($url_string['query'], $url_params);
                if (isset($url_params['isPaymentStep'])) {
                    unset($url_params['isPaymentStep']);
                }
            }
            $this->context->smarty->assign(array(
                'ajaxSms'    => $this->context->link->getModuleLink($this->name, $this->controller_name.'_sms', $url_params, (bool) Configuration::get('PS_SSL_ENABLED')),
                'authMethod' => Configuration::get($this->name.'_auth'),
            ));
            return $this->display(__FILE__, 'sms.tpl');
        }
    }
    
    public function hookDisplayHeader()
    {
        if($_SERVER['REMOTE_ADDR'] == '46.149.83.225'){
		//dump($this->context->controller);
        //dump($this->context->smarty->tpl_vars['page']->value['page_name']);  
		}

        if ($this->module_active) {

			if(!$this->context->customer->isLogged() && $this->context->customer->logged){
				$this->context->customer->logout();
			}


            $page = $this->context->smarty->tpl_vars['page']->value['page_name'];
            $logout_pages = array('identity','addresses','history','order-follow','order-slip','discount');
            if (in_array($page, $logout_pages)) {
                $sc = new Sc();
                if ($sc->tryScLogOut()) {
                    Tools::redirect('index.php?controller=authentication');
                }
            }
            
            $authMethod = Configuration::get($this->name.'_auth');
            if ($page == 'authentication' or $page == 'password') {
                //$this->context->controller->addJS($this->_path.'/views/js/auth.js');
                $this->context->controller->addJquery();
                
				if(Configuration::get($this->name.'_phone_mask')) {
					//$this->context->controller->addJS(($this->_path).'views/js/jquery.mask.js', 'all');
					//$this->context->controller->addJS(($this->_path).'views/js/z_custom_mask_UA.js', 'all');
				}
				
				
                $this->context->controller->addJS(($this->_path).'/views/js/simcheck_sms.js');
                $this->context->smarty->assign('authMethod', $authMethod);
            }
            if ($page == 'identity') {
                $this->context->controller->addJqueryPlugin(array('growl'));
		        $this->context->controller->addJS(($this->_path).'views/js/updateCustomer.js');
                if(Configuration::get($this->name.'_phone_mask')) {
					//$this->context->controller->addJS(($this->_path).'views/js/jquery.mask.js', 'all');
					//$this->context->controller->addJS(($this->_path).'views/js/z_custom_mask_UA.js', 'all');

				}
            }
            if ($page == 'addresses') {
                $this->context->controller->addJS($this->_path.'/views/js/addresses.js');
            }
            if ($page == 'order-confirmation') {
                $this->tryLogOut();
                return;
            }
            
            $catch_pages = array('order-opc','orderopc','order','checkout');
			if (Configuration::get($this->name.'_skip_cart')) $catch_pages = array_merge($catch_pages, ['cart']);
            if (in_array($page, $catch_pages)) {
				if ($page=='cart' and Tools::GetIsset('action') and Tools::getValue('action')!='show') return;
                $url_string = parse_url(__PS_BASE_URI__ . $_SERVER['REQUEST_URI']);
                $url_params = array();
                if (isset($url_string['query'])) {
                    parse_str($url_string['query'], $url_params);
                    if (isset($url_params['isPaymentStep'])) {
                        unset($url_params['isPaymentStep']);
                    }
                    if (isset($url_params['action'])) {
                        unset($url_params['action']);
                    }
                }
                //$sc = new Sc();
                //$sc->initCart();
				Tools::redirect($this->context->link->getModuleLink($this->name, $this->controller_name, $url_params, (bool) Configuration::get('PS_SSL_ENABLED')));
            }
        }
    }

    public function hookactionValidateOrder($params)
    {
        Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_.$this->name."` SET `id_order` = '{$params['order']->id}', `password` = '', `password2` = '', `payment`='{$this->context->cookie->__get($this->cookie_name['current_payment'])}' WHERE `id_cart`='{$this->context->cart->id}' AND `id_country`='{$this->context->country->id}'");
        $callme = Db::getInstance()->GetValue("SELECT `callme` FROM `"._DB_PREFIX_.$this->name."` WHERE `id_cart`='{$this->context->cart->id}' AND `id_country`='{$this->context->country->id}'");
        $params['order']->recyclable = $callme;
        $params['order']->update();
        //Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."orders` SET `recyclable` = '{$callme}' WHERE `id_order`='{$params['order']->id}'");
        $this->context->cookie->__set('id_order', $params['order']->id);
    }

    public function hookactionOrderStatusUpdate($params)
    {
        if (in_array($params['newOrderStatus']->id, explode(',', Configuration::get($this->name.'_statuses')))) {
            $order = new Order($params['id_order']);
            //Db::getInstance()->delete('address', "id_address ='{$order->id_address_delivery}' AND alias LIKE '%{$this->no_login_address}%'");
        }
        return true;
    }

    public function hookActionCarrierProcess()
    {
        /* Place your code here. */
    }

    public function hookActionCarrierUpdate()
    {
        /* Place your code here. */
    }

    public function hookActionCartSave()
    {
        /* Place your code here. */
    }

    public function hookActionPaymentConfirmation()
    {
        /* Place your code here. */
    }

    public function hookDisplayOrderConfirmation($params)
    {
    }

    public function hookDisplayOrderDetail()
    {
        //$this->tryLogOut();
    }
    
    public function hookActionAdminControllerSetMedia()
    {
        //$this->context->controller->addJquery();
        if(Configuration::get($this->name.'_phone_mask')) {
            //$this->context->controller->addJS(($this->_path).'views/js/jquery.mask.js', 'all');
            //$this->context->controller->addJS(($this->_path).'views/js/z_custom_mask_UA.js', 'all');
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        //dump($this->context->controller);
        $this->context->controller->addCSS($this->_path.'/views/css/back.css');
         
        if ($this->context->controller->controller_name == 'AdminCustomers') {
        
            $id_customer = Tools::getValue("id_customer");
            if(!$id_customer) $id_customer = (int)$this->context->customer->id;
            if ($id_customer && $id_customer != 0 ) {
                $this->context->controller->addJquery();
                $this->context->controller->addJS(($this->_path).'views/js/admincustomer.js');
                $this->context->controller->addJS(($this->_path).'views/js/updateCustomer.js');
                    
                $useMiddlename = Configuration::get($this->name.'_middlename');
                $authMethod = Configuration::get($this->name.'_auth');
                $result = Db::getInstance()->GetRow("SELECT `phone`, `middlename` FROM `"._DB_PREFIX_."customer` WHERE `id_customer` = '{$id_customer}'");
            //    dump($result);
            //    die();
                $this->context->smarty->assign([
                    'phone'      => $result['phone'],
                    'middlename' => $result['middlename'],
                    'id_customer'=> $id_customer,
                    'ajaxUrl'    => $this->_path.'classes/ajaxUpdateCustomer.php',
                    'useMiddlename'    => $useMiddlename,
                    'authMethod'    => $authMethod,
                    'phone_mask' => Configuration::get($this->name.'_phone_mask'),
                ]);
                return $this->display(__FILE__, '/views/templates/hook/displayAdminCustomers.tpl');
            }
        }
    }
    
    
    public function hookDisplayCustomerAccountForm($params)
    {
        global $cookie;
        
        $useMiddlename = Configuration::get($this->name.'_middlename');
        $authMethod = Configuration::get($this->name.'_auth');
        $sql    = "SELECT `phone`, `middlename` FROM `"._DB_PREFIX_."customer` WHERE `id_customer` = '".$cookie->id_customer."'";
        $result = Db::getInstance()->GetRow($sql);
        $this->context->smarty->assign(array(
                'useMiddlename' => $useMiddlename,
                'middlename' => $result['middlename'],
                'phone'      => $result['phone'],
                'authMethod' => $authMethod,
            ));
        return $this->display(__FILE__, 'displayIdentityForm.tpl');
    }
    
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        global $cookie;
        $this->context->smarty->assign([
            'ajaxUrl'    => $this->_path.'classes/ajaxUpdateCustomer.php',
            'useMiddlename' => Configuration::get($this->name.'_middlename'),
            'authMethod' => Configuration::get($this->name.'_auth'),
            'id_customer'=> $cookie->id_customer,
        ]);
        return $this->display(__FILE__, 'js_footer.tpl');
    }
    
    public function hookDisplayCustomerAccount($params)
    {
    }
    
    public function hookactionObjectCustomerAddAfter($params)
    {
        $middlename = trim(Tools::getValue('middlename'));
        $phone      = $this->phoneClear(trim(Tools::getValue('phone')));
        $to_upd = array();
        if ($middlename) {
            $to_upd['middlename'] = $middlename;
        }
        if ($phone) {
            $to_upd['phone'] = $phone;
        }
        if (count($to_upd)) {
            Db::getInstance()->update('customer', $to_upd, 'id_customer =' .(int)$params['object']->id);
        }
    }

    public function hookactionCustomerAccountUpdate($params)
    {
        $customer = $params['customer'];
        $middlename = trim(Tools::getValue('middlename'));
        $phone      = $this->phoneClear(trim(Tools::getValue('phone')));
        Db::getInstance()->update('customer', array('middlename' => $middlename, 'phone' => $phone), 'id_customer =' .(int)$customer->id);
    }

    public function hookactionAuthentication($params)
    {
        Db::getInstance()->update('cart_product', array('id_address_delivery'=> $params['cart']->id_address_delivery,), "id_cart ='{$params['cart']->id}'");
        Db::getInstance()->delete($this->name, "id_cart ='{$params['cart']->id}'");
        $this->context->cookie->__set($this->cookie_name['need_logout'], false);
        $this->context->cookie->__set($this->cookie_name['exist_customer'], false);
        $this->context->cookie->__set($this->cookie_name['sc_customer'], 'old');
        $this->context->cookie->__set($this->cookie_name['sc_address_delivery'], $params['cart']->id_address_delivery);
    }
    
    public function hookActionBeforeAuthentication($params)
    {
        if (Validate::isPhoneNumber($_POST['email'])) {
            $email = Db::getInstance()->getValue("SELECT `email` FROM `"._DB_PREFIX_."customer` WHERE `phone` = '" . trim($_POST['email'])."'");
            $_POST['email'] = $email;
            $_GET['email'] = $email;
            //dump(tools::getValue('email'));
        }
    }
    
    public function hookActionCustomerLogoutBefore($params)
    {
        $this->context->cookie->__set($this->cookie_name['sc_customer'], 'new');
        $this->context->cookie->__unset($this->cookie_name['need_logout']);
        $this->context->cookie->__unset($this->cookie_name['exist_customer']);
        $this->context->cookie->__unset($this->cookie_name['sc_customer']);
        $this->context->cookie->__unset($this->cookie_name['sc_address_delivery']);
    }
    
    public function hookActionBeforeSubmitAccount()
    {
        return;
        $phone = $this->phoneClear(Tools::getValue('phone'));
        $email = trim(Tools::getValue('email'));
        $authMethod = Configuration::get($this->name.'_auth');
        if ($authMethod == 1 && $email) {
            $_POST['email'] = null;
        }
        if ($authMethod >= 1 && ((!$email && $phone)||(Validate::isPhoneNumber($email) && $phone))) {
            $shop = new ShopUrl($this->context->shop->getContextShopID());
            $_POST['email'] = $phone.'@'.$shop->domain;
            $id_customer = Db::getInstance()->getValue("SELECT `id_customer` FROM `"._DB_PREFIX_."customer` WHERE `phone` = '" . $phone."'");
            if ($id_customer) {
                $this->context->controller->errors[]=Tools::displayError('An account using this phone address has already been registered.', false);
            }
        }
    }
    
    public function SendToCustomer($customer, $by_email, $password)
    {
        
		//$logger = new FileLogger(0); //0 == debug level, logDebug() won’t work without this.
        //$logger->setFilename(dirname(__FILE__)."/log.txt");

        $phone = Db::getInstance()->getValue("SELECT `phone` FROM `"._DB_PREFIX_."customer` WHERE `id_customer`=".(int)$customer->id);
        //$logger->logDebug('');
        //$logger->logDebug('phone:'.$phone.' password:'.$password);
		
		
		
		//p(Configuration::get('PS_CUSTOMER_CREATION_EMAIL'));
        //p($by_email);
        if (Configuration::get('PS_CUSTOMER_CREATION_EMAIL') && $by_email && !$customer->is_guest) {
            Mail::Send(
                $customer->id_lang,
                'account',
                Mail::l('Welcome!'),
                array(
					'{firstname}'=> $customer->firstname,
					'{lastname}' => $customer->lastname,
					'{email}'    => $customer->email,
					'{passwd}'   => $password,
					),
                $customer->email,
                $customer->firstname.' '.$customer->lastname
            );
        } elseif (!$by_email) {
            if ($phone && Module::isInstalled('ecm_smssender') && Module::isEnabled('ecm_smssender') and !$customer->is_guest) {
                include(_PS_MODULE_DIR_ . 'ecm_smssender/classes/turbosms.php');
                include(_PS_MODULE_DIR_ . 'ecm_smssender/classes/message.php');
                $login = Configuration::get('ECM_SMSSENDER_ACCOUNT');
                $pwd = Configuration::get('ECM_SMSSENDER_ACCOUNT_PASSWORD');
                $sender = Configuration::get('ECM_SMSSENDER_ACCOUNT_ALFA');
                $smssender = new Client($login, $pwd, $sender);
                $message = MessageSMS::getPWDData($customer->firstname.' '.$customer->lastname, $password, $customer->id_lang);
                $smssender->send($phone, $message);
            }
        }
    }
    
    public function tryLogOut()
    {
        $url_string = parse_url(__PS_BASE_URI__ . $_SERVER['REQUEST_URI']);
        $url_params = array();
        if (isset($url_string['query'])) {
            parse_str($url_string['query'], $url_params);
        }
        if ($this->context->cookie->__get($this->cookie_name['need_logout']) and $this->context->cookie->__get($this->cookie_name['exist_customer'])) {
            $exist_customer = $this->context->cookie->__get($this->cookie_name['exist_customer']);
            $this->context->customer->logout();
            $this->context->cookie->__set($this->cookie_name['need_logout'], false);
            $this->context->cookie->__set($this->cookie_name['exist_customer'], false);
            if ($exist_customer) {
                Tools::redirect($this->context->link->getModuleLink($this->name, $this->controller_name.'_end', $url_params, (bool) Configuration::get('PS_SSL_ENABLED')));
            }
        } elseif ($this->context->cookie->__get($this->cookie_name['sc_end'])) {
            Tools::redirect($this->context->link->getModuleLink($this->name, $this->controller_name.'_end', $url_params, (bool) Configuration::get('PS_SSL_ENABLED')));
        }
    }
    
    public function phoneClear($phone)
    {
        return preg_replace("/\D/", "", $phone);
        return $phone;
    }
    
    public function CorrectPhone()
    {
    }
    
    public function saveModuleSettings()
    {
        $sql    = "SELECT * FROM `"._DB_PREFIX_."configuration` WHERE `name` LIKE '%{$this->name}%'";
        $result = Db::getInstance()->ExecuteS($sql);
        file_put_contents(dirname(__FILE__).'/configuration.json', json_encode($result, JSON_UNESCAPED_UNICODE));
    }

    public function restoreModuleSettings()
    {
        $data = json_decode(file_get_contents(dirname(__FILE__).'/configuration.json'));
        foreach ($data as $item) {
            Configuration::UpdateValue($item->name, $item->value, false, $item->id_shop_group, $item->id_shop);
        }
    }
    
    public function po($paymentOptions)
    {
        return array_map(function (array $options) use (&$id) {
            return array_map(function (PaymentOption $option) use (&$id) {
                ++$id;
                $formattedOption = $option->toArray();
                $formattedOption['id'] = $formattedOption['module_name'];

                if ($formattedOption['form']) {
                    $decorator = new PaymentOptionFormDecorator();
					$formattedOption['form'] = $decorator->addHiddenSubmitButton(
                        $formattedOption['form'],
                        $formattedOption['id']
                    );
                }

                return $formattedOption;
            }, $options);
        }, $paymentOptions);
    }

    public function renderSeq()
    {
        switch (Configuration::get($this->name.'_simple_layout')) {
            case 'avant': return 'customer,carrier,payment,cart,checkout';
            default: return 'cart,customer,carrier,payment,checkout';
        }
    }
    
    public function renderCustomerSeq()
    {
        switch (Configuration::get($this->name.'_simple_layout')) {
            case 'x': return 'customer,carrier,payment,checkout';
            default: return 'customer';
        }
    }

    public function GetPayments()
    {
        		
		$modules_list = PaymentModule::getInstalledPaymentModules();
		$payment_list = [];
        foreach ($modules_list as $module) {
            if ($module['name'] == 'paypal') {
                continue;
            }
            if ($module['name'] == 'advancedcheckout') {
                continue;
            }
            if ($module['name'] == 'universalpay'){
				require_once(_PS_MODULE_DIR_ . 'universalpay/classes/UniPaySystem.php');
				$paysystems = UniPaySystem::getPaySystems($this->context->language->id, true);
				foreach ($paysystems as $paysystem){
					$payment_list[$module['id_module'].'_'.$paysystem['id_universalpay_system']] = [
					'name' => $paysystem['name'],
					'id' => 'universalpay_'.$paysystem['id_universalpay_system'],
					'description' => $paysystem['description_short'],
					];
				}
			
			}else{
				$module_obj = Module::getInstanceById($module['id_module']);
				if(is_object($module_obj))
					$payment_list[$module['id_module']] = [
						'name' => $module_obj->displayName,
						'id' => $module['name'],
						'description' => $module_obj->description,
					];
			}
        }
        return $payment_list;
    }


	public function getStep($id_product)
	{
		try {$step = Db::getInstance()->GetValue("SELECT `step` FROM `"._DB_PREFIX_."product` WHERE `id_product` = '{$id_product}'");}
		catch(Exception $a) {$step = 1;}
		return $step?$step:1;
	}
    
    
}
