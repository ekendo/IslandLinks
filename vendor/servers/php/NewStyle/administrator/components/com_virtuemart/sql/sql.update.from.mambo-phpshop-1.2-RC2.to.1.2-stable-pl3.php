<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: sql.update.from.mambo-phpshop-1.2-RC2.to.1.2-stable-pl3.php,v 1.2 2005/10/01 16:24:53 soeren_nb Exp $
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
$database->setQuery( "ALTER TABLE `#__pshop_shopper_group` ADD `show_price_including_tax` TINYINT( 1 ) DEFAULT '1' NOT NULL AFTER `shopper_group_discount` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_zone_shipping` ADD `zone_tax_rate` INT( 11 ) NOT NULL ;"); $database->query();

$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_relations` (
	  `product_id` int(11) NOT NULL default '0',
	  `related_products` text,
	  PRIMARY KEY  (`product_id`)
	) TYPE=MyISAM;"); $database->query();
	
$database->setQuery( "ALTER TABLE `#__pshop_product` CHANGE `product_in_stock` `product_in_stock` INT( 11 ) UNSIGNED DEFAULT NULL "); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_order_item` ADD `product_final_price` DECIMAL( 10, 2 ) NOT NULL AFTER `product_item_price` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_product_price` CHANGE `product_price` `product_price` DECIMAL( 10, 5 ) DEFAULT NULL "); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_product_price` 
						ADD `price_quantity_start` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL ,
						ADD `price_quantity_end` INT( 11 ) UNSIGNED NOT NULL ;"); $database->query();
						
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_order_history` (
`order_status_history_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`order_id` int( 11 ) NOT NULL default '0',
`order_status_code` CHAR( 1 ) NOT NULL DEFAULT '0',
`date_added` datetime NOT NULL default '0000-00-00 00:00:00',
`customer_notified` int( 1 ) default '0',
`comments` text,
PRIMARY KEY ( `order_status_history_id` )
) TYPE = MYISAM;" ); $database->query();

/**
* Insert the User Billto & Shipto Info into __pshop_order_user_info from all users that have placed and order
*/
$database->setQuery( "SELECT 1 FROM `#__pshop_order_user_info` LIMIT 0");
if( $database->query() )
  $order_user_info_exists = true;
else
  $order_user_info_exists = false;


$database->setQuery( "ALTER TABLE `#__pshop_order_item` DROP INDEX `idx_order_item_product_id` ;"); $database->query();
$database->setQuery( "ALTER TABLE `#__pshop_order_item` 
  ADD `order_item_sku` VARCHAR( 64 ) NOT NULL AFTER `product_id` ,
  ADD `order_item_name` VARCHAR( 64 ) NOT NULL AFTER `order_item_sku` ;"); 
if( $database->query() )
  $copy_product_info = true;
else
  $copy_product_info = false;
  
if( !$order_user_info_exists ) {
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
	$database->setQuery( "SELECT order_id, user_id, user_info_id FROM `#__pshop_orders`;"); 
	$orders = $database->loadObjectList();
	foreach( $orders as $order ) {
	  $user = Array();
	  // Bill To Address
	  $database->setQuery( "SELECT * FROM #__users WHERE id='".$order->user_id."'" );
	  $database->loadObject( $user );
	  $q = "INSERT INTO `#__pshop_order_user_info` ";
	  $q .= "(`order_id` , `user_id` , `address_type` , `address_type_name` , `company` , `title` , `last_name` , `first_name` , `middle_name` , `phone_1` , `phone_2` , `fax` , `address_1` , `address_2` , `city` , `state` , `country` , `zip` , `user_email`, `extra_field_1`, `extra_field_2`, `extra_field_3`, `extra_field_4`, `extra_field_5` ) ";
	  $q .= "VALUES ('".$order->order_id."', '".$order->user_id."', 'BT', '', '".$user->company."', '".$user->title."', '".$user->last_name."', '".$user->first_name."', '".$user->middle_name."', '".$user->phone_1."', '".$user->phone_2."', '".$user->fax."', '".$user->address_1."', '".$user->address_2."', '".$user->city."', '".$user->state."', '".$user->country."', '".$user->zip."', '".$user->email."', '".$user->extra_field_1."', '".$user->extra_field_2."', '".$user->extra_field_3."', '".$user->extra_field_4."', '".$user->extra_field_5."')";
	  $database->setQuery( $q );
	  $database->query();
	   
	  $user = Array();
	  // Ship to Address if applicable
	  $database->setQuery( "SELECT * FROM #__pshop_user_info WHERE user_id='".$order->user_id."' AND user_info_id='".$order->user_info_id."'" );
	  $database->loadObject( $user );
	  $last_id = null;
	  if( $user ) {
		$q = "INSERT INTO `#__pshop_order_user_info` ";
		$q .= "(`order_id` , `user_id` , `address_type` , `address_type_name` , `company` , `title` , `last_name` , `first_name` , `middle_name` , `phone_1` , `phone_2` , `fax` , `address_1` , `address_2` , `city` , `state` , `country` , `zip` , `user_email`, `extra_field_1`, `extra_field_2`, `extra_field_3`, `extra_field_4`, `extra_field_5` ) ";
		$q .= "VALUES ('".$order->order_id."', '".$order->user_id."', 'ST', '', '".$user->company."', '".$user->title."', '".$user->last_name."', '".$user->first_name."', '".$user->middle_name."', '".$user->phone_1."', '".$user->phone_2."', '".$user->fax."', '".$user->address_1."', '".$user->address_2."', '".$user->city."', '".$user->state."', '".$user->country."', '".$user->zip."', '".$user->user_email."', '".$user->extra_field_1."', '".$user->extra_field_2."', '".$user->extra_field_3."', '".$user->extra_field_4."', '".$user->extra_field_5."')";
		$database->setQuery( $q );
		$database->query();
		$last_id= $database->insertid();
	  }
	  if( $last_id ) {
		$database->setQuery( "UPDATE #__pshop_orders SET user_info_id='$last_id' WHERE order_id='".$order->order_id."'" );
		$database->query();
	  }
	}
}
if( $copy_product_info ) {
  /**
  * Insert the product sku and product name into __pshop_order_item
  */
  $database->setQuery( "SELECT order_item_id, product_id FROM `#__pshop_order_item`;"); 
  $products = $database->loadObjectList();
  foreach( $products as $order_item ) {
	$product = Array();
	$database->setQuery( "SELECT product_sku, product_name FROM #__pshop_product WHERE product_id='".$order_item->product_id."'" );
	$database->loadObject( $product );
	if( $product ) {
	  $database->setQuery( "UPDATE #__pshop_order_item SET order_item_sku='".addslashes($product->product_sku)."', order_item_name='".addslashes($product->product_name)."' WHERE order_item_id='".$order_item->order_item_id."'" );
	  $database->query();
	}
  }
}
/** THIS IS FOR MAMBELFISH - INTEGRATION
******************************************/
// Check if we need to update!
$database->setQuery( "SHOW COLUMNS FROM `#__pshop_category` " );
// Get the first row: category_id
$res = $database->loadResult();
if( $res->Type != "int(11)" ) {
  
  $database->setQuery( "SELECT category_id FROM #__pshop_category ORDER BY cdate" );
  $category_rows = $database->loadObjectList();
  $categories = Array();
  $i = 1;
  foreach( $category_rows as $category_row ) {
	$categories["old_id"] = $category_row->category_id;
	// assign the new_id to a Key named like the old_id
	$categories[$category_row->category_id] = $i++;
  }
  // Now as we have stored the old IDs we can update the table
  // mos_pshop_categories
  foreach( $category_rows as $category_row ) {
	$q = "UPDATE #__pshop_category SET category_id='".$categories[$category_row->category_id]."' WHERE category_id='".$category_row->category_id."'";
	$database->setQuery( $q ); $database->query();
  }
  // Alter the Table now
  $database->setQuery( "ALTER TABLE `#__pshop_category` CHANGE `category_id` `category_id` INT( 11 ) NOT NULL AUTO_INCREMENT;" ); $database->query();
  
  // Now update the Category XREF Table
  foreach( $category_rows as $category_row ) {
	$q = "UPDATE #__pshop_category_xref SET category_parent_id='".$categories[$category_row->category_id]."' WHERE category_parent_id='".$category_row->category_id."'";
	$database->setQuery( $q );  $database->query();
	$q = "UPDATE #__pshop_category_xref SET category_child_id='".$categories[$category_row->category_id]."' WHERE category_child_id='".$category_row->category_id."'";
	$database->setQuery( $q );  $database->query();
  }
  // When we have done that, Alter the Table!
  $database->setQuery( "ALTER TABLE `#__pshop_category_xref` 
						  CHANGE `category_parent_id` `category_parent_id` INT( 11 ) DEFAULT '0' NOT NULL ,
						  CHANGE `category_child_id` `category_child_id` INT( 11 ) DEFAULT '0' NOT NULL;" ); $database->query();
  
  // Now update the Product <-> Category XREF Table
  foreach( $category_rows as $category_row ) {
	$q = "UPDATE #__pshop_product_category_xref SET category_id='".$categories[$category_row->category_id]."' WHERE category_id='".$category_row->category_id."'";
	$database->setQuery( $q );  $database->query();
  }
  // Alter the Table now
  $database->setQuery( "ALTER TABLE `#__pshop_product_category_xref` CHANGE `category_id` `category_id` INT( 11 ) NOT NULL;" ); $database->query();
}
/*********************************
END MAMBELFISH ADJUSTMENT **/

/**
* Begin Product Types Integration */
$database->setQuery( "SELECT function_id FROM `#__pshop_function` WHERE function_name = 'ProductTypeAdd'");
$rows = $database->loadObjectList();
if( sizeof( $rows ) > 0 ) {
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeAdd', 'ps_product_type', 'add', 'Function add a Product Type and create new table product_type_<id>.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeUpdate', 'ps_product_type', 'update', 'Update a Product Type.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeDelete', 'ps_product_type', 'delete', 'Delete a Product Type and drop table product_type_<id>.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeReorder', 'ps_product_type', 'reorder', 'Changes the list order of a Product Type.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeAddParam', 'ps_product_type_parameter', 'add_parameter', 'Function add a Parameter into a Product Type and create new column in table product_type_<id>.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeUpdateParam', 'ps_product_type_parameter', 'update_parameter', 'Function update a Parameter in a Product Type and a column in table product_type_<id>.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeDeleteParam', 'ps_product_type_parameter', 'delete_parameter', 'Function delete a Parameter from a Product Type and drop a column in table product_type_<id>.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'ProductTypeReorderParam', 'ps_product_type_parameter', 'reorder_parameter', 'Changes the list order of a Parameter.', 'admin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'productProductTypeAdd', 'ps_product_product_type', 'add', 'Add a Product into a Product Type.', 'admin,storeadmin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'productProductTypeDelete', 'ps_product_product_type', 'delete', 'Delete a Product from a Product Type.', 'admin,storeadmin');" ); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 1, 'stateAdd', 'ps_country', 'addState', 'Add a State ', 'storeadmin,admin');"); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 1, 'stateUpdate', 'ps_country', 'updateState', 'Update a state record', 'storeadmin,admin');"); $database->query();
  $database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 1, 'stateDelete', 'ps_country', 'deleteState', 'Delete a state record', 'storeadmin,admin');"); $database->query();
}
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_type` (
	  `product_type_id` int(11) NOT NULL auto_increment,
	  `product_type_name` varchar(255) NOT NULL default '',
	  `product_type_description` text default NULL,
	  `product_type_publish` char(1) default NULL,
	  `product_type_browsepage` varchar(255) default NULL,
	  `product_type_flypage` varchar(255) default NULL,
	  `product_type_list_order` int(11) default NULL,
	  PRIMARY KEY (`product_type_id`)
  ) TYPE=MyISAM;" ); $database->query();
  
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_product_type_xref` (
	  `product_id` int(11) NOT NULL,
	  `product_type_id` int(11) NOT NULL,
	  KEY `idx_product_product_type_xref_product_id` (`product_id`),
	  KEY `idx_product_product_type_xref_product_type_id` (`product_type_id`)
  ) TYPE=MyISAM;" ); $database->query();
  
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_product_type_parameter` (
	  `product_type_id` int(11) NOT NULL,
	  `parameter_name` varchar(255) NOT NULL,
	  `parameter_label` varchar(255) NOT NULL default '',
	  `parameter_description` text,
	  `parameter_list_order` int(11) NOT NULL,
	  `parameter_type` char(1) NOT NULL default 'T',
	  `parameter_values` varchar(255) default NULL,
	  `parameter_multiselect` char(1) default NULL,
	  `parameter_default` varchar(255) default NULL,
	  `parameter_unit` varchar(32) default NULL,
	  PRIMARY KEY (`product_type_id`,`parameter_name`),
	  KEY `idx_product_type_parameter_product_type_id` (`product_type_id`),
	  KEY `idx_product_type_parameter_parameter_order` (`parameter_list_order`)
  ) TYPE=MyISAM;" ); $database->query();
/**
* End Product Types Integration */

# States Management; 05.05.2005
$database->setQuery( "CREATE TABLE IF NOT EXISTS `#__pshop_state` (
	`state_id` int(11) NOT NULL auto_increment,
	`country_id` int(11) NOT NULL default '1',
	`state_name` varchar(64) default NULL,
	`state_3_code` char(3) default NULL,
	`state_2_code` char(2) default NULL,
	PRIMARY KEY  (`state_id`),
	UNIQUE KEY `state_3_code` (`state_3_code`,`state_2_code`),
	KEY `idx_country_id` (`country_id`)
  ) TYPE=MyISAM;" ); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_state` VALUES
('', 223, 'Alabama', 'ALA', 'AL'),  ('', 223, 'Alaska', 'ALK', 'AK'),  ('', 223, 'Arizona', 'ARZ', 'AZ'),
('', 223, 'Arkansas', 'ARK', 'AR'),  ('', 223, 'California', 'CAL', 'CA'),  ('', 223, 'Colorado', 'COL', 'CO'),
('', 223, 'Connecticut', 'CCT', 'CT'),  ('', 223, 'Delaware', 'DEL', 'DE'),  ('', 223, 'District Of Columbia', 'DOC', 'DC'),
('', 223, 'Florida', 'FLO', 'FL'),  ('', 223, 'Georgia', 'GEA', 'GA'),  ('', 223, 'Hawaii', 'HWI', 'HI'),
('', 223, 'Idaho', 'IDA', 'ID'),  ('', 223, 'Illinois', 'ILL', 'IL'),  ('', 223, 'Indiana', 'IND', 'IN'),
('', 223, 'Iowa', 'IOA', 'IA'),  ('', 223, 'Kansas', 'KAS', 'KS'),  ('', 223, 'Kentucky', 'KTY', 'KY'),
('', 223, 'Louisiana', 'LOA', 'LA'),  ('', 223, 'Maine', 'MAI', 'ME'),  ('', 223, 'Maryland', 'MLD', 'MD'),
('', 223, 'Massachusetts', 'MSA', 'MA'),  ('', 223, 'Michigan', 'MIC', 'MI'),  ('', 223, 'Minnesota', 'MIN', 'MN'),
('', 223, 'Mississippi', 'MIS', 'MS'),  ('', 223, 'Missouri', 'MIO', 'MO'),  ('', 223, 'Montana', 'MOT', 'MT'),
('', 223, 'Nebraska', 'NEB', 'NE'),  ('', 223, 'Nevada', 'NEV', 'NV'),  ('', 223, 'New Hampshire', 'NEH', 'NH'),
('', 223, 'New Jersey', 'NEJ', 'NJ'),  ('', 223, 'New Mexico', 'NEM', 'NM'),  ('', 223, 'New York', 'NEY', 'NY'),
('', 223, 'North Carolina', 'NOC', 'NC'),  ('', 223, 'North Dakota', 'NOD', 'ND'),  ('', 223, 'Ohio', 'OHI', 'OH'),
('', 223, 'Oklahoma', 'OKL', 'OK'),  ('', 223, 'Oregon', 'ORN', 'OR'),  ('', 223, 'Pennsylvania', 'PEA', 'PA'),
('', 223, 'Rhode Island', 'RHI', 'RI'),  ('', 223, 'South Carolina', 'SOC', 'SC'),  ('', 223, 'South Dakota', 'SOD', 'SD'),
('', 223, 'Tennessee', 'TEN', 'TN'),  ('', 223, 'Texas', 'TXS', 'TX'), ('', 223, 'Utah', 'UTA', 'UT'),  
('', 223, 'Vermont', 'VMT', 'VT'),  ('', 223, 'Virginia', 'VIA', 'VA'),  ('', 223, 'Washington', 'WAS', 'WA'),  
('', 223, 'West Virginia', 'WEV', 'WV'),  ('', 223, 'Wisconsin', 'WIS', 'WI'), ('', 223, 'Wyoming', 'WYO', 'WY'),

('', 38, 'Alberta', 'ALB', 'AB'),  ('', 38, 'British Columbia', 'BRC', 'BC'),  ('', 38, 'Manitoba', 'MAB', 'MB'),
('', 38, 'New Brunswick', 'NEB', 'NB'),  ('', 38, 'Newfoundland and Labrador', 'NFL', 'NL'),  ('', 38, 'Northwest Territories', 'NWT', 'NT'),
('', 38, 'Nova Scotia', 'NOS', 'NS'),  ('', 38, 'Nunavut', 'NUT', 'NU'),  ('', 38, 'Ontario', 'ONT', 'ON'),
('', 38, 'Prince Edward Island', 'PEI', 'PE'),  ('', 38, 'Quebec', 'QEC', 'QC'),  ('', 38, 'Saskatchewan', 'SAK', 'SK'),
('', 38, 'Yukon', 'YUT', 'YT'),  ('', 222, 'England', 'ENG', 'EN'),  ('', 222, 'Northern Ireland', 'NOI', 'NI'),
('', 222, 'Scotland', 'SCO', 'SD'),  ('', 222, 'Wales', 'WLS', 'WS'),  ('', 13, 'Australian Capital Territory', 'ACT', 'AT'),
('', 13, 'New South Wales', 'NSW', 'NW'),  ('', 13, 'Northern Territory', 'NOT', 'NT'),  ('', 13, 'Queensland', 'QLD', 'QL'),
('', 13, 'South Australia', 'SOA', 'SA'),  ('', 13, 'Tasmania', 'TAS', 'TA'),  ('', 13, 'Victoria', 'VIC', 'VI'),  ('', 13, 'Western Australia', 'WEA', 'WA'),

('', 138, 'Aguascalientes', 'AGS', 'AG'),  ('', 138, 'Baja California Norte', 'BCN', 'BN'),  ('', 138, 'Baja California Sur', 'BCS', 'BS'),
('', 138, 'Campeche', 'CAM', 'CA'),  ('', 138, 'Chiapas', 'CHI', 'CS'),  ('', 138, 'Chihuahua', 'CHA', 'CH'),
('', 138, 'Coahuila', 'COA', 'CO'),  ('', 138, 'Colima', 'COL', 'CM'),  ('', 138, 'Distrito Federal', 'DFM', 'DF'),
('', 138, 'Durango', 'DGO', 'DO'),  ('', 138, 'Guanajuato', 'GTO', 'GO'),  ('', 138, 'Guerrero', 'GRO', 'GU'),
('', 138, 'Hidalgo', 'HGO', 'HI'),  ('', 138, 'Jalisco', 'JAL', 'JA'),  ('', 138, 'México (Estado de)', 'EDM', 'EM'),
('', 138, 'Michoacán', 'MCN', 'MI'),  ('', 138, 'Morelos', 'MOR', 'MO'),  ('', 138, 'Nayarit', 'NAY', 'NY'),
('', 138, 'Nuevo León', 'NUL', 'NL'),  ('', 138, 'Oaxaca', 'OAX', 'OA'),  ('', 138, 'Puebla', 'PUE', 'PU'),
('', 138, 'Querétaro', 'QRO', 'QU'),  ('', 138, 'Quintana Roo', 'QUR', 'QR'),  ('', 138, 'San Luis Potosí', 'SLP', 'SP'),
('', 138, 'Sinaloa', 'SIN', 'SI'),  ('', 138, 'Sonora', 'SON', 'SO'),  ('', 138, 'Tabasco', 'TAB', 'TA'),
('', 138, 'Tamaulipas', 'TAM', 'TM'),  ('', 138, 'Tlaxcala', 'TLX', 'TX'),  ('', 138, 'Veracruz', 'VER', 'VZ'),
('', 138, 'Yucatán', 'YUC', 'YU'),  ('', 138, 'Zacatecas', 'ZAC', 'ZA'),

('', 30, 'Acre', 'ACR', 'AC'),  ('', 30, 'Alagoas', 'ALG', 'AL'),  ('', 30, 'Amapá', 'AMP', 'AP'),
('', 30, 'Amazonas', 'AMZ', 'AM'),  ('', 30, 'Bahía', 'BAH', 'BA'),  ('', 30, 'Ceará', 'CEA', 'CE'),
('', 30, 'Distrito Federal', 'DFB', 'DF'),  ('', 30, 'Espirito Santo', 'ESS', 'ES'),  ('', 30, 'Goiás', 'GOI', 'GO'),
('', 30, 'Maranhão', 'MAR', 'MA'),  ('', 30, 'Mato Grosso', 'MAT', 'MT'),
('', 30, 'Mato Grosso do Sul', 'MGS', 'MS'),  ('', 30, 'Minas Geraís', 'MIG', 'MG'),  ('', 30, 'Paraná', 'PAR', 'PR'),
('', 30, 'Paraíba', 'PRB', 'PB'),  ('', 30, 'Pará', 'PAB', 'PA'),  ('', 30, 'Pernambuco', 'PER', 'PR'),
('', 30, 'Piauí', 'PIA', 'PI'),  ('', 30, 'Rio Grande do Norte', 'RGN', 'RN'),  ('', 30, 'Rio Grande do Sul', 'RGS', 'RS'),
('', 30, 'Rio de Janeiro', 'RDJ', 'RJ'),  ('', 30, 'Rondônia', 'RON', 'RO'),
('', 30, 'Roraima', 'ROR', 'RR'),  ('', 30, 'Santa Catarina', 'SAC', 'SC'),  ('', 30, 'Sergipe', 'SER', 'SE'),
('', 30, 'São Paulo', 'SAP', 'SP'),  ('', 30, 'Tocantins', 'TOC', 'TO'),  

('', 44, 'Anhui', 'ANH', 'AN'),  ('', 44, 'Beijing', 'BEI', 'BE'),  ('', 44, 'Fujian', 'FUJ', 'FJ'),
('', 44, 'Gansu', 'GAN', 'GU'),  ('', 44, 'Guangdong', 'GUA', 'GU'),  ('', 44, 'Guangxi Zhuang', 'GUZ', 'GZ'),
('', 44, 'Guizhou', 'GUI', 'GI'),  ('', 44, 'Hainan', 'HAI', 'HA'),  ('', 44, 'Hebei', 'HEB', 'HE'),
('', 44, 'Heilongjiang', 'HEI', 'HG'),  ('', 44, 'Henan', 'HEN', 'HN'), 
('', 44, 'Hubei', 'HUB', 'HI'),  ('', 44, 'Hunan', 'HUN', 'HU'),  ('', 44, 'Jiangsu', 'JIA', 'JI'),
('', 44, 'Jiangxi', 'JIX', 'JX'),  ('', 44, 'Jilin', 'JIL', 'JN'),  ('', 44, 'Liaoning', 'LIA', 'LI'),
('', 44, 'Nei Mongol', 'NML', 'NM'),  ('', 44, 'Ningxia Hui', 'NIH', 'NH'),  ('', 44, 'Qinghai', 'QIN', 'QI'),
('', 44, 'Shaanxi', 'SHA', 'SH'),  ('', 44, 'Shandong', 'SNG', 'SG'),  ('', 44, 'Shanghai', 'SHH', 'SI'),
('', 44, 'Shanxi', 'SHX', 'SX'),  ('', 44, 'Sichuan', 'SIC', 'SN'),  ('', 44, 'Tianjin', 'TIA', 'TI'),
('', 44, 'Xinjiang Uygur', 'XIU', 'XU'),  ('', 44, 'Xizang', 'XIZ', 'XI'),  ('', 44, 'Yunnan', 'YUN', 'YU'),  ('', 44, 'Zhejiang', 'ZHE', 'ZH');"); $database->query();

$database->setQuery( "DROP TABLE IF EXISTS `#__pshop_csv`;"); $database->query();
$database->setQuery( "CREATE TABLE `#__pshop_csv` (
  `field_id` int(11) NOT NULL auto_increment,
  `field_name` VARCHAR(128) NOT NULL,
  `field_default_value` text,
  `field_ordering` int(3) NOT NULL,
  `field_required` char(1) default 'N',
  PRIMARY KEY  (`field_id`)
) TYPE=MyISAM;"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_csv` VALUES
  ('', 'product_sku', '', 1, 'Y' ),  ('', 'product_s_desc', '', 2, 'N' ),  ('', 'product_desc', '', 3, 'N' ),
  ('', 'product_thumb_image', '', 4, 'N' ),  ('', 'product_full_image', '', 5, 'N' ),  ('', 'product_weight', '', 6, 'N' ),
  ('', 'product_weight_uom', 'KG', 7, 'N' ),  ('', 'product_length', '', 8, 'N' ),  ('', 'product_width', '', 9, 'N' ),
  ('', 'product_height', '', 10, 'N' ),  ('', 'product_lwh_uom', '', 11, 'N' ),  ('', 'product_in_stock', '0', 12, 'N' ),
  ('', 'product_available_date', '', 13, 'N' ),  ('', 'product_discount_id', '', 14, 'N' ),  ('', 'product_name', '', 15, 'Y' ),
  ('', 'product_price', '', 16, 'N' ),  ('', 'category_path', '', 17, 'Y' ),  ('', 'manufacturer_id', '', 18, 'N' ),
  ('', 'product_tax_id', '', 19, 'N' ),  ('', 'product_sales', '', 20, 'N' ),  ('', 'product_parent_id', '0', 21, 'N' ),
  ('', 'attribute', '', 22, 'N' ),  ('', 'custom_attribute', '', 23, 'N' ), ('', 'attributes', '', 24, 'N' ),  ('', 'attribute_values', '', 25, 'N' );"); $database->query();

$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'csvFieldAdd', 'ps_csv', 'add', 'Add a CSV Field ', 'storeadmin,admin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'csvFieldUpdate', 'ps_csv', 'update', 'Update a CSV Field', 'storeadmin,admin');"); $database->query();
$database->setQuery( "INSERT INTO `#__pshop_function` VALUES ('', 2, 'csvFieldDelete', 'ps_csv', 'delete', 'Delete a CSV Field', 'storeadmin,admin');"); $database->query();

$database->setQuery( "INSERT INTO `#__pshop_payment_method` VALUES ('', 1, 'Dankort / PBS', 'ps_pbs', 5, '0.00', 0, 'PBS', 'P', 0, 'N', '', '', '');"); $database->query();
  
?> 