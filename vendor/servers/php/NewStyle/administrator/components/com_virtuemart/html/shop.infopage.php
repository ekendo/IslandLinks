<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: shop.infopage.php,v 1.3.2.1 2006/02/27 19:41:42 soeren_nb Exp $
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

require_once(CLASSPATH . 'ps_product.php');
$ps_product = new ps_product;
require_once(CLASSPATH . 'ps_product_category.php');
$ps_product_category = new ps_product_category;
require_once(CLASSPATH . 'ps_product_attribute.php');
$ps_product_attribute = new ps_product_attribute;

?><h3><?php echo " " . $ps_product->get_vend_idname($vendor_id);?></h3>


<?php
  $q  = "SELECT * FROM #__{vm}_vendor WHERE ";
  $q .= "vendor_id='".intval($vendor_id)."'";
  $db->query($q);
  $db->next_record();
  
	$v_name=$db->f("vendor_name"); 
  $v_address_1=$db->f("vendor_address_1"); 
	$v_address_2=$db->f("vendor_address_2"); 
  $v_zip=$db->f("vendor_zip"); 
  $v_city=$db->f("vendor_city"); 
  $v_title=$db->f("contact_title");
  $v_first_name=$db->f("contact_first_name"); 
  $v_last_name=$db->f("contact_last_name"); 
  $v_fax=$db->f("contact_fax"); 
  $v_email=$db->f("contact_email");
  $v_logo=$db->f("vendor_full_image");
  $v_category = $db->f("vendor_store_name");
?>
   <br />
  <div align="center">
    <a href="<?php $db->p("vendor_url") ?>" target="blank">
      <img border="0" src="<?php echo IMAGEURL ?>vendor/<?php echo $v_logo; ?>">
    </a>
  </div>
  <br /><br />
  <table align="center" cellspacing="0" cellpadding="0" border="0">
      <tr valign="top"> 
        <th colspan="2" align="center" class="sectiontableheader">
          <strong><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_CONTACT_LBL ?></strong>
        </th>
	</tr>
	<tr valign="top">
	<td align="center" colspan="2"><br />
        <?php echo "&nbsp;" . $v_name . "<br />&nbsp;" . $v_address_1 . "<br />&nbsp;" . $v_address_2 . "<br />&nbsp;" . $v_zip . " " . $v_city; ?>
        <br /><br /></td>
  </tr>

	<tr>
      <td valign="top" align="center" colspan="2">
          <br /><?php echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_NAME ?>:&nbsp;<?php echo $v_title ." " . $v_first_name . " " . $v_last_name ?>
          <br /><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_PHONE ?>:&nbsp;<?php $db->p("contact_phone_1");?>
          <br /><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_FAX ?>:&nbsp;<?php echo $v_fax ?>
          <br /><?php echo $VM_LANG->_PHPSHOP_STORE_FORM_EMAIL ?>:&nbsp;<?php echo $v_email; ?><br />
          <br /><a href="<?php $db->p("vendor_url") ?>" target="_blank"><?php $db->p("vendor_url") ?></a><br />
      </td>
	</tr>
	<tr>
      <td valign="top" align="left" colspan="2">
          <br><?php $db->p("vendor_store_desc") ?><br />
      </td>
	</tr>
</table>