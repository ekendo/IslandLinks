#############################################
# SQL update script for upgrading 
# from VirtueMart 1.0.x to VirtueMart 1.0.3
#
#############################################

ALTER TABLE `jos_vm_product_mf_xref` CHANGE `product_id` `product_id` INT( 11 ) NULL DEFAULT NULL;

ALTER TABLE `jos_vm_orders` ADD `order_tax_details` TEXT NOT NULL AFTER `order_tax`;

UPDATE `jos_components` SET `params` = 'RELEASE=1.0.4\nDEV_STATUS=stable' WHERE `name` = 'virtuemart_version';