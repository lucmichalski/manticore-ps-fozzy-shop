<?php

class AdminKassaSVController extends AdminController
{

    public $date;
    public $filial;
    public $closed;
    
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
      $this->addJS('/modules/fozzy_kassa/views/js/ks_05.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title = $this->l('Кассовый отчет');
    }

    public function initToolBar()
    {
        return true;
    }

    
  private function getStartForm()
    {
     $filials = array(                              
                0 => array('id_filial'=>'KZ','name'=>'Киев - Заболотного'),
                1 => array('id_filial'=>'KP','name'=>'Киев - Петровка'),
                2 => array('id_filial'=>'KPr','name'=>'Киев - Пролиски'),
                3 => array('id_filial'=>'O','name'=>'Одесса'),
                4 => array('id_filial'=>'D','name'=>'Днепр'),
                5 => array('id_filial'=>'KH','name'=>'Харьков'),
                6 => array('id_filial'=>'RV','name'=>'Ровно'),
                7 => array('id_filial'=>'KR','name'=>'Кременчуг'),
                );
     
     $form1 = array(
            'input' => array(
                array(
                  'type' => 'date',
                  'label' => $this->l('Дата'),
                  'name' => 'k_date',
                  'size' => 10,
                  'required' => true
                ),
                array(
      						'type' => 'select',
      						'label' => $this->l('Филиал'),
      						'name' => 'k_filial[]',
                  'multiple' => false,
      						'class' => 'fixed-width-xxl',
      						'options' => array(
      							'query' => $filials,
      							'id' => 'id_filial',
      							'name' => 'name'
      						),
      					)
              ),
            'submit' => array(
                'title' => $this->l('Сформировать'),
                'id' => 'submitForm',
                'name' => 'submitForm',
                'icon' => 'process-icon-download'
              ),
      );
      $form2 = array(
            'input' => array(
                array(
                  'type' => 'text',
                  'label' => $this->l('Дата'),
                  'name' => 'k_date',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                array(
                  'type' => $this->closed == 1 ? 'text' : 'hidden',
                  'label' => $this->l('Филиал'),
                  'name' => 'k_filial',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                array(
                  'type' => 'hidden',
                  'label' => $this->l('Филиал #'),
                  'name' => 'k_filial_num',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                array(
                  'type' => 'text',
                  'label' => $this->l('Кассир'),
                  'name' => 'k_kassir',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Изначальная сумма'),
                  'name' => 'k_summ',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Сумма в сейфе'),
                  'name' => 'k_summ_seif',
                  'size' => 10,
                  'required' => true
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Сумма в экспедиции'),
                  'name' => 'k_summ_exp',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Сумма возвратов'),
                  'name' => 'k_summ_vozv',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Сумма не закрытых чеков'),
                  'name' => 'k_summ_notzakr',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Мелочь'),
                  'name' => 'k_summ_moneta',
                  'size' => 10,
                  'required' => true
                ), 
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Итого в кассе'),
                  'name' => 'k_summ_kassa',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'text',
                  'label' => $this->l('Разница'),
                  'name' => 'k_summ_kassa_r',
                  'size' => 10,
                  'required' => true,
                  'readonly' => true,
                ),
                $this->closed == 1 ? '' : array(
                  'type' => 'hidden',
                  'label' => $this->l('Разница с предыдущим днем'),
                  'name' => 'k_summ_kassa_rp',
                  'size' => 10,
                  'required' => true
                )
              ),
            'submit' => $this->closed == 1 ? '' : array(
                'title' => $this->l('Закрыть'),
                'id' => 'submitClose',
                'name' => 'submitClose',
                'icon' => 'process-icon-download',
              ),
      );
      
      if ($this->filial) return $form2;
      else return $form1;
    }
  
  private function getRoutes()
    {
     $id_shop = (int)$this->context->shop->id;
     $sql_routes = "SELECT `date_mar`, `mar_num`, `vodila`, SUM(chek_summ*nal) as summa, `zone` FROM `"._DB_PREFIX_."fozzy_kassa_prihod` WHERE `id_filial` = ".$id_shop." AND `closed` = 0 GROUP BY `date_mar`, `mar_num` ORDER BY `date_mar`, `mar_num`";
  //   dump($sql_routes);
     $routes = Db::getInstance()->executeS($sql_routes);
     return $routes;
    }
  
  private function getKassa()
    {
     $id_shop = (int)$this->context->shop->id;
     $sql_kassas = "SELECT * FROM `"._DB_PREFIX_."fozzy_kassa_svod` WHERE 1 ORDER BY `k_date` DESC";
  //   dump($sql_routes);
     $kassa = Db::getInstance()->executeS($sql_kassas);
     return $kassa;
    }
    
  private function getPointsList()
    {
     $fields_list = array(
			'k_date' => array(
				'title' => $this->l('Дата принятия'),
				'type' => 'date',
			),
      'filial_name' => array(
				'title' => $this->l('Филиал'),
				'type' => 'text',
			),
			'user' => array(
				'title' => $this->l('Кассир'),
				'type' => 'text',
			),
			'k_summ' => array(
				'title' => $this->l('Сумма начальная'),
				'type' => 'text',
			),
			'k_summ_seif' => array(
				'title' => $this->l('В сейфе'),
				'type' => 'text',
			),
			'k_summ_exp' => array(
				'title' => $this->l('В экспедиции'),
				'type' => 'text',
			),
			'k_summ_vozv' => array(
				'title' => $this->l('Возвраты'),
				'type' => 'text',
			),
			'k_summ_notzakr' => array(
				'title' => $this->l('Не закрыто'),
				'type' => 'text',
			),
			'k_summ_moneta' => array(
				'title' => $this->l('Мелочь'),
				'type' => 'text',
			),
			'k_summ_kassa' => array(
				'title' => $this->l('Касса'),
				'type' => 'text',
			),
			'k_summ_kassa_r' => array(
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
    
		$helper->title = $this->l('Закрытые кассовые дни');
		$helper->table = 'fozzy_kassa_marshrut';
    $helper->token = Tools::GetValue('token');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$kassa = $this->getKassa();
		if (is_array($kassa) && count($kassa))
			return $helper->generateList($kassa, $fields_list);    
		else
			return false;
    }
  
  public function renderForm()
    {
      if (!$this->date) $this->date = date('Y-m-d');
      if ($this->date)  $this->fields_value = $this->getMainValues($this->date);
      $this->fields_form = $this->getStartForm();
      
      return parent::renderForm();
    }
  
  private function getOrdersSum()
    {
      $id_shop = (int)$this->context->shop->id;
      $sql_sel_pr = "SELECT SUM(CASE
    WHEN `summ_to_vz` = 0 
        THEN `fiskal`
    WHEN `summ_to_vz` > 0 
        THEN `summ_to_vz`
END ) as summa, `zone` FROM `"._DB_PREFIX_."orders` WHERE `current_state` = 933 AND `id_shop` =".$id_shop." AND (`payment` = 'Оплата наличными при получении' OR `payment` = 'Оплата при отриманні') GROUP BY `zone` ORDER BY `zone`";
      $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);
      return $sql_sel_pr_ar;
    }
  
  private function getOrdersSum_VZ()
    {
      $id_shop = (int)$this->context->shop->id;
      $sql_sel_pr = "SELECT SUM(a.`fiskal`) as summa, `zone`, a.`zone_name` FROM `"._DB_PREFIX_."orders` a WHERE a.`fiskal` > 0 AND a.`id_vodila` = 0 AND a.`date_add` > '2020-01-01' AND a.`summ_to_vz` = 0 AND (a.`payment` = 'Оплата наличными при получении' OR a.`payment` = 'Оплата при отриманні') AND a.`current_state` NOT IN (933) AND a.`id_shop` =".$id_shop." AND 933 NOT IN (SELECT o.`id_order_state` FROM `"._DB_PREFIX_."order_history` o WHERE o.`id_order` = a.`id_order`) "." GROUP BY a.`zone` ORDER BY a.`zone`";
      $sql_sel_pr_ar = Db::getInstance()->executeS($sql_sel_pr);

      return $sql_sel_pr_ar;
    }
    
  protected function getMainValues($date)
    {
     $summs_1 = $this->getRoutes();
     $summs = array();
     
     foreach ($summs_1 as $key1=>$summa1)
     {
      $z = (int)$summa1['zone'];
      $summs[$z] = $summs[$z] + $summa1['summa'];
     }
     
     $summs_vz_1 = $this->getOrdersSum();
     $summs_vz = array();
     foreach ($summs_vz_1 as $key2=>$summa2)
     {
      $z = (int)$summa2['zone'];
      $summs_vz[$z] = $summs_vz[$z] + $summa2['summa'];
     }
     
     $summs_vz_2 = $this->getOrdersSum_VZ();
     $summs_voz = array();
     foreach ($summs_vz_2 as $key2=>$summa3)
     {
      $z = (int)$summa3['zone'];
      $summs_voz[$z] = $summs_voz[$z] + $summa3['summa'];
     }
        switch ($this->filial)
          {
           case 'KZ':
            $k_summ = 220000;
            $filial = 5;
            $filial_name = 'Заболотного';
           break;
           case 'KP':
            $k_summ = 200000;
            $filial = 4;
            $filial_name = 'Петрівка';
           break;
           case 'KPr':
            $k_summ = 150000;
            $filial = 6;
            $filial_name = 'Проліски';
           break;
           case 'O':
            $k_summ = 130000;
            $filial = 200;
            $filial_name = 'Одеса';
           break;
           case 'D':
            $k_summ = 100000;
            $filial = 300;
            $filial_name = 'Дніпро';
           break;
           case 'KH':
            $k_summ = 100000;
            $filial = 400;
            $filial_name = 'Харків';
           break;
           case 'RV':
            $k_summ = 100000;
            $filial = 500;
            $filial_name = 'Рівне';
           break;
           case 'KR':
            $k_summ = 100000;
            $filial = 600;
            $filial_name = 'Кременчуг';
           break;
          }
        $id_shop = (int)$this->context->shop->id;
        $kassir = $this->context->employee->lastname." ".$this->context->employee->firstname;
        $k_summ_exp = $summs[$filial];
        $k_summ_vozv = $summs_vz[$filial];
        $k_summ_notzakr = $summs_voz[$filial];
        $ddate = date('Y-m-d', strtotime("-1 day", strtotime($date)));
        
        $sql_r = "SELECT `k_summ_kassa_r` FROM `"._DB_PREFIX_."fozzy_kassa_svod` WHERE `id_filial` = $filial AND `id_shop` = $id_shop AND `k_date` = '".$ddate."'";
        $sql_rs = (float)Db::getInstance()->getValue($sql_r);
        
        return array(
            'k_date' => $date,
            'k_summ' => $k_summ,
            'k_filial' => $filial_name,
            'k_filial_num' => $filial,
            'k_kassir' => $kassir,
            'k_summ_exp' => (float)$k_summ_exp,
            'k_summ_vozv' => (float)$k_summ_vozv,
            'k_summ_notzakr' => (float)$k_summ_notzakr,
            'k_summ_kassa_rp' => (float)$sql_rs,
            'k_summ_moneta' => 0,
            'k_summ_seif' => 0, 
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
     //   $this->content .= $this->renderKpis();
        $this->content .= $this->getPointsList();  

        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }
    
	public function postProcess()
	{
    if( Tools::GetValue('submitForm') == 1 )
      {
        $this->filial = implode(",",Tools::getValue('k_filial'));
        $this->date = Tools::GetValue('k_date');
        $id_shop = (int)$this->context->shop->id;
        switch ($this->filial)
          {
           case 'KZ':
            $filial = 5;
           break;
           case 'KP':
            $filial = 4;
           break;
           case 'KPr':
            $filial = 6;
           break;
           case 'O':
            $filial = 200;
           break;
           case 'D':
            $filial = 300;
           break;
           case 'KH':
            $filial = 400;
           break;
           case 'RV':
            $filial = 500;
           break;
           case 'KR':
            $filial = 600;
           break;
          }
        $sql_s = "SELECT `closed` FROM `"._DB_PREFIX_."fozzy_kassa_svod` WHERE `id_filial` = $filial AND `id_shop` = $id_shop AND `k_date` = '".$this->date."'";
        $sql_ars = (int)Db::getInstance()->getValue($sql_s);
        if ($sql_ars) $this->closed = 1;
      }
    
    if( Tools::GetValue('submitClose') == 1 )
      {
        $id_filial = Tools::GetValue('k_filial_num');
        $filial_name = Tools::GetValue('k_filial');
        $id_shop = (int)$this->context->shop->id;
        $user = Tools::GetValue('k_kassir');
        $k_date = Tools::GetValue('k_date');
        $k_summ = (float)Tools::GetValue('k_summ');
        $k_summ_seif = (float)Tools::GetValue('k_summ_seif');
        $k_summ_exp = (float)Tools::GetValue('k_summ_exp');
        $k_summ_vozv = (float)Tools::GetValue('k_summ_vozv');
        $k_summ_notzakr = (float)Tools::GetValue('k_summ_notzakr');
        $k_summ_moneta = (float)Tools::GetValue('k_summ_moneta');
        $k_summ_kassa = (float)Tools::GetValue('k_summ_kassa');
        $k_summ_kassa_r = (float)Tools::GetValue('k_summ_kassa_r');
        $k_summ_kassa_rp = (float)Tools::GetValue('k_summ_kassa_rp');
        $sql = "INSERT INTO `"._DB_PREFIX_."fozzy_kassa_svod`(`id_filial`, `filial_name`, `id_shop`, `user`, `closed`, `k_date`, `k_summ`, `k_summ_seif`, `k_summ_exp`, `k_summ_vozv`, `k_summ_notzakr`, `k_summ_moneta`, `k_summ_kassa`, `k_summ_kassa_r`, `k_summ_kassa_rp`) VALUES ($id_filial,'$filial_name',$id_shop,'$user',1,'$k_date',$k_summ,$k_summ_seif,$k_summ_exp,$k_summ_vozv,$k_summ_notzakr,$k_summ_moneta,$k_summ_kassa,$k_summ_kassa_r,$k_summ_kassa_rp)";
        $sql_ar = Db::getInstance()->execute($sql);
        $this->date = null;
      }

    parent::postProcess();
	}
	

}