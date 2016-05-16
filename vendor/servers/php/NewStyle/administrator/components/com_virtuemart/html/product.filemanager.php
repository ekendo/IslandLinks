<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.filemanager.php,v 1.7 2005/11/22 18:30:08 soeren_nb Exp $
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
require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

$product_id = mosGetParam($_REQUEST, 'product_id' );

if (!empty($keyword)) {
	$list  = "SELECT product_id, product_name, product_sku, product_publish,product_parent_id FROM #__{vm}_product WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_product WHERE ";
	//$q  = "product.vendor_id = '$ps_vendor_id' ";
	$q = "(#__{vm}_product.product_name LIKE '%$keyword%' OR ";
	$q .= "#__{vm}_product.product_sku LIKE '%$keyword%' OR ";
	$q .= "#__{vm}_product.product_s_desc LIKE '%$keyword%' OR ";
	$q .= "#__{vm}_product.product_desc LIKE '%$keyword%'";
	$q .= ") ";
	$q .= "ORDER BY product_name ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;
}
else {
	$list  = "SELECT product_id, product_name, product_sku, product_publish,product_parent_id FROM #__{vm}_product ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_product ";
	//$q  = "WHERE product.vendor_id = '$ps_vendor_id' ";
	$q = "ORDER BY product_name ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;
}
$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");
  
// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_FILEMANAGER_LIST, IMAGEURL."ps_image/mediamanager.png", $modulename, "filemanager");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_NAME => '',
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_SKU => '',
					$VM_LANG->_PHPSHOP_FILEMANAGER_ADD => '',
					$VM_LANG->_PHPSHOP_FILEMANAGER_IMAGES => '',
					$VM_LANG->_PHPSHOP_FILEMANAGER_DOWNLOADABLE => '',
					$VM_LANG->_PHPSHOP_FILEMANAGER_FILES => '',
					$VM_LANG->_PHPSHOP_FILEMANAGER_PUBLISHED => ''
				);
$listObj->writeTableHeader( $columns );
	
$db->query($list);
$i = 0;
$dbp = new ps_DB;
while ($db->next_record()) {
	
	$listObj->newRow();
	
	$tmp_cell = "";
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
      
	// Is the product downloadable?
	$dbp->setQuery( "SELECT attribute_name FROM #__{vm}_product_attribute WHERE product_id='" . $db->f("product_id") . "' AND attribute_name='download'" );
	$dbp->loadObject( $downloadable );
      
	// What Images does the product have ?
	$dbp->setQuery( "SELECT count(file_id) as images FROM #__{vm}_product_files WHERE file_product_id='" . $db->f("product_id") . "' AND file_is_image='1' " );
	$images = array();
	$dbp->loadObject($images);
      
	// What Files does the product have ?
	$dbp->setQuery( "SELECT count(file_id) as files FROM #__{vm}_product_files WHERE file_product_id='" . $db->f("product_id") . "' AND file_is_image='0' " );
	$files = array();
	$dbp->loadObject($files);
	
	if( $db->f("product_parent_id")) {
		$tmp_cell = "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	$tmp_cell .= $db->f("product_name");
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $db->f("product_sku") );
	
	$url = $_SERVER['PHP_SELF']."?page=$modulename.file_list&product_id=" . $db->f("product_id");
	$tmp_cell = "&nbsp;&nbsp;<a href=\"" . $sess->url($url) . "\">[ ".$VM_LANG->_PHPSHOP_FILEMANAGER_ADD." ]</a>";
	$listObj->addCell( $tmp_cell );
	
	$tmp_cell = empty($images->images) ? "0" : $images->images; 
	$listObj->addCell( $tmp_cell );
	
	if (empty($downloadable)) {
		$tmp_cell = '<img src="'. $mosConfig_live_site .'/administrator/images/publish_x.png" border="0" alt="Publish" />';
	} 
	else { 
		$tmp_cell = '<img src="'. $mosConfig_live_site .'/administrator/images/tick.png" border="0" alt="Unpublish" />';
	}
	$listObj->addCell( $tmp_cell );
	
	unset( $downloadable );
    
	$tmp_cell = empty($files->files) ? "0" : $files->files; 
	$listObj->addCell( $tmp_cell );
	
	if ($db->f("product_publish")=="N") { 
		$tmp_cell = '<img src="'. $mosConfig_live_site .'/administrator/images/publish_x.png" border="0" alt="Publish" />';
	} 
	else { 
		$tmp_cell = '<img src="'. $mosConfig_live_site .'/administrator/images/tick.png" border="0" alt="Unpublish" />';
	}
	$listObj->addCell( $tmp_cell );
	
	$i++;
}

$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword, "&task=list" );
  
?>