<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_inventory.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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

$category_id = mosgetparam($_REQUEST, 'category_id', null );
$allproducts = mosgetparam($_REQUEST, 'allproducts', 0 );

require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

// Check to see if this is a search or a browse by category
// Default is to show all products
if( !empty($category_id)) {
	$list  = "SELECT * FROM #__{vm}_product, #__{vm}_product_category_xref WHERE ";
	$count  = "SELECT count(*) as num_rows FROM #__{vm}_product, 
		#__{vm}_product_category_xref WHERE ";
	$q  = "#__{vm}_product.vendor_id = '$ps_vendor_id' ";
	$q .= "AND #__{vm}_product_category_xref.category_id='$category_id' "; 
	$q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
	$q .= "AND product_in_stock > 0 ";
	$q .= "ORDER BY product_name ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;
}
elseif( !empty($keyword)) {
	$list  = "SELECT * FROM #__{vm}_product WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_product WHERE ";
	$q  = "#__{vm}_product.vendor_id = '$ps_vendor_id' ";
	$q .= "AND (#__{vm}_product.product_name LIKE '%$keyword%' OR ";
	$q .= "#__{vm}_product.product_sku LIKE '%$keyword%' OR ";
	$q .= "#__{vm}_product.product_s_desc LIKE '%$keyword%' OR ";
	$q .= "#__{vm}_product.product_desc LIKE '%$keyword%'";
	$q .= ") ";
	$q .= "AND product_in_stock > 0 ";
	$q .= "ORDER BY product_name ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else {
	$list  = "SELECT * FROM #__{vm}_product WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_product WHERE ";
	$q  = "#__{vm}_product.vendor_id = '$ps_vendor_id' ";
	if ($allproducts != 1) 
		$q .= "AND product_in_stock > 0 ";
	$q .= "ORDER BY product_name ";
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
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_PRODUCT_INVENTORY_LBL, IMAGEURL."ps_image/inventory.gif", $modulename, "product_inventory");

echo '&nbsp;&nbsp;';
if($allproducts != 1) echo '<a href="'.$sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=$page&allproducts=1").'" title="'.$VM_LANG->_PHPSHOP_LIST_ALL_PRODUCTS.'">';
echo $VM_LANG->_PHPSHOP_LIST_ALL_PRODUCTS;
if ($allproducts != 1) echo '</a>';

echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
if ($allproducts == 1) echo '<a href="'.$sess->url($_SERVER['PHP_SELF']."?pshop_mode=admin&page=$page&allproducts=0").'" title="'.$VM_LANG->_PHPSHOP_HIDE_OUT_OF_STOCK.'">';
echo $VM_LANG->_PHPSHOP_HIDE_OUT_OF_STOCK;
if ($allproducts == 1) '</a>';
echo '<br /><br />';

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_NAME => '',
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_SKU => '',
					$VM_LANG->_PHPSHOP_PRODUCT_INVENTORY_STOCK => '',
					$VM_LANG->_PHPSHOP_PRODUCT_INVENTORY_PRICE => '',
					$VM_LANG->_PHPSHOP_PRODUCT_INVENTORY_WEIGHT => '',
				);
$listObj->writeTableHeader( $columns );

$db->query($list);
$i = 0;
while ($db->next_record()) {
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	$url = $_SERVER['PHP_SELF'] . "?pshop_mode=admin&page=$modulename.product_form&product_id=" . $db->f("product_id");
	if ($db->f("product_parent_id")) {
		$url .= "&product_parent_id=" . $db->f("product_parent_id");
	}
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">". $db->f("product_name"). "</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $db->f("product_sku") );
	$listObj->addCell( $db->f("product_in_stock") );
	$price=$ps_product->get_price($db->f("product_id"));
	if ($price) {
		if (!empty($price["item"])) {
			$tmp_cell = $price["product_price"];
		} 
		else {
			$tmp_cell = "none";
		} 
	} 
	else {
		$tmp_cell = "none";
	} 
	$listObj->addCell( $tmp_cell );
       
	$listObj->addCell( $db->f("product_weight") );

	$i++;
}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword, "&allproducts=$allproducts" );

?>