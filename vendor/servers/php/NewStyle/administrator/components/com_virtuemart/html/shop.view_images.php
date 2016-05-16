<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shop.view_images.php,v 1.4.2.1 2005/12/15 20:59:30 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
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
mm_showMyFileName( __FILE__ );

$flypage = mosGetParam($_REQUEST, "flypage", FLYPAGE);
$product_id = intval(mosgetparam($_REQUEST, "product_id"));
$page = mosgetparam($_REQUEST, "page", null);
$image_id = intval(mosgetparam($_REQUEST, "image_id", "product"));
$Itemid = intval(mosgetparam($_REQUEST, "Itemid"));

if( !empty($product_id) ) {

  require_once( CLASSPATH . "ps_product.php" );
  $ps_product =& new ps_product();
  
// Get the default full and thumb image
  $db->query( "SELECT product_name,product_full_image,product_thumb_image FROM #__{vm}_product WHERE product_id='$product_id'" );
  $db->next_record();
  echo "<h3>".$VM_LANG->_PHPSHOP_AVAILABLE_IMAGES." ".$db->f("product_name")."</h3>\n";
  echo "<a href=\"".$_SERVER['PHP_SELF']."?option=com_virtuemart&page=shop.product_details&flypage=$flypage&product_id=$product_id&Itemid=$Itemid\">"
      . $VM_LANG->_PHPSHOP_BACK_TO_DETAILS."</a>\n<br/><br/><br/>";
  
  $alt = $db->f("product_name");
  $height = PSHOP_IMG_HEIGHT;
  $width = PSHOP_IMG_WIDTH;
  $border = ($image_id == "product") ? "4" : "1";
  $href = $_SERVER['PHP_SELF']."?option=com_virtuemart&page=$page&product_id=$product_id&image_id=product&Itemid=".$Itemid;
  $title = $db->f("product_name");
  echo "<a href=\"$href\" target=\"_self\" title=\"$title\">\n";
  $ps_product->show_image( $db->f("product_thumb_image"), "alt=\"$alt\" align=\"center\" border=\"$border\"");
  echo "</a>&nbsp;&nbsp;&nbsp;";
  $dbi = new ps_DB();
// Let's have a look wether the product has more images.
  $dbi->query( "SELECT * FROM #__{vm}_product_files WHERE file_product_id='$product_id' AND file_is_image='1'" );
  $images = $dbi->record;
  $i = 0;
  foreach( $images as $image ) {
    $info = pathinfo( $image->file_name );
    
    $src = dirname($image->file_url) ."/resized/". basename($image->file_name, ".".$info["extension"])."_".PSHOP_IMG_WIDTH."x".PSHOP_IMG_HEIGHT.".".$info["extension"];
    $alt = $image->file_title;
    $height = empty($image->file_image_thumb_height) ? PSHOP_IMG_HEIGHT : $image->file_image_thumb_height; 
    $width = empty($image->file_image_thumb_width) ? PSHOP_IMG_WIDTH : $image->file_image_thumb_width; 
    
    $border = ($image->file_id == $image_id) ? "4" : "1";
    $href = $_SERVER['PHP_SELF']."?option=com_virtuemart&page=$page&product_id=$product_id&image_id=".$image->file_id."&Itemid=".$Itemid;
    $title = $image->file_title;
    echo "<a href=\"$href\" target=\"_self\" title=\"$title\"><img src=\"$src\" alt=\"$alt\" align=\"center\" width=\"$width\" border=\"$border\" /></a>\n&nbsp;&nbsp;&nbsp;";
    // Break Row when needed
    //if( $i++ >= 4 ) { $i=0; echo "<br/><br/>"; }
  }
  echo "<br/><br/><hr/>\n";
  
  if( $image_id == "product" ) {
    echo "<div style=\"text-align:center;overflow:auto;\">";
    $ps_product->show_image($db->f("product_full_image"), "alt=\"$alt\" align=\"center\" border=\"0\"", 0);
    echo "</div>";
  }
  else {
    if( !empty($image_id) ) {
      // Get that image!
      $dbi->query( "SELECT * FROM #__{vm}_product_files WHERE file_product_id='$product_id' AND file_is_image='1' AND file_id='$image_id'" );
    }
    else {
      // Get the first image
      $dbi->query( "SELECT * FROM #__{vm}_product_files WHERE file_product_id='$product_id' AND file_is_image='1'" );
    }
    $show_img = $dbi->record[0];
    if( $show_img ) {
      $src = str_replace( $mosConfig_absolute_path, $mosConfig_live_site, $show_img->file_name );
      if( strstr( $src, $mosConfig_live_site.$show_img->file_name))
        $src = str_replace( $mosConfig_live_site.$show_img->file_name, $mosConfig_live_site."/".$show_img->file_name, $src );
      $alt = $show_img->file_title;
      $height = $show_img->file_image_height; 
      $width = $show_img->file_image_width;
      echo "<div style=\"text-align:center;overflow:auto;\"><img src=\"$src\" alt=\"$alt\" width=\"$width\" height=\"$height\" border=\"0\" /></div>";
    }
    else {
      echo $VM_LANG->_PHPSHOP_IMAGE_NOT_FOUND;
    }
  }
}
?>
