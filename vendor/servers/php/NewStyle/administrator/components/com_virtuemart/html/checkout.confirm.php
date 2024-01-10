<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: checkout.confirm.php,v 1.3.2.1 2006/03/10 15:55:15 soeren_nb Exp $
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

require_once(CLASSPATH.'ps_payment_method.php');
$ps_payment_method = new ps_payment_method;
?>

<h3><?php echo $VM_LANG->_PHPSHOP_ORDER_CONFIRM_MNU ?></h3>

<?php include(PAGEPATH."ro_basket.php"); ?>

<BR>
<?php
if ($checkout) {

?>
<form action="<?php echo SECUREURL ?>" METHOD="POST" NAME="Checkout">
<input type="hidden" name="zone_qty" value="<?php echo $zone_qty ?>" />
<input type="hidden" name="option" value="com_virtuemart" />
<input type="hidden" name="page" value="<?php echo $modulename?>.thankyou" />
<input type="hidden" name="func" value="checkoutcomplete" />
<input type="hidden" name="user_id" value=<?php echo $auth["user_id"];?>" />
  <input type="hidden" name="ship_to_info_id" value="<?php echo $ship_to_info_id ?>" />
  <!-- customer information --> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top" width="48%"> 
        <table border=0 cellspacing=0 cellpadding=2 width=100%>
          <tr class="sectiontableheader"> 
            <td colspan=2><b><?php

$q  = "SELECT * from #__users WHERE ";
$q .= "id='" . $auth["user_id"] . "' ";
$q .= "AND address_type='BT'";
$db->query($q);
if(!$db->num_rows()) {
    $q  = "SELECT * from #__{vm}_user_info WHERE ";
    $q .= "user_id='" . $auth["user_id"] . "' ";
    $q .= "AND address_type='BT'";
    $db->query($q);
}
$db->next_record();
?><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_BILLINFO ?></B></TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_COMPANY ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("company");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT> <B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_NAME ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("first_name");
     echo " ";
     $db->p("middle_name");
     echo " ";
     $db->p("last_name");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT> <B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_ADDRESS ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("address_1");
     echo "<BR>";
     $db->p("address_2");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT>&nbsp;</TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("city");
     echo ",";
     $db->p("state");
     echo " ";
     $db->p("zip");
     echo "<br> ";
     $db->p("country");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PHONE ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("phone_1");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_FAX ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("fax");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_EMAIL ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php if (!$db->f("user_email")) { $db->p("email"); } else $db->f("user_email"); ?>
            </TD>
          </TR>
        </TABLE>
      </TD>
      <TD VALIGN="TOP" WIDTH="52%"> 
        <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2 WIDTH=100%>
          <TR class="sectiontableheader"> 
            <TD COLSPAN=2><B><?php
            
    $q  = "SELECT * from #__users WHERE ";
    $q .= "user_info_id='$ship_to_info_id' ";
    $db->query($q);
    
    if (!$db->num_rows()) {
        $q  = "SELECT * from #__{vm}_user_info WHERE ";
        $q .= "user_info_id='$ship_to_info_id'";
        $db->query($q);
    }
    $db->next_record();
    
?><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_SHIPINFO ?></B></TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_SHIPINFO_COMPANY ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("company");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B> <?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_SHIPINFO_NAME ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("first_name");
     echo " ";
     $db->p("middle_name");
     echo " ";
     $db->p("last_name");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B> <?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_SHIPINFO_ADDRESS ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("address_1");
     echo "<BR>";
     $db->p("address_2");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B></B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("city");
     echo ",";
     $db->p("state");
     echo " ";
     $db->p("zip");
     echo "<br> ";
     $db->p("country");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_SHIPINFO_PHONE ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("phone_1");
   ?> </TD>
          </TR>
          <TR> 
            <TD WIDTH=10% ALIGN=RIGHT><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_SHIPINFO_FAX ?>: </B></TD>
            <TD WIDTH=90% NOWRAP> <?php
     $db->p("fax");
   ?> </TD>
          </TR>
        </TABLE>
      </TD>
    </TR>
  </TABLE>
  <!-- Customer Information Ends --> 
  <!-- Customer Shipping --> 
  <?php 
if (IS_ENABLE AND $weight_total!=0) {
include(PAGEPATH."/checkout.shipping_selected.php"); 
}
?><!-- END Customer Shipping --><BR>

<!-- Begin Payment Infomation -->
  <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2 WIDTH=100%>
    <TR class="sectiontableheader"> 
      <TD COLSPAN=2><B><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO ?></B></TD>
    </TR>
    <TR> 
      <TD NOWRAP WIDTH=10% ALIGN=RIGHT><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_METHOD ?>: </TD>
      <TD><?php $ps_payment_method->list_method($db->sf("payment_method_id")) ?></TD>
    </TR>
    <TR> 
      <TD colspan=2>&nbsp;</TD>
    </TR>
    <TR> 
      <TD NOWRAP WIDTH=10% ALIGN=RIGHT><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_NAMECARD ?>*: </TD>
      <TD> 
        <INPUT type="text" class="inputbox" NAME=order_payment_name VALUE="<?php echo $order_payment_name ?>">
      </TD>
    </TR>
    <TR> 
      <TD NOWRAP WIDTH=10% ALIGN=RIGHT><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_CCNUM ?>*: </TD>
      <TD> 
        <INPUT type="text"  class="inputbox" NAME=order_payment_number VALUE="<?php echo $order_payment_number ?>">
      </TD>
    </TR>
    <TR> 
      <TD NOWRAP WIDTH=10% ALIGN=RIGHT><?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_EXDATE ?>*: </TD>
      <TD><?php $ps_html->list_month("order_payment_expire_month") . "/" . $ps_html->list_year("order_payment_expire_year") ?></TD>
    </TR>
  </TABLE>
<!-- End payment information -->
<BR><br />
*<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_REQINFO ?>.<br /><br /><br />
<table width=100% border=0 cellspacing=0 cellpadding=0>
<tr align=center>
  <td><input type=submit class="button" name=submit value="<?php echo $VM_LANG->_PHPSHOP_CHECKOUT_CONF_PAYINFO_COMPORDER ?>"></TD>
</tr>
</table>

</form>
<!-- Body ends here -->
<?php 
}
?>
