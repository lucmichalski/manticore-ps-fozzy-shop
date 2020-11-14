<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockguestbook
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

class guestbook extends Module{

    private $_width = 85;
    private $_height = 85;
    private $_name;
    private $_http_host;
    private $_is_cloud;
	
	public function __construct(){
		$this->_name = "blockguestbook";

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        } else {
            $this->_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
        }


        if (defined('_PS_HOST_MODE_'))
            $this->_is_cloud = 1;
        else
            $this->_is_cloud = 0;


        // for test
        //$this->_is_cloud = 1;
        // for test

        if($this->_is_cloud){
            $this->path_img_cloud = DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
        } else {
            $this->path_img_cloud = DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR;

        }



        if (version_compare(_PS_VERSION_, '1.5', '<')){
            require_once(_PS_MODULE_DIR_.$this->_name.'/backward_compatibility/backward.php');
        }


		$this->initContext();
	}
	
	private function initContext()
	{
        $this->context = Context::getContext();
	}
	
	public function saveItem($_data){
		
		$cookie = $this->context->cookie;
		$id_lang = (int)$cookie->id_lang;

        $name = $_data['name'];
        $email = $_data['email'];
        $web = $_data['web'];
        $text_review =  $_data['text_review'];
        $company = $_data['company'];
        $address = $_data['address'];

        $country = $_data['country'];
        $city = $_data['city'];
		
		$ip = $_SERVER['REMOTE_ADDR'];

		$sql = 'INSERT into `'._DB_PREFIX_.'blockguestbook` SET
							   `name` = \''.pSQL($name).'\',
							   `email` = \''.pSQL($email).'\',
							   `ip` = \''.pSQL($ip).'\',
							   `message` = \''.pSQL($text_review).'\',
							   `web` = \''.pSQL($web).'\',
							   `company` = \''.pSQL($company).'\',
							   `address` = \''.pSQL($address).'\',
							   `country` = \''.pSQL($country).'\',
							   `city` = \''.pSQL($city).'\',
							    `id_shop` = \''.$this->getIdShop().'\',
							   `id_lang` = \''.$id_lang.'\',
							   `date_add` = NOW()
							   ';
		Db::getInstance()->Execute($sql);

        $id_item = Db::getInstance()->Insert_ID();

        $this->saveImage(array('id'=>$id_item));


        if(Configuration::get($this->_name.'notig') == 1){
		
		include_once(dirname(__FILE__).'/../blockguestbook.php');
		$obj_blockguestbook = new blockguestbook();
		$_data_translate = $obj_blockguestbook->translateItems();
		$subject = $_data_translate['subject'];

            $message = "<span style='color:#333'><strong>".$_data_translate['message'].': </strong></span>'.$text_review;



            if(Configuration::get($this->_name.'is_webg') == 1){
                $web = isset($_data['web']) ? $_data['web'] :'' ;
                if(Tools::strlen($web)>0)
                    $message .= "<br/><br/><b>".$_data_translate['web'].": </b>".$web;
            }

            if(Configuration::get($this->_name.'is_companyg') == 1){
                $company = isset($_data['company']) ? $_data['company'] :'' ;
                if(Tools::strlen($company)>0)
                    $message .= "<br/><br/><b>".$_data_translate['company'].": </b>".$company;
            }

            if(Configuration::get($this->_name.'is_addrg') == 1){
                $address = isset($_data['address']) ? $_data['address'] :'' ;
                if(Tools::strlen($address)>0)
                    $message .= "<br/><br/><b>".$_data_translate['address'].": </b>".$address;
            }

            if(Configuration::get($this->_name.'is_countryg') == 1){
                $country = isset($_data['country']) ? $_data['country'] :'' ;
                if(Tools::strlen($country)>0)
                    $message .= "<br/><br/><b>".$_data_translate['country'].": </b>".$country;
            }

            if(Configuration::get($this->_name.'is_cityg') == 1){
                $city = isset($_data['city']) ? $_data['city'] :'' ;
                if(Tools::strlen($city)>0)
                    $message .= "<br/><br/><b>".$_data_translate['city'].": </b>".$city;
            }

		/* Email generation */
		$templateVars = array(
			'{email}' => $email,
			'{name}' => $name,
			'{message}' => $message
		);


            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }
			
		/* Email sending */
		Mail::Send($id_lang_current, 'guestbook', $subject, $templateVars,
			Configuration::get($this->_name.'mailg'), 'Guestbook Form', $email, $name,
			NULL, NULL, dirname(__FILE__).'/../mails/');
		}
		
		
	}
	
	public function getIdShop(){
		$id_shop = 0;
		if(version_compare(_PS_VERSION_, '1.5', '>'))
			$id_shop = Context::getContext()->shop->id;
		return $id_shop;
	} 
	
	
	public function getItems($_data){
		
		$start = $_data['start'];
		$step = $_data['step'];;
		$admin = isset($_data['admin'])?$_data['admin']:null;

		
		if($admin){
			$reviews = Db::getInstance()->ExecuteS('
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blockguestbook` pc
			WHERE pc.`is_deleted` = 0
			ORDER BY pc.`date_add` DESC LIMIT '.$start.' ,'.$step.'');
			
			$data_count_reviews = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blockguestbook` 
			WHERE is_deleted = 0
			');
		}else{
            $sql_condition = $this->getConditionMultilanguageAndMultiStore(array('and'=>1));

			$reviews = Db::getInstance()->ExecuteS('
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blockguestbook` pc
			WHERE pc.active = 1 AND pc.`is_deleted` = 0
			'.$sql_condition.'
			ORDER BY pc.`date_add` DESC LIMIT '.$start.' ,'.$step.'');
			
			$i=0;
			foreach($reviews as $_item_tmp){
				$date_add = date("d-m-Y H:i:s",strtotime($_item_tmp['date_add']));
				$reviews[$i]['date_add'] = $date_add;
				
				$message = $_item_tmp['message'];
				$message = str_replace("\r\n","<br/>",$message);
				$message = str_replace("\n","<br/>",$message);
				
				$reviews[$i]['message'] = $message;
				$i++;
			}
			
			$data_count_reviews = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blockguestbook` 
			WHERE active = 1 AND is_deleted = 0 '.$sql_condition.'
			');
		}
		 return array('reviews' => $reviews, 'count_all_reviews' => $data_count_reviews['count'] );
	}
	
	public function publish($data){
		$id = $data['id'];
		$sql = 'UPDATE `'._DB_PREFIX_.'blockguestbook` 
	    						SET
						   		active = 1
						   		WHERE id = '.$id.' 
						   ';
		Db::getInstance()->Execute($sql);
	}
	
	public function unpublish($data){
		$id = $data['id'];
		$sql = 'UPDATE `'._DB_PREFIX_.'blockguestbook` 
	    						SET
						   		active = 0
						   		WHERE id = '.$id.' 
						   ';
		Db::getInstance()->Execute($sql);
	}
	
	public function delete($data){
		$id = $data['id'];
		$sql = 'UPDATE `'._DB_PREFIX_.'blockguestbook` 
	    						SET
						   		is_deleted = 1
						   		WHERE id = '.$id.''; 
		Db::getInstance()->Execute($sql);
	}

    public function setPublsh($data){
        $id = $data['id'];
        $active = $data['active'];

        $sql = 'UPDATE `'._DB_PREFIX_.'blockguestbook`
	    				SET
				   		active = '.(int)($active).'
				   		WHERE id = '.(int)($id).'
						   ';
        Db::getInstance()->Execute($sql);
    }
	
	public function updateItem($data){

        $name = $data['name'];
        $email = $data['email'];
        $web = $data['web'];
        $message = $data['message'];
        $publish = $data['publish'];
        $id = $data['id'];
        $company = $data['company'];
        $address = $data['address'];

        $country = $data['country'];
        $city = $data['city'];

        $response = $data['response'];
        $is_noti = $data['is_noti'];
        $is_show = $data['is_show'];

        $id_lang = $data['id_lang'];

        $date_add = date('Y-m-d H:i:s',strtotime($data['date_add']));


        $sql_condition_web = '';
        if(Configuration::get($this->_name.'is_webg') == 1){
            $sql_condition_web = '`web` = "'.pSQL($web).'",';
        }

        $sql_condition_company = '';
        if(Configuration::get($this->_name.'is_companyg') == 1){
            $sql_condition_company = '`company` = "'.pSQL($company).'",';
        }

        $sql_condition_address = '';
        if(Configuration::get($this->_name.'is_addrg') == 1){
            $sql_condition_address = '`address` = "'.pSQL($address).'",';
        }

        $sql_condition_country = '';
        if(Configuration::get($this->_name.'is_countryg') == 1){
            $sql_condition_country = '`country` = "'.pSQL($country).'",';
        }

        $sql_condition_city = '';
        if(Configuration::get($this->_name.'is_cityg') == 1){
            $sql_condition_city = '`city` = "'.pSQL($city).'",';
        }


		$sql = 'UPDATE `'._DB_PREFIX_.'blockguestbook` 
	    						SET `name` = "'.pSQL($name).'",
						   			`email` = "'.pSQL($email).'",
						   			'.$sql_condition_web.'
						   			`message` = "'.pSQL($message).'",
						   			`date_add` = "'.pSQL($date_add).'",
						   			`response` = "'.pSQL($response).'",
						   			`is_show` = "'.pSQL($is_show).'",
						   			`id_lang` = '.(int)($id_lang).',
						   			'.$sql_condition_company.'
									'.$sql_condition_address.'
									'.$sql_condition_country.'
									'.$sql_condition_city.'
									`active` = '.(int)$publish.'
						   		WHERE id = '.(int)$id.'';
		Db::getInstance()->Execute($sql);

        if($is_noti){
            // send email
            $this->sendNotificationResponseGuestbook(array('id'=>$id));
        }
	}


    public function sendNotificationResponseGuestbook($data = null){


        include_once(dirname(__FILE__).'/../blockguestbook.php');
        $obj_blockguestbook = new blockguestbook();
        $_data_translate = $obj_blockguestbook->translateItems();
        $subject_response = $_data_translate['subject_response'];



        $id = $data['id'];

        $_data_item_tmp = $this->getItem(array('id'=>$id));
        $_data = $_data_item_tmp['reviews'][0];


        $cookie = $this->context->cookie;

        $id_lang = (int)($cookie->id_lang);
        $id_lang = isset($_data['id_lang']) ? $_data['id_lang'] :$id_lang ;

        $name = isset($_data['name']) ? $_data['name'] :'' ;
        $email = isset($_data['email']) ? $_data['email'] :@Configuration::get('PS_SHOP_EMAIL') ;

        $data_url = $this->getSEOURLs();
        $items_url = $data_url['guestbook_url'];

        $response = isset($_data['response']) ? $_data['response'] :'' ;

        $message = isset($_data['message'])?"<span style='color:#333'><strong>".$_data_translate['message'].': </strong></span>'.$_data['message']:"";


        if(Configuration::get($this->_name.'is_webg') == 1){
            $web = isset($_data['web']) ? $_data['web'] :'' ;
            if(Tools::strlen($web)>0)
                $message .= "<br/><br/><b>".$_data_translate['web'].": </b>".$web;
        }

        if(Configuration::get($this->_name.'is_companyg') == 1){
            $company = isset($_data['company']) ? $_data['company'] :'' ;
            if(Tools::strlen($company)>0)
                $message .= "<br/><br/><b>".$_data_translate['company'].": </b>".$company;
        }

        if(Configuration::get($this->_name.'is_addrg') == 1){
            $address = isset($_data['address']) ? $_data['address'] :'' ;
            if(Tools::strlen($address)>0)
                $message .= "<br/><br/><b>".$_data_translate['address'].": </b>".$address;

        }

        if(Configuration::get($this->_name.'is_countryg') == 1){
            $country = isset($_data['country']) ? $_data['country'] :'' ;
            if(Tools::strlen($country)>0)
                $message .= "<br/><br/><b>".$_data_translate['country'].": </b>".$country;

        }

        if(Configuration::get($this->_name.'is_cityg') == 1){
            $city = isset($_data['city']) ? $_data['city'] :'' ;
            if(Tools::strlen($city)>0)
                $message .= "<br/><br/><b>".$_data_translate['city'].": </b>".$city;

        }


        /* Email generation */
        $templateVars = array(
            '{name}' => $name,
            '{text}' => $message,
            '{response}' => $response,
            '{link}' => $items_url,

        );

        //echo "<pre>"; var_dump($templateVars); exit;

        /* Email sending */


        $iso_lng = Language::getIsoById((int)($id_lang));

        $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

        if (is_dir($dir_mails . $iso_lng . '/')) {
            $id_lang_current = $id_lang;
        }
        else {
            $id_lang_current = Language::getIdByIso('en');
        }

        Mail::Send($id_lang_current, 'response-guestbook', $subject_response, $templateVars,
            $email, 'Response Form', NULL, NULL,
            NULL, NULL, dirname(__FILE__).'/../mails/');



    }
	
	public function getItem($_data){
		$id = $_data['id'];

        $items = Db::getInstance()->ExecuteS('
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blockguestbook` pc
			WHERE pc.`is_deleted` = 0 AND pc.`id` = '.(int)$id.'');

        $cookie = $this->context->cookie;

        $i=0;
        foreach($items as $_item) {
            $id_lang = ($_item['id_lang'] != 0) ? $_item['id_lang'] : (int)($cookie->id_lang);

            $name_lang = Language::getLanguage((int)($id_lang));
            $items[$i]['name_lang'] = $name_lang['name'];
        }

	   return array('reviews' => $items);
	}
	
	public function PageNav($start,$count,$step, $_data =null )
	{
		$_admin = isset($_data['admin'])?$_data['admin']:null;
		$page_translate = isset($_data['page'])?$_data['page']:$this->l('Page');


        $data_url = $this->getSEOURLs();

		$res = '';
		$product_count = $count;
		$res .= '<div class="pages">';
		$res .= '<span>'.$page_translate.': </span>';
		$res .= '<span class="nums">';
		
		$start1 = $start;
			for ($start1 = ($start - $step*4 >= 0 ? $start - $step*4 : 0); $start1 < ($start + $step*5 < $product_count ? $start + $step*5 : $product_count); $start1 += $step)
				{
					$par = (int)($start1 / $step) + 1;
					if ($start1 == $start)
						{
						
						$res .= '<b>'. $par .'</b>';
						}
					else
						{
							if($_admin){
								$token = $_data['token'];
								$currentIndex = $_data['currentIndex'];
								$token = $_data['token'];
								$res .= '<a href="'.$currentIndex.'&page='.($start1 ? $start1 : 0).'&configure='.$this->_name.'&token='.$token.'" >'.$par.'</a>';
							} else {


                                $delimeter_rewrite = "&";
                                if(Configuration::get('PS_REWRITING_SETTINGS')){
                                    $delimeter_rewrite = "?";
                                }


                                // all items page
                                if(Configuration::get($this->_name.'is_urlrewrite')==1 && Configuration::get('PS_REWRITING_SETTINGS')) {

                                    $items_url = $data_url['guestbook_url'];
                                    if(version_compare(_PS_VERSION_, '1.6', '<')) {
                                        $p = ($start1 ? $delimeter_rewrite.'p='.$par : '');
                                    } else {
                                        $p = ($start1 ? $delimeter_rewrite.'p='.$par : '');
                                    }


                                } else {

                                    $items_url = $data_url['guestbook_url'];
                                    $p = ($start1 ? $delimeter_rewrite.'p='.$par : '');

                                }

                                $res .= '<a href="'.$items_url.$p.'" title="'.$par.'">'.$par.'</a>';
                                // all items page
							}
						}
				}
		
		$res .= '</span>';
		$res .= '</div>';
		
		
		return $res;
	}


    public function getfacebooklocale()
    {
        $locales = array();

        if (($xml=simplexml_load_file(_PS_MODULE_DIR_ . $this->_name."/lib/facebook_locales.xml")) === false)
            return $locales;

        for ($i=0;$i<sizeof($xml);$i++)
        {
            $locale = $xml->locale[$i]->codes->code->standard->representation;
            $locales[]= $locale;
        }

        return $locales;
    }

    public function getfacebooklib($id_lang){

        $lang = new Language((int)$id_lang);

        $lng_code = isset($lang->language_code)?$lang->language_code:$lang->iso_code;
        if(strstr($lng_code, '-')){
            $res = explode('-', $lng_code);
            $language_iso = Tools::strtolower($res[0]).'_'.Tools::strtoupper($res[1]);
            $rss_language_iso = Tools::strtolower($res[0]);
        } else {
            $language_iso = Tools::strtolower($lng_code).'_'.Tools::strtoupper($lng_code);
            $rss_language_iso = $lng_code;
        }


        if (!in_array($language_iso, $this->getfacebooklocale()))
            $language_iso = "en_US";

        if (Configuration::get('PS_SSL_ENABLED') == 1)
            $url = "https://";
        else
            $url = "http://";



        return array('url'=>$url . 'connect.facebook.net/'.$language_iso.'/all.js#xfbml=1',
            'lng_iso' => $language_iso, 'rss_language_iso' => $rss_language_iso);
    }

    public function createRSSFile($post_title,$post_description,$post_link,$post_pubdate)
    {


        $returnITEM = "<item>\n";
        # this will return the Title of the Article.
        $returnITEM .= "<title><![CDATA[".$post_title."]]></title>\n";
        # this will return the Description of the Article.
        $returnITEM .= "<description><![CDATA[".$post_description."]]></description>\n";
        # this will return the URL to the post.
        $returnITEM .= "<link>".$post_link."</link>\n";

        $returnITEM .= "<pubDate>".$post_pubdate."</pubDate>\n";
        $returnITEM .= "</item>\n";
        return $returnITEM;
    }

    public function getItemsForRSS(){

        $step = Configuration::get($this->_name.'n_rssitemsg');


        $cookie = $this->context->cookie;
        $current_language = (int)$cookie->id_lang;

        $_is_friendly_url = $this->isURLRewriting();
        if($_is_friendly_url)
            $_iso_lng = Language::getIsoById((int)($current_language))."/";
        else
            $_iso_lng = '';

        $sql_condition = $this->getConditionMultilanguageAndMultiStore(array('and'=>1));

        $sql  = '
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blockguestbook` pc
			WHERE pc.active = 1 AND pc.`is_deleted` = 0 '.$sql_condition.'
			ORDER BY pc.`date_add` DESC LIMIT '.(int)($step);

        $items = Db::getInstance()->ExecuteS($sql);

        foreach($items as $k1=>$_item){

            //if($current_language == $_item['id_lang']){
                $items[$k1]['title'] = $_item['name'];
                $items[$k1]['seo_description'] = htmlspecialchars(strip_tags($_item['message']));
                $items[$k1]['pubdate'] = date('D, d M Y H:i:s +0000',strtotime($_item['date_add']));

                if(Configuration::get($this->_name.'is_urlrewrite') == 1){
                    $items[$k1]['page'] = $this->_http_host.$_iso_lng."guestbook";
                } else {
                    $items[$k1]['page'] = $this->_http_host."modules/'.$this->_name.'/'.$this->_name.'-form.php";
                }

            //}


        }


        return array('items' => $items);
    }
	
	public function getLangISO(){
        $cookie = $this->context->cookie;
        $id_lang = (int)$cookie->id_lang;

        $all_laguages = Language::getLanguages(true);

        if($this->isURLRewriting() && sizeof($all_laguages)>1)
            $iso_lang = Language::getIsoById((int)($id_lang))."/";
        else
            $iso_lang = '';

        return $iso_lang;
    }
    
    public function isURLRewriting(){
    	$_is_rewriting_settings = 0;
    	if(Configuration::get('PS_REWRITING_SETTINGS') && Configuration::get($this->_name.'is_urlrewrite') == 1){
			$_is_rewriting_settings = 1;
		} 
		return $_is_rewriting_settings;
    }

    public function getSEOURLs(){
        $iso_code = $this->getLangISO();

        if(Configuration::get($this->_name.'is_urlrewrite')==1){


            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                $guestbook_url = $this->getHttpost() . $iso_code. 'guestbook';
            } else {

                ## for ps 1.5, 1.6 and higher version ##
                $cookie = $this->context->cookie;
                $id_lang = (int)$cookie->id_lang;
                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $link = new Link();
                $guestbook_url = $link->getModuleLink("blockguestbook", 'guestbook', array(), $is_ssl, $id_lang);
                ## for ps 1.5, 1.6 and higher version ##
            }

        } else {


            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $guestbook_url = $this->getHttpost() . 'modules/' . $this->_name . '/blockguestbook-form.php';
            } else {
                ## for ps 1.5, 1.6 and higher version ##
                $cookie = $this->context->cookie;
                $id_lang = (int)$cookie->id_lang;
                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $link = new Link();
                $guestbook_url = $link->getModuleLink("blockguestbook", 'guestbook', array(), $is_ssl, $id_lang);
                ## for ps 1.5, 1.6 and higher version ##
            }
        }



        return array(
            'guestbook_url' => $guestbook_url,

        );
    }

    public function getHttpost(){
        if(version_compare(_PS_VERSION_, '1.5', '>')){
            $custom_ssl_var = 0;
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                $custom_ssl_var = 1;


            if ($custom_ssl_var == 1)
                $_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
            else
                $_http_host = _PS_BASE_URL_.__PS_BASE_URI__;

        } else {
            $_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
        }
        return $_http_host;
    }


    public function saveImage($data = null){

        $files = $_FILES['avatar-review'];

        ############### files ###############################
        if(!empty($files['name']))
        {
            if(!$files['error'])
            {
                $type_one = $files['type'];
                srand((double)microtime()*1000000);
                $uniq_name_image = uniqid(rand());
                $type_one = Tools::substr($type_one,6,Tools::strlen($type_one)-6);
                $filename = $uniq_name_image.'.'.$type_one;

                move_uploaded_file($files['tmp_name'], dirname(__FILE__).$this->path_img_cloud.$filename);

                $this->copyImage(array('dir_without_ext'=>dirname(__FILE__).$this->path_img_cloud.$uniq_name_image,
                        'name'=>dirname(__FILE__).$this->path_img_cloud.$filename)
                );

                $this->saveAvatar(array(
                        'avatar' => $uniq_name_image.'.jpg',
                        'id'=>$data['id'],
                    )
                );

                //$ext = explode("/",$type_one);
                @unlink(dirname(__FILE__).$this->path_img_cloud.$uniq_name_image.".".$type_one);
            }

        }

    }

    public function deleteAvatar($data){
        $id = $data['id'];

        $info_post = $this->getItem(array('id'=>$id));
        $img = $info_post['reviews'][0]['avatar'];

        //update
        $query = 'UPDATE '._DB_PREFIX_.'blockguestbook SET avatar =  ""

													    WHERE id = '.(int)$id;


        Db::getInstance()->Execute($query);


        @unlink(dirname(__FILE__).$this->path_img_cloud.$img);
    }


    public function saveAvatar($data){
        $avatar = $data['avatar'];
        $id = $data['id'];


        //update
        $query = 'UPDATE '._DB_PREFIX_.'blockguestbook SET avatar =  "'.pSQL($avatar).'"

													    WHERE id = '.(int)$id;


        Db::getInstance()->Execute($query);
    }

    public function copyImage($data){

        $filename = $data['name'];
        $dir_without_ext = $data['dir_without_ext'];

        $is_height_width = 0;
        if(isset($data['width']) && isset($data['height'])){
            $is_height_width = 1;
        }


        $width = isset($data['width'])?$data['width']:$this->_width;
        $height = isset($data['height'])?$data['height']:$this->_height;

        $width_orig_custom = $width;
        $height_orig_custom = $height;

        if (!$width){ $width = 85;}
        if (!$height){ $height = 85;}
        // Content type
        $size_img = getimagesize($filename);
        // Get new dimensions
        list($width_orig, $height_orig) = getimagesize($filename);
        $ratio_orig = $width_orig/$height_orig;

        if($width_orig>$height_orig){
            $height =  $width/$ratio_orig;
        }else{
            $width = $height*$ratio_orig;
        }
        if($width_orig<$width){
            $width = $width_orig;
            $height = $height_orig;
        }

        $image_p = imagecreatetruecolor($width, $height);
        $bgcolor=ImageColorAllocate($image_p, 255, 255, 255);
        //
        imageFill($image_p, 5, 5, $bgcolor);

        if ($size_img[2]==2){ $image = imagecreatefromjpeg($filename);}
        else if ($size_img[2]==1){  $image = imagecreatefromgif($filename);}
        else if ($size_img[2]==3) { $image = imagecreatefrompng($filename); }

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        // Output

        if ($is_height_width)
            $users_img = $dir_without_ext.'-'.$width_orig_custom.'x'.$height_orig_custom.'.jpg';
        else
            $users_img = $dir_without_ext.'.jpg';

        if ($size_img[2]==2)  imagejpeg($image_p, $users_img, 100);
        else if ($size_img[2]==1)  imagejpeg($image_p, $users_img, 100);
        else if ($size_img[2]==3)  imagejpeg($image_p, $users_img, 100);
        imageDestroy($image_p);
        imageDestroy($image);
        //unlink($filename);

    }

    private function getConditionMultilanguageAndMultiStore($data){

        $and = ($data['and']==1)?'AND':'';

        $id_shop = $this->getIdShop();

        if(Configuration::get($this->_name.'switch_lng_gb') == 1){
            $cookie = $this->context->cookie;
            $id_lang = (int)($cookie->id_lang);
            $sql_condition = $and.'  id_lang = '.(int)($id_lang).' AND id_shop = '.(int)($id_shop).'';
        } else {
            $sql_condition = $and.'  id_shop = '.(int)($id_shop).'';
        }
        return $sql_condition;
    }


    public function getInfoAboutCustomer($data=null){
        $id_customer = (int) $data['id_customer'];
        $is_full = isset($data['is_full'])?$data['is_full']:0;
        //get info about customer
        $sql = '
	        	SELECT * FROM `'._DB_PREFIX_.'customer`
		        WHERE `active` = 1 AND `id_customer` = \''.(int)($id_customer).'\'
		        AND `deleted` = 0 AND id_shop = '.(int)($this->getIdShop()).'  '.(defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
		        ';
        $result = Db::getInstance()->GetRow($sql);

        if(!$is_full)
            $lastname = Tools::strtoupper(Tools::substr($result['lastname'],0,1));
        else
            $lastname = $result['lastname'];

        $firstname = $result['firstname'];
        $customer_name = $firstname . " " . $lastname;
        $email = $result['email'];


        return array('customer_name' => $customer_name,'email'=>$email);
    }

    public function deleteGDPRCustomerData($email){

        $data_customer = Customer::getCustomersByEmail($email);
        if(count($data_customer)>0) {

            // blockguestbook
            $sql = 'DELETE FROM `'._DB_PREFIX_.'blockguestbook`
		        	WHERE  `email` = \''.pSQL($email).'\'
		        	AND `id_shop` = '.(int)$this->getIdShop().'
		        	';
            Db::getInstance()->Execute($sql);


        }

        return true;
    }

    public function getGDPRCustomerData($email){

        $data_customer = Customer::getCustomersByEmail($email);
        $customer_data = array();
        if(count($data_customer)>0) {

            // blockguestbook
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'blockguestbook`
		        	WHERE  `email` = \''.pSQL($email).'\'
		        	AND `id_shop` = '.(int)$this->getIdShop().'
		        	';
            $result_blockguestbook = Db::getInstance()->ExecuteS($sql);
            if(count($result_blockguestbook)>0)
                $customer_data['blockguestbook'] = $result_blockguestbook;



        }
        return $customer_data;

    }
	
}