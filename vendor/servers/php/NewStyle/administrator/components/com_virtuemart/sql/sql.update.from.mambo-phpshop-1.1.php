<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: sql.update.from.mambo-phpshop-1.1.php,v 1.2 2005/10/01 11:47:16 soeren_nb Exp $
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
/**
* Run all the upgrade queries 
*/
$database->setQuery( "ALTER TABLE `#__pshop_shopper_group` ADD `default` TINYINT( 1 ) DEFAULT '0' NOT NULL ;"); $database->query();
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_reviews` (
  `product_id` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  `userid` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `user_rating` tinyint(1) NOT NULL default '0',
  `review_ok` int(11) NOT NULL default '0',
  `review_votes` int(11) NOT NULL default '0'
) TYPE=MyISAM;"); $database->query();
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_votes` (
  `product_id` int(255) NOT NULL default '0',
  `votes` text NOT NULL,
  `allvotes` int(11) NOT NULL default '0',
  `rating` tinyint(1) NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '0'
) TYPE=MyISAM;"); $database->query();

$database->setQuery( "ALTER TABLE `#__pshop_category` ADD `category_browsepage` VARCHAR( 255 ) DEFAULT 'browse_1' NOT NULL AFTER `mdate` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_category` ADD `products_per_row` TINYINT( 2 ) DEFAULT '1' NOT NULL AFTER `category_browsepage` ;"); $database->query();

$database->setQuery( "ALTER TABLE `#__pshop_csv` ADD `csv_manufacturer_id` INT( 2 ) DEFAULT NULL;"); $database->query();
$database->setQuery( "UPDATE `#__pshop_csv` SET csv_manufacturer_id='19';"); $database->query();

$database->setQuery( "ALTER TABLE `#__pshop_payment_method` ADD `payment_class` VARCHAR( 50 ) NOT NULL AFTER `payment_method_name` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_payment_method` ADD `payment_enabled` CHAR( 1 ) DEFAULT 'N' NOT NULL ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_payment_method` ADD `accepted_creditcards` VARCHAR( 128 ) NOT NULL ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_payment_method` ADD `payment_extrainfo` TEXT NOT NULL ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_payment_method` ADD `payment_passkey` BLOB NOT NULL ;"); $database->query();
	
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'NEW_Authorize.net', 'ps_authorize', 5, '0.00', 0, 'AN', 'Y', 0, 'N', '1,2,6,7,', '', '');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'NEW_PayPal', 'ps_paypal', 5, '0.00', 0, 'PP', 'P', 0, 'N', '', '<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_blank\">\r\n<input type=\"image\" name=\"submit\" src=\"http://images.paypal.com/images/x-click-but6.gif\" border=\"0\" alt=\"Make payments with PayPal, it\'s fast, free, and secure!\">\r\n<input type=\"hidden\" name=\"cmd\" value=\"_xclick\" />\r\n<input type=\"hidden\" name=\"business\" value=\"<?php echo PAYPAL_EMAIL ?>\" />\r\n<input type=\"hidden\" name=\"receiver_email\" value=\"<?php echo PAYPAL_EMAIL ?>\" />\r\n<input type=\"hidden\" name=\"item_name\" value=\"Order Nr. <?php \$db->p(\"order_id\") ?>\" />\r\n<input type=\"hidden\" name=\"order_id\" value=\"<?php \$db->p(\"order_id\") ?>\" />\r\n<input type=\"hidden\" name=\"invoice\" value=\"<?php \$db->p(\"order_number\") ?>\" />\r\n<input type=\"hidden\" name=\"amount\" value=\"<?php printf(\"%.2f\", \$db->f(\"order_total\"))?>\" />\r\n<input type=\"hidden\" name=\"currency_code\" value=\"<?php echo \$_SESSION[\'vendor_currency\'] ?>\" />\r\n<input type=\"hidden\" name=\"image_url\" value=\"<?php echo \$vendor_image_url ?>\" />\r\n<input type=\"hidden\" name=\"return\" value=\"<?php echo SECUREURL .\"index.php?option=com_phpshop&amp;page=checkout.result&amp;order_id=\".\$db->f(\"order_id\") ?>\" />\r\n<input type=\"hidden\" name=\"notify_url\" value=\"<?php echo SECUREURL .\"administrator/components/com_phpshop/notify.php\" ?>\" />\r\n<input type=\"hidden\" name=\"cancel_return\" value=\"<?php echo SECUREURL .\"index.php\" ?>\" />\r\n<input type=\"hidden\" name=\"undefined_quantity\" value=\"0\" />\r\n<input type=\"hidden\" name=\"pal\" value=\"NRUBJXESJTY24\" />\r\n<input type=\"hidden\" name=\"no_shipping\" value=\"1\" />\r\n<input type=\"hidden\" name=\"no_note\" value=\"1\" />\r\n</form>', '');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'NEW_PayMate', 'ps_paymate', 5, '0.00', 0, 'PM', 'P', 0, 'N', '', '<script type=\"text/javascript\" language=\"javascript\">
  function openExpress(){
	var url = 'https://www.paymate.com.au/PayMate/ExpressPayment?mid=<?php echo PAYMATE_USERNAME.
	  \"&amt=\".\$db->f(\"order_total\").
	  \"&currency=\".\$_SESSION['vendor_currency'].
	  \"&ref=\".\$db->f(\"order_id\").
	  \"&pmt_sender_email=\".\$user->email.
	  \"&pmt_contact_firstname=\".\$user->first_name.
	  \"&pmt_contact_surname=\".\$user->last_name.
	  \"&regindi_address1=\".\$user->address_1.
	  \"&regindi_address2=\".\$user->address_2.
	  \"&regindi_sub=\".\$user->city.
	  \"&regindi_pcode=\".\$user->zip;?>'
	var newWin = window.open(url, 'wizard', 'height=640,width=500,scrollbars=0,toolbar=no');
	self.name = 'parent';
	newWin.focus();
  }
  </script>
  <div align=\"center\">
  <p>
  <a href=\"javascript:openExpress();\">
  <img src=\"https://www.paymate.com.au/homepage/images/butt_PayNow.gif\" border=\"0\" alt=\"Pay with Paymate Express\">
  <br />click here to pay your account</a>
  </p>
  </div>', '');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'NEW_WorldPay', 'ps_worldpay', 5, '0.00', 0, 'WP', 'P', 0, 'N', '', '<form action=\"https://select.worldpay.com/wcc/purchase\" method=\"post\">
						<input type=hidden name=\"testMode\" value=\"100\"> 
						<input type=\"hidden\" name=\"instId\" value=\"<?php echo WORLDPAY_INST_ID ?>\" />
						<input type=\"hidden\" name=\"cartId\" value=\"<?php echo \$db->f(\"order_id\") ?>\" />
						<input type=\"hidden\" name=\"amount\" value=\"<?php echo \$db->f(\"order_total\") ?>\" />
						<input type=\"hidden\" name=\"currency\" value=\"<?php echo \$_SESSION[\'vendor_currency\'] ?>\" />
						<input type=\"hidden\" name=\"desc\" value=\"Products\" />
						<input type=\"hidden\" name=\"email\" value=\"<?php echo \$user->email?>\" />
						<input type=\"hidden\" name=\"address\" value=\"<?php echo \$user->address_1?>&#10<?php echo \$user->address_2?>&#10<?php echo
						\$user->city?>&#10<?php echo \$user->state?>\" />
						<input type=\"hidden\" name=\"name\" value=\"<?php echo \$user->title?><?php echo \$user->first_name?>. <?php echo \$user->middle_name?><?php echo \$user->last_name?>\" />
						<input type=\"hidden\" name=\"country\" value=\"<?php echo \$user->country?>\"/>
						<input type=\"hidden\" name=\"postcode\" value=\"<?php echo \$user->zip?>\" />
						<input type=\"hidden\" name=\"tel\"  value=\"<?php echo \$user->phone_1?>\">
						<input type=\"hidden\" name=\"withDelivery\"  value=\"true\">
						<br />
						<input type=\"submit\" value =\"PROCEED TO PAYMENT PAGE\" />
						</form>', '');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'NEW_2Checkout', 'ps_twocheckout', 5, '0.00', 0, '2CO', 'P', 0, 'N', '<?php
  \$q  = \"SELECT * FROM #__users WHERE user_info_id=\'\".\$db->f(\"user_info_id\").\"\'\"; 
  \$dbbt = new ps_DB;
  \$dbbt->setQuery(\$q);
  \$dbbt->query();
  \$dbbt->next_record(); 
  // Get ship_to information
  if( \$db->f(\"user_info_id\") != \$dbbt->f(\"user_info_id\")) {
	\$q2  = \"SELECT * FROM #__pshop_user_info WHERE user_info_id=\'\".\$db->f(\"user_info_id\").\"\'\"; 
	\$dbst = new ps_DB;
	\$dbst->setQuery(\$q2);
	\$dbst->query();
	\$dbst->next_record();
  }
  else  {
	\$dbst = \$dbbt;
  }
		  
  //Authnet vars to send
  \$formdata = array (
   \'x_login\' => TWOCO_LOGIN,
   \'x_email_merchant\' => ((TWOCO_MERCHANT_EMAIL == \'True\') ? \'TRUE\' : \'FALSE\'),
		   
   // Customer Name and Billing Address
   \'x_first_name\' => \$dbbt->f(\"first_name\"),
   \'x_last_name\' => \$dbbt->f(\"last_name\"),
   \'x_company\' => \$dbbt->f(\"company\"),
   \'x_address\' => \$dbbt->f(\"address_1\"),
   \'x_city\' => \$dbbt->f(\"city\"),
   \'x_state\' => \$dbbt->f(\"state\"),
   \'x_zip\' => \$dbbt->f(\"zip\"),
   \'x_country\' => \$dbbt->f(\"country\"),
   \'x_phone\' => \$dbbt->f(\"phone_1\"),
   \'x_fax\' => \$dbbt->f(\"fax\"),
   \'x_email\' => \$dbbt->f(\"email\"),
  
   // Customer Shipping Address
   \'x_ship_to_first_name\' => \$dbst->f(\"first_name\"),
   \'x_ship_to_last_name\' => \$dbst->f(\"last_name\"),
   \'x_ship_to_company\' => \$dbst->f(\"company\"),
   \'x_ship_to_address\' => \$dbst->f(\"address_1\"),
   \'x_ship_to_city\' => \$dbst->f(\"city\"),
   \'x_ship_to_state\' => \$dbst->f(\"state\"),
   \'x_ship_to_zip\' => \$dbst->f(\"zip\"),
   \'x_ship_to_country\' => \$dbst->f(\"country\"),
  
   \'x_invoice_num\' => \$db->f(\"order_number\"),
   \'x_receipt_link_url\' => SECUREURL.\"2checkout_notify.php\"
   );
   
  if( TWOCO_TESTMODE == \"Y\" )
	\$formdata[\'demo\'] = \"Y\";
  
   \$version = \"2\";
   \$url = \"https://www2.2checkout.com/2co/buyer/purchase\";
   \$formdata[\'x_amount\'] = \$db->f(\"order_total\");
	
   //build the post string
   \$poststring = \'\';
   foreach(\$formdata AS \$key => \$val){
	 \$poststring .= \"<input type=\'hidden\' name=\'\$key\' value=\'\$val\' />
  \";
   }
  
  ?>
  <form action=\"<?php echo \$url ?>\" method=\"post\" target=\"_blank\">
  <?php echo \$poststring ?>
  <p>Click on the Image below to pay...</p>
  <input type=\"image\" name=\"submit\" src=\"https://www.2checkout.com/images/buy_logo.gif\" border=\"0\" alt=\"Make payments with 2Checkout, it\'s fast and secure!\" title=\"Pay your Order with 2Checkout, it\'s fast and secure!\" />
  </form>', '');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'NEW_NoChex', 'ps_nochex', 5, '0.00', 0, 'NOCHEX', 'P', 0, 'N', '', '<form action=\"https://www.nochex.com/nochex.dll/checkout\" method=post target=\"_blank\"> 
											<input type=\"hidden\" name=\"email\" value=\"<?php echo NOCHEX_EMAIL ?>\" />
											<input type=\"hidden\" name=\"amount\" value=\"<?php printf(\"%.2f\", \$db->f(\"order_total\"))?>\" />
											<input type=\"hidden\" name=\"ordernumber\" value=\"<?php \$db->p(\"order_id\") ?>\" />
											<input type=\"hidden\" name=\"logo\" value=\"<?php echo \$vendor_image_url ?>\" />
											<input type=\"hidden\" name=\"returnurl\" value=\"<?php echo SECUREURL .\"index.php?option=com_phpshop&amp;page=checkout.result&amp;order_id=\".\$db->f(\"order_id\") ?>\" />
											<input type=\"image\" name=\"submit\" src=\"http://www.nochex.com/web/images/paymeanimated.gif\"> 
											</form>', '');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'NEW_eWay', 'ps_eway', 5, '0.00', 0, 'EW', 'Y', 0, 'N', '', '', '');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'eCheck.net', 'ps_echeck', 5, '0.00', 0, 'ECK', 'B', 0, 'N', '', '', '');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'Dankort / PBS', 'ps_pbs', 5, '0.00', 0, 'PBS', 'P', 0, 'N', '', '', '');"); $database->query();

$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_creditcard` (
`creditcard_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`vendor_id` INT( 11 ) NOT NULL,
`creditcard_name` VARCHAR( 70 ) NOT NULL ,
`creditcard_code` VARCHAR( 30 ) NOT NULL ,
PRIMARY KEY ( `creditcard_id` ));"); $database->query();

$database->setQuery( "INSERT INTO `#__pshop_creditcard` VALUES (1, 1, 'Visa', 'VISA');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_creditcard` VALUES (2, 1, 'MasterCard', 'MC');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_creditcard` VALUES (3, 1, 'American Express', 'amex');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_creditcard` VALUES (4, 1, 'Discover Card', 'discover');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_creditcard` VALUES (5, 1, 'Diners Club', 'diners');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_creditcard` VALUES (6, 1, 'JCB', 'jcb');" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_creditcard` VALUES (7, 1, 'Australian Bankcard', 'australian_bc');" ); $database->query();

$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 7, 'addReview', 'ps_reviews', 'process_review', 'This lets the user add a review and rating to a product.', 'admin,storeadmin,shopper,demo');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', '7', 'productReviewDelete', 'ps_reviews', 'delete_review', 'This deletes a review and from a product.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', '2', 'publishProduct', 'ps_product', 'product_publish', 'Changes the product_publish field, so that a product can be published or unpublished easily.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', '2', 'export_csv', 'ps_csv', 'export_csv', 'This function exports all relevant product data to CSV.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', '8', 'creditcardAdd', 'ps_creditcard', 'add', 'Adds a Credit Card entry.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', '8', 'creditcardUpdate', 'ps_creditcard', 'update', 'Updates a Credit Card entry.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', '8', 'creditcardDelete', 'ps_creditcard', 'delete', 'Deletes a Credit Card entry.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'reorder', 'ps_product_category', 'reorder', 'Changes the list order of a category.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'discountAdd', 'ps_product_discount', 'add', 'Adds a discount.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'discountUpdate', 'ps_product_discount', 'update', 'Updates a discount.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'discountDelete', 'ps_product_discount', 'delete', 'Deletes a discount.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 8, 'shippingmethodSave', 'ps_shipping_method', 'save', '', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'uploadProductFile', 'ps_product_files', 'add', 'Uploads and Adds a Product Image/File.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'updateProductFile', 'ps_product_files', 'update', 'Updates a Product Image/File.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'deleteProductFile', 'ps_product_files', 'delete', 'Deletes a Product Image/File.', 'admin,storeadmin');"); $database->query();

$database->setQuery( "INSERT INTO `#__pshop_module` VALUES (12843, 'coupon', 'Coupon Management', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 99, 'eng', '', '', '', '', '', '', '', '', '', 'Coupon', '', '', '', '');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 12843, 'couponAdd', 'ps_coupon', 'add_coupon_code', 'Adds a Coupon.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 12843, 'couponUpdate', 'ps_coupon', 'update_coupon', 'Updates a Coupon.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 12843, 'couponDelete', 'ps_coupon', 'remove_coupon_code', 'Deletes a Coupon.', 'admin,storeadmin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 12843, 'couponProcess', 'ps_coupon', 'process_coupon_code', 'Processes a Coupon.', 'admin,storeadmin,shopper,demo');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 1, 'stateAdd', 'ps_country', 'addState', 'Add a State ', 'storeadmin,admin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 1, 'stateUpdate', 'ps_country', 'updateState', 'Update a state record', 'storeadmin,admin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 1, 'stateDelete', 'ps_country', 'deleteState', 'Delete a state record', 'storeadmin,admin');"); $database->query();

$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_discount` (
  `discount_id` int(11) NOT NULL auto_increment,
  `amount` decimal(5,2) NOT NULL default '0.00',
  `is_percent` tinyint(1) NOT NULL default '0',
  `start_date` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`discount_id`)
) TYPE=MyISAM;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_shopper_group` ADD `shopper_group_discount` DECIMAL( 3,2 ) DEFAULT '0' NOT NULL AFTER `shopper_group_desc` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_vendor` ADD `vendor_currency_display_style` VARCHAR( 64 ) DEFAULT '1|$|2|.| |2|1' NOT NULL ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_vendor` ADD `vendor_freeshipping` DECIMAL( 10, 2 ) NOT NULL AFTER `vendor_min_pov` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_product` ADD `custom_attribute` TEXT NOT NULL;"); $database->query();

$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_files` (
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
) TYPE=MyISAM;" ); $database->query();
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_coupons` (
  `coupon_id` int(16) NOT NULL auto_increment,
  `coupon_code` varchar(32) NOT NULL default '',
  `percent_or_total` enum('percent','total') NOT NULL default 'percent',
  `coupon_type` ENUM( 'gift', 'permanent' ) DEFAULT 'gift' NOT NULL,
  `coupon_value` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`coupon_id`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;"); $database->query();

$database->setQuery( "ALTER TABLE `#__pshop_orders` ADD `coupon_discount` DECIMAL( 10, 2 ) NOT NULL AFTER `order_shipping_tax` ;"); $database->query();

$database->setQuery( "ALTER TABLE `#__users` ADD `bank_account_type` ENUM( 'Checking', 'Business Checking', 'Savings' ) DEFAULT 'Checking' NOT NULL ;"); $database->query();

$database->setQuery( "ALTER TABLE `#__pshop_order_payment` ADD `order_payment_trans_id` TEXT NOT NULL ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_order_payment` ADD `order_payment_code` VARCHAR( 30 ) NOT NULL AFTER `payment_method_id` ;"); $database->query();
	
$database->setQuery( "ALTER TABLE `#__pshop_order_item` DROP INDEX `idx_order_item_product_id` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_order_item` 
  ADD `order_item_sku` VARCHAR( 64 ) NOT NULL AFTER `product_id` ,
  ADD `order_item_name` VARCHAR( 64 ) NOT NULL AFTER `order_item_sku` ;"); $database->query();

$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_order_user_info` (
  `order_info_id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `user_id` varchar(32) NOT NULL default '',
  `address_type` char(2) default NULL,
  `address_type_name` varchar(32) default NULL,
  `company` varchar(64) default NULL,
  `title` varchar(32) default NULL,
  `last_name` varchar(32) default NULL,
  `first_name` varchar(32) default NULL,
  `middle_name` varchar(32) default NULL,
  `phone_1` varchar(32) default NULL,
  `phone_2` varchar(32) default NULL,
  `fax` varchar(32) default NULL,
  `address_1` varchar(64) NOT NULL default '',
  `address_2` varchar(64) default NULL,
  `city` varchar(32) NOT NULL default '',
  `state` varchar(32) NOT NULL default '',
  `country` varchar(32) NOT NULL default 'US',
  `zip` varchar(32) NOT NULL default '',
  `user_email` varchar(255) default NULL,
  `extra_field_1` varchar(255) default NULL,
  `extra_field_2` varchar(255) default NULL,
  `extra_field_3` varchar(255) default NULL,
  `extra_field_4` char(1) default NULL,
  `extra_field_5` char(1) default NULL,
  PRIMARY KEY  (`order_info_id`),
  KEY `idx_order_info_order_id` (`order_id`)
) TYPE=MyISAM;"); $database->query();

@rename( $mosConfig_absolute_path."/components/com_phpshop/shop_image/ps_image/toplogo.gif", $mosConfig_absolute_path."/administrator/components/com_phpshop/shop_image/ps_image/toplogo.gif" );
@rename( $mosConfig_absolute_path."/components/com_phpshop/shop_image/ps_image/com_phpshop_poweredby.gif", $mosConfig_absolute_path."/administrator/components/com_phpshop/shop_image/ps_image/com_phpshop_poweredby.gif" );

?> 