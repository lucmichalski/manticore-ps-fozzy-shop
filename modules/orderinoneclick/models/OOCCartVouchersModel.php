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

if (!class_exists('OOCCartVouchersModel')) {

    class OOCCartVouchersModel extends ObjectModel
    {
        public $id_sm_ooc_cart_voucher;
        public $id_sm_ooc_cart;
        public $id_voucher;

        public $object = null;

        public static $definition = array(
            'table' => 'sm_ooc_cart_voucher',
            'primary' => 'id_sm_ooc_cart_voucher',
            'fields' => array(
                'id_sm_ooc_cart' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_voucher' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
            ),
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            parent::__construct($id, $id_lang, $id_shop);

            if ($id) {
                $this->object = new CartRule($this->id_voucher);
            }
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
                $objects[] = new OOCCartVouchersModel($ooc_product[self::$definition['primary']]);
            }
            return $objects;
        }
    }
}
