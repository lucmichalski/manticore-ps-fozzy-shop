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

class Fozzy_kassa extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fozzy_kassa';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Britoff A., Novevision.com';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Сведение кассы');
        $this->description = $this->l('Фоззи - Сведение кассы');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {

        include(dirname(__FILE__).'/sql/install.php');
        $this->createAllModuleTabs();
        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionOrderHistoryAddAfter');
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
        if (((bool)Tools::isSubmit('submitFozzy_kassaModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = '';

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
        $helper->submit_action = 'submitFozzy_kassaModule';
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
                        'name' => 'FOZZY_KASSA_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
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
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'FOZZY_KASSA_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'FOZZY_KASSA_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
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
      /*  return array(
            'FOZZY_KASSA_LIVE_MODE' => Configuration::get('FOZZY_KASSA_LIVE_MODE', true),
            'FOZZY_KASSA_ACCOUNT_EMAIL' => Configuration::get('FOZZY_KASSA_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'FOZZY_KASSA_ACCOUNT_PASSWORD' => Configuration::get('FOZZY_KASSA_ACCOUNT_PASSWORD', null),
        ); */
        return array();
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
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }
    
    public function hookActionOrderHistoryAddAfter($params)
    {
     
     $id_status = (int)$params['order_history']->id_order_state;
     $id_order = (int)$params['order_history']->id_order;
     
     if ($id_status == 4)
      {
        
     $sql_d = "SELECT `id_vodila` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order;
     $d = Db::getInstance()->executeS($sql_d);
     $vodila = (int)$d[0]['id_vodila']; //перевозчик
        
     if ($vodila > 0)
     {
      $sql_insert = "INSERT INTO `"._DB_PREFIX_."fozzy_kassa_prihod` (`date_mar`, `mar_num`, `window`, `id_address`, `adr_name`, `adr`, `weight`, `id_order`, `phone`, `id_vodila`, `vodila`, `payment`, `chek_summ`, `vozvrat`, `full_return`, `pretenzia`, `vopros_perenos`, `perenos`, `st_oplat_chek`, `st_oplat_vozvr`, `comment`, `date_chek`, `date_vozvr`, `date_vozvr_chek`, `id_filial`, `zone`, `zone_name`, `closed`, `nal`) 
        SELECT o.`dateofdelivery`, o.`Route_Num`, o.`period`, o.`id_address_delivery`, CONCAT(a.`lastname`, ' ', a.`firstname`, ' ', a.`company`) AS adr_name, CONCAT(a.`city`, ' ', a.`address1`) AS adr, o.`QtyW`, o.`id_order`, a.`phone_mobile`, o.`id_vodila`, v.`fio`, o.`payment`, o.`fiskal`,0,0,0,0,0,0,0,'',null,null,null, o.`id_shop`, o.`zone`, o.`zone_name`, 0, CASE
    WHEN (o.`payment` = 'Оплата наличными при получении' OR o.`payment` = 'Оплата при отриманні') 
        THEN 1
    ELSE 0
END AS `nal` 
        FROM `"._DB_PREFIX_."orders` o, `"._DB_PREFIX_."address` a, `"._DB_PREFIX_."fozzy_logistic_vodila` v
        WHERE o.`id_order` = ".$id_order." AND o.`id_address_delivery` = a.`id_address` AND o.`id_vodila` = v.`id_vodila`";
      Db::getInstance()->execute($sql_insert);
     }   
     else
     {
      $history = new OrderHistory();
      $history->id_order = $id_order;
      $history->id_employee = $this->context->employee->id;
      $history->changeIdOrderState(16, $id_order);
      $history->add();
     }   
        
        
      }
     if ($id_status == 932)
      {
        $sql_cancel = "UPDATE `"._DB_PREFIX_."orders` SET `id_vodila` = 0 WHERE `id_order` = ".$id_order;
        Db::getInstance()->execute($sql_cancel);
      }
      
     return;
    
    }
    
    
    public function myInstallModuleTab($name, $className, $id_parent)
    {
        $tab = new Tab();
        $tab->name = array();
        $tab->class_name = $className;

        if ($className == 'AdminKassa') {
            $tab->icon = 'payment';
        }

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->id_parent = (int) $id_parent;
        $tab->module = $this->name;

        if ($tab->save()) {
            return $tab;
        }
    }

    public function myDeleteModuleTabs()
    {
        $tabs = array(
            'AdminKassaSV',
            'AdminKassaMN',
            'AdminKassa',
            'AdminKassaSVController',
            'AdminKassaMNController',
            'AdminKassaMNPController',
            'AdminKassaVZController',
            'AdminKassaFZController',
            'AdminKassaVOZController',
            'AdminKassaController'
        );

        $idTabs = array();
        foreach ($tabs as $className) {
            $idTabs[] = Tab::getIdFromClassName($className);
        }

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                if (Validate::isLoadedObject($tab)) {
                    $tab->delete();
                }
            }
        }
    }

    /**
     * Install all module tabs.
     */
    public function createAllModuleTabs($force = false)
    {
        // Main tab
        $mainTab = $this->myInstallModuleTab(
            $this->l('Касса'),
            'AdminKassa',
            Tab::getIdFromClassName('IMPROVE')
        );

        // Posts
        $this->myInstallModuleTab(
            $this->l('Касса'),
            'AdminKassaSV',
            (int) $mainTab->id
        );

        // Categories
        $this->myInstallModuleTab(
            $this->l('Прием денег'),
            'AdminKassaMN',
            (int) $mainTab->id
        );
        
        $this->myInstallModuleTab(
            $this->l('Прием возвратов'),
            'AdminKassaVZ',
            (int) $mainTab->id
        );
        
        $this->myInstallModuleTab(
            $this->l('Не закрытые чеки'),
            'AdminKassaFZ',
            (int) $mainTab->id
        );
        
        $this->myInstallModuleTab(
            $this->l('Не закрытые маршруты'),
            'AdminKassaVOZ',
            (int) $mainTab->id
        );
        
        $this->myInstallModuleTab(
            $this->l('Принятые суммы'),
            'AdminKassaMNP',
            (int) $mainTab->id
        );

    }
    

}
