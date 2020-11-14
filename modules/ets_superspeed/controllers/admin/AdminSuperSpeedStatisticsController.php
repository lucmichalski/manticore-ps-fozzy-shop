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
class AdminSuperSpeedStatisticsController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function initContent()
    {
        parent::initContent();
        if(Tools::isSubmit('getTimeSpeed'))
        {
            if(Tools::getValue('request_time'))
            {
                $request_time = Tools::ps_round(Tools::getValue('request_time')/1000,2);
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_superspeed_time(id_shop,`date`,`time`) VALUES("'.(int)$this->context->shop->id.'","'.pSQL(date('Y-m-d H:i:s')).'","'.(float)$request_time.'")');
                //die('INSERT INTO '._DB_PREFIX_.'ets_superspeed_time(id_shop,`date`,`time`) VALUES("'.(int)$this->context->shop->id.'","'.pSQL(date('Y-m-d H:i:s')).'","'.(float)$request_time.'")');
                $count= Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'ets_superspeed_time WHERE id_shop="'.(int)$this->context->shop->id.'"');
                if($count>150)
                {
                    $mintime= Db::getInstance()->getValue('SELECT MIN(`date`) FROM '._DB_PREFIX_.'ets_superspeed_time WHERE id_shop="'.(int)$this->context->shop->id.'"');
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_superspeed_time WHERE id_shop="'.(int)$this->context->shop->id.'" AND `date` ="'.pSQL($mintime).'"');
                }
            }
            $times= $this->module->getTimeSpeed(true);
            die(
                Tools::jsonEncode(
                    array(
                       'time' => $times['time'],
                       'value'=>$times['value'],
                    )
                )
            );
        }
    }
    public function renderList()
    {
        $this->context->smarty->assign(
            $this->getCacheSettingFieldsValues()
        );
        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'html_form' =>$this->module->renderSpeedStatistics(),
            )
        );
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin.tpl');
    }
    public function getCacheSettingFieldsValues()
    {
        $file_caches= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ets_superspeed_cache_page` WHERE id_shop="'.(int)$this->context->shop->id.'" AND request_uri not like "%check_speed=1%" ORDER BY date_upd desc LIMIT 0,10');
        if($file_caches)
        {
            foreach($file_caches as &$file_cache)
            {
                $file_cache['basename'] = basename($file_cache['file_cache']);
                if($file_cache['file_size']==0)
                {
                    $file_cache['file_size'] = Tools::ps_round(@filesize($file_cache['file_cache'])/1024,2);
                }
                if(Tools::strlen($file_cache['request_uri'])>26)
                    $file_cache['name_display'] = Tools::substr($file_cache['request_uri'],0,13).' . . . '.Tools::substr($file_cache['request_uri'],Tools::strlen($file_cache['request_uri'])-13);
            } 
        }
        $total_image_product= $this->module->getTotalImage('product',true,false,false,true);
        $total_image_category = $this->module->getTotalImage('category',true,false,false,true);
        $total_image_manufacturer = $this->module->getTotalImage('manufacturer',true,false,false,true);
        $total_image_supplier = $this->module->getTotalImage('supplier',true,false,false,true);
        $total_image_product_optimizaed = $this->module->getTotalImage('product',true,true,false,true);
        $total_image_category_optimizaed = $this->module->getTotalImage('category',true,true,false,true);
        $total_image_manufacturer_optimizaed = $this->module->getTotalImage('manufacturer',true,true,false,true);
        $total_image_supplier_optimizaed = $this->module->getTotalImage('supplier',true,true,false,true);
        $total_images = $total_image_product + $total_image_category + $total_image_manufacturer + $total_image_supplier;
        $total_optimized_images = $total_image_category_optimizaed + $total_image_product_optimizaed + $total_image_supplier_optimizaed + $total_image_manufacturer_optimizaed;
        if($this->module->isblog)
        {
            $total_image_blog_post= $this->module->getTotalImage('blog_post',true,false,false,true);
            $total_image_blog_category = $this->module->getTotalImage('blog_category',true,false,false,true);
            $total_image_blog_gallery = $this->module->getTotalImage('blog_gallery',true,false,false,true);
            $total_image_blog_slide = $this->module->getTotalImage('blog_slide',true,false,false,true);
            $total_images += $total_image_blog_post + $total_image_blog_category + $total_image_blog_gallery + $total_image_blog_slide;
            $total_image_blog_post_optimizaed = $this->module->getTotalImage('blog_post',true,true,false,true);
            $total_image_blog_category_optimizaed = $this->module->getTotalImage('blog_category',true,true,false,true);
            $total_image_blog_gallery_optimizaed = $this->module->getTotalImage('blog_gallery',true,true,false,true);
            $total_image_blog_slide_optimizaed = $this->module->getTotalImage('blog_slide',true,true,false,true);
            $total_optimized_images += $total_image_blog_post_optimizaed + $total_image_blog_category_optimizaed + $total_image_blog_gallery_optimizaed + $total_image_blog_slide_optimizaed;
        }
        if($this->module->isSlide)
        {
            $total_image_home_slide= $this->module->getTotalImage('home_slide',true,false,false,true);
            $total_image_home_slide_optimizaed = $this->module->getTotalImage('home_slide',true,true,false,true);
            $total_images += $total_image_home_slide;
            $total_optimized_images += $total_image_home_slide_optimizaed; 
        }
        $total_image_others = $this->module->getTotalImage('others',true,false,false,true);
        $total_image_others_optimizaed = $this->module->getTotalImage('others',true,true,false,true);
        $total_images += $total_image_others;
        $total_optimized_images += $total_image_others_optimizaed; 
        $total_unoptimized_images = $total_images - $total_optimized_images;
        $percent_optimized_images = $total_images ? Tools::ps_round(($total_optimized_images/$total_images)*100,2) :0;
        $percent_unoptimized_images= 100 - $percent_optimized_images;
        $total_cache = Db::getInstance()->getValue('SELECT SUM(file_size) FROM '._DB_PREFIX_.'ets_superspeed_cache_page WHERE id_shop='.(int)$this->context->shop->id);
        if($total_cache <1024)
            $total_text ='KB';
        else
        {
            $total_cache = $total_cache/1024;
            if($total_cache<1024)
                $total_text='Mb';
            else
            {
                $total_cache= $total_cache/1024;
                $total_text='Gb';
            }
        }
        $check_points = array();
        $total_point = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'ets_superspeed_hook_time pht
        INNER JOIN '._DB_PREFIX_.'hook h ON (pht.hook_name = h.name COLLATE utf8_unicode_ci)
        INNER JOIN '._DB_PREFIX_.'hook_module hm ON (hm.id_hook=h.id_hook AND hm.id_module=pht.id_module)
        WHERE hm.id_shop="'.(int)$this->context->shop->id.'" AND pht.time >1');
        $check_points[] = array(
            'check_point' => $this->l('Number of module hooks have execution time greater than 1000 ms'),
            'number_data' => $total_point ,
            'status' =>$total_point ? $this->l('Bad') : $this->l('Good'),
            'class_status' => $total_point ? 'status-bad' :'status-good',
        );
        return array(
            'ETS_SPEED_SMARTY_CACHE' => Tools::getValue('ETS_SPEED_SMARTY_CACHE' , Configuration::get('PS_SMARTY_FORCE_COMPILE')==0 || Configuration::get('PS_SMARTY_FORCE_COMPILE')==1 && Configuration::get('PS_SMARTY_CACHE')),
            'PS_SMARTY_CACHE' => Tools::getValue('PS_SMARTY_CACHE',Configuration::get('PS_SMARTY_CACHE')),
            'PS_HTML_THEME_COMPRESSION' => Tools::getValue('PS_HTML_THEME_COMPRESSION',Configuration::get('PS_HTML_THEME_COMPRESSION')),
            'PS_JS_THEME_CACHE' => Tools::getValue('PS_JS_THEME_CACHE',Configuration::get('PS_JS_THEME_CACHE')),
            'PS_CSS_THEME_CACHE' => Tools::getValue('PS_CSS_THEME_CACHE',Configuration::get('PS_CSS_THEME_CACHE')),
            'ETS_SPEED_ENABLE_PAGE_CACHE' => Configuration::get('ETS_SPEED_ENABLE_PAGE_CACHE'),
            'ETS_SPEED_OPTIMIZE_NEW_IMAGE' => Configuration::get('ETS_SPEED_OPTIMIZE_NEW_IMAGE'),
            'PS_HTACCESS_CACHE_CONTROL' => Configuration::get('PS_HTACCESS_CACHE_CONTROL'),
            'PS_MODE_DEV' => _PS_MODE_DEV_,
            'lazy_load' => Configuration::get('ETS_SPEED_ENABLE_LAYZY_LOAD') && Configuration::get('ETS_SPEED_LAZY_FOR'),
            'cache_url_ajax' => $this->context->link->getAdminLink('AdminSuperSpeedPageCaches'),
            'file_caches' => $file_caches,
            'total_images' => $total_images,
            'total_optimized_images' => $total_optimized_images,
            'total_unoptimized_images' => $total_unoptimized_images,
            'percent_optimized_images' => $percent_optimized_images,
            'percent_unoptimized_images' => $percent_unoptimized_images,
            'total_optimized_size_images' => $this->module->getTotalSizeSave(),
            'check_points' => array_merge($check_points,$this->module->getCheckPoints()),
            'link_optimize_image' => $this->context->link->getAdminLink('AdminSuperSpeedImage'),
            'total_cache' => $total_cache ? Tools::ps_round($total_cache,2).$total_text :'',
            
        );
    }
}