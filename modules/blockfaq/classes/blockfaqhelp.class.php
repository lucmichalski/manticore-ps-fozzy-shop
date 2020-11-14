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
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

class blockfaqhelp extends Module{
	
	private $_id_shop;
	private $_is15;
	private $_name = 'blockfaq';
	private $_http_host;
	
	public function __construct(){
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$this->_id_shop = Context::getContext()->shop->id;
			$this->_is15 = 1;
		} else {
			$this->_id_shop = 0;
			$this->_is15 = 0;
		}
		
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$this->_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
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
	
	public function saveItemFAQ($data=null){
		$cookie = $this->context->cookie;
		$current_language = (int)$cookie->id_lang;
			
			
		$category_id = $data['category'];
		$name = $data['name'];
		$email = $data['email'];
		$text = $data['text'];
		
		#### faq item ####
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_item` SET
								`is_by_customer` = 1,
								`is_add_by_customer` = 1,
								`customer_name` = \''.pSQL($name).'\',
								`customer_email` = \''.pSQL($email).'\',
							   `status` = 0,
							   `ids_shops` = \''.(int)$this->_id_shop.'\'
							   ';
		
		Db::getInstance()->Execute($sql);
		
		$insert_id = Db::getInstance()->Insert_ID();
		
		Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'faq_item 
							SET order_by = '. (int)$insert_id .' WHERE id = ' .(int) $insert_id);
		
		
		$faq_id = $insert_id;
		
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_item_data` SET
							   `id_item` = \''.pSQL($faq_id).'\',
							   `id_lang` = \''.pSQL($current_language).'\',
							   `title` = \''.pSQL($text).'\'
							   ';
		Db::getInstance()->Execute($sql);
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_category2item` SET
						   `category_id` = \''.(int)($category_id).'\',
						   `faq_id` = \''.(int)($faq_id).'\'
						   ';
		Db::getInstance()->Execute($sql);
		
		#### faq item ####
		
		$this->sendNotification(array('name'=>$name, 'text'=>$text));
		
		
	}
	
	
	public function sendNotification($data = null){
		if(Configuration::get($this->_name.'notifaq') == 1){
		
			$name = $data['name'];
			$text = $data['text'];
			$cookie = $this->context->cookie;
			/* Email generation */
			$templateVars = array(
				'{name}' => $name,
				'{text}' => Tools::stripslashes($text)
			);
			$id_lang = (int)($cookie->id_lang);	
			/* Email sending */
			
			include_once(dirname(__FILE__).'/../blockfaq.php');
			$obj_blockfaq = new blockfaq();
			$_data_translate = $obj_blockfaq->translateItems();
			$notification_new_q = $_data_translate['notification_new_q']; 
			
			Mail::Send($id_lang, 'question-faq', $notification_new_q, $templateVars,
				Configuration::get($this->_name.'mailfaq'), 'Questions Form', NULL, NULL,
				NULL, NULL, dirname(__FILE__).'/../mails/');
		}
		
	}
	
	
public function sendNotificationResponse($data = null){
		
	if(Configuration::get($this->_name.'notifaq') == 1){
			$id = $data['id'];
			
			$_data_item_tmp = $this->getItem(array('id'=>$id));
			
			$_data = $_data_item_tmp['item'][0];
			
			$data_custom = isset($_data_item_tmp['item']['data'])?$_data_item_tmp['item']['data']:array();
			foreach($data_custom as $_custom){
				
				$name = $_custom['customer_name'];
				$question = $_custom['title'];
				$response = $_custom['content'];
				$email = $_custom['customer_email']; 
				
			}
			$id = $_data['id'];
			
			
			$cookie = $this->context->cookie;

			$data_url = $this->getSEOURLs();
            $faq_url = $data_url['faq_url'];
            $link_question = $faq_url.'#faq_'.$id;

			
			/* Email generation */
			$templateVars = array(
				'{name}' => $name,
				'{question}' => Tools::stripslashes($question),
				'{response}' => Tools::stripslashes($response),
				'{link_question}' => $link_question
			);
			
			
			
			//echo "<pre>"; var_dump($templateVars); var_dump($email);exit;
					
			/* Email sending */
			include_once(dirname(__FILE__).'/../blockfaq.php');
			$obj_blockfaq = new blockfaq();
			$_data_translate = $obj_blockfaq->translateItems();
			$response_for_q = $_data_translate['response_for_q'];
			
			$id_lang = isset($_data['id_lang'])?$_data['id_lang']:(int)($cookie->id_lang);
			
			
			Mail::Send($id_lang, 'response-faq', $response_for_q, $templateVars,
				$email, 'Response Form', NULL, NULL,
				NULL, NULL, dirname(__FILE__).'/../mails/');
				
		}
		
	}


    public function getItemsSite($_data = null){

        $is_search = isset($_data['is_search'])?$_data['is_search']:0;
        $search = isset($_data['search'])?$_data['search']:'';


        $sql_condition = '';
        if($is_search == 1){
            $sql_condition = "AND (
	    		   LOWER(pc_d.title) LIKE BINARY LOWER('%".pSQL(trim($search))."%')
	    		   OR
	    		   LOWER(pc_d.content) LIKE BINARY LOWER('%".pSQL(trim($search))."%')
	    		   ) ";
        }

        $_id_selected_category = isset($_data['id_category'])?$_data['id_category']:0;
        $sql_condition_cat_where = '';
        if($_id_selected_category!=0){
            $sql_condition_cat_where = ' pc.id = '.$_id_selected_category.' and ';
        }

        $cookie = $this->context->cookie;
        $current_language = (int)$cookie->id_lang;


        $sql_cat = '
			SELECT pc.*, pc_d.title as category_title
			FROM `'._DB_PREFIX_.'faq_category` pc
			LEFT JOIN `'._DB_PREFIX_.'faq_category_data` pc_d
			on(pc.id = pc_d.id_item)

			WHERE '.$sql_condition_cat_where.' pc.status = 1 and pc_d.id_lang = '.(int)$current_language.' AND
			FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			ORDER BY pc.`order_by` DESC';


        $items_cat = Db::getInstance()->ExecuteS($sql_cat);
        $items = array();


        foreach($items_cat as $_item_cat) {

            $category_id_parent = $_item_cat['id'];
            $category_title_parent = $_item_cat['category_title'];


            $sql = '
			SELECT pc.*
			FROM `' . _DB_PREFIX_ . 'faq_item` pc
			LEFT JOIN `' . _DB_PREFIX_ . 'faq_item_data` pc_d
			on(pc.id = pc_d.id_item)
			left join `'._DB_PREFIX_.'faq_category2item` fc2i
			ON(fc2i.faq_id = pc.id)

			WHERE fc2i.category_id  = '.(int)$category_id_parent.' and pc.status = 1 and pc_d.id_lang = ' . (int)$current_language . ' AND
			FIND_IN_SET(' . (int)$this->_id_shop . ',pc.ids_shops) ' . $sql_condition . '
			ORDER BY pc.`order_by` DESC';

            $items_tmp = Db::getInstance()->ExecuteS($sql);




            foreach ($items_tmp as $k => $_item) {

                $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `' . _DB_PREFIX_ . 'faq_item_data` pc
				WHERE pc.id_item = ' . (int)$_item['id'] . '
				');


                foreach ($items_data as $item_data) {

                    if ($current_language == $item_data['id_lang']) {

                        $items[$category_id_parent][$k]['category_title_parent'] = $category_title_parent;

                        $items[$category_id_parent][$k]['title'] = $item_data['title'];
                        $items[$category_id_parent][$k]['content'] = $item_data['content'];
                        $items[$category_id_parent][$k]['is_by_customer'] = $_item['is_by_customer'];
                        $items[$category_id_parent][$k]['customer_name'] = $_item['customer_name'];
                        $items[$category_id_parent][$k]['id'] = $_item['id'];
                        $items[$category_id_parent][$k]['time_add'] = $_item['time_add'];


                        $sql = '
						SELECT pc.category_id, fcd.title
						FROM `' . _DB_PREFIX_ . 'faq_category2item` pc
						LEFT JOIN `' . _DB_PREFIX_ . 'faq_category` fc
						on(fc.id = pc.category_id)
						left join `' . _DB_PREFIX_ . 'faq_category_data` fcd
						on(fcd.id_item = fc.id)
						WHERE pc.`faq_id` = ' . (int)$_item['id'] . ' and fcd.id_lang = ' . (int)$current_language;

                        $data_category_for_questions = Db::getInstance()->ExecuteS($sql);

                        $items[$category_id_parent][$k]['categories'] = $data_category_for_questions;

                    }
                }
            }
        }

        //echo "<pre>"; var_dump($items);exit;
        return array('items' => $items);
    }
	
	public function getItemsSite1($_data = null){
			
			$is_search = isset($_data['is_search'])?$_data['is_search']:0;
			$search = isset($_data['search'])?$_data['search']:'';
			
			$sql_condition = '';
			if($is_search == 1){
				$sql_condition = "AND (
	    		   LOWER(pc_d.title) LIKE BINARY LOWER('%".pSQL(trim($search))."%')
	    		   OR
	    		   LOWER(pc_d.content) LIKE BINARY LOWER('%".pSQL(trim($search))."%')
	    		   ) ";
			}
			
			$_id_selected_category = isset($_data['id_category'])?$_data['id_category']:0;
			$sql_condition_cat = '';
			$sql_condition_cat_where = '';
			if($_id_selected_category!=0){
			$sql_condition_cat = 'left join `'._DB_PREFIX_.'faq_category2item` fc2i 
								ON(fc2i.faq_id = pc.id) ';
			$sql_condition_cat_where = 'fc2i.category_id  = '.$_id_selected_category.' and ';
			}
			
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			$sql = '
			SELECT pc.*
			FROM `'._DB_PREFIX_.'faq_item` pc 
			LEFT JOIN `'._DB_PREFIX_.'faq_item_data` pc_d
			on(pc.id = pc_d.id_item)
			'.$sql_condition_cat.'
			WHERE '.$sql_condition_cat_where.' pc.status = 1 and pc_d.id_lang = '.(int)$current_language.' AND
			FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) '.$sql_condition.'
			ORDER BY pc.`order_by` DESC';
			
			$items_tmp = Db::getInstance()->ExecuteS($sql);
			
			$items = array();
			
			foreach($items_tmp as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_item_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    			$items[$k]['content'] = $item_data['content'];
		    			$items[$k]['is_by_customer'] = $_item['is_by_customer'];
		    			$items[$k]['customer_name'] = $_item['customer_name'];
		    			$items[$k]['id'] = $_item['id'];
		    			$items[$k]['time_add'] = $_item['time_add'];
		    			
		    			
		    			$sql = '
						SELECT pc.category_id, fcd.title
						FROM `'._DB_PREFIX_.'faq_category2item` pc
						LEFT JOIN `'._DB_PREFIX_.'faq_category` fc
						on(fc.id = pc.category_id)
						left join `'._DB_PREFIX_.'faq_category_data` fcd
						on(fcd.id_item = fc.id)
						WHERE pc.`faq_id` = '.(int)$_item['id'].' and fcd.id_lang = '.(int)$current_language;
		    			
		    			$data_category_for_questions = Db::getInstance()->ExecuteS($sql);
		    			
		    			$items[$k]['categories'] = $data_category_for_questions;
		    			
		    		} 
		    	}
		    }
		    
		    return array('items' => $items);
	}
	
	public function getItems($_data = null){
		
		$_id_selected_category = isset($_data['id_category'])?$_data['id_category']:0;
			$sql_condition = '';
			if($_id_selected_category!=0)
			$sql_condition = 'left join `'._DB_PREFIX_.'faq_category2item` fc2i 
								ON(fc2i.faq_id = pc.id) where fc2i.category_id  = '.(int)$_id_selected_category;
		
		$sql = '
			SELECT pc.*
			FROM `'._DB_PREFIX_.'faq_item` pc
			'.$sql_condition.'
			ORDER BY pc.`order_by` DESC';


		$items = Db::getInstance()->ExecuteS($sql);
			
			foreach($items as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_item_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				$cookie = $this->context->cookie;
				$defaultLanguage =  $cookie->id_lang;
				
				$tmp_title = '';
				// languages
				$languages_tmp_array = array();
			
				foreach ($items_data as $item_data){
		    		
					$languages_tmp_array[] = $item_data['id_lang'];
		    		
					
		    		$title = isset($item_data['title'])?$item_data['title']:'';
		    		if(Tools::strlen($tmp_title)==0){
		    			if(Tools::strlen($title)>0)
		    					$tmp_title = $title; 
		    		}
		    		
		    		
		    		if($defaultLanguage == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    		} 
		    	}
		    	
		    	if(@Tools::strlen($items[$k]['title'])==0)
		    		$items[$k]['title'] = $tmp_title;
		    		
		    	// languages
		    	$items[$k]['ids_lng'] = $languages_tmp_array;
		    	
		    	
		    	$questions_ids = Db::getInstance()->ExecuteS('
				SELECT pc.category_id, pc.faq_id
				FROM `'._DB_PREFIX_.'faq_category2item` pc
				WHERE pc.`faq_id` = '.(int)$_item['id'].'');
				$data_questions_ids = array();
				foreach($questions_ids as $v1){
					$data_questions_ids[] = $v1['category_id'];
				}
				$items[$k]['faq_category_ids'] = implode(",",$data_questions_ids);
		    	
				
			}
			

		return array('items' => $items);
	}


    public function getItemsAll16(){

        $cookie = $this->context->cookie;
        $current_language = (int)$cookie->id_lang;
        $sql = '
			SELECT *
			FROM `'._DB_PREFIX_.'faq_item` fi join `'._DB_PREFIX_.'faq_item_data` fid on(fi.id = fid.id_item)
			where fid.id_lang = '.(int)$current_language.'
			ORDER BY fi.order_by DESC';


        $items = Db::getInstance()->ExecuteS($sql);


        return array('items' => $items);
    }
	
	public function getCategoryItem($_data){
		$id = $_data['id'];
		$admin = isset($_data['admin'])?$_data['admin']:0;
		
		if($admin == 1){
				$sql = '
					SELECT pc.*
					FROM `'._DB_PREFIX_.'faq_category` pc
					WHERE id = '.(int)$id.'';
			$item = Db::getInstance()->ExecuteS($sql);
			
			foreach($item as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    			$item['data'][$item_data['id_lang']]['title'] = $item_data['title'];
		    			
		    	}
		    	
			}
			
			$questions_ids = Db::getInstance()->ExecuteS('
			SELECT pc.category_id, pc.faq_id
			FROM `'._DB_PREFIX_.'faq_category2item` pc
			WHERE pc.`category_id` = '.(int)$id.'');
			$data_questions_ids = array();
			foreach($questions_ids as $k => $v){
				$data_questions_ids[] = $v['faq_id'];
			}
			
			$item[0]['faq_questions_ids'] = $data_questions_ids;
			
		} else {
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			
				$sql = '
					SELECT pc.*
					FROM `'._DB_PREFIX_.'faq_category` pc
					LEFT JOIN `'._DB_PREFIX_.'faq_category_data` pc1
					ON(pc1.id_item = pc.id)
					WHERE pc.`id` = '.(int)$id.' AND pc1.id_lang = '.(int)$current_language;
			
			$item = Db::getInstance()->ExecuteS($sql);
			
			foreach($item as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$item[$k]['title'] = $item_data['title'];
		    			
		    		}
		    	}
		    }
			
		}
		
	   return array('item' => $item);
	}
	
	public function saveItem($data){
	
		$ids_shops = implode(",",$data['faq_shop_association']);
		
		$item_status = $data['item_status'];
		
		$is_by_customer = $data['is_by_customer'];
		$faq_customer_name = $data['faq_customer_name'];
		$faq_customer_email = $data['faq_customer_email'];

        $time_add = isset($data['time_add'])?$data['time_add']:null;
        $sql_time_add = '';
        if($time_add) {
            $sql_time_add = ' `time_add` = "'.pSQL($time_add).'", ';
        }
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_item` SET
								`is_by_customer` = \''.pSQL($is_by_customer).'\',
								`customer_name` = \''.pSQL($faq_customer_name).'\',
								`customer_email` = \''.pSQL($faq_customer_email).'\',
							   `status` = \''.pSQL($item_status).'\',
							   '.$sql_time_add.'
							   `ids_shops` = \''.pSQL($ids_shops).'\'
							   ';
		Db::getInstance()->Execute($sql);
		
		$insert_id = Db::getInstance()->Insert_ID();
		
		Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'faq_item 
							SET order_by = '. (int)$insert_id .' WHERE id = ' . (int)$insert_id);
		
		
		$post_id = $insert_id;
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$title = $item['title'];
		$content = $item['content'];
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_item_data` SET
							   `id_item` = \''.pSQL($post_id).'\',
							   `id_lang` = \''.pSQL($language).'\',
							   `title` = \''.pSQL($title).'\',
							   `content` = "'.pSQL($content,true).'"
							   ';
		Db::getInstance()->Execute($sql);
		}
		
		$faq_category_association = sizeof($data['faq_category_association'])>0?$data['faq_category_association']:array();

        if($faq_category_association) {
            foreach ($faq_category_association as $id_cat) {
                if ($id_cat != 0) {
                    $sql = 'INSERT into `' . _DB_PREFIX_ . 'faq_category2item` SET
							   `category_id` = \'' . (int)($id_cat) . '\',
							   `faq_id` = \'' . (int)($post_id) . '\'
							   ';
                    Db::getInstance()->Execute($sql);
                }
            }
        }
		
		
		
		
		
	}
	
	
	public function saveItemCategory($data){
	
		$ids_shops = implode(",",$data['faq_shop_association']);
		
		$item_status = $data['item_status'];

        $time_add = isset($data['time_add'])?$data['time_add']:null;
        $sql_time_add = '';
        if($time_add) {
            $sql_time_add = ' `time_add` = "'.pSQL($time_add).'", ';
        }
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_category` SET
							   `status` = \''.pSQL($item_status).'\',
							    '.$sql_time_add.'
							   `ids_shops` = \''.pSQL($ids_shops).'\'
							   ';
		Db::getInstance()->Execute($sql);
		
		$insert_id = Db::getInstance()->Insert_ID();
		
		Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'faq_category 
							SET order_by = '. (int)$insert_id .' WHERE id = ' . (int)$insert_id);
		
		
		$faq_cat_id = $insert_id;
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$title = $item['title'];
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_category_data` SET
							   `id_item` = \''.pSQL($faq_cat_id).'\',
							   `id_lang` = \''.pSQL($language).'\',
							   `title` = \''.pSQL($title).'\'
							   ';
		Db::getInstance()->Execute($sql);
		}
		
	$faq_questions_association = sizeof($data['faq_questions_association'])>0?$data['faq_questions_association']:array();

        if($faq_questions_association) {
            foreach ($faq_questions_association as $id_cat) {
                if ($id_cat != 0) {
                    $sql = 'INSERT into `' . _DB_PREFIX_ . 'faq_category2item` SET
							   `category_id` = \'' . (int)($insert_id) . '\',
							   `faq_id` = \'' . (int)($id_cat) . '\'
							   ';
                    Db::getInstance()->Execute($sql);
                }
            }
        }
		
		
		
		
		
	}
	
	public function deleteItem($data){
		
	
		$id = $data['id'];
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_item`
					   WHERE id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_item_data`
					   WHERE id_item ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_category2item`
					   WHERE faq_id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
			
	}
	
	public function deleteItemCategory($data){
		$id = $data['id'];
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_category`
					   WHERE id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_category_data`
					   WHERE id_item ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_category2item`
					   WHERE category_id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
	}
	
	public function getItem($_data){
		$id = $_data['id'];
		
		
		$sql = '
			SELECT pc.*
			FROM `'._DB_PREFIX_.'faq_item` pc
			WHERE id = '.(int)$id;
			
			$item = Db::getInstance()->ExecuteS($sql);
			
			foreach($item as $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_item_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    			$item['data'][$item_data['id_lang']]['title'] = $item_data['title'];
		    			$item['data'][$item_data['id_lang']]['content'] = $item_data['content'];
		    			$item['data'][$item_data['id_lang']]['customer_name'] = $_item['customer_name'];
		    			$item['data'][$item_data['id_lang']]['customer_email'] = $_item['customer_email'];
		    	}
		    	
		    	
			}
			
			$category_ids = Db::getInstance()->ExecuteS('
			SELECT pc.category_id, pc.faq_id
			FROM `'._DB_PREFIX_.'faq_category2item` pc
			WHERE pc.`faq_id` = '.(int)$id.'');
			$data_category_ids = array();
			foreach($category_ids as $v){
				$data_category_ids[] = $v['category_id'];
			}
			
			$item[0]['faq_category_ids'] = $data_category_ids;
			
			//echo "<pre>"; var_dump($item); exit;
	   return array('item' => $item);
	}


    public function getItemsCategoryAll16(){

        $cookie = $this->context->cookie;
        $current_language = (int)$cookie->id_lang;
        $sql = '
			SELECT *
			FROM `'._DB_PREFIX_.'faq_category` fi join `'._DB_PREFIX_.'faq_category_data` fid on(fi.id = fid.id_item)
			where fid.id_lang = '.(int)$current_language.'
			ORDER BY fi.order_by DESC';


        $items = Db::getInstance()->ExecuteS($sql);


        return array('items' => $items);
    }
	
public function getItemsCategory($_data = null){
		$admin = isset($_data['admin'])?$_data['admin']:null;
		$items = array();
		if($admin){
			
				$sql = '
				SELECT pc.*,
				(select count(*) as count from `'._DB_PREFIX_.'faq_category2item` c2p
				    WHERE c2p.category_id = pc.id ) as count_faq
				FROM `'._DB_PREFIX_.'faq_category` pc
				ORDER BY pc.`order_by` DESC';
				
			$categories = Db::getInstance()->ExecuteS($sql);
			
			
			foreach($categories as $k => $_item){
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				
				$cookie = $this->context->cookie;
				$defaultLanguage =  $cookie->id_lang;
				
				$tmp_title = '';
				$tmp_id = '';
				$tmp_time_add = '';

				// languages
				$languages_tmp_array = array();
				
				foreach ($items_data as $item_data){
					$languages_tmp_array[] = $item_data['id_lang'];
		    		
		    		$title = isset($item_data['title'])?$item_data['title']:'';
		    		$id = isset($item_data['id_item'])?$item_data['id_item']:'';
		    		$time_add = isset($categories[$k]['time_add'])?$categories[$k]['time_add']:'';
		    		
		    		if(Tools::strlen($tmp_title)==0){
		    			if(Tools::strlen($title)>0)
		    					$tmp_title = $title; 
		    		}
		    		
					if(Tools::strlen($tmp_id)==0){
		    			if(Tools::strlen($id)>0)
		    					$tmp_id = $id; 
		    		}
		    		
					if(Tools::strlen($tmp_time_add)==0){
		    			if(Tools::strlen($time_add)>0)
		    					$tmp_time_add = $time_add; 
		    		}
		    		
		    		if($defaultLanguage == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    			$items[$k]['id'] = $id;
		    			$items[$k]['time_add'] = $time_add;
		    		}
		    		
		    	}
		    	
		    	if(@Tools::strlen($items[$k]['title'])==0)
		    		$items[$k]['title'] = $tmp_title;
		    		
		    	if(@Tools::strlen($items[$k]['id'])==0)
		    		$items[$k]['id'] = $tmp_id;
		    		
		    	if(@Tools::strlen($items[$k]['time_add'])==0)
		    		$items[$k]['time_add'] = $tmp_time_add;
		    	
		    	$items[$k]['count_faq'] = $categories[$k]['count_faq'];
		    	$items[$k]['order_by'] = $categories[$k]['order_by'];
		    	$items[$k]['status'] = $categories[$k]['status'];
		    	
		    	$items[$k]['ids_shops'] = $categories[$k]['ids_shops'];
		    	// languages
		    	$items[$k]['ids_lng'] = $languages_tmp_array;


		    	
			}
			
			$data_count_categories = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'faq_category` 
			');
			
		} else{
			
			
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			$items_tmp = Db::getInstance()->ExecuteS('
			SELECT pc.*,
				   (select count(*) as count from `'._DB_PREFIX_.'faq_item` pc1 
				    LEFT JOIN `'._DB_PREFIX_.'faq_category2item` c2p
				    ON(pc1.id = c2p.faq_id)
				    LEFT JOIN `'._DB_PREFIX_.'faq_item_data` bpd
				    ON(bpd.id_item = pc1.id)
					WHERE c2p.category_id = pc.id AND bpd.id_lang = '.(int)$current_language.'
					AND pc1.status = 1 AND FIND_IN_SET('.(int)$this->_id_shop.',pc1.ids_shops)) as count_faq
			FROM `'._DB_PREFIX_.'faq_category` pc
			LEFT JOIN `'._DB_PREFIX_.'faq_category_data` pc_d
			on(pc.id = pc_d.id_item)
			WHERE pc_d.id_lang = '.(int)$current_language.'  AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			ORDER BY pc.`time_add` DESC');	
			
			$items = array();
			
			foreach($items_tmp as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    			$items[$k]['count_faq'] = $_item['count_faq'];
		    			$items[$k]['id'] = $_item['id'];
		    			$items[$k]['time_add'] = $_item['time_add'];
		    		} 
		    	}
		    }
			
			$data_count_categories = Db::getInstance()->getRow('
			SELECT COUNT(pc.`id`) AS "count"
			FROM `'._DB_PREFIX_.'faq_category` pc LEFT JOIN `'._DB_PREFIX_.'faq_category_data` pc_d
			on(pc.id = pc_d.id_item)
			WHERE pc_d.id_lang = '.(int)$current_language.'  AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			');
		}	
		return array('items' => $items, 'count_all' => $data_count_categories['count'] );
	}
	
	
	public function updateItem($data){
		
		
		$id = $data['id'];
		$ids_shops = implode(",",$data['faq_shop_association']);
		
		$item_status = $data['item_status'];
		
		$is_add_by_customer = $data['is_add_by_customer'];
		
		$is_by_customer = $data['is_by_customer'];
		$faq_customer_name = $data['faq_customer_name'];
		$faq_customer_email = $data['faq_customer_email'];


        $time_add = isset($data['time_add'])?$data['time_add']:null;
        $sql_time_add = '';
        if($time_add) {
            $sql_time_add = ' `time_add` = "'.pSQL($time_add).'", ';
        }
		
		// update
		$sql = 'UPDATE `'._DB_PREFIX_.'faq_item` SET
								`is_by_customer` = \''.pSQL($is_by_customer).'\',
								`customer_name` = \''.pSQL($faq_customer_name).'\',
								`customer_email` = \''.pSQL($faq_customer_email).'\',
							   `status` = \''.pSQL($item_status).'\',
							   '.$sql_time_add.'
							   `ids_shops` = \''.pSQL($ids_shops).'\'
							   WHERE id = '.(int)$id.'
							   ';
		Db::getInstance()->Execute($sql);
		
		/// delete tabs data
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_item_data` WHERE id_item = '.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$title = $item['title'];
		$content = $item['content'];
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_item_data` SET
							   `id_item` = \''.pSQL($id).'\',
							   `id_lang` = \''.pSQL($language).'\',
							   `title` = \''.pSQL($title).'\',
							   `content` = "'.pSQL($content,true).'"
							   ';
		Db::getInstance()->Execute($sql);
		}
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_category2item`
					   WHERE `faq_id` = \''.(int)($id).'\'';
		Db::getInstance()->Execute($sql);
		
		$faq_category_association = sizeof($data['faq_category_association'])>0?$data['faq_category_association']:array();
		
		if(!empty($faq_category_association)){
			foreach($faq_category_association as $id_cat){
				if($id_cat!=0){
				$sql = 'INSERT into `'._DB_PREFIX_.'faq_category2item` SET
								   `category_id` = \''.(int)($id_cat).'\',
								   `faq_id` = \''.(int)($id).'\'
								   ';
				Db::getInstance()->Execute($sql);
				}
			}
		}
		if($is_add_by_customer == 1 && $item_status == 1){
			$this->sendNotificationResponse(array('id'=>$id));
		}
		
	}
	
	public function updateItemCategory($data){
		$id = $data['id'];
		$ids_shops = implode(",",$data['faq_shop_association']);
		
		$item_status = $data['item_status'];

        $time_add = isset($data['time_add'])?$data['time_add']:null;
        $sql_time_add = '';
        if($time_add) {
            $sql_time_add = ' `time_add` = "'.pSQL($time_add).'", ';
        }

		// update
		$sql = 'UPDATE `'._DB_PREFIX_.'faq_category` SET
							   `status` = \''.pSQL($item_status).'\',
							   '.$sql_time_add.'
							   `ids_shops` = \''.pSQL($ids_shops).'\'
							   WHERE id = '.(int)$id.'
							   ';
		Db::getInstance()->Execute($sql);
		
		/// delete tabs data
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_category_data` WHERE id_item = '.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$title = $item['title'];
		
		$sql = 'INSERT into `'._DB_PREFIX_.'faq_category_data` SET
							   `id_item` = \''.pSQL($id).'\',
							   `id_lang` = \''.pSQL($language).'\',
							   `title` = \''.pSQL($title).'\'
							   ';
		Db::getInstance()->Execute($sql);
		}
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'faq_category2item`
					   WHERE `category_id` = \''.(int)($id).'\'';
		Db::getInstance()->Execute($sql);
		
		$faq_questions_association = sizeof($data['faq_questions_association'])>0?$data['faq_questions_association']:array();

        if($faq_questions_association) {
            foreach ($faq_questions_association as $id_cat) {
                if ($id_cat != 0) {
                    $sql = 'INSERT into `' . _DB_PREFIX_ . 'faq_category2item` SET
							   `category_id` = \'' . (int)($id) . '\',
							   `faq_id` = \'' . (int)($id_cat) . '\'
							   ';
                    Db::getInstance()->Execute($sql);
                }
            }
        }
		
		
	}


    public function updateCategoryStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'faq_category` SET
						`status` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);



    }


    public function updateQuestionStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'faq_item` SET
						`status` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);



    }
	
	public function getItemsBlock(){
		
		$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			$limit  = Configuration::get($this->_name.'faq_blc');
			$sql = '
			SELECT pc.*
			FROM `'._DB_PREFIX_.'faq_item` pc 
			LEFT JOIN `'._DB_PREFIX_.'faq_item_data` pc_d
			ON(pc.id = pc_d.id_item) 
			WHERE pc.status = 1 AND
			FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			and pc_d.id_lang = '.(int)$current_language.' ORDER BY pc.`order_by` DESC LIMIT '.(int)$limit;
			
			$items = Db::getInstance()->ExecuteS($sql);
			$items_tmp = array();
			foreach($items as $k => $_item){
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'faq_item_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				
				
				foreach ($items_data as $item_data){
		    		if($current_language == $item_data['id_lang']){
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['title'] = $item_data['title'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['content'] = $item_data['content'];
		    			//$items_tmp[$k]['data'][$item_data['id_lang']]['time_add'] = $_item['time_add'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['id'] = $_item['id'];
		    		}
		    	}
		    	
			}
		return array('items' => $items_tmp );
			
			
	
	}
	
	
	public function update_order($id, $order, $id_change, $order_change){
		
		$db = Db::getInstance();

		$db->Execute('UPDATE ' . _DB_PREFIX_ . 'faq_item SET order_by = '. (int)$order_change .' WHERE id = ' . (int)$id);
		$db->Execute('UPDATE ' . _DB_PREFIX_ . 'faq_item SET order_by = '. (int)$order .' WHERE id = ' . (int)$id_change);

		
	}
	
	public function update_order_faqcat($id, $order, $id_change, $order_change){
		
		$db = Db::getInstance();

		$db->Execute('UPDATE ' . _DB_PREFIX_ . 'faq_category SET order_by = '. (int)$order_change .' WHERE id = ' . (int)$id);
		$db->Execute('UPDATE ' . _DB_PREFIX_ . 'faq_category SET order_by = '. (int)$order .' WHERE id = ' . (int)$id_change);

		
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
                $faq_url = $this->getHttpost() . $iso_code . 'faq';
            } else {

                ## for ps 1.5, 1.6 and higher version ##
                $cookie = $this->context->cookie;
                $id_lang = (int)$cookie->id_lang;
                $is_ssl = false;
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
                    $is_ssl = true;

                $link = new Link();
                $faq_url = $link->getModuleLink("blockfaq", 'faq', array(), $is_ssl, $id_lang);
                ## for ps 1.5, 1.6 and higher version ##
            }

        } else {

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $faq_url = $this->getHttpost() . 'modules/' . $this->_name . '/faq.php';
            } else {
                ## for ps 1.5, 1.6 and higher version ##
                $cookie = $this->context->cookie;
                $id_lang = (int)$cookie->id_lang;
                $is_ssl = false;
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
                    $is_ssl = true;

                $link = new Link();
                $faq_url = $link->getModuleLink("blockfaq", 'faq', array(), $is_ssl, $id_lang);
                ## for ps 1.5, 1.6 and higher version ##
            }
        }



        return array(
            'faq_url' => $faq_url,

        );
    }

    public function getHttpost(){
        if(version_compare(_PS_VERSION_, '1.5', '>')){
            $custom_ssl_var = 0;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
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
	
	 
	
}