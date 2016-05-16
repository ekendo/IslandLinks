<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shipping.carrier_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_CARRIER_FORM_LBL );
//Then Start the form
$formObj->startForm();

$shipping_carrier_id = mosgetparam( $_REQUEST, 'shipping_carrier_id');
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

if (!empty($shipping_carrier_id)) {
  $q = "SELECT * FROM #__{vm}_shipping_carrier WHERE shipping_carrier_id='$shipping_carrier_id'";
  $db->query($q);
  $db->next_record();
}
?><br />
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td width="21%" ><div align="right"><?php echo $VM_LANG->_PHPSHOP_CARRIER_FORM_NAME ?>:</div></td>
		<td width="79%" ><input class="inputbox" type="text" name="shipping_carrier_name" size="32" maxlength="255" value="<?php $db->sp("shipping_carrier_name") ?>"></td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><?php echo $VM_LANG->_PHPSHOP_CARRIER_FORM_LIST_ORDER ?>:</div></td>
		<td width="79%" ><input class="inputbox" type="text" name="shipping_carrier_list_order" size="32" maxlength="255" value="<?php $db->sp("shipping_carrier_list_order") ?>"></td>
	</tr>
</table>
<?php

// Add necessary hidden fields
$formObj->hiddenField( 'shipping_carrier_id', $shipping_carrier_id );

$funcname = !empty($shipping_carrier_id) ? "carrierupdate" : "carrieradd";

// finally close the form:
$formObj->finishForm( $funcname, $modulename.'.carrier_list', $option );
?>