<?php

class Image extends ImageCore
{
    /**
     * Delete the product image from disk and remove the containing folder if empty
     * Handles both legacy and new image filesystems.
     */
    public function deleteImage($forceDelete = false)
    {
        header('X-deleteImageId: '.$this->id);
        if (!$this->id) {
            return false;
        }
        $sourceFile = $this->image_dir . $this->getExistingImgPath() . '.' . $this->image_format;

        // не трогаем другие фотки
        if (false===stripos($sourceFile,'img/p/')) {
            return parent::deleteImage($forceDelete);
        }

        header('X-sourceFile: '.$sourceFile);
        $file = str_replace(_PS_ROOT_DIR_, '', $sourceFile);
        header('X-file: '.$file);

        $sftp = Image::getImageConnect();
        $fileExists = file_exists('ssh2.sftp://' . $sftp . $file);
        header('X-exists: ' . json_encode($fileExists));

        $filesDels = array();

        if ($fileExists) {
            // Delete base image
            if (unlink('ssh2.sftp://' . $sftp . $file)) {
                $filesDels[] = $file;
            }

            //$statinfo = ssh2_sftp_stat($sftp, $file);
            //header('X-Stats: ' . json_encode($statinfo));
            //var_dump($statinfo);

            // Delete auto-generated images
            $image_types = ImageType::getImagesTypes();
            foreach ($image_types as $imageType) {
                $sourceFile = $this->image_dir . $this->getExistingImgPath() . '-' . $imageType['name'] . '.' . $this->image_format;
                $file = str_replace(_PS_ROOT_DIR_, '', $sourceFile);

                $fileExists = file_exists('ssh2.sftp://' . $sftp . $file );
                if ($fileExists) {
                    // Delete base image
                    if (unlink('ssh2.sftp://' . $sftp . $file)) {
                        $filesDels[] = $file;
                    }
                }
            }

            // Can we delete the image folder?
            $sourceDir = $this->image_dir . $this->getImgFolder();
            $dir = str_replace(_PS_ROOT_DIR_, '', $sourceDir);
            if (is_dir('ssh2.sftp://' . $sftp . $dir)) {
                $deleteFolder = true;
                foreach (scandir($dir, SCANDIR_SORT_NONE) as $file) {
                    if (($file != '.' && $file != '..')) {
                        $deleteFolder = false;
                        break;
                    }
                }
                if ($deleteFolder) {
                    @rmdir('ssh2.sftp://' . $sftp . $dir);
                }
            }
        }
        header('X-filesDels: ' . json_encode($filesDels));

        return parent::deleteImage($forceDelete);
    }

    /**
     * Recursively deletes all product images in the given folder tree and removes empty folders.
     *
     * @param string $path folder containing the product images to delete
     * @param string $format image format
     *
     * @return bool success
     */
    public static function deleteAllImages($path, $format = 'jpg')
    {
        if (!$path || !$format || !is_dir($path)) {
            return false;
        }

        // не трогаем другие фотки
        if (false===stripos($path,'img/p/')) {
            return parent::deleteAllImages($path);
        }

        $sftp = Image::getImageConnect();

        foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
            if (preg_match('/^[0-9]+(\-(.*))?\.' . $format . '$/', $file)) {
                unlink('ssh2.sftp://' . $sftp . $path . $file);
            } elseif (is_dir($path . $file) && (preg_match('/^[0-9]$/', $file))) {
                Image::deleteAllImages($path . $file . '/', $format);
            }
        }

        // Can we remove the image folder?
        if (is_numeric(basename($path))) {
            $removeFolder = true;
            foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
                if (($file != '.' && $file != '..')) {
                    $removeFolder = false;
                    break;
                }
            }

            if ($removeFolder) {
                @rmdir('ssh2.sftp://' . $sftp . $path);
            }
        }

        return true;
    }

    public static function getImageConnect()
    {
        $config = Image::getImageСonfig();
        $ssh2 = ssh2_connect($config['host'], $config['port']);
        if (!$ssh2) throw new ImageException('no connect', 401);

        if (!ssh2_auth_password($ssh2, $config['username'], $config['password'])) {
            throw new ImageException('authorization fail', 401);
        }

        $sftp = ssh2_sftp($ssh2);
        return $sftp;
    }

    public static function getImageСonfig()
    {
        $config = include _PS_ROOT_DIR_ . '/app/config/parameters.php';
        return [
            'host' => $config['parameters']['image_host'],
            'port' => $config['parameters']['image_port'],
            'username' => $config['parameters']['image_username'],
            'password' => $config['parameters']['image_password'],
        ];
    }
}

class ImageException extends Exception {}