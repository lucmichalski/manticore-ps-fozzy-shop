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

class BlockfaqAllModuleFrontController extends ModuleFrontController
{
	
	public function init()
	{
		
		$http_referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		
		$faq_exists = stripos($http_referrer, 'faq');

		// faq
		if($faq_exists != false){
		
		include_once(dirname(__FILE__).'../../../classes/blockfaqhelp.class.php');
		$obj = new blockfaqhelp();
		$is_friendly_url = $obj->isURLRewriting();
		
		if($is_friendly_url){
			
		if(Tools::strlen($http_referrer)>0){
				
			$lang_iso_redirect = Language::getIsoById((int)(Tools::getValue("id_lang")));
		
			$languages = Language::getLanguages(false);
			foreach ($languages as $language){
				$iso_array = Language::getIsoById((int)($language['id_lang']));
				if(preg_match('/\/'.$iso_array.'\//i',$http_referrer)){
					$iso_code = $iso_array;
					break;
				}
			}
			$seo_friendly_url = '';
			$item_seo_url = '';
			
			
				$to = '/'.$iso_code.'/';
				$from = '/'.$lang_iso_redirect.'/';
				$http_referrer = str_replace($to,$from,$http_referrer);
				
				$to = $item_seo_url;
				$from = $seo_friendly_url;
				$http_referrer = str_replace($to,$from,$http_referrer);
				
			
		}
		
		}
		
		}
		
		// faq
		
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
							
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	
		header("HTTP/1.1 301 Moved Permanently");
		Tools::redirect($http_referrer);
		parent::init();
	
	}
	
	public function setMedia()
	{
		parent::setMedia();
		//$this->addJqueryPlugin(array('thickbox', 'idTabs'));
	}

	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		
		parent::initContent();
		
		$this->setTemplate('all.tpl');
		
	}
}