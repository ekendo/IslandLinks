<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_price_list.php,v 1.5.2.1 2006/03/14 18:42:23 soeren_nb Exp $
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

if( is_array( $product_id ))
	$product_id = (int)$product_id[0];

$product_parent_id = mosgetparam($_REQUEST, 'product_parent_id', 0);
$return_args = mosgetparam($_REQUEST, 'return_args');

if (empty($product_parent_id)) {
  $title = $VM_LANG->_PHPSHOP_PRODUCT_LBL;
} else {
  $title = $VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_LBL;
}
$title .=  "<br/>". $VM_LANG->_PHPSHOP_PRICE_LIST_FOR_LBL."&nbsp;&nbsp;";
$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&product_id=$product_id&product_parent_id=$product_parent_id";
$title .=  "<a href=\"" . $sess->url($url) . "\">". $ps_product->get_field($product_id,"product_name")."</a>"; 


$count = "SELECT COUNT(*) ";
$list = "SELECT shopper_group_name,product_price_id,product_price,product_currency,price_quantity_start,price_quantity_end ";
$q = "FROM #__{vm}_shopper_group,#__{vm}_product_price ";
$q .= "WHERE product_id = '$product_id' ";
if( !$perm->check("admin"))
  $q .= "AND #__{vm}_shopper_group.vendor_id = '$ps_vendor_id' ";
$q .= "AND #__{vm}_shopper_group.shopper_group_id = #__{vm}_product_price.shopper_group_id ";
$q .= "ORDER BY shopper_group_name,price_quantity_start, product_price ";

$count .= $q;
$db->query($count);
$num_rows = $db->num_rows();

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

$list .= $q . 'LIMIT '.$pageNav->limitstart.', '.$pageNav->limit;

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($title, IMAGEURL."ps_image/product_code.png", $modulename, "product_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_PRICE_LIST_GROUP_NAME => '',
					$VM_LANG->_PHPSHOP_PRICE_LIST_PRICE => '',
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_CURRENCY => '',
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_QUANTITY_START => 'width="50"',
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_QUANTITY_END => 'width="50"',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

$db->query( $list );
$i = 0;
while ($db->next_record()) {

	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("product_price_id"), false, "product_price_id" ) );
	
	$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_price_form&limitstart=$limitstart&keyword=$keyword&product_price_id=" . $db->f("product_price_id") . "&product_id=$product_id&product_parent_id=$product_parent_id&return_args=" . urlencode($return_args);
	$tmp_cell = "<a href=" . $sess->url($url) . ">". $db->f("shopper_group_name"). "</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $db->f("product_price"));
	$listObj->addCell( $db->f("product_currency"));
	$listObj->addCell( $db->f("price_quantity_start"));
	$listObj->addCell( $db->f("price_quantity_end"));
	
	$listObj->addCell( $ps_html->deleteButton( "product_price_id", $db->f("product_price_id"), "productPriceDelete", $keyword, $limitstart, "&product_id=$product_id&product_parent_id=$product_parent_id&return_args=" . urlencode($return_args) ) );

	$i++;
}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword, "&product_id=$product_id&product_parent_id=$product_parent_id&return_args=$return_args" );
?>