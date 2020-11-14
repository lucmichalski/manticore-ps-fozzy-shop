<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SitemapController extends SitemapControllerCore
{
  
	public function initContent()
	{
		
    parent::initContent();
    
    $id_lang = $this->context->language->id;
    $iso_lang = Language::getIsoById((int)$id_lang);
    $id_shop = (int)$this->context->shop->id;
    $shops = Shop::getShops();
      
		  $def_lang = Configuration::get('PS_LANG_DEFAULT');
		  $def_lang_iso = Language::getIsoById((int)$def_lang);
      
      foreach ($shops as $shop)
        {
          if ($shop['id_shop'] == $id_shop)
          {
          $host = $shop['domain'];
          $uri = $shop['uri'];
          }
        }
    
    $categs = 'SELECT l.`id_simpleblog_category`, l.`link_rewrite`, l.`name`, b.* FROM `'._DB_PREFIX_.'simpleblog_category` b, `'._DB_PREFIX_.'simpleblog_category_lang` l, `'._DB_PREFIX_.'simpleblog_category_shop` s ';
    $categs .='WHERE b.`active`=1 ';
    $categs .='AND (l.`id_simpleblog_category` = b.`id_simpleblog_category`) ';
    $categs .='AND (l.`id_simpleblog_category` = s.`id_simpleblog_category`) ';
    $categs .='AND (s.`id_shop`= '.$id_shop.') ';
    $categs .='AND (l.`id_lang` = '.$id_lang.') ';
    $categs_list = Db::getInstance()->executeS ($categs);
    
    foreach ($categs_list as $k=>$c)
    {
    $categs_list[$k]['blogs']=SimpleBlogPost::getPosts($id_lang, 1000, $c['id_simpleblog_category'], null, true, false, false, null, false, false,  $id_shop);
    $categs_list[$k]['category_url'] = $categs_list[$k]['blogs'][0]['category_url'];
    }
 //  d($categs_list);
		$this->context->smarty->assign('simpleblogmap', $categs_list);


	}
}
