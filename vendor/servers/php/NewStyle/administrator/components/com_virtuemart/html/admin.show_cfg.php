<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: admin.show_cfg.php,v 1.12.2.6 2006/04/27 19:35:52 soeren_nb Exp $
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
global $acl, $VM_BROWSE_ORDERBY_FIELDS;
if( !isset( $VM_BROWSE_ORDERBY_FIELDS )) {
        $VM_BROWSE_ORDERBY_FIELDS = array();
}
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

// Compose the Access DropDown List, for the first time used for setting Price Acess
$fieldname = 'group_id';
if( $_VERSION->PRODUCT == 'Joomla!' && $_VERSION->RELEASE >= 1.1 ) {
        $fieldname = 'id';
}
$db->query( 'SELECT `'.$fieldname.'` FROM #__core_acl_aro_groups WHERE name=\''.VM_PRICE_ACCESS_LEVEL.'\'' );
$db->next_record();
$gtree = ps_perm::getGroupChildrenTree( null, 'USERS', false );
$access_group_list = mosHTML::selectList( $gtree, 'conf_VM_PRICE_ACCESS_LEVEL', 'size="4"', 'value', 'text', $db->f($fieldname) );
                
$title = '&nbsp;&nbsp;&nbsp;<img src="'. IMAGEURL .'ps_image/settings.png" width="32" height="32" border="0" />';
$title .= $VM_LANG->_PHPSHOP_CONFIG;

//First create the object and let it print a form heading
$formObj = &new formFactory( $title );
//Then Start the form
$formObj->startForm();

$ps_html->writableIndicator( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/virtuemart.cfg.php' ); 

$spacer = '&nbsp;&nbsp;&nbsp;';
$tabs = new mShopTabs(0, 1, "_main");
$tabs->startPane("content-pane");
$tabs->startTab( $spacer . $VM_LANG->_PHPSHOP_ADMIN_CFG_GLOBAL . $spacer, "global-page");

?>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_GLOBAL ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell">
				<label for="conf_PSHOP_IS_OFFLINE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOP_OFFLINE ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_PSHOP_IS_OFFLINE" name="conf_PSHOP_IS_OFFLINE" class="inputbox" <?php if (PSHOP_IS_OFFLINE == 1) echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOP_OFFLINE_TIP ?></td>
		</tr>  
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOP_OFFLINE_MSG ?>:</td>
			<td colspan="2">
				<textarea rows="3" cols="70" name="conf_PSHOP_OFFLINE_MESSAGE"><?php echo stripslashes(PSHOP_OFFLINE_MESSAGE); ?></textarea>
			</td>
		</tr>  
		<tr>
			<td class="labelcell">
				<label for="conf_USE_AS_CATALOGUE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_USE_ONLY_AS_CATALOGUE ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_USE_AS_CATALOGUE" name="conf_USE_AS_CATALOGUE" class="inputbox" <?php if (USE_AS_CATALOGUE == 1) echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_USE_ONLY_AS_CATALOGUE_EXPLAIN ?>
			</td>
		</tr>
	</table>
</fieldset>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICE_CONFIGURATION ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell">
				<label for="conf__SHOW_PRICES"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOW_PRICES ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf__SHOW_PRICES" name="conf__SHOW_PRICES" class="inputbox" <?php if (_SHOW_PRICES == 1) echo "checked=\"checked\""; ?> value="1" />
			</td> 
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOW_PRICES_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICE_ACCESS_LEVEL ?></td>
			<td><?php
				echo '<input type="checkbox" value="Y" name="use_price_access" onclick="document.adminForm.conf_VM_PRICE_ACCESS_LEVEL.disabled = document.adminForm.conf_VM_PRICE_ACCESS_LEVEL.disabled ? false : true;" id="use_price_access"';
				if( VM_PRICE_ACCESS_LEVEL != '0' ) { echo ' checked="checked"'; }
				echo ' />';
				echo '<label for="use_price_access"><strong>Enable this feature</strong></label><br />';
				echo $access_group_list;
				?>
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICE_ACCESS_LEVEL_TIP ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_VM_PRICE_SHOW_INCLUDINGTAX"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICE_SHOW_INCLUDINGTAX ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_VM_PRICE_SHOW_INCLUDINGTAX" name="conf_VM_PRICE_SHOW_INCLUDINGTAX" class="inputbox" <?php if (VM_PRICE_SHOW_INCLUDINGTAX == 1) echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICE_SHOW_INCLUDINGTAX_TIP ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_VM_PRICE_SHOW_PACKAGING_PRICELABEL"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICE_SHOW_PACKAGING_PRICELABEL ?>
				</label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_VM_PRICE_SHOW_PACKAGING_PRICELABEL" name="conf_VM_PRICE_SHOW_PACKAGING_PRICELABEL" class="inputbox" <?php if (VM_PRICE_SHOW_PACKAGING_PRICELABEL == 1) echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_ADMIN_CFG_PRICE_SHOW_PACKAGING_PRICELABEL_TIP ) ?>
			</td>
		</tr>
	</table>
</fieldset>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_USER_REGISTRATION_SETTINGS ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell">
				<label for="conf_VM_SILENT_REGISTRATION"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SILENT_REGISTRATION ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_VM_SILENT_REGISTRATION" name="conf_VM_SILENT_REGISTRATION" class="inputbox" <?php if (@VM_SILENT_REGISTRATION == "1") echo "checked=\"checked\""; ?> value="1" />
			</td> 
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SILENT_REGISTRATION_TIP ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell" colspan="2"><?php
				echo $_VERSION->PRODUCT.': ' .  $VM_LANG->_PHPSHOP_ADMIN_CFG_ALLOW_REGISTRATION;
			?></td>
			<td><?php
			if( $mosConfig_allowUserRegistration == '1' ) {
				echo '<span style="color:green;">'.$VM_LANG->_PHPSHOP_ADMIN_CFG_YES.'</span>';
			}
			else {
				echo '<span style="color:red;font-weight:bold;">'.$VM_LANG->_PHPSHOP_ADMIN_CFG_NO.'</span>';
			}
			?></td>
		</tr>
		<tr>
			<td class="labelcell" colspan="2"><?php
				echo $_VERSION->PRODUCT.': ' .  $VM_LANG->_PHPSHOP_ADMIN_CFG_ACCOUNT_ACTIVATION;
			?></td>
			<td><?php
			if( $mosConfig_useractivation == '0' ) {
				echo '<span style="color:green;">'.$VM_LANG->_PHPSHOP_ADMIN_CFG_NO.'</span>';
			}
			else {
				echo '<span style="color:red;font-weight:bold;">'.$VM_LANG->_PHPSHOP_ADMIN_CFG_YES.'</span>';
			}
			?></td>
		</tr>
		
	</table>
</fieldset>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_TAX_CONFIGURATION ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell">
				<label for="conf_TAX_VIRTUAL"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_VIRTUAL_TAX ?></label>
				
			</td>
			<td align="left">
				<input type="checkbox" name="conf_TAX_VIRTUAL" id="conf_TAX_VIRTUAL" class="inputbox" <?php if (TAX_VIRTUAL == 1) echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_VIRTUAL_TAX_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_TAX_MODE ?></td>
			<td>
				<select name="conf_TAX_MODE" class="inputbox">
					<option value="0" <?php if (TAX_MODE == 0) echo "selected"; ?>>
					<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_TAX_MODE_SHIP ?>
					</option>
					<option value="1" <?php if (TAX_MODE == 1) echo "selected"; ?>>
					<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_TAX_MODE_VENDOR ?>
					</option>
				</select>
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_TAX_MODE_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_MULTIPLE_TAXRATES_ENABLE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MULTI_TAX_RATE ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_MULTIPLE_TAXRATES_ENABLE" name="conf_MULTIPLE_TAXRATES_ENABLE" class="inputbox" <?php if (MULTIPLE_TAXRATES_ENABLE == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MULTI_TAX_RATE_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_PAYMENT_DISCOUNT_BEFORE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SUBSTRACT_PAYEMENT_BEFORE ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_PAYMENT_DISCOUNT_BEFORE" name="conf_PAYMENT_DISCOUNT_BEFORE" class="inputbox" <?php if (PAYMENT_DISCOUNT_BEFORE == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SUBSTRACT_PAYEMENT_BEFORE_EXPLAIN ?>
			</td>
		</tr>
	</table>
</fieldset>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_FRONTEND_FEATURES ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell">
				<label for="conf_PSHOP_COUPONS_ENABLE"><?php echo $VM_LANG->_PHPSHOP_COUPONS_ENABLE ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_PSHOP_COUPONS_ENABLE" name="conf_PSHOP_COUPONS_ENABLE" class="inputbox" <?php if (PSHOP_COUPONS_ENABLE == '1') echo "checked='checked'"; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_COUPONS_ENABLE_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_PSHOP_ALLOW_REVIEWS"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_REVIEW ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_PSHOP_ALLOW_REVIEWS" name="conf_PSHOP_ALLOW_REVIEWS" class="inputbox" <?php if (PSHOP_ALLOW_REVIEWS == '1') echo "checked='checked'"; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_REVIEW_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_LEAVE_BANK_DATA"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ACCOUNT_CAN_BE_BLANK ?>
				</label>
				
			</td>
			<td>
				<input type="checkbox" name="conf_LEAVE_BANK_DATA" id="conf_LEAVE_BANK_DATA" class="inputbox" <?php if (LEAVE_BANK_DATA == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ACCOUNT_CAN_BE_BLANK_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_CAN_SELECT_STATES"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CAN_SELECT_STATE ?>
				</label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_CAN_SELECT_STATES" name="conf_CAN_SELECT_STATES" class="inputbox" <?php if (CAN_SELECT_STATES == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CAN_SELECT_STATE_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_MUST_AGREE_TO_TOS"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AGREE_TERMS ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_MUST_AGREE_TO_TOS" name="conf_MUST_AGREE_TO_TOS" class="inputbox" <?php if (MUST_AGREE_TO_TOS == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AGREE_TERMS_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_PSHOP_AGREE_TO_TOS_ONORDER"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AGREE_TERMS_ONORDER ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_PSHOP_AGREE_TO_TOS_ONORDER" name="conf_PSHOP_AGREE_TO_TOS_ONORDER" class="inputbox" <?php if (PSHOP_AGREE_TO_TOS_ONORDER == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_AGREE_TERMS_ONORDER_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_VM_ONCHECKOUT_SHOW_LEGALINFO"><?php echo $VM_LANG->_VM_ADMIN_ONCHECKOUT_SHOW_LEGALINFO ?></label>
			</td>
			<td>
				<input type="checkbox" id="conf_VM_ONCHECKOUT_SHOW_LEGALINFO" name="conf_VM_ONCHECKOUT_SHOW_LEGALINFO" class="inputbox" <?php if (@VM_ONCHECKOUT_SHOW_LEGALINFO == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo mm_ToolTip( $VM_LANG->_VM_ADMIN_ONCHECKOUT_SHOW_LEGALINFO_TIP ) ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_VM_ONCHECKOUT_LEGALINFO_SHORTTEXT"><?php echo $VM_LANG->_VM_ADMIN_ONCHECKOUT_LEGALINFO_SHORTTEXT ?></label>
			</td>
			<td>
				<textarea rows="6" cols="40" id="conf_VM_ONCHECKOUT_LEGALINFO_SHORTTEXT" name="conf_VM_ONCHECKOUT_LEGALINFO_SHORTTEXT" class="inputbox"><?php if( @VM_ONCHECKOUT_LEGALINFO_SHORTTEXT=='' || !defined('VM_ONCHECKOUT_LEGALINFO_SHORTTEXT')) {echo $VM_LANG->_VM_LEGALINFO_SHORTTEXT;} else {echo @VM_ONCHECKOUT_LEGALINFO_SHORTTEXT;} ?></textarea>
			</td>
			<td><?php echo mm_ToolTip( $VM_LANG->_VM_ADMIN_ONCHECKOUT_LEGALINFO_SHORTTEXT_TIP ) ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_VM_ONCHECKOUT_LEGALINFO_LINK"><?php echo $VM_LANG->_VM_ADMIN_ONCHECKOUT_LEGALINFO_LINK ?></label>
			</td>
			<td>
			<?php
				$database->setQuery( "SELECT id AS value, CONCAT( title, ' (', title_alias, ')' ) AS text FROM #__content ORDER BY id" );
				$content = $database->loadObjectList( );
				$select =  "<select size=\"5\" name=\"conf_VM_ONCHECKOUT_LEGALINFO_LINK\" id=\"conf_VM_ONCHECKOUT_LEGALINFO_LINK\" class=\"inputbox\">\n"; 
				foreach($content as $objElement) { 
					$selected = @VM_ONCHECKOUT_LEGALINFO_LINK == $objElement->value ? 'selected="selected"' : '';
					$select .= "<option value=\"{$objElement->value}\" $selected>{$objElement->text}</option>\n"; 
				} 
				$select .=  "</select>\n"; 
				echo $select;
			?>
			</td>
			<td><?php echo mm_ToolTip( $VM_LANG->_VM_ADMIN_ONCHECKOUT_LEGALINFO_LINK_TIP ) ?>
			</td>
		</tr>
	</table>
</fieldset>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CORE_SETTINGS ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell">
				<label for="conf_CHECK_STOCK"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECK_STOCK ?></label>
				
				<div style="visibility:hidden;" id="cs1"><br/><br/>
					<strong>
						<label for="conf_PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS">
							<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOW_OUT_OF_STOCK_PRODUCTS ?>
						</label>
					</strong>
				</div>
			</td>
			<td valign="top">
				<input onchange="if(this.checked) { document.getElementById('cs1').style.visibility='visible';document.getElementById('cs2').style.visibility='visible';document.getElementById('cs3').style.visibility='visible';} else {document.getElementById('cs1').style.visibility='hidden';document.getElementById('cs2').style.visibility='hidden';document.getElementById('cs3').style.visibility='hidden';}" type="checkbox" name="conf_CHECK_STOCK" id="conf_CHECK_STOCK" class="inputbox" <?php if (CHECK_STOCK == '1') echo "checked=\"checked\""; ?> value="1" />
				<div style="visibility:hidden;" id="cs2"><br/><br/><input type="checkbox" name="conf_PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS" id="conf_PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS" class="inputbox" <?php if (PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS == '1') echo "checked=\"checked\""; ?> value="1" /></div>
			</td>
			<td valign="top"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECK_STOCK_EXPLAIN ?>
			<div style="visibility:hidden;" align="left" id="cs3">
			<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOW_OUT_OF_STOCK_PRODUCTS_EXPLAIN ?>
			</div>
			</td>
		</tr>
		<tr>
			<td class="labelcell">
				<label for="conf_AFFILIATE_ENABLE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_AFFILIATE ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_AFFILIATE_ENABLE" name="conf_AFFILIATE_ENABLE" class="inputbox" <?php if (AFFILIATE_ENABLE == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_AFFILIATE_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MAIL_FORMAT ?></td>
			<td>
				<select name="conf_ORDER_MAIL_HTML" class="inputbox">
				<option value="0" <?php if (ORDER_MAIL_HTML == '0') echo "selected"; ?>>
			   <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MAIL_FORMAT_TEXT ?>
				</option>
				<option value="1" <?php if (ORDER_MAIL_HTML == '1') echo "selected"; ?>>
				<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MAIL_FORMAT_HTML ?>
				</option>
				</select>
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MAIL_FORMAT_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_ENCODEKEY ?>&nbsp;&nbsp;</td>
			<td>
				<input type="text" name="conf_ENCODE_KEY" class="inputbox" value="<?php echo ENCODE_KEY ?>" />
			</td>
			<td><?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_ENCODEKEY_EXPLAIN ); ?></td>
		</tr>
	<?php
	  if (stristr($my->usertype, "admin")) { ?>
		  <tr>
			<td class="labelcell">
				<label for="conf_PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_FRONTENDAMDIN ?></label>
				
			</td>
			<td>
				<input type="checkbox" id="conf_PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS" name="conf_PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS" class="inputbox" <?php if (PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS == '1') echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_FRONTENDAMDIN_EXPLAIN ?>
			</td>
		</tr>
	<?php
	  }
	  else
		echo '<input type="hidden" name="conf_PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS" value="'.PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS.'" />';
?>
	</table>
</fieldset>
<?php

$tabs->endTab();
$tabs->startTab( $spacer . $VM_LANG->_PHPSHOP_ADMIN_CFG_PATHANDURL . $spacer, "pathandurl-page");
?>

<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_URLSECURE ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_URLSECURE ?></td>
			<td>
				<input size="40" type="text" name="conf_SECUREURL" class="inputbox" value="<?php echo SECUREURL ?>" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_URLSECURE_EXPLAIN ?>
			</td>
		</tr>
	</table>
</fieldset>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MORE_CORE_SETTINGS ?></legend>
	<table class="adminform">
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_TABLEPREFIX ?></td>
			<td>
				<input size="40" type="text" name="conf_VM_TABLEPREFIX" class="inputbox" value="<?php echo VM_TABLEPREFIX ?>" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_TABLEPREFIX_TIP ?>
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr />&nbsp;</td>
		</tr>
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_HOMEPAGE ?></td>
			<td>
				<input type="text" name="conf_HOMEPAGE" class="inputbox" value="<?php echo HOMEPAGE ?>" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_HOMEPAGE_EXPLAIN ?>
			</td>
		</tr>
		<tr>
			<td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ERRORPAGE ?></td>
			<td>
				<input type="text" name="conf_ERRORPAGE" class="inputbox" value="<?php echo ERRORPAGE ?>" />
			</td>
			<td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ERRORPAGE_EXPLAIN ?>
			</td>
		</tr>
	</table>
</fieldset>
<br/>
<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DEBUG ?></legend>
	<table class="adminform">
		<tr>
			<td>
				<input type="checkbox" id="conf_DEBUG" name="conf_DEBUG" class="inputbox" <?php if (DEBUG == 1) echo "checked=\"checked\""; ?> value="1" />
			</td>
			<td><label for="conf_DEBUG"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DEBUG_EXPLAIN ?></label>
			</td>
		</tr>
	</table>
</fieldset>
<?php
  $tabs->endTab();
  $tabs->startTab( $spacer . $VM_LANG->_PHPSHOP_ADMIN_CFG_SITE . $spacer, "site-page");
    $subtabs2 = new mShopTabs(0, 0, "_layout");
    $subtabs2->startPane("layout-pane");
    $subtabs2->startTab( "Display", "layout-1-page");
?>

<table class="adminform">
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PDF_BUTTON ?></td>
        <td>
        <input type="checkbox" name="conf_PSHOP_PDF_BUTTON_ENABLE" class="inputbox" <?php if (PSHOP_PDF_BUTTON_ENABLE == 1) echo "checked=\"checked\""; ?> value="1" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PDF_BUTTON_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_FLYPAGE ?></td>
        <td>
            <input type="text" name="conf_FLYPAGE" class="inputbox" value="<?php echo FLYPAGE ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_FLYPAGE_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CATEGORY_TEMPLATE ?></td>
        <td>
            <input type="text" name="conf_CATEGORY_TEMPLATE" class="inputbox" value="<?php echo CATEGORY_TEMPLATE ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CATEGORY_TEMPLATE_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NAV_AT_TOP ?></td>
        <td>
            <input type="checkbox" name="conf_PSHOP_SHOW_TOP_PAGENAV" class="inputbox" <?php if (PSHOP_SHOW_TOP_PAGENAV == '1') echo "checked=\"checked\""; ?> value="1" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NAV_AT_TOP_TIP ?></td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_VM_BROWSE_ORDERBY_DEFAULT_FIELD_LBL ?></td>
        <td>
                <select class="inputbox" name="conf_VM_BROWSE_ORDERBY_FIELD">
                        <option value="product_name" <?php if (@VM_BROWSE_ORDERBY_FIELD == 'product_name') echo "selected=\"selected\""; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_NAME_TITLE ?></option>
                        <option value="product_price" <?php if (@VM_BROWSE_ORDERBY_FIELD == 'product_price') echo "selected=\"selected\""; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRICE_TITLE ?></option>
                        <option value="product_sku" <?php if (@VM_BROWSE_ORDERBY_FIELD == 'product_sku') echo "selected=\"selected\""; ?>><?php echo $VM_LANG->_PHPSHOP_CART_SKU ?></option>
                        <option value="product_cdate" <?php if (@VM_BROWSE_ORDERBY_FIELD == 'product_cdate') echo "selected=\"selected\""; ?>><?php echo $VM_LANG->_PHPSHOP_LATEST ?></option>
            </select>
        </td>
        <td><?php echo $VM_LANG->_VM_BROWSE_ORDERBY_DEFAULT_FIELD_LBL_TIP ?></td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_VM_BROWSE_ORDERBY_FIELDS_LBL ?></td>
        <td>
                        <input name="conf_VM_BROWSE_ORDERBY_FIELDS[]" type="checkbox" value="product_name" <?php if (in_array( 'product_name', $VM_BROWSE_ORDERBY_FIELDS )) echo "checked=\"checked\""; ?> id="conf_VM_BROWSE_ORDERBY_FIELDS1" />
                        <label for="conf_VM_BROWSE_ORDERBY_FIELDS1"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_NAME_TITLE ?></label><br />
            
                        <input name="conf_VM_BROWSE_ORDERBY_FIELDS[]" type="checkbox" value="product_price" <?php if (in_array( 'product_price', $VM_BROWSE_ORDERBY_FIELDS )) echo "checked=\"checked\""; ?> id="conf_VM_BROWSE_ORDERBY_FIELDS2" />
                        <label for="conf_VM_BROWSE_ORDERBY_FIELDS2"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_PRICE_TITLE ?></label><br />

                        <input name="conf_VM_BROWSE_ORDERBY_FIELDS[]" type="checkbox" value="product_cdate" <?php if (in_array( 'product_cdate', $VM_BROWSE_ORDERBY_FIELDS )) echo "checked=\"checked\""; ?> id="conf_VM_BROWSE_ORDERBY_FIELDS3" />
                        <label for="conf_VM_BROWSE_ORDERBY_FIELDS3"><?php echo $VM_LANG->_PHPSHOP_LATEST ?></label><br />

                        <input name="conf_VM_BROWSE_ORDERBY_FIELDS[]" type="checkbox" value="product_sku" <?php if (in_array( 'product_sku', $VM_BROWSE_ORDERBY_FIELDS )) echo "checked=\"checked\""; ?> id="conf_VM_BROWSE_ORDERBY_FIELDS4" />
                        <label for="conf_VM_BROWSE_ORDERBY_FIELDS4"><?php echo $VM_LANG->_PHPSHOP_CART_SKU ?></label>
                        
        </td>
        <td><?php echo $VM_LANG->_VM_BROWSE_ORDERBY_FIELDS_LBL_TIP ?></td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOW_PRODUCT_COUNT ?></td>
        <td>
            <input type="checkbox" name="conf_PSHOP_SHOW_PRODUCTS_IN_CATEGORY" class="inputbox" <?php if (PSHOP_SHOW_PRODUCTS_IN_CATEGORY == '1') echo "checked=\"checked\""; ?> value="1" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOW_PRODUCT_COUNT_TIP ?></td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRODUCTS_PER_ROW ?></td>
        <td>
            <input type="text" name="conf_PRODUCTS_PER_ROW" size="4" class="inputbox" value="<?php echo PRODUCTS_PER_ROW ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_PRODUCTS_PER_ROW_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NOIMAGEPAGE ?></td>
        <td>
            <input type="text" name="conf_NO_IMAGE" class="inputbox" value="<?php echo NO_IMAGE ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NOIMAGEPAGE_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOWPHPSHOP_VERSION ?></td>
        <td>
            <input type="checkbox" name="conf_SHOWVERSION" class="inputbox" <?php if (SHOWVERSION == 1) echo "checked=\"checked\""; ?> value="1" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHOWPHPSHOP_VERSION_EXPLAIN ?>
        </td>
    </tr>
</table>
<?php
    $subtabs2->endTab();
    $subtabs2->startTab( "Layout", "layout-2-page");
?>
<table class="adminform">
    <tr>
        <td valign="top"><strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ADDTOCART_STYLE ?></strong></td>
        <td valign="middle" colspan="2"><?php
                    $path = IMAGEPATH."ps_image";
            $files = mosReadDirectory( "$path", "add-to-cart_?.", true, true);
            foreach ($files as $file) { 
                $file_info = pathinfo($file);
                $filename = $file_info['basename'];
                $checked = ($filename == PSHOP_ADD_TO_CART_STYLE) ? "checked=\"checked\"" : "";
                echo "<input type=\"radio\" name=\"conf_PSHOP_ADD_TO_CART_STYLE\" value=\"$filename\" $checked />&nbsp;&nbsp;";
                echo "<img align=\"center\" src=\"".IMAGEURL."ps_image/$filename\" border=\"0\" alt=\"$filename\" />";
                echo "&nbsp;&nbsp;($filename)<br />";
            }
        ?></td>
    </tr>
    <tr>
        <td colspan="3"><hr />&nbsp;</td>
    </tr>
    <?php
    if( function_exists('imagecreatefromjpeg')) {
    	?>
    
	    <tr>
	        <td width="30%" valign="top" align="right">
			<strong><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DYNAMIC_THUMBNAIL_RESIZING ?></strong></td>
	        <td width="15%" valign="top">
	            <input type="checkbox" name="conf_PSHOP_IMG_RESIZE_ENABLE" class="inputbox" <?php if (PSHOP_IMG_RESIZE_ENABLE == '1') echo "checked=\"checked\""; ?> value="1" />
	        </td>
	        <td width="55%"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DYNAMIC_THUMBNAIL_RESIZING_TIP ?></td>
	    </tr>
	    <tr>
	        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_THUMBNAIL_WIDTH ?></td>
	        <td>
	            <input type="text" name="conf_PSHOP_IMG_WIDTH" class="inputbox" value="<?php echo PSHOP_IMG_WIDTH ?>" />
	        </td>
	        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_THUMBNAIL_WIDTH_TIP ?></td>
	    </tr>
	    <tr>
	        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_THUMBNAIL_HEIGHT ?></td>
	        <td>
	            <input type="text" name="conf_PSHOP_IMG_HEIGHT" class="inputbox" value="<?php echo PSHOP_IMG_HEIGHT ?>" />
	        </td>
	        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_THUMBNAIL_HEIGHT_TIP ?></td>
	    </tr>
	    <tr>
	        <td colspan="3"><hr />&nbsp;</td>
	    </tr>
	    <?php
    }
    else {
    	echo '<input type="hidden" name="conf_PSHOP_IMG_RESIZE_ENABLE" value="0" />';
    	echo '<input type="hidden" name="conf_PSHOP_IMG_WIDTH" value="'. PSHOP_IMG_WIDTH .'" />';
    	echo '<input type="hidden" name="conf_PSHOP_IMG_HEIGHT" value="'. PSHOP_IMG_HEIGHT .'" />';
    }
    ?>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SEARCHCOLOR1 ?></td>
        <td>
            <input type="text" name="conf_SEARCH_COLOR_1" class="inputbox" value="<?php echo SEARCH_COLOR_1 ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SEARCHCOLOR1_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SEARCHCOLOR2 ?></td>
        <td>
            <input type="text" name="conf_SEARCH_COLOR_2" class="inputbox" value="<?php echo SEARCH_COLOR_2 ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SEARCHCOLOR2_EXPLAIN ?>
        </td>
    </tr>
</table>

<?php
    $subtabs2->endTab();
    $subtabs2->endPane();
  $tabs->endTab();
  
  $tabs->startTab( $spacer . $VM_LANG->_PHPSHOP_ADMIN_CFG_SHIPPING . $spacer, "shipping-page");
?>


<fieldset>
	<legend><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD ?></legend>
	<table class="adminform">
<?php
require_once( CLASSPATH. "ps_shipping_method.php" );
$ps_shipping_method = new ps_shipping_method;
$rows = $ps_shipping_method->method_list();
$i = 0;
foreach( $rows as $row ) { 
    if( $row['filename'] == "standard_shipping.php" ) { ?>
                <tr>
                        <td>
                                <input type="checkbox" id="sh<?php echo $i ?>" name="conf_SHIPPING[]" <?php if (array_search('standard_shipping', $PSHOP_SHIPPING_MODULES) !== false) echo "checked=\"checked\""; ?> value="standard_shipping" />
                        </td>
                        <td><label for="sh<?php echo $i ?>"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_STANDARD ?></label>
                        </td>
                </tr><?php  
                }
		elseif( $row['filename'] == "zone_shipping.php" ) { ?>
		<tr>
                        <td valign="top">
                                <input type="checkbox" id="sh<?php echo $i ?>" name="conf_SHIPPING[]" <?php if (array_search('zone_shipping', $PSHOP_SHIPPING_MODULES) !== false) echo "checked=\"checked\""; ?> value="zone_shipping" />
                        </td>
                        <td><label for="sh<?php echo $i ?>"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_ZONE ?></label>
                        </td>
                </tr><?php  
                }
		elseif( $row['filename'] == "ups.php" ) { ?>
		<tr>
                        <td>
                                <input type="checkbox" id="sh<?php echo $i ?>" name="conf_SHIPPING[]" <?php if (array_search('ups', $PSHOP_SHIPPING_MODULES) !== false) echo "checked=\"checked\""; ?> value="ups" />
                        </td>
                        <td><label for="sh<?php echo $i ?>"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_UPS ?></label>
                        </td>
                </tr><?php  
                }
		elseif( $row['filename'] == "intershipper.php" ) { ?>
		<tr>
                        <td>
                                <input type="checkbox" id="sh<?php echo $i ?>" name="conf_SHIPPING[]" <?php if (array_search('intershipper', $PSHOP_SHIPPING_MODULES) !== false) echo "checked=\"checked\""; ?> value="intershipper" />
                        </td>
                        <td><label for="sh<?php echo $i ?>"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_INTERSHIPPER ?></label>
                        </td>
                </tr><?php  
                }
		elseif( $row['filename'] != "no_shipping.php" ) {
	?><tr>
                <td>
                        <input type="checkbox" id="sh<?php echo $i ?>" name="conf_SHIPPING[]" <?php if (array_search(basename($row['filename'], ".php"), $PSHOP_SHIPPING_MODULES) !== false) echo "checked=\"checked\""; ?> value="<?php echo basename($row['filename'], ".php") ?>" />
                </td>
                <td><label for="sh<?php echo $i ?>"><?php echo $row["description"]; ?></label></td>
                </tr><?php    
                }
                $i++;
	}
	echo "<input type=\"hidden\" name=\"shippingMethodCount\" value=\"".count($rows)."\" />";
		?>
		<tr><td colspan="2"><hr/></td></tr>
		<tr>
                        <td>
                                <input type="checkbox" id="sh<?php echo $i ?>" name="conf_SHIPPING[]" onclick="unCheckAndDisable( this.checked );" <?php if (NO_SHIPPING == '1') echo "checked=\"checked\""; ?> value="no_shipping" />
                        </td>
                        <td><label for="sh<?php echo $i ?>"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_STORE_SHIPPING_METHOD_DISABLE ?></label>
                        </td>
                </tr>
        </table>
</fieldset>
<?php
  $tabs->endTab();
  $tabs->startTab( $spacer . $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECKOUT . $spacer, "checkout-page");
?>

<table class="adminform">
   <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_CHECKOUTBAR ?></td>
        <td>
            <input type="checkbox" name="conf_SHOW_CHECKOUT_BAR" class="inputbox" <?php if (SHOW_CHECKOUT_BAR == 1) echo "checked=\"checked\""; ?> value="1" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_CHECKOUTBAR_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td rowspan="4" valign="top"><div align="right"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECKOUT_PROCESS ?></td>
        <td width="40" valign="top">
            <input type="radio" name="conf_CHECKOUT_STYLE" <?php if (CHECKOUT_STYLE == '1') echo "checked=\"checked\""; ?> value="1" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECKOUT_PROCESS_STANDARD ?>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <input type="radio" name="conf_CHECKOUT_STYLE" <?php if (CHECKOUT_STYLE == '2') echo "checked=\"checked\""; ?> value="2" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECKOUT_PROCESS_2 ?>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <input type="radio" name="conf_CHECKOUT_STYLE" <?php if (CHECKOUT_STYLE == '3') echo "checked=\"checked\""; ?> value="3" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECKOUT_PROCESS_3 ?>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <input type="radio" name="conf_CHECKOUT_STYLE" <?php if (CHECKOUT_STYLE == '4') echo "checked=\"checked\""; ?> value="4" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_CHECKOUT_PROCESS_4 ?>
        </td>
    </tr>
  </table>

<?php
  $tabs->endTab();
  $tabs->startTab( $spacer. $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOADABLEGOODS . $spacer, "download-page");
?>

  <table class="adminform">
  <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_DOWNLOADS ?></td>
        <td>
            <input type="checkbox" name="conf_ENABLE_DOWNLOADS" class="inputbox" <?php if (ENABLE_DOWNLOADS == 1) echo "checked=\"checked\""; ?> value="1" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_DOWNLOADS_EXPLAIN ?>
        </td>
    </tr>
    <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ORDER_ENABLE_DOWNLOADS ?></td>
        <td>
            <select name="conf_ENABLE_DOWNLOAD_STATUS" class="inputbox" >
            <?php
                $db = new ps_DB;
                $q = "SELECT order_status_name,order_status_code FROM #__{vm}_order_status ORDER BY list_order";
                $db->query($q);
                $order_status_code = Array();
                $order_status_name = Array();
                
                while ($db->next_record()) {
                  $order_status_code[] = $db->f("order_status_code");
                  $order_status_name[] =  $db->f("order_status_name");
                }
                
                for ($i = 0; $i < sizeof($order_status_code); $i++) {
                  echo "<option value=\"" . $order_status_code[$i];
                  if (ENABLE_DOWNLOAD_STATUS == $order_status_code[$i]) 
                     echo "\" selected=\"selected\">";
                  else
                     echo "\">";
                  echo $order_status_name[$i] . "</option>\n";
                }?>
                </select>
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ORDER_ENABLE_DOWNLOADS_EXPLAIN ?>
        </td>
    </tr>
        <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ORDER_DISABLE_DOWNLOADS ?></td>
        <td>
            <select name="conf_DISABLE_DOWNLOAD_STATUS" class="inputbox" >
            <?php
                for ($i = 0; $i < sizeof($order_status_code); $i++) {
                  echo "<option value=\"" . $order_status_code[$i];
                  if (DISABLE_DOWNLOAD_STATUS == $order_status_code[$i]) 
                     echo "\" selected=\"selected\">";
                  else
                     echo "\">";
                  echo $order_status_name[$i] . "</option>\n";
                }?>
                </select>
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ORDER_DISABLE_DOWNLOADS_EXPLAIN ?>
        </td>
    </tr>
      <tr>
        <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOADROOT ?></td>
        <td valign="top">
            <input size="40" type="text" name="conf_DOWNLOADROOT" class="inputbox" value="<?php echo DOWNLOADROOT ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOADROOT_EXPLAIN ?>
        </td>
    </tr>
    <tr>
      <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOAD_MAX ?></td>
        <td>
            <input size="3" type="text" name="conf_DOWNLOAD_MAX" class="inputbox" value="<?php echo DOWNLOAD_MAX ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOAD_MAX_EXPLAIN ?>
        </td>
    </tr>
    <tr>
      <td class="labelcell"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOAD_EXPIRE ?></td>
        <td>
            <input size="8" type="text" name="conf_DOWNLOAD_EXPIRE" class="inputbox" value="<?php echo DOWNLOAD_EXPIRE ?>" />
        </td>
        <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_DOWNLOAD_EXPIRE_EXPLAIN ?>
        </td>
    </tr>
  </table>  

<?php
  $tabs->endTab();
  $tabs->endPane();
  
// Add necessary hidden fields
$formObj->hiddenField( 'conf_SEARCH_ROWS', $mosConfig_list_limit );
$formObj->hiddenField( 'myname', 'Jabba Binks' );

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( 'writeConfig', $modulename.'.index', $option );
?>   

<script type="text/javascript">
function unCheckAndDisable( disable ) {

    var n = document.adminForm.shippingMethodCount.value;
    var fldName = 'sh';
	var f = document.adminForm;
	var n2 = 0;
    if( disable )
        for (i=0; i < n; i++) {
            cb = eval( 'f.' + fldName + '' + i );
            if (cb) {
                cb.disabled = true;
                n2++;
            }
        }
    else
        for (i=0; i < n; i++) {
            cb = eval( 'f.' + fldName + '' + i );
            if (cb) {
                cb.disabled = false;
                n2++;
            }
        }
}
function submitbutton(pressbutton) {
    var form = document.adminForm;
    
    /* Shipping Configuration */
    var correct = false;
    var n = document.adminForm.shippingMethodCount.value;
    var fldName = 'sh';
	var f = document.adminForm;
	var n2 = 0;
    for (i=0; i <= n; i++) {
        cb = eval( 'f.' + fldName + '' + i );
        if (cb) {
            if(cb.checked)
                correct = true;
        }
    }
    if(!correct)
        alert('<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_SHIPPING_NO_SELECTION ?>');

    else
        submitform( pressbutton );
}
var count = document.adminForm.shippingMethodCount.value;
var elem = eval( 'document.adminForm.sh' + count );
unCheckAndDisable( elem.checked );
if(document.adminForm.conf_CHECK_STOCK.checked) { document.getElementById('cs1').style.visibility='visible';document.getElementById('cs2').style.visibility='visible';document.getElementById('cs3').style.visibility='visible';} else {document.getElementById('cs1').style.visibility='hidden';document.getElementById('cs2').style.visibility='hidden';document.getElementById('cs3').style.visibility='hidden';}
<?php
if( VM_PRICE_ACCESS_LEVEL == '0' ) { ?>
document.adminForm.conf_VM_PRICE_ACCESS_LEVEL.disabled = true;
<?php
} ?>
</script>
