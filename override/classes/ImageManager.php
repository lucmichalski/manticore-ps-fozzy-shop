<?php

class ImageManager extends ImageManagerCore
{
    /**
     * Ресай делается на новом сервере
     *
     * @param string $sourceFile Image object from $_FILE
     * @param string $destinationFile Destination filename
     * @param int $destinationWidth Desired width (optional)
     * @param int $destinationHeight Desired height (optional)
     * @param string $fileType Desired file_type (may be override by PS_IMAGE_QUALITY)
     * @param bool $forceType Don't override $file_type
     * @param int $error Out error code
     * @param int $targetWidth Needed by AdminImportController to speed up the import process
     * @param int $targetHeight Needed by AdminImportController to speed up the import process
     * @param int $quality Needed by AdminImportController to speed up the import process
     * @param int $sourceWidth Needed by AdminImportController to speed up the import process
     * @param int $sourceHeight Needed by AdminImportController to speed up the import process
     *
     *@return bool Operation result
     */
    public static function resize(
        $sourceFile,
        $destinationFile,
        $destinationWidth = null,
        $destinationHeight = null,
        $fileType = 'jpg',
        $forceType = false,
        &$error = 0,
        &$targetWidth = null,
        &$targetHeight = null,
        $quality = 5,
        &$sourceWidth = null,
        &$sourceHeight = null
    ) {
        // для картинки не от товаров используем стандартные методы
        if (false===stripos($destinationFile,'img/p/')) {
            $resize = parent::resize($sourceFile, $destinationFile, $destinationWidth, $destinationHeight, $fileType, $forceType,
                $error, $targetWidth, $targetHeight, $quality, $sourceWidth, $sourceHeight
            );
            if (false===$resize) return $resize;
        }
        if (false!==stripos($destinationFile,'_default.')) {
            // не уменьшаем на этом сервере
            return true;
        }

        header('X-destinationFile: '.$destinationFile);

        $sendFile = str_replace(_PS_ROOT_DIR_, '', $destinationFile);

        header('X-imageFile: '.$sendFile);

        try {
            header('X-sourceFile: '.$sourceFile);
            if (!file_exists($sourceFile)) {
                throw new ImageManagerException('No found file', 404);
            }

            if (!extension_loaded('ssh2')) {
                throw new ImageManagerException('No found ssh2_connect', 501);
            }

            $sftp = Image::getImageConnect();

            if (!ssh2_sftp_mkdir($sftp, dirname($sendFile), 0777, 1)){
                throw new ImageManagerException('no created dir '.dirname($sendFile), 404);

            }

            if (!copy($sourceFile, 'ssh2.sftp://' . $sftp . $sendFile)){
                throw new ImageManagerException('fail load file to '.$sendFile, 404);
            }

            ssh2_sftp_chmod($sftp, $sendFile, 0644);

            //$statinfo = ssh2_sftp_stat($sftp, $sendFile);
            //header('X-Stats: '.json_encode($statinfo));

            if (file_exists($sourceFile)) unlink($sourceFile);
            if (file_exists($destinationFile)) unlink($destinationFile);

            $fileExists = file_exists('ssh2.sftp://' . $sftp . $sendFile );
            return $fileExists;

        } catch (Exception $e) {
            header('X-Error: '.$e->getMessage());
            //echo 'Error: '.$e->getMessage().PHP_EOL;
            return false;
        }

        return false;
    }
}

class ImageManagerException extends Exception {}