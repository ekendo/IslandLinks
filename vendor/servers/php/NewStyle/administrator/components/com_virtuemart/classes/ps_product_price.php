<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_product_price.php,v 1.6.2.1 2005/12/15 20:59:30 soeren_nb Exp $
* @package VirtueMart
* @subpackage classes
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

/****************************************************************************
*
* CLASS DESCRIPTION
*
* ps_product_price
*
*************************************************************************/
class ps_product_price {
	var $classname = "ps_product_price";

	/**************************************************************************
	** name: validate()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate(&$d) {
		$valid = true;
		$d["error"] = "";
		if (!isset($d["product_price"])) {
			$d["error"] .= "ERROR: A price must be entered.";
			$valid = false;
		}
		if (empty($d["product_id"])) {
			$d["error"] .= "ERROR: A product ID is missing.";
			$valid = false;
		}
		// convert all "," in prices to decimal points.
		if (stristr($d["product_price"],","))
		$d['product_price'] = str_replace(',', '.', $d["product_price"]);

		if (!$d["product_currency"]) {
			$d["error"] .= "ERROR: A currency must be entered.";
			$valid = false;
		}
		$d["price_quantity_start"] = intval($d["price_quantity_start"]);
		$d["price_quantity_end"] = intval($d["price_quantity_end"]);

		if ($d["price_quantity_end"] < $d["price_quantity_start"]) {
			$d["error"] .= "ERROR: The entered Quantity End is less than the Quantity Start.";
			$valid = false;
		}

		$db = new ps_DB;
		$q = "SELECT count(*) AS num_rows FROM #__{vm}_product_price WHERE";
		if (!empty($d["product_price_id"])) {
			$q .= " product_price_id != '".$d['product_price_id']."' AND";
		}
		$q .= " shopper_group_id = '".$d["shopper_group_id"]."'";
		$q .= " AND product_id = '".$d['product_id']."'";
		$q .= " AND product_currency = '".$d['product_currency']."'";
		$q .= " AND (('".$d['price_quantity_start']."' >= price_quantity_start AND '".$d['price_quantity_start']."' <= price_quantity_end)";
		$q .= " OR ('".$d['price_quantity_end']."' >= price_quantity_start AND '".$d['price_quantity_end']."' <= price_quantity_end))";
		$db->query( $q ); $db->next_record();

		if ($db->f("num_rows") > 0) {
			$d["error"] .= "ERROR: This product already has a price for the selected Shopper Group and the specified Quantity Range.";
			$valid = false;
		}
		return $valid;
	}

	/**************************************************************************
	** name: add()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function add(&$d) {
		if (!$this->validate($d)) {
			return false;
		}

		$timestamp = time();
		if (empty($d["product_price_vdate"])) $d["product_price_vdate"] = '';
		if (empty($d["product_price_edate"])) $d["product_price_edate"] = '';

		$db = new ps_DB;
		$q  = "INSERT INTO #__{vm}_product_price (product_id,shopper_group_id,";
		$q .= "product_price,product_currency,product_price_vdate,";
		$q .= "product_price_edate,cdate,mdate,price_quantity_start,price_quantity_end) ";
		$q .= "VALUES ('" . $d["product_id"] . "','" . $d["shopper_group_id"];
		$q .= "','" . $d["product_price"] . "','" . $d["product_currency"] . "','";
		$q .= $d["product_price_vdate"] . "','" . $d["product_price_edate"] . "',";
		$q .= "'$timestamp','$timestamp', '".$d["price_quantity_start"]."','".$d["price_quantity_end"]."')";

		$db->query($q);

		return true;
	}

	/**************************************************************************
	** name: update()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function update(&$d) {
		if (!$this->validate($d)) {
			return false;
		}

		$timestamp = time();

		$db = new ps_DB;
		if (empty($d["product_price_vdate"])) $d["product_price_vdate"] = '';
		if (empty($d["product_price_edate"])) $d["product_price_edate"] = '';

		$q  = "UPDATE #__{vm}_product_price SET ";
		$q .= "shopper_group_id='" . $d["shopper_group_id"] . "',";
		$q .= "product_id='" . $d["product_id"] . "',";
		$q .= "product_price='" . $d["product_price"] . "',";
		$q .= "product_currency='" . $d["product_currency"] . "',";
		$q .= "product_price_vdate='" . $d["product_price_vdate"] . "',";
		$q .= "product_price_edate='" . $d["product_price_edate"] . "',";
		$q .= "price_quantity_start='" . $d["price_quantity_start"] . "',";
		$q .= "price_quantity_end='" . $d["price_quantity_end"] . "',";
		$q .= "mdate='$timestamp' ";
		$q .= "WHERE product_price_id='" . $d["product_price_id"] . "' ";

		$db->query($q);

		return true;
	}

	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {

		$record_id = $d["product_price_id"];

		if( is_array( $record_id)) {
			foreach( $record_id as $record) {
				if( !$this->delete_record( $record, $d ))
				return false;
			}
			return true;
		}
		else {
			return $this->delete_record( $record_id, $d );
		}
	}
	/**
	* Deletes one Record.
	*/
	function delete_record( $record_id, &$d ) {
		global $db;
		$q  = "DELETE FROM #__{vm}_product_price ";
		$q .= "WHERE product_price_id = '$record_id' ";
		$db->query($q);

		return True;
	}


}
?>