<?php
if (!defined('_PS_VERSION_'))
    exit;

class AdminNPMainController extends AdminController {
    public function __construct() {
        parent::__construct();
        $this->lang = false;
        $this->context = Context::getContext();
        if (!Tools::redirectAdmin('index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure=ecm_novaposhta&tab_module=shipping_logistics&module_name=ecm_novaposhta'))
            return false;
        return true;
    }
}

?>
