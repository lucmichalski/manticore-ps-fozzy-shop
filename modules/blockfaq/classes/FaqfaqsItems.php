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

class FaqfaqsItems extends ObjectModel
{


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'faq_item',
        'primary' => 'id',

    );




    public function deleteSelection($selection)
    {
        foreach ($selection as $value) {
            $obj = new FaqfaqsItems($value);
            if (!$obj->delete()) {
                return false;
            }
        }
        return true;
    }

    public function delete()
    {
        $return = false;

        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {

            require_once(_PS_MODULE_DIR_ . 'blockfaq/classes/blockfaqhelp.class.php');
            $blockfaqhelp = new blockfaqhelp();
            $blockfaqhelp->deleteItem(array('id'=>(int)$this->id));


            $return = true;
        }
        return $return;
    }


    
}
?>
