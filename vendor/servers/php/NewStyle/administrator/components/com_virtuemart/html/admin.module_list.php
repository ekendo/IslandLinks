<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: admin.module_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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

	$list  = "SELECT * FROM #__{vm}_module WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_module WHERE ";
	$q  = "(module_name LIKE '%$keyword%' OR ";
	$q .= "module_description LIKE '%$keyword%'";
	$q .= ") ";
	$q .= "ORDER BY list_order ASC ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else {
	$q = "";
	$list  = "SELECT * FROM #__{vm}_module ORDER BY list_order ASC ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_module ORDER BY list_order "; 
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
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_MODULE_LIST_LBL, IMAGEURL."ps_image/modules.gif", "admin", "module_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_MODULE_LIST_NAME => "",
					$VM_LANG->_PHPSHOP_MODULE_LIST_PERMS => "",
					$VM_LANG->_PHPSHOP_MODULE_LIST_FUNCTIONS => "",
					$VM_LANG->_PHPSHOP_MODULE_LIST_ORDER => "",
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
	$listObj->addCell( mosHTML::idBox( $i, $db->f("module_id"), false, "module_id" ) );
	
    $tmp_cell = "<a href=\"". $sess->url( $_SERVER['PHP_SELF'] . "?page=$modulename.module_form&&limitstart=$limitstart&module_id=" . $db->f("module_id"))."\">";
    $tmp_cell .= $db->f("module_name")."</a>";
    $listObj->addCell( $tmp_cell );
	
    $listObj->addCell( $db->f("module_perms") );
	
	$tmp_cell = "<a href=\"".$sess->url($_SERVER['PHP_SELF']."?page=$modulename.function_list&module_id=" . $db->f("module_id"))."\">". $VM_LANG->_PHPSHOP_LIST ."</a>";
    $listObj->addCell( $tmp_cell );
	
    $listObj->addCell( $db->f("list_order") );
	
	$listObj->addCell( $ps_html->deleteButton( "module_id", $db->f("module_id"), "moduleDelete", $keyword, $limitstart ) );

	$i++;
}

$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );

?>