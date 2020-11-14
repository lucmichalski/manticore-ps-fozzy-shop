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

class blockfaq extends Module
{
	private $_is15;
	private $_admin_email;
	private $_is_friendly_url;
	private $_iso_lng;
	private $_is16;
	
	public function __construct()
	{
		$this->name = 'blockfaq';
		$this->tab = 'content_management';
		$this->version = '1.4.3';
		$this->author = 'SPM';
		$this->module_key = 'befbbee1ca6152857a2ffb46d90d455c';
		$this->confirmUninstall = $this->l('Are you sure you want to remove it ? Your Frequently asked questions - FAQ will no longer work. Be careful, all your configuration and your data will be lost');

        require_once(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');

		parent::__construct(); // The parent construct is required for translations

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Frequently asked questions - FAQ');
		$this->description = $this->l('Add Frequently asked questions - FAQ');

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->bootstrap = true;
            $this->need_instance = 0;
        }
 	 	
 	 	
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$this->_is15 = 1;
		} else {
			$this->_is15 = 0;
		}
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
 	 		$this->_is16 = 1;
 	 	} else {
 	 		$this->_is16 = 0;
 	 	}
 	 	
		$this->_admin_email = @Configuration::get('PS_SHOP_EMAIL');
		
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
 	 		include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
 	 	}else{
 	 		include_once(_PS_MODULE_DIR_.$this->name.'/classes/blockfaqhelp.class.php');	
 	 	}
 	 	
		$obj = new blockfaqhelp();
		$is_friendly_url = $obj->isURLRewriting();
		$this->_is_friendly_url = $is_friendly_url;
		$this->_iso_lng = $obj->getLangISO();
		
		$this->initContext();


        ## prestashop 1.7 ##
        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_.$this->name.'/classes/ps17helpblockfaq.class.php');
            $ps17help = new ps17helpblockfaq();
            $ps17help->setMissedVariables();
        } else {
            $smarty = $this->context->smarty;
            $smarty->assign($this->name.'is17' , 0);
        }
        ## prestashop 1.7 ##


	}



	private function initContext()
	{
        $this->context = Context::getContext();

        if (version_compare(_PS_VERSION_, '1.5', '>')){
            $this->context->currentindex = isset(AdminController::$currentIndex)?AdminController::$currentIndex:'index.php?controller=AdminModules';
        } else {

            $variables14 = variables_blockfaq14();
            $this->context->currentindex = $variables14['currentindex'];


        }
	}

	public function install()
	{


		
		if (!parent::install())
			return false;

        Configuration::updateValue($this->name.'faq_spm', 1);

		Configuration::updateValue($this->name.'faq_home', 1);
			
		Configuration::updateValue($this->name.'faq_left', 1);
			
		Configuration::updateValue($this->name.'faq_blc', 5);
		
		Configuration::updateValue($this->name.'faqis_captcha', 1);

        if(Configuration::get('PS_REWRITING_SETTINGS')){
            Configuration::updateValue($this->name.'is_urlrewrite', 1);
        }
		
		
		Configuration::updateValue($this->name.'faqis_askform', 1);
		
		Configuration::updateValue($this->name.'notifaq', 1);	
		Configuration::updateValue($this->name.'mailfaq', $this->_admin_email);


        if(version_compare(_PS_VERSION_, '1.6', '<'))
    		$this->generateRewriteRules();
		
		if($this->_is15 == 1)
	 		$this->createAdminTabs();
	 	else
	 		$this->createAdminTabs14();
		
		if (!$this->registerHook('leftColumn') 
			OR !$this->registerHook('rightColumn')
			OR !$this->registerHook('Header') 
			OR !$this->registerHook('home')
			OR !$this->registerHook('footer')  
			OR !$this->_installDB()
            OR !((version_compare(_PS_VERSION_, '1.6', '>'))? $this->registerHook('ModuleRoutes') : true)
            OR !((version_compare(_PS_VERSION_, '1.6', '>'))? $this->registerHook('DisplayBackOfficeHeader') : true)
            OR !((version_compare(_PS_VERSION_, '1.5', '>'))? $this->registerHook('faqSPM') : true)
			 )
			return false;
		
		
		return true;
	}
	
	public function uninstall()
	{
		if($this->_is15 == 1)
			$this->uninstallTab();
		else
			$this->uninstallTab14();
		
		if (!parent::uninstall() OR !$this->uninstallTable())
			return false;
		return true;
	}


    public function hookModuleRoutes()
    {
        return array(

            ## faq ##

            'blockfaq-faq' => array(
                'controller' =>	null,
                'rule' =>		'{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => 'faq', 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockfaq'
                )
            ),

            ## faq ##


        );
    }


    public function hookDisplayBackOfficeHeader()
    {

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $base_dir = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        } else {
            $base_dir = _PS_BASE_URL_.__PS_BASE_URI__;
        }


        $css = '';
        $css .= '<style type="text/css">
		.icon-AdminFaq:before {
		content: url("'.$base_dir.'modules/'.$this->name.'/AdminFaqsold.gif");
	}
	</style>
	';
        return $css;
    }


    public function createAdminTabs(){


			@copy_custom_blockfaq(dirname(__FILE__)."/AdminFaqsold.gif",_PS_ROOT_DIR_."/img/t/AdminFaqsold.gif");

            if(version_compare(_PS_VERSION_, '1.6', '>')) {

                $langs = Language::getLanguages();


                $tab0 = new Tab();
                $tab0->class_name = "AdminFaq";
                $tab0->module = $this->name;
                $tab0->id_parent = 0;
                foreach ($langs as $l) {
                    $tab0->name[$l['id_lang']] = $this->l('FAQ');
                }
                $tab0->save();
                $main_tab_id = $tab0->id;

                unset($tab0);

                $tab1 = new Tab();
                $tab1->class_name = "AdminFaqcategories";
                $tab1->module = $this->name;
                $tab1->id_parent = $main_tab_id;
                foreach ($langs as $l) {
                    $tab1->name[$l['id_lang']] = $this->l('Moderate FAQ Categories');
                }
                $tab1->save();

                unset($tab1);

                $tab2 = new Tab();
                $tab2->class_name = "AdminFaqquestions";
                $tab2->module = $this->name;
                $tab2->id_parent = $main_tab_id;
                foreach ($langs as $l) {
                    $tab2->name[$l['id_lang']] = $this->l('Moderate FAQ Questions');
                }
                $tab2->save();

                unset($tab2);

            } else {
                $langs = Language::getLanguages();


                $tab0 = new Tab();
                $tab0->class_name = "AdminFaqold";
                $tab0->module = $this->name;
                $tab0->id_parent = 0;
                foreach ($langs as $l) {
                    $tab0->name[$l['id_lang']] = $this->l('FAQ');
                }
                $tab0->save();
                $main_tab_id = $tab0->id;

                unset($tab0);

                $tab1 = new Tab();
                $tab1->class_name = "AdminFaqsold";
                $tab1->module = $this->name;
                $tab1->id_parent = $main_tab_id;
                foreach ($langs as $l) {
                    $tab1->name[$l['id_lang']] = $this->l('Moderate FAQ');
                }
                $tab1->save();

                unset($tab1);
            }
        

	}
	
	private function createAdminTabs14(){
			@copy_custom_blockfaq(dirname(__FILE__)."/AdminFaqsold.gif",_PS_ROOT_DIR_."/img/t/AdminFaqsold.gif");
		
		 	$langs = Language::getLanguages();
            
          
            $tab0 = new Tab();
            $tab0->class_name = "AdminFaqsold";
            $tab0->module = $this->name;
            $tab0->id_parent = 0; 
            foreach($langs as $l){
                    $tab0->name[$l['id_lang']] = $this->l('FAQ');
            }
            $tab0->save();

	}
	
	private function uninstallTab(){

        if(version_compare(_PS_VERSION_, '1.6', '>')) {

            $tab_id = Tab::getIdFromClassName("AdminFaq");
            if ($tab_id) {
                $tab = new Tab($tab_id);
                $tab->delete();
            }

            $tab_id = Tab::getIdFromClassName("AdminFaqcategories");
            if ($tab_id) {
                $tab = new Tab($tab_id);
                $tab->delete();
            }
            $tab_id = Tab::getIdFromClassName("AdminFaqquestions");
            if ($tab_id) {
                $tab = new Tab($tab_id);
                $tab->delete();
            }

        } else {
            $tab_id = Tab::getIdFromClassName("AdminFaqold");
            if ($tab_id) {
                $tab = new Tab($tab_id);
                $tab->delete();
            }

            $tab_id = Tab::getIdFromClassName("AdminFaqsold");
            if ($tab_id) {
                $tab = new Tab($tab_id);
                $tab->delete();
            }
        }
		
		@unlink(_PS_ROOT_DIR_."/img/t/AdminFaq.gif");
	}
	
	private function uninstallTab14(){
		
		$tab_id = Tab::getIdFromClassName("AdminFaqsold");
		if($tab_id){
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		@unlink(_PS_ROOT_DIR_."/img/t/AdminFaqsold.gif");
	}
	
	
	private function generateRewriteRules(){
            
            if(Configuration::get('PS_REWRITING_SETTINGS')){

                $rules = "#blockfaq - not remove this comment \n";
                
                $physical_uri = array();
                
                if($this->_is15){
	                foreach (ShopUrl::getShopUrls() as $shop_url)
					{
	                    if(in_array($shop_url->physical_uri,$physical_uri)) continue;
	                    
	                  $rules .= "RewriteRule ^(.*)faq/?$ ".$shop_url->physical_uri."modules/".$this->name."/faq.php [QSA,L] \n"; 
	                    
	                    $physical_uri[] = $shop_url->physical_uri;
	                } 
                } else{
                	$rules .= "RewriteRule ^(.*)faq/?$ /modules/".$this->name."/faq.php [QSA,L] \n"; 
	                  
                }
                $rules .= "#blockfaq \n\n";
                
                $path = _PS_ROOT_DIR_.'/.htaccess';

                  if(is_writable($path)){
                      
                      $existingRules = file_get_contents_custom_blockfaq($path);
                      
                      if(!strpos($existingRules, "blockfaq")){
                        $handle = fopen($path, 'w');
                        fwrite($handle, $rules.$existingRules);
                        fclose($handle);
                      }
                  }
              }
        }
	
	
	private function _installDB(){
		
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'faq_category` (
							  `id` int(11) NOT NULL auto_increment,
							  `ids_shops` varchar(1024) NOT NULL default \'0\',
							  `order_by` int(10) default NULL,
							  `status` int(11) NOT NULL default \'1\',
							  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
							  PRIMARY KEY  (`id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		if (!Db::getInstance()->Execute($sql))
			return false;
			
		$query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'faq_category_data` (
							  `id_item` int(11) NOT NULL,
							  `id_lang` int(11) NOT NULL,
							  `title` varchar(5000) default NULL,
							  KEY `id_item` (`id_item`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8';
		if (!Db::getInstance()->Execute($query))
			return false;
		
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'faq_item` (
							  `id` int(11) NOT NULL auto_increment,
							  `status` int(11) NOT NULL default \'1\',
							  `order_by` int(10) default NULL,
							  `ids_shops` varchar(1024) NOT NULL default \'0\',
							  `customer_name` varchar(5000) NOT NULL,
							  `customer_email` varchar(5000) NOT NULL,
							  `is_by_customer` int(11) NOT NULL DEFAULT \'0\',
							  `is_add_by_customer` int(11) NOT NULL DEFAULT \'0\',
							  `time_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
							  PRIMARY KEY  (`id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		Db::getInstance()->Execute($sql);
			
		$query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'faq_item_data` (
							  `id_item` int(11) NOT NULL,
							  `id_lang` int(11) NOT NULL,
							  `title` varchar(5000) NOT NULL,
							  `content` text,
							  KEY `id_item` (`id_item`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8';
		Db::getInstance()->Execute($query);
		
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'faq_category2item` (
							  `category_id` int(11) NOT NULL,
							  `faq_id` int(11) NOT NULL,
							  KEY `category_id` (`category_id`),
							  KEY `post_id` (`faq_id`),
							  KEY `category2item` (`category_id`,`faq_id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		if (!Db::getInstance()->Execute($sql))
			return false;
			
		return true;
	}
	
	public function uninstallTable() {
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'faq_category');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'faq_category_data');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'faq_item');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'faq_item_data');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'faq_category2item');
		return true;
	}

    public function hookfaqSPM($params)
    {
        $smarty = $this->context->smarty;
        include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
        $obj_blockfaqhelp = new blockfaqhelp();
        $_data = $obj_blockfaqhelp->getItemsBlock();

        $smarty->assign(array($this->name.'itemsblock' => $_data['items']
            )
        );
        $smarty->assign($this->name.'faq_home', Configuration::get($this->name.'faq_home'));

        $smarty->assign($this->name.'faq_spm', Configuration::get($this->name.'faq_spm'));


        $this->setSEOUrls();
        return $this->display(__FILE__, 'views/templates/hooks/faqspm.tpl');

    }
	
	public function hookHome($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
		$obj_blockfaqhelp = new blockfaqhelp();
    	$_data = $obj_blockfaqhelp->getItemsBlock();
		
		$smarty->assign(array($this->name.'itemsblock' => $_data['items']
							  )
						);
		$smarty->assign($this->name.'faq_home', Configuration::get($this->name.'faq_home'));



        $this->setSEOUrls();
		return $this->display(__FILE__, 'views/templates/hooks/home.tpl');		
	}
	
	public function hookFooter($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
		$obj_blockfaqhelp = new blockfaqhelp();
    	$_data = $obj_blockfaqhelp->getItemsBlock();
		
		$smarty->assign(array($this->name.'itemsblock' => $_data['items']
							  )
						);
		$smarty->assign($this->name.'faq_footer', Configuration::get($this->name.'faq_footer'));
        $this->setSEOUrls();
		
		return $this->display(__FILE__, 'views/templates/hooks/footer.tpl');		
	}
	
	public function hookLeftColumn($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
		$obj_blockfaqhelp = new blockfaqhelp();
    	$_data = $obj_blockfaqhelp->getItemsBlock();
		
		$smarty->assign(array($this->name.'itemsblock' => $_data['items']
							  )
						);
		$smarty->assign($this->name.'faq_left', Configuration::get($this->name.'faq_left'));


        $this->setSEOUrls();
		
		return $this->display(__FILE__, 'views/templates/hooks/left.tpl');		
	}
	
	public function hookRightColumn($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
		$obj_blockfaqhelp = new blockfaqhelp();
    	$_data = $obj_blockfaqhelp->getItemsBlock();
		
		$smarty->assign(array($this->name.'itemsblock' => $_data['items']
							  )
						);
		$smarty->assign($this->name.'faq_right', Configuration::get($this->name.'faq_right'));
        $this->setSEOUrls();
		
		return $this->display(__FILE__, 'views/templates/hooks/right.tpl');	
	}
	
	public function hookHeader($params){

    	if(version_compare(_PS_VERSION_, '1.5', '>')){
    		$this->context->controller->addCSS(($this->_path).'views/css/blockfaq.css', 'all');
    	}

        if(version_compare(_PS_VERSION_, '1.7', '>')){
            $this->context->controller->addCSS(($this->_path).'views/css/blockfaq17.css', 'all');
        }

        $this->setSEOUrls();
    	
    	return $this->display(__FILE__, 'views/templates/hooks/head.tpl');
    }

    protected function addBackOfficeMedia()
    {
        $this->context->controller->addCSS($this->_path.'views/css/font-custom.min.css');
        //CSS files

        // JS files
        $this->context->controller->addJs($this->_path.'views/js/menu16.js');


    }
    
     public function getContent()
    {
        $this->_html = '';
        $cookie = $this->context->cookie;
        $currentIndex = $this->context->currentindex;
        include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
		$obj_blockfaq = new blockfaqhelp();
		
		
		$id_self = (int)Tools::getValue('id');
		$order_self = Tools::getValue('order_self');
    	if($order_self){
				$obj_blockfaq->update_order($id_self, $order_self, Tools::getValue('id_change'), Tools::getValue('order_change'));
                Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
			}

			
    	$id_self = (int)Tools::getValue('id_faqcat');
		$order_self = Tools::getValue('order_self_faqcat');
		if($order_self){
				$obj_blockfaq->update_order_faqcat($id_self, $order_self, Tools::getValue('id_change_faqcat'), 
											 Tools::getValue('order_change_faqcat'));
                Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
			}
			
		if(Tools::isSubmit('submit_item_faq_cat')){
			
			
			$languages = Language::getLanguages(false);
	    	$data_title_content_lang = array();
	    	
        	if($this->_is15){
	    		$faq_shop_association = Tools::getValue("faq_shop_association");
	    	} else{
	    		$faq_shop_association = array(0=>0);
	    	}
	    	$cat_status = Tools::getValue("faq_cat_status");
	    	$faq_questions_association = Tools::getValue("faq_questions_association");
	    	
	    	foreach ($languages as $language){
	    		$id_lang = $language['id_lang'];
	    		$title = Tools::getValue("titlecat_".$id_lang);
	    		
	    		if(Tools::strlen($title)>0 && !empty($faq_shop_association))
	    		{
	    			$data_title_content_lang[$id_lang] = array('title' => $title,
	    									 				    );		
	    		}
	    	}
	    	
        	$data = array( 'data_title_content_lang'=>$data_title_content_lang,
        				   'item_status' => $cat_status,
        				   'faq_shop_association' => $faq_shop_association,
        				   'faq_questions_association'=>$faq_questions_association
         				  );
         				  
         	if(sizeof($data_title_content_lang)>0)
         		$obj_blockfaq->saveItemCategory($data);
        		
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
		}	
		
		
		if(Tools::isSubmit('update_item_faq_cat')){
        	
        	
        	$id = Tools::getValue("id");
     		
        	$languages = Language::getLanguages(false);
	    	$data_title_content_lang = array();
        	if($this->_is15){
	    		$faq_shop_association = Tools::getValue("faq_shop_association");
	    	} else{
	    		$faq_shop_association = array(0=>0);
	    	}
	    	
	    	foreach ($languages as $language){
	    		$id_lang = $language['id_lang'];
	    		$title = Tools::getValue("titlecat_".$id_lang);
	    		
	    		if(Tools::strlen($title)>0 && !empty($faq_shop_association))
	    		{
	    			$data_title_content_lang[$id_lang] = array('title' => $title
	    									 				    );		
	    		}
	    	}
        	
         	$cat_status = Tools::getValue("faq_cat_status");
         	$faq_questions_association = Tools::getValue("faq_questions_association");
	    	
         	$data = array('data_title_content_lang'=>$data_title_content_lang,
        				  'id' => $id,
         				  'item_status' => $cat_status,
         				  'faq_shop_association' => $faq_shop_association,
         				  'faq_questions_association' => $faq_questions_association
         				  );
         	if(sizeof($data_title_content_lang)>0)
         		$obj_blockfaq->updateItemCategory($data);
        	
          	Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
			
        }
        
   		 if (Tools::isSubmit("delete_item_faqcat")) {
			if (Validate::isInt(Tools::getValue("id"))) {
				
				$data = array('id' => Tools::getValue("id"));
				$obj_blockfaq->deleteItemCategory($data);
				
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
			}
			
		}
		
    	if (Tools::isSubmit('cancel_item_faq_cat'))
        {
        	Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
		}
		
		
		
		
		if (Tools::isSubmit('submit_item'))
        {
        	
        	$languages = Language::getLanguages(false);
	    	$data_title_content_lang = array();
	    	
        	if($this->_is15){
	    		$faq_shop_association = Tools::getValue("faq_shop_association");
	    	} else{
	    		$faq_shop_association = array(0=>0);
	    	}
	    	
	    	
	    	$faq_item_status = Tools::getValue("faq_item_status");
	    	$is_by_customer = Tools::getValue("is_by_customer");
	    	$faq_customer_email = Tools::getValue("faq_customer_email");
	    	$faq_customer_name = Tools::getValue("faq_customer_name");
	    	$faq_category_association = Tools::getValue("faq_category_association");
	    	
	    	
	    	
	    	foreach ($languages as $language){
	    		$id_lang = $language['id_lang'];
	    		$title = Tools::getValue("title_".$id_lang);
	    		$content = Tools::getValue("content_".$id_lang);
	    		
	    		if(Tools::strlen($title)>0 && Tools::strlen($content)>0 && !empty($faq_shop_association))
	    		{
	    			$data_title_content_lang[$id_lang] = array('title' => $title,
	    									 				    'content' => $content
	    													    );		
	    		}
	    	}
	    	
        	$data = array( 'data_title_content_lang'=>$data_title_content_lang,
        				   'item_status' => $faq_item_status,
        				   'faq_shop_association' => $faq_shop_association,
        				   'is_by_customer' => $is_by_customer,
        				   'faq_customer_name' => $faq_customer_name,
        				   'faq_customer_email'=>$faq_customer_email,
        				   'faq_category_association'=>$faq_category_association
        				  
         				  );
         	if(sizeof($data_title_content_lang)>0)
         		$obj_blockfaq->saveItem($data);
        	
        	
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
		}
		
    	if (Tools::isSubmit("delete_item")) {
			if (Validate::isInt(Tools::getValue("id"))) {
				
				$data = array('id' => Tools::getValue("id"));
				$obj_blockfaq->deleteItem($data);
				
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
			}
			
		}
		
    	
       
    	if (Tools::isSubmit('cancel_item'))
        {
        	Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
		}
		
		if (Tools::isSubmit('update_item'))
        {
        	
        	$id = Tools::getValue("id");
     		
        	$languages = Language::getLanguages(false);
	    	$data_title_content_lang = array();
        	if($this->_is15){
	    		$faq_shop_association = Tools::getValue("faq_shop_association");
	    	} else{
	    		$faq_shop_association = array(0=>0);
	    	}
	    	
	    	$faq_item_status = Tools::getValue("faq_item_status");
	    	$is_by_customer = Tools::getValue("is_by_customer");
	    	$faq_customer_email = Tools::getValue("faq_customer_email");
	    	$faq_customer_name = Tools::getValue("faq_customer_name");
	    	$faq_category_association = Tools::getValue("faq_category_association");
	    	
	    	$is_add_by_customer = Tools::getValue("is_add_by_customer");
	    	
	    	
	    	foreach ($languages as $language){
	    		$id_lang = $language['id_lang'];
	    		$title = Tools::getValue("title_".$id_lang);
	    		$content = Tools::getValue("content_".$id_lang);
	    		
	    		if(Tools::strlen($title)>0 && Tools::strlen($content)>0 && !empty($faq_shop_association))
	    		{
	    			$data_title_content_lang[$id_lang] = array('title' => $title,
	    									 				    'content' => $content
	    													    );		
	    		}
	    	}
        	
         	
         	$data = array('data_title_content_lang'=>$data_title_content_lang,
        				  'id' => $id,
         				  'item_status' => $faq_item_status,
         				  'faq_shop_association' => $faq_shop_association,
         				  'is_by_customer' => $is_by_customer,
        				  'faq_customer_name' => $faq_customer_name,
        				  'faq_customer_email'=>$faq_customer_email,
        				  'faq_category_association'=>$faq_category_association,
         				  'is_add_by_customer' => $is_add_by_customer	
         				  );
         	if(sizeof($data_title_content_lang)>0)
         		$obj_blockfaq->updateItem($data);
         		
        	
         	Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
		
        }

        $faq_settingsset = Tools::getValue("faq_settingsset");
        if (Tools::strlen($faq_settingsset)>0) {
            $this->_html .= '<script>init_tabs(3);</script>';
        }
        
    	if (Tools::isSubmit('submit_faq'))
        {
        	 Configuration::updateValue($this->name.'faq_home', Tools::getValue('faq_home'));
        	 Configuration::updateValue($this->name.'faq_left', Tools::getValue('faq_left'));
        	 Configuration::updateValue($this->name.'faq_right', Tools::getValue('faq_right'));
        	 Configuration::updateValue($this->name.'faq_footer', Tools::getValue('faq_footer'));
        	 
        	 Configuration::updateValue($this->name.'faq_blc', Tools::getValue('faq_blc'));
        	 
        	 
        	 Configuration::updateValue($this->name.'is_urlrewrite', Tools::getValue('is_urlrewrite'));
        	
        	 Configuration::updateValue($this->name.'faqis_captcha', Tools::getValue('faqis_captcha'));
        	 
        	 
        	 Configuration::updateValue($this->name.'faqis_askform', Tools::getValue('faqis_askform'));
        	 
        	 
        	 Configuration::updateValue($this->name.'notifaq', Tools::getValue('notifaq'));
        	 Configuration::updateValue($this->name.'mailfaq', Tools::getValue('mailfaq'));

            Configuration::updateValue($this->name.'faq_spm', Tools::getValue('faq_spm'));

            if(version_compare(_PS_VERSION_, '1.6', '>')) {
                $url = $currentIndex . '&conf=6&tab=AdminModules&faq_settingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
                Tools::redirectAdmin($url);
            }
        	
        }


        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->addBackOfficeMedia();
        } else {
            $this->_html .= $this->_jsandcss();
        }


        if(version_compare(_PS_VERSION_, '1.6', '>')){

            $this->_html .= $this->_displayForm16();
        } else {
            $this->_html .= $this->_displayForm13_14_15();
        }


        return $this->_html;
    	
    }



    private function _displayForm16(){

        $this->_html .= '<div class="row">
    	<div class="col-lg-12">
    	<div class="row">';

        $this->_html .= '<div class="col-lg-12 col-md-3">

						<div class="list-group">';
        $this->_html .= '<ul class="nav nav-pills" id="navtabs16">

							    <li class="active"><a href="#welcome" data-toggle="tab" class="list-group-item"><i class="fa fa-home fa-lg"></i>&nbsp;'.$this->l('Welcome').'</a></li>
							    <li><a href="#modulesettings" data-toggle="tab" class="list-group-item"><i class="fa fa-cogs fa-lg"></i>&nbsp;'.$this->l('Main Settings').'</a></li>
							    <li><a href="#info" data-toggle="tab" class="list-group-item"><i class="fa fa-question-circle fa-lg"></i>&nbsp;'.$this->l('Help / Documentation').'</a></li>
							    <li><a href="http://addons.prestashop.com/en/2_community-developer?contributor=61669" target="_blank"  class="list-group-item"><img src="../modules/'.$this->name.'/views/img/spm-logo.png"  />&nbsp;&nbsp;'.$this->l('Other SPM Modules').'</a></li>


							</ul>';
        $this->_html .= '</div>
    				</div>';


        $this->_html .= '<div class="tab-content col-lg-12 col-md-9">';
        $this->_html .= '<div class="tab-pane active" id="welcome">'.$this->_welcome().'</div>';
        $this->_html .= '<div class="tab-pane" id="modulesettings">'.$this->_modulesettings16().'</div>';
        $this->_html .= '<div class="tab-pane" id="info">'.$this->_help_documentation().'</div>';
        $this->_html .= '</div>';



        $this->_html .= '</div></div></div>';

    }

    private function _modulesettings16(){
        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $this->l('Main Settings'),
                    'icon' => 'fa fa-cogs fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable URL rewriting'),
                        'name' => 'is_urlrewrite',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),


                    array(
                        'type' => 'text',
                        'label' => $this->l('The number of items in the "Block FAQ":'),
                        'name' => 'faq_blc',
                        'class' => ' fixed-width-sm',

                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Ask a Question submit form'),
                        'name' => 'faqis_askform',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Captcha on submit form'),
                        'name' => 'faqis_captcha',

                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Admin email:'),
                        'name' => 'mailfaq',
                        'id' => 'mailfaq',
                        'lang' => FALSE,

                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('E-mail notification:'),
                        'name' => 'notifaq',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'notifaq')
                        ),
                    ),



                    array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Position "Block FAQ":'),
                        'name' => 'faq_left',
                        'values' => array(
                            'query' => array(

                                array(
                                    'id' => 'faq_left',
                                    'name' => $this->l('Left column'),
                                    'val' => 1
                                ),


                                array(
                                    'id' => 'faq_right',
                                    'name' => $this->l('Right column'),
                                    'val' => 1
                                ),


                                array(
                                    'id' => 'faq_footer',
                                    'name' => $this->l('Footer'),
                                    'val' => 1
                                ),

                                array(
                                    'id' => 'faq_home',
                                    'name' => $this->l('Home'),
                                    'val' => 1
                                ),

                                array(
                                    'id' => 'faq_spm',
                                    'name' => $this->l('FAQ in CUSTOM HOOK (faqSPM)'),
                                    'val' => 1
                                ),





                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),






                ),
            ),
        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Update Settings'),
                )
            ),
        );

        $helper = new HelperForm();



        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_faq';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesFaqSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        return  $helper->generateForm(array($fields_form,$fields_form1)).$this->_customhookhelp();
    }

    public function getConfigFieldsValuesFaqSettings(){

        $data_config = array(
            'is_urlrewrite' => Configuration::get($this->name.'is_urlrewrite'),

            'faq_blc'=> Configuration::get($this->name.'faq_blc'),

            'faqis_askform'=>Configuration::get($this->name.'faqis_askform'),

            'faqis_captcha'=>Configuration::get($this->name.'faqis_captcha'),

            'mailfaq'=>Configuration::get($this->name.'mailfaq'),
            'notifaq'=>Configuration::get($this->name.'notifaq'),

            'faq_left'=>Configuration::get($this->name.'faq_left'),
            'faq_right'=>Configuration::get($this->name.'faq_right'),
            'faq_footer'=>Configuration::get($this->name.'faq_footer'),
            'faq_home'=>Configuration::get($this->name.'faq_home'),
            'faq_spm'=>Configuration::get($this->name.'faq_spm'),

        );

        return $data_config;

    }

    private function _customhookhelp(){
        $_html  = '';

            $_html .= '<div class="panel">

		<div class="panel-heading"><i class="fa fa-question-circle fa-lg"></i>&nbsp;'.$this->l('Frequently Asked Questions').'</div>';

        if(version_compare(_PS_VERSION_, '1.5', '>')){

            $_html .= '<div class="row ">

                       ';

            $_html .= '<div class="span">
                          <p>
                             <span style="font-weight: bold; font-size: 15px;" class="question">
                             	- <b style="color:red">'.$this->l('CUSTOM HOOK HELP:').'</b> '.$this->l('How I can show FAQ questions block on a single page (CMS or other places for example) ?').'
                             </span>
                             <br/><br/>
                             <span style="color: black;" class="answer">
                             	   '.$this->l('You just need to add a line of code to the tpl file of the page where you want to add the FAQ questions block').':
                                   <br/><br/>
                                   <pre>{hook h=\'faqSPM\'}</pre>
                              </span>

                         </p>
                       </div><br/><br/>';
            $_html .= '</div>';
        }




            $_html .= '</div>';

        return $_html;
    }


    private function _drawSettingsFAQ(){
    	
    	$_html = '';
		$_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" >';
		
		$_html .= '<style type="text/css">
    				.table-settings{width:100%}
    				.table-settings tr td{padding:3px}
    				</style>';	
		
		$_html .= '
		<fieldset>
					<legend><img src="../modules/'.$this->name.'/logo.gif"  />
					'.$this->displayName.'</legend>';
		
		$_html .= '<table class="table-settings">';
    	
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:35%;padding:0 20px 0 0">';
    	
    	$_html .= '<b>'.$this->l('Enable or Disable URL rewriting').':</b>';
    	
    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="radio" value="1" id="text_list_on" name="is_urlrewrite" onclick="enableOrDisableTools(1)"
							'.(Tools::getValue('is_urlrewrite', Configuration::get($this->name.'is_urlrewrite')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_urlrewrite" onclick="enableOrDisableTools(0)"
						   '.(!Tools::getValue('is_urlrewrite', Configuration::get($this->name.'is_urlrewrite')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
				';
		
		
		$_html .= '<script type="text/javascript">
			    	function enableOrDisableTools(id)
						{
						if(id==0){
							$("#block-tools-settings").hide(200);
						} else {
							$("#block-tools-settings").show(200);
						}
							
						}
					</script>';
		$_html .= '</td>';
		$_html .= '</tr>';
    	
		
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:35%;padding:0 20px 0 0">';
    	
    	$_html .= '<b>'.$this->l('The number of items in the "Block FAQ":').'</b>';
    	
    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="text" name="faq_blc"  
			               value="'.Tools::getValue('faq_blc', Configuration::get($this->name.'faq_blc')).'"
			               >
				';
		$_html .= '</td>';
		$_html .= '</tr>';
		
		
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';
    	
    	$_html .= '<b>'.$this->l('Enable Ask a Question submit form').':</b>';
    	
    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="radio" value="1" id="text_list_on" name="faqis_askform"
							'.(Tools::getValue('faqis_askform', Configuration::get($this->name.'faqis_askform')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="faqis_askform"
						   '.(!Tools::getValue('faqis_askform', Configuration::get($this->name.'faqis_askform')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
				';
		$_html .= '</td>';
		$_html .= '</tr>';
		
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';
    	
    	$_html .= '<b>'.$this->l('Enable Captcha on submit form').':</b>';
    	
    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="radio" value="1" id="text_list_on" name="faqis_captcha"
							'.(Tools::getValue('faqis_captcha', Configuration::get($this->name.'faqis_captcha')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="faqis_captcha"
						   '.(!Tools::getValue('faqis_captcha', Configuration::get($this->name.'faqis_captcha')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
				';
		$_html .= '</td>';
		$_html .= '</tr>';
		
		
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';
    	
    	$_html .= '<b>'.$this->l('Admin email:').'</b>';
    	
    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="text" name="mailfaq"  size="40"
			               value="'.Tools::getValue('mailfaq', Configuration::get($this->name.'mailfaq')).'"
			               >
				';
		$_html .= '</td>';
		$_html .= '</tr>';
		
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';
    	
    	$_html .= '<b>'.$this->l('E-mail notification:').'</b>';
    	
    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .= '<input type = "checkbox" name = "notifaq" id = "notifaq" value ="1" '.((Tools::getValue($this->name.'notifaq', Configuration::get($this->name.'notifaq')) ==1)?'checked':'').'/>';
		$_html .= '</td>';
		$_html .= '</tr>';
		
		$_html .= '<tr>';
    	$_html .= '<td colspan=2>&nbsp;</td>';
		$_html .= '</tr>';
		
		####
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:40%;padding:0 20px 0 0">';
    	
		$_html .= '<b>'.$this->l('Position "Block FAQ":').'</b>';
		
		$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		
				//$_html .= '<div class="margin-form choose_hooks">';
	    		$_html .= '<table style="width:66%;">
	    				<tr>
	    					<td style="width: 33%">'.$this->l('Left Column').'</td>
	    					<td style="width: 33%">'.$this->l('Right Column').'</td>
	    					
	    				</tr>
	    				<tr>
	    					<td>
	    						<input type="checkbox" name="faq_left" '.((Tools::getValue($this->name.'faq_left', Configuration::get($this->name.'faq_left')) ==1)?'checked':'').'  value="1"/>
	    					</td>
	    					<td>
	    						<input type="checkbox" name="faq_right" '.((Tools::getValue($this->name.'faq_right', Configuration::get($this->name.'faq_right')) ==1)?'checked':'') .' value="1"/>
	    					</td>
	    					
	    				</tr>
	    				<tr>
	    					<td>'.$this->l('Footer').'</td>
	    					<td>'.$this->l('Home').'</td>
	    					
	    				</tr>
	    				<tr>
	    					<td>
	    						<input type="checkbox" name="faq_footer" '.((Tools::getValue($this->name.'faq_footer', Configuration::get($this->name.'faq_footer')) ==1)?'checked':'').' value="1"/>
	    					</td>
	    					<td>
	    						<input type="checkbox" name="faq_home" '.((Tools::getValue($this->name.'faq_home', Configuration::get($this->name.'faq_home')) ==1)?'checked':'').' value="1"/>
	    					</td>
	    					
	    				</tr>
	    				
	    			</table>';
	    //$_html .= '</div>';
		$_html .= '</td>';
		$_html .= '</tr>';
				
		
		
    	$_html .= '</table>';
			
			$_html .= '<p class="center" style="border: 1px solid #EBEDF4; padding: 10px; margin-top: 10px;">
					<input type="submit" name="submit_faq" value="'.$this->l('Update settings').'" 
                		   class="button"  />
                	</p>';
					
					
		$_html .= '</fieldset>	';
		$_html .= '</form>';
		
		$_html .= '<br/><br/>';
    	
    	$_html .= '<div id="block-tools-settings" '.(Configuration::get($this->name.'is_urlrewrite')==1?'style="display:block"':'style="display:none"').'>';
    	
    	$_html .= '<fieldset>
					<legend>'.$this->l('Tools').'</legend>';
    	$_html .= $this->_hint();
    	$_html .= '</fieldset>';
    	$_html .= '</div>';
		
		return $_html;
		
    }
    
private function _hint(){
    	$_html = '';
    	
    	$_html .= '<p style="display: block; font-size: 11px; width: 95%; margin-bottom:20px;position:relative" class="hint clear">
    	<b style="color:#585A69">'.$this->l('If url rewriting doesn\'t works, check that this above lines exist in your current .htaccess file, if no, add it manually on top of your .htaccess file').':</b>
    	<br/><br/>
    	<code>
		RewriteRule ^(.*)faq/?$ /modules/'.$this->name.'/faq.php [QSA,L] 
	    </code>
		
			<br/><br/>
		</p>';
    	
    	return $_html;
    }
    
    public function drawFAQCategories($data=null){
    	$cookie = $this->context->cookie;


     	
    	$currentIndex = isset($data['currentindex'])?$data['currentindex']:$this->context->currentindex;
    	$controller = isset($data['controller'])?$data['controller']:'AdminModules';
    	$token = isset($data['token'])?$data['token']:Tools::getAdminToken($controller.(int)(Tab::getIdFromClassName($controller)).(int)($cookie->id_employee));
    	
    	
     	$_html = '';
		
		
		//faq operations
		
		$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/logo.gif" />
					'.$this->l('Moderate FAQ Categories').'</legend>';
		
		
		
		include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
		$obj_blockfaq = new blockfaqhelp();
		
		
		if(Tools::isSubmit("edit_item_faqcat")){
			$divLangName = "titlecat";
			$_data = $obj_blockfaq->getCategoryItem(array('id'=>(int)Tools::getValue("id"),'admin'=>1));
			
			$data_content = isset($_data['item']['data'])?$_data['item']['data']:'';
			$status = isset($_data['item'][0]['status'])?$_data['item'][0]['status']:'';
			
			$faq_questions_association = isset($_data['item'][0]['faq_questions_ids'])?$_data['item'][0]['faq_questions_ids']:array();
			//var_dump($faq_questions_association);
			$_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
    		
    		$_html .= '<label>'.$this->l('Category title').':</label>
    					<div class="margin-form">';
			
    		
    		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	    	$languages = Language::getLanguages(false);
	    	
	    	foreach ($languages as $language){
			$id_lng = (int)$language['id_lang'];
	    	$title = isset($data_content[$id_lng]['title'])?$data_content[$id_lng]['title']:"";
			
			$_html .= '	<div id="titlecat_'.$language['id_lang'].'" 
							 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
							 >

						<input type="text" style="width:400px"   
								  id="titlecat_'.$language['id_lang'].'" 
								  name="titlecat_'.$language['id_lang'].'" 
								  value="'.htmlentities(Tools::stripslashes($title), ENT_COMPAT, 'UTF-8').'"/>
						</div>';
	    	}
			ob_start();
			$this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlecat');
			$displayflags = ob_get_clean();
			$_html .= $displayflags;
    		$_html .= '<div style="clear:both"></div>';
    		
    		$_html .= '</div>';
    		
    		
    		
			$_data_cat = $obj_blockfaq->getItems();
			
	    	if(sizeof($_data_cat['items'])){
	    		
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Select Questions (optional)').':</label>';
	    	
	    	$_html .=  '<div class="margin-form">';
	    	
	    	$_html .= '<select name="faq_questions_association[]" multiple size="10">';
	    	foreach($_data_cat['items'] as $item){
	    		$_html .= '<option value='.$item['id'].' '.(in_array($item['id'],$faq_questions_association)?'selected="true"':'').'>'.$item['title'].'</option>';	
	    	}
	    	
	    	$_html .= '</select>';
	    	
	    	$_html .= '</div>';
	    	
	    	}
	    	
	    	if($this->_is15){
	    	// shop association
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Shop association').':</label>';
	    	$_html .= '<div class="margin-form">';
	
			$_html .= '<table width="50%" cellspacing="0" cellpadding="0" class="table">
							<tr>
								<th>Shop</th>
							</tr>';
			$u = 0;
			
			$shops = Shop::getShops();
			$shops_tmp = explode(",",isset($_data['item'][0]['ids_shops'])?$_data['item'][0]['ids_shops']:"");
			
			$count_shops = sizeof($shops);
			foreach($shops as $_shop){
				$id_shop = $_shop['id_shop'];
				$name_shop = $_shop['name'];
				 $_html .= '<tr>
							<td>
								<img src="../img/admin/lv2_'.((($count_shops-1)==$u)?"f":"b").'.png" alt="" style="vertical-align:middle;">
								<label class="child">';
			 
				
					$_html .= '<input type="checkbox"  
									   name="faq_shop_association[]" 
									   value="'.$id_shop.'" '.((in_array($id_shop,$shops_tmp))?'checked="checked"':'').' 
									   class="input_shop" 
									   />
									'.$name_shop.'';
					
					$_html .= '</label>
							</td>
						</tr>';
			 $u++;
			}
		
			$_html .= '</table>';
				
			$_html .= '</div>';
																	
	    	}
	    	// shop association
	    	
	    	
	    	
	    	
	    	$_html .= '<label>'.$this->l('Status').'</label>
				<div class = "margin-form">';
				
			$_html .= '<select name="faq_cat_status" style="width:100px">
						<option value=1 '.(($status==1)?"selected=\"true\"":"").'>'.$this->l('Enabled').'</option>
						<option value=0 '.(($status==0)?"selected=\"true\"":"").'>'.$this->l('Disabled').'</option>
					   </select>';
			
				
			$_html .= '</div>';
    	
    		$_html .= '<label>&nbsp;</label>
						<div class = "margin-form"  style="margin-top:10px">
						<input type="submit" name="cancel_item_faq_cat" value="'.$this->l('Cancel').'" 
                		   class="button"  />&nbsp;&nbsp;&nbsp;
						<input type="submit" name="update_item_faq_cat" value="'.$this->l('Update').'" 
                		   class="button"  />
                		  </div>';
    		
    		$_html .= '</form>';
    		
		}else{
			$divLangName = "titlecat";
			$status = 1;
			$name = "";
			
			
    		$_html .= '<a href="javascript:void(0)" onclick="$(\'#add-cat-form\').show(200);$(\'#link-add-cat-form\').hide(200)"
    					id="link-add-cat-form"	
					  style="width:100%;border: 1px solid rgb(222, 222, 222); padding: 5px; margin-bottom: 10px; display: block; font-size: 16px; color: maroon; text-align: center; font-weight: bold; text-decoration: underline;"
					  >'.$this->l('Add New FAQ Category').'</a>';
    		
    		$_html .= '<div style="border: 1px solid rgb(222, 222, 222);padding-top:10px;display:none" id="add-cat-form">';
			$_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
    		
    		$_html .= '<label>'.$this->l('Category title').':</label>
    					<div class="margin-form">';
			
    		
    		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	    	$languages = Language::getLanguages(false);
	    	
	    	foreach ($languages as $language){
			$id_lng = (int)$language['id_lang'];
	    	
			$_html .= '	<div id="titlecat_'.$language['id_lang'].'" 
							 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
							 >

						<input type="text" style="width:400px"   
								  id="titlecat_'.$language['id_lang'].'" 
								  name="titlecat_'.$language['id_lang'].'" 
								  value="'.htmlentities(Tools::stripslashes($name), ENT_COMPAT, 'UTF-8').'"/>
						</div>';
	    	}
			ob_start();
			$this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlecat');
			$displayflags = ob_get_clean();
			$_html .= $displayflags;
			$_html .= '<div style="clear:both"></div>';
    		
    		$_html .= '</div>';
    		
    		
			$_data_cat = $obj_blockfaq->getItems();
	    	if(sizeof($_data_cat['items'])){
	    		
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Select Questions (optional)').':</label>';
	    	
	    	$_html .=  '<div class="margin-form">';
	    	
	    	$_html .= '<select name="faq_questions_association[]" multiple size="10">';
	    	foreach($_data_cat['items'] as $item){
	    		$_html .= '<option value='.$item['id'].'>'.$item['title'].'</option>';	
	    	}
	    	
	    	$_html .= '</select>';
	    	
	    	$_html .= '</div>';
	    	
	    	}
    		
	    	
	    	if($this->_is15){
	    	// shop association
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Shop association').':</label>';
	    	$_html .= '<div class="margin-form">';
	
			$_html .= '<table width="50%" cellspacing="0" cellpadding="0" class="table">
							<tr>
								<th>Shop</th>
							</tr>';
			$u = 0;
			
			$shops = Shop::getShops();
			$shops_tmp = explode(",",isset($_data['item'][0]['ids_shops'])?$_data['item'][0]['ids_shops']:"");
			
			$count_shops = sizeof($shops);
			foreach($shops as $_shop){
				$id_shop = $_shop['id_shop'];
				$name_shop = $_shop['name'];
				 $_html .= '<tr>
							<td>
								<img src="../img/admin/lv2_'.((($count_shops-1)==$u)?"f":"b").'.png" alt="" style="vertical-align:middle;">
								<label class="child">';
			 
				
					$_html .= '<input type="checkbox"  
									   name="faq_shop_association[]" 
									   value="'.$id_shop.'" '.((in_array($id_shop,$shops_tmp))?'checked="checked"':'').' 
									   class="input_shop" 
									   />
									'.$name_shop.'';
					
					$_html .= '</label>
							</td>
						</tr>';
			 $u++;
			}
		
			$_html .= '</table>';
				
			$_html .= '</div>';
																	
	    	}
	    	// shop association
	    	
	    	
	    	$_html .= '<label>'.$this->l('Status').'</label>
				<div class = "margin-form">';
				
			$_html .= '<select name="faq_cat_status" style="width:100px">
					<option value=1 '.(($status==1)?"selected=\"true\"":"").'>'.$this->l('Enabled').'</option>
					<option value=0 '.(($status==0)?"selected=\"true\"":"").'>'.$this->l('Disabled').'</option>
				   </select>';
			
				
			$_html .= '</div>';
    	
    		$_html .= '<label>&nbsp;</label>
						<div class = "margin-form"  style="margin-top:10px">
						<input type="button" value="'.$this->l('Cancel').'" 
                		   class="button"  
                		   onclick="$(\'#link-add-cat-form\').show(200);$(\'#add-cat-form\').hide(200);" 
                		   />&nbsp;&nbsp;&nbsp;
						<input type="submit" name="submit_item_faq_cat" value="'.$this->l('Save').'" 
                		   class="button"  />
                		  </div>';
    		
    		$_html .= '</form>';
    		$_html .= '</div>';
		
    		$_html .= '<br/>';
    		
			$_html .= '<table class = "table" width = 100%>
			<tr>
				<th width=50>'.$this->l('ID').'</th>
				<th width=100>'.$this->l('Lang').'</th>';
			
		
    		if($this->_is15){
    			$_html .= '<th width = 100>'.$this->l('Shop').'</th>';
    		}
    		
    		
			$_html .= '<th>'.$this->l('Category Title').'</th>';
			
			$_html .= '<th width=150>'.$this->l('Number of questions').'</th>';
    		
			
			$_html .= '<th width=100>'.$this->l('Position').'</th>';
			
			
			
			$_html .= '<th width = "50">'.$this->l('Status').'</th>
				<th width = "50">'.$this->l('Action').'</th>
			</tr>';
			$_data = $obj_blockfaq->getItemsCategory(array('admin'=>1));
			$_items = $_data['items'];
			$count_stickers =  sizeof($_items);
			if($count_stickers>0){
				$i=0;
				foreach($_items as $_item){
                    $sticker = $_items[$i];
					$id = $_item['id'];
					$title = $_item['title'];
					$status = $_items[$i]['status'];
					
					$count_faq = $_items[$i]['count_faq'];
					
					if($this->_is15){

						$id_shop = $_item['ids_shops'];
						$id_shop = explode(",",$id_shop);
						$shops = Shop::getShops();
						$name_shop = array();
						foreach($shops as $_shop){
							$id_shop_lists = $_shop['id_shop'];
							if(in_array($id_shop_lists,$id_shop))
								$name_shop[] = $_shop['name'];
						}
						$name_shop = implode(",",$name_shop);
					}
					
					$ids_lng = isset($_item['ids_lng'])?$_item['ids_lng']:array();
					$lang_for_faq = array();
					foreach($ids_lng as $lng_id){
						$data_lng = Language::getLanguage($lng_id);
						$lang_for_faq[] = $data_lng['iso_code']; 
					}
					$lang_for_faq = implode(",",$lang_for_faq);
					
					$_html .= 
						'<tr>
						<td style = "color:black;">'.$id.'</td>';
					$_html .= '<td style = "color:black;">'.$lang_for_faq.'</td>';
					
					if($this->_is15){
						$_html .= '<td style="color:black">'.$name_shop.'</td>';
					}
					
					$_html .= '<td style = "color:black;">'.$title.'</td>';
					
					$_html .= '<td style = "color:black;">'.$count_faq.'</td>';
					
					
					$_html .= '<td style = "color:black;">';
					if($i < $count_stickers - 1):
				$_html	.= '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&id_faqcat=' . $id . '&order_self_faqcat=' . $sticker['order_by'] . '&id_change_faqcat='. $_items[$i+1]['id'] . '&order_change_faqcat=' . $_items[$i+1]['order_by'].'">
								<img border="0" src="'.__PS_BASE_URI__.'img/admin/down.gif">
							</a>';
 					endif;
				if($i > 0):
				$_html	.= '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&id_faqcat=' .$id . '&order_self_faqcat=' . $sticker['order_by'] . '&id_change_faqcat='. $_items[$i-1]['id'] . '&order_change_faqcat=' . $_items[$i-1]['order_by'] .'">
								<img border="0" src="'.__PS_BASE_URI__.'img/admin/up.gif">
							</a>';
				endif;
					
					$_html .= '</td>';
					
					
				if($status)
					$_html .= '<td><img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif"></td>';
				else
					$_html .= '<td><img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif"></td>';
					
					
					$_html .= '<td>
				
								 <input type = "hidden" name = "id" value = "'.$id.'"/>
								 <a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&edit_item_faqcat&id='.(int)($id).'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a> 
								 <a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&delete_item_faqcat&id='.(int)($id).'" title="'.$this->l('Delete').'"  onclick = "javascript:return confirm(\''.$this->l('Are you sure you want to remove this item?').'\');"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>'; 
								 $_html .= '</form>
							 </td>';
					$_html .= '</tr>';
					$i++;
				}
			} else {
			$_html .= '<tr><td colspan="7" style="text-align:center;font-weight:bold;padding:10px">
						'.$this->l('FAQ Categories not found').'</td></tr>';	
			}
			
			$_html .= '</table>';
		}
			
			
		
		$_html .=	'</fieldset>'; 
		
		
     	return $_html;
    }
    
    public function drawFaqItems($data = null){
    	$cookie = $this->context->cookie;
    	
    	

     	
    	$currentIndex = isset($data['currentindex'])?$data['currentindex']:$this->context->currentindex;
    	$controller = isset($data['controller'])?$data['controller']:'AdminModules';
    	$token = isset($data['token'])?$data['token']:Tools::getAdminToken($controller.(int)(Tab::getIdFromClassName($controller)).(int)($cookie->id_employee));
    	
    	
     	$_html = '';
		
		
		//faq operations
		
		$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/logo.gif" />
					'.$this->l('Moderate Questions').'</legend>';
		
		
		
		include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
		$obj_blockfaq = new blockfaqhelp();
		
		
		if(Tools::isSubmit("edit_item")){
			$divLangName = "ccontent¤title";
			
			$_data = $obj_blockfaq->getItem(array('id'=>(int)Tools::getValue("id")));
			
			$data_content = isset($_data['item']['data'])?$_data['item']['data']:'';
			
			$status = isset($_data['item'][0]['status'])?$_data['item'][0]['status']:'';
			
			$is_by_customer = isset($_data['item'][0]['is_by_customer'])?$_data['item'][0]['is_by_customer']:0;
			
			$is_add_by_customer = isset($_data['item'][0]['is_add_by_customer'])?$_data['item'][0]['is_add_by_customer']:0;
			
			$customer_email = isset($_data['item'][0]['customer_email'])?$_data['item'][0]['customer_email']:$this->_admin_email;
			$customer_name = isset($_data['item'][0]['customer_name'])?$_data['item'][0]['customer_name']:$this->l('admin');
			
			$faq_category_association = isset($_data['item'][0]['faq_category_ids'])?$_data['item'][0]['faq_category_ids']:array();

			$_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
    		$_html .= '<input type="hidden" value="'.$is_add_by_customer.'" name="is_add_by_customer"/>';
    		$_html .= '<label>'.$this->l('Question:').'</label>
    					<div class="margin-form">';
			
    		
    		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	    	$languages = Language::getLanguages(false);
	    	
	    	foreach ($languages as $language){
			$id_lng = (int)$language['id_lang'];
	    	$title = isset($data_content[$id_lng]['title'])?$data_content[$id_lng]['title']:"";
			
			$_html .= '	<div id="title_'.$language['id_lang'].'" 
							 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
							 >

						<input type="text" style="width:400px"   
								  id="title_'.$language['id_lang'].'" 
								  name="title_'.$language['id_lang'].'" 
								  value="'.htmlentities(Tools::stripslashes($title), ENT_COMPAT, 'UTF-8').'"/>
						</div>';
	    	}
			ob_start();
			$this->displayFlags($languages, $defaultLanguage, $divLangName, 'title');
			$displayflags = ob_get_clean();
			$_html .= $displayflags;
    		$_html .= '<div style="clear:both"></div>';
    		
    		$_html .= '</div>';
    		
    		if(defined('_MYSQL_ENGINE_')){
	    	$_html .= '<label>'.$this->l('Answer:').'</label>
	    					<div class="margin-form" >';
			
	    	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	    	$languages = Language::getLanguages(false);
	    	
	    	foreach ($languages as $language){
	    	$id_lng = (int)$language['id_lang'];
	    	$content = isset($data_content[$id_lng]['content'])?$data_content[$id_lng]['content']:"";
			
			$_html .= '	<div id="ccontent_'.$language['id_lang'].'" 
							 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;';
							 
	    	if($this->_is16 == 1){ $_html .= 'width:80%;'; }
	    					 
			$_html .= ' "
							 >

						<textarea class="rte" cols="25" rows="10"';
			if($this->_is16 == 0){
				$_html .= 'style="width:400px"';
			} 
			$_html .= '   
								  id="content_'.$language['id_lang'].'" 
								  name="content_'.$language['id_lang'].'">'.htmlentities(Tools::stripslashes($content), ENT_COMPAT, 'UTF-8').'</textarea>

					</div>';
	    	}
	    	
			ob_start();
			$this->displayFlags($languages, $defaultLanguage, $divLangName, 'ccontent');
			$displayflags = ob_get_clean();
			$_html .= $displayflags;
	    	
			$_html .= '<div style="clear:both"></div>';
			
	    	
	    	$_html .= '</div>
						';
	    	}else{
	    		$_html .= '<label>'.$this->l('Answer').'</label>
	    					<div class="margin-form">';
				
	    		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		    	$languages = Language::getLanguages(false);
		    	$divLangName = "ccontent";
		    	
		    	foreach ($languages as $language){
				$id_lng = (int)$language['id_lang'];
	    		$content = isset($data_content[$id_lng]['content'])?$data_content[$id_lng]['content']:"";
			
				$_html .= '	<div id="ccontent_'.$language['id_lang'].'" 
								 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
								 >
	
							<textarea class="rte" cols="25" rows="10" style="width:400px"   
									  id="content_'.$language['id_lang'].'" 
									  name="content_'.$language['id_lang'].'">'.htmlentities(Tools::stripslashes($content), ENT_COMPAT, 'UTF-8').'</textarea>
	
						</div>';
		    	}
				ob_start();
				$this->displayFlags($languages, $defaultLanguage, $divLangName, 'ccontent');
				$displayflags = ob_get_clean();
				$_html .= $displayflags;
		    	
				$_html .= '<div style="clear:both"></div>';
	    		
	    		$_html .= '</div>
						';
	    	}
	    	
	    	
			$_data_cat = $obj_blockfaq->getItemsCategory(array('admin'=>1));
	    	if(sizeof($_data_cat['items'])){
	    		
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Category association').':</label>';
	    	
	    	$_html .=  '<div class="margin-form">';
	    	
			
	    	$_html .= '<select name="faq_category_association[]" multiple size="10">';
	    	$_html .= '<option value=0>---</option>';	
	    	foreach($_data_cat['items'] as $item){
	    		
	    		
	    		$_html .= '<option value='.$item['id'].' '.(in_array($item['id'],$faq_category_association)?'selected="true"':'').'>'.$item['title'].'</option>';	
	    	}
	    	
	    	$_html .= '</select>';
	    	
	    	$_html .= '</div>';
	    	
	    	}
	    	
	    	if($this->_is15){
	    	// shop association
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Shop association').':</label>';
	    	$_html .= '<div class="margin-form">';
	
			$_html .= '<table width="50%" cellspacing="0" cellpadding="0" class="table">
							<tr>
								<th>Shop</th>
							</tr>';
			$u = 0;
			
			$shops = Shop::getShops();
			$shops_tmp = explode(",",isset($_data['item'][0]['ids_shops'])?$_data['item'][0]['ids_shops']:"");
			
			$count_shops = sizeof($shops);
			foreach($shops as $_shop){
				$id_shop = $_shop['id_shop'];
				$name_shop = $_shop['name'];
				 $_html .= '<tr>
							<td>
								<img src="../img/admin/lv2_'.((($count_shops-1)==$u)?"f":"b").'.png" alt="" style="vertical-align:middle;">
								<label class="child">';
			 
				
					$_html .= '<input type="checkbox"  
									   name="faq_shop_association[]" 
									   value="'.$id_shop.'" '.((in_array($id_shop,$shops_tmp))?'checked="checked"':'').' 
									   class="input_shop" 
									   />
									'.$name_shop.'';
					
					$_html .= '</label>
							</td>
						</tr>';
			 $u++;
			}
		
			$_html .= '</table>';
				
			$_html .= '</div>';
																	
	    	}
	    	// shop association
	    	
	    	$_html .= '<label>'.$this->l('Show By customer').':</label>';
	    	
	    	$_html .=  '<div class="margin-form">
	    	
					<input type="radio" value="1" id="text_list_on" name="is_by_customer"
							'.($is_by_customer ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_by_customer" 
						   '.(!$is_by_customer ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
				</div>';
	    	
	    	$_html .= '<label>'.$this->l('Customer email').':</label>
    			
    				<div class="margin-form">
					<input style="width:400px" type="text" name="faq_customer_email" size="128" value="'.$customer_email.'">
			        
			       </div>';
	    	
	    	
	    	$_html .= '<label>'.$this->l('Customer name').':</label>
    			
    				<div class="margin-form">
					<input style="width:400px" type="text" name="faq_customer_name" size="128" value="'.$customer_name.'">
			        
			       </div>';
	    	
	    	$_html .= '<label>'.$this->l('Status').'</label>
				<div class = "margin-form">';
				
			$_html .= '<select name="faq_item_status" style="width:100px">
						<option value=1 '.(($status==1)?"selected=\"true\"":"").'>'.$this->l('Enabled').'</option>
						<option value=0 '.(($status==0)?"selected=\"true\"":"").'>'.$this->l('Disabled').'</option>
					   </select>';
			
				
			$_html .= '</div>';
    	
    		$_html .= '<label>&nbsp;</label>
						<div class = "margin-form"  style="margin-top:10px">
						<input type="submit" name="cancel_item" value="'.$this->l('Cancel').'" 
                		   class="button"  />&nbsp;&nbsp;&nbsp;
						<input type="submit" name="update_item" value="'.$this->l('Update').'" 
                		   class="button"  />
                		  </div>';
    		
    		$_html .= '</form>';
    		
		}else{
			$divLangName = "ccontent¤title";
			$name = "";
			$content = "";
			$status = 1;
			
			$customer_email = $this->_admin_email;
			$customer_name = $this->l('admin');
			$is_by_customer = 0;
			
			
    		$_html .= '<a href="javascript:void(0)" onclick="$(\'#add-question-form\').show(200);$(\'#link-add-question-form\').hide(200)"
    					id="link-add-question-form"	
					  style="width:100%;border: 1px solid rgb(222, 222, 222); padding: 5px; margin-bottom: 10px; display: block; font-size: 16px; color: maroon; text-align: center; font-weight: bold; text-decoration: underline;"
					  >'.$this->l('Add New Question').'</a>';
    		
    		$_html .= '<div style="border: 1px solid rgb(222, 222, 222);padding-top:10px;display:none" id="add-question-form">';
			$_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
    		
    		$_html .= '<label>'.$this->l('Question').':</label>
    					<div class="margin-form">';
			
    		
    		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	    	$languages = Language::getLanguages(false);
	    	
	    	foreach ($languages as $language){
			$id_lng = (int)$language['id_lang'];
	    	
			$_html .= '	<div id="title_'.$language['id_lang'].'" 
							 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
							 >

						<input type="text" style="width:400px"   
								  id="title_'.$language['id_lang'].'" 
								  name="title_'.$language['id_lang'].'" 
								  value="'.htmlentities(Tools::stripslashes($name), ENT_COMPAT, 'UTF-8').'"/>
						</div>';
	    	}
			ob_start();
			$this->displayFlags($languages, $defaultLanguage, $divLangName, 'title');
			$displayflags = ob_get_clean();
			$_html .= $displayflags;
			$_html .= '<div style="clear:both"></div>';
    		
    		$_html .= '</div>';
    		
    		if(defined('_MYSQL_ENGINE_')){
	    	$_html .= '<label>'.$this->l('Answer').':</label>
	    					<div class="margin-form" >';
							
	    	
	    	
	    		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	    	$languages = Language::getLanguages(false);
	    	
	    	foreach ($languages as $language){

			$_html .= '	<div id="ccontent_'.$language['id_lang'].'" 
							 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;';
							 
	    	if($this->_is16 == 1){ $_html .= 'width:80%;'; }				 
			
	    	$_html .= '"
							 >

						<textarea class="rte" cols="25" rows="10"';
			if($this->_is16 == 0){
				$_html .= 'style="width:400px"';
			} 
	    	$_html .= '   
								  id="content_'.$language['id_lang'].'" 
								  name="content_'.$language['id_lang'].'">'.htmlentities(Tools::stripslashes($content), ENT_COMPAT, 'UTF-8').'</textarea>

					</div>';
	    	}
			ob_start();
			$this->displayFlags($languages, $defaultLanguage, $divLangName, 'ccontent');
			$displayflags = ob_get_clean();
			$_html .= $displayflags;
			$_html .= '<div style="clear:both"></div>';
	    	
	    	$_html .= '</div>
						';
	    	}else{
	    		$_html .= '<label>'.$this->l('Answer').'</label>
	    					<div class="margin-form">';
	    		
				
	    		
	    		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	    	$languages = Language::getLanguages(false);
	    	
	    	foreach ($languages as $language)

			$_html .= '	<div id="ccontent_'.$language['id_lang'].'" 
							 style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
							 >

						<textarea class="rte" cols="25" rows="10" style="width:400px"   
								  id="content_'.$language['id_lang'].'" 
								  name="content_'.$language['id_lang'].'">'.htmlentities(Tools::stripslashes($content), ENT_COMPAT, 'UTF-8').'</textarea>

					</div>';
			ob_start();
			$this->displayFlags($languages, $defaultLanguage, $divLangName, 'ccontent');
			$displayflags = ob_get_clean();
			$_html .= $displayflags;
			$_html .= '<div style="clear:both"></div>';
				
				$_html .= '</div>
						';
	    	}
	    	
	    	
	    	$_data_cat = $obj_blockfaq->getItemsCategory(array('admin'=>1));
	    	if(sizeof($_data_cat['items'])){
	    		
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Category association').':</label>';
	    	
	    	$_html .=  '<div class="margin-form">';
	    	
			//echo "<pre>"; var_dump($_data_cat); 
	    	
	    	$_html .= '<select name="faq_category_association[]" multiple size="10">';
	    	$_html .= '<option value=0 selected="true">---</option>';	
	    	foreach($_data_cat['items'] as $item){
	    		$_html .= '<option value='.$item['id'].'>'.$item['title'].'</option>';	
	    	}
	    	
	    	$_html .= '</select>';
	    	
	    	$_html .= '</div>';
	    	
	    	}
	    	
	    	if($this->_is15){
	    	// shop association
	    	$_html .= '<div class="clear"></div>';
	    	$_html .= '<label>'.$this->l('Shop association').':</label>';
	    	$_html .= '<div class="margin-form">';
	
			$_html .= '<table width="50%" cellspacing="0" cellpadding="0" class="table">
							<tr>
								<th>Shop</th>
							</tr>';
			$u = 0;
			
			$shops = Shop::getShops();
			$shops_tmp = explode(",",isset($_data['item'][0]['ids_shops'])?$_data['item'][0]['ids_shops']:"");
			
			$count_shops = sizeof($shops);
			foreach($shops as $_shop){
				$id_shop = $_shop['id_shop'];
				$name_shop = $_shop['name'];
				 $_html .= '<tr>
							<td>
								<img src="../img/admin/lv2_'.((($count_shops-1)==$u)?"f":"b").'.png" alt="" style="vertical-align:middle;">
								<label class="child">';
			 
				
					$_html .= '<input type="checkbox"  
									   name="faq_shop_association[]" 
									   value="'.$id_shop.'" '.((in_array($id_shop,$shops_tmp))?'checked="checked"':'').' 
									   class="input_shop" 
									   />
									'.$name_shop.'';
					
					$_html .= '</label>
							</td>
						</tr>';
			 $u++;
			}
		
			$_html .= '</table>';
				
			$_html .= '</div>';
																	
	    	}
	    	// shop association
	    	
	    	$_html .= '<label>'.$this->l('Show By customer').':</label>';
	    	
	    	$_html .=  '<div class="margin-form">
	    	
					<input type="radio" value="1" id="text_list_on" name="is_by_customer"
							'.($is_by_customer ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_by_customer" 
						   '.(!$is_by_customer ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
				</div>';
	    	
	    	$_html .= '<label>'.$this->l('Customer email').':</label>
    			
    				<div class="margin-form">
					<input style="width:400px" type="text" name="faq_customer_email" size="128" value="'.$customer_email.'">
			        
			       </div>';
	    	
	    	
	    	$_html .= '<label>'.$this->l('Customer name').':</label>
    			
    				<div class="margin-form">
					<input style="width:400px" type="text" name="faq_customer_name" size="128" value="'.$customer_name.'">
			        
			       </div>';
	    	
	    	
	    	$_html .= '<label>'.$this->l('Status').'</label>
				<div class = "margin-form">';
				
			$_html .= '<select name="faq_item_status" style="width:100px">
						<option value=1 '.(($status==1)?"selected=\"true\"":"").'>'.$this->l('Enabled').'</option>
						<option value=0 '.(($status==0)?"selected=\"true\"":"").'>'.$this->l('Disabled').'</option>
					   </select>';
			
				
			$_html .= '</div>';
    	
    		$_html .= '<label>&nbsp;</label>
						<div class = "margin-form"  style="margin-top:10px">
						<input type="button" value="'.$this->l('Cancel').'" 
                		   class="button"  
                		   onclick="$(\'#link-add-question-form\').show(200);$(\'#add-question-form\').hide(200);" 
                		   />&nbsp;&nbsp;&nbsp;
						<input type="submit" name="submit_item" value="'.$this->l('Save').'" 
                		   class="button"  />
                		  </div>';
    		
    		$_html .= '</form>';
    		$_html .= '</div>';
		
    		$_html .= '<br/>';
    		
    		$_data = $obj_blockfaq->getItemsCategory(array('admin'=>1));
    		
    		$_id_selected_category = Tools::getValue("id_category");
    		
    		$all_category = $_data['items'];
			
			
			if(sizeof($all_category)>0){
			$_html .= '<div style="margin-bottom:10px;float:right">';
			$_html .= '<b>'.$this->l('Filter questions by category').':&nbsp;</b>';
			$_html .= '<select onchange="window.location.href=\''.$currentIndex.'&configure='.$this->name.'&token='.$token.'&id_category=\'+this.options[this.selectedIndex].value">';
			$_html .= '<option value=0>---</option>';
			foreach($all_category as $_items){
				$_category_id = $_items['id'];
							$name_product1 = isset($_items['title'])?Tools::stripslashes($_items['title']):'';
	    					if(Tools::strlen($name_product1)==0) continue;
							$_html .= '<option value='.$_category_id.' '.(($_id_selected_category == $_category_id)?'selected="selected"':'').'>'.$name_product1.'</option>';
	    		
			}
			
			$_html .= '<select>';
			if($_id_selected_category)
			$_html .= '&nbsp;&nbsp;<a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'" 
							style="text-decoration:underline">'.$this->l('Clear search').'</a>';
			$_html .= '</div>';
			
			$_html .= '<div style="clear:both"></div>';
			}
    		
			$_html .= '<table class = "table" width = 100%>
			<tr>
				<th width=50>'.$this->l('ID').'</th>
				<th width=100>'.$this->l('Lang').'</th>';
			
			if($this->_is15){
    			$_html .= '<th width = 100>'.$this->l('Shop').'</th>';
    		}
    		
			
				$_html .= '<th>'.$this->l('Question').'</th>';
				
				$_html .= '<th width=100>'.$this->l('Category ID').'</th>';
    			$_html .= '<th width=120>'.$this->l('By Customer').'</th>';
				
				$_html .= '<th width=100>'.$this->l('Position').'</th>
				<th width = "50">'.$this->l('Status').'</th>
				<th width = "50">'.$this->l('Action').'</th>
			</tr>';
			
			$_data = $obj_blockfaq->getItems(array('id_category'=>$_id_selected_category));
			
			$_items = $_data['items'];
			$count_stickers =  sizeof($_items);
			if($count_stickers>0){
				$i=0;
				foreach($_items as $_item){
					$sticker = $_items[$i];
					$id = $_item['id'];
					$title = $_item['title'];
					$is_by_customer = $_item['is_by_customer'];
					//$customer_name = isset($_item['customer_name'])?$_item['customer_name']:'<span style="text-align:center">---</span>';
					$status = $_items[$i]['status'];
					
					
					$faq_category_ids = isset($_items[$i]['faq_category_ids'])?$_items[$i]['faq_category_ids']:'<span style="text-align:center">---</span>';
					
					if(Tools::strlen($faq_category_ids)==0)
						$faq_category_ids = '<span style="text-align:center">---</span>';
					
					
					if($this->_is15){

						$id_shop = $_item['ids_shops'];
						$id_shop = explode(",",$id_shop);
						$shops = Shop::getShops();
						$name_shop = array();
						foreach($shops as $_shop){
							$id_shop_lists = $_shop['id_shop'];
							if(in_array($id_shop_lists,$id_shop))
								$name_shop[] = $_shop['name'];
						}
						$name_shop = implode(",",$name_shop);
					}
					
					$ids_lng = isset($_item['ids_lng'])?$_item['ids_lng']:array();
					$lang_for_faq = array();
					foreach($ids_lng as $lng_id){
						$data_lng = Language::getLanguage($lng_id);
						$lang_for_faq[] = $data_lng['iso_code']; 
					}
					$lang_for_faq = implode(",",$lang_for_faq);
					
					$_html .= 
						'<tr>
						<td style = "color:black;">'.$id.'</td>';
					$_html .= '<td style = "color:black;">'.$lang_for_faq.'</td>';
					
					if($this->_is15){
						$_html .= '<td style="color:black">'.$name_shop.'</td>';
					}
					
					$_html .= '<td style = "color:black;">'.$title.'</td>';
					
					$_html .= '<td style = "color:black">'.$faq_category_ids.'</td>';
					
					
				if($is_by_customer)
					$_html .= '<td><img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif"></td>';
				else
					$_html .= '<td><img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif"></td>';
					
				//	$_html .= '<td style = "color:black">'.$customer_name.'</td>';
					
					
					
					$_html .= '<td style = "color:black;">';
					if($i < $count_stickers - 1) {
                        $_html .= '<a style="text-decoration:none" href="' . $currentIndex . '&configure=' . $this->name . '&token=' . $token . '
										&id=' . $id . '
										&order_self=' . $sticker['order_by'] . '
										&id_change=' . $_items[$i + 1]['id'] . '
										&order_change=' . $_items[$i + 1]['order_by'] . '">
								<img border="0" src="' . __PS_BASE_URI__ . 'img/admin/down.gif">
							</a>';
                    }
                    if($i > 0) {
                        $_html .= '<a style="text-decoration:none" href="' . $currentIndex . '&configure=' . $this->name . '&token=' . $token . '&id=' . $id . '&order_self=' . $sticker['order_by'] . '&id_change=' . $_items[$i - 1]['id'] . '&order_change=' . $_items[$i - 1]['order_by'] . '">
                                    <img border="0" src="' . __PS_BASE_URI__ . 'img/admin/up.gif">
                                </a>';
                    }

					$_html .= '</td>';

					if($status)
					$_html .= '<td><img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif"></td>';
				else
					$_html .= '<td><img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif"></td>';
					
			
					
					$_html .= '<td>
				
								 <input type = "hidden" name = "id" value = "'.$id.'"/>
								 <a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&edit_item&id='.(int)($id).'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a> 
								 <a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&delete_item&id='.(int)($id).'" title="'.$this->l('Delete').'"  onclick = "javascript:return confirm(\''.$this->l('Are you sure you want to remove this item?').'\');"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>'; 
								 $_html .= '</form>
							 </td>';
					$_html .= '</tr>';
					$i++;
				}
			} else {
			$_html .= '<tr><td colspan="9" style="text-align:center;font-weight:bold;padding:10px">'.$this->l('Questions not found').'</td></tr>';	
			}
			
			$_html .= '</table>';
		}
			
			
		
		$_html .=	'</fieldset>'; 
		
		
     	return $_html;
    
    } 
    
    private function _displayForm13_14_15()
     {
     	$_html = '';
     	$_html .= $this->_drawSettingsFAQ();
     	
     	$_html .= '<br/><br/>';
     	$_html .= $this->drawFAQCategories();
     	
     	$_html .= '<br/><br/><br/><br/>';
     	
     	$_html .= $this->drawFaqItems();
     	
     	$_html .= '<br/><br/><br/><br/>';
     	
     	return $_html;
     	
     }
    

    
     public function _jsandcss(){
    	$_html = '';
    	
    	$cookie = $this->context->cookie;
    	
     	if(version_compare(_PS_VERSION_, '1.6', '>')){
    	$_html .=  '<link rel="stylesheet" media="screen" type="text/css" href="../modules/'.$this->name.'/views/css/prestashop16.css" />';
    		
    	}
    	
    	
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		
		if(defined('_MYSQL_ENGINE_') && Tools::substr(_PS_VERSION_,0,3) != '1.5'){
		$_html .=  '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>';
			$_html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
		$_html .= '
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>';
		} 
		
		if(version_compare(_PS_VERSION_, '1.5', '>')  || 
			!defined('_MYSQL_ENGINE_')){
			
			if(version_compare(_PS_VERSION_, '1.5', '>')){
				$_html .=  '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>';
				$_html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
			} else {
				$_html .=  '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>';
				$_html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
				';
			}
			
			
			
		$_html .= '<script type="text/javascript">
					tinyMCE.init({
						mode : "specific_textareas",
						theme : "advanced",
						editor_selector : "rte",';
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			 $_html .= 'skin:"cirkuit",';
		}
			$_html  .=  'editor_deselector : "noEditor",';
			
			if(version_compare(_PS_VERSION_, '1.6', '<')){
			$_html .=  'plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
						//Theme options
						theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
						theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor",
						theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
						theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "bottom",
						theme_advanced_resizing : false,
					';
			}else{
			$_html .= 'toolbar1 : "code,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,cleanup,|,media,image",
		   			   plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor",
		   			   ';
			}
		
						
						
		  $_html .=	   'content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",
						document_base_url : "'.__PS_BASE_URI__.'",';
		  if(!defined('_MYSQL_ENGINE_')){
		  $_html .=		'width: "550",';
		  } else {
		  	if(version_compare(_PS_VERSION_, '1.5', '>'))
		  		$_html .=		'width: "650",';
		  	else
		  		$_html .= 'width: "400",';
		  }
		  
		  $_html .=	    'height: "auto",
						font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
						// Drop lists for link/image/media/template dialogs
						template_external_list_url : "lists/template_list.js",
						external_link_list_url : "lists/link_list.js",
						external_image_list_url : "lists/image_list.js",
						media_external_list_url : "lists/media_list.js",';
			
			if(version_compare(_PS_VERSION_, '1.5', '>')){
			$_html .= 	'elements : "nourlconvert,ajaxfilemanager",
						 file_browser_callback : "ajaxfilemanager",';
			} else {
			$_html .= 	'elements : "nourlconvert",';
			}
			
			$_html .=	'entity_encoding: "raw",
						convert_urls : false,
						language : "'.(file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'"
						
					});
		</script>';
		
		}
		
    	return $_html;
    }	
    
    public function renderTpl(){
    	return Module::display(dirname(__FILE__).'/blockfaq.php', 'views/templates/front/faq.tpl');
    }
    
	public function translateItems(){
    	
    	return array('meta_title_faq' => $this->l('Frequently asked questions - FAQ'),
    				 'meta_description_faq' => $this->l('Frequently asked questions - FAQ'),
    				 'meta_keywords_faq' => $this->l('Frequently asked questions - FAQ'),
    				 'notification_new_q' => $this->l('New Question'),
    				 'response_for_q' => $this->l('Response for question'),
    				 'guest' => $this->l('Guest'),

                    'faq_msg2'=>$this->l('Please, enter the Name.'),
                    'faq_msg3'=>$this->l('Please, enter the Email.'),
                    'faq_msg4'=>$this->l('Please, enter the Question.'),
                    'faq_msg5'=>$this->l('Please, enter the security code.'),
                    'faq_msg6'=>$this->l('Please enter a valid email address. For example johndoe@domain.com.'),
                    'faq_msg7'=>$this->l('You entered the wrong security code.'),

    				);
    }

    private function _welcome(){

        $_html  = '';

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $_html .= '<div class="panel">

                <div class="panel-heading"><i class="fa fa-home fa-lg"></i>&nbsp;'.$this->l('Welcome').'</div>';
        } else {
            $_html .= '<h3 class="title-block-content"><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Welcome').'</h3>';
        }


        $_html .=  '<p class="alert alert-info">'.$this->l('Welcome and thank you for purchasing our module.').'</p>';


        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $_html .= '</div>';
        }

        return $_html;



    }

    private function _help_documentation(){
        $_html = '';

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $_html .= '<div class="panel">

				<div class="panel-heading"><i class="fa fa-question-circle fa-lg"></i>&nbsp;'.$this->l('Help / Documentation').'</div>';
        } else {
            $_html .= '<h3 class="title-block-content">'.$this->l('Help / Documentation').'</h3>';
        }

        $_html .= '<b style="text-transform:uppercase">'.$this->l('MODULE DOCUMENTATION ').':</b>&nbsp;<a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf" style="text-decoration:underline;font-weight:bold">Installation_Guid.pdf</a>
    			';
        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $_html .= '</div>';
        }
        return $_html;
    }


    public function setSEOUrls(){
        $smarty = $this->context->smarty;
        include_once(dirname(__FILE__).'/classes/blockfaqhelp.class.php');
        $obj_blockfaq = new blockfaqhelp();

        $data_url = $obj_blockfaq->getSEOURLs();
        $faq_url = $data_url['faq_url'];

        $smarty->assign(
            array($this->name.'faq_url' => $faq_url,

            )
        );

        $smarty->assign($this->name.'is_ps15', $this->_is15);
        $smarty->assign($this->name.'is15', $this->_is15);
        $smarty->assign($this->name.'is16', $this->_is16);
        if($this->_is_friendly_url){
            $smarty->assign($this->name.'iso_lng', $this->_iso_lng);
        } else {
            $smarty->assign($this->name.'iso_lng', '');
        }

        $smarty->assign($this->name.'is_urlrewrite', Configuration::get($this->name.'is_urlrewrite'));


    }
}