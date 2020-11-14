<?php
/**
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
 *  @author    Yuri Denisov <contact@splashmart.ru>
 *  @copyright 2014-2017 Yuri Denisov
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

if (!class_exists('OOCCartModel')) {
    include_once(dirname(__FILE__).'/OOCCartProductModel.php');

    class OOCCartModel extends ObjectModel
    {
        public $id_sm_ooc_cart;
        public $id_currency;
        public $order_price;
        public $total_discount;

        public $products = null;
        public $vouchers = null;

        public static $definition = array(
            'table' => 'sm_ooc_cart',
            'primary' => 'id_sm_ooc_cart',
            'fields' => array(
                'id_currency' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'order_price' => array('type' =>  self::TYPE_FLOAT, 'validate' => 'isPrice'),
                'total_discount' => array('type' =>  self::TYPE_FLOAT, 'validate' => 'isPrice'),
            ),
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            parent::__construct($id, $id_lang, $id_shop);

            $this->products = $id ? OOCCartProductModel::getObjectsByIDCart($this->id_sm_ooc_cart) : null;
            $this->vouchers = $id ? OOCCartVouchersModel::getObjectsByIDCart($this->id_sm_ooc_cart) : null;
        }

        public function delete()
        {
            $result = true;
            if (!empty($this->products)) {
                foreach ($this->products as $product) {
                    $result = $result && $product->delete();
                }
            }
            return $result && parent::delete();
        }
    }
}
