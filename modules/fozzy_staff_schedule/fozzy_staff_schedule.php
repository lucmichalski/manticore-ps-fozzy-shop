<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'fozzy_staff_schedule/classes/WeeklyScheduleKpi.php';
require_once _PS_MODULE_DIR_ . 'fozzy_staff_schedule/classes/CommonFunctions.php';

class Fozzy_staff_schedule extends Module {

    public $id_shop;

    public function __construct() {

        $this->name = 'fozzy_staff_schedule';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Rudyk M.';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fozzy Staff Schedule');
        $this->description = $this->l('Staff Schedule for Fozzy');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Установка модуля.
     * Module installation.
     * @return bool
     */
    public function install() {
        include dirname(__FILE__) . '/sql/install.php';
        return parent::install();
    }

    /**
     * Удаление модуля.
     * Removing a module.
     * @return bool
     */
    public function uninstall() {
        include dirname(__FILE__) . '/sql/uninstall.php';
        return parent::uninstall();
    }

    /**
     * Основная функция модуля.
     * The main function of the module.
     * @return string
     */
    public function getContent() {
        $output = '';
        $output .= '<h2>'.$this->displayName.'</h2>';
        $cookie = $this->context->cookie;
        $currentIndex = $this->context->currentindex;
        $this->id_shop = (int)Shop::getContextShopID();
        $this->context->controller->addJqueryPlugin('select2');
        $this->context->controller->addJS($this->_path.'views/js/admin.js');
        $this->context->controller->addCSS($this->_path.'views/css/admin.css');

        if(Tools::GetValue('id_picker')) {
            $id_picker = Tools::GetValue('id_person');
        } elseif(Tools::GetValue('id_packer')) {
            $id_packer = Tools::GetValue('id_person');
        } elseif(Tools::GetValue('id_driver')) {
            $id_driver = Tools::GetValue('id_person');
        } else {
            $id_manager = Tools::GetValue('id_person');
        }

        $edit = 0;
        $edit_driver = 0;
        if (Tools::isSubmit('updatetable_picker') || Tools::isSubmit('updatetable_packer') || Tools::isSubmit('updatetable_driver') || Tools::isSubmit('updatetable_manager')){
            if(isset($id_driver) && $id_driver > 0) {
                $edit_driver = 1;
                $output .= '<script>fozzy_staff_schedule_init_tabs();</script>';
            } else {
                $edit = 1;
            }
        }

        /**
         * Добавление графика Сборщикам, Упаковщикам, Менеджерам.
         */
        if (Tools::isSubmit('submitLinkAdd')){
            $count_work_day = 0;
            if(count($_POST['fozzy_staff_schedule_name_personal']) != 0) {
                for($i = 0; $i < count($_POST['fozzy_staff_schedule_name_personal']); $i++) {
                    $person_week_number = Tools::GetValue('fozzy_staff_schedule_week_number');
                    $person_year_number = date('Y');
                    if(Tools::GetValue('fozzy_staff_schedule_shop') == 1 || Tools::GetValue('fozzy_staff_schedule_shop') == 25 || Tools::GetValue('fozzy_staff_schedule_shop') == 30) {
                        $person_shop = 1;
                        $person_fillial = Tools::GetValue('fozzy_staff_schedule_shop');
                    } else {
                        $person_shop = Tools::GetValue('fozzy_staff_schedule_shop') ? Tools::GetValue('fozzy_staff_schedule_shop') : 1;
                        $person_fillial = Tools::GetValue('fozzy_staff_schedule_shop');
                    }
                    $person_role = Tools::GetValue('fozzy_staff_schedule_stype');
                    $person_tab_numper = $this->getTabNumber(Tools::GetValue('fozzy_staff_schedule_name_personal')[$i], $person_role);
                    $person_name = Tools::GetValue('fozzy_staff_schedule_name_personal')[$i];
                    $person_norm_day = Tools::GetValue('fozzy_staff_schedule_norma_day');
                    $person_start_work = Tools::GetValue('fozzy_staff_schedule_start_work').' : 00';
                    $person_end_work = Tools::GetValue('fozzy_staff_schedule_end_work').' : 00';
                    $person_time_work_day = ($person_end_work - $person_start_work) - 1;
                    $person_day_pn = Tools::GetValue('fozzy_staff_schedule_active_monday') ? Tools::GetValue('fozzy_staff_schedule_active_monday') : '-';
                    if($person_day_pn == 1) {
                        $count_work_day++;
                    }
                    $person_day_vt = Tools::GetValue('fozzy_staff_schedule_active_tuesday') ? Tools::GetValue('fozzy_staff_schedule_active_tuesday') : '-';
                    if($person_day_vt == 1) {
                        $count_work_day++;
                    }
                    $person_day_sr = Tools::GetValue('fozzy_staff_schedule_active_wednesday') ? Tools::GetValue('fozzy_staff_schedule_active_wednesday') : '-';
                    if($person_day_sr == 1) {
                        $count_work_day++;
                    }
                    $person_day_cht = Tools::GetValue('fozzy_staff_schedule_active_thursday') ? Tools::GetValue('fozzy_staff_schedule_active_thursday') : '-';
                    if($person_day_cht == 1) {
                        $count_work_day++;
                    }
                    $person_day_pt = Tools::GetValue('fozzy_staff_schedule_active_friday') ? Tools::GetValue('fozzy_staff_schedule_active_friday') : '-';
                    if($person_day_pt == 1) {
                        $count_work_day++;
                    }
                    $person_day_sb = Tools::GetValue('fozzy_staff_schedule_active_saturday') ? Tools::GetValue('fozzy_staff_schedule_active_saturday') : '-';
                    if($person_day_sb == 1) {
                        $count_work_day++;
                    }
                    $person_day_vs = Tools::GetValue('fozzy_staff_schedule_active_sunday') ? Tools::GetValue('fozzy_staff_schedule_active_sunday') : '-';
                    if($person_day_vs == 1) {
                        $count_work_day++;
                    }
                    $person_time_all = $count_work_day * $person_time_work_day;
                    $person_work_day_all = $count_work_day;
                    $person_output_day_all = 7 - $count_work_day;
                    $person_comment = Tools::GetValue('fozzy_staff_schedule_to_comment');

                    if($person_role == 1 && $person_tab_numper != 0) {
                        $person_schedule_create = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_picker` WHERE `week_number_picker` = ".$person_week_number." AND `tab_number_picker` = ".$person_tab_numper." AND `fillial_name_picker` = ".$person_fillial);
                    } elseif ($person_role == 2 && $person_tab_numper != 0) {
                        $person_schedule_create = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_packer` WHERE `week_number_packer` = ".$person_week_number." AND `tab_number_packer` = ".$person_tab_numper." AND `fillial_name_packer` = ".$person_fillial);
                    } elseif ($person_role == 4 && $person_tab_numper != 0) {
                        $person_schedule_create = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_manager` WHERE `week_number_manager` = " . $person_week_number . " AND `tab_number_manager` = " . $person_tab_numper." AND `fillial_name_manager` = ".$person_fillial);
                    }

                    if(empty($person_schedule_create)) {
                        switch ($person_role) {
                            case 1:
                                $sql_add_picker = "INSERT INTO `" . _DB_PREFIX_ . "staff_schedule_picker` (`week_number_picker`, `year_number_picker`, `shop_name_picker`, `fillial_name_picker`, `persone_role`, `tab_number_picker`, `picker_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn`, `day_vt`, `day_sr`, `day_cht`, `day_pt`, `day_sb`, `day_vs`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('" . $person_week_number . "','" . $person_year_number . "','" . $person_shop . "','" . $person_fillial . "','" . $person_role . "','" . $person_tab_numper . "','" . $person_name . "','" . $person_norm_day . "','" . $person_start_work . "','" . $person_end_work . "','" . $person_time_work_day . "','" . $person_day_pn . "','" . $person_day_vt . "','" . $person_day_sr . "','" . $person_day_cht . "','" . $person_day_pt . "','" . $person_day_sb . "','" . $person_day_vs . "','" . $person_time_all . "','" . $person_work_day_all . "','" . $person_output_day_all . "','" . $person_comment . "')";
                                Db::getInstance()->execute($sql_add_picker);
                                $count_work_day = 0;
                                break;

                            case 2:
                                $sql_add_packer = "INSERT INTO `" . _DB_PREFIX_ . "staff_schedule_packer` (`week_number_packer`, `year_number_packer`, `shop_name_packer`, `fillial_name_packer`, `persone_role`, `tab_number_packer`, `packer_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn`, `day_vt`, `day_sr`, `day_cht`, `day_pt`, `day_sb`, `day_vs`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('" . $person_week_number . "','" . $person_year_number . "','" . $person_shop . "','" . $person_fillial . "','" . $person_role . "','" . $person_tab_numper . "','" . $person_name . "','" . $person_norm_day . "','" . $person_start_work . "','" . $person_end_work . "','" . $person_time_work_day . "','" . $person_day_pn . "','" . $person_day_vt . "','" . $person_day_sr . "','" . $person_day_cht . "','" . $person_day_pt . "','" . $person_day_sb . "','" . $person_day_vs . "','" . $person_time_all . "','" . $person_work_day_all . "','" . $person_output_day_all . "','" . $person_comment . "')";
                                Db::getInstance()->execute($sql_add_packer);
                                $count_work_day = 0;
                                break;

                            case 4:
                                $sql_add_manager = "INSERT INTO `" . _DB_PREFIX_ . "staff_schedule_manager` (`week_number_manager`, `year_number_manager`, `shop_name_manager`, `fillial_name_manager`, `persone_role`, `tab_number_manager`, `manager_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn`, `day_vt`, `day_sr`, `day_cht`, `day_pt`, `day_sb`, `day_vs`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('" . $person_week_number . "','" . $person_year_number . "','" . $person_shop . "','" . $person_fillial . "','" . $person_role . "','" . $person_tab_numper . "','" . $person_name . "','" . $person_norm_day . "','" . $person_start_work . "','" . $person_end_work . "','" . $person_time_work_day . "','" . $person_day_pn . "','" . $person_day_vt . "','" . $person_day_sr . "','" . $person_day_cht . "','" . $person_day_pt . "','" . $person_day_sb . "','" . $person_day_vs . "','" . $person_time_all . "','" . $person_work_day_all . "','" . $person_output_day_all . "','" . $person_comment . "')";
                                Db::getInstance()->execute($sql_add_manager);
                                $count_work_day = 0;
                                break;
                        }
                    } else {
                        $url = $currentIndex . '&fozzy_staff_schedule_settings=1&add_person='.$person_role.'&copy_schedule_fozzy_staff_schedule&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '#graphsdriver';
                        Tools::redirectAdmin($url);
                    }
                }
                $url = $currentIndex . '&fozzy_staff_schedule_settings=1&add_person='.$person_role.'&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '#graphsall';
                Tools::redirectAdmin($url);
            } else {
                $output .= $this->displayError($this->l('Error adding a schedule to an employee.'));

            }
        }

        /**
         * Редактирование графика Сборщикам, Упаковщикам, Менеджерам.
         */
        if (Tools::isSubmit('submitLinkEdit')){
            $count_work_day = 0;
            for($i = 0; $i < count(array_unique($_POST['fozzy_staff_schedule_name_personal'])); $i++) {
                $id_person = Tools::GetValue('fozzy_staff_schedule_id_person');
                $person_week_number = Tools::GetValue('fozzy_staff_schedule_week_number');
                if(Tools::GetValue('fozzy_staff_schedule_shop') == 1 || Tools::GetValue('fozzy_staff_schedule_shop') == 25 || Tools::GetValue('fozzy_staff_schedule_shop') == 30) {
                    $person_shop = 1;
                    $person_fillial = Tools::GetValue('fozzy_staff_schedule_shop');
                } else {
                    $person_shop = Tools::GetValue('fozzy_staff_schedule_shop') ? Tools::GetValue('fozzy_staff_schedule_shop') : 1;
                    $person_fillial = Tools::GetValue('fozzy_staff_schedule_shop');
                }
                $person_role = Tools::GetValue('fozzy_staff_schedule_stype');
                $person_tab_numper = $this->getTabNumber(Tools::GetValue('fozzy_staff_schedule_name_personal')[$i], $person_role);
                $person_name = array_unique(Tools::GetValue('fozzy_staff_schedule_name_personal'))[0];
                $person_norm_day = Tools::GetValue('fozzy_staff_schedule_norma_day');
                $person_start_work = Tools::GetValue('fozzy_staff_schedule_start_work').' : 00';
                $person_end_work = Tools::GetValue('fozzy_staff_schedule_end_work').' : 00';
                $person_time_work_day = ($person_end_work - $person_start_work) - 1;
                $person_day_pn = Tools::GetValue('fozzy_staff_schedule_active_monday') ? Tools::GetValue('fozzy_staff_schedule_active_monday') : '-';
                if($person_day_pn == 1) {
                    $count_work_day++;
                }
                $person_day_vt = Tools::GetValue('fozzy_staff_schedule_active_tuesday') ? Tools::GetValue('fozzy_staff_schedule_active_tuesday') : '-';
                if($person_day_vt == 1) {
                    $count_work_day++;
                }
                $person_day_sr = Tools::GetValue('fozzy_staff_schedule_active_wednesday') ? Tools::GetValue('fozzy_staff_schedule_active_wednesday') : '-';
                if($person_day_sr == 1) {
                    $count_work_day++;
                }
                $person_day_cht = Tools::GetValue('fozzy_staff_schedule_active_thursday') ? Tools::GetValue('fozzy_staff_schedule_active_thursday') : '-';
                if($person_day_cht == 1) {
                    $count_work_day++;
                }
                $person_day_pt = Tools::GetValue('fozzy_staff_schedule_active_friday') ? Tools::GetValue('fozzy_staff_schedule_active_friday') : '-';
                if($person_day_pt == 1) {
                    $count_work_day++;
                }
                $person_day_sb = Tools::GetValue('fozzy_staff_schedule_active_saturday') ? Tools::GetValue('fozzy_staff_schedule_active_saturday') : '-';
                if($person_day_sb == 1) {
                    $count_work_day++;
                }
                $person_day_vs = Tools::GetValue('fozzy_staff_schedule_active_sunday') ? Tools::GetValue('fozzy_staff_schedule_active_sunday') : '-';
                if($person_day_vs == 1) {
                    $count_work_day++;
                }
                $person_time_all = $count_work_day * $person_time_work_day;
                $person_work_day_all = $count_work_day;
                $person_output_day_all = 7 - $count_work_day;
                $person_comment = Tools::GetValue('fozzy_staff_schedule_to_comment');

                switch ($person_role) {
                    case 1:
                        $sql_update_picker = "UPDATE `"._DB_PREFIX_."staff_schedule_picker` 
                        SET `week_number_picker` = ".$person_week_number.", 
                        `shop_name_picker` = ".$person_shop.",
                        `fillial_name_picker` = ".$person_fillial.", 
                        `persone_role` = '".$person_role."', 
                        `tab_number_picker` = '".$person_tab_numper."', 
                        `picker_name_person` = '".$person_name."', 
                        `norm_day` = ".$person_norm_day.", 
                        `start_work` = '".$person_start_work."', 
                        `end_work` = '".$person_end_work."', 
                        `time_work` = '".$person_time_work_day."', 
                        `day_pn` = '".$person_day_pn."', 
                        `day_vt` = '".$person_day_vt."', 
                        `day_sr` = '".$person_day_sr."', 
                        `day_cht` = '".$person_day_cht."', 
                        `day_pt` = '".$person_day_pt."', 
                        `day_sb` = '".$person_day_sb."', 
                        `day_vs` = '".$person_day_vs."', 
                        `week_time_all` = '".$person_time_all."', 
                        `week_work_day_all` = '".$person_work_day_all."', 
                        `week_output_day_all` = '".$person_output_day_all."', 
                        `comment_person` = '".$person_comment."' 
                        WHERE `id_person` = ".$id_person;
                        Db::getInstance()->execute($sql_update_picker);

                        $output .= $this->displayConfirmation($this->l('Collectors schedule has been successfully updated.'));
                        $count_work_day = 0;
                        break;

                    case 2:
                        $sql_update_packer = "UPDATE `"._DB_PREFIX_."staff_schedule_packer` 
                        SET `week_number_packer` = ".$person_week_number.", 
                        `shop_name_packer` = ".$person_shop.",
                        `fillial_name_packer` = ".$person_fillial.", 
                        `persone_role` = '".$person_role."', 
                        `tab_number_packer` = '".$person_tab_numper."', 
                        `packer_name_person` = '".$person_name."', 
                        `norm_day` = ".$person_norm_day.", 
                        `start_work` = '".$person_start_work."', 
                        `end_work` = '".$person_end_work."', 
                        `time_work` = '".$person_time_work_day."', 
                        `day_pn` = '".$person_day_pn."', 
                        `day_vt` = '".$person_day_vt."', 
                        `day_sr` = '".$person_day_sr."', 
                        `day_cht` = '".$person_day_cht."', 
                        `day_pt` = '".$person_day_pt."', 
                        `day_sb` = '".$person_day_sb."', 
                        `day_vs` = '".$person_day_vs."', 
                        `week_time_all` = '".$person_time_all."', 
                        `week_work_day_all` = '".$person_work_day_all."', 
                        `week_output_day_all` = '".$person_output_day_all."', 
                        `comment_person` = '".$person_comment."' 
                        WHERE `id_person` = ".$id_person;
                        Db::getInstance()->execute($sql_update_packer);

                        $output .= $this->displayConfirmation($this->l('The schedule for the packer has been successfully updated.'));
                        $count_work_day = 0;
                        break;

                    case 4:
                        $sql_update_manager = "UPDATE `"._DB_PREFIX_."staff_schedule_manager` 
                        SET `week_number_manager` = ".$person_week_number.", 
                        `shop_name_manager` = ".$person_shop.",
                        `fillial_name_manager` = ".$person_fillial.", 
                        `persone_role` = '".$person_role."', 
                        `tab_number_manager` = '".$person_tab_numper."', 
                        `manager_name_person` = '".$person_name."', 
                        `norm_day` = ".$person_norm_day.", 
                        `start_work` = '".$person_start_work."', 
                        `end_work` = '".$person_end_work."', 
                        `time_work` = '".$person_time_work_day."', 
                        `day_pn` = '".$person_day_pn."', 
                        `day_vt` = '".$person_day_vt."', 
                        `day_sr` = '".$person_day_sr."', 
                        `day_cht` = '".$person_day_cht."', 
                        `day_pt` = '".$person_day_pt."', 
                        `day_sb` = '".$person_day_sb."', 
                        `day_vs` = '".$person_day_vs."', 
                        `week_time_all` = '".$person_time_all."', 
                        `week_work_day_all` = '".$person_work_day_all."', 
                        `week_output_day_all` = '".$person_output_day_all."', 
                        `comment_person` = '".$person_comment."' 
                        WHERE `id_person` = ".$id_person;
                        Db::getInstance()->execute($sql_update_manager);

                        $output .= $this->displayConfirmation($this->l('The managers schedule has been successfully updated.'));
                        $count_work_day = 0;
                        break;
                }
            }

        }

        /**
         * Добавление графика водителям.
         */
        if(Tools::isSubmit('submitLinkAddDriver')) {
            $count_work_day = 0;
            $count_work_hour = 0;
            if(count($_POST['fozzy_staff_schedule_driver_name_personal']) != 0) {
                for($i = 0; $i < count($_POST['fozzy_staff_schedule_driver_name_personal']); $i++) {
                    $person_week_number = Tools::GetValue('fozzy_staff_schedule_driver_week_number');
                    $person_year_number = date('Y');
                    if(Tools::GetValue('fozzy_staff_schedule_driver_shop') == 1 || Tools::GetValue('fozzy_staff_schedule_driver_shop') == 25 || Tools::GetValue('fozzy_staff_schedule_driver_shop') == 30) {
                        $person_shop = 1;
                        $person_fillial = Tools::GetValue('fozzy_staff_schedule_driver_shop');
                    } else {
                        $person_shop = Tools::GetValue('fozzy_staff_schedule_driver_shop') ? Tools::GetValue('fozzy_staff_schedule_driver_shop') : 1;
                        $person_fillial = Tools::GetValue('fozzy_staff_schedule_driver_shop');
                    }
                    $person_role = Tools::GetValue('fozzy_staff_schedule_driver_stype');
                    $person_tab_numper = $this->getTabNumber(Tools::GetValue('fozzy_staff_schedule_driver_name_personal')[$i], $person_role);
                    $person_name = Tools::GetValue('fozzy_staff_schedule_driver_name_personal')[$i];
                    $person_norm_day = Tools::GetValue('fozzy_staff_schedule_driver_norma_day');
                    $person_start_work = Tools::GetValue('fozzy_staff_schedule_driver_start_work').' : 00';
                    $person_end_work = Tools::GetValue('fozzy_staff_schedule_driver_end_work').' : 00';
                    $person_time_work_day = ($person_end_work - $person_start_work) - 1;

                    // Три волны понедельника.
                    $person_day_pn_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_monday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_monday_1') : '-';
                    if($person_day_pn_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pn_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_monday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_monday_2') : '-';
                    if($person_day_pn_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pn_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_monday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_monday_3') : '-';
                    if($person_day_pn_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_pn_1 == 1 || $person_day_pn_2 == 1 || $person_day_pn_3 == 1) {
                        $count_work_day++;
                    }

                    // Три волны вторника.
                    $person_day_vt_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_1') : '-';
                    if($person_day_vt_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vt_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_2') : '-';
                    if($person_day_vt_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vt_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_3') : '-';
                    if($person_day_vt_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_vt_1 == 1 || $person_day_vt_2 == 1 || $person_day_vt_3 == 1) {
                        $count_work_day++;
                    }

                    // Три волны среды.
                    $person_day_sr_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_1') : '-';
                    if($person_day_sr_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sr_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_2') : '-';
                    if($person_day_sr_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sr_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_3') : '-';
                    if($person_day_sr_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_sr_1 == 1 || $person_day_sr_2 == 1 || $person_day_sr_3 == 1) {
                        $count_work_day++;
                    }

                    // Три волны четверга.
                    $person_day_cht_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_1') : '-';
                    if($person_day_cht_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_cht_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_2') : '-';
                    if($person_day_cht_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_cht_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_3') : '-';
                    if($person_day_cht_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_cht_1 == 1 || $person_day_cht_2 == 1 || $person_day_cht_3 == 1) {
                        $count_work_day++;
                    }

                    // Три волны пятници.
                    $person_day_pt_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_friday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_friday_1') : '-';
                    if($person_day_pt_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pt_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_friday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_friday_2') : '-';
                    if($person_day_pt_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pt_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_friday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_friday_3') : '-';
                    if($person_day_pt_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_pt_1 == 1 || $person_day_pt_2 == 1 || $person_day_pt_3 == 1) {
                        $count_work_day++;
                    }

                    // Три волны субботы.
                    $person_day_sb_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_1') : '-';
                    if($person_day_sb_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sb_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_2') : '-';
                    if($person_day_sb_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sb_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_3') : '-';
                    if($person_day_sb_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_sb_1 == 1 || $person_day_sb_2 == 1 || $person_day_sb_3 == 1) {
                        $count_work_day++;
                    }

                    // Три волны воскресенья.
                    $person_day_vs_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_1') : '-';
                    if($person_day_vs_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vs_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_2') : '-';
                    if($person_day_vs_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vs_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_3') : '-';
                    if($person_day_vs_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_vs_1 == 1 || $person_day_vs_2 == 1 || $person_day_vs_3 == 1) {
                        $count_work_day++;
                    }

                    $person_time_all = $count_work_hour * 4;
                    $person_work_day_all = $count_work_day;
                    $person_output_day_all = 7 - $count_work_day;
                    $person_comment = Tools::GetValue('fozzy_staff_schedule_driver_to_comment');

                    if($person_tab_numper != 0){
                        $person_schedule_create = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_driver` WHERE `week_number_driver` = ".$person_week_number." AND `tab_number_driver` = ".$person_tab_numper." AND `fillial_name_driver` = ".$person_fillial);
                    }
                    if(empty($person_schedule_create)) {
                        switch ($person_role) {
                            case 3:
                                $sql_add_driver = "INSERT INTO `"._DB_PREFIX_."staff_schedule_driver` (`week_number_driver`, `year_number_driver`, `shop_name_driver`, `fillial_name_driver`, `persone_role`, `tab_number_driver`, `driver_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn_1`, `day_pn_2`, `day_pn_3`, `day_vt_1`, `day_vt_2`, `day_vt_3`, `day_sr_1`, `day_sr_2`, `day_sr_3`, `day_cht_1`, `day_cht_2`, `day_cht_3`, `day_pt_1`, `day_pt_2`, `day_pt_3`, `day_sb_1`, `day_sb_2`, `day_sb_3`, `day_vs_1`, `day_vs_2`, `day_vs_3`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('".$person_week_number."','".$person_year_number."','".$person_shop."','".$person_fillial."','".$person_role."','".$person_tab_numper."','".$person_name."','".$person_norm_day."','".$person_start_work."','".$person_end_work."','".$person_time_work_day."','".$person_day_pn_1."','".$person_day_pn_2."','".$person_day_pn_3."','".$person_day_vt_1."','".$person_day_vt_2."','".$person_day_vt_3."','".$person_day_sr_1."','".$person_day_sr_2."','".$person_day_sr_3."','".$person_day_cht_1."','".$person_day_cht_2."','".$person_day_cht_3."','".$person_day_pt_1."','".$person_day_pt_2."','".$person_day_pt_3."','".$person_day_sb_1."','".$person_day_sb_2."','".$person_day_sb_3."','".$person_day_vs_1."','".$person_day_vs_2."','".$person_day_vs_3."','".$person_time_all."','".$person_work_day_all."','".$person_output_day_all."','".$person_comment."')";
                                Db::getInstance()->execute($sql_add_driver);
                                $count_work_hour = 0;
                                $count_work_day = 0;
                                break;
                        }
                    } else {
                        $url = $currentIndex . '&fozzy_staff_schedule_settings=1&copy_schedule_fozzy_staff_schedule_driver&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '#graphsdriver';
                        Tools::redirectAdmin($url);
                    }
                }
                $url = $currentIndex . '&fozzy_staff_schedule_settings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '#graphsdriver';
                Tools::redirectAdmin($url);
            } else {
                $output .= $this->displayError($this->l('Error adding a schedule to an employee.'));
            }
        }

        /**
         * Обновление графика водителям.
         */
        if(Tools::isSubmit('submitLinkEditDriver')) {
            $count_work_day = 0;
            $count_work_hour = 0;

            if(count($_POST['fozzy_staff_schedule_driver_name_personal']) != 0) {
                for($i = 0; $i < count($_POST['fozzy_staff_schedule_driver_name_personal']); $i++) {
                    $id_person = Tools::GetValue('fozzy_staff_schedule_driver_id_person');
                    $person_week_number = Tools::GetValue('fozzy_staff_schedule_driver_week_number');
                    $person_year_number = date('Y');
                    if(Tools::GetValue('fozzy_staff_schedule_driver_shop') == 1 || Tools::GetValue('fozzy_staff_schedule_driver_shop') == 25 || Tools::GetValue('fozzy_staff_schedule_driver_shop') == 30) {
                        $person_shop = 1;
                        $person_fillial = Tools::GetValue('fozzy_staff_schedule_driver_shop');
                    } else {
                        $person_shop = Tools::GetValue('fozzy_staff_schedule_driver_shop') ? Tools::GetValue('fozzy_staff_schedule_driver_shop') : 1;
                        $person_fillial = Tools::GetValue('fozzy_staff_schedule_driver_shop');
                    }
                    $person_role = Tools::GetValue('fozzy_staff_schedule_driver_stype');
                    $person_tab_numper = $this->getTabNumber(Tools::GetValue('fozzy_staff_schedule_driver_name_personal')[$i], $person_role);
                    $person_name = Tools::GetValue('fozzy_staff_schedule_driver_name_personal')[$i];
                    $person_norm_day = Tools::GetValue('fozzy_staff_schedule_driver_norma_day');
                    $person_start_work = Tools::GetValue('fozzy_staff_schedule_driver_start_work').' : 00';
                    $person_end_work = Tools::GetValue('fozzy_staff_schedule_driver_end_work').' : 00';
                    $person_time_work_day = ($person_end_work - $person_start_work) - 1;

                    /**
                     * Три волны понедельника.
                     */
                    $person_day_pn_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_monday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_monday_1') : '-';
                    if($person_day_pn_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pn_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_monday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_monday_2') : '-';
                    if($person_day_pn_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pn_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_monday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_monday_3') : '-';
                    if($person_day_pn_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_pn_1 == 1 || $person_day_pn_2 == 1 || $person_day_pn_3 == 1) {
                        $count_work_day++;
                    }

                    /**
                     * Три волны вторника.
                     */
                    $person_day_vt_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_1') : '-';
                    if($person_day_vt_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vt_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_2') : '-';
                    if($person_day_vt_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vt_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_tuesday_3') : '-';
                    if($person_day_vt_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_vt_1 == 1 || $person_day_vt_2 == 1 || $person_day_vt_3 == 1) {
                        $count_work_day++;
                    }

                    /**
                     * Три волны среды.
                     */
                    $person_day_sr_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_1') : '-';
                    if($person_day_sr_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sr_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_2') : '-';
                    if($person_day_sr_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sr_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_wednesday_3') : '-';
                    if($person_day_sr_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_sr_1 == 1 || $person_day_sr_2 == 1 || $person_day_sr_3 == 1) {
                        $count_work_day++;
                    }

                    /**
                     * Три волны четверга.
                     */
                    $person_day_cht_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_1') : '-';
                    if($person_day_cht_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_cht_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_2') : '-';
                    if($person_day_cht_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_cht_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_thursday_3') : '-';
                    if($person_day_cht_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_cht_1 == 1 || $person_day_cht_2 == 1 || $person_day_cht_3 == 1) {
                        $count_work_day++;
                    }

                    /**
                     * Три волны пятници.
                     */
                    $person_day_pt_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_friday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_friday_1') : '-';
                    if($person_day_pt_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pt_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_friday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_friday_2') : '-';
                    if($person_day_pt_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_pt_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_friday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_friday_3') : '-';
                    if($person_day_pt_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_pt_1 == 1 || $person_day_pt_2 == 1 || $person_day_pt_3 == 1) {
                        $count_work_day++;
                    }

                    /**
                     * Три волны субботы.
                     */
                    $person_day_sb_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_1') : '-';
                    if($person_day_sb_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sb_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_2') : '-';
                    if($person_day_sb_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_sb_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_saturday_3') : '-';
                    if($person_day_sb_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_sb_1 == 1 || $person_day_sb_2 == 1 || $person_day_sb_3 == 1) {
                        $count_work_day++;
                    }

                    /**
                     * Три волны воскресенья.
                     */
                    $person_day_vs_1 = Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_1') ? Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_1') : '-';
                    if($person_day_vs_1 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vs_2 = Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_2') ? Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_2') : '-';
                    if($person_day_vs_2 == 1) {
                        $count_work_hour++;
                    }
                    $person_day_vs_3 = Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_3') ? Tools::GetValue('fozzy_staff_schedule_driver_active_sunday_3') : '-';
                    if($person_day_vs_3 == 1) {
                        $count_work_hour++;
                    }
                    if($person_day_vs_1 == 1 || $person_day_vs_2 == 1 || $person_day_vs_3 == 1) {
                        $count_work_day++;
                    }

                    $person_time_all = $count_work_hour * 4;
                    $person_work_day_all = $count_work_day;
                    $person_output_day_all = 7 - $count_work_day;
                    $person_comment = Tools::GetValue('fozzy_staff_schedule_driver_to_comment');

                    switch ($person_role) {
                        case 3:
                            $sql_update_driver = "UPDATE `"._DB_PREFIX_."staff_schedule_driver` 
                            SET `week_number_driver` = ".$person_week_number.",
                            `year_number_driver` = ".$person_year_number.", 
                            `shop_name_driver` = ".$person_shop.",
                            `fillial_name_driver` = ".$person_fillial.", 
                            `persone_role` = '".$person_role."', 
                            `tab_number_driver` = '".$person_tab_numper."', 
                            `driver_name_person` = '".$person_name."', 
                            `norm_day` = ".$person_norm_day.", 
                            `start_work` = '".$person_start_work."', 
                            `end_work` = '".$person_end_work."', 
                            `time_work` = '".$person_time_work_day."', 
                            `day_pn_1` = '".$person_day_pn_1."', 
                            `day_pn_2` = '".$person_day_pn_2."', 
                            `day_pn_3` = '".$person_day_pn_3."', 
                            `day_vt_1` = '".$person_day_vt_1."', 
                            `day_vt_2` = '".$person_day_vt_2."', 
                            `day_vt_3` = '".$person_day_vt_3."', 
                            `day_sr_1` = '".$person_day_sr_1."', 
                            `day_sr_2` = '".$person_day_sr_2."', 
                            `day_sr_3` = '".$person_day_sr_3."', 
                            `day_cht_1` = '".$person_day_cht_1."', 
                            `day_cht_2` = '".$person_day_cht_2."', 
                            `day_cht_3` = '".$person_day_cht_3."', 
                            `day_pt_1` = '".$person_day_pt_1."', 
                            `day_pt_2` = '".$person_day_pt_2."', 
                            `day_pt_3` = '".$person_day_pt_3."', 
                            `day_sb_1` = '".$person_day_sb_1."', 
                            `day_sb_2` = '".$person_day_sb_2."', 
                            `day_sb_3` = '".$person_day_sb_3."', 
                            `day_vs_1` = '".$person_day_vs_1."', 
                            `day_vs_2` = '".$person_day_vs_2."', 
                            `day_vs_3` = '".$person_day_vs_3."', 
                            `week_time_all` = '".$person_time_all."', 
                            `week_work_day_all` = '".$person_work_day_all."', 
                            `week_output_day_all` = '".$person_output_day_all."', 
                            `comment_person` = '".$person_comment."' 
                            WHERE `id_person` = ".$id_person;
                            Db::getInstance()->execute($sql_update_driver);

                            $url = $currentIndex . '&fozzy_staff_schedule_settings=1&updatefozzy_staff_schedule_driver&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '#graphsdriver';
                            Tools::redirectAdmin($url);
                            $count_work_day = 0;
                            break;
                    }
                }
            } else {
                $output .= $this->displayError($this->l('An error occurred while updating the schedule to the driver.'));
            }

        }

        /**
         * Создания графика на следующую неделю.
         */
        if (Tools::isSubmit('submitBulkScheduleNextWeektable_picker')){
            $this_week_person = $_POST;
            $schedule_already_been_created_picker = 0;
            $schedule_already_been_created_picker_name = array();
            for ($i = 0; $i < count($this_week_person['table_pickerBox']); $i++) {
                $this_week = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_picker` WHERE `id_person` = " . $this_week_person['table_pickerBox'][$i]);
                $person_week_number = date('W') + 1;
                $person_year_number = date('Y');

                $sql_question  = "SELECT *  FROM `" . _DB_PREFIX_ . "staff_schedule_picker`";
                $sql_question  .= ' LEFT JOIN `ps_fozzy_logistic_sborshik` ON `ps_fozzy_logistic_sborshik`.`id_sborshik` = `ps_staff_schedule_picker`.`picker_name_person`';
                $sql_question  .= " WHERE `week_number_picker` = " . $person_week_number;
                $sql_question  .= " AND `fillial_name_picker` = " . $this_week[0]['fillial_name_picker'];
                $sql_question  .= " AND `tab_number_picker` = " . $this_week[0]['tab_number_picker'];
                $next_week = Db::getInstance()->executeS($sql_question);

                if(empty($next_week) || $this_week[0]['tab_number_picker'] == 0) {
                    $sql_next_week_picker = "INSERT INTO `"._DB_PREFIX_."staff_schedule_picker` (`week_number_picker`, `year_number_picker`, `shop_name_picker`, `fillial_name_picker`, `persone_role`, `tab_number_picker`, `picker_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn`, `day_vt`, `day_sr`, `day_cht`, `day_pt`, `day_sb`, `day_vs`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('".$person_week_number."','".$person_year_number."','".$this_week[0]['shop_name_picker']."','".$this_week[0]['fillial_name_picker']."','".$this_week[0]['persone_role']."','".$this_week[0]['tab_number_picker']."','".$this_week[0]['picker_name_person']."','".$this_week[0]['norm_day']."','".$this_week[0]['start_work']."','".$this_week[0]['end_work']."','".$this_week[0]['time_work']."','".$this_week[0]['day_pn']."','".$this_week[0]['day_vt']."','".$this_week[0]['day_sr']."','".$this_week[0]['day_cht']."','".$this_week[0]['day_pt']."','".$this_week[0]['day_sb']."','".$this_week[0]['day_vs']."','".$this_week[0]['week_time_all']."','".$this_week[0]['week_work_day_all']."','".$this_week[0]['week_output_day_all']."','".$this_week[0]['comment_person']."')";
                    Db::getInstance()->execute($sql_next_week_picker);
                } else {
                    array_push($schedule_already_been_created_picker_name, $next_week[0]['fio']);
                    $schedule_already_been_created_picker++;
                }
            }

            if($schedule_already_been_created_picker == 0) {
                $url = $currentIndex . '&fozzy_staff_schedule_settings=1&add_person=1&nextweekfozzy_staff_schedule_picker&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee));
                Tools::redirectAdmin($url);
            } elseif ($schedule_already_been_created_picker == count($this_week_person['table_pickerBox'])) {
                $output .= $this->displayError($this->l('График для этих сборщиков на следующую неделю уже был создан.'));
            } else {
                $output .= $this->displayInformation(implode(", ", $schedule_already_been_created_picker_name) . $this->l(' - для этих сборщиков на следующую неделю уже был создан график.'));
            }
        }

        if (Tools::isSubmit('submitBulkScheduleNextWeektable_packer')) {
            $this_week_person = $_POST;
            $schedule_already_been_created_packer = 0;
            $schedule_already_been_created_packer_name = array();
            for ($i = 0; $i < count($this_week_person['table_packerBox']); $i++) {
                $this_week = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_packer` WHERE `id_person` = " . $this_week_person['table_packerBox'][$i]);
                $person_week_number = date('W') + 1;
                $person_year_number = date('Y');

                $sql_question  = "SELECT *  FROM `" . _DB_PREFIX_ . "staff_schedule_packer`";
                $sql_question  .= ' LEFT JOIN `ps_fozzy_logistic_packer` ON `ps_fozzy_logistic_packer`.`id_packer` = `ps_staff_schedule_packer`.`packer_name_person`';
                $sql_question  .= " WHERE `week_number_packer` = " . $person_week_number;
                $sql_question  .= " AND `fillial_name_packer` = " . $this_week[0]['fillial_name_packer'];
                $sql_question  .= " AND `tab_number_packer` = " . $this_week[0]['tab_number_packer'];
                $next_week = Db::getInstance()->executeS($sql_question);

                if(empty($next_week) || $this_week[0]['tab_number_packer'] == 0) {
                    $sql_next_week_packer = "INSERT INTO `"._DB_PREFIX_."staff_schedule_packer` (`week_number_packer`, `year_number_packer`, `shop_name_packer`, `fillial_name_packer`, `persone_role`, `tab_number_packer`, `packer_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn`, `day_vt`, `day_sr`, `day_cht`, `day_pt`, `day_sb`, `day_vs`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('".$person_week_number."','".$person_year_number."','".$this_week[0]['shop_name_packer']."','".$this_week[0]['fillial_name_packer']."','".$this_week[0]['persone_role']."','".$this_week[0]['tab_number_packer']."','".$this_week[0]['packer_name_person']."','".$this_week[0]['norm_day']."','".$this_week[0]['start_work']."','".$this_week[0]['end_work']."','".$this_week[0]['time_work']."','".$this_week[0]['day_pn']."','".$this_week[0]['day_vt']."','".$this_week[0]['day_sr']."','".$this_week[0]['day_cht']."','".$this_week[0]['day_pt']."','".$this_week[0]['day_sb']."','".$this_week[0]['day_vs']."','".$this_week[0]['week_time_all']."','".$this_week[0]['week_work_day_all']."','".$this_week[0]['week_output_day_all']."','".$this_week[0]['comment_person']."')";
                    Db::getInstance()->execute($sql_next_week_packer);
                } else {
                    array_push($schedule_already_been_created_packer_name, $next_week[0]['fio']);
                    $schedule_already_been_created_packer++;
                }
            }

            if($schedule_already_been_created_packer == 0) {
                $url = $currentIndex . '&fozzy_staff_schedule_settings=1&add_person=2&nextweekfozzy_staff_schedule_packer&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee));
                Tools::redirectAdmin($url);
            } elseif ($schedule_already_been_created_packer == count($this_week_person['table_packerBox'])) {
                $output .= $this->displayError($this->l('График для этих упаковщиков на следующую неделю уже был создан.'));
            } else {
                $output .= $this->displayInformation(implode(", ", $schedule_already_been_created_packer_name) . $this->l(' - для этих упаковщиков на следующую неделю уже был создан график.'));
            }
        }

        if (Tools::isSubmit('submitBulkScheduleNextWeektable_driver')) {
            $this_week_person = $_POST;
            $schedule_already_been_created_driver = 0;
            $schedule_already_been_created_driver_name = array();
            for ($i = 0; $i < count($this_week_person['table_driverBox']); $i++) {
                $this_week = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_driver` WHERE `id_person` = " . $this_week_person['table_driverBox'][$i]);
                $person_week_number = date('W') + 1;
                $person_year_number = date('Y');

                $sql_question  = "SELECT *  FROM `" . _DB_PREFIX_ . "staff_schedule_driver`";
                $sql_question  .= ' LEFT JOIN `ps_fozzy_logistic_vodila` ON `ps_fozzy_logistic_vodila`.`id_vodila` = `ps_staff_schedule_driver`.`driver_name_person`';
                $sql_question  .= " WHERE `week_number_driver` = " . $person_week_number;
                $sql_question  .= " AND `fillial_name_driver` = " . $this_week[0]['fillial_name_driver'];
                $sql_question  .= " AND `tab_number_driver` = " . $this_week[0]['tab_number_driver'];
                $next_week = Db::getInstance()->executeS($sql_question);

                if(empty($next_week) || $this_week[0]['tab_number_driver'] == 0) {
                    $sql_next_week_driver = "INSERT INTO `"._DB_PREFIX_."staff_schedule_driver` (`week_number_driver`, `year_number_driver`, `shop_name_driver`, `fillial_name_driver`, `persone_role`, `tab_number_driver`, `driver_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn_1`, `day_pn_2`, `day_pn_3`, `day_vt_1`, `day_vt_2`, `day_vt_3`, `day_sr_1`, `day_sr_2`, `day_sr_3`, `day_cht_1`, `day_cht_2`, `day_cht_3`, `day_pt_1`, `day_pt_2`, `day_pt_3`, `day_sb_1`, `day_sb_2`, `day_sb_3`, `day_vs_1`, `day_vs_2`, `day_vs_3`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('".$person_week_number."','".$person_year_number."','".$this_week[0]['shop_name_driver']."','".$this_week[0]['fillial_name_driver']."','".$this_week[0]['persone_role']."','".$this_week[0]['tab_number_driver']."','".$this_week[0]['driver_name_person']."','".$this_week[0]['norm_day']."','".$this_week[0]['start_work']."','".$this_week[0]['end_work']."','".$this_week[0]['time_work']."','".$this_week[0]['day_pn_1']."','".$this_week[0]['day_pn_2']."','".$this_week[0]['day_pn_3']."','".$this_week[0]['day_vt_1']."','".$this_week[0]['day_vt_2']."','".$this_week[0]['day_vt_3']."','".$this_week[0]['day_sr_1']."','".$this_week[0]['day_sr_2']."','".$this_week[0]['day_sr_3']."','".$this_week[0]['day_cht_1']."','".$this_week[0]['day_cht_2']."','".$this_week[0]['day_cht_3']."','".$this_week[0]['day_pt_1']."','".$this_week[0]['day_pt_2']."','".$this_week[0]['day_pt_3']."','".$this_week[0]['day_sb_1']."','".$this_week[0]['day_sb_2']."','".$this_week[0]['day_sb_3']."','".$this_week[0]['day_vs_1']."','".$this_week[0]['day_vs_2']."','".$this_week[0]['day_vs_3']."','".$this_week[0]['week_time_all']."','".$this_week[0]['week_work_day_all']."','".$this_week[0]['week_output_day_all']."','".$this_week[0]['comment_person']."')";
                    Db::getInstance()->execute($sql_next_week_driver);
                } else {
                    array_push($schedule_already_been_created_driver_name, $next_week[0]['fio']);
                    $schedule_already_been_created_driver++;
                }
            }

            if($schedule_already_been_created_driver == 0) {
                $url = $currentIndex . '&fozzy_staff_schedule_settings=1&nextweekfozzy_staff_schedule_driver&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee));
                Tools::redirectAdmin($url);
            } elseif ($schedule_already_been_created_driver == count($this_week_person['table_driverBox'])) {
                $output .= $this->displayError($this->l('График для этих водителей на следующую неделю уже был создан.'));
            } else {
                $output .= $this->displayInformation(implode(", ", $schedule_already_been_created_driver_name) . $this->l(' - для этих водителей на следующую неделю уже был создан график.'));
            }
        }

        if (Tools::isSubmit('submitBulkScheduleNextWeektable_manager')) {
            $this_week_person = $_POST;
            $schedule_already_been_created_manager = 0;
            $schedule_already_been_created_manager_name = array();
            for ($i = 0; $i < count($this_week_person['table_managerBox']); $i++) {
                $this_week = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "staff_schedule_manager` WHERE `id_person` = " . $this_week_person['table_managerBox'][$i]);
                $person_week_number = date('W') + 1;
                $person_year_number = date('Y');

                $sql_question  = "SELECT *  FROM `" . _DB_PREFIX_ . "staff_schedule_manager`";
                $sql_question  .= ' LEFT JOIN `ps_fozzy_logistic_manager` ON `ps_fozzy_logistic_manager`.`id_manager` = `ps_staff_schedule_manager`.`manager_name_person`';
                $sql_question  .= " WHERE `week_number_manager` = " . $person_week_number;
                $sql_question  .= " AND `fillial_name_manager` = " . $this_week[0]['fillial_name_manager'];
                $sql_question  .= " AND `tab_number_manager` = " . $this_week[0]['tab_number_manager'];
                $next_week = Db::getInstance()->executeS($sql_question);

                if(empty($next_week) || $this_week[0]['tab_number_manager'] == 0) {
                    $sql_next_week_manager = "INSERT INTO `" . _DB_PREFIX_ . "staff_schedule_manager` (`week_number_manager`, `year_number_manager`, `shop_name_manager`, `fillial_name_manager`, `persone_role`, `tab_number_manager`, `manager_name_person`, `norm_day`, `start_work`, `end_work`, `time_work`, `day_pn`, `day_vt`, `day_sr`, `day_cht`, `day_pt`, `day_sb`, `day_vs`, `week_time_all`, `week_work_day_all`, `week_output_day_all`, `comment_person`) VALUES ('".$person_week_number."','".$person_year_number."','".$this_week[0]['shop_name_manager']."','".$this_week[0]['fillial_name_manager']."','".$this_week[0]['persone_role']."','".$this_week[0]['tab_number_manager']."','".$this_week[0]['manager_name_person']."','".$this_week[0]['norm_day']."','".$this_week[0]['start_work']."','".$this_week[0]['end_work']."','".$this_week[0]['time_work']."','".$this_week[0]['day_pn']."','".$this_week[0]['day_vt']."','".$this_week[0]['day_sr']."','".$this_week[0]['day_cht']."','".$this_week[0]['day_pt']."','".$this_week[0]['day_sb']."','".$this_week[0]['day_vs']."','".$this_week[0]['week_time_all']."','".$this_week[0]['week_work_day_all']."','".$this_week[0]['week_output_day_all']."','".$this_week[0]['comment_person']."')";
                    Db::getInstance()->execute($sql_next_week_manager);
                } else {
                    array_push($schedule_already_been_created_manager_name, $next_week[0]['fio']);
                    $schedule_already_been_created_manager++;
                }
            }

            if($schedule_already_been_created_manager == 0) {
                $url = $currentIndex . '&fozzy_staff_schedule_settings=1&add_person=4&nextweekfozzy_staff_schedule_manager&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee));
                Tools::redirectAdmin($url);
            } elseif ($schedule_already_been_created_manager == count($this_week_person['table_managerBox'])) {
                $output .= $this->displayError($this->l('График для этих менеджеров на следующую неделю уже был создан.'));
            } else {
                $output .= $this->displayInformation(implode(", ", $schedule_already_been_created_manager_name) . $this->l(' - для этих менеджеров на следующую неделю уже был создан график.'));
            }
        }

        /**
         * Массовое удаление графика сотрудников.
         */
        if (Tools::isSubmit('submitBulkDeleteSchedulePersontable_picker')){
            $this_delete_persons = $_POST;
            $week_number_picker = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_picker`.`week_number_picker` FROM `'._DB_PREFIX_.'staff_schedule_picker` WHERE `id_person` = '.$this_delete_persons['table_pickerBox'][0]);
            if($week_number_picker > date('W')) {
                for ($i = 0; $i < count($this_delete_persons['table_pickerBox']); $i++) {
                    Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."staff_schedule_picker` WHERE `id_person` = ".$this_delete_persons['table_pickerBox'][$i]);
                }
                $output .= $this->displayConfirmation($this->l('The collector schedule has been removed.'));
            } else {
                $output .= $this->displayWarning($this->l('График сборщиков можно удалить только с выше текущей недели.'));
            }
        }

        if (Tools::isSubmit('submitBulkDeleteSchedulePersontable_packer')) {
            $this_delete_persons = $_POST;
            $week_number_packer = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_packer`.`week_number_packer` FROM `'._DB_PREFIX_.'staff_schedule_packer` WHERE `id_person` = '.$this_delete_persons['table_packerBox'][0]);
            if($week_number_packer > date('W')) {
                for ($i = 0; $i < count($this_delete_persons['table_packerBox']); $i++) {
                    Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."staff_schedule_packer` WHERE `id_person` = ".$this_delete_persons['table_packerBox'][$i]);
                }
                $output .= $this->displayConfirmation($this->l('The packer schedule has been removed.'));
            } else {
                $output .= $this->displayWarning($this->l('График упаковщиков можно удалить только с выше текущей недели.'));
            }
        }

        if (Tools::isSubmit('submitBulkDeleteSchedulePersontable_driver')) {
            $this_delete_persons = $_POST;
            $week_number_driver = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_driver`.`week_number_driver` FROM `'._DB_PREFIX_.'staff_schedule_driver` WHERE `id_person` = '.$this_delete_persons['table_driverBox'][0]);
            if($week_number_driver > date('W')) {
                for ($i = 0; $i < count($this_delete_persons['table_driverBox']); $i++) {
                    Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."staff_schedule_driver` WHERE `id_person` = ".$this_delete_persons['table_driverBox'][$i]);
                }
                $output .= '<script>fozzy_staff_schedule_init_tabs();</script>';
                $output .= $this->displayConfirmation($this->l('The drivers schedule has been removed.'));
            } else {
                $output .= '<script>fozzy_staff_schedule_init_tabs();</script>';
                $output .= $this->displayWarning($this->l('График водителей можно удалить только с выше текущей недели.'));
            }
        }

        if (Tools::isSubmit('submitBulkDeleteSchedulePersontable_manager')) {
            $this_delete_persons = $_POST;
            $week_number_manager = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_manager`.`week_number_manager` FROM `'._DB_PREFIX_.'staff_schedule_manager` WHERE `id_person` = '.$this_delete_persons['table_managerBox'][0]);
            if($week_number_manager > date('W')) {
                for ($i = 0; $i < count($this_delete_persons['table_managerBox']); $i++) {
                    Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."staff_schedule_manager` WHERE `id_person` = ".$this_delete_persons['table_managerBox'][$i]);
                }
                $output .= $this->displayConfirmation($this->l('The managers schedule has been removed.'));
            } else {
                $output .= $this->displayWarning($this->l('График менеджеров можно удалить только с выше текущей недели.'));
            }
        }

        /**
         * Удаление и редактирование сотрудников.
         */
        $persona = array();
        $persona_driver = array();
        if (isset($id_picker) && $id_picker > 0) {
            if (Tools::isSubmit('deletetable_picker')) {
                $week_number_picker = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_picker`.`week_number_picker` FROM `'._DB_PREFIX_.'staff_schedule_picker` WHERE `id_person` = '.$id_picker);
                if ($week_number_picker > date('W')) {
                    Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."staff_schedule_picker` WHERE `id_person` = ".$id_picker);
                    $output .= $this->displayConfirmation($this->l('The collector schedule has been removed.'));
                } else {
                    $output .= $this->displayWarning($this->l('График сборщика можно удалить только с выше текущей недели.'));
                }
            }
            if ($edit){
                $persona = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'staff_schedule_picker` WHERE `id_person` = '.$id_picker);
            }
        }

        if(isset($id_packer) && $id_packer > 0) {
            if (Tools::isSubmit('deletetable_packer')) {
                $week_number_packer = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_packer`.`week_number_packer` FROM `'._DB_PREFIX_.'staff_schedule_packer` WHERE `id_person` = '.$id_packer);
                if ($week_number_packer > date('W')) {
                    Db::getInstance()->execute("DELETE FROM `" . _DB_PREFIX_ . "staff_schedule_packer` WHERE `id_person` = " . $id_packer);
                    $output .= $this->displayConfirmation($this->l('The packer schedule has been removed.'));
                } else {
                    $output .= $this->displayWarning($this->l('График упаковщика можно удалить только с выше текущей недели.'));
                }
            }
            if ($edit){
                $persona = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'staff_schedule_packer` WHERE `id_person` = '.$id_packer);
            }
        }

        if(isset($id_driver) && $id_driver > 0) {
            if (Tools::isSubmit('deletetable_driver')) {
                $week_number_driver = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_driver`.`week_number_driver` FROM `'._DB_PREFIX_.'staff_schedule_driver` WHERE `id_person` = '.$id_driver);
                if ($week_number_driver > date('W')) {
                    Db::getInstance()->execute("DELETE FROM `" . _DB_PREFIX_ . "staff_schedule_driver` WHERE `id_person` = " . $id_driver);
                    $output .= '<script>fozzy_staff_schedule_init_tabs();</script>';
                    $output .= $this->displayConfirmation($this->l('The drivers schedule has been removed.'));
                } else {
                    $output .= '<script>fozzy_staff_schedule_init_tabs();</script>';
                    $output .= $this->displayWarning($this->l('График водителя можно удалить только с выше текущей недели.'));
                }
            }
            if ($edit_driver){
                $persona_driver = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'staff_schedule_driver` WHERE `id_person` = '.$id_driver);
            }
        }

        if(isset($id_manager) && $id_manager > 0) {
            if (Tools::isSubmit('deletetable_manager')) {
                $week_number_manager = Db::getInstance()->getValue('SELECT `'._DB_PREFIX_.'staff_schedule_manager`.`week_number_manager` FROM `'._DB_PREFIX_.'staff_schedule_manager` WHERE `id_person` = '.$id_manager);
                if ($week_number_manager > date('W')) {
                    Db::getInstance()->execute("DELETE FROM `" . _DB_PREFIX_ . "staff_schedule_manager` WHERE `id_person` = " . $id_manager);
                    $output .= $this->displayConfirmation($this->l('The managers schedule has been removed.'));
                } else {
                    $output .= $this->displayWarning($this->l('График менеджера можно удалить только с выше текущей недели.'));
                }
            }
            if ($edit){
                $persona = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'staff_schedule_manager` WHERE `id_person` = '.$id_manager);
            }
        }

        if(Tools::isSubmit('submitFiltertable_driver')) {
            $output .= '<script>fozzy_staff_schedule_init_tabs();</script>';
        }

        /**
         * Обнуление фильтров.
         */
        if (Tools::isSubmit('submitResettable_picker')) {
            foreach($_POST as $name_filter => $value_filter) {
              if (strpos($name_filter, 'table_pickerFilter_') !== false)
                $_POST[$name_filter] = NULL;
            }
            unset($this->context->cookie->activeFilterPicker);
        } elseif (Tools::isSubmit('submitResettable_packer')) {
            foreach($_POST as $name_filter => $value_filter) {
              if (strpos($name_filter, 'table_packerFilter_') !== false)
                $_POST[$name_filter] = NULL;
            }
            unset($this->context->cookie->activeFilterPacker);
        } elseif (Tools::isSubmit('submitResettable_manager')) {
            foreach($_POST as $name_filter => $value_filter) {
              if (strpos($name_filter, 'table_managerFilter_') !== false)
                $_POST[$name_filter] = NULL;
            }
            unset($this->context->cookie->activeFilterManager);
        } elseif (Tools::isSubmit('submitResettable_driver')) {
            foreach($_POST as $name_filter => $value_filter) {
              if (strpos($name_filter, 'table_driverFilter_') !== false)
                $_POST[$name_filter] = NULL;
            }
            unset($this->context->cookie->activeFilterDriver);
        }

        /**
         * Редиректы с выводом уведомлений.
         */
        $fozzy_staff_schedule_settings = Tools::getValue("fozzy_staff_schedule_settings");
        if (Tools::strlen($fozzy_staff_schedule_settings) > 0) {
            if(Tools::getValue("add_person") == 1) {
                if(Tools::isSubmit('nextweekfozzy_staff_schedule_picker')) {
                    $output .= $this->displayConfirmation($this->l('График на следующую неделю для сборщиков успешно создан.'));
                } elseif (Tools::isSubmit('copy_schedule_fozzy_staff_schedule')){
                    $output .= $this->displayError($this->l('График для этого сборщика на эту неделю уже был создан.'));
                } else {
                    $output .= $this->displayConfirmation($this->l('The schedule has been successfully added to the picker.'));
                }
            } elseif(Tools::getValue("add_person") == 2){
                if(Tools::isSubmit('nextweekfozzy_staff_schedule_packer')) {
                    $output .= $this->displayConfirmation($this->l('График на следующую неделю для упаковщиков успешно создан.'));
                } elseif (Tools::isSubmit('copy_schedule_fozzy_staff_schedule')){
                    $output .= $this->displayError($this->l('График для этого упаковщика на эту неделю уже был создан.'));
                } else {
                    $output .= $this->displayConfirmation($this->l('The schedule has been successfully added to the packer.'));
                }
            } elseif(Tools::getValue("add_person") == 4){
                if(Tools::isSubmit('nextweekfozzy_staff_schedule_manager')) {
                    $output .= $this->displayConfirmation($this->l('График на следующую неделю для для менеджеров успешно создан.'));
                } elseif (Tools::isSubmit('copy_schedule_fozzy_staff_schedule')){
                    $output .= $this->displayError($this->l('График для этого менеджера на эту неделю уже был создан.'));
                } else {
                    $output .= $this->displayConfirmation($this->l('The schedule has been successfully added to the manager.'));
                }
            } else {
                $output .= '<script>fozzy_staff_schedule_init_tabs();</script>';
                if(Tools::isSubmit('updatefozzy_staff_schedule_driver')){
                    $output .= $this->displayConfirmation($this->l('Drivers schedule has been successfully updated.'));
                } elseif (Tools::isSubmit('nextweekfozzy_staff_schedule_driver')) {
                    $output .= $this->displayConfirmation($this->l('График на следующую неделю для водителя успешно создан.'));
                } elseif (Tools::isSubmit('copy_schedule_fozzy_staff_schedule_driver')){
                    $output .= $this->displayError($this->l('График для этого водителя на эту неделю уже был создан.'));
                } else {
                    $output .= $this->displayConfirmation($this->l('The drivers schedule has been successfully added.'));
                }
            }

        }

        /**
         * Экспорт списка сотрудников в csv файл.
         * Export a list of persone to a csv file.
         */
        if (Tools::isSubmit('btnExport')) {
            $stype = $_POST['fozzy_staff_schedule_stype'];
            $week_number = $_POST['fozzy_staff_schedule_week_number'];
            $response_csv_data = array();
            for ($i = 0; $i < count($_POST['fozzy_staff_schedule_shop']); $i++) {
                $shop = $_POST['fozzy_staff_schedule_shop'][$i];
                if($stype == 1) {
                    $table = 'staff_schedule_picker';
                    $suf = 'picker';
                    $lable = $this->l('Picker List');
                } elseif($stype == 2) {
                    $table = 'staff_schedule_packer';
                    $suf = 'packer';
                    $lable = $this->l('List of packers');
                } elseif ($stype == 3) {
                    $table = 'staff_schedule_driver';
                    $suf = 'driver';
                    $export_stype_driver = 3;
                    $lable = $this->l('List of drivers');
                } else {
                    $table = 'staff_schedule_manager';
                    $suf = 'manager';
                    $lable = $this->l('List of managers');
                }

                $response_csv_data[] = $this->getLinksExport($table, $suf, $week_number, $shop);
            }

            $response_csv_data_export = array();
            $j = 0;
            foreach ($response_csv_data as $value) {
                for ($i = 0; $i < count($value); $i++) {
                    $response_csv_data_export[$j] = $value[$i];
                    $j++;
                }
            }

            $filename = $lable;

            $this->export_data_to_csv($response_csv_data_export, $filename, $export_stype_driver);
            $output .= $this->displayConfirmation($this->l('Data export was successful.'));
        }

        //Media::addJsDef(array('ajax_path' => '/modules/fozzy_staff_schedule/controllers/admin/ajax.php'));
        $Kpi = WeeklyScheduleKpi::renderKpis();

        $output .= $Kpi;
        $output .= $this->_displayFormMenu($persona, $edit, $persona_driver, $edit_driver);
        return $output;
    }

    public function _displayFormMenu($persona = array(), $edit = 0, $persona_driver = array(), $edit_driver = 0) {
        $this->output = '';
        $this->output .= '<div class="row"><div class="col-lg-12"><div class="row">';
        $this->output .= '<div class="col-lg-12 col-md-3"><div class="list-group">';
        $this->output .= '<ul class="nav nav-pills" id="navtabs16">
							    <li class="active"><a href="#graphsall" data-toggle="tab" class="list-group-item"><i class="icon-bar-chart fa-lg"></i>&nbsp;'.$this->l('Employee schedules').'</a></li>
							    <li><a href="#graphsdriver" data-toggle="tab" class="list-group-item"><i class="icon-bar-chart fa-lg"></i>&nbsp;'.$this->l('Drivers schedule').'</a></li>
							    <li><a href="#graphsexport" data-toggle="tab" class="list-group-item"><i class="icon-file-excel-o fa-lg"></i> '.$this->l('Export employee schedule').'</a></li>
							</ul>';
        $this->output .= '</div></div>';
        $this->output .= '<div class="tab-content col-lg-12 col-md-9">';
        $this->output .= '<div class="tab-pane active" id="graphsall">'.$this->renderAddForm($persona, $edit).' '.$this->renderScheduleListPicker().' '.$this->renderScheduleListPacker().' '.$this->renderScheduleListManager().'</div>';
        $this->output .= '<div class="tab-pane" id="graphsdriver">'.$this->renderAddFormDriver($persona_driver, $edit_driver).' '.$this->renderScheduleListDriver().'</div>';
        $this->output .= '<div class="tab-pane" id="graphsexport">'.$this->renderButtonForm().'</div>';
        $this->output .= '</div>';
        $this->output .= '</div></div></div>';
        return $this->output;
    }

    /**
     * Форма для составления графика сотруднику.
     * Schedule form for employee.
     * @return string
     * @throws PrestaShopException
     * где она зарускается
     */
    private function renderAddForm($persona = 0, $edit = 0) {
        $title = $this->l('Add schedule to employee');
        $title_button = $this->l('Add schedule to employee button');
        $icon = 'icon-plus-sign-alt';

        $personal_types = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "fozzy_logistic_role` WHERE `id_role` != 3");
        $personal_fio_picker = Db::getInstance()->executeS("SELECT `ps_fozzy_logistic_sborshik`.*, `ps_fozzy_logistic_sborshik`.`id_sborshik` as `id_person` FROM `" . _DB_PREFIX_ . "fozzy_logistic_sborshik` WHERE `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC");
        $personal_fio_packer = Db::getInstance()->executeS("SELECT `ps_fozzy_logistic_packer`.*, `ps_fozzy_logistic_packer`.`id_packer` as `id_person` FROM `" . _DB_PREFIX_ . "fozzy_logistic_packer` WHERE `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC");
        $personal_fio_manager = Db::getInstance()->executeS("SELECT `ps_fozzy_logistic_manager`.*, `ps_fozzy_logistic_manager`.`id_manager` as `id_person` FROM `" . _DB_PREFIX_ . "fozzy_logistic_manager` WHERE `active` = 1 AND `deleted` = 0 ORDER BY `fio` ASC");
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` WHERE 1 ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shoplist = Db::getInstance()->executeS($sql);

        if ($edit) {
            $title = $this->l('Edit a person');
            $title_button = $this->l('Edit a person button');
            $icon = 'icon-cog';
        }

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => $icon
                ),

                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'fozzy_staff_schedule_id_person',
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'name' => 'fozzy_staff_schedule_stype',
                        'label' => $this->l('Type of employee'),
                        'required' => true,
                        'options' => array(
                            'query' => $personal_types,
                            'id' => 'id_role',
                            'name' => 'role_name'
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'name' => 'fozzy_staff_schedule_shop',
                        'label' => $this->l('Shop name'),
                        'required' => true,
                        'options' => array(
                            'query' => $shoplist,
                            'id' => 'id_fillial',
                            'name' => 'shop_name'
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'id' => 'picker_personal',
                        'label' => $this->l('FIO picker'),
                        'name' => 'fozzy_staff_schedule_name_personal',
                        'multiple' => true,
                        'search' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $personal_fio_picker,
                            'id' => 'id_person',
                            'name' => 'fio',
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'id' => 'packer_personal',
                        'label' => $this->l('FIO packer'),
                        'name' => 'fozzy_staff_schedule_name_personal',
                        'multiple' => true,
                        'search' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $personal_fio_packer,
                            'id' => 'id_person',
                            'name' => 'fio',
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'id' => 'manager_personal',
                        'label' => $this->l('FIO manager'),
                        'name' => 'fozzy_staff_schedule_name_personal',
                        'multiple' => true,
                        'search' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $personal_fio_manager,
                            'id' => 'id_person',
                            'name' => 'fio',
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('Week number'),
                        'name' => 'fozzy_staff_schedule_week_number',
                        'desc' => $this->l('The number of the week for which you want to create a schedule for employees.'),
                        'required' => true,
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('Norma day'),
                        'name' => 'fozzy_staff_schedule_norma_day',
                        'desc' => $this->l('Daily rate.'),
                        'required' => true
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('Start work'),
                        'name' => 'fozzy_staff_schedule_start_work',
                        'desc' => $this->l('The begining of the work day.'),
                        'required' => true
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('End work'),
                        'name' => 'fozzy_staff_schedule_end_work',
                        'desc' => $this->l('The end of the working day.'),
                        'required' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Monday'),
                        'name' => 'fozzy_staff_schedule_active_monday',
                        'desc' => $this->l('Monday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Tuesday'),
                        'name' => 'fozzy_staff_schedule_active_tuesday',
                        'desc' => $this->l('Tuesday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Wednesday'),
                        'name' => 'fozzy_staff_schedule_active_wednesday',
                        'desc' => $this->l('Wednesday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Thursday'),
                        'name' => 'fozzy_staff_schedule_active_thursday',
                        'desc' => $this->l('Thursday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Friday'),
                        'name' => 'fozzy_staff_schedule_active_friday',
                        'desc' => $this->l('Friday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Saturday'),
                        'name' => 'fozzy_staff_schedule_active_saturday',
                        'desc' => $this->l('Saturday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Sunday'),
                        'name' => 'fozzy_staff_schedule_active_sunday',
                        'desc' => $this->l('Sunday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 4,
                        'rows' => 6,
                        'type' => 'text',
                        'label' => $this->l('Comment'),
                        'name' => 'fozzy_staff_schedule_to_comment',
                        'desc' => $this->l('Comment on the person.')
                    ),
                ),

                'submit' => array(
                    'title' => $title_button,
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->identifier = $this->identifier;
        if (!$edit)
            $helper->submit_action = 'submitLinkAdd';
        else
            $helper->submit_action = 'submitLinkEdit';

        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues($persona)
        );
        return $helper->generateForm(array($fields_form_1));
    }

    /**
     * Форма для составления графика водителям.
     * Schedule form for employee.
     * @return string
     * @throws PrestaShopException
     */
    private function renderAddFormDriver($persona_driver = array(), $edit_driver = 0) {
        $title = $this->l('Add schedule to drive');
        $title_button = $this->l('Add schedule to employee button');
        $icon = 'icon-plus-sign-alt';

        $personal_types = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "fozzy_logistic_role` WHERE `id_role` = 3");
        $personal_fio_driver = Db::getInstance()->executeS("SELECT `ps_fozzy_logistic_vodila`.*, `ps_fozzy_logistic_vodila`.`id_vodila` as `id_person` FROM `" . _DB_PREFIX_ . "fozzy_logistic_vodila` WHERE `active` = 1 AND `deleted` = 0 ORDER BY `id_vodila` ASC");
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` WHERE 1 ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shoplist = Db::getInstance()->executeS($sql);

        if ($edit_driver) {
            $title = $this->l('Edit a person drive');
            $title_button = $this->l('Edit a person button');
            $icon = 'icon-cog';
        }

        $fields_form_2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => $icon
                ),

                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'fozzy_staff_schedule_driver_id_person',
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'name' => 'fozzy_staff_schedule_driver_stype',
                        'label' => $this->l('Type of employee'),
                        'required' => true,
                        'options' => array(
                            'query' => $personal_types,
                            'id' => 'id_role',
                            'name' => 'role_name'
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'name' => 'fozzy_staff_schedule_driver_shop',
                        'label' => $this->l('Shop name'),
                        'required' => true,
                        'options' => array(
                            'query' => $shoplist,
                            'id' => 'id_fillial',
                            'name' => 'shop_name'
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'select',
                        'label' => $this->l('FIO driver'),
                        'id' => 'driver_personal',
                        'name' => 'fozzy_staff_schedule_driver_name_personal',
                        'multiple' => true,
                        'search' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $personal_fio_driver,
                            'id' => 'id_person',
                            'name' => 'fio',
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('Week number driver'),
                        'name' => 'fozzy_staff_schedule_driver_week_number',
                        'desc' => $this->l('The number of the week for which you want to create a schedule for employees.'),
                        'required' => true,
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('Norma day driver'),
                        'name' => 'fozzy_staff_schedule_driver_norma_day',
                        'desc' => $this->l('Daily rate.'),
                        'required' => true
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('Start work'),
                        'name' => 'fozzy_staff_schedule_driver_start_work',
                        'desc' => $this->l('The begining of the work day.'),
                        'required' => true
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'label' => $this->l('End work'),
                        'name' => 'fozzy_staff_schedule_driver_end_work',
                        'desc' => $this->l('The end of the working day.'),
                        'required' => true
                    ),

                    // Три волны понедельника.
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Monday 10:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_monday_1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Monday 13:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_monday_2',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Monday 17:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_monday_3',
                        'desc' => $this->l('Monday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    // Три волны вторника.
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Tuesday 10:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_tuesday_1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Tuesday 13:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_tuesday_2',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Tuesday 17:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_tuesday_3',
                        'desc' => $this->l('Tuesday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    // Три волны среды.
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Wednesday 10:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_wednesday_1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Wednesday 13:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_wednesday_2',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Wednesday 17:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_wednesday_3',
                        'desc' => $this->l('Wednesday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    // Три волны четверга.
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Thursday 10:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_thursday_1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Thursday 13:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_thursday_2',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Thursday 17:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_thursday_3',
                        'desc' => $this->l('Thursday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    // Три волны пятници.
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Friday 10:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_friday_1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Friday 13:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_friday_2',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Friday 17:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_friday_3',
                        'desc' => $this->l('Friday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    // Три волны субботы.
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Saturday 10:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_saturday_1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Saturday 13:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_saturday_2',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Saturday 17:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_saturday_3',
                        'desc' => $this->l('Saturday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    // Три волны воскресенья
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Sunday 10:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_sunday_1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Sunday 13:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_sunday_2',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Sunday 17:00'),
                        'name' => 'fozzy_staff_schedule_driver_active_sunday_3',
                        'desc' => $this->l('Sunday day.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 4,
                        'rows' => 6,
                        'type' => 'text',
                        'label' => $this->l('Comment'),
                        'name' => 'fozzy_staff_schedule_driver_to_comment',
                        'desc' => $this->l('Comment on the person.')
                    ),
                ),

                'submit' => array(
                    'title' => $title_button,
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->identifier = $this->identifier;
        if (!$edit_driver)
            $helper->submit_action = 'submitLinkAddDriver';
        else
            $helper->submit_action = 'submitLinkEditDriver';

        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValuesDriver($persona_driver)
        );
        return $helper->generateForm(array($fields_form_2));
    }

    /**
     * Функция отображения недельного графика "Сборщиков".
     * @return bool|string
     */
    public function renderScheduleListPicker() {
        /**
         * Получение списка магазинов в зависимости от  $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` ";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop;
        } else {
            $sql .= " WHERE 1 ";
        }
        $sql .= " ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shop_name = Db::getInstance()->executeS($sql);

        $shop_list = array();
        foreach ($shop_name as $shoplist) {
            if($shoplist['id_fillial'] != 40) {
                $shop_list[$shoplist['id_fillial']] = $shoplist['shop_name'];
            }
        }

        /**
         * Получение списка сотрудников в зависимости от $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_sborshik`.`id_sborshik`, `ps_fozzy_logistic_sborshik`.`fio` FROM `" . _DB_PREFIX_ . "fozzy_logistic_sborshik`";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `active` = 1 AND `deleted` = 0";
        } else {
            $sql .= " WHERE `active` = 1 AND `deleted` = 0";
        }
        $sql .= " ORDER BY `id_sborshik` ASC";
        $personal_fio_picker = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_picker as $value) {
            $personal_fio[$value['id_sborshik']] =  $value['fio'];
        }

        $fields_list_picker = array(
            'id_person' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_number_picker' => array(
                'title' => $this->l('Week number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'fillial_name_picker' => array(
                'title' => $this->l('Shop name'),
                'type' => 'select',
                'list' => $shop_list,
                'filter_key' => 'fillial_name_picker',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Role name'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tab_number_picker' => array(
                'title' => $this->l('Tab number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'picker_name_person' => array(
                'title' => $this->l('FIO'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter_key' => 'picker_name_person',
                'class' => 'fixed-width-xs',
            ),
            'norm_day' => array(
                'title' => $this->l('Working norm'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'start_work' => array(
                'title' => $this->l('Start work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'end_work' => array(
                'title' => $this->l('End work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'time_work' => array(
                'title' => $this->l('Working hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pn' => array(
                'title' => $this->l('Pn'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vt' => array(
                'title' => $this->l('Vt'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sr' => array(
                'title' => $this->l('Sr'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_cht' => array(
                'title' => $this->l('Cht'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pt' => array(
                'title' => $this->l('Pt'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sb' => array(
                'title' => $this->l('Sb'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vs' => array(
                'title' => $this->l('Nd'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_time_all' => array(
                'title' => $this->l('Hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_work_day_all' => array(
                'title' => $this->l('Working day'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_output_day_all' => array(
                'title' => $this->l('Days off'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'comment_person' => array(
                'title' => $this->l('Сomment'),
                'type' => 'text',
                'search' => false,
                'class' => 'fixed-width-xs',
            ),
        );
        $helper = new HelperList();
        $helper->identifier = 'id_person';
        $helper->actions = array('edit','delete');
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->title = $this->l('List picker').' на ' . date('W') . ' неделю';
        $helper->table = 'table_picker';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&id_picker=1';
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getPicker($_POST);
        $helper->listTotal = count($content);
        $helper->bulk_actions = array(
            'ScheduleNextWeek' => array('text' => $this->l('График на следующую неделю'), 'icon' => 'icon-copy'),
            'DeleteSchedulePerson' => array('text' => $this->l('Удалить отмеченных сотрудников'), 'icon' => 'icon-trash'),
        );

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        return $helper->generateList($content, $fields_list_picker);
    }

    /**
     * Функция пагинации таблиц.
     * @param $content
     * @param int $page
     * @param int $pagination
     * @return array
     */
    public function paginate_content($content, $page = 1, $pagination = 10) {
        if( count($content) > $pagination) {
            $content = array_slice($content, $pagination * ($page - 1), $pagination);
        }

        return $content;
    }

    /**
     * Получение списка графика сборщиков в таблицу.
     * @return array|false|mysqli_result|PDOStatement|resource|null
     * @throws PrestaShopDatabaseException
     */
    public function getPicker($filter = array()) {
        if(Tools::isSubmit('submitFiltertable_picker')) {
            $this->context->cookie->activeFilterPicker = '';
            $this->context->cookie->activeFilterPicker = serialize($filter);
        }
        $sql = 'SELECT `ps_staff_schedule_picker`.*, `ps_fozzy_logistic_role`.`role_name`, `ps_fozzy_logistic_shop`.shop_name as `fillial_name_picker`, `ps_fozzy_logistic_sborshik`.`fio` as `picker_name_person` FROM `'._DB_PREFIX_.'staff_schedule_picker` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_staff_schedule_picker`.`persone_role`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_staff_schedule_picker`.`fillial_name_picker`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_sborshik` ON `ps_fozzy_logistic_sborshik`.`id_sborshik` = `ps_staff_schedule_picker`.`picker_name_person`';

        $data = unserialize($this->context->cookie->activeFilterPicker);
        if(!empty($data)) {
            $filter = $_POST = $data;
        }
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_picker') && !array_key_exists('submitResettable_picker', $filter)) {
            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_pickerFilter_') !== false) {
                    $name_filter = str_replace('table_pickerFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `$name_filter` LIKE '%$value_filter%'";
                    }
                    else {
                        if($name_filter == 'week_number_picker' && empty($value_filter)) {
                            $sql .= " AND `$name_filter` LIKE '%".date('W')."%'";
                        }
                        elseif ($name_filter == 'fillial_name_picker') {
                            $sql .= " AND `$name_filter` LIKE '%$value_filter'";
                            if (empty($value_filter) && $this->id_shop != 0) {
                                $sql .= " AND `shop_name_picker` LIKE '%$this->id_shop%'";
                            }
                        }
                        else {
                            $sql .= " AND `$name_filter` LIKE '%$value_filter%'";
                        }
                    }
                    $i++;
                }
            }
        } else {
            $sql .= ' WHERE `week_number_picker`= '. date('W');
            if($this->id_shop != 0) {
                $sql .= ' AND `shop_name_picker` = ' . $this->id_shop;
            }
        }

        if(!empty(Tools::GetValue('table_pickerOrderby')))
            $sql .= ' ORDER BY `ps_staff_schedule_picker`.`'.Tools::GetValue('table_pickerOrderby').'` '.Tools::GetValue('table_pickerOrderway');
        else
            $sql .= ' ORDER BY `id_person` ASC';

        $links = Db::getInstance()->executeS($sql);

        return $links;
    }

    /**
     * Функция отображения недельного графика "Упаковщиков".
     * @return string
     */
    public function renderScheduleListPacker() {
        /**
         * Получение списка магазинов в зависимости от  $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` ";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop;
        } else {
            $sql .= " WHERE 1 ";
        }
        $sql .= " ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shop_name = Db::getInstance()->executeS($sql);

        $shop_list = array();
        foreach ($shop_name as $shoplist) {
            if($shoplist['id_fillial'] != 40) {
                $shop_list[$shoplist['id_fillial']] = $shoplist['shop_name'];
            }
        }

        /**
         * Получение списка сотрудников в зависимости от $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_packer`.`id_packer`, `ps_fozzy_logistic_packer`.`fio` FROM `" . _DB_PREFIX_ . "fozzy_logistic_packer`";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `active` = 1 AND `deleted` = 0";
        } else {
            $sql .= " WHERE `active` = 1 AND `deleted` = 0";
        }
        $sql .= " ORDER BY `id_packer` ASC";
        $personal_fio_packer = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_packer as $value) {
            $personal_fio[$value['id_packer']] =  $value['fio'];
        }

        $fields_list_packer = array(
            'id_person' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_number_packer' => array(
                'title' => $this->l('Week number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'fillial_name_packer' => array(
                'title' => $this->l('Shop name'),
                'type' => 'select',
                'list' => $shop_list,
                'filter' => true,
                'filter_key' => 'fillial_name_packer',
                'multiple' => true,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Role name'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tab_number_packer' => array(
                'title' => $this->l('Tab number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'packer_name_person' => array(
                'title' => $this->l('FIO'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter' => true,
                'filter_key' => 'packer_name_person',
                'multiple' => true,
                'class' => 'fixed-width-xs',
            ),
            'norm_day' => array(
                'title' => $this->l('Working norm'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'start_work' => array(
                'title' => $this->l('Start work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'end_work' => array(
                'title' => $this->l('End work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'time_work' => array(
                'title' => $this->l('Working hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pn' => array(
                'title' => $this->l('Pn'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vt' => array(
                'title' => $this->l('Vt'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sr' => array(
                'title' => $this->l('Sr'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_cht' => array(
                'title' => $this->l('Cht'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pt' => array(
                'title' => $this->l('Pt'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sb' => array(
                'title' => $this->l('Sb'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vs' => array(
                'title' => $this->l('Nd'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_time_all' => array(
                'title' => $this->l('Hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_work_day_all' => array(
                'title' => $this->l('Working day'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_output_day_all' => array(
                'title' => $this->l('Days off'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'comment_person' => array(
                'title' => $this->l('Сomment'),
                'type' => 'text',
                'search' => false,
                'class' => 'fixed-width-xs',
            ),
        );

        $helper = new HelperList();
        $helper->identifier = 'id_person';
        $helper->actions = array('edit','delete');
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->title = $this->l('List packer').' на ' . date('W') . ' неделю';
        $helper->table = 'table_packer';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&id_packer=1';
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getPacker($_POST);
        $helper->listTotal = count($content);
        $helper->bulk_actions = array(
            'ScheduleNextWeek' => array('text' => $this->l('График на следующую неделю'), 'icon' => 'icon-copy'),
            'DeleteSchedulePerson' => array('text' => $this->l('Удалить отмеченных сотрудников'), 'icon' => 'icon-trash'),
        );

        /* Paginate the result */
        $page = ($page = Tools::getValue('submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        return $helper->generateList($content, $fields_list_packer);
    }

    /**
     * Получение графика упаковщиков в таблицу.
     * @return array|false|mysqli_result|PDOStatement|resource|null
     * @throws PrestaShopDatabaseException
     */
    public function getPacker($filter = array()) {
        if(Tools::isSubmit('submitFiltertable_packer')) {
            $this->context->cookie->activeFilterPacker = '';
            $this->context->cookie->activeFilterPacker = serialize($filter);
        }
        $sql = 'SELECT `ps_staff_schedule_packer`.*, `ps_fozzy_logistic_role`.role_name, `ps_fozzy_logistic_shop`.shop_name as fillial_name_packer, `ps_fozzy_logistic_packer`.`fio` as `packer_name_person` FROM `'._DB_PREFIX_.'staff_schedule_packer` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_staff_schedule_packer`.`persone_role`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_staff_schedule_packer`.`fillial_name_packer`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_packer` ON `ps_fozzy_logistic_packer`.`id_packer` = `ps_staff_schedule_packer`.`packer_name_person`';

        $data = unserialize($this->context->cookie->activeFilterPacker);
        if(!empty($data)) {
            $filter = $_POST = $data;
        }
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_packer') && !array_key_exists('submitResettable_packer', $filter)) {
            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_packerFilter_') !== false) {
                    $name_filter = str_replace('table_packerFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `$name_filter` LIKE '%$value_filter%'";
                    }
                    else {
                        if($name_filter == 'week_number_packer' && empty($value_filter)) {
                            $sql .= " AND `$name_filter` LIKE '%".date('W')."%'";
                        } elseif ($name_filter == 'fillial_name_packer') {
                            $sql .= " AND `$name_filter` LIKE '%$value_filter'";
                            if (empty($value_filter) && $this->id_shop != 0) {
                                $sql .= " AND `shop_name_packer` LIKE '%$this->id_shop%'";
                            }
                        }
                        else {
                            $sql .= " AND `$name_filter` LIKE '%$value_filter%'";
                        }
                    }
                    $i++;
                }
            }
        } else {
            $sql .= ' WHERE `week_number_packer`='. date('W');
            if($this->id_shop != 0) {
                $sql .= ' AND `shop_name_packer` = ' . $this->id_shop;
            }
        }

        if(!empty(Tools::GetValue('table_packerOrderby')))
            $sql .= ' ORDER BY `ps_staff_schedule_packer`.`'.Tools::GetValue('table_packerOrderby').'` '.Tools::GetValue('table_packerOrderway');
        else
            $sql .= ' ORDER BY `id_person` ASC';
        $links = Db::getInstance()->executeS($sql);

        return $links;
    }

    /**
     * Функция отображения недельного графика "Водителей".
     * @return string
     */
    public function renderScheduleListDriver() {
        /**
         * Получение списка магазинов в зависимости от  $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` ";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop;
        } else {
            $sql .= " WHERE 1 ";
        }
        $sql .= " ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shop_name = Db::getInstance()->executeS($sql);

        $shop_list = array();
        foreach ($shop_name as $shoplist) {
            if($shoplist['id_fillial'] != 40) {
                $shop_list[$shoplist['id_fillial']] = $shoplist['shop_name'];
            }
        }

        /**
         * Получение списка сотрудников в зависимости от $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_vodila`.`id_vodila`, `ps_fozzy_logistic_vodila`.`fio` FROM `" . _DB_PREFIX_ . "fozzy_logistic_vodila`";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `active` = 1 AND `deleted` = 0";
        } else {
            $sql .= " WHERE `active` = 1 AND `deleted` = 0";
        }
        $sql .= " ORDER BY `id_vodila` ASC";
        $personal_fio_driver = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_driver as $value) {
            $personal_fio[$value['id_vodila']] =  $value['fio'];
        }

        $fields_list_driver = array(
            'id_person' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_number_driver' => array(
                'title' => $this->l('Week number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'fillial_name_driver' => array(
                'title' => $this->l('Shop name'),
                'type' => 'select',
                'list' => $shop_list,
                'filter' => true,
                'filter_key' => 'fillial_name_driver',
                'multiple' => true,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Role name'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tab_number_driver' => array(
                'title' => $this->l('Tab number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'driver_name_person' => array(
                'title' => $this->l('FIO'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter' => true,
                'filter_key' => 'driver_name_person',
                'multiple' => true,
                'class' => 'fixed-width-xs',
            ),
            'norm_day' => array(
                'title' => $this->l('Working norm driver'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'start_work' => array(
                'title' => $this->l('Start work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'end_work' => array(
                'title' => $this->l('End work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'time_work' => array(
                'title' => $this->l('Working hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            // Три волны понедельника.
            'day_pn_1' => array(
                'title' => $this->l('Pn 10:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pn_2' => array(
                'title' => $this->l('Pn 13:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pn_3' => array(
                'title' => $this->l('Pn 17:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            // Три волны вторника.
            'day_vt_1' => array(
                'title' => $this->l('Vt 10:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vt_2' => array(
                'title' => $this->l('Vt 13:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vt_3' => array(
                'title' => $this->l('Vt 17:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            // Три волны среды.
            'day_sr_1' => array(
                'title' => $this->l('Ср 10:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sr_2' => array(
                'title' => $this->l('Ср 13:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sr_3' => array(
                'title' => $this->l('Ср 17:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            // Три волны четверга.
            'day_cht_1' => array(
                'title' => $this->l('Чт 10:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_cht_2' => array(
                'title' => $this->l('Чт 13:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_cht_3' => array(
                'title' => $this->l('Чт 17:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            // Три волны пятници.
            'day_pt_1' => array(
                'title' => $this->l('Пт 10:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pt_2' => array(
                'title' => $this->l('Пт 13:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pt_3' => array(
                'title' => $this->l('Пт 17:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            // Три волны субботы.
            'day_sb_1' => array(
                'title' => $this->l('Сб 10:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sb_2' => array(
                'title' => $this->l('Сб 13:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sb_3' => array(
                'title' => $this->l('Сб 17:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            // Три волны воскресенья.
            'day_vs_1' => array(
                'title' => $this->l('Вс 10:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vs_2' => array(
                'title' => $this->l('Вс 13:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vs_3' => array(
                'title' => $this->l('Вс 17:00'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),

            'week_time_all' => array(
                'title' => $this->l('Hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_work_day_all' => array(
                'title' => $this->l('Working day'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_output_day_all' => array(
                'title' => $this->l('Days off'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'comment_person' => array(
                'title' => $this->l('Сomment'),
                'type' => 'text',
                'search' => false,
                'class' => 'fixed-width-xs',
            ),
        );

        $helper = new HelperList();
        $helper->identifier = 'id_person';
        $helper->actions = array('edit','delete');
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->title = $this->l('List driver').' на ' . date('W') . ' неделю';
        $helper->table = 'table_driver';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&id_driver=1';
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getDriver($_POST);
        $helper->listTotal = count($content);
        $helper->bulk_actions = array(
            'ScheduleNextWeek' => array('text' => $this->l('График на следующую неделю'), 'icon' => 'icon-copy'),
            'DeleteSchedulePerson' => array('text' => $this->l('Удалить отмеченных сотрудников'), 'icon' => 'icon-trash'),
        );

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        return $helper->generateList($content, $fields_list_driver);
    }

    /**
     * Получение графика водителей в таблицу.
     * @return array|false|mysqli_result|PDOStatement|resource|null
     * @throws PrestaShopDatabaseException
     */
    public function getDriver($filter = array()) {
        if(Tools::isSubmit('submitFiltertable_driver')) {
            $this->context->cookie->activeFilterDriver = '';
            $this->context->cookie->activeFilterDriver = serialize($filter);
        }
        $sql = 'SELECT `ps_staff_schedule_driver`.*, `ps_fozzy_logistic_role`.`role_name`, `ps_fozzy_logistic_shop`.`shop_name` as `fillial_name_driver`, `ps_fozzy_logistic_vodila`.`fio` as `driver_name_person` FROM `'._DB_PREFIX_.'staff_schedule_driver` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_staff_schedule_driver`.`persone_role`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_staff_schedule_driver`.`fillial_name_driver`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_vodila` ON `ps_fozzy_logistic_vodila`.`id_vodila` = `ps_staff_schedule_driver`.`driver_name_person`';

        $data = unserialize($this->context->cookie->activeFilterDriver);
        if(!empty($data)) {
            $filter = $_POST = $data;
        }
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_driver') && !array_key_exists('submitResettable_driver', $filter)) {            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_driverFilter_') !== false) {
                    $name_filter = str_replace('table_driverFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `$name_filter` LIKE '%$value_filter%'";
                    }
                    else {
                        if($name_filter == 'week_number_driver' && empty($value_filter)) {
                            $sql .= " AND `$name_filter` LIKE '%".date('W')."%'";
                        } elseif ($name_filter == 'fillial_name_driver') {
                            $sql .= " AND `$name_filter` LIKE '%$value_filter'";
                            if (empty($value_filter) && $this->id_shop != 0) {
                                $sql .= " AND `shop_name_driver` LIKE '%$this->id_shop%'";
                            }
                        } else
                            $sql .= " AND `$name_filter` LIKE '%$value_filter%'";
                    }
                    $i++;
                }
            }
        } else {
            $sql .= ' WHERE `week_number_driver`='. date('W');
            if($this->id_shop != 0) {
                $sql .= ' AND `shop_name_driver` = ' . $this->id_shop;
            }
        }

        if(!empty(Tools::GetValue('table_driverOrderby')))
            $sql .= ' ORDER BY `ps_staff_schedule_driver`.`'.Tools::GetValue('table_driverOrderby').'` '.Tools::GetValue('table_driverOrderway');
        else
            $sql .= ' ORDER BY `id_person` ASC';

        $links = Db::getInstance()->executeS($sql);

        return $links;
    }

    /**
     * Функция отображения недельного графика "Менеджеров".
     * @return bool|string
     */
    public function renderScheduleListManager() {
        /**
         * Получение списка магазинов в зависимости от  $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` ";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop;
        } else {
            $sql .= " WHERE 1 ";
        }
        $sql .= " ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shop_name = Db::getInstance()->executeS($sql);

        $shop_list = array();
        foreach ($shop_name as $shoplist) {
            if($shoplist['id_fillial'] != 40) {
                $shop_list[$shoplist['id_fillial']] = $shoplist['shop_name'];
            }
        }

        /**
         * Получение списка сотрудников в зависимости от $this->id_shop который я
         * получаю от главного фильтра по городам.
         */
        $sql = "SELECT `ps_fozzy_logistic_manager`.`id_manager`, `ps_fozzy_logistic_manager`.`fio` FROM `" . _DB_PREFIX_ . "fozzy_logistic_manager`";
        if($this->id_shop != 0) {
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `active` = 1 AND `deleted` = 0";
        } else {
            $sql .= " WHERE `active` = 1 AND `deleted` = 0";
        }
        $sql .= " ORDER BY `id_manager` ASC";
        $personal_fio_manager = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_manager as $value) {
            $personal_fio[$value['id_manager']] =  $value['fio'];
        }

        $fields_list_manager = array(
            'id_person' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_number_manager' => array(
                'title' => $this->l('Week number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'fillial_name_manager' => array(
                'title' => $this->l('Shop name'),
                'type' => 'select',
                'list' => $shop_list,
                'filter' => true,
                'filter_key' => 'fillial_name_manager',
                'multiple' => true,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Role name'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tab_number_manager' => array(
                'title' => $this->l('Tab number'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'manager_name_person' => array(
                'title' => $this->l('FIO'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter' => true,
                'filter_key' => 'manager_name_person',
                'multiple' => true,
                'class' => 'fixed-width-xs',
            ),
            'norm_day' => array(
                'title' => $this->l('Working norm'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'start_work' => array(
                'title' => $this->l('Start work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'end_work' => array(
                'title' => $this->l('End work'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'time_work' => array(
                'title' => $this->l('Working hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pn' => array(
                'title' => $this->l('Pn'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vt' => array(
                'title' => $this->l('Vt'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sr' => array(
                'title' => $this->l('Sr'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_cht' => array(
                'title' => $this->l('Cht'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_pt' => array(
                'title' => $this->l('Pt'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_sb' => array(
                'title' => $this->l('Sb'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'day_vs' => array(
                'title' => $this->l('Nd'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_time_all' => array(
                'title' => $this->l('Hours'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_work_day_all' => array(
                'title' => $this->l('Working day'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'week_output_day_all' => array(
                'title' => $this->l('Days off'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'comment_person' => array(
                'title' => $this->l('Сomment'),
                'type' => 'text',
                'search' => false,
                'class' => 'fixed-width-xs',
            ),
        );

        $helper = new HelperList();
        $helper->identifier = 'id_person';
        $helper->actions = array('edit','delete');
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->title = $this->l('List manager').' на ' . date('W') . ' неделю';
        $helper->table = 'table_manager';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&id_manager=1';
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getManager($_POST);
        $helper->listTotal = count($content);
        $helper->bulk_actions = array(
            'ScheduleNextWeek' => array('text' => $this->l('График на следующую неделю'), 'icon' => 'icon-copy'),
            'DeleteSchedulePerson' => array('text' => $this->l('Удалить отмеченных сотрудников'), 'icon' => 'icon-trash'),
        );

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        return $helper->generateList($content, $fields_list_manager);
    }

    /**
     * Получение графика менеджеров в таблицу.
     * @return array|false|mysqli_result|PDOStatement|resource|null
     * @throws PrestaShopDatabaseException
     */
    public function getManager($filter = array()) {
        if(Tools::isSubmit('submitFiltertable_manager')) {
            $this->context->cookie->activeFilterManager = '';
            $this->context->cookie->activeFilterManager = serialize($filter);
        }
        $sql = 'SELECT `ps_staff_schedule_manager`.*, `ps_fozzy_logistic_role`.`role_name`, `ps_fozzy_logistic_shop`.`shop_name` as `fillial_name_manager`, `ps_fozzy_logistic_manager`.`fio` as `manager_name_person` FROM `'._DB_PREFIX_.'staff_schedule_manager` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_staff_schedule_manager`.`persone_role`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_staff_schedule_manager`.`fillial_name_manager`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_manager` ON `ps_fozzy_logistic_manager`.`id_manager` = `ps_staff_schedule_manager`.`manager_name_person`';

        $data = unserialize($this->context->cookie->activeFilterManager);
        if(!empty($data)) {
            $filter = $_POST = $data;
        }
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_manager') && !array_key_exists('submitResettable_manager', $filter)) {
            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_managerFilter_') !== false) {
                    $name_filter = str_replace('table_managerFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `$name_filter` LIKE '%$value_filter%'";
                    }
                    else {
                        if($name_filter == 'week_number_manager' && empty($value_filter)) {
                            $sql .= " AND `$name_filter` LIKE '%".date('W')."%'";
                        } elseif ($name_filter == 'fillial_name_manager') {
                            $sql .= " AND `$name_filter` LIKE '%$value_filter'";
                            if (empty($value_filter) && $this->id_shop != 0) {
                                $sql .= " AND `shop_name_manager` LIKE '%$this->id_shop%'";
                            }
                        } else {
                            $sql .= " AND `$name_filter` LIKE '%$value_filter%'";
                        }
                    }
                    $i++;
                }
            }
        } else {
            $sql .= ' WHERE `week_number_manager`='. date('W');
            if($this->id_shop != 0) {
                $sql .= ' AND `shop_name_manager` = ' . $this->id_shop;
            }
        }
        if(!empty(Tools::GetValue('table_managerOrderby')))
            $sql .= ' ORDER BY `ps_staff_schedule_manager`.`'.Tools::GetValue('table_managerOrderby').'` '.Tools::GetValue('table_managerOrderway');
        else
            $sql .= ' ORDER BY `id_person` ASC';

        $links = Db::getInstance()->executeS($sql);
        return $links;
    }

    /**
     * Получение табельного номера сотрудника с модуля "Справочник сотрудников".
     * @param int $id_person
     * @return false|string|null
     */
    public function getTabNumber($id_person = 0, $person_role = 0) {
        switch ($person_role) {
            case 1:
                $sql_question = "SELECT `ps_fozzy_logistic_sborshik`.`tabnum` FROM `" . _DB_PREFIX_ . "fozzy_logistic_sborshik` WHERE `id_sborshik` = " . $id_person;
                $tabnumber = Db::getInstance()->getValue($sql_question);
                return $tabnumber;
                break;
            case 2:
                $sql_question = "SELECT `ps_fozzy_logistic_packer`.`tabnum` FROM `" . _DB_PREFIX_ . "fozzy_logistic_packer` WHERE `id_packer` = " . $id_person;
                $tabnumber = Db::getInstance()->getValue($sql_question);
                return $tabnumber;
                break;
            case 3:
                $sql_question = "SELECT `ps_fozzy_logistic_vodila`.`tabnum` FROM `" . _DB_PREFIX_ . "fozzy_logistic_vodila` WHERE `id_vodila` = " . $id_person;
                $tabnumber = Db::getInstance()->getValue($sql_question);
                return $tabnumber;
                break;
            case 4:
                $sql_question = "SELECT `ps_fozzy_logistic_manager`.`tabnum` FROM `" . _DB_PREFIX_ . "fozzy_logistic_manager` WHERE `id_manager` = " . $id_person;
                $tabnumber = Db::getInstance()->getValue($sql_question);
                return $tabnumber;
                break;
        }
    }

    /**
     * Функция для корректного отображения информации при редактировании.
     * @param array $persona
     * @return array|void
     */
    public function getConfigFormValues($persona = array()) {
        if(is_array($persona)) {
            if($persona[0]['persone_role'] == 1) {
                $week_number = $persona[0]['week_number_picker'];
                $name_person = $persona[0]['picker_name_person'];
                $name_shop = $persona[0]['fillial_name_picker'];
            } elseif($persona[0]['persone_role'] == 2) {
                $week_number = $persona[0]['week_number_packer'];
                $name_person = $persona[0]['packer_name_person'];
                $name_shop = $persona[0]['fillial_name_packer'];
            } else {
                $week_number = $persona[0]['week_number_manager'];
                $name_person = $persona[0]['manager_name_person'];
                $name_shop = $persona[0]['fillial_name_manager'];
            }
            $start_work = explode(' ', $persona[0]['start_work']);
            $end_work = explode(' ', $persona[0]['end_work']);
            return array(
                'fozzy_staff_schedule_id_person' => $persona[0]['id_person'],
                'fozzy_staff_schedule_shop' => $name_shop,
                'fozzy_staff_schedule_stype' => $persona[0]['persone_role'],
                'fozzy_staff_schedule_name_personal[]' => $name_person,
                'fozzy_staff_schedule_week_number' => $week_number,
                'fozzy_staff_schedule_norma_day' => $persona[0]['norm_day'],
                'fozzy_staff_schedule_start_work' => $start_work[0],
                'fozzy_staff_schedule_end_work' => $end_work[0],
                'fozzy_staff_schedule_active_monday' => $persona[0]['day_pn'],
                'fozzy_staff_schedule_active_tuesday' => $persona[0]['day_vt'],
                'fozzy_staff_schedule_active_wednesday' => $persona[0]['day_sr'],
                'fozzy_staff_schedule_active_thursday' => $persona[0]['day_cht'],
                'fozzy_staff_schedule_active_friday' => $persona[0]['day_pt'],
                'fozzy_staff_schedule_active_saturday' => $persona[0]['day_sb'],
                'fozzy_staff_schedule_active_sunday' => $persona[0]['day_vs'],
                'fozzy_staff_schedule_to_comment' => $persona[0]['comment_person'],
            );
        } else {
            return;
        }
    }

    /**
     * Функция для корректного отображения информации водителей при редактировании.
     * @param array $persona
     * @return array|void
     */
    public function getConfigFormValuesDriver($persona = array()) {
        if(is_array($persona)) {
            if($persona[0]['persone_role'] == 3) {
                $week_number = $persona[0]['week_number_driver'];
                $name_person = $persona[0]['driver_name_person'];
                $name_shop = $persona[0]['fillial_name_driver'];
            }
            $starrt_work = explode(' ', $persona[0]['start_work']);
            $end_work = explode(' ', $persona[0]['end_work']);
            return array(
                'fozzy_staff_schedule_driver_id_person' => $persona[0]['id_person'],
                'fozzy_staff_schedule_driver_shop' => $name_shop,
                'fozzy_staff_schedule_driver_stype' => $persona[0]['persone_role'],
                'fozzy_staff_schedule_driver_name_personal[]' => $name_person,
                'fozzy_staff_schedule_driver_week_number' => $week_number,
                'fozzy_staff_schedule_driver_norma_day' => $persona[0]['norm_day'],
                'fozzy_staff_schedule_driver_start_work' => $starrt_work[0],
                'fozzy_staff_schedule_driver_end_work' => $end_work[0],

                // Три волны понедельника.
                'fozzy_staff_schedule_driver_active_monday_1' => $persona[0]['day_pn_1'],
                'fozzy_staff_schedule_driver_active_monday_2' => $persona[0]['day_pn_2'],
                'fozzy_staff_schedule_driver_active_monday_3' => $persona[0]['day_pn_3'],

                // Три волны вторника.
                'fozzy_staff_schedule_driver_active_tuesday_1' => $persona[0]['day_vt_1'],
                'fozzy_staff_schedule_driver_active_tuesday_2' => $persona[0]['day_vt_2'],
                'fozzy_staff_schedule_driver_active_tuesday_3' => $persona[0]['day_vt_3'],

                // Три волны среды.
                'fozzy_staff_schedule_driver_active_wednesday_1' => $persona[0]['day_sr_1'],
                'fozzy_staff_schedule_driver_active_wednesday_2' => $persona[0]['day_sr_2'],
                'fozzy_staff_schedule_driver_active_wednesday_3' => $persona[0]['day_sr_3'],

                // Три волны четверга.
                'fozzy_staff_schedule_driver_active_thursday_1' => $persona[0]['day_cht_1'],
                'fozzy_staff_schedule_driver_active_thursday_2' => $persona[0]['day_cht_2'],
                'fozzy_staff_schedule_driver_active_thursday_3' => $persona[0]['day_cht_3'],

                // Три волны пятници.
                'fozzy_staff_schedule_driver_active_friday_1' => $persona[0]['day_pt_1'],
                'fozzy_staff_schedule_driver_active_friday_2' => $persona[0]['day_pt_2'],
                'fozzy_staff_schedule_driver_active_friday_3' => $persona[0]['day_pt_3'],

                // Три волны субботы.
                'fozzy_staff_schedule_driver_active_saturday_1' => $persona[0]['day_sb_1'],
                'fozzy_staff_schedule_driver_active_saturday_2' => $persona[0]['day_sb_2'],
                'fozzy_staff_schedule_driver_active_saturday_3' => $persona[0]['day_sb_3'],

                // Три волны воскресенья.
                'fozzy_staff_schedule_driver_active_sunday_1' => $persona[0]['day_vs_1'],
                'fozzy_staff_schedule_driver_active_sunday_2' => $persona[0]['day_vs_2'],
                'fozzy_staff_schedule_driver_active_sunday_3' => $persona[0]['day_vs_3'],

                'fozzy_staff_schedule_driver_to_comment' => $persona[0]['comment_person'],
            );
        } else {
            return;
        }
    }

    /**
     * Форма экспорта списка сотрудников.
     * @return string
     */
    public function renderButtonForm() {
        $personal_types = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "fozzy_logistic_role` WHERE 1");
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` WHERE 1 ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shoplist = Db::getInstance()->executeS($sql);

        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('EXPORT TO EXCEL'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'col' => 4,
                    'type' => 'select',
                    'name' => 'fozzy_staff_schedule_stype',
                    'label' => $this->l('Employee type'),
                    'required' => true,
                    'options' => array(
                        'query' => $personal_types,
                        'id' => 'id_role',
                        'name' => 'role_name'
                    ),
                ),
                array(
                    'col' => 4,
                    'type' => 'select',
                    'id' => 'fozzy_staff_schedule_shop',
                    'name' => 'fozzy_staff_schedule_shop',
                    'multiple' => true,
                    'label' => $this->l('Score'),
                    'options' => array(
                        'query' =>$shoplist,
                        'id' => 'id_fillial',
                        'name' => 'shop_name'
                    ),
                ),
                array(
                    'col' => 1,
                    'type' => 'text',
                    'name' => 'fozzy_staff_schedule_week_number',
                    'label' => $this->l('Week number'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Export'),
            )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'btnExport';

        return $helper->generateForm($fields_form);
    }

    /**
     * Экспорт списка сотрудников в csv файл.
     */
    function export_data_to_csv($data,$filename='export',$export_stype_driver,$delimiter = ';',$enclosure = '"') {
        // Tells to the browser that a file is returned, with its name : $filename.csv
        header("Content-disposition: attachment; filename=$filename.csv");
        // Tells to the browser that the content is a csv file
        header("Content-Type: text/csv");

        // I open PHP memory as a file
        $fp = fopen("php://output", 'w');

        // Insert the UTF-8 BOM in the file
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // I add the array keys as CSV headers
        if($export_stype_driver != 3) {
            $headerss = array('ID сотрудника', 'Номер недели', 'Магазин', 'Должность', 'Табельный номер', 'ФИО', 'Норма/8 часов', 'Начало работы', 'Конец работы', 'Рабочих часов', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд', 'Часов', 'Раб. дней', 'Вых.дней', 'Комментарий');
        } else {
            $headerss = array('ID сотрудника', 'Номер недели', 'Магазин', 'Должность', 'Табельный номер', 'ФИО', 'Норма/8 часов', 'Начало работы', 'Конец работы', 'Рабочих часов', 'Пн 10:00', 'Пн 13:00', 'Пн 17:00', 'Вт 10:00', 'Вт 13:00', 'Вт 17:00', 'Ср 10:00', 'Ср 13:00', 'Ср 17:00', 'Чт 10:00', 'Чт 13:00', 'Чт 17:00', 'Пт 10:00', 'Пт 13:00', 'Пт 17:00', 'Сб 10:00', 'Сб 13:00', 'Сб 17:00', 'Нд 10:00', 'Нд 13:00', 'Нд 17:00', 'Часов', 'Раб. дней', 'Вых.дней', 'Комментарий');
        }
        fputcsv($fp,$headerss,$delimiter,$enclosure);

        // Add all the data in the file
        foreach ($data as $fields) {
            $test = fputcsv($fp, $fields,$delimiter,$enclosure);
        }

        // Close the file
        fclose($fp);

        // Stop the script
        die();
    }

    /**
     * Получение списка сотрудников при экспорте в csv файл.
     */
    public function getLinksExport($table, $suf, $week_number, $shop) {
        if($suf == 'picker')
            $replacement = 'sborshik';
        elseif ($suf == 'packer')
            $replacement = $suf;
        elseif ($suf == 'driver')
            $replacement = 'vodila';
        else
            $replacement = $suf;

        if($table != 'staff_schedule_driver') {
            $sql = "SELECT `" . _DB_PREFIX_ . $table . "`.id_person, 
        `" . _DB_PREFIX_ . $table . "`.week_number_" . $suf . ", 
        `ps_fozzy_logistic_shop`.`shop_name` , `ps_fozzy_logistic_role`.role_name, 
        `" . _DB_PREFIX_ . $table . "`.tab_number_" . $suf . ", 
        `ps_fozzy_logistic_" . $replacement . "`.fio, 
        `" . _DB_PREFIX_ . $table . "`.norm_day, 
        `" . _DB_PREFIX_ . $table . "`.start_work, 
        `" . _DB_PREFIX_ . $table . "`.end_work, 
        `" . _DB_PREFIX_ . $table . "`.time_work, 
        `" . _DB_PREFIX_ . $table . "`.day_pn, 
        `" . _DB_PREFIX_ . $table . "`.day_vt, 
        `" . _DB_PREFIX_ . $table . "`.day_sr, 
        `" . _DB_PREFIX_ . $table . "`.day_cht, 
        `" . _DB_PREFIX_ . $table . "`.day_pt, 
        `" . _DB_PREFIX_ . $table . "`.day_sb, 
        `" . _DB_PREFIX_ . $table . "`.day_vs, 
        `" . _DB_PREFIX_ . $table . "`.week_time_all, 
        `" . _DB_PREFIX_ . $table . "`.week_work_day_all, 
        `" . _DB_PREFIX_ . $table . "`.week_output_day_all, 
        `" . _DB_PREFIX_ . $table . "`.comment_person   FROM `" . _DB_PREFIX_ . $table . "`";
        } else {
            $sql = "SELECT `" . _DB_PREFIX_ . $table . "`.id_person, 
        `" . _DB_PREFIX_ . $table . "`.week_number_" . $suf . ", 
        `ps_fozzy_logistic_shop`.`shop_name` , `ps_fozzy_logistic_role`.role_name, 
        `" . _DB_PREFIX_ . $table . "`.tab_number_" . $suf . ", 
        `ps_fozzy_logistic_" . $replacement . "`.fio, 
        `" . _DB_PREFIX_ . $table . "`.norm_day, 
        `" . _DB_PREFIX_ . $table . "`.start_work, 
        `" . _DB_PREFIX_ . $table . "`.end_work, 
        `" . _DB_PREFIX_ . $table . "`.time_work, 
        `" . _DB_PREFIX_ . $table . "`.day_pn_1, 
        `" . _DB_PREFIX_ . $table . "`.day_pn_2, 
        `" . _DB_PREFIX_ . $table . "`.day_pn_3, 
        `" . _DB_PREFIX_ . $table . "`.day_vt_1, 
        `" . _DB_PREFIX_ . $table . "`.day_vt_2, 
        `" . _DB_PREFIX_ . $table . "`.day_vt_3, 
        `" . _DB_PREFIX_ . $table . "`.day_sr_1, 
        `" . _DB_PREFIX_ . $table . "`.day_sr_2, 
        `" . _DB_PREFIX_ . $table . "`.day_sr_3, 
        `" . _DB_PREFIX_ . $table . "`.day_cht_1, 
        `" . _DB_PREFIX_ . $table . "`.day_cht_2, 
        `" . _DB_PREFIX_ . $table . "`.day_cht_3, 
        `" . _DB_PREFIX_ . $table . "`.day_pt_1, 
        `" . _DB_PREFIX_ . $table . "`.day_pt_2, 
        `" . _DB_PREFIX_ . $table . "`.day_pt_3, 
        `" . _DB_PREFIX_ . $table . "`.day_sb_1, 
        `" . _DB_PREFIX_ . $table . "`.day_sb_2, 
        `" . _DB_PREFIX_ . $table . "`.day_sb_3, 
        `" . _DB_PREFIX_ . $table . "`.day_vs_1, 
        `" . _DB_PREFIX_ . $table . "`.day_vs_2, 
        `" . _DB_PREFIX_ . $table . "`.day_vs_3, 
        `" . _DB_PREFIX_ . $table . "`.week_time_all, 
        `" . _DB_PREFIX_ . $table . "`.week_work_day_all, 
        `" . _DB_PREFIX_ . $table . "`.week_output_day_all, 
        `" . _DB_PREFIX_ . $table . "`.comment_person   FROM `" . _DB_PREFIX_ . $table . "`";
        }

        $sql .= " LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `"._DB_PREFIX_.$table."`.`fillial_name_".$suf."`";
        $sql .= " LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `"._DB_PREFIX_.$table."`.`persone_role`";
        $sql .= " LEFT JOIN `ps_fozzy_logistic_".$replacement."` ON `ps_fozzy_logistic_".$replacement."`.`id_".$replacement."` = `ps_staff_schedule_".$suf."`.`".$suf."_name_person`";
        $sql .= " WHERE `week_number_".$suf."` = ".$week_number." AND `fillial_name_".$suf."` = ".$shop;

        $links = Db::getInstance()->executeS($sql);

        return $links;
    }
}