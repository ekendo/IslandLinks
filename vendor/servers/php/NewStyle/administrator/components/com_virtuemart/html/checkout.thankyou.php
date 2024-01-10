<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* This file is called after the order has been placed by the customer
*
* @version $Id: checkout.thankyou.php,v 1.7 2005/10/24 18:13:07 soeren_nb Exp $
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

require_once(CLASSPATH.'ps_product.php');
$ps_product= new ps_product;
$Itemid = mosGetParam( $_REQUEST, "Itemid", null );

global $vendor_currency;

// Order_id is returned by checkoutComplete function
$order_id = $GLOBALS['vmInputFilter']->process( $vars["order_id"] );

$print = mosgetparam( $_REQUEST, 'print', 0);

/** Retrieve User Email **/
$q  = "SELECT * FROM #__{vm}_order_user_info WHERE order_id='$order_id' AND address_type='BT'";
$db->query( $q );
$db->next_record();
$user = $db->record[0];
$dbbt = $db->_clone( $db );

$user->email = $db->f("user_email");

/** Retrieve Order & Payment Info **/
$db = new ps_DB;
$q  = "SELECT * FROM #__{vm}_payment_method, #__{vm}_order_payment, #__{vm}_orders ";
$q .= "WHERE #__{vm}_order_payment.order_id='$order_id' ";
$q .= "AND #__{vm}_payment_method.payment_method_id=#__{vm}_order_payment.payment_method_id ";
$q .= "AND #__{vm}_orders.user_id='" . $auth["user_id"] . "' ";
$q .= "AND #__{vm}_orders.order_id='$order_id' ";
$db->query($q);
if ($db->next_record()) {

?>
<h3><?php echo $VM_LANG->_PHPSHOP_THANKYOU ?></h3>
 <p>
 <?php 
 if( empty($vars['error'])) { ?>
   <img src="<?php echo IMAGEURL ?>ps_image/button_ok.png" height="48" width="48" align="center" alt="Success" border="0" />
   <?php echo $VM_LANG->_PHPSHOP_THANKYOU_SUCCESS?>
  
  <br /><br />
  <?php echo $VM_LANG->_PHPSHOP_EMAIL_SENDTO .": <strong>". $user->user_email; ?></strong><br />
  </p>
  <?php 
 } ?>
  
<!-- Begin Payment Information -->
<?php

if ($db->f("order_status") == "P" ) {
	// Copy the db object to prevent it gets altered
	$db_temp = ps_DB::_clone( $db );
 /** Start printing out HTML Form code (Payment Extra Info) **/ ?>
 <br />
<table width="100%">
  <tr>
    <td width="100%" align="center">
    <?php 
    /* Try to get PayPal/PayMate/Worldpay/whatever Configuration File */
    @include( CLASSPATH."payment/".$db->f("payment_class").".cfg.php" );
    
	echo DEBUG ? vmCommonHTML::getInfoField('Beginning to parse the payment extra info code...' ) : '';
	
    // Here's the place where the Payment Extra Form Code is included
    // Thanks to Steve for this solution (why make it complicated...?)
    if( eval('?>' . $db->f("payment_extrainfo") . '<?php ') === false ) {
    	echo vmCommonHTML::getErrorField( "Error: The code of the payment method ".$db->f( 'payment_method_name').' ('.$db->f('payment_method_code').') '
    	.'contains a Parse Error!<br />Please correct that first' );
    }
    else {
    	echo DEBUG ? vmCommonHTML::getInfoField('Successfully parsed the payment extra info code.' ) : '';
    }
    /** END printing out HTML Form code (Payment Extra Info) **/

      ?>
    </td>
  </tr>
</table>
<br />
<?php
$db = $db_temp;
}
?>
 <p><a href="<?php $sess->purl(SECUREURL."index.php?page=account.order_details&order_id=". $order_id) ?>">
 <?php echo $VM_LANG->_PHPSHOP_ORDER_LINK ?></a>
 </p>
 <?php

} /* End of security check */
?>
