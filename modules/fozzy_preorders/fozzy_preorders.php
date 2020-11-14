<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Fozzy_preorders extends Module {
	public function __construct()
    {
        $this->name = 'fozzy_preorders';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Rudyk M.';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Pre-orders');
        $this->description = $this->l('Pre-orders for Fozzy');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Функция установки модуля.
     * Module installation function.
     * @return bool
     */
    public function install() {
        include dirname(__FILE__) . '/sql/install.php';
        return parent::install();
    }

    /**
     * Функция удаления модуля.
     * Module removal function.
     * @return bool
     */
    public function uninstall() {
        include dirname(__FILE__) . '/sql/uninstall.php';
        return parent::uninstall();
    }


    public function getContent() {
    	$output = '';
        $output .= '<h2>'.$this->displayName.'</h2>';
        $cookie = $this->context->cookie;
        $currentIndex = $this->context->currentindex;
        $this->context->controller->addJS($this->_path.'views/js/admin.js');

        //Section for adding articles.\\
        if(!Tools::getValue('id_vendor_code')) {
            //Add new vendore code.
            if (Tools::isSubmit('submitLinkAdd')) {
                if ($this->addLink()) {
                	$output .= $this->displayConfirmation($this->l('The vendor code has been added.'));
                } else {
            		$output .= $this->displayError($this->l('An error occurred during vendor code creation.'));
        		}
            }
        } else {
            //Update vendore code.
            if (Tools::isSubmit('submitLinkAdd')) {
                if (!$this->updateLink()) {
                    $output .= $this->displayError($this->l('An error occurred during vendor code updating.'));
                } else {
                    $output .= $this->displayConfirmation($this->l('The block vendor code has been updated.'));
                }
            }
        }

        //Delete vendore code.
        if (Tools::isSubmit('deletefozzy_preorders') && Tools::getValue('id_vendorcode')) {
            if (is_numeric($_GET['id_vendorcode']) && $this->deleteLink()) {
                $output .= $this->displayConfirmation($this->l('The vendor code has been deleted.'));
            }
            else {
                $output .= $this->displayError($this->l('An error occurred during vendor code deletion.'));
            }
        }

        //Add branches section.\\
        if(!Tools::getValue('id_email')) {
            //Add new filial.
            if (Tools::isSubmit('submitLinkAddSettings')) {
                if ($this->addLinkSettings()) {
                	$url = $currentIndex . '&fozzy_preorders_addsettings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee));
                	Tools::redirectAdmin($url);
                } else {
            		$output .= $this->displayError($this->l('An error occurred during email creation.'));
        		}
            }
        } else {
            //Update filial.
            if (Tools::isSubmit('submitLinkAddSettings')) {
                if (!$this->updateLinkSettings()) {
                    $output .= $this->displayError($this->l('An error occurred during filial updating.'));
                } else {
                	$url = $currentIndex . '&fozzy_preorders_settings=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($cookie->id_employee));
                	Tools::redirectAdmin($url);
                }
            }
        }

        //Delete filial.
        if (Tools::isSubmit('deletetable_settings') && Tools::getValue('id_email')) {
            if (is_numeric($_GET['id_email']) && $this->deleteLinkSettings()) {
                $output .= $this->displayConfirmation($this->l('The filial has been deleted.'));
            }
            else {
                $output .= $this->displayError($this->l('An error occurred during filial deletion.'));
            }
        }

        $fozzy_preorders_settings = Tools::getValue("fozzy_preorders_settings");
        if (Tools::strlen($fozzy_preorders_settings) > 0) {
        	$output .= '<script>fozzy_preorders_init_tabs();</script>';
        	$output .= $this->displayConfirmation($this->l('The block filial has been updated.'));
        } elseif (Tools::isSubmit('updatetable_settings')) {
        	$output .= '<script>fozzy_preorders_init_tabs();</script>';
        } elseif (Tools::getValue("fozzy_preorders_addsettings") > 0) {
        	$output .= '<script>fozzy_preorders_init_tabs();</script>';
      		$output .= $this->displayConfirmation($this->l('The email has been added.'));
        }

         //Resetting the filter.
        if (Tools::isSubmit('submitResetfozzy_preorders')) {
            foreach($_POST as $name_filter => $value_filter) {
              if (strpos($name_filter, 'fozzy_preordersFilter_') !== false)
                $_POST[$name_filter] = NULL;
            }
        }

        $output .= $this->_displayFormMenu();
        return $output;
    }

    /**
	 * Отображения верхнего меню.
     * Top menu display.
	 */
    public function _displayFormMenu($persona = array(), $edit = 0, $persona_driver = array(), $edit_driver = 0) {
        $this->output .= '<div class="row">
    	<div class="col-lg-12">
    	<div class="row">';

        $this->output .= '<div class="col-lg-12 col-md-3">

						<div class="list-group">';
        $this->output .= '<ul class="nav nav-pills" id="navtabs16">
							    <li class="active"><a href="#filial" data-toggle="tab" class="list-group-item"><i class="fa fa-home fa-lg"></i>&nbsp;'.$this->l('Vendor code all').'</a></li>
							    <li><a href="#settings" data-toggle="tab" class="list-group-item"><i class="fa fa-cogs fa-lg"></i>&nbsp;'.$this->l('Send settings').'</a></li>
							</ul>';
        $this->output .= '</div>
    				</div>';

        $this->output .= '<div class="tab-content col-lg-12 col-md-9">';
        $this->output .= '<div class="tab-pane active" id="filial">'.$this->renderAddForm().' '.$this->renderList().'</div>';
        $this->output .= '<div class="tab-pane" id="settings">'.$this->renderAddFormSettings().''.$this->renderListSettings().'</div>';
        $this->output .= '</div>';

        $this->output .= '</div></div></div>';
        return $this->output;
    }
	
	/**
	 * Форма добавление артикулов.
     * Adding form vendor code.
	 */
    protected function renderAddForm() {
    	$title = $this->l('Add a new vendor code');
        $icon = 'icon-plus-sign-alt';
        $button = $this->l('Save');

        if (Tools::getValue('id_vendorcode') && !Tools::isSubmit('deletefozzy_preorders')) {
            $title = $this->l('Edit a vendor code');
            $icon = 'icon-cog';
            $button = $this->l('Edit');
        }

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => $icon
                ),

                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_vendor_code',
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Vendor code'),
                        'name' => 'to_vendor_code',
                        'required' => true
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Name product'),
                        'name' => 'name_product',
                        'required' => true
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Unit'),
                        'name' => 'to_unit',
                        'required' => true
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Department'),
                        'name' => 'to_department',
                        'required' => true
                    ),
                   
                    array(
                        'col' => 2,
                        'rows' => 2,
                        'type' => 'text',
                        'label' => $this->l('Comment'),
                        'name' => 'to_comment',
                    ),
                ),

                'submit' => array(
                    'title' => $button,
                    'name' => 'submitLinkAdd',
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->identifier = 'id_vendor_code';
        $helper->submit_action = 'submit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues()
        );
        return $helper->generateForm(array($fields_form_1));
    }

    /**
	 * Добавление артикулов.
     * Adding vendor code.
	 */
	public function addLink() {
		$sql = "SELECT `ps_fozzy_preorders_vendorcode`.* FROM `ps_fozzy_preorders_vendorcode` WHERE `ps_fozzy_preorders_vendorcode`.`id_vendorcode` = ".$_POST['to_vendor_code'];
        $double_vendor_code = Db::getInstance()->executeS($sql);

        if(empty($double_vendor_code)) {
			if (!empty($_POST['to_vendor_code']) && !empty($_POST['name_product']) && !empty($_POST['to_unit']) && !empty($_POST['to_department'])) {
				$vendorcode = $_POST['to_vendor_code'];
				$name_product = $_POST['name_product'];
				$unit_product = $_POST['to_unit'];
				$department = $_POST['to_department'];
				$comment_vendorcode = $_POST['to_comment'];

				$sql = "INSERT INTO `ps_fozzy_preorders_vendorcode` (`vendorcode`, `name_product`, `unit_product`, `department`, `comment_vendorcode`) VALUES ('$vendorcode', '$name_product', '$unit_product', '$department', '$comment_vendorcode')";
				Db::getInstance()->execute($sql);

				return true;
			} else {
				return false;
			} 
		} else {
				return false;
		}   	
	}

	/**
     * Обновление артикулов.
     * Updating vendore code.
     */
    public function updateLink() {
        if (!empty($_POST)) {
            $id_vendorcode = $_POST['id_vendor_code'];
            $vendorcode = $_POST['to_vendor_code'];
            $name_product = $_POST['name_product'];
            $unit_product = $_POST['to_unit'];
            $department = $_POST['to_department'];
            $comment_vendorcode = $_POST['to_comment'];

            return (Db::getInstance()->execute("UPDATE `ps_fozzy_preorders_vendorcode` SET `vendorcode` = '$vendorcode' ,`name_product` = '$name_product', `unit_product` = '$unit_product', `department` = '$department', `comment_vendorcode` = '$comment_vendorcode' WHERE `id_vendorcode` = '$id_vendorcode'"));
        } else {
            return false;
        }
    }

    /**
     * Удаление артикулов.
     * Removing articles.
     */
    public function deleteLink() {
        return (Db::getInstance()->execute("DELETE FROM `ps_fozzy_preorders_vendorcode` WHERE `id_vendorcode` = ".(int)$_GET['id_vendorcode']));
    }

	/**
	 * Отображение списка артикулов.
     * Adding vendor code.
	 */
    protected function renderList() {
		$list_vendorecode = array(
            'id_vendorcode' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'search' => false,
                'class' => 'fixed-width-xs'
            ),
			'vendorcode' => array(
				'title' => $this->l('Vendor code'),
				'type' => 'text',
				'search' => true,
                'class' => 'fixed-width-xs'
			),
			'name_product' => array(
				'title' => $this->l('Name product'),
				'type' => 'text',
				'search' => true,
                'class' => 'fixed-width-xs'
			),
			'unit_product' => array(
				'title' => $this->l('Unit of measurement'),
				'type' => 'text',
				'search' => false,
                'class' => 'fixed-width-xs'
			),
			'department' => array(
				'title' => $this->l('Department'),
				'type' => 'text',
				'search' => true,
                'class' => 'fixed-width-xs'
			),
			'comment_vendorcode' => array( 
				'title' => $this->l('Comment'),
				'type' => 'text',
				'search' => false,
                'class' => 'fixed-width-xs'
			),
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = false;
		$helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
		$helper->identifier = 'id_vendorcode';
		$helper->actions = array('edit','delete');

		$helper->title = $this->l('Vendor code list');
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->_pagination = array(10, 20, 50, 100, 200);
        $content = $this->getLinks($id, $_POST);
        $helper->listTotal = count($content);

        /* Paginate the result */
        $page = ($page = Tools::getValue( 'submitFilter' . $helper->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue( $helper->table . '_pagination')) ? $pagination : 50;

        $content = $this->paginate_content($content, $page, $pagination);

		return $helper->generateList($content, $list_vendorecode);
	}

	public function paginate_content($content, $page = 1, $pagination = 50) {
        if( count($content) > $pagination) {
            $content = array_slice($content, $pagination * ($page - 1), $pagination);
        }

        return $content;
    }

	/**
     * Добавление данных в форму при редактировании артикула.
     * Adding data to the form when editing the article.
     */
    public function getConfigFieldsValues() {
        if (Tools::getValue('id_vendorcode')) {
            $links = $this->getLinks((int)Tools::getValue('id_vendorcode'));

            $id_vendorcode =  $links[0]['id_vendorcode'];
            $vendorcode =  $links[0]['vendorcode'];
            $name_product = $links[0]['name_product'];
            $unit_product = $links[0]['unit_product'];
            $department = $links[0]['department'];
            $comment_vendorcode = $links[0]['comment_vendorcode'];

            $fields_values = array(
            'id_vendor_code'  => $id_vendorcode,
            'to_vendor_code' => $vendorcode,
            'name_product' => $name_product,
            'to_unit' => $unit_product,
            'to_department' => $department,
            'to_comment' => $comment_vendorcode,
        	);
        } else {
            return;
        }
        
        return $fields_values;
    }


	/**
     * Получение списка артикулов.
     * Getting a list of vendore code.
     */
    public function getLinks($id = 0, $filter = array()) {
        if(empty($id)) {
            $sql = "SELECT `ps_fozzy_preorders_vendorcode`.* FROM `ps_fozzy_preorders_vendorcode` ";
            if(is_array($filter) && count($filter) > 1 && Tools::isSubmit('submitFilterfozzy_preorders') && !array_key_exists('submitResetfozzy_preorders', $filter)) {
	            $i = 0;
	            foreach($filter as $name_filter => $value_filter) {
	                if (strpos($name_filter, 'fozzy_preordersFilter_') !== false) {
	                    $name_filter = str_replace('fozzy_preordersFilter_', '', $name_filter);
	                    if($i == 0)
	                        $sql .= " WHERE `$name_filter` LIKE '%$value_filter%'";
	                    else
	                        $sql .= " AND `$name_filter` LIKE '%$value_filter%'";
	                    $i++;
	                	}
	            	}
        	} else {
            	$sql .= " WHERE 1";
        	}

            $sql .= " ORDER BY  `ps_fozzy_preorders_vendorcode`.`id_vendorcode` ASC";
            $links = Db::getInstance()->executeS($sql);
        } else {
            $sql = "SELECT `ps_fozzy_preorders_vendorcode`.* FROM `ps_fozzy_preorders_vendorcode` WHERE `id_vendorcode` = ".$id." ORDER BY  `ps_fozzy_preorders_vendorcode`.`id_vendorcode` ASC";
            $links = Db::getInstance()->executeS($sql);
        }

        return $links;
    }

    /**
     * Форма привязки почты к городу.
     * Form of binding mail to the city.
     */
	protected function renderAddFormSettings(){
    	$title = $this->l('Add a new settings');
        $icon = 'icon-plus-sign-alt';
        $button = $this->l('Save');

        //Sending an array with assembly zones.
        $sql = "SELECT `ps_fozzy_preorders_zone`.* FROM `ps_fozzy_preorders_zone` WHERE 1 ORDER BY  `ps_fozzy_preorders_zone`.`id_shop` ASC";
        $shoplist = Db::getInstance()->executeS($sql);

        if (Tools::getValue('id_email') && !Tools::isSubmit('deletefozzy_preorder')) {
            $title = $this->l('Edit a settings');
            $icon = 'icon-cog';
            $button = $this->l('Edit');
        }

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => $icon
                ),

                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_email',
                    ),
                    array(
                        'col' => 3,
                        'type' => 'select',
                        'name' => 'shop_name',
                        'label' => $this->l('Shop name'),
                        'required' => true,
                        'options' => array(
                            'query' => $shoplist,
                            'id' => 'id_shop',
                            'name' => 'shop_name'
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'label' => $this->l('Email'),
                        'name' => 'email',
                        'required' => true
                    ),
                ),

                'submit' => array(
                    'title' => $button,
                    'name' => 'submitLinkAddSettings',
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->identifier = 'id_email';
        $helper->submit_action = 'submit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValuesSettings()
        );
        return $helper->generateForm(array($fields_form_1));
    }

    /**
	 * Привязка почты к филиалу.
     * Adding vendor code.
	 */
	public function addLinkSettings() {
		if (!empty($_POST['shop_name']) && !empty($_POST['email'])) {
			$shop_name = $_POST['shop_name'];
			$email = $_POST['email'];

			$sql = "INSERT INTO `ps_fozzy_preorders_settings` (`id_shop`, `email`) VALUES ('$shop_name', '$email')";

			Db::getInstance()->execute($sql);
			return true;
		} else {
			return false;
		}    	
	}

	/**
     * Обновление филиалов.
     * Updating filial.
     */
    public function updateLinkSettings() {
        if (!empty($_POST)) {
            $id_email = $_POST['id_email'];
            $id_shop = $_POST['shop_name'];
            $email = $_POST['email'];

            return (Db::getInstance()->execute("UPDATE `ps_fozzy_preorders_settings` SET `id_shop` = '$id_shop' ,`email` = '$email' WHERE `id_email` = '$id_email'"));
        } else {
            return false;
        }
    }

    /**
     * Удаление филиала.
     * Removing filial.
     */
    public function deleteLinkSettings() {
        return (Db::getInstance()->execute("DELETE FROM `ps_fozzy_preorders_settings` WHERE `id_email` = ".(int)$_GET['id_email']));
    }

    protected function renderListSettings() {
		$list_print = array(
            'id_email' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'class' => 'fixed-width-xs'
            ),
			'shop_name' => array(
				'title' => $this->l('Shop name'),
				'type' => 'text',
                'class' => 'fixed-width-xs'
			),
			'email' => array(
				'title' => $this->l('Email'),
				'type' => 'text',
                'class' => 'fixed-width-xs'
			),
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_email';
		$helper->actions = array('edit','delete');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Settings list');
		$helper->table = 'table_settings';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $links = $this->getLinksSettings();
        
		return $helper->generateList($links, $list_print);
	}

	/**
     * Получение списка филиал.
     * Getting a list of filial.
     */
    public function getLinksSettings($id = 0) {
        if(empty($id)) {
            $sql = "SELECT `ps_fozzy_preorders_settings`.*, `ps_fozzy_preorders_zone`.`shop_name` FROM `ps_fozzy_preorders_settings`";
            $sql .= ' LEFT JOIN `ps_fozzy_preorders_zone` ON `ps_fozzy_preorders_zone`.`id_shop` = `ps_fozzy_preorders_settings`.`id_shop`';
            $sql .= " WHERE 1";
            $sql .= ' ORDER BY `ps_fozzy_preorders_settings`.`id_email` ASC';
            $links = Db::getInstance()->executeS($sql);
        } else {
            $sql = "SELECT `ps_fozzy_preorders_settings`.* FROM `ps_fozzy_preorders_settings` WHERE `id_email` = ".$id." ORDER BY  `ps_fozzy_preorders_settings`.`id_email` ASC";
            $links = Db::getInstance()->executeS($sql);
        }

        return $links;
    }

    /**
     * Добавление данных в форму при редактировании филиала.
     * Adding data to the form when editing the filial.
     */
    public function getConfigFieldsValuesSettings() {
        if (Tools::getValue('id_email')) {
            $links = $this->getLinksSettings((int)Tools::getValue('id_email'));

            $id_email = $links[0]['id_email'];
            $id_shop =  $links[0]['id_shop'];
            $email =  $links[0]['email'];

            $fields_values = array(
            'id_email' => $id_email,
            'shop_name'  => $id_shop,
            'email' => $email,
        	);
        } else {
            return;
        }
       
        return $fields_values;
    }

    /**
     * Отправка файлов с предварительным заказом на почту.
     * Sending files with pre-order by mail.
     */
    public function sendFileToEmail() {
    	//Branches to which an email with a pre-order file will be sent.      
        $sql = "SELECT `ps_fozzy_preorders_zone`.* FROM `ps_fozzy_preorders_zone` WHERE 1 ORDER BY  `ps_fozzy_preorders_zone`.`id_shop` ASC";
        $filial = Db::getInstance()->executeS($sql);

    	foreach ($filial as $value) {
    		//Pre-order the next day.
	        $date = date("Y-m-d");
			$date_tomorrow = str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d',strtotime($date_tomorrow . "+1 days"));
			
	        $sql = "SELECT `ps_orders`.`zone_name`, `ps_orders`.`id_order`, `ps_orders`.`dateofdelivery`, CONCAT(`ps_nove_dateofdelivery`.`timefrom`, ' - ', `ps_nove_dateofdelivery`.`timeto`) as `period`, `ps_fozzy_preorders_vendorcode`.`department`, `ps_order_detail`.`product_reference`, `ps_order_detail`.`product_name`, `ps_fozzy_preorders_vendorcode`.`unit_product`, FORMAT(`ps_order_detail`.`product_quantity`, 3) as `quantity` FROM `ps_orders` ";
	        $sql .= "LEFT JOIN `ps_order_detail` ON `ps_order_detail`.`id_order` = `ps_orders`.`id_order`";
	        $sql .= " LEFT JOIN `ps_nove_dateofdelivery_cart` ON `ps_nove_dateofdelivery_cart`.`cart_id` = `ps_orders`.`id_cart`";
	        $sql .= " LEFT JOIN `ps_fozzy_preorders_vendorcode` ON `ps_fozzy_preorders_vendorcode`.`vendorcode` = `ps_order_detail`.`product_reference`";
	        $sql .= " LEFT JOIN `ps_nove_dateofdelivery` ON `ps_nove_dateofdelivery`.`id_period` = `ps_nove_dateofdelivery_cart`.`period`";
	        $sql .= " WHERE `ps_orders`.`current_state` != 6 AND `ps_orders`.`dateofdelivery` = '".$tomorrow."' AND `ps_orders`.`zone` = '".$value['id_shop']."' AND (`ps_nove_dateofdelivery`.`timefrom` = '10:00:00' AND `ps_nove_dateofdelivery`.`timeto` = '12:00:00' OR `ps_nove_dateofdelivery`.`timefrom` = '12:00:00' AND `ps_nove_dateofdelivery`.`timeto` = '14:00:00')";
	        $preorders = Db::getInstance()->executeS($sql);
	        $arr_preorders = count($preorders);

	        //if(!empty($preorders)) {
	        //Article base.
	        $sql = "SELECT `ps_fozzy_preorders_vendorcode`.* FROM `ps_fozzy_preorders_vendorcode` WHERE 1 ORDER BY  `ps_fozzy_preorders_vendorcode`.`id_vendorcode` ASC";
	        $vendorcode = Db::getInstance()->executeS($sql);
	        $data_vendorcode = count($vendorcode);

	        $data_preorder = array();
	        for ($j = 0; $j < $data_vendorcode; $j++) {
	        	for ($g = 0; $g < $arr_preorders; $g++) { 
	        		if($vendorcode[$j]['vendorcode'] == $preorders[$g]['product_reference']) {
	        			array_push($data_preorder, $preorders[$g]);
	        		}
	        	}
	        }
	        
	        $response_csv_data = $data_preorder;
	        $filename = $value['shop_name'];

            $sql = "SELECT `ps_fozzy_preorders_settings`.`email` FROM `ps_fozzy_preorders_settings` WHERE `ps_fozzy_preorders_settings`.`id_shop` = ". $value['id_shop'];
            $arr_email_send = Db::getInstance()->executeS($sql);
            $result = [];
            array_walk_recursive($arr_email_send, function ($item, $key) use (&$result) {
                $result[] = $item;    
            });
	        $email_send = $result;
	        $this->export_data_to_csv($response_csv_data, $filename, $email_send);
	        //}
    	}
    	die;
    }

    /**
     * Экспорт списка артикулов в csv файл.
     * Export a list of vendore code to a csv file.
     */
    function export_data_to_csv($data, $filename='export', $email_send, $delimiter = ';', $enclosure = '"') {
        // I open PHP memory as a file
        $fp = fopen(_PS_MODULE_DIR_."fozzy_preorders/".$filename.".csv", 'w');

        // Insert the UTF-8 BOM in the file
        fputs($fp, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // I add the array keys as CSV headers
        $headerss = array('Филиал', '№ Заказа', 'Дата доставки', 'Окна доставки', 'Отдел', 'Артикул', 'Наименование', 'Ед изм', 'Кол-во');
        fputcsv($fp, $headerss, $delimiter, $enclosure);

        // Add all the data in the file
        foreach ($data as $fields) {
            fputcsv($fp, $fields, $delimiter, $enclosure);
        }

        // Close the file
        fclose($fp);

        $cont = file_get_contents(_PS_MODULE_DIR_."fozzy_preorders/".$filename.".csv");
        unlink(_PS_MODULE_DIR_."fozzy_preorders/".$filename.".csv");
        $file_attachement['content'] = $cont;
        $file_attachement['name'] = $filename.".csv";
        $file_attachement['mime'] = 'application/csv';        
        $data = [];

        Mail::Send(
          (int) $this->context->language->id,
          'preorder_message',  
          $filename.'. '.$this->l('Selection for tomorrow'),
          $data,
          $email_send,
          null,
          null,
          null,
          $file_attachement,
          null,
          _PS_MAIL_DIR_,
          false,
          (int) $this->context->shop->id
        );
    }
}