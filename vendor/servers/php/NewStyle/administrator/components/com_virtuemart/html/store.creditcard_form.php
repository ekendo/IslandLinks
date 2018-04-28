<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: store.creditcard_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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

$creditcard_id = mosgetparam( $_REQUEST, 'creditcard_id', "");
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

if (!empty($creditcard_id)) {
    $q = "SELECT * FROM #__{vm}_creditcard WHERE creditcard_id='$creditcard_id'";
    $db->query($q);
    $db->next_record();
}

//First create the object and let it print a form heading
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_CREDITCARD_FORM_LBL );
//Then Start the form
$formObj->startForm();

?> 
<table class="adminform">
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="24%" align="right"><?php echo $VM_LANG->_PHPSHOP_CREDITCARD_NAME ?>:</td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="creditcard_name" value="<?php $db->sp("creditcard_name") ?>" />
      </td>
    </tr>
    <tr> 
      <td width="24%" align="right"><?php echo $VM_LANG->_PHPSHOP_CREDITCARD_CODE ?>:</td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="creditcard_code" value="<?php $db->sp("creditcard_code") ?>">
      </td>
    </tr>
</table>
<?php
// Add necessary hidden fields
$formObj->hiddenField( 'creditcard_id', $creditcard_id );

$funcname = !empty($creditcard_id) ? "creditcardUpdate" : "creditcardAdd";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.creditcard_list', $option );
?>