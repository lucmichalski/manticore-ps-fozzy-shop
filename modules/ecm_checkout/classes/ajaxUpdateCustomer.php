<?php
include(dirname(__FILE__) . '/../../../config/config.inc.php');
$rawData = file_get_contents('php://input');
$data    = json_decode($rawData);
if ($data) {
    if ($data->val_name) {
        if ($data->val_name == 'phone') {
            if (Validate::isPhoneNumber($data->val_flag)) {
                $id_customer = Db::getInstance()->getValue("SELECT `id_customer` FROM `"._DB_PREFIX_."customer` WHERE `phone` = '" . trim($data->val_flag)."'");
                if ($id_customer) {
                    die('PHONE_ERROR');
                }
            }else
            die('PHONE_ERROR_FORMAT');
        }
        Db::getInstance()->update('customer', array($data->val_name => pSQL(trim($data->val_flag))),'id_customer =' .(int)$data->val_id);
        die('OK');
    }
}