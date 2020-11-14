<?php
$sql = array();
$sql[] ='DROP TABLE `'._DB_PREFIX_.'ecm_smssender`';
$sql[] ='DROP TABLE `'._DB_PREFIX_.'ecm_smsquestion`';
$sql[] ='DROP TABLE `'._DB_PREFIX_.'ecm_smsanswer`';
$sql[] ='DROP TABLE `'._DB_PREFIX_.'ecm_smslistanswer`';
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
