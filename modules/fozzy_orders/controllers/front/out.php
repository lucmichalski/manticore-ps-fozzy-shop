<?php

class fozzy_ordersoutModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $id_order = Tools::GetValue('id_order');
       // d($id_order);
        if (!$id_order) die('Order ID not found');  
        $data = $this->module->outOrder($id_order);
        die('OK');
        
    }
}