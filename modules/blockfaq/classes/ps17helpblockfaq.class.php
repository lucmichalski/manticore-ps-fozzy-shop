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
 * @category social_networks
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

class ps17helpblockfaq {
    private $_name = 'blockfaq';

    public function setMissedVariables(){
        $smarty = Context::getContext()->smarty;



        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            $custom_ssl_var = 1;

        if ($custom_ssl_var == 1)
            $base_dir_ssl = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        else
            $base_dir_ssl = _PS_BASE_URL_.__PS_BASE_URI__;

        $smarty->assign('base_dir_ssl' , $base_dir_ssl);


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $smarty->assign($this->_name.'is17' , 1);
        }

    }
}