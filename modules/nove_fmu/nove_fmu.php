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

class Nove_fmu extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'nove_fmu';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ФМУ ICOS');
        $this->description = $this->l('Отправляет данные о покупках в ФМУ');

        $this->confirmUninstall = $this->l('Вы точно хотите удалить это модуль?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('NOVE_FMU_LINK', 'https://corezoid.loqator.com.ua/api/1/json/public/4865/44bcacd00d345819f4db4a35ad9a5668546c2d9a');
        Configuration::updateValue('NOVE_FMU_CATEGORY', '300800,300800');
        Configuration::updateValue('NOVE_FMU_STATUS', '916');
        
        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionOrderStatusUpdate');
    }

    public function uninstall()
    {
        Configuration::deleteByName('NOVE_FMU_LINK');
        Configuration::deleteByName('NOVE_FMU_CATEGORY');
        Configuration::deleteByName('NOVE_FMU_STATUS');

        return parent::uninstall();
    }

    public function getContent()
    {
        $html = '';
        
        if (((bool)Tools::isSubmit('submit_save')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $html.$this->renderForm();
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
        $helper->submit_action = 'submit_save';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
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
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-link"></i>',
                        'desc' => $this->l('Ссылка на API'),
                        'name' => 'NOVE_FMU_LINK',
                        'label' => $this->l('Ссылка'),
                    ),
                    array(
                        'type' => 'text',
                        'col' => 3,
                        'prefix' => '<i class="icon icon-file"></i>',
                        'name' => 'NOVE_FMU_CATEGORY',
                        'desc' => $this->l('Категории для отправки через запятую'),
                        'label' => $this->l('Категории'),
                    ),
                    array(
                        'type' => 'text',
                        'col' => 3,
                        'prefix' => '<i class="icon icon-file"></i>',
                        'name' => 'NOVE_FMU_STATUS',
                        'desc' => $this->l('ID статуса заказа, по которому отправлять данные'),
                        'label' => $this->l('Статус'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'NOVE_FMU_LINK' => Configuration::get('NOVE_FMU_LINK'),
            'NOVE_FMU_CATEGORY' => Configuration::get('NOVE_FMU_CATEGORY'),
            'NOVE_FMU_STATUS' => Configuration::get('NOVE_FMU_STATUS'),
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
          //  $this->context->controller->addJS($this->_path.'views/js/back.js');
          //  $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }
    
    public function hookActionOrderStatusUpdate($params)  
    {
       
       $link = Configuration::get('NOVE_FMU_LINK');
       $categories = explode(",",Configuration::get('NOVE_FMU_CATEGORY'));
       $id_status_to_send = Configuration::get('NOVE_FMU_STATUS');
       
       $id_status = (int)$params['newOrderStatus']->id;
       $id_order = (int)$params['id_order'];
       
       if ($id_status == $id_status_to_send)
        {
         
         $order = new Order($id_order);
         $id_address = $order->id_address_delivery;
         $address = new Address($id_address);
         $id_customer = $order->id_customer;
         $customer = new Customer($id_customer);
         $customer_lastname = $customer->lastname;
         $customer_firstname = $customer->firstname;
         $customer_phone = $address->phone_mobile;
         if  (!$customer_phone || $customer_phone == '' || $customer_phone == " " ) $customer_phone = $address->phone;
         $products = $order->getProducts();
         
         $data_to_send = array();
         $data_to_send['firstname'] = $customer_firstname;
         $data_to_send['lastname'] = $customer_lastname;
         $data_to_send['phone'] = $customer_phone;
         
         $i=0;
         foreach ($products as $product) {
            if (!in_array($product['id_category_default'], $categories))
                            {
                            continue;
                            }
              $data_to_send['products'][$i]['product_name'] = $product['product_name'];
              $data_to_send['products'][$i]['quantity'] = number_format ($product['product_quantity'],0,'.','');
            $i++;
          }
         
          $request = json_encode($data_to_send); 
          if ($i>0) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $link);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','accept: application/json'));
          curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
          $responce = curl_exec($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);
          }
        }
        
       return;
    }
    
    
public function object2array($object) {
  return json_decode(json_encode($object), TRUE); 
}
  
    
}
