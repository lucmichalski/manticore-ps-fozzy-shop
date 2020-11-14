<?php
/**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class FsRegenerateThumbsMessenger
{
    private static $messages = array();
    private static $readed_from_cookie = false;

    public static function getMessagesHtml()
    {
        return self::getErrorMessages(true).self::getSuccessMessages(true);
    }

    public static function addSuccessMessage($message)
    {
        self::addMessage('success', $message);
    }

    public static function getSuccessMessages($html = false)
    {
        $return_messages = array();

        self::readFromCookie();

        if (self::$messages) {
            foreach (self::$messages as $message) {
                if ($message['type'] == 'success') {
                    $return_messages[] = $message['message'];
                }
            }
        }

        if ($html) {
            if ($return_messages) {
                $module = Module::getInstanceByName('fsregeneratethumbs');
                return $module->displayConfirmation(implode('<br />', $return_messages));
            }
            return '';
        }

        return $return_messages;
    }

    public static function addErrorMessage($message)
    {
        self::addMessage('error', $message);
    }

    public static function getErrorMessages($html = false)
    {
        $return_messages = array();

        self::readFromCookie();

        if (self::$messages) {
            foreach (self::$messages as $message) {
                if ($message['type'] == 'error') {
                    $return_messages[] = $message['message'];
                }
            }
        }

        if ($html) {
            if ($return_messages) {
                $module = Module::getInstanceByName('fsregeneratethumbs');
                if (count($return_messages) < 2) {
                    $return_messages = implode('', $return_messages);
                }
                return $module->displayError($return_messages);
            }
            return '';
        }

        return $return_messages;
    }

    private static function addMessage($type, $message)
    {
        self::$messages[] = array('type' => $type, 'message' => $message);
        self::saveToCookie();
    }

    private static function readFromCookie()
    {
        $cookie = Context::getContext()->cookie;

        if (!self::$readed_from_cookie) {
            if (isset($cookie->fsmessenger)) {
                self::$messages = Fsregeneratethumbs::jsonDecodeStatic($cookie->fsmessenger, true);
                unset($cookie->fsmessenger);
            }

            self::$readed_from_cookie = true;
        }
    }

    private static function saveToCookie()
    {
        $cookie = Context::getContext()->cookie;
        $cookie->fsmessenger = Fsregeneratethumbs::jsonEncodeStatic(self::$messages);
    }
}
