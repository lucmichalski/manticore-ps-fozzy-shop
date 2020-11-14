<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2019 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
if (!defined('THUMBLIB_BASE_PATH') && file_exists(_PS_MODULE_DIR_.'ph_simpleblog/assets/phpthumb/ThumbLib.inc.php')) {
    require_once _PS_MODULE_DIR_.'ph_simpleblog/assets/phpthumb/ThumbLib.inc.php';
}

require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogCategory.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPost.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPostType.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPostImage.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogTag.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogComment.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPostAuthor.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/classes/SimpleBlogHelper.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/classes/BlogPostsFinder.php';

define('_SIMPLEBLOG_GALLERY_DIR_', _PS_MODULE_DIR_.'ph_simpleblog/galleries/');
define('_SIMPLEBLOG_GALLERY_URL_', _MODULE_DIR_.'ph_simpleblog/galleries/');

if (!defined('_PS_VERSION_')) {
    exit;
}

class ph_simpleblog extends Module
{
    public $is_16;
    public $is_17;

    // Not used at this moment, some issues with PrestaShop 1.7.x
    public $moduleTabs = array(
        array(
            'name' => array(
                'en' => 'Blog for PrestaShop',
                'pl' => 'Blog dla PrestaShop',
            ),
            'class_name' => 'AdminBlogForPrestaShop',
            'parent_class_name' => 'IMPROVE',
        ),
        array(
            'name' => array(
                'en' => 'Posts',
                'pl' => 'Wpisy',
            ),
            'class_name' => 'AdminSimpleBlogPosts',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Categories',
                'pl' => 'Kategorie',
            ),
            'class_name' => 'AdminSimpleBlogCategories',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Comments',
                'pl' => 'Komentarze',
            ),
            'class_name' => 'AdminSimpleBlogComments',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Tags',
                'pl' => 'Tagi',
            ),
            'class_name' => 'AdminSimpleBlogTags',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Authors',
                'pl' => 'Autorzy',
            ),
            'class_name' => 'AdminSimpleBlogAuthors',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Settings',
                'pl' => 'Ustawienia',
            ),
            'class_name' => 'AdminSimpleBlogSettings',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
    );

    public function __construct()
    {
        $this->name = 'ph_simpleblog';
        $this->tab = 'front_office_features';
        $this->version = '1.7.0';
        $this->author = 'PrestaHome';
        $this->need_instance = 0;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy['min'] = '1.6';
        $this->ps_versions_compliancy['max'] = _PS_VERSION_;
        $this->secure_key = Tools::encrypt($this->name);
        $this->is_16 = (version_compare(_PS_VERSION_, '1.6.0', '>=') === true && version_compare(_PS_VERSION_, '1.7.0', '<') === true) ? true : false;
        $this->is_17 = (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) ? true : false;
        $this->controllers = array('single', 'list', 'category', 'categorypage', 'page', 'author', 'authorslist');
        $this->bootstrap = true;

        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation('simpleblog_category', array('type' => 'shop'));
            Shop::addTableAssociation('simpleblog_post', array('type' => 'shop'));
        }

        parent::__construct();

        $this->displayName = $this->l('Blog for PrestaShop');
        $this->description = $this->l('Adds a blog to your PrestaShop store');

        $this->confirmUninstall = $this->l('Are you sure you want to delete this module?');

        if ($this->id && !$this->isRegisteredInHook('moduleRoutes')) {
            $this->registerHook('moduleRoutes');
        }

        if ($this->id && !$this->isRegisteredInHook('displayPrestaHomeBlogAfterPostContent')) {
            $this->registerHook('displayPrestaHomeBlogAfterPostContent');
        }

        if ($this->id && $this->is_17) {
            setlocale(LC_TIME, str_replace('-', '_', $this->context->language->locale));
        }

        // $this->registerHook('gSitemapAppendUrls');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Hooks & Install
        return parent::install()
                && $this->prepareModuleSettings()
                && $this->registerHook('moduleRoutes')
                && $this->registerHook('displaySimpleBlogPosts')
                && $this->registerHook('displaySimpleBlogCategories')
                && $this->registerHook('displayHeader')
                && $this->registerHook('displayTop')
                && $this->registerHook('displayBackOfficeHeader')
                && $this->registerHook('displayPrestaHomeBlogAfterPostContent')
                && $this->registerHook('displayLeftColumn');
    }

    /**
     * Install module tab with specified arguments.
     *
     * @param string $name
     * @param string $className
     * @param int    $id_parent
     *
     * @return object new Tab instance
     */
    public function myInstallModuleTab($name, $className, $id_parent)
    {
        $tab = new Tab();
        $tab->name = array();
        $tab->class_name = $className;

        if ($className == 'AdminBlogForPrestaShop' && $this->is_17) {
            $tab->icon = 'note';
        }

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->id_parent = (int) $id_parent;
        $tab->module = $this->name;

        if ($tab->save()) {
            return $tab;
        }
    }

    /**
     * Delete module tabs.
     */
    public function myDeleteModuleTabs()
    {
        $tabs = array(
            'AdminSimpleBlog',
            'AdminSimpleBlogCategories',
            'AdminSimpleBlogPosts',
            'AdminSimpleBlogSettings',
            'AdminSimpleBlogTags',
            'AdminSimpleBlogComments',
            'AdminSimpleBlogAuthors',
            'AdminBlogForPrestaShop',
            'AdminSimpleBlogPostsParent',
        );

        $idTabs = array();
        foreach ($tabs as $className) {
            $idTabs[] = Tab::getIdFromClassName($className);
        }

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                if (Validate::isLoadedObject($tab)) {
                    $tab->delete();
                }
            }
        }
    }

    /**
     * Install all module tabs.
     */
    public function createAllModuleTabs($force = false)
    {
        // Main tab
        $mainTab = $this->myInstallModuleTab(
            $this->l('Blog for PrestaShop'),
            $this->is_17 ? 'AdminBlogForPrestaShop' : 'AdminSimpleBlog',
            Tab::getIdFromClassName('IMPROVE')
        );

        // Posts
        $this->myInstallModuleTab(
            $this->l('Posts'),
            'AdminSimpleBlogPosts',
            (int) $mainTab->id
        );

        // Categories
        $this->myInstallModuleTab(
            $this->l('Categories'),
            'AdminSimpleBlogCategories',
            (int) $mainTab->id
        );

        // Comments
        $this->myInstallModuleTab(
            $this->l('Comments'),
            'AdminSimpleBlogComments',
            (int) $mainTab->id
        );

        // Tags
        $this->myInstallModuleTab(
            $this->l('Tags'),
            'AdminSimpleBlogTags',
            (int) $mainTab->id
        );

        // Authors
        $this->myInstallModuleTab(
            $this->l('Authors'),
            'AdminSimpleBlogAuthors',
            (int) $mainTab->id
        );

        // Settings
        $this->myInstallModuleTab(
            $this->l('Settings'),
            'AdminSimpleBlogSettings',
            (int) $mainTab->id
        );
    }

    /**
     * This methods check wether we need to update db schema because of
     * well known problem between ~1.6.0.4 and 1.6.0.6 where "hide_post_mode" wasn't available.
     */
    protected function checkDatabaseIntegrationProblems()
    {
        $sql = 'SHOW COLUMNS FROM `'._DB_PREFIX_.'tab`';
        $tab_columns = Db::getInstance()->executeS($sql);

        $createColumn = true;
        foreach ($tab_columns as $column) {
            if ($column['Field'] == 'hide_host_mode') {
                $createColumn = false;
            }
        }

        if ($createColumn == true) {
            Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.'tab` ADD `hide_host_mode` tinyint(1) NOT NULL DEFAULT 0 AFTER  `active`');
        }
    }

    /**
     * Setup default content for module.
     */
    protected function setupBlogDefaultContent()
    {
        // Default category
        $simple_blog_category = new SimpleBlogCategory();

        foreach (Language::getLanguages(true) as $lang) {
            $simple_blog_category->name[$lang['id_lang']] = 'News';
        }

        foreach (Language::getLanguages(true) as $lang) {
            $simple_blog_category->link_rewrite[$lang['id_lang']] = 'news';
        }
        $simple_blog_category->add();
        $simple_blog_category->associateTo(Shop::getCompleteListOfShopsID());

        // Post Types
        $default_post_type = new SimpleBlogPostType();
        $default_post_type->name = 'Post';
        $default_post_type->slug = 'post';
        $default_post_type->description = $this->l('Default type of post');
        $default_post_type->add();

        $gallery_post_type = new SimpleBlogPostType();
        $gallery_post_type->name = 'Gallery';
        $gallery_post_type->slug = 'gallery';
        $gallery_post_type->add();

        $external_url_post_type = new SimpleBlogPostType();
        $external_url_post_type->name = $this->l('External URL');
        $external_url_post_type->slug = 'url';
        $external_url_post_type->add();

        $video_post_type = new SimpleBlogPostType();
        $video_post_type->name = $this->l('Video');
        $video_post_type->slug = 'video';
        $video_post_type->add();
    }

    public static function getLanguagesIDs()
    {
        $langs = Language::getLanguages(false);
        $ids = array();
        foreach ($langs as $lang) {
            $ids[] = $lang['id_lang'];
        }

        return $ids;
    }

    protected function getSimpleBlogSettings()
    {
        if (function_exists('date_default_timezone_get')) {
            $timezone = @date_default_timezone_get();
        } else {
            $timezone = 'Europe/Warsaw';
        }

        return array(
            'PH_BLOG_SLUG' => 'blog',
            'PH_BLOG_POSTS_PER_PAGE' => 10,
            'PH_BLOG_FB_COMMENTS' => true,
            'PH_BLOG_COLUMNS' => 'prestashop',
            'PH_BLOG_LAYOUT' => 'default',
            'PH_BLOG_LIST_LAYOUT' => 'grid',
            'PH_BLOG_GRID_COLUMNS' => 2,
            'PH_BLOG_MAIN_TITLE' => array_fill_keys(self::getLanguagesIDs(), $this->l('Blog - whats new?')),
            'PH_BLOG_LOAD_FA' => false,
            'PH_BLOG_DISPLAY_AUTHOR' => true,
            'PH_BLOG_DISPLAY_DATE' => true,
            'PH_BLOG_DISPLAY_THUMBNAIL' => true,
            'PH_BLOG_DISPLAY_CATEGORY' => true,
            'PH_BLOG_DISPLAY_SHARER' => true,
            'PH_BLOG_DISPLAY_TAGS' => true,
            'PH_BLOG_DISPLAY_DESCRIPTION' => true,
            'PH_BLOG_THUMB_METHOD' => 1,
            'PH_BLOG_THUMB_X' => 600,
            'PH_BLOG_THUMB_Y' => 300,
            'PH_BLOG_THUMB_X_WIDE' => 900,
            'PH_BLOG_THUMB_Y_WIDE' => 350,
            'PH_BLOG_DISPLAY_CAT_DESC' => true,
            'PH_BLOG_POST_BY_AUTHOR' => true,
            'PH_BLOG_FB_INIT' => true,
            'PH_BLOG_DISPLAY_FEATURED' => true,
            'PH_BLOG_DISPLAY_BREADCRUMBS' => true,
            'PH_BLOG_DISPLAY_FEATURED' => true,
            'PH_BLOG_DISPLAY_CATEGORY_IMAGE' => true,
            'PH_BLOG_DISPLAY_LIKES' => true,
            'PH_BLOG_DISPLAY_VIEWS' => true,
            'PH_CATEGORY_IMAGE_X' => 900,
            'PH_CATEGORY_IMAGE_Y' => 250,
            'PH_CATEGORY_SORTBY' => 'position',
            'PH_BLOG_DATEFORMAT' => '%B %e, %Y',
            'PH_BLOG_NATIVE_COMMENTS' => true,
            'PH_BLOG_COMMENT_NOTIFICATIONS' => true,
            'PH_BLOG_NEW_AUTHORS' => 0,
            'PH_BLOG_AUTHOR_INFO' => true,
            'PH_BLOG_COMMENT_AUTO_APPROVAL' => false,
            'PH_BLOG_COMMENT_ALLOW' => true,
            'PH_BLOG_COMMENT_STUFF_HIGHLIGHT' => true,
            'PH_BLOG_COMMENT_NOTIFY_EMAIL' => Configuration::get('PS_SHOP_EMAIL'),
            'PH_BLOG_FACEBOOK_MODERATOR' => '',
            'PH_BLOG_FACEBOOK_APP_ID' => '',
            'PH_BLOG_FACEBOOK_COLOR_SCHEME' => 'light',
            'PH_BLOG_DISPLAY_MORE' => true,
            'PH_BLOG_MASONRY_LAYOUT' => false,
            'PH_BLOG_TIMEZONE' => $timezone,
            'PH_BLOG_LOAD_FONT_AWESOME' => true,
            'PH_BLOG_LOAD_BXSLIDER' => true,
            'PH_BLOG_LOAD_MASONRY' => true,
            'PH_BLOG_LOAD_FITVIDS' => true,
            'PH_BLOG_DISPLAY_RELATED' => true,
            'PH_BLOG_COMMENT_ALLOW_GUEST' => false,
            'PH_BLOG_COMMENTS_RECAPTCHA' => true,
            'PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY' => '',
            'PH_BLOG_COMMENTS_RECAPTCHA_SECRET_KEY' => '',
            'PH_BLOG_COMMENTS_RECAPTCHA_THEME' => 'light',
            'PH_BLOG_RELATED_PRODUCTS_USE_DEFAULT_LIST' => false,
            'PH_BLOG_ADVERTISING' => true,
            'PH_BLOG_COMMENTS_SYSTEM' => 'native',
            'PH_BLOG_DISQUS_SHORTNAME' => 'blogforprestashop',
            'PH_BLOG_CANONICAL' => '',
            'PH_BLOG_IMAGE_FBSHARE' => 'featured',
        );
    }

    public function prepareModuleSettings()
    {
        // Database
        $sql = array();
        include_once dirname(__FILE__).'/init/install_sql.php';
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                die('Error while creating DB');
            }
        }
        $this->checkDatabaseIntegrationProblems();

        // Tabs
        $this->createAllModuleTabs();

        // Default content
        $this->setupBlogDefaultContent();

        // Settings
        foreach ($this->getSimpleBlogSettings() as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        // For theme developers, you're welcome!
        if (file_exists(_PS_MODULE_DIR_.$this->name.'/init/my-install.php')) {
            include_once _PS_MODULE_DIR_.$this->name.'/init/my-install.php';
        }

        return true;
    }

    public function uninstall()
    {
        // Database
        $sql = array();
        include_once dirname(__FILE__).'/init/uninstall_sql.php';
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return false;
            }
        }

        // Settings
        foreach ($this->getSimpleBlogSettings() as $key => $value) {
            Configuration::deleteByName($key);
        }

        // Tabs
        $this->myDeleteModuleTabs();

        // For theme developers - you're welcome!
        if (file_exists(_PS_MODULE_DIR_.$this->name.'/init/my-uninstall.php')) {
            include_once _PS_MODULE_DIR_.$this->name.'/init/my-uninstall.php';
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    protected function myAssignModuleAssets()
    {
        if ($this->is_16) {
            if (Configuration::get('PH_BLOG_LOAD_BXSLIDER')) {
                $this->context->controller->addCSS($this->_path.'css/jquery.bxslider.css');
            }

            if (Configuration::get('PH_BLOG_LOAD_FONT_AWESOME')) {
                $this->context->controller->addCSS($this->_path.'css/font-awesome.css');
            }

            $this->context->controller->addCSS($this->_path.'css/ph_simpleblog.css');
            $this->context->controller->addCSS($this->_path.'css/custom.css');

            if (Configuration::get('PH_BLOG_LOAD_FITVIDS')) {
                $this->context->controller->addJS($this->_path.'js/jquery.fitvids.js');
            }

            if (Configuration::get('PH_BLOG_LOAD_BXSLIDER')) {
                $this->context->controller->addJS($this->_path.'js/jquery.bxslider.min.js');
            }

            $this->context->controller->addJS($this->_path.'js/ph_simpleblog.js');
            $this->context->controller->addJqueryPlugin(array('bxslider', 'fancybox'));

            if (Configuration::get('PH_BLOG_MASONRY_LAYOUT') && Configuration::get('PH_BLOG_LOAD_MASONRY')) {
                $this->context->controller->addJS($this->_path.'js/isotope.pkgd.min.js');
                $this->context->controller->addJS($this->_path.'js/ph_simpleblog_masonry.js');
            }
        }

        if ($this->is_17) {
            $this->context->controller->registerStylesheet('modules-phsimpleblog', 'modules/'.$this->name.'/views/css/ph_simpleblog-17.css', array('media' => 'all', 'priority' => 150));
            $this->context->controller->registerStylesheet('modules-phsimpleblog-custom', 'modules/'.$this->name.'/css/custom.css', array('position' => 'bottom', 'priority' => 150));
            $this->context->controller->registerJavascript('modules-phsimpleblog', 'modules/'.$this->name.'/views/js/ph_simpleblog-17.js', array('position' => 'bottom', 'priority' => 150));

            if (Configuration::get('PH_BLOG_LOAD_FITVIDS')) {
                $this->context->controller->addJS($this->_path.'js/jquery.fitvids.js');
            }

            $this->context->controller->addJqueryPlugin(array('fancybox'));
            $this->context->controller->requireAssets(array('font-awesome-css'));
        }
        
    }

    public function hookDisplayHeader($params)
    {
        $this->myAssignModuleAssets();

        if (Tools::getValue('module') && Tools::getValue('module') == 'ph_simpleblog' && Tools::getValue('controller') == 'single') {
            $post = $this->context->controller->getPost();

            $imageForFacebook = Configuration::get('PH_BLOG_IMAGE_FBSHARE', 'featured') == 'featured' ? 'featured_image' : 'banner';

            $this->context->smarty->assign(array(
                'post_url' => $post->url,
                'post_title' => $post->title,
                'post_description' => strip_tags($post->short_content),
                'post_image' => rtrim($this->context->shop->getBaseUrl(true, false), '/').$post->{$imageForFacebook},
            ));

            Media::addJsDef(
                array(
                    'ph_sharing_name' => addcslashes($post->title, "'"),
                    'ph_sharing_url' => addcslashes($post->url, "'"),
                    'ph_sharing_img' => addcslashes(rtrim($this->context->shop->getBaseUrl(true, false), '/').$post->{$imageForFacebook}, "'"),
                )
            );

            return $this->display(__FILE__, 'header.tpl');
        }
    }

    public function hookModuleRoutes($params)
    {
        if (Tools::getIsset('SubmitGsitemap')) {
            return array();
        }

        $blog_slug = Configuration::get('PH_BLOG_SLUG');

        $routes = array(
            // Home list
            'module-ph_simpleblog-list' => array(
                'controller' => 'list',
                'rule' => $blog_slug,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Home pagination
            'module-ph_simpleblog-page' => array(
                'controller' => 'page',
                'rule' => $blog_slug.'/page/{p}',
                'keywords' => array(
                    'p' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'p',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Authors list
            'module-ph_simpleblog-authorslist' => array(
                'controller' => 'authorslist',
                'rule' => $blog_slug.'/'.Tools::link_rewrite($this->l('authors')),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Category list
            'module-ph_simpleblog-category' => array(
                'controller' => 'category',
                'rule' => $blog_slug.'/{sb_category}',
                'keywords' => array(
                    'sb_category' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'sb_category',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Single author page
            'module-ph_simpleblog-author' => array(
                'controller' => 'author',
                'rule' => $blog_slug.'/'.Tools::link_rewrite($this->l('author')).'/{author_id}',
                'keywords' => array(
                    'author_id' => array(
                        'regexp' => '[0-9]+',
                        'param' => 'author_id',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Category pagination
            'module-ph_simpleblog-categorypage' => array(
                'controller' => 'categorypage',
                'rule' => $blog_slug.'/{sb_category}/page/{p}',
                'keywords' => array(
                    'p' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'p',
                    ),
                    'sb_category' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'sb_category',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Single
            'module-ph_simpleblog-single' => array(
                'controller' => 'single',
                'rule' => $blog_slug.'/{sb_category}/{rewrite}',
                'keywords' => array(
                    'sb_category' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'sb_category',
                    ),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'rewrite',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),
        );

        return $routes;
    }

    public static function myRealURL()
    {
        return Context::getContext()->shop->getBaseUrl(true);
    }

    public static function getLink()
    {
        return Context::getContext()->link->getModuleLink('ph_simpleblog', 'list');
    }

    public function prepareSimpleBlogCategories()
    {
        $this->context->smarty->assign(array(
            'categories' => SimpleBlogCategory::getCategories($this->context->language->id, true),
        ));
    }

    public function hookDisplaySimpleBlogCategories($params)
    {
        $this->prepareSimpleBlogCategories();

        if (isset($params['template'])) {
            return $this->display(__FILE__, $params['template'].'.tpl');
        } else {
            return $this->hookDisplayLeftColumn($params);
        }
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->prepareSimpleBlogCategories();

        if ($this->is_17) {
            return $this->fetch('module:'.$this->name.'/views/templates/hook/1.7/left-column.tpl');
        }

        return $this->display(__FILE__, 'left-column.tpl');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayHome($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayFooter($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (method_exists($this->context->controller, 'addCSS')) {
            $this->context->controller->addCSS(($this->_path).'css/simpleblog-admin.css', 'all');
        }
    }

    public function hookDisplayPrestaHomeBlogAfterPostContent($params)
    {
        if ($this->is_17) {
            return $this->fetch('module:'.$this->name.'/views/templates/hook/1.7/after-post-content.tpl');
        }

        return $this->display(__FILE__, 'after-post-content.tpl');
    }

    public function getContent()
    {
        $this->smarty->assign('phpWarning', version_compare(phpversion(), '5.6', '<'));
        $this->smarty->assign('module_path', _MODULE_DIR_.'ph_simpleblog/');
        $this->smarty->assign('shopUrl', $this->context->shop->getBaseUrl());

        return $this->display(__FILE__, 'views/templates/admin/welcome.tpl');
    }

    // Britoff Sitemap
    public function hookgSitemapAppendUrls($params) {
      
      $id_lang = (int)$params['lang']['id_lang'];
      $iso_lang = $params['lang']['iso_code'];
      $id_shop = (int)$params['lang']['id_shop'];
      $shops = Shop::getShops();
      $langs = Language::getLanguages(true);
      $multylangs = count($langs);
      
		  $def_lang = Configuration::get('PS_LANG_DEFAULT');  
		  $def_lang_iso = Language::getIsoById((int)$def_lang);
      $def_ssl = Configuration::get('PS_SSL_ENABLED');
      $def_blog = Configuration::get('PH_BLOG_SLUG');
      
      $protokol = 'http://';
      if ($def_ssl) $protokol = 'https://';
      
      foreach ($shops as $shop)
        {
          if ($shop['id_shop'] == $id_shop)
          {
          $host = $shop['domain'];
          $uri = $shop['uri'];
          }
        }
      
      $posts = 'SELECT l.`id_simpleblog_post`, l.`link_rewrite`, l.`title`, b.`date_upd` FROM `'._DB_PREFIX_.'simpleblog_post` b, `'._DB_PREFIX_.'simpleblog_post_lang` l, `'._DB_PREFIX_.'simpleblog_post_shop` s ';
      $posts .='WHERE b.`active`=1 ';
      $posts .='AND (l.`id_simpleblog_post` = b.`id_simpleblog_post`) ';
      $posts .='AND (l.`id_simpleblog_post` = s.`id_simpleblog_post`) ';
      $posts .='AND (s.`id_shop`= '.$id_shop.') ';
      $posts .='AND (l.`id_lang` = '.$id_lang.') ';
      
      $categs = 'SELECT l.`id_simpleblog_category`, l.`link_rewrite`, l.`name`, b.`date_upd` FROM `'._DB_PREFIX_.'simpleblog_category` b, `'._DB_PREFIX_.'simpleblog_category_lang` l, `'._DB_PREFIX_.'simpleblog_category_shop` s ';
      $categs .='WHERE b.`active`=1 ';
      $categs .='AND (l.`id_simpleblog_category` = b.`id_simpleblog_category`) ';
      $categs .='AND (l.`id_simpleblog_category` = s.`id_simpleblog_category`) ';
      $categs .='AND (s.`id_shop`= '.$id_shop.') ';
      $categs .='AND (l.`id_lang` = '.$id_lang.') ';
      
      $links_all = array();
      $links = array();
      $links_cat = array();
      $posts_list = Db::getInstance()->executeS ($posts);
      $categs_list = Db::getInstance()->executeS ($categs);
      
      foreach ($posts_list as $key=>$post)
      {
  		  if ($post['link_rewrite']) {
          $postik = new SimpleBlogPost($post['id_simpleblog_post'],$id_lang,$id_shop);
          $links[$key]['type'] = 'blog';
          $links[$key]['page'] = 'blog';
          $links[$key]['lastmod'] = $post['date_upd'];
          
          $links[$key]['link'] = $protokol.$host.$uri.$def_blog.'/'.$postik->category_rewrite.'/'.$postik->link_rewrite;
          if ($multylangs > 1)  $links[$key]['link'] = $protokol.$host.$uri.$iso_lang.'/'.$def_blog.'/'.$postik->category_rewrite.'/'.$postik->link_rewrite;
          
          if ($postik->banner)
          {
          $links[$key]['image']['link'] = $protokol.$host.$postik->banner;
          $links[$key]['image']['caption'] = $post['title'];
          $links[$key]['image']['title_img'] = $postik->meta_title;
          }
          else
          {
          $links[$key]['image']='';
          }
          
        }
        
      }
      foreach ($categs_list as $key=>$post)
      {
  		  if ($post['link_rewrite']) {
          $postik = new SimpleBlogCategory($post['id_simpleblog_category'],$id_lang,$id_shop);
          $links_cat[$key]['type'] = 'blog';
          $links_cat[$key]['page'] = 'blog';
          $links_cat[$key]['lastmod'] = $post['date_upd'];
          if ($postik->image)
          {
          $links_cat[$key]['image']['link'] = $protokol.$host.$postik->image;
          $links_cat[$key]['image']['caption'] = $post['name'];
          $links_cat[$key]['image']['title_img'] = $postik->meta_title;
          }
          else
          {
          $links_cat[$key]['image']='';
          }
          $links_cat[$key]['link'] = $protokol.$host.$uri.$def_blog.'/'.$postik->link_rewrite;
          if ($multylangs > 1) $links_cat[$key]['link'] = $protokol.$host.$uri.$iso_lang.'/'.$def_blog.'/'.$postik->link_rewrite; 
        }
        
      } 
      
    $links_all = array_merge($links,$links_cat); 
    return $links_all;    
    }
}
