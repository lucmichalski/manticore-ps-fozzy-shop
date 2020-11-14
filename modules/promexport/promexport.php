<?php
if (!defined('_PS_VERSION_'))
    exit;

class promexport extends Module
{
    private $_html = '';
    private $_postErrors = array();

    function __construct()
    {
        $this->name = 'promexport';
        $this->tab = 'export';
        $this->version = '0.1';
        $this->author = 'Novevision.com, Britoff A.';
        $this->need_instance = 0;
        $this->bootstrap = true;


        parent::__construct();

        $this->displayName = $this->l('Hotline export');
        $this->description = $this->l('export files in Hotline');

    }

    public function install()
    {
        return (parent::install()
            && Configuration::updateValue('promexport_categories', serialize(array()))
            && Configuration::updateValue('promexport_shop', Configuration::get('PS_SHOP_NAME'))
            && Configuration::updateValue('promexport_supprefence', 0)

        );
    }

    public function uninstall()
    {
        return (parent::uninstall()
            && Configuration::deleteByName('promexport_shop')
            && Configuration::deleteByName('promexport_categories')
            && Configuration::deleteByName('promexport_supprefence')
        );
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitpromexport')) {
            Configuration::updateValue('promexport_supprefence', Tools::getValue('supprefence'));
            $this->_postValidation();
            if (!sizeof($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors AS $err)
                    $this->_html .= $this->displayError($err);
        } elseif (Tools::isSubmit('generate')) {
            Configuration::updateValue('promexport_supprefence', Tools::getValue('supprefence'));
            $this->generate(true, false);
            $this->_html .= $this->displayConfirmation($this->l('Generating completed.'));
        }
        $this->_displayForm();
        return $this->_html;
    }

    protected function _displayForm()
    {
        $this->_display = 'index';


        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'image' => _PS_ADMIN_IMG_ . 'information.png'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Shop Name'),
                    'desc' => $this->l('Shop name in Hotlite'),
                    'name' => 'promexport_shop',
                    'size' => 33,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Shop ID'),
                    'desc' => $this->l('Shop ID in Hotlite'),
                    'name' => 'promexport_shop_id',
                    'size' => 33,
                ),

                array(
                        'type' => 'categories',
                        'label' => $this->l('Categories'),
                        'name' => 'promexport_categories',
                        'desc' => $this->l('Categories to export. If necessary, subcategories must be checked too.'),
                        'values' => array(
                            'trads' => array(
                                'Root' => $root_category,
                                'selected' => $this->l('Selected'),
                                'Check all' => $this->l('Check all'),
                                'Check All' => $this->l('Check All'),
                                'Uncheck All'  => $this->l('Uncheck All'),
                                'Collapse All' => $this->l('Collapse All'),
                                'Expand All' => $this->l('Expand All')
                            ),
                            'selected_cat' => $selected_cat,
                            'input_name' => 'promexport_categories[]',
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
                            'selected_categories' => unserialize(Configuration::get('promexport_categories')),
                            'input_name' => 'promexport_categories[]',
                        ),
                        'selected_cat' => unserialize(Configuration::get('promexport_categories')),
                    ),
            ),

            'submit' => array(
                'name' => 'submitpromexport',
                'title' => $this->l('Save'),
                'class'=> 'pull-right'
            )
        );

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Hotline export configuration information'),
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
                ),
                array(
          					'type' => 'switch',
          					'label' => $this->l('Use supplier reference:'),
          					'name' => 'supprefence',
          					'is_bool' => true,
          					'values' => array(
          						array(
          							'id' => 'offprod_on',
          							'value' => 1,
          							'label' => $this->l('Yes')),
          						array(
          							'id' => 'offprod_off',
          							'value' => 0,
          							'label' => $this->l('No')),
          					),
                    'validation' => 'isBool',
        				  ),
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

        $this->fields_value['promexport_shop'] = Configuration::get('promexport_shop');
        $this->fields_value['promexport_shop_id'] = Configuration::get('promexport_shop_id');


        $this->fields_value['url1'] = 'http://' . Tools::getHttpHost(false, true) . _THEME_PROD_PIC_DIR_ . 'promexport.xml' . (Configuration::get('promexport_gzip') ? '.gz' : '');
        $this->fields_value['url2'] = $this->context->link->getModuleLink('promexport', 'generate', array(), true);
        $this->fields_value['url3'] = $this->context->link->getModuleLink('promexport', 'generate', array('cron' => '1'), true);
        $this->fields_value['supprefence'] = Configuration::get('promexport_supprefence');

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
        if (Tools::getValue('promexport_shop') && (!Validate::isString(Tools::getValue('promexport_shop'))))
            $this->_postErrors[] = $this->l('Invalid') . ' ' . $this->l('Shop Name');
        if (Tools::getValue('promexport_shop_id') && (!Validate::isString(Tools::getValue('promexport_shop_id'))))
            $this->_postErrors[] = $this->l('Invalid') . ' ' . $this->l('Shop Id');
        if (Tools::getValue('promexport_categories') && (!is_array(Tools::getValue('promexport_categories'))))
            $this->_postErrors[] = $this->l('Invalid') . ' ' . $this->l('Categories');
    }

    private function _postProcess()
    {
        Configuration::updateValue('promexport_categories', serialize(Tools::getValue('promexport_categories')));
        Configuration::updateValue('promexport_shop', Tools::getValue('promexport_shop'));
        Configuration::updateValue('promexport_shop_id', Tools::getValue('promexport_shop_id'));

        $this->_html .= $this->displayConfirmation($this->l('Settings updated.'));
    }


    public function generate($cron = false, $front = true)
    {
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $supprefactive = Configuration::get('promexport_supprefence');
        if (!$front)
          {
          $shopdomain = "http://".$this->context->shop->domain.$this->context->shop->physical_uri;
          }
        
        //Категории
        $categories = Category::getCategories($id_lang, false, false);
        
        $promexport_categories = unserialize(Configuration::get('promexport_categories'));
        $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        require 'simp.php'; 
        $price_xml = new SimpleXMLElementExtended('<?xml version="1.0" encoding="UTF-8" ' . 'standalone="yes"?><yml_catalog date="'.date("Y-m-d H:i").'"/>');
        $shop_xml = $price_xml->addChild('shop');
        $shop_xml->addChild('name', Configuration::get('promexport_shop'));
        $shop_xml->addChild('company', Configuration::get('promexport_shop_id'));
        $shop_xml->addChild('url', $this->context->link->getPageLink('index',true));
        $shop_xml->addChild('region', $this->context->shop->name);
        $shop_xml->addChild('platform', 'prom_prestashop');

        $cur_xml = $shop_xml->addChild('currencies');
        $cur_xmlc = $cur_xml->addChild('currency');
        $cur_xmlc->addAttribute('id', $currency_default->iso_code);
        $cur_xmlc->addAttribute('rate', (float)$currency_default->conversion_rate);

        $catalog_xml = $shop_xml->addChild('categories');
        foreach ($categories as $category) {
            if ($category['active'] && in_array($category['id_category'], $promexport_categories)) {
                $category_xml = $catalog_xml->addChild('category', $category['name']);
                $category_xml->addAttribute('id', $category['id_category']);
                if ($category['id_parent'] > 1) {
                    $category_xml->addAttribute('parentId', $category['id_parent']);
                }
            }
        }
        $items_xml = $shop_xml->addChild('offers');
        
   //     foreach ($categories as $category) {
     //       if ($category['active'] && in_array($category['id_category'], $promexport_categories)) {
              //  $category_object = new Category($category['id_category']);
                //$products = $category_object->getProducts($id_lang, 1, 1000000);
                $products = Product::getPricesDrop($id_lang, 0, 1000000, false);
                
         //     d($products);
                if ($products)
                    foreach ($products as $product) {
                    //    if ($product['id_category_default'] != $category['id_category'])
                    //        continue;

                        $product_object = new Product($product['id_product'], false, $id_lang);
                        $combinations = $product_object->getAttributeCombinations($id_lang);

                        if (count($combinations) > 0) {

                            foreach ($combinations AS $combination) {
                              $combArray[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
							                $combArray[$combination['id_product_attribute']]['price'] = Product::getPriceStatic($product['id_product'], true, $combination['id_product_attribute']);
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
                /*                $product['description'] = $this->mb_str_replace('&nbsp;', ' ', strip_tags($product['description']));
                                $product['description'] = $this->mb_str_replace('&frac12;', '1/2', $product['description']);
                                $product['description'] = $this->mb_str_replace('&frac14;', '1/4', $product['description']);
                                $product['description'] = $this->mb_str_replace('&frac34;', '3/4', $product['description']);
                                $product['description'] = $this->mb_str_replace('&deg;', 'град. ', $product['description']);
                                $product['description'] = $this->mb_str_replace('&', '&amp;', $product['description']);
                                $product['description'] = $this->mb_str_replace('<', '&lt;', $product['description']);
                                $product['description'] = $this->mb_str_replace('>', '&gt;', $product['description']);
                                $product['description'] = $this->mb_str_replace("'", '&apos;', $product['description']);
                                $product['description'] = $this->mb_str_replace('"', '&quot;', $product['description']);

                                $product['name'] = $this->mb_str_replace('&nbsp;', ' ', strip_tags($product['name']));
                                $product['name'] = $this->mb_str_replace('&frac12;', '1/2', $product['name']);
                                $product['name'] = $this->mb_str_replace('&frac14;', '1/4', $product['name']);
                                $product['name'] = $this->mb_str_replace('&frac34;', '3/4', $product['name']);
                                $product['name'] = $this->mb_str_replace('&deg;', 'град. ', $product['name']);
                                $product['name'] = $this->mb_str_replace('&', '&amp;', $product['name']);
                                $product['name'] = $this->mb_str_replace('<', '&lt;', $product['name']);
                                $product['name'] = $this->mb_str_replace('>', '&gt;', $product['name']);
                                $product['name'] = $this->mb_str_replace("'", '&apos;', $product['name']);
                                $product['name'] = $this->mb_str_replace('"', '&quot;', $product['name']);
	                              $product['name'] = $this->fix_cyrillic( $product[ 'name' ] );   */

                                $item_xml = $items_xml->addChild('offer');
                                $item_xml->addAttribute('id', $product_object->id."c".$combination['id_product_attribute']);
                                $item_xml->addAttribute('available', 'true');
                                $item_xml->addChild('url', $product['link']."#".$combination['comb_url']);
                                $item_xml->addChild('region', $this->context->shop->name);
                                $item_xml->addChild('price', Tools::ps_round($combination['price'], 2));
                                $item_xml->addChild('oldprice', Tools::ps_round($combination['price_without_reduction'], 2));
                                $item_xml->addChild('startdate', date('d-m-Y',strtotime($product['specific_prices']['from'])));
                                $item_xml->addChild('enddate', date('d-m-Y',strtotime($product['specific_prices']['to'])));
                                $item_xml->addChild('currencyId', $currency_default->iso_code);
                                $item_xml->addChild('categoryId', $product['id_category_default']);
                                $item_xml->addChild('picture', $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'],'large_default'));
                                $item_xml->addChild('store', 'false');
                                $item_xml->addChild('pickup', 'true');
                                $item_xml->addChild('delivery', 'true');
                                $item_xml->addChild('weight', $product['unity']);
                                $item_xml->addChildWithCDATA('name', $product['name']." ".$namecomb);
                                $item_xml->addChild('vendor',  htmlspecialchars( $product['manufacturer_name'] ) );
                                $item_xml->addChildWithCDATA('description', $product['description']);
                                if ($product['virtual']) $item_xml->addChild('downloadable', 'true');
                                else $item_xml->addChild('downloadable', 'false');
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

                   /*         $product['description'] = $this->mb_str_replace('&nbsp;', ' ', strip_tags($product['description']));
                            $product['description'] = $this->mb_str_replace('&frac12;', '1/2', $product['description']);
                            $product['description'] = $this->mb_str_replace('&frac14;', '1/4', $product['description']);
                            $product['description'] = $this->mb_str_replace('&frac34;', '3/4', $product['description']);
                            $product['description'] = $this->mb_str_replace('&deg;', 'град. ', $product['description']);
                            $product['description'] = $this->mb_str_replace('&', '&amp;', $product['description']);
                            $product['description'] = $this->mb_str_replace('<', '&lt;', $product['description']);
                            $product['description'] = $this->mb_str_replace('>', '&gt;', $product['description']);
                            $product['description'] = $this->mb_str_replace("'", '&apos;', $product['description']);
                            $product['description'] = $this->mb_str_replace('"', '&quot;', $product['description']);

                            $product['name'] = $this->mb_str_replace('&nbsp;', ' ', strip_tags($product['name']));
                            $product['name'] = $this->mb_str_replace('&frac12;', '1/2', $product['name']);
                            $product['name'] = $this->mb_str_replace('&frac14;', '1/4', $product['name']);
                            $product['name'] = $this->mb_str_replace('&frac34;', '3/4', $product['name']);
                            $product['name'] = $this->mb_str_replace('&deg;', 'град. ', $product['name']);
                            $product['name'] = $this->mb_str_replace('&', '&amp;', $product['name']);
                            $product['name'] = $this->mb_str_replace('<', '&lt;', $product['name']);
                            $product['name'] = $this->mb_str_replace('>', '&gt;', $product['name']);
                            $product['name'] = $this->mb_str_replace("'", '&apos;', $product['name']);
                            $product['name'] = $this->mb_str_replace('"', '&quot;', $product['name']);
	                          $product['name'] = $this->fix_cyrillic( $product[ 'name' ] );         */

                            $sup_ref = ProductSupplier::getProductSupplierReference($product['id_product'],0,$product['id_supplier']);
                            $item_xml = $items_xml->addChild('offer'); 
                            $item_xml->addAttribute('id', $product['id_product']);
                            $item_xml->addAttribute('available', 'true');
                            if (!$front)
                                  {
                                  $linkp = $shopdomain.$product['category']."/".$product['id_product']."-".$product['link_rewrite'].".html";
                                  $item_xml->addChild('url', $linkp);
                                  }
                                else
                                  {
                                  $item_xml->addChild('url', $product['link']);
                                  }
                            $item_xml->addChild('region', $this->context->shop->name);
                            $item_xml->addChild('price', Tools::ps_round($product['price'], 2));
                            $item_xml->addChild('oldprice', Tools::ps_round($product['price_without_reduction'], 2));
                            $item_xml->addChild('startdate', date('d-m-Y',strtotime($product['specific_prices']['from'])));
                            $item_xml->addChild('enddate', date('d-m-Y',strtotime($product['specific_prices']['to'])));
                            $item_xml->addChild('currencyId', $currency_default->iso_code);
                            $item_xml->addChild('categoryId', $product['id_category_default']);
                            $item_xml->addChild('picture', $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'],'large_default'));
                            $item_xml->addChild('store', 'false');
                            $item_xml->addChild('pickup', 'true');
                            $item_xml->addChild('delivery', 'true');
                            $item_xml->addChild('weight', $product['unity']);
                            $item_xml->addChildWithCDATA('name', $product['name']);    
                            $item_xml->addChild('vendor', htmlspecialchars( $product['manufacturer_name'] ) );
                            $item_xml->addChildWithCDATA('description', $product['description']);    
                            if ($product['virtual']) $item_xml->addChild('downloadable', 'true');
                            else $item_xml->addChild('downloadable', 'false');
                            if ($product['features']) {
                                    foreach ($product['features'] as $feature) {
                                        $param_xml = $item_xml->addChildWithCDATA('param', $feature['value']);
                                        $param_xml->addAttribute('name', $feature['name']);
                                    }
                                }    
                                                              
                        }
                                
                        
                        
                        
                    }
          //  }
       // }

        if ($cron)
        {
            if ($fp = fopen(dirname(__FILE__) . '/../../upload/promua.'.$this->context->shop->id.'.xml', 'w'))
            {
                fwrite($fp, $price_xml->asXML());
                fclose($fp);
            }
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