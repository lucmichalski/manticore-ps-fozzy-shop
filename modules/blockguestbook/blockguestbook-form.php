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

$_GET['controller'] = 'all'; 
$_GET['fc'] = 'module';
$_GET['module'] = 'blockguestbook';
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

$name_module = 'blockguestbook';

if (version_compare(_PS_VERSION_, '1.5', '<')){
    require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
} else{
    $smarty = Context::getContext()->smarty;
}

include_once(dirname(__FILE__).'/blockguestbook.php');
$obj_blockguestbook = new blockguestbook();
$_data_translate = $obj_blockguestbook->translateItems();

$obj_blockguestbook->setSEOUrls();


$smarty->assign('meta_title' , $_data_translate['meta_title_guestbook']);
$smarty->assign('meta_description' , $_data_translate['meta_description_guestbook']);
$smarty->assign('meta_keywords' , $_data_translate['meta_keywords_guestbook']);


$smarty->assign(
    array(
        $name_module.'msg1' => $_data_translate['msg1'],
        $name_module.'msg2' => $_data_translate['msg2'],
        $name_module.'msg3' => $_data_translate['msg3'],
        $name_module.'msg4' => $_data_translate['msg4'],
        $name_module.'msg5' => $_data_translate['msg5'],
        $name_module.'msg6' => $_data_translate['msg6'],
        $name_module.'msg7' => $_data_translate['msg7'],
        $name_module.'msg8' => $_data_translate['msg8'],
        $name_module.'msg9' => $_data_translate['msg9'],
    )
);

$page_translate = $_data_translate['page'];


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


include_once(dirname(__FILE__).'/classes/guestbook.class.php');
$obj_guestbook = new guestbook();



if(version_compare(_PS_VERSION_, '1.6', '>')){
 	$smarty->assign($name_module.'is16' , 1);
} else {
 	$smarty->assign($name_module.'is16' , 0);
}

$smarty->assign($name_module.'is_captchag', Configuration::get($name_module.'is_captchag'));
$smarty->assign($name_module.'is_webg', Configuration::get($name_module.'is_webg'));
$smarty->assign($name_module.'is_companyg', Configuration::get($name_module.'is_companyg'));
$smarty->assign($name_module.'is_addrg', Configuration::get($name_module.'is_addrg'));
$smarty->assign($name_module.'is_countryg', Configuration::get($name_module.'is_countryg'));
$smarty->assign($name_module.'is_cityg', Configuration::get($name_module.'is_cityg'));

$step = Configuration::get($name_module.'perpageg');
$p = (int)Tools::getValue('p');


$start = (int)(($p - 1)*$step);
if($start<0)
    $start = 0;


$_data = $obj_guestbook->getItems(array('start'=>$start,'step'=>$step));

$paging = $obj_guestbook->PageNav($start,$_data['count_all_reviews'],$step,array('page'=>$page_translate));


$smarty->assign(array('reviews_items' => $_data['reviews'], 
					  'count_all_reviews' => $_data['count_all_reviews'],
					  'paging' => $paging
					  )
				);


if(version_compare(_PS_VERSION_, '1.5', '>')){
	
	if(version_compare(_PS_VERSION_, '1.6', '>')){
					
		$obj_front_c = new ModuleFrontController();
		$obj_front_c->module->name = 'blockguestbook';
		$obj_front_c->setTemplate('blockguestbook.tpl');
		
		$obj_front_c->setMedia();
		
		$obj_front_c->initHeader();
		$obj_front_c->initFooter();
		
		$obj_front_c->initContent();
		
		
		
		$obj_front_c->display();
		
	} else {
		echo $obj_blockguestbook->renderTplGuestbook();
	}
} else {
	echo Module::display(dirname(__FILE__).'/blockguestbook.php', 'views/templates/front/blockguestbook.tpl');
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

?>