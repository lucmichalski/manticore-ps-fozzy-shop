<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockguestbook
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
ob_start(); 
$status = 'success';
$message = '';

$name_module = 'blockguestbook';


if (version_compare(_PS_VERSION_, '1.5', '<')){
    require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
} else{
    $smarty = Context::getContext()->smarty;
}
		

include_once(dirname(__FILE__).'/classes/guestbook.class.php');
$obj_guestbook = new guestbook();

$action = Tools::getValue('action');

switch ($action){
	
	case 'addreviewguestbook':
		$_html = '';
		$error_type = 0;
		$codeCaptcha = Tools::getValue('captcha');

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $data_code = getcookie_blockguestbook();
            $code = $data_code['code'];
        } else {
            $cookie = new Cookie($name_module);
            $code = $cookie->secure_code_guestb;
        }

        //var_dump($codeCaptcha); var_dump($code);exit;

        $ok_captcha = 1;
		$is_captchag = Configuration::get($name_module.'is_captchag');
		if($is_captchag == 1){
			if($code == $codeCaptcha)
				$ok_captcha = 1;
			else
				$ok_captcha = 0;
		}
		
		if($ok_captcha == 1){

		    $name = strip_tags(trim(htmlspecialchars(Tools::getValue('name-review'))));

            $country = strip_tags(trim(htmlspecialchars(Tools::getValue('country-review'))));
            $city = strip_tags(trim(htmlspecialchars(Tools::getValue('city-review'))));

            $email = trim(Tools::getValue('email-review'));
            $web = strip_tags(str_replace("http://","",trim(Tools::getValue('web-review'))));
            $web = strip_tags(str_replace("https://","",trim(Tools::getValue('web-review'))));
            $text_review = strip_tags(trim(htmlspecialchars(Tools::getValue('text-review'))));
            $company = strip_tags(trim(htmlspecialchars(Tools::getValue('company-review'))));
            $address = strip_tags(trim(htmlspecialchars(Tools::getValue('address-review'))));

            if(!Validate::isEmail($email)) {
                $error_type = 2;
                $status = 'error';
            }

            if($error_type == 0 && Tools::strlen($name)==0){
                $error_type = 1;
                $status = 'error';
            }

            if($error_type == 0 && Tools::strlen($text_review)==0){
                $error_type = 3;
                $status = 'error';
            }

            $files = $_FILES['avatar-review'];
            if(!empty($files['name']))
            {
                if(!$files['error'])
                {
                    $type_one = $files['type'];
                    $ext = explode("/",$type_one);

                    if(strpos('_'.$type_one,'image')<1)
                    {
                        $error_type = 8;
                        $status = 'error';

                    }elseif(!in_array($ext[1],array('png','x-png','gif','jpg','jpeg','pjpeg'))) {
                        $error_type = 9;
                        $status = 'error';
                    }
                }
            }
		
		 if($error_type == 0){
			//insert item

             $_data = array('name' => $name,
                             'email' => $email,
                             'web' => $web,
                             'text_review' => $text_review,
                             'company' => $company,
                             'address' => $address,
                             'city'=>$city,
                             'country'=>$country,
             );

			$obj_guestbook->saveItem($_data);
			
		 }
		
		} else {
			$_html = '';
			
			// invalid security code (captcha)
			$error_type = 4;
			$status = 'error';
		}
		
	break;
    case 'active':
        $id = (int)Tools::getValue('id');
        $value = (int)Tools::getValue('value');
        if($value == 0){
            $value = 1;
        } else {
            $value = 0;
        }
        $type_action = Tools::getValue('type_action');

        switch($type_action){
            case 'testimonial':
                $obj_guestbook->setPublsh(array('id'=>$id,'active'=>$value));
                break;


        }



        break;
    case 'deleteimg':
        include_once(dirname(__FILE__).'/blockguestbook.php');
        $obj_blockguestbook = new blockguestbook();

        if($obj_blockguestbook->is_demo){
            $status = 'error';
            $message = 'Feature disabled on the demo mode!';
        } else {
            $item_id = Tools::getValue('item_id');
            $obj_guestbook->deleteAvatar(array('id' => $item_id));
        }
        break;
	default:
		$status = 'error';
		$message = 'Unknown parameters!';
	break;
}


$response = new stdClass();
$content = ob_get_clean();
$response->status = $status;
$response->message = $message;	
if($action == "addreviewguestbook"){
	$response->params = array('content' => $_html,
							  'error_type' => $error_type
							   );
}
else
	$response->params = array('content' => $content);


echo Tools::jsonEncode($response);
	

?>