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

class FaqcategoriesItems extends ObjectModel
{

    /** @var string Name */
    public $id;
    public $ids_shops;
    public $status;
    public $time_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'faq_category',
        'primary' => 'id',
        'fields' => array(
            'id' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedInt','required' => true,),

            'ids_shops' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),
            'order_by' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),

            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'time_add' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),


        ),

    );




    public function deleteSelection($selection)
    {
        foreach ($selection as $value) {
            $obj = new FaqcategoriesItems($value);
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
            $blockfaqhelp->deleteItemCategory(array('id'=>(int)$this->id));


            $return = true;
        }
        return $return;
    }





}
?>
