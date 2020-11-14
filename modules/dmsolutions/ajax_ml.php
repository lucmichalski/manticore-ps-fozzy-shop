<?php
//include(dirname(__FILE__).'/../../config/config.inc.php');
//include(dirname(__FILE__).'/../../init.php');
$id_shop = $_GET['dm_id_shop']; if (empty($id_shop)) return;
$lat = $_GET['lat']; if (empty($lat)) return;
$lng = $_GET['lng']; if (empty($lng)) return;

//$id_shop = Tools::GetValue('dm_id_shop'); if (empty($id_shop)) return;
//$lat = Tools::GetValue('lat'); if (empty($lat)) return;
//$lng = Tools::GetValue('lng'); if (empty($lng)) return;

    $ant_server=file_get_contents("http://ant-logistics.com/config?req=api_http");
    $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.k@gmail.com&pass=123qaZ456&ByUser=0";
    if ($id_shop == 2) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.o@gmail.com&pass=123qaZ456&ByUser=0";
    if ($id_shop == 3) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistika.d@gmail.com&pass=123qaZ456&ByUser=0";
    if ($id_shop == 4) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kh@gmail.com&pass=123qaZ456&ByUser=0";
    if ($id_shop == 8) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.rv@gmail.com&pass=123qaZ456&ByUser=0";
    if ($id_shop == 9) $ant_to_avt=$ant_server."DEX_UserAuthorization?format=xml&type=login&email=fozzy.logistka.kr@gmail.com&pass=123qaZ456&ByUser=0";
        
    $ch = curl_init();
  
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
    curl_setopt( $ch, CURLOPT_POST, true);
    curl_setopt( $ch, CURLOPT_HEADER, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_URL, $ant_to_avt );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 90);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, array());
    
    $ant_autoriz = curl_exec( $ch );
    curl_close( $ch );
      
    $ant_session_xml =simplexml_load_string($ant_autoriz);
    $Session_Ident = (string)$ant_session_xml->Session_Ident;
    
    $comps_array = array();
    $comps_array[0]['Comp_Id'] = 100000000;
    $comps_array[0]['lat'] = $lat;
    $comps_array[0]['lng'] = $lng;
    $comps_array[0]['Comp_Name'] = 'TEST';  
    $comps_array[0]['Address'] = "Киев, ул. Заболотного 37";   
    $comps_array[0]['UserField_1'] = 31323; 
    
    $ant_post_points_string = $ant_server."DEX_Import_Request_JSON";                                             
    $data_to_send="Session_Ident=".$Session_Ident."&Date_Data=01.01.2020&remove=0&Update_GeoCoord=1&Ext_Ident=19&Comps=".json_encode($comps_array);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ant_post_points_string);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_to_send);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
    $responce = curl_exec($ch);
    curl_close($ch); 
      
    $ant_get_points_string = $ant_server."DEX_Export_Request";                                             
    $data_to_get="Session_Ident=".$Session_Ident."&Date_Data=01.01.2020&Ext_Ident=19&ByUser=0&GeoAreaInfo=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ant_get_points_string."?".$data_to_get);
    curl_setopt($ch, CURLOPT_POST, 0);
    //curl_setopt($ch, CURLOPT_POSTFIELDS,$data_to_get);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
    $responce_n = curl_exec($ch);
    curl_close($ch);
    
    
    $ant_del_points_string = $ant_server.'DEX_Delete_Request';
    $data_to_del = 'Session_Ident='.$Session_Ident.'&Ext_Ident=19&Date_Data=01.01.2020&ByUser=0';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ant_del_points_string);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_to_del);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
    $responce = curl_exec($ch);
    curl_close($ch);


echo $responce_n;

?>