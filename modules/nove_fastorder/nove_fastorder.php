<?php
/**
* 2007-2018 PrestaShop
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
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Nove_fastorder extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'nove_fastorder';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fast order your favorite products');
        $this->description = $this->l('Order all your favorite products in 1 click');

        $this->confirmUninstall = $this->l('Are you sure?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('NOVE_FASTORDER_repetitions', 3);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayMainMenu');
    }

    public function uninstall()
    {
        Configuration::deleteByName('NOVE_FASTORDER_LIVE_MODE');
        Configuration::deleteByName('NOVE_FASTORDER_repetitions');
        
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
        if (((bool)Tools::isSubmit('submitNove_fastorderModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);


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
        $helper->submit_action = 'submitNove_fastorderModule';
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
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Number of repetitions of goods in orders'),
                        'name' => 'NOVE_FASTORDER_repetitions',
                        'label' => $this->l('Number of repetitions'),
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
            'NOVE_FASTORDER_repetitions' => Configuration::get('NOVE_FASTORDER_repetitions', '5'),
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
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
   /* public function hookHeader()
    {

    }      */

    public function hookDisplayHeader()
    {
        $this->context->controller->addJqueryPlugin(array('fancybox')); 
        $this->context->controller->addJS($this->_path.'/views/js/front_03.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        
    }

    public function hookDisplayTop()
    {

    }
    
    public function hookdisplayMainMenu()
    {
      $id_customer = (int)$this->context->customer->id;
      if ($id_customer) $fast_customer = $id_customer;
      else $fast_customer = 0;
      $this->smarty->assign(
				array(
					'fast_customer' => $fast_customer,
				)
        ); 
      return $this->display(__FILE__, 'footer.tpl');
    }
    
    public function _favorder($qty_check = 3, $cart_null = false)
    {
      $id_customer = (int)$this->context->customer->id;
      $products = array();
   //    $cart_null = 1;
      // Add cart if no cart found
      if (!$this->context->cart->id) {
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
            }
      
      $sql_fav_products = "SELECT od.`product_id`, od.`product_attribute_id`, ROUND(SUM(od.`product_quantity`)/count(*),0) AS quantity FROM `"._DB_PREFIX_."order_detail` od, `"._DB_PREFIX_."orders` o WHERE od.`id_order` = o.`id_order` AND o.`id_customer` = ".$id_customer." GROUP BY od.`product_id` HAVING count(*)>".$qty_check;  
      $products = Db::getInstance()->executeS($sql_fav_products);
      foreach ($products as $key=>$product)
              {
                $qtt = Product::getQuantity($product['product_id'],$product['product_attribute_id']);
                if ( $qtt == 0) unset($products[$key]);
              }
      unset($key);
      unset($product);
      $products = array_values($products);
     // d($products);
        if ($products) {
          if ($cart_null) {
            $cart = new Cart((int)$this->context->cart->id);
            $old_cart = $cart->getProducts();
            foreach ($old_cart as $old_product)
              {
                $cart->deleteProduct($old_product['id_product'],$old_product['id_product_attribute']);
              }
            foreach ($products as $product)
              {
                $cart->updateQty(1,$product['product_id'],$product['product_attribute_id']);
              }
          }
          else
          {
            $cart = new Cart((int)$this->context->cart->id);
            $old_cart = $cart->getProducts();
            foreach ($old_cart as $old_product)
              {
                foreach ($products as $key=>$product)
                  {
                    if ( $old_product['id_product'] == $product['product_id'] && $old_product['id_product_attribute'] == $product['product_attribute_id'] ) {
                      unset($products[$key]);
                    }
                  }
                 
              }
             foreach ($products as $product)
              {
                $cart->updateQty(1,$product['product_id'],$product['product_attribute_id']);
              }
          }
        }
        else
        {
          $sql_get_last_order = "SELECT MAX(id_order) AS id_order FROM "._DB_PREFIX_."orders WHERE id_customer = ".$id_customer;
          $last_order = Db::getInstance()->executeS($sql_get_last_order);
          $id_last_order = (int)$last_order[0]['id_order'];
          $sql_fav_products = "SELECT od.`product_id`, od.`product_attribute_id`, od.`product_quantity` AS quantity FROM `"._DB_PREFIX_."order_detail` od, `"._DB_PREFIX_."orders` o WHERE od.`id_order` = o.`id_order` AND o.`id_customer` = ".$id_customer." AND o.`id_order` = ".$id_last_order;
          $last_products = Db::getInstance()->executeS($sql_fav_products);
          if ($last_products) {
            $cart = new Cart((int)$this->context->cart->id);
            $old_cart = $cart->getProducts();
            foreach ($old_cart as $old_product)
              {
                foreach ($last_products as $key=>$product)
                  {
                    if ( $old_product['id_product'] == $product['product_id'] && $old_product['id_product_attribute'] == $product['product_attribute_id'] ) {
                      unset($last_products[$key]);
                    }
                  }
                 
              }
             foreach ($last_products as $product)
              {
                $cart->updateQty(1,$product['product_id'],$product['product_attribute_id']);
              }
          }
        }
    Tools::redirect('/cart?action=show');
    }
}
