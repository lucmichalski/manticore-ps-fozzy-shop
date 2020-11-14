<?php
/**
 * 2007-2018 PrestaShop
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
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fozzy_logistics extends Module {
    protected $config_form = false;
    public $id_shop;

    public function __construct() {
        $this->name = 'fozzy_logistics';
        $this->tab = 'shipping_logistics';
        $this->version = '1.5.0';
        $this->author = 'Novevision.com, Britoff A., Rudyk M.';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Logistics for Fozzy');
        $this->description = $this->l('Module for advandced logistics for Fozzy');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install() {
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionObjectOrderAddAfter') &&
            $this->registerHook('actionOrderEdited');
    }

    public function uninstall() {
        include(dirname(__FILE__).'/sql/uninstall.php');
        return parent::uninstall();
    }

    public function getContent() {
        $this->id_shop = (int)Shop::getContextShopID();
        $id_sborshik = Tools::GetValue('id_sborshik');
        $id_vodila = Tools::GetValue('id_vodila');
        $id_packer = Tools::GetValue('id_packer');
        $id_manager = Tools::GetValue('id_manager');
        $edit = 0;
        $output = '';

        if (Tools::isSubmit('updatetable_picker') || Tools::isSubmit('updatetable_packer') || Tools::isSubmit('updatetable_driver') || Tools::isSubmit('updatetable_manager')){
             $edit = 1;
         }

         if (Tools::isSubmit('submitAdd')){
             $pers_id = 0;
             $pers_type = Tools::GetValue('FOZZY_LOGISTICS_STYPE');
             $pers_fio = Tools::GetValue('FOZZY_LOGISTICS_FIO');
             $pers_tabnum = Tools::GetValue('FOZZY_LOGISTICS_TABNUM') ? Tools::GetValue('FOZZY_LOGISTICS_TABNUM') : 0;
             $pers_inn = Tools::GetValue('FOZZY_LOGISTICS_INN');
             $pers_phone = Tools::GetValue('FOZZY_LOGISTICS_PHONE');
             $pers_ant = Tools::GetValue('FOZZY_LOGISTICS_ANT') ? Tools::GetValue('FOZZY_LOGISTICS_ANT') : 0;
             if(Tools::GetValue('FOZZY_LOGISTICS_SHOP') == 1 || Tools::GetValue('FOZZY_LOGISTICS_SHOP') == 25 || Tools::GetValue('FOZZY_LOGISTICS_SHOP') == 30) {
                 $pers_shop = 1;
                 $pers_fillial = Tools::GetValue('FOZZY_LOGISTICS_SHOP');
             } else {
                 $pers_shop = Tools::GetValue('FOZZY_LOGISTICS_SHOP') ? Tools::GetValue('FOZZY_LOGISTICS_SHOP') : 1;
                 $pers_fillial = Tools::GetValue('FOZZY_LOGISTICS_SHOP');
             }

             $pers_employment = Tools::GetValue('FOZZY_LOGISTICS_EMPLOYMENT');
             $pers_addwork = Tools::GetValue('FOZZY_LOGISTICS_DATEADDWORK') ? Tools::GetValue('FOZZY_LOGISTICS_DATEADDWORK') : '0000-00-00';
             $pers_delwork = Tools::GetValue('FOZZY_LOGISTICS_DATEDELWORK') ? Tools::GetValue('FOZZY_LOGISTICS_DATEDELWORK') : '0000-00-00';
             $pers_active = Tools::GetValue('FOZZY_LOGISTICS_ACTIVE') ? Tools::GetValue('FOZZY_LOGISTICS_ACTIVE') : 0;
             switch ($pers_type) {
                 case 1:
                     $sql_add_picker = "INSERT INTO `"._DB_PREFIX_."fozzy_logistic_sborshik` (`tabnum`, `INN`, `fio`, `phone`, `position`, `active`, `id_shop`, `id_fillial`, `employment`, `add_date`, `delete_date`) VALUES (".$pers_tabnum.",'".$pers_inn."','".$pers_fio."','".$pers_phone."','".$pers_type."','".$pers_active."','".$pers_shop."','".$pers_fillial."','".$pers_employment."','".date("Y-m-d", strtotime($pers_addwork))."','".date("Y-m-d", strtotime($pers_delwork))."')";
                     Db::getInstance()->execute($sql_add_picker);
                     $output .= $this->displayConfirmation($this->l('Сборщик успешно добавлен.'));
                     break;
                 case 2:
                     $sql_add_packer = "INSERT INTO `"._DB_PREFIX_."fozzy_logistic_packer` (`tabnum`, `INN`, `fio`, `phone`, `position`, `active`, `id_shop`, `id_fillial`, `employment`, `add_date`, `delete_date`) VALUES (".$pers_tabnum.",'".$pers_inn."','".$pers_fio."','".$pers_phone."','".$pers_type."','".$pers_active."','".$pers_shop."','".$pers_fillial."','".$pers_employment."','".date("Y-m-d", strtotime($pers_addwork))."','".date("Y-m-d", strtotime($pers_delwork))."')";
                     Db::getInstance()->execute($sql_add_packer);
                     $output .= $this->displayConfirmation($this->l('Упаковщик успешно добавлен.'));
                     break;
                 case 3:
                     $sql_add_driver = "INSERT INTO `"._DB_PREFIX_."fozzy_logistic_vodila` (`tabnum`, `INN`, `fio`, `phone`, `position`, `active`, `id_shop`, `id_fillial`, `driver_id`, `employment`, `add_date`, `delete_date`) VALUES (".$pers_tabnum.",'".$pers_inn."','".$pers_fio."','".$pers_phone."','".$pers_type."','".$pers_active."','".$pers_shop."','".$pers_fillial."','".$pers_ant."','".$pers_employment."','".date("Y-m-d", strtotime($pers_addwork))."','".date("Y-m-d", strtotime($pers_delwork))."')";
                     Db::getInstance()->execute($sql_add_driver);
                     $output .= $this->displayConfirmation($this->l('Водитель успешно добавлен.'));
                     break;
                 case 4:
                     $sql_add_manager = "INSERT INTO `"._DB_PREFIX_."fozzy_logistic_manager` (`tabnum`, `INN`, `fio`, `phone`, `position`, `active`, `id_shop`, `id_fillial`, `employment`, `add_date`, `delete_date`) VALUES (".$pers_tabnum.",'".$pers_inn."','".$pers_fio."','".$pers_phone."','".$pers_type."','".$pers_active."','".$pers_shop."','".$pers_fillial."','".$pers_employment."','".date("Y-m-d", strtotime($pers_addwork))."','".date("Y-m-d", strtotime($pers_delwork))."')";
                     Db::getInstance()->execute($sql_add_manager);
                     $output .= $this->displayConfirmation($this->l('Менеджер успешно добавлен.'));
                     break;
             }
         }

         if (Tools::isSubmit('submitEdit')) {
             $pers_id = Tools::GetValue('FOZZY_LOGISTICS_ID');
             $pers_type = Tools::GetValue('FOZZY_LOGISTICS_STYPE');
             $pers_fio = Tools::GetValue('FOZZY_LOGISTICS_FIO');
             $pers_tabnum = Tools::GetValue('FOZZY_LOGISTICS_TABNUM') ? Tools::GetValue('FOZZY_LOGISTICS_TABNUM') : 0;
             $pers_inn = Tools::GetValue('FOZZY_LOGISTICS_INN');
             $pers_phone = Tools::GetValue('FOZZY_LOGISTICS_PHONE');
             $pers_ant = Tools::GetValue('FOZZY_LOGISTICS_ANT') ? Tools::GetValue('FOZZY_LOGISTICS_ANT') : 0;
             if(Tools::GetValue('FOZZY_LOGISTICS_SHOP') == 1 || Tools::GetValue('FOZZY_LOGISTICS_SHOP') == 25 || Tools::GetValue('FOZZY_LOGISTICS_SHOP') == 30) {
                 $pers_shop = 1;
                 $pers_fillial = Tools::GetValue('FOZZY_LOGISTICS_SHOP');
             } else {
                 $pers_shop = Tools::GetValue('FOZZY_LOGISTICS_SHOP') ? Tools::GetValue('FOZZY_LOGISTICS_SHOP') : 1;
                 $pers_fillial = Tools::GetValue('FOZZY_LOGISTICS_SHOP');
             }
             $pers_employment = Tools::GetValue('FOZZY_LOGISTICS_EMPLOYMENT');
             $pers_addwork = Tools::GetValue('FOZZY_LOGISTICS_DATEADDWORK') ? Tools::GetValue('FOZZY_LOGISTICS_DATEADDWORK') : '0000-00-00';
             $pers_delwork = Tools::GetValue('FOZZY_LOGISTICS_DATEDELWORK') ? Tools::GetValue('FOZZY_LOGISTICS_DATEDELWORK') : '0000-00-00';
             $pers_active = Tools::GetValue('FOZZY_LOGISTICS_ACTIVE') ? Tools::GetValue('FOZZY_LOGISTICS_ACTIVE') : 0;
             switch ($pers_type) {
                 case 1:
                     $sql_update_picker = "UPDATE `"._DB_PREFIX_."fozzy_logistic_sborshik` SET `position` = ".$pers_type.", `tabnum` = ".$pers_tabnum.", `INN` = '".$pers_inn."', `fio` = '".$pers_fio."', `phone`= '".$pers_phone."', `active` = ".$pers_active.", `id_shop` = ".$pers_shop.", `id_fillial` = ".$pers_fillial.", `employment` = ".$pers_employment.", `add_date` = '".date("Y-m-d", strtotime($pers_addwork))."', `delete_date` = '".date("Y-m-d", strtotime($pers_delwork))."' WHERE `id_sborshik` = ".$pers_id;
                     Db::getInstance()->execute($sql_update_picker);
                     $output .= $this->displayConfirmation($this->l('Сборщик успешно обновлен.'));
                     break;
                 case 2:
                     $sql_update_packer = "UPDATE `"._DB_PREFIX_."fozzy_logistic_packer` SET `position` = ".$pers_type.", `tabnum` = ".$pers_tabnum.", `INN` = '".$pers_inn."', `fio` = '".$pers_fio."', `phone`= '".$pers_phone."', `active` = ".$pers_active.", `id_shop` = ".$pers_shop.", `id_fillial` = ".$pers_fillial.", `employment` = ".$pers_employment.", `add_date` = '".date("Y-m-d", strtotime($pers_addwork))."', `delete_date` = '".date("Y-m-d", strtotime($pers_delwork))."' WHERE `id_packer` = ".$pers_id;
                     Db::getInstance()->execute($sql_update_packer);
                     $output .= $this->displayConfirmation($this->l('Упаковщик успешно обновлен.'));
                     break;
                 case 3:
                     $sql_update_driver = "UPDATE `"._DB_PREFIX_."fozzy_logistic_vodila` SET `position` = ".$pers_type.", `tabnum` = ".$pers_tabnum.", `INN` = '".$pers_inn."', `fio` = '".$pers_fio."', `phone`= '".$pers_phone."', `active` = ".$pers_active.", `id_shop` = ".$pers_shop.", `id_fillial` = ".$pers_fillial.", `driver_id` = ".$pers_ant.", `employment` = ".$pers_employment.", `add_date` = '".date("Y-m-d", strtotime($pers_addwork))."', `delete_date` = '".date("Y-m-d", strtotime($pers_delwork))."' WHERE `id_vodila` = ".$pers_id;
                     Db::getInstance()->execute($sql_update_driver);
                     $output .= $this->displayConfirmation($this->l('Водитель успешно обновлен.'));
                     break;
                 case 4:
                     $sql_update_manager = "UPDATE `"._DB_PREFIX_."fozzy_logistic_manager` SET `position` = ".$pers_type.", `tabnum` = ".$pers_tabnum.", `INN` = '".$pers_inn."', `fio` = '".$pers_fio."', `phone`= '".$pers_phone."', `active` = ".$pers_active.", `id_shop` = ".$pers_shop.", `id_fillial` = ".$pers_fillial.", `employment` = ".$pers_employment.", `add_date` = '".date("Y-m-d", strtotime($pers_addwork))."', `delete_date` = '".date("Y-m-d", strtotime($pers_delwork))."' WHERE `id_manager` = ".$pers_id;
                     Db::getInstance()->execute($sql_update_manager);
                     $output .= $this->displayConfirmation($this->l('Менеджер успешно обновлен.'));
                     break;
             }
         }

        /**
         * Обновление статуса, редактирование, удаление сотрудника.
         */
         if (isset($id_sborshik) && $id_sborshik > 0) {
             if (Tools::isSubmit('statustable_picker')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_sborshik SET `active` = 1 - `active` WHERE `id_sborshik` = '.$id_sborshik);
             }
             if (Tools::isSubmit('deletetable_picker')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_sborshik SET `active` = 0, `deleted` = 1 WHERE `id_sborshik` = '.$id_sborshik);
             }
             if ($edit){
                 $persona = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'fozzy_logistic_sborshik WHERE `id_sborshik` = '.$id_sborshik);
             }
         }

         if (isset($id_packer) && $id_packer > 0) {
             if (Tools::isSubmit('statustable_packer')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_packer SET `active` = 1 - `active` WHERE `id_packer` = '.$id_packer);
             }
             if (Tools::isSubmit('deletetable_packer')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_packer SET `active` = 0, `deleted` = 1 WHERE `id_packer` = '.$id_packer);
             }
             if ($edit){
                 $persona = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'fozzy_logistic_packer WHERE `id_packer` = '.$id_packer);
             }
         }

         if (isset($id_vodila) && $id_vodila > 0) {
             if (Tools::isSubmit('statustable_vodila')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_vodila SET `active` = 1 - `active` WHERE `id_vodila` = '.$id_vodila);
             }
             if (Tools::isSubmit('deletetable_vodila')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_vodila SET `active` = 0, `deleted` = 1 WHERE `id_vodila` = '.$id_vodila);
             }
             if ($edit){
                 $persona = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'fozzy_logistic_vodila WHERE `id_vodila` = '.$id_vodila);
             }
         }

         if (isset($id_manager) && $id_manager > 0) {
             if (Tools::isSubmit('statustable_manager')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_manager SET `active` = 1 - `active` WHERE `id_manager` = '.$id_manager);
             }
             if (Tools::isSubmit('deletetable_manager')) {
                 Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'fozzy_logistic_manager SET `active` = 0, `deleted` = 1 WHERE `id_manager` = '.$id_manager);
             }
             if ($edit){
                 $persona = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'fozzy_logistic_manager WHERE `id_manager` = '.$id_manager);
             }
         }

        /**
         * Обнуление фильтров.
         */
         if (Tools::isSubmit('submitResettable_picker')) {
             foreach($_POST as $name_filter => $value_filter) {
                 if (strpos($name_filter, 'table_pickerFilter_') !== false)
                     $_POST[$name_filter] = NULL;
             }
         }

         if (Tools::isSubmit('submitResettable_packer')) {
             foreach($_POST as $name_filter => $value_filter) {
                 if (strpos($name_filter, 'table_packerFilter_') !== false)
                     $_POST[$name_filter] = NULL;
             }
         }

         if (Tools::isSubmit('submitResettable_driver')) {
             foreach($_POST as $name_filter => $value_filter) {
                 if (strpos($name_filter, 'table_driverFilter_') !== false)
                     $_POST[$name_filter] = NULL;
             }
         }

        if (Tools::isSubmit('submitResettable_manager')) {
            foreach($_POST as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_managerFilter_') !== false)
                    $_POST[$name_filter] = NULL;
            }
        }

        /**
         * Экспорт списка сотрудников в csv файл.
         * Export a list of printers to a csv file.
         */
         if (Tools::isSubmit('btnExport')) {
             $stype = $_POST['fozzy_logistics_stype'];
             $status = $_POST['fozzy_logistics_status'];
             $shop = $_POST['fozzy_logistics_shop'];
             if($stype == 1) {
                 $table = 'fozzy_logistic_sborshik';
                 $lable = $this->l('Список сборщиков');
             } elseif($stype == 2) {
                 $table = 'fozzy_logistic_packer';
                 $lable = $this->l('Список упаковщиков');
             } elseif ($stype == 3) {
                 $table = 'fozzy_logistic_vodila';
                 $lable = $this->l('Список водителей');
             } else {
                 $table = 'fozzy_logistic_manager';
                 $lable = $this->l('Список менеджеров');
             }

             $response_csv_data = $this->getLinksExport($table, $status, $shop);

             $filename = $lable;
             $this->export_data_to_csv($response_csv_data, $filename);
             $output .= $this->displayConfirmation($this->l('Data export was successful.'));
         }

         //$this->context->smarty->assign('module_dir', $this->_path);
        $output .= $this->renderForm($edit,$persona);
        $output .= $this->renderList_SB();
        $output .= $this->renderList_UP();
        $output .= $this->renderList_V();
        $output .= $this->renderList_MG();
        $output .= $this->renderButtonForm();
         return $output;
     }

     protected function renderForm($edit = 0,$persona = array()) {
         $helper = new HelperForm();

         $helper->show_toolbar = false;
         $helper->table = $this->table;
         $helper->module = $this;
         $helper->default_form_language = $this->context->language->id;
         $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
         $helper->identifier = $this->identifier;
         if (!$edit)
             $helper->submit_action = 'submitAdd';
         else
             $helper->submit_action = 'submitEdit';
         $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
             .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
         $helper->token = Tools::getAdminTokenLite('AdminModules');

         $helper->tpl_vars = array(
             'fields_value' => $this->getConfigFormValues($persona),
             'languages' => $this->context->controller->getLanguages(),
             'id_language' => $this->context->language->id,
         );

         return $helper->generateForm(array($this->getConfigForm($edit)));
    }

    protected function getConfigForm($edit = 0) {
        $personal_types = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "fozzy_logistic_role` WHERE 1");
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` WHERE 1 ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shoplist = Db::getInstance()->executeS($sql);

        if (!$edit) {
            $form_title = $this->l('Добавить сотрудника');
        } else {
            $form_title = $this->l('Редактировать сотрудника');
        }

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $form_title,
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'hidden',
                        'name' => 'FOZZY_LOGISTICS_ID',
                        'label' => $this->l('ID'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'select',
                        'name' => 'FOZZY_LOGISTICS_STYPE',
                        'label' => $this->l('Вид сотрудника'),
                        'required' => true,
                        'options' => array(
                            'query' => $personal_types,
                            'id' => 'id_role',
                            'name' => 'role_name'
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'select',
                        'name' => 'FOZZY_LOGISTICS_SHOP',
                        'label' => $this->l('Магазин'),
                        'required' => true,
                        'options' => array(
                            'query' => $shoplist,
                            'id' => 'id_fillial',
                            'name' => 'shop_name'
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'FOZZY_LOGISTICS_FIO',
                        'label' => $this->l('ФИО'),
                        'required' => true,
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'FOZZY_LOGISTICS_TABNUM',
                        'label' => $this->l('Табельный номер'),
                        'required' => true,
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'FOZZY_LOGISTICS_INN',
                        'label' => $this->l('ИНН'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'FOZZY_LOGISTICS_PHONE',
                        'label' => $this->l('Телефон'),
                        'desc' => $this->l('Формат заполнения телефона: 098 371 27 53'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'FOZZY_LOGISTICS_EMPLOYMENT',
                        'label' => $this->l('% Занятости'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'FOZZY_LOGISTICS_ANT',
                        'label' => $this->l('Номер водителя в логистике'),
                        'desc' => $this->l('Поле заполняется при добавнеии нового водителя.'),
                    ),
                    array(
                        'col' => 9,
                        'type' => 'date',
                        'name' => 'FOZZY_LOGISTICS_DATEADDWORK',
                        'label' => $this->l('Дата приема'),
                    ),
                    array(
                        'col' => 9,
                        'type' => 'date',
                        'name' => 'FOZZY_LOGISTICS_DATEDELWORK',
                        'label' => $this->l('Дата увольнения'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Активен'),
                        'name' => 'FOZZY_LOGISTICS_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FOZZY_LOGISTICS_ACTIVE_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'FOZZY_LOGISTICS_ACTIVE_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Таблица списка сборщиков.
     * Picker List Table.
     * @return bool|string
     */
    public function renderList_SB() {
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
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `deleted` = 0";
        } else {
            $sql .= " WHERE `deleted` = 0";
        }
        $sql .= " ORDER BY `fio` ASC";
        $personal_fio_picker = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_picker as $value) {
            $personal_fio[$value['fio']] =  $value['fio'];
        }

        $fields_list_sb = array(
            'id_sborshik' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'fio' => array(
                'title' => $this->l('ФИО'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter_key' => 'fio',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Должность'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'shop_name' => array(
                'title' => $this->l('Магазин'),
                'type' => 'select',
                'list' => $shop_list,
                'filter_key' => 'id_fillial',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tabnum' => array(
                'title' => $this->l('Табельный №'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'INN' => array(
                'title' => $this->l('ИНН'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'phone' => array(
                'title' => $this->l('Телефон'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => false,
            ),
            'employment' => array(
                'title' => $this->l('% Занятости'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'add_date' => array(
                'title' => $this->l('Дата приема'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'delete_date' => array(
                'title' => $this->l('Дата увольнения'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'active' => array(
                'title' => $this->l('Активен'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            )
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->identifier = 'id_sborshik';
        $helper->actions = array('edit','delete');
        $helper->title = $this->l('Список сборщиков');
        $helper->table = 'table_picker';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getSborshik($_POST);
        $helper->listTotal = count($content);

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        return $helper->generateList($content, $fields_list_sb);
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
     * Таблица списка упаковщиков.
     * Packer List Table.
     * @return bool|string
     */
    public function renderList_UP() {
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
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `deleted` = 0";
        } else {
            $sql .= " WHERE `deleted` = 0";
        }
        $sql .= " ORDER BY `fio` ASC";
        $personal_fio_picker = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_picker as $value) {
            $personal_fio[$value['fio']] =  $value['fio'];
        }

        $fields_list_up = array(
            'id_packer' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'fio' => array(
                'title' => $this->l('ФИО'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter_key' => 'fio',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Должность'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'shop_name' => array(
                'title' => $this->l('Магазин'),
                'type' => 'select',
                'list' => $shop_list,
                'filter_key' => 'id_fillial',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tabnum' => array(
                'title' => $this->l('Табельный №'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'INN' => array(
                'title' => $this->l('ИНН'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'phone' => array(
                'title' => $this->l('Телефон'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'employment' => array(
                'title' => $this->l('% Занятости'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'add_date' => array(
                'title' => $this->l('Дата приема'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'delete_date' => array(
                'title' => $this->l('Дата увольнения'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'active' => array(
                'title' => $this->l('Активен'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            )
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->identifier = 'id_packer';
        $helper->actions = array('edit','delete');
        $helper->title = $this->l('Список упаковщиков');
        $helper->table = 'table_packer';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getPacker($_POST);
        $helper->listTotal = count($content);

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        //if (is_array($content) && count($content))
            return $helper->generateList($content, $fields_list_up);
        //else
            //return false;
    }

    /**
     * Таблица списка водителей.
     * Drivers List Table.
     * @return bool|string
     */
    public function renderList_V() {
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
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `deleted` = 0";
        } else {
            $sql .= " WHERE `deleted` = 0";
        }
        $sql .= " ORDER BY `fio` ASC";
        $personal_fio_picker = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_picker as $value) {
            $personal_fio[$value['fio']] =  $value['fio'];
        }
        $fields_list_v = array(
            'id_vodila' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'fio' => array(
                'title' => $this->l('ФИО'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter_key' => 'fio',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Должность'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'shop_name' => array(
                'title' => $this->l('Магазин'),
                'type' => 'select',
                'list' => $shop_list,
                'filter_key' => 'id_fillial',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tabnum' => array(
                'title' => $this->l('Табельный №'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'INN' => array(
                'title' => $this->l('ИНН'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'phone' => array(
                'title' => $this->l('Телефон'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'employment' => array(
                'title' => $this->l('% Занятости'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'driver_id' => array(
                'title' => $this->l('ID в МЛ'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'add_date' => array(
                'title' => $this->l('Дата приема'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'delete_date' => array(
                'title' => $this->l('Дата увольнения'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'active' => array(
                'title' => $this->l('Активен'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            )
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->identifier = 'id_vodila';
        $helper->actions = array('edit','delete');
        $helper->title = $this->l('Список водителей');
        $helper->table = 'table_driver';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getVodila($_POST);
        $helper->listTotal = count($content);

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        return $helper->generateList($content, $fields_list_v);
    }

    /**
     * Таблица списка менеджеров.
     * Managers list table.
     * @return bool|string
     */
    public function renderList_MG() {
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
            $sql .= " WHERE `id_shop` = ". $this->id_shop . " AND `deleted` = 0";
        } else {
            $sql .= " WHERE `deleted` = 0";
        }
        $sql .= " ORDER BY `fio` ASC";
        $personal_fio_picker = Db::getInstance()->executeS($sql);

        $personal_fio = array();
        foreach ($personal_fio_picker as $value) {
            $personal_fio[$value['fio']] =  $value['fio'];
        }
        $fields_list_up = array(
            'id_manager' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'fio' => array(
                'title' => $this->l('ФИО'),
                'type' => 'select',
                'list' => $personal_fio,
                'filter_key' => 'fio',
                'class' => 'fixed-width-xs',
            ),
            'role_name' => array(
                'title' => $this->l('Должность'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'shop_name' => array(
                'title' => $this->l('Магазин'),
                'type' => 'select',
                'list' => $shop_list,
                'filter_key' => 'id_fillial',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'tabnum' => array(
                'title' => $this->l('Табельный №'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'INN' => array(
                'title' => $this->l('ИНН'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'phone' => array(
                'title' => $this->l('Телефон'),
                'type' => 'text',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'employment' => array(
                'title' => $this->l('% Занятости'),
                'type' => 'text',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'add_date' => array(
                'title' => $this->l('Дата приема'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'delete_date' => array(
                'title' => $this->l('Дата увольнения'),
                'type' => 'date',
                'search' => false,
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'active' => array(
                'title' => $this->l('Активен'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            )
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->identifier = 'id_manager';
        $helper->actions = array('edit','delete');
        $helper->title = $this->l('Список менеджеров');
        $helper->table = 'table_manager';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('show_filters' => true);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $helper->_default_pagination = 10;
        $content = $this->getManager($_POST);
        $helper->listTotal = count($content);

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 10;
        $content = $this->paginate_content($content, $page, $pagination);

        return $helper->generateList($content, $fields_list_up);
    }

    /**
     * Форма экспорта списка сотрудников.
     * Employee List Export Form.
     * @return string
     */
    public function renderButtonForm() {
        $personal_types = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "fozzy_logistic_role` WHERE 1");
        $sql = "SELECT `ps_fozzy_logistic_shop`.* FROM `ps_fozzy_logistic_shop` WHERE 1 ORDER BY  `ps_fozzy_logistic_shop`.`shop_name` ASC";
        $shoplist = Db::getInstance()->executeS($sql);

        $personal_status = array(
            array(
                'id_status' => 1,
                'persone_status_name' => 'Все'
            ),

            array(
                'id_status' => 2,
                'persone_status_name' => 'Активные'
            ),

            array(
                'id_status' => 3,
                'persone_status_name' => 'Неактивные'
            ),

            array(
                'id_status' => 4,
                'persone_status_name' => 'Уволенные'
            )
        );
        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('ЭКСПОРТ В EXCEL'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'col' => 3,
                    'type' => 'select',
                    'name' => 'fozzy_logistics_stype',
                    'label' => $this->l('Вид сотрудника'),
                    'required' => true,
                    'options' => array(
                        'query' => $personal_types,
                        'id' => 'id_role',
                        'name' => 'role_name'
                    ),
                ),
                array(
                    'col' => 3,
                    'type' => 'select',
                    'name' => 'fozzy_logistics_shop',
                    'label' => $this->l('Магазин'),
                    'options' => array(
                        'query' => $shoplist,
                        'id' => 'id_fillial',
                        'name' => 'shop_name'
                    ),
                ),
                array(
                    'col' => 3,
                    'type' => 'select',
                    'name' => 'fozzy_logistics_status',
                    'label' => $this->l('Статус сотрудника'),
                    'options' => array(
                        'query' => $personal_status,
                        'id' => 'id_status',
                        'name' => 'persone_status_name'
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Export'),
                'id' => 'btnExport',
                'name' => 'btnExport'
            )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit_'.$this->name;

        return $helper->generateForm($fields_form);
    }

    public function getSborshik($filter = array()) {
        $sql = 'SELECT `ps_fozzy_logistic_sborshik`.*, `ps_fozzy_logistic_role`.role_name, `ps_fozzy_logistic_shop`.shop_name FROM `'._DB_PREFIX_.'fozzy_logistic_sborshik` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_fozzy_logistic_sborshik`.`position`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_fozzy_logistic_sborshik`.`id_fillial`';
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_picker') && !array_key_exists('submitResettable_picker', $filter)) {
            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_pickerFilter_') !== false) {
                    $name_filter = str_replace('table_pickerFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `"._DB_PREFIX_."fozzy_logistic_sborshik`.`$name_filter` LIKE '%$value_filter%'";
                    } elseif ($name_filter == 'id_fillial') {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_sborshik`.`$name_filter` LIKE '%$value_filter'";
                        if (empty($value_filter) && $this->id_shop != 0) {
                            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_sborshik`.`id_shop` LIKE '%$this->id_shop%'";
                        }
                    } else {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_sborshik`.`$name_filter` LIKE '%$value_filter%'";
                    }
                    $i++;
                }
            }
            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_sborshik`.`deleted` = 0";
        } else {
            $sql .= ' WHERE `ps_fozzy_logistic_sborshik`.`deleted` = 0';
            if($this->id_shop != 0) {
                $sql .= ' AND `ps_fozzy_logistic_sborshik`.`id_shop` = ' . $this->id_shop;
                //if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
                    //$sql .= ' AND `ps_fozzy_logistic_sborshik`.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
            }
        }

        $sql .= ' ORDER BY `fio` ASC';

        $links = Db::getInstance()->executeS($sql);
        return $links;
    }

    public function getPacker($filter = array()) {
        $sql = 'SELECT `ps_fozzy_logistic_packer`.*, `ps_fozzy_logistic_role`.role_name, `ps_fozzy_logistic_shop`.shop_name FROM `'._DB_PREFIX_.'fozzy_logistic_packer` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_fozzy_logistic_packer`.`position`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_fozzy_logistic_packer`.`id_fillial`';
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_packer') && !array_key_exists('submitResettable_packer', $filter)) {
            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_packerFilter_') !== false) {
                    $name_filter = str_replace('table_packerFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `"._DB_PREFIX_."fozzy_logistic_packer`.`$name_filter` LIKE '%$value_filter%'";
                    } elseif ($name_filter == 'id_fillial') {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_packer`.`$name_filter` LIKE '%$value_filter'";
                        if (empty($value_filter) && $this->id_shop != 0) {
                            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_packer`.`id_shop` LIKE '%$this->id_shop%'";
                        }
                    } else {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_packer`.`$name_filter` LIKE '%$value_filter%'";
                    }
                    $i++;
                }
            }
            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_packer`.`deleted` = 0";
        } else {
            $sql .= ' WHERE `ps_fozzy_logistic_packer`.`deleted` = 0 ';
            if($this->id_shop != 0) {
                $sql .= ' AND `ps_fozzy_logistic_packer`.`id_shop` = ' . $this->id_shop;
                //if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
                    //$sql .= ' AND `ps_shop`.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
            }
        }

        $sql .= ' ORDER BY  `fio` ASC';

        $links = Db::getInstance()->executeS($sql);
        return $links;
    }

    public function getVodila($filter = array()) {
        $sql = 'SELECT `ps_fozzy_logistic_vodila`.*, `ps_fozzy_logistic_role`.role_name, `ps_fozzy_logistic_shop`.shop_name FROM `'._DB_PREFIX_.'fozzy_logistic_vodila` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_fozzy_logistic_vodila`.`position`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_fozzy_logistic_vodila`.`id_fillial`';
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_driver') && !array_key_exists('submitResettable_driver', $filter)) {
            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_driverFilter_') !== false) {
                    $name_filter = str_replace('table_driverFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `"._DB_PREFIX_."fozzy_logistic_vodila`.`$name_filter` LIKE '%$value_filter%'";
                    } elseif ($name_filter == 'id_fillial') {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_vodila`.`$name_filter` LIKE '%$value_filter'";
                        if (empty($value_filter) && $this->id_shop != 0) {
                            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_vodila`.`id_shop` LIKE '%$this->id_shop%'";
                        }
                    } else {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_vodila`.`$name_filter` LIKE '%$value_filter%'";
                    }
                    $i++;
                }
            }
            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_vodila`.`deleted` = 0";
        } else {
            $sql .= ' WHERE `ps_fozzy_logistic_vodila`.`deleted` = 0 ';
            if($this->id_shop != 0) {
                $sql .= ' AND `ps_fozzy_logistic_vodila`.`id_shop` = ' . $this->id_shop;
                //if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
                    //$sql .= ' AND `ps_fozzy_logistic_vodila`.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
            }
        }

        $sql .= ' ORDER BY  `fio` ASC';

        $links = Db::getInstance()->executeS($sql);
        return $links;
    }

    public function getManager($filter = array()) {
        $sql = 'SELECT `ps_fozzy_logistic_manager`.*, `ps_fozzy_logistic_role`.role_name, `ps_fozzy_logistic_shop`.shop_name FROM `'._DB_PREFIX_.'fozzy_logistic_manager` ';
        $sql .= 'LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `ps_fozzy_logistic_manager`.`position`';
        $sql .= ' LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `ps_fozzy_logistic_manager`.`id_fillial`';
        if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFiltertable_manager') && !array_key_exists('submitResettable_driver', $filter)) {
            $i = 0;
            foreach($filter as $name_filter => $value_filter) {
                if (strpos($name_filter, 'table_managerFilter_') !== false) {
                    $name_filter = str_replace('table_managerFilter_', '', $name_filter);
                    if($i == 0) {
                        $sql .= " WHERE `"._DB_PREFIX_."fozzy_logistic_manager`.`$name_filter` LIKE '%$value_filter%'";
                    } elseif ($name_filter == 'id_fillial') {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_manager`.`$name_filter` LIKE '%$value_filter'";
                        if (empty($value_filter) && $this->id_shop != 0) {
                            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_manager`.`id_shop` LIKE '%$this->id_shop%'";
                        }
                    } else {
                        $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_manager`.`$name_filter` LIKE '%$value_filter%'";
                    }
                    $i++;
                }
            }
            $sql .= " AND `"._DB_PREFIX_."fozzy_logistic_manager`.`deleted` = 0";
        } else {
            $sql .= ' WHERE `ps_fozzy_logistic_manager`.`deleted` = 0 ';
            if($this->id_shop != 0) {
                $sql .= ' AND `ps_fozzy_logistic_manager`.`id_shop` = ' . $this->id_shop;
                //if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
                    //$sql .= ' AND `ps_fozzy_logistic_manager`.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
            }
        }

        $sql .= ' ORDER BY  `fio` ASC';

        $links = Db::getInstance()->executeS($sql);
        return $links;
    }

    /**
     * Получение списка сотрудников при экспорте в csv файл.
     */
    public function getLinksExport($table, $status, $shop) {
        if($status == 1){
            $sql = "SELECT
            `"._DB_PREFIX_.$table."`.fio,
            `ps_fozzy_logistic_role`.role_name,
            `ps_fozzy_logistic_shop`.shop_name,
            `"._DB_PREFIX_.$table."`.tabnum, 
            `"._DB_PREFIX_.$table."`.INN, 
            `"._DB_PREFIX_.$table."`.phone, 
            `"._DB_PREFIX_.$table."`.employment,
            `"._DB_PREFIX_.$table."`.add_date,
            `"._DB_PREFIX_.$table."`.delete_date   
            FROM `"._DB_PREFIX_.$table."` 
            LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `"._DB_PREFIX_.$table."`.`position` 
            LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `"._DB_PREFIX_.$table."`.`id_fillial` 
            WHERE ".$status." AND `"._DB_PREFIX_.$table."`.`id_fillial` = ".$shop;
        } elseif ($status == 2) {
            $sql = "SELECT
            `"._DB_PREFIX_.$table."`.fio,
            `ps_fozzy_logistic_role`.role_name,
            `ps_fozzy_logistic_shop`.shop_name,
            `"._DB_PREFIX_.$table."`.tabnum, 
            `"._DB_PREFIX_.$table."`.INN, 
            `"._DB_PREFIX_.$table."`.phone, 
            `"._DB_PREFIX_.$table."`.employment,
            `"._DB_PREFIX_.$table."`.add_date,
            `"._DB_PREFIX_.$table."`.delete_date   
            FROM `"._DB_PREFIX_.$table."` 
            LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `"._DB_PREFIX_.$table."`.`position` 
            LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `"._DB_PREFIX_.$table."`.`id_fillial` 
            WHERE `active` = 1 AND `deleted` = 0 AND `"._DB_PREFIX_.$table."`.`id_fillial` = ".$shop;
        } elseif ($status == 3) {
            $sql = "SELECT
            `"._DB_PREFIX_.$table."`.fio,
            `ps_fozzy_logistic_role`.role_name,
            `ps_fozzy_logistic_shop`.shop_name,
            `"._DB_PREFIX_.$table."`.tabnum, 
            `"._DB_PREFIX_.$table."`.INN, 
            `"._DB_PREFIX_.$table."`.phone, 
            `"._DB_PREFIX_.$table."`.employment,
            `"._DB_PREFIX_.$table."`.add_date,
            `"._DB_PREFIX_.$table."`.delete_date   
            FROM `"._DB_PREFIX_.$table."` 
            LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `"._DB_PREFIX_.$table."`.`position` 
            LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `"._DB_PREFIX_.$table."`.`id_fillial` 
            WHERE `active` = 0 AND `deleted` = 0 AND `"._DB_PREFIX_.$table."`.`id_fillial` = ".$shop;
        } else {
            $sql = "SELECT
            `"._DB_PREFIX_.$table."`.fio,
            `ps_fozzy_logistic_role`.role_name,
            `ps_fozzy_logistic_shop`.shop_name,
            `"._DB_PREFIX_.$table."`.tabnum, 
            `"._DB_PREFIX_.$table."`.INN, 
            `"._DB_PREFIX_.$table."`.phone, 
            `"._DB_PREFIX_.$table."`.employment,
            `"._DB_PREFIX_.$table."`.add_date,
            `"._DB_PREFIX_.$table."`.delete_date   
            FROM `"._DB_PREFIX_.$table."` 
            LEFT JOIN `ps_fozzy_logistic_role` ON `ps_fozzy_logistic_role`.`id_role` = `"._DB_PREFIX_.$table."`.`position` 
            LEFT JOIN `ps_fozzy_logistic_shop` ON `ps_fozzy_logistic_shop`.`id_fillial` = `"._DB_PREFIX_.$table."`.`id_fillial` 
            WHERE `deleted` = 1 AND `"._DB_PREFIX_.$table."`.`id_fillial` = ".$shop;
        }

        $links = Db::getInstance()->executeS($sql);
        return $links;
    }

    /**
     * Экспорт списка сотрудников в csv файл.
     */
    function export_data_to_csv($data, $filename='export', $delimiter = ';', $enclosure = '"') {
        // Tells to the browser that a file is returned, with its name : $filename.csv
        header("Content-disposition: attachment; filename=$filename.csv");
        // Tells to the browser that the content is a csv file
        header("Content-Type: text/csv");

        // I open PHP memory as a file
        $fp = fopen("php://output", 'w');

        // Insert the UTF-8 BOM in the file
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // I add the array keys as CSV headers
        $headerss = array('ФИО', 'Должность', 'Магазин', 'Табельный номер', 'ИНН', 'Телефон', '% Занятости', 'Дата принятия', 'Дата увольнения');
        fputcsv($fp,$headerss,$delimiter,$enclosure);

        // Add all the data in the file
        foreach ($data as $fields) {
            fputcsv($fp, $fields,$delimiter,$enclosure);
        }

        // Close the file
        fclose($fp);

        // Stop the script
        die();
    }

    /**
     * Получение данных при редактировании сотрудника.
     */
    protected function getConfigFormValues($persona = array())
    {
        if ($persona) {
            if (array_key_exists('id_sborshik', $persona[0])){
                $id = $persona[0]['id_sborshik'];
                $type = 1;
                $driver = '';
            } elseif (array_key_exists('id_packer', $persona[0])) {
                $id = $persona[0]['id_packer'];
                $type = 2;
                $driver = '';
            } elseif (array_key_exists('driver_id', $persona[0])) {
                $id = $persona[0]['id_vodila'];
                $type = 3;
                $driver = $persona[0]['driver_id'];
            } else  {
                $id = $persona[0]['id_manager'];
                $type = 4;
                $driver = '';
            }
            return array(
                'FOZZY_LOGISTICS_ID' => $id,
                'FOZZY_LOGISTICS_STYPE' => $type,
                'FOZZY_LOGISTICS_FIO' => $persona[0]['fio'],
                'FOZZY_LOGISTICS_TABNUM' => $persona[0]['tabnum'],
                'FOZZY_LOGISTICS_INN' => $persona[0]['INN'],
                'FOZZY_LOGISTICS_PHONE' => $persona[0]['phone'],
                'FOZZY_LOGISTICS_ANT' => $driver,
                'FOZZY_LOGISTICS_SHOP' => $persona[0]['id_fillial'],
                'FOZZY_LOGISTICS_ACTIVE' => $persona[0]['active'],
                'FOZZY_LOGISTICS_EMPLOYMENT' => $persona[0]['employment'],
                'FOZZY_LOGISTICS_DATEADDWORK' => $persona[0]['add_date'],
                'FOZZY_LOGISTICS_DATEDELWORK' => $persona[0]['delete_date'],
            );
        } else {
            return;
        }

    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookActionObjectOrderAddAfter($params)
    {
     $order = $params['object'];
     $id_order = (int)$order->id;
     
     $order_products = $order->product_list;
     
     foreach ( $order_products as $productd ) 
     {
        $product = new Product($productd['id_product']);
        
        if ($product->condition == 'new') {
          $sql = "UPDATE `"._DB_PREFIX_."orders` SET `norm` = 1 WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql);
        }
        if ($product->condition  == 'refurbished') {
          $sql = "UPDATE `"._DB_PREFIX_."orders` SET `ice` = 1 WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql);
        }
        if ($product->condition  == 'used') {
          $sql = "UPDATE `"._DB_PREFIX_."orders` SET `fresh` = 1 WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql);
        }
      }
    }
    
    public function hookActionOrderEdited($params)
    {       
      $order = $params['order'];
      $products = $order->getProducts();
      $id_order = (int)$order->id;
      
      if ($id_order) {
          $sql = "UPDATE `"._DB_PREFIX_."orders` SET `norm` = 0, `ice` = 0, `fresh` = 0  WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql);
      }
      foreach ($products as $product) {
        if ($product['condition'] == 'new' && $product['product_quantity'] > 0) {
          $sql = "UPDATE `"._DB_PREFIX_."orders` SET `norm` = 1 WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql);
        }
        if ($product['condition'] == 'refurbished' && $product['product_quantity'] > 0) {
          $sql = "UPDATE `"._DB_PREFIX_."orders` SET `ice` = 1 WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql);
        }
        if ($product['condition'] == 'used' && $product['product_quantity'] > 0) {
          $sql = "UPDATE `"._DB_PREFIX_."orders` SET `fresh` = 1 WHERE `id_order` = ".$id_order;
          Db::getInstance()->execute($sql);
        }
      }
    }
}
