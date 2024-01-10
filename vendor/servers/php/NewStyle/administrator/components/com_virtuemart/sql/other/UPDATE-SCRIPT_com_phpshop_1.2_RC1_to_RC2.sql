# SQL update script for upgrading 
# from phpshop package 1.2 RC1  to 1.2 RC2
# 
CREATE TABLE `mos_pshop_product_files` (
  `file_id` int(19) NOT NULL auto_increment,
  `file_product_id` int(11) NOT NULL default '0',
  `file_name` varchar(128) NOT NULL default '',
  `file_title` varchar(128) NOT NULL default '',
  `file_description` mediumtext NOT NULL,
  `file_extension` varchar(128) NOT NULL default '',
  `file_mimetype` varchar(64) NOT NULL default '',
  `file_url` varchar(254) NOT NULL default '',
  `file_published` tinyint(1) NOT NULL default '0',
  `file_is_image` tinyint(1) NOT NULL default '0',
  `file_image_height` int NOT NULL default '0',
  `file_image_width` int NOT NULL default '0',
  `file_image_thumb_height` int NOT NULL default '50',
  `file_image_thumb_width` int NOT NULL default '0',
  PRIMARY KEY  (`file_id`)
) TYPE=MyISAM;

INSERT INTO `mos_pshop_function` VALUES ('', '7', 'productReviewDelete', 'ps_reviews', 'delete_review', 'This deletes a review and from a product.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 2, 'uploadProductFile', 'ps_product_files', 'add', 'Uploads and Adds a Product Image/File.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 2, 'updateProductFile', 'ps_product_files', 'update', 'Updates a Product Image/File.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 2, 'deleteProductFile', 'ps_product_files', 'delete', 'Deletes a Product Image/File.', 'admin,storeadmin');
INSERT INTO `mos_pshop_payment_method` VALUES ('', 1, 'eWay', '', 5, '0.00', 0, 'EW', 'P', 0, 'N', '', '<?php\r\n//your eWAY customer details\r\n$ewayCustomerID = "87654321";\r\n\r\n//amount in cents, so we multiply by 10\r\n$ewayTotalAmount = intval($db->f("order_total")) * 10;\r\n\r\n//order details\r\n$ewayCustomerFirstName = $user->first_name;\r\n$ewayCustomerLastName = $user->last_name;\r\n$ewayCustomerEmail = $user->email;\r\n$ewayCustomerAddress = $user->address_1;\r\n$ewayCustomerPostcode = $user->zip;\r\n$ewayCustomerInvoiceDescription = $db->f("order_id");\r\n$ewayCustomerInvoiceRef = $db->f("order_number");\r\n$ewayURL = $mosConfig_live_site."/index.php?option=com_phpshop&page=account.order_details&order_id=".$db->f("order_id");\r\nglobal $vendor_name;\r\n$ewaySiteTitle = $vendor_name;\r\n$ewayAutoRedirect = "1";\r\n\r\n/* additional information you can pass\r\n$eWAYoption1 = "book";\r\n$eWAYoption2 = "section1.xls";\r\n$eWAYoption3 = "";\r\n$ewayTrxnNumber = "2323";\r\n<input type="hidden" name="eWAYoption1" value="<?php echo $eWAYoption1; ?>" />\r\n<input type="hidden" name="eWAYoption2" value="<?php echo $eWAYoption2; ?>" />\r\n<input type="hidden" name="eWAYoption3" value="<?php echo $eWAYoption3; ?>" />\r\n<input type="hidden" name="ewayTrxnNumber" value="<?php echo $ewayTrxnNumber; ?>" />\r\n*/\r\n?>\r\n<form method="post" action="https://www.eway.com.au/gateway/payment.asp">\r\n  <input type="hidden" name="ewayCustomerID" value="<?php echo $ewayCustomerID; ?>" />\r\n  <input type="hidden" name="ewayTotalAmount" value="<?php echo $ewayTotalAmount; ?>" />\r\n  <input type="hidden" name="ewayCustomerFirstName" value="<?php echo $ewayCustomerFirstName; ?>" />\r\n  <input type="hidden" name="ewayCustomerLastName" value="<?php echo $ewayCustomerLastName; ?>" />\r\n  <input type="hidden" name="ewayCustomerEmail" value="<?php echo $ewayCustomerEmail; ?>" />\r\n  <input type="hidden" name="ewayCustomerAddress" value="<?php echo $ewayCustomerAddress; ?>" />\r\n  <input type="hidden" name="ewayCustomerPostcode" value="<?php echo $ewayCustomerPostcode; ?>" />\r\n  <input type="hidden" name="ewayCustomerInvoiceDescription" value="<?php echo $ewayCustomerInvoiceDescription; ?>" />\r\n  <input type="hidden" name="ewayCustomerInvoiceRef" value="<?php echo $ewayCustomerInvoiceRef; ?>" />\r\n  <input type="hidden" name="ewayURL" value="<?php echo $ewayURL; ?>" />\r\n  <input type="hidden" name="ewaySiteTitle" value="<?php echo $ewaySiteTitle; ?>" />\r\n  <input type="hidden" name="ewayAutoRedirect" value="<?php echo $ewayAutoRedirect; ?>" />\r\n  <p><input type="image" alt="Process Secure Credit Card Transaction using eWAY" border="0" height="91" src="http://www.eway.com.au/images/logos/eway.gif" width="200" /></p>\r\n</form>');
INSERT INTO `mos_pshop_payment_method` VALUES ('', 1, 'eCheck.net', 'ps_echeck', 5, '0.00', 0, 'ECK', 'B', 0, 'N', '', '');
	
##############
# COUPONS !!
INSERT INTO `mos_pshop_module` VALUES (12843, 'coupon', 'Coupon Management', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 99, 'eng', '', '', '', '', '', '', '', '', '', 'Coupon', '', '', '', '');

INSERT INTO `mos_pshop_function` VALUES ('', 12843, 'couponAdd', 'ps_coupon', 'add_coupon_code', 'Adds a Coupon.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 12843, 'couponUpdate', 'ps_coupon', 'update_coupon', 'Updates a Coupon.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 12843, 'couponDelete', 'ps_coupon', 'remove_coupon_code', 'Deletes a Coupon.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 12843, 'couponProcess', 'ps_coupon', 'process_coupon_code', 'Processes a Coupon.', 'admin,storeadmin,shopper,demo');
DROP TABLE IF EXISTS `mos_pshop_coupons`;
CREATE TABLE IF NOT EXISTS `mos_pshop_coupons` (
  `coupon_id` int(16) NOT NULL auto_increment,
  `coupon_code` varchar(32) NOT NULL default '',
  `percent_or_total` enum('percent','total') NOT NULL default 'percent',
  `coupon_value` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`coupon_id`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

INSERT INTO `mos_pshop_coupons` VALUES (1, 'test1', 'total', 6.00);
INSERT INTO `mos_pshop_coupons` VALUES (2, 'test2', 'percent', 15.00);
INSERT INTO `mos_pshop_coupons` VALUES (3, 'test3', 'total', 4.00);
INSERT INTO `mos_pshop_coupons` VALUES (4, 'test4', 'total', 15.00);

ALTER TABLE `mos_pshop_vendor` ADD `vendor_freeshipping` DECIMAL( 10, 2 ) NOT NULL AFTER `vendor_min_pov` ;
ALTER TABLE `mos_pshop_orders` ADD `coupon_discount` DECIMAL( 10, 2 ) NOT NULL AFTER `order_shipping_tax` ;

ALTER TABLE `mos_pshop_coupons` ADD `coupon_type` ENUM( 'gift', 'permanent' ) DEFAULT 'gift' NOT NULL AFTER `percent_or_total` ;

ALTER TABLE `mos_users` ADD `bank_account_type` ENUM( 'Checking', 'Business Checking', 'Savings' ) DEFAULT 'Checking' NOT NULL ;

ALTER TABLE `mos_pshop_payment_method` ADD `payment_passkey` BLOB NOT NULL ;
ALTER TABLE `mos_pshop_order_payment` ADD `order_payment_trans_id` TEXT NOT NULL ;
