<?php

class fozzy_autoupdupdateModuleFrontController extends ModuleFrontController {

  public function init()
    {
        parent::init();
    
        $filial = Tools::GetValue('filial');
        if (!$filial) $filial = 1614; 
        $date_from = date('Y-m-d\TH:i:00.000', strtotime("-60 min"));
        $data = $this->module->LoadPrices($filial, $date_from);
        if ($data == true) 
          {
            $update = $this->module->UpdatePrices($filial);
            if ($update == true) 
              {
              echo 'OK';
              die();
              }
          }
        else
          {
           echo 'Пакет пуст';
           die();
          }   
    echo 'Ahtung keine OK';
    die();
    }


}