<?
/*

 * Image-Creator / Sample
 * Part of PHP-Barcode 0.4
 
 * (C) 2001,2002,2003,2004 by Folke Ashberg <folke@ashberg.de>
 
 * The newest version can be found at http://www.ashberg.de/php-barcode
 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

 */

include('BarcodeGenerator.php');
include('BarcodeGeneratorPNG.php');
include('BarcodeGeneratorSVG.php');
include('BarcodeGeneratorJPG.php');
include('BarcodeGeneratorHTML.php'); 

function getvar($name){
    global $_GET, $_POST;
    if (isset($_GET[$name])) return $_GET[$name];
    else if (isset($_POST[$name])) return $_POST[$name];
    else return false;
}

if (get_magic_quotes_gpc()){
    $code=stripslashes(getvar('code'));
} else {
    $code=getvar('code');
}
if (get_magic_quotes_gpc()){
    $height=stripslashes(getvar('height'));
} else {
    $height=getvar('height');
}
if (!$height) $height=80;
if (!$code) $code='123456789012';
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
//header("Content-Type: text/html");
print $generatorPNG->getBarcode($code, $generatorPNG::TYPE_CODE_128,2,$height);
//barcode_print_c($code,getvar('encoding'),getvar('scale'),getvar('mode'));



?>
