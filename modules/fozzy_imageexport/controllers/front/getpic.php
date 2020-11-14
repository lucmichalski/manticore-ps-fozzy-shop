<?php

class fozzy_imageexportgetpicModuleFrontController extends ModuleFrontController {

  public function init()
    {
        parent::init();
        //http://fozzyshop.com.ua/getpic?ref=17333
        //Отдает large_default изображения или ссылку на него
        $this->module->getpic( (int)Tools::GetValue('ref') );
    }


}