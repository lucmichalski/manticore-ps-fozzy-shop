<?php

if (!defined('_PS_VERSION_'))
	exit;


class Novekassa extends Module
{
	protected $html = '';

	public function __construct()
	{
		$this->name = 'novekassa';
		$this->tab = 'pricing_promotion';
		$this->version = '1.0.0';
		$this->author = 'Novevision.com, Britoff A.';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Cash-desk area');
		$this->description = $this->l('Provide a custom products to Cash-desk area.');
	}

	public function install()
	{
		if (!parent::install() 
    || !$this->registerHook('displayShoppingCart')
    
    )
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}

  public function hookdisplayShoppingCartFooter($params) {
  /*
  $cart_products = $params['products'];
  $cart = $params['cart'];
  $customer = (int)$cart->id_customer; 
  $id_lang = (int)$this->context->language->id;
  $id_shop = (int)$this->context->shop->id;
  
  $for_cart_products_cat = 300621; 
  
  $category = new Category ($for_cart_products_cat, $id_lang, $id_shop);
  $for_cart_products = $category->getProducts($id_lang, 1, 16, null, null, false, true, false, 16, true, null);
  
    $this->context->smarty->assign(array(
            //  'new_client' => $new_client,
              'id_cart' => $cart->id,
              'file' => __DIR__.'/product-slider.tpl',
              'for_cart_products' => $for_cart_products,
          ));
        
   // if ($customer == 117) return $this->display(__FILE__, 'novekassa.tpl');
   return $this->display(__FILE__, 'novekassa.tpl');       */
   }

  public function hookdisplayShoppingCart($params) {
  //dump($params);
  //die;
  //$cart_products = $params['products'];
  $cart = $params['cart'];
  $customer = (int)$cart->id_customer; 
  $id_lang = (int)$this->context->language->id;
  $id_shop = (int)$this->context->shop->id;
  
  $for_cart_products_cat = 300621; 
  
  $category = new Category ($for_cart_products_cat, $id_lang, $id_shop);
  $for_cart_products = $category->getProducts($id_lang, 1, 16, null, null, false, true, false, 16, true, null);
  
    $this->context->smarty->assign(array(
              'id_cart' => $cart->id,
              'file' => __DIR__.'/product-slider.tpl',
              'for_cart_products' => $for_cart_products,
          ));
        
   // if ($customer == 117) return $this->display(__FILE__, 'novekassa.tpl');
   return $this->display(__FILE__, 'novekassa.tpl');
   }
   
   
  public function AddProduct($id_cart) {

  }
  
  
  
  
  
  
  
}