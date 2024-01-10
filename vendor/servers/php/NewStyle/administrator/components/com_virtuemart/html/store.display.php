<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: store.display.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
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
<br />
<table>
<tr>
<td width="60"><img src="<?php echo IMAGEURL ?>ps_image/store.gif" border="0" /></td>
<td align="left"><span class="sectionname"><?php echo $VM_LANG->_PHPSHOP_STORE_MOD ?></span></td>
</tr>
</table>
<br /><br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td colspan="2" align="right"> 
      <div align="left"><b><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_LBL ?> </B>

<?php   $q = "SELECT * from #__{vm}_vendor WHERE vendor_id='$ps_vendor_id'";
        $db->query($q);
        $db->next_record();
?></div>
    </td>
  </tr>
  <tr> 
    <td width="22%" align="right" ><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_STORE_NAME ?> :</td>
    <td width="78%" > <?php $db->sp("vendor_store_name") ?></td>
  </tr>
  <tr> 
    <td width="22%" align="right" ><?php echo $VM_LANG->_PHPSHOP_VENDOR_LIST_VENDOR_NAME ?> :</td>
    <td width="78%" > <?php $db->sp("vendor_name") ?> </td>
  </tr>
  <tr> 
    <td width="22%" align="right" >&nbsp;</td>
    <td width="78%" > <?php $db->sp("vendor_address_1") ?><?php 
if ($db->sf("vendor_address_2"))
$db->sp("vendor_address_2") 
?></td>
  </tr>
  <tr> 
    <td width="22%" align="right" >&nbsp;</td>
    <td width="78%" > <?php $db->sp("vendor_city") ?>, <?php $db->sp("vendor_state") ?> 
      <?php $db->sp("vendor_zip") ?> <?php $db->sp("vendor_country") ?></td>
  </tr>
  <tr> 
    <td width="22%" align="right" ><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_PHONE ?> :</td>
    <td width="78%" > <?php $db->sp("vendor_phone") ?></td>
  </tr>
  <tr> 
    <td colspan="2" class="topmenu"><b><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_CONTACT_LBL ?></b></td>
  </tr>
  <tr> 
    <td width="22%" align="right" ><?php echo $VM_LANG->_PHPSHOP_CART_NAME ?> :</td>
    <td width="78%" > <?php $db->sp("contact_title") ?> <?php $db->sp("contact_first_name") ?> 
      <?php $db->sp("contact_middle_name") ?> <?php $db->sp("contact_last_name") ?></td>
  </tr>
  <tr> 
    <td width="22%" align="right" ><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_PHONE_1 ?> :</TD>
    <TD WIDTH="78%" > <?php $db->sp("contact_phone_1") ?></TD>
  </TR>
  <TR> 
    <TD WIDTH="22%" ALIGN="right" ><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_PHONE_2 ?> :</TD>
    <TD WIDTH="78%" > <?php $db->sp("contact_phone_2") ?></TD>
  </TR>
  <TR> 
    <TD WIDTH="22%" ALIGN="right" ><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_FAX ?> :</TD>
    <TD WIDTH="78%" > <?php $db->sp("contact_fax") ?></TD>
  </TR>
  <TR> 
    <TD WIDTH="22%" ALIGN="right" ><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_EMAIL ?> :</TD>
    <TD WIDTH="78%" > <?php $db->sp("contact_email") ?></TD>
  </TR>
  <TR> 
    <TD COLSPAN="2" ALIGN="center" >&nbsp; </TD>
  </TR>
</TABLE>
<h2>&nbsp;</H2>
