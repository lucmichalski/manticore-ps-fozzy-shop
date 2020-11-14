<?php
include(dirname(__FILE__) . '/../../../config/config.inc.php');
$rawData = file_get_contents('php://input');
$data    = json_decode($rawData);

if ($data) {
    $name = 'ecm_smssender_' . $data->val_name;
    $flag = $data->val_flag;
    if(is_array($flag))
    $flag = implode(',',$flag);
   	if(!empty($name) && !empty($flag)){
        Db::getInstance()->execute(
            "UPDATE `ps_configuration` SET `id_shop_group` = NULL ,`id_shop` = NULL , `value` = '$flag'  WHERE `name` = '$name'");
        $value = Db::getInstance()->getValue("
                SELECT `value` FROM `" . _DB_PREFIX_ . "configuration` WHERE `name` = '$name'");
        if($value == $flag){
                echo 'success';
            } else {
                echo 'error';
            }
	} else {
        echo 'error';
    }
}
