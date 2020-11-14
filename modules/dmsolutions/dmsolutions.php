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

class Dmsolutions extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'dmsolutions';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DM Solutions');
        $this->description = $this->l('Get geocode from DM Solutions');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall DM Solutions module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('DMSOLUTIONS_LIVE_MODE', true);
        Configuration::updateValue('DMSOLUTIONS_ACCOUNT_EMAIL', 'a.britoff@gmail.com');
        Configuration::updateValue('DMSOLUTIONS_ACCOUNT_PASSWORD', '123qaZ456!');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('displayBackOfficeFooter') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('DMSOLUTIONS_LIVE_MODE');
        Configuration::deleteByName('DMSOLUTIONS_ACCOUNT_EMAIL');
        Configuration::deleteByName('DMSOLUTIONS_ACCOUNT_PASSWORD');
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
        if (((bool)Tools::isSubmit('submitDmsolutionsModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        //$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $output = '';
      //  dump($this->context->employee->id);
        return $output.$this->renderForm();
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
        $helper->submit_action = 'submitDmsolutionsModule';
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

    /**
     * Create the structure of your form.
     */
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
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'DMSOLUTIONS_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('DM Solutions account email'),
                        'name' => 'DMSOLUTIONS_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'DMSOLUTIONS_ACCOUNT_PASSWORD',
                        'label' => $this->l('DM Solutions account password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'DMSOLUTIONS_LIVE_MODE' => Configuration::get('DMSOLUTIONS_LIVE_MODE', true),
            'DMSOLUTIONS_ACCOUNT_EMAIL' => Configuration::get('DMSOLUTIONS_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'DMSOLUTIONS_ACCOUNT_PASSWORD' => Configuration::get('DMSOLUTIONS_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookactionAdminControllerSetMedia()
    {
       if ($this->context->controller->controller_name == 'AdminAddresses' && Tools::getValue('id_address') )
       {
        $this->context->controller->addJS($this->_path.'views/js/back_40.js'.'?'.time());
        $this->context->controller->addCSS($this->_path.'views/css/back_04.css');
       }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
      if ($this->context->controller->php_self == 'order') $this->context->controller->addJS($this->_path.'/views/js/front_03.js');
       // $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayBackOfficeHeader()
    {
       //  dump($this->context->employee->id);
       if ($this->context->controller->controller_name == 'AdminAddresses' && Tools::getValue('id_address') )
       {
       // dump($this->context->controller);
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.dmsolutions.com.ua:2661/Token",
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 10,
          CURLOPT_FOLLOWLOCATION => false,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "username=".Configuration::get('DMSOLUTIONS_ACCOUNT_EMAIL')."&password=".Configuration::get('DMSOLUTIONS_ACCOUNT_PASSWORD')."&grant_type=password",
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/x-www-form-urlencoded"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
        //  dump("cURL Error #:" . $err);
        } else {
          $res_array = json_decode($response);
          $dm_token = $res_array->access_token;
        }
        
        $address = new Address((int)Tools::getValue('id_address'));
        $id_customer = (int)$address->id_customer; 
        $customer = new Customer($id_customer);
        $c_id_shop = $customer->id_shop;
        Media::addJsDef(array(
          'dm_token' => $dm_token,
          'dm_id_shop' => $c_id_shop
        ));
      
        //dump();
        
       }
    }
}
