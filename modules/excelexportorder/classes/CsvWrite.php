<?php
  class CsvWrite {
 
 private $id_lang;
 private $feeddir;     
 private $rates;
 private $mode;
 private $imgdir;
 
 public function __construct( $feeddir, $id_lang, $rates=null, $imgdir=null) {
     $this->feeddir=$feeddir;
     
     $this->id_lang=$id_lang;
     if(!is_null($rates)) {
     if(is_array($rates)) {
         foreach($rates as $r) {
           $this->rates[$r['rate']]= $r['id_tax'];
         }
     }
     else {
        $this->rates=(int) $rates;    
     }
     }
     $this->imgdir=$imgdir;
    // $this->stamp=date('ymdHi');
 }
 
public function createProducts($products,$from, $description=0) 
  {
    $this->mode='product';
    $path=$this->_getPath($from);
     $fp=fopen($path, "w+");
    if(!$fp) {
    die ("failed to open ".$path);  
    }
 
  $keys=$this->_getKeys();
  if($description && $from == 1)    
    $this->_collumnDescription($fp);
    
    foreach ($products AS $product)
    { 
    if ((int)$product['id_category_default'] != 0)
    {
    $categ = '';
    $categ_def = '';
    $categ_def[]=(int)$product['id_category_default'];
    $categ = array_merge($categ_def,array_values(array_diff(Product::getProductCategories((int)$product['id_product']),$categ_def)));
    $product ['categories'] = implode(",",$categ);
    }
    else
    {
    $product ['categories'] = '';
    }
    $product ['features'] = implode(",",$this->getFeaturesProduct((int)$product['id_product']));
    $product ['price'] = number_format($product ['price'], 2, ',', '');
    $product ['wholesale_price'] = number_format($product ['wholesale_price'], 2, ',', '');		
    $product ['width'] = number_format($product ['width'], 2, ',', '');
    $product ['height'] = number_format($product ['height'], 2, ',', '');
    $product ['depth'] = number_format($product ['depth'], 2, ',', '');
    $product ['weight'] = number_format($product ['weight'], 2, ',', '');
    $product ['deletephotos'] = 0;
    $product ['description_short'] = str_replace(";", ",",  strip_tags($product['description_short']));
    $product ['description'] = str_replace(";", ",",  strip_tags($product['description']));

    $this->_writeData( $product, $fp);
  
    }

  fclose($fp);   
  }
     
private function getFeaturesProduct($id_product)  
{
  global $cookie;
  $features=Product::getFeaturesStatic($id_product);
  
  foreach ($features as $feature)
          {
          $a = Feature::getFeature((int)$cookie->id_lang, $feature['id_feature']);
          $b = FeatureValue::getFeatureValueLang($feature['id_feature_value']);
          foreach ($b as $l)
            {
            if ($l['id_lang']==(int)$cookie->id_lang) $c=$l['value'];
            }
          $prod_features[]=$a['name'].":".$c;
          }
  unset ($features);        
  return $prod_features;
  unset ($prod_features);
}	
      
 public function createItems($items, $mode, $description) {  
 
$this->mode=$mode;
$path =$this->_getPath();

$fp=fopen($path, "w+");
if(!$fp) {
    die ("failed to open ".$path);  
}
if($description)    
    $this->_collumnDescription($fp);  
    $row=array();
   
   foreach($items as $item) { 
       $this->_writeData( $item, $fp); 
   }  
   
   fclose($fp);  
  }
  
  
  private function   _writeData($array,  $fp) {
     $s=''; 
     $keys=$this->_getKeys();    
      
     $row=array();
       for($i=0; $i<count($keys); $i++) {
        
       if(isset($array[$keys[$i]]) ) {
            $row[$keys[$i]]=$array[$keys[$i]];
           
       }
       else {
          $row[$keys[$i]]=""; 
       }
    }  
    
       foreach($row as $item) {
       if(is_numeric($item)) {
          $s.='"'.$item.'";'; 
       }
       elseif(empty($item)) {
          $s.='"";'; 
       }
       else {
          $item=str_replace(array("\r\n", "\r", "\n", "\t"), ' ',  $item); 
          $item=str_replace('"', '""',   $item);  
          $s.='"'.$item.'";'; 
       }
    
    }
    
    $s=substr($s,0,strlen($s)-1). chr(10);
   
     
         fputs($fp, iconv("utf-8", "windows-1251", $s));    
  
  }
  
  
  private function _getKeys() {
      switch($this->mode) {
         case 'category':
           return   array('id_category', 'active', 'name', 'id_parent',  'is_root_category', 'description', 'meta_title', 'meta_keywords', 'meta_description', 'link_rewrite');         
           case 'manufacturer':
           return   array('id_manufacturer', 'active', 'name', 'description',  'short_description', 'meta_title', 'meta_keywords', 'meta_description');  
             case 'supplier':
           return   array('id_supplier', 'active', 'name', 'description',  'meta_description', 'meta_title', 'meta_keywords');    
            case 'customer':
           return   array('id_customer', 'active', 'id_gender',  'email',  'passwd',  'birthday','lastname', 'firstname','newsletter','optin');    
         case 'address': // maji v ni bordel
           return   array('id_address', 'alias', 'active', 'email',  'id_manufacturer', 'id_supplier', 
           'company',  'lastname',  'firstname', 'address1', 'address2',  'postcode',  'city',  
           'id_country',    'id_state', 'other', 'phone', 'phone_mobile', 'vat_number');        
        case 'product':
           return   array('id_product','active','name','categories','price','wholesale_price','on_sale','reduction_amount','reduction_percent','reduction_from','reduction_to','reference','supplier_reference','supplier_name','manufacturer_name','ean13','width','height','depth','weight','quantity','description_short','description','imageurls','deletephotos','features','is_virtual','available_now', 'meta_title', 'meta_keywords', 'meta_description', 'link_rewrite','tovar');
 
      }
      
  }
  
  
  private function _collumnDescription($fp) {

     $arr['id_product'] = 'ID';
     $arr['active'] = 'Статус (0 - неактивен/1 - активен)';
     $arr['name'] = 'Название';
     $arr['categories'] = 'Категории (указываются ID через запятую)';
     $arr['price'] = 'Цена';
     $arr['wholesale_price'] = 'Закупочная цена';
     $arr['on_sale'] = 'Распродажа (0/1)';
     $arr['reduction_amount'] = 'Скидка (сумма)';
     $arr['reduction_percent'] = 'Скидка (процент)';
     $arr['reduction_from'] = 'Скидка с (yyyy-mm-dd)';
     $arr['reduction_to'] = 'Скидка по (yyyy-mm-dd)';
     $arr['reference'] = 'Артикул';
     $arr['supplier_reference'] = 'Артикул поставщика';
     $arr['supplier_name'] = 'Поставщик';
     $arr['manufacturer_name'] = 'Производитель';
     $arr['ean13'] = 'EAN13 - Штрих код';
     $arr['width'] = 'Ширина упаковки';
     $arr['height'] = 'Высота упаковки';
     $arr['depth'] = 'Глубина упаковки';
     $arr['weight'] = 'Вес';
     $arr['quantity'] = 'Количество';
     $arr['description_short'] = 'Короткое описание';
     $arr['description'] = 'Подробное описание';
     $arr['imageurls'] = 'Ссылки на изображения (x,y,z...)';
     $arr['deletephotos'] = 'Удалить старые фото (0=Нет, 1=Да)';
     $arr['features'] = 'Характеристики (Имя:Значение:Позиция)';
     $arr['is_virtual'] = 'Доступен только онлайн (0 = Нет, 1 = Да)';
     $arr['available_now'] = 'Текст когда товар на складе';
     $arr['meta_title'] = 'Мета-заголовок';
     $arr['meta_keywords'] = 'Мета-ключевые слова';
     $arr['meta_description'] = 'Мета-описание';
     $arr['link_rewrite'] = 'ЧПУ';
     $arr['tovar'] = 'Признак Товар(1)/Комбинация(ID комбинации)';
     $this->_writeData($arr, $fp); 
  }
  
  private function _getPath($from=null) {
       $path =$this->mode.'.csv';
      
       if($from && is_numeric($from))
           $path =sprintf('%03d',$from).'_'.$path;

       $path =$this->feeddir."/".$path;     
           
            if(file_exists($path))
            unlink($path);
            return $path;
  }
  }
?>
