# SQL update script for upgrading 
# from phpshop package 1.0(.1) beta to 1.0.2a beta
# 

INSERT INTO `mos_pshop_function` VALUES ('', '12837', 'shipDelete', 'ps_intershipper', 'delete', 'Deletes a shipping method from your DB.', 'admin, storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', '12837', 'shipAdd', 'ps_intershipper', 'add', 'Adds a shipping method to your DB.', 'admin,storeadmin');

ALTER TABLE `mos_pshop_category_xref` CHANGE `category_parent_id` `category_parent_id` VARCHAR( 32 ) DEFAULT '0';
ALTER TABLE `mos_pshop_product` CHANGE `product_parent_id` `product_parent_id` INT( 11 ) DEFAULT '0' ;

ALTER TABLE `mos_users` DROP `perms`;
ALTER TABLE `mos_pshop_user_info` ADD `perms` VARCHAR(40) DEFAULT 'shopper' NOT NULL;