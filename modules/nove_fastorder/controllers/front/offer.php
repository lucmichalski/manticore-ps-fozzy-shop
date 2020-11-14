<?php

class nove_fastorderofferModuleFrontController extends ModuleFrontController {

  public function init()
    {
        parent::init();
        $qty_check = Configuration::get('NOVE_FASTORDER_repetitions');
        $this->module->_favorder($qty_check);
    }


}