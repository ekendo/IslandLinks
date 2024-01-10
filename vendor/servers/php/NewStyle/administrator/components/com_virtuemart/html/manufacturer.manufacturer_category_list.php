<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: manufacturer.manufacturer_category_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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
	$list  = "SELECT * FROM #__{vm}_manufacturer_category WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_manufacturer_category WHERE ";
	$q  = "(mf_category_name LIKE '%$keyword%' OR ";
	$q .= "mf_category_desc LIKE '%$keyword%'";
	$q .= ") ";
	$q .= "ORDER BY mf_category_name ASC ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else {
	$q = "";
	$list  = "SELECT * FROM #__{vm}_manufacturer_category ORDER BY mf_category_name ASC ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_manufacturer_category"; 
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
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_MANUFACTURER_CAT_LIST_LBL, "", $modulename, "manufacturer_category_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_MANUFACTURER_CAT_NAME => 'width="21%"',
					$VM_LANG->_PHPSHOP_MANUFACTURER_CAT_DESCRIPTION => 'width="66%"',
					$VM_LANG->_PHPSHOP_MANUFACTURER_CAT_MANUFACTURERS => 'width="13%"',
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
	$listObj->addCell( mosHTML::idBox( $i, $db->f("mf_category_id"), false, "mf_category_id" ) );
	
	$url = $_SERVER['PHP_SELF']."?page=$modulename.manufacturer_category_form&limitstart=$limitstart&keyword=$keyword&mf_category_id=";
	$url .= $db->f("mf_category_id");
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">". $db->f("mf_category_name")."</a><br />";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $db->f("mf_category_desc") );
    
	$url = $_SERVER['PHP_SELF']."?page=$modulename.manufacturer_list&mf_category_id=". $db->f("mf_category_id");
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">".$VM_LANG->_PHPSHOP_MANUFACTURER_LIST_LBL."</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $ps_html->deleteButton( "mf_category_id", $db->f("mf_category_id"), "manufacturerCategoryDelete", $keyword, $limitstart ) );

	$i++;

} 
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );

?>
