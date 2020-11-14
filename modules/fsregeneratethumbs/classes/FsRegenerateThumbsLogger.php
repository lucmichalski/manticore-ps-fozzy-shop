<?php
/**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class FsRegenerateThumbsLogger
{
    private $module_name = 'fsregeneratethumbs';
    private $log_file;

    public function __construct($filename)
    {
        $this->log_file = _PS_MODULE_DIR_.$this->module_name.'/'.$filename;
    }

    public function log($message)
    {
        $msg = '['.strftime('%Y-%m-%d %H:%M:%S').']: '.$message;
        $file = fopen($this->log_file, 'a');
        fwrite($file, $msg."\n");
        fclose($file);
    }

    public function clear()
    {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
    }

    public function hasLog()
    {
        if (file_exists($this->log_file) && filesize($this->log_file) > 1) {
            return true;
        }
        return false;
    }

    public function download()
    {
        if (!file_exists($this->log_file)) {
            $fh = fopen($this->log_file, 'w');
            fclose($fh);
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($this->log_file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($this->log_file));
        readfile($this->log_file);
        exit;
    }
}
