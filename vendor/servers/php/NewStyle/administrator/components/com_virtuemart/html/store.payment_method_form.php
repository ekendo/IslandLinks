<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: store.payment_method_form.php,v 1.7 2005/11/08 19:21:02 soeren_nb Exp $
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

require_once( CLASSPATH . 'ps_creditcard.php' );

include_class( 'shopper');
global $ps_shopper_group;

$payment_method_id = mosgetparam($_REQUEST, 'payment_method_id', "");
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

$vars['payment_enabled'] = "Y";

if (!empty($payment_method_id)) {
    $q = "SELECT * FROM #__{vm}_payment_method WHERE vendor_id='$ps_vendor_id' AND ";
    $q .= "payment_method_id='$payment_method_id'"; 
    $db->query($q);  
    $db->next_record();
}

if ( $db->f("payment_class") ) {

    if (include( CLASSPATH."payment/".$db->f("payment_class").".php" ))
        eval( "\$_PAYMENT = new ".$db->f("payment_class")."();");
}
else {
    include( CLASSPATH."payment/ps_payment.php" );
    $_PAYMENT = new ps_payment();
}
//First create the object and let it print a form heading
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_LBL );
//Then Start the form
$formObj->startForm();

?>
<br />
<?php
$tabs = new mShopTabs(0, 1, "_main");
$tabs->startPane("content-pane");
$tabs->startTab( "General", "global-page");
?>
<table class="adminform">
    <tr>
      <td><strong><?php echo $VM_LANG->_PHPSHOP_ISSHIP_LIST_PUBLISH_LBL ?>?:</strong></td>
      <td><input type="checkbox" name="payment_enabled" class="inputbox" value="Y" <?php echo $db->sf("payment_enabled")=="Y" ? "checked=\"checked\"" : "" ?> /></td>
    </tr>
    <tr> 
      <td width="31%" align="right" nowrap ><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_NAME ?>:</strong></td>
      <td width="69%" > 
        <input type="text" class="inputbox" name="payment_method_name" value="<?php $db->sp("payment_method_name") ?>" size="32" />
      </td>
    </tr>
    <tr> 
      <td width="31%" align="right" nowrap ><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_CODE ?>:</strong></td>
      <td width="69%" > 
        <input type="text" class="inputbox" name="payment_method_code" value="<?php $db->sp("payment_method_code") ?>" size="4" maxlength="8" />
      </td>
    </tr>
    <tr>
      <td width="31%" align="right">
          Payment class name (e.g. <strong>ps_netbanx</strong>) :<br />
          default: ps_payment<br />
          <i>Leave blank if you're not sure what to fill in!</i>
      </td>
      <td width="69%"><input type="text" class="inputbox" name="payment_class" value="<?php $db->sp("payment_class") ?>" /></td>
    </tr>
    <tr> 
      <td width="31%" valign="top" align="right" nowrap ><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_ENABLE_PROCESSOR ?>:</strong></td>
      <td width="69%" >
      <?php 
          $payment_process = $db->f("enable_processor"); 
          $payment_types = array( "" => $VM_LANG->_PHPSHOP_PAYMENT_FORM_CC, 
                                                  "Y" => $VM_LANG->_PHPSHOP_PAYMENT_FORM_USE_PP, 
                                                  "B" => $VM_LANG->_PHPSHOP_PAYMENT_FORM_BANK_DEBIT, 
                                                  "N" => $VM_LANG->_PHPSHOP_PAYMENT_FORM_AO, 
                                                  "P" => "PayPal (or related)");
          foreach( $payment_types as $value => $description) {
            echo "<input type=\"radio\" onchange=\"check()\" name=\"enable_processor\" value=\"$value\"";
            echo $payment_process == $value ? " checked=\"checked\">\n" : ">\n";
            echo $description . "<br />";
          }
      ?>
      </td>
    </tr>
    <tr>
      <td colspan="2"><hr /></td>
    </tr>
    <tr>
      <td width="31%" align="right" valign="top"><div id="accepted_creditcards1"></div></td>
      <td width="69%"><div id="accepted_creditcards2"></div></td>
    </tr>
    <div id="accepted_creditcards_store"></div>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="31%" align="right"  valign="top"><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_SHOPPER_GROUP ?>:</strong></td>
      <td width="69%" ><?php $ps_shopper_group->list_shopper_groups("shopper_group_id", $db->sf("shopper_group_id")) ?> 
      </td>
    </tr>
    <tr> 
      <td width="31%" align="right" valign="top"><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_DISCOUNT ?>:</strong></td>
      <td width="69%" > 
        <INPUT type="text" class="inputbox" name="payment_method_discount" size="6"  value="<?php $db->sp("payment_method_discount"); ?>" />
      </td>
    </tr>
    <tr> 
      <td width="31%" align="right" valign="top"><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_FORM_LIST_ORDER ?>:</strong></td>
      <td width="69%" > 
        <input type="text" class="inputbox" name="list_order" size="4" maxlength="4" value="<?php $db->sp("list_order"); ?>" />
      </td>
    </tr>
    <tr> 
      <td width="31%" align="right"  valign="top">&nbsp;</td>
      <td width="69%" >&nbsp; </td>
    </tr>
  </table>
<?php
        $tabs->endTab();
        $tabs->startTab( $VM_LANG->_PHPSHOP_CONFIG, "config-page");
        
        $_PAYMENT->show_configuration();
?>
<br />
<strong>Payment Extra Info:</strong>
<?php echo mm_ToolTip("Is shown on the Order Confirmation Page. Can be: HTML Form Code from your Payment Service Provider, Hints to the customer etc.") ?>
<br />
<textarea class="inputbox" name="payment_extrainfo" cols="120" rows="20"><?php echo htmlspecialchars( $db->sf("payment_extrainfo") ); ?></textarea>
<?php
$tabs->endTab();
$tabs->endPane();

// Add necessary hidden fields
$formObj->hiddenField( 'payment_method_id', $payment_method_id );

$funcname = !empty($payment_method_id) ? "paymentMethodUpdate" : "paymentMethodAdd";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.payment_method_list', $option );
?>
  
  <script type="text/javascript">
function check() {
   if (document.adminForm.enable_processor[0].checked == true || document.adminForm.enable_processor[1].checked == true) {
      document.getElementById('accepted_creditcards1').innerHTML = '<strong>Accepted Credit Card Types:</strong>';
      if (document.getElementById('accepted_creditcards_store').innerHTML != '')
        document.getElementById('accepted_creditcards2').innerHTML ='<input type="text" name="accepted_creditcards" value="' + document.getElementById('accepted_creditcards_store').innerHTML + '" class="inputbox" />';
      else
        document.getElementById('accepted_creditcards2').innerHTML = '<?php ps_creditcard::creditcard_checkboxes( $db->f("accepted_creditcards") ); ?>';
   }
   else {
    try {
      document.getElementById('accepted_creditcards_store').innerHTML = document.adminForm.accepted_creditcards.value;
    }
    catch (e) {}
    document.getElementById('accepted_creditcards1').innerHTML = '';
    document.getElementById('accepted_creditcards2').innerHTML = '';
  }
}
check();
</script>
