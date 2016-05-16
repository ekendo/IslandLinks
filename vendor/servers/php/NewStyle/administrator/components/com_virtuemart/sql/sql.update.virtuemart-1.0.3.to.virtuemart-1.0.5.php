<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: sql.update.virtuemart-1.0.3.to.virtuemart-1.0.5.php,v 1.1.2.1 2006/05/06 10:05:27 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2006 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_phpshop/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/


// http://virtuemart.net/index.php?option=com_flyspray&Itemid=83&do=details&id=521
# Allow Shopper group discounts up to 100.00%
$db->setQuery( "ALTER TABLE `#__{vm}_shopper_group` CHANGE `shopper_group_discount` `shopper_group_discount` DECIMAL( 5, 2 ) NOT NULL DEFAULT '0.00';" );$db->query();
# Allow bigger discounts than 999.99
$db->setQuery( "ALTER TABLE `#__{vm}_product_discount` CHANGE `amount` `amount` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';" );$db->query();
# Allow prices up to 9 999 999 999.99
$db->setQuery( "ALTER TABLE `#__{vm}_product_price` CHANGE `product_price` `product_price` DECIMAL( 12, 5 ) NULL DEFAULT NULL ;" );$db->query();
# Adjust order item price
$db->setQuery( "ALTER TABLE `#__{vm}_order_item` CHANGE `product_item_price` `product_item_price` DECIMAL( 15, 5 ) NULL DEFAULT NULL ;" );$db->query();
# Adjust order item final price
$db->setQuery( "ALTER TABLE `#__{vm}_order_item` CHANGE `product_final_price` `product_final_price` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';" );$db->query();
# Adjust order total, allowing totals up to 9 999 999 999 999.99
$db->setQuery( "ALTER TABLE `#__{vm}_orders` CHANGE `order_total` `order_total` DECIMAL( 15, 5 ) NULL DEFAULT NULL ;" );$db->query();
$db->setQuery( "ALTER TABLE `#__{vm}_orders` CHANGE `order_subtotal` `order_subtotal` DECIMAL( 15, 5 ) NULL DEFAULT NULL ;" );$db->query();

# Allow larger coupon amounts
$db->setQuery( "ALTER TABLE `#__{vm}_orders` CHANGE `coupon_discount` `coupon_discount` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';" );$db->query();
$db->setQuery( "ALTER TABLE `#__{vm}_coupons` CHANGE `coupon_value` `coupon_value` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';" );$db->query();

# Allow larger payment discounts
$db->setQuery( "ALTER TABLE `#__{vm}_orders` CHANGE `order_discount` `order_discount` DECIMAL( 12, 2 ) NOT NULL DEFAULT '0.00';" ); $db->query();
$db->setQuery( "ALTER TABLE `#__{vm}_payment_method` CHANGE `payment_method_discount` `payment_method_discount` DECIMAL( 12, 2 ) NULL DEFAULT NULL ;" ); $db->query();