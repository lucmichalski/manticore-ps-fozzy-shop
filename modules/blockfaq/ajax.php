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
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

$HTTP_X_REQUESTED_WITH = isset($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']:'';
if($HTTP_X_REQUESTED_WITH != 'XMLHttpRequest') {
    exit;
}

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
ob_start(); 
$status = 'success';
$message = '';
$error_type = 0;

$name_module = 'blockfaq';

$action = Tools::getValue('action');

include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
$obj_blockfaqhelp = new blockfaqhelp();


if (version_compare(_PS_VERSION_, '1.5', '<')){
    require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
}

switch ($action){
	
	case 'addquestion':
		$name = htmlspecialchars(Tools::getValue('name'));
		$email = htmlspecialchars(Tools::getValue('email'));
		$text = htmlspecialchars(Tools::getValue('text_question'));
		$category = Tools::getValue('category');
		
		$codeCaptcha = Tools::strlen(Tools::getValue('captcha'))>0?Tools::getValue('captcha'):'';


        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $data_code = getcookie_blockfaq();
            $code = $data_code['code'];
        } else {
            $cookie = new Cookie($name_module);
            $code = $cookie->secure_code_blockfaq;
        }

		$ok_captcha = 1;
		$is_captcha = Configuration::get($name_module.'faqis_captcha');
		if($is_captcha == 1){
			if($code == $codeCaptcha)
				$ok_captcha = 1;
			else
				$ok_captcha = 0;
		}
		
		if($ok_captcha == 1){
		if(!preg_match("/[0-9a-z-_]+@[0-9a-z-_^\.]+\.[a-z]{2,4}/i", $email)) {
		    $error_type = 2;
			$status = 'error';
		 }
		
		 if($error_type == 0){
		 	
		 	$data = array('category'=>$category,
						  'name' => $name,
					  	  'email' => $email,
						  'text' => $text
		 				 );
		 	$obj_blockfaqhelp->saveItemFAQ($data);
			
		 }
		} else {
			$_html = '';
			// invalid security code (captcha)
			$error_type = 3;
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
            case 'categoryfaq':
                $obj_blockfaqhelp->updateCategoryStatus(array('id'=>$id,'status'=>$value));
                break;
            case 'questionfaq':
                $obj_blockfaqhelp->updateQuestionStatus(array('id'=>$id,'status'=>$value));
                break;

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

$response->params = array('content' => $content, 'error_type' => $error_type);

echo Tools::jsonEncode($response);
	

?>