<?php
class ProfitCalculationsClass extends ObjectModel {
	public $id_profitcalculations;
	public $id_order;
	public $type_action;
	public $debit;
	public $credit;
	public $profit;
	public $comment;
	public $active;
	public $date_transaction;
	
	public static $definition = array(
		'table' => 'profitcalculations',
		'primary' => 'id_profitcalculations',
		'fields' => array(
			'id_profitcalculations' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'type_action' => array('type' => self::TYPE_STRING),
			'id_order' => array('type' => self::TYPE_INT),
			'debit' => array('type' => self::TYPE_INT),
			'credit' => array('type' => self::TYPE_INT),
			'profit' => array('type' => self::TYPE_INT),
			'comment' => array('type' => self::TYPE_STRING),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_transaction' => array('type' => self::TYPE_STRING)
		),
	);
}