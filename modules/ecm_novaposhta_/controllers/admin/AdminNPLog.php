<?php
include_once(dirname(__FILE__).'/../../ecm_novaposhta.php');
class AdminNPLogController extends AdminController{
	public $log;
	public function __construct(){
		$this->bootstrap = true;
		parent::__construct();
	}

	public function readlog(){
		$file = fopen(_PS_MODULE_DIR_."ecm_novaposhta/log.txt", "r");
		$array= array();
		while(!feof($file)){
			$array[] = fgets($file);
		}
		fclose($file);
		return $array;
	}

	public function renderList(){
		$this->module = new ecm_novaposhta();
		$path = _MODULE_DIR_."ecm_novaposhta";
		$this->context->smarty->assign('log',$this->readlog());
		$more = $this->module->display($path, 'views/log.tpl');
		return $more.parent::renderList();
	}
}

