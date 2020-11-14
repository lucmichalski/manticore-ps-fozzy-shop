<?php

include(dirname(__FILE__) . '/../../config/config.inc.php');
$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
$employee = new Employee((int)$cookie->id_employee);

if (!(Validate::isLoadedObject($employee) && $employee->checkPassword((int)$cookie->id_employee, $cookie->passwd) && (!isset($cookie->remote_addr) || $cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP'))))
        die('User is not logged in');
$mode = Tools::getValue('mode');

switch($mode){
	case "changecarrier": changecarrier(); break;
	case "changecarr"   : changecarr(Tools::getValue('carrier'),Tools::getValue('id_order'));	break;
}
function changecarrier(){
	global $smarty, $cookie;
	$id_lang = $cookie->id_lang;
	$id_order = Tools::getValue('id_order');
	$id_carrier = Db::getInstance()->GetValue ("SELECT `id_carrier` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = '$id_order'");
	$carriers_list = Carrier::getCarriers($id_lang,true, false, false, null, ALL_CARRIERS);
	$i = 0;
	foreach ($carriers_list as $carrier){
		$carriers[$i]['id_carrier'] = $carrier['id_carrier'];
		$carriers[$i]['name'] = $carrier['name'];
		($carrier['id_carrier'] == $id_carrier)? $carriers[$i]['selected'] = 'checked="checked"': $carriers[$i]['selected'] ='';
		$carriers[$i]['delay'] = $carrier['delay'];
		$i++;
	}
	$smarty->assign(
		array(
			'img_dir' => __PS_BASE_URI__.'img/',
			'carriers_list' => $carriers,
			'id_order' =>$id_order,
		)
	);
	$smarty->display(dirname(__FILE__).'/views/changecarrier.tpl');
}

function changecarr($id_carrier, $id_order) {
	$order = new Order($id_order);
	$cart = new Cart($order->id_cart);
	$shipping = $cart->getPackageShippingCost($id_carrier);
	$cart->id_carrier = $id_carrier;
	$cart->id_lang = $order->id_lang;
	$cart->id_currency = $order->id_currency;
	$cart->update();
	$order->id_carrier = $id_carrier;
	$order->total_shipping = $shipping;
	$order->total_shipping_tax_incl = $shipping;
	$order->total_shipping_tax_excl = $shipping;
	$order->total_paid = $order->total_products_wt + $shipping;
	$order->total_paid_tax_incl = $order->total_products_wt + $shipping;
	$order->total_paid_tax_excl = $order->total_products_wt + $shipping;
	$order->update();
	
	$sql = "UPDATE `"._DB_PREFIX_."order_invoice` SET
		`total_paid_tax_incl` = '".$order->total_paid."',
		`total_paid_tax_excl` = '".$order->total_paid."',
		`total_shipping_tax_incl` = '$shipping',
		`total_shipping_tax_excl` = '$shipping'
	   WHERE `id_order` = '$id_order'";
	Db::getInstance()->Execute($sql);
	Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."orders` SET `id_carrier` = '$id_carrier' WHERE `id_order` = '$id_order'");
	Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."order_carrier` SET `id_carrier` = '$id_carrier', `shipping_cost_tax_excl` = '$shipping', `shipping_cost_tax_incl` = '$shipping'  WHERE `id_order` = '$id_order'");
}


