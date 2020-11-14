<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

include(dirname(__FILE__).'/priceexcel.php');
 
$id_order = (int)Tools::GetValue('id_order');

if (!$id_order) die();

PriceExcel::ExportOrder($id_order);
	
//header('Location: '.$_SERVER['HTTP_REFERER']);
