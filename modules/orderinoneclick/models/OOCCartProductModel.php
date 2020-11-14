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

if (!class_exists('OOCCartProductModel')) {

    class OOCCartProductModel extends ObjectModel
    {
        public $id_sm_ooc_cart_product;
        public $id_sm_ooc_cart;
        public $id_product;
        public $id_product_attribute;
        public $quantity;

        // Value at time of order
        public $price;

        public $image = null;
        public $product_object = null;
        public $available = null;

        public $customization = null;
        public $combination = null;

        public static $definition = array(
            'table' => 'sm_ooc_cart_product',
            'primary' => 'id_sm_ooc_cart_product',
            'fields' => array(
                'id_sm_ooc_cart' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_product' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_product_attribute' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'quantity' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'price' => array('type' =>  self::TYPE_FLOAT, 'validate' => 'isPrice'),
            ),
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            parent::__construct($id, $id_lang, $id_shop);

            if ($id) {
                $this->product_object = new Product($this->id_product);
                $img_cover = Image::getCover($this->id_product);
                $img_pa = Image::getBestImageAttribute(
                    Context::getContext()->shop->id,
                    Context::getContext()->language->id,
                    $this->id_product,
                    $this->id_product_attribute
                );
                $this->image = $img_pa ? $img_pa : $img_cover;
                $this->available = StockAvailable::getQuantityAvailableByProduct(
                    $this->id_product,
                    $this->id_product_attribute,
                    Context::getContext()->shop->id
                );
                $this->customization = OOCCustomizationModel::getObjectsByOOCProductID($this->id);
                $this->combination = $this->id_product_attribute ? new Combination($this->id_product_attribute) : null;
            }
        }

        public function delete()
        {
            $result = true;

            if (!empty($this->customization)) {
                foreach ($this->customization as $customization) {
                    $cust_to_delete = new OOCCustomizationModel($customization->id_sm_ooc_customization);
                    $result = $result && $cust_to_delete->delete();
                }
            }

            return $result && parent::delete();
        }

        public static function getObjectsByIDCart($id_sm_ooc_cart = null)
        {
            if (empty($id_sm_ooc_cart)) {
                return;
            }
            $objects = array();
            $sql = 'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_sm_ooc_cart = '.(int)$id_sm_ooc_cart;
            $sql_result = Db::getInstance()->ExecuteS($sql);
            foreach ($sql_result as $ooc_product) {
                $objects[] = new OOCCartProductModel($ooc_product[self::$definition['primary']]);
            }
            return $objects;
        }
    }
}
