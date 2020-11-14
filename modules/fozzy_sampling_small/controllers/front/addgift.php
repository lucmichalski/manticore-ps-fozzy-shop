<?php


class fozzy_sampling_smalladdgiftModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        parent::postProcess();
        $this->module->AddGift(Tools::getValue('id_cart'));
    }
}