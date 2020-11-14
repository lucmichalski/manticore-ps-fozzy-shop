<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class Nove_CustomPlugins extends Module
{
  public function __construct()
  {
    $this->name = 'nove_customplugins'; 
    $this->tab = 'administration';
    $this->version = '1.0.2';
    $this->author = 'Novevision.com, Britoff A.';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    $this->bootstrap = true;
  
    parent::__construct();
  
    $this->displayName = $this->l('Custom CSS and JS');
    $this->description = $this->l('Add custom CSS and JS (all files from folders CSS and JS)');
  
  }
  
  public function install()
  {
    if (Shop::isFeatureActive())
      Shop::setContext(Shop::CONTEXT_ALL);
   
    if (!parent::install() ||
      !$this->registerHook('displayHeader') 
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
  
  public function hookdisplayHeader($params)
  {
   
     // $mobile = $this->context->isMobile();
      $id_shop = $this->context->shop->id;
      $id_lang = $this->context->language->id;
      
      $this->smarty->assign(array(
      //  'mobile_m' => $mobile,
        'n_id_shop' => $id_shop,
        'n_id_lang' => $id_lang
      ));
      
      $fileurl = '/views/js';
      $dh = scandir(dirname(__FILE__).$fileurl);
      foreach ($dh as $file)
        { 
          if($file !='.' && $file !='..' && substr($file, -3) == '.js')
          {
            $this->context->controller->addJS($this->_path.'views/js/'.$file);
          }
        }
      
      $fileurl = '/views/css';
      $dh = scandir(dirname(__FILE__).$fileurl);
      foreach ($dh as $file)
        { 
          if($file !='.' && $file !='..' && substr($file, -4) == '.css')
          {
            $this->context->controller->addCSS($this->_path.'views/css/'.$file, 'all');
          }
        }
      return $this->display(__FILE__, 'header.tpl');
  }
     
}