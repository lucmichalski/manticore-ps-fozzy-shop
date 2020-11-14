<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Fozzy_shopselector extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'fozzy_shopselector';
        $this->author = 'PrestaShop';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->trans('Shop selector block', array(), 'Modules.Shopselector.Admin');
        $this->description = $this->trans('Adds a block allowing customers to select a shop for your store\'s content.', array(), 'Modules.Shopselector.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:fozzy_shopselector/fozzy_shopselector.tpl';
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $shops = Shop::getShops(true,1);

        if (1 < count($shops)) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

            return $this->fetch($this->templateFile);
        }

        return false;
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
      /*  if ($this->context->customer->id == 5)
        {
         dump($_SERVER);
        }*/ 
        $shops = Shop::getShops(true,1);
        $shops_ukr = array(1=>'Київ',2=>'Одеса',3=>'Дніпро',4=>'Харків',8=>'Рівне',9=>'Кременчук',10=>'Львів');
   //      dump($shops);
        if (isset($_SERVER['REQUEST_URI'])) $red = $_SERVER['REQUEST_URI'];
        else $red = '';
        foreach ($shops as $key=>$shop)
          {
           $r = substr($red, strlen ( $shops[$this->context->shop->id]['uri'] ));
           $shops[$key]['url'] = $_SERVER['REQUEST_SCHEME'].'://'.$shop['domain'].$shops[$key]['uri'].$r;
           if ($this->context->language->id == 2) $shops[$key]['name'] = $shops_ukr[$key];
           if ($key == 10) unset($shops[$key]);
          }
        $current_shop_name = $shops[$this->context->shop->id]['name'];  
        return array(
            'shops' => $shops,
            'current_shop' => array(
                'id_shop' => $this->context->shop->id,
                'name' => $current_shop_name
            )
        );
    }

    private function getNameSimple($name)
    {
        return preg_replace('/\s\(.*\)$/', '', $name);
    }
}
