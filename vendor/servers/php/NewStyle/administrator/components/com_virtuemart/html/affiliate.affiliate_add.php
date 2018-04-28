<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: affiliate.affiliate_add.php,v 1.3.2.1 2005/11/30 20:18:59 soeren_nb Exp $
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
?>
<h2><?php echo $VM_LANG->_PHPSHOP_AFFILIATE_MOD ?></h2>

 
<h2><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_LBL ?></h2>
<?php 

 include_class ( 'shopper' );

if (isset($user_id)) {
   $q = "SELECT * from #__users, #__{vm}_shopper_vendor_xref ";
   $q .= "WHERE #__users.id='$user_id' ";
   //$q .= "AND #__{vm}_shopper_vendor_xref.user_id='$user_id' ";
   $db->query($q);
   $db->next_record();
}
?> 
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" name="adminForm" class="adminform">
  <input type="hidden" name="user_info_id" value="<?php $db->sp("user_info_id"); ?>">
  <input type="hidden" name="cache" value="0">
  <input type="hidden" name="func" value="<?php echo isset($user_id) ? "affiliateAdd" : "shopperAdd"; ?>">
  <input type="hidden" name="page" value="<?php echo $modulename?>.shopper_list">
  <input type="hidden" name="option" value="com_virtuemart">
  <input type="hidden" name="task" value="">
  <table width="95%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="2" valign="top"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME ?>:</td>
            <td width="76%" > 
              <input type="text" name="first_name" size="18" value="<?php $db->sp("first_name") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME ?>:</td>
            <td width="76%" > 
              <input type="text" name="last_name" size="18" value="<?php $db->sp("last_name") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_MIDDLE_NAME ?>:</td>
            <td width="76%" > 
              <input type="text" name="middle_name" size="16" value="<?php $db->sp("middle_name") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_TITLE ?>:</td>
            <td width="76%" > <?php $ps_html->list_user_title($db->sf("title")); ?></td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_USERNAME ?>:</td>
            <td width="76%" > 
              <input type="text" name="username" size="16" value="<?php $db->sp("username") ?>">
            </td> 
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" > <?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_GROUP ?>:</td>
            <td width="76%" ><?php
                $ps_shopper_group->list_shopper_groups("shopper_group_id",$db->sf("shopper_group_id")); ?> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" nowrap>&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2" nowrap><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_BILLTO_LBL ?></b></td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_COMPANY_NAME ?>:</td>
            <td width="76%" > 
              <input type="text" name="company" size="24" value="<?php $db->sp("company") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE ?>:</td>
            <td width="76%" > 
              <input type="text" name="state" size="3" value="<?php $db->sp("state") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP ?>:</td>
            <td width="76%" > 
              <input type="text" name="zip" size="10" value="<?php $db->sp("zip") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY ?>:</td>
            <td width="76%" > 
              <input type="text" name="country" size="16" value="<?php $db->sp("country") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" > <?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE ?>:</td>
            <td width="76%" > 
              <input type="text" name="phone_1" size="12" value="<?php $db->sp("phone_1") ?>">
            </td>
          </tr>
          <tr> 
            <td width="24%" nowrap align="right" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EMAIL ?>:</td>
            <td width="76%" > 
              <input type="text" name="user_email" size="24" value="<?php $db->sp("user_email") ?>">
            </td>
          </tr>
          <tr> 
            <td nowrap colspan="2">&nbsp; </td>
          </tr>
   </table>
            


</td>
          </tr>
</table>
</form>

