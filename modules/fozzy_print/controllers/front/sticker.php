<?php

class fozzy_printstickerModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        
        parent::init();
        $id_order = (int)Tools::GetValue('id_order');
        $show = (int)Tools::GetValue('show');
        $filial = (int)Tools::GetValue('filial');
        $np = (int)Tools::GetValue('np');
        
        if (!$id_order) die('Order ID not found');  
        $data = $this->module->printPDF($id_order,$show,$filial,$np);
       //  dump($data);
        die();
        
    }
}