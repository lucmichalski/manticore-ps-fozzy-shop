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

class Profitcalculations extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'profitcalculations';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'prestashopov.ru';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Profit calculations');
        $this->description = $this->l('Profit calculations');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('PROFIT_CACL_STATUS', '');
        Configuration::updateValue('PROFIT_CACL_PRODUCT', 'order');
        Configuration::updateValue('PROFIT_CACL_SHIPPING', 'shipping');

        include(dirname(__FILE__).'/sql/install.php');	
		$this->installTab();
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayBackOfficeHeader');
    }
	
  	public function installTab(){
  		$id = Tab::getIdFromClassName('AdminCatalog');
  		$tab = new Tab();
  		$tab->class_name = 'AdminProfitCalculations';
  		$tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Profit calculations');
  		$tab->id_parent = $id;
  		$tab->module = $this->name;
  		$tab->add();
  		return true;
  	}	

    public function uninstall()
    {
        Configuration::deleteByName('PROFIT_CACL_STATUS');
        Configuration::deleteByName('PROFIT_CACL_PRODUCT');
        Configuration::deleteByName('PROFIT_CACL_SHIPPING');		
		
		$idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminProfitCalculations');
          foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }			

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
        if (((bool)Tools::isSubmit('submitProfitcalculationsModule')) == true) {
            $this->postProcess();
        }

        return $this->renderForm();
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
        $helper->submit_action = 'submitProfitcalculationsModule';
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
						'type' => 'select',
						'label' => $this->l('Status of delivered orders'),
						'name' => 'PROFIT_CACL_STATUS',
						'options' => array(
							'query' => OrderState::getOrderStates($this->context->language->id),
							'id' => 'id_order_state',
							'name' => 'name'
						),
					 ),
					 array(
						'type' => 'text',
						'col' => 2,
						'label' => $this->l('Transaction type'),
						'desc' => $this->l('Type the name of the balance of order. Default "order"'),
						'name' => 'PROFIT_CACL_PRODUCT',
						),
					 array(
						'type' => 'text',
						'col' => 2,
						'label' => $this->l('Transaction type'),
						'desc' => $this->l('Type the name of the balance of shipping of the delivered order. Default "shipping"'),
						'name' => 'PROFIT_CACL_SHIPPING',
						)
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
				// 'buttons' => array(
					// array(
						// 'type' => 'submit',
						// 'id' => 'prod_check_all_product',
						// 'class' => 'pull-right',
						// 'icon' => 'process-icon-refresh',
						// 'name' => 'refreshProfit',
						// 'title' => $this->l('Check Products'),
					// )
				// ),				
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'PROFIT_CACL_STATUS' => Configuration::get('PROFIT_CACL_STATUS'),
            'PROFIT_CACL_PRODUCT' => Configuration::get('PROFIT_CACL_PRODUCT'),
            'PROFIT_CACL_SHIPPING' => Configuration::get('PROFIT_CACL_SHIPPING'),
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
		if (Tools::isSubmit('refreshProfit'))
		{		
			
		}
    }
	
	// public function refreshProfit() {
		
	// }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        /* Place your code here. */
    }
}
