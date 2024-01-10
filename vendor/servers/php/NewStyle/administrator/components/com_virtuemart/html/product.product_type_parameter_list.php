<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_type_parameter_list.php,v 1.5 2005/09/29 20:02:18 soeren_nb Exp $
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

$product_type_id = mosgetparam($_REQUEST, 'product_type_id', 0);

require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

$q  = "SELECT product_type_name FROM #__{vm}_product_type ";
$q .= "WHERE product_type_id=$product_type_id";
$db->query($q);   
$db->next_record();

$title = $VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_LIST_LBL .": ";
if ($product_type_id && $db->f("product_type_name"))
	$title .= $db->f("product_type_name");
$title .= '<a href="'. $_SERVER['PHP_SELF'] .'?option=com_virtuemart&page=product.product_type_list">['. $VM_LANG->_PHPSHOP_PRODUCT_TYPE_LIST_LBL .']</a>';

$q  = "SELECT * FROM #__{vm}_product_type_parameter ";
$q .= "WHERE product_type_id=$product_type_id ";
$q .= "ORDER BY parameter_list_order asc ";
$q .= "LIMIT $limitstart, $limit";

$db->query($q);   
$num_rows = $db->num_rows();
	
// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader( $title, IMAGEURL."ps_image/categories.gif", $modulename, "product_type_parameter_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_LABEL => 'width="25%"',
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_PARAMETER_FORM_NAME => 'width="20%"',
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_DESCRIPTION => 'width="40%"',
					$VM_LANG->_PHPSHOP_MODULE_LIST_ORDER => 'width="5%"',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

$i = 0;
while ($db->next_record()) {

	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("parameter_name"), false, "parameter_name" ) );
	
    $tmp_cell = "<a href=\"" . $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_type_parameter_form&product_type_id=" . $db->f("product_type_id")."&parameter_name=".$db->f("parameter_name")."&task=edit\">". $db->f("parameter_label") . "</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $db->f("parameter_name") );
	
	$listObj->addCell( $db->f("parameter_description"));
      
	 //      echo "<a href=\"javascript: void(0);\" onClick=\"return listItemTask('cb$i','orderdown')\">";
//      echo "Down</a>";	
	$tmp_cell = "<div align=\"center\">"
			. $pageNav->orderUpIcon( $i, $i > 0 , "orderup", "Order Up", $page, 'ProductTypeReorderParam')
			. "\n&nbsp;" 
			. $pageNav->orderDownIcon( $i, $db->num_rows(), $i-1 <= $db->num_rows(), "orderdown", "Order Down", $page, 'ProductTypeReorderParam' )
			. "</div>";
	$listObj->addCell( $tmp_cell );  
	
	$listObj->addCell( $ps_html->deleteButton( "parameter_name", $db->f("parameter_name"), "ProductTypeDeleteParam", $keyword, $limitstart, "&product_type_id=". $product_type_id ) );

	$i++;
}

$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword, "&product_type_id=". $product_type_id );
?>