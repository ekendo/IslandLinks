<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: order.order_status_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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

if (!empty($keyword)) {
	$list  = "SELECT * FROM #__{vm}_order_status WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_order_status WHERE ";
	$q  = "(order_status_code LIKE '%$keyword%' ";
	$q .= "OR order_status_name LIKE '%$keyword%' ";
	$q .= ") ";
	if( !$perm->check( "admin" ))
		$q .= "AND vendor_id='$ps_vendor_id' ";
	$q .= "ORDER BY list_order ASC";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else {
	$q = "";
	$list  = "SELECT * FROM #__{vm}_order_status WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_order_status WHERE ";
	$q .= "vendor_id='$ps_vendor_id' ";
	$q .= "ORDER BY list_order ASC";
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
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_ORDER_STATUS_LIST_MNU, "", $modulename, "order_status_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_ORDER_STATUS_LIST_NAME => '',
					$VM_LANG->_PHPSHOP_ORDER_STATUS_LIST_CODE => '',
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
	$listObj->addCell( mosHTML::idBox( $i, $db->f("order_status_id"), false, "order_status_id" ) );

	$tmp_cell = "<a href=\"".$sess->url($_SERVER['PHP_SELF'] . "?page=$modulename.order_status_form&limitstart=$limitstart&keyword=$keyword&order_status_id=".$db->f("order_status_id"))."\">".$db->f("order_status_name")."</a>";
	$listObj->addCell( $tmp_cell );
	
    $listObj->addCell( $db->f("order_status_code"));
	
	$listObj->addCell( $ps_html->deleteButton( "order_status_id", $db->f("order_status_id"), "OrderStatusDelete", $keyword, $limitstart ) );

	$i++;

}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );
?>