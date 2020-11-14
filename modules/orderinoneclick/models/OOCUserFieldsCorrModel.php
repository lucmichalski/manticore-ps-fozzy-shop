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

if (!class_exists('OOCUserFieldsCorrModel')) {

    class OOCUserFieldsCorrModel extends ObjectModel
    {
        public $id;
        public $id_sm_ooc_user_field_corr;
        public $id_sm_ooc_type_order_field;
        public $corr;

        public static $definition = array(
            'table' => 'sm_ooc_user_field_corr',
            'primary' => 'id_sm_ooc_user_field_corr',
            'fields' => array(
                'id_sm_ooc_type_order_field' => array(
                    'type' =>  self::TYPE_INT,
                    'validate' => 'isunsignedInt',
                    'required' => true
                ),
                'corr' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            ),
        );

        public static function getCorrByIDType($id_sm_ooc_type_order_field = null)
        {
            if (!$id_sm_ooc_type_order_field) {
                return;
            }

            $sql = 'SELECT corr FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_sm_ooc_type_order_field = '.(int)$id_sm_ooc_type_order_field;
            $result = Db::getInstance()->getRow($sql);

            return $result ? $result['corr'] : null;
        }
    }
}
