<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/exec_.php');
require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/exec.php');
require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/api2.php');



if ((bool)Tools::GetValue('secure_key')){
	$secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
	if (!empty($secureKey) && $secureKey === Tools::GetValue('secure_key')){
		$status_map = json_decode(Configuration::get('ecm_np_status_map'), true);
		$final_status = implode(',',json_decode(Configuration::get('ecm_np_final_status'), true));
		
		$carriers = json_decode(Configuration::get('ecm_np_carriers'), true);
		$carriers_ids = array();
		foreach ($carriers as $id_reference){
			$carrier = Carrier::getCarrierByReference($id_reference);
			$carriers_ids[] = $carrier->id;
		}
		$carriers_ids = implode(',', $carriers_ids);
        $sql = "SELECT o.`id_order`, oc.`id_carrier`, oc.`tracking_number` shipping_number, `phone_mobile`, `phone`, o.`current_state`, o.`date_upd` FROM `" . _DB_PREFIX_ . "orders` o 
			LEFT JOIN `" . _DB_PREFIX_ . "address` a ON a.`id_address` = o.`id_address_delivery`
			LEFT JOIN `" . _DB_PREFIX_ . "order_carrier` oc ON oc.`id_order` = o.`id_order`
			WHERE oc.`id_carrier` IN ({$carriers_ids}) AND o.`current_state` NOT IN ({$final_status}) AND oc.`tracking_number` != '' ";		
		
		$orders = Db::getInstance()->ExecuteS($sql);
		$documents = array();
		$day = Configuration::get('ecm_np_warning_day');
        foreach($orders as $order){
			$documents[trim($order['shipping_number'])] = array(
				'DocumentNumber' => trim($order['shipping_number']),
				'Phone' => $order['phone_mobile']?$order['phone_mobile']:$order['phone'],
				'id_order' => $order['id_order'],
				'current_state' => $order['current_state'],
				'date' => date('Y-m-d', strtotime($order['date_upd']. " + $day days")),
			);
		}
        
		$docs = array_chunk($documents, 100); // 100 -- ограничение НП
		foreach ($docs as $doc) {
			$statuses = np::getStatusDocuments($doc);
            foreach ($statuses as $status){
				$old_status = $documents[$status->Number]['current_state'];
				$new_status = @$status_map[$status->StatusCode];
                if($new_status){
                    if($old_status == Configuration::get('ecm_np_ware_status')){ 	// проверить задержку
                        if (!$status->RecipientDateTime){							// не забрал (пусто)
                            if(time() > strtotime($documents[$status->Number]['date'])){ 
                                $history = new OrderHistory();
                                $history->id_order = $documents[$status->Number]['id_order'];
                                $history->id_employee = abs(Configuration::get('ecm_np_Employee'));
                                $history->changeIdOrderState(Configuration::get('ecm_np_warning_status'), $documents[$status->Number]['id_order']);
                                $history->addWithemail();
                            }
                            continue;
                        }
                    }
                    
                    if($old_status == Configuration::get('ecm_np_warning_status')     // просрочка
						and $new_status == Configuration::get('ecm_np_ware_status')){ // и еще у НП Прибув на відділення
                        continue;
                    }
				}
				
				$exist_status = Db::getInstance()->getValue("SELECT `id_order_state`FROM `". _DB_PREFIX_ . "order_history` WHERE `id_order` = '{$documents[$status->Number]['id_order']}' ORDER BY `date_add` DESC, `id_order_history` DESC'");
				
				if($new_status != $old_status && $new_status != $exist_status){
					
					$history = new OrderHistory();
					$history->id_order = $documents[$status->Number]['id_order'];
					$history->id_order_state = $old_status;
					$history->id_employee = abs(Configuration::get('ecm_np_Employee'));
					$history->changeIdOrderState($new_status, $documents[$status->Number]['id_order']);
					try {
						$history->addWithemail();
					} catch (Exception $e) {
						//p(' Заказ :'.$documents[$status->Number]['id_order'].'-'.$documents[$status->Number]['DocumentNumber'].' Error: '.$e->getMessage());
					}
				}
			}
		}

		echo 'OK';
	}else{
		echo 'wrong secure_key <br>';
	}
}
else {
	echo 'no secure_key';
}
