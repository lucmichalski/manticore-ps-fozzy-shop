<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/exec_.php');
require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/exec.php');
require_once(_PS_MODULE_DIR_ . 'ecm_novaposhta/classes/api2.php');



if ((bool)Tools::GetValue('secure_key')){
	$secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
	if (!empty($secureKey) && $secureKey === Tools::GetValue('secure_key')){
		$arr = $arr_c = $arr_a = array();
		$arr = np::warehouse();
		$arr_c = np::city();
		$arr_a = np::area();
		if(count($arr) and count($arr_c) and count($arr_a) and Exec::warehouse($arr, $arr_c, $arr_a)) {	echo 'OK';}
		else{echo 'Failure!';}
	}else{
		echo 'wrong secure_key <br>';
		//echo $secureKey. '<br>';
		echo Tools::GetValue('secure_key');
	}
}
else{echo 'no secure_key';}
