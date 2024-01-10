<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_product_type_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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

$product_id = mosgetparam($_REQUEST, 'product_id', 0);
$product_parent_id = mosgetparam($_REQUEST, 'product_parent_id', 0);
global $ps_product_type;

require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

$q  = "SELECT * FROM #__{vm}_product_type,#__{vm}_product_product_type_xref ";
$q .= "WHERE #__{vm}_product_type.product_type_id=#__{vm}_product_product_type_xref.product_type_id ";
$q .= "AND product_id='".$product_id."' ";
$q .= "ORDER BY product_type_list_order asc ";
$db->setQuery($q);   
$db->query();

// Create the List Object with page navigation
$listObj = new listFactory( );

$title = $VM_LANG->_PHPSHOP_PRODUCT_PRODUCT_TYPE_LIST_LBL;
if (!empty($product_parent_id)) {
  $title .= " Item: ";
} else {
  $title .= " Product: ";
}
$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&product_id=$product_id&product_parent_id=$product_parent_id";
$title .= "<a href=\"" . $sess->url($url) . "\">". $ps_product->get_field($product_id,"product_name")."</a>";

// print out the search field and a list heading
$listObj->writeSearchHeader( $title, IMAGEURL."ps_image/product_code.png", $modulename, "product_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$db->num_rows().")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_NAME => 'width="25%"',
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_DESCRIPTION => 'width="30%"',
					$VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_PARAMETERS => 'width="15%"',
					$VM_LANG->_PHPSHOP_PRODUCTS_LBL => 'width="15%"',
					_E_REMOVE => "width=\"10%\""
				);
$listObj->writeTableHeader( $columns );

$i = 0;
while ($db->next_record()) {
	$product_count = $ps_product_type->product_count($db->f("product_type_id"));
	$parameter_count = $ps_product_type->parameter_count($db->f("product_type_id"));
	
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $i+1 );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("product_type_id"), false, "product_type_id" ) );
	
	$tmp_cell = "<a href=\"" . $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_type_form&product_type_id=" . $db->f("product_type_id"). "\">". $db->f("product_type_name") . "</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $db->f("product_type_description"));
      
	$tmp_cell = $parameter_count . " " . $VM_LANG->_PHPSHOP_PARAMETERS_LBL 
			. " <a href=\"". $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_type_parameter_list&product_type_id=". $db->f("product_type_id") . "\">[ ".$VM_LANG->_PHPSHOP_SHOW." ]</a>";
	$listObj->addCell( $tmp_cell );
	
	$tmp_cell = $product_count ." ". $VM_LANG->_PHPSHOP_PRODUCTS_LBL
			."&nbsp;<a href=\"". $_SERVER['PHP_SELF'] . "?option=com_virtuemart&page=product.product_list&product_type_id=" . $db->f("product_type_id"). "\">[ ".$VM_LANG->_PHPSHOP_SHOW." ]</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $ps_html->deleteButton( "product_type_id", $db->f("product_type_id"), "productProductTypeDelete", $keyword, $limitstart, "&product_id=". $product_id ) );

	$i++;
}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword, "&product_id=".$product_id );

?>