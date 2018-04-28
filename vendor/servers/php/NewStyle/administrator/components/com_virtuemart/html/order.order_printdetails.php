<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: order.order_printdetails.php,v 1.5.2.2 2006/03/10 15:55:15 soeren_nb Exp $
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

require_once(CLASSPATH.'ps_checkout.php');
require_once(CLASSPATH.'ps_product.php');
$ps_product= new ps_product;

$order_id = mosgetparam( $_REQUEST, 'order_id', null);
$dbc = new ps_DB;
if (!is_numeric($order_id))
    die ('Please provide a valid Order ID!');

$q = "SELECT * FROM #__{vm}_orders WHERE order_id='$order_id' and vendor_id = $ps_vendor_id"; 
$db->query($q);
$db->next_record();
// if ($db->next_record()) {
?>

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td valign="top">
     <h2><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_LBL ?></h2>
     <p><?php
        echo $vendor_name . "<br />";
        echo $vendor_address . "<br />";
        echo $vendor_city . ", ";
        echo $vendor_state . " ";
        echo $vendor_zip; ?></p>
    </td>
    <td valign="top" width="10%" align="right"><?php echo $vendor_image; ?></td>
  </tr>
</table>

<table border="0" cellspacing="0" cellpadding="2" width="100%">
  <!-- begin customer information --> 
  <tr class="sectiontableheader"> 
    <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ACC_ORDER_INFO ?></th>
  </tr>
  <tr> 
    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER?>:</td>
    <td><?php printf("%08d", $db->f("order_id")); ?></td>
  </tr>

  <tr> 
    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_DATE ?>:</td>
    <td><?php echo date("d-M-Y H:i", $db->f("cdate")); ?></td>
  </tr>
  <tr> 
    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_STATUS ?>:</td>
    <td><?php
         $q = "SELECT order_status_name FROM #__{vm}_order_status WHERE ";
         $q .= "order_status_code = '" . $db->f("order_status") . "'";
         $dbos = new ps_DB;
         $dbos->query($q);
         $dbos->next_record();
         echo $dbos->f("order_status_name");
         ?>

</td>
  </tr>
  <!-- End Customer Information --> 
  <!-- Begin 2 column bill-ship to --> 
  <tr class="sectiontableheader"> 
    <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_INFO_LBL ?></th>
  </tr>
  <tr valign="top"> 
    <td width="50%"> <!-- Begin BillTo --><?php
    // Get bill_to information
    $dbbt = new ps_DB;
    $q  = "SELECT * FROM #__{vm}_order_user_info WHERE user_id='" . $db->f("user_id") . "'  AND order_id='$order_id' ORDER BY address_type ASC"; 
    $dbbt->query($q);
    $dbbt->next_record(); 
    $user = $dbbt->record;
  ?> 
      <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr> 
          <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_BILL_TO_LBL ?></strong></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?> :</td>
          <td><?php $dbbt->p("company"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?> :</td>
          <td><?php 
                $dbbt->p("first_name"); 
                echo " ";
                $dbbt->p("middle_name"); 
                echo " ";
                $dbbt->p("last_name"); 
         ?></td>
        </tr>
        <tr valign="top"> 
          <td><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?> :</td>
          <td><?php 
              $dbbt->p("address_1"); 
              echo "<br />";
              $dbbt->p("address_2");    
         ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?> :</td>
          <td><?php $dbbt->p("city"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_STATE ?> :</td>
          <td><?php $dbbt->p("state"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?> :</td>
          <td><?php $dbbt->p("zip"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?> :</td>
          <td><?php 
		$country = $dbbt->f("country");
		$dbc->query( "SELECT country_name FROM #__{vm}_country WHERE country_3_code = '$country'");
		$dbc->next_record();
		$country_name = $dbc->f("country_name");
		echo $country_name;
	?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?> :</td>
          <td><?php $dbbt->p("phone_1"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?> :</td>
          <td><?php $dbbt->p("fax"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?> :</td>
          <td><?php $dbbt->p("user_email"); ?></td>
        </tr>
      </table>
      <!-- End BillTo --> </td>
    <td width="50%"> <!-- Begin ShipTo --> <?php
    // Get ship_to information
    $dbbt->next_record(); 
    $dbst =& $dbbt;
  ?> 
 <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr> 
          <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIP_TO_LBL ?></strong></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?> :</td>
          <td><?php $dbst->p("company"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?> :</td>
          <td><?php 
         $dbst->p("first_name"); 
         echo " ";
         $dbst->p("middle_name"); 
         echo " ";
         $dbst->p("last_name"); 
         ?></td>
        </tr>
        <tr valign="top"> 
          <td><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?> :</td>
          <td><?php 
          $dbst->p("address_1"); 
          echo "<br />";
          $dbst->p("address_2");    
         ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?> :</td>
          <td><?php $dbst->p("city"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_STATE ?> :</td>
          <td><?php $dbst->p("state"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?> :</td>
          <td><?php $dbst->p("zip"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?> :</td>
          <td><?php 
		if( $country != $dbst->f("country")) {
			$country = $dbst->f("country");
			$dbc->query( "SELECT country_name FROM #__{vm}_country WHERE country_3_code = '$country'");
			$dbc->next_record();
			$country_name = $dbc->f("country_name");
		}
		echo $country_name;
		?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?> :</td>
          <td><?php $dbst->p("phone_1"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?> :</td>
          <td><?php $dbst->p("fax"); ?></td>
        </tr>
      </table>
      <!-- End ShipTo --> 
      <!-- End Customer Information --> 
    </td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <?php if ($PSHOP_SHIPPING_MODULES[0] != "no_shipping" && $db->f("ship_method_id")) { ?> 
  <tr> 
    <td colspan="2"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        
        <tr class="sectiontableheader"> 
          <th align="left"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_SHIPPING_LBL ?></th>
        </tr>
        <tr> 
          <td> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_CARRIER_LBL ?></strong></td>
                <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_MODE_LBL ?></strong></td>
                <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PRICE ?>&nbsp;</strong></td>
              </tr>
              <tr> 
                <td><?php 
                    $details = explode( "|", $db->f("ship_method_id"));
                    echo $details[1];
                    ?>&nbsp;
                </td>
                <td><?php 
                    echo $details[2];
                    ?>
                </td>
                <td><?php 
                    echo $CURRENCY_DISPLAY->getFullValue($details[3]); 
                    ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        
      </table>
    </td>
  </tr><?php
     } 

  ?> 
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <!-- Begin Order Items Information --> 
  <tr class="sectiontableheader"> 
    <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_ITEM ?></th>
  </tr>
  <tr> 
    <td colspan="2"> 
      <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr align="left"> 
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_QTY ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_NAME ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SKU ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PRICE ?></th>
          <th align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL ?>&nbsp;&nbsp;&nbsp;</th>
        </tr>
        <?php 
      $dbcart = new ps_DB;
      $q  = "SELECT * FROM #__{vm}_order_item ";
      $q .= "WHERE #__{vm}_order_item.order_id='$order_id' ";
      $dbcart->query($q); 
      $subtotal = 0;
      while ($dbcart->next_record()) {
?> 
        <tr align="left"> 
          <td><?php $dbcart->p("product_quantity"); ?></td>
          <td><?php $dbcart->p("order_item_name"); echo " <font size=\"-2\">" . $dbcart->f("product_attribute") . "</font>";?></td>
          <td><?php $dbcart->p("order_item_sku"); ?></td>
          <td><?php /*
                $price = $ps_product->get_price($dbcart->f("product_id"));
                $item_price = $price["product_price"]; */
                $item_price = $dbcart->f("product_item_price");
               echo $CURRENCY_DISPLAY->getFullValue($item_price);
               
           ?></td>
          <td align="right"><?php $total = $dbcart->f("product_quantity") * $item_price; 
                $subtotal += $total;
                echo $CURRENCY_DISPLAY->getFullValue($total);
           ?>&nbsp;&nbsp;&nbsp;</td>
        </tr><?php
      }
?> 
        <tr> 
          <td colspan="4" align="right">&nbsp;&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SUBTOTAL ?> :</td>
          <td align="right"><?php echo $CURRENCY_DISPLAY->getFullValue($subtotal) ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
<?php 
      /* COUPON DISCOUNT */
      $coupon_discount = $db->f("coupon_discount");
      
      if( $coupon_discount > 0 ) {
        $subtotal -= $coupon_discount;
      ?>
        <tr>
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_COUPON_DISCOUNT ?>:
          </td> 
          <td align="right"><?php
            echo "- ".$CURRENCY_DISPLAY->getFullValue( $coupon_discount ); ?>&nbsp;&nbsp;&nbsp;
          </td>
        </tr>
      <?php
      }
      if (($db->f("order_discount") != 0) && (PAYMENT_DISCOUNT_BEFORE == '1')) { ?>
        <tr>
            <td colspan="4" align="right"><?php 
              if( $db->f("order_discount") > 0)
                echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT;
              else
                echo $VM_LANG->_PHPSHOP_FEE;
                ?>:
            </td> 
            <td align="right"><?php
          if ($db->f("order_discount") > 0 )
               echo "- ".$CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount")));
          elseif ($db->f("order_discount") < 0 )
               echo "+ ".$CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount"))); ?>
            &nbsp;&nbsp;&nbsp;</td>
        </tr>
        
        <?php 
      }
?>
        
        <tr> 
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING ?> :</td>
          <td align="right"><?php 
            $shipping_total = $db->f("order_shipping");
            echo $CURRENCY_DISPLAY->getFullValue($shipping_total);
            
            ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL_TAX ?> :</td>
          <td align="right"><?php 
            $tax_total = $db->f("order_tax")+ $db->f("order_shipping_tax");
            echo $CURRENCY_DISPLAY->getFullValue($tax_total);
            
            ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
     
        <tr> 
          <td colspan="4" align="right">
          <?php if (PAYMENT_DISCOUNT_BEFORE == '1') { ?><strong><?php } 
          
          echo $VM_LANG->_PHPSHOP_CART_TOTAL .":"; if (PAYMENT_DISCOUNT_BEFORE != '1') { ?></strong><?php } ?></td>
          
          <td align="right"><?php 
          if (PAYMENT_DISCOUNT_BEFORE == '1') { ?><strong><?php  
            $total = $db->f("order_total");
            echo $CURRENCY_DISPLAY->getFullValue($total);
          }
          else {
            $total = $db->f("order_subtotal") + $db->f("order_tax") + $db->f("order_shipping");
            echo $CURRENCY_DISPLAY->getFullValue($total);
          }
          if (PAYMENT_DISCOUNT_BEFORE == '1') { ?></strong><?php } ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <?php 
        if ($db->f("order_discount") != 0.00 && PAYMENT_DISCOUNT_BEFORE != '1') { ?>
        <tr>
        <td colspan="4" align="right"><?php 
              if( $db->f("order_discount") > 0)
                echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT;
              else
                echo $VM_LANG->_PHPSHOP_FEE;
                ?>:
        </td> 
        <td align="right"><?php
          if ($db->f("order_discount") > 0 )
               echo "- ".$CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount")));
          elseif ($db->f("order_discount") < 0 )
               echo "+ ".$CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount")));
               ?>&nbsp;&nbsp;&nbsp;
        </td>
        </tr>
        <tr>
            <td colspan="4" align="right"><strong><?php echo $VM_LANG->_PHPSHOP_CART_TOTAL ?>: </strong></td>
        <td align="right"><strong><?php echo $CURRENCY_DISPLAY->getFullValue($db->f("order_total")); ?>
        </strong>&nbsp;&nbsp;&nbsp;
          </td>
        </tr>
        <?php
        } 
        ?>
        <tr>
            <td colspan="4" align="right">&nbsp;</td>
        <td align="right"><strong><?php echo ps_checkout::show_tax_details( $db->f('order_tax_details') ); ?>
        </strong>&nbsp;&nbsp;&nbsp;
          </td>
        </tr>            
      </table>
    </td>
  </tr>
  <!-- End Order Items Information --> 

<br />

  <!-- Begin Payment Information --> 

      <table width="100%">
      <tr class="sectiontableheader"> 
        <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYINFO_LBL ?></th>
      </tr>
          <?php
          /** Retrieve Payment Info **/
          $dbpm = new ps_DB;
          $q  = "SELECT * FROM #__{vm}_payment_method, #__{vm}_order_payment, #__{vm}_orders ";
          $q .= "WHERE #__{vm}_order_payment.order_id='$order_id' ";
          $q .= "AND #__{vm}_payment_method.payment_method_id=#__{vm}_order_payment.payment_method_id ";
          $q .= "AND #__{vm}_orders.user_id='" . $db->f("user_id") . "' ";
          $q .= "AND #__{vm}_orders.order_id='$order_id' ";
          $dbpm->query($q);
          $dbpm->next_record(); ?> 
      <tr> 
        <td width="20%"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYMENT_LBL ?> :</td>
        <td><?php $dbpm->p("payment_method_name"); ?> </td>
      </tr>
	  <?php
          require_once(CLASSPATH.'ps_payment_method.php');
          $ps_payment_method = new ps_payment_method;
          $payment = $dbpm->f("payment_method_id");
          
          if ($ps_payment_method->is_creditcard($payment)) { 
          
            // DECODE Account Number
            $dbaccount = new ps_DB;
            $q = "SELECT DECODE(\"". $dbpm->f("order_payment_number")."\",\"".ENCODE_KEY."\") as account_number FROM #__{vm}_order_payment WHERE order_id='".$order_id."'";
            $dbaccount->query($q);
            $dbaccount->next_record(); ?>
      <tr> 
        <td width="10%"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ACCOUNT_NAME ?> :</td>
        <td><?php $dbpm->p("order_payment_name"); ?> </td>
      </tr>
      <tr> 
        <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ACCOUNT_NUMBER ?> :</td>
        <td><?php echo ps_checkout::asterisk_pad($dbaccount->f("account_number"),4);
    ?> </td>
      </tr>
      <tr> 
        <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EXPIRE_DATE ?> :</td>
        <td><?php echo date("M-Y", $dbpm->f("order_payment_expire")); ?> </td>
      </tr>
	  <?php } ?>
      <!-- end payment information --> 
      </table>
</center>
<?php // }
  
  /** Print out the customer note **/
  if ( $db->f("customer_note") ) {
    ?>
    <table width="100%">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="sectiontableheader">
        <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_NOTE ?></th>
      </tr>
      <tr>
        <td colspan="2">
         <?php echo nl2br($db->f("customer_note"))."<br />"; ?>
       </td>
      </tr>
    </table>
    <?php
  }
  
// } /* End of security check */
?>
