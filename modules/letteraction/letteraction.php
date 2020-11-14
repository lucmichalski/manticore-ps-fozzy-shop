<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class LetterAction extends Module
{
  public function __construct()
  {
    $this->name = 'letteraction'; 
    $this->tab = 'emailing';
    $this->version = '1.1';
    $this->author = 'Novevision.com, Britoff A.';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    $this->bootstrap = true;
  
    parent::__construct();
  
    $this->displayName = $this->l('Letteraction');
    $this->description = $this->l('Description of my module.');
  
  }
  
  public function install()
  {
    if (Shop::isFeatureActive())
      Shop::setContext(Shop::CONTEXT_ALL);
   
    if (!parent::install() ||
      !Configuration::updateValue('letteraction_pr', '736,747,746,745,744,743,742,741,740') ||
   //   !Configuration::updateValue('letteraction_pr2', '736,747,746,745,744,743,742,741,740') ||
   //   !Configuration::updateValue('letteraction_pr3', '736,747,746,745,744,743,742,741,740') ||
      !Configuration::updateValue('letteraction_priceoff', 0) ||
  //    !Configuration::updateValue('letteraction_title', 'Товар недели') ||
  //    !Configuration::updateValue('letteraction_title2', 'Наши акции') ||
  //    !Configuration::updateValue('letteraction_title3', 'Лучшие цены') ||
      !Configuration::updateValue('letteraction_lettertext', '',true)
    )
      return false;
   
    return true;
  }
  
  public function uninstall()
  {
    if (!parent::uninstall() ||
      !Configuration::deleteByName('letteraction_pr') ||
   //   !Configuration::deleteByName('letteraction_pr2') ||
  //    !Configuration::deleteByName('letteraction_pr3') ||
      !Configuration::deleteByName('letteraction_priceoff') ||
   //   !Configuration::deleteByName('letteraction_title') ||
   //   !Configuration::deleteByName('letteraction_title2') ||
  //    !Configuration::deleteByName('letteraction_title3') ||
      !Configuration::deleteByName('letteraction_lettertext')
    )
      return false;
   
    return true;
  }
  
  public function getContent()
  {
      $output = null;
      $id_shop = $this->context->shop->id;
      if (Tools::isSubmit('submit'.$this->name))
      {
          $letteraction_pr = Tools::getValue('letteraction_pr');
          Configuration::updateValue('letteraction_pr', $letteraction_pr);
    //      $letteraction_pr2 = Tools::getValue('letteraction_pr2');
   //       Configuration::updateValue('letteraction_pr2', $letteraction_pr2);
   //       $letteraction_pr3 = Tools::getValue('letteraction_pr3');
    //      Configuration::updateValue('letteraction_pr3', $letteraction_pr3);
          $letteraction_priceoff = Tools::getValue('letteraction_priceoff');
          Configuration::updateValue('letteraction_priceoff', $letteraction_priceoff);
  //        $letteraction_title = Tools::getValue('letteraction_title');
  //        Configuration::updateValue('letteraction_title', $letteraction_title);
  //        $letteraction_title2 = Tools::getValue('letteraction_title2');
  //        Configuration::updateValue('letteraction_title2', $letteraction_title2);
  //        $letteraction_title3 = Tools::getValue('letteraction_title3');
  //        Configuration::updateValue('letteraction_title3', $letteraction_title3);
          
          
          
          $html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html style="width:100%;font-family:arial, helvetica, sans-serif;"><head><meta charset="UTF-8"><meta content="width=device-width, initial-scale=1" name="viewport"><meta name="x-apple-disable-message-reformatting"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta content="telephone=no" name="format-detection"><title>Чекали з нетерпінням?</title><!--[if (mso 16)]><style type="text/css"> a {text-decoration: none;}</style><![endif]--><!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--></head><body>';
          $html.='<table style="display: block; background: #ffffff; margin: 10px auto; border: 1px solid #3F4044; width: 680px; padding: 10px; border-collapse: collapse;"><tbody><tr> <td style="padding-bottom: 10px;"><div id="header" style="width: 100%;"><div id="desktop_logo" style="width: 100%;margin: 0 auto;text-align: center;"> <a href="https://fozzyshop.ua/"> <img class="logo img-fluid" src="https://fozzyshop.ua/img/cms/banners/post_n_1.jpg" alt="Fozzyshop"></a></div></div></td></tr>';
          $html.='<tr> <td width="100%" align="center" valign="top"><table cellpadding="0" cellspacing="0" width="100%"><tbody><tr> <td align="left" class="esd-block-text"><p style="font-size: 14px; color: #000000;">Доброго дня, %FIRSTNAME%! <br/>Не хвилюйтесь, <a style="color: #FF0000;text-decoration: none;" href="https://fozzyshop.ua/" style="color: #FF0000;text-decoration: none;">FOZZYSHOP</a>, як завжди, вчасно підготував найкращі пропозиції за привабливими цінами. Обирайте продукти та робіть замовлення - зустрічайте акції із <a href="https://fozzyshop.ua/" style="color: #FF0000;text-decoration: none;">FOZZYSHOP</a>!</p></td></tr></tbody></table></td></tr>';
       //   $html.='<tr style="border: 1px solid #cbaa71;border-bottom: none;"> <td style="text-align: center; padding: 10px 5px;" align="center" class="esd-block-text"> <span style="font-size: 20px; line-height: 24px;">'.$letteraction_title.'</span></td></tr><tr style="border: 1px solid #cbaa71;border-top: none;"><td>';
          $html.='<tr><td><div style="display: block;margin: 0 auto;width: 640px;"><ul style="list-style: none;margin: 0 0 0 10px;padding: 0;">';
          
          
          if ($letteraction_pr)
            {
             $products = explode(",", $letteraction_pr);  
            }
            
          foreach ($products as $product)
            {
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$product."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if (!$id_product) continue;
            $pr = new Product($id_product,true,2,$id_shop);
            $price = number_format(round($pr->price,2), 2, ',', ' ');
            $baseprice = number_format(round($pr->base_price,2), 2, ',', ' ');
            $link_rewrite = $pr->getLink();
            $name = $pr->name;
            if (mb_strlen($name) > 16)
            {
            $name_naz = mb_substr($name,0,17)." ...";
            }
            else
            {
            $name_naz = $name;
            }
            $link = new Link();
            
            $cover = $link->getImageLink('warehouse',implode(',',Product::getCover($id_product)),'home_default');
            $html.='<li style="width: 31%;display: block;float: left;margin: 5px;text-align: center;min-height: 300px;border: solid 1px #dad8d8;">';
            $html.='<div style="padding: 8px;background: #ffffff;">';
            $html.='<a style="display: block;" href="'.$link_rewrite.'" title="'.htmlspecialchars($name).'">';
            $html.='<img style="width: 100%;" src="http://'.$cover.'" alt="'.htmlspecialchars($name).'" ></a>';
            $html.='</div><div style="padding-top: 5px;min-height: 75px;">';
            $html.='<a style="color: #565b64;text-decoration: none;font-size: 12px;display: block;min-height: 30px;" href="'.$link_rewrite.'" title="'.htmlspecialchars($name).'">'.$name_naz.'</a>';
            $html.='<div style="display: block;clear: both;margin: 0px 0 0 0;">';
            $html.='<span style="color: #FF0000;font-weight: bold;display: block;font-size: 18px;">'.$price.' грн.</span>';
            if ($letteraction_priceoff) $html.='<span style="font-size: 12px;font-weight: normal;display: block;margin-left: 6px;color: #84888f;text-decoration: line-through;">'.$baseprice.'грн.</span>';
            $html.='</div><a href="'.$link_rewrite.'" class="es-button" target="_blank" style="display:inline-block;line-height: 18px;width:auto;text-align:center;color: #FFFFFF;font-size: 14px;background: #3F4044;padding: 7px 20px;margin: 10px 0;text-decoration: none;">ЗАМОВИТИ</a></div></li>';
            }
          
            $html.='</ul></div></td></tr>';
            $html.='<tr><td>&nbsp;</td></tr>';
            $letteraction_pr2 = 0;
            if ($letteraction_pr2)
            {
            $html.='<tr style="border: 1px solid #cbaa71;border-bottom: none;"> <td style="text-align: center; padding: 10px 5px;" align="center" class="esd-block-text"> <span style="font-size: 20px; line-height: 24px;">'.$letteraction_title2.'</span></td></tr><tr style="border: 1px solid #cbaa71;border-top: none;"><td>';
            $html.='<div style="display: block;margin: 0 auto;width: 640px;"><ul style="list-style: none;margin: 0 0 0 10px;padding: 0;">';
            
            if ($letteraction_pr2)
            {
             $products = explode(",", $letteraction_pr2);  
            }
            
          foreach ($products as $product)
            {
            $pr = new Product($product,true,(int)Configuration::get('PS_LANG_DEFAULT'));
            $currency = Currency::getCurrencyInstance(1);
            $price = number_format(round($pr->price*$currency->conversion_rate,2), 2, ',', ' ');
            $baseprice = number_format(round($pr->base_price*$currency->conversion_rate,2), 2, ',', ' ');
            $link_rewrite = $pr->getLink();
            $name = $pr->name;
            if (mb_strlen($name) > 59)
            {
            $name_naz = mb_substr($name,0,60)." ...";
            }
            else
            {
            $name_naz = $name;
            }
            $link = new Link();
            
            $cover = $link->getImageLink('warehouse',implode(',',Product::getCover($product)),'home_default');
            $html.='<li style="width: 31%;display: block;float: left;margin: 5px;text-align: center;min-height: 300px;">';
            $html.='<div style="padding: 8px;background: #ffffff;-webkit-box-shadow: 0 0 0 1px rgba(0,0,0,0.095), 0 1px 1px 0 rgba(0,0,0,0.2), 0 2px 1px 0 rgba(0,0,0,0.1);-moz-box-shadow: 0 0 0 1px rgba(0,0,0,0.095), 0 1px 1px 0 rgba(0,0,0,0.2), 0 2px 1px 0 rgba(0,0,0,0.1);box-shadow: 0 0 0 1px rgba(0,0,0,0.095), 0 1px 1px 0 rgba(0,0,0,0.2), 0 2px 1px 0 rgba(0,0,0,0.1);">';
            $html.='<a style="display: block;" href="'.$link_rewrite.'">';
            $html.='<img style="width: 100%;" src="http://'.$cover.'" alt="'.$name.'" title="'.$name.'"></a>';
            $html.='</div><div style="padding-top: 5px;min-height: 75px;">';
            $html.='<a style="color: #565b64;text-decoration: none;" href="'.$link_rewrite.'" title="'.$name.'">'.$name_naz.'</a>';
            $html.='<div style="display: block;clear: both;margin: 0px 0 0 0;">';
            $html.='<span style="color: #e0320b;font-weight: bold;display: block;font-size: 16px;">'.$price.' грн.</span>';
            if ($letteraction_priceoff) $html.='<span style="font-size: 14px;font-weight: normal;display: block;margin-left: 6px;color: #84888f;text-decoration: line-through;">'.$baseprice.'грн.</span>';
            $html.='</div></div></li>';
            }
            
            $html.='</ul></div></td></tr>';
            $html.='<tr><td>&nbsp;</td></tr>';
            }
            if ($letteraction_pr3)
            {
            $html.='<tr style="border: 1px solid #cbaa71;border-bottom: none;"> <td style="text-align: center; padding: 10px 5px;" align="center" class="esd-block-text"> <span style="font-size: 20px; line-height: 24px;">'.$letteraction_title3.'</span></td></tr><tr style="border: 1px solid #cbaa71;border-top: none;"><td>';
            $html.='<div style="display: block;margin: 0 auto;width: 640px;"><ul style="list-style: none;margin: 0 0 0 10px;padding: 0;">';
            $letteraction_pr3 = 0;
            if ($letteraction_pr3)
            {
             $products = explode(",", $letteraction_pr3);  
            }
            
          foreach ($products as $product)
            {
            $pr = new Product($product,true,(int)Configuration::get('PS_LANG_DEFAULT'));
            $currency = Currency::getCurrencyInstance(1);
            $price = number_format(round($pr->price*$currency->conversion_rate,2), 2, ',', ' ');
            $baseprice = number_format(round($pr->base_price*$currency->conversion_rate,2), 2, ',', ' ');
            $link_rewrite = $pr->getLink();
            $name = $pr->name;
            if (mb_strlen($name) > 59)
            {
            $name_naz = mb_substr($name,0,60)." ...";
            }
            else
            {
            $name_naz = $name;
            }
            $link = new Link();
            
            $cover = $link->getImageLink('warehouse',implode(',',Product::getCover($product)),'home_default');
            $html.='<li style="width: 31%;display: block;float: left;margin: 5px;text-align: center;min-height: 300px;">';
            $html.='<div style="padding: 8px;background: #ffffff;-webkit-box-shadow: 0 0 0 1px rgba(0,0,0,0.095), 0 1px 1px 0 rgba(0,0,0,0.2), 0 2px 1px 0 rgba(0,0,0,0.1);-moz-box-shadow: 0 0 0 1px rgba(0,0,0,0.095), 0 1px 1px 0 rgba(0,0,0,0.2), 0 2px 1px 0 rgba(0,0,0,0.1);box-shadow: 0 0 0 1px rgba(0,0,0,0.095), 0 1px 1px 0 rgba(0,0,0,0.2), 0 2px 1px 0 rgba(0,0,0,0.1);">';
            $html.='<a style="display: block;" href="'.$link_rewrite.'">';
            $html.='<img style="width: 100%;" src="http://'.$cover.'" alt="'.$name.'" title="'.$name.'"></a>';
            $html.='</div><div style="padding-top: 5px;min-height: 75px;">';
            $html.='<a style="color: #565b64;text-decoration: none;" href="'.$link_rewrite.'" title="'.$name.'">'.$name_naz.'</a>';
            $html.='<div style="display: block;clear: both;margin: 0px 0 0 0;">';
            $html.='<span style="color: #e0320b;font-weight: bold;display: block;font-size: 16px;">'.$price.' грн.</span>';
            if ($letteraction_priceoff) $html.='<span style="font-size: 14px;font-weight: normal;display: block;margin-left: 6px;color: #84888f;text-decoration: line-through;">'.$baseprice.'грн.</span>';
            $html.='</div></div></li>';
            }
            $html.='</ul></div></td></tr>';
            $html.='<tr><td>&nbsp;</td></tr>';
            }
            
            $html.='<tr style="height: 60px;"> <td align="right"> <a href="https://fozzyshop.ua/prices-drop"  target="_blank" style="padding: 9px 15px;background: #3F4044;color: #ffffff;border: solid 1px #3F4044;text-decoration: none;text-transform: uppercase;">Більше пропозицій</a></td></tr>';
            $html.='<tr> <td style="text-align: center;padding: 7px 5px;border-top: 1px solid #3F4044;padding-top: 10px;color: white;background-color: #3f4044;font-size: 12px;"  align="center" class="esd-block-text"><table style="width: 100%;"><tr> <td align="left" style="width: 50%;"><table><tbody><tr><td> <a style="color: #FF0000;text-decoration: none;" target="_blank" href="https://fozzyshop.ua">FOZZYSHOP</a> <br/>м. Київ, вул. Заболотного, 37 <br/><a style="color: #FF0000;text-decoration: none;" target="_blank" href="tel:0800300168">0 800 300 168</a> <br/><a style="color: #FF0000;text-decoration: none;" target="_blank" href="mailto:zakaz@fozzy.ua">zakaz@fozzy.ua</a> <br/><span style="font-size: 10px;" >Copyright 2020 ТОВ "Експансія"<span></td></tr></tbody></table></td> <td align="right" style="width: 50%;"><table><tbody><tr> <td style="padding: 0 0 15px;">Приєднуйтесь до нас!</td></tr><tr>
<td     align="center"><a target="_blank" href="https://www.facebook.com/fozzyshop.ua"> <img style="width: 30px;height: 30px;" title="Facebook" src="https://esputnik.com/content/stripostatic/assets/img/social-icons/circle-white/facebook-circle-white.png" alt="Fb"/></a></td></tr></tbody></table></td></tr></table></td></tr>';
            $html.='<tr> <td style="text-align: center; padding: 0px 5px;border-top: 1px solid #3F4044;"  align="center" class="esd-block-text"> <span style="font-size: 10px; line-height: 12px;color: #e2e2e2;">Ви отримали цей лист, оскільки є зареєстрованим користувачем інтернет-магазину <a target="_blank" href="https://fozzyshop.ua" style="font-size: 10px; line-height: 12px;color: #e2e2e2;">fozzyshop.ua</a></span><br /> <a style="font-size: 10px; line-height: 12px;color: #e2e2e2;" href="https://esputnik.com/unsubscribe">Відписатись від розсилки</a></td></tr>';
            $html.='</body></html>';
            Configuration::updateValue('letteraction_lettertext', $html, true);
          
          $output .= $this->displayConfirmation($this->l('Settings updated'));
      }
      return $output.$this->displayForm();
  }
  
  public function displayForm()
  {
      // Init Fields form array
      $fields_form[0]['form'] = array(
          'legend' => array(
				  'title' => $this->l('Settings'),
			    ),
          'input' => array(
              array(
                  'type' => 'text',
                  'label' => $this->l('ID - Товар недели'),
                  'name' => 'letteraction_pr',
                  'size' => 20
              ),
     /*         array(
                  'type' => 'text',
                  'label' => $this->l('ID - Акции'),
                  'name' => 'letteraction_pr2',
                  'size' => 20
              ),
              array(
                  'type' => 'text',
                  'label' => $this->l('ID - Лучшие предложения'),
                  'name' => 'letteraction_pr3',
                  'size' => 20
              ),      /*/
              array(
        					'type' => 'switch',
        					'label' => $this->l('Show old prices:'),
        					'name' => 'letteraction_priceoff',
        					'is_bool' => true,
        					'values' => array(
        						array(
        							'id' => 'letteraction_priceoff_on',
        							'value' => 1,
        							'label' => $this->l('Yes')),
        						array(
        							'id' => 'letteraction_priceoff_off',
        							'value' => 0,
        							'label' => $this->l('No')),
        					),
                            'validation' => 'isBool',
        				),
      /*        array(
                  'type' => 'text',
                  'label' => $this->l('Заголовок - товар недели'),
                  'name' => 'letteraction_title',
                  'size' => 20
              ),
              array(
                  'type' => 'text',
                  'label' => $this->l('Заголовок - Акции'),
                  'name' => 'letteraction_title2',
                  'size' => 20
              ),
              array(
                  'type' => 'text',
                  'label' => $this->l('Заголовок - Лучшие предложения'),
                  'name' => 'letteraction_title3',
                  'size' => 20
              ),     */
            array(
                  'type' => 'textarea',
                  'label' => $this->l('Превью письма'),
                  'name' => 'letteraction_lettertext',
                  'autoload_rte' => true,
                  'cols' => 60,          
                  'rows' => 60           
              ),
           array(
                  'type' => 'textarea',
                  'label' => $this->l('Код для вставки'),
                  'name' => 'letteraction_lettertext',
                  'autoload_rte' => false,
                  'cols' => 60,          
                  'rows' => 120           
              ),
          ),
          
          'submit' => array(
              'title' => $this->l('Save'),
              'class' => 'button'
          )
      );
       
      $helper = new HelperForm();

	  // Module, token and currentIndex
      $helper->module = $this;
      $helper->name_controller = $this->name;
      $helper->token = Tools::getAdminTokenLite('AdminModules');
      $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
       
      // Language
      $languages = Language::getLanguages(false);
      $helper->languages = $languages;
      $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		  $helper->allow_employee_form_lang = true;

       
      // Title and toolbar
      $helper->title = $this->displayName;
      $helper->show_toolbar = true;        // false -> remove toolbar
      $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
      $helper->submit_action = 'submit'.$this->name;
       
      // Load current value
      $helper->fields_value['letteraction_pr'] = Configuration::get('letteraction_pr');
      $helper->fields_value['letteraction_pr2'] = Configuration::get('letteraction_pr2');
      $helper->fields_value['letteraction_title2'] = Configuration::get('letteraction_title2');
      $helper->fields_value['letteraction_pr3'] = Configuration::get('letteraction_pr3');
      $helper->fields_value['letteraction_title3'] = Configuration::get('letteraction_title3');
      $helper->fields_value['letteraction_priceoff'] = Configuration::get('letteraction_priceoff');
      $helper->fields_value['letteraction_lettertext'] = Configuration::get('letteraction_lettertext');
      $helper->fields_value['letteraction_title'] = Configuration::get('letteraction_title'); 
      
      return $helper->generateForm($fields_form);
  }



}