<?php


class fozzy_easteraddeasterModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        parent::postProcess();
        $this->module->AddGift(Tools::getValue('id_cart'));
    }
}