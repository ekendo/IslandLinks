<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shipping.rate_form.php,v 1.5.2.2 2006/03/11 18:06:18 soeren_nb Exp $
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
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_RATE_FORM_LBL );
//Then Start the form
$formObj->startForm();

$shipping_rate_id = mosgetparam( $_REQUEST, 'shipping_rate_id');
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;
if (!isset($ps_shipping)) $ps_shipping = new ps_shipping();

if (!empty($shipping_rate_id)) {
  $q = "SELECT * FROM #__{vm}_shipping_rate WHERE shipping_rate_id='$shipping_rate_id'";
  $db->query($q);
  $db->next_record();
}
?><br />

<table class="adminform">
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_NAME ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_name" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_name") ?>">
		</td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_LIST_ORDER ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_list_order" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_list_order") ?>">
		</td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_CARRIER ?>:</strong></div></td>
		<td width="79%" ><?php $ps_shipping->carrier_list("shipping_rate_carrier_id", $db->f("shipping_rate_carrier_id")); ?></td>
	</tr>
	<tr>
		<td width="21%" valign="top" ><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_COUNTRY .": </strong><br/><br/>".$VM_LANG->_PHPSHOP_MULTISELECT ?></td>
		<td width="79%" ><?php $ps_shipping->country_multiple_list("shipping_rate_country[]", $db->f("shipping_rate_country")); ?></td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_ZIP_START ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_zip_start" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_zip_start") ?>">
		</td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_ZIP_END ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_zip_end" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_zip_end") ?>">
		</td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_WEIGHT_START ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_weight_start" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_weight_start") ?>">
		</td>
	</tr>
		<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_WEIGHT_END ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_weight_end" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_weight_end") ?>">
		</td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_VALUE ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_value" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_value") ?>">
		</td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_PACKAGE_FEE ?>:</strong></div></td>
		<td width="79%" >
		<input type="text" class="inputbox" name="shipping_rate_package_fee" size="32" maxlength="255" value="<?php $db->sp("shipping_rate_package_fee") ?>">
		</td>
	</tr>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_CURRENCY ?>:</strong></div></td>
		<td width="79%" >
		<?php $ps_html->list_currency_id("shipping_rate_currency_id",$db->sf("shipping_rate_currency_id")) ?>
		</td>
	</tr>
	<?php 
if (TAX_MODE == '1') { ?>
	<tr>
		<td width="21%" ><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_RATE_FORM_VAT_ID ?>:</strong></div></td>
		<td width="79%" >
		<?php
		require_once(CLASSPATH.'ps_tax.php');
		$ps_tax = new ps_tax;
		$ps_tax->list_tax_value("shipping_rate_vat_id",$db->sf("shipping_rate_vat_id")) ?>
		</td>
	</tr>
<?php 
} //end if TAX_MODE == '1' 
?>
</table>
<?php
// Add necessary hidden fields
$formObj->hiddenField( 'shipping_rate_id', $shipping_rate_id );

$funcname = !empty($shipping_rate_id) ? "rateupdate" : "rateadd";

// finally close the form:
$formObj->finishForm( $funcname, $modulename.'.rate_list', $option );
?>