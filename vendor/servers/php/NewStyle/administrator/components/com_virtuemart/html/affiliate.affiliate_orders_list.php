<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: affiliate.affiliate_orders_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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

$start_date = mktime(0,0,0,date("n"),1,date("Y"));
$end_date = mktime(24,0,0,date("n")+1,0,date("Y"));


$affiliate = $ps_affiliate->get_affiliate_details($auth["user_id"]);

$q = "select * from #__{vm}_orders,#__{vm}_affiliate_sale";
$q .=" where #__{vm}_orders.order_id = #__{vm}_affiliate_sale.order_id";
$q .=" and #__{vm}_affiliate_sale.affiliate_id = '".$affiliate["id"]."'";
$q .= " AND cdate BETWEEN $start_date AND $end_date ";
$q .= " LIMIT $limitstart, $limit";

$db->query($q);
$num_rows = $db->num_rows();		

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader("Order Summary ". date('f y',$date), "", "affiliate", "affiliate_orders_list");

echo $affiliate["company"];

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					"order Ref" => "",
					"date Ordered" => "",
					"order Total" => "",
					"commission(rate)" => "",
					"order Status" => "",					
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );
           
while($db->next_record()){ 
      
	$listObj->newRow();

	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("order_id"), false, "order_id" ) );
	
	$listObj->addCell( sprintf("%08d", $db->f("order_id")) );
	
	$tmp_cell = "<a href=\"". $sess->url( $_SERVER['PHP_SELF']. "?page=affiliate.orders_detail&print=1&order_id=".$db->f("order_id")) ."\">View</a>";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( date("d-m-y", $db->f("cdate")));
    $listObj->addCell( sprintf("%1.2f", $db->f("order_subtotal")) );
	$listObj->addCell( sprintf("%1.2f", $db->f("order_subtotal") *$db->f("rate")*0.01) ); 
	
	$listObj->addCell( $db->f("order_status") );
	
	$listObj->addCell( $ps_html->deleteButton( "order_id", $db->f("order_id"), "orderDelete", $keyword, $limitstart ) );

	$i++;
}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );
?>