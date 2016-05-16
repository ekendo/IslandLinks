<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: vendor.vendor_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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

$vendor_category_id= mosgetparam( $_REQUEST, 'vendor_category_id', "" );
  
if (!empty($keyword)) {
	$list  = "SELECT * FROM #__{vm}_vendor WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_vendor WHERE ";
	$q  = "(vendor_name LIKE '%$keyword%' OR ";
	$q .= "vendor_store_desc LIKE '%$keyword%'";
	$q .= ") ";
	$q .= "AND vendor_id > 1 ";
	$q .= "ORDER BY vendor_name ASC ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
elseif (!empty($vendor_category_id)) {
	$q = "";
	$list="SELECT * FROM #__{vm}_vendor, #__{vm}_vendor_category WHERE ";
	$count="SELECT count(*) as num_rows FROM #__{vm}_vendor,#__{vm}_vendor_category WHERE "; 
	$q = "#__{vm}_vendor.vendor_category_id=#__{vm}_vendor_category.vendor_category_id ";
	$q .= "AND vendor_id > 1 ";
	$q .= "ORDER BY #__{vm}_vendor.vendor_name ASC ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else {
	$q = "";
	$list  = "SELECT * FROM #__{vm}_vendor ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_vendor ";
	$q .= "WHERE vendor_id > 1 ORDER BY vendor_name ASC ";
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
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_VENDOR_LIST_LBL, IMAGEURL."ps_image/vendors.gif", $modulename, "vendor_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_VENDOR_LIST_VENDOR_NAME => '',
					$VM_LANG->_PHPSHOP_VENDOR_LIST_ADMIN => '',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

$db->query($list);
$i = 0;
while ($db->next_record()) {

	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("vendor_id"), false, "vendor_id" ) );
    
	$url = $_SERVER['PHP_SELF']."?page=$modulename.vendor_form&limitstart=$limitstart&keyword=$keyword&vendor_id=". $db->f("vendor_id");
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">". $db->f("vendor_name"). "</a>";
	$listObj->addCell( $tmp_cell );
	
    $tmp_cell = '<a href="'. $sess->url($_SERVER['PHP_SELF']."?page=$modulename.vendor_form&vendor_id=" . $db->f("vendor_id")) .'">'. $VM_LANG->_PHPSHOP_UPDATE .'</a>';
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $ps_html->deleteButton( "vendor_id", $db->f("vendor_id"), "vendorDelete", $keyword, $limitstart ) );

	$i++;

}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );

?>