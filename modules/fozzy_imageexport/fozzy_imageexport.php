<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fozzy_imageexport extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fozzy_imageexport';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Fozzy';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fozzy image export');
        $this->description = $this->l('Service for image export from site by reference');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    
    public function getimagebyref($reference, $type=1)  //Отдает оригинал изображения или ссылку на него
    {
     
     if ($reference != 0)
      {
        $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$reference."'";
        $idnumber = Db::getInstance()->getRow($sql_get_id);
        $id_product = (int)$idnumber['id_product'];
      }
      
     if ($id_product != 0)
      {
       $cover = Image::getCover($id_product);
       $name = 'original';
       $link = new Link();
       $image_link = $link->getImageLink($name, (int)$cover['id_image'], null);
       $image_link = "http://".$image_link;
      }
    else
      {
       $image_link = 'http://fozzyshop.com.ua/page-not-found';
      }

    if ($type == 1) header('Location: '.$image_link); 
    if ($type == 2) return $image_link; 
    }
   
   public function getpic ($reference, $type=1)    //Отдает large_default изображения или ссылку на него
    {
     
     if ($reference != 0)
      {
        $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$reference."'";
        $idnumber = Db::getInstance()->getRow($sql_get_id);
        $id_product = (int)$idnumber['id_product'];
      }
     if ($id_product != 0)
      {
       $cover = Image::getCover($id_product);
       $name = 'large_default';
       $im = $this->product->id.'-'.(int)$cover['id_image'];
       $link = new Link();
       $image_link = $link->getImageLink($name, $im, $name);
       $image_link = "http://".$image_link;
      }
    else
      {
       $image_link = 'http://fozzyshop.com.ua/page-not-found';
      }

    if ($type == 1) header('Location: '.$image_link); 
    if ($type == 2) return $image_link; 
    }
   
   public function getpicfull ($reference, $type=1)      //Отдает thickbox_default изображения или ссылку на него
    {
     
     if ($reference != 0)
      {
        $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$reference."'";
        $idnumber = Db::getInstance()->getRow($sql_get_id);
        $id_product = (int)$idnumber['id_product'];
      }
     if ($id_product != 0)
      {
       $cover = Image::getCover($id_product);
       $name = 'thickbox_default';
       $im = $this->product->id.'-'.(int)$cover['id_image'];
       $link = new Link();
       $image_link = $link->getImageLink($name, $im, $name);
       $image_link = "http://".$image_link;
      }
    else
      {
       $image_link = 'http://fozzyshop.com.ua/page-not-found';
      }

    if ($type == 1) header('Location: '.$image_link); 
    if ($type == 2) return $image_link; 
    }
   
}
