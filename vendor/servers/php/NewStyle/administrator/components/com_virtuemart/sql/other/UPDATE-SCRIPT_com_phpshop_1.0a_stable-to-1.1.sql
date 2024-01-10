ALTER TABLE `mos_pshop_product` ADD `attribute` text NULL;
ALTER TABLE `mos_pshop_order_item` ADD `product_attribute` text NULL;
ALTER TABLE `mos_pshop_product` ADD `product_tax_id` TINYINT( 2 ) NOT NULL ;
ALTER TABLE `mos_pshop_category` ADD `list_order` INT( 11 ) NOT NULL ;
ALTER TABLE `mos_pshop_product` ADD `product_availability` VARCHAR( 56 ) NOT NULL AFTER `product_available_date` ;

DROP TABLE IF EXISTS `mos_pshop_affiliate_sale`;
CREATE TABLE `mos_pshop_affiliate_sale` (
               `order_id` int(11) NOT NULL,
               `visit_id` varchar(32) NOT NULL,
               `affiliate_id` int(11) NOT NULL,
               `rate` int(2) NOT NULL,
               PRIMARY KEY (`order_id`));
DROP TABLE IF EXISTS `mos_pshop_affiliate`;
CREATE TABLE `mos_pshop_affiliate` (
       `affiliate_id` int(11) NOT NULL auto_increment,
       `user_id` varchar(32) NOT NULL,
       `active` char(1) DEFAULT 'N' NOT NULL,
       `rate` int(11) NOT NULL,
       PRIMARY KEY (`affiliate_id`));
       
DROP TABLE IF EXISTS `mos_pshop_visit`;
CREATE TABLE `mos_pshop_visit` (
             `visit_id` varchar(255) NOT NULL,
             `affiliate_id` int(11) NOT NULL,
             `pages` int(11) NOT NULL,
             `entry_page` varchar(255) NOT NULL,
             `exit_page` varchar(255) NOT NULL,
             `sdate` int(11) NOT NULL,
             `edate` int(11) NOT NULL,
             PRIMARY KEY (`visit_id`));
             
DROP TABLE IF EXISTS `mos_pshop_manufacturer`;
CREATE TABLE `mos_pshop_manufacturer` (
    `manufacturer_id` int(11) NOT NULL auto_increment,
    `mf_name` varchar(64) default NULL,
    `mf_email` varchar(255) default NULL,
    `mf_desc` text,
    `mf_category_id` int(11) default NULL,
    `mf_url` VARCHAR( 255 ) NOT NULL,
    PRIMARY KEY  (`manufacturer_id`)
  ) TYPE=MyISAM;
DROP TABLE IF EXISTS `mos_pshop_manufacturer_category`;
CREATE TABLE `mos_pshop_manufacturer_category` (
              `mf_category_id` int(11) NOT NULL auto_increment,
              `mf_category_name` varchar(64) default NULL,
              `mf_category_desc` text,
              PRIMARY KEY  (`mf_category_id`),
              KEY `idx_manufacturer_category_category_name` (`mf_category_name`)
            ) TYPE=MyISAM;
INSERT INTO `mos_pshop_manufacturer_category` VALUES ('1', '-default-', 'This is the default manufacturer category');
DROP TABLE IF EXISTS `mos_pshop_product_mf_xref`;
CREATE TABLE `mos_pshop_product_mf_xref` (
              `product_id` varchar(32) default NULL,
              `manufacturer_id` int(11) default NULL,
              KEY `idx_product_mf_xref_product_id` (`product_id`),
              KEY `idx_product_mf_xref_manufacturer_id` (`manufacturer_id`)
            ) TYPE=MyISAM;
                          
INSERT INTO `mos_pshop_module` VALUES( '98', 'affiliate', 'administrate the affiliates on your store.', 'storeadmin,admin', 'header.ihtml', 'footer.ihtml', 'N', '99', 'EN', 'ES', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'affiliates', '', '', '', '');
INSERT INTO `mos_pshop_function` VALUES ( '', '98', 'affiliateAdd', 'ps_affiliate', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '98', 'affiliateUpdate', 'ps_affiliate', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '98', 'affiliateDelete', 'ps_affiliate', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '98', 'affiliateEmail', 'ps_affiliate', 'email', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '99', 'manufacturerAdd', 'ps_manufacturer', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '99', 'manufacturerUpdate', 'ps_manufacturer', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '99', 'manufacturerDelete', 'ps_manufacturer', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '99', 'manufacturercategoryAdd', 'ps_manufacturer_category', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '99', 'manufacturercategoryUpdate', 'ps_manufacturer_category', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '', '99', 'manufacturercategoryDelete', 'ps_manufacturer_category', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_module` VALUES( '99', 'manufacturer', 'Manage the manufacturers of products in your store.', 'storeadmin,admin', 'header.ihtml', 'footer.ihtml', 'N', '99', 'EN', 'ES', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'manufacturer', '', '', '', '');