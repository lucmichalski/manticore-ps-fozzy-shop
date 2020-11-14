<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkPwaHelper
{
    const _PWA_MODULE_NAME_ = 'wkpwa';              // getModuleNameFromClass(get_called_class())
    const _PWA_LOGO_NAME_ = 'wk-pwa-logo';
    const _PWA_FAVICON_NAME_ = 'wk-pwa-favicon';

    public static $logoSize = array('48', '72', '96', '144', '152', '168', '192', '256', '384', '512');
    public static $faviconSize = array('72');

    // NOTE:: For IOS
    // const _PWA_IOS_SPLASH_SCREEN_SIZE_ = 'wk-apple-launch';
    // public static $iosSplashScreenSize = array(
    //     '2048' => '2732',
    //     '1668' => '2224',
    //     '1536' => '2048',
    //     '1125' => '2436',
    //     '1242' => '2208',
    //     '750' => '1334',
    //     '640' => '1136'
    // );

    public static function getBaseDirUrl()
    {
        $forceSsl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $baseDirSsl = $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__;
        $baseDir = _PS_BASE_URL_.__PS_BASE_URI__;

        $startUrl = $forceSsl ? $baseDirSsl : $baseDir;
        return $startUrl;
    }

    public static function getIconsJson()
    {
        $startUrl = self::getBaseDirUrl();

        $icons = '';
        foreach (self::$logoSize as $size) {
            if ($icons) {
                $icons .= ', ';
            }

            $iconSrc = $startUrl.'modules/'.self::_PWA_MODULE_NAME_.'/views/img/appIcon/';
            $iconSrc .= self::_PWA_LOGO_NAME_.'-'.$size.'x'.$size.'.png';
            $icons .= '{
                "src": "'.$iconSrc.'",
                "sizes": "'.$size.'x'.$size.'",
                "type": "image/png"
            }';
        }

        foreach (self::$faviconSize as $size) {
            if ($icons) {
                $icons .= ', ';
            }

            $iconSrc = $startUrl.'modules/'.self::_PWA_MODULE_NAME_.'/views/img/appIcon/';
            $iconSrc .= self::_PWA_FAVICON_NAME_.'-'.$size.'x'.$size.'.png';
            $icons .= '{
                "src": "'.$iconSrc.'",
                "sizes": "'.$size.'x'.$size.'",
                "type": "image/png"
            }';
        }

        return $icons;
    }

    public static function generateManifestFile()
    {
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $startUrl = self::getBaseDirUrl();
            if ($languages = Language::getLanguages(true, Context::getContext()->shop->id)) {
                if (count($languages) > 1) {
                    $startUrl = self::getBaseDirUrl().Context::getContext()->language->iso_code.'/';
                }
            }
        } else {
            $startUrl = self::getBaseDirUrl().'index.php';
        }

        $icons = self::getIconsJson();

        $manifestContent = '{
            "name": "'.Configuration::get('WK_PWA_NAME').'",
            "short_name": "'.Configuration::get('WK_PWA_SHOT_NAME').'",
            "icons": ['.$icons.'],
            "start_url": "'.$startUrl.'",
            "display": "standalone",
            "gcm_sender_id": "'.Configuration::get('WK_PWA_SENDER_ID').'",
            "background_color": "'.Configuration::get('WK_PWA_BG_COLOR').'",
            "theme_color": "'.Configuration::get('WK_PWA_THEME_COLOR').'"
        }';

        // Write content in manifest file
        $myfile = fopen(_PS_MODULE_DIR_.self::_PWA_MODULE_NAME_.'/manifest.json', "w") or die("Unable to open file!");
        fwrite($myfile, $manifestContent);
        fclose($myfile);
        if (Module::isEnabled('wkpos')) {
            $startUrl = Context::getContext()->link->getModuleLink('wkpos', 'login');
            $manifestContent = '{
                "name": "'.Configuration::get('WK_PWA_NAME').' POS",
                "short_name": "'.Configuration::get('WK_PWA_SHOT_NAME').' POS",
                "icons": ['.$icons.'],
                "start_url": "'.$startUrl.'",
                "display": "standalone",
                "gcm_sender_id": "'.Configuration::get('WK_PWA_SENDER_ID').'",
                "background_color": "'.Configuration::get('WK_PWA_BG_COLOR').'",
                "theme_color": "'.Configuration::get('WK_PWA_THEME_COLOR').'"
            }';
            $myfile = fopen(_PS_MODULE_DIR_.'wkpos/manifest.json', "w") or die("Unable to open file!");
            fwrite($myfile, $manifestContent);
            fclose($myfile);
        }

        return true;
    }

    public static function generateAppLogo($fileSource)
    {
        if (!$fileSource) {
            return false;
        }

        return self::generateManifestImages($fileSource, self::_PWA_LOGO_NAME_, self::$logoSize);
    }

    public static function generateAppFavicon($fileSource)
    {
        if (!$fileSource) {
            return false;
        }

        return self::generateManifestImages($fileSource, self::_PWA_FAVICON_NAME_, self::$faviconSize);
    }

    public static function generateManifestImages($fileSource, $imgName, $imgSizes)
    {
        if (!$fileSource) {
            return false;
        }

        // delete Existing Img files
        self::deleteAppLogo($imgName);

        $imgFolderPath = self::_PWA_MODULE_NAME_.'/views/img/appIcon/';
        foreach ($imgSizes as $size) {
            $imgPath = _PS_MODULE_DIR_.$imgFolderPath.$imgName.'-'.$size.'x'.$size.'.png';
            ImageManager::resize($fileSource, $imgPath, $size, $size, 'png');
        }
        $imgPath = _PS_MODULE_DIR_.$imgFolderPath.$imgName.'_'.time().'.png';
        ImageManager::resize($fileSource, $imgPath);

        return true;
    }

    public static function generateIosSplashScreenImages($fileSource, $imgName, $screenSizes)
    {
        if (!$fileSource) {
            return false;
        }

        // delete Existing Img files
        self::deleteAppLogo($imgName);

        $imgFolderPath = self::_PWA_MODULE_NAME_.'/views/img/appIcon/';
        foreach ($screenSizes as $screenWidth => $screenHeight) {
            $imgPath = _PS_MODULE_DIR_.$imgFolderPath.$imgName.'-'.$screenWidth.'x'.$screenHeight.'.png';
            ImageManager::resize($fileSource, $imgPath, $screenWidth, $screenHeight, 'png');
        }

        return true;
    }

    public static function deleteAppLogo($fileName)
    {
        $imgFolderPath = self::_PWA_MODULE_NAME_.'/views/img/appIcon/';
        $allFiles = glob(_PS_MODULE_DIR_.$imgFolderPath.$fileName.'*'); // get all file names
        if (count($allFiles)) {
            foreach ($allFiles as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                }
            }
        }

        return true;
    }

    public static function getOriginalAppImgUrl($fileName)
    {
        $imgDir = self::_PWA_MODULE_NAME_.'/views/img/appIcon/';
        if ($imgExist = glob(_PS_MODULE_DIR_.$imgDir.$fileName.'_'.'*')) {
            $imgParts = explode("/", $imgExist[0]);
            $imgName = $imgParts[(count($imgParts) - 1)];

            return _MODULE_DIR_.$imgDir.$imgName;
        }

        return false;
    }

    public static function emptyNotificationIconFolder()
    {
        $imgFolderPath = self::_PWA_MODULE_NAME_.'/views/img/notificationIcon/';
        $allFiles = glob(_PS_MODULE_DIR_.$imgFolderPath.'*.png'); // get all file names
        if (count($allFiles)) {
            foreach ($allFiles as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                }
            }
        }

        return true;
    }

    public static function getCustomerOrdersTotalPaid($idCustomer, $idCurrency = 0, $withSign = 0)
    {
        $ordersTotalPaid = 0;
        if (!$idCurrency) {
            $idCurrency = Context::getContext()->currency->id;
        }
        if ($orders = Db::getInstance()->executeS(
            'SELECT `total_paid_tax_incl`, `id_currency`
            FROM `'._DB_PREFIX_.'orders`
            WHERE `id_customer` = '.(int)$idCustomer
        )) {
            foreach ($orders as $order) {
                if ($order['id_currency'] != $idCurrency) {
                    $ordersTotalPaid += Tools::convertPriceFull(
                        $order['total_paid_tax_incl'],
                        new Currency($order['id_currency']),
                        new Currency($idCurrency)
                    );
                } else {
                    $ordersTotalPaid += $order['total_paid_tax_incl'];
                }
            }
        }
        if ($withSign) {
            return Tools::displayPrice($ordersTotalPaid);
        }
        return $ordersTotalPaid;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $rand = '';

        for ($i = 0; $i < $length; ++$i) {
            $rand = $rand.$characters[mt_rand(0, Tools::strlen($characters) - 1)];
        }

        return $rand;
    }

    public static function getLibraryPath()
    {
        $versionArray = explode('.', phpversion());
        $majorVersion = (int)$versionArray[0];
        return _PS_MODULE_DIR_.'wkpwa/libs/php'.$majorVersion.'/web-push/vendor/autoload.php';
    }

    public function createWkPwaTables()
    {
        $response = true;
        if ($tableQueries = $this->getWkPwaTables()) {
            foreach ($tableQueries as $mpQuery) {
                $response &= Db::getInstance()->execute(trim($mpQuery));
                if (!$response) {
                    break;
                }
            }
        }

        return $response;
    }

    /**
     * Get browser.
     * Code copied from Guest.php
     *
     * @param string $userAgent
     */
    public function getIdBrowser()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if ($userAgent) {
            $browserArray = array(
                'Chrome' => 'Chrome/',
                'Safari' => 'Safari',
                'Safari iPad' => 'iPad',
                'Firefox' => 'Firefox/',
                'Opera' => 'Opera',
                'IE 11' => 'Trident',
                'IE 10' => 'MSIE 10',
                'IE 9' => 'MSIE 9',
                'IE 8' => 'MSIE 8',
                'IE 7' => 'MSIE 7',
                'IE 6' => 'MSIE 6',
            );
            foreach ($browserArray as $k => $value) {
                if (strstr($userAgent, $value)) {
                    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                    SELECT `id_web_browser`
                    FROM `' . _DB_PREFIX_ . 'web_browser` wb
                    WHERE wb.`name` = \'' . pSQL($k) . '\'');

                    return $result['id_web_browser'];
                }
            }
        }

        return null;
    }

    public function getWkPwaTables()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_pwa_push_notification_token` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_guest` int(11) NOT NULL,
                `id_customer` int(11) NOT NULL,
                `ip` text NOT NULL,
                `id_lang` int(11) NOT NULL,
                `id_web_browser` int(11) NOT NULL,
                `token` text NOT NULL,
                `endpoint` text NOT NULL,
                `user_public_key` text NOT NULL,
                `user_auth_token` text NOT NULL,
                `active` tinyint(1) NOT NULL,
                `expired` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_pwa_push_notification` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_notification_type` int(11) NOT NULL,
                `icon` text NOT NULL,
                `title` text NOT NULL,
                `body` text NOT NULL,
                `target_url` text NOT NULL,
                `customer_type` tinyint(2) NOT NULL,
                `customer_type_value` int(11) NOT NULL,
                `push_schedule` date NOT NULL,
                `remainder_count` int(11) NOT NULL,
                `remainder_interval` int(11) NOT NULL,
                `order_status` text NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_pwa_push_notification_history` (
                `id_push_notification_history` int(11) NOT NULL AUTO_INCREMENT,
                `id_notification_type` int(11) NOT NULL,
                `icon` text NOT NULL,
                `title` text NOT NULL,
                `body` text NOT NULL,
                `target_url` text NOT NULL,
                `customer_type` int(11) NOT NULL,
                `customer_type_value` int(11) NOT NULL,
                `id_element` int(11) NOT NULL,
                `remainder_left` int(11) NOT NULL,
                `remainder_interval` int(11) NOT NULL,
                `last_remainder_date` date NOT NULL,
                `delivered_count` int(11) NOT NULL,
                `clicked_count` int(11) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_push_notification_history`)
                ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wk_pwa_push_notification_type` (
                `id_notification_type` int(11) NOT NULL AUTO_INCREMENT,
                `name` text NOT NULL,
                `active` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_notification_type`)
                ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
            "INSERT INTO `"._DB_PREFIX_."wk_pwa_push_notification_type`
                (`id_notification_type`, `name`, `active`, `date_add`, `date_upd`) VALUES
                (1,	'Manual Notification',	1,	'CURDATE()',	'CURDATE()'),
                (2,	'Product Notification',	0,	'CURDATE()',	'CURDATE()'),
                (3,	'Order Status Notification',	0,	'CURDATE()',	'CURDATE()'),
                (4,	'Welcome Notification',	0,	'CURDATE()',	'CURDATE()'),
                (5,	'Cart Reminder Notification',	0,	'CURDATE()',	'CURDATE()')",
        );
    }

    public function deleteWkPwaTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wk_pwa_push_notification`,
            `'._DB_PREFIX_.'wk_pwa_push_notification_type`,
            `'._DB_PREFIX_.'wk_pwa_push_notification_token`,
            `'._DB_PREFIX_.'wk_pwa_push_notification_history`'
        );
    }
}
