# $Id: phpshop.sql,v 1.2 2005/09/29 20:02:18 soeren_nb Exp $
#
#
############################################################
# DATABASE STRUCTURE AND DATA FOR VirtueMart Component
############################################################

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
ALTER TABLE mos_users ADD  `bank_account_nr` varchar(32) NOT NULL;
ALTER TABLE mos_users ADD  `bank_name` varchar(32) NOT NULL;
ALTER TABLE mos_users ADD  `bank_sort_code` varchar(16) NOT NULL;
ALTER TABLE mos_users ADD  `bank_iban` varchar(64) NOT NULL;
ALTER TABLE mos_users ADD  `bank_account_holder` varchar(48) NOT NULL;

DROP TABLE IF EXISTS `mos_{vm}_auth_user_vendor`;
CREATE TABLE `mos_{vm}_auth_user_vendor` (
      `user_id` varchar(32) default NULL,
      `vendor_id` int(11) default NULL,
      KEY `idx_auth_user_vendor_user_id` (`user_id`),
      KEY `idx_auth_user_vendor_vendor_id` (`vendor_id`)
        ) TYPE=MyISAM;
        
DROP TABLE IF EXISTS `mos_{vm}_category`;
CREATE TABLE `mos_{vm}_category` (
  `category_id` varchar(32) NOT NULL default '',
  `vendor_id` int(11) NOT NULL default '0',
  `category_name` varchar(128) NOT NULL default '',
  `category_description` text,
  `category_thumb_image` varchar(255) default NULL,
  `category_full_image` varchar(255) default NULL,
  `category_publish` char(1) default NULL,
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `category_browsepage` VARCHAR( 255 ) DEFAULT 'browse_1' NOT NULL,
  `products_per_row` TINYINT( 2 ) DEFAULT '1' NOT NULL,
  `category_flypage` varchar(255) default NULL,
  `list_order`int(11) default NULL,
  PRIMARY KEY  (`category_id`),
  KEY `idx_category_vendor_id` (`vendor_id`),
  KEY `idx_category_name` (`category_name`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_category_xref`;
CREATE TABLE `mos_{vm}_category_xref` (
  `category_parent_id` varchar(32) default '0' NOT NULL,
  `category_child_id` varchar(32) default '0' NOT NULL,
  `category_list` int(11) default NULL,
  KEY `category_xref_category_parent_id` (`category_parent_id`),
  KEY `category_xref_category_child_id` (`category_child_id`),
  KEY `idx_category_xref_category_list` (`category_list`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_country`;
CREATE TABLE `mos_{vm}_country` (
  `country_id` int(11) NOT NULL auto_increment,
  `zone_id` int(11) NOT NULL default '1',
  `country_name` varchar(64) default NULL,
  `country_3_code` char(3) default NULL,
  `country_2_code` char(2) default NULL,
  PRIMARY KEY  (`country_id`),
  KEY `idx_country_name` (`country_name`)
) TYPE=MyISAM AUTO_INCREMENT=240 ;
INSERT INTO `mos_{vm}_country` VALUES (1, 1, 'Afghanistan', 'AFG', 'AF');
INSERT INTO `mos_{vm}_country` VALUES (2, 1, 'Albania', 'ALB', 'AL');
INSERT INTO `mos_{vm}_country` VALUES (3, 1, 'Algeria', 'DZA', 'DZ');
INSERT INTO `mos_{vm}_country` VALUES (4, 1, 'American Samoa', 'ASM', 'AS');
INSERT INTO `mos_{vm}_country` VALUES (5, 1, 'Andorra', 'AND', 'AD');
INSERT INTO `mos_{vm}_country` VALUES (6, 1, 'Angola', 'AGO', 'AO');
INSERT INTO `mos_{vm}_country` VALUES (7, 1, 'Anguilla', 'AIA', 'AI');
INSERT INTO `mos_{vm}_country` VALUES (8, 1, 'Antarctica', 'ATA', 'AQ');
INSERT INTO `mos_{vm}_country` VALUES (9, 1, 'Antigua and Barbuda', 'ATG', 'AG');
INSERT INTO `mos_{vm}_country` VALUES (10, 1, 'Argentina', 'ARG', 'AR');
INSERT INTO `mos_{vm}_country` VALUES (11, 1, 'Armenia', 'ARM', 'AM');
INSERT INTO `mos_{vm}_country` VALUES (12, 1, 'Aruba', 'ABW', 'AW');
INSERT INTO `mos_{vm}_country` VALUES (13, 1, 'Australia', 'AUS', 'AU');
INSERT INTO `mos_{vm}_country` VALUES (14, 1, 'Austria', 'AUT', 'AT');
INSERT INTO `mos_{vm}_country` VALUES (15, 1, 'Azerbaijan', 'AZE', 'AZ');
INSERT INTO `mos_{vm}_country` VALUES (16, 1, 'Bahamas', 'BHS', 'BS');
INSERT INTO `mos_{vm}_country` VALUES (17, 1, 'Bahrain', 'BHR', 'BH');
INSERT INTO `mos_{vm}_country` VALUES (18, 1, 'Bangladesh', 'BGD', 'BD');
INSERT INTO `mos_{vm}_country` VALUES (19, 1, 'Barbados', 'BRB', 'BB');
INSERT INTO `mos_{vm}_country` VALUES (20, 1, 'Belarus', 'BLR', 'BY');
INSERT INTO `mos_{vm}_country` VALUES (21, 1, 'Belgium', 'BEL', 'BE');
INSERT INTO `mos_{vm}_country` VALUES (22, 1, 'Belize', 'BLZ', 'BZ');
INSERT INTO `mos_{vm}_country` VALUES (23, 1, 'Benin', 'BEN', 'BJ');
INSERT INTO `mos_{vm}_country` VALUES (24, 1, 'Bermuda', 'BMU', 'BM');
INSERT INTO `mos_{vm}_country` VALUES (25, 1, 'Bhutan', 'BTN', 'BT');
INSERT INTO `mos_{vm}_country` VALUES (26, 1, 'Bolivia', 'BOL', 'BO');
INSERT INTO `mos_{vm}_country` VALUES (27, 1, 'Bosnia and Herzegowina', 'BIH', 'BA');
INSERT INTO `mos_{vm}_country` VALUES (28, 1, 'Botswana', 'BWA', 'BW');
INSERT INTO `mos_{vm}_country` VALUES (29, 1, 'Bouvet Island', 'BVT', 'BV');
INSERT INTO `mos_{vm}_country` VALUES (30, 1, 'Brazil', 'BRA', 'BR');
INSERT INTO `mos_{vm}_country` VALUES (31, 1, 'British Indian Ocean Territory', 'IOT', 'IO');
INSERT INTO `mos_{vm}_country` VALUES (32, 1, 'Brunei Darussalam', 'BRN', 'BN');
INSERT INTO `mos_{vm}_country` VALUES (33, 1, 'Bulgaria', 'BGR', 'BG');
INSERT INTO `mos_{vm}_country` VALUES (34, 1, 'Burkina Faso', 'BFA', 'BF');
INSERT INTO `mos_{vm}_country` VALUES (35, 1, 'Burundi', 'BDI', 'BI');
INSERT INTO `mos_{vm}_country` VALUES (36, 1, 'Cambodia', 'KHM', 'KH');
INSERT INTO `mos_{vm}_country` VALUES (37, 1, 'Cameroon', 'CMR', 'CM');
INSERT INTO `mos_{vm}_country` VALUES (38, 1, 'Canada', 'CAN', 'CA');
INSERT INTO `mos_{vm}_country` VALUES (39, 1, 'Cape Verde', 'CPV', 'CV');
INSERT INTO `mos_{vm}_country` VALUES (40, 1, 'Cayman Islands', 'CYM', 'KY');
INSERT INTO `mos_{vm}_country` VALUES (41, 1, 'Central African Republic', 'CAF', 'CF');
INSERT INTO `mos_{vm}_country` VALUES (42, 1, 'Chad', 'TCD', 'TD');
INSERT INTO `mos_{vm}_country` VALUES (43, 1, 'Chile', 'CHL', 'CL');
INSERT INTO `mos_{vm}_country` VALUES (44, 1, 'China', 'CHN', 'CN');
INSERT INTO `mos_{vm}_country` VALUES (45, 1, 'Christmas Island', 'CXR', 'CX');
INSERT INTO `mos_{vm}_country` VALUES (46, 1, 'Cocos (Keeling) Islands', 'CCK', 'CC');
INSERT INTO `mos_{vm}_country` VALUES (47, 1, 'Colombia', 'COL', 'CO');
INSERT INTO `mos_{vm}_country` VALUES (48, 1, 'Comoros', 'COM', 'KM');
INSERT INTO `mos_{vm}_country` VALUES (49, 1, 'Congo', 'COG', 'CG');
INSERT INTO `mos_{vm}_country` VALUES (50, 1, 'Cook Islands', 'COK', 'CK');
INSERT INTO `mos_{vm}_country` VALUES (51, 1, 'Costa Rica', 'CRI', 'CR');
INSERT INTO `mos_{vm}_country` VALUES (52, 1, 'Cote D\'Ivoire', 'CIV', 'CI');
INSERT INTO `mos_{vm}_country` VALUES (53, 1, 'Croatia', 'HRV', 'HR');
INSERT INTO `mos_{vm}_country` VALUES (54, 1, 'Cuba', 'CUB', 'CU');
INSERT INTO `mos_{vm}_country` VALUES (55, 1, 'Cyprus', 'CYP', 'CY');
INSERT INTO `mos_{vm}_country` VALUES (56, 1, 'Czech Republic', 'CZE', 'CZ');
INSERT INTO `mos_{vm}_country` VALUES (57, 1, 'Denmark', 'DNK', 'DK');
INSERT INTO `mos_{vm}_country` VALUES (58, 1, 'Djibouti', 'DJI', 'DJ');
INSERT INTO `mos_{vm}_country` VALUES (59, 1, 'Dominica', 'DMA', 'DM');
INSERT INTO `mos_{vm}_country` VALUES (60, 1, 'Dominican Republic', 'DOM', 'DO');
INSERT INTO `mos_{vm}_country` VALUES (61, 1, 'East Timor', 'TMP', 'TP');
INSERT INTO `mos_{vm}_country` VALUES (62, 1, 'Ecuador', 'ECU', 'EC');
INSERT INTO `mos_{vm}_country` VALUES (63, 1, 'Egypt', 'EGY', 'EG');
INSERT INTO `mos_{vm}_country` VALUES (64, 1, 'El Salvador', 'SLV', 'SV');
INSERT INTO `mos_{vm}_country` VALUES (65, 1, 'Equatorial Guinea', 'GNQ', 'GQ');
INSERT INTO `mos_{vm}_country` VALUES (66, 1, 'Eritrea', 'ERI', 'ER');
INSERT INTO `mos_{vm}_country` VALUES (67, 1, 'Estonia', 'EST', 'EE');
INSERT INTO `mos_{vm}_country` VALUES (68, 1, 'Ethiopia', 'ETH', 'ET');
INSERT INTO `mos_{vm}_country` VALUES (69, 1, 'Falkland Islands (Malvinas)', 'FLK', 'FK');
INSERT INTO `mos_{vm}_country` VALUES (70, 1, 'Faroe Islands', 'FRO', 'FO');
INSERT INTO `mos_{vm}_country` VALUES (71, 1, 'Fiji', 'FJI', 'FJ');
INSERT INTO `mos_{vm}_country` VALUES (72, 1, 'Finland', 'FIN', 'FI');
INSERT INTO `mos_{vm}_country` VALUES (73, 1, 'France', 'FRA', 'FR');
INSERT INTO `mos_{vm}_country` VALUES (74, 1, 'France, Metropolitan', 'FXX', 'FX');
INSERT INTO `mos_{vm}_country` VALUES (75, 1, 'French Guiana', 'GUF', 'GF');
INSERT INTO `mos_{vm}_country` VALUES (76, 1, 'French Polynesia', 'PYF', 'PF');
INSERT INTO `mos_{vm}_country` VALUES (77, 1, 'French Southern Territories', 'ATF', 'TF');
INSERT INTO `mos_{vm}_country` VALUES (78, 1, 'Gabon', 'GAB', 'GA');
INSERT INTO `mos_{vm}_country` VALUES (79, 1, 'Gambia', 'GMB', 'GM');
INSERT INTO `mos_{vm}_country` VALUES (80, 1, 'Georgia', 'GEO', 'GE');
INSERT INTO `mos_{vm}_country` VALUES (81, 1, 'Germany', 'DEU', 'DE');
INSERT INTO `mos_{vm}_country` VALUES (82, 1, 'Ghana', 'GHA', 'GH');
INSERT INTO `mos_{vm}_country` VALUES (83, 1, 'Gibraltar', 'GIB', 'GI');
INSERT INTO `mos_{vm}_country` VALUES (84, 1, 'Greece', 'GRC', 'GR');
INSERT INTO `mos_{vm}_country` VALUES (85, 1, 'Greenland', 'GRL', 'GL');
INSERT INTO `mos_{vm}_country` VALUES (86, 1, 'Grenada', 'GRD', 'GD');
INSERT INTO `mos_{vm}_country` VALUES (87, 1, 'Guadeloupe', 'GLP', 'GP');
INSERT INTO `mos_{vm}_country` VALUES (88, 1, 'Guam', 'GUM', 'GU');
INSERT INTO `mos_{vm}_country` VALUES (89, 1, 'Guatemala', 'GTM', 'GT');
INSERT INTO `mos_{vm}_country` VALUES (90, 1, 'Guinea', 'GIN', 'GN');
INSERT INTO `mos_{vm}_country` VALUES (91, 1, 'Guinea-bissau', 'GNB', 'GW');
INSERT INTO `mos_{vm}_country` VALUES (92, 1, 'Guyana', 'GUY', 'GY');
INSERT INTO `mos_{vm}_country` VALUES (93, 1, 'Haiti', 'HTI', 'HT');
INSERT INTO `mos_{vm}_country` VALUES (94, 1, 'Heard and Mc Donald Islands', 'HMD', 'HM');
INSERT INTO `mos_{vm}_country` VALUES (95, 1, 'Honduras', 'HND', 'HN');
INSERT INTO `mos_{vm}_country` VALUES (96, 1, 'Hong Kong', 'HKG', 'HK');
INSERT INTO `mos_{vm}_country` VALUES (97, 1, 'Hungary', 'HUN', 'HU');
INSERT INTO `mos_{vm}_country` VALUES (98, 1, 'Iceland', 'ISL', 'IS');
INSERT INTO `mos_{vm}_country` VALUES (99, 1, 'India', 'IND', 'IN');
INSERT INTO `mos_{vm}_country` VALUES (100, 1, 'Indonesia', 'IDN', 'ID');
INSERT INTO `mos_{vm}_country` VALUES (101, 1, 'Iran (Islamic Republic of)', 'IRN', 'IR');
INSERT INTO `mos_{vm}_country` VALUES (102, 1, 'Iraq', 'IRQ', 'IQ');
INSERT INTO `mos_{vm}_country` VALUES (103, 1, 'Ireland', 'IRL', 'IE');
INSERT INTO `mos_{vm}_country` VALUES (104, 1, 'Israel', 'ISR', 'IL');
INSERT INTO `mos_{vm}_country` VALUES (105, 1, 'Italy', 'ITA', 'IT');
INSERT INTO `mos_{vm}_country` VALUES (106, 1, 'Jamaica', 'JAM', 'JM');
INSERT INTO `mos_{vm}_country` VALUES (107, 1, 'Japan', 'JPN', 'JP');
INSERT INTO `mos_{vm}_country` VALUES (108, 1, 'Jordan', 'JOR', 'JO');
INSERT INTO `mos_{vm}_country` VALUES (109, 1, 'Kazakhstan', 'KAZ', 'KZ');
INSERT INTO `mos_{vm}_country` VALUES (110, 1, 'Kenya', 'KEN', 'KE');
INSERT INTO `mos_{vm}_country` VALUES (111, 1, 'Kiribati', 'KIR', 'KI');
INSERT INTO `mos_{vm}_country` VALUES (112, 1, 'Korea, Democratic People\'s Republic of', 'PRK', 'KP');
INSERT INTO `mos_{vm}_country` VALUES (113, 1, 'Korea, Republic of', 'KOR', 'KR');
INSERT INTO `mos_{vm}_country` VALUES (114, 1, 'Kuwait', 'KWT', 'KW');
INSERT INTO `mos_{vm}_country` VALUES (115, 1, 'Kyrgyzstan', 'KGZ', 'KG');
INSERT INTO `mos_{vm}_country` VALUES (116, 1, 'Lao People\'s Democratic Republic', 'LAO', 'LA');
INSERT INTO `mos_{vm}_country` VALUES (117, 1, 'Latvia', 'LVA', 'LV');
INSERT INTO `mos_{vm}_country` VALUES (118, 1, 'Lebanon', 'LBN', 'LB');
INSERT INTO `mos_{vm}_country` VALUES (119, 1, 'Lesotho', 'LSO', 'LS');
INSERT INTO `mos_{vm}_country` VALUES (120, 1, 'Liberia', 'LBR', 'LR');
INSERT INTO `mos_{vm}_country` VALUES (121, 1, 'Libyan Arab Jamahiriya', 'LBY', 'LY');
INSERT INTO `mos_{vm}_country` VALUES (122, 1, 'Liechtenstein', 'LIE', 'LI');
INSERT INTO `mos_{vm}_country` VALUES (123, 1, 'Lithuania', 'LTU', 'LT');
INSERT INTO `mos_{vm}_country` VALUES (124, 1, 'Luxembourg', 'LUX', 'LU');
INSERT INTO `mos_{vm}_country` VALUES (125, 1, 'Macau', 'MAC', 'MO');
INSERT INTO `mos_{vm}_country` VALUES (126, 1, 'Macedonia, The Former Yugoslav Republic of', 'MKD', 'MK');
INSERT INTO `mos_{vm}_country` VALUES (127, 1, 'Madagascar', 'MDG', 'MG');
INSERT INTO `mos_{vm}_country` VALUES (128, 1, 'Malawi', 'MWI', 'MW');
INSERT INTO `mos_{vm}_country` VALUES (129, 1, 'Malaysia', 'MYS', 'MY');
INSERT INTO `mos_{vm}_country` VALUES (130, 1, 'Maldives', 'MDV', 'MV');
INSERT INTO `mos_{vm}_country` VALUES (131, 1, 'Mali', 'MLI', 'ML');
INSERT INTO `mos_{vm}_country` VALUES (132, 1, 'Malta', 'MLT', 'MT');
INSERT INTO `mos_{vm}_country` VALUES (133, 1, 'Marshall Islands', 'MHL', 'MH');
INSERT INTO `mos_{vm}_country` VALUES (134, 1, 'Martinique', 'MTQ', 'MQ');
INSERT INTO `mos_{vm}_country` VALUES (135, 1, 'Mauritania', 'MRT', 'MR');
INSERT INTO `mos_{vm}_country` VALUES (136, 1, 'Mauritius', 'MUS', 'MU');
INSERT INTO `mos_{vm}_country` VALUES (137, 1, 'Mayotte', 'MYT', 'YT');
INSERT INTO `mos_{vm}_country` VALUES (138, 1, 'Mexico', 'MEX', 'MX');
INSERT INTO `mos_{vm}_country` VALUES (139, 1, 'Micronesia, Federated States of', 'FSM', 'FM');
INSERT INTO `mos_{vm}_country` VALUES (140, 1, 'Moldova, Republic of', 'MDA', 'MD');
INSERT INTO `mos_{vm}_country` VALUES (141, 1, 'Monaco', 'MCO', 'MC');
INSERT INTO `mos_{vm}_country` VALUES (142, 1, 'Mongolia', 'MNG', 'MN');
INSERT INTO `mos_{vm}_country` VALUES (143, 1, 'Montserrat', 'MSR', 'MS');
INSERT INTO `mos_{vm}_country` VALUES (144, 1, 'Morocco', 'MAR', 'MA');
INSERT INTO `mos_{vm}_country` VALUES (145, 1, 'Mozambique', 'MOZ', 'MZ');
INSERT INTO `mos_{vm}_country` VALUES (146, 1, 'Myanmar', 'MMR', 'MM');
INSERT INTO `mos_{vm}_country` VALUES (147, 1, 'Namibia', 'NAM', 'NA');
INSERT INTO `mos_{vm}_country` VALUES (148, 1, 'Nauru', 'NRU', 'NR');
INSERT INTO `mos_{vm}_country` VALUES (149, 1, 'Nepal', 'NPL', 'NP');
INSERT INTO `mos_{vm}_country` VALUES (150, 1, 'Netherlands', 'NLD', 'NL');
INSERT INTO `mos_{vm}_country` VALUES (151, 1, 'Netherlands Antilles', 'ANT', 'AN');
INSERT INTO `mos_{vm}_country` VALUES (152, 1, 'New Caledonia', 'NCL', 'NC');
INSERT INTO `mos_{vm}_country` VALUES (153, 1, 'New Zealand', 'NZL', 'NZ');
INSERT INTO `mos_{vm}_country` VALUES (154, 1, 'Nicaragua', 'NIC', 'NI');
INSERT INTO `mos_{vm}_country` VALUES (155, 1, 'Niger', 'NER', 'NE');
INSERT INTO `mos_{vm}_country` VALUES (156, 1, 'Nigeria', 'NGA', 'NG');
INSERT INTO `mos_{vm}_country` VALUES (157, 1, 'Niue', 'NIU', 'NU');
INSERT INTO `mos_{vm}_country` VALUES (158, 1, 'Norfolk Island', 'NFK', 'NF');
INSERT INTO `mos_{vm}_country` VALUES (159, 1, 'Northern Mariana Islands', 'MNP', 'MP');
INSERT INTO `mos_{vm}_country` VALUES (160, 1, 'Norway', 'NOR', 'NO');
INSERT INTO `mos_{vm}_country` VALUES (161, 1, 'Oman', 'OMN', 'OM');
INSERT INTO `mos_{vm}_country` VALUES (162, 1, 'Pakistan', 'PAK', 'PK');
INSERT INTO `mos_{vm}_country` VALUES (163, 1, 'Palau', 'PLW', 'PW');
INSERT INTO `mos_{vm}_country` VALUES (164, 1, 'Panama', 'PAN', 'PA');
INSERT INTO `mos_{vm}_country` VALUES (165, 1, 'Papua New Guinea', 'PNG', 'PG');
INSERT INTO `mos_{vm}_country` VALUES (166, 1, 'Paraguay', 'PRY', 'PY');
INSERT INTO `mos_{vm}_country` VALUES (167, 1, 'Peru', 'PER', 'PE');
INSERT INTO `mos_{vm}_country` VALUES (168, 1, 'Philippines', 'PHL', 'PH');
INSERT INTO `mos_{vm}_country` VALUES (169, 1, 'Pitcairn', 'PCN', 'PN');
INSERT INTO `mos_{vm}_country` VALUES (170, 1, 'Poland', 'POL', 'PL');
INSERT INTO `mos_{vm}_country` VALUES (171, 1, 'Portugal', 'PRT', 'PT');
INSERT INTO `mos_{vm}_country` VALUES (172, 1, 'Puerto Rico', 'PRI', 'PR');
INSERT INTO `mos_{vm}_country` VALUES (173, 1, 'Qatar', 'QAT', 'QA');
INSERT INTO `mos_{vm}_country` VALUES (174, 1, 'Reunion', 'REU', 'RE');
INSERT INTO `mos_{vm}_country` VALUES (175, 1, 'Romania', 'ROM', 'RO');
INSERT INTO `mos_{vm}_country` VALUES (176, 1, 'Russian Federation', 'RUS', 'RU');
INSERT INTO `mos_{vm}_country` VALUES (177, 1, 'Rwanda', 'RWA', 'RW');
INSERT INTO `mos_{vm}_country` VALUES (178, 1, 'Saint Kitts and Nevis', 'KNA', 'KN');
INSERT INTO `mos_{vm}_country` VALUES (179, 1, 'Saint Lucia', 'LCA', 'LC');
INSERT INTO `mos_{vm}_country` VALUES (180, 1, 'Saint Vincent and the Grenadines', 'VCT', 'VC');
INSERT INTO `mos_{vm}_country` VALUES (181, 1, 'Samoa', 'WSM', 'WS');
INSERT INTO `mos_{vm}_country` VALUES (182, 1, 'San Marino', 'SMR', 'SM');
INSERT INTO `mos_{vm}_country` VALUES (183, 1, 'Sao Tome and Principe', 'STP', 'ST');
INSERT INTO `mos_{vm}_country` VALUES (184, 1, 'Saudi Arabia', 'SAU', 'SA');
INSERT INTO `mos_{vm}_country` VALUES (185, 1, 'Senegal', 'SEN', 'SN');
INSERT INTO `mos_{vm}_country` VALUES (186, 1, 'Seychelles', 'SYC', 'SC');
INSERT INTO `mos_{vm}_country` VALUES (187, 1, 'Sierra Leone', 'SLE', 'SL');
INSERT INTO `mos_{vm}_country` VALUES (188, 1, 'Singapore', 'SGP', 'SG');
INSERT INTO `mos_{vm}_country` VALUES (189, 1, 'Slovakia (Slovak Republic)', 'SVK', 'SK');
INSERT INTO `mos_{vm}_country` VALUES (190, 1, 'Slovenia', 'SVN', 'SI');
INSERT INTO `mos_{vm}_country` VALUES (191, 1, 'Solomon Islands', 'SLB', 'SB');
INSERT INTO `mos_{vm}_country` VALUES (192, 1, 'Somalia', 'SOM', 'SO');
INSERT INTO `mos_{vm}_country` VALUES (193, 1, 'South Africa', 'ZAF', 'ZA');
INSERT INTO `mos_{vm}_country` VALUES (194, 1, 'South Georgia and the South Sandwich Islands', 'SGS', 'GS');
INSERT INTO `mos_{vm}_country` VALUES (195, 1, 'Spain', 'ESP', 'ES');
INSERT INTO `mos_{vm}_country` VALUES (196, 1, 'Sri Lanka', 'LKA', 'LK');
INSERT INTO `mos_{vm}_country` VALUES (197, 1, 'St. Helena', 'SHN', 'SH');
INSERT INTO `mos_{vm}_country` VALUES (198, 1, 'St. Pierre and Miquelon', 'SPM', 'PM');
INSERT INTO `mos_{vm}_country` VALUES (199, 1, 'Sudan', 'SDN', 'SD');
INSERT INTO `mos_{vm}_country` VALUES (200, 1, 'Suriname', 'SUR', 'SR');
INSERT INTO `mos_{vm}_country` VALUES (201, 1, 'Svalbard and Jan Mayen Islands', 'SJM', 'SJ');
INSERT INTO `mos_{vm}_country` VALUES (202, 1, 'Swaziland', 'SWZ', 'SZ');
INSERT INTO `mos_{vm}_country` VALUES (203, 1, 'Sweden', 'SWE', 'SE');
INSERT INTO `mos_{vm}_country` VALUES (204, 1, 'Switzerland', 'CHE', 'CH');
INSERT INTO `mos_{vm}_country` VALUES (205, 1, 'Syrian Arab Republic', 'SYR', 'SY');
INSERT INTO `mos_{vm}_country` VALUES (206, 1, 'Taiwan', 'TWN', 'TW');
INSERT INTO `mos_{vm}_country` VALUES (207, 1, 'Tajikistan', 'TJK', 'TJ');
INSERT INTO `mos_{vm}_country` VALUES (208, 1, 'Tanzania, United Republic of', 'TZA', 'TZ');
INSERT INTO `mos_{vm}_country` VALUES (209, 1, 'Thailand', 'THA', 'TH');
INSERT INTO `mos_{vm}_country` VALUES (210, 1, 'Togo', 'TGO', 'TG');
INSERT INTO `mos_{vm}_country` VALUES (211, 1, 'Tokelau', 'TKL', 'TK');
INSERT INTO `mos_{vm}_country` VALUES (212, 1, 'Tonga', 'TON', 'TO');
INSERT INTO `mos_{vm}_country` VALUES (213, 1, 'Trinidad and Tobago', 'TTO', 'TT');
INSERT INTO `mos_{vm}_country` VALUES (214, 1, 'Tunisia', 'TUN', 'TN');
INSERT INTO `mos_{vm}_country` VALUES (215, 1, 'Turkey', 'TUR', 'TR');
INSERT INTO `mos_{vm}_country` VALUES (216, 1, 'Turkmenistan', 'TKM', 'TM');
INSERT INTO `mos_{vm}_country` VALUES (217, 1, 'Turks and Caicos Islands', 'TCA', 'TC');
INSERT INTO `mos_{vm}_country` VALUES (218, 1, 'Tuvalu', 'TUV', 'TV');
INSERT INTO `mos_{vm}_country` VALUES (219, 1, 'Uganda', 'UGA', 'UG');
INSERT INTO `mos_{vm}_country` VALUES (220, 1, 'Ukraine', 'UKR', 'UA');
INSERT INTO `mos_{vm}_country` VALUES (221, 1, 'United Arab Emirates', 'ARE', 'AE');
INSERT INTO `mos_{vm}_country` VALUES (222, 1, 'United Kingdom', 'GBR', 'GB');
INSERT INTO `mos_{vm}_country` VALUES (223, 1, 'United States', 'USA', 'US');
INSERT INTO `mos_{vm}_country` VALUES (224, 1, 'United States Minor Outlying Islands', 'UMI', 'UM');
INSERT INTO `mos_{vm}_country` VALUES (225, 1, 'Uruguay', 'URY', 'UY');
INSERT INTO `mos_{vm}_country` VALUES (226, 1, 'Uzbekistan', 'UZB', 'UZ');
INSERT INTO `mos_{vm}_country` VALUES (227, 1, 'Vanuatu', 'VUT', 'VU');
INSERT INTO `mos_{vm}_country` VALUES (228, 1, 'Vatican City State (Holy See)', 'VAT', 'VA');
INSERT INTO `mos_{vm}_country` VALUES (229, 1, 'Venezuela', 'VEN', 'VE');
INSERT INTO `mos_{vm}_country` VALUES (230, 1, 'Viet Nam', 'VNM', 'VN');
INSERT INTO `mos_{vm}_country` VALUES (231, 1, 'Virgin Islands (British)', 'VGB', 'VG');
INSERT INTO `mos_{vm}_country` VALUES (232, 1, 'Virgin Islands (U.S.)', 'VIR', 'VI');
INSERT INTO `mos_{vm}_country` VALUES (233, 1, 'Wallis and Futuna Islands', 'WLF', 'WF');
INSERT INTO `mos_{vm}_country` VALUES (234, 1, 'Western Sahara', 'ESH', 'EH');
INSERT INTO `mos_{vm}_country` VALUES (235, 1, 'Yemen', 'YEM', 'YE');
INSERT INTO `mos_{vm}_country` VALUES (236, 1, 'Yugoslavia', 'YUG', 'YU');
INSERT INTO `mos_{vm}_country` VALUES (237, 1, 'Zaire', 'ZAR', 'ZR');
INSERT INTO `mos_{vm}_country` VALUES (238, 1, 'Zambia', 'ZMB', 'ZM');
INSERT INTO `mos_{vm}_country` VALUES (239, 1, 'Zimbabwe', 'ZWE', 'ZW');

DROP TABLE IF EXISTS `mos_{vm}_csv`;
CREATE TABLE `mos_{vm}_csv` (
  csv_product_sku int(2) default NULL,
  csv_product_s_desc int(2) default NULL,
  csv_product_desc int(2) default NULL,
  csv_product_thumb_image int(2) default NULL,
  csv_product_full_image int(2) default NULL,
  csv_product_weight int(2) default NULL,
  csv_product_weight_uom int(2) default NULL,
  csv_product_length int(2) default NULL,
  csv_product_width int(2) default NULL,
  csv_product_height int(2) default NULL,
  csv_product_lwh_uom int(2) default NULL,
  csv_product_in_stock int(2) default NULL,
  csv_product_available_date int(2) default NULL,
  csv_product_special int(2) default NULL,
  csv_product_discount_id int(2) default NULL,
  csv_product_name int(2) default NULL,
  csv_product_price int(2) default NULL,
  csv_category_path int(2) default NULL,
  csv_manufacturer_id int(2) default NULL
) TYPE=MyISAM;
INSERT INTO `mos_{vm}_csv` VALUES (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19);

DROP TABLE IF EXISTS `mos_{vm}_currency`;
CREATE TABLE `mos_{vm}_currency` (
  `currency_id` int(11) NOT NULL auto_increment,
  `currency_name` varchar(64) default NULL,
  `currency_code` char(3) default NULL,
  PRIMARY KEY  (`currency_id`),
  KEY `idx_currency_name` (`currency_name`)
) TYPE=MyISAM AUTO_INCREMENT=157 ;
INSERT INTO `mos_{vm}_currency` VALUES (1,'Andorran Peseta','ADP');
INSERT INTO `mos_{vm}_currency` VALUES (2,'United Arab Emirates Dirham','AED');
INSERT INTO `mos_{vm}_currency` VALUES (3,'Afghanistan Afghani','AFA');
INSERT INTO `mos_{vm}_currency` VALUES (4,'Albanian Lek','ALL');
INSERT INTO `mos_{vm}_currency` VALUES (5,'Netherlands Antillian Guilder','ANG');
INSERT INTO `mos_{vm}_currency` VALUES (6,'Angolan Kwanza','AOK');
INSERT INTO `mos_{vm}_currency` VALUES (7,'Argentinian Austral','ARA');
INSERT INTO `mos_{vm}_currency` VALUES (9,'Australian Dollar','AUD');
INSERT INTO `mos_{vm}_currency` VALUES (10,'Aruban Florin','AWG');
INSERT INTO `mos_{vm}_currency` VALUES (11,'Barbados Dollar','BBD');
INSERT INTO `mos_{vm}_currency` VALUES (12,'Bangladeshi Taka','BDT');
INSERT INTO `mos_{vm}_currency` VALUES (14,'Bulgarian Lev','BGL');
INSERT INTO `mos_{vm}_currency` VALUES (15,'Bahraini Dinar','BHD');
INSERT INTO `mos_{vm}_currency` VALUES (16,'Burundi Franc','BIF');
INSERT INTO `mos_{vm}_currency` VALUES (17,'Bermudian Dollar','BMD');
INSERT INTO `mos_{vm}_currency` VALUES (18,'Brunei Dollar','BND');
INSERT INTO `mos_{vm}_currency` VALUES (19,'Bolivian Boliviano','BOB');
INSERT INTO `mos_{vm}_currency` VALUES (20,'Brazilian Cruzeiro','BRC');
INSERT INTO `mos_{vm}_currency` VALUES (21,'Bahamian Dollar','BSD');
INSERT INTO `mos_{vm}_currency` VALUES (22,'Bhutan Ngultrum','BTN');
INSERT INTO `mos_{vm}_currency` VALUES (23,'Burma Kyat','BUK');
INSERT INTO `mos_{vm}_currency` VALUES (24,'Botswanian Pula','BWP');
INSERT INTO `mos_{vm}_currency` VALUES (25,'Belize Dollar','BZD');
INSERT INTO `mos_{vm}_currency` VALUES (26,'Canadian Dollar','CAD');
INSERT INTO `mos_{vm}_currency` VALUES (27,'Swiss Franc','CHF');
INSERT INTO `mos_{vm}_currency` VALUES (28,'Chilean Unidades de Fomento','CLF');
INSERT INTO `mos_{vm}_currency` VALUES (29,'Chilean Peso','CLP');
INSERT INTO `mos_{vm}_currency` VALUES (30,'Yuan (Chinese) Renminbi','CNY');
INSERT INTO `mos_{vm}_currency` VALUES (31,'Colombian Peso','COP');
INSERT INTO `mos_{vm}_currency` VALUES (32,'Costa Rican Colon','CRC');
INSERT INTO `mos_{vm}_currency` VALUES (33,'Czech Koruna','CSK');
INSERT INTO `mos_{vm}_currency` VALUES (34,'Cuban Peso','CUP');
INSERT INTO `mos_{vm}_currency` VALUES (35,'Cape Verde Escudo','CVE');
INSERT INTO `mos_{vm}_currency` VALUES (36,'Cyprus Pound','CYP');
INSERT INTO `mos_{vm}_currency` VALUES (40,'Danish Krone','DKK');
INSERT INTO `mos_{vm}_currency` VALUES (41,'Dominican Peso','DOP');
INSERT INTO `mos_{vm}_currency` VALUES (42,'Algerian Dinar','DZD');
INSERT INTO `mos_{vm}_currency` VALUES (43,'Ecuador Sucre','ECS');
INSERT INTO `mos_{vm}_currency` VALUES (44,'Egyptian Pound','EGP');
INSERT INTO `mos_{vm}_currency` VALUES (46,'Ethiopian Birr','ETB');
INSERT INTO `mos_{vm}_currency` VALUES (47,'Euro','EUR');
INSERT INTO `mos_{vm}_currency` VALUES (49,'Fiji Dollar','FJD');
INSERT INTO `mos_{vm}_currency` VALUES (50,'Falkland Islands Pound','FKP');
INSERT INTO `mos_{vm}_currency` VALUES (52,'British Pound','GBP');
INSERT INTO `mos_{vm}_currency` VALUES (53,'Ghanaian Cedi','GHC');
INSERT INTO `mos_{vm}_currency` VALUES (54,'Gibraltar Pound','GIP');
INSERT INTO `mos_{vm}_currency` VALUES (55,'Gambian Dalasi','GMD');
INSERT INTO `mos_{vm}_currency` VALUES (56,'Guinea Franc','GNF');
INSERT INTO `mos_{vm}_currency` VALUES (58,'Guatemalan Quetzal','GTQ');
INSERT INTO `mos_{vm}_currency` VALUES (59,'Guinea-Bissau Peso','GWP');
INSERT INTO `mos_{vm}_currency` VALUES (60,'Guyanan Dollar','GYD');
INSERT INTO `mos_{vm}_currency` VALUES (61,'Hong Kong Dollar','HKD');
INSERT INTO `mos_{vm}_currency` VALUES (62,'Honduran Lempira','HNL');
INSERT INTO `mos_{vm}_currency` VALUES (63,'Haitian Gourde','HTG');
INSERT INTO `mos_{vm}_currency` VALUES (64,'Hungarian Forint','HUF');
INSERT INTO `mos_{vm}_currency` VALUES (65,'Indonesian Rupiah','IDR');
INSERT INTO `mos_{vm}_currency` VALUES (66,'Irish Punt','IEP');
INSERT INTO `mos_{vm}_currency` VALUES (67,'Israeli Shekel','ILS');
INSERT INTO `mos_{vm}_currency` VALUES (68,'Indian Rupee','INR');
INSERT INTO `mos_{vm}_currency` VALUES (69,'Iraqi Dinar','IQD');
INSERT INTO `mos_{vm}_currency` VALUES (70,'Iranian Rial','IRR');
INSERT INTO `mos_{vm}_currency` VALUES (73,'Jamaican Dollar','JMD');
INSERT INTO `mos_{vm}_currency` VALUES (74,'Jordanian Dinar','JOD');
INSERT INTO `mos_{vm}_currency` VALUES (75,'Japanese Yen','JPY');
INSERT INTO `mos_{vm}_currency` VALUES (76,'Kenyan Schilling','KES');
INSERT INTO `mos_{vm}_currency` VALUES (77,'Kampuchean (Cambodian) Riel','KHR');
INSERT INTO `mos_{vm}_currency` VALUES (78,'Comoros Franc','KMF');
INSERT INTO `mos_{vm}_currency` VALUES (79,'North Korean Won','KPW');
INSERT INTO `mos_{vm}_currency` VALUES (80,'(South) Korean Won','KRW');
INSERT INTO `mos_{vm}_currency` VALUES (81,'Kuwaiti Dinar','KWD');
INSERT INTO `mos_{vm}_currency` VALUES (82,'Cayman Islands Dollar','KYD');
INSERT INTO `mos_{vm}_currency` VALUES (83,'Lao Kip','LAK');
INSERT INTO `mos_{vm}_currency` VALUES (84,'Lebanese Pound','LBP');
INSERT INTO `mos_{vm}_currency` VALUES (85,'Sri Lanka Rupee','LKR');
INSERT INTO `mos_{vm}_currency` VALUES (86,'Liberian Dollar','LRD');
INSERT INTO `mos_{vm}_currency` VALUES (87,'Lesotho Loti','LSL');
INSERT INTO `mos_{vm}_currency` VALUES (89,'Libyan Dinar','LYD');
INSERT INTO `mos_{vm}_currency` VALUES (90,'Moroccan Dirham','MAD');
INSERT INTO `mos_{vm}_currency` VALUES (91,'Malagasy Franc','MGF');
INSERT INTO `mos_{vm}_currency` VALUES (92,'Mongolian Tugrik','MNT');
INSERT INTO `mos_{vm}_currency` VALUES (93,'Macau Pataca','MOP');
INSERT INTO `mos_{vm}_currency` VALUES (94,'Mauritanian Ouguiya','MRO');
INSERT INTO `mos_{vm}_currency` VALUES (95,'Maltese Lira','MTL');
INSERT INTO `mos_{vm}_currency` VALUES (96,'Mauritius Rupee','MUR');
INSERT INTO `mos_{vm}_currency` VALUES (97,'Maldive Rufiyaa','MVR');
INSERT INTO `mos_{vm}_currency` VALUES (98,'Malawi Kwacha','MWK');
INSERT INTO `mos_{vm}_currency` VALUES (99,'Mexican Peso','MXP');
INSERT INTO `mos_{vm}_currency` VALUES (100,'Malaysian Ringgit','MYR');
INSERT INTO `mos_{vm}_currency` VALUES (101,'Mozambique Metical','MZM');
INSERT INTO `mos_{vm}_currency` VALUES (102,'Nigerian Naira','NGN');
INSERT INTO `mos_{vm}_currency` VALUES (103,'Nicaraguan Cordoba','NIC');
INSERT INTO `mos_{vm}_currency` VALUES (105,'Norwegian Kroner','NOK');
INSERT INTO `mos_{vm}_currency` VALUES (106,'Nepalese Rupee','NPR');
INSERT INTO `mos_{vm}_currency` VALUES (107,'New Zealand Dollar','NZD');
INSERT INTO `mos_{vm}_currency` VALUES (108,'Omani Rial','OMR');
INSERT INTO `mos_{vm}_currency` VALUES (109,'Panamanian Balboa','PAB');
INSERT INTO `mos_{vm}_currency` VALUES (110,'Peruvian Inti','PEI');
INSERT INTO `mos_{vm}_currency` VALUES (111,'Papua New Guinea Kina','PGK');
INSERT INTO `mos_{vm}_currency` VALUES (112,'Philippine Peso','PHP');
INSERT INTO `mos_{vm}_currency` VALUES (113,'Pakistan Rupee','PKR');
INSERT INTO `mos_{vm}_currency` VALUES (114,'Polish Zloty','PLZ');
INSERT INTO `mos_{vm}_currency` VALUES (116,'Paraguay Guarani','PYG');
INSERT INTO `mos_{vm}_currency` VALUES (117,'Qatari Rial','QAR');
INSERT INTO `mos_{vm}_currency` VALUES (118,'Romanian Leu','ROL');
INSERT INTO `mos_{vm}_currency` VALUES (119,'Rwanda Franc','RWF');
INSERT INTO `mos_{vm}_currency` VALUES (120,'Saudi Arabian Riyal','SAR');
INSERT INTO `mos_{vm}_currency` VALUES (121,'Solomon Islands Dollar','SBD');
INSERT INTO `mos_{vm}_currency` VALUES (122,'Seychelles Rupee','SCR');
INSERT INTO `mos_{vm}_currency` VALUES (123,'Sudanese Pound','SDP');
INSERT INTO `mos_{vm}_currency` VALUES (124,'Swedish Krona','SEK');
INSERT INTO `mos_{vm}_currency` VALUES (125,'Singapore Dollar','SGD');
INSERT INTO `mos_{vm}_currency` VALUES (126,'St. Helena Pound','SHP');
INSERT INTO `mos_{vm}_currency` VALUES (127,'Sierra Leone Leone','SLL');
INSERT INTO `mos_{vm}_currency` VALUES (128,'Somali Schilling','SOS');
INSERT INTO `mos_{vm}_currency` VALUES (129,'Suriname Guilder','SRG');
INSERT INTO `mos_{vm}_currency` VALUES (130,'Sao Tome and Principe Dobra','STD');
INSERT INTO `mos_{vm}_currency` VALUES (131,'USSR Rouble','SUR');
INSERT INTO `mos_{vm}_currency` VALUES (132,'El Salvador Colon','SVC');
INSERT INTO `mos_{vm}_currency` VALUES (133,'Syrian Potmd','SYP');
INSERT INTO `mos_{vm}_currency` VALUES (134,'Swaziland Lilangeni','SZL');
INSERT INTO `mos_{vm}_currency` VALUES (135,'Thai Bhat','THB');
INSERT INTO `mos_{vm}_currency` VALUES (136,'Tunisian Dinar','TND');
INSERT INTO `mos_{vm}_currency` VALUES (137,'Tongan Pa\'anga','TOP');
INSERT INTO `mos_{vm}_currency` VALUES (138,'East Timor Escudo','TPE');
INSERT INTO `mos_{vm}_currency` VALUES (139,'Turkish Lira','TRL');
INSERT INTO `mos_{vm}_currency` VALUES (140,'Trinidad and Tobago Dollar','TTD');
INSERT INTO `mos_{vm}_currency` VALUES (141,'Taiwan Dollar','TWD');
INSERT INTO `mos_{vm}_currency` VALUES (142,'Tanzanian Schilling','TZS');
INSERT INTO `mos_{vm}_currency` VALUES (143,'Uganda Shilling','UGS');
INSERT INTO `mos_{vm}_currency` VALUES (144,'US Dollar','USD');
INSERT INTO `mos_{vm}_currency` VALUES (145,'Uruguayan Peso','UYP');
INSERT INTO `mos_{vm}_currency` VALUES (146,'Venezualan Bolivar','VEB');
INSERT INTO `mos_{vm}_currency` VALUES (147,'Vietnamese Dong','VND');
INSERT INTO `mos_{vm}_currency` VALUES (148,'Vanuatu Vatu','VUV');
INSERT INTO `mos_{vm}_currency` VALUES (149,'Samoan Tala','WST');
INSERT INTO `mos_{vm}_currency` VALUES (150,'Democratic Yemeni Dinar','YDD');
INSERT INTO `mos_{vm}_currency` VALUES (151,'Yemeni Rial','YER');
INSERT INTO `mos_{vm}_currency` VALUES (152,'New Yugoslavia Dinar','YUD');
INSERT INTO `mos_{vm}_currency` VALUES (153,'South African Rand','ZAR');
INSERT INTO `mos_{vm}_currency` VALUES (154,'Zambian Kwacha','ZMK');
INSERT INTO `mos_{vm}_currency` VALUES (155,'Zaire Zaire','ZRZ');
INSERT INTO `mos_{vm}_currency` VALUES (156,'Zimbabwe Dollar','ZWD');

DROP TABLE IF EXISTS `mos_{vm}_function`;
CREATE TABLE `mos_{vm}_function` (
  `function_id` int(11) NOT NULL auto_increment,
  `module_id` int(11) default NULL,
  `function_name` varchar(32) default NULL,
  `function_class` varchar(32) default NULL,
  `function_method` varchar(32) default NULL,
  `function_description` text,
  `function_perms` varchar(255) default NULL,
  PRIMARY KEY  (`function_id`),
  KEY `idx_function_module_id` (`module_id`),
  KEY `idx_function_name` (`function_name`)
) TYPE=MyISAM AUTO_INCREMENT=110 ;
INSERT INTO `mos_{vm}_function` VALUES (1, 1, 'userAdd', 'ps_user', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (2, 1, 'userDelete', 'ps_user', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (3, 1, 'userUpdate', 'ps_user', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (4, 1, 'adminPasswdUpdate', 'ps_user', 'update_admin_passwd', 'Updates Site Administrator Password', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (31, 2, 'productAdd', 'ps_product', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (6, 1, 'functionAdd', 'ps_function', 'add', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (7, 1, 'functionUpdate', 'ps_function', 'update', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (8, 1, 'functionDelete', 'ps_function', 'delete', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (9, 1, 'userLogout', 'ps_user', 'logout', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (10, 1, 'userAddressAdd', 'ps_user_address', 'add', '', 'admin,storeadmin,shopper,demo');
INSERT INTO `mos_{vm}_function` VALUES (11, 1, 'userAddressUpdate', 'ps_user_address', 'update', '', 'admin,storeadmin,shopper');
INSERT INTO `mos_{vm}_function` VALUES (12, 1, 'userAddressDelete', 'ps_user_address', 'delete', '', 'admin,storeadmin,shopper');
INSERT INTO `mos_{vm}_function` VALUES (13, 1, 'moduleAdd', 'ps_module', 'add', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (14, 1, 'moduleUpdate', 'ps_module', 'update', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (15, 1, 'moduleDelete', 'ps_module', 'delete', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (16, 1, 'userLogin', 'ps_user', 'login', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (17, 3, 'vendorAdd', 'ps_vendor', 'add', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (18, 3, 'vendorUpdate', 'ps_vendor', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (19, 3, 'vendorDelete', 'ps_vendor', 'delete', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (20, 3, 'vendorCategoryAdd', 'ps_vendor_category', 'add', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (21, 3, 'vendorCategoryUpdate', 'ps_vendor_category', 'update', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (22, 3, 'vendorCategoryDelete', 'ps_vendor_category', 'delete', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES (23, 4, 'shopperAdd', 'ps_shopper', 'add', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (24, 4, 'shopperDelete', 'ps_shopper', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (25, 4, 'shopperUpdate', 'ps_shopper', 'update', '', 'admin,storeadmin,shopper');
INSERT INTO `mos_{vm}_function` VALUES (26, 4, 'shopperGroupAdd', 'ps_shopper_group', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (27, 4, 'shopperGroupUpdate', 'ps_shopper_group', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (28, 4, 'shopperGroupDelete', 'ps_shopper_group', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (29, 5, 'orderSearch', 'ps_order', 'find', '', 'admin,storeadmin,demo');
INSERT INTO `mos_{vm}_function` VALUES (30, 5, 'orderStatusSet', 'ps_order', 'order_status_update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (32, 2, 'productDelete', 'ps_product', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (33, 2, 'productUpdate', 'ps_product', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (34, 2, 'productCategoryAdd', 'ps_product_category', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (35, 2, 'productCategoryUpdate', 'ps_product_category', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (36, 2, 'productCategoryDelete', 'ps_product_category', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (37, 2, 'productPriceAdd', 'ps_product_price', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (38, 2, 'productPriceUpdate', 'ps_product_price', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (39, 2, 'productPriceDelete', 'ps_product_price', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (40, 2, 'productAttributeAdd', 'ps_product_attribute', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (41, 2, 'productAttributeUpdate', 'ps_product_attribute', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (42, 2, 'productAttributeDelete', 'ps_product_attribute', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (43, 7, 'cartAdd', 'ps_cart', 'add', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (44, 7, 'cartUpdate', 'ps_cart', 'update', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (45, 7, 'cartDelete', 'ps_cart', 'delete', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (46, 10, 'checkoutComplete', 'ps_checkout', 'add', '', 'shopper,storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (47, 1, 'setLanguage', 'ps_module', 'set_language', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (48, 8, 'paymentMethodUpdate', 'ps_payment_method', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (49, 8, 'paymentMethodAdd', 'ps_payment_method', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (50, 8, 'paymentMethodDelete', 'ps_payment_method', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (51, 5, 'orderDelete', 'ps_order', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (52, 11, 'addTaxRate', 'ps_tax', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (53, 11, 'updateTaxRate', 'ps_tax', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (54, 11, 'deleteTaxRate', 'ps_tax', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (55, 10, 'checkoutValidateST', 'ps_checkout', 'validate_shipto', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES (59, 5, 'orderStatusUpdate', 'ps_order_status', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (60, 5, 'orderStatusAdd', 'ps_order_status', 'add', '', 'storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (61, 5, 'orderStatusDelete', 'ps_order_status', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES (62, 1, 'currencyAdd', 'ps_currency', 'add', 'add a currency', 'storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (63, 1, 'currencyUpdate', 'ps_currency', 'update', '        update a currency', 'storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (64, 1, 'currencyDelete', 'ps_currency', 'delete', 'delete a currency', 'storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (65, 1, 'countryAdd', 'ps_country', 'add', 'Add a country ', 'storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (66, 1, 'countryUpdate', 'ps_country', 'update', 'Update a country record', 'storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (67, 1, 'countryDelete', 'ps_country', 'delete', 'Delete a country record', 'storeadmin,admin');
INSERT INTO `mos_{vm}_function` VALUES (68, 2, 'product_csv', 'ps_csv', 'upload_csv', '', 'admin');
INSERT INTO `mos_{vm}_function` VALUES ('', 7, 'waitingListAdd', 'zw_waiting_list', 'add', '', 'none');
INSERT INTO `mos_{vm}_function` VALUES ('', 13, 'addzone', 'ps_zone', 'add', 'This will add a zone', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', 13, 'updatezone', 'ps_zone', 'update', 'This will update a zone', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', 13, 'deletezone', 'ps_zone', 'delete', 'This will delete a zone', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', 13, 'zoneassign', 'ps_zone', 'assign', 'This will assign a country to a zone', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', 1, 'writeConfig', 'ps_config', 'writeconfig', 'This will write the configuration details to virtuemart.cfg.php', 'admin');
INSERT INTO `mos_{vm}_function` VALUES ('', '12839', 'carrierAdd', 'ps_shipping', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '12839', 'carrierDelete', 'ps_shipping', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '12839', 'carrierUpdate', 'ps_shipping', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '12839', 'rateAdd', 'ps_shipping', 'rate_add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '12839', 'rateUpdate', 'ps_shipping', 'rate_update', '', 'admin,shopadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '12839', 'rateDelete', 'ps_shipping', 'rate_delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '10', 'checkoutProcess', 'ps_checkout', 'process', '', 'shopper,storeadmin,admin,demo');
INSERT INTO `mos_{vm}_function` VALUES ('', '5', 'downloadRequest', 'ps_order', 'download_request', 'This checks if the download request is valid and sends the file to the browser as file download if the request was successful, otherwise echoes an error', 'admin,storeadmin,shopper');
INSERT INTO `mos_{vm}_function` VALUES ('', '98', 'affiliateAdd', 'ps_affiliate', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '98', 'affiliateUpdate', 'ps_affiliate', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '98', 'affiliateDelete', 'ps_affiliate', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '98', 'affiliateEmail', 'ps_affiliate', 'email', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '99', 'manufacturerAdd', 'ps_manufacturer', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '99', 'manufacturerUpdate', 'ps_manufacturer', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '99', 'manufacturerDelete', 'ps_manufacturer', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '99', 'manufacturercategoryAdd', 'ps_manufacturer_category', 'add', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '99', 'manufacturercategoryUpdate', 'ps_manufacturer_category', 'update', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '99', 'manufacturercategoryDelete', 'ps_manufacturer_category', 'delete', '', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '7', 'addReview', 'ps_reviews', 'process_review', 'This lets the user add a review and rating to a product.', 'admin,storeadmin,shopper,demo');
INSERT INTO `mos_{vm}_function` VALUES ('', '8', 'creditcardAdd', 'ps_creditcard', 'add', 'Adds a Credit Card entry.', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '8', 'creditcardUpdate', 'ps_creditcard', 'update', 'Updates a Credit Card entry.', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '8', 'creditcardDelete', 'ps_creditcard', 'delete', 'Deletes a Credit Card entry.', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '2', 'publishProduct', 'ps_product', 'product_publish', 'Changes the product_publish field, so that a product can be published or unpublished easily.', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '2', 'export_csv', 'ps_csv', 'export_csv', 'This function exports all relevant product data to CSV.', 'admin,storeadmin');
INSERT INTO `mos_{vm}_function` VALUES ('', '2', 'reorder', 'ps_product_category', 'reorder', 'Changes the list order of a category.', 'admin,storeadmin');

DROP TABLE IF EXISTS `mos_{vm}_manufacturer`;
CREATE TABLE `mos_{vm}_manufacturer` (
    `manufacturer_id` int(11) NOT NULL auto_increment,
    `mf_name` varchar(64) default NULL,
    `mf_email` varchar(255) default NULL,
    `mf_desc` text,
    `mf_category_id` int(11) default NULL,
    `mf_url` VARCHAR( 255 ) NOT NULL,
    PRIMARY KEY  (`manufacturer_id`)
  ) TYPE=MyISAM;
INSERT INTO `mos_{vm}_manufacturer` VALUES ('1', 'Manufacturer', 'info@manufacturer.com', 'A manufacturer example.', '1', 'http://www.a-url.com');

DROP TABLE IF EXISTS `mos_{vm}_manufacturer_category`;
CREATE TABLE `mos_{vm}_manufacturer_category` (
              `mf_category_id` int(11) NOT NULL auto_increment,
              `mf_category_name` varchar(64) default NULL,
              `mf_category_desc` text,
              PRIMARY KEY  (`mf_category_id`),
              KEY `idx_manufacturer_category_category_name` (`mf_category_name`)
            ) TYPE=MyISAM;
INSERT INTO `mos_{vm}_manufacturer_category` VALUES ('1', '-default-', 'This is the default manufacturer category');

DROP TABLE IF EXISTS `mos_{vm}_product_mf_xref`;
CREATE TABLE `mos_{vm}_product_mf_xref` (
              `product_id` varchar(32) default NULL,
              `manufacturer_id` int(11) default NULL,
              KEY `idx_product_mf_xref_product_id` (`product_id`),
              KEY `idx_product_mf_xref_manufacturer_id` (`manufacturer_id`)
            ) TYPE=MyISAM;
            
DROP TABLE IF EXISTS `mos_{vm}_module`;
CREATE TABLE `mos_{vm}_module` (
  `module_id` int(11) NOT NULL auto_increment,
  `module_name` varchar(255) default NULL,
  `module_description` text,
  `module_perms` varchar(255) default NULL,
  `module_header` varchar(255) default NULL,
  `module_footer` varchar(255) default NULL,
  `module_publish` char(1) default NULL,
  `list_order` int(11) default NULL,
  `language_code_1` varchar(4) default NULL,
  `language_code_2` varchar(4) default NULL,
  `language_code_3` varchar(4) default NULL,
  `language_code_4` varchar(4) default NULL,
  `language_code_5` varchar(4) default NULL,
  `language_file_1` varchar(255) default NULL,
  `language_file_2` varchar(255) default NULL,
  `language_file_3` varchar(255) default NULL,
  `language_file_4` varchar(255) default NULL,
  `language_file_5` varchar(255) default NULL,
  `module_label_1` varchar(255) default NULL,
  `module_label_2` varchar(255) default NULL,
  `module_label_3` varchar(255) default NULL,
  `module_label_4` varchar(255) default NULL,
  `module_label_5` varchar(255) default NULL,
  PRIMARY KEY  (`module_id`),
  KEY `idx_module_name` (`module_name`),
  KEY `idx_module_list_order` (`list_order`)
) TYPE=MyISAM AUTO_INCREMENT=12838 ;
INSERT INTO `mos_{vm}_module` VALUES (1, 'admin', '<h4>ADMINISTRATIVE USERS ONLY</h4>\r\n\r\n<p>Only used for the following:</p>\r\n<OL>\r\n\r\n<LI>User Maintenance</LI>\r\n<LI>Module Maintenance</LI>\r\n<LI>Function Maintenance</LI>\r\n</OL>\r\n', 'admin', 'header.ihtml', 'footer.ihtml', 'Y', 1, 'eng', 'esl', '', '', '', 'lang_eng.inc', 'lang_esl.inc', '', '', '', 'Admin', 'Admin', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (2, 'product', '<p>Here you can adminster your online catalog of products.  The Product Administrator allows you to create product categories, create new products, edit product attributes, and add product items for each attribute value.</p>', 'storeadmin,admin', 'header.ihtml', 'footer.ihtml', 'Y', 4, 'eng', 'esl', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'Products', 'Mis<br />Productos', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (3, 'vendor', '<h4>ADMINISTRATIVE USERS ONLY</h4>\r\n<p>Here you can manage the vendors on the phpShop system.</p>', 'admin', 'header.ihtml', 'footer.ihtml', 'Y', 6, 'eng', 'esl', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'Vendors', 'Los<br />Distribuidores', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (4, 'shopper', '<p>Manage shoppers in your store.  Allows you to create shopper groups.  Shopper groups can be used when setting the price for a product.  This allows you to create different prices for different types of users.  An example of this would be to have a \'wholesale\' group and a \'retail\' group. </p>', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 4, 'eng', 'esl', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'Shoppers', 'Mis<br />Clientes', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (5, 'order', '<p>View Order and Update Order Status.</p>', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 5, 'eng', 'esl', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'Orders', 'Mis<br />Ordenes', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (6, 'msgs', 'This module is unprotected an used for displaying system messages to users.  We need to have an area that does not require authorization when things go wrong.', 'none', 'header.ihtml', 'footer.ihtml', 'N', 99, 'eng', 'esl', '', '', '', 'lang_en.inc', '', '', '', '', 'Admin', '', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (7, 'shop', 'This is the Washupito store module.  This is the demo store included with the phpShop distribution.', 'none', 's_header.ihtml', 's_footer.ihtml', 'Y', 99, 'eng', 'esl', '', '', '', '', '', '', '', '', 'Shop', 'Visita<br />la Tienda', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (8, 'store', '', 'storeadmin,admin', 'header.ihtml', 'footer.ihtml', 'Y', 2, 'eng', 'esl', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'Store', 'Mi<br />Tienda', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (9, 'account', 'This module allows shoppers to update their account information and view previously placed orders.', 'shopper,storeadmin,admin,demo', 's_header.ihtml', 's_footer.ihtml', 'N', 99, 'eng', 'esl', '', '', '', '', '', '', '', '', 'Account', 'Account', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (10, 'checkout', '', 'none', 's_header.ihtml', 's_footer.ihtml', 'N', 99, 'eng', 'esl', '', '', '', '', '', '', '', '', 'Checkout', 'Checkout', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (11, 'tax', 'The tax module allows you to set tax rates for states or regions within a country.  The rate is set as a decimal figure.  For example, 2 percent tax would be 0.02.', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 8, 'eng', 'esl', '', '', '', '', '', '', '', '', 'Taxes', 'Impuestos', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (12, 'reportbasic', 'The report basic module allows you to do queries on all orders.', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 7, 'eng', 'esl', '', '', '', '', '', '', '', '', 'Report Basic', 'Report Basic', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (13, 'zone', 'This is the zone-shipping module. Here you can manage your shipping costs according to Zones.', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'N', 9, 'eng', 'esl', '', '', '', '', '', '', '', '', 'Zone Shipping', 'Zone Shipping', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES ( '12839', 'shipping', '<h4>Shipping</h4><p>Let this module calculate the shipping fees for your customers.<br>
Create carriers for shipping areas and weight groups.</p>', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', '20', 'eng', 'ger', '', '', '', '', '', '', '', '', 'Shipping', 'Versand', '', '', '');;
INSERT INTO `mos_{vm}_module` VALUES( '98', 'affiliate', 'administrate the affiliates on your store.', 'storeadmin,admin', 'header.ihtml', 'footer.ihtml', 'N', '99', 'EN', 'ES', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'affiliates', '', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES( '99', 'manufacturer', 'Manage the manufacturers of products in your store.', 'storeadmin,admin', 'header.ihtml', 'footer.ihtml', 'Y', '99', 'EN', 'ES', '', '', '', 'lang_en.inc', 'lang_es.inc', '', '', '', 'manufacturer', '', '', '', '');
INSERT INTO `mos_{vm}_module` VALUES (12842, 'help', 'Help for virtuemart', 'admin,storeadmin', 'header.ihtml', 'footer.ihtml', 'Y', 99, 'eng', '', '', '', '', '', '', '', '', '', 'Help', '', '', '', '');

DROP TABLE IF EXISTS `mos_{vm}_order_item`;
CREATE TABLE `mos_{vm}_order_item` (
  `order_item_id` int(11) NOT NULL auto_increment,
  `order_id` int(11) default NULL,
  `user_info_id` varchar(32) default NULL default NULL,
  `vendor_id` int(11) default NULL,
  `product_id` int(11) default NULL,
  `product_quantity` int(11) default NULL,
  `product_item_price` decimal(10,2) default NULL,
  `order_item_currency` varchar(16) default NULL,
  `order_status` char(1) default NULL,
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `product_attribute` text default NULL,
  PRIMARY KEY  (`order_item_id`),
  KEY `idx_order_item_order_id` (`order_id`),
  KEY `idx_order_item_user_info_id` (`user_info_id`),
  KEY `idx_order_item_vendor_id` (`vendor_id`),
  KEY `idx_order_item_product_id` (`product_id`)
) TYPE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `mos_{vm}_order_payment`;
CREATE TABLE `mos_{vm}_order_payment` (
  `order_id` int(11) NOT NULL default '0',
  `payment_method_id` int(11) default NULL,
  `order_payment_number` blob,
  `order_payment_expire` int(11) default NULL,
  `order_payment_name` varchar(255) default NULL,
  `order_payment_log` text,
  KEY `idx_order_payment_order_id` (`order_id`),
  KEY `idx_order_payment_method_id` (`payment_method_id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_order_status`;
CREATE TABLE `mos_{vm}_order_status` (
  `order_status_id` int(11) NOT NULL auto_increment,
  `order_status_code` char(1) NOT NULL default '',
  `order_status_name` varchar(64) default NULL,
  `list_order` int(11) default NULL,
  `vendor_id` int(11) default NULL,
  PRIMARY KEY  (`order_status_id`),
  KEY `idx_order_status_list_order` (`list_order`),
  KEY `idx_order_status_vendor_id` (`vendor_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;
INSERT INTO `mos_{vm}_order_status` VALUES (1, 'P', 'Pending', 1, 1);
INSERT INTO `mos_{vm}_order_status` VALUES (2, 'C', 'Confirmed', 1, 1);
INSERT INTO `mos_{vm}_order_status` VALUES (3, 'X', 'Cancelled', 3, 1);
INSERT INTO `mos_{vm}_order_status` VALUES (4, 'S', 'Shipped', 4, 1);

DROP TABLE IF EXISTS `mos_{vm}_orders`;
CREATE TABLE `mos_{vm}_orders` (
  `order_id` int(11) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `vendor_id` int(11) NOT NULL default '0',
  `order_number` varchar(32) default NULL,
  `user_info_id` varchar(32) default NULL,
  `order_total` DECIMAL( 10, 2 ) DEFAULT '0,0' NOT NULL,
  `order_subtotal` decimal(10,2) default NULL,
  `order_tax` decimal(10,2) default NULL,
  `order_shipping` decimal(10,2) default NULL,
  `order_shipping_tax` decimal(10,2) default NULL,
  `order_discount` DECIMAL( 10, 2 ) NOT NULL,
  `order_currency` varchar(16) default NULL,
  `order_status` char(1) default NULL,
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `ship_method_id` VARCHAR( 255 ) DEFAULT NULL,
  `customer_note` text NOT NULL,
  `ip_address` VARCHAR(15) NOT NULL,
  PRIMARY KEY  (`order_id`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_vendor_id` (`vendor_id`),
  KEY `idx_orders_order_number` (`order_number`),
  KEY `idx_orders_user_info_id` (`user_info_id`),
  KEY `idx_orders_ship_method_id` (`ship_method_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `mos_{vm}_payment_method`;
CREATE TABLE `mos_{vm}_payment_method` (
  `payment_method_id` int(11) NOT NULL auto_increment,
  `vendor_id` int(11) default NULL,
  `payment_method_name` varchar(255) default NULL,
  `payment_class` VARCHAR( 50 ) NOT NULL,
  `shopper_group_id` int(11) default NULL,
  `payment_method_discount` decimal(10,2) default NULL,
  `list_order` int(11) default NULL,
  `payment_method_code` varchar(8) default NULL,
  `enable_processor` char(1) default NULL,
  `is_creditcard` TINYINT( 1 ) NOT NULL,
  `payment_enabled` CHAR( 1 ) DEFAULT 'N' NOT NULL,
  `accepted_creditcards` VARCHAR( 128 ) NOT NULL,
  `payment_extrainfo` TEXT NOT NULL,
  PRIMARY KEY  (`payment_method_id`),
  KEY `idx_payment_method_vendor_id` (`vendor_id`),
  KEY `idx_payment_method_name` (`payment_method_name`),
  KEY `idx_payment_method_list_order` (`list_order`),
  KEY `idx_payment_method_shopper_group_id` (`shopper_group_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;
INSERT INTO `mos_{vm}_payment_method` VALUES (1, 1, 'Purchase Order', '', 5, '0.00', 4, 'PO', 'N', 0, 'Y', '', '');
INSERT INTO `mos_{vm}_payment_method` VALUES (2, 1, 'Cash On Delivery', '', 5, '-2.00', 5, 'COD', 'N', 0, 'Y', '', '');
INSERT INTO `mos_{vm}_payment_method` VALUES (3, 1, 'Credit Card', 'ps_authorize', 5, '0.00', 0, 'AN', 'Y', 0, 'Y', '1,2,6,7,', '');
INSERT INTO `mos_{vm}_payment_method` VALUES (4, 1, 'PayPal', 'ps_paypal', 5, '0.00', 0, 'PP', 'P', 0, 'Y', '', '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">\r\n<input type="image" name="submit" src="http://images.paypal.com/images/x-click-but6.gif" border="0" alt="Make payments with PayPal, it\'s fast, free, and secure!">\r\n<input type="hidden" name="cmd" value="_xclick" />\r\n<input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL ?>" />\r\n<input type="hidden" name="receiver_email" value="<?php echo PAYPAL_EMAIL ?>" />\r\n<input type="hidden" name="item_name" value="Order Nr. <?php $db->p("order_id") ?>" />\r\n<input type="hidden" name="invoice" value="<?php $db->p("order_number") ?>" />\r\n<input type="hidden" name="amount" value="<?php printf("%.2f", $db->f("order_total"))?>" />\r\n<input type="hidden" name="currency_code" value="<?php echo $_SESSION[\'vendor_currency\'] ?>" />\r\n<input type="hidden" name="image_url" value="<?php echo $vendor_image_url ?>" />\r\n<input type="hidden" name="return" value="<?php echo SECUREURL ."index.php?option=com_virtuemart&amp;page=checkout.result&amp;order_id=".$db->f("order_id") ?>" />\r\n<input type="hidden" name="notify_url" value="<?php echo SECUREURL ."administrator/components/com_virtuemart/notify.php" ?>" />\r\n<input type="hidden" name="cancel_return" value="<?php echo SECUREURL ."index.php" ?>" />\r\n<input type="hidden" name="undefined_quantity" value="0" />\r\n<input type="hidden" name="mrb" value="R-3WH47588B4505740X" />\r\n<input type="hidden" name="no_shipping" value="0" />\r\n<input type="hidden" name="no_note" value="1" />\r\n</form>');
INSERT INTO `mos_{vm}_payment_method` VALUES (5, 1, 'PayMate', 'ps_paymate', 5, '0.00', 0, 'PM', 'P', 0, 'Y', '', '<script language="javascript">\r\nfunction openExpress(){\r\n   var url = \'https://www.paymate.com.au/PayMate/ExpressPayment?mid=<?php echo PAYMATE_USERNAME."&amt=".$db->f("order_total")."&currency=".$_SESSION[\'vendor_currency\']."&ref=".$db->f("order_id")."&pmt_sender_email=".$dbbt->f("email");?>\'\r\n   var newWin = window.open(url, \'wizard\', \'height=580,width=500,scrollbars=1,toolbar=no\');\r\n   self.name = \'parent\';\r\n   newWin.focus();\r\n}\r\n</script>\r\n<div align="center">\r\n<p>\r\n<a href="javascript:openExpress();">\r\n<img src="https://www.paymate.com.au/images/paymate-PE-payment-88x31.gif" border="0" alt="Pay with Paymate Express"></a>\r\n<br />Pay with Paymate Express\r\n</p>\r\n</div>');
INSERT INTO `mos_{vm}_payment_method` VALUES (6, 1, 'WorldPay', 'ps_worldpay', 5, '0.00', 0, 'WP', 'P', 0, 'N', '', '<form action="https://select.worldpay.com/wcc/purchase" method="post">\r\n<input type="hidden" name="instId" value="<?php echo WORLDPAY_INST_ID ?>" />\r\n<input type="hidden" name="cartId" value="<?php echo $db->f("order_id") ?>" />\r\n<input type="hidden" name="amount" value="<?php echo $db->f("order_total") ?>" />\r\n<input type="hidden" name="currency" value="<?php echo $_SESSION[\'vendor_currency\'] ?>" />\r\n<input type="hidden" name="desc" value="" />\r\n<input type="hidden" name="email" value="<?php $dbbt->p("email"); ?>" />\r\n<input type="image" scr="http://www.ecommercetemplates.com/images/worldpay.gif" alt="WorldPay" />\r\n</form>');
INSERT INTO `mos_{vm}_payment_method` VALUES (7, 1, 'Credit Card (2Checkout)', 'ps_twocheckout', 5, '0.00', 0, '2CO', 'Y', 0, 'N', '1,2,3,', '');

DROP TABLE IF EXISTS `mos_{vm}_product`;
CREATE TABLE `mos_{vm}_product` (
  `product_id` int(11) NOT NULL auto_increment,
  `vendor_id` int(11) NOT NULL default '0',
  `product_parent_id` int(11) default '0' NOT NULL,
  `product_sku` varchar(64) NOT NULL default '',
  `product_s_desc` varchar(255) default NULL,
  `product_desc` text,
  `product_thumb_image` varchar(255) default NULL,
  `product_full_image` varchar(255) default NULL,
  `product_publish` char(1) default NULL,
  `product_weight` decimal(10,4) default NULL,
  `product_weight_uom` varchar(32) default 'pounds.',
  `product_length` decimal(10,4) default NULL,
  `product_width` decimal(10,4) default NULL,
  `product_height` decimal(10,4) default NULL,
  `product_lwh_uom` varchar(32) default 'inches',
  `product_url` varchar(255) default NULL,
  `product_in_stock` int(11) default NULL,
  `product_available_date` int(11) default NULL,
  `product_availability` VARCHAR( 56 ) NOT NULL,
  `product_special` char(1) default NULL,
  `product_discount_id` int(11) default NULL,
  `ship_code_id` int(11) default NULL,
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `product_name` varchar(64) default NULL,
  `product_sales` int(11) NOT NULL default 0,
  `attribute` text default NULL,
  `product_tax_id` TINYINT( 2 ) NOT NULL,
  PRIMARY KEY  (`product_id`),
  KEY `idx_product_vendor_id` (`vendor_id`),
  KEY `idx_product_product_parent_id` (`product_parent_id`),
  KEY `idx_product_sku` (`product_sku`),
  KEY `idx_product_ship_code_id` (`ship_code_id`),
  KEY `idx_product_name` (`product_name`)
) TYPE=MyISAM AUTO_INCREMENT=17;

DROP TABLE IF EXISTS `mos_{vm}_product_attribute`;
CREATE TABLE `mos_{vm}_product_attribute` (
  `product_id` int(11) NOT NULL default '0',
  `attribute_name` char(255) NOT NULL default '',
  `attribute_value` char(255) default NULL,
  KEY `idx_product_attribute_product_id` (`product_id`),
  KEY `idx_product_attribute_name` (`attribute_name`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_product_attribute_sku`;
CREATE TABLE `mos_{vm}_product_attribute_sku` (
  `product_id` int(11) NOT NULL default '0',
  `attribute_name` char(255) NOT NULL default '',
  `attribute_list` int(11) default NULL,
  KEY `idx_product_attribute_sku_product_id` (`product_id`),
  KEY `idx_product_attribute_sku_attribute_name` (`attribute_name`),
  KEY `idx_product_attribute_list` (`attribute_list`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_product_category_xref`;
CREATE TABLE `mos_{vm}_product_category_xref` (
  `category_id` varchar(32) default NULL,
  `product_id` int(11) NOT NULL default '0',
  `product_list` int(11) default NULL,
  KEY `idx_product_category_xref_category_id` (`category_id`),
  KEY `idx_product_category_xref_product_id` (`product_id`),
  KEY `idx_product_category_xref_product_list` (`product_list`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_product_download`;
CREATE TABLE `mos_{vm}_product_download` (
  `product_id` int( 11 ) DEFAULT '0' NOT NULL ,
  `user_id` varchar( 255 ) DEFAULT '' NOT NULL ,
  `order_id` varchar( 255 ) DEFAULT '' NOT NULL ,
  `end_date` varchar( 255 ) DEFAULT '' NOT NULL ,
  `download_max` varchar( 255 ) DEFAULT '' NOT NULL ,
  `download_id` varchar( 255 ) DEFAULT '' NOT NULL ,
  `file_name` varchar( 255 ) DEFAULT '' NOT NULL ,
  PRIMARY KEY ( `download_id` ) 
  );
  
DROP TABLE IF EXISTS `mos_{vm}_product_price`;
CREATE TABLE `mos_{vm}_product_price` (
  `product_price_id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL default '0',
  `product_price` decimal(10,2) default NULL,
  `product_currency` char(16) default NULL,
  `product_price_vdate` int(11) default NULL,
  `product_price_edate` int(11) default NULL,
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `shopper_group_id` int(11) default NULL,
  PRIMARY KEY  (`product_price_id`),
  KEY `idx_product_price_product_id` (`product_id`),
  KEY `idx_product_price_shopper_group_id` (`shopper_group_id`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;

DROP TABLE IF EXISTS `mos_{vm}_product_reviews`;
CREATE TABLE `mos_{vm}_product_reviews` (
      `product_id` varchar(255) NOT NULL default '',
      `comment` text NOT NULL,
      `userid` int(11) NOT NULL default '0',
      `time` int(11) NOT NULL default '0',
      `user_rating` tinyint(1) NOT NULL default '0',
      `review_ok` int(11) NOT NULL default '0',
      `review_votes` int(11) NOT NULL default '0'
    ) TYPE=MyISAM;
    
DROP TABLE IF EXISTS `mos_{vm}_product_votes`
CREATE TABLE `mos_{vm}_product_votes` (
  `product_id` int(255) NOT NULL default '0',
  `votes` text NOT NULL,
  `allvotes` int(11) NOT NULL default '0',
  `rating` tinyint(1) NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '0'
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_shipping_carrier`;
create table `mos_{vm}_shipping_carrier` (
                `shipping_carrier_id` int(11) not null auto_increment, 
                `shipping_carrier_name` char(80) default '' not null, 
                `shipping_carrier_list_order` int(11) not null default 0, 
                PRIMARY KEY (`shipping_carrier_id`)) ;
INSERT INTO `mos_{vm}_shipping_carrier` VALUES (1, 'DHL', 0);
INSERT INTO `mos_{vm}_shipping_carrier` VALUES (2, 'UPS', 1);

DROP TABLE IF EXISTS `mos_{vm}_shipping_rate` ;
CREATE TABLE `mos_{vm}_shipping_rate` (
            `shipping_rate_id` int(11) not null auto_increment, 
            `shipping_rate_name` varchar(255) default '' not null, 
            `shipping_rate_carrier_id` int(11) default '0' not null, 
            `shipping_rate_country` text default '' not null, 
            `shipping_rate_zip_start` varchar(32) default '' not null, 
            `shipping_rate_zip_end` varchar(32) default '' not null, 
            `shipping_rate_weight_start` decimal(10,3) default '0' not null, 
            `shipping_rate_weight_end` decimal(10,3) default '0' not null, 
            `shipping_rate_value` decimal(10,2) default '0' not null, 
            `shipping_rate_package_fee` decimal(10,2) default '0' not null, 
            `shipping_rate_currency_id` int(11) default '0' not null, 
            `shipping_rate_vat_id` int(11) default '0' not null,
            `shipping_rate_list_order` int(11) default '0' not null, 
            PRIMARY KEY (`shipping_rate_id`));
            
INSERT INTO `mos_{vm}_shipping_rate` VALUES (1,'Inland &gt; 4kg','1','DEU','00000','99999','0.0','4.0','5.62','2','47','0','1');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (2,'Inland &gt; 8kg','1','DEU','00000','99999','4.0','8.0','6.39','2','47','0','2');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (3,'Inland &gt; 12kg','1','DEU','00000','99999','8.0','12.0','7.16','2','47','0','3');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (4,'Inland &gt; 20kg','1','DEU','00000','99999','12.0','20.0','8.69','2','47','0','4');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (5,'EU+ &gt;  4kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','0.0','4.0','14,57','2','47','0','5');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (6,'EU+ &gt;  8kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','4.0','8.0','18,66','2','47','0','6');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (7,'EU+ &gt; 12kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','8.0','12.0','22,57','2','47','0','7');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (8,'EU+ &gt; 20kg','1','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','12.0','20.0','30,93','2','47','0','8');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (9,'Europe &gt; 4kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','0.0','4.0','23,78','2','47','0','9');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (10,'Europe &gt;  8kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','4.0','8.0','29,91','2','47','0','10');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (11,'Europe &gt; 12kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','8.0','12.0','36,05','2','47','0','11');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (12,'Europe &gt; 20kg','1','ALB;ARM;AZE;BLR;BIH;BGR;EST;GEO;GIB;ISL;YUG;KAZ;HRV;LVA;LTU;MLT;MKD;MDA;NOR;ROM;RUS;SVN;TUR;UKR;HUN;BLR;CYP','00000','99999','12.0','20.0','48,32','2','47','0','12');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (13,'World_1 &gt;  4kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','0.0','4.0','26,84','2','47','0','13');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (14,'World_1 &gt; 8kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','4.0','8.0','35,02','2','47','0','14');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (15,'World_1 &gt;12kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','8.0','12.0','43,20','2','47','0','15');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (16,'World_1 &gt;20kg','1','EGY;DZA;BHR;IRQ;IRN;ISR;YEM;JOR;CAN;QAT;KWT;LBN;LBY;MAR;OMN;SAU;SYR;TUN;ARE;USA','00000','99999','12.0','20.0','59,57','2','47','0','16');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (17,'World_2 &gt; 4kg','1','','00000','99999','0.0','4.0','32,98','2','47','0','17');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (18,'World_2 &gt; 8kg','1','','00000','99999','4.0','8.0','47,29','2','47','0','18');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (19,'World_2 &gt; 12kg','1','','00000','99999','8.0','12.0','61,61','2','47','0','19');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (20,'World_2 &gt; 20kg','1','','00000','99999','12.0','20.0','90,24','2','47','0','20');
INSERT INTO `mos_{vm}_shipping_rate` VALUES (21,'UPS Express','2','AND;BEL;DNK;FRO;FIN;FRA;GRC;GRL;GBR;IRL;ITA;LIE;LUX;MCO;NLD;AUT;POL;PRT;SMR;SWE;CHE;SVK;ESP;CZE','00000','99999','0.0','20.0','5,24','2','47','0','21');

DROP TABLE IF EXISTS `mos_{vm}_shopper_group`;
CREATE TABLE `mos_{vm}_shopper_group` (
  `shopper_group_id` int(11) NOT NULL auto_increment,
  `vendor_id` int(11) default NULL,
  `shopper_group_name` varchar(32) default NULL,
  `shopper_group_desc` text,
  `shopper_group_discount` DECIMAL( 3,2 ) DEFAULT '0.00' NOT NULL,
  `default`tinyint(1) default '0' NOT NULL,
  PRIMARY KEY  (`shopper_group_id`),
  KEY `idx_shopper_group_vendor_id` (`vendor_id`),
  KEY `idx_shopper_group_name` (`shopper_group_name`)
) TYPE=MyISAM AUTO_INCREMENT=8 ;
INSERT INTO `mos_{vm}_shopper_group` VALUES (5, 1, '-default-', 'This is the default shopper group.', '0.00', '1');
INSERT INTO `mos_{vm}_shopper_group` VALUES (6, 1, 'Gold Level', 'Gold Level phpShoppers.', '0.00', '0');
INSERT INTO `mos_{vm}_shopper_group` VALUES (7, 1, 'Wholesale', 'Shoppers that can buy at wholesale.', '0.00', '0');

DROP TABLE IF EXISTS `mos_{vm}_shopper_vendor_xref`;
CREATE TABLE `mos_{vm}_shopper_vendor_xref` (
  `user_id` varchar(32) default NULL,
  `vendor_id` int(11) default NULL,
  `shopper_group_id` int(11) default NULL,
  `customer_number` varchar(32) default NULL,
  KEY `idx_shopper_vendor_xref_user_id` (`user_id`),
  KEY `idx_shopper_vendor_xref_vendor_id` (`vendor_id`),
  KEY `idx_shopper_vendor_xref_shopper_group_id` (`shopper_group_id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `mos_{vm}_tax_rate`;
CREATE TABLE `mos_{vm}_tax_rate` (
  `tax_rate_id` int(11) NOT NULL auto_increment,
  `vendor_id` int(11) default NULL,
  `tax_state` varchar(64) default NULL,
  `tax_country` varchar(64) default NULL,
  `mdate` int(11) default NULL,
  `tax_rate` decimal(10,4) default NULL,
  PRIMARY KEY  (`tax_rate_id`),
  KEY `idx_tax_rate_vendor_id` (`vendor_id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;
INSERT INTO `mos_{vm}_tax_rate` VALUES (2, 1, 'CA', 'USA', 964565926, '0.0650');

DROP TABLE IF EXISTS `mos_{vm}_user_info`;
CREATE TABLE `mos_{vm}_user_info` (
  `user_info_id` int(11) NOT NULL auto_increment,
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
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `perms` VARCHAR( 40 ) DEFAULT 'shopper' NOT NULL,
  PRIMARY KEY  (`user_info_id`),
  KEY `idx_user_info_user_id` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=20;

DROP TABLE IF EXISTS `mos_{vm}_vendor`;
CREATE TABLE `mos_{vm}_vendor` (
  `vendor_id` int(11) NOT NULL auto_increment,
  `vendor_name` varchar(64) default NULL,
  `contact_last_name` varchar(32) NOT NULL default '',
  `contact_first_name` varchar(32) NOT NULL default '',
  `contact_middle_name` varchar(32) default NULL,
  `contact_title` varchar(32) default NULL,
  `contact_phone_1` varchar(32) NOT NULL default '',
  `contact_phone_2` varchar(32) default NULL,
  `contact_fax` varchar(32) default NULL,
  `contact_email` varchar(255) default NULL,
  `vendor_phone` varchar(32) default NULL,
  `vendor_address_1` varchar(64) NOT NULL default '',
  `vendor_address_2` varchar(64) default NULL,
  `vendor_city` varchar(32) NOT NULL default '',
  `vendor_state` varchar(32) NOT NULL default '',
  `vendor_country` varchar(32) NOT NULL default 'US',
  `vendor_zip` varchar(32) NOT NULL default '',
  `vendor_store_name` varchar(128) NOT NULL default '',
  `vendor_store_desc` text,
  `vendor_category_id` int(11) default NULL,
  `vendor_thumb_image` varchar(255) default NULL,
  `vendor_full_image` varchar(255) default NULL,
  `vendor_currency` varchar(16) default NULL,
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `vendor_image_path` varchar(255) default NULL,
  `vendor_terms_of_service` TEXT NOT NULL,
  `vendor_url` VARCHAR( 255 ) NOT NULL,
  `vendor_min_pov` DECIMAL( 10, 2 ),
  PRIMARY KEY  (`vendor_id`),
  KEY `idx_vendor_name` (`vendor_name`),
  KEY `idx_vendor_category_id` (`vendor_category_id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

INSERT INTO `mos_{vm}_vendor` VALUES (1, 'Washupito\'s Tiendita', 'Owner', 'Demo', 'Store', 'Mr.', 
  '555-555-1212', '555-555-1212', '555-555-1212', 'demo_order@virtuemart.org', '555-555-1212', '100 Washupito Avenue, N.W.', 
  '', 'Lake Forest', 'CA', 'USA', '92630', 'Washupito\'s Tiendita', '<p>We have the best tools for do-it-yourselfers.  Check us out! </p>\r\n
  <p>We were established in 1969 in a time when getting good tools was expensive, but the quality was good.  Now that only a select few of those authentic 
  tools survive, we have dedicated this store to bringing the experience alive for collectors and master mechanics everywhere.  </p>\r\n\r\n
  <p>You can easily find products selecting the category you would like to browse above.</p>', 0, '', 'c19970d6f2970cb0d1b13bea3af3144a.gif', 'USD', 950302468, 968309845, 'shop_image/', 
  '<h5>You haven\'t configured any terms of service yet. Click <a href=administrator/index2.php?page=store.store_form&option=com_virtuemart>here</a> to change this text.</h5>',
  'http://www.virtuemart.net','0.00');

DROP TABLE IF EXISTS `mos_{vm}_vendor_category`;
CREATE TABLE `mos_{vm}_vendor_category` (
  `vendor_category_id` int(11) NOT NULL auto_increment,
  `vendor_category_name` varchar(64) default NULL,
  `vendor_category_desc` text,
  PRIMARY KEY  (`vendor_category_id`),
  KEY `idx_vendor_category_category_name` (`vendor_category_name`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;
INSERT INTO `mos_{vm}_vendor_category` VALUES (6, '-default-', 'Default');

DROP TABLE IF EXISTS `mos_{vm}_waiting_list`;
CREATE TABLE mos_{vm}_waiting_list (
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
    
DROP TABLE IF EXISTS `mos_{vm}_zone_shipping`;
CREATE TABLE `mos_{vm}_zone_shipping` (
  `zone_id` int(11) NOT NULL auto_increment,
  `zone_name` varchar(255) default NULL,
  `zone_cost` decimal(10,2) default NULL,
  `zone_limit` decimal(10,2) default NULL,
  `zone_description` text NOT NULL,
  PRIMARY KEY  (`zone_id`),
  KEY zone_id (`zone_id`)
) TYPE=MyISAM;
INSERT INTO `mos_{vm}_zone_shipping` VALUES (1, 'Default', '6.00', '35.00', 'This is the default Shipping Zone. This is the zone information that all countries will use until you assign each individual country to a Zone.');
INSERT INTO `mos_{vm}_zone_shipping` VALUES (2, 'Zone 1', '1000.00', '10000.00', 'This is a zone example');
INSERT INTO `mos_{vm}_zone_shipping` VALUES (3, 'Zone 2', '2.00', '22.00', 'This is the second zone. You can use this for notes about this zone');
INSERT INTO `mos_{vm}_zone_shipping` VALUES (4, 'Zone 3', '11.00', '64.00', 'Another usefull thing might be details about this zone or special instructions.');

DROP TABLE IF EXISTS `mos_{vm}_affiliate_sale`;
CREATE TABLE `mos_{vm}_affiliate_sale` (
               `order_id` int(11) NOT NULL,
               `visit_id` varchar(32) NOT NULL,
               `affiliate_id` int(11) NOT NULL,
               `rate` int(2) NOT NULL,
               PRIMARY KEY (`order_id`));
               
DROP TABLE IF EXISTS `mos_{vm}_affiliate`;
CREATE TABLE `mos_{vm}_affiliate` (
       `affiliate_id` int(11) NOT NULL auto_increment,
       `user_id` VARCHAR(32) NOT NULL,
       `active` char(1) DEFAULT 'N' NOT NULL,
       `rate` int(11) NOT NULL,
       PRIMARY KEY (`affiliate_id`));
       
DROP TABLE IF EXISTS `mos_{vm}_visit`;
CREATE TABLE `mos_{vm}_visit` (
             `visit_id` varchar(255) NOT NULL,
             `affiliate_id` int(11) NOT NULL,
             `pages` int(11) NOT NULL,
             `entry_page` varchar(255) NOT NULL,
             `exit_page` varchar(255) NOT NULL,
             `sdate` int(11) NOT NULL,
             `edate` int(11) NOT NULL,
             PRIMARY KEY (`visit_id`));
             
DROP TABLE IF EXISTS `mos_{vm}_creditcard`;            
CREATE TABLE `mos_{vm}_creditcard` (
              `creditcard_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
              `vendor_id` INT( 11 ) NOT NULL,
              `creditcard_name` VARCHAR( 70 ) NOT NULL ,
              `creditcard_code` VARCHAR( 30 ) NOT NULL ,
              PRIMARY KEY ( `creditcard_id` ));  
INSERT INTO `mos_{vm}_creditcard` VALUES (1, 1, 'Visa', 'VISA');
INSERT INTO `mos_{vm}_creditcard` VALUES (2, 1, 'MasterCard', 'MC');
INSERT INTO `mos_{vm}_creditcard` VALUES (3, 1, 'American Express', 'amex');
INSERT INTO `mos_{vm}_creditcard` VALUES (4, 1, 'Discover Card', 'discover');
INSERT INTO `mos_{vm}_creditcard` VALUES (5, 1, 'Diners Club', 'diners');
INSERT INTO `mos_{vm}_creditcard` VALUES (6, 1, 'JCB', 'jcb');
INSERT INTO `mos_{vm}_creditcard` VALUES (7, 1, 'Australian Bankcard', 'australian_bc');
