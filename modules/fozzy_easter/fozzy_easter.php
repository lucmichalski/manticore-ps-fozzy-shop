<?php

if (!defined('_PS_VERSION_'))
	exit;


class Fozzy_easter extends Module
{
	protected $html = '';

	public function __construct()
	{
		$this->name = 'fozzy_easter';
		$this->tab = 'pricing_promotion';
		$this->version = '1.0.0';
		$this->author = 'Novevision.com, Britoff A.';
		$this->need_instance = 1;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Customer rewards carusel');
		$this->description = $this->l('Provide a loyalty program to your customers.');
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

  public function hookdisplayShoppingCart($params) {
  
  $cart = $params['cart'];
 
  $rules = $cart->getProducts(true);
  
  $gift_added = 0;
  if ($rules) foreach ($rules as $rule)
    {
      if ($rule['id_product'] == 57195) 
      {
       $gift_added = 1;
       break;
      }
      else $gift_added = 0;
    }
  $paska_gut = 0;
  $paska_gut = (int)StockAvailable::getQuantityAvailableByProduct(57195,null,$id_shop);
  $cart_summ = $cart->getOrderTotal(true,Cart::ONLY_PRODUCTS);
 // $this->context->controller->addCSS($this->_path.'fozzy_easter.css', 'all');  
  
//  if ((int)$cart->id_customer == 5)
//  {
  
  if ( !$gift_added && $paska_gut > 0 && $cart_summ > 999 ) 
    {
    $this->context->smarty->assign(array(
              'id_cart' => $cart->id,
          ));
    return $this->display(__FILE__, 'fozzy_easter.tpl');
   }
   
  // }
   return false;
  }


  public function AddGift($id_cart) {
  
  $cart = new Cart ($id_cart);
  
  $products  =  $cart->getProducts();
  $id_customer = $cart->id_customer;
  $id_shop = $cart->id_shop;
  $paska_gut = 0;
  $paska_gut = (int)StockAvailable::getQuantityAvailableByProduct(57195,null,$id_shop);

   if ($paska_gut && $paska_gut > 0)
    {
      $cart -> updateQty (1,57195);
      header( 'Location: /cart?action=show', true, 303 );
      die();
    }
   
  header( 'Location: /cart?action=show', true, 303 );
  die();
  }
  
  
  
  
  
  
  
}