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

if (!class_exists('OOCGroupModel')) {

    class OOCGroupModel extends ObjectModel
    {
        public $name;

        public static $definition = array(
            'table' => 'sm_ooc_group',
            'primary' => 'id_sm_ooc_group',
            'fields' => array(
                'name' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
            ),
        );

        public static function getGroupsArray()
        {
            $sql = 'SELECT `id_sm_ooc_group`, `name`
                FROM `'._DB_PREFIX_.'sm_ooc_group`
		ORDER BY `id_sm_ooc_group` ASC';
            $sql_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $groups_array = array();
            foreach ($sql_result as $group) {
                $groups_array[$group['id_sm_ooc_group']] = $group['name'];
            }
            return $groups_array;
        }
    }
}
