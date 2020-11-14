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

if (!class_exists('OOCTypeOrderFieldsModel')) {

    class OOCTypeOrderFieldsModel extends ObjectModel
    {
        public $id;
        public $id_sm_ooc_type_order_field;
        public $name;
        public $validate_func;

        public static $definition = array(
            'table' => 'sm_ooc_type_order_field',
            'primary' => 'id_sm_ooc_type_order_field',
            'fields' => array(
                'name' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
                'validate_func' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
            ),
        );

        public static function getAllTypes()
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.self::$definition['table'].'`';
            return Db::getInstance()->executeS($sql);
        }

        public static function getObjectByValidateFunc($validate_func = '')
        {
            if (empty($validate_func)) {
                return;
            }

            $result = Db::getInstance()->getRow(
                'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                .' WHERE validate_func LIKE \''.pSQL($validate_func).'\''
            );

            return $result[self::$definition['primary']];
        }
    }
}
