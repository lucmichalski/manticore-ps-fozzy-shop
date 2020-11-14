<?php

class fozzy_imageexportgetpicfullModuleFrontController extends ModuleFrontController {

  public function init()
    {
        parent::init();
        //http://fozzyshop.com.ua/getpicfull?ref=17333
        //Отдает thickbox_default изображения или ссылку на него
        $this->module->getpicfull( (int)Tools::GetValue('ref') );
    }


}