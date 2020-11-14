<?php

abstract class Db extends DbCore
{
    public function execute($sql, $use_cache = false)
    {
        if (false !== stripos($sql, 'SQL_NO_CACHE')) $use_cache = false;
        elseif (false !== stripos($sql, 'SQL_CALC_FOUND_ROWS')) $use_cache = false;
        elseif (false !== stripos($sql, _DB_PREFIX_ . 'orders')) $use_cache = false;
        elseif (false !== stripos($sql, _DB_PREFIX_ . 'cart')) $use_cache = false;
        elseif (false !== stripos($sql, 'SQL_CACHE')) $use_cache = true;
        return parent::execute($sql, $use_cache);
    }

    public function executeS($sql, $array = true, $use_cache = false)
    {
        if (false !== stripos($sql, 'SQL_NO_CACHE')) $use_cache = false;
        elseif (false !== stripos($sql, 'SQL_CALC_FOUND_ROWS')) $use_cache = false;
        elseif (false !== stripos($sql, _DB_PREFIX_ . 'orders')) $use_cache = false;
        elseif (false !== stripos($sql, _DB_PREFIX_ . 'cart')) $use_cache = false;
        elseif (false !== stripos($sql, 'SQL_CACHE')) $use_cache = true;
        return parent::executeS($sql, $array, $use_cache);
    }
}