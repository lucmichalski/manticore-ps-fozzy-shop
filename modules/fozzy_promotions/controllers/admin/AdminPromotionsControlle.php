<?php
if(defined('_PS_VERSION_') == 0)
    exit('Restricted Access!!!');

class AdminPromotionsControlle extends ModuleAdminController {
    public function __construct(){
        $this->table = 'profitcalculations';
        $this->list_no_link = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->bootstrap = true;

        $this->_defaultOrderBy = 'id_profitcalculations';
        $this->_defaultOrderWay = 'DESC';





        $this->fields_list = array(
            'id_profitcalculations' => array(
                'title' => 'ID',
                'filter' => true
            ),
            'id_order' => array(
                'title' => 'Order No.',
                'filter' => true
            ),
            'type_action' => array(
                'title' => 'Type',
                'filter' => true
            ),
            'debit' => array(
                'title' => 'Debit',
                'filter' => true
            ),
            'credit' => array(
                'title' => 'Credit',
                'filter' => true
            ),
            'profit' => array(
                'title' => 'Profit',
                'filter' => true
            ),
            'comment' => array(
                'title' => 'Comment',
                'filter' => true
            ),
            'active' => array(
                'title' => 'Enabled',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-xs'
            ),
            'date_transaction' => array(
                'title' => 'Date',
                'align' => 'text-right',
                'type' => 'date',
                'filter_key' => 'a!date_transaction'
            ),
        );

        parent::__construct();
    }
}