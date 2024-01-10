<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_attribute_list.php,v 1.5 2005/10/23 19:14:43 soeren_nb Exp $
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

$product_parent_id = mosgetparam($_REQUEST, 'product_parent_id', 0);
$return_args = mosgetparam($_REQUEST, 'return_args');
$product_id = mosgetparam($_REQUEST, 'product_id', 0);

if (!empty($product_parent_id)) {
  $title = $VM_LANG->_PHPSHOP_ATTRIBUTE_LIST_LBL. " - Product:";
} else {
  $title = $VM_LANG->_PHPSHOP_ATTRIBUTE_LIST_LBL. " - Item:";
}
$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&product_id=$product_id&product_parent_id=$product_parent_id";
$title .= "<a href=\"" . $sess->url($url) . "\">". $ps_product->get_field($product_id,"product_name") ."</a>"; 

$q = "SELECT * FROM #__{vm}_product_attribute_sku WHERE product_id = '$product_id' ";
$q .= "ORDER BY attribute_list,attribute_name";
$db->query($q);

// Create the List Object with page navigation
$listObj = new listFactory();

// print out the search field and a list heading
$listObj->writeSearchHeader( $title, IMAGEURL."ps_image/product_code.png", $modulename, "product_attribute_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$db->num_rows().")\" />" => 'width="5%"',
					$VM_LANG->_PHPSHOP_ATTRIBUTE_LIST_NAME => 'width="30%"',
					$VM_LANG->_PHPSHOP_ATTRIBUTE_LIST_ORDER => 'width="45%"',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

$i = 0;
while ($db->next_record()) { 
        
	$attribute_name = $db->f("attribute_name");
	$url_att_name = urlencode($attribute_name);
	
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $i+1 );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, urlencode($db->f("attribute_name")), false, "attribute_name" ) );
	
	$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_attribute_form&limitstart=$limitstart&keyword=$keyword&product_id=" . $product_id . "&attribute_name=" . urlencode($db->f("attribute_name")) . "&return_args=" . urlencode($return_args);
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">$attribute_name</a>";
	$listObj->addCell( $tmp_cell );
	
    $listObj->addCell( $db->f("attribute_list") );
	
	$listObj->addCell( $ps_html->deleteButton( "attribute_name", $db->f("attribute_name"), "productAttributeDelete", $keyword, $limitstart, "&product_id=$product_id" ) );

	$i++;
}

$listObj->writeTable();

$listObj->writeFooter( "", "&product_id=$product_id&return_args=$return_args" );

$listObj->endTable();

?>