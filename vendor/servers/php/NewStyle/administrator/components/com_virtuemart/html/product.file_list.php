<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* List all files of a specific product
* @author Soeren Eberhardt
* @param int product_id
*
* @version $Id: product.file_list.php,v 1.6 2005/11/22 18:30:08 soeren_nb Exp $
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
*
*/
mm_showMyFileName( __FILE__ );

require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

global $option;
$product_id = mosGetParam($_REQUEST, 'product_id' );
$task = mosGetParam($_REQUEST, 'task' );

$q  = "SELECT product_name FROM #__{vm}_product WHERE product_id = '$product_id'";
$db->query($q);
$db->next_record();
$product_name = '<a href="'.$_SERVER['PHP_SELF'].'?option='.$option.'&amp;product_id='.$product_id.'&amp;page=product.product_form">'.$db->f("product_name").'</a>';

$dbf = new ps_DB;
$sql = 'SELECT attribute_value FROM #__{vm}_product_attribute WHERE `product_id` = \''.$product_id.'\' AND attribute_name=\'download\'';
$dbf->query( $sql );
$dbf->next_record();
	
$q = "SELECT file_id, file_is_image, file_product_id, file_extension, file_url, file_published, file_name, file_title FROM #__{vm}_product_files  ";
$q .= "WHERE file_product_id = '$product_id' ";
$q .= "ORDER BY file_is_image ";
$db->query($q);
$db->next_record();
if( $db->num_rows() < 1 && $task != "cancel" ) {
  mosRedirect( $_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.file_form&product_id=$product_id" );
}
else {
	$num_rows = $db->num_rows();
}
	
// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_FILES_LIST ." " . $product_name, $mosConfig_live_site."/administrator/images/mediamanager.png", $modulename, "file_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => 'width="20"', 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll($num_rows)\" />" => 'width="20"',
					$VM_LANG->_PHPSHOP_FILES_LIST_FILENAME => '',
					$VM_LANG->_PHPSHOP_VIEW => '',
					$VM_LANG->_PHPSHOP_FILES_LIST_FILETITLE => '',
					$VM_LANG->_PHPSHOP_UPDATE => '',
					$VM_LANG->_PHPSHOP_FILES_LIST_FILETYPE => '',
					$VM_LANG->_PHPSHOP_FILEMANAGER_PUBLISHED => '',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

// Reset Result pointer
$db->called=false;

$i = 0;
while ($db->next_record()) {
	
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	$isProductDownload = ($db->f("file_title") == $dbf->f("attribute_value") && $db->f("file_title") != '' ) ? true : false;
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("file_id"), $isProductDownload, "file_id" ) );
	
	if($db->f("file_name")) {
		$tmp_cell = basename($db->f("file_name"));
	}
	else {
		$tmp_cell = basename($db->f("file_url"));
	}
	$listObj->addCell( $tmp_cell );	
	
	$tmp_cell = "";
	if( $db->f("file_is_image")) {
		$fullimg = $db->f("file_name");
		$info = pathinfo( $fullimg );
		$thumb = $info["dirname"] ."/resized/". basename($db->f("file_name"),".".$info["extension"])."_".PSHOP_IMG_WIDTH."x".PSHOP_IMG_HEIGHT.".".$info["extension"];
		$thumburl = str_replace( $mosConfig_absolute_path, $mosConfig_live_site, $thumb );
		if( is_file( $fullimg ) ) {
			$tmp_cell .= $VM_LANG->_PHPSHOP_FILES_LIST_FULL_IMG.": ";
			$tmp_cell .= mm_ToolTip( '&nbsp;<img src="'.$db->f("file_url") . '" alt="Full Image" />', $VM_LANG->_PHPSHOP_FILES_LIST_FULL_IMG, '{mosConfig_live_site}/images/M_images/con_info.png', '', '[ '.$VM_LANG->_PHPSHOP_VIEW . ' ]' ); 
		}
		$tmp_cell .= '<br/>';
		if( is_file( $thumb ) ) {
			$tmp_cell .= $VM_LANG->_PHPSHOP_FILES_LIST_THUMBNAIL_IMG.": ";
			$tmp_cell .= mm_ToolTip( '&nbsp;<img src="'.$thumburl.'" alt="thumbnail" />', $VM_LANG->_PHPSHOP_FILES_LIST_THUMBNAIL_IMG, '{mosConfig_live_site}/images/M_images/con_info.png', '', '[ '.$VM_LANG->_PHPSHOP_VIEW . ' ]' ); 
		}
		if( !$db->f("file_name") ) {
			$tmp_cell = "&nbsp;<a target=\"_blank\" href=\"".$db->f("file_url"). "\">[ ".$VM_LANG->_PHPSHOP_VIEW . " ]</a><br/>"; 
		}
	}
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $db->f("file_title"));
	   
	if( !$isProductDownload )
		$tmp_cell = "<a href=\"". $_SERVER['PHP_SELF']."?option=com_virtuemart&page=product.file_form&product_id=$product_id&file_id=".$db->f("file_id") ."\">"
				. $VM_LANG->_PHPSHOP_FILES_LIST_EDITFILE 
				."</a>&nbsp;";
	else
		$tmp_cell = " - ";
		
    $listObj->addCell( $tmp_cell );	  
	


	$listObj->addCell( $db->f("file_extension") );


	if ($db->f("file_published")=="0") { 
		$tmp_cell = '<img src="'. $mosConfig_live_site .'/administrator/images/publish_x.png" border="0" alt="Publish" />';
	} 
	else { 
		$tmp_cell = '<img src="'. $mosConfig_live_site .'/administrator/images/tick.png" border="0" alt="Unpublish" />';
	} 
	$listObj->addCell( $tmp_cell );
	if( !$isProductDownload )
		$listObj->addCell( $ps_html->deleteButton( "file_id", $db->f("file_id"), "deleteProductFile", $keyword, $limitstart, "&product_id=$product_id" ) );
	else
		$listObj->addCell( "" );
		
	$i++;

}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword,"&product_id=$product_id" );

?>