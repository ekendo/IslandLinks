<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: sql.update.mambo-phpshop-1.2-stable-pl3.to.virtuemart.php,v 1.10.2.1 2005/11/30 20:18:59 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_phpshop/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
global $mosConfig_dbprefix;

// RENAME all mambo-phpShop tables used by this site
$database->setQuery( "SHOW TABLES LIKE '".$mosConfig_dbprefix."pshop_%'" );
$tables = $database->loadObjectList();
foreach( $tables as $pshop_table ) {
	foreach (get_object_vars($pshop_table) as $k => $v) {
		if( substr( $k, 0, 1 ) != '_' ) {			// internal attributes of an object are ignored
			$vm_table = str_replace( '_pshop_', '_vm_', $v );
			$database->setQuery( 'ALTER TABLE `'.$v.'` RENAME `'.$vm_table.'` ;' );
			if( !$database->query() )
				$messages[] = "Failed renaming table $v to $vm_table";
			else
				$messages[] = "Successfully renamed table $v to $vm_table";
		}
	}
}
		
$db->query( 'SELECT file_name FROM `#__{vm}_product_files`' );
$files_to_copy = $db->record;
if( $files_to_copy ) {
	foreach( $files_to_copy as $file ) {
		if( stristr( $file, 'com_phpshop' ) ) {
			$newFile = str_replace( 'com_phpshop', 'com_virtuemart' );
			copy( $file, $newFile );
		}
	}
}
// REPLACE 'com_phpshop' with 'com_virtuemart' for file references
// in the table mos_vm_product_files
$db->query( "UPDATE `#__{vm}_product_files` SET 
file_name = REPLACE (file_name ,'com_phpshop','com_virtuemart'), 
file_url = REPLACE (file_url  ,'com_phpshop','com_virtuemart')" );

$db->query( "ALTER TABLE #__{vm}_order_user_info ADD `bank_account_nr` varchar(32) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_order_user_info ADD `bank_name` varchar(32) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_order_user_info ADD `bank_sort_code` varchar(16) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_order_user_info ADD `bank_iban` varchar(64) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_order_user_info ADD `bank_account_holder` varchar(48) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_order_user_info ADD `bank_account_type` ENUM( 'Checking', 'Business Checking', 'Savings' ) DEFAULT 'Checking' NOT NULL;" ); 

$db->query( "ALTER TABLE #__{vm}_user_info ADD `bank_account_nr` varchar(32) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_user_info ADD `bank_name` varchar(32) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_user_info ADD `bank_sort_code` varchar(16) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_user_info ADD `bank_iban` varchar(64) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_user_info ADD `bank_account_holder` varchar(48) NOT NULL;" );
$db->query( "ALTER TABLE #__{vm}_user_info ADD `bank_account_type` ENUM( 'Checking', 'Business Checking', 'Savings' ) DEFAULT 'Checking' NOT NULL;" ); 

$db->query( 'INSERT INTO `#__{vm}_user_info`
	SELECT `user_info_id`, `id`, `address_type`, `address_type_name`, 
	`company`, `title`, `last_name`, `first_name`, `middle_name`, 
	`phone_1`, `phone_2`, `fax`, `address_1`, `address_2`, `city`, 
	`state`, `country`, `zip`,`email`, `extra_field_1`, `extra_field_2`, 
	`extra_field_3`, `extra_field_4`, `extra_field_5`, UNIX_TIMESTAMP( registerDate ), 
	UNIX_TIMESTAMP( lastvisitDate ), `perms`, `bank_account_nr`, `bank_name`, 
	`bank_sort_code`, `bank_iban`, `bank_account_holder`, `bank_account_type`
	FROM #__users WHERE address_type=\'BT\';' );

$db->query( 'ALTER TABLE `#__users` DROP `user_info_id`;' );
$db->query( 'ALTER TABLE `#__users` DROP `address_type`;' );
$db->query( 'ALTER TABLE `#__users` DROP `address_type_name`;' );
$db->query( 'ALTER TABLE `#__users` DROP `company`;' );
$db->query( 'ALTER TABLE `#__users` DROP `title`;' );
$db->query( 'ALTER TABLE `#__users` DROP `last_name`;' );
$db->query( 'ALTER TABLE `#__users` DROP `first_name`;' );
$db->query( 'ALTER TABLE `#__users` DROP `middle_name`;' );
$db->query( 'ALTER TABLE `#__users` DROP `phone_1`;' );
$db->query( 'ALTER TABLE `#__users` DROP `phone_2`;' );
$db->query( 'ALTER TABLE `#__users` DROP `fax`;' );
$db->query( 'ALTER TABLE `#__users` DROP `address_1`;' );
$db->query( 'ALTER TABLE `#__users` DROP `address_2`;' );
$db->query( 'ALTER TABLE `#__users` DROP `city`;' );
$db->query( 'ALTER TABLE `#__users` DROP `state`;' );
$db->query( 'ALTER TABLE `#__users` DROP `country`;' );
$db->query( 'ALTER TABLE `#__users` DROP `zip`;' );
$db->query( 'ALTER TABLE `#__users` DROP `extra_field_1`;' );
$db->query( 'ALTER TABLE `#__users` DROP `extra_field_2`;' );
$db->query( 'ALTER TABLE `#__users` DROP `extra_field_3`;' );
$db->query( 'ALTER TABLE `#__users` DROP `extra_field_4`;' );
$db->query( 'ALTER TABLE `#__users` DROP `extra_field_5`;' );
$db->query( 'ALTER TABLE `#__users` DROP `perms`;' );
$db->query( 'ALTER TABLE `#__users` DROP `bank_account_nr`;' );
$db->query( 'ALTER TABLE `#__users` DROP `bank_account_type`;' );
$db->query( 'ALTER TABLE `#__users` DROP `bank_name`;' );
$db->query( 'ALTER TABLE `#__users` DROP `bank_sort_code`;' );
$db->query( 'ALTER TABLE `#__users` DROP `bank_iban`;' );
$db->query( 'ALTER TABLE `#__users` DROP `bank_account_holder`;' );

# We don't need these fields!
$db->query( 'ALTER TABLE `#__{vm}_module` DROP `language_code_1` ,
							DROP `language_code_2` ,
							DROP `language_code_3` ,
							DROP `language_code_4` ,
							DROP `language_code_5` ,
							DROP `language_file_1` ,
							DROP `language_file_2` ,
							DROP `language_file_3` ,
							DROP `language_file_4` ,
							DROP `language_file_5` ,
							DROP `module_label_1` ,
							DROP `module_label_2` ,
							DROP `module_label_3` ,
							DROP `module_label_4` ,
							DROP `module_label_5` ,
							DROP `module_header` ,
							DROP `module_footer` ;' );

# 12.08.2005
/** Packaging - Begin */
$db->query( 'ALTER TABLE `#__{vm}_product` ADD `product_unit` varchar(32);' );
$db->query( 'ALTER TABLE `#__{vm}_product` ADD `product_packaging` int(11);' );
/** Packaging - End */

# 23.08.2005
/** Extra fields */
$db->query( 'ALTER TABLE #__{vm}_order_user_info ADD  `extra_field_1` varchar(255) default NULL;' );
$db->query( 'ALTER TABLE #__{vm}_order_user_info ADD  `extra_field_2` varchar(255) default NULL;' );
$db->query( 'ALTER TABLE #__{vm}_order_user_info ADD  `extra_field_3` varchar(255) default NULL;' );
$db->query( 'ALTER TABLE #__{vm}_order_user_info ADD  `extra_field_4` char(1) default NULL;' );
$db->query( 'ALTER TABLE #__{vm}_order_user_info ADD  `extra_field_5` char(1) default NULL;' );

# We don't need another int(11) auto_increment field here
# This allows us to copy the user information from mos_users into #__{vm}_user_info
$db->query( 'ALTER TABLE `#__{vm}_user_info` CHANGE `user_info_id` `user_info_id` VARCHAR( 32 ) NOT NULL;' );

$db->query( 'ALTER TABLE `#__{vm}_user_info` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;' );
$db->query( 'ALTER TABLE `#__{vm}_waiting_list` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;' );
$db->query( 'ALTER TABLE `#__{vm}_shopper_vendor_xref` CHANGE `user_id` `user_id` INT( 11 ) NULL DEFAULT NULL ;' );

$db->query( 'ALTER TABLE `#__{vm}_product_download` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;' );
$db->query( 'ALTER TABLE `#__{vm}_product_download` CHANGE `order_id` `order_id` INT( 11 ) NOT NULL ;' );
$db->query( 'ALTER TABLE `#__{vm}_product_download` CHANGE `end_date` `end_date` INT( 11 ) NOT NULL ;' );
$db->query( 'ALTER TABLE `#__{vm}_product_download` CHANGE `download_max` `download_max` INT( 11 ) NOT NULL ;' );
$db->query( 'ALTER TABLE `#__{vm}_product_download` CHANGE `download_id` `download_id` VARCHAR( 32 ) NOT NULL ;' );

$db->query( 'ALTER TABLE `#__{vm}_order_user_info` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;' );

$db->query( 'ALTER TABLE `#__{vm}_orders` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;' );

$db->query( 'ALTER TABLE `#__{vm}_orders` CHANGE `order_subtotal` `order_subtotal` DECIMAL( 10, 5 ) NULL DEFAULT NULL;' );

$db->query( 'ALTER TABLE `#__{vm}_auth_user_vendor` CHANGE `user_id` `user_id` INT( 11 ) NULL DEFAULT NULL ' );

$db->query( 'ALTER TABLE `#__{vm}_affiliate` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;' );

$db->query( 'ALTER TABLE `#__{vm}_order_item` CHANGE `product_item_price` `product_item_price` DECIMAL( 10, 5 ) NULL DEFAULT NULL ' );

$db->query( "UPDATE `#__{vm}_function` SET `function_name` = 'changePublishState',
`function_class` = 'vmAbstractObject.class',
`function_method` = 'handlePublishState',
`function_description` = 'Changes the publish field of an item, so that it can be published or unpublished easily.' WHERE `function_name` ='productPublish' LIMIT 1 ;");

$db->query("UPDATE `#__{vm}_payment_method` 
			SET `payment_extrainfo` = REPLACE (
				`payment_extrainfo` ,'com_phpshop','com_virtuemart'
			);");

$db->query( 'ALTER TABLE `#__{vm}_product` CHANGE `product_in_stock` `product_in_stock` INT( 11 ) NULL DEFAULT NULL;');

// Unpublish old mambots which could cause VirtueMart not to load
$db->query( 'UPDATE `#__mambots` SET published=0 WHERE element=\'phpshop.searchbot\'');
$db->query( 'UPDATE `#__mambots` SET published=0 WHERE element=\'mosproductsnap\'');

?>