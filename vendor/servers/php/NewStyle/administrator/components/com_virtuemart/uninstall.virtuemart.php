<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: uninstall.virtuemart.php,v 1.6.2.1 2006/03/14 18:42:04 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

function com_uninstall() {
	global $database, $mosConfig_absolute_path, $mosConfig_live_site;
	
	require( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/virtuemart.cfg.php' );
	require( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/classes/ps_database.php' );
	
	$db = &new ps_DB;
	
	  
	// This is the function which is called on Uninstall after the component files
	// have been removed and all tables for VirtueMart that are contained
	// in the virtuemart.xml file have been removed.
	// But what if we can't predict the number of tables?
	// e.g.: For each new Product Type we dynamically create one new Table.
	// So let's remove those tables (if there).
	$db->query( "SELECT product_type_id FROM #__{vm}_product_type" );
	$tables = $db->record;
	if( !empty( $tables )) {
		foreach( $tables as $table ) {
			$db->query( "DROP TABLE IF EXISTS `#__{vm}_product_type_". $table->product_type_id . "`" );
		}
	}
	
	$db->query( 'DROP TABLE `#__{vm}_affiliate`;' );
	$db->query( 'DROP TABLE `#__{vm}_affiliate_sale`;' );
	$db->query( 'DROP TABLE `#__{vm}_auth_user_vendor`;' );
	$db->query( 'DROP TABLE `#__{vm}_category`;' );
	$db->query( 'DROP TABLE `#__{vm}_category_xref`;' );
	$db->query( 'DROP TABLE `#__{vm}_country`;' );
	$db->query( 'DROP TABLE `#__{vm}_coupons`;' );
	$db->query( 'DROP TABLE `#__{vm}_creditcard`;' );
	$db->query( 'DROP TABLE `#__{vm}_csv`;' );
	$db->query( 'DROP TABLE `#__{vm}_currency`;' );
	$db->query( 'DROP TABLE `#__{vm}_function`;' );
	$db->query( 'DROP TABLE `#__{vm}_manufacturer`;' );
	$db->query( 'DROP TABLE `#__{vm}_manufacturer_category`;' );
	$db->query( 'DROP TABLE `#__{vm}_module`;' );
	$db->query( 'DROP TABLE `#__{vm}_order_history`;' );
	$db->query( 'DROP TABLE `#__{vm}_order_item`;' );
	$db->query( 'DROP TABLE `#__{vm}_order_payment`;' );
	$db->query( 'DROP TABLE `#__{vm}_order_status`;' );
	$db->query( 'DROP TABLE `#__{vm}_order_user_info`;' );
	$db->query( 'DROP TABLE `#__{vm}_orders`;' );
	$db->query( 'DROP TABLE `#__{vm}_payment_method`;' );
	$db->query( 'DROP TABLE `#__{vm}_product`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_attribute`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_attribute_sku`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_category_xref`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_discount`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_download`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_files`;' );   
	$db->query( 'DROP TABLE `#__{vm}_product_mf_xref`;' );   
	$db->query( 'DROP TABLE `#__{vm}_product_price`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_relations`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_reviews`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_type`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_type_parameter`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_product_type_xref`;' );
	$db->query( 'DROP TABLE `#__{vm}_product_votes`;' );
	$db->query( 'DROP TABLE `#__{vm}_shipping_carrier`;' );
	$db->query( 'DROP TABLE `#__{vm}_shipping_rate`;' );
	$db->query( 'DROP TABLE `#__{vm}_shopper_group`;' );
	$db->query( 'DROP TABLE `#__{vm}_shopper_vendor_xref`;' );
	$db->query( 'DROP TABLE `#__{vm}_state`;' );
	$db->query( 'DROP TABLE `#__{vm}_tax_rate`;' );
	$db->query( 'DROP TABLE `#__{vm}_user_info`;' );
	$db->query( 'DROP TABLE `#__{vm}_vendor`;' );
	$db->query( 'DROP TABLE `#__{vm}_vendor_category`;' );
	$db->query( 'DROP TABLE `#__{vm}_visit`;' );
	$db->query( 'DROP TABLE `#__{vm}_waiting_list`;' );
	$db->query( 'DROP TABLE `#__{vm}_zone_shipping`;' );
	
	$db->query( 'DELETE FROM `#__components` WHERE name = \'virtuemart_version\';' );
  
}

?>
