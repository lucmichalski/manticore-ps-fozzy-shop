<?php
if(defined('_PS_VERSION_') == 0)
    exit('Restricted Access!!!');

include_once(dirname(__FILE__).'/../../fozzy_promotions.php');

class AdminPromotionsController extends ModuleAdminController {
    public function __construct() {
        $this->table = 'promotions_rule';
        $this->list_no_link = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->bootstrap = true;
        $this->lang = true;
        $this->_defaultOrderBy = 'id_promotions_rule';
        $this->_defaultOrderWay = 'DESC';


        //nove_dateofdelivery_cart
        //ps_nove_dateofdelivery_block

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array('text' => $this->l('Удалить отмеченных сотрудников'), 'icon' => 'icon-trash', 'confirm' => 'Delete selected items?',
            )
        );

        $this->fields_list = array(
            'id_promotions_rule' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'align' => 'text-center',
                'filter' => true
            ),
            'title_rule_lang' => array(
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
//    public function initPageHeaderToolbar() {
//        if (empty($this->display)) {
//            $this->page_header_toolbar_btn['add_new'] = array(
//                'href' => self::$currentIndex.'&addpromotions_rule&token='.$this->token,
//                'desc' => $this->l('Add promotion', null, null, false),
//                'icon' => 'process-icon-new'
//            );
//            $this->page_header_toolbar_btn['refresh_all'] = array(
//                'href' => self::$currentIndex.'&action=refresh&token='.$this->token,
//                'desc' => $this->l('Refresh promotion', null, null, false),
//                'icon' => 'process-icon-refresh'
//            );
//        }
//        parent::initPageHeaderToolbar();
//    }

    /**
     * Смена статуса акции.
     * Change of promotion status.
     * @return false|ObjectModel|void
     * @throws PrestaShopException
     */
    public function processStatus() {
        if (!$id_profitcalculations = (int)Tools::getValue('id_profitcalculations')) {
            die(Tools::jsonEncode(array('success' => false, 'error' => true, 'text' => $this->l('Failed to update the status'))));
        } else {
            $id_profitcalculations = Tools::getValue('id_profitcalculations');
            $profitcalculations = new ProfitCalculationsClass($id_profitcalculations);
            if (Validate::isLoadedObject($profitcalculations)) {

                $link_redirect = $this->context->link->getAdminLink('AdminProfitCalculations');

                $profitcalculations->active = $profitcalculations->active == 1 ? 0 : 1;
                $profitcalculations->save() ? Tools::redirectAdmin($link_redirect . '&confirm='.$this->l('Transaction status changed')) : Tools::redirectAdmin($link_redirect . '&error='.$this->l('Some thing is wrong. Transaction status not changed'));
            }
        }
    }

    public function renderForm() {
        // loads current warehouse
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add share'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_promotions_rule',
                ),
                array(
                    'label' => $this->l('Title rule'),
                    'name' => 'title_rule',
                    'type' => 'select',
                    'col' => 3,
                    'required' => true
                ),
                array(
                    'label' => $this->l('Credit'),
                    'name' => 'credit',
                    'col' => 3,
                    'type' => 'text',
                    'required' => true
                ),
                array(
                    'label' => $this->l('Comment'),
                    'name' => 'comment',
                    'type' => 'textarea',
                    'col' => 3,
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('Date'),
                    'name' => 'date_transaction',
                    'hint' => $this->l('Date of the transaction'),
                    'required' => true
                ),
                array(
                    'label' => $this->l('Priority Rule'),
                    'name' => 'priority_rule',
                    'type' => 'text',
                    'col' => 1,
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
                'title' => $this->l('Save')
            )
        );

        $profitcalculations = (int)Tools::getValue('id_profitcalculations', null);
        if ($profitcalculations != null) {
            $profitcalculations = new ProfitCalculationsClass($profitcalculations);
            $this->fields_value = array(
                'type_action' => $profitcalculations->type_action,
                'debit' => $profitcalculations->debit,
                'credit' => $profitcalculations->credit,
                'profit' => $profitcalculations->profit,
                'comment' => $profitcalculations->comment,
                'active' => $profitcalculations->active,
                'date_transaction' => $profitcalculations->date_transaction,
            );
        }

        return parent::renderForm();
    }
}