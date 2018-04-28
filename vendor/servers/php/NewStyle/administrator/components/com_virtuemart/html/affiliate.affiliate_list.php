<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: affiliate.affiliate_list.php,v 1.4 2005/09/29 20:02:18 soeren_nb Exp $
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
global $ps_affiliate;

$vendor_category_id = mosGetParam( $_REQUEST, 'vendor_category_id' );

require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

if (!empty($keyword)) {
	$list  = "SELECT DISTINCT * FROM #__{vm}_affiliate, #__users WHERE ";
	$count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_affiliate, #__users  WHERE ";
	$q  = "((first_name LIKE '%$keyword%') OR (";
	$q  .= "last_name LIKE '%$keyword%') OR (";
	$q  .= "username LIKE '%$keyword%') OR (";
	$q  .= "company LIKE '%$keyword%') OR (";
	$q  .= "name LIKE '%$keyword%')) ";
	$q .= "ORDER BY first_name ASC ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
elseif (!empty($vendor_category_id)) {
	$q = "";
     $list  = "SELECT * FROM #__{vm}_affiliate, #__users WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_affiliate, #__users  WHERE ";
	$q = "user_info_id=user_id ";
	$q .= "ORDER BY first_name ASC ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else {
	$q = "";
	$list  = "SELECT * FROM #__users, #__{vm}_affiliate";
	$list .= " WHERE #__users.user_info_id =#__{vm}_affiliate.user_id";
	//$list .= " ORDER BY company ASC";
	$count = "SELECT count(affiliate_id) as num_rows FROM #__{vm}_affiliate"; 
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
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_AFFILIATE_LIST_LBL, IMAGEURL."ps_image/affiliate.gif", "affiliate", "affiliate_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_AFFILIATE_LIST_AFFILIATE_NAME => "",
					$VM_LANG->_PHPSHOP_AFFILIATE_LIST_AFFILIATE_ACTIVE => "",
					$VM_LANG->_PHPSHOP_AFFILIATE_LIST_MONTH_TOTAL => "",
					$VM_LANG->_PHPSHOP_AFFILIATE_LIST_MONTH_COMMISSION => "",
					$VM_LANG->_PHPSHOP_AFFILIATE_LIST_RATE => "",
					$VM_LANG->_PHPSHOP_AFFILIATE_LIST_ORDERS => "",
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

if (!isset($date)) $date = time();

?>
<h4>Showing Details for <?php echo date("M-Y",$date);?></h4>
<?php
$db->query($list);
$i = 0;


while ($db->next_record()) {

	$affiliate = $ps_affiliate->get_details($date,$db->f("affiliate_id"));
	
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("affiliate_id"), false, "affiliate_di" ) );
	
	$url = SECUREURL . "?page=$modulename.affiliate_form&affiliate_id=";
	$url .= $db->f("affiliate_id");
	$tmp_cell =  "<a href=" . $sess->url($url) . ">". $db->f("first_name")." ".$db->f("last_name")." (".$db->f("username").")</a><br />";
	
	$listObj->addCell( $tmp_cell );


	if($db->f("active")=='Y') 
		$tmp_cell = "Yes"; 
	else 
		$tmp_cell= "No";
	$listObj->addCell( $tmp_cell );


	if (!empty($affiliate["orders_total"])) 
		$tmp_cell = $affiliate["orders_total"];
	else 
		$tmp_cell = "no sales";
	$listObj->addCell( $tmp_cell );
    
	
	if (!empty($affiliate["commission_total"]))
		$tmp_cell = $affiliate["commission_total"];
	else 
		$tmp_cell = "no sales"; 
	$listObj->addCell( $tmp_cell );


    $listObj->addCell( $affiliate["rate"]."%" );
	
	$url = SECUREURL . "?page=$modulename.affiliate_orders_detail&affiliate_id=";
	$url .= $db->f("affiliate_id");
	$url.="&date=".$date;
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">List Orders</a><br />";
	$listObj->addCell( $tmp_cell );
	
	$listObj->addCell( $ps_html->deleteButton( "user_info_id", $db->f("user_id"), "affiliatedelete", $keyword, $limitstart ) );

	$i++;
} 
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );
?>
  
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
	<input type="hidden" name="user_id" value="<?php $db->sp("user_id"); ?>" />
	<input type="hidden" name="date" value="<?php echo isset($date) ? $date : ""; ?>" /> 
	<input type="hidden" name="page" value="<?php echo $modulename?>.affiliate_list" /> 
	<input type="hidden" name="option" value="com_virtuemart" /> 
	<input type="hidden" name="task" value="" /> 
	<br/>Month
	<select class="inputbox" name="date" size="1"><?php
	  for($i=0; $i<12; $i++){ 
		$mytime = mktime(0,0,0,date('m')-$i,1,date('y'));?>
		<option value="<?php echo $mytime ?>" <?php if($mytime == $date) echo "selected"?>><?php 
		echo date('F Y',$mytime); ?>
		</option><?php echo "\n";
	}
	?>
	</select>
	<br/><br/>
	
	<input type="submit" name="submit" class="submit" value="Change View" />

</form>