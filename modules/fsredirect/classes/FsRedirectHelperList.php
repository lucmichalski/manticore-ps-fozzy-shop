<?php
/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class FsRedirectHelperList extends HelperList
{
    public function displayConverttoredirectLink($token, $id, $name)
    {
        if (Module::isEnabled('fsredirect')) {
            $fsredirect = Module::getInstanceByName('fsredirect');
            return $fsredirect->displayConverttoredirectLink($token, $id, $name);
        }
        return '';
    }
}
