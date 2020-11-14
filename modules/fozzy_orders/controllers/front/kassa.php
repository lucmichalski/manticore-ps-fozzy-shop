<?php

class fozzy_orderskassaModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $postData = file_get_contents('php://input');
        $data = $this->module->PayConfirmFromKassa($postData);
        die($data);
        
    }
}