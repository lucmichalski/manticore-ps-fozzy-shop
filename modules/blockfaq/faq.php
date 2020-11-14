<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://storeprestamodules.com/LICENSE.txt
 *
 /*
 * 
 * @author    StorePrestaModules SPM <kykyryzopresto@gmail.com>
 * @category others
 * @package blockfaq
 * @copyright Copyright (c) 2011 - 2014 SPM LLC. (http://storeprestamodules.com)
 * @license   http://storeprestamodules.com/LICENSE.txt
 */

$_GET['controller'] = 'all'; 
$_GET['fc'] = 'module';
$_GET['module'] = 'blockfaq';
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

$name_module = 'blockfaq';

if (version_compare(_PS_VERSION_, '1.5', '<')){
    require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
} else{
    $smarty = Context::getContext()->smarty;
    $cookie = Context::getContext()->cookie;
}


include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
$obj_blockfaqhelp = new blockfaqhelp();

$search = Tools::getValue("search");
$is_search = 0;

### search ###
if(Tools::strlen($search)>0){
$is_search = 1;

}

$_data = $obj_blockfaqhelp->getItemsSite(array('is_search'=>$is_search,
											   'search'=>$search,
											   'id_category'=>(int)Tools::getValue("category_id")
											   )
										 );
$_items = $_data['items'];



if(version_compare(_PS_VERSION_, '1.6', '>')){
 	$smarty->assign($name_module.'is16' , 1);
} else {
 	$smarty->assign($name_module.'is16' , 0);
}

include_once(dirname(__FILE__).'/blockfaq.php');
$obj_blockfaq = new blockfaq();

$obj_blockfaq->setSEOUrls();
	
$_data_translate = $obj_blockfaq->translateItems();

$smarty->assign('meta_title' , $_data_translate['meta_title_faq']);
$smarty->assign('meta_description' , $_data_translate['meta_description_faq']);
$smarty->assign('meta_keywords' , $_data_translate['meta_keywords_faq']);


$smarty->assign(
    array(
        $name_module.'msg2' => $_data_translate['faq_msg2'],
        $name_module.'msg3' => $_data_translate['faq_msg3'],
        $name_module.'msg4' => $_data_translate['faq_msg4'],
        $name_module.'msg5' => $_data_translate['faq_msg5'],
        $name_module.'msg6' => $_data_translate['faq_msg6'],
        $name_module.'msg7' => $_data_translate['faq_msg7'],
    )
);

$id_customer = (int)$cookie->id_customer;

$customer_lastname = "";
$customer_firstname = "";
$email = "";
if($id_customer != 0){
	$customer_lastname = $cookie->customer_lastname;
	$customer_firstname = $cookie->customer_firstname;
	$email = $cookie->email;
}
$smarty->assign(array($name_module.'customer_lastname' => $customer_lastname));
$smarty->assign(array($name_module.'customer_firstname' => $customer_firstname));
$smarty->assign(array($name_module.'email' => $email));
		

$smarty->assign($name_module.'faqis_captcha', Configuration::get($name_module.'faqis_captcha'));

$smarty->assign($name_module.'is_urlrewrite', Configuration::get($name_module.'is_urlrewrite'));

$smarty->assign($name_module.'faqis_askform', Configuration::get($name_module.'faqis_askform'));

$ps15 = 0;
if(version_compare(_PS_VERSION_, '1.5', '>')){
	$ps15 = 1;
} 
$smarty->assign($name_module.'is_ps15', $ps15);


$data_categories = $obj_blockfaqhelp->getItemsCategory();
    		
$smarty->assign(array($name_module.'items' => $_items,
					  $name_module.'is_search' => $is_search,
					  $name_module.'search' => $search,
					  $name_module.'data_categories' => $data_categories,
					  $name_module.'selected_cat' =>(int)Tools::getValue("category_id")
					  )
				);


if (version_compare(_PS_VERSION_, '1.5', '>')  && version_compare(_PS_VERSION_, '1.6', '<')) {
				if (isset(Context::getContext()->controller)) {
					$oController = Context::getContext()->controller;
				}
				else {
					$oController = new FrontController();
					$oController->init();
				}
				// header
				$oController->setMedia();
				@$oController->displayHeader();
			}
			else {
				if(version_compare(_PS_VERSION_, '1.5', '<'))
					include_once(dirname(__FILE__).'/../../header.php');
			}


if(version_compare(_PS_VERSION_, '1.5', '>')){
	
	if(version_compare(_PS_VERSION_, '1.6', '>')){
					
		$obj_front_c = new ModuleFrontController();
		$obj_front_c->module->name = 'blockfaq';
		$obj_front_c->setTemplate('faq.tpl');
		
		$obj_front_c->setMedia();
		
		$obj_front_c->initHeader();
		
		$obj_front_c->initContent();
		
		$obj_front_c->initFooter();
		
		
		$obj_front_c->display();
		
	} else {
		echo $obj_blockfaq->renderTpl();
	}
} else {
	echo Module::display(dirname(__FILE__).'/blockfaq.php', 'views/templates/front/faq.tpl');
}



if (version_compare(_PS_VERSION_, '1.5', '>')  && version_compare(_PS_VERSION_, '1.6', '<')) {
				if (isset(Context::getContext()->controller)) {
					$oController = Context::getContext()->controller;
				}
				else {
					$oController = new FrontController();
					$oController->init();
				}
				// footer
				@$oController->displayFooter();
			}
			else {
				if(version_compare(_PS_VERSION_, '1.5', '<'))
					include_once(dirname(__FILE__).'/../../footer.php');
			}
