<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: admin.user_form.php,v 1.5.2.7 2006/06/29 18:27:12 soeren_nb Exp $
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

global $acl;
if (!$acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_users' )) {
	mosRedirect( $_SERVER['PHP_SELF'], _NOT_AUTH );
}
global $ps_shopper_group;
include_class( 'shopper' );

if( !isset($ps_shopper_group)) {
        $ps_shopper_group = new ps_shopper_group();
}

$user_id = intval( mosGetParam( $_REQUEST, 'user_id' ));

if( !empty($user_id) ) {
        $q = "SELECT * FROM #__users AS u LEFT JOIN #__{vm}_user_info AS ui ON id=user_id ";
        $q .= "WHERE id=$user_id ";
        $q .= "AND (address_type='BT' OR address_type IS NULL ) ";
        $q .= "AND gid <= ".$my->gid;
        $db->query($q);
	$db->next_record();
}

//First create the object and let it print a form heading
$formObj = &new formFactory( $VM_LANG->_PHPSHOP_USER_FORM_LBL );
//Then Start the form
$formObj->startForm();

$tabs = new mShopTabs(0, 1, "_userform");
$tabs->startPane("userform-pane");
$tabs->startTab( 'General User Information', "userform-page");

$_REQUEST['cid'][0] = $user_id;
$_REQUEST['task'] = $task = 'edit';
$GLOBALS['option'] = 'com_users'; // Cheat Joomla 1.1
$mainframe->_path->admin_html = $mosConfig_absolute_path.'/administrator/components/com_users/admin.users.html.php';
require_once( $mainframe->_path->admin_html );
$mainframe->_path->class = $mosConfig_absolute_path.'/administrator/components/com_users/users.class.php';
ob_start();
require( $mosConfig_absolute_path.'/administrator/components/com_users/admin.users.php' );
$userform = ob_get_contents();
ob_end_clean();

$userform = str_replace( '<form action="index2.php" method="post" name="adminForm">', '', $userform );
$userform = str_replace( '</form>', '', $userform );
$userform = str_replace( '<div id="editcell">', '', $userform );
$userform = str_replace( '</table>
                </div>', '</table>', $userform );
echo $userform;

$_REQUEST['option'] = $GLOBALS['option'] = 'com_virtuemart';

$tabs->endTab();

$tabs->startTab( $VM_LANG->_PHPSHOP_USER_FORM_BILLTO_LBL, "billto-page");
?>
<table class="adminform">  
        <tr> 
                <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_TITLE ?>:</td> 
                <td width="62%" > 
                        <?php $ps_html->list_user_title($db->sf("title")); ?> 
                </td> 
        </tr>
        <tr> 
                <td nowrap="nowrap" style="text-align:right;" width="38%"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_FIRST_NAME ?>:</td> 
                <td width="62%"> 
                        <input type="text" name="first_name" size="18" value="<?php $db->sp("first_name") ?>" /> 
                </td> 
        </tr> 
        <tr> 
                <td nowrap="nowrap" style="text-align:right;" width="38%"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_LAST_NAME ?>:</td> 
                <td width="62%"> 
                <input type="text" name="last_name" size="18" value="<?php $db->sp("last_name") ?>" /> 
                </td> 
        </tr> 
        <tr> 
                <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_MIDDLE_NAME ?>:</td> 
                <td width="62%" > 
                        <input type="text" name="middle_name" size="16" value="<?php $db->sp("middle_name") ?>" /> 
                </td> 
        </tr>
        <tr> 
                <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_FORM_VENDOR ?>:</td>
                <td><?php $ps_product->list_vendor($db->f("vendor_id"));  ?></td>
        </tr>
	<tr> 
                <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_PERMS ?>:</td> 
                <td width="62%" > 
                        <?php
                        if( !isset( $ps_user)) { $ps_user = new ps_user(); }
                        $ps_user->list_perms("perms", $db->sf("perms"));
                        ?> 
                </td> 
        </tr> 
</table> 

<h3><?php echo $VM_LANG->_PHPSHOP_USER_FORM_BILLTO_LBL ?></h3>

<table class="adminform"> 
          <tr> 
             <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_COMPANY_NAME ?>:</td> 
             <td width="62%" > 
              <input type="text" name="company" size="24" value="<?php $db->sp("company") ?>" /> 
            </td> 
           </tr> 
          <tr> 
             <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_1 ?>: </td> 
             <td width="62%" > 
              <input type="text" name="address_1" size="24" value="<?php $db->sp("address_1") ?>" /> 
            </td> 
           </tr> 
          <tr> 
             <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_2 ?>: </td> 
             <td width="62%" > 
              <input type="text" name="address_2" size="24" value="<?php $db->sp("address_2") ?>" /> 
            </td> 
           </tr> 
          <tr> 
             <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_CITY ?>:</td> 
             <td width="62%" > 
              <input type="text" name="city" size="18" value="<?php $db->sp("city") ?>" /> 
            </td> 
           </tr> 
          <tr> 
             <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ZIP ?>:</td> 
             <td width="62%" > 
              <input type="text" name="zip" size="10" value="<?php $db->sp("zip") ?>" /> 
            </td> 
           </tr>
          <tr> 
            <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_COUNTRY ?>:</td>
            <td > 
              <?php $ps_html->list_country("country", $db->sf("country"), "id=\"country_field\" onchange=\"changeStateList();\"") ?>
            </td>
          </tr>
          <tr> 
            <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_STATE ?>:</td>
            <td ><?php
              echo $ps_html->dynamic_state_lists( "country", "state", $db->sf('country'), $db->sf('state') );
              ?>
                        </td>
                </tr>
          <tr> 
          <tr> 
             <td nowrap="nowrap" style="text-align:right;" width="38%" > <?php echo $VM_LANG->_PHPSHOP_USER_FORM_PHONE ?>:</td> 
             <td width="62%" > 
              <input type="text" name="phone_1" size="12" value="<?php $db->sp("phone_1") ?>" /> 
            </td> 
           </tr> 
          <tr> 
            <td style="text-align:right;" > <?php echo $VM_LANG->_PHPSHOP_USER_FORM_PHONE2 ?>:</td>
            <td > 
              <input type="text" class="inputbox" name="phone_2" size="40" value="<?php $db->sp("phone_2") ?>">
            </td>
        </tr>
          <tr> 
             <td nowrap="nowrap" style="text-align:right;" width="38%" ><?php echo $VM_LANG->_PHPSHOP_USER_FORM_FAX ?>:</td> 
             <td width="62%" > 
              <input type="text" name="fax" size="12" value="<?php $db->sp("fax") ?>" /> 
            </td> 
           </tr> 
        <tr> 
                <td nowrap="nowrap" width="38%">&nbsp; </td> 
                <td nowrap="nowrap" width="62%">&nbsp;</td> 
        </tr> 
        <?php 
        /**
        <!-- If you do not wish show a EXTRA FIELD in this form add into condition "false && ".
                         For example: if( false && $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) -->
                <!-- EXTRA FIELD 1 - BEGIN - You can move this section into any other position of form. -->
        */
        if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) { ?>
                <tr> 
                        <td style="text-align:right;" ><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 ?>:</td>
                        <td > 
                        <input type="text" class="inputbox" name="extra_field_1" size="40" value="<?php $db->sp("extra_field_1") ?>">
                        </td>
                </tr>
                <?php 
        }
        if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2 != "" ) { ?>
                <tr> 
                        <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2 ?>:</td>
                        <td > 
                        <input type="text" class="inputbox" name="extra_field_2" size="40" value="<?php $db->sp("extra_field_2") ?>">
                        </td>
                </tr>
                <?php 
        }
        if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3 != "" ) { ?>
                <tr> 
                        <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3 ?>:</td>
                        <td > 
                                <input type="text" class="inputbox" name="extra_field_3" size="40" value="<?php $db->sp("extra_field_3") ?>">
                        </td>
                </tr>
                <?php 
        } 
        if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4 != "" ) { ?>
                <tr> 
                        <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4 ?>:</td>
                        <td ><?php $ps_html->list_extra_field_4($db->sf("extra_field_4")); ?></td>
                </tr>
                <?php 
        }
        if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5 != "" ) { ?>
                <tr> 
                        <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5 ?>:</td>
                        <td ><?php $ps_html->list_extra_field_5($db->sf("extra_field_5")); ?></td>
                </tr>
                <?php 
        }
        # <!-- EXTRA FIELD 5 - END -->
        ?>
       
</table> 
       
<?php 
if( $db->f("user_id") ) { 
?> 
     
         <h3><?php echo $VM_LANG->_PHPSHOP_USER_FORM_SHIPTO_LBL ?>

		<a href="<?php $sess->purl($_SERVER['PHP_SELF'] . "?page=$modulename.user_address_form&amp;user_id=$user_id") ?>" >
		(<?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADD_SHIPTO_LBL ?>)</a></h3> 
       
	<table class="adminlist"> 
		<tr> 
			<td > 
				  <?php
			$qt = "SELECT * from #__{vm}_user_info WHERE user_id='$user_id' AND address_type='ST'"; 
			$dbt = new ps_DB;
			$dbt->query($qt);
			if (!$dbt->num_rows()) {
			  echo "No shipping addresses.";
			}
			else {
			  while ($dbt->next_record()) {
				$url = $sess->url( $_SERVER['PHP_SELF'] . "?page=$modulename.user_address_form&user_id=$user_id&user_info_id=" . $dbt->f("user_info_id"));
				echo '&raquo; <a href="' . $sess->url($url) . '">';
				echo $dbt->f("address_type_name") . "</a><br/>";
			  }
			} ?> 
			</td> 
		</tr> 
	</table> 
         <?php 
}

$tabs->endTab();
$tabs->startTab( $VM_LANG->_PHPSHOP_SHOPPER_FORM_LBL, "third-page");

  $selected[0] = $db->sf('bank_account_type')=="Checking" ? 'selected="selected"' : '';
  $selected[1] = $db->sf('bank_account_type')=="Business Checking" ? 'selected="selected"' : '';
  $selected[2] = $db->sf('bank_account_type')=="Savings" ? 'selected="selected"' : '';
?>
        
<h3><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_LBL ?> </h3>
<table class="adminform">
          <tr> 
            <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_CUSTOMER_NUMBER ?>:</td>
            <td > 
              <input type="text" class="inputbox" name="customer_number" size="40" value="<?php echo $ps_shopper_group->get_customer_num($db->f("user_id")) ?>" />
            </td>
          </tr>
          <tr> 
            <td style="text-align:right;"> <?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_GROUP ?>:</td>
            <td><?php
                include_class('shopper');
                $sg_id = $ps_shopper_group->get_shoppergroup_by_id($db->f("user_id"));
                $ps_shopper_group->list_shopper_groups("shopper_group_id",$sg_id["shopper_group_id"]);?>
            </td>
          </tr>
</table>

<h3><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_BANK_TITLE ?> </h3>

<table class="adminform">
        <tr>
                <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_ACCOUNT_HOLDER ?>:</td>
                <td><input type="text" class="inputbox" name="bank_account_holder" size="40" value="<?php $db->sp("bank_account_holder") ?>"></td>
        </tr>
        <tr>
                <td style="text-align:right;" width="100"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_ACCOUNT_NR ?>:</td>
                <td width="85%" ><input type="text" class="inputbox" name="bank_account_nr" size="40" value="<?php $db->sp("bank_account_nr") ?>"></td>
        </tr>
        <tr>
                <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_SORT_CODE ?>:</td>
                <td><input type="text" class="inputbox" name="bank_sort_code" size="40" value="<?php $db->sp("bank_sort_code") ?>"></td>
        </tr>
        <tr>
                <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_NAME ?>:</td>
                <td><input type="text" class="inputbox" name="bank_name" size="40" value="<?php $db->sp("bank_name") ?>"></td>
        </tr>
        <tr>
                <td style="text-align:right;"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_IBAN ?>:</td>
                <td><input type="text" class="inputbox" name="bank_iban" size="40" value="<?php $db->sp("bank_iban") ?>"></td>
        </tr>
        <tr>
                <td width="27%" nowrap="nowrap" style="text-align:right;" ><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE ?>:</td>
                <td width="73%" >
                        <select class="inputbox" name="bank_account_type">
                                <option <?php echo $selected[0] ?> value="Checking"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_CHECKING ?></option>
                                <option <?php echo $selected[1] ?> value="Business Checking"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_BUSINESSCHECKING ?></option>
                                <option <?php echo $selected[2] ?> value="Savings"><?php echo $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_SAVINGS ?></option>
                        </select>
                </td>
        </tr>
</table>

<?php
$tabs->endTab();
$tabs->endPane();

// Add necessary hidden fields
$formObj->hiddenField( 'address_type', 'BT' );
$formObj->hiddenField( 'address_type_name', '-default-' );
$formObj->hiddenField( 'user_id', $user_id );

$funcname = $db->f("user_id") ? "userUpdate" : "userAdd";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, 'admin.user_list', $option );
?>
