<?php

class fozzy_imageexportgetimagebyrefModuleFrontController extends ModuleFrontController {

  public function init()
    {
        parent::init();
        //http://fozzyshop.com.ua/getimagebyref?reference=17333
        //Отдает оригинал изображения или ссылку на него
        $this->module->getimagebyref( (int)Tools::GetValue('reference') );
    }


}