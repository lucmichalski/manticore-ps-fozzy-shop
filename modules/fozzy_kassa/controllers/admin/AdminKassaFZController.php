<?php

class AdminKassaFZController extends AdminController
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
        $this->toolbar_title = $this->l('Не закрытые чеки');
    }

    public function initToolBar()
    {
        return true;
    }

  private function getOrdersList()
    {
     $fields_list = array(
      'id_order' => array(
				'title' => $this->l('Заказ'),
				'type' => 'text',
        'class' => 'id_order'
			),
      'osname' => array(
				'title' => $this->l('Статус'),
				'type' => 'date',
			),
      'dateofdelivery' => array(
				'title' => $this->l('Дата отгрузки'),
				'type' => 'date',
			),
      'zone_name' => array(
				'title' => $this->l('Зона отгрузки'),
				'type' => 'text',
			),
      'payment' => array(
				'title' => $this->l('Тип оплаты'),
				'type' => 'text',
			),
			'fiskal' => array(
				'title' => $this->l('Сумма по чеку'),
				'type' => 'text',
			)
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_order';

		$helper->title = $this->l('Не закрытые чеки');
		$helper->table = 'orders';
    $token = Tools::getAdminToken('AdminOrders'.intval(Tab::getIdFromClassName('AdminOrders')).intval($this->context->employee->id));
    $helper->token = $token;

		$helper->currentIndex = '?controller=AdminOrders&vieworder';
		$orders = $this->getOrders();
		if (is_array($orders) && count($orders))
			return $helper->generateList($orders, $fields_list);
		else
			return false;
    }
  
  private function getOrders()
    {
      $id_shop = (int)$this->context->shop->id;
      $id_lang = (int)$this->context->language-id;
      $sql_sel_pr = "SELECT a.`id_order`, a.`dateofdelivery`, a.`zone_name`, a.`zone`, osl.`name` AS `osname`, a.`fiskal`, a.`id_prihod`, a.`payment` FROM `"._DB_PREFIX_."orders` a
      LEFT JOIN `"._DB_PREFIX_."order_state` os ON (os.`id_order_state` = a.`current_state`)
      LEFT JOIN `"._DB_PREFIX_."order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ".$id_lang.")
      WHERE a.`fiskal` > 0 AND a.`id_vodila` = 0 AND a.`date_add` > '2020-01-01' AND a.`current_state` NOT IN (933) AND a.`summ_to_vz` = 0 AND (a.`payment` = 'Оплата наличными при получении' OR a.`payment` = 'Оплата при отриманні') AND a.`id_shop` =".$id_shop." AND 933 NOT IN (SELECT `id_order_state` FROM `"._DB_PREFIX_."order_history` WHERE `id_order` = a.`id_order`)";

      $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);
      return $sql_sel_pr_ar;
    }
  
  private function getOrdersSum()
    {
      $id_shop = (int)$this->context->shop->id;
      $sql_sel_pr = "SELECT SUM(a.`fiskal`) as summa, `zone`, a.`zone_name` FROM `"._DB_PREFIX_."orders` a WHERE a.`fiskal` > 0 AND a.`id_vodila` = 0 AND a.`date_add` > '2020-01-01' AND a.`summ_to_vz` = 0 AND (a.`payment` = 'Оплата наличными при получении' OR a.`payment` = 'Оплата при отриманні') AND a.`current_state` NOT IN (933) AND a.`id_shop` =".$id_shop." AND 933 NOT IN (SELECT o.`id_order_state` FROM `"._DB_PREFIX_."order_history` o WHERE o.`id_order` = a.`id_order`) "." GROUP BY a.`zone` ORDER BY a.`zone`";
      $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);

      return $sql_sel_pr_ar;
    }
  
  public function renderKpis()
    {
     $kpis = array();
     $id_shop = (int)$this->context->shop->id;
     
     $summs = $this->getOrdersSum();
     
     foreach ($summs as $key=>$summa)
     {
     $c = $key+1;
     $helper = new HelperKpi();
     $helper->id = 'box-new-order';
     $helper->icon = 'icon-cart-plus';
     $helper->color = 'color'.$c;
     $helper->title = $summa['zone_name'];
     $helper->value = $summa['summa']; 
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