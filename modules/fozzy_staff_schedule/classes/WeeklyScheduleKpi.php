<?php

require_once _PS_MODULE_DIR_ . 'fozzy_staff_schedule/fozzy_staff_schedule.php';
require_once _PS_MODULE_DIR_ . 'fozzy_staff_schedule/classes/KpiFunctions.php';

class WeeklyScheduleKpi {
	static function renderKpis() {
        $module = new Fozzy_staff_schedule();
        $id_shop = (int)Shop::getContextShopID();
        $kpis = array();

        /**
         * Количество сборщиков на смене на каждый день.
         */
        /*Сборщиков на смене в понедельник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pn';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $module->l('Сборщиков на смене Понедельник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_picker = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_picker', 'fillial_name_picker','week_number_picker', 'table_pickerFilter_fillial_name_picker', 'day_pn', 'shop_name_picker');
        $helper->value = $count_picker;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_pickerFilter_fillial_name_picker'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPickerNameShiftData($id_shop, 'day_pn');
            $picker_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $picker_name_person;
        } 

        if ($count_picker == 0) {
            $helper->tooltip = $module->l('Сборщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Сборщиков на смене во вторник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vt';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $module->l('Сборщиков на смене Вторник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_picker = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_picker', 'fillial_name_picker','week_number_picker', 'table_pickerFilter_fillial_name_picker', 'day_vt', 'shop_name_picker');
        $helper->value = $count_picker;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_pickerFilter_fillial_name_picker'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPickerNameShiftData($id_shop, 'day_vt');
            $picker_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $picker_name_person;
        } 

        if($count_picker == 0) {
            $helper->tooltip = $module->l('Сборщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Сборщиков на смене в среду.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sr';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $module->l('Сборщиков на смене Среда');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_picker = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_picker', 'fillial_name_picker','week_number_picker', 'table_pickerFilter_fillial_name_picker', 'day_sr', 'shop_name_picker');
        $helper->value = $count_picker;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_pickerFilter_fillial_name_picker'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPickerNameShiftData($id_shop, 'day_sr');
            $picker_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $picker_name_person;
        } 

        if ($count_picker == 0) {
            $helper->tooltip = $module->l('Сборщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Сборщиков на смене в четверг.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-cht';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $module->l('Сборщиков на смене Четверг');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_picker = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_picker', 'fillial_name_picker','week_number_picker', 'table_pickerFilter_fillial_name_picker', 'day_cht', 'shop_name_picker');
        $helper->value = $count_picker;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_pickerFilter_fillial_name_picker'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPickerNameShiftData($id_shop, 'day_cht');
            $picker_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $picker_name_person;
        } 

        if ($count_picker == 0) {
            $helper->tooltip = $module->l('Сборщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Сборщиков на смене в пятницу.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pt';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $module->l('Сборщиков на смене Пятница');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_picker = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_picker', 'fillial_name_picker','week_number_picker', 'table_pickerFilter_fillial_name_picker', 'day_pt', 'shop_name_picker');
        $helper->value = $count_picker;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_pickerFilter_fillial_name_picker'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPickerNameShiftData($id_shop, 'day_pt');
            $picker_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $picker_name_person;
        } 

        if ($count_picker == 0) {
            $helper->tooltip = $module->l('Сборщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Сборщиков на смене в субботу.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sb';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $module->l('Сборщиков на смене Суббота');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_picker = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_picker', 'fillial_name_picker','week_number_picker', 'table_pickerFilter_fillial_name_picker', 'day_sb', 'shop_name_picker');
        $helper->value = $count_picker;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_pickerFilter_fillial_name_picker'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPickerNameShiftData($id_shop, 'day_sb');
            $picker_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $picker_name_person;
        } 

        if ($count_picker == 0) {
            $helper->tooltip = $module->l('Сборщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Сборщиков на смене в воскресенье.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vs';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $module->l('Сборщиков на смене Воскресенье');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_picker = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_picker', 'fillial_name_picker','week_number_picker', 'table_pickerFilter_fillial_name_picker', 'day_vs', 'shop_name_picker');
        $helper->value = $count_picker;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_pickerFilter_fillial_name_picker'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPickerNameShiftData($id_shop, 'day_vs');
            $picker_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $picker_name_person;
        } 

        if ($count_picker == 0) {
            $helper->tooltip = $module->l('Сборщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /**
         * Количество упаковщиков на смене на каждый день.
         */
        /*Упаковщиков на смене в понедельник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pn';
        $helper->icon = 'icon-user';
        $helper->color = 'color3';
        $helper->title = $module->l('Упаковщиков на смене Понедельник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_packer = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_packer', 'fillial_name_packer','week_number_packer', 'table_packerFilter_fillial_name_packer','day_pn', 'shop_name_packer');
        $helper->value = $count_packer;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_packerFilter_fillial_name_packer'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPackerNameShiftData($id_shop, 'day_pn');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        } 

        if ($count_packer == 0) {
            $helper->tooltip = $module->l('Упаковщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Упаковщиков на смене во вторник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vt';
        $helper->icon = 'icon-user';
        $helper->color = 'color3';
        $helper->title = $module->l('Упаковщиков на смене Вторник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_packer = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_packer', 'fillial_name_packer','week_number_packer', 'table_packerFilter_fillial_name_packer','day_vt', 'shop_name_packer');
        $helper->value = $count_packer;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_packerFilter_fillial_name_packer'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPackerNameShiftData($id_shop, 'day_vt');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        } 

        if ($count_packer == 0) {
            $helper->tooltip = $module->l('Упаковщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Упаковщиков на смене в среду.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sr';
        $helper->icon = 'icon-user';
        $helper->color = 'color3';
        $helper->title = $module->l('Упаковщиков на смене Среда');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_packer = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_packer', 'fillial_name_packer','week_number_packer', 'table_packerFilter_fillial_name_packer','day_sr', 'shop_name_packer');
        $helper->value = $count_packer;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_packerFilter_fillial_name_packer'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPackerNameShiftData($id_shop, 'day_sr');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        } 

        if ($count_packer == 0) {
            $helper->tooltip = $module->l('Упаковщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Упаковщиков на смене в четверг.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-cht';
        $helper->icon = 'icon-user';
        $helper->color = 'color3';
        $helper->title = $module->l('Упаковщиков на смене Четверг');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_packer = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_packer', 'fillial_name_packer','week_number_packer', 'table_packerFilter_fillial_name_packer','day_cht', 'shop_name_packer');
        $helper->value = $count_packer;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_packerFilter_fillial_name_packer'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPackerNameShiftData($id_shop, 'day_cht');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        } 

        if ($count_packer == 0) {
            $helper->tooltip = $module->l('Упаковщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Упаковщиков на смене в пятницу.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pt';
        $helper->icon = 'icon-user';
        $helper->color = 'color3';
        $helper->title = $module->l('Упаковщиков на смене Пятница');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_packer = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_packer', 'fillial_name_packer','week_number_packer', 'table_packerFilter_fillial_name_packer','day_pt', 'shop_name_packer');
        $helper->value = $count_packer;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_packerFilter_fillial_name_packer'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPackerNameShiftData($id_shop, 'day_pt');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        } 

        if ($count_packer == 0) {
            $helper->tooltip = $module->l('Упаковщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Упаковщиков на смене в суботу.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sb';
        $helper->icon = 'icon-user';
        $helper->color = 'color3';
        $helper->title = $module->l('Упаковщиков на смене Суббота');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_packer = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_packer', 'fillial_name_packer','week_number_packer', 'table_packerFilter_fillial_name_packer','day_sb', 'shop_name_packer');
        $helper->value = $count_packer;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_packerFilter_fillial_name_packer'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPackerNameShiftData($id_shop, 'day_sb');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        } 

        if ($count_packer == 0) {
            $helper->tooltip = $module->l('Упаковщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Упаковщиков на смене в воскресенье.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vs';
        $helper->icon = 'icon-user';
        $helper->color = 'color3';
        $helper->title = $module->l('Упаковщиков на смене Воскресенье');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_packer = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_packer', 'fillial_name_packer','week_number_packer', 'table_packerFilter_fillial_name_packer','day_vs', 'shop_name_packer');
        $helper->value = $count_packer;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_packerFilter_fillial_name_packer'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getPackerNameShiftData($id_shop, 'day_vs');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        } 

        if ($count_packer == 0) {
            $helper->tooltip = $module->l('Упаковщиков нет на смене');
        }
        $kpis[] = $helper->generate();

        /**
         * Количество водителей на смене на каждый день.
         */
        /*Водителей на смене в понедельник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pn';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $module->l('Водителей на смене Понедельник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_driver = KpiFunctions::getCountDriverShiftData($id_shop, 'day_pn_1', 'day_pn_2', 'day_pn_3');
        $helper->value = $count_driver;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0){
            if($id_shop == 1 && $_POST['table_driverFilter_fillial_name_driver'] == NUll) {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $data = KpiFunctions::getDriverNameShiftData($id_shop, '', 'day_pn_1', 'day_pn_2', 'day_pn_3');
                $driver_name_person = implode(', ', array_column($data, 'fio'));
                $helper->tooltip .= $driver_name_person;
            } else {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №1: ');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_pn_1');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №2');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_pn_2');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №3');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_pn_3');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";
            }
        }

        if($count_driver == 0) {
            $helper->tooltip = $module->l('Водителей нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Водителей на смене во вторник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vt';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $module->l('Водителей на смене Вторник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_driver = KpiFunctions::getCountDriverShiftData($id_shop, 'day_vt_1', 'day_vt_2', 'day_vt_3');
        $helper->value = $count_driver;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0){
            if($id_shop == 1 && !isset($_POST['table_driverFilter_fillial_name_driver'])) {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $data = KpiFunctions::getDriverNameShiftData($id_shop, '', 'day_vt_1', 'day_vt_2', 'day_vt_3');
                $driver_name_person = implode(', ', array_column($data, 'fio'));
                $helper->tooltip .= $driver_name_person;
            } else {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №1: ');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_vt_1');
                $helper->tooltip .= implode(', ',array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №2');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_vt_2');
                $helper->tooltip .= implode(', ',array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №3');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_vt_3');
                $helper->tooltip .= implode(', ',array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";
            }
        }

        if($count_driver == 0) {
            $helper->tooltip = $module->l('Водителей нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Водителей на смене в среду.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sr';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $module->l('Водителей на смене Среда');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_driver = KpiFunctions::getCountDriverShiftData($id_shop, 'day_sr_1', 'day_sr_2', 'day_sr_3');
        $helper->value = $count_driver;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0){
            if($id_shop == 1 && !isset($_POST['table_driverFilter_fillial_name_driver'])) {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $data = KpiFunctions::getDriverNameShiftData($id_shop, '', 'day_sr_1', 'day_sr_2', 'day_sr_3');
                $driver_name_person = implode(', ', array_column($data, 'fio'));
                $helper->tooltip .= $driver_name_person;
            } else {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №1: ');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_sr_1');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №2');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_sr_2');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №3');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_sr_3');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";
            }
        }

        if($count_driver == 0) {
            $helper->tooltip = $module->l('Водителей нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Водителей на смене в четверг.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-cht';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $module->l('Водителей на смене Четверг');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_driver = KpiFunctions::getCountDriverShiftData($id_shop, 'day_cht_1', 'day_cht_2', 'day_cht_3');
        $helper->value = $count_driver;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0){
            if($id_shop == 1 && !isset($_POST['table_driverFilter_fillial_name_driver'])) {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $data = KpiFunctions::getDriverNameShiftData($id_shop, '', 'day_cht_1', 'day_cht_2', 'day_cht_3');
                $driver_name_person = implode(', ', array_column($data, 'fio'));
                $helper->tooltip .= $driver_name_person;
            } else {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №1: ');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_cht_1');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №2');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_cht_2');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №3');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_cht_3');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";
            }
        }

        if($count_driver == 0) {
            $helper->tooltip = $module->l('Водителей нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Водителей на смене в пятницу.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pt';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $module->l('Водителей на смене Пятница');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_driver = KpiFunctions::getCountDriverShiftData($id_shop, 'day_pt_1', 'day_pt_2', 'day_pt_3');
        $helper->value = $count_driver;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0){
            if($id_shop == 1 && !isset($_POST['table_driverFilter_fillial_name_driver'])) {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $data = KpiFunctions::getDriverNameShiftData($id_shop, '', 'day_pt_1', 'day_pt_2', 'day_pt_3');
                $driver_name_person = implode(', ', array_column($data, 'fio'));
                $helper->tooltip .= $driver_name_person;
            } else {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №1: ');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_pt_1');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №2');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_pt_2');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №3');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_pt_3');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";
            }
        }

        if($count_driver == 0) {
            $helper->tooltip = $module->l('Водителей нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Водителей на смене в суботу.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sb';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $module->l('Водителей на смене Суббота');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_driver = KpiFunctions::getCountDriverShiftData($id_shop, 'day_sb_1', 'day_sb_2', 'day_sb_3');
        $helper->value = $count_driver;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0){
            if($id_shop == 1 && !isset($_POST['table_driverFilter_fillial_name_driver'])) {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $data = KpiFunctions::getDriverNameShiftData($id_shop, '', 'day_sb_1', 'day_sb_2', 'day_sb_3');
                $driver_name_person = implode(', ', array_column($data, 'fio'));
                $helper->tooltip .= $driver_name_person;
            } else {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №1: ');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_sb_1');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №2');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_sb_2');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №3');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_sb_3');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";
            }
        }

        if($count_driver == 0) {
            $helper->tooltip = $module->l('Водителей нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Водителей на смене в воскресенье.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vs';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $module->l('Водителей на смене Воскресенье');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_driver = KpiFunctions::getCountDriverShiftData($id_shop, 'day_sb_1', 'day_sb_2', 'day_sb_3');
        $helper->value = $count_driver;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0){
            if($id_shop == 1 && !isset($_POST['table_driverFilter_fillial_name_driver'])) {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $data = KpiFunctions::getDriverNameShiftData($id_shop, '', 'day_vs_1', 'day_vs_2', 'day_vs_3');
                $driver_name_person = implode(', ', array_column($data, 'fio'));
                $helper->tooltip .= $driver_name_person;
            } else {
                $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_driverFilter_fillial_name_driver'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №1: ');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_vs_1');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №2');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_vs_2');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";

                $helper->tooltip .= $module->l('ВОЛНА №3');
                $helper->tooltip .= "\n";
                $driver_name_person = KpiFunctions::getDriverNameShiftData($id_shop, 'day_vs_3');
                $helper->tooltip .= implode(', ', array_column($driver_name_person, 'fio'));

                $helper->tooltip .= "\n";
                $helper->tooltip .= "\n";
            }
        }

        if($count_driver == 0) {
            $helper->tooltip = $module->l('Водителей нет на смене');
        }
        $kpis[] = $helper->generate();

        /**
         * Количество менеджеров на смене на каждый день.
         */
        /*Менеджеров на смене в понедельник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pn';
        $helper->icon = 'icon-user';
        $helper->color = 'color2';
        $helper->title = $module->l('Менеджеров на смене Понедельник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_manager = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_manager', 'fillial_name_manager','week_number_manager', 'table_managerFilter_fillial_name_manager','day_pn', 'shop_name_manager');
        $helper->value = $count_manager;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_managerFilter_fillial_name_manager'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getManagerNameShiftData($id_shop, 'day_pn');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        }

        if($count_manager == 0) {
            $helper->tooltip = $module->l('Менеджеров нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Менеджеров на смене во вторник.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vt';
        $helper->icon = 'icon-user';
        $helper->color = 'color2';
        $helper->title = $module->l('Менеджеров на смене Вторник');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_manager = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_manager', 'fillial_name_manager','week_number_manager', 'table_managerFilter_fillial_name_manager','day_vt', 'shop_name_manager');
        $helper->value = $count_manager;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_managerFilter_fillial_name_manager'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getManagerNameShiftData($id_shop, 'day_vt');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        }

        if($count_manager == 0) {
            $helper->tooltip = $module->l('Менеджеров нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Менеджеров на смене в среду.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sr';
        $helper->icon = 'icon-user';
        $helper->color = 'color2';
        $helper->title = $module->l('Менеджеров на смене Среда');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_manager = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_manager', 'fillial_name_manager','week_number_manager', 'table_managerFilter_fillial_name_manager','day_sr', 'shop_name_manager');
        $helper->value = $count_manager;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_managerFilter_fillial_name_manager'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getManagerNameShiftData($id_shop, 'day_sr');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        }

        if($count_manager == 0) {
            $helper->tooltip = $module->l('Менеджеров нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Менеджеров на смене в четверг.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-cht';
        $helper->icon = 'icon-user';
        $helper->color = 'color2';
        $helper->title = $module->l('Менеджеров на смене Четверг');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_manager = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_manager', 'fillial_name_manager','week_number_manager', 'table_managerFilter_fillial_name_manager','day_cht', 'shop_name_manager');
        $helper->value = $count_manager;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_managerFilter_fillial_name_manager'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getManagerNameShiftData($id_shop, 'day_cht');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        }

        if($count_manager == 0) {
            $helper->tooltip = $module->l('Менеджеров нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Менеджеров на смене в пятница.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-pt';
        $helper->icon = 'icon-user';
        $helper->color = 'color2';
        $helper->title = $module->l('Менеджеров на смене Пятница');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_manager = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_manager', 'fillial_name_manager','week_number_manager', 'table_managerFilter_fillial_name_manager','day_pt', 'shop_name_manager');
        $helper->value = $count_manager;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_managerFilter_fillial_name_manager'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getManagerNameShiftData($id_shop, 'day_pt');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        }

        if($count_manager == 0) {
            $helper->tooltip = $module->l('Менеджеров нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Менеджеров на смене в суботу.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-sb';
        $helper->icon = 'icon-user';
        $helper->color = 'color2';
        $helper->title = $module->l('Менеджеров на смене Суббота');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_manager = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_manager', 'fillial_name_manager','week_number_manager', 'table_managerFilter_fillial_name_manager','day_sb', 'shop_name_manager');
        $helper->value = $count_manager;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_managerFilter_fillial_name_manager'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getManagerNameShiftData($id_shop, 'day_sb');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        }

        if($count_manager == 0) {
            $helper->tooltip = $module->l('Менеджеров нет на смене');
        }
        $kpis[] = $helper->generate();

        /*Менеджеров на смене в воскресенье.*/
        $helper = new HelperKpi();
        $helper->id = 'box-new-vs';
        $helper->icon = 'icon-user';
        $helper->color = 'color2';
        $helper->title = $module->l('Менеджеров на смене Воскресенье');
        $helper->subtitle = date('W') . ' неделя';

        //Получение количества человек на смене.
        $count_manager = KpiFunctions::getCountPeopleShiftData($id_shop, 'staff_schedule_manager', 'fillial_name_manager','week_number_manager', 'table_managerFilter_fillial_name_manager','day_vs', 'shop_name_manager');
        $helper->value = $count_manager;

        //Получение имен сотрудников которые на смене.
        if($id_shop != 0) {
            $helper->tooltip .= $module->l(KpiFunctions::getHeadingKpiDay($id_shop, 'table_managerFilter_fillial_name_manager'));

            $helper->tooltip .= "\n";
            $helper->tooltip .= "\n";

            $data = KpiFunctions::getManagerNameShiftData($id_shop, 'day_vs');
            $packer_name_person = implode(', ', array_column($data, 'fio'));
            $helper->tooltip .= $packer_name_person;
        }

        if($count_manager == 0) {
            $helper->tooltip = $module->l('Менеджеров нет на смене');
        }
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        return $helper->generate();
    }
}