<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: account.billing.php,v 1.6.2.2 2006/03/21 19:38:23 soeren_nb Exp $
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

$mainframe->setPageTitle( $VM_LANG->_PHPSHOP_USER_FORM_BILLTO_LBL );
      
$next_page = mosGetParam( $_REQUEST, "next_page", "account.index");
$Itemid = mosGetParam( $_REQUEST, "Itemid", null);

$missing = mosGetParam( $vars, 'missing' );
$missing_style = "color: Red; font-weight: Bold;";

$label_div_style = 'float:left;width:30%;text-align:right;vertical-align:bottom;font-weight: bold;padding-right: 5px;';
$field_div_style = 'float:left;width:60%;';
/**
 * This section will be changed in future releases of VirtueMart,
 * when we have a registration form manager
 */
$required_fields = Array( 'email', 'first_name', 'last_name', 'address_1', 'city', 'zip', 'country', 'phone_1' );

$shopper_fields = array();
// This is a list of all fields in the form
// They are structured into fieldset
// where the begin of the fieldset is marked by 
// an index called uniqid('fieldset_begin')
// and the end uniqid('fieldset_end')
$shopper_fields[uniqid('fieldset_begin')] = $VM_LANG->_PHPSHOP_ACC_ACCOUNT_INFO;
	$shopper_fields['username'] = _REGISTER_UNAME;
	$shopper_fields['password'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_1;
	$shopper_fields['password2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_2;
	$shopper_fields['email'] = _REGISTER_EMAIL;
$shopper_fields[uniqid('fieldset_end')]	 = "";

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
$shopper_fields[uniqid('fieldset_end')] = "";

if (LEAVE_BANK_DATA == '1') { 
	
    $selected[0] = $db->sf("bank_account_type")=="Checking" ? 'selected="selected"' : '';
    $selected[1] = $db->sp("bank_account_type")=="Business Checking" ? 'selected="selected"' : '';
    $selected[2] = $db->sp("bank_account_type")=="Savings" ? 'selected="selected"' : '';
    
    $shopper_fields[uniqid('fieldset_begin')] = $VM_LANG->_PHPSHOP_ACCOUNT_BANK_TITLE;
	    $shopper_fields['bank_account_holder'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_ACCOUNT_HOLDER;
	    $shopper_fields['bank_account_nr'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_ACCOUNT_NR;
	    $shopper_fields['bank_sort_code'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_SORT_CODE;
	    $shopper_fields['bank_name'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_NAME;
	    $shopper_fields['bank_account_type'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE;
	    $shopper_fields['bank_iban'] = $VM_LANG->_PHPSHOP_ACCOUNT_LBL_BANK_IBAN;
    $shopper_fields[uniqid('fieldset_end')] = "";
} 


if (!empty($missing))
    echo "<script type=\"text/javascript\"> alert('"._CONTACT_FORM_NC."'); </script>\n";

$q =  "SELECT * FROM #__users, #__{vm}_user_info 
		WHERE user_id='" . $auth["user_id"] . "' 
		AND user_id = id
		AND address_type='BT' ";
$db->query($q);
$db->next_record();

require_once( CLASSPATH.'ps_product_category.php');
$pathway = "<a href=\"".$sess->url( SECUREURL ."index.php?page=account.index")."\" title=\"".$VM_LANG->_PHPSHOP_ACCOUNT_TITLE."\">"
      .$VM_LANG->_PHPSHOP_ACCOUNT_TITLE."</a> ".ps_product_category::pathway_separator().' '
      .$VM_LANG->_PHPSHOP_USER_FORM_BILLTO_LBL;
$mainframe->appendPathWay( $pathway );
echo "<div>$pathway</div><br/>";

// Form validation function
vmCommonHTML::printJS_formvalidation( $required_fields, 'adminForm', 'submitshopperform' );
?>

<!-- BillTo form -->
<form action="<?php echo $mm_action_url."index.php" ?>" method="post" name="adminForm">
  
<div style="float:left;width:90%;text-align:right;"> 
    <span>
    	<input type="image" src="images/save_f2.png" name="submit" alt="<?php echo _E_SAVE ?>"  onclick="return( submitshopperform());" />
    </span>
    <span style="margin-left:10px;">
    	<a href="<?php $sess->purl( SECUREURL."index.php?page=account.index") ?>">
    		<img src="images/back_f2.png" alt="<?php echo _BACK ?>" border="0" />
    	</a>
    </span>
</div>
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
      	
   		switch( $fieldname ) {
   			case 'title':
   				$ps_html->list_user_title($db->sf("title"), "id=\"user_title\"");
   			break;
   			
   			case 'country':
   				if( CAN_SELECT_STATES ) {
   					$onchange = "onchange=\"changeStateList();\"";
   				}
   				else {
   					$onchange = "";
   				}
   				$ps_html->list_country("country", $db->sf("country"), "id=\"country_field\" $onchange");
   			break;
   			
   			case 'state':
   			  echo $ps_html->dynamic_state_lists( "country", "state", $db->sf('country'), $db->sf('state') );
		      echo "<noscript>\n";
		      $ps_html->list_states("state", $db->sf('state'), "", "id=\"state\"");
		      echo "</noscript>\n";
   			break;
   			
   			case 'password':
			case 'password2':
				echo '<input type="password" id="'.$fieldname.'_field" name="'.$fieldname.'" size="40" class="inputbox" />'."\n";
	   			break;
			    
			case 'bank_account_type':
				echo '<select class="inputbox" name="bank_account_type">
			            <option '. $selected[0] .' value="Checking">'. $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_CHECKING .'</option>
			            <option '. $selected[1] .' value="Business Checking">'. $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_BUSINESSCHECKING .'</option>
			            <option '. $selected[2] .' value="Savings">'. $VM_LANG->_PHPSHOP_ACCOUNT_LBL_ACCOUNT_TYPE_SAVINGS .'</option>
			          </select>';
				break;
			
   			default:
		        echo '<input type="text" id="'.$fieldname.'_field" name="'.$fieldname.'" size="40" value="'. $db->sf($fieldname) .'" class="inputbox" />'."\n";
	   			
	   			break;
   		}
   		
   		echo '</div>
			      <br/><br/>';
   }
   /**
    * @deprecated 
    * thanks to Zdenek for that. Checks for Extra Form Fields
    */
   for( $i=1; $i<6; $i++ ) {
   		$property = "_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_$i";
   		if( $VM_LANG->$property != "" ) { ?>

	      <div style="float:left;width:30%;text-align:right;" >
	        <?php echo "<label for=\"extra_field_".$i."\">".$VM_LANG->$property."</label>" ?>:</div>
	      <div style="float:left;width:60%;"> 
	      <?php
	      	if( $i == 4 || $i == 5) {
	      		eval( "\$ps_html->list_extra_field_$i(\$db->sf(\"extra_field_$i\"), \"id=\\\"extra_field_$i\\\"\");" );
	      	}
	      	else {
	      		echo '<input type="text" id="extra_field_'. $i.'" name="extra_field_'. $i .'" size="40" value="'. $db->sf("extra_field_".$i).'" class="inputbox" />';
	      	}
	      ?>
	      </div>
	    <br/><br/>
    	<?php 
		} 
   }
    ?>

  </div>
    <input type="hidden" name="option" value="<?php echo $option ?>" />
  <input type="hidden" name="page" value="<?php echo $next_page; ?>" />
  <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
  <input type="hidden" name="func" value="shopperupdate" />
  <input type="hidden" name="user_info_id" value="<?php $db->p("user_info_id"); ?>" />
  <input type="hidden" name="id" value="<?php echo $auth["user_id"] ?>" />
  <input type="hidden" name="user_id" value="<?php echo $auth["user_id"] ?>" />
  <input type="hidden" name="address_type" value="BT" />
</form>
