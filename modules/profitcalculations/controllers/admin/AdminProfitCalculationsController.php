<?php
if(defined('__PS_VERSION_'))
exit('Restricted Access!!!');

include_once(_PS_MODULE_DIR_ .'profitcalculations/classes/ProfitCalculationsClass.php');

class AdminProfitCalculationsController extends ModuleAdminController
{
	public function __construct()
    {
        $this->table = 'profitcalculations';
        $this->className = 'ProfitCalculations';
		$this->list_no_link = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;		
        $this->bootstrap = true;
		
        $this->_defaultOrderBy = 'id_profitcalculations';
        $this->_defaultOrderWay = 'DESC';
		

        $this->bulk_actions = array(
            'delete' => array('text' => 'Delete', 'icon' => 'icon-trash', 'confirm' => 'Delete selected items?',
            )
        );	

        $this->fields_list = array(
						'id_profitcalculations' => array(
							'title' => 'ID',
							'filter' => true
						),
						'id_order' => array(
							'title' => 'Order No.',
							'filter' => true
						),
						'type_action' => array(
							'title' => 'Type',
							'filter' => true
						),
						'debit' => array(
							'title' => 'Debit',
							'filter' => true
						),
						'credit' => array(
							'title' => 'Credit',
							'filter' => true
						),
						'profit' => array(
							'title' => 'Profit',
						'filter' => true
								 ),
								'comment' => array(
									'title' => 'Comment',
						'filter' => true
								 ),
					 'active' => array(
							'title' => 'Enabled',
							'align' => 'center',
							'active' => 'status',
							'type' => 'bool',
							'orderby' => false,
							'class' => 'fixed-width-xs'
						),
						'date_transaction' => array(
								'title' => 'Date',
								'align' => 'text-right',
								'type' => 'date',				
								'filter_key' => 'a!date_transaction'
						),            
						
						'date_transaction' => array(
								'title' => 'Date',
								'align' => 'text-right',
								'type' => 'date',			
								'filter_key' => 'a!date_transaction'
						),
					);

        parent::__construct();
    }	

	public function renderKpis()
    {
        $time = time();
        $kpis = array();
		$filter = $this->_filter ? ' WHERE '.substr($this->_filter, 4) : "";
		$_filter = $this->_filter ? $this->_filter : "";
		$data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT 
				SUM(a.debit) as debit,
				SUM(a.credit) as credit,
				SUM(a.profit) as profit,
				(SELECT SUM(debit) FROM `'._DB_PREFIX_.'profitcalculations` WHERE type_action != "'.Configuration::get('PROFIT_CACL_SHIPPING').'" AND type_action !="'.Configuration::get('PROFIT_CACL_PRODUCT').'"'.$_filter.') as another_debit,
				(SELECT SUM(credit) FROM `'._DB_PREFIX_.'profitcalculations` WHERE type_action != "'.Configuration::get('PROFIT_CACL_SHIPPING').'" AND type_action !="'.Configuration::get('PROFIT_CACL_PRODUCT').'"'.$_filter.') as another_credit,
				(SELECT SUM(debit) FROM `'._DB_PREFIX_.'profitcalculations` WHERE type_action = "'.Configuration::get('PROFIT_CACL_PRODUCT').'"'.$_filter.' AND active = 1) as prod_debit,
				(SELECT SUM(credit) FROM `'._DB_PREFIX_.'profitcalculations` WHERE type_action = "'.Configuration::get('PROFIT_CACL_PRODUCT').'"'.$_filter.' AND active = 1) as prod_credit,
				(SELECT SUM(debit) FROM `'._DB_PREFIX_.'profitcalculations` WHERE type_action = "'.Configuration::get('PROFIT_CACL_SHIPPING').'"'.$_filter.' AND active = 1) as ship_debit,
				(SELECT SUM(credit) FROM `'._DB_PREFIX_.'profitcalculations` WHERE type_action = "'.Configuration::get('PROFIT_CACL_SHIPPING').'"'.$_filter.' AND active = 1) as ship_credit
			FROM `'._DB_PREFIX_.'profitcalculations` a'.$filter.' WHERE a.active = 1');

// die(var_dump($data));
		$kpis = array();
		
		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-qrcode';
		$helper->color = 'color1';
		$helper->title = $this->l('Another');
		$helper->subtitle = $this->l('debit');
		$helper->value = Tools::displayPrice($data[0]['another_debit']);
		$kpis[] = $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-qrcode';
		$helper->color = 'color1';
		$helper->title = $this->l('Another');
		$helper->subtitle = $this->l('credit');
		$helper->value = Tools::displayPrice($data[0]['another_credit']);
		$kpis[count($kpis)-1] .= $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-truck';
		$helper->color = 'color4';
		$helper->title = $this->l('Shipping');
		$helper->subtitle = $this->l('debit');
		$helper->value = Tools::displayPrice($data[0]['ship_debit']);
		$kpis[] = $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-truck';
		$helper->color = 'color4';
		$helper->title = $this->l('Shipping');
		$helper->subtitle = $this->l('credit');
		$helper->value = Tools::displayPrice($data[0]['ship_credit']);
		$kpis[count($kpis)-1] .= $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-barcode';
		$helper->color = 'color3';
		$helper->title = $this->l('Products');
		$helper->subtitle = $this->l('debit');
		$helper->value = Tools::displayPrice($data[0]['prod_debit']);
		$kpis[] = $helper->generate();


		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-barcode';
		$helper->color = 'color3';
		$helper->title = $this->l('Products');
		$helper->subtitle = $this->l('credit');
		$helper->value = Tools::displayPrice($data[0]['prod_credit']);
		$kpis[count($kpis)-1] .= $helper->generate();

		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-money';
		$helper->color = 'color2';
		$helper->title = $this->l('Total');
		$helper->subtitle = $this->l('debit');
		$helper->value = Tools::displayPrice($data[0]['debit']);
		$kpis[] = $helper->generate();


		$helper = new HelperKpi();
		$helper->id = 'box-total-products';
		$helper->icon = 'icon-trophy';
		$helper->color = 'color2';
		$helper->title = $this->l('Total');
		$helper->subtitle = $this->l('profit');
		$helper->value = Tools::displayPrice($data[0]['profit']);
		$kpis[count($kpis)-1] .= $helper->generate();

		$rows = '';
		$helper = new HelperKpiRow();
		$helper->kpis = $kpis;
		$rows .= $helper->generate();
		
		return $rows;
    }
	
	protected function processBulkDelete() {
		if (is_array($this->boxes) && !empty($this->boxes)) {
			$arr_to_del = Tools::getValue($this->table.'Box');
			foreach ($arr_to_del as $id_profitcalculations) {
				$profitcalculations = new ProfitCalculationsClass($id_profitcalculations);
				 if (Validate::isLoadedObject($profitcalculations))
					 $profitcalculations->delete();
			}
			$link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');
			Tools::redirectAdmin($link_redirect . '&confirm='.$this->l('Transaction delete'));
		} else {
			$link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');
			Tools::redirectAdmin($link_redirect . '&error='.$this->l('Transaction not delete'));
		}
	}
	
	protected function processBulkEnableSelection() {
		if (is_array($this->boxes) && !empty($this->boxes)) {
			$arr_to = Tools::getValue($this->table.'Box');
			foreach ($arr_to as $id_profitcalculations) {
				$profitcalculations = new ProfitCalculationsClass($id_profitcalculations);
				 if (Validate::isLoadedObject($profitcalculations))
					$profitcalculations->active = 1;	
					$profitcalculations->save();
			}
			$link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');
			Tools::redirectAdmin($link_redirect . '&confirm='.$this->l('Transactions enabled'));
		} else {
			$link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');
			Tools::redirectAdmin($link_redirect . '&error='.$this->l('Transaction status not change'));
		}
	}
	
	protected function processBulkDisableSelection() {
		if (is_array($this->boxes) && !empty($this->boxes)) {
			$arr_to = Tools::getValue($this->table.'Box');
			foreach ($arr_to as $id_profitcalculations) {
				$profitcalculations = new ProfitCalculationsClass($id_profitcalculations);
				 if (Validate::isLoadedObject($profitcalculations))
					$profitcalculations->active = 0;	
					$profitcalculations->save();
			}
			$link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');
			Tools::redirectAdmin($link_redirect . '&confirm='.$this->l('Transactions disabled'));
		} else {
			$link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');
			Tools::redirectAdmin($link_redirect . '&error='.$this->l('Transaction status not change'));
		}
	}
	
	public function processStatus()
    {
        if (!$id_profitcalculations = (int)Tools::getValue('id_profitcalculations')) {
            die(Tools::jsonEncode(array('success' => false, 'error' => true, 'text' => $this->l('Failed to update the status'))));
        } else {
			$id_profitcalculations = Tools::getValue('id_profitcalculations');
            $profitcalculations = new ProfitCalculationsClass($id_profitcalculations);		
            if (Validate::isLoadedObject($profitcalculations)) {
				
				$link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');
				
                $profitcalculations->active = $profitcalculations->active == 1 ? 0 : 1;	
                $profitcalculations->save() ? Tools::redirectAdmin($link_redirect . '&confirm='.$this->l('Transaction status changed')) : Tools::redirectAdmin($link_redirect . '&error='.$this->l('Some thing is wrong. Transaction status not changed'));
            }
        }
	}
	
    public function initPageHeaderToolbar()
    {

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['add_new'] = array(
                'href' => self::$currentIndex.'&addprofitcalculations&token='.$this->token,
                'desc' => $this->l('Add transaction', null, null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['refresh_all'] = array(
                'href' => self::$currentIndex.'&action=refresh&token='.$this->token,
                'desc' => $this->l('Refresh transaction', null, null, false),
                'icon' => 'process-icon-refresh'
            );
        }
        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
		 // loads current warehouse
        if (!($obj = $this->loadObject(true))) {
            return;
        }
		
		$this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Transaction'),
            ),
            'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_profitcalculations',
				),
				array(
					'label' => $this->l('Type transaction'),
					'name' => 'type_action',
					'type' => 'text',
					'col' => 3,
					'required' => true
				),
				array(
					'label' => $this->l('Debit'),
					'name' => 'debit',
					'type' => 'text',
					'col' => 3,
					'required' => true
				),
				array(
					'label' => $this->l('Credit'),
					'name' => 'credit',
					'col' => 3,
					'type' => 'text',
					'required' => true
				),
				array(
					'label' => $this->l('Comment'),
					'name' => 'comment',
					'type' => 'textarea',
					'col' => 3,
				),
				array(
					'type' => 'date',
					'label' => $this->l('Date'),
					'name' => 'date_transaction',			
					'hint' => $this->l('Date of the transaction'),
					'required' => true
				),					
				array(
					'type' => 'switch',
					'label' => $this->l('Enable'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)					
				)
			),
            'submit' => array(
                'title' => $this->l('Save')
            )
		);
		
		$profitcalculations = (int)Tools::getValue('id_profitcalculations', null);
		 if ($profitcalculations != null) {
			$profitcalculations = new ProfitCalculationsClass($profitcalculations);
            $this->fields_value = array(
                'type_action' => $profitcalculations->type_action,
                'debit' => $profitcalculations->debit,
                'credit' => $profitcalculations->credit,
                'profit' => $profitcalculations->profit,
                'comment' => $profitcalculations->comment,
                'active' => $profitcalculations->active,
                'date_transaction' => $profitcalculations->date_transaction,
            );
		 }
		
		return parent::renderForm();		
	}
	
    public function initContent()
    {
        if ($error = Tools::getValue('error')) {
           $this->errors[] = Tools::displayError($error);
        }
        if ($confirm = Tools::getValue('confirm')) {
			$this->confirmations[] = Tools::displayError($confirm);
		}
		
		if (Tools::getValue('action') && Tools::getValue('action') == 'refresh') {
			$result = $this->refreshTransaction();
        } else {
            $this->_use_found_rows = false;
        }	
        parent::initContent();
    }
	
	public function refreshTransaction() {
		$profitStatus = Configuration::get('PROFIT_CACL_STATUS');
		if(!$profitStatus) {
			return Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&error='.$this->l('Please, select order status for delivered orders in module setting.'));
		} else {
			$orders_transaction = $this->getOrdersTransactions(); //из заказов
			for ($i = 0; count($orders_transaction) > $i; $i++) {
					$checkStatus = $this->checkStatusByOrder($orders_transaction[$i]['id_order']);
					If($checkStatus) {
						if ($orders_transaction[$i]['type_action'] == Configuration::get('PROFIT_CACL_PRODUCT')) {
								$updOrder = $this->checkMathProduct($orders_transaction[$i]['id_order'], $orders_transaction[$i]['profit']);
								if($updOrder) {
									foreach ($checkStatus as $row) {
										if ($row['type_action'] == Configuration::get('PROFIT_CACL_PRODUCT') && $row['active'] == 1)
											$this->updeteProfit($row['id_profitcalculations'], $orders_transaction[$i]['debit'], $orders_transaction[$i]['credit'], $orders_transaction[$i]['profit']);
									}
								}
						} else {
							$updOrder = $this->checkMathShipping($orders_transaction[$i]['id_order'], $orders_transaction[$i]['profit']);
							if($updOrder) {
									foreach ($checkStatus as $row) {
										if ($row['type_action'] == Configuration::get('PROFIT_CACL_SHIPPING') && $row['active'] == 1)
											$this->updeteProfit($row['id_profitcalculations'], $orders_transaction[$i]['debit'], $orders_transaction[$i]['credit'], $orders_transaction[$i]['profit']);
									}
								}
						}
					} else {
						foreach ($orders_transaction as $row) {
							$profitcalculations = new ProfitCalculationsClass();
							$profitcalculations->id_order = $row['id_order'];
							$profitcalculations->type_action = $row['type_action'];
							$profitcalculations->debit = $row['debit'];
							$profitcalculations->credit = $row['credit'];
							$profitcalculations->profit = $row['profit'];
							$profitcalculations->active = 1;
							$profitcalculations->date_transaction = $row['date_transaction'];
							$profitcalculations->save();
						}
					}
			}
			Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
		}
	}
	public function updeteProfit($id_profitcalculations, $debit, $credit, $profit) {
		$profitcalculations = new ProfitCalculationsClass($id_profitcalculations);
		$profitcalculations->debit = $debit;
		$profitcalculations->credit = $credit;
		$profitcalculations->profit = $profit;
		$profitcalculations->update();
	}
	
	public function checkMathShipping($id, $profit) {
		$result = Db::getInstance()->executeS('
			SELECT 	o.id_order,
					oc.shipping_cost_tax_incl as debit,
					oc.real_shipping as credit,
					(oc.shipping_cost_tax_incl - oc.real_shipping) as profit,
					o.date_add as date_transaction,
					"'.Configuration::get('PROFIT_CACL_SHIPPING').'" as "type_action"
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_carrier` oc ON (o.id_order = oc.id_order)
				WHERE o.id_order = "'.$id.'"');
		return ($result[0]['profit'] == $profit ? $result : false);
	}
	
	public function checkMathProduct($id, $profit) {
		$result = Db::getInstance()->executeS('
			SELECT 	o.id_order,
					SUM(od.unit_price_tax_incl * od.product_quantity) as debit,
					SUM(od.original_wholesale_price * od.product_quantity) as credit,
					(SUM(od.unit_price_tax_incl * od.product_quantity) - SUM(od.original_wholesale_price * od.product_quantity)) as profit,
					o.date_add as date_transaction,
					"'.Configuration::get('PROFIT_CACL_PRODUCT').'" as "type_action"
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (o.id_order = od.id_order)
				WHERE o.id_order = "'.$id.'"');
		return ($result[0]['profit'] == $profit ? $result : false);
	}
	
	public function checkStatusByOrder($id) {
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'profitcalculations` WHERE id_order = "'.$id.'"');
	}
	
	
	public function getOrdersForRefresh() {
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'profitcalculations` WHERE type_action IN ("'.Configuration::get('PROFIT_CACL_PRODUCT').'", "'.Configuration::get('PROFIT_CACL_SHIPPING').'") AND active = 1');
	}
	
	public function getOrdersTransactions() {
		$id_orders = Db::getInstance()->executeS('
			SELECT 	o.id_order,
					SUM(od.unit_price_tax_incl * od.product_quantity) as debit,
					SUM(od.original_wholesale_price * od.product_quantity) as credit,
					(SUM(od.unit_price_tax_incl * od.product_quantity) - SUM(od.original_wholesale_price * od.product_quantity)) as profit,
					o.date_add as date_transaction,
					"'.Configuration::get('PROFIT_CACL_PRODUCT').'" as "type_action"
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (o.id_order = od.id_order)
				WHERE o.current_state = "'.Configuration::get('PROFIT_CACL_STATUS').'" GROUP BY o.id_order');

		$shipping = Db::getInstance()->executeS('
			SELECT 	o.id_order,
					oc.shipping_cost_tax_incl as debit,
					oc.real_shipping as credit,
					(oc.shipping_cost_tax_incl - oc.real_shipping) as profit,
					o.date_add as date_transaction,
					"'.Configuration::get('PROFIT_CACL_SHIPPING').'" as "type_action"
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_carrier` oc ON (o.id_order = oc.id_order)
				WHERE o.current_state = "'.Configuration::get('PROFIT_CACL_STATUS').'"');
	
		$main = array_merge($id_orders,$shipping);
		$main = $this->array_multisort_value($main, 'id_order', SORT_ASC);
		return $main;
	}
	
	
	public function getProfitTransactions() {
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'profitcalculations`');
	}
	
	function array_multisort_value()
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row) {
					$tmp[$key] = $row[$field];
				}
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}

	
    public function initToolbar()
    {
        parent::initToolbar();
        $this->addPageHeaderToolBarModulesListButton();

        if (empty($this->display) && $this->can_import) {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=suppliers',
                'desc' => $this->l('Import')
            );
        }
    }	
    public function postProcess()
    {
        // checks access
        if (Tools::isSubmit('submitAdd'.$this->table) && !($this->tabAccess['add'] === '1')) {
            $this->errors[] = Tools::displayError('You do not have permission to add transaction.');
            return parent::postProcess();
        }

        if (Tools::isSubmit('submitAdd'.$this->table)) {
			$id_profitcalculations = Tools::getValue('id_profitcalculations');
			
			if($id_profitcalculations)
				$profitcalculations = new ProfitCalculationsClass($id_profitcalculations);	
			else 
				$profitcalculations = new ProfitCalculationsClass();
			
			    $profitcalculations->type_action = Tools::getValue('type_action', null);
                $profitcalculations->debit =  Tools::getValue('debit', 0);
                $profitcalculations->credit =  Tools::getValue('credit', 0);
                $profitcalculations->comment =  Tools::getValue('comment', null);
                $profitcalculations->profit =  (int)Tools::getValue('debit') - (int)Tools::getValue('credit');
                $profitcalculations->active =  Tools::getValue('active');
                $profitcalculations->date_transaction =  Tools::getValue('date_transaction');
				
			if ($id_profitcalculations) {
                    $profitcalculations->update();
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    $profitcalculations->save();
					Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                }	
        } elseif (Tools::isSubmit('submitRefresh'.$this->table)) {
			die(var_dump('123'));
        } elseif (Tools::isSubmit('delete'.$this->table)) {
            if (!(Tools::getValue('id_profitcalculations'))) {
                return;
            } else {
                $profitcalculations = new ProfitCalculationsClass((int)Tools::getValue('id_profitcalculations'));
                $profitcalculations->delete();
				Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            return parent::postProcess();
        }
    }
}