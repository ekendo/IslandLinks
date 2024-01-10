<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: checkout_register_form.php,v 1.13.2.3 2006/04/05 18:16:54 soeren_nb Exp $
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

$country = mosGetParam( $_REQUEST, 'country', $vendor_country_3_code);
$state = mosGetParam( $_REQUEST, 'state', '');

$missing = mosGetParam( $_REQUEST, "missing", "" );
$missing_style = "color:red; font-weight:bold;";

if (!empty( $missing )) {
	echo "<script type=\"text/javascript\">alert('"._CONTACT_FORM_NC."'); </script>\n";
}
$label_div_style = 'float:left;width:30%;text-align:right;vertical-align:bottom;font-weight: bold;padding-right: 5px;';
$field_div_style = 'float:left;width:60%;';
/**
 * This section will be changed in future releases of VirtueMart,
 * when we have a registration form manager
 */
$required_fields = Array( 'first_name', 'last_name', 'address_1', 'city', 'zip', 'country', 'phone_1' );

$shopper_fields = array();
// This is a list of all fields in the form
// They are structured into fieldset
// where the begin of the fieldset is marked by 
// an index called uniqid('fieldset_begin')
// and the end uniqid('fieldset_end')

if (!$my->id && VM_SILENT_REGISTRATION != '1' ) {
	// These are the fields for registering a completely new user!
	
	// Create a new fieldset
	$shopper_fields[uniqid('fieldset_begin')] = $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_INFO_LBL;
		$shopper_fields['username'] = _REGISTER_UNAME;
		$shopper_fields['email'] = _REGISTER_EMAIL;
		$shopper_fields['password'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_1;
		$shopper_fields['password2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_2;
	// Finish the fieldset
	$shopper_fields[uniqid('fieldset_end')] = "";
	// Add the new required fields into the existing array of required fields
	$required_fields = array_merge( $required_fields, Array( 'email', 'username','password','password2') );
}
// Now the fields for customer information...Bill To !
$shopper_fields[uniqid('fieldset_begin')] = $VM_LANG->_PHPSHOP_USER_FORM_BILLTO_LBL;
	$shopper_fields['company'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COMPANY_NAME;
	$shopper_fields['title'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_TITLE;
	$shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
	$shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
	$shopper_fields['middle_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_MIDDLE_NAME;
	$shopper_fields['address_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_1;
	$shopper_fields['address_2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_2;
	$shopper_fields['city'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY;
	$shopper_fields['zip'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP;
	$shopper_fields['country'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY;
	if (CAN_SELECT_STATES == '1') {
		$shopper_fields['state'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE;
		$required_fields[] = 'state';
	}
	$shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;
	$shopper_fields['phone_2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE2;
	$shopper_fields['fax'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FAX;
	if (!$my->id && VM_SILENT_REGISTRATION == '1') {
		$shopper_fields['email'] = _REGISTER_EMAIL;
		$required_fields[] = 'email';
	}
	
	// Extra Fields when defined in the language file
	for( $i=1; $i<6; $i++ ) {
		$property = "_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_$i";
		if( $VM_LANG->$property != "" ) {
			$shopper_fields['extra_field_'.$i] = $VM_LANG->$property;
		}
	}

$shopper_fields[uniqid('fieldset_end')] = "";

// Is entering bank account information possible?
if (LEAVE_BANK_DATA == '1') { 
    $selected[0] = @$_REQUEST['bank_account_type']=="Checking" ? 'selected="selected"' : '';
    $selected[1] = @$_REQUEST['bank_account_type']=="Business Checking" ? 'selected="selected"' : '';
    $selected[2] = @$_REQUEST['bank_account_type']=="Savings" ? 'selected="selected"' : '';
    
    $shopper_fields[uniqid('fieldset_begin')] = $VM_LANG->_PHPSHOP_ACCOUNT_BANK_TITLE;
	    $shopper_fields['bank_account_holder'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_ACCOUNT_HOLDER;
	    $shopper_fields['bank_account_nr'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_ACCOUNT_NR;
	    $shopper_fields['bank_sort_code'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_SORT_CODE;
	    $shopper_fields['bank_name'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_NAME;
	    $shopper_fields['bank_account_type'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE;
	    $shopper_fields['bank_iban'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_IBAN;
    $shopper_fields[uniqid('fieldset_end')] = "";
}

// Does the customer have to agree to your Terms & Conditions?
if (MUST_AGREE_TO_TOS == '1') {
	$shopper_fields[uniqid('fieldset_begin')] = _BUTTON_SEND_REG;
		// This label is a JS link with a noscript alternative for non-JS users
		$shopper_fields['agreed'] = '<script type="text/javascript">//<![CDATA[
			document.write(\'<label for="agreed_field"><a href="javascript:void window.open(\\\''. $mosConfig_live_site .'/index2.php?option=com_virtuemart&page=shop.tos&pop=1\\\', \\\'win2\\\', \\\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\\\');">\');
			document.write(\''.htmlspecialchars( $VM_LANG->_PHPSHOP_I_AGREE_TO_TOS, ENT_QUOTES ) .'</a></label>\');
			//]]></script>
			<noscript><label for="agreed_field"><a target="_blank" href="'. $mosConfig_live_site .'/index.php?option=com_virtuemart&page=shop.tos" title="'. $VM_LANG->_PHPSHOP_I_AGREE_TO_TOS .'">
			'. $VM_LANG->_PHPSHOP_I_AGREE_TO_TOS .'</a></label></noscript>';
		$required_fields[] = 'agreed';
	$shopper_fields[uniqid('fieldset_end')] = "";
}
// Form validation function
vmCommonHTML::printJS_formvalidation( $required_fields );
?>
<script language="javascript" type="text/javascript" src="includes/js/mambojavascript.js"></script>

<form action="<?php echo $mm_action_url ?>index.php" method="post" name="adminForm">
	
<div style="width:90%;">
	<div style="padding:5px;text-align:center;"><strong>(* = <?php echo _CMN_REQUIRED ?>)</strong></div>
   <?php
   foreach( $shopper_fields as $fieldname => $label) {
   		if( stristr( $fieldname, 'fieldset_begin' )) {
   			echo '<fieldset>
			     <legend class="sectiontableheader">'.$label.'</legend>
			     ';
   			continue;
   		}
   		if( stristr( $fieldname, 'fieldset_end' )) {
   			echo '</fieldset>
			     ';
   			continue;
   		}
   		echo '<div id="'.$fieldname.'_div" style="'.$label_div_style;
   		if (stristr($missing,$fieldname)) {
   			echo $missing_style;
   		}
   		echo '">';
        echo '<label for="'.$fieldname.'_field">'.$label.'</label>';
        if( in_array( $fieldname, $required_fields)) {
        	echo '<strong>* </strong>';
        }
      	echo ' </div>
      <div style="'.$field_div_style.'">'."\n";
      	
      	/**
      	 * This is the most important part of this file
      	 * Here we print the field & its contents!
      	 */
   		switch( $fieldname ) {
   			case 'title':
   				$ps_html->list_user_title(mosGetParam( $_REQUEST, 'title', ''), "id=\"user_title\"");
   			break;
   			
   			case 'country':
   				if( CAN_SELECT_STATES ) {
   					$onchange = "onchange=\"changeStateList();\"";
   				}
   				else {
   					$onchange = "";
   				}
   				$ps_html->list_country("country", $country, "id=\"country_field\" $onchange");
   				break;
   			
   			case 'state':
   				echo $ps_html->dynamic_state_lists( "country", "state", $country, $state );
			    echo "<noscript>\n";
			    $ps_html->list_states("state", $state, "", "id=\"state\"");
			    echo "</noscript>\n";
   				break;
			    
			case 'bank_account_type':
				echo '<select class="inputbox" name="bank_account_type">
			            <option '. $selected[0] .' value="Checking">'. $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_CHECKING .'</option>
			            <option '. $selected[1] .' value="Business Checking">'. $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_BUSINESSCHECKING .'</option>
			            <option '. $selected[2] .' value="Savings">'. $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_SAVINGS .'</option>
			          </select>';
				break;
				
			case 'agreed':
				echo '<input type="checkbox" id="agreed_field" name="agreed" value="1" class="inputbox" />';
				break;
			case 'password':
			case 'password2':
				echo '<input type="password" id="'.$fieldname.'_field" name="'.$fieldname.'" size="30" class="inputbox" />'."\n";
	   			break;
	   		
	   		case 'extra_field_4': case 'extra_field_5':
	   			eval( "\$ps_html->list_extra_field_$i( mosGetParam( \$_REQUEST, 'extra_field_$i'), \"id=\\\"extra_field_$i\\\"\");" );
	   			break;
	   			
   			default:
		        echo '<input type="text" id="'.$fieldname.'_field" name="'.$fieldname.'" size="30" value="'. mosGetParam( $_REQUEST, $fieldname) .'" class="inputbox" />'."\n";
	   			break;
   		}
   		
   		echo '</div>
			      <br/><br/>';
   }
   
    echo '
	<div align="center">';
    
	if( !$mosConfig_useractivation && VM_SILENT_REGISTRATION != '1') {
		echo '<input type="checkbox" name="remember" value="yes" id="remember_login2" checked="checked" />
		<label for="remember_login2">'. _REMEMBER_ME .'</label><br /><br />';
	}
	else {
		echo '<input type="hidden" name="remember" value="yes" />';
	}
	echo '
		<input type="submit" value="'. _BUTTON_SEND_REG . '" class="button" onclick="return( submitregistration());" />
	</div>
	<input type="hidden" name="Itemid" value="'. @$_REQUEST['Itemid'] .'" />
	<input type="hidden" name="gid" value="'. $my->gid .'" />
	<input type="hidden" name="id" value="'. $my->id .'" />
	<input type="hidden" name="user_id" value="'. $my->id .'" />
	<input type="hidden" name="option" value="com_virtuemart" />
	
	<input type="hidden" name="useractivation" value="'. $mosConfig_useractivation .'" />
	<input type="hidden" name="func" value="shopperadd" />
	<input type="hidden" name="page" value="checkout.index" />
	</form>
</div>';
	
?>