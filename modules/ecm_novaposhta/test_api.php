<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

if (!defined('_NP_TIMEOUT_'))  define ("_NP_TIMEOUT_", 3);
$post = '';
		$url = 'https://api.novaposhta.ua/v2.0/json/';
		$ch = curl_init('https://api.novaposhta.ua/v2.0/json/');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, _NP_TIMEOUT_ );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($post))
		);
		//p($ch);
		$result = curl_exec($ch);
		/* $content = 'content';
		$result = file_get_contents('https://api.novaposhta.ua/v2.0/json/', null, stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => 'Content-Type: application/json'.'\r\n'.'Content-Length: '.strlen($post).'\r\n',
			$content => $post,
			),
		))); 
		
		if (!$result) {
			$res["success"] = 0;
			$res["errors"] = array(
				array(
					"fgc" => 'Error: file_get_contents not work coorectly'
					)
				);
			$result = json_encode($res);
		} 
		
		return $result;*/
		
		
		//p(curl_errno($ch));
		if (curl_errno($ch) > 0 and curl_errno($ch) < 100) {
			//p("Error:".curl_errno($ch)."-".curl_error($ch));
			$res["success"] = 0;
			$res["errors"] = array(
				array(
					"curl" => 'Error: '.curl_errno($ch).' - '.curl_error($ch)
					)
				);
			$result = json_encode($res);
		} 
		curl_close($ch);
	pp($result);

     $url = 'http://lic.elcommerce.com.ua/api.php';
     //$url = 'http://elcommerce.com.ua';
     //$url = 'http://oooo.duckdns.org/';
     //$url = 'http://google.com';
     //$url = 'https://api.novaposhta.ua/v2.0/json/';
     $local_ua = 'NovaPoshta';
     $query_string = 'type=check';
     $remote_timeout = _NP_TIMEOUT_;
     
        if (!function_exists('curl_init')) {
            return false;
        }

        $curl = curl_init($url);

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";
        $header[] = "Content-Length: ".strlen($query_string);
        $header[] = "Content-Type: application/json";
        
        //curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $local_ua);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);// allow redirects 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
  		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $remote_timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $remote_timeout);
        
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            //curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}

        
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        $info['ref'] = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
        
	pp($info);
	pp(curl_error($curl));
	pp($result);

        return ;

function pp($string){
	if (function_exists ('p')) {p($string);}
	elseif (function_exists ('dump')) {dump($string);}
}