<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_type_list.php,v 1.5.2.2 2006/04/27 19:35:52 soeren_nb Exp $
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
require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );
global $ps_product_type;

$q = "SELECT * FROM #__{vm}_product_type ";
/*    $q .= "WHERE #__{vm}_category_xref.category_parent_id='";
$q .= $category_id . "' AND ";
$q .= "#__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
$q .= "AND #__{vm}_category.vendor_id = $ps_vendor_id ";*/
$q .= "ORDER BY product_type_list_order asc ";

$db->query( $q );
$num_rows = $db->num_rows();

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

$q .= "LIMIT {$pageNav->limitstart}, {$pageNav->limit}";
$db->query( $q );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_PRODUCT_TYPE_LIST_LBL, IMAGEURL."ps_image/categories.gif", $modulename, "product_type_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_NAME => 'width="25%"',
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_DESCRIPTION => 'width="30%"',
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_PARAMETERS => 'width="15%"',
					$VM_LANG->_PHPSHOP_PRODUCTS_LBL => 'width="15%"',
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_PUBLISH => 'width="5%"',
					$VM_LANG->_PHPSHOP_MODULE_LIST_ORDER => 'width="5%"',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );


$i = 0;
while ($db->next_record()) {
	$product_count = $ps_product_type->product_count($db->f("product_type_id"));
	$parameter_count = $ps_product_type->parameter_count($db->f("product_type_id"));
	
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("product_type_id"), false, "product_type_id" ) );
	
	$tmp_cell = "<a href=\"". $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_type_form&product_type_id=" . $db->f("product_type_id") . "\">". $db->f("product_type_name") . "</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell(  $db->f("product_type_description"));

	$tmp_cell = $parameter_count . " " . $VM_LANG->_PHPSHOP_PARAMETERS_LBL . " <a href=\""
			. $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_type_parameter_list&product_type_id="
			. $db->f("product_type_id") . "\">[ ".$VM_LANG->_PHPSHOP_SHOW." ]</a>";
	$listObj->addCell( $tmp_cell );
	
	$tmp_cell = $product_count ." ". $VM_LANG->_PHPSHOP_PRODUCTS_LBL."&nbsp;<a href=\""
			. $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_list&product_type_id=" . $db->f("product_type_id")
			. "\">[ ".$VM_LANG->_PHPSHOP_SHOW." ]</a>";
	$listObj->addCell( $tmp_cell );
      //$listObj->addCell( $db->f("list_order"));

	$listObj->addCell( vmCommonHTML::getYesNoIcon( $db->f("product_type_publish") ) );
	
//      echo "<a href=\"javascript: void(0);\" onClick=\"return listItemTask('cb$i','orderdown')\">";
//      echo "Down</a>";	
	$tmp_cell = "<div align=\"center\">"
			. $pageNav->orderUpIcon( $i, $i > 0, "orderup", "Order Up", $page, "ProductTypeReorder" )
			. "\n&nbsp;" 
			. $pageNav->orderDownIcon( $i, $db->num_rows(), $i-1 <= $db->num_rows(), "orderdown", "Order Down", $page, "ProductTypeReorder" )
			. "</div>";
	$listObj->addCell( $tmp_cell );  
	
	$listObj->addCell( $ps_html->deleteButton( "product_type_id", $db->f("product_type_id"), "ProductTypeDelete", $keyword, $limitstart ) );

	$i++;
}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );
?>