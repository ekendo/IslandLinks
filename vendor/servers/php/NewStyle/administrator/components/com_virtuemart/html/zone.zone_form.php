<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: zone.zone_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_ZONE_MOD );
//Then Start the form
$formObj->startForm();

$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;
$zone_id = mosgetparam( $_REQUEST, 'zone_id');

if (!empty($zone_id)) {
  $q = "SELECT * FROM #__{vm}_zone_shipping WHERE zone_id='$zone_id'"; 
  $db->query($q);  
  $db->next_record();
}  
?>
<br/>

<table class="adminform">
	<tr>
		<td valign="top">
			<div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_ZONE_FORM_NAME_LBL;?>:&nbsp;</strong></div>
		</td>
		<td valign="top">
		  <input type="text" name="zone_name" size="25" value="<?php echo $db->sp("zone_name");?>" />
		</td>
	</tr>
	<tr>
		<td valign="top">
			<div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_ZONE_FORM_DESC_LBL;?>:&nbsp;</strong></div>
		</td>
		<td valign="top">
		  <textarea name="zone_description" rows="7" cols="35"><?php echo $db->sp("zone_description");?></textarea>
		</td>
	</tr>
	<tr>
	  <td valign="top">
		  <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_ZONE_FORM_COST_PER_LBL;?>:&nbsp;</strong></div>
	  </td>
	  <td valign="top">
		<input type="text" name="zone_cost" size="5" value="<?php echo $db->sp("zone_cost");?>" />
	  </td>
	</tr>
	<tr>
	  <td valign="top">
		  <div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_ZONE_FORM_COST_LIMIT_LBL;?>:&nbsp;</strong></div>
	  </td>
	  <td valign="top">
		<input type="text" name="zone_limit" size="5" value="<?php echo $db->sp("zone_limit");?>" />
	  </td>
	</tr>
	<tr>
	  <td><div align="right"><strong><?php echo $VM_LANG->_PHPSHOP_UPS_TAX_CLASS ?></strong></div></td>
	  <td>
		<?php
		require_once(CLASSPATH.'ps_tax.php');
		ps_tax::list_tax_value("zone_tax_rate", $db->sf("zone_tax_rate")) ;
		echo mm_ToolTip($VM_LANG->_PHPSHOP_UPS_TAX_CLASS_TOOLTIP) ?>
	  </td>
	</tr>	
	<tr>
		<td valign="top" colspan="2">&nbsp; </td>
	</tr>
</table>
<?php 
// Add necessary hidden fields
$formObj->hiddenField( 'zone_id', $zone_id );

$funcname = !empty($zone_id) ? "updatezone" : "addzone";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.zone_list', $option );

?>