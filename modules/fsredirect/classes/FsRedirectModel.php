<?php
/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class FsRedirectModel extends ObjectModel
{
    /** @var integer fsredirect id*/
    public $id;

    /** @var integer fsredirect id shop*/
    public $id_shop;

    /** @var string fsredirect old url */
    public $old_url;

    /** @var string fsredirect matching type */
    public $matching_type;

    /** @var string fsredirect new url */
    public $new_url;

    /** @var string fsredirect redirect type */
    public $redirect_type;

    /** @var boolean fsredirect statuts */
    public $active = true;

    /** @var string fsredirect creation date */
    public $date_add;

    /** @var string fsredirect last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'fsredirect',
        'primary' => 'id_fsredirect',
        'multilang' => false,
        'fields' => array(
            'id_shop' =>       array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'old_url' =>       array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true),
            'matching_type' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'new_url' =>       array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true),
            'redirect_type' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'active' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>      array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>      array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
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
        if (isset($filter['id_fsredirect']) && $filter['id_fsredirect'] != '') {
            $where .= ' AND id_fsredirect = \''.pSQL($filter['id_fsredirect']).'\'';
        }

        if (isset($filter['matching_type']) && $filter['matching_type'] != '') {
            $where .= ' AND matching_type = \''.pSQL($filter['matching_type']).'\'';
        }

        if (isset($filter['old_url']) && $filter['old_url'] != '') {
            $where .= ' AND old_url LIKE \'%'.pSQL($filter['old_url']).'%\'';
        }

        if (isset($filter['new_url']) && $filter['new_url'] != '') {
            $where .= ' AND new_url LIKE \'%'.pSQL($filter['new_url']).'%\'';
        }

        if (isset($filter['redirect_type']) && $filter['redirect_type'] != '') {
            $where .= ' AND redirect_type = \''.pSQL($filter['redirect_type']).'\'';
        }

        if (isset($filter['active']) && $filter['active'] != '') {
            $where .= ' AND active = \''.pSQL($filter['active']).'\'';
        }

        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'fsredirect` WHERE '.$where.' ORDER BY '.$filter['order_by'].' '.
            $filter['order_way'].' LIMIT '.(($filter['page'] - 1) * $filter['limit']).', '.$filter['limit']
        );
    }

    public static function getListContentCount($filter)
    {
        $where = str_replace(' AND ', '', Shop::addSqlRestrictionOnLang());
        if (isset($filter['id_fsredirect']) && $filter['id_fsredirect'] != '') {
            $where .= ' AND id_fsredirect = \''.pSQL($filter['id_fsredirect']).'\'';
        }

        if (isset($filter['matching_type']) && $filter['matching_type'] != '') {
            $where .= ' AND matching_type = \''.pSQL($filter['matching_type']).'\'';
        }

        if (isset($filter['old_url']) && $filter['old_url'] != '') {
            $where .= ' AND old_url LIKE \'%'.pSQL($filter['old_url']).'%\'';
        }

        if (isset($filter['new_url']) && $filter['new_url'] != '') {
            $where .= ' AND new_url LIKE \'%'.pSQL($filter['new_url']).'%\'';
        }

        if (isset($filter['redirect_type']) && $filter['redirect_type'] != '') {
            $where .= ' AND redirect_type = \''.pSQL($filter['redirect_type']).'\'';
        }

        if (isset($filter['active']) && $filter['active'] != '') {
            $where .= ' AND active = \''.pSQL($filter['active']).'\'';
        }

        $result = Db::getInstance()->executeS('SELECT id_fsredirect FROM `'._DB_PREFIX_.'fsredirect` WHERE '.$where);
        return count($result);
    }

    public static function getAllActive()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'fsredirect` WHERE active = \'1\''.Shop::addSqlRestrictionOnLang()
        );
    }

    public static function getAll()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'fsredirect` WHERE '.str_replace(' AND ', '', Shop::addSqlRestrictionOnLang())
        );
    }

    public static function deleteAll($id_shop)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'fsredirect` WHERE `id_shop` = \''.pSQL($id_shop).'\''
        );
    }
}
