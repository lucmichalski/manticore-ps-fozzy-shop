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

if (!class_exists('OOCOrderFieldsModel')) {

    class OOCOrderFieldsModel extends ObjectModel
    {
        public $name;
        public $position;
        public $description;
        public $tip;
        public $id_shop;
        public $id_sm_ooc_type_order_field;
        public $required;
        public $active;

        public static $definition = array(
            'table' => 'sm_ooc_order_fields',
            'primary' => 'id_sm_ooc_order_fields',
            'multilang' => true,
            'fields' => array(
                'name' => array(
                    'type' => self::TYPE_STRING,
                    'validate' => 'isString',
                    'lang' => true,
                    'required' => true
                ),
                'description' => array('type' =>  self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
                'tip' => array('type' =>  self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
                'position' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_shop' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_sm_ooc_type_order_field' => array(
                    'type' =>  self::TYPE_INT,
                    'validate' => 'isunsignedInt',
                    'required' => true
                ),
                'required' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            ),
        );

        public function add($autodate = true, $null_values = false)
        {
            $this->id_shop = Context::getContext()->shop->id;
            $this->position = $this->getMaxPosition()+1;

            return parent::add($autodate, $null_values);
        }

        public function getMaxPosition()
        {
            $sql = 'SELECT MAX(`position`)'
                . ' FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_shop = '.(int)$this->id_shop;
            $position = DB::getInstance()->getValue($sql);

            return (is_numeric($position)) ? $position : (-1);
        }

        public static function getFieldsByShop($id_shop = null, $active = true)
        {
            if (!$id_shop) {
                return null;
            }

            $sql = 'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_shop = '.(int)$id_shop.' AND active = '.(int)$active.' ORDER BY position ASC';
            $result = Db::getInstance()->ExecuteS($sql);

            $array = array();
            foreach ($result as $field) {
                $object = new self($field[self::$definition['primary']]);
                $array[] = (array)$object;
            }
            return $array;
        }
    }
}
