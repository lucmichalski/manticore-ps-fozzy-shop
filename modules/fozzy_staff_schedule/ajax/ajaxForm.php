<?php
include(dirname(__FILE__) . '/../../../config/config.inc.php');

if (!empty($_POST['person_shop'])) {
    if ($_POST['type_persone'] == 1) {
        $sql = "SELECT `ps_fozzy_logistic_sborshik`.*, `ps_fozzy_logistic_sborshik`.`id_sborshik` as `id_person` FROM `ps_fozzy_logistic_sborshik` WHERE `id_fillial` = " . $_POST['person_shop'] . " AND `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC";
        $persone = Db::getInstance()->executeS($sql);
        $type_persona = 'picker_personal';
    } elseif ($_POST['type_persone'] == 2) {
        $sql = "SELECT `ps_fozzy_logistic_packer`.*, `ps_fozzy_logistic_packer`.`id_packer` as `id_person` FROM `ps_fozzy_logistic_packer` WHERE `id_fillial` = " . $_POST['person_shop'] . " AND `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC";
        $persone = Db::getInstance()->executeS($sql);
        $type_persona = 'packer_personal';
    } elseif ($_POST['type_persone'] == 3) {
        $sql = "SELECT `ps_fozzy_logistic_vodila`.*, `ps_fozzy_logistic_vodila`.`id_vodila` as `id_person` FROM `ps_fozzy_logistic_vodila` WHERE `id_fillial` = " . $_POST['person_shop'] . " AND `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC";
        $persone = Db::getInstance()->executeS($sql);
        $type_persona = 'driver_personal';
    } else {
        $sql = "SELECT `ps_fozzy_logistic_manager`.*, `ps_fozzy_logistic_manager`.`id_manager` as `id_person` FROM `ps_fozzy_logistic_manager` WHERE `id_fillial` = " . $_POST['person_shop'] . " AND `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC";
        $persone = Db::getInstance()->executeS($sql);
        $type_persona = 'manager_personal';
    }

    echo json_encode([
        'success' => true,
        'id' => $type_persona,
        'data' => $persone,
    ]);
}
