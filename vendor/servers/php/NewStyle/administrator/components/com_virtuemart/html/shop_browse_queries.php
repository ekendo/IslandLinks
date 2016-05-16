<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/*
* This file is to be included from the file shop.browse.php
* and uses variables from the environment of the file shop.browse.php
*
* @version $Id: shop_browse_queries.php,v 1.6.2.4 2006/04/23 19:40:07 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
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
mm_showMyFileName( __FILE__ );

/** Prepare the SQL Queries
*
*/
// These are the names of all fields we fetch data from
$fieldnames = "`product_name`,`products_per_row`,`category_browsepage`,`category_flypage`,`#__{vm}_product`.`product_id`,`#__{vm}_category`.`category_id`,`product_full_image`,`product_thumb_image`,`product_s_desc`,`product_parent_id`,`product_publish`,`product_in_stock`,`product_sku`";
$count_name = "COUNT(DISTINCT `#__{vm}_product`.`product_sku`) as num_rows";

switch( $orderby ) {
	case 'product_name':
		$orderbyField = '`#__{vm}_product`.`product_name`'; break;
	case 'product_price':
		$orderbyField = '`#__{vm}_product_price`.`product_price`'; break;
	case 'product_sku':
		$orderbyField = '`#__{vm}_product`.`product_sku`'; break;
	case 'product_cdate':
		$orderbyField = '`#__{vm}_product`.`cdate`'; break;
	default:
		$orderbyField = '`#__{vm}_product`.`product_name`'; break;
}

/** Changed Product Type - Begin */
if (!empty($product_type_id)) {
	require_once (CLASSPATH."ps_product_type.php");
	$ps_product_type = new ps_product_type();

	// list parameters:
	$q  = "SELECT `parameter_name`, `parameter_type` FROM `#__{vm}_product_type_parameter` WHERE `product_type_id`='$product_type_id'";
	$db_browse->query($q);

	/*** GET ALL PUBLISHED PRODUCT WHICH MATCH PARAMETERS ***/
	$list  = "SELECT DISTINCT $fieldnames FROM (`#__{vm}_product`, `#__{vm}_category`, `#__{vm}_product_category_xref`,`#__{vm}_shopper_group`) ";
	$count  = "SELECT $count_name FROM (`#__{vm}_product`, `#__{vm}_category`, `#__{vm}_product_category_xref`,`#__{vm}_shopper_group`) ";

	$q  = "LEFT JOIN `#__{vm}_product_price` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_price`.`product_id` ";
	$q .= "\n \n LEFT JOIN `#__{vm}_product_type_$product_type_id` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_type_$product_type_id`.`product_id` ";
	$q .= "\n LEFT JOIN `#__{vm}_product_product_type_xref` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_product_type_xref`.`product_id` ";
	$q .= "\n WHERE `#__{vm}_product_category_xref`.`category_id`=`#__{vm}_category`.`category_id` ";
	// $q .= "\n AND `#__{vm}_product`.`product_id`=`#__{vm}_product_category_xref`.`product_id` ";
	//  $q .= "\n AND `#__{vm}_product`.`product_parent_id`='0' ";
	$q .= "\n AND (`#__{vm}_product`.`product_id`=`#__{vm}_product_category_xref`.`product_id` ";
	$q .= "\n OR `#__{vm}_product`.`product_parent_id`=`#__{vm}_product_category_xref`.`product_id`)";
	if( !$perm->check("admin,storeadmin") ) {
		$q .= "\n  AND `product_publish`='Y'";
		if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
			$q .= "\n  AND `product_in_stock` > 0 ";
		}
	}

	$q .= "\n AND `#__{vm}_product_product_type_xref`.`product_type_id`=$product_type_id ";

	// find by parameters
	while ($db_browse->next_record()) {
		$parameter_name = $db_browse->f("parameter_name");
		$item_name = "product_type_$product_type_id"."_".$parameter_name;
		$get_item_value = mosgetparam($_REQUEST, $item_name, "");
		$get_item_value_comp = mosgetparam($_REQUEST, $item_name."_comp", "");

		if (is_array($get_item_value) ? count($get_item_value) : strlen($get_item_value) ) {
			// comparison
			switch ($get_item_value_comp) {
				case "lt": $comp = " < "; break;
				case "le": $comp = " <= "; break;
				case "eq": $comp = " <=> "; break;
				case "ge": $comp = " >= "; break;
				case "gt": $comp = " > "; break;
				case "ne": $comp = " <> "; break;
				case "texteq":
				$comp = " <=> ";
				break;
				case "like":
				$comp = " LIKE ";
				$get_item_value = "%".$get_item_value."%";
				break;
				case "notlike":
				$comp = "COALESCE(`".$parameter_name."` NOT LIKE '%".$get_item_value."%',1)";
				$parameter_name = "";
				$get_item_value = "";
				break;
				case "in": // Multiple section List of values
				$comp = " IN ('".join("','",$get_item_value)."')";
				$get_item_value = "";
				break;
				case "fulltext":
				$comp = "MATCH (`".$parameter_name."`) AGAINST ";
				$parameter_name = "";
				$get_item_value = "('".$get_item_value."')";
				break;
				case "find_in_set":
				$comp = "FIND_IN_SET('$get_item_value',`$parameter_name`)";
				$parameter_name = "";
				$get_item_value = "";
				break;
				case "find_in_set_all":
				case "find_in_set_any":
				$comp = array();
				foreach($get_item_value as $value) {
					array_push($comp,"FIND_IN_SET('$value',`$parameter_name`)");
				}
				$comp = "(" . join($get_item_value_comp == "find_in_set_all"?" AND ":" OR ", $comp) . ")";
				$parameter_name = "";
				$get_item_value = "";
				break;
			}
			switch ($db_browse->f("parameter_type")) {
				case "D": $get_item_value = "CAST('".$get_item_value."' AS DATETIME)"; break;
				case "A": $get_item_value = "CAST('".$get_item_value."' AS DATE)"; break;
				case "M": $get_item_value = "CAST('".$get_item_value."' AS TIME)"; break;
				case "C": $get_item_value = "'".substr($get_item_value,0,1)."'"; break;
				default:
				if( strlen($get_item_value) ) $get_item_value = "'".$get_item_value."'";
			}
			if( !empty($parameter_name) ) $parameter_name = "`".$parameter_name."`";
			$q .= "\n AND ".$parameter_name.$comp.$get_item_value." ";
		}
	}
	$item_name = "price";
	$get_item_value = mosgetparam($_REQUEST, $item_name, "");
	$get_item_value_comp = mosgetparam($_REQUEST, $item_name."_comp", "");
	// search by price
	if (!empty($get_item_value)) {
		// comparison
		switch ($get_item_value_comp) {
			case "lt": $comp = " < "; break;
			case "le": $comp = " <= "; break;
			case "eq": $comp = " = "; break;
			case "ge": $comp = " >= "; break;
			case "gt": $comp = " > "; break;
			case "ne": $comp = " <> "; break;
		}
		$q .= "\n AND ( ISNULL(product_price) OR product_price".$comp.$get_item_value." ) ";
		$auth = $_SESSION['auth'];
		// get Shopper Group
		$q .= "\n AND ( ISNULL(`#__{vm}_product_price`.`shopper_group_id`) OR `#__{vm}_product_price`.`shopper_group_id` IN (";
		$comma="";
		if ($auth["user_id"] != 0) { // find user's Shopper Group
			$q2 = "SELECT `shopper_group_id` FROM `#__{vm}_shopper_vendor_xref` WHERE `user_id`='".$auth["user_id"]."'";
			$db_browse->query($q2);
			while ($db_browse->next_record()) {
				$q .= $comma.$db_browse->f("shopper_group_id");
				$comma=",";
			}
		}
		// find default Shopper Groups
		$q2 = "SELECT `shopper_group_id` FROM `#__{vm}_shopper_group` WHERE `default` = 1";
		$db_browse->query($q2);
		while ($db_browse->next_record()) {
			$q .= $comma.$db_browse->f("shopper_group_id");
			$comma=",";
		}
		$q .= "\n )) ";
	}

	$q .= "\n GROUP BY `#__{vm}_product`.`product_sku` ";
	$count .= $q;
	$q .= "\n ORDER BY $orderbyField ".$DescOrderBy;
	$list .= $q . " LIMIT $limitstart, " . $limit;
	//  $error = $list; // only for debug
}
/** Changed Product Type - End */
elseif (empty($manufacturer_id)) {

	/*** GET ALL PUBLISHED PRODUCTS ***/
	$list  = "SELECT DISTINCT $fieldnames FROM (`#__{vm}_product`, `#__{vm}_category`, `#__{vm}_product_category_xref`,`#__{vm}_shopper_group`) ";
	$count  = "SELECT $count_name FROM (`#__{vm}_product`, `#__{vm}_category`, `#__{vm}_product_category_xref`,`#__{vm}_shopper_group`) ";
	$q  = "LEFT JOIN `#__{vm}_product_price` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_price`.`product_id` ";
	$q .= "\n WHERE `#__{vm}_product_category_xref`.`category_id`=`#__{vm}_category`.`category_id` ";
	if( $category_id ) {
		$q .= "\n AND `#__{vm}_product_category_xref`.`category_id`='".$category_id."' ";
	}
	$q .= "\n AND `#__{vm}_product`.`product_id`=`#__{vm}_product_category_xref`.`product_id` ";
	$q .= "\n AND `#__{vm}_product`.`product_parent_id`='0' ";
	if( !$perm->check("admin,storeadmin") ) {
		$q .= "\n  AND `product_publish`='Y'";
		if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
			$q .= "\n  AND `product_in_stock` > 0 ";
		}
	}

	$q .= "\n AND ((";
	if ($auth["shopper_group_id"] > 0) {
		$q .= "\n `#__{vm}_shopper_group`.`shopper_group_id`=`#__{vm}_product_price`.`shopper_group_id` ";
		//$q .= "\n AND `#__{vm}_shopper_group`.`shopper_group_id`='".$auth["`shopper_group_id`"]."'";
	}
	else {
		$q .= "\n `#__{vm}_shopper_group`.`shopper_group_id`=`#__{vm}_product_price`.`shopper_group_id` ";
		//$q .= "\n AND `#__{vm}_shopper_group`.default = '1' ";
	}
	$q .= "\n ) OR (`#__{vm}_product_price`.`product_id` IS NULL)) ";

	if( $keyword1 ) {
		$q .= "\n AND (";
		if ($search_limiter=="name") {
			$q .= "\n `#__{vm}_product`.`product_name` LIKE '%$keyword1%' ";
		}
		elseif ($search_limiter=="cp") {
			$q .= "\n `#__{vm}_product`.`product_url` LIKE '%$keyword1%' ";
		}
		elseif ($search_limiter=="desc") {
			$q .= "\n `#__{vm}_product`.`product_s_desc` LIKE '%$keyword1%' OR ";
			$q .= "\n `#__{vm}_product`.`product_desc` LIKE '%$keyword1%'";
		}
		else {
			$q .= "\n `#__{vm}_product`.`product_name` LIKE '%$keyword1%' OR ";
			$q .= "\n `#__{vm}_product`.`product_url` LIKE '%$keyword1%' OR ";
			$q .= "\n `#__{vm}_category`.`category_name` LIKE '%$keyword1%' OR ";
			$q .= "\n `#__{vm}_product`.`product_sku` LIKE '%$keyword1%' OR ";
			$q .= "\n `#__{vm}_product`.`product_s_desc` LIKE '%$keyword1%' OR ";
			$q .= "\n `#__{vm}_product`.`product_desc` LIKE '%$keyword1%'";
		}
		$q .= "\n ) ";
		/*** KEYWORD 2 TO REFINE THE SEARCH ***/
		if ( !empty($keyword2) ) {
			$q .= "\n $search_op (";
			if ($search_limiter=="name") {
				$q .= "\n `#__{vm}_product`.product_name LIKE '%$keyword2%' ";
			}
			elseif ($search_limiter=="cp") {
				$q .= "\n `#__{vm}_product`.product_url LIKE '%$keyword2%' ";
			}
			elseif ($search_limiter=="desc") {
				$q .= "\n `#__{vm}_product`.`product_s_desc` LIKE '%$keyword2%' OR ";
				$q .= "\n `#__{vm}_product`.`product_desc` LIKE '%$keyword2%'";
			}
			else {
				$q .= "\n `#__{vm}_product`.`product_name` LIKE '%$keyword2%' OR ";
				$q .= "\n `#__{vm}_product`.`product_url` LIKE '%$keyword2%' OR ";
				$q .= "\n `#__{vm}_category`.`category_name` LIKE '%$keyword2%' OR ";
				$q .= "\n `#__{vm}_product`.`product_sku` LIKE '%$keyword2%' OR ";
				$q .= "\n `#__{vm}_product`.product_s_desc` LIKE '%$keyword2%' OR ";
				$q .= "\n `#__{vm}_product`.`product_desc` LIKE '%$keyword2%'";
			}
			$q .= "\n ) ";
		}
	}
	elseif( $keyword ) {
		$q .= "\n AND (";
		$keywords = explode( " ", $keyword, 10 );
		$numKeywords = count( $keywords );
		$i = 1;
		foreach( $keywords as $searchstring ) {
			$searchstring = trim( stripslashes($searchstring) );
			if( !empty( $searchstring )) {
				if( $searchstring[0] == "\"" || $searchstring[0]=="'" )
				$searchstring[0] = " ";
				if( $searchstring[strlen($searchstring)-1] == "\"" || $searchstring[strlen($searchstring)-1]=="'" )
				$searchstring[strlen($searchstring)-1] = " ";
				$searchstring = trim( $searchstring );
				$q .= "\n (`#__{vm}_product`.`product_name` LIKE '%$searchstring%' OR ";
				$q .= "\n `#__{vm}_product`.`product_sku` LIKE '%$searchstring%' OR ";
				$q .= "\n `#__{vm}_product`.`product_s_desc` LIKE '%$searchstring%' OR ";
				$q .= "\n `#__{vm}_product`.`product_desc` LIKE '%$searchstring%') ";
			}
			if( $i++ < $numKeywords ) {
				$q .= "\n  AND ";
			}
		}
		$q .= "\n ) ";
	}
	$count .= $q;
	$q .= "\n GROUP BY `#__{vm}_product`.`product_sku` ";
	$q .= "\n ORDER BY $orderbyField $DescOrderBy";
	$list .= $q . " LIMIT $limitstart, " . $limit;
}

/*** GET ALL PUBLISHED PRODUCTS FROM THAT MANUFACTURER ***/
elseif (!empty($manufacturer_id)) {
	$list  = "SELECT DISTINCT *,`#__{vm}_product`.`product_id` FROM (`#__{vm}_product`, `#__{vm}_product_mf_xref`,`#__{vm}_shopper_group` ";
	$count  = "SELECT $count_name FROM (`#__{vm}_product`, `#__{vm}_product_mf_xref`,`#__{vm}_shopper_group` ";
	$q  = " manufacturer_id='".$manufacturer_id."' ";
	$q .= "\n AND `#__{vm}_product`.`product_id`=`#__{vm}_product_mf_xref`.`product_id` ";
	if( $perm->is_registered_customer($my->id) ) {
		$list .= ",`#__{vm}_shopper_vendor_xref`) LEFT JOIN `#__{vm}_product_price` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_price`.`product_id` WHERE ";
		$count .= ",`#__{vm}_shopper_vendor_xref`) LEFT JOIN `#__{vm}_product_price` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_price`.`product_id` WHERE ";
		$q .= "\n AND (`#__{vm}_product`.`product_id`=`#__{vm}_product_price`.`product_id` OR `#__{vm}_product_price`.`product_id` IS NULL) ";
		$q .= "\n AND ((`#__{vm}_shopper_vendor_xref`.user_id =".$my->id." ";
		$q .= "\n AND `#__{vm}_shopper_vendor_xref`.`shopper_group_id`=`#__{vm}_shopper_group`.`shopper_group_id`) OR `#__{vm}_product_price`.`shopper_group_id` IS NULL) ";
	}
	else {
		$list .= ")  LEFT JOIN `#__{vm}_product_price` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_price`.`product_id` WHERE ";
		$count .= ") LEFT JOIN `#__{vm}_product_price` ON `#__{vm}_product`.`product_id` = `#__{vm}_product_price`.`product_id` WHERE ";
		$q .= "\n AND (`#__{vm}_product`.`product_id`=`#__{vm}_product_price`.`product_id` OR `#__{vm}_product_price`.`product_id` IS NULL) ";
		$q .= "\n AND `#__{vm}_shopper_group`.`default` = '1' ";
	}
	$q .= "\n AND ((`product_parent_id`='0') OR (`product_parent_id`='')) ";
	if( !$perm->check("admin,storeadmin") ) {
		$q .= "\n  AND `product_publish`='Y' ";
		if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
			$q .= "\n  AND product_in_stock > 0 ";
		}
	}
	$count .= $q;
	$q .= "\n GROUP BY `#__{vm}_product`.`product_sku` ";
	$q .= "\n ORDER BY $orderbyField $DescOrderBy";
	$list .= $q . " LIMIT $limitstart, " . $limit;
}
// BACK TO shop.browse.php !
?>