<?php

class fozzy_orderslistenModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $postData = file_get_contents('php://input');
        $data = $this->module->ListenAll(1,$postData);
        die($data);
    }
}