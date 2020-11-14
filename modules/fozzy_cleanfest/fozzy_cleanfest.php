<?php
/**
* 2007-2020 PrestaShop
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
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fozzy_cleanfest extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fozzy_cleanfest';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'Fozzy, Britoff A.';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Фестиваль чистоты');
        $this->description = $this->l('Фестиваль чистоты октябрь-ноябрь 2020');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('FOZZY_CLEANFEST_LIVE_MODE', true);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('FOZZY_CLEANFEST_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitFozzy_cleanfestModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = '';

        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFozzy_cleanfestModule';
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
                        'name' => 'FOZZY_CLEANFEST_LIVE_MODE',
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
                        'name' => 'FOZZY_CLEANFEST_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'FOZZY_CLEANFEST_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'FOZZY_CLEANFEST_LIVE_MODE' => Configuration::get('FOZZY_CLEANFEST_LIVE_MODE', true),
            'FOZZY_CLEANFEST_ACCOUNT_EMAIL' => Configuration::get('FOZZY_CLEANFEST_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'FOZZY_CLEANFEST_ACCOUNT_PASSWORD' => Configuration::get('FOZZY_CLEANFEST_ACCOUNT_PASSWORD', null),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookdisplayHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front_01.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front1.css');
    }
    
    public function add_fiskal($data = array())
    {
        $sql_check = "SELECT `fiskal_num` FROM `" . _DB_PREFIX_ . "fozzy_cleanfest` WHERE `fiskal_num` = '".$data['fiskal_num']."'";
        $check = Db::getInstance()->GetValue($sql_check);
        
          $code = 0;
          $ret = $this->l('Чек успешно зарегистрирован!');
          if ($check) {
            $ret = $this->l('Данный чек уже внесен ранее!');
            $code = 1;
            }
        
          $return = array(
            'errorcode' => $code,
            'errormes'  => $ret
            );
            
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "fozzy_cleanfest` (`id_fozzy_cleanfest`, `lastname`, `firstname`, `phone`, `email`, `shop`, `shop_address`, `fiskal_num`, `fiskal_date`, `pravila`) VALUES (NULL, '".$data['lastname']."', '".$data['firstname']."', '".$data['phone']."', '".$data['email']."', NULL, NULL, '".$data['fiskal_num']."', '".$data['fiskal_date']."', '1')";
        Db::getInstance()->execute($sql);
        
        return $return; 
    }
    
    
}
