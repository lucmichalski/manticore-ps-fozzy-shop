<?php
class JustinMy
{              
public $url = "http://195.201.72.186/justin_pms/hs/v2/runRequest";

public function justin_getregion($login, $password, $lang = 'RU')
    {
     $pass = $password.":".date("Y-m-d");
     $get_oblast_array = array();
     $get_oblast_array['keyAccount'] = $login;
     $get_oblast_array['sign'] = sha1($pass);
     $get_oblast_array['request'] = 'getData';
     $get_oblast_array['type'] = 'catalog';
     $get_oblast_array['name'] = 'cat_Region';
     $get_oblast_array['language'] = $lang;
     $get_oblast_array['TOP'] = 100000;
     
     $url = $this->url;
     
     $json_data = json_encode($get_oblast_array);

     return json_decode($this->file_get_contents_curl($url,true,$json_data));
    }

public function justin_getregioncenters($login, $password, $lang = 'RU')
    {
     $pass = $password.":".date("Y-m-d");
     $get_oblast_array = array();
     $get_oblast_array['keyAccount'] = $login;
     $get_oblast_array['sign'] = sha1($pass);
     $get_oblast_array['request'] = 'getData';
     $get_oblast_array['type'] = 'catalog';
     $get_oblast_array['name'] = 'cat_areasRegion';
     $get_oblast_array['language'] = $lang;
     $get_oblast_array['TOP'] = 100000;
     
     $url = $this->url;
     
     $json_data = json_encode($get_oblast_array);

     return json_decode($this->file_get_contents_curl($url,true,$json_data));
    }
    
public function justin_gettowns($login, $password, $lang = 'RU')
    {
     $pass = $password.":".date("Y-m-d");
     $get_oblast_array = array();
     $get_oblast_array['keyAccount'] = $login;
     $get_oblast_array['sign'] = sha1($pass);
     $get_oblast_array['request'] = 'getData';
     $get_oblast_array['type'] = 'catalog';
     $get_oblast_array['name'] = 'cat_Cities';
     $get_oblast_array['language'] = $lang;
     $get_oblast_array['TOP'] = 100000;
     
     $url = $this->url;
     
     $json_data = json_encode($get_oblast_array);

     return json_decode($this->file_get_contents_curl($url,true,$json_data));
    }
    
public function justin_getware($login, $password, $lang = 'RU')
    {
     $pass = $password.":".date("Y-m-d");
     $get_oblast_array = array();
     $get_oblast_array['keyAccount'] = $login;
     $get_oblast_array['sign'] = sha1($pass);
     $get_oblast_array['request'] = 'getData';
     $get_oblast_array['type'] = 'request';
     $get_oblast_array['name'] = 'req_DepartmentsLang';
     $get_oblast_array['language'] = $lang;
     $get_oblast_array['params']['language'] = $lang;
     $get_oblast_array['TOP'] = 100000;
     
     $url = $this->url;
     
     $json_data = json_encode($get_oblast_array);

     return json_decode($this->file_get_contents_curl($url,true,$json_data));
    }
    
private function file_get_contents_curl( $url, $post = true, $json_data = "" ) {

    $ch = curl_init();
    
    curl_setopt( $ch, CURLOPT_USERPWD, "Exchange:Exchange");
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
    curl_setopt( $ch, CURLOPT_POST, $post);
    curl_setopt( $ch, CURLOPT_HEADER, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
 //   curl_setopt( $ch, CURLOPT_POSTFIELDS, array());
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    
    $data = curl_exec( $ch );
    curl_close( $ch );

  return $data;

  }

}