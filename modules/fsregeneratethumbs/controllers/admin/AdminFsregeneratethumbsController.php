<?php
/**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class AdminFsregeneratethumbsController extends ModuleAdminController
{
    protected $logger;

    public function __construct()
    {
        parent::__construct();

        $this->logger = new FsRegenerateThumbsLogger('regenerate_log.txt');
    }

    public function ajaxProcessDownloadlog()
    {
        $this->logger->download();
    }

    public function ajaxProcessGeneratequeue()
    {
        $context = Context::getContext();
        $fsrt = new Fsregeneratethumbs();
        $this->logger->clear();

        if ($this->hasAccess('edit')) {
            $type = Tools::getValue('fsrt_type', 'all');
            $format = Tools::getValue('fsrt_format', 'all');
            $image_formats_by_type = $fsrt->image_formats_by_type;

            foreach ($image_formats_by_type as $type_key => $types) {
                if ($type != 'all' && $type_key != $type) {
                    unset($image_formats_by_type[$type_key]);
                    continue;
                }

                if ($format != 'all') {
                    foreach ($types as $format_key => $format_a) {
                        if ($format_a['name'] != $format) {
                            unset($image_formats_by_type[$type_key][$format_key]);
                        }
                    }
                }

                if (!count($image_formats_by_type[$type_key])) {
                    unset($image_formats_by_type[$type_key]);
                }
            }

            $context->smarty->assign(array(
                'image_formats_by_type' => $image_formats_by_type,
                'image_types' => $fsrt->image_types
            ));

            if ($fsrt->isPs15()) {
                $this->content = $fsrt->display($fsrt->getModuleFile(), 'views/templates/admin/queue_15.tpl');
            } else {
                $this->content = $fsrt->display($fsrt->getModuleFile(), 'views/templates/admin/queue.tpl');
            }
        } else {
            $this->errors[] = $fsrt->l('Access denied');
        }
    }

    public function ajaxProcessGeneratethumbnail()
    {
        $response = array(
            'has_more' => false,
            'has_error' => false,
            'progress_bar_percent' => 0,
            'processed_count' => 0,
            'total_count' => 0,
        );

        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $process_step = 5;
        $generate_high_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

        $type = Tools::getValue('fsrt_type', false);
        $format = Tools::getValue('fsrt_format', false);
        $offset = Tools::getValue('fsrt_offset');
        $response['processed_count'] = $offset;

        if ($type && $format) {
            $image_type = ImageType::getByNameNType($format, $type);
            if (!$image_type) {
                $image_type = $this->module->getImageType($format, $type);
            }
            $dir = $this->getDirByType($type);

            if ($response['processed_count'] < 1) {
                $this->regenerateNoPictureImages($dir, $image_type);
            }

            if ($type == 'products') {
                if ($response['processed_count'] < 1) {
                    $images = Db::getInstance()->executeS(
                        'SELECT COUNT(`id_image`) as counter FROM `'.
                        _DB_PREFIX_.'image_shop` WHERE `id_shop` = '.(int)$id_shop
                    );

                    if (isset($images[0]['counter'])) {
                        $context->cookie->fsrt_total_count = $images[0]['counter'];
                        $context->cookie->write();
                    }
                }

                $response['total_count'] = $context->cookie->fsrt_total_count;
                $images = Db::getInstance()->executeS(
                    'SELECT `id_image` FROM `'._DB_PREFIX_.'image_shop` WHERE `id_shop` = '.(int)$id_shop.
                    ' ORDER BY `id_image` ASC LIMIT '.(int)$offset.', '.(int)$process_step
                );

                $watermark_modules = Db::getInstance()->executeS('
                SELECT m.`name` FROM `'._DB_PREFIX_.'module` m
                LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
                LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
                WHERE h.`name` = \'actionWatermark\' AND m.`active` = 1');

                foreach ($images as $id_image) {
                    $image = new Image($id_image['id_image']);
                    $existing_img = $dir.$image->getExistingImgPath().'.jpg';

                    if (file_exists(
                        $dir.$image->getExistingImgPath().'-'.Tools::stripslashes($image_type['name']).'.jpg'
                    )) {
                        unlink($dir.$image->getExistingImgPath().'-'.Tools::stripslashes($image_type['name']).'.jpg');
                    }

                    if (file_exists($existing_img) && filesize($existing_img)) {
                        $this->tryToResizeProductImage(
                            $existing_img,
                            $dir.$image->getExistingImgPath().'-'.Tools::stripslashes($image_type['name']).'.jpg',
                            (int)$image_type['width'],
                            (int)$image_type['height'],
                            $image->id_product
                        );

                        if ($generate_high_dpi_images) {
                            $this->tryToResizeProductImage(
                                $existing_img,
                                $dir.$image->getExistingImgPath().'-'.Tools::stripslashes($image_type['name']).'2x.jpg',
                                (int)$image_type['width'] * 2,
                                (int)$image_type['height'] * 2,
                                $image->id_product
                            );
                        }

                        if ($watermark_modules && count($watermark_modules)) {
                            foreach ($watermark_modules as $module) {
                                $module_instance = Module::getInstanceByName($module['name']);
                                if ($module_instance && is_callable(array($module_instance, 'hookActionWatermark'))) {
                                    try {
                                        call_user_func(
                                            array($module_instance, 'hookActionWatermark'),
                                            array(
                                                'id_image' => $image->id,
                                                'id_product' => $image->id_product,
                                                'image_type' => array($image_type)
                                            )
                                        );
                                    } catch (Exception $e) {
                                        $system_message = $e->getMessage();
                                        $msg = sprintf(
                                            $this->module->l(
                                                'Watermark error on image (%s) for product ID %s'
                                            ),
                                            $existing_img,
                                            $image->id_product
                                        );

                                        if ($system_message) {
                                            $msg .= $this->module->l('System Error Message').': ';
                                            $msg .= $system_message;
                                        }

                                        $this->logger->log($msg);
                                    }
                                }
                            }
                        }
                    }

                    unset($image);
                    $response['processed_count']++;
                }
            } elseif (in_array($type, array('categories', 'manufacturers', 'suppliers', 'stores', 'scenes'))) {
                $type_name = $this->module->image_types[$type];
                $sqls = array(
                    'categories' => 'SELECT `id_category` as `id` FROM `'._DB_PREFIX_.
                        'category_shop` WHERE `id_shop` = '.(int)$id_shop,
                    'manufacturers' => 'SELECT `id_manufacturer` as `id` FROM `'._DB_PREFIX_.
                        'manufacturer_shop` WHERE `id_shop` = '.(int)$id_shop,
                    'suppliers' => 'SELECT `id_supplier` as `id` FROM `'._DB_PREFIX_.
                        'supplier_shop` WHERE `id_shop` = '.(int)$id_shop,
                    'stores' => 'SELECT `id_store` as `id` FROM `'._DB_PREFIX_.
                        'store_shop` WHERE `id_shop` = '.(int)$id_shop,
                    'scenes' => 'SELECT `id_scene` as `id` FROM `'._DB_PREFIX_.
                        'scene_shop` WHERE `id_shop` = '.(int)$id_shop,
                );
                $sql = $sqls[$type];

                if ($response['processed_count'] < 1) {
                    $images = Db::getInstance()->executeS($sql);
                    $context->cookie->fsrt_total_count = count($images);
                    $context->cookie->write();
                }

                $response['total_count'] = $context->cookie->fsrt_total_count;
                $images = Db::getInstance()->executeS(
                    $sql.' ORDER BY `id` ASC LIMIT '.(int)$offset.', '.(int)$process_step
                );

                foreach ($images as $image) {
                    $existing_img = $dir.$image['id'].'.jpg';
                    if (file_exists($existing_img) && filesize($existing_img)) {
                        if (file_exists($dir.$image['id'].'-'.Tools::stripslashes($image_type['name']).'.jpg')) {
                            unlink($dir.$image['id'].'-'.Tools::stripslashes($image_type['name']).'.jpg');
                        }

                        $this->tryToResizeOtherImage(
                            $existing_img,
                            $dir.$image['id'].'-'.Tools::stripslashes($image_type['name']).'.jpg',
                            (int)$image_type['width'],
                            (int)$image_type['height'],
                            $image['id'],
                            $type_name
                        );

                        if ($generate_high_dpi_images) {
                            $this->tryToResizeOtherImage(
                                $existing_img,
                                $dir.$image['id'].'-'.Tools::stripslashes($image_type['name']).'2x.jpg',
                                (int)$image_type['width'] * 2,
                                (int)$image_type['height'] * 2,
                                $image['id'],
                                $type_name
                            );
                        }
                    }

                    $response['processed_count']++;
                }
            }

            if (!$response['total_count']) {
                $response['progress_bar_percent'] = 100;
            } else {
                $response['progress_bar_percent'] =
                    round($response['processed_count'] / $response['total_count'] * 100, 0);
            }
            if ($response['processed_count'] < $response['total_count']) {
                $response['has_more'] = true;
            }
        }

        if ($this->logger->hasLog()) {
            $response['has_error'] = true;
        }

        if (ob_get_status()) {
            ob_clean();
        }
        echo $this->module->jsonEncode($response);
        exit;
    }

    protected function hasAccess($type)
    {
        $tabAccess = Profile::getProfileAccesses(Context::getContext()->employee->id_profile, 'class_name');

        if (isset($tabAccess['AdminFsregeneratethumbs'][$type])) {
            if ($tabAccess['AdminFsregeneratethumbs'][$type] === '1') {
                return true;
            }
        }
        return false;
    }

    protected function regenerateNoPictureImages($dir, $image_type)
    {
        $errors = false;
        $generate_high_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');
        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $file = $dir.$language['iso_code'].'.jpg';
            if (!file_exists($file)) {
                $file = _PS_PROD_IMG_DIR_.Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT')).'.jpg';
            }
            if (file_exists($dir.$language['iso_code'].'-default-'.Tools::stripslashes($image_type['name']).'.jpg')) {
                unlink($dir.$language['iso_code'].'-default-'.Tools::stripslashes($image_type['name']).'.jpg');
            }

            if (!ImageManager::resize(
                $file,
                $dir.$language['iso_code'].'-default-'.Tools::stripslashes($image_type['name']).'.jpg',
                (int)$image_type['width'],
                (int)$image_type['height']
            )) {
                $errors = true;
            }

            if ($generate_high_dpi_images) {
                if (!ImageManager::resize(
                    $file,
                    $dir.$language['iso_code'].'-default-'.Tools::stripslashes($image_type['name']).'2x.jpg',
                    (int)$image_type['width'] * 2,
                    (int)$image_type['height'] * 2
                )) {
                    $errors = true;
                }
            }
        }

        return $errors;
    }

    protected function tryToResize($src_file, $dst_file, $dst_width = null, $dst_height = null)
    {
        $response = array(
            'status' => false,
            'message' => '',
        );

        try {
            $response['status'] = ImageManager::resize(
                $src_file,
                $dst_file,
                $dst_width,
                $dst_height
            );
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    protected function tryToResizeProductImage($src_file, $dst_file, $dst_width, $dst_height, $id_product)
    {
        $resize_response = $this->tryToResize($src_file, $dst_file, $dst_width, $dst_height);

        if (!$resize_response['status']) {
            $msg = sprintf(
                $this->module->l('Original image is corrupt (%s) for product ID %s'),
                $src_file,
                $id_product
            );

            if ($resize_response['message']) {
                $msg .= $this->module->l('System Error Message').': ';
                $msg .= $resize_response['message'];
            }

            $this->logger->log($msg);
        }

        return $resize_response['status'];
    }

    protected function tryToResizeOtherImage($src_file, $dst_file, $dst_width, $dst_height, $id_object, $type_name)
    {
        $resize_response = $this->tryToResize($src_file, $dst_file, $dst_width, $dst_height);

        if (!$resize_response['status']) {
            $msg = sprintf(
                $this->module->l('Original image is corrupt (%s) for type %s ID %s'),
                $src_file,
                $type_name,
                $id_object
            );

            if ($resize_response['message']) {
                $msg .= $this->module->l('System Error Message').': ';
                $msg .= $resize_response['message'];
            }

            $this->logger->log($msg);
        }

        return $resize_response['status'];
    }

    protected function getDirByType($type)
    {
        $dirs = array(
            'categories' => _PS_CAT_IMG_DIR_,
            'manufacturers' => _PS_MANU_IMG_DIR_,
            'suppliers' => _PS_SUPP_IMG_DIR_,
            'stores' => _PS_STORE_IMG_DIR_,
            'products' => _PS_PROD_IMG_DIR_
        );

        return $dirs[$type];
    }
}
