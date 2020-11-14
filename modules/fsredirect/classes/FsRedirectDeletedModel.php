<?php
/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class FsRedirectDeletedModel extends ObjectModel
{
    /** @var integer fsredirectdeleted id*/
    public $id;

    /** @var integer fsredirectdeleted id shop*/
    public $id_shop;

    /** @var string fsredirectdeleted name */
    public $name;

    /** @var string fsredirectdeleted url */
    public $url;

    /** @var string fsredirectdeleted type */
    public $type;

    /** @var string fsredirectdeleted creation date */
    public $date_add;

    /** @var string fsredirectdeleted last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'fsredirect_deleted',
        'primary' => 'id_fsredirect_deleted',
        'multilang' => false,
        'fields' => array(
            'id_shop' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'name' =>       array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'url' =>        array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true),
            'type' =>       array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date_add' =>   array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>   array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        )
    );

    public function toArray()
    {
        $values = array();
        foreach (array_keys(self::$definition['fields']) as $key) {
            $values[$key] = $this->{$key};
        }
        return $values;
    }

    public function copyFromPost()
    {
        foreach (array_keys(self::$definition['fields']) as $key) {
            $this->{$key} = trim(Tools::getValue($key));
        }
    }

    public static function getListContent($filter)
    {
        $where = str_replace(' AND ', '', Shop::addSqlRestrictionOnLang());
        if (isset($filter['id_fsredirect_deleted']) && $filter['id_fsredirect_deleted'] != '') {
            $where .= ' AND id_fsredirect_deleted = \''.pSQL($filter['id_fsredirect_deleted']).'\'';
        }

        if (isset($filter['url']) && $filter['url'] != '') {
            $where .= ' AND url LIKE \'%'.pSQL($filter['url']).'%\'';
        }

        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'fsredirect_deleted` WHERE '.$where.' ORDER BY '.$filter['order_by'].' '.
            $filter['order_way'].' LIMIT '.(($filter['page'] - 1) * $filter['limit']).', '.$filter['limit']
        );
    }

    public static function getListContentCount($filter)
    {
        $where = str_replace(' AND ', '', Shop::addSqlRestrictionOnLang());
        if (isset($filter['id_fsredirect_deleted']) && $filter['id_fsredirect_deleted'] != '') {
            $where .= ' AND id_fsredirect_deleted = \''.pSQL($filter['id_fsredirect_deleted']).'\'';
        }

        if (isset($filter['url']) && $filter['url'] != '') {
            $where .= ' AND url LIKE \'%'.pSQL($filter['url']).'%\'';
        }

        $result = Db::getInstance()->executeS(
            'SELECT id_fsredirect_deleted FROM `'._DB_PREFIX_.'fsredirect_deleted` WHERE '.$where
        );
        return count($result);
    }

    public static function getAll()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'fsredirect_deleted` WHERE '.
            str_replace(' AND ', '', Shop::addSqlRestrictionOnLang())
        );
    }

    public static function deleteAll($id_shop)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'fsredirect_deleted` WHERE `id_shop` = \''.pSQL($id_shop).'\''
        );
    }

    public static function deleteByUrl($url, $id_shop)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'fsredirect_deleted` WHERE
            `url` = \''.pSQL($url).'\' AND `id_shop` = \''.pSQL($id_shop).'\'');
    }

    public static function isUrlInDatabase($url, $id_shop)
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT id_fsredirect_deleted FROM `'._DB_PREFIX_.'fsredirect_deleted` WHERE `url` = \''.
            pSQL($url).'\' AND `id_shop` = \''.pSQL($id_shop).'\''
        );
    }
}
