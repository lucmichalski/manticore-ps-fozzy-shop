<?php
/**
 * 2018 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2018 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Gshoppingfeed extends Module
{
    protected $config_form = false;
    protected $confirmations = array();
    protected $errors = array();
    protected static $currentIndex;
    protected $base_uri;
    protected $useTax = false;

    /** @var array $cache_lang use to cache texts in current language */
    public static $cache_lang = array();

    public function __construct()
    {
        $this->name = 'gshoppingfeed';
        $this->tab = 'administration';
        $this->version = '3.9.7';
        $this->author = 'TerranetPro';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->id_product = 21889;
        $this->displayName = $this->l('Google Shopping Feed');
        $this->description = $this->l('Export your products to Google Merchant Center, easily.');

        $this->base_uri = ToolsCore::getCurrentUrlProtocolPrefix() . $this->context->shop->domain_ssl . $this->context->shop->physical_uri;
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->createExportFolder();
    }

    protected function createExportFolder()
    {
        if (!is_dir(dirname(__FILE__) . '/export')) {
            @mkdir(dirname(__FILE__) . '/export', 0755, true);
        }
        @chmod(dirname(__FILE__) . '/export', 0755);

        return true;
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    public function getContent()
    {
        if (Tools::getValue('showFeedLinks', false)) {
            return $this->renderShowFeedLinks();
        }

        if (Tools::isSubmit('verifyTables')) {
            $resUpdate = $this->verifyTableIntegrity();
            if (is_bool($resUpdate) && $resUpdate) {
                $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.'));
            } elseif (is_array($resUpdate) && isset($resUpdate['error']) && $resUpdate['error'] && isset($resUpdate['msg'])) {
                $this->errors[] = $this->displayError($resUpdate['msg']);
            }
        }

        if ((bool)Tools::isSubmit('taxonomy_trunctable') === true) {
            $id_lang = (int)Tools::getValue('lang_google_lists');
            if (DB::getInstance()->delete('gshoppingfeed_taxonomy', 'id_lang=' . (int)$id_lang)) {
                $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.'));
            } else {
                $this->errors[] = $this->displayError($this->l('The settings have not been updated!'));
            }
        }

        if ((bool)Tools::isSubmit('update_all_taxonomy_list') === true
            && Tools::getValue('update_all_taxonomy_item')) {
            $item = Tools::getValue('update_all_taxonomy_item');
            $item = explode('___', $item);
            $id_lang = (int)Tools::getValue('lang_google_lists');
            if (is_array($item) && count($item) == 2 && is_numeric($item[0])) {
                $allCat = Category::getAllCategoriesName();
                $rootCat = Configuration::get('PS_ROOT_CATEGORY');
                $data_insert = array();
                foreach ($allCat as $cat) {
                    if ($rootCat == $cat['id_category']) {
                        continue;
                    }
                    $data_insert[] = array(
                        'id_category' => (int)$cat['id_category'],
                        'id_taxonomy' => (int)$item[0],
                        'name_taxonomy' => pSQL($item[1]),
                        'id_lang' => (int)$id_lang
                    );
                }
                DB::getInstance()->delete('gshoppingfeed_taxonomy', 'id_lang=' . (int)$id_lang);
                $insert = Db::getInstance()->insert('gshoppingfeed_taxonomy', $data_insert, false, true);
                if ($insert) {
                    $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.'));
                } else {
                    $this->errors[] = $this->displayError($this->l('The settings have not been updated!'));
                }
            }
        }
        self::$currentIndex = $_SERVER['SCRIPT_NAME'] . (($controller = Tools::getValue('controller')) ? '?controller=' . $controller : '');

        if (Tools::isSubmit('reloadTaxonomyLists') && Tools::getValue('reloadTaxonomyLists') == 1
            && Tools::getValue('ajax') && Tools::isSubmit('getTaxonomy') && Validate::isInt(Tools::getValue('getTaxonomy'))
            && Tools::getValue('getTaxonomy') > 0
        ) {
            die($this->renderLinksTaxonomyList((int)Tools::getValue('getTaxonomy')));
        }

        if (Tools::isSubmit('getTaxonomyOptionsListsForBulk') && Tools::getValue('getTaxonomyOptionsListsForBulk') == 1
            && Tools::getValue('ajax') && Tools::isSubmit('getTaxonomyLang') && Validate::isInt(Tools::getValue('getTaxonomyLang'))
            && Tools::getValue('getTaxonomyLang') > 0) {
            $taxonomyLists = self::getGoogleTxtCategoryFeed((int)Tools::getValue('getTaxonomyLang'), false);
            $this->smarty->assign(
                array(
                    'forbulk' => 1,
                    'taxonomyLists' => $taxonomyLists
                )
            );

            $template_config = $this->display(__FILE__, 'views/templates/admin/taxonomy_configure.tpl');

            die($template_config);
        }

        if (Tools::isSubmit('getTaxonomyOptionsLists') && Tools::getValue('getTaxonomyOptionsLists') == 1
            && Tools::getValue('ajax') && Tools::isSubmit('getTaxonomyLang') && Validate::isInt(Tools::getValue('getTaxonomyLang'))
            && Tools::getValue('getTaxonomyLang') > 0 && Tools::isSubmit('setInd') && Tools::getValue('setInd') > 0
        ) {
            $taxonomyLists = self::getGoogleTxtCategoryFeed((int)Tools::getValue('getTaxonomyLang'), false);
            $taxSelected = self::getTaxonomyCategoryLangLinkd((int)Tools::getValue('setInd'), (int)Tools::getValue('getTaxonomyLang'));
            $this->smarty->assign(
                array(
                    'taxonomySelected' => $taxSelected,
                    'taxonomyLists' => $taxonomyLists
                )
            );
            $template_config = $this->display(__FILE__, 'views/templates/admin/taxonomy_configure.tpl');
            die($template_config);
        }

        if (Tools::isSubmit('getBulkUpdateList') && Tools::getValue('getBulkUpdateList') == 1
            && Tools::getValue('ajax') && Tools::isSubmit('getTaxonomyLang') && Validate::isInt(Tools::getValue('getTaxonomyLang'))
            && Tools::getValue('getTaxonomyLang') > 0
        ) {
            $taxonomyLists = self::getGoogleTxtCategoryFeed((int)Tools::getValue('getTaxonomyLang'), false);
            $taxSelected = self::getTaxonomyCategoryLangLinkd((int)Tools::getValue('setInd'), (int)Tools::getValue('getTaxonomyLang'));
            $this->smarty->assign(
                array(
                    'taxonomySelected' => $taxSelected,
                    'taxonomyLists' => $taxonomyLists
                )
            );
            $template_config = $this->display(__FILE__, 'views/templates/admin/bulk_update_taxonomy_configure.tpl');
            die($template_config);
        }

        if (Tools::isSubmit('setTaxonomyOptionsLists') && Tools::getValue('setTaxonomyOptionsLists') == 1
            && Tools::getValue('ajax') && Tools::isSubmit('setTaxonomyLang') && Validate::isInt(Tools::getValue('setTaxonomyLang'))
            && Tools::getValue('setTaxonomyLang') > 0 && Tools::isSubmit('setInd') && Tools::getValue('setInd') > 0
            && Validate::isInt(Tools::getValue('taxonomy_selected'))
        ) {
            $update = $this->setTaxonomyCategoryLang((int)Tools::getValue('setInd'), (int)Tools::getValue('taxonomy_selected'), (int)Tools::getValue('setTaxonomyLang'));
            die(Tools::jsonEncode($update));
        }

        if (Tools::getValue('rebuild') && Tools::getValue('rebuild') == 1
            && Tools::getValue('id_gshoppingfeed', false)
            && Validate::isInt(Tools::getValue('id_gshoppingfeed'))) {
            $ret = array();

            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'gshoppingfeed` WHERE `id_gshoppingfeed` = ' . (int)Tools::getValue('id_gshoppingfeed');
            $gshoppingfeed = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if ($gshoppingfeed && count($gshoppingfeed) > 0) {
                $ret['name_feed'] = !empty($gshoppingfeed['name']) ? $gshoppingfeed['name'] : Configuration::get('PS_SHOP_NAME');
                $ret['brand_type'] = $gshoppingfeed['brand_type'];
                $ret['only_active'] = (int)$gshoppingfeed['only_active'];
                $ret['description_crop'] = (int)$gshoppingfeed['description_crop'];
                $ret['modify_uppercase_title'] = (int)$gshoppingfeed['modify_uppercase_title'];
                $ret['modify_uppercase_description'] = (int)$gshoppingfeed['modify_uppercase_description'];
                $ret['parts_payment_enabled'] = (int)$gshoppingfeed['parts_payment_enabled'];
                $ret['product_title_in_product_type'] = (int)$gshoppingfeed['product_title_in_product_type'];
                $ret['identifier_exists_mpn'] = (int)$gshoppingfeed['identifier_exists_mpn'];
                $ret['visible_product_hide'] = (int)$gshoppingfeed['visible_product_hide'];
                $ret['mpn_force_on'] = (int)$gshoppingfeed['mpn_force_on'];
                $ret['max_parts_payment'] = (int)$gshoppingfeed['max_parts_payment'];
                $ret['interest_rates'] = (int)$gshoppingfeed['interest_rates'];
                $ret['exclude_ids'] = $gshoppingfeed['exclude_ids'];
                $ret['title_suffix'] = $gshoppingfeed['title_suffix'];
                $ret['description_suffix'] = $gshoppingfeed['description_suffix'];
                $ret['additional_each_product'] = $gshoppingfeed['additional_each_product'];
                $ret['export_attributes'] = $gshoppingfeed['export_attributes'];
                $ret['filtered_by_associated_type'] = $gshoppingfeed['filtered_by_associated_type'];
                $ret['export_attributes_only_first'] = $gshoppingfeed['export_attributes_only_first'];
                $ret['export_attribute_url'] = $gshoppingfeed['export_attribute_url'];
                $ret['export_attribute_prices'] = $gshoppingfeed['export_attribute_prices'];
                $ret['export_attribute_images'] = $gshoppingfeed['export_attribute_images'];
                $ret['export_feature'] = $gshoppingfeed['export_feature'];
                $ret['use_additional_shipping_cost'] = $gshoppingfeed['use_additional_shipping_cost'];
                $ret['type_image'] = $gshoppingfeed['type_image'];
                $ret['type_description'] = $gshoppingfeed['type_description'];
                $ret['id_currency'] = $gshoppingfeed['id_currency'];
                $ret['id_country'] = $gshoppingfeed['id_country'];
                $ret['id_carrier'] = Tools::jsonDecode($gshoppingfeed['id_carrier']);
                $ret['select_lang'] = $gshoppingfeed['select_lang'];
                $ret['get_features_gender'] = $gshoppingfeed['get_features_gender'];
                $ret['get_features_age_group'] = $gshoppingfeed['get_features_age_group'];
                $ret['instance_of_tax'] = $gshoppingfeed['instance_of_tax'];
                $ret['get_attribute_size'] = Tools::jsonDecode($gshoppingfeed['get_attribute_size']);
                $ret['get_attribute_color'] = Tools::jsonDecode($gshoppingfeed['get_attribute_color']);
                $ret['get_attribute_pattern'] = Tools::jsonDecode($gshoppingfeed['get_attribute_pattern']);
                $ret['get_attribute_material'] = Tools::jsonDecode($gshoppingfeed['get_attribute_material']);
                $ret['export_non_available'] = $gshoppingfeed['export_non_available'];
                $ret['export_product_quantity'] = $gshoppingfeed['export_product_quantity'];
                $ret['additional_image'] = $gshoppingfeed['additional_image'];
                $ret['min_price'] = $gshoppingfeed['min_price_filter'];
                $ret['max_price'] = $gshoppingfeed['max_price_filter'];
                $ret['mpn_type'] = json_decode($gshoppingfeed['mpn_type']);
                $ret['gtin_type'] = json_decode($gshoppingfeed['gtin_type']);
                $ret['select_manufacturers'] = Tools::jsonDecode($gshoppingfeed['manufacturers_filter']);
                $ret['category_filter'] = Tools::jsonDecode($gshoppingfeed['category_filter']);

                $ret['id_gshoppingfeed'] = $gshoppingfeed['id_gshoppingfeed'];

                $this->generationList($ret);
            }
        }

        if (((bool)Tools::isSubmit('submitGshoppingfeedModule')) == true
            || ((bool)Tools::isSubmit('submitTaxonomyEdit')) == true
            || ((bool)Tools::isSubmit('submitTaxonomyLangEdit')) == true
        ) {
            $this->postProcess();
        }

        if ((bool)Tools::isSubmit('deletegshoppingfeed') == true && Validate::isInt(Tools::getValue('id_gshoppingfeed'))) {
            if (Db::getInstance()->delete('gshoppingfeed', 'id_gshoppingfeed= ' . (int)Tools::getValue('id_gshoppingfeed'))) {
                Tools::redirectAdmin(
                    AdminController::$currentIndex . '&configure=' . urlencode($this->name) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&conf=5'
                );
            }
        }

        $output = (!empty($this->confirmations) && count($this->confirmations) > 0) ? join('<br/>', $this->confirmations) : '';
        $output .= (!empty($this->errors) && count($this->errors) > 0) ? join('<br/>', $this->errors) : '';

        $language_code = Tools::getValue('language_code');
        if ((bool)Tools::isSubmit('updategshoppingfeed_taxonomy_lang_list') === true && !empty($language_code)) {
            return $output . $this->formEditTaxonomyList() ;
        }

        if ((bool)Tools::isSubmit('updategshoppingfeed_taxonomy') === true && Validate::isInt(Tools::getValue('id_category')) && Tools::getValue('id_category') > 0) {
            return $output . $this->renderTaxonomyEditForm() ;
        }

        return $output . $this->getFastLink() . $this->renderList() . $this->renderLinksTaxonomyList() . $this->renderForm() . $this->renderTaxonomyList() . $this->getVerifyBtn() ;
    }

    public function getVerifyBtn()
    {
        $linkToUpdateTables = AdminController::$currentIndex . '&configure=' . urlencode($this->name) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&verifyTables';

        $this->smarty->assign(array(
            'linkToUpdate' => $linkToUpdateTables
        ));

        return $this->display($this->name, 'tableVerifyTablesBtn.tpl');
    }

    public function verifyTableIntegrity()
    {
        $tables = array(
            array(
                'name' => 'gshoppingfeed',
                'fields' => array(
                    'id_gshoppingfeed' => 'int-autoincrement',
                    'only_active' => 'tinyint',
                    'description_crop' => 'tinyint',
                    'modify_uppercase_title' => 'tinyint',
                    'modify_uppercase_description' => 'tinyint',
                    'parts_payment_enabled' => 'tinyint',
                    'product_title_in_product_type' => 'tinyint',
                    'identifier_exists_mpn' => 'tinyint',
                    'visible_product_hide' => 'tinyint',
                    'mpn_force_on' => 'tinyint',
                    'max_parts_payment' => 'int',
                    'interest_rates' => 'varchar',
                    'name' => 'varchar',
                    'title_suffix' => 'varchar',
                    'description_suffix' => 'text',
                    'additional_each_product' => 'text',
                    'type_image' => 'varchar',
                    'brand_type' => 'varchar',
                    'mpn_type' => 'varchar',
                    'gtin_type' => 'varchar',
                    'additional_image' => 'tinyint',
                    'type_description' => 'tinyint',
                    'select_lang' => 'int',
                    'id_currency' => 'int',
                    'id_country' => 'int',
                    'id_carrier' => 'varchar',
                    'export_attributes' => 'tinyint',
                    'filtered_by_associated_type' => 'tinyint',
                    'export_attributes_only_first' => 'tinyint',
                    'export_attribute_url' => 'tinyint',
                    'export_attribute_prices' => 'tinyint',
                    'export_attribute_images' => 'tinyint',
                    'export_feature' => 'tinyint',
                    'use_additional_shipping_cost' => 'tinyint',
                    'export_product_quantity' => 'tinyint',
                    'get_features_gender' => 'int',
                    'get_features_age_group' => 'int',
                    'instance_of_tax' => 'int',
                    'get_attribute_color' => 'text',
                    'get_attribute_material' => 'text',
                    'get_attribute_size' => 'text',
                    'get_attribute_pattern' => 'text',
                    'unique_product' => 'tinyint',
                    'identifier_exists' => 'tinyint',
                    'export_non_available' => 'tinyint',
                    'category_filter' => 'text',
                    'manufacturers_filter' => 'text',
                    'min_price_filter' => 'float',
                    'max_price_filter' => 'float',
                    'exclude_ids' => 'text',
                    'date_update' => 'date'
                )
            ),
            array(
                'name' => 'gshoppingfeed_taxonomy',
                'fields' => array(
                    'id_category' => 'int',
                    'id_taxonomy' => 'int',
                    'id_lang' => 'int',
                    'name_taxonomy' => 'text'
                )
            ),
            array(
                'name' => 'gshoppingfeed_custom_features',
                'fields' => array(
                    'id_gshoppingfeed' => 'int',
                    'id_feature' => 'int',
                    'unit' => 'varchar'
                )
            ),
            array(
                'name' => 'gshoppingfeed_custom_attributes',
                'fields' => array(
                    'id_gshoppingfeed' => 'int',
                    'id_attribute' => 'int',
                    'unit' => 'varchar'
                )
            )
        );

        foreach ($tables as $tableKey => $table) {
            $result = Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . pSQL($table['name']) . "'");
            if ($result) {
                $tableColumns = Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . pSQL($table['name']));
                if ($tableColumns && is_array($tableColumns) && count($tableColumns)) {
                    foreach ($tableColumns as $tableColumn) {
                        if (isset($tables[$tableKey]['fields'][$tableColumn['Field']])) {
                            if ($tableColumn['Field'] == 'min_price_filter' && $tableColumn['Type'] == 'int(11)') {
                                $sqlMod = 'ALTER TABLE `' . _DB_PREFIX_ . pSQL($table['name']) . '` MODIFY  `min_price_filter` FLOAT(10, 2)';
                                Db::getInstance()->execute($sqlMod);
                            }
                            if ($tableColumn['Field'] == 'max_price_filter' && $tableColumn['Type'] == 'int(11)') {
                                $sqlMod = 'ALTER TABLE `' . _DB_PREFIX_ . pSQL($table['name']) . '` MODIFY  `max_price_filter` FLOAT(10, 2)';
                                Db::getInstance()->execute($sqlMod);
                            }

                            unset($tables[$tableKey]['fields'][$tableColumn['Field']]);
                        }
                    }
                }
            }
        }

        foreach ($tables as $itemTable) {
            if (count($itemTable['fields'])) {
                $result = Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . pSQL($table['name']) . "'");
                if (!$result) {
                    $sql = 'CREATE TABLE IF NOT EXISTS `'. _DB_PREFIX_ . pSQL($table['name']).'` (';
                    $primary = '';
                    $sql_inner_fields = array();
                    foreach ($table['fields'] as $fieldKey => $field) {
                        $sql_inner_fields[] = '`' . pSQL($fieldKey) . '` ' . $this->getSqlByFieldType($field);
                        if ($field == 'int-autoincrement') {
                            $primary = ', PRIMARY KEY  (`' . pSQL($fieldKey) . '`)';
                        }
                    }
                    $sql .= join(', ', $sql_inner_fields) . $primary . ') ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
                    DB::getInstance()->execute($sql);
                } else {
                    foreach ($itemTable['fields'] as $fieldName => $fieldType) {
                        $tableType = '';
                        switch ($fieldType) {
                            case 'int-autoincrement':
                                $tableType = 'int(11) NOT NULL AUTO_INCREMENT';
                                break;
                            case 'int':
                                $tableType = 'INT(11)';
                                break;
                            case 'tinyint':
                                $tableType = 'TINYINT NOT NULL DEFAULT 0';
                                break;
                            case 'varchar':
                                $tableType = 'VARCHAR(256) NOT NULL';
                                break;
                            case 'text':
                                $tableType = 'TEXT DEFAULT NULL';
                                break;
                            case 'date':
                                $tableType = 'DATETIME NULL';
                                break;
                        }
                        try {
                            Db::getInstance()->execute('
                            ALTER TABLE `' . _DB_PREFIX_ . pSQL($itemTable['name']) . '`
                            ADD COLUMN `' . pSQL($fieldName) . '` ' . pSQL($tableType));
                        } catch (Exception $e) {
                            return array(
                                'error' => true,
                                'msg' => $e->getMessage()
                            );
                        }
                    }
                }
            }
        }

        return true;
    }

    private function getSqlByFieldType($fieldType)
    {
        switch ($fieldType) {
            case 'int-autoincrement':
                return 'int(11) NOT NULL AUTO_INCREMENT';
                break;
            case 'int':
                return 'INT(11)';
                break;
            case 'tinyint':
                return 'TINYINT(2) NOT NULL DEFAULT 0';
                break;
            case 'varchar':
                return 'VARCHAR(256) NOT NULL';
                break;
            case 'text':
                return 'TEXT DEFAULT NULL';
                break;
            case 'date':
                return 'DATETIME NULL';
                break;
        }
    }

    public function getFastLink()
    {
        if (Tools::isSubmit('updategshoppingfeed') && Validate::isInt(Tools::getValue('id_gshoppingfeed'))
            && (int)Tools::getValue('id_gshoppingfeed', 0) > 0
        ) {
            $this->context->smarty->assign(array('cron_link' => $this->context->link->getModuleLink($this->name, 'getList', array('key' => (int)Tools::getValue('id_gshoppingfeed'), 'token' => md5(_COOKIE_KEY_ . Tools::getValue('id_gshoppingfeed')))),
                'cron_link_rebuild' => $this->context->link->getModuleLink($this->name, 'getList', array('key' => (int)Tools::getValue('id_gshoppingfeed'), 'token' => md5(_COOKIE_KEY_ . Tools::getValue('id_gshoppingfeed')), 'only_rebuild' => '1')),
                'cron_link_download' => $this->context->link->getModuleLink($this->name, 'getList', array('key' => (int)Tools::getValue('id_gshoppingfeed'), 'token' => md5(_COOKIE_KEY_ . Tools::getValue('id_gshoppingfeed')), 'only_download' => '1'))
            ));
            return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/cron_link.tpl');
        }

        return false;
    }

    public function renderTaxonomyList()
    {
        $fields_list = array();
        $fields_list['name'] = array(
            'title' => $this->l('Name'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['iso_code'] = array(
            'title' => $this->l('ISO code'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['language_code'] = array(
            'title' => $this->l('Language code'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['taxonomy'] = array(
            'title' => $this->l('Lang'),
            'type' => 'taxonomy_exist',
            'search' => false,
            'orderby' => false,
            'class' => 'fixed-width-xs'
        );

        $helper = new HelperList();
        $helper->module = $this;
        $helper->simple_header = false;
        $helper->title = $this->l('Google taxonomy list');
        $helper->identifier = 'language_code';
        $helper->actions = array('edit');
        $helper->show_toolbar = true;
        $helper->shopLinkType = '';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->table = 'gshoppingfeed_taxonomy_lang_list';
        $helper->table_id = 'module-gshoppingfeed';
        $helper->currentIndex = self::$currentIndex . '&configure=' . urlencode($this->name);

        return $helper->generateList($this->getTaxLangList(), $fields_list);
    }

    public function formEditTaxonomyList()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'gshoppingfeed_taxonomy';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitTaxonomyLangEdit';

        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => array('language_code' => Tools::getValue('language_code')),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($this->getTaxonomyLangList()));
    }

    protected function getTaxonomyLangList()
    {
        $language_code = Tools::getValue('language_code');
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Taxonomy List Settings: ' . ((isset($language_code) && !empty($language_code)) ? $language_code : '')),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'label' => 'Taxonomy file ',
                        'name' => 'taxonomy_file',
                        'desc' => 'example file: https://www.google.com/basepages/producttype/taxonomy-with-ids.it-IT.txt'
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'language_code',
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            )
        );

        return $form;
    }

    public function renderLinksTaxonomyList($select_lang = '')
    {
        if (empty($select_lang) && !Validate::isInt($select_lang)) {
            $select_lang = $this->context->language->id;
        }

        $fields_list = array();
        $fields_list['id_category'] = array(
            'title' => $this->l('Category id'),
            'type' => 'text',
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'search' => false,
            'orderby' => false,
            'col' => 1
        );
        $fields_list['name'] = array(
            'title' => $this->l('Category name'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
            'class' => 'fixed-width-lg'
        );
        $fields_list['taxonomy_id'] = array(
            'title' => $this->l('Taxonomy id'),
            'type' => 'taxonomy_text',
            'search' => false,
            'orderby' => false,
            'class' => 'fixed-width-xs td_taxonomy_id'
        );
        $fields_list['name_taxonomy'] = array(
            'title' => $this->l('Taxonomy path'),
            'type' => 'taxonomy_text',
            'class' => 'taxonomy_breadcrumb',
            'remove_onclick' => true,
            'search' => false,
            'orderby' => false
        );
        $fields_list['taxonomy_lists'] = array(
            'title' => $this->l('Edit'),
            'type' => 'taxonomy_lists',
            'align' => 'right',
            'class' => 'text-right',
            'search' => false,
            'orderby' => false
        );

        $helper = new HelperList();
        $helper->module = $this;
        $helper->simple_header = false;
        $helper->title = $this->l('Google taxonomy link list');
        $helper->identifier = 'id_category';
        $helper->actions = array(); //'edit'
        $helper->show_toolbar = true;
        $helper->shopLinkType = '';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->table = 'gshoppingfeed_taxonomy';
        $helper->table_id = 'module-gshoppingfeed';
        $helper->currentIndex = self::$currentIndex . '&configure=' . urlencode($this->name);
        $helper->tpl_vars = array(
            'fields_value' => array('language_code' => Tools::getValue('language_code')),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id,
            'taxonomyLists' => array(), // self::getGoogleTxtCategoryFeed((int)Tools::getValue('getTaxonomyLang'), false),
            'update_path' => self::$currentIndex . '&configure=' . urlencode($this->name) . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&reloadTaxonomyLists'
        );

        return $helper->generateList($this->getTaxonomyLinks($select_lang), $fields_list);
    }

    public function getTaxonomyLinks($select_lang = '')
    {
        $categoryList = self::getAllCategoriesName(null, $select_lang);
        $categotyLinkId = $this->getTaxonomiesLinkid($select_lang);

        $catListLink = array();
        foreach ($categotyLinkId as $categotyLinkId_item) {
            $catListLink[$categotyLinkId_item['id_category']][$categotyLinkId_item['id_lang']] = $categotyLinkId_item;
            if ($isoLang = Language::getIsoById($categotyLinkId_item['id_lang'])) {
                $catListLink[$categotyLinkId_item['id_category']][$categotyLinkId_item['id_lang']]['iso'] = $isoLang;
            }
        }

        foreach ($categoryList as &$catItem) {
            $taxonomy_inf = array();
            foreach (Language::getLanguages(false) as $language) {
                if (isset($catListLink[$catItem['id_category']][$language['id_lang']]['id_taxonomy'])
                    && is_numeric($catListLink[$catItem['id_category']][$language['id_lang']]['id_taxonomy'])
                    && $catListLink[$catItem['id_category']][$language['id_lang']]['id_taxonomy'] > 0
                ) {
                    $taxonomy_inf['taxonomy_id'][] = array(
                        'iso' => $catListLink[$catItem['id_category']][$language['id_lang']]['iso'],
                        'item' => (int)$catListLink[$catItem['id_category']][$language['id_lang']]['id_taxonomy']
                    );
                }
                if (isset($catListLink[$catItem['id_category']][$language['id_lang']]['name_taxonomy'])
                    && !empty($catListLink[$catItem['id_category']][$language['id_lang']]['name_taxonomy'])
                ) {
                    $taxonomy_inf['name_taxonomy'][] = array(
                        'iso' => $catListLink[$catItem['id_category']][$language['id_lang']]['iso'],
                        'item' => $catListLink[$catItem['id_category']][$language['id_lang']]['name_taxonomy']
                    );
                }
            }
            if (isset($taxonomy_inf['taxonomy_id']) && isset($taxonomy_inf['name_taxonomy'])) {
                $catItem['taxonomy_id'] = (!empty($taxonomy_inf['taxonomy_id'])) ? $taxonomy_inf['taxonomy_id'] : '-';
                $catItem['name_taxonomy'] = (!empty($taxonomy_inf['name_taxonomy'])) ? $taxonomy_inf['name_taxonomy'] : '-';
            } else {
                $catItem['taxonomy_id'] = '-';
                $catItem['name_taxonomy'] = '-';
            }
        }

        return $categoryList;
    }

    public static function getAllCategoriesName($root_category = null, $id_lang = false, $active = true, $use_shop_restriction = true)
    {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cache_id = 'GShoppingFeed::getAllCategoriesName_' . md5((int)$root_category . (int)$id_lang . (int)$active . (int)$use_shop_restriction);
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('SELECT c.id_category, cl.name, cl.id_lang FROM `' . _DB_PREFIX_ . 'category` c ' . ($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
				' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int)$root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
				WHERE 1 ' . ($id_lang ? 'AND `id_lang` = ' . (int)$id_lang : '') . ' ' . ($active ? ' AND c.`active` = 1' : ''));
            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        return $result;
    }

    public function getTaxLangList()
    {
        $return = array();
        $langList = $this->context->controller->getLanguages();
        if (count($langList) > 0) {
            foreach ($langList as $langItem) {
                $taxonomy = 0;
                $iso_code = trim($langItem['iso_code']);
                $language_code = trim($langItem['language_code']);
                $language_code = Tools::strtolower($language_code);
                $_pathTaxonomy = _PS_MODULE_DIR_ . '/' . $this->name . '/google_taxonomy/' . $language_code . '/taxonomy-with-ids.' . $language_code . '.txt';
                if (file_exists($_pathTaxonomy)) {
                    $taxonomy = 1;
                }
                $return[] = array(
                    'name' => $langItem['name'],
                    'iso_code' => $iso_code,
                    'language_code' => $language_code,
                    'taxonomy' => $taxonomy
                );
            }
        }

        return $return;
    }

    // mod edit tax. category

    protected function renderTaxonomyEditForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'gshoppingfeed_taxonomy';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitTaxonomyEdit';

        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getTaxonomyData(),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigTaxonomyForm()));
    }

    protected function getTaxonomyData()
    {
        $return = array();
        $id_category = ((bool)Tools::isSubmit('id_category') === true && Validate::isInt(Tools::getValue('id_category'))) ? (int)Tools::getValue('id_category') : 0;
        $taxonomyCatLists = self::getTaxonomyLinkid($id_category);
        $return['id_category'] = $id_category;
        $taxonomy_tmp = array();
        if ($taxonomyCatLists && count($taxonomyCatLists) > 0) {
            foreach ($taxonomyCatLists as $taxonomyCatList) {
                $taxonomy_tmp[$taxonomyCatList['id_lang']] = $taxonomyCatList['id_taxonomy'];
            }
        }
        foreach (Language::getLanguages() as $language) {
            $return[$language['iso_code'] . '_taxonomy_linking'] = (isset($taxonomy_tmp[$language['id_lang']]) && Validate::isInt($taxonomy_tmp[$language['id_lang']])) ? (int)$taxonomy_tmp[$language['id_lang']] : 0;
        }

        return $return;
    }

    protected static function getTaxonomyLinkid($id_category)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'gshoppingfeed_taxonomy
                    WHERE `id_category` = ' . (int)$id_category;
        return Db::getInstance()->executeS($sql);
    }

    protected static function getTaxonomyCategoryLangLinkd($id_category, $lang)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'gshoppingfeed_taxonomy
                    WHERE `id_category` = ' . (int)$id_category . ' AND `id_lang` = ' . (int)$lang;
        return Db::getInstance()->getRow($sql);
    }

    protected static function getTaxonomiesLinkid($select_lang = '')
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'gshoppingfeed_taxonomy' . ((!empty($select_lang) && Validate::isInt($select_lang)) ? ' WHERE `id_lang` = ' . (int)$select_lang : '');
        return Db::getInstance()->executeS($sql);
    }

    protected function getConfigTaxonomyForm()
    {
        if (Validate::isInt(Tools::getValue('id_category')) && Tools::getValue('id_category') > 0) {
            $category_information = self::getProductPath((int)Tools::getValue('id_category'));
        }
        $category_link_name = (isset($category_information) && !empty($category_information)) ? (string)$category_information : '';

        $language_form = array();
        foreach (Language::getLanguages(false) as $language) {
            $language_form[] = array(
                'type' => 'select',
                'label' => $language['iso_code'] . ': ',
                'desc' => 'This category: ' . $category_link_name,
                'name' => $language['iso_code'] . '_taxonomy_linking',
                'class' => 'chosen fixed-width-all',
                'options' => array(
                    'query' => $this->getGoogleTxtCategoryFeed($language['id_lang']),
                    'id' => 'key',
                    'name' => 'name',
                    'default' => array(
                        'value' => 0,
                        'label' => $this->l('-- no selected --')
                    )
                )
            );
        }
        $language_form[] = array(
            'type' => 'hidden',
            'name' => 'id_category'
        );

        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Google Shopping Feed Taxonomy Settings:'),
                    'icon' => 'icon-cogs',
                ),
                'input' => $language_form,
                'submit' => array('title' => $this->l('Save'))
            )
        );

        return $form;
    }

    public function generationList($param = '', $type_generation = null)
    {
        if ((empty($param) || !isset($param['id_gshoppingfeed'])) && Tools::getValue('submitGshoppingfeedModule') && Tools::getValue('submitGshoppingfeedModule') == 1) {
            $param['id_gshoppingfeed'] = 'auto';
            $param['name_feed'] = (Tools::getValue('name_feed')) ? pSQL(Tools::getValue('name_feed')) : pSQL(Configuration::get('PS_SHOP_NAME'));
            $param['brand_type'] = (Tools::getValue('brand_type')) ? pSQL(Tools::getValue('brand_type')) : '';
            $param['only_active'] = (Tools::getValue('only_active') && Tools::getValue('only_active') == 1) ? 1 : 0;
            $param['description_crop'] = (Tools::getValue('description_crop') && Tools::getValue('description_crop') == 1) ? 1 : 0;
            $param['modify_uppercase_title'] = (Tools::getValue('modify_uppercase_title') && Tools::getValue('modify_uppercase_title') == 1) ? 1 : 0;
            $param['modify_uppercase_description'] = (Tools::getValue('modify_uppercase_description') && Tools::getValue('modify_uppercase_description') == 1) ? 1 : 0;
            $param['parts_payment_enabled'] = (Tools::getValue('parts_payment_enabled') && Tools::getValue('parts_payment_enabled') == 1) ? 1 : 0;
            $param['product_title_in_product_type'] = (Tools::getValue('product_title_in_product_type') && Tools::getValue('product_title_in_product_type') == 1) ? 1 : 0;
            $param['identifier_exists_mpn'] = (Tools::getValue('identifier_exists_mpn') && Tools::getValue('identifier_exists_mpn') == 1) ? 1 : 0;
            $param['visible_product_hide'] = (Tools::getValue('visible_product_hide') && Tools::getValue('visible_product_hide') == 1) ? 1 : 0;
            $param['mpn_force_on'] = (Tools::getValue('mpn_force_on') && Tools::getValue('mpn_force_on') == 1) ? 1 : 0;
            $param['max_parts_payment'] = (int)Tools::getValue('max_parts_payment', 1);
            $param['interest_rates'] = (float)Tools::getValue('interest_rates', 0);
            $param['exclude_ids'] = Tools::getValue('exclude_ids', '');
            $param['title_suffix'] = Tools::getValue('title_suffix', '');
            $param['description_suffix'] = Tools::getValue('description_suffix', '');
            $param['additional_each_product'] = Tools::getValue('additional_each_product', '');
            $param['export_attributes'] = (Tools::getValue('export_attributes') && Tools::getValue('export_attributes') == 1) ? (int)Tools::getValue('export_attributes') : 0;
            $param['filtered_by_associated_type'] = (Tools::getValue('filtered_by_associated_type') && Tools::getValue('filtered_by_associated_type') == 1) ? (int)Tools::getValue('filtered_by_associated_type') : 0;
            $param['export_attributes_only_first'] = (Tools::getValue('export_attributes_only_first') && Tools::getValue('export_attributes_only_first') == 1) ? (int)Tools::getValue('export_attributes_only_first') : 0;
            $param['export_attribute_url'] = (Tools::getValue('export_attribute_url') && Tools::getValue('export_attribute_url') == 1) ? (int)Tools::getValue('export_attribute_url') : 0;
            $param['export_attribute_prices'] = (Tools::getValue('export_attribute_prices') && Tools::getValue('export_attribute_prices') == 1) ? (int)Tools::getValue('export_attribute_prices') : 0;
            $param['export_attribute_images'] = (Tools::getValue('export_attribute_images') && Tools::getValue('export_attribute_images') == 1) ? (int)Tools::getValue('export_attribute_images') : 0;
            $param['export_feature'] = (Tools::getValue('export_feature') && Tools::getValue('export_feature') == 1) ? (int)Tools::getValue('export_feature') : 0;
            $param['use_additional_shipping_cost'] = (Tools::getValue('use_additional_shipping_cost') && Tools::getValue('use_additional_shipping_cost') == 1) ? (int)Tools::getValue('use_additional_shipping_cost') : 0;
            $param['type_image'] = (Tools::getValue('type_image') && Validate::isInt(Tools::getValue('type_image'))) ? (int)Tools::getValue('type_image') : 0;
            $param['type_description'] = (Tools::getValue('type_description') && Validate::isInt(Tools::getValue('type_description'))) ? (int)Tools::getValue('type_description') : 0;
            $param['id_currency'] = (Tools::getValue('id_currency') && Validate::isInt(Tools::getValue('id_currency'))) ? (int)Tools::getValue('id_currency') : 0;
            $param['id_country'] = (Tools::getValue('id_country') && Validate::isInt(Tools::getValue('id_country'))) ? (int)Tools::getValue('id_country') : 0;
            $param['id_carrier'] = (Tools::getValue('id_carrier') && is_array(Tools::getValue('id_carrier')) && count(Tools::getValue('id_carrier') > 0)) ? (array)Tools::getValue('id_carrier') : '';
            $param['select_lang'] = (Tools::getValue('select_lang') && Validate::isInt(Tools::getValue('select_lang'))) ? (int)Tools::getValue('select_lang') : 0;
            $param['get_features_gender'] = (Tools::getValue('get_features_gender') && Validate::isInt(Tools::getValue('get_features_gender'))) ? (int)Tools::getValue('get_features_gender') : 0;
            $param['get_features_age_group'] = (Tools::getValue('get_features_age_group') && Validate::isInt(Tools::getValue('get_features_age_group'))) ? (int)Tools::getValue('get_features_age_group') : 0;
            $param['instance_of_tax'] = (Tools::getValue('instance_of_tax') && Validate::isInt(Tools::getValue('instance_of_tax'))) ? (int)Tools::getValue('instance_of_tax') : 0;
            $param['get_attribute_size'] = (Tools::getValue('get_attribute_size') && is_array(Tools::getValue('get_attribute_size')) && count(Tools::getValue('get_attribute_size') > 0)) ? (array)Tools::getValue('get_attribute_size') : '';
            $param['get_attribute_color'] = (Tools::getValue('get_attribute_color') && is_array(Tools::getValue('get_attribute_color')) && count(Tools::getValue('get_attribute_color') > 0)) ? (array)Tools::getValue('get_attribute_color') : '';
            $param['get_attribute_pattern'] = (Tools::getValue('get_attribute_pattern') && is_array(Tools::getValue('get_attribute_pattern')) && count(Tools::getValue('get_attribute_pattern') > 0)) ? (array)Tools::getValue('get_attribute_pattern') : '';
            $param['get_attribute_material'] = (Tools::getValue('get_attribute_material') && is_array(Tools::getValue('get_attribute_material')) && count(Tools::getValue('get_attribute_material') > 0)) ? (array)Tools::getValue('get_attribute_material') : '';
            $param['export_non_available'] = (Tools::getValue('export_non_available') && Tools::getValue('export_non_available') == 1) ? 1 : 0;
            $param['export_product_quantity'] = (Tools::getValue('export_product_quantity') && Tools::getValue('export_product_quantity') == 1) ? (int)Tools::getValue('export_product_quantity') : 0;
            $param['additional_image'] = (Tools::getValue('additional_image') && Tools::getValue('additional_image') == 1) ? 1 : 0;
            $param['min_price'] = (Tools::getValue('min_price') && Validate::isInt(Tools::getValue('min_price'))) ? (int)Tools::getValue('min_price') : 0;
            $param['max_price'] = (Tools::getValue('max_price') && Validate::isInt(Tools::getValue('max_price'))) ? (int)Tools::getValue('max_price') : 0;
            $param['select_manufacturers'] = (Tools::getValue('select_manufacturers') && is_array(Tools::getValue('select_manufacturers')) && count(Tools::getValue('select_manufacturers') > 0)) ? (array)Tools::getValue('select_manufacturers') : '';
            $param['category_filter'] = (Tools::getValue('selected_categories') && is_array(Tools::getValue('selected_categories')) && count(Tools::getValue('selected_categories') > 0)) ? (array)Tools::getValue('selected_categories') : '';
            $param['mpn_type'] = (Tools::getValue('mpn_type') && is_array(Tools::getValue('mpn_type')) && count(Tools::getValue('mpn_type') > 0)) ? (array)Tools::getValue('mpn_type') : '';
            $param['gtin_type'] = (Tools::getValue('gtin_type') && is_array(Tools::getValue('gtin_type')) && count(Tools::getValue('gtin_type') > 0)) ? (array)Tools::getValue('gtin_type') : '';
        }

        $generate_path = _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $param['id_gshoppingfeed'];
        $generate_file = 'export.xml';
        $generate_path_file = $generate_path . DIRECTORY_SEPARATOR . $generate_file;

        if (!is_dir($generate_path)) {
            mkdir($generate_path, 0777, true);
        }

        if (file_exists($generate_path_file)) {
            unlink($generate_path_file);
        }

        if (isset($param['instance_of_tax'])) {
            switch ($param['instance_of_tax']) {
                case '0':
                    // when tax include
                    $this->useTax = true;
                    if (Group::getPriceDisplayMethod((int)Configuration::get('PS_GUEST_GROUP'))) {
                        // when tax exclude
                        $this->useTax = false;
                    }
                    break;
                case '1':
                    $this->useTax = true;
                    break;
                default:
                    $this->useTax = false;
                    break;
            }
        }

        $shop_url = Tools::getCurrentUrlProtocolPrefix() . $this->context->shop->domain_ssl . $this->context->shop->physical_uri;
        $id_lang = (int)$param['select_lang'];
        $image_convert = '';
        $imageType = ImageType::getImagesTypes();
        foreach ($imageType as $item) {
            if ($item['id_image_type'] == $param['type_image']) {
                $image_convert = $item['name'];
                break;
            }
        }

        $sql = new DbQuery();
        $sql->select('p.id_product, p.visibility, p.condition, p.reference, product_shop.id_product, pl.name, pl.link_rewrite,
        stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
        product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . (Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY')) . '" as new');
        switch ($param['type_description']) {
            case 0:
                $sql->select('pl.description `desc`');
                break;
            case 1:
                $sql->select('pl.description_short `desc`');
                break;
            case 2:
                $sql->select('pl.meta_description `desc`');
                break;
            default:
                $sql->select('pl.description `desc`');
                break;
        }
        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl'));
        if ($param['only_active'] == 1) {
            $sql->where('product_shop.`active` = 1');
        }
        $sql->select('image.`id_image` id_image');
        $sql->leftJoin('image', 'image', 'image.`id_product` = p.`id_product` AND image.cover=1');
        $includeIdsCategory = '';
        if (is_array($param['category_filter']) && count($param['category_filter']) > 0) {
            $includeIdsCategory = implode(',', array_map('intval', $param['category_filter']));
        }
        if (!empty($includeIdsCategory) && $param['filtered_by_associated_type']) {
            $sql->innerJoin('category_product', 'pc', 'pc.`id_product` = p.`id_product`');
            $sql->where('pc.`id_category` IN (' . $includeIdsCategory . ')');
        } elseif (!empty($includeIdsCategory)) {
            $sql->innerJoin('category', 'ct', 'ct.`id_category` = p.`id_category_default`');
            $sql->where('ct.`id_category` IN (' . $includeIdsCategory . ')');
        }
        $sql->select('m.`name` manufacturer_name');
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
        $sql->join(Product::sqlStock('p', 0));
        $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
        if ((bool)$param['export_non_available'] === true && $PS_STOCK_MANAGEMENT) {
            $sql->where('stock.quantity > 0');
        }
        $includeIdsManufacturers = '';
        if (is_array($param['select_manufacturers']) && count($param['select_manufacturers']) > 0) {
            $includeIdsManufacturers = implode(',', array_map('intval', $param['select_manufacturers']));
        }
        if (!empty($includeIdsManufacturers)) {
            $sql->where('m.`id_manufacturer` IN (' . $includeIdsManufacturers . ')');
        }
        $sql->select('gtx.`id_taxonomy`, gtx.`name_taxonomy`');
        $sql->innerJoin('gshoppingfeed_taxonomy', 'gtx', 'gtx.`id_category` = p.`id_category_default` AND gtx.`id_lang`=' . (int)$id_lang);
        $currecncy_default = Currency::getDefaultCurrency();
        if (!isset($param['id_currency']) || empty($param['id_currency'])) {
            $param['id_currency'] = $currecncy_default->id;
        }
        $currency_ratio = $currecncy_default->conversion_rate;
        if ($param['min_price'] > 0 || $param['max_price'] > 0) {
            if ((int)$param['id_currency'] != $currecncy_default->id) {
                $this_currecncy = Currency::getCurrency((int)$param['id_currency']);
                $currency_ratio = $this_currecncy['conversion_rate'];
            }
            if ($param['min_price'] > 0) {
                $sql->where(' (p.price * ' . (float)$currency_ratio . ') >= ' . $param['min_price']);
            }
            if ($param['max_price'] > 0) {
                $sql->where(' (p.price * ' . (float)$currency_ratio . ') <= ' . $param['max_price']);
            }
        }
        if (isset($param['exclude_ids']) && !empty($param['exclude_ids'])) {
            $explodeIDSArray = explode(',', $param['exclude_ids']);
            if (is_array($explodeIDSArray) && count($explodeIDSArray)) {
                $explodeIds = array();
                foreach ($explodeIDSArray as $explodeId) {
                    if (Validate::isInt($explodeId) && $explodeId > 0) {
                        $explodeIds[] = (int)$explodeId;
                    }
                }
                if (is_array($explodeIds) && count($explodeIds) > 0) {
                    $sql->where('p.id_product NOT IN (' . implode(',', array_map('intval', $explodeIds)) . ')');
                }
            }
        }
        $sql->groupBy('p.id_product');

        if (isset($param['visible_product_hide']) && $param['visible_product_hide']) {
            $sql->where('p.visibility != "none"');
        }
        $products_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        // Google Shopping XML
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $xml .= '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">' . "\n\n";
        $xml .= '<title><![CDATA[' . $param['name_feed'] . ']]></title>' . "\n";
        $xml .= '<link rel="self" href="' . $shop_url . '"/>' . "\n";
        $xml .= '<updated>' . date('Y-m-d') . 'T' . date('H:i:s') . 'Z</updated>' . "\n";

        $googleObject = fopen($generate_path_file, 'w');
        fwrite($googleObject, pack("CCC", 0xef, 0xbb, 0xbf));
        fwrite($googleObject, $xml);

        foreach ($products_list as $product_item) {
            $xml_entry = $this->getItemXML($product_item, $param, $id_lang, $image_convert);
            if ($xml_entry) {
                fwrite($googleObject, $xml_entry);
            }
        }

        $xml = '' . "\n" . '</feed>';
        fwrite($googleObject, $xml);
        fclose($googleObject);
        @chmod($googleObject, 0777);

        if ($type_generation != 'only_rebuild') {
            $download_file_name = Date('m-d-y') . '_google';
            header('Content-disposition: attachment; filename="' . $download_file_name . '.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
            readfile($generate_path_file);
        } else {
            echo $this->l('All done.');
        }

        exit();
    }

    public static function getProductAttributesIdsOverride($id_product, $onlyMain = false)
    {
        return Db::getInstance()->executeS('
		SELECT pa.id_product_attribute
		FROM `' . _DB_PREFIX_ . 'product_attribute` pa
		WHERE pa.`id_product` = ' . (int)$id_product .
            ($onlyMain ? ' AND pa.default_on = 1' : ''));
    }

    protected function getItemXML($product, $param, $id_lang, $image_type, $attribute_flag = false, $attribute_param = array(), $attribute_group = array())
    {
        $context = Context::getContext();
        $context->language = new Language($id_lang);
        $context->currency = new Currency((int)$param['id_currency'], (int)$id_lang);
        $_product = new Product((int)$product['id_product'], true, (int)$id_lang, null, $context);

        $combination_weight = 0;
        $combination_qty = $_product->quantity;
        if ($attribute_flag && $attribute_group['id_group'] > 0) {
            $combination = new Combination($attribute_group['id_group']);
            $combination_weight = $combination->weight;
            $combination_qty = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute_group['id_group']);
            unset($combination);
        }

        $continue_item = false;
        $xml_entry = '';

        if ($param['export_attributes'] > 0 && !$attribute_flag) {
            $attribute_report = false;
            $get_attribute_lists = array();



            if (isset($param['get_attribute_size']) && is_array($param['get_attribute_size']) && count($param['get_attribute_size']) > 0) {
                foreach ($param['get_attribute_size'] as $get_attribute_size) {
                    if (!in_array($get_attribute_size, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_size;
                    }
                }
            }
            if (isset($param['get_attribute_color']) && is_array($param['get_attribute_color']) && count($param['get_attribute_color']) > 0) {
                foreach ($param['get_attribute_color'] as $get_attribute_color) {
                    if (!in_array($get_attribute_color, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_color;
                    }
                }
            }
            if (isset($param['get_attribute_pattern']) && is_array($param['get_attribute_pattern']) && count($param['get_attribute_pattern']) > 0) {
                foreach ($param['get_attribute_pattern'] as $get_attribute_pattern) {
                    if (!in_array($get_attribute_pattern, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_pattern;
                    }
                }
            }
            if (isset($param['get_attribute_material']) && is_array($param['get_attribute_material']) && count($param['get_attribute_material']) > 0) {
                foreach ($param['get_attribute_material'] as $get_attribute_material) {
                    if (!in_array($get_attribute_material, $get_attribute_lists)) {
                        $get_attribute_lists[] = $get_attribute_material;
                    }
                }
            }

            if (is_array($param['custom_attribute']) && count($param['custom_attribute'])) {
                foreach ($param['custom_attribute'] as $customAttrParam) {
                    $get_attribute_lists[] = $customAttrParam['id_attribute'];
                }
            }

            $attr = array();
            $onlyMainAttribute = ($param['export_attributes_only_first']) ? true : false;
            $attributesLists = self::getProductAttributesIdsOverride($_product->id, $onlyMainAttribute);

            if ($attributesLists && is_array($attributesLists) && count($attributesLists) > 0) {
                foreach ($attributesLists as $attributesList) {
                    $attribute_values = Product::getAttributesParams($_product->id, $attributesList['id_product_attribute']);
                    if ($attribute_values && is_array($attribute_values) && count($attribute_values) > 0) {
                        foreach ($attribute_values as $attribute_value) {
                            if (in_array($attribute_value['id_attribute_group'], $get_attribute_lists)) {
                                $attr[$attributesList['id_product_attribute']][$attribute_value['id_attribute_group']] = $attribute_value['name'];
                                $attribute_report = true;
                            }
                        }
                    }

                    $combination = new Combination((int)$attributesList['id_product_attribute']);
                    $attr[$attributesList['id_product_attribute']]['ean13'] = $combination->ean13;
                    $attr[$attributesList['id_product_attribute']]['isbn'] = (isset($combination->isbn)) ? $combination->isbn : '';
                    $attr[$attributesList['id_product_attribute']]['upc'] = $combination->upc;
                    $attr[$attributesList['id_product_attribute']]['reference'] = $combination->reference;
                    $supplier_ref = ProductSupplier::getProductSupplierReference($_product->id, (int)$attributesList['id_product_attribute'], $_product->id_supplier);
                    $attr[$attributesList['id_product_attribute']]['supplier_reference'] = $supplier_ref;

                    unset($combination);
                }
            }

            $attr_variables = array(
                'get_attribute_size',
                'get_attribute_color',
                'get_attribute_pattern',
                'get_attribute_material'
            );
            $rebuild_arr = array();

            foreach ($attr as $key => &$item) {
                $exists = false;
                foreach ($item as $key_in => $item_in) {
                    foreach ($attr_variables as $attr_variables_item) {
                        if (isset($param[$attr_variables_item]) && is_array($param[$attr_variables_item])
                            && in_array($key_in, $param[$attr_variables_item])) {
                            $m = 0;
                            $repeat_param = array();
                            foreach ($param[$attr_variables_item] as $get_attribute_size_val) {
                                if (isset($item[$get_attribute_size_val])) {
                                    $repeat_param[] = $get_attribute_size_val;
                                    ++$m;
                                }
                            }
                            if ($m == 0) {
                                unset($item);
                            } elseif ($m >= 2) {
                                $rebuild_arr[$key] = array('data' => $item, 'attr' => $repeat_param);
                            }
                            $exists = true;
                        }
                    }

                    if (is_array($param['custom_attribute']) && count($param['custom_attribute'])) {
                        foreach ($param['custom_attribute'] as $customAttrParam) {
                            if ($key_in == $customAttrParam['id_attribute']) {
                                $m = 0;
                                $repeat_param = array();
                                if (isset($item[$customAttrParam['id_attribute']])) {
                                    $repeat_param[] = $customAttrParam['id_attribute'];
                                    ++$m;
                                }
                                if ($m == 0) {
                                    unset($item);
                                } elseif ($m >= 2) {
                                    $rebuild_arr[$key] = array('data' => $item, 'attr' => $repeat_param);
                                }
                                $exists = true;
                            }
                        }
                    }
                }
                if (!$exists) {
                    unset($attr[$key]);
                }
            }

            if (count($rebuild_arr) > 0) {
                $unset_list = array();
                foreach ($rebuild_arr as $rebuild_arr_key => $rebuild_arr_item) {
                    foreach ($rebuild_arr_item['attr'] as $item_key => $item_attr) {
                        foreach ($rebuild_arr_item['data'] as $item_data_key => $item_data_val) {
                            if (!in_array($item_data_key, $rebuild_arr_item['attr']) || $item_data_key != $item_attr) {
                                $attr[$rebuild_arr_key . '-' . $item_attr][$item_data_key] = $item_data_val;
                                $unset_list[] = $rebuild_arr_key;
                            }
                        }
                    }
                }
                if (isset($unset_list) && count($unset_list) > 0) {
                    foreach ($unset_list as $item_unset) {
                        if (isset($attr[$item_unset])) {
                            unset($attr[$item_unset]);
                        }
                    }
                }
            }

            if ($attribute_report) {
                foreach ($attr as $gr_id => $itm) {
                    $xml_entry .= $this->getItemXML($product, $param, $id_lang, $image_type, true, $itm, array('id_group' => $gr_id));
                }
                return $xml_entry;
            }
        }

        $xml_entry .= '<entry>' . "\n";
        if ($attribute_flag && $attribute_group['id_group'] > 0) {
            $xml_entry .= '<g:item_group_id>' . (int)$product['reference'] . '</g:item_group_id>' . "\n";
            $xml_entry .= '<g:id>' . (int)$product['reference'] . '-' . $attribute_group['id_group'] . '</g:id>' . "\n";
        } else {
            $xml_entry .= '<g:id>' . (int)$product['reference'] . '</g:id>' . "\n";
        }

        $replaceAttributeTitle = array();
        if ($attribute_flag && count($attribute_param) > 0) {
            if (isset($param['get_attribute_size']) && is_array($param['get_attribute_size']) && count($param['get_attribute_size']) > 0) {
                foreach ($param['get_attribute_size'] as $get_attribute_size) {
                    if (isset($attribute_param[$get_attribute_size]) && !empty($attribute_param[$get_attribute_size])) {
                        $replaceAttributeTitle['size'][] = $attribute_param[$get_attribute_size];
                    }
                }
            }
            if (isset($param['get_attribute_color']) && is_array($param['get_attribute_color']) && count($param['get_attribute_color']) > 0) {
                foreach ($param['get_attribute_color'] as $get_attribute_color) {
                    if (isset($attribute_param[$get_attribute_color]) && !empty($attribute_param[$get_attribute_color])) {
                        $replaceAttributeTitle['color'][] = $attribute_param[$get_attribute_color];
                    }
                }
            }
            if (isset($param['get_attribute_pattern']) && is_array($param['get_attribute_pattern']) && count($param['get_attribute_pattern']) > 0) {
                foreach ($param['get_attribute_pattern'] as $get_attribute_pattern) {
                    if (isset($attribute_param[$get_attribute_pattern]) && !empty($attribute_param[$get_attribute_pattern])) {
                        $replaceAttributeTitle['pattern'][] = $attribute_param[$get_attribute_pattern];
                    }
                }
            }
            if (isset($param['get_attribute_material']) && is_array($param['get_attribute_material']) && count($param['get_attribute_material']) > 0) {
                foreach ($param['get_attribute_material'] as $get_attribute_material) {
                    if (isset($attribute_param[$get_attribute_material]) && !empty($attribute_param[$get_attribute_material])) {
                        $replaceAttributeTitle['material'][] = $attribute_param[$get_attribute_material];
                    }
                }
            }
        }

        if (isset($param['title_suffix']) && !empty($param['title_suffix'])) {
            $new_name_field = str_replace('{title}', $product['name'], $param['title_suffix']);
            $new_name_field = str_replace('{manufacturer_name}', $product['manufacturer_name'], $new_name_field);

            $replace_size = (isset($replaceAttributeTitle['size']) && count($replaceAttributeTitle['size'])) ? join(', ', $replaceAttributeTitle['size']) : '';
            $new_name_field = str_replace('{size}', $replace_size, $new_name_field);

            $replace_color = (isset($replaceAttributeTitle['color']) && count($replaceAttributeTitle['color'])) ? join(', ', $replaceAttributeTitle['color']) : '';
            $new_name_field = str_replace('{color}', $replace_color, $new_name_field);

            $replace_pattern = (isset($replaceAttributeTitle['pattern']) && count($replaceAttributeTitle['pattern'])) ? join(', ', $replaceAttributeTitle['pattern']) : '';
            $new_name_field = str_replace('{pattern}', $replace_pattern, $new_name_field);

            $replace_material = (isset($replaceAttributeTitle['material']) && count($replaceAttributeTitle['material'])) ? join(', ', $replaceAttributeTitle['material']) : '';
            $new_name_field = str_replace('{material}', $replace_material, $new_name_field);

            if (!empty($new_name_field)) {
                if ($param['modify_uppercase_title'] == true) {
                    $new_name_field = Tools::strtolower($new_name_field);
                    $new_name_field = Tools::ucfirst($new_name_field);
                }

                $xml_entry .= '<g:title><![CDATA[' . $new_name_field . ']]></g:title>' . "\n";
            } else {
                if ($param['modify_uppercase_title'] == true) {
                    $product['name'] = Tools::strtolower($product['name']);
                    $product['name'] = Tools::ucfirst($product['name']);
                }

                $xml_entry .= '<g:title><![CDATA[' . $product['name'] . ']]></g:title>' . "\n";
            }
        } else {
            if ($param['modify_uppercase_title'] == true) {
                $product['name'] = Tools::strtolower($product['name']);
                $product['name'] = Tools::ucfirst($product['name']);
            }

            $xml_entry .= '<g:title><![CDATA[' . $product['name'] . ']]></g:title>' . "\n";
        }

        if (isset($param['description_suffix']) && !empty($param['description_suffix'])) {
            $new_desc_field = str_replace('{description}', $product['desc'], $param['description_suffix']);
            $desc = $this->clearHtmlTags($new_desc_field);
            if ($param['description_crop'] == 1) {
                $desc = mb_substr($desc, 0, 5000);
            }
            if ($param['modify_uppercase_description']) {
                $desc = $this->mbStrTolowerAfterPoint($desc);
            }
            $xml_entry .= '<g:description><![CDATA[' . $this->clearHtmlTags($desc) . ']]></g:description>' . "\n";
        } else {
            $desc = $this->clearHtmlTags($product['desc']);
            if ($param['description_crop'] == 1) {
                $desc = mb_substr($desc, 0, 5000);
            }
            if ($param['modify_uppercase_description']) {
                $desc = $this->mbStrTolowerAfterPoint($desc);
            }
            $xml_entry .= '<g:description><![CDATA[' . $desc . ']]></g:description>' . "\n";
        }

        if ($attribute_flag && $attribute_group['id_group'] && $param['export_attribute_url']) {
            $xml_entry .= '<g:link><![CDATA[' . $this->context->link->getProductLink((int)($product['id_product']), $product['link_rewrite'], null, null, $id_lang, null, (int)$attribute_group['id_group'], false, false, true) . ']]></g:link>' . "\n";
        } else {
            $xml_entry .= '<g:link><![CDATA[' . $this->context->link->getProductLink((int)($product['id_product']), $product['link_rewrite'], null, null, $id_lang) . ']]></g:link>' . "\n";
        }

        $defaultImageInsert = true;
        if ($attribute_flag && $attribute_group['id_group'] > 0 && $param['export_attribute_images'] == 1) {
            $thisGroup = explode('-', $attribute_group['id_group']);
            $groupImages = Product::_getAttributeImageAssociations($thisGroup[0]);
            if (is_array($groupImages) && count($groupImages)) {
                $imagesTMP = Image::getImages((int)$id_lang, (int)$product['id_product']);
                $newGroupImages = array();
                foreach ($imagesTMP as $imageTMP) {
                    if (in_array($imageTMP['id_image'], $groupImages)) {
                        $newGroupImages[] = (int)$imageTMP['id_image'];
                    }
                }
                $insert_default = false;
                $limit = 10;
                foreach ($newGroupImages as $id_image) {
                    if (!is_numeric($id_image)) {
                        continue;
                    }
                    if (!$insert_default) {
                        $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . (int)$id_image, $image_type);
                        $xml_entry .= '<g:image_link><![CDATA[' . $image . ']]></g:image_link>' . "\n";
                        $insert_default = true;
                    } else {
                        if (isset($param['additional_image']) && ((bool)$param['additional_image']) == true) {
                            if (($limit--) <= 0) {
                                break;
                            }
                            $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . (int)$id_image, $image_type);
                            $xml_entry .= '<g:additional_image_link><![CDATA[' . $image . ']]></g:additional_image_link>' . "\n";
                        }
                    }
                    $defaultImageInsert = false;
                }
            }
        }

        if ($defaultImageInsert) {
            $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . $product['id_image'], $image_type);
            $xml_entry .= '<g:image_link><![CDATA[' . $image . ']]></g:image_link>' . "\n";
            if (isset($param['additional_image']) && ((bool)$param['additional_image']) == true) {
                $images = Image::getImages((int)$id_lang, (int)$product['id_product']);
                $limit = 10;
                foreach ($images as $image) {
                    if ($image['cover'] == 1) {
                        continue;
                    }
                    if (($limit--) <= 0) {
                        break;
                    }
                    $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . $image['id_image'], $image_type);
                    $xml_entry .= '<g:additional_image_link><![CDATA[' . $image . ']]></g:additional_image_link>' . "\n";
                }
            }
        }

        //interest_rates
        if (isset($param['parts_payment_enabled']) && $param['parts_payment_enabled'] && isset($param['max_parts_payment'])
            && $param['max_parts_payment'] >= 1) {
            $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax), 2);
            $price_without_reduct = number_format($price_without_reduct, 2, '.', ' ');
            $xml_entry .= '<g:price>' . number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
            if ($param['interest_rates'] > 0 && $param['interest_rates'] <= 1) {
                $xml_entry .= '<g:sale_price>' . number_format((float)$price_without_reduct * (1 - $param['interest_rates']), 2, '.', '') . ' ' . $context->currency->iso_code . ' </g:sale_price>' . "\n";
            }
        } else {
            if ($attribute_flag && $attribute_group['id_group'] > 0 && $param['export_attribute_prices'] == 1) {
                $getGroup = explode('-', $attribute_group['id_group']);

                $price = round($_product->getPriceStatic($product['id_product'], $this->useTax, (int)$getGroup[0]), 2);
                $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax, (int)$getGroup[0]), 2);
                $price_without_reduct = number_format($price_without_reduct, 2, '.', ' ');

                if ((float)($price) < (float)str_replace(' ', '', $price_without_reduct)) {
                    $xml_entry .= '<g:price>' . number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                    $xml_entry .= '<g:sale_price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . ' </g:sale_price>' . "\n";
                    $xml_entry .= '<g:sale_price_effective_date>' . strtok($_product->specificPrice['from'], ' ') .'/'. strtok($_product->specificPrice['to'], ' ') . '</g:sale_price_effective_date>' . "\n";
                    if ($context->language->id == 1) $xml_entry .= '<g:custom_label >Скидка</g:custom_label >' . "\n";
                    if ($context->language->id == 2) $xml_entry .= '<g:custom_label >Знижка</g:custom_label >' . "\n";
                } else {
                    $xml_entry .= '<g:price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                }
            } else {
                $price = round($_product->getPriceStatic($product['id_product'], $this->useTax), 2);
            //    dump($product);
            //    die();
                $price_without_reduct = round($_product->getPriceWithoutReduct(!$this->useTax), 2);
                $price_without_reduct = number_format($price_without_reduct, 2, '.', ' ');
                if ((float)($price) < (float)str_replace(' ', '', $price_without_reduct)) {
                    $xml_entry .= '<g:price>' . number_format((float)str_replace(' ', '', $price_without_reduct), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                    $xml_entry .= '<g:sale_price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:sale_price>' . "\n";
                    $xml_entry .= '<g:sale_price_effective_date>' . strtok($_product->specificPrice['from'], ' ') .'/' . strtok($_product->specificPrice['to'], ' ') . '</g:sale_price_effective_date>' . "\n";
                    if ($context->language->id == 1) $xml_entry .= '<g:custom_label >Скидка</g:custom_label >' . "\n";
                    if ($context->language->id == 2) $xml_entry .= '<g:custom_label >Знижка</g:custom_label >' . "\n"; 
                } else {
                    $xml_entry .= '<g:price>' . number_format((float)$price, 2, '.', '') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                }
            }
        }

        if (isset($param['parts_payment_enabled']) && $param['parts_payment_enabled'] && isset($param['max_parts_payment'])
            && $param['max_parts_payment'] >= 1) {
            $convPrice = (float)str_replace(' ', '', $price_without_reduct) / (int)$param['max_parts_payment'];
            $xml_entry .= '<g:installment>' . "\n";
            $xml_entry .= '<g:months>' . (int)$param['max_parts_payment'] . '</g:months>' . "\n";
            $xml_entry .= '<g:amount>' . number_format((float)str_replace(' ', '', $convPrice), 2, '.', '') . ' ' . $context->currency->iso_code . '</g:amount>' . "\n";
            $xml_entry .= '</g:installment>' . "\n";
        }

        if ($attribute_flag) {
            if ((int)$combination_qty || !$param['export_product_quantity']) {
                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
            } else {
                $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
                $PS_ORDER_OUT_OF_STOCK = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
                $curret_date = strtotime('now');
                $available_date = strtotime($_product->available_date);
                if ($PS_STOCK_MANAGEMENT > 0) {
                    if ($product['out_of_stock'] == 0) {
                        $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                    } elseif ($product['out_of_stock'] == 1) {
                        if ($_product->available_date != '0000-00-00' && ($available_date > $curret_date)) {
                            $datetime1 = date_create($_product->available_date);
                            $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                            $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                        } else {
                            $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                        }
                    } else {
                        if ($PS_ORDER_OUT_OF_STOCK) {
                            if ($_product->available_date != '0000-00-00' && ($available_date > $curret_date)) {
                                $datetime1 = date_create($_product->available_date);
                                $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                                $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                            } else {
                                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                            }
                        } else {
                            $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                        }
                    }
                } else {
                    $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                }
            }
        } else {
            if ((int)$_product->quantity || !$param['export_product_quantity']) {
                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
            } else {
                $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
                $PS_ORDER_OUT_OF_STOCK = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
                $curret_date = strtotime('now');
                $available_date = strtotime($_product->available_date);
                if ($PS_STOCK_MANAGEMENT > 0) {
                    if ($product['out_of_stock'] == 0) {
                        $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                    } elseif ($product['out_of_stock'] == 1) {
                        if ($_product->available_date != '0000-00-00' && ($available_date > $curret_date)) {
                            $datetime1 = date_create($_product->available_date);
                            $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                            $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                        } else {
                            $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                        }
                    } else {
                        if ($PS_ORDER_OUT_OF_STOCK) {
                            if ($_product->available_date != '0000-00-00' && ($available_date > $curret_date)) {
                                $datetime1 = date_create($_product->available_date);
                                $xml_entry .= '<g:availability>preorder</g:availability>' . "\n";
                                $xml_entry .= '<g:availability_date>' . $datetime1->format("c") . '</g:availability_date>' . "\n";
                            } else {
                                $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                            }
                        } else {
                            $xml_entry .= '<g:availability>out of stock</g:availability>' . "\n";
                        }
                    }
                } else {
                    $xml_entry .= '<g:availability>in stock</g:availability>' . "\n";
                }
            }
        }
        if (isset($product['id_taxonomy'])
            && Validate::isInt($product['id_taxonomy'])
            && (int)$product['id_taxonomy'] > 0
            && isset($product['name_taxonomy'])
            && !empty($product['name_taxonomy'])
        ) {
            $xml_entry .= '<g:google_product_category><![CDATA[' . $product['name_taxonomy'] . ']]></g:google_product_category>' . "\n";
        } else {
            $continue_item = true;
        }

        $fullpath = self::getProductPath((int)$_product->id_category_default, $_product->name, $param['product_title_in_product_type'], $context);
        $xml_entry .= '<g:product_type><![CDATA[' . $fullpath . ']]></g:product_type>' . "\n";
    //    $xml_entry .= '<g:unit_pricing_base_measure><![CDATA[' . $_product->unity . ']]></g:unit_pricing_base_measure>' . "\n"; 
        /*  Brand :
         *  identifier_exists : если нет Brand И GTIN или MPN
         * 1.   Condition: new | - (опции в админке НЕ выводить - работает по умолчанию:  если NEW и BRAND,GTIN,MPN === 0
         *  то identifier_exists == NO и в XML идет только 2 параметра CONDITION=NEW и identifier_exists=NO)
         * 2.   Brand!='' и (GTIN=='' и MPN =='') и Condition == NEW  ->  identifier_exists == NO | CONDITION=NEW
         *                                   и Condition != NEW  ->  identifier_exists == YES | CONDITION=(VARIABLE FROM CONDITION)
         * 3.   Brand!='' и (GTIN != '' ИЛИ MPN != '') ->  identifier_exists == YES   //
         * */
        $identifier_brand_exists = false;
        if (!empty($_product->manufacturer_name) && $param['brand_type'] == 'manufacturer') {
            $identifier_brand_exists = true;
            $xml_entry .= '<g:brand><![CDATA[' . $this->clearHtmlTags($_product->manufacturer_name) . ']]></g:brand>' . "\n";
        } elseif ($param['brand_type'] == 'supplier' && isset($_product->supplier_name) && !empty($_product->supplier_name)) {
            $identifier_brand_exists = true;
            $xml_entry .= '<g:brand><![CDATA[' . $this->clearHtmlTags($_product->supplier_name) . ']]></g:brand>' . "\n";
        }

        $identifier_gtin_exists = false;
        if (isset($param['gtin_type']) && is_array($param['gtin_type'])) {
            foreach ($param['gtin_type'] as $gtinTypeItem) {
                switch ($gtinTypeItem) {
                    case 'ean_13_jan':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['ean13'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['ean13'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->ean13) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->ean13 . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'upc':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['upc'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['upc'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->upc) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->upc . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'isbn':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['isbn'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['isbn'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->isbn) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->isbn . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'reference':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['reference'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['reference'] . ']]></g:gtin>' . "\n";
                        } elseif (!empty($_product->reference) && !$attribute_flag) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $_product->reference . ']]></g:gtin>' . "\n";
                        }
                        break;
                    case 'supplier_reference':
                        if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['supplier_reference'])) {
                            $identifier_gtin_exists = true;
                            $xml_entry .= '<g:gtin><![CDATA[' . $attribute_param['supplier_reference'] . ']]></g:gtin>' . "\n";
                        } elseif (!$attribute_flag) {
                            $idProductAttribute = null;
                            $supplier_ref = ProductSupplier::getProductSupplierReference($_product->id, $idProductAttribute, $_product->id_supplier);
                            if ($supplier_ref && !empty($supplier_ref)) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:gtin><![CDATA[' . $supplier_ref . ']]></g:gtin>' . "\n";
                            }
                        }
                        break;
                }
                if ($identifier_gtin_exists) {
                    break;
                }
            }
        }
        $identifier_only_gtin_exists = $identifier_gtin_exists;

        if (!$identifier_gtin_exists || $param['mpn_force_on']) {
            if (isset($param['mpn_type']) && is_array($param['mpn_type'])) {
                foreach ($param['mpn_type'] as $mpnTypeItem) {
                    switch ($mpnTypeItem) {
                        case 'ean_13_jan':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['ean13'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['ean13'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->ean13) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->ean13 . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'upc':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['upc'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['upc'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->upc) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->upc . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'isbn':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['isbn'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['isbn'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->isbn) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->isbn . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'reference':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['reference'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['reference'] . ']]></g:mpn>' . "\n";
                            } elseif (!empty($_product->reference) && !$attribute_flag) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $_product->reference . ']]></g:mpn>' . "\n";
                            }
                            break;
                        case 'supplier_reference':
                            if ($attribute_flag && $attribute_group['id_group'] && !empty($attribute_param['supplier_reference'])) {
                                $identifier_gtin_exists = true;
                                $xml_entry .= '<g:mpn><![CDATA[' . $attribute_param['supplier_reference'] . ']]></g:mpn>' . "\n";
                            } elseif (!$attribute_flag) {
                                $idProductAttribute = null;
                                $supplier_ref = ProductSupplier::getProductSupplierReference($_product->id, $idProductAttribute, $_product->id_supplier);
                                if ($supplier_ref && !empty($supplier_ref)) {
                                    $identifier_gtin_exists = true;
                                    $xml_entry .= '<g:mpn><![CDATA[' . $supplier_ref . ']]></g:mpn>' . "\n";
                                }
                            }
                            break;
                    }
                    if ($identifier_gtin_exists) {
                        break;
                    }
                }
            }
        }

        if (!$param['identifier_exists_mpn'] && $identifier_gtin_exists || $identifier_only_gtin_exists) {
            $xml_entry .= '<g:identifier_exists>yes</g:identifier_exists>' . "\n";
        } else {
            $xml_entry .= '<g:identifier_exists>no</g:identifier_exists>' . "\n";
        }

      //  if (isset($_product->condition) && in_array($_product->condition, array('new', 'used', 'refurbished'))) {
            $xml_entry .= '<g:condition><![CDATA[new]]></g:condition>' . "\n";
      //  }
        if ($param['export_feature'] > 0) {
            $features_exp = array();
            $flists = $_product->getFeatures();
            if ($flists && is_array($flists) && count($flists) > 0) {
                foreach ($flists as $flist) {
                    $val_lists = FeatureValue::getFeatureValueLang($flist['id_feature_value']);
                    if ($val_lists && is_array($val_lists) && count($val_lists) > 0) {
                        foreach ($val_lists as $val_list) {
                            $features_exp[$flist['id_feature']][$val_list['id_lang']] = $val_list['value'];
                        }
                    }
                }
            }
            if (isset($param['get_features_gender']) && !empty($param['get_features_gender']) && Validate::isInt($param['get_features_gender'])
                && isset($features_exp[$param['get_features_gender']][$id_lang]) && !empty($features_exp[$param['get_features_gender']][$id_lang])
            ) {
                $xml_entry .= '<g:gender>' . $features_exp[$param['get_features_gender']][$id_lang] . '</g:gender>' . "\n";
            }
            if (isset($param['get_features_age_group']) && !empty($param['get_features_age_group']) && Validate::isInt($param['get_features_age_group'])
                && isset($features_exp[$param['get_features_age_group']][$id_lang]) && !empty($features_exp[$param['get_features_age_group']][$id_lang])
            ) {
                $xml_entry .= '<g:age_group>' . $features_exp[$param['get_features_age_group']][$id_lang] . '</g:age_group>' . "\n";
            }
        }

        if ($attribute_flag && count($attribute_param) > 0) {
            if (isset($param['get_attribute_size']) && is_array($param['get_attribute_size']) && count($param['get_attribute_size']) > 0) {
                foreach ($param['get_attribute_size'] as $get_attribute_size) {
                    if (isset($attribute_param[$get_attribute_size]) && !empty($attribute_param[$get_attribute_size])) {
                        $xml_entry .= '<g:size><![CDATA[' . $attribute_param[$get_attribute_size] . ']]></g:size>' . "\n";
                    }
                }
            }
            if (isset($param['get_attribute_color']) && is_array($param['get_attribute_color']) && count($param['get_attribute_color']) > 0) {
                foreach ($param['get_attribute_color'] as $get_attribute_color) {
                    if (isset($attribute_param[$get_attribute_color]) && !empty($attribute_param[$get_attribute_color])) {
                        $xml_entry .= '<g:color><![CDATA[' . $attribute_param[$get_attribute_color] . ']]></g:color>' . "\n";
                    }
                }
            }
            if (isset($param['get_attribute_pattern']) && is_array($param['get_attribute_pattern']) && count($param['get_attribute_pattern']) > 0) {
                foreach ($param['get_attribute_pattern'] as $get_attribute_pattern) {
                    if (isset($attribute_param[$get_attribute_pattern]) && !empty($attribute_param[$get_attribute_pattern])) {
                        $xml_entry .= '<g:pattern><![CDATA[' . $attribute_param[$get_attribute_pattern] . ']]></g:pattern>' . "\n";
                    }
                }
            }
            if (isset($param['get_attribute_material']) && is_array($param['get_attribute_material']) && count($param['get_attribute_material']) > 0) {
                foreach ($param['get_attribute_material'] as $get_attribute_material) {
                    if (isset($attribute_param[$get_attribute_material]) && !empty($attribute_param[$get_attribute_material])) {
                        $xml_entry .= '<g:material><![CDATA[' . $attribute_param[$get_attribute_material] . ']]></g:material>' . "\n";
                    }
                }
            }

            if (isset($param['custom_attribute']) && is_array($param['custom_attribute'])
                && count($param['custom_attribute'])) {
                foreach ($param['custom_attribute'] as $customAttr) {
                    if (isset($attribute_param[$customAttr['id_attribute']]) && !empty($attribute_param[$customAttr['id_attribute']])) {
                        $xml_entry .= '<g:' . $customAttr['unit'] . '><![CDATA[' . $attribute_param[$customAttr['id_attribute']] . ']]></g:' . $customAttr['unit'] . '>' . "\n";
                    }
                }
            }
        }

        $language = new Language($id_lang);
        $id_country = (int)$param['id_country'];
        if (!isset($param['id_country']) || !Validate::isInt($param['id_country'])) {
            $id_country = Country::getByIso($language->iso_code);
        }
        $country = new Country($id_country);
        $shipping_lists = array();
        if (is_array($param['id_carrier']) && count($param['id_carrier']) > 0) {
            foreach ($param['id_carrier'] as $carrier) {
                if (Carrier::checkCarrierZone($carrier, (int)$country->id_zone)) {
                    $carrier = new Carrier($carrier);
                    if ((int)$carrier->shipping_method == 1) {
                        $weight = round($_product->weight, 2);
                        if ($attribute_flag && $attribute_group['id_group'] > 0 && is_numeric($combination_weight) &&
                            $combination_weight > 0) {
                            $weight += round($combination_weight, 2);
                        }
                        $shipping = round($carrier->getDeliveryPriceByWeight($weight, (int)$country->id_zone) * (1 + ((float)Tax::getCarrierTaxRate((int)$carrier->id) / 100)), 2);
                        $id_default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                        if ($id_default_currency != (int)$context->currency->id) {
                            $shipping = Tools::convertPrice($shipping, (int)$context->currency->id);
                        }
                        $shipping_lists[] = array(
                            'country' => $country->iso_code,
                            'service' => (!empty($carrier->name)) ? $carrier->name : 'Standard',
                            'price' => ((!empty($shipping) && is_numeric($shipping) && $shipping > 0) ? $shipping : 0)
                        );
                    } elseif ((int)$carrier->shipping_method == 2) {
                        $price = round($_product->getPriceStatic((int)$product['id_product'], $this->useTax), 2);
                        $shipping = round($carrier->getDeliveryPriceByPrice($price, (int)$country->id_zone, (int)$context->currency->id) * (1 + ((float)Tax::getCarrierTaxRate((int)$carrier->id) / 100)), 2);
                        $id_default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                        if ($id_default_currency != (int)$context->currency->id) {
                            $shipping = Tools::convertPrice($shipping, (int)$context->currency->id);
                        }
                        $shipping_lists[] = array(
                            'country' => $country->iso_code,
                            'service' => (!empty($carrier->name)) ? $carrier->name : 'Standard',
                            'price' => ((!empty($shipping) && is_numeric($shipping) && $shipping > 0) ? $shipping : 0)
                        );
                    }
                }
            }
        }

        $additional_shipping_cost = 0;
        if ($param['use_additional_shipping_cost']) {
            $additional_shipping_cost = (isset($_product->additional_shipping_cost)
                && !empty($_product->additional_shipping_cost)
                && is_numeric($_product->additional_shipping_cost)) ? (float)$_product->additional_shipping_cost : 0;
        }

        if (count($shipping_lists) > 0) {
            foreach ($shipping_lists as $shipping_list) {
                $shipping_list['price'] += $additional_shipping_cost;
                $xml_entry .= '<g:shipping>' . "\n";
                $xml_entry .= "\t" . '<g:country>' . $shipping_list['country'] . '</g:country>' . "\n";
                $xml_entry .= "\t" . '<g:service>' . $shipping_list['service'] . '</g:service>' . "\n";
                if (!empty($shipping_list['price'])) {
                    $xml_entry .= "\t" . '<g:price>' . number_format($shipping_list['price'], 2, '.', ' ') . ' ' . $context->currency->iso_code . '</g:price>' . "\n";
                } else {
                    $xml_entry .= "\t" . '<g:price> 0.00 ' . $context->currency->iso_code . '</g:price>' . "\n";
                }
                $xml_entry .= '</g:shipping>' . "\n";
            }
        }

        if (!empty($_product->weight) && is_numeric($_product->weight) && $_product->weight > 0) {
            $weight = round($_product->weight, 2);
            if ($attribute_flag && $attribute_group['id_group'] > 0 && is_numeric($combination_weight) &&
                $combination_weight > 0) {
                $weight += round($combination_weight, 2);
            }

            $xml_entry .= '<g:shipping_weight>' . number_format($weight, 2, '.', '') . ' ' . Configuration::get('PS_WEIGHT_UNIT') . '</g:shipping_weight>' . "\n";
        }

        $featureLists = $_product::getFrontFeaturesStatic((int)$context->language->id, (int)$product['id_product']);
        if (isset($param['features_custom_modification']) && is_array($param['features_custom_modification'])
            && count($param['features_custom_modification']) && $featureLists && count($featureLists) > 0) {
            foreach ($param['features_custom_modification'] as $custom_param) {
                if (empty($custom_param['id_feature'])) {
                    continue;
                }
                foreach ($featureLists as $featureItem) {
                    if ($featureItem['id_feature'] == $custom_param['id_feature']) {
                        $xml_entry .= '<' . $custom_param['unit'] . '><![CDATA[' . $this->clearHtmlTags($featureItem['value']) . ']]></' . $custom_param['unit'] . '>' . "\n";
                    }
                }
            }
        }

        if (!empty($param['additional_each_product'])) {
            $additionalLines = explode(PHP_EOL, $param['additional_each_product']);
            if (is_array($additionalLines) && count($additionalLines)) {
                foreach ($additionalLines as $additionalLine) {
                    $additionalLine = trim($additionalLine);
                    if (!empty($additionalLine)) {
                        $xml_entry .= $additionalLine;
                    }
                }
            }
        }

        $xml_entry .= '</entry>' . "\n\n";

        unset($context);
        unset($_product);
        return (!$continue_item) ? $xml_entry : '';
    }

    protected function mbStrTolowerAfterPoint($text = '')
    {
        if (empty($text)) {
            return '';
        }

        $text = Tools::strtolower($text);
        $pointExplode = explode('. ', $text);
        $pointExplode = array_map(function ($data) {
            $fc = mb_strtoupper(mb_substr($data, 0, 1));
            return $fc . mb_substr($data, 1);
        }, $pointExplode);

        return join('. ', $pointExplode);
    }

    public static function getProductPath($id_category, $path = '', $product_title_in_product_type = 0, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_category = (int)$id_category;
        if ($id_category == 1) {
            return $path;
        }

        $pipe = Configuration::get('PS_NAVIGATION_PIPE');
        if (empty($pipe)) {
            $pipe = '>';
        }

        $full_path = '';
        $interval = Category::getInterval($id_category);
        $id_root_category = $context->shop->getCategory();
        $interval_root = Category::getInterval($id_root_category);
        if ($interval) {
            $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
                    FROM ' . _DB_PREFIX_ . 'category c
                    LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category' . Shop::addSqlRestrictionOnLang('cl') . ')
                    ' . Shop::addSqlAssociation('category', 'c') . '
                    WHERE c.nleft <= ' . (int)$interval['nleft'] . '
                        AND c.nright >= ' . (int)$interval['nright'] . '
                        AND c.nleft >= ' . (int)$interval_root['nleft'] . '
                        AND c.nright <= ' . (int)$interval_root['nright'] . '
                        AND cl.id_lang = ' . (int)$context->language->id . '
                        AND c.active = 1
                        AND c.level_depth > ' . (int)$interval_root['level_depth'] . '
                    ORDER BY c.level_depth ASC';
            $categories = Db::getInstance()->executeS($sql);

            if (!$product_title_in_product_type) {
                $path = '';
            }
            
            $n = 1;
            $n_categories = count($categories);
            foreach ($categories as $category) {
                $full_path .=
                    htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8') .
                    (($n++ != $n_categories || !empty($path)) ? " " . $pipe . " " : '');
            }

            return $full_path . $path;
        }
    }

    private function clearHtmlTags($string)
    {
        $string = Tools::getDescriptionClean($string);
        $string = preg_replace('/<[^>]*>/', ' ', $string);
        $string = str_replace("\r", '', $string);
        $string = str_replace("\n", ' ', $string);
        $string = str_replace("\t", ' ', $string);
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
        $string = preg_replace('/[\x00-\x1F\x7F]/', '', $string);

        return $string;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGshoppingfeedModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id,
        );
        $form_config = array(
            'form' => array(
                'id_form' => 'form-gshoppingfeed_action',
                'anchor' => ((Tools::isSubmit('updategshoppingfeed') && Tools::getValue('id_gshoppingfeed') > 0) ? 1 : 0),
                'getLink' => ((Tools::isSubmit('generationlink') && Tools::getValue('generationlink') > 0) ? 1 : 0),
                'newForm' => ((Tools::isSubmit('newexport') && Tools::getValue('newexport') == 1) ? 1 : 0),
            )
        );
        return $helper->generateForm(array($this->getConfigForm(), 'form' => $form_config));
    }

    protected static function getGoogleTxtCategoryFeed($lang = '', $only_list = false)
    {
        if (empty($lang) || !Validate::isInt($lang)) {
            $lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $langInfo = Language::getLanguage((int)$lang);
        $language_code = trim($langInfo['language_code']);
        $language_code = Tools::strtolower($language_code);
        $file_name = 'taxonomy-with-ids.' . $language_code . '.txt';
        $_pathTaxonomy = _PS_MODULE_DIR_ . 'gshoppingfeed' . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($_pathTaxonomy)) {
            return array();
        }

        $googleShoppingFeedCategory = array();
        $handle = fopen($_pathTaxonomy, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if ($line[0] != '#') {
                    $lineGSF = explode(' - ', $line);
                    $keyGSF = array_shift($lineGSF);
                    $keyGSF = trim($keyGSF);
                    $valGSF = join(' - ', $lineGSF);
                    $valGSF = trim($valGSF);
                    if (!$only_list) {
                        $googleShoppingFeedCategory[] = array('key' => $keyGSF, 'name' => $valGSF);
                    } else {
                        $googleShoppingFeedCategory[$keyGSF] = $valGSF;
                    }
                }
            }

            fclose($handle);
        }

        return $googleShoppingFeedCategory;
    }

    public function renderList()
    {
        $field_value = $this->getExistTemplate();
        $this->fields_list = array();
        $this->fields_list['name'] = array(
            'title' => $this->l('Data feed name'),
            'type' => 'text',
            'search' => false,
            'class' => 'col-md-5',
            'orderby' => false);
        $this->fields_list['select_lang'] = array(
            'title' => $this->l('Lang'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
            'class' => 'col-md-1');
        $this->fields_list['additional_image'] = array(
            'title' => $this->l('Additional image'),
            'active' => 'additional_image',
            'align' => 'text-center',
            'type' => 'bool',
            'search' => false,
            'orderby' => false,
            'class' => 'fixed-width-sm'
        );
        $this->fields_list['type_description'] = array(
            'title' => $this->l('Description type'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $this->fields_list['date_update'] = array(
            'title' => $this->l('Last update'),
            'type' => 'text',
            'search' => false,
            'orderby' => false
        );
        $this->fields_list['fast_generation'] = array(
            'title' => $this->l('Fast generation link'),
            'type' => 'fast_generation',
            'search' => false,
            'orderby' => false,
        );

        $helper = new HelperList();
        $helper->module = $this;
        $helper->simple_header = false;
        $helper->title = $this->l('History export configuration');
        $helper->identifier = 'id_gshoppingfeed';
        $helper->actions = array('edit', 'delete');
        $helper->listTotal = count($field_value);
        $helper->toolbar_btn['new'] = array(
            'href' => $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&module_name=' . $this->name
                . '&newexport=1&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new export')
        );

        $helper->show_toolbar = true;
        $helper->shopLinkType = '';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->table = 'gshoppingfeed';
        $helper->table_id = 'module-gshoppingfeed';
        $helper->currentIndex = self::$currentIndex . '&configure=' . urlencode($this->name);

        $helper->tpl_vars['allFeedLink'] = $this->context->link->getAdminLink('AdminModules', true, array(), array('showFeedLinks'=>1, 'configure'=>'gshoppingfeed'));

        return $helper->generateList($field_value, $this->fields_list);
    }

    protected function getExistTemplate()
    {
        $return_gsf = array();
        $descList = $this->getTypeDescription();
        $gshoppingfeed = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gshoppingfeed`
            GROUP BY `id_gshoppingfeed` ASC');
        if ($gshoppingfeed && count($gshoppingfeed) > 0) {
            foreach ($gshoppingfeed as &$gsfeed) {
                $gsfeed['type_description'] = (isset($gsfeed['type_description']) && isset($descList[$gsfeed['type_description']]['name'])) ? $descList[$gsfeed['type_description']]['name'] : ' - ';
                if (!isset($gsfeed['select_lang']) || empty($gsfeed['select_lang']) || !is_numeric($gsfeed['select_lang'])) {
                    $iso = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
                    $gsfeed['select_lang'] = Language::getLanguageCodeByIso($iso);
                } else {
                    $iso = Language::getIsoById((int)$gsfeed['select_lang']);
                    $gsfeed['select_lang'] = Language::getLanguageCodeByIso($iso);
                }
                $gsfeed['fast_generation'] = '';
            }
            if (is_array($gshoppingfeed) && count($gshoppingfeed) > 0) {
                $return_gsf = $gshoppingfeed;
            }
        }
        return $return_gsf;
    }

    protected function getConfigForm()
    {
        $attribute_lists = AttributeGroup::getAttributesGroups((int)$this->context->language->id);
        $feature_lists = Feature::getFeatures((int)$this->context->language->id);

        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Google Shopping Feed Settings:'),
                    'icon' => 'icon-cogs',
                ),
                'tabs' => array(
                    'set' => $this->l('General configuration'),
                    'set_filter' => $this->l('Filter configuration'),
                    'set_other' => $this->l('Other setting')
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'tab' => 'set',
                        'maxchar' => 50,
                        'class' => 'input fixed-width-xxl',
                        'label' => 'Name of your data feed',
                        'desc' => $this->l('No more than 50 characters'),
                        'name' => 'name_feed'
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Only Active product'),
                        'name' => 'only_active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Images type',
                        'name' => 'type_image',
                        'options' => array(
                            'query' => ImageType::getImagesTypes(null, true),
                            'id' => 'id_image_type',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Additional image'),
                        'desc' => $this->l('No more 10 image'),
                        'col' => 4,
                        'name' => 'additional_image',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => $this->l('Description type'),
                        'name' => 'type_description',
                        'options' => array(
                            'query' => $this->getTypeDescription(),
                            'id' => 'desc_key',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Your product’s description (Max 5000 characters)'),
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Products descriptions - Max size: 5000'),
                        'col' => 4,
                        'name' => 'description_crop',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Uppercase to lowercase for Title (yes / no)'),
                        'col' => 4,
                        'name' => 'modify_uppercase_title',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Uppercase to lowercase for Description (yes / no)'),
                        'col' => 4,
                        'name' => 'modify_uppercase_description',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => $this->l('Select Lang'),
                        'required' => true,
                        'name' => 'select_lang',
                        'options' => array(
                            'query' => Language::getLanguages(),
                            'id' => 'id_lang',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'required' => true,
                        'label' => $this->l('Currency'),
                        'name' => 'id_currency',
                        'options' => array(
                            'query' => Currency::getCurrencies(false, true, true),
                            'id' => 'id_currency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'required' => true,
                        'label' => $this->l('Select country, this need to calculate a shipping cost'),
                        'name' => 'id_country',
                        'multiple' => false,
                        'options' => array(
                            'query' => Country::getCountries((int)$this->context->language->id, true),
                            'id' => 'id_country',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'required' => true,
                        'label' => $this->l('Manage the shipping cost'),
                        'name' => 'id_carrier[]',
                        'class' => 'chosen',
                        'multiple' => true,
                        'options' => array(
                            'query' => Carrier::getCarriers((int)$this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
                            'id' => 'id_carrier',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Use Additional shipping cost'),
                        'col' => 4,
                        'name' => 'use_additional_shipping_cost',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Brand',
                        'col' => 4,
                        'name' => 'brand_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'name' => 'Manufacturer',
                                    'key' => 'manufacturer'
                                ),
                                array(
                                    'name' => 'Supplier',
                                    'key' => 'supplier'
                                ),
                            ),
                            'id' => 'key',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('For <g:brand> use manufacturer or supplier (default supplier) field')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'GTIN',
                        'col' => 4,
                        'name' => 'gtin_type[]',
                        'class' => 'chosen',
                        'multiple' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'name' => 'EAN-13 or JAN barcode',
                                    'key' => 'ean_13_jan'
                                ),
                                array(
                                    'name' => 'UPC barcode',
                                    'key' => 'upc'
                                ),
                                array(
                                    'name' => 'ISBN',
                                    'key' => 'isbn'
                                ),

                                array(
                                    'name' => 'Reference',
                                    'key' => 'reference'
                                ),
                                array(
                                    'name' => 'Supplier reference',
                                    'key' => 'supplier_reference'
                                ),
                            ),
                            'id' => 'key',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('A Global Trade Item Number (GTIN) is a unique and internationally recognized identifier for a product.')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Manufacturer Part Number (MPN)',
                        'col' => 4,
                        'name' => 'mpn_type[]',
                        'class' => 'chosen',
                        'multiple' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'name' => 'EAN-13 or JAN barcode',
                                    'key' => 'ean_13_jan'
                                ),
                                array(
                                    'name' => 'UPC barcode',
                                    'key' => 'upc'
                                ),
                                array(
                                    'name' => 'ISBN',
                                    'key' => 'isbn'
                                ),

                                array(
                                    'name' => 'Reference',
                                    'key' => 'reference'
                                ),
                                array(
                                    'name' => 'Supplier reference',
                                    'key' => 'supplier_reference'
                                ),
                            ),
                            'id' => 'key',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('Use the mpn attribute to submit your product’s Manufacturer Part Number (MPN). Required for all products without a manufacturer-assigned GTIN.')
                    ),
                    array(
                        'type' => 'html',
                        'tab' => 'set',
                        'col' => 4,
                        'name' => 'note_inf',
                        'html_content' => '<p>' . $this->l('Note: If your product is new (which you submit through the condition attribute) and it does not have a gtin and brand or mpn and brand then will submit the products without any gtin or mpn.') . '</p>'
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Exclude out of stock products'),
                        'desc' => $this->l('Works only when the StockManager is active, only for main products, doesn\'t work with combination'),
                        'col' => 4,
                        'name' => 'export_non_available',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Export product quantity'),
                        'col' => 4,
                        'name' => 'export_product_quantity',
                        'desc' => $this->l('If enabled: correct statuses: preorder / in stock / out of stock, else if disabled: all-stock in the status: in_stock'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Enable feature'),
                        'col' => 4,
                        'name' => 'export_feature',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Products gender feature',
                        'name' => 'get_features_gender',
                        'options' => array(
                            'query' => $feature_lists,
                            'id' => 'id_feature',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('male/female/unisex')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Products age group feature',
                        'name' => 'get_features_age_group',
                        'options' => array(
                            'query' => $feature_lists,
                            'id' => 'id_feature',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('newborn/infant/toddler/kids/adult
                            Required (For all clothing items that target Brazil, France, Germany, Japan, the UK and the US as well as all products with assigned age groups)
                            Your product’s targeted demographic
                        '),
                        'col' => 4
                    ),


                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Only one default product from Combination'),
                        'col' => 4,
                        'name' => 'export_attributes_only_first',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),


                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Export attributes combinations'),
                        'col' => 4,
                        'name' => 'export_attributes',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('URLs for combination (Yes / No)'),
                        'col' => 4,
                        'name' => 'export_attribute_url',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Use Combinations Prices (Yes/No)'),
                        'col' => 4,
                        'name' => 'export_attribute_prices',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set',
                        'label' => $this->l('Use Combinations Images (Yes/No)'),
                        'col' => 4,
                        'name' => 'export_attribute_images',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Products color attribute',
                        'name' => 'get_attribute_color[]',
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('Black, OR several attribute: Black/Green')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Products material attribute',
                        'name' => 'get_attribute_material[]',
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('Cotton OR several attribute: CottonPolyesterElastane -> /polyester/elastane')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Products pattern attribute',
                        'name' => 'get_attribute_pattern[]',
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('Enabled item group id')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set',
                        'label' => 'Products size attribute',
                        'name' => 'get_attribute_size[]',
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'col' => '4',
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',

                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        ),
                        'desc' => $this->l('XXS, XS, S, M, L, XL, 1XL, 2XL, 3XL, 4XL, 5XL, 6XL.
                                    00, 0, 02, 04, 06, 08, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34.
                                    23, 24, 26, 27, 28, 29, 30, 32, 34, 36, 38, 40, 42, 44...')
                    ),
                    array(
                        'type' => 'custom_attribute',
                        'tab' => 'set',
                        'label' => $this->l('Custom attribute set'),
                        'name' => 'custom_attribute[]',
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'col' => '6',
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set_filter',
                        'label' => $this->l('Filter by asssociated categories'),
                        'desc' => $this->l('If NO filtered works by Main category'),
                        'col' => 4,
                        'name' => 'filtered_by_associated_type',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('By categories'),
                        'name' => 'selected_categories',
                        'tab' => 'set_filter',
                        'tree' => array(
                            'id' => 'categories-tree',
                            'selected_categories' => $this->getCategoryFilterSelected(),
                            'root_category' => (int)Category::getRootCategory()->id,
                            'use_search' => true,
                            'use_checkbox' => true
                        ),
                        'desc' => $this->l('Default, all enabled categories')
                    ),
                    array(
                        'type' => 'select',
                        'label' => 'Select Manufacturers',
                        'name' => 'select_manufacturers[]',
                        'class' => 'chosen fixed-width-xxl',
                        'tab' => 'set_filter',
                        'multiple' => true,
                        'options' => array(
                            'query' => Manufacturer::getManufacturers(false, (int)Context::getContext()->cookie->id_lang),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'set_filter',
                        'class' => 'input fixed-width-md',
                        'label' => 'Minimum product price',
                        'desc' => $this->l('Only numeric 1, 2.55, 0.99, 3...9999'),
                        'name' => 'min_price'
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'set_filter',
                        'class' => 'input fixed-width-md',
                        'label' => 'Maximum product price',
                        'desc' => $this->l('Only numeric 1, 2.55, 0.99, 3...9999'),
                        'name' => 'max_price'
                    ),
                    array(
                        'type' => 'textarea',
                        'tab' => 'set_filter',
                        'col' => 6,
                        'class' => 'input',
                        'label' => 'Exclude products from the feed by ID',
                        'desc' => $this->l('For example: 1,3,25,36...'),
                        'name' => 'exclude_ids'
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'set_other',
                        'label' => 'Enable tax',
                        'name' => 'instance_of_tax',
                        'desc' => $this->l('Select whether or not to include tax on purchases.'),
                        'options' => array(
                            'query' => $this->getTaxOptions(),
                            'id' => 'id_tax',
                            'name' => 'name'
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'tab' => 'set_other',
                        'label' => $this->l('Use the product name in the product_type tag (In breadcrums)'),
                        'name' => 'product_title_in_product_type',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),

                    array(
                        'type' => 'switch',
                        'tab' => 'set_other',
                        'label' => $this->l('if product GTIN doesn\'t exist, identifier_exists will be always NO'),
                        'name' => 'identifier_exists_mpn',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set_other',
                        'label' => $this->l('Always show MPN'),
                        'name' => 'mpn_force_on',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set_other',
                        'label' => $this->l('Export only Visible products (Yes / No)'),
                        'name' => 'visible_product_hide',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'tab' => 'set_other',
                        'col' => 6,
                        'class' => 'input',
                        'label' => 'Additional product title',
                        'desc' => $this->l('Example: PREFIX {title} SUFFIX ( {color}, {material}, {pattern}, {size}, {manufacturer_name} )'),
                        'name' => 'title_suffix'
                    ),
                    array(
                        'type' => 'textarea_clean',
                        'tab' => 'set_other',
                        'col' => 6,
                        'rows' => 4,
                        'label' => $this->l('Additional product description'),
                        'desc' => $this->l('Example: PREFIX {description} SUFFIX'),
                        'name' => 'description_suffix'
                    ),
                    array(
                        'type' => 'textarea_clean',
                        'tab' => 'set_other',
                        'col' => 6,
                        'rows' => 4,
                        'label' => $this->l('Additional code for each product'),
                        'desc' => $this->l('Example: <g:{you custom field}>{you custom value}</g:{you custom field}>'),
                        'name' => 'additional_each_product'
                    ),
                    array(
                        'type' => 'custom_param',
                        'tab' => 'set_other',
                        'label' => $this->l('Custom parameters for properties (features)'),
                        'name' => 'features_custom_mod',
                        'features' => Feature::getFeatures($this->context->language->id),
                        'desc' => $this->l('Append new config row before close "</entry>" tag, example: "g:energy_efficiency_class"'),
                        'col' => 4
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'set_other',
                        'label' => $this->l('Installment (for Brasilia or Mexico)'),
                        'name' => 'parts_payment_enabled',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'set_other',
                        'class' => 'input fixed-width-md',
                        'label' => 'Maximum number of parts = k1',
                        'desc' => $this->l('Only integers 1,2,3...99 / price B = k1 (Price / k1)'),
                        'name' => 'max_parts_payment'
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'set_other',
                        'class' => 'input fixed-width-md',
                        'label' => 'Interest rates = K',
                        'desc' => $this->l('0.00 > 1.00 / price A = Price (1 - K)'),
                        'name' => 'interest_rates'
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_gshoppingfeed',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                ),
            )
        );
        if ((bool)Tools::isSubmit('updategshoppingfeed') === true && Tools::getValue('id_gshoppingfeed') > 0) {
            $form['form']['buttons'] = array(
                array(
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                    'title' => $this->l('Back to list'),
                    'icon' => 'process-icon-back'
                )
            );
        }

        return $form;
    }

    public function getTaxOptions()
    {
        return array(
            array(
                'id_tax' => 0,
                'name' => $this->l('Default (Guest)')
            ),
            array(
                'id_tax' => 1,
                'name' => $this->l('With Taxes')
            ),
            array(
                'id_tax' => 2,
                'name' => $this->l('Without Taxes')
            ),
        );
    }

    protected function getTypeDescription()
    {
        return
            array(
                '0' => array(
                    'desc_key' => '0',
                    'name' => $this->l('Long description')
                ),
                '1' => array(
                    'desc_key' => '1',
                    'name' => $this->l('Short description')
                ),
                '2' => array(
                    'desc_key' => '2',
                    'name' => $this->l('Meta Description')
                ),
            );
    }

    protected function getCategoryFilterSelected()
    {
        if ((bool)Tools::isSubmit('updategshoppingfeed') == true && Validate::isInt(Tools::getValue('id_gshoppingfeed'))) {
            $sql = 'SELECT `category_filter` FROM `' . _DB_PREFIX_ . 'gshoppingfeed` WHERE `id_gshoppingfeed` = ' . (int)Tools::getValue('id_gshoppingfeed');
            $gshoppingfeed = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if ($gshoppingfeed['category_filter'] && !empty($gshoppingfeed['category_filter'])) {
                return Tools::jsonDecode($gshoppingfeed['category_filter']);
            }
            return array();
        }
        return array();
    }

    protected function getConfigFormValues()
    {
        $ret = array(
            'name_feed' => '',
            'brand_type' => '',
            'only_active' => 1,
            'description_crop' => 0,
            'modify_uppercase_title' => 0,
            'modify_uppercase_description' => 0,
            'parts_payment_enabled' => 0,
            'product_title_in_product_type' => 0,
            'identifier_exists_mpn' => 0,
            'visible_product_hide' => 0,
            'mpn_force_on' => 0,
            'max_parts_payment' => 1,
            'interest_rates' => 0,
            'exclude_ids' => '',
            'title_suffix' => '',
            'description_suffix' => '',
            'additional_each_product' => '',
            'export_attributes' => 0,
            'filtered_by_associated_type' => 0,
            'export_attributes_only_first' => 0,
            'export_attribute_url' => 0,
            'export_attribute_prices' => 0,
            'export_attribute_images' => 0,
            'export_feature' => 0,
            'use_additional_shipping_cost' => 0,
            'type_image' => '0',
            'type_description' => '0',
            'get_features_gender' => '',
            'get_features_age_group' => '',
            'instance_of_tax' => '',
            'get_attribute_size[]' => '',
            'get_attribute_color[]' => '',
            'get_attribute_pattern[]' => '',
            'get_attribute_material[]' => '',
            'export_non_available' => 0,
            'export_product_quantity' => 1,
            'additional_image' => 1,
            'min_price' => '',
            'max_price' => '',
            'id_currency' => (int)$this->context->currency->id,
            'id_country' => Country::getByIso($this->context->language->iso_code),
            'id_carrier[]' => '',
            'select_lang' => (int)$this->context->language->id,
            'select_manufacturers[]' => '',
            'mpn_type[]' => '',
            'gtin_type[]' => '',
            'id_gshoppingfeed' => ''
        );

        if ((bool)Tools::isSubmit('updategshoppingfeed') == true
            && Validate::isInt(Tools::getValue('id_gshoppingfeed'))) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'gshoppingfeed` WHERE `id_gshoppingfeed` = ' . (int)Tools::getValue('id_gshoppingfeed');
            $gshoppingfeed = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if ($gshoppingfeed && count($gshoppingfeed) > 0) {
                $ret['name_feed'] = $gshoppingfeed['name'];
                $ret['brand_type'] = $gshoppingfeed['brand_type'];
                $ret['only_active'] = (int)$gshoppingfeed['only_active'];
                $ret['description_crop'] = (int)$gshoppingfeed['description_crop'];
                $ret['modify_uppercase_title'] = (int)$gshoppingfeed['modify_uppercase_title'];
                $ret['modify_uppercase_description'] = (int)$gshoppingfeed['modify_uppercase_description'];
                $ret['parts_payment_enabled'] = (int)$gshoppingfeed['parts_payment_enabled'];
                $ret['product_title_in_product_type'] = (int)$gshoppingfeed['product_title_in_product_type'];
                $ret['identifier_exists_mpn'] = (int)$gshoppingfeed['identifier_exists_mpn'];
                $ret['visible_product_hide'] = (int)$gshoppingfeed['visible_product_hide'];
                $ret['mpn_force_on'] = (int)$gshoppingfeed['mpn_force_on'];
                $ret['max_parts_payment'] = (int)$gshoppingfeed['max_parts_payment'];
                $ret['interest_rates'] = (float)$gshoppingfeed['interest_rates'];
                $ret['exclude_ids'] = $gshoppingfeed['exclude_ids'];
                $ret['title_suffix'] = $gshoppingfeed['title_suffix'];
                $ret['description_suffix'] = $gshoppingfeed['description_suffix'];
                $ret['additional_each_product'] = $gshoppingfeed['additional_each_product'];
                $ret['export_attributes'] = $gshoppingfeed['export_attributes'];
                $ret['filtered_by_associated_type'] = $gshoppingfeed['filtered_by_associated_type'];
                $ret['export_attributes_only_first'] = $gshoppingfeed['export_attributes_only_first'];
                $ret['export_attribute_url'] = $gshoppingfeed['export_attribute_url'];
                $ret['export_attribute_prices'] = $gshoppingfeed['export_attribute_prices'];
                $ret['export_attribute_images'] = $gshoppingfeed['export_attribute_images'];
                $ret['export_feature'] = $gshoppingfeed['export_feature'];
                $ret['use_additional_shipping_cost'] = $gshoppingfeed['use_additional_shipping_cost'];
                $ret['type_image'] = $gshoppingfeed['type_image'];
                $ret['type_description'] = $gshoppingfeed['type_description'];
                $ret['id_currency'] = $gshoppingfeed['id_currency'];
                $ret['id_country'] = $gshoppingfeed['id_country'];
                $ret['id_carrier[]'] = Tools::jsonDecode($gshoppingfeed['id_carrier']);
                $ret['select_lang'] = $gshoppingfeed['select_lang'];
                $ret['get_features_gender'] = $gshoppingfeed['get_features_gender'];
                $ret['get_features_age_group'] = $gshoppingfeed['get_features_age_group'];
                $ret['instance_of_tax'] = $gshoppingfeed['instance_of_tax'];
                $ret['get_attribute_size[]'] = Tools::jsonDecode($gshoppingfeed['get_attribute_size']);
                $ret['get_attribute_color[]'] = Tools::jsonDecode($gshoppingfeed['get_attribute_color']);
                $ret['get_attribute_pattern[]'] = Tools::jsonDecode($gshoppingfeed['get_attribute_pattern']);
                $ret['get_attribute_material[]'] = Tools::jsonDecode($gshoppingfeed['get_attribute_material']);
                $ret['export_non_available'] = $gshoppingfeed['export_non_available'];
                $ret['export_product_quantity'] = $gshoppingfeed['export_product_quantity'];
                $ret['additional_image'] = $gshoppingfeed['additional_image'];
                $ret['min_price'] = $gshoppingfeed['min_price_filter'];
                $ret['max_price'] = $gshoppingfeed['max_price_filter'];
                $ret['select_manufacturers[]'] = Tools::jsonDecode($gshoppingfeed['manufacturers_filter']);
                $ret['category_filter[]'] = Tools::jsonDecode($gshoppingfeed['category_filter']);
                $ret['mpn_type[]'] = json_decode($gshoppingfeed['mpn_type']);
                $ret['gtin_type[]'] = json_decode($gshoppingfeed['gtin_type']);
                $ret['id_gshoppingfeed'] = $gshoppingfeed['id_gshoppingfeed'];
                $ret['features_custom_mod'] = self::getCustomFeatureById((int)Tools::getValue('id_gshoppingfeed'), (int)$gshoppingfeed['select_lang']);
                $ret['custom_attribute'] = self::getCustomAttrById((int)Tools::getValue('id_gshoppingfeed'), (int)$gshoppingfeed['select_lang']);
            }
        }
        return $ret;
    }

    protected function postProcess()
    {
        if (Tools::getValue('submitGshoppingfeedModule') && Tools::getValue('submitGshoppingfeedModule') == 1) {
            $insert_data = array();
            $insert_data['only_active'] = (Tools::getValue('only_active') && Tools::getValue('only_active') == 1) ? 1 : 0;
            $insert_data['description_crop'] = (Tools::getValue('description_crop') && Tools::getValue('description_crop') == 1) ? 1 : 0;
            $insert_data['modify_uppercase_title'] = (Tools::getValue('modify_uppercase_title') && Tools::getValue('modify_uppercase_title') == 1) ? 1 : 0;
            $insert_data['modify_uppercase_description'] = (Tools::getValue('modify_uppercase_description') && Tools::getValue('modify_uppercase_description') == 1) ? 1 : 0;
            $insert_data['parts_payment_enabled'] = (Tools::getValue('parts_payment_enabled') && Tools::getValue('parts_payment_enabled') == 1) ? 1 : 0;
            $insert_data['product_title_in_product_type'] = (Tools::getValue('product_title_in_product_type') && Tools::getValue('product_title_in_product_type') == 1) ? 1 : 0;
            $insert_data['identifier_exists_mpn'] = (Tools::getValue('identifier_exists_mpn') && Tools::getValue('identifier_exists_mpn') == 1) ? 1 : 0;
            $insert_data['visible_product_hide'] = (Tools::getValue('visible_product_hide') && Tools::getValue('visible_product_hide') == 1) ? 1 : 0;
            $insert_data['mpn_force_on'] = (Tools::getValue('mpn_force_on') && Tools::getValue('mpn_force_on') == 1) ? 1 : 0;
            $insert_data['max_parts_payment'] = (Tools::getValue('max_parts_payment') && Validate::isInt(Tools::getValue('max_parts_payment'))) ? (int)Tools::getValue('max_parts_payment') : 1;
            $insert_data['interest_rates'] = (Tools::getValue('interest_rates') && Validate::isFloat(Tools::getValue('interest_rates'))) ? (float)Tools::getValue('interest_rates') : 0;
            $insert_data['exclude_ids'] = '';
            $join_exlude_data = array();
            if (Tools::getValue('exclude_ids')) {
                $expExculdeIds = explode(',', Tools::getValue('exclude_ids'));
                if (count($expExculdeIds) > 0) {
                    foreach ($expExculdeIds as $expExculdeId) {
                        if (Validate::isInt($expExculdeId) && $expExculdeId > 0 && !in_array($expExculdeId, $join_exlude_data)) {
                            $join_exlude_data[] = $expExculdeId;
                        }
                    }
                }
            }
            $insert_data['exclude_ids'] = join(',', $join_exlude_data);
            $insert_data['name'] = (Tools::getValue('name_feed')) ? pSQL(Tools::getValue('name_feed')) : pSQL(Configuration::get('PS_SHOP_NAME'));
            $insert_data['title_suffix'] = (Tools::getValue('title_suffix')) ? pSQL(Tools::getValue('title_suffix')) : '';
            $insert_data['description_suffix'] = (Tools::getValue('description_suffix')) ? pSQL(Tools::getValue('description_suffix')) : '';
            $insert_data['additional_each_product'] = (Tools::getValue('additional_each_product')) ? pSQL(Tools::getValue('additional_each_product'), true) : '';
            $insert_data['type_image'] = (Tools::getValue('type_image') && Validate::isInt(Tools::getValue('type_image'))) ? (int)Tools::getValue('type_image') : 0;
            $insert_data['brand_type'] = (Tools::getValue('brand_type')) ? pSQL(Tools::getValue('brand_type')) : '';
            $insert_data['additional_image'] = (Tools::getValue('additional_image') && Tools::getValue('additional_image') == 1) ? 1 : 0;
            $insert_data['type_description'] = (Tools::getValue('type_description') && Validate::isInt(Tools::getValue('type_description'))) ? (int)Tools::getValue('type_description') : 0;
            $insert_data['select_lang'] = (Tools::getValue('select_lang') && Validate::isInt(Tools::getValue('select_lang'))) ? (int)Tools::getValue('select_lang') : 0;
            $insert_data['id_currency'] = (Tools::getValue('id_currency') && Validate::isInt(Tools::getValue('id_currency'))) ? (int)Tools::getValue('id_currency') : 0;
            $insert_data['id_country'] = (Tools::getValue('id_country') && Validate::isInt(Tools::getValue('id_country'))) ? (int)Tools::getValue('id_country') : 0;
            $insert_data['id_carrier'] = (Tools::getValue('id_carrier') && is_array(Tools::getValue('id_carrier')) && count(Tools::getValue('id_carrier')) > 0) ? pSQL(Tools::jsonEncode(Tools::getValue('id_carrier'))) : '';
            $insert_data['export_attributes'] = (Tools::getValue('export_attributes') && Tools::getValue('export_attributes') == 1) ? (int)Tools::getValue('export_attributes') : 0;
            $insert_data['filtered_by_associated_type'] = (Tools::getValue('filtered_by_associated_type') && Tools::getValue('filtered_by_associated_type') == 1) ? (int)Tools::getValue('filtered_by_associated_type') : 0;
            $insert_data['export_attributes_only_first'] = (Tools::getValue('export_attributes_only_first') && Tools::getValue('export_attributes_only_first') == 1) ? (int)Tools::getValue('export_attributes_only_first') : 0;
            $insert_data['export_attribute_url'] = (Tools::getValue('export_attribute_url') && Tools::getValue('export_attribute_url') == 1) ? (int)Tools::getValue('export_attribute_url') : 0;
            $insert_data['export_attribute_prices'] = (Tools::getValue('export_attribute_prices') && Tools::getValue('export_attribute_prices') == 1) ? (int)Tools::getValue('export_attribute_prices') : 0;
            $insert_data['export_attribute_images'] = (Tools::getValue('export_attribute_images') && Tools::getValue('export_attribute_images') == 1) ? (int)Tools::getValue('export_attribute_images') : 0;
            $insert_data['export_feature'] = (Tools::getValue('export_feature') && Tools::getValue('export_feature') == 1) ? (int)Tools::getValue('export_feature') : 0;
            $insert_data['use_additional_shipping_cost'] = (Tools::getValue('use_additional_shipping_cost') && Tools::getValue('use_additional_shipping_cost') == 1) ? (int)Tools::getValue('use_additional_shipping_cost') : 0;
            $insert_data['export_product_quantity'] = (Tools::getValue('export_product_quantity') && Tools::getValue('export_product_quantity') == 1) ? (int)Tools::getValue('export_product_quantity') : 0;
            $insert_data['get_features_gender'] = (Tools::getValue('get_features_gender') && Validate::isInt(Tools::getValue('get_features_gender'))) ? (int)Tools::getValue('get_features_gender') : 0;
            $insert_data['get_features_age_group'] = (Tools::getValue('get_features_age_group') && Validate::isInt(Tools::getValue('get_features_age_group'))) ? (int)Tools::getValue('get_features_age_group') : 0;
            $insert_data['instance_of_tax'] = (Tools::getValue('instance_of_tax') && Validate::isInt(Tools::getValue('instance_of_tax'))) ? (int)Tools::getValue('instance_of_tax') : 0;
            $insert_data['get_attribute_color'] = (Tools::getValue('get_attribute_color') && is_array(Tools::getValue('get_attribute_color')) && count(Tools::getValue('get_attribute_color') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('get_attribute_color'))) : '';
            $insert_data['get_attribute_size'] = (Tools::getValue('get_attribute_size') && is_array(Tools::getValue('get_attribute_size')) && count(Tools::getValue('get_attribute_size') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('get_attribute_size'))) : '';
            $insert_data['get_attribute_pattern'] = (Tools::getValue('get_attribute_pattern') && is_array(Tools::getValue('get_attribute_pattern')) && count(Tools::getValue('get_attribute_pattern') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('get_attribute_pattern'))) : '';
            $insert_data['get_attribute_material'] = (Tools::getValue('get_attribute_material') && is_array(Tools::getValue('get_attribute_material')) && count(Tools::getValue('get_attribute_material') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('get_attribute_material'))) : '';
            $insert_data['export_non_available'] = (Tools::getValue('export_non_available') && Tools::getValue('export_non_available') == 1) ? 1 : 0;
            $insert_data['category_filter'] = (Tools::getValue('selected_categories') && is_array(Tools::getValue('selected_categories')) && count(Tools::getValue('selected_categories') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('selected_categories'))) : '';

            $insert_data['manufacturers_filter'] = (Tools::getValue('select_manufacturers') && is_array(Tools::getValue('select_manufacturers')) && count(Tools::getValue('select_manufacturers') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('select_manufacturers'))) : '';
            $insert_data['min_price_filter'] = (Tools::getValue('min_price') && Validate::isUnsignedFloat(Tools::getValue('min_price'))) ? (float)Tools::getValue('min_price') : 0;
            $insert_data['max_price_filter'] = (Tools::getValue('max_price') && Validate::isUnsignedFloat(Tools::getValue('max_price'))) ? (float)Tools::getValue('max_price') : 0;
            $insert_data['date_update'] = pSQL(date("Y-m-d H:i:s"));
            $insert_data['mpn_type'] = (Tools::getValue('mpn_type') && is_array(Tools::getValue('mpn_type')) && count(Tools::getValue('mpn_type') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('mpn_type'))) : '';
            $insert_data['gtin_type'] = (Tools::getValue('gtin_type') && is_array(Tools::getValue('gtin_type')) && count(Tools::getValue('gtin_type') > 0)) ? pSQL(Tools::jsonEncode(Tools::getValue('gtin_type'))) : '';
            if (Tools::getValue('id_gshoppingfeed', false) && Validate::isInt(Tools::getValue('id_gshoppingfeed'))) {
                if (Db::getInstance()->update('gshoppingfeed', $insert_data, 'id_gshoppingfeed = ' . (int)Tools::getValue('id_gshoppingfeed'))) {
                    $this->updateOtherSet((int)Tools::getValue('id_gshoppingfeed'));
                    $this->confirmations[] = $this->displayConfirmation($this->l('Configuration updated'));
                }
            } else {
                if (Db::getInstance()->insert('gshoppingfeed', $insert_data, false, true)) {
                    $id_gshoppingfeed = Db::getInstance()->Insert_ID();
                    $this->updateOtherSet((int)$id_gshoppingfeed);
                    $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.'));
                }
            }

            return true;
        }

        if ((bool)Tools::isSubmit('submitTaxonomyEdit') === true && Validate::isInt(Tools::getValue('id_category'))
            && Tools::getValue('id_category') > 0
        ) {
            foreach (Language::getLanguages(false) as $language) {
                if (Tools::isSubmit($language['iso_code'] . '_taxonomy_linking')) {
                    $exist_note = Db::getInstance()->getRow('SELECT `id_taxonomy`, `id_category`
                        FROM `' . _DB_PREFIX_ . 'gshoppingfeed_taxonomy`
                            WHERE `id_category` = ' . (int)Tools::getValue('id_category') . ' AND `id_lang` = ' . (int)$language['id_lang']);

                    $taxonomy_lists = array();
                    if (Tools::getValue($language['iso_code'] . '_taxonomy_linking') != 0) {
                        $taxonomy_lists = $this->getGoogleTxtCategoryFeed($language['id_lang'], true);
                    }
                    if (!$exist_note) {
                        $taxonomy_path = '';
                        if (isset($taxonomy_lists[(int)Tools::getValue($language['iso_code'] . '_taxonomy_linking')])
                            && !empty($taxonomy_lists[(int)Tools::getValue($language['iso_code'] . '_taxonomy_linking')])
                        ) {
                            $taxonomy_path = (string)$taxonomy_lists[(int)Tools::getValue($language['iso_code'] . '_taxonomy_linking')];
                        }
                        $data_insert = array(
                            'id_category' => (int)Tools::getValue('id_category'),
                            'id_taxonomy' => (int)Tools::getValue($language['iso_code'] . '_taxonomy_linking'),
                            'name_taxonomy' => pSQL($taxonomy_path),
                            'id_lang' => (int)$language['id_lang']
                        );
                        Db::getInstance()->insert('gshoppingfeed_taxonomy', $data_insert, false, true);
                    } else {
                        $taxonomy_path = '';
                        if (isset($taxonomy_lists[(int)Tools::getValue($language['iso_code'] . '_taxonomy_linking')])
                            && !empty($taxonomy_lists[(int)Tools::getValue($language['iso_code'] . '_taxonomy_linking')])
                        ) {
                            $taxonomy_path = (string)$taxonomy_lists[(int)Tools::getValue($language['iso_code'] . '_taxonomy_linking')];
                        }
                        $data_update = array(
                            'id_taxonomy' => (int)Tools::getValue($language['iso_code'] . '_taxonomy_linking'),
                            'name_taxonomy' => pSQL($taxonomy_path)
                        );
                        Db::getInstance()->update('gshoppingfeed_taxonomy', $data_update, '`id_category` = ' . (int)Tools::getValue('id_category') . ' AND `id_lang` = ' . (int)$language['id_lang']);
                    }
                }
            }
            $this->confirmations[] = $this->displayConfirmation($this->l('Configuration updated'));
        }

        if ((bool)Tools::isSubmit('submitTaxonomyLangEdit') === true && Validate::isLanguageCode(Tools::getValue('language_code'))) {
            if (isset($_FILES['taxonomy_file']) && is_uploaded_file($_FILES['taxonomy_file']['tmp_name'])) {
                if (isset($_FILES['taxonomy_file']['type']) && $_FILES['taxonomy_file']['type'] == 'text/plain') {
                    $language_code = trim(Tools::getValue('language_code'));
                    $language_code = Tools::strtolower($language_code);
                    if (!is_dir(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code)) {
                        mkdir(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code);
                        copy(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . 'index.php', _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . 'index.php');
                    }
                    $file_name = 'taxonomy-with-ids.' . $language_code . '.txt';
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name)) {
                        unlink(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name);
                    }
                    if (!move_uploaded_file($_FILES['taxonomy_file']['tmp_name'], _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name)) {
                        $this->errors[] = $this->displayError($this->l('Failed to copy the file.'));
                    } else {
                        $this->confirmations[] = $this->displayConfirmation($this->l('Configuration updated!'));
                    }
                } else {
                    $this->errors[] = $this->displayError($this->l('Upload error. Please check your upload file (*.txt) .'));
                }
            } elseif (array_key_exists('taxonomy_file', $_FILES) && (int)$_FILES['taxonomy_file']['error'] === 1) {
                $max_upload = (int)ini_get('upload_max_filesize');
                $max_post = (int)ini_get('post_max_size');
                $upload_mb = min($max_upload, $max_post);
                $this->errors[] = sprintf($this->displayError($this->l('The file %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.')), '', '<b>' . $_FILES['taxonomy_file']['name'] . '</b> ', '<b>' . $upload_mb . '</b>');
            }
        }
    }

    public function updateOtherSet($id_gshopping = 0)
    {
        $this->updateCustomFeatures($id_gshopping);
        $this->updateCustomAttr($id_gshopping);
    }

    private function updateCustomFeatures($id_gshopping)
    {
        $features_custom_modification = array();
        Db::getInstance()->delete('gshoppingfeed_custom_features', 'id_gshoppingfeed=' . (int)$id_gshopping);
        if (Tools::getValue('feature_custom_inheritage')) {
            $feature_custom_inheritage = Tools::getValue('feature_custom_inheritage');
            $feature_custom_inheritage_param = Tools::getValue('feature_custom_inheritage_param');
            if (is_array($feature_custom_inheritage) && count($feature_custom_inheritage)) {
                foreach ($feature_custom_inheritage as $f_pos => $feature_s) {
                    if (Validate::isInt($feature_s) && $feature_s > 0) {
                        $features_custom_modification[] = array(
                            'id_feature' => (int)$feature_s,
                            'unit' => (isset($feature_custom_inheritage_param[$f_pos]) && !empty($feature_custom_inheritage_param[$f_pos])) ? urldecode($feature_custom_inheritage_param[$f_pos]) : ''
                        );
                    }
                }
            }

            if (is_array($features_custom_modification) && count($features_custom_modification)) {
                Db::getInstance()->insert('gshoppingfeed_custom_features', array_map(function ($data) use ($id_gshopping) {
                    return array(
                        'id_gshoppingfeed' => (int)$id_gshopping,
                        'id_feature' => (int)$data['id_feature'],
                        'unit' => pSQL($data['unit'])
                    );
                }, $features_custom_modification));
            }
        }
    }

    private function updateCustomAttr($id_gshopping)
    {
        DB::getInstance()->delete('gshoppingfeed_custom_attributes', 'id_gshoppingfeed=' . (int)$id_gshopping);
        $customAttrKey = Tools::getValue('custom_attr_key');
        $customAttrId = Tools::getValue('custom_attr_id');

        $prepareAttrForInsert = array();
        if (is_array($customAttrKey) && count($customAttrKey)) {
            foreach ($customAttrKey as $linePos => $attr) {
                $attr = trim($attr);
                $attr = str_replace(' ', '_', $attr);
                $prepareAttrForInsert[] = array(
                    'id_gshoppingfeed' => (int)$id_gshopping,
                    'id_attribute' => (int)$customAttrId[$linePos],
                    'unit' => pSQL($attr)
                );
            }

            DB::getInstance()->insert('gshoppingfeed_custom_attributes', $prepareAttrForInsert);
        }
    }

    public function setTaxonomyCategoryLang($category, $taxonomy_id, $lang)
    {
        $exist_note = Db::getInstance()->getRow('SELECT `id_taxonomy`, `id_category`
                        FROM `' . _DB_PREFIX_ . 'gshoppingfeed_taxonomy`
                            WHERE `id_category` = ' . (int)$category . ' AND `id_lang` = ' . (int)$lang);

        $taxonomy_lists = $this->getGoogleTxtCategoryFeed($lang, true);

        if (!$exist_note) {
            $taxonomy_path = '';
            if (isset($taxonomy_lists[(int)$taxonomy_id])
                && !empty($taxonomy_lists[(int)$taxonomy_id])
            ) {
                $taxonomy_path = (string)$taxonomy_lists[(int)$taxonomy_id];
            }
            $data_insert = array(
                'id_category' => (int)$category,
                'id_taxonomy' => (int)$taxonomy_id,
                'name_taxonomy' => pSQL($taxonomy_path),
                'id_lang' => (int)$lang
            );

            $update = Db::getInstance()->insert('gshoppingfeed_taxonomy', $data_insert, false, true);
        } else {
            $taxonomy_path = '';
            if (isset($taxonomy_lists[(int)$taxonomy_id])
                && !empty($taxonomy_lists[(int)$taxonomy_id])
            ) {
                $taxonomy_path = (string)$taxonomy_lists[(int)$taxonomy_id];
            }
            $data_update = array(
                'id_taxonomy' => (int)$taxonomy_id,
                'name_taxonomy' => pSQL($taxonomy_path)
            );
            $update = Db::getInstance()->update('gshoppingfeed_taxonomy', $data_update, '`id_category` = ' . (int)$category . ' AND `id_lang` = ' . (int)$lang);
        }

        if ($update) {
            $language = Language::getLanguage($lang);
            return array(
                'taxonomy_id' => (int)$taxonomy_id,
                'name_taxonomy' => $taxonomy_path,
                'language' => $language['iso_code'],
                'update' => 1
            );
        }

        return '';
    }

    public function renderDiscoverModules()
    {
        $modules = array();
        $link_to_doc = '';
        $lang_code = $this->context->language->iso_code;

        $modules_file = _PS_CACHEFS_DIRECTORY_ . 'terranetpro-modules.xml';
        if (!Tools::file_exists_no_cache($modules_file) || (filemtime($modules_file) < (time() - 86400))) {
            $contents = @Tools::file_get_contents('http://terranetpro.com/modules.xml');
            if ($contents) {
                file_put_contents($modules_file, $contents);
            } else {
                $modules_file = $this->getLocalPath() . '/config/modules.xml';
            }
        }

        if (Tools::file_exists_no_cache($modules_file)) {
            $modules_list = simplexml_load_file($modules_file);
            foreach ($modules_list->module as $module) {
                $id_product = (string)$module['id_product'];
                if ($this->id_product == $id_product) {
                    continue;
                }

                if (empty($module->{$lang_code})) {
                    $lang_code = 'en';
                }

                $product_mod_name = '';
                if (isset($module->{$lang_code})) {
                    $name_tmp = $module->{$lang_code};
                    $product_mod_name = (isset($name_tmp['name']) && !empty($name_tmp['name'])) ? $name_tmp['name'] : '';
                }

                $modules[] = array(
                    'id_product' => $id_product,
                    'rate' => (string)$module['rate'],
                    'lang_code' => $lang_code,
                    'name' => (string)$product_mod_name,
                    'description' => (string)$module->{$lang_code}
                );
            }

            foreach ($modules_list->documentation->module as $module_item) {
                $item_id = (string)$module_item['id_product'];
                if ($item_id == $this->id_product) {
                    $link_to_doc = (isset($module_item->link)) ? (string)$module_item->link : '';
                    $link_to_doc = trim($link_to_doc);
                }
            }
        }

        $this->smarty->assign(array(
            'this_module' => $this,
            'documentation_link' => $link_to_doc,
            'documentation_text' => $this->l('Module documentation'),
            'modules' => array_slice($modules, 0, 3),
            'labels' => array(
                'like' => $this->l('Do you like the [1]%s[/1] module?'),
                'yes' => $this->l('Yes'),
                'no' => $this->l('No'),
                'title' => $this->l('Promote your products'),
                'discover' => $this->l('Discover')
            )
        ));

        return $this->display($this->name, 'modules.tpl');
    }

    public static function getCustomFeatureById($id_gshoppingfeed, $id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $rows = Db::getInstance()->executeS('SELECT `id_feature`, `unit` 
            FROM `' . _DB_PREFIX_ . 'gshoppingfeed_custom_features`
                WHERE `id_gshoppingfeed` = ' . (int)$id_gshoppingfeed);

        return ($rows && is_array($rows) && count($rows)) ? array_map(function ($data) use ($id_lang) {
            return array_merge($data, array('name' => (new Feature($data['id_feature'], $id_lang))->name));
        }, $rows) : array();
    }

    public static function getCustomAttrById($id_gshoppingfeed, $id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $rows = Db::getInstance()->executeS('SELECT `id_attribute`, `unit` 
            FROM `' . _DB_PREFIX_ . 'gshoppingfeed_custom_attributes`
                WHERE `id_gshoppingfeed` = ' . (int)$id_gshoppingfeed);
        return ($rows && is_array($rows) && count($rows)) ? array_map(function ($data) use ($id_lang) {
            return array_merge($data, array('name' => (new AttributeGroup($data['id_attribute'], $id_lang))->name));
        }, $rows) : array();
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }

            $this->context->controller->addJqueryPlugin(array('scrollTo'));
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    public function renderShowFeedLinks()
    {
        $sql = 'SELECT `id_gshoppingfeed`, `date_update`, `name`  FROM `' . _DB_PREFIX_ . 'gshoppingfeed`';
        $lists = DB::getInstance()->executeS($sql);
        if (is_array($lists) && count($lists)) {
            $linkRef = $this->context->link;
            $lists = array_map(function ($data) use ($linkRef) {
                $urlRebuildParams = array(
                    'key' => (int)$data['id_gshoppingfeed'],
                    'token' => md5(_COOKIE_KEY_ . $data['id_gshoppingfeed']),
                    'only_rebuild' => '1');
                $data['rebuild_url'] = $linkRef->getModuleLink('gshoppingfeed', 'getList', $urlRebuildParams);
                $urlDownloadParams = array(
                    'key' => (int)$data['id_gshoppingfeed'],
                    'token' => md5(_COOKIE_KEY_ . $data['id_gshoppingfeed']),
                    'only_download' => '1');
                $data['download_url'] = $linkRef->getModuleLink('gshoppingfeed', 'getList', $urlDownloadParams);

                return $data;
            }, $lists);
        }

        $this->smarty->assign(
            array(
                'backUrl' => $this->context->link->getAdminLink('AdminModules', true, array(), array('configure' => $this->name)),
                'lists' => $lists
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/view_feed_links.tpl');
    }
}
