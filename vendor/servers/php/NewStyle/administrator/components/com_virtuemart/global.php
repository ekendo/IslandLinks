<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

session_save_path("/home/users/web/b453/hy.ekendodreamof/cgi-bin/tmp");
session_start();

/**
* @version $Id: global.php,v 1.4.2.2 2006/02/18 09:20:05 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

global $header_bgcolor, $leftbar_title_bgcolor, $leftbar_font_color,$vendor_image,$vendor_country_2_code ,$vendor_country_3_code ,
        $vendor_image_url, $vendor_name, $vendor_address, $vendor_city,$vendor_country,$vendor_mail,$vendor_store_name,
        $vendor_state, $vendor_zip, $vendor_phone, $vendor_currency, $vendor_store_desc, $vendor_freeshipping,
        $module_description, $VM_LANG, $vendor_currency_display_style, $vendor_full_image;

# Global links
$search_box_title = $VM_LANG->_PHPSHOP_SEARCH_TITLE;

# Some database values we will need throughout
# Get Vendor Information
// Benjamin: change this using a dynamic global variable...
$default_vendor = 1;

if( $my->id ) {
	$db->query( 'SELECT `vendor_id` FROM `#__{vm}_auth_user_vendor` WHERE `user_id` ='.$my->id );
	$db->next_record();
	if( $db->f( 'vendor_id' ) ) {
		$default_vendor = $db->f( 'vendor_id' );
	}
}

$_SESSION["ps_vendor_id"] = $ps_vendor_id = $default_vendor;

$q = "SELECT vendor_id, vendor_min_pov,vendor_name,vendor_store_name,contact_email,vendor_full_image, vendor_freeshipping,
			vendor_address_1, vendor_city, vendor_state, vendor_country, country_2_code, country_3_code,
			vendor_zip, vendor_phone, vendor_store_desc, vendor_currency, vendor_currency_display_style
		FROM (`#__{vm}_vendor`, `#__{vm}_country`)
		WHERE `vendor_id`=$default_vendor
		AND (vendor_country=country_2_code OR vendor_country=country_3_code);";

$db->query($q);
$db->next_record();

$_SESSION['minimum_pov'] = $db->f("vendor_min_pov");
$vendor_name = $db->f("vendor_name");
$vendor_store_name = $db->f("vendor_store_name");
$vendor_mail = $db->f("contact_email");
$vendor_freeshipping = $db->f("vendor_freeshipping");
$vendor_image = "<img border=\"0\" src=\"" .IMAGEURL ."vendor/" . $db->f("vendor_full_image") . "\" />";
$vendor_full_image = $db->f("vendor_full_image");
$vendor_image_url = IMAGEURL."vendor/".$db->f("vendor_full_image");
$vendor_address = $db->f("vendor_address_1");
$vendor_city = $db->f("vendor_city");
$vendor_state = $db->f("vendor_state");
$vendor_state = empty($vendor_state) ? "" : $db->f("vendor_state");
$vendor_country = $db->f("vendor_country");
$vendor_country_2_code = $db->f("country_2_code");
$vendor_country_3_code = $db->f("country_3_code");
$vendor_zip = $db->f("vendor_zip");
$vendor_phone = $db->f("vendor_phone");
$vendor_store_desc = $db->f("vendor_store_desc");
$vendor_currency = $db->f("vendor_currency");
$vendor_currency_display_style = $db->f("vendor_currency_display_style");
$_SESSION["vendor_currency"] = $vendor_currency;

?>