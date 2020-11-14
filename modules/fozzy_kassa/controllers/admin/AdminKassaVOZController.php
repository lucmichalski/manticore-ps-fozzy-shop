<?php

class AdminKassaVOZController extends AdminController
{

    public function __construct()
    {
	
		$this->display = 'view';
    $this->bootstrap = true;     
  
        parent::__construct();
    }


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title = $this->l('Не закрытые маршруты');
    }

    public function initToolBar()
    {
        return true;
    }

  private function getOrdersList()
    {
     $fields_list = array(
      'date_mar' => array(
				'title' => $this->l('Дата'),
				'type' => 'date'
			),
      'zone_name' => array(
				'title' => $this->l('Зона сборки'),
				'type' => 'text',
			),
      'mar_num' => array(
				'title' => $this->l('Номер маршрута'),
				'type' => 'text',
			),
			'vodila' => array(
				'title' => $this->l('Водитель'),
				'type' => 'text',
			),
			'summa' => array(
				'title' => $this->l('Сумма'),
				'type' => 'text',
			)
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_order';
		$helper->has_actions = false;
    $helper->no_link = true;
		$helper->show_toolbar = false;

		$helper->title = $this->l('Не закрытые маршруты');
		$helper->table = 'fozzy_kassa_prihod';
    $helper->token = Tools::GetValue('token');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$routes = $this->getRoutes();
		if (is_array($routes) && count($routes))
			return $helper->generateList($routes, $fields_list);
		else
			return false;
    }
  
  private function getRoutes()
    {
     $id_shop = (int)$this->context->shop->id;
     $sql_routes = "SELECT `date_mar`, `mar_num`, `vodila`, SUM(chek_summ*nal) as summa, `zone_name` FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `id_filial` = ".$id_shop." AND `closed` = 0 GROUP BY `date_mar`, `mar_num` ORDER BY `date_mar`, `mar_num`";
  //   $sql_routes = "SELECT `date_mar`, `mar_num`, `vodila`, SUM(chek_summ) OVER (PARTITION BY `payment` ORDER BY `date_mar`, `mar_num`, `payment`) as summa, `zone_name` FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `id_filial` = ".$id_shop." AND `closed` = 0 GROUP BY `date_mar`, `mar_num` ORDER BY `date_mar`, `mar_num`, `payment`";
  //   dump($sql_routes);
     $routes = Db::getInstance()->executeS($sql_routes);
     return $routes;
    }
  
  public function renderKpis()
    {
     $kpis = array();
     $id_shop = (int)$this->context->shop->id;
     
     $summs_1 = $this->getRoutes();
     $summs = array();
     
     foreach ($summs_1 as $key1=>$summa1)
     {
      $summs[$summa1['zone_name']] = $summs[$summa1['zone_name']] + $summa1['summa'];
     }
     $c = 0;
     foreach ($summs as $key=>$summa)
     {
     $c++;
     $helper = new HelperKpi();
     $helper->id = 'box-new-order';
     $helper->icon = 'icon-cart-plus';
     $helper->color = 'color'.$c;
     $helper->title = $key;
     $helper->value = $summa; 
     $kpis[] = $helper->generate();
     }
     
     $helper = new HelperKpiRow();
     $helper->refresh = false;
     $helper->kpis = $kpis;

     return $helper->generate();   
    }
  
  public function renderForm()
    {
      return parent::renderForm();
    }

	public function renderView()
	{ 
		return parent::renderView();             
	}

	public function initContent()
    {
        $this->initToolBarTitle();
        $this->initToolbar();
        $this->content .= $this->renderKpis();
      //  $this->content .= $this->renderView();
      //  $this->content .= $this->renderForm();
        $this->content .= $this->getOrdersList();

        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }
    
	public function postProcess()
	{
    parent::postProcess();
	}
	

}