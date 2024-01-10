<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: affiliate.affiliate_orders_detail.php,v 1.3.2.1 2005/11/30 20:18:59 soeren_nb Exp $
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
?>

<h3>Order Summary <?php echo date('F Y',$date) ?></h3>
<table class="adminlist">
<tr valign=top> 
<td align=left width=90%><?php 

  $affiliate = $ps_affiliate->get_affiliate_details(0,isset($affiliate_id) ? $affiliate_id : "");
 
  echo $affiliate["company"];
?></td>
<td width=10% align=right>&nbsp; </td>
</tr>
<tr>
<td colspan=2>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<th width="22%">order Ref </th>
<th width="19%">Date Ordered</th>
<th WIDTH="19%">Order Total</th>
<th WIDTH="23%">Commission(rate)</th>
<th WIDTH="17%">Order Status </th>
</tr><?php 
	$start_date = mktime(0,0,0,date("n"),1,date("Y"));
	$end_date = mktime(24,0,0,date("n")+1,0,date("Y"));
		
	$q = "SELECT * FROM #__{vm}_orders,#__{vm}_affiliate_sale";
	$q .=" WHERE #__{vm}_orders.order_id = #__{vm}_affiliate_sale.order_id";
	$q .=" AND #__{vm}_affiliate_sale.affiliate_id = '".$affiliate["id"]."'";
	$q .= " AND #__{vm}_orders.order_status = 'C'";
	$q .= " AND cdate BETWEEN $start_date AND $end_date ";

	$db->query($q);
	while($db->next_record()){  ?>
    <tr>
        <td width="22%"><?php  printf("%08d", $db->f("order_id")); ?></td>
        <td width="19%"><?php echo date("d-m-y", $db->f("cdate")); ?></td>
        <td width="19%"><?php  printf("%1.2f", $db->f("order_subtotal")); ?></td>
        <td width="23%"><?php  printf("%1.2f", $db->f("order_subtotal") *$db->f("rate")*0.01); echo "(".$db->f("rate")."%)";?></td>
        <td width="17%"><?php  $db->p("order_status") ?></td>
    </tr> 
<?php }?>

</table>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
  <input type="hidden" name="user_id" value="<?php $db->sp("user_id"); ?>">
  <input type="hidden" name="date" value="<?php echo isset($date) ? $date : ""; ?>"> 
  <input type="hidden" name="page" value="<?php echo $modulename?>.affiliate_orders_detail"> 
  <input type="hidden" name="option" value="com_virtuemart"> 
  <input type="hidden" name="task" value=""> 
  <br>Month
  <select name="date" size="1"> <?php
  for($i=0;$i<12;$i++){ 
    $mytime = mktime(0,0,0,date('m')-$i,1,date('y'));?>
    <option value="<?php echo $mytime ?>" <?php if($mytime == $date) echo "selected"?>> <?php 
    echo date('F Y',$mytime); ?>
    </option><?php echo "\n";
}
?> </select><br><br>
    <input type="submit" name="submit" class="submit" value="change view">
  </form>
  </td>
  </tr> 
</table>
