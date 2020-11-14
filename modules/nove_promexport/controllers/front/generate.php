<?php
class nove_promexportgenerateModuleFrontController extends ModuleFrontController
{
    public $display_header = false;
    public $display_column_left = false;
    public $display_column_right = false;
    public $display_footer = false;
    public $ssl = false;

    public function postProcess()
    {
        parent::postProcess();

        $this->module->generate(Tools::getValue('cron'));
    }
}