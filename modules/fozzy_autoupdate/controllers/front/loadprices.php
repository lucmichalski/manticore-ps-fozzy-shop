<?php

class fozzy_autoupdateloadpricesModuleFrontController extends ModuleFrontController {

  public function init()
    {
        parent::init();
    
        $filial = Tools::GetValue('filial');
        if (!$filial) {
            echo 'Филиал не указан';
            die();
        }
        
        if ($filial == 12000) {
           $this->module->UpdateOnlineOnly();
           die();
        }
        
        if ( date('H') > 20 && $filial != 200) {            
            echo 'Не обновляем';
            die();          
        }  
        
        if ( date('H') < 4 && $filial != 200) {            
            $date_from = date('Y-m-d\TH:i:00.000', strtotime("-360 min"));          
        }
        else
        {
           $date_from = date('Y-m-d\TH:i:00.000', strtotime("-60 min"));
        }
        
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
    echo 'Все плохо. Что-то пошло не так';
    die();
    }


}