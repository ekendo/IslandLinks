ALTER TABLE `mos_pshop_vendor` ADD `vendor_url` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `mos_pshop_vendor` ADD `vendor_min_pov` DECIMAL( 10, 2 ) ;

ALTER TABLE `mos_pshop_orders` ADD `order_total` DECIMAL( 10, 2 ) DEFAULT '0,0' NOT NULL AFTER `user_info_id` ;
ALTER TABLE `mos_pshop_orders` ADD `order_discount` DECIMAL( 10, 2 ) NOT NULL AFTER `order_shipping_tax` ;

ALTER TABLE `mos_users` ADD bank_account_nr varchar(32) NOT NULL;
ALTER TABLE `mos_users` ADD bank_name varchar(32) NOT NULL;
ALTER TABLE `mos_users` ADD bank_sort_code varchar(16) NOT NULL;
ALTER TABLE `mos_users` ADD bank_iban varchar(64) NOT NULL;
ALTER TABLE `mos_users` ADD bank_account_holder varchar(48) NOT NULL;

#
# TABLE SHIPPING CARRIER
#
create table `mos_pshop_shipping_carrier` (
shipping_carrier_id int(11) not null auto_increment, 
shipping_carrier_name char(80) default '' not null, 
shipping_carrier_list_order int(11) not null default 0, 
PRIMARY KEY (shipping_carrier_id));

INSERT INTO `mos_pshop_shipping_carrier` VALUES (1, 'Deutsche Post', 0);

#
# TABLE SHIPPING RATE
#
DROP table if exists `mos_pshop_shipping_rate`;
create table `mos_pshop_shipping_rate` (
shipping_rate_id int(11) not null auto_increment, 
shipping_rate_name varchar(255) default '' not null, 
shipping_rate_carrier_id int(11) default '0' not null, 
shipping_rate_country text default '' not null, 
shipping_rate_zip_start varchar(32) default '' not null, 
shipping_rate_zip_end varchar(32) default '' not null, 
shipping_rate_weight_start decimal(10,3) default '0' not null, 
shipping_rate_weight_end decimal(10,3) default '0' not null, 
shipping_rate_value decimal(10,2) default '0' not null, 
shipping_rate_package_fee decimal(10,2) default '0' not null, 
shipping_rate_currency_id int(11) default '0' not null, 
shipping_rate_vat_id int(11) default '0' not null,
shipping_rate_list_order int(11) default '0' not null, 
PRIMARY KEY (shipping_rate_id));

INSERT INTO `mos_pshop_shipping_rate` VALUES (1,'Inland <  4kg','1','DEU','00000','99999','0.0','4.0','5.62','2','47','0','1');
INSERT INTO `mos_pshop_shipping_rate` VALUES (2,'Inland <  8kg','1','DEU','00000','99999','4.0','8.0','6.39','2','47','0','2');
INSERT INTO `mos_pshop_shipping_rate` VALUES (3,'Inland < 12kg','1','DEU','00000','99999','8.0','12.0','7.16','2','47','0','3');
INSERT INTO `mos_pshop_shipping_rate` VALUES (4,'Inland < 20kg','1','DEU','00000','99999','12.0','20.0','8.69','2','47','0','4');
INSERT INTO `mos_pshop_shipping_rate` VALUES (5,'EU+ <  4kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','0.0','4.0','14,57','2','47','0','5');
INSERT INTO `mos_pshop_shipping_rate` VALUES (6,'EU+ <  8kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','4.0','8.0','18,66','2','47','0','6');
INSERT INTO `mos_pshop_shipping_rate` VALUES (7,'EU+ < 12kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','8.0','12.0','22,57','2','47','0','7');
INSERT INTO `mos_pshop_shipping_rate` VALUES (8,'EU+ < 20kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','12.0','20.0','30,93','2','47','0','8');
INSERT INTO `mos_pshop_shipping_rate` VALUES (9,'Europa <  4kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','0.0','4.0','23,78','2','47','0','9');
INSERT INTO `mos_pshop_shipping_rate` VALUES (10,'Europa <  8kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','4.0','8.0','29,91','2','47','0','10');
INSERT INTO `mos_pshop_shipping_rate` VALUES (11,'Europa < 12kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','8.0','12.0','36,05','2','47','0','11');
INSERT INTO `mos_pshop_shipping_rate` VALUES (12,'Europa < 20kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','12.0','20.0','48,32','2','47','0','12');
INSERT INTO `mos_pshop_shipping_rate` VALUES (13,'Welt_1 <  4kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','0.0','4.0','26,84','2','47','0','13');
INSERT INTO `mos_pshop_shipping_rate` VALUES (14,'Welt_1 <  8kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','4.0','8.0','35,02','2','47','0','14');
INSERT INTO `mos_pshop_shipping_rate` VALUES (15,'Welt_1 < 12kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','8.0','12.0','43,20','2','47','0','15');
INSERT INTO `mos_pshop_shipping_rate` VALUES (16,'Welt_1 < 20kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','12.0','20.0','59,57','2','47','0','16');
INSERT INTO `mos_pshop_shipping_rate` VALUES (17,'Welt_2 <  4kg','1','','00000','99999','0.0','4.0','32,98','2','47','0','17');
INSERT INTO `mos_pshop_shipping_rate` VALUES (18,'Welt_2 <  8kg','1','','00000','99999','4.0','8.0','47,29','2','47','0','18');
INSERT INTO `mos_pshop_shipping_rate` VALUES (19,'Welt_2 < 12kg','1','','00000','99999','8.0','12.0','61,61','2','47','0','19');
INSERT INTO `mos_pshop_shipping_rate` VALUES (20,'Welt_2 < 20kg','1','','00000','99999','12.0','20.0','90,24','2','47','0','20');

INSERT INTO `mos_pshop_function` VALUES ( '86', '12839', 'carrierAdd', 'ps_shipping', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '87', '12839', 'carrierDelete', 'ps_shipping', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '88', '12839', 'carrierUpdate', 'ps_shipping', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '89', '12839', 'rateAdd', 'ps_shipping', 'rate_add', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '90', '12839', 'rateUpdate', 'ps_shipping', 'rate_update', '', 'admin,shopadmin');
INSERT INTO `mos_pshop_function` VALUES ( '91', '12839', 'rateDelete', 'ps_shipping', 'rate_delete', '', 'admin,storeadmin');
INSERT INTO `mos_pshop_function` VALUES ( '92', '10', 'checkoutProcess', 'ps_checkout', 'process', '', 'shopper,storeadmin,admin,demo');

INSERT INTO `mos_pshop_module` VALUES ( '12839', 'shipping', '<h4>Shipping</h4>
<p>Let this module calculate the shipping fees for your customers.<br>
Create carriers for shipping areas and weight groups.</p>', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', '20', 'eng', 'ger', '', '', '', '', '', '', '', '', 'Shipping', 'Versand', '', '', '');
