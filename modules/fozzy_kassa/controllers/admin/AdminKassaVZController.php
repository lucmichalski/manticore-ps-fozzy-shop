<?php

class AdminKassaVZController extends AdminController
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
      //  $this->addJS('/modules/fozzy_kassa/views/js/vz_01.js');
        //return parent::setMedia();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title = $this->l('Погашение возвратов');
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
      'zone_name' => array(
				'title' => $this->l('Склад'),
				'type' => 'text',
			),
      'payment' => array(
				'title' => $this->l('Тип оплаты'),
				'type' => 'text',
			),
			'fiskal' => array(
				'title' => $this->l('Сумма по чеку'),
				'type' => 'text',
			),
			'summ_to_vz' => array(
				'title' => $this->l('Сумма к погашению'),
				'type' => 'input',
			),
      'summ_vz' => array(
				'title' => $this->l('Сумма возвращенная'),
				'type' => 'input',
        'class' => 'summ_to_vz'
			),
	/*		'returned' => array(
				'title' => $this->l('Вернуть'),
				'type' => 'bool',
        'active' => 'full_return',
        'class' => 'link_to_change'
			)      */
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_order';
		$helper->has_actions = false;
    $helper->no_link = true;
		$helper->show_toolbar = false;

		$helper->title = $this->l('Заказы к возврату');
		$helper->table = 'orders';
    $helper->token = Tools::GetValue('token');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$orders = $this->getOrders();
		if (is_array($orders) && count($orders))
			return $helper->generateList($orders, $fields_list);
		else
			return false;
    }
  
  private function getOrders()
    {
      $id_shop = (int)$this->context->shop->id;
      $sql_sel_pr = "SELECT `id_order`, `fiskal`, 
      CASE
    WHEN `summ_to_vz` = 0 
        THEN `fiskal`
    WHEN `summ_to_vz` > 0 
        THEN `summ_to_vz`
END AS `summ_to_vz`, `summ_to_vz` as `summ_vz`, `id_prihod`, `payment`, `zone_name` FROM `"._DB_PREFIX_."orders` WHERE `current_state` = 933 AND `id_shop` =".$id_shop;
      $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);
      return $sql_sel_pr_ar;
    }
  private function getOrdersSum()
    {
      $id_shop = (int)$this->context->shop->id;
      $sql_sel_pr = "SELECT SUM(CASE
    WHEN `summ_to_vz` = 0 
        THEN `fiskal`
    WHEN `summ_to_vz` > 0 
        THEN `summ_to_vz`
END ) as summa, `zone_name` FROM `"._DB_PREFIX_."orders` WHERE `current_state` = 933 AND `payment` = 'Оплата наличными при получении' AND `id_shop` =".$id_shop." GROUP BY `zone` ORDER BY `zone`";
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
      //  $this->content .= $this->renderView();
        $this->content .= $this->renderKpis();
        $this->content .= $this->getOrdersList();

        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }
    
	public function postProcess()
	{
    
    if (Tools::isSubmit('full_returnorders'))
     {
      $id_order = (int)Tools::GetValue('id_order');
      $summ = (float)Tools::GetValue('summ');

      $date = date('Y-m-d');

      $sql_sel_pr = "SELECT `id_prihod`, `fiskal`, `summ_to_vz` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order;
      $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);
      $id_prihod = (int)$sql_sel_pr_ar[0]['id_prihod'];
      $fiskal = (float)$sql_sel_pr_ar[0]['fiskal'];
      $summ_to_vz = (float)$sql_sel_pr_ar[0]['summ_to_vz'];
      
      if ($summ > 0)
      {
      $sql_upd_pr = "UPDATE `"._DB_PREFIX_."fozzy_kassa_prihod` SET `st_oplat_vozvr` = 1, `vzvshno` = ".$summ.", `date_vozvr_chek` = '".$date."' WHERE `id_prihod` = ".$id_prihod;
      }
      else
      {
      $sql_upd_pr = "UPDATE `"._DB_PREFIX_."fozzy_kassa_prihod` SET `st_oplat_vozvr` = 1, `vzvshno` = `vozvrat`, `date_vozvr_chek` = '".$date."' WHERE `id_prihod` = ".$id_prihod;
      }
      Db::getInstance()->execute($sql_upd_pr);
      
      if ($fiskal == $summ)
      {
       $history = new OrderHistory();
       $history->id_order = $id_order;
       $history->id_employee = $this->context->employee->id;
       $history->changeIdOrderState(6, $id_order);  //Статус - Отклонен
       $history->add();
      }
      else
      {
       $history = new OrderHistory();
       $history->id_order = $id_order;
       $history->id_employee = $this->context->employee->id;
       $history->changeIdOrderState(916, $id_order);  //Статус - Закрыт
       $history->add();
      }
      
      
     }              
    parent::postProcess();
	}
	

}