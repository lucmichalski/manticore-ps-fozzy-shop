<?php

class AdminKassaMNController extends AdminController
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
      $this->addJS('/modules/fozzy_kassa/views/js/jquery.mask.min.js');
      $this->addJS('/modules/fozzy_kassa/views/js/mn_10.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title = $this->l('Приём денег');
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
  private function getRouteSelect()
    {
     $id_shop = (int)$this->context->shop->id;
    
     $sql_routes = "SELECT `mar_num`, CONCAT(`mar_num`,' - ',`vodila`,' - ',COALESCE(`zone_name`,'')) as name FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `date_mar` = '".$this->date."' AND `id_filial` = ".$id_shop." GROUP BY `mar_num` ORDER BY `mar_num`";
     $routes = Db::getInstance()->executeS($sql_routes);

     $form_vals = $this->fields_value; 
     if ($this->context->employee->id == 1)
          {
     //      dump($form_vals);
     //      die();
          }     
     if ($form_vals['m_code'])
     {
     $form = array(
            'input' => array(
                array(
                  'type' => 'text',
                  'label' => $this->l('Дата'),
                  'name' => 'm_date',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true
                ),
                array(
      						'type' => 'select',
      						'label' => $this->l('Маршрут №:'),
      						'name' => 'm_code',
      						'class' => 'fixed-width-xxl',
      						'options' => array(
      							'query' => $routes,
      							'id' => 'mar_num',
      							'name' => 'name'
      						),
      					),
                array(
                  'type' => 'text',
                  'label' => $this->l('Точек в маршруте'),
                  'name' => 'm_points',
                  'size' => 10,
                  'readonly' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Итого по маршруту'),
                  'name' => 'm_summ_to_close',
                  'size' => 10,
                  'readonly' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Внесенная сумма'),
                  'name' => 'm_summ_from_vodila',
                  'size' => 10,
                  'readonly' => !$form_vals['m_vodila_mm'] ? false : true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Разница'),
                  'name' => 'm_summ_r',
                  'size' => 10,
                  'readonly' => true
                ),
              ),
            'submit' => array(
                'title' => $this->l('Открыть маршрут'),
                'id' => 'submitOpenRoute',
                'name' => 'submitOpenRoute',
                'icon' => 'process-icon-download'
              ) 
      );
      if (!$form_vals['m_vodila_mm']) {
           $form['buttons'] = array(
                array(
                'title' => $this->l('Закрыть маршрут'),
                'id' => 'submitSaveRoute',
                'name' => 'submitSaveRoute',
                'icon' => 'process-icon-save',
                'type' => 'submit'
                ) 
                
            );
        }
      }
      else
      {
      $form = array(
            'input' => array(
                array(
                  'type' => 'text',
                  'label' => $this->l('Дата'),
                  'name' => 'm_date',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true
                ),
                array(
      						'type' => 'select',
      						'label' => $this->l('Маршрут №:'),
      						'name' => 'm_code',
      						'class' => 'fixed-width-xxl',
      						'options' => array(
      							'query' => $routes,
      							'id' => 'mar_num',
      							'name' => 'name'
      						),
      					)
              ),
            'submit' => array(
                'title' => $this->l('Открыть маршрут'),
                'id' => 'submitOpenRoute',
                'name' => 'submitOpenRoute',
                'icon' => 'process-icon-download'
              ) 
      );
      }
      return $form;
    }
  
  private function getPointsList()
    {
     $fields_list = array(
			'id_prihod' => array(
				'title' => $this->l('ID'),
				'type' => 'text',
			),
      'id_order' => array(
				'title' => $this->l('Заказ'),
				'type' => 'text',
			),
			'adr_name' => array(
				'title' => $this->l('Клиент'),
				'type' => 'text',
			),
			'adr' => array(
				'title' => $this->l('Адрес'),
				'type' => 'text',
			),
			'weight' => array(
				'title' => $this->l('Вес'),
				'type' => 'text',
			),
			'phone' => array(
				'title' => $this->l('Телефон'),
				'type' => 'text',
			),
			'payment' => array(
				'title' => $this->l('Оплата'),
				'type' => 'text',
			),
			'chek_summ' => array(
				'title' => $this->l('Сумма по чеку'),
				'type' => 'text',
			),
			'vozvrat' => array(
				'title' => $this->l('Сумма возврата'),
				'type' => 'text',
			),
			'full_return' => array(
				'title' => $this->l('Полный возврат'),
				'type' => 'bool',
        'active' => 'status',
			),
			'pretenzia' => array(
				'title' => $this->l('Сумма претензии'),
				'type' => 'text',
			),
			'vopros_perenos' => array(
				'title' => $this->l('Возможно перенос'),
				'type' => 'bool',
        'active' => 'status',
			),
			'perenos' => array(
				'title' => $this->l('Перенос'),
				'type' => 'bool',
        'active' => 'status',
			),
			'st_oplat_chek' => array(
				'title' => $this->l('Чек погашен'),
				'type' => 'bool',
        'active' => 'status',
			),
			'st_oplat_vozvr' => array(
				'title' => $this->l('Возврат погшен'),
				'type' => 'bool',
        'active' => 'status',
			),
			'date_chek' => array(
				'title' => $this->l('Дата погашения чека'),
				'type' => 'date',
			),
			'date_vozvr' => array(
				'title' => $this->l('Дата получения возврата'),
				'type' => 'date',
			),
			'date_vozvr_chek' => array(
				'title' => $this->l('Дата погашения возврата'),
				'type' => 'date',
        
			),
			'closed' => array(
				'title' => $this->l('Точка закрыта'),
				'type' => 'bool',
        'active' => 'status',
			),
			'comment' => array(
				'title' => $this->l('Комментарий'),
				'type' => 'text',
			)
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_prihod';
		$helper->actions = array('edit');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Точки в маршруте');
		$helper->table = 'fozzy_kassa_prihod';
    $helper->token = Tools::GetValue('token');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$routes = $this->getRoutes($this->date,$this->id_route);
		if (is_array($routes) && count($routes))
			return $helper->generateList($routes, $fields_list);
		else
			return false;
    }
  
  private function getForm_Order()
    {
     $form_vals = $this->fields_value;
     $form = array(
            'input' => array(
                array(
                  'type' => 'text',
                  'label' => $this->l('Дата'),
                  'name' => 'date_mar',
                  'size' => 10,
                  'readonly' => true, 
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Маршрут №'),
                  'name' => 'mar_num',
                  'size' => 10,
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'hidden',
                  'name' => 'id_prihod',
                ),   
                array(
                  'type' => 'text',
                  'label' => $this->l('ID Точки'),
                  'name' => 'id_address',
                  'size' => 5, 
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Клиент'),
                  'name' => 'adr_name',
                  'size' => 5,  
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Адрес'),
                  'name' => 'adr',
                  'size' => 5,  
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Вес'),
                  'name' => 'weight',
                  'size' => 5,  
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Заказ №'),
                  'name' => 'id_order',
                  'size' => 5,  
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Телефон'),
                  'name' => 'phone',
                  'size' => 5,   
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'hidden',
                  'name' => 'id_vodila',
                  'size' => 5,   
                ), 
                array(
                  'type' => 'text',
                  'label' => $this->l('Водитель'),
                  'name' => 'vodila',
                  'size' => 5,   
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Тип оплаты'),
                  'name' => 'payment',
                  'size' => 5,   
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Сумма по чеку'),
                  'name' => 'chek_summ',
                  'size' => 5,   
                  'readonly' => true,
                  'required' => true
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Сумма возврата'),
                  'name' => 'vozvrat',
                  'size' => 5,  
                  'readonly' => $form_vals['closed'] > 0 ? true : false 
                ),
                array(
                  'type' => 'switch',
                  'label' => $this->l('Полный возврат'),
                  'name' => 'full_return',
                  'is_bool' => true,
                  'values' => array(
                  			array(
                  				'id' => 'full_return_on',
                  				'value' => 1,
                  				'label' => $this->l('Yes'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false),
                  			array(
                  				'id' => 'full_return_off',
                  				'value' => 0,
                  				'label' => $this->l('No'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false)),
                   'validation' => 'isBool',
                ),               
                array(
                  'type' => 'text',
                  'label' => $this->l('Сумма претензии'),
                  'name' => 'pretenzia',
                  'size' => 5,
                  'readonly' => $form_vals['closed'] > 0 ? true : false
                ),
                array(
                  'type' => 'switch',
                  'label' => $this->l('Возможно перенос'),
                  'name' => 'vopros_perenos',
                  'is_bool' => true,
                  'values' => array(
                  			array(
                  				'id' => 'vopros_perenos_on',
                  				'value' => 1,
                  				'label' => $this->l('Yes'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false),
                  			array(
                  				'id' => 'vopros_perenos_off',
                  				'value' => 0,
                  				'label' => $this->l('No'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false)),
                   'validation' => 'isBool',
                ), 
                array(
                  'type' => 'hidden',
                  'label' => $this->l('Перенос'),
                  'name' => 'perenos'
                ),
                array(
                  'type' => 'switch',
                  'label' => $this->l('Чек погашен'),
                  'name' => 'st_oplat_chek',
                  'is_bool' => true,
                  'values' => array(
                  			array(
                  				'id' => 'st_oplat_chek_on',
                  				'value' => 1,
                  				'label' => $this->l('Yes'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false),
                  			array(
                  				'id' => 'st_oplat_chek_off',
                  				'value' => 0,
                  				'label' => $this->l('No'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false)),
                   'validation' => 'isBool',
                ),
                array(
                  'type' => 'switch',
                  'label' => $this->l('Статус возврата'),
                  'name' => 'st_oplat_vozvr',
                  'is_bool' => true,
                  'values' => array(
                  			array(
                  				'id' => 'st_oplat_vozvr_on',
                  				'value' => 1,
                  				'label' => $this->l('Yes'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false),
                  			array(
                  				'id' => 'st_oplat_vozvr_off',
                  				'value' => 0,
                  				'label' => $this->l('No'),
                          'disabled' => $form_vals['closed'] > 0 ? true : false)),
                   'validation' => 'isBool',
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Комментарий'),
                  'name' => 'comment',
                  'size' => 5,
                  'readonly' => $form_vals['closed'] > 0 ? true : false
                ),  /*
                array(
                  'type' => $form_vals['closed'] > 0 ? 'text' : 'date',
                  'label' => $this->l('Дата погашения чека'),
                  'name' => 'date_chek',
                  'size' => 5,
                  'readonly' => $form_vals['closed'] > 0 ? true : false
                ),
                array(
                  'type' => $form_vals['closed'] > 0 ? 'text' : 'date',
                  'label' => $this->l('Дата получения возврата'),
                  'name' => 'date_vozvr',
                  'size' => 5,
                  'readonly' => $form_vals['closed'] > 0 ? true : false
                ),
                array(
                  'type' => $form_vals['closed'] > 0 ? 'text' : 'date',
                  'label' => $this->l('Дата погашения возврата'),
                  'name' => 'date_vozvr_chek',
                  'size' => 5,
                  'readonly' => $form_vals['closed'] > 0 ? true : false
                ),  */
                array(
                  'type' => 'hidden',
                  'name' => 'date_chek'
                ),
                array(
                  'type' => 'hidden',
                  'name' => 'date_vozvr'
                ),
                array(
                  'type' => 'hidden',
                  'name' => 'date_vozvr_chek'
                ),
                array(
                  'type' => 'hidden',
                  'name' => 'id_filial'
                ),
                array(
                  'type' => 'hidden',
                  'name' => 'closed'
                )
                
            ),
            'buttons' => array(
                array(
                'title' => $form_vals['closed'] > 0 ? $this->l('Назад') : $this->l('Сохранить'),
                'id' => $form_vals['closed'] > 0 ? 'submitSavePoint1' : 'submitSavePoint',
                'name' => $form_vals['closed'] > 0 ? 'submitSavePoint1' : 'submitSavePoint',
                'icon' => $form_vals['closed'] > 0 ? 'process-icon-cancel' : 'process-icon-save',
                'type' => 'submit'
                )
                
            )  
        );
        return $form;
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
        $this->fields_form = $this->getRouteSelect();
       break;
       case 2:
        $this->fields_value = $this->getRouteValues($this->date);
        $this->fields_form = $this->getRouteSelect();
       break;
       case 3:
        $point_values_sql = $this->getPoint($this->route_select);
        $point_values = $point_values_sql[0]; 
        $this->fields_value = $point_values;
        $this->fields_form = $this->getForm_Order();
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
  
  protected function getRouteValues($date)
    {
        $id_shop = (int)$this->context->shop->id;
       
        $sql_summ_to_close = "SELECT SUM(`chek_summ`) - SUM(`vozvrat`) as summa FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `mar_num` = '".$this->id_route."' AND `date_mar` = '".$date."' AND `full_return` = 0 AND `vopros_perenos` = 0 AND `perenos` = 0 AND (`payment` = 'Оплата наличными при получении' OR `payment` = 'Оплата при отриманні') AND `id_filial` =".$id_shop;
        $summ = Db::getInstance()->executeS($sql_summ_to_close);
        $sql_points_to_close = "SELECT COUNT(`id_prihod`) as points FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `mar_num` = '".$this->id_route."' AND `date_mar` = '".$date."'"." AND `id_filial` =".$id_shop;
        $points_to_close = Db::getInstance()->executeS($sql_points_to_close);
        $sql_summ_from_vodila = "SELECT `summ_vodila`, `raznitsa`, `vodila` FROM `"._DB_PREFIX_."fozzy_kassa_marshrut` WHERE `mar_date` = '".$date."' AND `mar_num` = ".$this->id_route." AND `id_filial` = ".$id_shop;
        $summ_v = Db::getInstance()->executeS($sql_summ_from_vodila);
        $summ_vv = (float)$summ_v[0]['summ_vodila'];
        $vodila = $summ_v[0]['vodila'];
        
        if ($this->context->employee->id == 1)
          {
       //    dump($vodila);
       //    die();
          }
        
        return array(
            'm_date' => $date,
            'm_code' => $this->id_route,
            'm_summ_to_close' => $summ[0]['summa'],
            'm_points' => $points_to_close[0]['points'],
            'm_summ_from_vodila' => $summ_vv,
            'm_vodila_mm' => $vodila,
            'm_summ_r' => (float)$summ_v[0]['raznitsa']
        );
    }  
  protected function getConfigFormValues($id_order, $id_route, $date)
    {
        return array(
            'id_order' => $id_order,
            'id_route' => $id_route,
            'main_date' => $date,
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
        $this->content .= $this->route_list;

        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }
  
  public function getRoutes($date,$route_num)
	{
		$routes = array();

    $id_shop = (int)$this->context->shop->id;
    /*
    switch ($id_shop)
    {
     case 1:
     $filial = array(4,5,'null',0);
     break;
     case 2:
     $filial = array(200);
     break;
     case 3:
     $filial = array(300);
     break;
     case 4:
     $filial = array(400);
     break;
    }
      */
		$sql_routes = "SELECT * FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `date_mar` = '".$date."' AND `mar_num` = ".$route_num." AND `id_filial` =".$id_shop;
    $routes = Db::getInstance()->executeS($sql_routes);

		return $routes;
	}
  
  public function getPoint($id_point)
	{
		$point = array();
   
		$sql_point = "SELECT * FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `id_prihod` = ".$id_point;
    $point = Db::getInstance()->executeS($sql_point);

		return $point;
	}
    
	public function postProcess()
	{
    
    if( Tools::GetValue('submitOpen') == 1 )
      {
        $this->date = Tools::GetValue('m_date');
        $this->form_num = 1;   
      }
    if (Tools::GetValue('submitOpenRoute') == 1)
      {
        $this->date = Tools::GetValue('m_date');
        $this->id_route = (int)Tools::GetValue('m_code');
        $this->route_list = $this->getPointsList();
        $this->form_num = 2;
      } 
    if (Tools::GetValue('id_prihod'))
      {
        $this->date = Tools::GetValue('m_date');
        $this->id_route = (int)Tools::GetValue('m_code');
        $this->route_list = $this->getPointsList();
        $this->route_select = (int)Tools::GetValue('id_prihod');
        $this->form_num = 3;
      }
    if (Tools::isSubmit('submitSavePoint'))
      {
        $this->date = Tools::GetValue('date_mar');
        $this->id_route = (int)Tools::GetValue('mar_num');
        $id_prihod = Tools::GetValue('id_prihod');
        $vozvrat = Tools::GetValue('vozvrat');    
        $full_return = Tools::GetValue('full_return');
        if ($full_return) $vozvrat = Tools::GetValue('chek_summ');
        $pretenzia = Tools::GetValue('pretenzia');
        $vopros_perenos = Tools::GetValue('vopros_perenos');
        $st_oplat_chek = Tools::GetValue('st_oplat_chek');
        $st_oplat_vozvr = Tools::GetValue('st_oplat_vozvr');
        $comment = Tools::GetValue('comment');
        $date_chek = Tools::GetValue('date_chek');
        $date_vozvr = Tools::GetValue('date_vozvr');
        $date_vozvr_chek = Tools::GetValue('date_vozvr_chek');
        if ( ($vozvrat > 0 && !$date_vozvr) || ($vozvrat > 0 && $date_vozvr=='0000-00-00') ) $date_vozvr = date('Y-m-d');
        $sql_point_upd = "UPDATE `"._DB_PREFIX_."fozzy_kassa_prihod` SET `vozvrat`=".$vozvrat.",`full_return`=".$full_return.",`pretenzia`=".$pretenzia.",`vopros_perenos`=".$vopros_perenos.",`st_oplat_chek`=".$st_oplat_chek.",`st_oplat_vozvr`=".$st_oplat_vozvr.",`comment`='".$comment."',`date_chek`='".$date_chek."',`date_vozvr`='".$date_vozvr."',`date_vozvr_chek`='".$date_vozvr_chek."' WHERE `id_prihod` = ".$id_prihod;
        Db::getInstance()->execute($sql_point_upd);
        $this->route_list = $this->getPointsList();
        $this->route_select = null;
        $this->form_num = 2;
      }
     if (Tools::isSubmit('submitSavePoint1'))
      {
        $this->date = Tools::GetValue('date_mar');
        $this->id_route = (int)Tools::GetValue('mar_num');
        $this->route_list = $this->getPointsList();
        $this->route_select = null;
        $this->form_num = 2;
      }
     if (Tools::isSubmit('submitSaveRoute'))
      {
       
       $id_shop = (int)$this->context->shop->id;
        
       $this->date = Tools::GetValue('m_date');
       $this->id_route = (int)Tools::GetValue('m_code');
       $m_summ_to_close = (float)Tools::GetValue('m_summ_to_close');
       $m_summ_from_vodila = (float)Tools::GetValue('m_summ_from_vodila');
       $kassir = $this->context->employee->lastname." ".$this->context->employee->firstname;
       $datapr = date('Y-m-d');
       
       $raznitsa = $m_summ_to_close - $m_summ_from_vodila; 
       $sql_get_vodila = "SELECT `vodila`,`zone`,`zone_name` FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `date_mar` = '".$this->date."' AND  `mar_num` = ".$this->id_route." AND `id_filial` =".$id_shop." LIMIT 1";
       $get_vodila = Db::getInstance()->executeS($sql_get_vodila);
       $vodila = $get_vodila[0]['vodila'];
       $zone = (int)$get_vodila[0]['zone'];
       $zone_name = $get_vodila[0]['zone_name'];
       $sql_close_marshrut = "INSERT INTO `"._DB_PREFIX_."fozzy_kassa_marshrut`(`mar_date`, `pr_date`, `mar_num`, `vodila`, `kassir`, `summ_marshrut`, `summ_vodila`, `raznitsa`,`zone`,`zone_name`,`id_filial`) VALUES ('".$this->date."','".$datapr."',".$this->id_route.",'".$vodila."','".$kassir."',".$m_summ_to_close.",".$m_summ_from_vodila.",".$raznitsa.",".$zone.",'".$zone_name."',".$id_shop.")";
       Db::getInstance()->execute($sql_close_marshrut);
       $sql_close_marshruts_points = "UPDATE `ps_fozzy_kassa_prihod` SET `st_oplat_chek` = 1, `date_chek` = '".date('Y-m-d')."', `closed` = 1 WHERE `date_mar` = '".$this->date."' AND `mar_num` = ".$this->id_route." AND `id_filial` =".$id_shop;
       Db::getInstance()->execute($sql_close_marshruts_points);
       
       $sql_select_to_change_status = "SELECT * FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `date_mar` = '".$this->date."' AND `mar_num` = ".$this->id_route." AND `id_filial` =".$id_shop;
       $to_change_status = Db::getInstance()->executeS($sql_select_to_change_status);
       
 //     if ($this->context->employee->id == 1)
   //   {
       foreach ($to_change_status as $zakaz)
         {
         $id_order = (int)$zakaz['id_order'];
         $id_prihod = (int)$zakaz['id_prihod'];
         if ($zakaz['vozvrat'] > 0) //Изменяем статус по возвратам
            {
             //dump($zakaz['id_order'].' - Возврат');
             $sql_upd_pr = "UPDATE `"._DB_PREFIX_."orders` SET `id_prihod` = ".$id_prihod.", `summ_to_vz` = ".$zakaz['vozvrat']." WHERE `id_order` = ".$id_order;
             Db::getInstance()->execute($sql_upd_pr);
             $history = new OrderHistory();
             $history->id_order = $id_order;
             $history->id_employee = $this->context->employee->id;
             $history->changeIdOrderState(933, $id_order);  //Статус - оформить возврат
             $history->add();
            }
         if ($zakaz['vopros_perenos'] == 1) //Изменяем статус по запросу переноса
            {
             //dump($zakaz['id_order'].' - Перенос');
             $sql_upd_vod = "UPDATE `"._DB_PREFIX_."orders` SET `id_vodila` = 0, `id_prihod` = ".$id_prihod." WHERE `id_order` = ".$id_order;
             Db::getInstance()->execute($sql_upd_vod);
             $history = new OrderHistory();
             $history->id_order = $id_order;
             $history->id_employee = $this->context->employee->id;
             $history->changeIdOrderState(932, $id_order); //Статус - Перенос заказа
             $history->add();
            }
         if ($zakaz['vopros_perenos'] == 0 && $zakaz['vozvrat'] == 0) //Изменяем статус по закрытию заказа
            {
             //dump($zakaz['id_order'].' - Норма');
             $sql_upd_pr = "UPDATE `"._DB_PREFIX_."orders` SET `id_prihod` = ".$id_prihod." WHERE `id_order` = ".$id_order;
             Db::getInstance()->execute($sql_upd_pr);
             $history = new OrderHistory();
             $history->id_order = $id_order;
             $history->id_employee = $this->context->employee->id;
             $history->changeIdOrderState(916, $id_order); //Статус - Закрыт
             $history->add();
            }
         }
   //   }
       $this->route_select = null;
       $this->form_num = 2;
      }

    parent::postProcess();
	}
	

}