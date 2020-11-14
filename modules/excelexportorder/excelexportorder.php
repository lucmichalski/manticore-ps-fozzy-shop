<?php

if (!defined('_PS_VERSION_'))
	exit;

class ExcelexportOrder extends Module
{

private $id_lang;
//private $id_shop;
private $_postErrors = array();
	
public function __construct()
	{
		$this->name = 'excelexportorder';
		$this->tab = 'quick_bulk_update';
		$this->version = '1.0';
		$this->author = 'Novevision.com, Britoff A.';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Orders list export to excel');
		$this->description = $this->l('Add export orders list to Excel 2007 (.xslx) files');
  }
    
public function install()
	{
		if (!parent::install()
    || !$this->registerHook('DisplayBackOfficeHeader')
    )
			return false;
		Configuration::updateValue('NV_EXPORTORDERS_LANG_E', Configuration::get('PS_LANG_DEFAULT'));  
    Configuration::updateValue('NV_EXPORTORDERS_ORDSTATE', 0);
    Configuration::updateValue('NV_EXPORTORDERS_FILETYPE', 0);
    Configuration::updateValue('NV_EXPORTORDERS_CARRIER', 0);
		return true;
	}

public function uninstall()
	{

		return (parent::uninstall()
			&& Configuration::deleteByName('NV_EXPORTORDERS_LANG_E')
			&& Configuration::deleteByName('NV_EXPORTORDERS_ORDSTATE')
      && Configuration::deleteByName('NV_EXPORTORDERS_FILETYPE')
      && Configuration::deleteByName('NV_EXPORTORDERS_CARRIER')
  		);
	}

private function PostXML($link, $post_get = 1,$xmlRequest = NULL) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_POST, $post_get);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8'));
    if ($xmlRequest) curl_setopt($ch, CURLOPT_POSTFIELDS,$xmlRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
    $responce = curl_exec($ch);
    curl_close($ch); 
  return $responce; 
  }
  
private function PostJSONvFZ($link, $post_get = 1,$jsonRequest = NULL) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, $post_get);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      if ($jsonRequest) curl_setopt($ch, CURLOPT_POSTFIELDS,$jsonRequest);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
      $responce = curl_exec($ch);
      curl_close($ch); 
      return $responce; 
    }  
      
private function PostJSON($link, $post_get = 1,$xmlRequest = NULL) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_POST, $post_get);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8'));
    if ($xmlRequest) curl_setopt($ch, CURLOPT_POSTFIELDS,$xmlRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
    $responce = curl_exec($ch);
    curl_close($ch); 
  return $responce; 
  }  
  
private function file_get_contents_curl( $url ) {

  $ch = curl_init();
  
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
  curl_setopt( $ch, CURLOPT_POST, true);
  curl_setopt( $ch, CURLOPT_HEADER, false );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $ch, CURLOPT_POSTFIELDS, array());
  
  $data = curl_exec( $ch );
  curl_close( $ch );

  return $data;

  }

//Доставка курьером + филиалы.
    public function getWindowsTime($courier_array) {
        $couriers = implode(",",$courier_array);
        $sql = "SELECT `id_period`, `carriers`, `timefrom`, `timeto`, `carriers_name` FROM `ps_nove_dateofdelivery` WHERE `carriers` IN (".$couriers.") AND `active` = 1 ORDER BY `timefrom`";
        $links = Db::getInstance()->executeS($sql);
        $id_shop = (int)$this->context->shop->id;
        $couriers2a = array(37,50);
        $couriers2 = implode(",",$couriers2a);
        $sql2 = "SELECT `id_period`, `carriers`, `timefrom`, `timeto`, `carriers_name` FROM `ps_nove_dateofdelivery` WHERE `carriers` IN (".$couriers2.") ORDER BY `timefrom`";
        $links2 = Db::getInstance()->executeS($sql2);
        if ($id_shop == 1) $links = array_merge($links, $links2);
        return $links;
    }

  
public function getContent()
	{
    $id_shop = (int)$this->context->shop->id;  
    $this->context->controller->addJS($this->_path.'excelexportorder.js');
		//$this->context->controller->addCSS($this->_path.'back.css');
		$this->_html = '';
    if (Tools::isSubmit('printANT'))
		{
     $files = glob(dirname(__FILE__).'/routes/*'); // get all file names
      foreach($files as $file){ // iterate files
        if(is_file($file))
          unlink($file); // delete file
      }
      
     $date_from = Tools::getValue('date_from');

     $sql_mar = "SELECT l.id_order, l.zone, l.zone_name, l.Route_Num, l.mest, l.fiskal, l.norm, l.fresh, l.ice, l.hot, l.order_type, l.Pos_Id, l.QtyW, l.Travel_Duration, l.longs, l.end_route, DATE_FORMAT(l.Time_Arrival, '%H:%i') as TimeArrival, l.distance, l.Unload_Time, vod.fio, l.payment, l.module as PayMod, DATE_FORMAT(l.dateofdelivery, '%d.%m.%Y') as deldate, UNIX_TIMESTAMP(l.dateofdelivery) as deldateunix, CONCAT(TIME_FORMAT(nvd.timefrom, '%H:%i'), ' - ',TIME_FORMAT(nvd.timeto, '%H:%i')) as Period, TIME_FORMAT(nvd.timefrom, '%H:%i') as start, adr.company, adr.lastname, adr.firstname, adr.address1, adr.city, adr.company, adr.other, adr.phone, adr.phone_mobile, adr.id_address, DATE_FORMAT(adr.date_upd, '%d.%m.%Y %H:%i') as adrdateupd, adr.zone_name as geo ";
     $sql_mar .= "FROM "._DB_PREFIX_."orders l ";
     $sql_mar .= "LEFT JOIN "._DB_PREFIX_."fozzy_logistic_vodila vod ON (vod.id_vodila = l.id_vodila) ";
   //  $sql_mar .= "LEFT JOIN "._DB_PREFIX_."orders odr ON (odr.id_order = l.id_order) ";
   //  $sql_mar .= "LEFT JOIN "._DB_PREFIX_."nove_dateofdelivery_cart nd ON (nd.cart_id = odr.id_cart) ";
     $sql_mar .= "LEFT JOIN "._DB_PREFIX_."nove_dateofdelivery nvd ON (nvd.id_period = l.period) ";          
     $sql_mar .= "LEFT JOIN "._DB_PREFIX_."address adr ON (adr.id_address = l.id_address_delivery) ";
   //  $sql_mar .= "LEFT JOIN "._DB_PREFIX_."message mess ON (mess.id_order = l.id_order) ";
     $sql_mar .= "WHERE l.dateofdelivery = '$date_from 00:00:00' ";
     $sql_mar .= "AND l.Route_Num > 0 ";
     $sql_mar .= "AND l.id_shop = ".$id_shop." ";
     $sql_mar .= " AND l.current_state <> 6 ";
     $sql_mar .= "ORDER BY l.dateofdelivery, l.Route_Num, l.Pos_Id ASC ";
     
     $data_1 = Db::getInstance()->executeS($sql_mar); 
    // dump($sql_mar);
   //  die();
     $routes = Array();
     
     foreach ($data_1 as $route) {
      $routes[$route['Route_Num']][] = $route;
     }
    // d($routes);
     
 //     require_once dirname(__FILE__) . '/classes/PHPExcel.php';
      $objPHPExcel = new PHPExcel();

      // Set document properties
      $objPHPExcel->getProperties()->setCreator($this->l('Fozzyshop'))
      							 ->setLastModifiedBy($this->l('Fozzyshop'))
      							 ->setTitle($this->l('Routes'))
      							 ->setSubject($this->l('Routes'))
      							 ->setDescription($this->l('Routes'))
      							 ->setKeywords($this->l('Routes'))
      							 ->setCategory($this->l('Routes'));
     
      $i=0;
      foreach ($routes as $route) {
     // d($route);
      $timestartar = explode(":",$route[0]['start']);
      $timestart = ($timestartar[0]*60 + $timestartar[1])/60;
      $timeendmin = substr($route[0]['end_route'],-2);
      $timeendnour = substr($route[0]['end_route'],-4,2);
      $timeend = ($timeendnour*60 + $timeendmin)/60;
      $Travel_Duration = $route[0]['Travel_Duration']/60;
      
      //Красотищща
      $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.25);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.25);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.25);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.25);
      
      $Head = new PHPExcel_Style();
      $Head->applyFromArray(
      array(
          'alignment' => array(
              'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
              'vertical'      => PHPExcel_Style_Alignment::VERTICAL_TOP,
              'rotation'      => 0,
              'wrap'          => false,
              'shrinkToFit'   => false,
              'indent'    => 0
          ),
          'font'=>array(
              'bold'      => true,
              'size'      => 14
          )
          
      ));
      
      $borderedHead = new PHPExcel_Style();
      $borderedHead->applyFromArray(
      array(
          'alignment' => array(
              'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'      => PHPExcel_Style_Alignment::VERTICAL_TOP,
              'rotation'      => 0,
              'wrap'          => true,
              'shrinkToFit'   => false,
              'indent'    => 0
          ),
          'font'=>array(
              'bold'      => true,
          ),
          'borders' => array(
              'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
              'right'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
              'top'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
              'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
          )
          
      ));
      
      $borderedNorm = new PHPExcel_Style();
      $borderedNorm->applyFromArray(
      array(
          'alignment' => array(
              'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'      => PHPExcel_Style_Alignment::VERTICAL_TOP,
              'rotation'      => 0,
              'wrap'          => true,
              'shrinkToFit'   => false,
              'indent'    => 0
          ),
          'font'=>array(
              'bold'      => false,
          ),
          'borders' => array(
              'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
              'right'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
              'top'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
              'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
          )
          
      ));

  //    d( strtotime ($route[0]['deldate'])  );
      $objPHPExcel->setActiveSheetIndex($i)
                    ->setCellValue('A1', $this->l('Водитель'))
                    ->setCellValue('A2', $this->l('Склад сборки'))
                    ->setCellValue('C1', $route[0]['fio'])
                    ->setCellValue('C2', $route[0]['zone_name'])
                    ->setCellValue('A3', $this->l('Дата маршрута'))
                    ->setCellValue('B3', $this->l('Начало маршрута'))
                    ->setCellValue('C3', $this->l('Окончание маршрута'))
                    ->setCellValue('D3', $this->l('Номер маршрута'))
                    ->setCellValue('E3', $this->l('Длительность маршрута, ч'))
                    ->setCellValue('F3', $this->l('Время в пути, ч'))
                    ->setCellValue('G3', $this->l('Вес, кг'))
                    ->setCellValue('H3', $this->l('Расстояние, км'))
                    ->setCellValue('I3', '')
                    ->setCellValue('J3', $this->l('Количество точек маршрута'))
                    ->setCellValue('K3', '')
                    ->setCellValue('L3', $this->l('Итого Сумма по чекам, грн'))
               //     ->setCellValue('M3', $this->l('Итого Возвратов, грн'))
               //     ->setCellValue('N3', $this->l('Итого получил, грн'))
                    ->setCellValue('A4', $route[0]['deldate'] )           
                    ->setCellValue('B4', $timestart/24)          
                    ->setCellValue('C4', $timeend/24)       
                    ->setCellValue('D4', $route[0]['Route_Num'])           
                    ->setCellValue('J4', count($route))
                    ->setCellValue('G4', $this->l('Вес, кг'))                  
                    ->setCellValue('H4', $route[0]['longs'])           
                    ->setCellValue('F4', $Travel_Duration/24)          
                    ->setCellValue('E4', '=C4-B4') 
                    ->setCellValue('I4', '')                            
                    ->setCellValue('K4', '')                            
                    ->setCellValue('L4', $this->l('Итого Сумма по чекам, грн'))
               //     ->setCellValue('M4', $this->l('Итого Возвратов, грн'))     
               //     ->setCellValue('N4', $this->l('Итого получил, грн'))
                    ->setCellValue('A6', $this->l('Позиция'))            
                    ->setCellValue('B6', $this->l('Наименование ТТ'))          
                    ->setCellValue('C6', $this->l('Адрес'))       
                    ->setCellValue('D6', $this->l('Примечание к адресу'))           
                    ->setCellValue('E6', $this->l('Примечание клиента'))
                    ->setCellValue('F6', $this->l('Телефон'))                  
                    ->setCellValue('G6', $this->l('Вес'))           
                    ->setCellValue('H6', $this->l('Количество мест'))          
                    ->setCellValue('I6', $this->l('Логистика')) 
                    ->setCellValue('J6', $this->l('Номер заказа'))                            
                    ->setCellValue('K6', $this->l('Тип оплаты'))                            
                    ->setCellValue('L6', $this->l('Сумма по чеку, грн'));
              //      ->setCellValue('M6', $this->l('Возврат, грн'))     
             //       ->setCellValue('N6', $this->l('Получил, грн'))      
      $objPHPExcel->getActiveSheet()->mergeCells("A1:B1");
      $objPHPExcel->getActiveSheet()->mergeCells("A2:B2");   
      $objPHPExcel->getActiveSheet()->mergeCells("C1:L1");
      $objPHPExcel->getActiveSheet()->mergeCells("C2:L2");
      $objPHPExcel->getActiveSheet()->getStyle('A4')->getNumberFormat()->setFormatCode('dd.mm.yyyy');
      $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode('hh:mm');
      $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode('hh:mm');
      $objPHPExcel->getActiveSheet()->getStyle('F4')->getNumberFormat()->setFormatCode('hh:mm');
      $objPHPExcel->getActiveSheet()->getStyle('E4')->getNumberFormat()->setFormatCode('hh:mm');
      $j=7;
      foreach ($route as $point)
        {
        //  d($point);
          $logistika = '';
          if ($point['norm']) $logistika .= 'C/';
          if ($point['ice']) $logistika .= 'З/';
          if ($point['fresh']) $logistika .= 'О/';
          if ($point['hot']) $logistika .= 'Г/';
          $logistika = substr ($logistika,0,-1);
          if ($point['order_type'] != 'Заказ') $logistika = $point['order_type'];
          
          $sql_ordermess = "SELECT message FROM "._DB_PREFIX_."message  WHERE id_order = ".$point['id_order'];
          $messages = Db::getInstance()->executeS($sql_ordermess);
          
          $mess = '';
     /*     if ($messages) {
          foreach ($messages as $message)
           {
           $mess .= $message['message'].',';
          }
          }
     */     
          $sql_m2 = 'SELECT *
          			FROM '._DB_PREFIX_.'customer_thread ct
          			LEFT JOIN '._DB_PREFIX_.'customer_message cm
          				ON ct.id_customer_thread = cm.id_customer_thread
          			WHERE ct.`id_order` = '.(int)$point['id_order']." AND cm.`id_employee` > 0 AND cm.`private` = 1 ";
              
           $messages2=Db::getInstance()->executeS($sql_m2);
          
          
          if ($messages2) {
          foreach ($messages2 as $message2)
           {
           $mess .= $message2['message'].',';
          }
          }
          
          if ($mess) $mess = substr ($mess,0,-1);
          
          
          if ( $point['payment'] == 'Оплата наличными при получении' )  $point['payment'] = 'НАЛ';
          if ( $point['payment'] == 'Оплата по безналичному расчету' )  $point['payment'] = 'БЕЗНАЛ';
          if ( $point['payment'] == 'Liqpay' )  
              {
                $point['payment'] = 'Кредитка';
                $point['fiskal'] = 0;
              } 
                
          $objPHPExcel->setActiveSheetIndex($i)
                    ->setCellValue("A$j", $point['Pos_Id'])            
                    ->setCellValue("B$j", $point['lastname']." ".$point['firstname']." ".$point['company'])          
                    ->setCellValue("C$j", $point['city']." ".$point['address1'])       
                    ->setCellValue("D$j", $point['other'])           
                    ->setCellValue("E$j", $mess)
                    ->setCellValue("F$j", ' '.$point['phone_mobile'].' '.$point['phone'])                  
                    ->setCellValue("G$j", $point['QtyW'])           
                    ->setCellValue("H$j", $point['mest'])          
                    ->setCellValue("I$j", $logistika) 
                    ->setCellValue("J$j", $point['id_order'])                            
                    ->setCellValue("K$j", $point['payment'])                            
                    ->setCellValue("L$j", $point['fiskal']);
               //     ->setCellValue("M$j", '')     
               //     ->setCellValue("N$j", '');
        $j++;
        }
        $lastrow = $j-1; 
      $objPHPExcel->setActiveSheetIndex($i)       
                    ->setCellValue('G4', "=SUM(G7:G$lastrow)")                                          
                    ->setCellValue('L4', "=SUM(L7:L$lastrow)");
                  //  ->setCellValue('M4', "=SUM(M7:M$lastrow)")     
                  //  ->setCellValue('N4', "=SUM(N7:N$lastrow)")
      
      
      
      $objPHPExcel->getActiveSheet()->setTitle($this->l('Route')."_".$route[0]['Route_Num']);
      $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
      
      $objPHPExcel->getActiveSheet()->setSharedStyle($Head, "A1:N1"); 
      $objPHPExcel->getActiveSheet()->setSharedStyle($Head, "A2:N2"); 
      $objPHPExcel->getActiveSheet()->setSharedStyle($borderedHead, "A3:L3");
      $objPHPExcel->getActiveSheet()->setSharedStyle($borderedHead, "J3:L3");
      $objPHPExcel->getActiveSheet()->setSharedStyle($borderedNorm, "A4:H4");
      $objPHPExcel->getActiveSheet()->setSharedStyle($borderedNorm, "J4:L4");
      $objPHPExcel->getActiveSheet()->setSharedStyle($borderedHead, "A6:L6"); 
      
      $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
      $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
      $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
      $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
      $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
      $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
      $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7);
      $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
      $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
      $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11);
      $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(9);
      $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
     // $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
    //  $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(16);

      $objPHPExcel->getActiveSheet()->getStyle('A4')->getNumberFormat()->setFormatCode('dd.mm.yyyy');
      $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode('hh:mm');
      $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode('hh:mm');
      $objPHPExcel->getActiveSheet()->getStyle('F4')->getNumberFormat()->setFormatCode('hh:mm');
      $objPHPExcel->getActiveSheet()->getStyle('E4')->getNumberFormat()->setFormatCode('hh:mm');
      $objPHPExcel->getActiveSheet()->setSharedStyle($borderedNorm, "A7:L$lastrow");
      
      $objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea("A1:L$lastrow");
      $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth();
      $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight();
      
      //Красотищща
      $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.25);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.25);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.25);
      $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.25);
      
      $i++;
      $objWorkSheet = $objPHPExcel->createSheet();
     }
     
     $objPHPExcel->setActiveSheetIndex($i)
                    ->setCellValue('A1', "Дата")
                    ->setCellValue('B1', "Номер маршрута")
                    ->setCellValue('C1', "Окно")
                    ->setCellValue('D1', "Comp_ID")
                    ->setCellValue('E1', "Наименование ТТ")
                    ->setCellValue('F1', "Адрес")
                    ->setCellValue('G1', "Вес, кг")
                    ->setCellValue('H1', "Номер заказа")
                    ->setCellValue('I1', "Телефон")
                    ->setCellValue('J1', "Водитель")
                    ->setCellValue('K1', "Тип оплаты")
                    ->setCellValue('L1', "Сумма по чеку, грн")
                    ->setCellValue('M1', "Возврат, грн")
                    ->setCellValue('N1', "Получено, грн")
                    ->setCellValue('O1', "Статус оплаты")
                    ->setCellValue('P1', "Комментарий")
                    ->setCellValue('Q1', "Склад сборки")
                    ->setCellValue('R1', "Гео");
     
      $j=2;
      foreach ($routes as $route) {
     // d($route);
     
      $timestartar = explode(":",$route[0]['start']);
      $timestart = ($timestartar[0]*60 + $timestartar[1])/60;
      $timeendmin = substr($route[0]['end_route'],-2);
      $timeendnour = substr($route[0]['end_route'],-4,2);
      $timeend = ($timeendnour*60 + $timeendmin)/60;
      $Travel_Duration = $route[0]['Travel_Duration']/60;
      
      foreach ($route as $point)
        {
        if ( $point['PayMod'] == 'ps_cashondelivery' )  $point['payment'] = 'НАЛ';
        if ( $point['PayMod'] == 'ps_wirepayment' )  {
              $point['payment'] = 'БЕЗНАЛ';
              $point['fiskal'] = 0;
              }
        if ( $point['PayMod'] == 'ecm_liqpay' )  
              {
                $point['payment'] = 'Кредитка';
                $point['fiskal'] = 0;
              }
        
        $compid = $point['id_address']+10000;
        $data_upd = $point['adrdateupd']; 
        $data_ch = '27.11.2018 00:00';
        
        $d_ch = strtotime($data_ch);
        $d_du = strtotime($row['adrdateupd']);
        
        if ($d_du > $d_ch) 
        {
         $compid = $row['id_address'] + $d_du;
        }
        
        
        $objPHPExcel->setActiveSheetIndex($i)
                    ->setCellValue("A$j", "=DATEVALUE(Z$j)")
                    ->setCellValue("B$j", $route[0]['Route_Num'])
                    ->setCellValue("C$j", $timestart/24)
                    ->setCellValue("D$j", $compid)
                    ->setCellValue("E$j", $point['lastname']." ".$point['firstname']." ".$point['company'])
                    ->setCellValue("F$j", $point['city']." ".$point['address1'])
                    ->setCellValue("G$j", $point['QtyW'])
                    ->setCellValue("H$j", $point['id_order'])
                    ->setCellValue("I$j", ' '.$point['phone_mobile'])
                    ->setCellValue("J$j", $route[0]['fio'])
                    ->setCellValue("K$j", $point['payment'])
                    ->setCellValue("L$j", $point['fiskal'])
                    ->setCellValue("M$j", "")
                    ->setCellValue("N$j", "")
                    ->setCellValue("O$j", "")
                    ->setCellValue("P$j", "")
                    ->setCellValue("Q$j", $point['zone_name'])
                    ->setCellValue("R$j", $point['geo'])
                    ->setCellValue("Z$j", $route[0]['deldate']);
        $objPHPExcel->getActiveSheet()->getStyle("C$j")->getNumberFormat()->setFormatCode('hh:mm');
        $objPHPExcel->getActiveSheet()->getStyle("A$j")->getNumberFormat()->setFormatCode('dd.mm.yyyy');
        $j++;
        }
        
     }
     
     // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle($this->l('All'));
      
      
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      
      
      // Save Excel 2007 file
      
     $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
     $time_t = time();
     $file_date = date('d_m_y_H_i_s', $time_t);
     $filename = _PS_MODULE_DIR_.'excelexportorder/routes/route_'.$file_date.'.xlsx';
   //  $filename = str_replace('.php', '_'.$file_date.'.xlsx', __FILE__ );
   //  $filename = str_replace('modules/excelexportorder/excelexportorder', 'modules/excelexportorder/routes/route', $filename );
     
     $objWriter->save($filename);
     
     $export_file_link = $this->_listFilesR();
          if ($export_file_link)
          $exp_mes = $this->l('Маршрутные листы сгенерированы')." ".$export_file_link;
          else $exp_mes = '';

  		$this->_html .= $this->displayConfirmation($this->l('Листы сгенерированы'));
     
       
  //   d($data_1);
    }
    
    if (Tools::isSubmit('btnANTSendOrders'))                                                      
		{                                      
        $ant_server=file_get_contents("http://ant-logistics.com/config?req=api_http");
        $ant_server='http://main.ant-logistics.com/AntLogistics/AntService.svc/';
        $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.k@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 2) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.o@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 3) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.d@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 4) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kh@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 8) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.rv@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 9) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kr@gmail.com&pass=123qaZ456&ByUser=0";
        
        if (!$ant_server) {
        dump('Сервер API не отвечает');
        die;
        }
        
        
        $ant_autoriz = $this->file_get_contents_curl($ant_to_avt);  
        $ant_session_xml =simplexml_load_string($ant_autoriz);

        $Session_Ident = (string)$ant_session_xml->Session_Ident;
        
        if (!$Session_Ident) {
        dump('Аутентефикация не пройдена!');
        die;
        }
    /*    if ($this->context->cookie->id_employee == 1) 
        {
        dump($ant_to_avt);
        dump($Session_Ident);
        die();
        }   */
        $date_q = explode('-', Tools::getValue('date_from'));
        $date = $date_q[2].".".$date_q[1].".".$date_q[0];

            set_time_limit(500);	
        	  $lang = Tools::getValue('lang_e');
            $order_state = implode(",",Tools::getValue('ord_state'));
            $carrier = implode(",",Tools::getValue('carrier'));
            $windows_time = implode(",",Tools::getValue('windows_time'));
            $file_type = Tools::getValue('file_types');
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            $add_id = Tools::getValue('add_id'); 
            $id_shop = (int)$this->context->shop->id;
            
            Configuration::updateValue('NV_EXPORTORDERS_LANG_E', $lang);
            Configuration::updateValue('NV_EXPORTORDERS_ORDSTATE', $order_state);
            Configuration::updateValue('NV_EXPORTORDERS_FILETYPE', $file_type);
            Configuration::updateValue('NV_EXPORTORDERS_CARRIER', $carrier);
            Configuration::updateValue('NV_EXPORTORDERS_WINDOWS_TIME', $windows_time);

            $select = ' SELECT a.`id_currency`, a.`id_order`, a.`reference`, c.`company`, c.`firstname`, c.`lastname`,	osl.`name` AS `osname`,	a.`payment`, car.`name` AS carrier, a.`id_carrier` AS cid, a.`total_paid`, a.`shipping_number`, a.`delivery_date`, a.`date_add`, a.`id_cart`, ';
            $select.= ' a.ice, a.norm, a.fresh, a.mest, a.fiskal, sbor.fio as sborshik, vod.fio as vodila, ';
            $select.= ' a.zone, a.zone_name, ';
            $select.= 'address.`id_address`, address.`city`, address.`address1`, address.`address2`,address.`other`, address.`phone`, address.`phone_mobile`, address.`lat` as lat, address.`lng` as lng, ';
            $select.=' address.`street`, address.`house`, address.`apartment`, address.`level`, address.`door`, address.`intercom`, address.`elevator`, address.`concierge`, ';
            $select.= "DATE_FORMAT(address.`date_upd`, '%d.%m.%Y %H:%i') as adrdateupd, ";
            $select.= 'a.`dateofdelivery` as dated, ';
       //     $select.= '(SELECT `dateofdelivery` FROM `'._DB_PREFIX_.'nove_dateofdelivery_cart` dtd WHERE dtd.`cart_id` = a.`id_cart` LIMIT 1) as dated, ';
      //      $select.= '(SELECT `weight` FROM `'._DB_PREFIX_.'order_carrier` orcar WHERE orcar.`id_order` = a.`id_order` LIMIT 1) as orderweight ';
            $select.= '(SELECT ROUND(SUM(`product_quantity`*`product_weight`),2) FROM `'._DB_PREFIX_.'order_detail` orcar WHERE orcar.`id_order` = a.`id_order` LIMIT 1) as orderweight ';
            $select.= ' FROM `'._DB_PREFIX_.'orders` a ';
            
            $select.= 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
        		INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
        		INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
        		INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.$lang.')
        		LEFT JOIN `'._DB_PREFIX_.'carrier` car ON (car.`id_carrier` = a.`id_carrier`) ';
         // $select.= ' LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic` log ON (log.`id_order` = a.`id_order`)';
            $select.= ' LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic_vodila` vod ON (vod.`id_vodila` = a.`id_vodila`)
            LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic_sborshik` sbor ON (sbor.`id_sborshik` = a.`id_sborshik`)
        		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
        		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.$lang.')';
         //   $select.= 'INNER JOIN `'._DB_PREFIX_.'nove_dateofdelivery_cart` dtd ON (dtd.`cart_id` = a.`id_cart`) ';
            $select.= " WHERE a.`dateofdelivery` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' ";
        //    $select.= " WHERE (SELECT `dateofdelivery` FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` dtd WHERE dtd.`cart_id` = a.`id_cart` LIMIT 1) BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' ";
            $select.= " AND a.`id_shop` = $id_shop ";
            if ($order_state !=0) $select.= "AND a.`current_state` IN ($order_state) ";
            if ($carrier !=0) $select.= "AND a.`id_carrier` IN ($carrier) ";
            if ($windows_time !=0) $select.= "AND a.`period` IN ($windows_time)";
            
            $select.= ' ORDER BY a.`date_add`';
         //   dump($select);
        //    die();
            $result = Db::getInstance()->executeS($select);

            $comps_array = Array();
            foreach ($result as $key=>$row) {
              $Comp_name = $row['firstname']." ".$row['lastname'];
              $shopdomain = $this->context->shop->domain;
                  
              $id_cart = (int)$row['id_order'];
              
              $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period FROM `"._DB_PREFIX_."orders` WHERE `id_order`=".$id_cart;
              $cart = Db::getInstance()->executeS($sql_select);
                       
              if (count($cart) > 0)
                {
                $cart_date = $cart[0]['cart_date'];
                $period = $cart[0]['period'];
                $sqll = "SELECT b.`id_period`, DATE_FORMAT (b.`timefrom`, '%H:%i') as timefrom, DATE_FORMAT (b.`timeto`, '%H:%i') as timeto, b.`timeoff`, b.`active` FROM `"._DB_PREFIX_."nove_dateofdelivery` b";
              		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
              			$sqll .= ' JOIN `'._DB_PREFIX_.'nove_dateofdelivery_shop` bs ON b.`id_period` = bs.`id_period` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
              		  $sqll .= ' WHERE  b.`id_period` = '. (int)$period;
                    $sqll .= ' ORDER BY  b.`timefrom` ASC';
                  
                $links = Db::getInstance()->executeS($sqll);
                $time_from = $links[0]['timefrom'];
                $time_to = $links[0]['timeto'];
                }
              else 
                {
                $time_from ='';
                $time_to ='';
                }
              /*
              $compid = $row['id_address']+10000;
              $data_upd = $row['adrdateupd']; 
              $data_ch = '27.11.2018 00:00';
              
              $d_ch = strtotime($data_ch);
              $d_du = strtotime($row['adrdateupd']);
              
              if ($d_du > $d_ch) 
              {
               $compid = $row['id_address'] + $d_du;
              } 
              */
             $compid = $row['id_address'];
           //   $del_ad_ar = array();
          //    $del_ad_ar = explode('|',$row['address2']);
          //    $delivery_adrress_short = $del_ad_ar[0].", ".$del_ad_ar[1];  //Britoff - адрес короткий
              $delivery_adrress_short = $row['street'].", ".$row['house'];  //Britoff - адрес короткий
              $delivery_adrress_full = "кв.".$row['apartment'].", эт.".$row['level'].", под.".$row['door'].", дф.".$row['intercom'].", лифт.".$row['elevator'].", кон.".$row['concierge'];  //Britoff - адрес дополненный

              $cid = (int)$row['cid'];
              if ($cid == 37)
                {
                 $compid = 3;
                 $delivery_adrress_short = 'вул. Пирогівський шлях, 135';
                 $np_lat = '50.353444';
                 $np_lng = '30.542863';
                 $delivery_adrress_full = $delivery_adrress_short;
                }
              if ($cid == 50)
                {
                 $compid = 4;
                 $delivery_adrress_short = 'вул. Заболотного, 37';
                 $j_lat = '50.343185';
                 $j_lng = '30.544842';
                 $delivery_adrress_full = $delivery_adrress_short;
                }

              $warehouse = 1;
            
              switch ((int)$row['zone']) {
                  case 4:
                      $warehouse = 45;
                      break;
                  case 5:
                      $warehouse = 1;
                      break;
                  case 6:
                      $warehouse = 107;
                      break;
                  case 200:
                      $warehouse = 1;
                      break;
                  case 300:
                      $warehouse = 1;
                      break;
                  case 400:
                  case 500:
                  case 600:
                      $warehouse = 1;
                      break;
                  default:
                      $warehouse = 1;
                      break;
              }
            
            
              $comps_array[$key]['Comp_Id'] = $compid;
              if ($cid == 37) 
              {
              $comps_array[$key]['lat'] = $np_lat;
              $comps_array[$key]['lng'] = $np_lng;
              $comps_array[$key]['Comp_Name'] = 'Нова Пошта'; 
              }
              else if ($cid == 50)
              {
              $comps_array[$key]['lat'] = $j_lat;
              $comps_array[$key]['lng'] = $j_lng;
              $comps_array[$key]['Comp_Name'] = 'JUSTIN'; 
              }
              else
              {
              $comps_array[$key]['lat'] = (string)$row['lat'];
              $comps_array[$key]['lng'] = (string)$row['lng'];
              $comps_array[$key]['Comp_Name'] = $Comp_name;  
              }
              $comps_array[$key]['Address'] = $row['city'].",".$delivery_adrress_short;   
              $comps_array[$key]['TimeWork_Beg'] = $time_from;
              $comps_array[$key]['TimeWork_End'] = $time_to;
              $comps_array[$key]['QtyW'] = $row['orderweight'];        
              $comps_array[$key]['Note'] = $row['other'];   
              $comps_array[$key]['Phone'] = $row['phone_mobile'];    
              $comps_array[$key]['Request_Num'] = $row['id_order']; 
              $comps_array[$key]['Warehouse_Id'] = $warehouse;
              $comps_array[$key]['UserField_1'] = $row['fiskal']; 
              $comps_array[$key]['UserField_2'] = $row['payment']; 
              $comps_array[$key]['UserField_3'] = $delivery_adrress_full;
              $comps_array[$key]['UserField_4'] = $row['id_address'];
              $comps_array[$key]['UserField_5'] = $row['osname'];
            } 
         
        $ant_post_points_string = $ant_server."DEX_Import_Request_JSON";

        /*  Britoff - test point
        if ($this->context->cookie->id_employee == 1) 
        {
          $comps_array = array();
          $comps_array[0]['Comp_Id'] = '1572698943';
              $comps_array[0]['lat'] = '50.353444';
              $comps_array[0]['lng'] = '30.542863';     
              $comps_array[0]['Comp_Name'] = 'Новая почта отделение №1';  
              $comps_array[0]['Address'] = "Киев, вул. Пирогівський шлях, 23";    
              $comps_array[0]['Phone'] = '380674404292';    
              $comps_array[0]['Request_Num'] = '1455676'; 
              $date = "07.01.2020";
 
        }
        */
        $data_to_send="Session_Ident=".$Session_Ident."&Date_Data=".$date."&remove=0&Update_GeoCoord=1&Comps=".json_encode($comps_array);
    //            dump($data_to_send);
    //    die();
        $ant_send_points = $this->PostJSON($ant_post_points_string,1,$data_to_send);
        $ant_send_points = json_decode($ant_send_points, TRUE);

        if ($ant_send_points['ErrorResponse']['error'] == 0) $this->_html .= $this->displayConfirmation($this->l('Точки отправлены'));
        else 
          {
          dump($data_to_send);
          dump($ant_send_points);
          die();
          }
           
    }
    
    if (Tools::isSubmit('btnANTGetOrders'))
		{
        $ant_server=file_get_contents("http://ant-logistics.com/config?req=api_http");
        $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.k@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 2) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.o@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 3) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.d@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 4) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kh@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 8) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.rv@gmail.com&pass=123qaZ456&ByUser=0";
        if ($id_shop == 9) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kr@gmail.com&pass=123qaZ456&ByUser=0";
        
        $ant_autoriz = $this->file_get_contents_curl($ant_to_avt);  
        $ant_session_xml =simplexml_load_string($ant_autoriz);
        $Session_Ident = (string)$ant_session_xml->Session_Ident;
        $date_from = explode("-",Tools::getValue('date_from'));
        
        $date = $date_from[2].".".$date_from[1].".".$date_from[0];

        $ant_get_points_string=$ant_server."DEX_Export_Request?&Session_Ident=".$Session_Ident."&Date_Data=".$date."&GeoAreaInfo=1&format=XML&ByUser=0";
        $ant_get_points = simplexml_load_string($this->PostXML($ant_get_points_string,0));
        $ant_get_points = json_decode(json_encode($ant_get_points), TRUE);
        
        foreach ($ant_get_points['Comps']['DEX_Request_Comps'] as $key=>$point)
          {
              $geoarea = explode(",",$point['GeoArea_List']);
              $geo_count = count($geoarea);
              $zone = '';
              $zone_name = '';
              if ($geo_count > 1) 
                  {
                  $geoarea = array_unique($geoarea);
                  sort($geoarea);
                  }
              $geo_count = count($geoarea);
              if ($geo_count == 0)
                {
                $zone_name = 'Поза зоною';
                $zone = '0';
                }
               if ($geo_count == 1 && (int)$geoarea[0] == 5)
                {
                $zone_name = 'Заболотного';
                $zone = '5';
                }
               if ($geo_count == 1 && (int)$geoarea[0] == 6)
                {
                $zone_name = 'Проліски';
                $zone = '6';
                }
               if ($geo_count == 1 && (int)$geoarea[0] == 4)
                {
                $zone_name = 'Петрівка';
                $zone = '4';
                }
               if ($geo_count == 2 && (int)$geoarea[0] == 4 && (int)$geoarea[1] == 5)
                {
                $zone_name = 'Загальна';
                $zone = '4,5';
                }
               if ($geo_count == 2 && (int)$geoarea[0] == 5 && (int)$geoarea[1] == 6)
                {
                $zone_name = 'Загальна';
                $zone = '5,6';
                } 
                if ($geo_count == 1 && (int)$geoarea[0] == 200)
                {
                $zone_name = 'Одеса';
                $zone = '200';
                } 
                if ($geo_count == 1 && (int)$geoarea[0] == 300)
                {
                $zone_name = 'Дніпро';
                $zone = '300';
                } 
                if ($geo_count == 1 && (int)$geoarea[0] == 400)
                {
                $zone_name = 'Харків';
                $zone = '400';
                }
                if ($geo_count == 1 && (int)$geoarea[0] == 500)
                {
                $zone_name = 'Рівне';
                $zone = '500';
                } 
                if ($geo_count == 1 && (int)$geoarea[0] == 600)
                {
                $zone_name = 'Кременчуг';
                $zone = '600';
                }  
             $sql_upd_addr = "UPDATE `"._DB_PREFIX_."address` SET `zone` = '".$zone."', `zone_name` = '".$zone_name."', `lat` = ".$point['lat'].", `lng` = ".$point['lng']." WHERE `id_address` = ".$point['UserField_4'];
             Db::getInstance()->executeS($sql_upd_addr);
          }
        
        $this->_html .= $this->displayConfirmation($this->l('Alles GUT!'));
        /*
        require_once dirname(__FILE__) . '/classes/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
        
              // Set document properties
              $objPHPExcel->getProperties()->setCreator($this->l('Prestashop'))
              							 ->setLastModifiedBy($this->l('Prestashop'))
              							 ->setTitle($this->l('Points'))
              							 ->setSubject($this->l('Points'))
              							 ->setDescription($this->l('Points'))
              							 ->setKeywords($this->l('Points'))
              							 ->setCategory($this->l('Points'));
        
              $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue('A1', 'Comp_Id')
                          ->setCellValue('B1', 'Название')
                          ->setCellValue('C1', 'Адрес')
                          ->setCellValue('D1', 'С')
                          ->setCellValue('E1', 'По')
                          ->setCellValue('F1', 'Вес')
                          ->setCellValue('G1', 'Примечние')
                          ->setCellValue('H1', 'Заказ')
                          ->setCellValue('I1', 'Сумма чека')
                          ->setCellValue('J1', 'Оплата')
                          ->setCellValue('K1', 'Телефон')
                          ->setCellValue('L1', 'ID Адреса')
                          ->setCellValue('M1', 'Номер зоны');   
        
        foreach ($ant_get_points['Comps']['DEX_Request_Comps'] as $key=>$point)
        {
       //       d($point);
              $pi = $key+2;
              $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue("A$pi", $point['Comp_Id'])
                          ->setCellValue("B$pi", $point['Comp_Name'])
                       //   ->setCellValue("C$pi", $point['Address'])
                       //   ->setCellValue("D$pi", $point['TimeWork_Beg'])
                       //   ->setCellValue("E$pi", $point['TimeWork_End'])
                       //   ->setCellValue("F$pi", $point['QtyW'])
                       //   ->setCellValue("G$pi", $point['Note'])
                       //   ->setCellValue("H$pi", $point['Request_Num'])
                       //   ->setCellValue("I$pi", $point['UserField_1'])
                      //    ->setCellValue("J$pi", $point['UserField_2'])
                       //   ->setCellValue("K$pi", $point['UserField_3'])
                       //   ->setCellValue("L$pi", $point['Comp_Code'])
                          ->setCellValue("M$pi", $point['GeoArea_List'])
                          ; 
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($this->l('Points'));
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        // Save Excel 2007 file
        
       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
       $time_t = time();
       $file_date = date('d_m_y_H_i_s', $time_t);
       $filename = 'points'.$file_date.'.xlsx';
       
       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
       header('Content-type: application/vnd.ms-excel');
       header('Content-Disposition: attachment; filename="'.$filename.'"');
       $objWriter->save('php://output');
       exit;
       */
    }
    
    
    if (Tools::isSubmit('btnANT'))
		{
      $ant_server=file_get_contents("http://ant-logistics.com/config?req=api_http");
      $ant_server='http://main.ant-logistics.com/AntLogistics/AntService.svc/';
      $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.k@gmail.com&pass=123qaZ456&ByUser=0";
      if ($id_shop == 2) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.o@gmail.com&pass=123qaZ456&ByUser=0";
      if ($id_shop == 3) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.d@gmail.com&pass=123qaZ456&ByUser=0";
      if ($id_shop == 4) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kh@gmail.com&pass=123qaZ456&ByUser=0";
      if ($id_shop == 8) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.rv@gmail.com&pass=123qaZ456&ByUser=0";
      if ($id_shop == 9) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kr@gmail.com&pass=123qaZ456&ByUser=0";
      
      if (!$ant_server) {
        dump('Сервер API не отвечает');
        die;
        }

      $ant_autoriz = $this->file_get_contents_curl($ant_to_avt);
      $ant_session_xml =simplexml_load_string($ant_autoriz);
      $Session_Ident = (string)$ant_session_xml->Session_Ident;
      
      if (!$Session_Ident) {
        dump('Аутентефикация не пройдена!');
        die;
        }
      
      $date_from = Tools::getValue('date_from');
      $date_q = explode('-', $date_from);
      $date = $date_q[2].".".$date_q[1].".".$date_q[0];
      
      // Разметка адресов - старт
        $ant_get_points_string=$ant_server."DEX_Export_Request?&Session_Ident=".$Session_Ident."&Date_Data=".$date."&GeoAreaInfo=1&format=XML&ByUser=0";
        $ant_get_points = simplexml_load_string($this->PostXML($ant_get_points_string,0));
        $ant_get_points = json_decode(json_encode($ant_get_points), TRUE);

        foreach ($ant_get_points['Comps']['DEX_Request_Comps'] as $key=>$point)
          {
              $geoarea = explode(",",$point['GeoArea_List']);
              $geo_count = count($geoarea);
              $zone = '';
              $zone_name = '';
              if ($geo_count > 1) 
                  {
                  $geoarea = array_unique($geoarea);
                  sort($geoarea);
                  }
              $geo_count = count($geoarea);
              if ($geo_count == 0)
                {
                $zone_name = 'Поза зоною';
                $zone = '0';
                }
               if ($geo_count == 1 && (int)$geoarea[0] == 5)
                {
                $zone_name = 'Заболотного';
                $zone = '5';
                }
               if ($geo_count == 1 && (int)$geoarea[0] == 6)
                {
                $zone_name = 'Проліски';
                $zone = '6';
                }
               if ($geo_count == 1 && (int)$geoarea[0] == 4)
                {
                $zone_name = 'Петрівка';
                $zone = '4';
                }
               if ($geo_count == 2 && (int)$geoarea[0] == 4 && (int)$geoarea[1] == 5)
                {
                $zone_name = 'Загальна';
                $zone = '4,5';
                }
               if ($geo_count == 2 && (int)$geoarea[0] == 5 && (int)$geoarea[1] == 6)
                {
                $zone_name = 'Загальна 2';
                $zone = '5,6';
                }
                if ($geo_count == 1 && (int)$geoarea[0] == 200)
                {
                $zone_name = 'Одеса';
                $zone = '200';
                } 
                if ($geo_count == 1 && (int)$geoarea[0] == 300)
                {
                $zone_name = 'Дніпро';
                $zone = '300';
                } 
                if ($geo_count == 1 && (int)$geoarea[0] == 400)
                {
                $zone_name = 'Харків';
                $zone = '400';
                }
                if ($geo_count == 1 && (int)$geoarea[0] == 500)
                {
                $zone_name = 'Рівне';
                $zone = '500';
                }
                if ($geo_count == 1 && (int)$geoarea[0] == 600)
                {
                $zone_name = 'Кременчуг';
                $zone = '600';
                } 
             $sql_upd_addr = "UPDATE `"._DB_PREFIX_."address` SET `zone` = '".$zone."', `zone_name` = '".$zone_name."', `lat` = ".$point['lat'].", `lng` = ".$point['lng']." WHERE `id_address` = ".$point['UserField_4'];
             Db::getInstance()->executeS($sql_upd_addr);
          }
      // Разметка адресов - стоп
      
      
      $ant_get_route_string=$ant_server."DEX_Export_Response_JSON?&Session_Ident=".$Session_Ident."&Date_Data=".$date."&CompInfo=1&format=XML&ByUser=0";
      
      // if ($this->context->cookie->id_employee == 1) $ant_get_route_string=$ant_server."DEX_Export_Response_JSON?&Session_Ident=".$Session_Ident."&Date_Data=01.01.2020&CompInfo=1&format=XML&ByUser=0";
      
      $ant_get_routes = simplexml_load_string($this->PostXML($ant_get_route_string,0));
      $ant_get_routes = json_decode(json_encode($ant_get_routes), TRUE);
      
      //Проверка на наличие одного маршрута.
        if(!isset($ant_get_routes['Comps']['DEX_Comps'][0])) {
            $new_arr = $ant_get_routes['Comps']['DEX_Comps'];
            $ant_get_routes['Comps']['DEX_Comps'] = array(0 => $new_arr);
        } elseif (!isset($ant_get_routes['Route']['DEX_Routes'][0])) {
            $new_arr_1 = $ant_get_routes['Route']['DEX_Routes'];
            $ant_get_routes['Route']['DEX_Routes'] = array(0 => $new_arr_1);
        }
        
      $to_base = Array();
      $i=0;
      
      foreach ($ant_get_routes['Comps']['DEX_Comps'] as $point)
        {
          if ($point['Request_Num']) 
            {
            
            $to_base[$i]['Request_Num'] =  $point['Request_Num'];
            $to_base[$i]['Route_Num'] =  $point['Route_Num'];
            
            foreach ($ant_get_routes['Route']['DEX_Routes'] as $rou) 
              {
               if ($rou['Route_Num'] == $point['Route_Num'])
                {
                 $to_base[$i]['end_route'] = (int)$rou['RouteTime_E'];
                 $to_base[$i]['longs'] = $rou['distance']/1000;
                 $to_base[$i]['Travel_Duration'] = (int)$rou['Travel_Duration'];
                }
              }
            
            $to_base[$i]['Pos_Id'] =  $point['Pos_Id'];
            $to_base[$i]['QtyW'] =  $point['QtyW'];
            $to_base[$i]['Time_Arrival'] =  $point['Time_Arrival'];
            $to_base[$i]['distance'] =  $point['distance'];
            $to_base[$i]['Unload_Time'] =  $point['Unload_Time'];
            
            $to_base[$i]['Driver_Id'] =  0; 
            foreach ($ant_get_routes['Route']['DEX_Routes'] as $route)
              {
              if ($route['Route_Num'] == $to_base[$i]['Route_Num']) $to_base[$i]['Driver_Id'] =  $route['Driver_Id'];
              }
            $i++;
            }
        }

      $sql_clear = "UPDATE `"._DB_PREFIX_."orders` SET `id_vodila` = 0, `Pos_Id` = null, `QtyW` = null, `Time_Arrival` = null, `distance` = null, `Unload_Time` = null, `Route_Num` = null, `end_route` = null, `longs` = null, `Travel_Duration` = null WHERE DATE(dateofdelivery) = '".$date_from."' AND `id_shop` = ".$id_shop;
      Db::getInstance()->execute($sql_clear);    //Очистка базы
      
      foreach ($to_base as $z)       
        {
       // d($z);
        $sql_U = "UPDATE `"._DB_PREFIX_."orders` fl, `"._DB_PREFIX_."fozzy_logistic_vodila` fv  SET fl.`id_vodila` = fv.`id_vodila`, fl.`Pos_Id`=".$z['Pos_Id'].", fl.`QtyW`='".$z['QtyW']."', fl.`Time_Arrival`='".$z['Time_Arrival']."', fl.`distance`=".$z['distance'].", fl.`Unload_Time`=".$z['Unload_Time'].", fl.`Route_Num`=".$z['Route_Num'].", fl.`end_route`='".$z['end_route']."', fl.`longs`=".$z['longs'].", fl.`Travel_Duration`=".$z['Travel_Duration']." WHERE fl.`id_order` = ".$z['Request_Num']." AND fv.`Driver_Id` = ".$z['Driver_Id']." AND fv.`id_shop` = ".$id_shop." ";
    //   dump($sql_U);
    //   die();
        Db::getInstance()->execute($sql_U);
          $id_order = $z['Request_Num'];                                                   
          $sql_logistika = "SELECT c.`mest`, s.`INN` as sborshik, v.`INN` as vodila, v.`fio` as vodilaname, c.`Route_Num`, c.`cartnum`, `norm`, `ice`, `fresh`, `hot` FROM `"._DB_PREFIX_."orders` c LEFT JOIN `"._DB_PREFIX_."fozzy_logistic_sborshik` s ON c.`id_sborshik`=s.`id_sborshik` LEFT JOIN `"._DB_PREFIX_."fozzy_logistic_vodila` v ON c.`id_vodila`=v.`id_vodila` WHERE `id_order` = ".$id_order;
          $logistika = Db::getInstance()->executeS($sql_logistika);
          $link_vodila = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/EditOrderData';
         // $comps_array_address = array('orderId'=>$id_order,'deliveryAddress'=>'ул. Пишка 245/4, кв. №34-а');
          $comps_array_vodila = array('orderId'=>$id_order,'driverName'=>$logistika[0]['vodilaname']);
          $comps_array_vodila_json = json_encode($comps_array_vodila);
          $restt = $this->PostJSONvFZ($link_vodila,1,$comps_array_vodila_json);
        } 
      $this->_html .= $this->displayConfirmation($this->l('Водители синхронизирваны'));     
     } 

    
		if (Tools::isSubmit('btnExport'))
		{
      $files = glob(dirname(__FILE__).'/export/*'); // get all file names
      foreach($files as $file){ // iterate files
        if(is_file($file))
          unlink($file); // delete file
      }

      $lang_e = Tools::getValue('lang_e');
      $ord_state = implode(",",Tools::getValue('ord_state'));
      $file_types = Tools::getValue('file_types'); 
      $carrier = implode(",",Tools::getValue('carrier'));
      $date_from = Tools::getValue('date_from');
      $date_to = Tools::getValue('date_to');
      
      Configuration::updateValue('NV_EXPORTORDERS_LANG_E', $lang_e);
      Configuration::updateValue('NV_EXPORTORDERS_ORDSTATE', $ord_state);
      Configuration::updateValue('NV_EXPORTORDERS_FILETYPE', $file_types);
      Configuration::updateValue('NV_EXPORTORDERS_CARRIER', $carrier);

      $this->_export($date_from, $date_to);

          $export_file_link = $this->_listFiles();
          if ($export_file_link)
          $exp_mes = $this->l('File is formed and ready to download.')." ".$export_file_link;
          else $exp_mes = '';

  		$this->_html .= $this->displayConfirmation($this->l('Export OK'));
    }
    
		$this->_html .= $this->renderForm($exp_mes);

		return $this->_html;
	}

public function renderForm($exp_mes = null)
	{
    $languages = $this->context->language->getLanguages();
    
    $orderstates = OrderState::getOrderStates($this->context->language->id);
    array_unshift($orderstates, array('id_order_state'=>0,'name'=>$this->l('All')));        //Добавляем элемент все
    
    $file_types = array(
                     //   array('id'=>0,'name'=>$this->l('All fields')),
                        array('id'=>1,'name'=>$this->l('Ant logistic')),
                        array('id'=>2,'name'=>$this->l('Выгрузка')),
                        array('id'=>3,'name'=>$this->l('Post.UA'))
                        );
    
    $courier_array = array(27,33,30,42,55,57,37,55);                    
    $carriers = Carrier::getCarriers ($this->context->language->id, true, false, false, null, 0);
    $carriers_to = array();
    foreach ($carriers as $key=>$carrier)
      {
        if (!in_array((int)$carrier['id_carrier'], $courier_array))
            {
            unset($carriers[$key]);
            }
            else
            {
            $carriers_to[]=(int)$carrier['id_carrier']; 
            }
      }
    //array_unshift($carriers, array('id_carrier'=>0,'name'=>$this->l('All')));        //Добавляем элемент все
    
    
    $windows_time = $this->getWindowsTime($carriers_to);

    //Доставка курьером + филиалы.
    $windows = array();
    foreach ($windows_time as $value) {
        $period = array('id_period' => $value['id_period'], 'id_carriers' => $value['carriers'], 'time' => date('H:i', strtotime($value['timefrom'])) ." - ". date('H:i', strtotime($value['timeto'])) ." - ". $value['carriers_name']);
        $windows[] = $period;
    }
    
    $fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Export orders list to Excel'),
					'icon' => 'icon-cogs'
				),
        'description' => $exp_mes,
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Choose language:'),
						'name' => 'lang_e',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $languages,
							'id' => 'id_lang',
							'name' => 'name'
						),
					),
          array(
						'type' => 'select',
						'label' => $this->l('Choose file type:'),
						'name' => 'file_types',
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $file_types,
							'id' => 'id',
							'name' => 'name'
						),
					),
          array(
						'type' => 'select',
						'label' => $this->l('Choose order state:'),
						'name' => 'ord_state[]',
            'multiple' => true,
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $orderstates,
							'id' => 'id_order_state',
							'name' => 'name'
						),
					),
          array(
						'type' => 'select',
						'label' => $this->l('Choose carrier:'),
						'name' => 'carrier[]',
            'multiple' => true,
						'class' => 'fixed-width-xxl',
						'options' => array(
							'query' => $carriers,
							'id' => 'id_carrier',
							'name' => 'name'
						),
					),
          array(
              'type' => 'select',
              'label' => $this->l('Окно:'),
              'name' => 'windows_time[]',
              'multiple' => true,
              'class' => 'fixed-width-xxl',
              'options' => array(
                  'query' => $windows,
                  'id' => 'id_period',
                  'name' => 'time',
              ),
          ),
          array(
						'type' => 'date',
						'label' => $this->l('Date from:'),
						'name' => 'date_from',
					),
          array(
						'type' => 'date',
						'label' => $this->l('Date to:'),
						'name' => 'date_to',
					),
					array(
  					'type' => 'switch',
  					'label' => $this->l('Выгружать ID адреса (только Муравьиная логистика):'),
  					'name' => 'add_id',
  					'is_bool' => true,
  					'values' => array(
  						array(
  							'id' => 'add_id_on',
  							'value' => 1,
  							'label' => $this->l('Да')),
  						array(
  							'id' => 'add_id_off',
  							'value' => 0,
  							'label' => $this->l('Нет')),
  					),
            'validation' => 'isBool',
				  ),
          ),
		/*		'buttons'	=> array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Отправить заявку'),
                    'icon' => 'process-icon-upload',
                    'name' => 'btnANTSendOrders',
                    'id'   => 'btnANTSendOrders',
                    'class'=> 'pull-left'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Получить зоны точек'),
                    'icon' => 'process-icon-download',
                    'name' => 'btnANTGetOrders',
                    'id'   => 'btnANTGetOrders',
                    'class'=> 'pull-right'
                ),  
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Получить маршрут'),
                    'icon' => 'process-icon-download',
                    'name' => 'btnANT',
                    'id'   => 'btnANT',
                    'class'=> 'pull-left'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Печатать маршруты'),
                    'icon' => 'process-icon-stats',
                    'name' => 'printANT',
                    'id'   => 'printANT',
                    'class'=> 'pull-left'
                )
         ),*/
		/*		'submit' => array(
					'title' => $this->l('Export'),
					'id' => 'btnExport',
					'name' => 'btnExport'
				  )   */
		  );    
      if ($this->context->employee->id_profile == 11)
        {
        $fields_form[0]['form']['buttons'] = array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Печатать маршруты'),
                    'icon' => 'process-icon-stats',
                    'name' => 'printANT',
                    'id'   => 'printANT',
                    'class'=> 'pull-left'
                ));
        }
      else
        {
         $fields_form[0]['form']['buttons'] = array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Отправить заявку'),
                    'icon' => 'process-icon-upload',
                    'name' => 'btnANTSendOrders',
                    'id'   => 'btnANTSendOrders',
                    'class'=> 'pull-left'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Получить маршрут'),
                    'icon' => 'process-icon-download',
                    'name' => 'btnANT',
                    'id'   => 'btnANT',
                    'class'=> 'pull-left'
                ),
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Печатать маршруты'),
                    'icon' => 'process-icon-stats',
                    'name' => 'printANT',
                    'id'   => 'printANT',
                    'class'=> 'pull-left'
                ));
        }  
      $helper = new HelperForm();
      
      // Module, token and currentIndex
      $helper->module = $this;
      $helper->name_controller = $this->name;
      $helper->token = Tools::getAdminTokenLite('AdminModules');
      $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
      
      // Language
      $languages = Language::getLanguages(false);
      $helper->languages = $languages;
      $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		  $helper->allow_employee_form_lang = true;
       
      // Title and toolbar
      $helper->title = $this->displayName;
      $helper->show_toolbar = true;        // false -> remove toolbar
      $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
      $helper->submit_action = 'submit'.$this->name;
      
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm($fields_form);
	}

public function getConfigFieldsValues()
	{
		$config_fields = array(
			'lang_e' => Configuration::get('NV_EXPORTORDERS_LANG_E'),
      'date_from' => date('Y-m-d'),
      'date_to' => date('Y-m-d'),
      'ord_state[]' => explode(",",Configuration::get('NV_EXPORTORDERS_ORDSTATE')),
      'carrier[]' => explode(",",Configuration::get('NV_EXPORTORDERS_CARRIER')),
      'file_types' => Configuration::get('NV_EXPORTORDERS_FILETYPE')      
		);
		return $config_fields;
	}

private function _export($date_from = null, $date_to = null)
	{
    set_time_limit(500);	
	  $lang = Configuration::get('NV_EXPORTORDERS_LANG_E');
    $order_state = Configuration::get('NV_EXPORTORDERS_ORDSTATE');
    $carrier = Configuration::get('NV_EXPORTORDERS_CARRIER');
    $file_type = Configuration::get('NV_EXPORTORDERS_FILETYPE');
    $from = Tools::getValue('date_from');
    $to = Tools::getValue('date_to');
    $add_id = Tools::getValue('add_id'); 
    $id_shop = (int)$this->context->shop->id;

switch ($file_type) 
    {
    case 1: 
    case 2:
    case 3:    
    $select = ' SELECT a.`id_currency`, a.`id_order`, a.`reference`, c.`company`, c.`firstname`, c.`lastname`,	osl.`name` AS `osname`,	a.`payment`, car.`name` AS carrier, a.`total_paid`, a.`shipping_number`, a.`delivery_date`, a.`date_add`, a.`id_cart`, a.`zone_name` as zone_sb, ';
    $select.= ' a.ice, a.norm, a.fresh, a.mest, a.fiskal, sbor.fio as sborshik, vod.fio as vodila, ';
    $select.= 'address.`id_address`, address.`city`, address.`address1`, address.`address2`,address.`other`, address.`phone`, address.`phone_mobile`, address.`zone_name` as geo, ';
    $select.= "DATE_FORMAT(address.`date_upd`, '%d.%m.%Y %H:%i') as adrdateupd, ";
    $select.= 'a.`dateofdelivery` as dated, ';
    $select.= '(SELECT `weight` FROM `'._DB_PREFIX_.'order_carrier` orcar WHERE orcar.`id_order` = a.`id_order` LIMIT 1) as orderweight, osl.name as orderstate ';
    $select.= ' FROM `'._DB_PREFIX_.'orders` a ';
    
    $select.= 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
		INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
		INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
		INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.$lang.')
		LEFT JOIN `'._DB_PREFIX_.'carrier` car ON (car.`id_carrier` = a.`id_carrier`) ';
//    $select.= ' LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic` log ON (log.`id_order` = a.`id_order`) ';
    $select.= ' LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic_vodila` vod ON (vod.`id_vodila` = a.`id_vodila`)
    LEFT JOIN `'._DB_PREFIX_.'fozzy_logistic_sborshik` sbor ON (sbor.`id_sborshik` = a.`id_sborshik`)
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.$lang.')';
   // $select.= 'INNER JOIN `'._DB_PREFIX_.'nove_dateofdelivery_cart` dtd ON (dtd.`cart_id` = a.`id_cart`) ';
    $select.= " WHERE a.`dateofdelivery` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' ";
    $select.= " AND a.`id_shop` = $id_shop ";
    if ($order_state !=0) $select.= "AND a.`current_state` IN ($order_state) ";
    if ($carrier !=0) $select.= "AND a.`id_carrier` IN ($carrier) ";
    $select.= ' ORDER BY a.`date_add`';
    
    break;
    
    default:
    $select = ' SELECT a.`id_currency`, a.`id_order`, a.`reference`, c.`company`, c.`firstname`, c.`lastname`,	osl.`name` AS `osname`,	a.`payment`, car.`name` AS carrier, a.`total_paid`, a.`shipping_number`, a.`delivery_date`, a.`date_add`, a.`id_cart`, ';
    $select.= 'address.`id_address`, address.`city`, address.`address1`, address.`address2`,address.`other`, address.`phone`, address.`phone_mobile` ';
    $select.= ' FROM `'._DB_PREFIX_.'orders` a ';
    
    $select.= 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
		INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
		INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
		INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.$lang.')
		LEFT JOIN `'._DB_PREFIX_.'carrier` car ON (car.`id_carrier` = a.`id_carrier`)
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.$lang.')';
    $select.= " WHERE a.`date_add` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' ";
    $select.= " AND a.`id_shop` = $id_shop ";
    if ($order_state !=0) $select.= "AND a.`current_state` IN ($order_state) ";
    if ($carrier !=0) $select.= "AND a.`id_carrier` IN ($carrier) ";
    $select.= ' ORDER BY a.`date_add`';
    break;
    }
    $result = Db::getInstance()->executeS($select);
//   d($select); 
  //  require_once dirname(__FILE__) . '/classes/PHPExcel.php';
    $objPHPExcel = new PHPExcel();

      // Set document properties
      $objPHPExcel->getProperties()->setCreator($this->l('Fozzy'))
      							 ->setLastModifiedBy($this->l('Fozzy'))
      							 ->setTitle($this->l('Orders'))
      							 ->setSubject($this->l('Orders'))
      							 ->setDescription($this->l('Orders'))
      							 ->setKeywords($this->l('Orders'))
      							 ->setCategory($this->l('Orders'));
switch ($file_type) 
    {
      case 1:
      if (!$add_id)
      {
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', 'Comp_Id')
                  ->setCellValue('B1', 'Comp_Name')
                  ->setCellValue('C1', 'Address')
                  ->setCellValue('D1', 'TimeWork_Beg')
                  ->setCellValue('E1', 'TimeWork_End')
                  ->setCellValue('F1', 'QtyW')
                  ->setCellValue('G1', 'Note')
                  ->setCellValue('H1', 'Request_Num')
                  ->setCellValue('I1', 'UserField_1')
                  ->setCellValue('J1', 'UserField_2')
                  ->setCellValue('K1', 'UserField_3');   
      }
      else
      {
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', 'Comp_Id')
                  ->setCellValue('B1', 'Comp_Name')
                  ->setCellValue('C1', 'Address')
                  ->setCellValue('D1', 'TimeWork_Beg')
                  ->setCellValue('E1', 'TimeWork_End')
                  ->setCellValue('F1', 'QtyW')
                  ->setCellValue('G1', 'Note')
                  ->setCellValue('H1', 'Request_Num')
                  ->setCellValue('I1', 'UserField_1')
                  ->setCellValue('J1', 'UserField_2')
                  ->setCellValue('K1', 'UserField_3')
                  ->setCellValue('L1', 'Comp_Code');
      }
      foreach ($result as $key=>$row) {
      $i1 = $key + 2;
      // Add some data
      $Comp_name = $row['firstname']." ".$row['lastname'];
      
      $shopdomain = $this->context->shop->domain;
      
      $id_cart = (int)$row['id_cart'];
      
      $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
      $cart = Db::getInstance()->executeS($sql_select);
               
      if (count($cart) > 0)
        {
        $cart_date = $cart[0]['cart_date'];
        $period = $cart[0]['period'];
        $sqll = "SELECT b.`id_period`, DATE_FORMAT (b.`timefrom`, '%H:%i') as timefrom, DATE_FORMAT (b.`timeto`, '%H:%i') as timeto, b.`timeoff`, b.`active` FROM `"._DB_PREFIX_."nove_dateofdelivery` b";
      		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
      			$sqll .= ' JOIN `'._DB_PREFIX_.'nove_dateofdelivery_shop` bs ON b.`id_period` = bs.`id_period` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
      		  $sqll .= ' WHERE  b.`id_period` = '. (int)$period;
            $sqll .= ' ORDER BY  b.`timefrom` ASC';
          
        $links = Db::getInstance()->executeS($sqll);
        $time_from = $links[0]['timefrom'];
        $time_to = $links[0]['timeto'];
        }
      else 
        {
        $time_from ='';
        $time_to ='';
        }
    /*  $compid = $row['id_address']+10000;
      $data_upd = $row['adrdateupd']; 
      $data_ch = '27.11.2018 00:00';
      
      $d_ch = strtotime($data_ch);
      $d_du = strtotime($row['adrdateupd']);
      
      if ($d_du > $d_ch) 
      {
       $compid = $row['id_address'] + $d_du;
      } 
     */
     $compid = $row['id_address'];
     
      $del_ad_ar = array();
      $del_ad_ar = explode('|',$row['address2']);
      $delivery_adrress_short = $del_ad_ar[0].", ".$del_ad_ar[1];  //Britoff - адрес короткий
      if (!$add_id)
        {          
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A$i1", $compid)
                    ->setCellValue("B$i1", $Comp_name)
                    ->setCellValue("C$i1", $row['city'].",".$delivery_adrress_short)
                    ->setCellValue("D$i1", $time_from)
                    ->setCellValue("E$i1", $time_to)
                    ->setCellValue("F$i1", $row['orderweight'])
                    ->setCellValue("G$i1", $row['other'])
                    ->setCellValue("H$i1", $row['id_order'])
                    ->setCellValue("I$i1", $row['fiskal'])
                    ->setCellValue("J$i1", $row['payment'])
                    ->setCellValue("K$i1", $row['phone_mobile']);      
       }
      else
       {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A$i1", $compid)
                    ->setCellValue("B$i1", $Comp_name)
                    ->setCellValue("C$i1", $row['city'].",".$delivery_adrress_short)
                    ->setCellValue("D$i1", $time_from)
                    ->setCellValue("E$i1", $time_to)
                    ->setCellValue("F$i1", $row['orderweight'])
                    ->setCellValue("G$i1", $row['other'])
                    ->setCellValue("H$i1", $row['id_order'])
                    ->setCellValue("I$i1", $row['fiskal'])
                    ->setCellValue("J$i1", $row['payment'])
                    ->setCellValue("K$i1", $row['phone_mobile'])
                    ->setCellValue("L$i1", $row['id_address']);
       
       }
     }
      break;
      case 2:
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', 'Заказ')
                  ->setCellValue('B1', 'Статус')
                  ->setCellValue('C1', 'Адрес')
                  ->setCellValue('D1', 'Оплата')
                  ->setCellValue('E1', 'Доставка')
                  ->setCellValue('F1', 'Дата доставки')
                  ->setCellValue('G1', 'С')
                  ->setCellValue('H1', 'До')
                  ->setCellValue('I1', 'Сумма по заказу')
                  ->setCellValue('J1', 'Мест')
                  ->setCellValue('K1', 'Сумма по чеку')
                  ->setCellValue('L1', 'Сборщик')
                  ->setCellValue('M1', 'Водитель')
                  ->setCellValue('N1', 'Зона сборки')
                  ->setCellValue('O1', 'Гео');
      
      foreach ($result as $key=>$row) {
      $i1 = $key + 2;
      // Add some data
      $Comp_name = $row['firstname']." ".$row['lastname'];
      
      $shopdomain = $this->context->shop->domain;
      
      $id_cart = (int)$row['id_cart'];
      
      $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
      $cart = Db::getInstance()->executeS($sql_select);
               
      if (count($cart) > 0)
        {
        $cart_date = $cart[0]['cart_date'];
        $period = $cart[0]['period'];
        $sqll = "SELECT b.`id_period`, DATE_FORMAT (b.`timefrom`, '%H:%i') as timefrom, DATE_FORMAT (b.`timeto`, '%H:%i') as timeto, b.`timeoff`, b.`active` FROM `"._DB_PREFIX_."nove_dateofdelivery` b";
      		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
      			$sqll .= ' JOIN `'._DB_PREFIX_.'nove_dateofdelivery_shop` bs ON b.`id_period` = bs.`id_period` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
      		  $sqll .= ' WHERE  b.`id_period` = '. (int)$period;
            $sqll .= ' ORDER BY  b.`timefrom` ASC';
          
        $links = Db::getInstance()->executeS($sqll);
        $time_from = $links[0]['timefrom'];
        $time_to = $links[0]['timeto'];
        }
      else 
        {
        $time_from ='';
        $time_to ='';
        }
 //     d(strtotime($row['dated']));
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue("A$i1", $row['id_order'])
                  ->setCellValue("B$i1", $row['orderstate'])
                  ->setCellValue("C$i1", $row['city'].",".$row['address1'])
                  ->setCellValue("D$i1", $row['payment'])
                  ->setCellValue("E$i1", $row['carrier'])
                //  ->setCellValue("F$i1", date("d.m.Y", strtotime($row['dated'])))
                  ->setCellValue("F$i1", PHPExcel_Shared_Date::PHPToExcel( date(strtotime($row['dated'])), true ) )
                  ->setCellValue("G$i1", $time_from)
                  ->setCellValue("H$i1", $time_to)
                  ->setCellValue("I$i1", $row['total_paid'])
                  ->setCellValue("J$i1", $row['mest'])
                  ->setCellValue("K$i1", $row['fiskal'])
                  ->setCellValue("L$i1", $row['sborshik'])
                  ->setCellValue("M$i1", $row['vodila'])
                  ->setCellValue("N$i1", $row['zone_sb'])
                  ->setCellValue("O$i1", $row['geo']);
      $objPHPExcel->getActiveSheet()->getStyle("F$i1")->getNumberFormat()->setFormatCode("dd.mm.yyyy");
      }
      break;
      case 3:
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', 'Номер Заказа')
                  ->setCellValue('B1', 'ФИО Получателя')
                  ->setCellValue('C1', 'Заявленая сумма')
                  ->setCellValue('D1', 'Адрес Доставки')
                  ->setCellValue('E1', 'Вес')
                  ->setCellValue('F1', 'Телефон Получателя')
                  ->setCellValue('G1', 'Дата доставки')
                  ->setCellValue('H1', 'С')
                  ->setCellValue('I1', 'По')
                  ->setCellValue('J1', 'Сумма к оплате')
                  ->setCellValue('K1', 'Заявленная сумма (страховка)')
                  ->setCellValue('L1', 'Комментарий к заказу')
                  ->setCellValue('M1', 'Кол-во мест')
                  ->setCellValue('N1', 'Тип оплаты');
      
      foreach ($result as $key=>$row) {
      $i1 = $key + 2;
      // Add some data
      $Comp_name = $row['firstname']." ".$row['lastname'];
      
      $shopdomain = $this->context->shop->domain;
      
      $id_cart = (int)$row['id_cart'];
      
      $sql_select = "SELECT DATE_FORMAT (`dateofdelivery`, '%d.%m.%Y') AS cart_date, period FROM `"._DB_PREFIX_."nove_dateofdelivery_cart` WHERE `cart_id`=".$id_cart;
      $cart = Db::getInstance()->executeS($sql_select);
               
      if (count($cart) > 0)
        {
        $cart_date = $cart[0]['cart_date'];
        $period = $cart[0]['period'];
        $sqll = "SELECT b.`id_period`, DATE_FORMAT (b.`timefrom`, '%H:%i') as timefrom, DATE_FORMAT (b.`timeto`, '%H:%i') as timeto, b.`timeoff`, b.`active` FROM `"._DB_PREFIX_."nove_dateofdelivery` b";
      		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
      			$sqll .= ' JOIN `'._DB_PREFIX_.'nove_dateofdelivery_shop` bs ON b.`id_period` = bs.`id_period` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
      		  $sqll .= ' WHERE  b.`id_period` = '. (int)$period;
            $sqll .= ' ORDER BY  b.`timefrom` ASC';
          
        $links = Db::getInstance()->executeS($sqll);
        $time_from = $links[0]['timefrom'];
        $time_to = $links[0]['timeto'];
        }
      else 
        {
        $time_from ='';
        $time_to ='';
        }
    //  d($row);
      if ($row['payment'] == 'Оплата наличными при получении')
      {
       $koplate = $row['fiskal'];
      }
      else
      {
       $koplate = 0;
      }
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue("A$i1", $row['id_order'])
                  ->setCellValue("B$i1", $Comp_name)
                  ->setCellValue("C$i1", $row['fiskal'])
                  ->setCellValue("D$i1", $row['city'].",".$row['address1'])
                  ->setCellValue("E$i1", $row['orderweight'])
                  ->setCellValue("F$i1", $row['phone_mobile'])
                  ->setCellValue("G$i1", str_replace("00:00:00","",str_replace("-",".",$row['dated'])))
                  ->setCellValue("H$i1", $time_from)
                  ->setCellValue("I$i1", $time_to)
                  ->setCellValue("J$i1", $row['fiskal'])
                  ->setCellValue("K$i1", $row['fiskal'])
                  ->setCellValue("L$i1", $row['other'])
                  ->setCellValue("M$i1", $row['mest'])
                  ->setCellValue("N$i1", $row['payment']);
      }
      break;
      default:
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', $this->l('ID'))
                  ->setCellValue('B1', $this->l('Reference'))
                  ->setCellValue('C1', $this->l('Date'))
                  ->setCellValue('D1', $this->l('Company'))
                  ->setCellValue('E1', $this->l('Firstname'))
                  ->setCellValue('F1', $this->l('Lastname'))
                  ->setCellValue('G1', $this->l('Total'))
                  ->setCellValue('H1', $this->l('Currency'))
                  ->setCellValue('I1', $this->l('Payment'))
                  ->setCellValue('J1', $this->l('Carrier'))
                  ->setCellValue('K1', $this->l('Shipping Number'))
                  ->setCellValue('L1', $this->l('Status'))
                  ->setCellValue('M1', $this->l('Delivery Date'))
                  ->setCellValue('N1', $this->l('City'))
                  ->setCellValue('O1', $this->l('Address'))
                  ->setCellValue('P1', $this->l('Address add.'))
                  ->setCellValue('Q1', $this->l('Phone'))
                  ->setCellValue('R1', $this->l('Phone Mobile'))
                  ->setCellValue('S1', $this->l('Other'));
                  
      foreach ($result as $key=>$row) {
      $i1 = $key + 2;
      
      // Add some data
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue("A$i1", $row['id_order'])
                  ->setCellValue("B$i1", $row['reference'])
                  ->setCellValue("C$i1", $row['date_add'])
                  ->setCellValue("D$i1", $row['company'])
                  ->setCellValue("E$i1", $row['firstname'])
                  ->setCellValue("F$i1", $row['lastname'])
                  ->setCellValue("G$i1", $row['total_paid'])
                  ->setCellValue("H$i1", $row['id_currency'])
                  ->setCellValue("I$i1", $row['payment'])
                  ->setCellValue("J$i1", $row['carrier'])
                  ->setCellValue("K$i1", $row['shipping_number'])
                  ->setCellValue("L$i1", $row['osname'])
                  ->setCellValue("M$i1", $row['delivery_date'])
                  ->setCellValue("N$i1", $row['city'])
                  ->setCellValue("O$i1", $row['address1'])
                  ->setCellValue("P$i1", $row['address2'])
                  ->setCellValue("Q$i1", $row['phone'])
                  ->setCellValue("R$i1", $row['phone_mobile'])
                  ->setCellValue("S$i1", $row['other']);
      }
    break;
  }
      
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle($this->l('Orders'));
      
      
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      
      
      // Save Excel 2007 file
      
     $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
     $time_t = time();
     $file_date = date('d_m_y_H_i_s', $time_t);
     $filename = str_replace('.php', '_'.$file_date.'.xlsx', __FILE__ );
     $filename = str_replace('modules/excelexportorder', 'modules/excelexportorder/export', $filename );
     
     $objWriter->save($filename);
      
  }	

private function _listFiles() {

   $link = '';
   $files = glob(dirname(__FILE__).'/export/*'); // get all file names

      foreach($files as $file){ // iterate files
        if(is_file($file))
      $file = str_replace('/home/admin/web/fozzyshop.com.ua/public_html/modules/excelexportorder/export/', '', $file ); 
      $link = '<a href="/modules/'.$this->name.'/export/'.$file.'">'.$this->l('Download').'</a>';
      }

   
   return $link;   
   }

private function _listFilesR() {

   $link = '';
   $files = glob(dirname(__FILE__).'/routes/*'); // get all file names

      foreach($files as $file){ // iterate files
        if(is_file($file))
      $file = str_replace(_PS_MODULE_DIR_.'excelexportorder/routes/', '', $file ); 
      $link = '<a href="/modules/'.$this->name.'/routes/'.$file.'">'.$this->l('Download').'</a>';
      }

   
   return $link;   
   }
  
}
