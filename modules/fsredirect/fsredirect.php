<?php
/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$class_folder = dirname(__FILE__).'/classes/';
require_once($class_folder.'FsRedirectDataTransfer.php');
require_once($class_folder.'FsRedirectDeletedModel.php');
require_once($class_folder.'FsRedirectHelperList.php');
require_once($class_folder.'FsRedirectMessenger.php');
require_once($class_folder.'FsRedirectModel.php');
require_once($class_folder.'FsRedirectTools.php');

class Fsredirect extends Module
{
    protected static $deleted_url_list_content;

    private static $smarty_registered = false;

    public function __construct()
    {
        $this->name = 'fsredirect';
        $this->tab = 'seo';
        $this->version = '1.2.0';
        $this->author = 'ModuleFactory';
        $this->need_instance = 0;
        $this->ps_versions_compliancy['min'] = '1.5';
        $this->module_key = '61c0498a90c60a6d1dfb46f260a1bd14';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('URL Redirect');
        $this->description = $this->l('Create 301, 302, 303, 307 URL redirects to your webshop.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->redirect_types = array(
            array(
                'id' => '301',
                'name' => $this->l('301 Moved Permanently'),
            ),
            array(
                'id' => '302',
                'name' => $this->l('302 Found'),
            ),
            array(
                'id' => '303',
                'name' => $this->l('303 See Other'),
            ),
            array(
                'id' => '307',
                'name' => $this->l('307 Temporary Redirect'),
            ),
        );

        $this->redirect_headers = array();
        foreach ($this->redirect_types as $redirect_type) {
            $this->redirect_headers[$redirect_type['id']] = 'HTTP/1.1 '.$redirect_type['name'];
        }

        $this->redirect_names = array();
        foreach ($this->redirect_types as $redirect_type) {
            $this->redirect_names[$redirect_type['id']] = $redirect_type['name'];
        }

        $this->matching_types = array(
            array(
                'id' => 'equalsto',
                'name' => $this->l('Equals to')
            ),
            array(
                'id' => 'beginswith',
                'name' => $this->l('Begins width')
            ),
            array(
                'id' => 'contains',
                'name' => $this->l('Contains')
            ),
        );

        $this->matching_names = array();
        foreach ($this->matching_types as $matching_type) {
            $this->matching_names[$matching_type['id']] = $matching_type['name'];
        }

        $this->deleted_types = array(
            array(
                'id' => 'product',
                'name' => $this->l('Product'),
            ),
            array(
                'id' => 'category',
                'name' => $this->l('Category'),
            ),
            array(
                'id' => 'manufacturer',
                'name' => $this->l('Manufacturer'),
            ),
            array(
                'id' => 'supplier',
                'name' => $this->l('Supplier'),
            ),
            array(
                'id' => 'cms',
                'name' => $this->l('CMS'),
            ),
            array(
                'id' => 'cms_category',
                'name' => $this->l('CMS Category'),
            ),
        );

        $this->deleted_names = array();
        foreach ($this->deleted_types as $deleted_type) {
            $this->deleted_names[$deleted_type['id']] = $deleted_type['name'];
        }

        if (!self::$smarty_registered) {
            smartyRegisterFunction(
                $this->context->smarty,
                'modifier',
                'fsrCorrectTheMess',
                array('FsRedirectTools', 'unescapeSmarty')
            );
            self::$smarty_registered = true;
        }
    }

    public function url()
    {
        $context = Context::getContext();
        $url = $context->link->getAdminLink('AdminModules').'&configure='.$this->name;
        if ($this->isPsMin17()) {
            return $url;
        }
        return FsRedirectTools::baseUrl().$url;
    }

    public function getModuleBaseUrl()
    {
        $context = Context::getContext();
        return $context->shop->getBaseURL().'modules/'.$this->name.'/';
    }

    public function getModuleFile()
    {
        return __FILE__;
    }

    public function install()
    {
        $return = true;
        $return = $return && parent::install();
        $return = $return && $this->registerHook('actionObjectProductDeleteBefore');
        $return = $return && $this->registerHook('actionObjectCategoryDeleteBefore');
        $return = $return && $this->registerHook('actionObjectManufacturerDeleteBefore');
        $return = $return && $this->registerHook('actionObjectSupplierDeleteBefore');
        $return = $return && $this->registerHook('actionObjectCMSDeleteBefore');
        $return = $return && $this->registerHook('actionObjectCMSCategoryDeleteBefore');
        $return = $return && $this->registerHook('displayBackOfficeHeader');
        $return = $return && $this->installDB();
        return $return;
    }

    public function installDB()
    {
        $return = Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fsredirect` (
                `id_fsredirect` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_shop` int(10) unsigned NOT NULL,
                `old_url` varchar(400) NOT NULL,
                `matching_type` varchar(100) NOT NULL,
                `new_url` varchar(400) NOT NULL,
                `redirect_type` varchar(100) NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_fsredirect`),
                KEY `id_shop` (`id_shop`),
                KEY `active` (`active`)
            ) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
        $return = $return && Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fsredirect_deleted` (
                `id_fsredirect_deleted` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_shop` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `url` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_fsredirect_deleted`),
                KEY `id_shop` (`id_shop`),
                KEY `url` (`url`)
            ) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
        return $return;
    }

    public function uninstall()
    {
        $return = parent::uninstall();
        $return = $return && $this->uninstallDB();
        return $return;
    }

    public function uninstallDB()
    {
        $return = true;
        $return = $return && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'fsredirect`');
        return $return;
    }

    #################### ADMIN ####################

    public function getContent()
    {
        $context = Context::getContext();
        $context->controller->addJS($this->_path.'views/js/admin.js');
        $context->controller->addCSS(($this->_path).'views/css/admin.css', 'all');

        $html = FsRedirectMessenger::getMessagesHtml();

        if (Tools::isSubmit('create'.$this->name)) {
            $fsredirect = new FsRedirectModel();
            $fsredirect->copyFromPost();
            $fsredirect->id_shop = $this->context->shop->id;

            if ($fsredirect->validateFields(false)) {
                $fsredirect->active = 1;
                $fsredirect->save();
                FsRedirectDeletedModel::deleteByUrl($fsredirect->old_url, $this->context->shop->id);
                FsRedirectMessenger::addSuccessMessage($this->l('Creation successful'));
                FsRedirectTools::redirect($this->url());
            } else {
                if (empty($fsredirect->old_url)) {
                    FsRedirectMessenger::addErrorMessage($this->l('Please enter a valid Old URL'));
                } elseif ((!FsRedirectTools::startsWith($fsredirect->old_url, '/')) &&
                    ($fsredirect->matching_type != 'contains')) {
                    FsRedirectMessenger::addErrorMessage(
                        $this->l('Please enter a valid Old URL, must starts with "/"')
                    );
                }
                if (!Validate::isUrl(Tools::getValue('new_url', false))) {
                    FsRedirectMessenger::addErrorMessage($this->l('Please enter a valid New URL'));
                }

                $transfer = $fsredirect->toArray();
                $transfer['create'.$this->name] = 1;
                FsRedirectDataTransfer::setData($transfer);
                FsRedirectTools::redirect($this->url());
            }
        } elseif (Tools::isSubmit('status'.$this->name)) {
            $id_fsredirect = (int)Tools::getValue('id_fsredirect');
            if ($id_fsredirect) {
                $fsredirect = new FsRedirectModel($id_fsredirect);
                if ($fsredirect->id) {
                    $fsredirect->toggleStatus();
                }
            }
            FsRedirectMessenger::addSuccessMessage($this->l('The status has been updated successfully.'));
            FsRedirectTools::redirectBack($this->url());
        } elseif (Tools::isSubmit('delete'.$this->name)) {
            $id_fsredirect = (int)Tools::getValue('id_fsredirect');
            if ($id_fsredirect) {
                $fsredirect = new FsRedirectModel($id_fsredirect);
                if ($fsredirect->id) {
                    $fsredirect->delete();
                }
            }
            FsRedirectMessenger::addSuccessMessage($this->l('Deletion successful'));
            FsRedirectTools::redirectBack($this->url());
        } elseif (Tools::isSubmit('import'.$this->name)) {
            $valid = true;
            $csv_field_separator = Tools::getValue('csv_field_separator', '');
            $csv_field_enclosure = Tools::getValue('csv_field_enclosure', '');
            $file_attachment = Tools::fileAttachment('csv');

            if (empty($csv_field_separator)) {
                FsRedirectMessenger::addErrorMessage($this->l('Please enter a valid field separator'));
                $valid = false;
            }

            if (!isset($file_attachment['content'])) {
                FsRedirectMessenger::addErrorMessage($this->l('Please upload a CSV file'));
                $valid = false;
            }

            if ($valid) {
                $tmp_csv_file = dirname(__FILE__).'/tmp/import.csv';
                $tmp_csv_file_handle = fopen($tmp_csv_file, 'w');
                fwrite($tmp_csv_file_handle, $file_attachment['content']);
                fclose($tmp_csv_file_handle);

                $tmp_csv_file_handle = fopen($tmp_csv_file, 'r');
                $line_counter = 1;
                $valid_line_counter = 0;
                while ($file_row = fgets($tmp_csv_file_handle)) {
                    $valid_line = true;
                    if ($line_counter > 1) {
                        $data = str_getcsv($file_row, $csv_field_separator, $csv_field_enclosure);
                        if (count($data) != 5) {
                            FsRedirectMessenger::addErrorMessage(
                                $this->l('Import error: Invalid column number:').' '.count($data).'. '.
                                $this->l('Must be').' 5! - Row data ('.$line_counter.'): '.$file_row
                            );
                            $valid_line = false;
                        }

                        if ($valid_line) {
                            // Matching type
                            if (!in_array($data[1], array_keys($this->matching_names))) {
                                FsRedirectMessenger::addErrorMessage(
                                    $this->l('Import error: Invalid matching type:').' '.
                                    $this->l('Must be').': '.implode(',', array_keys($this->matching_names)).
                                    ' - Row data ('.$line_counter.'): '.$file_row
                                );
                                $valid_line = false;
                            }

                            // Redirect type
                            if (!in_array($data[3], array_keys($this->redirect_names))) {
                                FsRedirectMessenger::addErrorMessage(
                                    $this->l('Import error: Invalid redirect type:').' '.
                                    $this->l('Must be').': '.implode(',', array_keys($this->redirect_names)).
                                    ' - Row data ('.$line_counter.'): '.$file_row
                                );
                                $valid_line = false;
                            }

                            //Old URL
                            if (empty($data[0])) {
                                FsRedirectMessenger::addErrorMessage(
                                    $this->l('Import error: Invalid old URL:').' - Row data ('.
                                    $line_counter.'): '.$file_row
                                );
                                $valid_line = false;
                            } elseif ((!FsRedirectTools::startsWith($data[0], '/')) && ($data[1] != 'contains')) {
                                FsRedirectMessenger::addErrorMessage(
                                    $this->l('Import error: Invalid old URL, must starts with "/":').
                                    ' - Row data ('.$line_counter.'): '.$file_row
                                );
                                $valid_line = false;
                            }

                            //New URL
                            if (empty($data[2])) {
                                FsRedirectMessenger::addErrorMessage(
                                    $this->l('Import error: Invalid new URL:').' - Row data ('.
                                    $line_counter.'): '.$file_row
                                );
                                $valid_line = false;
                            }

                            // Status
                            if (!in_array($data[4], array(0, 1))) {
                                FsRedirectMessenger::addErrorMessage(
                                    $this->l('Import error: Invalid active value:').' '.
                                    $this->l('Must be').': 0,1 - Row data ('.$line_counter.'): '.$file_row
                                );
                                $valid_line = false;
                            }

                            if ($valid_line) {
                                $valid_line_counter++;
                                $fsredirect = new FsRedirectModel();
                                $fsredirect->id_shop = $this->context->shop->id;
                                $fsredirect->old_url = $data[0];
                                $fsredirect->matching_type = $data[1];
                                $fsredirect->new_url = $data[2];
                                $fsredirect->redirect_type = $data[3];
                                $fsredirect->active = $data[4];

                                if ($fsredirect->validateFields(false)) {
                                    $fsredirect->save();
                                    FsRedirectDeletedModel::deleteByUrl($fsredirect->old_url, $this->context->shop->id);
                                } else {
                                    FsRedirectMessenger::addErrorMessage(
                                        $this->l('Import error: Invalid data:').' - Row data ('.
                                        $line_counter.'): '.$file_row
                                    );
                                }
                            }
                        }
                    }
                    $line_counter++;
                }
                FsRedirectMessenger::addSuccessMessage(
                    $this->l('Import successful').' ('.$valid_line_counter.' '.$this->l('redirects').')'
                );
                FsRedirectTools::redirect($this->url());
            } else {
                $transfer = array(
                    'import'.$this->name => 1,
                    'csv_field_separator' => $csv_field_separator,
                    'csv_field_enclosure' => $csv_field_enclosure,
                );
                FsRedirectDataTransfer::setData($transfer);
                FsRedirectTools::redirect($this->url());
            }
        } elseif (Tools::isSubmit('empty'.$this->name)) {
            FsRedirectModel::deleteAll($this->context->shop->id);
            FsRedirectMessenger::addSuccessMessage($this->l('Deletion successful'));
            FsRedirectTools::redirectBack($this->url());
        } elseif (Tools::isSubmit('export'.$this->name)) {
            $redirects = FsRedirectModel::getAll();
            $export_csv_file = dirname(__FILE__).'/tmp/url_redirects.csv';
            $export_csv_file_handle = fopen($export_csv_file, 'w');
            fputcsv(
                $export_csv_file_handle,
                array('Old URL','Matching type (equalsto/beginswith/contains)',
                    'New URL',
                    'Redirect type (301/302/303/307)',
                    'Active (0/1)'),
                ',',
                '"'
            );
            if ($redirects) {
                foreach ($redirects as $redirect) {
                    fputcsv(
                        $export_csv_file_handle,
                        array(
                            $redirect['old_url'],
                            $redirect['matching_type'],
                            $redirect['new_url'],
                            $redirect['redirect_type'],
                            $redirect['active']
                        ),
                        ',',
                        '"'
                    );
                }
            }

            fclose($export_csv_file_handle);
            FsRedirectTools::redirect('/modules/fsredirect/tmp/url_redirects.csv');
        } elseif (Tools::isSubmit('delete'.$this->name.'_deleted')) {
            $id_fsredirect_deleted = (int)Tools::getValue('id_fsredirect_deleted');
            if ($id_fsredirect_deleted) {
                $fsredirect_deleted = new FsRedirectDeletedModel($id_fsredirect_deleted);
                if ($fsredirect_deleted->id) {
                    $fsredirect_deleted->delete();
                }
            }
            FsRedirectMessenger::addSuccessMessage($this->l('Deletion successful'));
            FsRedirectTools::redirectBack($this->url());
        } elseif (Tools::isSubmit('empty'.$this->name.'_deleted')) {
            FsRedirectDeletedModel::deleteAll($this->context->shop->id);
            FsRedirectMessenger::addSuccessMessage($this->l('Deletion successful'));
            FsRedirectTools::redirectBack($this->url());
        } elseif (Tools::isSubmit('export'.$this->name.'_deleted')) {
            $redirects_deleted = FsRedirectDeletedModel::getAll();
            $export_csv_file = dirname(__FILE__).'/tmp/deleted_urls.csv';
            $export_csv_file_handle = fopen($export_csv_file, 'w');
            fputcsv($export_csv_file_handle, array('Old URL','Matching type (equalsto/beginswith/contains)',
                'New URL','Redirect type (301/302/303/307)','Active (0/1)'), ',', '"');
            if ($redirects_deleted) {
                foreach ($redirects_deleted as $redirect_deleted) {
                    fputcsv($export_csv_file_handle, array($redirect_deleted['url'], 'equalsto', '',
                        '301', '1'), ',', '"');
                }
            }

            fclose($export_csv_file_handle);
            FsRedirectTools::redirect('/modules/fsredirect/tmp/deleted_urls.csv');
        }

        if (Shop::isFeatureActive()) {
            if (!FsRedirectTools::startsWith($context->cookie->shopContext, 's-')) {
                $html .= $this->displayError($this->l('Please select a shop!'));
                return $html;
            }
        }

        // Generate the forms
        $create_form = $this->initCreateForm();
        $import_form = $this->initImportForm();

        $data_transfer = FsRedirectDataTransfer::getData();
        if ($data_transfer) {
            if (isset($data_transfer['create'.$this->name])) {
                $create_form->fields_value['matching_type'] =
                    FsRedirectTools::getValue('matching_type', 'equalstp', $data_transfer);
                $create_form->fields_value['redirect_type'] =
                    FsRedirectTools::getValue('redirect_type', '301', $data_transfer);
                $create_form->fields_value['old_url'] = FsRedirectTools::getValue('old_url', '', $data_transfer);
                $create_form->fields_value['new_url'] = FsRedirectTools::getValue('new_url', '', $data_transfer);
            } else {
                $create_form->fields_value['matching_type'] = 'equalsto';
                $create_form->fields_value['redirect_type'] = '301';
                $create_form->fields_value['old_url'] = '';
                $create_form->fields_value['new_url'] = '';
            }

            if (isset($data_transfer['import'.$this->name])) {
                $import_form->fields_value['csv_field_separator'] =
                    FsRedirectTools::getValue('csv_field_separator', ',', $data_transfer);
                $import_form->fields_value['csv_field_enclosure'] =
                    FsRedirectTools::getValue('csv_field_enclosure', '', $data_transfer);
            } else {
                $import_form->fields_value['csv_field_separator'] = ',';
                $import_form->fields_value['csv_field_enclosure'] = '"';
            }
        } else {
            $create_form->fields_value['matching_type'] = 'equalsto';
            $create_form->fields_value['redirect_type'] = '301';
            $create_form->fields_value['old_url'] = '';
            $create_form->fields_value['new_url'] = '';

            $import_form->fields_value['csv_field_separator'] = ',';
            $import_form->fields_value['csv_field_enclosure'] = '"';
        }

        $html .= $create_form->generateForm($this->create_fields_form);

        $redirect_list = $this->initRedirectList();
        $fsredirect_pagination_default = 50;
        if ($this->isPs15()) {
            $fsredirect_pagination_default = 20;
        }
        $filter = array(
            'id_fsredirect' => Tools::getValue('fsredirectFilter_id_fsredirect', ''),
            'matching_type' => Tools::getValue('fsredirectFilter_matching_type', ''),
            'old_url' => Tools::getValue('fsredirectFilter_old_url', ''),
            'new_url' => Tools::getValue('fsredirectFilter_new_url', ''),
            'redirect_type' => Tools::getValue('fsredirectFilter_redirect_type', ''),
            'active' => Tools::getValue('fsredirectFilter_active', ''),
            'page' => Tools::getValue('submitFilterfsredirect', 1),
            'limit' => Tools::getValue('fsredirect_pagination', $fsredirect_pagination_default),
            'order_by' => Tools::getValue('fsredirectOrderby', 'id_fsredirect'),
            'order_way' => Tools::strtoupper(Tools::getValue('fsredirectOrderway', 'DESC')),
        );

        if (!$filter['page']) {
            $filter['page'] = 1;
        }

        if (Tools::isSubmit('submitResetfsredirect')) {
            $filter['id_fsredirect'] = '';
            $filter['matching_type'] = '';
            $filter['old_url'] = '';
            $filter['new_url'] = '';
            $filter['redirect_type'] = '';
            $filter['active'] = '';
            $filter['page'] = 1;
            $filter['limit'] = Tools::getValue('fsredirect_pagination', $fsredirect_pagination_default);
        }

        $context->cookie->fsredirectFilter_id_fsredirect = $filter['id_fsredirect'];
        $context->cookie->fsredirectFilter_matching_type = $filter['matching_type'];
        $context->cookie->fsredirectFilter_old_url = $filter['old_url'];
        $context->cookie->fsredirectFilter_new_url = $filter['new_url'];
        $context->cookie->fsredirectFilter_redirect_type = $filter['redirect_type'];
        $context->cookie->fsredirectFilter_active = $filter['active'];

        $redirect_list_content = FsRedirectModel::getListContent($filter);
        if ($redirect_list_content) {
            foreach ($redirect_list_content as $key => $row) {
                $redirect_list_content[$key]['matching_type'] = $this->matching_names[$row['matching_type']];
            }
            $redirect_list->listTotal = FsRedirectModel::getListContentCount($filter);
        }

        $context->smarty->assign(array(
            'is_ps_15' => $this->isPs15(),
            'module_export_url' => $this->url().'&export'.$this->name,
            'module_delete_all_url' => $this->url().'&empty'.$this->name,
            'generated_list' => $redirect_list->generateList(
                $redirect_list_content,
                $this->redirect_fields_list
            )
        ));

        $html .= $this->display(__FILE__, 'views/templates/admin/list_wrapper.tpl');

        $html .= $import_form->generateForm($this->import_fields_form);

        $deleted_url_list = $this->initDeletedUrlList();
        $fsredirect_deleted_pagination_default = 50;
        if ($this->isPs15()) {
            $fsredirect_deleted_pagination_default = 20;
        }
        $filter = array(
            'id_fsredirect_deleted' => Tools::getValue('fsredirect_deletedFilter_id_fsredirect_deleted', ''),
            'name' => Tools::getValue('fsredirect_deletedFilter_name', ''),
            'url' => Tools::getValue('fsredirect_deletedFilter_url', ''),
            'type' => Tools::getValue('fsredirect_deletedFilter_type', ''),
            'page' => Tools::getValue('submitFilterfsredirect_deleted', 1),
            'limit' => Tools::getValue('fsredirect_deleted_pagination', $fsredirect_deleted_pagination_default),
            'order_by' => Tools::getValue('fsredirect_deletedOrderby', 'id_fsredirect_deleted'),
            'order_way' => Tools::strtoupper(Tools::getValue('fsredirect_deletedOrderway', 'DESC')),
        );

        if (!$filter['page']) {
            $filter['page'] = 1;
        }

        if (Tools::isSubmit('submitResetfsredirect_deleted')) {
            $filter['id_fsredirect_deleted'] = '';
            $filter['name'] = '';
            $filter['url'] = '';
            $filter['type'] = '';
            $filter['page'] = 1;
            $filter['limit'] = Tools::getValue('fsredirect_deleted_pagination', $fsredirect_deleted_pagination_default);
        }

        $context->cookie->fsredirect_deletedFilter_id_fsredirect_deleted = $filter['id_fsredirect_deleted'];
        $context->cookie->fsredirect_deletedFilter_name = $filter['name'];
        $context->cookie->fsredirect_deletedFilter_url = $filter['url'];
        $context->cookie->fsredirect_deletedFilter_type = $filter['type'];

        $deleted_url_list_content = FsRedirectDeletedModel::getListContent($filter);
        if ($deleted_url_list_content) {
            foreach ($deleted_url_list_content as $row) {
                self::$deleted_url_list_content[$row['id_fsredirect_deleted']] = $row;
                self::$deleted_url_list_content[$row['id_fsredirect_deleted']]['type'] =
                    $this->deleted_names[$row['type']];
            }
            $deleted_url_list->listTotal = FsRedirectDeletedModel::getListContentCount($filter);
        } else {
            self::$deleted_url_list_content = array();
        }

        $context->smarty->assign(array(
            'is_ps_15' => $this->isPs15(),
            'module_export_url' => $this->url().'&export'.$this->name.'_deleted',
            'module_delete_all_url' => $this->url().'&empty'.$this->name.'_deleted',
            'generated_list' => $deleted_url_list->generateList(
                self::$deleted_url_list_content,
                $this->deleted_url_fields_list
            )
        ));

        $html .= $this->display(__FILE__, 'views/templates/admin/list_wrapper_deleted.tpl');

        $context->smarty->assign(array(
            'is_ps_15' => $this->isPs15(),
            'is_ps_min_16' => $this->isPsMin16(),
            'module_base_url' => $this->getModuleBaseUrl()
        ));

        $html .= $this->display(__FILE__, 'views/templates/admin/help.tpl');

        return $html;
    }

    protected function initCreateForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->create_fields_form[0]['form'] = array(
            'legend' => array(
                'title' => 'Create URL Redirect'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Old URL matching type:'),
                    'name' => 'matching_type',
                    'options' => array(
                        'query' => $this->matching_types,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('How the old URL matches the current request url.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Old URL:'),
                    'lang' => false,
                    'name' => 'old_url',
                    'size' => 70,
                    'required' => true,
                    'desc' => $this->l('Must be the URL part after the base URL, start with "/".
                    Example: "/3-women" or any string if matching type is "Contains"').
                        '<br /><strong>'.$this->l('Base URL:').' '.Tools::getShopDomainSsl(true, true).
                        Tools::substr(__PS_BASE_URI__, 0, -1).'</strong>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('New URL:'),
                    'lang' => false,
                    'name' => 'new_url',
                    'size' => 70,
                    'required' => true,
                    'desc' => $this->l('Can be a relative URL to the base URL or absolute URL.
                    Example: "/8-dresses" or "http://www.prestashop.com"'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Redirect type:'),
                    'name' => 'redirect_type',
                    'options' => array(
                        'query' => $this->redirect_types,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Create'),
            )
        );

        if ($this->isPs15()) {
            $this->create_fields_form[0]['form']['submit']['class'] = 'button';
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = false;
        $helper->show_toolbar = false;
        $helper->title[] = $this->displayName;
        $helper->submit_action = 'create'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Create'),
                    'href' => $this->url().'&create'.$this->name,
                ),
        );
        return $helper;
    }

    protected function initImportForm()
    {
        $sample_csv_url = $this->_path.'tmp/redirect-sample.csv';
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->import_fields_form[0]['form'] = array(
            'legend' => array(
                'title' => 'Import URL Redirects'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Field separator:'),
                    'lang' => false,
                    'name' => 'csv_field_separator',
                    'size' => 70,
                    'required' => true,
                    'desc' => $this->l('Enter in the character that is used in your CSV import file that separates each
                     field or column. Make sure this character does not appear elsewhere in the CSV file.
                     Usually in a CSV (Comma Separated Values) file this character is a \',\'. Make sure your
                     columns themselves don\'t contain commas by searching and replacing all commas with just a
                     blank value.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Field enclosure:'),
                    'lang' => false,
                    'name' => 'csv_field_enclosure',
                    'size' => 70,
                    'required' => false,
                    'desc' => $this->l('Which character is each field enclosed by? All fields must have this character
                        around them. For example, a record might look like this:').
                        '<br/><br/>"/product_url.html", "equalsto", "/new_product_url.html","301","1"',
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('CSV file:'),
                    'lang' => false,
                    'name' => 'csv',
                    'size' => 70,
                    'required' => true,
                    'desc' => $this->l('The CSV file must contains a header row!').' <a href="'.$sample_csv_url.
                        '" target="_blank">'.$this->l('Download sample CSV file').'</a>.',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Import')
            )
        );

        if ($this->isPs15()) {
            $this->import_fields_form[0]['form']['submit']['class'] = 'button';
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = false;
        $helper->show_toolbar = false;
        $helper->title[] = $this->displayName;
        $helper->submit_action = 'import'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Create'),
                    'href' => $this->url().'&import'.$this->name,
                ),
        );
        return $helper;
    }

    protected function initRedirectList()
    {
        $this->redirect_fields_list = array(
            'id_fsredirect' => array(
                'title' => $this->l('ID'),
                'width' => 80,
            ),
            'matching_type' => array(
                'title' => $this->l('Matching type'),
                'type' => 'select',
                'list' => $this->matching_names,
                'filter_key' => 'matching_type',
            ),
            'old_url' => array(
                'title' => $this->l('Old URL'),
            ),
            'new_url' => array(
                'title' => $this->l('New URL'),
            ),
            'redirect_type' => array(
                'title' => $this->l('Redirect type'),
                'align' => 'center',
                'type' => 'select',
                'list' => $this->redirect_names,
                'filter_key' => 'redirect_type',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => 100,
                'type' => 'bool',
                'active' => 'status',
                'align' => 'center',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->no_link = true;
        $helper->identifier = 'id_fsredirect';
        $helper->actions = array('delete');
        $helper->show_toolbar = false;
        $helper->imageType = 'jpg';
        $helper->title[] = $this->displayName.'s';
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->toolbar_btn = array(
            'delete' => array(
                'desc' => $this->l('Delete All URL Redirects'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&empty'.
                    $this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'js' => 'if (confirm(\''.$this->l('Are you sure you want to delete all URL Redirects?').
                    '\')){return true;} else {return false;}',
            ),
            'export' => array(
                'desc' => $this->l('Export'),
                'href' => $this->url().'&export'.$this->name,
            ),
        );

        return $helper;
    }

    protected function initDeletedUrlList()
    {
        $this->deleted_url_fields_list = array(
            'id_fsredirect_deleted' => array(
                'title' => $this->l('ID'),
                'width' => 80,
            ),
            'name' => array(
                'title' => $this->l('Deleted name'),
            ),
            'url' => array(
                'title' => $this->l('Deleted URL'),
            ),
            'type' => array(
                'title' => $this->l('Deleted type'),
                'align' => 'center',
                'type' => 'select',
                'list' => $this->deleted_names,
                'filter_key' => 'type',
            ),
        );

        $helper = new FsRedirectHelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->no_link = true;
        $helper->identifier = 'id_fsredirect_deleted';
        $helper->actions = array('converttoredirect', 'delete');
        $helper->show_toolbar = false;
        $helper->imageType = 'jpg';
        $helper->title[] = $this->l('Deleted Items');
        $helper->table = $this->name.'_deleted';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->toolbar_btn = array(
            'delete' => array(
                'desc' => $this->l('Delete All Deleted Items'),
                'href' => $this->url().'&empty'.$this->name.'_deleted',
                'js' => 'if (confirm(\''.$this->l('Are you sure you want to delete all deleted items?').
                    '\')){return true;} else {return false;}',
            ),
            'export' => array(
                'desc' => $this->l('Export'),
                'href' => $this->url().'&export'.$this->name.'_deleted',
            ),
        );

        return $helper;
    }

    public function displayConverttoredirectLink($token, $id, $name)
    {
        $context = Context::getContext();
        $url = '';
        if (isset(self::$deleted_url_list_content[$id])) {
            $url = self::$deleted_url_list_content[$id]['url'];
        }

        $context->smarty->assign(array(
            'list_item_link' => $url,
            'list_item_link_token' => $token,
            'list_item_link_name' => $name
        ));

        return $this->display(__FILE__, 'views/templates/admin/list_item_link.tpl');
    }

    #################### ADMIN HOOKS ####################

    public function hookActionObjectProductDeleteBefore($params)
    {
        $p = $params['object'];
        if (Shop::isFeatureActive()) {
            foreach (Shop::getShops() as $shop) {
                $id_shop = $shop['id_shop'];
                $s = new Shop($id_shop);
                $shop_url = $s->getBaseURL();
                if (Tools::substr($shop_url, -1) == '/') {
                    $shop_url = Tools::substr($shop_url, 0, -1);
                }
                foreach (Language::getLanguages(false) as $lang) {
                    $id_lang = $lang['id_lang'];
                    $url = $this->context->link->getProductLink($p->id, null, null, null, $id_lang, $id_shop);
                    $url = str_replace($shop_url, '', $url);

                    if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                        $fsredirect_deleted = new FsRedirectDeletedModel();
                        $fsredirect_deleted->id_shop = $id_shop;
                        $fsredirect_deleted->name = $p->name[$id_lang];
                        $fsredirect_deleted->url = $url;
                        $fsredirect_deleted->type = 'product';
                        $fsredirect_deleted->save();
                    }
                }
            }
        } else {
            $id_shop = $p->id_shop_default;
            $s = new Shop($id_shop);
            $shop_url = $s->getBaseURL();
            if (Tools::substr($shop_url, -1) == '/') {
                $shop_url = Tools::substr($shop_url, 0, -1);
            }
            foreach (Language::getLanguages(false) as $lang) {
                $id_lang = $lang['id_lang'];
                $url = $this->context->link->getProductLink($p->id, null, null, null, $id_lang);
                $url = str_replace($shop_url, '', $url);

                if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                    $fsredirect_deleted = new FsRedirectDeletedModel();
                    $fsredirect_deleted->id_shop = $id_shop;
                    $fsredirect_deleted->name = $p->name[$id_lang];
                    $fsredirect_deleted->url = $url;
                    $fsredirect_deleted->type = 'product';
                    $fsredirect_deleted->save();
                }
            }
        }
    }

    public function hookActionObjectCategoryDeleteBefore($params)
    {
        $c = $params['object'];
        if (Shop::isFeatureActive()) {
            foreach (Shop::getShops() as $shop) {
                $id_shop = $shop['id_shop'];
                $s = new Shop($id_shop);
                $shop_url = $s->getBaseURL();
                if (Tools::substr($shop_url, -1) == '/') {
                    $shop_url = Tools::substr($shop_url, 0, -1);
                }
                foreach (Language::getLanguages(false) as $lang) {
                    $id_lang = $lang['id_lang'];
                    $url = $this->context->link->getCategoryLink($c->id, null, $id_lang, null, $id_shop);
                    $url = str_replace($shop_url, '', $url);

                    if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                        $fsredirect_deleted = new FsRedirectDeletedModel();
                        $fsredirect_deleted->id_shop = $id_shop;
                        $fsredirect_deleted->name = $c->name[$id_lang];
                        $fsredirect_deleted->url = $url;
                        $fsredirect_deleted->type = 'category';
                        $fsredirect_deleted->save();
                    }
                }
            }
        } else {
            $id_shop = $c->id_shop_default;
            $s = new Shop($id_shop);
            $shop_url = $s->getBaseURL();
            if (Tools::substr($shop_url, -1) == '/') {
                $shop_url = Tools::substr($shop_url, 0, -1);
            }
            foreach (Language::getLanguages(false) as $lang) {
                $id_lang = $lang['id_lang'];
                $url = $this->context->link->getCategoryLink($c->id, null, $id_lang, null, $id_shop);
                $url = str_replace($shop_url, '', $url);

                if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                    $fsredirect_deleted = new FsRedirectDeletedModel();
                    $fsredirect_deleted->id_shop = $id_shop;
                    $fsredirect_deleted->name = $c->name[$id_lang];
                    $fsredirect_deleted->url = $url;
                    $fsredirect_deleted->type = 'category';
                    $fsredirect_deleted->save();
                }
            }
        }
    }

    public function hookActionObjectManufacturerDeleteBefore($params)
    {
        $m = $params['object'];
        if (Shop::isFeatureActive()) {
            foreach (Shop::getShops() as $shop) {
                $id_shop = $shop['id_shop'];
                $s = new Shop($id_shop);
                $shop_url = $s->getBaseURL();
                if (Tools::substr($shop_url, -1) == '/') {
                    $shop_url = Tools::substr($shop_url, 0, -1);
                }
                foreach (Language::getLanguages(false) as $lang) {
                    $id_lang = $lang['id_lang'];
                    $url = $this->context->link->getManufacturerLink($m->id, null, $id_lang, $id_shop);
                    $url = str_replace($shop_url, '', $url);

                    if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                        $fsredirect_deleted = new FsRedirectDeletedModel();
                        $fsredirect_deleted->id_shop = $id_shop;
                        $fsredirect_deleted->name = $m->name;
                        $fsredirect_deleted->url = $url;
                        $fsredirect_deleted->type = 'manufacturer';
                        $fsredirect_deleted->save();
                    }
                }
            }
        } else {
            $id_shop = $this->context->shop->id;
            $s = new Shop($id_shop);
            $shop_url = $s->getBaseURL();
            if (Tools::substr($shop_url, -1) == '/') {
                $shop_url = Tools::substr($shop_url, 0, -1);
            }
            foreach (Language::getLanguages(false) as $lang) {
                $id_lang = $lang['id_lang'];
                $url = $this->context->link->getManufacturerLink($m->id, null, $id_lang, $id_shop);
                $url = str_replace($shop_url, '', $url);

                if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                    $fsredirect_deleted = new FsRedirectDeletedModel();
                    $fsredirect_deleted->id_shop = $id_shop;
                    $fsredirect_deleted->name = $m->name;
                    $fsredirect_deleted->url = $url;
                    $fsredirect_deleted->type = 'manufacturer';
                    $fsredirect_deleted->save();
                }
            }
        }
    }

    public function hookActionObjectSupplierDeleteBefore($params)
    {
        $s = $params['object'];
        if (Shop::isFeatureActive()) {
            foreach (Shop::getShops() as $shop) {
                $id_shop = $shop['id_shop'];
                $s = new Shop($id_shop);
                $shop_url = $s->getBaseURL();
                if (Tools::substr($shop_url, -1) == '/') {
                    $shop_url = Tools::substr($shop_url, 0, -1);
                }
                foreach (Language::getLanguages(false) as $lang) {
                    $id_lang = $lang['id_lang'];
                    $url = $this->context->link->getSupplierLink($s->id, null, $id_lang, $id_shop);
                    $url = str_replace($shop_url, '', $url);

                    if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                        $fsredirect_deleted = new FsRedirectDeletedModel();
                        $fsredirect_deleted->id_shop = $id_shop;
                        $fsredirect_deleted->name = $s->name;
                        $fsredirect_deleted->url = $url;
                        $fsredirect_deleted->type = 'supplier';
                        $fsredirect_deleted->save();
                    }
                }
            }
        } else {
            $id_shop = $this->context->shop->id;
            $s = new Shop($id_shop);
            $shop_url = $s->getBaseURL();
            if (Tools::substr($shop_url, -1) == '/') {
                $shop_url = Tools::substr($shop_url, 0, -1);
            }
            foreach (Language::getLanguages(false) as $lang) {
                $id_lang = $lang['id_lang'];
                $url = $this->context->link->getSupplierLink($s->id, null, $id_lang, $id_shop);
                $url = str_replace($shop_url, '', $url);

                if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                    $fsredirect_deleted = new FsRedirectDeletedModel();
                    $fsredirect_deleted->id_shop = $id_shop;
                    $fsredirect_deleted->name = $s->name;
                    $fsredirect_deleted->url = $url;
                    $fsredirect_deleted->type = 'supplier';
                    $fsredirect_deleted->save();
                }
            }
        }
    }

    public function hookActionObjectCMSDeleteBefore($params)
    {
        $cms = $params['object'];
        if (Shop::isFeatureActive()) {
            foreach (Shop::getShops() as $shop) {
                $id_shop = $shop['id_shop'];
                $s = new Shop($id_shop);
                $shop_url = $s->getBaseURL();
                if (Tools::substr($shop_url, -1) == '/') {
                    $shop_url = Tools::substr($shop_url, 0, -1);
                }
                foreach (Language::getLanguages(false) as $lang) {
                    $id_lang = $lang['id_lang'];
                    $url = $this->context->link->getCMSLink($cms->id, null, null, $id_lang, $id_shop);
                    $url = str_replace($shop_url, '', $url);

                    if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                        $fsredirect_deleted = new FsRedirectDeletedModel();
                        $fsredirect_deleted->id_shop = $id_shop;
                        $fsredirect_deleted->name = $cms->meta_title[$id_lang];
                        $fsredirect_deleted->url = $url;
                        $fsredirect_deleted->type = 'cms';
                        $fsredirect_deleted->save();
                    }
                }
            }
        } else {
            $id_shop = $this->context->shop->id;
            $s = new Shop($id_shop);
            $shop_url = $s->getBaseURL();
            if (Tools::substr($shop_url, -1) == '/') {
                $shop_url = Tools::substr($shop_url, 0, -1);
            }
            foreach (Language::getLanguages(false) as $lang) {
                $id_lang = $lang['id_lang'];
                $url = $this->context->link->getCMSLink($cms->id, null, null, $id_lang, $id_shop);
                $url = str_replace($shop_url, '', $url);

                if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                    $fsredirect_deleted = new FsRedirectDeletedModel();
                    $fsredirect_deleted->id_shop = $id_shop;
                    $fsredirect_deleted->name = $cms->meta_title[$id_lang];
                    $fsredirect_deleted->url = $url;
                    $fsredirect_deleted->type = 'cms';
                    $fsredirect_deleted->save();
                }
            }
        }
    }

    public function hookActionObjectCMSCategoryDeleteBefore($params)
    {
        $cms_category = $params['object'];
        if (Shop::isFeatureActive()) {
            foreach (Shop::getShops() as $shop) {
                $id_shop = $shop['id_shop'];
                $s = new Shop($id_shop);
                $shop_url = $s->getBaseURL();
                if (Tools::substr($shop_url, -1) == '/') {
                    $shop_url = Tools::substr($shop_url, 0, -1);
                }
                foreach (Language::getLanguages(false) as $lang) {
                    $id_lang = $lang['id_lang'];
                    $url = $this->context->link->getCMSCategoryLink($cms_category->id, null, $id_lang, $id_shop);
                    $url = str_replace($shop_url, '', $url);

                    if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                        $fsredirect_deleted = new FsRedirectDeletedModel();
                        $fsredirect_deleted->id_shop = $id_shop;
                        $fsredirect_deleted->name = $cms_category->name[$id_lang];
                        $fsredirect_deleted->url = $url;
                        $fsredirect_deleted->type = 'cms_category';
                        $fsredirect_deleted->save();
                    }
                }
            }
        } else {
            $id_shop = $this->context->shop->id;
            $s = new Shop($id_shop);
            $shop_url = $s->getBaseURL();
            if (Tools::substr($shop_url, -1) == '/') {
                $shop_url = Tools::substr($shop_url, 0, -1);
            }
            foreach (Language::getLanguages(false) as $lang) {
                $id_lang = $lang['id_lang'];
                $url = $this->context->link->getCMSCategoryLink($cms_category->id, null, $id_lang, $id_shop);
                $url = str_replace($shop_url, '', $url);

                if (!FsRedirectDeletedModel::isUrlInDatabase($url, $id_shop)) {
                    $fsredirect_deleted = new FsRedirectDeletedModel();
                    $fsredirect_deleted->id_shop = $id_shop;
                    $fsredirect_deleted->name = $cms_category->name[$id_lang];
                    $fsredirect_deleted->url = $url;
                    $fsredirect_deleted->type = 'cms_category';
                    $fsredirect_deleted->save();
                }
            }
        }
    }

    #################### FRONT HOOKS ####################

    public function hookDisplayHeader($params)
    {
        $request_uri = urldecode(FsRedirectTools::getRequestUri());
        $redirects = FsRedirectModel::getAllActive();
        if ($redirects) {
            foreach ($redirects as $redirect) {
                switch ($redirect['matching_type']) {
                    case 'equalsto':
                        if ($request_uri == $redirect['old_url']) {
                            FsRedirectTools::redirect(
                                $redirect['new_url'],
                                $this->redirect_headers[$redirect['redirect_type']]
                            );
                        }
                        break;
                    case 'beginswith':
                        if (FsRedirectTools::startsWith($request_uri, $redirect['old_url'])) {
                            FsRedirectTools::redirect(
                                $redirect['new_url'],
                                $this->redirect_headers[$redirect['redirect_type']]
                            );
                        }
                        break;
                    case 'contains':
                        if (FsRedirectTools::contains($request_uri, $redirect['old_url'])) {
                            FsRedirectTools::redirect(
                                $redirect['new_url'],
                                $this->redirect_headers[$redirect['redirect_type']]
                            );
                        }
                        break;
                }
            }
        }
    }

    public function redirect()
    {
        $this->hookDisplayHeader(null);
    }

    public function getPath()
    {
        return $this->_path;
    }

    ################ 1.7 ################

    /**
     * @return bool
     */
    public function isPs15()
    {
        return self::isPs15Static();
    }

    /**
     * @return bool
     */
    public static function isPs15Static()
    {
        return version_compare(_PS_VERSION_, '1.5.0.0', '>=') &&
        version_compare(_PS_VERSION_, '1.6.0.0', '<');
    }

    /**
     * @return bool
     */
    public function isPsMin16()
    {
        return self::isPsMin16Static();
    }

    /**
     * @return bool
     */
    public static function isPsMin16Static()
    {
        return version_compare(_PS_VERSION_, '1.6.0.0', '>=');
    }

    /**
     * @return bool
     */
    public function isPs16()
    {
        return self::isPs16Static();
    }

    /**
     * @return bool
     */
    public static function isPs16Static()
    {
        return version_compare(_PS_VERSION_, '1.6.0.0', '>=') &&
        version_compare(_PS_VERSION_, '1.7.0.0', '<');
    }

    /**
     * @return bool
     */
    public function isPsMin17()
    {
        return self::isPsMin17Static();
    }

    /**
     * @return bool
     */
    public static function isPsMin17Static()
    {
        return version_compare(_PS_VERSION_, '1.7.0.0', '>=');
    }

    /**
     * @param $data
     * @return string
     */
    public function jsonEncode($data)
    {
        return self::jsonEncodeStatic($data);
    }

    /**
     * @param $data
     * @return string
     */
    public static function jsonEncodeStatic($data)
    {
        if (self::isPsMin17Static()) {
            return json_encode($data);
        }
        return Tools::jsonEncode($data);
    }

    /**
     * @param $data
     * @param bool $assoc
     * @return array|mixed
     */
    public function jsonDecode($data, $assoc = false)
    {
        return self::jsonDecodeStatic($data, $assoc);
    }

    /**
     * @param $data
     * @param bool $assoc
     * @return array|mixed
     */
    public static function jsonDecodeStatic($data, $assoc = false)
    {
        if (self::isPsMin17Static()) {
            return json_decode($data, $assoc);
        }
        return Tools::jsonDecode($data, $assoc);
    }
}
