# SQL update script for upgrading 
# from phpshop package 1.2 pre-RC1  to 1.2 RC1
# 
ALTER TABLE `mos_pshop_product` ADD `custom_attribute` TEXT NOT NULL;
INSERT INTO `mos_pshop_function` VALUES ('', 8, 'shippingmethodSave', 'ps_shipping_method', 'save', '', 'admin,storeadmin');
