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

class Fozzy_orders extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fozzy_orders';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Fozzy';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FozzyOrders');
        $this->description = $this->l('Sync orders for Fozzy');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('FOZZY_ORDERS_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionOrderStatusPostUpdate') &&
            $this->registerHook('actionOrderHistoryAddAfter') &&
            $this->registerHook('actionOrderEdited') &&            
            $this->registerHook('actionOrderStatusUpdate');
    }

    public function uninstall()
    {
        Configuration::deleteByName('FOZZY_ORDERS_LIVE_MODE');
        Configuration::deleteByName('FOZZY_ORDERS_ACCOUNT_EMAIL');
        Configuration::deleteByName('FOZZY_ORDERS_ACCOUNT_PASSWORD');

        return parent::uninstall();
    }

    public function getContent()
    {
        $html = '';
        
        if (((bool)Tools::isSubmit('submitFozzy_ordersModule')) == true) {
            $this->postProcess();
        }

        if (Tools::isSubmit('UpdateStatus'))
		      {
           $html .= $this->displayConfirmation($this->putStatuses());
          } 
        if (Tools::isSubmit('UpdateCarriers'))
		      {
           $html .= $this->displayConfirmation($this->putDelivery());
          }
        if (Tools::isSubmit('UpdatePayments'))
		      {
          $html .= $this->displayConfirmation($this->putPayment());
          }
        if (Tools::isSubmit('FZStatus'))
		      {
           $this->showStatuses();
          } 
        if (Tools::isSubmit('FZCarriers'))
		      {
           $this->showDelivery();
          }
        if (Tools::isSubmit('FZPayments'))
		      {
           $this->showPayment();
          }
        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $output = '';
        return $html.$this->renderForm().$output;
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
        $helper->submit_action = 'submitFozzy_ordersModule';
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
                        'label' => $this->l('Работа на проде'),
                        'name' => 'FOZZY_ORDERS_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Режим работы на продуктивном сервере'),
                        'values' => array(
                            array(
                                'id' => 'FOZZY_ORDERS_LIVE_MODE_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'FOZZY_ORDERS_LIVE_MODE_off',
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
                        'name' => 'FOZZY_ORDERS_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'text',
                        'col' => 3,
                        'prefix' => '<i class="icon icon-key"></i>',
                        'name' => 'FOZZY_ORDERS_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'buttons' => array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Обновить статусы'),
                    'icon' => 'process-icon-update',
                    'name' => 'UpdateStatus',
                    'id'   => 'UpdateStatus',
                    'class'=> 'pull-left'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Обновить перевозчиков'),
                    'icon' => 'process-icon-update',
                    'name' => 'UpdateCarriers',
                    'id'   => 'UpdateCarriers',
                    'class'=> 'pull-left'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Обновить методы оплат'),
                    'icon' => 'process-icon-update',
                    'name' => 'UpdatePayments',
                    'id'   => 'UpdatePayments',
                    'class'=> 'pull-left'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Показать статусы FZ'),
                    'icon' => 'process-icon-download',
                    'name' => 'FZStatus',
                    'id'   => 'FZStatus',
                    'class'=> 'pull-right'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Показать перевозчиков FZ'),
                    'icon' => 'process-icon-download',
                    'name' => 'FZCarriers',
                    'id'   => 'FZCarriers',
                    'class'=> 'pull-right'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Показать методы оплат FZ'),
                    'icon' => 'process-icon-download',
                    'name' => 'FZPayments',
                    'id'   => 'FZPayments',
                    'class'=> 'pull-right'
                )
            ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
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
            'FOZZY_ORDERS_LIVE_MODE' => Configuration::get('FOZZY_ORDERS_LIVE_MODE'),
            'FOZZY_ORDERS_ACCOUNT_EMAIL' => Configuration::get('FOZZY_ORDERS_ACCOUNT_EMAIL'),
            'FOZZY_ORDERS_ACCOUNT_PASSWORD' => Configuration::get('FOZZY_ORDERS_ACCOUNT_PASSWORD'),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }
    
    public function outOrder($id_order = 1)              //Функция для проверки, выдает текущее состояние заказа чистым XML
    {
       $order = new Order($id_order);    
       $id_shop = $order->id_shop;
       $id_status = $order->current_state;
       $products = $order->getProducts();
       array_multisort(array_column($products, 'sorder'), SORT_ASC, $products); //Сортируем товары по порядку
       $sql_log = "SELECT c.`mest`, s.`INN` as sborshik, v.`INN` as vodila, c.`Route_Num`, c.`cartnum`, `norm`, `ice`, `fresh`, `hot`  FROM `"._DB_PREFIX_."orders` c LEFT JOIN `"._DB_PREFIX_."fozzy_logistic_sborshik` s ON c.`id_sborshik`=s.`id_sborshik` LEFT JOIN `"._DB_PREFIX_."fozzy_logistic_vodila` v ON c.`id_vodila`=v.`id_vodila` WHERE `id_order` = ".$id_order;
       $log = Db::getInstance()->executeS($sql_log);

       $sql_d = "SELECT c.`dateofdelivery`, d.`timefrom`, d.`timeto` FROM `"._DB_PREFIX_."orders` c LEFT JOIN `"._DB_PREFIX_."nove_dateofdelivery` d ON c.`period`=d.`id_period` WHERE c.`id_order` = ".$id_order;
       $d = Db::getInstance()->executeS($sql_d);
       
        switch ($id_shop) {
            case 1:
                $filial_id = 1614;
                break;
            case 2:
                $filial_id = 322;
                break;
            case 3:
                $filial_id = 1674;
                break;
            case 4:
                $filial_id = 510;
                break;
            case 8:
                $filial_id = 382;
                break;
            case 9:
                $filial_id = 1292;
                break;    
            default:
                $filial_id = 1614;
                break;
        }
      
       $id_address = $order->id_address_delivery;
       $id_customer = $order->id_customer;
       $customer = new Customer($id_customer);
       $address = new Address($id_address);
       $customer_fio = $customer->lastname." ".$customer->firstname;
       $customer_ad = explode("|",$address->address2);
       $customer_address = $customer_ad[0].", ".$customer_ad[1].", кв/оф. ".$customer_ad[2];
       $customer_phone = $address->phone_mobile;
        
       $delivery_reference = 0;
       
       $ship_cost = $order->total_shipping; //стоимость доставки
       $carrier = $order->id_carrier; //перевозчик
       $courier_array = array();
       $courier_array = array(27,33,30,42,55,57);
        
       $discounts = $order->getCartRules();

        if ($discounts) {
            
          foreach ($discounts as $cart_rule)
            {
              if ((int)$cart_rule['free_shipping'] == 1) $ship_cost = 0;
            }
          
        }
        
        
        if ( $ship_cost > 0 && in_array($carrier, $courier_array) ) $shipprint=$ship_cost;  
        
        else $shipprint=0;
        if ($shipprint > 0) $delivery_reference = '600757';
       
       $code128 = '1167615'."_".$id_order."_".$delivery_reference;
        if ($log[0]['norm'] == 1) $logistika .= 'Сухая | ';
        if ($log[0]['ice'] == 1) $logistika .= 'Заморозка | ';
        if ($log[0]['fresh'] == 1) $logistika .= 'Охлажденка | ';
        if ($log[0]['hot'] == 1) $logistika .= 'Горячее | ';
        $logistika = substr($logistika,0,-3);
        
      $mn = Module::getModuleIdByName($order->module);
      
       $OrderData_xml_1 = new SimpleXMLElement('<OrderData xmlns:i="http://www.w3.org/2001/XMLSchema-instance"></OrderData>');
       $order_xml = $OrderData_xml_1->addChild('order');
       $forder_xml = $order_xml->addChild('FzShopOrder');
       $forder_xml->addChild('clientFullName', $customer_fio); 
       $forder_xml->addChild('clientMobilePhone', $customer_phone); 
       $forder_xml->addChild('dateModified', date("d.m.Y H:i:s", strtotime($order->date_upd)));
       $forder_xml->addChild('deliveryAddress', $customer_address); 
       $forder_xml->addChild('deliveryDate', date("d.m.Y H:i:s", strtotime($d[0]['dateofdelivery'])));
       $forder_xml->addChild('deliveryId', $carrier);                 
       $forder_xml->addChild('deliveryTimeFrom', $d[0]['timefrom']);
       $forder_xml->addChild('deliveryTimeTo', $d[0]['timeto']);
       $forder_xml->addChild('driverId', $log[0]['vodila']);
       $forder_xml->addChild('filialId', $filial_id);
       $forder_xml->addChild('globalUserId', $log[0]['sborshik']);
       $forder_xml->addChild('lastContainerBarcode', !$log[0]['cartnum']? '0' : $log[0]['cartnum']);
       $forder_xml->addChild('logisticsType', $logistika);       
       $forder_xml->addChild('orderBarcode', $code128);
       $forder_xml->addChild('orderCreated', date("d.m.Y H:i:s", strtotime($order->date_add)));
       $forder_xml->addChild('orderId', $id_order);
       $forder_xml->addChild('orderStatus', $id_status);
       $forder_xml->addChild('paymentId', $mn);
       $forder_xml->addChild('placesCount', $log[0]['mest']);
       $forder_xml->addChild('priority', 1);
       $forder_xml->addChild('remark', 'Reserved for remarks');
       $orderline_xml = $OrderData_xml_1->addChild('orderLines');
       
       foreach ($products as $product) {
       $forderline_xml = $orderline_xml->addChild('FzShopOrderLines');
       $forderline_xml->addChild('containerBarcode', !$product['cartnum'] ? '0' : $product['cartnum'] );
       $forderline_xml->addChild('dateModified', date("d.m.Y H:i:s", strtotime($product['datemodified'])));
       $forderline_xml->addChild('globalUserId', !$product['loginmodified'] ? '0' : $product['loginmodified']);  
       $forderline_xml->addChild('lagerId', $product['product_reference']);
       $forderline_xml->addChild('orderId', $id_order);
       if ( $product['product_quantity'] == 0) $forderline_xml->addChild('orderQuantity', '0');
       else $forderline_xml->addChild('orderQuantity', $product['product_quantity']);
       $forderline_xml->addChild('pickerQuantity', $product['realqty']);
       $forderline_xml->addChild('priceOut', $product['product_price']);
       $forderline_xml->addChild('replacementLagers', $product['replacement']);
       }
      
       header("Content-Type: application/xml");
       echo $OrderData_xml_1->asXML();
       die();
      
      
    }
    
    public function hookActionOrderEdited($params)
    {
 /*      $id_order = $params['order']->id;
       $order = new Order($id_order);
       $products = $order->getProducts();
ob_start();
var_dump($products);
$data = ob_get_clean();
$fp = fopen("textfile.txt", "w");
fwrite($fp, $data);
fclose($fp);   */
    return;
    }
    
    public function hookActionOrderHistoryAddAfter($params)
    {
     
     $id_status = (int)$params['order_history']->id_order_state;
     $id_order = (int)$params['order_history']->id_order;
     $courier_array = array();
     $courier_array = array(27,33,30,42,55,57);


     //Britoff 19-05-2020 Внедрение оверсуммы - Старт
     if ( $id_status == 915 ) 
          {
              $order_ref = Db::getInstance()->getValue("SELECT `reference` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
              $blocked_summ = (float)Db::getInstance()->getValue("SELECT `amount` FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = '".$order_ref."'");
              $type_summ = Db::getInstance()->getValue("SELECT `payment_method` FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = '".$order_ref."'");
              $order_summ = (float)Db::getInstance()->getValue("SELECT `total_paid` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order); 
              
              if ($type_summ == 'Liqpay')
                {
                if ($blocked_summ < $order_summ) 
                  {
                    $history = new OrderHistory();
                    $history->id_order = $id_order;
                    $history->id_employee = $this->context->employee->id;
                    $history->changeIdOrderState(927, $id_order);
                    $history->add();
                  }
                }
          
          }   
     //Britoff 19-05-2020 Внедрение оверсуммы - Стоп


     if ($id_status == 913)
     {
     $sql_d = "SELECT c.`dateofdelivery`, d.`timefrom`, d.`timeto`, c.`period`, c.`zone`, c.`id_shop`, c.`id_carrier` FROM `"._DB_PREFIX_."orders` c LEFT JOIN `"._DB_PREFIX_."nove_dateofdelivery` d ON c.`period`=d.`id_period` WHERE c.`id_order` = ".$id_order;
     $d = Db::getInstance()->executeS($sql_d);
     $dateofdelivery = strtotime($d[0]['dateofdelivery']);
     $order_zone = (int)$d[0]['zone'];
     $id_shop = (int)$d[0]['id_shop'];
     $carrier = (int)$d[0]['id_carrier']; //перевозчик

        if ( ($order_zone == 0 || $order_zone == 1000) && $id_shop == 1 && in_array($carrier, $courier_array))   //Киев
          {
            // Start - Блок геолокации
           if ($order_zone == 0)
            {
              $history = new OrderHistory();
              $history->id_order = $id_order;
              $history->id_employee = $this->context->employee->id;
              $history->changeIdOrderState(930, $id_order);
              $history->add();
             
            }
            // Stop - Блок геолокации
            // Start - Блок ожидания
           if ($order_zone == 1000)
            {
              $history = new OrderHistory();
              $history->id_order = $id_order;
              $history->id_employee = $this->context->employee->id;
              $history->changeIdOrderState(931, $id_order);
              $history->add();
             
            }
            // Stop - Блок ожидания
          }
        if ( ($order_zone == 0) && $id_shop != 1 && in_array($carrier, $courier_array))   //не Киев
          {
           // Start - Блок геолокации
           if ($order_zone == 0)
            {
              $history = new OrderHistory();
              $history->id_order = $id_order;
              $history->id_employee = $this->context->employee->id;
              $history->changeIdOrderState(930, $id_order);
              $history->add();
            }
            // Stop - Блок геолокации
          }
      }
     
   //  if ($this->context->employee->id == 1)
   //     {
         $id_order_states = Db::getInstance()->executeS('
        SELECT `id_order_state`
        FROM `' . _DB_PREFIX_ . 'order_history`
        WHERE `id_order` = ' . (int) $id_order . '
        ORDER BY `id_order_history` DESC');

         if ($id_status == 6 && (int)$id_order_states[1]['id_order_state'] == 932) // Подмена статуса отклонен на ожидание возврата денег 
          {
            $order_fiskal = (float)Db::getInstance()->getValue("SELECT `fiskal` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order); 
            if ($order_fiskal > 0) {  
              $sql_sel_pr = "SELECT `id_prihod` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order;
              $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);
              $id_prihod = (int)$sql_sel_pr_ar[0]['id_prihod'];
              $sql_upd_pr = "UPDATE `"._DB_PREFIX_."fozzy_kassa_prihod` SET `full_return` = 1, `vozvrat` = `chek_summ`, `date_vozvr` = `date_chek` WHERE `id_prihod` = ".$id_prihod;
              Db::getInstance()->execute($sql_upd_pr);
              $sql_upd_pr1 = "UPDATE `"._DB_PREFIX_."orders` SET `summ_to_vz` = `fiskal` WHERE `id_order` = ".$id_order;
              Db::getInstance()->execute($sql_upd_pr1);
              $history = new OrderHistory();
              $history->id_order = $id_order;
              $history->id_employee = $this->context->employee->id;
              $history->changeIdOrderState(933, $id_order);  //Статус - оформить возврат
              $history->add();      
            }
          return;
          } 
    //    }
     
     //Britoff 22-01-2020 Внедрение автоподтверждения - Старт
        if ($id_status == 912) 
          {     
          $sql_d = "SELECT `id_address_delivery`, `id_carrier`, `id_shop`, `module` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order;
          $d = Db::getInstance()->executeS($sql_d);
          $id_shop = (int)$d[0]['id_shop'];
          $p_module = $d[0]['module'];
          $id_address_delivery = (int)$d[0]['id_address_delivery'];
          $carrier = (int)$d[0]['id_carrier']; //перевозчик 
          $sql_a = "SELECT `valid_adr`, `lat`, `lng` FROM `"._DB_PREFIX_."address` WHERE `id_address` = ".$id_address_delivery;
          $a = Db::getInstance()->executeS($sql_a);
          $valid_adr = (int)$a[0]['valid_adr'];
          $lat = $a[0]['lat'];
          $lng = $a[0]['lng'];
          
          if (in_array($carrier, $courier_array) && $valid_adr && $lat && $lng && $p_module != 'ps_wirepayment') 
            {
            $history = new OrderHistory();
            $history->id_order = $id_order;
            //$history->id_employee = $this->context->employee->id;
            $history->changeIdOrderState(913, $id_order);  //Статус - подтвержден
            $history->add();
            } 
           $courier_self_array = array(22,24,32,43,51,54,56,58,59);
           if (in_array($carrier, $courier_self_array) && $p_module == 'ecm_liqpay') 
            {
            $history = new OrderHistory();
            $history->id_order = $id_order;
            //$history->id_employee = $this->context->employee->id;
            $history->changeIdOrderState(913, $id_order);  //Статус - подтвержден
            $history->add();
            } 
          return;
          }     
     //Britoff 22-01-2020 Внедрение автоподтверждения - Стоп
     
     return;
    }
    
    public function hookActionOrderStatusUpdate($params)     //Передаем заказ на FZ Client сменой статуса
    {
       $eeee = date('d_m_Y_H_i_s');      
       $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
       $sql_status_to_send = "SELECT `id_order_state` FROM `"._DB_PREFIX_."order_state` WHERE `tsd` = 1";
       $status_to_send_from_base = Db::getInstance()->executeS($sql_status_to_send);
       $statuses_to_send = array();
       foreach ($status_to_send_from_base as $status_r)
        {
          $statuses_to_send[]=$status_r['id_order_state'];
        }
     
       $id_status = (int)$params['newOrderStatus']->id;
       $id_order = (int)$params['id_order'];
       $order = new Order($id_order);
       $id_shop = (int)$order->id_shop;
       $shops_to_tsd = array(1,2,3,4,8,9);
       
       if (!in_array($id_shop,$shops_to_tsd)) return;
       if (!in_array($id_status,$statuses_to_send)) return;
       //if ($id_status == 918) $id_status = 913; // Подмена статуса - Отправлен на ТСД -> Подтвержден
       //if ($id_status == 908) $id_status = 6; // Подмена статуса - Отклонен изменение даты -> Отклонен 
       //if ($id_status == 7) $id_status = 6; // Подмена статуса - Отклонен изменение даты -> Отклонен 
       
       $sql_order = "update `"._DB_PREFIX_."order_detail` set `sorder`=@num:=@num+10 where 0 in(select @num:=0) AND `id_order` = ".$id_order." ORDER BY `sorder`";
       Db::getInstance()->execute($sql_order);
       
       $messages = CustomerMessage::getMessagesByOrderId($id_order);
       $mess = '';
       foreach ($messages as $message)
        {
        $mess .= ' '.$message['message'];
        }
       $mess = html_entity_decode($mess); 

       $id_address = $order->id_address_delivery;
       $id_customer = $order->id_customer;
       $customer = new Customer($id_customer);
       $address = new Address($id_address);
       $customer_fio = $customer->lastname." ".$customer->firstname;
    //   $customer_ad = explode("|",$address->address2);
    //   $customer_address = $customer_ad[0].", ".$customer_ad[1].", кв/оф. ".$customer_ad[2];
       $customer_address = $address->city.", ".$address->street.", ".$address->house.", кв/оф. ".$address->apartment;
       $customer_phone = $address->phone_mobile;
       if  (!$customer_phone || $customer_phone == '' || $customer_phone == " " ) $customer_phone = $address->phone;
       $customer_firma = $customer->company;
       $customer_okpo = $customer->siret;
       $blocked_summ = Db::getInstance()->getValue("SELECT `amount` FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = '".$order->reference."'");
       if (!$blocked_summ) $blocked_summ = 0;
        
       $delivery_reference = 0;
       
       $ship_cost = $order->total_shipping; //стоимость доставки
       $carrier = $order->id_carrier; //перевозчик
       $courier_array = array();
       $courier_array = array(27,33,30,42,55,57);
       
       $discounts = $order->getCartRules();

        if ($discounts) {
            
          foreach ($discounts as $cart_rule)
            {
              if ((int)$cart_rule['free_shipping'] == 1) $ship_cost = 0;
            }
          
        }
        
        
        if ( $ship_cost > 0 && in_array($carrier, $courier_array) ) $shipprint=$ship_cost;  
        
        else $shipprint=0;
        if ($shipprint > 0) $delivery_reference = '600757';
       
       $code128 = '1167615'."_".$id_order."_".$delivery_reference;
       
       
       $products = $order->getProducts();
       foreach ($products as &$order_detail)
    			{
          $category = new Category ((int)$order_detail['id_category_default'],Context::getContext()->language->id);
          $parents = $category->getParentsCategories();
          $order_detail['parent_category'] = $parents[1]['name']; 
          }
          
       array_multisort(array_column($products, 'parent_category'), SORT_ASC, $products); //Сортируем товары по порядку 'sorder' или по категории родителю 'parent_category'
       
       $rn = 0;
       foreach ($products as &$order_detail)
    			{
          $rn++;
          $order_detail['rowNum'] = $rn; 
          }
       
       $sql_log = "SELECT c.`mest`, s.`INN` as sborshik, v.`tabnum` as vodila, v.`fio` as vodilaname, c.`Route_Num`, c.`cartnum`, `norm`, `ice`, `fresh`, `hot` FROM `"._DB_PREFIX_."orders` c LEFT JOIN `"._DB_PREFIX_."fozzy_logistic_sborshik` s ON c.`id_sborshik`=s.`id_sborshik` LEFT JOIN `"._DB_PREFIX_."fozzy_logistic_vodila` v ON c.`id_vodila`=v.`id_vodila` WHERE `id_order` = ".$id_order;
       $log = Db::getInstance()->executeS($sql_log);

        if ($log[0]['norm'] == 1) $logistika .= 'Сухая | ';
        if ($log[0]['ice'] == 1) $logistika .= 'Заморозка | ';
        if ($log[0]['fresh'] == 1) $logistika .= 'Охлажденка | ';
        if ($log[0]['hot'] == 1) $logistika .= 'Горячее | ';
        $logistika = substr($logistika,0,-3);

       $courier_self_array = array(22,24,32,43,51,54,56,58);
       if (in_array($carrier, $courier_self_array)) $log[0]['vodilaname'] = 'Самовывоз';
       if ($carrier == 37) $log[0]['vodilaname'] = 'Нова Пошта'; 
       if ($carrier == 50) $log[0]['vodilaname'] = 'JUSTIN';
       if ($carrier == 59) $log[0]['vodilaname'] = 'Кинодром';
       
       $sql_d = "SELECT c.`dateofdelivery`, d.`timefrom`, d.`timeto`, c.`period` FROM `"._DB_PREFIX_."orders` c LEFT JOIN `"._DB_PREFIX_."nove_dateofdelivery` d ON c.`period`=d.`id_period` WHERE c.`id_order` = ".$id_order;
       $d = Db::getInstance()->executeS($sql_d);
       
       //Britoff - распределение заказов - Старт
       
       // Start - Отправка на подтверждение в Киеве
       if ($id_shop == 1 && $id_status == 913 && in_array($carrier, $courier_array) && (!$order->zone || $order->zone == '0' || (int)$order->zone == 1000))
          {
          // Данные по текущему заказу
          $order_date_delivery = date("d.m.Y H:i:s", strtotime($d[0]['dateofdelivery']));
          $order_period_delivery = $d[0]['period'];
          $order_week_delivery = date('W', strtotime($d[0]['dateofdelivery']));
          $order_day_delivery = date('D', strtotime($d[0]['dateofdelivery']));
          // Данные по количеству разрешенных слотов на точках сборки
          $sql_windows_plan_z = "SELECT `".$order_day_delivery."` FROM `"._DB_PREFIX_."nove_dateofdelivery_block` WHERE `week` = ".$order_week_delivery." AND `window` = ".$order_period_delivery." AND `id_shop` = 1";
          $sql_windows_plan_p = "SELECT `".$order_day_delivery."` FROM `"._DB_PREFIX_."nove_dateofdelivery_block` WHERE `week` = ".$order_week_delivery." AND `window` = ".$order_period_delivery." AND `id_shop` = 25";
          $sql_windows_plan_pr = "SELECT `".$order_day_delivery."` FROM `"._DB_PREFIX_."nove_dateofdelivery_block` WHERE `week` = ".$order_week_delivery." AND `window` = ".$order_period_delivery." AND `id_shop` = 30";
          $windows_plan_z = (int)Db::getInstance()->getValue($sql_windows_plan_z);
          $windows_plan_p = (int)Db::getInstance()->getValue($sql_windows_plan_p);
          $windows_plan_pr = (int)Db::getInstance()->getValue($sql_windows_plan_pr);
          // Данные по количеству размещенных заказов на точках сборки
          $sql_full_orders_z = "SELECT COUNT(`id_order`) AS full FROM `"._DB_PREFIX_."orders` WHERE `zone` = '5' AND `period` = ".$order_period_delivery." AND `dateofdelivery` = '".$d[0]['dateofdelivery']."' AND current_state IN (SELECT `id_order_state` FROM `"._DB_PREFIX_."order_state` WHERE `window` = 1)";
          $sql_full_orders_p = "SELECT COUNT(`id_order`) AS full FROM `"._DB_PREFIX_."orders` WHERE `zone` = '4' AND `period` = ".$order_period_delivery." AND `dateofdelivery` = '".$d[0]['dateofdelivery']."' AND current_state IN (SELECT `id_order_state` FROM `"._DB_PREFIX_."order_state` WHERE `window` = 1)";
          $sql_full_orders_pr = "SELECT COUNT(`id_order`) AS full FROM `"._DB_PREFIX_."orders` WHERE `zone` = '6' AND `period` = ".$order_period_delivery." AND `dateofdelivery` = '".$d[0]['dateofdelivery']."' AND current_state IN (SELECT `id_order_state` FROM `"._DB_PREFIX_."order_state` WHERE `window` = 1)";
          
          $full_orders_z = (int)Db::getInstance()->getValue($sql_full_orders_z);
          $full_orders_p = (int)Db::getInstance()->getValue($sql_full_orders_p);
          $full_orders_pr = (int)Db::getInstance()->getValue($sql_full_orders_pr);
          // Свободные слоты
          $clear_z = $windows_plan_z - $full_orders_z; 
          $clear_p = $windows_plan_p - $full_orders_p;
          $clear_pr = $windows_plan_pr - $full_orders_pr;
          if ($windows_plan_p > 0) $clear_p = $windows_plan_p + 2 - $full_orders_p;
          if ($windows_plan_pr > 0) $clear_pr = $windows_plan_pr + 2 - $full_orders_pr;   
          // Распределение
          $order_zone = 0;
//dump($clear_z);
//dump($clear_p);

          switch ($address->zone) {
            case '4':  // Петровка
               if ($clear_p > 0)
                 {
                   $order_zone = '4';
                 }
               else
                 {
                   $order_zone = '5';
                 }
            break;
            case '5':   // Заболотного
               if ($clear_z > 0)
                 {
                   $order_zone = '5';
                 }
               else
                 {
                   $order_zone = '5';
                 } 
            break;
            case '6':  // Пролиски
               if ($clear_pr > 0)
                 {
                   $order_zone = '6';
                 }
               else
                 {
                   $order_zone = '5';
                 }
            break;
            case '4,5':  // Общее
               if ((int)$order->zone == 1000)
               {
               if ($clear_p > $clear_z && $clear_p > 0) 
                {
                   $order_zone = '4';
                } 
               elseif ($clear_p < $clear_z  && $clear_z > 0) 
                {
                   $order_zone = '5';
                }
               else
                {
                   $order_zone = '5';
                }
               }
               else
               {
                  $order_zone = '1000';
               }
            break;
            case '5,6':  // Общее 2
               if ((int)$order->zone == 1000)
               {
               if ($clear_pr > $clear_z && $clear_pr > 0) 
                {
                   $order_zone = '6';
                } 
               elseif ($clear_pr < $clear_z  && $clear_z > 0) 
                {
                   $order_zone = '5';
                }
               else
                {
                   $order_zone = '5';
                }
               }
               else
               {
                  $order_zone = '1000';
               }
            break;
            }
                    
          switch ((int)$order_zone) {
            case 0:
                $order_zone_name = 'Не розподілено';
                $id_shop = 1;
                break;
            case 4:
                $order_zone_name = 'Петрівка';
                $id_shop = 25;
                break;
            case 5:
                $order_zone_name = 'Заболотного';
                $id_shop = 1;
                break;
            case 6:
                $order_zone_name = 'Проліски';
                $id_shop = 30;
                break;
            case 1000:
                $order_zone_name = 'Очікування';
                $id_shop = 1;
                break;
            }
          
        
        //Для заказов по безналу - Всегда Заболотного
          $mnp = (int)Module::getModuleIdByName($order->module);
          if ($mnp == 29)  
          {
            $order_zone = '5';
            $order_zone_name = 'Заболотного';
            $id_shop = 1;
            
            switch ($address->zone) {
              case '4':  // Петровка
                $order_zone = '4';
                $order_zone_name = 'Петрівка';
                $id_shop = 25;
              break;
              case '5': //Заболотного
              case '5,6':
              case '4,5':
                $order_zone = '5';
                $order_zone_name = 'Заболотного';
                $id_shop = 1;
              break;
              case '6':  // Проліски
                $order_zone = '6';
                $order_zone_name = 'Проліски';
                $id_shop = 30;
              break;
            }
          }
            
          // Обновление зоны
          $sql_order_changezone = "UPDATE `"._DB_PREFIX_."orders` SET `zone` = '".$order_zone."', `zone_name` = '".$order_zone_name."' WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql_order_changezone);
          
          }
             
         
          
          //Самовывозы 
          $courier_super_self_array = array(22,24,32,43,51,54,56,58,37,50,59);
          if ( in_array($carrier, $courier_super_self_array) && $id_status == 913)
          {
              switch ((int)$carrier) {
                case 22:
                    $order_zone = '5';
                    $order_zone_name = 'Заболотного';
                    //$id_shop = 25;
                break;
                case 37:
                case 50:
                    $order_zone = '5';
                    $order_zone_name = 'Заболотного';
                    $id_shop = 1;
                break;
                case 51:
                case 59:
                    $order_zone = '4';
                    $order_zone_name = 'Петрівка';
                    $id_shop = 25;
                break;
                case 56:
                    $order_zone = '6';
                    $order_zone_name = 'Проліски';
                    $id_shop = 30;
                break;
                case 24:
                    $order_zone = '200';
                    $order_zone_name = 'Одеса';
                break;
                case 32:
                    $order_zone = '300';
                    $order_zone_name = 'Дніпро';
                break;
                case 43:
                    $order_zone = '400';
                    $order_zone_name = 'Харків';
                break;
                case 54:
                    $order_zone = '500';
                    $order_zone_name = 'Рівне';
                break;
                case 58:
                    $order_zone = '600';
                    $order_zone_name = 'Кременчуг';
                break;
                }
              /*
              if ($carrier == 22)  
              {
                $order_zone = '5';
                $order_zone_name = 'Заболотного';
            //    $id_shop = 25;
                $sql_order_changezone = "UPDATE `"._DB_PREFIX_."orders` SET `zone` = '".$order_zone."', `zone_name` = '".$order_zone_name."' WHERE `id_order` = ".$id_order;
                Db::getInstance()->execute($sql_order_changezone);
              }
              if ($carrier == 51)  
              {
                $order_zone = '4';
                $order_zone_name = 'Петрівка';
                $id_shop = 25;
                $sql_order_changezone = "UPDATE `"._DB_PREFIX_."orders` SET `zone` = '".$order_zone."', `zone_name` = '".$order_zone_name."' WHERE `id_order` = ".$id_order;
                Db::getInstance()->execute($sql_order_changezone);
              }
              if ($carrier == 56)  
              {
                $order_zone = '6';
                $order_zone_name = 'Проліски';
                $id_shop = 30;
                $sql_order_changezone = "UPDATE `"._DB_PREFIX_."orders` SET `zone` = '".$order_zone."', `zone_name` = '".$order_zone_name."' WHERE `id_order` = ".$id_order;
                Db::getInstance()->execute($sql_order_changezone);
              }    */
              $sql_order_changezone = "UPDATE `"._DB_PREFIX_."orders` SET `zone` = '".$order_zone."', `zone_name` = '".$order_zone_name."' WHERE `id_order` = ".$id_order;
              Db::getInstance()->execute($sql_order_changezone);
              // Обновление зоны
          }
          
          // Stop - Отправка на подтверждение в Киеве
          
          // Start - Отправка на подтверждение не в Киеве
          if ($id_shop != 1 && $id_shop != 25 && $id_shop != 30 && $id_status == 913 && in_array($carrier, $courier_array) && (!$order->zone || $order->zone == '0'))
          {
            if ($address->zone == '0')
              {
               $order_zone = '0';
               $order_zone_name = 'Не розподілено';
              }
            if ($address->zone == '200')
              {
               $order_zone = '200';
               $order_zone_name = 'Одеса';
              }
            if ($address->zone == '300')
              {
               $order_zone = '300';
               $order_zone_name = 'Дніпро';
              }
            if ($address->zone == '400')
              {
               $order_zone = '400';
               $order_zone_name = 'Харків';
              }
            if ($address->zone == '500')
              {
               $order_zone = '500';
               $order_zone_name = 'Рівне';
              }
            if ($address->zone == '600')
              {
               $order_zone = '600';
               $order_zone_name = 'Кременчуг';
              }
            $sql_order_changezone = "UPDATE `"._DB_PREFIX_."orders` SET `zone` = '".$order_zone."', `zone_name` = '".$order_zone_name."' WHERE `id_order` = ".$id_order;
            Db::getInstance()->execute($sql_order_changezone);
          
          }
         // Stop - Отправка на подтверждение не в Киеве
                           
        // Для всех остальных статусов - Петровка
        if ($id_shop == 1 && ($order->zone == '4' || $order->zone == 4))  
          {
           $id_shop = 25;
          }
        // Для всех остальных статусов - Пролиски
        if ($id_shop == 1 && ($order->zone == '6' || $order->zone == 6))  
          {
           $id_shop = 30;
          }
        
         
       
       //Britoff - распределение заказов - Стоп
    /*
       if ($id_order == 220771)
        {
         dump($order);
        }
        unset($order);
       $order = new Order($id_order);
       if ($id_order == 220771)
        {
         dump($order);
         die();
        }
    */   
       
        switch ($id_shop) {
            case 1:
                $filial_id = 1614;
                break;
            case 2:
                $filial_id = 322;
                break;
            case 3:
                $filial_id = 1674;
                break;
            case 4:
                $filial_id = 510;
                break;
            case 8:
                $filial_id = 382;
                break;
            case 9:
                $filial_id = 1292;
                break;
            case 25:
                $filial_id = 1839;   //Филиал Петровка
                break;
            case 30:
                $filial_id = 154;   //Филиал Пролиски
                break;
            default:
                $filial_id = 1614;
                break;
        }
      
      //Тест - Пролиски
 //     if ($order->id == 188506)
 //       {
 //       $filial_id = 154;
 //       }
      
      
      $time_base = 2000000000;
      $time_critical = date("d.m.Y", strtotime($d[0]['dateofdelivery']))." ".$d[0]['timefrom'];
      $time_critical_unix =  strtotime($time_critical);
      if ($id_status == 915) $prioritet = $time_base;
      else $prioritet = $time_base - $time_critical_unix;
       
        $mn = Module::getModuleIdByName($order->module);
         
       require_once 'simp.php'; 
       $OrderData_xml = new SimpleXMLElementExtended('<OrderData xmlns:i="http://www.w3.org/2001/XMLSchema-instance"></OrderData>');
       $order_xml = $OrderData_xml->addChild('order');
       $forder_xml = $order_xml->addChild('FzShopOrder');
       $forder_xml->addChild('clientFullName', $customer_fio); 
       $forder_xml->addChild('clientMobilePhone', $customer_phone);  
       $forder_xml->addChild('contragentFullName', $customer_firma);
       $forder_xml->addChild('contragentOKPO', $customer_okpo);
       $forder_xml->addChild('dateModified', date("d.m.Y H:i:s", strtotime($order->date_upd)));
       $forder_xml->addChild('deliveryAddress', $customer_address);    
       $forder_xml->addChild('deliveryDate', date("d.m.Y H:i:s", strtotime($d[0]['dateofdelivery'])));
       $forder_xml->addChild('deliveryId', $carrier);                 
       $forder_xml->addChild('deliveryTimeFrom', $d[0]['timefrom']);
       $forder_xml->addChild('deliveryTimeTo', $d[0]['timeto']);
       $forder_xml->addChild('driverId', $log[0]['vodila']);
       $forder_xml->addChild('driverName', $log[0]['vodilaname']);
       $forder_xml->addChild('filialId', $filial_id);
       if ($log[0]['sborshik']) $forder_xml->addChild('globalUserId', $log[0]['sborshik']);
       if ($log[0]['cartnum']) $forder_xml->addChild('lastContainerBarcode', $log[0]['cartnum']);
       $forder_xml->addChild('logisticsType', $logistika);       
       $forder_xml->addChild('orderBarcode', $code128);       
       $forder_xml->addChild('orderCreated', date("d.m.Y H:i:s", strtotime($order->date_add)));
       $forder_xml->addChild('orderId', $id_order);
       $forder_xml->addChild('orderStatus', $id_status);
       $forder_xml->addChild('paymentId', $mn);
       $forder_xml->addChild('placesCount', $log[0]['mest']);
       $forder_xml->addChild('priority', $prioritet);
       $forder_xml->addChild('remark', $mess);
       $forder_xml->addChild('sumPaymentFromInternet', $blocked_summ);
       $orderline_xml = $OrderData_xml->addChild('orderLines');
       foreach ($products as $product) {
       $forderline_xml = $orderline_xml->addChild('FzShopOrderLines');
       if ($product['cartnum']) $forderline_xml->addChild('containerBarcode', $product['cartnum'] );
       if ($product['datemodified']) $forderline_xml->addChild('dateModified', date("d.m.Y H:i:s", strtotime($product['datemodified'])));
       switch ($product['condition']) {
            case 'new':
                $freezeStatus = '0';
                break;
            case 'used':
                $freezeStatus = 1;
                break;
            case 'refurbished':
                $freezeStatus = 2;
                break;
            default:
                $freezeStatus = '0';
                break;
        }
       $forderline_xml->addChild('freezeStatus', $freezeStatus );
       if ($product['loginmodified']) $forderline_xml->addChild('globalUserId', $product['loginmodified']);  
       $forderline_xml->addChild('lagerId', $product['product_reference']);
       $forderline_xml->addChild('orderId', $id_order);
       if ( $product['product_quantity'] == 0) $forderline_xml->addChild('orderQuantity', '0');
       else $forderline_xml->addChild('orderQuantity', $product['product_quantity']);
       if ($product['realqty']) $forderline_xml->addChild('pickerQuantity',  $product['realqty']);
       $forderline_xml->addChild('priceOut', $product['product_price']);
       if ($product['replacement']) $forderline_xml->addChild('replacementLagers', $product['replacement']);
       $forderline_xml->addChild('rowNum', $product['rowNum']);
       }
       $lxml = $OrderData_xml->asXML(); 
       $xml = str_replace(['<?xml version="1.0"?>'],'',$lxml);
       $filename = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/sborka/'.$id_order."_".date('d_m_Y_H_i_s')."_".$eeee."_send.xml"; 
       $OrderData_xml->asXML($filename);
  //     header("Content-Type: text/xml");
  //     echo $lxml;
  //     die();
     $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
     if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutOrderData';
     else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutOrderData'; 
      
     $result = $this->PostXML($link_to_send,$OrderData_xml->asXML()); 
     if ($result != '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode></ConfirmResponse>')
        {
          $filename = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/sborka/'.$id_order."_".date('d_m_Y_H_i_s')."_send_error.txt";
          $file = fopen($filename, 'w');
          fwrite($file, $result);
          fclose($file);
          dump($result);
          die();
        }
     else
        {
          $filename = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/sborka/'.$id_order."_".date('d_m_Y_H_i_s')."_send_result.xml"; 
          $OrderData_xml->asXML($filename);
          return;
        }      
    }
    
  public function isAlive()
    {
     return '<isAliveResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode><errorMessage>'.date("Y-m-d H:i:s").'</errorMessage></isAliveResponse>';
    }
  
  public function PayConfirmFromKassa($data = NULL)
    {
    $errors[] = array();
    $KassaData_xml = simplexml_load_string($data);
    //$filename = '/home/admin/web/test.fozzyshop.com.ua/public_html/modules/fozzy_orders/log/_18_11_2019_14_56_kassa.xml';
    //$KassaData_xml = simplexml_load_file($filename); 
    
    $KassaData_array = array();
    $KassaData_array = $this->object2array($KassaData_xml);
    
    $id_order = (int)$KassaData_array['orderId'];
    $id_filial = (int)$KassaData_array['filial'];
    $sumPayment = (float)$KassaData_array['sumPayment'];
    $id_shop = 1;
    
    //Возврат - старт
    if ($sumPayment < 0)
      {
      $summ_to_return = abs($sumPayment);
      $id_cart = Db::getInstance()->getValue("SELECT `id_cart` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
      $order_ref = Db::getInstance()->getValue("SELECT `reference` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
      $order_module = Db::getInstance()->getValue("SELECT `module` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
      $fiskal = (float)Db::getInstance()->getValue("SELECT `fiskal` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
      
      
      
      //Временная жопа
      /*
      $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = 937, `date_upd` = '".date("Y-m-d H:i:s", time())."' WHERE `id_order` = ".$id_order;
      $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$id_order.",937,'".date("Y-m-d H:i:s", time())."')";
      $order_up = Db::getInstance()->execute($update_order);
      $order_s_up = Db::getInstance()->execute($update_order_status);
      
      $order_pay = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = ".$order_ref);
      $AuthorizeCode = $order_pay[0]['card_brand'];
      $PanOrigin = $order_pay[0]['card_number'];
      if ($id_filial == 1614) $Merchantid = 'i82027887313';
      if ($id_filial == 1839) $Merchantid = 'i17001049739';
      if ($id_filial == 154) $Merchantid = 'i25843801331';
      if ($id_filial == 322) $Merchantid = 'i76491608512';
      if ($id_filial == 1674) $Merchantid = 'i21976893440';
      if ($id_filial == 510) $Merchantid = 'i75033423344';
      if ($id_filial == 382) $Merchantid = 'i59359871774';
      if ($id_filial == 1292) $Merchantid = 'i28519541258';
      $TerminalID = $Merchantid;
      $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><AuthorizeCode>'.$AuthorizeCode.'</AuthorizeCode><errorCode>0</errorCode><errorMessage></errorMessage><isDelete>0</isDelete><Merchantid>'.$Merchantid.'</Merchantid><MFO>305299</MFO><PanOrigin>'.$PanOrigin.'</PanOrigin><RRN>'.$id_cart.'</RRN><TerminalID>'.$TerminalID.'</TerminalID></PayConfirmResponse>';
      $KassaData_xml_out = simplexml_load_string($out);
      $filename_log_out = '/home/admin/web/fozzyshop.com.ua/public_html/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_v.xml";
      $KassaData_xml_out->asXML($filename_log_out);
      return $out;
      */
      //Конец временной жопы
      
      
      
      $filename_log_vin = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_in_v.xml";
      $KassaData_xml->asXML($filename_log_vin);
      
      if (!$order_ref) 
      {
        $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>2</errorCode><errorMessage>Замовлення не знайдено.</errorMessage></PayConfirmResponse>';
        $KassaData_xml_out = simplexml_load_string($out);
        $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_vn.xml";
        $KassaData_xml_out->asXML($filename_log_out);
        return $out;
      }
      
      switch ($order_module)
      {
        case 'ecm_liqpay':
        // liqpay - start
          switch ($id_filial) {
            case 1614:
            case 154:
            case 1839:
                $id_shop = 1;
                break;
            case 322:
                $id_shop = 2;
                break;
            case 1674:
                $id_shop = 3;
                break;
            case 510:
                $id_shop = 4;
                break;
            case 382:
                $id_shop = 8;
                break;
            case 1292:
                $id_shop = 9;
                break;    
            default:
                $id_shop = 1;
                break;
          }
          $liq_params = $this->params($id_cart,'refund',$summ_to_return);
          $liq = $this->api_refund("request", $liq_params, $id_shop, $id_filial);
           
          if ($liq->status == 'reversed' && $liq->result == 'ok') {                                                                  
              
              $state_num = 937;
              $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = ".$state_num.", `date_upd` = '".date("Y-m-d H:i:s", time())."' WHERE `id_order` = ".$id_order;
              $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$id_order.",".$state_num.",'".date("Y-m-d H:i:s", time())."')";
              $order_up = Db::getInstance()->execute($update_order);
              $order_s_up = Db::getInstance()->execute($update_order_status);
              
              if ($summ_to_return != $fiskal)
                  {
                   $state_num = 916;
                   $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = ".$state_num.", `date_upd` = '".date("Y-m-d H:i:s", time())."' WHERE `id_order` = ".$id_order;
                   $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$id_order.",".$state_num.",'".date("Y-m-d H:i:s", time())."')";
                   $order_up = Db::getInstance()->execute($update_order);
                   $order_s_up = Db::getInstance()->execute($update_order_status);
                  }
              
              $date_vz = date('Y-m-d');
              $sql_sel_pr = "SELECT `id_prihod`, `fiskal`, `summ_to_vz` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order;
              $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);
              $id_prihod = (int)$sql_sel_pr_ar[0]['id_prihod'];
              
              $sql_upd_pr = "UPDATE `"._DB_PREFIX_."fozzy_kassa_prihod` SET `st_oplat_vozvr` = 1, `vzvshno` = ".$summ_to_return.", `date_vozvr_chek` = '".$date_vz."' WHERE `id_prihod` = ".$id_prihod;
              Db::getInstance()->execute($sql_upd_pr);
              
              
              $Merchantid = $liq->public_key;
              
              $order_pay = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = ".$order_ref);
              $AuthorizeCode = $order_pay[0]['card_brand'];
              $PanOrigin = $order_pay[0]['card_number'];
              if ($id_filial == 1614) $Merchantid = 'i82027887313';
              if ($id_filial == 1839) $Merchantid = 'i17001049739';
              if ($id_filial == 154) $Merchantid = 'i25843801331';
              if ($id_filial == 322) $Merchantid = 'i76491608512';
              if ($id_filial == 1674) $Merchantid = 'i21976893440';
              if ($id_filial == 510) $Merchantid = 'i75033423344';
              if ($id_filial == 382) $Merchantid = 'i59359871774';
              if ($id_filial == 1292) $Merchantid = 'i28519541258';
              $TerminalID = $Merchantid;
              $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><AuthorizeCode>'.$AuthorizeCode.'</AuthorizeCode><errorCode>0</errorCode><errorMessage></errorMessage><isDelete>0</isDelete><Merchantid>'.$Merchantid.'</Merchantid><MFO>305299</MFO><PanOrigin>'.$PanOrigin.'</PanOrigin><RRN>'.$id_cart.'</RRN><TerminalID>'.$TerminalID.'</TerminalID></PayConfirmResponse>';
              $KassaData_xml_out = simplexml_load_string($out);
              $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_v.xml";
              $KassaData_xml_out->asXML($filename_log_out);
            return $out;
          } 
          else
          {
            $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>'.$liq->err_code.'</errorCode><errorDescription>'.$liq->err_description.'</errorDescription><errorMessage>Неможливо виконати платіжну операцію.</errorMessage><isDelete>0</isDelete></PayConfirmResponse>';
            $KassaData_xml_out = simplexml_load_string($out);
            $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_ve.xml";
            $KassaData_xml_out->asXML($filename_log_out);
            return $out;
          }
      // liqpay - stop  
      break;
      case 'ps_cashondelivery':
      // Cash - start
        $state_num = 938;
        $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = ".$state_num.", `date_upd` = '".date("Y-m-d H:i:s", time())."' WHERE `id_order` = ".$id_order;
        $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$id_order.",".$state_num.",'".date("Y-m-d H:i:s", time())."')";
        $order_up = Db::getInstance()->execute($update_order);
        $order_s_up = Db::getInstance()->execute($update_order_status);
        
        if ($summ_to_return != $fiskal)
                  {
                   $state_num = 916;
                   $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = ".$state_num.", `date_upd` = '".date("Y-m-d H:i:s", time())."' WHERE `id_order` = ".$id_order;
                   $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$id_order.",".$state_num.",'".date("Y-m-d H:i:s", time())."')";
                   $order_up = Db::getInstance()->execute($update_order);
                   $order_s_up = Db::getInstance()->execute($update_order_status);
                  }
        
        $date_vz = date('Y-m-d');
        $sql_sel_pr = "SELECT `id_prihod`, `fiskal`, `summ_to_vz` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order;
        $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);
        $id_prihod = (int)$sql_sel_pr_ar[0]['id_prihod'];
        
        $sql_upd_pr = "UPDATE `"._DB_PREFIX_."fozzy_kassa_prihod` SET `st_oplat_vozvr` = 1, `vzvshno` = ".$summ_to_return.", `date_vozvr_chek` = '".$date_vz."' WHERE `id_prihod` = ".$id_prihod;
        Db::getInstance()->execute($sql_upd_pr);
        
        $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode><errorMessage></errorMessage></PayConfirmResponse>';
        $KassaData_xml_out = simplexml_load_string($out);
        $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_vnal.xml";
        $KassaData_xml_out->asXML($filename_log_out);
        return $out;
      // Cash - stop
      break;
      }
      
      
      
      return true;
      
      }
    //Возврат - стоп
    
    $split_filials = array();
    $split_filials[] = 1614;
    $split_filials[] = 1839;
    $split_filials[] = 154;
    
    if (in_array($id_filial, $split_filials))
      {
       $split = 1;
       $id_shop = 1;
      }
    else
      {
       $split = 0;
       switch ($id_filial) {
            case 1614:
                $id_shop = 1;
                break;
            case 1839:
                $id_shop = 1;
                break;
            case 154:
                $id_shop = 1;
                break;    
            case 322:
                $id_shop = 2;
                break;
            case 1674:
                $id_shop = 3;
                break;
            case 510:
                $id_shop = 4;
                break;
            case 382:
                $id_shop = 8;
                break;
            case 1292:
                $id_shop = 9;
                break;
            default:
                $id_shop = 1;
                break;
        }
      }
    
    $filename_log = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_in.xml";
    $KassaData_xml->asXML($filename_log);
    
    $id_cart = Db::getInstance()->getValue("SELECT `id_cart` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
    $order_ref = Db::getInstance()->getValue("SELECT `reference` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
    
  if (!$order_ref) 
    {
    $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>2</errorCode><errorMessage>Замовлення не знайдено.</errorMessage></PayConfirmResponse>';
      $KassaData_xml_out = simplexml_load_string($out);
      $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_noorder.xml";
      $KassaData_xml_out->asXML($filename_log_out);
      return $out;
    }
    $blocked_summ = (float)Db::getInstance()->getValue("SELECT `amount` FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = '".$order_ref."'");

//проверка статуса заказа
$liq_params = $this->params($id_cart,'status',0);
$liq = $this->api("request", $liq_params, $id_shop);
  
if ($liq->status == 'success')   //Если оплачено - отдаем реквизиты
{
  $Merchantid = $liq->public_key;
    if ($id_filial == 1614) $Merchantid = 'i82027887313';
    if ($id_filial == 1839) $Merchantid = 'i17001049739';
    if ($id_filial == 154) $Merchantid = 'i25843801331';
  $TerminalID = $Merchantid;
  $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><AuthorizeCode>'.$liq->authcode_debit.'</AuthorizeCode><errorCode>0</errorCode><errorMessage></errorMessage><isDelete>0</isDelete><Merchantid>'.$Merchantid.'</Merchantid><MFO>305299</MFO><PanOrigin>'.$liq->sender_card_mask2.'</PanOrigin><RRN>'.$liq->payment_id.'</RRN><TerminalID>'.$TerminalID.'</TerminalID></PayConfirmResponse>';
  $KassaData_xml_out = simplexml_load_string($out);
  $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out.xml";
  $KassaData_xml_out->asXML($filename_log_out);
  return $out;
}
else  //Если не оплачено - снимаем деньги
{
  if ($blocked_summ < $sumPayment) {
    $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = 927, `date_upd` = '".date("Y-m-d H:i:s", time())."' WHERE `id_order` = ".$id_order;
    $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$id_order.",927,'".date("Y-m-d H:i:s", time())."')";
    $order_up = Db::getInstance()->execute($update_order);
    $order_s_up = Db::getInstance()->execute($update_order_status);
    $data_to_send = array();
    $data_to_send['id_order'] = $id_order;
    
    $object_status = new stdClass();
    $object_status->id = 927;
    
    $data_to_send['newOrderStatus'] = $object_status;
    $send_to_fzclient = $this->hookActionOrderStatusUpdate($data_to_send);
    $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>err_amount_hold</errorCode><errorDescription>Итоговая сумма заказа превысила заблокированную сумму оплаты через интернет.</errorDescription><errorMessage>Помилка! Загальна сума до сплати, перевищує сумму заблоковану для оплати замовлення.</errorMessage><isDelete>1</isDelete></PayConfirmResponse>';
      $KassaData_xml_out = simplexml_load_string($out);
      $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out.xml";
      $KassaData_xml_out->asXML($filename_log_out);
      return $out;
    }                                                                                                                      
                                                                                                                        
  if ($id_cart && ($blocked_summ >= $sumPayment) ) {  
    
    if ($split) 
    {
     if ($id_filial == 1614) 
     {
       $split_rules[0] = array(
        'public_key' => 'i82027887313',
        'amount' => $sumPayment,
        'commission_payer' => 'receiver',
       );
       $liq_params = array(
  			'action'        => 'hold_completion',
  			'version'       => '3',
  			'order_id'      => $id_cart,
  			'amount'        => $sumPayment,
  			'split_rules'   => $split_rules,
  		 );
     }
     if ($id_filial == 1839) 
     {
       $split_rules[0] = array(
        'public_key' => 'i17001049739',
        'amount' => $sumPayment,
        'commission_payer' => 'receiver',
       );
       $liq_params = array(
  			'action'        => 'hold_completion',
  			'version'       => '3',
  			'order_id'      => $id_cart,
  			'amount'        => $sumPayment,
  			'split_rules'   => $split_rules,
  		 );
     }
    if ($id_filial == 154) 
     {
       $split_rules[0] = array(
        'public_key' => 'i25843801331',
        'amount' => $sumPayment,
        'commission_payer' => 'receiver',
       );
       $liq_params = array(
  			'action'        => 'hold_completion',
  			'version'       => '3',
  			'order_id'      => $id_cart,
  			'amount'        => $sumPayment,
  			'split_rules'   => $split_rules,
  		 );
     }     
     
    }
    else
    {
      $liq_params = $this->params($id_cart,'hold_completion',$sumPayment);
    }
    
    $liq = $this->api("request", $liq_params, $id_shop); //Снятие суммы                    
  //  dump($liq);                                                                                                        
//    $liq = $this->api("request", $this->params($id_cart,'hold_completion',$sumPayment)); //Снятие суммы          
              
    if ($liq->result == 'ok' && $liq->action == 'hold' && $liq->status == 'success') {                                                                  
      $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = 929, `date_upd` = '".date("Y-m-d H:i:s", time())."' WHERE `id_order` = ".$id_order;
      $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$id_order.",929,'".date("Y-m-d H:i:s", time())."')";
      $order_up = Db::getInstance()->execute($update_order);
      $order_s_up = Db::getInstance()->execute($update_order_status);
      $Merchantid = $liq->public_key;
      if ($id_filial == 1614) $Merchantid = 'i82027887313';
      if ($id_filial == 1839) $Merchantid = 'i17001049739';
      if ($id_filial == 154) $Merchantid = 'i25843801331';
      $TerminalID = $Merchantid;
      $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><AuthorizeCode>'.$liq->authcode_debit.'</AuthorizeCode><errorCode>0</errorCode><errorMessage></errorMessage><isDelete>0</isDelete><Merchantid>'.$Merchantid.'</Merchantid><MFO>305299</MFO><PanOrigin>'.$liq->sender_card_mask2.'</PanOrigin><RRN>'.$liq->payment_id.'</RRN><TerminalID>'.$TerminalID.'</TerminalID></PayConfirmResponse>';
      $KassaData_xml_out = simplexml_load_string($out);
      $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out.xml";
      $KassaData_xml_out->asXML($filename_log_out);
      return $out;
    } 
    if ($liq->result == 'error') {
      $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>'.$liq->err_code.'</errorCode><errorDescription>'.$liq->err_description.'</errorDescription><errorMessage>Неможливо виконати платіжну операцію.</errorMessage><isDelete>0</isDelete></PayConfirmResponse>';
      $KassaData_xml_out = simplexml_load_string($out);
      $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_error.xml";
      $KassaData_xml_out->asXML($filename_log_out);
      return $out;
    }
    else
    {
     $out = '<PayConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>100</errorCode><errorDescription>Ошибка списания средств</errorDescription><errorMessage>Неможливо виконати платіжну операцію.</errorMessage><isDelete>0</isDelete></PayConfirmResponse>';
      $KassaData_xml_out = simplexml_load_string($out);
      $filename_log_out = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_imp.xml";
      $filename_log_out_imp = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/kassa/'.$id_order."_".date('d_m_Y_H_i_s')."_kassa_out_imp.log";
      $KassaData_xml_out->asXML($filename_log_out);
      $fp = fopen($filename_log_out_imp, "w");
      $text = print_r($this->object2array($liq), true);
      fwrite($fp, $text);
      fclose($fp);
      return $out;
    }
    }
}
    return;
    }

 public function api_refund($path, $params = array(),  $id_shop = 1, $id_filial = 0, $timeout = 15)
 {
    if (!isset($params['version'])) {
        throw new InvalidArgumentException('version is null');
    }
    
    $config = Configuration::getMultiple(array('liqpay_id', 'liqpay_pass'),null,1,$id_shop);
    $public_key  = $config['liqpay_id'];
    $private_key = $config['liqpay_pass'];
    
    if ($id_shop == 1 && $id_filial == 1614) 
      {
       $public_key = 'i82027887313';
       $private_key = 'yTvSjWIB4cuiRuYnC3UrY58IdVoKFrT4qeNJBfLm';
      }
    if ($id_shop == 1 && $id_filial == 1839) 
      {
       $public_key = 'i17001049739';
       $private_key = 'yhZZfYp7NmOYW3owGRuz2lbTPu64vvUst1Xaf8ru';
      }
    if ($id_shop == 1 && $id_filial == 154) 
      {
       $public_key = 'i25843801331';
       $private_key = 'sHTKZWb6iJuj6i0LRHXD5CWYpMzalxUarhpv0QKG';
      }  
     
     
     $url         = 'https://www.liqpay.ua/api/' . $path;
     
     $data        = $this->encode_params(array_merge(compact('public_key'), $params));
     $signature   = $this->str_to_sign($private_key.$data.$private_key);
     $postfields  = http_build_query(array(
        'data'  => $data,
        'signature' => $signature
     ));
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Avoid MITM vulnerability http://phpsecurity.readthedocs.io/en/latest/Input-Validation.html#validation-of-input-sources
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Check the existence of a common name and also verify that it matches the hostname provided
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);   // The number of seconds to wait while trying to connect
     curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);          // The maximum number of seconds to allow cURL functions to execute
     curl_setopt($ch, CURLOPT_POST, true);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $server_output = curl_exec($ch);
            
     $this->_server_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     curl_close($ch);
     $resp = json_decode($server_output);
     if($resp->result == 'error') {
        //$this->context->cookie->__set('redirect_errors', $this->l('err_code: ').$resp->err_code.' '.$resp->err_description);
        }
     if($resp->result == 'ok' && $resp->action == 'hold') {
         //$this->context->cookie->__set('redirect_success', $this->l('payment in the amount of ').$resp->amount.$this->l(' UAH  was successfully confirmed'));
    		}
     if($resp->action == 'refund' && $resp->status == 'reversed') {
         //$this->context->cookie->__set('redirect_success', $this->l('payment was successfully returned'));
    		}
     return json_decode($server_output);
  }
   
 public function api($path, $params = array(),  $id_shop = 1, $timeout = 30)
 {
    if (!isset($params['version'])) {
        throw new InvalidArgumentException('version is null');
    }
            
     $config = Configuration::getMultiple(array('liqpay_id', 'liqpay_pass'),null,1,$id_shop);
     
     $url         = 'https://www.liqpay.ua/api/' . $path;
     $public_key  = $config['liqpay_id'];
     $private_key = $config['liqpay_pass'];
     $data        = $this->encode_params(array_merge(compact('public_key'), $params));
     $signature   = $this->str_to_sign($private_key.$data.$private_key);
     $postfields  = http_build_query(array(
        'data'  => $data,
        'signature' => $signature
     ));
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Avoid MITM vulnerability http://phpsecurity.readthedocs.io/en/latest/Input-Validation.html#validation-of-input-sources
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Check the existence of a common name and also verify that it matches the hostname provided
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);   // The number of seconds to wait while trying to connect
     curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);          // The maximum number of seconds to allow cURL functions to execute
     curl_setopt($ch, CURLOPT_POST, true);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $server_output = curl_exec($ch);
            
     $this->_server_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     curl_close($ch);
     $resp = json_decode($server_output);
     if($resp->result == 'error') {
        //$this->context->cookie->__set('redirect_errors', $this->l('err_code: ').$resp->err_code.' '.$resp->err_description);
        }
     if($resp->result == 'ok' && $resp->action == 'hold') {
         //$this->context->cookie->__set('redirect_success', $this->l('payment in the amount of ').$resp->amount.$this->l(' UAH  was successfully confirmed'));
    		}
     if($resp->action == 'refund' && $resp->status == 'reversed') {
         //$this->context->cookie->__set('redirect_success', $this->l('payment was successfully returned'));
    		}
     return json_decode($server_output);
  }
  
  private function encode_params($params)
    {
        return base64_encode(json_encode($params));
    }
    
  public function str_to_sign($str)
    {
        $signature = base64_encode(sha1($str, 1));
        return $signature;
    }
    
	private function params($id_order,$action,$amount)
    {
		return array(
			'action'        => $action,
			'version'       => '3',
			'order_id'      => $id_order,
			'amount'        => $amount,
			//'sandbox'       => 1,
			);
	  }  
    
  public function ListenAll($auto, $data = NULL, $id_order = NULL)      //Слушающий запрос от FZ Client, ручной и автоматический
  {
    if ($auto == 1 && $data)
        {
        $OrderData_xml = simplexml_load_string($data);
        $OrderData_array = array();
        $OrderData_array = $this->object2array($OrderData_xml);
        $id_order = $OrderData_array['order']['FzShopOrder']['orderId'];
        $filename = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/sborka/'.$id_order."_".date('d_m_Y_H_i_s')."_post.xml";
        }
    elseif ($auto == 0 && $id_order)
        {
        $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
         if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetOrderData?orderId='.$id_order;
         else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetOrderData?orderId='.$id_order;
        
        $postData = $this->GetXML($link_to_send); 
        $OrderData_xml = simplexml_load_string($postData);
        $OrderData_array = array();
        $OrderData_array = $this->object2array($OrderData_xml);
        
        $filename = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/sborka/'.$id_order."_".date('d_m_Y_H_i_s')."_mget.xml";
        }
    else
        {
        die();
        }
    
    
    $OrderData_xml->asXML($filename);
    
    switch ($OrderData_array['order']['FzShopOrder']['filialId']) {
            case 1614:
                $filial_id = 1;
                break;
            case 322:
                $filial_id = 2;
                break;
            case 1674:
                $filial_id = 3;
                break;
            case 510:
                $filial_id = 4;
                break;
            case 1839:
                $filial_id = 1;
                break;
            case 154:
                $filial_id = 1;
                break;
            case 382:
                $filial_id = 8;
                break;
            case 1292:
                $filial_id = 9;
                break;
            default:
                return '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>404</errorCode><errorMessage>Филиал не подключен</errorMessage></ConfirmResponse>';
                break;
        }
    
    
    $inn_g = $this->getSBbyINN($OrderData_array['order']['FzShopOrder']['globalUserId']);
    // Шапка заказа
    $update_order = "UPDATE `"._DB_PREFIX_."orders` SET `current_state` = ".$OrderData_array['order']['FzShopOrder']['orderStatus'].", `date_upd` = '".date("Y-m-d H:i:s", strtotime($OrderData_array['order']['FzShopOrder']['dateModified']))."' WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
    $update_order_status = "INSERT INTO `"._DB_PREFIX_."order_history`(`id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (47,".$OrderData_array['order']['FzShopOrder']['orderId'].",".$OrderData_array['order']['FzShopOrder']['orderStatus'].",'".date("Y-m-d H:i:s", time())."')";
    if (!$OrderData_array['order']['FzShopOrder']['placesCount']) $places = 0;
    else $places = $OrderData_array['order']['FzShopOrder']['placesCount'];
    if (!$OrderData_array['order']['FzShopOrder']['lastContainerBarcode']) $ordCart = 'NULL'; 
    else $ordCart = $OrderData_array['order']['FzShopOrder']['lastContainerBarcode'];
    if ( !is_int($ordCart) )  $ordCart = 1;
    $update_order_logistic = "UPDATE `"._DB_PREFIX_."orders` l SET l.`mest` = ".$places.", l.`id_sborshik` = ".$inn_g.", l.`cartnum` = ".$ordCart." WHERE l.`id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];     //Проверить телегу
    $errors = array();
    $order_up = Db::getInstance()->execute($update_order);
    if (!$order_up) $errors[] = 'Can`t update header of order';
    $order_s_up = Db::getInstance()->execute($update_order_status);
    if (!$order_s_up) $errors[] = 'Can`t update status of order';
    $order_l_up = Db::getInstance()->execute($update_order_logistic); 
    if (!$order_l_up) $errors[] = 'Can`t update logistics of order';
    
    
    if (!$OrderData_array['order']['FzShopOrder']['containerBarcodes']) $ordCont = 0; 
    else $ordCont = $OrderData_array['order']['FzShopOrder']['containerBarcodes'];
    
    if ($ordCont) {
       $update_order_cont = "UPDATE `"._DB_PREFIX_."orders` l SET l.`containerBarcodes` = '".$ordCont."' WHERE l.`id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId']; 
       $order_cont_up = Db::getInstance()->execute($update_order_cont); 
       if (!$order_cont_up) $errors[] = 'Can`t update containerBarcodes of order';
    }
    
    
    
    $sumPaymentFromKassa = (float)$OrderData_array['order']['FzShopOrder']['sumPaymentFromKassa'];
    $rroNumber = $OrderData_array['order']['FzShopOrder']['rroNumber'];
    $carrier_id = (int)$OrderData_array['order']['FzShopOrder']['deliveryId'];
    
    if ($carrier_id == 37)
      {
        $sql_upd_np_mest = "UPDATE `"._DB_PREFIX_."ecm_newpost_orders` SET `seats_amount` = ".$places." WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
        Db::getInstance()->execute($sql_upd_np_mest);
      }
      
    if ($OrderData_array['order']['FzShopOrder']['orderStatus'] == 16 && $carrier_id == 37)
      {
        $sql_upd_orr = "UPDATE `"._DB_PREFIX_."orders` SET `dateofdelivery` = '".date('Y-m-d 00:00:00', strtotime("+1 day"))."', `period` = 139  WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
        Db::getInstance()->execute($sql_upd_orr);
      }
    
    if ($sumPaymentFromKassa)
    {
     $update_order_fiskal = "UPDATE `"._DB_PREFIX_."orders` l SET l.`fiskal` = ".$sumPaymentFromKassa." WHERE l.`id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
     $order_fiskal = Db::getInstance()->execute($update_order_fiskal);
    }
    
    if ($rroNumber)
    {
     $update_order_rro = "UPDATE `"._DB_PREFIX_."orders` l SET l.`rro_num` = '".$rroNumber."' WHERE l.`id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
     $order_rro = Db::getInstance()->execute($update_order_rro);
    }
    
    
    if (isset($OrderData_array['orderLines']['FzShopOrderLines'][0])) $listing = $OrderData_array['orderLines']['FzShopOrderLines'];
    else $listing = $OrderData_array['orderLines'];
    //Тело заказа
    foreach ($listing as $line)
      {
       $sql_find = "SELECT `id_order_detail` FROM `"._DB_PREFIX_."order_detail` WHERE `id_order` = ".$line['orderId']." AND `product_reference` = '".$line['lagerId']."'";
       $id_order_detail = Db::getInstance()->getValue($sql_find);
       if (isset($id_order_detail) && $id_order_detail )
        {
        $date = date("Y-m-d H:i:s", strtotime($line['dateModified']));
        $id_sborshik = $this->getSBbyINN( is_array($line['globalUserId']) ? 0 : $line['globalUserId'] );
        $reference = $line['lagerId'];
        $reference_ord = $line['orderId'];
        
        if ($line['containerBarcode']) $cartnum = $line['containerBarcode'];
        else  $cartnum = 'NULL';
        if ( !is_int($cartnum) )  $cartnum = 1;
        $product_quantity = floatval(str_replace(',', '.', (string)$line['orderQuantity']));
        if ($line['pickerQuantity']) $realqty = floatval(str_replace(',', '.', (string)$line['pickerQuantity']));
        else  $realqty = 'NULL';
        $priceOut = floatval(str_replace(',', '.', (string)$line['priceOut']));
        $replacementLagers = is_array($line['replacementLagers']) ? 0 : $line['replacementLagers']; 
        
        //Доработка по автозамене колва по факту
    /*    if ($line['pickerQuantity'] && $line['pickerQuantity'] === '0,000') 
            {
            $product_quantity = 0;
            }
        else {     */
           if ($realqty != 'NULL' && $realqty > 0)
              {
                if (filter_var($realqty, FILTER_VALIDATE_INT) === false)
                  {
                   $percent = abs(floor((($product_quantity / $realqty) * 100)-100));
                   if ( $product_quantity <= 2 && $percent < 21) $product_quantity = $realqty;
                   if ( $product_quantity > 2 && $percent < 11) $product_quantity = $realqty;
                  }
              }
   /*     }
                   */
     //   $upd_line = "UPDATE `ps_order_detail` SET `loginmodified`=".$id_sborshik.", `datemodified`='".$date."', `realqty` = ".$realqty.", `product_quantity` = ".$product_quantity.", `cartnum` = '".$cartnum."' WHERE `id_order_detail` =".$id_order_detail;
        $upd_line = "UPDATE `ps_order_detail` SET `loginmodified`=".$id_sborshik.", `datemodified`='".$date."', `realqty` = ".$realqty.", `total_price_tax_incl`=".$priceOut*$product_quantity.", `total_price_tax_excl`=".$priceOut*$product_quantity.", `unit_price_tax_incl`=".$priceOut.", `unit_price_tax_excl`=".$priceOut.", `product_quantity` = ".$product_quantity.", `cartnum` = '".$cartnum."' WHERE `id_order_detail` =".$id_order_detail;
        $line_up = Db::getInstance()->execute($upd_line);
        if (!$line_up) $errors[] = 'Can`t update order line with reference = '.$line['lagerId'];
        
        if ($replacementLagers)
          {
          $replacement = Db::getInstance()->getValue("SELECT `replacement` FROM `"._DB_PREFIX_."order_detail` WHERE `id_order_detail` = ".$id_order_detail);
          if (!$replacement)
           {
              $id_order_invoice = Db::getInstance()->getValue("SELECT `id_order_invoice` FROM `"._DB_PREFIX_."order_detail` WHERE `id_order_detail` = ".$id_order_detail);
              $sorder = Db::getInstance()->getValue("SELECT `sorder` FROM `"._DB_PREFIX_."order_detail` WHERE `id_order_detail` = ".$id_order_detail);
              $replacement_a = array();
              $replacement_a = explode(",",$replacementLagers);
           //   d($replacement_a);
              foreach ($replacement_a as $ref)
              {
                $sql_find_p = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = ".$ref;
                $product_id = Db::getInstance()->getValue($sql_find_p);
                
                $sql_find_p_dubl = "SELECT `product_id` FROM `"._DB_PREFIX_."order_detail` WHERE `id_order` = ".$line['orderId']." AND `product_reference` = '".$ref."'";   //Не учитываем на замену товар который уже есть в заказе
                $product_id_dubl = Db::getInstance()->getValue($sql_find_p_dubl);
                
                if ($product_id && !$product_id_dubl) {
                  $sorder = $sorder + 1;
                  $product = new Product($product_id,null,2,$filial_id);
                //  $price = $product->getPrice();
                  $x = new stdClass();
                  $price = Product::priceCalculation($filial_id,$product_id,null,216,null,null,1,null,1,true,2,false,true,false,$x,true,null,true,null,0,null);
                  $pr_ava = (int)$product->available_for_order;
                  //$qty = StockAvailable::getQuantityAvailableByProduct($product_id, null,$filial_id);
    
                  $repl_i="INSERT INTO `ps_order_detail`(`id_order`, `id_order_invoice`, `id_warehouse`, `id_shop`, `product_id`, `product_attribute_id`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, `product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, `reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_upc`, `product_reference`, `product_supplier_reference`, `product_weight`, `id_tax_rules_group`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, `total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `loginmodified`, `replacement`, `who_repl`, `realqty`, `sorder`, `cartnum`, `datemodified`) VALUES ($reference_ord,$id_order_invoice,0,$filial_id,$product_id,0, '".$product->name."',0,1,0,0, 0,$price,0,0,0, 0,0,0,'','',$ref, '',$product->weight,0,0,'',0,0, 0,0,'',0,NULL,$price, $price,$price,$price,0,0, 0,$price,0,$id_sborshik,'',$reference,0, $sorder,NULL,'$date')";
                  if ($pr_ava) Db::getInstance()->execute($repl_i);
                 }
              }
            }        
          }
        $upd_line_r = "UPDATE `"._DB_PREFIX_."order_detail` SET `replacement`='".$replacementLagers."' WHERE `id_order_detail` =".$id_order_detail;
        if ($replacementLagers == 0) $upd_line_r = "UPDATE `"._DB_PREFIX_."order_detail` SET `replacement`= NULL WHERE `id_order_detail` =".$id_order_detail;
        $line_up_r = Db::getInstance()->execute($upd_line_r);
        if (!$line_up_r) $errors[] = 'Can`t update order line (dont add replacement) with reference = '.$line['lagerId'];
        }
        //Пересчет заказа
        $sql_get_sum = "SELECT ROUND(SUM(`product_quantity`*`unit_price_tax_incl`),2) AS itog FROM `"._DB_PREFIX_."order_detail` WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
        $order_final_sum = Db::getInstance()->getValue($sql_get_sum);
        $sql_udate_sum_order = "UPDATE `"._DB_PREFIX_."orders` SET `total_products` = ".$order_final_sum.", `total_products_wt` = ".$order_final_sum." WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
        $upd_sum_order = Db::getInstance()->execute($sql_udate_sum_order);
        $sql_udate_Ssum_order = "UPDATE `"._DB_PREFIX_."orders` SET `total_paid` = (`total_products` + `total_shipping` - `total_discounts`), `total_paid_tax_incl` = (`total_products` + `total_shipping` - `total_discounts`), `total_paid_tax_excl` = (`total_products` + `total_shipping` - `total_discounts`), `total_paid_real` = (`total_products` + `total_shipping` - `total_discounts`) WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
        if ($carrier_id == 37 || $carrier_id == 50) $sql_udate_Ssum_order = "UPDATE `"._DB_PREFIX_."orders` SET `total_paid` = (`total_products` - `total_discounts`), `total_paid_tax_incl` = (`total_products` - `total_discounts`), `total_paid_tax_excl` = (`total_products` - `total_discounts`), `total_paid_real` = (`total_products` - `total_discounts`) WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId']; //Fix for Нова Пошта и JUSTIN
        
        $upd_Ssum_order = Db::getInstance()->execute($sql_udate_Ssum_order);
        //Пересчет веса
      //  $sql_get_weight = "SELECT ROUND(SUM(`product_quantity`*`product_weight`),2) AS weight FROM `"._DB_PREFIX_."order_detail` WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
      //  $order_final_weight = Db::getInstance()->getValue($sql_get_weight);
      //  $sql_udate_final_weight = "UPDATE `"._DB_PREFIX_."order_carrier` SET `weight` = ".$order_final_weight." WHERE `id_order` = ".$OrderData_array['order']['FzShopOrder']['orderId'];
      //  $upd_final_weight = Db::getInstance()->execute($sql_udate_sum_order);
      }
    
    //if ($OrderData_array['order']['FzShopOrder']['filialId'] == 1614 && $OrderData_array['order']['FzShopOrder']['orderStatus'] == 16) //16 - старый режим, 923 - новый
    if ($OrderData_array['order']['FzShopOrder']['orderStatus'] == 923) //16 - старый режим, 923 - новый
        {
        include_once(_PS_MODULE_DIR_.'fozzy_print/fozzy_print.php');
        $print = new Fozzy_print();
        $printed = $print->printPDF((int)$OrderData_array['order']['FzShopOrder']['orderId'],0,$OrderData_array['order']['FzShopOrder']['filialId']);
        }
     //Britoff 19-05-2020 Внедрение оверсуммы - Старт
     if ($OrderData_array['order']['FzShopOrder']['orderStatus'] == 911 || $OrderData_array['order']['FzShopOrder']['orderStatus'] == 914) 
          {
              $order_ref = Db::getInstance()->getValue("SELECT `reference` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".(int)$OrderData_array['order']['FzShopOrder']['orderId']);
              $blocked_summ = (float)Db::getInstance()->getValue("SELECT `amount` FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = '".$order_ref."'");
              $type_summ = Db::getInstance()->getValue("SELECT `payment_method` FROM `"._DB_PREFIX_."order_payment` WHERE `order_reference` = '".$order_ref."'");
              $order_summ = (float)Db::getInstance()->getValue("SELECT `total_paid` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".(int)$OrderData_array['order']['FzShopOrder']['orderId']); 
              
              if ($type_summ == 'Liqpay')
                {
                if ($blocked_summ < $order_summ) 
                  {
                    $history = new OrderHistory();
                    $history->id_order = $id_order;
                    $history->id_employee = $this->context->employee->id;
                    $history->changeIdOrderState(927, $id_order);
                    $history->add();
                  }
                }
          
          }   
     //Britoff 19-05-2020 Внедрение оверсуммы - Старт
    $filename2 = _PS_ROOT_DIR_.'/modules/fozzy_orders/log/sborka/'.$id_order."_".date('d_m_Y_H_i_s')."_post_result.xml";
    $OrderData_xml->asXML($filename2);
    if (!$errors) return '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode></ConfirmResponse>';
    else return '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>1</errorCode><errorMessage>'.implode(",",$errors).'</errorMessage></ConfirmResponse>';     
  }

  //http://fozzyshop.com.ua/go_to_order?id_order=149642&email=a.britoff@gmail.com
  public function gotoorder($id_order = NULL, $email = 'a.britoff@gmail.com')      //Слушающий запрос от CallCentre, редирект на заказ
  {
 //  $beshnniy = $this->getSBbyINN(2957810698);
 //  dump($beshnniy);
 //  die();
 /* $order = new Order($id_order);
  $products = $order->getProducts();
  dump($products);
  die();*/
   $employee = new Employee();
   $employer = $employee->getByEmail($email);
   $id_employee = $employer->id; 
   $token = Tools::getAdminToken('AdminOrders' . (int) Tab::getIdFromClassName('AdminOrders') . (int) $id_employee);
   $link = 'https://fozzyshop.ua/operations/index.php?controller=AdminOrders&id_order='.$id_order.'&vieworder&token='.$token;
   header('Location: '.$link,true,301);
   die();
  }

  public function showStatuses()
  {
    $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
     if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetDictionaryData?dictionaryName=FzShopOrderStatusesList';
     else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetDictionaryData?dictionaryName=FzShopOrderStatusesList';
    
    $postData = $this->GetXML($link_to_send); 
    $Data_xml = simplexml_load_string($postData);
    dump($Data_xml);
    die();
  }
  
  public function showPayment()
  {
    $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
     if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetDictionaryData?dictionaryName=FzShopPaymentList';
     else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetDictionaryData?dictionaryName=FzShopPaymentList';
    
    $postData = $this->GetXML($link_to_send); 
    $Data_xml = simplexml_load_string($postData);
    dump($Data_xml);
    die();
  }
  
  public function showDelivery()
  {
    $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
     if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetDictionaryData?dictionaryName=FzShopDeliveryList';
     else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/GetDictionaryData?dictionaryName=FzShopDeliveryList';
    
    $postData = $this->GetXML($link_to_send); 
    $Data_xml = simplexml_load_string($postData);
    dump($Data_xml);
    die();
  } 
  
  public function putStatuses()
  {
    $states = OrderState::getOrderStates(2);
    $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
     if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutDictionaryData';
     else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutDictionaryData';
    
    $DictionaryDataList = new SimpleXMLElement('<DictionaryDataList xmlns:i="http://www.w3.org/2001/XMLSchema-instance"></DictionaryDataList>');
    $odictionaryData_o = $DictionaryDataList->addChild('dictionaryData');
    foreach ($states as $state) {
    $odictionaryData = $odictionaryData_o->addChild('DictionaryData');
    $odictionaryData->addChild('id', $state['id_order_state']);
    $odictionaryData->addChild('name', $state['name']);
    }
    $DictionaryDataList->addChild('dictionaryName','FzShopOrderStatusesList');
    $DictionaryDataList->asXML(_PS_ROOT_DRI_."/modules/fozzy_orders/log/send_status.xml");

    $result = $this->PostXML($link_to_send,$DictionaryDataList->asXML()); 
    if ($result != '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode></ConfirmResponse>')
        {
          dump($result);
          die();
        }
    else return 'Статусы обновлены';  
  }
  
  public function putDelivery()
  {
    $carriers1 = Carrier::getCarriers(2, true, false, false, null, 1);
    $carriers2 = Carrier::getCarriers(2, true, false, false, null, 2);
    $carriers = array_merge($carriers1,$carriers2);
    
    $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
     if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutDictionaryData';
     else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutDictionaryData';
     
    $DictionaryDataList = new SimpleXMLElement('<DictionaryDataList xmlns:i="http://www.w3.org/2001/XMLSchema-instance"></DictionaryDataList>');
    $odictionaryData_o = $DictionaryDataList->addChild('dictionaryData');
    foreach ($carriers as $carrier) {
    $odictionaryData = $odictionaryData_o->addChild('DictionaryData');
    $odictionaryData->addChild('id', $carrier['id_carrier']);
    $odictionaryData->addChild('name', $carrier['name']);
    }
    $DictionaryDataList->addChild('dictionaryName','FzShopDeliveryList');
    $DictionaryDataList->asXML(_PS_ROOT_DIR_."/modules/fozzy_orders/log/send_Delivery.xml");

    $result = $this->PostXML($link_to_send,$DictionaryDataList->asXML());       
    if ($result != '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode></ConfirmResponse>')
        {
          dump($result);
          die();
        }
    else return 'Перевозчики обновлены';
  }  

  public function putPayment()
  {
    $payments = PaymentModule::getInstalledPaymentModules();  
    $prod_mode = Configuration::get('FOZZY_ORDERS_LIVE_MODE');
     if ($prod_mode) $link_to_send = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutDictionaryData';
     else $link_to_send = 'https://test-bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PutDictionaryData';
     
    $DictionaryDataList = new SimpleXMLElement('<DictionaryDataList xmlns:i="http://www.w3.org/2001/XMLSchema-instance"></DictionaryDataList>');
    $odictionaryData_o = $DictionaryDataList->addChild('dictionaryData');
    foreach ($payments as $payment) {
    $odictionaryData = $odictionaryData_o->addChild('DictionaryData');
    $odictionaryData->addChild('id', $payment['id_module']);
    $odictionaryData->addChild('name', Module::getModuleName($payment['name']));
    }
    $odictionaryData = $odictionaryData_o->addChild('DictionaryData');
    $odictionaryData->addChild('id', 239);
    $odictionaryData->addChild('name', 'Оплата наличными при получении');
    $odictionaryData = $odictionaryData_o->addChild('DictionaryData');
    $odictionaryData->addChild('id', 246);
    $odictionaryData->addChild('name', 'Оплата по безналичному расчету');
    $odictionaryData = $odictionaryData_o->addChild('DictionaryData');
    $odictionaryData->addChild('id', 336);
    $odictionaryData->addChild('name', 'Оплата в отделении Новой Почты');
    
    $DictionaryDataList->addChild('dictionaryName','FzShopPaymentList');
    $DictionaryDataList->asXML(_PS_ROOT_DIR_."/modules/fozzy_orders/log/send_Payment.xml");

    $result = $this->PostXML($link_to_send,$DictionaryDataList->asXML());       
    if ($result != '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode></ConfirmResponse>') 
        {
          dump($result);
          die();
        }
        
    else return 'Оплаты обновлены';
  }  

//http://test.fozzyshop.com.ua/GetOrderForFozzyshop?id_order=112497
  
  private function GetXML($link)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
    $responce = curl_exec($ch);
    curl_close($ch); 
  return $responce; 
  }
   
  private function PostXML($link, $xmlRequest = NULL)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8'));
    if ($xmlRequest) curl_setopt($ch, CURLOPT_POSTFIELDS,$xmlRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
    $responce = curl_exec($ch);
    curl_close($ch); 
  return $responce; 
  }
    
  public function object2array($object) {
    return json_decode(json_encode($object), TRUE); 
  }
  
  public function getSBbyINN($inn = 0) {
    if ($inn == 0) return 0;
    $sql = "SELECT `id_sborshik` FROM `"._DB_PREFIX_."fozzy_logistic_sborshik` WHERE `INN` = '".$inn."'";
    $id = Db::getInstance()->getValue($sql);
    if (!$id) $id = 0;
    return $id; 
  }  
    
}
