#############################################
# SQL update script for upgrading 
# from mambo-phpshop Version 1.2 stable-pl3 to VirtueMart 1.0
#
#############################################

# 12.08.2005
/** Packaging - Begin */
ALTER TABLE `mos_pshop_product` ADD `product_unit` varchar(32);
ALTER TABLE `mos_pshop_product` ADD `product_packaging` int(11);
/** Packaging - End */

# 23.08.2005
/** Extra fields */
ALTER TABLE mos_pshop_order_user_info ADD  `extra_field_1` varchar(255) default NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `extra_field_2` varchar(255) default NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `extra_field_3` varchar(255) default NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `extra_field_4` char(1) default NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `extra_field_5` char(1) default NULL;

# 01.10.2005
# Moving Customer Information from mos_users to mos_vm_user_info
ALTER TABLE mos_pshop_user_info ADD  `bank_account_nr` varchar(32) NOT NULL;
ALTER TABLE mos_pshop_user_info ADD  `bank_name` varchar(32) NOT NULL;
ALTER TABLE mos_pshop_user_info ADD  `bank_sort_code` varchar(16) NOT NULL;
ALTER TABLE mos_pshop_user_info ADD  `bank_iban` varchar(64) NOT NULL;
ALTER TABLE mos_pshop_user_info ADD  `bank_account_holder` varchar(48) NOT NULL;
ALTER TABLE mos_pshop_user_info ADD  `bank_account_type` ENUM( 'Checking', 'Business Checking', 'Savings' ) DEFAULT 'Checking' NOT NULL;


ALTER TABLE mos_pshop_order_user_info ADD  `bank_account_nr` varchar(32) NOT NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `bank_name` varchar(32) NOT NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `bank_sort_code` varchar(16) NOT NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `bank_iban` varchar(64) NOT NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `bank_account_holder` varchar(48) NOT NULL;
ALTER TABLE mos_pshop_order_user_info ADD  `bank_account_type` ENUM( 'Checking', 'Business Checking', 'Savings' ) DEFAULT 'Checking' NOT NULL;

# We don't need another int(11) auto_increment field here
# This allows us to copy the user information from mos_users into mos_pshop_user_info
ALTER TABLE `mos_pshop_user_info` CHANGE `user_info_id` `user_info_id` VARCHAR( 32 ) NOT NULL;

# All user ids are stored in int(11) fields, so let's unify this
ALTER TABLE `mos_pshop_user_info` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;
ALTER TABLE `mos_pshop_waiting_list` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;
ALTER TABLE `mos_pshop_shopper_vendor_xref` CHANGE `user_id` `user_id` INT( 11 ) NULL DEFAULT NULL ;

ALTER TABLE `mos_pshop_product_download` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;
ALTER TABLE `mos_pshop_product_download` CHANGE `order_id` `order_id` INT( 11 ) NOT NULL ;
ALTER TABLE `mos_pshop_product_download` CHANGE `end_date` `end_date` INT( 11 ) NOT NULL ;
ALTER TABLE `mos_pshop_product_download` CHANGE `download_max` `download_max` INT( 11 ) NOT NULL ;
ALTER TABLE `mos_pshop_product_download` CHANGE `download_id` `download_id` VARCHAR( 32 ) NOT NULL ;

ALTER TABLE `mos_pshop_order_user_info` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;

ALTER TABLE `mos_pshop_orders` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;

ALTER TABLE `mos_pshop_auth_user_vendor` CHANGE `user_id` `user_id` INT( 11 ) NULL DEFAULT NULL ;

ALTER TABLE `mos_pshop_affiliate` CHANGE `user_id` `user_id` INT( 11 ) NOT NULL ;

# We don't need these fields!
ALTER TABLE `mos_pshop_module` DROP `language_code_1` ,
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
DROP `module_footer` ;

# Rename all tables from *pshop* to *vm*
ALTER TABLE `mos_pshop_affiliate` RENAME `mos_vm_affiliate`;
ALTER TABLE `mos_pshop_affiliate_sale` RENAME `mos_vm_affiliate_sale`;
ALTER TABLE `mos_pshop_auth_user_vendor` RENAME `mos_vm_auth_user_vendor`;
ALTER TABLE `mos_pshop_category` RENAME `mos_vm_category`;
ALTER TABLE `mos_pshop_category_xref` RENAME `mos_vm_category_xref`;
ALTER TABLE `mos_pshop_country` RENAME `mos_vm_country`;
ALTER TABLE `mos_pshop_coupons` RENAME `mos_vm_coupons`;
ALTER TABLE `mos_pshop_creditcard` RENAME `mos_vm_creditcard`;
ALTER TABLE `mos_pshop_csv` RENAME `mos_vm_csv`;
ALTER TABLE `mos_pshop_currency` RENAME `mos_vm_currency`;
ALTER TABLE `mos_pshop_function` RENAME `mos_vm_function`;
ALTER TABLE `mos_pshop_manufacturer` RENAME `mos_vm_manufacturer`;
ALTER TABLE `mos_pshop_manufacturer_category` RENAME `mos_vm_manufacturer_category`;
ALTER TABLE `mos_pshop_module` RENAME `mos_vm_module`;
ALTER TABLE `mos_pshop_order_history` RENAME `mos_vm_order_history`;
ALTER TABLE `mos_pshop_order_item` RENAME `mos_vm_order_item`;
ALTER TABLE `mos_pshop_order_payment` RENAME `mos_vm_order_payment`;
ALTER TABLE `mos_pshop_order_status` RENAME `mos_vm_order_status`;
ALTER TABLE `mos_pshop_order_user_info` RENAME `mos_vm_order_user_info`;
ALTER TABLE `mos_pshop_orders` RENAME `mos_vm_orders`;
ALTER TABLE `mos_pshop_payment_method` RENAME `mos_vm_payment_method`;
ALTER TABLE `mos_pshop_product` RENAME `mos_vm_product`;
ALTER TABLE `mos_pshop_product_attribute` RENAME `mos_vm_product_attribute`;
ALTER TABLE `mos_pshop_product_attribute_sku` RENAME `mos_vm_product_attribute_sku`;
ALTER TABLE `mos_pshop_product_category_xref` RENAME `mos_vm_product_category_xref`;
ALTER TABLE `mos_pshop_product_discount` RENAME `mos_vm_product_discount`;
ALTER TABLE `mos_pshop_product_download` RENAME `mos_vm_product_download`;
ALTER TABLE `mos_pshop_product_files` RENAME `mos_vm_product_files`;
ALTER TABLE `mos_pshop_product_mf_xref` RENAME `mos_vm_product_mf_xref`;
ALTER TABLE `mos_pshop_product_price` RENAME `mos_vm_product_price`;
ALTER TABLE `mos_pshop_product_relations` RENAME `mos_vm_product_relations`;
ALTER TABLE `mos_pshop_product_reviews` RENAME `mos_vm_product_reviews`;
ALTER TABLE `mos_pshop_product_type` RENAME `mos_vm_product_type`;
ALTER TABLE `mos_pshop_product_type_parameter` RENAME `mos_vm_product_type_parameter`;
ALTER TABLE `mos_pshop_product_product_type_xref` RENAME `mos_vm_product_product_type_xref`;
ALTER TABLE `mos_pshop_product_votes` RENAME `mos_vm_product_votes`;
ALTER TABLE `mos_pshop_shipping_carrier` RENAME `mos_vm_shipping_carrier`;
ALTER TABLE `mos_pshop_shipping_rate` RENAME `mos_vm_shipping_rate`;
ALTER TABLE `mos_pshop_shopper_group` RENAME `mos_vm_shopper_group`;
ALTER TABLE `mos_pshop_shopper_vendor_xref` RENAME `mos_vm_shopper_vendor_xref`;
ALTER TABLE `mos_pshop_state` RENAME `mos_vm_state`;
ALTER TABLE `mos_pshop_tax_rate` RENAME `mos_vm_tax_rate`;
ALTER TABLE `mos_pshop_user_info` RENAME `mos_vm_user_info`;
ALTER TABLE `mos_pshop_vendor` RENAME `mos_vm_vendor`;
ALTER TABLE `mos_pshop_vendor_category` RENAME `mos_vm_vendor_category`;
ALTER TABLE `mos_pshop_visit` RENAME `mos_vm_visit`;
ALTER TABLE `mos_pshop_waiting_list` RENAME `mos_vm_waiting_list`;
ALTER TABLE `mos_pshop_zone_shipping` RENAME `mos_vm_zone_shipping`;

INSERT INTO `mos_vm_user_info`
	SELECT `user_info_id`, `id`, `address_type`, `address_type_name`, `company`, `title`, `last_name`, `first_name`, `middle_name`, `phone_1`, `phone_2`, `fax`, `address_1`, `address_2`, `city`, `state`, `country`, `zip`, `email`, `extra_field_1`, `extra_field_2`, `extra_field_3`, `extra_field_4`, `extra_field_5`, UNIX_TIMESTAMP( registerDate ), UNIX_TIMESTAMP( lastvisitDate ), `perms`, `bank_account_nr`, `bank_name`, `bank_sort_code`, `bank_iban`, `bank_account_holder`, `bank_account_type`
	FROM mos_users WHERE address_type='BT';
	
ALTER TABLE `mos_users` DROP `user_info_id`;
ALTER TABLE `mos_users` DROP `address_type`;
ALTER TABLE `mos_users` DROP `address_type_name`;
ALTER TABLE `mos_users` DROP `company`;
ALTER TABLE `mos_users` DROP `title`;
ALTER TABLE `mos_users` DROP `last_name`;
ALTER TABLE `mos_users` DROP `first_name`;
ALTER TABLE `mos_users` DROP `middle_name`;
ALTER TABLE `mos_users` DROP `phone_1`;
ALTER TABLE `mos_users` DROP `phone_2`;
ALTER TABLE `mos_users` DROP `fax`;
ALTER TABLE `mos_users` DROP `address_1`;
ALTER TABLE `mos_users` DROP `address_2`;
ALTER TABLE `mos_users` DROP `city`;
ALTER TABLE `mos_users` DROP `state`;
ALTER TABLE `mos_users` DROP `country`;
ALTER TABLE `mos_users` DROP `zip`;
ALTER TABLE `mos_users` DROP `extra_field_1`;
ALTER TABLE `mos_users` DROP `extra_field_2`;
ALTER TABLE `mos_users` DROP `extra_field_3`;
ALTER TABLE `mos_users` DROP `extra_field_4`;
ALTER TABLE `mos_users` DROP `extra_field_5`;
ALTER TABLE `mos_users` DROP `perms`;
ALTER TABLE `mos_users` DROP `bank_account_nr`;
ALTER TABLE `mos_users` DROP `bank_account_type`;
ALTER TABLE `mos_users` DROP `bank_name`;
ALTER TABLE `mos_users` DROP `bank_sort_code`;
ALTER TABLE `mos_users` DROP `bank_iban`;
ALTER TABLE `mos_users` DROP `bank_account_holder`;

ALTER TABLE `mos_vm_order_item` CHANGE `product_item_price` `product_item_price` DECIMAL( 10, 5 ) NULL DEFAULT NULL;

UPDATE `mos_vm_function` SET `function_name` = 'changePublishState',
`function_class` = 'vmAbstractObject.class',
`function_method` = 'handlePublishState',
`function_description` = 'Changes the publish field of an item, so that it can be published or unpublished easily.' WHERE `function_name` ='productPublish' LIMIT 1 ;

UPDATE `mos_vm_payment_method` SET `payment_extrainfo` = REPLACE (
`payment_extrainfo` ,
'com_phpshop',
'com_virtuemart'
);