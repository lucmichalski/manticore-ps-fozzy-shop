<?php
/**
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
 *  @author    Yuri Denisov <contact@splashmart.ru>
 *  @copyright 2014-2017 Yuri Denisov
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once(dirname(__FILE__).'/../../models/OOCOrderFieldsModel.php');
include_once(dirname(__FILE__).'/../../models/OOCTypeOrderFieldsModel.php');

class AdminOOCOrderFieldsController extends ModuleAdminController
{
    const OOC_ORDER_FIELDS_CONTROLLER = 'AdminOOCOrderFields';
    const OOC_CONTROLLER = 'AdminOOC';
    const ADMIN_MODULES = 'AdminModules';

    public function __construct()
    {
        $this->table = 'sm_ooc_order_fields';
        $this->className = 'OOCOrderFieldsModel';
        $this->bootstrap = true;
        $this->lang = true;
        $this->_use_found_rows = false;

        parent::__construct();
    }

    public function renderList()
    {
        $data = $this->createTemplate('preview_window.tpl');
        $data->assign('action_url', __PS_BASE_URI__.'modules/orderinoneclick/ajax.php');
        $data->assign('preview_shop_id', (int)$this->context->shop->id);
        $data->assign('id_lang', (int)$this->context->language->id);

        if (OOCToolsClass::notAllowOwnFields()) {
            $this->warnings[] = $this->l('This option for current shop is disabled in general configuration');
            return $data->fetch();
        }

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->toolbar_title = $this->l('Quick Orders Fields');
        $this->position_identifier = 'position';
        $this->_orderBy = 'position';
        $this->_where .= Shop::AddSqlRestriction(false, 'a');

        $this->fields_list['required'] = array(
            'title' => $this->l('Required'),
            'search' => false,
            'width' => 100,
            'callback' => 'getIconSelectedRequired',
            'align' => 'center',
        );

        $this->fields_list['name'] = array(
            'title' => $this->l('Name'),
            'search' => false,
            'width' => 'auto',
        );

        $this->fields_list['description'] = array(
            'title' => $this->l('Placeholder'),
            'search' => false,
            'width' => 'auto',
        );

        if ((Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_SHOP)) ||
            !Shop::isFeatureActive()
        ) {
            $this->fields_list['position'] = array(
                'title' => $this->l('Position'),
                'position' => 'position',
                'search' => false,
                'width' => 100,
            );
        }

        $this->fields_list['active'] = array(
            'title' => $this->l('Active'),
            'active' => 'status',
            'search' => false,
            'width' => 100,
        );

        $this->fields_list['id_sm_ooc_type_order_field'] = array(
            'title' => $this->l('Type'),
            'search' => false,
            'width' => 'auto',
            'callback' => 'displayTypeName',
            'align' => 'center',
        );

        if (Shop::isFeatureActive() && (Shop::getContext() != Shop::CONTEXT_SHOP)) {
            $this->fields_list['id_shop'] = array(
                'title' => $this->l('Shop'),
                'width' => 100,
                'search' => false,
                'align' => 'center',
                'callback' => 'getShopName',
                'callback_object' => 'OOCToolsClass',
            );
        }

        return $data->fetch().parent::renderList();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/ooc_window.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/ooc_window.css');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/spinner.css');
    }

    public function renderForm()
    {
        if (OOCToolsClass::notAllowOwnFields()) {
            return "This option for you shop is disabled in general configuration";
        }

        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = (int)($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }

        $this->display = Validate::isLoadedObject($this->object) ? 'edit' : 'add';

        if (Shop::isFeatureActive() &&
            Shop::getContext() != Shop::CONTEXT_SHOP &&
            $this->display == 'add'
        ) {
            return "Please, shoose one shop before add a fields";
        }

        $title = $this->display == 'edit' ? $this->l('Edit a order field') : $this->l('Add a new order field');

        $this->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->languages = $languages;
        $this->fields_form = array(
            'legend' => array(
                    'title' => $title
            ),
            'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'values' => array(
                                    array(
                                        'id'    => 'active_on',
                                        'value' => 1,
                                    ),
                                    array(
                                        'id'    => 'active_off',
                                        'value' => 0,
                                    ),
                                ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Required'),
                        'name' => 'required',
                        'values' => array(
                                    array(
                                        'id'    => 'active_on',
                                        'value' => 1,
                                    ),
                                    array(
                                        'id'    => 'active_off',
                                        'value' => 0,
                                    ),
                                ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Type of validation:'),
                        'name' => 'id_sm_ooc_type_order_field',
                        'required' => true,
                        'options' => array(
                            'query' => OOCTypeOrderFieldsModel::getAllTypes(),
                            'id' => 'id_sm_ooc_type_order_field',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name of Field'),
                        'required' => true,
                        'name' => 'name',
                        'col' => '2',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Placeholder'),
                        'name' => 'description',
                        'col' => '4',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tooltip'),
                        'name' => 'tip',
                        'col' => '4',
                        'lang' => true,
                    )
            ),
            'submit' => array(
                    'title' => $this->l('Save')
            )
        );

        return parent::renderForm();
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Quick Orders Fields Settings');

        if ((!$this->display && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) ||
            !Shop::isFeatureActive()
        ) {
            $this->page_header_toolbar_btn['preview'] = array(
                'href' => '#',
                'desc' => $this->l('Test order window'),
                'icon' => 'process-icon-preview',
                'class' => 'show_ooc_window',
                'help' => $this->l('You can view orders in customer currency or in your default currency'),
            );

            if (!OOCToolsClass::notAllowOwnFields()) {
                $settings_url = $this->context->link->getAdminLink(self::ADMIN_MODULES);
                $settings_url .= '&configure='.$this->module->name;
                $settings_url .= '&confoocwindow'.$this->module->name;
                $settings_url .= '&token='.Tools::getAdminTokenLite(self::ADMIN_MODULES);
                $this->page_header_toolbar_btn['config'] = array(
                    'href' => $settings_url,
                    'desc' => $this->l('Settings of Quick Order Window'),
                    'icon' => 'process-icon-cogs',
                );
            }
        }

        if ($this->display === 'edit' || $this->display === 'add') {
            $back_url = $this->context->link->getAdminLink(self::OOC_ORDER_FIELDS_CONTROLLER);
            $this->page_header_toolbar_btn['back_previous_page'] = array(
                'href' => $back_url,
                'desc' => $this->l('List Order Fields'),
                'icon' => 'process-icon-back'
            );
        } else {
            $back_url = $this->context->link->getAdminLink(self::OOC_CONTROLLER);
            $this->page_header_toolbar_btn['back_previous_page'] = array(
                'href' => $back_url,
                'desc' => $this->l('List Orders'),
                'icon' => 'process-icon-back'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /*
     * AJAX
     */
    public function ajaxProcessUpdatePositions()
    {
        $positions = Tools::getValue($this->table);

        foreach ($positions as $key => $position) {
            $pos = explode('_', $position);
            $order_field = new OOCOrderFieldsModel($pos[2]);
            $order_field->position = $key;
            if (Context::getContext()->shop->id == $order_field->id_shop) {
                $order_field->save();
            }
        }
    }

    /**
     * TOOLS
     */
    public function displayTypeName($id_sm_ooc_type_order_field = null)
    {
        if ($id_sm_ooc_type_order_field) {
            $type = new OOCTypeOrderFieldsModel($id_sm_ooc_type_order_field);
            return $type->name;
        }
        return null;
    }

    public function getIconSelectedRequired($required)
    {
        if ($required) {
            return '<i class="icon-asterisk"></i>';
        }
        return null;
    }
}
