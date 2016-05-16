<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: sql.update.virtuemart-1.0.x.to.virtuemart-1.0.3.php,v 1.1.2.2 2006/03/22 19:29:31 soeren_nb Exp $
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
$db->setQuery( 'ALTER TABLE `#__{vm}_product_mf_xref` CHANGE `product_id` `product_id` INT( 11 ) NULL DEFAULT NULL');
$db->query();

$db->setQuery( 'ALTER TABLE `#__{vm}_orders` ADD `order_tax_details` TEXT NOT NULL AFTER `order_tax`');
$db->query();