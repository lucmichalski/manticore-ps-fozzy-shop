<?php
//include(dirname(__FILE__).'/../../config/config.inc.php');
//include(dirname(__FILE__).'/../../init.php');
$sRequest = $_GET['sRequest']; if (empty($sRequest)) return;
$stMoniker = $_GET['stMoniker']; if (empty($stMoniker)) return;
$dm_token = $_GET['dm_token']; if (empty($dm_token)) return;
//$sRequest = Tools::GetValue('sRequest'); if (empty($sRequest)) return;
//$stMoniker = Tools::GetValue('stMoniker'); if (empty($stMoniker)) return;
//$dm_token = Tools::GetValue('dm_token'); if (empty($dm_token)) return;

      $curl = curl_init();
      
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.dmsolutions.com.ua:2661/api/Streets?sRequest=".urlencode($sRequest)."&stMoniker=".$stMoniker."&sLang=uk_UA",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer ".$dm_token
        ),
      ));
      
      $response = curl_exec($curl);
      $err = curl_error($curl);
 //     dump($response);
 //     dump($err);
 //     die();
      curl_close($curl);

if  ($err) return false;
else echo $response;

?>