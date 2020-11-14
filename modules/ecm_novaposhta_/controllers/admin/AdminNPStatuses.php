<?php
include_once(dirname(__FILE__).'/../../ecm_novaposhta.php');

class AdminNPStatusesController extends AdminController{
	public function __construct(){
		$this->bootstrap = true;
        $this->np_statuses = array(
            1 => 'Нова пошта очікує надходження від відправника',
            2 => 'Видалено',
            3 => 'Номер не знайдено',
            4 => 'Відправлення у місті ХХXХ. (Статус для межобластных отправлений)',
            41 => 'Відправлення у місті ХХXХ. (Статус для услуг локал стандарт и локал экспресс - доставка в пределах города)',
            5 => 'Відправлення прямує до міста YYYY.',
            6 => 'Відправлення у місті YYYY, орієнтовна доставка до ВІДДІЛЕННЯ-XXX dd-mm. Очікуйте додаткове повідомлення про прибуття.',
            7 => 'Прибув на відділення',
            8 => 'Прибув на відділення',
            9 => 'Відправлення отримано',
            10 => 'Відправлення отримано %DateReceived%. Протягом доби ви одержите SMS-повідомлення про надходження грошового переказу та зможете отримати його в касі відділення «Нова пошта».',
            11 => 'Відправлення отримано %DateReceived%. Грошовий переказ видано одержувачу.',
            14 => 'Відправлення передано до огляду отримувачу',
            101 => 'На шляху до одержувача',
            102 => 'Відмова одержувача',
            103 => 'Відмова одержувача',
            108 => 'Відмова одержувача',
            104 => 'Змінено адресу',
            105 => 'Припинено зберігання',
            106 => 'Одержано і є ТТН грошовий переказ',
        );
		parent::__construct();
	}

	public function renderList(){
		//$this->module = new ecm_novaposhta();
        $tpl = $this->context->smarty->createTemplate(dirname(__FILE__).'/../../views/templates/admin/statuses.tpl');
        $tpl->assign(array(
            'np_statuses' => $this->np_statuses,
            'status_map' => json_decode(Configuration::get('ecm_np_status_map'), true),
            'carriers_list' => $this->getCarriers(),
            'carriers' => json_decode(Configuration::get('ecm_np_carriers'), true),
            'status_list' => $this->getStatuses(),
            'final_status' => json_decode(Configuration::get('ecm_np_final_status'), true),
            'ware_status' => Configuration::get('ecm_np_ware_status'),
            'warning_status' => Configuration::get('ecm_np_warning_status'),
            'warning_day' => Configuration::get('ecm_np_warning_day'),
            'cron_url' => $this->getCronUrl(),
        ));
        return $tpl->fetch();
	}
    
    public function initContent()
    {
        parent::initContent();
		//$this->setTemplate('statuses.tpl');
    }

    public function ajaxProcessRefresh()
    {
        if(!Tools::GetValue('command')){ die(Tools::jsonEncode(array(
            'status' => 'failure',
            'details' => 'no command',
        )));}
		$command = Tools::GetValue('command');
        switch ($command) {
	        case 'map_change':
	            $status_map = json_decode(Configuration::get('ecm_np_status_map'), true);
				$status_map[Tools::GetValue('np_status')] = Tools::GetValue('map_status');
				Configuration::updateValue('ecm_np_status_map', json_encode($status_map));
                break;
	        case 'carriers':
	        case 'final_status':
				$values = Tools::GetValue('values');
				Configuration::updateValue('ecm_np_'.$command, json_encode($values));
                break;
	        case 'ware_status':
	        case 'warning_status':
	        case 'warning_day':
				$values = Tools::GetValue('values');
				Configuration::updateValue('ecm_np_'.$command, $values);
                break;
        }
        
        die(Tools::jsonEncode(array(
            'status' => 'OK',
            'details' => $final_status,
         )));
    }
    protected function getStatuses()
    {
        $statuses = array();
        $list = OrderState::getOrderStates($this->context->language->id);
        $statuses[0] = $this->l('Not assign');
        foreach ($list as $status) {
            if (!$status['deleted'] or !$status['hidden']) {
                $statuses[$status['id_order_state']] = $status['id_order_state'].'. '.$status['name'];
            }
        }
        return $statuses;
    }
    protected function getCarriers()
    {
        $carriers = array();
        $list = Carrier::getCarriers($this->context->language->id,false, false, false, null, Carrier::ALL_CARRIERS);
        foreach ($list as $carrier) {
            if (!$carrier['deleted'] or $carrier['active']) {
                $carriers[$carrier['id_reference']] = $carrier['name'];
            }
        }
        return $carriers;
    }
    protected function getCronUrl()
    {
        $secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
		$domain = Tools::getHttpHost(true);
		$cron_url = $domain.__PS_BASE_URI__.'modules/ecm_novaposhta/cronstatuses.php?secure_key='.$secureKey;
		return $cron_url;
    }
}
