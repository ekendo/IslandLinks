# SQL update script for upgrading 
# from phpshop package 1.0(.1) beta to 1.0.2a beta
# 

#
# Table structure for table `waiting_list`
#

CREATE TABLE `mos_pshop_waiting_list` (
  waiting_list_id int(11) NOT NULL auto_increment,
  product_id int(11) NOT NULL default '0',
  user_id varchar(32) NOT NULL default '',
  notify_email varchar(150) NOT NULL default '',
  notified enum('0','1') default '0',
  notify_date timestamp(14) NOT NULL,
  PRIMARY KEY  (waiting_list_id),
  KEY product_id (product_id),
  KEY notify_email (notify_email)
) TYPE=MyISAM;

#
# New function for `mos_pshop_waiting_list` and `mos_pshop_zone_shipping`
#
INSERT INTO `mos_pshop_function` VALUES ('', 7, 'waitingListAdd', 'zw_waiting_list', 'add', '', 'none');
INSERT INTO `mos_pshop_function` VALUES ('', 13, 'addzone', 'ps_zone', 'add', 'This will add a zone', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 13, 'updatezone', 'ps_zone', 'update', 'This will update a zone', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 13, 'deletezone', 'ps_zone', 'delete', 'This will delete a zone', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 13, 'zoneassign', 'ps_zone', 'assign', 'This will assign a country to a zone', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ('', 1, 'writeConfig', 'ps_config', 'writeconfig', 'This will write the configuration details to phpshop.cfg.php', 'admin');

INSERT INTO `mos_pshop_module` VALUES (13, 'zone', 'This is the zone-shipping module. Here you can manage your shipping costs according to Zones.', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 5, 'eng', 'esl', '', '', '', '', '', '', '', '', 'Zone Shipping', 'Zone Shipping', '', '', '');

UPDATE `mos_pshop_module` SET `module_publish`='N' WHERE `module_name`='isshipping' ;

UPDATE `mos_pshop_function` SET `function_perms` = 'admin,storeadmin,shopper,demo' WHERE `function_id` = '10';

ALTER TABLE `mos_pshop_country` ADD `zone_id` INT( 11 ) DEFAULT '1' NOT NULL AFTER `country_id` ;

ALTER TABLE `mos_pshop_product`ADD `product_sales` int(11) NOT NULL default 0;

ALTER TABLE `mos_pshop_vendor` ADD `vendor_terms_of_service` TEXT NOT NULL ;
INSERT INTO `mos_pshop_vendor` (`vendor_terms_of_service`) VALUES ('<h5>You haven\'t configured any terms of service yet. Click <a href=administrator/index2.php?page=store/store_form&option=com_phpshop>here</a> to change this text.</h5>');

#
# Table structure for table `mos_pshop_zone_shipping`
#

CREATE TABLE `mos_pshop_zone_shipping` (
  `zone_id` int(11) NOT NULL auto_increment,
  `zone_name` varchar(255) default NULL,
  `zone_cost` decimal(10,2) default NULL,
  `zone_limit` decimal(10,2) default NULL,
  `zone_description` text NOT NULL,
  PRIMARY KEY  (`zone_id`),
  KEY `zone_id` (`zone_id`)
) TYPE=MyISAM;

#
# Dumping data for table `mos_pshop_zone_shipping`
#

INSERT INTO `mos_pshop_zone_shipping` VALUES (1, 'Default', '6.00', '35.00', 'This is the default Shipping Zone. This is the zone information that all countries will use until you assign each individual country to a Zone.');
INSERT INTO `mos_pshop_zone_shipping` VALUES (2, 'Zone 1', '100.00', '1000.00', 'This is a zone example');
INSERT INTO `mos_pshop_zone_shipping` VALUES (3, 'Zone 2', '2.00', '22.00', 'This is the second zone. You can use this for notes about this zone');
INSERT INTO `mos_pshop_zone_shipping` VALUES (4, 'Zone 3', '11.00', '64.00', 'Another usefull thing might be details about this zone or special instructions.');

ALTER TABLE mos_users ADD  `user_info_id` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `address_type` char(2) default NULL;
ALTER TABLE mos_users ADD  `address_type_name` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `company` varchar(64) default NULL;
ALTER TABLE mos_users ADD  `title` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `last_name` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `first_name` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `middle_name` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `phone_1` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `phone_2` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `fax` varchar(32) default NULL;
ALTER TABLE mos_users ADD  `address_1` varchar(64) NOT NULL default '';
ALTER TABLE mos_users ADD  `address_2` varchar(64) default NULL;
ALTER TABLE mos_users ADD  `city` varchar(32) NOT NULL default '';
ALTER TABLE mos_users ADD  `state` varchar(32) NOT NULL default '';
ALTER TABLE mos_users ADD  `country` varchar(32) NOT NULL default 'US';
ALTER TABLE mos_users ADD  `zip` varchar(32) NOT NULL default '';
ALTER TABLE mos_users ADD  `extra_field_1` varchar(255) default NULL;
ALTER TABLE mos_users ADD  `extra_field_2` varchar(255) default NULL;
ALTER TABLE mos_users ADD  `extra_field_3` varchar(255) default NULL;
ALTER TABLE mos_users ADD  `extra_field_4` char(1) default NULL;
ALTER TABLE mos_users ADD  `extra_field_5` char(1) default NULL;
ALTER TABLE mos_users ADD  `perms` VARCHAR( 40 ) DEFAULT 'shopper' NOT NULL;