<?php

if (!defined('_PS_VERSION_'))
	exit;


class Fozzy_twoplusone extends Module
{
	protected $html = '';

	public function __construct()
	{
		$this->name = 'fozzy_twoplusone';
		$this->tab = 'pricing_promotion';
		$this->version = '1.0.0';
		$this->author = 'Novevision.com, Britoff A.';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Акция 2+1');
		$this->description = $this->l('Добавляем акцию 2 + 1 за 10 коп');
	}

	public function install()
	{
		if (!parent::install() 
    || !$this->registerHook('actionCartSave')
    
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
  
  public function hookactionCartSave ($params) {
   
  $cart = $params['cart'];
  if ($cart) {
      $id_customer = $cart->id_customer;
      
      $products = $cart->getProducts(true);
      $pizzas = array(71073,71074,71075,71076,71077,71078,71079,71080,71081);
      $gift = 57195;
      $gift_added = 0;
      $count_pizzas = 0;
      
      if ($products) foreach ($products as $product)
        {
          if (in_array($product['id_product'],$pizzas)) 
          {
           $count_pizzas += $product['cart_quantity'];
          }
          if ($product['id_product'] == $gift) 
          {
           $gift_added += $product['cart_quantity'];
          }
        }
    
       $need_colas = intdiv( $count_pizzas, 2) - $gift_added; 
       $all_need_colas = intdiv( $count_pizzas, 2);
       
       if ($all_need_colas != $gift_added) 
       {
        $cart->updateQty($need_colas, $gift);
        $cart->save();
       }
       return true;
    }
  }
  
}