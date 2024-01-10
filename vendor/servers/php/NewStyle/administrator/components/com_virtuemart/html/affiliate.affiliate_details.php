<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: affiliate.affiliate_details.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
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

search_header($VM_LANG->_PHPSHOP_AFFILIATE_LIST_LBL, 'affiliate', "affiliate_list"); 


// Enable the multi-page search result display
$limitstart = mosgetparam( $_REQUEST, 'limitstart', 0);

  if ($keyword) {
     $list  = "SELECT * FROM #__{vm}_vendor WHERE ";
     $count = "SELECT count(*) as num_rows FROM v#__{vm}_endor WHERE ";
     $q  = "(vendor_name LIKE '%$keyword%' OR ";
     $q .= "vendor_store_desc LIKE '%$keyword%'";
     $q .= ") ";
     $q .= "ORDER BY vendor_name ASC ";
     $list .= $q . " LIMIT $limitstart, " . SEARCH_ROWS;
     $count .= $q;   
  }

  elseif ($vendor_category_id) 
  {
     $q = "";
     $list="SELECT * FROM #__{vm}_vendor, #__{vm}_vendor_category WHERE ";
     $count="SELECT count(*) as num_rows FROM #__{vm}_vendor,#__{vm}_vendor_category WHERE "; 
     $q = "#__{vm}_vendor.vendor_category_id=#__{vm}_vendor_category.vendor_category_id ";
     $q .= "ORDER BY #__{vm}_vendor.vendor_name ASC ";
     $list .= $q . " LIMIT $limitstart, " . SEARCH_ROWS;
     $count .= $q;   
  }
  else 
  {
     $q = "";
     $list  = "SELECT * FROM #__users, #__{vm}_affiliate";
     $list .= " WHERE #__users.user_info_id = #__{vm}_affiliate.user_id";
     $list .= " AND #__users.id = '".$auth["user_id"]."'";
	 $list .= " ORDER BY company ASC";
     $count = "SELECT count(*) as num_rows FROM #__{vm}_affiliate"; 
     $list .= $q . " LIMIT $limitstart, " . SEARCH_ROWS;
     $count .= $q;   
  }
  $db->query($count);
  $db->next_record();
  $num_rows = $db->f("num_rows");

  if ($num_rows == 0) {
     echo $VM_LANG->_PHPSHOP_NO_SEARCH_RESULT;

  }
  else {
?>
<table class="adminlist">

  <tr > 
    <th width="28%"><?php echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_AFFILIATE_NAME ?></th>
    <th width="12%"><?php echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_AFFILIATE_ACTIVE ?></th>
    <th width="18%"><?php echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_MONTH_TOTAL?></th>
    <th width="31%"><?php echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_MONTH_COMMISSION?></th>
    <th width="11%"><?php echo $VM_LANG->_PHPSHOP_AFFILIATE_LIST_ADMIN ?></th>

  </tr>

  <?php
$db->query($list);
$i = 0;

while ($db->next_record()) {
  if ($i++ % 2) 
     $bgcolor=SEARCH_COLOR_1;
  else
     $bgcolor=SEARCH_COLOR_2;
?>
  <tr BGCOLOR=<?php echo $bgcolor ?>> 
    <td width="28%" nowrap>
   <?php
    $url = SECUREURL . "?page=$modulename.affiliate_form&affiliate_id=";
    $url .= $db->f("affiliate_id");
    echo "<A HREF=" . $sess->url($url) . ">";
    echo $db->f("company");
    echo "</A><BR>";
   ?>
   </td>
    <td width="12%"><?php

if($db->f("active")=='Y') echo "Yes"; else echo "No";

 ?></td>
    <td width="18%">
	<?php
	$dbt = new ps_DB;

	$q = "SELECT affiliate_id, SUM(order_subtotal) AS stotal FROM  #__{vm}_orders,#__{vm}_affiliate_sale";
	$q .=" WHERE #__{vm}_orders.order_id = #__{vm}_affiliate_sale.order_id";
	$q .=" AND #__{vm}_affiliate_sale.affiliate_id = '".$db->f("affiliate_id")."'";
	$q .=" GROUP BY affiliate_id";
	$dbt->query($q);
	if($dbt->next_record()){

	 printf("%1.2f",$dbt->f("stotal"));
	 }
	else echo "no sales";

	?>
	</td>
    <td width="31%">
	<?php
	if($dbt->f("stotal")){
	  printf("%1.2f",$dbt->f("stotal") * ($db->f("rate")*0.01));
	}
	else echo "none";
	?>
	</td>
    <td width="11%"><a href="<?php $sess->purl(SECUREURL . "?page=$modulename.affiliate_form&affiliate_id=" . $db->f("affiliate_id")) ?>">go</a></td>
  </tr>
  <?php } ?> 
</table>
<?php 

  search_footer('affiliate', "affiliate_list", $limitstart, $num_rows, $keyword); 

}

?>

