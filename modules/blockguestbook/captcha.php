<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockguestbook
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

header("Expires: Mon, 23 Jul 1993 05:00:00 GMT");// always modified
header("Last-Modified: Mon, 23 Jul 1993 05:00:00 GMT");// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);// HTTP/1.0
header("Pragma: no-cache");
// Создаем строку из трех случайных букв
$codev = chr(rand(48, 57)).chr(rand(48, 57)).chr(rand(48, 57)).chr(rand(48, 57)).chr(rand(48, 57)).chr(rand(48, 57));
// Сохраняем ее в сессию для того, чтобы потом проверить при обработке формы ввода

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');




$name_module = "blockguestbook";

if (version_compare(_PS_VERSION_, '1.5', '<')){
    require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
    setcookie_blockguestbook(array('codev'=>$codev));
} else{
    $cookie = new Cookie($name_module);
    $cookie->secure_code_guestb = $codev;
}



if (!function_exists('imagerotate')) {

    /*
        Imagerotate replacement. ignore_transparent is work for png images
        Also, have some standard functions for 90, 180 and 270 degrees.
        Rotation is clockwise
    */

    function imagerotate_rotateX($x, $y, $theta) {
        return $x * cos($theta) - $y * sin($theta);
    }

    function imagerotate_rotateY($x, $y, $theta) {
        return $x * sin($theta) + $y * cos($theta);
    }

    function imagerotate($srcImg, $angle, $bgcolor = 0, $ignore_transparent = 0) {
        $srcw = imagesx($srcImg);
        $srch = imagesy($srcImg);

        //Normalize angle
        $angle %= 360;
        //Set rotate to clockwise
        $angle = -$angle;

        if ($angle == 0) {
            if ($ignore_transparent == 0) {
                imagesavealpha($srcImg, true);
            }
            return $srcImg;
        }

        // Convert the angle to radians
        $theta = deg2rad($angle);

        //Standart case of rotate
        if ((abs($angle) == 90) || (abs($angle) == 270)) {
            $width = $srch;
            $height = $srcw;
            if (($angle == 90) || ($angle == -270)) {
                $minX = 0;
                $maxX = $width;
                $minY = -$height+1;
                $maxY = 1;
            } else if (($angle == -90) || ($angle == 270)) {
                $minX = -$width+1;
                $maxX = 1;
                $minY = 0;
                $maxY = $height;
            }
        } else if (abs($angle) === 180) {
            $width = $srcw;
            $height = $srch;
            $minX = -$width+1;
            $maxX = 1;
            $minY = -$height+1;
            $maxY = 1;
        } else {
            // Calculate the width of the destination image.
            $temp = array(
                imagerotate_rotateX(0, 0, 0 - $theta),
                imagerotate_rotateX($srcw, 0, 0 - $theta),
                imagerotate_rotateX(0, $srch, 0 - $theta),
                imagerotate_rotateX($srcw, $srch, 0 - $theta)
            );
            $minX = floor(min($temp));
            $maxX = ceil(max($temp));
            $width = $maxX - $minX;

            // Calculate the height of the destination image.
            $temp = array(
                imagerotate_rotateY(0, 0, 0 - $theta),
                imagerotate_rotateY($srcw, 0, 0 - $theta),
                imagerotate_rotateY(0, $srch, 0 - $theta),
                imagerotate_rotateY($srcw, $srch, 0 - $theta)
            );
            $minY = floor(min($temp));
            $maxY = ceil(max($temp));
            $height = $maxY - $minY;
        }

        $destimg = imagecreatetruecolor($width, $height);
        if ($ignore_transparent == 0) {
            imagefill($destimg, 0, 0, imagecolorallocatealpha($destimg, 255,255, 255, 127));
            imagesavealpha($destimg, true);
        }

        // sets all pixels in the new image
        for ($x = $minX; $x < $maxX; $x++) {
            for ($y = $minY; $y < $maxY; $y++) {
                // fetch corresponding pixel from the source image
                $srcX = round(imagerotate_rotateX($x, $y, $theta));
                $srcY = round(imagerotate_rotateY($x, $y, $theta));
                if ($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
                    $color = imagecolorat($srcImg, $srcX, $srcY);
                } else {
                    $color = $bgcolor;
                }
                imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
            }
        }

        return $destimg;
    }

}


// Создаем картинку, на которую положим буквы
$im = imagecreatetruecolor(100, 26);
// Заливаем картинку черным цветом
$black = imagecolorallocate($im, 255, 255, 255);
imagefill($im, 0, 0, $black);
//echo $codev;
// Проходим по буквам
for ($i = 0; $i < Tools::strlen($codev); $i++) {
    // Создаем картиночку для одной буквы
    $char = imagecreatetruecolor(12, 16);
    // Заливаем ее черным цветом
    $black = imagecolorallocate($char, 255, 255, 255);
    imagefill($char, 0, 0, $black);
    // Рисуем букву
    imagestring($char, rand(4, 5),// случайный из двух встроенных шрифтов
    1,
    1,
    Tools::substr($codev, $i, 1),
    //imagecolorallocate($char, rand(0, 5), rand(150, 255), rand(150, 255))
        imagecolorallocate($char, 0, 0, 0)
    );
    // Попорачиваем букву под случайным углом
    $char = imagerotate($char, rand(-10, 10), $black);
    // Переносим ее на итоговую картинку со случайным сдвигом
    //imagecopy($im, $char, 10 + (14 * ($i - 1)) + rand(10, 15), 5 + rand(-4, 3), 0, 0, 11, 15 );
    imagecopy($im, $char, 10 + (14 * ($i - 1)) + 10, 4 , 0, 0, 11, 15 );
    // Чистим мусор
    
}
	//создание рисунка в зависимости от доступного формата
		if (function_exists("imagepng")) {
		   header("Content-type: image/png");
		   imagepng($im);
		} elseif (function_exists("imagegif")) {
		   header("Content-type: image/gif");
		   imagegif($im);
		} elseif (function_exists("imagejpeg")) {
		   header("Content-type: image/jpeg");
		   imagejpeg($im);
		} else {
		   die("No image support in this PHP server!");
		}
		imagedestroy($char);
    


?>