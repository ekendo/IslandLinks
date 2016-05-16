<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: admin.curr_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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

$currency_id = mosgetparam( $_REQUEST, 'currency_id');
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

if ($currency_id) {
    $q = "SELECT * from #__{vm}_currency WHERE currency_id='$currency_id'";
    $db->query($q);
    $db->next_record();
}
  
//First create the object and let it print a form heading
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_CURRENCY_LIST_ADD );
//Then Start the form
$formObj->startForm();

?> 
<table class="adminform">
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="24%" align="right"><?php echo $VM_LANG->_PHPSHOP_CURRENCY_LIST_NAME ?>:</td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="currency_name" value="<?php $db->sp("currency_name") ?>" />
      </td>
    </tr>
    <tr> 
      <td width="24%" align="right"><?php echo $VM_LANG->_PHPSHOP_CURRENCY_LIST_CODE ?>:</td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="currency_code" value="<?php $db->sp("currency_code") ?>" />
      </td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
<?php
// Add necessary hidden fields
$formObj->hiddenField( 'currency_id', $currency_id );

$funcname = !empty($currency_id) ? "currencyUpdate" : "currencyAdd";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, 'admin.curr_list', $option );
?>