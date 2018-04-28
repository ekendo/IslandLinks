# SQL update script for upgrading 
# from phpshop package 1.1a  to 1.2
# 

ALTER TABLE `mos_pshop_shopper_group` ADD `default` TINYINT( 1 ) DEFAULT '0' NOT NULL ;

#
# Tabellenstruktur für Tabelle `mos_pshop_product_reviews`
#

CREATE TABLE `mos_pshop_product_reviews` (
  `product_id` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  `userid` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `user_rating` tinyint(1) NOT NULL default '0',
  `review_ok` int(11) NOT NULL default '0',
  `review_votes` int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mos_pshop_product_votes`
#

CREATE TABLE `mos_pshop_product_votes` (
  `product_id` int(255) NOT NULL default '0',
  `votes` text NOT NULL,
  `allvotes` int(11) NOT NULL default '0',
  `rating` tinyint(1) NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '0'
) TYPE=MyISAM;

INSERT INTO `mos_pshop_function` VALUES ('', 7, 'addReview', 'ps_reviews', 'process_review', 'This lets the user add a review and rating to a product.', 'admin,storeadmin,shopper,demo');

ALTER TABLE `mos_pshop_category` ADD `category_browsepage` VARCHAR( 255 ) DEFAULT 'browse_1' NOT NULL AFTER `mdate` ;
ALTER TABLE `mos_pshop_category` ADD `products_per_row` TINYINT( 2 ) DEFAULT '1' NOT NULL AFTER `category_browsepage` ;

ALTER TABLE `mos_pshop_csv` ADD `csv_manufacturer_id` INT( 2 ) DEFAULT NULL;
UPDATE `mos_pshop_csv` SET csv_manufacturer_id='19';

ALTER TABLE `mos_pshop_payment_method` ADD `payment_class` VARCHAR( 50 ) NOT NULL AFTER `payment_method_name` ;
ALTER TABLE `mos_pshop_payment_method` ADD `payment_enabled` CHAR( 1 ) DEFAULT 'N' NOT NULL ;
ALTER TABLE `mos_pshop_payment_method` ADD `accepted_creditcards` VARCHAR( 128 ) NOT NULL ;
ALTER TABLE `mos_pshop_payment_method` ADD `payment_extrainfo` TEXT NOT NULL ;

ALTER TABLE `mos_pshop_order_payment` ADD `order_payment_code` VARCHAR( 30 ) NOT NULL AFTER `payment_method_id` ;

CREATE TABLE `mos_pshop_creditcard` (
`creditcard_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`vendor_id` INT( 11 ) NOT NULL,
`creditcard_name` VARCHAR( 70 ) NOT NULL ,
`creditcard_code` VARCHAR( 30 ) NOT NULL ,
PRIMARY KEY ( `creditcard_id` )
);
INSERT INTO `mos_pshop_creditcard` VALUES (1, 1, 'Visa', 'VISA');
INSERT INTO `mos_pshop_creditcard` VALUES (2, 1, 'MasterCard', 'MC');
INSERT INTO `mos_pshop_creditcard` VALUES (3, 1, 'American Express', 'amex');
INSERT INTO `mos_pshop_creditcard` VALUES (4, 1, 'Discover Card', 'discover');
INSERT INTO `mos_pshop_creditcard` VALUES (5, 1, 'Diners Club', 'diners');
INSERT INTO `mos_pshop_creditcard` VALUES (6, 1, 'JCB', 'jcb');
INSERT INTO `mos_pshop_creditcard` VALUES (7, 1, 'Australian Bankcard', 'australian_bc');

INSERT INTO `mos_pshop_function` VALUES ('', '2', 'publishProduct', 'ps_product', 'product_publish', 'Changes the product_publish field, so that a product can be published or unpublished easily.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', '2', 'export_csv', 'ps_csv', 'export_csv', 'This function exports all relevant product data to CSV.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', '8', 'creditcardAdd', 'ps_creditcard', 'add', 'Adds a Credit Card entry.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', '8', 'creditcardUpdate', 'ps_creditcard', 'update', 'Updates a Credit Card entry.', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', '8', 'creditcardDelete', 'ps_creditcard', 'delete', 'Deletes a Credit Card entry.', 'admin,storeadmin');
