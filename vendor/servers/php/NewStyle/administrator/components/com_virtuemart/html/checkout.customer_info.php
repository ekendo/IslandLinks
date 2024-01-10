<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* This file show the customer information in a table
* while checking out
*
* @version $Id: checkout.customer_info.php,v 1.4 2005/10/04 18:30:34 soeren_nb Exp $
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

$db = new ps_DB;
$q  = "SELECT * FROM #__{vm}_user_info WHERE user_id='" . $auth["user_id"] . "' ";
$q .= "AND address_type='BT'";
$db->query($q);
$db->next_record(); ?>

<!-- Customer Information --> 
    <table border="0" cellspacing="0" cellpadding="2" width="100%">
        <tr class="sectiontableheader">
            <th colspan="2" align="left"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL ?></th>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?>: </td>
           <td width="90%">
           <?php
             $db->p("company");
           ?>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?>: </td>
           <td width="90%"><?php
             echo $db->f("first_name"). " " . $db->f("middle_name") ." " . $db->f("last_name"); ?>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?>: </td>
           <td width="90%">
           <?php
             $db->p("address_1");
             echo "<br />";
             $db->p("address_2");
           ?>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right">&nbsp;</td>
           <td width="90%">
           <?php
             $db->p("city");
             echo ",";
             $db->p("state");
             echo " ";
             $db->p("zip");
             echo "<br /> ";
             $db->p("country");
           ?>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>: </td>
           <td width="90%">
           <?php
             $db->p("phone_1");
           ?>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap"width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?>: </td>
           <td width="90%">
           <?php
             $db->p("fax");
           ?>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?>: </td>
           <td width="90%">
           <?php
             $db->p("user_email");
           ?>
           </td>
        </tr>
        <tr><td align="center" colspan="2"><a href="<?php $sess->purl( SECUREURL ."index.php?page=account.billing&next_page=$page"); ?>">
            (<?php echo $VM_LANG->_PHPSHOP_UDATE_ADDRESS ?>)</a>
            </td>
        </tr>
    </table>
    <!-- customer information ends -->
    <br />
