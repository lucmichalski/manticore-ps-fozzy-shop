<?php
//НоваПошта
if (!defined('_PS_VERSION_'))
	exit;

class ecm_changecourier extends Module {
	const PREFIX = 'ecm_';
	private $_last_updated = '';

	protected $_hooks = array(
		'displayAdminOrderTabShip',
		);

	public
	function __construct() {

		$this->name = 'ecm_changecourier';
		$this->tab = 'shipping_logistics';
		$this->version = '0.6';
		$this->author = 'Elcommerce';
		$this->bootstrap = TRUE;
		parent::__construct();
		$this->displayName = $this->l('Change courier');
		$this->description = $this->l('Change courier on admin order page');
		$this->ps_versions_compliancy = array(
			'min' => '1.5',
			'max' => _PS_VERSION_
		);
	}

	public
	function install() {

		if (parent::install()) {
			foreach ($this->_hooks as $hook) {
				if (!$this->registerHook($hook)) {
					return FALSE;
				}
			}
			return TRUE;
		}
		return FALSE;
	}

	public
	function uninstall() {
		if (parent::uninstall()) {
			foreach ($this->_hooks as $hook) {
				if (!$this->unregisterHook($hook)) {
					return FALSE;
				}
			}
			return TRUE;
		}
		return FALSE;
	}

	private
	function _displayabout() {
		$this->_html = '
        <div class="panel"><div class="panel-heading"><i class="icon-info"></i> '.$this->l('Information').'</div>
        <span><b>' . $this->l('Version') . ':</b> ' . $this->version . '</span><br>
        <span><b>' . $this->l('Developer') . ':</b> <a class="link" href="mailto:admin@elcommerce.com.ua" target="_blank">Mice  </a>
        <span><b>' . $this->l('Decription') . ':</b> <a class="link" href="http://elcommerce.com.ua" target="_blank">ElCommerce.com.ua</a><br>
        <br><p style="text-align:center"><a href="http://elcommerce.com.ua/" target="_blank"><img src="http://elcommerce.com.ua/img/m/logo.png" alt="Электронный учет коммерческой деятельности" /></a>
        </div>
        ';

	}



	public
	function getContent() {
		global $cookie;
		$this->_displayabout();
		return $this->_html;
	}


	public
	function hookdisplayAdminOrderTabShip($params) {
		global $cookie;
		$this->context->smarty->assign(array(
			'id_order' => $params['order']->id,
		));
		return $this->display(__FILE__, 'displayAdminOrder.tpl');
	}

	

}
