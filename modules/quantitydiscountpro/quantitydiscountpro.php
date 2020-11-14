<?php
/**
* Quantity Discount Pro
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2019 idnovate.com
*  @license   See above
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRule.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleFamily.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountDatabase.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleCondition.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleGroup.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleAction.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleMessage.php');

class QuantityDiscountPro extends Module
{
    protected static $_validRules;
    protected $_default_pagination = 10;

    public function __construct()
    {
        $this->name = 'quantitydiscountpro';
        $this->author = 'idnovate';
        $this->version = '2.1.31';
        $this->tab = 'pricing_promotion';

        parent::__construct();

        $parent_class_name = version_compare(_PS_VERSION_, '1.7', '<') ? 'AdminPriceRule' : 'AdminCatalog';

        $this->tabs = array();
        $this->tabs[] = array(
            'class_name' => 'AdminQuantityDiscountRules',
            'parent_class_name' => $parent_class_name,
            'name' => $this->l('Promotions and discounts'),
            'visible' => true,
        );

        $this->tabs[] = array(
            'class_name' => 'AdminQuantityDiscountRulesFamilies',
            'name' => $this->l('Rule families'),
            'visible' => false,
        );

        $this->displayName = $this->l('Promotions and discounts - (3x2, reductions, campaigns)');
        $this->description = $this->l('Apply discounts depending on the products from the cart');
        $this->confirmUninstall = $this->l('Are you sure that you want to delete the module and the related data?');

        $this->warning = $this->getWarnings(false);
    }

    public function install()
    {
        if (!$this->copyOverrideFolder()) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.7', '>=')
            && !file_exists(_PS_OVERRIDE_DIR_.'classes/checkout')) {
            mkdir(_PS_OVERRIDE_DIR_.'classes/checkout', 0777, true);
        }

        /*Register hooks and tab*/
        if (!parent::install()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayLeftColumn')
            || !$this->registerHook('displayLeftColumn')
            || !$this->registerHook('displayLeftColumnProduct')
            || !$this->registerHook('displayRightColumn')
            || !$this->registerHook('displayRightColumnProduct')
            || !$this->registerHook('displayproductButtons')
            || !$this->registerHook('displayProductTab')
            || !$this->registerHook('displayProductTabContent')
            || !$this->registerHook('displayFooterProduct')
            || !$this->registerHook('displayProductPriceBlock')
            || !$this->registerHook('shoppingCart')
            || !$this->registerHook('shoppingCartExtra')
            || !$this->registerHook('displayBeforeCarrier')
            || !$this->registerHook('displayPaymentTop')
            || !$this->registerHook('displayTop')
            || !$this->registerHook('displayFooter')
            || !$this->registerHook('displayBanner')
            || !$this->registerHook('displayHome')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('actionAuthentication')
            || !$this->registerHook('actionCustomerAccountAdd')
            || !$this->registerHook('displayQuantityDiscountProCustom1')
            || !$this->registerHook('displayQuantityDiscountProCustom2')
            || !$this->registerHook('displayQuantityDiscountProCustom3')
            || !$this->registerHook('displayQuantityDiscountProCustom4')
            || !$this->registerHook('displayQuantityDiscountProCustom5')
            || !QuantityDiscountDatabase::CreateTables()
            || !$this->installTabs()) {
            return false;
        }

        //install first family
        $shops = Shop::getShops(false, null, true);
        foreach ($shops as $shop) {
            $qdrf = new QuantityDiscountRuleFamily();
            $qdrf->active = 1;
            $qdrf->id_shop = $shop;
            $qdrf->name = 'Default';
            $qdrf->execute_other_families = 1;
            $qdrf->save();
        }

        return true;
    }

    public function copyOverrideFolder()
    {
        if (!is_writable(_PS_MODULE_DIR_.$this->name)) {
            return false;
        }

        $override_folder_name = "override";

        $version_override_folder = _PS_MODULE_DIR_.$this->name.'/'.$override_folder_name.'_'.Tools::substr(str_replace('.', '', _PS_VERSION_), 0, 2);
        $override_folder = _PS_MODULE_DIR_.$this->name.'/'.$override_folder_name;

        if (file_exists($override_folder) && is_dir($override_folder)) {
            $this->recursiveRmdir($override_folder);
        }

        if (is_dir($version_override_folder)) {
            $this->copyDir($version_override_folder, $override_folder);
        }

        return true;
    }

    protected function copyDir($src, $dst)
    {
        if (is_dir($src)) {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src.'/'.$file)) {
                        $this->copyDir($src.'/'.$file, $dst.'/'.$file);
                    } else {
                        copy($src.'/'.$file, $dst.'/'.$file);
                    }
                }
            }
            closedir($dir);
        }
    }

    protected function recursiveRmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->recursiveRmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function installTabs()
    {
        if (version_compare(_PS_VERSION_, '1.7.1', '>=')) {
            return true;
        }

        foreach ($this->tabs as $myTab) {
            if (!Tab::getIdFromClassName($myTab['class_name'])) {
                $tab = new Tab();
                $tab->class_name = $myTab['class_name'];
                $tab->module = $this->name;

                if (isset($myTab['parent_class_name'])) {
                    $tab->id_parent = Tab::getIdFromClassName($myTab['parent_class_name']);
                } else {
                    $tab->id_parent = -1;
                }

                $languages = Language::getLanguages(false);
                foreach ($languages as $lang) {
                    $tab->name[$lang['id_lang']] = $myTab['name'];
                }

                $tab->add();
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (!$this->copyOverrideFolder()) {
            return false;
        }

        $result = parent::uninstall()
            && $this->uninstallTab()
            && QuantityDiscountRule::removeUnusedRules()
            && QuantityDiscountDatabase::dropTables();

        return (bool)$result;
    }

    public function uninstallTab()
    {
        if (version_compare(_PS_VERSION_, '1.7.1', '>=')) {
            return true;
        }

        foreach ($this->tabs as $myTab) {
            $idTab = Tab::getIdFromClassName($myTab['class_name']);
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }

        return true;
    }

    public function enable($force_all = false)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            if (!$this->copyOverrideFolder()) {
                return false;
            }

            if (!file_exists(_PS_OVERRIDE_DIR_.'classes/checkout')) {
                mkdir(_PS_OVERRIDE_DIR_.'classes/checkout', 0777, true);
            }
        }

        return parent::enable($force_all);
    }

    public function getContent()
    {
        if (Tools::getValue('magic')) {
            return $this->renderForm();
        }

        return Tools::redirectAdmin('index.php?controller=AdminQuantityDiscountRules&token='.Tools::getAdminTokenLite('AdminQuantityDiscountRules'));
    }

        public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => 'Configuration',
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => 'Light mode',
                        'name' => 'QDP_LIGHT_MODE',
                        'col' => 1
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&magic=1';
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'QDP_LIGHT_MODE' => Tools::getValue('QDP_LIGHT_MODE', Configuration::get('QDP_LIGHT_MODE')),
        );
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/qdp.css');
        $this->context->controller->addJS($this->_path.'views/js/qdp.js');

        $request_uri = Tools::getHttpHost(false, false);

        if (isset($_SERVER['REQUEST_URI'])) {
            $request_uri .= $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['REDIRECT_URL'])) {
            $request_uri .= $_SERVER['REDIRECT_URL'];
        }

        if (!Validate::isUrl($request_uri)) {
            $request_uri = '';
        }

        //Get QDR with referer condition
        $quantitydiscountRules = QuantityDiscountRule::getQuantityDiscountRulesWithCondition(27);
        foreach ($quantitydiscountRules as $quantitydiscountRule) {
            $quantitydiscountRuleCondition = new QuantityDiscountRuleCondition($quantitydiscountRule['id_quantity_discount_rule_condition']);
            if (strpos($request_uri, $quantitydiscountRuleCondition->url_string)) {
                $qdp_url_string = Tools::jsonDecode(Context::getContext()->cookie->qdp_url_string, true);
                if (!isset($qdp_url_string[$quantitydiscountRule['id_quantity_discount_rule']])) {
                    $qdp_url_string[$quantitydiscountRule['id_quantity_discount_rule']] = true;
                    $this->context->cookie->qdp_url_string = Tools::jsonEncode($qdp_url_string);
                }
            }
        }
    }

    public function hookDisplayFooter()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    public function hookDisplayHome()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    public function hookActionValidateOrder($params)
    {
        //Get all cart rules and check if there are Quantity Discount cart rules, to insert a new record
        $cart_rules = array();

        $cart_rules_array = $this->context->cart->getCartRules();
        foreach ($cart_rules_array as $value) {
            $cart_rules[] = $value['id_cart_rule'];
        }

        $quantity_discount_cart_rules = $this->getQuantityDiscountCartRules((int)$this->context->cart->id);

        if (count($cart_rules) > 0 and count($quantity_discount_cart_rules) > 0) {
            foreach ($quantity_discount_cart_rules as $quantity_discount_cart_rule) {
                if (in_array($quantity_discount_cart_rule['id_cart_rule'], $cart_rules)) {
                    $fields = array(
                        'id_order' => (int)$params['order']->id,
                        'id_quantity_discount_rule' => (int)$quantity_discount_cart_rule['id_quantity_discount_rule'],
                        'id_cart_rule' => (int)$quantity_discount_cart_rule['id_cart_rule'],
                    );

                    Db::getInstance()->insert('quantity_discount_rule_order', $fields);
                }
            }
        }
    }

    public function hookActionAuthentication()
    {
        $quantityDiscount = new QuantityDiscountRule();
        $quantityDiscount->createAndRemoveRules();
    }

    public function hookActionCustomerAccountAdd()
    {
        $quantityDiscount = new QuantityDiscountRule();
        $quantityDiscount->createAndRemoveRules();
    }

    public function hookActionCarrierProcess()
    {
        if (version_compare(_PS_VERSION_, '1.5.0.13', '<') && Module::isInstalled('mondialrelay')
            || (version_compare(_PS_VERSION_, '1.5.0.13', '>=') && Module::isEnabled('mondialrelay'))) {
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules(null, null, false);
        }
    }

    public function getQuantityDiscountCartRules($id_cart)
    {
        $results = Db::getInstance()->executeS(
            'SELECT id_cart_rule, id_quantity_discount_rule
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`
            WHERE `id_cart` = '.(int)$id_cart
        );

        $cart_rule = array();
        foreach ($results as $result) {
            $cart_rule[] = $result;
        }

        return $cart_rule;
    }

    /* Common */
    public function hookDisplayLeftColumn()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    public function hookDisplayRightColumn()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    public function hookDisplayTop()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    /* Product page */
    public function hookDisplayLeftColumnProduct()
    {
        return $this->getMessage(__FUNCTION__, (int)Tools::getValue('id_product'), true);
    }

    public function hookDisplayRightColumnProduct()
    {
        return $this->getMessage(__FUNCTION__, (int)Tools::getValue('id_product'), true);
    }

    public function hookDisplayProductButtons()
    {
        return $this->getMessage(__FUNCTION__, (int)Tools::getValue('id_product'), true);
    }

    public function hookDisplayProductTab()
    {
        return $this->getMessage(__FUNCTION__, (int)Tools::getValue('id_product'), true);
    }

    public function hookDisplayProductTabContent()
    {
        return $this->getMessage(__FUNCTION__, (int)Tools::getValue('id_product'), true);
    }

    public function hookDisplayFooterProduct()
    {
        return $this->getMessage(__FUNCTION__, (int)Tools::getValue('id_product'), true);
    }

    /* Category page */
    public function hookDisplayProductPriceBlock($params)
    {
        if (Dispatcher::getInstance()->getController() != 'product'
            && isset($params['product'])
            && isset($params['type'])
            && $params['type'] == 'weight') {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } else {
                $id_product = $params['product']->id;
            }

            return $this->getMessage(__FUNCTION__, (int)$id_product, isset($id_product) && $id_product ? true : false);
        } else if (Dispatcher::getInstance()->getController() == 'product'
            && isset($params['type'])
            && $params['type'] == 'after_price') {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } else {
                $id_product = $params['product']->id;
            }

            return $this->getMessage(__FUNCTION__.'Product', (int)$id_product, isset($id_product) && $id_product ? true : false);
        }
    }

    /* Shopping cart */
    public function hookShoppingCart()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    public function hookShoppingCartExtra()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    public function hookDisplayBeforeCarrier()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    public function hookDisplayPaymentTop()
    {
        return $this->getMessage(__FUNCTION__, null, false);
    }

    /* Custom */
    public function hookDisplayQuantityDiscountProCustom1($params)
    {
        if (isset($params['product'])) {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } else {
                $id_product = $params['product']->id;
            }
        } else {
            $id_product = null;
        }

        return $this->getMessage(__FUNCTION__, $id_product, isset($id_product) && $id_product ? true: false);
    }

    public function hookDisplayQuantityDiscountProCustom2($params)
    {
        if (isset($params['product'])) {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } else {
                $id_product = $params['product']->id;
            }
        } else {
            $id_product = null;
        }

        return $this->getMessage(__FUNCTION__, $id_product, isset($id_product) && $id_product ? true: false);
    }

    public function hookDisplayQuantityDiscountProCustom3($params)
    {
        if (isset($params['product'])) {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } else {
                $id_product = $params['product']->id;
            }
        } else {
            $id_product = null;
        }

        return $this->getMessage(__FUNCTION__, $id_product, isset($id_product) && $id_product ? true: false);
    }

    public function hookDisplayQuantityDiscountProCustom4($params)
    {
        if (isset($params['product'])) {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } else {
                $id_product = $params['product']->id;
            }
        } else {
            $id_product = null;
        }

        return $this->getMessage(__FUNCTION__, $id_product, isset($id_product) && $id_product ? true: false);
    }

    public function hookDisplayQuantityDiscountProCustom5($params)
    {
        if (isset($params['product'])) {
            if (is_array($params['product'])) {
                $id_product = $params['product']['id_product'];
            } else {
                $id_product = $params['product']->id;
            }
        } else {
            $id_product = null;
        }

        return $this->getMessage(__FUNCTION__, $id_product, isset($id_product) && $id_product ? true: false);
    }

    private function getMessage($hookName, $id_product = null, $validateProducts = true)
    {
        $html = '';

        $key = (int)$id_product.'_'.(bool)$validateProducts.'_'.$hookName;

        if (!isset(self::$_validRules[$key])) {
            foreach (QuantityDiscountRuleFamily::getQuantityDiscountRuleFamilies() as $ruleFamily) {
                $quantityDiscountRules = QuantityDiscountRule::getQuantityDiscountRulesByFamilyForMessages((int)$ruleFamily['id_quantity_discount_rule_family'], $hookName);
                if (is_array($quantityDiscountRules) && count($quantityDiscountRules)) {
                    foreach ($quantityDiscountRules as $quantityDiscountRule) {
                        $quantityDiscountRuleObj = new QuantityDiscountRule((int)$quantityDiscountRule['id_quantity_discount_rule']);
                        if ($quantityDiscountRuleObj->isQuantityDiscountRuleValidForMessages()
                            && ($quantityDiscountRuleObj->validateCartRuleForMessages($id_product, $validateProducts))) {
                            self::$_validRules[$key][] = $quantityDiscountRuleObj->id_quantity_discount_rule;
                        }
                    }
                }
            }
        }

        if (isset(self::$_validRules[$key])) {
            self::$_validRules[$key] = array_reverse(self::$_validRules[$key]);
            foreach (self::$_validRules[$key] as $validRule) {
                $quantityDiscountRuleObj = new QuantityDiscountRule((int)$validRule);
                $messages = $quantityDiscountRuleObj->getMessagesToDisplay($hookName, $id_product);
                if ($messages && array_filter($messages)) {
                    foreach ($messages as $message) {
                        $message = new QuantityDiscountRuleMessage((int)$message['id_quantity_discount_rule_message'], (int)$this->context->language->id);
                        $html .= $message->message;
                    }
                }
            }
        }

        return $html;
    }

    public function getWarnings($getAll = true)
    {
        $warning = array();

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            if (Configuration::get('PS_DISABLE_NON_NATIVE_MODULE')) {
                $warning[] = $this->l('You have to enable non PrestaShop modules at ADVANCED PARAMETERS - PERFORMANCE');
            }

            if (Configuration::get('PS_DISABLE_OVERRIDES')) {
                $warning[] = $this->l('You have to enable overrides at ADVANCED PARAMETERS - PERFORMANCE');
            }
        }

        if (count($warning) && !$getAll) {
            return $warning[0];
        }

        return $warning;
    }

    public function setFilters()
    {
        if (Tools::isSubmit('submitFilterquantity_discount_rule_cart') && (int)Tools::getValue('submitFilterquantity_discount_rule_cart') > 0) {
            $this->_filters = array(
                'filter_cr.id_cart_rule' => (string)Tools::getValue('quantity_discount_rule_cartFilter_id_cart_rule'),
                'filter_qdcr.id_cart' => (string)Tools::getValue('quantity_discount_rule_cartFilter_id_cart'),
                'filter_qdrc.id_quantity_discount_rule' => (string)Tools::getValue('quantity_discount_rule_cartFilter_id_quantity_discount_rule'),
                'filter_qdrl.name' => (string)Tools::getValue('quantity_discount_rule_cartFilter_name'),
                'filter_o.id_order' => (string)Tools::getValue('quantity_discount_rule_cartFilter_id_order'),
                'filter_c.id_customer' => (string)Tools::getValue('quantity_discount_rule_cartFilter_id_customer'),
            );
        }
    }

    public function getQuantityDiscountRules()
    {
        $_filters = '';
        $this->setFilters();
        if (Tools::isSubmit('submitFilterquantity_discount_rule_cart') && (int)Tools::getValue('submitFilterquantity_discount_rule_cart') > 0) {
            if (!empty($this->_filters)) {
                foreach ($this->_filters as $_field => $_value) {
                    if ($_value != '') {
                        $_filters .= ' AND '.str_replace('filter_', '', $_field).' LIKE "%'.pSQL($_value).'%"';
                    }
                }
            }
        }

        $sql = 'SELECT cr.`id_cart_rule`, qdrc.`id_cart`, qdrc.`id_quantity_discount_rule`, qdrl.`name`, o.`id_order`, c.`id_customer`
                FROM `'._DB_PREFIX_ .'quantity_discount_rule_cart` qdrc
                LEFT JOIN `'._DB_PREFIX_ .'cart_rule` cr ON (qdrc.`id_cart_rule` = cr.`id_cart_rule`)
                LEFT JOIN `'._DB_PREFIX_ .'quantity_discount_rule` qdr ON (qdrc.`id_quantity_discount_rule` = qdr.`id_quantity_discount_rule`)
                LEFT JOIN `'._DB_PREFIX_ .'quantity_discount_rule_lang` qdrl ON (qdr.`id_quantity_discount_rule` = qdrl.`id_quantity_discount_rule`)
                LEFT JOIN `'._DB_PREFIX_ .'orders` o ON (qdrc.id_cart = o.id_cart)
                LEFT JOIN `'._DB_PREFIX_ .'cart` c ON (qdrc.id_cart = c.id_cart)
                WHERE qdrl.`id_lang` = '.(int)$this->context->language->id.
                    $_filters.
                    (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP) ? '' : ' AND qdr.`id_shop` = '.(int)Context::getContext()->shop->id)
                .' ORDER BY cr.`id_cart_rule` DESC;';

        $quantityDiscountRules =  Db::getInstance()->executeS($sql);

        $fields_list = array(
            'id_cart_rule' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs', 'callback' => 'getCartRuleLink'),
            'id_cart' => array('title' => $this->l('Cart'), 'align' => 'center', 'class' => 'fixed-width-xs', 'callback' => 'getCartLink'),
            'id_order' => array('title' => $this->l('Order'), 'align' => 'center', 'class' => 'fixed-width-xs', 'callback' => 'getOrderLink'),
            'id_customer' => array('title' => $this->l('Customer'), 'align' => 'center', 'callback' => 'getCustomerLink'),
            'name' => array('title' => $this->l('Rule name')),
        );

        $helper_list = new HelperList();
        $helper_list->module = $this;
        $helper_list->title = $this->l('Quantity discount rules generated');
        $helper_list->shopLinkType = '';
        $helper_list->no_link = true;
        $helper_list->show_toolbar = true;
        $helper_list->simple_header = false;
        $helper_list->identifier = 'id_quantity_discount_rule';
        if (version_compare(_PS_VERSION_, '1.7.6.1', '>=')) {
            $helper_list->table = 'quantity_discount_rule';
        } else {
            $helper_list->table = 'quantity_discount_rule_cart';
        }
        $helper_list->token = Tools::getAdminTokenLite('AdminQuantityDiscountRules');
        $helper_list->currentIndex = AdminController::$currentIndex;

        $helper_list->tpl_vars['icon'] = 'icon-money';
        // This is needed for displayEnableLink to avoid code duplication
        $this->_helperlist = $helper_list;
        $helper_list->listTotal = count($quantityDiscountRules);

        if (version_compare(_PS_VERSION_, '1.6.0.14', '>')) {
            $helper_list->_default_pagination = $this->_default_pagination;
            $helper_list->_pagination = array(10, 50, 100);
        }

        /* Paginate the result */
        $page = ($page = Tools::getValue('submitFilter'.$helper_list->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper_list->table.'_pagination')) ? $pagination : $this->_default_pagination;
        $quantityDiscountRules = $this->paginate($quantityDiscountRules, $page, $pagination);

        return $helper_list->generateList($quantityDiscountRules, $fields_list);
    }

    public function paginate($array_elements, $page = 1, $pagination = 5)
    {
        if (count($array_elements) > $pagination) {
            $array_elements = array_slice($array_elements, $pagination * ($page - 1), $pagination);
        }

        return $array_elements;
    }
}
