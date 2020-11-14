<?php
/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

class Ets_ss_class_cache
{
    public $user_agent = '';
	public	function __construct()
	{
        $this->context = Context::getContext();
        if(Configuration::get('ETS_SPEED_CHECK_USER_AGENT'))
        {
            $this->user_agent .= str_replace(array('/','(',')','.','\\','_',';'),'_',$_SERVER['HTTP_USER_AGENT']);
        }
	}
    public function getFileCacheByUrl()
    {
        if(!isset($_SERVER['SERVER_PORT']))
            return '';
        if($_SERVER['SERVER_PORT']!="80")
        {
            $url =$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
        }
        else
            $url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            $url ='https://'.$url;
        }
        else
            $url ='http://'.$url;
        if (strpos($url, '#') !== FALSE) {
            $url = Tools::substr($url, 0, strpos($url, '#'));
        }
        $this->context = Context::getContext();
        $query_string = parse_url( $url, PHP_URL_QUERY );
        $params = '&ets_currency='.($this->context->cookie->id_currency ? $this->context->cookie->id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
        $id_customer = (isset($this->context->customer->id)) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        } 
        $params .= '&ets_group='.(int)$id_group; 
        $id_country =isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int) Country::getByIso(Tools::strtoupper($this->context->cookie->iso_code_country)) : (int) Tools::getCountry();
        $params .='&ets_country='.($id_country ? $id_country : (int)$this->context->country->id);
        if(isset($this->context->cookie->id_cart) && $this->context->cookie->id_cart)
            $params .='&hascart=1';
        $params .='&user_agent='.$this->user_agent;
        if ($query_string == '') {
            $query_string .= Tools::substr($params, 1);
        }
        else {
            $query_string .= $params;
        }
        $uri = http_build_url($url, array("query" => $query_string));
        return md5(_COOKIE_KEY_.$uri);
    }
    public function getCache($check_connect=false)
    {
        $file_name=$this->getFileCacheByUrl();
        if(Configuration::get('ETS_SPEED_COMPRESS_CACHE_FIIE') && class_exists('ZipArchive'))
            $file_cache = _ETS_SPEED_CACHE_DIR_.(int)$this->context->shop->id.'/'.$file_name.'.zip';
        else
            $file_cache = _ETS_SPEED_CACHE_DIR_.(int)$this->context->shop->id.'/'.$file_name.'.html';
        if(Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'ets_superspeed_cache_page"'))
        {
            $page= Db::getInstance()->getValue('SELECT page FROM '._DB_PREFIX_.'ets_superspeed_cache_page WHERE file_cache="'.pSQL($file_cache).'" AND id_shop="'.(int)Context::getContext()->shop->id.'"');
            if(file_exists($file_cache) && $page)
            {
                if(Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_superspeed_cache_page WHERE id_shop="'.(int)Context::getContext()->shop->id.'" AND  file_cache ="'.pSQL($file_cache).'"'.(Configuration::get('ETS_SPEED_TIME_CACHE_'.Tools::strtoupper($page)) !=31 ? ' AND date_upd > "'.date('Y-m-d', strtotime('-'.(Configuration::get('ETS_SPEED_TIME_CACHE_'.Tools::strtoupper($page)) ? (int)Configuration::get('ETS_SPEED_TIME_CACHE_'.Tools::strtoupper($page)) : 1).' DAY')).'"':'')))
                {
                    if(!Tools::isSubmit('check_speed') && $check_connect)
                    {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_superspeed_cache_page SET click = click+1 WHERE file_cache="'.pSQL($file_cache).'" AND id_shop="'.(int)Context::getContext()->shop->id.'"');
                        
                       
                    }
                    if(Configuration::get('ETS_SPEED_COMPRESS_CACHE_FIIE') && class_exists('ZipArchive'))
                    {
                        $zip = new ZipArchive();
                        if($zip->open($file_cache))
                        {
                            
                            return $zip->getFromName($file_name);
                        }
                    }
                    return Tools::file_get_contents($file_cache);
                }
            }
        }
        return false;
    }
    public function setCache($value)
    {
        if($_SERVER['REQUEST_METHOD']=='POST' || !Configuration::get('ETS_SPEED_ENABLE_PAGE_CACHE'))
            return '';
        $controller = Tools::getValue('controller');
        $id_object = (int)Tools::getValue('id_'.$controller);
        if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog') && Tools::getValue('fc')=='moudle' && Tools::getValue('module')=='ybc_blog' && in_array($controller,array('blog','category','gallery','author')))
        {
            if($controller=='blog')
            {
                $id_post = (int)Tools::getValue('id_post');
                if(!$id_post && Tools::getValue('post_url_alias'))
                {
                    $id_post = (int)Db::getInstance()->getValue('SELECT ps.id_post FROM '._DB_PREFIX_.'ybc_blog_post_lang pl ,'._DB_PREFIX_.'ybc_blog_post_shop ps  WHERE ps.id_shop="'.(int)$this->context->shop->id.'" AND ps.id_post=pl.id_post AND pl.url_alias ="'.pSQL(Tools::getValue('post_url_alias')).'"');
                }
                if($id_post)
                    $id_object=$id_post;
                else
                {
                    $id_category = (int)trim(Tools::getValue('id_category'));
                    if(!$id_category && Tools::getValue('category_url_alias'))
                    {
                        $id_category = (int)Db::getInstance()->getValue('SELECT cs.id_category FROM '._DB_PREFIX_.'ybc_blog_category_lang cl,'._DB_PREFIX_.'ybc_blog_category_shop cs WHERE cs.id_category=cl.id_category AND cs.id_shop="'.(int)$this->context->shop->id.'" AND cl.url_alias ="'.pSQL(Tools::getValue('category_url_alias')).'"');    
                    }
                    if($id_category)
                        $id_object=$id_category;
                    else($id_author = (int)Tools::getValue('id_author'));
                        $id_object=$id_author;
                }
                
            }
            $controller = 'blog';
        }
        if((int)Tools::getValue('check_speed')<=0 && Db::getInstance()->getValue('SELECT id_cache_page FROM '._DB_PREFIX_.'ets_superspeed_cache_page WHERE ip="'.pSQL(Tools::getRemoteAddr()).'" AND page="'.pSQL($controller).'" AND user_agent="'.pSQL($this->user_agent).'" AND date_upd > "'.pSQL(date('Y-m-d H:i:s', strtotime('-1 HOUR'))).'"'.((int)$id_object ? 'AND id_object="'.(int)$id_object.'"':'').' AND request_uri not like "%check_speed=%"'.($this->context->customer->id ? ' AND has_customer=1':' AND has_customer=0').($this->context->cart->id ? ' AND has_cart=1':' AND has_cart=0') ))
            return '';
        elseif(Tools::getValue('check_speed')>1)
            return '';
        if($pages_exception = Configuration::get('ETS_SPEED_PAGES_EXCEPTION'))
        {
            $pages_exception = explode("\n",$pages_exception);
            foreach($pages_exception as $page_exception)
            {
                if($page_exception && Tools::strpos($_SERVER['REQUEST_URI'],$page_exception)!==false)
                    return '';
            }
        }
        $id_shop= $this->context->shop->id;
        if(!is_dir(_ETS_SPEED_CACHE_DIR_))
            mkdir(_ETS_SPEED_CACHE_DIR_,0777,true);
        if(!is_dir(_ETS_SPEED_CACHE_DIR_.$id_shop))
            @mkdir(_ETS_SPEED_CACHE_DIR_.$id_shop,0777,true);
        if(Configuration::get('ETS_SPEED_COMPRESS_CACHE_FIIE') && class_exists('ZipArchive'))
        {
            $file_name=$this->getFileCacheByUrl();
            $cache_file = _ETS_SPEED_CACHE_DIR_.(int)$id_shop.'/'.$file_name.'.zip';
            $zip = new ZipArchive();
            
            if(!file_exists($cache_file))
            {
                if($zip->open($cache_file, ZipArchive::CREATE | ZipArchive::CHECKCONS)===true)
                {
                    $zip->addFromString($file_name, $value);
                }
            }
            else
            {
                if($zip->open($cache_file))
                {
                    $zip->addFromString($file_name, $value);
                }
            }
            $zip->close();
        }
        else
        {
            $cache_file = _ETS_SPEED_CACHE_DIR_.(int)$id_shop.'/'.$this->getFileCacheByUrl().'.html';
            file_put_contents($cache_file, $value);
        } 
           
        if($id_page_cache = Db::getInstance()->getValue('SELECT id_cache_page FROM '._DB_PREFIX_.'ets_superspeed_cache_page WHERE file_cache like "'.pSQL($cache_file).'" AND id_shop="'.(int)Context::getContext()->shop->id.'"'))
        {
            $page_cache = new Ets_superspeed_cache_page($id_page_cache);
            $page_cache->page = $controller;
            $page_cache->id_object=  (int)$id_object;
            $page_cache->date_upd = date('Y-m-d H:i:s');
            $page_cache->file_size = Tools::ps_round(@filesize($cache_file)/1024,2);
            $page_cache->update();
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_superspeed_cache_page_hook WHERE id_cache_page="'.(int)$id_page_cache.'"'); 
        }
        else
        {
            $page_cache= new Ets_superspeed_cache_page();
            $page_cache->page= $controller;
            $page_cache->id_object = (int)$id_object;
            $page_cache->ip= Tools::getRemoteAddr();
            $page_cache->id_product_attribute = (int)Tools::getValue('id_product_attribute');
            $page_cache->id_shop = Context::getContext()->shop->id;
            $page_cache->file_cache= $cache_file;
            $page_cache->has_customer = $this->context->customer->id ? 1 : 0;
            $page_cache->has_cart = $this->context->cart->id ? 1 : 0;
            $page_cache->request_uri = $_SERVER['REQUEST_URI'];
            $page_cache->file_size = Tools::ps_round(@filesize($cache_file)/1024,2);
            $page_cache->user_agent = $this->user_agent;
            $page_cache->date_add = date('Y-m-d H:i:s');
            $page_cache->date_upd=date('Y-m-d H:i:s');
            $page_cache->add();
            $id_page_cache = $page_cache->id;
        }
        if(Hook::$executed_hooks && $id_page_cache)
        {
            foreach(Hook::$executed_hooks as $hook_name)
            {
                if(!Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_superspeed_cache_page_hook WHERE id_cache_page="'.(int)$id_page_cache.'" AND hook_name="'.pSQL($hook_name).'"'))
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_superspeed_cache_page_hook set id_cache_page="'.(int)$id_page_cache.'", hook_name="'.pSQL($hook_name).'"');
            }
        }
    }
    public function deleteCache($page='',$id_object=0,$hook_name='')
    {
        $pages_cache= Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_superspeed_cache_page
        WHERE 1 '.($page ? ' AND page="'.pSQL($page).'"':'').($id_object ? ' AND id_object="'.(int)$id_object.'"':'').($hook_name ? ' AND id_cache_page IN (SELECT id_cache_page FROM '._DB_PREFIX_.'ets_superspeed_cache_page_hook WHERE hook_name="'.pSQL($hook_name).'")':''));
        if($pages_cache)
        {
            foreach($pages_cache as $page_cache)
            {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_superspeed_cache_page WHERE id_cache_page ='.(int)$page_cache['id_cache_page']);
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_superspeed_cache_page_hook WHERE id_cache_page="'.(int)$page_cache['id_cache_page'].'"');
                @unlink($page_cache['file_cache']);
            }
        }
        return true;
    }
}