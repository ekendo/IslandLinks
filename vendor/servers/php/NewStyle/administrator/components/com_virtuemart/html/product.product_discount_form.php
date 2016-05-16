<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_discount_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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

//First create the object and let it print a form heading
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ADDEDIT );
//Then Start the form
$formObj->startForm();

$discount_id = mosGetParam( $_REQUEST, 'discount_id', null );
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

if ( !empty($discount_id) ) {
  $q = "SELECT * FROM #__{vm}_product_discount WHERE discount_id='$discount_id'";
  $db->query($q);
  $db->next_record();
}
?> 
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $mosConfig_live_site ?>/includes/js/calendar/calendar-mos.css" title="green" />
<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/includes/js/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/includes/js/calendar/lang/calendar-en.js"></script>

<table class="adminform">
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="24%"><div align="right"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_AMOUNT ?>:</div></td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="amount" value="<?php $db->sp("amount") ?>" />
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_AMOUNT_TIP ); ?>
      </td>
    </tr>
    <tr> 
      <td width="24%"><div align="right"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_AMOUNTTYPE ?>:</div></td>
      <td width="76%"> 
        <input type="radio" class="inputbox" name="is_percent" value="1" <?php if($db->sf("is_percent")==1) echo "checked=\"checked\""; ?> />
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ISPERCENT ?>&nbsp;&nbsp;&nbsp;
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ISPERCENT_TIP ); ?><br />
        <input type="radio" class="inputbox" name="is_percent" value="0" <?php if($db->sf("is_percent")==0) echo "checked=\"checked\""; ?> />
        <?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ISTOTAL ?>
      </td>
    </tr>
    <tr> 
      <td width="24%"><div align="right"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_STARTDATE ?>:</div></td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="start_date" id="start_date" value="<?php if($db->sf("start_date")) echo strftime("%Y-%m-%d", $db->sf("start_date")); ?>" />
        <input name="reset" type="reset" class="button" onclick="return showCalendar('start_date', 'y-mm-dd');" value="..." />&nbsp;&nbsp;&nbsp;
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_STARTDATE_TIP ); ?>
      </td>
    </tr>
    <tr> 
      <td width="24%"><div align="right"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ENDDATE ?>:</div></td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="end_date" id="end_date" value="<?php if($db->sf("end_date")) echo strftime("%Y-%m-%d", $db->sf("end_date")); ?>" />
        <input name="reset" type="reset" class="button" onclick="return showCalendar('end_date', 'y-mm-dd');" value="..." />&nbsp;&nbsp;&nbsp;
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRODUCT_DISCOUNT_ENDDATE_TIP ); ?>
      </td>
    </tr>
    <tr> 
      <td valign="top" colspan="2" align="right">&nbsp; </td>
    </tr>   
  </table>

<?php
// Add necessary hidden fields
$formObj->hiddenField( 'discount_id', $discount_id );

$funcname = empty( $discount_id) ? "discountAdd" : "discountUpdate";

// finally close the form:
$formObj->finishForm( $funcname, $modulename.'.product_discount_list', $option );

?>