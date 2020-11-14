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

ob_start();
require_once(_PS_MODULE_DIR_ . 'blockfaq/classes/FaqfaqsItems.php');

class AdminFaqquestionsController extends ModuleAdminController{

    private $_name_controller = 'AdminFaqquestions';
    private $_name_module = 'blockfaq';
    private $_data_table = 'faq_item_data';
    private  $_id_lang;
    private  $_id_shop;
    private  $_iso_code;

    public function __construct()

    {

        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'faq_item';


        $this->identifier = 'id';
        $this->className = 'FaqfaqsItems';

        $this->lang = false;

        $this->_default_pagination = 1000;


        $this->_orderBy = 'order_by';
        $this->_orderWay = 'DESC';



        $this->allow_export = false;
        $this->list_no_link = true;


        $id_lang =  $this->context->cookie->id_lang;
        $this->_id_lang = $id_lang;



        $id_shop =  $this->context->shop->id;

        $this->_id_shop = $id_shop;



        $iso_code = Language::getIsoById($id_lang);
        $this->_iso_code = $iso_code;

        $this->_select .= 'a.id, c.title, a.time_add , a.order_by, a.is_by_customer,  c.id_lang, '.$id_shop.' as id_shop, a.status ';
        $this->_join .= '  JOIN `' . _DB_PREFIX_ . $this->_data_table.'` c ON (c.id_item = a.id and c.id_lang = '.$id_lang.')';

        //$this->_join .= '  JOIN `' . _DB_PREFIX_ . $this->_data_table.'` c ON (c.id_item = a.id)';

        $this->_select .= ', (SELECT group_concat(sh.`name` SEPARATOR \', \')
                    FROM `'._DB_PREFIX_.'shop` sh
                    WHERE sh.`active` = 1 AND sh.deleted = 0 AND sh.`id_shop`
                    IN(SELECT
                          SUBSTRING_INDEX(SUBSTRING_INDEX(pt_in.ids_shops, \',\', sh_in.id_shop), \',\', -1) name
                        FROM
                          '._DB_PREFIX_.'shop as sh_in INNER JOIN '._DB_PREFIX_.$this->table.' pt_in
                          ON CHAR_LENGTH(pt_in.ids_shops)
                             -CHAR_LENGTH(REPLACE(pt_in.ids_shops, \',\', \'\'))>=sh_in.id_shop-1
                        WHERE pt_in.id =  a.id
                        ORDER BY
                          id, sh_in.id_shop)
                    ) as shop_name';


        $this->_select .= ', (SELECT group_concat(l.`iso_code` SEPARATOR \', \')
	            FROM `'._DB_PREFIX_.'lang` l
	            JOIN
	            `'._DB_PREFIX_.'lang_shop` ls
	            ON(l.id_lang = ls.id_lang)
	            WHERE l.`active` = 1 AND ls.id_shop = '.$id_shop.' AND l.`id_lang`
	            IN( select pt_d.id_lang FROM `'._DB_PREFIX_.$this->_data_table.'` pt_d WHERE pt_d.id_item = a.id)) as language';

        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.$this->table.'` pc1
				    LEFT JOIN `'._DB_PREFIX_.'faq_category2item` c2p
				    ON(pc1.id = c2p.faq_id)
				    LEFT JOIN `'._DB_PREFIX_.'faq_item_data` bpd
				    ON(bpd.id_item = pc1.id)
					WHERE c2p.category_id = a.id AND bpd.id_lang = '.(int)$id_lang.'
					AND FIND_IN_SET('.$id_shop.',pc1.ids_shops)) as count_posts ';


        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.$this->table.'` pc2
				    WHERE FIND_IN_SET('.$id_shop.',pc2.ids_shops)) as count_all';


        $this->_select .= ', (select group_concat(pc3.`category_id` SEPARATOR \', \') from `'._DB_PREFIX_.'faq_category2item` pc3
				    WHERE pc3.faq_id = a.id) as ids_category';



        $filter = Tools::getValue('submitFilterfaq_item');
        $category_id_filter = Tools::getValue('faq_itemFilter_fc2i!category_id');


        $cookie = new Cookie($this->_name_module);

        //var_dump($category_id_filter);var_dump(Tools::strlen($category_id_filter));var_dump(($filter));exit;
        // '' = '' , 1
        // 0 = 0 ,  1

        //$cookie->is_filterfaq = 1;

        //var_dump($category_id_filter);exit;

        //var_dump($filter);exit;

        if(
            (Tools::strlen($category_id_filter)==0 && $category_id_filter !== false && $filter)
            ||
            !$filter
            ){

            //var_dump($category_id_filter);var_dump(Tools::strlen($category_id_filter));var_dump(($filter));exit;

            $cookie->is_filterfaq = 0;
        } else {

            //var_dump($category_id_filter);var_dump(Tools::strlen($category_id_filter));var_dump(($filter));exit;
            if($filter && $category_id_filter !== false)
                $cookie->is_filterfaq = 1;
        }

        $is_filterfaq_order = $cookie->is_filterfaq_order;

        if($is_filterfaq_order == 1){
            $cookie->is_filterfaq = 1;
        }

        $is_filterfaq = $cookie->is_filterfaq;
//var_dump($is_filterfaq);exit;

//var_dumP($is_filterfaq);var_dump($_POST);exit;

        if($is_filterfaq){

            $cookie->is_filterfaq = 1;

            $this->_join .= ' JOIN `'._DB_PREFIX_.'faq_category2item` fc2i ON(fc2i.faq_id = a.id )';
        }



        $this->addRowAction('edit');
        $this->addRowAction('delete');
        //$this->addRowAction('view');
        //$this->addRowAction('&nbsp;');



        if(Configuration::get($this->_name_module.'is_urlrewrite')){
            $is_rewrite = 1;
        } else {
            $is_rewrite = 0;
        }


        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blockfaqhelp.class.php');
        $obj_blockfaq = new blockfaqhelp();
        $_data = $obj_blockfaq->getItemsAll16();
        $all_items_sort = $_data['items'];

        //echo "<pre>"; var_dump($all_items_sort);exit;


        ### get categories ##
        $_data = $obj_blockfaq->getItemsCategory(array('admin'=>1));
        $all_category_tmp = $_data['items'];
        $all_category = array();
        $all_category[0] = $this->l('Questions without Category (Category ID = 0)');
        foreach($all_category_tmp as $_item_tmp){
            $all_category[$_item_tmp['id']] = $_item_tmp['title'].' ('.$this->l('Category ID').' = '.$_item_tmp['id'].')';
        }
        ### get categories ##





        $all_languages = Language::getLanguages(true);

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,

            ),

            'title' => array(
                'title' => $this->l('Question title'),
                'width' => 'auto',
                'orderby' => true,
                'type_custom' => 'title_category',
                'is_rewrite' => $is_rewrite,
                'iso_code' => count($all_languages)>1?$this->_iso_code."/":"",
                'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,

            ),

            'ids_category' => array(
                'title' => $this->l('Filter questions by category'),
                'width' => 'auto',
                'type' => 'select', 'list' => $all_category  ,
                'filter_key' => 'fc2i!category_id',
                'orderby' => false,
                'align' => 'center',
                'type_custom'=>'category_id',

            ),

            'is_by_customer' => array(
                'title' => $this->l('By Customer'),
                'width' => 'auto',
                'search' => false,
                'align' => 'center',
                'type_custom' => 'is_by_customer'

            ),


            'shop_name' => array(
                'title' => $this->l('Shop'),
                'width' => 'auto',
                'search' => false

            ),

            'language' => array(
                'title' => $this->l('Language'),
                'width' => 'auto',
                'search' => false

            ),

            'time_add' => array(
                'title' => $this->l('Date add'),
                'width' => 'auto',
                'search' => false,

            ),


            'order_by' => array(
                'title' => $this->l('Position'),
                'align' => 'center',
                'search' => false,
                'type_custom'=>'order_by',
                'name_controller'=>$this->_name_controller,
                'token'=>Tools::getAdminToken($this->_name_controller.(int)(Tab::getIdFromClassName($this->_name_controller)).(int)($this->context->cookie->id_employee)),
                'all_items_sort' => $all_items_sort,
            ),

            'status' => array(
                'title' => $this->l('Status'),
                'width' => 40,
                'align' => 'center',
                'type' => 'bool',
                'orderby' => FALSE,
                'type_custom' => 'is_active',
            ),

        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );



        parent::__construct();

    }




    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        $list = parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        $this->_listsql = false;
        return $list;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['add_item'] = array(
                'href' => self::$currentIndex.'&addfaq_item&token='.$this->token,
                'desc' => $this->l('Add new question', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar() {

        parent::initToolbar();


    }



    public function postProcess()
    {


        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blockfaqhelp.class.php');
        $blockfaqhelp_obj = new blockfaqhelp();


        $id_self = (int)Tools::getValue('id');
        $order_self = Tools::getValue('order_self');
        if($order_self){
            $blockfaqhelp_obj->update_order($id_self, $order_self, Tools::getValue('id_change'), Tools::getValue('order_change'));


            $cookie = new Cookie($this->_name_module);
            $is_filterfaq = $cookie->is_filterfaq;

            if($is_filterfaq == 1){
                $cookie->is_filterfaq_order = 1;
            }

            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . Tools::getAdminTokenLite($this->_name_controller));
        } else {


            $cookie = new Cookie($this->_name_module);
            $cookie->is_filterfaq_order = 0;
        }


        if (Tools::isSubmit('add_item')) {
            ## add item ##


            $time_add = Tools::getValue("time_add");

            $languages = Language::getLanguages(false);
            $data_title_content_lang = array();

            $faq_shop_association = Tools::getValue("cat_shop_association");


            $faq_item_status = Tools::getValue("status");
            $is_by_customer = Tools::getValue("is_by_customer");
            $faq_customer_email = Tools::getValue("faq_customer_email");
            $faq_customer_name = Tools::getValue("faq_customer_name");
            $faq_category_association = Tools::getValue("faq_category_association");


            $data_validation_title = array();
            $data_validation_content = array();

            foreach ($languages as $language){
                $id_lang = $language['id_lang'];
                $title = Tools::getValue("title_".$id_lang);
                $content = Tools::getValue("content_".$id_lang);

                if(Tools::strlen($title)>0 && Tools::strlen($content)>0 && !empty($faq_shop_association))
                {
                    $data_title_content_lang[$id_lang] = array('title' => $title,
                                                                'content' => $content
                    );

                    $data_validation_title[$id_lang] = $title;
                    $data_validation_content[$id_lang] = $content;
                }
            }

            $data = array( 'data_title_content_lang'=>$data_title_content_lang,
                            'item_status' => $faq_item_status,
                            'faq_shop_association' => $faq_shop_association,
                            'is_by_customer' => $is_by_customer,
                            'faq_customer_name' => $faq_customer_name,
                            'faq_customer_email'=>$faq_customer_email,
                            'faq_category_association'=>$faq_category_association,
                            'time_add' => $time_add,

            );


            if(sizeof($data_validation_title)==0)
                $this->errors[] = Tools::displayError('Please fill the Title');
            if(sizeof($data_validation_content)==0)
                $this->errors[] = Tools::displayError('Please fill the Content');
            if(!($faq_shop_association))
                $this->errors[] = Tools::displayError('Please select the Shop');
            if(!$time_add)
                $this->errors[] = Tools::displayError('Please select Date Add');


            if (empty($this->errors)) {

                $blockfaqhelp_obj->saveItem($data);

                Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . Tools::getAdminTokenLite($this->_name_controller));
            } else {
                $this->display = 'add';
                return FALSE;
            }
            ## add item ##

        } elseif(Tools::isSubmit('update_item')) {
            $id = Tools::getValue('id');
            ## update item ##

            $time_add = Tools::getValue("time_add");

            $languages = Language::getLanguages(false);
            $data_title_content_lang = array();
            $faq_shop_association = Tools::getValue("cat_shop_association");

            $faq_item_status = Tools::getValue("status");
            $is_by_customer = Tools::getValue("is_by_customer");
            $faq_customer_email = Tools::getValue("faq_customer_email");
            $faq_customer_name = Tools::getValue("faq_customer_name");
            $faq_category_association = Tools::getValue("faq_category_association");

            $is_add_by_customer = Tools::getValue("is_add_by_customer");

            $data_validation_title = array();
            $data_validation_content = array();

            foreach ($languages as $language){
                $id_lang = $language['id_lang'];
                $title = Tools::getValue("title_".$id_lang);
                $content = Tools::getValue("content_".$id_lang);

                if(Tools::strlen($title)>0 && Tools::strlen($content)>0 && !empty($faq_shop_association))
                {
                    $data_title_content_lang[$id_lang] = array('title' => $title,
                                                                'content' => $content
                    );

                    $data_validation_title[$id_lang] = $title;
                    $data_validation_content[$id_lang] = $content;
                }
            }


            $data = array('data_title_content_lang'=>$data_title_content_lang,
                        'id' => $id,
                        'item_status' => $faq_item_status,
                        'faq_shop_association' => $faq_shop_association,
                        'is_by_customer' => $is_by_customer,
                        'faq_customer_name' => $faq_customer_name,
                        'faq_customer_email'=>$faq_customer_email,
                        'faq_category_association'=>$faq_category_association,
                        'is_add_by_customer' => $is_add_by_customer,
                        'time_add' => $time_add,
            );




            if(sizeof($data_validation_title)==0)
                $this->errors[] = Tools::displayError('Please fill the Title');
            if(sizeof($data_validation_content)==0)
                $this->errors[] = Tools::displayError('Please fill the Content');
            if(!($faq_shop_association))
                $this->errors[] = Tools::displayError('Please select the Shop');
            if(!$time_add)
                $this->errors[] = Tools::displayError('Please select Date Add');



            if (empty($this->errors)) {

                $blockfaqhelp_obj->updateItem($data);

                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . Tools::getAdminTokenLite($this->_name_controller));
            }else{

                $this->display = 'add';
                return FALSE;
            }

            ## update item ##
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->_name_module)) {
            ### delete more than one  items ###
            if ($this->tabAccess['delete'] === '1') {
                if (Tools::getValue($this->list_id . 'Box')) {


                    $object = new $this->className();

                    if ($object->deleteSelection(Tools::getValue($this->list_id . 'Box'))) {
                        Tools::redirectAdmin(self::$currentIndex . '&conf=2' . '&token=' . $this->token);
                    }
                    $this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
                } else {
                    $this->errors[] = Tools::displayError('You must select at least one element to delete.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
            ### delete more than one  items ###
        } elseif (Tools::isSubmit('delete' . $this->_name_module)) {
            ## delete item ##

            $id = Tools::getValue('id');

            $blockfaqhelp_obj->deleteItem(array('id' => $id));

            Tools::redirectAdmin(self::$currentIndex . '&conf=1&token=' . Tools::getAdminTokenLite($this->_name_controller));
            ## delete item ##
        } else {
            return parent::postProcess(true);
        }




    }


    public function setMedia()
    {
        parent::setMedia();

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/'.$this->_name_module.'17.css');

        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/admin.js');

        $this->addJqueryUi(array('ui.core','ui.widget','ui.datepicker'));

    }


    public function renderForm()
    {
        if (!($this->loadObject(true)))
            return;

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'update';
        } else {
            $this->display = 'add';
        }


        $id = (int)Tools::getValue('id');

        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blockfaqhelp.class.php');
        $blockfaqhelp_obj = new blockfaqhelp();

        if($id) {

            $_data = $blockfaqhelp_obj->getItem(array('id'=>$id));

            $id_shop = isset($_data['item'][0]['ids_shops']) ? explode(",",$_data['item'][0]['ids_shops']) : array();
            $time_add = isset($_data['item'][0]['time_add']) ? $_data['item'][0]['time_add'] :'' ;
            $selected_data = isset($_data['item'][0]['faq_category_ids'])?$_data['item'][0]['faq_category_ids']:array();

            $is_add_by_customer = isset($_data['item'][0]['is_add_by_customer'])?$_data['item'][0]['is_add_by_customer']:0;

            $customer_email = isset($_data['item'][0]['customer_email'])?$_data['item'][0]['customer_email']:@Configuration::get('PS_SHOP_EMAIL');
            $customer_name = isset($_data['item'][0]['customer_name'])?$_data['item'][0]['customer_name']:$this->l('admin');


        } else {

            $id_shop = array();
            $time_add = date("Y-m-d H:i:s");
            $selected_data = array();
            $is_add_by_customer = 0;

            $customer_email = @Configuration::get('PS_SHOP_EMAIL');
            $customer_name = $this->l('admin');

        }


        $_data_cat = $blockfaqhelp_obj->getItemsCategory(array('admin'=>1));
        $faq_questions_association = $_data_cat['items'];



        if($id){
            $title_item_form = $this->l('Edit question:');
        } else{
            $title_item_form = $this->l('Add new question:');
        }




        $this->fields_form = array(
            'tinymce' => TRUE,
            'legend' => array(
                'title' => $title_item_form,
                //'icon' => 'fa fa-list fa-lg'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Question'),
                    'name' => 'title',
                    'id' => 'title',
                    'lang' => true,
                    'required' => TRUE,
                    'size' => 5000,
                    'maxlength' => 5000,
                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('Answer'),
                    'name' => 'content',
                    'id' => 'content',
                    'required' => TRUE,
                    'autoload_rte' => TRUE,
                    'lang' => TRUE,
                    'rows' => 8,
                    'cols' => 40,

                ),

                array(
                    'type' => 'related_items',
                    'label' => $this->l('Category association (optional)'),
                    'name' => 'faq_category_association',
                    'values'=>$faq_questions_association,
                    'selected_data'=>$selected_data,
                    'required' => false,
                    'name_field_custom'=>'faq_category_association',
                    'is_add_by_customer' => $is_add_by_customer,
                ),
                array(
                    'type' => 'cms_pages',
                    'label' => $this->l('Shop association'),
                    'name' => 'cat_shop_association',
                    'values'=>Shop::getShops(),
                    'selected_data'=>$id_shop,
                    'required' => TRUE,
                ),

                array(
                    'type' => 'item_date',
                    'label' => $this->l('Date Add'),
                    'name' => 'date_on',
                    'time_add' => $time_add,
                    'required' => TRUE,
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Show By customer'),
                    'name' => 'is_by_customer',
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => TRUE,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),

                array(
                    'type' => 'text_custom',
                    'label' => $this->l('Customer email'),
                    'name' => 'faq_customer_email',
                    'id' => 'faq_customer_email',
                    'lang' => false,
                    'required' => FALSE,
                    'value'=>$customer_email,
                ),

                array(
                    'type' => 'text_custom',
                    'label' => $this->l('Customer name'),
                    'name' => 'faq_customer_name',
                    'id' => 'faq_customer_name',
                    'lang' => false,
                    'required' => FALSE,
                    'value'=>$customer_name,
                ),


                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'status',
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => TRUE,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),


            ),


        );


        $this->fields_form['submit'] = array(
            'title' => ($id)?$this->l('Update'):$this->l('Save'),
        );




        if($id) {

            $this->tpl_form_vars = array(
                'fields_value' => $this->getConfigFieldsValuesForm(array('id'=>$id)),
            );

            $this->submit_action = 'update_item';
        } else {
            $this->submit_action = 'add_item';

        }



        return parent::renderForm();
    }



    public function getConfigFieldsValuesForm($data_in){



        $id = (int)Tools::getValue('id');
        if($id) {
            $id = $data_in['id'];
            require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blockfaqhelp.class.php');
            $blockfaqhelp_obj = new blockfaqhelp();
            $_data = $blockfaqhelp_obj->getItem(array('id'=>$id));


            $languages = Language::getLanguages(false);
            $fields_title = array();
            $fields_content = array();

            foreach ($languages as $lang)
            {
                $fields_title[$lang['id_lang']] = isset($_data['item']['data'][$lang['id_lang']]['title'])?$_data['item']['data'][$lang['id_lang']]['title']:'';
                $fields_content[$lang['id_lang']] = isset($_data['item']['data'][$lang['id_lang']]['content'])?$_data['item']['data'][$lang['id_lang']]['content']:'';
            }

            $status = isset($_data['item'][0]['status'])?$_data['item'][0]['status']:"";

            $is_by_customer = isset($_data['item'][0]['is_by_customer'])?$_data['item'][0]['is_by_customer']:0;






            $config_array = array(
                'title' => $fields_title,
                'status' => $status,
                'content'=>$fields_content,
                'is_by_customer' => $is_by_customer,

            );
        } else {
            $config_array = array();
        }
        return $config_array;
    }


    public function l($string , $class = NULL, $addslashes = false, $htmlentities = true){
        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::l($string);
        } else {
            $class = array();
            return Context::getContext()->getTranslator()->trans($string, $class, $addslashes, $htmlentities);
        }
    }

}





?>

