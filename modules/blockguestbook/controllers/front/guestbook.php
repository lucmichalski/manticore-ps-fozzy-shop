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

class BlockguestbookguestbookModuleFrontController extends ModuleFrontController
{
    public $php_self;
	public function init()
	{

		parent::init();
	}
	
	public function setMedia()
	{
		parent::setMedia();
    }

	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
        $name_module = 'blockguestbook';

        $this->php_self = 'module-'.$name_module.'-guestbook';

		parent::initContent();


        $cookie = Context::getContext()->cookie;


        include_once(dirname(__FILE__).'../../../blockguestbook.php');
        $obj_blockguestbook = new blockguestbook();
        $_data_translate = $obj_blockguestbook->translateItems();

        $obj_blockguestbook->setSEOUrls();



        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $_data_translate['meta_title_guestbook'];
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $_data_translate['meta_description_guestbook'];
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $_data_translate['meta_keywords_guestbook'];
        }

        $this->context->smarty->assign('meta_title' , $_data_translate['meta_title_guestbook']);
        $this->context->smarty->assign('meta_description' , $_data_translate['meta_description_guestbook']);
        $this->context->smarty->assign('meta_keywords' , $_data_translate['meta_keywords_guestbook']);

        $page_translate = $_data_translate['page'];


        $this->context->smarty->assign(
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


        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->context->smarty->assign($name_module.'is16' , 1);
        } else {
            $this->context->smarty->assign($name_module.'is16' , 0);
        }


        include_once(dirname(__FILE__).'../../../classes/guestbook.class.php');
        $obj_guestbook = new guestbook();


        $this->context->smarty->assign($name_module.'is_captchag', Configuration::get($name_module.'is_captchag'));
        $this->context->smarty->assign($name_module.'is_webg', Configuration::get($name_module.'is_webg'));
        $this->context->smarty->assign($name_module.'is_companyg', Configuration::get($name_module.'is_companyg'));
        $this->context->smarty->assign($name_module.'is_addrg', Configuration::get($name_module.'is_addrg'));
        $this->context->smarty->assign($name_module.'is_countryg', Configuration::get($name_module.'is_countryg'));
        $this->context->smarty->assign($name_module.'is_cityg', Configuration::get($name_module.'is_cityg'));

        $step = Configuration::get($name_module.'perpageg');
        $p = (int)Tools::getValue('p');


        $start = (int)(($p - 1)*$step);
        if($start<0)
            $start = 0;

        $_data = $obj_guestbook->getItems(array('start'=>$start,'step'=>$step));

        $paging = $obj_guestbook->PageNav($start,$_data['count_all_reviews'],$step,array('page'=>$page_translate));


        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
        $name_customer = '';
        $email_customer = '';
        if($id_customer) {
            $customer_data = $obj_guestbook->getInfoAboutCustomer(array('id_customer' => $id_customer, 'is_full' => 1));
            $name_customer = $customer_data['customer_name'];
            $email_customer = $customer_data['email'];
        }


        // gdpr
        $this->context->smarty->assign(array('id_module' => $obj_blockguestbook->getIdModule()));
        // gdpr

        $this->context->smarty->assign(array('reviews_items' => $_data['reviews'],
                'count_all_reviews' => $_data['count_all_reviews'],
                'paging' => $paging,
                'shop_name_snippet'=>Configuration::get('PS_SHOP_NAME'),

                $name_module.'name_c' => $name_customer,
                $name_module.'email_c' => $email_customer,

            )
        );

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/blockguestbook17.tpl');
        }else {
            $this->setTemplate('blockguestbook.tpl');
        }




    }
}