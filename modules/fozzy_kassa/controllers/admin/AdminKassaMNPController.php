<?php

class AdminKassaMNPController extends AdminController
{

    public $id_order;
    public $id_route;
    public $date;
    public $route_select;
    public $route_list;
    public $form_num;
    
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
        $this->toolbar_title = $this->l('Принятые суммы');
    }

    public function initToolBar()
    {
        return true;
    }

    
  private function getStartForm()
    {
     $form = array(
            'input' => array(
                array(
                  'type' => 'date',
                  'label' => $this->l('Дата'),
                  'name' => 'm_date',
                  'size' => 10,
                  'required' => true
                )
              ),
            'submit' => array(
                'title' => $this->l('Открыть'),
                'id' => 'submitOpen',
                'name' => 'submitOpen',
                'icon' => 'process-icon-download'
              ),
      );
      return $form;
    }
  
  public function getRoutes($date)
	{
		$routes = array();

    $id_shop = (int)$this->context->shop->id;

		$sql_routes = "SELECT * FROM `"._DB_PREFIX_."fozzy_kassa_marshrut` WHERE `pr_date` = '".$date."' AND `id_filial` =".$id_shop;
    $routes = Db::getInstance()->executeS($sql_routes);
		return $routes;
	}
  
  public function renderKpis()
    {
     $kpis = array();
     $id_shop = (int)$this->context->shop->id;
     
     $summs_1 = $this->getRoutes($this->date);
     $summs = array();
     
     if (!$summs_1) return;
     
     foreach ($summs_1 as $key1=>$summa1)
     {
      $summs[$summa1['zone_name']] = $summs[$summa1['zone_name']] + $summa1['summ_vodila'];
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
  
  private function getPointsList()
    {
     $fields_list = array(
			'pr_date' => array(
				'title' => $this->l('Дата принятия'),
				'type' => 'date',
			),
      'mar_date' => array(
				'title' => $this->l('Дата маршрута'),
				'type' => 'date',
			),
      'zone_name' => array(
				'title' => $this->l('Зона'),
				'type' => 'text',
			),
      'mar_num' => array(
				'title' => $this->l('Маршрут'),
				'type' => 'text',
			),
			'vodila' => array(
				'title' => $this->l('Водитель'),
				'type' => 'text',
			),
			'kassir' => array(
				'title' => $this->l('Кассир'),
				'type' => 'text',
			),
			'summ_marshrut' => array(
				'title' => $this->l('Сумма маршрута'),
				'type' => 'text',
			),
			'summ_vodila' => array(
				'title' => $this->l('Принято'),
				'type' => 'text',
			),
			'raznitsa' => array(
				'title' => $this->l('Разница'),
				'type' => 'text',
			)
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id';
    $helper->has_actions = false;
    $helper->no_link = true;     
    $helper->show_toolbar = true;
    
		$helper->title = $this->l('Принятые маршруты');
		$helper->table = 'fozzy_kassa_marshrut';
    $helper->token = Tools::GetValue('token');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$routes = $this->getRoutes($this->date);
		if (is_array($routes) && count($routes))
			return $helper->generateList($routes, $fields_list);    
		else
			return false;
    }
  
  public function renderForm()
    {
      switch ((int)$this->form_num)
      {
       case 0:
        $this->fields_value = array('m_date' => date('Y-m-d'));
        $this->fields_form = $this->getStartForm();
       break;
       case 1:
        $this->fields_value = $this->getMainValues($this->date);
        $this->fields_form = $this->getStartForm();
       break;
      }
      
      return parent::renderForm();
    }
  
  protected function getMainValues($date)
    {
        return array(
            'm_date' => $date
        );
    }
  
	public function renderView()
	{ 
		return parent::renderView();             
	}

	public function initContent()
    {
        $this->initToolBarTitle();
        $this->initToolbar();
      //  $this->content .= $this->renderView();
        $this->content .= $this->renderForm();
        $this->content .= $this->renderKpis();
        $this->content .= $this->route_list;

        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }
    
	public function postProcess()
	{
    
    if( Tools::GetValue('submitOpen') == 1 )
      {
        $this->date = Tools::GetValue('m_date');
        $this->form_num = 1; 
        $this->route_list = $this->getPointsList();  
      }

    parent::postProcess();
	}
	

}