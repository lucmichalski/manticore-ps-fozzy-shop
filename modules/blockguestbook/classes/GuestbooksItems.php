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
 * @package blockguestbook
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

class GuestbooksItems extends ObjectModel
{


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'blockguestbook',
        'primary' => 'id',

    );




    public function deleteSelection($selection)
    {
        foreach ($selection as $value) {
            $obj = new GuestbooksItems($value);
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

            require_once(_PS_MODULE_DIR_ . 'blockguestbook/classes/guestbook.class.php');
            $guestbook = new guestbook();
            $guestbook->delete(array('id'=>(int)$this->id));


            $return = true;
        }
        return $return;
    }


    
}
?>
