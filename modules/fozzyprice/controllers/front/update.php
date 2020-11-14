<?php

class fozzypriceupdateModuleFrontController extends ModuleFrontController {

  public function init()
    {
        parent::init();
    /*
    Филиалы
    Заболотного  1614
    Одесса   322
    Днепр  1674
    */
/*   $ddd[0] = array(
    'artikul'=>'4234234', 
    'kolvo'=>4, 
    'name'=>'Памперс'
    );
    $ddd[1] = array(
    'artikul'=>'4234255', 
    'kolvo'=>5, 
    'name'=>'Молоко'
    );
    $ee= json_encode ($ddd);
    p($ee);
   d($ddd);   */ 
        $filial = Tools::GetValue('filial');
        if (!$filial) $filial = 1614; 
        $date_from = date('Y-m-d\TH:i:00.000', strtotime("-60 min"));
        $data = $this->module->LoadPrices($filial, $date_from);
        if ($filial == 100) die();
        if ($filial == 200) die();
        if ($data == true) 
          {
            $update = $this->module->UpdatePrices($filial);
            if ($update == true) 
              {
              //dump('OK');
              echo 'OK';
              die();
              }
          }
        else
          {
           echo 'не OK';
        //   dump($date_from);
           die();
          }   
    echo 'Ahtung keine OK';
    die();
    }


}