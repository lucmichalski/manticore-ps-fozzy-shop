<?php
include(dirname(__FILE__) . '/../../../config/config.inc.php');
ini_set('display_errors', '1');
error_reporting(E_ALL);
$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
$employee = new Employee((int)$cookie->id_employee);
if (!(Validate::isLoadedObject($employee) && $employee->checkPassword((int)$cookie->id_employee, $cookie->passwd) && (!isset($cookie->remote_addr) || $cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP'))))
die('User is not logged in');
include(dirname(__FILE__) . '/../classes/turbosms.php');
include(dirname(__FILE__) . '/../classes/message.php');
$mode = Tools::getValue('mode');
switch($mode){
	case "showform": showform(); break;
	case "sendsms"   : sendsms(Tools::getValue('phone'),Tools::getValue('id_order'),Tools::getValue('header'));	break;
}
function showform(){
	global $smarty, $cookie;
	$id_lang = $cookie->id_lang;
	$id_order = Tools::getValue('id_order');
	$header = Tools::getValue('header');
	$phones = Db::getInstance()->ExecuteS("
                    SELECT
                    a.`phone`,
                    a.`phone_mobile`
                    FROM `" . _DB_PREFIX_ . "orders` o
                    LEFT JOIN `" . _DB_PREFIX_ . "address` a
                    ON o.`id_address_delivery` = a.`id_address`
                    WHERE o.`id_order`=" .  $id_order);
    if (Module::isInstalled('ecm_novaposhta')){
		$another_phone = Db::getInstance()->ExecuteS("
                    SELECT
                   `another_phone` as phone_mobile
                    FROM `" . _DB_PREFIX_ . "ecm_newpost_orders`
                    WHERE `id_order`=" .  (int)$id_order);

     if(isset($another_phone[0]['phone_mobile']) && $another_phone[0]['phone_mobile'])
     $phones = $another_phone;

		}
	$smarty->assign(
		array(
			'phones' => $phones,
			'id_order' =>$id_order,
			'header' =>$header,
		)
	);
	$smarty->display(_PS_MODULE_DIR_.'ecm_smssender/views/templates/admin/showPhone.tpl');
}

function sendsms($phone, $id_order,$header) {
	$login = Configuration::get('ECM_SMSSENDER_ACCOUNT');
	$pwd = Configuration::get('ECM_SMSSENDER_ACCOUNT_PASSWORD');
	$sender = Configuration::get('ECM_SMSSENDER_ACCOUNT_ALFA');
	$id_lang = Db::getInstance()->getValue("
            SELECT `id_lang` FROM `" . _DB_PREFIX_ . "orders` WHERE `id_order`=".$id_order);
	$smssender = new Client($login,$pwd,$sender);
	$message = ($header==1)?MessageSMS::getTTNData($id_order,$id_lang):MessageSMS::getRecvData($id_order,$id_lang);
	echo $smssender->send($phone,$message);
}

