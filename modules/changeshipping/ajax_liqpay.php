<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'../../../init.php');
include(dirname(__FILE__).'/changeshipping.php');

$changeshipping = new Changeshipping();
global $smarty, $cookie;
ini_set('display_errors', 'on');


		$id_lang = $cookie->id_lang;
    $order_id = (int)Tools::getValue('orderid');
    $id_cart = Db::getInstance()->getValue("SELECT `id_cart` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$order_id);
    $id_shop = Db::getInstance()->getValue("SELECT `id_shop` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$order_id);
    $id_filial = Db::getInstance()->getValue("SELECT `zone` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$order_id);
    $fiskal = (float)Db::getInstance()->getValue("SELECT `fiskal` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$order_id);
    
    $liq_params = params($id_cart,'status',0);
    $liq = api("request", $liq_params, $id_shop,15,$id_filial,$fiskal);
    
    if (isset($liq->authcode_debit)) $authcode = $liq->authcode_debit;
    else  $authcode = 0;
    
    
    $status = $liq->status;              
    if ($liq->status == 'hold_wait') $status = 'Не оплачено';
    if ($liq->status == 'success') $status = 'Оплачено';
    if ($liq->status == 'reversed') $status = 'Возврат';
    
    $smarty->assign(
        array(
            'status' => $status,
            'rrn' => $liq->payment_id,
            'terminal' => $liq->public_key,
            'sender_card_mask2' => $liq->sender_card_mask2,
            'sender_card_bank' => $liq->sender_card_bank,
            'sender_card_type' => $liq->sender_card_type,
            'authcode' => $authcode,
         //   'date' => date("d/m/Y H:i:s", $liq->create_date),
         //   'liq' => $liq,
            'amount' => $liq->amount
        )
    );
    
$smarty->display(dirname(__FILE__).'/form_liqpay.tpl');


 function api($path, $params = array(),  $id_shop = 1, $timeout = 15, $id_filial = null, $fiskal = 0)
 {
    if (!isset($params['version'])) {
        throw new InvalidArgumentException('version is null');
    }
            
     $config = Configuration::getMultiple(array('liqpay_id', 'liqpay_pass'),null,1,$id_shop);
     
     $url         = 'https://www.liqpay.ua/api/' . $path;
     $public_key  = $config['liqpay_id'];
     $private_key = $config['liqpay_pass'];
     if ($fiskal > 0)
      {
       if ($id_filial == 4) {
        $public_key = 'i17001049739';
        $private_key = 'yhZZfYp7NmOYW3owGRuz2lbTPu64vvUst1Xaf8ru';
        }
       if ($id_filial == 5) {
        $public_key = 'i82027887313';
        $private_key = 'yTvSjWIB4cuiRuYnC3UrY58IdVoKFrT4qeNJBfLm';
        }
       if ($id_filial == 6) {
        $public_key = 'i25843801331';
        $private_key = 'sHTKZWb6iJuj6i0LRHXD5CWYpMzalxUarhpv0QKG';
        }
      } 
     
     $data        = encode_params(array_merge(compact('public_key'), $params));
     $signature   = str_to_sign($private_key.$data.$private_key);
     $postfields  = http_build_query(array(
        'data'  => $data,
        'signature' => $signature
     ));
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Avoid MITM vulnerability http://phpsecurity.readthedocs.io/en/latest/Input-Validation.html#validation-of-input-sources
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Check the existence of a common name and also verify that it matches the hostname provided
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);   // The number of seconds to wait while trying to connect
     curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);          // The maximum number of seconds to allow cURL functions to execute
     curl_setopt($ch, CURLOPT_POST, true);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $server_output = curl_exec($ch);
            
    // $this->_server_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     curl_close($ch);
     $resp = json_decode($server_output);
     if($resp->result == 'error') {
        //$this->context->cookie->__set('redirect_errors', $this->l('err_code: ').$resp->err_code.' '.$resp->err_description);
        }
     if($resp->result == 'ok' && $resp->action == 'hold') {
         //$this->context->cookie->__set('redirect_success', $this->l('payment in the amount of ').$resp->amount.$this->l(' UAH  was successfully confirmed'));
    		}
     if($resp->action == 'refund' && $resp->status == 'reversed') {
         //$this->context->cookie->__set('redirect_success', $this->l('payment was successfully returned'));
    		}
     return json_decode($server_output);
  }
  
 function encode_params($params)
    {
        return base64_encode(json_encode($params));
    }
    
 function str_to_sign($str)
    {
        $signature = base64_encode(sha1($str, 1));
        return $signature;
    }
    
 function params($id_order,$action,$amount = 0)
    {
		return array(
			'action'        => $action,
			'version'       => '3',
			'order_id'      => $id_order,
			'amount'        => $amount,
			//'sandbox'       => 1,
			);
	  }  
    
    