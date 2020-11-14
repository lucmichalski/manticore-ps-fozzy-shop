<?php

class fozzy_ordersgetModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $id_order = Tools::GetValue('id_order');
        if (!$id_order) die('Order ID not found');  
        $data = $this->module->ListenAll(0,NULL,$id_order);
        die($data);
        
    }
}