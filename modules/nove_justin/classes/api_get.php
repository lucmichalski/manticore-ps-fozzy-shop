<?php
class Justin_get
{
public static function areaList()
    {
      $sql_area = "SELECT * FROM `" . _DB_PREFIX_ . "nv_justin_region` WHERE 1";
      $areas_sql = Db::getInstance()->executes($sql_area);
      
      return $areas_sql;
    }

public static function townList($area_uuid)
    {
      $sql_area = "SELECT * FROM `" . _DB_PREFIX_ . "nv_justin_towns` WHERE `owner_uuid` = '".$area_uuid."'";
      $areas_sql = Db::getInstance()->executes($sql_area);
      return $areas_sql;
    }

public static function wareList($town_uuid)
    {
      $sql_area = "SELECT * FROM `" . _DB_PREFIX_ . "nv_justin_ware` WHERE `owner_uuid` = '".$town_uuid."'";
      $areas_sql = Db::getInstance()->executes($sql_area);
      
      return $areas_sql;
    }

}  