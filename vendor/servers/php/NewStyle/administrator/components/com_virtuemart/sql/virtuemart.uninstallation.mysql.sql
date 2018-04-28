# $Id: virtuemart.uninstallation.mysql.sql,v 1.2.2.1 2006/03/23 19:41:00 soeren_nb Exp $
# SQL Uninstall script for VirtueMart
#
#
############################################################
# DELETE TABLES FOR VirtueMart Component
############################################################

DROP TABLE IF EXISTS `jos_vm_affiliate_sale`;
DROP TABLE IF EXISTS `jos_vm_affiliate`;

DROP TABLE IF EXISTS  `jos_vm_auth_user_vendor`;

DROP TABLE IF EXISTS  `jos_vm_category`;

DROP TABLE IF EXISTS  `jos_vm_category_xref`;

DROP TABLE IF EXISTS  `jos_vm_country`;

DROP TABLE IF EXISTS  `jos_vm_creditcard`;

DROP TABLE IF EXISTS  `jos_vm_csv`;

DROP TABLE IF EXISTS  `jos_vm_currency`;

DROP TABLE IF EXISTS  `jos_vm_function`;

DROP TABLE IF EXISTS `jos_vm_manufacturer`;

DROP TABLE IF EXISTS `jos_vm_manufacturer_category`;

DROP TABLE IF EXISTS  `jos_vm_module`;

DROP TABLE IF EXISTS  `jos_vm_order_history`;

DROP TABLE IF EXISTS  `jos_vm_order_item`;

DROP TABLE IF EXISTS  `jos_vm_order_payment`;

DROP TABLE IF EXISTS  `jos_vm_order_status`;

DROP TABLE IF EXISTS  `jos_vm_order_user_info`;

DROP TABLE IF EXISTS  `jos_vm_orders`;

DROP TABLE IF EXISTS  `jos_vm_payment_method`;

DROP TABLE IF EXISTS  `jos_vm_product`;

DROP TABLE IF EXISTS  `jos_vm_product_attribute`;

DROP TABLE IF EXISTS  `jos_vm_product_attribute_sku`;

DROP TABLE IF EXISTS  `jos_vm_product_category_xref`;

DROP TABLE IF EXISTS `jos_vm_product_mf_xref`;

DROP TABLE IF EXISTS  `jos_vm_product_price`;
DROP TABLE IF EXISTS  `jos_vm_product_relations`;
DROP TABLE IF EXISTS  `jos_vm_product_reviews`;

DROP TABLE IF EXISTS  `jos_vm_product_type`;
DROP TABLE IF EXISTS  `jos_vm_product_type_parameter`;
DROP TABLE IF EXISTS  `jos_vm_product_product_type_xref`;

DROP TABLE IF EXISTS  `jos_vm_product_votes`;

DROP TABLE IF EXISTS  `jos_vm_shipping_carrier`;

DROP TABLE IF EXISTS  `jos_vm_shipping_rate`;

DROP TABLE IF EXISTS  `jos_vm_state`;

DROP TABLE IF EXISTS  `jos_vm_product_download`;

DROP TABLE IF EXISTS  `jos_vm_shopper_group`;

DROP TABLE IF EXISTS  `jos_vm_shopper_vendor_xref`;

DROP TABLE IF EXISTS  `jos_vm_tax_rate`;

DROP TABLE IF EXISTS  `jos_vm_user_info`;

DROP TABLE IF EXISTS  `jos_vm_vendor`;

DROP TABLE IF EXISTS  `jos_vm_vendor_category`;

DROP TABLE IF EXISTS  `jos_vm_visit`;
DROP TABLE IF EXISTS  `jos_vm_waiting_list`;

DROP TABLE IF EXISTS  `jos_vm_zone_shipping`;



############################################################
# DELETE virtuemart record from jos_components
############################################################

DELETE FROM `jos_components` WHERE `option`='com_virtuemart';
DELETE FROM `jos_components` WHERE `name`='virtuemart_version';

