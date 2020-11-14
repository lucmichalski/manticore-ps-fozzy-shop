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
class Ets_superspeed_cache_page extends ObjectModel
{
    public $page;
    public $id_object;
    public $id_product_attribute;
    public $ip;
	public $file_cache;
    public $request_uri;
	public $id_shop;
    public $file_size;
    public $user_agent;
    public $has_customer;
    public $has_cart;
    public $date_add;
    public $date_upd;
    public static $definition = array(
		'table' => 'ets_superspeed_cache_page',
		'primary' => 'id_cache_page',
		'multilang' => false,
		'fields' => array(
			'page' => array('type' => self::TYPE_STRING),
            'id_object' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'ip' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),  
            'file_cache' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),            
            'request_uri' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'id_shop'  => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'), 
            'has_customer'  => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'), 
            'has_cart'  => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'), 
            'file_size' => array('type' =>   self::TYPE_FLOAT),   
            'user_agent' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_upd' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),    
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
}