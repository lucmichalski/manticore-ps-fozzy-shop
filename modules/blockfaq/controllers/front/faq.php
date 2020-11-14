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

class BlockfaqfaqModuleFrontController extends ModuleFrontController
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
        $name_module = 'blockfaq';

        $this->php_self = 'module-'.$name_module.'-faq';


		parent::initContent();

        $cookie = Context::getContext()->cookie;

        include_once(dirname(__FILE__).'../../../classes/blockfaqhelp.class.php');
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
            $this->context->smarty->assign($name_module.'is16' , 1);
        } else {
            $this->context->smarty->assign($name_module.'is16' , 0);
        }

        include_once(dirname(__FILE__).'../../../blockfaq.php');
        $obj_blockfaq = new blockfaq();

        $obj_blockfaq->setSEOUrls();

        $_data_translate = $obj_blockfaq->translateItems();

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $_data_translate['meta_title_faq'];
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $_data_translate['meta_description_faq'];
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $_data_translate['meta_keywords_faq'];
        }

        $this->context->smarty->assign('meta_title' , $_data_translate['meta_title_faq']);
        $this->context->smarty->assign('meta_description' , $_data_translate['meta_description_faq']);
        $this->context->smarty->assign('meta_keywords' , $_data_translate['meta_keywords_faq']);


        $this->context->smarty->assign(
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
        $this->context->smarty->assign(array($name_module.'customer_lastname' => $customer_lastname));
        $this->context->smarty->assign(array($name_module.'customer_firstname' => $customer_firstname));
        $this->context->smarty->assign(array($name_module.'email' => $email));


        $this->context->smarty->assign($name_module.'faqis_captcha', Configuration::get($name_module.'faqis_captcha'));

        $this->context->smarty->assign($name_module.'is_urlrewrite', Configuration::get($name_module.'is_urlrewrite'));

        $this->context->smarty->assign($name_module.'faqis_askform', Configuration::get($name_module.'faqis_askform'));

        $ps15 = 0;
        if(version_compare(_PS_VERSION_, '1.5', '>')){
            $ps15 = 1;
        }
        $this->context->smarty->assign($name_module.'is_ps15', $ps15);


        $data_categories = $obj_blockfaqhelp->getItemsCategory();

        $this->context->smarty->assign(array($name_module.'items' => $_items,
                $name_module.'is_search' => $is_search,
                $name_module.'search' => $search,
                $name_module.'data_categories' => $data_categories,
                $name_module.'selected_cat' =>(int)Tools::getValue("category_id")
            )
        );


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/faq17.tpl');
        }else {
            $this->setTemplate('faq.tpl');
        }



    }
}