<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* This file is the Toolbar controller for VirtueMart
*
* There are three main Toolbar cases:
* - a List Toolbar with "New / Delete / Publish"
* - a Forms Toolbar with "Save / Cancel"
* - no toolbar
*
*
* @version $Id: toolbar.virtuemart.php,v 1.3.2.1 2006/04/10 19:10:12 soeren_nb Exp $
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
if( stristr( $_SERVER['PHP_SELF'], 'administrator')) {
	@define( '_PSHOP_ADMIN', '1' );
}
	
global $page;
if (!file_exists( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/install.php' )) {
    // We parse the phpShop main code before loading the toolbar,
    // for we can catch errors and adjust the toolbar when
    // the admin has to stay on a site or is redirected back on error
    require_once( $mosConfig_absolute_path."/components/com_virtuemart/virtuemart_parser.php");

	require_once( $mainframe->getPath( 'toolbar_html' ) );
	require_once( CLASSPATH . "menuBar.class.php" );
	
	// We have to do some page declarations here
	
	// Used for pages that allow (un)publishing items
	$allowsListPublish = Array( "product.product_list", 
							"product.product_category_list",
							"store.payment_method_list"
						);
	// The list of pages with their functions that allow batch deletion
	$allowsListDeletion = Array(
								"admin.country_list" => "countryDelete",
								"admin.curr_list" => "currencyDelete",
								"admin.function_list" => "functionDelete",
								"admin.module_list" => "moduleDelete",
								"admin.user_list" => "userDelete",
								"affiliate.affiliate_list" => "affiliateDelete",
								"coupon.coupon_list" => "couponDelete",
								"store.creditcard_list" => "creditcardDelete",
								"product.file_list" => "deleteProductFile",
								"tax.tax_list" => "deleteTaxRate",
								"manufacturer.manufacturer_category_list" => "manufacturercategoryDelete",
								"manufacturer.manufacturer_list" => "manufacturerDelete",
								"order.order_list" => "orderDelete",
								"order.order_status_list" => "orderStatusDelete",
								"store.payment_method_list" => "paymentMethodDelete",
								"product.product_attribute_list" => "productAttributeDelete",
								"product.product_category_list" => "productCategoryDelete",
								"product.product_discount_list" => "discountDelete",
								"product.product_list" => "productDelete",
								"product.product_price_list" => "productPriceDelete",
								"product.product_produt_type_list" => "productProductTypeDelete",
								"product.review_list" => "productReviewDelete",
								"product.product_type_list" => "ProductTypeDelete",
								"product.product_type_parameter_list" => "ProductTypeDeleteParam",
								"shipping.rate_list" => "rateDelete",
								"shipping.carrier_list" => "carrierDelete",
								"shopper.shopper_group_list" => "shopperGroupDelete",
								"zone.zone_list" => "deletezone"
								);
	// Can be used for lists that allow NO batch delete
	$noListDelete = Array();
	
	//  Forms Toolbar
	if ( stristr($page, "form") || $page == "admin.show_cfg" || $page == "affiliate.affiliate_add" ) {
			
		MENU_virtuemart::FORMS_MENU_SAVE_CANCEL();
	}
	// Lists Toolbar
	elseif ( stristr($page,"list") && $page != "affiliate.shopper_list" ) {
		
		vmMenuBar::startTable();
		
		// Some lists allow special tasks like "Add price" or "Add State"
		MENU_virtuemart::LISTS_SPECIAL_TASKS( $page );
		
		if( $page != "order.order_list") {
			// For New / Cloning Items
			MENU_virtuemart::LISTS_MENU_NEW();
		}
		// For (Un)Publishing Items
		if( in_array( $page, $allowsListPublish )) {
			MENU_virtuemart::LISTS_MENU_PUBLISH( 'changePublishState' );
		}
		// Delete Items
		if( !empty( $allowsListDeletion[$page] )) {
			vmMenuBar::divider();
			vmMenuBar::spacer();
			MENU_virtuemart::LISTS_MENU_DELETE( $allowsListDeletion[$page] );
		}
		vmMenuBar::endTable();
	}
	elseif( $page == "zone.assign_zones" ) {
		vmMenuBar::startTable();
		vmMenuBar::custom( 'save', $page, $vmIcons['save_icon'], $vmIcons['save_icon2'], 'Save Zone Assignments', true, "adminForm", 'zoneassign' );
		vmMenuBar::endTable();
	}

}
?>