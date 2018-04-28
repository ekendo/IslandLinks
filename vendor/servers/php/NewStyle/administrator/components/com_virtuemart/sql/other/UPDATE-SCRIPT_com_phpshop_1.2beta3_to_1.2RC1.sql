# SQL update script for upgrading 
# from phpshop package 1.2 beta3  to 1.2 RC1
# 

ALTER TABLE `mos_pshop_shopper_group` ADD `shopper_group_discount` DECIMAL( 3,2 ) DEFAULT '0' NOT NULL AFTER `shopper_group_desc` ;

INSERT INTO `mos_pshop_function` VALUES ('', 2, 'reorder', 'ps_product_category', 'reorder', 'Changes the list order of a category.', 'admin,storeadmin');

ALTER TABLE `mos_pshop_orders` CHANGE `ship_method_id` `ship_method_id` VARCHAR( 255 ) DEFAULT NULL;

DROP TABLE IF EXISTS `mos_pshop_isshipping`;

INSERT INTO `mos_pshop_payment_method` VALUES ('', 1, 'NoChex', 'ps_nochex', 5, '0.00', 0, 'NOCHEX', 'P', 0, 'N', '', '<form action="https://www.nochex.com/nochex.dll/checkout" method=post target="_blank"> 
                                                <input type="hidden" name="email" value="<?php echo NOCHEX_EMAIL ?>" />
                                                <input type="hidden" name="amount" value="<?php printf("%.2f", $db->f("order_total"))?>" />
                                                <input type="hidden" name="ordernumber" value="<?php $db->p("order_id") ?>" />
                                                <input type="hidden" name="logo" value="<?php echo $vendor_image_url ?>" />
                                                <input type="hidden" name="returnurl" value="<?php echo SECUREURL ."index.php?option=com_phpshop&amp;page=checkout.result&amp;order_id=".$db->f("order_id") ?>" />
                                                <input type="image" name="submit" SRC="http://www.nochex.com/web/images/paymeanimated.gif"> 
                                                </form>');

CREATE TABLE `mos_pshop_product_discount` (
      `discount_id` int(11) NOT NULL auto_increment,
      `amount` decimal(3,2) NOT NULL default '0.00',
      `is_percent` tinyint(1) NOT NULL default '0',
      `start_date` int(11) NOT NULL default '0',
      `end_date` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`discount_id`)
    ) TYPE=MyISAM;
INSERT INTO `mos_pshop_function` VALUES ('', 2, 'discountAdd', 'ps_product_discount', 'add', 'Adds a discount.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 2, 'discountUpdate', 'ps_product_discount', 'update', 'Updates a discount.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 2, 'discountDelete', 'ps_product_discount', 'delete', 'Deletes a discount.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 8, 'shippingmethodSave', 'ps_shipping_method', 'save', '', 'admin,storeadmin');

ALTER TABLE `mos_pshop_vendor` ADD `vendor_currency_display_style` VARCHAR( 64 ) NOT NULL ;
ALTER TABLE `mos_pshop_product` ADD `custom_attribute` TEXT NOT NULL;
