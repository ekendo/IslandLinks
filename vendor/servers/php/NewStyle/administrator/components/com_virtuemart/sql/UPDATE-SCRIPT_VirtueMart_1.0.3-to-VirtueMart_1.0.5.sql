#############################################
# SQL update script for upgrading 
# from VirtueMart Version <= 1.0.3 to VirtueMart 1.0.5
#
#############################################

# Allow Shopper group discounts up to 100.00%
ALTER TABLE `jos_vm_shopper_group` CHANGE `shopper_group_discount` `shopper_group_discount` DECIMAL( 5, 2 ) NOT NULL DEFAULT '0.00';
# Allow bigger discounts than 999.99
ALTER TABLE `jos_vm_product_discount` CHANGE `amount` `amount` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';
# Allow prices up to 9 999 999 999.99
ALTER TABLE `jos_vm_product_price` CHANGE `product_price` `product_price` DECIMAL( 12, 5 ) NULL DEFAULT NULL ;
# Adjust order item price
ALTER TABLE `jos_vm_order_item` CHANGE `product_item_price` `product_item_price` DECIMAL( 15, 5 ) NULL DEFAULT NULL ;
# Adjust order item final price
ALTER TABLE `jos_vm_order_item` CHANGE `product_final_price` `product_final_price` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';
# Adjust order total, allowing totals up to 9 999 999 999 999.99
ALTER TABLE `jos_vm_orders` CHANGE `order_total` `order_total` DECIMAL( 15, 5 ) NULL DEFAULT NULL ;
ALTER TABLE `jos_vm_orders` CHANGE `order_subtotal` `order_subtotal` DECIMAL( 15, 5 ) NULL DEFAULT NULL ;

# Allow larger coupon amounts
ALTER TABLE `jos_vm_orders` CHANGE `coupon_discount` `coupon_discount` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';
ALTER TABLE `jos_vm_coupons` CHANGE `coupon_value` `coupon_value` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';

# Allow larger payment discounts
ALTER TABLE `jos_vm_orders` CHANGE `order_discount` `order_discount` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';
ALTER TABLE `jos_vm_payment_method` CHANGE `payment_method_discount` `payment_method_discount` DECIMAL( 12, 2 ) NULL DEFAULT NULL ;

UPDATE `jos_components` SET `params` = 'RELEASE=1.0.5\nDEV_STATUS=stable' WHERE `name` = 'virtuemart_version';