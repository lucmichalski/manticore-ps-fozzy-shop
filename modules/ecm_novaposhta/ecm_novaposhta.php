<?php
/*НоваПошта*/
if (!defined('_PS_VERSION_')){exit;}
if (!defined('_VW_')) {define ('_VW_', 4000);}
if(!Configuration::get('ecm'))Configuration::updateValue('ecm', '//0050f');


class ecm_novaposhta extends CarrierModule {
	const PREFIX = 'ecm_';
	private $_last_updated = '';
	public $id_carrier;

	public
	function __construct() {

		$this->name = 'ecm_novaposhta';
		$this->tab = 'shipping_logistics';
		$this->version = '3.6.0';
		$this->author = 'Elcommerce';
		$this->bootstrap = true;
		parent::__construct();
		$this->module_key = 'e1c38ursnmtgo6h0xdl27a4p95ifb_ ';
		$this->displayName = $this->l('NovaPoshta Shipping');
		$this->description = $this->l('Shipping module for NovaPoshta');
		$this->ps_versions_compliancy = array(
			'min' => '1.5',
			'max' => _PS_VERSION_
		);
		if ($this->upgradeCheck('NP'))
			$this->warning = $this->l('We have released a new version of the module, click to upgrade!!!');
		if (!(Currency::getIdByIsoCode('UAH')))
			$this->warning = $this->l('There is currency "UAH" not presently in your shop! Creat it!');
		
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/exec_.php');
		//if(file_exists(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/json.php')) {p ("Отсутствует файл! Настройте антивирус")}
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/json.php');
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/load_files.php');
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/exec.php');
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/api2.php');
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/split.php');
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/npmail.php');
		require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/upgrade.php');

        $this->carrier_active = $this->checkCarrier();
        
        $this->poshtomats = [
            '95dc212d-479c-4ffb-a8ab-8c1b9073d0bc',
            'cab18137-df1b-472d-8737-22dd1d18b51d',
            'f9316480-5f2d-425d-bc2c-ac7cd29decf0',
        ];
        $this->min_poshtomat = 33;
        $this->max_poshtomat = 57;
 
	}

	public $_hooks = array(
		'displayCarrierExtraContent',
		'displayaftercarrier',
		'actionCarrierUpdate',
		'actionCartSave',
		'actionValidateOrder',
		'displayOrderDetail',
		'displayAdminOrder',
		'DisplayHeader',
		'actionAuthentication',
		'actionAdminControllerSetMedia',
		'actionObjectOrderUpdateBefore',
		'actionObjectOrderUpdateAfter',
		'actionObjectOrderCartRuleAddAfter',
		'actionObjectOrderCartRuleDeleteAfter',
		'actionObjectOrderDetailAddAfter',
		'actionObjectOrderDetailDeleteAfter',
		'actionObjectOrderDetailUpdateAfter',
		'ExtraCarrier' //For control change of the carrier's ID (id_carrier), the module must use the updateCarrier hook.
		);

	protected $_carriers = array(
	//"Public carrier name" => "technical name",
		'Нова Пошта' => 'ecm_novaposhta');
	
	public
	function install() {

		if (parent::install()) {
            foreach ($this->_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    return false;
                }
            }
			
            if(!Configuration::get($this->name.'_DONT_TOUCH_CARRIER')){
                $carrier = $this->addCarrier();
                $this->addZones($carrier);
                $this->addGroups($carrier);
                $this->addRanges($carrier);
                Configuration::updateValue($this->name.'_DONT_TOUCH_CARRIER', true);
            }    

            $this->CreateTabs();
            @unlink(_PS_MODULE_DIR_.$SELF.'/key.lic');
            include(dirname(__FILE__).'/upgrade/install.php');

            return true;
		}
		return false;
	}

	public
	function uninstall() {
        //Configuration::updateValue($this->name.'_DONT_TOUCH_CARRIER', true); // for test
		if (parent::uninstall()) {
            if(!Configuration::get($this->name.'_DONT_TOUCH_CARRIER')){
                if (!$this->deleteCarriers()) return false;
                return true;
            }
            $this->DeleteTabs();
            include(dirname(__FILE__).'/upgrade/uninstall.php');
	        return true;

		}
		return false;
	}

    protected function addCarrier(){
        $carrier = new Carrier();

        $carrier->name = 'Нова Пошта';
        $carrier->active = 0;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = true;
        $carrier->shipping_handling = false;
        $carrier->is_module = true;
        $carrier->external_module_name = $this->name;
        $carrier->shipping_method = 1;
        $carrier->grade = 8; // 0-slow, 9-fast
		$carrier->url = 'http://novaposhta.ua/tracking/?cargo_number=@';

		$delay = array('uk' => 'від 1 до 5 днів', 'ru' => 'от 1 до 5 дней', 'en' => '1-5 day',);

		foreach (Language::getLanguages(true) as $language) {
			if (array_key_exists($language['iso_code'], $delay)) {$carrier->delay[$language['id_lang']] = $delay[$language['iso_code']];}
			else {$carrier->delay[$language['id_lang']] = $delay['en'];}
		}


		if ($carrier->add() == true){
            @copy(dirname(__FILE__).'/views/img/'.$this->name.'.jpg', _PS_SHIP_IMG_DIR_.'/'.(int) $carrier->id . '.jpg'); //assign carrier logo
			Configuration::updateValue(self::PREFIX . $this->name, $carrier->id);
			Configuration::updateValue(self::PREFIX . $this->name.'_reference', $carrier->id);
			//Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = '".self::PREFIX . $this->name ."'");
			//Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = '".self::PREFIX . $this->name . "_reference'");

			$md_carriers = json_decode(Configuration::get('ecm_md_carriers'), true);
			$md_carriers[$carrier->id] = 'ecm_newpost';
			Configuration::deleteByName('ecm_md_carriers');
			Configuration::updateValue('ecm_md_carriers', json_encode($md_carriers));
			//Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_md_carriers'");

            
            return $carrier;
        }

        return false;
    }

    protected function addGroups($carrier){
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group){$groups_ids[] = $group['id_group'];}
        $carrier->setGroups($groups_ids);
    }

    protected function addRanges($carrier){
	$price_list = array();
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = 0;
        $range_weight->delimiter2 = 30;
		$range_weight->add();
		foreach ($carrier->getZones() as $zone) {
			$price_list[] = array(
                'id_range_price' => null,
                'id_range_weight' => (int)$range_weight->id,
                'id_carrier' => (int)$carrier->id,
                'id_zone' => (int)$zone['id_zone'],
                'price' => 0,
            );
		}
		$carrier->addDeliveryPrice($price_list, true);
    }

    protected function addZones($carrier){
        $zones = Zone::getZones();
        foreach ($zones as $zone){$carrier->addZone($zone['id_zone']);}
    }
	
	protected function createCarriers() {
		foreach ($this->_carriers as $key => $value) {
			//Create new carrier
			$carrier = new Carrier();
			$carrier->name = $key;
			$carrier->active = false;
			$carrier->deleted = 0;
			$carrier->url = "http://novaposhta.ua/tracking/?cargo_number=@";
			$carrier->shipping_handling = false;
			$carrier->range_behavior = 0;
			$carrier->delay[Configuration::get('PS_LANG_DEFAULT')] = $key;
			$carrier->shipping_external = true;
			$carrier->is_module = true;
			$carrier->external_module_name = $this->name;
			$carrier->need_range = true;
			$carrier->zone = 1;
			$carrier->grade = 0;
			if ($carrier->add()) {
				Db::getInstance()->Execute("
					INSERT INTO `"._DB_PREFIX_ . "carrier_zone`
						(`id_carrier`, `id_zone`) VALUES ('{$carrier->id}', 1)
					ON DUPLICATE KEY UPDATE
						id_carrier = VALUES(id_carrier),
						id_zone = VALUES(id_zone)
					");

				$groups = Group::getGroups(true);
				foreach ($groups as $group) {
					// Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_group', array(
						// 'id_carrier' => (int) $carrier->id,
						// 'id_group' => (int) $group['id_group']
					// ), 'INSERT');
					Db::getInstance()->insert('carrier_group', array(
						'id_carrier' => (int) $carrier->id,
						'id_group' => (int) $group['id_group']
					));
				}

				$rangePrice = new RangePrice();
				$rangePrice->id_carrier = $carrier->id;
				$rangePrice->delimiter1 = '0';
				$rangePrice->delimiter2 = '10000';
				$rangePrice->id_zone = 1;
				$rangePrice->price = 1;
				$rangePrice->add();
				$rangeWeight = new RangeWeight();
				$rangeWeight->id_carrier = $carrier->id;
				$rangeWeight->delimiter1 = '0';
				$rangeWeight->delimiter2 = '10000';
				$rangeWeight->id_zone = 1;
				$rangeWeight->price = 1;
				$rangeWeight->add();

				copy(dirname(__FILE__) . '/views/img/' . $value . '.jpg', _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg'); //assign carrier logo
				Configuration::updateValue(self::PREFIX . $value, $carrier->id);
				Configuration::updateValue(self::PREFIX . $value.'_reference', $carrier->id);
				//Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = '".self::PREFIX . $value ."'");
				//Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = '".self::PREFIX . $value . "_reference'");

				$md_carriers = json_decode(Configuration::get('ecm_md_carriers'), true);
				$md_carriers[$carrier->id] = 'ecm_newpost';
				Configuration::deleteByName('ecm_md_carriers');
				Configuration::updateValue('ecm_md_carriers', json_encode($md_carriers));
				//Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_md_carriers'");
			}
		}
		return TRUE;
	}

	protected function deleteCarriers() {
		foreach ($this->_carriers as $value) {
			$tmp_carrier_id = Configuration::get(self::PREFIX . $this->name);
			$carrier = new Carrier($tmp_carrier_id);
			$carrier->delete();
			//Configuration::deleteByName(self::PREFIX . $this->name);
			//Configuration::deleteByName(self::PREFIX . $this->name.'_reference');
			$md_carriers = json_decode(Configuration::get('ecm_md_carriers'), true);
			unset($md_carriers[$tmp_carrier_id]);
			//Configuration::deleteByName('ecm_md_carriers');
			Configuration::updateValue('ecm_md_carriers', json_encode($md_carriers));
			//Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_md_carriers'");
		}
        Configuration::updateValue($this->name.'_DONT_TOUCH_CARRIER', false);
		return TRUE;
	}

	public
	function hookactionCartSave($params) {
		if(isset($params['cart']->id) && $this->carrier_active){
			exec::fixCost($params['cart']);
		}
		return true;
	}

	public function getOrderShippingCost($params, $shipping_cost) 
	{
		if($this->carrier_active){
			$cost = exec::getCost($params);
            return $cost['cost_pr'] + $shipping_cost;
       }
       return $shipping_cost;
	}

	public function getOrderShippingCostExternal($params) {
		unset ($params);
		return true;
	}

	public function hookActionCarrierUpdate($params) {
		if ($params['carrier']->id_reference == Configuration::get(self::PREFIX . $this->name . '_reference')) {
			$ecm_novaposhta_prev = Configuration::get(self::PREFIX . $this->name);
			Configuration::deleteByName(self::PREFIX . $this->name);
			Configuration::updateValue(self::PREFIX . $this->name, $params['carrier']->id);
			Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = '".self::PREFIX . $this->name."'");
			exec::ChangeCarriers($ecm_novaposhta_prev, Configuration::get(self::PREFIX . $this->name));

			$md_carriers = json_decode(Configuration::get('ecm_md_carriers'), true);
			unset($md_carriers[$ecm_novaposhta_prev]);
			$md_carriers[$params['carrier']->id] = 'ecm_newpost';
			Configuration::deleteByName('ecm_md_carriers');
			Configuration::updateValue('ecm_md_carriers', json_encode($md_carriers));
			Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_md_carriers'");
		}
	}

	private function _displayabout() {
		$secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
		$this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
        <div class="panel"><div class="panel-heading"><i class="icon-info"></i> '.$this->l('Information').'</div>
        <span><b>' . $this->l('Version') . ':</b> ' . $this->version . '</span><br>
        <span><b>' . $this->l('Developer') . ':</b> <a class="link" href="mailto:admin@elcommerce.com.ua" target="_blank">Mice  </a>
        <span><b>' . $this->l('Decription') . ':</b> <a class="link" href="https://elcommerce.com.ua" target="_blank">ElCommerce.com.ua</a><br>
        <span><b>' . $this->l('Licension key') . ':</b>  <a class="link" href="'.Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/key_changer.php?secure_key='.$secureKey.'" target="_blank">'. $this->l(Configuration::get(self::PREFIX . 'np_LIC_KEY')) . '</a></span><br><br>
        <p style="text-align:center"><a href="https://elcommerce.com.ua/" target="_blank"><img src="https://elcommerce.com.ua/img/m/logo.png" alt="Электронный учет коммерческой деятельности" /></a>
        </div>
        ';

	}

	private function _advanced_checkout() {
		global $cookie;
		$this->context->smarty->assign(array(
			'fill' => (Configuration::get(self::PREFIX . 'np_fill') ? 'checked="checked" ' : ''),
			'ac' => (Configuration::get(self::PREFIX . 'np_ac') ? 'checked="checked" ' : ''),

		));
		if (Configuration::get(self::PREFIX . 'np_LIC_KEY') and Configuration::get(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee))
			$this->_html .= $this->display(__FILE__, 'views/advcheckout.tpl');

	}

	private function _areas() {
		global $cookie;
		if (Configuration::get(self::PREFIX . 'np_LIC_KEY') and Configuration::get(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee)){
			global $cookie;
			$BlockedWarehouse = json_decode(Configuration::get(self::PREFIX . 'np_BlockedWarehouse'));
			$BlockedWarehouse = is_array($BlockedWarehouse)?$BlockedWarehouse:array();	
			$BlockedAreas = json_decode(Configuration::get(self::PREFIX . 'np_BlockedAreas'));
			$BlockedAreas = is_array($BlockedAreas)?$BlockedAreas:array();
			
			$select = exec_::select((string)$this->context->employee->id_lang);
			$countwh = Db::getInstance()->GetValue("SELECT COUNT(`ref`) FROM `" . _DB_PREFIX_ . "ecm_newpost_warehouse`");
			$this->context->smarty->assign(array(
				'employee' => $this->context->employee->id,
				'Area' => exec::getout('area',-$cookie->id_employee),
				'City' => exec::getout('city',-$cookie->id_employee),
				'Ware' => exec::getout('ref',-$cookie->id_employee),
				
				'StreetType' => exec::getout('StreetType',-$cookie->id_employee),
				'StreetName' => exec::getout('StreetName',-$cookie->id_employee),
				'StreetRef' => exec::getout('StreetRef',-$cookie->id_employee),
				'BuildingNumber' => exec::getout('BuildingNumber',-$cookie->id_employee),
				'Flat' => exec::getout('Flat',-$cookie->id_employee),
				'AddressRef' => exec::getout('AddressRef',-$cookie->id_employee),

				'Areas' => exec::areaList2($select,true),
				'Citys' => exec::cityList2(exec::getout('area',-$cookie->id_employee), $select),
				'Wares' => exec::wareList2(exec::getout('city',-$cookie->id_employee), $select),
				'BlockedWarehouse' =>  $BlockedWarehouse,
				'BlockedAreas' => $BlockedAreas,
				'WarehouseTypes' =>  Exec::simpleList('WarehouseTypes'),
				'countwh' => $countwh,
				'separatePlace' => Configuration::get(self::PREFIX . 'np_separatePlace') ? 'checked="checked" ' : '',
				'capital_top' => Configuration::get(self::PREFIX . 'np_capital_top') ? 'checked="checked" ' : '',
                'DONT_TOUCH_CARRIER' => Configuration::get($this->name.'_DONT_TOUCH_CARRIER') ? 'checked="checked" ' : '',

			));

			$this->_html .= $this->display(__FILE__, 'views/areas.tpl');
		}
	}


	private
	function _privelegy() {
		$ps_groups = Group::getGroups($this->context->employee->id_lang);
		foreach ($ps_groups as $group){$groups[$group['id_group']] = $group['name'];}
		$select = exec_::select((string)$this->context->employee->id_lang);
		$this->context->smarty->assign(array(
			'employee' => $this->context->employee->id,
			'privileged_ware' =>  json_decode(Configuration::get(self::PREFIX . 'np_privileged_ware')),
			'WarehouseTypes' =>  Exec::simpleList('WarehouseTypes'),
			'privileged_group' =>  json_decode(Configuration::get(self::PREFIX . 'np_privileged_group')),
			'groups' =>  $groups,
		));
		$this->_html .= $this->display(__FILE__, 'views/privelegy.tpl');
	}


	private
	function _cron() {
		$secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
		$domain = Tools::getHttpHost(true);
		$cron_url = $domain.__PS_BASE_URI__.'modules/ecm_novaposhta/cron.php?secure_key='.$secureKey;
		$class    = version_compare(_PS_VERSION_, '1.6', '>=') ? 'alert alert-info' : 'description';
		$this->context->smarty->assign(array(
			'cron_url' => $cron_url,
			'class' => $class,

		));
		$this->_html .= $this->display(__FILE__, 'views/cron.tpl');
	}

	private
	function _displayupgrade() {
		$this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
        <fieldset class="space">
        <legend><img src="../img/admin/module_warning.png" /> ' . $this->l('Upgrade') . '</legend>
        <div id="dev_div">
        <span style="color:red"><b>' . $this->l('New version is available!!! Please, click button "Upgrade" to install it!') . ':</b></span>
        <center><hr>
        <input class="button" type="submit" name="submitUPGR" value="' . $this->l('Upgrade') . '" />
        </center>
        </div>
        </fieldset>
        ';
	}

	private
	function _counterparties() {
		global $cookie;
		if (Configuration::get(self::PREFIX . 'np_LIC_KEY') and Configuration::get(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee)){
			$by_order = Configuration::get(self::PREFIX . 'np_by_order_'.$cookie->id_employee) ? "": "readonly='readonly'";
			$this->context->smarty->assign(array(
				'counterparties' => np::getCounterparties("Sender", $cookie->id_employee),
				'by_order' => $by_order,
				'counterparty' => exec::getout('counterparty', -$cookie->id_employee),

			));
			$this->_html .= $this->display(__FILE__, 'views/counterparties.tpl');
		}
	}

	private
	function _contactPersons() {
		global $cookie;
		if (Configuration::get(self::PREFIX . 'np_LIC_KEY') and Configuration::get(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee)){
			$counterparties = np::getCounterparties("Sender", $cookie->id_employee);
			//$counterparty = Configuration::get(self::PREFIX . 'np_by_order_'.$cookie->id_employee) ? ((exec::getout('counterparty', -$cookie->id_employee) != '') ? exec::getout('counterparty', -$cookie->id_employee) : $counterparties[0]->Ref) : $counterparties[0]->Ref;
			$counterparty = exec::getout('counterparty', -$cookie->id_employee) != '' ? exec::getout('counterparty', -$cookie->id_employee) : $counterparties[0]->Ref ;
			$contacts = np::getCounterpartyContactPersons($counterparty, $cookie->id_employee);
			if (!count($contacts) 
				and !Configuration::get(self::PREFIX . 'np_by_order_'.$cookie->id_employee)
				and exec::getout('city',-$cookie->id_employee)){
				//np::CreateLoyality($cookie->id_employee);
				$contacts = np::getCounterpartyContactPersons($counterparty, $cookie->id_employee);
			}
			
			$this->context->smarty->assign(array(
				'by_order' => Configuration::get(self::PREFIX . 'np_by_order_'.$cookie->id_employee) ? "": "readonly='readonly'",
				'active' => exec::getout('contact', -$cookie->id_employee),
				'contacts' => $contacts,
				'counterparty' => $counterparty,

			));
			$this->_html .= $this->display(__FILE__, 'views/contactPersons.tpl');
		}
	}

	private
	function _settings() {
		global $cookie;
		if (!function_exists('curl_version')){
			$this->_html .= '
                <div class="bootstrap">
                <div class="alert alert-danger">
                <btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
                Не установлено расширение <b>curl</b>, Работа модуля невозможна !!!
                </div>
                </div>
                ';
			return;
		}
		if (!Configuration::get(self::PREFIX . 'np_LIC_KEY')) {
			$this->_html .= '<fieldset class="space">
            <legend><img src="../img/admin/cog.gif" alt="" class="middle" />' . $this->l('Settings') . '</legend>
            <div class="col-xs-12">
            <label>' . $this->l('Licension key') . '</label>
            <div class="margin-form">
            <input type="text" name="LIC_KEY" placeholder="' . $this->l('Licension key') . '" required
            value=""/>
            <p class="clear">' . $this->l('Enter your Licension key ') . '</p>
            </div>
            <center><hr>
            <input class="button" type="submit" name="submitLIC" value="' . $this->l('Save') . '" />
            </center>
            ';
			return;
		}
		$by_order = Configuration::get(self::PREFIX . 'np_by_order_'.$cookie->id_employee) ? 'checked="checked" ' : '';

		if (!Configuration::get(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee)) {
			$this->_html .= '<fieldset class="space">
            <legend><img src="../img/admin/cog.gif" alt="" class="middle" />' . $this->l('Settings') . '</legend>
            <input id="customer" name="customer" type="hidden" value ="-{$cookie->id_employee}"/>
            <input id="employee" name="employee" type="hidden" value ="-{$cookie->id_employee}"/>
            <input type="hidden" name="TrimMsg" value="101"/>
            <input type="hidden" name="comiso" value="2"/>
            <input type="hidden" name="insurance" value="0"/>
            <div class="col-xs-12">
            <label>' . $this->l('API key') . '</label>
            <div class="margin-form">
            <input type="text" name="API_KEY" placeholder="' . $this->l('API key') . '" required
            value=""/>
            <p class="clear">' . $this->l('Enter your API key (in the particular office at site "Nova poshta")') . '</p>
            </div>
            <label>' . $this->l('By oder') . '</label>
            <div class="margin-form">
            <input type="checkbox" name="by_order"  value="1" '.$by_order.' />
            <p class="clear">' . $this->l('Check if you work by "Order", else "Loyality programm"') . '</p>
            </div>
            <center><hr>
            <input class="button" type="submit" name="submitAPI" value="' . $this->l('Save') . '" />
            </center>
            ';
			return;
		}

		$currency_def = Currency::getDefaultCurrency();

		$this->context->smarty->assign(array(
			'sign' => $currency_def->name,
			'show' => (Configuration::get(self::PREFIX . 'np_show') ? 'checked="checked" ' : ''),
			'byorder' => Configuration::get(self::PREFIX . 'np_by_order_'.$cookie->id_employee) ?  'checked="checked" ' : '',
			'out_company' => Configuration::get(self::PREFIX . 'np_out_company'),
			'out_name' => Configuration::get(self::PREFIX . 'np_out_name'),
			'out_phone' => Configuration::get(self::PREFIX . 'np_out_phone'),
			'out_email' => Configuration::get(self::PREFIX . 'np_out_email'),
			'API_KEY' => Configuration::get(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee),
			'LIC_KEY' => Configuration::get(self::PREFIX . 'np_LIC_KEY'),
			'percentage' => Configuration::get(self::PREFIX . 'np_percentage')?Configuration::get(self::PREFIX . 'np_percentage'):2,
			'comiso' => Configuration::get(self::PREFIX . 'np_comiso'),
			'np_module_dir' => _MODULE_DIR_ . $this->name,
			'counterparty' => exec::getout('counterparty', -$cookie->id_employee),
			'payment_form' => Configuration::get(self::PREFIX . 'np_payment_method'),
			'payment_forms' => Exec::simpleList('PaymentForms'),
			'Ownership' => Configuration::get(self::PREFIX . 'np_Ownership'),
			'OwnershipFormsList' => Exec::simpleList('OwnershipFormsList', 'FullName'),
			'CargoType' => Configuration::get(self::PREFIX . 'np_CargoType'),
			'CargoTypes' => Exec::simpleList('CargoTypes'),
			'description' => Configuration::get(self::PREFIX . 'np_description'),
			'pack' => Configuration::get(self::PREFIX . 'np_pack'),
			'insurance' => Configuration::get(self::PREFIX . 'np_insurance'),
			'time' => Configuration::get(self::PREFIX . 'np_time'),
			'format' => Configuration::get(self::PREFIX . 'np_format'),
			'formats' => array('HTML'=>'HTML','PDF'=>'PDF'),
			'InfoRegClientBarcode' => Configuration::get(self::PREFIX . 'np_InfoRegClientBarcode'),
			'InfoRegClientBarcodes' => array(
				'' => $this->l('None'),
				'id' => $this->l('Id order'),
				'reference' => $this->l('Reference'),
			),
			'redelivery' => (Configuration::get(self::PREFIX . 'np_redelivery') ? 'checked="checked" ' : ''),
			'senderpay' => (Configuration::get(self::PREFIX . 'np_senderpay') ? 'checked="checked" ' : ''),
			'ignore_freelimit' => (Configuration::get(self::PREFIX . 'np_ignore_freelimit') ? 'checked="checked" ' : ''),
			'cod_manual_correction' => (Configuration::get(self::PREFIX . 'np_cod_manual_correction') ? 'checked="checked" ' : ''),
			'buyerpay' => (Configuration::get(self::PREFIX . 'np_buyerpay') ? 'checked="checked" ' : ''),
			'senderpay_redelivery' => (Configuration::get(self::PREFIX . 'np_senderpay_redelivery') ? 'checked="checked" ' : ''),
			'add_msg' => (Configuration::get(self::PREFIX . 'np_add_msg') ? 'checked="checked" ' : ''),
			'SendNPmail' => (Configuration::get(self::PREFIX . 'np_SendNPmail') ? 'checked="checked" ' : ''),
			'SendNPadminmail' => Configuration::get(self::PREFIX . 'np_SendNPadminmail'),
			'weght' => Configuration::get(self::PREFIX . 'np_weght'),
			'vweght' => Configuration::get(self::PREFIX . 'np_vweght'),
			'fixcost' => Configuration::get(self::PREFIX . 'np_fixcost'),
			'fixcost_address' => Configuration::get(self::PREFIX . 'np_fixcost_address'),
			'chk_fixcost' => (Configuration::get(self::PREFIX . 'np_chk_fixcost') ? 'checked="checked" ' : ''),
			'only_fixcost' => (Configuration::get(self::PREFIX . 'np_only_fixcost') ? 'checked="checked" ' : ''),
			'chk_fixcost_cod' => (Configuration::get(self::PREFIX . 'np_chk_fixcost_cod') ? 'checked="checked" ' : ''),
			'no_add_to_total' => (Configuration::get(self::PREFIX . 'np_no_add_to_total') ? 'checked="checked" ' : ''),
			'by_order' => $by_order,
			'addtoorder' => (Configuration::get(self::PREFIX . 'np_addtoorder') ? 'checked="checked" ' : ''),
			'FreeLimit' => Configuration::get(self::PREFIX . 'np_FreeLimit'),
			'FreeLimitMaxWeight' => Configuration::get(self::PREFIX . 'np_FreeLimitMaxWeight'),
			'FreeLimitAddr' => Configuration::get(self::PREFIX . 'np_FreeLimitAddr'),
			'FreeLimitMaxWeightAddr' => Configuration::get(self::PREFIX . 'np_FreeLimitMaxWeightAddr'),
			'lang' => $this->context->employee->id_lang,
			'TrimMsg' => Configuration::get(self::PREFIX . 'np_TrimMsg'),
			'address_delivery' => Configuration::get(self::PREFIX . 'np_address_delivery') ? 'checked="checked" ' : '',
			'address_delivery_def' => Configuration::get(self::PREFIX . 'np_address_delivery_def') ? 'checked="checked" ' : '',
			'another_recipient' => Configuration::get(self::PREFIX . 'np_another_recipient') ? 'checked="checked" ' : '',
			'ignore_real_paid' => Configuration::get(self::PREFIX . 'np_ignore_real_paid') ? 'checked="checked" ' : '',
			'AfterpaymentOnGoods' => Configuration::get(self::PREFIX . 'np_AfterpaymentOnGoods') ? 'checked="checked" ' : '',

			'add_COD' => Configuration::get(self::PREFIX . 'np_add_COD') ? 'checked="checked" ' : '',
			
			'invoice_address' => Configuration::get('ecm_md_invoice_address') ? 'checked="checked" ' : '',
			'correct_address' => Configuration::get('ecm_md_correct_address') ? 'checked="checked" ' : '',
			'middlename' => (Configuration::get('ecm_simcheck_middlename') or Configuration::get('ecm_checkout_middlename')) ? 'checked="checked" ' : '',
			'another_alias' => Configuration::get('ecm_np_another_alias'),

		));
		$this->_html .= $this->display(__FILE__, 'views/settings.tpl');
	}


	public
	function getContent() {
		global $cookie;
		$this->_html = '<h2>' . $this->l('Delivery "Nova poshta"') . '</h2>';
		//$this->postProcess();
		$this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <input id="customer" name="customer" type="hidden" value ="-'.$cookie->id_employee.'"/>
            <input id="employee" name="employee" type="hidden" value ="-'.$cookie->id_employee.'"/>
		';
		if (Tools::isSubmit('submitWarehouse')) {
			Configuration::updateValue(self::PREFIX . 'np_capital_top', Tools::getValue('capital_top') ? 1 : 0);
			if ($errors = exec::getLists()) {
				$errorlist = '';
				foreach ($errors as $error)
					$errorlist .= '<li>' . $error . '</li>';
				$this->_html .= '
	            <div class="bootstrap">
	            <div class="alert alert-danger">
	            <button type="button" class="close" data-dismiss="alert">×</button>
	            <h4>' . $this->l('Errors detect" !!!') . '</h4>
	            <ul class="list-unstyled">' . $errorlist . '</ul>
	            </div>
	            </div>
	            ';
			}
			$arr = $arr_c = $arr_a = array();
			$arr = np::warehouse();
			$arr_c = np::city();
			$arr_a = np::area();
			//p($arr);
			if(count($arr) and count($arr_c) and count($arr_a) and Exec::warehouse($arr, $arr_c, $arr_a)) {
				$this->_html .= '
				<div class="bootstrap">
				<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">×</button>
				' . $this->l('Settings refresh') . '
				</div>
				</div>';
			}
			else{
				$this->_html .= '
				<div class="bootstrap">
				<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">×</button>
				' . $this->l('Failed recive warehouses, citys or areas') . '
				</div>
				</div>';
			}
		} else {
			if (null == Configuration::get(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee))
				$this->_html .= '
            <div class="bootstrap">
            <div class="alert error">
            <button type="button" class="close" data-dismiss="alert">×</button>
            ' . $this->l('Since fill in "Settings" !!!') . '
            </div>
            </div>
            ';
		}

		if (Tools::isSubmit('submitLIC')) {
			$pattern = '/^[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}$/';
			$lic_key = Configuration::get(self::PREFIX . 'np_LIC_KEY');
			if (empty($lic_key))
				$lic_key = Tools::getValue('LIC_KEY');
			preg_match($pattern, $lic_key, $matches);
			if (sizeof($matches)){
				Configuration::updateValue(self::PREFIX . 'np_LIC_KEY', $lic_key);
				Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_np_LIC_KEY'");
			} else {
				$this->_html .= '
                <div class="bootstrap">
                <div class="alert alert-danger">
                <btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
                Укажите валидный лицензионный ключ!!!
                </div>
                </div>
                ';
			}
		}
		if (Tools::isSubmit('submitUPGR')) {

			upgrade::load();
			header("Refresh:0");
			$this->_html .= '
            <div class="bootstrap">
            <div class="alert alert-success">
            <btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
            Обновление успешно завершено!!!
            </div>
            </div>
            ';

		}
		if (Tools::isSubmit('submitAPI')) {
			$pattern = '/^[0-9a-f]{32}$/';
			$api_key = Tools::getValue('API_KEY');
			preg_match($pattern, $api_key, $matches);
			if (sizeof($matches)){
				Configuration::updateValue(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee, $api_key);
				Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_np_API_KEY_".$cookie->id_employee."'");
			}
			else {
				$this->_html .= '
                <div class="bootstrap">
                <div class="alert alert-danger">
                <btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
                API ключ не соответствует требованиям!!!
                </div>
                </div>
                ';
			}
			Configuration::updateValue(self::PREFIX . 'np_comiso', Tools::getValue('comiso'));
			Configuration::updateValue(self::PREFIX . 'np_TrimMsg', Tools::getValue('TrimMsg'));
			Configuration::updateValue(self::PREFIX . 'np_insurance', Tools::getValue('insurance'));
			Configuration::updateValue(self::PREFIX . 'np_by_order_'.$cookie->id_employee, Tools::getValue('by_order'));

		}

		if (Tools::isSubmit('submitUPDATE')) {
			$pattern = '/^[0-9a-f]{32}$/';
			$api_key = Tools::getValue('API_KEY');
			preg_match($pattern, $api_key, $matches);
			if (sizeof($matches)){
				Configuration::updateValue(self::PREFIX . 'np_API_KEY_'.$cookie->id_employee, $api_key, $api_key);
				Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_np_API_KEY_".$cookie->id_employee."'");
			} else {
				$this->_html .= '
                <div class="bootstrap">
                <div class="alert alert-danger">
                <btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
                API ключ не соответствует требованиям!!!
                </div>
                </div>
                ';
			}
			Configuration::updateValue(self::PREFIX . 'np_by_order_'.$cookie->id_employee, Tools::getValue('by_order') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_out_company', Tools::getValue('out_company'));
			Configuration::updateValue(self::PREFIX . 'np_out_name', Tools::getValue('out_name'));
			Configuration::updateValue(self::PREFIX . 'np_out_phone', Tools::getValue('out_phone'));
			Configuration::updateValue(self::PREFIX . 'np_out_email', Tools::getValue('out_email'));
			Configuration::updateValue(self::PREFIX . 'np_time', Tools::getValue('time'));
			Configuration::updateValue(self::PREFIX . 'np_weght', Tools::getValue('weght'));
			Configuration::updateValue(self::PREFIX . 'np_vweght', Tools::getValue('vweght'));
			Configuration::updateValue(self::PREFIX . 'np_fixcost', Tools::getValue('fixcost'));
			Configuration::updateValue(self::PREFIX . 'np_fixcost_address', Tools::getValue('fixcost_address'));
			Configuration::updateValue(self::PREFIX . 'np_chk_fixcost', Tools::getValue('chk_fixcost') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_only_fixcost', Tools::getValue('only_fixcost') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_chk_fixcost_cod', Tools::getValue('chk_fixcost_cod') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_no_add_to_total', Tools::getValue('no_add_to_total') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_addtoorder', Tools::getValue('addtoorder') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_add_msg', Tools::getValue('add_msg') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_show', Tools::getValue('show') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_fill', Tools::getValue('fill') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_ac', Tools::getValue('ac') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_redelivery', Tools::getValue('redelivery') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_senderpay', Tools::getValue('senderpay') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_ignore_freelimit', Tools::getValue('ignore_freelimit') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_cod_manual_correction', Tools::getValue('cod_manual_correction') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_buyerpay', Tools::getValue('buyerpay') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_SendNPmail', Tools::getValue('SendNPmail') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_SendNPadminmail', Tools::getValue('SendNPadminmail'));
			Configuration::updateValue(self::PREFIX . 'np_senderpay_redelivery', Tools::getValue('senderpay_redelivery') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_percentage', Tools::getValue('percentage'));
			Configuration::updateValue(self::PREFIX . 'np_comiso', Tools::getValue('comiso'));
			Configuration::updateValue(self::PREFIX . 'np_payment_method', Tools::getValue('payment_method'));
			Configuration::updateValue(self::PREFIX . 'np_description', Tools::getValue('description'));
			Configuration::updateValue(self::PREFIX . 'np_pack', Tools::getValue('pack'));
			Configuration::updateValue(self::PREFIX . 'np_insurance', Tools::getValue('insurance'));
			Configuration::updateValue(self::PREFIX . 'np_format', Tools::getValue('format'));
			Configuration::updateValue(self::PREFIX . 'np_InfoRegClientBarcode', Tools::getValue('InfoRegClientBarcode'));
			Configuration::updateValue(self::PREFIX . 'np_CargoType', Tools::getValue('CargoType'));
			Configuration::updateValue(self::PREFIX . 'np_TrimMsg', Tools::getValue('TrimMsg'));
			Configuration::updateValue(self::PREFIX . 'np_FreeLimit', Tools::getValue('FreeLimit'));
			Configuration::updateValue(self::PREFIX . 'np_FreeLimitMaxWeight', Tools::getValue('FreeLimitMaxWeight'));
			Configuration::updateValue(self::PREFIX . 'np_FreeLimitAddr', Tools::getValue('FreeLimitAddr'));
			Configuration::updateValue(self::PREFIX . 'np_FreeLimitMaxWeightAddr', Tools::getValue('FreeLimitMaxWeightAddr'));
			Configuration::updateValue(self::PREFIX . 'np_Ownership', Tools::getValue('Ownership'));
			Configuration::updateValue(self::PREFIX . 'np_Employee', -$cookie->id_employee);
			Configuration::updateValue(self::PREFIX . 'np_BlockedWarehouse', json_encode(Tools::getValue('BlockedWarehouse')));
			Configuration::updateValue(self::PREFIX . 'np_BlockedAreas', json_encode(Tools::getValue('BlockedAreas')));
			Configuration::updateValue(self::PREFIX . 'np_privileged_group', json_encode(Tools::getValue('privileged_group')));
			Configuration::updateValue(self::PREFIX . 'np_privileged_ware', json_encode(Tools::getValue('privileged_ware')));
			Configuration::updateValue(self::PREFIX . 'np_another_recipient', Tools::getValue('another_recipient') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_address_delivery', Tools::getValue('address_delivery') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_address_delivery_def', Tools::getValue('address_delivery_def') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_separatePlace', Tools::getValue('separatePlace') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_capital_top', Tools::getValue('capital_top') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_ignore_real_paid', Tools::getValue('ignore_real_paid') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_AfterpaymentOnGoods', Tools::getValue('AfterpaymentOnGoods') ? 1 : 0);
			Configuration::updateValue(self::PREFIX . 'np_add_COD', Tools::getValue('add_COD') ? 1 : 0);
			Configuration::updateValue($this->name.'_DONT_TOUCH_CARRIER', Tools::getValue('DONT_TOUCH_CARRIER') ? 1 : 0);

			Configuration::updateValue('ecm_simcheck_middlename', Tools::getValue('middlename') ? 1 : 0);
			Configuration::updateValue('ecm_checkout_middlename', Tools::getValue('middlename') ? 1 : 0);
			Configuration::updateValue('ecm_md_invoice_address', Tools::getValue('invoice_address') ? 1 : 0);
			Configuration::updateValue('ecm_md_correct_address', Tools::getValue('correct_address') ? 1 : 0);
			Configuration::updateValue('ecm_np_another_alias', Tools::getValue('another_alias'));
		}
		exec_::set_capital();

		if ($this->upgradeCheck('NP'))
			$this->_displayupgrade();
		$this->_areas();
		//$this->_privelegy();
		$this->_settings();
		$this->_counterparties();
		$this->_contactPersons();
		$this->_cron();
		$this->_advanced_checkout();
		$this->_displayabout();
		$this->_html .= '</form>';

		return $this->_html;
	}

	public function hookDisplayHeader() {
       if($this->carrier_active){
            if (method_exists($this->context->controller, 'registerJavascript')){
                $this->context->controller->registerJavascript($this->name.'_np.js', '/modules/'.$this->name.'/views/js/np.js', 
                    ['position' => 'bottom', 'priority' => 333]
                );
            }
            elseif (method_exists($this->context->controller, 'addJS')){
                $this->context->controller->addJS($this->_path.'/views/js/np.js');
            }

            if (method_exists($this->context->controller, 'addJqueryPlugin')){
                $this->context->controller->addJqueryPlugin(array('growl'));
            }
            
            Media::addJsDef(array($this->name => [
                'module_dir' => _MODULE_DIR_ .$this->name.'/classes/refresh.php',
                'version' => $this->version,
                'id' => Configuration::get(self::PREFIX . $this->name),
                'show' => Configuration::get(self::PREFIX . 'np_show'),
                'show_another' => Configuration::get(self::PREFIX . 'np_another_recipient'),
                'ac' => Configuration::get(self::PREFIX . 'np_ac'),
                'ecm_checkout_active' => Configuration::get('ecm_checkout_active'),
                'capital_top' => Configuration::get('ecm_np_capital_top'),
                'address_default' => (int)(Configuration::get('ecm_np_address_delivery') and Configuration::get('ecm_np_address_delivery_def')),
                'page' => 'cart',
            ]));
       }
	}
	
	public function hookactionAdminControllerSetMedia() {
        if(   @$this->context->controller->controller_name == 'AdminNPStatuses'
           or @$this->context->controller->controller_name == 'AdminOrders' 
		   or @$this->context->smarty->tpl_vars['module_name'] == $this->name)
        {
            if (method_exists($this->context->controller, 'addCSS')){
				$this->context->controller->addCSS(($this->_path) . 'views/css/ecm_novaposhta.css', 'all');
			}
			if (method_exists($this->context->controller, 'addJS')){
				$this->context->controller->addJS($this->_path.'/views/js/np.js?'.filemtime(dirname(__FILE__).'/views/js/np.js'));
				$this->context->controller->addJS($this->_path.'/views/js/back.js?'.filemtime(dirname(__FILE__).'/views/js/back.js'));
			}
		}
		
		Media::addJsDef(array($this->name => [
			'module_dir' => _MODULE_DIR_ .$this->name.'/classes/refresh.php',
			'id' => Configuration::get(self::PREFIX . $this->name),
			'show_another' => Configuration::get(self::PREFIX . 'np_another_recipient'),
			'capital_top' => Configuration::get('ecm_np_capital_top'),
			'address_default' => (int)(Configuration::get('ecm_np_address_delivery') and Configuration::get('ecm_np_address_delivery_def')),
        ]));

	}

	public function hookactionAuthentication($params){
		$cart_np = exec::GetCartNP($params['cart']->id);
		if (!$cart_np['area']){
			Db::getInstance()->delete('ecm_newpost_cart', "id_cart ='{$params['cart']->id}'");
		}
	}

	public
	function hookdisplayCarrierExtraContent($params) {  //supercheckout comment this line
//	function hookdisplayaftercarrier($params) {         //supercheckout uncomment this line
		$np_id = Configuration::get(self::PREFIX . $this->name);
		$md_carriers = json_decode(Configuration::get('ecm_md_carriers'),true);
		setcookie('md_carriers', json_encode($md_carriers), time() + 600, '/', Tools::getShopDomain());
		if (1) {                                        //supercheckout comment this line
//		if ($np_id == $params['cart']->id_carrier) {    //supercheckout uncomment this line
			setcookie('npid', $np_id, time() + 600, '/', Tools::getShopDomain());
			$select = exec_::select($params['cart']->id_lang);
			$cart_np = exec::GetCartNP($params['cart']->id);
			$cart_np['another_recipient']= $cart_np['another_recipient'] ? "checked='checked' ":"";
			$cartdetails = exec::cartdetails($params['cart']->id);
			//$address = exec::CheckAddress($params['cart'], $np_id);
			$this->context->smarty->assign(array(
				'np_id' => $np_id,
				'cart_id' => $params['cart']->id,
				'customer_id' => $params['cart']->id_customer,
				'id_lang' => $params['cart']->id_lang,
				'cart_np' => $cart_np,
				'id_carrier' => $params['cart']->id_carrier,
				'cartdetails' => $cartdetails,
				'currency_sign' => 'грн.',
				'employee' => Configuration::get(self::PREFIX . 'np_Employee'),
				'fixcost' => Configuration::get(self::PREFIX . 'np_chk_fixcost'),
				'fill' => Configuration::get(self::PREFIX . 'np_fill'),
				'show' => Configuration::get(self::PREFIX . 'np_show'),
				'show_another' => Configuration::get(self::PREFIX . 'np_another_recipient'),
				'ac' => Configuration::get(self::PREFIX . 'np_ac'),
				'pnp' => (int)Db::getInstance()->GetValue("
					SELECT ms.`id_shop` FROM `"._DB_PREFIX_."module` m
					LEFT JOIN `"._DB_PREFIX_."module_shop` ms ON ms.`id_module` = m.`id_module`
					WHERE m.`name` = 'ecm_cashnovaposta' OR m.`name` = 'ecm_cashnovaposhta'"),
				'Areas' => exec::areaList2($select),
				'Citys' => exec::cityList2($cart_np['area'], $select),
				'Wares' => exec::wareList2($cart_np['city'], $select),
                'np_module_dir' => _MODULE_DIR_ . $this->name,
                'address_delivery_label' => $select['address'],
			));
			//$carrier.extraContent in tpl
			//return $this->context->smarty->fixcost;
			if (Configuration::get('ecm_checkout_active')) {return $this->display(__FILE__, '/sc7/changeshipping.tpl');}
			if (Configuration::get(self::PREFIX . 'np_ac')){return $this->display(__FILE__, '/7opc/changeshipping.tpl');}
			else{return $this->display(__FILE__, '/7/changeshipping.tpl');}
		}
		else{
			if(!array_key_exists($params['cart']->id_carrier, $md_carriers) and Configuration::get(self::PREFIX . 'np_fill')){
                $this->context->smarty->assign (array('id_carrier' => $params['cart']->id_carrier,));
				return $this->display(__FILE__, 'hide.tpl');
			}
		}
	}


	public
	function hookExtraCarrier($params) {
		$np_id = Configuration::get(self::PREFIX . $this->name);
//		if($params['cart']->id_carrier==0){$params['cart']->id_carrier=$np_id;$params['cart']->update();}
		$md_carriers = json_decode(Configuration::get('ecm_md_carriers'));
		setcookie('md_carriers', json_encode($md_carriers), time() + 600, '/', Tools::getShopDomain());
		if ($np_id == $params['cart']->id_carrier) {
			setcookie('npid', $np_id, time() + 600, '/', Tools::getShopDomain());
			$select = exec_::select($params['cart']->id_lang);
			$cart_np = exec::GetCartNP($params['cart']->id);
			$cart_np['another_recipient']= $cart_np['another_recipient'] ? 'checked="checked" ':'';
			$cartdetails = exec::cartdetails($params['cart']->id);
			//$address = exec::CheckAddress($params['cart'], $np_id);
			$this->context->smarty->assign(array(
				'carrier' => $np_id,
				'np_id' => $np_id,
				'id_carrier' => $params['cart']->id_carrier,
				'id_lang' => $params['cart']->id_lang,
				'cart_id' => $params['cart']->id,
				'customer_id' => $params['cart']->id_customer,
				'cart_np' => $cart_np,
				'cartdetails' => $cartdetails,
				'employee' => Configuration::get(self::PREFIX . 'np_Employee'),
				'fixcost' => Configuration::get(self::PREFIX . 'np_chk_fixcost'),
				'fill' => Configuration::get(self::PREFIX . 'np_fill'),
				'show' => Configuration::get(self::PREFIX . 'np_show'),
				'show_another' => Configuration::get(self::PREFIX . 'np_another_recipient'),
				'ac' => Configuration::get(self::PREFIX . 'np_ac'),
				'pnp' => (int)Db::getInstance()->GetValue("
					SELECT ms.`id_shop` FROM `"._DB_PREFIX_."module` m
					LEFT JOIN `"._DB_PREFIX_."module_shop` ms ON ms.`id_module` = m.`id_module`
					WHERE m.`name` = 'ecm_cashnovaposta' OR m.`name` = 'ecm_cashnovaposhta'"),
				'Areas' => exec::areaList2($select),
				'Citys' => exec::cityList2($cart_np['area'], $select),
				'Wares' => exec::wareList2($cart_np['city'], $select),
                'np_module_dir' => _MODULE_DIR_ . $this->name,
                'address_delivery_label' => $select['address'],
			));
			if (Configuration::get('ecm_simcheck_active')) {return $this->display(__FILE__, '/sc6/changeshipping.tpl');}
			return $this->display(__FILE__, 'changeshipping.tpl');
		}
		else{
            if(!array_key_exists($params['cart']->id_carrier, $md_carriers) and Configuration::get(self::PREFIX . 'np_fill')){
                $this->context->smarty->assign (array('id_carrier' => $params['cart']->id_carrier,));
 				return $this->display(__FILE__, 'hide.tpl');
			}
		}
	}

	public
	function hookdisplayOrderDetail($params) {
		$np_id = Configuration::get(self::PREFIX . $this->name);
		$order_details = exec::GetOrderDetails3($params['order']->id);
		if($order_details){
			$this->context->smarty->assign(array(
				'np_id' => $np_id,
				'order_details' => $order_details,
				'address' => new Address ($params['order']->id_address_delivery),
				'area' => exec::getareaname($order_details['area']),
				'city' => exec::getcityname($order_details['city']),
				'ware' => exec::getwarename($order_details['ware']),
				'np_module_dir' => _MODULE_DIR_ . $this->name
			));
			return $this->display(__FILE__, 'orderdetail_np.tpl');
		}
	}


	public
	function hookdisplayAdminOrder($params) {
		$order = new Order($params['id_order']);
		if (Configuration::get(self::PREFIX . $this->name) == $order->id_carrier){
			global $cookie;
			$cod = array(0 => $this->l('No'), 1 => $this->l('COD'), 2 => $this->l('Afterpayment on goods'));
			if(!Configuration::get(self::PREFIX . 'np_by_order_'.$cookie->id_employee)){unset ($cod[2]);}
			$select = exec_::select((string)$this->context->employee->id_lang);
			$currency_def = Currency::getDefaultCurrency();
			$currency_order = new Currency($order->id_currency);
			$order_details = exec::GetOrderDetails($order->id);
			$order_details['id_carrier'] = $order_details['id_carrier'] ? $order_details['id_carrier'] : $order->id_carrier;
			$order_details['free_limit'] = Tools::convertPriceFull((float)Configuration::get('ecm_np_FreeLimit'),$currency_def,$currency_order);
			$order_details['another_recipient']= $order_details['another_recipient'] ? 'checked="checked" ':'';
			$this->context->smarty->assign(array(
				'id_order' => $order->id,
				'np_id' => Configuration::get(self::PREFIX . $this->name),
				'order_details' => $order_details,
				'api_key' => Configuration::get('ecm_np_API_KEY_'.$cookie->id_employee),
				'weght' => Configuration::get(self::PREFIX . 'np_weght'),
				'vweght' => Configuration::get(self::PREFIX . 'np_vweght'),
				'format' => Configuration::get(self::PREFIX . 'np_format'),
				'Areas' => exec::areaList2($select,true),
				'Citys' => exec::cityList2($order_details['area'], $select),
				'Wares' => exec::wareList2($order_details['city'], $select),
				'outCitys' => exec::cityList2(exec::getout('area',-$cookie->id_employee), $select),
				'outcity' => exec::getout('city',-$cookie->id_employee),
				'outWares' => exec::wareList2(exec::getout('city',-$cookie->id_employee), $select),
				'outware' => exec::getout('ref',-$cookie->id_employee),
				'outarea' => exec::getout('area',-$cookie->id_employee),
				'CargoType' => Configuration::get(self::PREFIX . 'np_CargoType'),
				'CargoTypes' => Exec::simpleList('CargoTypes'),
				'card' => Configuration::get(self::PREFIX . 'np_card_'.$cookie->id_employee),
				'cards' => Exec::getCards(),
				'cod' => $cod,
				'data' => (date("H:i") >= Configuration::get(self::PREFIX.'np_time'))? date("d.m.Y",(time()+3600*24)) : date("d.m.Y"),
				'TrimMsg' => Configuration::get(self::PREFIX . 'np_TrimMsg'),
				'payment_form' => Configuration::get(self::PREFIX . 'np_payment_method'),
				'payment_forms' => Exec::simpleList('PaymentForms'),
                'np_module_dir' => _MODULE_DIR_ . $this->name,

			));
			return $this->display(__FILE__, 'displayAdminOrder.tpl');
		}
	}


	public
	function hookactionObjectOrderCartRuleAddAfter($object) {
		exec::CostByHook($object['object']->id_order);
		return true;
	}

	public
	function hookactionObjectOrderCartRuleDeleteAfter($object) {
		exec::CostByHook($object['object']->id_order);
		return true;
	}

	public
	function hookactionObjectOrderUpdateBefore($object) {
		if ($object['object']->current_state > 0
		and ($object['object']->total_paid < 0 or $object['object']->total_discounts < 0)
		and Configuration::get(self::PREFIX . $this->name) == $object['object']->id_carrier){
			exec::FixPaidDiscount($object['object']->id);
		}
		return true;
	}

	public
	function hookactionObjectOrderUpdateAfter($object) {
//$this->log->LogInfo("================== ". __FUNCTION__ ." ====================");
		if ($object['object']->current_state > 0
		and Configuration::get(self::PREFIX . $this->name) == $object['object']->id_carrier){
			exec::FixNoShipping($object['object']);
			exec::FixPaidToInvoice($object['object']);
			exec::CostByHook($object['object']->id);
		}
		return true;
	}


	public
	function hookactionObjectOrderDetailUpdateAfter($object) {
		exec::CostByHook($object['object']->id_order);
	}

	public
	function hookactionObjectOrderDetailDeleteAfter($object) {
		exec::CostByHook($object['object']->id_order);
	}

	public
	function hookactionObjectOrderDetailAddAfter($object) {
		//exec::CostByHook($object['object']->id_order);
	}


	public
	function hookactionValidateOrder($params) {
		if($params['order']->id_carrier == Configuration::get(self::PREFIX . $this->name)){
			exec::copyOrder((int) $params['order']->id, true);
			if(Configuration::get(self::PREFIX . 'np_SendNPmail')) {npmail::sendnpmail((int) $params['order']->id);}
			if(Configuration::get(self::PREFIX . 'np_SendNPadminmail')) {npmail::sendnpadminmail((int) $params['order']->id);}
		}
		return true;
	}

	private
	function upgradeCheck($module) {
		return false;
		global $cookie;

		$context = Context::getContext();
		if (!isset($context->employee) || !$context->employee->isLoggedBack())
			return;

		// Get module version info
		$mod_info = $this->version;
		$time = time();
		if ($this->_last_updated <= 0) {
			$time_up = Configuration::get('ECM_NP_UP');
			if (!Configuration::get('ECM_NP_UP')) {
				Configuration::updateValue('ECM_NP_UP', $time);
				$this->_last_updated = $time;
			} else {
				$this->_last_updated = $time_up;
			}
		}
		if ($time - $this->_last_updated > 86400) {
			$url = 'http://update.elcommerce.com.ua/version.xml';

			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$mod = curl_exec($ch);
			}
			$xml = simplexml_load_string($mod);
			@$current_version = $xml->novaposhta->version;

			if ((string) $current_version > $this->version)
				return true;
			else
				return false;
		}
	}

	public function correctAddress($order){
		if (Configuration::get('ecm_md_correct_address') and (_PS_VERSION_ >= '1.7')){
			$sql = "SELECT * FROM `"._DB_PREFIX_."ecm_newpost_orders` onp
			WHERE onp.`id_order` = '{$order->id}'";
			$np_address = Db::getInstance()->getRow($sql);
			$address = new Address($order->id_address_delivery);
			if ($np_address['another_recipient']){
				$address->firstname = $np_address['another_firstname'] ? $np_address['another_firstname'] : $address->firstname;
				$address->lastname =  $np_address['another_lastname']  ? $np_address['another_lastname'] : $address->lastname;
				$address->phone =     $np_address['another_phone']     ? $np_address['another_phone'] : $address->phone;
			} else {
				$address->firstname = $np_address['firstname'] ? $np_address['firstname'] : $address->firstname;
				$address->lastname =  $np_address['lastname']  ? $np_address['lastname'] : $address->lastname;
				$address->phone =     $np_address['phone']     ? $np_address['phone'] : $address->phone;
			}
			$address1 = exec::getwarename($np_address['ware']);
			$city = exec::getcityname($np_address['city']);
			$state = exec::getareaid($np_address['area']);
			$address->address1 = $address1 ? $address1 : $address->address1;
			$address->city = $city ? $city : $address->city;
			$address->state = $state ? $state : $address->state;
			$address->update();
		}
	}

    public function DeleteTabs() 
    {    
        $id = Tab::getIdFromClassName('AdminParentShipping');
		foreach (Tab::getTabs(Configuration::get('PS_LANG_DEFAULT'), $id) as $tab) {
			if ($tab['module'] == 'ecm_novaposhta') {
				$tab = new Tab($tab['id_tab']);
				$tab->delete();
			}
		}
		
		$id = Tab::getIdFromClassName('AdminNPMain');
		foreach (Tab::getTabs(Configuration::get('PS_LANG_DEFAULT'), $id) as $tab) {
			if ($tab['module'] == 'ecm_novaposhta') {
				$tab = new Tab($tab['id_tab']);
				$tab->delete();
			}
		}
		$tab = new Tab($id);
		$tab->delete();
    }

    
	public function CreateTabs() 
    {    
        $this->DeleteTabs();
		
		$parent_tab = new Tab();
		$parent_tab->class_name = 'AdminNPMain';
		$parent_tab->id_parent = ( _PS_VERSION_ >= '1.7')?(int)Tab::getIdFromClassName('IMPROVE'):0;
		$parent_tab->module = $this->name;
		$parent_tab->icon = 'airplanemode_active';
		if( _PS_VERSION_ < '1.7') {
			$this->hookactionAdminControllerSetMedia();
			$parent_tab->icon = 'icon-plane';
		}
		$parent_tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('NP Delivery');
		$parent_tab->add();
		
		$id = $parent_tab->id;



		// settings
		$tab = new Tab();
		$tab->class_name = 'AdminNP';
		$tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('NP Settings');
		$tab->id_parent = $id;
		$tab->module = $this->name;
		$tab->add();

		// log
		$tab = new Tab();
		$tab->class_name = 'AdminNPLog';
		$tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('NP Log');
		$tab->id_parent = $id;
		$tab->module = $this->name;
		$tab->add();

		// statuses
		$tab = new Tab();
		$tab->class_name = 'AdminNPStatuses';
		$tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('NP Statuses');
		$tab->id_parent = $id;
		$tab->module = $this->name;
		$tab->add();
	}	

	public function checkCarrier() {
        $carrier = new Carrier(Configuration::get(self::PREFIX . $this->name));
        return $carrier->active;
	}
	


}
