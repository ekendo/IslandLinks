<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.review_list.php,v 1.4.2.1 2006/04/05 18:16:54 soeren_nb Exp $
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

$product_id = mosgetparam($_REQUEST, 'product_id', 0);

$q = "";
$count = "SELECT COUNT(*) AS num_rows ";
$list = "SELECT comment, user_rating,userid,username,time ";
$q .= "FROM #__{vm}_product_reviews,#__users ";
$q .= "WHERE product_id = '$product_id' AND #__users.id=#__{vm}_product_reviews.userid ";
if( !empty( $keyword ))
	$q .= "AND ( comment LIKE '%$keyword%' OR username LIKE '%$keyword%' ) ";
$q .= "ORDER BY userid "; 
$list .= $q ." LIMIT $limitstart, $limit";
$count .= $q;
$db->query($count);
$num_rows = $db->f('num_rows');

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

$title = $VM_LANG->_PHPSHOP_REVIEWS;
$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&product_id=$product_id";
$title .= " :: [ <a href=\"" . $sess->url($url) . "\">". $ps_product->get_field($product_id,"product_name"). "</a> ]";
		  
// print out the search field and a list heading
$listObj->writeSearchHeader( $title, IMAGEURL."ps_image/reviews.gif", $modulename, "review_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					"Name/Date" => 'width="15%"',
					$VM_LANG->_PHPSHOP_REVIEW_COMMENT => 'width="45%"',
					$VM_LANG->_PHPSHOP_RATE_NOM => 'width="25%"',
					_E_REMOVE => 'width="10%"'
				);
$listObj->writeTableHeader( $columns );

$db->query( $list );
$i = 0;
while ($db->next_record()) {

	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("userid"), false, "userid" ) );

	$listObj->addCell( $db->f("username")."</strong><br />(".date("Y-m-d", $db->f("time")).")" );
	$listObj->addCell( substr($db->f("comment"), 0 , 500) );
	$listObj->addCell( '<img src="'. IMAGEURL.'stars/'.$db->f("user_rating").'.gif" border="0" alt="stars" />' );
	
	$listObj->addCell( $ps_html->deleteButton( "userid", $db->f("userid"), "productReviewDelete", $keyword, $limitstart, "&product_id=$product_id" ) );

	$i++;
	
}

$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword, "&product_id=$product_id" );

?>