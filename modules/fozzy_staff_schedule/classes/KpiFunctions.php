<?php
class KpiFunctions {
	/**
     * Получение количества человек на смене Сборщиков, Упаковщиков, Менеджеров.
     */
	public static function getCountPeopleShiftData($id_shop = 0, $table = '', $fillial, $week_number = '', $post_name_key = '', $day = '', $shop_name = '') {
		$sql_question = "SELECT COUNT(`id_person`) FROM `" . _DB_PREFIX_ . $table ."`";
        $sql_question .= " WHERE `".$week_number."` = " . date('W'). " AND `".$day."` = 1";
        if($id_shop != 0){
            $sql_question .= " AND `".$shop_name."` = ". $id_shop;
        }
        if (isset($_POST[$post_name_key]) && $_POST[$post_name_key] == 1) {
            $sql_question .= " AND `".$fillial."` = 1";
        }
        if (isset($_POST[$post_name_key]) && $_POST[$post_name_key] == 25) {
            $sql_question .= " AND `".$fillial."` = 25";
        }
        if (isset($_POST[$post_name_key]) && $_POST[$post_name_key] == 30) {
            $sql_question .= " AND `".$fillial."` = 30";
        }
        $count_picker = Db::getInstance()->getValue($sql_question);
        return $count_picker;
    }

    /**
     * Получение количества человек на смене Водителей.
     */
	public static function getCountDriverShiftData($id_shop = 0, $day1 = '', $day2 = '', $day3 = '') {
		$sql_question = "SELECT COUNT(`id_person`) FROM `" . _DB_PREFIX_ . "staff_schedule_driver`";
        $sql_question .= " WHERE `week_number_driver` = " . date('W'). " AND (`".$day1."` = 1 OR `".$day2."` = 1 OR `".$day3."` = 1)";
        if($id_shop != 0){
            $sql_question .= " AND `shop_name_driver` = ". $id_shop;
        }
        if (isset($_POST['table_driverFilter_fillial_name_driver']) && !empty($_POST['table_driverFilter_fillial_name_driver'])) {
            $sql_question .= " AND `fillial_name_driver` = ". $_POST['table_driverFilter_fillial_name_driver'];
        }
        $count_driver = Db::getInstance()->getValue($sql_question);
        return $count_driver;
    }

    /**
     * Получение имен "Сборщиков" которые на смене.
     */
    public static function getPickerNameShiftData($id_shop = 0, $day = '') {
    	$sql_question = "SELECT `ps_fozzy_logistic_sborshik`.fio FROM `" . _DB_PREFIX_ . "staff_schedule_picker`";
        $sql_question .= " LEFT JOIN `ps_fozzy_logistic_sborshik` ON `ps_fozzy_logistic_sborshik`.`id_sborshik` = `ps_staff_schedule_picker`.`picker_name_person`";
        $sql_question .= " WHERE `week_number_picker` = " . date('W'). " AND `".$day."` = 1";
        $sql_question .= " AND `shop_name_picker` = ". $id_shop;
        if(isset($_POST['table_pickerFilter_fillial_name_picker']) && !empty($_POST['table_pickerFilter_fillial_name_picker'])){
            $sql_question .= " AND `fillial_name_picker` = ". $_POST['table_pickerFilter_fillial_name_picker'];
        }
        $data = Db::getInstance()->executeS($sql_question);
        return $data;
    }

    /**
     * Получение имен "Упаковщиков" которые на смене.
     */
    public static function getPackerNameShiftData($id_shop = 0, $day = '') {
    	$sql_question = "SELECT `ps_fozzy_logistic_packer`.fio FROM `" . _DB_PREFIX_ . "staff_schedule_packer`";
        $sql_question .= " LEFT JOIN `ps_fozzy_logistic_packer` ON `ps_fozzy_logistic_packer`.`id_packer` = `ps_staff_schedule_packer`.`packer_name_person`";
        $sql_question .= " WHERE `week_number_packer` = " . date('W'). " AND `".$day."` = 1";
        $sql_question .= " AND `shop_name_packer` = ". $id_shop;
        if(isset($_POST['table_packerFilter_fillial_name_packer']) && !empty($_POST['table_packerFilter_fillial_name_packer'])){
            $sql_question .= " AND `fillial_name_packer` = ". $_POST['table_packerFilter_fillial_name_packer'];
        }
        $data = Db::getInstance()->executeS($sql_question);
        return $data;
    }

    /**
     * Получение имен "Водителей" которые на смене.
     */
    public static function getDriverNameShiftData($id_shop = 0, $day = '', $day1 = '', $day2 = '', $day3 = '') {
        $sql_question = "SELECT `ps_fozzy_logistic_vodila`.fio FROM `" . _DB_PREFIX_ . "staff_schedule_driver`";
        $sql_question .= " LEFT JOIN `ps_fozzy_logistic_vodila` ON `ps_fozzy_logistic_vodila`.`id_vodila` = `ps_staff_schedule_driver`.`driver_name_person`";
        if($id_shop == 1 && $_POST['table_driverFilter_fillial_name_driver'] == NUll) {
            $sql_question .= " WHERE `week_number_driver` = " . date('W'). " AND (`".$day1."` = 1 OR `".$day2."` = 1 OR `".$day3."` = 1)";
        } else {
            $sql_question .= " WHERE `week_number_driver` = " . date('W'). " AND `".$day."` = 1";
        }
        $sql_question .= " AND `shop_name_driver` = " . $id_shop;
        if(isset($_POST['table_driverFilter_fillial_name_driver']) && !empty($_POST['table_driverFilter_fillial_name_driver'])){
            $sql_question .= " AND `fillial_name_driver` = ". $_POST['table_driverFilter_fillial_name_driver'];
        }
        $data = Db::getInstance()->executeS($sql_question);
        return $data;
    }

    /**
     * Получение имен "Менеджеров" которые на смене.
     */
    public static function getManagerNameShiftData($id_shop = 0, $day = '') {
        $sql_question = "SELECT `ps_fozzy_logistic_manager`.fio FROM `" . _DB_PREFIX_ . "staff_schedule_manager`";
        $sql_question .= " LEFT JOIN `ps_fozzy_logistic_manager` ON `ps_fozzy_logistic_manager`.`id_manager` = `ps_staff_schedule_manager`.`manager_name_person`";
        $sql_question .= " WHERE `week_number_manager` = " . date('W'). " AND `".$day."` = 1";
        $sql_question .= " AND `shop_name_manager` = ". $id_shop;
        if(isset($_POST['table_managerFilter_fillial_name_manager']) && !empty($_POST['table_managerFilter_fillial_name_manager'])){
            $sql_question .= " AND `fillial_name_manager` = ". $_POST['table_managerFilter_fillial_name_manager'];
        }
        $data = Db::getInstance()->executeS($sql_question);
        return $data;
    }

    /**
     * Получение заголовков в Kpi графике.
     */
    public static function getHeadingKpiDay($id_shop = 0, $post_name_key = '') {
        if($id_shop == 1) {
            if(isset($_POST[$post_name_key]) && $_POST[$post_name_key] == 30){
                $heading_kpi_day = 'Пролиски: ';
            } elseif (isset($_POST[$post_name_key]) && $_POST[$post_name_key] == 25) {
                $heading_kpi_day = 'Петровка: ';
            } elseif (isset($_POST[$post_name_key]) && $_POST[$post_name_key] == 1) {
                $heading_kpi_day = 'Заболотного: ';
            } else {
                $heading_kpi_day = 'Киев: ';
            }
        } elseif ($id_shop == 2) {
            $heading_kpi_day = 'Одесса: ';
        } elseif ($id_shop == 3) {
            $heading_kpi_day = 'Днепр: ';
        } elseif ($id_shop == 4) {
            $heading_kpi_day = 'Харьков: ';
        } elseif ($id_shop == 8) {
            $heading_kpi_day = 'Ровно: ';
        } elseif ($id_shop == 9) {
            $heading_kpi_day = 'Кременчуг: ';
        } else {
            $heading_kpi_day = '';
        }

        return $heading_kpi_day;
    }
}