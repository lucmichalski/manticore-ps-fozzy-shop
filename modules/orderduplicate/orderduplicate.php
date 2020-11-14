<?php
/**
 * OrderDuplicate
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2017 silbersaiten
 * @version   1.1.3
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

class OrderDuplicate extends Module
{
    private $list_hooks = array(
        'displayBackOfficeHeader',
        'displayAdminOrder'
    );
    public $vt = 't17';

    public function __construct()
    {
        $this->name = 'orderduplicate';
        $this->version = '1.1.3';
        $this->tab = 'administration';
        $this->author = 'Silbersaiten';
        $this->module_key = '8f876e5e232b729aceb04072a270f6ab';

        if (version_compare('1.7.0.0', _PS_VERSION_, '>')) {
            $this->vt = 't16';
        }

        parent::__construct();

        $this->displayName = $this->l('Order Duplicator');
        $this->description = $this->l('Clone existing orders');
    }

    public function install()
    {
        if (parent::install()) {
            foreach ($this->list_hooks as $hook) {
                $this->registerHook($hook);
            }

            return true;
        }
        return false;
    }

    public function getContent()
    {
        if (Tools::getIsset('ajax')) {
            $this->ajaxCall();
        }

        Tools::redirectAdmin(
            AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules')
        );
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        unset($params);
        if ($this->context->controller instanceof AdminOrdersController) {
            $this->context->controller->addCSS($this->_path.'views/css/style.css');
            $this->context->controller->addJquery();
            $this->context->controller->addJs($this->_path.'views/js/order12.js');
            $this->context->controller->addJqueryPlugin('autocomplete');

            return '
            <script type="text/javascript">
            var duplicator_path = "'.$this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&ajax=true",
                orderduplicateTranslation = {
                    "Duplicate": "'.$this->l('Сервис').'"
                }
            </script>';
        }
    }

    public function hookDisplayAdminOrder($params)
    {

    }

    public function ajaxCall()
    {
        $action = Tools::getValue('action');

        switch ($action) {
            case 'duplicateGetInfo':
                $id_order = Tools::getValue('id_order');
                $check = true;

                $order = new Order((int)$id_order);
                $products = $order->getProductsDetail();
                $customer = new Customer((int)$order->id_customer);
                $address_delivery = new Address((int)$order->id_address_delivery);
                //$address_invoice = new Address((int)$order->id_address_delivery);
                $formatted_addresse = AddressFormat::generateAddress(
                                $address_delivery,
                                array(),
                                '<br />'
                            );
                
                //$payment_modules = Module::getPaymentModules();
                $payment_modules = PaymentModule::getInstalledPaymentModules();
                foreach ($payment_modules as &$pm) {
                    $obj = Module::getInstanceById($pm['id_module']);
                    $pm['displayName'] = $obj->displayName;
                }
                $order_payment_module = Module::getInstanceByName($order->module);

                $this->context->smarty->assign(array(
                    'order' => $order,
                    'prod' => $check,
                    'products' => $products,
                    'customer' => $customer,
                    'address_delivery' => $address_delivery,
                    'address_invoice' => $address_delivery,
                    'address' => $formatted_addresse,
                    'states' => OrderState::getOrderStates($this->context->language->id),
                    'methods' => $payment_modules,
                    'selected_method' => $order_payment_module->id
                ));

                die($this->display(__FILE__, 'fancybox.tpl'));
                //break;
            case 'getCustomerList':
                $query = Tools::getValue('q', false);

                $result = Db::getInstance()->executeS(
                    'SELECT
                    `firstname`,
                    `lastname`,
                    `id_customer`
                    FROM
                    `'._DB_PREFIX_.'customer`
                    WHERE
                    `active` = 1
                    AND `firstname` LIKE \'%'.pSQL($query).'%\' OR `lastname` LIKE \'%'.pSQL($query).'%\''
                );

                if ($result) {
                    die(Tools::jsonEncode($result));
                }

                die(Tools::jsonEncode(new stdClass));
                //break;
            case 'getAddressList':
                $id_customer = Tools::getValue('id_customer');
                $customer = new Customer((int)$id_customer);

                if (Validate::isLoadedObject($customer)) {
                    $addresses = $customer->getAddresses($this->context->language->id);

                    if (count($addresses)) {
                        $formatted_addresses = array();

                        foreach ($addresses as $address) {
                            $formatted_addresses[$address['id_address']] = AddressFormat::generateAddress(
                                new Address($address['id_address']),
                                array(),
                                '<br />'
                            );
                        }

                        $this->context->smarty->assign(array(
                            'id_address_delivery_selected' => Tools::getValue('id_address_delivery_selected'),
                            'id_address_invoice_selected' => Tools::getValue('id_address_invoice_selected'),
                            'addresses' => $formatted_addresses
                        ));
                    }
                }

                die($this->display(__FILE__, $this->vt.'_addresses.tpl'));
                //break;
            case 'cloneOrder':
                $id_customer = Tools::getValue('id_customer');
                $id_address_delivery = Tools::getValue('id_address_delivery');
                $id_address_invoice = Tools::getValue('id_address_invoice');
                $id_order = Tools::getValue('id_order');
                $id_order_state = Tools::getValue('id_order_state');
                $id_payment_method = Tools::getValue('id_payment_method');
                $id_order_type = Tools::getValue('id_order_type');
                $ids_products = Tools::getValue('ids_products');
                $products_to_copy = implode(",",$ids_products);

                $order = new Order((int)$id_order);
                
                if ($id_payment_method != 0) {
                    $payment_module = Module::getInstanceById((int)$id_payment_method);    
                } else {
                    $payment_module = Module::getInstanceByName($order->module);
                }
                if (Validate::isLoadedObject($order)) {
                    if (Validate::isLoadedObject($payment_module)
                        && $cart = $this->cloneCart(
                            $order->id_cart,
                            $id_customer,
                            $id_address_delivery,
                            $id_address_invoice
                        )
                    ) {
                        $this->context->cart = $cart;
                                                  
                        try {
                            if ($payment_module->validateOrder(
                                $cart->id,
                                (int)$id_order_state,
                                $order->total_paid_tax_incl,
                                ((int)$id_payment_method != 0 ? $payment_module->displayName : $order->payment)
                            )) {
                                $neworder = new Order((int)$payment_module->currentOrder);
                               // $neworder->zone = $order->zone;
                               // $neworder->zone_name = $order->zone_name;
                                
                                $neworder->order_type = $id_order_type;
                                $neworder->order_from = (int)$id_order;
                                
                                $sql_del_cartrules = "DELETE FROM `"._DB_PREFIX_."order_cart_rule` WHERE `id_order` = ".(int)$payment_module->currentOrder;
                                Db::getInstance()->execute($sql_del_cartrules);
                                $sql_del_cartrules_order = "UPDATE `"._DB_PREFIX_."orders` SET `total_discounts` = 0, `total_discounts_tax_incl` = 0, `total_discounts_tax_excl` = 0 WHERE `id_order` = ".(int)$payment_module->currentOrder;
                                $del_cartrules_order = Db::getInstance()->execute($sql_del_cartrules_order);
                                
                                $rule_name = array();
                                $rule_name[1] = 'Бесплатная доставка сервисного заказа';
                                $rule_name[2] = 'Безкоштовна доставка сервісного замовлення';
                                $cart_rule = new CartRule();
                            		$cart_rule->code = 'Service_'.(int)$payment_module->currentOrder;
                            		$cart_rule->name = $rule_name;
                            		$cart_rule->id_customer = (int)$id_customer;
                            		$cart_rule->free_shipping = true;
                            		$cart_rule->quantity = 1;
                            		$cart_rule->quantity_per_user = 1;
                            		$cart_rule->minimum_amount_currency = (int)$this->context->cart->id_currency;
                            		$cart_rule->reduction_currency = (int)$this->context->cart->id_currency;
                            		$cart_rule->date_from = date('Y-m-d H:i:s', time());
                            		$cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
                            		$cart_rule->active = 1;
                            		$cart_rule->add();
                                
                                $values = array();
                                $values['tax_incl']=$neworder->total_shipping_tax_incl;
                                $values['tax_excl']=$neworder->total_shipping_tax_excl;
                                
                                $neworder->addCartRule($cart_rule->id, $rule_name[1], $values, 0, 1);
                                
                                $neworder->update();
                                $sql_del = "DELETE FROM `"._DB_PREFIX_."order_detail` WHERE `id_order` = ".(int)$payment_module->currentOrder;
                                $sql_insert = "INSERT INTO `"._DB_PREFIX_."order_detail` (`id_order`, `id_order_invoice`, `id_warehouse`, `id_shop`, `product_id`, `product_attribute_id`, `id_customization`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, `product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, `reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_isbn`, `product_upc`, `product_reference`, `product_supplier_reference`, `product_weight`, `id_tax_rules_group`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, `total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `loginmodified`, `replacement`, `who_repl`, `realqty`, `sorder`, `cartnum`, `datemodified`, `special`) SELECT ".(int)$payment_module->currentOrder.", null, `id_warehouse`, `id_shop`, `product_id`, `product_attribute_id`, `id_customization`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, `product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, `reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_isbn`, `product_upc`, `product_reference`, `product_supplier_reference`, `product_weight`, `id_tax_rules_group`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, `total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `loginmodified`, `replacement`, `who_repl`, null, `sorder`, `cartnum`, `datemodified`, `special` FROM `"._DB_PREFIX_."order_detail` WHERE `id_order` =".(int)$id_order." AND `id_order_detail` IN (".$products_to_copy.")";
                                Db::getInstance()->execute($sql_del);
                                Db::getInstance()->execute($sql_insert);
                                
                                //Пересчет заказа
                                $sql_get_sum = "SELECT ROUND(SUM(`product_quantity`*`unit_price_tax_incl`),2) AS itog FROM `"._DB_PREFIX_."order_detail` WHERE `id_order` = ".(int)$payment_module->currentOrder;
                                $order_final_sum = Db::getInstance()->getValue($sql_get_sum);
                                $sql_udate_sum_order = "UPDATE `"._DB_PREFIX_."orders` SET `zone` = '".$order->zone."', `zone_name` = '".$order->zone_name."', `total_products` = ".$order_final_sum.", `total_products_wt` = ".$order_final_sum." WHERE `id_order` = ".(int)$payment_module->currentOrder;
                                $upd_sum_order = Db::getInstance()->execute($sql_udate_sum_order);
                                $sql_udate_Ssum_order = "UPDATE `"._DB_PREFIX_."orders` SET `total_paid` = (`total_products` + `total_shipping` - `total_discounts`), `total_paid_tax_incl` = (`total_products` + `total_shipping` - `total_discounts`), `total_paid_tax_excl` = (`total_products` + `total_shipping` - `total_discounts`), `total_paid_real` = (`total_products` + `total_shipping` - `total_discounts`) WHERE `id_order` = ".(int)$payment_module->currentOrder;
                                $upd_Ssum_order = Db::getInstance()->execute($sql_udate_Ssum_order);
                                
                                /* $discs = $neworder->getCartRules();  - Убрал клонирование скидкм
                                $discs = array();
                                if (count($discs)) {
                                    foreach ($discs as $disc) {
                                        $cr = new CartRule($disc['id_cart_rule']);
                                        $cr->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($neworder->date_add)));
                                        $cr->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
                                        $cr->name[Configuration::get('PS_LANG_DEFAULT')] = $disc['name'];
                                        $cr->quantity = 0;
                                        $cr->quantity_per_user = 1;
                                        $cr->active = 0;
                                        if ($res = $cr->update()) {
                                            $order_cart_rule = new OrderCartRule($disc['id_order_cart_rule']);

                                            $neworder->total_discounts += $order_cart_rule->value;
                                            $neworder->total_discounts_tax_incl += $order_cart_rule->value;
                                            $neworder->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
                                            $neworder->total_paid -= $order_cart_rule->value;
                                            $neworder->total_paid_tax_incl -= $order_cart_rule->value;
                                            $neworder->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
                                            $neworder->update();
                                        } else {
                                            break;
                                        }
                                    }
                                }   */
                                Hook::exec('actionOrderCloned', array(
                                    'order_old' => $order,
                                    'order_new' => $neworder
                                ));
                                $id_employee = (int)$this->context->employee->id;
                                $new_ord_id = (int)$payment_module->currentOrder;
                                $token = Tools::getAdminToken('AdminOrders' . (int) Tab::getIdFromClassName('AdminOrders').$id_employee);
                                $link = 'https://fozzyshop.com.ua/operations/index.php?controller=AdminOrders&id_order='.$new_ord_id.'&vieworder&token='.$token;
                                //header('Location: '.$link,true,301);
                                die(Tools::jsonEncode(array('success' => true, 'link' => $link)));
                            }
                        } catch (PrestaShopException $e) {
                            die(Tools::jsonEncode(array(
                                'error' => $this->l('Unable to clone this order').': '.$e->getMessage()
                            )));
                        }
                    }
                }

                die(Tools::jsonEncode(array('error' => $this->l('Unable to clone this order'))));
                //break;
            case 'deleteOrder':
                if ($this->deleteOrder(Tools::getValue('id_order'))) {
                    die(Tools::jsonEncode(array('success' => true)));
                }

                die(Tools::jsonEncode(array('error' => $this->l('Unable to delete this order'))));
                //break;
        }
    }
    private function debugfile ($text = ''){
      $fp = fopen("file.txt", "w");
      fwrite($fp, $text);
      fclose($fp);
    }
    private function cloneCart($id_cart, $id_customer, $id_address_delivery, $id_address_invoice)
    {
        if (Validate::isLoadedObject($cart = new Cart((int)$id_cart))) {
            $cloned = $cart;

            $cloned->id = null;
            $cloned->id_customer = (int)$id_customer;
            $cloned->id_address_delivery = (int)$id_address_delivery;
            $cloned->id_address_invoice = (int)$id_address_invoice;

            //$delivery_option = Tools::unSerialize($cloned->delivery_option);
         //   $delivery_option = array();
         //   $delivery_option[$cloned->id_address_delivery] = $cloned->id_carrier.',';
         //   $cloned->delivery_option = serialize($delivery_option);

            if ($cloned->add()) {
                $new_cart_id = $cloned->id;

                $cart_products = Db::getInstance()->ExecuteS(
                    'SELECT * FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$id_cart
                );
                
                $cart_rules = Db::getInstance()->ExecuteS(
                    'SELECT * FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart` = '.(int)$new_cart_id
                );
                
                if (count($cart_products)) {
                    foreach ($cart_products as $cart_product) {
                        $cart_product['id_cart'] = $new_cart_id;
                        $cart_product['id_address_delivery'] = (int)$id_address_delivery;

                        Db::getInstance()->insert('cart_product', $cart_product);
                    }
                }
              
              if (count($cart_rules)) {
                    foreach ($cart_rules as $cart_rule) {
                       $cloned->removeCartRule((int)$cart_rule['id_cart_rule']);
                    }
                }
              
             /*   
              $rule_name = array();
              $rule_name[1] = 'Бесплатная доставка сервисного заказа';
              $rule_name[2] = 'Безкоштовна доставка сервісного замовлення';
              $cart_rule = new CartRule();
          		$cart_rule->code = 'Service_'.$cloned->id;
          		$cart_rule->name = $rule_name;
          		$cart_rule->id_customer = (int)$id_customer;
          		$cart_rule->free_shipping = true;
          		$cart_rule->quantity = 1;
          		$cart_rule->quantity_per_user = 1;
          		$cart_rule->minimum_amount_currency = (int)$cloned->id_currency;
          		$cart_rule->reduction_currency = (int)$cloned->id_currency;
          		$cart_rule->date_from = date('Y-m-d H:i:s', time());
          		$cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
          		$cart_rule->active = 1;
          		$cart_rule->add();
              
              $cloned->addCartRule((int)$cart_rule->id);    */
              
              return $cloned;
            }
        }

        return false;
    }

    private function deleteOrderPayments(Order $order)
    {
        $invoices = $order->getInvoicesCollection()->getResults();

        if ($invoices) {
            $invoice_list = array();

            foreach ($invoices as $invoice) {
                array_push($invoice_list, (int)$invoice->id);
            }

            Db::getInstance()->delete(
                'order_invoice_payment',
                '`id_order_invoice` IN ('.(implode(',', $invoice_list)).')'
            );
        }
    }

    private function deleteOrderInvoices(Order $order)
    {
        $invoices = $order->getInvoicesCollection()->getResults();

        if ($invoices) {
            foreach ($invoices as $invoice) {
                Db::getInstance()->delete('order_invoice', '`id_order_invoice` = '.(int)$invoice->id);
                Db::getInstance()->delete('order_invoice_payment', '`id_order_invoice` = '.(int)$invoice->id);
                Db::getInstance()->delete('order_invoice_tax', '`id_order_invoice` = '.(int)$invoice->id);
            }
        }
    }

    private function deleteOrderSlips(Order $order)
    {
        $slips = $order->getDeliverySlipsCollection()->getResults();

        if ($slips) {
            foreach ($slips as $slip) {
                Db::getInstance()->delete('order_slip', '`id_order_slip` = '.(int)$slip->id);
                Db::getInstance()->delete('order_slip_detail', '`id_order_slip` = '.(int)$slip->id);
            }
        }
    }

    private function deleteOrderReturns(Order $order)
    {
        $order_returns = OrderReturn::getOrdersReturn($order->id_customer, $order->id);

        if ($order_returns) {
            foreach ($order_returns as $order_return) {
                $return_data = OrderReturn::getOrdersReturnDetail($order_return['id_order_return']);

                if ($return_data) {
                    foreach ($return_data as $return_detail) {
                        Db::getInstance()->delete(
                            'order_return_detail',
                            '`id_order_return` = '.(int)$return_detail['id_order_return']
                        );
                    }
                }

                Db::getInstance()->delete(
                    'order_return',
                    '`id_order_return` = '.(int)$return_detail['id_order_return']
                );
            }
        }
    }

    private function deleteOrderHistory(Order $order)
    {
        Db::getInstance()->delete('order_history', '`id_order` = '.(int)$order->id);
    }

    private function deleteOrderDetails(Order $order)
    {
        $order_details = Db::getInstance()->ExecuteS(
            'SELECT
            *
            FROM
            `'._DB_PREFIX_.'order_detail`
            WHERE
            `id_order` = '.(int)$order->id
        );

        if ($order_details) {
            $order_detail_ids = array();

            foreach ($order_details as $order_detail) {
                array_push($order_detail_ids, (int)$order_detail['id_order_detail']);

                if (!StockAvailable::dependsOnStock($order_detail['product_id'])) {
                    StockAvailable::updateQuantity(
                        $order_detail['product_id'],
                        $order_detail['product_attribute_id'],
                        $order_detail['product_quantity']
                    );
                }
            }

            Db::getInstance()->delete('order_detail_tax', '`id_order_detail` IN ('.implode(',', $order_detail_ids).')');
        }

        Db::getInstance()->delete('order_detail', '`id_order` = '.(int)$order->id);
    }

    private function deleteOrderCarriers(Order $order)
    {
        Db::getInstance()->delete('order_carrier', '`id_order` = '.(int)$order->id);
    }

    private function deleteOrderMessages(Order $order)
    {
        $threads = Db::getInstance()->ExecuteS(
            'SELECT
            DISTINCT(`id_customer_thread`)
            FROM
            `'._DB_PREFIX_.'customer_thread`
            WHERE
            `id_order` = '.(int)$order->id
        );

        if ($threads) {
            foreach ($threads as $thread) {
                Db::getInstance()->delete(
                    'customer_thread',
                    '`id_customer_thread` = '.(int)$thread['id_customer_thread']
                );
                Db::getInstance()->delete(
                    'customer_message',
                    '`id_customer_thread` = '.(int)$thread['id_customer_thread']
                );
            }
        }

        $messages = Db::getInstance()->ExecuteS(
            'SELECT * FROM `'._DB_PREFIX_.'message` WHERE `id_order` = '.(int)$order->id
        );

        if ($messages) {
            foreach ($messages as $message) {
                Db::getInstance()->delete('message_readed', '`id_message` = '.(int)$message['id_message']);
                Db::getInstance()->delete('message', '`id_message` = '.(int)$message['id_message']);
            }
        }
    }

    private function deleteOrderCartRules(Order $order)
    {
        $rules = $order->getCartRules();

        if ($rules) {
            foreach ($rules as $rule) {
                Db::getInstance()->delete(
                    'order_cart_rule',
                    '`id_order_cart_rule` = '.(int)$rule['id_order_cart_rule']
                );
            }
        }
    }

    public function deleteOrder($id_order)
    {
        if (Validate::isLoadedObject($order = new Order((int)$id_order))) {
            $this->deleteOrderPayments($order);
            $this->deleteOrderCarriers($order);
            $this->deleteOrderCartRules($order);
            $this->deleteOrderDetails($order);
            $this->deleteOrderHistory($order);
            $this->deleteOrderInvoices($order);
            $this->deleteOrderMessages($order);
            $this->deleteOrderReturns($order);
            $this->deleteOrderSlips($order);

            Db::getInstance()->delete('orders', '`id_order` = '.(int)$order->id);

            Hook::exec('actionOrderDelete', array('order' => $order));

            return true;
        }

        return false;
    }
}
