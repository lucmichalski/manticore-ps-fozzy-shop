<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class PriceExcel extends Module
{
  public function __construct()
  {
    $this->name = 'priceexcel'; 
    $this->tab = 'other';
    $this->version = '1.0';
    $this->author = 'Novevision.com, Britoff A.';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.8');
    $this->bootstrap = true;
  
    parent::__construct();
  
    $this->displayName = $this->l('Excel Price');
    $this->description = $this->l('Get excel price');
  }
  public function install()
  {
    if (Shop::isFeatureActive())
      Shop::setContext(Shop::CONTEXT_ALL);
  
    if (!parent::install() ||
      !$this->registerHook('DisplayFooter')
    )
      return false;
   
    return true;
  }
  
  public function uninstall()
  {
    
    if (!parent::uninstall()
    )
      return false;
   
    return true;
  }
  

  public static function CreatePrice () {
  
  $sql = "SELECT p.`id_product`, p.`reference`, l.`name`, p.`price`, l.`link_rewrite`  FROM `"._DB_PREFIX_."product` p, `"._DB_PREFIX_."product_lang` l  WHERE p.`active` > 0 AND (p.`available_for_order` > 0) AND (p.`id_product` = l.`id_product`) AND (p.`id_category_default` IN (300506, 300507, 300508, 300509, 300510, 300511, 300512, 300513, 300514, 300515, 300516, 300345, 300346, 300347, 300348, 300349, 300350, 300351, 300352, 300353, 300354, 300355, 300335, 300336, 300337, 300338, 300526, 300333, 300334, 300356, 300357, 300358, 300359, 300360, 300361, 300362, 300421, 300422, 300423, 300424, 300425, 300426, 300427, 300428, 300429, 300430, 300431, 300432, 300433, 300434, 300435, 300436, 300437, 300438, 300439, 300440, 300441, 300442, 300443, 300444, 300445, 300446, 300447, 300448, 300449, 300450, 300451, 300452, 300453, 300454, 300455, 300456, 300457, 300458, 300459, 300460, 300461, 300462, 300463, 300464, 300465, 300466, 300467, 300468, 300469, 300470, 300471, 300472, 300473, 300474, 300475, 300476, 300477, 300478, 300479, 300480, 300481, 300482, 300483, 300484, 300485, 300486, 300487, 300488, 300609))";   
  $result = Db::getInstance()->executeS($sql);
  
    
 //   require_once dirname(__FILE__) . '/classes/PHPExcel.php';
    $objPHPExcel = new PHPExcel();

      // Set document properties
      $objPHPExcel->getProperties()->setCreator("Fozzy Foods")
      							 ->setLastModifiedBy("Fozzy Foods")
      							 ->setTitle("Fozzy price")
      							 ->setSubject("Fozzy price")
      							 ->setDescription("Fozzy price")
      							 ->setKeywords("Fozzy price")
      							 ->setCategory("Fozzy price");
  //setCellValueExplicit('A1', 'Код',PHPExcel_Cell_DataType::TYPE_STRING)    
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', 'ID')
                  ->setCellValue('B1', 'Item title')
                  ->setCellValue('C1', 'Item description')
                  ->setCellValue('D1', 'Price')
                  ->setCellValue('E1', 'Final URL')
                  ->setCellValue('F1', 'Image URL');
                  
      foreach ($result as $key=>$row) {
      $i1 = $key + 2;
      $product = new Product($row['id_product'],1,2);
      $cover = Image::getCover($row['id_product']);
      $link = new Link();
      $cover_img = 'http://'.$link->getImageLink('small_default', $cover['id_image']);
      // Add some data

      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue("A$i1", $row['reference'])
                  ->setCellValue("B$i1", $row['name'])
                  ->setCellValue("C$i1", '')
                  ->setCellValue("D$i1", $row['price']." UAH")
                  ->setCellValue("E$i1", $product->getLink())
                  ->setCellValue("F$i1", $cover_img);
      }

      
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Feed Fozzyshop');
      
      
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      
      
      // Save Excel 2007 file
      
      //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
      
      // Redirect output to a client’s web browser (Excel2007)
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="fozzy_feed.xlsx"');
   /*   header('Content-Type: text');
      header('Content-Disposition: attachment;filename="fozzy_feed.csv"'); */
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');
      
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
      $objWriter->save('php://output');
  }

  public static function ExportOrder ($id_order) {

  $order = new Order ($id_order);
  $products = $order->getProducts();
  $skidka_summ = $order->total_products - $order->total_paid_tax_incl;
  $skidka_percent = 1 - ($order->total_products - $order->total_paid_tax_incl)/$order->total_products;

//  d($skidka_percent);
 //  require_once dirname(__FILE__) . '/classes/PHPExcel.php';
      
    $objPHPExcel = new PHPExcel();

      // Set document properties
      $objPHPExcel->getProperties()->setCreator("Fozzy Foods")
      							 ->setLastModifiedBy("Fozzy Foods")
      							 ->setTitle("Fozzy order")
      							 ->setSubject("Fozzy order")
      							 ->setDescription("Fozzy order")
      							 ->setKeywords("Fozzy order")
      							 ->setCategory("Fozzy order");

      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A1', 'Артикул')
                  ->setCellValue('B1', 'Наименование')
                  ->setCellValue('C1', 'Кол-во')
                  ->setCellValue('D1', 'Цена')
                  ->setCellValue('E1', 'Остаток');
      $i1=1;
    foreach ($products as $row) {
      $i1++;
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue("A$i1", $row['product_reference'])
                  ->setCellValue("B$i1", $row['product_name'])
                  ->setCellValue("C$i1", $row['product_quantity'])
                  ->setCellValue("D$i1", $row['unit_price_tax_incl']*$skidka_percent)
                  ->setCellValue("E$i1", $row['current_stock']);
      }
      
      $objPHPExcel->getActiveSheet()->setTitle('Order');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="order_'.$id_order.'.xlsx"');
      header('Cache-Control: max-age=0');
     $objWriter->save('php://output');

  }
  
 /* public function hookDisplayFooter($params)
	{
    return $this->display(__FILE__, 'priceexcel.tpl');
	}
 */ 
 
 


}