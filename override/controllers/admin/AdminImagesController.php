<?php

class AdminImagesController extends AdminImagesControllerCore
{
    /**
     * Delete resized image then regenerate new one with updated settings.
     *
     * @param string $dir
     * @param array $type
     * @param bool $product
     *
     * @return bool
     */
    protected function _deleteOldImages($dir, $type, $product = false)
    {
        // не трогаем другие фотки
        if (false===stripos($dir,'img/p/')) {
            return parent::_deleteOldImages($dir, $type, $product);
        }
        return true;
    }

    /**
     * Regenerate images.
     *
     * @param $dir
     * @param $type
     * @param bool $productsImages
     *
     * @return bool|string
     */
    protected function _regenerateNewImages($dir, $type, $productsImages = false)
    {
        // не трогаем другие фотки
        if (false===stripos($dir,'img/p/')) {
            return parent::_regenerateNewImages($dir, $type, $productsImages);
        }
        return true;
    }

    /**
     * Regenerate no-pictures images.
     *
     * @param $dir
     * @param $type
     * @param $languages
     *
     * @return bool
     */
    protected function _regenerateNoPictureImages($dir, $type, $languages)
    {
        if (false===stripos($dir,'img/p/')) {
            return parent::_regenerateNoPictureImages($dir, $type, $languages);
        }
        return true;
    }

    protected function _regenerateThumbnails($type = 'all', $deleteOldImages = false)
    {
        return false;

        return parent::_regenerateThumbnails($type, $deleteOldImages);
    }
}
