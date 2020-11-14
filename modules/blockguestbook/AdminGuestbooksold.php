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

ob_start();
	/*@ini_set('display_errors', 'on');	
	define('_PS_DEBUG_SQL_', true);
	define('_PS_DISPLAY_COMPATIBILITY_WARNING_', true);
	error_reporting(E_ALL|E_STRICT);
	*/
class AdminGuestbooksold extends AdminTab{

	private $_is15;
	public function __construct()

	{
		$this->module = 'blockguestbook';
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$this->multishop_context = Shop::CONTEXT_ALL;
			$this->_is15 = 1;
		} else {
			$this->_is15 = 0;
		}
		
		
		parent::__construct();
		
	}
	
	public function addJS(){
		
	}
	
	public function addCss(){
		
	}
	
	public function display()
	{
		echo '<style type="text/css">.warn{display:none!important}
									 #maintab20{display:none!important}
									 
		</style>';

        $tab = 'AdminGuestbooksold';

        if (version_compare(_PS_VERSION_, '1.5', '<')){
            require_once(_PS_MODULE_DIR_.'blockguestbook/backward_compatibility/backward.php');
        } else {
            $currentIndex = isset(AdminController::$currentIndex)?AdminController::$currentIndex:'index.php?controller='.$tab;
        }

		// include main class
		require_once(dirname(__FILE__) .  '/blockguestbook.php');
		// instantiate
		$obj_main = new blockguestbook();
		

		
		$token = $this->token;
		
		
		include_once(dirname(__FILE__).'/classes/guestbook.class.php');
		$obj_guestbook = new guestbook();
		
		
		// publish
	   if (Tools::isSubmit("published")) {
			if (Validate::isInt(Tools::getValue("id"))){
				$obj_guestbook->publish(array('id'=>Tools::getValue("id")));
			} 
			Tools::redirectAdmin($currentIndex.'&tab='.$tab.'&configure='.$this->module.'&token='.$token.'');
			
		}
		
		//unpublish
		if (Tools::isSubmit("unpublished")) {
			if (Validate::isInt(Tools::getValue("id"))){
				$obj_guestbook->unpublish(array('id'=>Tools::getValue("id")));
			} 
			Tools::redirectAdmin($currentIndex.'&tab='.$tab.'&configure='.$this->module.'&token='.$token.'');
			
		}
		
    	// delete item
		if (Tools::isSubmit("delete_item")) {
			if (Validate::isInt(Tools::getValue("id"))) {
				$obj_guestbook->delete(array('id'=>Tools::getValue("id")));
				Tools::redirectAdmin($currentIndex.'&tab='.$tab.'&configure='.$this->module.'&token='.$token.'');
			}
			
		}
		
		if (Tools::isSubmit('submit_item'))
        {
        	$name = Tools::getValue("name");
        	$email = Tools::getValue("email");
        	$message = Tools::getValue("message");
        	$publish = (int)Tools::getValue("publish");

            $web = Tools::getValue("web");
            $company = Tools::getValue("company");
            $address = Tools::getValue("address");

            $country = Tools::getValue("country");
            $city = Tools::getValue("city");

            $response = Tools::getValue("response");
            $is_noti = Tools::getValue("is_noti");
            $is_show = Tools::getValue("is_show");

            $date_add = Tools::getValue("date_add");


        	$obj_guestbook->updateItem(array('name'=>$name,
        									 'email'=>$email,
        									 'message'=>$message,
        									 'publish'=>$publish,
        									 'id'=>Tools::getValue("id"),

                                            'web' =>$web,
                                            'address'=>$address,
                                            'company'=>$company,
                                            'country'=>$country,
                                            'city'=>$city,
                                            'date_add' => $date_add,
                                            'response'=>$response,
                                            'is_noti'=>$is_noti,
                                            'is_show'=>$is_show,

        									 )
        								);
        	
        	Tools::redirectAdmin($currentIndex.'&tab='.$tab.'&configure='.$this->module.'&token='.$token.'');
		
        }
               
		if (Tools::isSubmit('cancel_item'))
        {
        	Tools::redirectAdmin($currentIndex.'&tab='.$tab.'&configure='.$this->module.'&token='.$token.'');
		}
		
	 
		echo $obj_main->_jsandcss();
		echo $obj_main->drawGuestbookMessages(array('currentindex'=>$currentIndex,'controller'=>$tab));
		
				
	}
		

}

?>

