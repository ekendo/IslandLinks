<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_cart.php,v 1.12.2.4 2006/05/06 10:05:26 soeren_nb Exp $
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
 * CLASS DESCRIPTION
 *                   
 * ps_cart
 *
 * The cart class is used to store products and carry them through the user's
 * session in the store.
 * properties:  
 * 	item() - an array of items
 *       idx - the current count of items in the cart
 *       error - the error message returned by validation if any
 * methods:
 *       add()
 *       update()
 *       delete()
*************************************************************************/

class ps_cart {
	var $classname="ps_cart";
	
	/**
	 * Calls the constructor
	 *
	 * @return array An empty cart
	 */
	function initCart() {
		global $my, $cart, $sess;
		// Register the cart
		if (empty($_SESSION['cart'])) {
			$cart = array();
			$cart['idx'] = 0;
			$_SESSION['cart'] = $cart;
			return $cart;
		}
		else {
			if( ( @$_SESSION['auth']['user_id'] != $my->id ) && empty( $my->id ) 
				&& @$_GET['cartReset'] != 'N') {
				// If the user ID has changed (after logging out)
				// empty the cart!
				$sess->emptySession();
				ps_cart::reset();
			}
		}
		return $_SESSION['cart'];
	}
	/**
 	* adds an item to the shopping cart
 	* @author pablo
 	* @param array $d
 	*/
	function add(&$d) {
		global $sess, $VM_LANG, $cart, $option, $vmLogger;

		include_class("product");

		$Itemid = mosgetparam($_REQUEST, "Itemid", null);
		$db = new ps_DB;
		$product_id = $d["product_id"];
		$quantity = isset($d["quantity"]) ? $d["quantity"] : 1;
		$_SESSION['last_page'] = "shop.product_details";

		// Check for negative quantity
		if ($quantity < 0) {
			$vmLogger->warning( $VM_LANG->_PHPSHOP_CART_ERROR_NO_NEGATIVE );
			return False;
		}

		if (!ereg("^[0-9]*$", $quantity)) {
			$vmLogger->warning( $VM_LANG->_PHPSHOP_CART_ERROR_NO_VALID_QUANTITY );
			return False;
		}

		// Check to see if checking stock quantity
		if (CHECK_STOCK) {
			$q = "SELECT product_in_stock ";
			$q .= "FROM #__{vm}_product where product_id='$product_id'";
			$db->query($q);
			$db->next_record();
			$product_in_stock = $db->f("product_in_stock");
			if (empty($product_in_stock)) {
				$product_in_stock = 0;
			}
			if ($quantity > $product_in_stock) {
				$msg = $VM_LANG->_PHPSHOP_CART_STOCK_1;
				eval( "\$msg .= \"".$VM_LANG->_PHPSHOP_CART_STOCK_2."\";" );
				
				$vmLogger->tip( $msg );
				$GLOBALS['page'] = 'shop.waiting_list';
				return true;
			}
		}

		// Quick add of item
		$q = "SELECT product_id FROM #__{vm}_product WHERE ";
		$q .= "product_parent_id = '".$d['product_id']."'";
		$db->query ( $q );

		if ( $db->num_rows()) {
			$vmLogger->tip( $VM_LANG->_PHPSHOP_CART_SELECT_ITEM );
			return false;
		}

		// If no quantity sent them assume 1
		if ($quantity == "")
		$quantity = 1;


		// Check to see if we already have it
		$updated = 0;
		
		$result = ps_product_attribute::cartGetAttributes( $d );
		
		if ( ($result["attribute_given"] == false && !empty( $result["advanced_attribute_list"] ))
		|| ($result["custom_attribute_given"] == false && !empty( $result["custom_attribute_list"] )) ) {
			$_REQUEST['flypage'] = ps_product::get_flypage($product_id);
			$GLOBALS['page'] = 'shop.product_details';
			$vmLogger->tip( $VM_LANG->_PHPSHOP_CART_SELECT_ITEM );
			return true;
		}

		// Check for duplicate and do not add to current quantity
		for ($i=0;$i<$_SESSION["cart"]["idx"];$i++) {
			// modified for advanced attributes
			if ($_SESSION['cart'][$i]["product_id"] == $product_id
			&&
			$_SESSION['cart'][$i]["description"] == $d["description"]
			) {
				$updated = 1;
			}
		}
		// If we did not update then add the item
		if (!$updated) {

			$k = $_SESSION['cart']["idx"];

			$_SESSION['cart'][$k]["quantity"] = $quantity;
			$_SESSION['cart'][$k]["product_id"] = $product_id;
			// added for the advanced attribute modification
			$_SESSION['cart'][$k]["description"] = $d["description"];
			$_SESSION['cart']["idx"]++;
		}
		else {
			$this->update( $d );
		}

		/* next 3 lines added by Erich for coupon code */
		/* if the cart was updated we gotta update any coupon discounts to avoid ppl getting free stuff */
		if( !empty( $_SESSION['coupon_discount'] )) {
			// Update the Coupon Discount !!
			$_POST['do_coupon'] = 'yes';
		}

		$cart = $_SESSION['cart'];
		return True;
	}

	/**
	 * updates the quantity of a product_id in the cart
	 * @author pablo
	 * @param array $d
	 * @return boolean result of the update
	 */
	function update(&$d) {
		global $sess,$VM_LANG, $vmLogger;

		include_class("product");

		$db = new ps_DB;
		$product_id = $d["product_id"];
		$quantity = $d["quantity"];
		$_SESSION['last_page'] = "shop.cart";

		// Check for negative quantity
		if ($quantity < 0) {
			$vmLogger->warning( $VM_LANG->_PHPSHOP_CART_ERROR_NO_NEGATIVE );
			return False;
		}

		if (!ereg("^[0-9]*$", $quantity)) {
			$vmLogger->warning( $VM_LANG->_PHPSHOP_CART_ERROR_NO_VALID_QUANTITY );
			return False;
		}

		// Check to see if checking stock quantity
		if (CHECK_STOCK) {
			$q = "SELECT product_in_stock ";
			$q .= "FROM #__{vm}_product where product_id=";
			$q .= $product_id;
			$db->query($q);
			$db->next_record();
			$product_in_stock = $db->f("product_in_stock");
			if (empty($product_in_stock)) $product_in_stock = 0;
			if ($quantity > $product_in_stock) {
				$msg = $VM_LANG->_PHPSHOP_CART_STOCK_1;
				eval( "\$msg .= \"".$VM_LANG->_PHPSHOP_CART_STOCK_2."\";" );
				
				$vmLogger->tip( $msg );
				$GLOBALS['page'] = 'shop.waiting_list';
				return true;
			}
		}

		if (!$product_id) {
			return false;
		}

		if ($quantity == 0) {
			$this->delete($d);
		}
		else {

			for ($i=0;$i<$_SESSION['cart']["idx"];$i++) {
				// modified for the advanced attribute modification
				if ( ($_SESSION['cart'][$i]["product_id"] == $product_id )
				&&
				($_SESSION['cart'][$i]["description"] == stripslashes($d["description"]) )
				) {
					$_SESSION['cart'][$i]["quantity"] = $quantity;
				}
			}
		}
		if( !empty( $_SESSION['coupon_discount'] )) {
			// Update the Coupon Discount !!
			$_POST['do_coupon'] = 'yes';
		}
		$_SESSION["cart"]=$_SESSION['cart'];
		return True;
	}

	/**
	 * deletes a given product_id from the cart
	 *
	 * @param array $d
	 * @return boolan Result of the deletion
	 */
	function delete($d) {

		$temp = array();
		$product_id = $d["product_id"];

		if (!$product_id) {
			$_SESSION['last_page'] = "shop.cart";
			return False;
		}

		$j = 0;
		for ($i=0;$i<$_SESSION['cart']["idx"];$i++) {
			// modified for the advanced attribute modification
			if (
			($_SESSION['cart'][$i]["product_id"] != $product_id)
			||
			($_SESSION['cart'][$i]["description"] != stripslashes($d["description"]))
			) {
				$temp[$j++] = $_SESSION['cart'][$i];
			}
		}
		$temp["idx"] = $j;
		$_SESSION['cart'] = $temp;

		if( !empty( $_SESSION['coupon_discount'] )) {
			// Update the Coupon Discount !!
			require_once( CLASSPATH . "ps_coupon.php" );
			ps_coupon::process_coupon_code( $d );
		}

		return True;
	}


	/**
	 * Empties the cart
	 * @author pablo
	 * @return boolean true
	 */
	function reset() {
		global $cart;
		$_SESSION['cart'] = array();
		$_SESSION['cart']["idx"]=0;
		$cart = $_SESSION['cart'];
		return True;
	}
}

?>