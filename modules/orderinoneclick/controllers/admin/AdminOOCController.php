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

include_once(dirname(__FILE__).'/../../models/OOCOrderModel.php');
include_once(dirname(__FILE__).'/../../models/OOCGroupModel.php');
include_once(dirname(__FILE__).'/../../models/OOCCartModel.php');
include_once(dirname(__FILE__).'/../../models/OOCTypeOrderFieldsModel.php');

class AdminOOCController extends ModuleAdminController
{
    public $id_tab_selected = 'BasicInfo';

    const OOC_CONTROLLER = 'AdminOOC';
    const OOC_ORDER_FIELDS_CONTROLLER = 'AdminOOCOrderFields';
    const ADMIN_MODULES = 'AdminModules';

    public function __construct()
    {
        $this->table = 'sm_ooc_order';
        $this->className = 'OOCOrderModel';
        $this->bootstrap = true;
        $this->lang = false;
        $this->_use_found_rows = false;

        parent::__construct();

        $this->available_tabs = array(
            'BasicInfo' => $this->l('Basic Info'),
            'ViewCustomerHistory' => $this->l('Customer History'),
            'CreateOrder' => $this->l('Create Order'),
        );

        $this->fields_list['id_sm_ooc_order'] = array(
            'title' => $this->l('ID Order'),
            'align' => 'center',
            'search' => false,
            'width' => 25,
        );
        $this->fields_list['id_guest'] = array(
            'title' => $this->l('ID Guest'),
            'align' => 'center',
            'filter_type' => 'int',
        );
        $this->fields_list['id_customer'] = array(
            'title' => $this->l('Customer'),
            'type' => 'select',
            'align' => 'center',
            'filter_type' => 'int',
            'filter_key' => 'a!id_customer',
            'list' => $this->getCustomersArray(),
            'width' => 150,
            'callback' => 'displayCustomer',
        );
        $this->fields_list['ooc_group'] = array(
            'title' => $this->l('Group Orders'),
            'type' => 'select',
            'align' => 'center',
            'filter_type' => 'int',
            'filter_key' => 'g!id_sm_ooc_group',
            'order_key' => 'g!name',
            'list' => OOCGroupModel::getGroupsArray(),
            'width' => 150,
        );
        $this->fields_list['date'] = array(
            'title' => $this->l('Date'),
            'align' => 'center',
            'orderby' => true,
            'type' => 'datetime',
        );
        $this->fields_list['id_sm_ooc_cart'] = array(
            'title' => $this->l('Price'),
            'width' => 'auto',
            'callback' => 'displayCartPrice',
            'callback_object' => 'OOCToolsClass',
            'align' => 'center',
            'search' => false,
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );
    }

    public function renderList()
    {
        $this->toolbar_title = $this->l('List Quick Orders');
        $this->_select .= 'g.name as ooc_group';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'sm_ooc_group` g ON (a.`id_sm_ooc_group` = g.`id_sm_ooc_group`)';
        $this->_where .= Shop::AddSqlRestriction(false, 'a');

        $this->addRowAction('vieworder');
        $this->addRowAction('delete');

        if (Shop::isFeatureActive() && (Shop::getContext() != Shop::CONTEXT_SHOP)) {
            $this->fields_list['id_shop'] = array(
                'title' => $this->l('Shop'),
                'width' => 100,
                'search' => false,
                'align' => 'center',
                'callback' => 'getShopName',
                'callback_object' => 'OOCToolsClass',
            );
        }

        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function displayViewOrderLink($token = null, $id = null, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
        if (!array_key_exists('ViewOrder', self::$cache_lang)) {
            self::$cache_lang['ViewOrder'] = $this->l('View Order');
        }
        $name = self::$cache_lang['ViewOrder'];
        $tpl->assign(
            array(
                'href' => Dispatcher::getInstance()->createUrl(
                    self::OOC_CONTROLLER,
                    $this->context->language->id,
                    array(
                        'id_sm_ooc_order' => (int)$id,
                        'update'.$this->table => 1,
                        'token' => Tools::getAdminTokenLite(self::OOC_CONTROLLER),
                    ),
                    false
                ),
                'action' => $name,
                'id' => $id,
                'token' => $token
            )
        );
        return $tpl->fetch();
    }

    public function initContent()
    {
        if ($this->display == 'edit') {
            $tabs = array();
            foreach ($this->available_tabs as $id => $tab) {
                $tabs[$id] = array(
                    'id' => $id,
                    'selected' => $this->id_tab_selected === $id,
                    'name' => $tab
                );
            }
            $this->tpl_form_vars['tabs'] = $tabs;
        }
        parent::initContent();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/adminooccontroller.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/sm_orderinoneclick.js');
        if ($this->display == 'edit') {
            $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/basicinfo.css');
            $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/basicinfo.js');
            $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
        }
    }

    public function renderForm()
    {
        $id_sm_ooc_order = (int)Tools::getValue('id_sm_ooc_order');

        if (Tools::getValue('createrealorder')) {
            $this->createRealOrder($id_sm_ooc_order);
        }

        if ($this->display == 'add') {
            return $this->l('You can\'t make quick order manually');
        }

        if ($id_sm_ooc_order) {
            $sm_ooc_order = new OOCOrderModel($id_sm_ooc_order);
            if (Context::getContext()->shop->id != $sm_ooc_order->id_shop) {
                return $this->l('Access denided');
            }
        }

        if ($sm_ooc_order->id_sm_ooc_group != Configuration::get('SM_OOC_VIEWED_GROUP_ORDERS')) {
            $sm_ooc_order->id_sm_ooc_group = Configuration::get('SM_OOC_VIEWED_GROUP_ORDERS');
            $sm_ooc_order->save();
        }

        $this->fields_form = array('');
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $this->tpl_form_vars['default_form_language'] = $this->default_form_language;
        $this->tpl_form_vars['id_lang_default'] = Configuration::get('PS_LANG_DEFAULT');

        foreach (array_keys($this->available_tabs) as $id) {
            if (method_exists($this, 'initForm'.$id)) {
                $this->tpl_form = Tools::strtolower($id).'.tpl';
                $this->{'initForm'.$id}($this->object);
            }
        }

        return parent::renderForm();
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('List Quick Orders');
        if (!$this->display) {
            $this->page_header_toolbar_btn['switch_pcc'] = array(
                'desc' => $this->l('Show Price in Customer Currency'),
                'icon' => 'process-icon-toggle-'.
                    (Configuration::get('SM_OOC_SHOW_PRICE_IN_CUSTOMER_CURRENCY') ? 'on' : 'off'),
                'help' => $this->l('You can view orders in customer currency or in your default currency')
            );
        }

        if ($this->display === 'edit' || $this->display === 'add') {
            $this->page_header_toolbar_title = $this->l('View Quick Order');
            $back_url = $this->context->link->getAdminLink(self::OOC_CONTROLLER);
            $this->page_header_toolbar_btn['back_previous_page'] = array(
                'href' => $back_url,
                'desc' => $this->l('List Orders'),
                'icon' => 'process-icon-back'
            );
        }

        $settings_url = $this->context->link->getAdminLink(self::ADMIN_MODULES);
        $settings_url .= '&configure='.$this->module->name;
        $settings_url .= '&token='.Tools::getAdminTokenLite(self::ADMIN_MODULES);
        $this->page_header_toolbar_btn['config'] = array(
            'href' => $settings_url,
            'desc' => $this->l('Basic Settings'),
            'icon' => 'process-icon-cogs',
        );

        if ((Shop::isFeatureActive() &&
            ($this->context->shop->isDefaultShop() || Configuration::get('SM_OOC_OWN_FIELDS_SETTINGS')))
            || !Shop::isFeatureActive()
        ) {
            $settings_url = $this->context->link->getAdminLink(self::OOC_ORDER_FIELDS_CONTROLLER);
            $this->page_header_toolbar_btn['order_fields'] = array(
                'href' => $settings_url,
                'desc' => $this->l('Order Fields'),
                'icon' => 'process-icon-cogs',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initFormBasicInfo($object)
    {
        $data = $this->createTemplate($this->tpl_form);
        $data->assign('ooc_order', $object);
        $data->assign('id_lang', $this->context->language->id);
        $data->assign('sm_upload_dir', __PS_BASE_URI__.'modules/orderinoneclick/files/');

        if ($object->id_customer != 0) {
            $customer = new Customer($object->id_customer);
            $data->assign('ooc_customer', $customer);
        } else {
            $data->assign('ooc_customer', null);
        }

        $this->tpl_form_vars['form_action'] = $this->context->link->getAdminLink(self::OOC_CONTROLLER)
            .'&id_sm_ooc_order='.$object->id_sm_ooc_order
            .'&update'.$this->table;

        $this->tpl_form_vars['tabs']['BasicInfo']['form_content_html'] = $data->fetch();
    }

    public function initFormCreateOrder($object)
    {
        $data = $this->createTemplate($this->tpl_form);
        $data->assign('ooc_order', $object);
        $data->assign('id_lang', $this->context->language->id);
        $this->tpl_form_vars['tabs']['CreateOrder']['form_content_html'] = $data->fetch();
    }

    public function initFormViewCustomerHistory($object)
    {
        $data = $this->createTemplate($this->tpl_form);

        $ooc_emails = $object->getEmailsFromOrder();

        if (!empty($ooc_emails)) {
            $ooc_maybe_orders = OOCOrderModel::getObjectsByEmail($ooc_emails, $object->id);
            $data->assign('ooc_maybe_orders', $ooc_maybe_orders);
            $emails = '';
            foreach ($ooc_emails as $em) {
                $emails .= $em.';';
            }
            $emails = Tools::substr($emails, 0, -1);
            $data->assign('emails', $emails);
        } else {
            $data->assign('ooc_maybe_orders', array());
        }

        $data->assign(
            'ooc_order_view_action',
            $this->context->link->getAdminLink(self::OOC_CONTROLLER)
                .'&update'.$this->table
        );

        if ($object->id_customer != 0) {
            $customer = new Customer($object->id_customer);
            $data->assign('ooc_customer', $customer);
            $ooc_orders = OOCOrderModel::getObjectsByIDCustomer($object->id_customer, $object->id);
            $data->assign('ooc_orders', $ooc_orders);
        } else {
            $data->assign('ooc_customer', null);
        }

        $this->tpl_form_vars['tabs']['ViewCustomerHistory']['form_content_html'] = $data->fetch();
    }

    protected function processBulkDelete()
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $oc_order = new OOCOrderModel((int)$id);
                $result = $result && $oc_order->delete();
            }
        }

        return $result && parent::processBulkDelete();
    }

    /**
     * AJAX
     */
    public function ajaxProcessSetPriceInCustomerCurrency()
    {
        $pcc = (int)Tools::getValue('SHOW_PRICE_IN_CUSTOMER_CURRENCY') ? true : false;
        Configuration::updateValue('SM_OOC_SHOW_PRICE_IN_CUSTOMER_CURRENCY', $pcc);
    }

    public function ajaxProcessFindCustomer()
    {
        $query = pSQL(Tools::getValue('q'));
        $customers = Customer::searchByName($query);

        $result = null;
        foreach ($customers as $customer) {
            $result .= $customer['firstname'].' '.$customer['lastname'].' ('.$customer['email'].') |'
                . $customer['id_customer']."\n";
        }

        die($result);
    }

    public function ajaxProcessCreateANewCustomer()
    {
        $errors = array();

        $firstname = Tools::truncate(pSQL(Tools::getValue('firstname')), 100, null);
        $lastname = Tools::truncate(pSQL(Tools::getValue('lastname')), 100, null);
        $email = Tools::truncate(pSQL(Tools::getValue('email')), 100, null);
        $password = Tools::truncate(pSQL(Tools::getValue('password')), 100, null);

        $errors[] = empty($firstname) ? $this->l('Firstname is required') : null;
        $errors[] = empty($lastname) ? $this->l('Lastname is required') : null;
        $errors[] = empty($email) ? $this->l('Email is required') : null;

        $errors[] = !Validate::isName($firstname) ? $this->l('Firstname is invalid') : null;
        $errors[] = !Validate::isName($lastname) ? $this->l('Lastname is invalid') : null;
        $errors[] = (!Validate::isEmail($email) && !empty($email)) ? $this->l('Email is invalid') : null;
        $errors[] = (!Validate::isPasswd($password) && !empty($password)) ? $this->l('Password is invalid') : null;

        if (Customer::customerExists($email)) {
            $json = array(
                'status' => 'error',
                'message' => $this->l('Customer with this email already present in store'),
            );
            die(Tools::jsonEncode($json));
        }

        $errors = array_unique($errors);

        if ($errors[0] == null && count($errors) == 1) {
            $customer = new Customer();
            $customer->is_guest = true;
            $customer->firstname = $firstname;
            $customer->lastname = $lastname;
            $customer->email = $email;
            $customer->transformToCustomer(
                Configuration::get('PS_LANG_DEFAULT'),
                (empty($password) ? Tools::passwdGen(8, 'RANDOM') : $password)
            );
            $result = $customer->add();

            if ($result) {
                $id_sm_ooc_order = (int)Tools::getValue('id_sm_ooc_order');
                if ($id_sm_ooc_order) {
                    $sm_ooc_order = new OOCOrderModel($id_sm_ooc_order);
                    $sm_ooc_order->id_customer = $customer->id;
                    $sm_ooc_order->id_guest = 0;
                    $result = $sm_ooc_order->save();
                    if ($result) {
                        $json = array(
                            'status' => 'ok',
                        );
                    } else {
                        $json = array(
                            'status' => 'error',
                            'message' => $this->l('Can\'t assign customer to order'),
                        );
                    }
                } else {
                    $json = array(
                        'status' => 'error',
                        'message' => $this->l('Can\'t assign customer to order (empty of ID order)'),
                    );
                }
            } else {
                $json = array(
                    'status' => 'error',
                    'message' => $this->l('Error create a customer'),
                );
            }
        } else {
            $json = array(
                'status' => 'error',
                'message' => $this->l('Error create a customer'),
            );
        }

        die(Tools::jsonEncode($json));
    }

    public function ajaxProcessAssignOrderToCustomer()
    {
        $id_sm_ooc_order = (int)Tools::getValue('id_sm_ooc_order');
        $id_new_customer = (int)Tools::getValue('id_customer');

        $result = false;

        if ($id_new_customer && $id_sm_ooc_order) {
            $sm_ooc_order = new OOCOrderModel($id_sm_ooc_order);
            $sm_ooc_order->id_customer = $id_new_customer;
            $sm_ooc_order->id_guest = 0;

            if (Context::getContext()->shop->id == $sm_ooc_order->id_shop) {
                $result = $sm_ooc_order->save();
                if ($result) {
                    $json = array(
                        'status' => 'ok',
                    );
                } else {
                    $json = array(
                        'status' => 'error',
                        'message' => $this->l('Error to assign'),
                    );
                }
            } else {
                $json = array(
                    'status' => 'error',
                    'message' => $this->l('Permission denided'),
                );
            }
        } else {
            $json = array(
                'status' => 'error',
                'message' => $this->l('Select customer first'),
            );
        }

        die(Tools::jsonEncode($json));
    }

    /**
     * TOOLS
     */
    public function displayCustomer($id_customer = 0)
    {
        if ($id_customer == 0) {
            return;
        }
        $customer_obj = new Customer((int)$id_customer);
        $customer = $customer_obj->firstname.' '.$customer_obj->lastname.' ('.$customer_obj->email.')';
        return $customer;
    }

    private function getCustomersArray()
    {
        $customers = Customer::getCustomers();
        $customers_array = array();
        foreach ($customers as $customer) {
            $id_customer = $customer['id_customer'];
            $customers_array[$id_customer] = $this->displayCustomer($id_customer);
        }
        return $customers_array;
    }

    public function createRealOrder($id_sm_ooc_order = null)
    {
        if (!$id_sm_ooc_order) {
            return;
        }

        $sm_ooc_order = new OOCOrderModel($id_sm_ooc_order);

        if (Context::getContext()->shop->id != $sm_ooc_order->id_shop) {
            return $this->l('Permission denided');
        }

        $cart = new Cart();
        $cart->id_customer = $sm_ooc_order->id_customer;
        $cart->id_shop = $sm_ooc_order->id_shop;
        $cart->id_currency = $sm_ooc_order->ooc_cart->id_currency;
        $cart->add();

        foreach ($sm_ooc_order->ooc_cart->products as $product) {
            $cart->updateQty($product->quantity, $product->id_product, $product->id_product_attribute);
        }

        if ($sm_ooc_order->ooc_cart->vouchers) {
            foreach ($sm_ooc_order->ooc_cart->vouchers as $voucher) {
                $cart->addCartRule($voucher->id_voucher);
            }
        }

        $sm_ooc_order->id_sm_ooc_group = Configuration::get('SM_OOC_ARCHIVE_GROUP_ORDERS');
        $sm_ooc_order->save();

        Tools::redirectAdmin(
            'index.php?controller=AdminOrders&token='.
            Tools::getAdminTokenLite('AdminOrders').
            '&id_cart='.
            $cart->id.
            '&addorder&'
        );
    }
}
