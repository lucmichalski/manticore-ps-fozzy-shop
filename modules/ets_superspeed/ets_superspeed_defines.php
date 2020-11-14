<?php
/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
	exit;
class Ets_superspeed_defines extends Module
{
    public $_admin_tabs=array();
    public $_hooks=array();
    public $_dynamic_hooks=array();
    public $_config_images=array();
    public $_datas_dynamic=array();
    public $_config_gzip=array();
    public $_cache_page_tabs=array();
    public $_cache_image_tabs=array();
    public function __construct()
	{
        $this->name= 'ets_superspeed';
	    $this->context = Context::getContext();
        $this->_module = Module::getInstanceByName('ets_superspeed');
        $this->_admin_tabs=array(
            array(
                'class_name' => 'AdminSuperSpeedStatistics',
                'tab_name' => $this->l('Dashboard','ets_superspeed_defines'),
                'icon'=>'icon icon-dashboard'
            ),
            array(
                'class_name' => 'AdminSuperSpeedPageCaches',
                'tab_name' => $this->l('Page cache','ets_superspeed_defines'),
                'icon'=>'icon icon-pagecache'
            ),
            array(
                'class_name' => 'AdminSuperSpeedImage',
                'tab_name' => $this->l('Image optimization','ets_superspeed_defines'),
                'icon'=>'icon icon-speedimage'
            ),
            array(
                'class_name' => 'AdminSuperSpeedMinization',
                'tab_name' => $this->l('Server cache and minification','ets_superspeed_defines'),
                'icon'=>'icon icon-speedminization'
            ),
            array(
                'class_name' => 'AdminSuperSpeedGzip',
                'tab_name' => $this->l('Browser cache and Gzip','ets_superspeed_defines'),
                'icon'=>'icon icon-speedgzip'
            ),
            array(
                'class_name' => 'AdminSuperSpeedDatabase',
                'tab_name' => $this->l('Database optimization','ets_superspeed_defines'),
                'icon'=>'icon icon-speeddatabase'
            ),
            array(
                'class_name' => 'AdminSuperSpeedSystemAnalytics',
                'tab_name' => $this->l('System Analytics','ets_superspeed_defines'),
                'icon'=>'icon icon-analytics'
            ),
            array(
                'class_name' => 'AdminSuperSpeedHelps',
                'tab_name' => $this->l('Help','ets_superspeed_defines'),
                'icon'=>'icon icon-help'
            ),
        );
        $intro = true;
        $localIps = array(
            '127.0.0.1',
            '::1'
        );
		$baseURL = Tools::strtolower(self::getBaseLink());
		if(!Tools::isSubmit('intro') && (in_array(Tools::getRemoteAddr(), $localIps) || preg_match('/^.*(localhost|demo|dev|test|:\d+).*$/i', $baseURL)))
		    $intro = false;
		if($intro)
		     $this->_admin_tabs[] = array(
                'tab_name' => $this->l('Other modules','ets_superspeed_defines'),
                'subtitle' => $this->l('Made by ETS-Soft','ets_superspeed_defines'),
                'custom_a_class' => 'link_othermodules',
                'custom_li_class' => 'li_othermodules',
                'class_name' => 'othermodules',
                'other_modules_link' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=ets_superspeed&othermodules=1',
            );
        $this->_hooks=array(
            'actionCategoryAdd',
            'actionProductUpdate',
            'actionCategoryUpdate',
            'actionHtaccessCreate',
            'actionWatermark',
            'displayAdminLeft',
            'displayBackOfficeHeader',
            'header',
            'actionPageCacheAjax',
            'actionObjectAddAfter',
            'actionObjectUpdateAfter',
            'hookActionObjectDeleteAfter',
            'hookActionObjectProductUpdateAfter',
            'hookActionObjectProductAddAfter',
            'hookActionObjectProductDeleteAfter',
            'hookActionObjectCategoryUpdateAfter',
            'hookActionObjectCategoryAddAfter',
            'hookActionObjectCategoryDeleteAfter',
            'actionModuleUnRegisterHookAfter',
            'actionModuleRegisterHookAfter',
            'actionOutputHTMLBefore',
            'actionAdminPerformanceControllerSaveAfter',
            'actionValidateOrder',
            'actionObjectCMSCategoryUpdateAfter',
            'actionObjectCMSCategoryDeleteAfter',
            'displayImagesBrowse',
            'displayImagesUploaded',
            'displayImagesCleaner',
            'actionUpdateBlogImage',
            'actionUpdateBlog',
        );
        $this->_dynamic_hooks=array(
            'displaytop',
            'displaynav',
            'displaynav1',
            'displaynav2',
            'displaytopcolumn',
            'displayhome',
            'displayhometab',
            'displaybanner',
            'displayhometabcontent',
            'displayrightcolumn',
            'displayrightcolumnproduct',
            'displayfooterproduct',
            'displayproductbuttons',
            'displayleftcolumn',
            'displayfooter',
            'displayCart',
            'displayRecommendProduct'
        );
        $this->_datas_dynamic=array(
            'connections'=>array(
                'table'=>'connections',
                'name' =>$this->l('Connections log','ets_superspeed_defines'),
                'desc' => $this->l('The records including info of every connections to your website (each visitor is a connection)','ets_superspeed_defines'),
                'where'=>'',
            ),
            'connections_source'=> array(
                'table'=>'connections_source',
                'name' =>$this->l('Page views','ets_superspeed_defines'),
                'desc' => $this->l('Measure the total number of views a particular page has received','ets_superspeed_defines'),
                'where'=>'',
            ),
            'cart_rule'=>array(
                'table'=>'cart_rule',
                'name' =>$this->l('Useless discount codes','ets_superspeed_defines'),
                'desc' => $this->l('Expired discount codes','ets_superspeed_defines'),
                'where'=>' WHERE date_to!="0000-00-00 00:00:00" AND date_to  < "'.pSQL(date('Y-m-d H:i:s')).'"',
                'table2'=>'specific_price',
                'where2'=>' WHERE `to` !="0000-00-00 00:00:00" AND `to`  < "'.pSQL(date('Y-m-d H:i:s')).'"',
            ),
            'cart'=>array(
                'table'=>'cart',
                'name' =>$this->l('Abandoned carts','ets_superspeed_defines'),
                'desc' => $this->l('The online cart that a customer added items to, but exited the website without purchasing those items','ets_superspeed_defines'),
                'where'=>' WHERE id_cart IN (SELECT id_cart FROM '._DB_PREFIX_.'orders) AND date_add < "'.pSQL(date('Y-m-d H:i:s',strtotime('-3 DAY'))).'"',
            ),
            'guest'=>array(
                'table'=>'guest',
                'name' =>$this->l('Guest data','ets_superspeed_defines'),
                'desc' => $this->l('Information of unregistered users (excluding users having orders)','ets_superspeed_defines'),
                'where'=>' WHERE id_customer=0',
            ),
        );
        $this->_config_gzip=array(
			array(
				'type' => 'switch',
				'label' => $this->l('Enable browser cache and Gzip','ets_superspeed_defines'),
				'name' => 'PS_HTACCESS_CACHE_CONTROL',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('On','ets_superspeed_defines')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('Off','ets_superspeed_defines')
					)
				),
                'desc'=> $this->l('Store several resources locally on web browser (images, icons, web fonts, etc.)','ets_superspeed_defines'),
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Use default Prestashop settings','ets_superspeed_defines'),
				'name' => 'ETS_SPEED_USE_DEFAULT_CACHE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes','ets_superspeed_defines')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No','ets_superspeed_defines')
					)
				),
                'form_group_class'=>'enable_cache',
                'desc'=> $this->l('Apply default Prestashop settings for browser cache and Gzip','ets_superspeed_defines'),
			),
            array(
                'type'=>'range',
                'label'=>$this->l('Browser cache image lifetime','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_LIFETIME_CACHE_IMAGE',
                'min'=>'1',
                'max'=>'30',
                'unit'=> $this->l('Day','ets_superspeed_defines'),
                'units'=> $this->l('Days','ets_superspeed_defines'),
                'form_group_class'=>'use_default form_group_range_small',
                'hint' => $this->l('Specify how long web browsers should keep images stored locally','ets_superspeed_defines'),
            ),
            array(
                'type'=>'range',
                'label'=>$this->l('Browser cache icon lifetime','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_LIFETIME_CACHE_ICON',
                'min'=>'1',
                'max'=>'10',
                'unit'=> $this->l('Year','ets_superspeed_defines'),
                'units'=> $this->l('Years','ets_superspeed_defines'),
                'form_group_class'=>'use_default form_group_range_small',
                'hint' => $this->l('Specify how long web browsers should keep icons stored locally','ets_superspeed_defines'),
            ),
            array(
                'type'=>'range',
                'label'=>$this->l('Browser cache css lifetime','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_LIFETIME_CACHE_CSS',
                'min'=>'1',
                'max'=>'48',
                'unit'=> $this->l('Week','ets_superspeed_defines'),
                'units'=> $this->l('Weeks','ets_superspeed_defines'),
                'form_group_class'=>'use_default form_group_range_small',
                'hint' => $this->l('Specify how long web browsers should keep CSS stored locally','ets_superspeed_defines'
            ),
            ),
            array(
                'type'=>'range',
                'label'=>$this->l('Browser cache js lifetime','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_LIFETIME_CACHE_JS',
                'min'=>'1',
                'max'=>'48',
                'unit'=> $this->l('Week','ets_superspeed_defines'),
                'units'=> $this->l('Weeks','ets_superspeed_defines'),
                'form_group_class'=>'use_default form_group_range_small',
                'hint' => $this->l('Specify how long web browsers should keep JavaScript files stored locally'),
            ),
            array(
                'type'=>'range',
                'label'=>$this->l('Browser cache font lifetime','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_LIFETIME_CACHE_FONT',
                'min'=>'1',
                'max'=>'10',
                'unit'=> $this->l('Year','ets_superspeed_defines'),
                'units'=> $this->l('Years','ets_superspeed_defines'),
                'form_group_class'=>'use_default form_group_range_small',
                'hint' => $this->l('Specify how long web browsers should keep text fonts stored locally'),
            )
		);
        $lazys =array(
            array(
                'value' => 'product_list',
                'label' => $this->l('Product listing','ets_superspeed_defines')
            )
        );
        if($this->_module->isSlide)
        {
            $lazys[] = array(
                'value' => 'home_slide',
                'label' => $this->l('Home slider','ets_superspeed_defines')
            );
        }
        if($this->_module->isBanner)
        {
            $lazys[] = array(
                'value' => 'home_banner',
                'label' => $this->l('Home banner','ets_superspeed_defines')
            );
        }
        if(Module::isInstalled('themeconfigurator') && Module::isEnabled('themeconfigurator'))
        {
            $lazys[] = array(
                'value' => 'home_themeconfig',
                'label' => $this->l('Home theme configurator','ets_superspeed_defines')
            );
        }
        $this->_config_images=array(
            array(
				'type' => 'switch',
				'label' => $this->l('Optimize newly uploaded images','ets_superspeed_defines'),
				'name' => 'ETS_SPEED_OPTIMIZE_NEW_IMAGE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes','ets_superspeed_defines')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No','ets_superspeed_defines')
					)
				),
                'form_group_class'=>'form_cache_page image_new',
                'desc' => $this->l('This will affect all new images uploaded in the future','ets_superspeed_defines'),
			),
            array(
				'type' => 'switch',
				'label' => $this->l('Enable lazy load','ets_superspeed_defines'),
				'name' => 'ETS_SPEED_ENABLE_LAYZY_LOAD',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes','ets_superspeed_defines')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No','ets_superspeed_defines')
					)
				),
                'form_group_class'=>'form_cache_page image_lazy_load',
			),
            array(
                'type' => 'radio',
				'label' => $this->l('Preloading image','ets_superspeed_defines'),
				'name' => 'ETS_SPEED_LOADING_IMAGE_TYPE',
                'default' => 'type_1',
				'values' => array(
					array(
						'id' => 'type_1',
						'value' => 'type_1',
						'label' => $this->l('Type 1','ets_superspeed_defines'),
                        'html' => '<' . 'd' . 'i' . 'v' . ' class="spinner_1"' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>'
					),
					array(
						'id' => 'type_2',
						'value' => 'type_2',
						'label' => $this->l('Type 2','ets_superspeed_defines'),
                        'html' => '<' . 'd' . 'i' . 'v' . ' class="lds-ring"' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>'
					),
                    array(
                        'id' => 'type_3',
                        'value' => 'type_3',
                        'label' => $this->l('Type 3','ets_superspeed_defines'),
                        'html' => '<' . 'd' . 'i' . 'v' . ' class="lds-roller"' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . '' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>'
                    ),
                    array(
                        'id' => 'type_4',
                        'value' => 'type_4',
                        'label' => $this->l('Type 4','ets_superspeed_defines'),
                        'html' => '<' . 'd' . 'i' . 'v' . ' class="lds-ellipsis"' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'iv' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '><' . 'd' . 'iv' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>'
                    ),
                    array(
                        'id' => 'type_5',
                        'value' => 'type_5',
                        'label' => $this->l('Type 5','ets_superspeed_defines'),
                        'html' => '<' . 'd' . 'i' . 'v' . ' class="lds-spinner"' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'iv' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '></' . 'd' . 'i' . 'v' . '><' . 'di' . 'v' . '>' . '</' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'iv' . '>' . '<' . 'di' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>' . '<' . 'd' . 'i' . 'v' . '>' . '</' . 'd' . 'i' . 'v' . '>' . '<' . '/' . 'd' . 'i' . 'v' . '>'
                    ),
				),
                'form_group_class'=>'form_cache_page image_lazy_load type',
            ),
            array(
                'type' => 'checkbox',
                'label' => $this ->l('Enable Lazy Load for','ets_superspeed_defines'),
                'name' => 'ETS_SPEED_LAZY_FOR',
                'values' => array(
                    'query'=> $lazys,
                    'id' => 'value',
                    'name' => 'label',
                ),
                'form_group_class'=>'form_cache_page image_lazy_load',
            ),
            array(
                'type'=>'checkbox',
                'label' => $this->l('Product images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_PRODUCT_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('products'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('products',true),
                'form_group_class'=>'form_cache_page image_new',
            ),
            array(
                'type'=>'checkbox',
                'label' => $this->l('Product category images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_CATEGORY_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('categories'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('categories',true),
                'form_group_class'=>'form_cache_page image_new',
            ),
            array(
                'type'=>'checkbox',
                'label' => $this->l('Supplier images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_SUPPLIER_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('suppliers'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('suppliers',true),
                'form_group_class'=>'form_cache_page image_new',
            ),
            array(
                'type'=>'checkbox',
                'label' => $this->l('Manufacturer images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_MANUFACTURER_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('manufacturers'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('suppliers',true),
                'form_group_class'=>'form_cache_page image_new',
            )
        );
        if($this->_module->isblog)
        {
            $blog_images = array(
                array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog post images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_BLOG_POST_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_post'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_post',true),
                    'form_group_class'=>'form_cache_page image_new',
                ),
                array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog category images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_BLOG_CATEGORY_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_category'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_category',true),
                    'form_group_class'=>'form_cache_page image_new',
                ),
                array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog gallery & slider images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_BLOG_GALLERY_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_gallery'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_gallery',true),
                    'form_group_class'=>'form_cache_page image_new blog_gallery',
                ),
                array(
                    'type'=>'checkbox',
                    'label' => $this->l('Slider images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_NEW_IMAGE_BLOG_SLIDE_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_slide'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_slide',true),
                    'form_group_class'=>'form_cache_page image_new blog_slide',
                )
            );
            $this->_config_images = array_merge($this->_config_images,$blog_images);
        }
        if($this->getImageTypes('products',false,true))
            $this->_config_images[]=array(
                'type'=>'checkbox',
                'label' => $this->l('Product images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_PRODCUT_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('products'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('products',true),
                'image_old'=>'product',
                'form_group_class'=>'form_cache_page image_old',
            );
        if($this->getImageTypes('categories',false,true))
            $this->_config_images[]= array(
                'type'=>'checkbox',
                'label' => $this->l('Product category images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_CATEGORY_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('categories'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('categories',true),
                'image_old'=>'category',
                'form_group_class'=>'form_cache_page image_old',
            );
        if($this->getImageTypes('suppliers',false,true))
            $this->_config_images[]= array(
                'type'=>'checkbox',
                'label' => $this->l('Supplier images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_SUPPLIER_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('suppliers'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('suppliers',true),
                'image_old'=>'supplier',
                'form_group_class'=>'form_cache_page image_old',
        );
        if($this->getImageTypes('manufacturers',false,true))
            $this->_config_images[]=array(
                'type'=>'checkbox',
                'label' => $this->l('Manufacturer images','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_MANUFACTURER_TYPE',
                'values' => array(
                     'query' => $this->getImageTypes('manufacturers'), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('manufacturers',true),
                'image_old'=>'manufacturer',
                'form_group_class'=>'form_cache_page image_old manufacturer',
            );
        
        if($this->_module->isblog)
        {
            if($this->getImageTypes('blog_post',false,true))
                $this->_config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog post images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_BLOG_POST_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_post'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_post',true),
                    'image_old'=>'blog_post',
                    'form_group_class'=>'form_cache_page image_old blog_post',
                );
            if($this->getImageTypes('blog_category',false,true))
                $this->_config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog category images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_category'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_category',true),
                    'image_old'=>'blog_category',
                    'form_group_class'=>'form_cache_page image_old blog_category',
                );
            if($this->getImageTypes('blog_gallery',false,true))
                $this->_config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog gallery & slider images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_gallery'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_gallery',true),
                    'image_old'=>'blog_gallery',
                    'form_group_class'=>'form_cache_page image_old blog_gallery',
                );
            if($this->getImageTypes('blog_slide',false,true))
                $this->_config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Slider images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('blog_slide'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('blog_slide',true),
                    'image_old'=>'blog_slide',
                    'form_group_class'=>'form_cache_page image_old blog_slide',
                );
        }
        if($this->_module->isSlide)
        {
            if($this->getImageTypes('home_slide',false,true))
                $this->_config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Home slider images','ets_superspeed_defines'),
                    'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE',
                    'values' => array(
                         'query' => $this->getImageTypes('home_slide'), 
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'default'=> $this->getImageTypes('home_slide',true),
                    'image_old'=>'home_slide',
                    'form_group_class'=>'form_cache_page image_old home_slide',
            );
        }
        $this->_config_images[] = array(
            'type'=>'checkbox',
            'label' => $this->l('Others images','ets_superspeed_defines'),
            'name'=>'ETS_SPEED_OPTIMIZE_IMAGE_OTHERS_TYPE',
            'values' => array(
                 'query' => $this->getImageTypes('others'), 
                 'id' => 'value',
                 'name' => 'label'                                                               
            ),
            'default'=> $this->getImageTypes('others',true),
            'image_old'=>'others',
            'form_group_class'=>'form_cache_page image_old others',
        );
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );
        $optimize_type = array(
            'type'=>'select',
            'label'=>$this->l('Image optimization method','ets_superspeed_defines'),
            'name'=>'ETS_SPEED_OPTIMIZE_SCRIPT',
            'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>'php',
                            'name' => $this->l('PHP image optimization script','ets_superspeed_defines')
                        ),
                        array(
                            'id_option' =>'resmush',
                            'name' => $this->l('Resmush - Free image optimization web service API','ets_superspeed_defines')
                        ),
                        array(
                            'id_option' =>'tynypng',
                            'name' => $this->l('TinyPNG - Premium image optimization web service API (500 images for free per month)','ets_superspeed_defines')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
            ),
            'form_group_class'=>'form_cache_page image_old',
            'default'=>'php',
		);
        if(in_array(Tools::getRemoteAddr(), $whitelist))
            unset($optimize_type['options']['query'][1]);
        $this->_config_images[]= $optimize_type;
        $optimize_type_new = array(
            'type'=>'select',
            'label'=>$this->l('Image optimization method','ets_superspeed_defines'),
            'name'=>'ETS_SPEED_OPTIMIZE_SCRIPT_NEW',
            'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>'php',
                            'name' => $this->l('PHP image optimization script','ets_superspeed_defines')
                        ),
                        array(
                            'id_option' =>'resmush',
                            'name' => $this->l('Resmush - Free image optimization web service API','ets_superspeed_defines')
                        ),
                        array(
                            'id_option' =>'tynypng',
                            'name' => $this->l('TinyPNG - Premium image optimization web service API (500 images for free per month)','ets_superspeed_defines')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
            ),
            'form_group_class'=>'form_cache_page image_new script',
            'default'=>'php',
		);
        if(in_array(Tools::getRemoteAddr(), $whitelist))
            unset($optimize_type_new['options']['query'][1]);
        $this->_config_images[]= $optimize_type_new;
        $this->_config_images[]= array(
                'type'=>'range',
                'label'=>$this->l('Image quality','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_QUALITY_OPTIMIZE',
                'min'=>'1',
                'max'=>'100',
                'unit' => '%',
                'units'=>'%',
                'form_group_class'=>'form_cache_page use_default form_group_range_small quality image_old',
                'desc' => $this->l('The higher image quality, the longer page loading time, 50% is recommended value. Setup image quality up to 100% will restore original images.','ets_superspeed_defines'),
                'default'=>50,
		); 
        $this->_config_images[]= array(
                'type'=>'range',
                'label'=>$this->l('Image quality','ets_superspeed_defines'),
                'name'=>'ETS_SPEED_QUALITY_OPTIMIZE_NEW',
                'min'=>'1',
                'max'=>'100',
                'unit' => '%',
                'units'=>'%',
                'form_group_class'=>'form_cache_page use_default form_group_range_small quality image_new',
                'desc' => $this->l('The higher image quality, the longer page loading time, 50% is recommended value. Setup image quality up to 100% will restore original images.','ets_superspeed_defines'),
                'default'=>50,
		);
        $this->_config_images[] = array(
				'type' => 'checkbox',
				'label' => '',
				'name' => 'ETS_SPEED_UPDATE_QUALITY',
                'values' => array(
                     'query' => array(
                        array(
                            'value' => 1,
                            'label' => $this->l('Do not reoptimize images that have been optimized with different image quality or image optimization method','ets_superspeed_defines'),
                        )
                     ), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'form_group_class'=>'form_cache_page image_old update_quality',
                'default' => 1,
		);
        $this->_cache_page_tabs=array(
            'page_setting'=> $this->l('Page cache settings','ets_superspeed_defines'),
            'dynamic_contents' => $this->l('Exceptions','ets_superspeed_defines'),
            'livescript' => $this->l('Live JavaScript','ets_superspeed_defines'),
            'page-list-caches' => $this->l('Cached URLs','ets_superspeed_defines'),
        );
        $this->_cache_image_tabs=array(
            'image_old' => $this->l('Existing images','ets_superspeed_defines'),
            'image_new'=> $this->l('New images','ets_superspeed_defines'),
            'image_upload'=> $this->l('Upload to optimize','ets_superspeed_defines'),
            'image_browse' => $this->l('Browse images','ets_superspeed_defines'),
            'image_cleaner' => $this->l('Image cleaner','ets_superspeed_defines'),
            'image_lazy_load' => $this->l('Lazy load','ets_superspeed_defines'),
        );
    }
    public static function getBaseLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$context->shop->domain.$context->shop->getBaseURI();
    }
    public function getImageTypes($type='',$string=false,$get_total=false)
    {
        if(Ets_superspeed::isInstalled('ets_superspeed'))
        {
            if(in_array($type,array('products','manufacturers','categories','suppliers')))
            {
                $sql = 'SELECT name as value,name as label FROM '._DB_PREFIX_.'image_type '.($type ? ' WHERE '.pSQL($type).'=1' :'' );
                $image_types = Db::getInstance()->executeS($sql);
                
            }
            elseif($type=='home_slide' && $this->_module->isSlide)
            {
                $image_types = array(
                    array(
                        'value'=> 'image',
                        'label' =>''
                    )
                );
            }
            elseif($type=='others')
            {
                $image_types = array(
                    array(
                        'value'=>'logo',
                        'label' => $this->l('Logo image','ets_superspeed_defines')
                    ),
                    array(
                        'value'=>'banner',
                        'label' => $this->l('Banner image','ets_superspeed_defines')
                    ),
                    array(
                        'value'=>'themeconfig',
                        'label' => $this->l('Theme configurator image','ets_superspeed_defines')
                    ),
                
                );
                if($this->_module->isSlide)
                {
                    $image_types[] = array(
                        'value'=>'home_slide',
                        'label' => $this->l('Home slider images','ets_superspeed_defines')
                    );
                }
            }
            elseif(in_array($type,array('blog_post','blog_category','blog_gallery','blog_slide')) &&  $this->_module->isblog)
            {
                $image_types = array(
                    array(
                        'value'=> 'image',
                        'label' => $this->l('Main image','ets_superspeed_defines')
                    ),
                    array(
                        'value'=> 'thumb',
                        'label' => $this->l('Thumb image','ets_superspeed_defines')
                    )
                );
                if($type=='blog_slide')
                {
                    $image_types= array(
                        array(
                            'value'=> 'image',
                            'label' =>''
                        ),
                    );
                }
                if($type=='blog_gallery')
                {
                    $image_types=array(
                        array(
                            'value'=> 'image',
                            'label' => $this->l('Main gallery image','ets_superspeed_defines')
                        ),
                        array(
                            'value'=> 'thumb',
                            'label' => $this->l('Thumb gallery image','ets_superspeed_defines')
                        ),
                        array(
                            'value'=> 'blog_slide',
                            'label' => $this->l('Slider images','ets_superspeed_defines')
                        )
                    );
                }
            }
            else
                $image_types=array();
            
            $total=0;
            if($string)
            {
                $images='';
                foreach($image_types as $image_type)
                {
                    $images .=','.$image_type['value'];
                }
                return trim($images,',');
            }
            else
            {
                if($image_types)
                {
                    foreach($image_types as &$image)
                    {
                        $total_image=0;
                        $total_image_optimized = 0;
                        switch($type){
                            case 'products':
                                $total_image_optimized = $this->_module->getTotalImage('product',false,true,true,false,$image['value']);
                                $image_product = $this->_module->getTotalImage('product',false,false,false,false,$image['value']);
                                $total_image =  $image_product- $total_image_optimized;
                                $total +=$image_product;
                                break;
                            case 'manufacturers':
                                $total_image_optimized = $this->_module->getTotalImage('manufacturer',false,true,true,false,$image['value']);
                                $image_manu = $this->_module->getTotalImage('manufacturer',false,false,false,false,$image['value']) ;
                                $total_image = $image_manu - $total_image_optimized;
                                $total +=$image_manu;
                                break;
                            case 'categories':
                                $total_image_optimized = $this->_module->getTotalImage('category',false,true,true,false,$image['value']);
                                $image_cate = $this->_module->getTotalImage('category',false,false,false,false,$image['value']);
                                $total_image =  $image_cate - $total_image_optimized;
                                $total +=$image_cate;
                                break;
                            case 'suppliers':
                                $total_image_optimized = $this->_module->getTotalImage('supplier',false,true,true,false,$image['value']);
                                $image_supplier = $this->_module->getTotalImage('supplier',false,false,false,false,$image['value']);
                                $total_image = $image_supplier - $total_image_optimized;
                                $total += $image_supplier;
                                break;
                            case 'blog_post' :
                                $total_image_optimized = $this->_module->getTotalImage('blog_post',false,true,true,false,$image['value']);
                                $image_post = $this->_module->getTotalImage('blog_post',false,false,false,false,$image['value']);
                                $total_image = $image_post - $total_image_optimized;
                                $total += $image_post;
                                break;
                            case 'blog_category' :
                                $total_image_optimized = $this->_module->getTotalImage('blog_category',false,true,true,false,$image['value']);
                                $image_blog_category = $this->_module->getTotalImage('blog_category',false,false,false,false,$image['value']);
                                $total_image = $image_blog_category - $total_image_optimized;
                                $total += $image_blog_category;
                                break;
                            case 'blog_gallery' :
                                if($image['value']=='blog_slide')
                                {
                                    $total_image_optimized = $this->_module->getTotalImage('blog_slide',false,true,true,false,'image');
                                    $image_blog_slide = $this->_module->getTotalImage('blog_slide',false,false,false,false,'image');
                                    $total_image = $image_blog_slide - $total_image_optimized;
                                    $total += $image_blog_slide;
                                }
                                else
                                {
                                    $total_image_optimized = $this->_module->getTotalImage('blog_gallery',false,true,true,false,$image['value']);
                                    $image_blog_gallery = $this->_module->getTotalImage('blog_gallery',false,false,false,false,$image['value']);
                                    $total_image = $image_blog_gallery - $total_image_optimized;
                                    $total += $image_blog_gallery;
                                }
                                break;
                            case 'blog_slide' :
                                $total_image_optimized = $this->_module->getTotalImage('blog_slide',false,true,true,false,$image['value']);
                                $image_blog_slide = $this->_module->getTotalImage('blog_slide',false,false,false,false,$image['value']);
                                $total_image = $image_blog_slide - $total_image_optimized;
                                $total += $image_blog_slide;
                                break;
                            case 'home_slide' :
                                $total_image_optimized = $this->_module->getTotalImage('home_slide',false,true,true,false,$image['value']);
                                $image_home_slide = $this->_module->getTotalImage('home_slide',false,false,false,false,$image['value']);
                                $total_image = $image_home_slide - $total_image_optimized;
                                $total += $image_home_slide;
                                break;
                            case 'others' :
                                if($image['value']=='home_slide')
                                {
                                    $total_image_optimized = $this->_module->getTotalImage('home_slide',false,true,true,false,$image['value']);
                                    $image_home_slide = $this->_module->getTotalImage('home_slide',false,false,false,false,$image['value']);
                                    $total_image = $image_home_slide - $total_image_optimized;
                                    $total += $image_home_slide;
                                }
                                else
                                {
                                    $total_image_optimized = $this->_module->getTotalImage('others',false,true,true,false,$image['value']);
                                    $image_others = $this->_module->getTotalImage('others',false,false,false,false,$image['value']);
                                    $total_image = $image_others - $total_image_optimized;
                                    $total += $image_others;
                                }
                                
                                break; 
                        }
                        $image['total_image'] = $total_image;
                        $image['total_image_optimized'] = $total_image_optimized;
                    }
                }
            }
            return $get_total ? $total : $image_types;
        }
        else    
            return false;
        
    }
}