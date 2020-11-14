<?php
/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

abstract class Controller extends ControllerCore
{
    public function __construct()
    {
        parent::__construct();
        if (Module::isEnabled('fsredirect')) {
            if (in_array($this->controller_type, array('modulefront', 'front'))) {
                $fsredirect = Module::getInstanceByName('fsredirect');
                $fsredirect->redirect();
            }
        }
    }
}
