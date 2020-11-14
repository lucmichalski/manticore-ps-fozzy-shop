<?php
/**
* 2007-2020 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fozzy_roudslider extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fozzy_roudslider';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Britoff A.';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fozzy Roundslider');
        $this->description = $this->l('Fozzy Roundslider module with popup');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        return parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayModalCartExtraProductActions');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookdisplayHeader()
    {                                      
      $fileurl = '/views/js';
      $dh = scandir(dirname(__FILE__).$fileurl);
      foreach ($dh as $file)
        { 
          if($file !='.' && $file !='..' && substr($file, -3) == '.js')
          {
            $this->context->controller->addJS($this->_path.'views/js/'.$file);
          }
        }
      
      $fileurl = '/views/css';
      $dh = scandir(dirname(__FILE__).$fileurl);
      foreach ($dh as $file)
        { 
          if($file !='.' && $file !='..' && substr($file, -4) == '.css')
          {
            $this->context->controller->addCSS($this->_path.'views/css/'.$file, 'all');
          }
        }
    }

    public function hookdisplayModalCartExtraProductActions($params)
    {
      $id_shop = $this->context->shop->id;
      $id_lang = $this->context->language->id;
      $product = $params['product'];
      
      $p = SpecificPrice::getQuantityDiscounts($product['id'], $id_shop, 1, 0, 0);
      
      //dump($p);
      
      $this->smarty->assign(array(
        'product' => $product,
        'product_sp' => $p,
      ));
      return $this->display(__FILE__, 'modal.tpl');
    }
}
