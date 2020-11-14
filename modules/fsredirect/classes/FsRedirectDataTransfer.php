<?php
/**
 *  2016 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2016 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class FsRedirectDataTransfer
{
    private static $data = null;
    private static $readed_from_cookie = false;

    public static function setData($var)
    {
        $cookie = Context::getContext()->cookie;
        $cookie->fsdatatransfer = Fsredirect::jsonEncodeStatic($var);
    }

    public static function getData()
    {
        $cookie = Context::getContext()->cookie;

        if (!self::$readed_from_cookie) {
            if (isset($cookie->fsdatatransfer)) {
                self::$data = Fsredirect::jsonDecodeStatic($cookie->fsdatatransfer, true);
                unset($cookie->fsdatatransfer);
            }

            self::$readed_from_cookie = true;
        }

        return self::$data;
    }
}
