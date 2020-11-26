<?php
class PromotionsRuleClass extends ObjectModel {
    public $id_promotions_rules;
    public $id_rule;
    public $priority_rule;
    public $code_rule;
    public $count_rule;
    public $date_from;
    public $date_to;
    public $delivery_block;
    public $free_shipping;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'promotions_rules',
        'primary' => 'id_promotions_rules',
        'fields' => array(
            'id_promotions_rules' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_rule' => array('type' => self::TYPE_INT),
            'priority_rule' => array('type' => self::TYPE_INT),
            'code_rule' => array('type' => self::TYPE_STRING),
            'count_rule' => array('type' => self::TYPE_INT),
            'date_from' => array('type' => self::TYPE_STRING),
            'date_to' => array('type' => self::TYPE_STRING),
            'delivery_block' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );
}