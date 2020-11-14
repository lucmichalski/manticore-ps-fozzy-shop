<?php

class Tools extends ToolsCore 
{

	/* 
  Sort multiple arrays 
  Usage: ArrayOrderBy($array, 'field_name_1', SORT_ASC, 'field_name_1', SORT_ASC);
  */
  public static function ArrayOrderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

	



}
