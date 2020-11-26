<?php
if(defined('_PS_VERSION_') == 0)
    exit('Restricted Access!!!');

include_once(_PS_MODULE_DIR_ .'fozzy_promotions/classes/PromotionsRuleClass.php');
include_once(dirname(__FILE__).'/../../fozzy_promotions.php');

class AdminPromotionsController extends ModuleAdminController {
    public function __construct() {
        $this->table = 'promotions_rules';
        $this->list_no_link = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->bootstrap = true;
        $this->lang = true;
        $this->_defaultOrderBy = 'id_promotions_rules';
        $this->_defaultOrderWay = 'DESC';

        //nove_dateofdelivery_cart
        //ps_nove_dateofdelivery_block

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array('text' => $this->l('Remove marked rules'), 'icon' => 'icon-trash', 'confirm' => $this->l('Delete selected items?'),
            )
        );

        $this->fields_list = array(
            'id_promotions_rules' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'title_rule' => array(
                'title' => $this->l('Title rule'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'priority_rule' => array(
                'title' => $this->l('Priority Rule'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'code_rule' => array(
                'title' => $this->l('Code rule'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'count_rule' => array(
                'title' => $this->l('Count rule'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'date_to' => array(
                'title' => $this->l('Date to'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'delivery_block' => array(
                'title' => $this->l('Delivery block'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-xs'
            ),
        );
    }

    /**
     * Добавление кнопок в шапку модуля.
     * Adding buttons to the module header.
     */
    public function initPageHeaderToolbar() {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['add_new'] = array(
                'href' => self::$currentIndex.'&addpromotions_rules&token='.$this->token,
                'desc' => $this->l('Add promotion', null, null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['refresh_all'] = array(
                'href' => self::$currentIndex.'&action=refresh&token='.$this->token,
                'desc' => $this->l('Refresh promotion', null, null, false),
                'icon' => 'process-icon-refresh'
            );
        }
        parent::initPageHeaderToolbar();
    }

    /**
     * Смена статуса акции.
     * Change of promotion status.
     * @return false|ObjectModel|void
     * @throws PrestaShopException
     */
    public function processStatus() {
        if (!$id_promotions_rules = (int)Tools::getValue('id_promotions_rules')) {
            die(Tools::jsonEncode(array('success' => false, 'error' => true, 'text' => $this->l('Failed to update the status'))));
        } else {
            $id_promotions_rules = Tools::getValue('id_promotions_rules');
            $promotions_rules = new PromotionsRuleClass($id_promotions_rules);
            if (Validate::isLoadedObject($promotions_rules)) {

                $link_redirect = $this->context->link->getAdminLink('AdminPromotions');

                $promotions_rules->active = $promotions_rules->active == 1 ? 0 : 1;
                $promotions_rules->save() ? Tools::redirectAdmin($link_redirect . '&confirm='.$this->l('Transaction status changed')) : Tools::redirectAdmin($link_redirect . '&error='.$this->l('Some thing is wrong. Transaction status not changed'));
            }
        }
    }

    /**
     * Форма добаления новой акции.
     * @return string|void
     * @throws SmartyException
     */
    public function renderForm() {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        if(empty((int)Tools::getValue('id_promotions_rules', null))) {
            $title = $this->l('Add share');
            $title_button = $this->l('Save');
            $icon = 'icon-plus-sign-alt';
        } else {
            $title = $this->l('Edit a promotion');
            $title_button = $this->l('Edit a promotion button');
            $icon = 'icon-cog';
        }

        $type_rule = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "promotions_types_rules` WHERE 1");
        $delivery_block = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "promotions_delivery_block` WHERE 1 ORDER BY `id_window`");

        $this->fields_form = array(
            'legend' => array(
                'title' => $title,
                'icon' => $icon,
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_promotions_rules',
                ),
                array(
                    'label' => $this->l('Title rule'),
                    'name' => 'title_rule',
                    'type' => 'select',
                    'hint' => $this->l('Select the type of promotion'),
                    'required' => true,
                    'options' => array(
                        'query' => $type_rule,
                        'id' => 'id_type_rule',
                        'name' => 'title_rule',
                    ),
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('Date from'),
                    'name' => 'date_from',
                    'hint' => $this->l('Start of the action'),
                    'required' => true
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('Date to.'),
                    'name' => 'date_to',
                    'hint' => $this->l('End of action'),
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Delivery  Block'),
                    'name' => 'delivery_block',
                    'hint' => $this->l('Select delivery window'),
                    'required' => true,
                    'options' => array(
                        'query' => $delivery_block,
                        'id' => 'window_block',
                        'name' => 'window_name',
                    ),
                ),
                array(
                    'label' => $this->l('Count rule'),
                    'name' => 'count_rule',
                    'type' => 'text',
                    'col' => 1,
                    'hint' => $this->l('Available stock amount'),
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Priority Rule'),
                    'name' => 'priority_rule',
                    'col' => 1,
                    'hint' => $this->l('Promotion priority'),
                    'required' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 'fixed-width-xs',
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
                    )
                )
            ),
            'submit' => array(
                'title' => $title_button
            )
        );

        $promotions_rules = (int)Tools::getValue('id_promotions_rules', null);
        if ($promotions_rules != null) {
            $promotions_rules = new PromotionsRuleClass($promotions_rules);

            $this->fields_value = array(
                'id_promotions_rules' => $promotions_rules->id_promotions_rules,
                'id_rule' => $promotions_rules->id_rule,
                'date_from' => $promotions_rules->date_from,
                'date_to' => $promotions_rules->date_to,
                'delivery_block' => $promotions_rules->delivery_block,
                'count_rule' => $promotions_rules->count_rule,
                'priority_rule' => $promotions_rules->priority_rule,
                'active' => $promotions_rules->active,
            );
        }

        return parent::renderForm();
    }

    /**
     * Функция обработки действи по нажатию на кнопки:
     *      - Добавить акцию.
     *      - Редактировать акцию.
     *      - Удалить акцию.
     * @return bool|ObjectModel|void
     * @throws PrestaShopException
     */
    public function postProcess() {
        echo '<pre>';
        var_dump($_POST);
        die;
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $id_promotions_rules = Tools::getValue('id_promotions_rules');

            if($id_promotions_rules) {
                $promotions_rules = new PromotionsRuleClass($id_promotions_rules);
            } else {
                $promotions_rules = new PromotionsRuleClass();
                if(Tools::getValue('title_rule', 0) == 1) {
                    $sql_add_picker = "INSERT INTO `" . _DB_PREFIX_ . "promotions_rules_lang` (`id_lang`, `title_rule`) VALUES ('1', 'Бесплатная доставка на дату и окно доставки')";
                    Db::getInstance()->execute($sql_add_picker);
                }
            }

            $promotions_rules->id_rule = Tools::getValue('title_rule', 0);
            $promotions_rules->date_from =  Tools::getValue('date_from', null);
            $promotions_rules->code_rule =  'NVBNKTRNS_' . Tools::getValue('count_rule', 0);
            $promotions_rules->date_to =  Tools::getValue('date_to', null);
            $promotions_rules->delivery_block =  Tools::getValue('delivery_block', 0);
            $promotions_rules->count_rule =  Tools::getValue('count_rule', 0);
            $promotions_rules->priority_rule =  Tools::getValue('priority_rule', 0);
            $promotions_rules->active =  Tools::getValue('active');

            if ($id_promotions_rules) {
                $promotions_rules->update();
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                $promotions_rules->save();
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } elseif (Tools::isSubmit('delete'.$this->table)) {
            if (!(Tools::getValue('id_promotions_rules'))) {
                return;
            } else {
                $promotions_rules = new PromotionsRuleClass((int)Tools::getValue('id_promotions_rules'));
                $promotions_rules->delete();
                Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$this->token);
            }
        } else {
            return parent::postProcess();
        }

        if (Tools::isSubmit('submitBulkdeletepromotions_rules')){
            $this_delete_rule = $_POST;
            for ($i = 0; $i < count($this_delete_rule['table_pickerBox']); $i++) {
                Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."staff_schedule_picker` WHERE `id_person` = ".$this_delete_persons['table_pickerBox'][$i]);
            }
            $output .= $this->displayConfirmation($this->l('The collector schedule has been removed.'));
        }
    }
}