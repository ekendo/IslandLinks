<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: affiliate.orders_list.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
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

<h3>order summary <?php echo date('f y',$date) ?></h3>

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="10">
    <tr valign="top"> 
      <td align="left" width="90%"><?php 
      
         // order_id is returned by checkoutcomplete function
        $affiliate = $ps_affiliate->get_affiliate_details($auth["user_id"]);
        echo $affiliate["company"];
      
      ?>
      </td>
      <td width=10% align=right>&nbsp; </td>
    </tr>
    <tr>
      <td colspan=2>
        <table class="adminlist">
          <tr>
            <td width="22%">order Ref </td>
            <td width="19%">date Ordered</td>
            <td width="19%">order Total</td>
            <td width="23%">commission(rate)</td>
            <td width="17%">order Status </td>
          </tr> <?php 
          
            $month = date("n",$date);
            $year = date("y",$date);
          
            $q = "select * from #__{vm}_orders,#__{vm}_affiliate_sale";
            $q .=" where #__{vm}_orders.order_id = #__{vm}_affiliate_sale.order_id";
            $q .=" and #__{vm}_affiliate_sale.affiliate_id = '".$affiliate["id"]."'";
            $q .= " and from_unixtime(cdate,'m') = $month";
            $q .= " and from_unixtime(cdate,'y') = $year";
            $db->query($q);
          
            while($db->next_record()){ 
          
          ?>
          <tr>
            <td width="22%"><?php  printf("%08d", $db->f("order_id")); ?></td>
            <td width="19%"><?php echo date("d-m-y", $db->f("cdate")); ?></td>
            <td width="19%"><?php  printf("%1.2f", $db->f("order_subtotal")); ?></td>
            <td width="23%"><?php  printf("%1.2f", $db->f("order_subtotal") *$db->f("rate")*0.01); echo "(".$db->f("rate")."%)";?></td>
            <td width="17%"><?php  $db->p("order_status") ?></td>
          </tr> 
        
        <?php }?> 
        </table>
    </td>
  </tr>
</table>
