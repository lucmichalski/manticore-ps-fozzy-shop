<?php
// Sample file for module update

if (!defined('_PS_VERSION_'))
  exit;

// object module ($this) available
function upgrade_module_1_2($object)
{
  //return Db::getInstance()->execute('UPDATE SQL QUERY');
  return true;
}
?>
