<?php
if (!defined('_PS_VERSION_'))
    exit;

class nove_promexport extends Module
{
    private $_html = '';
    private $_postErrors = array();

    function __construct()
    {
        $this->name = 'nove_promexport';
        $this->tab = 'export';
        $this->version = '1.0.1';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 1;
        $this->bootstrap = true;


        parent::__construct();

        $this->displayName = $this->l('Prom.ua export');
        $this->description = $this->l('Export products to Prom.ua');

    }

    public function install()
    {
        return (parent::install()
            && Configuration::updateValue('nove_promexport_categories', serialize(array(56,8,98)))
            && Configuration::updateValue('nove_promexport_brands', 0)
            && Configuration::updateValue('nove_promexport_shop', Configuration::get('PS_SHOP_NAME'))
            && Configuration::updateValue('nove_promexport_code', '', true)
            && Configuration::updateValue('nove_promexport_sd', 1)
            && Configuration::updateValue('nove_promexport_qt', 1)
            && Configuration::updateValue('nove_promexport_qtu', 1)
            && Configuration::updateValue('nove_promexport_av', 1)
            && Configuration::updateValue('nove_promexport_comb', 0)
            && Configuration::updateValue('nove_promexport_supprefence', 0)
            && Configuration::updateValue('nove_promexport_p_refurbished', 0)

        );
    }

    public function uninstall()
    {
        return (parent::uninstall()
            && Configuration::deleteByName('nove_promexport_shop')
            && Configuration::deleteByName('nove_promexport_code')
            && Configuration::deleteByName('nove_promexport_categories')
            && Configuration::deleteByName('nove_promexport_brands')
            && Configuration::deleteByName('nove_promexport_sd')
            && Configuration::deleteByName('nove_promexport_av')
            && Configuration::deleteByName('nove_promexport_qt')
            && Configuration::deleteByName('nove_promexport_qtu')
            && Configuration::deleteByName('nove_promexport_comb')
            && Configuration::deleteByName('nove_promexport_supprefence')
            && Configuration::deleteByName('nove_promexport_p_refurbished')
        );
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitpromexport')) {
            Configuration::updateValue('nove_promexport_supprefence', Tools::getValue('supprefence'));
            Configuration::updateValue('nove_promexport_p_refurbished', Tools::getValue('p_refurbished'));
            $this->_postValidation();
            if (!sizeof($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors AS $err)
                    $this->_html .= $this->displayError($err);
        } elseif (Tools::isSubmit('generate')) {
            Configuration::updateValue('nove_promexport_supprefence', Tools::getValue('supprefence'));
            Configuration::updateValue('nove_promexport_p_refurbished', Tools::getValue('p_refurbished'));
            $this->generate(true, false);
            $this->_html .= $this->displayConfirmation($this->l('Generating completed.'));
        }
        $this->_displayForm();
        return $this->_html;
    }

    protected function _displayForm()
    {
        $this->_display = 'index';
        
        $brands = Manufacturer::getManufacturers();
        array_unshift($brands, array('manufacturer'=>0,'name'=>$this->l('All')));
        
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Настройки'),
                'image' => _PS_ADMIN_IMG_ . 'information.png'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Название магазина'),
                    'desc' => $this->l('Название магазина в Prom.UA'),
                    'name' => 'nove_promexport_shop',
                    'size' => 33,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('ID Магазина'),
                    'desc' => $this->l('ID Магазина в Prom.UA'),
                    'name' => 'nove_promexport_shop_id',
                    'size' => 33,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('GTM код'),
                    'desc' => $this->l('Код отслеживания в URL'),
                    'name' => 'nove_promexport_code',
                    'size' => 33,
                ),
                array(
                        'type' => 'categories',
                        'label' => $this->l('Категории'),
                        'name' => 'nove_promexport_categories',
                        'desc' => $this->l('Категории для экспорта.'),
                        'values' => array(
                            'trads' => array(
                                'selected' => $this->l('Выбранные'),
                                'Check All' => $this->l('Выбрать все'),
                                'Uncheck All'  => $this->l('Снять все'),
                                'Collapse All' => $this->l('Свернуть все'),
                                'Expand All' => $this->l('Развернуть все')
                            ),
                            'selected_cat' => unserialize(Configuration::get('nove_promexport_categories')),
                            'input_name' => 'nove_promexport_categories[]',
                            'use_radio' => false,
                            'use_search' => false,
                            'disabled_categories' => array(),
                            'top_category' => Category::getTopCategory(),
                            'use_context' => true,
                        ),
                        'tree' => array(
                            'id' => 'categories-tree',
                            'use_checkbox' => true,
                            'use_search' => false,
                            'selected_categories' => unserialize(Configuration::get('nove_promexport_categories')),
                            'input_name' => 'nove_promexport_categories[]',
                        )
                    ),
                    array(
          						'type' => 'select',
          						'label' => $this->l('Бренды:'),
                      'desc' => $this->l('Бренды для экспорта.'),
          						'name' => 'nove_promexport_brands[]',
                      'multiple' => true,
          						'class' => 'fixed-width-xxl',
          						'options' => array(
          							'query' => $brands,
          							'id' => 'id_manufacturer',
          							'name' => 'name'
          						),
          					),
                    array(
          					'type' => 'switch',
          					'label' => $this->l('Короткое описание как основное?'),
          					'name' => 'nove_promexport_sd',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'nove_promexport_sd_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'nove_promexport_sd_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				    ),
                    array(
          					'type' => 'switch',
          					'label' => $this->l('Не показывать товары с остактом 0'),
          					'name' => 'nove_promexport_qt',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'nove_promexport_qt_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'nove_promexport_qt_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				    ),
                    array(
          					'type' => 'switch',
          					'label' => $this->l('Считать все товары доступными к заказу'),
                    'desc' => $this->l('При выборе этой опции все товары бутут отмечены как есть в наличии. Иначе отметка будет ставиться согласно остатка.'),
          					'name' => 'nove_promexport_qtu',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'nove_promexport_qtu_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'nove_promexport_qtu_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				    ),
                    array(
          					'type' => 'switch',
          					'label' => $this->l('Не показывать товары запрещенные к продаже'),
          					'name' => 'nove_promexport_av',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'nove_promexport_av_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'nove_promexport_av_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				    ),
                    array(
          					'type' => 'switch',
          					'label' => $this->l('Не показывать комбинации товаров'),
          					'name' => 'nove_promexport_comb',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'nove_promexport_comb_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'nove_promexport_comb_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				    ),
            /*    array(
          					'type' => 'switch',
          					'label' => $this->l('Use supplier reference:'),
          					'name' => 'supprefence',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'supprefence_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'supprefence_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				  ),         */
                array(
          					'type' => 'switch',
          					'label' => $this->l('Not show refurbished:'),
          					'name' => 'p_refurbished',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'p_refurbished_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'p_refurbished_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				  )
            ),

            'submit' => array(
                'name' => 'submitpromexport',
                'title' => $this->l('Save'),
                'class'=> 'pull-right'
            )
        );

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Prom.UA export configuration information'),
                'image' => _PS_ADMIN_IMG_ . 'information.png'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Static url'),
                    'desc' => $this->l('URL to download file generated by cron or Export button.'),
                    'name' => 'url1',
                    'size' => 120,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Dinamic url'),
                    'desc' => $this->l('URL to download dinamicaly generated export file.'),
                    'name' => 'url2',
                    'size' => 120,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Cron url'),
                    'desc' => $this->l('URL to regenerate export file by cron.'),
                    'name' => 'url3',
                    'size' => 120,
                )
            ),
            'buttons' => array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Generate'),
                    'icon' => 'process-icon-update',
                    'name' => 'generate',
                    'id'   => 'generate',
                    'class'=> 'pull-right'
                ),
            )
        );
        $this->fields_value['nove_promexport_shop'] = Configuration::get('nove_promexport_shop');
        $this->fields_value['nove_promexport_code'] = Configuration::get('nove_promexport_code');
        $this->fields_value['nove_promexport_shop_id'] = Configuration::get('nove_promexport_shop_id');
        $this->fields_value['nove_promexport_brands[]'] = explode(",",Configuration::get('nove_promexport_brands'));
        $this->fields_value['nove_promexport_sd'] = Configuration::get('nove_promexport_sd');
        $this->fields_value['nove_promexport_qt'] = Configuration::get('nove_promexport_qt');
        $this->fields_value['nove_promexport_qtu'] = Configuration::get('nove_promexport_qtu');
        $this->fields_value['nove_promexport_comb'] = Configuration::get('nove_promexport_comb');
        $this->fields_value['nove_promexport_av'] = Configuration::get('nove_promexport_av');
        $this->fields_value['url1'] = 'https://' . Tools::getHttpHost(false, true) . _THEME_PROD_PIC_DIR_ . 'omprice.'.$this->context->shop->id.'.xml' . (Configuration::get('nove_promexport_gzip') ? '.gz' : '');
        $this->fields_value['url2'] = $this->context->link->getModuleLink('nove_promexport', 'generate', array(), true);
        $this->fields_value['url3'] = $this->context->link->getModuleLink('nove_promexport', 'generate', array('cron' => '1'), true);
        $this->fields_value['supprefence'] = Configuration::get('nove_promexport_supprefence');
        $this->fields_value['p_refurbished'] = Configuration::get('nove_promexport_p_refurbished');

        $helper = $this->initForm();
        $helper->submit_action = '';

        $helper->title = $this->displayName;

        $helper->fields_value = $this->fields_value;
        $this->_html .= $helper->generateForm($this->fields_form);
        return;
    }

    private function initForm()
    {
        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = 'promexport';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->toolbar_scroll = true;
        $helper->tpl_vars['version'] = $this->version;
        $helper->tpl_vars['author'] = $this->author;
        $helper->tpl_vars['this_path'] = $this->_path;

        return $helper;
    }

    private function _postValidation()
    {
        if (Tools::getValue('nove_promexport_shop') && (!Validate::isString(Tools::getValue('nove_promexport_shop'))))
            $this->_postErrors[] = $this->l('Invalid') . ' ' . $this->l('Shop Name');
        if (Tools::getValue('nove_promexport_shop_id') && (!Validate::isString(Tools::getValue('nove_promexport_shop_id'))))
            $this->_postErrors[] = $this->l('Invalid') . ' ' . $this->l('Shop Id');
        if (Tools::getValue('nove_promexport_categories') && (!is_array(Tools::getValue('nove_promexport_categories'))))
            $this->_postErrors[] = $this->l('Invalid') . ' ' . $this->l('Categories');
    }

    private function _postProcess()
    {
       
        Configuration::updateValue('nove_promexport_categories', serialize(Tools::getValue('nove_promexport_categories')));
        Configuration::updateValue('nove_promexport_brands', implode(",",Tools::getValue('nove_promexport_brands')));
        Configuration::updateValue('nove_promexport_sd', Tools::getValue('nove_promexport_sd'));
        Configuration::updateValue('nove_promexport_qt', Tools::getValue('nove_promexport_qt'));
        Configuration::updateValue('nove_promexport_qtu', Tools::getValue('nove_promexport_qtu'));
        Configuration::updateValue('nove_promexport_av', Tools::getValue('nove_promexport_av'));
        Configuration::updateValue('nove_promexport_comb', Tools::getValue('nove_promexport_comb'));
        Configuration::updateValue('nove_promexport_shop', Tools::getValue('nove_promexport_shop'));
        Configuration::updateValue('nove_promexport_code', Tools::getValue('nove_promexport_code'), true);
        Configuration::updateValue('nove_promexport_shop_id', Tools::getValue('nove_promexport_shop_id'));
        
        $this->_html .= $this->displayConfirmation($this->l('Settings updated.'));
    }


    public function generate($cron = false, $front = true)
    {
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $supprefactive = Configuration::get('nove_promexport_supprefence');
        $p_refurbished = Configuration::get('nove_promexport_p_refurbished');
        $p_qt = Configuration::get('nove_promexport_qt');
        $p_qtu = Configuration::get('nove_promexport_qtu');
        $p_sd = Configuration::get('nove_promexport_sd');
        $p_av = Configuration::get('nove_promexport_av');
        $p_comb = Configuration::get('nove_promexport_comb');
        $code = Configuration::get('nove_promexport_code');
        
        if (!$front)
          {
          $shopdomain = "http://".$this->context->shop->domain.$this->context->shop->physical_uri;
          }
       // dump($this->context->shop->id);
       // die();
        //Категории
        $categories = Category::getCategories($id_lang, false, false);
        $nove_promexport_categories = unserialize(Configuration::get('nove_promexport_categories'));
        if (Configuration::get('nove_promexport_brands')) $nove_promexport_brands = explode(",",Configuration::get('nove_promexport_brands'));
        else $nove_promexport_brands = 0;
        
        $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        require 'simp.php'; 
        $price_xml = new SimpleXMLElementExtended('<?xml version="1.0" encoding="UTF-8" ' . 'standalone="yes"?><yml_catalog date="'.date("Y-m-d H:i").'"/>');
        $shop_xml = $price_xml->addChild('shop');
        $shop_xml->addChild('name', Configuration::get('nove_promexport_shop'));
        $shop_xml->addChild('company', Configuration::get('nove_promexport_shop_id'));
        $shop_xml->addChild('url', $this->context->link->getPageLink('index',true));
        $shop_xml->addChild('platform', 'Novevision.com - Prom.ua');

        $cur_xml = $shop_xml->addChild('currencies');
        $cur_xmlc = $cur_xml->addChild('currency');
        $cur_xmlc->addAttribute('id', $currency_default->iso_code);
        $cur_xmlc->addAttribute('rate', (float)$currency_default->conversion_rate);

        $catalog_xml = $shop_xml->addChild('categories');
        foreach ($categories as $category) {
            if ($category['active'] && in_array($category['id_category'], $nove_promexport_categories)) {
                $category_xml = $catalog_xml->addChild('category', $category['name']);
                $category_xml->addAttribute('id', $category['id_category']);
                if ($category['id_parent'] > 2) {
                    $category_xml->addAttribute('parentId', $category['id_parent']);
                }
            }
        }
        $items_xml = $shop_xml->addChild('offers');
        foreach ($categories as $category) {
            if ($category['active'] && in_array($category['id_category'], $nove_promexport_categories)) {
                $category_object = new Category($category['id_category']);
                $products = $category_object->getProducts($id_lang, 1, 1000000);
                if ($products)
                    foreach ($products as $product) {
                  /*  if ($product['id_product'] == 6) {
                    dump($product);
                    die();
                    }*/         
                        if ($product['id_category_default'] != $category['id_category'])
                            {
                            continue;
                            }
                        if ( is_array($nove_promexport_brands) && !in_array($product['id_manufacturer'],$nove_promexport_brands)  )
                            {
                            continue;
                            }
                        if ($p_refurbished && $product['condition'] == 'refurbished')
                        {
                         continue;
                        }
                        if ($p_qt && $product['quantity'] == 0)
                        {
                         continue;
                        }
                        if ($p_av && $product['available_for_order'] == 0)
                        {
                         continue;
                        }
                        $product_object = new Product($product['id_product'], false, $id_lang);
                        $combinations = $product_object->getAttributeCombinations($id_lang);
                        
                        if (count($combinations) > 0 && !$p_comb) {

                            foreach ($combinations AS $combination) {
                              $combArray[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
							                $combArray[$combination['id_product_attribute']]['price'] = Product::getPriceStatic($product['id_product'], true, $combination['id_product_attribute']);
							                $combArray[$combination['id_product_attribute']]['price_without_reduction'] = Product::getPriceStatic($product['id_product'],false,$combination['id_product_attribute'],2,null,false,false);
                              $combArray[$combination['id_product_attribute']]['reduction'] = Product::getPriceStatic($product['id_product'],false,$combination['id_product_attribute'],2,null,true,true);
                              $combArray[$combination['id_product_attribute']]['reference'] = $combination['reference'];
							                $combArray[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
							                $combArray[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
							                $combArray[$combination['id_product_attribute']]['minimal_quantity'] = $combination['minimal_quantity'];
							                $combArray[$combination['id_product_attribute']]['weight'] = $combination['weight'];
							                $combArray[$combination['id_product_attribute']]['attributes'][$combination['group_name']] = $combination['attribute_name'];
                  							if (!isset($combArray[$combination['id_product_attribute']]['comb_url']))
                  							$combArray[$combination['id_product_attribute']]['comb_url'] = '';
                  							$combArray[$combination['id_product_attribute']]['comb_url'] .= '/'.$combination['id_attribute'].'-'.Tools::str2url($combination['group_name']).'-'.str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::str2url(str_replace(array(',', '.'), '-', $combination['attribute_name'])));
                  						}
                            
                            foreach ($combArray as $combination) {

                                $namecomb = '';
                                foreach ($combination['attributes'] as $comkey=>$conbname)
                                {
                                $namecomb .= $comkey.": ".$conbname."; ";
                                }
                                // Комбинация

                                $item_xml = $items_xml->addChild('offer');
                                $item_xml->addAttribute('id', $product_object->id."c".$combination['id_product_attribute']);
                                if ($p_qtu)
                                {
                                 $item_xml->addAttribute('available', 'true');
                                }
                                else
                                {
                                  if ($combination['quantity']>0) $item_xml->addAttribute('available', 'true');
                                  else $item_xml->addAttribute('available', 'false');
                                }
                                if ($code) $item_xml->addChild('url', $product['link']."#".$combination['comb_url'].$code);
                                else $item_xml->addChild('url', $product['link']."#".$combination['comb_url']);
                                $item_xml->addChild('price', Tools::ps_round($combination['price'], 2));
                                if ($combination['reduction']>0)
                                  {
                                   $item_xml->addChild('oldprice', Tools::ps_round($combination['price_without_reduction'], 2));
                                  }
                                $item_xml->addChild('currencyId', $currency_default->iso_code);
                                $item_xml->addChild('categoryId', $product['id_category_default']);
                                
                                /*image*/                                                             
                        				$images = self::getImages($product['id_product'], $id_lang);
                        				$l_i = 0;
                        				foreach ($images as $image_obj)
                        				{
                        					$id_images = $image_obj['id_image'];
                        					$img_path = Tools::getHttpHost(true).__PS_BASE_URI__.'img/p/';
                        					$length = Tools::strlen($id_images);
                        					for ($i = 0; $i < $length; $i++)
                        						$img_path .= $id_images[$i].'/';
                        					$main_img_path = $img_path .= $id_images.'.jpg';
                                  $item_xml->addChild('picture', $img_path);
                        					$l_i++;
                        					if ($l_i == 10)
                        						break;
                        				}
                      				  /*/image*/
                                
                                $item_xml->addChild('store', 'false');
                                $item_xml->addChild('pickup', 'true');
                                $item_xml->addChild('delivery', 'true');
                                $item_xml->addChildWithCDATA('name', $product['name']." ".$namecomb);
                                $item_xml->addChild('vendor',  htmlspecialchars( $product['manufacturer_name'] ) );
                                if ($p_sd) 
                                  {
                                    $item_xml->addChildWithCDATA('description', $product['description_short']);
                                  }
                                else
                                  {
                                    $item_xml->addChildWithCDATA('description', $product['description']);
                                  }
                                if ($product['virtual']) $item_xml->addChild('downloadable', 'true');
                                else $item_xml->addChild('downloadable', 'false');
                                $param_xml = $item_xml->addChildWithCDATA('param', $product['unity']);
                                $param_xml->addAttribute('name', 'Фасовка');
                                if ($product['features']) {
                                    foreach ($product['features'] as $feature) {
                                        $param_xml = $item_xml->addChild('param', $feature['value']);
                                        $param_xml->addAttribute('name', $feature['name']);
                                    }
                                } 
                            }
                            unset ($combArray);

                        }else{

                            // товар

                            $sup_ref = ProductSupplier::getProductSupplierReference($product['id_product'],0,$product['id_supplier']);
                            $item_xml = $items_xml->addChild('item'); 
                            $item_xml->addAttribute('id', $product['id_product']);
                            if ($p_qtu)
                                {
                                 $item_xml->addAttribute('available', 'true');
                                }
                                else
                                {
                                  if ($product['quantity']>0) $item_xml->addAttribute('available', 'true');
                                  else $item_xml->addAttribute('available', 'false');
                                }
                            if (!$front)
                                  {
                                  $linkp = $shopdomain.$product['category']."/".$product['id_product']."-".$product['link_rewrite'].".html";
                                  $item_xml->addChild('url', $linkp);
                                  }
                                else
                                  {
                                  if ($code) $item_xml->addChild('url', $product['link'].$code);
                                  else $item_xml->addChild('url', $product['link']);
                                  }
                            $item_xml->addChild('price', Tools::ps_round($product['price'], 2));
                            if ($product['reduction']>0)
                            {
                             $item_xml->addChild('oldprice', Tools::ps_round($product['price_without_reduction'], 2));
                            }
                            
                            if ( $product['price'] > $product['wholesale_price'] && $product['opt_kol'] > 0 && $product['wholesale_price'] > $product['mrс'] )
                            {
                                $item_xml->addChild('opt_price', Tools::ps_round($product['wholesale_price'], 2));
                                $item_xml->addChild('opt_kol', $product['opt_kol']);
                            }
                            
                            
                            $item_xml->addChild('currencyId', $currency_default->iso_code);
                            $item_xml->addChild('reference', $product['reference']);
                            $item_xml->addChild('ean13', $product['ean13']);
                            $item_xml->addChild('categoryId', $product['id_category_default']);
                            
                             /*image*/
                        				$images = self::getImages($product['id_product'], $id_lang);
                        				$l_i = 0;
                        				foreach ($images as $image_obj)
                        				{
                        					$id_images = $image_obj['id_image'];
                        					$img_path = Tools::getHttpHost(true).__PS_BASE_URI__.'img/p/';
                        					$length = Tools::strlen($id_images);
                        					for ($i = 0; $i < $length; $i++)
                        						$img_path .= $id_images[$i].'/';
                        					$main_img_path = $img_path .= $id_images.'.jpg';
                                  $item_xml->addChild('picture', $img_path);
                        					$l_i++;
                        					if ($l_i == 10)
                        						break;
                        				}
                      				  /*/image*/
                                
                            $item_xml->addChild('store', 'false');
                            $item_xml->addChild('pickup', 'true');
                            $item_xml->addChild('delivery', 'true');
                            $item_xml->addChildWithCDATA('name', $product['name']);    
                            $item_xml->addChild('vendor', htmlspecialchars( $product['manufacturer_name'] ) );
                            /*
                            if ($p_sd) 
                                  {
                                    $item_xml->addChildWithCDATA('description', $product['description_short']);
                                  }
                                else
                                  {
                                    $item_xml->addChildWithCDATA('description', $product['description']);
                                  }    
                            */
                            if ($product['virtual']) $item_xml->addChild('downloadable', 'true');
                            else $item_xml->addChild('downloadable', 'false');
                            $param_xml = $item_xml->addChildWithCDATA('param', $product['unity']);
                            $param_xml->addAttribute('name', 'Фасовка');
                            if ($product['features']) {
                                    foreach ($product['features'] as $feature) {
                                        $param_xml = $item_xml->addChild('param', $feature['value']);
                                        $param_xml->addAttribute('name', $feature['name']);
                                    }
                                }    
                                                              
                        }
                                
                        
                        
                        
                    }
            }
        }

        if ($cron)
        {
            if ($fp = fopen(dirname(__FILE__) . '/../../upload/omprice.'.$this->context->shop->id.'.xml', 'w'))
            {
                fwrite($fp, $price_xml->asXML());
                fclose($fp);
            }
            die();
        } else {
            header("Content-type: text/xml; charset=utf-8");
            print $price_xml->asXML();
            die();
        }
    }
function mb_str_replace($needle, $replacement, $haystack)
{
    $needle_len = mb_strlen($needle);
    $replacement_len = mb_strlen($replacement);
    $pos = mb_strpos($haystack, $needle);
    while ($pos !== false)
    {
        $haystack = mb_substr($haystack, 0, $pos) . $replacement
                . mb_substr($haystack, $pos + $needle_len);
        $pos = mb_strpos($haystack, $needle, $pos + $replacement_len);
    }
    return $haystack;
}
public function ru2lat($str)
{
	$tr = array(
		"А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g", "Д"=>"d",
		"Е"=>"e", "Ё"=>"yo", "Ж"=>"zh", "З"=>"z", "И"=>"i",
		"Й"=>"j", "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n",
		"О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s", "Т"=>"t",
		"У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"ts", "Ч"=>"ch",
		"Ш"=>"sh", "Щ"=>"sch", "Ъ"=>"", "Ы"=>"y", "Ь"=>"",
		"Э"=>"e", "Ю"=>"yu", "Я"=>"ya", "а"=>"a", "б"=>"b",
		"в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ё"=>"yo",
		"ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"j", "к"=>"k",
		"л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p",
		"р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f",
		"х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch",
		"ъ"=>"", "ы"=>"y", "ь"=>"", "э"=>"e", "ю"=>"yu",
		"я"=>"ya"
	);
	return strtr($str,$tr);
}

 public static function getImages($id_proiduct, $id_lang)
	{
		return Db::getInstance()->executeS('
			SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
			FROM `'._DB_PREFIX_.'image` i
			'.Shop::addSqlAssociation('image', 'i').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			WHERE i.`id_product` = '.(int)$id_proiduct.'
			ORDER BY `position`'
		);
	}
  
	public function fix_cyrillic( $str ) {
			$tr = [
				'с' => 'c', 'С' => 'C'
			];
		return strtr($str,$tr);
	}

	public function fix_dots( $str ) {
		$tr = [
			',' => '', '.' => ''
		];
		return strtr($str,$tr);
	}
}