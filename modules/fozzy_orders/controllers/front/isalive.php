<?php

class fozzy_ordersisaliveModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init(); 
        $data = $this->module->isAlive();
        die($data);
        
    }
}