<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: zw_waiting_list.php,v 1.4.2.1 2005/12/07 20:10:10 soeren_nb Exp $
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

/**
* This class is meant to manage Waiting Lists
* @copyright (C) devcompany.com  All rights reserved.
* @license MPL
*/
class zw_waiting_list {
	var $classname = "ps_waiting_list";

	/*
	** VALIDATION FUNCTIONS
	**
	*/

	function validate_add(&$d) {
		$db = new ps_DB;
		
		$q = "SELECT waiting_list_id from #__{vm}_waiting_list WHERE ";
		$q .= "notify_email='" . $d["notify_email"] . "' AND ";
		$q .= "product_id='" . $d["product_id"] . "' AND notified='0'";
		$db->query($q);
		if ($db->next_record()) {
			$d["error"] .= "You are already being notified of this product.";
			return False;
		}
		if (!$d["notify_email"]) {
			$d["error"] .= "You must enter an e-mail address to be on the waiting list.";
			return False;
		}
		if (!mShop_validateEmail($d["notify_email"])) {
			$d["error"] .= "Please provide a valid email address.";
			return False;
		}
		if (!$d["product_id"]) {
			$d["error"] .= "You must enter a product to be on the waiting list.";
			return False;
		}
		return True;
	}

	function validate_delete($d) {

		if (!$d["notify_email"]) {
			$d["error"] = "ERROR:  Please select a waiting list id to delete.";
			return False;
		}
		if (!mShop_validateEmail($d["notify_email"])) {
			$d["error"] .= "Please provide a valid email address.";
			return False;
		}
		if (!$d["product_id"]) {
			$d["error"] .= "You must enter a product id!";
			return False;
		}
		return True;
	}


	/**************************************************************************
	* name: add()
	* created by: mwattier
	* description: creates a new waiting list entry
	* parameters:
	* returns:
	**************************************************************************/
	function add(&$d) {
		global $my;
		$db = new ps_DB;

		if (!$this->validate_add($d)) {
			return False;
		}
		$q = "INSERT INTO #__{vm}_waiting_list (product_id, user_id, notify_email)";
		$q .= " VALUES ('";
		$q .= $d["product_id"] . "','";
		$q .= $my->id . "','";
		$q .= $d["notify_email"] . "')";
		$db->query($q);
		$db->next_record();
		return True;

	}

	/**************************************************************************
	* name: update()
	* created by: pablo
	* description: updates function information
	* parameters:
	* returns:
	**************************************************************************/
	function update($shopper_email,$product_id) {
		$dbu = new ps_DB;

		$qu = "UPDATE #__{vm}_waiting_list SET notified='1' WHERE ";
		$qu .= "notify_email='$shopper_email' AND ";
		$qu .= "product_id='$product_id'";
		$dbu->query($qu);
		return True;
	}

	/**************************************************************************
	* name: delete()
	* created by: pablo
	* description: Should delete a category and and categories under it.
	* parameters:
	* returns:
	**************************************************************************/
	function delete(&$d) {
		$db = new ps_DB;

		if (!$this->validate_delete($d)) {
			return False;
		}
		$q = "DELETE from #__{vm}_waiting_list where notify_email='" . $d["notify_email"] . "'";
		$q .= " AND product_id='" .$d["product_id"] ."'";
		$db->query($q);
		$db->next_record();
		return True;
		
	}
	/**************************************************************************
	* name: notify_list()
	* created by:
	* description: Will notify all people who have not been notified
	* parameters: takes the $product_id
	* returns: true
	**************************************************************************/
	function notify_list($product_id) {
		global $sess;
		
		$option = mosGetParam( $_REQUEST, 'option' );
		
		if (!$product_id) {
			return False;
		}

		$dbv = new ps_DB;
		$qt = "SELECT * from #__{vm}_vendor ";
		$qt .= "WHERE vendor_id='1'";
		$dbv->query($qt);
		$dbv->next_record();
		$from_email = $dbv->f("contact_email");


		$db = new ps_DB;
		$q = "SELECT * FROM #__{vm}_waiting_list WHERE ";
		$q .= "notified='0' AND product_id='$product_id'";
		$db->query($q);

		require_once( CLASSPATH. 'ps_product.php');
		$ps_product = new ps_product;

		while ($db->next_record()) {
			// get the product name for the e-mail
			$product_name = $ps_product->get_field($product_id, "product_name");

			// lets make the e-mail up from the info we have
			$notice_subject = $product_name;
			$notice_subject .= " Has Arrived! ";

			$notice_body = "Thank you for your patience.\n Our ";
			$notice_body .= $product_name;
			$notice_body .= " is now in stock and can be purchased by following this link\n";

			// now get the url information
			$url = URL . "index.php?page=shop.product_details&flypage=shop.flypage&product_id=$product_id&option=$option&Itemid=".$sess->getShopItemid();
			$notice_body .= $url;
			$notice_body .= "\n";

			$notice_body .= " This is a one time notice, you will not receive this e-mail again.";

			// send the e-mail
			$shopper_email = $db->f("notify_email");
			mail($shopper_email,
			$notice_subject, $notice_body, "From: $from_email\n");

			$this->update( $shopper_email, $product_id);

		}
		return True;
	}
}
?>
