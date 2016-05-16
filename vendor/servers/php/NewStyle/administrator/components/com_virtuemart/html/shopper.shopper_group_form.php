<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shopper.shopper_group_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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

global $ps_product;
$shopper_group_id = mosgetparam( $_REQUEST, 'shopper_group_id', null );
$option = mosgetparam( $_REQUEST, 'option', 'com_virtuemart' );
//First create the object and let it print a form heading
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_SHOPPER_GROUP_FORM_LBL );
//Then Start the form
$formObj->startForm();

if (!empty($shopper_group_id)) {
   $q = "SELECT * FROM #__{vm}_shopper_group ";
   $q .= "WHERE shopper_group_id='$shopper_group_id'";
   if( !$perm->check("admin")) {
     $q .= " AND vendor_id = '$ps_vendor_id'";
   }
   $db->query($q);
   $db->next_record();
}
?>
<table class="adminform">
    <tr>
      <td width="23%" nowrap>
        <strong><div align="right"><?php echo $VM_LANG->_PHPSHOP_DEFAULT ?> ?:</div></strong>
      </td>
      <td width="77%" >
<?php 
	if($db->f("default")=="1") { ?>
		<img src="<?php echo $mosConfig_live_site ?>/administrator/images/tick.png" border="0" />
		<input type="hidden" name="default" value="1" />
      <?php 
	}
	else { ?>
		<input type="checkbox" name="default" value="1"  />
        <?php 
	} 
?>
      </td>
    </tr>
    <tr>
      <td width="23%" nowrap>
        <strong><div align="right"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_GROUP_FORM_NAME ?>:</div></strong>
      </td>
      <td width="77%" > 
        <input type="text" class="inputbox" name="shopper_group_name" size="18" value="<?php $db->sp('shopper_group_name') ?>" />
        </td>
    </tr>

<?php
    if( $perm->check("admin")) { 
      include_class("product");  ?>
      <tr> 
        <td width="23%"><strong><div align="right">
          <?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_VENDOR ?>:</div></strong>
        </td>
        <td width="77%" ><?php $ps_product->list_vendor($db->sf("vendor_id"));  ?></td>
      </tr>
    <?php
    }
    else{ 
      echo "<input type=\"hidden\" name=\"vendor_id\" value=\"$ps_vendor_id\" />";
    }
    $selected[0] = $db->f('show_price_including_tax') == "0" ? "selected=\"selected\"" : "";
    $selected[1] = $db->f('show_price_including_tax') == "1" ? "selected=\"selected\"" : "";
?>
    <tr>
      <td width="23%" nowrap><strong><div align="right"><?php
      echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICES_INCLUDE_TAX.": "; ?></div></strong>
      </td>
      <td width="77%" > 
        <select class="inputbox" name="show_price_including_tax">
          <option <?php echo $selected[0] ?> value="0"><?php echo _CMN_NO ?></option>
          <option <?php echo $selected[1] ?> value="1"><?php echo _CMN_YES ?></option>
        </select>&nbsp;
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICES_INCLUDE_TAX_EXPLAIN ); ?>
      </td>
    </tr> 
    <tr>
      <td width="23%" nowrap><strong><div align="right"><?php
      echo $VM_LANG->_PHPSHOP_SHOPPER_GROUP_FORM_DISCOUNT.": "; ?></div></strong>
      </td>
      <td width="77%" > 
        <input type="text" class="inputbox" name="shopper_group_discount" size="18" value="<?php $db->sp('shopper_group_discount') ?>" />
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_SHOPPER_GROUP_FORM_DISCOUNT_TIP ); ?>
      </td>
    </tr> 
    <tr> 
      <td width="23%" nowrap valign="top"><strong><div align="right">
      <?php echo $VM_LANG->_PHPSHOP_SHOPPER_GROUP_FORM_DESC ?>:</div></strong>
      </td>
      <td width="77%" valign="top" >
      <?php editorArea( 'editor1', $db->f('shopper_group_desc'), 'shopper_group_desc', '300', '100', '60', '4' ) ?>
      </td>
    </tr>
    <tr> 
      <td width="23%" nowrap valign="top" >&nbsp;</td>
      <td width="77%" valign="top" >&nbsp;</td>
    </tr>
</table>

<?php
// Add necessary hidden fields
$formObj->hiddenField( 'shopper_group_id', $shopper_group_id );

$funcname = !empty($shopper_group_id) ? "shopperGroupUpdate" : "shopperGroupAdd";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.shopper_group_list', $option );
?>