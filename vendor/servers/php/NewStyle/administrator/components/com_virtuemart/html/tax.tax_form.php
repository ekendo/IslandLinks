<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: tax.tax_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_TAX_FORM_LBL );
//Then Start the form
$formObj->startForm();

$tax_rate_id= mosgetparam( $_REQUEST, 'tax_rate_id');
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

if (!empty($tax_rate_id)) {
  $q = "SELECT * FROM #__{vm}_tax_rate WHERE tax_rate_id='$tax_rate_id'"; 
  $db->query($q);  
  $db->next_record();
}
?><br />

<table class="adminform">
    <tr> 
      <td><b><?php echo $VM_LANG->_PHPSHOP_TAX_FORM_LBL ?></b></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_TAX_FORM_COUNTRY ?>:</td>
      <td>
        <?php $ps_html->list_country("tax_country", $db->sf("tax_country"), "onchange=\"changeStateList();\"") ?> 
      </td>
    </tr>
    <tr align="center">
      <td colspan="2" >&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_TAX_FORM_STATE ?>:</td>
      <td><?php 
        //$ps_html->list_states("tax_state", $db->sf("tax_state")); 
        echo $ps_html->dynamic_state_lists( "tax_country", "tax_state", $db->sf("tax_country"), $db->sf("tax_state") );
        ?>
      </td>
    </tr>
    <tr align="center">
      <td colspan="2" >&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_TAX_FORM_RATE ?>:</td>
      <td> 
        <input type="text" class="inputbox" name="tax_rate" value="<?php $db->sp("tax_rate") ?>" size="16" />
      </td>
    </tr>
    <tr align="center">
      <td colspan="2" >&nbsp;</td>
    </tr>
</table>
<?php

// Add necessary hidden fields
$formObj->hiddenField( 'tax_rate_id', $tax_rate_id );

$funcname = !empty($tax_rate_id) ? "updatetaxrate" : "addtaxrate";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.tax_list', $option );
?>