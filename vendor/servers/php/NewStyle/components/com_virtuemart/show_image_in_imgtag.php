<?php
/**
* Image Resizer & img Tag "Filler"
*
* @author Andreas Martens <heyn@plautdietsch.de>
* @author Patrick Teague <webdude@veslach.com>
*
* @version $Id: show_image_in_imgtag.php,v 1.5 2005/10/27 19:42:53 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
define('_VALID_MOS', 1);
include_once("../../configuration.php");
include_once("../../administrator/components/com_virtuemart/virtuemart.cfg.php");

//	Image2Thumbnail - Klasse einbinden 
include( CLASSPATH . "class.img2thumb.php");

$filename = @basename(urldecode($_REQUEST['filename']));
$filename = IMAGEPATH."product/".$filename;
$newxsize = @$_REQUEST['newxsize'];
$newysize = @$_REQUEST['newysize'];
$maxsize = false;
$bgred = 255;
$bggreen = 255;
$bgblue = 255;

/*
if( !isset($fileout) )
	$fileout="";
if( !isset($maxsize) )
	$maxsize=0;
*/

/* Minimum security */
file_exists( $filename ) or exit();

$fileinfo = pathinfo( $filename );
$file = str_replace(".".$fileinfo['extension'], "", $fileinfo['basename']);
// In class.img2thumb in the function NewImgShow() the extension .jpg will be added to .gif if imagegif does not exist.

// If the image is a gif, and imagegif() returns false then make the extension ".gif.jpg"

if( $fileinfo['extension'] == "gif") {
  if( function_exists("imagegif") ) {
    $ext = ".".$fileinfo['extension'];
    $noimgif="";
  }
  else {
    $ext = ".jpg";
    $noimgif = ".".$fileinfo['extension'];
  }
} 
else {
  $ext =  ".".$fileinfo['extension'];
  $noimgif="";
}

$fileout = IMAGEPATH."/product/resized/".$file."_".PSHOP_IMG_WIDTH."x".PSHOP_IMG_HEIGHT.$noimgif.$ext;

if( file_exists( $fileout ) ) {
  /* We already have a resized image
  * So send the file to the browser */
  switch($ext)
		{
			case ".gif":
				header ("Content-type: image/gif");
				readfile($fileout);
				break;
			case ".jpg":
				header ("Content-type: image/jpeg");
				readfile($fileout);
				break;
			case ".png":
				header ("Content-type: image/png");
				readfile($fileout);
				break;
		}
}
else {
  /* We need to resize the image and Save the new one (all done in the constructor) */
  $neu = new Img2Thumb($filename,$newxsize,$newysize,$fileout,$maxsize,$bgred,$bggreen,$bgblue);
  
  /* Send the file to the browser */
  switch($ext)
		{
			case ".gif":
				header ("Content-type: image/gif");
				readfile($fileout);
				break;
			case ".jpg":
				header ("Content-type: image/jpeg");
				readfile($fileout);
				break;
			case ".png":
				header ("Content-type: image/png");
				readfile($fileout);
				break;
		}
}
?>
