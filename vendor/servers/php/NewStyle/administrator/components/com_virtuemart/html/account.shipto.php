<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: account.shipto.php,v 1.4.2.2 2006/04/05 18:16:54 soeren_nb Exp $
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

$mainframe->setPageTitle( $VM_LANG->_PHPSHOP_ADD_SHIPTO_1 ." ".$VM_LANG->_PHPSHOP_ADD_SHIPTO_2 );
      
$Itemid = mosGetParam( $_REQUEST, "Itemid", null );
$next_page = mosGetParam( $_REQUEST, "next_page", "account.shipping" );
$user_info_id = mosGetParam( $_REQUEST, "user_info_id", "" );

$vars['country'] = empty( $_REQUEST['country'] ) ? $vendor_country : $_REQUEST['country'];
$missing = mosGetParam( $vars, 'missing' );
$missing_style = "color: Red; font-weight: Bold;";

if (!empty( $missing )) {
    echo "<script type=\"text/javascript\">alert('".html_entity_decode( _CONTACT_FORM_NC )."'); </script>\n";
}
$db = new ps_DB;
if (!empty($user_info_id)) {
  $q =  "SELECT * from #__{vm}_user_info WHERE user_info_id='".$database->getEscaped($user_info_id)."' ";
  $q .=  " AND user_id='".$auth['user_id']."'";
  $q .=  " AND address_type='ST'";
  $db->query($q);
  $db->next_record();
}

/*****************************
** Checkout Bar Feature
**/
 if (SHOW_CHECKOUT_BAR == '1' && $next_page=="checkout.index") {
    
     echo "<h3>". $VM_LANG->_PHPSHOP_CHECKOUT_TITLE ."</h3>";
     
     // This is the file, where the checkout symbols are displayed
    // 1 - 2 - 3 - 4 , you know ;-)
    include_class('checkout');
    include(PAGEPATH. 'checkout_bar.php'); 
    
 }
/**
** End Checkout Bar Feature
*****************************/
echo "<fieldset>
        <legend><span class=\"sectiontableheader\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_SHIPTO_LBL."</span></legend>";
        
echo "<br />".$VM_LANG->_PHPSHOP_SHIPTO_TEXT. "<br /><br /><br />";
?>
<div style="width:90%;">
<!-- Registration form -->
<form action="<?php echo SECUREURL ?>index.php" method="post" name="adminForm">
  <input type="hidden" name="option" value="com_virtuemart" />
  <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" />
  <input type="hidden" name="page" value="<?php echo $next_page ?>" />
  <input type="hidden" name="next_page" value="<?php echo $next_page ?>" />
<?php
   if (!empty($user_info_id)) { ?>
      <input type="hidden" name="func" value="userAddressUpdate" />
      <input type="hidden" name="user_info_id" value="<?php echo $user_info_id ?>" />
<?php 
   }
   else { ?>
      <input type="hidden" name="func" value="userAddressAdd" />
<?php 
    } ?>
  <input type="hidden" name="user_id" value="<?php echo $auth["user_id"] ?>" />
  <input type="hidden" name="address_type" value="ST" />
  

  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'address_type_name')) echo $missing_style ?>">
    <?php echo "<label for=\"address_type_name\">".$VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_LABEL."</label>*" ?>:</div>
  <div style="float:left;width:60%;">
    <input type="text" class="inputbox" id="address_type_name" name="address_type_name" value="<?php $db->sp("address_type_name") ?>" maxlength="64" size="16" />
  </div>
  <br/><br/>
  
  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'first_name')) echo $missing_style ?>">
    <?php echo "<label for=\"first_name\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME."</label>*"  ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="first_name" name="first_name" size="40" value="<?php $db->sp("first_name") ?>" class="inputbox" />
  </div>
<br/><br/>
 
  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'last_name')) echo $missing_style ?>" >
  <?php echo "<label for=\"last_name\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME."</label>*" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="last_name" name="last_name" size="40" value="<?php $db->sp("last_name") ?>" class="inputbox" />
  </div>
<br/><br/>
 
  <div style="float:left;width:30%;text-align:right;">
    <?php echo "<label for=\"middle_name\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_MIDDLE_NAME."</label>"  ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="middle_name" name="middle_name" size="40" value="<?php $db->sp("middle_name") ?>" class="inputbox" />
  </div>
  <br/><br/>
  <div style="float:left;width:30%;text-align:right;" >
    <?php echo "<label for=\"company\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_COMPANY_NAME."</label>" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="company" name="company" size="40" value="<?php $db->sp("company") ?>" class="inputbox" />
  </div>
<br/><br/>
 
  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'address_1')) echo $missing_style ?>">
  <?php echo "<label for=\"address_1\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_1."</label>*" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="address_1" name="address_1" size="40" value="<?php $db->sp("address_1") ?>" class="inputbox" />
  </div>
<br/><br/>
 
  <div style="float:left;width:30%;text-align:right;" >
    <?php echo "<label for=\"address_2\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_2."</label>" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="address_2" name="address_2" size="40" value="<?php $db->sp("address_2") ?>" class="inputbox" />
  </div>
<br/><br/>
 
  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'city')) echo $missing_style ?>">
  <?php echo "<label for=\"city\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY."</label>*" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="city" name="city" size="40" value="<?php $db->sp("city") ?>" class="inputbox" />
  </div>
<br/><br/>
 
  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'zip')) echo $missing_style ?>">
  <?php echo "<label for=\"zip\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP."</label>*" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="zip" name="zip" size="10" value="<?php $db->sp("zip") ?>" class="inputbox" />
  </div>
<br/><br/>
     
      <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'country')) echo $missing_style ?>">
      <?php echo "<label for=\"country_field\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY."</label>*" ?>:</div>
      <div style="float:left;width:60%;">
        <?php $ps_html->list_country("country", $db->sf("country"), "id=\"country_field\" onchange=\"changeStateList();\"") ?>
      </div>
    <br/><br/>
    <?php 
    if (CAN_SELECT_STATES == '1') {
?>
      <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'state')) echo $missing_style ?>">
      <?php echo "<label for=\"state\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE."</label>" ?>:</div>
      <div style="float:left;width:60%;"> 
        <?php 
        
      echo $ps_html->dynamic_state_lists( "country", "state", $db->sf('country'), $db->sf('state') );
      echo "<noscript>\n";
      $ps_html->list_states("state", $db->sf('state'), "", "id=\"state\"");
      echo "</noscript>\n";
      ?>
      </div>
    <br/><br/>
    <?php } ?>
 
  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'phone_1')) echo $missing_style ?>" >
    <?php echo "<label for=\"phone_1\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE."</label>*" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="phone_1" name="phone_1" size="40" value="<?php $db->sp("phone_1") ?>" class="inputbox" />
  </div>
<br/><br/>  
 
  <div style="float:left;width:30%;text-align:right;<?php if (stristr($missing,'phone_2')) echo $missing_style ?>" >
    <?php echo "<label for=\"phone_2\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE2."</label>" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="phone_2" name="phone_2" size="40" value="<?php $db->sp("phone_2") ?>" class="inputbox" />
  </div>
<br/><br/>  
 
  <div style="float:left;width:30%;text-align:right;" >
    <?php echo "<label for=\"fax\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_FAX."</label>" ?>:</div>
  <div style="float:left;width:60%;"> 
    <input type="text" id="fax" name="fax" size="40" value="<?php $db->sp("fax") ?>" class="inputbox" />
  </div>
<br/><br/>

    <!-- If you wish show a EXTRA FIELD only in account billing address (not in this form) add into condition "false && ".
         For example: if( false && $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) -->
    <!-- EXTRA FIELD 1 - BEGIN - You can move this section into any other position of form. -->
    <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) { ?>
      <div style="float:left;width:30%;text-align:right;" >
        <?php echo "<label for=\"extra_field_1\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1."</label>" ?>:</div>
      <div style="float:left;width:60%;"> 
        <input type="text" id="extra_field_1" name="extra_field_1" size="40" value="<?php $db->sp("extra_field_1"); ?>" class="inputbox" />
      </div>
    <br/><br/>
    <?php } ?>
    <!-- EXTRA FIELD 1 - END -->
    
    <!-- EXTRA FIELD 2 - BEGIN - You can move this section into any other position of form. -->
    <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2 != "" ) { ?>
      <div style="float:left;width:30%;text-align:right;" >
        <?php echo "<label for=\"extra_field_2\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2."</label>" ?>:</div>
      <div style="float:left;width:60%;"> 
        <input type="text" id="extra_field_2" name="extra_field_2" size="40" value="<?php $db->sp("extra_field_2"); ?>" class="inputbox" />
      </div>
    <br/><br/>
    <?php } ?>
    <!-- EXTRA FIELD 2 - END -->
    
    <!-- EXTRA FIELD 3- BEGIN - You can move this section into any other position of form. -->
    <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3 != "" ) { ?>
      <div style="float:left;width:30%;text-align:right;" >
        <?php echo "<label for=\"extra_field_3\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3."</label>" ?>:</div>
      <div style="float:left;width:60%;"> 
        <input type="text" id="extra_field_3" name="extra_field_3" size="40" value="<?php $db->sp("extra_field_3"); ?>" class="inputbox" />
      </div>
    <br/><br/>
    <?php } ?>
    <!-- EXTRA FIELD 3 - END -->
    
    <!-- EXTRA FIELD 4 - BEGIN - You can move this section into any other position of form. -->
    <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4 != "" ) { ?>
      <div style="float:left;width:30%;text-align:right;" >
        <?php echo "<label for=\"extra_field_4\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_4."</label>" ?>:</div>
      <div style="float:left;width:60%;"><?php $ps_html->list_extra_field_4($db->sf("extra_field_4"), "id=\"extra_field_4\""); ?></div>
    <br/><br/>
    <?php } ?>
    <!-- EXTRA FIELD 4 - END -->
    
    <!-- EXTRA FIELD 5 BEGIN - You can move this section into any other position of form. -->
    <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5 != "" ) { ?>
      <div style="float:left;width:30%;text-align:right;" >
        <?php echo "<label for=\"extra_field_5\">".$VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_5."</label>" ?>:</div>
      <div style="float:left;width:60%;"><?php $ps_html->list_extra_field_5($db->sf("extra_field_5"), "id=\"extra_field_5\""); ?></div>
    <br/><br/>
    <?php } ?>
    <!-- EXTRA FIELD 5 - END -->
    
  <br/>
  <div style="float:left;width:45%;text-align:right;" >
    <input type="submit" class="button" name="submit" value="<?php echo _E_SAVE ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="<?php $sess->purl( SECUREURL."index.php?page=$next_page") ?>" class="button"><?php echo _BACK ?></a>
  </div>
  </form>
<?php
  if (!empty($user_info_id)) { ?>
    <div style="float:left;width:45%;text-align:center;"> 
      <form action="<?php echo SECUREURL ?>index.php" method="post">
        <input type="hidden" name="option" value="com_virtuemart" />
        <input type="hidden" name="page" value="<?php echo $next_page ?>" />
        <input type="hidden" name="next_page" value="<?php echo $next_page ?>" />
        <input type="hidden" name="func" value="useraddressdelete" />
        <input type="hidden" name="user_info_id" value="<?php echo $user_info_id ?>" />
        <input type="hidden" name="user_id" value="<?php echo $auth["user_id"] ?>" />
        <input type="submit" class="button" name="submit" value="<?php echo _E_REMOVE ?>" />
      </form>
    </div>
<?php 
  } ?>
  </div>
  </fieldset>
