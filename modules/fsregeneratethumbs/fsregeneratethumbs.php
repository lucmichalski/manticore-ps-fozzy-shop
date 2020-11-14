<?php
/**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$class_folder = dirname(__FILE__).'/classes/';
require_once($class_folder.'FsRegenerateThumbsDataTransfer.php');
require_once($class_folder.'FsRegenerateThumbsLogger.php');
require_once($class_folder.'FsRegenerateThumbsMessenger.php');
require_once($class_folder.'FsRegenerateThumbsTools.php');

class Fsregeneratethumbs extends Module
{
    public $image_types;
    public $image_formats;
    public $image_formats_by_type;

    private static $smarty_registered = false;
    protected static $images_types_name_cache = array();

    public function __construct()
    {
        $this->name = 'fsregeneratethumbs';
        $this->tab = 'administration';
        $this->version = '1.1.1';
        $this->author = 'ModuleFactory';
        $this->need_instance = 0;
        $this->ps_versions_compliancy['min'] = '1.5';
        $this->module_key = 'd1c0ef9722d17dbf0d5ccb2f18a4d637';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Regenerate Image Thumbnails');
        $this->description = $this->l('Easy and convenient way to regenerate your thumbnail images.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->image_types = array(
            'categories' => $this->l('Categories'),
            'manufacturers' => $this->l('Manufacturers'),
            'suppliers' => $this->l('Suppliers'),
            'products' => $this->l('Products'),
            'stores' => $this->l('Stores')
        );

        $this->image_formats_by_type = array();
        foreach (array_keys($this->image_types) as $i) {
            $this->image_formats_by_type[$i] = ImageType::getImagesTypes($i);
        }

        $this->image_formats = ImageType::getImagesTypes();

        if (!self::$smarty_registered) {
            smartyRegisterFunction(
                $this->context->smarty,
                'modifier',
                'fsrtCorrectTheMess',
                array('FsRegenerateThumbsTools', 'unescapeSmarty'),
                false
            );
            smartyRegisterFunction(
                $this->context->smarty,
                'block',
                'fsrtMinifyCss',
                array('FsRegenerateThumbsTools', 'minifyCss'),
                false
            );
            if ($this->isPs15()) {
                $context = Context::getContext();
                $context->smarty->registerPlugin(
                    'block',
                    'fsrtMinifyCss',
                    array('FsRegenerateThumbsTools', 'minifyCss')
                );
            }
            self::$smarty_registered = true;
        }
    }

    public function url()
    {
        $url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name;
        if ($this->isPsMin17()) {
            return $url;
        }
        return FsRegenerateThumbsTools::baseUrl().$url;
    }

    public function adminAjaxUrl($controller, $params = array())
    {
        $context = Context::getContext();
        $params_string = '';
        if ($params) {
            $params_string .= '&'.http_build_query($params);
        }
        $url = $context->link->getAdminLink($controller).$params_string;
        if ($this->isPsMin17()) {
            return $url;
        }
        return FsRegenerateThumbsTools::baseUrl().$url;
    }

    public function getModuleBaseUrl()
    {
        $context = Context::getContext();
        return $context->shop->getBaseURL(true).'modules/'.$this->name.'/';
    }

    public function getModuleFile()
    {
        return __FILE__;
    }

    public function install()
    {
        $return = parent::install();

        $tab = Tab::getInstanceFromClassName('AdminFsregeneratethumbs', Configuration::get('PS_LANG_DEFAULT'));
        if (!$tab->module) {
            $tab = new Tab();
            $tab->id_parent = -1;
            if ($this->isPsMin17()) {
                $tab->id_parent = 0;
            }
            $tab->position = 0;
            $tab->module = $this->name;
            $tab->class_name = 'AdminFsregeneratethumbs';
            $tab->active = 1;
            $tab->name = $this->generateMultilangualFields($this->displayName);
            $tab->save();
        }

        return $return;
    }

    public function uninstall()
    {
        $return = parent::uninstall();

        $tab = Tab::getInstanceFromClassName('AdminFsregeneratethumbs', Configuration::get('PS_LANG_DEFAULT'));
        $tab->delete();

        return $return;
    }

    #################### ADMIN ####################

    public function getContent()
    {
        $context = Context::getContext();
        $context->controller->addCSS(($this->_path).'views/css/admin.css', 'all');
        $context->controller->addCSS(($this->_path).'views/css/sweetalert.css', 'all');
        if ($this->isPs15()) {
            $context->controller->addCSS(($this->_path).'views/css/bootstrap-progress-bar.css', 'all');
        }
        $context->controller->addJS(($this->_path).'views/js/admin.js');
        $context->controller->addJS(($this->_path).'views/js/sweetalert.min.js');
        $html = $this->getCssAndJs();
        $html .= FsRegenerateThumbsMessenger::getMessagesHtml();

        $context->smarty->assign(array(
            'image_types' => $this->image_types,
            'image_formats' => $this->image_formats,
            'download_log_url' => $this->adminAjaxUrl(
                'AdminFsregeneratethumbs',
                array('ajax' => '1', 'action' => 'downloadlog')
            )
        ));

        if ($this->isPs15()) {
            $html .= $this->display(__FILE__, 'views/templates/admin/form_15.tpl');
        } else {
            $html .= $this->display(__FILE__, 'views/templates/admin/form.tpl');
        }

        $context->smarty->assign(array(
            'is_ps_15' => $this->isPs15(),
            'is_ps_min_16' => $this->isPsMin16(),
            'module_base_url' => $this->getModuleBaseUrl()
        ));

        $html .= $this->display(__FILE__, 'views/templates/admin/help.tpl');

        return $html;
    }

    public static function generateMultilangualFieldsStatic($default_value = '')
    {
        $multilangual_fields = array();
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $multilangual_fields[$language['id_lang']] = $default_value;
        }

        return $multilangual_fields;
    }

    public function generateMultilangualFields($default_value = '')
    {
        return self::generateMultilangualFieldsStatic($default_value);
    }

    private function getCssAndJs()
    {
        $context = Context::getContext();
        $context->smarty->assign(array(
            'is_ps_15' => ($this->isPs15())?'true':'false',
            'is_ps_min_16' => ($this->isPsMin16())?'true':'false',
            'is_ps_16' => ($this->isPs16())?'true':'false',
            'is_ps_min_17' => ($this->isPsMin17())?'true':'false',
            'image_formats_by_type' => $this->jsonEncode($this->image_formats_by_type),
            'generate_queue_url' => $this->adminAjaxUrl(
                'AdminFsregeneratethumbs',
                array('ajax' => '1', 'action' => 'generatequeue')
            ),
            'generate_thumbnail_url' => $this->adminAjaxUrl(
                'AdminFsregeneratethumbs',
                array('ajax' => '1', 'action' => 'generatethumbnail')
            ),
        ));

        return $this->display(__FILE__, 'views/templates/admin/css_js.tpl');
    }

    public function getImageType($name, $type = null, $order = 0)
    {
        static $is_passed = false;

        if (!isset(self::$images_types_name_cache[$name.'_'.$type.'_'.$order]) && !$is_passed) {
            $results = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'image_type`');

            $types = array('products', 'categories', 'manufacturers', 'suppliers', 'stores');
            $total = count($types);

            foreach ($results as $result) {
                foreach ($result as $value) {
                    for ($i = 0; $i < $total; ++$i) {
                        self::$images_types_name_cache[$result['name'].'_'.$types[$i].'_'.$value] = $result;
                    }
                }
            }

            $is_passed = true;
        }

        $return = false;
        if (isset(self::$images_types_name_cache[$name.'_'.$type.'_'.$order])) {
            $return = self::$images_types_name_cache[$name.'_'.$type.'_'.$order];
        }
        return $return;
    }

    #################### 1.7 ####################

    /**
     * @return bool
     */
    public function isPs15()
    {
        return self::isPs15Static();
    }

    /**
     * @return bool
     */
    public static function isPs15Static()
    {
        return version_compare(_PS_VERSION_, '1.5.0.0', '>=') &&
        version_compare(_PS_VERSION_, '1.6.0.0', '<');
    }

    /**
     * @return bool
     */
    public function isPsMin16()
    {
        return self::isPsMin16Static();
    }

    /**
     * @return bool
     */
    public static function isPsMin16Static()
    {
        return version_compare(_PS_VERSION_, '1.6.0.0', '>=');
    }

    /**
     * @return bool
     */
    public function isPs16()
    {
        return self::isPs16Static();
    }

    /**
     * @return bool
     */
    public static function isPs16Static()
    {
        return version_compare(_PS_VERSION_, '1.6.0.0', '>=') &&
        version_compare(_PS_VERSION_, '1.7.0.0', '<');
    }

    /**
     * @return bool
     */
    public function isPsMin17()
    {
        return self::isPsMin17Static();
    }

    /**
     * @return bool
     */
    public static function isPsMin17Static()
    {
        return version_compare(_PS_VERSION_, '1.7.0.0', '>=');
    }

    /**
     * @param $data
     * @return string
     */
    public function jsonEncode($data)
    {
        return self::jsonEncodeStatic($data);
    }

    /**
     * @param $data
     * @return string
     */
    public static function jsonEncodeStatic($data)
    {
        if (self::isPsMin17Static()) {
            return json_encode($data);
        }
        return Tools::jsonEncode($data);
    }

    /**
     * @param $data
     * @param bool $assoc
     * @return array|mixed
     */
    public function jsonDecode($data, $assoc = false)
    {
        return self::jsonDecodeStatic($data, $assoc);
    }

    /**
     * @param $data
     * @param bool $assoc
     * @return array|mixed
     */
    public static function jsonDecodeStatic($data, $assoc = false)
    {
        if (self::isPsMin17Static()) {
            return json_decode($data, $assoc);
        }
        return Tools::jsonDecode($data, $assoc);
    }
}
