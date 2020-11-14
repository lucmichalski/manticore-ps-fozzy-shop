<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
define('UNFRIENDLY_ERRORS', false);

//ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_NOTICE);

function memoryUsage($usage, $base_memory_usage) {
printf("Bytes diff: %d\n", ($usage - $base_memory_usage)/1048576);
}

if (isset($_POST['offset']) && $_POST['offset']) $offset=(int)$_POST['offset'];
else $offset = 1;
if ($offset == 0) $offset = 1;

//$offset = 2;

function debugfile ($text = ''){
  $fp = fopen("file.txt", "w");
  fwrite($fp, $text);
  fclose($fp);
}
 
 if (isset($_POST['import_options']) && $_POST['import_options']) 
    {
    $import_option=$_POST['import_options'];
    }
  else
    {
    return;
    }    
 
  if (isset($_POST['delete_features']) && $_POST['delete_features']) 
    {
    $delete_features = (int)$_POST['delete_features'];
    }
  else
    {
    $delete_features = 0;
    }
    
  if (isset($_POST['off_products']) && $_POST['off_products']) 
    {
    $off_products = (int)$_POST['off_products'];
    }
  else
    {
    $off_products = 0;
    }

//$import_option = 'updatenames';

  function remove_tags ($str, $allowed_tags = null, $remove_id = false, $remove_class = false, $remove_style = false, $remove_other = false)
  {
   if ($remove_id) $str = preg_replace('/id=(["\'])[^\1]*?\1/i', '', $str, -1);
   if ($remove_class) $str = preg_replace('/class=(["\'])[^\1]*?\1/i', '', $str, -1);
   if ($remove_style) $str = preg_replace('/style=(["\'])[^\1]*?\1/i', '', $str, -1);
   if ($remove_other) 
    {
      $str = preg_replace('/onload=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/onclick=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/onmouseover=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/title=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/alt=(["\'])[^\1]*?\1/i', '', $str, -1);
    }
   $str = strip_tags($str, $allowed_tags);
   return $str;
  }
  
  function createMultiLangField($field)
    {
        $res = array();
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }

        return $res;
    }
  
	function copyImg($id_entity, $id_image = null, $url, $entity = 'products', $regenerate = true)
	{
		$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
		$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

		switch ($entity)
		{
			default:
			case 'products':
				$image_obj = new Image($id_image);
				$path = $image_obj->getPathForCreation();
			break;
			case 'categories':
				$path = _PS_CAT_IMG_DIR_.(int)$id_entity;
			break;
			case 'manufacturers':
				$path = _PS_MANU_IMG_DIR_.(int)$id_entity;
			break;
			case 'suppliers':
				$path = _PS_SUPP_IMG_DIR_.(int)$id_entity;
			break;
		}

		$url = str_replace(' ', '%20', trim($url));
		$url = urldecode($url);
		$parced_url = parse_url($url);

		if (isset($parced_url['path']))
		{
			$uri = ltrim($parced_url['path'], '/');
			$parts = explode('/', $uri);
			foreach ($parts as &$part)
				$part = urlencode ($part);
			unset($part);
			$parced_url['path'] = '/'.implode('/', $parts);
		}

		if (isset($parced_url['query']))
		{
			$query_parts = array();
			parse_str($parced_url['query'], $query_parts);
			$parced_url['query'] = http_build_query($query_parts);
		}

		if (!function_exists('http_build_url'))
			require_once(_PS_TOOL_DIR_.'http_build_url/http_build_url.php');

		$url = http_build_url('', $parced_url);

		// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
		if (!ImageManager::checkImageMemoryLimit($url))
			return false;

		// 'file_exists' doesn't work on distant file, and getimagesize makes the import slower.
		// Just hide the warning, the processing will be the same.
		if (Tools::copy($url, $tmpfile))
		{
			ImageManager::resize($tmpfile, $path.'.jpg');
			$images_types = ImageType::getImagesTypes($entity);

			if ($regenerate)
				foreach ($images_types as $image_type)
				{
					ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']);
					if (in_array($image_type['id_image_type'], $watermark_types))
						Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
				}
		}
		else
		{
			unlink($tmpfile);
			return false;
		}
		unlink($tmpfile);
		return true;
	}
  
  $sqlcol = 'SELECT `id_excel` FROM `'._DB_PREFIX_.'exceltable`';
  $kolline = count(Db::getInstance()->executeS($sqlcol));

  $sql_row = 'SELECT * FROM `'._DB_PREFIX_.'exceltable` WHERE `id_excel` = '.$offset; 
  $row = Db::getInstance()->getRow($sql_row);
  $alllang = Language::getLanguages(false);
  $name = array();
  $link_rewrite = array();
  $cur = Configuration::get('PS_EXCEL_CUR');
  $art_unical = Configuration::get('PS_EXCEL_ART');
  $clsale = Configuration::get('PS_EXCEL_CLSALE');

  switch ($import_option) 
          {
          case "newproduct":
              //Проверка товар-комбинация 
              if ($row['tovar'] != 1) break;
              
             //Проверка на уникальность артикула
              if ($art_unical) {
              
              $query = new DbQuery();
		          $query->select('p.reference');
		          $query->from('product', 'p');
		          $query->where('p.reference = \''.$row['reference'].'\'');

		          $is_reference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

                if ($is_reference)  {
                
                $sql_row_unic_updated = 'UPDATE `'._DB_PREFIX_.'exceltable` SET `checkin` = 0 WHERE `id_excel` = '. $offset; 
                Db::getInstance()->execute($sql_row_unic_updated);
                
                break;
                }
              } 
              
              if (isset($row['categories']) && !empty($row['categories']) && $row['categories'] != "-")
                {
                $cat_array = explode(",",$row['categories']);
                }
              $shop = new Shop ($row['id_shop'], $row['id_lang']);
              $rootcat = Category::getRootCategory ($row['id_lang'],$shop);
              unset ($shop);
              $rootcat = $rootcat->id;
              //$product = new Product(NULL,false,$row['id_lang'],$row['id_shop']);
              $product = new Product(NULL,true);
              $product->active = $row['active'];
              foreach ($alllang as $l)
                {
                 $name[$l['id_lang']] = $row['name'];
                 if ($row['link_rewrite'] != "-") 
                  {
                  $link_rewrite[$l['id_lang']] = $row['link_rewrite'];
                  }
                 else 
                  {
                  $link_rewrite[$l['id_lang']] = str_replace(" ",'-',mb_substr(Tools::link_rewrite($row['name']),0,128));
                  }
                }
              $name[2] = $row['name_ukr'];
              $product->name = $name;
              $product->price = $row['price'];
              $product->mrс = $row['wholesale_price'];
              $product->opt_kol = $row['quantity'];
              $product->on_sale = $row['on_sale'];
              if ($row['reference'] != "-") $product->reference = $row['reference'];
              $product->id_category_default = $cat_array[0] ? $cat_array[0] : $rootcat;
              $product->ean13 = $row['ean13'] != "-" ? $row['ean13'] : '';
              $product->width = $row['width'];
              $product->height = $row['height'];
              $product->depth = $row['depth'];
              $product->weight = $row['weight'];
              if ( Configuration::get('PS_EXCEL_CLHTML') == 1 )
                {
                $row['description_short'] = remove_tags($row['description_short'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                $row['description'] = remove_tags($row['description'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                }
              $product->description_short = '';
              $product->unity = $row['description_short'] != "-" ? $row['description_short'] : '';  
              $des = array();
              $des[1] = $row['description'] != "-" ? $row['description'] : '';
              $des[2] = $row['des_ukr'] != "-" ? $row['des_ukr'] : '';
              $product->description = $des;
              $product->online_only = $row['online_only'];
              //$product->available_for_order = $row['available_for_order'];
              if ($row['available_now'] == 'Охлажденная') $product->condition = 'used';
              if ($row['available_now'] == 'охлажденка') $product->condition = 'used';
              if ($row['available_now'] == 'Охлажденка') $product->condition = 'used';
              if ($row['available_now'] == 'Заморозка') $product->condition = 'refurbished';
              if ($row['description_short'] == 'кг') $product->minimal_quantity = 0.1;
              else  $product->minimal_quantity = 1;
              $product->available_for_order = 0;
              $product->show_price = 0;
              $product->visibility = 'none';
              $available_now = array();
              $available_now[1] = 'Есть в наличии';
              $available_now[2] = 'Є в нявності';
              $available_later = array();
              $available_later[1] = 'Нет в наличии';
              $available_later[2] = 'Немає в нявності';
              $product->available_now = $available_now;
              $product->available_later = $available_later;
              $meta_title = array();
              $meta_title[1] =  $row['name'].' в Киеве и пригороде: купить по хорошей цене с доставкой. Розница, фасовка '.$row['description_short'];
              $meta_title[2] =  $row['name_ukr'].' в Києві та передмісті: купити за хорошою ціною з доставкою. Роздріб, фасування '.$row['description_short'];
              $meta_description = array();
              $meta_description[1] = $row['name'].' - купить по хорошей цене в Киеве и пригороде в розницу, фасовка '.$row['description_short'].' ☎ 0 800 300 168 ✔ Только качественные и свежие товары ✔ Доставка 69 грн.';
              $meta_description[2] = $row['name_ukr'].' - купити за хорошою ціною в Києві і передмісті в роздріб, фасування '.$row['description_short'].' ☎ 0 800 300 168 ✔ Тільки якісні та свіжі товари ✔ Доставка 69 грн.';
              $product->meta_title = $meta_title;
              $product->meta_keywords = '';
              $product->meta_description = $meta_description;
              $product->link_rewrite = $link_rewrite;
              $product->add();

              if ($product->id) {
                $data = array();
                $id_shops = array(2,3,4,5,6,7,8,9);
                foreach ($id_shops as $id_shop) {
                 $sql1 = "CREATE TEMPORARY TABLE `foo` AS SELECT * FROM `"._DB_PREFIX_."product_shop` WHERE id_product = ".$product->id." AND id_shop=1";
                 $sql2 = "UPDATE `foo` SET id_shop=$id_shop";
                 $sql3 = "INSERT INTO `"._DB_PREFIX_."product_shop` SELECT * FROM `foo`";
                 $sql4 = "DROP TABLE `foo`";
                  Db::getInstance()->execute($sql1);
                  Db::getInstance()->execute($sql2);
                  Db::getInstance()->execute($sql3);
                  Db::getInstance()->execute($sql4);
                }
                 $sql5 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Киеве и пригороде: купить по хорошей цене с доставкой. Розница, фасовка ', p.`unity`) WHERE pl.`id_shop` IN (1,5) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql6 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Одессе и пригороде: купить по хорошей цене с доставкой. Розница, фасовка ', p.`unity`) WHERE pl.`id_shop` IN (2,6) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql7 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Днепре и пригороде: купить по хорошей цене с доставкой. Розница, фасовка ', p.`unity`) WHERE pl.`id_shop` IN (3) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql8 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Харькове и пригороде: купить по хорошей цене с доставкой. Розница, фасовка ', p.`unity`) WHERE pl.`id_shop` IN (4,7) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql9 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купить по хорошей цене в Киеве и пригороде в розницу, фасовка ', p.`unity`, ' ☎ 0 800 300 168 ✔ Только качественные и свежие товары ✔ Доставка 69 грн.') WHERE pl.`id_shop` IN (1,5) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql10 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купить по хорошей цене в Одессе и пригороде в розницу, фасовка ', p.`unity`, ' ☎ 0 800 300 168 ✔ Только качественные и свежие товары ✔ Доставка 49 грн.') WHERE pl.`id_shop` IN (2,6) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql11 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купить по хорошей цене в Днепре и пригороде в розницу, фасовка ', p.`unity`, ' ☎ 0 800 300 168 ✔ Только качественные и свежие товары ✔ Доставка 49 грн.') WHERE pl.`id_shop` IN (3) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql12 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купить по хорошей цене в Харькове и пригороде в розницу, фасовка ', p.`unity`, ' ☎ 0 800 300 168 ✔ Только качественные и свежие товары ✔ Доставка 40 грн.') WHERE pl.`id_shop` IN (4,7) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql_l_08 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Ровно и пригороде: купить по хорошей цене с доставкой. Розница, фасовка ', p.`unity`) WHERE pl.`id_shop` IN (8) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql_l_08_1 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купить по хорошей цене в Ровно и пригороде в розницу, фасовка ', p.`unity`, ' ☎ 0 800 300 168 ✔ Только качественные и свежие товары ✔ Доставка 40 грн.') WHERE pl.`id_shop` IN (8) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql_l_09 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Кременчуге и пригороде: купить по хорошей цене с доставкой. Розница, фасовка ', p.`unity`) WHERE pl.`id_shop` IN (9) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 $sql_l_09_1 = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купить по хорошей цене в Кременчуге и пригороде в розницу, фасовка ', p.`unity`, ' ☎ 0 800 300 168 ✔ Только качественные и свежие товары ✔ Доставка 40 грн') WHERE pl.`id_shop` IN (9) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_product` = ".$product->id;
                 Db::getInstance()->execute($sql5);
                 Db::getInstance()->execute($sql6);
                 Db::getInstance()->execute($sql7);
                 Db::getInstance()->execute($sql8);
                 Db::getInstance()->execute($sql9);
                 Db::getInstance()->execute($sql10);
                 Db::getInstance()->execute($sql11);
                 Db::getInstance()->execute($sql12);
                 Db::getInstance()->execute($sql_l_08);
                 Db::getInstance()->execute($sql_l_08_1);
                 Db::getInstance()->execute($sql_l_09);
                 Db::getInstance()->execute($sql_l_09_1);
                 $sql5u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Києві та передмісті: купити за хорошою ціною з доставкою. Роздріб, фасування ', p.`unity`) WHERE pl.`id_shop` IN (1,5) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql6u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Одесі і передмісті: купити за хорошою ціною з доставкою. Роздріб, фасування ', p.`unity`) WHERE pl.`id_shop` IN (2,6) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql7u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Дніпрі і передмісті: купити за хорошою ціною з доставкою. Роздріб, фасування ', p.`unity`) WHERE pl.`id_shop` IN (3) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql8u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Харкові і передмісті: купити за хорошою ціною з доставкою. Роздріб, фасування ', p.`unity`) WHERE pl.`id_shop` IN (4,7) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql9u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купити за хорошою ціною в Києві і передмісті в роздріб, фасування ', p.`unity`, ' ☎ 0 800 300 168 ✔ Тільки якісні та свіжі товари ✔ Доставка 69 грн.') WHERE pl.`id_shop` IN (1,5) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql10u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купити за хорошою ціною в Одесі і передмісті в роздріб, фасування ', p.`unity`, ' ☎ 0 800 300 168 ✔ Тільки якісні та свіжі товари ✔ Доставка 49 грн.') WHERE pl.`id_shop` IN (2,6) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql11u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купити за хорошою ціною в Дніпрі і передмісті в роздріб, фасування ', p.`unity`, ' ☎ 0 800 300 168 ✔ Тільки якісні та свіжі товари ✔ Доставка 49 грн.') WHERE pl.`id_shop` IN (3) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql12u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купити за хорошою ціною в Харкові і передмісті в роздріб, фасування ', p.`unity`, ' ☎ 0 800 300 168 ✔ Тільки якісні та свіжі товари ✔ Доставка 40 грн.') WHERE pl.`id_shop` IN (4,7) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql_l_08u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Рівному і передмісті: купити за хорошою ціною з доставкою. Роздріб, фасування ', p.`unity`) WHERE pl.`id_shop` IN (8) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql_l_08_1u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купити за хорошою ціною в Рівному і передмісті в роздріб, фасування ', p.`unity`, ' ☎ 0 800 300 168 ✔ Тільки якісні та свіжі товари ✔ Доставка 40 грн.') WHERE pl.`id_shop` IN (8) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql_l_09u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_title` = CONCAT (pl.`name`, ' в Кременчуці і передмісті: купити за хорошою ціною з доставкою. Роздріб, фасування ', p.`unity`) WHERE pl.`id_shop` IN (9) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 $sql_l_09_1u = "UPDATE `"._DB_PREFIX_."product_lang` pl, `"._DB_PREFIX_."product` p SET pl.`meta_description` = CONCAT (pl.`name`, ' - купити за хорошою ціною в Кременчуці і передмісті в роздріб, фасування ', p.`unity`, ' ☎ 0 800 300 168 ✔ Тільки якісні та свіжі товари ✔ Доставка 40 грн.') WHERE pl.`id_shop` IN (9) AND pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2 AND pl.`id_product` = ".$product->id;
                 Db::getInstance()->execute($sql5u);
                 Db::getInstance()->execute($sql6u);
                 Db::getInstance()->execute($sql7u);
                 Db::getInstance()->execute($sql8u);
                 Db::getInstance()->execute($sql9u);
                 Db::getInstance()->execute($sql10u);
                 Db::getInstance()->execute($sql11u);
                 Db::getInstance()->execute($sql12u);
                 Db::getInstance()->execute($sql_l_08u);
                 Db::getInstance()->execute($sql_l_08_1u);
                 Db::getInstance()->execute($sql_l_09u);
                 Db::getInstance()->execute($sql_l_09_1u);
              }
              
              if ($cur) 
               {
               $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.(int)$product->id;
               Db::getInstance()->execute($sql_cur);
               }
              if ( isset($cat_array) && !empty($cat_array) ) {$product->addToCategories($cat_array); }
              else $product->addToCategories(array($rootcat));
              $id_product = $product->id;
           //   StockAvailable::setQuantity($id_product, 0, 0, $row['id_shop']);
              
              /*
              if ($row['supplier'] != "-") {
              $supplier_id = Supplier::getIdByName ($row['supplier']);
                  if (!$supplier_id)
                    {
                    $supplier = new Supplier();
                    $supplier->name = $row['supplier'];
                    $supplier->active = true;
                    $supplier->add();
                    $supplier_id = $supplier->id;
                    $supplier = 0;
                    unset ($supplier);
                    }
              $product->id_supplier = $supplier_id;
              $product->supplier_reference = $row['supplier_reference'] != "-" ? $row['supplier_reference'] : '';
              $product->supplier_name = $row['supplier'];
              $product->addSupplierReference($supplier_id, 0, $row['supplier_reference'] != "-" ? $row['supplier_reference'] : '');
              }
              */
              $product->id_supplier = 6;
              $product->supplier_reference = '';
              $product->supplier_name = 'Fozzy';
              $product->addSupplierReference(6, 0, '');
              
              if ($row['manufacturer'] != "-") {
              $manufacturer_id = Manufacturer::getIdByName ($row['manufacturer']);
                  if (!$manufacturer_id)
                    {
                    $manufacturer = new Manufacturer();
                    $manufacturer->name = $row['manufacturer'];
                    $manufacturer->active = true;
                    $manufacturer->add();
                    $manufacturer_id = $manufacturer->id;
                    $manufacturer = 0;
                    unset ($manufacturer);
                    }
              $product->id_manufacturer = $manufacturer_id;
              }
              $product->update();
              if ($row['reduction_s'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_s'];
                    $sp->reduction_type = 'amount';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    } 
              if ($row['reduction_p'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_p']/100;
                    $sp->reduction_type = 'percentage';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    }
                $features = $row['features'];
            				if (isset($features) && !empty($features) && $features != '-')  {
            					foreach (explode(",", $features) as $single_feature)
            					{
            						if (empty($single_feature))
            							continue;
            						$tab_feature = explode(':', $single_feature);
            						$feature_name = isset($tab_feature[0]) ? trim($tab_feature[0]) : '';
            						$feature_value = isset($tab_feature[1]) ? trim($tab_feature[1]) : '';
            						$position = isset($tab_feature[2]) ? (int)$tab_feature[2] - 1 : false;
            						$custom = isset($tab_feature[3]) ? (int)$tab_feature[3] : false;
            						if(!empty($feature_name) && !empty($feature_value))
            						{
            							$id_feature = (int)Feature::addFeatureImport($feature_name, $position);
            							$id_feature_value = (int)FeatureValue::addFeatureValueImport($id_feature, $feature_value, $id_product, $row['id_lang'], $custom);
            							Product::addFeatureProductImport($id_product, $id_feature, $id_feature_value);
            						}
            					}
            		// clean feature positions to avoid conflict
            		Feature::cleanPositions(); }
                // Images
                if (isset($row['images']) && $row['images'] != "-")
              			{
              				$row['images'] = explode(',', $row['images']);
                       $shops = array(2,3,4,5,6,7,8,9);
              				if (is_array($row['images'] ) && count($row['images'] ))
              					
                        foreach ($row['images'] as $url)
              					{
              						$url = trim($url);
              						$product_has_images = (bool)Image::getImages($row['id_lang'], $id_product);
              
              						$image = new Image();
              						$image->id_product = (int)$id_product;
              						$image->position = Image::getHighestPosition($id_product) + 1;
              						$image->cover = (!$product_has_images) ? true : false;
              
                          $image->legend = $name;
              
              						$field_error = $image->validateFields(UNFRIENDLY_ERRORS, true);
              						$lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERRORS, true);
                          
              						if ($field_error === true && $lang_field_error === true && $image->add())
              						{
              							$image->associateTo($row['id_shop']);
                            
              							if (!copyImg($id_product, $image->id, $url, 'products', !Tools::getValue('regenerate')))
              							{
              								$image->delete();
                              unset ($image);
              							}
              							else
              								$id_image[] = (int)$image->id;
              						}
              						else
              						{
              				
              						}
                          
              					}
                              $data = array();
                              foreach ($id_shops as $id_shop) {
                               $sql1 = "CREATE TEMPORARY TABLE `foo` AS SELECT * FROM `"._DB_PREFIX_."image_shop` WHERE id_product = $id_product AND id_shop=1";
                               $sql2 = "UPDATE `foo` SET id_shop=$id_shop";
                               $sql3 = "INSERT INTO `"._DB_PREFIX_."image_shop` SELECT * FROM `foo`";
                               $sql4 = "DROP TABLE `foo`";
                               Db::getInstance()->execute($sql1);
                               Db::getInstance()->execute($sql2);
                               Db::getInstance()->execute($sql3);
                               Db::getInstance()->execute($sql4);
                              }
              			}
              // Stocks
              foreach ($id_shops as $id_shop) {
                 $sql_q1 = "CREATE TEMPORARY TABLE `foo2` AS SELECT * FROM `"._DB_PREFIX_."stock_available` WHERE id_product = ".$product->id." AND id_shop=1";
                 $sql_q2 = "UPDATE `foo2` SET id_shop=$id_shop";
                 $sql_q3 = "INSERT INTO `ps_stock_available` (`id_product`, `id_product_attribute`, `id_shop`, `id_shop_group`, `quantity`, `physical_quantity`, `reserved_quantity`, `depends_on_stock`, `out_of_stock`, `location`) SELECT `id_product`,`id_product_attribute`,`id_shop`,`id_shop_group`,`quantity`,`physical_quantity`,`reserved_quantity`,`depends_on_stock`,`out_of_stock`,`location` FROM `foo2`";
                 $sql_q4 = "DROP TABLE `foo2`";
                  Db::getInstance()->execute($sql_q1);
                  Db::getInstance()->execute($sql_q2);
                  Db::getInstance()->execute($sql_q3);
                  Db::getInstance()->execute($sql_q4);
                }
              $sql13 = "UPDATE `"._DB_PREFIX_."stock_available` SET `out_of_stock` = 2, `quantity` = 0 WHERE `id_product` = ".$product->id;
              Db::getInstance()->execute($sql13);
              //actions
              $id_shops_actions = array(1,2,3,4,8,9);
              foreach ($id_shops_actions as $id_shop) {
                $sql_at_01 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) VALUES (0, 0, ".$product->id.", ".$id_shop.", 0, 0, 0, 0, 0, 0, -1, 1, 0, 1, 'amount', '0000-00-00 00:00:00', '2001-01-01 00:00:00')";
                $sql_at_02 = "INSERT INTO `"._DB_PREFIX_."specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) VALUES (0, 0, ".$product->id.", ".$id_shop.", 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 'amount', '0000-00-00 00:00:00', '2001-01-01 00:00:00')";
             //   $sql_at_03 = "INSERT INTO `"._DB_PREFIX_."specific_price_priority` (`id_product`, `priority`) VALUES (".$product->id.", 'id_shop;id_currency;id_country;id_group')";
                Db::getInstance()->execute($sql_at_01);
                Db::getInstance()->execute($sql_at_02);
             //   Db::getInstance()->execute($sql_at_03);
              }
          break;
          case "fullupdate":
             if ($row['tovar'] == 1)
             {
             $id_product = (int)$row['id_product'];
             $id_attribute = 0;
             }
             else 
             { 
             $id_attribute = (int)$row['tovar'];
             $id_product_a = (int)$row['id_product'];
             $id_product = 0;
             }
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              $product->active = $row['active'];
              $product->name = $row['name'];
              $product->price = $row['price'];
              $product->wholesale_price = $row['wholesale_price'];
              $product->on_sale = $row['on_sale'];
              $product->reference = $row['reference'] != "-" ? $row['reference'] : '';
              $product->ean13 = $row['ean13'] != "-" ? $row['ean13'] : '';
              $product->width = $row['width'];
              $product->height = $row['height'];
              $product->depth = $row['depth'];
              $product->weight = $row['weight'];
              StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
              if ( Configuration::get('PS_EXCEL_CLHTML') == 1 )
                {
                $row['description_short'] = remove_tags($row['description_short'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                $row['description'] = remove_tags($row['description'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                }
              $product->description_short = $row['description_short'] != "-" ? $row['description_short'] : '';
              $product->description = $row['description'] != "-" ? $row['description'] : '';
              $product->online_only = $row['online_only'];
              $product->available_for_order = $row['available_for_order'];
              $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
              $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
              $product->meta_title = $row['meta_title'] != "-" ? $row['meta_title'] : '';
              $product->meta_keywords = $row['meta_keywords'] != "-" ? $row['meta_keywords'] : '';
              $product->meta_description = $row['meta_description'] != "-" ? $row['meta_description'] : '';
              if ($cur) 
               {
               $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.$id_product;
               Db::getInstance()->execute($sql_cur);
               }
              if ($row['link_rewrite'] != "-") $product->link_rewrite = $row['link_rewrite'];
              else $product->link_rewrite = str_replace(" ",'-',mb_substr(Tools::link_rewrite($row['name']),0,128));
              if ($row['supplier'] != "-") {
              $supplier_id = Supplier::getIdByName ($row['supplier']);
                  if (!$supplier_id)
                    {
                    $supplier = new Supplier();
                    $supplier->name = $row['supplier'];
                    $supplier->active = true;
                    $supplier->add();
                    $supplier_id = $supplier->id;
                    $supplier = 0;
                    unset ($supplier);
                    }
              $product->id_supplier = $supplier_id;
              $product->supplier_reference = $row['supplier_reference'] != "-" ? $row['supplier_reference'] : '';
              $product->supplier_name = $row['supplier'];
              $product->addSupplierReference($supplier_id, 0, $row['supplier_reference'] != "-" ? $row['supplier_reference'] : '');
              }
              if ($row['manufacturer'] != "-") {
              $manufacturer_id = Manufacturer::getIdByName ($row['manufacturer']);
                  if (!$manufacturer_id)
                    {
                    $manufacturer = new Manufacturer();
                    $manufacturer->name = $row['manufacturer'];
                    $manufacturer->active = true;
                    $manufacturer->add();
                    $manufacturer_id = $manufacturer->id;
                    $manufacturer = 0;
                    unset ($manufacturer);
                    }
              $product->id_manufacturer = $manufacturer_id;
              }
              $product->update();
              
              if ($clsale) {
                $sp = new SpecificPrice();
                $sp->id_product = $id_product;
                $sp->deleteByProductId($id_product);
              }
              
              if ($row['reduction_s'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_s'];
                    $sp->reduction_type = 'amount';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    } 
              if ($row['reduction_p'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_p']/100;
                    $sp->reduction_type = 'percentage';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    }
                if ($delete_features == 1) $product->deleteProductFeatures(); 
                $features = $row['features'];
            				if (isset($features) && !empty($features) && $features != '-')  {
            					foreach (explode(",", $features) as $single_feature)
            					{
            						if (empty($single_feature))
            							continue;
            						$tab_feature = explode(':', $single_feature);
            						$feature_name = isset($tab_feature[0]) ? trim($tab_feature[0]) : '';
            						$feature_value = isset($tab_feature[1]) ? trim($tab_feature[1]) : '';
            						$position = isset($tab_feature[2]) ? (int)$tab_feature[2] - 1 : false;
            						$custom = isset($tab_feature[3]) ? (int)$tab_feature[3] : false;
            						if(!empty($feature_name) && !empty($feature_value))
            						{
            							$id_feature = (int)Feature::addFeatureImport($feature_name, $position);
            							$id_feature_value = (int)FeatureValue::addFeatureValueImport($id_feature, $feature_value, $id_product, $row['id_lang'], $custom);
            							Product::addFeatureProductImport($id_product, $id_feature, $id_feature_value);
            						}
            					}
            		// clean feature positions to avoid conflict
            		Feature::cleanPositions(); }
                // Images
                if ($row['images_del'] == 1) $product->deleteImages();
                if (isset($row['images']) && $row['images'] != "-")
              			{
              				$row['images'] = explode(',', $row['images']);
              
              				if (is_array($row['images'] ) && count($row['images'] ))
              					foreach ($row['images'] as $url)
              					{
              						$url = trim($url);
              						$product_has_images = (bool)Image::getImages($row['id_lang'], $id_product);
              
              						$image = new Image();
              						$image->id_product = (int)$id_product;
              						$image->position = Image::getHighestPosition($id_product) + 1;
              						$image->cover = (!$product_has_images) ? true : false;
              
              						$field_error = $image->validateFields(UNFRIENDLY_ERRORS, true);
              						$lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERRORS, true);
              
              						if ($field_error === true && $lang_field_error === true && $image->add())
              						{
              							$image->associateTo($row['id_shop']);
              							if (!copyImg($id_product, $image->id, $url, 'products', !Tools::getValue('regenerate')))
              							{
              								$image->delete();
              							}
              							else
              								$id_image[] = (int)$image->id;
              						}
              						else
              						{
              	
              						}
              					}
              			}
                }
                if ($id_attribute) {
                                                        // 
                $product = new Product($id_product_a,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                $attributes = $product->getAttributesResume ($row['id_lang']);
                $attributes_images = $product->getCombinationImages ($row['id_lang']);
                
                foreach ($attributes as $attribute)
                          {
                          if ($attribute['id_product_attribute'] == $id_attribute) 
                            {
                            $attributes_array['id_product'] = $attribute['id_product'];
                            $attributes_array['name'] = $attribute['attribute_designation'];
                            $attributes_array['reference'] = $attribute['reference'];
                            $attributes_array['ean13'] = $attribute['ean13'];
                            $attributes_array['wholesale_price'] = $attribute['wholesale_price'];
                            $attributes_array['id_category_default'] = 0;
                            $attributes_array['default_on'] = $attribute['default_on'];
                            $attributes_array['unit_price_impact'] = $attribute['unit_price_impact'];
                            $attributes_array['ecotax'] = $attribute['ecotax'];
                            $attributes_array['available_date'] = $attribute['available_date'];
                            $attributes_array['location'] = $attribute['location'];
                            $attributes_array['upc'] = $attribute['upc'];
                            $attributes_array['minimal_quantity'] = $attribute['minimal_quantity'];
                            $attributes_array['price'] = $attribute['price'];
                            $attributes_array['quantity'] = $attribute['quantity'];
                            $attributes_array['weight'] = $attribute['weight'];
                            $attributes_array['tovar'] = $attribute['id_product_attribute'];
                            $attributes_array['images'] = '';
                            
                            if ( isset( $attributes_images[$attribute['id_product_attribute']] ) )
                              {
                                 $attr_images = array();
                                 foreach ($attributes_images[$attribute['id_product_attribute']] as $attr_image)
                                  {
                                   $attr_images[]= $attr_image['id_image'];
                                  }
                                 
                              $attributes_array['images'] = $attr_images;
                              unset($attr_images);
                              }
                            
                            }
                          }
              $product -> updateAttribute ($id_attribute, $row['wholesale_price'], $row['price'],$row['weight'],$attributes_array['unit_price_impact'],$attributes_array['ecotax'],$attributes_array['images'],$row['reference'],$row['ean13'],$attributes_array['default_on'],$attributes_array['location'],$attributes_array['upc'],$attributes_array['minimal_quantity'],$attributes_array['available_date'],false);
                StockAvailable::setQuantity($id_product_a, $id_attribute, $row['quantity'], $row['id_shop']);
                }          
          break;
          case "updateimages":
           if ($row['tovar'] != 1)
           {
            $id_attribute = (int)$row['tovar'];
            if ($row['images'] != '-') $row['images'] = explode(',', str_replace ('.',',',(string)$row['images']));
            
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_image` WHERE `id_product_attribute` = '.$id_attribute);
            
            if (is_array($row['images']) && count($row['images']))
		        {
              $sql_values = array();

        			foreach ($row['images'] as $value)
        				$sql_values[] = '('.$id_attribute.', '.(int)$value.')';
        
        			if (is_array($sql_values) && count($sql_values))
        				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`) VALUES '.implode(',', $sql_values));
            }      
           }
           else
           {  
             $id_product = (int)$row['id_product'];
             
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
                // Images
                if ($row['images_del'] == 1) $product->deleteImages();
                if (isset($row['images']) && $row['images'] != "-")
              			{
              				$row['images'] = explode(',', $row['images']);
              
              				if (is_array($row['images'] ) && count($row['images'] ))
              					foreach ($row['images'] as $url)
              					{
              						$url = trim($url);
              						$product_has_images = (bool)Image::getImages($row['id_lang'], $id_product);
              
              						$image = new Image();
              						$image->id_product = (int)$id_product;
              						$image->position = Image::getHighestPosition($id_product) + 1;
              						$image->cover = (!$product_has_images) ? true : false;
              
              						$field_error = $image->validateFields(UNFRIENDLY_ERRORS, true);
              						$lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERRORS, true);
              
              						if ($field_error === true && $lang_field_error === true && $image->add())
              						{
              							$image->associateTo($row['id_shop']);
              							if (!copyImg($id_product, $image->id, $url, 'products', !Tools::getValue('regenerate')))
              							{
              								$image->delete();
              							}
              							else
              								$id_image[] = (int)$image->id;
              						}
              						else
              						{
              	
              						}
              					}
              			}
                }
              }
          break;
          case "updateimagesa":
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
                // Images
                if ($row['images_del'] == 1) 
                {
                 $sql_del_i1 = "DELETE FROM `"._DB_PREFIX_."image_lang` WHERE `id_image` IN (SELECT `id_image` FROM `"._DB_PREFIX_."image` WHERE `id_product` = ".$id_product.")";
                 $sql_del_i2 = "DELETE FROM `"._DB_PREFIX_."image` WHERE `id_product` = ".$id_product;
                 $sql_del_i3 = "DELETE FROM `"._DB_PREFIX_."image_shop` WHERE `id_product` = ".$id_product;
                 Db::getInstance()->execute($sql_del_i1);
                 Db::getInstance()->execute($sql_del_i2);
                 Db::getInstance()->execute($sql_del_i3);
                }
                
                if (isset($row['images']) && $row['images'] != "-")
              			{
              				$row['images'] = explode(',', $row['images']);
              
              				if (is_array($row['images'] ) && count($row['images'] ))
              					foreach ($row['images'] as $url)
              					{
              						$url = trim($url);
              						$product_has_images = (bool)Image::getImages($row['id_lang'], $id_product);
              
              						$image = new Image();
              						$image->id_product = (int)$id_product;
              						$image->position = Image::getHighestPosition($id_product) + 1;
              						$image->cover = (!$product_has_images) ? true : false;
              
              						$field_error = $image->validateFields(UNFRIENDLY_ERRORS, true);
              						$lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERRORS, true);
                          
              						if ($field_error === true && $lang_field_error === true && $image->add())
              						{
              							$image->associateTo($row['id_shop']);
                            
              							if (!copyImg($id_product, $image->id, $url, 'products', !Tools::getValue('regenerate')))
              							{
              								$image->delete();
                              unset ($image);
              							}
              							else
              								$id_image[] = (int)$image->id;
              						}
              						else
              						{
              				
              						}
                          
              					}
                         $id_shops = array(2,3,4,5,6,7,8,9);
                         $data = array();
                              foreach ($id_shops as $id_shop) {
                               $sql1 = "CREATE TEMPORARY TABLE `foo` AS SELECT * FROM `"._DB_PREFIX_."image_shop` WHERE id_product = $id_product AND id_shop=1";
                               $sql2 = "UPDATE `foo` SET id_shop=$id_shop";
                               $sql3 = "INSERT INTO `"._DB_PREFIX_."image_shop` SELECT * FROM `foo`";
                               $sql4 = "DROP TABLE `foo`";
                               Db::getInstance()->execute($sql1);
                               Db::getInstance()->execute($sql2);
                               Db::getInstance()->execute($sql3);
                               Db::getInstance()->execute($sql4);
                              }
              			}
                }
          break;
          case "updateprice":
             if ($row['tovar'] == 1)
             {
             $id_product = (int)$row['id_product'];
             $id_attribute = 0;
             }
             else 
             { 
             $id_attribute = (int)$row['tovar'];
             $id_product_a = (int)$row['id_product'];
             $id_product = 0;
             }
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              $product->active = $row['active'];
              $product->price = $row['price'];
              $product->wholesale_price = $row['wholesale_price'];
              $product->on_sale = $row['on_sale'];
              if ($cur) 
               {
               $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.$id_product;
               Db::getInstance()->execute($sql_cur);
               }
              $product->update();
              
              if ($clsale) {
                $sp = new SpecificPrice();
                $sp->id_product = $id_product;
                $sp->deleteByProductId($id_product);
              }
              
                if ($row['reduction_s'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_s'];
                    $sp->reduction_type = 'amount';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    } 
                if ($row['reduction_p'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_p']/100;
                    $sp->reduction_type = 'percentage';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    }
                } 
                if ($id_attribute) {  
                $product = new Product($id_product_a,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                $attributes = $product->getAttributesResume ($row['id_lang']); 
                foreach ($attributes as $attribute)
                          {
                          if ($attribute['id_product_attribute'] == $id_attribute) 
                            {
                            $attributes_array['id_product'] = $attribute['id_product'];
                            $attributes_array['name'] = $attribute['attribute_designation'];
                            $attributes_array['reference'] = $attribute['reference'];
                            $attributes_array['ean13'] = $attribute['ean13'];
                            $attributes_array['wholesale_price'] = $attribute['wholesale_price'];
                            $attributes_array['id_category_default'] = 0;
                            $attributes_array['default_on'] = $attribute['default_on'];
                            $attributes_array['unit_price_impact'] = $attribute['unit_price_impact'];
                            $attributes_array['ecotax'] = $attribute['ecotax'];
                            $attributes_array['available_date'] = $attribute['available_date'];
                            $attributes_array['location'] = $attribute['location'];
                            $attributes_array['upc'] = $attribute['upc'];
                            $attributes_array['minimal_quantity'] = $attribute['minimal_quantity'];
                            $attributes_array['price'] = $attribute['price'];
                            $attributes_array['quantity'] = $attribute['quantity'];
                            $attributes_array['weight'] = $attribute['weight'];
                            $attributes_array['tovar'] = $attribute['id_product_attribute'];
                            }
                          }
              $product -> updateAttribute ($id_attribute, $row['wholesale_price'], $row['price'],$attributes_array['weight'],$attributes_array['unit_price_impact'],$attributes_array['ecotax'],'',$attributes_array['reference'],$attributes_array['ean13'],$attributes_array['default_on'],$attributes_array['location'],$attributes_array['upc'],$attributes_array['minimal_quantity'],$attributes_array['available_date'],false);
                $attributes_array = 0;
                }          
          break;
          case "updatepra":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
              
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              $product->active = $row['active'];
              $product->price = $row['price'];
              $product->wholesale_price = $row['wholesale_price'];
              $product->on_sale = $row['on_sale'];
              if ($cur) 
               {
               $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.$id_product;
               Db::getInstance()->execute($sql_cur);
               }
              $product->update();
              
              if ($clsale) {
                $sp = new SpecificPrice();
                $sp->id_product = $id_product;
                $sp->deleteByProductId($id_product);
              }
              
                if ($row['reduction_s'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_s'];
                    $sp->reduction_type = 'amount';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    } 
                if ($row['reduction_p'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_p']/100;
                    $sp->reduction_type = 'percentage';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    }
              }
          break;
          case "updatepriceq":
             if ($row['tovar'] == 1)
             {
             $id_product = (int)$row['id_product'];
             $id_attribute = 0;
             }
             else 
             { 
             $id_attribute = (int)$row['tovar'];
             $id_product_a = (int)$row['id_product'];
             $id_product = 0;
             }
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              $product->active = $row['active'];
              $product->price = $row['price'];
              $product->wholesale_price = $row['wholesale_price'];
              $product->on_sale = $row['on_sale'];
              $product->available_for_order = $row['available_for_order'];
              $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
              $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
              if ($cur) 
               {
               $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.$id_product;
               Db::getInstance()->execute($sql_cur);
               }
              StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
              $product->update();
              
              if ($clsale) {
                $sp = new SpecificPrice();
                $sp->id_product = $id_product;
                $sp->deleteByProductId($id_product);
              }
              
                if ($row['reduction_s'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_s'];
                    $sp->reduction_type = 'amount';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    } 
                if ($row['reduction_p'] > 0)
                    {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                    $sp->reduction = $row['reduction_p']/100;
                    $sp->reduction_type = 'percentage';
                    $sp->from = $row['reduction_from'];
                    $sp->to = $row['reduction_to'];
                    $sp->id_shop = $row['id_shop'];
                    $sp->id_currency = 0;
                    $sp->id_country = 0;
                    $sp->id_group = 0;
                    $sp->id_customer = 0;
                    $sp->price = -1;
                    $sp->from_quantity = 1;
                    $sp->add();
                    }
                }
                if ($id_attribute) {  
                $product = new Product($id_product_a,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                $attributes = $product->getAttributesResume ($row['id_lang']);
                foreach ($attributes as $attribute)
                          {
                          if ($attribute['id_product_attribute'] == $id_attribute) 
                            {
                            $attributes_array['id_product'] = $attribute['id_product'];
                            $attributes_array['name'] = $attribute['attribute_designation'];
                            $attributes_array['reference'] = $attribute['reference'];
                            $attributes_array['ean13'] = $attribute['ean13'];
                            $attributes_array['wholesale_price'] = $attribute['wholesale_price'];
                            $attributes_array['id_category_default'] = 0;
                            $attributes_array['default_on'] = $attribute['default_on'];
                            $attributes_array['unit_price_impact'] = $attribute['unit_price_impact'];
                            $attributes_array['ecotax'] = $attribute['ecotax'];
                            $attributes_array['available_date'] = $attribute['available_date'];
                            $attributes_array['location'] = $attribute['location'];
                            $attributes_array['upc'] = $attribute['upc'];
                            $attributes_array['minimal_quantity'] = $attribute['minimal_quantity'];
                            $attributes_array['price'] = $attribute['price'];
                            $attributes_array['quantity'] = $attribute['quantity'];
                            $attributes_array['weight'] = $attribute['weight'];
                            $attributes_array['tovar'] = $attribute['id_product_attribute'];
                            }
                          }
              $product -> updateAttribute ($id_attribute, $row['wholesale_price'], $row['price'],$attributes_array['weight'],$attributes_array['unit_price_impact'],$attributes_array['ecotax'],'',$attributes_array['reference'],$attributes_array['ean13'],$attributes_array['default_on'],$attributes_array['location'],$attributes_array['upc'],$attributes_array['minimal_quantity'],$attributes_array['available_date'],false);
                $attributes_array = 0;
                StockAvailable::setQuantity($id_product_a, $id_attribute, $row['quantity'], $row['id_shop']);
                }          
          break;
          case "updatepraq":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product) 
              {
                 $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                 if (!$product->id) break;
                 $product->active = $row['active'];
                 $product->price = $row['price'];
                 $product->wholesale_price = $row['wholesale_price'];
                 $product->on_sale = $row['on_sale'];
                 $product->available_for_order = $row['available_for_order'];
                 $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
                 $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
                 if ($cur) 
                   {
                   $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.$id_product;
                   Db::getInstance()->execute($sql_cur);
                   }
                 StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
                 $product->update();
                 
                  if ($clsale) {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                  }
              
                if ($row['reduction_s'] > 0)
                       {
                       $sp = new SpecificPrice();
                       $sp->id_product = $id_product;
                       $sp->deleteByProductId($id_product);
                       $sp->reduction = $row['reduction_s'];
                       $sp->reduction_type = 'amount';
                       $sp->from = $row['reduction_from'];
                       $sp->to = $row['reduction_to'];
                       $sp->id_shop = $row['id_shop'];
                       $sp->id_currency = 0;
                       $sp->id_country = 0;
                       $sp->id_group = 0;
                       $sp->id_customer = 0;
                       $sp->price = -1;
                       $sp->from_quantity = 1;
                       $sp->add();
                       } 
                   if ($row['reduction_p'] > 0)
                       {
                       $sp = new SpecificPrice();
                       $sp->id_product = $id_product;
                       $sp->deleteByProductId($id_product);
                       $sp->reduction = $row['reduction_p']/100;
                       $sp->reduction_type = 'percentage';
                       $sp->from = $row['reduction_from'];
                       $sp->to = $row['reduction_to'];
                       $sp->id_shop = $row['id_shop'];
                       $sp->id_currency = 0;
                       $sp->id_country = 0;
                       $sp->id_group = 0;
                       $sp->id_customer = 0;
                       $sp->price = -1;
                       $sp->from_quantity = 1;
                       $sp->add();
                       }   
               }     
              
          break;
          case "updatepriceqfozzy":
              //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $id_product = (int)$row['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              if ( $product->price != $row['price'] || $product->active != $row['active'])
                  {
                  $product->active = $row['active'];
                  $product->on_sale = $row['on_sale'];
                  $product->price = $row['price'];
                  $product->wholesale_price = $row['wholesale_price'];
                  StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
                  $product->update();
                    if ($row['reduction_s'] > 0)
                        {
                        $sp = new SpecificPrice();
                        $sp->id_product = $id_product;
                        $sp->deleteByProductId($id_product);
                        $sp->reduction = $row['reduction_s'];
                        $sp->reduction_type = 'amount';
                        $sp->from = $row['reduction_from'];
                        $sp->to = $row['reduction_to'];
                        $sp->id_shop = $row['id_shop'];
                        $sp->id_currency = 0;
                        $sp->id_country = 0;
                        $sp->id_group = 0;
                        $sp->id_customer = 0;
                        $sp->price = -1;
                        $sp->from_quantity = 1;
                        $sp->add();
                        } 
                    if ($row['reduction_p'] > 0)
                        {
                        $sp = new SpecificPrice();
                        $sp->id_product = $id_product;
                        $sp->deleteByProductId($id_product);
                        $sp->reduction = $row['reduction_p']/100;
                        $sp->reduction_type = 'percentage';
                        $sp->from = $row['reduction_from'];
                        $sp->to = $row['reduction_to'];
                        $sp->id_shop = $row['id_shop'];
                        $sp->id_currency = 0;
                        $sp->id_country = 0;
                        $sp->id_group = 0;
                        $sp->id_customer = 0;
                        $sp->price = -1;
                        $sp->from_quantity = 1;
                        $sp->add();
                        }
                  }
                }          
          break;
          case "updatepriceqfozzysales":
                          //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $id_product = (int)$row['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              
              if (!$product->id) break;
              if ( $product->price != $row['price'] || $product->on_sale == 1 || $row['on_sale'] == 1)
                  {
                    if ($row['price'] > 0)
                      { 
                        if ($product->on_sale == 1) 
                        {
                          $sp = new SpecificPrice();
                          $sp->deleteByProductId($id_product);
                        }
                        $product->on_sale = 0;
                        $product->show_price = 1;
                        $product->available_for_order = 1;
                        $product->indexed = 1;
                        
                        if ($row['on_sale'] == 1) 
                          {
                            $delta_price = round ($product->price - $row['price'],2);
      
                            if ($delta_price > 0)
                              {
                              $row['reduction_s'] = $delta_price;
                              $product->on_sale = 1;
                              }
                            else
                              {
                              $product->price = $row['price'];
                              }
                          }
                        else
                          {
                            $product->price = $row['price'];
                          }
                        $product->wholesale_price = $row['wholesale_price'];
                        
                        StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
                        $product->update();
                        
                          if ($row['reduction_s'] > 0)
                              {
                              $sp = new SpecificPrice();
                              $sp->id_product = $id_product;
                              $sp->deleteByProductId($id_product);
                              $sp->reduction = $row['reduction_s'];
                              $sp->reduction_type = 'amount';
                              $sp->from = $row['reduction_from'];
                              $sp->to = $row['reduction_to'];
                              $sp->id_shop = $row['id_shop'];
                              $sp->id_currency = 0;
                              $sp->id_country = 0;
                              $sp->id_group = 0;
                              $sp->id_customer = 0;
                              $sp->price = -1;
                              $sp->from_quantity = 1;
                              $sp->add();
                              } 
                    }
                    else
                    {
                        if ($product->on_sale == 1) 
                          {
                            $sp = new SpecificPrice();
                            $sp->deleteByProductId($id_product);
                          }
                        $product->on_sale = 0;
                        $product->show_price = 0;
                        $product->available_for_order = 0;
                        $product->indexed = 1;
                        StockAvailable::setQuantity($id_product, 0, 0, $row['id_shop']);
                        $product->update();
                    }
                  }
                } 
          break;
          case "updatemass":
              if ($row['tovar'] == 1)
             {
             $id_product = (int)$row['id_product'];
             $id_attribute = 0;
             
             $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
             $product->available_for_order = $row['available_for_order'];
             $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
             $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
             $product->update();
             
             }
             else 
             { 
             $id_attribute = (int)$row['tovar'];
             $id_product_a = (int)$row['id_product'];
             $id_product = 0;
             }
             if ($id_product)
              {
              StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
              }
             if ($id_attribute) {  
                $product = new Product($id_product_a,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                StockAvailable::setQuantity($id_product_a, $id_attribute, $row['quantity'], $row['id_shop']);
                }      
          break;
          case "updatemassa":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              $product->available_for_order = $row['available_for_order'];
              $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
              $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
              $product->update();
              StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
              }
          break;
           case "updatemassean":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `ean13` = '".$row['ean13']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              $product->available_for_order = $row['available_for_order'];
              $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
              $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
              $product->update();
              StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
              }
          break;
          case "updatepraean":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `ean13` = '".$row['ean13']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product) 
              {
                 $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                 if (!$product->id) break;
                 $product->active = $row['active'];
                 $product->price = $row['price'];
                 $product->wholesale_price = $row['wholesale_price'];
                 $product->on_sale = $row['on_sale'];
                 $product->available_for_order = $row['available_for_order'];
                 $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
                 $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
                 if ($cur) 
                   {
                   $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.$id_product;
                   Db::getInstance()->execute($sql_cur);
                   }
                 $product->update();
                 
                  if ($clsale) {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                  }
              
                if ($row['reduction_s'] > 0)
                       {
                       $sp = new SpecificPrice();
                       $sp->id_product = $id_product;
                       $sp->deleteByProductId($id_product);
                       $sp->reduction = $row['reduction_s'];
                       $sp->reduction_type = 'amount';
                       $sp->from = $row['reduction_from'];
                       $sp->to = $row['reduction_to'];
                       $sp->id_shop = $row['id_shop'];
                       $sp->id_currency = 0;
                       $sp->id_country = 0;
                       $sp->id_group = 0;
                       $sp->id_customer = 0;
                       $sp->price = -1;
                       $sp->from_quantity = 1;
                       $sp->add();
                       } 
                   if ($row['reduction_p'] > 0)
                       {
                       $sp = new SpecificPrice();
                       $sp->id_product = $id_product;
                       $sp->deleteByProductId($id_product);
                       $sp->reduction = $row['reduction_p']/100;
                       $sp->reduction_type = 'percentage';
                       $sp->from = $row['reduction_from'];
                       $sp->to = $row['reduction_to'];
                       $sp->id_shop = $row['id_shop'];
                       $sp->id_currency = 0;
                       $sp->id_country = 0;
                       $sp->id_group = 0;
                       $sp->id_customer = 0;
                       $sp->price = -1;
                       $sp->from_quantity = 1;
                       $sp->add();
                       }   
               }     
              
          break;
          case "updatepraqean":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `ean13` = '".$row['ean13']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product) 
              {
                 $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                 if (!$product->id) break;
                 $product->active = $row['active'];
                 $product->price = $row['price'];
                 $product->wholesale_price = $row['wholesale_price'];
                 $product->on_sale = $row['on_sale'];
                 $product->available_for_order = $row['available_for_order'];
                 $product->available_now = $row['available_now'] != "-" ? $row['available_now'] : '';
                 $product->available_later = $row['available_later'] != "-" ? $row['available_later'] : '';
                 if ($cur) 
                   {
                   $sql_cur = 'UPDATE `'._DB_PREFIX_.'product` SET `pc_currency` = '.$row['cur'].' WHERE `id_product` = '.$id_product;
                   Db::getInstance()->execute($sql_cur);
                   }
                 StockAvailable::setQuantity($id_product, 0, $row['quantity'], $row['id_shop']);
                 $product->update();
                 
                  if ($clsale) {
                    $sp = new SpecificPrice();
                    $sp->id_product = $id_product;
                    $sp->deleteByProductId($id_product);
                  }
              
                if ($row['reduction_s'] > 0)
                       {
                       $sp = new SpecificPrice();
                       $sp->id_product = $id_product;
                       $sp->deleteByProductId($id_product);
                       $sp->reduction = $row['reduction_s'];
                       $sp->reduction_type = 'amount';
                       $sp->from = $row['reduction_from'];
                       $sp->to = $row['reduction_to'];
                       $sp->id_shop = $row['id_shop'];
                       $sp->id_currency = 0;
                       $sp->id_country = 0;
                       $sp->id_group = 0;
                       $sp->id_customer = 0;
                       $sp->price = -1;
                       $sp->from_quantity = 1;
                       $sp->add();
                       } 
                   if ($row['reduction_p'] > 0)
                       {
                       $sp = new SpecificPrice();
                       $sp->id_product = $id_product;
                       $sp->deleteByProductId($id_product);
                       $sp->reduction = $row['reduction_p']/100;
                       $sp->reduction_type = 'percentage';
                       $sp->from = $row['reduction_from'];
                       $sp->to = $row['reduction_to'];
                       $sp->id_shop = $row['id_shop'];
                       $sp->id_currency = 0;
                       $sp->id_country = 0;
                       $sp->id_group = 0;
                       $sp->id_customer = 0;
                       $sp->price = -1;
                       $sp->from_quantity = 1;
                       $sp->add();
                       }   
               }     
              
          break;
          case "updatedescription":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $id_product = (int)$row['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              if ( Configuration::get('PS_EXCEL_CLHTML') == 1 )
                {
                $row['description_short'] = remove_tags($row['description_short'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                $row['description'] = remove_tags($row['description'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                }
              if ($row['description'] != '-') $product->description = $row['description'];
              if ($row['description_short'] != '-') $product->description_short = $row['description_short'];
              $product->update();
              }
          break;
          case "updatedescriptiona":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
             // $product = new Product($id_product,false,$row['id_lang']);
             // if (!$product->id) break;
              if ( Configuration::get('PS_EXCEL_CLHTML') == 1 )
                {
                $row['description_short'] = remove_tags($row['description_short'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                $row['description'] = remove_tags($row['description'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                }
              if ($row['description'] != '-') {
                $sql_d = "UPDATE `"._DB_PREFIX_."product_lang` SET `description` = '".$row['description']."' WHERE `id_product` = ".$id_product;
                Db::getInstance()->execute($sql_d);
              }
              if ($row['description_short'] != '-') {
                $sql_d = "UPDATE `"._DB_PREFIX_."product_lang` SET `description_short` = '".$row['description_short']."' WHERE `id_product` = ".$id_product;
                Db::getInstance()->execute($sql_d);
              }
              
              }
          break;
          case "updateshortdescription":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $id_product = (int)$row['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              if ( Configuration::get('PS_EXCEL_CLHTML') == 1 )
                {
                $row['description_short'] = remove_tags($row['description_short'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                $row['description'] = remove_tags($row['description'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                }
              if ($row['description_short'] != '-') $product->description_short = $row['description_short'];
              $product->update();
              }
          break;
          case "updateshortdescriptiona":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              if ( Configuration::get('PS_EXCEL_CLHTML') == 1 )
                {
                $row['description_short'] = remove_tags($row['description_short'], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                }
              if ($row['description_short'] != '-') $product->description_short = $row['description_short'];
              $product->update();
              }
          break;
          case "updateean13a":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              if ($row['ean13'] != '-') $product->ean13 = $row['ean13'];
              $product->update();
              }
          break;
          case "updatenames":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $id_product = (int)$row['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,'',$row['id_shop']);
              if (!$product->id) break;
              $product->name[$row['id_lang']] = $row['name'];
              if ($row['link_rewrite'] == '-' || $row['link_rewrite'] == '' || $row['link_rewrite'] == ' ')  $product->link_rewrite[$row['id_lang']] = str_replace(" ",'-',mb_substr(Tools::link_rewrite($row['name']),0,128));
              else $product->link_rewrite[$row['id_lang']] = $row['link_rewrite'];
              $product->update();
              }
          break;
          case "updatenamesa":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,'',$row['id_shop']);
              if (!$product->id) break;
              $product->name[$row['id_lang']] = $row['name'];
              if ($row['link_rewrite'] == '-' || $row['link_rewrite'] == '' || $row['link_rewrite'] == ' ')  $product->link_rewrite[$row['id_lang']] = str_replace(" ",'-',mb_substr(Tools::link_rewrite($row['name']),0,128));
              else $product->link_rewrite[$row['id_lang']] = $row['link_rewrite'];
              $product->update();
              }
          break;
          case "updatereference":
             if ($row['tovar'] == 1)
             {
             $id_product = (int)$row['id_product'];
             $id_attribute = 0;
             }
             else 
             { 
             $id_attribute = (int)$row['tovar'];
             $id_product_a = (int)$row['id_product'];
             $id_product = 0;
             }
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              $product->reference = $row['reference'];
              $product->update();
              }
              if ($id_attribute) {  
                $product = new Product($id_product_a,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                $attributes = $product->getAttributesResume ($row['id_lang']);
                foreach ($attributes as $attribute)
                          {
                          if ($attribute['id_product_attribute'] == $id_attribute) 
                            {
                            $attributes_array['id_product'] = $attribute['id_product'];
                            $attributes_array['name'] = $attribute['attribute_designation'];
                            $attributes_array['reference'] = $attribute['reference'];
                            $attributes_array['ean13'] = $attribute['ean13'];
                            $attributes_array['wholesale_price'] = $attribute['wholesale_price'];
                            $attributes_array['id_category_default'] = 0;
                            $attributes_array['default_on'] = $attribute['default_on'];
                            $attributes_array['unit_price_impact'] = $attribute['unit_price_impact'];
                            $attributes_array['ecotax'] = $attribute['ecotax'];
                            $attributes_array['available_date'] = $attribute['available_date'];
                            $attributes_array['location'] = $attribute['location'];
                            $attributes_array['upc'] = $attribute['upc'];
                            $attributes_array['minimal_quantity'] = $attribute['minimal_quantity'];
                            $attributes_array['price'] = $attribute['price'];
                            $attributes_array['quantity'] = $attribute['quantity'];
                            $attributes_array['weight'] = $attribute['weight'];
                            $attributes_array['tovar'] = $attribute['id_product_attribute'];
                            }
                          }
              $product -> updateAttribute ($id_attribute, $attributes_array['wholesale_price'], $attributes_array['price'],$attributes_array['weight'],$attributes_array['unit_price_impact'],$attributes_array['ecotax'],'',$row['reference'],$attributes_array['ean13'],$attributes_array['default_on'],$attributes_array['location'],$attributes_array['upc'],$attributes_array['minimal_quantity'],$attributes_array['available_date'],false);
                $attributes_array = 0;
              }
          break;
          case "updateseo":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $id_product = (int)$row['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              if ($row['meta_title'] != '-') $product->meta_title = $row['meta_title'];
              if ($row['meta_keywords'] != '-') $product->meta_keywords = $row['meta_keywords'];
              if ($row['meta_description'] != '-') $product->meta_description = $row['meta_description'];
              if ($row['link_rewrite'] != '-') $product->link_rewrite = $row['link_rewrite'];
              $product->update();
              }
          break;
          case "updateseoa":
             //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
             $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
             $idnumber = Db::getInstance()->getRow($sql_get_id);
             $id_product = (int)$idnumber['id_product'];
             if ($id_product)
              {
              $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
              if (!$product->id) break;
              if ($row['meta_title'] != '-') $product->meta_title = $row['meta_title'];
              if ($row['meta_keywords'] != '-') $product->meta_keywords = $row['meta_keywords'];
              if ($row['meta_description'] != '-') $product->meta_description = $row['meta_description'];
              if ($row['link_rewrite'] != '-') $product->link_rewrite = $row['link_rewrite'];
              $product->update();
              }
          break;
          case "updatesupplier":
              //Проверка товар-комбинация 
             if ($row['tovar'] == 1) 
             {
              $supplier_id = Supplier::getIdByName ($row['supplier']);
                  if (!$supplier_id)
                    {
                    $supplier = new Supplier();
                    $supplier->name = $row['supplier'];
                    $supplier->active = true;
                    $supplier->add();
                    $supplier_id = $supplier->id;
                    $supplier = 0;
                    unset ($supplier);
                    }
              $id_product = (int)$row['id_product'];
              if ($id_product && $supplier_id && $supplier_id != '-')
                {
                $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                $product->deleteFromSupplier();
                $product->id_supplier = $supplier_id;
                if ($row['supplier_reference'] != '-') $product->supplier_reference = $row['supplier_reference'];
                $product->supplier_name = $row['supplier'];
                $product->update();
                if ($row['supplier_reference'] != '-') $product->addSupplierReference($supplier_id, 0, $row['supplier_reference']);
                else $product->addSupplierReference($supplier_id, 0);
                }
             }
             else
             {
              $id_attribute = (int)$row['tovar'];
              $supplier_id = Supplier::getIdByName ($row['supplier']);
                  if (!$supplier_id)
                    {
                    $supplier = new Supplier();
                    $supplier->name = $row['supplier'];
                    $supplier->active = true;
                    $supplier->add();
                    $supplier_id = $supplier->id;
                    $supplier = 0;
                    unset ($supplier);
                    }
              $id_product = (int)$row['id_product'];
              if ($id_product && $id_attribute && $supplier_id && $supplier_id != '-')
                {
                $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                if ($row['supplier_reference'] != '-') $product->addSupplierReference($supplier_id, $id_attribute, $row['supplier_reference']);
                else $product->addSupplierReference($supplier_id, $id_attribute);
                }   
            }
          break;
          case "updatemanufacturer":
              //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
              $manufacturer_id = Manufacturer::getIdByName ($row['manufacturer']);
                  if (!$manufacturer_id)
                    {
                    $manufacturer = new Manufacturer();
                    $manufacturer->name = $row['manufacturer'];
                    $manufacturer->active = true;
                    $manufacturer->add();
                    $manufacturer_id = $manufacturer->id;
                    $manufacturer = 0;
                    unset ($manufacturer);
                    }
              $id_product = (int)$row['id_product'];
              if ($id_product && $manufacturer_id && $manufacturer_id != '-')
                {
                $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                if (!$product->id) break;
                $product->id_manufacturer = $manufacturer_id;
                $product->manufacturer_name = $row['manufacturer'];
                $product->update();
                }
          break;
          case "updatefeatures":
              //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
              $features = $row['features'];
                  $id_product = (int)$row['id_product'];
                  if ($delete_features == 1) {
                      $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                      if (!$product->id) break;
                      $product->deleteProductFeatures();
                     }
            				if (isset($features) && !empty($features) && $features != '-') {
            					foreach (explode(",", $features) as $single_feature)
            					{
            						if (empty($single_feature))
            							continue;
            						$tab_feature = explode(':', $single_feature);
            						$feature_name = isset($tab_feature[0]) ? trim($tab_feature[0]) : '';
            						$feature_value = isset($tab_feature[1]) ? trim($tab_feature[1]) : '';
            						$position = isset($tab_feature[2]) ? (int)$tab_feature[2] - 1 : false;
            						$custom = isset($tab_feature[3]) ? (int)$tab_feature[3] : false;
            						if(!empty($feature_name) && !empty($feature_value))
            						{
            							$id_feature = (int)Feature::addFeatureImport($feature_name, $position);
            							$id_feature_value = (int)FeatureValue::addFeatureValueImport($id_feature, $feature_value, $id_product, $row['id_lang'], $custom);
            							Product::addFeatureProductImport($id_product, $id_feature, $id_feature_value);
            						}
            					}
            		// clean feature positions to avoid conflict
            		Feature::cleanPositions(); }
          break;
          case "updatefeaturesa":
              //Проверка товар-комбинация 
             if ($row['tovar'] != 1) break;
             
              $features = $row['features'];
                  $sql_get_id = "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '".$row['reference']."'";
                  $idnumber = Db::getInstance()->getRow($sql_get_id);
                  $id_product = (int)$idnumber['id_product'];
                  if ($delete_features == 1) {
                      $product = new Product($id_product,false,$row['id_lang'],$row['id_shop']);
                      if (!$product->id) break;
                      $product->deleteProductFeatures();
                     }
            				if (isset($features) && !empty($features) && $features != '-')  {
            					foreach (explode(",", $features) as $single_feature)
            					{
            						if (empty($single_feature))
            							continue;
            						$tab_feature = explode(':', $single_feature);
            						$feature_name = isset($tab_feature[0]) ? trim($tab_feature[0]) : '';
            						$feature_value = isset($tab_feature[1]) ? trim($tab_feature[1]) : '';
            						$position = isset($tab_feature[2]) ? (int)$tab_feature[2] - 1 : false;
            						$custom = isset($tab_feature[3]) ? (int)$tab_feature[3] : false;
            						if(!empty($feature_name) && !empty($feature_value))
            						{
            							$id_feature = (int)Feature::addFeatureImport($feature_name, $position);
            							$id_feature_value = (int)FeatureValue::addFeatureValueImport($id_feature, $feature_value, $id_product, $row['id_lang'], $custom);
            							Product::addFeatureProductImport($id_product, $id_feature, $id_feature_value);
            						}
            					}
            		// clean feature positions to avoid conflict
            		Feature::cleanPositions();  }
          break;
          }

  $offset++;
  $sql_row_updated = 'UPDATE `'._DB_PREFIX_.'exceltable` SET `offset` = '.$offset.' WHERE `id_excel` = '. ($offset-1); 
  $row = Db::getInstance()->execute($sql_row_updated);
  
  if ($offset > $kolline) 
    {
      if ($import_option == 'updatemass' || $import_option == 'updatemassa' || $import_option == 'updatepraq' || $import_option == 'updatepra' || $import_option == 'updateprice' || $import_option == 'updatepriceq')  
      if ($off_products == 1) {
        $sql_get_time = 'SELECT `date_upd` FROM `'._DB_PREFIX_.'exceltable` WHERE `id_excel` = 1';
        $sql_time = Db::getInstance()->getRow($sql_get_time);
        $sql_prod_off = 'UPDATE `'._DB_PREFIX_.'product` SET `active` = 0 WHERE `date_upd` < '."'".$sql_time['date_upd']."'";
        $sql_prod_off_shop = 'UPDATE `'._DB_PREFIX_.'product_shop` SET `active` = 0 WHERE `date_upd` < '."'".$sql_time['date_upd']."'";
        Db::getInstance()->execute($sql_prod_off);
        Db::getInstance()->execute($sql_prod_off_shop);
      }
     
     if ($art_unical) {
        $sql_gut = 'DELETE FROM `'._DB_PREFIX_.'exceltable` WHERE `checkin` = 1';
        Db::getInstance()->execute($sql_gut);
        $sql_gut_o = 'UPDATE `'._DB_PREFIX_.'exceltable` SET `offset` = 0';
        Db::getInstance()->execute($sql_gut_o);
        $sucsess = 1;
        
                $qclsale = 'SELECT reference FROM '._DB_PREFIX_.'exceltable';
                $qclsaler = Db::getInstance()->executeS($qclsale);
                $references = array();
                $reference = '';
                if ($qclsaler)
                  {
                  foreach ($qclsaler as $rown)
                    {
                    $references[]=$rown['reference'];
                    }
                  $reference = implode(",", $references);
                  $message = 'Not unical references: ' . $reference;
            
                  $sql_gut = 'TRUNCATE TABLE `'._DB_PREFIX_.'exceltable`';
                  Db::getInstance()->execute($sql_gut);
                  }
                else
                  {
                   $message = '';
                  }
        
        
      }   
     else {
        $sql_gut = 'TRUNCATE TABLE `'._DB_PREFIX_.'exceltable`';
        Db::getInstance()->execute($sql_gut);
        $sucsess = 1;
        $message = '';
      }
    
    }
  else
    {
      $sucsess = $offset/($kolline+1);
      $message = '';
    }    
  // И возвращаем клиенту данные (номер итерации и сообщение об окончании работы скрипта)
  $output = Array('offset' => $offset, 'sucsess' => $sucsess, 'message' => $message);

echo json_encode($output);
