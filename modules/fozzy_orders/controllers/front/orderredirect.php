<?php

class fozzy_ordersorderredirectModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $id_order = Tools::GetValue('id_order');
        $email = Tools::GetValue('email');
        if (!$id_order) die('Order ID not found');
        if (!$email) die('Employee Email not found');   
        $data = $this->module->gotoorder($id_order,$email);
        die();
        
    }
}