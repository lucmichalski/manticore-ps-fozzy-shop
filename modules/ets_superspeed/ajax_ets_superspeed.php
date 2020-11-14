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
 
include(dirname(__FILE__).'/../../config/config.inc.php');
$ets_superspeed = Module::getInstanceByName('ets_superspeed');
if(Tools::getValue('token')!=md5($ets_superspeed->id))
   exit;
$optimized_images = array();
$list_image_optimized = Configuration::get('ETS_SP_LIST_IMAGE_OPTIMIZED');
if($list_image_optimized)
{
    $list_image_optimized = explode(',',$list_image_optimized);
    foreach($list_image_optimized as $image)
    {
        $optimized_images[]= array(
            'image'=>str_replace(array('/','\\','.'),'',Tools::substr($image,5)),
            'image_cat' => Tools::strlen($image) > 40 ? Tools::substr($image,0,20).' . . . '.Tools::substr($image,Tools::strlen($image)-20) : $image
        );
    }
}
if(Tools::getValue('getPercentageImageOptimize'))
{
    $total_optimizeed = (int)$total_optimizeed = (int)Configuration::get('ETS_SP_TOTAL_IMAGE_OPTIMIZED');
    $total = (int)Tools::getValue('total_optimize_images');
    
    if($total && $total_optimizeed)
    {
        die(
            Tools::jsonEncode(
                array(
                    'percent' => Tools::ps_round($total_optimizeed*100/$total,2),
                    'total_optimizeed' => $total_optimizeed,
                    'optimized_images' => $optimized_images,
                    'image' => $ets_superspeed->getImageOptimize(true),
                    'ETS_SPEEP_RESUMSH' => Configuration::get('ETS_SPEEP_RESUMSH'),
                )
            )
        );
    }
    die(
        Tools::jsonEncode(
            array(
                'percent' => 100,
            )
        )
    );
}
if(Tools::getValue('getPercentageAllImageOptimize'))
{
    $total=0;
    $total_optimizeed =0;
    $total += $ets_superspeed->getTotalImage('product',true,false,false,true);
    $total_optimizeed += $ets_superspeed->getTotalImage('product',true,true,true,true);
    $total += $ets_superspeed->getTotalImage('category',true,false,false,true);
    $total_optimizeed += $ets_superspeed->getTotalImage('category',true,true,true,true);
    $total += $ets_superspeed->getTotalImage('supplier',true,false,false,true);
    $total_optimizeed += $ets_superspeed->getTotalImage('supplier',true,true,true,true);
    $total += $ets_superspeed->getTotalImage('manufacturer',true,false,false,true);
    $total_optimizeed += $ets_superspeed->getTotalImage('manufacturer',true,true,true,true);
    if($ets_superspeed->isblog)
    {
        $total += $ets_superspeed->getTotalImage('blog_post',true,false,false,true);
        $total_optimizeed += $ets_superspeed->getTotalImage('blog_post',true,true,true,true);
        $total += $ets_superspeed->getTotalImage('blog_category',true,false,false,true);
        $total_optimizeed += $ets_superspeed->getTotalImage('blog_category',true,true,true,true);
        $total += $ets_superspeed->getTotalImage('blog_gallery',true,false,false,true);
        $total_optimizeed += $ets_superspeed->getTotalImage('blog_gallery',true,true,true,true);
        $total += $ets_superspeed->getTotalImage('blog_slide',true,false,false,true);
        $total_optimizeed += $ets_superspeed->getTotalImage('blog_slide',true,true,true,true);
    }
    if($ets_superspeed->isSlide)
    {
        $total += $ets_superspeed->getTotalImage('home_slide',true,false,false,true);
        $total_optimizeed += $ets_superspeed->getTotalImage('home_slide',true,true,true,true);
    }
    $total += $ets_superspeed->getTotalImage('others',true,false,false,true);
    $total_optimizeed += $ets_superspeed->getTotalImage('others',true,true,true,true);
    $total_optimizeed2 = (int)Configuration::get('ETS_SP_TOTAL_IMAGE_OPTIMIZED');
    $total2 = (int)Tools::getValue('total_optimize_images');
    if($total && $total_optimizeed)
    {
        die(
            Tools::jsonEncode(
                array(
                    'percent' => Tools::ps_round($total_optimizeed*100/$total,2),
                    'percent2' => Tools::ps_round($total_optimizeed2*100/$total2,2),
                    'total_optimizeed2' => $total_optimizeed2,
                    'total_optimizeed' => $total_optimizeed,
                    'total_unoptimized' => $total- $total_optimizeed,
                    'optimized_images' => $optimized_images,
                    'percent_unoptimized' => Tools::ps_round(100 - Tools::ps_round($total_optimizeed*100/$total,2),2),
                    'total_size_save' => $ets_superspeed->getTotalSizeSave(),
                    'ETS_SPEEP_RESUMSH' => Configuration::get('ETS_SPEEP_RESUMSH'),
                )
            )
        );
    }
    die(
        Tools::jsonEncode(
            array(
                'percent' => 100,
            )
        )
    );
}
