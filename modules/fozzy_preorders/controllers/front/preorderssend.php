<?php

class fozzy_preorderspreorderssendModuleFrontController extends ModuleFrontController
{
    public function init()
    {
		parent::init();

		if(!isset($_GET['send']))
			die();

		$this->module->sendFileToEmail();
    }
}