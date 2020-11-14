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

class blockguestbook extends Module
{
	private $_step = 10;
	private $_is15;
	private $_is16;
	private $_is_friendly_url;
	private $_iso_lng;

    private $_is_cloud;
    public $is_demo = 0;

	
	public function __construct()
	{
		$this->name = 'blockguestbook';
		$this->tab = 'content_management';
		$this->version = '1.3.4';
		$this->author = 'SPM';
		$this->module_key = '';

        require_once(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');

		parent::__construct(); // The parent construct is required for translations

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Guestbook');
		$this->description = $this->l('Add Guestbook');


        if (defined('_PS_HOST_MODE_'))
            $this->_is_cloud = 1;
        else
            $this->_is_cloud = 0;

        //$this->_is_cloud = 1;

        if($this->_is_cloud){
            $this->path_img_cloud = "modules/".$this->name."/upload/";
        } else {
            $this->path_img_cloud = "upload/".$this->name."/";

        }


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
 	 	
 	 	
 	 	
		if(version_compare(_PS_VERSION_, '1.5', '>')){
 	 		include_once(dirname(__FILE__).'/classes/guestbook.class.php');
 	 	}else{
 	 		include_once(_PS_MODULE_DIR_.$this->name.'/classes/guestbook.class.php');	
 	 	}
 	 	
		$obj = new guestbook();
		$is_friendly_url = $obj->isURLRewriting();
		$this->_is_friendly_url = $is_friendly_url;
		$this->_iso_lng = $obj->getLangISO();
		
		$this->initContext();


        ## prestashop 1.7 ##
        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_.$this->name.'/classes/ps17helpblockguestbook.class.php');
            $ps17help = new ps17helpblockguestbook();
            $ps17help->setMissedVariables();
        } else {
            $smarty = $this->context->smarty;
            $smarty->assign($this->name.'is17' , 0);
        }
        ## prestashop 1.7 ##
		
	}

    public function getIdModule(){
        return $this->id;
    }

	private function initContext()
	{
        $this->context = Context::getContext();

        if (version_compare(_PS_VERSION_, '1.5', '>')){
            $this->context->currentindex = isset(AdminController::$currentIndex)?AdminController::$currentIndex:'index.php?controller=AdminModules';
        } else {

            $variables14 = variables14_blockguestbook();
            $this->context->currentindex = $variables14['currentindex'];


        }
	}
	

	public function install()
	{
		
		if (!parent::install())
			return false;


        Configuration::updateValue($this->name.'switch_lng_gb', 0);

        Configuration::updateValue($this->name.'is_avatarg', 1);

        if(Configuration::get('PS_REWRITING_SETTINGS')){
            Configuration::updateValue($this->name.'is_urlrewrite', 1);
        }

        Configuration::updateValue($this->name.'BGCOLOR_G', '#fafafa');

		if($this->_is16 == 1){
			Configuration::updateValue($this->name.'g_home', 1);
			Configuration::updateValue($this->name.'g_footer', 1);
		
		}	
		Configuration::updateValue($this->name.'g_left', 1);


        Configuration::updateValue($this->name.'is_webg', 1);
        Configuration::updateValue($this->name.'is_companyg', 1);
        Configuration::updateValue($this->name.'is_addrg', 1);

        Configuration::updateValue($this->name.'is_countryg', 1);
        Configuration::updateValue($this->name.'is_cityg', 1);
		
		Configuration::updateValue($this->name.'perpageg', 5);
		Configuration::updateValue($this->name.'notig', 1);
		Configuration::updateValue($this->name.'mailg', @Configuration::get('PS_SHOP_EMAIL'));
		Configuration::updateValue($this->name.'gbook_blc', 3);
		
		Configuration::updateValue($this->name.'is_captchag', 1);

        Configuration::updateValue($this->name.'n_rssitemsg', 10);
        Configuration::updateValue($this->name.'rssong', 1);


        if(version_compare(_PS_VERSION_, '1.6', '<'))
            $this->generateRewriteRules();
		
		if($this->_is15 == 1)
	 		$this->createAdminTabs();
	 	else
	 		$this->createAdminTabs14();
		
		if (!$this->registerHook('displayLeftColumn')
			OR !$this->registerHook('displayRightColumn')
            OR !$this->registerHook('displayHeader')
            OR !$this->registerHook('displayHome')
            OR !$this->registerHook('displayFooter')
			OR !$this->createGuestBookTable()
			OR !((version_compare(_PS_VERSION_, '1.5', '>'))? $this->registerHook('DisplayBackOfficeHeader') : true)
            OR !((version_compare(_PS_VERSION_, '1.6', '>'))? $this->registerHook('ModuleRoutes') : true)
            OR !($this->_is_cloud? true : $this->createFolderAndSetPermissions())

            //GDPR
            || !$this->registerHook('registerGDPRConsent')
            || !$this->registerHook('actionDeleteGDPRCustomer')
            || !$this->registerHook('actionExportGDPRData')
            //GDPR



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
			
		if (!parent::uninstall()  || !$this->uninstallTable())
			return false;
		return true;
	}
	
	
	private function uninstallTable() {
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'blockguestbook');
		return true;
	}


    public function hookActionDeleteGDPRCustomer ($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {

            include_once(_PS_MODULE_DIR_.$this->name.'/classes/guestbook.class.php');
            $obj = new guestbook();
            $return = $obj->deleteGDPRCustomerData($customer['email']);

            if ($return) {
                return json_encode(true);
            }
            return json_encode($this->displayName . ' : ' .$this->l('Unable to delete customer using email.'));
        }
    }


    public function hookActionExportGDPRData ($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {

            include_once(_PS_MODULE_DIR_.$this->name.'/classes/guestbook.class.php');
            $obj = new guestbook();
            $return = $obj->getGDPRCustomerData($customer['email']);

            if (count($return)>0) {
                return json_encode($return);
            }
            return json_encode($this->displayName . ' : ' .$this->l('Unable to export customer using email.'));
        }
    }


    public function hookDisplayBackOfficeHeader()
    {

        if(version_compare(_PS_VERSION_, '1.6', '>')) {
            $base_dir = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;


            $css = '';
            $css .= '<style type="text/css">
                    .icon-AdminGuestbook:before {
                            content: url("' . $base_dir . 'modules/' . $this->name . '/logo.gif");
                        }
                        </style>
                ';
            return $css;
        }
    }

    public function hookModuleRoutes()
    {
        return array(

            ## guestbook ##

            'blockguestbook-guestbook' => array(
                'controller' =>	null,
                'rule' =>		'{controller}',
                'keywords' => array(
                    'controller'	=>	array('regexp' => 'guestbook', 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockguestbook'
                )
            ),

            'blockguestbook-guestbook-p' => array(
                'controller' =>	null,
                'rule' =>		'{controller}/{p}',
                'keywords' => array(
                    'p'				=>	array('regexp' => '[0-9]+', 'param' => 'p'),
                    'controller'	=>	array('regexp' => 'guestbook', 'param' => 'controller')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'blockguestbook'
                )
            ),

            ## guestbook ##

        );
    }

    public function createFolderAndSetPermissions(){

        $prev_cwd = getcwd();

        $module_dir = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
        @chdir($module_dir);
        //folder avatars
        $module_dir_img = $module_dir.$this->name.DIRECTORY_SEPARATOR;
        @mkdir($module_dir_img, 0777);

        @chdir($prev_cwd);

        return true;
    }

	public function createAdminTabs(){

        if(version_compare(_PS_VERSION_, '1.6', '>')) {
            $prefix = '';
        } else {
            $prefix = 'old';
        }

            copy_custom_blockguestbook(dirname(__FILE__)."/views/img/AdminGuestbooks".$prefix.".gif",_PS_ROOT_DIR_."/img/t/AdminGuestbooks".$prefix.".gif");
		
		 	$langs = Language::getLanguages();
            
          
            $tab0 = new Tab();
            $tab0->class_name = "AdminGuestbook".$prefix;
            $tab0->module = $this->name;
            $tab0->id_parent = 0; 
            foreach($langs as $l){
                    $tab0->name[$l['id_lang']] = $this->l('Guestbook');
            }
            $tab0->save();
            $main_tab_id = $tab0->id;

            unset($tab0);
            
            $tab1 = new Tab();
            $tab1->class_name = "AdminGuestbooks".$prefix;
            $tab1->module = $this->name;
            $tab1->id_parent = $main_tab_id; 
            foreach($langs as $l){
                    $tab1->name[$l['id_lang']] = $this->l('Moderate Messages');
            }
            $tab1->save();

            unset($tab1);
        

	}
	
	private function createAdminTabs14(){
         copy_custom_blockguestbook(dirname(__FILE__)."AdminGuestbooksold.gif", _PS_ROOT_DIR_."/img/t/AdminGuestbooksold.gif");
		
		 	$langs = Language::getLanguages();
            
          
            $tab0 = new Tab();
            $tab0->class_name = "AdminGuestbooksold";
            $tab0->module = $this->name;
            $tab0->id_parent = 0; 
            foreach($langs as $l){
                    $tab0->name[$l['id_lang']] = $this->l('Guestbook');
            }
            $tab0->save();

	}
	
	private function uninstallTab(){

        if(version_compare(_PS_VERSION_, '1.6', '>')) {
            $prefix = '';
        } else {
            $prefix = 'old';
        }
		
		$tab_id = Tab::getIdFromClassName("AdminGuestbook".$prefix);
		if($tab_id){
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		$tab_id = Tab::getIdFromClassName("AdminGuestbooks".$prefix);
		if($tab_id){
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		@unlink(_PS_ROOT_DIR_."/img/t/AdminGuestbook".$prefix.".gif");
	}
	
	private function uninstallTab14(){
		
		$tab_id = Tab::getIdFromClassName("AdminGuestbooksold");
		if($tab_id){
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		@unlink(_PS_ROOT_DIR_."/img/t/AdminGuestbookold.gif");
	}
	

	
	public function createGuestBookTable()
	{
		$db = Db::getInstance();
	
		$query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blockguestbook` (
							  `id` int(11) NOT NULL auto_increment,
							  `name` varchar(500) NOT NULL,
							  `avatar` text,
							  `email` varchar(500) NOT NULL,
							  `ip` varchar(500) default NULL,
							  `message` text NOT NULL,
							  `response` text,
							  `is_show` int(11) NOT NULL default \'0\',

							  `web` varchar(500) default NULL,
							  `company` varchar(500) default NULL,
							  `address` varchar(500) default NULL,
							  `country` varchar(500) default NULL,
							  `city` varchar(500) default NULL,

							  `id_shop` int(11) NOT NULL default \'0\',
							  `id_lang` int(11) NOT NULL default \'0\',
							  `active` int(11) NOT NULL default \'0\',
							  `is_deleted` int(11) NOT NULL default \'0\',
							  `date_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
							  PRIMARY KEY  (`id`)
							) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8;';
		$db->Execute($query);
		return true;
	}	
	
	private function generateRewriteRules(){
            
            if(Configuration::get('PS_REWRITING_SETTINGS')){

                $rules = "#blockguestbook - not remove this comment \n";
                
                $physical_uri = array();
                
                if($this->_is15){
	                foreach (ShopUrl::getShopUrls() as $shop_url)
					{
	                    if(in_array($shop_url->physical_uri,$physical_uri)) continue;
	                    
	                  $rules .= "RewriteRule ^(.*)guestbook/?$ ".$shop_url->physical_uri."modules/".$this->name."/blockguestbook-form.php [QSA,L] \n"; 
	                    
	                    $physical_uri[] = $shop_url->physical_uri;
	                } 
                } else{
                	$rules .= "RewriteRule ^(.*)guestbook/?$ /modules/".$this->name."/blockguestbook-form.php [QSA,L] \n"; 
	                  
                }
                $rules .= "#blockguestbook \n\n";
                
                $path = _PS_ROOT_DIR_.'/.htaccess';

                  if(is_writable($path)){
                      
                      $existingRules = file_get_contents_custom_blockguestbook($path);
                      
                      if(!strpos($existingRules, "blockguestbook")){
                        $handle = fopen($path, 'w');
                        fwrite($handle, $rules.$existingRules);
                        fclose($handle);
                      }
                  }
              }
        }
	
	
	public function hookdisplayLeftColumn($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/guestbook.class.php');
		$obj_guestbook = new guestbook();
    	$_data = $obj_guestbook->getItems(array('start'=>0,
    											 'step'=>Configuration::get($this->name.'gbook_blc')
    											));

		$smarty->assign(array($this->name.'reviews' => $_data['reviews'], 
							  $this->name.'count_all_reviews' => $_data['count_all_reviews'])
						);
		
		$smarty->assign($this->name.'g_left', Configuration::get($this->name.'g_left'));

        $this->setSEOUrls();

		
		return $this->display(__FILE__, 'views/templates/hooks/left.tpl');		
	}
	
	public function hookdisplayRightColumn($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/guestbook.class.php');
		$obj_guestbook = new guestbook();
    	$_data = $obj_guestbook->getItems(array('start'=>0,
    											 'step'=>Configuration::get($this->name.'gbook_blc')
    											));

        $smarty->assign(array($this->name.'reviews' => $_data['reviews'],
                $this->name.'count_all_reviews' => $_data['count_all_reviews'])
        );
		$smarty->assign($this->name.'g_right', Configuration::get($this->name.'g_right'));

        $this->setSEOUrls();
		
		return $this->display(__FILE__, 'views/templates/hooks/right.tpl');		
	}
	
	public function hookdisplayHome($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/guestbook.class.php');
		$obj_guestbook = new guestbook();
    	$_data = $obj_guestbook->getItems(array('start'=>0,
    											 'step'=>Configuration::get($this->name.'gbook_blc')
    											));

		$smarty->assign(array($this->name.'reviews' => $_data['reviews'], 
							  $this->name.'count_all_reviews' => $_data['count_all_reviews'])
						);
		
		$smarty->assign($this->name.'g_home', Configuration::get($this->name.'g_home'));

        $smarty->assign($this->name.'is_webg', Configuration::get($this->name.'is_webg'));
        $smarty->assign($this->name.'is_countryg', Configuration::get($this->name.'is_countryg'));
        $smarty->assign($this->name.'is_cityg', Configuration::get($this->name.'is_cityg'));

        $this->setSEOUrls();

		return $this->display(__FILE__, 'views/templates/hooks/home.tpl');		
	}
	
	public function hookdisplayFooter($params)
	{
		$smarty = $this->context->smarty;
		include_once(dirname(__FILE__).'/classes/guestbook.class.php');
		$obj_guestbook = new guestbook();
    	$_data = $obj_guestbook->getItems(array('start'=>0,
    											 'step'=>Configuration::get($this->name.'gbook_blc')
    											));

		$smarty->assign(array($this->name.'reviews_footer' => $_data['reviews'],
							  $this->name.'count_all_reviews' => $_data['count_all_reviews'])
						);
		
		$smarty->assign($this->name.'g_footer', Configuration::get($this->name.'g_footer'));



        $this->setSEOUrls();
		
		return $this->display(__FILE__, 'views/templates/hooks/footer.tpl');		
	}
	
	public function hookdisplayHeader($params){
    	$smarty = $this->context->smarty;

        $smarty->assign($this->name.'rssong', Configuration::get($this->name.'rssong'));


        $smarty->assign($this->name.'BGCOLOR_G', Configuration::get($this->name.'BGCOLOR_G'));


        $smarty->assign($this->name.'is15', $this->_is15);
		
    	if(version_compare(_PS_VERSION_, '1.5', '>')){
    		$this->context->controller->addCSS(($this->_path).'views/css/blockguestbook.css', 'all');
            $this->context->controller->addJS($this->_path.'views/js/blockguestbook.js');
    	}

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


        include_once(dirname(__FILE__).'/classes/guestbook.class.php');
		$obj_guestbook = new guestbook();
		
	    
       // publish
	   if (Tools::isSubmit("published")) {
			if (Validate::isInt(Tools::getValue("id"))){
				$obj_guestbook->publish(array('id'=>Tools::getValue("id")));
			} 
				
		}
		
		//unpublish
		if (Tools::isSubmit("unpublished")) {
			if (Validate::isInt(Tools::getValue("id"))){
				$obj_guestbook->unpublish(array('id'=>Tools::getValue("id")));
			} 
				
		}
		
    	// delete item
		if (Tools::isSubmit("delete_item")) {
			if (Validate::isInt(Tools::getValue("id"))) {
				$obj_guestbook->delete(array('id'=>Tools::getValue("id")));
				Tools::redirectAdmin($currentIndex.'&tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
			}
			
		}


        $guestbook_settingsset = Tools::getValue("guestbook_settingsset");
        if (Tools::strlen($guestbook_settingsset)>0 && version_compare(_PS_VERSION_, '1.6', '>')) {
            $this->_html .= '<script>init_tabs(3);</script>';
        }
	    
	    if (Tools::isSubmit('submit_guestbook'))
        {
            Configuration::updateValue($this->name.'switch_lng_gb', Tools::getValue('switch_lng_gb'));
            Configuration::updateValue($this->name.'is_avatarg', Tools::getValue('is_avatarg'));

        	 Configuration::updateValue($this->name.'g_home', Tools::getValue('g_home'));
        	 Configuration::updateValue($this->name.'g_left', Tools::getValue('g_left'));
        	 Configuration::updateValue($this->name.'g_right', Tools::getValue('g_right'));
        	 Configuration::updateValue($this->name.'g_footer', Tools::getValue('g_footer'));
        	

        	 Configuration::updateValue($this->name.'perpageg', Tools::getValue('perpage'));
        	 Configuration::updateValue($this->name.'notig', Tools::getValue('noti'));
        	 Configuration::updateValue($this->name.'mailg', Tools::getValue('mail'));
        	 Configuration::updateValue($this->name.'gbook_blc', Tools::getValue('gbook_blc'));
        	 
        	 Configuration::updateValue($this->name.'is_urlrewrite', Tools::getValue('is_urlrewrite'));
        	 Configuration::updateValue($this->name.'is_captchag', Tools::getValue('is_captcha'));
            Configuration::updateValue($this->name.'is_webg', Tools::getValue('is_web'));
            Configuration::updateValue($this->name.'is_companyg', Tools::getValue('is_company'));
            Configuration::updateValue($this->name.'is_addrg', Tools::getValue('is_addr'));

            Configuration::updateValue($this->name.'is_countryg', Tools::getValue('is_country'));
            Configuration::updateValue($this->name.'is_cityg', Tools::getValue('is_city'));

            Configuration::updateValue($this->name.'n_rssitemsg', Tools::getValue('n_rssitemsg'));
            Configuration::updateValue($this->name.'rssong', Tools::getValue('rssong'));

            Configuration::updateValue($this->name.'BGCOLOR_G', Tools::getValue($this->name.'BGCOLOR_G'));


            $url = $currentIndex . '&conf=6&tab=AdminModules&guestbook_settingsset=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee)) . '';
            Tools::redirectAdmin($url);
        	 
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
        	
        	Tools::redirectAdmin($currentIndex.'&tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
		
        }
               
		if (Tools::isSubmit('cancel_item'))
        {
        	Tools::redirectAdmin($currentIndex.'&tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'');
		}

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->addBackOfficeMedia();
        }

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->_displayForm16();
        } else {
            $this->_displayForm13_14_15();
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
                        'label' => $this->l('Enable or Disable URL rewriting:'),
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
                        'label' => $this->l('The number of items in the "Block Guestbook":'),
                        'name' => 'gbook_blc',
                        'class' => ' fixed-width-sm',

                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Messages per Page:'),
                        'name' => 'perpage',
                        'class' => ' fixed-width-sm',

                    ),

                    array(
                        'type' => 'color',
                        'lang' => true,
                        'label' => $this->l('Guestbook posts block background color'),
                        'name' => $this->name.'BGCOLOR_G',
                        'desc' => $this->l('You can enter Hexadecimal color code for the background like #000000')
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('MULTILANGUAGE. Separates different languages comments depended on the language selected by the customer (e.g. only English comments on the English site):'),
                        'name' => 'switch_lng_gb',

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
                        'label' => $this->l('Enable Avatar in the submit form'),
                        'name' => 'is_avatarg',

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
                        'label' => $this->l('Enable Captcha in the submit form'),
                        'name' => 'is_captcha',

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
                        'label' => $this->l('Enable Web address in the submit form:'),
                        'name' => 'is_web',

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
                        'label' => $this->l('Enable Company in the submit form:'),
                        'name' => 'is_company',

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
                        'label' => $this->l('Enable Address in the submit form:'),
                        'name' => 'is_addr',

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
                        'label' => $this->l('Enable Country in the submit form:'),
                        'name' => 'is_country',

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
                        'label' => $this->l('Enable City in the submit form:'),
                        'name' => 'is_city',

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
                        'name' => 'mail',
                        'id' => 'mail',
                        'lang' => FALSE,

                    ),

                    array(
                        'type' => 'checkbox_custom',
                        'label' => $this->l('E-mail notification:'),
                        'name' => 'noti',
                        'values' => array(
                            'value' => (int)Configuration::get($this->name.'notig')
                        ),
                    ),



                    array(
                        'type' => 'checkbox_custom_blocks',
                        'label' => $this->l('Position Messages Block:'),
                        'name' => 't_left',
                        'values' => array(
                            'query' => array(

                                array(
                                    'id' => 'g_left',
                                    'name' => $this->l('Left column'),
                                    'val' => 1
                                ),


                                array(
                                    'id' => 'g_right',
                                    'name' => $this->l('Right column'),
                                    'val' => 1
                                ),


                                array(
                                    'id' => 'g_footer',
                                    'name' => $this->l('Footer'),
                                    'val' => 1
                                ),

                                array(
                                    'id' => 'g_home',
                                    'name' => $this->l('Home'),
                                    'val' => 1
                                ),






                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),

                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable or Disable RSS Feed:'),
                        'name' => 'rssong',

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
                        'label' => $this->l('Number of items in RSS Feed:'),
                        'name' => 'n_rssitemsg',
                        'class' => ' fixed-width-sm',

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
        $helper->submit_action = 'submit_guestbook';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesTestimonialsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesTestimonialsSettings(){

        $data_config = array(
            'is_urlrewrite' => Configuration::get($this->name.'is_urlrewrite'),

            $this->name.'BGCOLOR_G'=>Configuration::get($this->name.'BGCOLOR_G'),

            'gbook_blc'=> Configuration::get($this->name.'gbook_blc'),
            'perpage'=> Configuration::get($this->name.'perpageg'),

            'switch_lng_gb'=>Configuration::get($this->name.'switch_lng_gb'),

            'is_avatarg'=>Configuration::get($this->name.'is_avatarg'),

            'is_captcha'=>Configuration::get($this->name.'is_captchag'),
            'is_web'=>Configuration::get($this->name.'is_webg'),
            'is_company'=>Configuration::get($this->name.'is_companyg'),
            'is_addr'=>Configuration::get($this->name.'is_addrg'),
            'is_country'=>Configuration::get($this->name.'is_countryg'),
            'is_city'=>Configuration::get($this->name.'is_cityg'),


            'mail'=>Configuration::get($this->name.'mailg'),

            'g_left'=>Configuration::get($this->name.'g_left'),
            'g_right'=>Configuration::get($this->name.'g_right'),
            'g_footer'=>Configuration::get($this->name.'g_footer'),
            'g_home'=>Configuration::get($this->name.'g_home'),

            'rssong'=> Configuration::get($this->name.'rssong'),
            'n_rssitemsg'=> Configuration::get($this->name.'n_rssitemsg'),

        );

        return $data_config;

    }


private function _displayForm13_14_15()
     {
     	$this->_html = '';
        $this->_html .= $this->_jsandcss();
        $this->_html .= $this->_drawSettings();
        $this->_html .= '<br/><br/>';
        $this->_html .= $this->drawGuestbookMessages();
        
     	
		
    }
    

    
    private function _drawSettings(){
    	
    	
    	$_html = '';

    	$_html .= '
        <div style="margin-top:10px">';
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/logo.gif" />'
    				.$this->l('Messages Settings:').'</legend>';
    	$_html .= '
        <form action="'.$_SERVER['REQUEST_URI'].'" method="post">';


    	$_html .= '<style type="text/css">
    				.table-settings{width:100%}
    				.table-settings tr td{padding:3px}
    				.guestbooksettings-left{text-align:right;width:30%;padding:0 20px 0 0}
    				</style>';

    	$_html .= '<table class="table-settings">';

    	$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';

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
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';

		$_html .= '<b>'.$this->l('Position Messages Block:').'</b>';

		$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';

				$_html .= '<table style="width:66%;">
	    				<tr>
	    					<td style="width: 33%">'.$this->l('Left Column').'</td>
	    					<td style="width: 33%">'.$this->l('Right Column').'</td>

	    				</tr>
	    				<tr>
	    					<td>
	    						<input type="checkbox" name="g_left" '.((Tools::getValue($this->name.'g_left', Configuration::get($this->name.'g_left')) ==1)?'checked':'').'  value="1"/>
	    					</td>
	    					<td>
	    						<input type="checkbox" name="g_right" '.((Tools::getValue($this->name.'g_right', Configuration::get($this->name.'g_right')) ==1)?'checked':'') .' value="1"/>
	    					</td>

	    				</tr>
	    				<tr>
	    					<td>'.$this->l('Footer').'</td>
	    					<td>'.$this->l('Home').'</td>

	    				</tr>
	    				<tr>
	    					<td>
	    						<input type="checkbox" name="g_footer" '.((Tools::getValue($this->name.'g_footer', Configuration::get($this->name.'g_footer')) ==1)?'checked':'').' value="1"/>
	    					</td>
	    					<td>
	    						<input type="checkbox" name="g_home" '.((Tools::getValue($this->name.'g_home', Configuration::get($this->name.'g_home')) ==1)?'checked':'').' value="1"/>
	    					</td>

	    				</tr>

	    			</table>';
	    //$_html .= '</div>';
		$_html .= '</td>';
		$_html .= '</tr>';

		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';

    	$_html .= '<b>'.$this->l('Messages per Page:').'</b>';

    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="text" name="perpage"
			               value="'.Tools::getValue('perpageg', Configuration::get($this->name.'perpageg')).'"
			               >
				';
		$_html .= '</td>';
		$_html .= '</tr>';

		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:35%;padding:0 20px 0 0">';

    	$_html .= '<b>'.$this->l('The number of items in the "Block Guestbook":').'</b>';

    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="text" name="gbook_blc"
			               value="'.Tools::getValue('gbook_blc', Configuration::get($this->name.'gbook_blc')).'"
			               >
				';
		$_html .= '</td>';
		$_html .= '</tr>';


        $_html .= $this->_colorpicker(array('name' => $this->name.'BGCOLOR_G',
            'color' => Configuration::get($this->name.'BGCOLOR_G'),
            'title' => $this->l('Background')
        ));

        $_html .= '<tr>';
        $_html .= '<td style="text-align:right;width:35%;padding:0 20px 0 0">';

        $_html .= '<b>'.$this->l('MULTILANGUAGE. Separates different languages comments depended on the language selected by the customer (e.g. only English comments on the English site):').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';
        $_html .=  '
					<input type="radio" value="1" id="text_list_on" name="switch_lng_gb"
							'.(Configuration::get($this->name.'switch_lng_gb') ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t">
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="switch_lng_gb"
						   '.(!Configuration::get($this->name.'switch_lng_gb') ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>

				';
        $_html .= '</td>';
        $_html .= '</tr>';



        $_html .= '<tr>';
        $_html .= '<td style="text-align:right;width:35%;padding:0 20px 0 0">';

        $_html .= '<b>'.$this->l('Enable Avatar in the submit form').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';
        $_html .=  '
					<input type="radio" value="1" id="text_list_on" name="is_avatarg"
							'.(Configuration::get($this->name.'is_avatarg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t">
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_avatarg"
						   '.(!Configuration::get($this->name.'is_avatarg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>

				';
        $_html .= '</td>';
        $_html .= '</tr>';


		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:35%;padding:0 20px 0 0">';

    	$_html .= '<b>'.$this->l('Enable Captcha in the submit form').':</b>';

    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .=  '
					<input type="radio" value="1" id="text_list_on" name="is_captcha"
							'.(Configuration::get($this->name.'is_captchag') ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t">
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_captchag"
						   '.(!Configuration::get($this->name.'is_captchag') ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>

				';
		$_html .= '</td>';
		$_html .= '</tr>';


        $_html .= '<tr>';
        $_html .= '<td class="guestbooksettings-left">';

        $_html .= '<b>'.$this->l('Enable Web address in the submit form').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';
        $_html .=  '
					<input type="radio" value="1" id="text_list_on" name="is_web"
							'.(Configuration::get($this->name.'is_webg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t">
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_web"
						   '.(!Configuration::get($this->name.'is_webg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>

				';
        $_html .= '</td>';
        $_html .= '</tr>';

        $_html .= '<tr>';
        $_html .= '<td class="guestbooksettings-left">';

        $_html .= '<b>'.$this->l('Enable Company in the submit form').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';
        $_html .=  '
					<input type="radio" value="1" id="text_list_on" name="is_company"
							'.(Configuration::get($this->name.'is_companyg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t">
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_company"
						   '.(!Configuration::get($this->name.'is_companyg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>

				';
        $_html .= '</td>';
        $_html .= '</tr>';


        $_html .= '<tr>';
        $_html .= '<td class="guestbooksettings-left">';

        $_html .= '<b>'.$this->l('Enable Address in the submit form').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';
        $_html .=  '
					<input type="radio" value="1" id="text_list_on" name="is_addr"
							'.(Configuration::get($this->name.'is_addrg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t">
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					<input type="radio" value="0" id="text_list_off" name="is_addr"
						   '.(!Configuration::get($this->name.'is_addrg') ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>

				';
        $_html .= '</td>';
        $_html .= '</tr>';


        $_html .= '<tr>';
        $_html .= '<td class="guestbooksettings-left">';

        $_html .= '<b>'.$this->l('Enable Country in the submit form').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';
        $_html .=  '
		<input type="radio" value="1" id="text_list_on" name="is_country"
		'.(Configuration::get($this->name.'is_countryg') ? 'checked="checked" ' : '').'>
		<label for="dhtml_on" class="t">
		<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
		</label>
		<input type="radio" value="0" id="text_list_off" name="is_country"
		'.(!Configuration::get($this->name.'is_countryg') ? 'checked="checked" ' : '').'>
		<label for="dhtml_off" class="t">
		<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
		</label>

		';
        $_html .= '</td>';
        $_html .= '</tr>';


        $_html .= '<tr>';
        $_html .= '<td class="guestbooksettings-left">';

        $_html .= '<b>'.$this->l('Enable City in the submit form').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';
        $_html .=  '
		<input type="radio" value="1" id="text_list_on" name="is_city"
		'.(Configuration::get($this->name.'is_cityg') ? 'checked="checked" ' : '').'>
		<label for="dhtml_on" class="t">
		<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
		</label>
		<input type="radio" value="0" id="text_list_off" name="is_city"
		'.(!Configuration::get($this->name.'is_cityg') ? 'checked="checked" ' : '').'>
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
					<input type="text" name="mail"  
			               value="'.Configuration::get($this->name.'mailg').'"
			               >
				';
		$_html .= '</td>';
		$_html .= '</tr>';
		
		$_html .= '<tr>';
    	$_html .= '<td style="text-align:right;width:30%;padding:0 20px 0 0">';
    	
    	$_html .= '<b>'.$this->l('E-mail notification:').'</b>';
    	
    	$_html .= '</td>';
    	$_html .= '<td style="text-align:left">';
		$_html .= '<input type = "checkbox" name = "noti" id = "noti" value ="1" '.((Configuration::get($this->name.'notig') ==1)?'checked':'').'/>';
		$_html .= '</td>';
		$_html .= '</tr>';


        $_html .= '<tr>';
        $_html .= '<td class="guestbooksettings-left">';

        $_html .= '<b>'.$this->l('Enable or Disable RSS Feed').':</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';


        $_html .=  '
					<input type="radio" value="1" id="text_list_on" name="rssong"
							'.(Configuration::get($this->name.'rssong') ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t">
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>

					<input type="radio" value="0" id="text_list_off" name="rssong"
						   '.(!Configuration::get($this->name.'rssong') ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
				';
        $_html .= '</td>';
        $_html .= '</tr>';



        $_html .= '<tr>';
        $_html .= '<td class="guestbooksettings-left">';


        $_html .= '<b>'.$this->l('Number of items in RSS Feed').':</b>';

        $_html .= '<td style="text-align:left">';
        $_html .=  '
					<input type="text" name="n_rssitemsg"
			               value="'.Configuration::get($this->name.'n_rssitemsg').'"
			               >
				';

        $_html .= '</td>';
        $_html .= '</tr>';
		
		
		$_html .= '</table>';
    	
    	$_html .= '<p class="center" style="border: 1px solid #EBEDF4; padding: 10px; margin-top: 10px;">
					<input type="submit" name="submit_guestbook" value="'.$this->l('Update settings').'"
                		   class="button"  />
                	</p>';
   		$_html .= '</form>';
    	$_html .=	'</fieldset>';
    	
    	$_html .= '<br/><br/>';
    	
    	$_html .= '<div id="block-tools-settings" '.(Configuration::get($this->name.'is_urlrewrite')==1?'style="display:block"':'style="display:none"').'>';
    	
    	$_html .= '<fieldset>
					<legend>'.$this->l('Tools').'</legend>';
    	$_html .= $this->_hint();
    	$_html .= '</fieldset>';
    	$_html .= '</div>';
    	
    	return $_html;
		
    }

    private function _colorpicker($data){

        $name = $data['name'];
        $color = $data['color'];
        $title = $data['title'];


        $_html = '';
        $_html .= '<tr>';
        $_html .= '<td style="text-align:right;width:35%;padding:0 20px 0 0">';

        $_html .= '<b>'.$title.':'.'</b>';

        $_html .= '</td>';
        $_html .= '<td style="text-align:left">';

        /*$_html .= '<label style="margin-top:6px">'.$title.':'.'</label>
					<div class="margin-form">';*/
        $_html .= '				<input type="text"
								id="'.$name.'_val"
							   value="'.Tools::getValue($name, Configuration::get($name)).'"
								name="'.$name.'" style="float:left;margin-top:6px;margin-right:10px" >';
        $_html .= '<div id="'.$name.'" style="float:left;"><div style="background-color: '.$color.';"></div></div>
    			  <div style="clear:both"></div>
						<script>$(\'#'.$name.'\').ColorPicker({
								color: \''.$color.'\',
								onShow: function (colpkr) {
									$(colpkr).fadeIn(500);
									return false;
								},
								onHide: function (colpkr) {
									$(colpkr).fadeOut(500);
									return false;
								},
								onChange: function (hsb, hex, rgb) {
									$(\'#'.$name.' div\').css(\'backgroundColor\', \'#\' + hex);
									$(\'#'.$name.'_val\').val(\'\');
									$(\'#'.$name.'_val\').val(\'#\' + hex);
								}
							});</script>';
        //$_html .= '</div>';
        $_html .= '</td>';
        $_html .= '</tr>';
        return $_html;
    }
    
private function _hint(){
    	$_html = '';
    	
    	$_html .= '<p style="display: block; font-size: 11px; width: 95%; margin-bottom:20px;position:relative" class="hint clear">
    	<b style="color:#585A69">'.$this->l('If url rewriting doesn\'t works, check that this above lines exist in your current .htaccess file, if no, add it manually on top of your .htaccess file').':</b>
    	<br/><br/>
    	<code>
		RewriteRule ^(.*)guestbook/?$ '.__PS_BASE_URI__.'modules/'.$this->name.'/blockguestbook-form.php [QSA,L]
	    </code>
		
			<br/><br/>
		</p>';
    	
    	return $_html;
    }

public function drawGuestbookMessages($data = null){
		$cookie = $this->context->cookie;
		

		/*$currentIndex = isset($data['currentindex'])?$data['currentindex']:$currentIndex;
    	$controller = isset($data['controller'])?$data['controller']:'AdminModules';
    	$token = isset($data['token'])?$data['token']:Tools::getAdminToken($controller.(int)(Tab::getIdFromClassName($controller)).(int)($cookie->id_employee));
    	*/

        $tab = 'AdminGuestbooksold';
        if(version_compare(_PS_VERSION_, '1.5', '>')) {
            $currentIndex = isset(AdminController::$currentIndex) ? AdminController::$currentIndex : 'index.php?controller='.$tab;
        } else {
            $currentIndex = 'index.php?tab=AdminModules';
        }

        $currentIndex = isset($data['currentindex'])?$data['currentindex']:$currentIndex;

        $controller = isset($data['controller'])?$data['controller']:'AdminModules';

        $token = isset($data['token'])?$data['token']:Tools::getAdminToken($controller.(int)(Tab::getIdFromClassName($controller)).(int)($cookie->id_employee));


        if($this->_is_cloud){
            $path_img_cloud = DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
        } else {
            $path_img_cloud = DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR;

        }

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $base_dir = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        } else {
            $base_dir = _PS_BASE_URL_.__PS_BASE_URI__;
        }

    	$_html = '';
    	
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Moderate Messages').'</legend>';
    	
    	include_once(dirname(__FILE__).'/classes/guestbook.class.php');
		$obj_guestbook = new guestbook();
				
    	if(Tools::isSubmit("edit_item")){
    		$_data = $obj_guestbook->getItem(array('id'=>(int)Tools::getValue("id")));
    		
    		$_html .= '
    		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
    		
    		//echo "<pre>"; var_dump($_data); exit;
    		$name = $_data['reviews'][0]['name'];
    		$email = $_data['reviews'][0]['email'];
    		$ip = $_data['reviews'][0]['ip'];
    		$message = $_data['reviews'][0]['message'];
    		$date = $_data['reviews'][0]['date_add'];
    		$active = $_data['reviews'][0]['active'];
    		$id = $_data['reviews'][0]['id'];


            $company = $_data['reviews'][0]['company'];
            $address = $_data['reviews'][0]['address'];
            $web = $_data['reviews'][0]['web'];

            $country = $_data['reviews'][0]['country'];
            $city = $_data['reviews'][0]['city'];

            $avatar = $_data['reviews'][0]['avatar'];

            $response = $_data['reviews'][0]['response'];
            $is_show = $_data['reviews'][0]['is_show'];

    		
    		$lang = $_data['reviews'][0]['id_lang'];
    		$data_lng = Language::getLanguage($lang);
			$lang_for_testimonial = $data_lng['iso_code'];

			if($this->_is15){
				$id_shop = $_data['reviews'][0]['id_shop'];
				
				$shops = Shop::getShops();
				$name_shop = '';
				foreach($shops as $_shop){
					$id_shop_lists = $_shop['id_shop'];
					if($id_shop == $id_shop_lists)
						$name_shop = $_shop['name'];
				}
			}
    		
    		$_html .= '<label>'.$this->l('ID').':</label>';
    		$_html .= '<div style="padding:0 0 1em 210px;line-height:1.6em;">'.$id.'</div>';
    		
    		if($this->_is15){
    			$_html .= '<label>'.$this->l('Shop').'</label>
    					<div class="margin-form">
							'.$name_shop.'
						</div>';
			}
			$_html .= '<label>'.$this->l('Language').'</label>
    					<div class="margin-form">
							'.$lang_for_testimonial.'
						</div>';
            $_html .= '<label>'.$this->l('IP').'</label>
    					<div class="margin-form">
							'.$ip.'
						</div>';

            $_html .= '<label>'.$this->l('Avatar:').'</label>
    					<div class="margin-form">';

            if(Tools::strlen($avatar)>0) {
                $_html .= '
            <span class="avatar-form">
            <img src="'.$base_dir.$path_img_cloud.$avatar.'" />
            </span>
            <br/>

            <a class="delete_product_image btn btn-default avatar-button15" href="javascript:void(0)"
               onclick = "delete_avatar('.$id.');"
               style="margin-top: 10px">
                '.$this->l('Delete avatar and use standart empty avatar').' <img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" />
            </a>';

            }else{
                $_html .= '<span class="avatar-form"><img src = "../modules/'.$this->name.'/views/img/avatar_m.gif" /></span>';
            }

            $_html .= '</div>';
    		
    		$_html .= '<label>'.$this->l('Name').':</label>
    					<div class="margin-form">
							<input type="text" name="name"  style="width:400px"
			                	   value="'.$name.'">
						</div>';
    		$_html .= '<label>'.$this->l('Email').':</label>
    					<div class="margin-form">
							<input type="text" name="email"  style="width:400px"
			                	   value="'.$email.'">
						</div>';


            if(Configuration::get($this->name.'is_webg')){
                $_html .= '<label>'.$this->l('Web:').'</label>
	    					<div class="margin-form">
								<input type="text" name="web"  style="width:200px"
				                	   value="'.$web.'">
							</div>';
            }
            if(Configuration::get($this->name.'is_companyg')){
                $_html .= '<label>'.$this->l('Company').':</label>
	    					<div class="margin-form">
								<input type="text" name="company"  style="width:200px"
				                	   value="'.$company.'">
							</div>';
            }
            if(Configuration::get($this->name.'is_addrg')){
                $_html .= '<label>'.$this->l('Address').':</label>
	    					<div class="margin-form">
								<input type="text" name="address"  style="width:200px"
				                	   value="'.$address.'">
							</div>';
            }
            if(Configuration::get($this->name.'is_countryg')){
                $_html .= '<label>'.$this->l('Country').':</label>
	    		<div class="margin-form">
	    			<input type="text" name="country"  style="width:200px" value="'.$country.'" />
	    		</div>';
            }

            if(Configuration::get($this->name.'is_cityg')){
                $_html .= '<label>'.$this->l('City').':</label>
	    		<div class="margin-form">
	    		<input type="text" name="city"  style="width:200px" value="'.$city.'" />
	    		</div>';
            }
    	
    		$_html .= '<label>'.$this->l('Message').':</label>
    					<div class="margin-form">
							<textarea name="message" cols="70" rows="10"  
			                	   >'.$message.'</textarea>
						</div>';

            $_html .= '<label>'.$this->l('Admin Response:').'</label>
    					<div class="margin-form">
							<textarea name="response" cols="80" rows="10"
			                	   >'.$response.'</textarea>
						</div>';

            $_html .= '
				<label>'.$this->l('Send "Admin Response" notification to the customer').'</label>
				<div class = "margin-form" >';

            $_html .= '<input type = "checkbox" name = "is_noti" id = "is_noti" value ="1" checked/>';

            $_html .= '</div>';

            $_html .= '<div class="clear"></div><br/>
				<label>'.$this->l('Display "Admin response" on the site').'</label>
				<div class = "margin-form" >';

            $_html .= '<input type = "checkbox" name = "is_show" id = "is_show" value ="1" '.(($is_show ==1)?'checked':'').'/>';

            $_html .= '</div><br/>';

    		/*$_html .= '<label>'.$this->l('Date Add').':</label>';
    		$_html .= '<div style="padding:0 0 1em 210px;line-height:1.6em;">'.$date.'</div>';*/

            $date_tmp = '';
            if(isset($date)){
                $date_tmp = strtotime($date);
                $date_tmp = date('Y-m-d H:i:s',$date_tmp);
            } else {
                $date_tmp = date('Y-m-d H:i:s');
            }

            $_html .= '<div class="clear"></div>';
            $_html .= $this->displayDateField('date_add', $date_tmp, $this->l('Date Add:'), $this->l('Format : YYYY-MM-DD HH:MM:SS'));

            if(version_compare(_PS_VERSION_, '1.5', '>')){
                $_html .= '<script type="text/javascript">
    	$(\'document\').ready( function() {


	    	if ($(".datepicker").length > 0){

	    	var dateObj = new Date();
			var hours = dateObj.getHours();
			var mins = dateObj.getMinutes();
			var secs = dateObj.getSeconds();
			if (hours < 10) { hours = "0" + hours; }
			if (mins < 10) { mins = "0" + mins; }
			if (secs < 10) { secs = "0" + secs; }
			var time = " "+hours+":"+mins+":"+secs;

	           $(".datepicker").datepicker({ prevText: \'\', nextText: \'\', dateFormat: \'yy-mm-dd\'+time});
	       	}
       	});
    	</script>';
            }

            #### publication date ####
    		
    		
    		$_html .= '
				<label>'.$this->l('Publish').'</label>
				<div class = "margin-form" >';
				
			$_html .= '<input type = "checkbox" name = "publish" id = "publish" value ="1" '.(($active ==1)?'checked':'').'/>';
				
			$_html .= '</div>';
				
			$_html .= '<label>&nbsp;</label>
						<div class = "margin-form"  style="margin-top:20px">
						<input type="submit" name="cancel_item" value="'.$this->l('Cancel').'" 
                		   class="button"  />&nbsp;&nbsp;&nbsp;
						<input type="submit" name="submit_item" value="'.$this->l('Save').'" 
                		   class="button"  />
                		  </div>';
			
    		$_html .= '</form>';
			
    		
    	} else {
    	
    	
    	$_html .= '<table class = "table" width = 100%>
			<tr>
				<th>'.$this->l('No.').'</th>';
    	
    	$_html .= '<th width = 50>'.$this->l('Lang').'</th>';
    		
    	if($this->_is15){
    		$_html .= '<th width = 100>'.$this->l('Shop').'</th>';
    	}
            $_html .= '<th>'.$this->l('Avatar').'</th>';
		$_html .= '<th>'.$this->l('Name').'</th>
				<th width = 100>'.$this->l('Email').'</th>';
            if(Configuration::get($this->name.'is_webg')){
                $_html .= '<th width = 100>'.$this->l('Web').'</th>';
            }
		$_html .=	'<th width = 100>'.$this->l('IP').'</th>
				<th width = "300">'.$this->l('Message').'</th>
				<th>'.$this->l('Date').'</th>
				<th>'.$this->l('Published').'</th>
				<th width = "44">'.$this->l('Action').'</th>
			</tr>';
    	
    	$start = (int)Tools::getValue("page");
		
		$_data = $obj_guestbook->getItems(array('start'=>$start,'step'=>$this->_step,'admin' => 1));

		$_data_translate = $this->translateItems();
		$page_translate = $_data_translate['page']; 

		$paging = $obj_guestbook->PageNav($start,$_data['count_all_reviews'],$this->_step, 
											array('admin' => 1,'currentIndex'=>$currentIndex,
												  'token' => $token,
												 'page'=>$page_translate)
											);
    	$i=0;
    	if(sizeof($_data['reviews'])>0){
		foreach($_data['reviews'] as $_item){
			$i++;
			$id = $_item['id'];
			$name = $_item['name'];
			$email = $_item['email'];
			$ip = $_item['ip'];
			$message = $_item['message'];
			$date = $_item['date_add'];
			$active = $_item['active'];

            $avatar = $_item['avatar'];
            $web = $_item['web'];
			
			$lang = $_item['id_lang'];
    		$data_lng = Language::getLanguage($lang);
			$lang_for_testimonial = $data_lng['iso_code'];

			if($this->_is15){
				$id_shop = $_data['reviews'][0]['id_shop'];
				
				$shops = Shop::getShops();
				$name_shop = '';
				foreach($shops as $_shop){
					$id_shop_lists = $_shop['id_shop'];
					if($id_shop == $id_shop_lists)
						$name_shop = $_shop['name'];
				}
			}
			
			$_html .= 
			'<tr>
			<td style = "color:black;">'.$id.'</td>';
			
			$_html .= '<td style="color:black">'.$lang_for_testimonial.'</td>';
	
			if($this->_is15){
				$_html .= '<td style="color:black">'.$name_shop.'</td>';
			}

            $_html .=      '<td><span class="avatar-list">';
            if(Tools::strlen($avatar)>0){
                $_html .= '<img src="'.$base_dir.$path_img_cloud.$avatar.'" />';

            } else {
                $_html .= '<img src = "../modules/'.$this->name.'/views/img/avatar_m.gif" />';
            }
            $_html .= '</span>
           </td>';

		$_html .= '<td style = "color:black;">'.$name.'</td>
			<td style = "color:black;">'.$email.'</td>';

            if(Configuration::get($this->name.'is_webg')){
                if(Tools::strlen($web)>0){
                    $_html .= '<td><a  style = "color:#996633;text-decoration:underline" href = "http://'.$web.'">http://'.$web.'</a></td>';
                } else {
                    $_html .= '<td>&nbsp;</td>';
                }
            }

			$_html .= '<td style = "color:black;">'.$ip.'</td>';
			
			$_html .= '<td style = "color:black;">'.(Tools::strlen($message)>50?Tools::substr($message,0,50)."...":$message).'</td>
			<td style = "color:black;">'.$date.'</td>';
			
			$_html .= '
			<td style = "color:black;">
			 <form action = "'.$_SERVER['REQUEST_URI'].'" method = "POST">';
			 if ($active == 1) {
					$_html .= '<input type = "submit" name = "unpublished" value = "Unpublish" class = "button unpublished"/>';
				 }
				 else{
					$_html .= '<input type = "submit" name = "published" value = "Publish" class = "button published"/>';
				 }
			$_html .= '</td>
			<td>
				
				 <input type = "hidden" name = "id" value = "'.$id.'"/>
				 <a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&edit_item&id='.(int)($id).'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a> 
				 <a href="'.$currentIndex.'&configure='.$this->name.'&token='.$token.'&delete_item&id='.(int)($id).'" title="'.$this->l('Delete').'"  onclick = "javascript:return confirm(\''.$this->l('Are you sure you want to remove this item?').'\');"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>'; 
				 $_html .= '</form>
			 </td>
			 </tr>';
			
			$_html .= '</tr>';
		}
    	
    	} else {
			$_html .= '<tr><td colspan="12" style="text-align:center;font-weight:bold;padding:10px">
			'.$this->l('Messages not found').'</td></tr>';	
			
		}
		
    	$_html .= '</table>
						';
    	if($i!=0){
    	$_html .= '<div style="margin:5px">';
    	$_html .= $paging;
    	$_html .= '</div>';
    	}
    	}
    	
    	
    	$_html .=	'</fieldset>'; 
		
		return $_html;
    }

    public function displayDateField($name, $value, $title, $description ) {
        $opt_defaults = array('class' => '', 'required' => false);
        $opt = $opt_defaults;

        $content = '<label > ' . $title . ' </label>
                                    <div class="margin-form" >
                                       <input type="text" name="' . $name . '" value="' . $value . '" class="datepicker ' . $opt['class'] . '" />';


        if (!is_null($description) && !empty($description)) {
            $content .= '<p class="preference_description">' . $description . '</p>';
        }

        $content .= '</div>';

        return $content;
    }


 public function _jsandcss(){
    	$_html = '';


     $_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/views/css/colorpicker.css" type="text/css" />';
     $_html .=  '<link rel="stylesheet" media="screen" type="text/css" href="../modules/'.$this->name.'/views/css/layout.css" />';
     $_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/colorpicker.js"></script>';
     $_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/eye.js"></script>';
     $_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/utils.js"></script>';
     $_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/layout.js?ver=1.0.2"></script>';




    $_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/views/css/blockguestbook.css" type="text/css" />';
     $_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/views/css/admin.css" type="text/css" />';

     $_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/admin.js"></script>';

     if(version_compare(_PS_VERSION_, '1.6', '>')){
         $_html .= $this->context->controller->addJqueryUI(array('ui.core', 'ui.datepicker'));
     } elseif(version_compare(_PS_VERSION_, '1.5', '>') && version_compare(_PS_VERSION_, '1.6', '<')){
         $_html .= '<link href="/js/jquery/ui/themes/base/jquery.ui.theme.css" rel="stylesheet" type="text/css" media="all" />
						<link href="/js/jquery/ui/themes/base/jquery.ui.core.css" rel="stylesheet" type="text/css" media="all" />
						<link href="/js/jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" media="all" />';

         $_html .= '<script type="text/javascript" src="/js/jquery/ui/jquery.ui.core.min.js"></script>
						<script type="text/javascript" src="/js/jquery/ui/jquery.ui.datepicker.min.js"></script>
						<script type="text/javascript" src="/js/jquery/ui/i18n/jquery.ui.datepicker-en.js"></script>';
     } /*elseif(version_compare(_PS_VERSION_, '1.4', '>') && version_compare(_PS_VERSION_, '1.5', '<')){
         $_html .= '<script type="text/javascript" src="/js/jquery/jquery-ui-1.8.10.custom.min.js"></script>
						<script type="text/javascript" src="/js/jquery/datepickerjquery-ui-personalized-1.6rc4.packed.js"></script>
						<script type="text/javascript" src="/js/jquery/datepicker/ui/i18n/ui.datepicker-uk.js"></script>';
     }*/



     $_html .= '<style type="text/css">';
     $_html .= '.update-button{border: 1px solid #EBEDF4;}';
     $_html .= '</style>';

    	return $_html;
    }

    public function renderTplGuestbook(){
    	return Module::display(dirname(__FILE__).'/blockguestbook.php', 'views/templates/front/blockguestbook.tpl');
    }
    

    
 public function translateItems(){
    	$page = $this->l('Page');
    	return array('page'=>$page,
    				 'subject'=>$this->l('New Post from Guestbook'),

                    'subject_response'=>$this->l('Response on the Guestbook Message'),
                    'company'=>$this->l('Company'),
                    'address'=>$this->l('Address'),
                    'country'=>$this->l('Country'),
                    'city'=>$this->l('City'),
                    'web'=>$this->l('Web address'),
                    'message'=>$this->l('Post'),

    	 			'meta_title_guestbook'=>$this->l('Guestbook'),
    				'meta_description_guestbook'=>$this->l('Guestbook'),
    				'meta_keywords_guestbook'=>$this->l('Guestbook'),

                    'msg1'=>$this->l('Please, choose the rating.'),
                    'msg2'=>$this->l('Please, enter the Name.'),
                    'msg3'=>$this->l('Please, enter the Email.'),
                    'msg4'=>$this->l('Please, enter the Message.'),
                    'msg5'=>$this->l('Please, enter the security code.'),
                    'msg6'=>$this->l('Please enter a valid email address. For example johndoe@domain.com.'),
                    'msg7'=>$this->l('You entered the wrong security code.'),
                    'msg8'=>$this->l('Invalid file type, please try again!'),
                    'msg9'=>$this->l('Wrong file format, please try again!'),
    				
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

               <br/><br/>';
        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $_html .= '</div>';
        }
        return $_html;
    }

    public function setSEOUrls(){
        $smarty = $this->context->smarty;
        include_once(dirname(__FILE__).'/classes/guestbook.class.php');
        $obj_guestbook = new guestbook();

        $data_url = $obj_guestbook->getSEOURLs();
        $guestbook_url = $data_url['guestbook_url'];

        $smarty->assign(
            array($this->name.'guestbook_url' => $guestbook_url,

            )
        );


        $smarty->assign($this->name.'is_webg', Configuration::get($this->name.'is_webg'));
        $smarty->assign($this->name.'rssong', Configuration::get($this->name.'rssong'));

        $smarty->assign($this->name.'is_avatarg', Configuration::get($this->name.'is_avatarg'));




        $smarty->assign($this->name.'is_ps15', $this->_is15);
        $smarty->assign($this->name.'is15', $this->_is15);
        $smarty->assign($this->name.'is16', $this->_is16);
        if($this->_is_friendly_url){
            $smarty->assign($this->name.'iso_lng', $this->_iso_lng);
        } else {
            $smarty->assign($this->name.'iso_lng', '');
        }

        if(version_compare(_PS_VERSION_, '1.5', '<')){
            $smarty->assign($this->name.'is14', 1);
        } else {
            $smarty->assign($this->name.'is14', 0);
        }

        $smarty->assign($this->name.'pic', $this->path_img_cloud);

        $smarty->assign($this->name.'is_urlrewrite', Configuration::get($this->name.'is_urlrewrite'));


    }
	
   
}


// stub for prestashop 1.7.4.0

if (!function_exists('idn_to_ascii'))
{
    function idn_to_ascii($email)
    {
        return $email;
    }
}

?>