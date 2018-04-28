<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: checkout.paymentradio.php,v 1.7.2.2 2006/03/21 19:38:23 soeren_nb Exp $
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

require_once( CLASSPATH. 'ps_creditcard.php' );

$payment_method_id = mosgetparam($_REQUEST, 'payment_method_id', 0);

// Do we have Credit Card Payments?
$db_cc  = new ps_DB;
$q = "SELECT * from #__{vm}_payment_method,#__{vm}_shopper_group WHERE ";
$q .= "#__{vm}_payment_method.shopper_group_id=#__{vm}_shopper_group.shopper_group_id ";
$q .= "AND (#__{vm}_payment_method.shopper_group_id='".$auth['shopper_group_id']."' ";
$q .= "OR #__{vm}_shopper_group.default='1') ";
$q .= "AND (enable_processor='' OR enable_processor='Y') ";
$q .= "AND payment_enabled='Y' ";
$q .= "AND #__{vm}_payment_method.vendor_id='$ps_vendor_id' ";
$q .= " ORDER BY list_order";
$db_cc->query($q);

if ($db_cc->num_rows()) {
    $cc_payments=true;
    $ps_creditcard = new ps_creditcard();
}
else {
    $cc_payments=false;
}
$count = 0;
$db_nocc  = new ps_DB;
$q = "SELECT * from #__{vm}_payment_method,#__{vm}_shopper_group WHERE ";
$q .= "#__{vm}_payment_method.shopper_group_id=#__{vm}_shopper_group.shopper_group_id ";
$q .= "AND (#__{vm}_payment_method.shopper_group_id='".$auth['shopper_group_id']."' ";
$q .= "OR #__{vm}_shopper_group.default='1') ";
$q .= "AND (enable_processor='B' OR enable_processor='N' OR enable_processor='P') ";
$q .= "AND payment_enabled='Y' ";
$q .= "AND #__{vm}_payment_method.vendor_id='$ps_vendor_id' ";
$q .= " ORDER BY list_order";
$db_nocc->query($q);
if ($db_nocc->next_record()) {
    $nocc_payments=true;
    $first_payment_method_id = $db_nocc->f("payment_method_id");
    $count = $db_nocc->num_rows();
    $db_nocc->reset();
}
else {
    $nocc_payments=false;
}
/** This redirect has lead to critics  **/
if ($count <= 1 && $cc_payments==false) {
	mosRedirect($sess->url(SECUREURL."index.php?page=checkout.index&payment_method_id=$first_payment_method_id&ship_to_info_id=$ship_to_info_id&shipping_rate_id=".urlencode($shipping_rate_id)."&checkout_this_step=99&checkout_next_step=99"),"");
}
elseif( $order_total <= 0.00 ) {
	// In case the order total is less than or equal zero, we don't need a payment method
	mosRedirect($sess->url(SECUREURL."index.php?page=checkout.index&ship_to_info_id=$ship_to_info_id&shipping_rate_id=".urlencode($shipping_rate_id)."&checkout_this_step=99&checkout_next_step=99"),"");
}
if( $nocc_payments &&  $cc_payments ) {
	echo '<table><tr valign="top"><td width="50%">';
}
        
if ($cc_payments==true) { 
  	?>
	<fieldset><legend><strong><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_PAYMENT_CC ?></strong></legend>
		<table border="0" cellspacing="0" cellpadding="2" width="100%">
		    <tr>
		        <td colspan="2">
		        	<?php $ps_payment_method->list_cc($payment_method_id, false) ?>
		        </td>
		    </tr>
		    <tr>
		        <td colspan="2"><strong>&nbsp;</strong></td>
		    </tr>
		    <tr>
		        <td nowrap width="10%" align="right">Credit Card Type:</td>
		        <td>
		        <?php echo $ps_creditcard->creditcard_lists( $db_cc ); ?>
		        <script language="Javascript" type="text/javascript"><!--
				writeDynaList( 'class="inputbox" name="creditcard_code" size="1"',
				orders, originalPos, originalPos, originalOrder );
				//-->
				</script>
		<?php 
		            $db_cc->reset();
		            $payment_class = $db_cc->f("payment_class");
		            $require_cvv_code = "YES";
		            if(file_exists(CLASSPATH."payment/$payment_class.php") && file_exists(CLASSPATH."payment/$payment_class.cfg.php")) {
		                require_once(CLASSPATH."payment/$payment_class.php");
		                require_once(CLASSPATH."payment/$payment_class.cfg.php");
		                eval( "\$_PAYMENT = new $payment_class();" );
		                eval( "\$require_cvv_code = ".$_PAYMENT->payment_code."_CHECK_CARD_CODE;" );
		            }
		?>      </td>
		    </tr>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="order_payment_name"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_NAMECARD ?>:</label>
		        </td>
		        <td>
		        <input type="text" class="inputbox" id="order_payment_name" name="order_payment_name" value="<?php if(!empty($_SESSION['ccdata']['order_payment_name'])) echo $_SESSION['ccdata']['order_payment_name'] ?>" autocomplete="off" />
		        </td>
		    </tr>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="order_payment_number"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_CCNUM ?>:</label>
		        </td>
		        <td>
		        <input type="text" class="inputbox" id="order_payment_number" name="order_payment_number" value="<?php if(!empty($_SESSION['ccdata']['order_payment_number'])) echo $_SESSION['ccdata']['order_payment_number'] ?>" autocomplete="off" />
		        </td>
		    </tr>
		<?php if( $require_cvv_code == "YES" ) { 
					$_SESSION['ccdata']['need_card_code'] = 1;	
			?>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="credit_card_code">Credit Card Security Code:</label></td>
		        <td>
		            <input type="text" class="inputbox" id="credit_card_code" name="credit_card_code" value="<?php if(!empty($_SESSION['ccdata']['credit_card_code'])) echo $_SESSION['ccdata']['credit_card_code'] ?>" autocomplete="off" />
		        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_CUSTOMER_CVV2_TOOLTIP); ?>
		        </td>
		    </tr>
		<?php } ?>
		    <tr>
		        <td nowrap width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_EXDATE ?>:</td>
		        <td><?php 
		        $ps_html->list_month("order_payment_expire_month", @$_SESSION['ccdata']['order_payment_expire_month']);
		        echo "/";
		        $ps_html->list_year("order_payment_expire_year", @$_SESSION['ccdata']['order_payment_expire_year']) ?>
		       </td>
		    </tr>
    	</table>
    </fieldset>
  <?php  
}

if( $nocc_payments &&  $cc_payments ) {
	echo '</td><td width="50%">';
}

if ($nocc_payments==true) {
    if ($cc_payments==true) { 
    	$title = $VM_LANG->_PHPSHOP_CHECKOUT_PAYMENT_OTHER;
    }
    else {
    	$title = $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYMENT_LBL;
    }
    	
   ?>
    <fieldset><legend><strong><?php echo $title ?></strong></legend>
		<table border="0" cellspacing="0" cellpadding="2" width="100%">
		    <tr>
		        <td colspan="2"><?php 
		            $ps_payment_method->list_nocheck($payment_method_id,  false); 
		            $ps_payment_method->list_bank($payment_method_id,  false);
		            $ps_payment_method->list_paypalrelated($payment_method_id,  false); ?>
		        </td>
		    </tr>
		 </table>
	</fieldset>
	<?php
}
if( $nocc_payments &&  $cc_payments ) {
	echo '</td></tr><table>';
}
  ?>