<?php
/**
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
 *  @author    Yuri Denisov <contact@splashmart.ru>
 *  @copyright 2014-2017 Yuri Denisov
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__).'/classes/OOCToolsClass.php');

include_once(dirname(__FILE__).'/models/OOCGroupModel.php');
include_once(dirname(__FILE__).'/models/OOCTitlesModel.php');
include_once(dirname(__FILE__).'/models/OOCOrderFieldsModel.php');
include_once(dirname(__FILE__).'/models/OOCTypeOrderFieldsModel.php');
include_once(dirname(__FILE__).'/models/OOCUserFieldsCorrModel.php');

include_once(dirname(__FILE__).'/models/OOCOrderModel.php');
include_once(dirname(__FILE__).'/models/OOCCartModel.php');
include_once(dirname(__FILE__).'/models/OOCFieldsModel.php');
include_once(dirname(__FILE__).'/models/OOCCartProductModel.php');
include_once(dirname(__FILE__).'/models/OOCCustomizationModel.php');
include_once(dirname(__FILE__).'/models/OOCCartVouchersModel.php');

class OrderInOneClick extends Module
{
    const OOC_CONTROLLER = 'AdminOOC';

    public function __construct()
    {
        $this->name = 'orderinoneclick';
        $this->tab = 'front_office_features';
        $this->version = '2.3.0';
        $this->author = 'Yuri Denisov';
        $this->module_key = 'ac9b2f9b0fd746ba8b60d6f2b115f20f';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Order In One Click (Quick order)');
        $this->description = $this->l('Allows customers or guests to create a quick orders in one click. ');
        $this->description .= $this->l('You can use as a service of pre-orders.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->_files = array(
            'name' => array('mail_to_customer','mail_to_admin'),
            'ext' => array(
                0 => 'html',
                1 => 'txt'
            )
        );
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install()
            && $this->initData()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayBackOfficeTop')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayExpressCheckout')
            && $this->registerHook('displayOOCProductButton')
            && $this->registerHook('displayOOCQuickViewButton')
            && $this->registerHook('displayFooter')
            && Configuration::updateGlobalValue('SM_OOC_SHOW_IN_TOP', false)
            && Configuration::updateGlobalValue('SM_OOC_SHOW_PRICE_IN_CUSTOMER_CURRENCY', true)
            && Configuration::updateGlobalValue('SM_OOC_OWN_FIELDS_SETTINGS', false)
            && Configuration::updateGlobalValue('SM_OOC_ALLOW_GUEST', true)
            && Configuration::updateGlobalValue('SM_OOC_ALLOW_CUSTOMER', true)
            && Configuration::updateGlobalValue('SM_OOC_SHOW_IN_PP', true)
            && Configuration::updateGlobalValue('SM_OOC_SHOW_IN_QW', true)
            && Configuration::updateGlobalValue('SM_OOC_SHOW_IN_CO', true)
            && Configuration::updateGlobalValue('SM_OOC_PQCHK', true)
            && Configuration::updateGlobalValue('SM_OOC_CMS_ID', 0)
            && Configuration::updateGlobalValue('SM_OOC_SEND_EMAIL_TO_CUSTOMER', true)
            && Configuration::updateGlobalValue('SM_OOC_EMAIL_TO_ADMIN_NOTIFY', null)
            && $this->_installModuleTab(1, self::OOC_CONTROLLER, 'Orders in One Click', 'AdminParentOrders')
            && $this->_installModuleTab(0, 'AdminOOCOrderFields', 'Quick Order Fields', 'AdminParentOrders');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall()
            && $this->_uninstallModuleTab(self::OOC_CONTROLLER)
            && $this->_uninstallModuleTab('AdminOOCOrderFields')
            && Configuration::deleteByName('SM_OOC_SHOW_IN_TOP')
            && Configuration::deleteByName('SM_OOC_SHOW_PRICE_IN_CUSTOMER_CURRENCY')
            && Configuration::deleteByName('SM_OOC_DEFAULT_GROUP_ORDERS')
            && Configuration::deleteByName('SM_OOC_VIEWED_GROUP_ORDERS')
            && Configuration::deleteByName('SM_OOC_DEFAULT_GROUP_ORDERS')
            && Configuration::deleteByName('SM_OOC_OWN_FIELDS_SETTINGS')
            && Configuration::deleteByName('SM_OOC_ALLOW_GUEST')
            && Configuration::deleteByName('SM_OOC_ALLOW_CUSTOMER')
            && Configuration::deleteByName('SM_OOC_SHOW_IN_PP')
            && Configuration::deleteByName('SM_OOC_SHOW_IN_QW')
            && Configuration::deleteByName('SM_OOC_SHOW_IN_CO')
            && Configuration::deleteByName('SM_OOC_PQCHK')
            && Configuration::deleteByName('SM_OOC_SEND_EMAIL_TO_CUSTOMER')
            && Configuration::deleteByName('SM_OOC_EMAIL_TO_ADMIN_NOTIFY')
            && Configuration::deleteByName('SM_OOC_CMS_ID');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/bootstrap-modals.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/ooc_front.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/ooc_window.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/spinner.css', 'all');
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/ooc_admin.css', 'all');
    }

    public function hookDisplayBackOfficeTop()
    {
        if (Configuration::get('SM_OOC_SHOW_IN_TOP')) {
            $this->context->smarty->assign(
                array(
                    'href' => $this->context->link->getAdminLink(self::OOC_CONTROLLER),
                    'num' => (int)OOCOrderModel::getNumNotViewedOrders($this->context->shop->id),
                )
            );
            return $this->display(__FILE__, 'views/templates/admin/notif_in_header.tpl');
        }
        return null;
    }

    public function hookDisplayExpressCheckout()
    {
        if (!Configuration::get('SM_OOC_SHOW_IN_CO')) {
            return;
        }

        $id_customer = $this->context->cart->id_customer;

        if (((int)Configuration::get('SM_OOC_ALLOW_CUSTOMER') && $id_customer) ||
            ((int)Configuration::get('SM_OOC_ALLOW_GUEST') && !$id_customer)
        ) {
            $this->context->smarty->assign(
                array(
                    'action_url' => __PS_BASE_URI__.'modules/orderinoneclick/ajax.php',
                    'product_button' => false,
                    'require_cms' => (int)Configuration::get('SM_OOC_CMS_ID'),
                )
            );
            return $this->display(__FILE__, 'views/templates/front/ooc_cart_button.tpl');
        }
        return null;
    }

    public function hookDisplayFooter()
    {
        return $this->display(__FILE__, 'views/templates/front/ooc_window_tag.tpl');
    }

    public function hookDisplayOOCQuickViewButton()
    {
        if ((int)Configuration::get('SM_OOC_SHOW_IN_QW')) {
            return $this->renderOOCProductButton();
        }
    }

    public function hookDisplayOOCProductButton()
    {
        if ((int)Configuration::get('SM_OOC_SHOW_IN_PP')) {
            return $this->renderOOCProductButton();
        }
    }

    protected function renderOOCProductButton()
    {
        $id_customer = (int)$this->context->cart->id_customer;
        $allow_customer = (int)Configuration::get('SM_OOC_ALLOW_CUSTOMER');
        $allow_guest = (int)Configuration::get('SM_OOC_ALLOW_GUEST');
        if (($allow_customer && $id_customer) || ($allow_guest && !$id_customer)) {
            $this->context->smarty->assign(
                array(
                    'action_url' => __PS_BASE_URI__.'modules/orderinoneclick/ajax.php',
                    'product_button' => true,
                    'require_cms' => (int)Configuration::get('SM_OOC_CMS_ID'),
                )
            );
            return $this->display(__FILE__, 'views/templates/front/ooc_cart_button.tpl');
        };
    }

    protected function renderConfigOOCWindow()
    {
        $id_shop = (int)$this->context->shop->id;
        $legend_title = $this->l('Quick Order fields settings for Shop: ').OOCToolsClass::getShopName($id_shop);

        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = (int)($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }

        $helper_window_config = new HelperForm();
        $helper_window_config->module = $this;
        $helper_window_config->name_controller = $this->name;
        $helper_window_config->token = Tools::getAdminTokenLite('AdminModules');
        $helper_window_config->currentIndex = AdminController::$currentIndex.'&configure='.$this->name
            . '&confoocwindow'.$this->name;

        $helper_window_config->languages = $languages;
        $helper_window_config->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper_window_config->allow_employee_form_lang = true;

        $helper_window_config->title = $this->displayName;
        $helper_window_config->show_toolbar = true;
        $helper_window_config->toolbar_scroll = true;
        $helper_window_config->submit_action = 'confoocwindow'.$this->name;

        $helper_window_config_fields_form = array();

        $helper_window_config_fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $legend_title,
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'col' => 4,
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'col' => 6,
                    'lang' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $title = array();
        $description = array();
        foreach ($languages as $lang) {
            $object = OOCTitlesModel::getObjectByShopAndLang($id_shop, $lang['id_lang']);
            $title[$lang['id_lang']] = $object->title;
            $description[$lang['id_lang']] = $object->description;
        }

        $helper_window_config->tpl_vars = array(
            'fields_value' => array(
                'title' => $title,
                'description' => $description,
             ),
        );

        return $helper_window_config->generateForm($helper_window_config_fields_form);
    }

    public function getContent()
    {
        $this->_postProcess();

        if (Tools::isSubmit('confoocwindow'.$this->name)) {
            if (OOCToolsClass::notAllowOwnFields()) {
                return $this->displayWarning(
                    $this->l('This option for current shop is disabled in general configuration')
                );
            } else {
                return $this->renderConfigOOCWindow();
            }
        }

        return $this->renderConfigForm();
    }

    protected function renderConfigForm()
    {
        $cms_tab = array();
        $cms_tab[] = array('id' => 0, 'name' => $this->l('NONE'));
        foreach (CMS::listCms(Configuration::get('PS_LANG_DEFAULT')) as $cms_file) {
            $cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);
        }

        $helper_config = new HelperForm();
        $helper_config->module = $this;
        $helper_config->name_controller = $this->name;
        $helper_config->token = Tools::getAdminTokenLite('AdminModules');
        $helper_config->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper_config->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper_config->allow_employee_form_lang = true;

        $helper_config->title = $this->displayName;
        $helper_config->show_toolbar = true;
        $helper_config->toolbar_scroll = true;
        $helper_config->submit_action = 'submit'.$this->name;

        if (Shop::isFeatureActive() && $this->context->shop->isDefaultShop()) {
            $helper_config_fields_form = array();
            $helper_config_fields_form[0]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Multistore settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Allow shops to use own custom fields and window of quick order'),
                        'desc' => $this->l('Show...'),
                        'name' => 'SM_OOC_OWN_FIELDS_SETTINGS',
                        'values' =>    array(
                                    array(
                                        'id'    => 'active_on',
                                        'value' => 1,
                                    ),
                                    array(
                                        'id'    => 'active_off',
                                        'value' => 0,
                                    ),
                                ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            );
        }

        $helper_config_fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('General settings'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show in backoffice top'),
                    'desc' => $this->l('Show icon that notifies of new orders'),
                    'name' => 'SM_OOC_SHOW_IN_TOP',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow for guest'),
                    'name' => 'SM_OOC_ALLOW_GUEST',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow for customers'),
                    'name' => 'SM_OOC_ALLOW_CUSTOMER',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Button on Product page'),
                    'name' => 'SM_OOC_SHOW_IN_PP',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Button on Quick View page'),
                    'name' => 'SM_OOC_SHOW_IN_QW',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Button on Checkout page'),
                    'name' => 'SM_OOC_SHOW_IN_CO',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable quantity check of product on Product page'),
                    'name' => 'SM_OOC_PQCHK',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Send email to customer (if known)'),
                    'name' => 'SM_OOC_SEND_EMAIL_TO_CUSTOMER',
                    'values' =>    array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                ),
                            ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('CMS page for the Conditions of use'),
                    'desc' => $this->l('If NONE is selected that is not visible to the customer'),
                    'name' => 'SM_OOC_CMS_ID',
                    'options' => array(
                        'query' => $cms_tab,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'cast' => 'intval'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Notify the store administrator for a new order'),
                    'desc' => $this->l('Leave blank to disable option'),
                    'name' => 'SM_OOC_EMAIL_TO_ADMIN_NOTIFY',
                    'col' => 5,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $helper_config->tpl_vars = array(
            'fields_value' => array(
                'SM_OOC_OWN_FIELDS_SETTINGS' => (int)Configuration::get('SM_OOC_OWN_FIELDS_SETTINGS'),
                'SM_OOC_SHOW_IN_TOP' => (int)Configuration::get('SM_OOC_SHOW_IN_TOP'),
                'SM_OOC_ALLOW_GUEST' => (int)Configuration::get('SM_OOC_ALLOW_GUEST'),
                'SM_OOC_ALLOW_CUSTOMER' => (int)Configuration::get('SM_OOC_ALLOW_CUSTOMER'),
                'SM_OOC_SHOW_IN_PP' => (int)Configuration::get('SM_OOC_SHOW_IN_PP'),
                'SM_OOC_SHOW_IN_QW' => (int)Configuration::get('SM_OOC_SHOW_IN_QW'),
                'SM_OOC_SHOW_IN_CO' => (int)Configuration::get('SM_OOC_SHOW_IN_CO'),
                'SM_OOC_SEND_EMAIL_TO_CUSTOMER' => (int)Configuration::get('SM_OOC_SEND_EMAIL_TO_CUSTOMER'),
                'SM_OOC_CMS_ID' => (int)Configuration::get('SM_OOC_CMS_ID'),
                'SM_OOC_EMAIL_TO_ADMIN_NOTIFY' => Configuration::get('SM_OOC_EMAIL_TO_ADMIN_NOTIFY'),
                'SM_OOC_PQCHK' => (int)Configuration::get('SM_OOC_PQCHK'),
            ),
        );

        return $helper_config->generateForm($helper_config_fields_form);
    }

    protected function _postProcess()
    {
        $id_shop = $this->context->shop->id;

        if (Shop::isFeatureActive() && $this->context->shop->isDefaultShop()) {
            Configuration::updateGlobalValue(
                'SM_OOC_OWN_FIELDS_SETTINGS',
                Tools::getValue('SM_OOC_OWN_FIELDS_SETTINGS', Configuration::get('SM_OOC_OWN_FIELDS_SETTINGS'))
            );

            if (Tools::isSubmit('update_for_all_shops')) {
                $shops = Shop::getShops();
                foreach ($shops as $shop) {
                    foreach (Language::getLanguages(false) as $lang) {
                        $object = OOCTitlesModel::getObjectByShopAndLang($shop['id_shop'], $lang['id_lang']);
                        if (!$object) {
                            $object = new OOCTitlesModel();
                            $object->id_shop = $shop['id_shop'];
                            $object->id_lang = $lang['id_lang'];
                        }
                        $object->title = Tools::getValue('title_'.$lang['id_lang'], $object->title);
                        $object->description = Tools::getValue('description_'.$lang['id_lang'], $object->description);
                        $object->save();
                    }
                }
            }
        }

        if (Tools::isSubmit('confoocwindow'.$this->name) && !Tools::isSubmit('update_for_all_shops')) {
            foreach (Language::getLanguages(false) as $lang) {
                $object = OOCTitlesModel::getObjectByShopAndLang($id_shop, $lang['id_lang']);
                if (!$object) {
                    $object = new OOCTitlesModel();
                    $object->id_shop = $id_shop;
                    $object->id_lang = $lang['id_lang'];
                }
                $object->title = Tools::getValue('title_'.$lang['id_lang'], $object->title);
                $object->description = Tools::getValue('description_'.$lang['id_lang'], $object->description);
                $object->save();
            }
        }

        Configuration::updateValue(
            'SM_OOC_SHOW_IN_TOP',
            (int)Tools::getValue('SM_OOC_SHOW_IN_TOP', (int)Configuration::get('SM_OOC_SHOW_IN_TOP'))
        );
        Configuration::updateValue(
            'SM_OOC_ALLOW_GUEST',
            (int)Tools::getValue('SM_OOC_ALLOW_GUEST', (int)Configuration::get('SM_OOC_ALLOW_GUEST'))
        );
        Configuration::updateValue(
            'SM_OOC_ALLOW_CUSTOMER',
            (int)Tools::getValue('SM_OOC_ALLOW_CUSTOMER', (int)Configuration::get('SM_OOC_ALLOW_CUSTOMER'))
        );
        Configuration::updateValue(
            'SM_OOC_SHOW_IN_PP',
            (int)Tools::getValue('SM_OOC_SHOW_IN_PP', (int)Configuration::get('SM_OOC_SHOW_IN_PP'))
        );
        Configuration::updateValue(
            'SM_OOC_SHOW_IN_QW',
            (int)Tools::getValue('SM_OOC_SHOW_IN_QW', (int)Configuration::get('SM_OOC_SHOW_IN_QW'))
        );
        Configuration::updateValue(
            'SM_OOC_SHOW_IN_CO',
            (int)Tools::getValue('SM_OOC_SHOW_IN_CO', (int)Configuration::get('SM_OOC_SHOW_IN_CO'))
        );
        Configuration::updateValue(
            'SM_OOC_PQCHK',
            (int)Tools::getValue('SM_OOC_PQCHK', (int)Configuration::get('SM_OOC_PQCHK'))
        );
        Configuration::updateValue(
            'SM_OOC_SEND_EMAIL_TO_CUSTOMER',
            (int)Tools::getValue(
                'SM_OOC_SEND_EMAIL_TO_CUSTOMER',
                (int)Configuration::get('SM_OOC_SEND_EMAIL_TO_CUSTOMER')
            )
        );
        Configuration::updateValue(
            'SM_OOC_EMAIL_TO_ADMIN_NOTIFY',
            Tools::getValue(
                'SM_OOC_EMAIL_TO_ADMIN_NOTIFY',
                Configuration::get('SM_OOC_EMAIL_TO_ADMIN_NOTIFY')
            )
        );
        Configuration::updateValue(
            'SM_OOC_CMS_ID',
            (int)Tools::getValue('SM_OOC_CMS_ID', (int)Configuration::get('SM_OOC_CMS_ID'))
        );
    }

    public function _installModuleTab($active = 1, $tabClass = null, $tabName = null, $tabParent = null)
    {
        if (!$tabClass || !$tabName || !$tabParent) {
            return false;
        }
        $idTabParent = Tab::getIdFromClassName($tabParent);
        $tab = new Tab();
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTabParent;
        $tab->active = $active;
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $tab->name[(int)$language['id_lang']] = $tabName;
        }
        return $tab->save();
    }

    public function _uninstallModuleTab($tabClass = null)
    {
        if (!$tabClass) {
            return false;
        }
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }
        return false;
    }

    public function initData()
    {
        $result = true;
        $fields_types = array (
            array('name'=>'Email', 'action'=>'isEmail', 'corr'=>'email'),
            array('name'=>'Name', 'action'=>'isName', 'corr'=>'name'),
            array('name'=>'StateIsoCode', 'action'=>'isStateIsoCode'),
            array('name'=>'NumericIsoCode', 'action'=>'isNumericIsoCode'),
            array('name'=>'DiscountName', 'action'=>'isDiscountName'),
            array('name'=>'Message', 'action'=>'isMessage'),
            array('name'=>'CountryName', 'action'=>'isCountryName'),
            array('name'=>'Address', 'action'=>'isAddress'),
            array('name'=>'CityName', 'action'=>'isCityName'),
            array('name'=>'GenericName', 'action'=>'isGenericName'),
            array('name'=>'DateFormat', 'action'=>'isDateFormat'),
            array('name'=>'BirthDate', 'action'=>'isBirthDate'),
            array('name'=>'PhoneNumber', 'action'=>'isPhoneNumber', 'corr'=>'phone'),
            array('name'=>'PostCode', 'action'=>'isPostCode')
        );

        foreach ($fields_types as $field) {
            $validation_type = new OOCTypeOrderFieldsModel();
            $validation_type->name = $field['name'];
            $validation_type->validate_func = $field['action'];
            $result = $result && $validation_type->add();

            if (isset($field['corr'])) {
                $corr = new OOCUserFieldsCorrModel();
                $corr->id_sm_ooc_type_order_field = $validation_type->id;
                $corr->corr = $field['corr'];
                $corr->add();
            }
        }

        $group_order = new OOCGroupModel();
        $group_order->name = 'New';
        $group_order->add();
        Configuration::updateGlobalValue('SM_OOC_DEFAULT_GROUP_ORDERS', $group_order->id);

        $group_order = new OOCGroupModel();
        $group_order->name = 'Viewed';
        $group_order->add();
        Configuration::updateGlobalValue('SM_OOC_VIEWED_GROUP_ORDERS', $group_order->id);

        $group_order = new OOCGroupModel();
        $group_order->name = 'Archive';
        $group_order->add();
        Configuration::updateGlobalValue('SM_OOC_ARCHIVE_GROUP_ORDERS', $group_order->id);

        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            foreach (Language::getLanguages(false) as $lang) {
                $sh = new OOCTitlesModel();
                $sh->id_shop = $shop['id_shop'];
                $sh->id_lang = $lang['id_lang'];
                $sh->title = Tools::strtolower($lang['iso_code']) == 'ru'
                    ? "Простой заказ в один клик"
                    : "Simple order in one click";
                $sh->description = Tools::strtolower($lang['iso_code']) == 'ru'
                    ? "Наш менеджер свяжется с Вами в ближайшее время"
                    : "Our experts will call you, and specify the details of the order!";
                $sh->add();
            }
        }
        return $result;
    }
}
