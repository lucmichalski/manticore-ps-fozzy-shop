<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

include_once(_PS_MODULE_DIR_.'nove_dateofdelivery/nove_dateofdelivery.php');
$d = new Nove_dateofdelivery();
$id_shop = Tools::GetValue('n_id_shop');
$id_lang = Tools::GetValue('n_id_lang');
$windows = $d->hookDisplayHome( array('id_lang'=>$id_lang, 'id_shop'=>$id_shop) );
echo $windows;
?>