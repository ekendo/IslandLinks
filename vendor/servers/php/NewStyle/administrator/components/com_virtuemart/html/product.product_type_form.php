<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: product.product_type_form.php,v 1.5.2.1 2006/04/27 19:35:52 soeren_nb Exp $
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
global $ps_product_type;
$product_type_id = mosgetparam($_REQUEST, 'product_type_id', 0);
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

//First create the object and let it print a form heading
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_LBL );
//Then Start the form
$formObj->startForm();

if ($product_type_id) {
    $q = "SELECT * from #__{vm}_product_type WHERE product_type_id='$product_type_id'";
    $db->query($q);
    $db->next_record();
} 
elseif (empty($vars["error"])) {
    $default["product_type_publish"] = "Y";
}
?> 
<table class="adminform">
	<tr> 
                  <td width="38%" nowrap align="right"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_PUBLISH ?>:</td>
                  <td width="62%"><?php 
                    if ($db->sf("product_type_publish")=="Y") { 
                      echo "<input type=\"checkbox\" name=\"product_type_publish\" value=\"Y\" checked=\"checked\" />";
                    } 
                    else {
                      echo "<input type=\"checkbox\" name=\"product_type_publish\" value=\"Y\" />";
                    }
                  ?> 
                  </td>
	</tr>
	<tr> 
                  <td width="38%" nowrap align="right"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_NAME ?>:</td>
                  <td width="62%"> 
                    <input type="text" class="inputbox" name="product_type_name" size="60" value="<?php $db->sp('product_type_name') ?>" />
                  </td>
	</tr>
	<tr> 
                  <td width="38%" nowrap align="right" valign="top"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_DESCRIPTION ?>:</td>
                  <td width="62%" valign="top"><?php
                    editorArea( 'editor1', $db->f("product_type_description"), 'product_type_description', '300', '100', '60', '6' ) ?>
                    <!--input type="text" class="inputbox" name="product_type_description" size="60" value="<?php // $db->sp('product_type_description') ?>" /-->
		  </td>
	</tr>
	<tr>
                  <td align="right"><?php echo $VM_LANG->_PHPSHOP_MODULE_LIST_ORDER ?>: </td>
                  <td valign="top"><?php 
                    echo $ps_product_type->list_order( $db->f("product_type_id"), $db->f("product_type_list_order"));
                    echo "<input type=\"hidden\" name=\"currentpos\" value=\"".$db->f("product_type_list_order")."\" />";
                  ?>
                  </td>
	</tr>
	<tr>
                  <td colspan="2"><br /></td>
	</tr>
	<tr>
                  <td align="right"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_BROWSEPAGE ." ". $VM_LANG->_PHPSHOP_LEAVE_BLANK ?>: </td>
                  <td valign="top">
                  <input type="text" class="inputbox" name="product_type_browsepage" value="<?php $db->sp("product_type_browsepage"); ?>" />
                  </td>
	</tr>
	<tr>
                  <td align="right">
                    <?php echo $VM_LANG->_PHPSHOP_PRODUCT_TYPE_FORM_FLYPAGE ." ". $VM_LANG->_PHPSHOP_LEAVE_BLANK ?>:
                  </td>
                  <td valign="top">
                  <input type="text" class="inputbox" name="product_type_flypage" value="<?php $db->sp("product_type_flypage"); ?>" />
                  </td>
	</tr>
</table>
<?php
// Add necessary hidden fields
$formObj->hiddenField( 'product_type_id', $product_type_id );

$funcname = !empty($product_type_id) ? "ProductTypeUpdate" : "ProductTypeAdd";

// finally close the form:
$formObj->finishForm( $funcname, $modulename.'.product_type_list', $option );
?>